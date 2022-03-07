<?php

namespace App\Models\Cas;

use Illuminate\Database\Eloquent\Model;

class AtiendeIncidencia extends Model
{
    protected $table = 'cas.incidencia_atiende';
    public $timestamps = false;
    protected $primaryKey = 'id_atiende';
}
