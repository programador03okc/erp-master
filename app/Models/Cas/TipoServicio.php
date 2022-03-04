<?php

namespace App\Models\Cas;

use Illuminate\Database\Eloquent\Model;

class TipoServicio extends Model
{
    protected $table = 'cas.incidencia_tipo_servicio';
    public $timestamps = false;
    protected $primaryKey = 'id_tipo_servicio';
}
