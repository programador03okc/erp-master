<?php

namespace App\Models\mgcp\AcuerdoMarco\Proforma;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ComentarioCompraOrdinaria extends Model
{
    protected $table = 'mgcp_acuerdo_marco.comentarios_proforma_co';
    public $timestamps = false;

    public function usuario() {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function getFechaAttribute() {
        return date_format(date_create($this->attributes['fecha']), 'd-m-Y g:i A');
    }
}
