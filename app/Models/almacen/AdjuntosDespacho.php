<?php

namespace App\models\almacen;

use Illuminate\Database\Eloquent\Model;

class AdjuntosDespacho extends Model
{
    //
    protected $table = 'almacen.adjuntos_despacho';
    protected $primaryKey = 'id_adjuntos_despacho';
    public $timestamps = false;
}
