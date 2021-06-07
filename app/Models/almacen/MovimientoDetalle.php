<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Model;

class MovimientoDetalle extends Model
{
    protected $table='almacen.mov_alm_det';
    protected $primaryKey='id_mov_alm_det';
    public $timestamps=false;
}
