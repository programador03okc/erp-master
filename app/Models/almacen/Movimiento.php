<?php

namespace App\Models\Almacen;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $table = 'almacen.mov_alm';
    protected $primaryKey = 'id_mov_alm';
    public $timestamps = false;
    protected $appends = ['ordenes_compra', 'comprobantes', 'ordenes_soft_link', 'requerimientos'];

    public function getFechaEmisionAttribute()
    {
        $fecha = new Carbon($this->attributes['fecha_emision']);
        return $fecha->format('d-m-Y');
    }

    public function getRequerimientosAttribute()
    {
        $requerimientos = MovimientoDetalle::join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', 'mov_alm_det.id_guia_com_det')
            ->join('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', 'guia_com_det.id_oc_det')
            ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', 'log_det_ord_compra.id_detalle_requerimiento')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', 'alm_det_req.id_requerimiento')
            ->where([
                ['mov_alm_det.id_mov_alm', '=', $this->attributes['id_mov_alm']],
                ['alm_req.estado', '!=', 7]
            ])
            ->select(['alm_req.codigo'])->distinct()->get();

        $resultado = [];
        foreach ($requerimientos as $req) {
            array_push($resultado, $req->codigo);
        }
        return implode(', ', $resultado);
    }

    public function getOrdenesCompraAttribute()
    {
        $ordenes = MovimientoDetalle::join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', 'mov_alm_det.id_guia_com_det')
            ->join('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', 'guia_com_det.id_oc_det')
            ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', 'log_det_ord_compra.id_orden_compra')
            ->where([
                ['mov_alm_det.id_mov_alm', '=', $this->attributes['id_mov_alm']],
                ['log_ord_compra.estado', '!=', 7]
            ])
            ->select(['log_ord_compra.codigo'])->distinct()->get();

        $resultado = [];
        foreach ($ordenes as $oc) {
            array_push($resultado, $oc->codigo);
        }
        return implode(', ', $resultado);
    }

    public function getOrdenesSoftLinkAttribute()
    {
        $ordenes = MovimientoDetalle::join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', 'mov_alm_det.id_guia_com_det')
            ->join('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', 'guia_com_det.id_oc_det')
            ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', 'log_det_ord_compra.id_orden_compra')
            ->where([
                ['mov_alm_det.id_mov_alm', '=', $this->attributes['id_mov_alm']],
                ['log_ord_compra.estado', '!=', 7]
            ])
            ->select(['log_ord_compra.codigo_softlink'])->distinct()->get();

        $resultado = [];
        foreach ($ordenes as $oc) {
            array_push($resultado, $oc->codigo_softlink);
        }
        return implode(', ', $resultado);
    }

    public function getComprobantesAttribute()
    {
        $comprobantes = MovimientoDetalle::join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', 'mov_alm_det.id_guia_com_det')
            ->join('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', 'guia_com_det.id_guia_com_det')
            ->join('almacen.doc_com', 'doc_com.id_doc_com', 'doc_com_det.id_doc')
            ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_com.moneda')
            ->join('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_com.id_condicion')
            ->leftJoin('administracion.sis_sede', 'doc_com.id_sede', '=', 'sis_sede.id_sede')

            ->where([
                ['mov_alm_det.id_mov_alm', '=', $this->attributes['id_mov_alm']],
                ['doc_com.estado', '!=', 7]
            ])
            ->select([
                'doc_com.serie', 
                'doc_com.numero',
                'sis_sede.descripcion as empresa_sede', 
                'sis_moneda.simbolo',
                'log_cdn_pago.descripcion as condicion_descripcion',
                'doc_com.sub_total',
                'doc_com.total_igv',
                'doc_com.total_a_pagar'
            ])->distinct()->get();

        $codigoComprobanteList = [];
        $montosList = [];
        $empresaSedeComprobante = '';
        $monedaComprobante = '';
        $condicionComprobante = '';
        foreach ($comprobantes as $doc) {
            $codigoComprobanteList[]= ($doc->serie . '-' . $doc->numero);
            $empresaSedeComprobante= $doc->empresa_sede;
            $monedaComprobante= $doc->simbolo;
            $condicionComprobante= $doc->condicion_descripcion;
            $montosList=[
                'sub_total'=>$doc->sub_total??0,
                'total_igv'=>$doc->total_igv??0,
                'total_a_pagar'=>$doc->total_a_pagar??0
            ];
        }
        return ['codigo'=>$codigoComprobanteList,'empresa_sede'=>$empresaSedeComprobante,'moneda'=>$monedaComprobante,'condicion'=>$condicionComprobante,'montos'=>$montosList];
    }

    // public function getMonedaComprobantesAttribute()
    // {
    //     $comprobantes = MovimientoDetalle::join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', 'mov_alm_det.id_guia_com_det')
    //         ->join('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', 'guia_com_det.id_guia_com_det')
    //         ->join('almacen.doc_com', 'doc_com.id_doc_com', 'doc_com_det.id_doc')
    //         ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_com.moneda')

    //         ->where([
    //             ['mov_alm_det.id_mov_alm', '=', $this->attributes['id_mov_alm']],
    //             ['doc_com.estado', '!=', 7]
    //         ])
    //         ->select(['sis_moneda.simbolo'])->distinct()->get();

    //     $resultado = [];
    //     foreach ($comprobantes as $doc) {
    //         array_push($resultado, $doc->simbolo);
    //     }
    //     return implode(', ', $resultado);
    // }
    // public function getCondicionComprobantesAttribute()
    // {
    //     $comprobantes = MovimientoDetalle::join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', 'mov_alm_det.id_guia_com_det')
    //         ->join('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', 'guia_com_det.id_guia_com_det')
    //         ->join('almacen.doc_com', 'doc_com.id_doc_com', 'doc_com_det.id_doc')
    //         ->join('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_com.id_condicion')

    //         ->where([
    //             ['mov_alm_det.id_mov_alm', '=', $this->attributes['id_mov_alm']],
    //             ['doc_com.estado', '!=', 7]
    //         ])
    //         ->select(['log_cdn_pago.descripcion'])->distinct()->get();

    //     $resultado = [];
    //     foreach ($comprobantes as $doc) {
    //         array_push($resultado, $doc->descripcion);
    //     }
    //     return implode(', ', $resultado);
    // }
    // public function getMontosComprobantesAttribute()
    // {
    //     $comprobantes = MovimientoDetalle::join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', 'mov_alm_det.id_guia_com_det')
    //         ->join('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', 'guia_com_det.id_guia_com_det')
    //         ->join('almacen.doc_com', 'doc_com.id_doc_com', 'doc_com_det.id_doc')

    //         ->where([
    //             ['mov_alm_det.id_mov_alm', '=', $this->attributes['id_mov_alm']],
    //             ['doc_com.estado', '!=', 7]
    //         ])
    //         ->select(['doc_com.sub_total','doc_com.total_igv','doc_com.total_a_pagar'])->distinct()->get();

    //     $resultado = [];
    //     foreach ($comprobantes as $doc) {
    //         $resultado=[
    //             'sub_total'=>$doc->sub_total??0,
    //             'total_igv'=>$doc->total_igv??0,
    //             'total_a_pagar'=>$doc->total_a_pagar??0
    //         ];
    //     }
    //     return $resultado;
    // }

    public function almacen(){
        return $this->hasOne('App\Models\Almacen\Almacen','id_almacen','id_almacen');
    }
    public function guia_compra(){
        return $this->hasOne('App\Models\Almacen\GuiaCompra','id_guia','id_guia_com');
    }
    public function guia_venta(){
        return $this->hasOne('App\Models\Almacen\GuiaVenta','id_guia_ven','id_guia_ven');
    }
    public function operacion(){
        return $this->hasOne('App\Models\Almacen\TipoOperacion','id_operacion','id_operacion');
    }
    public function documento_compra(){
        return $this->hasOne('App\Models\Almacen\DocumentoCompra','id_doc_com','id_doc_com');
    }
    public function documento_venta(){
        return $this->hasOne('App\Models\Almacen\DocumentoVenta','id_doc_ven','id_doc_ven');
    }
    public function usuario(){
        return $this->hasOne('App\Models\Configuracion\Usuario','id_usuario','usuario');
    }
    public function estado(){
        return $this->hasone('App\Models\Administracion\Estado','id_estado_doc','estado');
    }
    public function movimiento_detalle(){
        return $this->hasMany('App\Models\Almacen\MovimientoDetalle','id_mov_alm','id_mov_alm');
    }
}
