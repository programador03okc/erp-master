<?php

namespace App\Helpers\Finanzas;

use App\Http\Controllers\Finanzas\Presupuesto\PresupuestoInternoController;
use App\Models\Administracion\Operacion;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Requerimiento;
use App\Models\Finanzas\HistorialPresupuestoInternoSaldo;
use App\Models\Finanzas\PresupuestoInterno;
use App\Models\Finanzas\PresupuestoInternoDetalle;
use App\Models\Logistica\Orden;
use App\Models\Logistica\OrdenCompraDetalle;
use App\Models\Tesoreria\RequerimientoPago;
use App\Models\Tesoreria\RequerimientoPagoDetalle;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Debugbar;

class PresupuestoInternoHistorialHelper
{


    public static function registrarEstadoGastoAprobadoDeRequerimiento($idRequerimiento, $idTipoDocumento)
    {
        if ($idTipoDocumento == 1) {
            $requerimientoLogistico = Requerimiento::find($idRequerimiento);
            if ($requerimientoLogistico->id_presupuesto_interno > 0) {
                $detalle = DetalleRequerimiento::where([['id_requerimiento', '=', $idRequerimiento], ['estado', '!=', 7]])->get();
                foreach ($detalle as $key => $item) {
                    $importe = $item->cantidad * $item->precio_unitario;
                    $registroExistente = HistorialPresupuestoInternoSaldo::where([['id_requerimiento', $idRequerimiento], ['id_detalle_requerimiento', $item->id_detalle_requerimiento], ['estado', 1]])->get();
                    if (count($registroExistente) > 0) { // actualizar
                        PresupuestoInternoHistorialHelper::actualizarHistorialSaldoParaDetalleRequerimientoLogistico($requerimientoLogistico->id_presupuesto_interno, $item->id_partida_pi, $importe, 1, $item->id_requerimiento, $item->id_detalle_requerimiento, $requerimientoLogistico->fecha_requerimiento);
                    } else { //crear 
                        PresupuestoInternoHistorialHelper::registrarHistorialSaldoParaDetalleRequerimientoLogistico($requerimientoLogistico->id_presupuesto_interno, $item->id_partida_pi, $importe, 1, $item->id_requerimiento, $item->id_detalle_requerimiento, $requerimientoLogistico->fecha_requerimiento);
                    }
                }
            }
        } else if ($idTipoDocumento == 11) {
            $requerimientoPago = RequerimientoPago::find($idRequerimiento);
            if ($requerimientoPago->id_presupuesto_interno > 0) {
                $detalle = RequerimientoPagoDetalle::where([['id_requerimiento_pago', '=', $idRequerimiento], ['id_estado', '!=', 7]])->get();
                foreach ($detalle as $key => $item) {
                    $importe = $item->cantidad * $item->precio_unitario;
                    $registroExistente = HistorialPresupuestoInternoSaldo::where([['id_requerimiento_pago', $idRequerimiento], ['id_requerimiento_pago_detalle', $item->id_requerimiento_pago_detalle], ['estado', 1]])->get();

                    if (count($registroExistente) > 0) { // actualizar
                        PresupuestoInternoHistorialHelper::actualizarHistorialSaldoParaDetalleRequerimientoPago($requerimientoPago->id_presupuesto_interno, $item->id_partida_pi, $importe, 1, $item->id_requerimiento_pago, $item->id_requerimiento_pago_detalle, $requerimientoPago->fecha_registro);
                    } else { // crear
                        PresupuestoInternoHistorialHelper::registrarHistorialSaldoParaDetalleRequerimientoPago($requerimientoPago->id_presupuesto_interno, $item->id_partida_pi, $importe, 1, $item->id_requerimiento_pago, $item->id_requerimiento_pago_detalle, $requerimientoPago->fecha_registro);
                    }
                }
            }
        }
    }

