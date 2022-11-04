<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class Penalidad extends Model
{
    //
    protected $table = 'gerencia_cobranza.penalidad';
    protected $primaryKey = 'id_penalidad';
    public $timestamps = false;
}
