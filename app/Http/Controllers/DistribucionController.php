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
    function view_requerimientosPendientes(){
        return view('almacen/distribucion/requerimientosPendientes');
    }
    public function listarRequerimientosPendientes(){
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*','sis_usua.nombre_corto','adm_grupo.descripcion as grupo')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->join('administracion.adm_grupo','adm_grupo.id_grupo','=','alm_req.id_grupo')
            ->where('alm_req.estado',5)
            ->get();
            $output['data'] = $data;
        return response()->json($output);
    }
    public function verDetalleRequerimiento($id_requerimiento){
        $data = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.*','alm_almacen.descripcion as almacen_descripcion',
            DB::raw("(CASE 
                    WHEN alm_item.id_item isNUll THEN alm_det_req.descripcion_adicional 
                    WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
                    WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
                    WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
                    ELSE 'nulo' END) AS descripcion_item
                    "),
                DB::raw("(CASE 
                    WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.codigo 
                    WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.codigo 
                    WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.codigo 
                    ELSE 'nulo' END) AS codigo_item
                    "),
                DB::raw("(CASE 
                    WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.abreviatura
                    WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'serv' 
                    WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'und' 
                    ELSE 'nulo' END) AS unidad_medida_item
                    "),
                    'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
            ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
            ->leftJoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_det_req.id_almacen')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_det_req.estado')
            ->where([['alm_det_req.id_requerimiento','=',$id_requerimiento],['alm_det_req.estado','!=',7]])
            ->get();
        return response()->json($data);
    }
}