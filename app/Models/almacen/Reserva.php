<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Reserva extends Model
{
    protected $table = 'almacen.alm_reserva';
    protected $primaryKey = 'id_reserva';
    public $timestamps = false;

    public static function crearCodigo(){
        $num = Reserva::obtenerCantidadRegistros();
        $yy = date('y', strtotime("now"));
        $correlativo= sprintf('%04d', ($num + 1));

        return "RE-{$yy}{$correlativo}";

    }
    public static function obtenerCantidadRegistros(){
        $yyyy = date('Y', strtotime("now"));
        $num = Reserva::whereYear('fecha_registro', '=', $yyyy)->count();
        return $num;

    }

    public function almacen(){
        return $this->hasone('App\Models\Almacen\Almacen','id_almacen','id_almacen_reserva');
    }
    public function usuario(){
        return $this->hasone('App\Models\Configuracion\Usuario','id_usuario','usuario_registro');
    }
    public function estado(){
        return $this->hasone('App\Models\Administracion\Estado','id_estado_doc','estado');
    }
}
