<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'almacen.alm_prod';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;
}