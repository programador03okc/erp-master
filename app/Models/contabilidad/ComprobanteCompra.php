<?php

namespace App\Models\contabilidad;

use Illuminate\Database\Eloquent\Model;

class ComprobanteCompra extends Model
{
    protected $table = 'almacen.doc_com';

    protected $primaryKey = 'id_doc_com';

    public $timestamps = false;
}
