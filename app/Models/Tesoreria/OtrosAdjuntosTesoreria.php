<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;


class OtrosAdjuntosTesoreria extends Model
{
    protected $table = 'tesoreria.otros_adjuntos';
    protected $primaryKey = 'id_otros_adjuntos';
    public $timestamps = false;

    public function categoriaAdjunto()
    {
        return $this->belongsTo('App\Models\Tesoreria\RequerimientoPagoCategoriaAdjunto', 'id_categoria_adjunto','id_requerimiento_pago_categoria_adjunto');
    }
}
