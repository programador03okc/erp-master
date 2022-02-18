<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReservasAlmacenController extends Controller
{
    function viewReservasAlmacen()
    {
        // if (!Auth::user()->tieneAccion(83)) {
        //     return 'No autorizado';
        // }

        return view('almacen/reservas/reservasAlmacen');
    }

    function listarReservasAlmacen()
    {
        $lista = DB::table('almacen.alm_reserva')
            ->select(
                'alm_reserva.*',
                'alm_prod.part_number',
                'alm_prod.descripcion',
                'alm_almacen.descripcion as almacen',
                'alm_req.codigo as codigo_req',
                'alm_req.estado as estado_requerimiento',
                'alm_req.tiene_transformacion',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'sis_usua.nombre_corto',
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_reserva.id_producto')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_reserva.id_almacen_reserva')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_reserva.estado')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_reserva.usuario_registro')
            ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'alm_reserva.id_detalle_requerimiento')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento');
        // ->get();

        // return response()->json($lista);
        return datatables($lista)->toJson();
    }
}
