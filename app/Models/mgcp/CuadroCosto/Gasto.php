<?php

namespace App\Models\mgcp\CuadroCosto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    // use HasFactory;
    protected $table = 'mgcp_cuadro_costos.gastos';
    public $timestamps = false;

    public static function listar()
    {
        return Gasto::join('mgcp_cuadro_costos.tipos_operacion', 'id_operacion', '=', 'tipos_operacion.id')
            ->join('mgcp_oportunidades.tipos_afectacion', 'id_afectacion', '=', 'tipos_afectacion.id')
            ->orderBy('concepto', 'asc')->select([
                'gastos.id', 'id_afectacion', 'concepto', 'id_operacion', 'tipo_operacion',
                'tipo_afectacion', 'porcentaje', 'desde', 'hasta'
            ])->get();
    }
}
