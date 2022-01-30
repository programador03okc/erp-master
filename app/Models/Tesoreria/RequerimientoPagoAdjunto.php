<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;


class RequerimientoPagoAdjunto extends Model
{
    protected $table = 'tesoreria.requerimiento_pago_adjunto';
    protected $primaryKey = 'id_requerimiento_pago_adjunto';
    public $timestamps = false;

    public function categoriaAdjunto()
    {
        return $this->belongsTo('App\Models\Tesoreria\RequerimientoPagoCategoriaAdjunto', 'id_categoria_adjunto','id_requerimiento_pago_categoria_adjunto');
    }
}
