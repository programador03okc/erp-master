<?php

namespace App\models\Configuracion;

use App\Models\Configuracion\SisUsua;
use Illuminate\Database\Eloquent\Model;

class AccesosUsuarios extends Model
{
    //
    protected $table = 'configuracion.accesos_usuarios';
    protected $primaryKey = 'id_acceso_usuario';
    public $timestamps = false;

    public function accesos()
    {
        return $this->hasOne(Accesos::class, 'id_acceso', 'id_acceso');
    }
    public function moduloPadre()
    {
        return $this->hasOne(TableConfiguracionModulo::class, 'id_modulo', 'id_padre');
<<<<<<< HEAD
    }
    public function usuario()
    {
        return $this->belongsTo(SisUsua::class, 'id_usuario', 'id_usuario')->where('estado',1);
=======
>>>>>>> develop
    }
}
