<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class RolConcepto extends Model
{
    //
    protected $table = 'rrhh.rrhh_rol_concepto';

    protected $primaryKey = 'id_rol_concepto';

    public $timestamps = false;
/*
   protected $fillable = [
        'codigo',
        'descripcion',
        'direccion',
        'estado',
    ];
   */
    protected $guarded = ['id_rol_concepto'];

	public function usuarios(){
		return $this->belongsToMany(Usuario::class, 'rrhh.rrhh_rol', 'id_rol_concepto', 'id_trabajador');
	}


	public function usuario(){
        return $this->hasOne(Usuario::class, 'usuario_id');
    }

    public function postulante(){
        return $this->belongsTo('App\Models\Tesoreria\Postulante','id_postulante','id_postulante');
    }


	public function trabajador(){
		return $this->belongsToMany(Trabajador::class,'rrhh.rrhh_rol','id_trabajador', 'id_rol_concepto')->withPivot('id_rol', 'id_area')->wherePivot('estado',1);
	}
}
