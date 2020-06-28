<?php


namespace App\Models\Configuracion;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model {

    protected $table = 'configuracion.sis_rol';
    protected $primaryKey = 'id_rol';
    public $timestamps = false;
}
