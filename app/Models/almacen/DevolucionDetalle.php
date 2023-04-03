<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class DevolucionDetalle extends Model
{
    protected $table = 'cas.devolucion_detalle';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;

}
