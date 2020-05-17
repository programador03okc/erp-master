<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Model;

class tipo_cuenta extends Model
{
         // table name
         protected $table = 'adm_tp_cta';
         //primary key
         protected $primaryKey = 'id_tipo_cuenta';
        //  public $incrementing = false;
         //Timesptamps
         public $timestamps = false;
   
       protected $fillable = [
           'id_tipo_cuenta',
           'descripcion',     
           'estado'    
   
       ];
}
