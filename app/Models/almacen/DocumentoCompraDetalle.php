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
 
}