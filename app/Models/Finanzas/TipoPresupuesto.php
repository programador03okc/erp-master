<?php

namespace App\Models\Finanzas;

use Illuminate\Database\Eloquent\Model;

class TipoPresupuesto extends Model
{
    //
    protected $table = 'finanzas.tipo_presupuesto';
    protected $primaryKey = 'id_tipo_presupuesto';
    public $timestamps = false;
}
