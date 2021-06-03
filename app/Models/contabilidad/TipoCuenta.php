<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TipoCuenta extends Model
{
    protected $table = 'contabilidad.adm_tp_cta';
    protected $primaryKey = 'id_tipo_cuenta';
    public $timestamps = false;

    public static function mostrar()
    {
        $data = TipoCuenta::select('adm_tp_cta.id_tipo_cuenta', 'adm_tp_cta.descripcion')
            ->where('adm_tp_cta.estado', '=', 1)
            ->orderBy('adm_tp_cta.descripcion', 'asc')
            ->get();
        return $data;
    }
}