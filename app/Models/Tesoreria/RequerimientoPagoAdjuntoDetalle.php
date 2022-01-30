<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;


class RequerimientoPagoAdjuntoDetalle extends Model
{
    protected $table = 'tesoreria.requerimiento_pago_detalle_adjunto';
    protected $primaryKey = 'id_requerimiento_pago_detalle_adjunto';
    public $timestamps = false;

 
}
