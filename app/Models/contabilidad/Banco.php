<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Banco extends Model
{
    protected $table = 'contabilidad.cont_banco';
    protected $primaryKey = 'id_banco';
    public $timestamps = false;

    public static function mostrar()
    {
        $data = Banco::select('cont_banco.id_banco', 'adm_contri.razon_social')
            ->join('contabilidad.adm_contri', 'cont_banco.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->where('cont_banco.estado', '=', 1)
            ->orderBy('adm_contri.razon_social', 'asc')
            ->get();
        return $data;
    }
}
