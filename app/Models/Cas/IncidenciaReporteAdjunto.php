<?php

namespace App\Models\Cas;

use Illuminate\Database\Eloquent\Model;

class IncidenciaReporteAdjunto extends Model
{
    protected $table = 'cas.incidencia_reporte_adjuntos';
    public $timestamps = false;
    protected $primaryKey = 'id_adjunto';
}
