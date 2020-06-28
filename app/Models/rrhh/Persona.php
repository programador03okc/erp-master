<?php


namespace App\Models\Rrhh;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model {
    protected $table = 'rrhh.rrhh_perso';
    protected $primaryKey = 'id_persona';
    public $timestamps = false;

    protected $appends = [
        'nombre_completo'
    ];

    public function getNombreCompletoAttribute(){
        return ucwords(strtolower($this->nombres) . ' ' . strtolower($this->apellido_paterno) . ' ' . strtolower($this->apellido_materno));
    }
}
