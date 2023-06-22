<?php

namespace App\Http\Controllers;

use App\Helpers\Almacen\AlmacenDashboardHelper;
use App\Models\Administracion\Empresa;
use App\Models\Administracion\Sede;
use App\Models\Almacen\Movimiento;
use App\Models\Almacen\MovimientoDetalle;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteIngresosExcel;
use App\Exports\ReporteSalidasExcel;
use App\models\Configuracion\AccesosUsuarios;
use App\Exports\ReporteStockSeriesExcel;
use App\Models\almacen\Catalogo\Categoria;
use App\Models\almacen\Catalogo\Clasificacion;
use App\Models\almacen\Catalogo\Marca;
use App\Models\Almacen\Catalogo\SubCategoria;
use App\Models\Almacen\StockSeriesView;
use Exception;
use Yajra\DataTables\Facades\DataTables;

date_default_timezone_set('America/Lima');

class AlmacenController extends Controller
{
    public function __construct()
    {
        // session_start();
    }
    function view_main_almacen()
    {
        $almacenDashboardHelper = new AlmacenDashboardHelper();

        $cantidades = AlmacenController::cantidades_main();
        $cantidad_requerimientos = $cantidades['requerimientos'];
        $cantidad_ordenes_pendientes = $almacenDashboardHelper->obtenerOrdenes(); //$cantidades['orden'];
        $cantidad_despachos_pendientes = $cantidades['despachos'];

        $cantidad_ingresos_pendientes = $cantidades['ingresos'];
        $cantidad_salidas_pendientes = $cantidades['salidas'];
        $cantidad_transferencias_pendientes = $cantidades['transferencias'];
        // $cantidad_pagos_pendientes = $cantidades['pagos'];
        $cantidad_transformaciones_pendientes = $cantidades['transformaciones_pend'];

        return view('almacen/main', compact(
            'cantidad_requerimientos',
            'cantidad_ordenes_pendientes',
            'cantidad_despachos_pendientes',
            'cantidad_ingresos_pendientes',
            'cantidad_salidas_pendientes',
            'cantidad_transferencias_pendientes',
            // 'cantidad_pagos_pendientes',
            'cantidad_transformaciones_pendientes'
        ));
    }
    public static function cantidades_main()
    {
        $requerimientos = DB::table('almacen.alm_req')
            ->select(
                'alm_req.estado',
                'alm_req.id_tipo_requerimiento',
                'alm_req.confirmacion_pago',
                'orden_despacho.id_od',
                DB::raw("(SELECT COUNT(*) FROM almacen.trans where
                        trans.id_requerimiento = alm_req.id_requerimiento
                        and trans.estado != 7) AS count_transferencia"),
                DB::raw("(SELECT COUNT(*) FROM almacen.trans where
                            trans.id_requerimiento = alm_req.id_requerimiento
                            and trans.estado = 14) AS count_transferencia_recibida")
            )
            ->leftJoin('almacen.orden_despacho', function ($join) {
                $join->on('orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento');
                $join->where('orden_despacho.estado', '!=', 7);
            })
            ->where('alm_req.estado', 9)
            ->orWhere('alm_req.estado', 20)
            ->orWhere([['alm_req.id_tipo_requerimiento', '=', 1], ['alm_req.estado', '=', 19], ['orden_despacho.id_od', '=', null]])
            ->orWhere([
                ['alm_req.id_tipo_requerimiento', '!=', 1], ['alm_req.estado', '=', 19], ['alm_req.confirmacion_pago', '=', true],
                ['orden_despacho.id_od', '=', null]
            ])
            ->get();

        $despachos = 0;
        foreach ($requerimientos as $req) {
            if ($req->estado == 19 && $req->id_tipo_requerimiento == 1 && $req->count_transferencia == $req->count_transferencia_recibida) {
                $despachos++;
            } else {
                $despachos++;
            }
        }

        $req = DB::table('almacen.alm_req')
            ->where([['alm_req.estado', '=', 1]])
            ->count();

        $orden = DB::table('almacen.alm_req')
            ->orWhere([['alm_req.estado', '=', 5]])
            ->orWhere([['alm_req.estado', '=', 15]])
            ->count();

        $ordenes = DB::table('logistica.log_ord_compra')
            ->where([
                ['log_ord_compra.estado', '!=', 7],
                ['log_ord_compra.en_almacen', '=', false]
            ])
            ->whereIn('log_ord_compra.id_tp_documento', [2, 12])
            ->count();

        $transformaciones = DB::table('almacen.transformacion')
            ->join('almacen.guia_ven', function ($join) {
                $join->on('guia_ven.id_od', '=', 'transformacion.id_od');
                $join->where('guia_ven.estado', '!=', 7);
            })
            ->where([['transformacion.estado', '=', 10]])
            ->count();

        $ingresos = $ordenes + $transformaciones;

        $salidas = DB::table('almacen.orden_despacho')
            ->where('estado', 1)
            ->where('flg_despacho', 0)
            ->count();

        $transferencias = DB::table('almacen.trans')
            ->where('estado', 1)
            ->orWhere('estado', 17)
            ->count();

        // $pagos = DB::table('almacen.alm_req')
        // ->where([['id_tipo_requerimiento','=',1],['estado','=',1],['confirmacion_pago','=',false]])
        // ->orWhere([['id_tipo_requerimiento','!=',1],['estado','=',19],['confirmacion_pago','=',false]])
        // ->count();

        $transformaciones_pend = DB::table('almacen.transformacion')
            ->where('estado', 1)
            ->orWhere('estado', 21)
            ->orWhere('estado', 24)
            ->count();

        return ([
            'requerimientos' => $req, 'orden' => $orden, 'despachos' => $despachos,
            'ingresos' => $ingresos, 'salidas' => $salidas,
            'transferencias' => $transferencias, 'transformaciones_pend' => $transformaciones_pend
        ]);
    }


    // function view_tipo_servicio(){
    //     return view('almacen/variables/tipoServ');
    // }
    // function view_servicio(){
    //     $tipos = $this->mostrar_tp_servicios_cbo();
    //     $detracciones = $this->mostrar_detracciones_cbo();
    //     return view('almacen/variables/servicio', compact('tipos','detracciones'));
    // }
    function view_tipo_movimiento()
    {
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
        $array_accesos_botonera=array();
        $accesos_botonera = AccesosUsuarios::where('accesos_usuarios.estado','=',1)
        ->select('accesos.*')
        ->join('configuracion.accesos','accesos.id_acceso','=','accesos_usuarios.id_acceso')
        ->where('accesos_usuarios.id_usuario',Auth::user()->id_usuario)
        ->where('accesos_usuarios.id_modulo',65)
        ->where('accesos_usuarios.id_padre',4)
        ->get();
        foreach ($accesos_botonera as $key => $value) {
            $value->accesos;
            array_push($array_accesos_botonera,$value->accesos->accesos_grupo);
        }
        $modulo='almacen';
        return view('almacen/variables/tipo_movimiento',compact('array_accesos','array_accesos_botonera','modulo'));
    }
    function view_unid_med()
    {
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
        $array_accesos_botonera=array();
        $accesos_botonera = AccesosUsuarios::where('accesos_usuarios.estado','=',1)
        ->select('accesos.*')
        ->join('configuracion.accesos','accesos.id_acceso','=','accesos_usuarios.id_acceso')
        ->where('accesos_usuarios.id_usuario',Auth::user()->id_usuario)
        ->where('accesos_usuarios.id_modulo',65)
        ->where('accesos_usuarios.id_padre',4)
        ->get();
        foreach ($accesos_botonera as $key => $value) {
            $value->accesos;
            array_push($array_accesos_botonera,$value->accesos->accesos_grupo);
        }
        $modulo='almacen';
        return view('almacen/variables/unid_med',compact('array_accesos','array_accesos_botonera','modulo'));
    }

    function view_guia_compra()
    {
        $proveedores = $this->mostrar_proveedores_cbo();
        $almacenes = AlmacenController::mostrar_almacenes_cbo();
        $posiciones = $this->mostrar_posiciones_cbo();
        // $motivos = $this->mostrar_motivos_cbo();
        $clasificaciones = $this->mostrar_guia_clas_cbo();
        $tp_doc = $this->mostrar_tp_doc_cbo();
        $monedas = AlmacenController::mostrar_moneda_cbo();
        $tp_doc_almacen = $this->tp_doc_almacen_cbo_ing();
        $tp_operacion = $this->tp_operacion_cbo_ing();
        $tp_contribuyente = $this->tp_contribuyente_cbo();
        $sis_identidad = AlmacenController::sis_identidad_cbo();
        $usuarios = $this->select_usuarios();
        $motivos_anu = AlmacenController::select_motivo_anu();
        $sedes = AlmacenController::mostrar_sedes_cbo();
        $condiciones = AlmacenController::mostrar_condiciones_cbo();
        return view('almacen/guias/guia_compra', compact(
            'proveedores',
            'almacenes',
            'posiciones',
            'clasificaciones',
            'tp_doc',
            'monedas',
            'tp_doc_almacen',
            'tp_operacion',
            'tp_contribuyente',
            'sis_identidad',
            'tp_prorrateo',
            'usuarios',
            'motivos_anu',
            'sedes',
            'condiciones'
        ));
    }
    function view_guia_venta()
    {
        $almacenes = AlmacenController::mostrar_almacenes_cbo();
        $posiciones = $this->mostrar_posiciones_cbo();
        // $motivos = $this->mostrar_motivos_cbo();
        $clasificaciones = $this->mostrar_guia_clas_cbo();
        // $empresas = AlmacenController::select_empresa();
        $sedes = AlmacenController::mostrar_sedes_cbo();
        $proveedores = $this->mostrar_proveedores_cbo();
        $tp_doc_almacen = $this->tp_doc_almacen_cbo_sal();
        $tp_operacion = AlmacenController::tp_operacion_cbo_sal();
        $tp_contribuyente = $this->tp_contribuyente_cbo();
        $sis_identidad = AlmacenController::sis_identidad_cbo();
        // $usuarios = $this->select_usuarios_almacen();
        $usuarios = $this->select_usuarios();
        $motivos_anu = AlmacenController::select_motivo_anu();
        return view('almacen/guias/guia_venta', compact('almacenes', 'posiciones', 'clasificaciones', 'sedes', 'proveedores', 'tp_doc_almacen', 'tp_operacion', 'tp_contribuyente', 'sis_identidad', 'usuarios', 'motivos_anu'));
    }
    function view_doc_venta()
    {
        // $empresas = AlmacenController::select_empresa();
        $sedes = AlmacenController::mostrar_sedes_cbo();
        $clasificaciones = $this->mostrar_guia_clas_cbo();
        $condiciones = $this->mostrar_condiciones_cbo();
        $tp_doc = $this->mostrar_tp_doc_cbo();
        $moneda = AlmacenController::mostrar_moneda_cbo();
        $usuarios = $this->select_usuarios();
        return view('almacen/documentos/doc_venta', compact('sedes', 'clasificaciones', 'condiciones', 'tp_doc', 'moneda', 'usuarios'));
    }
    function view_kardex_general()
    {
        $empresas = AlmacenController::select_empresa();
        $almacenes = AlmacenController::mostrar_almacenes_cbo();
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
        return view('almacen/reportes/kardex_general', compact('almacenes', 'empresas','array_accesos'));
    }
    function view_kardex_detallado()
    {
        $empresas = AlmacenController::select_empresa();
        $almacenes = AlmacenController::mostrar_almacenes_cbo();
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
        return view('almacen/reportes/kardex_detallado', compact('almacenes', 'empresas','array_accesos'));
    }
    function view_tipo_doc_almacen()
    {

        $tp_doc = $this->mostrar_tp_doc_cbo();
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
        $array_accesos_botonera=array();
        $accesos_botonera = AccesosUsuarios::where('accesos_usuarios.estado','=',1)
        ->select('accesos.*')
        ->join('configuracion.accesos','accesos.id_acceso','=','accesos_usuarios.id_acceso')
        ->where('accesos_usuarios.id_usuario',Auth::user()->id_usuario)
        ->where('accesos_usuarios.id_modulo',65)
        ->where('accesos_usuarios.id_padre',4)
        ->get();
        foreach ($accesos_botonera as $key => $value) {
            $value->accesos;
            array_push($array_accesos_botonera,$value->accesos->accesos_grupo);
        }
        $modulo='almacen';
        return view('almacen/variables/tipo_doc_almacen', compact('tp_doc','array_accesos','array_accesos_botonera','modulo'));
    }
    function view_ingresos()
    {
        $empresas = Empresa::mostrar();
        $almacenes = AlmacenController::mostrar_almacenes_cbo();
        $tp_doc_almacen = $this->tp_doc_almacen_cbo_ing();
        $tp_operacion = $this->tp_operacion_cbo_ing();
        $usuarios = $this->select_almaceneros();
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
        return view('almacen/reportes/lista_ingresos', compact('almacenes', 'empresas', 'tp_doc_almacen', 'tp_operacion', 'usuarios','array_accesos'));
    }
    function view_salidas()
    {
        $empresas = AlmacenController::select_empresa();
        $almacenes = AlmacenController::mostrar_almacenes_cbo();
        $tp_doc_almacen = $this->tp_doc_almacen_cbo_sal();
        $tp_operacion = AlmacenController::tp_operacion_cbo_sal();
        $usuarios = $this->select_almaceneros();
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
        return view('almacen/reportes/lista_salidas', compact('almacenes', 'empresas', 'tp_doc_almacen', 'tp_operacion', 'usuarios','array_accesos'));
    }
    function view_busqueda_ingresos()
    {
        $empresas = AlmacenController::select_empresa();
        $almacenes = AlmacenController::mostrar_almacenes_cbo();
        $tp_doc_almacen = $this->tp_doc_almacen_cbo_ing();
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
        return view('almacen/reportes/busqueda_ingresos', compact('almacenes', 'empresas', 'tp_doc_almacen','array_accesos'));
    }
    function view_busqueda_salidas()
    {
        $empresas = AlmacenController::select_empresa();
        $almacenes = AlmacenController::mostrar_almacenes_cbo();
        $tp_doc_almacen = $this->tp_doc_almacen_cbo_sal();
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
        return view('almacen/reportes/busqueda_salidas', compact('almacenes', 'empresas', 'tp_doc_almacen','array_accesos'));
    }

    function view_serie_numero()
    {
        $tipos = $this->select_cont_tp_doc();
        $sedes = AlmacenController::mostrar_sedes_cbo();
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
        $array_accesos_botonera=array();
        $accesos_botonera = AccesosUsuarios::where('accesos_usuarios.estado','=',1)
        ->select('accesos.*')
        ->join('configuracion.accesos','accesos.id_acceso','=','accesos_usuarios.id_acceso')
        ->where('accesos_usuarios.id_usuario',Auth::user()->id_usuario)
        ->where('accesos_usuarios.id_modulo',65)
        ->where('accesos_usuarios.id_padre',4)
        ->get();
        foreach ($accesos_botonera as $key => $value) {
            $value->accesos;
            array_push($array_accesos_botonera,$value->accesos->accesos_grupo);
        }
        $modulo='almacen';
        return view('almacen/variables/serie_numero', compact('tipos', 'sedes','array_accesos','array_accesos_botonera','modulo'));
    }
    function view_docs_prorrateo()
    {
        // $tp_doc = $this->mostrar_tp_doc_cbo();
        return view('almacen/reportes/docs_prorrateo');
    }

    /* Combos */
    public function select_cont_tp_doc()
    {
        $data = DB::table('contabilidad.cont_tp_doc')
            ->select('cont_tp_doc.*')
            ->where([['estado', '=', 1], ['abreviatura', '!=', null]])
            ->orderBy('cod_sunat', 'asc')->get();
        return $data;
    }
    public function select_adm_tp_docum()
    {
        $data = DB::table('administracion.adm_tp_docum')
            ->select('adm_tp_docum.*')
            ->where('adm_tp_docum.estado', '=', 1)
            ->orderBy('adm_tp_docum.descripcion', 'asc')->get();
        return $data;
    }
    public static function select_motivo_anu()
    {
        $data = DB::table('almacen.motivo_anu')
            ->select('motivo_anu.id_motivo', 'motivo_anu.descripcion')
            ->where('motivo_anu.estado', '=', 1)
            ->orderBy('motivo_anu.descripcion', 'asc')->get();
        return $data;
    }
    public function tp_contribuyente_cbo()
    {
        $data = DB::table('contabilidad.adm_tp_contri')
            ->select('adm_tp_contri.id_tipo_contribuyente', 'adm_tp_contri.descripcion')
            ->where('adm_tp_contri.estado', '=', 1)
            ->orderBy('adm_tp_contri.descripcion', 'asc')->get();
        return $data;
    }
    public static function sis_identidad_cbo()
    {
        $data = DB::table('contabilidad.sis_identi')
            ->select('sis_identi.id_doc_identidad', 'sis_identi.descripcion')
            ->where('sis_identi.estado', '=', 1)
            ->orderBy('sis_identi.descripcion', 'asc')->get();
        return $data;
    }

    public static function tp_operacion_cbo_ing()
    {
        $data = DB::table('almacen.tp_ope')
            ->select('tp_ope.id_operacion', 'tp_ope.cod_sunat', 'tp_ope.descripcion')
            ->where('tp_ope.estado', 1)
            ->whereIn('tp_ope.tipo', [1, 3])
            ->get();
        return $data;
    }

    public static function tp_operacion_ids($ids)
    {
        $data = DB::table('almacen.tp_ope')
            ->select('tp_ope.id_operacion', 'tp_ope.cod_sunat', 'tp_ope.descripcion')
            ->whereIn('tp_ope.id_operacion', $ids)
            ->get();
        return $data;
    }

    public static function tp_operacion_cbo_sal()
    {
        $data = DB::table('almacen.tp_ope')
            ->select('tp_ope.id_operacion', 'tp_ope.cod_sunat', 'tp_ope.descripcion')
            ->where('tp_ope.estado', 1)
            ->whereIn('tp_ope.tipo', [2, 3])
            ->orderBy('cod_sunat', 'asc')
            ->get();
        return $data;
    }
    public function tp_doc_almacen_cbo_ing()
    {
        $data = DB::table('almacen.tp_doc_almacen')
            ->select('tp_doc_almacen.id_tp_doc_almacen', 'tp_doc_almacen.descripcion')
            ->where([
                ['tp_doc_almacen.estado', '=', 1],
                ['tp_doc_almacen.tipo', '=', 1]
            ])
            ->get();
        return $data;
    }
    public function tp_doc_almacen_cbo_sal()
    {
        $data = DB::table('almacen.tp_doc_almacen')
            ->select('tp_doc_almacen.id_tp_doc_almacen', 'tp_doc_almacen.descripcion')
            ->where([
                ['tp_doc_almacen.estado', '=', 1],
                ['tp_doc_almacen.tipo', '=', 2]
            ])
            ->orderBy('descripcion', 'desc')
            ->get();
        return $data;
    }
    public function mostrar_impuestos_cbo()
    {
        $data = DB::table('contabilidad.cont_impuesto')
            ->select(
                'cont_impuesto.id_impuesto',
                'cont_impuesto.descripcion',
                'cont_impuesto.porcentaje'
            )
            ->where('cont_impuesto.estado', '=', 1)
            ->get();
        return $data;
    }
    public static function select_empresa()
    {
        $data = DB::table('administracion.adm_empresa')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->select('adm_empresa.id_empresa', 'adm_contri.id_contribuyente', 'adm_contri.nro_documento', 'adm_contri.razon_social')
            ->where('adm_empresa.estado', '=', 1)
            ->orderBy('adm_empresa.id_empresa', 'asc')->get();
        return $data;
    }
    public function mostrar_proyecto_cbo()
    {
        $data = DB::table('proyectos.proy_contrato')
            ->select('proy_proyecto.id_proyecto', 'proy_proyecto.descripcion')
            ->join('proyectos.proy_proyecto', 'proy_proyecto.id_proyecto', '=', 'proy_contrato.id_proyecto')
            ->where('proy_contrato.estado', '=', 1)
            ->get();
        return $data;
    }
    public function mostrar_area_cbo()
    {
        $data = DB::table('administracion.adm_area')
            ->select('adm_area.id_area', DB::raw("(adm_grupo.descripcion) || ' ' || (adm_area.descripcion) as area_descripcion"))
            ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_area.id_grupo')
            ->where('adm_area.estado', '=', 1)
            ->get();
        return $data;
    }
    public function mostrar_trabajadores_cbo()
    {
        $data = DB::table('rrhh.rrhh_trab')
            ->select(
                'rrhh_trab.id_trabajador',
                DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) as nombre_trabajador")
            )
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->where('rrhh_trab.estado', '=', 1)
            ->get();
        return $data;
    }
    public static function select_usuarios()
    {
        $data = DB::table('configuracion.sis_usua')
            ->select('sis_usua.id_usuario', 'sis_usua.nombre_corto')
            ->where([['sis_usua.estado', '=', 1], ['sis_usua.nombre_corto', '<>', null]])
            ->orderBy('sis_usua.nombre_corto')
            ->get();
        return $data;
    }
    public function select_usuarios_almacen()
    {
        $data = DB::table('rrhh.rrhh_rol')
            ->select('sis_usua.id_usuario', 'sis_usua.nombre_corto')
            ->where([['rrhh_rol.id_area', '=', 47], ['sis_usua.estado', '=', 1]])
            ->join('configuracion.sis_usua', 'sis_usua.id_trabajador', '=', 'rrhh_rol.id_trabajador')
            ->get();
        return $data;
    }
    public function select_almaceneros()
    {
        $data = DB::table('rrhh.rrhh_rol')
            ->select(
                'sis_usua.id_usuario',
                'rrhh_rol.id_trabajador',
                DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) as nombre_trabajador")
            )
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_rol.id_trabajador')
            ->join('configuracion.sis_usua', 'sis_usua.id_trabajador', '=', 'rrhh_rol.id_trabajador')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->where([['sis_usua.estado', '=', 1], ['rrhh_rol.id_area', '=', 8]]) //Area = Almacen
            ->get();
        return $data;
    }
    public function mostrar_equipo_cbo()
    {
        $data = DB::table('logistica.equipo')
            ->select('equipo.id_equipo', 'equipo.codigo', 'equipo.descripcion')
            ->where('estado', '=', 1)
            ->get();
        return $data;
    }
    public function mostrar_unid_program_cbo()
    {
        $data = DB::table('proyectos.proy_unid_program')
            ->select('proy_unid_program.id_unid_program', 'proy_unid_program.descripcion')
            ->where('estado', '=', 1)
            ->get();
        return $data;
    }
    public function mostrar_tp_combustible_cbo()
    {
        $data = DB::table('logistica.tp_combustible')
            ->select('tp_combustible.id_tp_combustible', 'tp_combustible.descripcion')
            ->where('estado', '=', 1)
            ->orderBy('tp_combustible.codigo', 'asc')->get();
        return $data;
    }
    public function mostrar_tp_seguro_cbo()
    {
        $data = DB::table('logistica.equi_tp_seguro')
            ->select('equi_tp_seguro.id_tp_seguro', 'equi_tp_seguro.descripcion')
            ->where('estado', '=', 1)
            ->get();
        return $data;
    }
    public static function mostrar_tipos_cbo()
    {
        $data = DB::table('almacen.alm_tp_prod')
            ->select('alm_tp_prod.id_tipo_producto', 'alm_tp_prod.descripcion')
            ->where('estado', '=', 1)
            ->orderBy('alm_tp_prod.id_tipo_producto', 'asc')->get();
        return $data;
    }
    public static function mostrar_clasificaciones_cbo()
    {
        $data = DB::table('almacen.alm_clasif')
            ->select('alm_clasif.id_clasificacion', 'alm_clasif.descripcion')
            ->where([['alm_clasif.estado', '=', 1]])
            ->orderBy('descripcion')
            ->get();
        return $data;
    }
    public static function mostrar_subcategorias_cbo()
    {
        $data = DB::table('almacen.alm_subcat')
            ->select('alm_subcat.id_subcategoria', 'alm_subcat.descripcion')
            ->where([['alm_subcat.estado', '=', 1]])
            ->orderBy('descripcion')
            ->get();
        return $data;
    }
    public static function mostrar_categorias_cbo()
    {
        $data = DB::table('almacen.alm_cat_prod')
            ->select('alm_cat_prod.id_categoria', 'alm_cat_prod.descripcion')
            ->where([['alm_cat_prod.estado', '=', 1]])
            ->orderBy('descripcion')
            ->get();
        return $data;
    }
    public static function mostrar_unidades_cbo()
    {
        $data = DB::table('almacen.alm_und_medida')
            ->select(
                'alm_und_medida.id_unidad_medida',
                'alm_und_medida.descripcion',
                'alm_und_medida.abreviatura'
            )
            ->where([['alm_und_medida.estado', '=', 1]])
            ->orderBy('descripcion')
            ->get();
        return $data;
    }
    public function mostrar_unidades()
    {
        $data = DB::table('almacen.alm_und_medida')
            ->select('alm_und_medida.*')
            ->where([['alm_und_medida.estado', '=', 1]])
            ->orderBy('id_unidad_medida')
            ->get();
        return response()->json($data);
    }
    public function mostrar_tp_servicios_cbo()
    {
        $data = DB::table('logistica.log_tp_servi')
            ->select('log_tp_servi.id_tipo_servicio', 'log_tp_servi.descripcion')
            ->where([['log_tp_servi.estado', '=', 1]])
            ->orderBy('id_tipo_servicio')
            ->get();
        return $data;
    }
    public static function mostrar_tp_almacen_cbo()
    {
        $data = DB::table('almacen.alm_tp_almacen')
            ->select('alm_tp_almacen.id_tipo_almacen', 'alm_tp_almacen.descripcion')
            ->where([['alm_tp_almacen.estado', '=', 1]])
            ->orderBy('descripcion')
            ->get();
        return $data;
    }
    public static function mostrar_sedes_cbo()
    {
        $data = DB::table('administracion.sis_sede')
            ->select('sis_sede.*', 'adm_contri.razon_social', 'adm_contri.nro_documento')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->where([['sis_sede.estado', '=', 1]])
            ->orderBy('descripcion')
            ->get();
        return $data;
    }
    public static function mostrar_almacenes_cbo()
    {
        $data = DB::table('almacen.alm_almacen')
            ->select('alm_almacen.id_almacen', 'alm_almacen.codigo', 'alm_almacen.descripcion')
            ->where([['alm_almacen.estado', '=', 1]])
            ->orderBy('codigo')
            ->get();
        return $data;
    }
    public function cargar_almacenes($id_sede)
    {
        $data = DB::table('almacen.alm_almacen')
            ->select(
                'alm_almacen.*',
                'sis_sede.descripcion as sede_descripcion',
                'adm_empresa.id_empresa',
                'alm_tp_almacen.descripcion as tp_almacen'
            )
            ->leftjoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
            ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('almacen.alm_tp_almacen', 'alm_tp_almacen.id_tipo_almacen', '=', 'alm_almacen.id_tipo_almacen')
            ->where([
                ['alm_almacen.estado', '=', 1],
                ['alm_almacen.id_sede', '=', $id_sede]
            ])
            ->orderBy('codigo')
            ->get();
        return $data;
    }

    public function cargar_almacenes_contrib($id_contribuyente)
    {
        $data = DB::table('almacen.alm_almacen')
            ->select('alm_almacen.id_almacen', 'alm_almacen.codigo', 'alm_almacen.descripcion')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->where([
                ['alm_almacen.estado', '=', 1],
                ['adm_contri.id_contribuyente', '=', $id_contribuyente]
            ])
            ->orderBy('codigo')
            ->get();
        return $data;
    }
    public function select_almacenes_empresa($id_empresa)
    {
        $data = DB::table('almacen.alm_almacen')
            ->select('alm_almacen.id_almacen', 'alm_almacen.codigo', 'alm_almacen.descripcion')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->where([
                ['alm_almacen.estado', '=', 1],
                ['adm_empresa.id_empresa', '=', $id_empresa]
            ])
            ->orderBy('alm_almacen.codigo')
            ->get();
        return $data;
    }

    public function mostrar_posiciones_cbo()
    {
        $data = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_ubi_posicion.id_posicion', 'alm_ubi_posicion.codigo')
            ->where([['alm_ubi_posicion.estado', '=', 1]])
            ->orderBy('codigo')
            ->get();
        return $data;
    }
    public function select_posiciones_almacen($id_almacen)
    {
        $data = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_ubi_posicion.id_posicion', 'alm_ubi_posicion.codigo')
            ->join('almacen.alm_ubi_nivel', 'alm_ubi_nivel.id_nivel', '=', 'alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante', 'alm_ubi_estante.id_estante', '=', 'alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_posicion.estado', '=', 1]])
            ->orderBy('codigo')
            ->get();
        return $data;
    }
    public function mostrar_ubicaciones_cbo()
    {
        $data = DB::table('almacen.alm_prod_ubi')
            ->select(
                'alm_prod_ubi.id_prod_ubi',
                'alm_almacen.descripcion as alm_descripcion',
                'alm_ubi_posicion.codigo as cod_posicion'
            )
            ->join('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'alm_prod_ubi.id_posicion')
            ->join('almacen.alm_ubi_nivel', 'alm_ubi_nivel.id_nivel', '=', 'alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante', 'alm_ubi_estante.id_estante', '=', 'alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_ubi_estante.id_almacen')
            ->where([['alm_prod_ubi.estado', '=', 1]])
            ->orderBy('cod_posicion')
            ->get();
        return $data;
    }
    public function mostrar_detracciones_cbo()
    {
        $data = DB::table('contabilidad.cont_detra_det')
            ->select('cont_detra_det.id_detra_det', 'cont_detra.cod_sunat', 'cont_detra_det.porcentaje', 'cont_detra.descripcion')
            ->join('contabilidad.cont_detra', 'cont_detra.id_cont_detra', '=', 'cont_detra_det.id_detra')
            ->where([['cont_detra_det.estado', '=', 1]])
            ->orderBy('cont_detra.descripcion')
            ->get();
        return $data;
    }
    public function mostrar_proveedores_cbo()
    {
        $data = DB::table('logistica.log_prove')
            ->select('log_prove.id_proveedor', 'adm_contri.nro_documento', 'adm_contri.razon_social')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->where([['log_prove.estado', '=', 1]])
            ->orderBy('adm_contri.nro_documento')
            ->get();
        return $data;
    }
    // public function mostrar_motivos_cbo()
    // {
    //     $data = DB::table('almacen.guia_motivo')
    //         ->select('guia_motivo.id_motivo','guia_motivo.descripcion')
    //         ->where([['guia_motivo.estado', '=', 1]])
    //         ->orderBy('guia_motivo.id_motivo')
    //         ->get();
    //     return $data;
    // }
    public static function mostrar_guia_clas_cbo()
    {
        $data = DB::table('almacen.guia_clas')
            ->select('guia_clas.id_clasificacion', 'guia_clas.descripcion')
            ->where([['guia_clas.estado', '=', 1]])
            ->orderBy('guia_clas.id_clasificacion')
            ->get();
        return $data;
    }
    public static function mostrar_condiciones_cbo()
    {
        $data = DB::table('logistica.log_cdn_pago')
            ->select('log_cdn_pago.id_condicion_pago', 'log_cdn_pago.descripcion')
            ->where('log_cdn_pago.estado', 1)
            ->orderBy('log_cdn_pago.descripcion')
            ->get();
        return $data;
    }
    public static function mostrar_tp_doc_cbo()
    {
        $data = DB::table('contabilidad.cont_tp_doc')
            ->select('cont_tp_doc.id_tp_doc', 'cont_tp_doc.cod_sunat', 'cont_tp_doc.descripcion')
            ->where([['cont_tp_doc.estado', '=', 1]])
            ->orderBy('cont_tp_doc.cod_sunat', 'asc')
            ->get();
        return $data;
    }
    public static function mostrar_moneda_cbo()
    {
        $data = DB::table('configuracion.sis_moneda')
            ->select('sis_moneda.id_moneda', 'sis_moneda.simbolo', 'sis_moneda.descripcion')
            ->where([['sis_moneda.estado', '=', 1]])
            ->orderBy('sis_moneda.id_moneda')
            ->get();
        return $data;
    }
    public function mostrar_equi_tipos_cbo()
    {
        $data = DB::table('logistica.equi_tipo')
            ->select('equi_tipo.id_tipo', 'equi_tipo.codigo', 'equi_tipo.descripcion')
            ->where([['estado', '=', 1]])
            ->get();
        return $data;
    }
    public function mostrar_equi_cats_cbo()
    {
        $data = DB::table('logistica.equi_cat')
            ->select('equi_cat.id_categoria', 'equi_cat.codigo', 'equi_cat.descripcion')
            ->where([['estado', '=', 1]])
            ->get();
        return $data;
    }
    public function mostrar_propietarios_cbo()
    {
        $data = DB::table('administracion.adm_empresa')
            ->select('adm_empresa.id_empresa', 'adm_contri.nro_documento', 'adm_contri.razon_social')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->where([['adm_empresa.estado', '=', 1]])
            ->get();
        return $data;
    }

    ///////////////////////////////////
    // public function mostrar_impuesto($cod, $fecha){
    //     $data = DB::table('contabilidad.cont_impuesto')
    //     ->select('cont_impuesto.*')
    //         ->where([['codigo','=',$cod],['fecha_inicio','<',$fecha]])
    //         ->orderBy('fecha_inicio','desc')
    //         ->first();
    //     return $data;
    // }

    public function mostrar_clientes()
    {
        $data = DB::table('comercial.com_cliente')
            ->select(
                'com_cliente.id_cliente',
                'com_cliente.id_contribuyente',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'adm_contri.telefono',
                'adm_contri.direccion_fiscal',
                'adm_contri.email'
            )
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->where([['com_cliente.estado', '=', 1]])
            ->orderBy('adm_contri.nro_documento')
            ->get();
        $output['data'] = $data;
        return $output;
    }
    public function mostrar_clientes_empresa()
    {
        $data = DB::table('comercial.com_cliente')
            ->select(
                'com_cliente.id_cliente',
                'com_cliente.id_contribuyente',
                'adm_contri.nro_documento',
                'adm_contri.razon_social'
            )
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->join('administracion.adm_empresa', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->where([['com_cliente.estado', '=', 1]])
            ->orderBy('adm_contri.nro_documento')
            ->get();
        $output['data'] = $data;
        return $output;
    }

    //Productos






    /* Tipo de Movimiento */
    public function mostrar_tipos_mov()
    {
        $data = DB::table('almacen.tp_ope')
            ->where([['tp_ope.estado', '=', 1]])
            ->orderBy('id_operacion')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_tipo_mov($id)
    {
        $data = DB::table('almacen.tp_ope')
            ->where([['tp_ope.id_operacion', '=', $id]])
            ->get();
        return response()->json($data);
    }

    public function guardar_tipo_mov(Request $request)
    {
        $id_operacion = DB::table('almacen.tp_ope')->insertGetId(
            [
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion,
                'cod_sunat' => $request->cod_sunat,
                'estado' => $request->estado,
            ],
            'id_operacion'
        );
        return response()->json($id_operacion);
    }

    public function update_tipo_mov(Request $request)
    {
        $data = DB::table('almacen.tp_ope')
            ->where('id_operacion', $request->id_operacion)
            ->update([
                'tipo' => $request->tipo,
                'cod_sunat' => $request->cod_sunat,
                'descripcion' => $request->descripcion
            ]);
        return response()->json($data);
    }

    public function anular_tipo_mov(Request $request, $id)
    {
        $data = DB::table('almacen.tp_ope')->where('id_operacion', $id)
            ->update(['estado' => 7]);
        return response()->json($data);
    }

    /* Unidades de Medida */
    public function mostrar_unidades_med()
    {
        $data = DB::table('almacen.alm_und_medida')
            ->where([['alm_und_medida.estado', '=', 1]])
            ->orderBy('id_unidad_medida')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_unid_med($id)
    {
        $data = DB::table('almacen.alm_und_medida')
            ->where([['alm_und_medida.id_unidad_medida', '=', $id]])
            ->get();
        return response()->json($data);
    }

    public function guardar_unid_med(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_unidad_medida = DB::table('almacen.alm_und_medida')->insertGetId(
            [
                'descripcion' => $request->descripcion,
                'abreviatura' => strtoupper($request->abreviatura),
                'estado' => 1,
                // 'fecha_registro' => $fecha,
            ],
            'id_unidad_medida'
        );
        return response()->json($id_unidad_medida);
    }

    public function update_unid_med(Request $request)
    {
        $data = DB::table('almacen.alm_und_medida')
            ->where('id_unidad_medida', $request->id_unidad_medida)
            ->update([
                'abreviatura' => $request->abreviatura,
                'descripcion' => $request->descripcion,
                'estado' => $request->estado,
            ]);
        return response()->json($data);
    }

    public function anular_unid_med(Request $request, $id)
    {
        $data = DB::table('almacen.alm_und_medida')->where('id_unidad_medida', $id)
            ->update(['estado' => 7]);
        return response()->json($data);
    }


    /**Guia Compra */
    public function listar_guias_compra()
    {
        $data = DB::table('almacen.guia_com')
            ->select(
                'guia_com.*',
                'adm_contri.razon_social',
                'tp_ope.descripcion as operacion',
                'alm_almacen.descripcion as almacen_descripcion',
                'mov_alm.codigo'
            )
            ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'guia_com.id_proveedor')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftjoin('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'guia_com.id_operacion')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'guia_com.id_almacen')
            ->leftjoin('almacen.mov_alm', 'mov_alm.id_guia_com', '=', 'guia_com.id_guia')
            ->where([['guia_com.estado', '!=', 7]])
            ->orderBy('fecha_emision', 'desc')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function listar_guias_proveedor($id_proveedor)
    {
        $data = DB::table('almacen.guia_com')
            ->select('guia_com.*', 'adm_contri.razon_social', 'adm_estado_doc.estado_doc as des_estado')
            ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'guia_com.id_proveedor')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftjoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'guia_com.estado')
            ->where([['guia_com.id_proveedor', '=', $id_proveedor], ['guia_com.estado', '!=', 7]])
            ->orderBy('fecha_emision', 'desc')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_guia_compra($id)
    {
        $guia = DB::table('almacen.guia_com')
            ->select(
                'guia_com.*',
                'adm_estado_doc.estado_doc AS des_estado',
                'sis_usua.nombre_corto',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'doc_com.serie as doc_serie',
                'doc_com.numero as doc_numero',
                'cont_tp_doc.abreviatura as tp_doc',
                'doc_com.id_doc_com',
                'tp_doc_almacen.abreviatura as tp_doc_abreviatura'
            )
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'guia_com.estado')
            ->leftjoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'guia_com.registrado_por')
            ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'guia_com.id_proveedor')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftjoin('almacen.doc_com_guia', 'doc_com_guia.id_guia_com', '=', 'guia_com.id_guia')
            ->leftjoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'doc_com_guia.id_doc_com')
            ->leftjoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_com.id_tp_doc')
            ->leftjoin('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_com.id_tp_doc_almacen')
            ->where([['guia_com.id_guia', '=', $id]])
            ->get();
        return response()->json($guia);
    }
    public function guardar_guia_compra(Request $request)
    {
        $usuario = Auth::user();
        $fecha = date('Y-m-d H:i:s');
        $id_guia = DB::table('almacen.guia_com')->insertGetId(
            [
                'id_tp_doc_almacen' => $request->id_tp_doc_almacen,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_proveedor' => $request->id_proveedor,
                'fecha_emision' => $request->fecha_emision,
                'fecha_almacen' => $request->fecha_almacen,
                'id_almacen' => $request->id_almacen,
                'id_motivo' => $request->id_motivo,
                'id_guia_clas' => $request->id_guia_clas,
                'id_operacion' => $request->id_operacion,
                'punto_partida' => $request->punto_partida,
                'punto_llegada' => $request->punto_llegada,
                'transportista' => $request->transportista,
                'fecha_traslado' => $request->fecha_traslado,
                'tra_serie' => $request->tra_serie,
                'tra_numero' => $request->tra_numero,
                'placa' => $request->placa,
                'usuario' => $request->usuario,
                'registrado_por' => $usuario->id_usuario,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
            'id_guia'
        );
        // $output['data'] = 'id_guia'
        return response()->json(["id_guia" => $id_guia, "id_proveedor" => $request->id_proveedor]);
    }

    public function update_guia_compra(Request $request)
    {
        $data = DB::table('almacen.guia_com')
            ->where('id_guia', $request->id_guia)
            ->update([
                'id_tp_doc_almacen' => $request->id_tp_doc_almacen,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_proveedor' => $request->id_proveedor,
                'fecha_emision' => $request->fecha_emision,
                'fecha_almacen' => $request->fecha_almacen,
                'id_almacen' => $request->id_almacen,
                'id_operacion' => $request->id_operacion,
                'id_guia_clas' => $request->id_guia_clas,
                'id_motivo' => $request->id_motivo,
                'punto_partida' => $request->punto_partida,
                'punto_llegada' => $request->punto_llegada,
                'transportista' => $request->transportista,
                'fecha_traslado' => $request->fecha_traslado,
                'tra_serie' => $request->tra_serie,
                'tra_numero' => $request->tra_numero,
                'placa' => $request->placa,
                'usuario' => $request->usuario
            ]);
        // return response()->json($data);
        return response()->json(["id_guia" => $request->id_guia, "id_proveedor" => $request->id_proveedor]);
    }

    public function anular_guia_compra(Request $request)
    {
        $rspta = '';
        $ing = DB::table('almacen.mov_alm')
            ->where([['id_guia_com', '=', $request->id_guia_com], ['estado', '=', 1]])
            ->first();

        if (isset($ing)) {
            //si el ingreso no esta revisado
            if ($ing->revisado == 0) {
                //Anula ingreso
                DB::table('almacen.mov_alm')
                    ->where('id_mov_alm', $ing->id_mov_alm)
                    ->update(['estado' => 7]);
                //Anula ingreso detalle
                $detalle = DB::table('almacen.guia_com_det')
                    ->where('id_guia_com', $request->id_guia_com)->get();

                foreach ($detalle as $det) {
                    DB::table('almacen.mov_alm_det')
                        ->where('id_guia_com_det', $det->id_guia_com_det)
                        ->update(['estado' => 7]);
                }

                //motivo de la anulacion
                $mot = DB::table('almacen.motivo_anu')
                    ->where('id_motivo', $request->id_motivo_obs)
                    ->first();

                $id_usuario = Auth::user()->id_usuario;
                $obs = $mot->descripcion . '. ' . $request->observacion;
                //Agrega observacion a la guia
                $id_obs = DB::table('almacen.guia_com_obs')->insertGetId(
                    [
                        'id_guia_com' => $request->id_guia_com,
                        'observacion' => $obs,
                        'registrado_por' => $id_usuario,
                        'id_motivo_anu' => $request->id_motivo_obs,
                        'fecha_registro' => date('Y-m-d H:i:s')
                    ],
                    'id_obs'
                );
                //Anula la Guia
                $data = DB::table('almacen.guia_com')
                    ->where('id_guia', $request->id_guia_com)
                    ->update(['estado' => 7]);
                //Anula la Guia Detalle
                $detalle = DB::table('almacen.guia_com_det')
                    ->where('id_guia_com', $request->id_guia_com)
                    ->update(['estado' => 7]);
                //Anula la Guia OC
                $ordenes = DB::table('almacen.guia_com_oc')
                    ->where([
                        ['id_guia_com', '=', $request->id_guia_com],
                        ['estado', '!=', 7]
                    ])
                    ->get();

                foreach ($ordenes as $oc) {
                    DB::table('logistica.log_ord_compra')
                        ->where('id_orden_compra', $oc->id_oc)
                        ->update(['en_almacen' => false]);
                }

                $ocs = DB::table('almacen.guia_com_oc')
                    ->where('id_guia_com', $request->id_guia_com)
                    ->update(['estado' => 7]);
                //Anula la Guia Doc
                $ocs = DB::table('almacen.doc_com_guia')
                    ->where('id_guia_com', $request->id_guia_com)
                    ->update(['estado' => 7]);
                //Anula detalle
                $detalle = DB::table('almacen.guia_com_det')
                    ->where('id_guia_com', $request->id_guia_com)->get();

                foreach ($detalle as $det) {
                    //cambiar estado OC detalle
                    if ($det->id_oc_det !== null) {
                        DB::table('logistica.log_det_ord_compra')
                            ->where('id_detalle_orden', $det->id_oc_det)
                            ->update(['estado' => 1]); //Elaborado

                        //cambiar estado OC en_almacen = false
                        DB::table('logistica.log_det_ord_compra')
                            ->where('id_detalle_orden', $det->id_oc_det)
                            ->update(['estado' => 1]);
                    }
                }

                $rspta = 'Se anuló la Guía y el Ingreso generado';
                $trans = DB::table('almacen.trans')
                    ->where('id_guia_com', $request->id_guia_com)
                    ->first();

                if (isset($trans)) {
                    DB::table('almacen.trans')
                        ->update(['id_guia_com' => null, 'estado' => 1]);
                }
            }
            //si el ingreso está revisado u observado
            else {
                $des = ($ing->revisado == 1 ? 'Revisado' : 'Observado');
                $rspta = 'No es posible anular!. El ingreso fue ' . $des . ' por el Jefe de Almacén';
            }
        } else {
            //motivo de la anulacion
            $mot = DB::table('almacen.motivo_anu')
                ->where('id_motivo', $request->id_motivo_obs)
                ->first();

            $id_usuario = Auth::user()->id_usuario;
            $obs = $mot->descripcion . '. ' . $request->observacion;
            //Agrega observacion a la guia
            $id_obs = DB::table('almacen.guia_com_obs')->insertGetId(
                [
                    'id_guia_com' => $request->id_guia_com,
                    'observacion' => $obs,
                    'registrado_por' => $id_usuario,
                    'fecha_registro' => date('Y-m-d H:i:s')
                ],
                'id_obs'
            );
            //Anula la Guia
            $data = DB::table('almacen.guia_com')
                ->where('id_guia', $request->id_guia_com)
                ->update(['estado' => 7]);
            //Anula la Guia Detalle
            $detalle = DB::table('almacen.guia_com_det')
                ->where('id_guia_com', $request->id_guia_com)
                ->update(['estado' => 7]);
            //Anula la Guia OC
            $ordenes = DB::table('almacen.guia_com_oc')
                ->where([
                    ['id_guia_com', '=', $request->id_guia_com],
                    ['estado', '!=', 7]
                ])
                ->get();

            foreach ($ordenes as $oc) {
                DB::table('logistica.log_ord_compra')
                    ->where('id_orden_compra', $oc->id_oc)
                    ->update(['en_almacen' => false]);
            }

            $ocs = DB::table('almacen.guia_com_oc')
                ->where('id_guia_com', $request->id_guia_com)
                ->update(['estado' => 7]);
            //Anula la Guia Doc
            $ocs = DB::table('almacen.doc_com_guia')
                ->where('id_guia_com', $request->id_guia_com)
                ->update(['estado' => 7]);
            //Anula detalle
            $detalle = DB::table('almacen.guia_com_det')
                ->where('id_guia_com', $request->id_guia_com)->get();

            foreach ($detalle as $det) {
                //cambiar estado OC detalle
                if ($det->id_oc_det !== null) {
                    //cambiar estado OC en_almacen = false
                    DB::table('logistica.log_det_ord_compra')
                        ->where('id_detalle_orden', $det->id_oc_det)
                        ->update(['estado' => 1]);
                }
            }
            $rspta = 'La Guía fue anulada correctamente';
        }
        return response()->json($rspta);
    }
    public static function nextMovimiento($tipo, $fecha, $id_alm)
    {
        // $mes = date('m',strtotime($fecha));
        $yyyy = date('Y', strtotime($fecha));
        $anio = date('y', strtotime($fecha));
        $tp = '';
        switch ($tipo) {
            case 0:
                $tp = 'INI';
                break;
            case 1:
                $tp = 'ING';
                break;
            case 2:
                $tp = 'SAL';
                break;
            default:
                break;
        }

        $data = DB::table('almacen.mov_alm')
            ->where([
                ['id_tp_mov', '=', $tipo],
                ['id_almacen', '=', $id_alm]
            ])
            ->whereYear('fecha_emision', '=', $yyyy)
            // ->whereMonth('fecha_emision','=',$mes)
            ->count();

        // $alm = DB::table('almacen.alm_almacen')
        // ->where('id_almacen',$id_alm)->first();

        $correlativo = AlmacenController::leftZero(3, $data + 1);

        $codigo = $tp . '-' . $id_alm . '-' . $anio . '-' . $correlativo;

        return $codigo;
    }
    /**Generar Ingreso */
    public function generar_ingreso($id_guia)
    {

        $fecha = date('Y-m-d H:i:s');
        $fecha_emision = date('Y-m-d');
        $id_usuario = Auth::user()->id_usuario;
        $id_ingreso = 0;

        //verifica si existe un ingreso ya generado
        $ingreso = DB::table('almacen.mov_alm')
            ->where([['id_guia_com', '=', $id_guia], ['estado', '=', 1]])
            ->first();

        if (!isset($ingreso)) {
            //obtiene la guia
            $guia = DB::table('almacen.guia_com')->where('id_guia', $id_guia)->first();
            //obtiene el detalle
            $detalle = DB::table('almacen.guia_com_det')
                ->select('guia_com_det.*', 'log_valorizacion_cotizacion.precio_sin_igv') //cambiar a precio_sin_igv
                ->leftjoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det')
                ->leftjoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', 'log_det_ord_compra.id_valorizacion_cotizacion')
                ->where([
                    ['guia_com_det.id_guia_com', '=', $id_guia],
                    ['guia_com_det.estado', '=', 1]
                ])->get()->toArray();

            $codigo = AlmacenController::nextMovimiento(
                1,
                $guia->fecha_almacen,
                $guia->id_almacen
            );

            $doc = DB::table('almacen.doc_com_guia')
                ->where('id_guia_com', $id_guia)
                ->first();

            $id_ingreso = DB::table('almacen.mov_alm')->insertGetId(
                [
                    'id_almacen' => $guia->id_almacen,
                    'id_tp_mov' => 1, //Ingresos
                    'codigo' => $codigo,
                    'fecha_emision' => $guia->fecha_almacen,
                    'id_guia_com' => $guia->id_guia,
                    'id_doc_com' => (isset($doc) ? $doc->id_doc_com : null),
                    'id_operacion' => $guia->id_operacion,
                    'revisado' => 0,
                    'usuario' => $id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                'id_mov_alm'
            );
            // $nuevo_detalle = [];
            $cant = 0;

            // foreach ($detalle as $det){
            //     $exist = false;
            //     foreach ($nuevo_detalle as $nue => $value){
            //         if ($det->id_producto == $value['id_producto']){
            //             $nuevo_detalle[$nue]['cantidad'] = floatval($value['cantidad']) + floatval($det->cantidad);
            //             $nuevo_detalle[$nue]['valorizacion'] = floatval($value['valorizacion']) + floatval($det->total);
            //             $exist = true;
            //         }
            //     }
            //     if ($exist === false){
            //         $nuevo = [
            //             'id_producto' => $det->id_producto,
            //             'id_posicion' => $det->id_posicion,
            //             'id_oc_det' => (isset($det->id_oc_det)) ? $det->id_oc_det : 0,
            //             'cantidad' => floatval($det->cantidad),
            //             'valorizacion' => floatval($det->total)
            //             ];
            //         array_push($nuevo_detalle, $nuevo);
            //     }
            // }

            foreach ($detalle as $det) {
                $prec = ($det->precio_sin_igv !== null ? $det->precio_sin_igv : $det->unitario);
                $id_det = DB::table('almacen.mov_alm_det')->insertGetId(
                    [
                        'id_mov_alm' => $id_ingreso,
                        'id_producto' => $det->id_producto,
                        'id_posicion' => $det->id_posicion,
                        'cantidad' => $det->cantidad,
                        'valorizacion' => (floatval($det->cantidad) * floatval($prec)),
                        'usuario' => $id_usuario,
                        'id_guia_com_det' => $det->id_guia_com_det,
                        'estado' => 1,
                        'fecha_registro' => $fecha,
                    ],
                    'id_mov_alm_det'
                );

                if ($det->id_posicion !== null) {

                    $ubi = DB::table('almacen.alm_prod_ubi')
                        ->where([
                            ['id_producto', '=', $det->id_producto],
                            ['id_posicion', '=', $det->id_posicion]
                        ])
                        ->first();
                    //traer stockActual
                    $saldo = $this->saldo_actual($det->id_producto, $det->id_posicion);
                    $costo = $this->costo_promedio($det->id_producto, $det->id_posicion);

                    if (!isset($ubi->id_posicion)) { //si no existe -> creo la ubicacion
                        DB::table('almacen.alm_prod_ubi')->insert([
                            'id_producto' => $det->id_producto,
                            'id_posicion' => $det->id_posicion,
                            'stock' => $saldo,
                            'costo_promedio' => $costo,
                            'estado' => 1,
                            'fecha_registro' => $fecha
                        ]);
                    } else {
                        DB::table('almacen.alm_prod_ubi')
                            ->where('id_prod_ubi', $ubi->id_prod_ubi)
                            ->update([
                                'stock' => $saldo,
                                'costo_promedio' => $costo
                            ]);
                    }
                }
                if ($det->id_oc_det !== null && $det->id_oc_det > 0) {
                    //cambiar estado orden
                    DB::table('logistica.log_det_ord_compra')
                        ->where('id_detalle_orden', $det->id_oc_det)
                        ->update(['estado' => 6]); //En Almacen

                    // //cambiar estado requerimiento
                    // DB::table('almacen.alm_det_req')
                    // ->join('logistica.log_det_ord_compra','log_det_ord_compra.id_valorizacion_cotizacion','=','log_valorizacion_cotizacion.id_valorizacion_cotizacion')
                    // ->join('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_detalle_requerimiento','=','alm_det_req.id_detalle_requerimiento')
                    // ->where('log_det_ord_compra.id_detalle_orden',$det->id_oc_det)
                    // ->update(['estado'=>6]);//En Almacen
                }
            }

            $ocs = DB::table('almacen.guia_com_oc')
                ->where([['id_guia_com', '=', $id_guia], ['estado', '=', 1]])
                ->get();

            foreach ($ocs as $oc) {
                $ingresadas = DB::table('logistica.log_det_ord_compra')
                    ->where([
                        ['id_orden_compra', '=', $oc->id_oc],
                        ['estado', '=', 6]
                    ])
                    ->count();

                $todas = DB::table('logistica.log_det_ord_compra')
                    ->where([
                        ['id_orden_compra', '=', $oc->id_oc],
                        ['estado', '!=', 7]
                    ])
                    ->count();

                if ($todas == $ingresadas) {
                    DB::table('logistica.log_ord_compra')
                        ->where('id_orden_compra', $oc->id_oc)
                        ->update(['en_almacen' => true]);
                }
            }
            //cambiar estado guiacom
            DB::table('almacen.guia_com')
                ->where('id_guia', $id_guia)->update(['estado' => 9]); //Procesado
        }

        return response()->json($id_ingreso);
    }
    public function req_almacen($id_oc_det)
    {
        $data = DB::table('almacen.alm_det_req')
            ->join('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')
            ->join('logistica.log_det_ord_compra', 'log_det_ord_compra.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
            ->where('log_det_ord_compra.id_detalle_orden', '=', $id_oc_det)
            // ->update(['estado'=>6]);//En Almacen
            ->get();
        // ->update(['alm_det_req.estado' => 6]);//En almacen
        return $data;
    }
    public function id_item($id_producto)
    {
        $item = DB::table('almacen.alm_item')
            ->where('alm_item.id_producto', $id_producto)
            ->first();
        return $item->id_item;
    }
    public function id_ingreso($id_guia)
    {
        $ing = DB::table('almacen.mov_alm')
            ->where('mov_alm.id_guia_com', $id_guia)
            ->first();
        return response()->json((isset($ing) ? $ing->id_mov_alm : 0));
    }

    public function imprimir($id_ing)
    {
        $result = $this->get_ingreso($id_ing);
        $ingreso = $result['ingreso'];
        // $detalle = $result->detalle;
        // $ocs = $result->ocs;
        return $ingreso->codigo;
    }

    public function mostrar_ingreso($id)
    {
        $ingreso = DB::table('almacen.mov_alm')
            ->select(
                'mov_alm.*',
                'alm_almacen.descripcion as des_almacen',
                DB::raw("('GR') || ' ' || (guia_com.serie) || ' ' || (guia_com.numero) as guia"),
                'guia_com.fecha_emision as fecha_guia',
                'sis_usua.usuario as nom_usuario'
            )
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
            ->join('almacen.tp_mov', 'tp_mov.id_tp_mov', '=', 'mov_alm.id_tp_mov')
            ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'mov_alm.id_guia_com')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'mov_alm.usuario')
            ->where('mov_alm.id_mov_alm', $id)
            ->first();

        $detalle = DB::table('almacen.mov_alm_det')
            ->select(
                'mov_alm_det.*',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_ubi_posicion.codigo as cod_posicion'
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
            ->join('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'mov_alm_det.id_posicion')
            ->where('mov_alm_det.estado', 1)
            ->get();

        return response()->json(['ingreso' => $ingreso, 'detalle' => $detalle]);
    }
    /**Guia Compra Transportista */

    // public function mostrar_transportistas($id)
    // {
    //     $data = DB::table('almacen.guia_com_tra')
    //         ->select('guia_com_tra.*', 'adm_contri.razon_social')
    //         ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'guia_com_tra.id_proveedor')
    //         ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
    //         ->where([['guia_com_tra.id_guia', '=', $id]])
    //         ->get();
    //     $output['data'] = $data;
    //     return response()->json($output);
    // }
    public function mostrar_transportista($id)
    {
        $data = DB::table('almacen.guia_com_tra')
            ->where([['guia_com_tra.id_guia_com_tra', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_transportista(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_guia = DB::table('almacen.guia_com_tra')->insertGetId(
            [
                'id_guia' => $request->id_guia,
                'serie' => $request->serie_tra,
                'numero' => $request->numero_tra,
                'id_proveedor' => $request->id_proveedor_tra,
                'fecha_emision' => $request->fecha_emision_tra,
                'referencia' => $request->referencia,
                'placa' => $request->placa,
                'usuario' => 3,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
            'id_guia_com_tra'
        );
        return response()->json($id_guia);
    }

    public function update_transportista(Request $request)
    {
        $data = DB::table('almacen.guia_com_tra')
            ->where('id_guia_com_tra', $request->id_guia_com_tra)
            ->update([
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_proveedor' => $request->id_proveedor,
                'fecha_emision' => $request->fecha_emision,
                'referencia' => $request->referencia,
                'placa' => $request->placa,
                // 'usuario' => 3,
            ]);
        return response()->json($data);
    }

    public function anular_transportista(Request $request, $id)
    {
        $data = DB::table('almacen.guia_com_tra')->where('id_guia_com_tra', $id)
            ->update(['estado' => 7]);
        return response()->json($data);
    }
    public function verifica_posiciones($id_guia)
    {
        $detalle = DB::table('almacen.guia_com_det')
            ->where('id_guia_com', $id_guia)->get();
        $pos = false;
        foreach ($detalle as $d) {
            if ($d->id_posicion == null) {
                $pos = true;
            }
        }
        return ($pos) ? 'Debe ingresar las posiciones de todos los items' : '';
    }
    /**Guia Detalle */
    public function listar_guia_detalle($id)
    {
        $data = DB::table('almacen.guia_com_det')
            ->select(
                'guia_com_det.*',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_und_medida.abreviatura',
                'alm_prod.series',
                'log_ord_compra.codigo AS cod_orden',
                DB::raw("(guia_ven.serie) || ' ' || (guia_ven.numero) as guia_ven")
            )
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_com_det.id_producto')
            ->leftjoin('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'guia_com_det.id_posicion')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'guia_com_det.id_unid_med')
            ->leftjoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det')
            ->leftjoin('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            ->leftjoin('administracion.adm_tp_docum', 'adm_tp_docum.id_tp_documento', '=', 'log_ord_compra.id_tp_documento')
            ->leftjoin('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven_det', '=', 'guia_com_det.id_guia_ven_det')
            ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')
            ->where([
                ['guia_com_det.id_guia_com', '=', $id],
                ['guia_com_det.estado', '=', 1]
            ])
            ->get();

        $html = '';
        $suma = 0;
        $chk = '';

        $guia = DB::table('almacen.guia_com')
            ->where('id_guia', $id)->first();

        //listar posiciones que no estan enlazadas con ningun producto
        $posiciones = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_ubi_posicion.id_posicion', 'alm_ubi_posicion.codigo')
            ->leftjoin('almacen.alm_prod_ubi', 'alm_prod_ubi.id_posicion', '=', 'alm_ubi_posicion.id_posicion')
            ->leftjoin('almacen.alm_ubi_nivel', 'alm_ubi_nivel.id_nivel', '=', 'alm_ubi_posicion.id_nivel')
            ->leftjoin('almacen.alm_ubi_estante', 'alm_ubi_estante.id_estante', '=', 'alm_ubi_nivel.id_estante')
            ->leftjoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_ubi_estante.id_almacen')
            ->where([
                ['alm_prod_ubi.id_posicion', '=', null],
                ['alm_ubi_posicion.estado', '=', 1],
                ['alm_almacen.id_almacen', '=', $guia->id_almacen]
            ])
            ->get();

        foreach ($data as $det) {
            $id_guia_com_det = $det->id_guia_com_det;
            $oc = $det->cod_orden;
            $codigo = $det->codigo;
            $descripcion = $det->descripcion;
            $cantidad = $det->cantidad;
            $abrev = $det->abreviatura;
            $id_posicion = $det->id_posicion;
            $unitario = (($det->unitario_adicional !== null && $det->unitario_adicional > 0)
                ? ($det->unitario + $det->unitario_adicional)
                : $det->unitario);
            $total = $unitario * $det->cantidad;
            $suma += $total;
            $tiene = strlen($oc);

            //jalar posicion relacionada con el producto
            $posicion = DB::table('almacen.alm_prod_ubi')
                ->select('alm_ubi_posicion.id_posicion', 'alm_ubi_posicion.codigo')
                ->join('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'alm_prod_ubi.id_posicion')
                ->join('almacen.alm_ubi_nivel', 'alm_ubi_nivel.id_nivel', '=', 'alm_ubi_posicion.id_nivel')
                ->join('almacen.alm_ubi_estante', 'alm_ubi_estante.id_estante', '=', 'alm_ubi_nivel.id_estante')
                // ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
                ->where([
                    ['alm_prod_ubi.id_producto', '=', $det->id_producto],
                    ['alm_prod_ubi.estado', '=', 1],
                    ['alm_ubi_estante.id_almacen', '=', $guia->id_almacen]
                ])
                ->get();
            $count = count($posicion);
            $o = false;
            if ($count > 0) {
                $posiciones = $posicion;
                $o = true;
            }
            $chk = ($det->series ? 'true' : 'false');
            $series = '';
            $nro_series = 0;

            if ($chk == 'true') {
                $det_series = DB::table('almacen.alm_prod_serie')
                    ->where([
                        ['alm_prod_serie.id_prod', '=', $det->id_producto],
                        ['alm_prod_serie.id_guia_com_det', '=', $id_guia_com_det],
                        ['alm_prod_serie.estado', '=', 1]
                    ])
                    ->get();

                if (isset($det_series)) {
                    foreach ($det_series as $s) {
                        if ($s->serie !== 'true') {
                            $nro_series++;
                            if ($series !== '') {
                                $series .= ', ' . $s->serie;
                            } else {
                                $series = 'Serie(s): ' . $s->serie;
                            }
                        }
                    }
                }
            }

            $html .=
                '<tr id="reg-' . $id_guia_com_det . '">
                <td>' . $oc . '</td>
                <td>' . $det->guia_ven . '</td>
                <td><input type="text" class="oculto" name="series" value="' . $chk . '"/><input type="number" class="oculto" name="nro_series" value="' . $nro_series . '"/>' . $codigo . '</td>
                <td>' . $descripcion . ' ' . $series . '</td>
                <td>
                    <select class="input-data" name="id_posicion" disabled="true">
                        <option value="0">Elija una opción</option>';
            foreach ($posiciones as $row) {
                if ($o) {
                    $html .= '<option value="' . $row->id_posicion . '" selected>' . $row->codigo . '</option>';
                } else {
                    $html .= '<option value="' . $row->id_posicion . '">' . $row->codigo . '</option>';
                }
            }
            $html .= '</select>
                </td>
                <td><input type="number" class="input-data right" name="cantidad" value="' . $cantidad . '" onChange="calcula_total(' . $id_guia_com_det . ');" disabled="true"/></td>
                <td>' . $abrev . '</td>
                <td><input type="number" class="input-data right" name="unitario" value="' . $unitario . '" onChange="calcula_total(' . $id_guia_com_det . ');" disabled="true"/></td>
                <td><input type="number" class="input-data right" name="total" value="' . $total . '" disabled="true"/></td>
                <td style="display:flex;">';
            if ($chk == "true") {
                $html .= '<i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" title="Agregar Series" onClick="agrega_series(' . $id_guia_com_det . ',' . $codigo . ');"></i>';
            }
            $html .= '<i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_detalle(' . $id_guia_com_det . ',' . $tiene . ');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_detalle(' . $id_guia_com_det . ');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_detalle(' . $id_guia_com_det . ');"></i>
                </td>
            </tr>';
        }
        return json_encode(['html' => $html, 'suma' => $suma]);
        // return response()->json($chk);
    }
    public function mostrar_detalle($id)
    {
        $data = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*', DB::raw("(alm_prod.codigo) || ' ' || (alm_prod.descripcion) as producto"), 'alm_und_medida.abreviatura')
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_com_det.id_producto')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'guia_com_det.id_unid_med')
            ->where([['guia_com_det.id_guia_com_det', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_detalle_oc(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $oc = explode(',', $request->id_oc_det);
        $prod = explode(',', $request->id_producto);
        $pos = explode(',', $request->id_posicion);
        $cant = explode(',', $request->cantidad);
        $unid = explode(',', $request->id_unid_med);
        $unit = explode(',', $request->unitario);
        // $total = explode(',',$request->total);
        $id_usuario = Auth::user()->id_usuario;
        $count = count($oc);

        for ($i = 0; $i < $count; $i++) {
            $id_guia_com = $request->id_guia_com;
            $id_oc_det = $oc[$i];
            $id_producto = $prod[$i];
            $id_posicion = $pos[$i];
            $cantidad = $cant[$i];
            $id_unid_med = $unid[$i];
            $unitario = $unit[$i];
            // $total = $total[$i];

            $p = DB::table('almacen.guia_com_det')
                ->where([
                    ['guia_com_det.id_guia_com', '=', $id_guia_com],
                    ['guia_com_det.id_producto', '=', $id_producto],
                    ['guia_com_det.id_oc_det', '=', $id_oc_det],
                    ['guia_com_det.estado', '=', 1]
                ])
                ->first();

            if (isset($p)) { //variable declarada que su valor NO es nulo
                $cant = floatval($p->cantidad) + floatval($cantidad);
                $data = DB::table('almacen.guia_com_det')
                    ->where('id_guia_com_det', $p->id_guia_com_det)
                    ->update(['cantidad' => $cant]);
            } else {
                $data = DB::table('almacen.guia_com_det')->insertGetId(
                    [
                        'id_guia_com' => $id_guia_com,
                        'id_producto' => $id_producto,
                        'id_posicion' => $id_posicion,
                        'cantidad' => $cantidad,
                        'id_unid_med' => $id_unid_med,
                        'id_oc_det' => $id_oc_det,
                        'unitario' => $unitario,
                        'total' => ($cantidad * $unitario),
                        'usuario' => $id_usuario,
                        'estado' => 1,
                        'fecha_registro' => $fecha
                    ],
                    'id_guia_com_det'
                );

                $ubi = DB::table('almacen.alm_prod_ubi')
                    ->where([
                        ['id_producto', '=', $id_producto],
                        ['id_posicion', '=', $id_posicion]
                    ])
                    ->first();

                if ($ubi == null) {
                    DB::table('almacen.alm_prod_ubi')->insertGetId(
                        [
                            'id_producto' => $id_producto,
                            'id_posicion' => $id_posicion,
                            'stock' => $cantidad,
                            'estado' => 1,
                            'fecha_registro' => $fecha,
                            'costo_promedio' => $unitario,
                        ],
                        'id_prod_ubi'
                    );
                }
            }
        }

        $id_oc = DB::table('logistica.log_det_ord_compra')
            ->where('id_detalle_orden', $oc[0])->first();

        $exist = DB::table('almacen.guia_com_oc')
            ->where([
                ['id_oc', '=', $id_oc->id_orden_compra],
                ['id_guia_com', '=', $request->id_guia_com],
                ['estado', '=', 1]
            ])->first();

        if (empty($exist)) {
            AlmacenController::guardar_oc($request->id_guia_com, $id_oc->id_orden_compra);
        }

        return response()->json($data);
    }
    public function usuario()
    {
        $usu = Auth::user()->id_usuario;
        return response()->json($usu);
    }
    public function guardar_guia_detalle(Request $request)
    {
        $usu = Auth::user()->id_usuario;
        $data = DB::table('almacen.guia_com_det')->insertGetId(
            [
                'id_guia_com' => $request->id_guia,
                'id_producto' => $request->id_producto,
                'id_posicion' => $request->id_posicion,
                'cantidad' => $request->cantidad,
                'id_unid_med' => $request->id_unid_med,
                'unitario' => $request->unitario,
                'unitario_adicional' => 0,
                'total' => $request->total,
                'usuario' => $usu,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
            'id_guia_com_det'
        );
        return response()->json(['data' => $data, 'usuario' => $usu]);
    }
    public function update_guia_detalle(Request $request)
    {
        $guia_det = DB::table('almacen.guia_com_det')
            ->where('id_guia_com_det', $request->id_guia_com_det)
            ->first();

        if ($guia_det->unitario_adicional !== null && $guia_det->unitario_adicional > 0) {
            $data = DB::table('almacen.guia_com_det')
                ->where('id_guia_com_det', $request->id_guia_com_det)
                ->update([
                    'id_posicion' => $request->id_posicion,
                    'cantidad' => $request->cantidad,
                    // 'unitario' => $request->unitario,
                    'total' => $request->total,
                    // 'id_unid_med' => $request->id_unid_med
                ]);
        } else {
            $data = DB::table('almacen.guia_com_det')
                ->where('id_guia_com_det', $request->id_guia_com_det)
                ->update([
                    'id_posicion' => $request->id_posicion,
                    'cantidad' => $request->cantidad,
                    'unitario' => $request->unitario,
                    'total' => $request->total,
                    // 'id_unid_med' => $request->id_unid_med
                ]);
        }

        if (isset($guia_det)) {
            if ($request->id_posicion !== null) {
                //revisa si tiene enlazado una ubicacion
                $ubi = DB::table('almacen.alm_prod_ubi')
                    ->where([
                        ['id_producto', '=', $guia_det->id_producto],
                        ['id_posicion', '=', $request->id_posicion]
                    ])
                    ->first();

                //si no tiene enlazado lo agrega
                if ($ubi == null) {
                    DB::table('almacen.alm_prod_ubi')->insertGetId(
                        [
                            'id_producto' => $guia_det->id_producto,
                            'id_posicion' => $request->id_posicion,
                            'stock' => $request->cantidad,
                            'estado' => 1,
                            'fecha_registro' => date('Y-m-d H:i:s'),
                            'costo_promedio' => $request->unitario,
                        ],
                        'id_prod_ubi'
                    );
                }
            }
        }
        return response()->json($data);
    }
    public function anular_detalle(Request $request, $id)
    {
        $data = DB::table('almacen.guia_com_det')->where('id_guia_com_det', $id)
            ->update(['estado' => 7]);
        return response()->json($data);
    }

    /**Guia Compra OC */
    public static function guardar_oc($id_guia, $id_oc)
    {
        $fecha = date('Y-m-d H:i:s');

        $exist = DB::table('almacen.guia_com_oc')
            ->where([['id_guia_com', $id_guia], ['id_oc', $id_oc]])
            ->first();

        if ($exist == null) {
            $data = DB::table('almacen.guia_com_oc')->insertGetId(
                [
                    'id_guia_com' => $id_guia,
                    'id_oc' => $id_oc,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                'id_guia_com_oc'
            );
            return response()->json($data);
        } else {
            return response()->json($exist);
        }
    }
    public function anular_oc($id, $guia)
    {
        $detalle = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*')
            ->join('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det')
            ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            ->where([
                ['log_ord_compra.id_orden_compra', '=', $id],
                ['guia_com_det.id_guia_com', '=', $guia]
            ])
            ->get()->toArray();

        foreach ($detalle as $det) {
            $dat = DB::table('almacen.guia_com_det')
                ->where('id_guia_com_det', $det->id_guia_com_det)
                ->update(['estado' => 7]);
        }

        $data = DB::table('almacen.guia_com_oc')
            ->where([['id_oc', '=', $id], ['id_guia_com', '=', $guia]])
            ->update(['estado' => 7]);

        return response()->json($data);
    }
    public function guia_ocs($id_guia)
    {
        $data = DB::table('almacen.guia_com_oc')
            ->select(
                'guia_com_oc.id_oc',
                'log_ord_compra.codigo',
                'adm_contri.razon_social',
                'log_ord_compra.fecha',
                //'log_cdn_pago.descripcion as condicion','log_esp_compra.forma_pago_credito','log_esp_compra.fecha_entrega','log_esp_compra.lugar_entrega',
                DB::raw("(log_ord_compra.codigo) || '-' || (adm_contri.razon_social) as orden"),
                DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) as nombre_trabajador")
            )
            ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'guia_com_oc.id_oc')
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->join('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_ord_compra.id_cotizacion')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'log_ord_compra.id_usuario')
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'sis_usua.id_trabajador')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            // ->leftjoin('logistica.log_esp_compra','log_esp_compra.id_especificacion_compra','=','log_cotizacion.id_especificacion_compra')
            // ->join('logistica.log_cdn_pago','log_cdn_pago.id_condicion_pago','=','log_esp_compra.id_condicion_pago')
            ->where([['guia_com_oc.id_guia_com', '=', $id_guia], ['guia_com_oc.estado', '=', 1]])
            ->get();
        return response()->json($data);
    }
    public function listar_ordenes($id_proveedor)
    {
        $data = DB::table('logistica.log_ord_compra')
            ->select(
                'log_ord_compra.id_orden_compra',
                DB::raw("(log_ord_compra.codigo) || ' ' || (adm_contri.razon_social) AS orden")
            )
            ->join('administracion.adm_tp_docum', 'adm_tp_docum.id_tp_documento', '=', 'log_ord_compra.id_tp_documento')
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->where([
                ['log_ord_compra.id_proveedor', '=', $id_proveedor],
                ['log_ord_compra.id_tp_documento', '=', 2],
                ['log_ord_compra.estado', '=', 1]
            ]) //Orden de Compra
            ->get();
        return response()->json($data);
    }
    public function listar_oc_det($id, $id_almacen)
    {
        $data = DB::table('logistica.log_det_ord_compra')
            ->select(
                'log_det_ord_compra.*',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_und_medida.abreviatura',
                'alm_und_medida.id_unidad_medida',
                'alm_item.id_producto',
                'log_ord_compra.codigo as cod_orden',
                'log_valorizacion_cotizacion.precio_cotizado',
                'log_valorizacion_cotizacion.cantidad_cotizada'
            )
            ->join('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            ->join('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', 'log_det_ord_compra.id_valorizacion_cotizacion')
            ->where([
                ['log_det_ord_compra.id_orden_compra', '=', $id],
                ['log_det_ord_compra.estado', '=', 1]
            ])
            ->get();

        $html = '';
        //listar posiciones que no estan enlazadas con ningun producto
        $posiciones = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_ubi_posicion.id_posicion', 'alm_ubi_posicion.codigo')
            ->leftjoin('almacen.alm_prod_ubi', 'alm_prod_ubi.id_posicion', '=', 'alm_ubi_posicion.id_posicion')
            ->leftjoin('almacen.alm_ubi_nivel', 'alm_ubi_nivel.id_nivel', '=', 'alm_ubi_posicion.id_nivel')
            ->leftjoin('almacen.alm_ubi_estante', 'alm_ubi_estante.id_estante', '=', 'alm_ubi_nivel.id_estante')
            ->leftjoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_ubi_estante.id_almacen')
            ->where([
                ['alm_prod_ubi.id_posicion', '=', null], ['alm_ubi_posicion.estado', '=', 1],
                ['alm_almacen.id_almacen', '=', $id_almacen]
            ])
            ->get();

        foreach ($data as $det) {
            $guia = DB::table('almacen.guia_com_det')
                ->select(DB::raw('SUM(guia_com_det.cantidad) as sum_cantidad'))
                ->where([
                    ['id_oc_det', '=', $det->id_detalle_orden],
                    ['estado', '=', 1]
                ])
                ->first();
            $cantidad_nueva = $det->cantidad_cotizada - ($guia->sum_cantidad !== null ? $guia->sum_cantidad : 0);
            //Si hay cantidad por atender = cantidad > 0
            if ($cantidad_nueva > 0) {
                $o = false;
                //jalar posicion relacionada con el producto
                $posicion = DB::table('almacen.alm_prod_ubi')
                    ->select('alm_ubi_posicion.id_posicion', 'alm_ubi_posicion.codigo')
                    ->join('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'alm_prod_ubi.id_posicion')
                    ->where([
                        ['alm_prod_ubi.id_producto', '=', $det->id_producto],
                        ['alm_prod_ubi.id_almacen', '=', $id_almacen],
                        ['alm_prod_ubi.estado', '=', 1]
                    ])
                    ->get();
                $count = count($posicion);
                if ($count > 0) {
                    $posiciones = $posicion;
                    $o = true;
                }
                // $unitario = $det->subtotal / $det->cantidad_cotizada;
                // $cod_posicion = (isset($posicion->cod_posicion) ? $posicion->cod_posicion : '');

                $html .=
                    '<tr id="oc-' . $det->id_detalle_orden . '">
                    <td><input type="checkbox" checked></td>
                    <td><input type="text" name="id_oc_det" class="oculto" value="' . $det->id_detalle_orden . '"/>' . $det->cod_orden . '</td>
                    <td><input type="text" name="id_producto" class="oculto" value="' . $det->id_producto . '"/>' . $det->codigo . '</td>
                    <td>' . $det->descripcion . '</td>
                    <td>
                        <select class="input-data js-example-basic-single" name="id_posicion">
                            <option value="0">Elija una opción</option>';
                // $pos = $this->mostrar_posiciones_cbo();
                foreach ($posiciones as $row) {
                    if ($o) {
                        $html .= '<option value="' . $row->id_posicion . '" selected>' . $row->codigo . '</option>';
                    } else {
                        $html .= '<option value="' . $row->id_posicion . '">' . $row->codigo . '</option>';
                    }
                }
                $html .= '</select>
                    </td>
                    <td><input type="number" name="cantidad" class="input-data right" onChange="calcula_total_oc(' . $det->id_detalle_orden . ');"  value="' . $cantidad_nueva . '"/></td>
                    <td><input type="text" name="id_unid_med" class="oculto" value="' . $det->id_unidad_medida . '"/>' . $det->abreviatura . '</td>
                    <td><input type="number" name="unitario" class="input-data right" readOnly value="' . $det->precio_cotizado . '"/></td>
                    <td><input type="number" name="total" class="input-data right" readOnly value="' . ($cantidad_nueva * $det->precio_cotizado) . '"/></td>
                </tr>';
            }
        }
        // return response()->json($nueva_data);
        return json_encode($html);
    }
    public function posiciones()
    {
        $posiciones = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_ubi_posicion.id_posicion', 'alm_ubi_posicion.codigo')
            ->leftjoin('almacen.alm_prod_ubi', 'alm_prod_ubi.id_posicion', '=', 'alm_ubi_posicion.id_posicion')
            ->where([['alm_prod_ubi.id_posicion', '=', null], ['alm_ubi_posicion.estado', '=', 1]])
            ->get();
        $posicion = DB::table('almacen.alm_prod_ubi')
            ->select('alm_ubi_posicion.id_posicion', 'alm_ubi_posicion.codigo')
            ->join('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'alm_prod_ubi.id_posicion')
            ->where([['alm_prod_ubi.id_producto', '=', 2930], ['alm_prod_ubi.estado', '=', 1]])
            ->get();
        $count = count($posicion);
        if ($count > 0) {
            $posiciones = $posicion;
        }
        return response()->json($posiciones);
    }
    /**Guardar Series */
    public function guardar_series(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $se = explode(',', $request->series);
        $count = count($se);
        $data = 0;
        if (!empty($request->series)) {
            $id = DB::table('almacen.guia_com_det')
                ->select('guia_com_det.*', 'guia_com.id_almacen')
                ->where('id_guia_com_det', $request->id_guia_com_det)
                ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
                ->first();

            for ($i = 0; $i < $count; $i++) {
                $serie = $se[$i];
                $data = DB::table('almacen.alm_prod_serie')->insertGetId(
                    [
                        'id_prod' => $id->id_producto,
                        'id_almacen' => $id->id_almacen,
                        'serie' => $serie,
                        'estado' => 1,
                        'fecha_registro' => $fecha,
                        'id_guia_com_det' => $request->id_guia_com_det
                    ],
                    'id_prod_serie'
                );
            }
        }
        $an = explode(',', $request->anulados);
        $can = count($an);

        if (!empty($request->anulados)) {
            for ($i = 0; $i < $can; $i++) {
                $data = DB::table('almacen.alm_prod_serie')
                    ->where('id_prod_serie', $an[$i])
                    ->update(['estado' => 7]);
            }
        }

        return response()->json($data);
    }
    public function listar_series($id_guia_com_det)
    {
        $series = DB::table('almacen.alm_prod_serie')
            ->where([
                ['id_guia_com_det', '=', $id_guia_com_det],
                ['estado', '=', 1]
            ])
            ->get();
        return response()->json($series);
    }

    public function listar_series_almacen($id_prod, $id_almacen)
    {
        $series = DB::table('almacen.alm_prod_serie')
            ->select(
                'alm_prod_serie.*',
                'guia_com.fecha_emision',
                DB::raw("(tp_doc_almacen.abreviatura) || '-' || (guia_com.serie) || '-' || (guia_com.numero) as guia_com")
            )
            ->join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'alm_prod_serie.id_guia_com_det')
            ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->join('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_com.id_tp_doc_almacen')
            ->where([
                ['alm_prod_serie.id_prod', '=', $id_prod],
                ['alm_prod_serie.id_almacen', '=', $id_almacen],
                ['alm_prod_serie.id_guia_ven_det', '=', null],
                ['alm_prod_serie.estado', '=', 1]
            ])
            ->get();
        $output['data'] = $series;
        return response()->json($output);
    }
    public function buscar_serie($serie)
    {
        $data = DB::table('almacen.alm_prod_serie')
            ->select(
                'alm_prod_serie.*',
                DB::raw("(tp_doc_com.abreviatura) || '-' || (guia_com.serie) || '-' || (guia_com.numero) as guia_com"),
                DB::raw("(tp_doc_ven.abreviatura) || '-' || (guia_ven.serie) || '-' || (guia_ven.numero) as guia_ven")
            )
            ->leftjoin('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven_det', '=', 'alm_prod_serie.id_guia_ven_det')
            ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')
            ->leftjoin('almacen.tp_doc_almacen as tp_doc_ven', 'tp_doc_ven.id_tp_doc_almacen', '=', 'guia_ven.id_tp_doc_almacen')
            ->leftjoin('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'alm_prod_serie.id_guia_com_det')
            ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->leftjoin('almacen.tp_doc_almacen as tp_doc_com', 'tp_doc_com.id_tp_doc_almacen', '=', 'guia_com.id_tp_doc_almacen')
            ->where([['alm_prod_serie.serie', '=', $serie], ['alm_prod_serie.estado', '=', 1]])
            ->first();
        return response()->json($data);
    }

    /**Comprobante de Compra */
    /** */

    public function listar_docven_guias($id_doc)
    {
        $guias = DB::table('almacen.doc_ven_guia')
            ->select(
                'doc_ven_guia.*',
                DB::raw("('GR') || '-' || (guia_ven.serie) || '-' || (guia_ven.numero) as guia"),
                'guia_ven.fecha_emision as fecha_guia', //'guia_motivo.descripcion as des_motivo',
                'adm_contri.razon_social'
            )
            ->join('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'doc_ven_guia.id_guia_ven')
            // ->join('almacen.guia_motivo','guia_motivo.id_motivo','=','guia_ven.id_motivo')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'guia_ven.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->where([
                ['doc_ven_guia.id_doc_ven', '=', $id_doc],
                ['doc_ven_guia.estado', '=', 1]
            ])
            ->get();
        $html = '';
        foreach ($guias as $guia) {
            $html .= '
            <tr id="doc-' . $guia->id_doc_ven_guia . '">
                <td>' . $guia->guia . '</td>
                <td>' . $guia->fecha_guia . '</td>
                <td>' . $guia->razon_social . '</td>
                <td>' . $guia->des_motivo . '</td>
                <td><i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom"
                    title="Anular Guia" onClick="anular_guia(' . $guia->id_guia_ven . ',' . $guia->id_doc_ven_guia . ');"></i>
                </td>
            </tr>';
        }
        return json_encode($html);
    }

    public function listar_guias_almacen($id_almacen)
    {
        $data = DB::table('almacen.guia_com')
            ->select('guia_com.*', 'adm_contri.razon_social')
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'guia_com.id_proveedor')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            // ->join('logistica.guia_motivo','guia_motivo.id_motivo','=','guia_com.id_motivo')
            ->where([['guia_com.id_almacen', '=', $id_almacen], ['guia_com.estado', '!=', 7]])
            ->get();
        return response()->json($data);
        // return json_encode($html);
    }
    public function listar_req($id_sede)
    {
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*')
            ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
            ->where([
                ['sis_sede.id_sede', '=', $id_sede],
                ['alm_req.stock_comprometido', '=', true], //stock comprometido de almacen
                ['alm_req.estado', '!=', 7]
            ])
            ->get();
        return response()->json($data);
    }

    public function listar_docven_items($id_doc)
    {
        $detalle = DB::table('almacen.doc_ven_det')
            ->select(
                'doc_ven_det.*',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'tp_doc_almacen.abreviatura as guia_tp_doc_almacen',
                'guia_ven.serie as guia_serie',
                'guia_ven.numero as guia_numero',
                'alm_und_medida.abreviatura'
            )
            ->join('almacen.alm_item', 'alm_item.id_item', '=', 'doc_ven_det.id_item')
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftjoin('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven_det', '=', 'doc_ven_det.id_guia_ven_det')
            ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')
            ->leftjoin('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_ven.id_tp_doc_almacen')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'doc_ven_det.id_unid_med')
            ->where([
                ['doc_ven_det.id_doc', '=', $id_doc],
                ['doc_ven_det.estado', '=', 1]
            ])
            ->get();
        $html = '';
        foreach ($detalle as $det) {
            // <td>'.($det->guia_tp_doc_almacen !== null ?
            // ($det->guia_tp_doc_almacen.'-'.$det->guia_serie.'-'.$det->guia_numero) : '').'</td>

            $html .= '
            <tr id="det-' . $det->id_doc_det . '">
                <td>' . $det->codigo . '</td>
                <td>' . $det->descripcion . '</td>
                <td><input type="number" class="input-data right" name="cantidad"
                    value="' . $det->cantidad . '" onChange="calcula_total(' . $det->id_doc_det . ');"
                    disabled="true"/>
                </td>
                <td>' . $det->abreviatura . '</td>
                <td><input type="number" class="input-data right" name="precio_unitario"
                    value="' . $det->precio_unitario . '" onChange="calcula_total(' . $det->id_doc_det . ');"
                    disabled="true"/>
                </td>
                <td><input type="number" class="input-data right" name="porcen_dscto"
                    value="' . $det->porcen_dscto . '" onChange="calcula_dscto(' . $det->id_doc_det . ');"
                    disabled="true"/>
                </td>
                <td><input type="number" class="input-data right" name="total_dscto"
                    value="' . $det->total_dscto . '" onChange="calcula_total(' . $det->id_doc_det . ');"
                    disabled="true"/>
                </td>
                <td><input type="number" class="input-data right" name="precio_total"
                    value="' . $det->precio_total . '" disabled="true"/>
                </td>
                <td style="display:flex;">
                    <i class="fas fa-pen-square icon-tabla blue boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_detalle(' . $det->id_doc_det . ');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_detalle(' . $det->id_doc_det . ');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_detalle(' . $det->id_doc_det . ');"></i>
                </td>
            </tr>';
        }
        return json_encode($html);
    }

    public function guardar_doc_guia(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $usuario = Auth::user();

        $guia = DB::table('almacen.guia_com')
            ->select('guia_com.*')
            ->where('id_guia', $request->id_guia)
            ->first();

        $oc = DB::table('almacen.guia_com_oc')
            ->select('log_ord_compra.id_moneda', 'log_ord_compra.id_condicion', 'log_ord_compra.plazo_dias')
            ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'guia_com_oc.id_oc')
            ->where('id_guia_com', $request->id_guia)
            ->first();

        $detalle = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*', 'log_valorizacion_cotizacion.precio_sin_igv')
            ->leftjoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det')
            ->leftjoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', 'log_det_ord_compra.id_valorizacion_cotizacion')
            ->where([
                ['guia_com_det.id_guia_com', '=', $request->id_guia],
                ['guia_com_det.estado', '=', 1]
            ])
            ->get();

        $id_doc = DB::table('almacen.doc_com')->insertGetId(
            [
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_tp_doc' => $request->id_tp_doc,
                'id_proveedor' => $guia->id_proveedor,
                'fecha_emision' => $request->fecha_emision,
                'fecha_vcmto' => $request->fecha_emision,
                'id_condicion' => (($oc !== null && $oc->id_condicion !== null) ? $oc->id_condicion : null),
                'credito_dias' => (($oc !== null && $oc->plazo_dias !== null) ? $oc->plazo_dias : null),
                'moneda' => (($oc !== null && $oc->id_moneda !== null) ? $oc->id_moneda : null),
                'usuario' => $usuario->id_usuario,
                'registrado_por' => $usuario->id_usuario,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
            'id_doc_com'
        );
        $sub_total = 0;

        foreach ($detalle as $det) {
            $total = ($det->precio_sin_igv !== null) ? $det->precio_sin_igv : $det->total;
            $unitario = $total / $det->cantidad;
            $sub_total += $total;

            $item = DB::table('almacen.alm_item')
                ->where('id_producto', $det->id_producto)
                ->first();

            $id_det = DB::table('almacen.doc_com_det')->insertGetId(
                [
                    'id_doc' => $id_doc,
                    'id_item' => $item->id_item,
                    'cantidad' => $det->cantidad,
                    'id_unid_med' => $det->id_unid_med,
                    'precio_unitario' => $unitario,
                    'sub_total' => $total,
                    'porcen_dscto' => 0,
                    'total_dscto' => 0,
                    'precio_total' => $total,
                    'id_guia_com_det' => $det->id_guia_com_det,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                'id_doc_det'
            );
        }
        //obtiene IGV
        $impuesto = DB::table('contabilidad.cont_impuesto')
            ->where([['codigo', '=', 'IGV'], ['fecha_inicio', '<', $request->fecha_emision]])
            ->orderBy('fecha_inicio', 'desc')
            ->first();
        $igv = $impuesto->porcentaje * $sub_total / 100;

        //actualiza totales
        DB::table('almacen.doc_com')->where('id_doc_com', $id_doc)
            ->update([
                'sub_total' => $sub_total,
                'total_descuento' => 0,
                'porcen_descuento' => 0,
                'total' => $sub_total,
                'total_igv' => $igv,
                'total_ant_igv' => 0,
                'porcen_igv' => $request->porcen_igv,
                'porcen_anticipo' => $request->porcen_anticipo,
                'total_otros' => $request->total_otros,
                'total_a_pagar' => ($sub_total + $igv)
            ]);

        $guia = DB::table('almacen.doc_com_guia')->insertGetId(
            [
                'id_doc_com' => $id_doc,
                'id_guia_com' => $request->id_guia,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
            'id_doc_com_guia'
        );
        $ingreso = DB::table('almacen.mov_alm')
            ->where('mov_alm.id_guia_com', $request->id_guia)
            ->first();

        if (isset($ingreso->id_mov_alm)) {
            DB::table('almacen.mov_alm')
                ->where('id_mov_alm', $ingreso->id_mov_alm)
                ->update(['id_doc_com' => $id_doc]);
        }
        $tp = DB::table('contabilidad.cont_tp_doc')->select('abreviatura')
            ->where('id_tp_doc', $request->id_tp_doc)->first();

        return response()->json(['id_doc' => $id_doc, 'tp_doc' => $tp->abreviatura, 'doc_serie' => $request->serie, 'doc_numero' => $request->numero]);
    }
    public function actualizar_doc_guia(Request $request)
    {
        $data = DB::table('almacen.doc_com')->where('id_doc_com', $request->id_doc_com)
            ->update([
                'id_tp_doc' => $request->id_tp_doc,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'fecha_emision' => $request->fecha_emision,
                'id_proveedor' => $request->id_proveedor,
            ]);
        $tp = DB::table('contabilidad.cont_tp_doc')->select('abreviatura')
            ->where('id_tp_doc', $request->id_tp_doc)->first();

        return response()->json(['id_doc' => $request->id_doc_com, 'tp_doc' => $tp->abreviatura, 'doc_serie' => $request->serie, 'doc_numero' => $request->numero]);
    }
    public function anular_guia($doc, $guia)
    {
        $detalle = DB::table('almacen.doc_com_det')
            ->select('doc_com_det.*')
            ->join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'doc_com_det.id_guia_com_det')
            ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->where([
                ['doc_com_det.id_doc', '=', $doc],
                ['guia_com.id_guia', '=', $guia]
            ])
            ->get()->toArray();

        foreach ($detalle as $det) {
            DB::table('almacen.doc_com_det')
                ->where('id_doc_det', $det->id_doc_det)
                ->update(['estado' => 7]);
        }

        $data = DB::table('almacen.doc_com_guia')
            ->where([['id_doc_com', '=', $doc], ['id_guia_com', '=', $guia]])
            ->update(['estado' => 7]);

        return response()->json($data);
    }
    public function anular_guiaven($doc, $guia)
    {
        $detalle = DB::table('almacen.doc_ven_det')
            ->select('doc_ven_det.*')
            ->join('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven_det', '=', 'doc_ven_det.id_guia_ven_det')
            ->join('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')
            ->where([
                ['doc_ven_det.id_doc', '=', $doc],
                ['guia_ven.id_guia_ven', '=', $guia]
            ])
            ->get()->toArray();

        foreach ($detalle as $det) {
            DB::table('almacen.doc_ven_det')
                ->where('id_doc_det', $det->id_doc_det)
                ->update(['estado' => 7]);
        }

        $data = DB::table('almacen.doc_ven_guia')
            ->where([['id_doc_ven', '=', $doc], ['id_guia_ven', '=', $guia]])
            ->update(['estado' => 7]);

        return response()->json($data);
    }
    public function listar_requerimientos()
    {
        $data = DB::table('almacen.alm_req')
            ->select(
                'alm_req.*',
                'proy_proyecto.descripcion as proy_descripcion',
                'adm_area.descripcion as area_descripcion',
                'adm_prioridad.descripcion as des_prioridad',
                'adm_grupo.descripcion as des_grupo',
                DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) as responsable")
            )
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'sis_usua.id_trabajador')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->join('administracion.adm_prioridad', 'adm_prioridad.id_prioridad', '=', 'alm_req.id_prioridad')
            ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftjoin('proyectos.proy_proyecto', 'proy_proyecto.id_proyecto', '=', 'alm_req.id_proyecto')
            ->leftjoin('administracion.adm_area', 'adm_area.id_area', '=', 'alm_req.id_area')
            ->where([
                ['alm_req.estado', '=', 1],
                ['alm_req.id_tipo_requerimiento', '=', 1]
            ])
            ->get();
        // $i = 1;
        // $html = '';

        // foreach($data as $reg){
        //     $html .= '
        //     <tr id="req-'.$reg->id_requerimiento.'">
        //         <td>
        //             <input type="checkbox" class="flat-red">
        //         </td>
        //         <td>'.$i.'</td>
        //         <td>'.$reg->codigo.'</td>
        //         <td>'.$reg->fecha_requerimiento.'</td>
        //         <td>'.$reg->responsable.'</td>
        //         <td>'.$reg->concepto.'</td>';
        //         if ($reg->id_proyecto !== null){
        //             $html.='<td>'.$reg->proy_descripcion.'</td>';
        //         } else {
        //             $html.='<td>'.$reg->area_descripcion.'</td>';
        //         }
        //         $html.='<td><i class="fas fa-search-plus icon-tabla blue"></i></td>
        //     </tr>
        //     ';
        //     $i++;
        // }
        $output['data'] = $data;
        return response()->json($output);
    }
    public function listar_items_req($id)
    {
        $data = DB::table('almacen.alm_det_req')
            ->select(
                'alm_det_req.*',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_und_medida.abreviatura',
                'alm_ubi_posicion.codigo as cod_posicion'
            )
            ->join('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftjoin('almacen.alm_prod_ubi', 'alm_prod_ubi.id_producto', '=', 'alm_prod.id_producto')
            ->leftjoin('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'alm_prod_ubi.id_posicion')
            ->where('alm_det_req.id_requerimiento', $id)
            ->get();
        // $i = 1;
        // $html = '';

        // foreach($data as $reg){
        //     $html .= '
        //     <tr id="det-'.$reg->id_detalle_requerimiento.'">
        //         <td>
        //             <input type="checkbox" class="flat-red">
        //         </td>
        //         <td>'.$i.'</td>
        //         <td>'.$reg->codigo.'</td>
        //         <td>'.$reg->descripcion.'</td>
        //         <td>'.$reg->cod_posicion.'</td>
        //         <td>'.$reg->cantidad.'</td>
        //         <td>'.$reg->abreviatura.'</td>
        //         <td>'.$reg->partida.'</td>
        //     </tr>
        //     ';
        //     $i++;
        // }
        // return json_encode($html);
        $output['data'] = $data;
        return response()->json($output);
    }
    public function id_producto($id_item)
    {
        $item = DB::table('almacen.alm_item')
            ->where('id_item', $id_item)
            ->first();
        return $item->id_producto;
    }
    public function id_salida($id_guia)
    {
        $sal = DB::table('almacen.mov_alm')
            ->where([
                ['mov_alm.id_guia_ven', '=', $id_guia],
                ['mov_alm.estado', '=', 1]
            ])
            ->first();
        if (isset($sal)) {
            return response()->json($sal->id_mov_alm);
        } else {
            return response()->json(0);
        }
    }

    public function kardex_general($almacenes, $finicio, $ffin)
    {
        $alm_array = explode(',', $almacenes);

        $query = MovimientoDetalle::select(
            'mov_alm_det.*',
            'mov_alm.fecha_emision',
            'mov_alm.id_tp_mov',
            'mov_alm.codigo',
            'alm_prod.descripcion as prod_descripcion',
            'alm_prod.codigo as prod_codigo',
            'alm_prod.part_number as prod_part_number',
            'alm_cat_prod.descripcion as categoria',
            'alm_subcat.descripcion as subcategoria',
            'alm_und_medida.abreviatura',
            'tp_ope_com.cod_sunat as cod_sunat_com',
            'tp_ope_com.descripcion as tp_com_descripcion',
            'tp_ope_ven.cod_sunat as cod_sunat_ven',
            'tp_ope_ven.descripcion as tp_ven_descripcion',
            DB::raw("(tp_guia_com.abreviatura) || '-' || (guia_com.serie) || '-' || (guia_com.numero) as guia_com"),
            DB::raw("(tp_guia_ven.abreviatura) || '-' || (guia_ven.serie) || '-' || (guia_ven.numero) as guia_ven"),
            'guia_com.id_guia',
            'guia_ven.id_guia_ven',
            'alm_almacen.descripcion as almacen_descripcion',
            'transformacion.codigo as cod_transformacion',
            'trans.codigo as cod_transferencia'
        )
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            ->leftjoin('almacen.transformacion', 'transformacion.id_transformacion', '=', 'mov_alm.id_transformacion')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
            ->join('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->join('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'mov_alm.id_guia_com')
            ->leftjoin('almacen.tp_doc_almacen as tp_guia_com', 'tp_guia_com.id_tp_doc_almacen', '=', 'guia_com.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope as tp_ope_com', 'tp_ope_com.id_operacion', '=', 'mov_alm.id_operacion')
            ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'mov_alm.id_guia_ven')
            ->leftjoin('almacen.tp_doc_almacen as tp_guia_ven', 'tp_guia_ven.id_tp_doc_almacen', '=', 'guia_ven.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope as tp_ope_ven', 'tp_ope_ven.id_operacion', '=', 'mov_alm.id_operacion')
            ->leftjoin('almacen.trans', 'trans.id_transferencia', '=', 'mov_alm.id_transferencia')
            ->leftjoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
            ->where([
                ['mov_alm.fecha_emision', '>=', $finicio],
                ['mov_alm.fecha_emision', '<=', $ffin],
                ['mov_alm_det.estado', '=', 1]
            ])
            ->whereIn('mov_alm.id_almacen', $alm_array)
            ->orderBy('alm_prod.codigo', 'asc')
            ->orderBy('mov_alm.fecha_emision', 'asc')
            ->orderBy('mov_alm.id_tp_mov', 'asc')
            ->get();

        $saldo = 0;
        $saldo_valor = 0;
        $movimientos = [];
        $codigo = '';

        $costo_promedio = 0;
        $saldo_valor_aux = 0;
        $valor_salida=0;
        foreach ($query as $d) {


            if ($d->prod_codigo !== $codigo) {
                $saldo = 0;
                $saldo_valor = 0;
            }
            $ordenes = "";
            $comprobantes_array = [];


            if ($d->id_tp_mov == 1 || $d->id_tp_mov == 0) {
                $saldo += $d->cantidad;
                $saldo_valor += $d->valorizacion;


                if ($d->id_guia_com_det !== null) {
                    $ordenes = $d->movimiento->requerimientos;

                    $comprobantes = MovimientoDetalle::join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', 'mov_alm_det.id_guia_com_det')
                        ->join('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', 'guia_com_det.id_guia_com_det')
                        ->join('almacen.doc_com', 'doc_com.id_doc_com', 'doc_com_det.id_doc')
                        ->join('logistica.log_prove', 'log_prove.id_proveedor', 'doc_com.id_proveedor')
                        ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', 'log_prove.id_contribuyente')
                        ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_com.moneda')
                        ->join('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_com.id_condicion')
                        ->where([
                            ['mov_alm_det.id_mov_alm', '=', $d->id_mov_alm],
                            ['mov_alm_det.estado', '!=', 7],
                            ['guia_com_det.estado', '!=', 7],
                            ['doc_com_det.estado', '!=', 7]
                        ])
                        ->select([
                            'doc_com.serie', 'doc_com.numero', 'doc_com.fecha_emision', 'sis_moneda.simbolo', 'doc_com.moneda',
                            'adm_contri.nro_documento', 'adm_contri.razon_social', 'log_cdn_pago.descripcion as des_condicion',
                            'doc_com.credito_dias', 'doc_com.sub_total', 'doc_com.total_igv', 'doc_com.total_a_pagar'
                        ])
                        ->distinct()->get();

                    foreach ($comprobantes as $doc) {
                        array_push($comprobantes_array, $doc->serie . '-' . $doc->numero);
                    }
                }
                $saldo_valor_aux += $d->valorizacion;
            } else if ($d->id_tp_mov == 2) {
                $saldo -= $d->cantidad;
                $saldo_valor -= $d->valorizacion;

                $valor_salida = (int) $costo_promedio * (int) $d->cantidad;
                $saldo_valor_aux -= $valor_salida;
            }

            $costo_promedio = ($saldo == 0 ? 0 : $saldo_valor_aux / $saldo);
            $costo_promedio = number_format($costo_promedio, 4, ".", ",");

            $codigo = $d->prod_codigo;

            $nuevo = [
                "id_mov_alm_det" => $d->id_mov_alm_det,
                "codigo" => $d->codigo,
                "categoria" => $d->categoria,
                "subcategoria" => $d->subcategoria,
                "prod_codigo" => $d->prod_codigo,
                "prod_part_number" => $d->prod_part_number,
                "prod_descripcion" => $d->prod_descripcion,
                "fecha_emision" => $d->fecha_emision,
                "posicion" => $d->posicion,
                "almacen_descripcion" => $d->almacen_descripcion,
                "abreviatura" => $d->abreviatura,
                "tipo" => $d->id_tp_mov,
                "cantidad" => $d->cantidad,
                "saldo" => $saldo,
                "valorizacion" => $d->valorizacion,
                "saldo_valor" => $saldo_valor,
                "cod_sunat_com" => $d->cod_sunat_com,
                "cod_sunat_ven" => $d->cod_sunat_ven,
                "tp_com_descripcion" => $d->tp_com_descripcion,
                "tp_ven_descripcion" => $d->tp_ven_descripcion,
                "id_guia_com" => $d->id_guia,
                "id_guia_ven" => $d->id_guia_ven,
                "guia_com" => $d->guia_com,
                "guia_ven" => $d->guia_ven,
                "cod_transformacion" => $d->cod_transformacion,
                "cod_transferencia" => $d->cod_transferencia,
                "orden" => $ordenes,
                "docs" => implode(', ', $comprobantes_array),

                "costo_promedio_2"=>$costo_promedio
            ];
            array_push($movimientos, $nuevo);
        }
        return response()->json($movimientos);
    }
    public function listar_saldos_por_almacen()
    {
        $data = DB::table('almacen.alm_item')
            ->select(
                'alm_item.id_item',
                'alm_item.id_servicio',
                'alm_prod.id_producto',
                'alm_prod.estado as estado_producto',
                'log_servi.estado as estado_servicio',
                // 'alm_prod.codigo',
                DB::raw("(CASE
                WHEN alm_item.id_servicio isNUll THEN alm_prod.codigo
                WHEN alm_item.id_producto isNUll THEN log_servi.codigo
                ELSE 'nulo' END) AS codigo
                "),
                // 'alm_prod.descripcion',
                DB::raw("(CASE
                WHEN alm_item.id_servicio isNUll THEN alm_prod.descripcion
                WHEN alm_item.id_producto isNUll THEN log_servi.descripcion
                ELSE 'nulo' END) AS descripcion
                "),
                'alm_und_medida.abreviatura',
                'alm_prod.codigo_anexo',
                'alm_prod.part_number',
                'alm_cat_prod.descripcion as des_categoria',
                'alm_subcat.descripcion as des_subcategoria',
                'alm_clasif.descripcion as des_clasificacion',
                'alm_prod.id_unidad_medida'
            )
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftJoin('almacen.alm_clasif', 'alm_clasif.id_clasificacion', '=', 'alm_prod.id_clasif')
            ->leftJoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->leftJoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->where([['alm_prod.estado', '=', 1], ['log_servi.estado', '=', null]])
            ->orWhere([['alm_prod.estado', '=', null], ['log_servi.estado', '=', 1]])
            ->distinct()->get();

        $nueva_data = [];
        $fecha = date('Y-m-d');
        $almacenes = DB::table('almacen.alm_almacen')->where('estado', 1)->get();

        foreach ($data as $d) {
            $stock_almacenes = [];

            foreach ($almacenes as $alm) {
                $stock = DB::table('almacen.alm_prod_ubi')
                    ->select(
                        'alm_prod_ubi.id_prod_ubi',
                        'alm_prod_ubi.stock',
                        'alm_prod_ubi.costo_promedio',
                        DB::raw("(SELECT SUM(alm_det_req.cantidad) FROM almacen.alm_det_req
                        WHERE alm_det_req.estado=19
                        AND alm_det_req.id_producto=alm_prod_ubi.id_producto
                        AND alm_det_req.id_almacen_reserva=alm_prod_ubi.id_almacen) as cantidad_reserva")
                    )
                    ->where([
                        ['alm_prod_ubi.id_producto', '=', $d->id_producto],
                        ['alm_prod_ubi.id_almacen', '=', $alm->id_almacen]
                    ])
                    ->first();

                if ($stock !== null) {
                    $nuevo = [
                        'id_prod_ubi' => $stock->id_prod_ubi,
                        'id_almacen' => $alm->id_almacen,
                        'almacen_descripcion' => $alm->descripcion,
                        'stock' => $stock->stock,
                        'costo_promedio' => $stock->costo_promedio,
                        'cantidad_reserva' => ($stock->cantidad_reserva !== null ? $stock->cantidad_reserva : 0)
                    ];
                    array_push($stock_almacenes, $nuevo);
                } else {
                    $nuevo = [
                        'id_prod_ubi' => 0,
                        'id_almacen' => $alm->id_almacen,
                        'almacen_descripcion' => $alm->descripcion,
                        'stock' => 0,
                        'costo_promedio' => 0,
                        'cantidad_reserva' => 0
                    ];
                    array_push($stock_almacenes, $nuevo);
                }
            }
            $nuevo = [
                'id_producto' => $d->id_producto,
                'id_servicio' => $d->id_servicio,
                'estado_producto' => $d->estado_producto,
                'estado_servicio' => $d->estado_servicio,
                'id_item' => $d->id_item,
                'codigo' => $d->codigo,
                'codigo_anexo' => $d->codigo_anexo,
                'part_number' => $d->part_number,
                'descripcion' => $d->descripcion,
                'abreviatura' => $d->abreviatura,
                'id_unidad_medida' => $d->id_unidad_medida,
                'des_clasificacion' => $d->des_clasificacion,
                'des_categoria' => $d->des_categoria,
                'des_subcategoria' => $d->des_subcategoria,
                'stock_almacenes' => $stock_almacenes
            ];
            array_push($nueva_data, $nuevo);
        }
        $output['data'] = $nueva_data;
        return response()->json($output);
    }

    public function listar_saldos_por_almacen_producto($id_producto)
    {
        $data = DB::table('almacen.alm_prod')
            ->select(
                'alm_item.id_item',
                'alm_prod.id_producto',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_und_medida.abreviatura',
                'alm_prod.codigo_anexo',
                'alm_prod.part_number',
                'alm_cat_prod.descripcion as des_categoria',
                'alm_subcat.descripcion as des_subcategoria',
                'alm_clasif.descripcion as des_clasificacion',
                'alm_prod.id_unidad_medida'
            )
            ->join('almacen.alm_item', 'alm_item.id_producto', '=', 'alm_prod.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->join('almacen.alm_clasif', 'alm_clasif.id_clasificacion', '=', 'alm_prod.id_clasif')
            ->join('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->join('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->where([
                ['alm_prod.estado', '=', 1],
                ['alm_prod.id_producto', '=', $id_producto]
            ])
            ->distinct()->get();

        $nueva_data = [];
        $fecha = date('Y-m-d');
        $almacenes = DB::table('almacen.alm_almacen')->where('estado', 1)->get();

        foreach ($data as $d) {
            $stock_almacenes = [];

            foreach ($almacenes as $alm) {
                $stock = DB::table('almacen.alm_prod_ubi')
                    ->select(
                        'alm_prod_ubi.id_prod_ubi',
                        'alm_prod_ubi.stock',
                        'alm_prod_ubi.costo_promedio',
                        DB::raw("(SELECT SUM(alm_det_req.cantidad) FROM almacen.alm_det_req
                        WHERE alm_det_req.estado=19
                        AND alm_det_req.id_producto=alm_prod_ubi.id_producto
                        AND alm_det_req.id_almacen_reserva=alm_prod_ubi.id_almacen) as cantidad_reserva")
                    )
                    ->where([
                        ['alm_prod_ubi.id_producto', '=', $d->id_producto],
                        ['alm_prod_ubi.id_almacen', '=', $alm->id_almacen]
                    ])
                    ->first();

                if ($stock !== null) {
                    $nuevo = [
                        'id_prod_ubi' => $stock->id_prod_ubi,
                        'id_almacen' => $alm->id_almacen,
                        'almacen_descripcion' => $alm->descripcion,
                        'stock' => $stock->stock,
                        'costo_promedio' => $stock->costo_promedio,
                        'cantidad_reserva' => ($stock->cantidad_reserva !== null ? $stock->cantidad_reserva : 0)
                    ];
                    array_push($stock_almacenes, $nuevo);
                } else {
                    $nuevo = [
                        'id_prod_ubi' => 0,
                        'id_almacen' => $alm->id_almacen,
                        'almacen_descripcion' => $alm->descripcion,
                        'stock' => 0,
                        'costo_promedio' => 0,
                        'cantidad_reserva' => 0
                    ];
                    array_push($stock_almacenes, $nuevo);
                }
            }
            $nuevo = [
                'id_producto' => $d->id_producto,
                'id_item' => $d->id_item,
                'codigo' => $d->codigo,
                'codigo_anexo' => $d->codigo_anexo,
                'part_number' => $d->part_number,
                'descripcion' => $d->descripcion,
                'abreviatura' => $d->abreviatura,
                'id_unidad_medida' => $d->id_unidad_medida,
                'des_clasificacion' => $d->des_clasificacion,
                'des_categoria' => $d->des_categoria,
                'des_subcategoria' => $d->des_subcategoria,
                'stock_almacenes' => $stock_almacenes
            ];
            array_push($nueva_data, $nuevo);
        }
        $output['data'] = $nueva_data;
        return response()->json($output);
    }

    public function saldo_actual($id_producto, $id_posicion)
    {
        $ing = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.cantidad) as ingresos"))
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([
                ['mov_alm_det.id_producto', '=', $id_producto],
                ['mov_alm_det.id_posicion', '=', $id_posicion],
                ['mov_alm.id_tp_mov', '<=', 1], //ingreso o carga inicial
                ['mov_alm_det.estado', '=', 1]
            ])
            ->first();

        $sal = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.cantidad) as salidas"))
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([
                ['mov_alm_det.id_producto', '=', $id_producto],
                ['mov_alm_det.id_posicion', '=', $id_posicion],
                ['mov_alm.id_tp_mov', '=', 2], //salida
                ['mov_alm_det.estado', '=', 1]
            ])
            ->first();

        $saldo = 0;
        if ($ing->ingresos !== null) $saldo += $ing->ingresos;
        if ($sal->salidas !== null) $saldo -= $sal->salidas;

        return $saldo;
    }

    public static function saldo_actual_almacen($id_producto, $id_almacen)
    {
        $ing = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.cantidad) as ingresos"))
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([
                ['mov_alm_det.id_producto', '=', $id_producto],
                ['mov_alm.id_almacen', '=', $id_almacen],
                ['mov_alm.id_tp_mov', '<=', 1], //ingreso o carga inicial
                ['mov_alm_det.estado', '=', 1]
            ])
            ->first();

        $sal = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.cantidad) as salidas"))
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([
                ['mov_alm_det.id_producto', '=', $id_producto],
                ['mov_alm.id_almacen', '=', $id_almacen],
                ['mov_alm.id_tp_mov', '=', 2], //salida
                ['mov_alm_det.estado', '=', 1]
            ])
            ->first();

        $saldo = 0;
        if ($ing->ingresos !== null) $saldo += $ing->ingresos;
        if ($sal->salidas !== null) $saldo -= $sal->salidas;

        return $saldo;
    }

    public function costo_promedio($id_producto, $id_posicion)
    {
        $ing = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.valorizacion) as ingresos"))
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([
                ['mov_alm_det.id_producto', '=', $id_producto],
                ['mov_alm_det.id_posicion', '=', $id_posicion],
                ['id_tp_mov', '<=', 1], //ingreso o carga inicial
                ['mov_alm_det.estado', '=', 1]
            ])
            ->first();

        $sal = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.valorizacion) as salidas"))
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([
                ['mov_alm_det.id_producto', '=', $id_producto],
                ['mov_alm_det.id_posicion', '=', $id_posicion],
                ['id_tp_mov', '=', 2], //salida
                ['mov_alm_det.estado', '=', 1]
            ])
            ->first();

        $valorizacion = 0;
        if ($ing->ingresos !== null) $valorizacion += $ing->ingresos;
        if ($sal->salidas !== null) $valorizacion -= $sal->salidas;

        $saldo = $this->saldo_actual($id_producto, $id_posicion);

        return ($saldo > 0 ? $valorizacion / $saldo : 0);
    }

    public static function valorizacion_almacen($id_producto, $id_almacen)
    {
        $ing = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.valorizacion) as ingresos"))
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([
                ['mov_alm_det.id_producto', '=', $id_producto],
                ['mov_alm.id_almacen', '=', $id_almacen],
                ['mov_alm.id_tp_mov', '<=', 1], //ingreso o carga inicial
                ['mov_alm_det.estado', '=', 1]
            ])
            ->first();

        $sal = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.valorizacion) as salidas"))
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([
                ['mov_alm_det.id_producto', '=', $id_producto],
                ['mov_alm.id_almacen', '=', $id_almacen],
                ['mov_alm.id_tp_mov', '=', 2], //salida
                ['mov_alm_det.estado', '=', 1]
            ])
            ->first();

        $valorizacion = 0;
        if ($ing->ingresos !== null) $valorizacion += $ing->ingresos;
        if ($sal->salidas !== null) $valorizacion -= $sal->salidas;

        return $valorizacion;
    }

    /**Guia de Venta */
    public function listar_guias_venta()
    {
        $data = DB::table('almacen.guia_ven')
            ->select(
                'guia_ven.*',
                'adm_contri.razon_social',
                'adm_estado_doc.estado_doc',
                'sis_usua.usuario as nombre_usuario',
                'tp_ope.descripcion as ope_descripcion',
                'tp_doc_almacen.abreviatura as tp_doc_almacen'
            )
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'guia_ven.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'guia_ven.estado')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'guia_ven.usuario')
            ->join('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'guia_ven.id_operacion')
            ->join('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_ven.id_tp_doc_almacen')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_guia_venta($id)
    {
        $data = DB::table('almacen.guia_ven')
            ->select(
                'guia_ven.*',
                'cliente.razon_social as cliente_razon_social',
                'cliente.id_contribuyente',
                'adm_contri.razon_social',
                'adm_estado_doc.estado_doc',
                'sis_usua.nombre_corto',
                'trans.codigo as codigo_trans',
                'trans.id_transferencia',
                'tp_doc_almacen.abreviatura as tp_doc_almacen'
            )
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'guia_ven.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftjoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'guia_ven.id_cliente')
            ->leftjoin('contabilidad.adm_contri as cliente', 'cliente.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'guia_ven.estado')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'guia_ven.registrado_por')
            ->leftjoin('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_ven.id_tp_doc_almacen')
            ->leftjoin('almacen.trans', 'trans.id_guia_ven', '=', 'guia_ven.id_guia_ven')
            ->where('guia_ven.id_guia_ven', $id)
            ->get();
        return response()->json($data);
    }

    public function guardar_guia_venta(Request $request)
    {
        $usuario = Auth::user();
        $fecha = date('Y-m-d H:i:s');
        $id_guia = DB::table('almacen.guia_ven')->insertGetId(
            [
                'id_tp_doc_almacen' => $request->id_tp_doc_almacen,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_sede' => $request->id_sede,
                'id_cliente' => $request->id_cliente,
                'fecha_emision' => $request->fecha_emision,
                'fecha_almacen' => $request->fecha_almacen,
                'id_almacen' => $request->id_almacen,
                // 'id_motivo' => $request->id_motivo,
                'id_operacion' => $request->id_operacion,
                'transportista' => $request->transportista,
                'tra_serie' => $request->tra_serie,
                'tra_numero' => $request->tra_numero,
                'punto_partida' => $request->punto_partida,
                'punto_llegada' => $request->punto_llegada,
                'fecha_traslado' => $request->fecha_traslado,
                'placa' => $request->placa,
                'usuario' => $request->usuario,
                'registrado_por' => $usuario->id_usuario,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
            'id_guia_ven'
        );

        DB::table('almacen.serie_numero')
            ->where('id_serie_numero', $request->id_serie_numero)
            ->update(['estado' => 8]); //emitido -> 8

        return response()->json(["id_guia_ven" => $id_guia, "id_almacen" => $request->id_almacen]);
    }
    public function update_guia_venta(Request $request)
    {
        $data = DB::table('almacen.guia_ven')
            ->where('id_guia_ven', $request->id_guia_ven)
            ->update([
                'id_tp_doc_almacen' => $request->id_tp_doc_almacen,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_sede' => $request->id_sede,
                'fecha_emision' => $request->fecha_emision,
                'fecha_almacen' => $request->fecha_almacen,
                'id_almacen' => $request->id_almacen,
                'id_motivo' => $request->id_motivo,
                'transportista' => $request->transportista,
                'tra_serie' => $request->tra_serie,
                'tra_numero' => $request->tra_numero,
                'punto_partida' => $request->punto_partida,
                'punto_llegada' => $request->punto_llegada,
                'fecha_traslado' => $request->fecha_traslado,
                'id_cliente' => $request->id_cliente,
                'usuario' => $request->usuario,
                'placa' => $request->placa
            ]);
        return response()->json($data);
    }

    public function anular_guia_venta(Request $request)
    {
        $rspta = '';
        //verifica si ya tiene guia de compra
        $tra = DB::table('almacen.trans')->where([
            ['id_guia_ven', '=', $request->id_guia_ven],
            ['estado', '=', 1]
        ])->first();
        //si ya tiene guia compra -> no puede anular
        if ($tra !== null && $tra->id_guia_com !== null) {
            $rspta = 'No puede anular. La Guia ya generó Ingreso en Almacén Destino \n Debe anular primero el Ingreso.';
        } else {
            $sal = DB::table('almacen.mov_alm')->where([['id_guia_ven', '=', $request->id_guia_ven], ['estado', '=', 1]])->first();
            //verifica si ya existe una salida
            if (isset($sal)) {
                //salida no revisada
                if ($sal->revisado == 0) {
                    //motivo de la anulación
                    $mot = DB::table('almacen.motivo_anu')
                        ->where('id_motivo', $request->id_motivo_obs_ven)
                        ->first();

                    $id_usuario = Auth::user()->id_usuario;
                    $obs = $mot->descripcion . '. ' . $request->observacion_guia_ven;
                    //Agrega observacion a la guia
                    $id_obs = DB::table('almacen.guia_ven_obs')->insertGetId(
                        [
                            'id_guia_ven' => $request->id_guia_ven,
                            'observacion' => $obs,
                            'registrado_por' => $id_usuario,
                            'fecha_registro' => date('Y-m-d H:i:s')
                        ],
                        'id_obs'
                    );
                    //Anula guia venta
                    $data = DB::table('almacen.guia_ven')->where('id_guia_ven', $request->id_guia_ven)
                        ->update(['estado' => 7]);
                    //Anula guia venta detalle
                    DB::table('almacen.guia_ven_det')->where('id_guia_ven', $request->id_guia_ven)
                        ->update(['estado' => 7]);

                    $guia = DB::table('almacen.guia_ven')
                        ->select('guia_ven.*', 'tp_doc_almacen.id_tp_doc')
                        ->where('guia_ven.id_guia_ven', $request->id_guia_ven)
                        ->join('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_ven.id_tp_doc_almacen')
                        ->first();

                    if (isset($guia)) {
                        DB::table('almacen.serie_numero')
                            ->where([
                                ['id_tp_documento', '=', $guia->id_tp_doc],
                                ['serie', '=', $guia->serie],
                                ['numero', '=', $guia->numero]
                            ])
                            ->update(['estado' => 7]);
                    }

                    //Anula salida
                    DB::table('almacen.mov_alm')->where('id_guia_ven', $request->id_guia_ven)
                        ->update(['estado' => 7]);

                    $detalle = DB::table('almacen.guia_ven_det')
                        ->where('id_guia_ven', $request->id_guia_ven)
                        ->get();

                    foreach ($detalle as $det) {
                        //Anula salida detalle
                        DB::table('almacen.mov_alm_det')->where([
                            ['id_guia_ven_det', '=', $det->id_guia_ven_det],
                            ['estado', '=', 1]
                        ])
                            ->update(['estado' => 7]);
                        //Desenlaza guia_ven_det
                        DB::table('almacen.alm_prod_serie')
                            ->where('id_guia_ven_det', $det->id_guia_ven_det)
                            ->update(['id_guia_ven_det' => null]);
                    }
                    if ($tra !== null) {
                        //Anula transferencia
                        DB::table('almacen.trans')
                            ->where([['id_guia_ven', '=', $request->id_guia_ven], ['estado', '=', 1]])
                            ->update(['estado' => 7]);
                    }

                    $rspta = 'Se anuló correctamente.';
                } else {
                    $rspta = 'La salida ya fue revisada por el jefe de almacén. Debe solicitar que se quite el visto.';
                }
            } else {
                //motivo de la anulación
                $mot = DB::table('almacen.motivo_anu')
                    ->where('id_motivo', $request->id_motivo_obs_ven)
                    ->first();

                $id_usuario = Auth::user()->id_usuario;
                $obs = $mot->descripcion . '. ' . $request->observacion_guia_ven;
                //Agrega observacion a la guia
                $id_obs = DB::table('almacen.guia_ven_obs')->insertGetId(
                    [
                        'id_guia_ven' => $request->id_guia_ven,
                        'observacion' => $obs,
                        'registrado_por' => $id_usuario,
                        'fecha_registro' => date('Y-m-d H:i:s')
                    ],
                    'id_obs'
                );
                //Anula guia venta
                $data = DB::table('almacen.guia_ven')->where('id_guia_ven', $request->id_guia_ven)
                    ->update(['estado' => 7]);
                //Anula guia venta detalle
                DB::table('almacen.guia_ven_det')->where('id_guia_ven', $request->id_guia_ven)
                    ->update(['estado' => 7]);

                $guia = DB::table('almacen.guia_ven')
                    ->select('guia_ven.*', 'tp_doc_almacen.id_tp_doc')
                    ->where('guia_ven.id_guia_ven', $request->id_guia_ven)
                    ->join('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_ven.id_tp_doc_almacen')
                    ->first();

                if (isset($guia)) {
                    DB::table('almacen.serie_numero')
                        ->where([
                            ['id_tp_documento', '=', $guia->id_tp_doc],
                            ['serie', '=', $guia->serie],
                            ['numero', '=', $guia->numero]
                        ])
                        ->update(['estado' => 7]);
                }
            }
        }
        return response()->json($rspta);
    }
    public function listar_detalle_doc($id_doc, $tipo, $id_almacen)
    {
        //Guia de Compra
        if ($tipo == 1) {
            $detalle = DB::table('almacen.mov_alm_det')
                ->where([['mov_alm.id_guia_com', '=', $id_doc], ['mov_alm_det.estado', '=', 1]])
                ->select(
                    'mov_alm_det.*',
                    'alm_prod.codigo',
                    'alm_prod.descripcion',
                    'alm_ubi_posicion.codigo as cod_posicion',
                    'alm_und_medida.abreviatura',
                    'guia_com.serie',
                    'guia_com.numero',
                    'mov_alm.codigo as cod_mov'
                )
                ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
                ->join('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'mov_alm_det.id_posicion')
                ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
                ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
                ->join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'mov_alm_det.id_guia_com_det')
                ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
                ->get();

            //Requerimiento
        } else if ($tipo == 2) {
            $detalle = DB::table('almacen.alm_det_req')
                ->where([
                    ['alm_det_req.id_requerimiento', '=', $id_doc],
                    ['alm_det_req.stock_comprometido', '>', 0],
                    ['alm_det_req.estado', '=', 1],
                    ['alm_req.stock_comprometido', '=', true]
                ])
                ->select(
                    'alm_det_req.*',
                    'alm_prod.id_producto',
                    'alm_prod.codigo',
                    'alm_prod.descripcion',
                    'alm_und_medida.abreviatura'
                )
                ->join('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
                ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
                ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
                ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
                ->get();

            //Comprobante de Pago Venta
        } else if ($tipo == 3) {
            $detalle = DB::table('almacen.doc_ven_det')
                ->where([
                    ['doc_ven_det.id_doc', '=', $id_doc],
                    ['doc_ven_det.estado', '=', 1]
                ])
                ->select(
                    'doc_ven_det.id_doc_det',
                    'doc_ven_det.id_item',
                    'doc_ven_det.cantidad',
                    'doc_ven_det.precio_total as valorizacion',
                    'alm_prod.id_producto',
                    'alm_prod.codigo',
                    'alm_prod.descripcion',
                    'alm_und_medida.abreviatura'
                )
                ->join('almacen.alm_item', 'alm_item.id_item', '=', 'doc_ven_det.id_item')
                ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
                ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
                // ->leftjoin('almacen.doc_ven_guia','doc_ven_guia.id_doc_ven','=','doc_ven_det.id_doc')
                ->get();
        }
        $html = '';

        if (isset($detalle)) {
            foreach ($detalle as $d) {
                // $data = DB::table('almacen.mov_alm_det')
                //     ->select('mov_alm_det.*','alm_prod.codigo','alm_prod.descripcion',
                //     'alm_ubi_posicion.codigo as cod_posicion','alm_und_medida.abreviatura')
                //     ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
                //     ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
                //     ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
                //     ->where([['mov_alm_det.id_mov_alm','=',$i->id_mov_alm],
                //             ['mov_alm_det.estado','=',1]])
                //     ->get();

                // foreach($data as $d){
                $posicion = DB::table('almacen.alm_prod_ubi')
                    ->select('alm_prod_ubi.*', 'alm_ubi_posicion.codigo as cod_posicion')
                    ->join('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'alm_prod_ubi.id_posicion')
                    ->join('almacen.alm_ubi_nivel', 'alm_ubi_nivel.id_nivel', '=', 'alm_ubi_posicion.id_nivel')
                    ->join('almacen.alm_ubi_estante', 'alm_ubi_estante.id_estante', '=', 'alm_ubi_nivel.id_estante')
                    ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_ubi_estante.id_almacen')
                    ->where([
                        ['alm_prod_ubi.id_producto', '=', $d->id_producto],
                        ['alm_almacen.id_almacen', '=', $id_almacen],
                        ['alm_prod_ubi.estado', '=', 1]
                    ])
                    ->first();

                $guia = (isset($d->serie) ? ('GR-' . $d->serie . '-' . $d->numero) : '');
                $unit = (isset($d->valorizacion) ? (floatval($d->valorizacion) / floatval($d->cantidad)) : (isset($posicion) ?
                    $this->costo_promedio($d->id_producto, $posicion->id_posicion) : ''));
                $total = (isset($d->valorizacion) ? $d->valorizacion : (floatval($d->cantidad) * floatval($unit)));
                $html .= '
                    <tr>
                        <td><input type="checkbox" checked></td>
                        <td hidden><input name="id" style="display:none;"
                        value="' . (isset($d->id_mov_alm_det) ? ('ing-' . $d->id_mov_alm_det)
                    : (isset($d->id_detalle_requerimiento) ? ('req-' . $d->id_detalle_requerimiento)
                        : (isset($d->id_doc_det) ? ('doc-' . $d->id_doc_det) : ''))) . '"/></td>
                        <td>' . $guia . '</td>
                        <td>' . (isset($d->cod_mov) ? $d->cod_mov : '') . '</td>
                        <td>' . (isset($d->codigo) ? $d->codigo : '') . '</td>
                        <td>' . (isset($d->descripcion) ? $d->descripcion : '') . '</td>
                        <td>' . (isset($d->cod_posicion) ? $d->cod_posicion : (isset($posicion) ? $posicion->cod_posicion : '')) . '</td>
                        <td>' . ($tipo == 2 ? $d->stock_comprometido : (isset($d->cantidad) ? $d->cantidad : '')) . '</td>
                        <td>' . (isset($d->abreviatura) ? $d->abreviatura : '') . '</td>
                        <td>' . $unit . '</td>
                        <td>' . $total . '</td>
                    </tr>
                    ';
                // }
            }
        }
        return json_encode($html);
        // return response()->json($det_ing);
    }
    public function guardar_detalle_ing(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id = explode(',', $request->id);
        $tp = explode(',', $request->tipo);
        $count = count($id);
        $ing_det = '';

        $id_guia_ven = $request->id_guia_ven;
        $id_req = null;
        $id_doc = null;
        $data = 0;

        for ($i = 0; $i < $count; $i++) {
            if ($tp[$i] == "ing") {

                $id_ing = $id[$i];
                $ing_det = DB::table('almacen.mov_alm_det')
                    ->select('mov_alm_det.*', 'alm_prod.id_unidad_medida')
                    ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
                    ->where([['mov_alm_det.id_mov_alm_det', '=', $id_ing]])->first();

                $data = DB::table('almacen.guia_ven_det')->insertGetId(
                    [
                        'id_guia_ven' => $id_guia_ven,
                        'id_producto' => $ing_det->id_producto,
                        'id_posicion' => $ing_det->id_posicion,
                        'cantidad'    => $ing_det->cantidad,
                        'id_unid_med' => $ing_det->id_unidad_medida,
                        'id_ing_det'  => $id_ing,
                        // 'usuario' => 3,
                        'estado'      => 1,
                        'fecha_registro' => $fecha
                    ],
                    'id_guia_ven_det'
                );
            } else if ($tp[$i] == "req") {
                $id_req = $id[$i];
                $req_det = DB::table('almacen.alm_det_req')
                    ->select('alm_det_req.*', 'alm_prod.id_producto', 'alm_prod.id_unidad_medida')
                    ->join('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
                    ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
                    ->where([['alm_det_req.id_detalle_requerimiento', '=', $id_req]])->first();

                $id_posicion = null;
                if ($request->id_almacen !== null) {
                    //jalar posicion relacionada con el producto
                    $posicion = DB::table('almacen.alm_prod_ubi')
                        ->select('alm_ubi_posicion.id_posicion')
                        ->join('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'alm_prod_ubi.id_posicion')
                        ->join('almacen.alm_ubi_nivel', 'alm_ubi_nivel.id_nivel', '=', 'alm_ubi_posicion.id_nivel')
                        ->join('almacen.alm_ubi_estante', 'alm_ubi_estante.id_estante', '=', 'alm_ubi_nivel.id_estante')
                        ->where([
                            ['alm_prod_ubi.id_producto', '=', $req_det->id_producto],
                            ['alm_prod_ubi.estado', '=', 1],
                            ['alm_ubi_estante.id_almacen', '=', $request->id_almacen]
                        ])
                        ->first();
                    if (isset($posicion)) {
                        $id_posicion = $posicion->id_posicion;
                    }
                }

                $data = DB::table('almacen.guia_ven_det')->insertGetId(
                    [
                        'id_guia_ven' => $id_guia_ven,
                        'id_producto' => $req_det->id_producto,
                        'id_posicion' => $id_posicion,
                        'cantidad'    => $req_det->stock_comprometido,
                        'id_unid_med' => $req_det->id_unidad_medida,
                        'id_req_det'  => $id_req,
                        // 'usuario' => 3,
                        'estado'      => 1,
                        'fecha_registro' => $fecha
                    ],
                    'id_guia_ven_det'
                );
                $id_req = $req_det->id_requerimiento;
            } else if ($tp[$i] == "doc") {
                $id_doc = $id[$i];
                $doc_det = DB::table('almacen.doc_ven_det')
                    ->select('doc_ven_det.*', 'alm_prod.id_producto', 'alm_prod.id_unidad_medida')
                    ->join('almacen.alm_item', 'alm_item.id_item', '=', 'doc_ven_det.id_item')
                    ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
                    ->where([['doc_ven_det.id_doc_det', '=', $id_doc]])->first();

                if (isset($doc_det)) {
                    $id_posicion = null;
                    if ($request->id_almacen !== null) {
                        //jalar posicion relacionada con el producto
                        $posicion = DB::table('almacen.alm_prod_ubi')
                            ->select('alm_ubi_posicion.id_posicion')
                            ->join('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'alm_prod_ubi.id_posicion')
                            ->join('almacen.alm_ubi_nivel', 'alm_ubi_nivel.id_nivel', '=', 'alm_ubi_posicion.id_nivel')
                            ->join('almacen.alm_ubi_estante', 'alm_ubi_estante.id_estante', '=', 'alm_ubi_nivel.id_estante')
                            ->where([
                                ['alm_prod_ubi.id_producto', '=', $doc_det->id_producto],
                                ['alm_prod_ubi.estado', '=', 1],
                                ['alm_ubi_estante.id_almacen', '=', $request->id_almacen]
                            ])
                            ->first();
                        if (isset($posicion)) {
                            $id_posicion = $posicion->id_posicion;
                        }
                    }
                    $data = DB::table('almacen.guia_ven_det')->insertGetId(
                        [
                            'id_guia_ven' => $id_guia_ven,
                            'id_producto' => $doc_det->id_producto,
                            'id_posicion' => $id_posicion,
                            'cantidad'    => $doc_det->cantidad,
                            'id_unid_med' => $doc_det->id_unidad_medida,
                            // 'id_req_det'  => $id_req,
                            'estado'      => 1,
                            'fecha_registro' => $fecha
                        ],
                        'id_guia_ven_det'
                    );
                    $id_doc = $doc_det->id_doc;
                }
            }
        }
        if ($id_req !== null) {
            DB::table('almacen.alm_req')->where('id_requerimiento', $id_req)
                ->update(['stock_comprometido' => false]);
        }
        if ($id_doc !== null) {
            DB::table('almacen.doc_ven_guia')->insertGetId(
                [
                    'id_doc_ven' => $id_doc,
                    'id_guia_ven' => $id_guia_ven,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                'id_doc_ven_guia'
            );
        }
        return response()->json($data);
    }
    public function posiciones_sin_producto($id_almacen)
    {
        //listar posiciones que no estan enlazadas con ningun producto
        $posiciones = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_ubi_posicion.id_posicion', 'alm_ubi_posicion.codigo')
            ->leftjoin('almacen.alm_prod_ubi', 'alm_prod_ubi.id_posicion', '=', 'alm_ubi_posicion.id_posicion')
            ->leftjoin('almacen.alm_ubi_nivel', 'alm_ubi_nivel.id_nivel', '=', 'alm_ubi_posicion.id_nivel')
            ->leftjoin('almacen.alm_ubi_estante', 'alm_ubi_estante.id_estante', '=', 'alm_ubi_nivel.id_estante')
            ->leftjoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_ubi_estante.id_almacen')
            ->where([
                ['alm_prod_ubi.id_posicion', '=', null],
                ['alm_ubi_posicion.estado', '=', 1],
                ['alm_almacen.id_almacen', '=', $id_almacen]
            ])
            ->get();
        return response()->json($posiciones);
    }
    public function listar_guia_ven_det($id_guia)
    {
        $guia = DB::table('almacen.guia_ven')->where('id_guia_ven', $id_guia)->first();
        $detalle = DB::table('almacen.guia_ven_det')
            ->select(
                'guia_ven_det.*',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_ubi_posicion.codigo as cod_posicion',
                'mov_alm.codigo as cod_mov',
                'alm_und_medida.abreviatura',
                'mov_alm_det.id_guia_com_det',
                'alm_prod.series',
                'guia_com.serie',
                'guia_com.numero',
                'tp_doc_almacen.abreviatura as tp_doc'
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_ven_det.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftjoin('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'guia_ven_det.id_posicion')
            ->leftjoin('almacen.mov_alm_det', 'mov_alm_det.id_mov_alm_det', '=', 'guia_ven_det.id_ing_det')
            ->leftjoin('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            ->leftjoin('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'mov_alm_det.id_guia_com_det')
            ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->leftjoin('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_com.id_tp_doc_almacen')
            ->where([
                ['guia_ven_det.id_guia_ven', '=', $id_guia],
                ['guia_ven_det.estado', '=', 1]
            ])
            ->get();

        $html = '';
        $chk = '';
        $posiciones = $this->posiciones_sin_producto($guia->id_almacen);

        foreach ($detalle as $d) {
            //jalar posicion relacionada con el producto
            $posicion = DB::table('almacen.alm_prod_ubi')
                ->select('alm_ubi_posicion.id_posicion', 'alm_ubi_posicion.codigo')
                ->join('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'alm_prod_ubi.id_posicion')
                ->join('almacen.alm_ubi_nivel', 'alm_ubi_nivel.id_nivel', '=', 'alm_ubi_posicion.id_nivel')
                ->join('almacen.alm_ubi_estante', 'alm_ubi_estante.id_estante', '=', 'alm_ubi_nivel.id_estante')
                // ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
                ->where([
                    ['alm_prod_ubi.id_producto', '=', $d->id_producto],
                    ['alm_prod_ubi.estado', '=', 1],
                    ['alm_ubi_estante.id_almacen', '=', $guia->id_almacen]
                ])
                ->get();

            $count = count($posicion);
            $o = false;

            if ($count > 0) {
                $posiciones = $posicion;
                $o = true;
            }

            $chk = ($d->series ? 'true' : 'false');
            $series = '';
            $nro_series = 0;

            if ($chk == 'true') {
                $det_series = DB::table('almacen.alm_prod_serie')
                    ->where([
                        ['alm_prod_serie.id_prod', '=', $d->id_producto],
                        ['alm_prod_serie.id_guia_ven_det', '=', $d->id_guia_ven_det],
                        ['alm_prod_serie.estado', '=', 1]
                    ])
                    ->get();

                if (isset($det_series)) {
                    foreach ($det_series as $s) {
                        if ($s->serie !== 'true') {
                            $nro_series++;
                            if ($series !== '') {
                                $series .= ', ' . $s->serie;
                            } else {
                                $series = 'Serie(s): ' . $s->serie;
                            }
                        }
                    }
                }
            }

            $html .= '
            <tr id="reg-' . $d->id_guia_ven_det . '">
                <td>' . ($d->tp_doc !== '' ? $d->tp_doc . '-' . $d->serie . '-' . $d->numero : $d->cod_mov) . '</td>
                <td><input type="text" class="oculto" name="series" value="' . $chk . '"/><input type="number" class="oculto" name="nro_series" value="' . $nro_series . '"/>' . $d->codigo . '</td>
                <td>' . $d->descripcion . ' ' . $series . '</td>
                <td>
                    <select class="input-data" name="id_posicion" disabled="true">
                        <option value="0">Elija una opción</option>';
            // $pos = $this->mostrar_posiciones_cbo();
            if ($o) {
                foreach ($posiciones as $row) {
                    if ($o) {
                        $html .= '<option value="' . $row->id_posicion . '" selected>' . $row->codigo . '</option>';
                    } else {
                        $html .= '<option value="' . $row->id_posicion . '">' . $row->codigo . '</option>';
                    }
                }
            }
            $html .= '</select>
                </td>
                <td><input type="number" name="cantidad" value="' . $d->cantidad . '" class="input-data right" disabled/></td>
                <td>' . $d->abreviatura . '</td>
                <td style="display:flex;">';
            if ($chk == "true") {
                $descripcion = "'" . $d->descripcion . "'";
                $html .= '<i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" title="Agregar Series" onClick="open_series(' . $d->id_guia_ven_det . ',' . $descripcion . ',' . $d->cantidad . ',' . $d->id_producto . ');"></i>';
            }
            $html .= '
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_detalle(' . $d->id_guia_ven_det . ');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_detalle(' . $d->id_guia_ven_det . ');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_detalle(' . $d->id_guia_ven_det . ');"></i>
                </td>
            </tr>
            ';
        }
        return json_encode($html);
    }
    public function guardar_guia_ven_detalle(Request $request)
    {
        $data = DB::table('almacen.guia_ven_det')->insertGetId(
            [
                'id_guia_ven' => $request->id_guia_ven,
                'id_producto' => $request->id_producto,
                'id_posicion' => $request->id_posicion,
                'cantidad' => $request->cantidad,
                'id_unid_med' => $request->id_unid_med,
                // 'unitario' => $request->unitario,
                // 'total' => $request->total,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
            'id_guia_ven_det'
        );
        return response()->json($data);
    }
    public function update_guia_ven_detalle(Request $request)
    {
        $data = DB::table('almacen.guia_ven_det')
            ->where('id_guia_ven_det', $request->id_guia_ven_det)
            ->update([
                'id_posicion' => $request->id_posicion,
                'cantidad' => $request->cantidad,
                // 'unitario' => $request->unitario,
                // 'total' => $request->total,
                // 'id_unid_med' => $request->id_unid_med
            ]);
        return response()->json($data);
    }
    public function anular_guia_ven_detalle(Request $request, $id)
    {
        $det = DB::table('almacen.guia_ven_det')
            ->select('guia_ven_det.*', 'alm_req.id_requerimiento')
            ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'guia_ven_det.id_req_det')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->where('guia_ven_det.id_guia_ven_det', $id)
            ->first();

        $data = DB::table('almacen.guia_ven_det')->where('id_guia_ven_det', $id)
            ->update(['estado' => 7]);

        if (isset($det)) {
            if ($det->id_requerimiento !== null) {
                DB::table('almacen.alm_req')->where('id_requerimiento', $det->id_requerimiento)
                    ->update(['stock_comprometido' => true]);
            }
            if ($det->id_guia_ven !== null) {
                $count = DB::table('almacen.guia_ven_det')
                    ->where([['id_guia_ven', '=', $det->id_guia_ven], ['estado', '=', 1]])
                    ->count();
                if ($count == 0) {
                    DB::table('almacen.doc_ven_guia')
                        ->where('id_guia_ven', $det->id_guia_ven)->delete();
                }
            }
        }

        return response()->json($data);
    }
    public function generar_salida_guia($id_guia)
    {

        $fecha = date('Y-m-d H:i:s');
        $fecha_emision = date('Y-m-d');
        $id_usuario = Auth::user()->id_usuario;

        $guia = DB::table('almacen.guia_ven')
            ->where('id_guia_ven', $id_guia)->first();

        $detalle = DB::table('almacen.guia_ven_det')
            ->select('guia_ven_det.*', 'alm_prod.codigo', 'alm_prod.descripcion')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_ven_det.id_producto')
            ->where([
                ['id_guia_ven', '=', $id_guia],
                ['guia_ven_det.estado', '=', 1]
            ])->get()->toArray();

        $msj = 'No hay saldo en almacén de los siguiente(s) producto(s):';
        $sin_saldo = 0;
        $saldo = null;

        foreach ($detalle as $det) {
            $saldo = $this->saldo_producto($guia->id_almacen, $det->id_producto, $guia->fecha_almacen);
            if ($saldo['saldo'] < floatval($det->cantidad)) {
                $msj .= "\n" . $det->codigo . ' ' . $det->descripcion . ' (saldo actual) = ' . $saldo['saldo'];
                $sin_saldo++;
            }
        }

        if ($sin_saldo > 0) {
            return response()->json([
                'msj' => $msj, 'id_salida' => 0, 'saldo' => $saldo,
                'id_alm' => $guia->id_almacen, 'id_prod' => $det->id_producto, 'fecha' => $guia->fecha_almacen
            ]);
        } else {
            $codigo = AlmacenController::nextMovimiento(
                2,
                $guia->fecha_almacen,
                $guia->id_almacen
            );

            $id_salida = DB::table('almacen.mov_alm')->insertGetId(
                [
                    'id_almacen' => $guia->id_almacen,
                    'id_tp_mov' => 2, //salidas
                    'codigo' => $codigo,
                    'fecha_emision' => $guia->fecha_almacen,
                    'id_guia_ven' => $id_guia,
                    'id_operacion' => $guia->id_operacion,
                    'revisado' => 0,
                    'usuario' => $id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                'id_mov_alm'
            );
            $nuevo_detalle = [];
            $cant = 0;

            // foreach ($detalle as $det){
            //     $exist = false;
            //     foreach ($nuevo_detalle as $nue => $value){
            //         if ($det->id_producto == $value['id_producto']){
            //             $nuevo_detalle[$nue]['cantidad'] = floatval($value['cantidad']) + floatval($det->cantidad);
            //             // $nuevo_detalle[$nue]['valorizacion'] = floatval($value['valorizacion']) + floatval($det->total);
            //             $exist = true;
            //         }
            //     }
            //     if ($exist === false){
            //         $nuevo = [
            //             'id_producto' => $det->id_producto,
            //             'id_posicion' => $det->id_posicion,
            //             // 'id_oc_det' => (isset($det->id_oc_det)) ? $det->id_oc_det : 0,
            //             'cantidad' => floatval($det->cantidad)
            //             // 'valorizacion' => floatval($det->total)
            //             ];
            //         array_push($nuevo_detalle, $nuevo);
            //     }
            // }

            foreach ($detalle as $det) {
                $costo = $this->costo_promedio($det->id_producto, $det->id_posicion);
                $valorizacion = $costo * $det->cantidad;

                $id_det = DB::table('almacen.mov_alm_det')->insertGetId(
                    [
                        'id_mov_alm' => $id_salida,
                        'id_producto' => $det->id_producto,
                        'id_posicion' => $det->id_posicion,
                        'cantidad' => $det->cantidad,
                        'valorizacion' => $valorizacion,
                        'id_guia_ven_det' => $det->id_guia_ven_det,
                        'usuario' => $id_usuario,
                        'estado' => 1,
                        'fecha_registro' => $fecha,
                    ],
                    'id_mov_alm_det'
                );

                if ($det->id_posicion !== null) {
                    $ubi = DB::table('almacen.alm_prod_ubi')
                        ->where([
                            ['id_producto', '=', $det->id_producto],
                            ['id_posicion', '=', $det->id_posicion]
                        ])
                        ->first();
                    //traer stockActual
                    $saldo = $this->saldo_actual($det->id_producto, $det->id_posicion);
                    $costo = $this->costo_promedio($det->id_producto, $det->id_posicion);

                    if (!isset($ubi->id_posicion)) {
                        DB::table('almacen.alm_prod_ubi')->insert([
                            'id_producto' => $det->id_producto,
                            'id_posicion' => $det->id_posicion,
                            'stock' => $saldo,
                            'costo_promedio' => $costo,
                            'estado' => 1,
                            'fecha_registro' => $fecha
                        ]);
                    } else {
                        DB::table('almacen.alm_prod_ubi')
                            ->where('id_prod_ubi', $ubi->id_prod_ubi)
                            ->update([
                                'stock' => $saldo,
                                'costo_promedio' => $costo
                            ]);
                    }
                }
            }
            // cambiar estado guiaven
            DB::table('almacen.guia_ven')
                ->where('id_guia_ven', $id_guia)->update(['estado' => 9]); //Procesado

            return response()->json(['msj' => '', 'id_salida' => $id_salida]);
        }
    }

    /**Comprobante de Venta */
    public function listar_docs_venta()
    {
        $data = DB::table('almacen.doc_ven')
            ->select('doc_ven.*', 'adm_contri.razon_social', 'adm_estado_doc.estado_doc')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'doc_ven.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'doc_ven.estado')
            // ->where('doc_ven.estado',1)
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_doc_venta($id)
    {
        $doc = DB::table('almacen.doc_ven')
            ->select(
                'doc_ven.*',
                'adm_estado_doc.estado_doc',
                'sis_usua.nombre_corto',
                'adm_contri.razon_social',
                'adm_contri.id_contribuyente'
            )
            // DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_usuario"))
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'doc_ven.estado')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'doc_ven.usuario')
            ->join('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'doc_ven.id_cliente')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->where('doc_ven.id_doc_ven', $id)
            ->get();
        return response()->json(['doc' => $doc]);
    }
    public function guardar_doc_venta(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_usuario = Auth::user()->id_usuario;
        $id_doc = DB::table('almacen.doc_ven')->insertGetId(
            [
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_tp_doc' => $request->id_tp_doc,
                'id_sede' => $request->id_sede,
                'id_condicion' => $request->id_condicion,
                'credito_dias' => $request->credito_dias,
                'id_cliente' => $request->id_cliente,
                'fecha_emision' => $request->fecha_emision,
                'fecha_vcmto' => $request->fecha_vcmto,
                'moneda' => $request->moneda,
                'tipo_cambio' => $request->tipo_cambio,
                'sub_total' => ($request->sub_total !== null ? $request->sub_total : 0),
                'total_descuento' => ($request->total_descuento !== null ? $request->total_descuento : 0),
                'porcen_descuento' => ($request->porcen_descuento !== null ? $request->porcen_descuento : 0),
                'total' => ($request->total !== null ? $request->total : 0),
                'total_igv' => ($request->total_igv !== null ? $request->total_igv : 0),
                'total_ant_igv' => ($request->total_ant_igv !== null ? $request->total_ant_igv : 0),
                'total_a_pagar' => ($request->total_a_pagar !== null ? $request->total_a_pagar : 0),
                'usuario' => $id_usuario,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
            'id_doc_ven'
        );

        DB::table('almacen.serie_numero')
            ->where('id_serie_numero', $request->id_serie_numero)
            ->update(['estado' => 8]); //emitido -> 8

        return response()->json($id_doc);
    }
    public function update_doc_venta(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $data = DB::table('almacen.doc_ven')
            ->where('id_doc_ven', $request->id_doc_ven)
            ->update([
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_tp_doc' => $request->id_tp_doc,
                'id_sede' => $request->id_sede,
                'id_condicion' => $request->id_condicion,
                'credito_dias' => $request->credito_dias,
                'id_cliente' => $request->id_cliente,
                'fecha_emision' => $request->fecha_emision,
                'fecha_vcmto' => $request->fecha_vcmto,
                'moneda' => $request->moneda,
                'tipo_cambio' => $request->tipo_cambio,
                'sub_total' => ($request->sub_total !== null ? $request->sub_total : 0),
                'total_descuento' => ($request->total_descuento !== null ? $request->total_descuento : 0),
                'porcen_descuento' => ($request->porcen_descuento !== null ? $request->porcen_descuento : 0),
                'total' => ($request->total !== null ? $request->total : 0),
                'total_igv' => ($request->total_igv !== null ? $request->total_igv : 0),
                'total_ant_igv' => ($request->total_ant_igv !== null ? $request->total_ant_igv : 0),
                'total_a_pagar' => ($request->total_a_pagar !== null ? $request->total_a_pagar : 0)
            ]);
        return response()->json($data);
    }
    public function anular_doc_venta($id)
    {
        $data = DB::table('almacen.doc_ven')->where('id_doc_ven', $id)
            ->update(['estado' => 7]);
        return response()->json($data);
    }
    public function guardar_docven_detalle(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_usuario = Auth::user()->id_usuario;
        $data = 0;

        $item = DB::table('almacen.alm_item')
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->where('alm_item.id_producto', $request->id_producto)
            ->first();

        if (isset($item)) {
            $data = DB::table('almacen.doc_ven_det')->insertGetId(
                [
                    'id_doc' => $request->id_doc,
                    'id_item' => $item->id_item,
                    'cantidad' => $request->cantidad,
                    'id_unid_med' => $request->id_unid_med,
                    'precio_unitario' => $request->precio_unitario,
                    'sub_total' => $request->sub_total,
                    'porcen_dscto' => 0,
                    'total_dscto' => 0,
                    'precio_total' => $request->sub_total,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                'id_doc_det'
            );
        }
        return response()->json($data);
    }
    public function update_docven_detalle(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $data = DB::table('almacen.doc_ven_det')
            ->where('id_doc_det', $request->id_doc_det)
            ->update([
                'cantidad' => $request->cantidad,
                'precio_unitario' => $request->precio_unitario,
                'porcen_dscto' => $request->porcen_dscto,
                'total_dscto' => $request->total_dscto,
                'precio_total' => $request->precio_total,
            ]);
        return response()->json($data);
    }
    public function anular_docven_detalle($id_doc_det)
    {
        $data = DB::table('almacen.doc_ven_det')
            ->where('id_doc_det', $id_doc_det)
            ->update(['estado' => 7]);
        return response()->json($data);
    }
    public function listar_guias_emp($id_empresa)
    {
        $data = DB::table('almacen.guia_ven')
            ->select('guia_ven.*', 'adm_contri.razon_social', 'adm_estado_doc.estado_doc')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'guia_ven.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'guia_ven.estado')
            ->where('adm_empresa.id_empresa', $id_empresa)
            ->get();
        return response()->json($data);
    }
    public function docven($id_guia, $id_doc)
    {
        $detalle = DB::table('almacen.guia_ven_det')
            ->select('guia_ven_det.*', DB::raw('(mov_alm_det.valorizacion / mov_alm_det.cantidad) as precio_unitario')) //jalar el precio unitario del ingreso
            ->leftjoin('almacen.mov_alm_det', 'mov_alm_det.id_mov_alm', '=', 'guia_ven_det.id_ing_det')
            ->where([
                ['guia_ven_det.id_guia_ven', '=', $id_guia],
                ['guia_ven_det.estado', '=', 1]
            ])
            ->get();
        return $detalle;
    }
    public function listar_doc_ven($id_sede, $id_cliente)
    {
        $data = DB::table('almacen.doc_ven')
            ->select('doc_ven.*', 'adm_contri.razon_social', 'cont_tp_doc.abreviatura')
            ->join('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'doc_ven.id_cliente')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->join('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_ven.id_tp_doc')
            ->leftjoin('almacen.doc_ven_guia', 'doc_ven_guia.id_doc_ven', '=', 'doc_ven.id_doc_ven')
            ->where([
                ['doc_ven.id_sede', '=', $id_sede],
                ['doc_ven.id_cliente', '=', $id_cliente],
                ['doc_ven_guia.id_doc_ven', '=', null],
                ['doc_ven.estado', '=', 1]
            ])
            ->get();
        return response()->json($data);
    }
    public function guardar_docven_items_guia($id_guia, $id_doc)
    {
        $fecha = date('Y-m-d H:i:s');
        $detalle = DB::table('almacen.guia_ven_det')
            ->select('guia_ven_det.*', DB::raw('(mov_alm_det.valorizacion / mov_alm_det.cantidad) as precio_unitario')) //jalar el precio unitario del ingreso
            ->leftjoin('almacen.mov_alm_det', 'mov_alm_det.id_mov_alm', '=', 'guia_ven_det.id_ing_det')
            ->where([
                ['guia_ven_det.id_guia_ven', '=', $id_guia],
                ['guia_ven_det.estado', '=', 1]
            ])
            ->get();
        $nuevo_detalle = [];
        $cant = 0;

        foreach ($detalle as $det) {
            $exist = false;
            foreach ($nuevo_detalle as $nue => $value) {
                if ($det->id_producto == $value['id_producto'] && $det->id_guia_ven == $value['id_guia_ven']) {
                    $nuevo_detalle[$nue]['cantidad'] = floatval($value['cantidad']) + floatval($det->cantidad);
                    $nuevo_detalle[$nue]['precio_unitario'] = floatval($value['precio_unitario']) + floatval($det->precio_unitario);
                    // $nuevo_detalle[$nue]['precio_total'] = floatval($value['precio_total']) + floatval($det->precio_total);
                    $exist = true;
                }
            }
            if ($exist === false) {
                $nuevo = [
                    'id_guia_ven_det' => $det->id_guia_ven_det,
                    'id_guia_ven' => $det->id_guia_ven,
                    'id_producto' => $det->id_producto,
                    'id_unid_med' => $det->id_unid_med,
                    'cantidad' => floatval($det->cantidad),
                    'precio_unitario' => floatval($det->precio_unitario),
                    'precio_total' => floatval($det->cantidad * $det->precio_unitario)
                ];
                array_push($nuevo_detalle, $nuevo);
            }
        }
        foreach ($nuevo_detalle as $det) {
            $item = DB::table('almacen.alm_item')
                ->where('id_producto', $det['id_producto'])
                ->first();

            $id_det = DB::table('almacen.doc_ven_det')->insert(
                [
                    'id_doc' => $id_doc,
                    'id_item' => $item->id_item,
                    'cantidad' => $det['cantidad'],
                    'id_unid_med' => $det['id_unid_med'],
                    'precio_unitario' => $det['precio_unitario'],
                    'sub_total' => $det['precio_total'],
                    'porcen_dscto' => 0,
                    'total_dscto' => 0,
                    'precio_total' => $det['precio_total'],
                    'id_guia_ven_det' => $det['id_guia_ven_det'],
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ]
            );
        }
        $guia = DB::table('almacen.doc_ven_guia')->insert(
            [
                'id_doc_ven' => $id_doc,
                'id_guia_ven' => $id_guia,
                'estado' => 1,
                'fecha_registro' => $fecha
            ]
        );
        $salida = DB::table('almacen.mov_alm')
            ->where('mov_alm.id_guia_ven', $id_guia)
            ->first();

        if (isset($salida->id_mov_alm)) {
            DB::table('almacen.mov_alm')
                ->where('id_mov_alm', $salida->id_mov_alm)
                ->update(['id_doc_ven' => $id_doc]);
        }

        return response()->json($guia);
    }

    public function update_series(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $ids = explode(',', $request->ids);
        $anulados = explode(',', $request->anulados);
        $count = count($ids);
        $count_anu = count($anulados);

        if ($count_anu > 0) {
            for ($i = 0; $i < $count_anu; $i++) {
                $id_anu = $anulados[$i];
                if ($id_anu !== '') {
                    $update = DB::table('almacen.alm_prod_serie')
                        ->where('id_prod_serie', $id_anu)
                        ->update(['id_guia_ven_det' => null]);
                }
            }
        }

        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $id = $ids[$i];
                if ($id !== null) {
                    $update = DB::table('almacen.alm_prod_serie')
                        ->where('id_prod_serie', $id)
                        ->update(['id_guia_ven_det' => $request->id_guia_ven_det]);
                }
            }
        }
        return response()->json($count_anu);
    }

    public function saldo_producto($id_almacen, $id_producto, $fecha)
    {
        $saldo = 0;

        $ingresos = DB::table('almacen.mov_alm_det')
            ->select(
                DB::raw('SUM(mov_alm_det.cantidad) as cant_ingresos'),
                DB::raw('SUM(mov_alm_det.valorizacion) as val_ingresos')
            )
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            ->join('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'mov_alm_det.id_posicion')
            ->join('almacen.alm_ubi_nivel', 'alm_ubi_nivel.id_nivel', '=', 'alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante', 'alm_ubi_estante.id_estante', '=', 'alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_ubi_estante.id_almacen')
            ->where([
                ['mov_alm_det.id_producto', '=', $id_producto],
                ['mov_alm_det.estado', '=', 1],
                ['mov_alm.fecha_emision', '<=', $fecha],
                ['alm_almacen.id_almacen', '=', $id_almacen],
                ['mov_alm.id_tp_mov', '=', 1]
            ])
            ->first();

        $salidas = DB::table('almacen.mov_alm_det')
            ->select(
                DB::raw('SUM(mov_alm_det.cantidad) as cant_salidas'),
                DB::raw('SUM(mov_alm_det.valorizacion) as val_salidas')
            )
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            ->join('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'mov_alm_det.id_posicion')
            ->join('almacen.alm_ubi_nivel', 'alm_ubi_nivel.id_nivel', '=', 'alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante', 'alm_ubi_estante.id_estante', '=', 'alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_ubi_estante.id_almacen')
            ->where([
                ['mov_alm_det.id_producto', '=', $id_producto],
                ['mov_alm_det.estado', '=', 1],
                ['mov_alm.fecha_emision', '<=', $fecha],
                ['alm_almacen.id_almacen', '=', $id_almacen],
                ['mov_alm.id_tp_mov', '=', 2]
            ])
            ->first();

        $saldo = $ingresos->cant_ingresos - $salidas->cant_salidas;
        $valorizacion = $ingresos->val_ingresos - $salidas->val_salidas;

        return ['saldo' => $saldo, 'valorizacion' => $valorizacion];
    }
    public function movimientos_producto($id_almacen, $id_producto, $finicio, $ffin)
    {
        $detalle = DB::table('almacen.mov_alm_det')
            ->select(
                'mov_alm_det.*',
                'mov_alm.fecha_emision',
                'mov_alm.id_tp_mov as tipo',
                // 'alm_prod.descripcion as prod_descripcion','alm_prod.codigo as prod_codigo',
                // 'alm_und_medida.abreviatura','alm_ubi_posicion.codigo as posicion',
                // 'tp_mov.tp_mov','tp_mov.tipo',
                'guia_com.fecha_emision as guia_com_fecha',
                'guia_com.serie as guia_com_serie',
                'guia_com.numero as guia_com_numero',
                'tp_doc_com.cod_sunat as cod_sunat_com',
                'doc_com.serie as doc_com_serie',
                'doc_com.numero as doc_com_numero',
                'doc_com.fecha_emision as doc_com_fecha',
                'guia_ven.fecha_emision as guia_ven_fecha',
                'guia_ven.serie as guia_ven_serie',
                'guia_ven.numero as guia_ven_numero',
                'tp_doc_ven.cod_sunat as cod_sunat_ven',
                'doc_ven.serie as doc_ven_serie',
                'doc_ven.numero as doc_ven_numero',
                'doc_ven.fecha_emision as doc_ven_fecha',
                'tp_op_com.cod_sunat as op_sunat_ing',
                'tp_op_ven.cod_sunat as op_sunat_sal',
                'tp_cod_sunat_com.cod_sunat as doc_sunat_com',
                'tp_cod_sunat_ven.cod_sunat as doc_sunat_ven'
            )
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftJoin('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'mov_alm_det.id_posicion')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->leftJoin('almacen.guia_com', 'guia_com.id_guia', '=', 'mov_alm.id_guia_com')
            ->leftJoin('almacen.tp_doc_almacen as doc_com_sunat', 'doc_com_sunat.id_tp_doc', '=', 'guia_com.id_tp_doc_almacen')
            ->leftJoin('contabilidad.cont_tp_doc as tp_cod_sunat_com', 'tp_cod_sunat_com.id_tp_doc', '=', 'doc_com_sunat.id_tp_doc')
            ->leftJoin('almacen.tp_ope as tp_op_com', 'tp_op_com.id_operacion', '=', 'guia_com.id_operacion')
            ->leftJoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'mov_alm.id_doc_com')
            ->leftJoin('contabilidad.cont_tp_doc as tp_doc_com', 'tp_doc_com.id_tp_doc', '=', 'doc_com.id_tp_doc')
            ->leftJoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'mov_alm.id_guia_ven')
            ->leftJoin('almacen.tp_doc_almacen as doc_ven_sunat', 'doc_ven_sunat.id_tp_doc', '=', 'guia_ven.id_tp_doc_almacen')
            ->leftJoin('contabilidad.cont_tp_doc as tp_cod_sunat_ven', 'tp_cod_sunat_ven.id_tp_doc', '=', 'doc_ven_sunat.id_tp_doc')
            ->leftJoin('almacen.tp_ope as tp_op_ven', 'tp_op_ven.id_operacion', '=', 'guia_ven.id_operacion')
            ->leftJoin('almacen.doc_ven', 'doc_ven.id_doc_ven', '=', 'mov_alm.id_doc_ven')
            ->leftJoin('contabilidad.cont_tp_doc as tp_doc_ven', 'tp_doc_ven.id_tp_doc', '=', 'doc_ven.id_tp_doc')
            // ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','mov_alm.id_req')
            ->where([
                ['mov_alm.id_almacen', '=', $id_almacen],
                ['mov_alm_det.id_producto', '=', $id_producto],
                ['mov_alm.fecha_emision', '>=', $finicio],
                ['mov_alm.fecha_emision', '<=', $ffin],
                ['mov_alm_det.estado', '=', 1]
            ])
            // ->orderBy('alm_prod.descripcion','asc')
            ->orderBy('mov_alm.fecha_emision', 'asc')
            ->orderBy('mov_alm.id_tp_mov', 'asc')
            ->get();
        return $detalle;
    }
    public function kardex_sunat($almacenes, $finicio, $ffin)
    {
        $html = '
        <html>
            <head>
                <style type="text/css">
                *{
                    font-family: "DejaVu Sans";
                }
                table{
                    width:100%;
                    font-size:12px;
                }
                #detalle thead{
                    padding: 4px;
                    background-color: #e5e5e5;
                }
                #detalle thead tr td{
                    padding: 4px;
                    background-color: #ddd;
                }
                #detalle tbody tr td{
                    font-size:11px;
                    padding: 4px;
                }
                .right{
                    text-align: right;
                }
                .left{
                    text-align: left;
                }
                .sup{
                    vertical-align:top;
                }
                </style>
            </head>
            <body>
                <h3 style="margin:0px;"><center>REGISTRO DE INVENTARIO PERMANENTE VALORIZADO - DETALLE DEL INVENTARIO VALORIZADO</center></h3>';

        $alm_array = explode(',', $almacenes);
        $count = count($alm_array);
        $mes = array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SETIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');

        $mes_inicio = $mes[(date('m', strtotime($finicio)) * 1) - 1];
        $yyyy_inicio = date('Y', strtotime($finicio));
        $mes_fin = $mes[(date('m', strtotime($ffin)) * 1) - 1];
        $yyyy_fin = date('Y', strtotime($ffin));

        for ($i = 0; $i < $count; $i++) {
            $id_almacen = $alm_array[$i];
            $alm = DB::table('almacen.alm_almacen')
                ->select('alm_almacen.*', 'adm_contri.razon_social', 'adm_contri.nro_documento')
                ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
                ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
                ->where('id_almacen', $id_almacen)->first();

            $html .= '
                    <table id="detalle" border="0" class="table table-condensed table-bordered table-hover sortable" width="100%">
                    <thead>
                        <tr>
                            <th class="left">Periodo:</th>
                            <th class="left">' . $mes_inicio . ' ' . $yyyy_inicio . ' - ' . $mes_fin . ' ' . $yyyy_fin . '</th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th>
                        </tr>
                        <tr>
                            <th class="left">R.U.C.:</th>
                            <th class="left">' . $alm->nro_documento . '</th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th>
                        </tr>
                        <tr>
                            <th class="left">Razon Social:</th>
                            <th class="left">' . $alm->razon_social . '</th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th>
                        </tr>
                        <tr>
                            <th class="left">Establecimiento:</th>
                            <th class="left">' . $alm->ubicacion . '</th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th>
                        </tr>
                        <tr>
                            <th class="left">Metodo Valuación:</th>
                            <th class="left">PROMEDIO PONDERADO</th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th>
                        </tr>
                        <tr>
                            <td rowspan="2"></td>
                            <td rowspan="2"></td>
                            <td rowspan="2">Fecha</td>
                            <td rowspan="2">Tipo</td>
                            <td rowspan="2">Serie</td>
                            <td rowspan="2">Numero</td>
                            <td rowspan="2">Fecha</td>
                            <td rowspan="2">Tipo</td>
                            <td rowspan="2">Serie</td>
                            <td rowspan="2">Numero</td>
                            <td rowspan="2">Tp.Ope</td>
                            <td colspan="3"><center>Entradas</center></td>
                            <td colspan="3"><center>Salidas</center></td>
                            <td colspan="3"><center>Saldo Final</center></td>
                        </tr>
                        <tr>
                            <td>Cantidad</td>
                            <td>Costo Unit.</td>
                            <td>Costo Total</td>
                            <td>Cantidad</td>
                            <td>Costo Unit.</td>
                            <td>Costo Total</td>
                            <td>Cantidad</td>
                            <td>Costo Unit.</td>
                            <td>Costo Total</td>
                        </tr>
                    </thead>
                    <tbody>';

            $productos = DB::table('almacen.alm_prod_ubi')
                ->select(
                    'alm_prod_ubi.*',
                    'alm_prod.codigo as prod_codigo',
                    'alm_prod.descripcion as prod_descripcion',
                    'alm_und_medida.abreviatura'
                )
                ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_prod_ubi.id_producto')
                ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
                ->leftJoin('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'alm_prod_ubi.id_posicion')
                // ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
                // ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
                ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_prod_ubi.id_almacen')
                ->where([
                    ['alm_prod_ubi.estado', '=', 1],
                    ['alm_almacen.id_almacen', '=', $id_almacen]
                ])
                ->get();


            foreach ($productos as $prod) {
                $detalle = $this->movimientos_producto($id_almacen, $prod->id_producto, $finicio, $ffin);
                $size = count($detalle);

                if ($size > 0) {

                    $html .= '
                                <tr>
                                    <td>Código de Existencia:</td>
                                    <td>01 MERCADERIAS</td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                </tr>
                                <tr>
                                    <td>Descripción:</td>
                                    <td>' . $prod->prod_codigo . ' ' . $prod->prod_descripcion . '</td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                </tr>
                                <tr>
                                    <td>Codigo de Unidad:</td>
                                    <td>' . $prod->abreviatura . '</td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                </tr>';

                    $saldo = 0;
                    $saldo_valor = 0;
                    $total_ing = 0;
                    $total_sal = 0;
                    $cant_ing = 0;
                    $cant_sal = 0;
                    $stock_inicial = false;
                    $costo_promedio = 0;
                    $valor_salida = 0;

                    foreach ($detalle as $det) {
                        if ($det->tipo == 1 || $det->tipo == 0) {
                            $saldo += $det->cantidad;
                            $saldo_valor += $det->valorizacion;
                        } else if ($det->tipo == 2) {
                            $saldo -= $det->cantidad;
                            // $saldo_valor -= $det->valorizacion;
                            $valor_salida = $costo_promedio * $det->cantidad;
                            $saldo_valor -= $valor_salida;
                        }
                        if ($saldo !== 0) {
                            $costo_promedio = ($saldo == 0 ? 0 : $saldo_valor / $saldo);
                        }
                        if ($det->tipo == 0) {
                            $total_ing += floatval($det->valorizacion);
                            $cant_ing += floatval($det->cantidad);
                            $html .= '  <tr>
                                <td></td>
                                <td></td>
                                <td></td><td></td><td></td><td></td><td></td><td></td>
                                <td></td><td></td><td></td><td></td><td></td><td></td>
                                <td></td><td></td>
                                <td>Stock Inicial:</td>
                                <td class="right" style="mso-number-format:"0.00";">' . $saldo . '</td>
                                <td class="right" style="mso-number-format:"0.00";">' . ($saldo !== 0 ? ($saldo_valor / $saldo) : 0) . '</td>
                                <td class="right" style="mso-number-format:"0.00";">' . $saldo_valor . '</td>
                            </tr>';
                        } else if ($det->tipo == 1) {
                            $total_ing += floatval($det->valorizacion);
                            $cant_ing += floatval($det->cantidad);
                            $unitario = floatval($det->valorizacion) / floatval($det->cantidad);
                            $saldo_unitario = $saldo !== 0 ? ($saldo_valor / $saldo) : 0;

                            $html .= '
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>' . $det->doc_com_fecha . '</td>
                                            <td>' . $det->cod_sunat_com . '</td>
                                            <td>' . $det->doc_com_serie . '</td>
                                            <td>' . $det->doc_com_numero . '</td>
                                            <td>' . $det->guia_com_fecha . '</td>
                                            <td>' . $det->doc_sunat_com . '</td>
                                            <td>' . $det->guia_com_serie . '</td>
                                            <td>' . $det->guia_com_numero . '</td>
                                            <td>' . $det->op_sunat_ing . '</td>
                                            <td class="right" style="mso-number-format:"0.00";">' . number_format($det->cantidad, 2, ".", ",") . '</td>
                                            <td class="right" style="mso-number-format:"0.00";">' . number_format($unitario, 3, ".", ",") . '</td>
                                            <td class="right" style="mso-number-format:"0.00";">' . number_format($det->valorizacion, 3, ".", ",") . '</td>
                                            <td class="right">0</td>
                                            <td class="right">0</td>
                                            <td class="right">0</td>
                                            <td class="right" style="mso-number-format:"0.00";">' . number_format($saldo, 2, ".", ",") . '</td>
                                            <td class="right" style="mso-number-format:"0.00";">' . number_format($saldo_unitario, 3, ".", ",") . '</td>
                                            <td class="right" style="mso-number-format:"0.00";">' . number_format($saldo_valor, 3, ".", ",") . '</td>
                                        </tr>';
                        } else if ($det->tipo == 2) {
                            $total_sal += floatval($valor_salida);
                            $cant_sal += floatval($det->cantidad);
                            $unitario = floatval($det->valorizacion) / floatval($det->cantidad);
                            $saldo_unitario = $saldo !== 0 ? ($saldo_valor / $saldo) : 0;

                            $html .= '
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>' . $det->doc_ven_fecha . '</td>
                                            <td>' . $det->cod_sunat_ven . '</td>
                                            <td>' . $det->doc_ven_serie . '</td>
                                            <td>' . $det->doc_ven_numero . '</td>
                                            <td>' . $det->guia_ven_fecha . '</td>
                                            <td>' . $det->doc_sunat_ven . '</td>
                                            <td>' . $det->guia_ven_serie . '</td>
                                            <td>' . $det->guia_ven_numero . '</td>
                                            <td>' . $det->op_sunat_sal . '</td>
                                            <td class="right">0</td>
                                            <td class="right">0</td>
                                            <td class="right">0</td>
                                            <td class="right" style="mso-number-format:"0.00";">' . floatval($det->cantidad) . '</td>
                                            <td class="right" style="mso-number-format:"0.00";">' . number_format($costo_promedio, 3, ".", ",") . '</td>
                                            <td class="right" style="mso-number-format:"0.00";">' . number_format($valor_salida, 3, ".", ",") . '</td>
                                            <td class="right" style="mso-number-format:"0.00";">' . number_format($saldo, 2, ".", ",") . '</td>
                                            <td class="right" style="mso-number-format:"0.00";">' . number_format($saldo_unitario, 3, ".", ",") . '</td>
                                            <td class="right" style="mso-number-format:"0.00";">' . number_format($saldo_valor, 3, ".", ",") . '</td>
                                        </tr>';
                        }
                        // $codigo = $det->prod_codigo;
                    }
                    $html .= '
                                <tr>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td>
                                    <td><strong>Total:</strong></td>
                                    <td class="right"><strong>' . $cant_ing . '</strong></td><td></td>
                                    <td class="right"><strong>' . number_format($total_ing, 2, ".", ",") . '</strong></td>
                                    <td class="right"><strong>' . $cant_sal . '</strong></td><td></td>
                                    <td class="right"><strong>' . number_format($total_sal, 2, ".", ",") . '</strong></td><td></td><td></td><td></td>
                                </tr>';
                }
            }
            $html .= '
                        </tbody>
                    </table>';
        }
        $html .= '
            </body>
        </html>';

        return $html;
        // return $detalle;
    }
    public function download_kardex_sunat($almacenes, $finicio, $ffin)
    {
        $data = $this->kardex_sunat($almacenes, $finicio, $ffin);
        return view('almacen/reportes/kardex_sunat_excel', compact('data'));
    }
    public function direccion_almacen($id_almacen)
    {
        $alm = DB::table('almacen.alm_almacen')
            ->where('id_almacen', $id_almacen)
            ->first();
        $data = $alm->ubicacion;
        return response()->json($data);
    }

    public function listar_tp_docs()
    {
        $data = DB::table('almacen.tp_doc_almacen')
            ->select('tp_doc_almacen.*', 'cont_tp_doc.cod_sunat')
            ->leftjoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'tp_doc_almacen.id_tp_doc')
            ->where('tp_doc_almacen.estado', 1)->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_tp_doc($id)
    {
        $data = DB::table('almacen.tp_doc_almacen')
            ->where('id_tp_doc_almacen', $id)
            ->get();
        return response()->json($data);
    }

    public function guardar_tp_doc(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_tp_doc = DB::table('almacen.tp_doc_almacen')->insertGetId(
            [
                'descripcion' => $request->descripcion,
                'id_tp_doc' => $request->id_tp_doc,
                'tipo' => $request->tipo,
                'abreviatura' => $request->abreviatura,
                'estado' => 1,
                'usuario' => $request->usuario,
                'fecha_registro' => $fecha
            ],
            'id_tp_doc_almacen'
        );
        return response()->json($id_tp_doc);
    }

    public function update_tp_doc(Request $request)
    {
        $data = DB::table('almacen.tp_doc_almacen')
            ->where('id_tp_doc_almacen', $request->id_tp_doc_almacen)
            ->update([
                'descripcion' => $request->descripcion,
                'id_tp_doc' => $request->id_tp_doc,
                'tipo' => $request->tipo,
                'abreviatura' => $request->abreviatura
            ]);
        return response()->json($data);
    }

    public function anular_tp_doc(Request $request, $id)
    {
        $data = DB::table('almacen.tp_doc_almacen')
            ->where('id_tp_doc_almacen', $id)
            ->update(['estado' => 7]);
        return response()->json($data);
    }
    public function listar_ocs()
    {
        $data = DB::table('logistica.log_ord_compra')
            ->select('log_ord_compra.*', 'adm_contri.razon_social')
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->where([
                ['log_ord_compra.estado', '!=', 7],
                ['log_ord_compra.en_almacen', '=', false],
                ['log_ord_compra.id_tp_documento', '=', 2]
            ])
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function listar_kardex_producto($id_producto, $almacen, $finicio, $ffin)
    {
        $html = '';
        $data = DB::table('almacen.mov_alm_det')
            ->select(
                'mov_alm_det.*',
                'alm_ubi_posicion.codigo as cod_posicion',
                'mov_alm.fecha_emision',
                'mov_alm.id_tp_mov',
                'mov_alm.codigo',
                DB::raw("(tp_doc_com.abreviatura) || '-' || (guia_com.serie) || '-' || (guia_com.numero) as guia_com"),
                'tp_doc_com.cod_sunat as cod_sunat_doc_com',
                // 'tp_ope_com.cod_sunat as cod_sunat_ope_com',
                // 'tp_ope_com.descripcion as des_ope_com',
                DB::raw("(tp_doc_ven.abreviatura) || '-' || (guia_ven.serie) || '-' || (guia_ven.numero) as guia_ven"),
                // 'tp_doc_ven.descripcion as des_doc_ven',
                'tp_doc_ven.cod_sunat as cod_sunat_doc_ven',
                'tp_ope.cod_sunat as cod_sunat_operacion',
                'tp_ope.descripcion as des_operacion',
                'adm_contri.razon_social',
                'trans.codigo as codigo_transferencia',
                'transformacion.codigo as codigo_transformacion',
            )
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            ->leftjoin('almacen.trans', 'trans.id_transferencia', '=', 'mov_alm.id_transferencia')
            ->leftjoin('almacen.transformacion', 'transformacion.id_transformacion', '=', 'mov_alm.id_transformacion')
            ->leftjoin('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'mov_alm_det.id_posicion')
            // ->leftjoin('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            // ->leftjoin('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->leftjoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
            ->leftjoin('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'mov_alm_det.id_guia_com_det')
            ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'guia_com.id_proveedor')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftjoin('almacen.tp_doc_almacen as tp_doc_guia_com', 'tp_doc_guia_com.id_tp_doc_almacen', '=', 'guia_com.id_tp_doc_almacen')
            ->leftjoin('contabilidad.cont_tp_doc as tp_doc_com', 'tp_doc_com.id_tp_doc', '=', 'tp_doc_guia_com.id_tp_doc')
            ->leftjoin('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'mov_alm.id_operacion')
            ->leftjoin('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven_det', '=', 'mov_alm_det.id_guia_ven_det')
            ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')
            ->leftjoin('almacen.tp_doc_almacen as tp_doc_guia_ven', 'tp_doc_guia_ven.id_tp_doc_almacen', '=', 'guia_ven.id_tp_doc_almacen')
            ->leftjoin('contabilidad.cont_tp_doc as tp_doc_ven', 'tp_doc_ven.id_tp_doc', '=', 'tp_doc_guia_ven.id_tp_doc')
            // ->leftjoin('almacen.tp_ope as tp_ope_ven', 'tp_ope_ven.id_operacion', '=', 'guia_ven.id_operacion')
            ->where([
                ['mov_alm_det.id_producto', '=', $id_producto],
                ['mov_alm.fecha_emision', '>=', $finicio],
                ['mov_alm.fecha_emision', '<=', $ffin],
                ['alm_almacen.id_almacen', '=', $almacen],
                ['mov_alm_det.estado', '=', 1]
            ])
            ->orderBy('mov_alm.fecha_emision', 'asc')
            ->orderBy('mov_alm.id_tp_mov', 'asc')
            ->get();

        if (isset($data)) {
            $saldo = 0;
            $saldo_valor = 0;
            $costo_promedio = 0;

            $suma_ing_cant = 0;
            $suma_sal_cant = 0;
            $suma_ing_val = 0;
            $suma_sal_val = 0;

            foreach ($data as $d) {

                if ($d->id_tp_mov == 1 || $d->id_tp_mov == 0) { //ingreso o inicial
                    $saldo += $d->cantidad;
                    $saldo_valor += $d->valorizacion;

                    $suma_ing_cant += $d->cantidad;
                    $suma_ing_val += $d->valorizacion;
                } else if ($d->id_tp_mov == 2) { //salida
                    $saldo -= $d->cantidad;
                    $valor_salida = $costo_promedio * $d->cantidad;
                    $saldo_valor -= $valor_salida;

                    $suma_sal_cant += $d->cantidad;
                    $suma_sal_val += $d->valorizacion;
                }

                $costo_promedio = ($saldo == 0 ? 0 : $saldo_valor / $saldo);

                if ($d->id_tp_mov == 1 || $d->id_tp_mov == 0) {
                    $html .= '
                    <tr id="' . $d->id_mov_alm_det . '">
                        <td>' . $d->fecha_emision . '</td>
                        <td>' . $d->codigo . '</td>
                        <td>' . ($d->guia_com !== null ? $d->guia_com : '') . '</td>
                        <td></td>
                        <td>' . ($d->razon_social !== null ? $d->razon_social : '') . '</td>
                        <td class="text-right" style="background:#ffffb0;">' . $d->cantidad . '</td>
                        <td class="text-right" style="background:#ffffb0;">0</td>
                        <td class="text-right" style="background:#ffffb0;">' . $saldo . '</td>
                        <td class="text-right" style="background:#d8fcfc;">' . number_format($d->valorizacion, 2, ".", ",") . '</td>
                        <td class="text-right" style="background:#d8fcfc;">0</td>
                        <td class="text-right" style="background:#d8fcfc;">' . number_format($saldo_valor, 2, ".", ",") . '</td>
                        <td class="text-right" style="background:#d8fcfc;">' . number_format($costo_promedio, 4, ".", ",") . '</td>
                        <td>' . ($d->cod_sunat_operacion !== null ? $d->cod_sunat_operacion : '') . '</td>
                        <td>' . $d->des_operacion . '</td>
                        <td>' . $d->codigo_transferencia . '</td>
                        <td>' . $d->codigo_transformacion . '</td>
                    </tr>';
                } else if ($d->id_tp_mov == 2) {
                    $html .= '
                    <tr id="' . $d->id_mov_alm_det . '">
                        <td>' . $d->fecha_emision . '</td>
                        <td>' . $d->codigo . '</td>
                        <td>' . ($d->guia_ven !== null ? $d->guia_ven : '') . '</td>
                        <td></td>
                        <td></td>
                        <td class="text-right" style="background:#ffffb0;">0</td>
                        <td class="text-right" style="background:#ffffb0;">' . $d->cantidad . '</td>
                        <td class="text-right" style="background:#ffffb0;">' . $saldo . '</td>
                        <td class="text-right" style="background:#d8fcfc;">0</td>
                        <td class="text-right" style="background:#d8fcfc;">' . number_format($valor_salida, 2, ".", ",") . '</td>
                        <td class="text-right" style="background:#d8fcfc;">' . number_format($saldo_valor, 2, ".", ",") . '</td>
                        <td class="text-right" style="background:#d8fcfc;">' . number_format($costo_promedio, 4, ".", ",") . '</td>
                        <td>' . $d->cod_sunat_operacion . '</td>
                        <td>' . $d->des_operacion . '</td>
                        <td>' . $d->codigo_transferencia . '</td>
                        <td>' . $d->codigo_transformacion . '</td>
                    </tr>';
                }
            }
            // $html.='</tbody></table>';
        }
        return ['html' => $html, 'suma_ing_cant' => $suma_ing_cant, 'suma_sal_cant' => $suma_sal_cant, 'suma_ing_val' => number_format($suma_ing_val, 2, ".", ","), 'suma_sal_val' => number_format($suma_sal_val, 2, ".", ",")];
    }
    public function kardex_producto($id_producto, $almacen, $finicio, $ffin)
    {
        $html = $this->listar_kardex_producto($id_producto, $almacen, $finicio, $ffin);
        return json_encode($html);
    }
    public function download_kardex_producto($id_producto, $almacen, $finicio, $ffin)
    {
        $data = $this->listar_kardex_producto($id_producto, $almacen, $finicio, $ffin);
        $html = $data['html'];
        return view('almacen/reportes/kardex_detallado_excel', compact('html'));
    }
    public function saldo_por_producto($id_producto)
    {
        $data = DB::table('almacen.alm_prod_ubi')
            ->select(
                'alm_prod_ubi.*',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_ubi_posicion.codigo as cod_posicion',
                'alm_almacen.descripcion as des_almacen'
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_prod_ubi.id_producto')
            ->join('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'alm_prod_ubi.id_posicion')
            ->join('almacen.alm_ubi_nivel', 'alm_ubi_nivel.id_nivel', '=', 'alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante', 'alm_ubi_estante.id_estante', '=', 'alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_ubi_estante.id_almacen')
            ->where([
                ['alm_prod_ubi.id_producto', '=', $id_producto],
                ['alm_prod_ubi.stock', '>', 0], ['alm_prod_ubi.estado', '=', 1]
            ])
            ->get();
        return response()->json($data);
    }

    public function ExportarExcelListaIngresos($idEmpresa, $idSede, $almacenes, $condiciones, $fecha_inicio, $fecha_fin, $id_proveedor, $id_usuario, $moneda, $transportista)
    {
        return Excel::download(new ReporteIngresosExcel($idEmpresa, $idSede, $almacenes, $condiciones, $fecha_inicio, $fecha_fin, $id_proveedor, $id_usuario, $moneda, $transportista), 'lista_ingresos.xlsx');
    }


    public function listar_ingresos_lista($idEmpresa, $idSede, $almacenes, $condiciones, $fecha_inicio, $fecha_fin, $id_proveedor, $id_usuario, $idMoneda, /*$referenciado,*/ $transportista)
    {
        $alm_array = explode(',', $almacenes);
        // $doc_array = explode(',',$documentos);
        $con_array = explode(',', $condiciones);

        $data = DB::table('almacen.mov_alm')
            ->select(
                'mov_alm.*',
                'sis_moneda.simbolo',
                'doc_com.total',
                'doc_com.fecha_vcmto',
                'doc_com.total_igv',
                'doc_com.total_a_pagar',
                'cont_tp_doc.abreviatura',
                'doc_com.credito_dias',
                'log_cdn_pago.descripcion as des_condicion',
                'doc_com.fecha_emision as fecha_doc',
                'alm_almacen.descripcion as des_almacen',
                'doc_com.tipo_cambio',
                'doc_com.moneda',
                'doc_com.id_sede',
                DB::raw("(doc_com.serie) || '-' || (doc_com.numero) as doc"),
                DB::raw("(guia_com.serie) || '-' || (guia_com.numero) as guia"),
                'guia_com.fecha_emision as fecha_guia',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'tp_ope.descripcion as des_operacion',
                'sis_usua.nombre_corto as nombre_trabajador'
            )
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
            ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'mov_alm.id_guia_com')
            ->leftjoin('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_com.id_tp_doc_almacen')
            ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'guia_com.id_proveedor')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->join('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'mov_alm.id_operacion')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'mov_alm.usuario')
            // ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','sis_usua.id_trabajador')
            // ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            // ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->leftjoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'mov_alm.id_doc_com')
            ->leftjoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_com.id_tp_doc')
            ->leftjoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_com.moneda')
            ->leftjoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_com.id_condicion')

            ->when(($idEmpresa > 0), function ($query) use ($idEmpresa) {
                $sedes = Sede::where('id_empresa', $idEmpresa)->get();
                $idSedeList = [];
                foreach ($sedes as $sede) {
                    $idSedeList[] = $sede->id_sede;
                }
                return $query->whereIn('alm_almacen.id_sede', $idSedeList);
            })
            ->when(($idSede > 0), function ($query) use ($idSede) {
                return $query->where('alm_almacen.id_sede', $idSede);
            })

            ->when((($fecha_inicio != 'SIN_FILTRO') and ($fecha_fin == 'SIN_FILTRO')), function ($query) use ($fecha_inicio) {
                return $query->where('mov_alm.fecha_emision', '>=', $fecha_inicio);
            })
            ->when((($fecha_inicio == 'SIN_FILTRO') and ($fecha_fin != 'SIN_FILTRO')), function ($query) use ($fecha_fin) {
                return $query->where('mov_alm.fecha_emision', '<=', $fecha_fin);
            })
            ->when((($fecha_inicio != 'SIN_FILTRO') and ($fecha_fin != 'SIN_FILTRO')), function ($query) use ($fecha_inicio, $fecha_fin) {
                return $query->whereBetween('mov_alm.fecha_emision', [$fecha_inicio, $fecha_fin]);
            })

            ->when((count($alm_array) > 0), function ($query) use ($alm_array) {
                return $query->whereIn('mov_alm.id_almacen', $alm_array);
            })
            ->when((count($con_array) > 0), function ($query) use ($con_array) {
                return $query->whereIn('mov_alm.id_operacion', $con_array);
            })
            ->when(($id_proveedor != null && $id_proveedor > 0), function ($query) use ($id_proveedor) {
                return $query->where('guia_com.id_proveedor', $id_proveedor);
            })
            ->when(($id_usuario != null && $id_usuario > 0), function ($query) use ($id_usuario) {
                return $query->where('guia_com.usuario', $id_usuario);
            })
            ->when(($idMoneda == 1 || $idMoneda == 2), function ($query) use ($idMoneda) {
                return $query->where('doc_com.moneda', $idMoneda);
            })
            ->when(($transportista != null && $transportista > 0), function ($query) use ($transportista) {
                return $query->where('guia_com.transportista', $transportista);
            })


            // ->whereIn('mov_alm.id_almacen', $alm_array)
            // ->whereIn('guia_com.id_tp_doc_almacen',$doc_array)
            // ->whereIn('doc_com.id_tp_doc',$docs)
            // ->whereIn('mov_alm.id_operacion', $con_array)
            // ->whereBetween('mov_alm.fecha_emision', [$fecha_inicio, $fecha_fin])
            ->where([['mov_alm.estado', '!=', 7]])
            ->get();

        $nueva_data = [];

        foreach ($data as $d) {
            // $ocs = DB::table('almacen.guia_com_oc')
            // ->select('log_ord_compra.codigo')
            // ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','guia_com_oc.id_oc')
            // ->where('id_guia_com',$d->id_guia_com)
            // ->get();
            $ordenes = MovimientoDetalle::join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', 'mov_alm_det.id_guia_com_det')
                ->join('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', 'guia_com_det.id_oc_det')
                ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', 'log_det_ord_compra.id_orden_compra')
                ->where('mov_alm_det.id_mov_alm', $d->id_mov_alm)
                ->select(['log_ord_compra.codigo'])->distinct()->get();

            $ordenes_array = [];
            foreach ($ordenes as $oc) {
                array_push($ordenes_array, $oc->codigo);
            }

            $comprobantes = MovimientoDetalle::join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', 'mov_alm_det.id_guia_com_det')
                ->join('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', 'guia_com_det.id_guia_com_det')
                ->join('almacen.doc_com', 'doc_com.id_doc_com', 'doc_com_det.id_doc')
                ->join('logistica.log_prove', 'log_prove.id_proveedor', 'doc_com.id_proveedor')
                ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', 'log_prove.id_contribuyente')
                ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_com.moneda')
                ->join('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_com.id_condicion')
                ->where([
                    ['mov_alm_det.id_mov_alm', '=', $d->id_mov_alm],
                    ['mov_alm_det.estado', '!=', 7],
                    ['guia_com_det.estado', '!=', 7],
                    ['doc_com_det.estado', '!=', 7]
                ])
                ->select([
                    'doc_com.serie', 'doc_com.numero', 'doc_com.fecha_emision', 'sis_moneda.simbolo', 'doc_com.moneda',
                    'adm_contri.nro_documento', 'adm_contri.razon_social', 'log_cdn_pago.descripcion as des_condicion',
                    'doc_com.credito_dias', 'doc_com.sub_total', 'doc_com.total_igv', 'doc_com.total_a_pagar'
                ])
                ->distinct()->get();

            $comprobantes_array = [];
            $doc_fecha_emision_array = [];
            $ruc = '';
            $razon_social = '';
            $simbolo = '';
            $moneda = '';
            $total = '';
            $total_igv = '';
            $total_a_pagar = '';
            $condicion = '';
            $credito_dias = '';

            foreach ($comprobantes as $doc) {
                array_push($comprobantes_array, $doc->serie . '-' . $doc->numero);
                array_push($doc_fecha_emision_array, $doc->fecha_emision);
                $ruc = ($doc->nro_documento !== null ? $doc->nro_documento : '');
                $razon_social = ($doc->razon_social !== null ? $doc->razon_social : '');
                $simbolo = ($doc->simbolo !== null ? $doc->simbolo : '');
                $moneda = ($doc->moneda !== null ? $doc->moneda : '');
                $total = ($doc->sub_total !== null ? $doc->sub_total : '');
                $total_igv = ($doc->total_igv !== null ? $doc->total_igv : '');
                $total_a_pagar = ($doc->total_a_pagar !== null ? $doc->total_a_pagar : '');
                $condicion = ($doc->des_condicion !== null ? $doc->des_condicion : '');
                $credito_dias = ($doc->credito_dias !== null ? $doc->credito_dias : '');
            }
            $nuevo = [
                'id_mov_alm' => $d->id_mov_alm,
                'revisado' => $d->revisado,
                'fecha_emision' => $d->fecha_emision,
                'codigo' => $d->codigo,
                'fecha_guia' => $d->fecha_guia,
                'guia' => $d->guia,
                'fecha_doc' => implode(', ', $doc_fecha_emision_array),
                'abreviatura' => $d->abreviatura,
                'doc' => $d->doc,
                'nro_documento' => $ruc,
                'razon_social' => $razon_social,
                'simbolo' => $simbolo,
                'moneda' => $moneda,
                'total' => $total,
                'total_igv' => $total_igv,
                'total_a_pagar' => $total_a_pagar,
                'des_condicion' => $condicion . ($credito_dias !== '' ? ' ' . $credito_dias . ' días' : ''),
                'credito_dias' => $credito_dias,
                'des_operacion' => $d->des_operacion,
                // 'fecha_vcmto'=>$d->fecha_vcmto,
                'nombre_trabajador' => $d->nombre_trabajador,
                'tipo_cambio' => $d->tipo_cambio,
                'des_almacen' => $d->des_almacen,
                'fecha_registro' => $d->fecha_registro,
                'ordenes' => implode(', ', $ordenes_array),
                'documentos' => implode(', ', $comprobantes_array),
            ];
            array_push($nueva_data, $nuevo);
        }

        return response()->json($nueva_data);
        // return response()->json(['docs'=>$docs,'alm'=>$alm,'oc'=>$oc]);
    }


    public function ExportarExcelListaSalidas($idEmpresa, $idSede, $almacenes, $condiciones, $fecha_inicio, $fecha_fin, $id_proveedor, $id_usuario, $moneda)
    {
        return Excel::download(new ReporteSalidasExcel($idEmpresa, $idSede, $almacenes, $condiciones, $fecha_inicio, $fecha_fin, $id_proveedor, $id_usuario, $moneda), 'lista_salidas.xlsx');
    }

    public function listar_salidas($almacenes, $documentos, $condiciones, $fecha_inicio, $fecha_fin, $id_cliente, $id_usuario, $moneda, $referenciado)
    {
        $alm_array = explode(',', $almacenes);
        $doc_array = explode(',', $documentos);
        $con_array = explode(',', $condiciones);

        $hasWhere = [];
        if ($id_cliente !== null && $id_cliente > 0) {
            $hasWhere[] = ['guia_ven.id_cliente', '=', $id_cliente];
        }
        if ($id_usuario !== null && $id_usuario > 0) {
            $hasWhere[] = ['guia_ven.usuario', '=', $id_usuario];
        }
        if ($moneda == 1 || $moneda == 2) {
            $hasWhere[] = ['doc_ven.moneda', '=', $moneda];
        }

        $data = DB::table('almacen.mov_alm')
            ->select(
                'mov_alm.*',
                'sis_moneda.simbolo',
                'doc_ven.total',
                'doc_ven.fecha_vcmto',
                'doc_ven.total_igv',
                'doc_ven.total_a_pagar',
                'cont_tp_doc.abreviatura',
                'doc_ven.credito_dias',
                'log_cdn_pago.descripcion as des_condicion',
                'doc_ven.fecha_emision as fecha_doc',
                'alm_almacen.descripcion as des_almacen',
                'doc_ven.tipo_cambio',
                'doc_ven.moneda',
                DB::raw("(doc_ven.serie) || '-' || (doc_ven.numero) as doc"),
                DB::raw("(guia_ven.serie) || '-' || (guia_ven.numero) as guia"),
                'guia_ven.fecha_emision as fecha_guia',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'tp_ope.descripcion as des_operacion',
                'sis_usua.nombre_corto as nombre_trabajador'
            )
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
            ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'mov_alm.id_guia_ven')
            ->leftjoin('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_ven.id_tp_doc_almacen')
            ->leftjoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'guia_ven.id_cliente')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->join('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'mov_alm.id_operacion')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'mov_alm.usuario')
            ->leftjoin('almacen.doc_ven', 'doc_ven.id_doc_ven', '=', 'mov_alm.id_doc_ven')
            ->leftjoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_ven.id_tp_doc')
            ->leftjoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_ven.moneda')
            ->leftjoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_ven.id_condicion')
            ->whereIn('mov_alm.id_almacen', $alm_array)
            // ->whereIn('guia_ven.id_tp_doc_almacen',$doc_array)
            ->whereIn('mov_alm.id_operacion', $con_array)
            ->whereBetween('mov_alm.fecha_emision', [$fecha_inicio, $fecha_fin])
            ->where([['mov_alm.estado', '!=', 7]])
            ->where($hasWhere)
            ->get();

        return response()->json($data);
    }
    public function update_revisado($id_mov_alm, $revisado, $obs)
    {
        $data = DB::table('almacen.mov_alm')
            ->where('id_mov_alm', $id_mov_alm)
            ->update([
                'revisado' => $revisado,
                'obs' => $obs
            ]);
        return response()->json($data);
    }
    public function listar_busqueda_ingresos($almacenes, $tipo, $descripcion, $documentos, $fecha_inicio, $fecha_fin)
    {
        $alm_array = explode(',', $almacenes);
        $doc_array = explode(',', $documentos);
        $des = strtoupper($descripcion);
        $hasWhere = '';

        if ($tipo == 1) {
            $hasWhere = 'alm_prod.descripcion';
        } else if ($tipo == 2) {
            $hasWhere = 'alm_prod.codigo';
        } else if ($tipo == 3) {
            $hasWhere = 'alm_prod.codigo_anexo';
        }

        if ($descripcion !== '<vacio>') {
            $data = DB::table('almacen.mov_alm_det')
                ->select(
                    'mov_alm_det.*',
                    'mov_alm.fecha_emision',
                    'tp_doc_almacen.abreviatura as tp_doc',
                    'guia_com.fecha_emision as fecha_guia',
                    DB::raw("(guia_com.serie) || '-' || (guia_com.numero) as guia"),
                    'adm_contri.razon_social',
                    'adm_contri.nro_documento',
                    'alm_almacen.descripcion as alm_descripcion',
                    'alm_prod.part_number',
                    'alm_prod.codigo',
                    'alm_prod.descripcion',
                    'tp_ope.descripcion as ope_descripcion',
                    'adm_estado_doc.estado_doc',
                    // 'alm_req.codigo as codigo_requerimiento',
                    // 'oportunidades.codigo_oportunidad',
                    DB::raw("(CASE WHEN alm_req.id_requerimiento >0  THEN alm_req.codigo 
                    WHEN alm_req_t.id_requerimiento > 0  THEN alm_req_t.codigo
                    WHEN alm_req_ts.id_requerimiento > 0  THEN alm_req_ts.codigo
                    ELSE '' END) AS codigo_requerimiento"),
                    DB::raw("(CASE WHEN oportunidades.id > 0  THEN oportunidades.codigo_oportunidad 
                    WHEN oportunidades_t.id > 0  THEN oportunidades_t.codigo_oportunidad
                    WHEN oportunidades_ts.id > 0  THEN oportunidades_ts.codigo_oportunidad
                    ELSE '' END) AS codigo_oportunidad")
                    // DB::raw("(SELECT 
                    // FROM almacen.guia_com
                    // WHERE   guia_com.id_guia = mov_alm_det.id_guia_com_det AND
                    // mov_alm_det.estado != 7) AS gg")
                )
                ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
                ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
                ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
                // ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
                ->leftjoin('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'mov_alm_det.id_guia_com_det')

                ->leftjoin('almacen.trans_detalle', 'trans_detalle.id_trans_detalle', '=', 'guia_com_det.id_trans_detalle')
                ->leftjoin('almacen.alm_det_req as alm_det_req_ts', 'alm_det_req_ts.id_detalle_requerimiento', '=', 'trans_detalle.id_requerimiento_detalle')
                ->leftjoin('almacen.alm_req as alm_req_ts', 'alm_req_ts.id_requerimiento', '=', 'alm_det_req_ts.id_requerimiento')
                ->leftjoin('mgcp_cuadro_costos.cc as cc_ts', 'cc_ts.id', '=', 'alm_req_ts.id_cc')
                ->leftjoin('mgcp_oportunidades.oportunidades as oportunidades_ts', 'oportunidades_ts.id', '=', 'cc_ts.id_oportunidad')

                ->leftjoin('almacen.transfor_transformado', 'transfor_transformado.id_transformado', '=', 'guia_com_det.id_transformado')
                ->leftjoin('almacen.orden_despacho_det', 'orden_despacho_det.id_od_detalle', '=', 'transfor_transformado.id_od_detalle')
                ->leftjoin('almacen.alm_det_req as alm_det_req_t', 'alm_det_req_t.id_detalle_requerimiento', '=', 'orden_despacho_det.id_detalle_requerimiento')
                ->leftjoin('almacen.alm_req as alm_req_t', 'alm_req_t.id_requerimiento', '=', 'alm_det_req_t.id_requerimiento')
                ->leftjoin('mgcp_cuadro_costos.cc as cc_t', 'cc_t.id', '=', 'alm_req_t.id_cc')
                ->leftjoin('mgcp_oportunidades.oportunidades as oportunidades_t', 'oportunidades_t.id', '=', 'cc_t.id_oportunidad')


                ->leftjoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det')
                ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
                ->leftjoin('almacen.alm_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento')
                ->leftjoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
                ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')

                ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'mov_alm_det.id_guia_com')
                ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'guia_com.id_proveedor')
                ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
                ->leftjoin('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_com.id_tp_doc_almacen')
                ->join('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'mov_alm.id_operacion')
                ->leftjoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'guia_com.estado')
                ->whereIn('mov_alm.id_almacen', $alm_array)
                ->whereIn('guia_com.id_tp_doc_almacen', $doc_array)
                ->whereBetween('mov_alm.fecha_emision', [$fecha_inicio, $fecha_fin])
                ->where($hasWhere, 'like', '%' . $des . '%')
                // ->where( ( ($des !== '') ? [$hasWhere,'like','%'.$des.'%'] : '' ) )
                ->get();
        } else {
            $data = DB::table('almacen.mov_alm_det')
                ->select(
                    'mov_alm_det.*',
                    'mov_alm.fecha_emision',
                    'tp_doc_almacen.abreviatura as tp_doc',
                    'guia_com.fecha_emision as fecha_guia',
                    DB::raw("(guia_com.serie) || '-' || (guia_com.numero) as guia"),
                    'adm_contri.razon_social',
                    'adm_contri.nro_documento',
                    'alm_almacen.descripcion as alm_descripcion',
                    'alm_prod.part_number',
                    'alm_prod.codigo',
                    'alm_prod.descripcion',
                    'tp_ope.descripcion as ope_descripcion',
                    'adm_estado_doc.estado_doc',
                    // 'alm_req.codigo as codigo_requerimiento',
                    // 'oportunidades.codigo_oportunidad',
                    DB::raw("(CASE WHEN alm_req.id_requerimiento > 0 THEN alm_req.codigo 
                    WHEN alm_req_t.id_requerimiento > 0 THEN alm_req_t.codigo
                    WHEN alm_req_ts.id_requerimiento > 0 THEN alm_req_ts.codigo
                    ELSE '' END) AS codigo_requerimiento"),
                    DB::raw("(CASE WHEN oportunidades.id > 0  THEN oportunidades.codigo_oportunidad 
                    WHEN oportunidades_t.id > 0 THEN oportunidades_t.codigo_oportunidad
                    WHEN oportunidades_ts.id > 0 THEN oportunidades_ts.codigo_oportunidad
                    ELSE '' END) AS codigo_oportunidad")

                )
                ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
                ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
                ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
                // ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
                ->join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'mov_alm_det.id_guia_com_det')


                ->leftjoin('almacen.trans_detalle', 'trans_detalle.id_trans_detalle', '=', 'guia_com_det.id_trans_detalle')
                ->leftjoin('almacen.alm_det_req as alm_det_req_ts', 'alm_det_req_ts.id_detalle_requerimiento', '=', 'trans_detalle.id_requerimiento_detalle')
                ->leftjoin('almacen.alm_req as alm_req_ts', 'alm_req_ts.id_requerimiento', '=', 'alm_det_req_ts.id_requerimiento')
                ->leftjoin('mgcp_cuadro_costos.cc as cc_ts', 'cc_ts.id', '=', 'alm_req_ts.id_cc')
                ->leftjoin('mgcp_oportunidades.oportunidades as oportunidades_ts', 'oportunidades_ts.id', '=', 'cc_ts.id_oportunidad')


                ->leftjoin('almacen.transfor_transformado', 'transfor_transformado.id_transformado', '=', 'guia_com_det.id_transformado')
                ->leftjoin('almacen.orden_despacho_det', 'orden_despacho_det.id_od_detalle', '=', 'transfor_transformado.id_od_detalle')
                ->leftjoin('almacen.alm_det_req as alm_det_req_t', 'alm_det_req_t.id_detalle_requerimiento', '=', 'orden_despacho_det.id_detalle_requerimiento')
                ->leftjoin('almacen.alm_req as alm_req_t', 'alm_req_t.id_requerimiento', '=', 'alm_det_req_t.id_requerimiento')
                ->leftjoin('mgcp_cuadro_costos.cc as cc_t', 'cc_t.id', '=', 'alm_req_t.id_cc')
                ->leftjoin('mgcp_oportunidades.oportunidades as oportunidades_t', 'oportunidades_t.id', '=', 'cc_t.id_oportunidad')


                ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
                ->leftjoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det')
                ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
                ->leftjoin('almacen.alm_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento')
                ->leftjoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
                ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')

                ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'guia_com.id_proveedor')
                ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
                ->leftjoin('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_com.id_tp_doc_almacen')
                ->leftjoin('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'guia_com.id_operacion')
                ->leftjoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'guia_com.estado')
                ->whereIn('mov_alm.id_almacen', $alm_array)
                ->whereIn('guia_com.id_tp_doc_almacen', $doc_array)
                ->whereBetween('mov_alm.fecha_emision', [$fecha_inicio, $fecha_fin])
                ->get();
        }

        return response()->json($data);
    }
    public function imprimir_guia_ingreso($id_ing)
    {
        $id = $this->decode5t($id_ing);
        $result = $this->get_ingreso($id);
        $ingreso = $result['ingreso'];
        $detalle = $result['detalle'];
        $ocs = $result['ocs'];

        $cod_ocs = '';
        foreach ($ocs as $oc) {
            if ($cod_ocs == '') {
                $cod_ocs .= $oc->codigo;
            } else {
                $cod_ocs .= ', ' . $oc->codigo;
            }
        }
        $revisado = ($ingreso->revisado !== 0 ? 'No Revisado' : ($ingreso->revisado !== 1 ? 'Revisado' : 'Observado'));
        $fecha_actual = date('Y-m-d');
        $hora_actual = date('H:i:s');

        $html = '
        <html>
            <head>
                <style type="text/css">
                *{
                    font-family: "DejaVu Sans";
                }
                table{
                    width:100%;
                    font-size:11px;
                }
                #detalle thead{
                    padding: 4px;
                    background-color: #e5e5e5;
                }
                #detalle tbody tr td,
                #detalle tfoot tr td{
                    font-size:11px;
                    padding: 4px;
                }
                #detalle tfoot{
                    border-top: 1px dashed #343a40;
                }
                .right{
                    text-align: right;
                }
                .sup{
                    vertical-align:top;
                }
                .guinda{
                    background-color: #8f1c1c;
                }
                </style>
            </head>
            <body>
                <table width="100%">
                    <tr>
                        <td>
                            <p style="text-align:left;font-size:10px;margin:0px;">' . $ingreso->ruc_empresa . '</p>
                            <p style="text-align:left;font-size:10px;margin:0px;">' . $ingreso->empresa_razon_social . '</p>
                            <p style="text-align:left;font-size:10px;margin:0px;">.::Sistema ERP v1.0::.</p>
                        </td>
                        <td>
                            <p style="text-align:right;font-size:10px;margin:0px;">Fecha: ' . $fecha_actual . '</p>
                            <p style="text-align:right;font-size:10px;margin:0px;">Hora : ' . $hora_actual . '</p>
                        </td>
                    </tr>
                </table>
                <div style="border:1px #212121 solid;padding:2px;background-color:#e5e5e5;width:60%;margin:auto">
                    <h3 style="margin:0px;"><center>' . $ingreso->tp_doc_descripcion . '</center></h3>
                </div>
                <h5 style="margin:5px;"><center>' . $revisado . '</center></h5>

                <table border="0" style="border:1px #212121 dashed;padding:3px;">
                    <tr>
                        <td width=120px class="subtitle">Sucursal / Almacén</td>
                        <td width=10px>:</td>
                        <td colSpan="7" class="verticalTop">' . $ingreso->empresa_razon_social . ' / ' . $ingreso->des_almacen . '</td>
                    </tr>
                    <tr>
                        <td>TD.</td>
                        <td width=10px>:</td>
                        <td width=130px>' . $ingreso->guia . '</td>
                        <td width=50px>Fecha</td>
                        <td width=10px>:</td>
                        <td width=100px>' . $ingreso->fecha_guia . '</td>
                        <td width=50px>Moneda</td>
                        <td width=10px>:</td>
                        <td>' . $ingreso->des_moneda . '</td>
                        <td width=30px>T.C.</td>
                        <td width=10px>:</td>
                        <td width=40px>' . $ingreso->tipo_cambio . '</td>
                    </tr>
                    <tr>
                        <td>Señores</td>
                        <td width=10px>:</td>
                        <td width=130px colSpan="4">' . $ingreso->razon_social . '</td>
                        <td width=50px>Teléfono(s)</td>
                        <td width=10px>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Dirección</td>
                        <td width=10px>:</td>
                        <td width=130px colSpan="4">' . $ingreso->direccion_fiscal . '</td>
                        <td width=50px>RUC</td>
                        <td width=10px>:</td>
                        <td>' . $ingreso->nro_documento . '</td>
                    </tr>
                    <tr>
                        <td>Responsable</td>
                        <td width=10px>:</td>
                        <td width=130px colSpan="4">' . $ingreso->persona . '</td>
                        <td width=50px>Condición</td>
                        <td width=10px>:</td>
                        <td colSpan="4">' . $ingreso->ope_descripcion . '</td>
                    </tr>
                    <tr>
                        <td>Cod. Ingreso</td>
                        <td width=10px>:</td>
                        <td width=130px colSpan="4">' . $ingreso->codigo . '</td>
                        <td width=50px>Fecha Ing</td>
                        <td width=10px>:</td>
                        <td colSpan="4">' . $ingreso->fecha_emision . '</td>
                    </tr>
                </table>
                <br/>
                <table id="detalle">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Código</th>
                            <th>Cód.Anexo</th>
                            <th width=40% >Descripción</th>
                            <th>Cant.</th>
                            <th>Unid.</th>
                            <th>V.Compra</th>
                            <th>Agregado</th>
                            <th>P.Total</th>
                            <th>Unitario</th>
                        </tr>
                    </thead>
                    <tbody>';
        $i = 1;
        $total = 0;
        $unitarios = 0;
        $agregado = 0;

        foreach ($detalle as $det) {
            $unitario = floatval($det->valorizacion) / floatval($det->cantidad);
            $total += floatval($det->valorizacion);
            $unitarios += floatval($unitario);
            $agregado += floatval($det->unitario_adicional);
            $html .= '
                        <tr>
                            <td class="right">' . $i . '</td>
                            <td>' . $det->codigo . '</td>
                            <td>' . $det->codigo_anexo . '</td>
                            <td>' . $det->descripcion . '</td>
                            <td class="right">' . $det->cantidad . '</td>
                            <td>' . $det->abreviatura . '</td>
                            <td class="right">' . $det->unitario . '</td>
                            <td class="right">' . $det->unitario_adicional . '</td>
                            <td class="right">' . $det->valorizacion . '</td>
                            <td class="right">' . $unitario . '</td>
                        </tr>';
            $i++;
        }
        $igv = $total * 0.18;
        $html .= '</tbody>
                    <tfoot>
                        <tr>
                            <td class="right" colSpan="6"><strong>Totales</strong></td>
                            <td class="right"></td>
                            <td class="right">' . $agregado . '</td>
                            <td class="right">' . $total . '</td>
                            <td class="right">' . $unitarios . '</td>
                        </tr>
                    </tfoot>
                </table>
                <br/>
                <div width=200px style="border:1px #212121 solid;padding:2px;background-color:#e5e5e5;">
                    <table>
                        <tr>
                            <td class="right"><strong>Monto Neto: </strong></td>
                            <td class="right">' . $total . '</td>
                            <td class="right"><strong>Impuesto: </strong></td>
                            <td class="right">' . $igv . '</td>
                            <td class="right"><strong>Total Doc: </strong></td>
                            <td class="right">' . ($total + $igv) . '</td>
                        </tr>
                    </table>
                </div>
                <p style="text-align:right;font-size:11px;">Elaborado por: ' . $ingreso->nom_usuario . ' ' . $ingreso->fecha_registro . '</p>

            </body>
        </html>';

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->stream();
        return $pdf->download('ingreso.pdf');
    }
    public function listar_busqueda_salidas($almacenes, $tipo, $descripcion, $documentos, $fecha_inicio, $fecha_fin)
    {
        $alm_array = explode(',', $almacenes);
        $doc_array = explode(',', $documentos);
        $des = strtoupper($descripcion);
        $hasWhere = '';

        if ($tipo == 1) {
            $hasWhere = 'alm_prod.descripcion';
        } else if ($tipo == 2) {
            $hasWhere = 'alm_prod.codigo';
        } else if ($tipo == 3) {
            $hasWhere = 'alm_prod.codigo_anexo';
        }

        if ($descripcion !== '<vacio>') {
            $data = DB::table('almacen.mov_alm_det')
                ->select(
                    'mov_alm_det.*',
                    'mov_alm.fecha_emision',
                    'tp_doc_almacen.abreviatura as tp_doc',
                    'guia_ven.fecha_emision as fecha_guia',
                    DB::raw("(guia_ven.serie) || '-' || (guia_ven.numero) as guia"),
                    'adm_contri.razon_social',
                    'adm_contri.nro_documento',
                    'alm_almacen.descripcion as alm_descripcion',
                    'alm_prod.part_number',
                    'alm_prod.codigo',
                    'alm_prod.descripcion',
                    'tp_ope.descripcion as ope_descripcion',
                    'adm_estado_doc.estado_doc',
                    'alm_req.codigo as codigo_requerimiento',
                    'oportunidades.codigo_oportunidad AS cdp',
                    'users.nombre_corto AS responsable'
                )
                ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
                ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
                ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
                // ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
                ->join('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven_det', '=', 'mov_alm_det.id_guia_ven_det')
                ->join('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')

                ->leftjoin('almacen.orden_despacho_det', 'orden_despacho_det.id_od_detalle', '=', 'guia_ven_det.id_od_det')
                ->join('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'orden_despacho_det.id_od')
                ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
                ->leftjoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
                ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
                ->leftjoin('mgcp_usuarios.users', 'users.id', '=', 'oportunidades.id_responsable')

                ->leftjoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'guia_ven.id_cliente')
                ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
                ->leftjoin('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_ven.id_tp_doc_almacen')
                ->leftjoin('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'guia_ven.id_operacion')
                ->leftjoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'guia_ven.estado')
                ->whereIn('mov_alm.id_almacen', $alm_array)
                ->whereIn('guia_ven.id_tp_doc_almacen', $doc_array)
                ->whereBetween('mov_alm.fecha_emision', [$fecha_inicio, $fecha_fin])
                ->where($hasWhere, 'like', '%' . $des . '%')
                // ->where( ( ($des !== '') ? [$hasWhere,'like','%'.$des.'%'] : '' ) )
                ->get();
        } else {
            $data = DB::table('almacen.mov_alm_det')
                ->select(
                    'mov_alm_det.*',
                    'mov_alm.fecha_emision',
                    'tp_doc_almacen.abreviatura as tp_doc',
                    'guia_ven.fecha_emision as fecha_guia',
                    DB::raw("(guia_ven.serie) || '-' || (guia_ven.numero) as guia"),
                    'adm_contri.razon_social',
                    'adm_contri.nro_documento',
                    'alm_almacen.descripcion as alm_descripcion',
                    'alm_prod.part_number',
                    'alm_prod.codigo',
                    'alm_prod.descripcion',
                    'tp_ope.descripcion as ope_descripcion',
                    'adm_estado_doc.estado_doc',
                    'alm_req.codigo as codigo_requerimiento',
                    'oportunidades.codigo_oportunidad AS cdp',
                    'users.nombre_corto AS responsable'

                )
                ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
                ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
                ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
                // ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
                ->join('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven_det', '=', 'mov_alm_det.id_guia_ven_det')
                ->join('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')

                ->join('almacen.orden_despacho_det', 'orden_despacho_det.id_od_detalle', '=', 'guia_ven_det.id_od_det')
                ->join('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'orden_despacho_det.id_od')
                ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
                ->leftjoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
                ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
                ->leftjoin('mgcp_usuarios.users', 'users.id', '=', 'oportunidades.id_responsable')

                ->leftjoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'guia_ven.id_cliente')
                ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
                ->leftjoin('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_ven.id_tp_doc_almacen')
                ->leftjoin('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'guia_ven.id_operacion')
                ->leftjoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'guia_ven.estado')
                ->whereIn('mov_alm.id_almacen', $alm_array)
                ->whereIn('guia_ven.id_tp_doc_almacen', $doc_array)
                ->whereBetween('mov_alm.fecha_emision', [$fecha_inicio, $fecha_fin])
                ->get();
        }

        return response()->json($data);
    }
    public function listar_transportistas_com()
    {
        $data = DB::table('almacen.guia_com')->distinct()
            ->select('guia_com.transportista', 'adm_contri.id_contribuyente', 'adm_contri.razon_social', 'adm_contri.nro_documento')
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'guia_com.transportista')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->where('guia_com.estado', '<>', 7)
            ->groupBy('guia_com.transportista', 'adm_contri.id_contribuyente', 'adm_contri.razon_social', 'adm_contri.nro_documento')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function listar_transportistas_ven()
    {
        $data = DB::table('almacen.guia_ven')->distinct()
            ->select('guia_ven.transportista', 'adm_contri.id_contribuyente', 'adm_contri.razon_social', 'adm_contri.nro_documento')
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'guia_ven.transportista')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->where('guia_ven.estado', '<>', 7)
            ->groupBy('guia_ven.transportista', 'adm_contri.id_contribuyente', 'adm_contri.razon_social', 'adm_contri.nro_documento')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    // public function guardar_prorrateo(Request $request){
    //     $id_usuario = Auth::user()->id_usuario;
    //     $id_doc_com = DB::table('almacen.doc_com')->insertGetId(
    //         [
    //             'serie' => $request->pro_serie,
    //             'numero' => $request->pro_numero,
    //             'id_tp_doc' => $request->id_tp_documento,
    //             'id_proveedor' => $request->doc_id_proveedor,
    //             'moneda' => $request->id_moneda,
    //             'fecha_emision' => $request->doc_fecha_emision,
    //             'tipo_cambio' => $request->tipo_cambio,
    //             'sub_total' => $request->sub_total,
    //             'total_descuento' => 0,
    //             'total' => $request->sub_total,
    //             'total_igv' => 0,
    //             'total_a_pagar' => $request->sub_total,
    //             'usuario' => $id_usuario,
    //             'registrado_por' => $id_usuario,
    //             'estado' => 1,
    //             'fecha_registro' => date('Y-m-d H:i:s')
    //         ],
    //             'id_doc_com'
    //         );

    //     $data = DB::table('almacen.guia_com_prorrateo')->insertGetId(
    //         [
    //             'id_guia_com' => $request->id_guia,
    //             'id_tp_prorrateo' => $request->id_tp_prorrateo,
    //             'id_doc_com' => $id_doc_com,
    //             'tipo' => 1,//calculo global
    //             'importe' => $request->importe,
    //             'fecha_registro' => date('Y-m-d H:i:s')
    //         ],
    //             'id_prorrateo'
    //         );

    //     return response()->json($data);
    // }


    // public function listar_documentos_prorrateo(){
    //     $data = DB::table('almacen.guia_com_prorrateo')
    //         ->select('guia_com_prorrateo.id_prorrateo','tp_prorrateo.descripcion as des_tp_prorrateo',
    //         'doc_com.*','sis_moneda.simbolo','tp_doc_almacen.abreviatura as tp_doc_guia',
    //         'guia_com.serie as serie_guia','guia_com.numero as numero_guia',
    //         'adm_contri.nro_documento','adm_contri.razon_social','cont_tp_doc.descripcion as tp_doc_descripcion')
    //         ->join('almacen.doc_com','doc_com.id_doc_com','=','guia_com_prorrateo.id_doc_com')
    //         ->join('logistica.log_prove','log_prove.id_proveedor','=','doc_com.id_proveedor')
    //         ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
    //         ->leftJoin('contabilidad.cont_tp_doc','cont_tp_doc.id_tp_doc','=','doc_com.id_tp_doc')
    //         ->join('almacen.tp_prorrateo','tp_prorrateo.id_tp_prorrateo','=','guia_com_prorrateo.id_tp_prorrateo')
    //         ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','doc_com.moneda')
    //         ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_prorrateo.id_guia_com')
    //         ->join('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
    //         // ->where('guia_com_prorrateo.id_guia_com',$id)
    //         ->get();
    //     $output['data'] = $data;
    //     return response()->json($output);
    // }

    // public function listar_guia_detalle_prorrateo($id, $total_comp){
    //     $data = DB::table('almacen.guia_com_det')
    //     ->select('guia_com_det.*','alm_prod.codigo','alm_prod.descripcion',
    //     'alm_und_medida.abreviatura','log_ord_compra.codigo AS cod_orden')
    //     ->leftjoin('almacen.alm_prod','alm_prod.id_producto','=','guia_com_det.id_producto')
    //     ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','guia_com_det.id_posicion')
    //     ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','guia_com_det.id_unid_med')
    //     ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
    //     ->leftjoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
    //     ->leftjoin('administracion.adm_tp_docum','adm_tp_docum.id_tp_documento','=','log_ord_compra.id_tp_documento')
    //     ->where([['guia_com_det.id_guia_com', '=', $id],
    //              ['guia_com_det.estado','=',1]])
    //         ->get();
    //     $html = '';
    //     $suma_total = 0;
    //     $suma_adicional = 0;
    //     $suma_costo = 0;

    //     foreach($data as $det){
    //         $suma_total += floatval($det->cantidad * $det->unitario);
    //     }

    //     $valor = $total_comp / ($suma_total > 0 ? $suma_total : 1);

    //     foreach($data as $det){
    //         $id_guia_com_det = $det->id_guia_com_det;
    //         $oc = $det->cod_orden;
    //         $codigo = $det->codigo;
    //         $descripcion = $det->descripcion;
    //         $cantidad = $det->cantidad;
    //         $abrev = $det->abreviatura;
    //         $id_posicion = $det->id_posicion;
    //         $unitario = $det->unitario;
    //         $total = floatval($det->cantidad * $det->unitario);

    //         $adic = DB::table('almacen.guia_com_prorrateo_det')
    //         ->select(DB::raw('sum(guia_com_prorrateo_det.importe) as importe_adicional'))
    //         ->where('id_guia_com_det',$id_guia_com_det)
    //         ->first();

    //         $adicional = round(($valor * $total),4,PHP_ROUND_HALF_UP) + round($adic->importe_adicional,4,PHP_ROUND_HALF_UP);
    //         $costo_total = $total + $adicional;

    //         $suma_adicional += $adicional;
    //         $suma_costo += $costo_total;

    //         $unit = round(($costo_total/$cantidad),4,PHP_ROUND_HALF_UP);

    //         $html .=
    //         '<tr id="det-'.$id_guia_com_det.'">
    //             <td>'.$oc.'</td>
    //             <td>'.$codigo.'</td>
    //             <td>'.$descripcion.'</td>
    //             <td style="text-align:right">'.$cantidad.'</td>
    //             <td>'.$abrev.'</td>
    //             <td style="text-align:right">'.$total.'</td>
    //             <td style="text-align:right">'.$adicional.'</td>
    //             <td style="text-align:right"><input type="text" class="oculto" name="unit" value="'.$unit.'"/>'.$costo_total.'</td>
    //         </tr>';
    //     }
    //     $sumas[] = [
    //         'suma_total'=>round($suma_total,2,PHP_ROUND_HALF_UP),
    //         'suma_adicional'=>round($suma_adicional,2,PHP_ROUND_HALF_UP),
    //         'suma_costo'=>round($suma_costo,2,PHP_ROUND_HALF_UP),
    //     ];
    //     return json_encode(['html'=>$html,'sumas'=>$sumas]);
    // }
    // public function update_doc_prorrateo(Request $request){
    //     $prorrateo = DB::table('almacen.guia_com_prorrateo')
    //     ->where('id_prorrateo',$request->id_prorrateo)
    //     ->update(['importe'=>$request->importe]);

    //     $doc = DB::table('almacen.doc_com')
    //     ->where('id_doc_com',$request->id_doc)
    //     ->update([ 'tipo_cambio'=>$request->tipo_cambio,
    //                'sub_total'=>$request->sub_total ]);

    //     return response()->json($prorrateo);
    // }
    // public function eliminar_doc_prorrateo($id_prorrateo, $id_doc){
    //     $data = DB::table('almacen.guia_com_prorrateo')
    //     ->where('id_prorrateo',$id_prorrateo)
    //     ->delete();

    //     $detalle = DB::table('almacen.doc_com_det')->where('id_doc',$id_doc)->get();

    //     if (isset($detalle)){
    //         DB::table('almacen.doc_com')->where('id_doc_com',$id_doc)
    //         ->delete();
    //         DB::table('almacen.guia_com_prorrateo_det')->where('id_prorrateo',$id_prorrateo)
    //         ->delete();
    //     }

    //     return response()->json($data);
    // }
    // public function mostrar_guia_detalle($id,$id_prorrateo){
    //     $data = DB::table('almacen.guia_com_det')
    //     ->select('guia_com_det.*','alm_prod.codigo','alm_prod.descripcion',
    //     'alm_und_medida.abreviatura','alm_prod.series','log_ord_compra.codigo AS cod_orden')
    //     ->leftjoin('almacen.alm_prod','alm_prod.id_producto','=','guia_com_det.id_producto')
    //     ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','guia_com_det.id_posicion')
    //     ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','guia_com_det.id_unid_med')
    //     ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
    //     ->leftjoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
    //     ->leftjoin('administracion.adm_tp_docum','adm_tp_docum.id_tp_documento','=','log_ord_compra.id_tp_documento')
    //     ->where([['guia_com_det.id_guia_com', '=', $id],
    //              ['guia_com_det.estado','=',1]])
    //     ->get();

    //     $html = '';
    //     foreach($data as $det){
    //         $pro = DB::table('almacen.guia_com_prorrateo_det')
    //         ->where([['id_guia_com_det','=',$det->id_guia_com_det],
    //                  ['id_prorrateo','=',$id_prorrateo]])
    //         ->first();

    //         $importe = 0;
    //         $chk = '';

    //         if (isset($pro)){
    //             $chk = 'checked';
    //             $importe = $pro->importe;
    //         }

    //         $html.='
    //         <tr id="'.$det->id_guia_com_det.'">
    //             <td><input type="checkbox" '.$chk.'/></td>
    //             <td>'.$det->codigo.'</td>
    //             <td>'.$det->descripcion.'</td>
    //             <td>'.$det->cantidad.'</td>
    //             <td>'.$det->abreviatura.'</td>
    //             <td>'.$det->unitario.'</td>
    //             <td>'.$det->total.'</td>
    //             <td>'.$importe.'</td>
    //         </tr>
    //         ';
    //     }
    //     return json_encode($html);
    // }
    // public function guardar_prorrateo_detalle(Request $request){
    //     $det = explode(',',$request->id_guia_com_det);
    //     $total_det = explode(',',$request->total_det);
    //     $count = count($det);

    //     $total_comp = floatval($request->importe_comp);
    //     $suma_total = floatval($request->suma_total);
    //     $valor = $total_comp / $suma_total;
    //     $result = [];

    //     for ($i=0; $i<$count; $i++){
    //         $id = $det[$i];
    //         $total = $total_det[$i];
    //         $adicional = round(($valor * $total),4,PHP_ROUND_HALF_UP);

    //         $pro_det = DB::table('almacen.guia_com_prorrateo_det')
    //         ->where([['id_guia_com_det','=',$id],['id_prorrateo','=',$request->id_prorrateo]])
    //         ->first();

    //         if (isset($pro_det)){//si no existe -> agrega
    //             DB::table('almacen.guia_com_prorrateo_det')
    //             ->where([['id_guia_com_det','=',$id],['id_prorrateo','=',$request->id_prorrateo]])
    //             ->update([  'importe'=>$adicional,
    //                         'fecha_registro'=>date('Y-m-d H:i:s')
    //                     ]);
    //         } else {//si existe -> actualiza
    //             DB::table('almacen.guia_com_prorrateo_det')->insertGetId(
    //                 [
    //                     'id_prorrateo'=>$request->id_prorrateo,
    //                     'id_guia_com_det'=>$id,
    //                     'importe'=>$adicional,
    //                     'fecha_registro'=>date('Y-m-d H:i:s')
    //                 ],
    //                     'id_prorrateo_det'
    //                 );
    //         }
    //     }
    //     $data = DB::table('almacen.guia_com_prorrateo')
    //     ->where('id_prorrateo',$request->id_prorrateo)
    //     ->update([ 'tipo' => 2 ]); //calculo por items

    //     return response()->json($data);
    // }
    /**Update adicional guia detalle */
    // public function update_guia_detalle_adic(Request $request){
    //     $id = explode(',',$request->id_guia_com_det);
    //     $unitario = explode(',',$request->unitario);
    //     $count = count($id);
    //     $update = '';

    //     for ($i=0; $i<$count; $i++){
    //         $id_guia_com_det = $id[$i];
    //         $unit = $unitario[$i];

    //         //Obtiene guia detalle
    //         $det = DB::table('almacen.guia_com_det')
    //         ->where('id_guia_com_det',$id_guia_com_det)
    //         ->first();

    //         //Calcula el nuevo unitario adicional
    //         $nuevo = $unit - $det->unitario;
    //         $nuevo_adic = ($nuevo < 0 ? 0 : $nuevo);
    //         $total = ($det->unitario + $nuevo_adic) * $det->cantidad;

    //         //Actualiza el total OJO:no mueve el unitario
    //         $update = DB::table('almacen.guia_com_det')
    //         ->where('id_guia_com_det',$id_guia_com_det)
    //         ->update(['unitario_adicional'=>$nuevo_adic,
    //                   'total'=>$total]);

    //         //Obtiene ingreso detalle
    //         $ing = DB::table('almacen.mov_alm_det')
    //         ->where([['id_guia_com_det','=',$id_guia_com_det],['estado','!=',7]])
    //         ->first();

    //         //Actualiza valorizacion
    //         if (isset($ing)){
    //             $valor = ($det->unitario + $nuevo_adic) * $ing->cantidad;
    //             $update = DB::table('almacen.mov_alm_det')
    //             ->where('id_guia_com_det',$id_guia_com_det)
    //             ->update([ 'valorizacion' => $valor ]);
    //         }
    //     }
    //     return response()->json($update);
    // }

    // public function tipo_cambio_compra($fecha){
    //     $data = DB::table('contabilidad.cont_tp_cambio')
    //     ->where('cont_tp_cambio.fecha','<=',$fecha)
    //     ->orderBy('fecha','desc')
    //     // ->take(1)->get();
    //     ->first();
    //     return $data->compra;
    // }
    // public function actualiza_totales_doc($por_dscto, $id_doc, $fecha_emision){
    //     $detalle = DB::table('almacen.doc_com_det')
    //     ->select(DB::raw('sum(doc_com_det.precio_total) as sub_total'))
    //     ->where([['id_doc','=',$id_doc],['estado','=',1]])
    //     ->first();

    //     //obtiene IGV
    //     $impuesto = DB::table('contabilidad.cont_impuesto')
    //         ->where([['codigo','=','IGV'],['fecha_inicio','<',$fecha_emision]])
    //         ->orderBy('fecha_inicio','desc')
    //         ->first();

    //     $dscto = $por_dscto * $detalle->sub_total / 100;
    //     $total = $detalle->sub_total - $dscto;
    //     $igv = $impuesto->porcentaje * $total / 100;

    //     //actualiza totales
    //     $data = DB::table('almacen.doc_com')->where('id_doc_com',$id_doc)
    //     ->update([
    //         'sub_total'=>$detalle->sub_total,
    //         'total_descuento'=>$dscto,
    //         'porcen_descuento'=>$por_dscto,
    //         'total'=>$total,
    //         'total_igv'=>$igv,
    //         'total_ant_igv'=>0,
    //         'porcen_igv' => $impuesto->porcentaje,
    //         'porcen_anticipo' => 0,
    //         'total_otros' => 0,
    //         'total_a_pagar'=>($total + $igv)
    //     ]);
    //     return response()->json($data);
    // }

    public function guardar_transferencia(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $codigo = $this->transferencia_nextId($request->id_almacen_origen);
        $id_usuario = Auth::user()->id_usuario;
        $guardar = false;

        if ($request->id_guia_ven !== null) {
            $trans = DB::table('almacen.trans')
                ->where([['id_guia_ven', '=', $request->id_guia_ven], ['estado', '!=', 7]])
                ->first();
            if (isset($trans)) {
                $id_trans = $trans->id_transferencia;
            } else {
                $guardar = true;
            }
        } else {
            $guardar = true;
        }
        if ($guardar) {
            $id_trans = DB::table('almacen.trans')->insertGetId(
                [
                    'id_almacen_origen' => $request->id_almacen_origen,
                    'id_almacen_destino' => $request->id_almacen_destino,
                    'codigo' => $codigo,
                    'id_guia_ven' => $request->id_guia_ven,
                    'responsable_origen' => $request->responsable_origen,
                    'responsable_destino' => $request->responsable_destino,
                    'fecha_transferencia' => $request->fecha_transferencia,
                    'registrado_por' => $id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                'id_transferencia'
            );
        }
        return response()->json(['id_trans' => $id_trans, 'codigo' => $codigo]);
    }
    public function listar_transferencias_pendientes($ori)
    {
        $data = DB::table('almacen.trans')
            ->select(
                'trans.*',
                'guia_ven.fecha_emision as fecha_guia',
                DB::raw("(guia_ven.serie) || '-' || (guia_ven.numero) as guia_ven"),
                DB::raw("(guia_com.serie) || '-' || (guia_com.numero) as guia_com"),
                'alm_origen.descripcion as alm_origen_descripcion',
                'alm_destino.descripcion as alm_destino_descripcion',
                'usu_origen.nombre_corto as nombre_origen',
                'usu_destino.nombre_corto as nombre_destino',
                'usu_registro.nombre_corto as nombre_registro',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color'
            )
            ->leftJoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'trans.id_guia_ven')
            ->leftJoin('almacen.guia_com', 'guia_com.id_guia', '=', 'trans.id_guia_com')
            ->join('almacen.alm_almacen as alm_origen', 'alm_origen.id_almacen', '=', 'trans.id_almacen_origen')
            ->leftJoin('almacen.alm_almacen as alm_destino', 'alm_destino.id_almacen', '=', 'trans.id_almacen_destino')
            ->leftJoin('configuracion.sis_usua as usu_origen', 'usu_origen.id_usuario', '=', 'trans.responsable_origen')
            ->leftJoin('configuracion.sis_usua as usu_destino', 'usu_destino.id_usuario', '=', 'trans.responsable_destino')
            ->join('configuracion.sis_usua as usu_registro', 'usu_registro.id_usuario', '=', 'trans.registrado_por')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'trans.estado')
            ->where([['trans.id_almacen_origen', '=', $ori]])
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function prueba_($id_transferencia)
    {
        $detalle = DB::table('almacen.guia_ven_det')
            ->select('guia_ven_det.*', 'alm_prod.codigo', 'alm_prod.descripcion', 'alm_und_medida.abreviatura')
            ->join('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')
            ->join('almacen.trans', 'trans.id_guia_ven', '=', 'guia_ven.id_guia_ven')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_ven_det.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where('trans.id_transferencia', $id_transferencia)
            ->get();

        $array = [];

        foreach ($detalle as $d) {
            $guia_com_det = DB::table('almacen.guia_com_det')
                ->select('guia_com_det.*')
                ->where([
                    ['guia_com_det.id_guia_ven_det', '=', $d->id_guia_ven_det],
                    ['guia_com_det.estado', '=', 1]
                ])
                ->first();

            $agrega = false;
            $nueva_cant = $d->cantidad;

            if ($guia_com_det !== null && $guia_com_det->cantidad !== null) {
                if ($guia_com_det->cantidad < $d->cantidad) {
                    $agrega = true;
                    $nueva_cant = $d->cantidad - $guia_com_det->cantidad;
                } else {
                    $agrega = false;
                }
            } else {
                $agrega = true;
            }
            array_push($array, $guia_com_det);
        }
        return response()->json(['array' => $array, 'detalle' => $detalle]);
    }
    public function listar_transferencia_detalle($id_transferencia)
    {
        $detalle = DB::table('almacen.guia_ven_det')
            ->select('guia_ven_det.*', 'alm_prod.codigo', 'alm_prod.descripcion', 'alm_und_medida.abreviatura')
            ->join('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')
            ->join('almacen.trans', 'trans.id_guia_ven', '=', 'guia_ven.id_guia_ven')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_ven_det.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where([
                ['trans.id_transferencia', '=', $id_transferencia],
                ['guia_ven_det.estado', '!=', 7]
            ])
            ->get();

        $trans = DB::table('almacen.trans')
            ->where('id_transferencia', $id_transferencia)
            ->first();

        //listar posiciones que no estan enlazadas con ningun producto
        $posiciones = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_ubi_posicion.id_posicion', 'alm_ubi_posicion.codigo')
            ->leftjoin('almacen.alm_prod_ubi', 'alm_prod_ubi.id_posicion', '=', 'alm_ubi_posicion.id_posicion')
            ->leftjoin('almacen.alm_ubi_nivel', 'alm_ubi_nivel.id_nivel', '=', 'alm_ubi_posicion.id_nivel')
            ->leftjoin('almacen.alm_ubi_estante', 'alm_ubi_estante.id_estante', '=', 'alm_ubi_nivel.id_estante')
            ->leftjoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_ubi_estante.id_almacen')
            ->where([
                ['alm_prod_ubi.id_posicion', '=', null],
                ['alm_ubi_posicion.estado', '=', 1],
                ['alm_almacen.id_almacen', '=', $trans->id_almacen_destino]
            ])
            ->get();

        $html = '';
        foreach ($detalle as $d) {
            $o = false;
            //jalar posicion relacionada con el producto
            $posicion = DB::table('almacen.alm_prod_ubi')
                ->select('alm_ubi_posicion.id_posicion', 'alm_ubi_posicion.codigo')
                ->join('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'alm_prod_ubi.id_posicion')
                ->join('almacen.alm_ubi_nivel', 'alm_ubi_nivel.id_nivel', '=', 'alm_ubi_posicion.id_nivel')
                ->join('almacen.alm_ubi_estante', 'alm_ubi_estante.id_estante', '=', 'alm_ubi_nivel.id_estante')
                // ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
                ->where([
                    ['alm_prod_ubi.id_producto', '=', $d->id_producto],
                    ['alm_prod_ubi.estado', '=', 1],
                    ['alm_ubi_estante.id_almacen', '=', $trans->id_almacen_destino]
                ])
                ->get();
            $count = count($posicion);
            if ($count > 0) {
                $posiciones = $posicion;
                $o = true;
            }
            $guia_com_det = DB::table('almacen.guia_com_det')
                ->select('guia_com_det.*')
                ->where([
                    ['guia_com_det.id_guia_ven_det', '=', $d->id_guia_ven_det],
                    ['guia_com_det.estado', '=', 1]
                ])
                ->first();

            $nueva_cant = $d->cantidad;

            if ($guia_com_det !== null && $guia_com_det->cantidad !== null) {
                $nueva_cant = $d->cantidad - $guia_com_det->cantidad;
            }

            // if ($agrega){

            $html .= '
                <tr id="' . $d->id_guia_ven_det . '">
                    <td><input type="checkbox" checked change="onCheck(' . $d->id_guia_ven_det . ');"/></td>
                    <td>' . $d->codigo . '</td>
                    <td>' . $d->descripcion . '</td>
                    <td>' . $d->cantidad . '</td>
                    <td><input type="number" class="input-data right" style="width:80px;" name="cantidad_recibida" value="' . $nueva_cant . '" max="' . $nueva_cant . '"/></td>
                    <td>' . $d->abreviatura . '</td>
                    <td>
                        <select class="input-data" name="id_posicion">
                            <option value="0">Elija una opción</option>';
            foreach ($posiciones as $row) {
                if ($o) {
                    $html .= '<option value="' . $row->id_posicion . '" selected>' . $row->codigo . '</option>';
                } else {
                    $html .= '<option value="' . $row->id_posicion . '">' . $row->codigo . '</option>';
                }
            }
            $html .= '</select>
                    </td>
                    <td><input type="text" class="input-data" name="observacion"/></td>
                </tr>
                ';
            // }
        }
        return json_encode($html);
        // return response()->json($detalle);
    }
    public function proveedor($id_ing_det)
    {
        $guia_com_det = DB::table('almacen.guia_ven_det')
            ->select('guia_com_det.*')
            ->leftjoin('almacen.mov_alm_det', 'mov_alm_det.id_mov_alm_det', '=', 'guia_ven_det.id_ing_det')
            ->leftjoin('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'mov_alm_det.id_guia_com_det')
            ->where('guia_ven_det.id_ing_det', $id_ing_det)
            ->first();
        $est = 'no agrega';
        if ($guia_com_det !== null) {
            if ($guia_com_det->cantidad < 2) {
                $est = 'agrega';
            }
        }
        return response()->json(['guia_com_det' => $guia_com_det, 'est' => $est]);
    }

    public function ingreso_transferencia($id_guia_com)
    {
        $data = DB::table('almacen.mov_alm')
            ->where('id_guia_com', $id_guia_com)->get();
        return response()->json($data);
    }

    public function listar_series_numeros()
    {
        $data = DB::table('almacen.serie_numero')
            ->select(
                'serie_numero.*',
                'sis_usua.nombre_corto',
                'adm_estado_doc.estado_doc',
                DB::raw("(adm_contri.razon_social) || ' - ' || (sis_sede.codigo) as empresa_sede"),
                'cont_tp_doc.descripcion as tipo_doc'
            )
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'serie_numero.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'serie_numero.registrado_por')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'serie_numero.estado')
            ->join('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'serie_numero.id_tp_documento')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_serie_numero($id)
    {
        $data = DB::table('almacen.serie_numero')
            ->select(
                'serie_numero.*',
                'sis_usua.nombre_corto',
                DB::raw("(adm_contri.razon_social) || ' ' || (sis_sede.codigo) as sede_descripcion")
            )
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'serie_numero.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'serie_numero.registrado_por')
            ->where('serie_numero.id_serie_numero', $id)
            ->get();
        return response()->json($data);
    }
    public function guardar_serie_numero(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $usuario = Auth::user()->id_usuario;
        $hasWhere = [];

        if ($request->numero_desde !== null && $request->numero_hasta !== null) {
            $hasWhere[] = [$request->numero_desde, $request->numero_hasta];
        } else {
            $hasWhere[] = [$request->numero, $request->numero];
        }

        $count = DB::table('almacen.serie_numero')
            ->where([
                ['id_tp_documento', '=', $request->id_tp_documento],
                ['id_sede', '=', $request->id_sede],
                ['serie', '=', $request->serie]
            ])
            ->whereBetween(DB::raw("CAST(numero AS INTEGER)"), $hasWhere)
            ->count();

        $rspta = '';
        if ($count == 0) {
            for ($i = $request->numero_desde; $i <= $request->numero_hasta; $i++) {
                $num = AlmacenController::leftZero(7, $i);
                DB::table('almacen.serie_numero')->insertGetId(
                    [
                        'id_tp_documento' => $request->id_tp_documento,
                        'id_sede' => $request->id_sede,
                        'serie' => $request->serie,
                        'numero' => $num,
                        'estado' => 1,
                        'registrado_por' => $usuario,
                        'fecha_registro' => $fecha
                    ],
                    'id_serie_numero'
                );
                $rspta = 'Se guardó las serie-numero con éxito.';
            }
        } else {
            $rspta = 'Ya existen dichas series!';
        }

        return response()->json($rspta);
    }
    public function update_serie_numero(Request $request)
    {
        $rspta = '';
        $count = DB::table('almacen.serie_numero')
            ->where([
                ['id_tp_documento', '=', $request->id_tp_documento],
                ['id_sede', '=', $request->id_sede],
                ['serie', '=', $request->serie],
                ['numero', '=', $request->numero]
            ])
            ->count();

        if ($count == 0) {
            $data = DB::table('almacen.serie_numero')
                ->where('id_serie_numero', $request->id_serie_numero)
                ->update([
                    'id_tp_documento' => $request->id_tp_documento,
                    'id_sede' => $request->id_sede,
                    'serie' => $request->serie,
                    'numero' => $request->numero
                ]);
            $rspta = 'Se actualizó con éxito.';
        } else {
            $rspta = 'Ya existe dicha serie-numero.';
        }
        return response()->json($rspta);
    }
    public function anular_serie_numero($id)
    {
        $data = DB::table('almacen.serie_numero')->where('id_serie_numero', $id)
            ->update(['estado' => 7]);
        return response()->json($data);
    }

    public function next_serie_numero_guia($id_sede, $id_tp_doc)
    {
        $tp_doc = DB::table('almacen.tp_doc_almacen')
            ->where('id_tp_doc_almacen', $id_tp_doc)
            ->first();

        $data = DB::table('almacen.serie_numero')
            ->select('serie_numero.*')
            ->where([
                ['id_sede', '=', $id_sede],
                ['id_tp_documento', '=', $tp_doc->id_tp_doc],
                ['estado', '=', 1]
            ])
            ->orderBy('numero', 'asc')
            ->first();

        if (isset($data)) {
            return response()->json($data);
        } else {
            return response()->json('');
        }
    }

    public function next_serie_numero_doc($id_sede, $id_tp_doc)
    {
        $data = DB::table('almacen.serie_numero')
            ->select('serie_numero.*')
            ->where([
                ['id_sede', '=', $id_sede],
                ['id_tp_documento', '=', $id_tp_doc],
                ['estado', '=', 1]
            ])
            ->orderBy('numero', 'asc')
            ->first();

        if (isset($data)) {
            return response()->json($data);
        } else {
            return response()->json('');
        }
    }

    public function copiar_items_occ_doc($id, $id_doc)
    {
        // $detalle = DB::connection('mgcp')->table('ordenes_compra')
        // ->select('orden_publica_detalles.*','ordenes_compra.id','productos_am.descripcion',
        // 'ordenes_compra.fecha_entrega','ordenes_compra.lugar_entrega')
        // ->leftjoin('orden_publica_detalles','orden_publica_detalles.id_oc','=','ordenes_compra.orden_compra')
        // ->leftjoin('productos_am','productos_am.id','=','orden_publica_detalles.id_producto')
        // ->where('ordenes_compra.id',$id)
        // ->get();

        $detalle = DB::table('logistica.log_det_ord_compra')
            ->select(
                'log_det_ord_compra.*',
                'log_valorizacion_cotizacion.precio_cotizado',
                'log_valorizacion_cotizacion.cantidad_cotizada',
                'log_valorizacion_cotizacion.id_unidad_medida',
                'log_valorizacion_cotizacion.porcentaje_descuento',
                'log_valorizacion_cotizacion.monto_descuento',
                'log_valorizacion_cotizacion.subtotal',
                DB::raw("(CASE
        WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion
        WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion
        WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion
        ELSE 'nulo' END) AS descripcion
        "),
                DB::raw("(CASE
        WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.codigo
        WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.codigo
        WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.codigo
        ELSE 'nulo' END) AS codigo
        "),
                DB::raw("(CASE
        WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.abreviatura
        WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'serv'
        WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'und'
        ELSE 'nulo' END) AS unidad_medida
        ")
            )
            ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            ->join('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', 'log_det_ord_compra.id_valorizacion_cotizacion')
            ->leftjoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftjoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftjoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
            ->where([['log_ord_compra.id_occ', '=', $id], ['log_ord_compra.estado', '!=', 7]])
            ->get();

        $html = '';

        foreach ($detalle as $det) {
            $id_doc_det = DB::table('almacen.doc_ven_det')->insertGetId(
                [
                    'id_doc' => $id_doc,
                    'id_item' => $det->id_item,
                    'cantidad' => $det->cantidad_cotizada,
                    'id_unid_med' => $det->id_unidad_medida,
                    'precio_unitario' => floatval($det->precio_cotizado),
                    'sub_total' => ($det->cantidad_cotizada * floatval($det->precio_cotizado)),
                    'porcen_dscto' => $det->porcentaje_descuento,
                    'total_dscto' => $det->monto_descuento,
                    'precio_total' => $det->subtotal,
                    'id_oc_det' => $det->id_detalle_orden,
                    'estado' => 1,
                    'fecha_registro' => date('Y-m-d H:i:s')
                ],
                'id_doc_det'
            );

            $html .= '
            <tr id="' . $id_doc_det . '">
                <td>' . $det->codigo . '</td>
                <td>' . $det->descripcion . '</td>
                <td>' . $det->cantidad_cotizada . '</td>
                <td>' . $det->unidad_medida . '</td>
                <td>' . $det->precio_cotizado . '</td>
                <td>' . $det->porcentaje_descuento . '</td>
                <td>' . $det->monto_descuento . '</td>
                <td>' . $det->subtotal . '</td>
                <td style="display:flex;">
                    <i class="fas fa-pen-square icon-tabla blue boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_detalle(' . $id_doc_det . ');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_detalle(' . $id_doc_det . ');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_detalle(' . $id_doc_det . ');"></i>
                </td>
            </tr>
            ';
        }
        return json_encode($html);
        // return response()->json($detalle);
    }

    public function actualiza_totales_doc_ven($id_doc)
    {
        $doc_ven = DB::table('almacen.doc_ven_det')
            ->select(
                DB::raw('sum(doc_ven_det.precio_total) as suma'),
                'doc_ven.fecha_emision',
                'doc_ven.total_descuento',
                'doc_ven.porcen_descuento',
                'doc_ven.total',
                'doc_ven.total_igv',
                'doc_ven.total_ant_igv',
                'doc_ven.total_a_pagar'
            )
            ->join('almacen.doc_ven', 'doc_ven.id_doc_ven', '=', 'doc_ven_det.id_doc')
            ->where('id_doc', $id_doc)
            ->groupBy(
                'doc_ven.fecha_emision',
                'doc_ven.total_descuento',
                'doc_ven.porcen_descuento',
                'doc_ven.total',
                'doc_ven.total_igv',
                'doc_ven.total_ant_igv',
                'doc_ven.total_a_pagar'
            )
            ->first();

        $igv = $this->mostrar_impuesto('IGV', $doc_ven->fecha_emision);
        $total_igv = $igv->porcentaje / 100 * $doc_ven->suma;

        $data = DB::table('almacen.doc_ven')->where('id_doc_ven', $id_doc)
            ->update([
                'sub_total' => $doc_ven->suma,
                'total_descuento' => ($doc_ven->total_descuento !== null ? $doc_ven->total_descuento : 0),
                'porcen_descuento' => ($doc_ven->porcen_descuento !== null ? $doc_ven->porcen_descuento : 0),
                'total_igv' => $total_igv,
                'total' => ($doc_ven->suma + $total_igv),
                'total_ant_igv' => ($doc_ven->total_ant_igv !== null ? $doc_ven->total_ant_igv : 0),
                'total_a_pagar' => ($doc_ven->suma + $total_igv)
            ]);
        return response()->json($data);
    }
    public function listar_occ()
    {
        $data = DB::connection('mgcp')->table('ordenes_compra')
            ->select('ordenes_compra.*', 'entidades.ruc', 'entidades.entidad')
            ->leftjoin('entidades', 'entidades.id', '=', 'ordenes_compra.id_entidad')
            ->where('estado_am', 'ACEPTADA C/ENTREGA PENDIENTE')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function migrar_docs_compra()
    {

        $data = DB::table('almacen.doc_com')
            ->select('doc_com.*', 'cont_tp_doc.abreviatura', 'adm_contri.nro_documento')
            ->join('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_com.id_tp_doc')
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'doc_com.id_proveedor')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->where([['doc_com.fecha_emision', '>=', '2019-11-09'], ['doc_com.estado', '=', 1]])
            ->get();


        foreach ($data as $d) {
            $guia = DB::table('almacen.doc_com_guia')
                ->select('guia_com.*', 'alm_almacen.codigo as cod_almacen')
                ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'doc_com_guia.id_guia_com')
                ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'guia_com.id_almacen')
                ->where('id_doc_com', $d->id_doc_com)
                ->first();

            $prove = DB::connection('soft')->table('auxiliar')
                ->where('ruc_auxi', $d->nro_documento)
                ->first();

            // if (isset($prove)){

            // }

            $guardar = DB::connection('soft')->table('movimien')->insertGetId(
                [
                    'tipo' => 1, //decimal(1,0) NOT NULL DEFAULT '0' COMMENT 'Tipo de Aplicacion 1=Compras 2=Ventas',
                    'cod_suc' => 1, //char(1) NOT NULL DEFAULT '' COMMENT 'ID de Sucursal',
                    'cod_alma' => $guia->cod_almacen, //char(3) NOT NULL DEFAULT '' COMMENT 'ID de Almacen',
                    'cod_docu' => $d->abreviatura, //char(2) NOT NULL DEFAULT '' COMMENT 'ID de Documento',
                    'num_docu' => ($d->serie . $d->numero), // char(11) NOT NULL DEFAULT '' COMMENT 'Numero de Documento',
                    'fec_docu' => $d->fecha_emision, //date NOT NULL COMMENT 'Fecha de Emision',
                    'fec_entre' => $d->fecha_emision, //date NOT NULL COMMENT 'Fecha de Entrega',
                    'fec_vcto' => $d->fecha_vcmto, //date NOT NULL COMMENT 'Fecha de Vencimiento',
                    'flg_sitpedido' => 0, //???? bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag que Indica si es de Seguimiento',
                    'cod_pedi' => 'GR', //char(2) NOT NULL DEFAULT '' COMMENT 'ID de Referencia',
                    'num_pedi' => ($guia->serie . $guia->numero), //char(11) NOT NULL DEFAULT '' COMMENT 'Numero de Referencia',
                    'cod_auxi' => (isset($prove) ? $prove->cod_auxi : ''), //char(6) NOT NULL DEFAULT '' COMMENT 'ID de Cliente o Proveedor',
                    'cod_trans' => '00000', //char(5) NOT NULL DEFAULT '00000' COMMENT 'ID de Transportista',
                    'cod_vend' => '000001', //char(6) NOT NULL DEFAULT '' COMMENT 'ID de Vendedor',
                    'tip_mone' => $d->moneda, //decimal(1,0) NOT NULL DEFAULT '0' COMMENT 'Moneda de Doc. 1=Soles 2=Dolares',
                    'impto1' => $d->porcen_igv, //decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Porct. % Impuesto 1',
                    'impto2' => 0, //decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Porct. % Impuesto 2',
                    'mon_bruto' => $d->sub_total, //decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto Neto',
                    'mon_impto1' => $d->total_igv, //decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto de Impuestos 1',
                    'mon_impto2' => 0, //decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto de Impuestos 2',
                    'mon_gravado' => $d->sub_total, //decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto de Gravado',
                    'mon_inafec' => 0, //decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto de Inafectos',
                    'mon_exonera' => 0, //decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto de Exonerado',
                    'mon_gratis' => 0, //decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto de Gratutito',
                    'mon_total' => $d->total_a_pagar, //decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto Total',
                    'sal_docu' => 0, //???decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Saldo de Documento',
                    'tot_cargo' => 0, //???decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Total Cargos',
                    'tot_percep' => 0, //???decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Total Percepcion',
                    'tip_codicion' => '02', //?????char(2) NOT NULL DEFAULT '' COMMENT 'ID de Condicion',
                    'txt_observa' => '', //varchar(250) NOT NULL DEFAULT ' ' COMMENT 'Notas de Documento',
                    'flg_kardex' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag que indica si actualizo Kardex',
                    'flg_anulado' => ($d->estado == 7 ? 1 : 0), //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag que indica si esta Anulado',
                    'flg_referen' => 0, //????bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag que indica si esta Referenciado',
                    'flg_percep' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag que indica si hace percepcion',
                    'cod_user' => '000001', //char(6) NOT NULL DEFAULT '' COMMENT 'ID de Usuario de sistema',
                    'programa' => '', //char(1) NOT NULL DEFAULT '' COMMENT 'Valor sin Uso',
                    'txt_nota' => '', //varchar(100) NOT NULL DEFAULT ' ' COMMENT 'Notas de Documento',
                    'tip_cambio' => $d->tipo_cambio, //decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT 'Tipo de Cambio',
                    'tdflags' => 'NSSNNSSSSN', //?????char(12) NOT NULL DEFAULT '' COMMENT 'Flags de Configuraciones',
                    'numlet' => '', //varchar(150) NOT NULL DEFAULT ' ' COMMENT 'Importe Total en Letras',
                    'impdcto' => $d->total_descuento, //decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'Importe del Descuento',
                    'impanticipos' => $d->total_ant_igv, //decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'Importe de Anticipo',
                    'registro' => $d->fecha_registro, //timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha Hora de creacion',
                    'tipo_canje' => 0, // decimal(1,0) NOT NULL DEFAULT '0' COMMENT 'Tipo de Canje Letras',
                    'numcanje' => 0, // varchar(11) NOT NULL DEFAULT '' COMMENT 'Numero de Canje de Letras',
                    'cobrobco'  => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flags que Indica si la Letra esta en Banco',
                    'ctabco'  => '', //char(3) NOT NULL DEFAULT '' COMMENT 'Cuenta de Banco donde se encuentra el Doc.',
                    'flg_qcont' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag que se ha Contabilizado / Anticipo cerrado',
                    'fec_anul' => '0000-00-00', //date NOT NULL COMMENT 'Fecha de Anulado del Doc.',
                    'audit' => 2, //decimal(1,0) NOT NULL DEFAULT '0' COMMENT 'Valor de Auditoria',
                    'origen' => '', //char(1) NOT NULL DEFAULT '' COMMENT 'Valor en caso sea de Importacion',
                    'tip_cont' => '', //char(2) NOT NULL DEFAULT '' COMMENT 'ID de Tipo de Contrato',
                    'tip_fact' => '', //char(2) NOT NULL DEFAULT '' COMMENT 'ID de Tipo de Facturacion',
                    'contrato' => '', //varchar(13) NOT NULL DEFAULT '' COMMENT 'ID y Numero de Contrato',
                    'idcontrato' => '', //varchar(10) NOT NULL DEFAULT '' COMMENT 'ID Prinicpal del Contrato',
                    'canje_fact' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag que indica que esta canjeado x una Factura',
                    'aceptado' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag de Aprobacion',
                    'reg_conta' => 0, //decimal(10,0) NOT NULL DEFAULT '0' COMMENT 'Nro de Registro Contable',
                    'mov_pago' => '', //varchar(10) NOT NULL DEFAULT ' ' COMMENT 'ID Prog de Pago / Estado de Letra',
                    'ndocu1' => '', //varchar(25) NOT NULL DEFAULT ' ' COMMENT 'Documento 1',
                    'ndocu2' => '', //varchar(25) NOT NULL DEFAULT ' ' COMMENT 'Documento 2',
                    'ndocu3' => '', //varchar(25) NOT NULL DEFAULT ' ' COMMENT 'Documento 3',
                    'flg_logis' => 0, //???bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag de Stock Pedido y en Transito',
                    'cod_recep' => '', //char(6) NOT NULL DEFAULT '' COMMENT 'ID de Receptor',
                    'flg_aprueba' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag de Transf. Aprobada',
                    'fec_aprueba' => '0000-00-00 00:00:00', // datetime NOT NULL COMMENT 'Fecha de Aprobacion de Transf.',
                    'flg_limite' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag para saber si afecta el Limite de credito',
                    'fecpago' => '0000-00-00', //date NOT NULL COMMENT 'Fecha de Cancelacion',
                    'imp_comi' => 0, //decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Importe de Comision',
                    'ptosbonus' => 0, //decimal(5,0) NOT NULL DEFAULT '0' COMMENT 'Ptos. ganados por documento',
                    'canjepedtran' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag que indica que el documento canjeo el Stock Comprometido',
                    'cod_clasi' => $guia->id_guia_clas, //char(1) NOT NULL DEFAULT '' COMMENT 'ID de Bien y/o Servicio',
                    'doc_elec' => '', //varchar(2) NOT NULL DEFAULT ' ' COMMENT 'ID doc.Elect. SUNAT',
                    'cod_nota' => '', //varchar(2) NOT NULL DEFAULT ' ' COMMENT 'ID de tipo de NC-ND',
                    'hashcpe' => '', //varchar(50) NOT NULL DEFAULT ' ' COMMENT 'Hash Sunat CPE',
                    'flg_sunat_acep' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag Aceptado en Sunat',
                    'flg_sunat_anul' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag Anulado en Sunat',
                    'flg_sunat_mail' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag Email enviado',
                    'flg_sunat_webs' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag Publicado Web custodia',
                    'mov_id_baja' => '', //varchar(10) NOT NULL DEFAULT ' ' COMMENT 'ID Comunicacion de baja',
                    'mov_id_resu_bv' => '', //varchar(10) NOT NULL DEFAULT ' ' COMMENT 'ID Resumen diario BV',
                    'mov_id_resu_ci' => '', //varchar(10) NOT NULL DEFAULT ' ' COMMENT 'ID Resumen comprobante impreso',
                    'flg_guia_traslado' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag Documento Guia Traslado',
                    'flg_anticipo_doc' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag Anticipo Recibido',
                    'flg_anticipo_reg' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag Anticipo Regularizacion',
                    'doc_anticipo_id' => '', //varchar(10) NOT NULL DEFAULT ' ' COMMENT 'MovID de doc. de anticipo',
                    'flg_emi_itinerante' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag Emisor Itinerante BOLETA',
                    'placa' => ''

                ],
                'mov_id' //???char(10) NOT NULL DEFAULT '' COMMENT 'ID Principal SYS(2015) VFP',
            );
        }

        return response()->json($guardar);
    }

    public function imprimir_guia_venta($id_guia_ven)
    {
        $id_guia = $this->decode5t($id_guia_ven);
        $data = DB::table('almacen.guia_ven')
            ->select(
                'guia_ven.*',
                'adm_contri.razon_social as cli_razon_social',
                'contri.nro_documento as emp_ruc',
                'adm_contri.nro_documento as cli_ruc',
                'adm_empresa.id_empresa',
                DB::raw("(ubi_dis.descripcion) || '-' || (ubi_prov.descripcion) || '-' || (ubi_dpto.descripcion) as ubigeo_cliente")
            )
            ->leftjoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'guia_ven.id_cliente')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->leftjoin('configuracion.ubi_dis', 'ubi_dis.id_dis', '=', 'adm_contri.ubigeo')
            ->leftjoin('configuracion.ubi_prov', 'ubi_prov.id_prov', '=', 'ubi_dis.id_prov')
            ->leftjoin('configuracion.ubi_dpto', 'ubi_dpto.id_dpto', '=', 'ubi_prov.id_dpto')
            ->leftjoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'guia_ven.id_sede')
            ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->leftjoin('contabilidad.adm_contri as contri', 'contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->where('id_guia_ven', $id_guia)
            ->first();

        $detalle = DB::table('almacen.guia_ven_det')
            ->select(
                'guia_ven_det.*',
                'alm_prod.codigo as cod_producto',
                'alm_prod.descripcion as des_producto',
                'alm_ubi_posicion.codigo as cod_posicion',
                'alm_und_medida.abreviatura'
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_ven_det.id_producto')
            ->join('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'guia_ven_det.id_posicion')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'guia_ven_det.id_unid_med')
            ->where([
                ['guia_ven_det.id_guia_ven', '=', $id_guia],
                ['guia_ven_det.estado', '=', 1]
            ])
            ->get();

        $nuevo_detalle = [];

        foreach ($detalle as $det) {
            $exist = false;
            foreach ($nuevo_detalle as $nue => $value) {
                if ($det->id_producto == $value['id_producto']) {
                    $nuevo_detalle[$nue]['cantidad'] = floatval($value['cantidad']) + floatval($det->cantidad);
                    // $nuevo_detalle[$nue]['valorizacion'] = floatval($value['valorizacion']) + floatval($det->total);
                    $series = DB::table('almacen.alm_prod_serie')
                        ->where([
                            ['id_guia_ven_det', '=', $det->id_guia_ven_det],
                            ['estado', '=', 1]
                        ])
                        ->get();
                    $imp_series = '';
                    if (isset($series)) {
                        foreach ($series as $se) {
                            if ($imp_series == '') {
                                $imp_series .= $se->serie;
                            } else {
                                $imp_series .= ', ' . $se->serie;
                            }
                        }
                    }
                    $nuevo_detalle[$nue]['series'] = $value['series'] . ', ' . $imp_series;
                    $exist = true;
                }
            }
            if ($exist === false) {
                $series = DB::table('almacen.alm_prod_serie')
                    ->where([
                        ['id_guia_ven_det', '=', $det->id_guia_ven_det],
                        ['estado', '=', 1]
                    ])
                    ->get();
                $imp_series = '';
                if (isset($series)) {
                    foreach ($series as $se) {
                        if ($imp_series == '') {
                            $imp_series .= $se->serie;
                        } else {
                            $imp_series .= ', ' . $se->serie;
                        }
                    }
                }
                $nuevo = [
                    'id_guia_ven_det' => $det->id_guia_ven_det,
                    'id_producto' => $det->id_producto,
                    'id_posicion' => $det->id_posicion,
                    'cod_producto' => $det->cod_producto,
                    'des_producto' => $det->des_producto,
                    'cod_posicion' => $det->cod_posicion,
                    'abreviatura' => $det->abreviatura,
                    'series' => $imp_series,
                    'cantidad' => floatval($det->cantidad)
                ];
                array_push($nuevo_detalle, $nuevo);
            }
        }

        $html = '';
        if ($data->id_empresa == 1) { // 1 Ok Computer
            $html = $this->guia_ok_computer($data, $nuevo_detalle);
        }

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->stream();
        return $pdf->download('guia_ven.pdf');
        // return $detalle;
    }

    public function guia_ok_computer($data, $nuevo_detalle)
    {
        $html = '
        <html>
            <head>
                <style type="text/css">
                *{
                    font-family: "DejaVu Sans";
                }
                table{
                    width:100%;
                    font-size:11px;
                }
                #detalle thead{
                    padding: 4px;
                    background-color: #e5e5e5;
                }
                #detalle tbody tr td,
                #detalle tfoot tr td{
                    font-size:11px;
                    padding: 4px;
                }
                #detalle tfoot{
                    border-top: 1px dashed #343a40;
                }
                .right{
                    text-align: right;
                }
                .sup{
                    vertical-align:top;
                }
                .blanco{
                    color:#fff;
                }
                </style>
            </head>
            <body>
                <table width="100%">
                    <tbody>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr>
                            <td colSpan="3" class="blanco">.</td>
                            <td width="280px" colSpan="4">' . $data->fecha_emision . '</td>
                            <td>.</td>
                            <td>' . $data->serie . '-' . $data->numero . '</td>
                        </tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr>
                            <td colSpan="2" class="blanco">.</td>
                            <td colSpan="4">' . $data->punto_partida . '</td>
                            <td width="90px" class="blanco">.</td>
                            <td>' . $data->cli_razon_social . '</td>
                        </tr>
                        <tr>
                            <td width="80px" class="blanco">.</td>
                            <td colSpan="5">' . $data->emp_ruc . '</td>
                            <td class="blanco">.</td>
                            <td>' . $data->punto_llegada . '</td>
                        </tr>
                        <tr>
                            <td colSpan="4" class="blanco">.</td>
                            <td colSpan="2">' . $data->fecha_traslado . '</td>
                            <td class="blanco">.</td>
                            <td>' . $data->cli_ruc . '  ' . $data->ubi_descripcion . '</td>
                        </tr>
                    </tbody>
                </table>
                <br/>
                <table id="detalle">
                    <tbody>
                        <tr><td colSpan="4" class="blanco">.</td></tr>
                        <tr><td colSpan="4" class="blanco">.</td></tr>
                        <tr><td colSpan="4" class="blanco">.</td></tr>';

        foreach ($nuevo_detalle as $det) {
            $html .= '
                        <tr>
                            <td class="sup">' . $det['cod_producto'] . '</td>
                            <td class="sup">' . $det['cantidad'] . '</td>
                            <td class="sup">' . trim($det['abreviatura']) . '</td>
                            <td class="sup">' . $det['des_producto'] . '. Series: ' . $det['series'] . '</td>
                        </tr>';
        }
        $html .= '</tbody>
                </table>
                <br/>
            </body>
        </html>';

        return $html;
    }

    public function guia_proyectec($data, $nuevo_detalle)
    {
        $dia = date("d", strtotime($data->fecha_emision));
        $mes = date("m", strtotime($data->fecha_emision));
        $anio = date("Y", strtotime($data->fecha_emision));
        $html = '
        <html>
            <head>
                <style type="text/css">
                *{
                    font-family: "DejaVu Sans";
                }
                table{
                    width:100%;
                    font-size:11px;
                }
                #detalle thead{
                    padding: 4px;
                    background-color: #e5e5e5;
                }
                #detalle tbody tr td,
                #detalle tfoot tr td{
                    font-size:11px;
                    padding: 4px;
                }
                #detalle tfoot{
                    border-top: 1px dashed #343a40;
                }
                .right{
                    text-align: right;
                }
                .sup{
                    vertical-align:top;
                }
                .blanco{
                    color:#fff;
                }
                </style>
            </head>
            <body>
                <table width="100%">
                    <tbody>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr>
                            <td colSpan="3" class="blanco">.</td>
                            <td width="280px" colSpan="4">' . $dia . '   ' . $mes . '   ' . $anio . '</td>
                            <td>.</td>
                            <td>' . $data->serie . '-' . $data->numero . '</td>
                        </tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr>
                            <td colSpan="2" class="blanco">.</td>
                            <td colSpan="4">' . $data->punto_partida . '</td>
                            <td width="90px" class="blanco">.</td>
                            <td>' . $data->punto_llegada . '</td>
                        </tr>
                        <tr>
                            <td width="80px" class="blanco">.</td>
                            <td colSpan="5">' . $data->emp_ruc . '</td>
                            <td class="blanco">.</td>
                            <td>' . $data->punto_llegada . '</td>
                        </tr>
                        <tr>
                            <td colSpan="4" class="blanco">.</td>
                            <td colSpan="2">' . $data->fecha_traslado . '</td>
                            <td class="blanco">.</td>
                            <td>' . $data->cli_ruc . '  ' . $data->ubi_descripcion . '</td>
                        </tr>
                    </tbody>
                </table>
                <br/>
                <table id="detalle">
                    <tbody>
                        <tr><td colSpan="4" class="blanco">.</td></tr>
                        <tr><td colSpan="4" class="blanco">.</td></tr>
                        <tr><td colSpan="4" class="blanco">.</td></tr>';

        foreach ($nuevo_detalle as $det) {
            $html .= '
                        <tr>
                            <td class="sup">' . $det['cod_producto'] . '</td>
                            <td class="sup">' . $det['cantidad'] . '</td>
                            <td class="sup">' . trim($det['abreviatura']) . '</td>
                            <td class="sup">' . $det['des_producto'] . '. Series: ' . $det['series'] . '</td>
                        </tr>';
        }
        $html .= '</tbody>
                </table>
                <br/>
            </body>
        </html>';

        return $html;
    }

    public function listar_documentos_adicionales()
    {
        $data = DB::table('almacen.guia_com_prorrateo')
            ->select('guia_com_prorrateo.*', 'doc_com.serie', 'doc_com.numero')
            //terminar referencias
            ->leftjoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'guia_com_prorrateo.id_doc_com')
            ->get();
        return response()->json($data);
    }
    public function listar_ubigeos()
    {
        $data = DB::table('configuracion.ubi_dis')
            ->select('ubi_dis.*', 'ubi_prov.descripcion as provincia', 'ubi_dpto.descripcion as departamento')
            ->join('configuracion.ubi_prov', 'ubi_prov.id_prov', '=', 'ubi_dis.id_prov')
            ->join('configuracion.ubi_dpto', 'ubi_dpto.id_dpto', '=', 'ubi_prov.id_dpto')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    function view_stock_series()
    {
        return view('almacen/reportes/stock_series');
    }


    public function obtener_data_stock_series(){
        set_time_limit(0);

        $stockSeries = StockSeriesView::where('estado','!=',7)->orderBy('fecha_ingreso','desc')->get();

        $data=[];
        foreach($stockSeries as $element){
            $data[]=[
                'almacen'=>$element->almacen??'',
                'codigo_producto'=>$element->codigo_producto??'',
                'part_number'=>$element->part_number??'',
                'serie'=>$element->serie??'',
                'descripcion'=>$element->descripcion??'',
                'unidad_medida'=>$element->unidad_medida??'',
                'afecto_igv'=>$element->afecto_igv??'',
                'fecha_ingreso'=>$element->fecha_ingreso??'',
                'guia_fecha_emision'=>$element->guia_fecha_emision??'',
                'documento_compra'=>$element->documento_compra??''
            ];
        }
        return $data;
        // return response()->json($data);

    }
    public function listar_stock_series(){
        $data = StockSeriesView::where('estado','!=',7);
        return DataTables::of($data)
        ->editColumn('fecha_ingreso', function ($data) {
            return date('d-m-Y', strtotime($data->fecha_ingreso));
        })
        ->editColumn('guia_fecha_emision', function ($data) {
            return date('d-m-Y', strtotime($data->guia_fecha_emision));
        })
        ->filterColumn('fecha_ingreso', function ($query, $keyword) {
            $keywords = date('Y-m-d', strtotime($keyword));
            $query->where('stock_series_view.fecha_ingreso', '>=', $keywords.' 00:00:00')->where('stock_series_view.fecha_ingreso', '<=', $keywords.' 23:59:59');
        })
        ->filterColumn('guia_fecha_emision', function ($query, $keyword) {
            $keywords = date('Y-m-d', strtotime($keyword));
            $query->where('stock_series_view.guia_fecha_emision', '>=', $keywords.' 00:00:00')->where('stock_series_view.guia_fecha_emision', '<=', $keywords.' 23:59:59');
        })
        ->make(true);
    }

    public function exportar_stock_series_excel()
    {
        return Excel::download(new ReporteStockSeriesExcel(), 'stock_series.xlsx');
    }

    ////////////////////////////////////////
    public static function leftZero($lenght, $number)
    {
        $nLen = strlen($number);
        $zeros = '';
        for ($i = 0; $i < ($lenght - $nLen); $i++) {
            $zeros = $zeros . '0';
        }
        return $zeros . $number;
    }
    // public function tipo_cambio(Request $request){
    //     $data = file_get_contents('https://api.sunat.cloud/cambio/'.$request->fecha);
    // $info = json_decode($data, true);
    // if ($data === '[]' || $info['fecha_inscripcion'] === '--'){
    //     $datos = array(0 => 'nada');
    // } else {
    //     $datos = array(
    //         0 => $info['compra'],
    //         1 => $info['venta']
    //     );
    // }
    //     return json_encode($data);
    // }
    public function encode5t($str)
    {
        for ($i = 0; $i < 5; $i++) {
            $str = strrev(base64_encode($str));
        }
        return $str;
    }

    public static function decode5t($str)
    {
        for ($i = 0; $i < 5; $i++) {
            $str = base64_decode(strrev($str));
        }
        return $str;
    }
    public function scripCategoria()
    {
        $clasificacion = array(
            array(
                "codigo"=>"00",
                "descripcion"=>"*OTROS",
                "categorias"=>array(
                    array(
                        "codigo"=>"00",
                        "descripcion"=>"*OTROS",
                        "sub_categoria"=>array(
                            array("codigo"=>"00","descripcion"=>"*OTROS"),
                        ),
                    ),
                ),
            ),
            array(
                "codigo"=>"01",
                "descripcion"=>"ALIMENTOS Y BEBIDAS DE CONSUMO HUMANO",
                "categorias"=>array(
                    array(
                        "codigo"=>"01",
                        "descripcion"=>"ALIMENTOS Y BEBIDAS DE CONSUMO HUMANO",
                        "sub_categoria"=>array(
                            array(
                                "codigo"=>"01",
                                "descripcion"=>"ALIMENTOS Y BEBIDAS DE CONSUMO HUMANO",
                            ),
                        ),
                    ),
                ),
            ),
            array(
                "codigo"=>"02",
                "descripcion"=>"EQUIPOS ELECTRICOS",
                "categorias"=>array(
                    array(
                        "codigo"=>"01",
                        "descripcion"=>"ACCESORIOS DE EQUIPOS ELÉTRICOS",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"CABLE DE PODER"),
                        ),
                    ),
                    array(
                        "codigo"=>02,
                        "descripcion"=>"EQUIPOS DE PROTECCION ELECTRICA",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"BATERIAS PARA UPS"),
                            array("codigo"=>"02","descripcion"=>"ESTABILIZADORES"),
                            array("codigo"=>"03","descripcion"=>"SUPRESOR DE PICOS"),
                            array("codigo"=>"04","descripcion"=>"TRANSFORMADORES"),
                            array("codigo"=>"05","descripcion"=>"UPS"),
                        ),
                    ),
                    array(
                        "codigo"=>03,
                        "descripcion"=>"EQUIPOS DE SISTEMA FOTOVOLTAICO",
                        "sub_categoria"=>array(
                            array("codigo"=>"01", "descripcion"=>"PANEL SOLAR"),
                            array("codigo"=>"02", "descripcion"=>"INMERSORES"),
                            array("codigo"=>"03", "descripcion"=>"CONTROLADORES"),
                            array("codigo"=>"04", "descripcion"=>"PARARAYOS"),
                            array("codigo"=>"05", "descripcion"=>"BATERIAS PARA PLACAS SOLARES"),

                        )
                    ),
                )
            ),
            array(
                "codigo"=>"03",
                "descripcion"=>"EQUIPOS, APARATOS Y ACCESORIOS INFORMÁTICOS",
                "categorias"=>array(
                    array(
                        "codigo"=>"01",
                        "descripcion"=>"ACCESORIOS DE COMPUTO",
                        "sub_categoria"=>array(
                            array("codigo"=>"07", "descripcion"=>"CARRITO PARA PORTATILES"),
                            array("codigo"=>"08", "descripcion"=>"ESCRITORIO CLASICO PARA ORDENADOR"),
                            array("codigo"=>"09", "descripcion"=>"ESCRITORIO GAMER"),
                            array("codigo"=>"10", "descripcion"=>"FUNDA"),
                            array("codigo"=>"11", "descripcion"=>"MALETIN"),
                            array("codigo"=>"12", "descripcion"=>"MANDOS DE JUEGO"),
                            array("codigo"=>"13", "descripcion"=>"MICRÓFONO"),
                            array("codigo"=>"14", "descripcion"=>"MOCHILA"),
                            array("codigo"=>"15", "descripcion"=>"MOUSE CLÁSICO ALÁMBRICO"),
                            array("codigo"=>"16", "descripcion"=>"MOUSE CLÁSICO INALÁMBRICO"),
                            array("codigo"=>"17", "descripcion"=>"MOUSE GAMER"),
                            array("codigo"=>"18", "descripcion"=>"PARLANTES CLASICOS"),
                            array("codigo"=>"19", "descripcion"=>"PARLANTES GAMER"),
                            array("codigo"=>"20", "descripcion"=>"REPLICADOR DE PUERTOS"),
                            array("codigo"=>"21", "descripcion"=>"SILLA CLASICA PARA ORDENADOR"),
                            array("codigo"=>"22", "descripcion"=>"SILLA GAMER"),
                            array("codigo"=>"23", "descripcion"=>"TECLADO CLÁSICO ALÁMBRICO"),
                            array("codigo"=>"24", "descripcion"=>"TECLADO CLÁSICO INALÁMBRICO"),
                            array("codigo"=>"25", "descripcion"=>"TECLADO GAMER"),
                        )
                    ),
                    array(
                        "codigo"=>"02",
                        "descripcion"=>"COMPONENTES DE COMPUTADORA",
                        "sub_categoria"=>array(
                            array("codigo"=>"01", "descripcion"=>"BATERIA PARA COMPUTADORA"),
                            array("codigo"=>"02", "descripcion"=>"CASES"),
                            array("codigo"=>"03", "descripcion"=>"COOLERS"),
                            array("codigo"=>"04", "descripcion"=>"FUENTE DE PODER"),
                            array("codigo"=>"05", "descripcion"=>"LECTORAS"),
                            array("codigo"=>"08", "descripcion"=>"PLACA MADRE"),
                            array("codigo"=>"09", "descripcion"=>"OTROS COMPONENTES"),
                            array("codigo"=>"10", "descripcion"=>"TARJETA BLUETOOH"),
                            array("codigo"=>"11", "descripcion"=>"TARJETA DE VIDEO"),
                            array("codigo"=>"12", "descripcion"=>"TARJETAS PCI"),
                        )
                    ),
                    array(
                        "codigo"=>"03",
                        "descripcion"=>"DISPOSITIVOS DE ALMACENAMIENTO DE DATOS",
                        "sub_categoria"=>array(
                            array("codigo"=>"01", "descripcion"=>"ACCESORIOS PARA DISCOS DUROS"),
                            array("codigo"=>"02", "descripcion"=>"CINTA LTO"),
                            array("codigo"=>"03", "descripcion"=>"DISCO DURO MECÁNICO EXTERNO 2.5''"),
                            array("codigo"=>"04", "descripcion"=>"DISCO DURO MECÁNICO EXTERNO 3.5''"),
                            array("codigo"=>"05", "descripcion"=>"DISCO DURO MECÁNICO INTERNO 2.5''"),
                            array("codigo"=>"06", "descripcion"=>"DISCO DURO MECÁNICO INTERNO 3.5''"),
                            array("codigo"=>"07", "descripcion"=>"DISCO DURO PARA SERVIDOR"),
                            array("codigo"=>"08", "descripcion"=>"DISCO DURO PARA SISTEMA DE ALMACENAMIENTO"),
                            array("codigo"=>"09", "descripcion"=>"DISCO DURO PARA SISTEMA DE RESPALDO"),
                            array("codigo"=>"10", "descripcion"=>"DISCO DURO SOLIDO EXTERNO 2.5''"),
                            array("codigo"=>"11", "descripcion"=>"DISCO DURO SOLIDO INTERNO 2.5''"),
                            array("codigo"=>"12", "descripcion"=>"DISCO DURO SOLIDO INTERNO M.2 NVMe 2230"),
                            array("codigo"=>"13", "descripcion"=>"DISCO DURO SOLIDO INTERNO M.2 SATA"),
                            array("codigo"=>"14", "descripcion"=>"MEMORIA USB"),
                            array("codigo"=>"15", "descripcion"=>"TARJETA DE MEMORIA SD"),
                            array("codigo"=>"16", "descripcion"=>"DISCO DURO SOLIDO INTERNO M.2 NVMe 2242"),
                            array("codigo"=>"17", "descripcion"=>"DISCO DURO SOLIDO INTERNO M.2 NVMe 2280"),

                        )
                    ),
                    array(
                        "codigo"=>"04",
                        "descripcion"=>"DISPOSITIVOS PARA CAPTURA DE IMÁGENES",
                        "sub_categoria"=>array(
                            array("codigo"=>"01", "descripcion"=>"ACCESORIOS DE LECTOR DE CODIGOS"),
                            array("codigo"=>"02", "descripcion"=>"CAMARA DE VIDEO"),
                            array("codigo"=>"03", "descripcion"=>"CAMARA FOTOGRAFICA"),
                            array("codigo"=>"04", "descripcion"=>"LECTOR DE CODIGOS"),
                            array("codigo"=>"05", "descripcion"=>"SCANNERS"),

                        )
                    ),
                    array(
                        "codigo"=>"05",
                        "descripcion"=>"DISPOSITIVOS TELEFÓNICOS",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"CELULARES"),
                            array("codigo"=>"02","descripcion"=>"TELEFONO IP"),

                        )
                    ),
                    array(
                        "codigo"=>"07",
                        "descripcion"=>"EQUIPOS DE INFRAESTRUCTURA PARA CENTRO DE DATOS",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"ACCESORIO PARA GABINETE"),
                            array("codigo"=>"02","descripcion"=>"ACCESORIOS PARA INFRAESTRUCTURA"),
                            array("codigo"=>"03","descripcion"=>"EQUIPO DE ADMINISTRACION TELEFONO IP"),
                            array("codigo"=>"04","descripcion"=>"EQUIPOS FIREWALL"),
                            array("codigo"=>"05","descripcion"=>"GABINETES"),
                            array("codigo"=>"06","descripcion"=>"SERVIDORES"),
                            array("codigo"=>"07","descripcion"=>"SISTEMA DE ALMACENAMIENTO"),
                            array("codigo"=>"08","descripcion"=>"SISTEMA DE RESPALDO"),
                            array("codigo"=>"09","descripcion"=>"SWITCH PARA ALMACENAMIENTO"),

                        )
                    ),
                    array(
                        "codigo"=>"08",
                        "descripcion"=>"IMPRESION",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"ACC. IMPRESORAS"),
                            array("codigo"=>"02","descripcion"=>"FOTOCOPIADORAS"),
                            array("codigo"=>"03","descripcion"=>"IMPRESORA 3D"),
                            array("codigo"=>"04","descripcion"=>"IMPRESORA DE FOTOCHECK"),
                            array("codigo"=>"05","descripcion"=>"IMPRESORA DE INJECCION DE TINTA"),
                            array("codigo"=>"06","descripcion"=>"IMPRESORA ETIQUETADORA"),
                            array("codigo"=>"07","descripcion"=>"IMPRESORA LASER"),
                            array("codigo"=>"08","descripcion"=>"IMPRESORA MATRICIAL"),
                            array("codigo"=>"09","descripcion"=>"IMPRESORA TERMICA"),
                            array("codigo"=>"10","descripcion"=>"PLOTTER"),
                            array("codigo"=>"11","descripcion"=>"IMPRESORA MULTIFUNCIONAL LASER"),
                            array("codigo"=>"12","descripcion"=>"IMPRESORA MULTIFUNCIONAL TINTA"),

                        )
                    ),
                    array(
                        "codigo"=>"09",
                        "descripcion"=>"MONITOR, PANTALLAS, TV, PROYECTORES",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"ECRAN"),
                            array("codigo"=>"02","descripcion"=>"MONITORES"),
                            array("codigo"=>"03","descripcion"=>"PANTALLA INTERACTIVA"),
                            array("codigo"=>"04","descripcion"=>"PIZARRA ACRILICA"),
                            array("codigo"=>"05","descripcion"=>"PROYECTORES MULTIMEDIA"),
                            array("codigo"=>"06","descripcion"=>"TELEVISOR"),

                        )
                    ),
                    array(
                        "codigo"=>"10",
                        "descripcion"=>"PRODUCTOS INFORMATICOS INTANGIBLES",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"LICENCIA DE SISTEMA OPERATIVO INFRAESTRUCTURA"),
                            array("codigo"=>"02","descripcion"=>"LICENCIA DE SISTEMA OPERATIVO USUARIO"),
                            array("codigo"=>"03","descripcion"=>"LICENCIAS DE ANTIVIRUS CONSUMO"),
                            array("codigo"=>"04","descripcion"=>"LICENCIAS DE ANTIVIRUS CORPORATIVO"),
                            array("codigo"=>"05","descripcion"=>"LICENCIAS DE OFFICE ESD"),
                            array("codigo"=>"06","descripcion"=>"LICENCIAS DE OFFICE OEM"),
                            array("codigo"=>"07","descripcion"=>"LICENCIAS DE OFFICE OLP"),
                            array("codigo"=>"08","descripcion"=>"LICIENCIA DE OFFICE POR SUSCRIPCIÓN"),
                            array("codigo"=>"09","descripcion"=>"SOFTWARE DE VIDEOGILANCIA"),
                            array("codigo"=>"10","descripcion"=>"GARANTIAS EXTENDIDAS"),

                        )
                    ),
                    array(
                        "codigo"=>"11",
                        "descripcion"=>"REDES Y CONECTIVIDAD",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"ACCESS POINT"),
                            array("codigo"=>"02","descripcion"=>"CABLE FIBRA"),
                            array("codigo"=>"03","descripcion"=>"CABLES UTP"),
                            array("codigo"=>"04","descripcion"=>"CONECTOR RJ45"),
                            array("codigo"=>"05","descripcion"=>"JACK"),
                            array("codigo"=>"06","descripcion"=>"ORDENADOR DE CABLES"),
                            array("codigo"=>"07","descripcion"=>"PATCH CORD"),
                            array("codigo"=>"08","descripcion"=>"PATCH PANEL"),
                            array("codigo"=>"09","descripcion"=>"ROUTER"),
                            array("codigo"=>"10","descripcion"=>"SWITCH LAN"),
                            array("codigo"=>"11","descripcion"=>"TARJETA DE RED"),

                        )
                    ),
                    array(
                        "codigo"=>"12",
                        "descripcion"=>"SUMINISTROS DE IMPRESIÓN",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"CINTA DE COLOR PARA FOTOCHECK"),
                            array("codigo"=>"02","descripcion"=>"FUSOR"),
                            array("codigo"=>"03","descripcion"=>"PAPEL TERMICO"),
                            array("codigo"=>"04","descripcion"=>"SUMINISTRO DE CINTA MATRICIAL"),
                            array("codigo"=>"05","descripcion"=>"SUMINISTRO DE TINTA                                                                                 "),
                            array("codigo"=>"06","descripcion"=>"SUMINISTRO DE TONER                                                                                 "),
                            array("codigo"=>"07","descripcion"=>"TAMBOR"),
                            array("codigo"=>"08","descripcion"=>"TARJETAS PVC"),

                        )
                    ),
                    array(
                        "codigo"=>"13",
                        "descripcion"=>"PROCESADOR (CPU)",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"PROCESADOR AMD RYZEN 3 S_AM4"),
                            array("codigo"=>"02","descripcion"=>"PROCESADOR AMD RYZEN 5 S_AM4"),
                            array("codigo"=>"03","descripcion"=>"PROCESADOR AMD RYZEN 5 S_AM5"),
                            array("codigo"=>"04","descripcion"=>"PROCESADOR AMD RYZEN 7 S_AM4"),
                            array("codigo"=>"05","descripcion"=>"PROCESADOR AMD RYZEN 7 S_AM5"),
                            array("codigo"=>"06","descripcion"=>"PROCESADOR AMD RYZEN 9 S_AM4"),
                            array("codigo"=>"07","descripcion"=>"PROCESADOR AMD RYZEN 9 S_AM5"),
                            array("codigo"=>"08","descripcion"=>"PROCESADOR CELERON DC GXXXX S1700"),
                            array("codigo"=>"09","descripcion"=>"PROCESADOR CORE I3 9XXX S1151-V2"),
                            array("codigo"=>"10","descripcion"=>"PROCESADOR CORE I3 10XXX S1200"),
                            array("codigo"=>"11","descripcion"=>"PROCESADOR CORE I3 11XXX S1200"),
                            array("codigo"=>"12","descripcion"=>"PROCESADOR CORE I3 12XXX S1700"),
                            array("codigo"=>"13","descripcion"=>"PROCESADOR CORE I5 7XXX S1151"),
                            array("codigo"=>"14","descripcion"=>"PROCESADOR CORE I5 8XXX S1151-V2"),
                            array("codigo"=>"15","descripcion"=>"PROCESADOR CORE I5 9XXX S1151-V2"),
                            array("codigo"=>"16","descripcion"=>"PROCESADOR CORE I5 10XXX S1200"),
                            array("codigo"=>"17","descripcion"=>"PROCESADOR CORE I5 11XXX S1200"),
                            array("codigo"=>"18","descripcion"=>"PROCESADOR CORE I5 12XXX S1700"),
                            array("codigo"=>"19","descripcion"=>"PROCESADOR CORE I5 13XXX S1700"),
                            array("codigo"=>"20","descripcion"=>"PROCESADOR CORE I7 7XXX S1151"),
                            array("codigo"=>"21","descripcion"=>"PROCESADOR CORE I7 7XXX S2066"),
                            array("codigo"=>"22","descripcion"=>"PROCESADOR CORE I7 10XXX S1200"),
                            array("codigo"=>"23","descripcion"=>"PROCESADOR CORE I7 11XXX S1200"),
                            array("codigo"=>"24","descripcion"=>"PROCESADOR CORE I7 12XXX S1700"),
                            array("codigo"=>"25","descripcion"=>"PROCESADOR CORE I7 13XXX S1700"),
                            array("codigo"=>"26","descripcion"=>"PROCESADOR CORE I9 10XXX S1200"),
                            array("codigo"=>"27","descripcion"=>"PROCESADOR CORE I9 11XXX S1200"),
                            array("codigo"=>"28","descripcion"=>"PROCESADOR CORE I9 12XXX S1700"),
                            array("codigo"=>"29","descripcion"=>"PROCESADOR CORE I9 13XXX S1700"),

                        )
                    ),
                    array(
                        "codigo"=>"14",
                        "descripcion"=>"MEMORIA RAM NOTEBOOK",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"RAM SODIMM DDR3 1600Mhz PC3-12800"),
                            array("codigo"=>"02","descripcion"=>"RAM SODIMM DDR4 2666Mhz PC4-21300"),
                            array("codigo"=>"03","descripcion"=>"RAM SODIMM DDR4 2800Mhz PC4-22400"),
                            array("codigo"=>"04","descripcion"=>"RAM SODIMM DDR4 3000Mhz PC4-24000"),
                            array("codigo"=>"05","descripcion"=>"RAM SODIMM DDR4 3200Mhz PC4-25600"),
                            array("codigo"=>"06","descripcion"=>"RAM SODIMM DDR5 4800Mhz PC5-38400"),
                            array("codigo"=>"07","descripcion"=>"RAM SODIMM DDR5 5200Mhz PC5-41600"),
                            array("codigo"=>"08","descripcion"=>"RAM SODIMM DDR5 5600Mhz PC5-44800"),
                            array("codigo"=>"09","descripcion"=>"RAM SODIMM DDR5 6000Mhz PC5-48000"),

                        )
                    ),
                    array(
                        "codigo"=>"15",
                        "descripcion"=>"MEMORIA RAM PC",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"RAM DDR3 1600Mhz PC3-12800"),
                            array("codigo"=>"02","descripcion"=>"RAM DDR3 1800Mhz PC3-14400"),
                            array("codigo"=>"03","descripcion"=>"RAM DDR3 2600Mhz PC3-20800"),
                            array("codigo"=>"04","descripcion"=>"RAM DDR4 2666Mhz PC4-21300"),
                            array("codigo"=>"05","descripcion"=>"RAM DDR4 3000Mhz PC4-24000"),
                            array("codigo"=>"06","descripcion"=>"RAM DDR4 3200Mhz PC4-25600"),
                            array("codigo"=>"07","descripcion"=>"RAM DDR4 3600Mhz PC4-28800"),
                            array("codigo"=>"08","descripcion"=>"RAM DDR4 4000Mhz PC4-32000"),
                            array("codigo"=>"09","descripcion"=>"RAM DDR5 4800Mhz PC5-38400"),
                            array("codigo"=>"10","descripcion"=>"RAM DDR5 5200Mhz PC5-41600"),
                            array("codigo"=>"11","descripcion"=>"RAM DDR5 5600Mhz PC5-44800"),
                            array("codigo"=>"12","descripcion"=>"RAM DDR5 6000Mhz PC5-48000"),

                        )
                    ),
                    array(
                        "codigo"=>"16",
                        "descripcion"=>"COMPUTADORA ALL IN ONE",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"COMPUTADORA ALL-IN-ONE AMD RYZEN 3"),
                            array("codigo"=>"02","descripcion"=>"COMPUTADORA ALL-IN-ONE AMD RYZEN 5"),
                            array("codigo"=>"03","descripcion"=>"COMPUTADORA ALL-IN-ONE AMD RYZEN 7"),
                            array("codigo"=>"04","descripcion"=>"COMPUTADORA ALL-IN-ONE CELERON"),
                            array("codigo"=>"05","descripcion"=>"COMPUTADORA ALL-IN-ONE CORE I3"),
                            array("codigo"=>"06","descripcion"=>"COMPUTADORA ALL-IN-ONE CORE I5"),
                            array("codigo"=>"07","descripcion"=>"COMPUTADORA ALL-IN-ONE CORE I7"),

                        )
                    ),
                    array(
                        "codigo"=>"17",
                        "descripcion"=>"COMPUTADORA DE ESCRITORIO",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"COMPUTADORA AMD RYZEN 5"),
                            array("codigo"=>"02","descripcion"=>"COMPUTADORA AMD RYZEN 7"),
                            array("codigo"=>"03","descripcion"=>"COMPUTADORA AMD RYZEN 9"),
                            array("codigo"=>"04","descripcion"=>"COMPUTADORA CELERON"),
                            array("codigo"=>"05","descripcion"=>"COMPUTADORA CORE I3"),
                            array("codigo"=>"06","descripcion"=>"COMPUTADORA CORE I5"),
                            array("codigo"=>"07","descripcion"=>"COMPUTADORA CORE I7"),

                        )
                    ),
                    array(
                        "codigo"=>"18",
                        "descripcion"=>"COMPUTADORA PORTÁTIL",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"NOTEBOOK CELERON"),
                            array("codigo"=>"02","descripcion"=>"NOTEBOOK CELERON DUAL CORE"),
                            array("codigo"=>"03","descripcion"=>"NOTEBOOK ATOM"),
                            array("codigo"=>"04","descripcion"=>"NOTEBOOK CORE I3"),
                            array("codigo"=>"05","descripcion"=>"NOTEBOOK CORE I5"),
                            array("codigo"=>"06","descripcion"=>"NOTEBOOK CORE I7"),
                            array("codigo"=>"07","descripcion"=>"NOTEBOOK AMD ATHLON"),
                            array("codigo"=>"08","descripcion"=>"NOTEBOOK AMD A SERIES"),
                            array("codigo"=>"09","descripcion"=>"NOTEBOOK PENTIUM QUAD CORE"),
                            array("codigo"=>"10","descripcion"=>"NOTEBOOK AMD RYZEN 5"),
                            array("codigo"=>"11","descripcion"=>"NOTEBOOK AMD RYZEN 3"),
                            array("codigo"=>"12","descripcion"=>"NOTEBOOK AMD RYZEN 7"),
                            array("codigo"=>"13","descripcion"=>"NOTEBOOK GAMING"),

                        )
                    ),
                    array(
                        "codigo"=>"19",
                        "descripcion"=>"ESTACIÓN DE TRABAJO",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"ESTACIÓN DE TRABAJO")
                        )
                    ),
                    array(
                        "codigo"=>"20",
                        "descripcion"=>"REPUESTOS DE EQUIPOS DE COMPUTO",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"REPUESTOS DE EQUIPOS DE COMPUTO")
                        )
                    ),
                    array(
                        "codigo"=>"21",
                        "descripcion"=>"TABLETA",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"TABLETA")
                        )
                    ),
                )
            ),
            array(
                "codigo"=>"04",
                "descripcion"=>"HERRAMIENTAS Y MATERIALES PARA PRODUCCION",
                "categorias"=>array(
                    array(
                        "codigo"=>"01",
                        "descripcion"=>"ACCESORIOS PARA HERRAMIENTAS",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"BROCAS"),
                            array("codigo"=>"02","descripcion"=>"CAJA DE HERRAMIENTAS PORTATIL DE PLÁSTICO "),
                            array("codigo"=>"03","descripcion"=>"CARGADORES PARA DESTORNILLADORES Y ATORNILLADORES INALAMBRICOS"),
                            array("codigo"=>"04","descripcion"=>"DISCOS ABRASIVOS"),
                            array("codigo"=>"05","descripcion"=>"DISCOS DIAMANTADOS"),
                            array("codigo"=>"06","descripcion"=>"DISCOS SIERRA"),
                            array("codigo"=>"07","descripcion"=>"DISCOS TRASLAPADOS"),
                            array("codigo"=>"08","descripcion"=>"PUNTAS PARA ATORNILLADOR"),
                            array("codigo"=>"09","descripcion"=>"DADOS PARA ATORNILLADOR"),

                        )
                    ),
                    array(
                        "codigo"=>"02",
                        "descripcion"=>"HERRAMIENTAS AUTOMOTRICES",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"CABLES PASA-CORRIENTE DE BATERIA"),
                            array("codigo"=>"02","descripcion"=>"PROBADOR DE CIRCUITOS"),
                        )
                    ),
                    array(
                        "codigo"=>"03",
                        "descripcion"=>"HERRAMIENTAS DE CERTIFICACIÓN",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"CERTIFICADOR DE DATOS"),
                            array("codigo"=>"02","descripcion"=>"MEGÓMETRO DIGITAL"),
                            array("codigo"=>"03","descripcion"=>"MULTIMETRO DIGITAL"),
                            array("codigo"=>"04","descripcion"=>"PINZA AMPERIMÉTRICA"),
                            array("codigo"=>"05","descripcion"=>"TELURÓMETRO DIGITAL"),
                            array("codigo"=>"06","descripcion"=>"TESTEADOR DE CABLES  DE RED"),

                        )
                    ),
                    array(
                        "codigo"=>"04",
                        "descripcion"=>"HERRAMIENTAS DE CONSTRUCCIÓN",
                        "sub_categoria"=>array(
                            array("codigo"=>"07","descripcion"=>"PISTOLAS DE CALOR"),
                            array("codigo"=>"08","descripcion"=>"SOPLADOR / ASPIRADOR"),
                        )
                    ),
                    array(
                        "codigo"=>"05",
                        "descripcion"=>"HERRAMIENTAS ELÉCTRICAS",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"MEDIDOR DE DISTANCIA"),

                        )
                    ),
                    array(
                        "codigo"=>"05",
                        "descripcion"=>"HERRAMIENTAS MECÁNICAS",
                        "sub_categoria"=>array(
                            array("codigo"=>"02","descripcion"=>"JUEGO DE HERRAMIENTAS MECÁNICAS"),
                        )
                    ),
                    array(
                        "codigo"=>"06",
                        "descripcion"=>"HERRAMIENTAS METALMECÁNICAS",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"ESMERILES"),
                            array("codigo"=>"02","descripcion"=>"PULIDORA"),
                            array("codigo"=>"03","descripcion"=>"TRONZADORA"),

                        )
                    ),
                    array(
                        "codigo"=>"07",
                        "descripcion"=>"HERRAMIENTAS NEUMÁTICAS",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"ADAPTADOR"),
                            array("codigo"=>"02","descripcion"=>"EXTENSIÓN"),
                            array("codigo"=>"03","descripcion"=>"JUNTA UNIVERSAL"),
                            array("codigo"=>"04","descripcion"=>"LLAVE o PISTOLA DE IMPACTO"),
                            array("codigo"=>"05","descripcion"=>"LLAVES COMBINADAS"),
                            array("codigo"=>"06","descripcion"=>"MANGO ARTICULADO PARA DADOS"),
                            array("codigo"=>"07","descripcion"=>"RATCHET NEUMÁTICO"),
                            array("codigo"=>"08","descripcion"=>"TALADRO NEUMÁTICO REVERSIBLE"),

                        )
                    ),
                    array(
                        "codigo"=>"08",
                        "descripcion"=>"HERRAMIENTAS PARA CONCRETO",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"MARTILLO CINCELADOR"),
                            array("codigo"=>"02","descripcion"=>"ROTO MARTILLO"),

                        )
                    ),
                    array(
                        "codigo"=>"09",
                        "descripcion"=>"HERRAMIENTAS PARA MADERA",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"CEPILLOS CARPINTERO"),
                            array("codigo"=>"02","descripcion"=>"LIJADORAS ELECTRICA"),
                            array("codigo"=>"03","descripcion"=>"REBAJADORAS / FRESADORAS"),
                            array("codigo"=>"04","descripcion"=>"SIERRAS"),

                        )
                    ),
                    array(
                        "codigo"=>"10",
                        "descripcion"=>"HERRAMIENTAS PARA REDES",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"CORTADOR DE CABLE"),
                            array("codigo"=>"02","descripcion"=>"CRIMPEADOR RJ45"),
                            array("codigo"=>"03","descripcion"=>"PONCHADOR PARA JACK"),
                            array("codigo"=>"04","descripcion"=>"FUSIONADORA DE FIBRA OPTICA"),

                        )
                    ),
                    array(
                        "codigo"=>"11",
                        "descripcion"=>"MATERIALES PARA PRODUCCÍÓN",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"CEMENTO"),
                            array("codigo"=>"02","descripcion"=>"CUTTER"),
                            array("codigo"=>"03","descripcion"=>"LIJAS PARA CEMENTO"),
                            array("codigo"=>"04","descripcion"=>"LIJAS PARA METAL"),
                            array("codigo"=>"05","descripcion"=>"LIJAS PARA MADERA"),
                            array("codigo"=>"06","descripcion"=>"PINTURAS"),

                        )
                    ),
                    array(
                        "codigo"=>"12",
                        "descripcion"=>"TALADROS Y ATORNILLADORES",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"ATORNILLADOR ALÁMBRICO"),
                            array("codigo"=>"02","descripcion"=>"TALADRO ALÁMBRICO DE ROTACIÓN"),
                            array("codigo"=>"03","descripcion"=>"TALADRO ALÁMBRICO PERCUTOR"),
                            array("codigo"=>"04","descripcion"=>"ATORNILLADORES INALÁMBRICO"),
                            array("codigo"=>"05","descripcion"=>"TALADRO INALÁMBRICO DE ROTACIÓN"),
                            array("codigo"=>"06","descripcion"=>"TALADRO INALÁMBRICO PERCUTOR"),

                        )
                    ),

                )
            ),
            array(
                "codigo"=>"05",
                "descripcion"=>"INSUMOS Y UTILES PARA LIMPIEZA",
                "categorias"=>array(
                    array(
                        "codigo"=>"01",
                        "descripcion"=>"INSUMOS Y UTILES PARA LIMPIEZA",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"INSUMOS Y UTILES PARA LIMPIEZA"),
                        )
                    )

                )
            ),
            array(
                "codigo"=>"06",
                "descripcion"=>"MOBILIARIO, APARATOS Y UTILES PARA OFICINA",
                "categorias"=>array(
                    array(
                        "codigo"=>"01",
                        "descripcion"=>"MUEBLES PARA OFICINAS",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"ESCRITORIOS"),
                            array("codigo"=>"02","descripcion"=>"SILLAS"),
                            array("codigo"=>"03","descripcion"=>"ORGANIZADORES"),

                        )
                    ),
                    array(
                        "codigo"=>"02",
                        "descripcion"=>"UTILES PARA OFICINA",
                        "sub_categoria"=>array(
                            array("codigo"=>"04","descripcion"=>"UTILES PARA OFICINA")
                        )
                    ),
                )
            ),
            array(
                "codigo"=>"07",
                "descripcion"=>"SEGURIDAD PERSONAL E INDUSTRIAL",
                "categorias"=>array(
                    array(
                        "codigo"=>"01",
                        "descripcion"=>"ARTÍCULOS DE BIOSEGURIDAD",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"GAFAS DE PROTECCIÓN"),
                            array("codigo"=>"02","descripcion"=>"MASCARILLAS"),
                            array("codigo"=>"03","descripcion"=>"PROTECTORES FACIALES"),
                            array("codigo"=>"04","descripcion"=>"RESPIRADORES"),

                        )
                    ),
                    array(
                        "codigo"=>"02",
                        "descripcion"=>"DISPOSITIVOS CONTRA INCENDIO",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"ALARMA CONTRAINCENDIO"),
                            array("codigo"=>"02","descripcion"=>"DETECTOR DE INCENDIOS"),
                        )
                    ),
                    array(
                        "codigo"=>"03",
                        "descripcion"=>"DISPOSITIVOS MÉDICOS",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"OXÍMETROS"),
                            array("codigo"=>"02","descripcion"=>"TERMÓMETROS A DISTANCIA"),

                        )
                    ),
                    array(
                        "codigo"=>"04",
                        "descripcion"=>"DISPOSITIVOS PARA CONTROL DE ACCESO",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"ACCESORIOS DE SISTEMA DE SEGURIDAD"),
                            array("codigo"=>"02","descripcion"=>"CONTROL BIOMETRICO"),
                            array("codigo"=>"03","descripcion"=>"LECTORES DE TARJETA DE ACCESO"),
                            array("codigo"=>"04","descripcion"=>"SENSORES DE MOVIMIENTO"),
                            array("codigo"=>"05","descripcion"=>"SISTEMA DE PUERTA"),

                        )
                    ),
                    array(
                        "codigo"=>"05",
                        "descripcion"=>"EPP EQUIPOS DE PROTECCIÓN PERSONAL",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"PROTECCIÓN ANTICAIDA"),
                            array("codigo"=>"02","descripcion"=>"ROPA DE TRABAJO"),
                            array("codigo"=>"03","descripcion"=>"PROTECCIÓN RESPIRATORIA"),
                            array("codigo"=>"04","descripcion"=>"PROTECCIÓN DE CABEZA"),
                            array("codigo"=>"05","descripcion"=>"PROTECCIÓN AUDITIVA"),
                            array("codigo"=>"06","descripcion"=>"PROTECCIÓN VISUAL Y FACIAL"),
                            array("codigo"=>"07","descripcion"=>"PROTECCIÓN DE MANOS"),
                            array("codigo"=>"08","descripcion"=>"PROTECCIÓN CONTRA CAIDAS"),
                            array("codigo"=>"09","descripcion"=>"PROTECCIÓN FACIAL"),
                            array("codigo"=>"10","descripcion"=>"PROTECCIÓN CORPORAL"),
                            array("codigo"=>"11","descripcion"=>"PROTECCIÓN DE PIES"),
                            array("codigo"=>"12","descripcion"=>"SEGURIDAD VIAL"),
                            array("codigo"=>"13","descripcion"=>"BLOQUEO DE SEGURIDAD LOCK/TAG OUT"),
                            array("codigo"=>"14","descripcion"=>"OTROS EPPS"),

                        )
                    ),
                    array(
                        "codigo"=>"06",
                        "descripcion"=>"ELEMENTOS DE SEGURIDAD INDUSTRIAL",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"CINTAS DE SEGURIDAD"),
                            array("codigo"=>"02","descripcion"=>"CONOS DE SEGURIDAD"),
                            array("codigo"=>"03","descripcion"=>"EXTINTOR"),
                            array("codigo"=>"04","descripcion"=>"LETREROS DE SEGURIDAD"),

                        )
                    ),
                    array(
                        "codigo"=>"07",
                        "descripcion"=>"VIGILANCIA DE VIDEO",
                        "sub_categoria"=>array(
                            array("codgio"=>"01","descripcion"=>"ACCESORIOS DE VIDEO VIGILANCIA"),
                            array("codgio"=>"02","descripcion"=>"CAMARAS ANALOGICAS"),
                            array("codgio"=>"03","descripcion"=>"CAMARAS IP"),
                            array("codgio"=>"04","descripcion"=>"DVRs SISTEMAS DE ADMINISTRACION DE CAMARAS ANALOGICAS"),
                            array("codgio"=>"05","descripcion"=>"NVRS SISTEMAS DE ADMINISTRACION DE CAMARAS IP"),
                        ),
                    ),
                )
            ),
            array(
                "codigo"=>"08",
                "descripcion"=>"VALES Y MERCHANDSING",
                "categorias"=>array(
                    array(
                        "codigo"=>"01",
                        "descripcion"=>"ACCESORIOS Y ARTÍCULOS PUBLICITARIOS",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"BANNERS PUBLICITARIOS"),
                            array("codigo"=>"02","descripcion"=>"GORROS, POLOS Y OTRAS PRENDAS CON PUBLICIDAD"),
                            array("codigo"=>"03","descripcion"=>"LAPICEROS"),
                            array("codigo"=>"04","descripcion"=>"LLAVEROS"),
                            array("codigo"=>"05","descripcion"=>"REVISTAS PUBLICITARIAS"),

                        )
                    ),
                    array(
                        "codigo"=>"02",
                        "descripcion"=>"GIFTCARDS Y VALES",
                        "sub_categoria"=>array(
                            array("codigo"=>"01","descripcion"=>"GIFTCARDS"),
                            array("codigo"=>"02","descripcion"=>"VALES"),
                        )
                    ),

                )
            )
        );
        // return $clasificacion;exit;
        // $clasificacion = (array) $clasificacion;
        $array_almacen_clasificacion = array();
        $array_almacen_categoria = array();
        foreach ($clasificacion as $key => $value) {
            $clasificacion = Clasificacion::where('descripcion',$value['descripcion'])->where('estado',1)->first();


            if (!$clasificacion) {
                $clasificacion = new Clasificacion();
                $clasificacion->descripcion = $value['descripcion'];
                $clasificacion->fecha_registro = date('Y-M-d H:i:s');
                $clasificacion->estado = 1;
                // $clasificacion->cod_softlink =;
                $clasificacion->save();

                $clasificacion = Clasificacion::find($clasificacion->id_clasificacion);
                $clasificacion->cod_softlink = $clasificacion->id_clasificacion;
                $clasificacion->save();

            }
            $value['id_clasificacion'] = $clasificacion->id_clasificacion;

            foreach ($value['categorias'] as $key_categoria => $value_categoria) {
                // $categoria = Categoria::where('descripcion',$value_categoria['descripcion'])->where('estado',1)->first();
                // if (!$categoria) {
                    $categoria = new Categoria();
                    $categoria->descripcion = $value_categoria['descripcion'];
                    $categoria->estado = 1;
                    $categoria->fecha_registro = date('Y-M-d H:i:s');
                    $categoria->id_clasificacion = $clasificacion->id_clasificacion;
                    $categoria->save();
                    // return response()->json($array_almacen_categoria);
                // }

                foreach ($value_categoria['sub_categoria'] as $key => $value_sub_categoria) {
                    // $sub_categoria = SubCategoria::where('descripcion',$value_sub_categoria['descripcion'])->where('estado',1)->first();
                    // if (!$sub_categoria) {
                        $sub_categoria = new SubCategoria();
                        $sub_categoria->id_tipo_producto = $categoria->id_tipo_producto;
                        $sub_categoria->cod_softlink = $clasificacion->id_clasificacion;
                        $sub_categoria->descripcion = $value_sub_categoria['descripcion'];
                        $sub_categoria->estado = 1;
                        $sub_categoria->fecha_registro = date('Y-M-d H:i:s');
                        $sub_categoria->save();
                    // }
                }

            }
            // return $clasificacion;
        }
        // return response()->json($array_almacen_categoria);
        return response()->json([
            "success"=>true,
            "status"=>200,
            "data"=>$clasificacion
        ]);
    }
    public function scripActualizarCategoriasSoftlink()
    {

        $array = array(
            "*OTROS",
            "ALIMENTOS Y BEBIDAS DE CONSUMO HUMANO",
            "EQUIPOS ELECTRICOS",
            "EQUIPOS, APARATOS Y ACCESORIOS INFORMÁTICOS",
            "HERRAMIENTAS Y MATERIALES PARA PRODUCCION",
            "INSUMOS Y UTILES PARA LIMPIEZA",
            "MOBILIARIO, APARATOS Y UTILES PARA OFICINA",
            "SEGURIDAD PERSONAL E INDUSTRIAL",
            "VALES Y MERCHANDSING",
        );
        $array_clasificacion = array();
        $array_sub_categoria = array();

        foreach ($array as $key => $value) {
            $clasificacion  = Clasificacion::where('estado',1)->where('descripcion',$value)->first();
            array_push($array_clasificacion,$clasificacion);
        }

        foreach ($array_clasificacion as $key => $value) {
            $categoria = Categoria::where('id_clasificacion',$value->id_clasificacion)->get();
            $value->categoria = $categoria;
            foreach ($value->categoria as $key_categoria => $value_categoria) {
                $sub_categoria = SubCategoria::where('id_tipo_producto',$value_categoria->id_tipo_producto)->get();
                $value_categoria->sub_categoria = $sub_categoria;
            }
        }

        foreach ($array_clasificacion as $key => $value) {
            // return trim($value->descripcion);exit;
            $soplinea = DB::connection('soft')->table('soplinea')->where('nom_line',trim($value->descripcion))->first();

            if (!$soplinea) {

                DB::connection('soft')->table('soplinea')->insert(
                    [
                        'cod_line' => $value->id_clasificacion,
                        'nom_line' => trim($value->descripcion),
                        'cod_sunat' => '',
                        'cod_osce' => ''
                    ]
                );
            }


            foreach ($value->categoria as $key_categoria => $value_categoria) {
                foreach ($value_categoria->sub_categoria as $key_sub_categoria => $value_sub_categoria) {
                    $cod_sub1 = '';
                    $cod_cate = DB::connection('soft')->table('sopsub1')->orderBy('cod_sub1','DESC')->first();
                    do {

                        $cod_sub1 = (intval($cod_cate->cod_sub1) + 1);
                        $cod_cate = DB::connection('soft')->table('sopsub1')->where('cod_sub1',$cod_sub1)->first();

                    } while ($cod_cate);

                    $categoria_soflink = DB::connection('soft')->table('sopsub1')->where('nom_sub1',trim($value_sub_categoria->descripcion))->where('cod_line',$value->id_clasificacion)->first();

                    if (!$categoria_soflink) {
                        DB::connection('soft')->table('sopsub1')->insert(
                            [
                                'cod_sub1' => $cod_sub1,
                                'nom_sub1' => trim($value_sub_categoria->descripcion),
                                'por_dcto' => 0,
                                'num_corr' => 0,
                                'cod_line' => $value->id_clasificacion
                            ]
                        );
                    }
                }
            }
        }
        return response()->json([
            "success"=>true,
            "status"=>200,
            "message"=>"actualizacion de categorias en el softlink",
        ]);
    }
}
