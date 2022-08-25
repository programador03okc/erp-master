<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Configuracion\Notificacion;
use App\Models\Configuracion\SMTPAuthentication;
use Carbon\Carbon;
use Swift_Mailer;
use Swift_Message;
use Swift_Preferences;
use Swift_SmtpTransport;
use Debugbar;

class NotificacionHelper
{

    static public function enviarEmail($payload)
    {
        $status = 0;
        $msg = '';
        $ouput = [];
        $smpt_setting = SMTPAuthentication::getAuthentication($payload['id_empresa']);
        if ($smpt_setting['status'] == 'success') {
            $smtpAddress = $smpt_setting['smtp_server'];
            $port = $smpt_setting['port'];
            $encryption = $smpt_setting['encryption'];
            $yourEmail = $smpt_setting['email'];
            $yourPassword = $smpt_setting['password'];

            Swift_Preferences::getInstance()->setCacheType('null');
            $transport = (new Swift_SmtpTransport($smtpAddress, $port, $encryption))
                ->setUsername($yourEmail)
                ->setPassword($yourPassword);
            $mailer = new Swift_Mailer($transport);
            $message = (new Swift_Message($payload['titulo']))
                ->setFrom([$yourEmail => 'SYSTEM AGILE'])
                ->setTo($payload['email_destinatario'])
                ->addPart($payload['mensaje'], 'text/html');
            if ($mailer->send($message)) {
                $msg = "Se envio un correo de notificación";
                $status = 200;
                $ouput = ['mensaje' => $msg, 'status' => $status];
                return $ouput;
            } else {
                $msg = "Algo salió mal al tratar de notificar por email";
                $ouput = ['mensaje' => $msg, 'status' => $status];
                return $ouput;
            }
        } else {
            $msg = 'Error, no existe configuración de correo para la empresa seleccionada';
        }
    }

    static public function notificacionFinalizacionCuadro($oportunidades, $usuarios, $payload)
    {
            //  Debugbar::info($usuarios);
            try {

            if(count($usuarios)>0){

                foreach ($usuarios as $idUsuario) {
                    // $mensajeNotificacion = 'Se ha finalizado eL CDP ' . (gettype($oportunidades) == 'string' ? $oportunidades : (implode(",", $oportunidades)));
                    // $notificacion = new Notificacion();
                    // $notificacion->id_usuario = $idUsuario;
                    // $notificacion->mensaje = $mensajeNotificacion ?? null;
                    // $notificacion->fecha = new Carbon();
                    // $notificacion->url = '';
                    // $notificacion->leido = 0;
                    // $notificacion->save();
                    $mensajeNotificacion='';
                    foreach($payload as $data){
                        $mensajeNotificacion .= 'Se ha finalizado el CDP '.$data['cuadro_presupuesto']->oportunidad->codigo_oportunidad.
                        ', Responsable: '.$data['cuadro_presupuesto']->oportunidad->responsable->name.
                        ', Fecha limite: '.$data['cuadro_presupuesto']->oportunidad->fecha_limite.
                        ', Cliente: '.$data['cuadro_presupuesto']->oportunidad->fecha_limite;
                    }
                    $notificacion = new Notificacion();
                    $notificacion->id_usuario = $idUsuario;
                    $notificacion->mensaje = $mensajeNotificacion ?? null;
                    $notificacion->fecha = new Carbon();
                    $notificacion->url = '';
                    $notificacion->leido = 0;
                    $notificacion->save();
                }

                return ['estado' => 'success', 'mensaje' => 'success'];
            }else{
                return ['estado' => 'warning', 'mensaje' => 'Hubo un problema para obtener los usuarios que se realizará la notificación, la variable $usuarios esta vacia'];

            }

        } catch (Exception $ex) {
            return ['estado' => 'error', 'mensaje' => $ex->getMessage()];
        }
    }

    static public function notificacionOrdenServicioTransformacion($codigoTransformacion, $usuarios, $oportunidad)
    {
            try {

                $mensajeNotificacion='';
                $orden = $oportunidad->ordenCompraPropia;
                $mensajeNotificacion = $codigoTransformacion != null ?$codigoTransformacion: 'O. SERVICIO';
                if ($orden == null) {
                    $mensajeNotificacion .= 'SIN O/C ';
                } else {
                    $mensajeNotificacion.= ', '.$orden->nro_orden;
                    $mensajeNotificacion.= ', '.$orden->entidad->nombre;
                }
                $mensajeNotificacion .= ', '.$oportunidad->codigo_oportunidad;
                if ($orden != null) {
                    $mensajeNotificacion .= ', '.$orden->empresa->abreviado;
                }


            if(count($usuarios)>0){

                foreach ($usuarios as $idUsuario) {
                    $notificacion = new Notificacion();
                    $notificacion->id_usuario = $idUsuario;
                    $notificacion->mensaje = $mensajeNotificacion ?? null;
                    $notificacion->fecha = new Carbon();
                    $notificacion->url = 'https://erp.okccloud.com/logistica/gestion-logistica/compras/ordenes/elaborar/imprimir_orden_servicio_o_transformacion/'.$oportunidad->id;
                    $notificacion->leido = 0;
                    $notificacion->save();
                }

                return ['estado' => 'success', 'mensaje' => 'success'];
            }else{
                return ['estado' => 'warning', 'mensaje' => 'Hubo un problema para obtener los usuarios que se realizará la notificación, la variable $usuarios esta vacia'];
            }

        } catch (Exception $ex) {
            return ['estado' => 'error', 'mensaje' => $ex->getMessage()];
        }
    }

