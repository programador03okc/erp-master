<?php

namespace App\Models\mgcp\OrdenCompra\Propia\AcuerdoMarco;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenCompraAm extends Model {
    use HasFactory;
    protected $table = 'mgcp_acuerdo_marco.oc_propias';
    public $timestamps = false;

    /*public function usuario() {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function ordenCompra() {
        return $this->belongsTo(OrdenCompraPropia::class, 'id_oc');
    }
    
    public function getFechaAttribute() {
        return date_format(date_create($this->attributes['fecha']), 'd-m-Y g:i A');
    }*/
}
