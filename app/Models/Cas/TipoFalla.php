<?php

namespace App\Models\Cas;

use Illuminate\Database\Eloquent\Model;

class TipoFalla extends Model
{
    protected $table = 'cas.incidencia_tipo_falla';
    public $timestamps = false;
    protected $primaryKey = 'id_tipo_falla';
}
