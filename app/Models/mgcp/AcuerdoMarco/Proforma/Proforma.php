<?php

namespace App\Models\mgcp\AcuerdoMarco\Proforma;

//use App\AcuerdoMarco\Producto\DescuentoVolumen;

use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;
use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use App\Models\mgcp\CuadroCosto\TipoCambio;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Proforma extends Model
{
    public $timestamps = false;
    protected $appends = ['empresa_dcto_volumen', 'producto_tiene_historial_actualizaciones', 'puede_deshacer_cotizacion'];

    public function setCostoEnvioPublicarAttribute($value)
    {
        switch ($value) {
            case '0':
                $this->attributes['costo_envio_publicar'] = 0.00;
                break;
            default:
                $this->attributes['costo_envio_publicar'] = $value == '' ? null : str_replace(',', '', $value);
                break;
        }
    }

    public function setPrecioPublicarAttribute($value)
    {
        $this->attributes['precio_publicar'] = $value == '' ? null : str_replace(',', '', $value);
    }

    public function getFechaEmisionAttribute()
    {
        return $this->attributes['fecha_emision'] == null ? '' : date_format(date_create($this->attributes['fecha_emision']), 'd-m-Y');
    }

    public function getInicioEntregaAttribute()
    {
        return $this->attributes['inicio_entrega'] == null ? '' : date_format(date_create($this->attributes['inicio_entrega']), 'd-m-Y');
    }

    public function getFinEntregaAttribute()
    {
        return $this->attributes['fin_entrega'] == null ? '' : date_format(date_create($this->attributes['fin_entrega']), 'd-m-Y');
    }

    public function getFechaLimiteAttribute()
    {
        return $this->attributes['fecha_limite'] == null ? '' : date_format(date_create($this->attributes['fecha_limite']), 'd-m-Y');
    }

    public function getLugarEntregaAttribute()
    {
        return str_replace('/', '/ ', $this->attributes['lugar_entrega']);
    }

    public function getPuedeDeshacerCotizacionAttribute()
    {
        if ($this->attributes['fecha_limite'] == null) {
            return false;
        } else {
            $fechaActual = (new Carbon())->setTime(0, 0, 0);
            $fechaLimite = new Carbon($this->attributes['fecha_limite']);
            return ($fechaActual <= $fechaLimite && $this->attributes['estado'] == 'COTIZADA');
        }
    }

    public function getSoftwareEducativoAttribute()
    {
        if ($this->attributes['software_educativo']) {
            return '<span class="text-danger"><strong>SÍ</strong></span>';
        } else {
            return 'NO';
        }
    }

    /*public function getPuedeRestringirAttribute()
    {
        $tipoCambio = TipoCambio::first()->tipo_cambio;
        $monto = ($this->attributes['moneda_ofertada'] == 'USD' ? $tipoCambio : 1) * ($this->attributes['precio_unitario_base'] ?? 0) * $this->attributes['cantidad'];
        return ($this->attributes['estado']=='PENDIENTE' && MontoMinimoAtencion::where('id_catalogo', $this->producto->categoria->id_catalogo)->where('monto_minimo', '<=', $monto)->first()== null);
    }*/

    public function getPaqueteAttribute()
    {
        if ($this->attributes['paquete']) {
            return '<span class="text-danger"><strong>SÍ</strong></span>';
        } else {
            return 'NO';
        }
    }
    /*public function setLugarEntregaAttribute($value) {
    $this->attributes['lugar_entrega'] = str_replace(" /", " / ", str_replace("  ", "", $value));
    }*/

    public function getEmpresaDctoVolumenAttribute()
    {
        return 0;
    }

    /*public function getMontoDctoEmpresaAttribute() {

    }*/

    public function getProductoTieneHistorialActualizacionesAttribute()
    {
        return 1;
    }

    public function producto()
    {
        return $this->hasOne(Producto::class, 'id', 'id_producto');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_ultimo_usuario', 'id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id');
    }

    public function entidad()
    {
        return $this->belongsTo(Entidad::class, 'id_entidad', 'id');
    }
}
