<?php


namespace App\Models\Configuracion;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model {

    protected $table = 'configuracion.sis_modulo';
    protected $primaryKey = 'id_modulo';
    public $timestamps = false;

    public function aplicacion()
    {
        return $this->hasMany(Aplicacion::class, 'id_sub_modulo', 'id_modulo');
    }
}
