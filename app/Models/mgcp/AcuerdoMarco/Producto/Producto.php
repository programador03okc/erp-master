<?php

namespace App\Models\mgcp\AcuerdoMarco\Producto;

use Illuminate\Database\Eloquent\Model;
use App\Models\mgcp\AcuerdoMarco\Producto\Categoria;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model {
    use HasFactory;
    protected $table = 'mgcp_acuerdo_marco.productos_am';
    public $timestamps = false;

    public function categoria() {
        return $this->hasOne(Categoria::class,'id','id_categoria');
    }

    public function getPrecioOkcAttribute($valor) {
        if ($valor == null) {
            return '-';
        } else {
            return ($this->attributes['moneda'] =='USD' ? '$' : 'S/') . number_format($valor, 2, '.', ',');
        }
    }

    public function getPrecioProyAttribute($valor) {
        if ($valor == null) {
            return '-';
        } else {
            return ($this->attributes['moneda'] =='USD' ? '$' : 'S/') . number_format($valor, 2, '.', ',');
        }
    }

    public function getPrecioSmartAttribute($valor) {
        if ($valor == null) {
            return '-';
        } else {
            return ($this->attributes['moneda'] =='USD' ? '$' : 'S/') . number_format($valor, 2, '.', ',');
        }
    }

    public function getPrecioDoradoAttribute($valor) {
        if ($valor == null) {
            return '-';
        } else {
            return ($this->attributes['moneda'] =='USD' ? '$' : 'S/') . number_format($valor, 2, '.', ',');
        }
    }

    public function getPrecioDezaAttribute($valor) {
        if ($valor == null) {
            return '-';
        } else {
            return ($this->attributes['moneda'] =='USD' ? '$' : 'S/') . number_format($valor, 2, '.', ',');
        }
    }

    public function getPrecioProtecAttribute($valor) {
        if ($valor == null) {
            return '-';
        } else {
            return ($this->attributes['moneda'] =='USD' ? '$' : 'S/') . number_format($valor, 2, '.', ',');
        }
    }
}
