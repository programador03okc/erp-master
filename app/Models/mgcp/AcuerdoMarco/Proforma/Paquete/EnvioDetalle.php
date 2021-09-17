<?php


namespace App\Models\mgcp\AcuerdoMarco\Proforma\Paquete;

use Illuminate\Database\Eloquent\Model;

class EnvioDetalle extends Model
{
    protected $table = 'mgcp_acuerdo_marco.proforma_paquete_envio_detalles';
    //protected $primaryKey = 'nro_detalle_entrega';
    //public $incrementing = false;
    public $timestamps = false;

    public function setCostoEnvioPublicarAttribute($value)
    {
        $this->attributes['costo_envio_publicar'] = $value == null ? null : str_replace(',', '', $value);
    }
}
