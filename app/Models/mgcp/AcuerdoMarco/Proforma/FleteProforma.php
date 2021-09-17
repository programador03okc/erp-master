<?php

namespace App\Models\mgcp\AcuerdoMarco\Proforma;

//use App\AcuerdoMarco\Producto\DescuentoVolumen;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FleteProforma extends Model
{
    protected $table = 'mgcp_acuerdo_marco.fletes_proformas';
    public $timestamps = false;
}
