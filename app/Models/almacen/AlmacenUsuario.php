<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class AlmacenUsuario extends Model
{
    protected $table = 'almacen.alm_almacen_usuario';
    protected $primaryKey = 'id_almacen_usuario';
    public $timestamps = false;
}
