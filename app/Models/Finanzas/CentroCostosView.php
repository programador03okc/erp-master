<?php

namespace App\Models\Finanzas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CentroCostosView extends Model
{
    protected $table = 'finanzas.cc_niveles_view';
    protected $primaryKey = 'id_centro_costo';
    public $timestamps = false;
}
