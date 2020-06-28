<?php


namespace App\Models\Rrhh;
use Illuminate\Database\Eloquent\Model;

class Postulante extends Model {
    protected $table = 'rrhh.rrhh_postu';
    protected $primaryKey = 'id_postulante';
    public $timestamps = false;

    public function persona()
    {
        return $this->belongsTo('App\Models\Rrhh\Persona','id_persona')->withDefault();
    }

}
