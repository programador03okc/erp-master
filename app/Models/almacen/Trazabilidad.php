<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Model;

class Trazabilidad extends Model
{
    protected $table = 'almacen.alm_req_obs';
    protected $primaryKey = 'id_observacion';
    public $timestamps = false;
}
