<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class RequerimientoPagoEstados extends Model
{
    protected $table = 'tesoreria.requerimiento_pago_estado';
    protected $primaryKey = 'id_requerimiento_pago_estado';
    public $timestamps = false;

}