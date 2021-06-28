<?php

namespace App\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class Operacion extends Model
{
    protected $table = 'administracion.adm_operacion';
    protected $primaryKey = 'id_operacion';
    public $timestamps = false;

    public static function getOperacion($IdTipoDocumento, $idGrupo, $idDivision, $idPrioridad)
    {

        $adm_operacion = Operacion::where([
            ['id_tp_documento', '=', $IdTipoDocumento],
            ['estado', '=', 1]
        ])
        ->when((intval($idGrupo)> 0), function($query)  use ($idGrupo) {
            return $query->whereRaw('adm_operacion.id_grupo = '.$idGrupo);
        })
        ->when((intval($idDivision)> 0), function($query)  use ($idDivision) {
            return $query->whereRaw('adm_operacion.division_id = '.$idDivision);
        })
        ->when((intval($idPrioridad)> 0), function($query)  use ($idPrioridad) {
            return $query->whereRaw('adm_operacion.id_prioridad = '.$idPrioridad);
        })
        ->get();

        if(count($adm_operacion)==0){
            $adm_operacion = Operacion::where([
                ['id_tp_documento', '=', $IdTipoDocumento],
                ['estado', '=', 1]
            ])
            ->when((intval($idGrupo)> 0), function($query)  use ($idGrupo) {
                return $query->whereRaw('adm_operacion.id_grupo = '.$idGrupo);
            })
            ->when((intval($idDivision)> 0), function($query)  use ($idDivision) {
                return $query->whereRaw('adm_operacion.division_id = '.$idDivision);
            })
            ->get();

            if(count($adm_operacion)==0){
                $adm_operacion = Operacion::where([
                    ['id_tp_documento', '=', $IdTipoDocumento],
                    ['estado', '=', 1]
                ])
                ->when((intval($idGrupo)> 0), function($query)  use ($idGrupo) {
                    return $query->whereRaw('adm_operacion.id_grupo = '.$idGrupo);
                })
                ->get();
            }   
        }   

        return $adm_operacion;
    }
}
