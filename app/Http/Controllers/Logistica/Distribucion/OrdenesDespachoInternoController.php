<?php

namespace App\Http\Controllers\Logistica\Distribucion;

use App\Helpers\NotificacionHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Configuracion\AccesosUsuarios;
use App\Models\Configuracion\Usuario;
use App\Models\Distribucion\OrdenDespacho;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use App\Models\mgcp\Oportunidad\Oportunidad;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Debugbar;

class OrdenesDespachoInternoController extends Controller
{
    function view_ordenes_despacho_interno()
    {
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
        return view('almacen/distribucion/ordenesDespachoInterno',compact('array_accesos'));
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

    public function anularDespachoInterno(Request $request)
    {
        try {
            DB::beginTransaction();
            $usuario = Auth::user()->id_usuario;
            $rspta = [];

            $salidas = DB::table('almacen.guia_ven_det')
                ->leftJoin('almacen.orden_despacho_det', function ($join) {
                    $join->on('guia_ven_det.id_od_det', '=', 'orden_despacho_det.id_od_detalle');
                    $join->where('guia_ven_det.estado', '!=', 7);
                })
                ->where('orden_despacho_det.id_od', $request->id_od)
                ->get()->count();

            if ($salidas > 0) {
                $rspta = [
                    'tipo' => 'warning',
                    'mensaje' => 'Almacén ya generó salida de dichos productos.'
                ];
            } else {
                DB::table('almacen.orden_despacho')
                    ->where('id_od', $request->id_od)
                    ->update([
                        'estado' => 7,
                        'usuario_anula' => $usuario,
                        'fecha_anulacion' => new Carbon(),
                    ]);

                DB::table('almacen.transformacion')
                    ->where('id_od', $request->id_od)
                    ->update([
                        'estado' => 7,
                        'usuario_anula' => $usuario,
                        'fecha_anulacion' => new Carbon(),
                    ]);

                DB::table('almacen.alm_req')
                    ->where('id_requerimiento', $request->id_requerimiento)
                    ->update(['estado_despacho' => 2]); //aprobado

                DB::commit();
                $rspta = [
                    'tipo' => 'success',
                    'mensaje' => 'El Despacho Interno ha sido anulado.'
                ];
            }

            return response()->json($rspta);
        } catch (\PDOException $e) {
            DB::rollBack();
            return array('tipo' => 'error', 'mensaje' => 'Hubo un problema al enviar la orden. Por favor intente de nuevo', 'error' => $e->getMessage());
        }
    }

    public function generarDespachoInterno(Request $request)
    {
        try {
            DB::beginTransaction();
            $idOd=0;
            $req = DB::table('almacen.alm_req')
                ->select(
                    'alm_req.*',
                    'despachoInterno.codigo as codigoDespachoInterno',
                    'despachoInterno.id_od',
                    'oportunidades.codigo_oportunidad',
                    'adm_contri.razon_social as razon_social_contribuyente',
                    'sis_sede.descripcion as empreas_sede',
                    'cc.id_oportunidad'
                )
                ->where('alm_req.id_requerimiento', $request->id_requerimiento)
                ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
                ->leftJoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
                ->leftJoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
                ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
                ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
                ->leftJoin('almacen.orden_despacho as despachoInterno', function ($join) {
                    $join->on('despachoInterno.id_requerimiento', '=', 'alm_req.id_requerimiento');
                    $join->where('despachoInterno.aplica_cambios', '=', true);
                    $join->where('despachoInterno.estado', '!=', 7);
                })
                ->first();

            if ($req !== null) {
                $idOd=$req->id_od;
                if ($req->codigoDespachoInterno !== null) {
                    DB::table('almacen.orden_despacho')
                        ->where('id_od', $req->id_od)
                        ->update([
                            'fecha_despacho' => $request->fecha_despacho,
                            'fecha_documento' => $request->fecha_documento,
                            'comentario' => trim($request->comentario),
                        ]);
                    $arrayRspta = array(
                        'tipo' => 'success',
                        'mensaje' => 'Se actualizó la Orden de Despacho Interno ' . $req->codigoDespachoInterno
                    );
                } else {
                    $codigo = OrdenDespacho::ODnextId($req->id_almacen, true, 0, $request->fecha_documento); //$this->ODnextId(date('Y-m-d'), $req->id_almacen, true);
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
                                'id_cliente' => $req->id_cliente,
                                'codigo' => $codigo,
                                'fecha_despacho' => $request->fecha_despacho,
                                'fecha_documento' => $request->fecha_documento,
                                'comentario' => trim($request->comentario),
                                'nro_orden' => ($nro_orden + 1),
                                'aplica_cambios' => true,
                                'registrado_por' => $usuario,
                                'fecha_registro' => $fechaRegistro,
                                'estado' => 1,
                            ],
                            'id_od'
                        );

                    $idOd=$id_od;

                    //Agrega accion en requerimiento
                    DB::table('almacen.alm_req_obs')
                        ->insert([
                            'id_requerimiento' => $req->id_requerimiento,
                            'accion' => 'DESPACHO INTERNO',
                            'descripcion' => 'Se generó la Orden de Despacho Interna ' . $codigo,
                            'id_usuario' => $usuario,
                            'fecha_registro' => $fechaRegistro
                        ]);

                    $codTrans = $this->transformacion_nextId($request->fecha_documento, $req->id_empresa);

                    $id_transformacion = DB::table('almacen.transformacion')
                        ->insertGetId(
                            [
                                'codigo' => $codTrans,
                                'tipo' => "OT",
                                'id_od' => $id_od,
                                'id_cc' => $req->id_cc,
                                'id_moneda' => 1,
                                'fecha_documento' => $request->fecha_documento,
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
                            ['id_tipo_item', '=', 1],
                            ['estado', '!=', 7]
                        ])
                        ->get();

                    foreach ($detalles as $i) {

                        if ($i->tiene_transformacion && $i->entrega_cliente) {

                            $id_od_detalle = DB::table('almacen.orden_despacho_det')
                                ->insertGetId(
                                    [
                                        'id_od' => $id_od,
                                        'id_detalle_requerimiento' => $i->id_detalle_requerimiento,
                                        'cantidad' => $i->cantidad,
                                        'transformado' => $i->tiene_transformacion,
                                        'estado' => 1,
                                        'fecha_registro' => $fechaRegistro
                                    ],
                                    'id_od_detalle'
                                );

                            DB::table('almacen.transfor_transformado')
                                ->insert([
                                    'id_transformacion' => $id_transformacion,
                                    'id_od_detalle' => $id_od_detalle,
                                    'cantidad' => $i->cantidad,
                                    'valor_unitario' => 0,
                                    'valor_total' => 0,
                                    'estado' => 1,
                                    'fecha_registro' => $fechaRegistro
                                ]);
                        } else if ($i->tiene_transformacion == false && $i->entrega_cliente == false) {

                            $id_od_detalle = DB::table('almacen.orden_despacho_det')
                                ->insertGetId(
                                    [
                                        'id_od' => $id_od,
                                        'id_detalle_requerimiento' => $i->id_detalle_requerimiento,
                                        'cantidad' => $i->cantidad,
                                        'transformado' => $i->tiene_transformacion,
                                        'estado' => 1,
                                        'fecha_registro' => $fechaRegistro
                                    ],
                                    'id_od_detalle'
                                );

                            DB::table('almacen.transfor_materia')
                                ->insert([
                                    'id_transformacion' => $id_transformacion,
                                    'cantidad' => $i->cantidad,
                                    'id_od_detalle' => $id_od_detalle,
                                    'valor_unitario' => 0, //($val / $i->cantidad),
                                    'valor_total' => 0,
                                    'estado' => 1,
                                    'fecha_registro' => $fechaRegistro
                                ]);
                        }

                        // DB::table('almacen.alm_det_req')
                        //     ->where('id_detalle_requerimiento', $i->id_detalle_requerimiento)
                        //     ->update(['estado' => 22]); //despacho interno

                    }

                    DB::table('almacen.alm_req')
                        ->where('id_requerimiento', $request->id_requerimiento)
                        ->update(['estado_despacho' => 22]); //despacho interno

                    $arrayRspta = array(
                        'tipo' => 'success',
                        'mensaje' => 'Se generó correctamente la Orden de Transformación. Para el ' . $req->codigo . ' ' . ($req->codigo_oportunidad !== null ? $req->codigo_oportunidad : '')
                    );
                }

                $idUsuarios=[];
                if (config('app.debug')) {
                    $correos[] = config('global.correoDebug1');
                    $idUsuarios[]=Auth::user()->id_usuario;
                } else {
                    $idUsuarios = Usuario::getAllIdUsuariosPorRol(26);
                }
                //$orden_despacho = OrdenDespacho::where('id_requerimiento', $request->id_requerimiento)->first();
                $idOd= isset($id_od) && $id_od>0?$id_od:(isset($req->id_od) && $req->id_od > 0? $req->id_od:0);
               // Debugbar::info($idOd);
                if($idOd>0){
                    $orden_despacho = OrdenDespacho::find($idOd);
                    if(intval($req->id_oportunidad)>0){
                        $cuadro = CuadroCosto::where('id_oportunidad', $req->id_oportunidad)->first();
                        $oportunidad = Oportunidad::find($cuadro->id_oportunidad);
                        $codigoOportunidad=$oportunidad->codigo_oportunidad;
                    }else{
                        $codigoOportunidad=null;
                    }
                    NotificacionHelper::notificacionODI(
                        $idUsuarios,
                        $orden_despacho->codigo,
                        $orden_despacho->fecha_despacho,
                        $codigoOportunidad,
                        $req,
                        trim($request->comentario)
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
                'orden_despacho.comentario',
                'transformacion.id_transformacion',
                'transformacion.codigo as codigo_transformacion',
                'oportunidades.id as id_oportunidad',
                'oportunidades.codigo_oportunidad',
                'alm_req.codigo as codigo_req',
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
                'orden_despacho.comentario',
                'orden_despacho.fecha_despacho',
                'transformacion.id_transformacion',
                'transformacion.codigo as codigo_transformacion',
                'oportunidades.id as id_oportunidad',
                'oportunidades.codigo_oportunidad',
                'alm_req.codigo as codigo_req',
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
                // ['orden_despacho.fecha_despacho', '=', $fecha],
            ])
            ->orderBy('orden_despacho.fecha_despacho')
            ->orderBy('orden_despacho.nro_orden')
            ->get();

        $listaProceso = DB::table('almacen.orden_despacho')
            ->select(
                'orden_despacho.id_od',
                'orden_despacho.estado',
                'orden_despacho.comentario',
                'orden_despacho.fecha_despacho',
                'transformacion.id_transformacion',
                'transformacion.codigo as codigo_transformacion',
                'oportunidades.id as id_oportunidad',
                'oportunidades.codigo_oportunidad',
                'alm_req.codigo as codigo_req',
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
                // ['orden_despacho.fecha_despacho', '=', $fecha],
            ])
            ->orderBy('orden_despacho.fecha_despacho')
            ->orderBy('orden_despacho.nro_orden')
            ->get();

        $listaFinalizadas = DB::table('almacen.orden_despacho')
            ->select(
                'orden_despacho.id_od',
                'orden_despacho.estado',
                'orden_despacho.comentario',
                'transformacion.id_transformacion',
                'transformacion.codigo as codigo_transformacion',
                'oportunidades.id as id_oportunidad',
                'oportunidades.codigo_oportunidad',
                'alm_req.codigo as codigo_req',
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
                // ['transformacion.fecha_transformacion', '=', $fecha],
            ])
            ->whereDate('transformacion.fecha_transformacion', $fecha)
            ->orderBy('orden_despacho.nro_orden')
            ->get();

