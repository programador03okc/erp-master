<?php

namespace App\Models\Administracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Prioridad extends Model
{
    protected $table = 'administracion.adm_prioridad';
    protected $primaryKey = 'id_prioridad';
    public $timestamps = false;

    public static function mostrar()
    {
        $data = Prioridad::select(
                'adm_prioridad.id_prioridad',
                'adm_prioridad.descripcion'
            )
            ->where([
                ['adm_prioridad.estado', '=', 1]
            ])
            ->orderBy('adm_prioridad.id_prioridad', 'asc')
            ->get();
        return $data;
    }
}
