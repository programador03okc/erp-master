<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class SolicitudSeguimiento extends Model
{
    //
    protected $table = 'finanzas.solicitudes_seguimiento';

    public $timestamps = false;

    protected $fillable = [
        'observacion',
        'solicitud_id',
        'estado_id',
        'usuario_id',
        'fecha'
    ];

    public function solicitud(){
        return $this->belongsTo(Solicitud::class, 'solicitud_id', 'id');
    }

    public function usuario(){
        return $this->belongsTo('App\Models\Tesoreria\Usuario','usuario_id','id_usuario');
    }
    public function estado(){
        return $this->belongsTo('App\Models\Tesoreria\Estado','estado_id','id_estado_doc');
    }

}