    public static function registrarEstadoGastoAfectadoDeRequerimientoLogistico($idOrden, $idPago, $detalleItemList, $operacion)
    {
        // $orden = Orden::find($idOrden);
        // $detalleOrden = OrdenCompraDetalle::where([['id_orden_compra',$idOrden],['estado','!=',7]])->get();

        foreach ($detalleItemList as $detOrd) {
            if ($detOrd->id_detalle_requerimiento > 0) {
                if ($detOrd->detalleRequerimiento->id_partida_pi > 0) {
                    PresupuestoInternoHistorialHelper::registrarHistorialSaldoParaDetalleRequerimientoLogistico(
                        $detOrd->detalleRequerimiento->requerimiento->id_presupuesto_interno,
                        $detOrd->detalleRequerimiento->id_partida_pi,
                        $detOrd->importe_item_para_presupuesto,
                        3,
                        $detOrd->detalleRequerimiento->requerimiento->id_requerimiento,
                        $detOrd->id_detalle_requerimiento,
                        $detOrd->detalleRequerimiento->requerimiento->fecha_requerimiento,
                        $idOrden,
                        $detOrd->id_detalle_orden,
                        $idPago
                    );

                    PresupuestoInternoHistorialHelper::afectarPresupuesto(
                        $detOrd->detalleRequerimiento->requerimiento->id_presupuesto_interno,
                        $detOrd->detalleRequerimiento->id_partida_pi,
                        $detOrd->detalleRequerimiento->requerimiento->fecha_requerimiento,
                        $detOrd->importe_item_para_presupuesto,
                        $operacion
                    );
                }
            }
        }
    }





    public static function registrarHistorialSaldoParaDetalleRequerimientoLogistico($idPresupuesto, $idPartida, $importe, $estado, $idRequerimiento, $idDetalleRequerimiento, $fecha, $idOrden = null, $idDetalleOrden = null, $idPago = null)
    {

        $historial = null;
        if ($idPresupuesto > 0 && $idPartida > 0) {
            $historial = new HistorialPresupuestoInternoSaldo();
            $historial->id_presupuesto_interno = $idPresupuesto;
            $historial->id_partida = $idPartida;
            $historial->id_requerimiento = $idRequerimiento;
            $historial->id_requerimiento_detalle = $idDetalleRequerimiento;
            $historial->tipo = 'SALIDA';
            $historial->operacion = 'R';
            $historial->importe = $importe;
            $historial->mes = str_pad(date('m', strtotime($fecha)), 2, "0", STR_PAD_LEFT);
            $historial->fecha_registro = new Carbon();
            $historial->estado = $estado;
            $historial->id_orden = $idOrden;
            $historial->id_orden_detalle = $idDetalleOrden;
            $historial->id_pago = $idPago;
            $historial->save();
        }
        return $historial;
    }
    public static function actualizarHistorialSaldoParaDetalleRequerimientoLogistico($idPresupuesto, $idPartida, $importe, $estado, $idRequerimiento, $idDetalleRequerimiento, $fecha, $idOrden = null, $idDetalleOrden = null, $idPago = null)
    {

        $historial = null;
        if ($idDetalleRequerimiento > 0 && $idPartida > 0) {
            $historial = HistorialPresupuestoInternoSaldo::where([['id_requerimiento', $idRequerimiento], ['id_detalle_requerimiento', $idDetalleRequerimiento], ['estado', 1]])->first();
            $historial->id_presupuesto_interno = $idPresupuesto;
            $historial->id_partida = $idPartida;
            $historial->id_requerimiento = $idRequerimiento;
            $historial->id_requerimiento_detalle = $idDetalleRequerimiento;
            $historial->tipo = 'SALIDA';
            $historial->operacion = 'R';
            $historial->importe = $importe;
            $historial->mes = str_pad(date('m', strtotime($fecha)), 2, "0", STR_PAD_LEFT);
            $historial->fecha_registro = new Carbon();
            $historial->estado = $estado;
            $historial->id_orden = $idOrden;
            $historial->id_orden_detalle = $idDetalleOrden;
            $historial->id_pago = $idPago;
            $historial->save();
        }
        return $historial;
    }
    public static function actualizarHistorialSaldoParaDetalleRequerimientoLogisticoConOrden($idPresupuesto, $idPartida, $idRequerimiento, $idDetalleRequerimiento, $fecha, $idOrden, $idDetalleOrden, $importe, $estado, $operacion)
    {

        $historial = null;
        if ($idPresupuesto > 0 && $idPartida > 0) {
            $historial = HistorialPresupuestoInternoSaldo::where(
                [
                    ['id_presupuesto_interno', '=', $idPresupuesto],
                    ['id_partida', '=', $idPartida],
                    ['id_requerimiento', '=', $idRequerimiento],
                    ['id_requerimiento_detalle', '=', $idDetalleRequerimiento],
                    ['tipo', '=', 'SALIDA'],
                    ['mes', '=', str_pad(date('m', strtotime($fecha)), 2, "0", STR_PAD_LEFT)]
                ]
            )
                ->first();
            $historial->importe = $importe;
            $historial->id_orden = $idOrden;
            $historial->id_orden_detalle = $idDetalleOrden;
            $historial->estado = $estado;
            $historial->operacion = $operacion;
            $historial->save();
        }
        return $historial;
    }

