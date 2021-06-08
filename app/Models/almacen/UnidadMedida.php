<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UnidadMedida extends Model
{
    protected $table = 'almacen.alm_und_medida';
    protected $primaryKey = 'id_unidad_medida';
    public $timestamps = false;

    public static function mostrar()
    {
        $data = UnidadMedida::select(
                'alm_und_medida.id_unidad_medida',
                'alm_und_medida.descripcion',
                'alm_und_medida.abreviatura',
                'alm_und_medida.estado',
                DB::raw("(CASE WHEN almacen.alm_und_medida.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
                ['alm_und_medida.estado', '=', 1]
            ])
            ->orderBy('alm_und_medida.descripcion', 'asc')
            ->get();
        return $data;
    }
}
