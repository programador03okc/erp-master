<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use App\Exports\GuiaSalidaOKCExcel;
use App\Exports\GuiaSalidaSVSExcel;
use App\Exports\SalidasPendientesExport;
use App\Exports\SalidasProcesadasExport;
use App\Exports\SeriesGuiaVentaDetalleExport;
use App\Http\Controllers\AlmacenController as GenericoAlmacenController;
use App\Http\Controllers\Tesoreria\CierreAperturaController as CierreAperturaController;
use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Almacen\Movimiento;
use App\models\Configuracion\AccesosUsuarios;
use App\models\contabilidad\Adjuntos;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class SalidasPendientesController extends Controller
{
    function view_despachosPendientes()
    {
        $tp_operacion = AlmacenController::tp_operacion_cbo_sal();
        $clasificaciones = AlmacenController::mostrar_guia_clas_cbo();
        $usuarios = AlmacenController::select_usuarios();
        $motivos_anu = AlmacenController::select_motivo_anu();
        $nro_od_pendientes = $this->nroDespachosPendientes();
        $array_accesos = [];
        $accesos_usuario = AccesosUsuarios::where('estado', 1)->where('id_usuario', Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos, $value->id_acceso);
        }
        return view(
            'almacen/guias/despachosPendientes',
            compact('tp_operacion', 'clasificaciones', 'usuarios', 'motivos_anu', 'nro_od_pendientes', 'array_accesos')
        );
    }

    public function nroDespachosPendientes()
    {
        // $array_sedes = $this->sedesPorUsuarioArray();
        $nro_od_pendientes = DB::table('almacen.orden_despacho')
            ->where('orden_despacho.estado', 1)
            ->where('orden_despacho.flg_despacho', 0)
            ->count();
        return $nro_od_pendientes;
    }

    public function listarOrdenesDespachoPendientes(Request $request)
    {
        $data = DB::table('almacen.orden_despacho')
            ->select(
                'orden_despacho.*',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'alm_req.codigo as codigo_req',
                'alm_req.concepto',
                'alm_req.tiene_transformacion',
                'alm_req.estado as estado_requerimiento',
                'alm_req.obs_facturacion',
                // 'sis_usua.nombre_corto', (orden_despacho.aplica_cambios==true ? false : alm_req.tiene_transformacion)
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'transformacion.id_transformacion',
                'oc_propias_view.nro_orden',
                'oc_propias_view.codigo_oportunidad',
                'oc_propias_view.id as id_oc_propia',
                'oc_propias_view.tipo',
                // DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
                'alm_almacen.descripcion as almacen_descripcion',
                DB::raw("(SELECT SUM(reserva.stock_comprometido)
                        FROM almacen.alm_reserva AS reserva
                        INNER JOIN almacen.orden_despacho_det AS despacho
                            ON( despacho.id_od = orden_despacho.id_od
                            and despacho.transformado = false)
                        WHERE reserva.id_detalle_requerimiento = despacho.id_detalle_requerimiento
                            and reserva.estado != 7
                            and reserva.estado != 5
                            and reserva.id_almacen_reserva = orden_despacho.id_almacen) AS suma_reservas"),
                DB::raw("(SELECT SUM(despacho.cantidad)
                        FROM  almacen.orden_despacho_det AS despacho
                        WHERE despacho.id_od = orden_despacho.id_od
                          AND despacho.transformado = false
                          AND despacho.estado != 7) AS suma_cantidad")
            )
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->leftjoin('almacen.transformacion', 'transformacion.id_od', '=', 'orden_despacho.id_od')
            ->leftjoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'orden_despacho.id_almacen')
            ->leftjoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            // ->leftjoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'alm_req.id_persona')
            // ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'orden_despacho.registrado_por')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'orden_despacho.estado');

        if ($request->select_mostrar_pendientes == 0) {
            $data->whereIn('orden_despacho.estado', [1, 25]);
            $data->where('orden_despacho.flg_despacho', 0);
        } else if ($request->select_mostrar_pendientes == 1) {
            $data->where('orden_despacho.estado', 25);
        } else if ($request->select_mostrar_pendientes == 2) {
            // $data->where('orden_despacho.estado', 25);
            $data->whereDate('orden_despacho.fecha_despacho', (new Carbon())->format('Y-m-d'));
        }
        return datatables($data)->toJson();
    }

    public function guardarSalidaGuiaDespacho(Request $request)
    {
        try {
            DB::beginTransaction();
            $id_salida = null;
            $mensaje = '';
            $id_tp_doc_almacen = 2; //Guia Venta
            $id_usuario = Auth::user()->id_usuario;
            $fecha_registro = date('Y-m-d H:i:s');

            $periodo_estado = CierreAperturaController::consultarPeriodo($request->fecha_emision, $request->id_almacen);

            if (intval($periodo_estado) == 2){
                $mensaje = 'El periodo esta cerrado. Consulte con contabilidad.';
                $tipo = 'warning';
            } else {
                //valida el saldo de los productos
                $detalle = json_decode($request->detalle);

                foreach ($detalle as $det) {
                    $producto = DB::table('almacen.alm_prod')
                        ->select('alm_prod.descripcion', 'alm_prod.codigo')
                        ->where('id_producto', $det->id_producto)
                        ->first();

                    $stockDisponible = $this->validaStockDisponible($det->id_producto, $request->id_almacen);

                    if (floatval($stockDisponible) < floatval($det->cantidad)) {
                        $mensaje .= $producto->codigo . ' - ' . $producto->descripcion . '
                        ';
                    }
                }

                if ($mensaje == '') {

                    if ($request->id_od !== null) { //crea la guia venta
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
                                'fecha_almacen' => $request->fecha_almacen,
                                'id_almacen' => $request->id_almacen,
                                'id_operacion' => $request->id_operacion,
                                'punto_partida' => $request->punto_partida,
                                'punto_llegada' => $request->punto_llegada,
                                'comentario' => $request->comentario,
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
                            $request->fecha_emision, // $request->fecha_almacen, se cambio a solicitud del sr juan mamani 3/01/2023
                            $request->id_almacen
                        );

                        $transformacion = DB::table('almacen.transformacion')
                            ->select('id_transformacion')
                            ->where([['id_od', '=', $request->id_od], ['estado', '!=', 7]])
                            ->first();

                        $id_salida = DB::table('almacen.mov_alm')->insertGetId(
                            [
                                'id_almacen' => $request->id_almacen,
                                'id_tp_mov' => 2, //Salidas
                                'codigo' => $codigo,
                                'fecha_emision' => $request->fecha_almacen,
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

                            $suma_despacho = DB::table('almacen.guia_ven_det')
                                ->where([['id_od_det', '=', $det->id_od_detalle], ['estado', '!=', 7]])
                                ->sum('cantidad');

                            $despacho_detalle = DB::table('almacen.orden_despacho_det')
                                ->where('id_od_detalle', $det->id_od_detalle)
                                ->first();

                            if ($suma_despacho == $despacho_detalle->cantidad) {
                                DB::table('almacen.orden_despacho_det')
                                    ->where('id_od_detalle', $det->id_od_detalle)
                                    ->update(['estado' => 21]); //Entregado
                            }

                            DB::table('almacen.doc_ven_det')
                                ->where('id_detalle_requerimiento', $det->id_detalle_requerimiento)
                                ->update(['id_guia_ven_det' => $id_guia_ven_det]);

                            if (count($det->series) > 0) {

                                foreach ($det->series as $s) {
                                    if ($s->estado == 1) {
                                        DB::table('almacen.alm_prod_serie')
                                            ->where('id_prod_serie', $s->id_prod_serie)
                                            ->update(['id_guia_ven_det' => $id_guia_ven_det]);
                                    }
                                }
                            }
                            //obtener costo promedio
                            $saldos_ubi = DB::table('almacen.alm_prod_ubi')
                                ->where([
                                    ['id_producto', '=', $det->id_producto],
                                    ['id_almacen', '=', $request->id_almacen]
                                ])
                                ->first();

                            $valorizacion = ($saldos_ubi !== null ? ($saldos_ubi->costo_promedio * $det->cantidad) : 0);
                            //Guardo los items de la salida
                            DB::table('almacen.mov_alm_det')->insertGetId(
                                [
                                    'id_mov_alm' => $id_salida,
                                    'id_producto' => $det->id_producto,
                                    // 'id_posicion' => $det->id_posicion,
                                    'cantidad' => $det->cantidad,
                                    'valorizacion' => $valorizacion,
                                    'usuario' => $id_usuario,
                                    'id_guia_ven_det' => $id_guia_ven_det,
                                    'estado' => 1,
                                    'fecha_registro' => $fecha_registro,
                                ],
                                'id_mov_alm_det'
                            );

                            if ($request->id_operacion == 27) {
                                //Actualiza costos en la OT
                                if ($despacho_detalle->transformado) {
                                    DB::table('almacen.transfor_transformado')
                                        ->where('id_od_detalle', $despacho_detalle->id_od_detalle)
                                        ->update([
                                            'valor_total' => $valorizacion,
                                            'valor_unitario' => ($despacho_detalle->cantidad > 0 ? ($valorizacion / $despacho_detalle->cantidad) : 0)
                                        ]);
                                } else {
                                    DB::table('almacen.transfor_materia')
                                        ->where('id_od_detalle', $despacho_detalle->id_od_detalle)
                                        ->update([
                                            'valor_total' => $valorizacion,
                                            'valor_unitario' => ($despacho_detalle->cantidad > 0 ? ($valorizacion / $despacho_detalle->cantidad) : 0)
                                        ]);
                                }
                            }

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

                            //Cantidad atendida con otras guias
                            $atendido = DB::table('almacen.alm_reserva')
                                ->select(DB::raw('SUM(guia_ven_det.cantidad) as cantidad_atendida'))
                                ->where([
                                    ['alm_reserva.id_detalle_requerimiento', '=', $det->id_detalle_requerimiento],
                                    ['alm_reserva.id_almacen_reserva', '=', $request->id_almacen]
                                ])
                                ->join('almacen.orden_despacho_det', 'orden_despacho_det.id_detalle_requerimiento', '=', 'alm_reserva.id_detalle_requerimiento')
                                ->join('almacen.guia_ven_det', function ($join) {
                                    $join->on('guia_ven_det.id_od_det', '=', 'orden_despacho_det.id_od_detalle');
                                    $join->where('guia_ven_det.estado', '!=', 7);
                                })
                                ->first();

                            $reservas_pendientes = DB::table('almacen.alm_reserva')
                                ->where([
                                    ['alm_reserva.id_detalle_requerimiento', '=', $det->id_detalle_requerimiento],
                                    ['alm_reserva.id_almacen_reserva', '=', $request->id_almacen],
                                    ['alm_reserva.estado', '=', 1],
                                ])
                                ->get();

                            $cantidad_acumulada = 0;

                            foreach ($reservas_pendientes as $res) {
                                $cantidad_acumulada += $res->stock_comprometido;

                                if ($atendido->cantidad_atendida >= $cantidad_acumulada) {
                                    //atiende la reserva
                                    DB::table('almacen.alm_reserva')
                                        ->where('id_reserva', $res->id_reserva)
                                        ->update(['estado' => 5]);
                                }
                            }
                        }

                        if ($transformacion !== null) {
                            DB::table('almacen.transformacion')
                                ->where('id_transformacion', $transformacion->id_transformacion)
                                ->update([
                                    'estado' => 21, //Entregado
                                    'fecha_entrega' => $request->fecha_almacen,
                                    'id_almacen' => $request->id_almacen
                                ]);

                            DB::table('almacen.orden_despacho')
                                ->where('id_od', $request->id_od)
                                ->update([
                                    'estado' => 21,
                                    'id_almacen' => $request->id_almacen
                                ]); //Entregado
                        } else {
                            $count_entregados = DB::table('almacen.orden_despacho_det')
                                ->where([['id_od', '=', $request->id_od], ['estado', '=', 21]])
                                ->count();

                            $count_todos = DB::table('almacen.orden_despacho_det') //validar cantidades
                                ->where('id_od', $request->id_od)->count();

                            if ($count_entregados == $count_todos) {
                                DB::table('almacen.orden_despacho')
                                    ->where('id_od', $request->id_od)
                                    ->update(['estado' => 21]); //Entregado
                            }
                        }
                        //Envia requerimiento a facturacion
                        $req = DB::table('almacen.alm_req')
                            ->where('id_requerimiento', $request->id_requerimiento)
                            ->first();

                        if (!$req->enviar_facturacion) {
                            DB::table('almacen.alm_req')
                                ->where('id_requerimiento', $request->id_requerimiento)
                                ->update([
                                    'enviar_facturacion' => true,
                                    'fecha_facturacion' => $request->fecha_emision,
                                    'obs_facturacion' => 'Enviado automáticamente al generar la guia venta',
                                ]);
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

                        $tipo = 'success';
                        $mensaje = 'Se guardó correctamente la salida de almacén';
                    } else if ($request->id_devolucion !== null) {
                        //crea la guia venta
                        $id_guia_ven = DB::table('almacen.guia_ven')->insertGetId(
                            [
                                'id_tp_doc_almacen' => $id_tp_doc_almacen,
                                'id_devolucion' => $request->id_devolucion,
                                'serie' => $request->serie,
                                'numero' => $request->numero,
                                'id_sede' => $request->id_sede,
                                'id_cliente' => $request->id_cliente,
                                'id_persona' => $request->id_persona,
                                'fecha_emision' => $request->fecha_emision,
                                'fecha_almacen' => $request->fecha_almacen,
                                'id_almacen' => $request->id_almacen,
                                'id_operacion' => $request->id_operacion,
                                'punto_partida' => $request->punto_partida,
                                'punto_llegada' => $request->punto_llegada,
                                'comentario' => $request->comentario,
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
                            $request->fecha_almacen,
                            $request->id_almacen
                        );

                        $id_salida = DB::table('almacen.mov_alm')->insertGetId(
                            [
                                'id_almacen' => $request->id_almacen,
                                'id_tp_mov' => 2, //Salidas
                                'codigo' => $codigo,
                                'fecha_emision' => $request->fecha_almacen,
                                'id_guia_ven' => $id_guia_ven,
                                'id_operacion' => $request->id_operacion,
                                'revisado' => 0,
                                'usuario' => $id_usuario,
                                'estado' => 1,
                                'fecha_registro' => $fecha_registro,
                            ],
                            'id_mov_alm'
                        );

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

                            if (count($det->series) > 0) {

                                foreach ($det->series as $s) {
                                    if ($s->estado == 1) {
                                        DB::table('almacen.alm_prod_serie')
                                            ->where('id_prod_serie', $s->id_prod_serie)
                                            ->update(['id_guia_ven_det' => $id_guia_ven_det]);
                                    }
                                }
                            }
                            //Guardo los items de la salida
                            DB::table('almacen.mov_alm_det')->insertGetId(
                                [
                                    'id_mov_alm' => $id_salida,
                                    'id_producto' => $det->id_producto,
                                    // 'id_posicion' => $det->id_posicion,
                                    'cantidad' => $det->cantidad,
                                    'valorizacion' => 0,
                                    'usuario' => $id_usuario,
                                    'id_guia_ven_det' => $id_guia_ven_det,
                                    'estado' => 1,
                                    'fecha_registro' => $fecha_registro,
                                ],
                                'id_mov_alm_det'
                            );
                            //atiende la reserva
                            DB::table('almacen.alm_reserva')
                                ->where([
                                    ['id_detalle_devolucion', '=', $det->id_detalle_devolucion],
                                    ['estado', '!=', 7]
                                ])
                                ->update(['estado' => 5]);
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
                        }

                        DB::table('cas.devolucion')
                            ->where('id_devolucion', $request->id_devolucion)
                            ->update(['estado' => 3]);

                        $tipo = 'success';
                        $mensaje = 'Se guardó correctamente la salida de almacén';
                    }
                } else {
                    $tipo = 'warning';
                    $mensaje = 'No hay stock disponible para éstos productos:
                        ' . $mensaje;
                }
            }

            DB::commit();
            return response()->json(
                array(
                    'tipo' => $tipo,
                    'mensaje' => $mensaje,
                    'nroDespachosPendientes' => $this->nroDespachosPendientes(), 200
                )
            );
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Hubo un problema al generar la salida. Por favor intente de nuevo', 'error' => $e->getMessage()), 200);
        }
    }

    public function validaStockDisponible($id_producto, $id_almacen)
    {
        $ing = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.cantidad) as ingresos"))
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            ->where([
                ['mov_alm_det.id_producto', '=', $id_producto],
                ['mov_alm.id_almacen', '=', $id_almacen],
                ['mov_alm.id_tp_mov', '<=', 1], //ingreso o carga inicial
                ['mov_alm_det.estado', '=', 1]
            ])
            ->first();

        $sal = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.cantidad) as salidas"))
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            ->where([
                ['mov_alm_det.id_producto', '=', $id_producto],
                ['mov_alm.id_almacen', '=', $id_almacen],
                ['mov_alm.id_tp_mov', '=', 2], //salida
                ['mov_alm_det.estado', '=', 1]
            ])
            ->first();

        $stockDisponible = 0;
        if ($ing->ingresos !== null) $stockDisponible += $ing->ingresos;
        if ($sal->salidas !== null) $stockDisponible -= $sal->salidas;

        return $stockDisponible;
    }

    public function listarSalidasProcesadas()
    {
        $data = Movimiento::select(
            'mov_alm.*',
            'guia_ven.serie',
            'guia_ven.numero',
            'guia_ven.fecha_almacen',
            'guia_ven.fecha_emision as fecha_emision_guia',
            'guia_ven.id_od',
            'guia_ven.id_devolucion',
            'guia_ven.comentario',
            'guia_ven.punto_partida',
            'guia_ven.punto_llegada',
            'orden_despacho.codigo as codigo_od',
            'alm_req.id_requerimiento',
            'alm_req.codigo as codigo_requerimiento',
            'alm_req.concepto',
            'alm_req.estado as estado_requerimiento',
            'adm_contri.razon_social',
            'alm_almacen.descripcion as almacen_descripcion',
            'sis_usua.nombre_corto',
            'tp_ope.descripcion as operacion',
            'orden_despacho.aplica_cambios',
            'orden_despacho.estado as estado_od',
            'oc_propias_view.nro_orden',
            'oc_propias_view.codigo_oportunidad',
            'oc_propias_view.id as id_oc_propia',
            'oc_propias_view.tipo',
            'usua_anula.nombre_corto as usuario_anulacion_nombre',
            'devolucion.codigo as codigo_devolucion'
            // DB::raw("(SELECT ingreso.codigo FROM almacen.mov_alm as ingreso
            // where ingreso.id_transformacion = mov_alm.id_transformacion
            //   and ingreso.id_tp_mov = 1
            //   and ingreso.estado != 7) AS tiene_ingreso_transformacion")
        )
            ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'mov_alm.id_guia_ven')
            ->leftjoin('cas.devolucion', 'devolucion.id_devolucion', '=', 'guia_ven.id_devolucion')
            ->leftjoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
            ->leftjoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'mov_alm.usuario')
            ->leftjoin('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'guia_ven.id_od')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->leftjoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'guia_ven.id_cliente')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->leftjoin('configuracion.sis_usua as usua_anula', 'usua_anula.id_usuario', '=', 'mov_alm.usuario_anulacion')
            ->join('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'mov_alm.id_operacion')
            ->where([['mov_alm.id_tp_mov', '=', 2]]);
        // ->where([['mov_alm.estado', '!=', '7'], ['mov_alm.id_tp_mov', '=', 2]]);
        return $data;
    }

    public function listarSalidasDespacho(Request $request)
    {
        $data = $this->listarSalidasProcesadas();
        return datatables($data)
        ->filterColumn('codigo_od', function ($query, $keyword) {
            $keywords = trim(strtoupper($keyword));
            $query->whereRaw("devolucion.codigo LIKE ?", ["%{$keywords}%"]);
        })
        ->toJson();
    }


    public function anular_salida(Request $request)
    {
        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $msj = '';
            $tipo = '';

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
                        ->where('orden_despacho.id_od', $request->id_od)
                        ->first();
                    //si la orden de despacho es Procesado
                    if ($od->estado == 21 || $od->estado == 1) { //entregado
                        //Anula salida
                        DB::table('almacen.mov_alm')
                            ->where('id_mov_alm', $request->id_salida)
                            ->update([
                                'estado' => 7,
                                'fecha_anulacion' => new Carbon(),
                                'usuario_anulacion' => $id_usuario,
                                'comentario_anulacion' => $request->observacion_guia_ven,
                                'id_motivo_anulacion' => $request->id_motivo_obs_ven,
                            ]);
                        //Anula el detalle
                        DB::table('almacen.mov_alm_det')
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
                        DB::table('almacen.guia_ven')
                            ->where('id_guia_ven', $request->id_guia_ven)
                            ->update(['estado' => 7]);
                        //Anula la Guia Detalle
                        DB::table('almacen.guia_ven_det')
                            ->where('id_guia_ven', $request->id_guia_ven)
                            ->update(['estado' => 7]);
                        //Quita estado de la orden
                        DB::table('almacen.orden_despacho')
                            ->where('id_od', $request->id_od)
                            ->update(['estado' => 1]);

                        $detalle = DB::table('almacen.guia_ven_det')
                            ->select('guia_ven_det.id_guia_ven_det', 'alm_det_req.id_detalle_requerimiento', 'guia_ven_det.id_od_det')
                            ->leftjoin('almacen.orden_despacho_det', 'orden_despacho_det.id_od_detalle', '=', 'guia_ven_det.id_od_det')
                            ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'orden_despacho_det.id_detalle_requerimiento')
                            ->where('id_guia_ven', $request->id_guia_ven)
                            ->get();

                        foreach ($detalle as $det) {
                            DB::table('almacen.alm_prod_serie')
                                ->where('id_guia_ven_det', $det->id_guia_ven_det)
                                ->update(['id_guia_ven_det' => null]);

                            if ($det->id_detalle_requerimiento !== null) {
                                //obtiene la reserva
                                $res = DB::table('almacen.alm_reserva')
                                    ->where([
                                        ['id_detalle_requerimiento', $det->id_detalle_requerimiento],
                                        ['id_almacen_reserva', $sal->id_almacen]
                                    ])
                                    ->first();

                                if ($res !== null) {
                                    //revierte la reserva
                                    DB::table('almacen.alm_reserva')
                                        ->where('id_reserva', $res->id_reserva)
                                        ->update(['estado' => 1]);
                                }
                            }

                            if ($det->id_od_det !== null) {
                                //revierte la reserva
                                DB::table('almacen.orden_despacho_det')
                                    ->where('id_od_detalle', $det->id_od_det)
                                    ->update(['estado' => 1]);
                            }
                        }
                        $msj = 'La salida fue anulada con éxito.';
                        $tipo = 'success';
                    } else {
                        $msj = 'La Orden de Despacho ya está con ' . $od->estado_doc;
                        $tipo = 'warning';
                    }
                } else {
                    //Anula salida
                    DB::table('almacen.mov_alm')
                        ->where('id_mov_alm', $request->id_salida)
                        ->update([
                            'estado' => 7,
                            'fecha_anulacion' => new Carbon(),
                            'usuario_anulacion' => $id_usuario,
                            'comentario_anulacion' => $request->observacion_guia_ven,
                            'id_motivo_anulacion' => $request->id_motivo_obs_ven,
                        ]);
                    //Anula el detalle
                    DB::table('almacen.mov_alm_det')
                        ->where('id_mov_alm', $request->id_salida)
                        ->update(['estado' => 7]);

                    if ($request->id_devolucion !== null) {
                        DB::table('cas.devolucion')
                            ->where('id_devolucion', $request->id_devolucion)
                            ->update(['estado' => 1]);
                    }
                    $detalle = DB::table('almacen.guia_ven_det')
                        ->select('guia_ven_det.id_guia_ven_det', 'alm_det_req.id_detalle_requerimiento', 'guia_ven_det.id_od_det')
                        ->leftjoin('almacen.orden_despacho_det', 'orden_despacho_det.id_od_detalle', '=', 'guia_ven_det.id_od_det')
                        ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'orden_despacho_det.id_detalle_requerimiento')
                        ->where('id_guia_ven', $request->id_guia_ven)
                        ->get();

                    foreach ($detalle as $det) {
                        DB::table('almacen.alm_prod_serie')
                            ->where('id_guia_ven_det', $det->id_guia_ven_det)
                            ->update(['id_guia_ven_det' => null]);
                    }

                    $dev_detalles = DB::table('cas.devolucion_detalle')
                        ->where('id_devolucion', $request->id_devolucion)
                        ->get();

                    foreach ($dev_detalles as $d) {

                        DB::table('almacen.alm_reserva')
                            ->where('id_detalle_devolucion', $d->id_detalle)
                            ->update(['estado' => 1]);
                    }

                    $msj = 'La salida fue anulada con éxito.';
                    $tipo = 'success';
                }
            } else {
                $msj = 'La salida ya fue revisada por el Jefe de Almacén';
                $tipo = 'warning';
            }
            DB::commit();
            return response()->json([
                'tipo' => $tipo,
                'mensaje' => $msj,
                'nroDespachosPendientes' => $this->nroDespachosPendientes(), 200
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al anular la salida. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
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

    public function verDetalleDespachox($id_od, $aplica_cambios, $tiene_transformacion)
    {
        $data = DB::table('almacen.orden_despacho_det')
            ->select(
                'orden_despacho_det.*',
                'alm_prod.id_producto',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.series as control_series',
                'alm_prod.part_number',
                'alm_prod.id_unidad_medida',
                'alm_und_medida.abreviatura',
                'orden_despacho.id_almacen',
                DB::raw("(SELECT SUM(guia_ven_det.cantidad)
                        FROM almacen.guia_ven_det
                        WHERE guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle
                            and guia_ven_det.estado != 7) as cantidad_despachada"),
                DB::raw("(SELECT SUM(reserva.stock_comprometido)
                        FROM almacen.alm_reserva AS reserva
                        WHERE reserva.id_detalle_requerimiento = orden_despacho_det.id_detalle_requerimiento
                            and reserva.estado != 7
                            and reserva.estado != 5
                            and reserva.id_almacen_reserva = orden_despacho.id_almacen) AS suma_reservas")
            )
            ->join('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'orden_despacho_det.id_od')
            ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'orden_despacho_det.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where([
                ['orden_despacho_det.id_od', '=', $id_od],
                ['orden_despacho_det.estado', '!=', 7],
                ['alm_det_req.estado', '!=', 7],
                // ['alm_det_req.entrega_cliente', '=', true],
                // ['orden_despacho_det.transformado', '=', ($aplica_cambios == 'si' ? false : ($tiene_transformacion == 'si' ? true : false))]
            ]);

        if ($aplica_cambios == true) {
            $lista = $data->where([['orden_despacho_det.transformado', '=', ($aplica_cambios == 'si' ? false : ($tiene_transformacion == 'si' ? true : false))]])
                ->get();
        } else {
            $lista = $data->where([['alm_det_req.entrega_cliente', '=', true]])
                ->get();
        }

        return response()->json($lista);
    }

    public function verDetalleDespacho($id_req, $id_od, $aplica_cambios, $tiene_transformacion)
    {
        $requerimiento = DB::table('almacen.alm_req')
            ->select('alm_req.id_tipo_requerimiento')
            ->where('id_requerimiento', $id_req)
            ->first();

        $data = DB::table('almacen.alm_det_req')
            ->select(
                'alm_det_req.id_detalle_requerimiento',
                'alm_det_req.cantidad',
                'orden_despacho_det.id_od_detalle',
                'alm_prod.id_producto',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.series as control_series',
                'alm_prod.part_number',
                'alm_prod.id_unidad_medida',
                'alm_und_medida.abreviatura',
                'alm_req.id_almacen',
                DB::raw("(SELECT SUM(guia_ven_det.cantidad)
                        FROM almacen.guia_ven_det
                        WHERE guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle
                            and guia_ven_det.estado != 7) as cantidad_despachada"),
                // 'alm_reserva.id_reserva',
                'alm_reserva.id_almacen_reserva',
                'alm_almacen.descripcion as almacen_reserva',
                DB::raw("(SELECT SUM(alm_reserva.stock_comprometido)
                        FROM almacen.alm_reserva
                        WHERE alm_reserva.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento
                            and alm_reserva.estado = 1) as stock_comprometido"),
                // 'alm_reserva.stock_comprometido'
            )
            // ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftJoin('almacen.orden_despacho_det', function ($join) use ($id_od) {
                $join->on('orden_despacho_det.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
                $join->where('orden_despacho_det.id_od', '=', $id_od);
                $join->where('orden_despacho_det.estado', '!=', 7);
            })
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftJoin('almacen.alm_reserva', function ($join) {
                $join->on('alm_reserva.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
                $join->where('alm_reserva.estado', '!=', 7);
                $join->where('alm_reserva.estado', '!=', 5);
            })
            ->leftJoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_reserva.id_almacen_reserva')
            ->where([
                ['alm_det_req.id_requerimiento', '=', $id_req],
                ['alm_det_req.estado', '!=', 7],
            ]);

        if ($aplica_cambios == 'si') { //DEspacho interno
            if ($requerimiento->id_tipo_requerimiento == 1) { //mgcp
                $lista = $data->where([
                    ['alm_det_req.tiene_transformacion', '=', ($aplica_cambios == 'si' ? false : ($tiene_transformacion == 'si' ? true : false))],
                    ['alm_det_req.entrega_cliente', '=', false]
                ])
                    ->distinct()->get();
            } else {
                $lista = $data->where([['alm_det_req.tiene_transformacion', '=', ($aplica_cambios == 'si' ? false : ($tiene_transformacion == 'si' ? true : false))]])
                    ->distinct()->get();
            }
        } else { //DEspacho externo
            // $valor = $requerimiento->id_tipo_requerimiento == 1 ? true : null;
            if ($requerimiento->id_tipo_requerimiento == 1) {
                $lista = $data->where([['alm_det_req.entrega_cliente', '=', true]])
                    ->distinct()->get();
            } else {
                $lista = $data->distinct()->get();
            }
        }

        return response()->json($lista);
    }

    public function verDetalleDevolucion($id_devolucion)
    {
        $lista = DB::table('cas.devolucion_detalle')
            ->select(
                'devolucion_detalle.id_detalle',
                'devolucion_detalle.cantidad',
                'alm_prod.id_producto',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.series as control_series',
                'alm_prod.part_number',
                'alm_prod.id_unidad_medida',
                'alm_und_medida.abreviatura',
                'devolucion.id_almacen',
                // DB::raw("(SELECT SUM(guia_ven_det.cantidad)
                //         FROM almacen.guia_ven_det
                //         WHERE guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle
                //             and guia_ven_det.estado != 7) as cantidad_despachada"),
                // 'alm_reserva.id_reserva',
                // 'alm_reserva.id_almacen_reserva',
                'alm_almacen.descripcion as almacen_reserva',
                // DB::raw("(SELECT SUM(alm_reserva.stock_comprometido)
                //         FROM almacen.alm_reserva
                //         WHERE alm_reserva.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento
                //             and alm_reserva.estado = 1) as stock_comprometido"),
                // 'alm_reserva.stock_comprometido'
            )
            ->leftJoin('cas.devolucion', 'devolucion.id_devolucion', '=', 'devolucion_detalle.id_devolucion')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'devolucion_detalle.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            // ->leftJoin('almacen.alm_reserva', function ($join) {
            //     $join->on('alm_reserva.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
            //     $join->where('alm_reserva.estado', '!=', 7);
            //     $join->where('alm_reserva.estado', '!=', 5);
            // })
            ->leftJoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'devolucion.id_almacen')
            ->where([
                ['devolucion_detalle.id_devolucion', '=', $id_devolucion],
                ['devolucion_detalle.estado', '!=', 7],
            ])->get();

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
                            <p style="text-align:left;font-size:10px;margin:0px;"><strong>' . strtoupper(config('global.nombreSistema')) . ' '  . config('global.version') . '</strong></p>
                        </td>
                    </tr>
                </table>
                <h3 style="margin:0px;"><center>SALIDA DE ALMACÉN</center></h3>
                <h5><center>' . $salida->des_almacen . '</center></h5>

                <table border="0">
                    <tr>
                        <td width=120px>Salida N°</td>
                        <td width=10px>:</td>
                        <td width=280px>' . $salida->codigo . '</td>
                        <td>Fecha Salida</td>
                        <td width=10px>:</td>
                        <td>' . (new Carbon($salida->fecha_emision))->format('d-m-Y') . '</td>
                    </tr>';

        if ($salida->guia !== null) {
            $html .= '<tr>
                                <td>Guía de Venta</td>
                                <td width=10px>:</td>
                                <td>' . $salida->guia . '</td>
                                <td>Fecha Guía</td>
                                <td width=10px>:</td>
                                <td>' . (new Carbon($salida->fecha_guia))->format('d-m-Y') . '</td>
                            </tr>';
        }
        if ($salida->fecha_transformacion !== null) {
            $html .= '<tr>
                                <td>Transformación</td>
                                <td>:</td>
                                <td width=250px>' . $salida->cod_transformacion . '</td>
                                <td width=150px>Fecha Transformación</td>
                                <td width=10px>:</td>
                                <td>' . (new Carbon($salida->fecha_transformacion))->format('d-m-Y') . '</td>
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
                                <td>' . (new Carbon($salida->fecha_transferencia))->format('d-m-Y') . '</td>
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
                                    <td>' . round(($det->valorizacion / $det->cantidad), 4, PHP_ROUND_HALF_UP) . '</td>
                                    <td class="right">' . round($det->valorizacion, 4, PHP_ROUND_HALF_UP) . '</td>
                                </tr>';
            $i++;
        }
        $html .= '</tbody>
                </table>
                <p style="text-align:right;font-size:11px;">Elaborado por: ' . $salida->nom_usuario . ' ' . (new Carbon($salida->fecha_registro))->format('d-m-Y H:i') . '</p>

            </body>
        </html>';

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        return $pdf->stream();
        return $pdf->download('salida.pdf');
        // return response()->json(['salida'=>$salida,'detalle'=>$detalle]);
    }


    function anular_orden_despacho($id_od, $tipo)
    {
        try {
            DB::beginTransaction();

            DB::table('almacen.orden_despacho')
                ->where('id_od', $id_od)
                ->update(['estado' => 7]);

            $detalle = DB::table('almacen.orden_despacho_det')
                ->select('orden_despacho_det.*', 'orden_despacho.id_almacen')
                ->join('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'orden_despacho_det.id_od')
                ->where([
                    ['orden_despacho_det.id_od', '=', $id_od],
                    ['orden_despacho_det.transformado', '=', ($tipo == 'interno' ? false : true)]
                ])->get();

            DB::table('almacen.orden_despacho_det')
                ->where('id_od', $id_od)
                ->update(['estado' => 7]);

            foreach ($detalle as $det) {

                DB::table('almacen.alm_det_req')
                    ->where('id_detalle_requerimiento', $det->id_detalle_requerimiento)
                    ->update(['estado' => 28]);

                //envia la reserva
                DB::table('almacen.alm_reserva')
                    ->where([
                        ['id_detalle_requerimiento', $det->id_detalle_requerimiento],
                        ['id_almacen_reserva', '=', $det->id_almacen],
                        ['estado', '!=', 7]
                    ])
                    ->update([
                        'estado' => 1,
                        'id_materia' => null
                    ]);
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

    public function listarSeriesGuiaVen($id_producto, $id_almacen)
    {
        $series = DB::table('almacen.alm_prod_serie')
            ->select(
                'alm_prod_serie.*',
                'transformacion.codigo as codigo_customizacion',
                DB::raw("(tp_doc_almacen.abreviatura) || '-' || (guia_com.serie) || '-' || (guia_com.numero) as guia_com")
            )
            ->leftjoin('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'alm_prod_serie.id_guia_com_det')
            ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->leftjoin('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_com.id_tp_doc_almacen')
            ->leftjoin('almacen.transfor_materia', function ($join) {
                $join->on('transfor_materia.id_materia', '=', 'alm_prod_serie.id_base');
                $join->where('transfor_materia.estado', '!=', 7);
            })
            ->leftjoin('almacen.mov_alm_det', function ($join) {
                $join->on('mov_alm_det.id_materia', '=', 'transfor_materia.id_materia');
                $join->where('mov_alm_det.estado', '!=', 7);
            })
            ->leftjoin('almacen.transformacion', 'transformacion.id_transformacion', '=', 'transfor_materia.id_transformacion')
            ->where([
                ['alm_prod_serie.id_prod', '=', $id_producto],
                ['alm_prod_serie.id_almacen', '=', $id_almacen],
                // ['alm_prod_serie.id_guia_ven_det', '=', null],
                // ['mov_alm_det.id_materia', '=', null],
                ['alm_prod_serie.estado', '=', 1]
            ])
            ->whereNull('alm_prod_serie.id_guia_ven_det')
            ->whereNull('mov_alm_det.id_materia')
            ->get();
        return response()->json($series);
    }

    public function marcar_despachado($id_od, $id_transformacion)
    {
        try {
            DB::beginTransaction();

            $od = DB::table('almacen.orden_despacho')
                ->where('id_od', $id_od)->first();

            if ($od->aplica_cambios) {

                DB::table('almacen.orden_despacho')
                    ->where('id_od', $id_od)
                    ->update(['estado' => 21]); //Entregado

                if ($id_transformacion !== null) {
                    DB::table('almacen.transformacion')
                        ->where('id_transformacion', $id_transformacion)
                        ->update(['estado' => 21]); //Entregado
                }
            } else {
                DB::table('almacen.orden_despacho')
                    ->where('id_od', $id_od)
                    ->update(['estado' => 29]); //Despachado

                DB::table('almacen.alm_req')
                    ->where('id_requerimiento', $od->id_requerimiento)
                    ->update(['estado' => 29]); //Despachado
            }

            DB::commit();
            return response()->json('ok');
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(':(');
        }
    }

    public function listarDetalleGuiaSalida($id_guia_ven)
    {
        $detalle = DB::table('almacen.guia_ven_det')
            ->select(
                'guia_ven_det.*',
                'alm_prod.codigo',
                'alm_prod.part_number',
                'alm_prod.descripcion',
                'alm_prod.series as serie',
                'alm_und_medida.abreviatura',
                'alm_subcat.descripcion as marca',
                // 'guia_ven.serie',
                // 'guia_ven.numero',
                'guia_ven.id_almacen'
            )
            ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_ven_det.id_producto')
            ->leftjoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where([['guia_ven_det.id_guia_ven', '=', $id_guia_ven], ['guia_ven_det.estado', '!=', 7]])
            ->get();

        $lista = [];

        foreach ($detalle as $det) {

            $series = DB::table('almacen.alm_prod_serie')
                ->select('alm_prod_serie.*')
                ->where([
                    ['alm_prod_serie.id_guia_ven_det', '=', $det->id_guia_ven_det],
                    ['alm_prod_serie.estado', '=', 1]
                ])
                ->get();

            array_push($lista, [
                'id_guia_ven_det' => $det->id_guia_ven_det,
                'id_almacen' => $det->id_almacen,
                'id_producto' => $det->id_producto,
                'codigo' => $det->codigo,
                'part_number' => $det->part_number,
                'descripcion' => $det->descripcion,
                'marca' => $det->marca,
                'cantidad' => $det->cantidad,
                'abreviatura' => $det->abreviatura,
                // 'serie' => $det->serie,
                // 'numero' => $det->numero,
                'series' => $series
            ]);
        }
        return $lista;
    }

    public function detalleMovimientoSalida($id_guia_ven)
    {
        $lista = $this->listarDetalleGuiaSalida($id_guia_ven);
        return response()->json($lista);
    }

    public function actualizarSalida(Request $request)
    {
        try {
            DB::beginTransaction();
            $msj = '';
            $salida = DB::table('almacen.guia_ven')
            ->select('guia_ven.fecha_almacen', 'guia_ven.id_almacen')
            ->where('id_guia_ven', $request->id_guia_ven)
            ->first();

            $periodo_estado = CierreAperturaController::consultarPeriodo($request->salida_fecha_emision, $salida->id_almacen);

            if (intval($periodo_estado) == 2){
                $msj = 'El periodo esta cerrado. Consulte con contabilidad.';
                
            } else {

                $fecha_anterior = $salida->fecha_almacen;
                $id_usuario = Auth::user()->id_usuario;

                DB::table('almacen.guia_ven')->where('id_guia_ven', $request->id_guia_ven)
                    ->update(
                        [
                            'serie' => $request->salida_serie,
                            'numero' => $request->salida_numero,
                            'comentario' => $request->salida_comentario,
                            'id_operacion' => $request->id_operacion_salida,
                            'fecha_emision' => $request->salida_fecha_emision,
                            'fecha_almacen' => $request->salida_fecha_almacen,
                            'punto_partida' => $request->salida_punto_partida,
                            'punto_llegada' => $request->salida_punto_llegada,
                        ]
                    );

                //Agrega motivo anulacion a la guia
                DB::table('almacen.guia_ven_obs')->insert(
                    [
                        'id_guia_ven' => $request->id_guia_ven,
                        'observacion' => $request->observacion,
                        'registrado_por' => $id_usuario,
                        'id_motivo_anu' => $request->id_motivo_cambio,
                        'fecha_registro' => date('Y-m-d H:i:s')
                    ]
                );

                DB::table('almacen.mov_alm')
                    ->where('id_mov_alm', $request->id_mov_alm)
                    ->update([
                        'fecha_emision' => $request->salida_fecha_almacen,
                        'id_operacion' => $request->id_operacion_salida
                    ]);
                $productos_en_negativo = '';

                if ($salida->fecha_almacen !== $request->salida_fecha_almacen) {
                    // DB::table('almacen.mov_alm')
                    //     ->where('id_mov_alm', $request->id_mov_alm)
                    //     ->update([
                    //         'fecha_emision' => $request->salida_fecha_almacen,
                    //         'id_operacion' => $request->id_operacion_salida
                    //     ]);

                    //Validacion por cambio de fecha
                    if ($request->salida_fecha_almacen > $salida->fecha_almacen) {
                        $productos = DB::table('almacen.guia_ven_det')
                            ->select('guia_ven_det.id_producto', 'alm_prod.descripcion')
                            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_ven_det.id_producto')
                            ->where([
                                ['guia_ven_det.id_guia_ven', '=', $request->id_guia_ven],
                                ['guia_ven_det.estado', '!=', 7]
                            ])
                            ->get();
                        $anio = (new Carbon($request->salida_fecha_almacen))->format('Y');

                        foreach ($productos as $prod) {
                            $alerta_negativo = ValidaMovimientosController::validaNegativosHistoricoKardex(
                                $prod->id_producto,
                                $salida->id_almacen,
                                $anio
                            );

                            if ($alerta_negativo > 0) {
                                $productos_en_negativo .= $prod->descripcion . ' Genera ' . $alerta_negativo . ' movimiento(s) negativo(s).<br>';
                            }
                        }
                    }
                }

                $msj = '';
                if ($productos_en_negativo !== '') {
                    // DB::beginTransaction();

                    DB::table('almacen.mov_alm')
                        ->where([
                            ['id_guia_ven', '=', $request->id_guia_ven],
                            ['estado', '!=', 7]
                        ])
                        ->update(['fecha_emision' => $fecha_anterior]);

                    DB::table('almacen.guia_ven')->where('id_guia_ven', $request->id_guia_ven)
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

    public function guiaSalidaExcel($id_guia_ven)
    {
        $guia = DB::table('almacen.guia_ven')
            ->select(
                'guia_ven.*',
                'adm_empresa.id_empresa',
                'empresa.nro_documento as empresa_nro_documento',
                'empresa.razon_social as empresa_razon_social',
                'cliente.nro_documento as cliente_nro_documento',
                'cliente.razon_social as cliente_razon_social',
                DB::raw("(SELECT json_agg(DISTINCT alm_req.codigo) FROM almacen.guia_ven_det
                INNER JOIN almacen.orden_despacho_det ON orden_despacho_det.id_od_detalle = guia_ven_det.id_od_det
                INNER JOIN almacen.alm_det_req ON alm_det_req.id_detalle_requerimiento = orden_despacho_det.id_detalle_requerimiento
                INNER JOIN almacen.alm_req ON alm_req.id_requerimiento = alm_det_req.id_requerimiento
                WHERE guia_ven.id_guia_ven = almacen.guia_ven_det.id_guia_ven ) as codigos_requerimiento"),

            )
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'guia_ven.id_almacen')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('contabilidad.adm_contri as empresa', 'empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftjoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'guia_ven.id_cliente')
            ->leftjoin('contabilidad.adm_contri as cliente', 'cliente.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->where('guia_ven.id_guia_ven', $id_guia_ven)
            ->first();
        $detalle = $this->listarDetalleGuiaSalida($id_guia_ven);
        //OKC PYC SVS PTEC
        switch ($guia->id_empresa) {
            case 1: //OKC
                GuiaSalidaExcelFormatoOKCController::construirExcel(['guia' => $guia, 'detalle' => $detalle]);
                break;
            case 2: //PYC
                GuiaSalidaExcelFormatoPYCController::construirExcel(['guia' => $guia, 'detalle' => $detalle]);
                break;
            case 3: //SVS
                GuiaSalidaExcelFormatoSVSController::construirExcel(['guia' => $guia, 'detalle' => $detalle]);
                break;
            case 4: //JEDR
                GuiaSalidaExcelFormatoJEDRController::construirExcel(['guia' => $guia, 'detalle' => $detalle]);
                break;
            case 5: //RBDB
                GuiaSalidaExcelFormatoRBDBController::construirExcel(['guia' => $guia, 'detalle' => $detalle]);
                break;
            case 6: //PTEC
                GuiaSalidaExcelFormatoPTECController::construirExcel(['guia' => $guia, 'detalle' => $detalle]);
                break;

            default:
                return ['guia' => $guia, 'detalle' => $detalle];
                break;
        }
    }

    public function seriesVentaExcel($id_guia_ven_det)
    {
        $data = DB::table('almacen.alm_prod_serie')
            ->select('alm_prod_serie.*')
            ->where([
                ['alm_prod_serie.id_guia_ven_det', '=', $id_guia_ven_det],
                ['alm_prod_serie.estado', '=', 1]
            ])
            ->get();
        return Excel::download(new SeriesGuiaVentaDetalleExport($data), 'series-' . $data[0]->id_prod . '.xlsx');
    }

    public function salidasPendientesExcel()
    {
        $data = DB::table('almacen.orden_despacho_det')
            ->select(
                'orden_despacho.*',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'alm_req.codigo as codigo_req',
                'alm_req.concepto',
                'alm_req.tiene_transformacion',
                'alm_req.estado as estado_requerimiento',
                'alm_req.obs_facturacion',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'transformacion.id_transformacion',
                'oc_propias_view.nro_orden',
                'oc_propias_view.codigo_oportunidad',
                'oc_propias_view.id as id_oc_propia',
                'oc_propias_view.tipo',
                'alm_almacen.descripcion as almacen_descripcion',
                'alm_prod.id_producto',
                'alm_prod.codigo as codigo_producto',
                'alm_prod.descripcion',
                'alm_prod.series as control_series',
                'alm_prod.part_number',
                'alm_prod.id_unidad_medida',
                'alm_det_req.cantidad',
                'alm_und_medida.abreviatura',
                DB::raw("(SELECT SUM(guia_ven_det.cantidad)
                        FROM almacen.guia_ven_det
                        WHERE guia_ven_det.id_od_det = orden_despacho_det.id_od_detalle
                            and guia_ven_det.estado != 7) as cantidad_despachada"),
                'alm_reserva.id_almacen_reserva',
                'almacen_reserva.descripcion as almacen_reserva',
                DB::raw("(SELECT SUM(alm_reserva.stock_comprometido)
                        FROM almacen.alm_reserva
                        WHERE alm_reserva.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento
                            and alm_reserva.estado = 1) as stock_comprometido")
            )
            ->join('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'orden_despacho_det.id_od')
            ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'orden_despacho_det.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->leftjoin('almacen.transformacion', 'transformacion.id_od', '=', 'orden_despacho.id_od')
            ->leftjoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'orden_despacho.id_almacen')
            ->leftjoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'orden_despacho.estado')
            ->leftJoin('almacen.alm_reserva', function ($join) {
                $join->on('alm_reserva.id_detalle_requerimiento', '=', 'orden_despacho_det.id_detalle_requerimiento');
                $join->where('alm_reserva.estado', '!=', 7);
                $join->where('alm_reserva.estado', '!=', 5);
            })
            ->leftjoin('almacen.alm_almacen as almacen_reserva', 'almacen_reserva.id_almacen', '=', 'alm_reserva.id_almacen_reserva')
            ->whereIn('orden_despacho.estado', [1, 25])
            ->where('orden_despacho.flg_despacho', 0)
            ->get();

        return Excel::download(new SalidasPendientesExport(
            $data,
        ), 'Salidas Pendientes al ' . new Carbon() . '.xlsx');
    }

    public function salidasProcesadasExcel()
    {
        $data = $this->listarSalidasProcesadas();
        return Excel::download(new SalidasProcesadasExport(
            $data->get(),
        ), 'Salidas Procesadas al ' . new Carbon() . '.xlsx');
    }

    public function actualizaItemsODE($id_requerimiento)
    {
        try {
            DB::beginTransaction();

            $insertados = 0;
            $requerimiento = DB::table('almacen.alm_req')
                ->where('id_requerimiento', $id_requerimiento)
                ->first();

            $ode = DB::table('almacen.orden_despacho')
                ->where([
                    ['id_requerimiento', '=', $id_requerimiento],
                    ['aplica_cambios', '=', false],
                    ['estado', '!=', 7]
                ])
                ->first();

            if ($requerimiento->id_tipo_requerimiento == 1) {

                $detalle = DB::table('almacen.alm_det_req')
                    ->where([
                        ['id_requerimiento', '=', $id_requerimiento],
                        ['id_tipo_item', '=', 1],
                        ['entrega_cliente', '=', true],
                        ['estado', '!=', 7]
                    ])
                    ->get();
            } else {
                $detalle = DB::table('almacen.alm_det_req')
                    ->where([
                        ['id_requerimiento', '=', $id_requerimiento],
                        ['id_tipo_item', '=', 1],
                        ['estado', '!=', 7]
                    ])
                    ->get();
            }

            foreach ($detalle as $d) {
                $det = DB::table('almacen.orden_despacho_det')
                    ->where([
                        ['id_detalle_requerimiento', '=', $d->id_detalle_requerimiento],
                        ['id_od', '=', $ode->id_od],
                        ['estado', '!=', 7]
                    ])
                    ->first();

                if ($det == null) {
                    DB::table('almacen.orden_despacho_det')
                        ->insert([
                            'id_od' => $ode->id_od,
                            'id_detalle_requerimiento' => $d->id_detalle_requerimiento,
                            'cantidad' => $d->cantidad,
                            'transformado' => $d->tiene_transformacion,
                            'estado' => 1,
                            'fecha_registro' => new Carbon()
                        ]);
                    $insertados++;
                }

                DB::table('almacen.alm_det_req')
                    ->where('id_detalle_requerimiento', $d->id_detalle_requerimiento)
                    ->update(['estado' => 23]); //despacho externo
            }
            if ($insertados > 0) {
                $tipo = "success";
                $msj = "Se insertaron " . $insertados . " items";
            } else {
                $tipo = "warning";
                $msj = "No se actualizaron items.";
            }
            DB::commit();
            return response()->json(['tipo' => $tipo, 'mensaje' => $msj, 200]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'danger', 'mensaje' => 'Algo salió mal. Inténtelo nuevamente.', 200]);
        }
    }

    public function actualizaItemsODI($id_requerimiento)
    {
        try {
            DB::beginTransaction();

            $insertados = 0;
            $tipo = '';
            $msj = '';

            $odi = DB::table('almacen.orden_despacho')
                ->where([
                    ['id_requerimiento', '=', $id_requerimiento],
                    ['aplica_cambios', '=', true],
                    ['estado', '!=', 7]
                ])
                ->first();

            if ($odi !== null) {

                $transformacion = DB::table('almacen.transformacion')
                    ->where([
                        ['id_od', '=', $odi->id_od],
                        ['estado', '!=', 7]
                    ])
                    ->first();

                $detalles = DB::table('almacen.alm_det_req')
                    ->where([
                        ['id_requerimiento', '=', $id_requerimiento],
                        ['id_tipo_item', '=', 1],
                        ['estado', '!=', 7]
                    ])
                    ->get();

                foreach ($detalles as $i) {
                    $det = DB::table('almacen.orden_despacho_det')
                        ->where([
                            ['id_detalle_requerimiento', '=', $i->id_detalle_requerimiento],
                            ['id_od', '=', $odi->id_od],
                            ['estado', '!=', 7]
                        ])
                        ->first();

                    if ($det == null) {
                        $id_od_detalle = DB::table('almacen.orden_despacho_det')
                            ->insertGetId(
                                [
                                    'id_od' => $odi->id_od,
                                    'id_detalle_requerimiento' => $i->id_detalle_requerimiento,
                                    'cantidad' => $i->cantidad,
                                    'transformado' => $i->tiene_transformacion,
                                    'estado' => 1,
                                    'fecha_registro' => new Carbon()
                                ],
                                'id_od_detalle'
                            );
                        $insertados++;
                    } else {
                        $id_od_detalle = $det->id_od_detalle;
                    }

                    if ($i->tiene_transformacion && $i->entrega_cliente) {
                        $transformado = DB::table('almacen.transfor_transformado')
                            ->where([
                                ['id_od_detalle', '=', $id_od_detalle],
                                ['estado', '!=', 7]
                            ])
                            ->first();

                        if ($transformado == null) {
                            DB::table('almacen.transfor_transformado')
                                ->insert([
                                    'id_transformacion' => $transformacion->id_transformacion,
                                    'id_od_detalle' => $id_od_detalle,
                                    'cantidad' => $i->cantidad,
                                    'valor_unitario' => 0,
                                    'valor_total' => 0,
                                    'estado' => 1,
                                    'fecha_registro' => new Carbon()
                                ]);
                        }
                    } else if ($i->tiene_transformacion == false && $i->entrega_cliente == false) {
                        $materia = DB::table('almacen.transfor_materia')
                            ->where([
                                ['id_od_detalle', '=', $id_od_detalle],
                                ['estado', '!=', 7]
                            ])
                            ->first();

                        if ($materia == null) {
                            DB::table('almacen.transfor_materia')
                                ->insert([
                                    'id_transformacion' => $transformacion->id_transformacion,
                                    'id_od_detalle' => $id_od_detalle,
                                    'cantidad' => $i->cantidad,
                                    'valor_unitario' => 0,
                                    'valor_total' => 0,
                                    'estado' => 1,
                                    'fecha_registro' => new Carbon()
                                ]);
                        }
                    }
                }
                if ($insertados > 0) {
                    $tipo = "success";
                    $msj = "Se insertaron " . $insertados . " items";
                } else {
                    $tipo = "warning";
                    $msj = "No se actualizaron items.";
                }
            }
            DB::commit();
            return response()->json(['tipo' => $tipo, 'mensaje' => $msj, 200]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'danger', 'mensaje' => 'Algo salió mal. Inténtelo nuevamente.', 200]);
        }
    }
    public function verAdjuntos(Request $request)
    {

        $data = Adjuntos::where('estado', 1)->where('id_requerimiento', $request->id)->get();
        return response()->json([
            "success" => true,
            "status" => 200,
            "data" => $data
        ]);
    }

    public function mostrarClientes()
    {
        $data = DB::table('comercial.com_cliente')
            ->select('com_cliente.id_cliente', 'adm_contri.nro_documento', 'adm_contri.razon_social')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->where('com_cliente.estado', 1)
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function guardarCliente(Request $request)
    {
        try {
            DB::beginTransaction();
            $array = [];

            $contribuyente = DB::table('contabilidad.adm_contri')
                ->where('nro_documento', trim($request->nro_documento))
                ->first();

            if ($contribuyente !== null) {
                $array = array(
                    'tipo' => 'warning',
                    'mensaje' => 'Ya existe el RUC ingresado.',
                );
            } else {
                $id_contribuyente = DB::table('contabilidad.adm_contri')
                    ->insertGetId(
                        [
                            'nro_documento' => trim($request->nro_documento),
                            'razon_social' => strtoupper(trim($request->razon_social)),
                            'telefono' => trim($request->telefono),
                            'direccion_fiscal' => trim($request->direccion_fiscal),
                            'fecha_registro' => date('Y-m-d H:i:s'),
                            'estado' => 1,
                            'transportista' => false
                        ],
                        'id_contribuyente'
                    );

                DB::table('comercial.com_cliente')
                    ->insert([
                        'id_contribuyente' => $id_contribuyente,
                        'estado' => 1,
                        'fecha_registro' => new Carbon(),
                    ]);

                $array = array(
                    'tipo' => 'success',
                    'mensaje' => 'Se guardó el cliente correctamente',
                );
            }
            DB::commit();
            return response()->json($array);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(
                array(
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un problema. Por favor intente de nuevo',
                    'error' => $e->getMessage()
                )
            );
        }
    }
}
