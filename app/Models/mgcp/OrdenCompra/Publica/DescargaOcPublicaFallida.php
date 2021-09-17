<?php

namespace App\Models\mgcp\OrdenCompra\Publica;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DescargaOcPublicaFallida extends Model
{
    use HasFactory;
    protected $table = 'mgcp_acuerdo_marco.descarga_oc_publica_fallidas';
    public $timestamps = false;
    protected $primaryKey = 'id_oc';
}