    public static function registrarHistorialSaldoParaDetalleRequerimientoPago($idPresupuesto, $idPartida, $importe, $estado, $idRequerimientoPago, $idDetalleRequerimientoPago, $fecha, $idPago = null)
    {
        $historial = null;
        if ($idPresupuesto > 0 && $idPartida > 0) {
            $historial = new HistorialPresupuestoInternoSaldo();
            $historial->id_presupuesto_interno = $idPresupuesto;
            $historial->id_partida = $idPartida;
            $historial->id_requerimiento_pago = $idRequerimientoPago;
            $historial->id_requerimiento_pago_detalle = $idDetalleRequerimientoPago;
            $historial->tipo = 'SALIDA';
            $historial->operacion = 'R';
            $historial->importe = $importe;
            $historial->mes = str_pad(date('m', strtotime($fecha)), 2, "0", STR_PAD_LEFT);
            $historial->fecha_registro = new Carbon();
            $historial->estado = $estado;
            $historial->id_pago = $idPago;
            $historial->save();
        }
        return $historial;
    }

    public static function actualizarHistorialSaldoParaDetalleRequerimientoPago($idPresupuesto, $idPartida, $importe, $estado, $idRequerimientoPago, $idDetalleRequerimientoPago, $fecha, $idPago = null)
    {
        $historial = null;
        if ($idPresupuesto > 0 && $idPartida > 0) {
            $historial =  HistorialPresupuestoInternoSaldo::where([['id_requerimiento_pago', $idRequerimientoPago], ['id_requerimiento_pago_detalle', $idDetalleRequerimientoPago], ['estado', 1]])->first();
            $historial->id_presupuesto_interno = $idPresupuesto;
            $historial->id_partida = $idPartida;
            $historial->id_requerimiento_pago = $idRequerimientoPago;
            $historial->id_requerimiento_pago_detalle = $idDetalleRequerimientoPago;
            $historial->tipo = 'SALIDA';
            $historial->operacion = 'R';
            $historial->importe = $importe;
            $historial->mes = str_pad(date('m', strtotime($fecha)), 2, "0", STR_PAD_LEFT);
            $historial->fecha_registro = new Carbon();
            $historial->estado = $estado;
            $historial->id_pago = $idPago;
            $historial->save();
        }
        return $historial;
    }


    public static function afectarPresupuesto($idPresupuesto, $idPartida, $fechaOMes, $importe, $operacion)
    {

        $mesLista = ['1' => 'enero', '2' => 'febrero', '3' => 'marzo', '4' => 'abril', '5' => 'mayo', '6' => 'junio', '7' => 'julio', '8' => 'agosto', '9' => 'setiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre'];
        $mes = strlen($fechaOMes) == 2 ? intval($fechaOMes) : intval(date('m', strtotime($fechaOMes)));
        $nombreMes = $mesLista[$mes];
        $nombreMesAux = $nombreMes . '_aux';
        // $mesEnDosDigitos =str_pad($mes, 2, "0", STR_PAD_LEFT);

        $presupuestoInternoDetalle = PresupuestoInternoDetalle::where([
            ['id_presupuesto_interno', $idPresupuesto],
            ['estado', 1], ['id_presupuesto_interno_detalle', $idPartida]
        ])->first();

        if ($presupuestoInternoDetalle) {
            if ($operacion == 'R') { // SALIDA
                $nuevoImporte = floatval($presupuestoInternoDetalle->$nombreMesAux) -  (isset($importe) && ($importe > 0) ? floatval($importe) : 0);
                $presupuestoInternoDetalle->$nombreMesAux = $nuevoImporte;
                $presupuestoInternoDetalle->save();
            } elseif ($operacion == 'S') { // RETORNO
                $nuevoImporte = floatval($presupuestoInternoDetalle->$nombreMesAux) +  (isset($importe) && ($importe > 0) ? floatval($importe) : 0);
                $presupuestoInternoDetalle->$nombreMesAux = $nuevoImporte;
                $presupuestoInternoDetalle->save();
            }
        }

        return $presupuestoInternoDetalle;
    }


