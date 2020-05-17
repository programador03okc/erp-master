<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    //
    protected $table = 'administracion.adm_empresa';
    //primary key
    protected $primaryKey = 'id_empresa';
    //  public $incrementing = false;
    //Timesptamps
    public $timestamps = false;

    protected $fillable = [
        'id_contribuyente',
        'codigo',
        'estado',
        'fecha_registro'

    ];

    protected $guarded = ['id_sede'];

    public function contribuyente(){
        return $this->belongsTo('App\Models\Tesoreria\Contribuyente','id_contribuyente','id_contribuyente');
    }

    public function almacenes(){
        return $this->hasMany('App\Models\Tesoreria\Almacen','id_sede');
    }

    public function cajachica(){
        return $this->hasOne('App\Models\Tesoreria\CajaChicaMovimiento','id');
    }

    public function sedes(){
    	return $this->hasMany(Sede::class, 'id_empresa');
	}

	public function presupuestos(){
		return $this->hasMany(Presupuesto::class,'id_grupo', 'id_grupo');
	}

    /*
	public function solicitudes(){
		return $this->hasMany(Solicitud::class,'id', 'area.grupo.sede.empresa.id_empresa');
	}
    */

    public function getSolicitudesAttribute(){

	}
}
