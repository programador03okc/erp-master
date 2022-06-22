<?php

namespace App\Http\Controllers\Tesoreria\Facturacion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AlmacenController as GenericoAlmacenController;
use Carbon\Carbon;

class PendientesFacturacionController extends Controller
{
    function view_pendientes_facturacion()
    {
        $tp_doc = GenericoAlmacenController::mostrar_tp_doc_cbo();
        $empresas = $this->mostrar_empresas_cbo();
        $motivos = $this->mostrar_motivos_emision_cbo();
        $monedas = GenericoAlmacenController::mostrar_moneda_cbo();
        $condiciones = GenericoAlmacenController::mostrar_condiciones_cbo();

        return view(
            'tesoreria/facturacion/pendientesFacturacion',
            compact('tp_doc', 'empresas', 'monedas', 'condiciones')
        );
    }

    public static function mostrar_empresas_cbo()
    {
        $data = DB::table('administracion.adm_empresa')
            ->select('adm_empresa.*', 'adm_contri.razon_social')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->where([['adm_empresa.estado', '!=', 7]])
            ->orderBy('adm_contri.razon_social')
            ->get();
        return $data;
    }

    public static function mostrar_motivos_emision_cbo()
    {
        $data = DB::table('almacen.doc_motivo_emision')
            ->select('doc_motivo_emision.*')
            ->where([['doc_motivo_emision.estado', '!=', 7]])
            ->get();
        return $data;
    }