    public static function obtenerDetalleRequerimientoLogisticoDeOrdenParaAfectarPresupuestoInterno($idOrden, $totalPago)
    {

        $orden = Orden::find($idOrden);
        $porcentajeParaProrrateo =  (floatval($totalPago) * 100) / floatval($orden->monto_total);

        $detalleArray = [];
        if ($idOrden > 0) {
            $ordenDetalle = OrdenCompraDetalle::with('detalleRequerimiento.requerimiento')
                ->where([['id_orden_compra', $idOrden], ['estado', '!=', 7]])->get();

            foreach ($ordenDetalle as $detOrd) {
                if ($detOrd->id_detalle_requerimiento > 0) {

                    if ($detOrd->detalleRequerimiento->id_partida_pi > 0) {
                        $detalleArray[] = $detOrd;
                    }
                }
            }
        }
        if ($orden->incluye_igv == true) {
            foreach ($detalleArray as $key => $item) {
                $detalleArray[$key]['importe_item_para_presupuesto'] = ((floatval($item['cantidad']) * floatval($item['precio']) * 1.18) * $porcentajeParaProrrateo) / 100;
            }
        } else {
            foreach ($detalleArray as $key => $item) {
                $detalleArray[$key]['importe_item_para_presupuesto'] = ((floatval($item['cantidad']) * floatval($item['precio'])) * $porcentajeParaProrrateo) / 100;
            }
        }
        return $detalleArray;
    }
    public static function obtenerDetalleRequerimientoPagoParaPresupuestoInterno($idRequerimientoPago, $totalPago)
    {

        $requerimientoPago = RequerimientoPago::find($idRequerimientoPago);
        $porcentajeParaProrrateo =  (floatval($totalPago) * 100) / floatval($requerimientoPago->monto_total);


        $detalleArray = [];
        if ($idRequerimientoPago > 0) {
            $requerimientoPagoDetalle = RequerimientoPagoDetalle::where([['id_requerimiento_pago', $idRequerimientoPago], ['id_estado', '!=', 7]])->get();
            $detalleArray = $requerimientoPagoDetalle;
            // return $idRequerimientoPago;exit;
            foreach ($detalleArray as $key => $item) {
                $detalleArray[$key]['importe_item_para_presupuesto'] = 0;
            }

            foreach ($detalleArray as $key => $item) {
                $detalleArray[$key]['importe_item_para_presupuesto'] = ((floatval($item['cantidad']) * floatval($item['precio_unitario'])) * $porcentajeParaProrrateo) / 100;
            }
        }

        return $detalleArray;
    }

    public static function registrarEstadoGastoAfectadoDeRequerimientoPago($idRequerimientoPago, $idPago, $detalleItemList, $operacion)
    {

        foreach ($detalleItemList as $item) {
            if ($item->id_requerimiento_pago_detalle > 0) {
                if ($item->id_partida_pi > 0) {
                    $requerimientoPago = RequerimientoPago::find($item->id_requerimiento_pago);
                    PresupuestoInternoHistorialHelper::registrarHistorialSaldoParaDetalleRequerimientoPago(
                        $requerimientoPago->id_presupuesto_interno,
                        $item->id_partida_pi,
                        $item->importe_item_para_presupuesto,
                        3,
                        $idRequerimientoPago,
                        $item->id_requerimiento_pago_detalle,
                        $requerimientoPago->fecha_registro,
                        $idPago
                    );

                    PresupuestoInternoHistorialHelper::afectarPresupuesto(
                        $requerimientoPago->id_presupuesto_interno,
                        $item->id_partida_pi,
                        $requerimientoPago->fecha_registro,
                        $item->importe_item_para_presupuesto,
                        $operacion
                    );
                }
            }
        }
    }

