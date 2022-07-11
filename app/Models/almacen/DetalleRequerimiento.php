<?php

namespace App\Models\Almacen;

use App\Models\Comercial\CuadroCosto\CcAmFila;
use App\Models\Finanzas\CentroCostosView;
use App\Models\Logistica\OrdenCompraDetalle;
use App\Models\Presupuestos\CentroCosto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetalleRequerimiento extends Model
{
    protected $table = 'almacen.alm_det_req';
    protected $primaryKey = 'id_detalle_requerimiento';
    public $timestamps = false;
    protected $appends = ['codigo_requerimiento', 'ordenes_compra', 'facturas', 'proveedor_seleccionado','movimiento_ingresos_almacen','movimiento_salidas_almacen'];

    public function getPartNumberAttribute()
    {
        return $this->attributes['part_number'] ?? '';
    }
    public function getCodigoRequerimientoAttribute()
    {
        $codigo = Requerimiento::find($this->attributes['id_requerimiento'])->codigo;
        return $codigo ?? '';
    }


    public function getOrdenesCompraAttribute()
    {

        $ordenes = OrdenCompraDetalle::join('almacen.alm_det_req', 'log_det_ord_compra.id_detalle_requerimiento', 'alm_det_req.id_detalle_requerimiento')
            ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', 'log_det_ord_compra.id_orden_compra')
            ->where([['alm_det_req.id_detalle_requerimiento', $this->attributes['id_detalle_requerimiento']], ['log_ord_compra.estado', '!=', 7], ['log_det_ord_compra.estado', '!=', 7]])
            ->select(['log_ord_compra.id_orden_compra', 'log_ord_compra.codigo', 'log_det_ord_compra.cantidad', 'log_det_ord_compra.estado'])->distinct()->get();

        // $keyed = $ordenes->mapWithKeys(function ($item) {
        //     return [$item['id_orden_compra'] => $item['codigo']];
        // });
        // $keyed->all();

        // return $keyed;
        return $ordenes;
    }

    public function getMovimientoIngresosAlmacenAttribute()
    {
        $dataIngresos = MovimientoDetalle::leftJoin('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', 'mov_alm_det.id_guia_com_det')
        ->leftJoin('almacen.mov_alm', 'mov_alm.id_mov_alm', 'mov_alm_det.id_mov_alm')
        ->join('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', 'guia_com_det.id_oc_det')
        ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', 'log_det_ord_compra.id_orden_compra')
        ->join('almacen.alm_det_req', 'log_det_ord_compra.id_detalle_requerimiento', 'alm_det_req.id_detalle_requerimiento')
        ->where([
            ['alm_det_req.id_detalle_requerimiento', $this->attributes['id_detalle_requerimiento']],
            ['mov_alm.estado', '!=', 7],
            ['log_ord_compra.estado', '!=', 7]
        ])
        ->select(['mov_alm.id_mov_alm', 'mov_alm.codigo','mov_alm.fecha_emision'])->distinct()->get();
        
        return $dataIngresos;
    }
    public function getMovimientoSalidasAlmacenAttribute()
    {
        $dataSalidas = MovimientoDetalle::leftJoin('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven_det', 'mov_alm_det.id_guia_ven_det')
        ->leftJoin('almacen.orden_despacho_det', 'orden_despacho_det.id_od_detalle', 'guia_ven_det.id_od_det')
        ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', 'orden_despacho_det.id_detalle_requerimiento')
        ->leftJoin('almacen.mov_alm', 'mov_alm.id_mov_alm', 'mov_alm_det.id_mov_alm')
        ->where([
            ['alm_det_req.id_detalle_requerimiento', $this->attributes['id_detalle_requerimiento']],
            ['mov_alm.estado', '!=', 7],
            ['orden_despacho_det.estado', '!=', 7]
        ])
        ->select(['mov_alm.id_mov_alm', 'mov_alm.codigo','mov_alm.fecha_emision'])->distinct()->get();
        
        return $dataSalidas;
    }
    // public function getGuiasIngresoAttribute(){

    //     $guiasIngreso = OrdenCompraDetalle::join('almacen.guia_com_det', 'guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden')
    //     ->join('almacen.alm_det_req','log_det_ord_compra.id_detalle_requerimiento','alm_det_req.id_detalle_requerimiento')
    //     ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
    //     ->select('guia_com.id_guia',DB::raw("concat(guia_com.serie, '-', guia_com.numero) AS codigo_guia"),'log_det_ord_compra.id_orden_compra')
    //     ->where('alm_det_req.id_detalle_requerimiento',$this->attributes['id_detalle_requerimiento'])

    //     ->get();

    //     return $guiasIngreso;
    // }
    public function getFacturasAttribute()
    {

        $facturas = OrdenCompraDetalle::join('almacen.guia_com_det', 'guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden')
            ->join('almacen.alm_det_req', 'log_det_ord_compra.id_detalle_requerimiento', 'alm_det_req.id_detalle_requerimiento')
            ->leftjoin('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', '=', 'guia_com_det.id_guia_com_det')
            ->leftjoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'doc_com_det.id_doc')
            ->select('doc_com.id_doc_com', DB::raw("concat(doc_com.serie, '-', doc_com.numero) AS codigo_factura"))
            ->where('alm_det_req.id_detalle_requerimiento', $this->attributes['id_detalle_requerimiento'])

            ->get();

        return $facturas;
    }
    public function getProveedorSeleccionadoAttribute()
    {

        $ccAmFila = CcAmFila::leftJoin('almacen.alm_det_req', 'cc_am_filas.id', '=', 'alm_det_req.id_cc_am_filas')
            ->leftJoin('mgcp_cuadro_costos.cc_am_proveedores', 'cc_am_proveedores.id', '=', 'cc_am_filas.proveedor_seleccionado')
            ->leftJoin('mgcp_cuadro_costos.proveedores as proveedores_am', 'proveedores_am.id', '=', 'cc_am_proveedores.id_proveedor')
            ->leftJoin('mgcp_cuadro_costos.cc_venta_filas', 'cc_venta_filas.id', '=', 'alm_det_req.id_cc_venta_filas')
            ->leftJoin('mgcp_cuadro_costos.cc_venta_proveedor', 'cc_venta_proveedor.id', '=', 'cc_venta_filas.proveedor_seleccionado')
            ->leftJoin('mgcp_cuadro_costos.proveedores as proveedores_venta', 'proveedores_venta.id', '=', 'cc_venta_filas.proveedor_seleccionado')
            ->select('proveedores_am.razon_social as razon_social_proveedor_seleccionado_am', 'proveedores_venta.razon_social as razon_social_proveedor_seleccionado_venta')
            ->where('alm_det_req.id_detalle_requerimiento', $this->attributes['id_detalle_requerimiento'])
            ->first();

        if ($ccAmFila) {
            $proveedorSeleccionado = $ccAmFila->razon_social_proveedor_seleccionado_am != null ? $ccAmFila->razon_social_proveedor_seleccionado_am : ($ccAmFila->razon_social_proveedor_seleccionado_venta != null ? $ccAmFila->razon_social_proveedor_seleccionado_venta : '');
        } else {
            $proveedorSeleccionado = '';
        }

        return $proveedorSeleccionado;
    }
    static public function actualizarEstadoDetalleRequerimientoAtendido($idDetalleRequerimiento)
    {

        $cantidadAtendidaConAlmacen = 0;
        $ReservasProductoActualizadas = Reserva::with('almacen', 'usuario.trabajador.postulante.persona', 'estado')->where([['id_detalle_requerimiento', $idDetalleRequerimiento], ['estado', 1]])->get();
        if ($ReservasProductoActualizadas) {
            foreach ($ReservasProductoActualizadas as $value) {
                $cantidadAtendidaConAlmacen += $value->stock_comprometido;
            }
        }

        $cantidadAtendidaConOrden = 0;
        $DetalleOrdenesActivas = OrdenCompraDetalle::where([['id_detalle_requerimiento', $idDetalleRequerimiento], ['estado', 1]])->get();
        if ($DetalleOrdenesActivas) {
            foreach ($DetalleOrdenesActivas as $value) {
                $cantidadAtendidaConOrden += $value->cantidad;
            }
        }
        // actualisar estdo de detalle requerimiento

        $detalleRequerimiento = DetalleRequerimiento::where('id_detalle_requerimiento', $idDetalleRequerimiento)->first();

        if (($cantidadAtendidaConOrden == 0) && ($cantidadAtendidaConAlmacen == 0)) {
            $detalleRequerimiento->estado = 1; //elaborado
        } elseif (($cantidadAtendidaConOrden == 0) && ($cantidadAtendidaConAlmacen == $detalleRequerimiento->cantidad)) {
            $detalleRequerimiento->estado = 28; //almacen total
        } elseif ($cantidadAtendidaConOrden + $cantidadAtendidaConAlmacen >= $detalleRequerimiento->cantidad) {
            $detalleRequerimiento->estado = 5; //antendido total
        } else {
            $detalleRequerimiento->estado = 15; //atendido parcial
        }
        $detalleRequerimiento->save();
    }
    public function ccfila()
    {
        return $this->belongsTo(CcAmFila::class, 'id_cc_am_filas');
    }
    public function producto()
    {
        return $this->hasone('App\Models\Almacen\Producto', 'id_producto', 'id_producto');
    }
    public function unidadMedida()
    {
        return $this->hasone('App\Models\Almacen\UnidadMedida', 'id_unidad_medida', 'id_unidad_medida');
    }
    public function reserva()
    {
        return $this->hasMany('App\Models\Almacen\Reserva', 'id_detalle_requerimiento', 'id_detalle_requerimiento');
    }
    public function adjuntoDetalleRequerimiento()
    {
        return $this->hasMany('App\Models\Almacen\AdjuntoDetalleRequerimiento', 'id_detalle_requerimiento', 'id_detalle_requerimiento');
    }
    public function detalle_orden()
    {
        return $this->hasMany('App\Models\Logistica\OrdenCompraDetalle', 'id_detalle_requerimiento', 'id_detalle_requerimiento');
    }
    public function estado()
    {
        return $this->hasone('App\Models\Administracion\Estado', 'id_estado_doc', 'estado');
    }
    public function requerimiento()
    {
        return $this->hasone('App\Models\Almacen\Requerimiento', 'id_requerimiento', 'id_requerimiento');
    }

    // public function getCentroCostosAttribute(){
    //     // $centroCostos = CentroCostosView::join('almacen.alm_det_req','cc_niveles_view.id_centro_costo','alm_det_req.centro_costo_id')
    //     $centroCostos = CentroCosto::join('almacen.alm_det_req','centro_costo.id_centro_costo','alm_det_req.centro_costo_id')
    //     ->select('centro_costo.*')
    //     ->where(
    //         'alm_det_req.id_detalle_requerimiento',$this->attributes['id_detalle_requerimiento']
    //     )
    //     ->get();
    //     return $centroCostos;
    // }


    // public function centroCostos(){
    //     return $this->belongsTo('App\Models\Finanzas\CentroCostosView','centro_costo_id','id_centro_costo')->withDefault(
    //         [
    //             'id_centro_costo' => null,
    //             'codigo' => null,
    //             'descripcion' => null,
    //             'grupo' => null,
    //             'unidad' => null,
    //             'division' => null,
    //             'segmento' => null
    //         ]
    //     );
    // }
}
