<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Almacen\Almacen;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Requerimiento;
use Illuminate\Support\Facades\DB;

class ReservasAlmacenController extends Controller
{
    function viewReservasAlmacen()
    {
        // if (!Auth::user()->tieneAccion(83)) {
        //     return 'No autorizado';
        // }
        $almacenes = Almacen::where('estado', 1)->orderBy('codigo')->get();
        return view('almacen/reservas/reservasAlmacen', compact('almacenes'));
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
                'alm_req.id_tipo_requerimiento',
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

    // function anularReserva($id_reserva, $id_detalle)
    // {
    //     $rspta = DB::table('almacen.alm_reserva')
    //         ->where('id_reserva', $id_reserva)
    //         ->update(['estado' => 7]);

    //     return response()->json($rspta);
    // }
    function anularReserva(Request $request)

    {
        $id_reserva = $request->id;
        $id_detalle=$request->id_detalle;


        $rspta = DB::table('almacen.alm_reserva')->where('id_reserva', $id_reserva)->update(['estado' => 7]);
        $Requerimiento = DB::table('almacen.alm_req')
        ->join('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento')
        ->where('alm_det_req.id_detalle_requerimiento', $id_detalle)->first();
        $nuevoEstado=[];

        if (intval($request->id_tipo_requerimiento)!==4) {
            DetalleRequerimiento::actualizarEstadoDetalleRequerimientoAtendido($id_detalle);
            $nuevoEstado = Requerimiento::actualizarEstadoRequerimientoAtendido('ANULAR', [$Requerimiento->id_requerimiento]);
        }

        return response()->json(array('respuesta' => $rspta, 'id_req' => $Requerimiento->id_requerimiento, 'estado' => $nuevoEstado));

    }

    function actualizarReserva(Request $request)
    {
        $rspta = DB::table('almacen.alm_reserva')
            ->where('id_reserva', $request->id_reserva)
            ->update([
                'id_almacen_reserva' => $request->id_almacen_reserva,
                'stock_comprometido' => $request->stock_comprometido,
            ]);

        return response()->json($rspta);
    }

    function actualizarReservas()
    {
        $lista = DB::table('almacen.alm_reserva')
            ->where([['alm_reserva.estado', '=', 1]])
            ->get();

        $reservas_actualizadas = 0;
        $lista_reservas_actualizadas = [];

        foreach ($lista as $reserva) {
            //Cantidad atendida con guias
            $atendido = DB::table('almacen.alm_reserva')
                ->select(DB::raw('SUM(guia_ven_det.cantidad) as cantidad_atendida'))
                ->where([
                    ['alm_reserva.id_detalle_requerimiento', '=', $reserva->id_detalle_requerimiento],
                    ['alm_reserva.id_almacen_reserva', '=', $reserva->id_almacen_reserva]
                ])
                ->join('almacen.orden_despacho_det', 'orden_despacho_det.id_detalle_requerimiento', '=', 'alm_reserva.id_detalle_requerimiento')
                ->join('almacen.guia_ven_det', function ($join) {
                    $join->on('guia_ven_det.id_od_det', '=', 'orden_despacho_det.id_od_detalle');
                    $join->where('guia_ven_det.estado', '!=', 7);
                })
                ->first();

            $reservas_pendientes = DB::table('almacen.alm_reserva')
                ->select('alm_reserva.*', 'alm_req.codigo as codigo_req')
                ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'alm_reserva.id_detalle_requerimiento')
                ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
                ->where([
                    ['alm_reserva.id_detalle_requerimiento', '=', $reserva->id_detalle_requerimiento],
                    ['alm_reserva.id_almacen_reserva', '=', $reserva->id_almacen_reserva],
                    ['alm_reserva.estado', '=', 1],
                ])
                ->get();

            $cantidad_acumulada = 0;

            foreach ($reservas_pendientes as $res) {
                $cantidad_acumulada += $res->stock_comprometido;

                if ($atendido->cantidad_atendida >= $cantidad_acumulada) {
                    //atiende la reserva
                    DB::table('almacen.alm_reserva')
                        ->where('id_reserva', $res->id_reserva)
                        ->update(['estado' => 5]);

                    $reservas_actualizadas++;

                    array_push($lista_reservas_actualizadas, [
                        'codigo_requerimiento' => $res->codigo_req,
                        'codigo_reserva' => $res->codigo,
                        'codigo_req' => $res->codigo_req,
                        'cantidad_atendida_en_guias_ven' => $atendido->cantidad_atendida,
                        'stock_comprometido' => $res->stock_comprometido,
                    ]);
                }
            }
        }
        return response()->json([
            'reservas_actualizadas' => $reservas_actualizadas,
            'lista_reservas_actualizadas' => $lista_reservas_actualizadas
        ]);
    }
}
