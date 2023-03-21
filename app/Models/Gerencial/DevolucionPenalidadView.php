<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class DevolucionPenalidadView extends Model
{
    protected $table = 'cobranza.rc_penalidades_view';
    protected $fillable = [
        "id_registro_cobranza", "cliente", "uu_ee", "fuente_financ", "factura", "cdp", "oc_fisica", "ocam", "siaf", "usuario_responsable", "doc_penalidad", "fecha_penalidad", "estado", "estado_reporte_id", 
        "fecha_cobro_penalidad", "gestion", "doc_devolucion_penalidad", "pagador", "motivo_devolucion", "importe_devolucion", "importe_penalidad", "moneda", "tipo_cambio", "importe_soles", "importe_dolares"
    ];
    public $timestamps = false;
}
