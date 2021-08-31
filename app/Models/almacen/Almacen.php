<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $table = 'almacen.alm_almacen';
    protected $primaryKey = 'id_almacen';
    public $timestamps = false;


    public static function mostrar()
    {
        $data = Almacen::select(
                'alm_almacen.*',
                'sis_sede.id_empresa',
                'sis_sede.descripcion as sede_descripcion',
                'alm_tp_almacen.descripcion as tp_almacen'
            )
            ->leftjoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
            ->join('almacen.alm_tp_almacen', 'alm_tp_almacen.id_tipo_almacen', '=', 'alm_almacen.id_tipo_almacen')
            ->where([['alm_almacen.estado', '=', 1]])
            ->orderBy('id_empresa', 'asc')
            ->get();
        return $data;
    }

}
