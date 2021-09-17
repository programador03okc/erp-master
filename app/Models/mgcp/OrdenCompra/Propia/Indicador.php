<?php

namespace App\Models\mgcp\OrdenCompra\Propia;

use Illuminate\Database\Eloquent\Model;

class Indicador extends Model
{
    protected $table = 'mgcp_acuerdo_marco.oc_propias_indicadores';
    public $timestamps = false;
    protected $primaryKey = 'tipo';
    public $incrementing = false;
}