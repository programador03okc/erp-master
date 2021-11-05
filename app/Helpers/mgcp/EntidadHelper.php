<?php

namespace App\Helpers\mgcp;

use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;

class EntidadHelper
{

  public static function obtenerIdPorNombre($nombre)
  {
    $entidad = Entidad::where('nombre', $nombre)->first();
    if ($entidad == null) {
      $entidad = new Entidad;
      $entidad->nombre = $nombre;
      $entidad->save();
    }
    return $entidad->id;
  }

  public static function obtenerIdPorRuc($ruc, $nombre, $semaforo)
  {
    $entidad = Entidad::where('ruc', $ruc)->first();
    if ($entidad == null) {
      $entidad = new Entidad;
      $entidad->ruc = $ruc;
      $entidad->nombre = $nombre;
    }
    if ($semaforo != null) {
      $entidad->indicador_semaforo = $semaforo;
    }
    $entidad->save();
    return $entidad->id;
  }

  public static function existeRuc($ruc, $id = null)
  {
    $existe = Entidad::where('ruc', $ruc);
    if ($id != null) {
      $existe = $existe->where('id', '!=', $id);
    }
    return (!$existe->first() == null);
  }

  public static function existeNombre($nombre, $id = null)
  {
    $existe = Entidad::where('nombre', mb_strtoupper($nombre));
    if ($id != null) {
      $existe = $existe->where('id', '!=', $id);
    }
    return (!$existe->first() == null);
  }
}
