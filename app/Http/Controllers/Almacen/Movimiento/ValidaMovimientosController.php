<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ValidaMovimientosController extends Controller
{
    public static function validaNegativosHistoricoKardex($id_producto, $id_almacen, $anio)
    {
        $detalles = DB::table('almacen.mov_alm_det')
            ->select('mov_alm.fecha_emision', 'mov_alm.id_tp_mov', 'mov_alm_det.cantidad')
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            ->where([
                ['mov_alm_det.id_producto', '=', $id_producto],
                ['mov_alm.id_almacen', '=', $id_almacen],
                ['mov_alm_det.estado', '!=', 7]
            ])
            ->whereYear('mov_alm.fecha_emision', $anio)
            ->orderBy('mov_alm.fecha_emision', 'asc')
            ->get();

        $alerta_negativo = 0;
        $saldo = 0;

        foreach ($detalles as $det) {
            if ($det->id_tp_mov == 0 || $det->id_tp_mov == 1) {
                $saldo += $det->cantidad;
            } else if ($det->id_tp_mov == 2) {
                $saldo -= $det->cantidad;
            }
            $alerta_negativo = ($saldo < 0 ? $alerta_negativo + 1 : $alerta_negativo);
        }

        return $alerta_negativo;
    }
}
