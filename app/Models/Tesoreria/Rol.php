<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    //
    protected $table = 'rrhh.rrhh_rol';

    protected $primaryKey = 'id_rol';

    public $timestamps = false;
/*
   protected $fillable = [
        'codigo',
        'descripcion',
        'direccion',
        'estado',
    ];
   */
    protected $guarded = ['id_rol'];

	public function trabajador(){
		return $this->belongsTo('App\Models\Tesoreria\Trabajador','id_trabajador','id_trabajador');
	}

	public function area(){
		return $this->belongsTo('App\Models\Tesoreria\Area', 'id_area', 'id_area');
	}

    public function cargo(){
        return $this->belongsTo(Cargo::class, 'id_cargo', 'id_cargo');
    }


	public function rol_concepto(){
		return $this->belongsTo(RolConcepto::class, 'id_rol_concepto', 'id_rol_concepto');
	}


}
