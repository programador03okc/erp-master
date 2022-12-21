<?php

namespace App\Http\Controllers;

use App\Mail\RecuperarClaveMailable;
use App\Models\Configuracion\SisUsua;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RecuperarClaveController extends Controller
{
    //
    public function recuperarClave()
    {
        return view('recuperar_clave');
    }
    public function enviarCorreo(Request $request)
    {
        $usuarios = SisUsua::where('email',$request->email)->where('usuario',$request->usuario)->where('estado',1)->where('deleted_at',null)->first();
        if (!$usuarios) {
            return response()->json([
                "success"=>false,
                "status"=>404,
                "message"=>"Usuario no encontrado"
            ]);
        }else{
            $data=[];
            $codigo = rand(1,9).''.rand(1,9).''.rand(1,9).''.rand(1,9);
            $usuarios = SisUsua::find($usuarios->id_usuario);
            $usuarios->codigo=(int) $codigo;
            $usuarios->save();
            $data['codigo']=$codigo;
            Mail::to($request->email)->send(new RecuperarClaveMailable(json_encode($data)));

            return response()->json([
                "success"=>true,
                "status"=>200,
                "message"=>"Correo enviado con éxito"
            ]);
        }


    }
    public function ingresarNuevaClave()
    {
        return view('cambio_clave');
    }
}
