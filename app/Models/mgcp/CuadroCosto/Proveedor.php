<?php

namespace App\Models\mgcp\CuadroCosto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    // use HasFactory;
    protected $table = 'mgcp_cuadro_costos.proveedores';
    public $timestamps = false;
}
