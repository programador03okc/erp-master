<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class sub_categoria extends Model
{
    protected $table = 'alm_subcategoria';

    protected $primaryKey = 'id_subcategoria';

    public $timestamps = false;

    protected $fillable = [
        'id_subcategoria',
        'id_categoria',
        'descripcion',
        'estado',
        'fecha_registro'
    ];
    protected $guarded = ['id_subcategoria'];
}