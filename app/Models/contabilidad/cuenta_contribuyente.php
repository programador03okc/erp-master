<?php

namespace App\Models\contabilidad;

use Illuminate\Database\Eloquent\Model;

class cuenta_contribuyente extends Model
{
         // table name
         protected $table = 'adm_cta_contri';
         //primary key
         protected $primaryKey = 'id_cuenta_contribuyente';
        //  public $incrementing = false;
         //Timesptamps
         public $timestamps = false;
   
       protected $fillable = [
           'id_cuenta_contribuyente',
           'id_banco',     
           'id_tipo_cuenta',   
           'nro_cuenta',   
           'nro_cuenta_interbancaria',   
           'fecha_registro'
   
       ];
}
