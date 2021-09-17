<?php

namespace App\Models\mgcp\Oportunidad;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Jenssegers\Date\Date;

class Actividad extends Model
{
    protected $table = 'mgcp_oportunidades.actividades';

    public function usuario()
    {
        return $this->belongsTo(User::class,'autor');
    }

    public function setUsuarioAttribute($valor)
    {
        $this->attributes['usuario']=strtoupper($valor);
    }

    public function getFechaInicioAttribute($date)
    {
        return Carbon::parse($date)->format('d-m-Y');
    }

    public function getFechaFinAttribute($date)
    {
        return Carbon::parse($date)->format('d-m-Y');
    }

    public function getFechaCreacionAttribute($date)
    {
        return Carbon::parse($date)->format('d-m-Y');
    }

    public function setFechaInicioAttribute($valor) {
        $this->attributes['fecha_inicio']=Carbon::createFromFormat('d-m-Y', $valor)->toDateString();
    }

    public function setFechaFinAttribute($valor) {
        $this->attributes['fecha_fin']=Carbon::createFromFormat('d-m-Y', $valor)->toDateString();
    }

    public function getFechaHumansAttribute() {
        //Date::setLocale('es');
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['fecha_creacion'])->diffForHumans(new Carbon());
    }

    public function archivos()
    {
        return $this->hasMany(ActividadArchivo::class,'id_actividad');
    }
}
