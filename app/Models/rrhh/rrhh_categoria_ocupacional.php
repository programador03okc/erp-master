<?php

namespace App\models\rrhh;

use Illuminate\Database\Eloquent\Model;

class rrhh_categoria_ocupacional extends Model
{
    //
    protected $table = 'rrhh.rrhh_categoria_ocupacional';
    protected $primaryKey = 'id_categoria_ocupacional';
    public $timestamps = false;

    protected $fillable = [
        'id_categoria_ocupacional',
        'descripcion',
        'estado'
    ];
}
