<?php

namespace App\Models\mgcp\CuadroCosto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AprobadorDos extends Model
{
    use HasFactory;
    protected $table = 'mgcp_cuadro_costos.aprobadores_tipo_dos';
    public $timestamps = false;
}
