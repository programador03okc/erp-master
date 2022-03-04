<?php

namespace App\Models\Cas;

use Illuminate\Database\Eloquent\Model;

class MedioReporte extends Model
{
    protected $table = 'cas.incidencia_medio';
    public $timestamps = false;
    protected $primaryKey = 'id_medio';
}
