<?php

namespace App\models\contabilidad;

use Illuminate\Database\Eloquent\Model;

class Adjuntos extends Model
{
    //
    protected $table = 'contabilidad.adjuntos';
    protected $primaryKey = 'id_adjuntos';
    public $timestamps = false;
}