    static public function notificacionAnularOrden($orden, $usuarios)
    {
            try {


            $codigoRequerimientoList = array();
            $responsableRequerimientoList = array();
            $codigoOportunidadList = array();
            foreach(($orden->requerimientos) as $r) {
                $codigoRequerimientoList[] = $r['codigo'];
                $responsableRequerimientoList[] = $r['nombre_corto'];
            }
            foreach(($orden->oportunidad) as $o) {
                $codigoOportunidadList[] = $o['codigo_oportunidad'];
            }

            if(count($usuarios)>0){
                $mensajeNotificacion='';
                foreach ($usuarios as $idUsuario) {

                    $mensajeNotificacion='Se anulo la orden '.$orden->codigo.' - '.$orden->sede->descripcion.
                    ', sustento: '.$orden->sustento_anulacion.
                    ', Requerimiento: '.(implode(",", $codigoRequerimientoList)).
                    ', responsable: '.(implode(",", $responsableRequerimientoList)).
                    ', CDP: '. implode(",", $codigoOportunidadList).
                    ', fecha creación: '. $orden->fecha.
                    ', fecha anulación: '. $orden->fecha_anulacion;

                    $notificacion = new Notificacion();
                    $notificacion->id_usuario = $idUsuario;
                    $notificacion->mensaje = $mensajeNotificacion ?? null;
                    $notificacion->fecha = new Carbon();
                    $notificacion->url = '';
                    $notificacion->leido = 0;
                    $notificacion->save();
                }

                return ['estado' => 'success', 'mensaje' => 'success'];
            }else{
                return ['estado' => 'warning', 'mensaje' => 'Hubo un problema para obtener los usuarios que se realizará la notificación, la variable $usuarios esta vacia'];

            }

        } catch (Exception $ex) {
            return ['estado' => 'error', 'mensaje' => $ex->getMessage()];
        }
    }

    static public function notificarContactoDespacho($mensaje,$usuarios){
        try {

            if(count($usuarios)>0){
                foreach ($usuarios as $idUsuario) {


                    $notificacion = new Notificacion();
                    $notificacion->id_usuario = $idUsuario;
                    $notificacion->mensaje = $mensaje ?? null;
                    $notificacion->fecha = new Carbon();
                    $notificacion->url = '';
                    $notificacion->leido = 0;
                    $notificacion->save();
                }

                return ['estado' => 'success', 'mensaje' => 'success'];
            }else{
                return ['estado' => 'warning', 'mensaje' => 'Hubo un problema para obtener los usuarios que se realizará la notificación, la variable $usuarios esta vacia'];

            }

        } catch (Exception $ex) {
            return ['estado' => 'error', 'mensaje' => $ex->getMessage()];
        }
    }

    static public function notificacionRequerimiento($idUsuarioDestinatario,$mensajeNotificacion){
        try {



            if(count($idUsuarioDestinatario)>0){
                foreach ($idUsuarioDestinatario as $idUsuario) {


                    $notificacion = new Notificacion();
                    $notificacion->id_usuario = $idUsuario;
                    $notificacion->mensaje = $mensajeNotificacion;
                    $notificacion->fecha = new Carbon();
                    $notificacion->url = '';
                    $notificacion->leido = 0;
                    $notificacion->save();
                }

                return ['estado' => 'success', 'mensaje' => 'success'];
            }else{
                return ['estado' => 'warning', 'mensaje' => 'Hubo un problema para obtener los usuarios que se realizará la notificación, la variable $usuarios esta vacia'];

            }

        } catch (Exception $ex) {
            return ['estado' => 'error', 'mensaje' => $ex->getMessage()];
        }
    }

    static public function notificacionOrdenDespacho($idUsuarioDestinatario,$mensajeNotificacion,$oportunidad,$requerimiento,$codigo, $fecha_registro){
        try {

            $mensajeNotificacion = 'Se ha generado la '.$codigo;
            if ($oportunidad !== null) {
                $orden = $oportunidad->ordenCompraPropia;
                $mensajeNotificacion .= 'O. SERVICIO';
                if ($orden == null) {
                    $mensajeNotificacion .= ' SIN O/C';
                } else {
                    $mensajeNotificacion .= ', '.$orden->entidad->nombre;
                    $mensajeNotificacion .= ', '.$orden->nro_orden;
                    // $mensajeNotificacion .= ', '.$orden->entidad->nombre;
                }
                $mensajeNotificacion .= ', '.$oportunidad->codigo_oportunidad;
                if ($orden != null) {
                    $mensajeNotificacion .= ', '.$orden->empresa->abreviado;
                }
            } else if ($requerimiento !== null) {
                $mensajeNotificacion .= ', DESPACHO DEL ' . $requerimiento->codigo . ' - ' . $requerimiento->concepto;
            }

            if(count($idUsuarioDestinatario)>0){
                foreach ($idUsuarioDestinatario as $idUsuario) {


                    $notificacion = new Notificacion();
                    $notificacion->id_usuario = $idUsuario;
                    $notificacion->mensaje = $mensajeNotificacion.', '.$fecha_registro;
                    $notificacion->fecha = new Carbon();
                    $notificacion->url = '';
                    $notificacion->leido = 0;
                    $notificacion->save();
                }

                return ['estado' => 'success', 'mensaje' => 'success'];
            }else{
                return ['estado' => 'warning', 'mensaje' => 'Hubo un problema para obtener los usuarios que se realizará la notificación, la variable $usuarios esta vacia'];

            }

        } catch (Exception $ex) {
            return ['estado' => 'error', 'mensaje' => $ex->getMessage()];
        }
    }
}
