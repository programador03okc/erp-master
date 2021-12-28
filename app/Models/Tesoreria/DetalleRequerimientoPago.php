<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetalleRequerimientoPago extends Model
{
    protected $table = 'tesoreria.detalle_requerimiento_pago';
    protected $primaryKey = 'id_detalle_requerimiento_pago';
    public $timestamps = false;
 

    public function producto(){
        return $this->hasone('App\Models\Almacen\Producto','id_producto','id_producto');
    }
    public function unidadMedida(){
        return $this->hasone('App\Models\Almacen\UnidadMedida','id_unidad_medida','id_unidad_medida');
    }
    public function estado(){
        return $this->hasone('App\Models\Administracion\Estado','id_estado_doc','estado');
    }
}

