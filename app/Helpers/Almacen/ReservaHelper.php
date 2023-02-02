<?php

namespace App\Helpers\Almacen;

use App\Helpers\Necesidad\RequerimientoHelper;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\almacen\GuiaCompraDetalle;
use App\Models\Almacen\Requerimiento;
use App\Models\Almacen\Reserva;
use App\Models\almacen\TransferenciaDetalle;
use App\Models\almacen\Transformacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Debugbar;

class ReservaHelper
{
    public function anularReservaDeProducto($idReserva=null,$idDetalleRequerimiento=null, $motivoDeAnulacion='')
    {
        try {
            DB::beginTransaction();
            $requerimientoHelper = new RequerimientoHelper();
            $CantidadGuiaComDet = 0;
            $CantidadTransDetalle = 0;
            $CantidadTransformado = 0;
            $cantidadEstadoNoElaborado = 0;
            $mensajeAdicional = '';
            $tipo_estado='';
            $reserva=[];

            if($idReserva!=null){
                $reserva = Reserva::where([['id_reserva', $idReserva]])->first();
            }else if($idDetalleRequerimiento!=null){
                if ($requerimientoHelper->EstaHabilitadoRequerimiento([$idDetalleRequerimiento]) == true) {
                    $reserva = Reserva::where([['id_detalle_requerimiento', $idDetalleRequerimiento]])->first();
                    
                }else{
                    return ['id_reserva' =>0, 'data' => [], 'tipo_estado' => 'warning', 'mensaje' => 'No puede anular la reserva, existe un requerimiento vinculado con estado "En pausa" o  "Por regularizar"'];
                }

            }

            $detReq = DetalleRequerimiento::where('id_detalle_requerimiento',$reserva->id_detalle_requerimiento)->first();
            $requerimiento= Requerimiento::find($detReq->id_requerimiento);


            if($requerimiento->id_tipo_requerimiento==4){
                $tipo_estado = $this->efectuarAnulacionReserva($reserva->id_reserva,$motivoDeAnulacion);

            }else{
                if ($reserva->estado == 1) {
                    if ($reserva->id_guia_com_det > 0) {
                        if(GuiaCompraDetalle::find($reserva->id_guia_com_det)){
                            if (GuiaCompraDetalle::find($reserva->id_guia_com_det)->estado != 7) {
                                $CantidadGuiaComDet++;
                            }
                        }
                    }
                    if ($reserva->id_trans_detalle > 0) {
                        if(TransferenciaDetalle::find($reserva->id_trans_detalle)){
                            if (TransferenciaDetalle::find($reserva->id_trans_detalle)->estado != 7) {
                                $CantidadTransDetalle++;
                            }
                        }
                    }
                    if ($reserva->id_transformado > 0) {
                        if(Transformacion::find($reserva->id_transformado)){
                            if (Transformacion::find($reserva->id_transformado)->estado != 7) {
                                $CantidadTransformado++;
                            }
                        }
                    }
                    if (($reserva->id_guia_com_det == null || $CantidadGuiaComDet == 0) && ($reserva->id_trans_detalle == null || $CantidadTransDetalle == 0) && ($reserva->id_transformado == null || $CantidadTransformado == 0)) {
                        $tipo_estado = $this->efectuarAnulacionReserva($reserva->id_reserva,$motivoDeAnulacion);
                    }
                } else {
                    $cantidadEstadoNoElaborado++;
                }
                if ($cantidadEstadoNoElaborado > 0) {
                    $mensajeAdicional .= ' Estados de reserva no permitido para anular.';
                }
                if ($CantidadGuiaComDet > 0) {
                    $mensajeAdicional .= ' Vínculo con guía.';
                }
                if ($CantidadTransDetalle > 0) {
                    $mensajeAdicional .= ' Vínculo con trasnferencia.';
                }
                if ($CantidadTransformado > 0) {
                    $mensajeAdicional .= ' Vínculo con item transformado.';
                }
            }


            if ($tipo_estado == 'success') {
                $mensaje = 'Se anuló la reserva.';
            } else {
                $tipo_estado = 'warning';
                $mensaje = 'No se pudo anular la reserva. ' . $mensajeAdicional;
            }
            $ReservasProductoActualizadas=[];
            $nuevoEstado['estado_actual'] = '';
            $nuevoEstado['lista_finalizados'] = '';
            $nuevoEstado['lista_restablecidos'] = '';

            if ($reserva->id_detalle_requerimiento > 0) {
                // actualizar estado de requerimiento solo si el tipo de requeriminto es distinto a 4 // compras para stock
                $detReq = DetalleRequerimiento::where('id_detalle_requerimiento',$reserva->id_detalle_requerimiento)->first();
                $req = Requerimiento::find($detReq->id_requerimiento);
                // if(intval($req->id_tipo_requerimiento) != 4){
                    DetalleRequerimiento::actualizarEstadoDetalleRequerimientoAtendido($reserva->id_detalle_requerimiento);
                    DB::commit();
                    $ReservasProductoActualizadas = Reserva::with('almacen', 'usuario', 'estado')->where('id_detalle_requerimiento', $reserva->id_detalle_requerimiento)->get();
                    $nuevoEstado = Requerimiento::actualizarEstadoRequerimientoAtendido('ANULAR', [$req->id_requerimiento]);
                // }else{
                //     $mensaje='El tipo de requerimiento, Compras para Stock';
                //     $tipo_estado = 'warning';

                // }

                // $detalleRequerimiento = DetalleRequerimiento::where('id_detalle_requerimiento', $reserva->id_detalle_requerimiento)->first();
            } 
            return ['id_reserva' =>$reserva->id_reserva, 'data' => $ReservasProductoActualizadas, 'tipo_estado' => $tipo_estado, 'mensaje' => $mensaje, 'estado_requerimiento' => $nuevoEstado['estado_actual'], 'lista_finalizados' => $nuevoEstado['lista_finalizados'], 'lista_restablecidos' => $nuevoEstado['lista_restablecidos']];
        
        } catch (\PDOException $e) {
            DB::rollBack();
            return ['id_reserva' =>0, 'data' => [], 'tipo_estado' => 'warning', 'mensaje' => 'No puede anular la reserva'];
        }
    }

