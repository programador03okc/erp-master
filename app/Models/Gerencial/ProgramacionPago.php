<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class ProgramacionPago extends Model
{
    //
    protected $table = 'cobranza.programacion_pago';
    protected $primaryKey = 'id_programacion_pago';
    public $timestamps = false;
}
