<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class Observaciones extends Model
{
    //
    protected $table = 'gerencia_cobranza.cobranzas_observaciones';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
