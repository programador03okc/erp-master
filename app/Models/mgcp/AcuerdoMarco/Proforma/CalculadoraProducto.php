<?php

namespace App\Models\mgcp\AcuerdoMarco\Proforma;

use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalculadoraProducto extends Model
{
    use HasFactory;
    protected $table = 'mgcp_acuerdo_marco.calculadora_productos';
    public $timestamps = false;
    protected $primaryKey = 'id_producto';
    public $incrementing = false;

    public function detalles()
    {
        return $this->hasMany(CalculadoraProductoDetalle::class, 'id_producto')->orderBy('id','asc');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class,'id_producto','id');
    }
}
