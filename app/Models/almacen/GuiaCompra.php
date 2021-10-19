<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class GuiaCompra extends Model
{
    protected $table = 'almacen.guia_com';
    protected $primaryKey ='id_guia';
    public $timestamps=false;

    public function tipo_documento_almacen(){
        return $this->hasOne('App\Models\Almacen\TipoDocumentoAlmacen','id_tp_doc_almacen','id_tp_doc_almacen');
    }
    public function proveedor(){
        return $this->hasOne('App\Models\Logistica\Proveedor','id_proveedor','id_proveedor');
    }
    public function estado(){
        return $this->hasone('App\Models\Administracion\Estado','id_estado_doc','estado');
    }
}