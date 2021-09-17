<?php

namespace App\Models\mgcp\CuadroCosto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CondicionCredito extends Model
{
    use HasFactory;
    protected $table = 'mgcp_cuadro_costos.condiciones_credito';
    public $timestamps = false;
}
