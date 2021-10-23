<?php

namespace App\Models\Comercial;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table='comercial.com_cliente';
    public $timestamps=false;
    protected $primaryKey='id_cliente';

    public function contribuyente(){
        return $this->hasOne('App\Models\Contabilidad\Contribuyente','id_contribuyente','id_contribuyente');
    }


}
