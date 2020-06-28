<?php


namespace App\Models\Configuracion;
use Illuminate\Database\Eloquent\Model;

class AccionRol extends Model {

    protected $table = 'configuracion.sis_accion_rol';
    protected $primaryKey = 'id_accion_rol';
    public $timestamps = false;
}
