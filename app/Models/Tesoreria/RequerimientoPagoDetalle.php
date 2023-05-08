<?php

namespace App\Models\Tesoreria;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RequerimientoPagoDetalle extends Model
{
    protected $table = 'tesoreria.requerimiento_pago_detalle';
    protected $primaryKey = 'id_requerimiento_pago_detalle';
    public $timestamps = false;
 
    public function getFechaRegistroAttribute()
    {
        $fecha = new Carbon($this->attributes['fecha_registro']);
        return $fecha->format('d-m-Y H:i');
    }

    public function adjunto(){
        return $this->hasMany('App\Models\Tesoreria\RequerimientoPagoAdjuntoDetalle','id_requerimiento_pago_detalle','id_requerimiento_pago_detalle');
    }
    public function presupuestoInternoDetalle(){
        return $this->hasone('App\Models\Finanzas\PresupuestoInternoDetalle','id_presupuesto_interno_detalle','id_partida_pi');
    }
    public function centroCosto(){
        return $this->hasone('App\Models\Presupuestos\CentroCosto','id_centro_costo','id_centro_costo');
    }
    public function partida(){
        return $this->hasone('App\Models\Presupuestos\Partida','id_partida','id_partida');
    }
    public function producto(){
        return $this->hasone('App\Models\Almacen\Producto','id_producto','id_producto');
    }
    public function unidadMedida(){
        return $this->hasone('App\Models\Almacen\UnidadMedida','id_unidad_medida','id_unidad_medida');
    }
    public function estado(){
        return $this->hasone('App\Models\Administracion\Estado','id_estado_doc','id_estado');
    }
}
