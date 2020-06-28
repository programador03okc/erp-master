<?php


namespace App\Models\Configuracion;
use Illuminate\Database\Eloquent\Model;

class Accion extends Model {

    protected $table = 'configuracion.sis_accion';
    protected $primaryKey = 'id_accion';
    public $timestamps = false;
}
