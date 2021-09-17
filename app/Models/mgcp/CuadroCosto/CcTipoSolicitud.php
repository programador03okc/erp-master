<?php

namespace App\Models\mgcp\CuadroCosto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CcTipoSolicitud extends Model
{
    use HasFactory;
    protected $table = 'mgcp_cuadro_costos.cc_tipos_solicitudes';
    public $timestamps = false;
}
