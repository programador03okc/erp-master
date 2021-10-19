<?php

namespace App\Models\Logistica;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class OrdenCompraDetalle extends Model
{
    protected $table = 'logistica.log_det_ord_compra';
    protected $primaryKey = 'id_detalle_orden';
    public $timestamps = false;


    public function orden(){
        return $this->hasOne('App\Models\Logistica\Orden','id_orden_compra','id_orden_compra');
    }
    public function reserva(){
        return $this->hasMany('App\Models\Almacen\Reserva','id_detalle_requerimiento','id_detalle_requerimiento');
    }

    public function detalleRequerimiento(){
        return $this->hasMany('App\Models\Almacen\DetalleRequerimiento','id_detalle_requerimiento','id_detalle_requerimiento');
    }

    public function getFechaCreacionAttribute(){
        $fecha= new Carbon($this->attributes['fecha_creacion']);
        return $fecha->format('d-m-Y h:m');
    }
    public function getFechaLimiteAttribute(){
        $fecha= new Carbon($this->attributes['fecha_limite']);
        return $fecha->format('d-m-Y h:m');
    }
    public function getFechaEstadoAttribute(){
        $fecha= new Carbon($this->attributes['fecha_estado']);
        return $fecha->format('d-m-Y h:m');
    }
}
