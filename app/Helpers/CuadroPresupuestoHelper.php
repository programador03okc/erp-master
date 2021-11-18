<?php

namespace App\Helpers;

use App\Mail\EmailFinalizacionCuadroPresupuesto;
use App\Mail\EmailOrdenServicioOrdenTransformacion;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Requerimiento;
use App\Models\almacen\Transformacion;
use App\Models\Configuracion\Usuario;
use App\Models\mgcp\AcuerdoMarco\OrdenCompraPropias;
use App\Models\mgcp\CuadroCosto\CcAmFila;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use App\Models\mgcp\CuadroCosto\CuadroCostoView;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class CuadroPresupuestoHelper
{

    static public function finalizar($listaRequerimientosParaFinalizar)
    {

        $payload = [];
        $codigoOportunidad='';
        $destinatarios=[];
        // $listaRestablecidos = [];
        $error = '';
        try {


            foreach ($listaRequerimientosParaFinalizar as $idReq) {
                $requerimiento = Requerimiento::find($idReq);
                $detalleRequerimiento = DetalleRequerimiento::where('id_requerimiento', $idReq)->get();


                foreach ($detalleRequerimiento as $dr) {

                    if ($dr->estado == 28 || $dr->estado == 5) {
                        if ($dr->id_cc_am_filas > 0) {
                            $ccAmFilas = CcAmFila::find($dr->id_cc_am_filas);
                            $ccAmFilas->comprado = true;
                            $ccAmFilas->save();
                        }
                    } else {
                        if ($dr->id_cc_am_filas > 0) {
                            $ccAmFilas = CcAmFila::find($dr->id_cc_am_filas);
                            $ccAmFilas->comprado = false;
                            $ccAmFilas->save();
                        }
                    }
                }


                if ($requerimiento->id_cc > 0) {

                    $cuadroPresupuesto = CuadroCosto::where('id',$requerimiento->id_cc)->with('oportunidad','oportunidad.entidad','oportunidad.tipoNegocio','oportunidad.responsable','oportunidad.ordenCompraPropia')->first();
                    
                    if ($cuadroPresupuesto !=null && $cuadroPresupuesto->id > 0 && $cuadroPresupuesto->estado_aprobacion != 5) { // cuando el estado aprobacion de cc pendiente por regularizar no se puede actualizar el estado del cc
                        $cc = CuadroCosto::find($requerimiento->id_cc);

                        if ($requerimiento->estado == 28 || $requerimiento->estado == 5) {
                            $cc->estado_aprobacion = 4;// finalizado
                            $cc->save();
                            $ordenPropia= OrdenCompraPropias::where('id_oportunidad',$cc->id_oportunidad)->first();
                            $ordenPropia->id_etapa= 2;// comprado 
                            $ordenPropia->save();

                            $codigoOportunidad.=$cuadroPresupuesto->oportunidad->codigo_oportunidad;
                            $payload[] = [
                                'requerimiento' => $requerimiento,
                                'cuadro_presupuesto' => $cuadroPresupuesto,
                                'orden_compra_propia' => $ordenPropia,
                                'oportunidad' => $cuadroPresupuesto->oportunidad
                            ];
                            $destinatarios[]=$cuadroPresupuesto->oportunidad->responsable->email;


                        } else { // si el requerimiento no esta atentido total o reserva total 
                            // if ($cc->estado_aprobacion == 4) { // verifica si el estado actual del cc es finalizado cuando el requerimiento no esta atentido 
                            //     $cc->estado_aprobacion = 3;
                            //     $cc->save();
                            //     $listaRestablecidos[] = [
                            //         'id_requerimiento' => $requerimiento->id_requerimiento,
                            //         'codigo_requerimiento' => $requerimiento->codigo,
                            //         'id_cuadro_presupuesto' => $cuadroPresupuesto->id,
                            //         'id_oportunidad' => $cuadroPresupuesto->oportunidad->id,
                            //         'codigo_cuadro_presupuesto' => $cuadroPresupuesto->oportunidad->codigo_oportunidad
                            //     ];
                            // }
                        }
                        // preparar correo
 
                        if (count($payload) > 0) {
 
                            $correosOrdenServicioTransformacion = [];
                            if (config('app.debug')) {
                                $correosOrdenServicioTransformacion[] = config('global.correoDebug2');
                                $correoFinalizacionCuadroPresupuesto[]='programador03@okcomputer.com.pe';

                            } else {
                                $idUsuarios = Usuario::getAllIdUsuariosPorRol(25);
                                foreach ($idUsuarios as $id) {
                                    $correosOrdenServicioTransformacion[] = Usuario::find($id)->email;
                                }

                                $correoUsuarioEnSession=Auth::user()->email;
                                $correoFinalizacionCuadroPresupuesto[]=$correoUsuarioEnSession;
                            }

                            $nombreUsuarioEnSession=Auth::user()->nombre_corto;
                            $correoUsuarioEnSession=Auth::user()->email;
                            $correoFinalizacionCuadroPresupuesto[]=$correoUsuarioEnSession;
                            
                            Mail::to(array_unique($correoFinalizacionCuadroPresupuesto))->send(new EmailFinalizacionCuadroPresupuesto($codigoOportunidad,$payload,$nombreUsuarioEnSession));


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
    
                                Mail::to($correosOrdenServicioTransformacion)->send(new EmailOrdenServicioOrdenTransformacion($pl['oportunidad'],$logoEmpresa,$codigoTransformacion));
                            }


                        }
                        // fin preparar correo
                    }
                }
            }
        } catch (Exception $ex) {
            $error = $ex->getMessage();
        }
        return ['lista_finalizados' => $payload, 'lista_restablecidos' => [], 'error' => $error];
    }

    static public function enviar_correo_prueba(){ //BORRAR ESTO ES UNA PRUEBA

        $requerimiento = Requerimiento::find(1368);
        $cuadroPresupuesto = CuadroCosto::where('id',1382)->with('oportunidad','oportunidad.entidad','oportunidad.tipoNegocio','oportunidad.responsable','oportunidad.ordenCompraPropia')->first();
        $ordenPropia= OrdenCompraPropias::where('id_oportunidad',1382)->first();
        $codigoOportunidad='';
        $oportunidad = $cuadroPresupuesto->oportunidad;
        $codigoOportunidad.= $cuadroPresupuesto->oportunidad->codigo_oportunidad;
        // $cc=CuadroCostoView::find(1382);
 
        $payload[]=[
            'requerimiento' => $requerimiento,
            'cuadro_presupuesto' => $cuadroPresupuesto,
            'orden_compra_propia' => $ordenPropia,
            'oportunidad' => $cuadroPresupuesto->oportunidad,
            // 'cc'=>$cc

        ];
        $nombreUsuarioEnSession=Auth::user()->nombre_corto;
        $correoUsuarioEnSession=Auth::user()->email;
        $destinatarios[]=$correoUsuarioEnSession;
        $destinatarios[]=$cuadroPresupuesto->oportunidad->responsable->email;
        // array_unique($destinatarios)
        Mail::to(['programador03@okcomputer.com.pe'])->send(new EmailFinalizacionCuadroPresupuesto($codigoOportunidad,$payload,$nombreUsuarioEnSession,$destinatarios));

 
        $correosOrdenServicio = [];
        if (config('app.debug')) {
            $correosOrdenServicio[] = config('global.correoDebug2');
        } else {
            $idUsuarios = Usuario::getAllIdUsuariosPorRol(25);
            foreach ($idUsuarios as $id) {
                $correosOrdenServicio[] = Usuario::find($id)->email;
            }
        }

        $transformacion =  Transformacion::select('transformacion.codigo', 'cc.id_oportunidad', 'adm_empresa.logo_empresa')
        ->leftjoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'transformacion.id_cc')
        ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'transformacion.id_almacen')
        ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
        ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
        ->where('cc.id', 1382)
        ->first();
        $logoEmpresa=empty($transformacion->logo_empresa)?null:$transformacion->logo_empresa;
        $codigoTransformacion=empty($transformacion->codigo)?null:$transformacion->codigo;


        Mail::to($correosOrdenServicio)->send(new EmailOrdenServicioOrdenTransformacion($oportunidad,$logoEmpresa,$codigoTransformacion));

    }
}
