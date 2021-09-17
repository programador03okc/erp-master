<?php

namespace App\Models\mgcp\OrdenCompra\Publica;

use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;
use Illuminate\Database\Eloquent\Model;

class OrdenCompraPublica extends Model {

    protected $table = 'mgcp_acuerdo_marco.oc_publicas';
    public $timestamps = false;
    protected $appends = ['fecha_formalizacion_format'];

    public function entidad() {
        return $this->hasOne(Entidad::class, 'id', 'id_entidad');
    }

    public function getFechaFormalizacionFormatAttribute() {
        if ($this->attributes['fecha_formalizacion'] == null) {
            return '';
        } else {
            return date_format(date_create($this->attributes['fecha_formalizacion']), 'd-m-Y');
        }
    }

    public function getPlazoEntregaAttribute()
    {
        return $this->attributes['plazo_entrega'] ?? '-';
    }
}
