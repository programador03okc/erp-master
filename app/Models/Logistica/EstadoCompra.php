<?php

namespace App\Models\Logistica;

use Illuminate\Database\Eloquent\Model;

class EstadoCompra extends Model
{
    protected $table = 'logistica.estados_compra';
    protected $primaryKey = 'id_estado';
    public $timestamps = false;

    public static function mostrar()
    {
        $data = EstadoCompra::select(
                'estados_compra.*'
            )
            ->where('estados_compra.estado','!=',7)
            ->orderBy('estados_compra.descripcion', 'asc')
            ->get();
        return $data;
    }
}