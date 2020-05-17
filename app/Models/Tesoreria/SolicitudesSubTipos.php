<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class SolicitudesSubTipos extends Model
{
    //
    protected $table = 'finanzas.solicitudes_subtipos';

    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'descripcion',
        'solicitudes_tipos_id'
    ];
    public function tipo(){
        return $this->belongsTo('App\Models\Tesoreria\SolicitudesTipos','solicitudes_tipos_id','id');
    }

    public function solicitudes(){
        return $this->hasMany('App\Models\Tesoreria\Solicitud','id');
    }
}
