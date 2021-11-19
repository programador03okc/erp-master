<?php

namespace App\Models\mgcp\CuadroCosto\Ajuste;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FondoIngreso extends Model {
    // use HasFactory;
    protected $table = 'mgcp_cuadro_costos.fondos_ingresos';
    public $timestamps = false;

    public function getFechaAttribute($value) {
        return (new Carbon($this->attributes['fecha']))->format('d-m-Y h:i');
    }
}