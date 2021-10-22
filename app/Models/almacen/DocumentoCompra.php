<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class DocumentoCompra extends Model
{
    protected $table = 'almacen.doc_com';
    protected $primaryKey ='id_doc_com';
    public $timestamps=false;


    public function tipo_documento(){
        return $this->hasOne('App\Models\Contabilidad\TipoDocumento','id_tp_doc','id_tp_doc');
    }
    public function moneda(){
        return $this->belongsTo('App\Models\Configuracion\Moneda','id_moneda','id_moneda');
    }
    public function condicion_pago(){
        return $this->hasOne('App\Models\Logistica\CondicionPago','id_condicion_pago','id_condicion');
    }
    public function proveedor(){
        return $this->hasOne('App\Models\Logistica\Proveedor','id_proveedor','id_proveedor');
    }
}