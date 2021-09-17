<?php

namespace App\Models\mgcp\OrdenCompra\Propia;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model {

    protected $table = 'mgcp_acuerdo_marco.oc_propias_estados';
    public $timestamps = false;

    /*public function usuario() {
        return $this->belongsTo('App\User', 'id_usuario');
    }

    public function ordenCompra() {
        return $this->belongsTo('App\mgcp\AcuerdoMarco\OrdenCompra\Propia\OrdenCompraPropia', 'id_oc');
    }
    
    public function getFechaAttribute() {
        return date_format(date_create($this->attributes['fecha']), 'd-m-Y g:i A');
    }*/
}
