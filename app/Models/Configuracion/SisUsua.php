<?php

namespace App\Models\Configuracion;

use App\models\Configuracion\UsuarioGrupo;
use App\models\Configuracion\UsuarioRol;
use Illuminate\Database\Eloquent\Model;

class SisUsua extends Model
{
    //
    protected $table = 'configuracion.sis_usua';
	protected $primaryKey = 'id_usuario';
    protected $fillable = ['id_trabajador', 'usuario', 'clave', 'password', 'estado', 'email', 'nombre_corto', 'nombre_largo', 'renovar'];
    public $timestamps = false;

    public function usuarioGrupo()
    {
        return $this->hasMany(UsuarioGrupo::class, 'id_usuario', 'id_usuario')->where('estado',1);
    }
    public function usuarioRol()
    {
        return $this->hasMany(UsuarioRol::class, 'id_usuario', 'id_usuario')->where('estado',1);
    }
}
