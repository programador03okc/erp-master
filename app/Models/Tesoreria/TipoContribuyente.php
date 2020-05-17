<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class TipoContribuyente extends Model {
	// table name
	protected $table = 'contabilidad.adm_tp_contri';
	//primary key
	protected $primaryKey = 'id_tipo_contribuyente';
	//  public $incrementing = false;
	//Timesptamps
	public $timestamps = false;

	protected $fillable = [
		'descripcion',
		'estado',
		'cod_sunat'

	];

}
