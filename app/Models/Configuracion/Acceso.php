<?php


namespace App\Models\Configuracion;
use Illuminate\Database\Eloquent\Model;

class Acceso extends Model {

    protected $table = 'configuracion.sis_acceso';
    protected $primaryKey = 'id_acceso';
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\Configuracion\Usuario','id_usuario');
    }

    public function rol()
    {
        return $this->belongsTo('App\Configuracion\Rol','id_rol');
    }
}
