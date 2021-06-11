<?php

namespace App\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'administracion.adm_documentos_aprob';
    protected $primaryKey = 'id_doc_aprob';
    public $timestamps = false;
}
