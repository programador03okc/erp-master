<?php

namespace App\Http\Controllers\Logistica\Requerimientos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AlmacenController;

use Illuminate\Support\Facades\DB;

class MapeoProductosController extends Controller
{
    function view_mapeo_productos()
    {
        $tipos = AlmacenController::mostrar_tipos_cbo();
        $clasificaciones = AlmacenController::mostrar_clasificaciones_cbo();
        $subcategorias = AlmacenController::mostrar_subcategorias_cbo();
        $categorias = AlmacenController::mostrar_categorias_cbo();
        $unidades = AlmacenController::mostrar_unidades_cbo();

        return view('logistica/requerimientos/mapeo/index', compact('tipos','clasificaciones','subcategorias','categorias','unidades'));
    }

    public function listarRequerimientos()
    {
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*','sis_usua.nombre_corto as responsable',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            'sis_sede.descripcion as sede_descripcion',
            'sis_moneda.simbolo'
            )
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
            ->leftJoin('administracion.sis_sede','sis_sede.id_sede','=','alm_req.id_sede')
            ->leftJoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','alm_req.id_moneda')
            // ->leftJoin('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->where([['alm_req.estado','=',2]])
            ->orderBy('alm_req.fecha_requerimiento','desc');

        return datatables($data)->toJson();
    }

    public function itemsRequerimiento($id)
    {
        $detalles = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.*','alm_prod.codigo','alm_prod.part_number','alm_prod.descripcion',
            'alm_und_medida.abreviatura')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            ->where([['alm_det_req.id_requerimiento','=',$id],
                     ['alm_det_req.estado','!=',7]])
            ->get();

        return response()->json($detalles);
    }
}
