<?php

namespace App\models\Configuracion;

use Illuminate\Database\Eloquent\Model;

class Accesos extends Model
{
    //
    protected $table = 'configuracion.accesos';
    protected $primaryKey = 'id_accesos';
    public $timestamps = false;

    public function accesoUsuario()
    {
        return $this->hasOne(AccesosUsuarios::class, 'id_acceso', 'id_acceso');
    }

    public function modulos()
    {
        return $this->hasOne(TableConfiguracionModulo::class, 'id_modulo', 'id_modulo')->where('estado',1);
    }

    public function modulosAll()
    {
        return $this->belongsTo(TableConfiguracionModulo::class, 'id_modulo', 'id_modulo');
    }
}
