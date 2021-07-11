<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Model;

class AdjuntoDetalleRequerimiento extends Model
{
    protected $table = 'almacen.alm_det_req_adjuntos';
    protected $primaryKey = 'id_adjunto';
    public $timestamps = false;

}
