<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class SolicitudDetalle extends Model
{
    //
    protected $table = 'finanzas.solicitudes_detalles';

    public $timestamps = false;

    protected $fillable = [
        'descripcion',
		'estimado',
        'solicitud_id',
        'partida_id'
    ];

    public function solicitud(){
        return $this->belongsTo(Solicitud::class, 'solicitud_id', 'id');
    }
    public function partida(){
        return $this->belongsTo(PresupuestoPartida::class, 'partida_id', 'id_partida');
    }

}
