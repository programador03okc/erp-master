<?php

namespace App\Models\Cas;

use Illuminate\Database\Eloquent\Model;

class IncidenciaProducto extends Model
{
    protected $table = 'almacen.incidencia_producto';
    public $timestamps = false;
    protected $primaryKey = 'id_incidencia_producto';
}
