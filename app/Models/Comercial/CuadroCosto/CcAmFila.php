<?php

namespace App\Models\Comercial\CuadroCosto;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CcAmFila extends Model
{
    protected $table='mgcp_cuadro_costos.cc_am_filas';
    public $timestamps=false;
    protected $primaryKey='id';
    

    public function getFechaCreacionAttribute()
    {
        return (new Carbon($this->attributes['fecha_creacion']))->format('d-m-Y') ;
    }


}
