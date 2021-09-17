<?php

namespace App\Models\mgcp\CuadroCosto\Ajuste;

use Illuminate\Database\Eloquent\Model;

class FondoProveedorView extends Model
{
    protected $table = 'mgcp_cuadro_costos.fondos_proveedores_view';
    public $timestamps = false;
    protected $appends = ['valor_unitario_format','subtotal_disponible_format'];

    public function getValorUnitarioFormatAttribute()
    {
        return ($this->attributes['moneda'] == 's' ? 'S/' : '$') . number_format($this->attributes['valor_unitario'], 2);
    }

    public function getSubtotalDisponibleFormatAttribute()
    {
        return ($this->attributes['moneda'] == 's' ? 'S/' : '$') . number_format($this->attributes['subtotal_disponible'], 2);
    }

}
