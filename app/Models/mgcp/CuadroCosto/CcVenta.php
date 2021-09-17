<?php

namespace App\Models\mgcp\CuadroCosto;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CcVenta extends Model
{
    use HasFactory;
    protected $table = 'mgcp_cuadro_costos.cc_venta';
    protected $primaryKey = 'id_cc';
    public $timestamps = false;

    public function cuadroCosto()
    {
        return $this->belongsTo(CuadroCosto::class,'id_cc');
    }

    public function getFechaEntregaAttribute()
    {
        if ($this->attributes['fecha_entrega'] == null) {
            return '-';
        } else {
            return date_format(date_create($this->attributes['fecha_entrega']), 'd-m-Y');
        }
    }

    public function setFechaEntregaAttribute($valor)
    {
        if ($valor == null || $valor == '') {
            $this->attributes['fecha_entrega'] = null;
        } else {
            $this->attributes['fecha_entrega'] = Carbon::createFromFormat('d-m-Y', $valor)->toDateString();
        }
    }

    public static function obtenerDetallesFilas(&$cuadroCosto)
    {
        $filas = CcVentaFila::with(['ventaProveedor', 'ventaProveedor.proveedor'])->where('id_cc_venta', $cuadroCosto->id)->orderBy('id', 'asc')->get();
        $contenedor = new \stdClass();
        $contenedor->filas = [];
        $monedaCuadro = $cuadroCosto->moneda == 's' ? 'S/' : '$';
        $contenedor->suma_costo_compra_convertido = 0;
        $contenedor->suma_total_flete = 0;
        $contenedor->suma_costo_compra_mas_flete = 0;
        $contenedor->suma_precio_venta_total = 0;
        $contenedor->suma_ganancia = 0;
        //return response()->json(array('tipo' => 'success', 'data'=> $cuadroVentaFilas), 200);
        foreach ($filas as $fila) {
            $detalle = new \stdClass();
            $detalle->margen_ganancia = $fila->margen_ganancia ?? 0;
            $detalle->part_no = $fila->part_no ?? '';
            $detalle->descripcion = $fila->descripcion ?? '';
            $detalle->cantidad = $fila->cantidad ?? 0;
            $detalle->proveedor = new \stdClass();
            $detalle->proveedor->moneda = $fila->ventaProveedor != null ? $fila->ventaProveedor->moneda : null;
            $monedaProveedor = $detalle->proveedor->moneda == null ? '' : ($detalle->proveedor->moneda == 's' ? 'S/' : '$');
            $detalle->proveedor->precio = $fila->ventaProveedor != null ? $fila->ventaProveedor->precio : null;
            $detalle->proveedor->precio_format = $detalle->proveedor->precio == null ? '' : $monedaProveedor . number_format($detalle->proveedor->precio, 2);
            $detalle->proveedor->nombre = $fila->ventaProveedor != null ? $fila->ventaProveedor->proveedor->razon_social : null;
            $detalle->proveedor->flete = $fila->ventaProveedor != null ? $fila->ventaProveedor->flete : null;
            $detalle->proveedor->flete_format = $detalle->proveedor->flete == null ? '' : 'S/' . number_format($detalle->proveedor->flete, 2);
            $detalle->proveedor->plazo = $fila->ventaProveedor != null ? $fila->ventaProveedor->plazo : null;

            $fondoProveedor = $fila->ventaProveedor != null ? $fila->ventaProveedor->fondoProveedor : null;
            if ($fondoProveedor == null) {
                $valorFondoProveedor = 0;
            } else {
                if ($detalle->proveedor->moneda == $fondoProveedor->moneda) {
                    $valorFondoProveedor = $fondoProveedor->valor_unitario;
                } else {
                    $valorFondoProveedor = $detalle->proveedor->moneda == 's' ? ($fondoProveedor->valor_unitario * $cuadroCosto->tipo_cambio) : ($fondoProveedor->valor_unitario / $cuadroCosto->tipo_cambio);
                }
            }
            $detalle->proveedor->fondo_format = $fondoProveedor != null ? '<strong style="color: #CB4335">' . $fondoProveedor->descripcion . " ($fondoProveedor->valor_unitario_format)</strong>" : 'Ninguno';

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
            $detalle->precio_venta_unitario = $detalle->proveedor->precio == null ? 0 : ($detalle->proveedor->precio - $valorFondoProveedor) * $tasa * (1 / ((100 - $detalle->margen_ganancia) / 100)) + ($detalle->proveedor->flete * $tasaFlete);
            $detalle->precio_venta_unitario_format = $monedaCuadro . number_format($detalle->precio_venta_unitario, 2);
            $detalle->precio_venta_total = $detalle->precio_venta_unitario * $detalle->cantidad;
            $detalle->precio_venta_total_format = $monedaCuadro . number_format($detalle->precio_venta_total, 2);
            $contenedor->suma_precio_venta_total += $detalle->precio_venta_total;
            $detalle->ganancia = $detalle->precio_venta_total - $detalle->costo_compra_mas_flete;
            $detalle->ganancia_format = $monedaCuadro . number_format($detalle->ganancia, 2);
            $contenedor->suma_ganancia += $detalle->ganancia;

            $contenedor->filas[] = $detalle;
        }
        $contenedor->suma_costo_compra_convertido_format = $monedaCuadro . number_format($contenedor->suma_costo_compra_convertido, 2);
        $contenedor->suma_total_flete_format = $monedaCuadro . number_format($contenedor->suma_total_flete, 2);
        $contenedor->suma_costo_compra_mas_flete_format = $monedaCuadro . number_format($contenedor->suma_costo_compra_mas_flete, 2);
        $contenedor->suma_precio_venta_total_format = $monedaCuadro . number_format($contenedor->suma_precio_venta_total, 2);
        $contenedor->suma_ganancia_format = $monedaCuadro . number_format($contenedor->suma_ganancia, 2);
        return $contenedor;
    }
}
