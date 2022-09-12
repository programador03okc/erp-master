<?php


namespace App\Models\Configuracion;
use Illuminate\Database\Eloquent\Model;

class Accion extends Model {

    protected $table = 'configuracion.sis_accion';
    protected $primaryKey = 'id_accion';
    public $timestamps = false;
    public function aplicacion()
    {
        return $this->belongsTo(Aplicacion::class, 'id_aplicacion', 'id_aplicacion');
    }
}
