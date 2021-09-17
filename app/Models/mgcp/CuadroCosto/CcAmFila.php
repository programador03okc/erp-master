<?php

namespace App\Models\mgcp\CuadroCosto;

use App\Models\mgcp\CuadroCosto\Ajuste\FondoProveedor;
use Illuminate\Database\Eloquent\Model;
use App\Models\mgcp\CuadroCosto\CcAmFilaComentario;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CcAmFila extends Model
{
    //use HasFactory;
    protected $table = 'mgcp_cuadro_costos.cc_am_filas';
    //public $timestamps = false;

    public function amProveedor()
    {
        return $this->hasOne(CcAmProveedor::class, 'id', 'proveedor_seleccionado');
    }

    public function fondoProveedor()
    {
        return $this->hasOne(FondoProveedor::class, 'id', 'id_fondo_proveedor');
    }

    public function cuadroAm()
    {
        return $this->belongsTo(CcAm::class, 'id_cc_am', 'id_cc');
    }

    public function comentarios()
    {
        return $this->hasMany(CcAmFilaComentario::class, 'id_fila', 'id');
    }

    public function setPartNoProductoTransformadoAttribute($value)
    {
        $this->attributes['part_no_producto_transformado'] = mb_strtoupper($value);
    }

    public function getPartNoProductoTransformadoAttribute()
    {
        return $this->attributes['part_no_producto_transformado'] ?? '';
    }

    public function setDescripcionProductoTransformadoAttribute($value)
    {
        $this->attributes['descripcion_producto_transformado'] = mb_strtoupper($value);
    }

    public function getDescripcionProductoTransformadoAttribute()
    {
        return $this->attributes['descripcion_producto_transformado'] ?? '';
    }

    public function setMarcaProductoTransformadoAttribute($value)
    {
        $this->attributes['marca_producto_transformado'] = mb_strtoupper($value);
    }

    public function getMarcaProductoTransformadoAttribute()
    {
        return $this->attributes['marca_producto_transformado'] ?? '';
    }

    public function setMarcaAttribute($value)
    {
        $this->attributes['marca'] = mb_strtoupper($value);
    }

    public function getMarcaAttribute()
    {
        return $this->attributes['marca'] ?? '';
    }

    public function setDescripcionAttribute($value)
    {
        $this->attributes['descripcion'] = mb_strtoupper($value);
    }

    public function getDescripcionAttribute()
    {
        return $this->attributes['descripcion'] ?? '';
    }

    public function setPartNoAttribute($value)
    {
        $this->attributes['part_no'] = mb_strtoupper($value);
    }

    public function getComentarioProductoTransformadoAttribute()
    {
        return $this->attributes['comentario_producto_transformado'] ?? '';
    }

    public function setComentarioProductoTransformadoAttribute($value)
    {
        $this->attributes['comentario_producto_transformado'] = mb_strtoupper($value);
    }

    public function getPartNoAttribute()
    {
        return $this->attributes['part_no'] ?? '';
    }

    public function tieneComentarios()
    {
        return CcAmFilaComentario::where('id_fila', $this->id)->count() > 0;
    }

    public function tieneTransformacion()
    {
        return !empty($this->attributes['part_no_producto_transformado']) || !empty($this->attributes['descripcion_producto_transformado']) || !empty($this->attributes['marca_producto_transformado']);
    }

    public function getEsIngresoTransformacionAttribute()
    {
        return CcFilaMovimientoTransformacion::where('id_fila_ingresa', $this->attributes['id'])->count() > 0;
    }
}