    public function efectuarAnulacionReserva($id_reserva,$motivoDeAnulacion){
        $actualReserva = Reserva::where('id_reserva', $id_reserva)->first();
        $actualReserva->estado = 7;
        $actualReserva->usuario_anulacion = Auth::user()->id_usuario;
        $actualReserva->deleted_at = new Carbon();
        $actualReserva->motivo_anulacion = isset($motivoDeAnulacion) ? $motivoDeAnulacion : '';
        $actualReserva->save();
        return 'success';
    }

    public function anularTodaReservaDeProducto($idDetalleRequerimiento, $motivoDeAnulacion)
    {
        try {
            DB::beginTransaction();
            $requerimientoHelper = new RequerimientoHelper();
            $cantidadReservasAnuladas = 0;
            $totalReservas = 0;
            $CantidadGuiaComDet = 0;
            $CantidadTransDetalle = 0;
            $CantidadTransformado = 0;
            $cantidadEstadoElaborado = 0;
            $mensajeAdicional = '';
            if ($requerimientoHelper->EstaHabilitadoRequerimiento([$idDetalleRequerimiento]) == true) {

                $reservas = Reserva::where([['id_detalle_requerimiento', $idDetalleRequerimiento], ['estado', '!=', 7]])->get();
                foreach ($reservas as $r) {
                    $totalReservas++;
                    if ($r->estado == 1) {
                        if ($r->id_guia_com_det > 0) {
                            if(GuiaCompraDetalle::find($r->id_guia_com_det)){
                                if (GuiaCompraDetalle::find($r->id_guia_com_det)->estado != 7) {
                                    $CantidadGuiaComDet++;
                                }
                            }
                        }
                        if ($r->id_trans_detalle > 0) {
                            if(TransferenciaDetalle::find($r->id_trans_detalle)){
                                if (TransferenciaDetalle::find($r->id_trans_detalle)->estado != 7) {
                                    $CantidadTransDetalle++;
                                }
                            }
                        }
                        if ($r->id_transformado > 0) {
                            if(Transformacion::find($r->id_transformado)){
                                if (Transformacion::find($r->id_transformado)->estado != 7) {
                                    $CantidadTransformado++;
                                }
                            }
                        }
                        if (($r->id_guia_com_det == null || $CantidadGuiaComDet == 0) && ($r->id_trans_detalle == null || $CantidadTransDetalle == 0) && ($r->id_transformado == null || $CantidadTransformado == 0)) {
                            $reserva = Reserva::where('id_reserva', $r->id_reserva)->first();
                            $reserva->estado = 7;
                            $reserva->usuario_anulacion = Auth::user()->id_usuario;
                            $reserva->deleted_at = new Carbon();
                            $reserva->motivo_anulacion = isset($motivoDeAnulacion) ? $motivoDeAnulacion : '';
                            $reserva->save();
                            $tipo_estado = 'success';
                            $cantidadReservasAnuladas++;
                            $mensaje = 'Reserva Anulada';
                        }
                    } else {
                        $cantidadEstadoElaborado++;
                    }
                }

                if ($cantidadEstadoElaborado > 0) {
                    $mensajeAdicional .= ' Estados de reserva no permitido para anular.';
                }
                if ($CantidadGuiaComDet > 0) {
                    $mensajeAdicional .= ' Vínculo con guía.';
                }
                if ($CantidadTransDetalle > 0) {
                    $mensajeAdicional .= ' Vínculo con trasnferencia.';
                }
                if ($CantidadTransformado > 0) {
                    $mensajeAdicional .= ' Vínculo con item transformado.';
                }

                if (($cantidadReservasAnuladas > 0) && ($totalReservas == $cantidadReservasAnuladas)) {
                    $mensaje = 'Se anuló todas las reservas correspondientes al producto. ';
                } else if ($cantidadReservasAnuladas < $totalReservas && $cantidadReservasAnuladas != 0) {
                    $mensaje = 'Se puedo anular ' . $cantidadReservasAnuladas . ' reserva(s). ' . $mensajeAdicional;
                } else if ($cantidadReservasAnuladas == 0) {
                    $tipo_estado = 'warning';
                    $mensaje = 'No se pudo anular la reserva. ' . $mensajeAdicional;
                }
                
                
                // actualizar estado de requerimiento solo si el tipo de requerimiento es distinto a 4 :: compras para stock

                $detReq = DetalleRequerimiento::where('id_detalle_requerimiento',$idDetalleRequerimiento)->first();
                $req = Requerimiento::find($detReq->id_requerimiento);
                if(intval($req->id_tipo_requerimiento) !== 4){
                
                    DetalleRequerimiento::actualizarEstadoDetalleRequerimientoAtendido($idDetalleRequerimiento);
                    DB::commit();
                    // $Requerimiento = DetalleRequerimiento::where('id_detalle_requerimiento', $idDetalleRequerimiento)->first();
                    $nuevoEstado = Requerimiento::actualizarEstadoRequerimientoAtendido('ANULAR', [$req->id_requerimiento]);
                }else{
                    $nuevoEstado['estado_actual'] = '';
                    $nuevoEstado['lista_finalizados'] = '';
                    $nuevoEstado['lista_restablecidos'] = '';
                }

                $ReservasProductoActualizadas = Reserva::with('almacen', 'usuario', 'estado')->where([['id_detalle_requerimiento', $idDetalleRequerimiento],['estado',1]])->get();

                return ['data' => $ReservasProductoActualizadas, 'tipo_estado' => $tipo_estado, 'mensaje' => $mensaje, 'estado_requerimiento' => $nuevoEstado['estado_actual'], 'lista_finalizados' => $nuevoEstado['lista_finalizados'], 'lista_restablecidos' => $nuevoEstado['lista_restablecidos']];
            }
        } catch (\PDOException $e) {
            DB::rollBack();
            return ['data' => [], 'tipo_estado' => 'warning', 'mensaje' => 'No puede anular la reserva, hubo un problema en el servidor'];
        }
    }
}
