<?php

namespace App\Models\Presupuestos;

use Illuminate\Database\Eloquent\Model;

class CentroCosto extends Model
{
    protected $table = 'finanzas.centro_costo';
    public $timestamps = false;
    protected $primaryKey = 'id_centro_costo';
}
