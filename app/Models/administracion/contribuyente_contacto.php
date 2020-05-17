<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Model;

class contribuyente_contacto extends Model
{
         // table name
         protected $table = 'adm_ctb_contac';
         //primary key
         protected $primaryKey = 'id_datos_contacto';
        //  public $incrementing = false;
         //Timesptamps
         public $timestamps = false;
   
       protected $fillable = [
           'id_datos_contacto',
           'id_contribuyente',     
           'nombre',   
           'cargo',   
           'telefono',   
           'email',   
           'estado',   
           'fecha_registro'
   
       ];

    //    public function contribuyentecontacto()
    //    {
    //        return $this->hasOne('App\Models\administracion\contribuyente','id_contribuyente','id_contribuyente');
 
    //    }
}
