<?php

namespace App\models\rrhh;

use Illuminate\Database\Eloquent\Model;

class rrhh_tipo_planilla extends Model
{
    //
    protected $table = 'rrhh.rrhh_tipo_planilla';
    protected $primaryKey = 'id_tipo_planilla';

    protected $fillable = [
        'id_tipo_planilla',
        'descripcion',
        'estado'
    ];
}
