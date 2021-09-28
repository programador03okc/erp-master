<?php

namespace App\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'administracion.adm_estado_doc';
    protected $primaryKey = 'id_estado_doc';
    public $timestamps = false;

    public static function mostrar()
    {
        $data = Estado::select(
                'adm_estado_doc.*'
            )
            ->where('adm_estado_doc.estado','!=',7)
            ->orderBy('adm_estado_doc.estado_doc', 'asc')
            ->get();
        return $data;
    }
}
