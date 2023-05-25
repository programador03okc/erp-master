<?php

namespace App\Helpers\Finanzas;

use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Finanzas\HistorialPresupuestoInternoSaldo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresupuestoInternoHistorialHelper
{
    public static function actualizaReqLogisticoEstadoHistorial($idDetalleRequerimiento,$estado)
    {
        $detalleRequerimiento = DB::table('almacen.alm_det_req')->select('alm_req.id_presupuesto_interno',
        'alm_det_req.id_partida_pi','alm_req.id_requerimiento','alm_req.id_presupuesto_interno','alm_req.fecha_requerimiento',
        'alm_det_req.id_detalle_requerimiento','alm_det_req.precio_unitario','alm_det_req.cantidad',
        'log_det_ord_compra.id_detalle_orden','log_det_ord_compra.id_orden_compra','log_det_ord_compra.precio')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
        ->leftjoin('logistica.log_det_ord_compra', function ($join) {
            $join->on('log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
            $join->where('log_det_ord_compra.estado', '!=', 7);
        })
        ->where('alm_det_req.id_detalle_requerimiento',$idDetalleRequerimiento)
        ->first();

        $historial = null;
        
        if ($detalleRequerimiento !== null && $detalleRequerimiento->id_presupuesto_interno !== null){
            if ($estado == 1){
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $detalleRequerimiento->id_presupuesto_interno;
                $historial->id_partida = $detalleRequerimiento->id_partida_pi;
                $historial->id_requerimiento = $detalleRequerimiento->id_requerimiento;
                $historial->id_requerimiento_detalle = $detalleRequerimiento->id_detalle_requerimiento;
                $historial->tipo = 'SALIDA';
                $historial->importe = ($detalleRequerimiento->cantidad * $detalleRequerimiento->precio_unitario);
                $historial->mes = date('m', strtotime($detalleRequerimiento->fecha_requerimiento) );
                $historial->fecha_registro = new Carbon();
                $historial->estado = 1;
                $historial->save();
            } else {
                $historial = HistorialPresupuestoInternoSaldo::where(
                    ['id_presupuesto_interno','=',$detalleRequerimiento->id_presupuesto_interno],
                    ['id_partida','=',$detalleRequerimiento->partida],
                    ['tipo','=','SALIDA'],
                    ['mes','=',date('m', strtotime($detalleRequerimiento->fecha_requerimiento))],
                    ['id_presupuesto_interno','=',$detalleRequerimiento->id_presupuesto_interno])
                ->first();
            
                $historial->importe = ($detalleRequerimiento->cantidad * $detalleRequerimiento->precio);
                $historial->id_orden_detalle = $detalleRequerimiento->id_detalle_orden;
                $historial->id_orden = $detalleRequerimiento->id_orden_compra;
                $historial->estado = $estado;
                $historial->save();
            }
        }
        
        return $historial;
    }

    public static function actualizaReqPagoEstadoHistorial($idDetalleRequerimiento,$estado)
    {
        $detalleRequerimiento = DB::table('tesoreria.requerimiento_pago_detalle')->select('requerimiento_pago.id_presupuesto_interno',
        'requerimiento_pago_detalle.id_partida_pi','requerimiento_pago.id_requerimiento_pago','requerimiento_pago.fecha_registro',
        'requerimiento_pago_detalle.id_requerimiento_pago_detalle','requerimiento_pago_detalle.subtotal')
        ->join('tesoreria.requerimiento_pago','requerimiento_pago.id_requerimiento_pago','=','requerimiento_pago_detalle.id_requerimiento_pago')
        ->where('requerimiento_pago_detalle.id_requerimiento_pago_detalle',$idDetalleRequerimiento)
        ->first();

        $historial = null;
        
        if ($detalleRequerimiento !== null && $detalleRequerimiento->id_presupuesto_interno !== null){
            if ($estado == 1){
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $detalleRequerimiento->id_presupuesto_interno;
                $historial->id_partida = $detalleRequerimiento->id_partida_pi;
                $historial->id_requerimiento_pago = $detalleRequerimiento->id_requerimiento_pago;
                $historial->id_requerimiento_pago_detalle = $detalleRequerimiento->id_requerimiento_pago_detalle;
                $historial->tipo = 'SALIDA';
                $historial->importe = ($detalleRequerimiento->subtotal);
                $historial->mes = date('m', strtotime($detalleRequerimiento->fecha_registro) );
                $historial->fecha_registro = new Carbon();
                $historial->estado = 1;
                $historial->save();
            } else {
                $historial = HistorialPresupuestoInternoSaldo::where(
                    ['id_presupuesto_interno','=',$detalleRequerimiento->id_presupuesto_interno],
                    ['id_partida','=',$detalleRequerimiento->id_partida_pi],
                    ['tipo','=','SALIDA'],
                    ['mes','=',date('m', strtotime($detalleRequerimiento->fecha_registro))],
                    ['id_presupuesto_interno','=',$detalleRequerimiento->id_presupuesto_interno])
                ->first();
            
                $historial->importe = ($detalleRequerimiento->subtotal);
                $historial->estado = $estado;
                $historial->save();
            }
        }
        
        return $historial;
    }
}
