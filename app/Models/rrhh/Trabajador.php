<?php


namespace App\Models\Rrhh;
use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model {
    protected $table = 'rrhh.rrhh_trab';
    protected $primaryKey = 'id_trabajador';
    public $timestamps = false;

    public function postulante()
    {
        return $this->belongsTo('App\Models\Rrhh\Postulante','id_postulante')->withDefault();
    }
}
