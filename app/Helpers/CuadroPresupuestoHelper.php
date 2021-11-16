<?php

namespace App\Helpers;

use App\Mail\EmailFinalizacionCuadroPresupuesto;
use App\Mail\EmailOrdenServicioOrdenTransformacion;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Requerimiento;
use App\Models\Comercial\CuadroCosto\CcAmFila;
use App\Models\Comercial\CuadroCosto\CuadroCosto;
use App\Models\Configuracion\Usuario;
use App\Models\mgcp\AcuerdoMarco\OrdenCompraPropia;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CuadroPresupuestoHelper
{

    static public function finalizar($listaRequerimientosParaFinalizar)
    {
        $listaFinalizados = [];
        $listaRestablecidos = [];
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

                    $cuadroPresupuesto = CuadroCosto::find($requerimiento->id_cc)->with('oportunidad')->first();

                    if ($cuadroPresupuesto && $cuadroPresupuesto->id > 0 && $cuadroPresupuesto->estado_aprobacion != 5) { // cuando el estado aprobacion de cc pendiente por regularizar no se puede actualizar el estado del cc
                        $cc = CuadroCosto::find($requerimiento->id_cc);

                        if ($requerimiento->estado == 28 || $requerimiento->estado == 5) {
                            $cc->estado_aprobacion = 4;// finalizado
                            $cc->save();
                            $ordenPropia= OrdenCompraPropia::where('id_oportunidad',$cc->id_oportunidad)->first();
                            $ordenPropia->id_etapa= 2;// comprado 
                            $ordenPropia->save();

                            $listaFinalizados[] = [
                                'id_requerimiento' => $requerimiento->id_requerimiento,
                                'codigo_requerimiento' => $requerimiento->codigo,
                                'id_cuadro_presupuesto' => $cuadroPresupuesto->id,
                                'codigo_cuadro_presupuesto' => $cuadroPresupuesto->oportunidad->codigo_oportunidad
                            ];
                        } else { // si el requerimiento no esta atentido total o reserva total 
                            if ($cc->estado_aprobacion == 4) { // verifica si el estado actual del cc es finalizado cuando el requerimiento no esta atentido 
                                $cc->estado_aprobacion = 3;
                                $cc->save();
                                $listaRestablecidos[] = [
                                    'id_requerimiento' => $requerimiento->id_requerimiento,
                                    'codigo_requerimiento' => $requerimiento->codigo,
                                    'id_cuadro_presupuesto' => $cuadroPresupuesto->id,
                                    'codigo_cuadro_presupuesto' => $cuadroPresupuesto->oportunidad->codigo_oportunidad
                                ];
                            }
                        }
                        // preparar correo
                        if (count($listaFinalizados) > 0) {
                            // $titulo = 'Se finalizo el cuadro de presupuesto' . implode(",",array_column($listaFinalizados, 'codigo_cuadro_presupuesto'));
                            // $mensaje='<ul>';
                            // foreach ($listaFinalizados as $lf) {
                            //     $mensaje .=
                            //     '<li> Código CP: ' . $lf['codigo_cuadro_presupuesto'] . ' correspondiente al requerimiento ' . $lf['codigo_requerimiento'] .  '</li>';
                            // }
                            // $mensaje .='</ul>';
                            // // $destinatarios[] = 'programador03@okcomputer.com.pe'; // implementar
                            // $mensaje .=   '<p> *Este correo es generado de manera automática, por favor no responder.</p> 
                            // <br> Saludos <br> Módulo de Logística <br> SYSTEM AGILE';

                            // $payload = [
                            //     'id_empresa' => 1,
                            //     'email_destinatario' => $destinatarios,
                            //     'titulo' => $titulo,
                            //     'mensaje' => $mensaje
                            // ];

                            // NotificacionHelper::enviarEmail($payload);
                            $correos = [];
                            if (config('app.debug')) {
                                $correos[] = config('global.correoDebug1');
                            } else {
                                $idUsuarios = Usuario::getAllIdUsuariosPorRol(25);
                                foreach ($idUsuarios as $id) {
                                    $correos[] = Usuario::find($id)->email;
                                }
                            }
                            Mail::to(['programador03@okcomputer.com.pe'])->send(new EmailFinalizacionCuadroPresupuesto($listaFinalizados, $listaRestablecidos));
                            Mail::to(['programador03@okcomputer.com.pe'])->send(new EmailOrdenServicioOrdenTransformacion($listaFinalizados, $listaRestablecidos));

                        }
                        // fin preparar correo
                    }
                }
            }
        } catch (Exception $ex) {
            $error = $ex->getMessage();
        }
        return ['lista_finalizados' => $listaFinalizados, 'lista_restablecidos' => $listaRestablecidos, 'error' => $error];
    }

    static public function enviar_correo_prueba(){ //BORRAR ESTO ES UNA PRUEBA

        //Obtencion de archivos en carpeta temporal
        // $archivosHT = TransformacionHelper::descargarArchivos(42);
        //Guardar archivos subidos


        $listaFinalizados[]=[
            'codigo_cuadro_presupuesto'=> "OKC2011021",
            'id_cuadro_presupuesto'=> 3643,
            'codigo_requerimiento'=> "RM-211283",
            'id_requerimiento'=> 1368

        ];
        
        // Mail::to(['programador03@okcomputer.com.pe'])->send(new EmailFinalizacionCuadroPresupuesto($listaFinalizados, []));
        Mail::to(['programador03@okcomputer.com.pe'])->send(new EmailOrdenServicioOrdenTransformacion($listaFinalizados, []));

        // foreach ($archivosHT as $archivo) {
        //     unlink($archivo);
        // }
    }
}
