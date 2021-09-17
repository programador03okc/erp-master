<?php

namespace App\Models\mgcp\AcuerdoMarco;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model {

    protected $table = 'mgcp_acuerdo_marco.provincias';
    public $timestamps = false;
    
    public static function obtenerDesdeUbigeo($ubigeo)
    {
        $primeraPos=strpos($ubigeo,"/");
        $segundaPos=strpos(substr($ubigeo,$primeraPos+2),"/");
        $nombreProvincia=substr(substr($ubigeo,$primeraPos+2),0,$segundaPos-1);
        return Provincia::where('nombre',$nombreProvincia)->first();
    }
}