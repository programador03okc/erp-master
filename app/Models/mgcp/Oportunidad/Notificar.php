<?php

namespace App\Models\mgcp\Oportunidad;


use Illuminate\Database\Eloquent\Model;

class Notificar extends Model
{
    protected $table = 'mgcp_oportunidades.notificar';
    public $timestamps = false;
    
    
    /*public function user()
    {
        return $this->belongsTo('App\User','autor');
    }
    public function actividadarchivo()
    {
        return $this->hasMany('App\Actividadarchivo','id_actividad','id');
    }
    public function setFechaInicioAttribute($valor) {
        $this->attributes['fecha_inicio']=Carbon::createFromFormat('d-m-Y', $valor)->toDateString();
    }
    public function setFechaFinAttribute($valor) {
        $this->attributes['fecha_fin']=Carbon::createFromFormat('d-m-Y', $valor)->toDateString();
    }*/
    
}
