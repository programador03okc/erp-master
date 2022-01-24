<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class RequerimientoPagoAdjunto extends Model
{
    protected $table = 'tesoreria.requerimiento_pago_adjunto';
    protected $primaryKey = 'id_requerimiento_pago_adjunto';
    public $timestamps = false;

    public function categoriaAdjunto()
    {
        return $this->hasOne('App\Models\Tesoreria\CategoriaAdjunto', 'id_categoria_adjunto', 'categoria_adjunto_id');
    }
}
