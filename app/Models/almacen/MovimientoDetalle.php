<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Model;

class MovimientoDetalle extends Model
{
    protected $table='almacen.mov_alm_det';
    protected $primaryKey='id_mov_alm_det';
    public $timestamps=false;

    public function guia_compra_detalle(){
        return $this->hasMany('App\Models\Almacen\GuiaCompraDetalle','id_guia_com_det','id_guia_com_det');
    }
}
