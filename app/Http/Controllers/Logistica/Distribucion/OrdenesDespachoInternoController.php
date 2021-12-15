<?php

namespace App\Http\Controllers\Logistica\Distribucion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Distribucion\OrdenDespacho;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdenesDespachoInternoController extends Controller
{
    function view_ordenes_despacho_interno()
    {
        return view('almacen/distribucion/ordenesDespachoInterno');
    }

    public function listarRequerimientosPendientesDespachoInterno(Request $request)
    {
        $data = DB::table('almacen.orden_despacho')
            ->select(
                'alm_req.*',
                'sis_usua.nombre_corto as responsable',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                DB::raw("(ubi_dis.descripcion) || ' - ' || (ubi_prov.descripcion) || ' - ' || (ubi_dpto.descripcion) AS ubigeo_descripcion"),
                'alm_almacen.descripcion as almacen_descripcion',
                'alm_req.id_sede as sede_requerimiento',
                'sede_req.descripcion as sede_descripcion_req',
                'orden_despacho.id_od',
                'orden_despacho.fecha_despacho',
                'est_od.estado_doc as estado_od',
                'est_od.bootstrap_color as estado_bootstrap_od',
                'orden_despacho.codigo as codigo_od',
                'transformacion.id_transformacion',
                'transformacion.codigo as codigo_transformacion',
                'est_trans.estado_doc as estado_transformacion',
                'est_trans.bootstrap_color as estado_bootstrap_transformacion',
                // 'orden_despacho.estado as estado_od',
                'adm_contri.nro_documento as cliente_ruc',
                'adm_contri.razon_social as cliente_razon_social',
                'oc_propias_view.nro_orden',
                'oc_propias_view.codigo_oportunidad',
                'oc_propias_view.id as id_oc_propia',
                'oc_propias_view.tipo',
                DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req where
                        alm_det_req.id_requerimiento = alm_req.id_requerimiento
                        and alm_det_req.estado != 7
                        and alm_det_req.id_producto is null) AS productos_no_mapeados")
            )
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->join('almacen.transformacion', 'transformacion.id_od', '=', 'orden_despacho.id_od')
            ->join('administracion.adm_estado_doc as est_trans', 'est_trans.id_estado_doc', '=', 'transformacion.estado')
            ->join('administracion.adm_estado_doc as est_od', 'est_od.id_estado_doc', '=', 'orden_despacho.estado')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.estado')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->leftJoin('configuracion.ubi_dis', 'ubi_dis.id_dis', '=', 'alm_req.id_ubigeo_entrega')
            ->leftJoin('configuracion.ubi_prov', 'ubi_prov.id_prov', '=', 'ubi_dis.id_prov')
            ->leftJoin('configuracion.ubi_dpto', 'ubi_dpto.id_dpto', '=', 'ubi_prov.id_dpto')
            ->leftJoin('administracion.sis_sede as sede_req', 'sede_req.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_req.id_almacen')
            ->leftJoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->where([
                ['alm_req.estado', '!=', 7],
                ['orden_despacho.aplica_cambios', '=', true],
                ['orden_despacho.estado', '!=', 7],
                ['orden_despacho.estado', '!=', 10],
            ]);
        if ($request->select_mostrar == 1) {
            $data->where('orden_despacho.estado', 25);
        } else if ($request->select_mostrar == 2) {
            $data->where('orden_despacho.estado', 25);
            $data->whereDate('fecha_despacho', (new Carbon())->format('Y-m-d'));
        }
        return datatables($data)->toJson();
    }

    public function priorizar(Request $request)
    {
        try {
            DB::beginTransaction();
            $despachos = json_decode($request->despachos_internos);

            foreach ($despachos as $det) {
                DB::table('almacen.orden_despacho')
                    ->where('id_od', $det->id_od)
                    ->update([
                        'fecha_despacho' => $request->fecha_despacho,
                        'estado' => 25 //priorizado
                    ]);

                DB::table('almacen.transformacion')
                    ->where('id_transformacion', $det->id_transformacion)
                    ->update(['estado' => 25]); //priorizado
            }

            DB::commit();
            return response()->json('ok');
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(':(');
        }
    }

    public function generarDespachoInternoNroOrden()
    {
        $fechaRegistro = new Carbon();
        $nro_orden = DB::table('almacen.orden_despacho')
            ->where([['estado', '!=', 7], ['aplica_cambios', '=', true]])
            ->whereDate('orden_despacho.fecha_despacho', '=', (new Carbon($fechaRegistro))->format('Y-m-d'))
            ->count();
        return $nro_orden;
    }
    public function generarDespachoInterno(Request $request)
    {
        try {
            DB::beginTransaction();

            $req = DB::table('almacen.alm_req')
                ->select('alm_req.*', 'despachoInterno.codigo as codigoDespachoInterno')
                ->where('alm_req.id_requerimiento', $request->id_requerimiento)
                ->leftJoin('almacen.orden_despacho as despachoInterno', function ($join) {
                    $join->on('despachoInterno.id_requerimiento', '=', 'alm_req.id_requerimiento');
                    $join->where('despachoInterno.aplica_cambios', '=', true);
                    $join->where('despachoInterno.estado', '!=', 7);
                })
                ->first();

            if ($req !== null) {
                if ($req->codigoDespachoInterno !== null) {
                    $arrayRspta = array(
                        'tipo' => 'warning',
                        'mensaje' => 'Ya existe una Orden de Transformación generada, es la: ' . $req->codigoDespachoInterno
                    );
                } else {
                    $codigo = OrdenDespacho::ODnextId($req->id_almacen, true, 0); //$this->ODnextId(date('Y-m-d'), $req->id_almacen, true);
                    $usuario = Auth::user()->id_usuario;
                    $fechaRegistro = new Carbon();
                    $nro_orden = DB::table('almacen.orden_despacho')
                        ->where([['estado', '!=', 7], ['aplica_cambios', '=', true]])
                        ->whereDate('fecha_despacho', '=', (new Carbon($request->fecha_despacho))->format('Y-m-d'))
                        ->count();

                    $id_od = DB::table('almacen.orden_despacho')
                        ->insertGetId(
                            [
                                'id_sede' => $req->id_sede,
                                'id_requerimiento' => $req->id_requerimiento,
                                'id_almacen' => $req->id_almacen,
                                'codigo' => $codigo,
                                'fecha_despacho' => $request->fecha_despacho,
                                'nro_orden' => ($nro_orden + 1),
                                'aplica_cambios' => true,
                                'registrado_por' => $usuario,
                                'fecha_registro' => $fechaRegistro,
                                'estado' => 1,
                            ],
                            'id_od'
                        );

                    //Agrega accion en requerimiento
                    DB::table('almacen.alm_req_obs')
                        ->insert([
                            'id_requerimiento' => $req->id_requerimiento,
                            'accion' => 'DESPACHO INTERNO',
                            'descripcion' => 'Se generó la Orden de Despacho Interna ' . $codigo,
                            'id_usuario' => $usuario,
                            'fecha_registro' => $fechaRegistro
                        ]);

                    $codTrans = $this->transformacion_nextId($fechaRegistro, $req->id_almacen);

                    $id_transformacion = DB::table('almacen.transformacion')
                        ->insertGetId(
                            [
                                'codigo' => $codTrans,
                                'id_od' => $id_od,
                                'id_cc' => $req->id_cc,
                                'id_moneda' => 1,
                                'id_almacen' => $req->id_almacen,
                                'descripcion_sobrantes' => '', //$req->descripcion_sobrantes,
                                'total_materias' => 0,
                                'total_directos' => 0,
                                'costo_primo' => 0,
                                'total_indirectos' => 0,
                                'total_sobrantes' => 0,
                                'costo_transformacion' => 0,
                                'registrado_por' => $usuario,
                                'conformidad' => false,
                                'tipo_cambio' => 1,
                                'fecha_registro' => $fechaRegistro,
                                'estado' => 1,
                            ],
                            'id_transformacion'
                        );

                    $detalles = DB::table('almacen.alm_det_req')
                        ->where([
                            ['id_requerimiento', '=', $request->id_requerimiento],
                            ['estado', '!=', 7]
                        ])
                        ->get();

                    foreach ($detalles as $i) {

                        $id_od_detalle = DB::table('almacen.orden_despacho_det')
                            ->insertGetId(
                                [
                                    'id_od' => $id_od,
                                    // 'id_producto' => ($i->id_producto !== null ? $i->id_producto : null),
                                    // 'descripcion_producto' => $i->descripcion,
                                    'id_detalle_requerimiento' => $i->id_detalle_requerimiento,
                                    'cantidad' => $i->cantidad,
                                    'transformado' => $i->tiene_transformacion,
                                    'estado' => 1,
                                    'fecha_registro' => $fechaRegistro
                                ],
                                'id_od_detalle'
                            );

                        if ($i->tiene_transformacion) {
                            DB::table('almacen.transfor_transformado')
                                ->insert([
                                    'id_transformacion' => $id_transformacion,
                                    // 'id_producto' => ($i->id_producto !== null ? $i->id_producto : null),
                                    'id_od_detalle' => $id_od_detalle,
                                    'cantidad' => $i->cantidad,
                                    'valor_unitario' => 0,
                                    'valor_total' => 0,
                                    'estado' => 1,
                                    'fecha_registro' => $fechaRegistro
                                ]);
                        } else {
                            DB::table('almacen.transfor_materia')
                                ->insert([
                                    'id_transformacion' => $id_transformacion,
                                    // 'id_producto' => ($i->id_producto !== null ? $i->id_producto : null),
                                    'cantidad' => $i->cantidad,
                                    'id_od_detalle' => $id_od_detalle,
                                    'valor_unitario' => 0, //($val / $i->cantidad),
                                    'valor_total' => 0,
                                    'estado' => 1,
                                    'fecha_registro' => $fechaRegistro
                                ]);
                        }

                        DB::table('almacen.alm_det_req')
                            ->where('id_detalle_requerimiento', $i->id_detalle_requerimiento)
                            ->update(['estado' => 22]); //despacho interno

                    }

                    DB::table('almacen.alm_req')
                        ->where('id_requerimiento', $request->id_requerimiento)
                        ->update(['estado' => 22]); //despacho interno

                    $arrayRspta = array(
                        'tipo' => 'success',
                        'mensaje' => 'Se programó y generó correctamente la Orden de Transformación. Para el ' . $req->codigo
                    );
                }
            } else {
                $arrayRspta = array(
                    'tipo' => 'warning',
                    'mensaje' => 'No existe el requerimiento.'
                );
            }

            DB::commit();
            return $arrayRspta;
        } catch (\PDOException $e) {
            DB::rollBack();
            return array('tipo' => 'error', 'mensaje' => 'Hubo un problema al enviar la orden. Por favor intente de nuevo', 'error' => $e->getMessage());
        }
    }

    public function pasarProgramadasAlDiaSiguiente($fecha)
    {
        try {
            DB::beginTransaction();

            $nueva_fecha = (new Carbon($fecha))->addDay();

            $programados = DB::table('almacen.orden_despacho')
                ->select('orden_despacho.id_od')
                ->where([
                    ['orden_despacho.aplica_cambios', '=', true],
                    ['orden_despacho.estado', '=', 1],
                    ['orden_despacho.fecha_despacho', '=', $fecha],
                ])
                ->orderBy('orden_despacho.nro_orden')
                ->get();

            foreach ($programados as $od) {
                DB::table('almacen.orden_despacho')
                    ->where('id_od', $od->id_od)
                    ->update(['fecha_despacho' => $nueva_fecha]);
            }
            DB::commit();
            return array('tipo' => 'success', 'mensaje' => 'Se pasaron los despachos programados para mañana.');
        } catch (\PDOException $e) {
            DB::rollBack();
            return array('tipo' => 'error', 'mensaje' => 'Hubo un problema al enviar la orden. Por favor intente de nuevo', 'error' => $e->getMessage());
        }
    }
    public function listarDespachosInternos($fecha)
    {
        $listaProgramados = DB::table('almacen.orden_despacho')
            ->select(
                'orden_despacho.id_od',
                'orden_despacho.estado',
                'transformacion.id_transformacion',
                'oportunidades.codigo_oportunidad',
                'orden_despacho.nro_orden',
                'oc_propias_view.nombre_entidad'
            )
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
            ->join('almacen.transformacion', 'transformacion.id_od', '=', 'orden_despacho.id_od')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->where([
                ['orden_despacho.aplica_cambios', '=', true],
                ['orden_despacho.estado', '=', 1],
                ['orden_despacho.fecha_despacho', '=', $fecha],
            ])
            ->orderBy('orden_despacho.nro_orden')
            ->get();

        $listaPendientes = DB::table('almacen.orden_despacho')
            ->select(
                'orden_despacho.id_od',
                'orden_despacho.estado',
                'transformacion.id_transformacion',
                'oportunidades.codigo_oportunidad',
                'oc_propias_view.nombre_entidad'
            )
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
            ->join('almacen.transformacion', 'transformacion.id_od', '=', 'orden_despacho.id_od')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->where([
                ['orden_despacho.aplica_cambios', '=', true],
                ['orden_despacho.estado', '=', 21],
                ['orden_despacho.fecha_despacho', '=', $fecha],
            ])
            ->orderBy('orden_despacho.nro_orden')
            ->get();

        $listaProceso = DB::table('almacen.orden_despacho')
            ->select(
                'orden_despacho.id_od',
                'orden_despacho.estado',
                'transformacion.id_transformacion',
                'oportunidades.codigo_oportunidad',
                'oc_propias_view.nombre_entidad'
            )
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
            ->join('almacen.transformacion', 'transformacion.id_od', '=', 'orden_despacho.id_od')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->where([
                ['orden_despacho.aplica_cambios', '=', true],
                ['orden_despacho.estado', '=', 24],
                ['orden_despacho.fecha_despacho', '=', $fecha],
            ])
            ->orderBy('orden_despacho.nro_orden')
            ->get();

        $listaFinalizadas = DB::table('almacen.orden_despacho')
            ->select(
                'orden_despacho.id_od',
                'orden_despacho.estado',
                'transformacion.id_transformacion',
                'oportunidades.codigo_oportunidad',
                'oc_propias_view.nombre_entidad'
            )
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
            ->join('almacen.transformacion', 'transformacion.id_od', '=', 'orden_despacho.id_od')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->where([
                ['orden_despacho.aplica_cambios', '=', true],
                ['orden_despacho.estado', '=', 10],
                ['orden_despacho.fecha_despacho', '=', $fecha],
            ])
            ->orderBy('orden_despacho.nro_orden')
            ->get();

        return response()->json([
            'listaProgramados' => $listaProgramados,
            'listaPendientes' => $listaPendientes,
            'listaProceso' => $listaProceso,
            'listaFinalizadas' => $listaFinalizadas,
        ]);
    }

    public function subirPrioridad($id_od)
    {
        try {
            DB::beginTransaction();

            $od = DB::table('almacen.orden_despacho')
                ->where('id_od', $id_od)
                ->first();
            $arrayRspta = [];

            if ($od->nro_orden > 1) {
                $nuevo_orden = intval($od->nro_orden) - 1;

                $od_anterior = DB::table('almacen.orden_despacho')
                    ->where([
                        ['fecha_despacho', '=', $od->fecha_despacho],
                        ['aplica_cambios', '=', true],
                        ['estado', '=', 1],
                        ['nro_orden', '=', $nuevo_orden]
                    ])
                    ->first();

                if ($od_anterior !== null) {
                    DB::table('almacen.orden_despacho')
                        ->where('id_od', $id_od)
                        ->update(['nro_orden' => $nuevo_orden]);

                    DB::table('almacen.orden_despacho')
                        ->where('id_od', $od_anterior->id_od)
                        ->update(['nro_orden' => (intval($od_anterior->nro_orden) + 1)]);
                }
                $arrayRspta = array('tipo' => 'success', 'mensaje' => 'Se subió correctamente prioridad.');
            } else {
                $arrayRspta = array('tipo' => 'warning', 'mensaje' => 'No hay mas para subir.');
            }

            DB::commit();
            return $arrayRspta;
        } catch (\PDOException $e) {
            DB::rollBack();
            return array('tipo' => 'error', 'mensaje' => 'Hubo un problema al enviar la orden. Por favor intente de nuevo.', 'error' => $e->getMessage());
        }
    }

    public function bajarPrioridad($id_od)
    {
        try {
            DB::beginTransaction();

            $od = DB::table('almacen.orden_despacho')
                ->where('id_od', $id_od)
                ->first();
            $arrayRspta = [];
            $nuevo_orden = intval($od->nro_orden) + 1;

            $od_superior = DB::table('almacen.orden_despacho')
                ->where([
                    ['fecha_despacho', '=', $od->fecha_despacho],
                    ['aplica_cambios', '=', true],
                    ['estado', '=', 1],
                    ['nro_orden', '=', $nuevo_orden]
                ])
                ->first();

            if ($od_superior !== null) {

                DB::table('almacen.orden_despacho')
                    ->where('id_od', $id_od)
                    ->update(['nro_orden' => $nuevo_orden]);

                DB::table('almacen.orden_despacho')
                    ->where('id_od', $od_superior->id_od)
                    ->update(['nro_orden' => (intval($od_superior->nro_orden) - 1)]);

                $arrayRspta = array('tipo' => 'success', 'mensaje' => 'Se bajó correctamente prioridad.');
            } else {
                $arrayRspta = array('tipo' => 'warning', 'mensaje' => 'No hay mas para bajar.');
            }

            DB::commit();
            return $arrayRspta;
        } catch (\PDOException $e) {
            DB::rollBack();
            return array('tipo' => 'error', 'mensaje' => 'Hubo un problema al enviar la orden. Por favor intente de nuevo.', 'error' => $e->getMessage());
        }
    }

    public function cambiaEstado(Request $request)
    {
        try {
            DB::beginTransaction();

            DB::table('almacen.orden_despacho')
                ->where('id_od', $request->id_od)
                ->update(['estado' => $request->estado]);

            $od = DB::table('almacen.orden_despacho')
                ->select('orden_despacho.id_requerimiento', 'orden_despacho.fecha_despacho')
                ->where('id_od', $request->id_od)->first();

            //actualiza datos de transformacion
            if ($request->estado == 24) {
                DB::table('almacen.transformacion')
                    ->where('id_transformacion', $request->id_transformacion)
                    ->update([
                        'estado' => $request->estado,
                        'fecha_inicio' => new Carbon()
                    ]);
            } else if ($request->estado == 10) {
                DB::table('almacen.transformacion')
                    ->where('id_transformacion', $request->id_transformacion)
                    ->update([
                        'estado' => $request->estado,
                        'fecha_transformacion' => new Carbon()
                    ]);
            } else if ($request->estado == 21) {
                DB::table('almacen.transformacion')
                    ->where('id_transformacion', $request->id_transformacion)
                    ->update(['estado' => $request->estado]);

                $this->actualizaNroOrden($od->fecha_despacho);
            } else if ($request->estado == 1) {
                DB::table('almacen.transformacion')
                    ->where('id_transformacion', $request->id_transformacion)
                    ->update(['estado' => $request->estado]);

                $this->actualizaNroOrden($od->fecha_despacho);
            }

            //actualiza estado del requerimiento
            if ($request->estado == 10) {
                DB::table('almacen.alm_req')
                    ->where('id_requerimiento', $od->id_requerimiento)
                    ->update(['estado' => $request->estado]);
            } else {
                DB::table('almacen.alm_req')
                    ->where('id_requerimiento', $od->id_requerimiento)
                    ->update(['estado' => 22]);
            }
            DB::commit();
            return array('tipo' => 'success', 'mensaje' => 'Se actualizó correctamente el estado.');
        } catch (\PDOException $e) {
            DB::rollBack();
            return array('tipo' => 'error', 'mensaje' => 'Hubo un problema al enviar la orden. Por favor intente de nuevo.', 'error' => $e->getMessage());
        }
    }

    private function actualizaNroOrden($fecha_despacho)
    {
        $despachos = DB::table('almacen.orden_despacho')
            ->where([
                ['fecha_despacho', '=', $fecha_despacho],
                ['aplica_cambios', '=', true],
                ['estado', '=', 1]
            ])
            ->orderBy('nro_orden')->get();
        $i = 1;
        foreach ($despachos as $d) {
            DB::table('almacen.orden_despacho')
                ->where('id_od', $d->id_od)
                ->update(['nro_orden' => $i]);
            $i++;
        }
    }

    public function transformacion_nextId($fecha, $id_almacen)
    {
        $yyyy = date('Y', strtotime($fecha));

        $almacen = DB::table('almacen.alm_almacen')
            ->select('codigo')
            ->where('id_almacen', $id_almacen)
            ->first();

        $cantidad = DB::table('almacen.transformacion')
            ->where([['id_almacen', '=', $id_almacen], ['estado', '!=', 7]])
            ->whereYear('fecha_transformacion', '=', $yyyy)
            ->get()->count();

        $val = $this->leftZero(3, ($cantidad + 1));
        $nextId = "OT-" . $almacen->codigo . "-" . $val;

        return $nextId;
    }

    public function leftZero($lenght, $number)
    {
        $nLen = strlen($number);
        $zeros = '';
        for ($i = 0; $i < ($lenght - $nLen); $i++) {
            $zeros = $zeros . '0';
        }
        return $zeros . $number;
    }
}
