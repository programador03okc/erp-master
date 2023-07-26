<?php

namespace App\Helpers\Finanzas;

use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Requerimiento;
use App\Models\Finanzas\HistorialPresupuestoInternoSaldo;
use App\Models\Finanzas\PresupuestoInternoDetalle;
use App\Models\Logistica\Orden;
use App\Models\Logistica\OrdenCompraDetalle;
use App\Models\Tesoreria\RegistroPago;
use App\Models\Tesoreria\RequerimientoPago;
use App\Models\Tesoreria\RequerimientoPagoDetalle;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Debugbar;
use Exception;

class PresupuestoInternoHistorialHelper
{


    public static function registrarEstadoGastoAprobadoDeRequerimiento($idRequerimiento, $idTipoDocumento)
    {
        if ($idTipoDocumento == 1) {
            $requerimientoLogistico = Requerimiento::find($idRequerimiento);
            if ($requerimientoLogistico->id_presupuesto_interno > 0) {
                $detalle = DetalleRequerimiento::where([['id_requerimiento', '=', $idRequerimiento], ['estado', '!=', 7]])->get();
                foreach ($detalle as $key => $item) {
                    if($requerimientoLogistico->id_moneda == 2) { // Si la moneda es dolares -> convertir a soles usando el tipo de cambio venta, si no existe el tiempo de cambio devolvera el precio unitario original.
                        $precioUnitario = PresupuestoInternoHistorialHelper::obtenerTipoCambioASoles($requerimientoLogistico->fecha_requerimiento,floatval($item['precio_unitario']));
                    }else{
                        $precioUnitario = floatval($item['precio_unitario']);
                    }
                    $importe =floatval($item->cantidad) * floatval($precioUnitario) * floatval(1.18); // incluir IGV
                    $registroExistente = HistorialPresupuestoInternoSaldo::where([['id_requerimiento', $idRequerimiento], ['id_requerimiento_detalle', $item->id_detalle_requerimiento], ['estado', 1]])->get();
                    if (count($registroExistente) > 0) { // actualizar
                        PresupuestoInternoHistorialHelper::actualizarHistorialSaldoParaDetalleRequerimientoLogistico($requerimientoLogistico->id_presupuesto_interno, $item->id_partida_pi, $importe, 1, $item->id_requerimiento, $item->id_detalle_requerimiento, $requerimientoLogistico->fecha_requerimiento,null,null,null,'Actualizar afectación regular');
                    } else { //crear
                        PresupuestoInternoHistorialHelper::registrarHistorialSaldoParaDetalleRequerimientoLogistico($requerimientoLogistico->id_presupuesto_interno, $item->id_partida_pi, $importe, 1, $item->id_requerimiento, $item->id_detalle_requerimiento, $requerimientoLogistico->fecha_requerimiento,null,null,null,'Registrar afectación regular');
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
                        PresupuestoInternoHistorialHelper::actualizarHistorialSaldoParaDetalleRequerimientoPago($requerimientoPago->id_presupuesto_interno, $item->id_partida_pi, $importe, 1, $item->id_requerimiento_pago, $item->id_requerimiento_pago_detalle, $requerimientoPago->fecha_registro,null, 'Actualizar afectación regular');
                    } else { // crear
                        PresupuestoInternoHistorialHelper::registrarHistorialSaldoParaDetalleRequerimientoPago($requerimientoPago->id_presupuesto_interno, $item->id_partida_pi, $importe, 1, $item->id_requerimiento_pago, $item->id_requerimiento_pago_detalle, $requerimientoPago->fecha_registro,null ,'Registrar afectación regular');
                    }
                }
            }
        }
    }

