<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Model;

class sede extends Model
{
         // table name
         protected $table = 'sis_sede';
         //primary key
         protected $primaryKey = 'id_sede';
        //  public $incrementing = false;
         //Timesptamps
         public $timestamps = false;
   
       protected $fillable = [
           'id_sede',
           'id_empresa',
           'codigo',
           'descripcion',     
           'direccion',     
           'estado',     
           'fecha_registro'     
       ];
}
