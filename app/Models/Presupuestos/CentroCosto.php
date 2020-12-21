<?php

namespace App\Models\Presupuestos;

use Illuminate\Database\Eloquent\Model;

class CentroCosto extends Model
{
    protected $table = 'finanzas.centro_costo';

    protected $primaryKey = 'id_centro_costo';

    public $timestamps = false;
    
    protected $fillable = [
        "codigo",
        "id_padre",
        "descripcion",
        "nivel",
        "estado"
    ];

}
