<?php

namespace App\Http\Controllers\Logistica\Requerimientos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\Migraciones\MigrateRequerimientoSoftLinkController;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Requerimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class MapeoProductosController extends Controller
{
    function view_mapeo_productos()
    {
        $tipos = AlmacenController::mostrar_tipos_cbo();
        $clasificaciones = AlmacenController::mostrar_clasificaciones_cbo();
        $subcategorias = AlmacenController::mostrar_subcategorias_cbo();
        $categorias = AlmacenController::mostrar_categorias_cbo();
        $unidades = AlmacenController::mostrar_unidades_cbo();

        return view('logistica/requerimientos/mapeo/index', 
        compact('tipos','clasificaciones','subcategorias','categorias','unidades'));
    }

    public function listarRequerimientos()
    {
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*','sis_usua.nombre_corto as responsable',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            'sis_sede.descripcion as sede_descripcion',
            'sis_moneda.simbolo','alm_tp_req.descripcion as tipo',
            DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req AS det
                        WHERE det.id_requerimiento = alm_req.id_requerimiento
                          AND det.id_producto is null
                          AND det.id_tipo_item = 1) AS count_pendientes")
            )
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
            ->join('almacen.alm_tp_req','alm_tp_req.id_tipo_requerimiento','=','alm_req.id_tipo_requerimiento')
            ->leftJoin('administracion.sis_sede','sis_sede.id_sede','=','alm_req.id_sede')
            ->leftJoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','alm_req.id_moneda')
            ->where([['alm_req.estado','=',2]]);

        return datatables($data)->toJson();
    }

    public function itemsRequerimiento($id)
    {
        $detalles = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.*','alm_prod.codigo','alm_prod.cod_softlink','alm_prod.part_number as part_number_prod',
            'alm_prod.descripcion as descripcion_prod','alm_und_medida.abreviatura', 'sis_moneda.descripcion AS descripcion_moneda')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'alm_prod.id_moneda')
            ->where([['alm_det_req.id_requerimiento','=',$id],
                     ['alm_det_req.estado','!=',7]])
            ->get();

        return response()->json($detalles);
    }

    function esValidaLaCantidadDeProductoDuplicadosConCantidadOrigen($detalle){
        $cantidadSuperiorACantidadOrigen= 0;
        $idDetalleRequerimientoOrigenList=[];
        foreach($detalle as $det){
            if( isset($det['id_detalle_requerimiento_origen']) && $det['id_detalle_requerimiento_origen'] >0){
                $detalleOrigen= DetalleRequerimiento::find($det['id_detalle_requerimiento_origen']);
                if(!in_array($detalleOrigen->id_detalle_requerimiento,$idDetalleRequerimientoOrigenList)){
                    $idDetalleRequerimientoOrigenList[] = $detalleOrigen->id_detalle_requerimiento;
                }
            }        
        }   
        
        if(count($idDetalleRequerimientoOrigenList)>0){
            foreach ($idDetalleRequerimientoOrigenList as $idOrigen) {
                $sumCantidadProductoDescompuesto=0;
                $cantidadNuevaProductoOriginalParaDescomponer=0;
                foreach ($detalle as $d) {
                    if( isset($d['id_detalle_requerimiento_origen']) && $d['id_detalle_requerimiento_origen'] >0){
                        if($idOrigen == $d['id_detalle_requerimiento_origen']){
                            $sumCantidadProductoDescompuesto+=$d['cantidad'];
                        }
                    }
                }
                foreach ($detalle as $e) {
                    if($e['id_detalle_requerimiento']==$idOrigen){
                        $cantidadNuevaProductoOriginalParaDescomponer=$e['cantidad'];
                    }
                }

                $cantidadOrigenDeProducto=DetalleRequerimiento::find($idOrigen)->cantidad;
                if(($sumCantidadProductoDescompuesto+$cantidadNuevaProductoOriginalParaDescomponer) > $cantidadOrigenDeProducto ){
                    $cantidadSuperiorACantidadOrigen++;
                }
            }

        }
        if($cantidadSuperiorACantidadOrigen >0){
            return false;
        }else{
            return true;
        }

    }

    public function guardar_mapeo_productos(Request $request)
    {
        DB::beginTransaction();
        try {
            $id_usuario = Auth::user()->id_usuario;

            $cantidadAnulado=0;
            $cantidadItemsMapeados=0;
            $cantidadItemsTotal=0;
            $mensaje =[];
            $status_migracion_occ=null;
            
            $validacionDescomposicion =$this->esValidaLaCantidadDeProductoDuplicadosConCantidadOrigen($request->detalle);
            if($validacionDescomposicion==false){
                return response()->json(['response' => 'warning','status_migracion_occ'=>null,
                'mensaje'=>'la cantidad total del producto original más (+) cantidad de producto descompuesto no puede superar la cantidad original',
                'estado_requerimiento'=>null,'cantidad_items_mapeados'=>0,'cantidad_total_items'=>0]);
            }

            $idDetalleRequerimientoOrigenList=[];
            foreach($request->detalle as $det){
    
                // anular items si existe
                if($det['id_detalle_requerimiento'] >0 && $det['estado'] =='7'){
                     DB::table('almacen.alm_det_req')->where('id_detalle_requerimiento', $det['id_detalle_requerimiento'])->update(['estado' => 7]); // estado anulado
                    $cantidadAnulado++;
                }   

                if( isset($det['id_detalle_requerimiento_origen']) && $det['id_detalle_requerimiento_origen'] >0){

                    $detalleOrigen= DetalleRequerimiento::find($det['id_detalle_requerimiento_origen']);
                    $idDetalleRequerimientoOrigenList[]=$detalleOrigen->id_detalle_requerimiento;

                    DB::table('almacen.alm_det_req')->insertGetId(
                        [
                            'id_requerimiento' => $detalleOrigen->id_requerimiento,
                            'part_number' => $det['part_number']??null,
                            'id_producto' => $det['id_producto']??null,
                            'cantidad' => $det['cantidad'],
                            // 'descripcion' => mb_convert_encoding((strtoupper($det['descripcion'])), 'UTF-8', 'UTF-8'),
                            'descripcion' => $det['descripcion'],
                            'id_unidad_medida' => $det['id_unidad_medida'],
                            'id_moneda' => $detalleOrigen->id_unidad_medida,
                            'estado' => 1,
                            'id_cc_am_filas' => $detalleOrigen->id_cc_am_filas,
                            'id_tipo_item' => $detalleOrigen->id_tipo_item,
                            'tiene_transformacion' => $detalleOrigen->tiene_transformacion,
                            'proveedor_id' => $detalleOrigen->proveedor_id,
                            'partida' => $detalleOrigen->partida,
                            'centro_costo_id' => $detalleOrigen->centro_costo_id,
                            'precio_unitario' => $detalleOrigen->precio_unitario,
                            'subtotal' => $detalleOrigen->subtotal,
                            'motivo' => $detalleOrigen->motivo,
                            'fecha_registro' => date('Y-m-d H:i:s')
                        ],
                            'id_detalle_requerimiento'
                        );

                }

                if (($det['id_producto'] !== null && $det['estado'] != 7)){
                    if(!preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $det['id_detalle_requerimiento']) >=1){
                        DB::table('almacen.alm_det_req')
                        ->where('id_detalle_requerimiento',$det['id_detalle_requerimiento'])
                        ->update(['id_producto'=>$det['id_producto']]);
                    }
                } 

                else if (($det['id_categoria'] !== null && $det['id_subcategoria'] !== null
                        && $det['id_clasif'] !== null && $det['id_producto'] == null && $det['estado'] != 7)){
                    $id_producto = DB::table('almacen.alm_prod')->insertGetId(
                        [
                            'part_number' => $det['part_number'],
                            'id_categoria' => $det['id_categoria'],
                            'id_subcategoria' => $det['id_subcategoria'],
                            'id_clasif' => $det['id_clasif'],
                            'descripcion' => mb_convert_encoding((strtoupper($det['descripcion'])), 'UTF-8', 'UTF-8'),
                            'id_unidad_medida' => $det['id_unidad_medida'],
                            'id_moneda' => $det['id_moneda'],
                            'series' => $det['series'],
                            'id_usuario' => $id_usuario,
                            'estado' => 1,
                            'fecha_registro' => date('Y-m-d H:i:s')
                        ],
                            'id_producto'
                        );
                    $codigo = AlmacenController::leftZero(7, $id_producto);
    
                    DB::table('almacen.alm_prod')
                    ->where('id_producto',$id_producto)
                    ->update(['codigo'=>$codigo]);

                    DB::table('almacen.alm_det_req')
                    ->where('id_detalle_requerimiento',$det['id_detalle_requerimiento'])
                    ->update(['id_producto'=>$id_producto]);
                }
            }

            if(count($idDetalleRequerimientoOrigenList)>0){
                foreach ($request->detalle as $det) {
                    if(in_array($det['id_detalle_requerimiento'],$idDetalleRequerimientoOrigenList)){
                        $detalleRequerimientoOrigen= DetalleRequerimiento::find($det['id_detalle_requerimiento']);
                        $detalleRequerimientoOrigen->cantidad= $det['cantidad'];
                        $detalleRequerimientoOrigen->save();
                    }
                }
            }

            $DetalleRequerimiento= DetalleRequerimiento::find($request->detalle[0]['id_detalle_requerimiento']);
            $cantidades = $this->obtenerCantidadProductosMapeados($DetalleRequerimiento->id_requerimiento);
            $cantidadItemsMapeados=$cantidades['cantidadMapeados'];
            $cantidadItemsTotal=$cantidades['cantidadTotal'];

        
            $estadoRequerimiento= null;
            if($cantidadItemsMapeados >0){
                $mensaje[]='Productos mapeados con éxito';

            }
            if($validacionDescomposicion ==true){
                $mensaje[]='Productos guardado éxito';

            }
            if($cantidadAnulado >0){
                $mensaje[]=' se anulo ('.$cantidadAnulado.') item(s)';
                //TODO: revisar si actualizar estado de requerimiento
                $estadoRequerimiento= Requerimiento::actualizarEstadoRequerimientoAtendido('ACTUALIZAR',[$DetalleRequerimiento->id_requerimiento]);
            }
    


            DB::commit();
            $detalleRequerimiento= DetalleRequerimiento::find($request->detalle[0]['id_detalle_requerimiento']);
            $todoDetalleRequerimientoNoMapeados=DetalleRequerimiento::where([['id_requerimiento',$detalleRequerimiento->id_requerimiento],['entrega_cliente',true],['estado','!=',7]])->count();
            $todoDetalleRequerimientoMapeados=DetalleRequerimiento::where([['id_requerimiento',$detalleRequerimiento->id_requerimiento],['entrega_cliente',true],['estado','!=',7]])->whereNotNull('id_producto')->count();


            return response()->json(['response' => 'ok','status_migracion_occ'=>$status_migracion_occ,'mensaje'=>$mensaje,'estado_requerimiento'=>$estadoRequerimiento,'cantidad_items_mapeados'=>$cantidadItemsMapeados,'cantidad_total_items'=>$cantidadItemsTotal]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['response' => null,'status_migracion_occ'=>$status_migracion_occ,'estado_requerimiento'=>'null','mensaje'=>'Hubo un problema al guardar el mapeo de productos. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage(),'cantidad_items_mapeados'=>$cantidadItemsMapeados,'cantidad_total_items'=>$cantidadItemsTotal]);
        }
    }

    public function obtenerCantidadProductosMapeados($idRequerimiento){
        $cantidadProductosTotal=0;
        $cantidadProductosMapeados=0;

        $todoDetalleRequerimiento = DetalleRequerimiento::where('id_requerimiento',$idRequerimiento)->where([['id_tipo_item','=',1],['estado','!=',7]])->get();

        $cantidadProductosTotal = count($todoDetalleRequerimiento);
        foreach ($todoDetalleRequerimiento as $value) {
            if($value->id_producto >0){
                $cantidadProductosMapeados++;
            }
        }

        return ['cantidadTotal'=>$cantidadProductosTotal,'cantidadMapeados'=>$cantidadProductosMapeados];
    }

}
