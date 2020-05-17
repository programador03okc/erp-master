<?php

namespace App\Models\sistema;

use Illuminate\Database\Eloquent\Model;

class usuario extends Model
{
  protected $table = 'sis_usua';
  protected $primaryKey = 'id_usuario';
        //  public $incrementing = false;
         //Timesptamps
  public $timestamps = false;
   
  protected $fillable = [
    'id_usuario',
    'id_trabajador',
    'usuario',
    'clave',        
    'estado',     
    'fecha_registro',
    'nombre_corto'  
  ];
}
