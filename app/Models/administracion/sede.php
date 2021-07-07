<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sede extends Model
{
  protected $table = 'administracion.sis_sede';
  protected $primaryKey = 'id_sede';
  public $timestamps = false;

  public static function listarSedesPorEmpresa($idEmpresa)
  {
      $data = Sede::select(
              'sis_sede.*', 'ubi_dis.descripcion as ubigeo_descripcion',
              DB::raw("concat(ubi_dis.descripcion, ' ',ubi_prov.descripcion,' ' ,ubi_dpto.descripcion)  AS ubigeo_descripcion")

          )
          ->leftJoin('configuracion.ubi_dis','ubi_dis.id_dis','=','sis_sede.id_ubigeo')
          ->leftJoin('configuracion.ubi_prov', 'ubi_dis.id_prov', '=', 'ubi_prov.id_prov')
          ->leftJoin('configuracion.ubi_dpto', 'ubi_prov.id_dpto', '=', 'ubi_dpto.id_dpto')
  
          ->where('sis_sede.id_empresa','=',$idEmpresa)
          ->orderBy('sis_sede.id_empresa', 'asc')
          ->get();
      return $data;
  }
}
