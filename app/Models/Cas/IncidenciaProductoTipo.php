<?php

namespace App\Models\Cas;

use Illuminate\Database\Eloquent\Model;

class IncidenciaProductoTipo extends Model
{
    protected $table = 'cas.incidencia_producto_tipo';
    public $timestamps = false;
    protected $primaryKey = 'id_tipo';
}
