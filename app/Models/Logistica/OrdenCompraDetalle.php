<?php

namespace App\Models\Logistica;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrdenCompraDetalle extends Model
{
    protected $table = 'logistica.log_det_ord_compra';
    protected $primaryKey = 'id_detalle_orden';
    protected $appends = ['codigo_requerimiento'];
    public $timestamps = false;





    public function producto(){
        return $this->hasone('App\Models\Almacen\Producto','id_producto','id_producto');
    }
    public function unidad_medida(){
        return $this->hasone('App\Models\Almacen\UnidadMedida','id_unidad_medida','id_unidad_medida');
    }
    public function estado_orden(){
        return $this->hasOne('App\Models\Logistica\EstadoCompra','id_estado','estado');
    }
    public function orden(){
        return $this->hasOne('App\Models\Logistica\Orden','id_orden_compra','id_orden_compra');
    }
    public function reserva(){
        return $this->hasMany('App\Models\Almacen\Reserva','id_detalle_requerimiento','id_detalle_requerimiento');
    }
    public function guia_compra_detalle(){
        return $this->hasMany('App\Models\Almacen\GuiaCompraDetalle','id_oc_det','id_detalle_orden');
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
        $fecha= new Carbon($this->id_detalle_orden['fecha_estado']);
        return $fecha->format('d-m-Y h:m');
    }

    public function getCodigoRequerimientoAttribute(){
        $codigoRequerimiento='';
        if(($this->attributes['id_detalle_orden']??0) >0){
            $codigoRequerimiento=OrdenCompraDetalle::leftJoin('almacen.alm_det_req','log_det_ord_compra.id_detalle_requerimiento','alm_det_req.id_detalle_requerimiento')
            ->Join('almacen.alm_req','alm_req.id_requerimiento','alm_det_req.id_requerimiento')
            ->where('log_det_ord_compra.id_detalle_orden',$this->attributes['id_detalle_orden'])
            ->select('alm_req.codigo')->first()->codigo; 
            

        }
        return $codigoRequerimiento;

    }
}
