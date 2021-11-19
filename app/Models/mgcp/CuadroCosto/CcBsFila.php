<?php

namespace App\Models\mgcp\CuadroCosto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CcBsFila extends Model
{
    // use HasFactory;
    protected $table = 'mgcp_cuadro_costos.cc_bs_filas';
    public $timestamps = false;

    public function bsProveedor()
    {
        return $this->hasOne(CcBsProveedor::class, 'id', 'proveedor_seleccionado');
    }
}
