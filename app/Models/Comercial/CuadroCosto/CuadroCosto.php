<?php

namespace App\Models\Comercial\CuadroCosto;

use Illuminate\Database\Eloquent\Model;

class CuadroCosto extends Model
{
    protected $table='mgcp_cuadro_costos.cc';
    protected $primaryKey = 'id';
    public $timestamps=false;


    public function oportunidad(){
        return $this->hasOne('App\Models\Comercial\Oportunidad','id','id_oportunidad');
    }
}
