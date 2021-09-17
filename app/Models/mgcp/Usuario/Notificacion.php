<?php

namespace App\Models\mgcp\Usuario;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notificacion extends Model {
    
    protected $table = 'mgcp_usuarios.notificaciones';
    public $timestamps = false;
    
    public function getFechaAttribute($date)
    {
        return Carbon::parse($date)->format('d-m-Y H:m:s');
    }
}
