<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Model;

class contribuyente_rubro extends Model
{
         // table name
         protected $table = 'adm_ctb_rubro';
         //primary key
         protected $primaryKey = 'id_rubro';
        //  public $incrementing = false;
         //Timesptamps
         public $timestamps = false;
   
       protected $fillable = [
           'id_rubro',
           'id_contribuyente',     
           'descripcion',   
           'fecha_registro'
   
       ];
}
