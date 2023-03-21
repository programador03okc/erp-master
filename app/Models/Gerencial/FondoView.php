<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class FondoView extends Model
{
    protected $table = 'cobranza.rc_fondos_view';
    protected $fillable = [
        "tipo_gestion", "tipo_negocio", "fecha_solicitud", "fecha_inicio", "fecha_vencimiento", "razon_social", "periodo", "forma_pago", "usuario_responsable", "detalles", 
        "claim", "pagador", "nro_documento", "fecha_cobranza", "observaciones", "estado", "estado_reporte_id", "importe", "moneda", "tipo_cambio", "importe_soles", "importe_dolares"
    ];
    public $timestamps = false;
}
