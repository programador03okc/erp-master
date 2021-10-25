<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class DocumentoVenta extends Model
{
    protected $table = 'almacen.doc_ven';
    protected $primaryKey ='id_doc_ven';
    public $timestamps=false;


    public function tipo_documento(){
        return $this->hasOne('App\Models\Contabilidad\TipoDocumento','id_tp_doc','id_tp_doc');
    }
    public function moneda(){
        return $this->hasOne('App\Models\Configuracion\Moneda','id_moneda','moneda');
    }
    public function condicion_pago(){
        return $this->hasOne('App\Models\Logistica\CondicionPago','id_condicion_pago','id_condicion');
    }
    public function cliente(){
        return $this->hasOne('App\Models\Comercial\Cliente','id_cliente','id_cliente');
    }
    public function sede(){
        return $this->hasOne('App\Models\Administracion\Sede','id_sede','id_sede');
    }
}