<?php

namespace App\Models\Cas;

use Illuminate\Database\Eloquent\Model;

class TipoGarantia extends Model
{
    protected $table = 'cas.incidencia_tipo_garantia';
    public $timestamps = false;
    protected $primaryKey = 'id_tipo_garantia';
}
