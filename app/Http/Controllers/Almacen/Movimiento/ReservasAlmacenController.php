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
                'alm_prod.codigo as codigo_producto',
                'alm_prod.part_number',
                'alm_prod.descripcion',
                'alm_almacen.descripcion as almacen',
                'alm_req.id_requerimiento',
                'alm_req.codigo as codigo_req',
                'alm_req.estado as estado_requerimiento',
                'alm_req.tiene_transformacion',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'sis_usua.nombre_corto',
                'guia_com.serie',
                'guia_com.numero',
                'trans.codigo as codigo_transferencia',
                'transformacion.codigo as codigo_transformado',
                'materia.codigo as codigo_materia',
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_reserva.id_producto')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_reserva.id_almacen_reserva')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_reserva.estado')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_reserva.usuario_registro')
            ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'alm_reserva.id_detalle_requerimiento')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftjoin('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'alm_reserva.id_guia_com_det')
            ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->leftjoin('almacen.trans_detalle', 'trans_detalle.id_trans_detalle', '=', 'alm_reserva.id_trans_detalle')
            ->leftjoin('almacen.trans', 'trans.id_transferencia', '=', 'trans_detalle.id_transferencia')
            ->leftjoin('almacen.transfor_transformado', 'transfor_transformado.id_transformado', '=', 'alm_reserva.id_transformado')
            ->leftjoin('almacen.transformacion', 'transformacion.id_transformacion', '=', 'transfor_transformado.id_transformacion')
            ->leftjoin('almacen.transfor_materia', 'transfor_materia.id_materia', '=', 'alm_reserva.id_materia')
            ->leftjoin('almacen.transformacion as materia', 'materia.id_transformacion', '=', 'transfor_materia.id_transformacion');
        // ->get();

        // return response()->json($lista);
        return datatables($lista)->toJson();
    }

    function anularReserva($id_reserva)
    {
        $rspta = DB::table('almacen.alm_reserva')
            ->where('id_reserva', $id_reserva)
            ->update(['estado' => 7]);

        return response()->json($rspta);
    }
}
