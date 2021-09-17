<?php

namespace App\Models\mgcp\OrdenCompra\Propia;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Despacho extends Model {

    protected $table = 'mgcp_ordenes_compra.despachos';
    public $timestamps = false;

    public function setFechaSalidaAttribute($value)
    {
        $this->attributes['fecha_salida']=$value==null ? null : Carbon::createFromFormat('d-m-Y',$value);
    }

    public function getFechaSalidaAttribute()
    {
        return $this->attributes['fecha_salida']==null ? '' : (new Carbon($this->attributes['fecha_salida']))->format('d-m-Y');
    }

    public function setFechaLlegadaAttribute($value)
    {
        $this->attributes['fecha_llegada']=$value==null ? null : Carbon::createFromFormat('d-m-Y',$value);
    }

    public function getFechaLlegadaAttribute()
    {
        return $this->attributes['fecha_llegada']==null ? '' : (new Carbon($this->attributes['fecha_llegada']))->format('d-m-Y');
    }
}
