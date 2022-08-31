<?php

namespace App\Helpers;

use App\Mail\EmailFinalizacionCuadroPresupuesto;
use App\Mail\EmailOrdenServicioOrdenTransformacion;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Requerimiento;
use App\Models\almacen\Transformacion;
use App\Models\Configuracion\Notificacion;
use App\Models\Configuracion\Usuario;
use App\Models\mgcp\CuadroCosto\CcAmFila;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use App\Models\mgcp\CuadroCosto\CuadroCostoView;
use App\Models\mgcp\OrdenCompra\Propia\AcuerdoMarco\OrdenCompraAm;
use App\Models\mgcp\OrdenCompra\Propia\OrdenCompraPropiaView;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Helpers\NotificacionHelper;

use Debugbar;


class CuadroPresupuestoHelper
{

    static public function finalizar($tipoPeticion,$listaRequerimientosParaFinalizar)
    {


        $payload = [];
        $payloadRestablecido=[];
        $codigoOportunidad=[];
        $destinatarios=[];
        $correoVendedor='';
        // $listaRestablecidos = [];
        $error = '';
        try {


            foreach ($listaRequerimientosParaFinalizar as $idReq) {
                $requerimiento = Requerimiento::find($idReq);
                $detalleRequerimiento = DetalleRequerimiento::where('id_requerimiento', $idReq)->get();


                foreach ($detalleRequerimiento as $dr) {

                    if ($dr->id_cc_am_filas!=null) { 
                        $ccAmFilas = CcAmFila::find($dr->id_cc_am_filas);
                        if ($dr->estado == 28 || $dr->estado == 5) {
                            $ccAmFilas->comprado = true;
                        }else{
                            $ccAmFilas->comprado = false;
                        }
                        $ccAmFilas->save();
                    }
                }


                if ($requerimiento->id_cc != null) {

                    $cuadroPresupuesto = CuadroCosto::where('id',$requerimiento->id_cc)->with('oportunidad','oportunidad.entidad','oportunidad.tipoNegocio','oportunidad.responsable','oportunidad.ordenCompraPropia')->first();
                    
                    if ($cuadroPresupuesto !=null && $cuadroPresupuesto->estado_aprobacion != 5) { // cuando el estado aprobacion de cc pendiente por regularizar no se puede actualizar el estado del cc
                        $cc = CuadroCosto::find($requerimiento->id_cc);

                        if ($requerimiento->estado == 28 || $requerimiento->estado == 5) {
                            $cc->estado_aprobacion = 4;// finalizado
                            $cc->save();
                            OrdenCompraPropiaView::actualizarEstadoCompra($cuadroPresupuesto->oportunidad->ordenCompraPropia,2);
                            /*$ordenPropia= OrdenCompraPropias::where('id_oportunidad',$cc->id_oportunidad)->first();
                            $ordenPropia->id_etapa= 2;// comprado 
                            $ordenPropia->save();*/

                            $codigoOportunidad[]=$cuadroPresupuesto->oportunidad->codigo_oportunidad;
                            $usuarioResponsable = DB::table('mgcp_usuarios.users')->where('id',$cuadroPresupuesto->oportunidad->id_responsable)->first();
                            $correoVendedor = $usuarioResponsable->email??'';
                            
                            $payload[] = [
                                'requerimiento' => $requerimiento,
                                'cuadro_presupuesto' => $cuadroPresupuesto,
                                'orden_compra_propia' => $cuadroPresupuesto->oportunidad->ordenCompraPropia,
                                'oportunidad' => $cuadroPresupuesto->oportunidad
                            ];
                            $destinatarios[]=$cuadroPresupuesto->oportunidad->responsable->email;


                        } else { // si el requerimiento no esta atentido total o reserva total 
                            if ($cc->estado_aprobacion == 4) { // verifica si el estado actual del cc es finalizado cuando el requerimiento no esta atentido 
                                $cc->estado_aprobacion = 3;
                                $cc->save();
                                $payloadRestablecido[] = [
                                    'requerimiento' => $requerimiento,
                                    'cuadro_presupuesto' => $cuadroPresupuesto,
                                    'orden_compra_propia' => $cuadroPresupuesto->oportunidad->ordenCompraPropia,
                                    'oportunidad' => $cuadroPresupuesto->oportunidad
                                ];
                            }
                        }
                        // preparar correo
                        if($tipoPeticion=='CREAR'){
                            $notificacionFinalizacionYOrdenServicio =CuadroPresupuestoHelper::enviarEmailNotificaciónFinalizaciónYOrdenServicio($requerimiento,$codigoOportunidad,$payload,$correoVendedor);
                            if($notificacionFinalizacionYOrdenServicio['estado']!='success'){
                                $error='Hubo un error al intentar enviar la notificación: '.$notificacionFinalizacionYOrdenServicio['mensaje'];
                            }
                        }
                    }
                }
            }
        } catch (Exception $ex) {
            $error = $ex->getMessage();
        }
        return ['lista_finalizados' => $payload, 'lista_restablecidos' => $payloadRestablecido, 'error' => $error];
    }