    public static function actualizarRegistroPorDocumentoAnuladoEnHistorialSaldo($idrequerimiento = null, $idOrden = null, $idRequerimientoPago = null)
    {
        $historialList = [];
        $tienePresupuestoInterno = false;
        if ($idrequerimiento > 0) {
            $requerimiento = Requerimiento::find($idrequerimiento);

            if ($requerimiento->id_presupuesto_interno > 0) {
                $tienePresupuestoInterno = true;
            }

            if ($tienePresupuestoInterno == true) {
                $historialList = HistorialPresupuestoInternoSaldo::where(
                    [['id_requerimiento', '=', $idrequerimiento]]
                )
                    ->get();

                foreach ($historialList as $value) {
                    $historial = HistorialPresupuestoInternoSaldo::find($value->id);
                    $historial->documento_anulado = true;
                    $historial->save();
                }
            }
        }
        if ($idOrden > 0) {

            $detalleOrden = OrdenCompraDetalle::where([['id_orden_compra', $idOrden], ['estado', '!=', 7]])->get();
            foreach ($detalleOrden as $itemOrden) {
                if ($itemOrden->id_detalle_requerimiento > 0) {
                    $detalleRequerimiento = DetalleRequerimiento::with('requerimiento')->find($itemOrden->id_detalle_requerimiento);

                    foreach ($detalleRequerimiento as $detReq) {
                        if ($detReq->requerimiento->id_presupuesto_interno > 0) {
                            $tienePresupuestoInterno = true;
                        }
                    }
                }
            }

            if ($tienePresupuestoInterno == true) {
                $historialList = HistorialPresupuestoInternoSaldo::where(
                    [['id_orden', '=', $idOrden]]
                )
                    ->get();
            }

            foreach ($historialList as $value) {
                $historial = HistorialPresupuestoInternoSaldo::find($value->id);
                $historial->documento_anulado = true;
                $historial->save();
            }
        }

        if ($idRequerimientoPago) {
            $requerimientoPago = RequerimientoPago::find($idRequerimientoPago);

            if ($requerimientoPago->id_presupuesto_interno > 0) {
                $tienePresupuestoInterno = true;
            }

            if ($tienePresupuestoInterno == true) {
                $historialList = HistorialPresupuestoInternoSaldo::where(
                    [['id_requerimiento_pago', '=', $idRequerimientoPago]]
                )
                    ->get();

                foreach ($historialList as $value) {
                    $historial = HistorialPresupuestoInternoSaldo::find($value->id);
                    $historial->documento_anulado = true;
                    $historial->save();
                }
            }
        }
    }

    public static function registrarRetornoDePresupuesto($idPago)
    {
        $historialList = HistorialPresupuestoInternoSaldo::where([['id_pago', '=', $idPago], ['estado', 3]])->get();

        foreach ($historialList as $fila) {
            if ($fila->tipo == 'SALIDA') {
                $nuevoHistorial = new HistorialPresupuestoInternoSaldo();
                $nuevoHistorial->id_presupuesto_interno = $fila->id_presupuesto_interno;
                $nuevoHistorial->id_partida = $fila->id_partida;
                $nuevoHistorial->id_requerimiento = $fila->id_requerimiento;
                $nuevoHistorial->id_requerimiento_detalle = $fila->id_requerimiento_detalle;
                $nuevoHistorial->id_orden = $fila->id_orden;
                $nuevoHistorial->id_orden_detalle = $fila->id_orden_detalle;
                $nuevoHistorial->id_requerimiento_pago = $fila->id_requerimiento_pago;
                $nuevoHistorial->id_requerimiento_pago_detalle = $fila->id_requerimiento_pago_detalle;
                $nuevoHistorial->tipo = 'RETORNO';
                $nuevoHistorial->operacion = 'S';
                $nuevoHistorial->importe = $fila->importe;
                $nuevoHistorial->mes = $fila->mes;
                $nuevoHistorial->fecha_registro = new Carbon();
                $nuevoHistorial->estado = 3;
                $nuevoHistorial->id_pago = $fila->id_pago;
                $nuevoHistorial->save();

                PresupuestoInternoHistorialHelper::afectarPresupuesto(
                    $fila->id_presupuesto_interno,
                    $fila->id_partida,
                    $fila->mes,
                    $fila->importe,
                    'S'
                );
            }
        }

        return $historialList;
    }

