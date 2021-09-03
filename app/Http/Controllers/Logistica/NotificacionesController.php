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

   

}