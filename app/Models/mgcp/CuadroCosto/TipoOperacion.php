<?php

namespace App\Models\mgcp\CuadroCosto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoOperacion extends Model
{
    use HasFactory;
    protected $table = 'mgcp_cuadro_costos.tipos_operacion';
    public $timestamps = false;
}
