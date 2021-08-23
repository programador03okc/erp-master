<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pais extends Model
{
    protected $table = 'configuracion.sis_pais';
    protected $primaryKey = 'id_pais';
    public $timestamps = false;

    public static function mostrar()
    {
        $data = Pais::select('sis_pais.id_pais', 
        'sis_pais.descripcion',
        'sis_pais.abreviatura',
        'sis_pais.estado'
        )
        ->where('sis_pais.estado', '=', 1)
        ->orderBy('sis_pais.descripcion', 'asc')
        ->get();
        return $data;
    }
}
