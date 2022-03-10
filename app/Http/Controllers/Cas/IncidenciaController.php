<?php

namespace App\Http\Controllers\Cas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Administracion\Division;
use App\Models\Almacen\Movimiento;
use App\Models\Cas\AtiendeIncidencia;
use App\Models\Cas\Incidencia;
use App\Models\Cas\IncidenciaProducto;
use App\Models\Cas\IncidenciaProductoTipo;
use App\Models\Cas\MedioReporte;
use App\Models\Cas\ModoIncidencia;
use App\Models\Cas\TipoFalla;
use App\Models\Cas\TipoGarantia;
use App\Models\Cas\TipoServicio;
use App\Models\Configuracion\Usuario;
use App\Models\Distribucion\OrdenDespacho;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IncidenciaController extends Controller
{
    function view_incidencia()
    {
        $tipoFallas = TipoFalla::where('estado', 1)->get();
        $tipoServicios = TipoServicio::where('estado', 1)->get();
        $divisiones = DB::table('administracion.division')->where([['estado', '=', 1], ['grupo_id', '=', 2]])->get();
        $usuarios = Usuario::join('configuracion.usuario_rol', 'usuario_rol.id_usuario', '=', 'sis_usua.id_usuario')
            ->where([['sis_usua.estado', '=', 1], ['usuario_rol.id_rol', '=', 20]])->get(); //20 CAS
        $medios = MedioReporte::where('estado', 1)->get();
        $modos = ModoIncidencia::where('estado', 1)->get();
        $atiende = AtiendeIncidencia::where('estado', 1)->get();
        $tiposGarantia = TipoGarantia::where('estado', 1)->get();
        $tiposProducto = IncidenciaProductoTipo::where('estado', 1)->get();

        return view('cas/incidencias/incidencia', compact(
            'tipoFallas',
            'tipoServicios',
            'usuarios',
            'divisiones',
            'medios',
            'modos',
            'atiende',
            'tiposGarantia',
            'tiposProducto'
        ));
    }

    function listarSalidasVenta()
    {
        // join('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'mov_alm.id_guia_ven')
        // ->join('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'guia_ven.id_od')
        $lista = OrdenDespacho::join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
            ->join('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'alm_req.id_empresa')
            ->leftjoin('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_datos_contacto', '=', 'alm_req.id_contacto')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->where([['orden_despacho.estado', '!=', '7'], ['orden_despacho.aplica_cambios', '=', false]])
            // ->where([['mov_alm.estado', '!=', '7'], ['mov_alm.id_tp_mov', '=', 2], ['mov_alm.id_operacion', '=', '1']])
            ->select(
                'orden_despacho.id_od',
                // 'guia_ven.serie',
                // 'guia_ven.numero',
                // 'guia_ven.id_od',
                'adm_contri.razon_social',
                'adm_contri.id_contribuyente',
                'adm_empresa.id_empresa',
                'alm_req.codigo as codigo_requerimiento',
                'alm_req.id_requerimiento',
                'alm_req.concepto',
                'alm_req.id_contacto',
                'adm_ctb_contac.nombre',
                'adm_ctb_contac.telefono',
                'adm_ctb_contac.cargo',
                'adm_ctb_contac.direccion',
                'adm_ctb_contac.horario',
                'adm_ctb_contac.email',
                'oportunidades.codigo_oportunidad',
                'oc_propias_view.id_entidad',
            );
        return datatables($lista)->toJson();
    }

    function listarSeriesProductos($id_guia_ven)
    {
        $lista = DB::table('almacen.alm_prod_serie')
            ->select(
                'alm_prod_serie.id_prod_serie',
                'alm_prod_serie.serie',
                'alm_prod.id_producto',
                'alm_prod.codigo',
                'alm_prod.part_number',
                'alm_prod.descripcion',
                'alm_prod_serie.id_guia_ven_det'
            )
            ->join('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven_det', '=', 'alm_prod_serie.id_guia_ven_det')
            ->join('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_prod_serie.id_prod')
            ->where('guia_ven.id_guia_ven', $id_guia_ven);

        return datatables($lista)->toJson();
    }

    function listarIncidencias()
    {
        $lista = Incidencia::with('contribuyente', 'responsable', 'estado')->where([['estado', '!=', 7]]);
        return datatables($lista)->toJson();
    }

    function mostrarIncidencia($id)
    {
        // $incidencia = Incidencia::with('contribuyente', 'contacto', 'responsable', 'estado')
        $incidencia = DB::table('cas.incidencia')
            ->select(
                'incidencia.*',
                'guia_ven.serie',
                'guia_ven.numero',
                'guia_ven.id_od',
                'adm_contri.razon_social',
                'adm_contri.id_contribuyente',
                'adm_empresa.id_empresa',
                'alm_req.codigo as codigo_requerimiento',
                'alm_req.id_requerimiento',
                'alm_req.concepto',
                'alm_req.id_contacto',
                'adm_ctb_contac.nombre',
                'adm_ctb_contac.telefono',
                'adm_ctb_contac.cargo',
                'adm_ctb_contac.direccion',
                'adm_ctb_contac.horario',
                'adm_ctb_contac.email',
                'oportunidades.codigo_oportunidad',
                'oc_propias_view.id_entidad'
            )
            ->leftjoin('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'incidencia.id_salida')
            ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'mov_alm.id_guia_ven')
            ->leftjoin('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'guia_ven.id_od')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'incidencia.id_empresa')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'incidencia.id_contribuyente')
            ->leftjoin('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_datos_contacto', '=', 'incidencia.id_contacto')
            ->where('incidencia.id_incidencia', $id)
            ->first();

        // $productos = IncidenciaProducto::with('producto')
        //     ->where([['id_incidencia', '=', $id], ['estado', '!=', 7]])
        //     ->get();
        $productos = IncidenciaProducto::where([['id_incidencia', '=', $id], ['estado', '!=', 7]])
            ->get();

        return response()->json(['incidencia' => $incidencia, 'productos' => $productos]);
    }

    function guardarIncidencia(Request $request)
    {
        try {
            DB::beginTransaction();
            $mensaje = '';
            $tipo = '';
            $yyyy = date('Y', strtotime("now"));

            $incidencia = new Incidencia();
            $incidencia->codigo = Incidencia::nuevoCodigoIncidencia($request->id_empresa, $yyyy);
            $incidencia->fecha_reporte = $request->fecha_reporte;
            $incidencia->id_responsable = $request->id_responsable;
            $incidencia->id_salida = $request->id_mov_alm;
            $incidencia->id_empresa = $request->id_empresa;
            $incidencia->sede_cliente = $request->sede_cliente;
            $incidencia->factura = $request->factura;
            $incidencia->id_contribuyente = $request->id_contribuyente;
            $incidencia->id_contacto = $request->id_contacto;
            $incidencia->usuario_final = $request->usuario_final;
            $incidencia->id_tipo_falla = $request->id_tipo_falla;
            $incidencia->id_tipo_servicio = $request->id_tipo_servicio;
            $incidencia->id_medio = $request->id_medio;
            $incidencia->conformidad = $request->conformidad;
            $incidencia->equipo_operativo = ((isset($request->equipo_operativo) && $request->equipo_operativo == 'on') ? true : false);
            $incidencia->falla_reportada = $request->falla_reportada;
            $incidencia->id_modo = $request->id_modo;
            $incidencia->id_tipo_garantia = $request->id_tipo_garantia;
            $incidencia->id_atiende = $request->id_atiende;
            $incidencia->numero_caso = $request->numero_caso;
            $incidencia->anio = $yyyy;
            $incidencia->estado = 1;
            $incidencia->fecha_registro = new Carbon();
            $incidencia->save();

            $detalle = json_decode($request->detalle);

            foreach ($detalle as $det) {
                $producto = new IncidenciaProducto();
                $producto->id_incidencia = $incidencia->id_incidencia;
                $producto->id_producto = $det->id_producto;
                $producto->id_prod_serie = $det->id_prod_serie;
                $producto->serie = $det->serie;
                $producto->id_usuario = Auth::user()->id_usuario;
                $producto->producto = $det->producto;
                $producto->marca = $det->marca;
                $producto->modelo = $det->modelo;
                $producto->id_tipo = $det->id_tipo;
                $producto->estado = 1;
                $producto->fecha_registro = new Carbon();
                $producto->save();
            }

            $mensaje = 'Se guardó la incidencia correctamente';
            $tipo = 'success';

            DB::commit();
            return response()->json(['tipo' => $tipo, 'mensaje' => $mensaje]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    function actualizarIncidencia(Request $request)
    {
        try {
            DB::beginTransaction();
            $mensaje = '';
            $tipo = '';

            $incidencia = Incidencia::find($request->id_incidencia);

            if ($incidencia !== null) {

                $incidencia->fecha_reporte = $request->fecha_reporte;
                $incidencia->id_responsable = $request->id_responsable;
                $incidencia->id_salida = $request->id_mov_alm;
                $incidencia->sede_cliente = $request->sede_cliente;
                $incidencia->factura = $request->factura;
                $incidencia->id_contribuyente = $request->id_contribuyente;
                $incidencia->id_contacto = $request->id_contacto;
                $incidencia->usuario_final = $request->usuario_final;
                $incidencia->id_tipo_falla = $request->id_tipo_falla;
                $incidencia->id_tipo_servicio = $request->id_tipo_servicio;
                $incidencia->id_medio = $request->id_medio;
                $incidencia->conformidad = $request->conformidad;
                $incidencia->equipo_operativo = ($request->equipo_operativo == 'on' ? true : false);
                $incidencia->falla_reportada = $request->falla_reportada;
                $incidencia->id_modo = $request->id_modo;
                $incidencia->id_tipo_garantia = $request->id_tipo_garantia;
                $incidencia->id_atiende = $request->id_atiende;
                $incidencia->numero_caso = $request->numero_caso;
                $incidencia->save();

                $detalle = json_decode($request->detalle);

                foreach ($detalle as $det) {
                    // $producto = IncidenciaProducto::where([['id_incidencia', '=', $det->id_incidencia], ['id_prod_serie', '=', $det->id_prod_serie]])->first();
                    $producto = IncidenciaProducto::where('id_incidencia_producto', $det->id_incidencia_producto)->first();

                    if ($producto == null) {
                        $producto = new IncidenciaProducto();
                        $producto->id_incidencia = $incidencia->id_incidencia;
                        $producto->id_producto = $det->id_producto;
                        $producto->id_prod_serie = $det->id_prod_serie;
                        $producto->id_usuario = Auth::user()->id_usuario;
                        $producto->serie = $det->serie;
                        $producto->producto = $det->producto;
                        $producto->marca = $det->marca;
                        $producto->modelo = $det->modelo;
                        $producto->id_tipo = $det->id_tipo;
                        $producto->estado = 1;
                        $producto->fecha_registro = new Carbon();
                        $producto->save();
                    } else {
                        $producto->serie = $det->serie;
                        $producto->producto = $det->producto;
                        $producto->marca = $det->marca;
                        $producto->modelo = $det->modelo;
                        $producto->id_tipo = $det->id_tipo;
                        $producto->save();
                    }
                }
                $mensaje = 'Se actualizó la incidencia correctamente';
                $tipo = 'success';
            } else {
                $mensaje = 'No existe la incidencia seleccionada';
                $tipo = 'warning';
            }

            DB::commit();
            return response()->json(['tipo' => $tipo, 'mensaje' => $mensaje]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    function anularIncidencia($id_incidencia)
    {
        try {
            DB::beginTransaction();
            $mensaje = '';
            $tipo = '';

            $incidencia = Incidencia::find($id_incidencia);

            if ($incidencia !== null) {
                $incidencia->estado = 7;
                $incidencia->save();

                $mensaje = 'Se anuló la incidencia correctamente.';
                $tipo = 'success';
            } else {
                $mensaje = 'No existe la incidencia.';
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
