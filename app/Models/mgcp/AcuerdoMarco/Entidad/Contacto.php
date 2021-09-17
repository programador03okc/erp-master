<?php

namespace App\Models\mgcp\AcuerdoMarco\Entidad;

use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
  protected $table = 'mgcp_acuerdo_marco.entidad_contactos';
  public $timestamps = false;

  public function getEmailAttribute()
  {
    return $this->attributes['email'] ?? '';
  }

  public function getCargoAttribute()
  {
    return $this->attributes['cargo'] ?? '';
  }

  public function getDireccionAttribute()
  {
    return $this->attributes['direccion'] ?? '';
  }

  public function getHorarioAttribute()
  {
    return $this->attributes['horario'] ?? '';
  }
}
