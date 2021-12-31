<?php

namespace App\Models\Proyectos;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = 'proyectos.proy_proyecto';
    protected $primaryKey ='id_proyecto';    
    public $timestamps=false;


}