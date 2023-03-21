<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class CobranzaView extends Model
{
    protected $table = 'cobranza.rc_ventas_view';
    protected $fillable = [
        "empresa", "sector", "cliente", "cliente_ruc", "categoria", "plazo_credito", "uu_ee", "fuente_financ", "factura", "cdp", "siaf", "oc_fisica", "ocam", "id_oc", 
        "periodo", "fecha_emision", "fecha_recepcion", "inicio_entrega", "fecha_entrega", "estado_cobranza", "estado", "estado_reporte_id", "tipo_tramite", "area", 
        "usuario_responsable", "fase", "tiene_penalidad", "importe", "moneda", "tipo_cambio", "importe_soles", "importe_dolares"
    ];
    public $timestamps = false;
    protected $appends = ['monto_penalidad', 'monto_retencion', 'monto_detraccion', 'programacion_pago'];

    public function getMontoPenalidadAttribute()
    {
        $penalidad = Penalidad::where('id_registro_cobranza', $this->attributes['id'])->where('tipo', 'PENALIDAD')->orderBy('id_penalidad', 'desc')->first();
        return ($penalidad) ? $penalidad->monto : 0 ;
    }

    public function getMontoRetencionAttribute()
    {
        $retencion = Penalidad::where('id_registro_cobranza', $this->attributes['id'])->where('tipo', 'RETENCION')->orderBy('id_penalidad', 'desc')->first();
        return ($retencion) ? $retencion->monto : 0 ;
    }

    public function getMontoDetraccionAttribute()
    {
        $detraccion = Penalidad::where('id_registro_cobranza', $this->attributes['id'])->where('tipo', 'DETRACCION')->orderBy('id_penalidad', 'desc')->first();
        return ($detraccion) ? $detraccion->monto : 0 ;
    }

    public function getProgramacionPagoAttribute()
    {
        $pago = ProgramacionPago::where('id_registro_cobranza', $this->attributes['id'])->orderBy('id_programacion_pago', 'desc')->first();
        return ($pago) ? $pago->fecha : 0 ;
    }
}
