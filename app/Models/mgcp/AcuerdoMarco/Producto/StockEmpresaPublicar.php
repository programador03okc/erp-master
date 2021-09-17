<?php

namespace App\Models\mgcp\AcuerdoMarco\Producto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEmpresaPublicar extends Model
{
    use HasFactory;
    protected $table = 'mgcp_acuerdo_marco.stock_empresas_publicar';
    public $timestamps = false;

    public function setStockAttribute($value) {
        $this->attributes['stock']=intval($value);
    }
}
