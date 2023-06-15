<?php

namespace App\Http\Controllers\Tesoreria;

use App\Exports\OrdenCompraServicioExport;
use App\Exports\OrdenesCompraServicioExport;
use App\Exports\RegistroPagosExport;
use App\Exports\RequerimientoPagosExport;
use App\Helpers\Finanzas\PresupuestoInternoHistorialHelper;
use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Finanzas\Presupuesto\PresupuestoInternoController;
use App\Models\Administracion\Empresa;
use App\Models\Administracion\Prioridad;
use App\Models\Almacen\AdjuntoDetalleRequerimiento;
use App\Models\Almacen\AdjuntoRequerimiento;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Requerimiento;
use App\Models\Configuracion\Moneda;
use App\Models\Logistica\Orden;
use App\Models\Logistica\OrdenCompraDetalle;
use App\Models\Logistica\PagoCuota;
use App\Models\Logistica\PagoCuotaDetalle;
use App\Models\Rrhh\Persona;
use App\Models\Tesoreria\OtrosAdjuntosTesoreria;
use App\Models\Tesoreria\RegistroPago;
use App\Models\Tesoreria\RegistroPagoAdjuntos;
use App\Models\Tesoreria\RequerimientoPago;
use App\Models\Tesoreria\RequerimientoPagoAdjunto;
use App\Models\Tesoreria\RequerimientoPagoAdjuntoDetalle;
use App\Models\Tesoreria\RequerimientoPagoCategoriaAdjunto;
use App\Models\Tesoreria\RequerimientoPagoDetalle;
use App\Models\Tesoreria\RequerimientoPagoEstados;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Debugbar;
use Exception;

class RegistroPagoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view_main_tesoreria()
    {
        $pagos_pendientes = DB::table('almacen.alm_req')
            ->where('estado', 8)->count();

        $confirmaciones_pendientes = DB::table('almacen.alm_req')
            ->where([['estado', '=', 19], ['confirmacion_pago', '=', false]])->count();

        $tipo_cambio = DB::table('contabilidad.cont_tp_cambio')->orderBy('fecha', 'desc')->first();

        return view('tesoreria/main', get_defined_vars());
    }

    function view_pendientes_pago()
    {
        $prioridad = Prioridad::all();
        $empresas = AlmacenController::select_empresa();
        $estados = RequerimientoPagoEstados::all();
        $moneda = Moneda::all();
        // return view('tesoreria/pagos/pendientesPago', compact('empresas'));
        return view('tesoreria.Pagos.pendientesPago', get_defined_vars());
    }

    public function listarRequerimientosPago(Request $request)
    {
        // return $request->all();exit;

        $data = DB::table('tesoreria.requerimiento_pago')
            ->select(
                'requerimiento_pago.*',
                'adm_prioridad.descripcion as prioridad',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'empresa.razon_social as razon_social_empresa',
                'adm_empresa.codigo as codigo_empresa',
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
                'sis_usua.nombre_corto',
                'autorizado.nombre_corto as nombre_autorizado',
                'rrhh_perso.nro_documento as dni_persona',
                DB::raw("concat(rrhh_perso.nombres, ' ' ,rrhh_perso.apellido_paterno, ' ' ,rrhh_perso.apellido_materno) AS persona"),
                DB::raw("(SELECT count(archivo) FROM tesoreria.requerimiento_pago_adjunto
                        WHERE requerimiento_pago_adjunto.id_requerimiento_pago = requerimiento_pago.id_requerimiento_pago
                        and requerimiento_pago_adjunto.id_estado != 7) AS count_adjunto_cabecera"),

                DB::raw("(SELECT count(archivo) FROM tesoreria.requerimiento_pago_detalle_adjunto
                        INNER JOIN tesoreria.requerimiento_pago_detalle as detalle on(
                            detalle.id_requerimiento_pago_detalle = requerimiento_pago_detalle_adjunto.id_requerimiento_pago_detalle
                        )
                        WHERE detalle.id_requerimiento_pago = requerimiento_pago.id_requerimiento_pago
                        and requerimiento_pago_detalle_adjunto.id_estado != 7) AS count_adjunto_detalle"),

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
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'requerimiento_pago.id_usuario')
            ->leftJoin('configuracion.sis_usua as autorizado', 'autorizado.id_usuario', '=', 'requerimiento_pago.usuario_autorizacion');



        $data = $data->whereIn('requerimiento_pago.id_estado', [6, 2, 5, 8, 9]);
        // ->where([['requerimiento_pago.id_estado', '!=', 7], ['requerimiento_pago.id_estado', '!=', 1]]);
        if (!empty($request->prioridad)) {
            $data = $data->where('adm_prioridad.id_prioridad', $request->prioridad);
        }
        if (!empty($request->empresa)) {
            $data = $data->where('adm_empresa.id_empresa', $request->empresa);
        }
        if (!empty($request->estado)) {
            $data = $data->where('requerimiento_pago_estado.id_requerimiento_pago_estado', $request->estado);
        }
        if (!empty($request->fecha_inicio)) {
            $data = $data->whereDate('requerimiento_pago.fecha_registro', '>=', $request->fecha_inicio);
        }
        if (!empty($request->fecha_final)) {
            $data = $data->whereDate('requerimiento_pago.fecha_registro', '<=', $request->fecha_final);
        }
        // return $request->prioridad;exit;
        return datatables($data)->filterColumn('persona', function ($query, $keyword) {
            $keywords = trim(strtoupper($keyword));
            $query->whereRaw("UPPER(CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno)) LIKE ?", ["%{$keywords}%"]);
        })->toJson();
    }

    public function listarOrdenesCompra(Request $request)
    {
        $data = Orden::select(
            'log_ord_compra.*',
            'adm_contri.nro_documento',
            'adm_contri.razon_social',
            'adm_empresa.id_empresa',
            'empresa.razon_social as razon_social_empresa',
            'adm_empresa.codigo as codigo_empresa',
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
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_completo_persona"),
            'rrhh_perso.nro_documento as nro_documento_persona',
            'adm_prioridad.descripcion as prioridad',
            'autorizado.nombre_corto as nombre_autorizado',
            DB::raw("(SELECT sum(subtotal) FROM logistica.log_det_ord_compra
                        WHERE log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra
                        and log_det_ord_compra.estado != 7) AS suma_total"),
            DB::raw("(SELECT sum(total_pago) FROM tesoreria.registro_pago
                        WHERE registro_pago.id_oc = log_ord_compra.id_orden_compra
                        and registro_pago.estado != 7) AS suma_pagado"),
            DB::raw("(SELECT COUNT(adjuntos_logisticos.id_adjunto)
                    FROM logistica.adjuntos_logisticos
                    WHERE  adjuntos_logisticos.id_orden = log_ord_compra.id_orden_compra AND
                    adjuntos_logisticos.estado != 7) AS cantidad_adjuntos_logisticos"),
            DB::raw("(SELECT monto_cuota FROM logistica.pago_cuota_detalle
            inner join logistica.pago_cuota on pago_cuota.id_pago_cuota = pago_cuota_detalle.id_pago_cuota
            WHERE  pago_cuota.id_orden = log_ord_compra.id_orden_compra and pago_cuota_detalle.id_estado != 7 order by pago_cuota_detalle.fecha_registro desc limit 1 ) AS ultima_monto_cuota"),
            DB::raw("(SELECT sum(monto_cuota) FROM logistica.pago_cuota_detalle
            inner join logistica.pago_cuota on pago_cuota.id_orden = log_ord_compra.id_orden_compra
            WHERE pago_cuota.id_pago_cuota = pago_cuota_detalle.id_pago_cuota
            and pago_cuota_detalle.id_estado =5) AS suma_cuotas_con_autorizacion"),

        )
            ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftjoin('tesoreria.requerimiento_pago_estado', 'requerimiento_pago_estado.id_requerimiento_pago_estado', '=', 'log_ord_compra.estado_pago')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_ord_compra.id_condicion')
            ->leftjoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
            ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->leftjoin('contabilidad.adm_contri as empresa', 'empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.adm_cta_contri', 'adm_cta_contri.id_cuenta_contribuyente', '=', 'log_ord_compra.id_cta_principal')
            ->leftJoin('contabilidad.adm_tp_cta', 'adm_tp_cta.id_tipo_cuenta', '=', 'adm_cta_contri.id_tipo_cuenta')
            ->leftJoin('contabilidad.cont_banco as bco_contribuyente', 'bco_contribuyente.id_banco', '=', 'adm_cta_contri.id_banco')
            ->leftJoin('contabilidad.adm_contri as banco_contribuyente', 'banco_contribuyente.id_contribuyente', '=', 'bco_contribuyente.id_contribuyente')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'log_ord_compra.id_persona_pago')
            ->leftJoin('rrhh.rrhh_cta_banc', 'rrhh_cta_banc.id_cuenta_bancaria', '=', 'log_ord_compra.id_cuenta_persona_pago')
            ->leftJoin('contabilidad.cont_banco as bco_persona', 'bco_persona.id_banco', '=', 'rrhh_cta_banc.id_banco')
            ->leftJoin('contabilidad.adm_contri as banco_persona', 'banco_persona.id_contribuyente', '=', 'bco_persona.id_contribuyente')
            ->leftJoin('contabilidad.adm_tp_cta as tp_cta_persona', 'tp_cta_persona.id_tipo_cuenta', '=', 'rrhh_cta_banc.id_tipo_cuenta')
            ->leftJoin('configuracion.sis_usua as autorizado', 'autorizado.id_usuario', '=', 'log_ord_compra.usuario_autorizacion')
            ->join('administracion.adm_prioridad', 'adm_prioridad.id_prioridad', '=', 'log_ord_compra.id_prioridad_pago')
            ->whereIn('log_ord_compra.estado_pago', [8, 5, 6, 9, 10])
            ->where('log_ord_compra.estado', '!=', 7);

        if (!empty($request->prioridad)) {
            $data = $data->where('adm_prioridad.id_prioridad', $request->prioridad);
        }
        if (!empty($request->empresa)) {
            $data = $data->where('adm_empresa.id_empresa', $request->empresa);
        }
        if (!empty($request->estado)) {
            $data = $data->where('requerimiento_pago_estado.id_requerimiento_pago_estado', $request->estado);
        }
        if (!empty($request->fecha_inicio)) {
            $data = $data->whereDate('log_ord_compra.fecha_solicitud_pago', '>=', $request->fecha_inicio);
        }
        if (!empty($request->fecha_final)) {
            $data = $data->whereDate('log_ord_compra.fecha_solicitud_pago', '<=', $request->fecha_final);
        }
        return DataTables::eloquent($data)
            ->filterColumn('requerimientos', function ($query, $keyword) {
                $sql_oc = "id_orden_compra IN (
                SELECT log_det_ord_compra.id_orden_compra FROM logistica.log_det_ord_compra
                INNER JOIN almacen.alm_det_req ON
                 log_det_ord_compra.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento
                INNER JOIN almacen.alm_req ON
                 alm_req.id_requerimiento = alm_det_req.id_requerimiento
                 WHERE   UPPER(alm_req.codigo) LIKE ? )
                    ";
                $query->whereRaw($sql_oc, ['%' . strtoupper($keyword) . '%']);
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
                'adm_cta_contri.nro_cuenta',
                DB::raw("(SELECT count(adjunto) FROM tesoreria.registro_pago_adjuntos
                      WHERE registro_pago_adjuntos.id_pago = registro_pago.id_pago
                        and registro_pago_adjuntos.estado != 7) AS count_adjuntos")
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
    public function listarPagosEnCuotas($tipo, $id)
    {
        $query = [];
        if ($tipo == "orden") {
            $query = PagoCuota::with(['orden', 'detalle' => function ($query) {
                $query->orderBy('fecha_registro', 'asc');
            }, 'detalle.creadoPor', 'detalle.adjuntos', 'detalle.estado'])->where('id_orden', $id)->first();
        }

        return response()->json($query);
    }

    public function verAdjuntosPago($id)
    {
        $adjuntos = DB::table('tesoreria.registro_pago_adjuntos')
            ->where('id_pago', $id)
            ->get();
        return response()->json($adjuntos);
    }

    function anularAdjuntoTesoreria(Request $request)
    {
        DB::beginTransaction();
        try {

            $estado_accion = '';
            $adjunto = RequerimientoPagoAdjunto::find($request->id_adjunto);
            if (isset($adjunto)) {
                $adjunto->id_estado = 7;
                $adjunto->save();
                $estado_accion = 'success';
                $mensaje = 'Adjuntos anulado';
            } else {
                $estado_accion = 'warning';
                $mensaje = 'Hubo un problema y no se pudo anular el adjuntos';
            }
            DB::commit();

            return response()->json(['status' => $estado_accion, 'mensaje' => $mensaje]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'mensaje' => 'Hubo un problema al anular el adjuntos. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
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

            if (isset($request->vincularCuotaARegistroDePago) && count($request->vincularCuotaARegistroDePago) > 0) {
                foreach ($request->vincularCuotaARegistroDePago as $key => $value) {
                    $pagoCuotaDetalle = PagoCuotaDetalle::where('id_pago_cuota_detalle', $value)->first();
                    $pagoCuotaDetalle->id_pago = $id_pago;
                    $pagoCuotaDetalle->id_estado = 6; // pagado
                    $pagoCuotaDetalle->save();
                }
            }

            //Guardar archivos subidos
            if ($request->hasFile('archivos')) {
                $archivos = $request->file('archivos');

                foreach ($archivos as $archivo) {
                    $id_adjunto = DB::table('tesoreria.registro_pago_adjuntos')
                        ->insertGetId([
                            'id_pago' => $id_pago,
                            // 'adjunto' => $nombre,
                            'estado' => 1,
                        ], 'id_adjunto');

                    //obtenemos el nombre del archivo
                    $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
                    $nombre = $id_adjunto . '.' . $request->codigo . '.' . $extension;

                    //indicamos que queremos guardar un nuevo archivo en el disco local
                    File::delete(public_path('tesoreria/pagos/' . $nombre));
                    Storage::disk('archivos')->put('tesoreria/pagos/' . $nombre, File::get($archivo));

                    DB::table('tesoreria.registro_pago_adjuntos')
                        ->where('id_adjunto', $id_adjunto)
                        ->update(['adjunto' => $nombre]);
                }
            }

            // Debugbar::info('total pago: '.$request->total_pago.' total:'.$request->total);
            if (floatval($request->total_pago) >= floatval(round($request->total, 2))) {

                if ($request->id_oc !== null) {
                    DB::table('logistica.log_ord_compra')
                        ->where('id_orden_compra', $request->id_oc)
                        ->update(['estado_pago' => 6]); //pagada

                    $detalleArray = PresupuestoInternoHistorialHelper::obtenerDetalleRequerimientoLogisticoDeOrdenParaAfectarPresupuestoInterno($request->id_oc, floatval($request->total_pago));
                    // Debugbar::info('completo orden');

                    PresupuestoInternoHistorialHelper::registrarEstadoGastoAfectadoDeRequerimientoLogistico($request->id_oc, $id_pago, $detalleArray, 'R', "Registrar afectación regular");
                } else if ($request->id_requerimiento_pago !== null) {
                    DB::table('tesoreria.requerimiento_pago')
                        ->where('id_requerimiento_pago', $request->id_requerimiento_pago)
                        ->update(['id_estado' => 6]); //pagada

                    // * aplicar afectación de presupuesto
                    $requerimientoPago =  RequerimientoPago::find($request->id_requerimiento_pago);
                    if ($requerimientoPago->id_presupuesto_interno > 0) {
                        $detalleArray = PresupuestoInternoHistorialHelper::obtenerDetalleRequerimientoPagoParaPresupuestoInterno($request->id_requerimiento_pago, floatval($request->total_pago));
                        // Debugbar::info('completo requerimiento pago');
                        // Debugbar::info($detalleArray);
                        // Debugbar::info(floatval($request->total_pago),floatval($request->total));

                        PresupuestoInternoHistorialHelper::registrarEstadoGastoAfectadoDeRequerimientoPago($request->id_requerimiento_pago, $id_pago, $detalleArray, 'R',$requerimientoPago->fecha_registro, 'Registrar afectación regular');
                    }
                }
            } else {
                if ($request->id_oc !== null) {
                    if (isset($request->vincularCuotaARegistroDePago) && count($request->vincularCuotaARegistroDePago) > 0) {
                        $nuevoEstado = 10;
                    } else {
                        $nuevoEstado = 9;
                    }
                    DB::table('logistica.log_ord_compra')
                        ->where('id_orden_compra', $request->id_oc)
                        ->update(['estado_pago' => $nuevoEstado]); //con saldo

                    $detalleArray = PresupuestoInternoHistorialHelper::obtenerDetalleRequerimientoLogisticoDeOrdenParaAfectarPresupuestoInterno($request->id_oc, floatval($request->total_pago));
                    // Debugbar::info('prorrateo orden compra');
                    // Debugbar::info($detalleArray);

                    PresupuestoInternoHistorialHelper::registrarEstadoGastoAfectadoDeRequerimientoLogistico($request->id_oc, $id_pago, $detalleArray, 'R', 'Registrar afectación regular');
                } else if ($request->id_requerimiento_pago !== null) {
                    DB::table('tesoreria.requerimiento_pago')
                        ->where('id_requerimiento_pago', $request->id_requerimiento_pago)
                        ->update(['id_estado' => 9]); //con saldo

                    // * aplicar afectación de presupuesto
                    $requerimientoPago =  RequerimientoPago::find($request->id_requerimiento_pago);
                    if ($requerimientoPago->id_presupuesto_interno > 0) {
                        $detalleArray = PresupuestoInternoHistorialHelper::obtenerDetalleRequerimientoPagoParaPresupuestoInterno($request->id_requerimiento_pago, floatval($request->total_pago));
                        // Debugbar::info('prorrateo requerimiento pago');
                        // Debugbar::info(floatval($request->total_pago),floatval($request->total));
                        PresupuestoInternoHistorialHelper::registrarEstadoGastoAfectadoDeRequerimientoPago($request->id_requerimiento_pago, $id_pago, $detalleArray, 'R',$requerimientoPago->fecha_registro, 'Registrar afectación regular');
                    }
                }
            }

            DB::commit();

            // determinar el estado de la estadopago en la orden si todo los pagoDetalle con estad 6 (pagado) es igual al monto e la orden, el estado podria ser "pagado" o "pagado con saldo"
            if (isset($request->vincularCuotaARegistroDePago) && count($request->vincularCuotaARegistroDePago) > 0) {
                if ($request->id_oc !== null) {
                    $ord = Orden::where('id_orden_compra', $request->id_oc)->first();
                    $lastPagoCuota = PagoCuota::where([['id_orden', $request->id_oc]])->first();
                    $lastPagoCuotaDetallePagadas = PagoCuotaDetalle::where([['id_pago_cuota', $lastPagoCuota->id_pago_cuota], ['id_estado', '=', 6]])->get();
                    $sumaPagos = 0;
                    foreach ($lastPagoCuotaDetallePagadas as $key => $detCuota) {
                        $sumaPagos += $detCuota->monto_cuota;
                    }

                    // Debugbar::info($ord->monto_total);
                    // Debugbar::info($sumaPagos);

                    if (floatval($ord->monto_total) > floatval($sumaPagos)) {
                        DB::table('logistica.log_ord_compra')
                            ->where('id_orden_compra', $ord->id_orden_compra)
                            ->update(['estado_pago' => 10]); //pagada con saldo
                    } else {
                        $pagoc = PagoCuota::where('id_orden', $ord->id_orden_compra)->first();
                        $pagoc->id_estado = 6; // pagado
                        $pagoc->save();
                    }
                }
            }


            return response()->json($id_pago);
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }




    function actualizarEstadoPago()
    {

        $nro_actualizados_req = 0;
        $nro_actualizados_oc = 0;

        $requerimientos = DB::table('tesoreria.requerimiento_pago')
            ->select(
                'requerimiento_pago.id_requerimiento_pago',
                DB::raw("(SELECT sum(total_pago) FROM tesoreria.registro_pago
                    WHERE registro_pago.id_requerimiento_pago = requerimiento_pago.id_requerimiento_pago
                    and registro_pago.estado != 7) AS suma_pagado")
            )
            ->where('id_estado', 5)
            ->get();

        foreach ($requerimientos as $req) {

            if ($req->suma_pagado > 0) {
                DB::table('tesoreria.requerimiento_pago')
                    ->where('id_requerimiento_pago', $req->id_requerimiento_pago)
                    ->update(['id_estado' => 9]);
                $nro_actualizados_req++;
            }
        }

        $ordenes = DB::table('logistica.log_ord_compra')
            ->select(
                'log_ord_compra.id_orden_compra',
                DB::raw("(SELECT sum(total_pago) FROM tesoreria.registro_pago
                            WHERE registro_pago.id_oc = log_ord_compra.id_orden_compra
                            and registro_pago.estado != 7) AS suma_pagado")
            )
            ->where('estado_pago', 5)
            ->get();

        foreach ($ordenes as $req) {

            if ($req->suma_pagado > 0) {
                DB::table('logistica.log_ord_compra')
                    ->where('id_orden_compra', $req->id_orden_compra)
                    ->update(['estado_pago' => 9]);
                $nro_actualizados_oc++;
            }
        }
        return response()->json("Se actualizaron " . $nro_actualizados_req . " requerimientos y " . $nro_actualizados_oc . " ordenes.");
    }

    function enviarAPago(Request $request)
    {
        try {
            DB::beginTransaction();
            $msj = '';
            $tipo = '';
            $id_usuario = Auth::user()->id_usuario;

            if ($request->tipo == "requerimiento") {
                $req = DB::table('tesoreria.requerimiento_pago')
                    ->where('id_requerimiento_pago', $request->id)->first();
                //ya fue pagado?
                if ($req->id_estado !== 6) {
                    //fue anulado?
                    if ($req->id_estado !== 7) {
                        DB::table('tesoreria.requerimiento_pago')
                            ->where('id_requerimiento_pago', $request->id)
                            ->update([
                                'id_estado' => 5,
                                'fecha_autorizacion' => new Carbon(),
                                'usuario_autorizacion' => $id_usuario
                            ]); //enviado a pago
                        $msj = 'Se autorizó el pago del requerimiento exitosamente';
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
                            ->update([
                                'estado_pago' => 5,
                                'fecha_autorizacion' => new Carbon(),
                                'usuario_autorizacion' => $id_usuario
                            ]); //enviado a pago

                        $msj = 'Se autorizó el pago de la orden exitosamente';

                        if (isset($request->idPagoCuotaDetalle) && ($request->idPagoCuotaDetalle > 0)) {
                            $pagoCuotaDetalle = PagoCuotaDetalle::where('id_pago_cuota_detalle', $request->idPagoCuotaDetalle)->first();
                            $pagoCuotaDetalle->fecha_autorizacion = new Carbon();
                            $pagoCuotaDetalle->id_estado = 5;
                            $pagoCuotaDetalle->save();

                            $msj = 'Se autorizó el pago de la cuota exitosamente';
                        }

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
                        $orden = Orden::find($request->id);
                        if ($orden->tiene_pago_en_cuotas == true) {
                            $pagoCuota = PagoCuota::where('id_orden', $request->id)->first();
                            if (isset($pagoCuota->id_pago_cuota)) {
                                $pagoCuotaDetalle = PagoCuotaDetalle::where([['id_pago_cuota', $pagoCuota->id_pago_cuota], ['id_estado', 5]])->get();
                                foreach ($pagoCuotaDetalle as $keyPcd => $pcd) {
                                    $updatePagoCuotaDetalle = PagoCuotaDetalle::find($pcd->id_pago_cuota_detalle);
                                    $updatePagoCuotaDetalle->id_estado = 7;
                                    $updatePagoCuotaDetalle->fecha_autorizacion = null;
                                    $updatePagoCuotaDetalle->save();
                                }
                            }
                        }
                        DB::table('logistica.log_ord_compra')
                            ->where('id_orden_compra', $request->id)
                            ->update(['estado_pago' => 1]);
                        $msj = 'La autorización y solicitud fueron revertidas';
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
                    'registro_pago.total_pago'
                )
                ->where('registro_pago.id_pago', $id_pago)
                ->first();

            if ($pago->id_requerimiento_pago !== null) {
                DB::table('tesoreria.requerimiento_pago')
                    ->where('id_requerimiento_pago', $pago->id_requerimiento_pago)
                    ->update(['id_estado' => 5]); //enviado a pago

                $requerimientoPago = RequerimientoPago::find($pago->id_requerimiento_pago);
                $detalleArray = [];

                if ($requerimientoPago->id_presupuesto_interno > 0) {
                    $todoDetalleRequerimientoPago = RequerimientoPagoDetalle::where([["id_requerimiento_pago", $pago->id_requerimiento_pago], ['id_estado', '!=', 7]])->get();
                    if (count($todoDetalleRequerimientoPago) == 1) {
                        foreach ($todoDetalleRequerimientoPago as $detalleRequerimientoPago) {
                            $detalle = RequerimientoPagoDetalle::where("id_requerimiento_pago_detalle", $detalleRequerimientoPago->id_requerimiento_pago_detalle)->first();
                            $detalle->importe_item_para_presupuesto = $pago->total_pago;
                            $detalleArray[] = $detalle;
                        }
                    } elseif (count($todoDetalleRequerimientoPago) > 1) {
                        $prorrateo = $pago->total_pago / count($todoDetalleRequerimientoPago);

                        foreach ($todoDetalleRequerimientoPago as $detalleRequerimientoPago) {
                            $detalle = RequerimientoPagoDetalle::where("id_requerimiento_pago_detalle", $detalleRequerimientoPago->id_requerimiento_pago_detalle)->first();
                            $detalle->importe_item_para_presupuesto = $prorrateo;
                            $detalleArray[] = $detalle;
                        }
                    }

                }
            } else if ($pago->id_oc !== null) {
                DB::table('logistica.log_ord_compra')
                    ->where('id_orden_compra', $pago->id_oc)
                    ->update(['estado_pago' => 5]); //enviado a pago
            } //falta agregar comprobante

            DB::table('tesoreria.registro_pago')
                ->where('id_pago', $id_pago)
                ->update(['estado' => 7]);

            $retornoDePresupuesto= PresupuestoInternoHistorialHelper::registrarRetornoDePresupuesto($id_pago);
            if($retornoDePresupuesto!=null){
                $mensaje = 'Se anulo correctamente y retorno presupuesto';
            }else{
                $mensaje = 'Se anulo correctamente';
            }

            DB::commit();
            return response()->json($mensaje);
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    function verAdjuntos($id_requerimiento_pago)
    {
        $usuarioPropietarioDeRequerimiento = RequerimientoPago::find($id_requerimiento_pago)->id_usuario;
        $adjuntoPadre = RequerimientoPagoAdjunto::where([['id_requerimiento_pago', $id_requerimiento_pago], ['id_estado', '!=', 7]])->with('categoriaAdjunto')->get();
        $adjuntoDetalle = RequerimientoPagoAdjuntoDetalle::join('tesoreria.requerimiento_pago_detalle', 'requerimiento_pago_detalle.id_requerimiento_pago_detalle', '=', 'requerimiento_pago_detalle_adjunto.id_requerimiento_pago_detalle')
            ->where([['requerimiento_pago_detalle.id_requerimiento_pago', $id_requerimiento_pago], ['requerimiento_pago_detalle_adjunto.id_estado', '!=', 7]])->get();

        $adjuntos_pagos = RegistroPago::select('registro_pago_adjuntos.adjunto', 'registro_pago_adjuntos.id_adjunto')
            ->where('id_requerimiento_pago', $id_requerimiento_pago)
            ->join('tesoreria.registro_pago_adjuntos', 'registro_pago_adjuntos.id_pago', '=', 'registro_pago.id_pago')
            ->get();
        $adjuntos_pagos_complementarios = OtrosAdjuntosTesoreria::where('id_requerimiento_pago', $id_requerimiento_pago)
            ->where('id_estado', '!=', 7)
            ->get();
        return response()->json(['adjuntoPadre' => $adjuntoPadre, 'adjuntoDetalle' => $adjuntoDetalle, 'adjuntos_pago' => $adjuntos_pagos, 'adjuntos_pagos_complementarios' => $adjuntos_pagos_complementarios]);
    }

    function verAdjuntosRegistroPagoOrden($id_orden)
    {

        $registro_pago = RegistroPago::where([['id_oc', $id_orden], ['estado', '!=', 7]])->first();
        $adjuntos_pagos = [];
        if ($registro_pago != null && $registro_pago->id_pago > 0) {
            $adjuntos_pagos = RegistroPagoAdjuntos::where([['id_pago', $registro_pago->id_pago], ['estado', '!=', 7]])->get();
        }

        $adjuntos_pagos_complementarios = OtrosAdjuntosTesoreria::where('id_orden', $id_orden)
            ->where('id_estado', '!=', 7)
            ->get();

        return response()->json(['adjuntos_pago' => $adjuntos_pagos, 'adjuntos_pagos_complementarios' => $adjuntos_pagos_complementarios]);
    }
    function verAdjuntosRequerimientoDeOrden($id_orden)
    {

        $orden = Orden::with(['detalle' => function ($q) {
            $q->where('log_det_ord_compra.estado', '!=', 7);
        }])->find($id_orden);

        $idRequerimientoList = [];
        $idDetalleRequerimientoList = [];
        $adjuntoPadre = [];
        $adjuntoDetalle = [];
        if ($orden) {
            if (isset($orden->requerimientos)) {
                foreach (($orden->requerimientos) as $key => $value) {
                    $idRequerimientoList[] = $value->id_requerimiento;
                }
            }
            if (isset($orden->detalle)) {
                foreach (($orden->detalle) as $key => $value) {
                    $idDetalleRequerimientoList[] = $value->id_detalle_requerimiento;
                }
            }
        }
        if (count($idRequerimientoList) > 0) {
            $adjuntoPadre = AdjuntoRequerimiento::with('categoriaAdjunto')->whereIn('id_requerimiento', $idRequerimientoList)->where('alm_req_adjuntos.estado', '!=', 7)->get();
        }
        if (count($idDetalleRequerimientoList) > 0) {
            $adjuntoDetalle = AdjuntoDetalleRequerimiento::whereIn('id_detalle_requerimiento', $idDetalleRequerimientoList)->where('alm_det_req_adjuntos.estado', '!=', 7)->get();
        }

        return response()->json(['adjuntoPadre' => $adjuntoPadre, 'adjuntoDetalle' => $adjuntoDetalle]);
    }

    function listarAdjuntosPago($id_requerimiento_pago)
    {
        $adjuntos = DB::table('tesoreria.registro_pago_adjuntos')
            ->select('registro_pago_adjuntos.*', 'registro_pago.fecha_pago', 'registro_pago.observacion')
            ->join('tesoreria.registro_pago', 'registro_pago.id_pago', '=', 'registro_pago_adjuntos.id_pago')
            ->where('registro_pago.id_requerimiento_pago', $id_requerimiento_pago)
            ->get();

        return response()->json($adjuntos);
    }
    public function registroPagosExportarExcel()
    {
        return Excel::download(new RegistroPagosExport, 'listado_ventas_Externas_exportar_excel.xlsx');
    }
    public function obtenerRegistroPagos()
    {
        return DB::table('tesoreria.requerimiento_pago')
            ->select(
                'requerimiento_pago.*',
                'adm_prioridad.descripcion as prioridad',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'empresa.razon_social as razon_social_empresa',
                'adm_empresa.codigo as codigo_empresa',
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
                'sis_usua.nombre_corto',
                'autorizado.nombre_corto as nombre_autorizado',
                'rrhh_perso.nro_documento as dni_persona',
                DB::raw("concat(rrhh_perso.nombres, ' ' ,rrhh_perso.apellido_paterno, ' ' ,rrhh_perso.apellido_materno) AS persona"),
                DB::raw("(SELECT count(archivo) FROM tesoreria.requerimiento_pago_adjunto
                    WHERE requerimiento_pago_adjunto.id_requerimiento_pago = requerimiento_pago.id_requerimiento_pago
                    and requerimiento_pago_adjunto.id_estado != 7) AS count_adjunto_cabecera"),

                DB::raw("(SELECT count(archivo) FROM tesoreria.requerimiento_pago_detalle_adjunto
                    INNER JOIN tesoreria.requerimiento_pago_detalle as detalle on(
                        detalle.id_requerimiento_pago_detalle = requerimiento_pago_detalle_adjunto.id_requerimiento_pago_detalle
                    )
                    WHERE detalle.id_requerimiento_pago = requerimiento_pago.id_requerimiento_pago
                    and requerimiento_pago_detalle_adjunto.id_estado != 7) AS count_adjunto_detalle"),

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
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'requerimiento_pago.id_usuario')
            ->leftJoin('configuracion.sis_usua as autorizado', 'autorizado.id_usuario', '=', 'requerimiento_pago.usuario_autorizacion')
            ->whereIn('requerimiento_pago.id_estado', [6, 2, 5, 8, 9]);
    }
    public function ordenesCompraServicioExportarExcel()
    {
        return Excel::download(new OrdenesCompraServicioExport, 'ordenes_compras_servicio_exportar_excel.xlsx');
    }
    public function obtenerOrdenesCompraServicio()
    {
        return Orden::select(
            'log_ord_compra.*',
            'adm_contri.nro_documento',
            'adm_contri.razon_social',
            'adm_empresa.id_empresa',
            'empresa.razon_social as razon_social_empresa',
            'adm_empresa.codigo as codigo_empresa',
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
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_completo_persona"),
            'rrhh_perso.nro_documento as nro_documento_persona',
            'adm_prioridad.descripcion as prioridad',
            'autorizado.nombre_corto as nombre_autorizado',
            DB::raw("(SELECT sum(subtotal) FROM logistica.log_det_ord_compra
                        WHERE log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra
                        and log_det_ord_compra.estado != 7) AS suma_total"),
            DB::raw("(SELECT sum(total_pago) FROM tesoreria.registro_pago
                        WHERE registro_pago.id_oc = log_ord_compra.id_orden_compra
                        and registro_pago.estado != 7) AS suma_pagado")
        )
            ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftjoin('tesoreria.requerimiento_pago_estado', 'requerimiento_pago_estado.id_requerimiento_pago_estado', '=', 'log_ord_compra.estado_pago')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_ord_compra.id_condicion')
            ->leftjoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
            ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->leftjoin('contabilidad.adm_contri as empresa', 'empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.adm_cta_contri', 'adm_cta_contri.id_cuenta_contribuyente', '=', 'log_ord_compra.id_cta_principal')
            ->leftJoin('contabilidad.adm_tp_cta', 'adm_tp_cta.id_tipo_cuenta', '=', 'adm_cta_contri.id_tipo_cuenta')
            ->leftJoin('contabilidad.cont_banco as bco_contribuyente', 'bco_contribuyente.id_banco', '=', 'adm_cta_contri.id_banco')
            ->leftJoin('contabilidad.adm_contri as banco_contribuyente', 'banco_contribuyente.id_contribuyente', '=', 'bco_contribuyente.id_contribuyente')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'log_ord_compra.id_persona_pago')
            ->leftJoin('rrhh.rrhh_cta_banc', 'rrhh_cta_banc.id_cuenta_bancaria', '=', 'log_ord_compra.id_cuenta_persona_pago')
            ->leftJoin('contabilidad.cont_banco as bco_persona', 'bco_persona.id_banco', '=', 'rrhh_cta_banc.id_banco')
            ->leftJoin('contabilidad.adm_contri as banco_persona', 'banco_persona.id_contribuyente', '=', 'bco_persona.id_contribuyente')
            ->leftJoin('contabilidad.adm_tp_cta as tp_cta_persona', 'tp_cta_persona.id_tipo_cuenta', '=', 'rrhh_cta_banc.id_tipo_cuenta')
            ->leftJoin('configuracion.sis_usua as autorizado', 'autorizado.id_usuario', '=', 'log_ord_compra.usuario_autorizacion')
            ->join('administracion.adm_prioridad', 'adm_prioridad.id_prioridad', '=', 'log_ord_compra.id_prioridad_pago')
            ->whereIn('log_ord_compra.estado_pago', [8, 5, 6, 9]);
    }
    public function obtenerRegistroPagosDetalle($id)
    {
        $detalles = DB::table('tesoreria.registro_pago')
            ->select(
                'registro_pago.*',
                'sis_usua.nombre_corto',
                'sis_moneda.simbolo',
                'adm_contri.razon_social as razon_social_empresa',
                'adm_cta_contri.nro_cuenta',
                DB::raw("(SELECT count(adjunto) FROM tesoreria.registro_pago_adjuntos
                      WHERE registro_pago_adjuntos.id_pago = registro_pago.id_pago
                        and registro_pago_adjuntos.estado != 7) AS count_adjuntos")
            )
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'registro_pago.registrado_por')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'registro_pago.id_empresa')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.adm_cta_contri', 'adm_cta_contri.id_cuenta_contribuyente', '=', 'registro_pago.id_cuenta_origen');

        $query = $detalles->join('tesoreria.requerimiento_pago', 'requerimiento_pago.id_requerimiento_pago', '=', 'registro_pago.id_requerimiento_pago')
            ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'requerimiento_pago.id_moneda')
            ->where([['registro_pago.id_requerimiento_pago', '=', $id], ['registro_pago.estado', '!=', 7]])
            ->get();

        return $query;
    }
    public function obtenerOrdenesCompraServicioDetalle($id_orden_compra)
    {
        $detalles = DB::table('tesoreria.registro_pago')
            ->select(
                'registro_pago.*',
                'sis_usua.nombre_corto',
                'sis_moneda.simbolo',
                'adm_contri.razon_social as razon_social_empresa',
                'adm_cta_contri.nro_cuenta',
                'log_ord_compra.id_orden_compra',
                DB::raw("(SELECT count(adjunto) FROM tesoreria.registro_pago_adjuntos
                    WHERE registro_pago_adjuntos.id_pago = registro_pago.id_pago
                        and registro_pago_adjuntos.estado != 7) AS count_adjuntos")
            )
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'registro_pago.registrado_por')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'registro_pago.id_empresa')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.adm_cta_contri', 'adm_cta_contri.id_cuenta_contribuyente', '=', 'registro_pago.id_cuenta_origen');

        $query = $detalles->leftJoin('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'registro_pago.id_oc')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->where([['registro_pago.id_oc', '=', $id_orden_compra], ['registro_pago.estado', '!=', 7]])
            ->get();
        return $query;
    }
    public function guardarAdjuntosTesoreria(Request $request)
    {
        $mensaje = '';
        $status = 'warning';
        if (count($request->adjuntos) > 0) {
            foreach ($request->adjuntos as $key => $archivo) {

                $fechaHoy = new Carbon();
                $sufijo = $fechaHoy->format('YmdHis');
                $file = $archivo->getClientOriginalName();
                // $codigo = $codigoRequerimiento;
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                // $newNameFile = $codigo . '_' . $key . $idCategoria . $sufijo . '.' . $extension;
                $newNameFile = $request->codigo_requerimiento . $key  . $sufijo . '.' . $extension;
                // Storage::disk('archivos')->put("tesoreria/pagos/" . $newNameFile, File::get($archivo));
                Storage::disk('archivos')->put("tesoreria/otros_adjuntos/" . $newNameFile, File::get($archivo));

                // $adjunto = new RequerimientoPagoAdjunto();
                // $adjunto->id_requerimiento_pago = $request->id_requerimiento_pago;
                // $adjunto->archivo  = $newNameFile;
                // $adjunto->id_estado  = 1;
                // $adjunto->fecha_registro  = $fechaHoy;
                // $adjunto->id_categoria_adjunto = 5;
                // // $adjunto->id_usuario = Auth::user()->id_usuario;
                // $adjunto->save();

                $adjunto = new OtrosAdjuntosTesoreria();
                if ($request->id_requerimiento_pago > 0 && ($request->id_orden == null || $request->id_orden == '')) {
                    $adjunto->id_requerimiento_pago = $request->id_requerimiento_pago;
                } else if ($request->id_orden > 0 && ($request->id_requerimiento_pago == null || $request->id_requerimiento_pago == '')) {

                    $adjunto->id_orden = $request->id_orden;
                }
                $adjunto->archivo  = $newNameFile;
                $adjunto->id_estado  = 1;
                $adjunto->fecha_registro  = $fechaHoy;
                // $adjunto->id_categoria_adjunto = 5;
                $adjunto->id_tp_doc = 1;
                $adjunto->id_usuario = Auth::user()->id_usuario;
                $adjunto->save();
                $mensaje = 'Se guardo el adjunto';
                $status = 'success';
            }
        } else {
            $mensaje = 'Hubo un problema al intentar guardo el adjunto.';
            $status = 'error';
        }


        return response()->json([
            "mensaje" => $mensaje,
            "status" => $status,
            "data" => $request
        ]);
    }
    public function exportarRequerimientosPagos(Request $request)
    {
        // return $request->$request;exit;
        $data = DB::table('tesoreria.requerimiento_pago')
            ->select(
                'requerimiento_pago.*',
                'adm_prioridad.descripcion as prioridad',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'empresa.razon_social as razon_social_empresa',
                'adm_empresa.codigo as codigo_empresa',
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
                'sis_usua.nombre_corto',
                'autorizado.nombre_corto as nombre_autorizado',
                'rrhh_perso.nro_documento as dni_persona',
                DB::raw("concat(rrhh_perso.nombres, ' ' ,rrhh_perso.apellido_paterno, ' ' ,rrhh_perso.apellido_materno) AS persona"),
                DB::raw("(SELECT count(archivo) FROM tesoreria.requerimiento_pago_adjunto
                        WHERE requerimiento_pago_adjunto.id_requerimiento_pago = requerimiento_pago.id_requerimiento_pago
                        and requerimiento_pago_adjunto.id_estado != 7) AS count_adjunto_cabecera"),

                DB::raw("(SELECT count(archivo) FROM tesoreria.requerimiento_pago_detalle_adjunto
                        INNER JOIN tesoreria.requerimiento_pago_detalle as detalle on(
                            detalle.id_requerimiento_pago_detalle = requerimiento_pago_detalle_adjunto.id_requerimiento_pago_detalle
                        )
                        WHERE detalle.id_requerimiento_pago = requerimiento_pago.id_requerimiento_pago
                        and requerimiento_pago_detalle_adjunto.id_estado != 7) AS count_adjunto_detalle"),

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
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'requerimiento_pago.id_usuario')
            ->leftJoin('configuracion.sis_usua as autorizado', 'autorizado.id_usuario', '=', 'requerimiento_pago.usuario_autorizacion')
            ->whereIn('requerimiento_pago.id_estado', [6, 2, 5, 8, 9]);
        // ->where([['requerimiento_pago.id_estado', '!=', 7], ['requerimiento_pago.id_estado', '!=', 1]]);
        if (!empty($request->prioridad)) {
            $data = $data->where('adm_prioridad.id_prioridad', $request->prioridad);
        }
        if (!empty($request->empresa)) {
            $data = $data->where('adm_empresa.id_empresa', $request->empresa);
        }
        if (!empty($request->estado)) {
            $data = $data->where('requerimiento_pago_estado.id_requerimiento_pago_estado', $request->estado);
        }
        if (!empty($request->fecha_inicio)) {
            $data = $data->whereDate('requerimiento_pago.fecha_registro', '>=', $request->fecha_inicio);
        }
        if (!empty($request->fecha_final)) {
            $data = $data->whereDate('requerimiento_pago.fecha_registro', '<=', $request->fecha_final);
        }

        $data = $data->orderBy('id_requerimiento_pago', 'DESC')->get();
        $json_excel = array();
        foreach ($data as $key => $value) {
            $pagado = floatval($value->suma_pagado !== null ? $value->suma_pagado : 0);
            $total = floatval($value->monto_total);
            $por_pagar = ($total - $pagado);

            array_push($json_excel, array(
                "prioridad" => $value->prioridad,
                "codigo_empresa" => $value->codigo_empresa,
                "codigo" => $value->codigo,
                "concepto" => $value->concepto,
                "nombre_corto" => $value->nombre_corto,
                "persona" => $value->persona,
                "fecha_registro" => $value->fecha_registro,
                "simbolo" => $value->simbolo,
                "monto_total" => $value->monto_total,
                "saldo" => $por_pagar,
                "estado_doc" => $value->estado_doc,
                "nombre_autorizado" => ($value->nombre_autorizado !== '' ? $value->nombre_autorizado . ' el ' . $value->fecha_autorizacion : ''),

            ));
        }
        // $json_excel = json_encode($json_excel);
        return Excel::download(new RequerimientoPagosExport(json_encode($json_excel)), 'requerimiento_pagados.xlsx');
        // return response()->json($json_excel,200);
    }
    public function exportarOrdenesComprasServicios(Request $request)
    {
        $data = Orden::select(
            'log_ord_compra.*',
            'adm_contri.nro_documento',
            'adm_contri.razon_social',
            'adm_empresa.id_empresa',
            'empresa.razon_social as razon_social_empresa',
            'adm_empresa.codigo as codigo_empresa',
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
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_completo_persona"),
            'rrhh_perso.nro_documento as nro_documento_persona',
            'adm_prioridad.descripcion as prioridad',
            'autorizado.nombre_corto as nombre_autorizado',
            DB::raw("(SELECT sum(subtotal) FROM logistica.log_det_ord_compra
                        WHERE log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra
                        and log_det_ord_compra.estado != 7) AS suma_total"),
            DB::raw("(SELECT sum(total_pago) FROM tesoreria.registro_pago
                        WHERE registro_pago.id_oc = log_ord_compra.id_orden_compra
                        and registro_pago.estado != 7) AS suma_pagado"),
            DB::raw("(SELECT COUNT(adjuntos_logisticos.id_adjunto)
                    FROM logistica.adjuntos_logisticos
                    WHERE  adjuntos_logisticos.id_orden = log_ord_compra.id_orden_compra AND
                    adjuntos_logisticos.estado != 7) AS cantidad_adjuntos_logisticos"),
            DB::raw("(SELECT monto_cuota FROM logistica.pago_cuota_detalle
            inner join logistica.pago_cuota on pago_cuota.id_pago_cuota = pago_cuota_detalle.id_pago_cuota
            WHERE  pago_cuota.id_orden = log_ord_compra.id_orden_compra and pago_cuota_detalle.id_estado != 7 order by pago_cuota_detalle.fecha_registro desc limit 1 ) AS ultima_monto_cuota"),
            DB::raw("(SELECT sum(monto_cuota) FROM logistica.pago_cuota_detalle
            inner join logistica.pago_cuota on pago_cuota.id_orden = log_ord_compra.id_orden_compra
            WHERE pago_cuota.id_pago_cuota = pago_cuota_detalle.id_pago_cuota
            and pago_cuota_detalle.id_estado =5) AS suma_cuotas_con_autorizacion"),

        )
            ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftjoin('tesoreria.requerimiento_pago_estado', 'requerimiento_pago_estado.id_requerimiento_pago_estado', '=', 'log_ord_compra.estado_pago')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_ord_compra.id_condicion')
            ->leftjoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
            ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->leftjoin('contabilidad.adm_contri as empresa', 'empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.adm_cta_contri', 'adm_cta_contri.id_cuenta_contribuyente', '=', 'log_ord_compra.id_cta_principal')
            ->leftJoin('contabilidad.adm_tp_cta', 'adm_tp_cta.id_tipo_cuenta', '=', 'adm_cta_contri.id_tipo_cuenta')
            ->leftJoin('contabilidad.cont_banco as bco_contribuyente', 'bco_contribuyente.id_banco', '=', 'adm_cta_contri.id_banco')
            ->leftJoin('contabilidad.adm_contri as banco_contribuyente', 'banco_contribuyente.id_contribuyente', '=', 'bco_contribuyente.id_contribuyente')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'log_ord_compra.id_persona_pago')
            ->leftJoin('rrhh.rrhh_cta_banc', 'rrhh_cta_banc.id_cuenta_bancaria', '=', 'log_ord_compra.id_cuenta_persona_pago')
            ->leftJoin('contabilidad.cont_banco as bco_persona', 'bco_persona.id_banco', '=', 'rrhh_cta_banc.id_banco')
            ->leftJoin('contabilidad.adm_contri as banco_persona', 'banco_persona.id_contribuyente', '=', 'bco_persona.id_contribuyente')
            ->leftJoin('contabilidad.adm_tp_cta as tp_cta_persona', 'tp_cta_persona.id_tipo_cuenta', '=', 'rrhh_cta_banc.id_tipo_cuenta')
            ->leftJoin('configuracion.sis_usua as autorizado', 'autorizado.id_usuario', '=', 'log_ord_compra.usuario_autorizacion')
            ->join('administracion.adm_prioridad', 'adm_prioridad.id_prioridad', '=', 'log_ord_compra.id_prioridad_pago')
            ->whereIn('log_ord_compra.estado_pago', [8, 5, 6, 9, 10])
            ->where('log_ord_compra.estado', '!=', 7);

        if (!empty($request->prioridad)) {
            $data = $data->where('adm_prioridad.id_prioridad', $request->prioridad);
        }
        if (!empty($request->empresa)) {
            $data = $data->where('adm_empresa.id_empresa', $request->empresa);
        }
        if (!empty($request->estado)) {
            $data = $data->where('requerimiento_pago_estado.id_requerimiento_pago_estado', $request->estado);
        }
        if (!empty($request->fecha_inicio)) {
            $data = $data->whereDate('log_ord_compra.fecha_solicitud_pago', '>=', $request->fecha_inicio);
        }
        if (!empty($request->fecha_final)) {
            $data = $data->whereDate('log_ord_compra.fecha_solicitud_pago', '<=', $request->fecha_final);
        }

        $data = $data->orderBy('id_orden_compra', 'DESC')->get();

        $json_excel = array();
        // return $data;exit;
        foreach ($data as $key => $value) {
            $pagado = floatval($value->suma_pagado !== null ? $value->suma_pagado : 0);
            $total = floatval($value->monto_total);
            $por_pagar = ($total - $pagado);

            $tiene_pago_en_cuotas = '';
            if ($value->tiene_pago_en_cuotas === true) {
                $tiene_pago_en_cuotas = ((floatval($value->ultima_monto_cuota) > 0 ? (floatval($value->ultima_monto_cuota)) : ($value->monto_total !== null ? floatval($value->monto_total) : '0.00')));
            } else {
                $tiene_pago_en_cuotas = '(No aplica)';
            }

            array_push($json_excel, array(
                "prioridad" => $value->prioridad,
                "requerimientos" => $value->requerimientos,
                "codigo_empresa" => $value->codigo_empresa,
                "codigo" => $value->codigo,
                "razon_social" => $value->razon_social,
                "fecha_solicitud_pago" => ($value->fecha_solicitud_pago !== null ? $value->fecha_solicitud_pago : ''),
                "simbolo" => $value->simbolo,

                "monto_total" => ($value->monto_total !== null ? round($value->monto_total, 2) : '0.00'),

                "saldo" => $por_pagar,
                "tiene_pago_en_cuotas" => $tiene_pago_en_cuotas,

                "estado_doc" => $value->estado_doc,
                "nombre_autorizado" => ($value->nombre_autorizado ? $value->nombre_autorizado . ' el ' . $value->fecha_autorizacion : ''),

            ));
        }

        return Excel::download(new OrdenCompraServicioExport(json_encode($json_excel)), 'requerimiento_pagados.xlsx');
    }
    public function cuadroComparativoPagos()
    {

        $empresa = Empresa::all();
        $estados = RequerimientoPagoEstados::whereIn('id_requerimiento_pago_estado', [6, 2, 5, 8, 9])->get();
        $monedas = Moneda::all();

        $grupo_estado = array();
        foreach ($estados as $key => $value) {
            $grupo_estado = array();
            foreach ($empresa as $key_empresa => $value_empresa) {
                $cantidad = RequerimientoPago::where('id_estado', $value->id_requerimiento_pago_estado)
                    ->whereIn('requerimiento_pago.id_estado', [6, 2, 5, 8, 9])
                    ->where('id_empresa', $value_empresa->id_empresa)
                    ->count();
                // return $cantidad;exit;
                array_push($grupo_estado, array(
                    "empresa_id" => $value_empresa->id_empresa,
                    "estado_id" => $value->id_requerimiento_pago_estado,
                    "cantidad" => $cantidad,
                ));
            }
            $value->grupo = $grupo_estado;
        }
        $grupo_moneda = array();
        foreach ($monedas as $key => $value) {
            $grupo_moneda = array();
            foreach ($empresa as $key_empresa => $value_empresa) {
                $cantidad = RequerimientoPago::where('id_moneda', $value->id_moneda)
                    ->where('id_empresa', $value_empresa->id_empresa)
                    ->whereIn('requerimiento_pago.id_estado', [6, 2, 5, 8, 9])
                    ->whereNotIn('id_estado', [7, 6])
                    ->get();
                $total = 0;

                foreach ($cantidad as $key_cantidad => $value_cantidad) {
                    $monto_total = $value_cantidad->monto_total;

                    $registro_pagados = RegistroPago::where('id_requerimiento_pago',$value_cantidad->id_requerimiento_pago)->where('estado','!=',7)->get();

                    $total_registro_pagado=0;
                    if (sizeof($registro_pagados)>0) {
                        foreach ($registro_pagados as $flight) {
                            $total_registro_pagado=$total_registro_pagado + $flight->total_pago;
                        }
                    }

                    $total= $total + ($monto_total - $total_registro_pagado);
                }
                // $total= $monto_total - $total_registro_pagado;
                // return $cantidad;exit;
                array_push($grupo_moneda,array(
                    "empresa_id"=>$value_empresa->id_empresa,
                    "moneda_id"=>$value->id_moneda,
                    "total"=> number_format($total, 2, '.', ','),
                ));
            }
            $value->grupo = $grupo_moneda;
        }
        return response()->json(["empresas" => $empresa, "estados" => $estados, "monedas" => $monedas], 200);
    }
    public function cuadroComparativoOrdenes()
    {
        $empresa = Empresa::all();
        $estados = RequerimientoPagoEstados::whereIn('id_requerimiento_pago_estado', [8, 5, 6, 9, 10])->get();
        $monedas = Moneda::all();

        $grupo_estado = array();
        foreach ($estados as $key => $value) {
            $grupo_estado = array();
            foreach ($empresa as $key_empresa => $value_empresa) {
                $cantidad = Orden::select('log_ord_compra.*')
                    ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
                    ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                    ->where('log_ord_compra.estado_pago', $value->id_requerimiento_pago_estado)
                    ->where('adm_empresa.id_empresa', $value_empresa->id_empresa)
                    ->whereIn('log_ord_compra.estado_pago', [8, 5, 6, 9, 10])
                    ->where('log_ord_compra.estado', '!=', 7)
                    ->count();
                // return $cantidad;exit;
                array_push($grupo_estado, array(
                    "empresa_id" => $value_empresa->id_empresa,
                    "estado_id" => $value->id_requerimiento_pago_estado,
                    "cantidad" => $cantidad,
                ));
            }
            $value->grupo = $grupo_estado;
        }

        $grupo_moneda = array();
        foreach ($monedas as $key => $value) {
            $grupo_moneda = array();
            foreach ($empresa as $key_empresa => $value_empresa) {
                $cantidad = Orden::select('log_ord_compra.*')
                    ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
                    ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')

                    ->where('adm_empresa.id_empresa', $value_empresa->id_empresa)
                    ->where('log_ord_compra.id_moneda', $value->id_moneda)
                    ->whereIn('log_ord_compra.estado_pago', [8, 5, 9, 10])
                    ->where('log_ord_compra.estado', '!=', 7)
                    ->whereNotIn('log_ord_compra.estado_pago', [7, 6])
                    ->get();
                $total = 0;

                foreach ($cantidad as $key_cantidad => $value_cantidad) {
                    $total = $value_cantidad->monto_total + $total;
                }
                // return $cantidad;exit;
                array_push($grupo_moneda, array(
                    "empresa_id" => $value_empresa->id_empresa,
                    "moneda_id" => $value->id_moneda,
                    "total" => round($total, 2),
                ));
            }
            $value->grupo = $grupo_moneda;
        }
        return response()->json(["empresas" => $empresa, "estados" => $estados, "monedas" => $monedas], 200);
    }
}
