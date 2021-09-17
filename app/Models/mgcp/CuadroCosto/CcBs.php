<?php

namespace App\Models\mgcp\CuadroCosto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CcBs extends Model
{
    use HasFactory;
    protected $table = 'mgcp_cuadro_costos.cc_bs';
    protected $primaryKey = 'id_cc';
    public $timestamps = false;

    public static function obtenerDetallesFilas(&$cuadroCosto)
    {
        $filas = CcBsFila::with(['bsProveedor', 'bsProveedor.proveedor'])->where('id_cc_bs', $cuadroCosto->id)->orderBy('id', 'asc')->get();
        $contenedor = new \stdClass();
        $contenedor->filas = [];
        $monedaCuadro = $cuadroCosto->moneda == 's' ? 'S/' : '$';
        $contenedor->suma_costo_compra_convertido = 0;
        $contenedor->suma_total_flete = 0;
        $contenedor->suma_costo_compra_mas_flete = 0;
        foreach ($filas as $fila) {
            $detalle = new \stdClass();
            $detalle->cantidad = $fila->cantidad ?? 0;
            $detalle->proveedor = new \stdClass();
            $detalle->proveedor->moneda = $fila->bsProveedor != null ? $fila->bsProveedor->moneda : null;
            $monedaProveedor = $detalle->proveedor->moneda == null ? '' : ($detalle->proveedor->moneda == 's' ? 'S/' : '$');
            $detalle->proveedor->precio = $fila->bsProveedor != null ? $fila->bsProveedor->precio : null;
            $detalle->proveedor->precio_format = $detalle->proveedor->precio == null ? '' : $monedaProveedor . number_format($detalle->proveedor->precio, 2);
            $detalle->proveedor->nombre = $fila->bsProveedor != null ? $fila->bsProveedor->proveedor->razon_social : null;
            $detalle->proveedor->flete = $fila->bsProveedor != null ? $fila->bsProveedor->flete : null;
            $detalle->proveedor->flete_format = $detalle->proveedor->flete == null ? '' : 'S/' . number_format($detalle->proveedor->flete, 2);
            $detalle->proveedor->plazo = $fila->bsProveedor != null ? $fila->bsProveedor->plazo : null;

            $fondoProveedor = $fila->bsProveedor != null ? $fila->bsProveedor->fondoProveedor : null;
            if ($fondoProveedor == null) {
                $valorFondoProveedor = 0;
            } else {
                if ($detalle->proveedor->moneda == $fondoProveedor->moneda) {
                    $valorFondoProveedor = $fondoProveedor->valor_unitario;
                } else {
                    $valorFondoProveedor = $detalle->proveedor->moneda == 's' ? ($fondoProveedor->valor_unitario * $cuadroCosto->tipo_cambio) : ($fondoProveedor->valor_unitario / $cuadroCosto->tipo_cambio);
                }
            }

            $detalle->proveedor->fondo_format = $fondoProveedor != null ? $fondoProveedor->descripcion . " ($fondoProveedor->valor_unitario_format)" : 'Ninguno';
            $detalle->costo_compra = $detalle->proveedor->precio == null ? 0 : ($detalle->cantidad * ($detalle->proveedor->precio - $valorFondoProveedor));
            $detalle->costo_compra_format = $monedaProveedor . number_format($detalle->costo_compra, 2);
            if ($detalle->proveedor->moneda == null) {
                $tasa = 0;
            } else {
                if ($cuadroCosto->moneda == $detalle->proveedor->moneda) {
                    $tasa = 1;
                } else {
                    if ($cuadroCosto->moneda == 's') {
                        $tasa = $cuadroCosto->tipo_cambio;
                    } else {
                        $tasa = 1 / $cuadroCosto->tipo_cambio;
                    }
                }
            }
            $tasaFlete = ($cuadroCosto->moneda == 's' ? 1 : (1 / $cuadroCosto->tipo_cambio));
            $detalle->costo_compra_convertido = $detalle->costo_compra * $tasa;
            $detalle->costo_compra_convertido_format = $monedaCuadro . number_format($detalle->costo_compra_convertido, 2);
            $contenedor->suma_costo_compra_convertido += $detalle->costo_compra_convertido;
            $detalle->total_flete = $detalle->proveedor->flete == null ? 0 : ($detalle->proveedor->flete * $detalle->cantidad * $tasaFlete);
            $detalle->total_flete_format = $monedaCuadro . number_format($detalle->total_flete, 2);
            $contenedor->suma_total_flete += $detalle->total_flete;
            $detalle->costo_compra_mas_flete = $detalle->costo_compra_convertido + ($detalle->total_flete);
            $detalle->costo_compra_mas_flete_format = $monedaCuadro . number_format($detalle->costo_compra_mas_flete, 2);
            $contenedor->suma_costo_compra_mas_flete += $detalle->costo_compra_mas_flete;
            $contenedor->filas[] = $detalle;
        }
        $contenedor->suma_costo_compra_convertido_format = $monedaCuadro . number_format($contenedor->suma_costo_compra_convertido, 2);
        $contenedor->suma_total_flete_format = $monedaCuadro . number_format($contenedor->suma_total_flete, 2);
        $contenedor->suma_costo_compra_mas_flete_format = $monedaCuadro . number_format($contenedor->suma_costo_compra_mas_flete, 2);
        return $contenedor;
    }
}
