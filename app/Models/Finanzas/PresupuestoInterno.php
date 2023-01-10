<?php

namespace App\Models\Finanzas;

use Illuminate\Database\Eloquent\Model;

class PresupuestoInterno extends Model
{
    //
    protected $table = 'finanzas.presupuesto_interno';
    protected $primaryKey = 'id_presupuesto_interno';
    public $timestamps = false;
}