    public function listarGuiasVentaPendientes()
    {
        $data = DB::table('almacen.guia_ven')
            ->select(
                'guia_ven.*',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'usu_trans.nombre_corto as nombre_corto_trans',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'alm_almacen.descripcion as almacen_descripcion',
                'sis_sede.descripcion as sede_descripcion',
                'sis_sede.id_empresa',
                'trans.codigo as codigo_trans',

                DB::raw("(SELECT count(guia.id_guia_ven_det) FROM almacen.guia_ven_det AS guia
                    LEFT JOIN almacen.doc_ven_det AS doc
                        on( guia.id_guia_ven_det = doc.id_guia_ven_det
                        and doc.estado != 7)
                    WHERE guia.id_guia_ven = guia_ven.id_guia_ven
                    and doc.id_guia_ven_det is null) AS items_restantes"),

                DB::raw("(SELECT count(distinct id_doc_ven) FROM almacen.doc_ven AS d
                    INNER JOIN almacen.guia_ven_det AS guia
                        on( guia.id_guia_ven = guia_ven.id_guia_ven
                        and guia.estado != 7)
                    INNER JOIN almacen.doc_ven_det AS doc
                        on( guia.id_guia_ven_det = doc.id_guia_ven_det
                        and doc.estado != 7)
                    WHERE d.id_doc_ven = doc.id_doc) AS count_facturas")
            )
            ->join('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'guia_ven.id_cliente')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'guia_ven.id_almacen')
            ->join('almacen.trans', 'trans.id_transferencia', '=', 'guia_ven.id_transferencia')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'trans.id_requerimiento')
            ->join('administracion.sis_sede as sede_req', 'sede_req.id_sede', '=', 'alm_req.id_sede')
            ->leftjoin('configuracion.sis_usua as usu_trans', 'usu_trans.id_usuario', '=', 'trans.responsable_origen')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'guia_ven.id_sede')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'guia_ven.estado')
            ->where('guia_ven.estado', 1)
            ->where('guia_ven.id_operacion', 1);

        return datatables($data)->toJson();
    }

    public function detalleFacturasGuias($id_guia)
    {
        $data = DB::table('almacen.guia_ven')
            ->select(
                'doc_ven.*',
                DB::raw("(cont_tp_doc.abreviatura) || ' ' || (doc_ven.serie) || '-' || (doc_ven.numero) as serie_numero"),
                'empresa.razon_social as empresa_razon_social',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'sis_moneda.simbolo',
                'sis_usua.nombre_corto',
                'log_cdn_pago.descripcion as condicion'
            )
            ->join('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven', '=', 'guia_ven.id_guia_ven')
            ->join('almacen.doc_ven_det', 'doc_ven_det.id_guia_ven_det', '=', 'guia_ven_det.id_guia_ven_det')
            ->join('almacen.doc_ven', 'doc_ven.id_doc_ven', '=', 'doc_ven_det.id_doc')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'doc_ven.id_empresa')
            ->join('contabilidad.adm_contri as empresa', 'empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->join('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'doc_ven.id_cliente')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_ven.moneda')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'doc_ven.usuario')
            ->join('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_ven.id_tp_doc')
            ->join('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_ven.id_condicion')
            ->where('guia_ven.id_guia_ven', $id_guia)
            ->where([['doc_ven.estado', '!=', 7]])
            ->distinct()
            ->get();
        return response()->json($data);
    }

    public function listarRequerimientosPendientes()
    {
        $data = DB::table('almacen.alm_req')
            ->select(
                'alm_req.*',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'sis_usua.nombre_corto',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'sis_sede.descripcion as sede_descripcion',
                'oc_propias_view.nro_orden',
                'oc_propias_view.codigo_oportunidad',
                'oc_propias_view.id as id_oc_propia',
                'oc_propias_view.tipo',
                'oc_propias_view.monto_total',
                'oc_propias_view.nombre_largo_responsable',
                'alm_req.codigo as codigo_req',
                'oc_propias_view.moneda_oc',

                DB::raw("(SELECT count(distinct doc.id_doc_det) FROM almacen.doc_ven_det AS doc
                INNER JOIN almacen.alm_det_req AS req
                    on( req.id_requerimiento = alm_req.id_requerimiento and
                    doc.id_detalle_requerimiento = req.id_detalle_requerimiento)
                WHERE  doc.estado != 7) AS count_facturas"),

                DB::raw("(SELECT count(alm_det_req.id_detalle_requerimiento) FROM almacen.alm_det_req 
                    LEFT JOIN almacen.doc_ven_det
                    on( alm_det_req.id_detalle_requerimiento = doc_ven_det.id_detalle_requerimiento
                    and doc_ven_det.id_detalle_requerimiento is null )
                    WHERE alm_det_req.id_requerimiento = alm_req.id_requerimiento
                        and alm_det_req.entrega_cliente = true
                        and alm_det_req.estado != 7) AS items_restantes")

            )
            ->join('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->leftjoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.estado')
            ->where('alm_req.enviar_facturacion', true);

        return datatables($data)->toJson();
    }

    public function detalleFacturasRequerimientos($id_requerimiento)
    {
        $data = DB::table('almacen.alm_req')
            ->select(
                'doc_ven.*',
                DB::raw("(cont_tp_doc.abreviatura) || ' ' || (doc_ven.serie) || '-' || (doc_ven.numero) as serie_numero"),
                'empresa.razon_social as empresa_razon_social',
                'adm_contri.razon_social',
                'sis_moneda.simbolo',
                'sis_usua.nombre_corto',
                'log_cdn_pago.descripcion as condicion'
            )
            ->join('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento')
            ->join('almacen.doc_ven_det', 'doc_ven_det.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')
            ->join('almacen.doc_ven', 'doc_ven.id_doc_ven', '=', 'doc_ven_det.id_doc')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'doc_ven.id_empresa')
            ->join('contabilidad.adm_contri as empresa', 'empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->join('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'doc_ven.id_cliente')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_ven.moneda')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'doc_ven.usuario')
            ->join('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_ven.id_tp_doc')
            ->join('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_ven.id_condicion')
            ->where([['alm_req.id_requerimiento', '=', $id_requerimiento], ['doc_ven.estado', '!=', 7]])
            ->distinct()->get();
        return response()->json($data);
    }

    public function obtenerGuiaVenta($id)
    {
        $guia = DB::table('almacen.guia_ven')
            ->select(
                'guia_ven.id_guia_ven',
                'guia_ven.id_cliente',
                'adm_contri.razon_social',
                'adm_contri.nro_documento',
                'guia_ven.serie',
                'guia_ven.numero',
                'sis_sede.id_empresa'
            )
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'guia_ven.id_sede')
            ->join('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'guia_ven.id_cliente')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->where('guia_ven.id_guia_ven', $id)
            ->first();

        $detalle = DB::table('almacen.guia_ven_det')
            ->select(
                'guia_ven_det.*',
                'guia_ven.serie',
                'guia_ven.numero',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.part_number',
                'alm_und_medida.abreviatura',
                'oc_propias_view.monto_total',
                'oc_propias_view.moneda_oc',
                'oc_propias_view.nombre_largo_responsable',
                DB::raw("(SELECT SUM(doc_ven_det.cantidad) FROM almacen.doc_ven_det
                    WHERE doc_ven_det.id_guia_ven_det = guia_ven_det.id_guia_ven_det
                    and doc_ven_det.estado != 7) AS cantidad_facturada")
            )
            ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')
            ->leftjoin('almacen.orden_despacho_det', 'orden_despacho_det.id_od_detalle', '=', 'guia_ven_det.id_od_det')
            ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'orden_despacho_det.id_detalle_requerimiento')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftjoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftjoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_ven_det.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where('guia_ven_det.id_guia_ven', $id)
            ->orderBy('guia_ven_det.id_guia_ven_det')
            ->get();

        $igv = DB::table('contabilidad.cont_impuesto')
            ->where('codigo', 'IGV')->first();

        return response()->json(['guia' => $guia, 'detalle' => $detalle, 'igv' => $igv->porcentaje]);
    }

    public function obtenerGuiaVentaSeleccionadas(Request $request)
    {
        $detalle = DB::table('almacen.guia_ven_det')
            ->select(
                'guia_ven_det.*',
                'guia_ven.serie',
                'guia_ven.numero',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.part_number',
                'alm_und_medida.abreviatura',
                'oc_propias_view.monto_total',
                'oc_propias_view.moneda_oc',
                'oc_propias_view.nombre_largo_responsable',
                DB::raw("(SELECT SUM(doc_ven_det.cantidad) FROM almacen.doc_ven_det
                    WHERE doc_ven_det.id_guia_ven_det = guia_ven_det.id_guia_ven_det
                    and doc_ven_det.estado != 7) AS cantidad_facturada")
            )
            ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')
            ->leftjoin('almacen.orden_despacho_det', 'orden_despacho_det.id_od_detalle', '=', 'guia_ven_det.id_od_det')
            ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'orden_despacho_det.id_detalle_requerimiento')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftjoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftjoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_ven_det.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->whereIn('guia_ven_det.id_guia_ven', $request->id_guias_seleccionadas)
            ->get();

        $igv = DB::table('contabilidad.cont_impuesto')
            ->where('codigo', 'IGV')->first();

        return response()->json(['detalle' => $detalle, 'igv' => $igv->porcentaje]);
    }

    public function obtenerRequerimiento($id)
    {
        $req = DB::table('almacen.alm_req')
            ->select(
                'alm_req.id_requerimiento',
                'alm_req.id_cliente',
                'adm_contri.razon_social',
                'adm_contri.nro_documento',
                'alm_req.codigo',
                'alm_req.concepto',
                'sis_sede.id_empresa'
            )
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->join('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->where('alm_req.id_requerimiento', $id)
            ->first();

        $detalle = DB::table('almacen.alm_det_req')
            ->select(
                'alm_det_req.*',
                'alm_req.codigo as cod_req',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.part_number',
                'alm_prod.id_unidad_medida as id_unid_med',
                'alm_und_medida.abreviatura',
                'oc_propias_view.monto_total',
                'oc_propias_view.moneda_oc',
                'oc_propias_view.nombre_largo_responsable',
                DB::raw("(SELECT SUM(doc_ven_det.cantidad) FROM almacen.doc_ven_det
                    WHERE doc_ven_det.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento
                    and doc_ven_det.estado != 7) AS cantidad_facturada")
            )
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftjoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftjoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where('alm_det_req.id_requerimiento', $id)
            ->where('alm_det_req.entrega_cliente', true)
            ->get();

        $igv = DB::table('contabilidad.cont_impuesto')
            ->where('codigo', 'IGV')->first();

        return response()->json(['req' => $req, 'detalle' => $detalle, 'igv' => $igv->porcentaje]);
    }

    public function guardar_doc_venta(Request $request)
    {
        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $fecha = date('Y-m-d H:i:s');

            $id_doc = DB::table('almacen.doc_ven')->insertGetId(
                [
                    'serie' => strtoupper($request->serie_doc),
                    'numero' => str_pad(intval($request->numero_doc), 7, "0", STR_PAD_LEFT),
                    'id_tp_doc' => $request->id_tp_doc,
                    'id_cliente' => $request->id_cliente,
                    'fecha_emision' => $request->fecha_emision_doc,
                    'fecha_vcmto' => $request->fecha_vencimiento,
                    'id_condicion' => $request->id_condicion,
                    'credito_dias' => $request->credito_dias,
                    'id_empresa' => $request->id_empresa,
                    // 'id_sede' => $request->id_sede,
                    'moneda' => $request->moneda,
                    'sub_total' => $request->sub_total,
                    'total_igv' => $request->igv,
                    'porcen_igv' => $request->porcentaje_igv,
                    'total_a_pagar' => round($request->total, 2),
                    'usuario' => $id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                'id_doc_ven'
            );

            $items = json_decode($request->detalle_items);

            foreach ($items as $item) {
                DB::table('almacen.doc_ven_det')
                    ->insert([
                        'id_doc' => $id_doc,
                        'id_guia_ven_det' => isset($item->id_guia_ven_det) ? $item->id_guia_ven_det : null,
                        'id_detalle_requerimiento' => isset($item->id_detalle_requerimiento) ? $item->id_detalle_requerimiento : null,
                        'id_item' => $item->id_producto,
                        'cantidad' => $item->cantidad,
                        'id_unid_med' => $item->id_unid_med,
                        'precio_unitario' => $item->precio,
                        'sub_total' => $item->sub_total,
                        'porcen_dscto' => $item->porcentaje_dscto,
                        'total_dscto' => $item->total_dscto,
                        'precio_total' => $item->total,
                        'estado' => 1,
                        'fecha_registro' => $fecha,
                    ]);
            }

            DB::commit();

            return response()->json($id_doc);
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
        }
    }

    public function documentos_ver($id_doc)
    {
        $docs = DB::table('almacen.doc_ven')
            ->select(
                'doc_ven.id_doc_ven',
                'doc_ven.serie',
                'doc_ven.numero',
                'doc_ven.fecha_emision',
                'cont_tp_doc.descripcion as tp_doc',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'sis_moneda.simbolo',
                'doc_ven.total_a_pagar',
                'doc_ven.sub_total',
                'doc_ven.total_igv',
                'log_cdn_pago.descripcion as condicion_descripcion',
                'empresa.razon_social as empresa_razon_social',
                'doc_ven.credito_dias',
                'doc_ven.tipo_cambio'
            )
            ->join('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'doc_ven.id_cliente')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->join('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_ven.id_tp_doc')
            ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_ven.moneda')
            ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_ven.id_condicion')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'doc_ven.id_empresa')
            ->join('contabilidad.adm_contri as empresa', 'empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->where('doc_ven.id_doc_ven', $id_doc)
            ->distinct()
            ->get();

        $detalles = DB::table('almacen.doc_ven_det')
            ->select(
                'doc_ven_det.*',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.part_number',
                'alm_und_medida.abreviatura',
                'guia_ven.serie',
                'guia_ven.numero',
                'alm_req.codigo as codigo_req'
            )
            ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'doc_ven_det.id_detalle_requerimiento')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftjoin('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven_det', '=', 'doc_ven_det.id_guia_ven_det')
            ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'doc_ven_det.id_item')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'doc_ven_det.id_unid_med')
            ->where('doc_ven_det.id_doc', $id_doc)
            ->get();

        return response()->json(['docs' => $docs, 'detalles' => $detalles]);
    }

    public function anular_doc_ven(Request $request)
    {
        try {
            DB::beginTransaction();
            $id_usuario = Auth::user()->id_usuario;

            DB::table('almacen.doc_ven')
                ->where('doc_ven.id_doc_ven', $request->id_doc_ven_anula)
                ->update([
                    'estado' => 7,
                    'fecha_anulacion' => new Carbon(),
                    'usuario_anulacion' => $id_usuario,
                    'comentario_anulacion' => $request->observacion,
                ]);

            DB::table('almacen.doc_ven_det')
                ->where('doc_ven_det.id_doc', $request->id_doc_ven_anula)
                ->update(['estado' => 7]);

            $msj = 'success';
            $tipo = 'Se anuló correctamente el documento.';

            DB::commit();
            return response()->json([
                'tipo' => $tipo,
                'mensaje' => $msj, 200
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al anular la salida. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    public function obtenerArchivosOc(Request $request)
    {
        $dataEnviar['id'] = $request->id;
        $dataEnviar['tipo'] = $request->tipo;
        $cUrl = curl_init();
        curl_setopt($cUrl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($cUrl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($cUrl, CURLOPT_VERBOSE, true);
        curl_setopt($cUrl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($cUrl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cUrl, CURLOPT_URL, 'https://mgcp.okccloud.com/mgcp/ordenes-compra/propias/obtener-informacion-adicional');
        curl_setopt($cUrl, CURLOPT_POST, true);
        curl_setopt($cUrl, CURLOPT_POSTFIELDS, (http_build_query($dataEnviar)));
        curl_setopt($cUrl, CURLOPT_HEADER, 0);
        return curl_exec($cUrl); //Devuelve el JSON que recibió de MGCP
    }
}
