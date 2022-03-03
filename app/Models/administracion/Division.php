<?php

namespace App\Models\Administracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Division extends Model
{
    protected $table = 'administracion.division';
    protected $primaryKey = 'id_division';
    public $timestamps = false;


    public static function mostrar()
    {
        $grupos = Auth::user()->getAllGrupo();
        $idGrupoList=[];

        foreach ($grupos as $value) {
            $idGrupoList[]=$value->id_grupo;
        }

        $divisiones = DB::table('administracion.division')
        ->select('division.*')
        ->whereIn('division.grupo_id', $idGrupoList)
        ->where('division.estado',1)
        ->get();

        return $divisiones;


    }
    
    public static function listarDivisionPorGrupo($idGrupo){
        $data = Division::select(
            'division.*',
            'adm_grupo.descripcion as nombre_grupo'
        )
         ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'division.grupo_id')

        ->where([['division.grupo_id','=',$idGrupo],['division.estado',1]])
        ->orderBy('division.descripcion', 'asc')
        ->get();
    return $data;
    }

}