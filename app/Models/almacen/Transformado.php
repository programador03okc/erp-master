<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class Transformado extends Model
{
    protected $table = 'almacen.transfor_transformado';
    protected $primaryKey = 'id_transformado';
    public $timestamps = false;

}
