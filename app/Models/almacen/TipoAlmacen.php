<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Model;

class TipoAlmacen extends Model
{
    protected $table = 'almacen.alm_tp_almacen';
    protected $primaryKey = 'id_tipo_almacen';
    public $timestamps = false;


}
