<?php

namespace App\Models\mgcp\Oportunidad;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\User;

class Comentario extends Model {

    protected $table = 'mgcp_oportunidades.comentarios';
    protected $appends = ['autor'];
    
    public function getPublicadoPorAttribute() {
        if (is_numeric($this->autor)) {
            return User::find($this->autor)->name; 
        } else {
            return $this->autor;
        }
    }
    
    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('d-m-Y');
    }
    
    public function oportunidad() {
        return $this->belongsTo(Oportunidad::class, 'id_oportunidad');
    }
}
