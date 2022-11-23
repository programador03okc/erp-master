<?php


namespace App\Models\Logistica;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Debugbar;

class PagoCuotaDetalle extends Model
{

    protected $table = 'logistica.pago_cuota_detalle';
    protected $primaryKey = 'id_pago_cuota_detalle';
    public $timestamps = false;

    public function getFechaRegistroAttribute()
    {
        $fecha = new Carbon($this->attributes['fecha_registro']);
        return $fecha->format('d-m-Y h:m');
    }
    public function getFechaAutorizacionAttribute()
    {
        if(isset($this->attributes['fecha_autorizacion'])){
            $fecha = new Carbon($this->attributes['fecha_autorizacion']);
            return $fecha->format('d-m-Y h:m');
        }
    }
    public function creadoPor()
    {
        return $this->belongsTo('App\Models\Configuracion\Usuario', 'id_usuario', 'id_usuario');
    }

    public function adjuntos()
    {
        return $this->hasMany('App\Models\Logistica\AdjuntosLogisticos', 'id_pago_cuota_detalle', 'id_pago_cuota_detalle');
    }
    public function estado()
    {
        return $this->hasOne('App\Models\Tesoreria\RequerimientoPagoEstados', 'id_requerimiento_pago_estado', 'id_estado');
    }
    
}
