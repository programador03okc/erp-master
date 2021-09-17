<?php

namespace App\Models\mgcp\CuadroCosto;

use Illuminate\Database\Eloquent\Model;
use App\Models\mgcp\CuadroCosto\CcVentaFilaComentario;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CcVentaFila extends Model
{
    use HasFactory;
    protected $table = 'mgcp_cuadro_costos.cc_venta_filas';
    public $timestamps = false;

    public function cuadroVenta()
    {
        return $this->belongsTo(CcVenta::class,'id_cc_venta');
    }

    public function ventaProveedor()
    {
        return $this->hasOne(CcVentaProveedor::class, 'id', 'proveedor_seleccionado');
    }

    public function tieneComentarios()
    {
        $comentarios = CcVentaFilaComentario::where('id_fila', $this->id)->get();
        return $comentarios->count() > 0;
    }

    public function getDescripcionAttribute()
    {
        return  $this->attributes['descripcion'] == null ? '' :  $this->attributes['descripcion'];
    }
}
