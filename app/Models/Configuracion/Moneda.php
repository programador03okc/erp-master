<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Moneda extends Model
{
    protected $table = 'configuracion.sis_moneda';
    protected $primaryKey = 'id_moneda';
    public $timestamps = false;

    public static function mostrar()
    {
        $data =  Moneda::select(
                'sis_moneda.*',
                DB::raw("(CASE WHEN configuracion.sis_moneda.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
                ['sis_moneda.estado', '=', 1]
            ])
            ->orderBy('sis_moneda.id_moneda', 'asc')
            ->get();
        return $data;
    }
}
