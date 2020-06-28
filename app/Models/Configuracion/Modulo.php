<?php


namespace App\Models\Configuracion;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model {

    protected $table = 'configuracion.sis_modulo';
    protected $primaryKey = 'id_modulo';
    public $timestamps = false;
}
