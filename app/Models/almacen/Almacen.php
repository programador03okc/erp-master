<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $table = 'almacen.alm_almacen';
    protected $primaryKey = 'id_almacen';
    public $timestamps = false;
}