    static public function enviarEmailNotificaciónFinalizaciónYOrdenServicio($requerimiento,$codigoOportunidad,$payload,$correoVendedor){
        if (count($payload) > 0) {  // cuando tiene CDP finalizados
            $idUsuarioOrdenServicioTransformacion = [];
            $correoFinalizacionCuadroPresupuesto=[];
            $correosOrdenServicioTransformacion=[];
            $idUsuariosFinalizacionCDP = [];

            if (config('app.debug')) {
                $idUsuarioOrdenServicioTransformacion[] = Auth::user()->id_usuario;
                $idUsuariosFinalizacionCDP[]=  Auth::user()->id_usuario;
                $correosOrdenServicioTransformacion[]=  Auth::user()->email;
                $correoFinalizacionCuadroPresupuesto[]=  Auth::user()->email;
            } else {

                if($correoVendedor != '' || $correoVendedor !=null){
                    $correosOrdenServicioTransformacion[] = $correoVendedor; // agregar correo de vendedor
                }
                $idUsuarios = Usuario::getAllIdUsuariosPorRol(25); //Rol de usuario de despacho externo
                foreach ($idUsuarios as $id) {
                    $correosOrdenServicioTransformacion[] = Usuario::find($id)->email;
                }

                $idUsuarioOrdenServicioTransformacion= Usuario::getAllIdUsuariosPorRol(25); //Rol de usuario de despacho externo
                
                //$correoUsuarioEnSession=Auth::user()->email;
                $correoFinalizacionCuadroPresupuesto[] = Auth::user()->email;
                $correoFinalizacionCuadroPresupuesto[] = Usuario::find($requerimiento->id_usuario)->email;
                $idUsuariosFinalizacionCDP[] = Auth::user()->id_usuario;
                $idUsuariosFinalizacionCDP[] = $requerimiento->id_usuario;
            }
            
            // Mail::to(array_unique($correoFinalizacionCuadroPresupuesto))->send(new EmailFinalizacionCuadroPresupuesto($codigoOportunidad, $payload, Auth::user()->nombre_corto));
            foreach ($payload as $pl) { // enviar orde servicio / transformacion a multiples usuarios
                $transformacion =  Transformacion::select('transformacion.codigo', 'cc.id_oportunidad', 'adm_empresa.logo_empresa')
                ->leftjoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'transformacion.id_cc')
                ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'transformacion.id_almacen')
                ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
                ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                ->where('cc.id', $pl['cuadro_presupuesto']->id)
                ->first();
                $logoEmpresa=empty($transformacion->logo_empresa)?null:$transformacion->logo_empresa;
                $codigoTransformacion=empty($transformacion->codigo)?null:$transformacion->codigo;
                // Mail::to($correosOrdenServicioTransformacion)->send(new EmailOrdenServicioOrdenTransformacion($pl['oportunidad'],$logoEmpresa,$codigoTransformacion));
            }
            // Debugbar::info('debe guardar notificacion');
            
            // $notificacionOrdenServicioTransformacion = NotificacionHelper::notificacionOrdenServicioTransformacion($codigoTransformacion,$idUsuarioOrdenServicioTransformacion,$pl['oportunidad']);
            $notificacionOrdenServicioTransformacion['mensaje']='';
            // $notificacionFinalizacionCuadro = NotificacionHelper::notificacionFinalizacionCuadro($codigoOportunidad, $idUsuariosFinalizacionCDP, $payload);
            $notificacionFinalizacionCuadro['mensaje']='';
            // if($notificacionOrdenServicioTransformacion['estado'] =='success' && $notificacionFinalizacionCuadro['estado']=='success'){
                $estadoNotificacion='success';
            // }else{
            //     $estadoNotificacion='warning';
            // }
            return ['estado'=>$estadoNotificacion,'mensaje'=>$notificacionOrdenServicioTransformacion['mensaje'].'. '.$notificacionFinalizacionCuadro['mensaje'].'.'];
        }

    }


    // static public function notificarFinalizacion(){
    //    return NotificacionHelper::notificacionFinalizacionCuadro('OKC20202', 1, []);

    // }
}
