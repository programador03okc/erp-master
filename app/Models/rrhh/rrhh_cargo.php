<?php

namespace App\Models\rrhh;

use Illuminate\Database\Eloquent\Model;

class rrhh_cargo extends Model
{
    protected $table = 'rrhh_cargo';
    protected $primaryKey = 'id_cargo';
    public $timestamps = false;

    protected $fillable = [
        'id_cargo',
        'codigo',
        'descripcion',
        'sueldo_rango_minimo',
        'sueldo_rango_maximo',
        'estado',
        'fecha_registro'
    ];
}
