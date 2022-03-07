<?php

namespace App\Models\Cas;

use Illuminate\Database\Eloquent\Model;

class ModoIncidencia extends Model
{
    protected $table = 'cas.incidencia_modo';
    public $timestamps = false;
    protected $primaryKey = 'id_modo';
}
