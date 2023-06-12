<?php

namespace App\Http\Controllers\Logistica\Distribucion;

use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Almacen\DetalleRequerimiento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdenesTransformacionController extends Controller
{
    public function __construct()
    {
        // session_start();
    }

    function view_ordenes_transformacion()
    {
        return view('almacen/distribucion/ordenesTransformacion');
    }

    function view_tablero_transformaciones()
    {
        return view('almacen.customizacion.tableroTransformaciones');
    }

    public function listarRequerimientosEnProceso()
    {
        $data = DB::table('almacen.alm_reserva')
            ->select(
                'alm_req.*',
                'sis_usua.nombre_corto as responsable',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'alm_almacen.descripcion as almacen_descripcion',
                'alm_req.id_sede as sede_requerimiento',
                'sede_req.descripcion as sede_descripcion_req',
                'adm_contri.razon_social as cliente_razon_social',
                'orden_despacho.id_od',
                'orden_despacho.codigo as codigo_od',
                'orden_despacho.estado as estado_od',
                'orden_despacho.aplica_cambios',
                DB::raw("(SELECT COUNT(*) FROM almacen.orden_despacho where
                    orden_despacho.id_requerimiento = alm_req.id_requerimiento
                    and orden_despacho.aplica_cambios = true
                    and orden_despacho.estado != 7) AS count_despachos_internos"),
                DB::raw("(SELECT COUNT(*) FROM almacen.trans where
                        trans.id_requerimiento = alm_req.id_requerimiento
                        and trans.estado != 7) AS count_transferencia"),
                DB::raw("(SELECT COUNT(*) FROM almacen.trans where
                        trans.id_requerimiento = alm_req.id_requerimiento
                        and trans.estado = 14) AS count_transferencia_recibida"),
                'oc_propias_view.nro_orden',
                'oc_propias_view.codigo_oportunidad',
                'oc_propias_view.id as id_oc_propia',
                'oc_propias_view.tipo'
            )
            ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'alm_reserva.id_detalle_requerimiento')
            ->join('almacen.alm_req', function ($join) {
                $join->on('alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento');
                // $join->on('alm_req.id_almacen', '=', 'alm_reserva.id_almacen_reserva');
                $join->whereNotNull('alm_reserva.id_almacen_reserva');
            })
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.estado')
            ->leftJoin('administracion.sis_sede as sede_req', 'sede_req.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_req.id_almacen')
            ->leftJoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->leftJoin('almacen.orden_despacho', function ($join) {
                $join->on('orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento');
                $join->where('orden_despacho.aplica_cambios', '=', true);
                $join->where('orden_despacho.estado', '!=', 7);
            })
            ->where('alm_req.tiene_transformacion', true)
            ->where([
                ['alm_det_req.estado', '!=', 7],
                ['alm_reserva.estado', '!=', 7],
                ['alm_reserva.estado', '!=', 5],
                ['alm_reserva.stock_comprometido', '>', 0]
            ])
            // ->whereIn('alm_req.estado', [17, 22, 27, 28])
            // ->orWhere([['alm_req.estado', '=', 19], ['alm_req.confirmacion_pago', '=', true]])
            ->distinct();


        return datatables($data)->toJson();
    }

    public function verDetalleRequerimientoDI($id_requerimiento)
    { //agregar precios a items base  DB::table('almacen.alm_det_req')
        $detalles = DetalleRequerimiento::select(
            'alm_det_req.*',
            'adm_estado_doc.estado_doc',
            'adm_estado_doc.bootstrap_color',
            'alm_prod.descripcion as producto_descripcion',
            'alm_prod.codigo as producto_codigo',
            'alm_prod.cod_softlink',
            'alm_prod.series',
            'alm_req.id_almacen',
            'alm_und_medida.abreviatura',
            'alm_prod.part_number',
            DB::raw("(SELECT SUM(cantidad)
                        FROM almacen.orden_despacho_det AS odd
                        INNER JOIN almacen.orden_despacho AS od
                            on(odd.id_od = od.id_od)
                        WHERE odd.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento
                            and odd.estado != 7
                            and od.estado != 7
                            and od.aplica_cambios = true) AS suma_despachos_internos"),
            DB::raw("(SELECT SUM(cantidad) FROM logistica.log_det_ord_compra
                        WHERE log_det_ord_compra.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento
                          and log_det_ord_compra.estado != 7) AS cantidad_orden"),
            DB::raw("(SELECT SUM(gv.cantidad)
                        FROM almacen.guia_ven_det AS gv
                        INNER JOIN almacen.orden_despacho_det AS odd
                              on(gv.id_od_det = odd.id_od_detalle)
                        INNER JOIN almacen.orden_despacho AS od
                              on(odd.id_od = od.id_od)
                        WHERE odd.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento
                              and odd.estado != 7
                              and od.estado != 7
                              and gv.estado != 7
                              and od.aplica_cambios = false) AS cantidad_despachada"),
            DB::raw("(SELECT SUM(alm_reserva.stock_comprometido)
                        FROM almacen.alm_reserva
                        WHERE alm_reserva.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento
                        and alm_reserva.estado = 1) as stock_comprometido"),
            'almacen_reserva.descripcion as almacen_reserva_descripcion',
            // DB::raw("(SELECT SUM(guia_ven_det.cantidad)
            //         FROM almacen.guia_ven_det
            //         WHERE guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle
            //             and guia_ven_det.estado != 7) as cantidad_despachada"),
            // DB::raw("(SELECT SUM(cantidad)
            //         FROM almacen.orden_despacho_det AS odd
            //         INNER JOIN almacen.orden_despacho AS od
            //             on(odd.id_od = od.id_od)
            //         WHERE odd.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento
            //             and odd.estado != 7
            //             and od.aplica_cambios = false) AS suma_despachos_externos"),
            // DB::raw("(SELECT SUM(guia.cantidad)
            //         FROM almacen.guia_com_det AS guia
            //         INNER JOIN logistica.log_det_ord_compra AS oc
            //             on(guia.id_oc_det = oc.id_detalle_orden)
            //         INNER JOIN almacen.alm_det_req AS req
            //             on(oc.id_detalle_requerimiento = req.id_detalle_requerimiento)
            //         WHERE req.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento
            //             and guia.estado != 7
            //             and oc.estado != 7) AS suma_ingresos"),
            // 'almacen_guia.id_almacen as id_almacen_guia_com',
            // 'almacen_guia.descripcion as almacen_guia_com_descripcion',
            // 'mov_alm_det.valorizacion',
            // 'cc_am_filas.part_no_producto_transformado as cc_pn',
            // 'cc_am_filas.descripcion_producto_transformado as cc_des',
            // 'cc_am_filas.comentario_producto_transformado as cc_com',
            // 'alm_reserva.id_reserva',
            // 'alm_reserva.id_almacen_reserva'
            // 'alm_reserva.stock_comprometido'
        )
            // ->leftJoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_det_req.id_almacen_reserva')
            // ->leftJoin('mgcp_cuadro_costos.cc_am_filas', 'cc_am_filas.id', '=', 'alm_det_req.id_cc_am_filas')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_det_req.estado')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftJoin('almacen.alm_reserva', function ($join) {
                $join->on('alm_reserva.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
                $join->where('alm_reserva.estado', '=', 1);
                $join->limit(1);
            })
            ->leftJoin('almacen.alm_almacen as almacen_reserva', 'almacen_reserva.id_almacen', '=', 'alm_reserva.id_almacen_reserva')
            ->where([
                ['alm_det_req.id_requerimiento', '=', $id_requerimiento],
                ['alm_det_req.estado', '!=', 7],
            ])
            ->distinct()->get();

        return response()->json($detalles);
    }

    public function ODnextId($fecha_despacho, $id_almacen, $aplica_cambios)
    {
        $yyyy = date('Y', strtotime($fecha_despacho));
        $yy = date('y', strtotime($fecha_despacho));

        $cantidad = DB::table('almacen.orden_despacho')
            ->whereYear('fecha_despacho', '=', $yyyy)
            ->where([
                ['id_almacen', '=', $id_almacen],
                ['aplica_cambios', '=', $aplica_cambios],
                ['estado', '!=', 7]
            ])
            ->get()->count();

        $val = AlmacenController::leftZero(3, ($cantidad + 1));
        $nextId = "D" . ($aplica_cambios ? "I-" : "E-") . $id_almacen . "-" . $yy . $val;
        return $nextId;
    }

    public function transformacion_nextId($fecha_transformacion)
    {
        $yyyy = date('Y', strtotime($fecha_transformacion));
        $yy = date('y', strtotime($fecha_transformacion));

        $cantidad = DB::table('almacen.transformacion')
            ->whereYear('fecha_registro', '=', $yyyy)
            ->where([['estado', '!=', 7]])
            ->get()->count();

        $val = AlmacenController::leftZero(3, ($cantidad + 1));
        $nextId = "OT-" . $yy . $val;
        return $nextId;
    }

    public function guardarOrdenDespachoInterno(Request $request)
    {
        try {
            DB::beginTransaction();

            $codigo = $this->ODnextId(date('Y-m-d'), $request->id_almacen, true);
            $usuario = Auth::user()->id_usuario;
            $fecha_registro = date('Y-m-d H:i:s');

            $id_od = DB::table('almacen.orden_despacho')
                ->insertGetId(
                    [
                        'id_sede' => $request->id_sede,
                        'id_requerimiento' => $request->id_requerimiento,
                        'id_almacen' => $request->id_almacen,
                        'codigo' => $codigo,
                        // 'fecha_despacho' => date('Y-m-d'),
                        // 'hora_despacho' => date('H:i:s'),
                        'aplica_cambios' => true,
                        'registrado_por' => $usuario,
                        'fecha_registro' => $fecha_registro,
                        'estado' => 1,
                    ],
                    'id_od'
                );

            //Agrega accion en requerimiento
            DB::table('almacen.alm_req_obs')
                ->insert([
                    'id_requerimiento' => $request->id_requerimiento,
                    'accion' => 'DESPACHO INTERNO',
                    'descripcion' => 'Se generó la Orden de Despacho Interna ' . $codigo,
                    'id_usuario' => $usuario,
                    'fecha_registro' => $fecha_registro
                ]);

            $fecha_actual = date('Y-m-d');
            $codTrans = $this->transformacion_nextId($fecha_actual);

            $id_transformacion = DB::table('almacen.transformacion')
                ->insertGetId(
                    [
                        // 'fecha_transformacion'=>$fecha_actual,
                        // 'responsable'=>$usuario,
                        'codigo' => $codTrans,
                        'id_od' => $id_od,
                        'id_cc' => $request->id_cc,
                        'id_moneda' => 1,
                        'id_almacen' => $request->id_almacen,
                        'descripcion_sobrantes' => $request->descripcion_sobrantes,
                        'total_materias' => 0,
                        'total_directos' => 0,
                        'costo_primo' => 0,
                        'total_indirectos' => 0,
                        'total_sobrantes' => 0,
                        'costo_transformacion' => 0,
                        'registrado_por' => $usuario,
                        'conformidad' => false,
                        'tipo_cambio' => 1,
                        'fecha_registro' => $fecha_registro,
                        'estado' => 1,
                        // 'observacion'=>'SALE: '.$request->sale
                    ],
                    'id_transformacion'
                );

            $ingresa = json_decode($request->detalle_ingresa);

            foreach ($ingresa as $i) {

                $id_od_detalle = DB::table('almacen.orden_despacho_det')
                    ->insertGetId(
                        [
                            'id_od' => $id_od,
                            'id_producto' => $i->id_producto,
                            'id_detalle_requerimiento' => $i->id_detalle_requerimiento,
                            'cantidad' => $i->cantidad,
                            'transformado' => false,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro
                        ],
                        'id_od_detalle'
                    );

                $val = AlmacenController::valorizacion_almacen($i->id_producto, $request->id_almacen);

                $id_materia = DB::table('almacen.transfor_materia')
                    ->insertGetId([
                        'id_transformacion' => $id_transformacion,
                        'id_producto' => $i->id_producto,
                        'cantidad' => $i->cantidad,
                        'id_od_detalle' => $id_od_detalle,
                        'valor_unitario' => ($val / $i->cantidad),
                        'valor_total' => $val,
                        'estado' => 1,
                        'fecha_registro' => $fecha_registro
                    ], 'id_materia');

                //envia la reserva
                DB::table('almacen.alm_reserva')
                    ->where('id_reserva', $i->id_reserva)
                    ->update([
                        'estado' => 17,
                        'id_materia' => $id_materia
                    ]);

                $detreq = DB::table('almacen.alm_det_req')
                    ->where('id_detalle_requerimiento', $i->id_detalle_requerimiento)
                    ->first();

                $detdes = DB::table('almacen.orden_despacho_det')
                    ->select(DB::raw('SUM(cantidad) as suma_cantidad'))
                    ->join('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'orden_despacho_det.id_od')
                    ->where([
                        ['orden_despacho_det.id_detalle_requerimiento', '=', $i->id_detalle_requerimiento],
                        ['orden_despacho_det.estado', '!=', 7],
                        ['orden_despacho.estado', '!=', 7],
                        ['orden_despacho.aplica_cambios', '=', true]
                    ])
                    ->first();

                //orden de despacho detalle estado   procesado
                if ($detdes->suma_cantidad >= $detreq->cantidad) {
                    DB::table('almacen.alm_det_req')
                        ->where('id_detalle_requerimiento', $i->id_detalle_requerimiento)
                        ->update(['estado' => 22]); //despacho interno
                }
            }

            $todo = DB::table('almacen.alm_det_req')
                ->where([
                    ['id_requerimiento', '=', $request->id_requerimiento],
                    ['tiene_transformacion', '=', false],
                    ['estado', '!=', 7]
                ])
                ->count();

            $desp = DB::table('almacen.alm_det_req')
                ->where([
                    ['id_requerimiento', '=', $request->id_requerimiento],
                    ['estado', '=', 22]
                ]) //despacho interno
                ->count();

            if ($desp == $todo) {
                DB::table('almacen.alm_req')
                    ->where('id_requerimiento', $request->id_requerimiento)
                    ->update(['estado' => 22]); //despacho interno
            }

            $sale = json_decode($request->detalle_sale);

            foreach ($sale as $s) {
                $id_od_detalle = DB::table('almacen.orden_despacho_det')
                    ->insertGetId(
                        [
                            'id_od' => $id_od,
                            'id_producto' => $s->id_producto,
                            'id_detalle_requerimiento' => $s->id_detalle_requerimiento,
                            'cantidad' => $s->cantidad,
                            'transformado' => true,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro
                        ],
                        'id_od_detalle'
                    );

                DB::table('almacen.transfor_transformado')
                    ->insert([
                        'id_transformacion' => $id_transformacion,
                        'id_producto' => $s->id_producto,
                        'id_od_detalle' => $id_od_detalle,
                        'cantidad' => $s->cantidad,
                        'valor_unitario' => 0,
                        'valor_total' => 0,
                        'estado' => 1,
                        'fecha_registro' => $fecha_registro
                    ]);
            }

            DB::commit();
            return response()->json('Se guardo existosamente el Despacho Interno: ' . $codigo . ' y la Orden de Transformacion: ' . $codTrans);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json("Ha ocurrido un problema. Inténtelo nuevamente.");
        }
    }

    public function verDetalleInstrucciones($id_detalle_requerimiento)
    {
        $data = DB::table('almacen.alm_det_req')
            ->select('cc_am_filas.*')
            ->join('mgcp_cuadro_costos.cc_am_filas', 'cc_am_filas.id', '=', 'alm_det_req.id_cc_am_filas')
            ->where('alm_det_req.id_detalle_requerimiento', $id_detalle_requerimiento)
            ->first();

        $detalle = DB::table('mgcp_cuadro_costos.cc_fila_movimientos_transformacion')
            ->select('cc_am_filas.descripcion as ingresa', 'cc_fila_movimientos_transformacion.sale')
            ->leftjoin('mgcp_cuadro_costos.cc_am_filas', 'cc_am_filas.id', '=', 'cc_fila_movimientos_transformacion.id_fila_ingresa')
            ->where('cc_fila_movimientos_transformacion.id_fila_base', $data->id)
            ->get();

        return response()->json(['fila' => $data, 'detalle' => $detalle]);
    }
}
