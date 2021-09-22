<?php

namespace App\Http\Controllers\Logistica\Requerimientos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AlmacenController;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            ->select('alm_det_req.*','alm_prod.codigo','alm_prod.part_number as part_number_prod',
            'alm_prod.descripcion as descripcion_prod','alm_und_medida.abreviatura')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            ->where([['alm_det_req.id_requerimiento','=',$id],
                     ['alm_det_req.estado','!=',7]])
            ->get();

        return response()->json($detalles);
    }

    public function guardar_mapeo_productos(Request $request)
    {
        DB::beginTransaction();
        try {
            $id_usuario = Auth::user()->id_usuario;
            // $datax = [];
            // foreach($request->detalle as $det){
            //     $id_prod = $det['id_producto'];
            //     $datax[] = [$id_prod];
            // }
            // dd($datax);
            // exit();
            $cantidadItemsHabilitados=0;
            foreach($request->detalle as $det){
                if($det['estado']!=7){
                    $cantidadItemsHabilitados++;
                }
            }
            $cantidadItemsMapeados=0;
            foreach($request->detalle as $det){
    
                if ($det['id_producto'] !== null && $det['estado'] != 7){
                    DB::table('almacen.alm_det_req')
                    ->where('id_detalle_requerimiento',$det['id_detalle_requerimiento'])
                    ->update(['id_producto'=>$det['id_producto']]);

                    $cantidadItemsMapeados++;
                } 
                else if ($det['id_categoria'] !== null && $det['id_subcategoria'] !== null
                        && $det['id_clasif'] !== null && $det['id_producto'] == null && $det['estado'] != 7){
                    $cantidadItemsMapeados++;
                    $id_producto = DB::table('almacen.alm_prod')->insertGetId(
                        [
                            'part_number' => $det['part_number'],
                            'id_categoria' => $det['id_categoria'],
                            'id_subcategoria' => $det['id_subcategoria'],
                            'id_clasif' => $det['id_clasif'],
                            'descripcion' => strtoupper($det['descripcion']),
                            'id_unidad_medida' => $det['id_unidad_medida'],
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
            DB::commit();
            $rpta = 'ok';
        } catch (\Throwable $th) {
            DB::rollBack();
            $rpta = 'null';
        }
        return response()->json(array('response' => $rpta,'cantidad_items_mapeados'=>$cantidadItemsMapeados,'cantidad_total_items'=>$cantidadItemsHabilitados), 200);
    }
    public function anular_item(Request $request)
    {
        DB::beginTransaction();
        try {
            $rpta='null';
            $cantidadAnulado=0;
            if(count($request->detalleRequerimiento)>0){
                foreach($request->detalleRequerimiento as $det){
                    if($det['id_detalle_requerimiento'] >0 && $det['estado'] =='7'){
                        $det = DB::table('almacen.alm_det_req')->where('id_detalle_requerimiento', $det['id_detalle_requerimiento'])->update(['estado' => 7]); // estado anulado
                        $cantidadAnulado++;
                    }           
                    
                }
                if($cantidadAnulado>0){
                    $rpta='ok';
                }else{
                    $rpta='sin cambios';
                }
            }
            
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $rpta = 'null';
        }
        return response()->json(array('response' => $rpta), 200);
    }

}
