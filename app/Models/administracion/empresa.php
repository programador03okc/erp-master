<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
  protected $table = 'administracion.adm_empresa';
  protected $primaryKey = 'id_empresa';
  public $timestamps = false;

  protected $fillable = [
    'id_empresa',
    'id_contribuyente',
    'codigo',
    'estado',
    'fecha_registro',
    'logo_empresa'

  ];

  public static function mostrar()
  {
      $data =Empresa::select('adm_empresa.id_empresa', 'adm_empresa.logo_empresa','adm_contri.nro_documento', 'adm_contri.razon_social')
          ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
          ->where('adm_empresa.estado', '=', 1)
          ->orderBy('adm_contri.razon_social', 'asc')
          ->get();
      return $data;
  }

}
