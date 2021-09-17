<?php

namespace App\Models\mgcp\AcuerdoMarco;

use Illuminate\Database\Eloquent\Model;

class EmpresaAcuerdo extends Model
{
    protected $table = 'mgcp_acuerdo_marco.empresas_acuerdos';
    public $timestamps = false;

    public function empresa() {
        return $this->hasOne(Empresa::class);
    }

    public function acuerdo() {
        return $this->hasOne(AcuerdoMarco::class);
    }
}