    // public static function actualizaReqLogisticoEstadoHistorial($idDetalleRequerimiento,$estado,  $importe = null, $operacion=null)
    // {

    //     $detalleRequerimiento = DetalleRequerimiento::select('alm_req.id_presupuesto_interno',
    //     'alm_det_req.id_partida_pi','alm_req.id_requerimiento','alm_req.id_presupuesto_interno','alm_req.fecha_requerimiento',
    //     'alm_det_req.id_detalle_requerimiento','alm_det_req.precio_unitario','alm_det_req.cantidad',
    //     'log_det_ord_compra.id_detalle_orden','log_det_ord_compra.id_orden_compra','log_det_ord_compra.precio')
    //     ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
    //     ->leftjoin('logistica.log_det_ord_compra', function ($join) {
    //         $join->on('log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
    //         $join->where('log_det_ord_compra.estado', '!=', 7);
    //     })
    //     ->leftjoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
    //     ->where([['alm_det_req.id_detalle_requerimiento',$idDetalleRequerimiento],['log_ord_compra.estado', '!=', 7]])
    //     ->first();

    //     $historial = null;

    //     if ($detalleRequerimiento !== null && $detalleRequerimiento->id_presupuesto_interno !== null){
    //         $importe =  $importe !=null ? $importe:  (floatval($detalleRequerimiento->cantidad) * floatval($detalleRequerimiento->precio_unitario));

    //         if ($estado == 1){
    //             $historial = new HistorialPresupuestoInternoSaldo();
    //             $historial->id_presupuesto_interno = $detalleRequerimiento->id_presupuesto_interno;
    //             $historial->id_partida = $detalleRequerimiento->id_partida_pi;
    //             $historial->id_requerimiento = $detalleRequerimiento->id_requerimiento;
    //             $historial->id_requerimiento_detalle = $detalleRequerimiento->id_detalle_requerimiento;
    //             $historial->tipo = 'SALIDA';
    //             $historial->importe = $importe;
    //             $historial->mes = str_pad(date('m', strtotime($detalleRequerimiento->fecha_requerimiento) ), 2, "0", STR_PAD_LEFT);
    //             $historial->fecha_registro = new Carbon();
    //             $historial->estado = 1;
    //             $historial->save();

    //         } elseif($estado == 2) {
    //             $historial = HistorialPresupuestoInternoSaldo::where(
    //             [['id_presupuesto_interno','=',$detalleRequerimiento->id_presupuesto_interno],
    //                 ['id_partida','=',$detalleRequerimiento->id_partida_pi],
    //                 ['id_requerimiento','=',$detalleRequerimiento->id_requerimiento],
    //                 ['id_requerimiento_detalle','=',$detalleRequerimiento->id_detalle_requerimiento],
    //                 ['tipo','=','SALIDA'],
    //                 ['mes','=',str_pad(date('m', strtotime($detalleRequerimiento->fecha_requerimiento) ), 2, "0", STR_PAD_LEFT)]
    //                 ])
    //             ->first();

    //             $historial->importe = $importe;
    //             $historial->id_orden_detalle = $detalleRequerimiento->id_detalle_orden;
    //             $historial->id_orden = $detalleRequerimiento->id_orden_compra;
    //             $historial->estado = $estado;
    //             $historial->operacion = $operacion;
    //             $historial->save();

    //         }elseif($estado == 3 && $operacion !=null){ // afectar presupuesto

