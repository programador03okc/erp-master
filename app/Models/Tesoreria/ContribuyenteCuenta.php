<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class ContribuyenteCuenta extends Model
{
    // table name
    protected $table = 'contabilidad.adm_cta_contri';
    //primary key
    protected $primaryKey = 'id_cuenta_contribuyente';
    //  public $incrementing = false;
    //Timesptamps
    public $timestamps = false;

    protected $fillable = [
        'id_contribuyente',
        'id_banco',
        'id_tipo_cuenta',
        'nro_cuenta',
        'nro_cuenta_interbancaria',
        'estado',
        'fecha_registro'

    ];

	public function contribuyente(){
		return $this->belongsTo(Contribuyente::class,'id_contribuyente','id_contribuyente');
	}


	public function banco(){
		return $this->belongsTo(Banco::class,'id_banco','id_banco');
	}


	public function tipo_cuenta	(){
		return $this->belongsTo(TipoCuenta::class,'id_tipo_cuenta','id_tipo_cuenta');
	}


}
