<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Events\AlertEvent;
use Illuminate\Support\Facades\Auth;

class SocketController extends Controller
{
    public function notification(Request $request){
        $params = [
            'title'     => $request->get('title'),
            'message'   => $request->get('message'),
            'id_area'  => $request->get('id_area'),
            'id_rol'  => $request->get('id_rol'),
            'id_empresa'  => $request->get('id_empresa')
        ];

        event(new AlertEvent($params, 'notification'));
    }
    public function notificaciones_sin_leer(){
        $id_usuario = Auth::user()->id_usuario;

        $notificaciones = DB::table('administracion.notificaciones')
        ->select(
            'notificaciones.*'
            )

        ->where([
            ['notificaciones.id_usuario','=',$id_usuario],
            ['notificaciones.leido','=',false]
        ])
        ->get();
        $cantidad_notificaciones = count($notificaciones);
        if($cantidad_notificaciones > 0) {
            foreach ($notificaciones as $key => $value) {
                $params[] = [
                    'id_usuario' => $value->id_usuario,
                    'mensaje' => $value->mensaje,
                    'fecha' => $value->fecha,
                    'url' => $value->url,
                    'leido' => $value->leido
                ];
            }
    
        }else{
            $params = [];
        }
     

        event(new AlertEvent($params, 'notificaciones_sin_leer'));
    }
}
