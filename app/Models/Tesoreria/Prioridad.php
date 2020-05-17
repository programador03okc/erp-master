<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class Prioridad extends Model
{
    //
    protected $table = 'administracion.adm_prioridad';


	protected $primaryKey = 'id_prioridad';
	//  public $incrementing = false;
	//Timesptamps
	public $timestamps = false;

	protected $fillable = [
		'descripcion',
		'estado',

	];

    public function subtipos(){
        return $this->hasMany('App\Models\Tesoreria\SolicitudesSubTipos');
    }

}
