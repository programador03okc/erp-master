<?php

namespace App\Models\mgcp\AcuerdoMarco\Entidad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Entidad extends Model
{
  // use HasFactory;
  protected $table = 'mgcp_acuerdo_marco.entidades';
  protected $appends = ['semaforo'];
  public $timestamps = false;

  public function getSemaforoAttribute()
  {
    switch ($this->attributes['indicador_semaforo']) {
      case '0':
        $color = 'green';
        break;
      case '1':
        $color = 'yellow';
        break;
      case '2':
        $color = 'orange';
        break;
      case '3':
        $color = 'red';
        break;
      default:
        $color = 'silver';
        break;
    }
    return '<i style="color: ' . $color . '" class="fa fa-circle" aria-hidden="true"></i>';
  }

  public function setUbigeoAttribute($valor)
  {
    $this->attributes['ubigeo'] = mb_strtoupper($valor);
  }

  public function setCorreoAttribute($valor)
  {
    $this->attributes['correo'] = mb_strtolower($valor);
  }

  public function getNombreContactoAttribute()
  {
    return $this->attributes['nombre_contacto'] ?? '';
  }

  public function getComentarioAttribute()
  {
    return $this->attributes['comentario'] ?? '';
  }

  public function getCargoContactoAttribute()
  {
    return $this->attributes['cargo_contacto'] ?? '';
  }

  public function getTelefonoContactoAttribute()
  {
    return $this->attributes['telefono_contacto'] ?? '';
  }

  public function getCorreoContactoAttribute()
  {
    return $this->attributes['correo_contacto'] ?? '';
  }

  public function getUbigeoAttribute()
  {
    return $this->attributes['ubigeo'] ?? '';
  }

  public function getCorreoAttribute()
  {
    return $this->attributes['correo'] ?? '';
  }

  public function getDireccionAttribute()
  {
    return $this->attributes['direccion'] ?? '';
  }

  public function setCorreoContactoAttribute($valor)
  {
    $this->attributes['correo_contacto'] = mb_strtolower($valor);
  }
}
