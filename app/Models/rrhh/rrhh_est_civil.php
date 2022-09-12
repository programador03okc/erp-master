<?php

namespace App\Models\rrhh;

use Illuminate\Database\Eloquent\Model;

class rrhh_est_civil extends Model
{
    protected $table = 'rrhh.rrhh_est_civil';
    protected $primaryKey = 'id_estado_civil';
    public $timestamps = false;

    protected $fillable = [
        'id_estado_civil',
        'descripcion',
        'estado'
    ];
}
