<?php

namespace App\Models\contabilidad;

use Illuminate\Database\Eloquent\Model;

class tipo_contribuyente extends Model
{
         // table name
         protected $table = 'adm_tp_contri';
         //primary key
         protected $primaryKey = 'id_tipo_contribuyente';
        //  public $incrementing = false;
         //Timesptamps
         public $timestamps = false;
   
       protected $fillable = [
           'id_tipo_contribuyente',
           'descripcion',     
           'estado'    
   
       ];


    //      public function contribuyente_tipocontribuyente()
    //    {
    //        return $this->hasOne('App\Models\administracion\contribuyente','id_tipo_contribuyente','id_tipo_contribuyente');
 
    //    }
}
