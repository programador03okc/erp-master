<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class categoria extends Model
{
    protected $table = 'alm_cat_prod';

    protected $primaryKey = 'id_categoria';

    public $timestamps = false;

    protected $fillable = [
        'id_categoria',
        'id_tipo_producto',
        'descripcion',
        'estado',
        'fecha_registro'
    ];
    protected $guarded = ['id_categoria'];
}