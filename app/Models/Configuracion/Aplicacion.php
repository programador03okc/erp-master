<?php


namespace App\Models\Configuracion;
use Illuminate\Database\Eloquent\Model;

class Aplicacion extends Model {

    protected $table = 'configuracion.sis_aplicacion';
    protected $primaryKey = 'id_aplicacion';
    public $timestamps = false;
}
