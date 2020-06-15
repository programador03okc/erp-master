<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Dompdf\Dompdf;
use PDF;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
date_default_timezone_set('America/Lima');

class DistribucionController extends Controller
{
    public function __construct(){
        // session_start();
    }
    function view_ordenesDespacho(){
        $usuarios = AlmacenController::select_usuarios();
        $sis_identidad = AlmacenController::sis_identidad_cbo();
        return view('almacen/distribucion/ordenesDespacho', compact('usuarios','sis_identidad'));
    }
    function view_despachosPendientes(){
        $tp_operacion = AlmacenController::tp_operacion_cbo_sal();
        $clasificaciones = AlmacenController::mostrar_guia_clas_cbo();
        $usuarios = AlmacenController::select_usuarios();
        return view('almacen/guias/despachosPendientes', compact('tp_operacion','clasificaciones','usuarios'));
    }
    function view_grupoDespachos(){
        // $usuarios = AlmacenController::select_usuarios();
        return view('almacen/distribucion/grupoDespachos');
    }

    public function listarRequerimientosPendientes(Request $request){
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*','sis_usua.nombre_corto as responsable','adm_grupo.descripcion as grupo',
            'adm_grupo.id_sede','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            'log_ord_compra.codigo as codigo_orden','guia_com.serie','guia_com.numero',
            'trans.id_transferencia','trans.codigo as codigo_transferencia','ubi_dis.descripcion as ubigeo_descripcion',
            'rrhh_perso.nro_documento as dni_persona','alm_almacen.descripcion as almacen_descripcion',
            'alm_almacen.id_sede as sede_almacen','orden_despacho.id_od','orden_despacho.codigo as codigo_od',
            'alm_tp_req.descripcion as tipo_req',
            DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
            'adm_contri.nro_documento as cliente_ruc','adm_contri.razon_social as cliente_razon_social')
            ->join('almacen.alm_tp_req','alm_tp_req.id_tipo_requerimiento','=','alm_req.id_tipo_requerimiento')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->leftjoin('administracion.adm_grupo','adm_grupo.id_grupo','=','alm_req.id_grupo')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
            ->leftJoin('logistica.log_ord_compra','log_ord_compra.id_requerimiento','=','alm_req.id_requerimiento')
            ->leftJoin('almacen.guia_com','guia_com.id_oc','=','log_ord_compra.id_orden_compra')
            ->leftJoin('almacen.mov_alm','mov_alm.id_guia_com','=','guia_com.id_guia')
            ->leftJoin('almacen.guia_ven','guia_ven.id_guia_com','=','guia_com.id_guia')
            ->leftJoin('almacen.trans','trans.id_guia_ven','=','guia_ven.id_guia_ven')
            ->leftJoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_req.id_almacen')
            ->leftJoin('configuracion.ubi_dis','ubi_dis.id_dis','=','alm_req.id_ubigeo_entrega')
            ->leftJoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','alm_req.id_persona')
            ->leftJoin('comercial.com_cliente','com_cliente.id_cliente','=','alm_req.id_cliente')
            ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->leftJoin('almacen.orden_despacho','orden_despacho.id_requerimiento','=','alm_req.id_requerimiento')
            ->where([['alm_req.estado','!=',1], ['alm_req.estado','!=',7]])//muestra todos los reservados
            ->get();
        return datatables($data)->toJson();
        // return response()->json($data);
    }

    public function verDetalleRequerimiento($id_requerimiento){
        $detalles = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.*','alm_almacen.descripcion as almacen_descripcion',
                    'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
                    'alm_prod.descripcion as producto_descripcion','alm_prod.codigo as producto_codigo',
                    'alm_und_medida.abreviatura')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
            // ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            ->leftJoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_det_req.id_almacen')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_det_req.estado')
            ->where([['alm_det_req.id_requerimiento','=',$id_requerimiento],['alm_det_req.estado','!=',7]])
            ->get();
        return response()->json($detalles);
    }

    public function verDetalleIngreso($id_requerimiento){
        $data = DB::table('almacen.mov_alm_det')
        ->select('mov_alm_det.*','alm_prod.codigo as codigo_producto',
        'alm_prod.descripcion as descripcion_producto','alm_und_medida.abreviatura as unidad_producto')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
        ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
        ->join('almacen.guia_com','guia_com.id_guia','=','mov_alm.id_guia_com')
        ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','guia_com.id_oc')
        ->where([['log_ord_compra.id_requerimiento','=',$id_requerimiento]])
        ->get();
        return response()->json($data);
    }

    public function guardar_orden_despacho(Request $request){

        try {
            DB::beginTransaction();

            $codigo = $this->ODnextId($request->fecha_despacho,$request->id_sede);
            $usuario = Auth::user()->id_usuario;

            $id_od = DB::table('almacen.orden_despacho')
                ->insertGetId([
                    'id_sede'=>$request->id_sede,
                    'id_requerimiento'=>$request->id_requerimiento,
                    'id_cliente'=>$request->id_cliente,
                    'id_persona'=>$request->id_persona,
                    'id_almacen'=>$request->id_almacen,
                    'codigo'=>$codigo,
                    'ubigeo_destino'=>$request->ubigeo,
                    'direccion_destino'=>$request->direccion_destino,
                    'fecha_despacho'=>$request->fecha_despacho,
                    'fecha_entrega'=>$request->fecha_entrega,
                    'aplica_cambios'=>($request->aplica_cambios_valor == 'si' ? true : false),
                    'registrado_por'=>$usuario,
                    'tipo_entrega'=>$request->tipo_entrega,
                    'fecha_registro'=>date('Y-m-d H:i:s'),
                    'estado'=>1,
                    'tipo_cliente'=>$request->tipo_cliente
                ],
                    'id_od'
            );

            if ($request->aplica_cambios_valor == 'si'){
                $fecha_actual = date('Y-m-d');
                $codTrans = $this->transformacion_nextId($fecha_actual);

                $id_transformacion = DB::table('almacen.transformacion')
                    ->insertGetId([
                        'fecha_transformacion'=>$fecha_actual,
                        'codigo'=>$codTrans,
                        // 'responsable'=>$usuario,
                        'id_moneda'=>1,
                        'id_almacen'=>$request->id_almacen,
                        'total_materias'=>0,
                        'total_directos'=>0,
                        'costo_primo'=>0,
                        'total_indirectos'=>0,
                        'total_sobrantes'=>0,
                        'costo_transformacion'=>0,
                        'registrado_por'=>$usuario,
                        'tipo_cambio'=>1,
                        'fecha_registro'=>date('Y-m-d H:i:s'),
                        'estado'=>1,
                        'observacion'=>'SALE: '.$request->sale
                    ],
                        'id_transformacion'
                );

                
                $data = json_decode($request->detalle_ingresa);
                
                foreach ($data as $d) {
                    DB::table('almacen.transfor_materia')
                    ->insert([
                        'id_transformacion'=>$id_transformacion,
                        'id_producto'=>$d->id_producto,
                        // 'id_posicion'=>$d->id_posicion,
                        'cantidad'=>$d->cantidad,
                        'valor_unitario'=>0,
                        'valor_total'=>0,
                        'estado'=>1,
                        'fecha_registro'=>date('Y-m-d H:i:s')
                    ]);
                }
            }
            else {
                $data = json_decode($request->detalle_requerimiento);
                
                foreach ($data as $d) {
                    DB::table('almacen.orden_despacho_det')
                    ->insert([
                        'id_od'=>$id_od,
                        'id_producto'=>$d->id_producto,
                        // 'id_posicion'=>($d->id_posicion),
                        'cantidad'=>$d->cantidad,
                        'descripcion_producto'=>($d->producto_descripcion !== null ? $d->producto_descripcion : $d->descripcion_adicional),
                        'estado'=>1,
                        'fecha_registro'=>date('Y-m-d H:i:s')
                    ]);
                }
            }
            DB::commit();
            return response()->json($id_od);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function listarOrdenesDespachoPendientes(Request $request){
        $data = DB::table('almacen.orden_despacho')
        ->select('orden_despacho.*','adm_contri.nro_documento','adm_contri.razon_social',
        'alm_req.codigo as codigo_req','alm_req.concepto','ubi_dis.descripcion as ubigeo_descripcion',
        'sis_usua.nombre_corto','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
        DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
        'alm_almacen.descripcion as almacen_descripcion')
        ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','orden_despacho.id_cliente')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
        ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','orden_despacho.id_persona')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','orden_despacho.id_almacen')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->join('configuracion.ubi_dis','ubi_dis.id_dis','=','orden_despacho.ubigeo_destino')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','orden_despacho.registrado_por')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','orden_despacho.estado')
        ->where('orden_despacho.estado',1)
        ->get();
        return datatables($data)->toJson();
    }

    public function listarOrdenesDespacho(Request $request){
        $data = DB::table('almacen.orden_despacho')
        ->select('orden_despacho.*','adm_contri.nro_documento','adm_contri.razon_social',
        'alm_req.codigo as codigo_req','alm_req.concepto','ubi_dis.descripcion as ubigeo_descripcion',
        'sis_usua.nombre_corto','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
        DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
        'alm_almacen.descripcion as almacen_descripcion')
        ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','orden_despacho.id_cliente')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
        ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','orden_despacho.id_persona')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','orden_despacho.id_almacen')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->join('configuracion.ubi_dis','ubi_dis.id_dis','=','orden_despacho.ubigeo_destino')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','orden_despacho.registrado_por')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','orden_despacho.estado')
        ->where('orden_despacho.estado',9)
        ->get();
        return datatables($data)->toJson();
    }

    public function verDetalleDespacho($id_od){
        $data = DB::table('almacen.orden_despacho_det')
        ->select('orden_despacho_det.*','alm_prod.codigo','alm_prod.descripcion',
        'alm_ubi_posicion.codigo as posicion','alm_und_medida.abreviatura')
        ->leftJoin('almacen.alm_prod','alm_prod.id_producto','=','orden_despacho_det.id_producto')
        ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
        ->leftJoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','orden_despacho_det.id_posicion')
        ->where([['orden_despacho_det.id_od','=',$id_od],['orden_despacho_det.estado','!=',7]])
        ->get();
        return response()->json($data);
    }

    public function guardar_grupo_despacho(Request $request){

        try {
            DB::beginTransaction();

            $codigo = $this->grupoODnextId($request->fecha_despacho,$request->id_sede);
            $id_usuario = Auth::user()->id_usuario;

            $id_od_grupo = DB::table('almacen.orden_despacho_grupo')
            ->insertGetId([
                'codigo'=>$codigo,
                'id_sede'=>$request->id_sede,
                'fecha_despacho'=>$request->fecha_despacho,
                'responsable'=>$request->responsable,
                'mov_propia'=>($request->mov_propia_valor == 'si' ? true : false),
                'id_proveedor'=>$request->id_proveedor,
                'observaciones'=>$request->observaciones,
                'confirmacion'=>false,
                'registrado_por'=>$id_usuario,
                'estado'=>1,
                'fecha_registro'=>date('Y-m-d H:i:s')
                ],
                'id_od_grupo'
            );
            $data = json_decode($request->ordenes_despacho);
            
            foreach ($data as $d) {
                DB::table('almacen.orden_despacho_grupo_det')
                ->insert([
                    'id_od_grupo'=>$id_od_grupo,
                    'id_od'=>$d->id_od,
                    'estado'=>1,
                    'fecha_registro'=>date('Y-m-d H:i:s')
                ]);
                //actualiza estado despachado
                DB::table('almacen.orden_despacho')
                ->where('id_od',$d->id_od)
                ->update(['estado'=>20]);

                DB::table('almacen.orden_despacho_det')
                ->where('id_od',$d->id_od)
                ->update(['estado'=>20]);

                DB::table('almacen.alm_req')
                ->where('id_requerimiento',$d->id_requerimiento)
                ->update(['estado'=>20]);

                DB::table('almacen.alm_det_req')
                ->where('id_requerimiento',$d->id_requerimiento)
                ->update(['estado'=>20]);

            }
            DB::commit();
            return response()->json($id_od_grupo);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function listarGruposDespachados(Request $request){
        $data = DB::table('almacen.orden_despacho_grupo')
        ->select('orden_despacho_grupo.*','sis_usua.nombre_corto','adm_contri.nro_documento','adm_contri.razon_social',
        'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
        ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','orden_despacho_grupo.id_proveedor')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','orden_despacho_grupo.responsable')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','orden_despacho_grupo.estado')
        ->where([['orden_despacho_grupo.estado','!=',7]])
        ->get();
        return datatables($data)->toJson();
    }

    public function verDetalleGrupoDespacho($id_od_grupo){
        $data = DB::table('almacen.orden_despacho_grupo_det')
        ->select('orden_despacho_grupo_det.*','orden_despacho.codigo','orden_despacho.direccion_destino',
        'orden_despacho.fecha_despacho','orden_despacho.fecha_entrega','adm_contri.nro_documento',
        'adm_contri.razon_social','alm_req.codigo as codigo_req','alm_req.concepto',
        'ubi_dis.descripcion as ubigeo_descripcion','sis_usua.nombre_corto','adm_estado_doc.estado_doc',
        DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
        'adm_estado_doc.bootstrap_color')
        ->join('almacen.orden_despacho','orden_despacho.id_od','=','orden_despacho_grupo_det.id_od')
        ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','orden_despacho.id_cliente')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
        ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','orden_despacho.id_persona')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->join('configuracion.ubi_dis','ubi_dis.id_dis','=','orden_despacho.ubigeo_destino')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','orden_despacho.registrado_por')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','orden_despacho.estado')
        ->where([['orden_despacho_grupo_det.estado','!=',7]])
        ->get();
        return response()->json($data);
    }

    public function guardar_guia_despacho(Request $request){
        try {
            DB::beginTransaction();
            $id_salida = null;

            if ($request->id_od !== null){

                $id_tp_doc_almacen = 2;//Guia Venta
                $id_usuario = Auth::user()->id_usuario;
                $fecha_registro = date('Y-m-d H:i:s');
    
                $od = DB::table('almacen.orden_despacho')
                ->where('id_od',$request->id_od)
                ->first();

                if ($od !== null){
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
                            // 'transportista' => $request->transportista,
                            // 'tra_serie' => $request->tra_serie,
                            // 'tra_numero' => $request->tra_numero,
                            // 'punto_partida' => $request->punto_partida,
                            // 'punto_llegada' => $request->punto_llegada,
                            // 'fecha_traslado' => $request->fecha_traslado,
                            // 'placa' => $request->placa,
                            'usuario' => $request->responsable,
                            'registrado_por' => $id_usuario,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro,
                        ],
                            'id_guia_ven'
                        );

                    //Genero la salida
                    $codigo = AlmacenController::nextMovimiento(2,//salida
                    $request->fecha_almacen,
                    $request->id_almacen);
    
                    $id_salida = DB::table('almacen.mov_alm')->insertGetId(
                        [
                            'id_almacen' => $request->id_almacen,
                            'id_tp_mov' => 2,//Salidas
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

                    $detalle = DB::table('almacen.orden_despacho_det')
                    ->select('orden_despacho_det.*','alm_prod.id_unidad_medida')
                    ->join('almacen.alm_prod','alm_prod.id_producto','=','orden_despacho_det.id_producto')
                    ->where([['orden_despacho_det.id_od','=',$request->id_od],
                            ['orden_despacho_det.estado','!=',7]])
                    ->get();

                    foreach ($detalle as $det) {
                        //guardo los items de la guia ven
                        $id_guia_ven_det = DB::table('almacen.guia_ven_det')->insertGetId([
                            'id_guia_ven' => $id_guia_ven,
                            'id_producto' => $det->id_producto,
                            // 'id_posicion' => $request->id_posicion,
                            'cantidad' => $det->cantidad,
                            'id_unid_med' => $det->id_unidad_medida,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro
                        ],
                            'id_guia_ven_det'
                        );

                        //Guardo los items de la salida
                        $id_det = DB::table('almacen.mov_alm_det')->insertGetId(
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
                        
                        //Actualizo los saldos del producto
                        //Obtengo el registro de saldos
                        $ubi = DB::table('almacen.alm_prod_ubi')
                        ->where([['id_producto','=',$det->id_producto],
                                ['id_almacen','=',$request->id_almacen]])
                        ->first();
                        //Traer stockActual
                        $saldo = AlmacenController::saldo_actual_almacen($det->id_producto, $request->id_almacen);
                        $valor = AlmacenController::valorizacion_almacen($det->id_producto, $request->id_almacen);
                        $cprom = ($saldo > 0 ? $valor/$saldo : 0);
                        //guardo saldos actualizados
                        if ($ubi !== null){//si no existe -> creo la ubicacion
                            DB::table('almacen.alm_prod_ubi')
                            ->where('id_prod_ubi',$ubi->id_prod_ubi)
                            ->update([  'stock' => $saldo,
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
                    //orden de despacho estado   procesado
                    DB::table('almacen.orden_despacho')
                    ->where('id_od',$request->id_od)
                    ->update(['estado'=>9]);
                    //orden de despacho detalle estado   procesado
                    DB::table('almacen.orden_despacho_det')
                    ->where('id_od',$request->id_od)
                    ->update(['estado'=>9]);
                    //requerimiento despachado
                    DB::table('almacen.alm_req')
                    ->where('id_requerimiento',$request->id_requerimiento)
                    ->update(['estado'=>9]);
                    //orden de despacho detalle estado   procesado
                    DB::table('almacen.alm_det_req')
                    ->where('id_requerimiento',$request->id_requerimiento)
                    ->update(['estado'=>9]);
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

    public function listarSalidasDespacho(Request $request){
        $data = DB::table('almacen.mov_alm')
        ->select('mov_alm.*','guia_ven.serie','guia_ven.numero','orden_despacho.codigo as codigo_od',
            DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
            'alm_req.codigo as codigo_requerimiento','adm_contri.razon_social','alm_req.concepto',
            'alm_almacen.descripcion as almacen_descripcion','sis_usua.nombre_corto')
            ->join('almacen.guia_ven','guia_ven.id_guia_ven','=','mov_alm.id_guia_ven')
            ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','guia_ven.id_cliente')
            ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','guia_ven.id_persona')
            ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','guia_ven.id_almacen')
            ->leftjoin('configuracion.sis_usua','sis_usua.id_usuario','=','guia_ven.usuario')
            ->join('almacen.orden_despacho','orden_despacho.id_od','=','guia_ven.id_od')
            ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
            ->get();
        // return response()->json($data);
        return datatables($data)->toJson();
    }

    public function ODnextId($fecha_despacho,$id_sede){
        $yyyy = date('Y',strtotime($fecha_despacho));
        
        $cantidad = DB::table('almacen.orden_despacho')
        ->whereYear('fecha_despacho','=',$yyyy)
        ->where([['id_sede','=',$id_sede],['estado','!=',7]])
        ->get()->count();

        $val = AlmacenController::leftZero(3,($cantidad + 1));
        $nextId = "OD-".$yyyy."-".$val;
        return $nextId;
    }

    public function grupoODnextId($fecha_despacho,$id_sede){
        $yyyy = date('Y',strtotime($fecha_despacho));
        
        $cantidad = DB::table('almacen.orden_despacho_grupo')
        ->whereYear('fecha_despacho','=',$yyyy)
        ->where([['id_sede','=',$id_sede],['estado','!=',7]])
        ->get()->count();

        $val = AlmacenController::leftZero(3,($cantidad + 1));
        $nextId = "D-".$yyyy."-".$val;
        return $nextId;
    }

    public function transformacion_nextId($fecha_transformacion){
        $yyyy = date('Y',strtotime($fecha_transformacion));
        
        $cantidad = DB::table('almacen.transformacion')
        ->whereYear('fecha_transformacion','=',$yyyy)
        ->where([['estado','!=',7]])
        ->get()->count();

        $val = AlmacenController::leftZero(3,($cantidad + 1));
        $nextId = "TF-".$yyyy."-".$val;
        return $nextId;
    }
}
