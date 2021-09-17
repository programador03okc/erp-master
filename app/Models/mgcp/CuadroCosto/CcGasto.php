<?php

namespace App\Models\mgcp\CuadroCosto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CcGasto extends Model
{
    use HasFactory;
    protected $table = 'mgcp_cuadro_costos.cc_gastos';
    public $timestamps = false;
}
