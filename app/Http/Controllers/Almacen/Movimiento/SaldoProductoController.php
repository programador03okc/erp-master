<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SaldoProductoController extends Controller
{
    public function listarProductosAlmacen(Request $request)
    {
        $data = DB::table('almacen.alm_prod_ubi')
            ->select(
                'alm_prod_ubi.id_producto',
                'alm_prod_ubi.id_almacen',
                'alm_prod.codigo',
                'alm_prod.cod_softlink',
                'alm_prod.part_number',
                'alm_prod.descripcion',
                'alm_und_medida.abreviatura',
                // 'sis_moneda.simbolo',
                'alm_prod.id_moneda',
                'alm_prod.id_unidad_medida',

                DB::raw("(SELECT SUM(alm_reserva.stock_comprometido) FROM almacen.alm_reserva 
                WHERE alm_reserva.id_producto = alm_prod_ubi.id_producto
                AND alm_reserva.id_almacen_reserva = alm_prod_ubi.id_almacen
                AND (alm_reserva.estado != 7 AND alm_reserva.estado != 5) ) as stock_comprometido"),

                DB::raw("(SELECT SUM(mov_alm_det.cantidad) FROM almacen.mov_alm_det
                JOIN almacen.mov_alm on(
                    mov_alm_det.id_mov_alm = mov_alm.id_mov_alm
                )
                WHERE mov_alm_det.id_producto = alm_prod_ubi.id_producto
                AND mov_alm.id_almacen = alm_prod_ubi.id_almacen
                AND (mov_alm.id_tp_mov = 0 OR mov_alm.id_tp_mov = 1)) AS suma_ingresos"),

                DB::raw("(SELECT SUM(mov_alm_det.cantidad) FROM almacen.mov_alm_det
                JOIN almacen.mov_alm on(
                    mov_alm_det.id_mov_alm = mov_alm.id_mov_alm
                )
                WHERE mov_alm_det.id_producto = alm_prod_ubi.id_producto
                AND mov_alm.id_almacen = alm_prod_ubi.id_almacen
                AND (mov_alm.id_tp_mov = 2)) AS suma_salidas")
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_prod_ubi.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where([
                ['alm_prod_ubi.estado', '=', 1],
                ['alm_prod_ubi.id_almacen', '=', $request->id_almacen_origen_nueva]
            ]);

        // $lista = [];

        // foreach ($data as $det) {
        //     $stock = (new SalidaPdfController)->obtenerSaldo($det->id_producto, $det->id_almacen, '2022-01-01', new Carbon()); //falta corregir la fecha
        //     array_push(
        //         $lista,
        //         [
        //             'id_producto' => $det->id_producto,
        //             'codigo' => $det->codigo,
        //             'cod_softlink' => $det->cod_softlink,
        //             'part_number' => $det->part_number,
        //             'descripcion' => $det->descripcion,
        //             'stock' => $stock,
        //             'stock_comprometido' => $det->stock_comprometido,
        //             'id_almacen' => $det->id_almacen,
        //             'abreviatura' => $det->abreviatura,
        //         ]
        //     );
        // }
        return datatables($data)->toJson();
    }
}
