<?php

namespace App\Models\mgcp\OrdenCompra\Publica;

use App\Models\mgcp\AcuerdoMarco\TcSbs;
use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use Illuminate\Database\Eloquent\Model;

class OrdenCompraPublicaDetalle extends Model
{

    protected $table = 'mgcp_acuerdo_marco.oc_publica_detalles';
    public $timestamps = false;
    protected $appends = ['precio_unitario_format', 'costo_envio_format', 'precio_unitario_usd_format'];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function ordenCompraPublica()
    {
        return $this->belongsTo(OrdenCompraPublica::class, 'id_orden_compra');
    }

    public function getPrecioUnitarioFormatAttribute()
    {
        /*if (isset($this->attributes['precio_unitario'])) {
            
        }*/
        return 'S/' . number_format($this->attributes['precio_unitario'], 2);
    }

    public function getPrecioUnitarioUsdFormatAttribute()
    {
        $oc = OrdenCompraPublica::find($this->attributes['id_orden_compra']);
        $ultimaTasa = $tasa = TcSbs::orderBy('fecha', 'desc')->first()->precio;
        if ($oc->fecha_formalizacion == null) {
            $tasa = $ultimaTasa;
        } else {
            $tasFecha = TcSbs::where('fecha', $oc->fecha_formalizacion)->first();
            $tasa = $tasFecha == null ? $ultimaTasa : $tasFecha->precio;
        }
        return '$' . number_format($this->attributes['precio_unitario'] / $tasa, 2);
        /*if (isset($this->attributes['precio_unitario'])) {
            return number_format($this->attributes['precio_unitario'],2);
        }*/
    }

    public function getCostoEnvioFormatAttribute()
    {
        /*if (isset($this->attributes['costo_envio'])) {
            
        }*/
        return 'S/' . number_format($this->attributes['costo_envio'], 2);
    }
}
