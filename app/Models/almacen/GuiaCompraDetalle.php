<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class GuiaCompraDetalle extends Model
{
    protected $table = 'almacen.guia_com_det';
    protected $primaryKey ='id_guia_com_det';
    public $timestamps=false;


    public function guia_compra(){
        return $this->hasOne('App\Models\Almacen\GuiaCompra','id_guia','id_guia_com');
    }
    public function documento_compra_detalle(){
        return $this->hasMany('App\Models\Almacen\DocumentoCompraDetalle','id_guia_com_det','id_guia_com_det');
    }

    public function orden_detalle(){
        return $this->hasMany('App\Models\Logistica\OrdenCompraDetalle','id_detalle_orden','id_oc_det');
    }
    public function movimiento_detalle(){
        return $this->hasMany('App\Models\Almacen\MovimientoDetalle','id_guia_com_det','id_guia_com_det');
    }
 
}