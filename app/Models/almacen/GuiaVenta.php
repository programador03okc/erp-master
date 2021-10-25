<?php

namespace App\Models\almacen;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class GuiaVenta extends Model
{
    protected $table = 'almacen.guia_ven';
    protected $primaryKey ='id_guia_ven';
    public $timestamps=false;
    protected $fillable = [
        
        'serie',
        'numero',
        'fecha_emision',
        'fecha_almacen',
        'id_almacen',
        'usuario',
        'estado',
        'fecha_registro',
        'id_sede',
        'punto_partida',
        'punto_llegada',
        'transportista',
        'fecha_traslado',
        'tra_serie',
        'tra_numero',
        'placa',
        'id_tp_doc_almacen',
        'id_operacion',
        'id_cliente',
        'registrado_por',
        'id_guia_com',
        'id_od',
        'id_persona',
        'id_transferencia'
    ];
    public function getFechaEmisionAttribute()
    {
        $fecha = new Carbon($this->attributes['fecha_emision']);
        return $fecha->format('d-m-Y');
    }

    public function tipo_documento_almacen(){
        return $this->hasOne('App\Models\Almacen\TipoDocumentoAlmacen','id_tp_doc_almacen','id_tp_doc_almacen');
    }
    public function cliente(){
        return $this->hasOne('App\Models\Comercial\Cliente','id_cliente','id_cliente');
    }
    public function estado(){
        return $this->hasone('App\Models\Administracion\Estado','id_estado_doc','estado');
    }
}