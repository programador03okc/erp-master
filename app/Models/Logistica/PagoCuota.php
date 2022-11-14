<?php


namespace App\Models\Logistica;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Debugbar;

class PagoCuota extends Model
{

    protected $table = 'logistica.pago_cuota';
    protected $primaryKey = 'id_pago_cuota';
    public $timestamps = false;

    public function getFechaRegistroAttribute()
    {
        $fecha = new Carbon($this->attributes['fecha_registro']);
        return $fecha->format('d-m-Y h:m');
    }

    public function detalle()
    {
        return $this->hasMany('App\Models\Logistica\PagoCuotaDetalle', 'id_pago_cuota', 'id_pago_cuota');
    }

}
