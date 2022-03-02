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
    

    public static function mostrarDivisionUsuarioNroOrdenUno(){
        $roles = Auth::user()->getAllRol();
        $idRolUsuarioList=[];
        foreach ($roles as $value) {
            $idRolUsuarioList[]=$value->id_rol;
        }

        $flujosUsuario = DB::table('administracion.adm_flujo')
        ->select('adm_flujo.*')
        ->whereIn('adm_flujo.id_rol',$idRolUsuarioList)
        ->where([['adm_flujo.estado', 1],['adm_flujo.orden', 1]]) 
        ->get();
        
        $idOperacionList=[];
        $divisionUsuarioList=[];
        if(count($flujosUsuario)>0){
            foreach ($flujosUsuario as $flujoUsuario) {
                $idOperacionList[]=$flujoUsuario->id_operacion;
            }

            $divisionUsuarioList = DB::table('administracion.adm_operacion')
            ->select('division.id_division', 'division.descripcion as division','adm_operacion.id_operacion')
            ->join('administracion.division','division.id_division', '=', 'adm_operacion.division_id')
            ->whereIn('adm_operacion.id_operacion',$idOperacionList)
            ->where([['adm_operacion.estado', 1],['division.estado',1]])
            ->get();
        }

        return $divisionUsuarioList;

    }
    
    public static function mostrarFlujoAprobacion()
    {
        $grupos = Auth::User()->getAllGrupo();
        $mostrarAprobantes=false;
        $numeroDeOrdenSeleccionado=null;
        $operacion = DB::table('administracion.adm_operacion')
        ->select('adm_operacion.*')
        ->where([['adm_operacion.estado', 1],['adm_operacion.id_grupo',$grupos[0]['id_grupo']]]) // el usuario pertenece a un solo grupo
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

    public static function listaIdRolAprobantesDeDivisonDeUsuario(){
        $grupos = Auth::User()->getAllGrupo();
        $roles = Auth::user()->getAllRol();
        $idGrupoList=[];
        $idRolList=[];
        $idOperacionList=[];

        foreach ($grupos as $grupo) {
            $idGrupoList[] = $grupo->id_grupo;
        }
        foreach ($roles as $rol) {
            $idRolList[] = $rol->id_rol;
        }

        $operaciones = DB::table('administracion.adm_operacion')
        ->select('adm_operacion.*')
        ->where('adm_operacion.estado', 1)
        ->whereIn('adm_operacion.id_grupo',$idGrupoList)
        ->get();

        foreach ($operaciones as $operacion) {
            $idOperacionList[] = $operacion->id_operacion;
        }

        $flujosActivosDeGrupoYRol = DB::table('administracion.adm_flujo')
        ->select('adm_flujo.*')
        ->where('adm_flujo.estado', 1)
        ->whereIn('adm_flujo.id_operacion',$idOperacionList)
        ->whereIn('adm_flujo.id_rol',$idRolList)
        ->get();

        $idRolAprobanteDeDivisionList=[];
        foreach ($flujosActivosDeGrupoYRol as $value) {
            $idRolAprobanteDeDivisionList[]=$value->id_rol;
        }

        return $idRolAprobanteDeDivisionList;
    }
}