    public static function registrarEstadoGastoAfectadoDeRequerimientoLogistico($idOrden, $idPago, $detalleItemList, $operacion, $fechaAfectacion, $descripcion)
    {
        $orden = Orden::find($idOrden);
        $presupuestoInternoDetalle = [];
        foreach ($detalleItemList as $detOrd) {
            if ($detOrd->id_detalle_requerimiento > 0) {
                // Debugbar::info($detOrd->detalleRequerimiento->id_partida_pi);

                if ($detOrd->detalleRequerimiento->id_partida_pi > 0) {
                    // Debugbar::info($detOrd->detalleRequerimiento->requerimiento->id_presupuesto_interno,
                    // $detOrd->detalleRequerimiento->id_partida_pi,
                    // $detOrd->importe_item_para_presupuesto,
                    // 3,
                    // $detOrd->detalleRequerimiento->requerimiento->id_requerimiento,
                    // $detOrd->id_detalle_requerimiento,
                    // $fechaAfectacion,
                    // $idOrden,
                    // $detOrd->id_detalle_orden,
                    // $idPago,
                    // $descripcion);

                    PresupuestoInternoHistorialHelper::registrarHistorialSaldoParaDetalleRequerimientoLogistico(
                        $detOrd->detalleRequerimiento->requerimiento->id_presupuesto_interno,
                        $detOrd->detalleRequerimiento->id_partida_pi,
                        $detOrd->importe_item_para_presupuesto,
                        3,
                        $detOrd->detalleRequerimiento->requerimiento->id_requerimiento,
                        $detOrd->id_detalle_requerimiento,
                        $fechaAfectacion,
                        $idOrden,
                        $detOrd->id_detalle_orden,
                        $idPago,
                        $descripcion
                    );

                    $presupuestoInternoDetalle =   PresupuestoInternoHistorialHelper::afectarPresupuesto(
                        $detOrd->detalleRequerimiento->requerimiento->id_presupuesto_interno,
                        $detOrd->detalleRequerimiento->id_partida_pi,
                        $fechaAfectacion,
                        $detOrd->importe_item_para_presupuesto,
                        $operacion
                    );
                }
            }
        }
        return $presupuestoInternoDetalle;
    }





    public static function registrarHistorialSaldoParaDetalleRequerimientoLogistico($idPresupuesto, $idPartida, $importe, $estado, $idRequerimiento, $idDetalleRequerimiento, $fecha, $idOrden = null, $idDetalleOrden = null, $idPago = null, $descripcion =null)
    {

        $historial = null;
        if ($idPresupuesto > 0 && $idPartida > 0) {
            $historial = new HistorialPresupuestoInternoSaldo();
            $historial->id_presupuesto_interno = $idPresupuesto;
            $historial->id_partida = $idPartida;
            $historial->id_requerimiento = $idRequerimiento;
            $historial->id_requerimiento_detalle = $idDetalleRequerimiento;
            $historial->tipo = 'SALIDA';
            $historial->descripcion = $descripcion;            
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
    public static function actualizarHistorialSaldoParaDetalleRequerimientoLogistico($idPresupuesto, $idPartida, $importe, $estado, $idRequerimiento, $idDetalleRequerimiento, $fecha, $idOrden = null, $idDetalleOrden = null, $idPago = null,$descripcion=null)
    {

        $historial = null;
        if ($idDetalleRequerimiento > 0 && $idPartida > 0) {
            $historial = HistorialPresupuestoInternoSaldo::where([['id_requerimiento', $idRequerimiento], ['id_requerimiento_detalle', $idDetalleRequerimiento], ['estado', 1]])->first();
            $historial->id_presupuesto_interno = $idPresupuesto;
            $historial->id_partida = $idPartida;
            $historial->id_requerimiento = $idRequerimiento;
            $historial->id_requerimiento_detalle = $idDetalleRequerimiento;
            $historial->tipo = 'SALIDA';
            $historial->descripcion = $descripcion;
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
                    ['descripcion', '=', 'Actualizar registro según la orden'],
                    ['mes', '=', str_pad(date('m', strtotime($fecha)), 2, "0", STR_PAD_LEFT)]
                ]
            )->first();

            if($historial){
                $historial->importe = $importe;
                $historial->id_orden = $idOrden;
                $historial->id_orden_detalle = $idDetalleOrden;
                $historial->estado = $estado;
                $historial->operacion = $operacion;
                $historial->save();
            }else{
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $idPresupuesto;
                $historial->id_partida = $idPartida;
                $historial->id_requerimiento = $idRequerimiento;
                $historial->id_requerimiento_detalle = $idDetalleRequerimiento;
                $historial->tipo = 'SALIDA';
                $historial->descripcion = 'Registrar nuevo según la orden';            
                $historial->operacion = $operacion;
                $historial->importe = $importe;
                $historial->mes = str_pad(date('m', strtotime($fecha)), 2, "0", STR_PAD_LEFT);
                $historial->fecha_registro = new Carbon();
                $historial->estado = $estado;
                $historial->id_orden = $idOrden;
                $historial->id_orden_detalle = $idDetalleOrden;
                $historial->save();
            }
        }
        return $historial;
    }

