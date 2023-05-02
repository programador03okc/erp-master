<?php

namespace App\Http\Controllers;

use App\Helpers\ConfiguracionHelper;
use App\Models\administracion\AdmGrupo;
use App\Models\Administracion\Empresa;
use App\Models\Administracion\Sede;
use App\Models\Configuracion\Usuario;
use App\Models\Contabilidad\Contribuyente;
use App\Models\Presupuestos\Grupo;
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
                "'aux_id'=>".$value->id_usuario.",".
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
                "'aux_id'=>".$value->id_usuario.",".
                "'created_at'=> date('Y-m-d H:i:s'),".
                "'updated_at'=> date('Y-m-d H:i:s'),".
                "'deleted_at'=> date('Y-m-d H:i:s')".
            "]);";
        }

        return response()->json([$usuarioSeeder],200);
    }
    public function empresas()
    {
        $empresas = Empresa::orderBy('id_empresa','ASC')->get();
        $seeder="";
        foreach ($empresas as $key => $value) {
            $contribuyente = Contribuyente::where('id_contribuyente',$value->id_contribuyente)->first();
            $value->descripcion = $contribuyente->razon_social;


            $correlativo = ConfiguracionHelper::leftZero(3, ($key+1));
            $seeder.="DB::table('administracion.empresas')->insert([".
                "'codigo'=>'".$correlativo."',".
                "'descripcion'=>'".$value->descripcion."',".
                "'abreviatura'=>'".$value->codigo."',".
                "'aux_id_contribuyente'=>'".$value->id_contribuyente."',".
                "'created_at'=>date('Y-m-d H:i:s'),".
                "'updated_at'=>date('Y-m-d H:i:s')".
            "]);";
        }
        return response()->json([$seeder],200);
    }
    public function sedes()
    {
        $seeder="";
        $sedes = Sede::orderBy('id_sede','ASC')->get();
        foreach ($sedes as $key => $value) {
            $array = explode('-',$value->descripcion);

            $correlativo = ConfiguracionHelper::leftZero(3, ($key+1));
            $codigo = $array[1].'-'.$correlativo ;
            $seeder.="DB::table('administracion.sedes')->insert([".
                "'codigo'=>'".$codigo."',".
                "'descripcion'=>'".$value->descripcion."',".
                "'direccion'=>'".$value->direccion."',".
                "'empresa_id'=>".$value->id_empresa.",".
                "'aux_id'=>".$value->id_sede.",".
                "'created_at'=>date('Y-m-d H:i:s'),".
                "'updated_at'=>date('Y-m-d H:i:s')".
            "]);";
        }

        return response()->json([$seeder],200);
    }
    public function grupos()
    {
        $seeder="";
        $grupos = AdmGrupo::orderBy('id_grupo','ASC')->get();
        foreach ($grupos as $key => $value) {
            $seeder.="DB::table('administracion.grupos')->insert([".
                "'codigo'=>'".$value->cod_grupo."',".
                "'descripcion'=>'".$value->descripcion."',".
                "'sede_id'=>".$value->id_sede.",".
                "'aux_id'=>".$value->id_grupo.",".
                "'created_at'=>date('Y-m-d H:i:s'),".
                "'updated_at'=>date('Y-m-d H:i:s')".
            "]);";
        }
        return response()->json([$seeder],200);
    }
}
