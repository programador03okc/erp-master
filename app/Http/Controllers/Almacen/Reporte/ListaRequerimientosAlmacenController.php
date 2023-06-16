<?php

namespace App\Http\Controllers\Almacen\Reporte;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Almacen\Almacen;
use App\models\Configuracion\AccesosUsuarios;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListaRequerimientosAlmacenController extends Controller
{
    function viewRequerimientosAlmacen()
    {
        $almacenes = Almacen::where('estado', 1)->orderBy('codigo')->get();
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
        return view('almacen/reportes/requerimientosAlmacen', compact('almacenes','array_accesos'));
    }

    function listarRequerimientosAlmacen()
    {

        $soloAutorizadoGarantias=false;
        $allRol = Auth::user()->getAllRol();
        foreach ($allRol as  $rol) {
            if($rol->id_rol == 52) // autorizado garantias
            {
                $soloAutorizadoGarantias=true;
            }
        }

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
                       ->when((($soloAutorizadoGarantias) ==true), function ($query) {
                return $query->whereRaw('alm_req.division_id = 2 and alm_req.id_tipo_requerimiento = 6');  // autorizado solo ver comercial divison CAS, tipo de requerimiento de garantias
            })
            ->get();

        return datatables($lista)->toJson();
    }

    function listarDetalleRequerimiento($id_requerimiento)
    {
        $lista = DB::table('almacen.alm_det_req')
            ->select(
                'alm_det_req.id_detalle_requerimiento',
                'alm_det_req.id_producto',
                'alm_det_req.cantidad',
                'alm_det_req.entrega_cliente',
                'alm_det_req.tiene_transformacion',
                'alm_prod.codigo',
                'alm_prod.part_number',
                'alm_prod.descripcion'
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
            ->where([
                ['alm_det_req.id_requerimiento', '=', $id_requerimiento],
                ['alm_det_req.estado', '!=', 7]
            ])
            ->get();

        return response()->json($lista);
    }

    function cambioAlmacen(Request $request)
    {
        try {
            DB::beginTransaction();

            $alm = DB::table('almacen.alm_almacen')
                ->select('alm_almacen.*')
                ->where('id_almacen', $request->id_almacen)
                ->first();

            $req_anterior = DB::table('almacen.alm_req')
                ->select('alm_req.id_almacen')
                ->where('id_requerimiento', $request->id_requerimiento)
                ->first();

            DB::table('almacen.alm_req')
                ->where('id_requerimiento', $request->id_requerimiento)
                ->update([
                    'id_almacen' => $request->id_almacen,
                    'id_sede' => $alm->id_sede
                ]);

            // $od = DB::table('almacen.orden_despacho')
            //     ->where([
            //         ['id_requerimiento', '=', $request->id_requerimiento],
            //         ['estado', '=', 1]
            //     ])
            //     ->first();

            // if ($od !== null) {
            //     DB::table('almacen.orden_despacho')
            //         ->where('id_od', $od->id_od)
            //         ->update(['id_almacen' => $request->id_almacen]);
            // }

            DB::table('almacen.orden_despacho')
                ->where([
                    ['id_requerimiento', '=', $request->id_requerimiento],
                    ['estado', '=', 1]
                ])
                ->update(['id_almacen' => $request->id_almacen]);

            $detalle = json_decode($request->detalle);

            foreach ($detalle as $det) {
                DB::table('almacen.alm_det_req')
                    ->where('id_detalle_requerimiento', $det->id_detalle_requerimiento)
                    ->update([
                        'tiene_transformacion' => $det->tiene_transformacion,
                        'entrega_cliente' => $det->entrega_cliente,
                    ]);

                // DB::table('almacen.orden_despacho_det')
                //     ->where([
                //         ['id_detalle_requerimiento','=', $det->id_detalle_requerimiento],
                //         ['estado','!=',7]
                //     ])
                //     ->get();

                // if ($det->tiene_transformacion && $det->entrega_cliente){
                //     DB::table('almacen.orden_despacho_det')
                //     ->update([])
                // }
            }

            DB::table('almacen.alm_req_obs')
                ->insert([
                    'id_requerimiento' => $request->id_requerimiento,
                    'accion' => 'CAMBIO',
                    'descripcion' => 'Almacén de ' . $req_anterior->id_almacen . ' a ' . $request->id_almacen,
                    'id_usuario' => Auth::user()->id_usuario,
                    'fecha_registro' => new Carbon()
                ]);

            DB::commit();
            return response()->json($alm);
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar el ingreso. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }
}
