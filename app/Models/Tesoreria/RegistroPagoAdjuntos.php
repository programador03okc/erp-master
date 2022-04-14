<?php

namespace App\Models\Tesoreria;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class RegistroPagoAdjuntos extends Model
{
    protected $table = 'tesoreria.registro_pago_adjuntos';
    protected $primaryKey = 'id_adjunto';
    public $timestamps = false;

}