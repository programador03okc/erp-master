<?php

namespace App\Models\mgcp\Oportunidad;
use Illuminate\Database\Eloquent\Model;

class StatusArchivo extends Model
{
    protected $table = 'mgcp_oportunidades.status_archivos';
    public $timestamps = false;
    
    public function status()
    {
        return $this->belongsTo(OportunidadStatus::class,'id','id_status');
    }
}
