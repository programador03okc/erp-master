<?php

namespace App\Helpers;

use App\Models\Configuracion\Notificacion;
use App\Models\Configuracion\SMTPAuthentication;
use Carbon\Carbon;
use Swift_Mailer;
use Swift_Message;
use Swift_Preferences;
use Swift_SmtpTransport;

class NotificacionHelper{

    static public function enviarEmail($payload){
        $status=0;
        $msg='';
        $ouput=[];
        $smpt_setting = SMTPAuthentication::getAuthentication($payload['id_empresa']);
        if($smpt_setting['status'] =='success'){
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
            ->addPart($payload['mensaje'],'text/html');
            if($mailer->send($message)){            
                $msg = "Se envio un correo de notificación";
                $status = 200;
                $ouput=['mensaje'=>$msg,'status'=>$status];
                return $ouput;
            }else{
                $msg= "Algo salió mal al tratar de notificar por email";
                $ouput=['mensaje'=>$msg,'status'=>$status];
                return $ouput;
    
            }
        }else{ 
            $msg= 'Error, no existe configuración de correo para la empresa seleccionada';
        }
    }

    static public function notificacionFinalizacionCuadro($oportunidades, $usuarios, $data)
    {
        $idUsuarios = [];
        $mensajeNotificacion = 'Se ha finalizado eL CDP <strong>'. implode(",", $oportunidades).'</strong>';

        // foreach ($usuarios as $clave => $usuario) {
        //     if (!in_array($idUsuarios, $usuario)) {
        //         array_push($idUsuarios, $usuario);
    
                // foreach ($data as $lista) {
                //     $mensajeNotificacion .= '<li>Oportunidad : '.$lista['cuadro_presupuesto']->oportunidad->oportunidad.'</li>
                //     <li>Responsable : '.$lista['cuadro_presupuesto']->oportunidad->responsable->name.'</li>
                //     <li>Fecha Limite : '.$lista['cuadro_presupuesto']->oportunidad->fecha_limite.'</li>
                //     <li>Cliente : '.$lista['cuadro_presupuesto']->oportunidad->entidad->nombre.'</li>
                //     <li>Tipo de negocio : '.$lista['cuadro_presupuesto']->oportunidad->tipoNegocio->tipo.'</li>
                //     <br>';
                // }
    
                $notificacion = new Notificacion();
                    $notificacion->id_usuario = $usuarios;
                    $notificacion->mensaje = $mensajeNotificacion;
                    $notificacion->fecha = new Carbon();
                    $notificacion->url = '';
                    $notificacion->leido = 0;
                $notificacion->save();
            }
    //     }
    // }
}