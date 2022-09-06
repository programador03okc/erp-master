<?php

namespace App\models\Configuracion;

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
}
