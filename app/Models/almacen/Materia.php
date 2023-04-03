<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table = 'almacen.transfor_materia';
    protected $primaryKey = 'id_materia';
    public $timestamps = false;

}