        return response()->json([
            'listaProgramados' => $listaProgramados,
            'listaPendientes' => $listaPendientes,
            'listaProceso' => $listaProceso,
            'listaFinalizadas' => $listaFinalizadas,
        ]);
    }

    public function listarPendientesAnteriores($fecha)
    {
        $lista = DB::table('almacen.orden_despacho')
            ->select(
                'orden_despacho.id_od',
                'orden_despacho.fecha_despacho',
                'orden_despacho.estado',
                'transformacion.id_transformacion',
                'oportunidades.id as id_oportunidad',
                'oportunidades.codigo_oportunidad',
                'orden_despacho.nro_orden',
                'alm_req.codigo as codigo_req',
                'oc_propias_view.nombre_entidad'
            )
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
            ->join('almacen.transformacion', 'transformacion.id_od', '=', 'orden_despacho.id_od')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->where([
                ['orden_despacho.aplica_cambios', '=', true],
                ['orden_despacho.fecha_despacho', '<', $fecha],
                ['orden_despacho.estado', '=', 1],
            ])
            // ->whereIn('orden_despacho.estado', [1, 21, 24])
            ->orderBy('orden_despacho.fecha_despacho', 'desc')
            ->get();
        $output['data'] = $lista;
        return response()->json($output);
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
            if ($request->estado == 24) { //iniciado
                DB::table('almacen.transformacion')
                    ->where('id_transformacion', $request->id_transformacion)
                    ->update([
                        'estado' => $request->estado,
                        'fecha_inicio' => new Carbon()
                    ]);
            } else if ($request->estado == 10) { //finalizado
                $usuario = Auth::user()->id_usuario;
                DB::table('almacen.transformacion')
                    ->where('id_transformacion', $request->id_transformacion)
                    ->update([
                        'estado' => $request->estado,
                        'responsable' => $usuario,
                        'fecha_transformacion' => new Carbon()
                    ]);

                DB::table('almacen.orden_despacho')
                    ->where('id_od', $request->id_od)
                    ->update([
                        'estado' => 10, //Culminado
                    ]);
            } else if ($request->estado == 21) { //entregado
                DB::table('almacen.transformacion')
                    ->where('id_transformacion', $request->id_transformacion)
                    ->update([
                        'fecha_entrega' => new Carbon(),
                        'estado' => $request->estado,
                    ]);

                $this->actualizaNroOrden($od->fecha_despacho);
            } else if ($request->estado == 1) { //elaborado
                DB::table('almacen.transformacion')
                    ->where('id_transformacion', $request->id_transformacion)
                    ->update(['estado' => $request->estado]);

                $this->actualizaNroOrden($od->fecha_despacho);
            }

            //actualiza estado del requerimiento
            if ($request->estado == 10) { //finalizado
                DB::table('almacen.alm_req')
                    ->where('id_requerimiento', $od->id_requerimiento)
                    ->update(['estado_despacho' => $request->estado]);
            } else { //despacho interno
                DB::table('almacen.alm_req')
                    ->where('id_requerimiento', $od->id_requerimiento)
                    ->update(['estado_despacho' => 22]);
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

    public function transformacion_nextId($fecha, $id_empresa)
    {
        $yyyy = date('Y', strtotime($fecha));
        $yy = date('y', strtotime($fecha));

        $empresa = DB::table('administracion.adm_empresa')
            ->select('codigo')
            ->where('id_empresa', $id_empresa)
            ->first();

        $cantidad = DB::table('almacen.transformacion')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'transformacion.id_almacen')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->where([
                ['adm_empresa.id_empresa', '=', $id_empresa],
                ['transformacion.estado', '!=', 7]
            ])
            ->whereYear('transformacion.fecha_registro', '=', $yyyy)
            ->get()->count();

        $val = $this->leftZero(4, ($cantidad + 1));
        $nextId = "OT-" . $empresa->codigo . "-" . $yy . "-" . $val;

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
