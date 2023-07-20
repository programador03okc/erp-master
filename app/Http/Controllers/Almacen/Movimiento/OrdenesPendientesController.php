<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use App\Exports\IngresosProcesadosExport;
use App\Exports\OrdenesPendientesExport;
use App\Exports\SeriesGuiaCompraDetalleExport;
use App\Http\Controllers\Almacen\Catalogo\CategoriaController;
use App\Http\Controllers\Almacen\Catalogo\ClasificacionController;
use App\Http\Controllers\Almacen\Catalogo\MarcaController;
use App\Http\Controllers\Almacen\Catalogo\SubCategoriaController;
use App\Http\Controllers\Almacen\Ubicacion\AlmacenController;
use App\Http\Controllers\Almacen\Movimiento\TransferenciaController;
use App\Http\Controllers\AlmacenController as GenericoAlmacenController;
use App\Http\Controllers\Tesoreria\CierreAperturaController as CierreAperturaController;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Almacen\Movimiento;
use App\Models\Almacen\MovimientoDetalle;
use App\Models\almacen\Reserva;
use App\models\Configuracion\AccesosUsuarios;
use App\Models\Tesoreria\TipoCambio;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

date_default_timezone_set('America/Lima');

class OrdenesPendientesController extends Controller
{
    public function __construct()
    {
        // session_start();
    }
    function view_ordenesPendientes()
    {
        // if (!Auth::user()->tieneAccion(83)) {
        //     return 'No autorizado';
        // }
        $almacenes = AlmacenController::mostrar_almacenes_cbo();
        $tp_doc = GenericoAlmacenController::mostrar_tp_doc_cbo();
        $tp_operacion = GenericoAlmacenController::tp_operacion_ids([5, 24, 12,28]);
        $clasificaciones_guia = GenericoAlmacenController::mostrar_guia_clas_cbo();
        $usuarios = GenericoAlmacenController::select_usuarios();
        $motivos_anu = GenericoAlmacenController::select_motivo_anu();
        $monedas = GenericoAlmacenController::mostrar_moneda_cbo();
        $clasificaciones = ClasificacionController::mostrar_clasificaciones_cbo();
        $tipos = CategoriaController::mostrar_tipos_cbo();
        $categorias = SubCategoriaController::mostrar_categorias_cbo();
        $subcategorias = MarcaController::mostrar_subcategorias_cbo();
        $unidades = GenericoAlmacenController::mostrar_unidades_cbo();
        $condiciones = GenericoAlmacenController::mostrar_condiciones_cbo();
        $sedes = GenericoAlmacenController::mostrar_sedes_cbo();
        $fechaActual = new Carbon();
        $fechaActual2 = new Carbon();
        // $array_sedes = $this->sedesPorUsuarioArray();

        $nro_oc_pendientes = $this->nroOrdenesPendientes();
        $nro_ot_pendientes = $this->nroTransformacionesPendientes();
        $nro_dev_pendientes = $this->nroDevolucionesPendientes();

        $array_accesos = [];
        $accesos_usuario = AccesosUsuarios::where('estado', 1)->where('id_usuario', Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos, $value->id_acceso);
        }

