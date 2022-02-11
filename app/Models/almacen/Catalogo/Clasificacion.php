<?php

namespace App\Models\almacen\Catalogo;

use Illuminate\Database\Eloquent\Model;

class Clasificacion extends Model
{
    protected $table = 'almacen.alm_clasif';
    public $timestamps = false;
    protected $primaryKey = 'id_clasificacion';
}
