<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Model;

class area extends Model
{
         // table name
         protected $table = 'adm_area';
         //primary key
         protected $primaryKey = 'id_area';
        //  public $incrementing = false;
         //Timesptamps
         public $timestamps = false;
   
       protected $fillable = [
           'id_area',
           'id_empresa',
           'codigo',
           'descripcion',     
           'estado',     
           'fecha_registro'     
       ];
}