        return view('almacen/guias/ordenesPendientes', compact(
            'almacenes',
            'tp_doc',
            'tp_operacion',
            'clasificaciones_guia',
            'usuarios',
            'motivos_anu',
            'monedas',
            'tipos',
            'categorias',
            'subcategorias',
            'clasificaciones',
            'unidades',
            'condiciones',
            'sedes',
            'fechaActual',
            'fechaActual2',
            'nro_oc_pendientes',
            'nro_ot_pendientes',
            'nro_dev_pendientes',
            'array_accesos',
        ));
    }

    public function nroOrdenesPendientes()
    {
        $array_sedes = $this->sedesPorUsuarioArray();
        $nro_oc_pendientes = DB::table('logistica.log_ord_compra')
            ->where([
                ['log_ord_compra.estado', '!=', 7],
                ['log_ord_compra.en_almacen', '=', false],
                ['log_ord_compra.id_tp_documento', '=', 2]
            ])
            ->whereIn('log_ord_compra.id_sede', $array_sedes)
            ->count();
        return $nro_oc_pendientes;
    }

    public function nroTransformacionesPendientes()
    {
        // $array_sedes = $this->sedesPorUsuarioArray();
        $nro_ot_pendientes = DB::table('almacen.transformacion')
            ->join('almacen.guia_ven', function ($join) {
                $join->on('guia_ven.id_od', '=', 'transformacion.id_od');
                $join->where('guia_ven.estado', '!=', 7);
            })
            ->where([['transformacion.estado', '=', 10]])->count();
        return $nro_ot_pendientes;
    }

    public function nroDevolucionesPendientes()
    {
        $nro_dev_pendientes = DB::table('cas.devolucion')
            ->where([['devolucion.estado', '=', 2]])->count();
        return $nro_dev_pendientes;
    }

    function sedesPorUsuario()
    {
        return DB::table('almacen.alm_almacen_usuario')
            ->select('sis_sede.id_sede', 'sis_sede.descripcion')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_almacen_usuario.id_almacen')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
            ->where([
                ['alm_almacen_usuario.id_usuario', '=', Auth::user()->id_usuario],
                ['alm_almacen_usuario.estado', '!=', 7]
            ])
            ->distinct()->get();
    }

    function sedesPorUsuarioArray()
    {
        $sedes = $this->sedesPorUsuario();

        $array_sedes = [];
        foreach ($sedes as $sede) {
            // if (!array_key_exists($sede->id_sede, $array_sedes)) {
            $array_sedes[] = [$sede->id_sede];
            // }
        }

        return $array_sedes;
    }

    public function actualizarFiltrosPendientes(Request $request)
    {
        if ($request->fecha_inicio != null) {
            //$request->session()->put('pendientesFilter_fechaInicio', $request->fecha_inicio);
            session(['pendientesFilter_fechaInicio' => $request->fecha_inicio]); //->put('pendientesFilter_fechaInicio', $request->fecha_inicio);
        } else {
            $request->session()->forget('pendientesFilter_fechaInicio');
        }

        if ($request->fecha_fin != null) {
            $request->session()->put('pendientesFilter_fechaFin', $request->fecha_fin);
        } else {
            $request->session()->forget('pendientesFilter_fechaFin');
        }

        if ($request->id_sede != null) {
            $request->session()->put('pendientesFilter_idSede', $request->id_sede);
        } else {
            $request->session()->forget('pendientesFilter_idSede');
        }

        /*return response()->json(
            array(
                'response' => 'ok',
                'inicio' => session()->get('pendientesFilter_fechaInicio'),
                'fin' => session()->get('pendientesFilter_fechaFin'),
                'sede' => session()->get('pendientesFilter_idSede')
            ),
            200
        );*/
    }

    public function ordenesPendientesLista(Request $request)
    {
        $data = DB::table('logistica.log_det_ord_compra')
            ->select(
                'log_ord_compra.id_orden_compra',
                'log_ord_compra.id_tp_documento',
                'log_ord_compra.id_proveedor',
                'log_ord_compra.id_sede',
                'log_ord_compra.fecha',
                'log_ord_compra.codigo as codigo_orden',
                'log_ord_compra.codigo_softlink',
                'estados_compra.descripcion as estado_doc',
                'adm_contri.razon_social',
                'sis_usua.nombre_corto',
                // 'alm_req.fecha_entrega',
                'sis_sede.descripcion as sede_descripcion',
                // 'alm_req.codigo as codigo_requerimiento',
                // 'alm_req.concepto',
                // 'alm_req.id_almacen',
            )
            ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            ->join('logistica.estados_compra', 'estados_compra.id_estado', '=', 'log_ord_compra.estado')
            ->join('logistica.log_prove', function ($join) {
                $join->on('log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor');
                $join->where('log_prove.estado', '!=', 7);
            })
            ->join('contabilidad.adm_contri', function ($join) {
                $join->on('adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente');
                $join->where('adm_contri.estado', '!=', 7);
            })
            ->join('configuracion.sis_usua', function ($join) {
                $join->on('sis_usua.id_usuario', '=', 'log_ord_compra.id_usuario');
                $join->where('sis_usua.estado', '!=', 7);
            })
            // ->leftJoin('almacen.alm_det_req', function ($join) {
            //     $join->on('alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento');
            //     $join->where('alm_det_req.estado', '!=', 7);
            // })
            // ->join('almacen.alm_req', function ($join) {
            //     $join->on('alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento');
            //     $join->where('alm_req.estado', '!=', 7);
            // })
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
            ->where([
                ['log_ord_compra.estado', '!=', 7],
                ['log_det_ord_compra.tipo_item_id', '!=', 2],
                ['log_ord_compra.en_almacen', '=', false]
            ])
            ->whereIn('log_ord_compra.id_tp_documento', [2, 12, 13]) //orden de compra, orden de importacion , orden de devolución
            ->whereDate('log_ord_compra.fecha', '>=', (new Carbon($request->ordenes_fecha_inicio))->format('Y-m-d'))
            ->whereDate('log_ord_compra.fecha', '<=', (new Carbon($request->ordenes_fecha_fin))->format('Y-m-d'));
        // whereBetween('created_at', ['2018/11/10 12:00', '2018/11/11 10:30'])
        $array_sedes = [];
        // $result = [];

        // if ($request->ordenes_fecha_inicio !== null) {
        // }
        // if ($request->ordenes_fecha_fin !== null) {
        // }
        // if ($request->ordenes_id_sede !== null) {

        if ($request->ordenes_id_sede == 0) {
            $array_sedes = $this->sedesPorUsuarioArray();
        } else {
            $array_sedes[] = [$request->ordenes_id_sede];
        }
        $result = $data->whereIn('log_ord_compra.id_sede', $array_sedes)->distinct();
        return $result;
    }

    public function listarOrdenesPendientes(Request $request)
    {
        $query = $this->ordenesPendientesLista($request);
        return datatables($query)->toJson();
    }

    public function ordenesPendientesExcel(Request $request)
    {
        $data = $this->ordenesPendientesLista($request);
        // return $data;
        return Excel::download(new OrdenesPendientesExport(
            $data,
            $request->fecha_inicio,
            $request->fecha_fin
        ), 'ordenesPendientes.xlsx');
    }

    public function seriesExcel($id_guia_com_det)
    {
        $data = $this->listaSeries($id_guia_com_det);
        return Excel::download(new SeriesGuiaCompraDetalleExport($data), 'series-' . $id_guia_com_det . '.xlsx');
    }

    public function ingresosLista(Request $request)
    {
        $data = Movimiento::select(
            'mov_alm.*',
            'guia_com.id_proveedor',
            'adm_contri.nro_documento',
            'adm_contri.razon_social',
            'sis_usua.nombre_corto',
            'sede_guia.descripcion as sede_guia_descripcion',
            'sede_guia.id_empresa',
            'alm_almacen.descripcion as almacen_descripcion',
            'alm_almacen.id_sede',
            'guia_com.serie',
            'guia_com.numero',
            'guia_com.fecha_emision as fecha_emision_guia',
            'guia_com.fecha_almacen as fecha_almacen_guia',
            'guia_com.comentario',
            'tp_ope.descripcion as operacion_descripcion',
            'devolucion.codigo as codigo_devolucion',
            DB::raw("(SELECT count(distinct id_doc_com) FROM almacen.doc_com AS d
                    INNER JOIN almacen.guia_com_det AS guia
                        on(guia.id_guia_com = mov_alm.id_guia_com)
                    INNER JOIN almacen.doc_com_det AS doc
                        on(doc.id_guia_com_det = guia.id_guia_com_det)
                    WHERE d.id_doc_com = doc.id_doc
                      and doc.estado != 7) AS count_facturas"),

            DB::raw("(SELECT distinct id_doc_com FROM almacen.doc_com AS d
                    INNER JOIN almacen.guia_com_det AS guia on(
                        guia.id_guia_com = mov_alm.id_guia_com)
                    INNER JOIN almacen.doc_com_det AS doc on(
                        doc.id_guia_com_det = guia.id_guia_com_det and
                        doc.estado != 7)
                    WHERE d.id_doc_com = doc.id_doc LIMIT 1) AS id_doc_com"),

            DB::raw("(SELECT COUNT(*) FROM almacen.trans_detalle
                    inner join almacen.mov_alm_det on(
                        mov_alm_det.id_guia_com_det = trans_detalle.id_guia_com_det
                    )
                    where   mov_alm_det.id_mov_alm = mov_alm.id_mov_alm
                            and trans_detalle.estado != 7) AS count_transferencias"),

            DB::raw("(SELECT COUNT(*) FROM almacen.orden_despacho_det as od
                    inner join almacen.alm_det_req as req on(
                            req.id_detalle_requerimiento = od.id_detalle_requerimiento and
                            req.estado != 7
                    )
                    inner join logistica.log_det_ord_compra as log on(
                            log.id_detalle_requerimiento = req.id_detalle_requerimiento and
                            log.estado != 7
                    )
                    inner join almacen.guia_com_det as guia on(
                            guia.id_oc_det = log.id_detalle_orden and
                            guia.estado != 7
                    )
                    inner join almacen.mov_alm_det as ing on(
                            ing.id_guia_com_det = guia.id_guia_com_det and
                            ing.estado != 7
                    )
                    where   ing.id_mov_alm = mov_alm.id_mov_alm and
                            od.estado != 7 and
                            od.estado != 1) AS count_despachos_oc")
        )
            ->leftJoin('almacen.guia_com', 'guia_com.id_guia', '=', 'mov_alm.id_guia_com')
            ->leftJoin('cas.devolucion', 'devolucion.id_devolucion', '=', 'mov_alm.id_devolucion')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
            ->join('administracion.sis_sede as sede_guia', 'sede_guia.id_sede', '=', 'alm_almacen.id_sede')
            ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'guia_com.id_proveedor')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'mov_alm.usuario')
            ->join('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'mov_alm.id_operacion')
            ->where([['mov_alm.estado', '!=', 7], ['mov_alm.id_tp_mov', '=', 1]])
            ->whereIn('mov_alm.id_operacion', [2, 26, 18, 21, 24]);

        $array_sedes = [];
        if ($request->ingreso_fecha_inicio !== null) {
            $data = $data->whereDate('mov_alm.fecha_emision', '>=', (new Carbon($request->ingreso_fecha_inicio))->format('Y-m-d'));
        }
        if ($request->ingreso_fecha_fin !== null) {
            $data = $data->whereDate('mov_alm.fecha_emision', '<=', (new Carbon($request->ingreso_fecha_fin))->format('Y-m-d'));
        }
        if ($request->ingreso_id_sede !== null) {
            if ($request->ingreso_id_sede == 0) {
                $array_sedes = $this->sedesPorUsuarioArray();
            } else {
                $array_sedes[] = [$request->ingreso_id_sede];
            }
            $data = $data->whereIn('alm_almacen.id_sede', $array_sedes);
        }

        return $data;
    }

    public function listarIngresos(Request $request)
    {
        $data = $this->ingresosLista($request);
        // return datatables($query)->toJson();
        return DataTables::eloquent($data)->filterColumn('ordenes', function ($query, $keyword) {
            $sql_oc = "id_mov_alm IN (
                SELECT mov_alm_det.id_mov_alm FROM almacen.mov_alm_det
                INNER JOIN almacen.guia_com_det ON
                guia_com_det.id_guia_com_det=mov_alm_det.id_guia_com_det
                INNER JOIN logistica.log_det_ord_compra ON
                log_det_ord_compra.id_detalle_orden=guia_com_det.id_oc_det
                INNER JOIN logistica.log_ord_compra ON
                log_ord_compra.id_orden_compra=log_det_ord_compra.id_orden_compra
                WHERE   CONCAT(UPPER(log_ord_compra.codigo), UPPER(log_ord_compra.codigo_softlink)) LIKE ? )
                ";
            $query->whereRaw($sql_oc, ['%' . strtoupper($keyword) . '%']);
        })->filterColumn('facturas', function ($query, $keyword) {
            $sql_dc = "id_guia_com IN (
                SELECT guia_com_det.id_guia_com FROM almacen.guia_com_det
                INNER JOIN almacen.doc_com_det ON
                doc_com_det.id_guia_com_det=guia_com_det.id_guia_com_det
                INNER JOIN almacen.doc_com ON
                doc_com.id_doc_com=doc_com_det.id_doc
                WHERE   CONCAT(UPPER(doc_com.serie), UPPER(doc_com.numero)) LIKE ? )
                ";
            $query->whereRaw($sql_dc, ['%' . strtoupper($keyword) . '%']);
        })->filterColumn('requerimientos', function ($query, $keyword) {
            $sql_req = "id_mov_alm IN (
                SELECT mov_alm_det.id_mov_alm FROM almacen.mov_alm_det
                LEFT JOIN almacen.guia_com_det ON
                    guia_com_det.id_guia_com_det=mov_alm_det.id_guia_com_det
                LEFT JOIN logistica.log_det_ord_compra ON
                    log_det_ord_compra.id_detalle_orden=guia_com_det.id_oc_det
                LEFT JOIN almacen.alm_det_req ON
                    alm_det_req.id_detalle_requerimiento=log_det_ord_compra.id_detalle_requerimiento
                LEFT JOIN almacen.alm_req ON
                    alm_req.id_requerimiento=alm_det_req.id_requerimiento
                LEFT JOIN almacen.mov_alm ON
                        mov_alm.id_mov_alm=mov_alm_det.id_mov_alm
                LEFT JOIN almacen.transformacion ON
                    transformacion.id_transformacion=mov_alm.id_transformacion
                LEFT JOIN almacen.orden_despacho ON
                    orden_despacho.id_od=transformacion.id_od
                LEFT JOIN almacen.alm_req as req_trans ON
                    req_trans.id_requerimiento=orden_despacho.id_requerimiento
                WHERE   mov_alm.id_tp_mov = 1 and mov_alm.estado !=7 and
								CONCAT(UPPER(req_trans.codigo),UPPER(alm_req.codigo)) LIKE ? )
                ";
            $query->whereRaw($sql_req, ['%' . strtoupper($keyword) . '%']);
        })->toJson();
    }

    public function ingresosProcesadosExcel(Request $request)
    {
        $data = $this->ingresosLista($request);
        return Excel::download(new IngresosProcesadosExport(
            $data,
            $request->fecha_inicio,
            $request->fecha_fin
        ), 'Ingresos Procesados al ' . new Carbon() . '.xlsx');
    }

    public function detalleOrden($id_orden, $soloProductos=null)
    {
        $detalle = DB::table('logistica.log_det_ord_compra')
            ->select(
                'log_det_ord_compra.*',
                'alm_prod.id_producto',
                'alm_prod.codigo',
                'alm_prod.part_number',
                'alm_cat_prod.descripcion as categoria',
                'alm_subcat.descripcion as subcategoria',
                'alm_req.id_requerimiento',
                'alm_req.codigo as codigo_req',
                'alm_req.tiene_transformacion',
                'alm_prod.descripcion',
                'alm_und_medida.abreviatura',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'sis_sede.descripcion as sede_req',
                'oc_propias_view.nro_orden',
                'oc_propias_view.codigo_oportunidad',
                'oc_propias_view.id as id_oc_propia',
                'oc_propias_view.tipo',
                'adm_contri.razon_social',
                'sis_usua.nombre_corto',
                DB::raw("(SELECT SUM(cantidad) FROM almacen.guia_com_det where
                    guia_com_det.id_oc_det = log_det_ord_compra.id_detalle_orden
                    and guia_com_det.estado != 7) AS cantidad_ingresada")
            )
            // ->leftjoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_producto')
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'log_det_ord_compra.id_producto')
            ->leftjoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->leftjoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_det_ord_compra.id_unidad_medida')
            ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftjoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->leftjoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftjoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->leftjoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'log_det_ord_compra.estado')
            ->when(($soloProductos != null), function ($query) use ($soloProductos) {
                return $query->whereRaw('log_det_ord_compra.tipo_item_id = 1');
            })
            ->where([
                ['log_det_ord_compra.id_orden_compra', '=', $id_orden],
                ['log_det_ord_compra.estado', '!=', 7]
            ])
            ->get();
        return response()->json($detalle);
    }

    public function detalleOrdenesSeleccionadas(Request $request)
    {
        $ordenes = json_decode($request->oc_seleccionadas);
        $detalle = DB::table('logistica.log_det_ord_compra')
            ->select(
                'log_det_ord_compra.*',
                'alm_prod.id_producto',
                'alm_prod.codigo',
                'alm_prod.part_number',
                'alm_cat_prod.descripcion as categoria',
                'alm_subcat.descripcion as subcategoria',
                'alm_prod.series',
                'alm_prod.descripcion',
                'alm_und_medida.abreviatura',
                'log_ord_compra.codigo as codigo_oc',
                'alm_prod.id_categoria',
                'sis_moneda.simbolo',
                DB::raw('(SELECT SUM(guia_com_det.cantidad) FROM almacen.guia_com_det
                          WHERE guia_com_det.id_oc_det = log_det_ord_compra.id_detalle_orden
                            AND guia_com_det.estado != 7)
                          AS suma_cantidad_guias')
            )
            ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            // ->leftjoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'log_det_ord_compra.id_producto')
            ->leftjoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->leftjoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_det_ord_compra.id_unidad_medida')
            ->leftjoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->whereIn('log_det_ord_compra.id_orden_compra', $ordenes)
            ->whereNotNull('log_det_ord_compra.id_producto')
            ->where([
                ['log_det_ord_compra.estado', '!=', 7],
                ['log_det_ord_compra.estado', '!=', 28],
                ['log_det_ord_compra.tipo_item_id', '!=', 2]
            ])
            ->get();

        return response()->json($detalle);
    }

    public function detalleMovimiento($id_guia)
    {
        $detalle = DB::table('almacen.guia_com_det')
            ->select(
                'guia_com_det.*',
                'alm_prod.codigo',
                'alm_prod.part_number',
                'alm_prod.descripcion',
                'alm_und_medida.abreviatura',
                'guia_com.serie',
                'guia_com.numero',
                'guia_com.id_almacen',
                'log_ord_compra.codigo as codigo_orden',
                'alm_req.codigo as codigo_req',
                'sis_sede.descripcion as sede_req',
                'req_od.codigo as codigo_req_od',
                'transformacion.codigo as codigo_transfor',
                'sede_req_od.descripcion as sede_req_od',

                'req_sobrante.codigo as codigo_req_sobrante',
                'tr_sobrante.codigo as codigo_tr_sobrante',
                'sede_req_sobrante.descripcion as sede_req_sob',

                'trans.codigo as codigo_trans',
                'req_tra.codigo as codigo_req_trans',
                'sede_tra.descripcion as sede_req_trans'
            )
            ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->leftjoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det')
            ->leftjoin('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftjoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')

            ->leftjoin('almacen.transfor_transformado', 'transfor_transformado.id_transformado', '=', 'guia_com_det.id_transformado')
            ->leftjoin('almacen.transformacion', 'transformacion.id_transformacion', '=', 'transfor_transformado.id_transformacion')
            ->leftjoin('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'transformacion.id_od')
            ->leftjoin('almacen.alm_req as req_od', 'req_od.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
            ->leftjoin('administracion.sis_sede as sede_req_od', 'sede_req_od.id_sede', '=', 'req_od.id_sede')

            ->leftjoin('almacen.transfor_sobrante', 'transfor_sobrante.id_sobrante', '=', 'guia_com_det.id_sobrante')
            ->leftjoin('almacen.transformacion as tr_sobrante', 'tr_sobrante.id_transformacion', '=', 'transfor_sobrante.id_transformacion')
            ->leftjoin('almacen.orden_despacho as od_sobrante', 'od_sobrante.id_od', '=', 'tr_sobrante.id_od')
            ->leftjoin('almacen.alm_req as req_sobrante', 'req_sobrante.id_requerimiento', '=', 'od_sobrante.id_requerimiento')
            ->leftjoin('administracion.sis_sede as sede_req_sobrante', 'sede_req_sobrante.id_sede', '=', 'req_sobrante.id_sede')

            ->leftjoin('almacen.trans_detalle', 'trans_detalle.id_trans_detalle', '=', 'guia_com_det.id_trans_detalle')
            ->leftjoin('almacen.trans', 'trans.id_transferencia', '=', 'trans_detalle.id_transferencia')
            ->leftjoin('almacen.alm_det_req as det_req_tra', 'det_req_tra.id_detalle_requerimiento', '=', 'trans_detalle.id_requerimiento_detalle')
            ->leftjoin('almacen.alm_req as req_tra', 'req_tra.id_requerimiento', '=', 'det_req_tra.id_requerimiento')
            ->leftjoin('administracion.sis_sede as sede_tra', 'sede_tra.id_sede', '=', 'req_tra.id_sede')

            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_com_det.id_producto')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where([['guia_com_det.id_guia_com', '=', $id_guia], ['guia_com_det.estado', '!=', 7]])
            ->get();

        $lista = [];

        foreach ($detalle as $det) {

            $series = DB::table('almacen.alm_prod_serie')
                ->select('alm_prod_serie.*')
                ->where([
                    ['alm_prod_serie.id_guia_com_det', '=', $det->id_guia_com_det],
                    ['alm_prod_serie.estado', '=', 1]
                ])
                ->get();

            array_push($lista, [
                'id_guia_com_det' => $det->id_guia_com_det,
                'id_almacen' => $det->id_almacen,
                'id_producto' => $det->id_producto,
                'codigo' => $det->codigo,
                'part_number' => $det->part_number,
                'descripcion' => $det->descripcion,
                'cantidad' => $det->cantidad,
                'abreviatura' => $det->abreviatura,
                'serie' => $det->serie,
                'numero' => $det->numero,
                'codigo_orden' => $det->codigo_orden,
                'codigo_transfor' => ($det->codigo_transfor !== null ? $det->codigo_transfor : $det->codigo_tr_sobrante),
                'codigo_trans' => $det->codigo_trans,
                'codigo_req' => ($det->codigo_req !== null ? $det->codigo_req : ($det->codigo_req_od !== null ? $det->codigo_req_od : ($det->codigo_req_trans !== null ? $det->codigo_req_trans : $det->codigo_req_sobrante))),
                'sede_req' => ($det->sede_req !== null ? $det->sede_req : ($det->sede_req_od !== null ? $det->sede_req_od : ($det->sede_req_trans !== null ? $det->sede_req_trans : $det->sede_req_sob))),
                'series' => $series
            ]);
        }
        return response()->json($lista);
    }

    public function listaSeries($id_guia_com_det)
    {
        $series = DB::table('almacen.alm_prod_serie')
            ->select('alm_prod_serie.*')
            ->where([
                ['alm_prod_serie.id_guia_com_det', '=', $id_guia_com_det],
                ['alm_prod_serie.estado', '=', 1]
            ])
            ->get();
        return $series;
    }

    public function mostrar_series($id_guia_com_det)
    {
        $series = $this->listaSeries($id_guia_com_det);
        return response()->json($series);
    }

    public function actualizar_series(Request $request)
    {
        $lista = json_decode($request->series);

        foreach ($lista as $s) {
            $data = DB::table('almacen.alm_prod_serie')
                ->where('id_prod_serie', $s->id_prod_serie)
                ->update(['serie' => $s->serie]);
        }
        return response()->json($data);
    }

    public function guardar_series(Request $request)
    {
        $lista = json_decode($request->series);
        $data = null;

        foreach ($lista as $serie) {
            $exist = DB::table('almacen.alm_prod_serie')
                ->where([['serie', '=', $serie], ['id_guia_com_det', '=', $request->id_guia_com_det]])
                ->first();

            if ($exist == null) {
                $data = DB::table('almacen.alm_prod_serie')->insert(
                    [
                        'id_prod' => $request->id_producto,
                        'id_almacen' => $request->id_almacen,
                        'serie' => $serie,
                        'estado' => 1,
                        'fecha_registro' => date('Y-m-d H:i:s'),
                        'id_guia_com_det' => $request->id_guia_com_det
                    ]
                );
            }
        }
        return response()->json($data);
    }

    public function verGuiasOrden($id_orden)
    {
        $guias = DB::table('almacen.guia_com_oc')
            ->select(
                'guia_com_oc.*',
                'guia_com.serie',
                'guia_com.numero',
                'guia_com.fecha_emision',
                'alm_almacen.descripcion as almacen',
                'tp_ope.descripcion as operacion',
                'responsable.nombre_corto as nombre_responsable',
                'adm_estado_doc.estado_doc',
                'registrado_por.nombre_corto as nombre_registrado_por',
                'adm_estado_doc.bootstrap_color'
            )
            ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_oc.id_guia_com')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'guia_com.id_almacen')
            ->join('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'guia_com.id_operacion')
            ->join('configuracion.sis_usua as responsable', 'responsable.id_usuario', '=', 'guia_com.usuario')
            ->join('configuracion.sis_usua as registrado_por', 'registrado_por.id_usuario', '=', 'guia_com.registrado_por')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'guia_com.estado')
            ->where([['guia_com_oc.id_oc', '=', $id_orden], ['guia_com_oc.estado', '!=', 7]])
            ->get();
        return response()->json($guias);
    }

    public function guardar_guia_com_oc(Request $request)
    {
        try {
            DB::beginTransaction();
            // database queries here
            $id_ingreso = null;
            $id_guia = null;
            $msj_trans = '';

            $tipo = '';
            $mensaje = '';

            $id_tp_doc_almacen = 1;
            $id_usuario = Auth::user()->id_usuario;
            $fecha_registro = date('Y-m-d H:i:s');

            $periodo_estado = CierreAperturaController::consultarPeriodo($request->fecha_emision, $request->id_almacen);

            if (intval($periodo_estado) == 2){
                $tipo = 'warning';
                $mensaje = 'El periodo esta cerrado. Consulte con contabilidad.';
            } else {
                //verifica si existe un ingreso ya generado
                $valida = DB::table('almacen.guia_com')
                    ->where([
                        ['serie', '=', $request->serie],
                        ['numero', '=', $request->numero],
                        ['id_proveedor', '=', $request->id_proveedor],
                        ['estado', '=', 1]
                    ])
                    ->first();

                if (!isset($valida)) {
                    //Genero la Guia
                    $id_guia = DB::table('almacen.guia_com')->insertGetId(
                        [
                            'id_tp_doc_almacen' => $id_tp_doc_almacen,
                            'serie' => $request->serie,
                            'numero' => $request->numero,
                            'id_proveedor' => $request->id_proveedor,
                            'fecha_emision' => $request->fecha_emision,
                            'fecha_almacen' => $request->fecha_almacen,
                            'id_almacen' => $request->id_almacen,
                            'id_guia_clas' => $request->id_guia_clas,
                            'id_operacion' => $request->id_operacion,
                            'comentario' => $request->comentario,
                            // 'id_oc' => $request->id_orden_compra,
                            'usuario' => $id_usuario,
                            'registrado_por' => $id_usuario,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro,
                        ],
                        'id_guia'
                    );
                    // AlmacenController::guardar_oc($id_guia, $request->id_orden_compra);
                    //Genero el ingreso
                    $codigo = GenericoAlmacenController::nextMovimiento(
                        1, //ingreso
                        $request->fecha_emision, // $request->fecha_almacen, se cambio a solicitud del sr juan mamani 3/01/2023
                        $request->id_almacen
                    );
    
                    $id_ingreso = DB::table('almacen.mov_alm')->insertGetId(
                        [
                            'id_almacen' => $request->id_almacen,
                            'id_tp_mov' => 1, //Ingresos
                            'codigo' => $codigo,
                            'fecha_emision' => $request->fecha_almacen,
                            'id_guia_com' => $id_guia,
                            'id_operacion' => $request->id_operacion,
                            'id_transformacion' => ($request->id_transformacion !== null ? $request->id_transformacion : null),
                            'id_devolucion' => ($request->id_devolucion !== null ? $request->id_devolucion : null),
                            'revisado' => 0,
                            'usuario' => $id_usuario,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro,
                        ],
                        'id_mov_alm'
                    );
    
                    if ($request->id_transformacion !== null) {
                        DB::table('almacen.transformacion')
                            ->where('id_transformacion', $request->id_transformacion)
                            ->update([
                                'estado' => 9,
                                'id_moneda' => $request->moneda_transformacion
                            ]); //Procesado
                    }
                    $detalle_oc = json_decode($request->detalle);
    
                    //Ingreso por transformacion
                    if ($request->id_operacion == '26') {
                        $id_od = $request->id_od;
                        $id_requerimiento = $request->id_requerimiento;
    
                        $tipo_cambio = TipoCambio::where([['moneda', '=', 2], ['fecha', '<=', $request->fecha_almacen]])
                            ->orderBy('fecha', 'DESC')->first();
    
                        foreach ($detalle_oc as $det) {
                            //Agrega sobrante
                            $id_sobrante = null;
                            if ($det->id_moneda == $request->moneda_transformacion) {
                                $unitario = $det->unitario;
                            } else {
                                if ($det->id_moneda == 1) { //soles
                                    $unitario = $det->unitario * $tipo_cambio->venta; //$request->tipo_cambio_transformacion;
                                } else { //dolares
                                    $unitario = $det->unitario / $tipo_cambio->venta; //$request->tipo_cambio_transformacion;
                                }
                            }
                            // $unitario = $request->moneda_transformacion == 2 ? ($det->unitario * $request->tipo_cambio_transformacion) : $det->unitario;
    
                            if ($det->tipo == "sobrante") {
    
                                if ($det->id_producto == null) {
    
                                    $id_producto = DB::table('almacen.alm_prod')->insertGetId(
                                        [
                                            'part_number' => $det->part_number,
                                            'id_categoria' => $det->id_categoria,
                                            'id_subcategoria' => $det->id_subcategoria,
                                            'id_clasif' => 2, //$det->id_clasif,
                                            'descripcion' => strtoupper($det->descripcion),
                                            'id_unidad_medida' => $det->id_unidad_medida,
                                            'series' => $det->control_series,
                                            'id_moneda' => $det->id_moneda,
                                            'id_usuario' => $id_usuario,
                                            'estado' => 1,
                                            'fecha_registro' => date('Y-m-d H:i:s')
                                        ],
                                        'id_producto'
                                    );
    
                                    $codigo = GenericoAlmacenController::leftZero(7, $id_producto);
    
                                    DB::table('almacen.alm_prod')
                                        ->where('id_producto', $id_producto)
                                        ->update(['codigo' => $codigo]);
    
                                    // $id_sobrante = DB::table('almacen.transfor_sobrante')->insertGetId(
                                    //     [
                                    //         'id_transformacion' => $request->id_transformacion,
                                    //         'id_producto' => $det->id_producto,
                                    //         'cantidad' => $det->cantidad,
                                    //         'valor_unitario' => $det->unitario,
                                    //         'valor_total' => (floatval($det->unitario) * floatval($det->cantidad)),
                                    //         'estado' => 1,
                                    //         'fecha_registro' => $fecha_registro,
                                    //     ],
                                    //     'id_sobrante'
                                    // );
                                } else {
                                    $id_producto = $det->id_producto;
                                }
    
                                DB::table('almacen.transfor_sobrante')
                                    ->where('id_sobrante', $det->id)
                                    ->update([
                                        'id_producto' => $id_producto,
                                        'cantidad' => $det->cantidad,
                                        'valor_unitario' => $unitario,
                                        'valor_total' => (floatval($unitario) * floatval($det->cantidad))
                                    ]);
                            } else if ($det->tipo == "transformado") {
    
                                DB::table('almacen.transfor_transformado')
                                    ->where('id_transformado', $det->id)
                                    ->update([
                                        'valor_unitario' => $unitario,
                                        'valor_total' => (floatval($unitario) * floatval($det->cantidad))
                                    ]);
    
                                if ($id_requerimiento !== null) {
                                    //Realiza la reserva en el requerimiento con item tiene transformacion
                                    // $det_req = DB::table('almacen.alm_det_req')
                                    //     ->where([
                                    //         ['id_requerimiento', '=', $id_requerimiento],
                                    //         ['tiene_transformacion', '=', true],
                                    //         ['id_producto', '=', $det->id_producto]
                                    //     ])
                                    //     ->first();
                                    $det_req = DB::table('almacen.transfor_transformado')
                                        ->select('alm_det_req.*')
                                        ->join('almacen.orden_despacho_det', 'orden_despacho_det.id_od_detalle', '=', 'transfor_transformado.id_od_detalle')
                                        ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'orden_despacho_det.id_detalle_requerimiento')
                                        ->where('transfor_transformado.id_transformado', $det->id)
                                        ->first();
                                    //realiza la reserva del transformado
                                    DB::table('almacen.alm_det_req')
                                        ->where('id_detalle_requerimiento', $det_req->id_detalle_requerimiento)
                                        ->update([
                                            'estado' => 10
                                        ]);
    
                                    DB::table('almacen.alm_reserva')
                                        ->insert([
                                            'codigo' => Reserva::crearCodigo($request->id_almacen),
                                            'id_producto' => $det->id_producto,
                                            'stock_comprometido' => $det->cantidad,
                                            'id_almacen_reserva' => $request->id_almacen,
                                            'id_detalle_requerimiento' => $det_req->id_detalle_requerimiento,
                                            'id_transformado' => $det->id,
                                            'estado' => 1,
                                            'usuario_registro' => $id_usuario,
                                            'fecha_registro' => new Carbon(),
                                        ]);
                                }
                                $id_producto = $det->id_producto;
                            }
                            //Guardo los items de la guia
                            $id_guia_com_det = DB::table('almacen.guia_com_det')->insertGetId(
                                [
                                    "id_guia_com" => $id_guia,
                                    "id_producto" => $id_producto,
                                    "cantidad" => $det->cantidad,
                                    // "id_unid_med" => $det->id_unidad_medida,
                                    "usuario" => $id_usuario,
                                    // "tipo_transfor" => $det->tipo,
                                    "id_transformado" => ($det->tipo == "transformado" ? $det->id : null),
                                    "id_sobrante" => ($det->tipo == "sobrante" ? $det->id : null),
                                    "id_moneda" => $det->id_moneda,
                                    "unitario" => $unitario,
                                    "total" => (floatval($unitario) * floatval($det->cantidad)),
                                    "unitario_adicional" => 0,
                                    'estado' => 1,
                                    'fecha_registro' => $fecha_registro,
                                ],
                                'id_guia_com_det'
                            );
    
                            if ($det->series !== null) {
                                //agrega series
                                foreach ($det->series as $serie) {
                                    DB::table('almacen.alm_prod_serie')->insert(
                                        [
                                            'id_prod' => $id_producto,
                                            'id_almacen' => $request->id_almacen,
                                            'serie' => $serie,
                                            'estado' => 1,
                                            'fecha_registro' => $fecha_registro,
                                            'id_guia_com_det' => $id_guia_com_det,
                                            'fecha_ingreso_soft' => $request->fecha_almacen,
                                            'precio_unitario_soft' => $det->unitario,
                                            'doc_ingreso_soft' => ($request->serie . '-' . $request->numero),
                                            'moneda_soft' => $det->id_moneda,
                                        ]
                                    );
                                }
                            }
                            //Guardo los items del ingreso
                            DB::table('almacen.mov_alm_det')->insertGetId(
                                [
                                    'id_mov_alm' => $id_ingreso,
                                    'id_producto' => $id_producto,
                                    // 'id_posicion' => $det->id_posicion,
                                    'cantidad' => $det->cantidad,
                                    'costo_promedio' => floatval($unitario),
                                    'valorizacion' => (floatval($unitario) * floatval($det->cantidad)),
                                    'usuario' => $id_usuario,
                                    'id_guia_com_det' => $id_guia_com_det,
                                    'estado' => 1,
                                    'fecha_registro' => $fecha_registro,
                                ],
                                'id_mov_alm_det'
                            );
                            OrdenesPendientesController::actualiza_prod_ubi($id_producto, $request->id_almacen);
                        }
    
                        // $od_detalles = DB::table('almacen.orden_despacho_det')
                        //     ->where('id_od', $id_od)
                        //     ->get();
    
                        // foreach ($od_detalles as $det) {
                        //     $detreq = DB::table('almacen.alm_det_req')
                        //         ->where('id_detalle_requerimiento', $det->id_detalle_requerimiento)
                        //         ->first();
    
                        //     $detdes = DB::table('almacen.orden_despacho_det')
                        //         ->select(DB::raw('SUM(cantidad) as suma_cantidad'))
                        //         ->join('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'orden_despacho_det.id_od')
                        //         ->join('almacen.transformacion', 'transformacion.id_od', '=', 'orden_despacho.id_od')
                        //         ->where([
                        //             ['orden_despacho_det.id_detalle_requerimiento', '=', $det->id_detalle_requerimiento],
                        //             ['transformacion.estado', '=', 10]
                        //         ])
                        //         ->first();
                        //     //orden de despacho detalle estado   procesado
                        //     if ($detdes->suma_cantidad >= $detreq->cantidad) {
                        //         DB::table('almacen.alm_det_req')
                        //             ->where('id_detalle_requerimiento', $det->id_detalle_requerimiento)
                        //             ->update(['estado' => 10]);
                        //     }
                        // }
    
                        // $culminados = DB::table('almacen.alm_det_req')
                        //     ->where([
                        //         ['id_requerimiento', '=', $id_requerimiento],
                        //         ['estado', '=', 10]
                        //     ])
                        //     ->count();
    
                        // $todos = DB::table('almacen.alm_det_req')
                        //     ->where([
                        //         ['id_requerimiento', '=', $id_requerimiento],
                        //         // ['tiene_transformacion','=',false],
                        //         ['estado', '!=', 7]
                        //     ])
                        //     ->count();
    
                        // if ($culminados == $todos) {
                        //     DB::table('almacen.alm_req')
                        //         ->where('id_requerimiento', $id_requerimiento)
                        //         ->update(['estado' => 10]);
                        // }
    
                        DB::table('almacen.alm_req_obs')
                            ->insert([
                                'id_requerimiento' => $id_requerimiento,
                                'accion' => 'INGRESADO',
                                'descripcion' => 'Ingresado a Almacén con Guía ' . $request->serie . '-' . $request->numero . '.',
                                'id_usuario' => $id_usuario,
                                'fecha_registro' => $fecha_registro
                            ]);
                    }
                    //Ingreso por compra guia compra o importacion
                    else if ($request->id_operacion == '2' || $request->id_operacion == '18') {
                        $ids_ocd = [];
    
                        foreach ($detalle_oc as $d) {
                            if ($d->id_detalle_orden !== null) {
                                array_push($ids_ocd, $d->id_detalle_orden);
                            }
                        }
    
                        $detalle = DB::table('logistica.log_det_ord_compra')
                            ->select(
                                'log_det_ord_compra.*',
                                'log_ord_compra.id_moneda as id_moneda_orden',
                                'alm_prod.id_producto',
                                'alm_prod.id_moneda as id_moneda_producto',
                                'alm_req.id_sede',
                                'alm_req.id_requerimiento',
                                'alm_req.id_almacen as id_almacen_destino'
                            )
                            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'log_det_ord_compra.id_producto')
                            ->leftjoin('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
                            ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
                            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
                            ->whereIn('id_detalle_orden', $ids_ocd)
                            ->get();
    
                        $cantidad = 0;
                        $padres_oc = [];
                        $padres_req = [];
    
                        $tipo_cambio = TipoCambio::where([['moneda', '=', 2], ['fecha', '<=', $request->fecha_almacen]])
                            ->orderBy('fecha', 'DESC')->first();
    
                        foreach ($detalle as $det) {
    
                            if (!in_array($det->id_orden_compra, $padres_oc)) {
                                array_push($padres_oc, $det->id_orden_compra);
                            }
                            if (!in_array($det->id_requerimiento, $padres_req)) {
                                array_push($padres_req, $det->id_requerimiento);
                            }
                            $series = [];
                            foreach ($detalle_oc as $d) {
                                if ($det->id_detalle_orden == $d->id_detalle_orden) {
                                    $cantidad = $d->cantidad;
                                    $series = $d->series;
                                    break;
                                }
                            }
                            //conversion de moneda
                            if ($det->id_moneda_producto == $det->id_moneda_orden) {
                                $unitario = floatval($det->precio);
                            } else {
                                if ($det->id_moneda_producto == 1) { //soles
                                    $unitario = floatval($det->precio) * $tipo_cambio->venta;
                                } else { //dolares
                                    $unitario = floatval($det->precio) / $tipo_cambio->venta;
                                }
                            }
                            //Guardo los items de la guia
                            $id_guia_com_det = DB::table('almacen.guia_com_det')->insertGetId(
                                [
                                    "id_guia_com" => $id_guia,
                                    "id_producto" => $det->id_producto,
                                    "cantidad" => $cantidad,
                                    "id_unid_med" => $det->id_unidad_medida,
                                    "usuario" => $id_usuario,
                                    "id_oc_det" => $det->id_detalle_orden,
                                    "unitario" => $det->precio,
                                    "total" => (floatval($det->precio) * floatval($cantidad)),
                                    "unitario_adicional" => 0,
                                    'estado' => 1,
                                    'fecha_registro' => $fecha_registro,
                                ],
                                'id_guia_com_det'
                            );
                            //agrega series
                            foreach ($series as $serie) {
                                DB::table('almacen.alm_prod_serie')->insert(
                                    [
                                        'id_prod' => $det->id_producto,
                                        'id_almacen' => $request->id_almacen,
                                        'serie' => $serie,
                                        'estado' => 1,
                                        'fecha_registro' => $fecha_registro,
                                        'id_guia_com_det' => $id_guia_com_det,
                                        'fecha_ingreso_soft' => $request->fecha_almacen,
                                        'precio_unitario_soft' => $det->precio,
                                        'doc_ingreso_soft' => ($request->serie . '-' . $request->numero),
                                        'moneda_soft' => $det->id_moneda_orden,
                                    ]
                                );
                            }
                            //Guardo los items del ingreso
                            DB::table('almacen.mov_alm_det')->insertGetId(
                                [
                                    'id_mov_alm' => $id_ingreso,
                                    'id_producto' => $det->id_producto,
                                    'cantidad' => $cantidad,
                                    'costo_promedio' => floatval($unitario),
                                    'valorizacion' => (floatval($unitario) * floatval($cantidad)),
                                    'usuario' => $id_usuario,
                                    'id_guia_com_det' => $id_guia_com_det,
                                    'estado' => 1,
                                    'fecha_registro' => $fecha_registro,
                                ],
                                'id_mov_alm_det'
                            );
    
                            OrdenesPendientesController::actualiza_prod_ubi($det->id_producto, $request->id_almacen);
                            //actualiza el estado de la orden y requerimiento
                            $this->actualizaEstadoOrden($det, $cantidad, $request->id_almacen, $id_guia_com_det);
                        }
                        //actualiza estado oc padre
                        $this->actualizaEstadoPadres($padres_oc, $padres_req);
                    }
                    //Ingreso por devolucion de cliente o proveedor
                    else if ($request->id_operacion == '24' || $request->id_operacion == '5') {
    
                        $tipo_cambio = TipoCambio::where([['moneda', '=', 2], ['fecha', '<=', $request->fecha_almacen]])
                            ->orderBy('fecha', 'DESC')->first();
    
                        DB::table('cas.devolucion')
                            ->where('id_devolucion', $request->id_devolucion)
                            ->update([
                                'estado' => 3,
                                'id_moneda' => $request->moneda_devolucion,
                                'tipo_cambio' => $tipo_cambio->venta,
                            ]);
    
                        foreach ($detalle_oc as $det) {
    
                            if ($det->id_moneda == $request->moneda_devolucion) {
                                $unitario = $det->unitario;
                            } else {
                                if ($det->id_moneda == 1) { //soles
                                    $unitario = $det->unitario * $tipo_cambio->venta;
                                } else { //dolares
                                    $unitario = $det->unitario / $tipo_cambio->venta;
                                }
                            }
                            //Guardo los items de la guia
                            $id_guia_com_det = DB::table('almacen.guia_com_det')->insertGetId(
                                [
                                    "id_guia_com" => $id_guia,
                                    "id_producto" => $det->id_producto,
                                    "cantidad" => $det->cantidad,
                                    "id_unid_med" => $det->id_unidad_medida,
                                    "usuario" => $id_usuario,
                                    "id_devolucion_detalle" => $det->id,
                                    "unitario" => floatval($det->unitario),
                                    "total" => (floatval($det->unitario) * floatval($det->cantidad)),
                                    "unitario_adicional" => 0,
                                    'estado' => 1,
                                    'fecha_registro' => $fecha_registro,
                                ],
                                'id_guia_com_det'
                            );
    
                            if ($det->series !== null) {
                                //agrega series
                                foreach ($det->series as $serie) {
                                    DB::table('almacen.alm_prod_serie')->insert(
                                        [
                                            'id_prod' => $det->id_producto,
                                            'id_almacen' => $request->id_almacen,
                                            'serie' => $serie,
                                            'estado' => 1,
                                            'fecha_registro' => $fecha_registro,
                                            'id_guia_com_det' => $id_guia_com_det,
                                            'fecha_ingreso_soft' => $request->fecha_almacen,
                                            'precio_unitario_soft' => $det->unitario,
                                            'doc_ingreso_soft' => ($request->serie . '-' . $request->numero),
                                            'moneda_soft' => $request->moneda_devolucion,
                                        ]
                                    );
                                }
                            }
                            //Guardo los items del ingreso
                            DB::table('almacen.mov_alm_det')->insertGetId(
                                [
                                    'id_mov_alm' => $id_ingreso,
                                    'id_producto' => $det->id_producto,
                                    'cantidad' => $det->cantidad,
                                    'costo_promedio' => floatval($unitario),
                                    'valorizacion' => (floatval($unitario) * floatval($det->cantidad)),
                                    'usuario' => $id_usuario,
                                    'id_guia_com_det' => $id_guia_com_det,
                                    'estado' => 1,
                                    'fecha_registro' => $fecha_registro,
                                ],
                                'id_mov_alm_det'
                            );
    
                            OrdenesPendientesController::actualiza_prod_ubi($det->id_producto, $request->id_almacen);
                        }
                    }
                    $tipo = 'success';
                    $mensaje = 'Se proceso el ingreso correctamente.';
                } else {
                    $tipo = 'warning';
                    $mensaje = 'Ya existe la serie-número de Guía!';
                }
            } 
            DB::commit();
            return response()->json([
                'tipo' => $tipo,
                'mensaje' => $mensaje,
                'id_ingreso' => $id_ingreso, 'id_guia' => $id_guia,
                'nroOrdenesPendientes' => $this->nroOrdenesPendientes(),
                'nroTransformacionesPendientes' => $this->nroTransformacionesPendientes(),
                'nroDevolucionesPendientes' => $this->nroDevolucionesPendientes(), 200
            ]);
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar el ingreso. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    public function actualizaEstadoPadres($padres_oc, $padres_req)
    {
        foreach ($padres_oc as $padre) {
            $count_alm = DB::table('logistica.log_det_ord_compra')
                ->where([
                    ['id_orden_compra', '=', $padre],
                    ['tipo_item_id', '=', 1],
                    ['estado', '=', 28]
                ])
                ->count();

            $count_todo = DB::table('logistica.log_det_ord_compra')
                ->where([
                    ['id_orden_compra', '=', $padre],
                    ['tipo_item_id', '=', 1],
                    ['estado', '!=', 7]
                ])
                ->count();

            if ($count_todo == $count_alm) {
                //cambiar orden En Almacen
                DB::table('logistica.log_ord_compra')
                    ->where('id_orden_compra', $padre)
                    ->update([
                        'en_almacen' => true,
                        'estado' => 28
                    ]);
            } else {
                DB::table('logistica.log_ord_compra')
                    ->where('id_orden_compra', $padre)
                    ->update(['estado' => 27]);
            }

            foreach ($padres_req as $padre) {
                $count_alm = DB::table('almacen.alm_det_req')
                    ->where([
                        ['id_requerimiento', '=', $padre],
                        ['estado', '=', 28]
                    ])
                    ->count();

                $count_todo = DB::table('almacen.alm_det_req')
                    ->where([
                        ['id_requerimiento', '=', $padre],
                        ['tiene_transformacion', '=', false],
                        ['estado', '!=', 7]
                    ])
                    ->count();

                if ($count_todo == $count_alm) {
                    //cambiar orden En Almacen
                    DB::table('almacen.alm_req')
                        ->where('id_requerimiento', $padre)
                        ->update(['estado' => 28]);
                } else {
                    DB::table('almacen.alm_req')
                        ->where('id_requerimiento', $padre)
                        ->update(['estado' => 27]);
                }
            }
        }
    }
    public function actualizaEstadoOrden($det, $cantidad, $id_almacen, $id_guia_com_det)
    {
        $padres_req = [];
        $id_usuario = Auth::user()->id_usuario;
        $fecha_registro = date('Y-m-d H:i:s');
        //cambiar estado orden
        $ant = DB::table('almacen.guia_com_det')
            ->select(DB::raw('SUM(cantidad) AS suma_cantidad'))
            ->where([['id_oc_det', '=', $det->id_detalle_orden], ['estado', '!=', 7]])
            ->first();

        $suma = ($ant !== null ? $ant->suma_cantidad : 0);

        if ($det->id_detalle_requerimiento !== null) {
            $dreq = DB::table('almacen.alm_det_req')
                ->select(
                    'alm_req.id_requerimiento',
                    'alm_req.id_almacen',
                    'alm_det_req.cantidad',
                    'alm_req.id_tipo_requerimiento'
                )
                ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
                ->where('id_detalle_requerimiento', $det->id_detalle_requerimiento)
                ->first();

            if ($dreq !== null) {
                if (!in_array($dreq->id_requerimiento, $padres_req)) {
                    array_push($padres_req, $dreq->id_requerimiento);
                }
            }
        }

        if ($det->cantidad <= $suma) {
            DB::table('logistica.log_det_ord_compra')
                ->where('id_detalle_orden', $det->id_detalle_orden)
                ->update(['estado' => 28]); //en almacen Total

            if ($det->id_detalle_requerimiento !== null) {
                $ant_oc = DB::table('logistica.log_det_ord_compra')
                    ->select(DB::raw('SUM(cantidad) AS suma_cantidad'))
                    ->where('id_detalle_requerimiento', $det->id_detalle_requerimiento)
                    ->where('estado', 28) //en almacen Total
                    ->orWhere('estado', 10) //culminado
                    ->first();

                $stock = DB::table('almacen.alm_reserva')
                    ->select(DB::raw('SUM(stock_comprometido) AS suma_stock'))
                    ->where('id_detalle_requerimiento', $det->id_detalle_requerimiento)
                    ->whereNull('id_guia_com_det')
                    ->whereNull('id_trans_detalle')
                    ->whereNull('id_transformado')
                    ->whereNull('id_materia')
                    ->first();

                if ($dreq !== null) {
                    if ($dreq->cantidad <= (floatval($ant_oc->suma_cantidad) + floatval($stock !== null ? $stock->suma_stock : 0))) {
                        DB::table('almacen.alm_det_req')
                            ->where('id_detalle_requerimiento', $det->id_detalle_requerimiento)
                            ->update([
                                'estado' => 28, //en almacen total
                                // 'stock_comprometido' => floatval($det->stock_comprometido) + floatval($cantidad),
                                // 'id_almacen_reserva' => $request->id_almacen
                            ]);
                    } else {
                        DB::table('almacen.alm_det_req')
                            ->where('id_detalle_requerimiento', $det->id_detalle_requerimiento)
                            ->update([
                                'estado' => 27, //en almacen parcial
                                // 'stock_comprometido' => floatval($det->stock_comprometido) + floatval($cantidad),
                                // 'id_almacen_reserva' => $request->id_almacen
                            ]);
                    }
                    if (intval($dreq->id_tipo_requerimiento) !== 4) {
                        DB::table('almacen.alm_reserva')
                            ->insert([
                                'codigo' => Reserva::crearCodigo($id_almacen),
                                'id_producto' => $det->id_producto,
                                'stock_comprometido' => $cantidad,
                                'id_almacen_reserva' => $id_almacen,
                                'id_detalle_requerimiento' => $det->id_detalle_requerimiento,
                                'id_guia_com_det' => $id_guia_com_det,
                                'estado' => 1,
                                'usuario_registro' => $id_usuario,
                                'fecha_registro' => date('Y-m-d H:i:s'),
                            ]);
                    } else {
                        if (intval($id_almacen) !== intval($dreq->id_almacen)) {
                            if (intval($dreq->id_tipo_requerimiento) !== 4) {
                            DB::table('almacen.alm_reserva')
                                ->insert([
                                    'codigo' => Reserva::crearCodigo($id_almacen),
                                    'id_producto' => $det->id_producto,
                                    'stock_comprometido' => $cantidad,
                                    'id_almacen_reserva' => $id_almacen,
                                    'id_detalle_requerimiento' => $det->id_detalle_requerimiento,
                                    'id_guia_com_det' => $id_guia_com_det,
                                    'estado' => 1,
                                    'usuario_registro' => $id_usuario,
                                    'fecha_registro' => date('Y-m-d H:i:s'),
                                ]);
                            }
                        }
                    }
                }
            }
        } else {
            DB::table('logistica.log_det_ord_compra')
                ->where('id_detalle_orden', $det->id_detalle_orden)
                ->update(['estado' => 27]); //en almacen parcial

            if ($det->id_detalle_requerimiento !== null) {

                DB::table('almacen.alm_det_req')
                    ->where('id_detalle_requerimiento', $det->id_detalle_requerimiento)
                    ->update([
                        'estado' => 27, //en almacen parcial
                        // 'stock_comprometido' => floatval($det->stock_comprometido) + floatval($cantidad),
                        // 'id_almacen_reserva' => $request->id_almacen
                    ]);

                if ($dreq->id_tipo_requerimiento !== 4) {
                    DB::table('almacen.alm_reserva')
                        ->insert([
                            'codigo' => Reserva::crearCodigo($id_almacen),
                            'id_producto' => $det->id_producto,
                            'stock_comprometido' => $cantidad,
                            'id_almacen_reserva' => $id_almacen,
                            'id_detalle_requerimiento' => $det->id_detalle_requerimiento,
                            'id_guia_com_det' => $id_guia_com_det,
                            'estado' => 1,
                            'usuario_registro' => $id_usuario,
                            'fecha_registro' => date('Y-m-d H:i:s'),
                        ]);
                }
            }
        }
    }

    public function pruebaR()
    {
        $stock = DB::table('almacen.alm_reserva')
            ->select(DB::raw('SUM(stock_comprometido) AS suma_stock'))
            ->where('id_detalle_requerimiento', 3184)
            ->whereNull('id_guia_com_det')
            ->whereNull('id_trans_detalle')
            ->whereNull('id_transformado')
            ->whereNull('id_materia')
            ->first();
        return response()->json($stock !== null ? $stock->suma_stock : 0);
    }
    public function reservaNextCodigo($id_almacen)
    {
        $yyyy = date('Y', strtotime(date('Y-m-d H:i:s')));
        $anio = date('y', strtotime(date('Y-m-d H:i:s')));

        $cantidad = DB::table('almacen.alm_reserva')
            ->where('id_almacen_reserva', $id_almacen)
            ->whereYear('fecha_registro', '=', $yyyy)
            ->get()->count();

        $val = GenericoAlmacenController::leftZero(4, ($cantidad + 1));
        $nextId = "RE-" . $id_almacen . "-" . $anio . $val;

        return $nextId;
    }
    public function transferencia($id_guia_com)
    {

        try {
            DB::beginTransaction();

            $guia = DB::table('almacen.guia_com')->where('id_guia', $id_guia_com)->first();

            $guia_detalle = DB::table('almacen.guia_com_det')
                ->select(
                    'guia_com_det.*',
                    'alm_req.id_sede',
                    'alm_req.id_requerimiento',
                    'alm_req.id_almacen as id_almacen_destino',
                    'alm_det_req.id_detalle_requerimiento',
                    'req_od.id_sede as id_sede_od',
                    'req_od.id_requerimiento as id_requerimiento_od',
                    'req_od.id_almacen as id_almacen_destino_od',
                    'req_det_od.id_detalle_requerimiento as id_detalle_requerimiento_od'
                )
                ->leftJoin('logistica.log_det_ord_compra', function ($join) {
                    $join->on('log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det');
                    $join->where('log_det_ord_compra.estado', '!=', 7);
                })
                ->leftJoin('almacen.alm_det_req', function ($join) {
                    $join->on('alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento');
                    $join->where('alm_det_req.estado', '!=', 7);
                })
                ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
                ->leftJoin('almacen.transfor_transformado', function ($join) {
                    $join->on('transfor_transformado.id_transformado', '=', 'guia_com_det.id_transformado');
                    $join->where('transfor_transformado.estado', '!=', 7);
                })
                ->leftJoin('almacen.orden_despacho_det', function ($join) {
                    $join->on('orden_despacho_det.id_od_detalle', '=', 'transfor_transformado.id_od_detalle');
                    $join->where('orden_despacho_det.estado', '!=', 7);
                })
                ->leftJoin('almacen.alm_det_req as req_det_od', function ($join) {
                    $join->on('req_det_od.id_detalle_requerimiento', '=', 'orden_despacho_det.id_detalle_requerimiento');
                    $join->where('req_det_od.estado', '!=', 7);
                })
                ->leftjoin('almacen.alm_req as req_od', 'req_od.id_requerimiento', '=', 'req_det_od.id_requerimiento')
                ->where('id_guia_com', $id_guia_com)
                ->get();

            $msj = null;
            $msj = $this->generarTransferencias($guia->id_almacen, $guia_detalle);

            DB::commit();
            return response()->json($msj);
        } catch (\PDOException $e) {
            DB::rollBack($e);
        }
    }

    public function generarTransferencias($id_almacen_origen, $detalle)
    {

        $array_padres = [];
        $array_items = [];

        $sede = DB::table('almacen.alm_almacen')
            ->select('id_sede')
            ->where('id_almacen', $id_almacen_origen)->first();

        foreach ($detalle as $det) {
            //sede de requerimiento !== sede de la guia
            $sede_det = ($det->id_sede !== null ? $det->id_sede : $det->id_sede_od);

            if (
                $sede_det !== null &&
                $sede_det !== $sede->id_sede
            ) {

                $searchedValue = ($det->id_requerimiento !== null ? $det->id_requerimiento : $det->id_requerimiento_od);
                $existe = false;

                if (count($array_padres) > 0) {
                    foreach ($array_padres as $padre) {
                        if ($padre['id_requerimiento'] == $searchedValue) {
                            $existe = true;
                            break;
                        }
                    }
                }
                if ($existe == false) {
                    $nuevo = [
                        'id_requerimiento' => $searchedValue,
                        'id_almacen_destino' => ($det->id_almacen_destino !== null ? $det->id_almacen_destino : $det->id_almacen_destino_od)
                    ];

                    array_push($array_padres, $nuevo);
                }
                array_push($array_items, $det);
            }
        }

        $id_usuario = Auth::user()->id_usuario;
        $fecha = date('Y-m-d H:i:s');
        $msj = '';
        $id_trans = null;

        foreach ($array_padres as $padre) {

            $codigo = TransferenciaController::transferencia_nextId($id_almacen_origen, new Carbon());

            if ($msj == '') {
                $msj = 'Se generó transferencia. ' . $codigo;
            } else {
                $msj .= ', ' . $codigo;
            }

            $id_trans = DB::table('almacen.trans')->insertGetId(
                [
                    'id_almacen_origen' => $id_almacen_origen,
                    'id_almacen_destino' => $padre['id_almacen_destino'],
                    'codigo' => $codigo,
                    'id_requerimiento' => $padre['id_requerimiento'],
                    'id_guia_ven' => null,
                    'responsable_origen' => null,
                    'responsable_destino' => null,
                    'fecha_transferencia' => date('Y-m-d'),
                    'registrado_por' => $id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                'id_transferencia'
            );

            foreach ($array_items as $item) {

                $id_detalle_requerimiento = ($item->id_detalle_requerimiento !== null ? $item->id_detalle_requerimiento : $item->id_detalle_requerimiento_od);
                $id_almacen_destino = ($item->id_almacen_destino !== null ? $item->id_almacen_destino : $item->id_almacen_destino_od);

                if (
                    $id_detalle_requerimiento !== null &&
                    $id_almacen_destino == $padre['id_almacen_destino']
                ) {

                    $id_trans_detalle = DB::table('almacen.trans_detalle')->insertGetId(
                        [
                            'id_transferencia' => $id_trans,
                            'id_producto' => $item->id_producto,
                            'cantidad' => $item->cantidad,
                            'estado' => 1,
                            'fecha_registro' => $fecha,
                            'id_requerimiento_detalle' => $id_detalle_requerimiento,
                            'id_guia_com_det' => $item->id_guia_com_det
                        ],
                        'id_trans_detalle'
                    );
                    //envia la reserva
                    DB::table('almacen.alm_reserva')
                        ->where('id_guia_com_det', $item->id_guia_com_det)
                        ->update([
                            'estado' => 17,
                            'id_trans_detalle' => $id_trans_detalle
                        ]);
                }
            }
        }
        return $msj;
    }

    public function prue($padre)
    {
        $count_todo = DB::table('almacen.alm_det_req')
            ->where([
                ['id_requerimiento', '=', $padre],
                ['estado', '!=', 7]
            ])
            ->where('tiene_transformacion', null)
            ->orWhere('tiene_transformacion', false)
            ->count();
        return $count_todo;
    }
    public static function actualiza_prod_ubi($id_producto, $id_almacen)
    {
        //Actualizo los saldos del producto
        //Obtengo el registro de saldos
        $ubi = DB::table('almacen.alm_prod_ubi')
            ->where([
                ['id_producto', '=', $id_producto],
                ['id_almacen', '=', $id_almacen]
            ])
            ->first();
        //Traer stockActual
        $saldo = GenericoAlmacenController::saldo_actual_almacen($id_producto, $id_almacen);
        $valor = GenericoAlmacenController::valorizacion_almacen($id_producto, $id_almacen);
        $cprom = ($saldo > 0 ? $valor / $saldo : 0);
        //guardo saldos actualizados
        if ($ubi !== null) {
            DB::table('almacen.alm_prod_ubi')
                ->where('id_prod_ubi', $ubi->id_prod_ubi)
                ->update([
                    'stock' => $saldo,
                    'valorizacion' => $valor,
                    'costo_promedio' => $cprom
                ]);
        } else { //si no existe -> creo la ubicacion
            DB::table('almacen.alm_prod_ubi')->insert([
                'id_producto' => $id_producto,
                'id_almacen' => $id_almacen,
                'stock' => $saldo,
                'valorizacion' => $valor,
                'costo_promedio' => $cprom,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public static function validaProdUbi($id_producto, $id_almacen)
    {
        //Obtengo el registro de saldos
        $ubi = DB::table('almacen.alm_prod_ubi')
            ->where([
                ['id_producto', '=', $id_producto],
                ['id_almacen', '=', $id_almacen]
            ])
            ->first();
        //si no existe -> creo la ubicacion
        if ($ubi == null) {
            DB::table('almacen.alm_prod_ubi')->insert([
                'id_producto' => $id_producto,
                'id_almacen' => $id_almacen,
                'stock' => 0,
                'valorizacion' => 0,
                'costo_promedio' => 0,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function anular_ingreso(Request $request)
    {
        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $msj = '';
            $tipo = '';

            $ing = DB::table('almacen.mov_alm')
                ->select('mov_alm.*', 'guia_com.serie', 'guia_com.numero')
                ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'mov_alm.id_guia_com')
                ->where('id_mov_alm', $request->id_mov_alm)
                ->first();

            //si el ingreso no esta revisado
            if ($ing->revisado == 0) {
                $prorrateos_count = 0;

                if ($ing->id_guia_com !== null) {
                    //Revisa si existen prorrateos
                    $prorrateos_count = DB::table('almacen.guia_com_prorrateo_det')
                        ->join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'guia_com_prorrateo_det.id_guia_com_det')
                        ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
                        ->where([
                            ['guia_com.id_guia', '=', $ing->id_guia_com],
                            ['guia_com_prorrateo_det.estado', '!=', 7]
                        ])
                        ->get()->count();
                }

                if ($prorrateos_count == 0) {
                    //Verifica si ya tiene transferencia u orden de despacho
                    $detalle = DB::table('almacen.mov_alm_det')
                        ->select(
                            'mov_alm_det.id_guia_com_det',
                            'mov_alm_det.id_producto',
                            'log_det_ord_compra.id_detalle_orden',
                            'log_det_ord_compra.id_orden_compra',
                            'alm_det_req.id_detalle_requerimiento',
                            'alm_det_req.id_requerimiento',
                            'trans_detalle.id_trans_detalle',
                            'trans.id_transferencia',
                            'trans.estado as estado_trans',
                            'guia_ven.id_guia_ven',
                            'guia_com_det.id_transformado'
                        )
                        ->leftjoin('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'mov_alm_det.id_guia_com_det')
                        ->leftjoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det')
                        ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
                        ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
                        ->leftJoin('almacen.orden_despacho', function ($join) {
                            $join->on('orden_despacho.id_requerimiento','=','alm_req.id_requerimiento');
                            $join->where('orden_despacho.estado', '!=', 7);
                        })
                        ->leftJoin('almacen.guia_ven', function ($join) {
                            $join->on('guia_ven.id_od','=','orden_despacho.id_od');
                            $join->where('guia_ven.estado', '!=', 7);
                        })
                        ->leftJoin('almacen.trans_detalle', function ($join) {
                            $join->on('trans_detalle.id_requerimiento_detalle', '=', 'alm_det_req.id_detalle_requerimiento');
                            $join->where('trans_detalle.estado', '!=', 7);
                        })
                        ->leftjoin('almacen.trans', 'trans.id_transferencia', '=', 'trans_detalle.id_transferencia')
                        ->where([['mov_alm_det.id_mov_alm', '=', $request->id_mov_alm], ['mov_alm_det.estado', '!=', 7]])
                        ->get();

                    if (count($detalle) > 0) {

                        $validado = true;
                        foreach ($detalle as $det) {
                            if (($det->id_trans_detalle !== null && ($det->estado_trans == 17 || $det->estado_trans == 14))) {    //recepcionada y enviada
                                $validado = false;
                                $msj = 'El ingreso ya fue procesado con una Transferencia.';
                            }
                            else if ($det->id_guia_ven !== null) {    //salida almacen
                                $validado = false;
                                $msj = 'El ingreso ya fue procesado con una Guia de Salida.';
                            }
                        }

                        if ($validado) {
                            //Anula ingreso
                            DB::table('almacen.mov_alm')
                                ->where('id_mov_alm', $request->id_mov_alm)
                                ->update([
                                    'estado' => 7,
                                    'fecha_anulacion' => new Carbon(),
                                    'usuario_anulacion' => $id_usuario,
                                    'comentario_anulacion' => $request->observacion,
                                    'id_motivo_anulacion' => $request->id_motivo_obs,
                                ]);
                            //Anula el detalle
                            DB::table('almacen.mov_alm_det')
                                ->where('id_mov_alm', $request->id_mov_alm)
                                ->update(['estado' => 7]);

                            if ($request->id_guia_com !== null) {
                                //Agrega motivo anulacion a la guia
                                DB::table('almacen.guia_com_obs')->insert(
                                    [
                                        'id_guia_com' => $request->id_guia_com,
                                        'observacion' => $request->observacion,
                                        'registrado_por' => $id_usuario,
                                        'id_motivo_anu' => $request->id_motivo_obs,
                                        'fecha_registro' => date('Y-m-d H:i:s')
                                    ]
                                );
                                //Anula la Guia
                                DB::table('almacen.guia_com')
                                    ->where('id_guia', $request->id_guia_com)
                                    ->update(['estado' => 7]);
                                //Anula la Guia Detalle
                                DB::table('almacen.guia_com_det')
                                    ->where('id_guia_com', $request->id_guia_com)
                                    ->update(['estado' => 7]);
                            }

                            if ($ing->id_transformacion !== null) {
                                DB::table('almacen.transformacion')
                                    ->where('id_transformacion', $ing->id_transformacion)
                                    ->update(['estado' => 10]); //finalizado
                            }

                            if ($ing->id_devolucion !== null) {
                                DB::table('cas.devolucion')
                                    ->where('id_devolucion', $ing->id_devolucion)
                                    ->update(['estado' => 2]); //revisado
                            }

                            $requerimientos = [];

                            foreach ($detalle as $det) {
                                //Actualiza stocks
                                OrdenesPendientesController::actualiza_prod_ubi($det->id_producto, $ing->id_almacen);
                                //Anula las series relacionadas
                                DB::table('almacen.alm_prod_serie')
                                    ->where([
                                        ['id_guia_com_det', '=', $det->id_guia_com_det],
                                        ['id_prod', '=', $det->id_producto]
                                    ])
                                    ->update(['estado' => 7]);

                                //Anula la reserva
                                if ($det->id_transformado !== null) {
                                    DB::table('almacen.alm_reserva')
                                        ->where('id_transformado', $det->id_transformado)
                                        ->update(['estado' => 7]);
                                } else {
                                    DB::table('almacen.alm_reserva')
                                        ->where('id_guia_com_det', $det->id_guia_com_det)
                                        ->update(['estado' => 7]);
                                }

                                if ($det->id_detalle_orden !== null) {
                                    //Quita estado de la orden
                                    DB::table('logistica.log_det_ord_compra')
                                        ->where('id_detalle_orden', $det->id_detalle_orden)
                                        ->update(['estado' => 1]);
                                    //Quita estado de la orden
                                    DB::table('logistica.log_ord_compra')
                                        ->where('id_orden_compra', $det->id_orden_compra)
                                        ->update([
                                            'en_almacen' => false,
                                            'estado' => 1
                                        ]);
                                }

                                DB::table('almacen.alm_det_req')
                                    ->where('id_detalle_requerimiento', $det->id_detalle_requerimiento)
                                    ->update(['estado' => 5]); //Atendido

                                if (!in_array($det->id_requerimiento, $requerimientos)) {
                                    //agrega id_requerimiento
                                    array_push($requerimientos, $det->id_requerimiento);
                                    //Requerimiento regresa a atendido
                                    DB::table('almacen.alm_req')
                                        ->where('id_requerimiento', $det->id_requerimiento)
                                        ->update(['estado' => 5]); //Atendido
                                }
                                //Anula transferencia
                                if ($det->id_trans_detalle !== null) {

                                    DB::table('almacen.trans_detalle')
                                        ->where('id_trans_detalle', $det->id_trans_detalle)
                                        ->update(['estado' => 7]); //Anulado

                                    DB::table('almacen.trans')
                                        ->where('id_transferencia', $det->id_transferencia)
                                        ->update(['estado' => 7]); //Anulado
                                }
                            }

                            foreach ($requerimientos as $id_requerimiento) {
                                //Agrega accion en requerimiento
                                DB::table('almacen.alm_req_obs')
                                    ->insert([
                                        'id_requerimiento' => $id_requerimiento,
                                        'accion' => 'INGRESO ANULADO',
                                        'descripcion' => 'Ingreso por Compra con Guía ' . $ing->serie . '-' . $ing->numero . ' e ' . $ing->codigo . ' fue Anulado. Requerimiento regresa a Atendido.',
                                        'id_usuario' => $id_usuario,
                                        'fecha_registro' => date('Y-m-d H:i:s')
                                    ]);
                            }
                            $msj = 'Se anuló el ingreso correctamente.';
                            $tipo = 'success';
                        } else {
                            //$msj = 'El ingreso ya fue procesado con una Guia de Salida o una Transferencia.';
                            $tipo = 'warning';
                        }
                    } else {
                        //Anula ingreso
                        DB::table('almacen.mov_alm')
                            ->where('id_mov_alm', $request->id_mov_alm)
                            ->update([
                                'estado' => 7,
                                'fecha_anulacion' => new Carbon(),
                                'usuario_anulacion' => $id_usuario,
                                'comentario_anulacion' => $request->observacion,
                                'id_motivo_anulacion' => $request->id_motivo_obs,
                            ]);
                        //Anula el detalle
                        DB::table('almacen.mov_alm_det')
                            ->where('id_mov_alm', $request->id_mov_alm)
                            ->update(['estado' => 7]);

                        //Anula la Guia
                        DB::table('almacen.guia_com')
                            ->where('id_guia', $request->id_guia_com)
                            ->update(['estado' => 7]);
                        //Anula la Guia Detalle
                        DB::table('almacen.guia_com_det')
                            ->where('id_guia_com', $request->id_guia_com)
                            ->update(['estado' => 7]);

                        if ($ing->id_transformacion !== null) {
                            DB::table('almacen.transformacion')
                                ->where('id_transformacion', $ing->id_transformacion)
                                ->update(['estado' => 10]); //finalizado
                        }

                        if ($ing->id_devolucion !== null) {
                            DB::table('cas.devolucion')
                                ->where('id_devolucion', $ing->id_devolucion)
                                ->update(['estado' => 2]); //revisado
                        }

                        $msj = 'Se anuló correctamente. No se encontró items de dicho ingreso ' . count($detalle);
                        $tipo = 'success';
                    }
                } else {
                    $msj = 'No es posible anular. El ingreso fue prorrateado.';
                    $tipo = 'warning';
                }
            } else {
                $msj = 'El ingreso ya fue revisado por el Jefe de Almacén';
                $tipo = 'warning';
            }
            DB::commit();
            return response()->json([
                'tipo' => $tipo,
                'mensaje' => $msj,
                'nroOrdenesPendientes' => $this->nroOrdenesPendientes(),
                'nroDevolucionesPendientes' => $this->nroDevolucionesPendientes(),
                'nroTransformacionesPendientes' => $this->nroTransformacionesPendientes(), 200
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al anular el ingreso. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    function obtenerGuia($id)
    {
        $guia = DB::table('almacen.guia_com')
            ->select(
                'guia_com.id_guia',
                'guia_com.id_proveedor',
                'adm_contri.razon_social',
                'guia_com.serie',
                'guia_com.numero',
                'guia_com.id_almacen'
            )
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'guia_com.id_proveedor')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->where('guia_com.id_guia', $id)
            ->first();

        $detalle = DB::table('almacen.guia_com_det')
            ->select(
                'guia_com_det.*',
                'log_ord_compra.codigo as cod_orden',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.part_number',
                'alm_prod.id_moneda as id_moneda_producto',
                'alm_und_medida.abreviatura',
                'log_det_ord_compra.precio',
                'log_ord_compra.id_condicion',
                'log_ord_compra.plazo_dias',
                'log_ord_compra.id_sede',
                'log_ord_compra.id_moneda',
                'sis_moneda.simbolo',
                'log_ord_compra.id_cta_principal'
            )
            ->leftjoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det')
            ->leftjoin('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            ->leftjoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_com_det.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where('guia_com_det.id_guia_com', $id)
            ->orderBy('guia_com_det.id_guia_com_det')
            ->get();

        $igv = DB::table('contabilidad.cont_impuesto')
            ->where('codigo', 'IGV')->first();

        return response()->json(['guia' => $guia, 'detalle' => $detalle, 'igv' => $igv->porcentaje]);
    }

    function obtenerGuiaSeleccionadas(Request $request)
    {
        $ingresos = json_decode($request->id_ingresos_seleccionados);

        $detalle = DB::table('almacen.guia_com_det')
            ->select(
                'guia_com_det.*',
                'log_ord_compra.codigo as cod_orden',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.part_number',
                'alm_prod.id_moneda as id_moneda_producto',
                'alm_und_medida.abreviatura',
                'log_det_ord_compra.precio',
                'log_ord_compra.id_condicion',
                'log_ord_compra.plazo_dias',
                'log_ord_compra.id_sede',
                'guia_com.serie',
                'guia_com.numero',
                'guia_com.id_almacen',
                'log_ord_compra.id_moneda',
                'sis_moneda.simbolo',
                'log_ord_compra.id_cta_principal'
            )
            ->leftjoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det')
            ->leftjoin('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            ->leftjoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_com_det.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->whereIn('guia_com_det.id_guia_com', $ingresos)
            ->orderBy('guia_com_det.id_guia_com_det')
            ->get();

        $igv = DB::table('contabilidad.cont_impuesto')
            ->where('codigo', 'IGV')->first();

        return response()->json(['detalle' => $detalle, 'igv' => $igv->porcentaje]);
    }

    public static function tipo_cambio_compra($fecha)
    {
        $data = DB::table('contabilidad.cont_tp_cambio')
            ->where('cont_tp_cambio.fecha', '<=', $fecha)
            ->orderBy('fecha', 'desc')
            ->first();
        return $data->compra;
    }

    public static function tipo_cambio_promedio($fecha, $moneda)
    {
        $data = DB::table('contabilidad.cont_tp_cambio')
            ->where([
                ['cont_tp_cambio.fecha', '<=', $fecha],
                ['cont_tp_cambio.moneda', '=', $moneda]
            ])
            ->orderBy('fecha', 'desc')
            ->first();
        return ($data !== null ? $data->promedio : 0);
    }

    public function guardar_doc_compra(Request $request)
    {
        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $fecha = date('Y-m-d H:i:s');
            $tc = TipoCambio::where([['moneda', '=', 2], ['fecha', '<=', $request->fecha_emision_doc]])
                ->orderBy('fecha', 'DESC')->first();

            $id_condicion_softlink = '';

            if ($request->id_condicion == 1) {
                $id_condicion_softlink = '02';
            } else if ($request->id_condicion == 2) {
                switch ($request->credito_dias) {
                    case 60:
                        $id_condicion_softlink = '03';
                        break;
                    case 20:
                        $id_condicion_softlink = '23';
                        break;
                    case 30:
                        $id_condicion_softlink = '01';
                        break;
                    case 45:
                        $id_condicion_softlink = '22';
                        break;
                    case 15:
                        $id_condicion_softlink = '06';
                        break;
                    case 7:
                        $id_condicion_softlink = '05';
                        break;
                    case 3:
                        $id_condicion_softlink = '14';
                        break;
                    case 40:
                        $id_condicion_softlink = '25';
                        break;
                    case 35:
                        $id_condicion_softlink = '24';
                        break;
                    default:
                        break;
                }
            }

            $id_doc = DB::table('almacen.doc_com')->insertGetId(
                [
                    'serie' => strtoupper($request->serie_doc),
                    'numero' => $request->numero_doc,
                    'id_tp_doc' => $request->id_tp_doc,
                    'id_proveedor' => $request->id_proveedor,
                    'fecha_emision' => $request->fecha_emision_doc,
                    'fecha_vcmto' => $request->fecha_emision_doc,
                    'id_condicion' => $request->id_condicion,
                    'credito_dias' => $request->credito_dias,
                    'id_sede' => $request->id_sede,
                    'moneda' => $request->moneda,
                    'tipo_cambio' => $tc->venta,
                    'sub_total' => $request->sub_total,
                    'id_condicion_softlink' => $id_condicion_softlink,
                    'total_igv' => $request->igv,
                    'total_icbper' => 0,
                    'porcen_igv' => $request->porcentaje_igv,
                    'total_a_pagar' => round($request->total, 2),
                    'usuario' => $id_usuario,
                    'registrado_por' => $id_usuario,
                    'id_cta_bancaria' => $request->id_cta_principal,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                'id_doc_com'
            );

            $items = json_decode($request->detalle_items);
            $suma_total = 0;
            $suma_servicio = 0;

            foreach ($items as $item) {
                DB::table('almacen.doc_com_det')
                    ->insert([
                        'id_doc' => $id_doc,
                        'id_guia_com_det' => ($item->id_producto == null ? null : $item->id_guia_com_det),
                        'id_item' => $item->id_producto,
                        'servicio_descripcion' => strtoupper($item->descripcion),
                        'cantidad' => $item->cantidad,
                        'id_unid_med' => $item->id_unid_med,
                        'precio_unitario' => $item->precio,
                        'sub_total' => $item->sub_total,
                        'porcen_dscto' => $item->porcentaje_dscto,
                        'total_dscto' => $item->total_dscto,
                        'precio_total' => $item->total,
                        'id_oc_det' => $item->id_oc_det,
                        'estado' => 1,
                        'fecha_registro' => $fecha,
                    ]);

                // if ($request->moneda == 2) {
                //     $valor = $tc * $item->total;
                // } else {
                //     $valor = $item->total;
                // }

                if ($item->id_producto !== null) {
                    $suma_total += floatval($item->total);
                } else {
                    $suma_servicio += floatval($item->total);
                }
            }

            // if ($suma_servicio != null && $suma_servicio > 0) {

            $factor = floatval(($suma_servicio !== '' && $suma_servicio !== null) ? $suma_servicio : 0) / ($suma_total > 0 ? $suma_total : 1);

            foreach ($items as $item) {

                if ($item->id_producto !== null) {

                    $adicional = floatval($item->total) * $factor;
                    $nuevo_total = floatval($item->total) + $adicional;

                    //conversion de moneda
                    if ($item->id_moneda_producto == $request->moneda) {
                        $valor = $nuevo_total;
                    } else {
                        if ($item->id_moneda_producto == 1) { //soles
                            $valor = $nuevo_total * $tc->venta;
                        } else { //dolares
                            $valor = $nuevo_total / $tc->venta;
                        }
                    }

                    // if ($request->moneda == 2) {
                    //     $valor = floatval($tc * $nuevo_total);
                    // } else {
                    //     $valor = floatval($nuevo_total);
                    // }

                    DB::table('almacen.guia_com_det')
                        ->where('id_guia_com_det', $item->id_guia_com_det)
                        ->update([
                            'unitario_adicional' => $adicional,
                            'id_moneda' => $request->moneda
                        ]);

                    DB::table('almacen.mov_alm_det')
                        ->where('id_guia_com_det', $item->id_guia_com_det)
                        ->update(['valorizacion' => $valor]);

                    OrdenesPendientesController::actualiza_prod_ubi($item->id_producto, $request->id_almacen_doc);
                }
            }
            // }
            DB::commit();
            return response()->json(['id_doc' => $id_doc]);
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
        }
    }

    public function anular_doc_com($id_doc)
    {
        $update = DB::table('almacen.doc_com')
            ->where('doc_com.id_doc_com', $id_doc)
            ->update(['estado' => 7]);

        $update = DB::table('almacen.doc_com_det')
            ->where('doc_com_det.id_doc', $id_doc)
            ->update(['estado' => 7]);

        return response()->json($update);
    }

    public function documentos_ver($id_doc)
    {
        $docs = DB::table('almacen.doc_com')
            ->select(
                'doc_com.id_doc_com',
                'doc_com.serie',
                'doc_com.numero',
                'doc_com.fecha_emision',
                'cont_tp_doc.descripcion as tp_doc',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'sis_moneda.simbolo',
                'doc_com.total_a_pagar',
                'doc_com.sub_total',
                'doc_com.total_igv',
                'doc_com.total_icbper',
                'log_cdn_pago.descripcion as condicion_descripcion',
                'sis_sede.descripcion as sede_descripcion',
                'doc_com.credito_dias',
                'doc_com.tipo_cambio'
            )
            // ->join('almacen.guia_com_det','guia_com_det.id_guia_com','=','guia_com.id_guia')
            // ->join('almacen.doc_com_det','doc_com_det.id_guia_com_det','=','guia_com_det.id_guia_com_det')
            // ->join('almacen.doc_com','doc_com.id_doc_com','=','doc_com_det.id_doc')
            ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'doc_com.id_proveedor')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->join('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_com.id_tp_doc')
            ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_com.moneda')
            ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_com.id_condicion')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'doc_com.id_sede')
            ->where('doc_com.id_doc_com', $id_doc)
            ->distinct()
            ->get();

        $detalles = DB::table('almacen.doc_com_det')
            ->select(
                'doc_com_det.*',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.part_number',
                'alm_und_medida.abreviatura',
                'guia_com.serie',
                'guia_com.numero'
            )
            ->leftjoin('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'doc_com_det.id_guia_com_det')
            ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'doc_com_det.id_item')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'doc_com_det.id_unid_med')
            ->where('doc_com_det.id_doc', $id_doc)
            ->get();

        return response()->json(['docs' => $docs, 'detalles' => $detalles]);
    }

    public function cambio_serie_numero(Request $request)
    {

        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $msj = '';

            $ing = DB::table('almacen.mov_alm')
                ->where('id_mov_alm', $request->id_ingreso)
                ->first();
            //si la ingreso no esta revisada
            if ($ing->revisado == 0) {
                //si existe una orden
                if ($ing->id_guia_com !== null) {
                    //Anula la Guia
                    $update = DB::table('almacen.guia_com')
                        ->where('id_guia', $ing->id_guia_com)
                        ->update([
                            'serie' => $request->serie_nuevo,
                            'numero' => $request->numero_nuevo
                        ]);
                    //Agrega motivo anulacion a la guia
                    DB::table('almacen.guia_com_obs')->insert(
                        [
                            'id_guia_com' => $ing->id_guia_com,
                            'observacion' => 'Se cambió la serie-número de la Guía Compra a ' . $request->serie_nuevo . '-' . $request->numero_nuevo,
                            'registrado_por' => $id_usuario,
                            'id_motivo_anu' => $request->id_motivo_obs_cambio,
                            'fecha_registro' => date('Y-m-d H:i:s')
                        ]
                    );
                } else {
                    $msj = 'No existe una orden de despacho enlazada';
                }
            } else {
                $msj = 'El ingreso ya fue revisada por el Jefe de Almacén';
            }
            DB::commit();
            return response()->json($msj);
        } catch (\PDOException $e) {

            DB::rollBack();
        }
    }


    public function get_ingreso($id)
    {
        $ingreso = DB::table('almacen.mov_alm')
            ->select(
                'mov_alm.*',
                'alm_almacen.descripcion as des_almacen',
                DB::raw("(tp_doc_almacen.abreviatura) || '-' || (guia_com.serie) || '-' || (guia_com.numero) as guia"),
                DB::raw("(cont_tp_doc.abreviatura) || '-' || (doc_com.serie) || '-' || (doc_com.numero) as doc"),
                'doc_com.fecha_emision as doc_fecha_emision',
                'tp_doc_almacen.descripcion as tp_doc_descripcion',
                'guia_com.fecha_emision as fecha_guia',
                'sis_usua.usuario as nom_usuario',
                'adm_contri.razon_social',
                'adm_contri.direccion_fiscal',
                'adm_contri.nro_documento',
                'tp_ope.cod_sunat',
                'tp_ope.descripcion as ope_descripcion',
                'empresa.razon_social as empresa_razon_social',
                'empresa.nro_documento as ruc_empresa',
                'doc_com.tipo_cambio',
                'sis_moneda.descripcion as des_moneda',
                'sis_usua.nombre_corto as persona',
                'transformacion.codigo as cod_transformacion',
                'transformacion.fecha_transformacion', //'transformacion.serie','transformacion.numero',
                'trans.codigo as trans_codigo',
                'alm_origen.descripcion as trans_almacen_origen'
            )
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('contabilidad.adm_contri as empresa', 'empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftjoin('almacen.transformacion', 'transformacion.id_transformacion', '=', 'mov_alm.id_transformacion')
            ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'mov_alm.id_guia_com')
            ->leftjoin('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_com.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'mov_alm.id_operacion')
            ->leftjoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'mov_alm.id_doc_com')
            ->leftjoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_com.id_tp_doc')
            ->leftjoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_com.moneda')
            ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'guia_com.id_proveedor')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftjoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'mov_alm.usuario')
            ->leftjoin('almacen.trans', 'trans.id_guia_com', '=', 'mov_alm.id_guia_com')
            ->leftjoin('almacen.alm_almacen as alm_origen', 'alm_origen.id_almacen', '=', 'trans.id_almacen_origen')
            ->where('mov_alm.id_mov_alm', $id)
            ->first();

        $detalle = DB::table('almacen.mov_alm_det')
            ->select(
                'mov_alm_det.*',
                'alm_prod.codigo',
                'alm_prod.part_number',
                'alm_prod.descripcion',
                'alm_und_medida.abreviatura',
                'sis_moneda.simbolo',
                'log_det_ord_compra.subtotal',
                'log_det_ord_compra.precio as unitario',
                'guia_com_det.unitario_adicional',
                'alm_prod.series',
                'log_ord_compra.codigo as codigo_oc'
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftjoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'alm_prod.id_moneda')
            ->leftjoin('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'mov_alm_det.id_guia_com_det')
            ->leftjoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det')
            ->leftjoin('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            ->where([['mov_alm_det.id_mov_alm', '=', $id], ['mov_alm_det.estado', '=', 1]])
            ->get();

        $ocs = [];
        if ($ingreso !== null) {
            foreach ($detalle as $det) {
                if (!in_array($det->codigo_oc, $ocs)) {
                    array_push($ocs, $det->codigo_oc);
                }
            }
        }
        return ['ingreso' => $ingreso, 'detalle' => $detalle, 'ocs' => $ocs];
    }

    public function imprimir_ingreso($id_ing)
    {

        $id = GenericoAlmacenController::decode5t($id_ing);
        $result = $this->get_ingreso($id);
        $ingreso = $result['ingreso'];
        $detalle = $result['detalle'];
        $ocs = $result['ocs'];

        $cod_ocs = '';
        foreach ($ocs as $oc) {
            if ($cod_ocs == '') {
                $cod_ocs .= $oc;
            } else {
                $cod_ocs .= ', ' . $oc;
            }
        }
        $fecha_actual = date('Y-m-d');
        $hora_actual = date('H:i:s');

        $html = '
        <html>
            <head>
                <style type="text/css">
                *{
                    font-family: "DejaVu Sans";
                }
                table{
                    width:100%;
                    font-size:12px;
                }
                #detalle thead{
                    padding: 4px;
                    background-color: #e5e5e5;
                }
                #detalle tbody tr td{
                    font-size:11px;
                    padding: 4px;
                }
                .right{
                    text-align: right;
                }
                .sup{
                    vertical-align:top;
                }
                </style>
            </head>
            <body>
                <table width="100%">
                    <tr>
                        <td>
                            <p style="text-align:left;font-size:10px;margin:0px;">' . $ingreso->ruc_empresa . '</p>
                            <p style="text-align:left;font-size:10px;margin:0px;">' . $ingreso->empresa_razon_social . '</p>
                            <p style="text-align:left;font-size:10px;margin:0px;"><strong>' . strtoupper(config('global.nombreSistema')) . ' '  . config('global.version') . '</strong></p>
                        </td>
                    </tr>
                </table>
                <h3 style="margin:0px; padding:0px;"><center>INGRESO A ALMACÉN</center></h3>
                <h5><center>' . $ingreso->des_almacen . '</center></h5>

                <table border="0">
                    <tr>
                        <td width=100px>Ingreso N°</td>
                        <td width=10px>:</td>
                        <td width=250px>' . $ingreso->codigo . '</td>
                        <td>Fecha Ingreso</td>
                        <td width=10px>:</td>
                        <td>' . (new Carbon($ingreso->fecha_emision))->format('d-m-Y') . '</td>
                    </tr>
                ';
        if ($ingreso->guia !== null) {
            $html .= '
                    <tr>
                        <td width=100px>Guía N°</td>
                        <td width=10px>:</td>
                        <td>' . $ingreso->guia . '</td>
                        <td>Fecha Guía</td>
                        <td width=10px>:</td>
                        <td>' . (new Carbon($ingreso->fecha_guia))->format('d-m-Y') . '</td>
                    </tr>';
        }
        if ($ingreso->doc !== null) {
            $html .= '<tr>
                        <td width=110px>Documento</td>
                        <td width=10px>:</td>
                        <td>' . $ingreso->doc . '</td>
                        <td width=120px>Fecha Documento</td>
                        <td width=10px>:</td>
                        <td>' . (new Carbon($ingreso->doc_fecha_emision))->format('d-m-Y') . '</td>
                    </tr>';
        }
        if ($ingreso->cod_transformacion !== null) {
            $html .= '<tr>
                        <td width=110px>Transformación</td>
                        <td width=10px>:</td>
                        <td width=300px>' . $ingreso->cod_transformacion . '</td>
                        <td width=150px>Fecha Transformación</td>
                        <td width=10px>:</td>
                        <td>' . (new Carbon($ingreso->fecha_transformacion))->format('d-m-Y') . '</td>
                    </tr>';
        }
        if ($ingreso->trans_codigo !== null) {
            $html .= '<tr>
                        <td width=110px>Transferencia</td>
                        <td width=10px>:</td>
                        <td width=180px>' . $ingreso->trans_codigo . '</td>
                        <td width=100px>Almacén Origen</td>
                        <td width=10px>:</td>
                        <td width=180px>' . $ingreso->trans_almacen_origen . '</td>
                    </tr>';
        }
        $html .= '
                    <tr>
                        <td>Proveedor</td>
                        <td>:</td>
                    ';
        if ($cod_ocs !== '') {
            $html .= '
                            <td>' . $ingreso->nro_documento . ' - ' . $ingreso->razon_social . '</td>
                            <td>Nro. OC</td>
                            <td>:</td>
                            <td>' . $cod_ocs . '</td>
                        ';
        } else {
            $html .= '<td colSpan="3">' . $ingreso->nro_documento . ' - ' . $ingreso->razon_social . '</td>
                        ';
        }
        $html .= '
                    </tr>
                    <tr>
                        <td width=100px>Tipo Movim.</td>
                        <td>:</td>
                        <td colSpan="4">' . $ingreso->cod_sunat . ' ' . $ingreso->ope_descripcion . '</td>
                    </tr>
                    <tr>
                        <td>Responsable</td>
                        <td>:</td>
                        <td>' . $ingreso->persona . '</td>
                    </tr>
                </table>
                <br/>
                <table id="detalle">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Código</th>
                            <th>PartNumber</th>
                            <th width=50% >Descripción</th>
                            <th>Cant.</th>
                            <th>Und.</th>
                            <th>Unitario</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>';
        $i = 1;

        foreach ($detalle as $det) {
            $series = '';
            $det_series = DB::table('almacen.alm_prod_serie')
                ->where([
                    ['alm_prod_serie.id_prod', '=', $det->id_producto],
                    ['alm_prod_serie.id_guia_com_det', '=', $det->id_guia_com_det]
                ])
                ->get();

            if (isset($det_series)) {
                foreach ($det_series as $s) {
                    if ($series !== '') {
                        $series .= ', ' . $s->serie;
                    } else {
                        $series = '<br>Serie(s): ' . $s->serie;
                    }
                }
            }
            $html .= '
                        <tr>
                            <td class="right">' . $i . '</td>
                            <td>' . $det->codigo . '</td>
                            <td>' . $det->part_number . '</td>
                            <td>' . $det->descripcion . ' <strong>' . $series . '</strong></td>
                            <td class="right">' . $det->cantidad . '</td>
                            <td>' . $det->abreviatura . '</td>
                            <td>' . round(($det->valorizacion / $det->cantidad), 4, PHP_ROUND_HALF_UP) . '</td>
                            <td class="right">' . round($det->valorizacion, 4, PHP_ROUND_HALF_UP) . '</td>
                        </tr>';
            $i++;
        }
        $html .= '</tbody>
                </table>
                <p style="text-align:right;font-size:11px;">Elaborado por: ' . $ingreso->nom_usuario . ' ' . (new Carbon($ingreso->fecha_registro))->format('d-m-Y H:i') . '</p>

            </body>
        </html>';

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->stream();
        return $pdf->download('ingreso.pdf');
    }

    public function actualizarIngreso(Request $request)
    {
        try {
            DB::beginTransaction();
            $msj = '';

            $ing = DB::table('almacen.guia_com')
            ->select('guia_com.fecha_almacen', 'guia_com.id_almacen')
            ->where('id_guia', $request->id_guia_com)
            ->first();

            $periodo_estado = CierreAperturaController::consultarPeriodo($request->ingreso_fecha_emision, $ing->id_almacen);

            if (intval($periodo_estado) == 2){
                $msj = 'El periodo esta cerrado. Consulte con contabilidad.';
            } else {

                $fecha_anterior = $ing->fecha_almacen;
                $id_usuario = Auth::user()->id_usuario;

                DB::table('almacen.guia_com')->where('id_guia', $request->id_guia_com)
                    ->update(
                        [
                            'serie' => $request->ingreso_serie,
                            'numero' => $request->ingreso_numero,
                            'comentario' => $request->ingreso_comentario,
                            'fecha_emision' => $request->ingreso_fecha_emision,
                            'fecha_almacen' => $request->ingreso_fecha_almacen,
                        ]
                    );

                //Agrega motivo anulacion a la guia
                DB::table('almacen.guia_com_obs')->insert(
                    [
                        'id_guia_com' => $request->id_guia_com,
                        'observacion' => $request->observacion,
                        'registrado_por' => $id_usuario,
                        'id_motivo_anu' => $request->id_motivo_cambio,
                        'fecha_registro' => date('Y-m-d H:i:s')
                    ]
                );

                $productos_en_negativo = '';

                if ($ing->fecha_almacen !== $request->ingreso_fecha_almacen) {
                    DB::table('almacen.mov_alm')
                        ->where([
                            ['id_guia_com', '=', $request->id_guia_com],
                            ['estado', '!=', 7]
                        ])
                        ->update(['fecha_emision' => $request->ingreso_fecha_almacen]);

                    //Validacion por cambio de fecha
                    if ($request->ingreso_fecha_almacen > $ing->fecha_almacen) {
                        $productos = DB::table('almacen.guia_com_det')
                            ->select('guia_com_det.id_producto', 'alm_prod.descripcion')
                            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_com_det.id_producto')
                            ->where([
                                ['guia_com_det.id_guia_com', '=', $request->id_guia_com],
                                ['guia_com_det.estado', '!=', 7]
                            ])
                            ->get();
                        $anio = (new Carbon($request->ingreso_fecha_almacen))->format('Y');

                        foreach ($productos as $prod) {
                            $alerta_negativo = ValidaMovimientosController::validaNegativosHistoricoKardex(
                                $prod->id_producto,
                                $ing->id_almacen,
                                $anio
                            );

                            if ($alerta_negativo > 0) {
                                $productos_en_negativo .= $prod->descripcion . ' Genera ' . $alerta_negativo . ' movimiento(s) negativo(s).<br>';
                            }
                        }
                    }
                }

                if ($productos_en_negativo !== '') {
                    // DB::beginTransaction();

                    DB::table('almacen.mov_alm')
                        ->where([
                            ['id_guia_com', '=', $request->id_guia_com],
                            ['estado', '!=', 7]
                        ])
                        ->update(['fecha_emision' => $fecha_anterior]);

                    DB::table('almacen.guia_com')->where('id_guia', $request->id_guia_com)
                        ->update(['fecha_almacen' => $fecha_anterior]);

                    // DB::commit();
                    $msj = 'No es posible realizar el cambio de fecha de ingreso porque genera negativos en el histórico del kardex.<br>' . $productos_en_negativo;
                } else {

                    $msj = 'ok';
                }
            }
            DB::commit();
            return response()->json($msj);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json('Algo salió mal. Inténtelo nuevamente.');
        }
    }
}
