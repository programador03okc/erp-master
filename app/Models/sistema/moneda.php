<?php

namespace App\Models\sistema;

use Illuminate\Database\Eloquent\Model;

class moneda extends Model
{
         // table name
         protected $table = 'sis_moneda';
         //primary key
         protected $primaryKey = 'id_moneda';
        //  public $incrementing = false;
         //Timesptamps
         public $timestamps = false;
   
       protected $fillable = [
           'id_moneda',
           'descripcion',     
           'simbolo',     
           'estado',     
    
       ];
}
