<?php

namespace App\Models\mgcp\AcuerdoMarco\Proforma;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalculadoraProductoDetalle extends Model
{
    // use HasFactory;
    protected $table = 'mgcp_acuerdo_marco.calculadora_producto_detalles';
    public $timestamps = false;

    public function getConceptoAttribute()
    {
        return $this->attributes['concepto'] ?? '';
    }

    public function getMontoAttribute()
    {
        return $this->attributes['monto'] ?? 0;
    }
}
