<?php

namespace App\Models\mgcp\Oportunidad;
use Illuminate\Database\Eloquent\Model;

class ActividadArchivo extends Model
{
    protected $table = 'mgcp_oportunidades.actividades_archivos';
    public $timestamps = false;
    
    public function actividad()
    {
        return $this->belongsTo(Actividad::class,'id_actividad','id');
    }
}
