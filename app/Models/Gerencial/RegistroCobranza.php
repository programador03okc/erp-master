<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class RegistroCobranza extends Model
{
    //
    protected $table = 'gerencia_cobranza.registros_cobranzas';
    protected $primaryKey = 'id_registro_cobranza';
    public $timestamps = false;
}
