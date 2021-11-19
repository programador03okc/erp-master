<?php

namespace App\Models\mgcp\CuadroCosto;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CcAm extends Model
{
    // use HasFactory;
    protected $table = 'mgcp_cuadro_costos.cc_am';
    protected $primaryKey = 'id_cc';
    public $timestamps = false;

    public function getFechaFormalizacionAttribute()
    {
        if ($this->attributes['fecha_formalizacion'] == null) {
            return '-';
        } else {
            return date_format(date_create($this->attributes['fecha_formalizacion']), 'd-m-Y');
        }
    }

    public function cuadroCosto()
    {
        return $this->belongsTo(CuadroCosto::class, 'id_cc', 'id');
    }

    public function getFechaEntregaAttribute()
    {
        if ($this->attributes['fecha_entrega'] == null) {
            return '-';
        } else {
            return date_format(date_create($this->attributes['fecha_entrega']), 'd-m-Y');
        }
    }

    public function setFechaFormalizacionAttribute($valor)
    {
        if ($valor == null || $valor == '') {
            $this->attributes['fecha_formalizacion'] = null;
        } else {
            $this->attributes['fecha_formalizacion'] = Carbon::createFromFormat('d-m-Y', $valor)->toDateString();
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

    public function filas()
    {
        return $this->hasMany(CcAmFila::class, 'id_cc_am', 'id_cc');
    }

    public static function obtenerDetallesFilas(&$cuadroCosto)
    {
        $cuadroAm=CcAm::find($cuadroCosto->id);
        $filas = CcAmFila::with(['amProveedor', 'amProveedor.proveedor'])->where('id_cc_am', $cuadroCosto->id)->orderBy('id', 'asc')->get();
        $contenedor = new \stdClass();
        $contenedor->filas = [];
        $monedaCuadro = $cuadroCosto->moneda == 's' ? 'S/' : '$';
        $contenedor->suma_costo_compra_convertido = 0;
        $contenedor->suma_total_flete = 0;
        $contenedor->suma_costo_compra_mas_flete = 0;
        $contenedor->suma_monto_adjudicado = 0;
        $contenedor->suma_ganancia = 0;
        //return response()->json(array('tipo' => 'success', 'data'=> $cuadroVentaFilas), 200);
        foreach ($filas as $fila) {
            $detalle = new \stdClass();
            //$detalle->margen_ganancia = $fila->margen_ganancia;
            $detalle->part_no = $fila->part_no ?? '';
            $detalle->descripcion = $fila->descripcion ?? '';
            $detalle->cantidad = $fila->cantidad ?? 0;
            $detalle->tiene_transformacion = $fila->tieneTransformacion(); //(empty($fila->part_no_producto_transformado) && empty($fila->descripcion_producto_transformado) && empty($fila->comentario_producto_transformado)) ? false : true;
            $detalle->proveedor = new \stdClass();
            $detalle->proveedor->moneda = $fila->amProveedor != null ? $fila->amProveedor->moneda : null;
            $monedaProveedor = $detalle->proveedor->moneda == null ? '' : ($detalle->proveedor->moneda == 's' ? 'S/' : '$');
            $detalle->proveedor->precio = $fila->amProveedor != null ? $fila->amProveedor->precio : null;
            $detalle->proveedor->precio_format = $detalle->proveedor->precio == null ? '' : $monedaProveedor . number_format($detalle->proveedor->precio, 2);
            $detalle->proveedor->nombre = $fila->amProveedor != null ? $fila->amProveedor->proveedor->razon_social : null;
            $detalle->proveedor->flete = $fila->amProveedor != null ? $fila->amProveedor->flete : null;
            $detalle->proveedor->flete_format = $detalle->proveedor->flete == null ? '' : 'S/' . number_format($detalle->proveedor->flete, 2);
            $detalle->proveedor->plazo = $fila->amProveedor != null ? $fila->amProveedor->plazo : null;

            $fondoProveedor = $fila->amProveedor != null ? $fila->amProveedor->fondoProveedor : null;
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
            //Calculo PVU
            if ($cuadroAm->moneda_pvu==$cuadroCosto->moneda)
            {
                $pvu=$fila->pvu_oc;
            }
            else
            {
                $pvu=$cuadroAm->moneda_pvu=='d' ? $fila->pvu_oc*$cuadroCosto->tipo_cambio : $fila->pvu_oc/$cuadroCosto->tipo_cambio;
            }
            $detalle->monto_adjudicado = ($fila->cantidad * $fila->flete_oc* $tasaFlete)+($fila->cantidad*$pvu); //Tasa flete también aplica porque el monto de la O/C siempre es en soles
            $detalle->monto_adjudicado_format = $monedaCuadro . number_format($detalle->monto_adjudicado, 2);
            $detalle->ganancia = $detalle->monto_adjudicado - $detalle->costo_compra_mas_flete;
            $contenedor->suma_ganancia += $detalle->ganancia;
            $detalle->ganancia_format = $monedaCuadro . number_format($detalle->ganancia, 2);
            $contenedor->suma_monto_adjudicado += $detalle->monto_adjudicado;
            /*$detalle->precio_venta_unitario = $detalle->proveedor->precio == null ? 0 : $detalle->proveedor->precio*$tasa * (1 / ((100 - $detalle->margen_ganancia) / 100)) + ($detalle->proveedor->flete*$tasaFlete);
            $detalle->precio_venta_unitario_format = $monedaCuadro . number_format($detalle->precio_venta_unitario, 2);
            $detalle->precio_venta_total = $detalle->precio_venta_unitario * $detalle->cantidad;
            $detalle->precio_venta_total_format = $monedaCuadro . number_format($detalle->precio_venta_total, 2);
            $contenedor->suma_precio_venta_total += $detalle->precio_venta_total;
            $detalle->ganancia = $detalle->precio_venta_total - $detalle->costo_compra_mas_flete;
            $detalle->ganancia_format = $monedaCuadro . number_format($detalle->ganancia, 2);
            $contenedor->suma_ganancia += $detalle->ganancia;*/

            $contenedor->filas[] = $detalle;
        }
        $contenedor->suma_costo_compra_convertido_format = $monedaCuadro . number_format($contenedor->suma_costo_compra_convertido, 2);
        $contenedor->suma_total_flete_format = $monedaCuadro . number_format($contenedor->suma_total_flete, 2);
        $contenedor->suma_costo_compra_mas_flete_format = $monedaCuadro . number_format($contenedor->suma_costo_compra_mas_flete, 2);
        $contenedor->suma_monto_adjudicado_format = $monedaCuadro . number_format($contenedor->suma_monto_adjudicado, 2);
        $contenedor->suma_ganancia_format = $monedaCuadro . number_format($contenedor->suma_ganancia, 2);
        return $contenedor;
    }
}
