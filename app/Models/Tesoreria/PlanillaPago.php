<?php

namespace App\Models\Tesoreria;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PlanillaPago extends Model
{
    //
    protected $table = 'finanzas.planillapagos';

    protected $fillable = [
        'fecha',
        'detalle',
        'cuenta_origen_id',
        'importe',
        'observaciones',
        'moneda_id',
		'planillapago_tipo_id',
        'estado_id',
        'usuario_id',
    ];

    protected $appends = [
        'observacion',
        'fecha_humanos'
    ];
	protected $dates = ['fecha'];

    public function historial(){
        return $this->hasMany(PlanillaPagosSeguimiento::class, 'planillapagos_id');
    }
    public function solicitudes(){
    	return $this->hasMany(PlanillaPagosSolicitudes::class, 'planillapagos_id');
	}
    public function cta_origen(){
    	return $this->belongsTo(ContribuyenteCuenta::class, 'cuenta_origen_id', 'id_cuenta_contribuyente');
	}
    public function tipo_planilla(){
    	return $this->belongsTo(PlanillaPagosTipos::class, 'planillapago_tipo_id');
	}
    public function usuario(){
        return $this->belongsTo('App\Models\Tesoreria\Usuario','usuario_id','id_usuario');
    }
    public function estado(){
        return $this->belongsTo(PlanillaPagosEstados::class,'estado_id');
    }
    public function moneda(){
        return $this->belongsTo('App\Models\Tesoreria\Moneda','moneda_id','id_moneda');
    }



    // Accessors

    public function getObservacionAttribute(){
        if ($this->historial()->count()>0){
            return $this->attributes['observacion'] = $this->historial()->orderBy('id', 'DESC')->first()->observacion;
        }
        else{
            return '';
        }

    }



    public function getFechaHumanosAttribute()
    {
    	$date = Carbon::parse($this->attributes['fecha']);
		return $date->diffInMonths(Carbon::now()) >= 1 ? $date->format('j M Y , g:ia') : $date->diffForHumans(null, null, null ,2);
    }



}
