<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class producto extends Model
{
    protected $table = 'alm_prod';

    protected $primaryKey = 'id_producto';

    public $timestamps = false;

    protected $fillable = [
        'id_producto',
        'codigo',
        'codigo_anexo',
        'codigo_proveedor',
        'descripcion',
        'id_unidad_medida',
        'presentacion',
        'id_subcategoria',
        'precio_unitario',
        'fecha_vencimiento',
        'merma',
        'desmedro',
        'consumible',
        'imagen',
        'estado',
        'fecha_registro'
    ];
    protected $guarded = ['id_producto'];
}