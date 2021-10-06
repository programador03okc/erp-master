<?php

namespace App\Http\Controllers\Logistica\Requerimientos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AlmacenController;
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

            $cantidadAnulado=0;
            $cantidadItemsMapeados=0;
            $cantidadItemsTotal=0;
            $mensaje =[];
            foreach($request->detalle as $det){
    
                // anular items si existe
                if($det['id_detalle_requerimiento'] >0 && $det['estado'] =='7'){
                     DB::table('almacen.alm_det_req')->where('id_detalle_requerimiento', $det['id_detalle_requerimiento'])->update(['estado' => 7]); // estado anulado
                    $cantidadAnulado++;
                }   

                if ($det['id_producto'] !== null && $det['estado'] != 7){
                    DB::table('almacen.alm_det_req')
                    ->where('id_detalle_requerimiento',$det['id_detalle_requerimiento'])
                    ->update(['id_producto'=>$det['id_producto']]);

                } 
                else if ($det['id_categoria'] !== null && $det['id_subcategoria'] !== null
                        && $det['id_clasif'] !== null && $det['id_producto'] == null && $det['estado'] != 7){
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

            $DetalleRequerimiento= DetalleRequerimiento::find($request->detalle[0]['id_detalle_requerimiento']);
            $cantidades = $this->obtenerCantidadItemsMapeados($DetalleRequerimiento->id_requerimiento);
            $cantidadItemsMapeados=$cantidades['cantidadMapeados'];
            $cantidadItemsTotal=$cantidades['cantidadTotal'];

          

            if($cantidadItemsMapeados >0){
                $mensaje[]='Productos mapeados con éxito';

            }
            if($cantidadAnulado >0){
                $mensaje[]=' se anulo ('.$cantidadAnulado.') item(s)';
                //TODO: revisar si actualizar estado de requerimiento
                Requerimiento::actualizarEstadoRequerimientoAtendido([$DetalleRequerimiento->id_requerimiento]);
            }
    


            DB::commit();
            return response()->json(array('response' => 'ok','mensaje'=>$mensaje,'cantidad_items_mapeados'=>$cantidadItemsMapeados,'cantidad_total_items'=>$cantidadItemsTotal), 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(array('response' => 'null','mensaje'=>'Hubo un problema al guardar el mapeo de productos. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage(),'cantidad_items_mapeados'=>$cantidadItemsMapeados,'cantidad_total_items'=>$cantidadItemsTotal), 200);
        }
    }

    public function obtenerCantidadItemsMapeados($idRequerimiento){
        $cantidadItemsTotal=0;
        $cantidadItemsMapeados=0;

        $todoDetalleRequerimiento = DetalleRequerimiento::where('id_requerimiento',$idRequerimiento)->where('estado','!=',7)->get();

        $cantidadItemsTotal = count($todoDetalleRequerimiento);
        foreach ($todoDetalleRequerimiento as $key => $value) {
            if($value->id_producto >0){
                $cantidadItemsMapeados++;
            }
        }

        return ['cantidadTotal'=>$cantidadItemsTotal,'cantidadMapeados'=>$cantidadItemsMapeados];
    }

}
