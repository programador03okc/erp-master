<?php

namespace App\Models\mgcp\Oportunidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoNegocio extends Model
{
    protected $table = 'mgcp_oportunidades.tipos_negocio';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
