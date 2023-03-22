<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class GuiaVentaDetalle extends Model
{
    protected $table = 'almacen.guia_ven_det';
    protected $primaryKey = 'id_guia_ven_det';
    public $timestamps = false;

}
