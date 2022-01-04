<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class AdjuntoRequerimientoPago extends Model
{
    protected $table = 'tesoreria.adjunto_requerimiento_pago';
    protected $primaryKey = 'id_adjunto';
    public $timestamps = false;

    public function categoriaAdjunto()
    {
        return $this->hasOne('App\Models\Tesoreria\CategoriaAdjunto', 'id_categoria_adjunto', 'categoria_adjunto_id');
    }
}
