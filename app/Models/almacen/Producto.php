<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'almacen.alm_prod';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;

    public function subcategoria(){
        return $this->hasOne('App\Models\Almacen\Catalogo\SubCategoria','id_subcategoria','id_subcategoria');
    }
    public function marca(){
        return $this->hasOne('App\Models\Almacen\Catalogo\Marca','id_subcategoria','id_subcategoria');
    }
    public function moneda(){
        return $this->hasOne('App\Models\Configuracion\Moneda','id_moneda','id_moneda');
    }
    public function unidadMedida()
    {
        return $this->hasone('App\Models\Almacen\UnidadMedida', 'id_unidad_medida', 'id_unidad_medida');
    }
}