<?php

namespace App\Models\sistema;

use Illuminate\Database\Eloquent\Model;

class sistema_doc_identidad extends Model
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
