<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class TipoDocumentoAlmacen extends Model
{
    protected $table = 'almacen.tp_doc_almacen';
    protected $primaryKey ='id_tp_doc_almacen';
    public $timestamps=false;
    
    public function estado(){
        return $this->hasone('App\Models\Administracion\Estado','id_estado_doc','estado');
    }
}