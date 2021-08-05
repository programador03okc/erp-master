<?php

namespace App\Models\contabilidad;

use Illuminate\Database\Eloquent\Model;

class ComprobanteVenta extends Model
{
    protected $table = 'almacen.doc_ven';

    protected $primaryKey = 'id_doc_ven';

    public $timestamps = false;
}
