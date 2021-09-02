<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class TransferenciaDetalle extends Model
{
    protected $table = 'almacen.trans_detalle';
    protected $primaryKey = 'id_trans_detalle';
    public $timestamps = false;
}
