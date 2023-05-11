<?php

namespace App\Http\Controllers\Finanzas\Reportes;

use App\Exports\ListaGastoDetalleRequerimientoLogisticoExport;
use App\Exports\ListaGastoDetalleRequerimientoPagoExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
 

            ->select(

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
 

            ->select(

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
                WHERE TO_DATE(to_char(cont_tp_cambio.fecha,'YYYY-MM-DD'),'YYYY-MM-DD') = TO_DATE(to_char(requerimiento_pago.fecha_registro,'YYYY-MM-DD'),'YYYY-MM-DD') limit 1) AS tipo_cambio"),
                
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
                WHERE TO_DATE(to_char(cont_tp_cambio.fecha,'YYYY-MM-DD'),'YYYY-MM-DD') = TO_DATE(to_char(requerimiento_pago.fecha_registro,'YYYY-MM-DD'),'YYYY-MM-DD') limit 1) AS tipo_cambio"),
                

            )
            ->where([['requerimiento_pago_detalle.id_estado', '!=', 7], ['requerimiento_pago.id_estado', '!=', 7]]);

            return datatables($listado)
            ->editColumn('fecha_registro', function ($data) {
                return date('d-m-Y', strtotime($data->fecha_registro));
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


}
