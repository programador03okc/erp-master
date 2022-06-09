<?php


namespace App\Models\Almacen;
use Illuminate\Database\Eloquent\Model;

class RequerimientoLogisticoView extends Model
{
    protected $table = 'almacen.requerimiento_logistico_view';
    public $timestamps = false;
    protected $casts = [
        'comprobante_venta_list' => 'json'
    ];

}

