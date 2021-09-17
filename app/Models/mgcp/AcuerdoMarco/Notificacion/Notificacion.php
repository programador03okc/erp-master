<?php

namespace App\Models\mgcp\AcuerdoMarco\Notificacion;

use App\Models\mgcp\AcuerdoMarco\Empresa;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
  protected $table = 'mgcp_acuerdo_marco.notificaciones';
  public $timestamps = false;
  public $incrementing = false;

  public function getFechaAttribute()
  {
    return $this->attributes['fecha'] == null ? '' : (new Carbon($this->attributes['fecha']))->format('d-m-Y g:i A');
  }

  public function empresa()
  {
    return $this->belongsTo(Empresa::class, 'id_empresa');
  }
}
