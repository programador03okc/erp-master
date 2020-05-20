<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Model;

class empresa extends Model
{
         // table name
         protected $table = 'adm_empresa';
         //primary key
         protected $primaryKey = 'id_empresa';
        //  public $incrementing = false;
         //Timesptamps
         public $timestamps = false;
   
       protected $fillable = [
           'id_empresa',
           'id_contribuyente',     
           'codigo',    
           'estado',    
           'fecha_registro'    
   
       ];
}
