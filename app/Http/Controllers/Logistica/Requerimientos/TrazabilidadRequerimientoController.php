<?php

namespace App\Http\Controllers\Logistica\Requerimientos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TrazabilidadRequerimientoController extends Controller
{
    public function mostrarDocumentosByRequerimiento($id_requerimiento)
    {
        $requerimiento = DB::table('almacen.alm_req')
            ->select('alm_req.id_requerimiento', 'alm_req.codigo', 'alm_req.fecha_requerimiento', 'alm_req.concepto', 'alm_req.estado', 'adm_estado_doc.estado_doc AS estado_descripcion')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
            ->where('id_requerimiento', $id_requerimiento)
            ->first();

        $ordenes = DB::table('logistica.log_det_ord_compra')
            ->select('log_ord_compra.*', 'estados_compra.descripcion AS estado_descripcion')
            ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftJoin('logistica.estados_compra', 'log_ord_compra.estado', '=', 'estados_compra.id_estado')

            ->where([
                ['alm_req.id_requerimiento', '=', $id_requerimiento],
                ['log_ord_compra.estado', '!=', 7]
            ])
            ->distinct()
            ->get();

        $reservas = DB::table('almacen.alm_reserva')
            ->select('alm_reserva.*')
            ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'alm_reserva.id_detalle_requerimiento')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->where([
                ['alm_req.id_requerimiento', '=', $id_requerimiento],
                ['alm_reserva.estado', '!=', 7]
            ])
            ->whereNull('id_guia_com_det')
            ->distinct()
            ->get();

        $guias = DB::table('almacen.alm_det_req')
            ->select(
                'mov_alm.id_mov_alm as id_ingreso',
                'mov_alm.codigo as codigo_ingreso',
                'guia_com.id_guia',
                'guia_com.estado as estado_guia',
                'guia_com.serie as serie_guia',
                'guia_com.numero as numero_guia'
            )
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->join('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')
            ->join('almacen.guia_com_det', 'guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden')
            ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->join('almacen.mov_alm_det', 'mov_alm_det.id_guia_com_det', '=', 'guia_com_det.id_guia_com_det')
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            ->where([
                ['alm_req.id_requerimiento', '=', $id_requerimiento],
                ['log_det_ord_compra.estado', '!=', 7],
                ['guia_com.estado', '!=', 7],
                ['mov_alm.estado', '!=', 7],
            ])
            ->distinct()
            ->get();

        $docs = DB::table('almacen.alm_det_req')
            ->select(
                'doc_com.id_doc_com',
                'doc_com.serie as serie_doc',
                'doc_com.numero as numero_doc',
                'doc_com.estado as estado_doc',
            )
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->join('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')
            ->join('almacen.guia_com_det', 'guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden')
            ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->join('almacen.mov_alm_det', 'mov_alm_det.id_guia_com_det', '=', 'guia_com_det.id_guia_com_det')
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            ->join('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', '=', 'guia_com_det.id_guia_com_det')
            ->join('almacen.doc_com', function ($join) {
                $join->on('doc_com.id_doc_com', '=', 'doc_com_det.id_doc');
                $join->where('doc_com.estado', '!=', 7);
            })
            ->where([
                ['alm_req.id_requerimiento', '=', $id_requerimiento],
                ['log_det_ord_compra.estado', '!=', 7],
                ['guia_com.estado', '!=', 7],
                ['mov_alm.estado', '!=', 7]
            ])
            ->distinct()
            ->get();

        $transferencias = DB::table('almacen.trans')
            ->select(
                'trans.*',
                'guia_com.serie as serie_guia_com',
                'guia_com.numero as numero_guia_com',
                'guia_ven.serie as serie_guia_ven',
                'guia_ven.numero as numero_guia_ven',
            )
            ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'trans.id_guia_com')
            ->join('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'trans.id_guia_ven')
            ->where([
                ['trans.id_requerimiento', '=', $id_requerimiento],
                ['trans.estado', '!=', 7]
            ])
            ->get();

        $transformaciones = DB::table('almacen.transformacion')
            ->select('transformacion.*')
            ->join('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'transformacion.id_od')
            ->where([
                ['orden_despacho.id_requerimiento', '=', $id_requerimiento],
                ['orden_despacho.estado', '!=', 7],
                ['transformacion.estado', '!=', 7],
            ])
            ->get();

        $despacho = DB::table('almacen.orden_despacho')
            ->select('orden_despacho.*')
            ->where([
                ['orden_despacho.id_requerimiento', '=', $id_requerimiento],
                ['orden_despacho.aplica_cambios', '=', false],
                ['orden_despacho.estado', '!=', 7]
            ])
            ->first();

        $guia_transportista = DB::table('almacen.orden_despacho')
            ->select(
                'orden_despacho.serie',
                'orden_despacho.numero',
                'orden_despacho.fecha_transportista',
                'orden_despacho.codigo_envio',
                'orden_despacho.importe_flete'
            )
            ->where([
                ['orden_despacho.id_requerimiento', '=', $id_requerimiento],
                ['orden_despacho.aplica_cambios', '=', false],
                ['orden_despacho.estado', '!=', 7]
            ])
            ->first();

        $estados_envio = DB::table('almacen.orden_despacho_obs')
            ->select('orden_despacho_obs.*', 'estado_envio.descripcion as accion_descripcion')
            ->join('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'orden_despacho_obs.id_od')
            ->join('almacen.estado_envio', 'estado_envio.id_estado', '=', 'orden_despacho_obs.accion')
            ->where([
                ['orden_despacho.id_requerimiento', '=', $id_requerimiento],
                ['orden_despacho.aplica_cambios', '=', false],
                ['orden_despacho.estado', '!=', 7]
            ])
            ->get();

        return response()->json([
            'requerimiento' => $requerimiento,
            'ordenes' => $ordenes,
            'reservado' => (count($reservas) > 0 ? true : false),
            // 'reservas' => $reservas,
            'ingresos' => $guias,
            'docs' => $docs,
            'transferencias' => $transferencias,
            'transformaciones' => $transformaciones,
            'despacho' => $despacho,
            'estados_envio' => $estados_envio,
            'guia_transportista' => $guia_transportista,
        ]);
    }
}
