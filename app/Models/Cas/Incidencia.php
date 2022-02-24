<?php

namespace App\Models\Cas;

use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    protected $table = 'almacen.incidencia';
    public $timestamps = false;
    protected $primaryKey = 'id_incidencia';
}
