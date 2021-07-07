<?php

namespace App\Models\Administracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Area extends Model
{
    protected $table = 'administracion.adm_area';
    protected $primaryKey = 'id_area';
    public $timestamps = false;

    public static function mostrar()
    {
        $data = Area::select(
                'adm_area.*',
                DB::raw("(CASE WHEN administracion.adm_area.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
                ['adm_area.estado', '=', 1]
            ])
            ->orderBy('adm_area.id_area', 'asc')
            ->get();
        return $data;
    }
}
