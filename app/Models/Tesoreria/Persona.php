<?php

namespace App\Models\Tesoreria;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    //
    protected $table = 'rrhh.rrhh_perso';

    protected $primaryKey = 'id_persona';

    public $timestamps = false;
/*
   protected $fillable = [
        'codigo',
        'descripcion',
        'direccion',
        'estado',
    ];
   */
    protected $guarded = ['id_persona'];

	protected $appends = [
		'nombre_completo'
	];

    public function getNombreCompletoAttribute(){
    	return ucwords(strtolower($this->nombres) . ' ' . strtolower($this->apellido_paterno) . ' ' . strtolower($this->apellido_materno));
	}

	public function getNombresAttribute($data){
		return ucwords(strtolower($data));
	}

	public function getFechaRegistroAttribute($data){
    	return Carbon::parse($data)->format('j M Y');
	}



}
