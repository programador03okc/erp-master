<?php


namespace App\Models\mgcp\AcuerdoMarco\Proforma\Paquete;

use Illuminate\Database\Eloquent\Model;

class ProductoDetalle extends Model
{
    protected $table = 'mgcp_acuerdo_marco.proforma_paquete_producto_detalles';
    //protected $primaryKey = 'nro_proforma';
    //public $incrementing = false;
    public $timestamps = false;

    public function setPrecioPublicarAttribute($value)
    {
        $this->attributes['precio_publicar'] = $value == null ? null : str_replace(',', '', $value);
    }
}
