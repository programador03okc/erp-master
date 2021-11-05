<?php


namespace App\Helpers\mgcp\CuadroCosto;


use App\Models\mgcp\CuadroCosto\CcAm;
use App\Models\mgcp\CuadroCosto\CcAmFila;
use App\Models\mgcp\CuadroCosto\CcBs;
use App\Models\mgcp\CuadroCosto\CcGg;
use App\Models\mgcp\CuadroCosto\CcVenta;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use App\Models\mgcp\CuadroCosto\TipoCambio;
use App\Models\mgcp\OrdenCompra\Propia\OrdenCompraPropiaView;
use App\Models\Presupuestos\CentroCostoNivelView;
use Carbon\Carbon;

class CuadroCostoHelper
{
    const CENTRO_COSTO_CO = '02.03.01.01'; //Compra ordinaria
    const CENTRO_COSTO_GC = '02.03.01.02'; //Gran compra

    public static function crearDesdeOportunidad($oportunidad)
    {
        $cuadroCosto = CuadroCosto::where('id_oportunidad', $oportunidad->id)->first();
        if ($cuadroCosto == null) {
            $tipoCambio = TipoCambio::find(1);
            $cuadroCosto = new CuadroCosto;
            $cuadroCosto->id_oportunidad = $oportunidad->id;
            $cuadroCosto->tipo_cambio = $tipoCambio->tipo_cambio;
            $cuadroCosto->igv = 18;
            $cuadroCosto->estado_aprobacion = 1;
            $orden = OrdenCompraPropiaView::where('id_oportunidad', $oportunidad->id)->first();
            $cuadroCosto->tipo_cuadro = 0; //YA NO SE USA $orden==null ? 0 : ($orden->tipo=='directa' ? 0 : 1);
            $cuadroCosto->porcentaje_responsable = 100;
            $cuadroCosto->moneda = $orden == null ? 's' : $orden->moneda_oc;
            /*$cuadroCosto->tipo_cambio_sbs = 3.4;
            $cuadroCosto->id_condicion_pago = 1;
            $cuadroCosto->prioridad = 1;
            $cuadroCosto->periodo_afecto = date('n');
            $cuadroCosto->id_detalle_proceso = 1;
            $cuadroCosto->fecha_entrega = $oportunidad->fecha_limite;
            $cuadroCosto->mejor_fecha_entrega = $oportunidad->fecha_limite;
            $cuadroCosto->ganancia_servicio = 0;*/
            $cuadroCosto->fecha_creacion = new Carbon();
            $cuadroCosto->id_condicion_credito = 2; //Crédito
            $cuadroCosto->dato_credito = 30; //30 días
            if ($orden != null) {
                switch ($orden->id_tipo) {
                    case 1:
                        $centroCosto = CentroCostoNivelView::where('codigo', self::CENTRO_COSTO_CO)->first();
                        break;
                    case 2:
                        $centroCosto = CentroCostoNivelView::where('codigo', self::CENTRO_COSTO_GC)->first();
                        break;
                    default:
                        $centroCosto = null;
                        break;
                }
                if ($centroCosto!=null)
                {
                    $cuadroCosto->id_centro_costo=$centroCosto->id_centro_costo;
                }
            }
            $cuadroCosto->save();
            /*$ccVenta = new CcVenta;
            $ccVenta->id_cc = $cuadroCosto->id;
            $ccVenta->margen_preferencial = 15;
            $ccVenta->fecha_entrega = $oportunidad->fecha_limite;
            $ccVenta->save();*/
            $ccAm = new CcAm;
            $ccAm->id_cc = $cuadroCosto->id;
            $ccAm->moneda_pvu = $cuadroCosto->moneda;
            /*$ccAm->id_categoria_gasto = 1;
            $ccAm->plazo_entrega = 0;*/
            $ccAm->save();
            $ccBs = new CcBs;
            $ccBs->id_cc = $cuadroCosto->id;
            $ccBs->save();
            $ccGg = new CcGg;
            $ccGg->id_cc = $cuadroCosto->id;
            $ccGg->save();
        }
        return $cuadroCosto;
    }

    public static function obtenerDetallesFilas($idCuadro)
    {
        $cuadroCosto = CuadroCosto::find($idCuadro);
        $monedaCuadro = $cuadroCosto->moneda == 's' ? 'S/' : '$';
        $contenedor = new \stdClass();
        $contenedor->id_oportunidad = $cuadroCosto->id_oportunidad;
        $contenedor->tipo_cuadro = $cuadroCosto->tipo_cuadro;
        $contenedor->moneda = $monedaCuadro;
        $contenedor->condicion_credito = $cuadroCosto->condicion_credito_format;
        $contenedor->tipo_cambio = $cuadroCosto->tipo_cambio;
        $contenedor->cuadroAm = CcAm::obtenerDetallesFilas($cuadroCosto);
        $contenedor->cuadroBs = CcBs::obtenerDetallesFilas($cuadroCosto);
        $contenedor->cuadroGg = CcGg::obtenerDetallesFilas($cuadroCosto);
        $contenedor->cuadroAm->ganancia_real = $contenedor->cuadroAm->suma_ganancia - $contenedor->cuadroGg->suma_total - $contenedor->cuadroBs->suma_costo_compra_mas_flete;
        $contenedor->cuadroAm->ganancia_real_format = $monedaCuadro . number_format($contenedor->cuadroAm->ganancia_real, 2);
        $contenedor->cuadroAm->margen_ganancia = $contenedor->cuadroAm->suma_monto_adjudicado == 0 ? 0 : (($contenedor->cuadroAm->ganancia_real * 100) / $contenedor->cuadroAm->suma_monto_adjudicado);
        $contenedor->cuadroAm->margen_ganancia_format = number_format($contenedor->cuadroAm->margen_ganancia, 2) . '%';
        $contenedor->cuadroAm->monto_adjudicado_mas_igv_format = $monedaCuadro . number_format(1 * ($contenedor->cuadroAm->suma_monto_adjudicado * ((100 + $cuadroCosto->igv) / 100)), 2);
        //Montos de acuerdo al tipo de cuadro elegido
        $contenedor->flete_total = $contenedor->cuadroAm->suma_total_flete + $contenedor->cuadroBs->suma_total_flete;
        $contenedor->flete_total_format = 'S/' . number_format($contenedor->flete_total, 2);
        $contenedor->costo_total_format = $contenedor->cuadroAm->suma_costo_compra_mas_flete_format;
        $contenedor->precio_venta_total = $contenedor->cuadroAm->suma_monto_adjudicado;
        $contenedor->precio_venta_total_format = $contenedor->cuadroAm->suma_monto_adjudicado_format;
        $contenedor->ganancia_real = $contenedor->cuadroAm->ganancia_real;
        $contenedor->ganancia_real_format = $contenedor->cuadroAm->ganancia_real_format;
        $contenedor->margen_ganancia = $contenedor->cuadroAm->margen_ganancia;
        $contenedor->margen_ganancia_format = $contenedor->cuadroAm->margen_ganancia_format;
        return $contenedor;
    }
}
