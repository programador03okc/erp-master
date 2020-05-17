<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class PlanillaPagosSolicitudes extends Model
{
    //
    protected $table = 'finanzas.planillapagos_solicitudes';


    protected $fillable = [
        'planillapagos_id',
        'solicitud_id',
        'cuenta_destino',
        'cuenta_destino_tipo',
        'proveedor_id',
        'persona_id',
    ];

    public function planillapago(){
        return $this->belongsTo(PlanillaPago::class, 'planillapagos_id', 'id');
    }

    public function solicitud(){
        return $this->belongsTo(Solicitud::class);
    }

    public function persona(){
        return $this->belongsTo(Persona::class,'persona_id','id_persona');
    }
    public function proveedor(){
		return $this->belongsTo(Proveedor::class,'proveedor_id', 'id_proveedor');
    }


	public function cta_destino_tipo(){
		return $this->belongsTo(TipoCuenta::class, 'cuenta_destino_tipo', 'id_tipo_cuenta');
	}

}
