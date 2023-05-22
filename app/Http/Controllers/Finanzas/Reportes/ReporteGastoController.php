<?php

namespace App\Http\Controllers\Finanzas\Reportes;

use App\Exports\ListaGastoDetalleCDPExport;
use App\Exports\ListaGastoDetalleRequerimientoLogisticoExport;
use App\Exports\ListaGastoDetalleRequerimientoPagoExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Comercial\CuadroCosto\CcAmFila;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

use Debugbar;
use Mockery\Undefined;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\TreasuryBill;
use PhpParser\Node\Stmt\TryCatch;

class ReporteGastoController extends Controller
{
    public function indexReporteGastoRequerimientoLogistico()
    {
        return view('finanzas/reportes/gasto_requerimiento_logistico');
    }

    public function indexReporteGastoRequerimientoPago()
    {
        return view('finanzas/reportes/gasto_requerimiento_pago');
    }

    public function indexReporteGastoCDP()
    {
        return view('finanzas/reportes/gasto_cdp');
    }


    public function dataGastoDetalleRequerimientoLogistico()
    {
        $detalleRequerimientoList = DB::table('almacen.alm_det_req')
            ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftJoin('finanzas.presupuesto_interno', 'presupuesto_interno.id_presupuesto_interno', '=', 'alm_req.id_presupuesto_interno')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
            ->leftJoin('configuracion.sis_moneda', 'alm_req.id_moneda', '=', 'sis_moneda.id_moneda')
            ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
            ->leftJoin('configuracion.sis_grupo', 'alm_req.id_grupo', '=', 'sis_grupo.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('administracion.division', 'division.id_division', '=', 'alm_req.division_id')
            ->leftJoin('proyectos.proy_proyecto', 'proy_proyecto.id_proyecto', '=', 'alm_req.id_proyecto')
            ->leftJoin('administracion.adm_empresa', 'alm_req.id_empresa', '=', 'adm_empresa.id_empresa')
            ->leftJoin('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftJoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('almacen.alm_tp_req', 'alm_tp_req.id_tipo_requerimiento', '=', 'alm_req.id_tipo_requerimiento')
            ->leftJoin('finanzas.presup_par', 'presup_par.id_partida', '=', 'alm_det_req.partida')
            ->leftJoin('finanzas.presup', 'presup.id_presup', '=', 'presup_par.id_presup')

            ->leftJoin('finanzas.centro_costo', 'centro_costo.id_centro_costo', '=', 'alm_det_req.centro_costo_id')
            ->leftJoin('finanzas.centro_costo as padre_centro_costo', 'padre_centro_costo.id_centro_costo', '=', 'centro_costo.id_padre')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_requerimiento','=','alm_det_req.id_detalle_requerimiento')
            ->leftJoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
            ->leftJoin('configuracion.sis_moneda as moneda_orden', 'moneda_orden.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->leftJoin('logistica.estados_compra', 'estados_compra.id_estado', '=', 'log_ord_compra.estado')
            ->leftJoin('tesoreria.requerimiento_pago_estado', 'requerimiento_pago_estado.id_requerimiento_pago_estado', '=', 'log_ord_compra.estado_pago')
            ->leftJoin('almacen.orden_despacho', 'orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento')
            ->leftJoin('administracion.adm_estado_doc as estado_despacho', 'estado_despacho.id_estado_doc', '=', 'alm_req.estado_despacho')


            ->select(
                'alm_prod.codigo as codigo_producto',
                'alm_prod.descripcion as descripcion_producto',
                'alm_det_req.descripcion as descripcion_detalle_requerimiento',
                'alm_det_req.motivo',
                'alm_det_req.cantidad',
                'alm_det_req.precio_unitario',
                'alm_det_req.subtotal',
                'alm_det_req.fecha_registro',
                'adm_prioridad.descripcion as prioridad',
                'alm_tp_req.descripcion AS tipo_requerimiento',
                'alm_req.codigo',
                'oportunidades.codigo_oportunidad',
                'alm_req.concepto',
                'alm_req.codigo',
                'alm_req.observacion',
                'sis_moneda.simbolo as simbolo_moneda',
                'sis_sede.codigo as sede',
                'sis_sede.descripcion as descripcion_empresa_sede',
                'adm_contri.razon_social as empresa_razon_social',
                'sis_identi.descripcion as empresa_tipo_documento',
                'proy_proyecto.descripcion AS descripcion_proyecto',
                'sis_grupo.descripcion as grupo',
                'division.descripcion as division',
                'alm_req.monto_total',
                'presup_par.descripcion as descripcion_partida',
                'presup_par.codigo as partida',
                'presup_par.id_partida',
                'padre_centro_costo.codigo as padre_centro_costo',
                'padre_centro_costo.descripcion as padre_descripcion_centro_costo',
                'centro_costo.descripcion as descripcion_centro_costo',
                'centro_costo.codigo as centro_costo',
                'centro_costo.id_centro_costo',
                'adm_estado_doc.estado_doc as estado_requerimiento',
                'presup.codigo as codigo_presupuesto_old',
                'presup.descripcion as descripcion_presupuesto_old',
                'presupuesto_interno.codigo as codigo_presupuesto_interno',
                'presupuesto_interno.descripcion as descripcion_presupuesto_interno',

                DB::raw("(SELECT presup_titu.descripcion
                FROM finanzas.presup_titu
                WHERE presup_titu.codigo = presup_par.cod_padre and presup_titu.id_presup=presup_par.id_presup limit 1) AS descripcion_partida_padre"),
                DB::raw("(SELECT presupuesto_interno_detalle.partida
                FROM finanzas.presupuesto_interno_detalle
                WHERE presupuesto_interno_detalle.id_presupuesto_interno_detalle = alm_det_req.id_partida_pi and alm_req.id_presupuesto_interno > 0 limit 1) AS codigo_sub_partida_presupuesto_interno"),
                DB::raw("(SELECT presupuesto_interno_detalle.descripcion
                FROM finanzas.presupuesto_interno_detalle
                WHERE presupuesto_interno_detalle.id_presupuesto_interno_detalle = alm_det_req.id_partida_pi and alm_req.id_presupuesto_interno > 0 limit 1) AS descripcion_sub_partida_presupuesto_interno"),
                DB::raw("(SELECT presupuesto_interno_modelo.descripcion
                FROM finanzas.presupuesto_interno_detalle
                inner join finanzas.presupuesto_interno_modelo on presupuesto_interno_modelo.id_modelo_presupuesto_interno = presupuesto_interno_detalle.id_padre
                WHERE presupuesto_interno_detalle.id_presupuesto_interno_detalle = alm_det_req.id_partida_pi and alm_req.id_presupuesto_interno > 0 limit 1) AS descripcion_partida_presupuesto_interno"),
                
                'log_ord_compra.codigo as nro_orden',
                'estados_compra.descripcion as estado_orden',
                'requerimiento_pago_estado.descripcion as estado_pago',
                'moneda_orden.simbolo as simbolo_moneda_orden',
                'log_det_ord_compra.cantidad as cantidad_orden',
                'log_det_ord_compra.precio as precio_orden',
                DB::raw("(SELECT log_det_ord_compra.subtotal  
                FROM logistica.log_ord_compra
                WHERE log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra 
                and log_det_ord_compra.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento limit 1) AS subtotal_orden"),
                DB::raw("(SELECT CASE WHEN 
                log_ord_compra.incluye_igv =true THEN (log_det_ord_compra.subtotal * 1.18 ) ELSE log_det_ord_compra.subtotal END  
                FROM logistica.log_ord_compra
                WHERE log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra 
                and log_det_ord_compra.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento limit 1) AS subtotal_orden_considera_igv"),

                'alm_req.fecha_requerimiento',

                DB::raw("(SELECT cont_tp_cambio.venta  
                FROM contabilidad.cont_tp_cambio
                WHERE TO_DATE(to_char(cont_tp_cambio.fecha,'YYYY-MM-DD'),'YYYY-MM-DD') = TO_DATE(to_char(alm_req.fecha_requerimiento,'YYYY-MM-DD'),'YYYY-MM-DD') limit 1) AS tipo_cambio"),
 
                'estado_despacho.estado_doc as estado_despacho',

                DB::raw("(SELECT string_agg(DISTINCT mov_alm.codigo::text, ', '::text) AS string_agg
                FROM almacen.orden_despacho_det
                left join almacen.guia_ven_det on guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle 
                left join almacen.mov_alm_det on mov_alm_det.id_guia_ven_det = guia_ven_det.id_guia_ven_det 
                left join almacen.mov_alm on mov_alm.id_mov_alm = mov_alm_det.id_mov_alm 
                WHERE orden_despacho_det.id_od = orden_despacho.id_od 
                and alm_det_req.id_detalle_requerimiento = orden_despacho_det.id_detalle_requerimiento
                and orden_despacho.aplica_cambios = true ) AS nro_salida_int"),
                DB::raw("(SELECT string_agg(DISTINCT mov_alm.codigo::text, ', '::text) AS string_agg
                FROM almacen.orden_despacho_det
                left join almacen.guia_ven_det on guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle 
                left join almacen.mov_alm_det on mov_alm_det.id_guia_ven_det = guia_ven_det.id_guia_ven_det 
                left join almacen.mov_alm on mov_alm.id_mov_alm = mov_alm_det.id_mov_alm 
                WHERE orden_despacho_det.id_od = orden_despacho.id_od 
                and alm_det_req.id_detalle_requerimiento = orden_despacho_det.id_detalle_requerimiento
                and orden_despacho.aplica_cambios = false ) AS nro_salida_ext"),
                DB::raw("(SELECT alm_almacen.descripcion  
                FROM almacen.alm_almacen
                WHERE  alm_almacen.id_almacen = orden_despacho.id_almacen order by orden_despacho.fecha_registro desc limit 1) AS almacen_salida"),

                DB::raw("(SELECT guia_ven_det.fecha_registro
                FROM almacen.orden_despacho_det
                left join almacen.guia_ven_det on guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle 
                WHERE orden_despacho_det.id_od = orden_despacho.id_od 
                and alm_det_req.id_detalle_requerimiento = orden_despacho_det.id_detalle_requerimiento
                order by orden_despacho.fecha_registro desc limit 1) AS fecha_salida"),

                DB::raw("(SELECT alm_prod.codigo
                FROM almacen.orden_despacho_det
                left join almacen.guia_ven_det on guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle 
                left join almacen.mov_alm_det on mov_alm_det.id_guia_ven_det = guia_ven_det.id_guia_ven_det 
                left join almacen.alm_prod on alm_prod.id_producto = mov_alm_det.id_producto 
                WHERE orden_despacho_det.id_od = orden_despacho.id_od 
                and alm_det_req.id_detalle_requerimiento = orden_despacho_det.id_detalle_requerimiento
                order by orden_despacho.fecha_registro desc limit 1) AS codigo_producto_salida"),
                
                DB::raw("(SELECT mov_alm_det.cantidad
                FROM almacen.orden_despacho_det
                left join almacen.guia_ven_det on guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle 
                left join almacen.mov_alm_det on mov_alm_det.id_guia_ven_det = guia_ven_det.id_guia_ven_det 
                WHERE orden_despacho_det.id_od = orden_despacho.id_od 
                and alm_det_req.id_detalle_requerimiento = orden_despacho_det.id_detalle_requerimiento
                order by orden_despacho.fecha_registro desc limit 1) AS cantidad_salida"),

                DB::raw("(SELECT alm_und_medida.abreviatura
                FROM almacen.orden_despacho_det
                left join almacen.guia_ven_det on guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle 
                left join almacen.mov_alm_det on mov_alm_det.id_guia_ven_det = guia_ven_det.id_guia_ven_det 
                left join almacen.alm_prod on alm_prod.id_producto = mov_alm_det.id_producto 
                left join almacen.alm_und_medida on alm_und_medida.id_unidad_medida = alm_prod.id_unidad_medida 
                WHERE orden_despacho_det.id_od = orden_despacho.id_od 
                and alm_det_req.id_detalle_requerimiento = orden_despacho_det.id_detalle_requerimiento
                order by orden_despacho.fecha_registro desc limit 1) AS moneda_producto_salida"),

                DB::raw("(SELECT round((mov_alm_det.valorizacion::numeric / mov_alm_det.cantidad::numeric),2)
                FROM almacen.orden_despacho_det
                left join almacen.guia_ven_det on guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle 
                left join almacen.mov_alm_det on mov_alm_det.id_guia_ven_det = guia_ven_det.id_guia_ven_det                 
                WHERE orden_despacho_det.id_od = orden_despacho.id_od 
                and alm_det_req.id_detalle_requerimiento = orden_despacho_det.id_detalle_requerimiento
                order by orden_despacho.fecha_registro desc limit 1) AS costo_unitario_salida"),
                
                DB::raw("(SELECT round(mov_alm_det.valorizacion::numeric ,2)
                FROM almacen.orden_despacho_det
                left join almacen.guia_ven_det on guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle 
                left join almacen.mov_alm_det on mov_alm_det.id_guia_ven_det = guia_ven_det.id_guia_ven_det                 
                WHERE orden_despacho_det.id_od = orden_despacho.id_od 
                and alm_det_req.id_detalle_requerimiento = orden_despacho_det.id_detalle_requerimiento
                order by orden_despacho.fecha_registro desc limit 1) AS costo_total_salida"),

                
            )
            ->where([['alm_det_req.estado', '!=', 7], ['alm_req.estado', '!=', 7]])
            ->orderBy('alm_det_req.fecha_registro', 'desc')
            ->get();

        return $detalleRequerimientoList;
    }

    public function listaGastoDetalleRequerimientoLogistico()
    {
        $listado = DB::table('almacen.alm_det_req')
            ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftJoin('finanzas.presupuesto_interno', 'presupuesto_interno.id_presupuesto_interno', '=', 'alm_req.id_presupuesto_interno')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
            ->leftJoin('configuracion.sis_moneda', 'alm_req.id_moneda', '=', 'sis_moneda.id_moneda')
            ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
            ->leftJoin('configuracion.sis_grupo', 'alm_req.id_grupo', '=', 'sis_grupo.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('administracion.division', 'division.id_division', '=', 'alm_req.division_id')
            ->leftJoin('proyectos.proy_proyecto', 'proy_proyecto.id_proyecto', '=', 'alm_req.id_proyecto')
            ->leftJoin('administracion.adm_empresa', 'alm_req.id_empresa', '=', 'adm_empresa.id_empresa')
            ->leftJoin('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftJoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('almacen.alm_tp_req', 'alm_tp_req.id_tipo_requerimiento', '=', 'alm_req.id_tipo_requerimiento')
            ->leftJoin('finanzas.presup_par', 'presup_par.id_partida', '=', 'alm_det_req.partida')
            ->leftJoin('finanzas.presup', 'presup.id_presup', '=', 'presup_par.id_presup')

            ->leftJoin('finanzas.centro_costo', 'centro_costo.id_centro_costo', '=', 'alm_det_req.centro_costo_id')
            ->leftJoin('finanzas.centro_costo as padre_centro_costo', 'padre_centro_costo.id_centro_costo', '=', 'centro_costo.id_padre')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_requerimiento','=','alm_det_req.id_detalle_requerimiento')
            ->leftJoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
            ->leftJoin('configuracion.sis_moneda as moneda_orden', 'moneda_orden.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->leftJoin('logistica.estados_compra', 'estados_compra.id_estado', '=', 'log_ord_compra.estado')
            ->leftJoin('tesoreria.requerimiento_pago_estado', 'requerimiento_pago_estado.id_requerimiento_pago_estado', '=', 'log_ord_compra.estado_pago')
            ->leftJoin('almacen.orden_despacho', 'orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento')
            ->leftJoin('administracion.adm_estado_doc as estado_despacho', 'estado_despacho.id_estado_doc', '=', 'orden_despacho.estado')


            ->select(

                'alm_prod.codigo as codigo_producto',
                'alm_prod.descripcion as descripcion_producto',
                'alm_det_req.descripcion as descripcion_detalle_requerimiento',
                'alm_det_req.motivo',
                'alm_det_req.cantidad',
                'alm_det_req.precio_unitario',
                'alm_det_req.subtotal',
                'alm_det_req.fecha_registro',
                'adm_prioridad.descripcion as prioridad',
                'alm_tp_req.descripcion AS tipo_requerimiento',
                'alm_req.codigo',
                'oportunidades.codigo_oportunidad',
                'alm_req.concepto',
                'alm_req.codigo',
                'alm_req.observacion',
                'sis_moneda.simbolo as simbolo_moneda',
                'sis_sede.codigo as sede',
                'sis_sede.descripcion as descripcion_empresa_sede',
                'adm_contri.razon_social as empresa_razon_social',
                'sis_identi.descripcion as empresa_tipo_documento',
                'proy_proyecto.descripcion AS descripcion_proyecto',
                'sis_grupo.descripcion as grupo',
                'division.descripcion as division',
                'alm_req.monto_total',
                'presup_par.descripcion as descripcion_partida',
                'presup_par.codigo as partida',
                'presup_par.id_partida',
                'padre_centro_costo.codigo as padre_centro_costo',
                'padre_centro_costo.descripcion as padre_descripcion_centro_costo',
                'centro_costo.descripcion as descripcion_centro_costo',
                'centro_costo.codigo as centro_costo',
                'centro_costo.id_centro_costo',
                'adm_estado_doc.estado_doc as estado_requerimiento',
                'presup.codigo as codigo_presupuesto_old',
                'presup.descripcion as descripcion_presupuesto_old',
                'presupuesto_interno.codigo as codigo_presupuesto_interno',
                'presupuesto_interno.descripcion as descripcion_presupuesto_interno',

                DB::raw("(SELECT presup_titu.descripcion
                FROM finanzas.presup_titu
                WHERE presup_titu.codigo = presup_par.cod_padre and presup_titu.id_presup=presup_par.id_presup limit 1) AS descripcion_partida_padre"),
                DB::raw("(SELECT presupuesto_interno_detalle.partida
                FROM finanzas.presupuesto_interno_detalle
                WHERE presupuesto_interno_detalle.id_presupuesto_interno_detalle = alm_det_req.id_partida_pi and alm_req.id_presupuesto_interno > 0 limit 1) AS codigo_sub_partida_presupuesto_interno"),
                DB::raw("(SELECT presupuesto_interno_detalle.descripcion
                FROM finanzas.presupuesto_interno_detalle
                WHERE presupuesto_interno_detalle.id_presupuesto_interno_detalle = alm_det_req.id_partida_pi and alm_req.id_presupuesto_interno > 0 limit 1) AS descripcion_sub_partida_presupuesto_interno"),
                DB::raw("(SELECT presupuesto_interno_modelo.descripcion
                FROM finanzas.presupuesto_interno_detalle
                inner join finanzas.presupuesto_interno_modelo on presupuesto_interno_modelo.id_modelo_presupuesto_interno = presupuesto_interno_detalle.id_padre
                WHERE presupuesto_interno_detalle.id_presupuesto_interno_detalle = alm_det_req.id_partida_pi and alm_req.id_presupuesto_interno > 0 limit 1) AS descripcion_partida_presupuesto_interno"),
                
                'log_ord_compra.codigo as nro_orden',
                'estados_compra.descripcion as estado_orden',
                'requerimiento_pago_estado.descripcion as estado_pago',
                'moneda_orden.simbolo as simbolo_moneda_orden',
                'log_det_ord_compra.cantidad as cantidad_orden',
                'log_det_ord_compra.precio as precio_orden',
                DB::raw("(SELECT log_det_ord_compra.subtotal  
                FROM logistica.log_ord_compra
                WHERE log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra 
                and log_det_ord_compra.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento limit 1) AS subtotal_orden"),
                DB::raw("(SELECT CASE WHEN 
                log_ord_compra.incluye_igv =true THEN (log_det_ord_compra.subtotal * 1.18 ) ELSE log_det_ord_compra.subtotal END  
                FROM logistica.log_ord_compra
                WHERE log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra 
                and log_det_ord_compra.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento limit 1) AS subtotal_orden_considera_igv"),

                'alm_req.fecha_requerimiento',
                DB::raw("(SELECT cont_tp_cambio.venta  
                FROM contabilidad.cont_tp_cambio
                WHERE TO_DATE(to_char(cont_tp_cambio.fecha,'YYYY-MM-DD'),'YYYY-MM-DD') = TO_DATE(to_char(alm_req.fecha_requerimiento,'YYYY-MM-DD'),'YYYY-MM-DD') limit 1) AS tipo_cambio"),

                'estado_despacho.estado_doc as estado_despacho',

                DB::raw("(SELECT string_agg(DISTINCT mov_alm.codigo::text, ', '::text) AS string_agg
                FROM almacen.orden_despacho_det
                left join almacen.guia_ven_det on guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle 
                left join almacen.mov_alm_det on mov_alm_det.id_guia_ven_det = guia_ven_det.id_guia_ven_det 
                left join almacen.mov_alm on mov_alm.id_mov_alm = mov_alm_det.id_mov_alm 
                WHERE orden_despacho_det.id_od = orden_despacho.id_od 
                and alm_det_req.id_detalle_requerimiento = orden_despacho_det.id_detalle_requerimiento
                and orden_despacho.aplica_cambios = true ) AS nro_salida_int"),
                DB::raw("(SELECT string_agg(DISTINCT mov_alm.codigo::text, ', '::text) AS string_agg
                FROM almacen.orden_despacho_det
                left join almacen.guia_ven_det on guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle 
                left join almacen.mov_alm_det on mov_alm_det.id_guia_ven_det = guia_ven_det.id_guia_ven_det 
                left join almacen.mov_alm on mov_alm.id_mov_alm = mov_alm_det.id_mov_alm 
                WHERE orden_despacho_det.id_od = orden_despacho.id_od 
                and alm_det_req.id_detalle_requerimiento = orden_despacho_det.id_detalle_requerimiento
                and orden_despacho.aplica_cambios = false ) AS nro_salida_ext"),
                
                DB::raw("(SELECT alm_almacen.descripcion  
                FROM almacen.alm_almacen
                WHERE  alm_almacen.id_almacen = orden_despacho.id_almacen order by orden_despacho.fecha_registro desc limit 1) AS almacen_salida"),

                DB::raw("(SELECT guia_ven_det.fecha_registro
                FROM almacen.orden_despacho_det
                left join almacen.guia_ven_det on guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle 
                WHERE orden_despacho_det.id_od = orden_despacho.id_od 
                and alm_det_req.id_detalle_requerimiento = orden_despacho_det.id_detalle_requerimiento
                order by orden_despacho.fecha_registro desc limit 1) AS fecha_salida"),

                DB::raw("(SELECT alm_prod.codigo
                FROM almacen.orden_despacho_det
                left join almacen.guia_ven_det on guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle 
                left join almacen.mov_alm_det on mov_alm_det.id_guia_ven_det = guia_ven_det.id_guia_ven_det 
                left join almacen.alm_prod on alm_prod.id_producto = mov_alm_det.id_producto 
                WHERE orden_despacho_det.id_od = orden_despacho.id_od 
                and alm_det_req.id_detalle_requerimiento = orden_despacho_det.id_detalle_requerimiento
                order by orden_despacho.fecha_registro desc limit 1) AS codigo_producto_salida"),
                
                DB::raw("(SELECT mov_alm_det.cantidad
                FROM almacen.orden_despacho_det
                left join almacen.guia_ven_det on guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle 
                left join almacen.mov_alm_det on mov_alm_det.id_guia_ven_det = guia_ven_det.id_guia_ven_det 
                WHERE orden_despacho_det.id_od = orden_despacho.id_od 
                and alm_det_req.id_detalle_requerimiento = orden_despacho_det.id_detalle_requerimiento
                order by orden_despacho.fecha_registro desc limit 1) AS cantidad_salida"),

                DB::raw("(SELECT alm_und_medida.abreviatura
                FROM almacen.orden_despacho_det
                left join almacen.guia_ven_det on guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle 
                left join almacen.mov_alm_det on mov_alm_det.id_guia_ven_det = guia_ven_det.id_guia_ven_det 
                left join almacen.alm_prod on alm_prod.id_producto = mov_alm_det.id_producto 
                left join almacen.alm_und_medida on alm_und_medida.id_unidad_medida = alm_prod.id_unidad_medida 
                WHERE orden_despacho_det.id_od = orden_despacho.id_od 
                and alm_det_req.id_detalle_requerimiento = orden_despacho_det.id_detalle_requerimiento
                order by orden_despacho.fecha_registro desc limit 1) AS moneda_producto_salida"),

                DB::raw("(SELECT round((mov_alm_det.valorizacion::numeric / mov_alm_det.cantidad::numeric),2)
                FROM almacen.orden_despacho_det
                left join almacen.guia_ven_det on guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle 
                left join almacen.mov_alm_det on mov_alm_det.id_guia_ven_det = guia_ven_det.id_guia_ven_det                 
                WHERE orden_despacho_det.id_od = orden_despacho.id_od 
                and alm_det_req.id_detalle_requerimiento = orden_despacho_det.id_detalle_requerimiento
                order by orden_despacho.fecha_registro desc limit 1) AS costo_unitario_salida"),
                
                DB::raw("(SELECT round(mov_alm_det.valorizacion::numeric ,2)
                FROM almacen.orden_despacho_det
                left join almacen.guia_ven_det on guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle 
                left join almacen.mov_alm_det on mov_alm_det.id_guia_ven_det = guia_ven_det.id_guia_ven_det                 
                WHERE orden_despacho_det.id_od = orden_despacho.id_od 
                and alm_det_req.id_detalle_requerimiento = orden_despacho_det.id_detalle_requerimiento
                order by orden_despacho.fecha_registro desc limit 1) AS costo_total_salida"),
                
            )
            ->where([['alm_det_req.estado', '!=', 7], ['alm_req.estado', '!=', 7]]);

        return datatables($listado)
        ->editColumn('fecha_registro', function ($data) {
            return date('d-m-Y', strtotime($data->fecha_registro));
        })
        ->addColumn('hora_registro', function ($data) { return  date('h:m:s', strtotime($data->fecha_registro)); })
        ->filterColumn('codigo', function ($query, $keyword) {
            try {
                $query->where('alm_req.codigo', trim($keyword));
            } catch (\Throwable $th) {
            }
        })
        ->toJson();

    }

    public function listaGastoDetalleRequerimientoLogisticoExcel()
    {
        return Excel::download(new ListaGastoDetalleRequerimientoLogisticoExport(), 'reporte_gastos_requerimiento_logistico.xlsx');;
    }


    public function dataGastoDetalleRequerimientoPago(){
        $data = DB::table('tesoreria.requerimiento_pago_detalle')
            ->leftJoin('tesoreria.requerimiento_pago', 'requerimiento_pago.id_requerimiento_pago', '=', 'requerimiento_pago_detalle.id_requerimiento_pago')
            ->leftJoin('finanzas.presupuesto_interno', 'presupuesto_interno.id_presupuesto_interno', '=', 'requerimiento_pago.id_presupuesto_interno')
            ->leftJoin('configuracion.sis_moneda', 'requerimiento_pago.id_moneda', '=', 'sis_moneda.id_moneda')
            ->leftJoin('administracion.adm_prioridad', 'requerimiento_pago.id_prioridad', '=', 'adm_prioridad.id_prioridad')
            ->leftJoin('configuracion.sis_grupo', 'requerimiento_pago.id_grupo', '=', 'sis_grupo.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'requerimiento_pago.id_sede')
            ->leftJoin('administracion.division', 'division.id_division', '=', 'requerimiento_pago.id_division')
            ->leftJoin('proyectos.proy_proyecto', 'proy_proyecto.id_proyecto', '=', 'requerimiento_pago.id_proyecto')
            ->leftJoin('administracion.adm_empresa', 'requerimiento_pago.id_empresa', '=', 'adm_empresa.id_empresa')
            ->leftJoin('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')

            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'requerimiento_pago.id_cc')
            ->leftJoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('tesoreria.requerimiento_pago_tipo', 'requerimiento_pago_tipo.id_requerimiento_pago_tipo', '=', 'requerimiento_pago.id_requerimiento_pago_tipo')

            ->leftJoin('finanzas.presup_par', 'presup_par.id_partida', '=', 'requerimiento_pago_detalle.id_partida')
            ->leftJoin('finanzas.presup', 'presup.id_presup', '=', 'presup_par.id_presup')
            ->leftJoin('finanzas.centro_costo', 'centro_costo.id_centro_costo', '=', 'requerimiento_pago_detalle.id_centro_costo')
            ->leftJoin('finanzas.centro_costo as padre_centro_costo', 'padre_centro_costo.id_centro_costo', '=', 'centro_costo.id_padre')

            ->leftJoin('tesoreria.requerimiento_pago_estado', 'requerimiento_pago.id_estado', '=', 'requerimiento_pago_estado.id_requerimiento_pago_estado')

            ->leftJoin('contabilidad.adm_contri as adm_contri_destinatario', 'requerimiento_pago.id_contribuyente', '=', 'adm_contri_destinatario.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi as tipo_doc_contrib', 'tipo_doc_contrib.id_doc_identidad', '=', 'adm_contri_destinatario.id_doc_identidad')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'requerimiento_pago.id_persona')
            ->leftJoin('contabilidad.sis_identi as tipo_doc_perso', 'tipo_doc_perso.id_doc_identidad', '=', 'rrhh_perso.id_documento_identidad')

            ->select(
                'requerimiento_pago_detalle.descripcion',
                'requerimiento_pago_detalle.motivo',
                'requerimiento_pago_detalle.cantidad',
                'requerimiento_pago_detalle.precio_unitario',
                'requerimiento_pago_detalle.subtotal',
                'requerimiento_pago_detalle.fecha_registro',
                'adm_prioridad.descripcion as prioridad',
                'requerimiento_pago_tipo.descripcion AS tipo_requerimiento',

                'requerimiento_pago.codigo',
                'oportunidades.codigo_oportunidad',
                'requerimiento_pago.concepto',
                'requerimiento_pago.comentario',
                'sis_moneda.simbolo as simbolo_moneda',
                'sis_sede.codigo as sede',
                'sis_sede.descripcion as descripcion_empresa_sede',
                'adm_contri.razon_social as empresa_razon_social',
                'sis_identi.descripcion as empresa_tipo_documento',
                'proy_proyecto.descripcion AS descripcion_proyecto',
                'sis_grupo.descripcion as grupo',
                'division.descripcion as division',
                'requerimiento_pago.monto_total',
                'presup_par.codigo as partida',
                'presup_par.descripcion as descripcion_partida',
                'presup_par.id_partida',
                'padre_centro_costo.codigo as padre_centro_costo',
                'padre_centro_costo.descripcion as padre_descripcion_centro_costo',
                'centro_costo.codigo as centro_costo',
                'centro_costo.descripcion as descripcion_centro_costo',
                'centro_costo.id_centro_costo',
                'requerimiento_pago_estado.descripcion as estado_requerimiento',
                'presup.codigo as codigo_presupuesto_old',
                'presup.descripcion as descripcion_presupuesto_old',
                'presupuesto_interno.codigo as codigo_presupuesto_interno',
                'presupuesto_interno.descripcion as descripcion_presupuesto_interno',
                DB::raw("(SELECT presup_titu.descripcion
                FROM finanzas.presup_titu
                WHERE presup_titu.codigo = presup_par.cod_padre and presup_titu.id_presup=presup_par.id_presup limit 1) AS descripcion_partida_padre"),
                DB::raw("(SELECT presupuesto_interno_detalle.partida
                FROM finanzas.presupuesto_interno_detalle
                WHERE presupuesto_interno_detalle.id_presupuesto_interno_detalle = requerimiento_pago_detalle.id_partida_pi and requerimiento_pago.id_presupuesto_interno > 0 limit 1) AS codigo_sub_partida_presupuesto_interno"),
                DB::raw("(SELECT presupuesto_interno_detalle.descripcion
                FROM finanzas.presupuesto_interno_detalle
                WHERE presupuesto_interno_detalle.id_presupuesto_interno_detalle = requerimiento_pago_detalle.id_partida_pi and requerimiento_pago.id_presupuesto_interno > 0 limit 1) AS descripcion_sub_partida_presupuesto_interno"),
                DB::raw("(SELECT presupuesto_interno_modelo.descripcion
                FROM finanzas.presupuesto_interno_detalle
                inner join finanzas.presupuesto_interno_modelo on presupuesto_interno_modelo.id_modelo_presupuesto_interno = presupuesto_interno_detalle.id_padre
                WHERE presupuesto_interno_detalle.id_presupuesto_interno_detalle = requerimiento_pago_detalle.id_partida_pi and requerimiento_pago.id_presupuesto_interno > 0 limit 1) AS descripcion_partida_presupuesto_interno"),
    
                
                DB::raw("(SELECT CASE WHEN requerimiento_pago.id_moneda =1 THEN requerimiento_pago_detalle.subtotal 
                WHEN requerimiento_pago.id_moneda =2 THEN (requerimiento_pago_detalle.subtotal * cont_tp_cambio.venta) ELSE 0 END
                FROM contabilidad.cont_tp_cambio
                WHERE TO_DATE(to_char(cont_tp_cambio.fecha,'YYYY-MM-DD'),'YYYY-MM-DD') = TO_DATE(to_char(requerimiento_pago.fecha_registro,'YYYY-MM-DD'),'YYYY-MM-DD') limit 1) AS subtotal_soles"),

                DB::raw("(SELECT cont_tp_cambio.venta
                FROM contabilidad.cont_tp_cambio
                WHERE TO_DATE(to_char(cont_tp_cambio.fecha,'YYYY-MM-DD'),'YYYY-MM-DD') = TO_DATE(to_char(((SELECT adm_aprobacion.fecha_vobo FROM administracion.adm_documentos_aprob 
                inner join administracion.adm_aprobacion on adm_aprobacion.id_doc_aprob = adm_documentos_aprob.id_doc_aprob
                WHERE adm_documentos_aprob.id_doc = requerimiento_pago.id_requerimiento_pago and adm_documentos_aprob.id_tp_documento = 11  and adm_aprobacion.id_vobo =1 ORDER BY adm_aprobacion.fecha_vobo DESC LIMIT 1)),'YYYY-MM-DD'),'YYYY-MM-DD') limit 1) AS tipo_cambio"),

                DB::raw("(SELECT adm_aprobacion.fecha_vobo 
                FROM administracion.adm_documentos_aprob 
                inner join administracion.adm_aprobacion on adm_aprobacion.id_doc_aprob = adm_documentos_aprob.id_doc_aprob
                WHERE adm_documentos_aprob.id_doc = requerimiento_pago.id_requerimiento_pago and adm_documentos_aprob.id_tp_documento = 11  and adm_aprobacion.id_vobo =1 ORDER BY adm_aprobacion.fecha_vobo DESC limit 1) AS fecha_aprobacion"),
    
                DB::raw("(SELECT sis_usua.nombre_corto 
                FROM administracion.adm_documentos_aprob 
                inner join administracion.adm_aprobacion on adm_aprobacion.id_doc_aprob = adm_documentos_aprob.id_doc_aprob
                inner join configuracion.sis_usua on sis_usua.id_usuario = adm_aprobacion.id_usuario
                WHERE adm_documentos_aprob.id_doc = requerimiento_pago.id_requerimiento_pago and adm_documentos_aprob.id_tp_documento = 11  and adm_aprobacion.id_vobo =1 ORDER BY adm_aprobacion.fecha_vobo DESC limit 1) AS usuario_aprobador"),

                DB::raw("(SELECT CASE WHEN requerimiento_pago.id_persona > 0 THEN CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno)
                WHEN requerimiento_pago.id_contribuyente >0 THEN adm_contri_destinatario.razon_social ELSE '' END) AS nombre_destinatario"),

                DB::raw("(SELECT CASE WHEN requerimiento_pago.id_persona > 0 THEN tipo_doc_perso.descripcion
                WHEN requerimiento_pago.id_contribuyente >0 THEN tipo_doc_contrib.descripcion ELSE '' END) AS tipo_documento_destinatario"),
                
                DB::raw("(SELECT CASE WHEN requerimiento_pago.id_persona > 0 THEN rrhh_perso.nro_documento
                WHEN requerimiento_pago.id_contribuyente >0 THEN adm_contri_destinatario.nro_documento ELSE '' END) AS nro_documento_destinatario")
            )

            ->where([['requerimiento_pago_detalle.id_estado', '!=', 7], ['requerimiento_pago.id_estado', '!=', 7]])
            ->orderBy('requerimiento_pago_detalle.fecha_registro', 'desc')
            ->get();

        return $data;
    }
        
    public function listaGastoDetalleRequerimientoPago()
    {

        $listado = DB::table('tesoreria.requerimiento_pago_detalle')
            ->leftJoin('tesoreria.requerimiento_pago', 'requerimiento_pago.id_requerimiento_pago', '=', 'requerimiento_pago_detalle.id_requerimiento_pago')
            ->leftJoin('finanzas.presupuesto_interno', 'presupuesto_interno.id_presupuesto_interno', '=', 'requerimiento_pago.id_presupuesto_interno')
            ->leftJoin('configuracion.sis_moneda', 'requerimiento_pago.id_moneda', '=', 'sis_moneda.id_moneda')
            ->leftJoin('administracion.adm_prioridad', 'requerimiento_pago.id_prioridad', '=', 'adm_prioridad.id_prioridad')
            ->leftJoin('configuracion.sis_grupo', 'requerimiento_pago.id_grupo', '=', 'sis_grupo.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'requerimiento_pago.id_sede')
            ->leftJoin('administracion.division', 'division.id_division', '=', 'requerimiento_pago.id_division')
            ->leftJoin('proyectos.proy_proyecto', 'proy_proyecto.id_proyecto', '=', 'requerimiento_pago.id_proyecto')
            ->leftJoin('administracion.adm_empresa', 'requerimiento_pago.id_empresa', '=', 'adm_empresa.id_empresa')
            ->leftJoin('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')

            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'requerimiento_pago.id_cc')
            ->leftJoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('tesoreria.requerimiento_pago_tipo', 'requerimiento_pago_tipo.id_requerimiento_pago_tipo', '=', 'requerimiento_pago.id_requerimiento_pago_tipo')

            ->leftJoin('finanzas.presup_par', 'presup_par.id_partida', '=', 'requerimiento_pago_detalle.id_partida')
            ->leftJoin('finanzas.presup', 'presup.id_presup', '=', 'presup_par.id_presup')
            ->leftJoin('finanzas.centro_costo', 'centro_costo.id_centro_costo', '=', 'requerimiento_pago_detalle.id_centro_costo')
            ->leftJoin('finanzas.centro_costo as padre_centro_costo', 'padre_centro_costo.id_centro_costo', '=', 'centro_costo.id_padre')

            ->leftJoin('tesoreria.requerimiento_pago_estado', 'requerimiento_pago.id_estado', '=', 'requerimiento_pago_estado.id_requerimiento_pago_estado')
            
            ->leftJoin('contabilidad.adm_contri as adm_contri_destinatario', 'requerimiento_pago.id_contribuyente', '=', 'adm_contri_destinatario.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi as tipo_doc_contrib', 'tipo_doc_contrib.id_doc_identidad', '=', 'adm_contri_destinatario.id_doc_identidad')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'requerimiento_pago.id_persona')
            ->leftJoin('contabilidad.sis_identi as tipo_doc_perso', 'tipo_doc_perso.id_doc_identidad', '=', 'rrhh_perso.id_documento_identidad')


            ->select(
                'requerimiento_pago_detalle.descripcion',
                'requerimiento_pago_detalle.motivo',
                'requerimiento_pago_detalle.cantidad',
                'requerimiento_pago_detalle.precio_unitario',
                'requerimiento_pago_detalle.subtotal',
                'requerimiento_pago_detalle.fecha_registro',
                'adm_prioridad.descripcion as prioridad',
                'requerimiento_pago_tipo.descripcion AS tipo_requerimiento',

                'requerimiento_pago.codigo',
                'oportunidades.codigo_oportunidad',
                'requerimiento_pago.concepto',
                'requerimiento_pago.comentario',
                'sis_moneda.simbolo as simbolo_moneda',
                'sis_sede.codigo as sede',
                'sis_sede.descripcion as descripcion_empresa_sede',
                'adm_contri.razon_social as empresa_razon_social',
                'sis_identi.descripcion as empresa_tipo_documento',
                'proy_proyecto.descripcion AS descripcion_proyecto',
                'sis_grupo.descripcion as grupo',
                'division.descripcion as division',
                'requerimiento_pago.monto_total',
                'presup_par.codigo as partida',
                'presup_par.descripcion as descripcion_partida',
                'presup_par.id_partida',
                'padre_centro_costo.codigo as padre_centro_costo',
                'padre_centro_costo.descripcion as padre_descripcion_centro_costo',
                'centro_costo.codigo as centro_costo',
                'centro_costo.descripcion as descripcion_centro_costo',
                'centro_costo.id_centro_costo',
                'requerimiento_pago_estado.descripcion as estado_requerimiento',
                'presup.codigo as codigo_presupuesto_old',
                'presup.descripcion as descripcion_presupuesto_old',
                'presupuesto_interno.codigo as codigo_presupuesto_interno',
                'presupuesto_interno.descripcion as descripcion_presupuesto_interno',
                DB::raw("(SELECT presup_titu.descripcion
                FROM finanzas.presup_titu
                WHERE presup_titu.codigo = presup_par.cod_padre and presup_titu.id_presup=presup_par.id_presup limit 1) AS descripcion_partida_padre"),
                DB::raw("(SELECT presupuesto_interno_detalle.partida
                FROM finanzas.presupuesto_interno_detalle
                WHERE presupuesto_interno_detalle.id_presupuesto_interno_detalle = requerimiento_pago_detalle.id_partida_pi and requerimiento_pago.id_presupuesto_interno > 0 limit 1) AS codigo_sub_partida_presupuesto_interno"),
                DB::raw("(SELECT presupuesto_interno_detalle.descripcion
                FROM finanzas.presupuesto_interno_detalle
                WHERE presupuesto_interno_detalle.id_presupuesto_interno_detalle = requerimiento_pago_detalle.id_partida_pi and requerimiento_pago.id_presupuesto_interno > 0 limit 1) AS descripcion_sub_partida_presupuesto_interno"),
                DB::raw("(SELECT presupuesto_interno_modelo.descripcion
                FROM finanzas.presupuesto_interno_detalle
                inner join finanzas.presupuesto_interno_modelo on presupuesto_interno_modelo.id_modelo_presupuesto_interno = presupuesto_interno_detalle.id_padre
                WHERE presupuesto_interno_detalle.id_presupuesto_interno_detalle = requerimiento_pago_detalle.id_partida_pi and requerimiento_pago.id_presupuesto_interno > 0 limit 1) AS descripcion_partida_presupuesto_interno"),
               
                DB::raw("(SELECT CASE WHEN requerimiento_pago.id_moneda =1 THEN requerimiento_pago_detalle.subtotal 
                WHEN requerimiento_pago.id_moneda =2 THEN (requerimiento_pago_detalle.subtotal * cont_tp_cambio.venta) ELSE 0 END
                FROM contabilidad.cont_tp_cambio
                WHERE TO_DATE(to_char(cont_tp_cambio.fecha,'YYYY-MM-DD'),'YYYY-MM-DD') = TO_DATE(to_char(requerimiento_pago.fecha_registro,'YYYY-MM-DD'),'YYYY-MM-DD') limit 1) AS subtotal_soles"),

                
                // DB::raw("(SELECT cont_tp_cambio.venta  
                // FROM contabilidad.cont_tp_cambio
                // WHERE TO_DATE(to_char(cont_tp_cambio.fecha,'YYYY-MM-DD'),'YYYY-MM-DD') = TO_DATE(to_char(requerimiento_pago.fecha_registro,'YYYY-MM-DD'),'YYYY-MM-DD') limit 1) AS tipo_cambio"),
                DB::raw("(SELECT cont_tp_cambio.venta
                FROM contabilidad.cont_tp_cambio
                WHERE TO_DATE(to_char(cont_tp_cambio.fecha,'YYYY-MM-DD'),'YYYY-MM-DD') = TO_DATE(to_char(((SELECT adm_aprobacion.fecha_vobo FROM administracion.adm_documentos_aprob 
                inner join administracion.adm_aprobacion on adm_aprobacion.id_doc_aprob = adm_documentos_aprob.id_doc_aprob
                WHERE adm_documentos_aprob.id_doc = requerimiento_pago.id_requerimiento_pago and adm_documentos_aprob.id_tp_documento = 11 and adm_aprobacion.id_vobo =1 ORDER BY adm_aprobacion.fecha_vobo DESC LIMIT 1)),'YYYY-MM-DD'),'YYYY-MM-DD') limit 1) AS tipo_cambio"),
                
                DB::raw("(SELECT adm_aprobacion.fecha_vobo 
                FROM administracion.adm_documentos_aprob 
                inner join administracion.adm_aprobacion on adm_aprobacion.id_doc_aprob = adm_documentos_aprob.id_doc_aprob
                WHERE adm_documentos_aprob.id_doc = requerimiento_pago.id_requerimiento_pago and adm_documentos_aprob.id_tp_documento = 11  and adm_aprobacion.id_vobo =1 ORDER BY adm_aprobacion.fecha_vobo DESC limit 1) AS fecha_aprobacion"),

                DB::raw("(SELECT sis_usua.nombre_corto 
                FROM administracion.adm_documentos_aprob 
                inner join administracion.adm_aprobacion on adm_aprobacion.id_doc_aprob = adm_documentos_aprob.id_doc_aprob
                inner join configuracion.sis_usua on sis_usua.id_usuario = adm_aprobacion.id_usuario
                WHERE adm_documentos_aprob.id_doc = requerimiento_pago.id_requerimiento_pago and adm_documentos_aprob.id_tp_documento = 11  and adm_aprobacion.id_vobo =1 ORDER BY adm_aprobacion.fecha_vobo DESC limit 1) AS usuario_aprobador"),
                
                DB::raw("(SELECT CASE WHEN requerimiento_pago.id_persona > 0 THEN CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno)
                WHEN requerimiento_pago.id_contribuyente >0 THEN adm_contri_destinatario.razon_social ELSE '' END) AS nombre_destinatario"),

                DB::raw("(SELECT CASE WHEN requerimiento_pago.id_persona > 0 THEN tipo_doc_perso.descripcion
                WHEN requerimiento_pago.id_contribuyente >0 THEN tipo_doc_contrib.descripcion ELSE '' END) AS tipo_documento_destinatario"),
                
                DB::raw("(SELECT CASE WHEN requerimiento_pago.id_persona > 0 THEN rrhh_perso.nro_documento
                WHEN requerimiento_pago.id_contribuyente >0 THEN adm_contri_destinatario.nro_documento ELSE '' END) AS nro_documento_destinatario")

            )
            ->where([['requerimiento_pago_detalle.id_estado', '!=', 7], ['requerimiento_pago.id_estado', '!=', 7]]);

            return datatables($listado)
            ->editColumn('fecha_registro', function ($data) {
                return date('d-m-Y', strtotime($data->fecha_registro));
            })
            ->editColumn('fecha_aprobacion', function ($data) {
                if(isset($data->fecha_aprobacion) && $data->fecha_aprobacion != null){
                    return date('d-m-Y', strtotime($data->fecha_aprobacion));
                }else{
                    return '';
                }
            })
            ->addColumn('hora_registro', function ($data) { return  date('h:m:s', strtotime($data->fecha_registro)); })
            ->filterColumn('codigo', function ($query, $keyword) {
                try {
                    $query->where('requerimiento_pago.codigo', trim($keyword));
                } catch (\Throwable $th) {
                }
            })
            ->toJson();
    }

    public function listaGastoDetalleRequerimienoPagoExcel()
    {
        return Excel::download(new ListaGastoDetalleRequerimientoPagoExport(), 'reporte_gastos_requerimiento_pago.xlsx');;
    }

    public function dataGastoCDP(){

        
        $listado = CcAmFila::select(
            'oportunidades.codigo_oportunidad',
            'oportunidades.oportunidad',
            'oportunidades.moneda as moneda_oportunidad',
            'oportunidades.importe as importe_oportunidad',
            'oportunidades.created_at as fecha_registro_oportunidad',
            'estados.estado as estado_oportunidad',
            'cc.tipo_cambio',
            'cc.igv',
            'cc_am_filas.id',
            'cc_am_filas.id as id_cc_am_filas',
            'cc_am_filas.id_cc_am',
            'cc_am_filas.part_no',
            'cc_am_filas.descripcion',
            'cc_am_filas.cantidad',
            'cc_am_filas.pvu_oc',
            'cc_am_filas.flete_oc',
            'cc_am_filas.proveedor_seleccionado',
            'proveedores.razon_social as razon_social_proveedor',
            'proveedores.ruc as ruc_proveedor',
            'cc_am_filas.garantia',
            'tipos_negocio.tipo as tipo_negocio',
            'origenes_costeo.origen as origen_costo',
            'cc_am_proveedores.precio as costo_unitario_proveedor',
            'cc_am_proveedores.moneda as moneda_costo_unitario_proveedor',
            'cc_am_proveedores.plazo as plazo_proveedor',
            'cc_am_proveedores.flete as flete_proveedor',
            'fondos_proveedores.descripcion as fondo_proveedor',
            'cc_am_filas.id_ultimo_usuario as id_autor',
            'users.name as nombre_autor',
            'cc_am_filas.created_at',
            'cc_am_filas.part_no_producto_transformado',
            'cc_am_filas.descripcion_producto_transformado',
            'cc_am_filas.comentario_producto_transformado'
            )
        ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'cc_am_filas.id_cc_am')
        ->leftJoin('mgcp_cuadro_costos.estados_aprobacion', 'estados_aprobacion.id', '=', 'cc.estado_aprobacion')
        ->leftJoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
        ->leftJoin('mgcp_oportunidades.tipos_negocio', 'tipos_negocio.id', '=', 'oportunidades.id_tipo_negocio')
        ->leftJoin('mgcp_oportunidades.estados', 'estados.id', '=', 'oportunidades.id_estado')
        ->leftJoin('mgcp_cuadro_costos.cc_am_proveedores', 'cc_am_proveedores.id', '=', 'cc_am_filas.proveedor_seleccionado')
        ->leftJoin('mgcp_cuadro_costos.proveedores', 'proveedores.id', '=', 'cc_am_proveedores.id_proveedor')
        ->leftJoin('mgcp_cuadro_costos.fondos_proveedores', 'fondos_proveedores.id', '=', 'cc_am_proveedores.id_fondo_proveedor')
        ->leftJoin('mgcp_usuarios.users', 'users.id', '=', 'cc_am_filas.id_ultimo_usuario')
        ->leftJoin('mgcp_cuadro_costos.origenes_costeo', 'origenes_costeo.id', '=', 'cc_am_filas.id_origen_costeo')  
        ->orderBy('oportunidades.codigo_oportunidad', 'desc')
        ->get();

        return $listado;

    }
    public function listaGastoCDP(){
        $listado = CcAmFila::select(
            'oportunidades.codigo_oportunidad',
            'oportunidades.oportunidad',
            'oportunidades.moneda as moneda_oportunidad',
            'oportunidades.importe as importe_oportunidad',
            'oportunidades.created_at as fecha_registro_oportunidad',
            'estados.estado as estado_oportunidad',
            'cc.tipo_cambio',
            'cc.igv',
            'cc_am_filas.id',
            'cc_am_filas.id as id_cc_am_filas',
            'cc_am_filas.id_cc_am',
            'cc_am_filas.part_no',
            'cc_am_filas.descripcion',
            'cc_am_filas.cantidad',
            'cc_am_filas.pvu_oc',
            'cc_am_filas.flete_oc',
            'cc_am_filas.proveedor_seleccionado',
            'proveedores.razon_social as razon_social_proveedor',
            'proveedores.ruc as ruc_proveedor',
            'cc_am_filas.garantia',
            'tipos_negocio.tipo as tipo_negocio',
            'origenes_costeo.origen as origen_costo',
            'cc_am_proveedores.precio as costo_unitario_proveedor',
            'cc_am_proveedores.moneda as moneda_costo_unitario_proveedor',
            'cc_am_proveedores.plazo as plazo_proveedor',
            'cc_am_proveedores.flete as flete_proveedor',
            'fondos_proveedores.descripcion as fondo_proveedor',
            'cc_am_filas.id_ultimo_usuario as id_autor',
            'users.name as nombre_autor',
            'cc_am_filas.created_at',
            'cc_am_filas.part_no_producto_transformado',
            'cc_am_filas.descripcion_producto_transformado',
            'cc_am_filas.comentario_producto_transformado'
            )
        ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'cc_am_filas.id_cc_am')
        ->leftJoin('mgcp_cuadro_costos.estados_aprobacion', 'estados_aprobacion.id', '=', 'cc.estado_aprobacion')
        ->leftJoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
        ->leftJoin('mgcp_oportunidades.tipos_negocio', 'tipos_negocio.id', '=', 'oportunidades.id_tipo_negocio')
        ->leftJoin('mgcp_oportunidades.estados', 'estados.id', '=', 'oportunidades.id_estado')
        ->leftJoin('mgcp_cuadro_costos.cc_am_proveedores', 'cc_am_proveedores.id', '=', 'cc_am_filas.proveedor_seleccionado')
        ->leftJoin('mgcp_cuadro_costos.proveedores', 'proveedores.id', '=', 'cc_am_proveedores.id_proveedor')
        ->leftJoin('mgcp_cuadro_costos.fondos_proveedores', 'fondos_proveedores.id', '=', 'cc_am_proveedores.id_fondo_proveedor')
        ->leftJoin('mgcp_usuarios.users', 'users.id', '=', 'cc_am_filas.id_ultimo_usuario')
        ->leftJoin('mgcp_cuadro_costos.origenes_costeo', 'origenes_costeo.id', '=', 'cc_am_filas.id_origen_costeo');
        
        return datatables($listado)
        ->editColumn('created_at', function ($data) {
            return date('d-m-Y', strtotime($data->created_at));
        })
     
        ->toJson();

        return $listado;
    }

    public function listaGastoCDPExcel(){
        return Excel::download(new ListaGastoDetalleCDPExport(), 'reporte_gastos_cdp_pago.xlsx');;
    }



}
