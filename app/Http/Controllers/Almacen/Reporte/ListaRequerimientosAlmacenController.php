<?php

namespace App\Http\Controllers\Almacen\Reporte;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Almacen\Almacen;
use Illuminate\Support\Facades\DB;

class ListaRequerimientosAlmacenController extends Controller
{
    function viewRequerimientosAlmacen()
    {
        $almacenes = Almacen::where('estado', 1)->get();
        return view('almacen/reportes/requerimientosAlmacen', compact('almacenes'));
    }

    function listarRequerimientosAlmacen()
    {
        $lista = DB::table('almacen.alm_req')
            ->select(
                'alm_req.*',
                'sis_grupo.descripcion as grupo_descripcion',
                'alm_almacen.descripcion as almacen_descripcion',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'sis_usua.nombre_corto',
                'estado_despacho.estado_doc as estado_despacho_descripcion',
                'despachoInterno.id_od as id_despacho_interno',
                'despachoInterno.codigo as codigo_despacho_interno',
                'orden_despacho.id_od as id_despacho_externo',
                'orden_despacho.codigo as codigo_despacho_externo',
                'transformacion.id_transformacion',
                'transformacion.codigo as codigo_transformacion',
                'estado_di.estado_doc as estado_di',
                DB::raw("(SELECT count(*) from almacen.trans
                    where trans.id_requerimiento = alm_req.id_requerimiento
                    and trans.estado != 7) AS count_transferencias")
            )
            ->join('configuracion.sis_grupo', 'sis_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_req.id_almacen')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.estado')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->join('administracion.adm_estado_doc as estado_despacho', 'estado_despacho.id_estado_doc', '=', 'alm_req.estado_despacho')
            ->leftJoin('almacen.orden_despacho', function ($join) {
                $join->on('orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento');
                $join->where('orden_despacho.aplica_cambios', '=', false);
                $join->where('orden_despacho.estado', '!=', 7);
            })
            ->leftJoin('almacen.orden_despacho as despachoInterno', function ($join) {
                $join->on('despachoInterno.id_requerimiento', '=', 'alm_req.id_requerimiento');
                $join->where('despachoInterno.aplica_cambios', '=', true);
                $join->where('despachoInterno.estado', '!=', 7);
            })
            ->leftJoin('almacen.transformacion', 'transformacion.id_od', '=', 'despachoInterno.id_od')
            ->leftJoin('administracion.adm_estado_doc as estado_di', 'estado_di.id_estado_doc', '=', 'despachoInterno.estado')
            // ->where([['alm_req.estado', '!=', 7]])
            ->get();

        return datatables($lista)->toJson();
    }

    function cambioAlmacen($id_requerimiento, $id_almacen)
    {
        $alm = DB::table('almacen.alm_almacen')
            ->select('alm_almacen.*')
            ->where('id_almacen', $id_almacen)
            ->first();

        DB::table('almacen.alm_req')
            ->where('id_requerimiento', $id_requerimiento)
            ->update([
                'id_almacen' => $id_almacen,
                'id_sede' => $alm->id_sede
            ]);

        return response()->json($alm);
    }
}
