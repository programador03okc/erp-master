<?php

namespace App\Models\Almacen;

use App\Models\Administracion\Estado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Reserva extends Model
{
    protected $table = 'almacen.alm_reserva';
    protected $primaryKey = 'id_reserva';
    public $timestamps = false;
    protected $appends = ['nombre_estado'];



    public function getNombreEstadoAttribute()
    {
        $estado = Estado::join('almacen.alm_reserva', 'adm_estado_doc.id_estado_doc', '=', 'alm_reserva.estado')
        ->where('alm_reserva.id_reserva', $this->attributes['id_reserva'])
        ->first()->estado_doc;
        return $estado;
     }
    

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

    public function unidad_medida(){
        return $this->hasone('App\Models\Almacen\UnidadMedida','id_unidad_medida','id_unidad_medida');
    }
    public function almacen(){
        return $this->hasone('App\Models\Almacen\Almacen','id_almacen','id_almacen_reserva');
    }
    public function producto(){
        return $this->hasone('App\Models\Almacen\Producto','id_producto','id_producto');
    }
    public function usuario(){
        return $this->hasone('App\Models\Configuracion\Usuario','id_usuario','usuario_registro');
    }
    public function guia_compra_detalle(){
        return $this->hasMany('App\Models\Almacen\GuiaCompraDetalle','id_guia_com_det','id_guia_com_det');
    }
    public function transferencia_detalle(){
        return $this->hasMany('App\Models\Almacen\TransferenciaDetalle','id_trans_detalle','id_trans_detalle');
    }

    public function estado(){
        return $this->hasone('App\Models\Administracion\Estado','id_estado_doc','estado');
    }
    
}
