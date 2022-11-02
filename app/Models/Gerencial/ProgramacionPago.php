<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class ProgramacionPago extends Model
{
    //
    protected $table = 'gerencia_cobranza.programacion_pago';
    protected $primaryKey = 'id_programacion_pago';
    public $timestamps = false;
}
