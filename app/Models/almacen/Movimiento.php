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
            ->where([
                ['mov_alm_det.id_mov_alm', '=', $this->attributes['id_mov_alm']],
                ['doc_com.estado', '!=', 7]
            ])
            ->select(['doc_com.serie', 'doc_com.numero'])->distinct()->get();

        $resultado = [];
        foreach ($comprobantes as $doc) {
            array_push($resultado, $doc->serie . '-' . $doc->numero);
        }
        return implode(', ', $resultado);
    }
}
