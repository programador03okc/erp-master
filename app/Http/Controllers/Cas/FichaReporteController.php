<?php

namespace App\Http\Controllers\Cas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cas\Incidencia;
use App\Models\Cas\IncidenciaReporte;
use App\Models\Cas\MedioReporte;
use App\Models\Configuracion\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FichaReporteController extends Controller
{
    function view_ficha_reporte()
    {
        $usuarios = Usuario::join('configuracion.usuario_rol', 'usuario_rol.id_usuario', '=', 'sis_usua.id_usuario')
            ->where([['sis_usua.estado', '=', 1], ['usuario_rol.id_rol', '=', 20]])->get(); //20 CAS

        return view('cas/fichasReporte/fichaReporte', compact('usuarios'));
    }

    function listarIncidencias()
    {
        $lista = DB::table('cas.incidencia')
            ->select(
                'incidencia.*',
                'guia_ven.serie',
                'guia_ven.numero',
                'guia_ven.id_od',
                'adm_contri.razon_social',
                'adm_contri.id_contribuyente',
                'adm_empresa.id_empresa',
                'empresa.razon_social as empresa_razon_social',
                'alm_req.codigo as codigo_requerimiento',
                'alm_req.id_requerimiento',
                'alm_req.concepto',
                'adm_ctb_contac.nombre',
                'adm_ctb_contac.telefono',
                'adm_ctb_contac.cargo',
                'adm_ctb_contac.direccion',
                'adm_ctb_contac.horario',
                'adm_ctb_contac.email',
                'sis_usua.nombre_corto',
                'incidencia_estado.descripcion as estado_doc',
                'incidencia_estado.bootstrap_color',
            )
            ->leftjoin('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'incidencia.id_salida')
            ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'mov_alm.id_guia_ven')
            ->leftjoin('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'guia_ven.id_od')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'incidencia.id_empresa')
            ->leftjoin('contabilidad.adm_contri as empresa', 'empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftjoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'incidencia.id_responsable')
            ->leftjoin('cas.incidencia_estado', 'incidencia_estado.id_estado', '=', 'incidencia.estado')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'incidencia.id_contribuyente')
            ->leftjoin('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_datos_contacto', '=', 'incidencia.id_contacto')
            ->where([['incidencia.estado', '!=', 7]]);

        return datatables($lista)->toJson();
    }

    function listarFichasReporte($id_incidencia)
    {
        $lista = IncidenciaReporte::where([
            ['id_incidencia', '=', $id_incidencia], ['estado', '!=', 7]
        ])->get();
        return response()->json($lista);
    }

    function guardarFichaReporte(Request $request)
    {
        try {
            DB::beginTransaction();
            $mensaje = '';
            $tipo = '';

            $reporte = new IncidenciaReporte();
            $reporte->codigo = IncidenciaReporte::nuevoCodigoFicha($request->padre_id_incidencia);
            $reporte->id_incidencia = $request->padre_id_incidencia;
            $reporte->fecha_reporte = $request->fecha_reporte;
            $reporte->id_usuario = $request->id_usuario;
            $reporte->acciones_realizadas = $request->acciones_realizadas;
            $reporte->estado = 1;
            $reporte->fecha_registro = new Carbon();
            $reporte->save();

            $mensaje = 'Se guardó la ficha reporte correctamente';
            $tipo = 'success';

            DB::commit();
            return response()->json(['tipo' => $tipo, 'mensaje' => $mensaje]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    function actualizarFichaReporte(Request $request)
    {
        try {
            DB::beginTransaction();
            $mensaje = '';
            $tipo = '';

            $reporte = IncidenciaReporte::find($request->id_incidencia_reporte);

            if ($reporte !== null) {
                $reporte->fecha_reporte = $request->fecha_reporte;
                $reporte->id_usuario = $request->id_usuario;
                $reporte->acciones_realizadas = $request->acciones_realizadas;
                $reporte->save();

                $mensaje = 'Se actualizó la ficha reporte correctamente';
                $tipo = 'success';
            } else {
                $mensaje = 'No existe la ficha reporte seleccionada';
                $tipo = 'warning';
            }

            DB::commit();
            return response()->json(['tipo' => $tipo, 'mensaje' => $mensaje]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al actualizar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    function anularFichaReporte($id_reporte)
    {
        try {
            DB::beginTransaction();
            $mensaje = '';
            $tipo = '';

            $reporte = IncidenciaReporte::find($id_reporte);

            if ($reporte !== null) {
                $reporte->estado = 7;
                $reporte->save();

                $mensaje = 'Se anuló la ficha reporte correctamente.';
                $tipo = 'success';
            } else {
                $mensaje = 'No existe la ficha reporte.';
                $tipo = 'warning';
            }

            DB::commit();
            return response()->json(['tipo' => $tipo, 'mensaje' => $mensaje]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }
}
