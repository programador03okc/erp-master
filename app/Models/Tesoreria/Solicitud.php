<?php

namespace App\Models\Tesoreria;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    //
    protected $table = 'finanzas.solicitudes';

    public $timestamps = false;
    /*
     * [
"reg_tipo" => "0"
"reg_subtipo" => "0"
"reg_detalle" => null
"reg_empresa" => "0"
"reg_sede" => "0"
"reg_area" => "0"
"reg_moneda" => "1"
"reg_importe" => "0"
]
     */
    protected $fillable = [
        'codigo',
        'detalle',
        'importe',
        'fecha',
		'prioridad_id',
        'solicitud_subtipo_id',
        'estado_id',
        'usuario_id',
        'area_id',
        'moneda_id',
		'trabajador_id',
		'adjuntos',
    ];

    protected $appends = [
        'observacion',
        'fecha_humanos',
		'fecha_j_s',
    ];
	protected $dates = ['fecha'];

    public function historial(){
        return $this->hasMany(SolicitudSeguimiento::class, 'solicitud_id');
    }

	public function detalles(){
		return $this->hasMany(SolicitudDetalle::class, 'solicitud_id');
	}



    public function usuario(){
        return $this->belongsTo('App\Models\Tesoreria\Usuario','usuario_id','id_usuario');
    }
    public function estado(){
        return $this->belongsTo('App\Models\Tesoreria\Estado','estado_id','id_estado_doc');
    }
    public function moneda(){
        return $this->belongsTo('App\Models\Tesoreria\Moneda','moneda_id','id_moneda');
    }
    public function subtipo(){
        return $this->belongsTo('App\Models\Tesoreria\SolicitudesSubTipos', 'solicitud_subtipo_id', 'id');
    }
    public function area(){
        return $this->belongsTo('App\Models\Tesoreria\Area', 'area_id', 'id_area');
    }
    public function prioridad(){
        return $this->belongsTo(Prioridad::class, 'prioridad_id', 'id_prioridad');
    }
    public function trabajador(){
        return $this->belongsTo(Trabajador::class, 'trabajador_id', 'id_trabajador');
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

    public function getFechaJSAttribute(){
    	return Carbon::parse($this->attributes['fecha'])->format('d/m/Y');

	}



}
