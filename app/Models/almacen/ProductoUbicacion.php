<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Model;

class ProductoUbicacion extends Model
{
    protected $table = 'almacen.alm_prod_ubi';
    protected $primaryKey = 'id_prod_ubi';
    public $timestamps = false;
}