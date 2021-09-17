<?php

namespace App\Models\mgcp\CuadroCosto;

use App\Models\mgcp\CuadroCosto\Ajuste\FondoProveedor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CcVentaProveedor extends Model
{
    use HasFactory;
    protected $table = 'mgcp_cuadro_costos.cc_venta_proveedor';
    public $timestamps = false;

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }

    public function getComentarioAttribute()
    {
        if ($this->attributes['comentario'] == null) {
            return "-";
        } else {
            return $this->attributes['comentario'];
        }
    }

    public function fondoProveedor()
    {
        return $this->hasOne(FondoProveedor::class, 'id', 'id_fondo_proveedor');
    }
}