    public static function registrarHistorialSaldoParaDetalleRequerimientoPago($idPresupuesto, $idPartida, $importe, $estado, $idRequerimientoPago, $idDetalleRequerimientoPago, $fecha, $idPago = null, $descripcion = null)
    {
        $historial = null;
        if ($idPresupuesto > 0 && $idPartida > 0) {
            $historial = new HistorialPresupuestoInternoSaldo();
            $historial->id_presupuesto_interno = $idPresupuesto;
            $historial->id_partida = $idPartida;
            $historial->id_requerimiento_pago = $idRequerimientoPago;
            $historial->id_requerimiento_pago_detalle = $idDetalleRequerimientoPago;
            $historial->tipo = 'SALIDA';
            $historial->descripcion = $descripcion;
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

    public static function actualizarHistorialSaldoParaDetalleRequerimientoPago($idPresupuesto, $idPartida, $importe, $estado, $idRequerimientoPago, $idDetalleRequerimientoPago, $fecha, $idPago = null, $descripcion = null)
    {
        $historial = null;
        if ($idPresupuesto > 0 && $idPartida > 0) {
            $historial =  HistorialPresupuestoInternoSaldo::where([['id_requerimiento_pago', $idRequerimientoPago], ['id_requerimiento_pago_detalle', $idDetalleRequerimientoPago], ['estado', 1]])->first();
            $historial->id_presupuesto_interno = $idPresupuesto;
            $historial->id_partida = $idPartida;
            $historial->id_requerimiento_pago = $idRequerimientoPago;
            $historial->id_requerimiento_pago_detalle = $idDetalleRequerimientoPago;
            $historial->tipo = 'SALIDA';
            $historial->descripion = $descripcion;
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


    public static function obtenerDetalleRequerimientoLogisticoDeOrdenParaAfectarPresupuestoInterno($idOrden, $totalPago, $idDetalleRequerimientoLogistico = null)
    {

        $orden = Orden::find($idOrden);
        $porcentajeParaProrrateo =  (floatval($totalPago) * 100) / floatval($orden->monto_total);

        $detalleArray = [];
        if ($idOrden > 0) {
            $ordenDetalle = OrdenCompraDetalle::with('detalleRequerimiento.requerimiento')
                ->where([['id_orden_compra', $idOrden], ['estado', '!=', 7]])->get();

            foreach ($ordenDetalle as $detOrd) {

                if($idDetalleRequerimientoLogistico !=null){ // si exista un id de detalle requerimiento logistico pasado como parametro
                    if ($detOrd->id_detalle_requerimiento == $idDetalleRequerimientoLogistico) {
                        if ($detOrd->detalleRequerimiento->id_partida_pi > 0) {
                            $detalleArray[] = $detOrd;
                        }
                    }

                }else{ // de lo contrario debe recorrera todo el detalle

                    if ($detOrd->id_detalle_requerimiento > 0) {
    
                        if ($detOrd->detalleRequerimiento->id_partida_pi > 0) {
                            $detalleArray[] = $detOrd;
                        }
                    }
                }
            }
        }
        if ($orden->incluye_igv == true) {
            foreach ($detalleArray as $key => $item) {

                if($orden->id_moneda == 2) { // Si la moneda es dolares -> convertir a soles usando el tipo de cambio venta, si no existe el tiempo de cambio devolvera el precio unitario original.
                    $precioUnitario = PresupuestoInternoHistorialHelper::obtenerTipoCambioASoles($orden->fecha_registro,floatval($item['precio']));
                }else{
                    $precioUnitario = floatval($item['precio']);
                }

                $detalleArray[$key]['importe_item_para_presupuesto'] = ((floatval($item['cantidad']) * $precioUnitario * 1.18) * $porcentajeParaProrrateo) / 100;
            }
        } else {
            foreach ($detalleArray as $key => $item) {

                if($orden->id_moneda == 2) { // Si la moneda es dolares -> convertir a soles usando el tipo de cambio venta, si no existe el tiempo de cambio devolvera el precio unitario original.
                    $precioUnitario = PresupuestoInternoHistorialHelper::obtenerTipoCambioASoles($orden->fecha_registro,floatval($item['precio']));
                }else{
                    $precioUnitario = floatval($item['precio']);
                }
                
                $detalleArray[$key]['importe_item_para_presupuesto'] = ((floatval($item['cantidad']) * $precioUnitario) * $porcentajeParaProrrateo) / 100;
            }
        }
        return $detalleArray;
    }
    public static function obtenerDetalleRequerimientoPagoParaPresupuestoInterno($idRequerimientoPago, $totalPago, $idDetalleRequerimientoPago=null)
    {

        $requerimientoPago = RequerimientoPago::find($idRequerimientoPago);
        $porcentajeParaProrrateo =  (floatval($totalPago) * 100) / floatval($requerimientoPago->monto_total);


        $detalleArray = [];
        if ($idRequerimientoPago > 0) {
            if($idDetalleRequerimientoPago !=null ){ // si es mayor a cero, es por el caso donde se normaliza el ppto pasando solo un item

                $requerimientoPagoDetalle = RequerimientoPagoDetalle::where([['id_requerimiento_pago', $idRequerimientoPago],['id_requerimiento_pago_detalle',$idDetalleRequerimientoPago], ['id_estado', '!=', 7]])->get();
            }else{
                $requerimientoPagoDetalle = RequerimientoPagoDetalle::where([['id_requerimiento_pago', $idRequerimientoPago], ['id_estado', '!=', 7]])->get();

            }
            $detalleArray = $requerimientoPagoDetalle;
            // return $idRequerimientoPago;exit;
            foreach ($detalleArray as $key => $item) {
                $detalleArray[$key]['importe_item_para_presupuesto'] = 0;
            }

            foreach ($detalleArray as $key => $item) { 
                if($requerimientoPago->id_moneda == 2) { // Si la moneda es dolares -> convertir a soles usando el tipo de cambio venta, si no existe el tiempo de cambio devolvera el precio unitario original.
                    $precioUnitario = PresupuestoInternoHistorialHelper::obtenerTipoCambioASoles($requerimientoPago->fecha_registro,floatval($item['precio_unitario']));
                }else{
                    $precioUnitario = floatval($item['precio_unitario']);
                }
                $detalleArray[$key]['importe_item_para_presupuesto'] = ((floatval($item['cantidad']) * $precioUnitario) * $porcentajeParaProrrateo) / 100;
            }
        }

        return $detalleArray;
    }

    public static function registrarEstadoGastoAfectadoDeRequerimientoPago($idRequerimientoPago, $idPago, $detalleItemList, $operacion, $fechaAfectacion, $descripcion)
    {

        $presupuestoInternoDetalle = [];

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
                        $fechaAfectacion,//$requerimientoPago->fecha_registro,
                        $idPago,
                        $descripcion
                    );

                    $presupuestoInternoDetalle = PresupuestoInternoHistorialHelper::afectarPresupuesto(
                        $requerimientoPago->id_presupuesto_interno,
                        $item->id_partida_pi,
                        $fechaAfectacion,//$requerimientoPago->fecha_registro,
                        $item->importe_item_para_presupuesto,
                        $operacion
                    );
                }
            }
        }
        return $presupuestoInternoDetalle;
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
                $nuevoHistorial->descripcion = 'Retorno de presupuesto';
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

    public static function normalizarRequerimientoDePago($idRequerimientoPago, $idDetalleRequerimientoPago)
    {

        try {
            DB::beginTransaction();
        $registroPago = RegistroPago::where([['id_requerimiento_pago', $idRequerimientoPago], ['estado', '!=', 7]])->get();
        $mensaje = '';
        $presupuestoInternoDetalle = [];
        $totalImporteRegistroPago = 0;
        // if ($registroPago) {
        if (sizeof($registroPago)>0) {
            $mensaje = 'Se encontro el registro de pago';
            foreach ($registroPago as $rp) {

                $detalleArray = PresupuestoInternoHistorialHelper::obtenerDetalleRequerimientoPagoParaPresupuestoInterno($idRequerimientoPago, floatval($rp->total_pago), $idDetalleRequerimientoPago); // * pasar parametro $idDetalleRequerimientoPago para el caso de normaliazar, asi devolver solo un item
                $presupuestoInternoDetalle = PresupuestoInternoHistorialHelper::registrarEstadoGastoAfectadoDeRequerimientoPago($idRequerimientoPago, $rp->id_pago, $detalleArray, 'R', $rp->fecha_pago, "Registrar afectación por regularización");
                $totalImporteRegistroPago += $rp->total_pago;
            }

            if ($presupuestoInternoDetalle != null) {

                $mensaje .= '. Se afectó presupuesto';
            }

            $requerimientoPago = RequerimientoPago::find($idRequerimientoPago);
            // $requerimientoPago->estado_normalizacion_presupuesto_interno = 1;
            $requerimientoPago->save();

            // $requerimientoPago->afectado_presupuesto_interno= true;
            //     if($totalImporteRegistroPago == $montoTotalRequerimientoPago){
            // }
        } else {
            $mensaje = 'No se encontró registro de pago para vincular';
        }

        DB::commit();
        return $mensaje;

        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }
    public static function normalizarOrden($idOrden, $idDetalleRequerimientoLogistico)
    {

        try {
            DB::beginTransaction();
        $registroPago = RegistroPago::where([['id_oc', $idOrden], ['estado', '!=', 7]])->get();
        $orden = Orden::find($idOrden);
        $mensaje = '';
        $presupuestoInternoDetalle = [];
        $totalImporteRegistroPago = 0;
        // if ($registroPago) {
        if (sizeof($registroPago)>0) {
            $mensaje = 'Se encontro el registro de pago';
            
            foreach ($registroPago as $rp) {
                $totalImporteRegistroPago += floatval($rp->total_pago);
            }

            if(floatval($totalImporteRegistroPago) == floatval($orden->monto_total)){
                $detalleArray = PresupuestoInternoHistorialHelper::obtenerDetalleRequerimientoLogisticoDeOrdenParaAfectarPresupuestoInterno($idOrden, floatval($rp->total_pago), $idDetalleRequerimientoLogistico); // * pasar parametro $idDetalleRequerimientoLogistico para el caso de normaliazar, asi devolver solo un item
                $presupuestoInternoDetalle = PresupuestoInternoHistorialHelper::registrarEstadoGastoAfectadoDeRequerimientoLogistico($idOrden, $rp->id_pago, $detalleArray, 'R', $rp->fecha_pago, "Registrar afectación por regularización");
                

            }else{
                $mensaje .= '. El monto de total de pagos (tesoreria) '.floatval($totalImporteRegistroPago).' no es igual al monto total de la orden '.floatval($orden->monto_total);


                $requerimiento_detalle = DetalleRequerimiento::find($idDetalleRequerimientoLogistico);
                $requerimiento_detalle->id_partida_pi = null;
                $requerimiento_detalle->save();

                $requerimiento = Requerimiento::find($requerimiento_detalle->id_requerimiento);
                $requerimiento->id_presupuesto_interno = null;
                $requerimiento->save();
            }

            if ($presupuestoInternoDetalle != null) {

                $mensaje .= '. Se afectó presupuesto';
            }

            // $requerimientoPago = RequerimientoPago::find($idOrden);
            // $requerimientoPago->estado_normalizacion_presupuesto_interno = 1;
            // $requerimientoPago->save();

            // $requerimientoPago->afectado_presupuesto_interno= true;
            //     if($totalImporteRegistroPago == $montoTotalRequerimientoPago){
            // }
        } else {
            $requerimiento_detalle = DetalleRequerimiento::find($idDetalleRequerimientoLogistico);
            $requerimiento_detalle->id_partida_pi = null;
            $requerimiento_detalle->save();

            $requerimiento = Requerimiento::find($requerimiento_detalle->id_requerimiento);
            $requerimiento->id_presupuesto_interno = null;
            $requerimiento->save();
            $mensaje = 'No se encontró registro de pago para vincular';
        }

        DB::commit();
        return $mensaje;

        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public static function obtenerTipoCambioASoles($fechaRegistro,$precioUnitario)
        { 
            $precioUnitarioSoles=$precioUnitario;
            $data = DB::table('contabilidad.cont_tp_cambio')
            ->where('cont_tp_cambio.fecha', '<=', $fechaRegistro)
            ->orderBy('fecha', 'desc')
            ->first();

            if($data->venta !=null && floatval($data->venta)>0){

                $precioUnitarioSoles = floatval($precioUnitario) * floatval($data->venta);
            }

            return $precioUnitarioSoles;
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
