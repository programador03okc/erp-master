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
        $motivos_anu = AlmacenController::select_motivo_anu();
        return view('almacen/guias/ordenesPendientes', compact('almacenes','tp_doc','tp_operacion','clasificaciones','usuarios','motivos_anu'));
    }

    public function listarOrdenesPendientes(){
        $data = DB::table('logistica.log_ord_compra')
            ->select('log_ord_compra.*','log_ord_compra.codigo as codigo_orden','log_ord_compra.codigo_softlink',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color','adm_contri.razon_social',
            'sis_usua.nombre_corto','alm_req.fecha_entrega','sis_sede.descripcion as sede_descripcion',
            // 'sis_moneda.simbolo',
            'alm_req.codigo as codigo_requerimiento','alm_req.concepto')
            // ->join('administracion.adm_tp_docum','adm_tp_docum.id_tp_documento','=','log_ord_compra.id_tp_documento')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','log_ord_compra.estado')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','log_ord_compra.id_usuario')
            // ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','log_ord_compra.id_moneda')
            ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','log_ord_compra.id_requerimiento')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','log_ord_compra.id_sede')
            ->where([['log_ord_compra.estado','!=',7],
                    ['log_ord_compra.en_almacen','=',false],
                    ['log_ord_compra.id_tp_documento','=',2]])//Orden de Compra
            ->get();
        return datatables($data)->toJson();
        // return response()->json($data);
    }
    
    public function listarOrdenesEntregadas(){
        $data = DB::table('almacen.mov_alm')
            ->select('mov_alm.*',
            // 'log_ord_compra.id_orden_compra','log_ord_compra.codigo as codigo_orden',
            // 'log_ord_compra.codigo_softlink',
            'adm_contri.nro_documento','adm_contri.razon_social',
            // 'log_ord_compra.fecha as fecha_orden',
            // 'alm_req.codigo as codigo_requerimiento','alm_req.concepto',
            // 'log_ord_compra.id_sede as sede_orden',
            'sis_usua.nombre_corto','sede_guia.descripcion as sede_guia_descripcion',
            // 'sis_moneda.simbolo','log_ord_compra.monto_subtotal','log_ord_compra.monto_igv','log_ord_compra.monto_total',
            'alm_almacen.descripcion as almacen_descripcion',
            // 'sede_req.descripcion as sede_requerimiento_descripcion',
            // 'sede_req.id_sede as sede_requerimiento',
            'guia_com.serie','guia_com.numero'
            // 'alm_req.id_requerimiento','alm_req.estado as estado_requerimiento',
            // 'alm_req.id_tipo_requerimiento','alm_req.id_almacen as almacen_requerimiento',
            // 'trans.id_transferencia','guia_ven_trans.id_guia_ven as id_guia_ven_trans',
            // 'trans.codigo as codigo_trans','trans.estado as estado_trans',
            // 'salida_trans.id_mov_alm as id_salida_trans'
            )
            ->join('almacen.guia_com','guia_com.id_guia','=','mov_alm.id_guia_com')
            // ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','guia_com.id_oc')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','guia_com.id_almacen')
            ->join('administracion.sis_sede as sede_guia','sede_guia.id_sede','=','alm_almacen.id_sede')
            ->leftJoin('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
            ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            // ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','log_ord_compra.id_requerimiento')
            // ->leftjoin('administracion.sis_sede as sede_req','sede_req.id_sede','=','alm_req.id_sede')
            // ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_req.id_almacen')
            // ->leftjoin('almacen.guia_ven','guia_ven.id_guia_com','=','mov_alm.id_guia_com')
            // ->leftJoin('almacen.guia_ven as guia_ven_trans', function($join)
            //              {   $join->on('guia_ven_trans.id_guia_com', '=', 'mov_alm.id_guia_com');
            //                  $join->where('guia_ven_trans.estado','!=', 7);
            //              })
            // ->leftJoin('almacen.mov_alm as salida_trans', function($join)
            //              {   $join->on('salida_trans.id_guia_ven', '=', 'guia_ven_trans.id_guia_ven');
            //                  $join->where('salida_trans.estado','!=', 7);
            //              })
            // ->leftJoin('almacen.trans', function($join)
            //              {   $join->on('trans.id_guia_ven', '=', 'guia_ven_trans.id_guia_ven');
            //                  $join->where('trans.estado','!=', 7);
            //              })
            // ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','log_ord_compra.id_moneda')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','mov_alm.usuario')
            // ->leftJoin('almacen.guia_ven','guia_ven.id_guia_com','=','mov_alm.id_guia_com')
            ->where([['mov_alm.estado','!=',7],['mov_alm.id_tp_mov','=',1]])
            ->orderBy('mov_alm.fecha_emision','desc')
            ->get();
        return datatables($data)->toJson();
    }

    public function detalleOrden($id_orden){
        $detalle = DB::table('logistica.log_det_ord_compra')
            ->select(
                'log_det_ord_compra.*','alm_item.id_producto','alm_prod.codigo',
                'alm_prod.part_number','alm_cat_prod.descripcion as categoria',
                'alm_subcat.descripcion as subcategoria',
                'alm_prod.descripcion','alm_und_medida.abreviatura',
                'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color'
                // 'log_valorizacion_cotizacion.cantidad_cotizada',
                // 'log_valorizacion_cotizacion.precio_cotizado',
                // 'log_valorizacion_cotizacion.monto_descuento',
                // 'log_valorizacion_cotizacion.subtotal'
                // 'alm_det_req.id_item'
            )
            // ->leftJoin('configuracion.sis_usua as sis_usua_aut', 'sis_usua_aut.id_usuario', '=', 'log_det_ord_compra.personal_autorizado')
            // ->leftJoin('rrhh.rrhh_trab as trab_aut', 'trab_aut.id_trabajador', '=', 'sis_usua_aut.id_trabajador')
            // ->leftJoin('rrhh.rrhh_postu as post_aut', 'post_aut.id_postulante', '=', 'trab_aut.id_postulante')
            // ->leftJoin('rrhh.rrhh_perso as pers_aut', 'pers_aut.id_persona', '=', 'post_aut.id_persona')

            // ->join('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', 'log_det_ord_compra.id_valorizacion_cotizacion')
            // ->join('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
            // ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'valoriza_coti_detalle.id_detalle_requerimiento')
            ->leftjoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftjoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->leftjoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_det_ord_compra.id_unidad_medida')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'log_det_ord_compra.estado')
            // ->leftjoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            // ->leftjoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
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
                'log_det_ord_compra.*','alm_item.id_producto','alm_prod.codigo',
                'alm_prod.part_number','alm_cat_prod.descripcion as categoria',
                'alm_subcat.descripcion as subcategoria',
                'alm_prod.descripcion','alm_und_medida.abreviatura',
                'log_ord_compra.codigo as codigo_oc',
                DB::raw('(SELECT SUM(guia_com_det.cantidad) FROM almacen.guia_com_det
                          WHERE guia_com_det.id_oc_det = log_det_ord_compra.id_detalle_orden 
                            AND guia_com_det.estado != 7) 
                          AS suma_cantidad_guias')
            )
            ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            ->leftjoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftjoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->leftjoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_det_ord_compra.id_unidad_medida')
            ->whereIn('log_det_ord_compra.id_orden_compra',$ordenes)
            ->where('log_det_ord_compra.estado',1)
            ->get();
        $html = '';
        $i = 1;
        foreach ($detalle as $det) {
            $cantidad = ($det->cantidad - $det->suma_cantidad_guias);
            $html.='<tr>
                <td><input type="checkbox" value="'.$det->id_detalle_orden.'" checked/></td>
                <td>'.$det->codigo_oc.'</td>
                <td>'.$det->codigo.'</td>
                <td>'.$det->part_number.'</td>
                <td>'.$det->categoria.'</td>
                <td>'.$det->subcategoria.'</td>
                <td>'.$det->descripcion.'</td>
                <td><input type="number" id="cantidad" value="'.$cantidad.'" min="1" max="'.$cantidad.'" style="width:80px;"/></td>
                <td>'.$det->abreviatura.'</td>
            </tr>';
            $i++;
        }
        return json_encode($html);
    }

    public function detalleMovimiento($id_mov_alm){
        $detalle = DB::table('almacen.mov_alm_det')
            ->select(
                'mov_alm_det.*','alm_prod.codigo','alm_prod.part_number','alm_cat_prod.descripcion as categoria',
                'alm_subcat.descripcion as subcategoria','alm_prod.descripcion','alm_und_medida.abreviatura',
                'log_ord_compra.codigo as codigo_orden','guia_com.serie','guia_com.numero','alm_req.codigo as codigo_req',
                'sis_sede.descripcion as sede_req'
            )
            ->leftjoin('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'mov_alm_det.id_guia_com_det')
            ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->leftjoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det')
            ->leftjoin('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'log_ord_compra.id_requerimiento')
            ->leftjoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
            ->leftjoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->leftjoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where([['mov_alm_det.id_mov_alm', '=', $id_mov_alm],['mov_alm_det.estado','!=',7]])
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
        
            // if ($request->id_orden_compra !== null){
                $id_tp_doc_almacen = 1;
                $id_usuario = Auth::user()->id_usuario;
                $fecha_registro = date('Y-m-d H:i:s');
    
                // $orden = DB::table('logistica.log_ord_compra')
                // ->where('id_orden_compra',$request->id_orden_compra)
                // ->first();
        
                // if (isset($orden)){
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
                            // 'id_doc_com' => (isset($doc) ? $doc->id_doc_com : null),
                            'id_operacion' => $request->id_operacion,
                            'revisado' => 0,
                            'usuario' => $id_usuario,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro,
                        ],
                            'id_mov_alm'
                        );

                    $detalle_oc = json_decode($request->detalle);
                    $ids_ocd = [];
                    
                    foreach($detalle_oc as $d){
                        array_push($ids_ocd, $d->id_detalle_orden);
                    }
                    
                    $detalle = DB::table('logistica.log_det_ord_compra')
                    ->select('log_det_ord_compra.*','alm_item.id_producto')
                    // 'log_valorizacion_cotizacion.precio_cotizado',
                    // 'log_valorizacion_cotizacion.id_unidad_medida','log_valorizacion_cotizacion.precio_sin_igv',
                    // 'log_valorizacion_cotizacion.cantidad_cotizada',
                    // 'log_valorizacion_cotizacion.monto_descuento'
                    // ->leftjoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','log_det_ord_compra.id_valorizacion_cotizacion')
                    ->leftjoin('almacen.alm_item','alm_item.id_item','=','log_det_ord_compra.id_item')
                    ->whereIn('id_detalle_orden',$ids_ocd)
                    // ->where([['log_det_ord_compra.estado','!=',7],
                    //          ['log_det_ord_compra.id_orden_compra','=',$request->id_orden_compra]])
                    ->get();
                    
                    $cantidad = 0;
                    $padres_oc = [];

                    foreach ($detalle as $det) {
                        // $posicion = DB::table('almacen.alm_prod_ubi')
                        // ->select('alm_ubi_posicion.id_posicion','alm_ubi_posicion.codigo')
                        // ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
                        // ->where([['alm_prod_ubi.id_producto','=',$det->id_producto],
                        //          ['alm_prod_ubi.id_almacen','=',$request->id_almacen],
                        //          ['alm_prod_ubi.estado','=',1]])
                        // ->orderBy('id_prod_ubi','desc')
                        // ->first();
                        if (!in_array($det->id_orden_compra, $padres_oc)){
                            array_push($padres_oc, $det->id_orden_compra);
                        }
                        foreach($detalle_oc as $d){
                            if ($det->id_detalle_orden == $d->id_detalle_orden){
                                $cantidad = $d->cantidad;
                                break;
                            }
                        }
                        //Guardo los items de la guia
                        $id_guia_com_det = DB::table('almacen.guia_com_det')->insertGetId(
                            [
                                "id_guia_com" => $id_guia,
                                "id_producto" => $det->id_producto,
                                // "id_posicion" => (isset($posicion) ? $posicion->id_posicion : null),
                                // "id_posicion" => $posicion->id_posicion,
                                "cantidad" => $cantidad,
                                "id_unid_med" => $det->id_unidad_medida,
                                "usuario" => $id_usuario,
                                "id_oc_det" => $det->id_detalle_orden,
                                "unitario" => $det->precio,
                                "total" => (floatval($det->precio) * floatval($cantidad)),
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
                                'cantidad' => $cantidad,
                                'valorizacion' => (floatval($det->precio) * floatval($cantidad)),
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
                        $ant = DB::table('almacen.guia_com_det')
                        ->select(DB::raw('SUM(cantidad) AS suma_cantidad'))
                        ->where([['id_oc_det','=',$det->id_detalle_orden],['estado','!=',7]])
                        ->first();

                        $suma = ($ant !== null ? $ant->suma_cantidad : 0);

                        if ($det->cantidad == $suma){
                            DB::table('logistica.log_det_ord_compra')
                            ->where('id_detalle_orden',$det->id_detalle_orden)
                            ->update(['estado' => 6]);//En Almacen
                        }
                    }
                    //actualiza estado oc padre
                    foreach ($padres_oc as $padre){
                        $count_alm = DB::table('logistica.log_det_ord_compra')
                        ->where([['id_orden_compra','=',$padre],
                                 ['estado','=',6]])
                        ->count();

                        $count_todo = DB::table('logistica.log_det_ord_compra')
                        ->where([['id_orden_compra','=',$padre],
                                 ['estado','!=',7]])
                        ->count();

                        if ($count_todo == $count_alm){
                            //cambiar orden En Almacen
                            DB::table('logistica.log_ord_compra')
                            ->where('id_orden_compra',$padre)
                            ->update(['en_almacen'=>true]);
                        }
                        //actualiza estado requerimiento reservado
                        $oc = DB::table('logistica.log_ord_compra')
                        ->select('log_ord_compra.*','alm_req.id_cliente','alm_req.id_persona','alm_req.id_tipo_requerimiento',
                        'alm_req.tipo_cliente','alm_req.id_sede as sede_requerimiento')
                        ->join('almacen.alm_req','alm_req.id_requerimiento','=','log_ord_compra.id_requerimiento')
                        // ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_req.id_almacen')
                        ->where('log_ord_compra.id_orden_compra',$padre)
                        ->first();
                        //si existe un requerimiento 
                        if ($oc !== null && $oc->id_requerimiento !== null){
                            $estado = '';

                            if (($oc->tipo_cliente == 1 || $oc->tipo_cliente == 2 || $oc->tipo_cliente == 4) ||
                                ($oc->tipo_cliente == 3 && ($oc->id_sede !== $oc->sede_requerimiento))){

                                $estado = 'Reservado';

                                DB::table('almacen.alm_req')
                                ->where('id_requerimiento',$oc->id_requerimiento)
                                ->update(['estado'=>19]);//Reservado
                                
                                DB::table('almacen.alm_det_req')
                                ->where('id_requerimiento',$oc->id_requerimiento)
                                ->update(['estado'=>19,
                                            'id_almacen_reserva'=>$request->id_almacen]);//Reservado

                                if ($oc->sede_requerimiento !== $request->id_sede){//sede orden y almacen
                                    $fecha = date('Y-m-d H:i:s');
                                    $codigo_tra = TransferenciaController::transferencia_nextId($request->id_almacen);
                                    $guardar = false;
                                    $almacen_destino = DB::table('almacen.alm_almacen')
                                        ->where([['id_sede','=',$oc->sede_requerimiento],['estado','!=',7]])->first();
                                    
                                    $id_trans = DB::table('almacen.trans')->insertGetId(
                                        [
                                            'id_almacen_origen' => $request->id_almacen,
                                            'id_almacen_destino' => $almacen_destino->id_almacen,
                                            'codigo' => $codigo_tra,
                                            'id_requerimiento' =>  $oc->id_requerimiento,
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

                                    foreach ($detalle as $det) {

                                        if ($det->id_orden_compra == $padre){
                                            
                                            foreach($detalle_oc as $d){
                                                if ($det->id_detalle_orden == $d->id_detalle_orden){
                                                    $cantidad = $d->cantidad;
                                                    break;
                                                }
                                            }

                                            DB::table('almacen.trans_detalle')->insert(
                                            [
                                                'id_transferencia' => $id_trans,
                                                'id_producto' => $det->id_producto,
                                                'id_requerimiento_detalle' => $det->id_detalle_requerimiento,
                                                'cantidad' => $cantidad,
                                                'estado' => 1,
                                                'fecha_registro' => $fecha
                                            ]);
                                        }
                                    }
                    
                                }
                            }
                            else {
                                $estado = 'Procesado';
                                DB::table('almacen.alm_req')
                                ->where('id_requerimiento',$oc->id_requerimiento)
                                ->update(['estado'=>9]);//Procesado
                                    
                                DB::table('almacen.alm_det_req')
                                ->where('id_requerimiento',$oc->id_requerimiento)
                                ->update(['estado'=>9,
                                            'id_almacen_reserva'=>null]);//Procesado
                            }
                            
                            DB::table('almacen.alm_req_obs')
                            ->insert(['id_requerimiento'=>$oc->id_requerimiento,
                                'accion'=>'INGRESADO',
                                'descripcion'=>'Ingresado a Almacén con Guía '.$request->serie.'-'.$request->numero.'. Pasa a estado: '.$estado,
                                'id_usuario'=>$id_usuario,
                                'fecha_registro'=>$fecha_registro
                                ]);
                            }
                        }
                    // }
            //     }
            // }    
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
                    'serie' => $request->trans_serie,
                    'numero' => $request->trans_numero,
                    'fecha_emision' => $request->fecha_emision,
                    'fecha_almacen' => $request->fecha_almacen,
                    'id_almacen' => $request->id_almacen_origen,
                    // 'usuario' => $request->responsable_origen,
                    'usuario' => $usuario,
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
                // 'responsable_origen' => $request->responsable_origen,
                'responsable_origen' => $usuario,
                'responsable_destino' => $request->responsable_destino_trans,
                'fecha_transferencia' => $fecha,
                'registrado_por' => $usuario,
                'estado' => 17,//enviado
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
                        'usuario' => $usuario,
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
                ->update(['estado'=>17,
                          'id_almacen'=>$request->id_almacen_destino]);//enviado
            //actualiza estado requerimiento_detalle: enviado
            DB::table('almacen.alm_det_req')
                ->where('id_requerimiento',$request->id_requerimiento)
                ->update(['estado'=>17]);//enviado
            //Agrega accion en requerimiento
            DB::table('almacen.alm_req_obs')
            ->insert(['id_requerimiento'=>$request->id_requerimiento,
                'accion'=>'SALIDA POR TRANSFERENCIA',
                'descripcion'=>'Salió del Almacén por Transferencia con Guía '.$request->trans_serie.'-'.$request->trans_numero,
                'id_usuario'=>$usuario,
                'fecha_registro'=>$fecha_registro
                ]);

            DB::commit();
            return response()->json($id_salida);
            
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            // return response()->json($e);
        }
    }

    public function anular_ingreso(Request $request){

        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $msj = '';

            $ing = DB::table('almacen.mov_alm')
            ->where('id_mov_alm', $request->id_mov_alm)
            ->first();
            //si el ingreso no esta revisado
            if ($ing->revisado == 0){
                //si existe una orden
                if ($request->id_oc !== null) {
                    //Verifica si ya tiene transferencia u orden de despacho
                    $req = DB::table('logistica.log_ord_compra')
                    ->select('alm_req.id_requerimiento','trans.id_transferencia','orden_despacho.id_od')
                    ->join('almacen.alm_req','alm_req.id_requerimiento','=','log_ord_compra.id_requerimiento')
                    ->join('almacen.guia_com','guia_com.id_oc','=','log_ord_compra.id_orden_compra')
                    ->leftJoin('almacen.guia_ven', function($join)
                    {   $join->on('guia_ven.id_guia_com', '=', 'guia_com.id_guia');
                        $join->where('guia_ven.estado','!=', 7);
                    })
                    ->leftJoin('almacen.trans', function($join)
                    {   $join->on('trans.id_guia_ven', '=', 'guia_ven.id_guia_ven');
                        $join->where('trans.estado','!=', 7);
                    })
                    ->leftJoin('almacen.orden_despacho', function($join)
                    {   $join->on('orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento');
                        $join->where('orden_despacho.estado','!=', 7);
                    })
                    ->where('id_orden_compra',$request->id_oc)
                    ->first();

                    if ($req !== null && $req->id_requerimiento !== null && $req->id_transferencia == null && $req->id_od == null){
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
                        //Quita estado de la orden
                        DB::table('logistica.log_ord_compra')
                        ->where('id_orden_compra',$request->id_oc)
                        ->update(['en_almacen' => false]);
    
                        if ($req->id_requerimiento !== null){
                            //Requerimiento regresa a atendido
                            DB::table('almacen.alm_req')
                            ->where('id_requerimiento',$req->id_requerimiento)
                            ->update(['estado'=>5]);//Atendido
    
                            DB::table('almacen.alm_det_req')
                            ->where('id_requerimiento',$req->id_requerimiento)
                            ->update(['estado'=>5]);//Atendido
                            //Agrega accion en requerimiento
                            DB::table('almacen.alm_req_obs')
                            ->insert([  'id_requerimiento'=>$req->id_requerimiento,
                                        'accion'=>'INGRESO ANULADO',
                                        'descripcion'=>'Ingreso por Compra Anulado. Requerimiento regresa a Atendido.',
                                        'id_usuario'=>$id_usuario,
                                        'fecha_registro'=>date('Y-m-d H:i:s')
                                ]);
                        }
                    } else {
                        $msj = 'El ingreso ya fue procesado con una Orden de Despacho o una Transferencia';
                    }
                // } else {
                //     $ordenes = DB::table('almacen.guia_com_oc')
                //     ->where('id_guia_com', $request->id_guia_com)
                //     ->get();
                    
                //     if ($ordenes !== null && isset($ordenes)){
                //         //Anula la Orden
                //         DB::table('almacen.guia_com_oc')
                //         ->where('id_guia_com', $request->id_guia_com)
                //         ->update([ 'estado' => 7 ]);
                        
                //         foreach($ordenes as $oc){
                //             DB::table('logistica.log_ord_compra')
                //             ->where('id_orden_compra',$oc->id_oc)
                //             ->update(['en_almacen' => false]);
                //         }
                //     } 
                } else {
                    $msj = 'No existe una orden enlazada';
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

}