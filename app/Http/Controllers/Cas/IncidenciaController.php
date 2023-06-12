<?php

namespace App\Http\Controllers\Cas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Administracion\Division;
use App\Models\Administracion\Empresa;
use App\Models\Almacen\Movimiento;
use App\Models\Cas\AtiendeIncidencia;
use App\Models\Cas\CasMarca;
use App\Models\Cas\CasModelo;
use App\Models\Cas\CasProducto;
use App\Models\Cas\Incidencia;
use App\Models\Cas\IncidenciaProducto;
use App\Models\Cas\IncidenciaProductoTipo;
use App\Models\Cas\IncidenciaReporte;
use App\Models\Cas\MedioReporte;
use App\Models\Cas\ModoIncidencia;
use App\Models\Cas\TipoFalla;
use App\Models\Cas\TipoGarantia;
use App\Models\Cas\TipoServicio;
use App\Models\Configuracion\Usuario;
use App\Models\Distribucion\OrdenDespacho;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class IncidenciaController extends Controller
{
    function view_incidencia()
    {
        $tipoFallas = TipoFalla::where('estado', 1)->get();
        $tipoServicios = TipoServicio::where('estado', 1)->get();
        $divisiones = DB::table('administracion.division')->where([['estado', '=', 1], ['grupo_id', '=', 2]])->get();
        $usuarios = Usuario::join('configuracion.usuario_rol', 'usuario_rol.id_usuario', '=', 'sis_usua.id_usuario')
            ->where([['sis_usua.estado', '=', 1], ['usuario_rol.id_rol', '=', 20], ['usuario_rol.estado', '=', 1]])->get(); //20 CAS

        $medios = MedioReporte::where('estado', 1)->get();
        $modos = ModoIncidencia::where('estado', 1)->get();
        $atiende = AtiendeIncidencia::where('estado', 1)->get();
        $tiposGarantia = TipoGarantia::where('estado', 1)->get();
        $tiposProducto = IncidenciaProductoTipo::where('estado', 1)->get();
        $empresas = Empresa::mostrar();

        $cas_marca = CasMarca::where('estado',1)->orderBy('descripcion','ASC')->get();
        $cas_modelo = CasModelo::where('estado',1)->orderBy('descripcion','ASC')->get();
        $cas_producto = CasProducto::where('estado',1)->orderBy('descripcion','ASC')->get();

        return view('cas.incidencias.incidencia', compact(
            'tipoFallas',
            'tipoServicios',
            'usuarios',
            'divisiones',
            'medios',
            'modos',
            'atiende',
            'tiposGarantia',
            'tiposProducto',
            'empresas',
            'cas_marca',
            'cas_modelo',
            'cas_producto',
        ));
    }

    function listarSalidasVenta()
    {
        $lista = DB::table('mgcp_ordenes_compra.oc_propias_view')
            ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'oc_propias_view.id_oportunidad')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id_oportunidad', '=', 'oportunidades.id')
            ->leftjoin('almacen.alm_req', 'alm_req.id_cc', '=', 'cc.id')

            ->leftjoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'alm_req.id_empresa')
            ->leftjoin('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_datos_contacto', '=', 'alm_req.id_contacto')
            // ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            // ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            // ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            // ->where([['orden_despacho.estado', '!=', '7'], ['orden_despacho.aplica_cambios', '=', false]])
            // ->where([['mov_alm.estado', '!=', '7'], ['mov_alm.id_tp_mov', '=', 2], ['mov_alm.id_operacion', '=', '1']])
            ->select(
                // 'orden_despacho.id_od',
                // 'adm_contri.razon_social',
                'oc_propias_view.id',
                'oc_propias_view.nro_orden',
                'oc_propias_view.nombre_entidad as razon_social',
                'oc_propias_view.id_empresa',
                'oc_propias_view.id_entidad',
                'oportunidades.codigo_oportunidad',
                'alm_req.codigo as codigo_requerimiento',
                'alm_req.id_requerimiento',
                'alm_req.id_contacto',
                'adm_contri.id_contribuyente',
                'adm_ctb_contac.nombre',
                'adm_ctb_contac.telefono',
                'adm_ctb_contac.cargo',
                'adm_ctb_contac.direccion',
                'adm_ctb_contac.horario',
                'adm_ctb_contac.email',
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
        // $lista = Incidencia::with('contribuyente', 'responsable', 'estado')->where([['estado', '!=', 7]]);
        $lista = DB::table('cas.incidencia')
        ->select(
                'incidencia.id_incidencia',
                'incidencia.codigo',
                'incidencia.fecha_reporte',
                'incidencia.fecha_documento',
                'adm_contri.razon_social',
                'adm_contri.id_contribuyente',
                'sis_usua.nombre_corto',
                'incidencia.factura',
                'incidencia.falla_reportada',
                'incidencia_estado.descripcion as estado_descripcion',
            )
            ->leftjoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'incidencia.id_responsable')
            ->leftjoin('cas.incidencia_estado', 'incidencia_estado.id_estado', '=', 'incidencia.estado')
            ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'incidencia.id_empresa')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'incidencia.id_contribuyente')
            ->where([['incidencia.estado', '!=', 7]])
            ->get();
        return datatables($lista)->toJson();
    }

    function mostrarIncidencia($id)
    {
        // $incidencia = Incidencia::with('contribuyente', 'contacto', 'responsable', 'estado')
        $incidencia = DB::table('cas.incidencia')
            ->select(
                'incidencia.*',
                'adm_contri.razon_social',
                'adm_contri.id_contribuyente',
                'adm_empresa.id_empresa',
                'alm_req.codigo as codigo_requerimiento',
                'alm_req.concepto',
                // 'adm_ctb_contac.nombre',
                // 'adm_ctb_contac.telefono',
                // 'adm_ctb_contac.cargo',
                // 'adm_ctb_contac.direccion',
                'adm_ctb_contac.horario',
                'adm_ctb_contac.email',
                'oportunidades.codigo_oportunidad',
                'oc_propias_view.id_entidad',
                DB::raw("(ubi_dpto.descripcion)||' '||(ubi_prov.descripcion)||' '||(ubi_dis.descripcion) as ubigeo_descripcion")
            )
            // ->leftjoin('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'incidencia.id_salida')
            // ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'mov_alm.id_guia_ven')
            // ->leftjoin('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'guia_ven.id_od')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'incidencia.id_requerimiento')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'incidencia.id_empresa')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'incidencia.id_contribuyente')
            ->leftjoin('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_datos_contacto', '=', 'incidencia.id_contacto')
            ->leftjoin('configuracion.ubi_dis', 'ubi_dis.id_dis', '=', 'incidencia.id_ubigeo_contacto')
            ->leftjoin('configuracion.ubi_prov', 'ubi_prov.id_prov', '=', 'ubi_dis.id_prov')
            ->leftjoin('configuracion.ubi_dpto', 'ubi_dpto.id_dpto', '=', 'ubi_prov.id_dpto')
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
            $yyyy = date('Y',  strtotime($request->fecha_documento));

            $incidencia = new Incidencia();
            $incidencia->codigo = Incidencia::nuevoCodigoIncidencia($request->id_empresa, $request->fecha_documento);
            $incidencia->fecha_reporte = $request->fecha_reporte;
            $incidencia->id_requerimiento = $request->id_requerimiento;
            $incidencia->id_responsable = $request->id_responsable;
            $incidencia->id_salida = $request->id_mov_alm;
            $incidencia->id_empresa = $request->id_empresa;
            $incidencia->sede_cliente = $request->sede_cliente;
            $incidencia->factura = $request->factura;
            $incidencia->fecha_documento = $request->fecha_documento;
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
            $incidencia->importe_gastado = $request->importe_gastado;
            $incidencia->comentarios_cierre = $request->comentarios_cierre;
            $incidencia->parte_reemplazada = $request->parte_reemplazada;
            $incidencia->cliente = $request->cliente_razon_social;
            $incidencia->nro_orden = $request->nro_orden;
            $incidencia->nombre_contacto = $request->nombre_contacto;
            $incidencia->cargo_contacto = $request->cargo_contacto;
            $incidencia->id_ubigeo_contacto = $request->id_ubigeo_contacto;
            $incidencia->telefono_contacto = $request->telefono_contacto;
            $incidencia->direccion_contacto = $request->direccion_contacto;
            $incidencia->anio = $yyyy;
            $incidencia->estado = 1;
            $incidencia->fecha_registro = new Carbon();

            $incidencia->serie = $request->serie;
            $incidencia->producto = $request->producto;
            $incidencia->marca = $request->marca;
            $incidencia->modelo = $request->modelo;
            $incidencia->id_tipo = $request->id_tipo;

            $incidencia->horario_contacto = $request->horario_contacto;
            $incidencia->email_contacto = $request->email_contacto;
            $incidencia->cdp = $request->cdp;
            $incidencia->save();

            // $detalle = json_decode($request->detalle);

            // foreach ($detalle as $det) {
            //     $producto = new IncidenciaProducto();
            //     $producto->id_incidencia = $incidencia->id_incidencia;
            //     $producto->id_producto = $det->id_producto;
            //     $producto->id_prod_serie = $det->id_prod_serie;
            //     $producto->id_usuario = Auth::user()->id_usuario;
            //     $producto->serie = $det->serie;
            //     $producto->producto = $det->producto;
            //     $producto->marca = $det->marca;
            //     $producto->modelo = $det->modelo;
            //     $producto->id_tipo = $det->id_tipo;
            //     $producto->estado = 1;
            //     $producto->fecha_registro = new Carbon();
            //     $producto->save();
            // }

            $mensaje = 'Se guardó la incidencia correctamente';
            $tipo = 'success';

            DB::commit();
            return response()->json(['incidencia' => $incidencia, 'tipo' => $tipo, 'mensaje' => $mensaje]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    function actualizarIncidencia(Request $request)
    {
        // return $request->all();exit;
        try {
            DB::beginTransaction();
            $mensaje = '';
            $tipo = '';

            $incidencia = Incidencia::find($request->id_incidencia);

            if ($incidencia !== null) {

                $incidencia->fecha_reporte = $request->fecha_reporte;
                $incidencia->id_requerimiento = $request->id_requerimiento;
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
                $incidencia->importe_gastado = $request->importe_gastado;
                $incidencia->comentarios_cierre = $request->comentarios_cierre;
                $incidencia->parte_reemplazada = $request->parte_reemplazada;
                $incidencia->cliente = $request->cliente_razon_social;
                $incidencia->nro_orden = $request->nro_orden;
                $incidencia->nombre_contacto = $request->nombre_contacto;
                $incidencia->cargo_contacto = $request->cargo_contacto;
                $incidencia->id_ubigeo_contacto = $request->id_ubigeo_contacto;
                $incidencia->telefono_contacto = $request->telefono_contacto;
                $incidencia->direccion_contacto = $request->direccion_contacto;

                $incidencia->serie = $request->serie;
                $incidencia->producto = $request->producto;
                $incidencia->marca = $request->marca;
                $incidencia->modelo = $request->modelo;
                $incidencia->id_tipo = $request->id_tipo;

                $incidencia->horario_contacto = $request->horario_contacto;
                $incidencia->email_contacto = $request->email_contacto;
                $incidencia->cdp = $request->cdp;
                $incidencia->fecha_documento = $request->fecha_documento;
                $incidencia->save();

                // $detalle = json_decode($request->detalle);

                // foreach ($detalle as $det) {
                //     $producto = IncidenciaProducto::where('id_incidencia_producto', $det->id_incidencia_producto)->first();

                //     if ($producto == null) {
                //         $producto = new IncidenciaProducto();
                //         $producto->id_incidencia = $incidencia->id_incidencia;
                //         $producto->id_producto = $det->id_producto;
                //         $producto->id_prod_serie = $det->id_prod_serie;
                //         $producto->id_usuario = Auth::user()->id_usuario;
                //         $producto->serie = $det->serie;
                //         $producto->producto = $det->producto;
                //         $producto->marca = $det->marca;
                //         $producto->modelo = $det->modelo;
                //         $producto->id_tipo = $det->id_tipo;
                //         $producto->estado = 1;
                //         $producto->fecha_registro = new Carbon();
                //         $producto->save();
                //     } else {
                //         $producto->serie = $det->serie;
                //         $producto->producto = $det->producto;
                //         $producto->marca = $det->marca;
                //         $producto->modelo = $det->modelo;
                //         $producto->id_tipo = $det->id_tipo;
                //         $producto->save();
                //     }
                // }
                $mensaje = 'Se actualizó la incidencia correctamente';
                $tipo = 'success';
            } else {
                $mensaje = 'No existe la incidencia seleccionada';
                $tipo = 'warning';
            }

            DB::commit();
            return response()->json(['incidencia' => $incidencia, 'tipo' => $tipo, 'mensaje' => $mensaje]);
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

    function imprimirIncidencia($id_incidencia)
    {
        $incidencia = DB::table('cas.incidencia')
            ->select(
                'incidencia.*',
                'adm_contri.razon_social',
                'adm_contri.id_contribuyente',
                'adm_empresa.id_empresa',
                'adm_empresa.logo_empresa',
                'alm_req.codigo as codigo_requerimiento',
                'alm_req.concepto',
                'incidencia_tipo_falla.descripcion as tipo_falla_descripcion',
                'incidencia_tipo_servicio.descripcion as tipo_servicio_descripcion',
                'incidencia_tipo_garantia.descripcion as tipo_garantia_descripcion',
                'incidencia_modo.descripcion as modo_descripcion',
                'incidencia_medio.descripcion as medio_descripcion',
                'incidencia_atiende.descripcion as atiende_descripcion',
                'incidencia_estado.descripcion as estado_descripcion',
                'oportunidades.codigo_oportunidad',
                'incidencia_producto_tipo.descripcion as tipo_descripcion',
                DB::raw("(ubi_dpto.descripcion)||' '||(ubi_prov.descripcion)||' '||(ubi_dis.descripcion) as ubigeo_descripcion")
            )
            // ->leftjoin('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'incidencia.id_salida')
            // ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'mov_alm.id_guia_ven')
            // ->leftjoin('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'guia_ven.id_od')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'incidencia.id_requerimiento')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'incidencia.id_empresa')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'incidencia.id_contribuyente')
            ->leftjoin('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_datos_contacto', '=', 'incidencia.id_contacto')
            ->leftjoin('cas.incidencia_tipo_falla', 'incidencia_tipo_falla.id_tipo_falla', '=', 'incidencia.id_tipo_falla')
            ->leftjoin('cas.incidencia_tipo_servicio', 'incidencia_tipo_servicio.id_tipo_servicio', '=', 'incidencia.id_tipo_servicio')
            ->leftjoin('cas.incidencia_tipo_garantia', 'incidencia_tipo_garantia.id_tipo_garantia', '=', 'incidencia.id_tipo_garantia')
            ->leftjoin('cas.incidencia_modo', 'incidencia_modo.id_modo', '=', 'incidencia.id_modo')
            ->leftjoin('cas.incidencia_medio', 'incidencia_medio.id_medio', '=', 'incidencia.id_medio')
            ->leftjoin('cas.incidencia_atiende', 'incidencia_atiende.id_atiende', '=', 'incidencia.id_atiende')
            ->leftjoin('cas.incidencia_estado', 'incidencia_estado.id_estado', '=', 'incidencia.estado')
            ->leftjoin('configuracion.ubi_dis', 'ubi_dis.id_dis', '=', 'incidencia.id_ubigeo_contacto')
            ->leftjoin('configuracion.ubi_prov', 'ubi_prov.id_prov', '=', 'ubi_dis.id_prov')
            ->leftjoin('configuracion.ubi_dpto', 'ubi_dpto.id_dpto', '=', 'ubi_prov.id_dpto')
            ->leftjoin('cas.incidencia_producto_tipo', 'incidencia_producto_tipo.id_tipo', '=', 'incidencia.id_tipo')
            ->where('incidencia.id_incidencia', $id_incidencia)
            ->first();

        $productos = IncidenciaProducto::where([['id_incidencia', '=', $id_incidencia], ['estado', '!=', 7]])->get();

        $reportes = IncidenciaReporte::with('usuario')->where('id_incidencia', $id_incidencia)->get();

        $logo_empresa = ".$incidencia->logo_empresa";
        $fecha_registro =  (new Carbon($incidencia->fecha_registro))->format('d-m-Y');
        $hora_registro = (new Carbon($incidencia->fecha_registro))->format('H:i:s');

        $vista = View::make(
            'cas/fichasReporte/incidenciaPdf',
            compact(
                'incidencia',
                'logo_empresa',
                'productos',
                'reportes',
                'fecha_registro',
                'hora_registro'
            )
        )->render();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($vista);

        return $pdf->stream();
        return $pdf->download($incidencia->codigo . '.pdf');

        // return response()->json(['incidencia' => $incidencia, 'productos' => $productos, 'reporte' => $reporte]);
    }


    function imprimirFichaAtencionBlanco($id_incidencia)
    {
        $incidencia = DB::table('cas.incidencia')
            ->select(
                'incidencia.*',
                'adm_contri.razon_social',
                'adm_contri.id_contribuyente',
                'adm_empresa.id_empresa',
                'adm_empresa.logo_empresa',
                'alm_req.codigo as codigo_requerimiento',
                'alm_req.concepto',
                'incidencia_tipo_falla.descripcion as tipo_falla_descripcion',
                'incidencia_tipo_servicio.descripcion as tipo_servicio_descripcion',
                'incidencia_tipo_garantia.descripcion as tipo_garantia_descripcion',
                'incidencia_modo.descripcion as modo_descripcion',
                'incidencia_medio.descripcion as medio_descripcion',
                'incidencia_atiende.descripcion as atiende_descripcion',
                'incidencia_estado.descripcion as estado_descripcion',
                'oportunidades.codigo_oportunidad',
                'incidencia_producto_tipo.descripcion as tipo_descripcion',
                DB::raw("(ubi_dpto.descripcion)||' '||(ubi_prov.descripcion)||' '||(ubi_dis.descripcion) as ubigeo_descripcion")
            )
            // ->leftjoin('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'incidencia.id_salida')
            // ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'mov_alm.id_guia_ven')
            // ->leftjoin('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'guia_ven.id_od')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'incidencia.id_requerimiento')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'incidencia.id_empresa')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'incidencia.id_contribuyente')
            ->leftjoin('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_datos_contacto', '=', 'incidencia.id_contacto')
            ->leftjoin('cas.incidencia_tipo_falla', 'incidencia_tipo_falla.id_tipo_falla', '=', 'incidencia.id_tipo_falla')
            ->leftjoin('cas.incidencia_tipo_servicio', 'incidencia_tipo_servicio.id_tipo_servicio', '=', 'incidencia.id_tipo_servicio')
            ->leftjoin('cas.incidencia_tipo_garantia', 'incidencia_tipo_garantia.id_tipo_garantia', '=', 'incidencia.id_tipo_garantia')
            ->leftjoin('cas.incidencia_modo', 'incidencia_modo.id_modo', '=', 'incidencia.id_modo')
            ->leftjoin('cas.incidencia_medio', 'incidencia_medio.id_medio', '=', 'incidencia.id_medio')
            ->leftjoin('cas.incidencia_atiende', 'incidencia_atiende.id_atiende', '=', 'incidencia.id_atiende')
            ->leftjoin('cas.incidencia_estado', 'incidencia_estado.id_estado', '=', 'incidencia.estado')
            ->leftjoin('configuracion.ubi_dis', 'ubi_dis.id_dis', '=', 'incidencia.id_ubigeo_contacto')
            ->leftjoin('configuracion.ubi_prov', 'ubi_prov.id_prov', '=', 'ubi_dis.id_prov')
            ->leftjoin('configuracion.ubi_dpto', 'ubi_dpto.id_dpto', '=', 'ubi_prov.id_dpto')
            ->leftjoin('cas.incidencia_producto_tipo', 'incidencia_producto_tipo.id_tipo', '=', 'incidencia.id_tipo')
            ->where('incidencia.id_incidencia', $id_incidencia)
            ->first();

        $productos = IncidenciaProducto::where([['id_incidencia', '=', $id_incidencia], ['estado', '!=', 7]])
            ->get();

        $logo_empresa = ".$incidencia->logo_empresa";
        $fecha_registro =  (new Carbon($incidencia->fecha_registro))->format('d-m-Y');
        $hora_registro = (new Carbon($incidencia->fecha_registro))->format('H:i:s');

        $vista = View::make(
            'cas/fichasReporte/fichaAtencionBlancoPdf',
            compact(
                'incidencia',
                'logo_empresa',
                'productos',
                'fecha_registro',
                'hora_registro'
            )
        )->render();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($vista);

        return $pdf->stream();
        return $pdf->download('FichaAtencionBlanco.pdf');

        // return response()->json(['incidencia' => $incidencia, 'productos' => $productos, 'reporte' => $reporte]);
    }
}
