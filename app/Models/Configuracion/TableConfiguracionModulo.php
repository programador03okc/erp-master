<?php

namespace App\models\Configuracion;

use Illuminate\Database\Eloquent\Model;

class TableConfiguracionModulo extends Model
{
    //
    protected $table = 'configuracion.modulos';
    protected $primaryKey = 'id_modulo';
    public $timestamps = false;

    public function accesos()
    {
        return $this->belongsTo(Accesos::class, 'id_modulo', 'id_modulo');
    }
    public function accesosUsuarios()
    {
        return $this->hasOne(AccesosUsuarios::class, 'id_padre', 'id_modulo');
    }
}
