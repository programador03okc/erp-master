<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    //
    protected $table = 'rrhh.rrhh_trab';

    protected $primaryKey = 'id_trabajador';

    public $timestamps = false;
/*
   protected $fillable = [
        'codigo',
        'descripcion',
        'direccion',
        'estado',
    ];
   */
    protected $guarded = ['id_trabajador'];

    public function usuario(){
        return $this->belongsTo(Usuario::class, 'id_trabajador', 'id_trabajador');
    }

    public function postulante(){
        return $this->belongsTo('App\Models\Tesoreria\Postulante','id_postulante','id_postulante');
    }

	public function roles(){
		return $this->belongsToMany(RolConcepto::class,'rrhh.rrhh_rol','id_trabajador', 'id_rol_concepto')->withPivot('id_rol', 'id_area')->wherePivot('estado',1);
	}



}
