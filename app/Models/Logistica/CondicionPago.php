<?php

namespace App\Models\Logistica;

use Illuminate\Database\Eloquent\Model;

class CondicionPago extends Model
{
    protected $table = 'logistica.log_cdn_pago';
    protected $primaryKey = 'id_condicion_pago';
    public $timestamps = false;

}