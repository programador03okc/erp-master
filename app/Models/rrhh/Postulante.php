<?php


namespace App\Models\Rrhh;
use Illuminate\Database\Eloquent\Model;

class Postulante extends Model {
    protected $table = 'rrhh.rrhh_postu';
    protected $primaryKey = 'id_postulante';
    protected $fillable = [
    'id_postulante',
    'id_persona',
    'direccion',
    'telefono',
    'correo',
    'brevette',
    'id_pais',
    'ubigeo',
    'fecha_registro'];
    public $timestamps = false;

    public function persona()
    {
        return $this->belongsTo('App\Models\Rrhh\Persona','id_persona');
        // return $this->belongsTo(Persona::class,'id_persona'); 
    }

}
