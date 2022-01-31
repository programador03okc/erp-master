<?php

namespace App\Http\Controllers\Tesoreria;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class RegistroPagoController extends Controller
{
    function view_main_tesoreria()
    {
        $pagos_pendientes = DB::table('almacen.alm_req')
            ->where('estado', 8)->count();

        $confirmaciones_pendientes = DB::table('almacen.alm_req')
            ->where([['estado', '=', 19], ['confirmacion_pago', '=', false]])->count();

        return view('tesoreria/main', compact('pagos_pendientes', 'confirmaciones_pendientes'));
    }

    function view_pendientes_pago()
    {
        return view('tesoreria/pagos/pendientesPago');
    }

    public function listarRequerimientosPago()
    {
        $data = DB::table('tesoreria.requerimiento_pago')
            ->select(
                'requerimiento_pago.*',
                'adm_contri.razon_social',
                'empresa.razon_social as razon_social_empresa',
                'sis_moneda.simbolo',
                'sis_grupo.descripcion as grupo_descripcion',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'sis_sede.descripcion as sede_descripcion',
                // 'adm_cta_contri.nro_cuenta',
                DB::raw("(SELECT sum(total_pago) FROM tesoreria.req_pagos
                        WHERE req_pagos.id_requerimiento_pago = requerimiento_pago.id_requerimiento_pago
                        and req_pagos.estado != 7) AS suma_pagado")
            )
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'requerimiento_pago.id_proveedor')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'requerimiento_pago.id_moneda')
            ->leftJoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'requerimiento_pago.id_estado')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'requerimiento_pago.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'requerimiento_pago.id_empresa')
            ->join('contabilidad.adm_contri as empresa', 'empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->join('configuracion.sis_grupo', 'sis_grupo.id_grupo', '=', 'requerimiento_pago.id_grupo')
            // ->leftJoin('contabilidad.adm_cta_contri', 'adm_cta_contri.id_cuenta_contribuyente', '=', 'requerimiento_pago.id_cuenta_proveedor')
            ->where([['requerimiento_pago.id_estado', '!=', 7]]);

        return datatables($data)->toJson();
    }

    public function listarOrdenesCompra()
    {
        $data = DB::table('logistica.log_ord_compra')
            ->select(
                'log_ord_compra.*',
                'adm_contri.razon_social',
                'estados_compra.descripcion as estado_doc',
                'sis_moneda.simbolo',
                'log_cdn_pago.descripcion AS condicion_pago',
                'sis_sede.descripcion as sede_descripcion',
                // 'req_pagos.total_pago','req_pagos.adjunto',
                // 'req_pagos.fecha_pago','req_pagos.observacion',
                // 'registrado_por.nombre_corto as usuario_pago',
                'adm_cta_contri.nro_cuenta',
                DB::raw("(SELECT sum(subtotal) FROM logistica.log_det_ord_compra
                        WHERE log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra
                        and log_det_ord_compra.estado != 7) AS suma_total"),
                DB::raw("(SELECT sum(total_pago) FROM tesoreria.req_pagos
                        WHERE req_pagos.id_oc = log_ord_compra.id_orden_compra
                        and req_pagos.estado != 7) AS suma_pagado")
            )
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->join('logistica.estados_compra', 'estados_compra.id_estado', '=', 'log_ord_compra.estado_pago')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_ord_compra.id_condicion')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
            // ->leftJoin('tesoreria.req_pagos','req_pagos.id_oc','=','log_ord_compra.id_orden_compra')
            // ->leftJoin('configuracion.sis_usua as registrado_por','registrado_por.id_usuario','=','req_pagos.registrado_por')
            ->leftJoin('contabilidad.adm_cta_contri', 'adm_cta_contri.id_cuenta_contribuyente', '=', 'log_ord_compra.id_cta_principal')
            ->where([['log_ord_compra.id_condicion', '=', 1], ['log_ord_compra.estado', '!=', 7]]);

        return datatables($data)->toJson();
    }

    public function listarComprobantesPagos()
    {
        $data = DB::table('almacen.doc_com')
            ->select(
                'doc_com.id_doc_com',
                'doc_com.serie',
                'doc_com.numero',
                'adm_contri.razon_social',
                'doc_com.fecha_emision',
                'doc_com.fecha_vcmto',
                'doc_com.serie',
                'doc_com.total_a_pagar',
                'doc_com.estado',
                'doc_com.credito_dias',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'sis_moneda.simbolo',
                'log_cdn_pago.descripcion AS condicion_pago',
                'cont_tp_doc.descripcion as tipo_documento',
                'adm_cta_contri.nro_cuenta',
                DB::raw("(SELECT sum(total_pago) FROM tesoreria.req_pagos
                      WHERE req_pagos.id_doc_com = doc_com.id_doc_com
                        and req_pagos.estado != 7) AS suma_pagado")
            )
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'doc_com.id_proveedor')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'doc_com.estado')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_com.moneda')
            ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_com.id_condicion')
            ->leftJoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_com.id_tp_doc')
            ->leftJoin('contabilidad.adm_cta_contri', 'adm_cta_contri.id_cuenta_contribuyente', '=', 'doc_com.id_cta_bancaria')
            ->where('doc_com.id_condicion', 2)
            ->whereIn('doc_com.estado', [1, 9]);

        // return datatables($data)->toJson();
        return DataTables::of($data)
            ->editColumn('fecha_emision', function ($data) {
                return ($data->fecha_emision !== null ? date('d-m-Y', strtotime($data->fecha_emision)) : '');
            })
            ->editColumn('condicion_pago', function ($data) {
                return ($data->condicion_pago !== null ? ($data->condicion_pago . ' ' . $data->credito_dias . ' días') : '');
            })
            ->editColumn('fecha_vcmto', function ($data) {
                return ($data->fecha_vcmto !== null ? date('d-m-Y', strtotime($data->fecha_vcmto)) : '');
            })
            ->addColumn('total_a_pagar_format', function ($data) {
                return ($data->total_a_pagar !== null ? number_format($data->total_a_pagar, 2) : '0.00');
            })
            ->addColumn('span_estado', function ($data) {
                $estado = ($data->estado == 9 ? 'Pagada' : $data->estado_doc);
                return '<span class="label label-' . $data->bootstrap_color . '">' . $estado . '</span>';
            })
            ->rawColumns(['span_estado', 'total_a_pagar_format'])

            ->make(true);
    }

    public function pagosComprobante($id_doc_com)
    {
        $detalles = DB::table('tesoreria.req_pagos')
            ->select('req_pagos.*', 'sis_usua.nombre_corto', 'sis_moneda.simbolo')
            ->leftJoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'req_pagos.id_doc_com')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_com.moneda')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'req_pagos.registrado_por')
            ->where([
                ['req_pagos.id_doc_com', '=', $id_doc_com],
                ['req_pagos.estado', '!=', 7]
            ])
            ->get();

        return response()->json($detalles);
    }

    public function pagosOrdenes($id_oc)
    {
        $detalles = DB::table('tesoreria.req_pagos')
            ->select('req_pagos.*', 'sis_usua.nombre_corto', 'sis_moneda.simbolo')
            ->leftJoin('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'req_pagos.id_oc')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'req_pagos.registrado_por')
            ->where([
                ['req_pagos.id_oc', '=', $id_oc],
                ['req_pagos.estado', '!=', 7]
            ])
            ->get();

        return response()->json($detalles);
    }

    public function pagosRequerimientos($id_requerimiento_pago)
    {
        $detalles = DB::table('tesoreria.req_pagos')
            ->select('req_pagos.*', 'sis_usua.nombre_corto', 'sis_moneda.simbolo')
            ->leftJoin('tesoreria.requerimiento_pago', 'requerimiento_pago.id_requerimiento_pago', '=', 'req_pagos.id_requerimiento_pago')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'requerimiento_pago.id_moneda')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'req_pagos.registrado_por')
            ->where([
                ['req_pagos.id_requerimiento_pago', '=', $id_requerimiento_pago],
                ['req_pagos.estado', '!=', 7]
            ])
            ->get();

        return response()->json($detalles);
    }

    public function detalleComprobante($id_doc_com)
    {
        $detalles = DB::table('almacen.doc_com_det')
            ->select(
                'doc_com_det.*',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'alm_prod.descripcion as producto_descripcion',
                'alm_prod.codigo as producto_codigo',
                'alm_und_medida.abreviatura',
                'alm_prod.part_number'
            )
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'doc_com_det.id_item')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'doc_com_det.id_unid_med')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'doc_com_det.estado')
            ->where([
                ['doc_com_det.id_doc', '=', $id_doc_com],
                ['doc_com_det.estado', '!=', 7]
            ])
            ->get();

        return response()->json($detalles);
    }

    function procesarPago(Request $request)
    {
        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $file = $request->file('adjunto');

            $id_pago = DB::table('tesoreria.req_pagos')
                ->insertGetId([
                    'id_oc' => $request->id_oc,
                    'id_requerimiento_pago' => $request->id_requerimiento_pago,
                    'id_doc_com' => $request->id_doc_com,
                    'fecha_pago' => $request->fecha_pago,
                    'observacion' => $request->observacion,
                    'total_pago' => round($request->total_pago, 2),
                    'registrado_por' => $id_usuario,
                    'estado' => 1,
                    'fecha_registro' => date('Y-m-d H:i:s')
                ], 'id_pago');

            if (isset($file)) {
                //obtenemos el nombre del archivo
                $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                $nombre = $request->codigo . '.' . $id_pago . '.' . $extension;
                //indicamos que queremos guardar un nuevo archivo en el disco local
                File::delete(public_path('tesoreria/pagos/' . $nombre));
                Storage::disk('archivos')->put('tesoreria/pagos/' . $nombre, File::get($file));

                DB::table('tesoreria.req_pagos')
                    ->where('id_pago', $id_pago)
                    ->update(['adjunto' => $nombre]);
            }

            if (floatval($request->total_pago) >= floatval(round($request->total, 2))) {

                if ($request->id_oc !== null) {
                    DB::table('logistica.log_ord_compra')
                        ->where('id_orden_compra', $request->id_oc)
                        ->update(['estado_pago' => 9]); //pagada
                } else if ($request->id_doc_com !== null) {
                    DB::table('almacen.doc_com')
                        ->where('id_doc_com', $request->id_doc_com)
                        ->update(['estado' => 9]); //procesado
                } else if ($request->id_requerimiento_pago !== null) {
                    DB::table('tesoreria.requerimiento_pago')
                        ->where('id_requerimiento_pago', $request->id_requerimiento_pago)
                        ->update(['estado' => 9]); //procesado
                }
            }

            DB::commit();
            return response()->json($id_pago);
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }
}
