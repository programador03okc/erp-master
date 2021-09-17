<?php

namespace App\Models\mgcp\Oportunidad;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Status extends Model {

    protected $table = 'mgcp_oportunidades.status';

    public function getCreatedAtAttribute($date) {
        return Carbon::parse($date)->format('d-m-Y');
    }

    public function getUpdatedAtAttribute($date) {
        return Carbon::parse($date)->format('d-m-Y');
    }

    public function oportunidad() {
        return $this->belongsTo(Oportunidad::class, 'id_oportunidad');
    }

    public function estado() {
        return $this->belongsTo(Estado::class, 'id_estado');
    }

    public function usuario() {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function archivos() {
        return $this->hasMany(StatusArchivo::class, 'id_status');
    }

}
