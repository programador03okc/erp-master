<?php

namespace App\Models\mgcp\CuadroCosto\Ajuste;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FondoProveedor extends Model {

    use HasFactory;
    protected $table = 'mgcp_cuadro_costos.fondos_proveedores';
    public $timestamps = false;
    protected $appends = ['valor_unitario_format'];

    public function setMonedaAttribute($value) {
        $this->attributes['moneda']=$value=='s' ? 's' : 'd';
    }

    public function getValorUnitarioFormatAttribute($value) {
        return ($this->attributes['moneda']=='s' ? 'S/' : '$').number_format($this->attributes['valor_unitario'],2);
    }
}