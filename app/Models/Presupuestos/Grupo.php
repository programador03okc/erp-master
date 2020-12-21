<?php

namespace App\Models\Presupuestos;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'configuracion.sis_grupo';

    protected $primaryKey = 'id_grupo';

    protected $fillable = [
        "cod_grupo",
        "descripcion"
    ];
}
