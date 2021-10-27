<?php

namespace App\Http\Controllers\Logistica\Distribucion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrdenesDespachoInternoController extends Controller
{
    function view_ordenes_despacho_interno()
    {
        return view('almacen/distribucion/ordenesDespachoInterno');
    }

    public function listarRequerimientosPendientesDespachoInterno(Request $request)
    {
        $data = DB::table('almacen.orden_despacho')
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
                'orden_despacho.fecha_despacho',
                'est_od.estado_doc as estado_od',
                'orden_despacho.codigo as codigo_od',
                'transformacion.id_transformacion',
                'transformacion.codigo as codigo_transformacion',
                'est_trans.estado_doc as estado_transformacion',
                'est_trans.bootstrap_color as estado_bootstrap_transformacion',
                // 'orden_despacho.estado as estado_od',
                'adm_contri.nro_documento as cliente_ruc',
                'adm_contri.razon_social as cliente_razon_social',
                'oc_propias_view.nro_orden',
                'oc_propias_view.codigo_oportunidad',
                'oc_propias_view.id as id_oc_propia',
                'oc_propias_view.tipo',
                DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req where
                        alm_det_req.id_requerimiento = alm_req.id_requerimiento
                        and alm_det_req.estado != 7
                        and alm_det_req.id_producto is null) AS productos_no_mapeados")
            )
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->join('almacen.transformacion', 'transformacion.id_od', '=', 'orden_despacho.id_od')
            ->join('administracion.adm_estado_doc as est_trans', 'est_trans.id_estado_doc', '=', 'transformacion.estado')
            ->join('administracion.adm_estado_doc as est_od', 'est_od.id_estado_doc', '=', 'orden_despacho.estado')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.estado')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->leftJoin('configuracion.ubi_dis', 'ubi_dis.id_dis', '=', 'alm_req.id_ubigeo_entrega')
            ->leftJoin('configuracion.ubi_prov', 'ubi_prov.id_prov', '=', 'ubi_dis.id_prov')
            ->leftJoin('configuracion.ubi_dpto', 'ubi_dpto.id_dpto', '=', 'ubi_prov.id_dpto')
            ->leftJoin('administracion.sis_sede as sede_req', 'sede_req.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_req.id_almacen')
            ->leftJoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->where([
                ['alm_req.estado', '!=', 7],
                ['orden_despacho.aplica_cambios', '=', true],
                ['orden_despacho.estado', '!=', 7],
            ]);
        if ($request->select_mostrar == 1) {
            $data->whereNotNull('fecha_despacho');
        } else if ($request->select_mostrar == 2) {
            $data->whereDate('fecha_despacho', (new Carbon())->format('Y-m-d'));
        }
        return datatables($data)->toJson();
    }

    public function priorizar(Request $request)
    {
        try {
            DB::beginTransaction();
            $despachos = json_decode($request->despachos_internos);

            foreach ($despachos as $det) {
                DB::table('almacen.orden_despacho')
                    ->where('id_od', $det->id_od)
                    ->update(['fecha_despacho' => $request->fecha_despacho]);
            }
            DB::commit();
            return response()->json('ok');
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(':(');
        }
    }
}
