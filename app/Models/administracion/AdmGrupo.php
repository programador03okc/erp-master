<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Model;

class AdmGrupo extends Model
{
    //
    protected $table = 'administracion.adm_grupo';
    protected $primaryKey = 'id_grupo';
    public $timestamps = false;
}
