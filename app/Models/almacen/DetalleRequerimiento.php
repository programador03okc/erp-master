<?php

namespace App\Models\Almacen;

use App\Models\Logistica\OrdenCompraDetalle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetalleRequerimiento extends Model
{
    protected $table = 'almacen.alm_det_req';
    protected $primaryKey = 'id_detalle_requerimiento';
    public $timestamps = false;
    protected $appends= ['ordenes_compra','guias_ingreso','facturas'];


    public function getPartNumberAttribute(){
        return $this->attributes['part_number'] ?? '';
    }

    public function getOrdenesCompraAttribute(){

        $ordenes=OrdenCompraDetalle::join('almacen.alm_det_req','log_det_ord_compra.id_detalle_requerimiento','alm_det_req.id_detalle_requerimiento')
        ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','log_det_ord_compra.id_orden_compra')
        ->where('alm_det_req.id_detalle_requerimiento',$this->attributes['id_detalle_requerimiento'])
        ->select(['log_ord_compra.id_orden_compra','log_ord_compra.codigo'])->distinct()->get(); 

        // $keyed = $ordenes->mapWithKeys(function ($item) {
        //     return [$item['id_orden_compra'] => $item['codigo']];
        // });
        // $keyed->all();

        // return $keyed;
        return $ordenes;
    }
    public function getGuiasIngresoAttribute(){

        $guiasIngreso = OrdenCompraDetalle::join('almacen.guia_com_det', 'guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden')
        ->join('almacen.alm_det_req','log_det_ord_compra.id_detalle_requerimiento','alm_det_req.id_detalle_requerimiento')
        ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
        ->leftjoin('almacen.mov_alm', 'mov_alm.id_guia_com', '=', 'guia_com.id_guia')
        ->select('mov_alm.id_mov_alm','mov_alm.codigo')
        ->where('alm_det_req.id_detalle_requerimiento',$this->attributes['id_detalle_requerimiento'])

        ->get();

        return $guiasIngreso;
    }
    public function getFacturasAttribute(){

        $facturas = OrdenCompraDetalle::join('almacen.guia_com_det', 'guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden')
        ->join('almacen.alm_det_req','log_det_ord_compra.id_detalle_requerimiento','alm_det_req.id_detalle_requerimiento')
        ->leftjoin('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', '=', 'guia_com_det.id_guia_com_det')
        ->leftjoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'doc_com_det.id_doc')
        ->select('doc_com.id_doc_com',DB::raw("concat(doc_com.serie, '-', doc_com.numero) AS codigo_factura"))
        ->where('alm_det_req.id_detalle_requerimiento',$this->attributes['id_detalle_requerimiento'])

        ->get();

        return $facturas;
    }

}

