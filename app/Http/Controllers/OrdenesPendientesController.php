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

class OrdenesPendientesController extends Controller
{
    public function __construct(){
        // session_start();
    }
    function view_ordenesPendientes(){
        $almacenes = AlmacenController::mostrar_almacenes_cbo();
        $tp_doc = AlmacenController::mostrar_tp_doc_cbo();
        $tp_operacion = AlmacenController::tp_operacion_cbo_ing();
        $clasificaciones = AlmacenController::mostrar_guia_clas_cbo();
        $usuarios = AlmacenController::select_usuarios();
        return view('almacen/guias/ordenesPendientes', compact('almacenes','tp_doc','tp_operacion','clasificaciones','usuarios'));
    }

    public function listarOrdenesPendientes(){
        $data = DB::table('logistica.log_ord_compra')
            ->select('log_ord_compra.*','log_ord_compra.codigo as codigo_orden',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color','adm_contri.razon_social',
            'adm_contri.nro_documento','sis_usua.nombre_corto',
            // 'sis_moneda.simbolo',
            'alm_req.codigo as codigo_requerimiento','alm_req.concepto')
            // ->join('administracion.adm_tp_docum','adm_tp_docum.id_tp_documento','=','log_ord_compra.id_tp_documento')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','log_ord_compra.estado')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','log_ord_compra.id_usuario')
            // ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','log_ord_compra.id_moneda')
            ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','log_ord_compra.id_requerimiento')
            ->where([['log_ord_compra.estado','!=',7],
                    ['log_ord_compra.en_almacen','=',false],
                    ['log_ord_compra.id_tp_documento','=',2]])//Orden de Compra
            ->get();
        return datatables($data)->toJson();
        // return response()->json($data);
    }
    
