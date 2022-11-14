<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class Vendedor extends Model
{
    //
    protected $table = 'gerencia_cobranza.vendedor';
    protected $primaryKey = 'id_vendedor';
    public $timestamps = false;
}
