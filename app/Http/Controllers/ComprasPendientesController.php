<?php

namespace App\Http\Controllers;

use App\Helpers\Necesidad\RequerimientoHelper;
use App\Http\Controllers\Almacen\Movimiento\OrdenesPendientesController;
use App\Models\Almacen\Almacen;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\almacen\GuiaCompraDetalle;
use App\Models\Almacen\ProductoUbicacion;
use App\Models\Almacen\Requerimiento;
use App\Models\Almacen\Reserva;
use App\Models\Almacen\Trazabilidad;
use App\Models\Almacen\UnidadMedida;
use App\Models\Comercial\CuadroCosto\CcAmFila;
use App\Models\Comercial\CuadroCosto\CuadroCosto;
use App\Models\Configuracion\Moneda;
use App\Models\Logistica\Orden;
use App\Models\Logistica\OrdenCompraDetalle;
use App\Models\mgcp\CuadroCosto\CuadroCostoView;
use App\Models\mgcp\Oportunidad\Oportunidad;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//date_default_timezone_set('America/Lima');

use Debugbar;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class ComprasPendientesController extends Controller
{

    function viewComprasPendientes()
    {
        $condiciones = $this->select_condiciones();
        // $tp_doc = $this->select_tp_doc();
        // $bancos = $this->select_bancos();
        // $cuentas = $this->select_tipos_cuenta();
        // $responsables = $this->select_responsables();
        // $contactos = $this->select_contacto();

        $tp_moneda = $this->select_moneda();
        $tp_documento = $this->select_documento();
        $sis_identidad = $this->select_sis_identidad();
        $sedes = $this->select_sedes();
        $empresas = $this->select_mostrar_empresas();
        $tp_doc = $this->select_tp_doc();
        $clasificaciones = (new AlmacenController)->mostrar_clasificaciones_cbo();
        $subcategorias = (new AlmacenController)->mostrar_subcategorias_cbo();
        $categorias = (new AlmacenController)->mostrar_categorias_cbo();
        $unidades = (new AlmacenController)->mostrar_unidades_cbo();

        $unidades_medida = UnidadMedida::mostrar();
        $monedas = Moneda::mostrar();
    // $sedes = Auth::user()->sedesAcceso();

        $tipos = AlmacenController::mostrar_tipos_cbo();
        $almacenes = Almacen::mostrar();

        return view(
            'logistica/gestion_logistica/compras/pendientes/vista_pendientes',
            compact(
                'sedes',
                'empresas',
                'sis_identidad',
                'tp_documento',
                'tp_moneda',
                'tp_doc',
                'condiciones',
                'clasificaciones',
                'subcategorias',
                'categorias',
                'unidades',
                'unidades_medida',
                'monedas',
                'tipos',
                'almacenes'
            )
        );
    }


    public function select_condiciones()
    {
        $data = DB::table('logistica.log_cdn_pago')
            ->select('log_cdn_pago.id_condicion_pago', 'log_cdn_pago.descripcion')
            ->where('log_cdn_pago.estado', 1)
            ->orderBy('log_cdn_pago.descripcion')
            ->get();
        return $data;
    }

    public function select_moneda()
    {
        $data = DB::table('configuracion.sis_moneda')
            ->select('sis_moneda.id_moneda', 'sis_moneda.descripcion', 'sis_moneda.simbolo')
            ->where([
                ['sis_moneda.estado', '=', 1]
            ])
            ->orderBy('sis_moneda.id_moneda', 'asc')
            ->get();
        return $data;
    }

    public function select_documento()
    {
        $data = DB::table('administracion.adm_tp_docum')
            ->select('adm_tp_docum.id_tp_documento', 'adm_tp_docum.descripcion', 'adm_tp_docum.abreviatura')
            ->where([
                ['adm_tp_docum.estado', '=', 1],
                ['adm_tp_docum.descripcion', 'like', '%Orden%']
            ])
            ->orderBy('adm_tp_docum.id_tp_documento', 'asc')
            ->get();
        return $data;
    }

    public function select_sis_identidad()
    {
        $data = DB::table('contabilidad.sis_identi')
            ->select('sis_identi.id_doc_identidad', 'sis_identi.descripcion')
            ->where('sis_identi.estado', '=', 1)
            ->orderBy('sis_identi.descripcion', 'asc')->get();
        return $data;
    }

    public function select_sedes()
    {
        $data = DB::table('administracion.sis_sede')
            ->select(
                'sis_sede.*'
            )
            ->orderBy('sis_sede.id_empresa', 'asc')
            ->get();
        return $data;
    }

    public function select_mostrar_empresas()
    {
        $data = DB::table('administracion.adm_empresa')
            ->select('adm_empresa.id_empresa', 'adm_empresa.logo_empresa', 'adm_contri.nro_documento', 'adm_contri.razon_social')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->where('adm_empresa.estado', '=', 1)
            ->orderBy('adm_contri.razon_social', 'asc')
            ->get();
        return $data;
    }

    public function select_tp_doc()
    {
        $data = DB::table('contabilidad.cont_tp_doc')
            ->select('cont_tp_doc.id_tp_doc', 'cont_tp_doc.cod_sunat', 'cont_tp_doc.descripcion')
            ->where([['cont_tp_doc.estado', '=', 1]])
            ->orderBy('cont_tp_doc.id_tp_doc')
            ->get();
        return $data;
    }


    public function listarRequerimientosPendientes(Request $request)
    {

        $empresa = $request->idEmpresa??'SIN_FILTRO';
        $sede = $request->idSede??'SIN_FILTRO';
        $fechaRegistroDesde = $request->fechaRegistroDesde??'SIN_FILTRO';
        $fechaRegistroHasta = $request->fechaRegistroHasta??'SIN_FILTRO';
        $reserva = $request->reserva??'SIN_FILTRO';
        $orden = $request->orden??'SIN_FILTRO';


        $alm_req = Requerimiento::join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('almacen.tipo_cliente', 'tipo_cliente.id_tipo_cliente', '=', 'alm_req.tipo_cliente')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('comercial.com_cliente', 'alm_req.id_cliente', '=', 'com_cliente.id_cliente')
            ->leftJoin('contabilidad.adm_contri as contri_cliente', 'com_cliente.id_contribuyente', '=', 'contri_cliente.id_contribuyente')
            ->leftJoin('rrhh.rrhh_perso as perso_natural', 'alm_req.id_persona', '=', 'perso_natural.id_persona')
            ->leftJoin('configuracion.sis_moneda', 'alm_req.id_moneda', '=', 'sis_moneda.id_moneda')
            ->leftJoin('rrhh.rrhh_trab as trab_solicitado_por', 'alm_req.trabajador_id', '=', 'trab_solicitado_por.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu as postu_solicitado_por', 'postu_solicitado_por.id_postulante', '=', 'trab_solicitado_por.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as perso_solicitado_por', 'perso_solicitado_por.id_persona', '=', 'postu_solicitado_por.id_persona')
            ->leftJoin('mgcp_cuadro_costos.cc_view', 'cc_view.id', '=', 'alm_req.id_cc')


            ->select(
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.concepto',
                'alm_req.id_moneda',
                'alm_req.fecha_entrega',
                'sis_moneda.simbolo as simbolo_moneda',
                'sis_moneda.descripcion as moneda',
                'alm_req.fecha_requerimiento',
                'alm_req.id_tipo_requerimiento',
                'alm_tp_req.descripcion AS tipo_req_desc',
                'alm_req.tipo_cliente',
                'tipo_cliente.descripcion AS tipo_cliente_desc',
                'sis_usua.usuario',
                DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_usuario"),
                'rrhh_rol.id_area',
                'adm_area.descripcion AS area_desc',
                'rrhh_rol.id_rol',
                'rrhh_rol.id_rol_concepto',
                'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
                'alm_req.id_grupo',
                'adm_grupo.descripcion AS adm_grupo_descripcion',
                // 'alm_req.id_op_com',
                // 'proy_op_com.codigo as codigo_op_com',
                // 'proy_op_com.descripcion as descripcion_op_com',
                'alm_req.concepto AS alm_req_concepto',
                // 'log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion',
                'alm_req.id_cliente',
                'contri_cliente.nro_documento as cliente_ruc',
                'contri_cliente.razon_social as cliente_razon_social',
                'alm_req.id_persona',
                'perso_natural.nro_documento as dni_persona',
                DB::raw("CONCAT(perso_natural.nombres,' ', perso_natural.apellido_paterno,' ', perso_natural.apellido_materno)  AS nombre_persona"),
                'alm_req.id_prioridad',
                'alm_req.fecha_registro',
                'alm_req.trabajador_id',
                DB::raw("CONCAT(perso_solicitado_por.nombres,' ', perso_solicitado_por.apellido_paterno,' ', perso_solicitado_por.apellido_materno)  AS solicitado_por"),
                'cc_view.name as cc_solicitado_por',
                'alm_req.estado',
                'alm_req.id_empresa',
                'alm_req.id_sede',
                'alm_req.tiene_transformacion',
                'sis_sede.descripcion as empresa_sede',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                DB::raw("(CASE WHEN alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc"),
                DB::raw("(SELECT  COUNT(alm_det_req.id_detalle_requerimiento) FROM almacen.alm_det_req
                WHERE alm_det_req.id_requerimiento = alm_req.id_requerimiento and alm_det_req.tiene_transformacion=false)::integer as cantidad_items_base"),
                // DB::raw("(SELECT  COUNT(alm_det_req.id_detalle_requerimiento) FROM almacen.alm_det_req
                // WHERE alm_det_req.id_requerimiento = alm_req.id_requerimiento and alm_det_req.estado=38)::integer as cantidad_por_regularizar"),
                DB::raw("(SELECT json_agg(DISTINCT nivel.unidad) FROM almacen.alm_det_req dr
                INNER JOIN finanzas.cc_niveles_view nivel ON dr.centro_costo_id = nivel.id_centro_costo
                WHERE dr.id_requerimiento = almacen.alm_req.id_requerimiento and dr.tiene_transformacion=false ) as division"),
                DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req AS det
                WHERE det.id_requerimiento = alm_req.id_requerimiento AND det.id_tipo_item =1
                AND det.id_producto >0 and det.estado != 7 ) AS count_mapeados"),
                DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req AS det
                WHERE det.id_requerimiento = alm_req.id_requerimiento AND det.id_tipo_item =1
                AND det.id_producto is null and det.estado !=7 ) AS count_pendientes"),
                DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req AS det
                INNER JOIN almacen.alm_reserva ON det.id_detalle_requerimiento = alm_reserva.id_detalle_requerimiento
                WHERE det.id_requerimiento = alm_req.id_requerimiento AND alm_reserva.estado = 1
                AND det.estado != 7) AS count_stock_comprometido")
            )
            ->when(($empresa >0), function ($query) use($empresa) {
                return $query->where('alm_req.id_empresa','=',$empresa); 
            })
            ->when(($sede >0), function ($query) use($sede) {
                return $query->where('alm_req.id_sede','=',$sede); 
            })
            ->when(($reserva == 'SIN_RESERVA'), function ($query) {
                $query->leftJoin('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento');
                return $query->whereRaw('alm_det_req.stock_comprometido isNULL'); 
            })
            ->when(($reserva == 'CON_RESERVA'), function ($query) {
                $query->leftJoin('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento');
                $query->leftJoin('almacen.alm_reserva', 'alm_reserva.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
                return $query->whereRaw('alm_reserva.stock_comprometido >0 and alm_reserva.estado !=7'); 
            })
            ->when(($orden == 'CON_ORDEN'), function ($query) {
                $query->Join('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento');
                $query->Join('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
                return $query->whereRaw('log_det_ord_compra.id_detalle_requerimiento > 0 and log_det_ord_compra.estado !=7 '); 
            })
            ->when(($orden == 'SIN_ORDEN'), function ($query) {
                $query->Join('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento');
                return $query->rightJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
            })
            
            ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta == 'SIN_FILTRO')), function ($query) use($fechaRegistroDesde) {
                return $query->where('alm_req.fecha_requerimiento' ,'>=',$fechaRegistroDesde); 
            })
            ->when((($fechaRegistroDesde == 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use($fechaRegistroHasta) {
                return $query->where('alm_req.fecha_requerimiento' ,'<=',$fechaRegistroHasta); 
            })
            ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use($fechaRegistroDesde,$fechaRegistroHasta) {
                return $query->whereBetween('alm_req.fecha_requerimiento' ,[$fechaRegistroDesde,$fechaRegistroHasta]); 
            })

            ->whereIn('alm_req.estado', [2,15,27,38,39])
            ->where('alm_req.flg_compras','=',0);
            
            return datatables($alm_req)
            ->filterColumn('alm_req.fecha_entrega', function ($query, $keyword) {
                try {
                    $keywords = Carbon::createFromFormat('d-m-Y', trim($keyword));
                    $query->where('alm_req.fecha_entrega', $keywords);
                } catch (\Throwable $th) {
                }
            })
            ->filterColumn('alm_req.fecha_registro', function ($query, $keyword) {
                try {
                    $desde = Carbon::createFromFormat('d-m-Y', trim($keyword))->hour(0)->minute(0)->second(0);
                    $hasta = Carbon::createFromFormat('d-m-Y', trim($keyword));
                    $query->whereBetween('alm_req.fecha_registro', [$desde, $hasta->addDay()->addSeconds(-1)]);
                } catch (\Throwable $th) {
                }
            })
            // ->filterColumn('division', function ($query, $keyword) {
            //     try {
                    
            //         $query->where('nivel.unidad', trim($keyword));
            //     } catch (\Throwable $th) {
            //     }
            // })
            ->filterColumn('cc_solicitado_por', function ($query, $keyword) {
                try {
                    $query->where('cc_view.name', trim($keyword));
                } catch (\Throwable $th) {
                }
            })
            ->filterColumn('estado_doc', function ($query, $keyword) {
                try {
                    $query->where('adm_estado_doc.estado_doc', trim($keyword));
                } catch (\Throwable $th) {
                }
            })
            ->toJson();
    }

    public function listarRequerimientosAtendidos(Request $request)
    {

        $empresa = $request->idEmpresa??'SIN_FILTRO';
        $sede = $request->idSede??'SIN_FILTRO';
        $fechaRegistroDesde = $request->fechaRegistroDesde??'SIN_FILTRO';
        $fechaRegistroHasta = $request->fechaRegistroHasta??'SIN_FILTRO';
        $reserva = $request->reserva??'SIN_FILTRO';
        $orden = $request->orden??'SIN_FILTRO';


        $alm_req = Requerimiento::join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('almacen.tipo_cliente', 'tipo_cliente.id_tipo_cliente', '=', 'alm_req.tipo_cliente')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('comercial.com_cliente', 'alm_req.id_cliente', '=', 'com_cliente.id_cliente')
            ->leftJoin('contabilidad.adm_contri as contri_cliente', 'com_cliente.id_contribuyente', '=', 'contri_cliente.id_contribuyente')
            ->leftJoin('rrhh.rrhh_perso as perso_natural', 'alm_req.id_persona', '=', 'perso_natural.id_persona')
            ->leftJoin('configuracion.sis_moneda', 'alm_req.id_moneda', '=', 'sis_moneda.id_moneda')
            ->leftJoin('rrhh.rrhh_trab as trab_solicitado_por', 'alm_req.trabajador_id', '=', 'trab_solicitado_por.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu as postu_solicitado_por', 'postu_solicitado_por.id_postulante', '=', 'trab_solicitado_por.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as perso_solicitado_por', 'perso_solicitado_por.id_persona', '=', 'postu_solicitado_por.id_persona')
            ->leftJoin('mgcp_cuadro_costos.cc_view', 'cc_view.id', '=', 'alm_req.id_cc')


            ->select(
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.concepto',
                'alm_req.id_moneda',
                'alm_req.fecha_entrega',
                'sis_moneda.simbolo as simbolo_moneda',
                'sis_moneda.descripcion as moneda',
                'alm_req.fecha_requerimiento',
                'alm_req.id_tipo_requerimiento',
                'alm_tp_req.descripcion AS tipo_req_desc',
                'alm_req.tipo_cliente',
                'tipo_cliente.descripcion AS tipo_cliente_desc',
                'sis_usua.usuario',
                DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_usuario"),
                'rrhh_rol.id_area',
                'adm_area.descripcion AS area_desc',
                'rrhh_rol.id_rol',
                'rrhh_rol.id_rol_concepto',
                'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
                'alm_req.id_grupo',
                'adm_grupo.descripcion AS adm_grupo_descripcion',
                'alm_req.concepto AS alm_req_concepto',
                'alm_req.id_cliente',
                'contri_cliente.nro_documento as cliente_ruc',
                'contri_cliente.razon_social as cliente_razon_social',
                'alm_req.id_persona',
                'perso_natural.nro_documento as dni_persona',
                DB::raw("CONCAT(perso_natural.nombres,' ', perso_natural.apellido_paterno,' ', perso_natural.apellido_materno)  AS nombre_persona"),
                'alm_req.id_prioridad',
                'alm_req.fecha_registro',
                'alm_req.trabajador_id',
                DB::raw("CONCAT(perso_solicitado_por.nombres,' ', perso_solicitado_por.apellido_paterno,' ', perso_solicitado_por.apellido_materno)  AS solicitado_por"),
                'cc_view.name as cc_solicitado_por',
                'alm_req.estado',
                'alm_req.id_empresa',
                'alm_req.id_sede',
                'alm_req.tiene_transformacion',
                'sis_sede.descripcion as empresa_sede',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                DB::raw("(CASE WHEN alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc"),
                DB::raw("(SELECT  COUNT(alm_det_req.id_detalle_requerimiento) FROM almacen.alm_det_req
                WHERE alm_det_req.id_requerimiento = alm_req.id_requerimiento and alm_det_req.tiene_transformacion=false)::integer as cantidad_items_base"),
                DB::raw("(SELECT json_agg(DISTINCT nivel.unidad) FROM almacen.alm_det_req dr
                INNER JOIN finanzas.cc_niveles_view nivel ON dr.centro_costo_id = nivel.id_centro_costo
                WHERE dr.id_requerimiento = almacen.alm_req.id_requerimiento and dr.tiene_transformacion=false ) as division"),
                DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req AS det
                WHERE det.id_requerimiento = alm_req.id_requerimiento AND det.id_tipo_item =1
                AND det.id_producto >0 and det.estado != 7 ) AS count_mapeados"),
                DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req AS det
                WHERE det.id_requerimiento = alm_req.id_requerimiento AND det.id_tipo_item =1
                AND det.id_producto is null and det.estado !=7 ) AS count_pendientes"),
                DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req AS det
                INNER JOIN almacen.alm_reserva ON det.id_detalle_requerimiento = alm_reserva.id_detalle_requerimiento
                WHERE det.id_requerimiento = alm_req.id_requerimiento AND alm_reserva.estado = 1
                AND det.estado != 7) AS count_stock_comprometido")
            )
            ->when(($empresa >0), function ($query) use($empresa) {
                return $query->where('alm_req.id_empresa','=',$empresa); 
            })
            ->when(($sede >0), function ($query) use($sede) {
                return $query->where('alm_req.id_sede','=',$sede); 
            })
            ->when(($reserva == 'SIN_RESERVA'), function ($query) {
                $query->leftJoin('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento');
                return $query->whereRaw('alm_det_req.stock_comprometido isNULL'); 
            })
            ->when(($reserva == 'CON_RESERVA'), function ($query) {
                $query->leftJoin('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento');
                $query->leftJoin('almacen.alm_reserva', 'alm_reserva.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
                return $query->whereRaw('alm_reserva.stock_comprometido >0 and alm_reserva.estado !=7'); 
            })
            ->when(($orden == 'CON_ORDEN'), function ($query) {
                $query->Join('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento');
                $query->Join('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
                return $query->whereRaw('log_det_ord_compra.id_detalle_requerimiento > 0 and log_det_ord_compra.estado !=7 '); 
            })
            ->when(($orden == 'SIN_ORDEN'), function ($query) {
                $query->Join('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento');
                return $query->rightJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
            })
            
            ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta == 'SIN_FILTRO')), function ($query) use($fechaRegistroDesde) {
                return $query->where('alm_req.fecha_requerimiento' ,'>=',$fechaRegistroDesde); 
            })
            ->when((($fechaRegistroDesde == 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use($fechaRegistroHasta) {
                return $query->where('alm_req.fecha_requerimiento' ,'<=',$fechaRegistroHasta); 
            })
            ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use($fechaRegistroDesde,$fechaRegistroHasta) {
                return $query->whereBetween('alm_req.fecha_requerimiento' ,[$fechaRegistroDesde,$fechaRegistroHasta]); 
            })
            ->where('alm_req.flg_compras','=',0)
            ->whereIn('alm_req.estado', [5,28]);
            
            return datatables($alm_req)
            ->filterColumn('alm_req.fecha_entrega', function ($query, $keyword) {
                try {
                    $keywords = Carbon::createFromFormat('d-m-Y', trim($keyword));
                    $query->where('alm_req.fecha_entrega', $keywords);
                } catch (\Throwable $th) {
                }
            })
            ->filterColumn('alm_req.fecha_registro', function ($query, $keyword) {
                try {
                    $desde = Carbon::createFromFormat('d-m-Y', trim($keyword))->hour(0)->minute(0)->second(0);
                    $hasta = Carbon::createFromFormat('d-m-Y', trim($keyword));
                    $query->whereBetween('alm_req.fecha_registro', [$desde, $hasta->addDay()->addSeconds(-1)]);
                } catch (\Throwable $th) {
                }
            })
            ->filterColumn('cc_solicitado_por', function ($query, $keyword) {
                try {
                    $query->where('cc_view.name', trim($keyword));
                } catch (\Throwable $th) {
                }
            })
            ->filterColumn('estado_doc', function ($query, $keyword) {
                try {
                    $query->where('adm_estado_doc.estado_doc', trim($keyword));
                } catch (\Throwable $th) {
                }
            })
            ->toJson();
    }

    public function get_lista_items_cuadro_costos_por_id_requerimiento(Request $request)
    {
        $requerimientoList = $request->requerimientoList;
        $temp_data = [];
        $data = [];

        if (count($requerimientoList) > 0) {

            $alm_req = DB::table('almacen.alm_req')
                ->select('alm_req.id_cc')
                ->whereIn('alm_req.id_requerimiento', $requerimientoList)
                ->orderBy('alm_req.id_requerimiento', 'desc')
                ->get();


            foreach ($alm_req as $element) {
                $cuadroCostos = ((new RequerimientoController)->get_detalle_cuadro_costos($element->id_cc));
                $temp_data[] = $cuadroCostos['detalle'];
                $headCuadroCostos = $cuadroCostos['head'];
            }

            if (count($temp_data) > 0) {
                foreach ($temp_data as $arr) {
                    foreach ($arr as $value) {
                        $data[] = $value;
                    }
                }
                $status = 200;
            } else {
                $status = 204;
            }
        }

        $output = ['status' => $status, 'head' => $headCuadroCostos, 'detalle' => $data];

        return response()->json($output);
    }

    function getGrupoSelectItemParaCompra()
    {

        $output = [];
        $clasificaciones = (new AlmacenController)->mostrar_clasificaciones_cbo();
        $subcategorias = (new AlmacenController)->mostrar_subcategorias_cbo();
        $categorias = (new AlmacenController)->mostrar_categorias_cbo();
        $monedas = (new LogisticaController)->mostrar_moneda();
        $unidades_medida = (new AlmacenController)->mostrar_unidades_cbo();
        $output[] = [
            'categoria' => $categorias,
            'subcategoria' => $subcategorias,
            'clasificacion' => $clasificaciones,
            'moneda' => $monedas,
            'unidad_medida' => $unidades_medida
        ];
        return response()->json($output);
    }

    function guardarReservaAlmacen(Request $request)
    {

        try {
            DB::beginTransaction();

            $mensaje='';
            $crearNuevaReserva=true;
            $codigoOIdReservaAnulada= '';
            $idRequerimientoList=[];
            

            $requerimientoHelper = new RequerimientoHelper();
            if($requerimientoHelper->EstaHabilitadoRequerimiento([$request->idDetalleRequerimiento])==true){
                $ReservasProductoActivas = Reserva::where([['id_detalle_requerimiento',$request->idDetalleRequerimiento],['estado',1]])->get();
                foreach ($ReservasProductoActivas as $value) {
                    if($value->id_almacen_reserva == $request->almacenReserva && $value->stock_comprometido == $request->cantidadReserva){
                        $crearNuevaReserva=false;
                        $mensaje.='No puede generar una reserva que actualmente existe con mismo almacén y misma cantidad a reservar';
                    }
                    if($value->id_almacen_reserva == $request->almacenReserva){
                        $reservaMismoAlmacen = Reserva::where([['id_detalle_requerimiento',$request->idDetalleRequerimiento],['estado',1],
                        ['id_almacen_reserva',$request->almacenReserva]])->first();
                        $reservaMismoAlmacen->estado=7;
                        $reservaMismoAlmacen->save();
                        $codigoOIdReservaAnulada= $reservaMismoAlmacen->codigo?$reservaMismoAlmacen->codigo:$reservaMismoAlmacen->id_reserva;
                        $crearNuevaReserva= true;
    
                    }
                }
                $reserva = new Reserva();
                if($crearNuevaReserva==true){
                    $reserva->codigo = Reserva::crearCodigo();
                    $reserva->id_unidad_medida = $request->idUnidadMedida;
                    $reserva->id_detalle_requerimiento = $request->idDetalleRequerimiento;
                    $reserva->id_producto = $request->idProducto;
                    $reserva->id_almacen_reserva = $request->almacenReserva;
                    $reserva->stock_comprometido = $request->cantidadReserva;
                    $reserva->usuario_registro =  Auth::user()->id_usuario;
                    $reserva->fecha_registro =  new Carbon();
                    $reserva->estado = 1;
                    $reserva->save();
    
                    if($request->idDetalleRequerimiento > 0){
                        $idRequerimientoList[]= DetalleRequerimiento::find($request->idDetalleRequerimiento)->first()->id_requerimiento;
                    }
        
                }
    
                
                if($reserva->id_reserva > 0){
     
                        $mensaje.=' Se creo nueva reserva '.$reserva->codigo;
                
                    OrdenesPendientesController::validaProdUbi($request->idProducto, $request->almacenReserva);
                    if(strlen($codigoOIdReservaAnulada)>0){
                        $mensaje.=' en remplazo por la reserva '.$codigoOIdReservaAnulada;
                    }
                } 
    
                $ReservasProductoActualizadas = Reserva::with('almacen','usuario.trabajador.postulante.persona','estado')->where([['id_detalle_requerimiento',$request->idDetalleRequerimiento], ['estado',1]])->get();
    
    
                
                DB::commit();
    
                DetalleRequerimiento::actualizarEstadoDetalleRequerimientoAtendido($request->idDetalleRequerimiento);
                // actualizar estado de requerimiento
                $Requerimiento = DetalleRequerimiento::where('id_detalle_requerimiento',$request->idDetalleRequerimiento)->first();
                $nuevoEstado=  Requerimiento::actualizarEstadoRequerimientoAtendido([$Requerimiento->id_requerimiento]);
    
                return response()->json(['id_reserva'=>$reserva->id_reserva,'codigo'=>$reserva->codigo,'lista_finalizados'=>$nuevoEstado['lista_finalizados'],'data'=>$ReservasProductoActualizadas,'tipo_estado'=>'success','estado_requerimiento'=>$nuevoEstado['estado_actual'] ,'mensaje'=>$mensaje]);

            }else{
                return response()->json(['id_reserva'=>'','codigo'=>'','lista_finalizados'=>[],'data'=>[],'tipo_estado'=>'warning','estado_requerimiento'=>null ,'mensaje'=>'No puede guardar la reserva, existe un requerimiento vinculado con estado "En pausa" o  "Por regularizar"']);

            }


        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['id_reserva' => 0, 'codigo' => '','lista_finalizados'=>[], 'data'=>[],'estado_requerimiento'=>[] ,'tipo_estado'=>'error','mensaje' => 'Hubo un problema al guardar la reserva. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);

        }
    }


    function obtenerStockAlmacen(Request $request){
            $stock=0;
            $saldo=0;

            $productoUbicacion=ProductoUbicacion::where([['id_producto',$request->idProducto],['id_almacen',$request->idAlmacen],['estado',1]])->first();
            $cantidadReservadas=0;
            $reservasActivas = Reserva::where([['id_producto',$request->idProducto],['estado',1]])->get();
            foreach ($reservasActivas as $value) {
                $cantidadReservadas+=floatval($value->stock_comprometido);
            }
            if(!empty($productoUbicacion)){
                $stock= $productoUbicacion->stock;
                $saldo = floatval($stock)  - floatval($cantidadReservadas);
            }
            return response()->json(['stock'=>$stock,'saldo'=>$saldo,'reservas'=>$cantidadReservadas]);

    }

    function anularReservaAlmacen(Request $request)
    {
        
        try {
            DB::beginTransaction();
        
            $requerimientoHelper = new RequerimientoHelper();
            if($requerimientoHelper->EstaHabilitadoRequerimiento([$request->idDetalleRequerimiento])==true){

                $reserva = Reserva::where('id_reserva',$request->idReserva)->first();
                $reserva->estado=7;
                $reserva->save();
    
            
                $ReservasProductoActualizadas = Reserva::with('almacen','usuario.trabajador.postulante.persona','estado')->where([['id_detalle_requerimiento',$request->idDetalleRequerimiento], ['estado',1]])->get();
                DetalleRequerimiento::actualizarEstadoDetalleRequerimientoAtendido($request->idDetalleRequerimiento);
                // actualizar estado de requerimiento
                $Requerimiento = DetalleRequerimiento::where('id_detalle_requerimiento',$request->idDetalleRequerimiento)->first();
                $nuevoEstado= Requerimiento::actualizarEstadoRequerimientoAtendido([$Requerimiento->id_requerimiento]);
    
            //     (new LogisticaController)->generarTransferenciaRequerimiento($id_requerimiento, $id_sede, $data);
                DB::commit();
                return response()->json(['id_reserva'=>$reserva->id_reserva,'data'=>$ReservasProductoActualizadas, 'tipo_estado'=>'success', 'estado_requerimiento'=>$nuevoEstado['estado_actual'],'lista_finalizados'=>$nuevoEstado['lista_finalizados'],'lista_restablecidos'=>$nuevoEstado['lista_restablecidos']]);
            }else{
                return response()->json(['id_reserva'=>0,'data'=>[],'tipo_estado'=>'warning','mensaje'=>'No puede anular la reserva, existe un requerimiento vinculado con estado "En pausa" o  "Por regularizar"']);

            }


        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['id_reserva' => 0, 'data'=>[],'estado_requerimiento'=>[] ,'tipo_estado'=>'error', 'mensaje' => 'Hubo un problema en el servidor al intentar anular la reserva. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);

        }
    }
    function anularTodaReservaAlmacenDetalleRequerimiento(Request $request)
    {
        
        try {
            DB::beginTransaction();
            $tipo_estado='warning';
            $requerimientoHelper = new RequerimientoHelper();
            if($requerimientoHelper->EstaHabilitadoRequerimiento([$request->idDetalleRequerimiento])==true){

                $reservas = Reserva::where([['id_detalle_requerimiento',$request->idDetalleRequerimiento],['estado','!=',7]])->get();
                foreach ($reservas as $r) {
                    $reserva= Reserva::where('id_reserva',$r->id_reserva)->first();
                    $reserva->estado=7;
                    $reserva->save();
                    $tipo_estado='success';
                }
    
            
                $ReservasProductoActualizadas = Reserva::with('almacen','usuario.trabajador.postulante.persona','estado')->where([['id_detalle_requerimiento',$request->idDetalleRequerimiento], ['estado',1]])->get();
                DetalleRequerimiento::actualizarEstadoDetalleRequerimientoAtendido($request->idDetalleRequerimiento);
                // actualizar estado de requerimiento
                $Requerimiento = DetalleRequerimiento::where('id_detalle_requerimiento',$request->idDetalleRequerimiento)->first();
                $nuevoEstado= Requerimiento::actualizarEstadoRequerimientoAtendido([$Requerimiento->id_requerimiento]);
    
                DB::commit();
                return response()->json(['data'=>$ReservasProductoActualizadas, 'tipo_estado'=>$tipo_estado, 'estado_requerimiento'=>$nuevoEstado['estado_actual'],'lista_finalizados'=>$nuevoEstado['lista_finalizados'],'lista_restablecidos'=>$nuevoEstado['lista_restablecidos']]);
            }else{
                return response()->json(['data'=>[],'tipo_estado'=>'warning','mensaje'=>'No puede anular la reserva, existe un requerimiento vinculado con estado "En pausa" o  "Por regularizar"']);

            }


        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['id_reserva' => 0, 'data'=>[],'estado_requerimiento'=>[] ,'tipo_estado'=>'error', 'mensaje' => 'Hubo un problema en el servidor al intentar anular la reserva. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);

        }
    }

    function buscarItemCatalogo(Request $request)
    {
        $part_number = $request->part_number;
        $descripcion = $request->descripcion;
        $where = [];
        if ($part_number !== null && $part_number !== '') {
            $where = [['alm_prod.part_number', '=', strtoupper($part_number)], ['alm_prod.estado', '=', 1]];
        } else if ($descripcion !== null && $descripcion !== '') {
            // $where=[['alm_prod.descripcion','like','%'.$descripcion.'%'],['alm_prod.estado','=',1]];
            $where = [['alm_prod.descripcion', '=', strtoupper($descripcion)], ['alm_prod.estado', '=', 1]];
        }

        $alm_prod = DB::table('almacen.alm_prod')
            ->select(
                'alm_item.id_item',
                'alm_item.codigo AS codigo_item',
                'alm_prod.id_producto',
                'alm_prod.codigo AS alm_prod_codigo',
                'alm_prod.part_number',
                'alm_prod.descripcion',
                'alm_prod.id_unidad_medida',
                'alm_prod.id_moneda',
                'sis_moneda.descripcion as moneda',
                'alm_prod.id_categoria',
                'alm_prod.id_subcategoria',
                'alm_prod.id_clasif',
                'alm_und_medida.descripcion as unidad_medida',
                'alm_cat_prod.descripcion as categoria',
                'alm_subcat.descripcion as subcategoria',
                'alm_clasif.descripcion as clasificacion'
            )
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'alm_prod.id_moneda')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftJoin('almacen.alm_item', 'alm_item.id_producto', '=', 'alm_prod.id_producto')
            ->leftJoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->leftJoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->leftJoin('almacen.alm_clasif', 'alm_clasif.id_clasificacion', '=', 'alm_prod.id_clasif')
            ->where($where)
            ->get();

        return response()->json($alm_prod);
    }

    function guardarItemsEnDetalleRequerimiento(Request $request)
    {
        $id_requerimiento_list = $request->id_requerimiento_list;
        $id_requerimiento = $id_requerimiento_list[0]; // solo toma el primero id_requerimieno
        $items = $request->items;
        $status = '';
        $newIdDetalleRequerimientoList = [];
        $cantidadItemSinCodigoProducto = 0;
        $idProductosGuardadosDetalleReq = [];
        $msj = [];
        $count_items = count($items);
        if ($count_items > 0) {


            $alm_det_req = DB::table('almacen.alm_det_req')
                ->select(
                    'alm_det_req.*'
                )
                ->where([['alm_det_req.id_requerimiento', '=', $id_requerimiento], ['alm_det_req.estado', '!=', 7]])
                ->get();

            foreach ($alm_det_req as $value) {
                $dProductosGuardadosDetalleReq[] = $value->id_producto;
            }


            for ($i = 0; $i < $count_items; $i++) {
                if ($items[$i]['id_producto'] > 0 && in_array(!$items[$i]['id_producto'], $idProductosGuardadosDetalleReq)) {

                    $alm_det_req = DB::table('almacen.alm_det_req')->insertGetId(

                        [
                            'id_requerimiento'      => $id_requerimiento,
                            'id_item'               => is_numeric($items[$i]['id_item']) == 1 && $items[$i]['id_item'] > 0 ? $items[$i]['id_item'] : null,
                            'id_cc_am_filas'        => is_numeric($items[$i]['id_cc_am_filas']) == 1 && $items[$i]['id_cc_am_filas'] > 0 ? $items[$i]['id_cc_am_filas'] : null,
                            'id_cc_venta_filas'     => is_numeric($items[$i]['id_cc_venta_filas']) == 1 && $items[$i]['id_cc_venta_filas'] > 0 ? $items[$i]['id_cc_venta_filas'] : null,
                            'id_producto'           => is_numeric($items[$i]['id_producto']) == 1 && $items[$i]['id_producto'] > 0 ? $items[$i]['id_producto'] : null,
                            'precio_unitario'       => is_numeric($items[$i]['precio_unitario']) == 1 ? $items[$i]['precio_unitario'] : null,
                            'cantidad'              => $items[$i]['cantidad'] ? $items[$i]['cantidad'] : null,
                            'id_moneda'             => $items[$i]['id_moneda'] ? $items[$i]['id_moneda'] : null,
                            'descripcion_adicional' => isset($items[$i]['descripcion']) ? $items[$i]['descripcion'] : null,
                            'id_unidad_medida'      => is_numeric($items[$i]['id_unidad_medida']) == 1 ? $items[$i]['id_unidad_medida'] : null,
                            'id_tipo_item'          => 1,
                            'fecha_registro'        => date('Y-m-d H:i:s'),
                            'estado'                => 1,
                            'tiene_transformacion'  => isset($items[$i]['tiene_transformacion']) ? $items[$i]['tiene_transformacion'] : false


                        ],
                        'id_detalle_requerimiento'
                    );

                    $newIdDetalleRequerimientoList[] = $alm_det_req;
                } else {
                    $cantidadItemSinCodigoProducto++;
                }
            }
        }

        if ($cantidadItemSinCodigoProducto > 0) {
            $msj[] = 'No se pudo agregar un item que no este guardado primero en el catálogo';
        }

        if (count($newIdDetalleRequerimientoList) > 0) {
            $status = 200;
            $msj[] = 'Items guardados!';
        } else {
            $status = 204;
            $msj[] = 'No se guardaron items';
        }

        $output = [
            'id_detalle_requerimiento_list' => $newIdDetalleRequerimientoList,
            'status' => $status,
            'mensaje' => $msj
        ];

        return response()->json($output);
    }


    // function listarItemsPorRegularizar($idRequerimiento){
    //     $data=  DetalleRequerimiento::where([['id_requerimiento',$idRequerimiento],['por_regularizar','=',true],['estado','!=',7]])->with('producto','reserva','unidadMedida')->get();
    //     return $data;
    // }

    function listarPorRegularizarCabecera($idRequerimiento){
        $requerimiento=Requerimiento::find($idRequerimiento);
        $cp= CuadroCosto::find($requerimiento->id_cc);
        $oportunidad = Oportunidad::find($cp->id_oportunidad);
        $cabecerra=['codigo_requerimiento'=>$requerimiento->codigo, 
        'codigo_cuadro_presupuesto'=>$oportunidad->codigo_oportunidad];
        return $cabecerra;
    }

    function listarPorRegularizarDetalle($idRequerimiento){
        $detalleRequerimientoList=  DetalleRequerimiento::where([['id_requerimiento',$idRequerimiento],['estado','!=',7]])->with('producto','detalle_orden.orden','detalle_orden.guia_compra_detalle.movimiento_detalle.movimiento','reserva','unidadMedida')->get();
        $requerimiento=Requerimiento::find($idRequerimiento);
        
        $detalle=[];
        foreach ($detalleRequerimientoList as $detalleRequerimiento) {
                $ccAmFila= CcAmFila::where([['id_cc_am',$requerimiento->id_cc],['id',$detalleRequerimiento->id_cc_am_filas]])->first();
                $detalleRequerimiento->setAttribute('detalle_cc',$ccAmFila);
                $detalle[]=$detalleRequerimiento;
                
                
            }

        return ['data'=>$detalle];
    }

 

    function restablecerEstadoDetalleRequerimiento($idDetalleRequerimiento){
        $detalleRequerimiento = DetalleRequerimiento::find($idDetalleRequerimiento);
        $ordenesCompraDetalle = OrdenCompraDetalle::where([['id_detalle_requerimiento',$idDetalleRequerimiento],['estado','!=',7]])->get();
        $reservas = Reserva::where([['id_detalle_requerimiento',$idDetalleRequerimiento],['estado','!=',7]])->get();
        $cantidadAtendidaOrden=0;
        $cantidadAtendidaReserva=0;
        $totalAtendido=0;
        $estadoDetalle=1;
        foreach ($ordenesCompraDetalle as $ocd) {
            $cantidadAtendidaOrden+=$ocd->cantidad;
            $totalAtendido+=$ocd->cantidad;
            
        }
        foreach ($reservas as $r) {
            $cantidadAtendidaReserva+=$r->stock_comprometido;
            $totalAtendido+=$r->stock_comprometido;
        }
        if($cantidadAtendidaOrden >0 && $cantidadAtendidaReserva > 0){
            // estado atendido parcial o atentido total
        }
        if($cantidadAtendidaOrden > 0 && $cantidadAtendidaReserva ==0){
            // estado atendido parcial o atentido total

        }
        if($cantidadAtendidaOrden== 0 && $cantidadAtendidaReserva >0){
            // estado atentido parcial o reserva total
        }

        if($totalAtendido == $detalleRequerimiento->cantidad){
            if($cantidadAtendidaReserva ==$detalleRequerimiento->cantidad){
                $estadoDetalle= 28; // almacen total
            }
            if($cantidadAtendidaOrden ==$detalleRequerimiento->cantidad){
                $estadoDetalle= 5; // atendido total
            }
            if(($cantidadAtendidaOrden+$cantidadAtendidaReserva) ==$detalleRequerimiento->cantidad){
                $estadoDetalle= 5; // atendido total
            }
        }else{
            $estadoDetalle= 15; // atendido parcial
        }

        $detalleRequerimiento->estado = $estadoDetalle;
        $detalleRequerimiento->save();
    }
    
    function existeDetalleRequerimientoPorRegularizar($idDetalleRequerimiento){
        $cantidadPorRegularizar=0;
        $detalleRequerimiento = DetalleRequerimiento::where('id_requerimiento',$idDetalleRequerimiento)->get();
        foreach ($detalleRequerimiento as $dr) {
            if($dr->estado == 38){
                $cantidadPorRegularizar++;
            }
        }
        
        return $cantidadPorRegularizar > 0 ? true:false;

    }


    function realizarRemplazoDeProductoEnTodaOrden(Request $request){
        DB::beginTransaction();
        try {
        // $request->idDetalleRequerimiento;
        $status=0;
        $cambiaEstadoRequerimiento=false;
        $detalleOrdenesAfectadas=[];
        if($request->idDetalleRequerimiento > 0){
            
            // trazabilidad interna
            $detalleRequerimiento = DetalleRequerimiento::find($request->idDetalleRequerimiento);
            $ordenDetalle=OrdenCompraDetalle::where([['id_detalle_requerimiento',$request->idDetalleRequerimiento],['estado','!=',7]])->get();

            $status=200;
            $mensaje="Remplazo realizado con éxito";

            foreach ($ordenDetalle as $ocd) {
                $detalleOrdenesAfectadas[]= [
                    'id_detalle_orden'=>$ocd->id_detalle_orden,
                    'id_orden_compra'=>$ocd->id_orden_compra,
                    'id_producto'=>$ocd->id_producto,
                    'id_detalle_requerimiento'=>$ocd->id_detalle_requerimiento
                ];
            }

            if(count($detalleOrdenesAfectadas)>0){
                $trazabilidad = new Trazabilidad();
                $trazabilidad->id_requerimiento = $detalleRequerimiento->id_requerimiento;
                $trazabilidad->id_usuario = Auth::user()->id_usuario;
                $trazabilidad->accion = 'Remplazo de producto';
                $trazabilidad->descripcion = 'Se remplazó el ID producto de las ordenes '.json_encode($detalleOrdenesAfectadas);
                $trazabilidad->fecha_registro = new Carbon();
                $trazabilidad->save();

            }
            // fin trazabilidad 
            OrdenCompraDetalle::where([['id_detalle_requerimiento',$request->idDetalleRequerimiento],['estado','!=',7]])->update(['id_producto' => $detalleRequerimiento->id_producto]);
            DetalleRequerimiento::actualizarEstadoDetalleRequerimientoAtendido($request->idDetalleRequerimiento);

        }else{
            $mensaje="el ID enviado no es valido, que no fue posible realizar el remplazo";
            $status=202;
        }

        DB::commit();
        $detalleRequerimiento = DetalleRequerimiento::find($request->idDetalleRequerimiento);

        if($this->existeDetalleRequerimientoPorRegularizar($detalleRequerimiento->id_requerimiento)==false){
            Requerimiento::actualizarEstadoRequerimientoAtendido([$detalleRequerimiento->id_requerimiento]);
            $cambiaEstadoRequerimiento=true;
        }
     

        return response()->json(['status' => $status,'cambiaEstadoRequerimiento'=>$cambiaEstadoRequerimiento,'mensaje'=>$mensaje]);
    } catch (Exception $e) {
        DB::rollBack();
        return response()->json(['status' => $status,'cambiaEstadoRequerimiento'=>$cambiaEstadoRequerimiento, 'mensaje' => 'Hubo un problema al remplazar el item en todas las orden relacionadas. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
    }
    }


    function realizarLiberacionDeProductoEnTodaOrden(Request $request){
        DB::beginTransaction();
        try {
         // $request->idDetalleRequerimiento;
        $status=0;
        $cambiaEstadoRequerimiento=false;
        if($request->idDetalleRequerimiento > 0){

            // trazabilidad interna
            $detalleRequerimiento = DetalleRequerimiento::find($request->idDetalleRequerimiento);
            $ordenDetalle=OrdenCompraDetalle::where([['id_detalle_requerimiento',$request->idDetalleRequerimiento],['estado','!=',7]])->get();
            foreach ($ordenDetalle as $ocd) {
                $detalleOrdenesAfectadas[]= [
                    'id_detalle_orden'=>$ocd->id_detalle_orden,
                    'id_orden_compra'=>$ocd->id_orden_compra,
                    'id_producto'=>$ocd->id_producto,
                    'id_detalle_requerimiento'=>$ocd->id_detalle_requerimiento
                ];
            }
            if(count($detalleOrdenesAfectadas)>0){
                $trazabilidad = new Trazabilidad();
                $trazabilidad->id_requerimiento = $detalleRequerimiento->id_requerimiento;
                $trazabilidad->id_usuario = Auth::user()->id_usuario;
                $trazabilidad->accion = 'Liberación de producto';
                $trazabilidad->descripcion = 'Se liberó el ID producto de las ordenes '.json_encode($detalleOrdenesAfectadas);
                $trazabilidad->fecha_registro = new Carbon();
                $trazabilidad->save();
            }
            // fin trazabilidad 
            OrdenCompraDetalle::where([['id_detalle_requerimiento',$request->idDetalleRequerimiento],['estado','!=',7]])->update(['id_producto' => null]);
            DetalleRequerimiento::actualizarEstadoDetalleRequerimientoAtendido($request->idDetalleRequerimiento);
            $status=200;
            $mensaje='Liberación realizada con éxito';
            
        }else{
            $mensaje="El ID enviado no es valido, que no fue posible realizar la liberación";
            $status=202;
        }
        DB::commit();

        $detalleRequerimiento = DetalleRequerimiento::find($request->idDetalleRequerimiento);

        if($this->existeDetalleRequerimientoPorRegularizar($detalleRequerimiento->id_requerimiento)==false){
            Requerimiento::actualizarEstadoRequerimientoAtendido([$detalleRequerimiento->id_requerimiento]);
            $cambiaEstadoRequerimiento=true;

        }

        return response()->json(['status' => $status,'cambiaEstadoRequerimiento'=>$cambiaEstadoRequerimiento,'mensaje'=>$mensaje]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => $status,'cambiaEstadoRequerimiento'=>$cambiaEstadoRequerimiento, 'mensaje' => 'Hubo un problema al remplazar liberar el item. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

    function realizarAnularItemEnTodaOrdenYReservas(Request $request){
        DB::beginTransaction();
        try {
         // $request->idDetalleRequerimiento;
        $status=0;
        $cambiaEstadoRequerimiento=false;
        $detalleOrdenesAfectadas=[];
        $reservasAfectadas=[];
        if($request->idDetalleRequerimiento > 0){

            // trazabilidad interna
            $ordenDetalle=OrdenCompraDetalle::where([['id_detalle_requerimiento',$request->idDetalleRequerimiento],['estado','!=',7]])->get();
            $reservas=Reserva::where([['id_detalle_requerimiento',$request->idDetalleRequerimiento],['estado','!=',7]])->get();
            $detalleRequerimiento = DetalleRequerimiento::find($request->idDetalleRequerimiento);
            foreach ($ordenDetalle as $ocd) {
                $detalleOrdenesAfectadas[]= [
                    'id_detalle_orden'=>$ocd->id_detalle_orden,
                    'id_orden_compra'=>$ocd->id_orden_compra,
                    'id_producto'=>$ocd->id_producto,
                    'id_detalle_requerimiento'=>$ocd->id_detalle_requerimiento
                ];
            }
            foreach ($reservas as $re) {
                $reservasAfectadas[]= [
                    'id_reserva'=>$re->id_reserva,
                    'id_producto'=>$re->id_producto,
                    'id_detalle_requerimiento'=>$re->id_detalle_requerimiento
                ];
            }
            // Debugbar::info($detalleOrdenesAfectadas);
            // Debugbar::info($reservasAfectadas);

            if(count($detalleOrdenesAfectadas)>0){
                $trazabilidad = new Trazabilidad();
                $trazabilidad->id_requerimiento = $detalleRequerimiento->id_requerimiento;
                $trazabilidad->id_usuario = Auth::user()->id_usuario;
                $trazabilidad->accion = 'Anulación de item - ordenes';
                $trazabilidad->descripcion = 'Se anuló el item de las ordenes '.json_encode($detalleOrdenesAfectadas);
                $trazabilidad->fecha_registro = new Carbon();
                $trazabilidad->save();
            }
            if(count($reservasAfectadas)>0){
                $trazabilidad = new Trazabilidad();
                $trazabilidad->id_requerimiento = $detalleRequerimiento->id_requerimiento;
                $trazabilidad->id_usuario = Auth::user()->id_usuario;
                $trazabilidad->accion = 'Anulación de item - reservas';
                $trazabilidad->descripcion = 'Se anuló el item de la reservas '.json_encode($reservasAfectadas);
                $trazabilidad->fecha_registro = new Carbon();
                $trazabilidad->save();
            }
            // fin trazabilidad 

            OrdenCompraDetalle::where([['id_detalle_requerimiento',$request->idDetalleRequerimiento]])->update(['estado' => 7]);
            Reserva::where([['id_detalle_requerimiento',$request->idDetalleRequerimiento]])->update(['estado' => 7]);
            DetalleRequerimiento::actualizarEstadoDetalleRequerimientoAtendido($request->idDetalleRequerimiento);

            $status=200;
            $mensaje='Anulación realizada con éxito';
        }else{
            $status=202;
            $mensaje="El ID enviado no es valido, que no fue posible realizar la anulación";
        }
        DB::commit();
        $detalleRequerimiento = DetalleRequerimiento::find($request->idDetalleRequerimiento);

        if($this->existeDetalleRequerimientoPorRegularizar($detalleRequerimiento->id_requerimiento)==false){
            Requerimiento::actualizarEstadoRequerimientoAtendido([$detalleRequerimiento->id_requerimiento]);
            $cambiaEstadoRequerimiento=true;
        }

        return response()->json(['status' => $status,'cambiaEstadoRequerimiento'=>$cambiaEstadoRequerimiento,'mensaje'=>$mensaje]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => $status,'cambiaEstadoRequerimiento'=>$cambiaEstadoRequerimiento, 'mensaje' => 'Hubo un problema al intentar anular. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

    function itemsOrdenItemsReservaPorDetalleRequerimiento($idDetalleRequerimiento){
        DB::beginTransaction();
        try {

        $status=0;
        $mensaje='';
        $reservas=[];
        $detalleOrdenes=[];
        $data=[];
        if($idDetalleRequerimiento > 0){
            $detalleOrdenes=    OrdenCompraDetalle::with('orden','producto.subcategoria','unidad_medida','estado_orden')->where([['id_detalle_requerimiento',$idDetalleRequerimiento],['estado','!=',7]])->get();
            $reservas = Reserva::with('producto.subcategoria','unidad_medida')->where([['id_detalle_requerimiento',$idDetalleRequerimiento],['estado','!=',7]])->get();

            foreach ($detalleOrdenes as $do) {
                $data[]=[
                    'id_detalle_orden'=>$do->id_detalle_orden,
                    'id_reserva'=>null,
                    'codigo_documento'=>$do->orden->codigo,
                    'part_number'=>$do->producto->part_number??'',
                    'descripcion'=>$do->producto->descripcion??'',
                    'unidad_medida'=>$do->unidad_medida->abreviatura??'',
                    'cantidad'=>$do->cantidad??'',
                    'estado'=>$do->estado_orden->descripcion??''
                ];
            }
            foreach ($reservas as $r) {
                $data[]=[
                    'id_detalle_orden'=>null,
                    'id_reserva'=>$r->id_reserva,
                    'codigo_documento'=>$r->codigo,
                    'part_number'=>$r->producto->part_number??'',
                    'descripcion'=>$r->producto->descripcion??'',
                    'unidad_medida'=>$r->unidad_medida->abreviatura??'',
                    'cantidad'=>$r->stock_comprometido??'',
                    'estado'=>$r->nombre_estado
                ];
            }

            $status=200;
            $mensaje='OK';

        }else{
            $status=201;
            $mensaje='El id enviado no es valido';

        }
        DB::commit();


        return response()->json(['status' => $status,'mensaje'=>$mensaje,'data'=>$data]);
    } catch (Exception $e) {
        DB::rollBack();
        return response()->json(['status' => $status, 'mensaje' => 'Hubo un problema al intentar obtener lista detalle de orden y reservas. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage(),'data'=>[]]);
    }
    }





}


 