<?php

namespace App\Models\Finanzas;

use Illuminate\Database\Eloquent\Model;

class PresupuestoInternoModelo extends Model
{
    //
    protected $table = 'finanzas.presupuesto_interno_modelo';
    protected $primaryKey = 'id_modelo_presupuesto_interno';
    public $timestamps = false;
}
