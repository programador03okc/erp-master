<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Identidad extends Model
{
    protected $table = 'contabilidad.sis_identi';
    protected $primaryKey = 'id_doc_identidad';
    public $timestamps = false;

    public static function mostrar()
    {
        $data = Identidad::select('sis_identi.id_doc_identidad', 'sis_identi.descripcion')
        ->where('sis_identi.estado', '=', 1)
        ->orderBy('sis_identi.descripcion', 'asc')->get();
    return $data;
    }
}
