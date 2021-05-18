<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Dompdf\Dompdf;
use PDF;
use Debugbar;
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
        $clasificaciones_guia = AlmacenController::mostrar_guia_clas_cbo();
        $usuarios = AlmacenController::select_usuarios();
        $motivos_anu = AlmacenController::select_motivo_anu();
        $monedas = AlmacenController::mostrar_moneda_cbo();
        $categorias = AlmacenController::mostrar_categorias_cbo();
        $subcategorias = AlmacenController::mostrar_subcategorias_cbo();
        $clasificaciones = AlmacenController::mostrar_clasificaciones_cbo();
        $unidades = AlmacenController::mostrar_unidades_cbo();
        $condiciones = AlmacenController::mostrar_condiciones_cbo();
        $sedes = AlmacenController::mostrar_sedes_cbo();

        return view('almacen/guias/ordenesPendientes', compact('almacenes','tp_doc','tp_operacion',
        'clasificaciones_guia','usuarios','motivos_anu','monedas','categorias','subcategorias',
        'clasificaciones','unidades','condiciones','sedes'));
    }

    public function listarOrdenesPendientes(){
        $data = DB::table('logistica.log_ord_compra')
            ->select('log_ord_compra.*','log_ord_compra.codigo as codigo_orden','log_ord_compra.codigo_softlink',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color','adm_contri.razon_social',
            'sis_usua.nombre_corto','alm_req.fecha_entrega','sis_sede.descripcion as sede_descripcion',
            'alm_req.codigo as codigo_requerimiento','alm_req.concepto')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','log_ord_compra.estado')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','log_ord_compra.id_usuario')
            ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','log_ord_compra.id_requerimiento')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','log_ord_compra.id_sede')
            ->where([['log_ord_compra.estado','!=',7],
                    ['log_ord_compra.en_almacen','=',false],
                    ['log_ord_compra.id_tp_documento','=',2]]);//Orden de Compra

        return datatables($data)->toJson();
    }
    
    public function listarIngresos(){

        $data = DB::table('almacen.mov_alm')
            ->select('mov_alm.*','guia_com.id_proveedor',
            'adm_contri.nro_documento','adm_contri.razon_social',
            'sis_usua.nombre_corto','sede_guia.descripcion as sede_guia_descripcion',
            'sede_guia.id_empresa',
            'alm_almacen.descripcion as almacen_descripcion',
            'alm_almacen.id_sede','guia_com.serie','guia_com.numero',
            'tp_ope.descripcion as operacion_descripcion',

            DB::raw("(SELECT count(distinct id_doc_com) FROM almacen.doc_com AS d
                    INNER JOIN almacen.guia_com_det AS guia
                        on(guia.id_guia_com = mov_alm.id_guia_com)
                    INNER JOIN almacen.doc_com_det AS doc
                        on(doc.id_guia_com_det = guia.id_guia_com_det)
                    WHERE d.id_doc_com = doc.id_doc) AS count_facturas"),

            DB::raw("(SELECT distinct id_doc_com FROM almacen.doc_com AS d
                    INNER JOIN almacen.guia_com_det AS guia on(
                        guia.id_guia_com = mov_alm.id_guia_com)
                    INNER JOIN almacen.doc_com_det AS doc on(
                        doc.id_guia_com_det = guia.id_guia_com_det and
                        doc.estado != 7)
                    WHERE d.id_doc_com = doc.id_doc) AS id_doc_com"),

            DB::raw("(SELECT COUNT(*) FROM almacen.trans_detalle 
                    inner join almacen.mov_alm_det on(
                        mov_alm_det.id_guia_com_det = trans_detalle.id_guia_com_det
                    )
                    where   mov_alm_det.id_mov_alm = mov_alm.id_mov_alm
                            and trans_detalle.estado != 7) AS count_transferencias"),
                            
            DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req 
                    inner join logistica.log_det_ord_compra as log on(
                            log.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento and
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
                    inner join almacen.alm_req as req on(
                            req.id_requerimiento = alm_det_req.id_requerimiento
                    )
                    where   ing.id_mov_alm = mov_alm.id_mov_alm
                            and req.id_sede != alm_almacen.id_sede
                            and alm_det_req.estado != 7) AS count_sedes_diferentes"),

            DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req 
                    inner join almacen.orden_despacho_det as od on(
                            od.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento and
                            od.estado != 7
                    )
                    inner join almacen.transfor_transformado as tra on(
                            tra.id_od_detalle = od.id_od_detalle and
                            tra.estado != 7
                    )
                    inner join almacen.guia_com_det as guia on(
                            guia.id_transformado = tra.id_transformado and
                            guia.estado != 7
                    )
                    inner join almacen.mov_alm_det as ing on(
                            ing.id_guia_com_det = guia.id_guia_com_det and
                            ing.estado != 7
                    )
                    inner join almacen.alm_req as req on(
                            req.id_requerimiento = alm_det_req.id_requerimiento
                    )
                    where   ing.id_mov_alm = mov_alm.id_mov_alm
                            and req.id_sede != alm_almacen.id_sede
                            and alm_det_req.estado != 7) AS count_sedes_diferentes_od"),

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
                            od.estado != 7) AS count_despachos_oc")
            )
            ->join('almacen.guia_com','guia_com.id_guia','=','mov_alm.id_guia_com')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','guia_com.id_almacen')
            ->join('administracion.sis_sede as sede_guia','sede_guia.id_sede','=','alm_almacen.id_sede')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
            ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','mov_alm.usuario')
            ->join('almacen.tp_ope','tp_ope.id_operacion','=','mov_alm.id_operacion')
            ->where([['mov_alm.estado','!=',7],['mov_alm.id_tp_mov','=',1]])
            ->orderBy('mov_alm.fecha_emision','desc');
            // ->get();
        return datatables($data)->toJson();
        // return response()->json($data);
    }

    public function detalleOrden($id_orden){
        $detalle = DB::table('logistica.log_det_ord_compra')
            ->select(
                'log_det_ord_compra.*','alm_item.id_producto','alm_prod.codigo',
                'alm_prod.part_number','alm_cat_prod.descripcion as categoria',
                'alm_subcat.descripcion as subcategoria','alm_req.id_requerimiento',
                'alm_prod.descripcion','alm_und_medida.abreviatura','alm_req.codigo as codigo_req',
                'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color','sis_sede.descripcion as sede_req',
                'oc_propias.orden_am','oportunidades.oportunidad','oportunidades.codigo_oportunidad',
                'entidades.nombre','oc_propias.id as id_oc_propia','oc_propias.url_oc_fisica',
                'users.name as user_name'
            )
            ->leftjoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftjoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->leftjoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_det_ord_compra.id_unidad_medida')
            ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftjoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftjoin('mgcp_cuadro_costos.cc','cc.id','=','alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades','oportunidades.id','=','cc.id_oportunidad')
            ->leftjoin('mgcp_usuarios.users','users.id','=','oportunidades.id_responsable')
            ->leftjoin('mgcp_acuerdo_marco.oc_propias','oc_propias.id_oportunidad','=','oportunidades.id')
            ->leftjoin('mgcp_acuerdo_marco.entidades','entidades.id','=','oportunidades.id_entidad')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'log_det_ord_compra.estado')
            ->where([
                ['log_det_ord_compra.id_orden_compra', '=', $id_orden]
            ])
            ->get();
        return response()->json($detalle);
    }

    public function detalleOrdenesSeleccionadas(Request $request){
        $ordenes = json_decode($request->oc_seleccionadas);
        $detalle = DB::table('logistica.log_det_ord_compra')
            ->select(
                'log_det_ord_compra.*','alm_prod.id_producto','alm_prod.codigo',
                'alm_prod.part_number','alm_cat_prod.descripcion as categoria',
                'alm_subcat.descripcion as subcategoria','alm_prod.series',
                'alm_prod.descripcion','alm_und_medida.abreviatura',
                'log_ord_compra.codigo as codigo_oc','alm_prod.id_categoria',
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
            ->whereIn('log_det_ord_compra.id_orden_compra',$ordenes)
            ->where([['log_det_ord_compra.estado','!=',7],
                    ['log_det_ord_compra.estado','!=',28]])
            ->get();
        
        return response()->json($detalle);
    }

    public function detalleMovimiento($id_guia){
        $detalle = DB::table('almacen.guia_com_det')
            ->select(
                'guia_com_det.*','alm_prod.codigo','alm_prod.part_number','alm_prod.descripcion','alm_und_medida.abreviatura',
                'guia_com.serie','guia_com.numero','guia_com.id_almacen','log_ord_compra.codigo as codigo_orden',
                'alm_req.codigo as codigo_req','sis_sede.descripcion as sede_req',
                'req_od.codigo as codigo_req_od','transformacion.codigo as codigo_transfor',
                'sede_req_od.descripcion as sede_req_od',
                'trans.codigo as codigo_trans',
                'req_tra.codigo as codigo_req_trans','sede_tra.descripcion as sede_req_trans'
            )
            ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->leftjoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det')
            ->leftjoin('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftjoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')

            ->leftjoin('almacen.transfor_transformado','transfor_transformado.id_transformado','=','guia_com_det.id_transformado')
            ->leftjoin('almacen.transformacion','transformacion.id_transformacion','=','transfor_transformado.id_transformacion')
            ->leftjoin('almacen.orden_despacho','orden_despacho.id_od','=','transformacion.id_od')
            ->leftjoin('almacen.alm_req as req_od','req_od.id_requerimiento','=','orden_despacho.id_requerimiento')
            ->leftjoin('administracion.sis_sede as sede_req_od','sede_req_od.id_sede','=','req_od.id_sede')

            ->leftjoin('almacen.trans_detalle','trans_detalle.id_trans_detalle','=','guia_com_det.id_trans_detalle')
            ->leftjoin('almacen.trans','trans.id_transferencia','=','trans_detalle.id_transferencia')
            ->leftjoin('almacen.alm_det_req as det_req_tra','det_req_tra.id_detalle_requerimiento','=','trans_detalle.id_requerimiento_detalle')
            ->leftjoin('almacen.alm_req as req_tra', 'req_tra.id_requerimiento', '=', 'det_req_tra.id_requerimiento')
            ->leftjoin('administracion.sis_sede as sede_tra', 'sede_tra.id_sede', '=', 'req_tra.id_sede')

            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_com_det.id_producto')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where([['guia_com_det.id_guia_com', '=', $id_guia],['guia_com_det.estado','!=',7]])
            ->get();

            $lista = [];

            foreach ($detalle as $det) {

                $series = DB::table('almacen.alm_prod_serie')
                ->select('alm_prod_serie.*')
                ->where([['alm_prod_serie.id_guia_com_det','=',$det->id_guia_com_det],
                        ['alm_prod_serie.estado','=',1]])
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
                    'codigo_transfor' => $det->codigo_transfor,
                    'codigo_trans' => $det->codigo_trans,
                    'codigo_req' => ($det->codigo_req!==null ? $det->codigo_req : ($det->codigo_req_od!==null?$det->codigo_req_od:$det->codigo_req_trans)),
                    'sede_req' => ($det->sede_req!==null ? $det->sede_req : ($det->sede_req_od!==null?$det->sede_req_od:$det->sede_req_trans)),
                    'series' => $series
                ]);
            }
        return response()->json($lista);
    }

    public function mostrar_series($id_guia_com_det){
        $series = DB::table('almacen.alm_prod_serie')
        ->select('alm_prod_serie.*')
        ->where([['alm_prod_serie.id_guia_com_det','=',$id_guia_com_det],
                ['alm_prod_serie.estado','=',1]])
        ->get();
        return response()->json($series);
    }

    public function actualizar_series(Request $request){
        $lista = json_decode($request->series);
        
        foreach ($lista as $s){
            $data = DB::table('almacen.alm_prod_serie')
            ->where('id_prod_serie',$s->id_prod_serie)
            ->update(['serie'=>$s->serie]);
        }
        return response()->json($data); 
    }

    public function guardar_series(Request $request){
        $lista = json_decode($request->series);
        $data = null;
        
        foreach ($lista as $serie){
            $exist = DB::table('almacen.alm_prod_serie')
            ->where([['serie','=',$serie],['id_guia_com_det','=',$request->id_guia_com_det]])
            ->first();

            if ($exist == null){
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
            $msj_trans = '';
            
            $id_tp_doc_almacen = 1;
            $id_usuario = Auth::user()->id_usuario;
            $fecha_registro = date('Y-m-d H:i:s');
            
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
                    'id_operacion' => $request->id_operacion,
                    'id_transformacion' => ($request->id_transformacion!==null ? $request->id_transformacion : null),
                    'revisado' => 0,
                    'usuario' => $id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha_registro,
                ],
                    'id_mov_alm'
                );

            if ($request->id_transformacion!==null){
                DB::table('almacen.transformacion')
                ->where('id_transformacion',$request->id_transformacion)
                ->update(['estado'=>10]);//Finalizado
            }
            $detalle_oc = json_decode($request->detalle);
            
            if ($request->id_operacion == '26'){//transformacion
                $id_od = null;
                $id_requerimiento = null;

                foreach($detalle_oc as $det){
                    //Guardo los items de la guia
                    $id_guia_com_det = DB::table('almacen.guia_com_det')->insertGetId(
                        [
                            "id_guia_com" => $id_guia,
                            "id_producto" => $det->id_producto,
                            "cantidad" => $det->cantidad,
                            // "id_unid_med" => $det->id_unidad_medida,
                            "usuario" => $id_usuario,
                            "tipo_transfor" => $det->tipo,
                            "id_transformado" => ($det->tipo == "transformado" ? $det->id : null),
                            "id_sobrante" => ($det->tipo == "sobrante" ? $det->id : null),
                            "unitario" => $det->unitario,
                            "total" => (floatval($det->unitario) * floatval($det->cantidad)),
                            "unitario_adicional" => 0,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro,
                        ],
                            'id_guia_com_det'
                        );

                    if ($det->series !== null){
                        //agrega series
                        foreach ($det->series as $serie) {
                            DB::table('almacen.alm_prod_serie')->insert(
                                [
                                    'id_prod' => $det->id_producto,
                                    'id_almacen' => $request->id_almacen,
                                    'serie' => $serie,
                                    'estado' => 1,
                                    'fecha_registro' => $fecha_registro,
                                    'id_guia_com_det' => $id_guia_com_det
                                ]
                            );
                        }
                    }
                    //Guardo los items del ingreso
                    $id_det = DB::table('almacen.mov_alm_det')->insertGetId(
                        [
                            'id_mov_alm' => $id_ingreso,
                            'id_producto' => $det->id_producto,
                            // 'id_posicion' => $det->id_posicion,
                            'cantidad' => $det->cantidad,
                            'valorizacion' => (floatval($det->unitario) * floatval($det->cantidad)),
                            'usuario' => $id_usuario,
                            'id_guia_com_det' => $id_guia_com_det,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro,
                        ],
                            'id_mov_alm_det'
                        );

                    OrdenesPendientesController::actualiza_prod_ubi($det->id_producto, $request->id_almacen);

                    if ($det->tipo == 'sobrante'){
                        
                        if ($id_od == null){
                            $sob = DB::table('almacen.transfor_sobrante')
                            ->select('orden_despacho.id_od','orden_despacho.id_requerimiento')
                            ->join('almacen.transformacion','transformacion.id_transformacion','=','transfor_sobrante.id_transformacion')
                            ->join('almacen.orden_despacho','orden_despacho.id_od','=','transformacion.id_od')
                            ->where('transfor_sobrante.id_sobrante',$det->id)->first();
    
                            $id_od = ($sob !== null ? $sob->id_od : null);
                            $id_requerimiento = ($sob !== null ? $sob->id_requerimiento : null);
                        }
                    }
                    else if ($det->tipo == 'transformado'){
                        
                        $tra = DB::table('almacen.transfor_transformado')
                        ->select('orden_despacho.id_od','orden_despacho.id_requerimiento','transfor_transformado.id_producto',
                        'transfor_transformado.cantidad','transformacion.id_almacen')
                        ->join('almacen.transformacion','transformacion.id_transformacion','=','transfor_transformado.id_transformacion')
                        ->join('almacen.orden_despacho','orden_despacho.id_od','=','transformacion.id_od')
                        ->where('transfor_transformado.id_transformado',$det->id)->first();

                        $id_od = ($tra !== null ? $tra->id_od : null);
                        $id_requerimiento = ($tra !== null ? $tra->id_requerimiento : null);
                        
                        if ($id_requerimiento!==null){
                            //Realiza la reserva en el requerimiento con item tiene transformacion
                            $det_req = DB::table('almacen.alm_det_req')
                            ->where([['id_requerimiento','=',$id_requerimiento],
                                    ['tiene_transformacion','=',true],
                                    ['id_producto','=',$det->id_producto]])
                                    ->first();
                            //realiza la reserva del transformado
                            DB::table('almacen.alm_det_req')
                            ->where('id_detalle_requerimiento',$det_req->id_detalle_requerimiento)
                            ->update([  'id_almacen_reserva'=>$tra->id_almacen,
                                        'stock_comprometido'=>$det->cantidad,
                                        'estado'=>10]);
                        }
                    }
                    
                }
                

                $od_detalles = DB::table('almacen.orden_despacho_det')
                ->where('id_od',$id_od)
                ->get();
                
                foreach ($od_detalles as $det) {
                    $detreq = DB::table('almacen.alm_det_req')
                            ->where('id_detalle_requerimiento',$det->id_detalle_requerimiento)
                            ->first();

                    $detdes = DB::table('almacen.orden_despacho_det')
                                ->select(DB::raw('SUM(cantidad) as suma_cantidad'))
                                ->join('almacen.orden_despacho','orden_despacho.id_od','=','orden_despacho_det.id_od')
                                ->join('almacen.transformacion','transformacion.id_od','=','orden_despacho.id_od')
                                ->where([['orden_despacho_det.id_detalle_requerimiento','=',$det->id_detalle_requerimiento],
                                            ['transformacion.estado','=',10]])
                                ->first();
                    //orden de despacho detalle estado   procesado
                    if ($detdes->suma_cantidad >= $detreq->cantidad){
                        DB::table('almacen.alm_det_req')
                        ->where('id_detalle_requerimiento',$det->id_detalle_requerimiento)
                        ->update(['estado'=>10]);
                    }
                }

                $culminados = DB::table('almacen.alm_det_req')
                ->where([['id_requerimiento','=',$id_requerimiento],
                        ['estado','=',10]])
                ->count();

                $todos = DB::table('almacen.alm_det_req')
                ->where([['id_requerimiento','=',$id_requerimiento],
                        // ['tiene_transformacion','=',false],
                        ['estado','!=',7]])
                ->count();

                if ($culminados == $todos){
                    DB::table('almacen.alm_req')
                    ->where('id_requerimiento',$id_requerimiento)
                    ->update(['estado'=>10]);
                }

                DB::table('almacen.alm_req_obs')
                ->insert([  'id_requerimiento'=>$id_requerimiento,
                            'accion'=>'INGRESADO',
                            'descripcion'=>'Ingresado a Almacén con Guía '.$request->serie.'-'.$request->numero.'.',
                            'id_usuario'=>$id_usuario,
                            'fecha_registro'=>$fecha_registro
                    ]);
            }
            else {
                $ids_ocd = [];
                
                foreach($detalle_oc as $d){
                    if ($d->id_detalle_orden !== null){
                        array_push($ids_ocd, $d->id_detalle_orden);
                    }
                }
                
                $detalle = DB::table('logistica.log_det_ord_compra')
                ->select('log_det_ord_compra.*','alm_item.id_producto','alm_req.id_sede','alm_req.id_requerimiento',
                            'alm_req.id_almacen as id_almacen_destino')
                ->leftjoin('almacen.alm_item','alm_item.id_item','=','log_det_ord_compra.id_item')
                ->leftjoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
                ->leftjoin('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','log_det_ord_compra.id_detalle_requerimiento')
                ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
                ->whereIn('id_detalle_orden',$ids_ocd)
                ->get();
                
                $cantidad = 0;
                $padres_oc = [];
                $padres_req = [];

                foreach ($detalle as $det) {

                    if (!in_array($det->id_orden_compra, $padres_oc)){
                        array_push($padres_oc, $det->id_orden_compra);
                    }
                    $series = [];
                    foreach($detalle_oc as $d){
                        if ($det->id_detalle_orden == $d->id_detalle_orden){
                            $cantidad = $d->cantidad;
                            $series = $d->series;
                            break;
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
                                'id_guia_com_det' => $id_guia_com_det
                            ]
                        );
                    }
                    //Guardo los items del ingreso
                    $id_det = DB::table('almacen.mov_alm_det')->insertGetId(
                        [
                            'id_mov_alm' => $id_ingreso,
                            'id_producto' => $det->id_producto,
                            // 'id_posicion' => $det->id_posicion,
                            'cantidad' => $cantidad,
                            'valorizacion' => (floatval($det->precio) * floatval($cantidad)),
                            'usuario' => $id_usuario,
                            'id_guia_com_det' => $id_guia_com_det,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro,
                        ],
                            'id_mov_alm_det'
                        );

                    OrdenesPendientesController::actualiza_prod_ubi($det->id_producto, $request->id_almacen);
                    //cambiar estado orden
                    $ant = DB::table('almacen.guia_com_det')
                    ->select(DB::raw('SUM(cantidad) AS suma_cantidad'))
                    ->where([['id_oc_det','=',$det->id_detalle_orden],['estado','!=',7]])
                    ->first();

                    $suma = ($ant !== null ? $ant->suma_cantidad : 0);

                    $dreq = DB::table('almacen.alm_det_req')
                    ->where('id_detalle_requerimiento',$det->id_detalle_requerimiento)
                    ->first();

                    if (!in_array($dreq->id_requerimiento, $padres_req)){
                        array_push($padres_req, $dreq->id_requerimiento);
                    }

                    if ($det->cantidad == $suma){
                        DB::table('logistica.log_det_ord_compra')
                        ->where('id_detalle_orden',$det->id_detalle_orden)
                        ->update(['estado' => 28]);//en almacen Total
                        
                        $ant_oc = DB::table('logistica.log_det_ord_compra')
                        ->select(DB::raw('SUM(cantidad) AS suma_cantidad'))
                        ->where('id_detalle_requerimiento',$det->id_detalle_requerimiento)
                        ->where('estado',28)//en almacen Total
                        ->orWhere('estado',10)//culminado
                        ->first();

                        // $detalle_req = DB::table('almacen.alm_det_req')
                        // ->where('id_detalle_requerimiento',$det->id_detalle_requerimiento)
                        // ->first();

                        // if ($detalle_req->estado !== 22){//Despacho interno
                            if ($dreq->cantidad == $ant_oc->suma_cantidad){
                                DB::table('almacen.alm_det_req')
                                ->where('id_detalle_requerimiento',$det->id_detalle_requerimiento)
                                ->update([  'estado'=>28,//en almacen total
                                            'stock_comprometido'=>$cantidad,
                                            'id_almacen_reserva'=>$request->id_almacen]);
                            } else {
                                DB::table('almacen.alm_det_req')
                                ->where('id_detalle_requerimiento',$det->id_detalle_requerimiento)
                                ->update([  'estado'=>27,//en almacen parcial
                                            'stock_comprometido'=>$cantidad,
                                            'id_almacen_reserva'=>$request->id_almacen]);
                            }
                        // }

                    } else {
                        DB::table('logistica.log_det_ord_compra')
                            ->where('id_detalle_orden',$det->id_detalle_orden)
                            ->update(['estado' => 27]);//en almacen parcial

                        DB::table('almacen.alm_det_req')
                            ->where('id_detalle_requerimiento',$det->id_detalle_requerimiento)
                            ->update([  'estado'=>27,//en almacen parcial
                                        'stock_comprometido'=>$cantidad,
                                        'id_almacen_reserva'=>$request->id_almacen]);
                    }
                }

                //actualiza estado oc padre
                foreach ($padres_oc as $padre){
                    $count_alm = DB::table('logistica.log_det_ord_compra')
                    ->where([['id_orden_compra','=',$padre],
                                ['estado','=',28]])
                    ->count();

                    $count_todo = DB::table('logistica.log_det_ord_compra')
                    ->where([['id_orden_compra','=',$padre],
                                ['estado','!=',7]])
                    ->count();

                    if ($count_todo == $count_alm){
                        //cambiar orden En Almacen
                        DB::table('logistica.log_ord_compra')
                        ->where('id_orden_compra',$padre)
                        ->update([  'en_almacen'=>true,
                                    'estado'=>28,
                                ]);
                    } else {
                        DB::table('logistica.log_ord_compra')
                        ->where('id_orden_compra',$padre)
                        ->update([  'estado'=>27  ]);
                    }

                    foreach ($padres_req as $padre){
                        $count_alm = DB::table('almacen.alm_det_req')
                        ->where([['id_requerimiento','=',$padre],
                                    ['estado','=',28]])
                        ->count();

                        $count_todo = DB::table('almacen.alm_det_req')
                        ->where([['id_requerimiento','=',$padre],
                                ['tiene_transformacion','=',false],
                                ['estado','!=',7]])
                        ->count();

                        if ($count_todo == $count_alm){
                            //cambiar orden En Almacen
                            DB::table('almacen.alm_req')
                            ->where('id_requerimiento',$padre)
                            ->update([  'estado'=>28  ]);
                        } else {
                            DB::table('almacen.alm_req')
                            ->where('id_requerimiento',$padre)
                            ->update([  'estado'=>27  ]);
                        }
                    }
                }
            }
            DB::commit();
            return response()->json(['id_ingreso'=>$id_ingreso,'id_guia'=>$id_guia]);
            
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
        }

    }

    public function transferenciax($id_guia_com){
        $guia_detalle = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*','alm_req.id_sede','alm_req.id_requerimiento',
                     'alm_req.id_almacen as id_almacen_destino','alm_det_req.id_detalle_requerimiento',
                     'req_od.id_sede as id_sede_od','req_od.id_requerimiento as id_requerimiento_od',
                     'req_od.id_almacen as id_almacen_destino_od',
                     'req_det_od.id_detalle_requerimiento as id_detalle_requerimiento_od')
            // ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
            ->leftJoin('logistica.log_det_ord_compra', function($join){
                $join->on('log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det');
                $join->where('log_det_ord_compra.estado','!=', 7);
            })
            // ->leftjoin('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','log_det_ord_compra.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_det_req', function($join){
                $join->on('alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento');
                $join->where('alm_det_req.estado','!=', 7);
            })
            ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
            // ->leftjoin('almacen.transfor_transformado','transfor_transformado.id_transformado','=','guia_com_det.id_transformado')
            ->leftJoin('almacen.transfor_transformado', function($join){
                $join->on('transfor_transformado.id_transformado', '=', 'guia_com_det.id_transformado');
                $join->where('transfor_transformado.estado','!=', 7);
            })
            // ->leftjoin('almacen.orden_despacho_det','orden_despacho_det.id_od_detalle','=','transfor_transformado.id_od_detalle')
            ->leftJoin('almacen.orden_despacho_det', function($join){
                $join->on('orden_despacho_det.id_od_detalle', '=', 'transfor_transformado.id_od_detalle');
                $join->where('orden_despacho_det.estado','!=', 7);
            })
            // ->leftjoin('almacen.alm_det_req as req_det_od','req_det_od.id_detalle_requerimiento','=','orden_despacho_det.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_det_req as req_det_od', function($join){
                $join->on('req_det_od.id_detalle_requerimiento', '=', 'orden_despacho_det.id_detalle_requerimiento');
                $join->where('req_det_od.estado','!=', 7);
            })
            ->leftjoin('almacen.alm_req as req_od','req_od.id_requerimiento','=','req_det_od.id_requerimiento')
            ->where('id_guia_com',$id_guia_com)
            ->get();
        return $guia_detalle;
    }

    public function transferencia($id_guia_com){

        try {
            DB::beginTransaction();

            $guia = DB::table('almacen.guia_com')->where('id_guia',$id_guia_com)->first();

            $guia_detalle = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*','alm_req.id_sede','alm_req.id_requerimiento',
                     'alm_req.id_almacen as id_almacen_destino','alm_det_req.id_detalle_requerimiento',
                     'req_od.id_sede as id_sede_od','req_od.id_requerimiento as id_requerimiento_od',
                     'req_od.id_almacen as id_almacen_destino_od',
                     'req_det_od.id_detalle_requerimiento as id_detalle_requerimiento_od')
            // ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
            ->leftJoin('logistica.log_det_ord_compra', function($join){
                $join->on('log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det');
                $join->where('log_det_ord_compra.estado','!=', 7);
            })
            // ->leftjoin('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','log_det_ord_compra.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_det_req', function($join){
                $join->on('alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento');
                $join->where('alm_det_req.estado','!=', 7);
            })
            ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
            // ->leftjoin('almacen.transfor_transformado','transfor_transformado.id_transformado','=','guia_com_det.id_transformado')
            ->leftJoin('almacen.transfor_transformado', function($join){
                $join->on('transfor_transformado.id_transformado', '=', 'guia_com_det.id_transformado');
                $join->where('transfor_transformado.estado','!=', 7);
            })
            // ->leftjoin('almacen.orden_despacho_det','orden_despacho_det.id_od_detalle','=','transfor_transformado.id_od_detalle')
            ->leftJoin('almacen.orden_despacho_det', function($join){
                $join->on('orden_despacho_det.id_od_detalle', '=', 'transfor_transformado.id_od_detalle');
                $join->where('orden_despacho_det.estado','!=', 7);
            })
            // ->leftjoin('almacen.alm_det_req as req_det_od','req_det_od.id_detalle_requerimiento','=','orden_despacho_det.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_det_req as req_det_od', function($join){
                $join->on('req_det_od.id_detalle_requerimiento', '=', 'orden_despacho_det.id_detalle_requerimiento');
                $join->where('req_det_od.estado','!=', 7);
            })
            ->leftjoin('almacen.alm_req as req_od','req_od.id_requerimiento','=','req_det_od.id_requerimiento')
            ->where('id_guia_com',$id_guia_com)
            ->get();

            $msj = null;
            $msj = $this->generarTransferencias($guia->id_almacen, $guia_detalle);

            DB::commit();
            return response()->json($msj);
            
        } catch (\PDOException $e) {
            DB::rollBack($e);
        }
    }

    public function generarTransferencias($id_almacen_origen, $detalle){

        $array_padres = [];
        $array_items = [];

        $sede = DB::table('almacen.alm_almacen')
                    ->select('id_sede')
                    ->where('id_almacen',$id_almacen_origen)->first();

        foreach($detalle as $det){
            //sede de requerimiento !== sede de la guia
            $sede_det = ($det->id_sede!==null ? $det->id_sede : $det->id_sede_od);

            if ($sede_det !== null && 
                $sede_det !== $sede->id_sede){

                $searchedValue = ($det->id_requerimiento!==null ? $det->id_requerimiento : $det->id_requerimiento_od);
                $existe = false;
                
                if (count($array_padres) > 0){
                    foreach($array_padres as $padre){
                        if ($padre['id_requerimiento'] == $searchedValue){
                            $existe = true;
                            break;
                        }
                    }
                }
                if ($existe == false){
                    $nuevo = [
                            'id_requerimiento' => $searchedValue,
                            'id_almacen_destino' => ($det->id_almacen_destino!==null ? $det->id_almacen_destino : $det->id_almacen_destino_od)
                    ];
                    
                    array_push($array_padres, $nuevo);
                }
                array_push($array_items, $det);
            }

            // if ($det->id_oc_det == null){
            //     array_push($array_items, $det);
            // }
        }

        $id_usuario = Auth::user()->id_usuario;
        $fecha = date('Y-m-d H:i:s');
        $msj = '';
        $id_trans = null;
        
        foreach ($array_padres as $padre){
            
            $codigo = TransferenciaController::transferencia_nextId($id_almacen_origen);
            
            if ($msj == ''){
                $msj = 'Se generó transferencia. '.$codigo;
            } else {
                $msj .= ', '.$codigo;
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

                $id_detalle_requerimiento = ($item->id_detalle_requerimiento!==null ? $item->id_detalle_requerimiento : $item->id_detalle_requerimiento_od);
                $id_almacen_destino = ($item->id_almacen_destino!==null ? $item->id_almacen_destino : $item->id_almacen_destino_od);

                if ($id_detalle_requerimiento !== null && 
                    $id_almacen_destino == $padre['id_almacen_destino']){

                    DB::table('almacen.trans_detalle')->insertGetId(
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

                    // DB::table('almacen.guia_com_det')
                    // ->where('id_guia_com_det', $item->id_guia_com_det)
                    // ->update(['id_trans_detalle' => $id_trans_det]);
                }
            }
        }

        // if ($id_trans !== null){

        //     foreach ($array_items as $item) {
        //         if ($item->id_detalle_requerimiento == null){
        //             $id_trans_det = DB::table('almacen.trans_detalle')->insertGetId(
        //                 [
        //                     'id_transferencia' => $id_trans,
        //                     'id_producto' => $item->id_producto,
        //                     'cantidad' => $item->cantidad,
        //                     'estado' => 1,
        //                     'fecha_registro' => $fecha,
        //                     'id_requerimiento_detalle' => null
        //                 ]);
    
        //             DB::table('almacen.guia_com_det')
        //             ->where('id_guia_com_det', $item->id_guia_com_det)
        //             ->update(['id_trans_detalle' => $id_trans_det]);
        //         }
        //     }
        // }
        return $msj;
    }

    public function prue($padre){
        $count_todo = DB::table('almacen.alm_det_req')
                    ->where([['id_requerimiento','=',$padre],
                            ['estado','!=',7]])
                    ->where('tiene_transformacion',null)
                    ->orWhere('tiene_transformacion',false)
                    ->count();
        return $count_todo;
    }
    public static function actualiza_prod_ubi($id_producto, $id_almacen){
        //Actualizo los saldos del producto
        //Obtengo el registro de saldos
        $ubi = DB::table('almacen.alm_prod_ubi')
        ->where([['id_producto','=',$id_producto],
                ['id_almacen','=',$id_almacen]])
        ->first();
        //Traer stockActual
        $saldo = AlmacenController::saldo_actual_almacen($id_producto, $id_almacen);
        $valor = AlmacenController::valorizacion_almacen($id_producto, $id_almacen);
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

    public function anular_ingreso(Request $request){

        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $msj = '';

            $ing = DB::table('almacen.mov_alm')
            ->select('mov_alm.*','guia_com.serie','guia_com.numero')
            ->join('almacen.guia_com','guia_com.id_guia','=','mov_alm.id_guia_com')
            ->where('id_mov_alm', $request->id_mov_alm)
            ->first();
            //si el ingreso no esta revisado
            if ($ing->revisado == 0){
                //Verifica si ya tiene transferencia u orden de despacho
                $detalle = DB::table('almacen.mov_alm_det')
                ->select('mov_alm_det.id_guia_com_det','mov_alm_det.id_producto',
                         'log_det_ord_compra.id_detalle_orden','log_det_ord_compra.id_orden_compra',
                         'alm_det_req.id_detalle_requerimiento','alm_det_req.id_requerimiento',
                         'trans_detalle.id_trans_detalle','trans.id_transferencia',
                         'trans.estado as estado_trans','orden_despacho.id_od')
                ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','mov_alm_det.id_guia_com_det')
                ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
                ->leftjoin('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','log_det_ord_compra.id_detalle_requerimiento')
                // ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
                ->leftjoin('almacen.orden_despacho','orden_despacho.id_requerimiento','=','alm_det_req.id_requerimiento')
                // ->leftjoin('almacen.trans_detalle','trans_detalle.id_trans_detalle','=','guia_com_det.id_trans_detalle')
                ->leftjoin('almacen.trans_detalle','trans_detalle.id_guia_com_det','=','guia_com_det.id_guia_com_det')
                ->leftjoin('almacen.trans','trans.id_transferencia','=','trans_detalle.id_transferencia')
                ->where([['mov_alm_det.id_mov_alm','=',$request->id_mov_alm],['mov_alm_det.estado','!=',7]])
                ->get();

                $validado = true;
                foreach ($detalle as $det) {
                    if (($det->id_trans_detalle !== null && $det->estado_trans == 17) || $det->id_od !== null){
                        $validado = false;
                    }
                }

                if ($validado){
                    //Anula ingreso
                    $update = DB::table('almacen.mov_alm')
                    ->where('id_mov_alm', $request->id_mov_alm)
                    ->update([ 'estado' => 7 ]);
                    //Anula el detalle
                    $update = DB::table('almacen.mov_alm_det')
                    ->where('id_mov_alm', $request->id_mov_alm)
                    ->update([ 'estado' => 7 ]);
                    //Agrega motivo anulacion a la guia
                    DB::table('almacen.guia_com_obs')->insert(
                    [
                        'id_guia_com'=>$request->id_guia_com,
                        'observacion'=>$request->observacion,
                        'registrado_por'=>$id_usuario,
                        'id_motivo_anu'=>$request->id_motivo_obs,
                        'fecha_registro'=>date('Y-m-d H:i:s')
                    ]);
                    //Anula la Guia
                    $update = DB::table('almacen.guia_com')
                    ->where('id_guia', $request->id_guia_com)
                    ->update([ 'estado' => 7 ]);
                    //Anula la Guia Detalle
                    $update = DB::table('almacen.guia_com_det')
                    ->where('id_guia_com', $request->id_guia_com)
                    ->update([ 'estado' => 7 ]);

                    if ($ing->id_transformacion !== null){
                        DB::table('almacen.transformacion')
                        ->where('id_transformacion',$ing->id_transformacion)
                        ->update(['estado' => 9]);//procesado
                    }
                    
                    $requerimientos = [];

                    foreach ($detalle as $det) {
                        //Actualiza stocks
                        OrdenesPendientesController::actualiza_prod_ubi($det->id_producto, $ing->id_almacen);
                        //Anula las series relacionadas
                        DB::table('almacen.alm_prod_serie')
                        ->where([['id_guia_com_det','=',$det->id_guia_com_det],
                                 ['id_prod','=',$det->id_producto]])
                        ->update(['estado' => 7]);

                        if ($det->id_detalle_orden !== null){
                            //Quita estado de la orden
                            DB::table('logistica.log_det_ord_compra')
                            ->where('id_detalle_orden',$det->id_detalle_orden)
                            ->update(['estado' => 26]);
                            //Quita estado de la orden
                            DB::table('logistica.log_ord_compra')
                            ->where('id_orden_compra',$det->id_orden_compra)
                            ->update([  'en_almacen' => false,
                                        'estado'=>26]);
                        }

                        DB::table('almacen.alm_det_req')
                        ->where('id_detalle_requerimiento',$det->id_detalle_requerimiento)
                        ->update(['estado' => 5]);//Atendido
                        
                        if (!in_array($det->id_requerimiento, $requerimientos)){
                            //agrega id_requerimiento
                            array_push($requerimientos, $det->id_requerimiento);
                            //Requerimiento regresa a atendido
                            DB::table('almacen.alm_req')
                            ->where('id_requerimiento',$det->id_requerimiento)
                            ->update(['estado' => 5]);//Atendido
                        }
                        //Anula transferencia
                        if ($det->id_trans_detalle !== null){
                            
                            DB::table('almacen.trans_detalle')
                            ->where('id_trans_detalle',$det->id_trans_detalle)
                            ->update(['estado' => 7]);//Anulado

                            DB::table('almacen.trans')
                            ->where('id_transferencia',$det->id_transferencia)
                            ->update(['estado' => 7]);//Anulado
                        }
                    }

                    foreach ($requerimientos as $id_requerimiento) {
                        //Agrega accion en requerimiento
                        DB::table('almacen.alm_req_obs')
                        ->insert([  'id_requerimiento'=>$id_requerimiento,
                                    'accion'=>'INGRESO ANULADO',
                                    'descripcion'=>'Ingreso por Compra con Guía '.$ing->serie.'-'.$ing->numero.' e '.$ing->codigo.' fue Anulado. Requerimiento regresa a Atendido.',
                                    'id_usuario'=>$id_usuario,
                                    'fecha_registro'=>date('Y-m-d H:i:s')
                            ]);                        
                    }
                } else {
                    $msj = 'El ingreso ya fue procesado con una Orden de Despacho o una Transferencia.';
                }
            } else {
                $msj = 'El ingreso ya fue revisado por el Jefe de Almacén';
            }
            DB::commit();
            return response()->json($msj);
            
        } catch (\PDOException $e) {

            DB::rollBack();
            // return response()->json($e);
        }
    }

    function obtenerGuia($id)
    {
        $guia = DB::table('almacen.guia_com')
        ->select('guia_com.id_guia','guia_com.id_proveedor','adm_contri.razon_social',
        'guia_com.serie','guia_com.numero')
        ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->where('guia_com.id_guia',$id)
        ->first();

        $detalle = DB::table('almacen.guia_com_det')
        ->select('guia_com_det.*','log_ord_compra.codigo as cod_orden','alm_prod.codigo','alm_prod.descripcion',
        'alm_prod.part_number','alm_und_medida.abreviatura','log_det_ord_compra.precio','log_ord_compra.id_condicion',
        'log_ord_compra.plazo_dias','log_ord_compra.id_sede')
        ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
        ->leftjoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','guia_com_det.id_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->where('guia_com_det.id_guia_com',$id)
        ->orderBy('guia_com_det.id_guia_com_det')
        ->get();

        $igv = DB::table('contabilidad.cont_impuesto')
        ->where('codigo','IGV')->first();

        return response()->json(['guia'=>$guia, 'detalle'=>$detalle, 'igv'=>$igv->porcentaje]);
    }

    function obtenerGuiaSeleccionadas(Request $request)
    {
        $ingresos = json_decode($request->id_ingresos_seleccionados);

        $detalle = DB::table('almacen.guia_com_det')
        ->select('guia_com_det.*','log_ord_compra.codigo as cod_orden','alm_prod.codigo','alm_prod.descripcion',
        'alm_prod.part_number','alm_und_medida.abreviatura','log_det_ord_compra.precio','log_ord_compra.id_condicion',
        'log_ord_compra.plazo_dias','log_ord_compra.id_sede','guia_com.serie','guia_com.numero')
        ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
        ->leftjoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','guia_com_det.id_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
        ->whereIn('guia_com_det.id_guia_com',$ingresos)
        ->orderBy('guia_com_det.id_guia_com_det')
        ->get();

        $igv = DB::table('contabilidad.cont_impuesto')
        ->where('codigo','IGV')->first();

        return response()->json(['detalle'=>$detalle, 'igv'=>$igv->porcentaje]);
    }

    public function guardar_doc_compra(Request $request)
    {
        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $fecha = date('Y-m-d H:i:s');

            $id_doc = DB::table('almacen.doc_com')->insertGetId(
                [
                    'serie' => $request->serie_doc,
                    'numero' => $request->numero_doc,
                    'id_tp_doc' => $request->id_tp_doc,
                    'id_proveedor' => $request->id_proveedor,
                    'fecha_emision' => $request->fecha_emision_doc,
                    'fecha_vcmto' => $request->fecha_emision_doc,
                    'id_condicion' => $request->id_condicion,
                    'credito_dias' => $request->credito_dias,
                    'id_sede' => $request->id_sede,
                    'moneda' => $request->moneda,
                    // 'tipo_cambio' => $request->tipo_cambio,
                    'sub_total' => $request->sub_total,
                    // 'total_descuento' => $request->total_descuento,
                    // 'porcen_descuento' => $request->porcen_descuento,
                    // 'total' => $request->importe,
                    'total_igv' => $request->igv,
                    // 'total_ant_igv' => $request->total_ant_igv,
                    'porcen_igv' => $request->porcentaje_igv,
                    // 'porcen_anticipo' => $request->porcen_anticipo,
                    // 'total_otros' => $request->total_otros,
                    'total_a_pagar' => $request->total,
                    'usuario' => $id_usuario,
                    'registrado_por' => $id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                    'id_doc_com'
                );

            $items = json_decode($request->detalle_items);

            foreach ($items as $item) {
                DB::table('almacen.doc_com_det')
                ->insert([
                    'id_doc' => $id_doc,
                    'id_guia_com_det' => $item->id_guia_com_det,
                    'id_item' => $item->id_producto,
                    'cantidad' => $item->cantidad,
                    'id_unid_med' => $item->id_unid_med,
                    'precio_unitario' => $item->precio,
                    'sub_total' => $item->sub_total,
                    'porcen_dscto' => $item->porcentaje_dscto,
                    'total_dscto' => $item->total_dscto,
                    'precio_total' => $item->total,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ]);

                DB::table('almacen.mov_alm_det')
                ->where('id_guia_com_det',$item->id_guia_com_det)
                ->update(['valorizacion'=>$item->total]);
            }
            
            DB::commit();
            return response()->json($id_doc);
            
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
        }

    }

    public function documentos_ver($id_doc)
    {
        $docs = DB::table('almacen.doc_com')
        ->select('doc_com.id_doc_com','doc_com.serie', 'doc_com.numero','doc_com.fecha_emision',
        'cont_tp_doc.descripcion as tp_doc','adm_contri.nro_documento','adm_contri.razon_social',
        'sis_moneda.simbolo','doc_com.total_a_pagar','doc_com.sub_total','doc_com.total_igv',
        'log_cdn_pago.descripcion as condicion_descripcion','sis_sede.descripcion as sede_descripcion',
        'doc_com.credito_dias')
        // ->join('almacen.guia_com_det','guia_com_det.id_guia_com','=','guia_com.id_guia')
        // ->join('almacen.doc_com_det','doc_com_det.id_guia_com_det','=','guia_com_det.id_guia_com_det')
        // ->join('almacen.doc_com','doc_com.id_doc_com','=','doc_com_det.id_doc')
        ->join('logistica.log_prove','log_prove.id_proveedor','=','doc_com.id_proveedor')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->join('contabilidad.cont_tp_doc','cont_tp_doc.id_tp_doc','=','doc_com.id_tp_doc')
        ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','doc_com.moneda')
        ->leftJoin('logistica.log_cdn_pago','log_cdn_pago.id_condicion_pago','=','doc_com.id_condicion')
        ->join('administracion.sis_sede','sis_sede.id_sede','=','doc_com.id_sede')
        ->where('doc_com.id_doc_com',$id_doc)
        ->distinct()
        ->get();

        $detalles = DB::table('almacen.doc_com_det')
        ->select('doc_com_det.*','alm_prod.codigo','alm_prod.descripcion','alm_prod.part_number',
        'alm_und_medida.abreviatura','guia_com.serie','guia_com.numero')
        ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','doc_com_det.id_guia_com_det')
        ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','doc_com_det.id_item')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','doc_com_det.id_unid_med')
        ->where('doc_com_det.id_doc',$id_doc)
        ->get();

        return response()->json(['docs'=>$docs,'detalles'=>$detalles]);
    }

    public function cambio_serie_numero(Request $request){
    
        try {
            DB::beginTransaction();
            
            $id_usuario = Auth::user()->id_usuario;
            $msj = '';
            
            $ing = DB::table('almacen.mov_alm')
            ->where('id_mov_alm', $request->id_ingreso)
            ->first();
            //si la ingreso no esta revisada
            if ($ing->revisado == 0){
                //si existe una orden
                if ($ing->id_guia_com !== null) {
                    //Anula la Guia
                    $update = DB::table('almacen.guia_com')
                    ->where('id_guia', $ing->id_guia_com)
                    ->update([  'serie' => $request->serie_nuevo,
                                'numero'=> $request->numero_nuevo ]);
                    //Agrega motivo anulacion a la guia
                    DB::table('almacen.guia_com_obs')->insert(
                        [
                            'id_guia_com'=>$ing->id_guia_com,
                            'observacion'=>'Se cambió la serie-número de la Guía Compra a '.$request->serie_nuevo.'-'.$request->numero_nuevo,
                            'registrado_por'=>$id_usuario,
                            'id_motivo_anu'=>$request->id_motivo_obs_cambio,
                            'fecha_registro'=>date('Y-m-d H:i:s')
                        ]);

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

}