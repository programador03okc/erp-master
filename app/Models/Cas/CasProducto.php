<?php

namespace App\Models\Cas;

use Illuminate\Database\Eloquent\Model;

class CasProducto extends Model
{
    //
    protected $table = 'cas.cas_producto';
    public $timestamps = false;
    protected $primaryKey = 'id_cas_producto';
}
