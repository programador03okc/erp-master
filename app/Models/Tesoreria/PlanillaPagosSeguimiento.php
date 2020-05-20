<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class PlanillaPagosSeguimiento extends Model
{
    //
    protected $table = 'finanzas.planillapagos_seguimiento';


    protected $fillable = [
        'observacion',
        'planillapagos_id',
        'estado_id',
        'usuario_id',
        'fecha'
    ];

    public function planillapago(){
        return $this->belongsTo(PlanillaPago::class, 'planillapagos_id', 'id');
    }

    public function usuario(){
        return $this->belongsTo('App\Models\Tesoreria\Usuario','usuario_id','id_usuario');
    }
    public function estado(){
		return $this->belongsTo(PlanillaPagosEstados::class,'estado_id');
    }

}
