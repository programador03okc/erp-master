<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class RequerimientoPagoTipoDestinatario extends Model
{
    protected $table = 'tesoreria.requerimiento_pago_tipo_destinatario';
    protected $primaryKey = 'id_requerimiento_pago_tipo_destinatario';
    public $timestamps = false;


    public static function mostrar()
    {
        $data =RequerimientoPagoTipoDestinatario::where('requerimiento_pago_tipo_destinatario.id_estado', '=', 1)
            ->orderBy('requerimiento_pago_tipo_destinatario.descripcion', 'desc')
            ->get();
        return $data;
    }

}