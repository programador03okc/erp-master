<?php

namespace App\Models\mgcp\AcuerdoMarco\Producto;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model {

    protected $table = 'mgcp_acuerdo_marco.categorias';
    public $timestamps = false;

    public function catalogo() {
        return $this->hasOne(Catalogo::class,'id','id_catalogo');
    }
}
