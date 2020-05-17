<?php

namespace App\Models\Tesoreria;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CajaChicaMovimiento extends Model
{
    //
    protected $table = 'finanzas.cajachica_movimientos';

    //protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'cajachica_id',
        'fecha',
        'tipo_movimiento',
        'doc_operacion_id',
		'vale_numero',
        'doc_pago',
        'proveedor_id',
        'moneda_id',
        'tipo_cambio',
        'importe',
        'observaciones',
    ];

	protected $appends = [
		'fecha_humanos',
		'fecha_j_s'
	];

    //  protected $hidden = ['id_sucursal'];

    //protected $guarded = ['id'];


    public function cajachica(){
        return $this->belongsTo(CajaChica::class,'cajachica_id');
    }

    public function moneda(){
        return $this->belongsTo('App\Models\Tesoreria\Moneda','moneda_id','id_moneda');
    }

    public function doc_operacion(){
        return $this->belongsTo('App\Models\Tesoreria\DocumentosOperacion','doc_operacion_id','id');
    }

    public function proveedor(){
    	return $this->hasOne(Proveedor::class, 'id_proveedor', 'proveedor_id');
	}

    public function saldo(){
    	return $this->hasOne(CajaChicaSaldos::class, 'cajachica_movimiento_id');
	}

    public function vale(){
    	return $this->hasOne(CajaChicaMovimientoVales::class, 'cajachica_movimiento_id');
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
