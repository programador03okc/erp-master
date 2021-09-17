<?php

namespace App\Models\mgcp\CuadroCosto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Responsable extends Model
{
    use HasFactory;
    protected $table = 'mgcp_cuadro_costos.responsables';
    public $timestamps = false;
}
