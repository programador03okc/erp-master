<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class RequerimientoPagoCategoriaAdjunto extends Model
{
    protected $table = 'tesoreria.requerimiento_pago_categoria_adjunto';
    protected $primaryKey = 'id_requerimiento_pago_categoria_adjunto';
    public $timestamps = false;

}
