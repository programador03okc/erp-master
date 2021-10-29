<?php

namespace App\Models\almacen;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class GuiaCompra extends Model
{
    protected $table = 'almacen.guia_com';
    protected $primaryKey ='id_guia';
    public $timestamps=false;
    protected $fillable = [
        'serie',
        'numero',
        'id_proveedor',
        'fecha_emision',
        'fecha_almacen',
        'id_almacen',
        'usuario',
        'estado',
        'fecha_registro',
        'id_guia_clas',
        'id_operacion',
        'punto_partida',
        'punto_llegada',
        'transportista',
        'fecha_traslado',
        'tra_serie',
        'tra_numero',
        'placa',
        'id_tp_doc_almacen',
        'registrado_por',
        'prorratear_segun'
    ];
    public function getFechaEmisionAttribute()
    {
        $fecha = new Carbon($this->attributes['fecha_emision']);
        return $fecha->format('d-m-Y');
    }

    public function tipo_documento_almacen(){
        return $this->hasOne('App\Models\Almacen\TipoDocumentoAlmacen','id_tp_doc_almacen','id_tp_doc_almacen');
    }
    public function proveedor(){
        return $this->hasOne('App\Models\Logistica\Proveedor','id_proveedor','id_proveedor');
    }
    public function movimiento(){
        return $this->hasone('App\Models\Almacen\Movimiento','id_guia_com','id_guia');
    }
    public function estado(){
        return $this->hasone('App\Models\Administracion\Estado','id_estado_doc','estado');
    }
}