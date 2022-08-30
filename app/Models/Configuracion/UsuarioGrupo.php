<?php

namespace App\models\Configuracion;

use Illuminate\Database\Eloquent\Model;

class UsuarioGrupo extends Model
{
    //
    protected $table = 'configuracion.usuario_grupo';
	protected $primaryKey = 'id_usuario_grupo';
    public $timestamps = false;

}
