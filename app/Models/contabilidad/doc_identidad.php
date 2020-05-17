<?php

namespace App\Models\contabilidad;

use Illuminate\Database\Eloquent\Model;

class doc_identidad extends Model
{
         // table name
         protected $table = 'sis_identi';
         //primary key
         protected $primaryKey = 'id_doc_identidad';
        //  public $incrementing = false;
         //Timesptamps
         public $timestamps = false;
   
       protected $fillable = [
           'id_doc_identidad',
           'descripcion',
           'longitud',
           'estado'     
       ];
}
