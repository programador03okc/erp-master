<?php

namespace App\Http\Controllers\Logistica;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Configuracion\SMTPAuthentication;
use Mail;
use Storage;
use File;
use Illuminate\Support\Facades\DB;
use Swift_Transport;
use Swift_Message;
use Swift_Mailer;
use Swift_Attachment;
use Swift_IoException;
use Swift_Preferences;

class NotificacionesController extends Controller 
{

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
            $transport = (new \Swift_SmtpTransport($smtpAddress, $port, $encryption))
                    ->setUsername($yourEmail)
                    ->setPassword($yourPassword);
            $mailer = new Swift_Mailer($transport);
            $message = (new \Swift_Message($payload['titulo']))
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

}