<?php

namespace App\Models\Presupuestos;

use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
    protected $table = 'configuracion.sis_moneda';

    protected $primaryKey = 'id_moneda';

    protected $fillable = [
        "descripcion",
        "simbolo",
        "estado"
    ];
}
