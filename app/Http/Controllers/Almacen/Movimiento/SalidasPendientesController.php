<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use App\Http\Controllers\AlmacenController as GenericoAlmacenController;
use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalidasPendientesController extends Controller
{
    function view_despachosPendientes()
    {
        $tp_operacion = AlmacenController::tp_operacion_cbo_sal();
        $clasificaciones = AlmacenController::mostrar_guia_clas_cbo();
        $usuarios = AlmacenController::select_usuarios();
        $motivos_anu = AlmacenController::select_motivo_anu();
        return view('almacen/guias/despachosPendientes', compact('tp_operacion', 'clasificaciones', 'usuarios', 'motivos_anu'));
    }

    public function listarOrdenesDespachoPendientes()
    {
        $data = DB::table('almacen.orden_despacho')
            ->select(
                'orden_despacho.*',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'alm_req.codigo as codigo_req',
                'alm_req.concepto',
                'sis_usua.nombre_corto',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
                'alm_almacen.descripcion as almacen_descripcion'
            )
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
            ->leftjoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_req.id_almacen')
            ->leftjoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->leftjoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'alm_req.id_persona')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'orden_despacho.registrado_por')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'orden_despacho.estado')
            ->where('orden_despacho.estado', 1)
            ->get();
        $output['data'] = $data;
        return response()->json($output);
        // return datatables($data)->toJson();
    }

    public function guardar_guia_despacho(Request $request)
    {
        try {
            DB::beginTransaction();
            $id_salida = null;

            if ($request->id_od !== null) {

                $id_tp_doc_almacen = 2; //Guia Venta
                $id_usuario = Auth::user()->id_usuario;
                $fecha_registro = date('Y-m-d H:i:s');

                $od = DB::table('almacen.orden_despacho')
                    ->where('id_od', $request->id_od)
                    ->first();

                if ($od !== null) {
                    $id_guia_ven = DB::table('almacen.guia_ven')->insertGetId(
                        [
                            'id_tp_doc_almacen' => $id_tp_doc_almacen,
                            'id_od' => $request->id_od,
                            'serie' => $request->serie,
                            'numero' => $request->numero,
                            'id_sede' => $request->id_sede,
                            'id_cliente' => $request->id_cliente,
                            'id_persona' => $request->id_persona,
                            'fecha_emision' => $request->fecha_emision,
                            'fecha_almacen' => $request->fecha_emision,
                            'id_almacen' => $request->id_almacen,
                            'id_operacion' => $request->id_operacion,
                            'usuario' => $id_usuario,
                            'registrado_por' => $id_usuario,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro,
                        ],
                        'id_guia_ven'
                    );

                    //Genero la salida
                    $codigo = AlmacenController::nextMovimiento(
                        2, //salida
                        $request->fecha_emision,
                        $request->id_almacen
                    );

                    $transformacion = DB::table('almacen.transformacion')
                        ->select('id_transformacion')
                        ->where('id_od', $request->id_od)
                        ->first();

                    if ($transformacion !== null) {
                        DB::table('almacen.transformacion')
                            ->where('id_transformacion', $transformacion->id_transformacion)
                            ->update([
                                'estado' => 21,
                                'fecha_entrega' => date('Y-m-d H:i:s')
                            ]); //Entregado
                    }

                    $id_salida = DB::table('almacen.mov_alm')->insertGetId(
                        [
                            'id_almacen' => $request->id_almacen,
                            'id_tp_mov' => 2, //Salidas
                            'codigo' => $codigo,
                            'fecha_emision' => $request->fecha_emision,
                            'id_guia_ven' => $id_guia_ven,
                            'id_operacion' => $request->id_operacion,
                            'id_transformacion' => ($transformacion !== null ? $transformacion->id_transformacion : null),
                            'revisado' => 0,
                            'usuario' => $id_usuario,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro,
                        ],
                        'id_mov_alm'
                    );
                    //orden de despacho estado   procesado
                    $est = ($request->id_operacion == 27 ? 22 : 9);
                    $aplica_cambios = ($request->id_operacion == 27 ? true : false);
                    $count_est = 0;
                    $detalle = json_decode($request->detalle); //No fucniona el json_decode
                    // dd($detalle);
                    // exit();
                    // return  response()->json($detalles);

                    foreach ($detalle as $det) {
                        //guardo los items de la guia ven
                        $id_guia_ven_det = DB::table('almacen.guia_ven_det')->insertGetId(
                            [
                                'id_guia_ven' => $id_guia_ven,
                                'id_producto' => $det->id_producto,
                                'id_od_det' => $det->id_od_detalle,
                                'cantidad' => $det->cantidad,
                                'id_unid_med' => $det->id_unidad_medida,
                                'estado' => 1,
                                'fecha_registro' => $fecha_registro
                            ],
                            'id_guia_ven_det'
                        );

                        DB::table('almacen.doc_ven_det')
                            ->where('id_detalle_requerimiento', $det->id_detalle_requerimiento)
                            ->update(['id_guia_ven_det' => $id_guia_ven_det]);

                        if (count($det->series) > 0) {

                            foreach ($det->series as $s) {
                                DB::table('almacen.alm_prod_serie')
                                    ->where('id_prod_serie', $s->id_prod_serie)
                                    ->update(['id_guia_ven_det' => $id_guia_ven_det]);
                            }
                        }
                        //obtener costo promedio
                        $saldos_ubi = DB::table('almacen.alm_prod_ubi')
                            ->where([
                                ['id_producto', '=', $det->id_producto],
                                ['id_almacen', '=', $request->id_almacen]
                            ])
                            ->first();
                        //Guardo los items de la salida
                        DB::table('almacen.mov_alm_det')->insertGetId(
                            [
                                'id_mov_alm' => $id_salida,
                                'id_producto' => $det->id_producto,
                                // 'id_posicion' => $det->id_posicion,
                                'cantidad' => $det->cantidad,
                                'valorizacion' => ($saldos_ubi !== null ? ($saldos_ubi->costo_promedio * $det->cantidad) : 0),
                                'usuario' => $id_usuario,
                                'id_guia_ven_det' => $id_guia_ven_det,
                                'estado' => 1,
                                'fecha_registro' => $fecha_registro,
                            ],
                            'id_mov_alm_det'
                        );

                        //Actualizo los saldos del producto
                        //Obtengo el registro de saldos
                        $ubi = DB::table('almacen.alm_prod_ubi')
                            ->where([
                                ['id_producto', '=', $det->id_producto],
                                ['id_almacen', '=', $request->id_almacen]
                            ])
                            ->first();
                        //Traer stockActual
                        $saldo = AlmacenController::saldo_actual_almacen($det->id_producto, $request->id_almacen);
                        $valor = AlmacenController::valorizacion_almacen($det->id_producto, $request->id_almacen);
                        $cprom = ($saldo > 0 ? $valor / $saldo : 0);
                        //guardo saldos actualizados
                        if ($ubi !== null) { //si no existe -> creo la ubicacion
                            DB::table('almacen.alm_prod_ubi')
                                ->where('id_prod_ubi', $ubi->id_prod_ubi)
                                ->update([
                                    'stock' => $saldo,
                                    'valorizacion' => $valor,
                                    'costo_promedio' => $cprom
                                ]);
                        } else {
                            DB::table('almacen.alm_prod_ubi')->insert([
                                'id_producto' => $det->id_producto,
                                'id_almacen' => $request->id_almacen,
                                'stock' => $saldo,
                                'valorizacion' => $valor,
                                'costo_promedio' => $cprom,
                                'estado' => 1,
                                'fecha_registro' => $fecha_registro
                            ]);
                        }

                        $detreq = DB::table('almacen.alm_det_req')
                            ->where('id_detalle_requerimiento', $det->id_detalle_requerimiento)
                            ->first();

                        $detdes = DB::table('almacen.orden_despacho_det')
                            ->select(DB::raw('SUM(cantidad) as suma_cantidad'))
                            ->join('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'orden_despacho_det.id_od')
                            ->where([
                                ['orden_despacho_det.id_detalle_requerimiento', '=', $det->id_detalle_requerimiento],
                                ['orden_despacho.estado', '!=', 7],
                                ['orden_despacho.aplica_cambios', '=', $aplica_cambios]
                            ])
                            ->first();

                        //orden de despacho detalle estado   procesado
                        if ($detdes->suma_cantidad >= $detreq->cantidad) {
                            DB::table('almacen.alm_det_req')
                                ->where('id_detalle_requerimiento', $det->id_detalle_requerimiento)
                                ->update(['estado' => $est]);
                        }
                    }

                    DB::table('almacen.orden_despacho')
                        ->where('id_od', $request->id_od)
                        ->update(['estado' => $est]);
                    //orden de despacho detalle estado   procesado
                    DB::table('almacen.orden_despacho_det')
                        ->where('id_od', $request->id_od)
                        ->update(['estado' => $est]);

                    $requerimiento = DB::table('almacen.alm_req')
                        ->where('id_requerimiento', $request->id_requerimiento)
                        ->first();
                    //requerimiento despachado
                    if ($requerimiento->tiene_transformacion) {

                        if ($aplica_cambios) {

                            $todo = DB::table('almacen.alm_det_req')
                                ->where([
                                    ['id_requerimiento', '=', $request->id_requerimiento],
                                    ['tiene_transformacion', '=', false],
                                    ['estado', '!=', 7]
                                ])
                                ->count();
                        } else {
                            $todo = DB::table('almacen.alm_det_req')
                                ->where([
                                    ['id_requerimiento', '=', $request->id_requerimiento],
                                    ['tiene_transformacion', '=', true],
                                    ['estado', '!=', 7]
                                ])
                                ->count();
                        }
                    } else {
                        $todo = DB::table('almacen.alm_det_req')
                            ->where([
                                ['id_requerimiento', '=', $request->id_requerimiento],
                                ['tiene_transformacion', '=', false],
                                ['estado', '!=', 7]
                            ])
                            ->count();
                    }
                    $desp = DB::table('almacen.alm_det_req')
                        ->where([
                            ['id_requerimiento', '=', $request->id_requerimiento],
                            ['estado', '=', $est]
                        ])
                        ->count();

                    if ($desp == $todo) {
                        DB::table('almacen.alm_req')
                            ->where('id_requerimiento', $request->id_requerimiento)
                            ->update(['estado' => $est]);
                    }
                    //Agrega accion en requerimiento
                    DB::table('almacen.alm_req_obs')
                        ->insert([
                            'id_requerimiento' => $request->id_requerimiento,
                            'accion' => 'SALIDA DE ALMACÉN',
                            'descripcion' => 'Se generó la Salida del Almacén con Guía ' . $request->serie . '-' . $request->numero,
                            'id_usuario' => $id_usuario,
                            'fecha_registro' => date('Y-m-d H:i:s')
                        ]);
                }
            }

            DB::commit();
            return response()->json($id_salida);
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            // return response()->json($e);
        }
    }

    public function listarSalidasDespacho(Request $request)
    {
        $data = DB::table('almacen.mov_alm')
            ->select(
                'mov_alm.*',
                'guia_ven.serie',
                'guia_ven.numero',
                'guia_ven.id_od',
                'orden_despacho.codigo as codigo_od',
                DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
                'alm_req.codigo as codigo_requerimiento',
                'alm_req.concepto',
                'adm_contri.razon_social',
                'req_trans.codigo as codigo_req_trans',
                'req_trans.concepto as concepto_trans',
                'alm_almacen.descripcion as almacen_descripcion',
                'sis_usua.nombre_corto',
                'tp_ope.descripcion as operacion',
                'orden_despacho.aplica_cambios',
                'trans.codigo as codigo_trans'
            )
            ->join('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'mov_alm.id_guia_ven')
            ->leftjoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'guia_ven.id_cliente')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->leftjoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'guia_ven.id_persona')
            ->leftjoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'guia_ven.id_almacen')
            ->leftjoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'guia_ven.usuario')
            ->leftjoin('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'guia_ven.id_od')
            ->leftjoin('almacen.trans', 'trans.id_guia_ven', '=', 'guia_ven.id_guia_ven')
            ->leftjoin('almacen.alm_req as req_trans', 'req_trans.id_requerimiento', '=', 'trans.id_requerimiento')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
            ->join('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'mov_alm.id_operacion')
            ->where([['mov_alm.estado', '!=', '7']]);
        // ->get();
        // return response()->json($data);
        return datatables($data)->toJson();
    }


    public function anular_salida(Request $request)
    {

        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $msj = '';

            $sal = DB::table('almacen.mov_alm')
                ->where('id_mov_alm', $request->id_salida)
                ->first();
            //si la salida no esta revisada
            if ($sal->revisado == 0) {
                //si existe una orden
                if ($request->id_od !== null) {
                    //Verifica si ya fue despachado
                    $od = DB::table('almacen.orden_despacho')
                        ->select('orden_despacho.*', 'adm_estado_doc.estado_doc')
                        ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'orden_despacho.estado')
                        ->where('id_od', $request->id_od)
                        ->first();
                    //si la orden de despacho es Procesado
                    if ($od->estado == 9 || $od->estado == 23) {
                        //Anula salida
                        $update = DB::table('almacen.mov_alm')
                            ->where('id_mov_alm', $request->id_salida)
                            ->update(['estado' => 7]);
                        //Anula el detalle
                        $update = DB::table('almacen.mov_alm_det')
                            ->where('id_mov_alm', $request->id_salida)
                            ->update(['estado' => 7]);
                        //Agrega motivo anulacion a la guia
                        DB::table('almacen.guia_ven_obs')->insert(
                            [
                                'id_guia_ven' => $request->id_guia_ven,
                                'observacion' => $request->observacion_guia_ven,
                                'registrado_por' => $id_usuario,
                                'id_motivo_anu' => $request->id_motivo_obs_ven,
                                'fecha_registro' => date('Y-m-d H:i:s')
                            ]
                        );
                        //Anula la Guia
                        $update = DB::table('almacen.guia_ven')
                            ->where('id_guia_ven', $request->id_guia_ven)
                            ->update(['estado' => 7]);
                        //Anula la Guia Detalle
                        $update = DB::table('almacen.guia_ven_det')
                            ->where('id_guia_ven', $request->id_guia_ven)
                            ->update(['estado' => 7]);
                        //Quita estado de la orden
                        DB::table('almacen.orden_despacho')
                            ->where('id_od', $request->id_od)
                            ->update(['estado' => 1]);

                        if ($od->id_requerimiento !== null) {
                            //Requerimiento regresa a por despachar
                            DB::table('almacen.alm_req')
                                ->where('id_requerimiento', $od->id_requerimiento)
                                ->update(['estado' => 29]); //por despachar

                            DB::table('almacen.alm_det_req')
                                ->where('id_requerimiento', $od->id_requerimiento)
                                ->update(['estado' => 29]); //por despachar
                            //Agrega accion en requerimiento
                            DB::table('almacen.alm_req_obs')
                                ->insert([
                                    'id_requerimiento' => $od->id_requerimiento,
                                    'accion' => 'SALIDA ANULADA',
                                    'descripcion' => 'Requerimiento regresa a Reservado',
                                    'id_usuario' => $id_usuario,
                                    'fecha_registro' => date('Y-m-d H:i:s')
                                ]);
                        }
                    } else {
                        $msj = 'La Orden de Despacho ya está con ' . $od->estado_doc;
                    }
                } else {
                    $msj = 'No existe una orden de despacho enlazada';
                }
            } else {
                $msj = 'La salida ya fue revisada por el Jefe de Almacén';
            }
            DB::commit();
            return response()->json($msj);
        } catch (\PDOException $e) {

            DB::rollBack();
        }
    }


    public function cambio_serie_numero(Request $request)
    {

        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $msj = '';

            $sal = DB::table('almacen.mov_alm')
                ->where('id_mov_alm', $request->id_salida)
                ->first();
            //si la salida no esta revisada
            if ($sal->revisado == 0) {
                //si existe una orden
                if ($request->id_od !== null) {
                    //Anula la Guia
                    $update = DB::table('almacen.guia_ven')
                        ->where('id_guia_ven', $request->id_guia_ven)
                        ->update([
                            'serie' => $request->serie_nuevo,
                            'numero' => $request->numero_nuevo
                        ]);
                    //Agrega motivo anulacion a la guia
                    DB::table('almacen.guia_ven_obs')->insert(
                        [
                            'id_guia_ven' => $request->id_guia_ven,
                            'observacion' => 'Se cambió la serie-número de la Guía Venta a ' . $request->serie_nuevo . '-' . $request->numero_nuevo,
                            'registrado_por' => $id_usuario,
                            'id_motivo_anu' => $request->id_motivo_obs_cambio,
                            'fecha_registro' => date('Y-m-d H:i:s')
                        ]
                    );
                } else {
                    $msj = 'No existe una orden de despacho enlazada';
                }
            } else {
                $msj = 'La salida ya fue revisada por el Jefe de Almacén';
            }
            DB::commit();
            return response()->json($msj);
        } catch (\PDOException $e) {

            DB::rollBack();
        }
    }

    public function verDetalleDespacho($id_od)
    {
        $data = DB::table('almacen.orden_despacho_det')
            ->select(
                'orden_despacho_det.*',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_und_medida.abreviatura',
                'alm_prod.part_number',
                'alm_prod.id_unidad_medida',
                'guia_oc.id_guia_com_det as id_guia_oc_det',
                'guia_trans.id_guia_ven_det as id_guia_trans_det',
                'orden_despacho.id_almacen',
                'goc.id_almacen as id_almacen_oc',
                'gtr.id_almacen as id_almacen_tr'
            )

            ->join('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'orden_despacho_det.id_od')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'orden_despacho_det.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftJoin('logistica.log_det_ord_compra', function ($join) {
                $join->on('log_det_ord_compra.id_detalle_requerimiento', '=', 'orden_despacho_det.id_detalle_requerimiento');
                $join->where('log_det_ord_compra.estado', '!=', 7);
            })
            ->leftJoin('almacen.guia_com_det as guia_oc', function ($join) {
                $join->on('guia_oc.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden');
                $join->where('guia_oc.estado', '!=', 7);
            })
            ->leftjoin('almacen.guia_com as goc', 'goc.id_guia', '=', 'guia_oc.id_guia_com')
            ->leftjoin('almacen.trans_detalle', 'trans_detalle.id_requerimiento_detalle', '=', 'orden_despacho_det.id_detalle_requerimiento')
            ->leftJoin('almacen.guia_ven_det', function ($join) {
                $join->on('guia_ven_det.id_trans_det', '=', 'trans_detalle.id_trans_detalle');
                $join->where('guia_ven_det.estado', '!=', 7);
            })
            ->leftJoin('almacen.guia_com_det as guia_trans', function ($join) {
                $join->on('guia_trans.id_guia_ven_det', '=', 'guia_ven_det.id_guia_ven_det');
                $join->where('guia_trans.estado', '!=', 7);
            })
            ->leftjoin('almacen.guia_com as gtr', 'gtr.id_guia', '=', 'guia_trans.id_guia_com')
            ->where([
                ['orden_despacho_det.id_od', '=', $id_od],
                ['orden_despacho_det.estado', '!=', 7],
                ['orden_despacho_det.transformado', '=', false]
            ])
            ->get();

        $lista = [];

        foreach ($data as $det) {

            $series = [];
            $exist = false;

            foreach ($lista as $item) {
                if ($item['id_od_detalle'] == $det->id_od_detalle) {
                    $exist = true;
                }
            }

            if (!$exist) {
                $id_guia_com_det = null;

                if (
                    $det->id_guia_oc_det !== null && $det->id_almacen_oc !== null &&
                    $det->id_almacen_oc == $det->id_almacen
                ) {
                    $id_guia_com_det = $det->id_guia_oc_det;
                    $series = DB::table('almacen.alm_prod_serie')
                        ->where('id_guia_com_det', $det->id_guia_oc_det)
                        ->get();
                } else if (
                    $det->id_guia_trans_det !== null && $det->id_almacen_tr !== null &&
                    $det->id_almacen_tr == $det->id_almacen
                ) {
                    $id_guia_com_det = $det->id_guia_trans_det;
                    $series = DB::table('almacen.alm_prod_serie')
                        ->where('id_guia_com_det', $det->id_guia_trans_det)
                        ->get();
                }

                array_push($lista, [
                    'id_od_detalle' => $det->id_od_detalle,
                    'id_detalle_requerimiento' => $det->id_detalle_requerimiento,
                    'id_guia_com_det' => $id_guia_com_det,
                    'id_producto' => $det->id_producto,
                    'id_unidad_medida' => $det->id_unidad_medida,
                    'codigo' => $det->codigo,
                    'part_number' => $det->part_number,
                    'descripcion' => $det->descripcion,
                    'cantidad' => $det->cantidad,
                    'abreviatura' => $det->abreviatura,
                    'series' => $series
                ]);
            }
        }

        return response()->json($lista);
    }

    public function imprimir_salida($id_sal)
    {
        $id = GenericoAlmacenController::decode5t($id_sal);
        $salida = DB::table('almacen.mov_alm')
            ->select(
                'mov_alm.*',
                'alm_almacen.descripcion as des_almacen',
                'sis_usua.usuario as nom_usuario',
                'tp_ope.cod_sunat',
                'tp_ope.descripcion as ope_descripcion',
                // 'proy_proyecto.descripcion as proy_descripcion','proy_proyecto.codigo as cod_proyecto',
                DB::raw("(tp_doc_almacen.abreviatura) || '-' || (guia_ven.serie) || '-' || (guia_ven.numero) as guia"),
                'trans.codigo as cod_trans',
                'alm_destino.descripcion as des_alm_destino',
                'trans.fecha_transferencia',
                DB::raw("(cont_tp_doc.abreviatura) || '-' || (doc_ven.serie) || '-' || (doc_ven.numero) as doc"),
                DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) as persona"),
                'transformacion.codigo as cod_transformacion', //'transformacion.serie','transformacion.numero',
                'transformacion.fecha_transformacion',
                'guia_ven.fecha_emision as fecha_guia',
                'adm_contri.nro_documento as ruc_empresa',
                'adm_contri.razon_social as empresa_razon_social'
            )
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'mov_alm.id_guia_ven')
            ->leftjoin('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'mov_alm.id_operacion')
            ->leftjoin('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_ven.id_tp_doc_almacen')
            // ->leftjoin('almacen.guia_motivo','guia_motivo.id_motivo','=','guia_ven.id_motivo')
            ->leftjoin('almacen.trans', 'trans.id_guia_ven', '=', 'guia_ven.id_guia_ven')
            ->leftjoin('almacen.alm_almacen as alm_destino', 'alm_destino.id_almacen', '=', 'trans.id_almacen_destino')
            ->leftjoin('almacen.doc_ven', 'doc_ven.id_doc_ven', '=', 'mov_alm.id_doc_ven')
            ->leftjoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_ven.id_tp_doc')
            ->leftjoin('almacen.transformacion', 'transformacion.id_transformacion', '=', 'mov_alm.id_transformacion')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'mov_alm.usuario')
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'sis_usua.id_trabajador')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->where('mov_alm.id_mov_alm', $id)
            ->first();

        $detalle = DB::table('almacen.mov_alm_det')
            ->select(
                'mov_alm_det.*',
                'alm_prod.codigo',
                'alm_prod.part_number',
                'alm_prod.descripcion',
                'alm_ubi_posicion.codigo as cod_posicion',
                'alm_und_medida.abreviatura',
                'alm_prod.series',
                'trans.codigo as cod_trans'
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftjoin('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'mov_alm_det.id_posicion')
            ->leftjoin('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven_det', '=', 'mov_alm_det.id_guia_ven_det')
            ->leftjoin('almacen.trans_detalle', 'trans_detalle.id_trans_detalle', '=', 'guia_ven_det.id_trans_det')
            ->leftjoin('almacen.trans', 'trans.id_transferencia', '=', 'trans_detalle.id_transferencia')
            ->where([['mov_alm_det.id_mov_alm', '=', $id], ['mov_alm_det.estado', '=', 1]])
            ->get();

        // $fecha_actual = date('Y-m-d');
        // $hora_actual = date('H:i:s');

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
                            <p style="text-align:left;font-size:10px;margin:0px;">' . $salida->ruc_empresa . '</p>
                            <p style="text-align:left;font-size:10px;margin:0px;">' . $salida->empresa_razon_social . '</p>
                            <p style="text-align:left;font-size:10px;margin:0px;"><strong>SYSTEM AGILE v1.3</strong></p>
                        </td>
                    </tr>
                </table>
                <h3 style="margin:0px;"><center>SALIDA DE ALMACÉN</center></h3>
                <h5><center>' . $salida->id_almacen . ' - ' . $salida->des_almacen . '</center></h5>
                
                <table border="0">
                    <tr>
                        <td width=120px>Salida N°</td>
                        <td width=10px>:</td>
                        <td width=280px>' . $salida->codigo . '</td>
                        <td>Fecha Salida</td>
                        <td width=10px>:</td>
                        <td>' . $salida->fecha_emision . '</td>
                    </tr>';

        if ($salida->guia !== null) {
            $html .= '<tr>
                                <td>Guía de Venta</td>
                                <td width=10px>:</td>
                                <td>' . $salida->guia . '</td>
                                <td>Fecha Guía</td>
                                <td width=10px>:</td>
                                <td>' . $salida->fecha_guia . '</td>
                            </tr>';
        }
        if ($salida->fecha_transformacion !== null) {
            $html .= '<tr>
                                <td>Transformación</td>
                                <td>:</td>
                                <td width=250px>' . $salida->cod_transformacion . '</td>
                                <td width=150px>Fecha Transformación</td>
                                <td width=10px>:</td>
                                <td>' . $salida->fecha_transformacion . '</td>
                            </tr>';
        }
        if ($salida->doc !== null) {
            $html .= '<tr>
                                <td>Documento de Venta</td>
                                <td>:</td>
                                <td>' . $salida->doc . '</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>';
        }
        if (isset($salida->cod_trans)) {
            $html .= '<tr>
                                <td width=130px>Transferencia</td>
                                <td>:</td>
                                <td>' . $salida->cod_trans . '</td>
                                <td>Fecha Transferencia</td>
                                <td>:</td>
                                <td>' . $salida->fecha_transferencia . '</td>
                            </tr>
                            <tr>
                                <td>Almacén Destino</td>
                                <td>:</td>
                                <td width=200px>' . $salida->des_alm_destino . '</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>';
        }

        $html .= '<tr>
                            <td>Tipo Movimiento</td>
                            <td>:</td>
                            <td colSpan="4">' . $salida->cod_sunat . ' ' . $salida->ope_descripcion . '</td>
                        </tr>';

        $html .= '<tr>
                            <td>Generado por</td>
                            <td>:</td>
                            <td colSpan="4">' . $salida->persona . '</td>
                        </tr>
                    </table>
                    <br/>
                    <table id="detalle">
                        <thead>
                            <tr>
                                <th>Nro</th>
                                <th>Código</th>
                                <th>PartNumber</th>
                                <th width=45% >Descripción</th>
                                <th>Cant.</th>
                                <th>Unid.</th>
                                <th>Valor.</th>
                            </tr>
                        </thead>
                        <tbody>';
        $i = 1;

        foreach ($detalle as $det) {
            $series = '';

            $det_series = DB::table('almacen.alm_prod_serie')
                ->where([
                    ['alm_prod_serie.id_prod', '=', $det->id_producto],
                    ['alm_prod_serie.id_guia_ven_det', '=', $det->id_guia_ven_det]
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
            $html .= '<tr>
                                    <td class="right">' . $i . '</td>
                                    <td>' . $det->codigo . '</td>
                                    <td>' . $det->part_number . '</td>
                                    <td>' . $det->descripcion . ' <strong>' . $series . '</strong></td>
                                    <td class="right">' . $det->cantidad . '</td>
                                    <td>' . $det->abreviatura . '</td>
                                    <td class="right">' . round($det->valorizacion, 2, PHP_ROUND_HALF_UP) . '</td>
                                </tr>';
            $i++;
        }
        $html .= '</tbody>
                </table>
                <p style="text-align:right;font-size:11px;">Elaborado por: ' . $salida->nom_usuario . ' ' . $salida->fecha_registro . '</p>

            </body>
        </html>';

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        return $pdf->stream();
        return $pdf->download('salida.pdf');
        // return response()->json(['salida'=>$salida,'detalle'=>$detalle]);
    }


    function anular_orden_despacho($id_od)
    {
        try {
            DB::beginTransaction();

            $update = DB::table('almacen.orden_despacho')
                ->where('id_od', $id_od)
                ->update(['estado' => 7]);

            $detalle = DB::table('almacen.orden_despacho_det')
                ->where('id_od', $id_od)->get();

            foreach ($detalle as $det) {

                $update = DB::table('almacen.orden_despacho_det')
                    ->where('id_od_detalle', $det->id_od_detalle)
                    ->update(['estado' => 7]);

                $detreq = DB::table('almacen.alm_det_req')
                    ->where('id_detalle_requerimiento', $det->id_detalle_requerimiento)
                    ->update(['estado' => 19]);
            }

            $od = DB::table('almacen.orden_despacho')
                ->select('orden_despacho.*', 'alm_req.id_tipo_requerimiento')
                ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
                ->where('id_od', $id_od)
                ->first();

            $count_ods = DB::table('almacen.orden_despacho')
                ->where([
                    ['id_requerimiento', '=', $od->id_requerimiento],
                    ['aplica_cambios', '=', true],
                    ['estado', '!=', 7]
                ])
                ->count();

            if ($od->aplica_cambios) {
                DB::table('almacen.transformacion')
                    ->where('id_od', $id_od)
                    ->update(['estado' => 7]);

                if ($count_ods > 0) {
                    DB::table('almacen.alm_req')
                        ->where('id_requerimiento', $od->id_requerimiento)
                        ->update(['estado' => 22]); //despacho interno
                } else {
                    DB::table('almacen.alm_req')
                        ->where('id_requerimiento', $od->id_requerimiento)
                        ->update(['estado' => 28]); //en almacen total
                }
            } else {
                if ($count_ods > 0) {
                    DB::table('almacen.alm_req')
                        ->where('id_requerimiento', $od->id_requerimiento)
                        ->update(['estado' => 10]); //transformado
                } else {
                    if ($od->id_tipo_requerimiento !== 1) {
                        DB::table('almacen.alm_req')
                            ->where('id_requerimiento', $od->id_requerimiento)
                            ->update(['estado' => 19]); //en almacen total
                    } else {
                        DB::table('almacen.alm_req')
                            ->where('id_requerimiento', $od->id_requerimiento)
                            ->update(['estado' => 28]); //en almacen total
                    }
                }
            }


            $id_usuario = Auth::user()->id_usuario;
            //Agrega accion en requerimiento
            $obs = DB::table('almacen.alm_req_obs')
                ->insertGetId(
                    [
                        'id_requerimiento' => $od->id_requerimiento,
                        'accion' => 'O.D. ANULADA',
                        'descripcion' => 'Orden de Despacho Anulado',
                        'id_usuario' => $id_usuario,
                        'fecha_registro' => date('Y-m-d H:i:s')
                    ],
                    'id_observacion'
                );

            DB::commit();
            return response()->json($obs);
        } catch (\PDOException $e) {

            DB::rollBack();
        }
    }

    public function listarSeriesGuiaVen($id_producto)
    {
        $series = DB::table('almacen.alm_prod_serie')
            ->select(
                'alm_prod_serie.*',
                DB::raw("(tp_doc_almacen.abreviatura) || '-' || (guia_com.serie) || '-' || (guia_com.numero) as guia_com")
            )
            ->join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'alm_prod_serie.id_guia_com_det')
            ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->join('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_com.id_tp_doc_almacen')
            ->where([
                ['alm_prod_serie.id_prod', '=', $id_producto],
                ['alm_prod_serie.id_guia_ven_det', '=', null],
                ['alm_prod_serie.estado', '=', 1]
            ])
            ->get();
        return response()->json($series);
    }
}
