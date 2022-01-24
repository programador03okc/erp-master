<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class RequerimientoPagoTipo extends Model
{
    protected $table = 'tesoreria.requerimiento_pago_tipo';
    protected $primaryKey = 'id_requerimiento_pago_tipo';
    public $timestamps = false;

    public static function mostrar()
    {
        $data = RequerimientoPagoTipo::join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'requerimiento_pago_tipo.id_estado')
        ->where('requerimiento_pago_tipo.id_estado', '=', 1)
        ->orderBy('requerimiento_pago_tipo.descripcion', 'asc')
        ->get();
        return $data;
    }
}
