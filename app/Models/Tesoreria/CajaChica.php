<?php

namespace App\Models\Tesoreria;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CajaChica extends Model
{
    //
    protected $table = 'finanzas.cajachica';

    //protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'descripcion',
		'monto_apertura',
		'saldo',
		'monto_minimo',
		'monto_maximo_movimiento',
		'fecha_creacion',
		'solicitud_id',
		'area_id',
		'moneda_id',
		'responsable_id',
		'usuario_id',
		'estado_id',
    ];

    //  protected $hidden = ['id_sucursal'];

    //protected $guarded = ['id'];
	protected $appends = [
		'fecha_humanos',
		'empresa_id',
		'empresa_nombre',
	];

	public function getFechaHumanosAttribute() {
		$date = Carbon::parse($this->attributes['fecha_creacion']);
		return $date->diffInMonths(Carbon::now()) >= 1 ? $date->format('j M Y , g:ia') : $date->diffForHumans(null, null, null ,2);
	}

	public function getEmpresaIdAttribute(){
		return $this->area->grupo->sede->empresa->id_empresa;
	}

	public function getEmpresaNombreAttribute(){
		return $this->area->grupo->sede->empresa->contribuyente->razon_social;
	}

	public function solicitud(){
		return $this->belongsTo(Solicitud::class, 'solicitud_id', 'id');
	}

	public function area(){
		return $this->belongsTo('App\Models\Tesoreria\Area', 'area_id', 'id_area');
	}

    public function moneda(){
        return $this->belongsTo('App\Models\Tesoreria\Moneda','moneda_id','id_moneda');
    }

	public function responsable(){
		return $this->belongsTo('App\Models\Tesoreria\Usuario','responsable_id','id_usuario');
	}

	public function usuario(){
		return $this->belongsTo('App\Models\Tesoreria\Usuario','usuario_id','id_usuario');
	}

	public function estado(){
		return $this->belongsTo('App\Models\Tesoreria\Estado','estado_id','id_estado_doc');
	}

	public function movimientos(){
		return $this->hasMany(CajaChicaMovimiento::class, 'cajachica_id');
	}
}
