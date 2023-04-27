<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class DocumentoCompraDetalle extends Model
{
    protected $table = 'almacen.doc_com_det';
    protected $primaryKey ='id_doc_det';
    public $timestamps=false;

    public function documento_compra(){
        return $this->hasOne('App\Models\Almacen\DocumentoCompra','id_doc_com','id_doc');
    }

    public function unidadMedida(){
        return $this->hasone('App\Models\Almacen\UnidadMedida','id_unidad_medida','id_unid_med');
    }

    public function requerimientoPagoDetalle(){
        return $this->hasOne('App\Models\Tesoreria\RequerimientoPagoDetalle','id_requerimiento_pago_detalle','id_requerimiento_pago_detalle');
    }

}