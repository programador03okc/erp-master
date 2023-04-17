<?php

namespace App\Http\Controllers;

use App\Models\Configuracion\Usuario;
use Illuminate\Http\Request;

class ScriptController extends Controller
{
    //
    public function usuarios()
    {
        $usuarios = Usuario::get();

        $usuarioSeeder="";
        foreach ($usuarios as $key => $value) {
            $delete = ($value->estado===7?date('Y-m-d H:i:s'):null);
            $usuarioSeeder.="DB::table('configuracion.usuarios')->insert([".
                "'usuario'=> '".strtoupper($value->usuario)."',".
                "'correo'=> '".strtoupper($value->email)."',".
                "'password'=> Hash::make('Inicio01'),".
                "'nombre_largo'=> '".strtoupper($value->nombre_corto)."',".
                "'nombre_corto'=> '".strtoupper($value->nombre_corto)."',".
                "'fecha_renovacion'=> date('Y-m-d', strtotime(date('Y-m-d').'+1 month')),".
                "'flag_renovacion'=> true,".
                "'remember_token'=> Str::random(10),".
                "'created_at'=> date('Y-m-d H:i:s'),".
                "'updated_at'=> date('Y-m-d H:i:s'),";
                if ($value->estado===7) {
                    $usuarioSeeder.="'deleted_at'=> date('Y-m-d H:i:s')";
                }

                $usuarioSeeder.="]);";
        }
        $usuarios_eliminados = Usuario::onlyTrashed()->get();
        foreach ($usuarios_eliminados as $key => $value) {

            $usuarioSeeder.="DB::table('configuracion.usuarios')->insert([".
                "'usuario'=> '".strtoupper($value->usuario)."',".
                "'correo'=> '".strtoupper($value->email)."',".
                "'password'=> Hash::make('Inicio01'),".
                "'nombre_largo'=> '".strtoupper($value->nombre_corto)."',".
                "'nombre_corto'=> '".strtoupper($value->nombre_corto)."',".
                "'fecha_renovacion'=> date('Y-m-d', strtotime(date('Y-m-d').'+1 month')),".
                "'flag_renovacion'=> true,".
                "'remember_token'=> Str::random(10),".
                "'created_at'=> date('Y-m-d H:i:s'),".
                "'updated_at'=> date('Y-m-d H:i:s'),".
                "'deleted_at'=> date('Y-m-d H:i:s')".
            "]);";
        }

        return response()->json(["eliminados"=>Usuario::onlyTrashed()->count()],200);
    }
}
