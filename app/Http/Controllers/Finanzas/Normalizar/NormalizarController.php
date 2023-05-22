<?php

namespace App\Http\Controllers\Finanzas\Normalizar;

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
        $req_pago = RequerimientoPago::whereMonth('fecha_registro',$request->mes);
        if (!empty($request->division)) {
            $req_pago = $req_pago->where('id_division',$request->division);
        }
        $req_pago = $req_pago->get();
        return DataTables::of($req_pago)
        // ->toJson();
        ->make(true);
    }
    public function listarOrdenes(Request $request)
    {
        // $ordenes = Orden::select('log_ord_compra.*')
        // ->join('logistica.log_det_ord_compra','log_det_ord_compra.id_orden_compra','=','log_ord_compra.id_orden_compra')
        // ->join('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','log_det_ord_compra.id_detalle_requerimiento')
        // ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento');

        // if (!empty($request->division)) {
        //     $ordenes = $ordenes->where('alm_req.division_id',$request->division);
        // }
        // if (!empty($request->mes)) {
        //     $ordenes = $ordenes->whereMonth('log_ord_compra.fecha_registro',$request->mes);
        // }
        // $ordenes = $ordenes->groupBy('log_ord_compra.id_orden_compra');
        // $ordenes = $ordenes->get();
        // return DataTables::of($ordenes)
        // ->make(true);



        $ordenes = OrdenesView::select('ordenes_view.*')
        ->join('logistica.log_det_ord_compra','log_det_ord_compra.id_orden_compra','=','ordenes_view.id')
        ->join('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','log_det_ord_compra.id_detalle_requerimiento')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento');
        ;

        if (!empty($request->division)) {
            $ordenes = $ordenes->where('alm_req.division_id',$request->division);
        }
        if (!empty($request->mes)) {
            $ordenes = $ordenes->whereMonth('ordenes_view.fecha_emision',$request->mes);
        }
        // $ordenes = $ordenes->groupBy('ordenes_view.id');
        $ordenes = $ordenes->get();

        return DataTables::of($ordenes)
        // ->toJson();
        ->make(true);
    }
    public function obtenerPresupuesto(Request $request)
    {
        $presupuesto_interno = PresupuestoInterno::where('id_area',$request->division)->where('estado','!=',7)->whereYear('fecha_registro',date('Y'))->first();
        $presupuesto_interno_detalle = PresupuestoInternoDetalle::where('id_presupuesto_interno',$presupuesto_interno->id_presupuesto_interno)->where('estado',1)->orderBy('partida')->get();
        return response()->json(["presupuesto"=>$presupuesto_interno,"presupuesto_detalle"=>$presupuesto_interno_detalle],200);
    }
    public function vincularPartida(Request $request)
    {
        // return response()->json($request->all(),200);exit;
        $variable = $request->tap;

        $afectaPresupuestoInternoResta = null;
        switch ($variable) {
            case 'orden':
                // $detalleArray = (new RegistroPagoController)->obtenerDetalleRequerimientoPagoParaPresupuestoInterno($request->requerimiento_pago_id,floatval($request->total_pago),'completo');
                // $afectaPresupuestoInternoResta = (new PresupuestoInternoController)->afectarPresupuestoInterno('resta','orden',$orden->id_orden_compra, $detalleParaPresupuestoRestaArray);
            break;

            case 'requerimiento de pago':
                $requerimiento_pago = RequerimientoPago::find($request->requerimiento_pago_id);
                $requerimiento_pago->id_presupuesto_interno=$request->presupuesto_interno_id;
                $requerimiento_pago->save();

                $detalleArray = (new RegistroPagoController)->obtenerDetalleRequerimientoPagoParaPresupuestoInterno($request->requerimiento_pago_id,floatval($requerimiento_pago->monto_total),'completo');

                $afectaPresupuestoInternoResta = (new PresupuestoInternoController)->afectarPresupuestoInterno('resta','requerimiento de pago',$request->requerimiento_pago_id,$detalleArray);
                return $afectaPresupuestoInternoResta;exit;
            break;
        }

        return response()->json(["success"=>$request->all()],200);
    }
    // public function FunctionName(Type $var = null)
    // {

    // }
}
