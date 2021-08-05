<?php

namespace App\Http\Controllers\Tesoreria\Facturacion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AlmacenController as GenericoAlmacenController;

class PendientesFacturacionController extends Controller
{
    function view_pendientes_facturacion()
    {
        $tp_doc = GenericoAlmacenController::mostrar_tp_doc_cbo();
        $sedes = GenericoAlmacenController::mostrar_sedes_cbo();
        $monedas = GenericoAlmacenController::mostrar_moneda_cbo();
        $condiciones = GenericoAlmacenController::mostrar_condiciones_cbo();

        return view(
            'tesoreria/facturacion/pendientesFacturacion',
            compact('tp_doc', 'sedes', 'monedas', 'condiciones')
        );
    }

    public function listarGuiasVentaPendientes()
    {
        $data = DB::table('almacen.guia_ven')
            ->select(
                'guia_ven.*',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'sis_usua.nombre_corto',
                'usu_trans.nombre_corto as nombre_corto_trans',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'alm_almacen.descripcion as almacen_descripcion',
                'sis_sede.descripcion as sede_descripcion',
                'oc_propias_view.nro_orden as orden_am',
                'oc_propias_view.codigo_oportunidad',
                'oc_propias_view.nombre_entidad',
                'oc_propias_view.id as id_oc_propia',
                'oc_propias_view.url_oc_fisica',
                'oc_propias_view.tipo',
                'oc_propias_view.monto_total',
                'alm_req.codigo as codigo_req',
                'oc_propias_view.moneda_oc',
                'alm_req.id_requerimiento',
                'alm_req.tiene_transformacion',
                'sede_req.descripcion as sede_descripcion_req',
                'trans.codigo as codigo_trans',
                DB::raw("(SELECT count(distinct id_doc_ven) FROM almacen.doc_ven AS d
                    INNER JOIN almacen.guia_ven_det AS guia
                        on(guia.id_guia_ven = guia_ven.id_guia_ven)
                    INNER JOIN almacen.doc_ven_det AS doc
                        on(doc.id_guia_ven_det = guia.id_guia_ven_det)
                    WHERE d.id_doc_ven = doc.id_doc
                      and doc.estado != 7) AS count_facturas"),
                DB::raw("(SELECT distinct id_doc_ven FROM almacen.doc_ven AS d
                    INNER JOIN almacen.guia_ven_det AS guia on(
                        guia.id_guia_ven = guia_ven.id_guia_ven)
                    INNER JOIN almacen.doc_ven_det AS doc on(
                        doc.id_guia_ven_det = guia.id_guia_ven_det and
                        doc.estado != 7)
                    WHERE d.id_doc_ven = doc.id_doc
                    limit 1) AS id_doc_ven"),
                DB::raw("(SELECT distinct (c.abreviatura) || ' ' || (d.serie) || '-' || (d.numero) as serie_numero FROM almacen.doc_ven AS d
                    INNER JOIN almacen.guia_ven_det AS guia on(
                        guia.id_guia_ven = guia_ven.id_guia_ven)
                    INNER JOIN almacen.doc_ven_det AS doc on(
                        doc.id_guia_ven_det = guia.id_guia_ven_det and
                        doc.estado != 7)
                    INNER JOIN almacen.doc_ven AS dv on(
                        dv.id_doc_ven = doc.id_doc and
                        dv.estado != 7)
                    INNER JOIN contabilidad.cont_tp_doc AS c on(
                        c.id_tp_doc = dv.id_tp_doc and
                        c.estado != 7)
                    WHERE d.id_doc_ven = doc.id_doc
                    limit 1) AS doc_ven"),
                DB::raw("(SELECT distinct d.fecha_emision FROM almacen.doc_ven AS d
                    INNER JOIN almacen.guia_ven_det AS guia on(
                        guia.id_guia_ven = guia_ven.id_guia_ven)
                    INNER JOIN almacen.doc_ven_det AS doc on(
                        doc.id_guia_ven_det = guia.id_guia_ven_det and
                        doc.estado != 7)
                    WHERE d.id_doc_ven = doc.id_doc
                    limit 1) AS fecha_doc_ven"),
            )
            ->join('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'guia_ven.id_cliente')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'guia_ven.id_almacen')
            ->join('almacen.trans', 'trans.id_transferencia', '=', 'guia_ven.id_transferencia')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'trans.id_requerimiento')
            ->join('administracion.sis_sede as sede_req', 'sede_req.id_sede', '=', 'alm_req.id_sede')
            ->leftjoin('configuracion.sis_usua as usu_trans', 'usu_trans.id_usuario', '=', 'trans.responsable_origen')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'guia_ven.id_sede')
            ->leftjoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'guia_ven.estado')
            ->where('guia_ven.estado', 1)
            ->where('guia_ven.id_operacion', 1);

        return datatables($data)->toJson();
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
                'oc_propias_view.nombre_entidad',
                'oc_propias_view.id as id_oc_propia',
                'oc_propias_view.url_oc_fisica',
                'oc_propias_view.tipo',
                'oc_propias_view.monto_total',
                'oc_propias_view.nombre_largo_responsable',
                'alm_req.codigo as codigo_req',
                'oc_propias_view.moneda_oc',

                DB::raw("(SELECT count(distinct id_doc_ven) FROM almacen.doc_ven AS d
                    INNER JOIN almacen.alm_det_req AS req
                        on( req.id_requerimiento = alm_req.id_requerimiento)
                    INNER JOIN almacen.doc_ven_det AS doc
                        on( doc.id_detalle_requerimiento = req.id_detalle_requerimiento 
                            and doc.estado != 7)
                    WHERE d.id_doc_ven = doc.id_doc) AS count_facturas"),

                DB::raw("(SELECT distinct id_doc_ven FROM almacen.doc_ven AS d
                    INNER JOIN almacen.alm_det_req AS req
                        on( req.id_requerimiento = alm_req.id_requerimiento)
                    INNER JOIN almacen.doc_ven_det AS doc
                        on( doc.id_detalle_requerimiento = req.id_detalle_requerimiento
                            and doc.estado != 7)
                    WHERE d.id_doc_ven = doc.id_doc
                    limit 1) AS id_doc_ven"),

                DB::raw("(SELECT distinct (c.abreviatura) || ' ' || (d.serie) || '-' || (d.numero) as serie_numero 
                    FROM almacen.doc_ven AS d
                    INNER JOIN almacen.alm_det_req AS req
                        on( req.id_requerimiento = alm_req.id_requerimiento)
                    INNER JOIN almacen.doc_ven_det AS doc
                        on( doc.id_detalle_requerimiento = req.id_detalle_requerimiento
                            and doc.estado != 7)
                    INNER JOIN almacen.doc_ven AS dv on(
                        dv.id_doc_ven = doc.id_doc and
                        dv.estado != 7)
                    INNER JOIN contabilidad.cont_tp_doc AS c on(
                        c.id_tp_doc = dv.id_tp_doc and
                        c.estado != 7)
                    WHERE d.id_doc_ven = doc.id_doc
                    limit 1) AS doc_ven"),

                DB::raw("(SELECT distinct d.fecha_emision FROM almacen.doc_ven AS d
                    INNER JOIN almacen.alm_det_req AS req
                        on( req.id_requerimiento = alm_req.id_requerimiento)
                    INNER JOIN almacen.doc_ven_det AS doc
                        on( doc.id_detalle_requerimiento = req.id_detalle_requerimiento
                            and doc.estado != 7)
                    WHERE d.id_doc_ven = doc.id_doc
                    limit 1) AS fecha_doc_ven")
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
                'guia_ven.id_sede'
            )
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
                'oc_propias_view.nombre_largo_responsable'
                // 'alm_req.id_sede'
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
                'alm_req.id_sede'
            )
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
                'oc_propias_view.nombre_largo_responsable'
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
                    'numero' => $request->numero_doc,
                    'id_tp_doc' => $request->id_tp_doc,
                    'id_cliente' => $request->id_cliente,
                    'fecha_emision' => $request->fecha_emision_doc,
                    'fecha_vcmto' => $request->fecha_emision_doc,
                    'id_sede' => $request->id_sede,
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
                // 'log_cdn_pago.descripcion as condicion_descripcion',
                'sis_sede.descripcion as sede_descripcion',
                'doc_ven.credito_dias',
                'doc_ven.tipo_cambio'
            )
            ->join('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'doc_ven.id_cliente')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->join('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_ven.id_tp_doc')
            ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_ven.moneda')
            // ->leftJoin('logistica.log_cdn_pago','log_cdn_pago.id_condicion_pago','=','doc_ven.id_condicion')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'doc_ven.id_sede')
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

    public function anular_doc_ven($id_doc)
    {
        $update = DB::table('almacen.doc_ven')
            ->where('doc_ven.id_doc_ven', $id_doc)
            ->update(['estado' => 7]);

        $update = DB::table('almacen.doc_ven_det')
            ->where('doc_ven_det.id_doc', $id_doc)
            ->update(['estado' => 7]);

        return response()->json($update);
    }
}
