<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $table='almacen.mov_alm';
    protected $primaryKey='id_mov_alm';
    public $timestamps=false;
    protected $appends=['ordenes_compra','comprobantes'];

    public function getOrdenesCompraAttribute()
    {
        $ordenes=MovimientoDetalle::join('almacen.guia_com_det','guia_com_det.id_guia_com_det','mov_alm_det.id_guia_com_det')
        ->join('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','guia_com_det.id_oc_det')   
        ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','log_det_ord_compra.id_orden_compra')
        ->where('mov_alm_det.id_mov_alm',$this->attributes['id_mov_alm'])
        ->select(['log_ord_compra.codigo'])->distinct()->get();

        $resultado=[];
        foreach ($ordenes as $oc) {
            array_push($resultado,$oc->codigo);
        }
        return implode(', ',$resultado);
    }

    public function getComprobantesAttribute()
    {
        $comprobantes = MovimientoDetalle::join('almacen.guia_com_det','guia_com_det.id_guia_com_det','mov_alm_det.id_guia_com_det')
        ->join('almacen.doc_com_det','doc_com_det.id_guia_com_det','guia_com_det.id_guia_com_det')   
        ->join('almacen.doc_com','doc_com.id_doc_com','doc_com_det.id_doc')
        ->where('mov_alm_det.id_mov_alm',$this->attributes['id_mov_alm'])
        ->select(['doc_com.serie','doc_com.numero'])->distinct()->get();

        $resultado=[];
        foreach ($comprobantes as $doc) {
            array_push($resultado,$doc->serie.'-'.$doc->numero);
        }
        return implode(', ',$resultado);
    }
}
