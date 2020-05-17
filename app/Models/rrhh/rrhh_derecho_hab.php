<?php

namespace App\Models\rrhh;

use Illuminate\Database\Eloquent\Model;

class rrhh_derecho_hab extends Model
{
    protected $table = 'rrhh_cdn_dhab';
    protected $primaryKey = 'id_condicion_dh';
    public $timestamps = false;

    protected $fillable = [
        'id_condicion_dh',
        'descripcion',
        'estado'
    ];
}
