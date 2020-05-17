<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class PlanillaPagosEstados extends Model
{
    //
    protected $table = 'finanzas.planillapagos_estados';

    protected $fillable = [
        'descripcion',
        'estado',
    ];
}
