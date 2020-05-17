<?php

namespace App\Models\sistema;

use Illuminate\Database\Eloquent\Model;

class pais extends Model
{
         // table name
         protected $table = 'sis_pais';
         //primary key
         protected $primaryKey = 'id_pais';
        //  public $incrementing = false;
         //Timesptamps
         public $timestamps = false;
   
       protected $fillable = [
           'id_pais',
           'descripcion',     
           'abreviatura',     
           'estado',     
    
       ];
}
