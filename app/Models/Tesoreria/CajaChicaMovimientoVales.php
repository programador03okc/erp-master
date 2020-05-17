<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class CajaChicaMovimientoVales extends Model
{
    //
    protected $table = 'finanzas.cajachica_movimientos_vales';

    //protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
    	'numero',
        'cajachica_movimiento_id',
        'emisor_id',
        'receptor_id',
		'estado_id'
    ];

    //  protected $hidden = ['id_sucursal'];

    //protected $guarded = ['id'];


    public function cajachica_movimiento(){
        return $this->belongsTo(CajaChicaMovimiento::class,'cajachica_movimiento_id');
    }

    public function emisor(){
    	return $this->belongsTo(Usuario::class, 'emisor_id', 'id_usuario');
	}

	public function receptor(){
		return $this->belongsTo(Usuario::class,'receptor_id','id_usuario');
	}

	public function estado(){
		return $this->belongsTo('App\Models\Tesoreria\Estado','estado_id','id_estado_doc');
	}
}
