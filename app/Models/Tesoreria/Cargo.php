<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    //
    protected $table = 'rrhh.rrhh_cargo';

    protected $primaryKey = 'id_cargo';

    public $timestamps = false;
/*
   protected $fillable = [
        'codigo',
        'descripcion',
        'direccion',
        'estado',
    ];
   */
    protected $guarded = ['id_cargo'];

	public function empresa() {
		return $this->hasOne('App\Models\Tesoreria\Empresa', 'id_empresa');
	}
}