    public function listarOrdenesEntregadas(){
        $data = DB::table('almacen.mov_alm')
            ->select('mov_alm.*','log_ord_compra.id_orden_compra','log_ord_compra.codigo as codigo_orden',
            'adm_contri.nro_documento','adm_contri.razon_social','log_ord_compra.fecha as fecha_orden',
            'alm_req.codigo as codigo_requerimiento','alm_req.concepto','log_ord_compra.id_sede as sede_orden',
            'sis_usua.nombre_corto',
            // 'sis_moneda.simbolo','log_ord_compra.monto_subtotal','log_ord_compra.monto_igv','log_ord_compra.monto_total',
            'alm_almacen.id_sede as sede_almacen',
            'alm_req.id_sede as sede_requerimiento','guia_com.serie','guia_com.numero',
            'alm_req.id_requerimiento','alm_req.estado as estado_requerimiento','guia_ven.id_guia_ven',
            'alm_req.id_tipo_requerimiento','alm_req.id_almacen as almacen_requerimiento')
            ->join('almacen.guia_com','guia_com.id_guia','=','mov_alm.id_guia_com')
            ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','guia_com.id_oc')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','log_ord_compra.id_requerimiento')
            ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_req.id_almacen')
            // ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','log_ord_compra.id_moneda')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','mov_alm.usuario')
            ->leftJoin('almacen.guia_ven','guia_ven.id_guia_com','=','mov_alm.id_guia_com')
            ->where([['mov_alm.estado','!=',7],['mov_alm.id_tp_mov','=',1]])
            ->get();
        return datatables($data)->toJson();
    }

    public function detalleOrden($id_orden){
        $detalle = DB::table('logistica.log_det_ord_compra')
            ->select(
                'log_det_ord_compra.*',
                DB::raw("CONCAT(pers_aut.nombres,' ',pers_aut.apellido_paterno,' ',pers_aut.apellido_materno) as nombre_personal_autorizado"),

                DB::raw("(CASE 
                WHEN alm_item.id_item isNUll THEN alm_det_req.descripcion_adicional 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
                ELSE 'nulo' END) AS descripcion
                "),
                DB::raw("(CASE 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.codigo 
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.codigo 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.codigo 
                ELSE 'nulo' END) AS codigo
                "),
                DB::raw("(CASE 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.abreviatura
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'serv' 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'und' 
                ELSE 'nulo' END) AS unidad_medida
                "),
                'alm_item.id_producto',
                'log_valorizacion_cotizacion.cantidad_cotizada',
                'log_valorizacion_cotizacion.precio_cotizado',
                'log_valorizacion_cotizacion.monto_descuento',
                'log_valorizacion_cotizacion.subtotal'
                // 'alm_det_req.id_item'
            )
            ->leftJoin('configuracion.sis_usua as sis_usua_aut', 'sis_usua_aut.id_usuario', '=', 'log_det_ord_compra.personal_autorizado')
            ->leftJoin('rrhh.rrhh_trab as trab_aut', 'trab_aut.id_trabajador', '=', 'sis_usua_aut.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu as post_aut', 'post_aut.id_postulante', '=', 'trab_aut.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as pers_aut', 'pers_aut.id_persona', '=', 'post_aut.id_persona')

            ->join('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', 'log_det_ord_compra.id_valorizacion_cotizacion')
            ->join('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
            ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'valoriza_coti_detalle.id_detalle_requerimiento')
            ->leftjoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_valorizacion_cotizacion.id_unidad_medida')
            ->leftjoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftjoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
            ->where([
                ['log_det_ord_compra.id_orden_compra', '=', $id_orden]
            ])
            ->get();
        return response()->json($detalle);
    }
    
    public function verGuiasOrden($id_orden){
        $guias = DB::table('almacen.guia_com_oc')
        ->select('guia_com_oc.*','guia_com.serie','guia_com.numero','guia_com.fecha_emision',
        'alm_almacen.descripcion as almacen','tp_ope.descripcion as operacion',
        'responsable.nombre_corto as nombre_responsable','adm_estado_doc.estado_doc',
        'registrado_por.nombre_corto as nombre_registrado_por','adm_estado_doc.bootstrap_color')
        ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_oc.id_guia_com')
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','guia_com.id_almacen')
        ->join('almacen.tp_ope','tp_ope.id_operacion','=','guia_com.id_operacion')
        ->join('configuracion.sis_usua as responsable','responsable.id_usuario','=','guia_com.usuario')
        ->join('configuracion.sis_usua as registrado_por','registrado_por.id_usuario','=','guia_com.registrado_por')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_com.estado')
        ->where([['guia_com_oc.id_oc','=',$id_orden],['guia_com_oc.estado','!=',7]])
        ->get();
        return response()->json($guias);
    }

    public function guardar_guia_com_oc(Request $request){

        try {
            DB::beginTransaction();
            // database queries here
            $id_ingreso = null;
        
            if ($request->id_orden_compra !== null){
                $id_tp_doc_almacen = 1;
                $id_usuario = Auth::user()->id_usuario;
                $fecha_registro = date('Y-m-d H:i:s');
    
                $orden = DB::table('logistica.log_ord_compra')
                ->where('id_orden_compra',$request->id_orden_compra)
                ->first();
        
                if (isset($orden)){
                    //Genero la Guia
                    $id_guia = DB::table('almacen.guia_com')->insertGetId(
                        [
                            'id_tp_doc_almacen' => $id_tp_doc_almacen,
                            'serie' => $request->serie,
                            'numero' => $request->numero,
                            'id_proveedor' => $orden->id_proveedor,
                            'fecha_emision' => $request->fecha_emision,
                            'fecha_almacen' => $request->fecha_almacen,
                            'id_almacen' => $request->id_almacen,
                            'id_guia_clas' => $request->id_guia_clas,
                            'id_operacion' => $request->id_operacion,
                            'id_oc' => $request->id_orden_compra,
                            'usuario' => $id_usuario,
                            'registrado_por' => $id_usuario,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro,
                        ],
                            'id_guia'
                        );
                    // AlmacenController::guardar_oc($id_guia, $request->id_orden_compra);
                    //Genero el ingreso
                    $codigo = AlmacenController::nextMovimiento(1,//ingreso
                    $request->fecha_almacen,
                    $request->id_almacen);
    
                    $id_ingreso = DB::table('almacen.mov_alm')->insertGetId(
                        [
                            'id_almacen' => $request->id_almacen,
                            'id_tp_mov' => 1,//Ingresos
                            'codigo' => $codigo,
                            'fecha_emision' => $request->fecha_almacen,
                            'id_guia_com' => $id_guia,
                            // 'id_doc_com' => (isset($doc) ? $doc->id_doc_com : null),
                            'id_operacion' => $request->id_operacion,
                            'revisado' => 0,
                            'usuario' => $id_usuario,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro,
                        ],
                            'id_mov_alm'
                        );

                    $detalle = DB::table('logistica.log_det_ord_compra')
                    ->select('log_det_ord_compra.*','alm_item.id_producto')
                    // 'log_valorizacion_cotizacion.precio_cotizado',
                    // 'log_valorizacion_cotizacion.id_unidad_medida','log_valorizacion_cotizacion.precio_sin_igv',
                    // 'log_valorizacion_cotizacion.cantidad_cotizada',
                    // 'log_valorizacion_cotizacion.monto_descuento'
                    // ->leftjoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','log_det_ord_compra.id_valorizacion_cotizacion')
                    ->leftjoin('almacen.alm_item','alm_item.id_item','=','log_det_ord_compra.id_item')
                    ->where([['log_det_ord_compra.estado','!=',7],
                             ['log_det_ord_compra.id_orden_compra','=',$request->id_orden_compra]])
                    ->get();
                    
                    foreach ($detalle as $det) {
                        // $posicion = DB::table('almacen.alm_prod_ubi')
                        // ->select('alm_ubi_posicion.id_posicion','alm_ubi_posicion.codigo')
                        // ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
                        // ->where([['alm_prod_ubi.id_producto','=',$det->id_producto],
                        //          ['alm_prod_ubi.id_almacen','=',$request->id_almacen],
                        //          ['alm_prod_ubi.estado','=',1]])
                        // ->orderBy('id_prod_ubi','desc')
                        // ->first();
                        //Guardo los items de la guia
                        $id_guia_com_det = DB::table('almacen.guia_com_det')->insertGetId(
                            [
                                "id_guia_com" => $id_guia,
                                "id_producto" => $det->id_producto,
                                // "id_posicion" => (isset($posicion) ? $posicion->id_posicion : null),
                                // "id_posicion" => $posicion->id_posicion,
                                "cantidad" => $det->cantidad,
                                "id_unid_med" => $det->id_unidad_medida,
                                "usuario" => $id_usuario,
                                "id_oc_det" => $det->id_detalle_orden,
                                "unitario" => $det->precio,
                                "total" => ($det->precio * $det->cantidad),
                                "unitario_adicional" => 0,
                                // "id_guia_ven_det" =>,
                                'estado' => 1,
                                'fecha_registro' => $fecha_registro,
                            ],
                                'id_guia_com_det'
                            );
                        //Guardo los items del ingreso
                        $id_det = DB::table('almacen.mov_alm_det')->insertGetId(
                            [
                                'id_mov_alm' => $id_ingreso,
                                'id_producto' => $det->id_producto,
                                // 'id_posicion' => $det->id_posicion,
                                'cantidad' => $det->cantidad,
                                'valorizacion' => (floatval($det->precio) * floatval($det->cantidad)),
                                'usuario' => $id_usuario,
                                'id_guia_com_det' => $id_guia_com_det,
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
                        //cambiar estado orden
                        DB::table('logistica.log_det_ord_compra')
                        ->where('id_detalle_orden',$det->id_detalle_orden)
                        ->update(['estado' => 6]);//En Almacen
                    }
                    //cambiar orden En Almacen
                    DB::table('logistica.log_ord_compra')
                    ->where('id_orden_compra',$request->id_orden_compra)
                    ->update(['en_almacen'=>true]);
                    //actualiza estado requerimiento reservado
                    $oc = DB::table('logistica.log_ord_compra')
                    ->select('log_ord_compra.*','alm_req.id_cliente','alm_req.id_persona','alm_req.id_tipo_requerimiento',
                    'alm_almacen.id_sede as sede_almacen')
                    ->join('almacen.alm_req','alm_req.id_requerimiento','=','log_ord_compra.id_requerimiento')
                    ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_req.id_almacen')
                    ->where('log_ord_compra.id_orden_compra',$request->id_orden_compra)
                    ->first();
                    //si existe un requerimiento por venta directa actualiza el estado
                    if ($oc !== null && $oc->id_requerimiento !== null){

                        if (($oc->id_tipo_requerimiento == 1 && ($oc->id_cliente !== null || $oc->id_persona !== null)) ||
                            ($oc->id_tipo_requerimiento == 3 && ($oc->id_sede !== $oc->sede_almacen))){
    
                            DB::table('almacen.alm_req')
                            ->where('id_requerimiento',$oc->id_requerimiento)
                            ->update(['estado'=>19]);//Reservado
    
                            DB::table('almacen.alm_det_req')
                            ->where('id_requerimiento',$oc->id_requerimiento)
                            ->update(['estado'=>19,
                                      'id_almacen_reserva'=>$request->id_almacen]);//Reservado
                        }
                    }
                }
            }    
            DB::commit();
            return response()->json($id_ingreso);
            
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            // return response()->json($e);
        }

    }

    public function guardar_guia_transferencia(Request $request){

        try {
            DB::beginTransaction();
            // database queries here
            $id_tp_doc_almacen = 2;//guia venta
            $id_operacion = 11;//salida por transferencia
            $fecha_registro = date('Y-m-d H:i:s');
            $fecha = date('Y-m-d');
            $usuario = Auth::user()->id_usuario;

            $id_guia = DB::table('almacen.guia_ven')->insertGetId(
                [
                    'id_tp_doc_almacen' => $id_tp_doc_almacen,
                    'serie' => $request->serie,
                    'numero' => $request->numero,
                    'fecha_emision' => $request->fecha_emision,
                    'fecha_almacen' => $request->fecha_almacen,
                    'id_almacen' => $request->id_almacen_origen,
                    'usuario' => $request->responsable_origen,
                    'estado' => 1,
                    'fecha_registro' => $fecha_registro,
                    'id_sede' => $request->id_sede,
                    'fecha_traslado' => $fecha,
                    'id_operacion' => $id_operacion,
                    'id_guia_com' => ($request->id_guia_com !== '' ? $request->id_guia_com : null),
                    // 'id_cliente' => $request->numero,
                    'registrado_por' => $usuario,
                ],
                'id_guia_ven'
            );
            //cambia estado serie-numero
            if ($request->id_serie_numero !== null && $request->id_serie_numero !== ''){
                DB::table('almacen.serie_numero')
                ->where('id_serie_numero',$request->id_serie_numero)
                ->update(['estado' => 8]);//emitido -> 8
            }

            $codigo_trans = TransferenciaController::transferencia_nextId($request->id_almacen_origen);
            //crear la transferencia
            $id_trans = DB::table('almacen.trans')->insertGetId([
                'id_almacen_origen' => $request->id_almacen_origen,
                'id_almacen_destino' => $request->id_almacen_destino,
                'codigo' => $codigo_trans,
                'id_guia_ven' => $id_guia,
                'responsable_origen' => $request->responsable_origen,
                'responsable_destino' => $request->responsable_destino,
                'fecha_transferencia' => $fecha,
                'registrado_por' => $usuario,
                'estado' => 1,
                'fecha_registro' => $fecha_registro,
            ],
                'id_transferencia'
            );
            // //copia id_transferencia en el ingreso
            // DB::table('almacen.mov_alm')
            //     ->where('id_mov_alm',$request->id_mov_alm)
            //     ->update(['id_transferencia'=>$id_trans]);
            //Genero la salida
            $codigo = AlmacenController::nextMovimiento(2,//salida
            $request->fecha_almacen,
            $request->id_almacen_origen);

            $id_salida = DB::table('almacen.mov_alm')->insertGetId(
                [
                    'id_almacen' => $request->id_almacen_origen,
                    'id_tp_mov' => 2,//Salidas
                    'codigo' => $codigo,
                    'fecha_emision' => $request->fecha_almacen,
                    'id_guia_ven' => $id_guia,
                    'id_transferencia' => $id_trans,
                    'id_operacion' => $id_operacion,
                    'revisado' => 0,
                    'usuario' => $usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha_registro,
                ],
                    'id_mov_alm'
                );

            $detalle = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','alm_prod.id_unidad_medida')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            ->where([['mov_alm_det.id_mov_alm',$request->id_mov_alm],['mov_alm_det.estado','!=',7]])
            ->get();

            foreach($detalle as $det){
                $id_guia_ven_det = DB::table('almacen.guia_ven_det')->insertGetId(
                    [
                        'id_guia_ven' => $id_guia,
                        'id_producto' => $det->id_producto,
                        'cantidad' => $det->cantidad,
                        'id_unid_med' => $det->id_unidad_medida,
                        'id_ing_det' => $det->id_mov_alm_det,
                        'estado' => 1,
                        'fecha_registro' => $fecha_registro,
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
                        'valorizacion' => $det->valorizacion,
                        'usuario' => $request->responsable_origen,
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
                        ['id_almacen','=',$request->id_almacen_origen]])
                ->first();
                //Traer stockActual
                $saldo = AlmacenController::saldo_actual_almacen($det->id_producto, $request->id_almacen_origen);
                $valor = AlmacenController::valorizacion_almacen($det->id_producto, $request->id_almacen_origen);
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
                        'id_almacen' => $request->id_almacen_origen,
                        'stock' => $saldo,
                        'valorizacion' => $valor,
                        'costo_promedio' => $cprom,
                        'estado' => 1,
                        'fecha_registro' => $fecha_registro
                        ]);
                }
            }

            //actualiza estado requerimiento: enviado
            DB::table('almacen.alm_req')
                ->where('id_requerimiento',$request->id_requerimiento)
                ->update(['estado'=>17]);//enviado
            //actualiza estado requerimiento_detalle: enviado
            DB::table('almacen.alm_det_req')
                ->where('id_requerimiento',$request->id_requerimiento)
                ->update(['estado'=>17]);//enviado

            DB::commit();
            return response()->json($id_salida);
            
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            // return response()->json($e);
        }
    }

}