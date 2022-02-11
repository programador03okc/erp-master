<?php

namespace App\Models\almacen\Catalogo;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'almacen.alm_tp_prod';
    public $timestamps = false;
    protected $primaryKey = 'id_tipo_producto';
}
