<?php


namespace App\Models\Configuracion;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model {

    protected $table = 'configuracion.sis_grupo';
    protected $primaryKey = 'id_grupo';
    public $timestamps = false;
}
