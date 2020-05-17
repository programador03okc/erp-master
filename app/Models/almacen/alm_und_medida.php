<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class alm_und_medida extends Model
{
    protected $table = 'alm_und_medida';

    protected $primaryKey = 'id_unidad_medida';

    public $timestamps = false;

    protected $fillable = [
        'id_unidad_medida',
        'descripcion',
        'abreviatura',
        'estado'
    ];
    protected $guarded = ['id_unidad_medida'];
}