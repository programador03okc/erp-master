<?php

namespace App\Models\Administracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Division extends Model
{
    protected $table = 'administracion.adm_flujo';
    protected $primaryKey = 'id_flujo';
    public $timestamps = false;

    public static function mostrarFlujoAprobacion()
    {
        $roles = Auth::User()->getAllGrupo();
        $mostrarAprobantes=false;
        $numeroDeOrdenSeleccionado=null;
        $operacion = DB::table('administracion.adm_operacion')
        ->select('adm_operacion.*')
        ->where([['adm_operacion.estado', 1],['adm_operacion.id_grupo',$roles[0]['id_grupo']]]) // el usuario pertenece a un solo grupo
        ->first();

        $flujos = DB::table('administracion.adm_flujo')
        ->select('adm_flujo.*')
        ->where([['adm_flujo.estado', 1],['adm_flujo.id_operacion',$operacion->id_operacion]]) // el usuario pertenece a un solo grupo
        ->get();

        $ordenList=[];
        foreach($flujos as $flujo){
            $ordenList[]=$flujo->orden;
        }
        asort($ordenList);

        $contadorRepetidosOrdenList = array_count_values($ordenList);

        foreach($contadorRepetidosOrdenList as $k => $v){
            if($v >1){
                $mostrarAprobantes = true;
                $numeroDeOrdenSeleccionado=$k;
            }
        }

        $flujosAprobante=[];
        if($mostrarAprobantes == true){
            foreach($flujos as $flujo){
                if($flujo->orden == $numeroDeOrdenSeleccionado){
                    $flujosAprobante[]= $flujo;
                }

            }
        }

        return $flujosAprobante;
    }
}
