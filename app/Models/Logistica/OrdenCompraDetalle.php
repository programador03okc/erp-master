<?php

namespace App\Models\Logistica;

use Illuminate\Database\Eloquent\Model;

class OrdenCompraDetalle extends Model
{
    protected $table = 'logistica.log_det_ord_compra';
    protected $primaryKey = 'id_detalle_orden';
    public $timestamps = false;
}
