<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TipoRequerimiento extends Model
{
    protected $table = 'almacen.alm_tp_req';
    protected $primaryKey = 'id_tipo_requerimiento';
    public $timestamps = false;

    public static function mostrar()
    {
        $data = TipoRequerimiento::select(
                'alm_tp_req.id_tipo_requerimiento',
                'alm_tp_req.descripcion'
            )
            ->where('alm_tp_req.estado',1)
            ->orderBy('alm_tp_req.id_tipo_requerimiento', 'asc')
            ->get();
        return $data;
    }
}
