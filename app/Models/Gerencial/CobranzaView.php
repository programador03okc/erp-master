<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class CobranzaView extends Model
{
    protected $table = 'cobranza.rc_ventas_view';
    protected $fillable = [
        "empresa", "sector", "cliente", "cliente_ruc", "categoria", "plazo_credito", "uu_ee", "fuente_financ", "factura", "cdp", "siaf", "oc_fisica", "ocam", "id_oc", 
        "periodo", "fecha_emision", "fecha_recepcion", "inicio_entrega", "fecha_entrega", "estado_cobranza", "estado", "estado_reporte_id", "tipo_tramite", "area", 
        "usuario_responsable", "fase", "tiene_penalidad", "tiene_retencion", "tiene_detraccion", "importe", "moneda", "tipo_cambio", "importe_soles", "importe_dolares"
    ];
    public $timestamps = false;
    protected $appends = ['monto_penalidad', 'monto_retencion', 'monto_detraccion', 'programacion_pago'];

    public function getMontoPenalidadAttribute()
    {
        $monto = 0;
        if ($this->attributes['tiene_penalidad']) {
            $penalidad = Penalidad::where('id_registro_cobranza', $this->attributes['id'])->where('tipo', 'PENALIDAD')->orderBy('id_penalidad', 'desc')->first();
            $monto = ($penalidad) ? $penalidad->monto : 0 ;
        }
        return $monto;
    }

    public function getMontoRetencionAttribute()
    {
        $monto = 0;
        if ($this->attributes['tiene_retencion']) {
            $retencion = Penalidad::where('id_registro_cobranza', $this->attributes['id'])->where('tipo', 'RETENCION')->orderBy('id_penalidad', 'desc')->first();
            $monto = ($retencion) ? $retencion->monto : 0 ;
        }
        return $monto;
    }

    public function getMontoDetraccionAttribute()
    {
        $monto = 0;
        if ($this->attributes['tiene_retencion']) {
            $detraccion = Penalidad::where('id_registro_cobranza', $this->attributes['id'])->where('tipo', 'DETRACCION')->orderBy('id_penalidad', 'desc')->first();
            $monto = ($detraccion) ? $detraccion->monto : 0 ;
        }
        return $monto;
    }

    public function getProgramacionPagoAttribute()
    {
        $pago = ProgramacionPago::where('id_registro_cobranza', $this->attributes['id'])->orderBy('id_programacion_pago', 'desc')->first();
        return ($pago) ? $pago->fecha : 0 ;
    }
}
