<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SaldoProductoController extends Controller
{
    public function listarProductosAlmacen(Request $request)
    {
        $data = DB::table('almacen.alm_prod_ubi')
            ->select(
                'alm_prod_ubi.*',
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
                AND (alm_reserva.estado = 1 OR alm_reserva.estado = 17) ) as stock_comprometido")
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_prod_ubi.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where([
                ['alm_prod_ubi.estado', '=', 1],
                ['alm_prod_ubi.id_almacen', '=', $request->id_almacen_origen_nueva]
            ]);
        return datatables($data)->toJson();
    }
}
