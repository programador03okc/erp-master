<?php

namespace App\Models\Logistica;

use Illuminate\Database\Eloquent\Model;

class EstadoProveedor extends Model
{
    protected $table = 'logistica.estado_proveedor';
    protected $primaryKey = 'id_estado';
    public $timestamps = false;

}