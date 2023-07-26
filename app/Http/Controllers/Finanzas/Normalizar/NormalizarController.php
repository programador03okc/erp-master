<?php

namespace App\Http\Controllers\Finanzas\Normalizar;

use App\Helpers\ConfiguracionHelper;
use App\Helpers\Finanzas\PresupuestoInternoHistorialHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Finanzas\Presupuesto\PresupuestoInternoController;
use App\Http\Controllers\Tesoreria\RegistroPagoController;
use App\Models\Administracion\Aprobacion;
use App\Models\Administracion\Division;
use App\Models\Administracion\Documento;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Requerimiento;
use App\Models\Finanzas\HistorialPresupuestoInternoSaldo;
use App\Models\Finanzas\PresupuestoInterno;
use App\Models\Finanzas\PresupuestoInternoDetalle;
use App\Models\Logistica\Orden;
use App\Models\Logistica\OrdenCompraDetalle;
use App\Models\Logistica\OrdenesView;
use App\Models\Tesoreria\RegistroPago;
use App\Models\Tesoreria\RequerimientoPago;
use App\Models\Tesoreria\RequerimientoPagoDetalle;
use Exception;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class NormalizarController extends Controller
{
    //
    public function lista()
    {
        $division = Division::where('estado',1)->get();
        return view('finanzas.normalizar.lista', get_defined_vars());
    }
    public function listar(Request  $request)
    {
        $ordenes = Orden::whereMonth('log_ord_compra.fecha_registro','01')
        ->select('log_ord_compra.*')
        ->join('logistica.log_det_ord_compra','log_det_ord_compra.id_orden_compra','=','log_ord_compra.id_orden_compra')
        ->join('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','log_det_ord_compra.id_detalle_requerimiento')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
        ->where('alm_req.division_id',8)
        ->groupBy('log_ord_compra.id_orden_compra')
        ->get();
        // $req_pago = RequerimientoPago::whereMonth('fecha_registro',$request->mes)->where('id_division',$request->division)->get();

        return response()->json(["ordenes"=>$ordenes],200);
    }
    public function listarRequerimientosPagos(Request $request)
    {
        // return $request->all();exit;

        $req_pago = RequerimientoPago::select('requerimiento_pago.*',
        DB::raw("requerimiento_pago.monto_total::numeric  - (select sum(registro_pago.total_pago)  from tesoreria.registro_pago  where registro_pago.id_requerimiento_pago = requerimiento_pago.id_requerimiento_pago  and registro_pago.estado !=7)::numeric as saldo")

        )
        // ->whereDate('requerimiento_pago.fecha_autorizacion','>=','2023-01-01 00:00:00')
        // ->whereDate('requerimiento_pago.fecha_autorizacion','<=','2023-04-30 23:59:59')

        ->join('tesoreria.requerimiento_pago_detalle','requerimiento_pago_detalle.id_requerimiento_pago','=','requerimiento_pago.id_requerimiento_pago')

        ->join('tesoreria.registro_pago','registro_pago.id_requerimiento_pago','=','requerimiento_pago.id_requerimiento_pago')

        ->whereDate('registro_pago.fecha_pago','>=','2023-01-01 00:00:00')
        ->whereDate('registro_pago.fecha_pago','<=','2023-04-30 23:59:59')

        ->where('requerimiento_pago.id_estado','=',6)
        ->whereNull('requerimiento_pago.id_proyecto')
        ->whereNull('requerimiento_pago.id_cc')
        ->whereNull('requerimiento_pago_detalle.id_partida')
        ->whereNull('requerimiento_pago_detalle.id_partida_pi');
        if (!empty($request->division)) {
            $req_pago = $req_pago->where('requerimiento_pago.id_division',$request->division);
        }
        $req_pago = $req_pago

        ->when(($request->tipo_pago ==1), function ($query) { // rtipo de pago es sin saldo
            return $query->whereRaw('requerimiento_pago.monto_total::numeric  - (select sum(registro_pago.total_pago)  from tesoreria.registro_pago  where registro_pago.id_requerimiento_pago = requerimiento_pago.id_requerimiento_pago  and registro_pago.estado !=7)::numeric =' . 0);
        })
        ->when(($request->tipo_pago ==2), function ($query) { //tipo de pago es con saldo
            return $query->whereRaw('requerimiento_pago.monto_total::numeric  - (select sum(registro_pago.total_pago)  from tesoreria.registro_pago  where registro_pago.id_requerimiento_pago = requerimiento_pago.id_requerimiento_pago  and registro_pago.estado !=7)::numeric >' . 0);
        })
        ->groupBy('requerimiento_pago.id_requerimiento_pago')
        // ->where('requerimiento_pago_detalle.id_estado','!=',7)
        ->get();
        return DataTables::of($req_pago)
        ->addColumn('mes', function ($data){

            $registro_pago = RegistroPago::where('id_requerimiento_pago',$data->id_requerimiento_pago)->first();

            $fecha_como_entero = strtotime($registro_pago->fecha_pago);
            $mes = date("m", $fecha_como_entero);

            return $mes;
        })
        // ->toJson();
        ->make(true);
    }
    public function listarOrdenes(Request $request)
    {


        // $ordenes = OrdenesView::select('ordenes_view.*')
        $ordenes = Orden::select('log_ord_compra.*',
        DB::raw("string_agg(DISTINCT alm_req.codigo::text, ', '::text) AS codigo_requerimiento_list"),
        'estado_pago_orden.descripcion as estado_pago',
        DB::raw("CASE WHEN pago_cuota.numero_de_cuotas =1 THEN 'CUOTA PERSONALIZADA' WHEN pago_cuota.numero_de_cuotas >1 THEN CONCAT(pago_cuota.numero_de_cuotas,' CUOTAS') ELSE 'NO APLICA' END AS numero_de_cuotas"),
        DB::raw("CASE WHEN estado_cuota.descripcion NOTNULL THEN estado_cuota.descripcion ELSE 'NO APLICA' END AS estado_pago_cuota"),
        DB::raw("(select sum(registro_pago.total_pago)  from tesoreria.registro_pago  where registro_pago.id_oc = log_ord_compra.id_orden_compra  and registro_pago.estado !=7)::numeric as total_pagado"),
        DB::raw("log_ord_compra.monto_total::numeric  - (select sum(registro_pago.total_pago)  from tesoreria.registro_pago  where registro_pago.id_oc = log_ord_compra.id_orden_compra  and registro_pago.estado !=7)::numeric as saldo"),
        DB::raw("CASE WHEN alm_req.tipo_impuesto =1 THEN 'DETRACCIÓN' WHEN alm_req.tipo_impuesto =2 THEN 'RENTA' ELSE '' END AS tipo_impuesto")
        )
        ->join('logistica.log_det_ord_compra','log_det_ord_compra.id_orden_compra','=','log_ord_compra.id_orden_compra')
        ->join('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','log_det_ord_compra.id_detalle_requerimiento')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
        ->leftJoin('logistica.pago_cuota','pago_cuota.id_orden','=','log_ord_compra.id_orden_compra')
        ->leftJoin('tesoreria.requerimiento_pago_estado as estado_pago_orden','estado_pago_orden.id_requerimiento_pago_estado','=','log_ord_compra.estado_pago')
        ->leftJoin('tesoreria.requerimiento_pago_estado as estado_cuota','estado_cuota.id_requerimiento_pago_estado','=','pago_cuota.id_estado')
        ;

        if (!empty($request->division)) {
            $ordenes = $ordenes->where('alm_req.division_id',$request->division);
        }
        // if (!empty($request->mes)) {
        //     $ordenes = $ordenes->whereMonth('ordenes_view.fecha_emision',$request->mes);
        // }
        $ordenes = $ordenes->whereDate('log_ord_compra.fecha_autorizacion','>=','2023-01-01 00:00:00');
        $ordenes = $ordenes->whereDate('log_ord_compra.fecha_autorizacion','<=','2023-04-30 23:59:59');

        $ordenes = $ordenes->whereIn('log_ord_compra.estado_pago',[6,9,10]); //pagado, con saldo, pagado con saldo
        $ordenes = $ordenes->where([['log_ord_compra.estado','!=',7],['alm_req.id_cc','=',null],['alm_req.id_proyecto','=',null]])
        ->when(($request->tipo_pago ==1), function ($query) { // rtipo de pago es sin saldo
            return $query->whereRaw('log_ord_compra.monto_total::numeric  - (select sum(registro_pago.total_pago)  from tesoreria.registro_pago  where registro_pago.id_oc = log_ord_compra.id_orden_compra  and registro_pago.estado !=7)::numeric =' . 0);
        })
        ->when(($request->tipo_pago ==2), function ($query) { //tipo de pago es con saldo
            return $query->whereRaw('log_ord_compra.monto_total::numeric  - (select sum(registro_pago.total_pago)  from tesoreria.registro_pago  where registro_pago.id_oc = log_ord_compra.id_orden_compra  and registro_pago.estado !=7)::numeric >' . 0);
        })
        ->groupBy('log_ord_compra.id_orden_compra','estado_pago_orden.descripcion','estado_cuota.descripcion','pago_cuota.numero_de_cuotas', 'estado_cuota.descripcion','alm_req.tipo_impuesto')
        ->get();
        // $ordenes = $ordenes->groupBy('log_det_ord_compra.id_orden_compra');
        return DataTables::of($ordenes)
        ->addColumn('mes', function ($data){
            $fecha_como_entero = strtotime($data->fecha_autorizacion);
            $mes = date("m", $fecha_como_entero);

            return $mes;
        })
        // ->toJson();
        ->make(true);
    }
    public function obtenerPresupuesto(Request $request)
    {
        switch ($request->tap) {
            case 'requerimiento de pago':

                break;

            case 'orden':

                break;
        }
        $presupuesto_interno = PresupuestoInterno::where('id_area',$request->division)->where('estado','=',2)->first();

        if ($presupuesto_interno) {
            $presupuesto_interno_detalle = PresupuestoInternoDetalle::where('id_presupuesto_interno',$presupuesto_interno->id_presupuesto_interno)->where('estado',1)->orderBy('partida')->get();

            if ($presupuesto_interno_detalle) {
                return response()->json(["presupuesto"=>$presupuesto_interno,"presupuesto_detalle"=>$presupuesto_interno_detalle,"status"=>200],200);
            }
        }
        return response()->json(["tipo"=>"warning","mensaje"=>"No cuenta con un presupuesto", "titulo"=>"Alerta","status"=>400],200);

    }
    public function vincularPartida(Request $request)
    {
        try {
            DB::beginTransaction();

            // return response()->json($request->all(),200);exit;
            $variable = $request->tap;

            $afectaPresupuestoInternoResta = null;

            $tipo='success';
            $mensaje='Se asigno a la partida con exito';
            $titulo='Éxito';
            $montoTotalRegistroDePago=0;
            switch ($variable) {
                case 'orden':

                    $registro_pago = RegistroPago::where([['id_oc',$request->orden_id],['estado',1]])->get();
                    $orden = Orden::find($request->orden_id);


                    $historial_saldo = HistorialPresupuestoInternoSaldo::where('id_requerimiento_detalle',$request->requerimiento_detalle_id)
                    ->where('id_orden',$request->orden_id)
                    ->first();

                    if (!$historial_saldo || $historial_saldo->estado!==3) {
                    
                        foreach ($registro_pago as $rp) {
                            $montoTotalRegistroDePago=+$rp->total_pago;
                        }
                        
                        // * caso #1: cuando el monto total de registro de pagos es igual al monto total de la orden
                        if($montoTotalRegistroDePago == $orden->monto_total){
                            
                        $requerimiento = Requerimiento::find($request->requerimiento_id);
                        $requerimiento->id_presupuesto_interno = $request->presupuesto_interno_id;
                        $requerimiento->save();
                        $requerimiento_detalle = DetalleRequerimiento::find($request->requerimiento_detalle_id);
                        $requerimiento_detalle->id_partida_pi = $request->presupuesto_interno_detalle_id;
                        $requerimiento_detalle->save();
                        // ----------------------
                        $tipo='info';
                        $mensaje = PresupuestoInternoHistorialHelper::normalizarOrden($request->orden_id,$request->requerimiento_detalle_id);
                        $titulo='Información';

                        }else{
                            // TODO : crear método para caso #2
                            // * caso #2: cuando el monto total de registro de pagos NO es igual al monto total de la orden (saldo)
                            $tipo='warning';
                            $mensaje='La orden tiene saldo pendiente, no esta habilitada la opcion para afectar este tipo de caso.';
                            $titulo='Información';
                        }

                    }else{
                        $tipo='warning';
                        $mensaje='El requerimiento ya se asigno a una partida';
                        $titulo='Información';
                    }

                    // $detalleArray = (new RegistroPagoController)->obtenerDetalleRequerimientoPagoParaPresupuestoInterno($request->requerimiento_pago_id,floatval($request->total_pago),'completo');
                    // $afectaPresupuestoInternoResta = (new PresupuestoInternoController)->afectarPresupuestoInterno('resta','orden',$orden->id_orden_compra, $detalleParaPresupuestoRestaArray);
                break;

                case 'requerimiento de pago':


                    $requerimiento_pago = RequerimientoPago::find($request->requerimiento_pago_id);
                    $registro_pago = RegistroPago::where('id_requerimiento_pago',$request->requerimiento_pago_id)->first();
                    $fecha_como_entero = strtotime($registro_pago->fecha_pago);
                    $mes = date("m", $fecha_como_entero);


                    $mes_string = ConfiguracionHelper::mesNumero($mes);
                    $mes_text = ConfiguracionHelper::mesNumero($mes).'_aux';

                    $saldo_presupuesto_detalle = PresupuestoInternoDetalle::where('id_presupuesto_interno_detalle',$request->presupuesto_interno_detalle_id)->first();

                    $historial_saldo = HistorialPresupuestoInternoSaldo::where('id_requerimiento_pago_detalle',$request->requerimiento_pago_detalle_id)
                    ->where('id_requerimiento_pago',$request->requerimiento_pago_id)
                    ->first();

                    if (!$historial_saldo || $historial_saldo->estado!==3) {

                        if (floatval($saldo_presupuesto_detalle->$mes_text)>=floatval($requerimiento_pago->monto_total)) {

                            $requerimiento_pago->id_presupuesto_interno=$request->presupuesto_interno_id;
                            $requerimiento_pago->save();

                            $requerimiento_pago = RequerimientoPagoDetalle::find($request->requerimiento_pago_detalle_id);
                            $requerimiento_pago->id_partida_pi = $request->presupuesto_interno_detalle_id;
                            $requerimiento_pago->save();

                            $tipo='info';
                            $mensaje = PresupuestoInternoHistorialHelper::normalizarRequerimientoDePago($request->requerimiento_pago_id,$request->requerimiento_pago_detalle_id);
                            $titulo='Información';

                        }else{
                            $tipo='warning';
                            $mensaje='El saldo del mes de '.$mes_string.' es menor que el monto del Requerimiento de Pago.';
                            $titulo='Éxito';
                        }

                    }else{
                        $tipo='warning';
                        $mensaje='El requerimiento ya se asigno a una partida';
                        $titulo='Información';
                    }

                break;
            }
        } catch (\PDOException $message) {
            DB::rollBack();
			$ouput=['tipo'=>'error','titulo'=>'Error','status'=>false,'message'=> $message->getMessage()];
            return response()->json($ouput,200);
        }

        DB::commit();

        return response()->json(["tipo"=>$tipo,"mensaje"=>$mensaje,"titulo"=>$titulo],200);
    }

    // public function getFechaAprobacionRequerimientoDePago($idRequerimientoPago) {
    //     $idDocumento = Documento::getIdDocAprob($idRequerimientoPago,11);
    //     $fechaAprobacion= '';
    //     if($idDocumento>0){
    //         $AprobacionList = Aprobacion::getVoBo($idDocumento);

    //         if($AprobacionList['status']=='200'){
    //             foreach ( $AprobacionList['data'] as $value) {
    //                 if($value->id_vobo == 1){
    //                     $fechaAprobacion= $value->fecha_vobo;
    //                 }
    //             };
    //         }
    //     }

    //     return $fechaAprobacion;
    // }

    public function detalleRequerimientoPago($id)
    {
        $requerimiento_pago = RequerimientoPagoDetalle::where('id_requerimiento_pago',$id)
        ->whereNull('id_partida')
        ->whereNull('id_partida_pi')
        ->where('id_estado','!=',7)
        ->get();
        return response()->json($requerimiento_pago,200);
    }
}
