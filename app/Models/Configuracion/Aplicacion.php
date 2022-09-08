<?php


namespace App\Models\Configuracion;
use Illuminate\Database\Eloquent\Model;

class Aplicacion extends Model {

    protected $table = 'configuracion.sis_aplicacion';
    protected $primaryKey = 'id_aplicacion';
    public $timestamps = false;

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'id_modulo', 'id_sub_modulo');
    }
    public function accion()
    {
        return $this->hasMany(Accion::class, 'id_aplicacion', 'id_aplicacion');
    }
}
