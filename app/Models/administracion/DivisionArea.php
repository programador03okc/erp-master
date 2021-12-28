<?php

namespace App\Models\Administracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DivisionArea extends Model
{
    protected $table = 'administracion.division';
    protected $primaryKey = 'id_division';
    public $timestamps = false;

    public static function listarDivisionPorGrupo($idGrupo){
        $data = DivisionArea::select(
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