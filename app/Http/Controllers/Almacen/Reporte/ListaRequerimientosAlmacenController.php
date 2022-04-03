<?php

namespace App\Http\Controllers\Almacen\Reporte;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ListaRequerimientosAlmacenController extends Controller
{
    function viewRequerimientosAlmacen()
    {
        return view('almacen/reportes/requerimientosAlmacen');
    }

    function listarRequerimientosAlmacen()
    {
        $lista = DB::table('almacen.alm_req')
            ->select(
                'alm_req.*',
                'sis_grupo.descripcion as grupo_descripcion',
                'alm_almacen.descripcion as almacen_descripcion',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'sis_usua.nombre_corto',
                'estado_despacho.estado_doc as estado_despacho_descripcion'
            )
            ->join('configuracion.sis_grupo', 'sis_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_req.id_almacen')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.estado')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->join('administracion.adm_estado_doc as estado_despacho', 'estado_despacho.id_estado_doc', '=', 'alm_req.estado_despacho')
            // ->where([['alm_req.estado', '!=', 7]])
            ->get();

        return datatables($lista)->toJson();
    }
}
