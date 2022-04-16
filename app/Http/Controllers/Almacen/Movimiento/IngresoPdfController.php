<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class IngresoPdfController extends Controller
{
    public function imprimir_ingreso($id_ingreso)
    {
        $ingreso = DB::table('almacen.mov_alm')
            ->select(
                'mov_alm.*',
                'alm_almacen.descripcion as des_almacen',
                DB::raw("(guia_com.serie) || '-' || (guia_com.numero) as guia"),
                DB::raw("(cont_tp_doc.abreviatura) || '-' || (doc_com.serie) || '-' || (doc_com.numero) as doc"),
                'doc_com.fecha_emision as doc_fecha_emision',
                'tp_doc_almacen.descripcion as tp_doc_descripcion',
                'guia_com.fecha_emision as fecha_guia',
                'sis_usua.usuario as nom_usuario',
                'adm_contri.razon_social',
                'adm_contri.direccion_fiscal',
                'adm_contri.nro_documento',
                'tp_ope.cod_sunat',
                'tp_ope.descripcion as ope_descripcion',
                'adm_empresa.logo_empresa',
                'empresa.razon_social as empresa_razon_social',
                'empresa.nro_documento as ruc_empresa',
                'doc_com.tipo_cambio',
                'sis_moneda.descripcion as des_moneda',
                'sis_usua.nombre_corto',
                'transformacion.codigo as cod_transformacion',
                'transformacion.fecha_transformacion', //'transformacion.serie','transformacion.numero',
                'trans.codigo as trans_codigo',
                'alm_origen.descripcion as trans_almacen_origen'
            )
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('contabilidad.adm_contri as empresa', 'empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftjoin('almacen.transformacion', 'transformacion.id_transformacion', '=', 'mov_alm.id_transformacion')
            ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'mov_alm.id_guia_com')
            ->leftjoin('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_com.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'mov_alm.id_operacion')
            ->leftjoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'mov_alm.id_doc_com')
            ->leftjoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_com.id_tp_doc')
            ->leftjoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_com.moneda')
            ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'guia_com.id_proveedor')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftjoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'mov_alm.usuario')
            ->leftjoin('almacen.trans', 'trans.id_guia_com', '=', 'mov_alm.id_guia_com')
            ->leftjoin('almacen.alm_almacen as alm_origen', 'alm_origen.id_almacen', '=', 'trans.id_almacen_origen')
            ->where('mov_alm.id_mov_alm', $id_ingreso)
            ->first();

        $detalle = DB::table('almacen.mov_alm_det')
            ->select(
                'mov_alm_det.*',
                'alm_prod.codigo',
                'alm_prod.part_number',
                'alm_prod.descripcion',
                'alm_und_medida.abreviatura',
                // 'log_det_ord_compra.subtotal',
                'log_det_ord_compra.precio as unitario',
                'guia_com_det.unitario_adicional',
                'guia_com_det.unitario as unitario_guia',
                'log_ord_compra.codigo as codigo_oc',
                'log_ord_compra.codigo_softlink',
                'doc_com.fecha_emision',
                'doc_com_det.precio_unitario',
                'doc_moneda.simbolo as moneda_doc',
                'oc_moneda.simbolo as moneda_oc',
                DB::raw("(cont_tp_doc.abreviatura) || '-' ||(doc_com.serie) || '-' || (doc_com.numero) as doc")
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'mov_alm_det.id_guia_com_det')
            ->leftjoin('logistica.log_det_ord_compra', function ($join) {
                $join->on('log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det');
                $join->where('log_det_ord_compra.estado', '!=', 7);
            })
            ->leftjoin('logistica.log_ord_compra', function ($join) {
                $join->on('log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra');
                $join->where('log_ord_compra.estado', '!=', 7);
            })
            ->leftjoin('almacen.doc_com_det', function ($join) {
                $join->on('doc_com_det.id_guia_com_det', '=', 'guia_com_det.id_guia_com_det');
                $join->where('doc_com_det.estado', '!=', 7);
            })
            ->leftjoin('almacen.doc_com', function ($join) {
                $join->on('doc_com.id_doc_com', '=', 'doc_com_det.id_doc');
                $join->where('doc_com.estado', '!=', 7);
            })
            ->leftjoin('configuracion.sis_moneda as doc_moneda', 'doc_moneda.id_moneda', '=', 'doc_com.moneda')
            ->leftjoin('configuracion.sis_moneda as oc_moneda', 'oc_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->leftjoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_com.id_tp_doc')
            ->where([['mov_alm_det.id_mov_alm', '=', $id_ingreso], ['mov_alm_det.estado', '!=', 7]])
            ->get();

        $ocs_array = [];
        $softlink_array = [];
        $docs_array = [];
        $docs_fecha_array = [];

        if ($ingreso !== null) {
            foreach ($detalle as $det) {
                if (!in_array($det->codigo_oc, $ocs_array)) {
                    array_push($ocs_array, $det->codigo_oc);
                }
                if (!in_array($det->codigo_softlink, $softlink_array)) {
                    array_push($softlink_array, $det->codigo_softlink);
                }
                if (!in_array($det->doc, $docs_array)) {
                    array_push($docs_array, $det->doc);
                }
                if (!in_array($det->fecha_emision, $docs_fecha_array)) {
                    array_push($docs_fecha_array, $det->fecha_emision);
                }
            }
        }

        $logo_empresa = ".$ingreso->logo_empresa";
        $fecha_registro =  (new Carbon($ingreso->fecha_registro))->format('d-m-Y');
        $hora_registro = (new Carbon($ingreso->fecha_registro))->format('H:i:s');
        $ocs = implode(",", $ocs_array);
        $softlink = implode(",", $softlink_array);
        $docs = implode(",", $docs_array);
        $docs_fecha = implode(",", $docs_fecha_array);

        $vista = View::make(
            'almacen/guias/ingreso_pdf',
            compact(
                'ingreso',
                'logo_empresa',
                'detalle',
                'ocs',
                'softlink',
                'docs',
                'docs_fecha',
                'fecha_registro',
                'hora_registro'
            )
        )->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($vista);

        return $pdf->stream();
        return $pdf->download($ingreso->codigo . '.pdf');
    }
}
