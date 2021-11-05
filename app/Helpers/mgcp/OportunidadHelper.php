<?php


namespace App\Helpers\mgcp;


use App\Models\mgcp\Oportunidad\Oportunidad;
use App\Models\mgcp\Oportunidad\Status;
use Carbon\Carbon;

class OportunidadHelper
{
    public static function crearDesdeOcPropia($descripcion, $responsable, &$ordenCompra)
    {
        $fecha=new Carbon;
        $oportunidad = new Oportunidad;
        $oportunidad->codigo_oportunidad = Oportunidad::crearCodigo();
        $oportunidad->id_entidad = $ordenCompra->id_entidad;
        $oportunidad->oportunidad = $descripcion;
        $oportunidad->probabilidad = 'alta';
        $oportunidad->fecha_limite = $ordenCompra->fecha_entrega_format;//$fecha->format('d-m-Y');
        $oportunidad->moneda = $ordenCompra->moneda_oc;
        $oportunidad->importe = str_replace(array('S/',' ',','), '',$ordenCompra->monto_total);
        $oportunidad->margen = 0;
        $oportunidad->eliminado = 0;
        $oportunidad->id_grupo = 1;
        $oportunidad->id_tipo_negocio = $ordenCompra->tipo=='directa' ? 5 : 1;
        $oportunidad->nombre_contacto = '';
        $oportunidad->telefono_contacto = '';
        $oportunidad->correo_contacto = '';
        $oportunidad->cargo_contacto = '';
        $oportunidad->reportado_por = '';
        $oportunidad->id_estado = 4;
        $oportunidad->id_responsable = $responsable;
        $oportunidad->save();
        $detalle = new Status;
        $detalle->detalle = 'Oportunidad ganada, se generó la orden ' . ($ordenCompra->orden_am ?? $ordenCompra->nro_orden);
        $detalle->id_oportunidad = $oportunidad->id;
        $detalle->id_estado = 4;
        $detalle->id_usuario = $responsable;
        $detalle->save();
        return $oportunidad;
    }
}
