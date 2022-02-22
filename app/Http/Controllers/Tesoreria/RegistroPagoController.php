<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Rrhh\Persona;
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
        $empresas = AlmacenController::select_empresa();
        return view('tesoreria/pagos/pendientesPago', compact('empresas'));
    }

    public function listarRequerimientosPago()
    {
        $data = DB::table('tesoreria.requerimiento_pago')
            ->select(
                'requerimiento_pago.*',
                'adm_prioridad.descripcion as prioridad',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'empresa.razon_social as razon_social_empresa',
                'sis_moneda.simbolo',
                'sis_grupo.descripcion as grupo_descripcion',
                'requerimiento_pago_estado.descripcion as estado_doc',
                'requerimiento_pago_estado.bootstrap_color',
                'sis_sede.descripcion as sede_descripcion',
                'adm_cta_contri.nro_cuenta',
                'adm_cta_contri.nro_cuenta_interbancaria',
                'adm_tp_cta.descripcion as tipo_cuenta',
                'banco_contribuyente.razon_social as banco_contribuyente',
                'rrhh_cta_banc.nro_cuenta as nro_cuenta_persona',
                'rrhh_cta_banc.nro_cci as nro_cci_persona',
                'tp_cta_persona.descripcion as tipo_cuenta_persona',
                'banco_persona.razon_social as banco_persona',
                DB::raw("(SELECT sum(total_pago) FROM tesoreria.registro_pago
                        WHERE registro_pago.id_requerimiento_pago = requerimiento_pago.id_requerimiento_pago
                        and registro_pago.estado != 7) AS suma_pagado")
            )
            // ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'requerimiento_pago.id_proveedor')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'requerimiento_pago.id_contribuyente')
            ->leftjoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'requerimiento_pago.id_persona')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'requerimiento_pago.id_moneda')
            ->leftJoin('tesoreria.requerimiento_pago_estado', 'requerimiento_pago_estado.id_requerimiento_pago_estado', '=', 'requerimiento_pago.id_estado')
            ->join('administracion.adm_prioridad', 'adm_prioridad.id_prioridad', '=', 'requerimiento_pago.id_prioridad')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'requerimiento_pago.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'requerimiento_pago.id_empresa')
            ->join('contabilidad.adm_contri as empresa', 'empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.adm_cta_contri', 'adm_cta_contri.id_cuenta_contribuyente', '=', 'requerimiento_pago.id_cuenta_contribuyente')
            ->leftJoin('contabilidad.adm_tp_cta', 'adm_tp_cta.id_tipo_cuenta', '=', 'adm_cta_contri.id_tipo_cuenta')
            ->leftJoin('contabilidad.cont_banco as bco_contribuyente', 'bco_contribuyente.id_banco', '=', 'adm_cta_contri.id_banco')
            ->leftJoin('contabilidad.adm_contri as banco_contribuyente', 'banco_contribuyente.id_contribuyente', '=', 'bco_contribuyente.id_contribuyente')
            ->leftJoin('rrhh.rrhh_cta_banc', 'rrhh_cta_banc.id_cuenta_bancaria', '=', 'requerimiento_pago.id_cuenta_persona')
            ->leftJoin('contabilidad.cont_banco as bco_persona', 'bco_persona.id_banco', '=', 'rrhh_cta_banc.id_banco')
            ->leftJoin('contabilidad.adm_contri as banco_persona', 'banco_persona.id_contribuyente', '=', 'bco_persona.id_contribuyente')
            ->leftJoin('contabilidad.adm_tp_cta as tp_cta_persona', 'tp_cta_persona.id_tipo_cuenta', '=', 'rrhh_cta_banc.id_tipo_cuenta')
            ->join('configuracion.sis_grupo', 'sis_grupo.id_grupo', '=', 'requerimiento_pago.id_grupo')
            ->where([['requerimiento_pago.id_estado', '!=', 7]]);

        return datatables($data)->addColumn('persona', function ($data) {
            $persona = Persona::find($data->id_persona);
            if (!empty($persona)) {
                return ([$persona]);
            } else {
                return ([]);
            };
        })->toJson();
    }

    public function listarOrdenesCompra()
    {
        $data = DB::table('logistica.log_ord_compra')
            ->select(
                'log_ord_compra.*',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'empresa.razon_social as razon_social_empresa',
                'requerimiento_pago_estado.descripcion as estado_doc',
                'requerimiento_pago_estado.bootstrap_color',
                'sis_moneda.simbolo',
                'log_cdn_pago.descripcion AS condicion_pago',
                'sis_sede.descripcion as sede_descripcion',
                'adm_cta_contri.nro_cuenta',
                'adm_cta_contri.nro_cuenta_interbancaria',
                'adm_tp_cta.descripcion as tipo_cuenta',
                'banco_contribuyente.razon_social as banco_contribuyente',
                'rrhh_cta_banc.nro_cuenta as nro_cuenta_persona',
                'rrhh_cta_banc.nro_cci as nro_cci_persona',
                'tp_cta_persona.descripcion as tipo_cuenta_persona',
                'banco_persona.razon_social as banco_persona',
                DB::raw("(SELECT sum(subtotal) FROM logistica.log_det_ord_compra
                        WHERE log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra
                        and log_det_ord_compra.estado != 7) AS suma_total"),
                DB::raw("(SELECT sum(total_pago) FROM tesoreria.registro_pago
                        WHERE registro_pago.id_oc = log_ord_compra.id_orden_compra
                        and registro_pago.estado != 7) AS suma_pagado")
            )
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->join('tesoreria.requerimiento_pago_estado', 'requerimiento_pago_estado.id_requerimiento_pago_estado', '=', 'log_ord_compra.estado_pago')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_ord_compra.id_condicion')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('contabilidad.adm_contri as empresa', 'empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.adm_cta_contri', 'adm_cta_contri.id_cuenta_contribuyente', '=', 'log_ord_compra.id_cta_principal')
            ->leftJoin('contabilidad.adm_tp_cta', 'adm_tp_cta.id_tipo_cuenta', '=', 'adm_cta_contri.id_tipo_cuenta')
            ->leftJoin('contabilidad.cont_banco as bco_contribuyente', 'bco_contribuyente.id_banco', '=', 'adm_cta_contri.id_banco')
            ->leftJoin('contabilidad.adm_contri as banco_contribuyente', 'banco_contribuyente.id_contribuyente', '=', 'bco_contribuyente.id_contribuyente')
            ->leftJoin('rrhh.rrhh_cta_banc', 'rrhh_cta_banc.id_cuenta_bancaria', '=', 'log_ord_compra.id_persona_pago')
            ->leftJoin('contabilidad.cont_banco as bco_persona', 'bco_persona.id_banco', '=', 'rrhh_cta_banc.id_banco')
            ->leftJoin('contabilidad.adm_contri as banco_persona', 'banco_persona.id_contribuyente', '=', 'bco_persona.id_contribuyente')
            ->leftJoin('contabilidad.adm_tp_cta as tp_cta_persona', 'tp_cta_persona.id_tipo_cuenta', '=', 'rrhh_cta_banc.id_tipo_cuenta')
            ->where([['log_ord_compra.id_condicion', '=', 1]])
            ->whereIn('log_ord_compra.estado_pago', [8, 5, 6]);

        return datatables($data)->addColumn('persona', function ($data) {
            $persona = Persona::find($data->id_persona_pago);
            if (!empty($persona)) {
                return ([$persona]);
            } else {
                return ([]);
            };
        })->toJson();
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
                DB::raw("(SELECT sum(total_pago) FROM tesoreria.registro_pago
                      WHERE registro_pago.id_doc_com = doc_com.id_doc_com
                        and registro_pago.estado != 7) AS suma_pagado")
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

    public function listarPagos($tipo, $id)
    {
        $detalles = DB::table('tesoreria.registro_pago')
            ->select(
                'registro_pago.*',
                'sis_usua.nombre_corto',
                'sis_moneda.simbolo',
                'adm_contri.razon_social as razon_social_empresa',
                'adm_cta_contri.nro_cuenta'
            )
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'registro_pago.registrado_por')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'registro_pago.id_empresa')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.adm_cta_contri', 'adm_cta_contri.id_cuenta_contribuyente', '=', 'registro_pago.id_cuenta_origen');

        if ($tipo == "orden") {
            $query = $detalles->leftJoin('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'registro_pago.id_oc')
                ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
                ->where([['registro_pago.id_oc', '=', $id], ['registro_pago.estado', '!=', 7]])
                ->get();
        } else if ($tipo == "requerimiento") {
            $query = $detalles->join('tesoreria.requerimiento_pago', 'requerimiento_pago.id_requerimiento_pago', '=', 'registro_pago.id_requerimiento_pago')
                ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'requerimiento_pago.id_moneda')
                ->where([['registro_pago.id_requerimiento_pago', '=', $id], ['registro_pago.estado', '!=', 7]])
                ->get();
        } else if ($tipo == "comprobante") {
            $query = $detalles->leftJoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'registro_pago.id_doc_com')
                ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_com.moneda')
                ->where([['registro_pago.id_doc_com', '=', $id], ['registro_pago.estado', '!=', 7]])
                ->get();
        }

        return response()->json($query);
    }
    /*
    public function pagosRequerimientos($id_requerimiento_pago)
    {
        $detalles = DB::table('tesoreria.registro_pago')
            ->select(
                'registro_pago.*',
                'sis_usua.nombre_corto',
                'sis_moneda.simbolo',
                'adm_contri.razon_social as razon_social_empresa',
                'adm_cta_contri.nro_cuenta'
            )
            ->leftJoin('tesoreria.requerimiento_pago', 'requerimiento_pago.id_requerimiento_pago', '=', 'registro_pago.id_requerimiento_pago')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'requerimiento_pago.id_moneda')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'registro_pago.registrado_por')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'registro_pago.id_empresa')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.adm_cta_contri', 'adm_cta_contri.id_cuenta_contribuyente', '=', 'registro_pago.id_cuenta_origen')
            ->where([
                ['registro_pago.id_requerimiento_pago', '=', $id_requerimiento_pago],
                ['registro_pago.estado', '!=', 7]
            ])
            ->get();

        return response()->json($detalles);
    }

    public function pagosComprobante($id_doc_com)
    {
        $detalles = DB::table('tesoreria.registro_pago')
            ->select(
                'registro_pago.*',
                'sis_usua.nombre_corto',
                'sis_moneda.simbolo',
                'adm_contri.razon_social as razon_social_empresa',
                'adm_cta_contri.nro_cuenta'
            )
            ->leftJoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'registro_pago.id_doc_com')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_com.moneda')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'registro_pago.registrado_por')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'registro_pago.id_empresa')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.adm_cta_contri', 'adm_cta_contri.id_cuenta_contribuyente', '=', 'registro_pago.id_cuenta_origen')
            ->where([
                ['registro_pago.id_doc_com', '=', $id_doc_com],
                ['registro_pago.estado', '!=', 7]
            ])
            ->get();

        return response()->json($detalles);
    }*/

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

    function cuentasOrigen($id_empresa)
    {
        $cuentas = DB::table('contabilidad.adm_cta_contri')
            ->select('adm_cta_contri.id_cuenta_contribuyente', 'adm_cta_contri.nro_cuenta')
            // ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_cta_contri.id_contribuyente')
            ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_contribuyente', '=', 'adm_cta_contri.id_contribuyente')
            ->where('adm_empresa.id_empresa', $id_empresa)
            ->get();
        return response()->json($cuentas);
    }

    function procesarPago(Request $request)
    {
        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $file = $request->file('adjunto');

            $id_pago = DB::table('tesoreria.registro_pago')
                ->insertGetId([
                    'id_oc' => $request->id_oc,
                    'id_requerimiento_pago' => $request->id_requerimiento_pago,
                    'id_doc_com' => $request->id_doc_com,
                    'fecha_pago' => $request->fecha_pago,
                    'observacion' => $request->observacion,
                    'total_pago' => round($request->total_pago, 2),
                    'id_empresa' => $request->id_empresa,
                    'id_cuenta_origen' => $request->id_cuenta_origen,
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

                DB::table('tesoreria.registro_pago')
                    ->where('id_pago', $id_pago)
                    ->update(['adjunto' => $nombre]);
            }

            if (floatval($request->total_pago) >= floatval(round($request->total, 2))) {

                if ($request->id_oc !== null) {
                    DB::table('logistica.log_ord_compra')
                        ->where('id_orden_compra', $request->id_oc)
                        ->update(['estado_pago' => 6]); //pagada
                } else if ($request->id_doc_com !== null) {
                    DB::table('almacen.doc_com')
                        ->where('id_doc_com', $request->id_doc_com)
                        ->update(['estado' => 9]); //procesado
                } else if ($request->id_requerimiento_pago !== null) {
                    DB::table('tesoreria.requerimiento_pago')
                        ->where('id_requerimiento_pago', $request->id_requerimiento_pago)
                        ->update(['id_estado' => 6]); //procesado
                }
            }

            DB::commit();
            return response()->json($id_pago);
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    function enviarAPago(Request $request)
    {
        try {
            DB::beginTransaction();
            $msj = '';
            $tipo = '';

            if ($request->tipo == "requerimiento") {
                $req = DB::table('tesoreria.requerimiento_pago')
                    ->where('id_requerimiento_pago', $request->id)->first();
                //ya fue pagado?
                if ($req->id_estado !== 6) {
                    //fue anulado?
                    if ($req->id_estado !== 7) {
                        DB::table('tesoreria.requerimiento_pago')
                            ->where('id_requerimiento_pago', $request->id)
                            ->update(['id_estado' => 5]); //enviado a pago
                        $msj = 'El requerimiento fue enviado a pago exitosamente';
                        $tipo = 'success';
                    } else {
                        $msj = 'El requerimiento fue anulado';
                        $tipo = 'warning';
                    }
                } else {
                    $msj = 'El requerimiento ya fue pagado';
                    $tipo = 'warning';
                }
            } else if ($request->tipo == "orden") {
                $oc = DB::table('logistica.log_ord_compra')
                    ->where('id_orden_compra', $request->id)->first();
                //fue pagada?
                if ($oc->estado_pago !== 6) {
                    //fue anulado?
                    if ($oc->estado !== 7) {
                        DB::table('logistica.log_ord_compra')
                            ->where('id_orden_compra', $request->id)
                            ->update(['estado_pago' => 5]); //enviado a pago
                        $msj = 'La orden fue enviada a pago exitosamente';
                        $tipo = 'success';
                    } else {
                        $msj = 'La orden fue anulada';
                        $tipo = 'warning';
                    }
                } else {
                    $msj = 'La orden ya fue pagada';
                    $tipo = 'warning';
                }
            }
            // else if ($tipo !== "comprobante") {
            //     DB::table('almacen.doc_com')
            //         ->where('id_doc_com', $id)
            //         ->update(['estado' => 5]); //
            // }

            DB::commit();
            return response()->json(['tipo' => $tipo, 'mensaje' => $msj]);
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    function revertirEnvio(Request $request)
    {
        try {
            DB::beginTransaction();
            $msj = '';
            $tipo = '';

            if ($request->tipo == "requerimiento") {
                $req = DB::table('tesoreria.requerimiento_pago')
                    ->where('id_requerimiento_pago', $request->id)->first();

                if ($req->id_estado !== 6) {
                    if ($req->id_estado !== 7) {
                        DB::table('tesoreria.requerimiento_pago')
                            ->where('id_requerimiento_pago', $request->id)
                            ->update(['id_estado' => 2]); //aprobado
                        $msj = 'El requerimiento fue enviado a pago exitosamente';
                        $tipo = 'success';
                    } else {
                        $msj = 'El requerimiento fue anulado';
                        $tipo = 'warning';
                    }
                } else {
                    $msj = 'El requerimiento ya fue pagado';
                    $tipo = 'warning';
                }
            } else if ($request->tipo == "orden") {
                $oc = DB::table('logistica.log_ord_compra')
                    ->where('id_orden_compra', $request->id)->first();

                if ($oc->estado_pago !== 6) {
                    if ($oc->estado !== 7) {
                        DB::table('logistica.log_ord_compra')
                            ->where('id_orden_compra', $request->id)
                            ->update(['estado_pago' => 1]); //elaborado
                        $msj = 'La orden fue enviada a pago exitosamente';
                        $tipo = 'success';
                    } else {
                        $msj = 'La orden fue anulada';
                        $tipo = 'warning';
                    }
                } else {
                    $msj = 'La orden ya fue pagada';
                    $tipo = 'warning';
                }
            }
            // else if ($tipo !== "comprobante") {
            //     DB::table('almacen.doc_com')
            //         ->where('id_doc_com', $id)
            //         ->update(['estado' => 5]); //
            // }

            DB::commit();
            return response()->json(['tipo' => $tipo, 'mensaje' => $msj]);
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    function anularPago($id_pago)
    {
        try {
            DB::beginTransaction();

            $pago = DB::table('tesoreria.registro_pago')
                ->select(
                    'registro_pago.id_requerimiento_pago',
                    'registro_pago.id_oc',
                    'registro_pago.id_doc_com',
                )
                ->where('registro_pago.id_pago', $id_pago)
                ->first();

            if ($pago->id_requerimiento_pago !== null) {
                DB::table('tesoreria.requerimiento_pago')
                    ->where('id_requerimiento_pago', $pago->id_requerimiento_pago)
                    ->update(['id_estado' => 5]); //enviado a pago

            } else if ($pago->id_oc !== null) {
                DB::table('logistica.log_ord_compra')
                    ->where('id_orden_compra', $pago->id_oc)
                    ->update(['estado_pago' => 5]); //enviado a pago
            } //falta agregar comprobante

            DB::table('tesoreria.registro_pago')
                ->where('id_pago', $id_pago)
                ->update(['estado' => 7]);

            DB::commit();
            return response()->json("Se anulo correctamente");
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }
}
