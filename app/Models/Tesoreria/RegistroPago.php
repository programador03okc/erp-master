<?php

namespace App\Models\Tesoreria;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class RegistroPago extends Model
{
    protected $table = 'tesoreria.registro_pago';
    protected $primaryKey = 'id_pago';
    public $timestamps = false;

    public function adjunto()
    {
        return $this->hasOne('App\Models\Tesoreria\RegistroPagoAdjuntos', 'id_pago', 'id_pago');
    }

}