<?php

namespace App\Http\Controllers\Logistica\Distribucion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OrdenesDespachoExternoController extends Controller
{
    public function __construct()
    {
        // session_start();
    }

    function view_ordenes_despacho_externo()
    {
        return view('almacen/distribucion/ordenesDespachoExterno');
    }


    public function listarRequerimientosPendientesDespachoExterno()
    {
        $data = DB::table('almacen.alm_reserva')
            ->select(
                'alm_req.*',
                'sis_usua.nombre_corto as responsable',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                DB::raw("(ubi_dis.descripcion) || ' - ' || (ubi_prov.descripcion) || ' - ' || (ubi_dpto.descripcion) AS ubigeo_descripcion"),
                'alm_almacen.descripcion as almacen_descripcion',
                'alm_req.id_sede as sede_requerimiento',
                'sede_req.descripcion as sede_descripcion_req',
                'orden_despacho.id_od',
                'orden_despacho.codigo as codigo_od',
                'orden_despacho.estado as estado_od',
                'adm_contri.nro_documento as cliente_ruc',
                'adm_contri.razon_social as cliente_razon_social',
                DB::raw("(SELECT COUNT(*) FROM almacen.trans where
                        trans.id_requerimiento = alm_req.id_requerimiento
                        and trans.estado != 7) AS count_transferencia"),
                'oc_propias_view.nro_orden',
                'oc_propias_view.codigo_oportunidad',
                'oc_propias_view.id as id_oc_propia',
                'oc_propias_view.tipo'
            )
            ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'alm_reserva.id_detalle_requerimiento')
            ->join('almacen.alm_req', function ($join) {
                $join->on('alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento');
                $join->on('alm_req.id_almacen', '=', 'alm_reserva.id_almacen_reserva');
                $join->whereNotNull('alm_reserva.id_almacen_reserva');
            })
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.estado')
            ->leftJoin('configuracion.ubi_dis', 'ubi_dis.id_dis', '=', 'alm_req.id_ubigeo_entrega')
            ->leftJoin('configuracion.ubi_prov', 'ubi_prov.id_prov', '=', 'ubi_dis.id_prov')
            ->leftJoin('configuracion.ubi_dpto', 'ubi_dpto.id_dpto', '=', 'ubi_prov.id_dpto')
            ->leftJoin('administracion.sis_sede as sede_req', 'sede_req.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_req.id_almacen')
            ->leftJoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->leftJoin('almacen.orden_despacho', function ($join) {
                $join->on('orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento');
                $join->where('orden_despacho.aplica_cambios', '=', false);
                $join->where('orden_despacho.estado', '!=', 7);
            })
            ->where([
                ['alm_det_req.estado', '!=', 7],
                ['alm_reserva.estado', '!=', 7],
                ['alm_reserva.estado', '!=', 5],
                ['alm_reserva.stock_comprometido', '>', 0]
            ])
            // ->whereIn('alm_req.estado',[])
            ->distinct();


        return datatables($data)->toJson();
    }
}
