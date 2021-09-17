<?php

namespace App\Models\mgcp\AcuerdoMarco\Proforma;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MontoMinimoAtencion extends Model
{
    use HasFactory;
    protected $table = 'mgcp_acuerdo_marco.montos_minimos_atencion';
    protected $primaryKey = 'id_catalogo';
    public $incrementing = false;
}
