<?php

namespace App\Models\Presupuestos;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'administracion.adm_empresa';

    protected $primaryKey = 'id_empresa';

    protected $fillable = [
        "id_contribuyente",
        "codigo",
        "estado",
        "fecha_registro"
    ];
}
