<?php

namespace App\Http\Controllers\Finanzas\Normalizar;

use App\Helpers\ConfiguracionHelper;
use App\Helpers\Finanzas\PresupuestoInternoHistorialHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Finanzas\Presupuesto\PresupuestoInternoController;
use App\Http\Controllers\Tesoreria\RegistroPagoController;
use App\Models\Administracion\Division;
use App\Models\Finanzas\HistorialPresupuestoInternoSaldo;
use App\Models\Finanzas\PresupuestoInterno;
use App\Models\Finanzas\PresupuestoInternoDetalle;
use App\Models\Logistica\Orden;
use App\Models\Logistica\OrdenCompraDetalle;
use App\Models\Logistica\OrdenesView;
use App\Models\Tesoreria\RequerimientoPago;
use App\Models\Tesoreria\RequerimientoPagoDetalle;
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
        // return $request->all();exit;;
        // $req_pago = RequerimientoPago::whereMonth('fecha_registro',$request->mes);
        // if (!empty($request->division)) {
        //     $req_pago = $req_pago->where('id_division',$request->division);
        // }
        // $req_pago = $req_pago->get();

        $req_pago = RequerimientoPago::select('requerimiento_pago.*')
        ->whereDate('requerimiento_pago.fecha_autorizacion','>=','2023-01-01 00:00:00')
        ->whereDate('requerimiento_pago.fecha_autorizacion','<=','2023-04-30 23:59:59')
        ->where('requerimiento_pago.id_estado','=',6)
        ->join('tesoreria.requerimiento_pago_detalle','requerimiento_pago_detalle.id_requerimiento_pago','=','requerimiento_pago.id_requerimiento_pago')
        ->whereNull('requerimiento_pago_detalle.id_partida')
        ->whereNull('requerimiento_pago_detalle.id_partida_pi');
        if (!empty($request->division)) {
            $req_pago = $req_pago->where('requerimiento_pago.id_division',$request->division);
        }
        $req_pago = $req_pago
        ->groupBy('requerimiento_pago.id_requerimiento_pago')
        // ->where('requerimiento_pago_detalle.id_estado','!=',7)
        ->get();
        return DataTables::of($req_pago)
        ->addColumn('mes', function ($data){
            $fecha_como_entero = strtotime($data->fecha_autorizacion);
            $mes = date("m", $fecha_como_entero);

            return $mes;
        })
        // ->toJson();
        ->make(true);
    }
    public function listarOrdenes(Request $request)
    {


        // $ordenes = OrdenesView::select('ordenes_view.*')
        $ordenes = Orden::select('log_ord_compra.*')
        ->join('logistica.log_det_ord_compra','log_det_ord_compra.id_orden_compra','=','log_ord_compra.id_orden_compra')
        ->join('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','log_det_ord_compra.id_detalle_requerimiento')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento');
        ;

        if (!empty($request->division)) {
            $ordenes = $ordenes->where('alm_req.division_id',$request->division);
        }
        // if (!empty($request->mes)) {
        //     $ordenes = $ordenes->whereMonth('ordenes_view.fecha_emision',$request->mes);
        // }
        $ordenes = $ordenes->whereDate('log_ord_compra.fecha_autorizacion','>=','2023-01-01 00:00:00');
        $ordenes = $ordenes->whereDate('log_ord_compra.fecha_autorizacion','<=','2023-04-30 23:59:59');

        $ordenes = $ordenes->where('log_ord_compra.estado_pago',6)->groupBy('log_ord_compra.id_orden_compra')->get();
        // $ordenes = $ordenes->groupBy('log_det_ord_compra.id_orden_compra');
        return DataTables::of($ordenes)
        // ->toJson();
        ->make(true);
    }
    public function obtenerPresupuesto(Request $request)
    {
        $presupuesto_interno = PresupuestoInterno::where('id_area',$request->division)->where('estado','=',2)->first();

        if ($presupuesto_interno) {
            $presupuesto_interno_detalle = PresupuestoInternoDetalle::where('id_presupuesto_interno',$presupuesto_interno->id_presupuesto_interno)->where('estado',1)->orderBy('partida')->get();

            if ($presupuesto_interno_detalle) {
                return response()->json(["presupuesto"=>$presupuesto_interno,"presupuesto_detalle"=>$presupuesto_interno_detalle,"status"=>200],200);
            }
            return response()->json(["tipo"=>"warning","mensaje"=>"No cuenta con partidas asignadas", "titulo"=>"Alerta","status"=>400],200);
        }
        return response()->json(["tipo"=>"warning","mensaje"=>"No cuenta con un presupuesto", "titulo"=>"Alerta","status"=>400],200);

    }
    public function vincularPartida(Request $request)
    {
        // return response()->json($request->all(),200);exit;
        $variable = $request->tap;

        $afectaPresupuestoInternoResta = null;

        $tipo='success';
        $mensaje='Se asigno a la partida con exito';
        $titulo='Éxito';
        switch ($variable) {
            case 'orden':
                // $detalleArray = (new RegistroPagoController)->obtenerDetalleRequerimientoPagoParaPresupuestoInterno($request->requerimiento_pago_id,floatval($request->total_pago),'completo');
                // $afectaPresupuestoInternoResta = (new PresupuestoInternoController)->afectarPresupuestoInterno('resta','orden',$orden->id_orden_compra, $detalleParaPresupuestoRestaArray);
            break;

            case 'requerimiento de pago':


                $requerimiento_pago = RequerimientoPago::find($request->requerimiento_pago_id);
                $fecha_como_entero = strtotime($requerimiento_pago->fecha_autorizacion);
                $mes = date("m", $fecha_como_entero);


                $mes_string = ConfiguracionHelper::mesNumero($mes);
                $mes_text = ConfiguracionHelper::mesNumero($mes).'_aux';

                $saldo_presupuesto_detalle = PresupuestoInternoDetalle::where('id_presupuesto_interno_detalle',$request->presupuesto_interno_detalle_id)->first();

                $historial_saldo = HistorialPresupuestoInternoSaldo::where('id_requerimiento_pago_detalle',$request->requerimiento_pago_detalle_id)
                ->where('id_requerimiento_pago',$request->requerimiento_pago_id)
                ->first();

                if (!$historial_saldo) {


                    if (floatval($saldo_presupuesto_detalle->$mes_text)>=floatval($requerimiento_pago->monto_total)) {

                        $requerimiento_pago->id_presupuesto_interno=$request->presupuesto_interno_id;
                        $requerimiento_pago->save();
                        
                        $requerimiento_pago = RequerimientoPagoDetalle::find($request->requerimiento_pago_detalle_id);
                        $requerimiento_pago->id_partida_pi = $request->presupuesto_interno_detalle_id;
                        $requerimiento_pago->save();

                        #agrega un campo al detalle del requerimiento de pago
                        // $detalleArray = (new RegistroPagoController)->obtenerDetalleRequerimientoPagoParaPresupuestoInterno($request->requerimiento_pago_id,floatval($requerimiento_pago->monto_total),'completo');
                        #registra en la tabla saldo para su descuento
                        // (new PresupuestoInternoController)->afectarPresupuestoInterno('resta','requerimiento de pago',$request->requerimiento_pago_id,$detalleArray);

                        // $detalleArray = PresupuestoInternoHistorialHelper::obtenerDetalleRequerimientoPagoParaPresupuestoInterno($request->id_requerimiento_pago, floatval($requerimiento_pago->monto_total));
                        $tipo='info';
                        $mensaje = PresupuestoInternoHistorialHelper::normalizarRequerimientoDePago($request->requerimiento_pago_id);
                        $titulo='Información';

                        // PresupuestoInternoHistorialHelper::registrarEstadoGastoAfectadoDeRequerimientoPago($request->requerimiento_pago_id, $id_pago, $detalleArray, 'R');
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

        return response()->json(["tipo"=>$tipo,"mensaje"=>$mensaje,"titulo"=>$titulo],200);
    }
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
