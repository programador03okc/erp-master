<?php

namespace App\Models\Administracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Periodo extends Model
{
    protected $table = 'administracion.adm_periodo';
    protected $primaryKey = 'id_periodo';
    public $timestamps = false;

    public static function mostrar()
    {
        $data = Periodo::select(
                'adm_periodo.*'
            )
            ->where([
                ['adm_periodo.estado', '=', 1]
            ])
            ->orderBy('adm_periodo.id_periodo', 'desc')
            ->get();
        return $data;
    }
}
