<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class PlanillaPagosTipos extends Model
{
    //
    protected $table = 'finanzas.planillapagos_tipos';

    protected $fillable = [
        'descripcion',
        'estado',
    ];
}
