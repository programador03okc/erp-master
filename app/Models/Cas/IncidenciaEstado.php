<?php

namespace App\Models\Cas;

use Illuminate\Database\Eloquent\Model;

class IncidenciaEstado extends Model
{
    protected $table = 'cas.incidencia_estado';
    public $timestamps = false;
    protected $primaryKey = 'id_estado';
}
