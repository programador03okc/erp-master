<?php

namespace App\Models\mgcp\OrdenCompra\Propia\AcuerdoMarco;

use Illuminate\Database\Eloquent\Model;

class FechaDescargaOcAm extends Model {

    protected $table = 'mgcp_acuerdo_marco.fechas_descarga_oc_propias';
    protected $primaryKey = 'id_empresa';
    public $timestamps = false;
    public $incrementing = false;
}
