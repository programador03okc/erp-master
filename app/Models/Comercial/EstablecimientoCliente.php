<?php

namespace App\Models\Comercial;

use Illuminate\Database\Eloquent\Model;

class EstablecimientoCliente extends Model
{
    //
    protected $table='comercial.establecimiento_cliente';
    public $timestamps=false;
    protected $primaryKey='id_establecimiento_cliente';
}
