<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SMTPAuthentication extends Model
{
    protected $table = 'configuracion.smtp_authentication';
    protected $primaryKey = 'id_smtp_authentication';
    public $timestamps = false;

    public static function getAuthentication($idEmpresa)
    {
        $smtp_server='';
        $port='';
        $emial='';
        $password='';
        $encryption='';

        $statusOption=['success','fail'];
        $status='';
        $data = SMTPAuthentication::select('smtp_authentication.*')
        ->where([
                ['smtp_authentication.estado', '=', 1],
                ['smtp_authentication.id_empresa','=',$idEmpresa]
                ])
        ->get();
        
        if($data->count() > 0){
            $smtp_server = $data->first()->smtp_server;
            $port = $data->first()->port;
            $email = $data->first()->email;
            $password = $data->first()->password;
            $encryption = $data->first()->encryption;

            $status=$statusOption[0];
        }else{
            $status=$statusOption[1];
        }

        return [
            'smtp_server'=>$smtp_server,
            'port'=>$port,
            'email'=>$email,
            'password'=>$password,
            'encryption'=>$encryption,
            'status'=>$status
        ];
    }
}
