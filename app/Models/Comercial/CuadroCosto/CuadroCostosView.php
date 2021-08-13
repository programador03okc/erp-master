<?php

namespace App\Models\Comercial\CuadroCosto;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CuadroCostosView extends Model
{
    protected $table = 'mgcp_cuadro_costos.cc_view';
    protected $primaryKey = 'id';
    public $timestamps = false;

}
