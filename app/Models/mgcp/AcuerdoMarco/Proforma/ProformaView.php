<?php

namespace App\Models\mgcp\AcuerdoMarco\Proforma;


use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProformaView extends Model
{
    protected $table = 'mgcp_acuerdo_marco.proformas_view';
    public $timestamps = false;

    /*public function getFechaEmisionAttribute()
    {
        return $this->attributes['fecha_emision'] == null ? '' : date_format(date_create($this->attributes['fecha_emision']), 'd-m-Y');
    }*/

    /*public function getInicioEntregaAttribute()
    {
        return $this->attributes['inicio_entrega'] == null ? '' : date_format(date_create($this->attributes['inicio_entrega']), 'd-m-Y');
    }

    public function getFinEntregaAttribute()
    {
        return $this->attributes['fin_entrega'] == null ? '' : date_format(date_create($this->attributes['fin_entrega']), 'd-m-Y');
    }

    public function getFechaLimiteAttribute()
    {
        return $this->attributes['fecha_limite'] == null ? '' : date_format(date_create($this->attributes['fecha_limite']), 'd-m-Y');
    }*/

    public function getSoftwareEducativoAttribute()
    {
        if ($this->attributes['software_educativo']) {
            return 'SÍ';
        } else {
            return 'NO';
        }
    }
}
