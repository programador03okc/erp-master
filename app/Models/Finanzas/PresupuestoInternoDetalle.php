<?php

namespace App\Models\Finanzas;

use Illuminate\Database\Eloquent\Model;

class PresupuestoInternoDetalle extends Model
{
    //
    protected $table = 'finanzas.presupuesto_interno_detalle';
    protected $primaryKey = 'id_presupuesto_interno_detalle';
    public $timestamps = false;
}
