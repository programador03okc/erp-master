<?php

namespace App\Http\Controllers\Almacen\Reporte;

use App\Http\Controllers\Controller;
use App\Models\Almacen\Almacen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaldosController extends Controller
{
    function view_saldos()
    {
        $almacenes = DB::table('almacen.alm_almacen')
            ->select('alm_almacen.*')
            ->where('alm_almacen.estado', 1)
            ->orderBy('codigo')
            ->get();
        return view('almacen/reportes/saldos', compact('almacenes'));
    }

    public function listar_saldos($almacen)
    {
        $data = DB::table('almacen.alm_prod_ubi')
            ->select(
                'alm_prod_ubi.*',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_und_medida.abreviatura',
                'alm_prod.part_number',
                'sis_moneda.simbolo',
                'alm_prod.id_moneda',
                'alm_prod.id_unidad_medida',
                'alm_almacen.descripcion as almacen_descripcion',
                DB::raw("(SELECT SUM(alm_reserva.stock_comprometido) FROM almacen.alm_reserva 
                WHERE alm_reserva.id_producto = alm_prod_ubi.id_producto
                AND alm_reserva.id_almacen_reserva = alm_prod_ubi.id_almacen
                AND (alm_reserva.estado = 1 OR alm_reserva.estado = 17) ) as cantidad_reserva")
                // DB::raw("(SELECT SUM(alm_reserva.stock_comprometido) FROM almacen.alm_reserva 
                // INNER JOIN almacen.alm_det_req ON(
                //     alm_reserva.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento
                //     AND alm_det_req.id_producto = alm_prod_ubi.id_producto 
                // ) 
                // WHERE alm_reserva.id_almacen_reserva = alm_prod_ubi.id_almacen
                // AND alm_reserva.estado = 1 ) as cantidad_reserva")
                // DB::raw("(SELECT SUM(alm_det_req.cantidad) FROM almacen.alm_det_req 
                // WHERE ( alm_det_req.estado=19 or alm_det_req.estado=28 or alm_det_req.estado=27
                //         or alm_det_req.estado=22)
                // AND alm_det_req.id_producto=alm_prod_ubi.id_producto 
                // AND alm_det_req.id_almacen_reserva=alm_prod_ubi.id_almacen ) as cantidad_reserva")
            )
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_prod_ubi.id_almacen')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_prod_ubi.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftjoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'alm_prod.id_moneda')
            ->where([['alm_prod_ubi.estado', '=', 1]]);

        if ($almacen == '0') {
            $array_almacen = $this->almacenesPorUsuarioArray();
            $query = $data->whereIn('alm_prod_ubi.id_almacen', $array_almacen)->get();
        } else {
            $query = $data->where('alm_prod_ubi.id_almacen', $almacen)->get();
        }

        $nueva_data = [];

        foreach ($query as $d) {
            // if ($d->stock !== '0') {
            $nuevo = [
                'id_prod_ubi' => $d->id_prod_ubi,
                'id_producto' => $d->id_producto,
                'id_almacen' => $d->id_almacen,
                'codigo' => $d->codigo,
                'part_number' => $d->part_number,
                'descripcion' => $d->descripcion,
                'abreviatura' => $d->abreviatura,
                'id_unidad_medida' => $d->id_unidad_medida,
                'stock' => $d->stock,
                'valorizacion' => $d->valorizacion,
                'simbolo' => $d->simbolo,
                'id_moneda' => $d->id_moneda,
                'costo_promedio' => round($d->costo_promedio, 4, PHP_ROUND_HALF_UP),
                'cantidad_reserva' => $d->cantidad_reserva,
                'almacen_descripcion' => $d->almacen_descripcion,
            ];
            array_push($nueva_data, $nuevo);
            // }
        }
        $output['data'] = $nueva_data;
        return response()->json($output);
    }

    function almacenesPorUsuario()
    {
        return DB::table('almacen.alm_almacen_usuario')
            ->select('alm_almacen.*')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_almacen_usuario.id_almacen')
            ->where('alm_almacen_usuario.id_usuario', Auth::user()->id_usuario)
            ->where('alm_almacen_usuario.estado', 1)
            ->get();
    }

    function almacenesPorUsuarioArray()
    {
        $almacenes = $this->almacenesPorUsuario();

        $array_almacen = [];
        foreach ($almacenes as $alm) {
            $array_almacen[] = [$alm->id_almacen];
        }

        return $array_almacen;
    }

    public function verRequerimientosReservados($id, $almacen)
    {
        $detalles = DB::table('almacen.alm_reserva')
            ->select(
                'alm_reserva.stock_comprometido',
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.concepto',
                'sis_usua.nombre_corto',
                'alm_almacen.descripcion as almacen_descripcion',
                DB::raw("CONCAT(guia_com.serie,'-',guia_com.numero) as guia_com"),
                'trans.codigo as codigo_trans',
                'transformacion.codigo as codigo_transfor_materia',
                'transformado.codigo as codigo_transfor_transformado',
            )
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_reserva.id_almacen_reserva')
            ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'alm_reserva.id_detalle_requerimiento')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftjoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->leftjoin('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'alm_reserva.id_guia_com_det')
            ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->leftjoin('almacen.trans_detalle', 'trans_detalle.id_trans_detalle', '=', 'alm_reserva.id_trans_detalle')
            ->leftjoin('almacen.trans', 'trans.id_transferencia', '=', 'trans_detalle.id_transferencia')
            ->leftjoin('almacen.transfor_materia', 'transfor_materia.id_materia', '=', 'alm_reserva.id_materia')
            ->leftjoin('almacen.transformacion', 'transformacion.id_transformacion', '=', 'transfor_materia.id_transformacion')
            ->leftjoin('almacen.transfor_transformado', 'transfor_transformado.id_transformado', '=', 'alm_reserva.id_transformado')
            ->leftjoin('almacen.transformacion as transformado', 'transformado.id_transformacion', '=', 'transfor_transformado.id_transformacion')
            ->where([
                ['alm_reserva.id_producto', '=', $id],
                ['alm_reserva.id_almacen_reserva', '=', $almacen],
                ['alm_reserva.estado', '!=', 7],
                ['alm_reserva.estado', '!=', 5],
            ])
            ->get();
        return response()->json($detalles);
    }

    public function tipo_cambio_compra($fecha)
    {
        $data = DB::table('contabilidad.cont_tp_cambio')
            ->where('cont_tp_cambio.fecha', '<=', $fecha)
            ->orderBy('fecha', 'desc')
            // ->take(1)->get();
            ->first();
        return $data->compra;
    }
}
