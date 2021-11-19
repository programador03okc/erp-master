<?php

namespace App\Models\mgcp\CuadroCosto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CcGg extends Model
{
    // use HasFactory;
    protected $table = 'mgcp_cuadro_costos.cc_gg';
    protected $primaryKey = 'id_cc';
    public $timestamps = false;

    public static function obtenerDetallesFilas(&$cuadroCosto)
    {
        $filas = CcGgFila::where('id_cc_gg', $cuadroCosto->id)->orderBy('id', 'asc')->get();
        $contenedor = new \stdClass();
        $contenedor->filas = [];
        $monedaCuadro = $cuadroCosto->moneda == 's' ? 'S/' : '$';
        $contenedor->suma_subtotal = 0;
        $contenedor->suma_otros = 0;
        $contenedor->suma_total = 0;
        foreach ($filas as $fila) {
            $tasa = 1;
            if ($cuadroCosto->moneda == 'd') {
                $tasa = 1 / $cuadroCosto->tipo_cambio;
            }
            $detalle = new \stdClass();
            $detalle->subtotal = $fila->personas * $fila->tiempo * $fila->costo * ($fila->porcentaje_participacion / 100) * $tasa;
            switch ($fila->id_categoria_gasto) {
                case 2:
                    $detalle->otros = $detalle->subtotal * 0.09;
                    break;
                case 6:
                    $detalle->otros = $detalle->subtotal * 0.46;
                    break;
                default:
                $detalle->otros = 0;
                    break;
            }
            $detalle->total=$detalle->subtotal+$detalle->otros;
            $detalle->subtotal_format=$monedaCuadro.number_format($detalle->subtotal,2);
            $detalle->total_format=$monedaCuadro.number_format($detalle->total,2);
            $detalle->otros_format=$monedaCuadro.number_format($detalle->otros,2);
            $contenedor->suma_subtotal+=$detalle->subtotal;
            $contenedor->suma_otros+=$detalle->otros;
            $contenedor->suma_total+=$detalle->total;
            $contenedor->filas[] = $detalle;
        }
        $contenedor->suma_subtotal_format=$monedaCuadro.number_format($contenedor->suma_subtotal,2);
        $contenedor->suma_otros_format=$monedaCuadro.number_format($contenedor->suma_otros,2);
        $contenedor->suma_total_format=$monedaCuadro.number_format($contenedor->suma_total,2);
        return $contenedor;
    }
}
