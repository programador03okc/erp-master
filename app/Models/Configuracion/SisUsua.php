<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;

class SisUsua extends Model
{
    //
    protected $table = 'configuracion.sis_usua';
	protected $primaryKey = 'id_usuario';
    public $timestamps = false;
}
