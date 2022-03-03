<?php

namespace App\Models\Presupuestos;

use App\Models\Configuracion\Grupo;
use Illuminate\Database\Eloquent\Model;

class CentroCosto extends Model
{
    protected $table = 'finanzas.centro_costo';
    public $timestamps = false;
    protected $primaryKey = 'id_centro_costo';

    public function grupo()
    {
        return $this->hasOne(Grupo::class, 'id_grupo');
    }
}
