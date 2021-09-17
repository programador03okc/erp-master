<?php

namespace App\Models\mgcp\CuadroCosto\Ajuste;

use Illuminate\Database\Eloquent\Model;

class FondoUtilizadoView extends Model
{
    protected $table = 'mgcp_cuadro_costos.fondos_proveedores_utilizados_view';
    public $timestamps = false;

    public function getPrecioAttribute()
    {
        return ($this->attributes['moneda'] == 's' ? 'S/' : '$') . number_format($this->attributes['precio'], 2);
    }

    /*public function getSubtotalDisponibleAttribute()
    {
        return ($this->attributes['moneda'] == 's' ? 'S/' : '$') . number_format($this->attributes['subtotal_disponible'], 2);
    }*/

}
