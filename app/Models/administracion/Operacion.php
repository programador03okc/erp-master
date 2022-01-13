<?php

namespace App\Models\Administracion;
use Debugbar;

use Illuminate\Database\Eloquent\Model;

class Operacion extends Model
{
    protected $table = 'administracion.adm_operacion';
    protected $primaryKey = 'id_operacion';
    public $timestamps = false;

    public static function getOperacion($IdTipoDocumento,$idTipoRequerimientoReq, $idGrupo, $idDivision, $idPrioridad)
    {

        $adm_operacion = Operacion::where([
            ['id_tp_documento', '=', $IdTipoDocumento],
            ['estado', '=', 1]
        ])
        ->when((intval($idTipoRequerimientoReq)> 0), function($query)  use ($idTipoRequerimientoReq) {
            return $query->whereRaw('adm_operacion.tipo_requerimiento_id = '.$idTipoRequerimientoReq);
        })
        ->when((intval($idGrupo)> 0), function($query)  use ($idGrupo) {
            return $query->whereRaw('adm_operacion.id_grupo = '.$idGrupo);
        })
        ->when((intval($idDivision)> 0), function($query)  use ($idDivision) {
            return $query->whereRaw('adm_operacion.division_id = '.$idDivision);
        })
        ->when((intval($idPrioridad)> 0), function($query)  use ($idPrioridad) {
            return $query->whereRaw('adm_operacion.prioridad_id = '.$idPrioridad);
        })
        ->get();
        // Debugbar::info(count($adm_operacion));
        // Debugbar::info($adm_operacion);

        if(count($adm_operacion)==0){
            $adm_operacion = Operacion::where([
                ['id_tp_documento', '=', $IdTipoDocumento],
                ['estado', '=', 1]
            ])
            ->when((intval($idPrioridad)>0), function($query)  use ($idPrioridad) {
                return $query->whereRaw('adm_operacion.prioridad_id isNULL');
            })
            ->when((intval($idTipoRequerimientoReq)> 0), function($query)  use ($idTipoRequerimientoReq) {
                return $query->whereRaw('adm_operacion.tipo_requerimiento_id = '.$idTipoRequerimientoReq);
            })
            ->when((intval($idGrupo)> 0), function($query)  use ($idGrupo) {
                return $query->whereRaw('adm_operacion.id_grupo = '.$idGrupo);
            })
            ->when((intval($idDivision)> 0), function($query)  use ($idDivision) {
                return $query->whereRaw('adm_operacion.division_id = '.$idDivision);
            })
    
            ->get();
        }   
        // Debugbar::info(count($adm_operacion));

        // Debugbar::info($adm_operacion);

        if(count($adm_operacion)==0){
            $adm_operacion = Operacion::where([
                ['id_tp_documento', '=', $IdTipoDocumento],
                ['estado', '=', 1]
            ])
            ->when((intval($idTipoRequerimientoReq)>0), function($query)  use ($idTipoRequerimientoReq) {
                return $query->whereRaw('adm_operacion.tipo_requerimiento_id isNULL');
            })
            ->when((intval($idGrupo)> 0), function($query)  use ($idGrupo) {
                return $query->whereRaw('adm_operacion.id_grupo = '.$idGrupo);
            })
            ->when((intval($idDivision)> 0), function($query)  use ($idDivision) {
                return $query->whereRaw('adm_operacion.division_id = '.$idDivision);
            })
    
            ->get();

        }
        // if(count($adm_operacion)==0){
        //     $adm_operacion = Operacion::where([
        //         ['id_tp_documento', '=', $IdTipoDocumento],
        //         ['estado', '=', 1]
        //     ])
        //     ->when((intval($idGrupo)> 0), function($query)  use ($idGrupo) {
        //         return $query->whereRaw('adm_operacion.id_grupo = '.$idGrupo);
        //     })
        //     ->when((intval($idDivision)> 0), function($query)  use ($idDivision) {
        //         return $query->whereRaw('adm_operacion.division_id = '.$idDivision);
        //     })
        //     // ->when((intval($idTipoRequerimientoReq)> 0), function($query) {
        //     //     return $query->whereRaw('adm_operacion.tipo_requerimiento_id isNUll');
        //     // })
        //     // ->when((intval($idPrioridad)> 0), function($query) {
        //     //     return $query->whereRaw('adm_operacion.prioridad_id isNULL');
        //     // })
        //     ->get();
        // }   


        return count($adm_operacion)==0 ?[]:$adm_operacion;
    }
}
