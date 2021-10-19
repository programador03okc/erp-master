<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class TipoOperacion extends Model
{
    protected $table = 'almacen.tp_ope';
    protected $primaryKey ='id_operacion';
    public $timestamps=false;

    public function estado(){
        return $this->hasone('App\Models\Administracion\Estado','id_estado_doc','estado');
    }
}