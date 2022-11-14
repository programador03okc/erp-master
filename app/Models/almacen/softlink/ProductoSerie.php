<?php

namespace App\Models\almacen\softlink;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductoSerie extends Model
{
    protected $table = 'producto_serie_softlink';
    use SoftDeletes;

    protected $fillable = ['id_almacen', 'id_producto', 'codigo_producto', 'nombre', 'serie', 'fecha', 'tipo', 'documento'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
