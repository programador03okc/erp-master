<?php

namespace App\models\Configuracion;

use App\Models\Configuracion\SisUsua;
use Illuminate\Database\Eloquent\Model;

class UsuarioGrupo extends Model
{
    //
    protected $table = 'configuracion.usuario_grupo';
	protected $primaryKey = 'id_usuario_grupo';
    public $timestamps = false;

    public function sisUSua()
    {
        return $this->belongsTo(SisUsua::class, 'id_usuario', 'id_usuario');
    }
}
