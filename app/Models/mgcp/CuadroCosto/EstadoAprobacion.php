<?php

namespace App\Models\mgcp\CuadroCosto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoAprobacion extends Model
{
    use HasFactory;
    protected $table = 'mgcp_cuadro_costos.estados_aprobacion';
    public $timestamps = false;
}
