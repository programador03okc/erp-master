<?php

namespace App\Models\mgcp\AcuerdoMarco\Notificacion;

use Illuminate\Database\Eloquent\Model;

class FechaDescargaNotificacion extends Model
{
    protected $table = 'mgcp_acuerdo_marco.fechas_descarga_notificaciones';
    public $timestamps = false;
    public $incrementing = false;
}
