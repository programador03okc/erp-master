<?php

namespace App\Models\Logistica;

use Illuminate\Database\Eloquent\Model;

class Contribuyente extends Model
{
    // table name
    protected $table = 'contabilidad.adm_contri';
    //primary key
    protected $primaryKey = 'id_contribuyente';
    //  public $incrementing = false;
    //Timesptamps
    public $timestamps = false;

 

    public function empresa()
    {
        return $this->hasOne('App\Models\Logistica\Empresa', 'id_empresa');
    }

    public function proveedor()
    {
        return $this->hasOne('App\Models\Logistica\Proveedor', 'id_proveedor');
    }


}
