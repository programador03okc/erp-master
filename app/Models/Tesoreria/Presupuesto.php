<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class Presupuesto extends Model
{
    //
    protected $table = 'finanzas.presup';

    protected $primaryKey = 'id_presup';

    public $timestamps = false;
/*
   protected $fillable = [
        'codigo',
        'descripcion',
        'direccion',
        'estado',
    ];
   */
    protected $guarded = ['id_presup'];

    public function presupuesto_titulos(){
    	return $this->hasMany(PresupuestoTitulo::class, 'id_presup');
	}


    public function empresa(){
    	return $this->belongsTo(Empresa::class, 'id_empresa');
	}

	public function grupo(){
    	return $this->belongsTo(Grupo::class, 'id_grupo');
	}


	public function moneda(){
		return $this->belongsTo(Moneda::class,'moneda','id_moneda');
	}

}
