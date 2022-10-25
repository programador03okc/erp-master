<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class DocVentReq extends Model
{
    //
    protected $table = 'almacen.doc_vent_req';
    protected $primaryKey ='id_documento_venta_requerimiento';
    public $timestamps=false;
}
