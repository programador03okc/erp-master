<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TipoDocumento extends Model
{
    protected $table = 'contabilidad.cont_tp_doc';
    protected $primaryKey = 'id_tp_doc';
    public $timestamps = false;

    public function estado(){
        return $this->hasone('App\Models\Administracion\Estado','id_estado_doc','estado');
    }

}