    //                 $historial = new HistorialPresupuestoInternoSaldo();
    //                 $historial->id_presupuesto_interno = $detalleRequerimiento->id_presupuesto_interno;
    //                 $historial->id_partida = $detalleRequerimiento->id_partida_pi;
    //                 $historial->id_requerimiento = $detalleRequerimiento->id_requerimiento;
    //                 $historial->id_requerimiento_detalle = $detalleRequerimiento->id_detalle_requerimiento;
    //                 $historial->tipo = 'SALIDA';
    //                 $historial->importe = $importe;
    //                 $historial->mes = str_pad(date('m', strtotime($detalleRequerimiento->fecha_requerimiento) ), 2, "0", STR_PAD_LEFT);
    //                 $historial->fecha_registro = new Carbon();
    //                 $historial->estado = 3;
    //                 $historial->save();

    //                 (new PresupuestoInternoController)->afectarPresupuestoPorFila($detalleRequerimiento->id_presupuesto_interno, $detalleRequerimiento->id_partida_pi, $importe, $detalleRequerimiento->fecha_requerimiento, $operacion);
    //             }
    //         }

    //     return $historial;
    // }

    // public static function actualizaReqPagoEstadoHistorial($idDetalleRequerimiento,$estado, $importe = null, $operacion=null)
    // {


    //     $detalleRequerimiento = DB::table('tesoreria.requerimiento_pago_detalle')->select('requerimiento_pago.id_presupuesto_interno',
    //     'requerimiento_pago_detalle.id_partida_pi','requerimiento_pago.id_requerimiento_pago','requerimiento_pago.fecha_registro',
    //     'requerimiento_pago_detalle.id_requerimiento_pago_detalle','requerimiento_pago_detalle.subtotal')
    //     ->join('tesoreria.requerimiento_pago','requerimiento_pago.id_requerimiento_pago','=','requerimiento_pago_detalle.id_requerimiento_pago')
    //     ->where('requerimiento_pago_detalle.id_requerimiento_pago_detalle',$idDetalleRequerimiento)
    //     ->first();

    //     // $operacion ='R';
    //     $historial = null;

    //     if ($detalleRequerimiento !== null && $detalleRequerimiento->id_presupuesto_interno !== null){

    //         $importe =  $importe !=null ? $importe: (floatval($detalleRequerimiento->subtotal));

    //         if ($estado == 1){
    //             $historial = new HistorialPresupuestoInternoSaldo();
    //             $historial->id_presupuesto_interno = $detalleRequerimiento->id_presupuesto_interno;
    //             $historial->id_partida = $detalleRequerimiento->id_partida_pi;
    //             $historial->id_requerimiento_pago = $detalleRequerimiento->id_requerimiento_pago;
    //             $historial->id_requerimiento_pago_detalle = $detalleRequerimiento->id_requerimiento_pago_detalle;
    //             $historial->tipo = 'SALIDA';
    //             $historial->importe = $importe;
    //             $historial->mes = str_pad(date('m', strtotime($detalleRequerimiento->fecha_registro) ), 2, "0", STR_PAD_LEFT);
    //             $historial->fecha_registro = new Carbon();
    //             $historial->estado = 1;
    //             $historial->save();
    //         } else {
    //             $historial = HistorialPresupuestoInternoSaldo::where(
    //                 ['id_presupuesto_interno','=',$detalleRequerimiento->id_presupuesto_interno],
    //                 ['id_partida','=',$detalleRequerimiento->id_partida_pi],
    //                 ['id_requerimiento_pago_detalle','=',$detalleRequerimiento->id_requerimiento_pago_detalle],
    //                 ['tipo','=','SALIDA'],
    //                 ['mes','=',str_pad(date('m', strtotime($detalleRequerimiento->fecha_registro) ), 2, "0", STR_PAD_LEFT)],
    //                 ['id_presupuesto_interno','=',$detalleRequerimiento->id_presupuesto_interno])
    //             ->first();

    //             $historial->importe = $importe;
    //             $historial->estado = $estado;
    //             $historial->save();


    //             if($estado == 3){ // afectar presupuesto
    //                 (new PresupuestoInternoController)->afectarPresupuestoPorFila($detalleRequerimiento->id_presupuesto_interno, $detalleRequerimiento->id_partida_pi, $importe, $detalleRequerimiento->fecha_registro, $operacion);
    //             }
    //         }
    //     }

    //     return $historial;
    // }



}
