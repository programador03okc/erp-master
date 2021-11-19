<?php

namespace App\Models\mgcp\AcuerdoMarco\Producto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NroParteIgnorado extends Model
{
    // use HasFactory;
    protected $table = 'mgcp_acuerdo_marco.nros_parte_ignorados';
    protected $primaryKey = 'part_no';
    public $incrementing = false;
    public $timestamps = false;
}
