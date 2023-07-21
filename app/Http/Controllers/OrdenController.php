<?php

namespace App\Http\Controllers;

use App\Exports\ListOrdenesDetailExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

date_default_timezone_set('America/Lima');

use Debugbar;
use PDO;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ListOrdenesHeadExport;
use App\Exports\ReporteComprasLocalesExcel;
use App\Exports\ReporteOrdenesCompraExcel;
use App\Exports\ReporteOrdenesServicioExcel;
use App\Exports\ReporteTransitoOrdenesCompraExcel;
use App\Helpers\CuadroPresupuestoHelper;
use App\Helpers\Finanzas\PresupuestoInternoHistorialHelper;
use App\Helpers\Necesidad\RequerimientoHelper;
use App\Helpers\NotificacionHelper;
use App\Http\Controllers\Finanzas\Presupuesto\PresupuestoInternoController;
use App\Http\Controllers\Migraciones\MigrateOrdenSoftLinkController;
use App\Http\Controllers\Tesoreria\CierreAperturaController;
use App\Mail\EmailFinalizacionCuadroPresupuesto;
use App\Mail\EmailOrdenAnulada;
use App\Mail\EmailOrdenServicioOrdenTransformacion;
use App\Models\Administracion\Empresa;
use App\Models\Administracion\Estado;
use App\Models\Administracion\Periodo;
use App\Models\Administracion\Prioridad;
use App\Models\Administracion\Sede;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Requerimiento;
use App\Models\almacen\Transformacion;
use App\Models\Almacen\UnidadMedida;
use App\Models\Comercial\CuadroCosto\CcAmFila;
use App\models\Configuracion\AccesosUsuarios;
use App\Models\Configuracion\Grupo;
use App\Models\Configuracion\LogActividad;
use App\Models\Configuracion\Moneda;
use App\Models\Configuracion\Notificacion;
use App\Models\Configuracion\Usuario;
use App\Models\Contabilidad\Banco;
use App\Models\Contabilidad\Contribuyente;
use App\Models\Contabilidad\CuentaContribuyente;
use App\Models\Contabilidad\Identidad;
use App\Models\Contabilidad\TipoCuenta;
use App\Models\Finanzas\HistorialPresupuestoInternoSaldo;
use App\Models\Logistica\CondicionSoftlink;
use App\Models\Logistica\EstadoCompra;
use App\Models\Logistica\ItemsOrdenesView;
use App\Models\Logistica\Orden;
use App\Models\Logistica\OrdenCompraDetalle;
use App\Models\Logistica\OrdenesView;
use App\Models\Logistica\PagoCuota;
use App\Models\Logistica\PagoCuotaDetalle;
use App\Models\Logistica\Proveedor;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use App\Models\mgcp\CuadroCosto\CuadroCostoView;
use App\Models\Rrhh\Persona;
use App\Models\Tesoreria\RegistroPago;
use App\Models\Tesoreria\RequerimientoPagoTipoDestinatario;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Unique;
use Mockery\Undefined;

use function GuzzleHttp\json_encode;

class OrdenController extends Controller
{

    public function mostrar()
    {
        $data = Orden::select('log_ord_compra.*')
            ->where('log_ord_compra.estado', 1)
            ->get();
        return $data;
    }



    function view_generar_orden_requerimiento()
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
        $periodos = Periodo::mostrar();

        return view('logistica/ordenes/generar_orden_requerimiento', compact('sedes', 'empresas', 'sis_identidad', 'tp_documento', 'tp_moneda', 'tp_doc', 'condiciones', 'clasificaciones', 'subcategorias', 'categorias', 'unidades', 'unidades_medida', 'monedas','periodos'));
    }
    function view_crear_orden_requerimiento()
    {
        $condiciones = $this->select_condiciones();
        $condiciones_softlink = CondicionSoftlink::mostrar();

        $tp_moneda = $this->select_moneda();
        $tp_documento = $this->select_documento();
        $sis_identidad = $this->select_sis_identidad();
        // $sedes = $this->select_sedes();
        $sedes = $this->select_empresa_sede();
        // $empresas = $this->select_mostrar_empresas();
        $tp_doc = $this->select_tp_doc();
        $clasificaciones = (new AlmacenController)->mostrar_clasificaciones_cbo();
        $subcategorias = (new AlmacenController)->mostrar_subcategorias_cbo();
        $categorias = (new AlmacenController)->mostrar_categorias_cbo();
        $unidades = (new AlmacenController)->mostrar_unidades_cbo();

        $unidades_medida = UnidadMedida::mostrar();
        $monedas = Moneda::mostrar();
        // $sedes = Auth::user()->sedesAcceso();
        $bancos = Banco::mostrar();
        $tipo_cuenta = TipoCuenta::mostrar();
        $empresas = $this->select_mostrar_empresas();
        $rubros = $this->select_mostrar_rubos();

        $array_accesos_botonera = array();
        $accesos_botonera = AccesosUsuarios::where('accesos_usuarios.estado', '=', 1)
            ->select('accesos.*')
            ->join('configuracion.accesos', 'accesos.id_acceso', '=', 'accesos_usuarios.id_acceso')
            ->where('accesos_usuarios.id_usuario', Auth::user()->id_usuario)
            ->where('accesos_usuarios.id_modulo', 91)
            ->where('accesos_usuarios.id_padre', 89)
            ->get();
        foreach ($accesos_botonera as $key => $value) {
            $value->accesos;
            array_push($array_accesos_botonera, $value->accesos->accesos_grupo);
        }
        $modulo = 'logistica';
        $array_accesos = [];
        $accesos_usuario = AccesosUsuarios::where('estado', 1)->where('id_usuario', Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos, $value->id_acceso);
        }
        $periodos = Periodo::mostrar();

        return view('logistica/gestion_logistica/compras/ordenes/elaborar/crear_orden_requerimiento', compact('empresas', 'rubros', 'bancos', 'tipo_cuenta', 'sedes', 'sis_identidad', 'tp_documento', 'tp_moneda', 'tp_doc', 'condiciones', 'condiciones_softlink', 'clasificaciones', 'subcategorias', 'categorias', 'unidades', 'unidades_medida', 'monedas', 'modulo', 'array_accesos_botonera', 'array_accesos','periodos'));
    }

    function lista_contactos_proveedor($id_proveedor)
    {

        $data = DB::table('logistica.log_prove')
            ->select(
                'adm_ctb_contac.id_datos_contacto as id_contacto',
                'adm_ctb_contac.nombre as nombre_contacto',
                'adm_ctb_contac.cargo as cargo_contacto',
                'adm_ctb_contac.email as email_contacto',
                'adm_ctb_contac.telefono as telefono_contacto',
                'adm_ctb_contac.direccion as direccion_contacto',
                'adm_ctb_contac.ubigeo as ubigeo_contacto'
            )
            // ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->join('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->where('log_prove.id_proveedor', $id_proveedor)
            ->orderby('adm_ctb_contac.nombre', 'asc')
            ->get();


        return response()->json(['data' => $data]);
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

    public function select_empresa_sede()
    {
        $data = DB::table('administracion.sis_sede')
            ->select(
                'sis_sede.*',
                'ubi_dis.descripcion as ubigeo_descripcion'
            )
            ->leftJoin('configuracion.ubi_dis', 'ubi_dis.id_dis', '=', 'sis_sede.id_ubigeo')
            ->where('sis_sede.estado', '=', '1')
            ->orderBy('sis_sede.id_empresa', 'asc')
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

    public function select_condiciones()
    {
        $data = DB::table('logistica.log_cdn_pago')
            ->select('log_cdn_pago.id_condicion_pago', 'log_cdn_pago.descripcion')
            ->where('log_cdn_pago.estado', 1)
            ->orderBy('log_cdn_pago.descripcion')
            ->get();
        return $data;
    }

    public function select_mostrar_empresas()
    {
        $data = DB::table('administracion.adm_empresa')
            ->select('adm_empresa.id_empresa', 'adm_empresa.codigo', 'adm_empresa.logo_empresa', 'adm_contri.nro_documento', 'adm_contri.razon_social')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->where('adm_empresa.estado', '=', 1)
            ->orderBy('adm_contri.razon_social', 'asc')
            ->get();
        return $data;
    }
    public function select_mostrar_rubos()

    {
        $data = DB::table('contabilidad.adm_rubro')
            ->where('adm_rubro.estado', '=', 1)
            ->orderBy('adm_rubro.descripcion', 'asc')
            ->get();
        return $data;
    }

    public function select_mostrar_estados_compra()
    {
        $data = DB::table('logistica.estados_compra')
            ->select('estados_compra.*')
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

    function sedesAcceso($id_empresa)
    {
        $id_usuario = Auth::user()->id_usuario;
        $sedes = DB::table('configuracion.sis_usua_sede')
            ->select(
                'sis_sede.*',
                DB::raw("(ubi_dis.descripcion) || ' ' || (ubi_prov.descripcion) || ' ' || (ubi_dpto.descripcion)  AS ubigeo_descripcion")
            )
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'sis_usua_sede.id_sede')
            ->leftJoin('configuracion.ubi_dis', 'ubi_dis.id_dis', '=', 'sis_sede.id_ubigeo')
            ->leftJoin('configuracion.ubi_prov', 'ubi_dis.id_prov', '=', 'ubi_prov.id_prov')
            ->leftJoin('configuracion.ubi_dpto', 'ubi_prov.id_dpto', '=', 'ubi_dpto.id_dpto')

            ->where([
                ['sis_usua_sede.id_usuario', '=', $id_usuario],
                ['sis_usua_sede.estado', '=', 1],
                ['sis_sede.estado', '=', 1],
                ['sis_sede.id_empresa', '=', $id_empresa]
            ])
            ->get();
        return $sedes;
    }




    // public function listarDetalleOrden(Request $request)
    // {

    //     $idEmpresa = $request->idEmpresa;
    //     $idSede = $request->idSede;
    //     $fechaRegistroDesde = $request->fechaRegistroDesde;
    //     $fechaRegistroHasta = $request->fechaRegistroHasta;
    //     $idEstado = $request->idEstado;


    //     $orden_obj = Orden::select(
    //         'log_ord_compra.id_orden_compra as id_orden_compra',
    //         'log_ord_compra.id_grupo_cotizacion',
    //         'log_ord_compra.id_tp_documento',
    //         'log_ord_compra.fecha',
    //         'log_ord_compra.incluye_igv',
    //         'log_ord_compra.id_usuario',
    //         'log_ord_compra.id_moneda',
    //         'sis_moneda.simbolo as simbolo_moneda',
    //         'log_ord_compra.igv_porcentaje',
    //         'log_ord_compra.monto_subtotal',
    //         'log_ord_compra.monto_igv',
    //         'log_ord_compra.monto_total',
    //         'log_ord_compra.estado',
    //         'log_ord_compra.id_proveedor',
    //         'log_ord_compra.codigo',
    //         'log_ord_compra.id_cotizacion',
    //         'log_ord_compra.id_condicion',
    //         'log_ord_compra.plazo_dias',
    //         'log_ord_compra.id_cta_principal',
    //         'log_ord_compra.id_cta_alternativa',
    //         'log_ord_compra.id_cta_detraccion',
    //         'log_ord_compra.personal_autorizado_1',
    //         'log_ord_compra.personal_autorizado_2',
    //         'log_ord_compra.plazo_entrega',
    //         'log_ord_compra.en_almacen',
    //         'log_ord_compra.id_occ',
    //         'log_ord_compra.id_sede',
    //         'log_ord_compra.codigo_softlink',
    //         'log_ord_compra.observacion',
    //         'adm_contri.id_contribuyente',
    //         'adm_contri.razon_social',
    //         'adm_contri.nro_documento',
    //         'sis_sede.descripcion as empresa_sede',
    //         'log_det_ord_compra.id_detalle_orden as detalle_orden_id_detalle_orden',
    //         'log_det_ord_compra.id_orden_compra as detalle_orden_id_orden_compra',
    //         'log_det_ord_compra.id_item as detalle_orden_id_item',
    //         'log_det_ord_compra.garantia as detalle_orden_garantia',
    //         'log_det_ord_compra.id_valorizacion_cotizacion as detalle_orden_id_valorizacion_cotizacion',
    //         'log_det_ord_compra.estado as id_detalle_orden_estado',
    //         'estados_compra.descripcion as detalle_orden_estado',
    //         'log_det_ord_compra.personal_autorizado as detalle_orden_personal_autorizado',
    //         'log_det_ord_compra.lugar_despacho as detalle_orden_lugar_despacho',
    //         'log_det_ord_compra.descripcion_adicional as detalle_orden_descripcion_adicional',
    //         'log_det_ord_compra.cantidad as detalle_orden_cantidad',
    //         'log_det_ord_compra.precio as detalle_orden_precio',
    //         'cc_am.moneda_pvu',
    //         'cc_am_filas.cantidad as cdc_cantidad',
    //         'cc_am_filas.pvu_oc as cdc_precio',
    //         'log_det_ord_compra.id_unidad_medida as detalle_orden_id_unidad_medida',
    //         'log_det_ord_compra.subtotal as detalle_orden_subtotal',
    //         'log_det_ord_compra.id_detalle_requerimiento as detalle_orden_id_detalle_requerimiento',
    //         'log_det_ord_compra.tipo_item_id',
    //         'alm_det_req.observacion as observacion_requerimiento',
    //         'alm_req.concepto',
    //         'alm_req.id_cliente',
    //         'contri_cli.razon_social as razon_social_cliente',
    //         'alm_req.id_requerimiento',
    //         'alm_req.codigo as codigo_requerimiento',
    //         'alm_prod.codigo AS alm_prod_codigo',
    //         'alm_prod.part_number',
    //         'alm_cat_prod.descripcion as categoria',
    //         'alm_subcat.descripcion as subcategoria',
    //         'alm_prod.descripcion AS alm_prod_descripcion'
    //     )

    //         ->leftJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_orden_compra', '=', 'log_ord_compra.id_orden_compra')
    //         ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
    //         ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
    //         ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
    //         ->leftJoin('configuracion.sis_moneda', 'log_ord_compra.id_moneda', '=', 'sis_moneda.id_moneda')
    //         ->leftJoin('logistica.estados_compra', 'log_det_ord_compra.estado', '=', 'estados_compra.id_estado')
    //         ->leftJoin('almacen.alm_prod', 'log_det_ord_compra.id_producto', '=', 'alm_prod.id_producto')
    //         ->leftJoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
    //         ->leftJoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
    //         ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
    //         ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
    //         ->leftJoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
    //         ->leftJoin('contabilidad.adm_contri as contri_cli', 'contri_cli.id_contribuyente', '=', 'com_cliente.id_contribuyente')
    //         ->leftJoin('mgcp_cuadro_costos.cc_am_filas', 'cc_am_filas.id', '=', 'alm_det_req.id_cc_am_filas')
    //         ->leftJoin('mgcp_cuadro_costos.cc_am', 'cc_am_filas.id_cc_am', '=', 'cc_am.id_cc')

    //         ->when(($idEmpresa > 0), function ($query) use ($idEmpresa) {
    //             return $query->whereRaw('sis_sede.id_empresa = ' . $idEmpresa);
    //         })
    //         ->when(($idSede > 0), function ($query) use ($idSede) {
    //             return $query->whereRaw('sis_sede.id_sede = ' . $idSede);
    //         })

    //         ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta == 'SIN_FILTRO')), function ($query) use ($fechaRegistroDesde) {
    //             return $query->where('log_ord_compra.fecha', '>=', $fechaRegistroDesde);
    //         })
    //         ->when((($fechaRegistroDesde == 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use ($fechaRegistroHasta) {
    //             return $query->where('log_ord_compra.fecha', '<=', $fechaRegistroHasta);
    //         })
    //         ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use ($fechaRegistroDesde, $fechaRegistroHasta) {
    //             return $query->whereBetween('log_ord_compra.fecha', [$fechaRegistroDesde, $fechaRegistroHasta]);
    //         })

    //         ->when(($idEstado > 0), function ($query) use ($idEstado) {
    //             return $query->whereRaw('log_ord_compra.estado = ' . $idEstado);
    //         })

    //         ->where([
    //             // ['log_ord_compra.codigo', '=', 'OC-21080176'],
    //             ['log_ord_compra.estado', '!=', 7],
    //             // $tipoOrden >0 ? ['log_ord_compra.id_tp_documento',$tipoOrden]:[null],
    //             // $empresa >0 ? ['sis_sede.id_empresa',$empresa]:[null],
    //             // $sede >0 ? ['sis_sede.id_sede',$sede]:[null],
    //             // ($tipoProveedor =='NACIONAL') ? ['adm_contri.id_pais','=','170']:($tipoProveedor =='EXTRANJERO' ? ['adm_contri.id_pais','=','170']:[null]),
    //             // $estado >0 ? ['log_ord_compra.estado',$estado]:[null]
    //         ])
    //         // ->when(($vinculadoPor !='null'), function($query) use ($vinculadoPor)  {
    //         //     if($vinculadoPor== 'REQUERIMIENTO'){
    //         //         $whereVinculadoPor='log_det_ord_compra.id_detalle_requerimiento > 0';
    //         //     }elseif($vinculadoPor == 'CUADRO_COMPARATIVO'){
    //         //         $whereVinculadoPor='log_det_ord_compra.detalle_cuadro_comparativo_id > 0';
    //         //     }
    //         //     return $query->WhereIn('log_ord_compra.id_orden_compra', function($query) use ($whereVinculadoPor)
    //         //     {
    //         //         $query->select('log_det_ord_compra.id_orden_compra')
    //         //         ->from('logistica.log_det_ord_compra')
    //         //         ->whereRaw($whereVinculadoPor);
    //         //     });
    //         // })
    //         // ->when(($enAlmacen =='true'), function($query)  {
    //         //     return $query->WhereIn('log_ord_compra.id_orden_compra', function($query)
    //         //     {
    //         //         $query->select('log_det_ord_compra.id_orden_compra')
    //         //             ->from('logistica.log_det_ord_compra')
    //         //             ->leftjoin('almacen.guia_com_det', 'guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden')
    //         //             ->whereRaw('guia_com_det.id_guia_com_det > 0');
    //         //     });
    //         // })
    //         ->orderBy('log_ord_compra.fecha', 'desc')
    //         // ->whereRaw('coalesce((log_det_ord_compra.cantidad * log_det_ord_compra.precio) ,0) '.$simboloSubtotal.' '.$subtotal)
    //         ->get();

    //     // $orden_list = collect($orden_obj)->map(function($x){ return (array) $x; })->toArray();
    //     // Debugbar::info($orden_obj);

    //     $output['data'] = $orden_obj;
    //     return $output;
    // }

    function documentosVinculadosOrden($id_orden)
    {
        $status = 0;
        $id_cc = '';
        $tipo_cuadro = '';
        $id_oportunidad = '';
        $documentos = [];

        $log_ord_compra = DB::table('logistica.log_ord_compra')
            ->select(
                'log_ord_compra.id_orden_compra',
                'log_ord_compra.codigo_softlink',
                'alm_req.id_cc',
                'alm_req.tipo_cuadro',
                'log_ord_compra.estado as estado_orden',
                'alm_req.codigo as codigo_requerimiento',
                'log_ord_compra.codigo as codigo_orden',
                'alm_det_req.id_requerimiento'
            )
            ->leftJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_orden_compra', '=', 'log_ord_compra.id_orden_compra')
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->where([
                ['log_ord_compra.id_orden_compra', '=', $id_orden]
            ])
            ->get();

        if (count($log_ord_compra) > 0) {
            foreach ($log_ord_compra as $data) {
                $id_cc = $data->id_cc;
                $tipo_cuadro = $data->tipo_cuadro;
            }

            $cc = DB::table('mgcp_cuadro_costos.cc')
                ->select('cc.id_oportunidad')
                ->where([
                    ['cc.id', '=', $id_cc]
                ])
                ->get();

            if (count($cc) > 0) {

                $id_oportunidad = $cc->first()->id_oportunidad;
                $oc_propias = DB::table('mgcp_acuerdo_marco.oc_propias')
                    ->select('oc_propias.id', 'oc_propias.url_oc_fisica')
                    ->where([
                        ['oc_propias.id_oportunidad', '=', $id_oportunidad]
                    ])
                    ->get();

                if (count($oc_propias) > 0) {
                    $orden_electronica = "https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=" . ($oc_propias->first()->id) . "&ImprimirCompleto=1";
                    $orden_fisica = $oc_propias->first()->url_oc_fisica;
                    $documentos[] = [
                        'orden_fisica' => $orden_fisica,
                        'orden_electronica' => $orden_electronica,
                    ];
                    $status = 200;
                } else {
                    $status = 204;
                }
            } else {
                $status = 204;
            }
        } else {
            $status = 204;
        }

        $output = ['status' => $status, 'data' => $documentos];

        return response()->json($output);
    }

    public function cantidadCompradaDetalleOrden($id_detalle_requerimiento)
    {
        $cantiadComprada = 0;
        $det_ord_compra = DB::table('logistica.log_det_ord_compra')
            ->select('log_det_ord_compra.*')
            ->where(
                [
                    ['log_det_ord_compra.id_detalle_requerimiento', '=', $id_detalle_requerimiento],
                    ['log_det_ord_compra.estado', '!=', 7]
                ]
            )
            ->get();

        if (isset($det_ord_compra) && sizeof($det_ord_compra) > 0) {
            foreach ($det_ord_compra as $data) {
                $cantiadComprada += $data->cantidad;
            }
        }
        return $cantiadComprada;
    }


    public function ObtenerRequerimientoDetallado(Request $request)
    {
        // public function ObtenerRequerimientoDetallado($requerimientoList ){
        $requerimientoList = $request->requerimientoList;

        $data = Requerimiento::with([
            'empresa',
            'sede',
            'moneda',
            'detalle' => function ($q) {
                $q->whereNotIn('alm_det_req.estado', [7, 5, 28]);
            }, 'detalle.estado',
            'detalle.producto.unidadMedida',
            'detalle.unidadMedida',
            'detalle.reserva' => function ($q) {
                $q->where('alm_reserva.estado', '=', 1);
            }
        ])->whereIn('id_requerimiento', $requerimientoList)->get();

        return $data;
    }


    // public function get_detalle_requerimiento_orden(Request $request )
    public function get_detalle_requerimiento_orden($id)
    {

        // $requerimientoList = $request->requerimientoList;
        $requerimientoList = $id;
        // return $requerimientoList;
        // return response()->json($output);


        $alm_req = DB::table('almacen.alm_req')
            ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->leftJoin('contabilidad.adm_contri as contrib', 'adm_empresa.id_contribuyente', '=', 'contrib.id_contribuyente')
            ->leftJoin('configuracion.ubi_dis as dis_empresa_sede', 'sis_sede.id_ubigeo', '=', 'dis_empresa_sede.id_dis')
            ->leftJoin('configuracion.ubi_prov as prov_empresa_sede', 'dis_empresa_sede.id_prov', '=', 'prov_empresa_sede.id_prov')
            ->leftJoin('configuracion.ubi_dpto as dpto_empresa_sede', 'prov_empresa_sede.id_dpto', '=', 'dpto_empresa_sede.id_dpto')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')

            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
            // ->leftJoin('proyectos.proy_op_com', 'proy_op_com.id_op_com', '=', 'alm_req.id_op_com')
            ->leftJoin('proyectos.proy_presup', 'alm_req.id_presupuesto', '=', 'proy_presup.id_presupuesto')
            ->leftJoin('rrhh.rrhh_perso as perso_natural', 'alm_req.id_persona', '=', 'perso_natural.id_persona')
            ->leftJoin('comercial.com_cliente', 'alm_req.id_cliente', '=', 'com_cliente.id_cliente')
            ->leftJoin('contabilidad.adm_contri as contri_cliente', 'com_cliente.id_contribuyente', '=', 'contri_cliente.id_contribuyente')
            ->leftJoin('configuracion.ubi_dis', 'alm_req.id_ubigeo_entrega', '=', 'ubi_dis.id_dis')
            ->leftJoin('configuracion.ubi_prov', 'ubi_dis.id_prov', '=', 'ubi_prov.id_prov')
            ->leftJoin('configuracion.ubi_dpto', 'ubi_prov.id_dpto', '=', 'ubi_dpto.id_dpto')

            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftJoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('mgcp_cuadro_costos.responsables', 'responsables.id', '=', 'oportunidades.id_responsable')
            ->leftJoin('mgcp_usuarios.users', 'users.id', '=', 'oportunidades.id_responsable')




            ->select(
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.concepto',
                'alm_req.id_moneda',
                'alm_req.id_periodo',
                'alm_req.id_prioridad',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'sis_sede.id_empresa',
                'alm_req.id_grupo',
                'contrib.razon_social as razon_social_empresa',
                'contrib.nro_documento AS nro_documento_empresa',
                'sis_sede.codigo as codigo_sede_empresa',
                'sis_sede.direccion AS direccion_fiscal_empresa_sede',
                'sis_sede.id_ubigeo AS id_ubigeo_empresa_sede',
                DB::raw("concat(dis_empresa_sede.descripcion, ' - ' ,prov_empresa_sede.descripcion, ' - ' ,dpto_empresa_sede.descripcion)  AS ubigeo_empresa_sede"),

                'adm_empresa.logo_empresa',
                'alm_req.fecha_requerimiento',
                'alm_req.id_periodo',
                'alm_req.id_tipo_requerimiento',
                'alm_req.observacion as observacion_requerimiento',
                'alm_tp_req.descripcion AS tp_req_descripcion',
                'alm_req.id_usuario',
                DB::raw("concat(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno, ' ', rrhh_perso.apellido_materno)  AS persona"),
                'sis_usua.usuario',
                'alm_req.id_rol',
                'rrhh_rol.id_rol_concepto',
                'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
                'alm_req.id_area',
                'adm_area.descripcion AS area_descripcion',
                // 'proy_op_com.codigo as codigo_op_com',
                // 'proy_op_com.descripcion as descripcion_op_com',
                'alm_req.id_presupuesto',
                'alm_req.fecha_registro',
                'alm_req.estado',
                'alm_req.id_sede',
                'alm_req.id_persona',
                'perso_natural.nro_documento as dni_persona',
                DB::raw("concat(perso_natural.nombres, ' ', perso_natural.apellido_paterno, ' ', perso_natural.apellido_materno)  AS nombre_persona"),
                'alm_req.tipo_cliente',
                'alm_req.id_cliente',
                'contri_cliente.nro_documento as cliente_ruc',
                'contri_cliente.razon_social as cliente_razon_social',
                'alm_req.id_ubigeo_entrega',
                DB::raw("concat(ubi_dis.descripcion, ' ', ubi_prov.descripcion, ' ' , ubi_dpto.descripcion)  AS name_ubigeo"),
                'alm_req.direccion_entrega',
                'alm_req.id_almacen',
                'oportunidades.codigo_oportunidad',
                'users.name as nombre_ejecutivo_responsable',
                'alm_req.id_cc',
                DB::raw("(CASE WHEN alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->whereIn('alm_req.id_requerimiento', [$requerimientoList])
            // ->WhereIn('alm_req.tiene_transformacion',[true,1])
            ->orderBy('alm_req.id_requerimiento', 'desc')
            ->get();

        if (sizeof($alm_req) <= 0) {
            $alm_req = [];
            return response()->json($alm_req);
        } else {

            foreach ($alm_req as $data) {

                $id_requerimiento = $data->id_requerimiento;

                $requerimiento[] = [
                    'id_requerimiento' => $data->id_requerimiento,
                    'codigo' => $data->codigo,
                    'concepto' => $data->concepto,
                    'id_moneda' => $data->id_moneda,
                    'id_periodo' => $data->id_periodo,
                    'estado_doc' => $data->estado_doc,
                    'bootstrap_color' => $data->bootstrap_color,
                    'id_prioridad' => $data->id_prioridad,
                    'id_empresa' => $data->id_empresa,
                    'id_grupo' => $data->id_grupo,
                    'id_sede' => $data->id_sede,
                    'id_ubigeo_empresa_sede' => $data->id_ubigeo_empresa_sede,
                    'razon_social_empresa' => $data->razon_social_empresa,
                    'nro_documento_empresa' => $data->nro_documento_empresa,
                    'direccion_fiscal_empresa_sede' => $data->direccion_fiscal_empresa_sede,
                    'ubigeo_empresa_sede' => $data->ubigeo_empresa_sede,
                    'codigo_sede_empresa' => $data->codigo_sede_empresa,
                    'logo_empresa' => $data->logo_empresa,
                    'fecha_requerimiento' => $data->fecha_requerimiento,
                    'id_periodo' => $data->id_periodo,
                    'id_tipo_requerimiento' => $data->id_tipo_requerimiento,
                    'tipo_requerimiento' => $data->tp_req_descripcion,
                    'id_usuario' => $data->id_usuario,
                    'persona' => $data->persona,
                    'usuario' => $data->usuario,
                    'id_rol' => $data->id_rol,
                    'id_area' => $data->id_area,
                    'area_descripcion' => $data->area_descripcion,
                    'id_presupuesto' => $data->id_presupuesto,
                    'observacion_requerimiento' => $data->observacion_requerimiento,
                    'fecha_registro' => $data->fecha_registro,
                    'estado' => $data->estado,
                    'estado_desc' => $data->estado_desc,
                    'id_persona' => $data->id_persona,
                    'dni_persona' => $data->dni_persona,
                    'nombre_persona' => $data->nombre_persona,
                    'tipo_cliente' => $data->tipo_cliente,
                    'id_cliente' => $data->id_cliente,
                    'cliente_ruc' => $data->cliente_ruc,
                    'cliente_razon_social' => $data->cliente_razon_social,
                    'id_ubigeo_entrega' => $data->id_ubigeo_entrega,
                    'name_ubigeo' => $data->name_ubigeo,
                    'direccion_entrega' => $data->direccion_entrega,
                    'id_almacen' => $data->id_almacen,
                    'codigo_oportunidad' => $data->codigo_oportunidad,
                    'nombre_ejecutivo_responsable' => $data->nombre_ejecutivo_responsable,
                    'id_cc' => $data->id_cc


                ];
            };

            $alm_det_req = DB::table('almacen.alm_det_req')
                ->leftJoin('almacen.alm_prod', 'alm_det_req.id_producto', '=', 'alm_prod.id_producto')
                ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
                ->leftJoin('almacen.alm_und_medida as und_medida_det_req', 'alm_det_req.id_unidad_medida', '=', 'und_medida_det_req.id_unidad_medida')
                ->leftJoin('almacen.alm_det_req_adjuntos', 'alm_det_req_adjuntos.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')

                ->select(
                    'alm_det_req.id_detalle_requerimiento',
                    'alm_req.id_requerimiento',
                    'alm_req.id_moneda',
                    'alm_req.codigo AS codigo_requerimiento',
                    'alm_det_req.id_requerimiento',
                    'alm_det_req.id_item AS id_item_alm_det_req',
                    'alm_det_req.precio_unitario',
                    'alm_det_req.subtotal',
                    'alm_det_req.cantidad',
                    'alm_det_req.id_unidad_medida',
                    'und_medida_det_req.descripcion AS unidad_medida',
                    'alm_det_req.fecha_registro AS fecha_registro_alm_det_req',
                    'alm_det_req.lugar_entrega',
                    'alm_det_req.descripcion_adicional',
                    'alm_det_req.id_tipo_item',
                    'alm_det_req.estado',
                    // 'alm_det_req.stock_comprometido',
                    'alm_det_req.observacion',



                    'alm_det_req.id_producto',
                    'alm_prod.codigo AS alm_prod_codigo',
                    'alm_prod.part_number',
                    'alm_prod.descripcion AS alm_prod_descripcion',

                    'alm_det_req_adjuntos.id_adjunto AS adjunto_id_adjunto',
                    'alm_det_req_adjuntos.archivo AS adjunto_archivo',
                    'alm_det_req_adjuntos.estado AS adjunto_estado',
                    'alm_det_req_adjuntos.fecha_registro AS adjunto_fecha_registro',
                    'alm_det_req_adjuntos.id_detalle_requerimiento AS adjunto_id_detalle_requerimiento'
                )
                ->whereIn('alm_req.id_requerimiento', [$requerimientoList])
                ->whereIn('alm_det_req.tiene_transformacion', [false])
                ->whereNotIn('alm_det_req.estado', [7])
                ->orderBy('alm_prod.id_producto', 'asc')
                ->get();

            // archivos adjuntos de items
            if (isset($alm_det_req)) {
                $detalle_requerimiento_adjunto = [];
                foreach ($alm_det_req as $data) {
                    $detalle_requerimiento_adjunto[] = [
                        'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                        'id_adjunto' => $data->adjunto_id_adjunto,
                        'archivo' => $data->adjunto_archivo,
                        'id_detalle_requerimiento' => $data->adjunto_id_detalle_requerimiento,
                        'fecha_registro' => $data->adjunto_fecha_registro,
                        'estado' => $data->adjunto_estado
                    ];
                }
            } else {
                $detalle_requerimiento_adjunto = [];
            }

            // $total = 0;
            if (isset($alm_det_req)) {
                $lastId = "";
                $detalle_requerimiento = [];
                foreach ($alm_det_req as $data) {
                    if ($data->id_detalle_requerimiento !== $lastId) {
                        $subtotal = +$data->cantidad *  $data->precio_unitario;
                        // $total = $subtotal;
                        $detalle_requerimiento[] = [
                            'id'                        => $data->id_detalle_requerimiento,
                            'id_detalle_requerimiento'  => $data->id_detalle_requerimiento,
                            'id_requerimiento'          => $data->id_requerimiento,
                            'codigo_requerimiento'      => $data->codigo_requerimiento,
                            'cantidad'                  => $data->cantidad - (isset($data->stock_comprometido) ? $data->stock_comprometido : 0) - ($this->cantidadCompradaDetalleOrden($data->id_detalle_requerimiento)),
                            'id_moneda'                 => $data->id_moneda,
                            'id_unidad_medida'          => $data->id_unidad_medida,
                            'unidad_medida'             => $data->unidad_medida,
                            'precio_unitario'           => $data->precio_unitario,
                            'subtotal'                  => $data->subtotal,
                            'descripcion_adicional'     => $data->descripcion_adicional,
                            'lugar_entrega'             => $data->lugar_entrega,
                            'fecha_registro'            => $data->fecha_registro_alm_det_req,
                            'estado'                    => $data->estado,
                            'id_tipo_item'              => $data->id_tipo_item,
                            'id_producto'               => $data->id_producto,
                            'alm_prod_codigo'           => $data->alm_prod_codigo,
                            'part_number'               => $data->part_number,
                            'descripcion'               => $data->alm_prod_descripcion,
                            // 'stock_comprometido'        => $data->stock_comprometido,
                            'observacion'               => $data->observacion,
                            'cantidad_a_comprar'        => 0,
                            'subtotal'               =>  $subtotal,
                        ];
                        $lastId = $data->id_detalle_requerimiento;
                    }
                }

                // insertar adjuntos
                for ($j = 0; $j < sizeof($detalle_requerimiento); $j++) {
                    for ($i = 0; $i < sizeof($detalle_requerimiento_adjunto); $i++) {
                        if ($detalle_requerimiento[$j]['id_detalle_requerimiento'] === $detalle_requerimiento_adjunto[$i]['id_detalle_requerimiento']) {
                            if ($detalle_requerimiento_adjunto[$i]['estado'] === NUll) {
                                $detalle_requerimiento_adjunto[$i]['estado'] = 0;
                            }
                            $detalle_requerimiento[$j]['adjunto'][] = $detalle_requerimiento_adjunto[$i];
                        }
                    }
                }
                // end insertar adjuntos

            } else {

                $detalle_requerimiento = [];
            }
        }

        // $collect = collect($requerimiento);
        // $collect->put('total',$total);

        $data = [
            "requerimiento" => $requerimiento,
            "det_req" => $detalle_requerimiento
        ];

        return $data;
    }

    function update_estado_orden(Request $request)
    {
        $id_orden_compra = $request->id_orden_compra;
        $id_estado_orden_selected = $request->id_estado_orden_selected;

        $update_log_ord_compra = DB::table('logistica.log_ord_compra')
            ->where([
                ['id_orden_compra', $id_orden_compra]
            ])
            ->update(
                [
                    'estado' => $id_estado_orden_selected
                ]
            );

        DB::table('logistica.log_det_ord_compra')
            ->where([
                ['id_orden_compra', $id_orden_compra],
                ['estado', '!=', 7]
            ])
            ->update(
                [
                    'estado' => $id_estado_orden_selected
                ]
            );

        return $update_log_ord_compra;
    }

    function update_estado_item_orden(Request $request)
    {
        $id_detalle_orden_compra = $request->id_detalle_orden_compra;
        $id_estado_detalle_orden_selected = $request->id_estado_detalle_orden_selected;

        $update_log_det_ord_compra = DB::table('logistica.log_det_ord_compra')
            ->where([
                ['id_detalle_orden', $id_detalle_orden_compra]
            ])
            ->update(
                [
                    'estado' => $id_estado_detalle_orden_selected
                ]
            );
        $detalleOrden = OrdenCompraDetalle::where('id_detalle_orden', $id_detalle_orden_compra)->first();

        $this->evaluarActualizarEstadoOrdenYDetalle($detalleOrden->id_orden_compra, $id_estado_detalle_orden_selected);

        return $update_log_det_ord_compra;
    }

    function evaluarActualizarEstadoOrdenYDetalle($idOrden, $estadoReferencial)
    {
        $orden = Orden::find($idOrden);
        $DetalleOrden = OrdenCompraDetalle::where([['id_orden_compra', $idOrden], ['estado', '!=', 7]])->get();
        $cantidadDetalleOrden = count($DetalleOrden);
        $cantidadEstadoCoincidente = 0;
        foreach ($DetalleOrden as $detalle) {
            if ($detalle->estado == $estadoReferencial) {
                $cantidadEstadoCoincidente++;
            }
        }
        if ($cantidadEstadoCoincidente == $cantidadDetalleOrden) {
            // actualizar cabecera de orden
            $orden->estado = $estadoReferencial;
            $orden->save();
        }
    }

    function update_estado_detalle_requerimiento($id_detalle_requerimiento, $estado)
    {

        $status = 500;
        $alm_det_req = DB::table('almacen.alm_det_req')
            ->where([
                ['id_detalle_requerimiento', $id_detalle_requerimiento]
            ])
            ->update(
                [
                    'estado' => $estado
                ]
            );
        if (isset($alm_det_req) && $alm_det_req > 0) {
            $status = 200;
        } else {
            $status = 204;
        }
        $output = ['status' => $status];

        return $output;
    }

    function view_listar_ordenes()
    {

        $empresas = Empresa::mostrar();
        $grupos = Grupo::mostrar();
        $estados = EstadoCompra::mostrar();
        $prioridades = Prioridad::mostrar();
        $tiposDestinatario = RequerimientoPagoTipoDestinatario::mostrar();
        $bancos = Banco::mostrar();
        $tipo_cuenta = TipoCuenta::mostrar();
        $monedas = Moneda::mostrar();
        $tipos_documentos = Identidad::mostrar();

        $array_accesos = [];
        $accesos_usuario = AccesosUsuarios::where('estado', 1)->where('id_usuario', Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos, $value->id_acceso);
        }
        return view('logistica/gestion_logistica/compras/ordenes/listado/listar_ordenes', compact('prioridades', 'empresas', 'grupos', 'estados', 'tiposDestinatario', 'bancos', 'tipo_cuenta', 'monedas', 'tipos_documentos', 'array_accesos'));
    }

    function consult_doc_aprob($id_doc, $tp_doc)
    {
        $sql = DB::table('administracion.adm_documentos_aprob')->where([['id_tp_documento', '=', $tp_doc], ['id_doc', '=', $id_doc]])->get();

        if ($sql->count() > 0) {
            $val = $sql->first()->id_doc_aprob;
        } else {
            $val = 0;
        }
        return $val;
    }

    public function obtenerFacturas($idOrden)
    {
        $facturas = [];
        $sql_facturas = DB::table('logistica.log_det_ord_compra')
            ->select(DB::raw("concat(doc_com.serie, '-', doc_com.numero) AS facturas"))
            ->join('almacen.guia_com_det', 'guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden')
            ->leftjoin('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', '=', 'guia_com_det.id_guia_com_det')
            ->leftjoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'doc_com_det.id_doc')
            ->where('log_det_ord_compra.id_orden_compra', $idOrden)
            ->get();
        if (count($sql_facturas) > 0) {
            foreach ($sql_facturas as $key => $value) {
                $facturas[] = $value->facturas;
            }
        }
        return array_values(array_unique($facturas));
    }
    public function listaOrdenesElaboradas(Request $request)
    {
        $filtro= $request->filtro;
        $ordenes = OrdenesView::where([['id_estado', '>=', 1], ['id_tp_documento', '!=', 13]])
        ->when(($filtro !=null || $filtro !='SIN_FILTRO' ), function ($query) use ($filtro) {
            switch ($filtro) {
                case 'ORDENES_SIN_ENVIAR_A_PAGO':
                    return $query->whereIn('ordenes_view.estado_pago', [1]);

                    break;
                case 'ORDENES_AUTORIZADAS_PARA_PAGO':
                    return $query->whereIn('ordenes_view.estado_pago', [5]);
                    break;

                default:
                    break;
            }
        })
        ;
        $ordenes = $ordenes->whereDate('fecha_emision','>=',$request->fecha_inicio);
        $ordenes = $ordenes->whereDate('fecha_emision','<=',$request->fecha_actual);
        // return datatables($ordenes)
        // ->filterColumn('codigo_requerimiento', function ($query, $keyword) {
        //     $query->whereHas('data_requerimiento.codigo_requerimiento', function ($q) use ($keyword) {
        //     $q->where('codigo_requerimiento', 'LIKE', "%{$keyword}%");
        //     });
        // })
        return datatables($ordenes)
        // ->addColumn('estado_prioridad', function ($data) {
        //     $orden_detalle
        //     return $data;
        // })
            // ->editColumn('codigo_requerimiento', function($ordenes){
            //     $payload='';
            //     foreach ($ordenes->data_requerimiento as $key) {
            //         $payload.= '<a href="/necesidades/requerimiento/elaboracion/index?id='.$key['id_requerimiento'].'" target="_blank" title="Abrir Requerimiento">'.$key['codigo_requerimiento'].'</a>';
            //     }
            //     return $payload;
            // })
            // ->filterColumn('codigo_requerimiento', function ($query, $keyword) {
            //     $query->whereJsonContains('data_requerimiento', ['codigo_requerimiento'=> $keyword]);

            // })
            // ->rawColumns(['codigo_requerimiento'])
            ->toJson();
    }

    public function listaItemsOrdenesElaboradas(Request $request)
    {

        $itemsOrdenes = ItemsOrdenesView::where([['id_estado', '>=', 1], ['id_tp_documento', '!=', 13]]);
        return datatables($itemsOrdenes)

            ->toJson();
    }
    // public function listarOrdenes(Request $request)
    // {

    //     $tipoOrden = $request->tipoOrden;
    //     $idEmpresa = $request->idEmpresa;
    //     $idSede = $request->idSede;
    //     // $idGrupo = $request->idGrupo;
    //     // $division = $request->idDivision;
    //     $fechaRegistroDesde = $request->fechaRegistroDesde;
    //     $fechaRegistroHasta = $request->fechaRegistroHasta;
    //     $idEstado = $request->idEstado;


    //     $ord_compra = Orden::select(
    //         'log_ord_compra.*',
    //         'log_ord_compra.monto_total as monto_total_orden',
    //         'sis_sede.descripcion as descripcion_sede_empresa',

    //         // 'log_cdn_pago.descripcion as condicion',
    //         DB::raw("(CASE
    //         WHEN log_ord_compra.id_condicion = 1 THEN log_cdn_pago.descripcion
    //         WHEN log_ord_compra.id_condicion = 2 THEN log_cdn_pago.descripcion || ' ' || log_ord_compra.plazo_dias  || ' Días'
    //         ELSE null END) AS condicion
    //         "),
    //         'sis_moneda.simbolo as moneda_simbolo',
    //         'sis_moneda.descripcion as moneda_descripcion',
    //         'adm_contri.id_contribuyente',
    //         'adm_contri.razon_social',
    //         'adm_contri.nro_documento',
    //         'cta_prin.nro_cuenta as nro_cuenta_prin',
    //         'cta_alter.nro_cuenta as nro_cuenta_alter',
    //         'cta_detra.nro_cuenta as nro_cuenta_detra',
    //         'estados_compra.descripcion as estado_doc',
    //         'log_ord_compra.estado',
    //         'log_ord_compra_pago.id_pago',
    //         'log_ord_compra_pago.detalle_pago',
    //         'log_ord_compra_pago.archivo_adjunto'

    //         // DB::raw("(SELECT  coalesce(sum((log_det_ord_compra.cantidad * log_det_ord_compra.precio))*1.18 ,0) AS monto_total_orden
    //         // FROM logistica.log_det_ord_compra
    //         // WHERE   log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra AND
    //         //         log_det_ord_compra.estado != 7) AS monto_total_orden"),
    //         // DB::raw("(SELECT  coalesce(oc_propias_view.monto_soles) AS monto_total_presup
    //         // FROM logistica.log_det_ord_compra
    //         // INNER JOIN almacen.alm_det_req on alm_det_req.id_detalle_requerimiento = log_det_ord_compra.id_detalle_requerimiento
    //         // INNER JOIN almacen.alm_req on alm_req.id_requerimiento = alm_det_req.id_requerimiento
    //         // INNER JOIN mgcp_cuadro_costos.cc on cc.id = alm_req.id_cc
    //         // INNER JOIN mgcp_ordenes_compra.oc_propias_view on oc_propias_view.id_oportunidad = cc.id_oportunidad

    //         // WHERE log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra AND
    //         // logistica.log_det_ord_compra.estado != 7 LIMIT 1) AS monto_total_presup")
    //     )
    //         ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
    //         ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
    //         ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
    //         ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
    //         ->leftjoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_ord_compra.id_condicion')
    //         ->leftjoin('contabilidad.adm_cta_contri as cta_prin', 'cta_prin.id_cuenta_contribuyente', '=', 'log_ord_compra.id_cta_principal')
    //         ->leftjoin('contabilidad.adm_cta_contri as cta_alter', 'cta_alter.id_cuenta_contribuyente', '=', 'log_ord_compra.id_cta_alternativa')
    //         ->leftjoin('contabilidad.adm_cta_contri as cta_detra', 'cta_detra.id_cuenta_contribuyente', '=', 'log_ord_compra.id_cta_detraccion')
    //         ->leftjoin('logistica.estados_compra', 'estados_compra.id_estado', '=', 'log_ord_compra.estado')
    //         ->leftjoin('logistica.log_ord_compra_pago', 'log_ord_compra_pago.id_orden_compra', '=', 'log_ord_compra.id_orden_compra')

    //         ->when(($tipoOrden > 0), function ($query) use ($tipoOrden) {
    //             return $query->whereRaw('log_ord_compra.id_tp_documento = ' . $tipoOrden);
    //         })
    //         ->when(($idEmpresa > 0), function ($query) use ($idEmpresa) {
    //             return $query->whereRaw('sis_sede.id_empresa = ' . $idEmpresa);
    //         })
    //         ->when(($idSede > 0), function ($query) use ($idSede) {
    //             return $query->whereRaw('sis_sede.id_sede = ' . $idSede);
    //         })

    //         ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta == 'SIN_FILTRO')), function ($query) use ($fechaRegistroDesde) {
    //             return $query->where('log_ord_compra.fecha', '>=', $fechaRegistroDesde);
    //         })
    //         ->when((($fechaRegistroDesde == 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use ($fechaRegistroHasta) {
    //             return $query->where('log_ord_compra.fecha', '<=', $fechaRegistroHasta);
    //         })
    //         ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use ($fechaRegistroDesde, $fechaRegistroHasta) {
    //             return $query->whereBetween('log_ord_compra.fecha', [$fechaRegistroDesde, $fechaRegistroHasta]);
    //         })

    //         ->when(($idEstado > 0), function ($query) use ($idEstado) {
    //             return $query->whereRaw('log_ord_compra.estado = ' . $idEstado);
    //         })


    //         ->where([
    //             ['log_ord_compra.estado', '!=', 7]


    //         ])


    //         ->orderBy('log_ord_compra.fecha', 'desc')


    //         ->get();


    //     $data_orden = [];
    //     if (count($ord_compra) > 0) {
    //         foreach ($ord_compra as $element) {

    //             $fechaHoy = Carbon::now();
    //             $fechaOrden = Carbon::create($element->fecha);
    //             $fechaLlegada = $fechaOrden->addDays($element->plazo_entrega);

    //             $data_orden[] = [
    //                 'id_orden_compra' => $element->id_orden_compra,
    //                 'id_tp_documento' => $element->id_tp_documento,
    //                 'fecha' => $element->fecha,
    //                 'fecha_formato' => $element->fecha_formato,
    //                 'codigo' => $element->codigo,
    //                 'descripcion_sede_empresa' => $element->descripcion_sede_empresa,
    //                 'nro_documento' => $element->nro_documento,
    //                 'razon_social' => $element->razon_social,
    //                 'id_moneda' => $element->id_moneda,
    //                 'tipo_cambio_compra' => $element->tipo_cambio_compra,
    //                 'moneda_simbolo' => $element->moneda_simbolo,
    //                 'incluye_igv' => $element->incluye_igv,
    //                 'leadtime' => $fechaLlegada->toDateString(),
    //                 'dias_restantes' => $fechaLlegada->diffInDays($fechaHoy->toDateString()),
    //                 'monto_igv' => $element->monto_igv,
    //                 'monto_total' => $element->monto_total,
    //                 'id_condicion' => $element->id_condicion,
    //                 'condicion' => $element->condicion,
    //                 'plazo_entrega' => $element->plazo_entrega,
    //                 'id_proveedor' => $element->id_proveedor,
    //                 'codigo_cuadro_comparativo' => '',
    //                 'estado' => $element->estado,
    //                 'estado_doc' => $element->estado_doc,
    //                 'detalle_pago' => $element->detalle_pago,
    //                 'archivo_adjunto' => $element->archivo_adjunto,
    //                 'monto_total_presup' => $element->monto_total_presup,
    //                 'monto_total_orden' => $element->monto_total_orden,
    //                 //  'facturas' => (new OrdenController)->reporteListaOrdenes($element->id_orden_compra),
    //                 'facturas' => [],
    //                 'requerimientos' => $element->requerimientos,
    //                 'estado_pago' => $element->estado_pago,
    //                 'id_prioridad_pago' => $element->id_prioridad_pago, //util para envia a pago,
    //                 'id_tipo_destinatario_pago' => $element->id_tipo_destinatario_pago, //util para envia a pago,
    //                 'id_cta_principal' => $element->id_cta_principal, //util para envia a pago,  es del mismo proveedor, reutilizando  (nuevacuentaBancariaDestinatario.js,  nuevoDestiantario.js)
    //                 'id_contribuyente' => $element->id_contribuyente, //util para envia a pago,  es del mismo proveedor, reutilizando  (nuevacuentaBancariaDestinatario.js,  nuevoDestiantario.js)
    //                 'nro_cuenta_prin' => $element->nro_cuenta_prin,
    //                 'nro_cuenta_alter' => $element->nro_cuenta_alter,
    //                 'nro_cuenta_detra' => $element->nro_cuenta_detra,
    //                 'id_persona_pago' => $element->id_persona_pago, //util para envia a pago,
    //                 'id_cuenta_persona_pago' => $element->id_cuenta_persona_pago, //util para envia a pago,
    //                 'comentario_pago' => $element->comentario_pago, //util para envia a pago,
    //                 'cantidad_ingresos_almacen' => $element->cantidad_ingresos_almacen

    //             ];
    //         }
    //     }

    //     $detalle_orden = Orden::select(
    //         'log_ord_compra.id_orden_compra',
    //         'log_det_ord_compra.id_detalle_orden',
    //         'alm_req.id_requerimiento',
    //         'alm_req.codigo as codigo_requerimiento',
    //         'alm_req.fecha_registro as fecha_registro_requerimiento',
    //         'cc_view.codigo_oportunidad',
    //         'oc_propias_view.fecha_entrega',
    //         'guia_com_det.fecha_registro as fecha_ingreso_almacen',
    //         'oc_propias_view.fecha_estado',
    //         'oc_propias_view.estado_aprobacion_cuadro'
    //     )
    //         ->leftJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_orden_compra', '=', 'log_ord_compra.id_orden_compra')
    //         ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
    //         ->leftJoin('almacen.guia_com_det', 'guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden')
    //         ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
    //         ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
    //         ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
    //         ->leftJoin('mgcp_cuadro_costos.cc_view', 'cc_view.id_oportunidad', '=', 'cc.id_oportunidad')
    //         ->where([
    //             ['log_ord_compra.estado', '!=', 7],
    //             ['log_det_ord_compra.id_detalle_requerimiento', '>', 0]
    //         ])
    //         ->orderBy('log_ord_compra.fecha', 'desc')
    //         ->get();

    //     $data_detalle_orden = [];
    //     if (count($ord_compra) > 0) {
    //         foreach ($detalle_orden as $element) {

    //             $data_detalle_orden[] = [
    //                 'id_orden_compra' => $element->id_orden_compra,
    //                 'id_detalle_orden' => $element->id_detalle_orden,
    //                 'id_requerimiento' => $element->id_requerimiento,
    //                 'codigo_requerimiento' => $element->codigo_requerimiento,
    //                 'codigo_oportunidad' => $element->codigo_oportunidad,
    //                 'fecha_entrega' => $element->fecha_entrega,
    //                 'fecha_ingreso_almacen' => $element->fecha_ingreso_almacen,
    //                 'estado_aprobacion' => $element->estado_aprobacion_cuadro,
    //                 'fecha_estado' => $element->fecha_estado,
    //                 'fecha_registro_requerimiento' => $element->fecha_registro_requerimiento
    //             ];
    //         }
    //     }


    //     // Debugbar::info($data_detalle_orden);

    //     foreach ($data_orden as $ordenKey => $ordenValue) {
    //         foreach ($data_detalle_orden as $detalleOrdnKey => $detalleOrdenValue) {
    //             if ($ordenValue['id_orden_compra'] == $detalleOrdenValue['id_orden_compra']) {
    //                 // if(in_array($detalleOrdenValue['codigo_requerimiento'],$data_orden[$ordenKey]['codigo_requerimiento'])==false){
    //                 // $data_orden[$ordenKey]['requerimientos'][]=['id_requerimiento'=>$detalleOrdenValue['id_requerimiento'], 'codigo'=> $detalleOrdenValue['codigo_requerimiento']];
    //                 $data_orden[$ordenKey]['codigo_oportunidad'] = $detalleOrdenValue['codigo_oportunidad'];
    //                 $data_orden[$ordenKey]['fecha_vencimiento_ocam'] = $detalleOrdenValue['fecha_entrega'];
    //                 $data_orden[$ordenKey]['fecha_ingreso_almacen'] = $detalleOrdenValue['fecha_ingreso_almacen'];
    //                 $data_orden[$ordenKey]['estado_aprobacion_cc'] = $detalleOrdenValue['estado_aprobacion'];
    //                 $data_orden[$ordenKey]['fecha_estado'] = $detalleOrdenValue['fecha_estado'];
    //                 $data_orden[$ordenKey]['fecha_registro_requerimiento'] = $detalleOrdenValue['fecha_registro_requerimiento'];
    //                 // }
    //             }
    //         }
    //     }

    //     $output['data'] = $data_orden;
    //     return $output;
    // }

    public function detalleOrden($idOrden)
    {
        $detalle = OrdenCompraDetalle::with(['reserva' => function ($q) {
            $q->where('alm_reserva.estado', '=', 1);
        }])->select(
            'log_ord_compra.id_orden_compra',
            'log_ord_compra.codigo as codigo_orden',
            'log_ord_compra.id_moneda',
            'log_ord_compra.monto_total',
            DB::raw("log_ord_compra.monto_total::numeric  - (select sum(registro_pago.total_pago)  from tesoreria.registro_pago  where registro_pago.id_oc = log_ord_compra.id_orden_compra  and registro_pago.estado !=7)::numeric as saldo"),
            'log_ord_compra.tipo_impuesto',
            'log_det_ord_compra.*',
            'alm_prod.codigo',
            'sis_moneda.simbolo as moneda_simbolo',
            'sis_moneda.descripcion as moneda_descripcion',
            'alm_prod.part_number',
            'alm_cat_prod.descripcion as categoria',
            'alm_subcat.descripcion as subcategoria',
            'alm_req.id_requerimiento',
            'alm_prod.descripcion',
            'alm_und_medida.abreviatura',
            'alm_req.codigo as codigo_req',
            'adm_estado_doc.estado_doc',
            'adm_estado_doc.bootstrap_color',
            'sis_sede.descripcion as sede_req',
            'oc_propias_view.nro_orden',
            'oc_propias_view.codigo_oportunidad',
            'oportunidades.oportunidad',
            'oc_propias_view.descripcion_larga_am',
            'oc_propias_view.nombre_entidad',
            'oc_propias_view.id as id_oc_propia',
            'oc_propias_view.tipo as tipo_oc_propia',
            'oc_propias_view.moneda_oc',
            'oc_propias_view.nombre_corto_responsable'
        )
            ->leftjoin('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'log_det_ord_compra.id_producto')
            ->leftjoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->leftjoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_det_ord_compra.id_unidad_medida')
            ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'log_det_ord_compra.estado')
            ->leftjoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftjoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'oc_propias_view.id_oportunidad')



            ->where([
                ['log_det_ord_compra.id_orden_compra', '=', $idOrden]
            ])
            ->get();
        return response()->json($detalle);
    }

    public function listaHistorialOrdenes()
    {
        $usuarioEnSesion = Auth::user()->id_usuario;
        $ordenes = Orden::select(
            'log_ord_compra.*',
            'sis_sede.descripcion as descripcion_sede_empresa',
            DB::raw("CONCAT(dis_destino.descripcion,' - ',prov_destino.descripcion, ' - ', dpto_destino.descripcion)  AS ubigeo_destino"),
            DB::raw("(CASE
            WHEN log_ord_compra.id_condicion = 1 THEN log_cdn_pago.descripcion
            WHEN log_ord_compra.id_condicion = 2 THEN log_cdn_pago.descripcion || ' ' || log_ord_compra.plazo_dias  || ' Días'
            ELSE null END) AS condicion
            "),
            'sis_moneda.simbolo as moneda_simbolo',
            'sis_moneda.descripcion as moneda_descripcion',
            'adm_contri.id_contribuyente',
            'adm_contri.razon_social',
            'adm_contri.nro_documento',
            'estados_compra.descripcion as estado_doc',
            'log_ord_compra_pago.id_pago',
            'log_ord_compra_pago.detalle_pago',
            'log_ord_compra_pago.archivo_adjunto'
        )
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->leftjoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_ord_compra.id_condicion')
            ->leftjoin('logistica.estados_compra', 'estados_compra.id_estado', '=', 'log_ord_compra.estado')

            ->leftjoin('logistica.log_ord_compra_pago', 'log_ord_compra_pago.id_orden_compra', '=', 'log_ord_compra.id_orden_compra')
            ->leftJoin('configuracion.ubi_dis as dis_destino', 'log_ord_compra.ubigeo_destino', '=', 'dis_destino.id_dis')
            ->leftJoin('configuracion.ubi_prov as prov_destino', 'dis_destino.id_prov', '=', 'prov_destino.id_prov')
            ->leftJoin('configuracion.ubi_dpto as dpto_destino', 'prov_destino.id_dpto', '=', 'dpto_destino.id_dpto')

            ->where(function ($query) use ($usuarioEnSesion) {
                $query->where(function ($query) use ($usuarioEnSesion) {
                    if (!in_array($usuarioEnSesion, [17, 27])) { // solo para usuario raulscodes,rhuancac se mostrarara OD
                        $query->where('log_ord_compra.id_tp_documento', '!=', 13);
                    }
                });
            });

        return datatables($ordenes)
            ->filterColumn('log_ord_compra.fecha', function ($query, $keyword) {
                try {
                    $desde = Carbon::createFromFormat('d-m-Y', trim($keyword))->hour(0)->minute(0)->second(0);
                    $hasta = Carbon::createFromFormat('d-m-Y', trim($keyword));
                    $query->whereBetween('log_ord_compra.fecha', [$desde, $hasta->addDay()->addSeconds(-1)]);
                } catch (\Throwable $th) {
                }
            })
            ->filterColumn('log_ord_compra.fecha_formato', function ($query, $keyword) {
                try {
                    $desde = Carbon::createFromFormat('d-m-Y', trim($keyword))->hour(0)->minute(0)->second(0);
                    $hasta = Carbon::createFromFormat('d-m-Y', trim($keyword));
                    $query->whereBetween('log_ord_compra.fecha', [$desde, $hasta->addDay()->addSeconds(-1)]);
                } catch (\Throwable $th) {
                }
            })
            ->filterColumn('moneda_descripcion', function ($query, $keyword) {
                $keywords = trim(strtoupper($keyword));
                $query->whereRaw("sis_moneda.descripcion LIKE ?", ["%{$keywords}%"]);
            })
            ->filterColumn('estado_doc', function ($query, $keyword) {
                $keywords = trim(strtoupper($keyword));
                $query->whereRaw("estados_compra.descripcion LIKE ?", ["%{$keywords}%"]);
            })
            ->toJson();
    }
    public function mostrarOrden($id_orden)
    {
        $orden = Orden::select(
            'log_ord_compra.id_orden_compra',
            'log_ord_compra.id_tp_documento',
            'log_ord_compra.codigo as codigo_orden',
            'log_ord_compra.id_moneda',
            'log_ord_compra.incluye_igv',
            'log_ord_compra.incluye_icbper',
            'log_ord_compra.monto_subtotal',
            'log_ord_compra.monto_igv',
            'log_ord_compra.monto_total',
            'sis_moneda.simbolo as moneda_simbolo',
            'sis_moneda.descripcion as moneda_descripcion',
            'log_ord_compra.codigo_softlink',
            'log_ord_compra.id_periodo',
            'log_ord_compra.fecha',
            'log_ord_compra.id_sede',
            'adm_empresa.id_empresa',
            'adm_empresa.logo_empresa',
            'sis_sede.codigo as codigo_sede_empresa',
            'log_ord_compra.id_proveedor',
            'adm_contri.id_contribuyente',
            'adm_contri.razon_social',
            'adm_contri.nro_documento',
            'adm_contri.direccion_fiscal',
            'adm_contri.ubigeo',
            'adm_contri.id_rubro',
            'adm_rubro.descripcion as rubro_descripcion',
            'log_ord_compra.id_cta_principal',
            'adm_cta_contri.nro_cuenta',
            'adm_cta_contri.id_moneda as id_moneda_cuenta',
            DB::raw("(dis_proveedor.descripcion) || ' - ' || (prov_proveedor.descripcion) || ' - ' || (dpto_proveedor.descripcion)  AS ubigeo_proveedor"),
            'log_ord_compra.id_contacto',
            'adm_ctb_contac.nombre as nombre_contacto',
            'adm_ctb_contac.nombre as telefono_contacto',
            'log_ord_compra.id_condicion_softlink',
            'log_ord_compra.id_condicion',
            DB::raw("(CASE
            WHEN log_ord_compra.id_condicion = 1 THEN log_cdn_pago.descripcion
            WHEN log_ord_compra.id_condicion = 2 THEN log_cdn_pago.descripcion || ' ' || log_ord_compra.plazo_dias  || ' Días'
            ELSE null END) AS condicion
            "),
            'log_ord_compra.plazo_dias',
            'log_ord_compra.plazo_entrega',
            'log_ord_compra.id_tp_doc',

            'log_ord_compra.direccion_destino',
            'log_ord_compra.ubigeo_destino as ubigeo_destino_id',
            DB::raw("(dis_destino.descripcion) || ' - ' || (prov_destino.descripcion) || ' - ' || (dpto_destino.descripcion)  AS ubigeo_destino"),
            'log_ord_compra.personal_autorizado_1',
            DB::raw("concat(pers_aut_1.nombres,' ',pers_aut_1.apellido_paterno,' ',pers_aut_1.apellido_materno) AS nombre_personal_autorizado_1"),
            'identi_aut_1.descripcion as documento_idendidad_personal_autorizado_1',
            'pers_aut_1.nro_documento as nro_documento_personal_autorizado_1',
            'log_ord_compra.personal_autorizado_2',
            DB::raw("concat(pers_aut_2.nombres,' ',pers_aut_2.apellido_paterno,' ',pers_aut_2.apellido_materno) AS nombre_personal_autorizado_2"),
            'identi_aut_2.descripcion as documento_idendidad_personal_autorizado_2',
            'pers_aut_2.nro_documento as nro_documento_personal_autorizado_2',

            'log_ord_compra.estado',
            'log_ord_compra.observacion',
            'log_ord_compra.compra_local',
            'adm_estado_doc.estado_doc',
            'adm_estado_doc.bootstrap_color'
        )
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
            ->leftJoin('administracion.adm_empresa', 'sis_sede.id_empresa', '=', 'adm_empresa.id_empresa')
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftJoin('contabilidad.adm_rubro', 'adm_rubro.id_rubro', '=', 'adm_contri.id_rubro')
            ->leftJoin('configuracion.ubi_dis as dis_proveedor', 'adm_contri.ubigeo', '=', 'dis_proveedor.id_dis')
            ->leftJoin('configuracion.ubi_prov as prov_proveedor', 'dis_proveedor.id_prov', '=', 'prov_proveedor.id_prov')
            ->leftJoin('configuracion.ubi_dpto as dpto_proveedor', 'prov_proveedor.id_dpto', '=', 'dpto_proveedor.id_dpto')
            ->leftJoin('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_datos_contacto', '=', 'log_ord_compra.id_contacto')
            ->leftJoin('contabilidad.adm_cta_contri', 'adm_cta_contri.id_cuenta_contribuyente', '=', 'log_ord_compra.id_cta_principal')

            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->leftjoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_ord_compra.id_condicion')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'log_ord_compra.estado')
            ->leftjoin('logistica.log_ord_compra_pago', 'log_ord_compra_pago.id_orden_compra', '=', 'log_ord_compra.id_orden_compra')
            ->leftJoin('configuracion.ubi_dis as dis_destino', 'log_ord_compra.ubigeo_destino', '=', 'dis_destino.id_dis')
            ->leftJoin('configuracion.ubi_prov as prov_destino', 'dis_destino.id_prov', '=', 'prov_destino.id_prov')
            ->leftJoin('configuracion.ubi_dpto as dpto_destino', 'prov_destino.id_dpto', '=', 'dpto_destino.id_dpto')
            ->leftJoin('rrhh.rrhh_trab as trab_aut_1', 'trab_aut_1.id_trabajador', '=', 'log_ord_compra.personal_autorizado_1')
            ->leftJoin('rrhh.rrhh_postu as post_aut_1', 'post_aut_1.id_postulante', '=', 'trab_aut_1.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as pers_aut_1', 'pers_aut_1.id_persona', '=', 'post_aut_1.id_persona')
            ->leftJoin('contabilidad.sis_identi as identi_aut_1', 'identi_aut_1.id_doc_identidad', '=', 'pers_aut_1.id_documento_identidad')
            ->leftJoin('rrhh.rrhh_trab as trab_aut_2', 'trab_aut_2.id_trabajador', '=', 'log_ord_compra.personal_autorizado_2')
            ->leftJoin('rrhh.rrhh_postu as post_aut_2', 'post_aut_2.id_postulante', '=', 'trab_aut_2.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as pers_aut_2', 'pers_aut_2.id_persona', '=', 'post_aut_2.id_persona')
            ->leftJoin('contabilidad.sis_identi as identi_aut_2', 'identi_aut_2.id_doc_identidad', '=', 'pers_aut_2.id_documento_identidad')
            ->with(['detalle.detalleRequerimiento.reserva', 'detalle.guia_compra_detalle' => function ($q) {
                $q->where('guia_com_det.estado', '!=', 7);
            }, 'detalle.producto.unidadMedida', 'detalle.unidad_medida', 'detalle.estado_orden'])
            // ->where([
            //     ['log_ord_compra.id_orden_compra', '=', $id_orden]
            // ])
            ->where('log_ord_compra.id_orden_compra', $id_orden)->first();

        return response()->json($orden);
    }

    public function groupIncluded($id_orden)
    {
        $sql = DB::table('logistica.log_ord_compra')
            ->select(
                'log_ord_compra.codigo as codigo_orden',
                'log_ord_compra.id_orden_compra',
                'log_ord_compra.estado as estado_orden',
                'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
                'valoriza_coti_detalle.id_detalle_requerimiento',
                'alm_det_req.id_requerimiento',
                'alm_req.codigo as codigo_requerimiento',
                'alm_req.id_grupo',
                'adm_grupo.descripcion as descripcion_grupo',
                'alm_req.id_area',
                'adm_area.descripcion as descripcion_area'
            )
            ->leftJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_orden_compra', '=', 'log_ord_compra.id_orden_compra')
            ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', 'log_det_ord_compra.id_valorizacion_cotizacion')
            ->leftJoin('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'valoriza_coti_detalle.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')

            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('administracion.adm_area', 'adm_area.id_area', '=', 'alm_req.id_area')
            ->where([
                ['log_ord_compra.id_orden_compra', '=', $id_orden],
                ['log_ord_compra.estado', '!=', 7]
            ])
            ->get();

        $output = [];
        $idGrupoList = [];
        foreach ($sql as $data) {
            if (in_array($data->id_grupo, $idGrupoList) == false) {
                array_push($idGrupoList, $data->id_grupo);
                $output[] = [
                    'id_orden' => $data->id_grupo,
                    'estado_orden' => $data->estado_orden,
                    'codigo_orden' => $data->codigo_orden,
                    'id_requerimiento' => $data->id_requerimiento,
                    'codigo_requerimiento' => $data->codigo_requerimiento,
                    'id_grupo' => $data->id_grupo,
                    'nombre_grupo' => $data->descripcion_grupo,
                    'id_area' => $data->id_area,
                    'nombre_area' => $data->descripcion_area
                ];
            }
        }
        return $output;
    }

    public function get_orden_por_requerimiento($id_orden_compra)
    {

        $head_orden_compra = Orden::select(
            'log_ord_compra.id_orden_compra',
            'log_ord_compra.id_tp_documento',
            'adm_tp_docum.descripcion AS tipo_documento',
            'log_ord_compra.fecha',
            'log_ord_compra.id_usuario',
            DB::raw("concat(pers.nombres,' ',pers.apellido_paterno,' ',pers.apellido_materno) as nombre_usuario"),
            'log_ord_compra.id_moneda',
            'sis_moneda.simbolo as moneda_simbolo',
            'log_ord_compra.incluye_igv',
            'log_ord_compra.incluye_icbper',
            'log_ord_compra.igv_porcentaje',
            'log_ord_compra.monto_subtotal',
            'log_ord_compra.monto_igv',
            'log_ord_compra.monto_total',
            'log_ord_compra.estado',
            'log_ord_compra.id_proveedor',
            'adm_contri.razon_social AS razon_social_proveedor',
            'adm_contri.nro_documento AS nro_documento_proveedor',
            'sis_identi.descripcion AS tipo_doc_proveedor',
            'adm_contri.telefono AS telefono_proveedor',
            'adm_contri.direccion_fiscal AS direccion_fiscal_proveedor',
            'adm_contri.email AS email_proveedor',
            DB::raw("(dis_prov.descripcion) || ' - ' || (prov_prov.descripcion) || ' - ' || (dpto_prov.descripcion)  AS ubigeo_proveedor"),
            'log_ord_compra.codigo',
            'log_ord_compra.id_condicion_softlink',
            'log_ord_compra.id_condicion',
            'log_cdn_pago.descripcion AS condicion_pago',
            'log_ord_compra.codigo_softlink',
            'log_ord_compra.plazo_dias',
            'log_ord_compra.id_cta_principal',
            'cta_prin.nro_cuenta as nro_cuenta_principal',
            'log_ord_compra.id_cta_alternativa',
            'cta_alter.nro_cuenta as nro_cuenta_alternativa',
            'log_ord_compra.id_cta_detraccion',
            'cta_detra.nro_cuenta as nro_cuenta_detraccion',
            'log_ord_compra.plazo_entrega',
            'log_ord_compra.observacion',
            'log_ord_compra.compra_local',
            'log_ord_compra.en_almacen',
            'log_ord_compra.id_occ',
            'log_ord_compra.id_sede',
            'log_ord_compra.direccion_destino',
            'log_ord_compra.ubigeo_destino',
            DB::raw("(dis_destino.descripcion) || ' - ' || (prov_destino.descripcion) || ' - ' || (dpto_destino.descripcion)  AS ubigeo_destino"),
            'log_ord_compra.personal_autorizado_1',
            DB::raw("concat(pers_aut_1.nombres,' ',pers_aut_1.apellido_paterno,' ',pers_aut_1.apellido_materno) AS nombre_personal_autorizado_1"),
            'identi_aut_1.descripcion as documento_idendidad_personal_autorizado_1',
            'pers_aut_1.nro_documento as nro_documento_personal_autorizado_1',
            'log_ord_compra.personal_autorizado_2',
            DB::raw("concat(pers_aut_2.nombres,' ',pers_aut_2.apellido_paterno,' ',pers_aut_2.apellido_materno) AS nombre_personal_autorizado_2"),
            'identi_aut_2.descripcion as documento_idendidad_personal_autorizado_2',
            'pers_aut_2.nro_documento as nro_documento_personal_autorizado_2',
            'contrib.razon_social as razon_social_empresa',
            'contrib.direccion_fiscal as direccion_fiscal_empresa',
            DB::raw("(dis_empresa.descripcion) || ' - ' || (prov_empresa.descripcion) || ' - ' || (dpto_empresa.descripcion)  AS ubigeo_empresa"),
            'sis_sede.codigo as codigo_sede_empresa',
            'sis_sede.id_empresa',
            'contab_sis_identi.descripcion AS tipo_doc_empresa',
            'contab_contri.nro_documento AS nro_documento_empresa',
            'sis_sede.direccion AS direccion_fiscal_empresa_sede',
            'contab_contri.telefono AS telefono_empresa',
            'contab_contri.email AS email_empresa',
            'adm_empresa.logo_empresa',
            // 'log_ord_compra.id_requerimiento',
            // 'alm_req.codigo as codigo_requerimiento',
            'log_ord_compra.fecha AS fecha_orden',
            'sis_moneda.descripcion as moneda_descripcion',
            'log_ord_compra.id_contacto',
            'log_ord_compra.sustento_anulacion',
            'log_ord_compra.compra_local',
            'adm_ctb_contac.nombre as nombre_contacto',
            'adm_ctb_contac.telefono as telefono_contacto',
            'adm_ctb_contac.email as email_contacto',
            'adm_ctb_contac.cargo as cargo_contacto',
            'adm_ctb_contac.direccion as direccion_contacto',
            'adm_ctb_contac.horario as horario_contacto',
            DB::raw("(dis_contac.descripcion) || ' ' || (prov_contac.descripcion) || ' ' || (dpto_contac.descripcion)  AS ubigeo_contacto")

        )
            ->leftJoin('administracion.adm_tp_docum', 'adm_tp_docum.id_tp_documento', '=', 'log_ord_compra.id_tp_documento')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'log_ord_compra.id_usuario')
            ->leftJoin('rrhh.rrhh_trab as trab', 'trab.id_trabajador', '=', 'sis_usua.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu as post', 'post.id_postulante', '=', 'trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as pers', 'pers.id_persona', '=', 'post.id_persona')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_ord_compra.id_condicion')
            // ->leftJoin('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_datos_contacto', '=', 'log_ord_compra.personal_responsable')
            ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            // ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'log_ord_compra.id_requerimiento')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->leftJoin('contabilidad.adm_contri as contab_contri', 'contab_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi as contab_sis_identi', 'contab_sis_identi.id_doc_identidad', '=', 'contab_contri.id_doc_identidad')
            ->leftJoin('contabilidad.adm_contri as contrib', 'adm_empresa.id_contribuyente', '=', 'contrib.id_contribuyente')
            ->leftjoin('contabilidad.adm_cta_contri as cta_prin', 'cta_prin.id_cuenta_contribuyente', '=', 'log_ord_compra.id_cta_principal')
            ->leftjoin('contabilidad.adm_cta_contri as cta_alter', 'cta_alter.id_cuenta_contribuyente', '=', 'log_ord_compra.id_cta_alternativa')
            ->leftjoin('contabilidad.adm_cta_contri as cta_detra', 'cta_detra.id_cuenta_contribuyente', '=', 'log_ord_compra.id_cta_detraccion')
            ->leftJoin('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_datos_contacto', '=', 'log_ord_compra.id_contacto')
            ->leftJoin('rrhh.rrhh_trab as trab_aut_1', 'trab_aut_1.id_trabajador', '=', 'log_ord_compra.personal_autorizado_1')
            ->leftJoin('rrhh.rrhh_postu as post_aut_1', 'post_aut_1.id_postulante', '=', 'trab_aut_1.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as pers_aut_1', 'pers_aut_1.id_persona', '=', 'post_aut_1.id_persona')
            ->leftJoin('contabilidad.sis_identi as identi_aut_1', 'identi_aut_1.id_doc_identidad', '=', 'pers_aut_1.id_documento_identidad')
            ->leftJoin('rrhh.rrhh_trab as trab_aut_2', 'trab_aut_2.id_trabajador', '=', 'log_ord_compra.personal_autorizado_2')
            ->leftJoin('rrhh.rrhh_postu as post_aut_2', 'post_aut_2.id_postulante', '=', 'trab_aut_2.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as pers_aut_2', 'pers_aut_2.id_persona', '=', 'post_aut_2.id_persona')
            ->leftJoin('contabilidad.sis_identi as identi_aut_2', 'identi_aut_2.id_doc_identidad', '=', 'pers_aut_2.id_documento_identidad')
            ->leftJoin('configuracion.ubi_dis as dis_prov', 'adm_contri.ubigeo', '=', 'dis_prov.id_dis')
            ->leftJoin('configuracion.ubi_prov as prov_prov', 'dis_prov.id_prov', '=', 'prov_prov.id_prov')
            ->leftJoin('configuracion.ubi_dpto as dpto_prov', 'prov_prov.id_dpto', '=', 'dpto_prov.id_dpto')
            ->leftJoin('configuracion.ubi_dis as dis_contac', 'adm_ctb_contac.ubigeo', '=', 'dis_contac.id_dis')
            ->leftJoin('configuracion.ubi_prov as prov_contac', 'dis_contac.id_prov', '=', 'prov_contac.id_prov')
            ->leftJoin('configuracion.ubi_dpto as dpto_contac', 'prov_contac.id_dpto', '=', 'dpto_contac.id_dpto')
            ->leftJoin('configuracion.ubi_dis as dis_empresa', 'contab_contri.ubigeo', '=', 'dis_empresa.id_dis')
            ->leftJoin('configuracion.ubi_prov as prov_empresa', 'dis_empresa.id_prov', '=', 'prov_empresa.id_prov')
            ->leftJoin('configuracion.ubi_dpto as dpto_empresa', 'prov_empresa.id_dpto', '=', 'dpto_empresa.id_dpto')
            ->leftJoin('configuracion.ubi_dis as dis_destino', 'log_ord_compra.ubigeo_destino', '=', 'dis_destino.id_dis')
            ->leftJoin('configuracion.ubi_prov as prov_destino', 'dis_destino.id_prov', '=', 'prov_destino.id_prov')
            ->leftJoin('configuracion.ubi_dpto as dpto_destino', 'prov_destino.id_dpto', '=', 'dpto_destino.id_dpto')

            ->where([
                ['log_ord_compra.id_orden_compra', '=', $id_orden_compra]
                // ['log_ord_compra.estado', '!=', 7]
            ])
            ->get();
        // return $head_orden_compra;

        $detalle_orden_compra = DB::table('logistica.log_det_ord_compra')
            ->select(
                'log_det_ord_compra.id_detalle_orden',
                'log_det_ord_compra.id_orden_compra',
                'log_det_ord_compra.id_item',
                'alm_item.codigo AS codigo_item',
                'alm_prod.descripcion AS descripcion_producto',
                'alm_prod.codigo AS codigo_producto',
                'alm_prod.part_number',
                'log_det_ord_compra.garantia',
                'log_det_ord_compra.estado',
                'log_det_ord_compra.personal_autorizado',
                'und_prod.descripcion AS unidad_medida_producto',

                // DB::raw("(pers_aut.nombres) || ' ' || (pers_aut.apellido_paterno) || ' ' || (pers_aut.apellido_materno) AS nombre_personal_autorizado"),
                'log_det_ord_compra.id_producto',
                'log_det_ord_compra.lugar_despacho',
                'log_det_ord_compra.descripcion_adicional',
                'log_det_ord_compra.descripcion_complementaria',
                'log_det_ord_compra.cantidad',
                'log_det_ord_compra.precio',
                'log_det_ord_compra.id_unidad_medida',
                'alm_und_medida.descripcion AS unidad_medida',
                'log_det_ord_compra.subtotal',
                'log_det_ord_compra.id_detalle_requerimiento'
            )
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_det_ord_compra.id_unidad_medida')
            ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'log_det_ord_compra.id_producto')
            ->leftJoin('almacen.alm_und_medida as und_prod', 'und_prod.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftJoin('configuracion.sis_usua as sis_usua_aut', 'sis_usua_aut.id_usuario', '=', 'log_det_ord_compra.personal_autorizado')
            ->leftJoin('rrhh.rrhh_trab as trab_aut', 'trab_aut.id_trabajador', '=', 'sis_usua_aut.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu as post_aut', 'post_aut.id_postulante', '=', 'trab_aut.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as pers_aut', 'pers_aut.id_persona', '=', 'post_aut.id_persona')
            ->where([
                ['log_det_ord_compra.id_orden_compra', '=', $id_orden_compra],
                ['log_det_ord_compra.estado', '!=', 7]
            ])
            ->orderby('log_det_ord_compra.id_detalle_orden', 'asc')

            ->get();


        $head = [];
        $detalle = [];

        if (count($head_orden_compra) > 0) {
            foreach ($head_orden_compra as $data) {
                $head = [
                    'id_orden_compra' => $data->id_orden_compra,
                    'logo_empresa' => $data->logo_empresa,
                    'codigo' => $data->codigo,
                    'fecha_orden' => $data->fecha_orden,
                    'id_tp_documento' => $data->id_tp_documento,
                    'tipo_documento' => $data->tipo_documento,
                    // 'codigo_requerimiento' => $data->codigo_requerimiento,
                    'fecha_registro' => $data->fecha,
                    'moneda_simbolo' => $data->moneda_simbolo,
                    'incluye_igv' => $data->incluye_igv,
                    'incluye_icbper' => $data->incluye_icbper,
                    'observacion' => $data->observacion,
                    'compra_local' => $data->compra_local,
                    'sustento_anulacion' => $data->sustento_anulacion,
                    'estado' => $data->estado,
                    // 'monto_igv' => $data->monto_igv,
                    // 'monto_total' => $data->monto_total,
                    // 'moneda_descripcion' => $data->moneda_descripcion
                    // 'nombre_usuario' => $data->nombre_usuario,
                    'proveedor' => [
                        'id_proveedor' => $data->id_proveedor,
                        'razon_social_proveedor' => $data->razon_social_proveedor,
                        'tipo_doc_proveedor' => $data->tipo_doc_proveedor,
                        'nro_documento_proveedor' => $data->nro_documento_proveedor,
                        'telefono_proveedor' => $data->telefono_proveedor,
                        'direccion_fiscal_proveedor' => $data->direccion_fiscal_proveedor,
                        'ubigeo_proveedor' => $data->ubigeo_proveedor,

                        'contacto' => [
                            'nombre_contacto' => $data->nombre_contacto,
                            'telefono_contacto' => $data->telefono_contacto,
                            'email_contacto' => $data->email_contacto,
                            'cargo_contacto' => $data->cargo_contacto,
                            'direccion_contacto' => $data->direccion_contacto,
                            'horario_contacto' => $data->horario_contacto,
                            'ubigeo_contacto' => $data->ubigeo_contacto
                        ],
                    ],

                    'condicion_compra' => [
                        'id_condicion_softlink' => $data->id_condicion_softlink,
                        'id_condicion' => $data->id_condicion,
                        'condicion_pago' => $data->condicion_pago,
                        'plazo_dias' => $data->plazo_dias,
                        'plazo_entrega' => $data->plazo_entrega,
                        'codigo_softlink' => $data->codigo_softlink


                    ],
                    'datos_para_despacho' => [
                        'id_empresa' => $data->id_empresa,
                        'sede' => $data->codigo_sede_empresa,
                        'razon_social_empresa' => $data->razon_social_empresa,
                        'tipo_doc_empresa' => $data->tipo_doc_empresa,
                        'nro_documento_empresa' => $data->nro_documento_empresa,
                        'direccion_sede' => $data->direccion_fiscal_empresa_sede,
                        'direccion_destino' => $data->direccion_destino,
                        'ubigeo_destino' => $data->ubigeo_destino,
                        'personal_autorizado_1' => $data->personal_autorizado_1,
                        'documento_idendidad_personal_autorizado_1' => $data->documento_idendidad_personal_autorizado_1,
                        'nro_documento_personal_autorizado_1' => $data->nro_documento_personal_autorizado_1,
                        'nombre_personal_autorizado_1' => $data->nombre_personal_autorizado_1,
                        'personal_autorizado_2' => $data->personal_autorizado_2,
                        'documento_idendidad_personal_autorizado_2' => $data->documento_idendidad_personal_autorizado_2,
                        'nro_documento_personal_autorizado_2' => $data->nro_documento_personal_autorizado_2,
                        'nombre_personal_autorizado_2' => $data->nombre_personal_autorizado_2
                    ],
                    'facturar_a_nombre' => [
                        'id_empresa' => $data->id_empresa,
                        'razon_social_empresa' => $data->razon_social_empresa,
                        'tipo_doc_empresa' => $data->tipo_doc_empresa,
                        'nro_documento_empresa' => $data->nro_documento_empresa,
                        'direccion_fiscal_empresa' => $data->direccion_fiscal_empresa,
                        'ubigeo_empresa' => $data->ubigeo_empresa,
                        // 'direccion_sede' => $data->direccion_fiscal_empresa_sede,
                        // 'telefono_empresa' => $data->telefono_empresa,
                        'email_empresa' => $data->email_empresa,
                        'sede' => $data->codigo_sede_empresa
                    ],
                    'nombre_usuario' => $data->nombre_usuario
                ];
            }
        }

        $idDetalleReqList = [];
        if (count($detalle_orden_compra) > 0) {
            foreach ($detalle_orden_compra as $data) {
                $detalle[] = [
                    'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                    'id_item' => $data->id_item,
                    'codigo_item' => $data->codigo_item,
                    'id_producto' => $data->id_producto,
                    'codigo_producto' => $data->codigo_producto,
                    'descripcion_producto' => $data->descripcion_producto,
                    'part_number' => $data->part_number,
                    'descripcion_adicional' => $data->descripcion_adicional,
                    'descripcion_complementaria' => $data->descripcion_complementaria,
                    'cantidad' => $data->cantidad,
                    'id_unidad_medida' => $data->id_unidad_medida,
                    'unidad_medida_producto' => $data->unidad_medida_producto,
                    'unidad_medida' => $data->unidad_medida,
                    'precio' => $data->precio,
                    // 'flete' => $data->flete,
                    // 'porcentaje_descuento' => $data->porcentaje_descuento,
                    // 'monto_descuento' => $data->monto_descuento,
                    'subtotal' => $data->subtotal,
                    // 'plazo_entrega' => $data->plazo_entrega,
                    // 'incluye_igv' => $data->incluye_igv,
                    'garantia' => $data->garantia,
                    'lugar_despacho' => $data->lugar_despacho
                    // 'nombre_personal_autorizado' => $data->nombre_personal_autorizado
                ];
                $idDetalleReqList[] = $data->id_detalle_requerimiento;
            }
        }

        $codigoReqList = [];
        $idCcList = [];
        if (count($idDetalleReqList) > 0) {
            $req = DB::table('almacen.alm_det_req')
                ->select(
                    'alm_req.id_requerimiento',
                    'alm_req.codigo',
                    'alm_req.concepto',
                    'alm_req.id_cc'
                )
                ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
                ->whereIn('alm_det_req.id_detalle_requerimiento', $idDetalleReqList)
                ->distinct()
                ->get();

            if (count($req) > 0) {
                foreach ($req as $data) {
                    $codigoReqList[] = $data->codigo;
                    $idCcList[] = $data->id_cc;
                }
            }

            $cdc = DB::table('mgcp_cuadro_costos.cc')
                ->select(
                    'oportunidades.codigo_oportunidad',
                    'users.name as nombre_responsable'
                )
                ->join('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
                ->LeftJoin('mgcp_cuadro_costos.responsables', 'responsables.id', '=', 'oportunidades.id_responsable')
                ->LeftJoin('mgcp_usuarios.users', 'users.id', '=', 'oportunidades.id_responsable')
                ->whereIn('cc.id', $idCcList)
                ->distinct()
                ->get();

            $codigo_oportunidad = count($cdc) > 0 ? $cdc->first()->codigo_oportunidad : '';
            $nombre_responsable = count($cdc) > 0 ? $cdc->first()->nombre_responsable : '';
            // dd( $cdc );

        }





        $codigoReqText = implode(",", $codigoReqList);

        $result = [
            'head' => $head,
            'detalle' => $detalle
        ];

        $result['head']['codigo_requerimiento'] = $codigoReqText;
        $result['head']['codigo_cc'] = isset($codigo_oportunidad) ? $codigo_oportunidad : '';
        $result['head']['nombre_responsable_cc'] = isset($nombre_responsable) ? $nombre_responsable : '';


        return $result;
    }

    public function imprimir_orden_por_requerimiento_pdf($id_orden_compra)
    {
        $ordenArray = $this->get_orden_por_requerimiento($id_orden_compra);
        // return dd($ordenArray);
        $sizeOrdenHeader = count($ordenArray['head']);

        if ($sizeOrdenHeader == 0) {
            $html = 'Error en documento';
            return $html;
        }

        $now = new \DateTime();

        $logoEmpresa = '';
        if (isset($ordenArray['head']['logo_empresa']) && ($ordenArray['head']['logo_empresa'] != null)) {
            $logoEmpresa = $ordenArray['head']['logo_empresa'] ? '<div><img src=".' . $ordenArray['head']['logo_empresa'] . '" alt="Logo" height="75px"></div>' : '<div></div>';
        }
        $EstadoSiOrdenAnulada = '<div style="position:absolute;" class="right">' . ((isset($ordenArray['head']['estado']) && $ordenArray['head']['estado'] == 7) ? '<h1 style=" color:red; ">ORDEN ANULADA </h2>' : '') . '</div>';
        $sustentoSiOrdenAnulada =  ((isset($ordenArray['head']['estado']) && $ordenArray['head']['estado'] == 7) ? '<div class="right" style="position:relative; color:red; font-size:1.5em;">Sustento de anulación: ' . ((isset($ordenArray['head']['sustento_anulacion']) && strlen($ordenArray['head']['sustento_anulacion']) > 0) ? $ordenArray['head']['sustento_anulacion'] : '(Sin sustento)') . ' </div>' : '');
        $html = '
        <html>
            <head>
            <style type="text/css">
                *{
                    box-sizing: border-box;
                }
                body{
                    background-color: #fff;
                    font-family: "DejaVu Sans";
                    font-size: 10px;
                    box-sizing: border-box;
                    padding:10px;
                }
                table{

                    width:95%;
                    height:auto;
                    border-collapse: collapse;
                }
                .tablePDF thead{
                    padding:4px;
                    background-color:#d04f46;
                    color:white;
                }
                .bgColorRed{

                }
                .tablePDF,
                .tablePDF tr td{
                    border: .5px solid #dbdbdb;
                }
                .tablePDF tr td{
                    padding: 5px;
                }
                h1{
                    text-transform: uppercase;
                }
                .subtitle{
                    font-weight: bold;
                }
                .bordebox{
                    border: 1px solid #000;
                }
                .verticalTop{
                    vertical-align:top;
                }
                .texttab {
                    display:block;
                    margin-left: 20px;
                    margin-bottom:5px;
                }
                .right{
                    text-align:right;
                }
                .left{
                    text-align:left;
                }
                .justify{
                    text-align: justify;
                }
                .top{
                    vertical-align:top;
                }
                hr{
                    color:#cc352a;
                }
                .tablePDF .noBorder{
                    border:none;
                    border-left:none;
                    border-right:none;
                    border-bottom:none;
                }
                .textBold{
                    font-weight:bold;
                }
                footer{
                    position:relative;
                }
                .pie_de_pagina{
                    position: absolute;
                    bottom:0px;
                    right:0px;
                    text-align:right;

                }

            </style>
            </head>
            <body>

        ';
        $html .= '<div style="display:flex; position:relative; justify-content: space-between;">' . $logoEmpresa . $EstadoSiOrdenAnulada . '</div>' . $sustentoSiOrdenAnulada;

        $html .= '
        </div>
                <br>
                <hr>
                <h1><center>' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'PURCHASE ORDER - IMPORT' : $ordenArray['head']['tipo_documento']) . '<br>' . $ordenArray['head']['codigo'] . '</center></h1>
                <table border="0" >
                    <tr>
                        <td nowrap  width="15%" class="subtitle verticalTop">Sr.(s):</td>
                        <td width="50%" class="verticalTop">' . $ordenArray['head']['proveedor']['nro_documento_proveedor'] . ' - ' . $ordenArray['head']['proveedor']['razon_social_proveedor'] . '</td>
                        <td nowrap  width="15%" class="subtitle verticalTop">' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Order date' : 'Fecha de emisión') . ':</td>
                        <td>' . substr($ordenArray['head']['fecha_orden'], 0, 11) . '</td>
                    </tr>
                    <tr>
                        <td nowrap  width="15%" class="subtitle"> ' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Address' : 'Dirección') . ':</td>
                        <td class="verticalTop">' . $ordenArray['head']['proveedor']['direccion_fiscal_proveedor'] . '<br>' . $ordenArray['head']['proveedor']['ubigeo_proveedor'] . '</td>
                        <td nowrap  width="15%" class="subtitle verticalTop"> ' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Phone number' : 'Teléfono') . ':</td>
                        <td class="verticalTop">' . $ordenArray['head']['proveedor']['telefono_proveedor'] . '</td>
                    </tr>
                    <tr>
                        <td nowrap  width="15%" class="subtitle"> ' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Sales person' : 'Contacto') . ':</td>
                        <td class="verticalTop">' . $ordenArray['head']['proveedor']['contacto']['nombre_contacto'] . ' - ' . $ordenArray['head']['proveedor']['contacto']['cargo_contacto'] . '</td>

                    </tr>

                </table>';

        $html .= '<p class="left" style="color:#d04f46">';

        // if($ordenArray['aprobaciones']['aprob_necesarias'] == $ordenArray['aprobaciones']['total_aprob']){
        //     $html.='<strong>ORDEN APROBADA </strong>';
        //     //   $apro=implode(',',array_values($ordenArray['aprobaciones']['aprobaciones']));
        //     //  $html.=$apro;

        //     foreach ($ordenArray['aprobaciones']['aprobaciones'] as $key => $data) {
        //         $apro[]=$data->nombre.' ['.$data->fecha_vobo.']';
        //     }
        //     $html.=implode(", ",$apro);

        // }else{
        //     $html.='<strong>ORDEN NO APROBADA </strong>';
        // }

        $html .= '</p>';


        $html .= '<br>

                <table class="tablePDF" style="border:0; font-size:8px;">
                <thead>
                    <tr class="subtitle">
                        <td style="width:5px; text-align:center;">' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Product code' : 'Código') . '</td>
                        <td style="width:5px; text-align:center;">Part number</td>
                        <td style="width:280px; text-align:center;">' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Product description' : 'Descripción') . '</td>
                        <td style="width:15px; text-align:center;">Und</td>
                        <td style="width:5px; text-align:center;">' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Quantity' : 'Cant') . '</td>
                        <td style="width:15px; text-align:center;">' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Unit price' : 'Precio') . '</td>
                        <td style="width:5px; text-align:center;">' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Discount' : 'Descuento') . '</td>
                        <td style="width:15px; text-align:center;">' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Total item' : 'Total') . '</td>
                    </tr>
                </thead>';

        // $total = 0;
        $monto_neto = 0;
        foreach ($ordenArray['detalle'] as $key => $data) {
            $monto_neto += $data['subtotal'];
        }
        $igv = 0;
        $icbper = 0;
        $monto_total = 0;

        if ($ordenArray['head']['incluye_icbper'] == true) {
            $icbper = 0.5;
        }

        foreach ($ordenArray['detalle'] as $key => $data) {

            if ($ordenArray['head']['incluye_igv'] == true) {
                $igv = (($monto_neto) * 0.18);
            } else {
                $igv = 0;
            }

            $monto_total = ($monto_neto + $igv+ $icbper);
            // $subtotal = $data['subtotal']>0?$data['subtotal']:number_format($data['cantidad'] * $data['precio'],2,'.','');

            $html .= '<tr style="text-align:left">';
            // $html .= '<td>' . ($key + 1) . '</td>';
            $html .= '<td>' . $data['codigo_producto'] . '</td>';
            $html .= '<td>' . $data['part_number'] . '</td>';
            if ($data['id_producto'] > 0 && strlen($data['descripcion_producto']) > 0) {

                $html .= '<td>' . ($data['descripcion_producto'] ? $data['descripcion_producto'] : $data['descripcion_adicional']) . ' ' . ($data['descripcion_complementaria'] ? $data['descripcion_complementaria'] : '') . '</td>';
            } else {
                $html .= '<td>' . ($data['descripcion_adicional'] ? $data['descripcion_adicional'] : $data['descripcion_adicional']) . ' ' . ($data['descripcion_complementaria'] ? $data['descripcion_complementaria'] : '') . '</td>';
            }
            $html .= '<td>' . ($data['unidad_medida_producto']==null || $data['unidad_medida_producto']==''?$data['unidad_medida']:$data['unidad_medida_producto']) . '</td>';
            $html .= '<td style="text-align:center">' . $data['cantidad'] . '</td>';
            $html .= '<td style="text-align:center">' . $ordenArray['head']['moneda_simbolo'] . number_format($data['precio'], 2) . '</td>';
            // $html .= '<td class="right">' . number_format((($data['cantidad'] * $data['precio']) - (($data['cantidad']* $data['precio'])/1.18)),2,'.','') . '</td>';
            $html .= '<td style="text-align:right"> </td>';
            $html .= '<td style="text-align:right">' . $ordenArray['head']['moneda_simbolo'] . number_format($data['subtotal'], 2) . '</td>';
            $html .= '</tr>';
            // $total = $total + ($data['cantidad'] * $data['precio']);
        }



        $html .= '
                <tr>
                    <td class="right noBorder textBold"  colspan="7"> ' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Total price before taxes ' : 'Monto neto ') . $ordenArray['head']['moneda_simbolo'] . '</td>
                    <td class="right  noBorder textBold">' . number_format($monto_neto, 2) . '</td>
                </tr>
                <tr>
                    <td class="right noBorder textBold"  colspan="7"> ' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Taxes ' : 'IGV ') . $ordenArray['head']['moneda_simbolo'] . '</td>
                    <td class="right noBorder textBold">' . number_format($igv, 2) . '</td>
                </tr>
                <tr>
                    <td class="right noBorder textBold"  colspan="7"> ' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Taxes ' : 'ICBPER ') . $ordenArray['head']['moneda_simbolo'] . '</td>
                    <td class="right noBorder textBold">' . number_format($icbper, 2) . '</td>
                </tr>
                <tr>
                    <td class="right noBorder textBold"  colspan="7"> ' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Total price after taxes ' : 'Monto total ') . $ordenArray['head']['moneda_simbolo'] . '</td>
                    <td class="right noBorder textBold">' . number_format($monto_total, 2) . '</td>
                </tr>
                </table>
                <br>
                <br>';


        $html .= '
                <table width="100%" border=0>
                <caption class="left subtitle" style="padding-bottom:10px; font-size:0.7rem">' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Puchase details' : 'Condición de compra') . ':</caption>

                <tr>
                <td nowrap  width="15%" class="subtitle">-' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Payment details' : 'Forma de pago') . ': </td>
                <td  class="verticalTop left">' . $ordenArray['head']['condicion_compra']['condicion_pago'] . ' ' . (($ordenArray['head']['condicion_compra']['id_condicion'] == 2) ? $ordenArray['head']['condicion_compra']['plazo_dias'] . ' días' : '') . '</td>';


        $html .= '
                    <td width="15%" class="verticalTop subtitle">-' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Delivery time' : 'Plazo de entrega') . ': </td>
                    <td class="verticalTop">' . $ordenArray['head']['condicion_compra']['plazo_entrega'] . ' Días</td>

                </tr>
                <tr>
                    <td width="15%" class="verticalTop subtitle">-CDP / Req.: </td>
                    <td class="verticalTop">' . ($ordenArray['head']['codigo_cc'] ? $ordenArray['head']['codigo_cc'] . '/' : '') . ($ordenArray['head']['codigo_requerimiento'] ? $ordenArray['head']['codigo_requerimiento'] : '') . '</td

                </tr>
                <tr>
                    <td width="15%" class="verticalTop subtitle">-Cod. Softlink: </td>
                    <td class="verticalTop">' .  ($ordenArray['head']['condicion_compra']['codigo_softlink'] ? $ordenArray['head']['condicion_compra']['codigo_softlink'] : '') . '</td

                </tr>
                </table>
                <br>
                ';

        $personal_autorizado_1 = $ordenArray['head']['datos_para_despacho']['personal_autorizado_1'] > 0 ? ($ordenArray['head']['datos_para_despacho']['nombre_personal_autorizado_1'] . ' (' . $ordenArray['head']['datos_para_despacho']['documento_idendidad_personal_autorizado_1'] . ': ' . $ordenArray['head']['datos_para_despacho']['nro_documento_personal_autorizado_1'] . ')') : '';
        $personal_autorizado_2 = $ordenArray['head']['datos_para_despacho']['personal_autorizado_2'] > 0 ? ($ordenArray['head']['datos_para_despacho']['nombre_personal_autorizado_2'] . ' (' . $ordenArray['head']['datos_para_despacho']['documento_idendidad_personal_autorizado_2'] . ': ' . $ordenArray['head']['datos_para_despacho']['nro_documento_personal_autorizado_2'] . ')') : '';
        $compra_local = ($ordenArray['head']['compra_local']) ? ' (COMPRA LOCAL)' : '';

        $html .= '
            <table width="100%" border=0>
                <caption class="left subtitle" style="padding-bottom:10px; font-size:0.7rem">' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Shipping details' : 'Datos para el despacho') . ':</caption>

                <tr>
                    <td nowrap width="15%" class="verticalTop subtitle">-' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Delivery address' : 'Dirección entrega') . ': </td>
                    <td class="verticalTop">' . $ordenArray['head']['datos_para_despacho']['direccion_destino'] . '<br>' . $ordenArray['head']['datos_para_despacho']['ubigeo_destino'] . '</td>
                    <td width="15%" class="verticalTop subtitle">-' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Authorized receiver' : 'Personal autorizado') . ':</td>
                    <td class="verticalTop">' . $personal_autorizado_1 . ($personal_autorizado_2 ? ("<br>" . $personal_autorizado_2) : "") . '</td>
                </tr>
                <tr>
                    <td nowrap width="15%" class="subtitle">-' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Comments' : 'Observación') . ':</td>
                    <td class="verticalTop">' . $ordenArray['head']['observacion'] . ' '. $compra_local . '</td>
                </tr>
            </table>
            <br>
        ';

        $html .= '
            <table width="100%" border=0>
                <caption class="left subtitle" style="padding-bottom:10px; font-size:0.7rem">' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Invoice details' : 'Facturar a nombre') . ':</caption>

                <tr>
                    <td nowrap  width="15%" class="verticalTop subtitle">-' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Company\'s name' : 'Razón social') . ': </td>
                    <td class="verticalTop">' . $ordenArray['head']['facturar_a_nombre']['razon_social_empresa'] . '</td>
                </tr>
                <tr>
                    <td nowrap  width="15%" class="verticalTop subtitle">-' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Tax ID' : 'RUC') . ': </td>
                    <td class="verticalTop">' . $ordenArray['head']['facturar_a_nombre']['nro_documento_empresa'] . '</td>
                </tr>
                <tr>
                    <td nowrap  width="15%" class="verticalTop subtitle">-' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Invoice address' : 'Dirección') . ': </td>
                    <td class="verticalTop">' . $ordenArray['head']['facturar_a_nombre']['direccion_fiscal_empresa'] . ', ' . $ordenArray['head']['facturar_a_nombre']['ubigeo_empresa'] . '</td>

                </tr>
            </table>
            <br>
        ';

        $html .= '<br>
        <table width="100%" border=0>
            <caption class="left subtitle" style="padding-bottom:10px; font-size:0.7rem">' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Documents for dispatch' : 'Documentos para despacho') . ':</caption>

            <tr>
                <td nowrap  width="15%" class="verticalTop">-' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Invoice referencing the purchase order' : 'Factura referenciando la orden de compra.') . ' </td>
            </tr>
            <tr>
                <td nowrap  width="15%" class="verticalTop">-' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Referral guide referencing the purchase order' : 'Guía de remisión referenciando la orden de compra') . ' </td>
            </tr>
            <tr>
                <td nowrap  width="15%" class="verticalTop">-' . ($ordenArray['head']['id_tp_documento'] == 12 ? '01 Copy of the purchase order' : '01 copia de la orden de compra.') . '</td>

            </tr>
        </table>
        <br>
    ';

        $html .= '<br>

                    <footer>
                        <p style="font-size:9px; " class="pie_de_pagina"> ' . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Document made by ' : 'Generado por ') . ucwords(strtolower($ordenArray['head']['nombre_usuario'])) .  '<br>'
            . ($ordenArray['head']['id_tp_documento'] == 12 ? 'Register date' : 'Fecha registro') . ': ' . $ordenArray['head']['fecha_registro'] . '<br>'
            . ($ordenArray['head']['id_tp_documento'] == 12 ? 'System version' : 'Versión del sistema') . ': ' . config('global.nombreSistema') . ' '  . config('global.version') . ' </p>
                    </footer>

            </body>

        </html>';

        return $html;
    }

    // public function imprimir_orden_pdf($id_orden_compra)
    // {
    //     $ordenArray = $this->get_orden($id_orden_compra);
    //     // $ordenArray = json_decode($orden, true);
    //     $sizeOrdenHeader=count($ordenArray['header_orden']);

    //     if($sizeOrdenHeader == 0){
    //         $html = 'Error en documento';
    //         return $html;
    //     }

    //     $now = new \DateTime();

    //     $html = '
    //     <html>
    //         <head>
    //         <style type="text/css">
    //             *{
    //                 box-sizing: border-box;
    //             }
    //             body{
    //                 background-color: #fff;
    //                 font-family: "DejaVu Sans";
    //                 font-size: 9px;
    //                 box-sizing: border-box;
    //                 padding:10px;
    //             }
    //             table{
    //                 width:100%;
    //                 border-collapse: collapse;
    //             }
    //             .tablePDF thead{
    //                 padding:4px;
    //                 background-color:#d04f46;
    //             }
    //             .bgColorRed{

    //             }
    //             .tablePDF,
    //             .tablePDF tr td{
    //                 border: 1px solid #dbdbdb;
    //             }
    //             .tablePDF tr td{
    //                 padding: 5px;
    //             }
    //             h1{
    //                 text-transform: uppercase;
    //             }
    //             .subtitle{
    //                 font-weight: bold;
    //             }
    //             .bordebox{
    //                 border: 1px solid #000;
    //             }
    //             .verticalTop{
    //                 vertical-align:top;
    //             }
    //             .texttab {
    //                 display:block;
    //                 margin-left: 20px;
    //                 margin-bottom:5px;
    //             }
    //             .right{
    //                 text-align:right;
    //             }
    //             .left{
    //                 text-align:left;
    //             }
    //             .justify{
    //                 text-align: justify;
    //             }
    //             .top{
    //                 vertical-align:top;
    //             }
    //             hr{
    //                 color:#cc352a;
    //             }
    //             footer {
    //                 position: absolute;
    //                 bottom: 0;
    //                 width: 100%;
    //                 height: auto;
    //             }

    //             .tablePDF .noBorder{
    //                 border:none;
    //                 border-left:none;
    //                 border-right:none;
    //                 border-bottom:none;
    //             }
    //             .textBold{
    //                 font-weight:bold;
    //             }
    //         </style>
    //         </head>
    //         <body>
    //             <img src="./images/LogoSlogan-80.png" alt="Logo" height="75px">
    //             <br>
    //             <hr>
    //             <h1><center>' . $ordenArray['header_orden']['tipo_documento'] . '<br>' . $ordenArray['header_orden']['codigo'] . '</center></h1>
    //             <table border="0">
    //                 <tr>
    //                     <td class="subtitle verticalTop">Sr.(s)</td>
    //                     <td class="subtitle verticalTop">:</td>
    //                     <td width="50%" class="verticalTop">' . $ordenArray['header_proveedor']['razon_social_proveedor'] . '</td>
    //                     <td width="15%" class="subtitle verticalTop">Fecha de Emisión</td>
    //                     <td class="subtitle verticalTop">:</td>
    //                     <td>' . substr($ordenArray['header_orden']['fecha_orden'], 0, 11) . '</td>
    //                 </tr>
    //                 <tr>
    //                     <td class="subtitle">Dirección</td>
    //                     <td class="subtitle verticalTop">:</td>
    //                     <td class="verticalTop">' . $ordenArray['header_proveedor']['direccion_fiscal_proveedor'] . '</td>
    //                 </tr>
    //                 <tr>
    //                     <td class="subtitle">Telefono</td>
    //                     <td class="subtitle verticalTop">:</td>
    //                     <td class="verticalTop">' . $ordenArray['header_proveedor']['telefono_proveedor'] . '</td>
    //                 </tr>
    //                 <tr>
    //                     <td class="subtitle">Contacto</td>
    //                     <td class="subtitle verticalTop">:</td>
    //                     <td class="verticalTop">' . $ordenArray['header_proveedor']['email_proveedor'] . '</td>
    //                 </tr>
    //                 <tr>
    //                     <td class="subtitle">Responsable</td>
    //                     <td class="subtitle verticalTop">:</td>
    //                     <td class="verticalTop">' . $ordenArray['header_orden']['nombre_personal_responsable'] . '</td>
    //                 </tr>
    //             </table>';

    //             $html.='<p class="left" style="color:#d04f46">';

    //             // if($ordenArray['aprobaciones']['aprob_necesarias'] == $ordenArray['aprobaciones']['total_aprob']){
    //             //     $html.='<strong>ORDEN APROBADA </strong>';
    //             //     //   $apro=implode(',',array_values($ordenArray['aprobaciones']['aprobaciones']));
    //             //     //  $html.=$apro;

    //             //     foreach ($ordenArray['aprobaciones']['aprobaciones'] as $key => $data) {
    //             //         $apro[]=$data->nombre.' ['.$data->fecha_vobo.']';
    //             //     }
    //             //     $html.=implode(", ",$apro);

    //             // }else{
    //             //     $html.='<strong>ORDEN NO APROBADA </strong>';
    //             // }

    //             $html.='</p>';


    //             $html.='<br>

    //             <table width="100%" class="tablePDF" border=0>
    //             <thead>
    //                 <tr class="subtitle">
    //                     <td width="2%">#</td>
    //                     <td width="48%">Descripción</td>
    //                     <td width="5%">Und</td>
    //                     <td width="5%">Cant.</td>
    //                     <td width="5%">Precio</td>
    //                     <td width="5%">IGV</td>
    //                     <td width="5%">Monto Dscto</td>
    //                     <td width="5%">Total</td>
    //                 </tr>
    //             </thead>';

    //     $total = 0;
    //     foreach ($ordenArray['detalle_orden'] as $key => $data) {
    //         $html .= '<tr>';
    //         $html .= '<td>' . ($key + 1) . '</td>';
    //         if($data['descripcion_detalle_orden'] != null && strlen($data['descripcion_detalle_orden']) > 0){

    //             $html .= '<td>' . ($data['codigo_item'] ? $data['codigo_item'] : '0') . ' - ' . ($data['descripcion_producto'] ? $data['descripcion_producto'] : $data['descripcion_requerimiento']) . '<br><small><ul><li>'.$data['descripcion_detalle_orden'].'</li></ul></small></td>';

    //         }else{
    //             $html .= '<td>' . ($data['codigo_item'] ? $data['codigo_item'] : '0') . ' - ' . ($data['descripcion_producto'] ? $data['descripcion_producto'] : $data['descripcion_requerimiento']) . '</td>';
    //         }
    //         $html .= '<td>' . $data['unidad_medida'] . '</td>';
    //         $html .= '<td class="right">' . $data['cantidad'] . '</td>';
    //         $html .= '<td class="right">' . $data['precio'] . '</td>';
    //         $html .= '<td class="right">' . number_format((($data['cantidad'] * $data['precio']) - (($data['cantidad']* $data['precio'])/1.18)),2,'.','') . '</td>';
    //         $html .= '<td class="right">' . $data['monto_descuento'] . '</td>';
    //         $html .= '<td class="right">' . $data['cantidad'] * $data['precio'] . '</td>';
    //         $html .= '</tr>';
    //         $total = $total + ($data['cantidad'] * $data['precio']);
    //     }

    //     $html .= '
    //             <tr>
    //                 <td class="right noBorder textBold"  colspan="7">Monto Neto '.$ordenArray['header_orden']['moneda_simbolo'].'</td>
    //                 <td class="right  noBorder textBold">' . $total . '</td>
    //             </tr>
    //             <tr>
    //                 <td class="right noBorder textBold"  colspan="7">IGV '.$ordenArray['header_orden']['moneda_simbolo'].'</td>
    //                 <td class="right noBorder textBold">' . $ordenArray['header_orden']['monto_igv'] . '</td>
    //             </tr>
    //             <tr>
    //                 <td class="right noBorder textBold"  colspan="7">Monto Total '.$ordenArray['header_orden']['moneda_simbolo'].'</td>
    //                 <td class="right noBorder textBold">' . $ordenArray['header_orden']['monto_total'] . '</td>
    //             </tr>
    //             </table>
    //             <br>

    //             <p class="subtitle">Datos para Despacho</p>
    //             <table width="100%" class="tablePDF" border=0>
    //                 <thead>
    //                     <tr class="subtitle">
    //                         <td width="2%">#</td>
    //                         <td width="38%">Descripción</td>
    //                         <td width="30%">Lugar de Despacho</td>
    //                         <td width="30%">Personal Autorizado</td>
    //                     </tr>
    //                 </thead>
    //     ';
    //         $total = 0;
    //         foreach ($ordenArray['detalle_orden'] as $key => $data) {
    //             $contador= $key + 1;
    //             $codigo= $data['codigo_item'] ? $data['codigo_item'] : '0';
    //             $desItem= $data['descripcion_producto'] ? $data['descripcion_producto'] : $data['descripcion_requerimiento'];
    //             $lugarDesp= $data['lugar_despacho_orden'];
    //             $persAut= $data['nombre_personal_autorizado'];

    //             $html .= '<tr>';
    //             $html .= '<td>' . $contador. '</td>';
    //             $html .= '<td>' .$codigo.'-'. $desItem . '</td>';
    //             $html .= '<td class="right">' . $lugarDesp . '</td>';
    //             $html .= '<td class="right">' . $persAut . '</td>';
    //             $html .= '</tr>';
    //         }

    //     $html.= '</table>
    //             <br>
    //             <p class="subtitle">Condición de Compra</p>
    //             <table border="0">
    //                 <tr>
    //                     <td width="20%"class="verticalTop">Forma de Pago</td>
    //                     <td width="5%" class="verticalTop">:</td>
    //                     <td width="70%" class="verticalTop">' . $ordenArray['condiciones']['condicion_pago'] . '</td>
    //                 </tr>
    //                 <tr>
    //                     <td width="20%" class="verticalTop">Plazo</td>
    //                     <td width="5%" class="verticalTop">:</td>
    //                     <td width="70%" class="verticalTop">' . $ordenArray['condiciones']['plazo_dias'] . '';
    //     if ($ordenArray['condiciones']['plazo_dias'] > 0) {
    //         $html .= ' días';
    //     }
    //     $html .= '</td>
    //                 </tr>
    //                 <tr>
    //                     <td width="20%"class="verticalTop">Req.</td>
    //                     <td width="5%" class="verticalTop">:</td>
    //                     <td width="70%" class="verticalTop">' . $ordenArray['header_orden']['codigo_requerimiento'] . '</td>
    //                 </tr>
    //                 <br>
    //             </table>

    //             <br>
    //             <p class="subtitle">Datos de Facturación</p>
    //             <table border="0">
    //                 <tr>
    //                     <td width="20%" class="verticalTop">Razon Social</td>
    //                     <td width="5%" class="verticalTop">:</td>
    //                     <td width="70%" class="verticalTop">' . $ordenArray['header_empresa']['razon_social_empresa'] . '</td>
    //                 </tr>
    //                 <tr>
    //                     <td width="20%"class="verticalTop">' . $ordenArray['header_empresa']['tipo_doc_empresa'] . '</td>
    //                     <td width="5%" class="verticalTop">:</td>
    //                     <td width="70%" class="verticalTop">' . $ordenArray['header_empresa']['nro_documento_empresa'] . '</td>
    //                 </tr>
    //                 <tr>
    //                     <td width="20%"class="verticalTop">Dirección</td>
    //                     <td width="5%" class="verticalTop">:</td>
    //                     <td width="70%" class="verticalTop">' . $ordenArray['header_empresa']['direccion_fiscal_empresa'] . '</td>
    //                 </tr>
    //             </table>

    //             <br/>
    //             <br/>
    //             <footer class="left">
    //             <p>GENERADO POR: ' . $ordenArray['header_orden']['nombre_usuario'] . '</p>
    //             <hr/>
    //             <table>
    //                 <tr>
    //                     <td>Oficina Principal</td>
    //                     <td>Urb. Villa del Mar M 22 - Fono: 053-484354 - Ilo</td>
    //                 </tr>
    //                 <tr>
    //                     <td>Oficina Logística</td>
    //                     <td>Urbanización Municipal cal. Condesuyos 103 - Arequipa</td>
    //                 </tr>
    //                 <tr>
    //                     <td>Oficina Logística</td>
    //                     <td>Mza. A Lote 03 APV. Vimcoop Samegua - Mariscal Nieto - Moquegua</td>
    //                 </tr>
    //                 <tr>
    //                     <td>Oficina Logística</td>
    //                     <td>Cal. R. Palma/p. Gamboa 960 - Tacna</td>
    //                 </tr>
    //                 <tr>
    //                     <td>Oficina Coorporativa</td>
    //                     <td>Cal Amador Merino Reyna 125 San Isidro - Lima</td>
    //                 </tr>
    //                 <tr>
    //                     <td>Mail</td>
    //                     <td>contacto@okcomputer.com.pe</td>
    //                 </tr>
    //                 <tr>
    //                     <td>Web</td>
    //                     <td>www.okcomputer.com.pe</td>
    //                 </tr>
    //             </footer>
    //         </body>

    //     </html>';
    //     // <p class="subtitle">Datos para Despacho</p>
    //     // <table border="0">
    //     //     <tr>
    //     //         <td width="20%" class="verticalTop">Destino / Dirección</td>
    //     //         <td width="5%" class="verticalTop">:</td>
    //     //         <td width="70%" class="verticalTop"></td>
    //     //     </tr>
    //     //     <tr>
    //     //         <td width="20%"class="verticalTop">Atención / Personal Autorizado</td>
    //     //         <td width="5%" class="verticalTop">:</td>
    //     //         <td width="70%" class="verticalTop"></td>
    //     //     </tr>
    //     // </table>
    //     return $html;
    // }



    function cambioElEstadoActualDetalleReq($id_detalle_requerimiento)
    {
        $alm_det_req = DB::table('almacen.alm_det_req')
            ->select(
                'alm_det_req.*'
            )
            ->where('alm_det_req.id_detalle_requerimiento', $id_detalle_requerimiento)
            ->whereNotIn('alm_det_req.estado', [1, 2, 15, 5, 7])
            ->get();

        if (count($alm_det_req) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function cambioElEstadoActualReq($id_requerimiento)
    {
        $alm_req = DB::table('almacen.alm_req')
            ->select(
                'alm_req.*'
            )
            ->where('alm_req.id_requerimiento', $id_requerimiento)
            ->whereNotIn('alm_req.estado', [1, 2, 15, 5, 7])
            ->get();

        if (count($alm_req) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function guardar_orden_por_requerimiento(Request $request)
    {

        try {
            DB::beginTransaction();

            // evaluar si el estado del cierre periodo
            $añoPeriodo= Periodo::find($request->id_periodo)->descripcion;
            $idEmpresa = Sede::find($request->id_sede)->id_empresa;
            // $fechaPeriodo = Carbon::createFromFormat('Y-m-d', ($periodo->descripcion . '-01-01'));
            $estadoOperativo = (new CierreAperturaController)->consultarPeriodoOperativo($añoPeriodo, $idEmpresa);
            if ($estadoOperativo != 1) { //1:abierto, 2:cerrado, 3:Declarado
                return response()->json(['id_orden_compra' => 0, 'codigo' => '','id_tp_documento' => '', 'tipo_estado' => 'warning',
                'lista_estado_requerimiento' => null,
                'lista_finalizados' => null,
                'historial_presupuesto_interno' => [],
                'mensaje' => 'No se puede generar la orden cuando el periodo operativo esta cerrado']);
            }


            $idOrden = '';
            $idTipoDocumento = '';
            $codigoOrden = '';
            $actualizarEstados = [];
            $detalleOrden=[];

            $idDetalleRequerimientoList = [];
            $count = count($request->idDetalleRequerimiento);
            for ($i = 0; $i < $count; $i++) {
                if ($request->idDetalleRequerimiento[$i] > 0) {
                    $idDetalleRequerimientoList[] = $request->idDetalleRequerimiento[$i];
                }
            }

            $requerimientoHelper = new RequerimientoHelper();
            if ($requerimientoHelper->EstaHabilitadoRequerimiento($idDetalleRequerimientoList) == true) { // buscar el requerimiento de cada detalle requerimiento y devolver si esta habilitado para acción de guardar, estado en pausa y por regularizar no es posible realizar acción de guardar

                $orden = new Orden();
                $tp_doc = ($request->id_tp_documento !== null ? $request->id_tp_documento : 2);
                // $orden->codigo =  Orden::nextCodigoOrden($tp_doc);
                $orden->id_grupo_cotizacion = $request->id_grupo_cotizacion ? $request->id_grupo_cotizacion : null;
                $orden->id_tp_documento = $tp_doc;
                $orden->id_periodo = $request->id_periodo ? $request->id_periodo : null;
                $orden->fecha = $request->fecha_emision ? $request->fecha_emision : new Carbon();
                $orden->fecha_registro = new Carbon();
                $orden->id_usuario = Auth::user()->id_usuario;
                $orden->id_moneda = $request->id_moneda ? $request->id_moneda : null;
                $orden->incluye_igv = isset($request->incluye_igv) ? $request->incluye_igv : false;
                $orden->incluye_icbper = isset($request->incluye_icbper) ? true : false;
                $orden->monto_subtotal = isset($request->monto_subtotal) ? $request->monto_subtotal : null;
                $orden->monto_igv = isset($request->monto_igv) ? $request->monto_igv : null;
                $orden->monto_total = isset($request->monto_total) ? $request->monto_total : null;
                $orden->id_proveedor = $request->id_proveedor;
                $orden->id_cta_principal = isset($request->id_cuenta_principal_proveedor) ? $request->id_cuenta_principal_proveedor : null;
                $orden->id_contacto = isset($request->id_contacto_proveedor) ? $request->id_contacto_proveedor : null;
                $orden->plazo_entrega =  $request->plazo_entrega ? $request->plazo_entrega : null;
                $orden->id_condicion_softlink = $request->id_condicion_softlink ? $request->id_condicion_softlink : null;
                $orden->id_condicion = $request->id_condicion ? $request->id_condicion : null;
                $orden->plazo_dias = $request->plazo_dias ? $request->plazo_dias : null;
                $orden->id_cotizacion = $request->id_cotizacion ? $request->id_cotizacion : null;
                $orden->id_tp_doc = isset($request->id_tp_doc) ? $request->id_tp_doc : null;
                $orden->personal_autorizado_1 = $request->personal_autorizado_1 ? $request->personal_autorizado_1 : null;
                $orden->personal_autorizado_2 = $request->personal_autorizado_2 ? $request->personal_autorizado_2 : null;
                $orden->id_occ = $request->id_cc ? $request->id_cc : null;
                $orden->id_sede = $request->id_sede ? $request->id_sede : null;
                $orden->direccion_destino = $request->direccion_destino != null ? trim(strtoupper($request->direccion_destino)) : null;
                $orden->ubigeo_destino = isset($request->id_ubigeo_destino) ? $request->id_ubigeo_destino : null;
                $orden->en_almacen = false;
                $orden->estado = 1;
                $orden->estado_pago = 1;
                $orden->codigo_softlink = $request->codigo_orden !== null ? $request->codigo_orden : '';
                $orden->observacion = $request->observacion != null ? trim(strtoupper($request->observacion)) : null;
                $orden->tipo_cambio_compra = isset($request->tipo_cambio_compra) ? $request->tipo_cambio_compra : true;
                $orden->compra_local = isset($request->compra_local) ? $request->compra_local : false;
                $orden->save();

                if ($request->id_proveedor > 0 && $request->id_rubro_proveedor != null && $request->id_rubro_proveedor > 0) {
                    $proveedor = Proveedor::find($request->id_proveedor);
                    if ($proveedor->id_contribuyente > 0) {
                        $contribuyenteProveedor = Contribuyente::find($proveedor->id_contribuyente);
                        $contribuyenteProveedor->id_rubro = $request->id_rubro_proveedor;
                        $contribuyenteProveedor->save();
                    }
                }

                for ($i = 0; $i < $count; $i++) {
                    $detalle = new OrdenCompraDetalle();
                    $detalle->id_orden_compra = $orden->id_orden_compra;
                    $detalle->id_producto = ($request->idProducto[$i] ? $request->idProducto[$i] : null);
                    $detalle->id_detalle_requerimiento = $request->idDetalleRequerimiento[$i] ? $request->idDetalleRequerimiento[$i] : null;
                    $detalle->cantidad = $request->cantidadAComprarRequerida[$i];
                    $detalle->id_unidad_medida = isset($request->unidad[$i]) ? $request->unidad[$i] : null;
                    $detalle->precio = $request->precioUnitario[$i];
                    // $detalle->descripcion_adicional = (isset($request->descripcion[$i]) && $request->descripcion[$i] != null) ? trim(strtoupper($request->descripcion[$i])) : null;
                    $detalle->descripcion_adicional = ($request->descripcion[$i] != null) ? trim(strtoupper(utf8_encode($request->descripcion[$i]))) : null;
                    $detalle->descripcion_complementaria = ($request->descripcionComplementaria[$i] != null) ? trim(strtoupper(utf8_encode($request->descripcionComplementaria[$i]))) : null;
                    $subtotalItem= floatval($request->cantidadAComprarRequerida[$i] * $request->precioUnitario[$i]);
                    $detalle->subtotal =$subtotalItem;
                    $detalle->tipo_item_id = $request->idTipoItem[$i];
                    $detalle->estado = 1;
                    $detalle->fecha_registro = new Carbon();

                    $detalle->save();
                    $detalle->importe_item_para_presupuesto= $subtotalItem;
                    $detalleOrden[]= $detalle;
                }

            

                DB::commit();

                $codigo = Orden::nextCodigoOrden($orden->id_tp_documento);
                $ord = Orden::find($orden->id_orden_compra);
                $ord->codigo = $codigo;
                $ord->save();

                $codigoOrden = $ord->codigo;
                $idOrden = $orden->id_orden_compra;
                $idTipoDocumento = $orden->id_tp_documento;

                if (isset($orden->id_orden_compra) and $orden->id_orden_compra > 0) {
                    $actualizarEstados = $this->actualizarNuevoEstadoRequerimiento('CREAR', $orden->id_orden_compra, $orden->codigo);
                }
                // $historialPresupuestoInterno = (new PresupuestoInternoController)->afectarPresupuestoInterno('resta','orden',$orden->id_orden_compra,$detalleOrden);

                //actualizar estado gasto
                foreach ($detalleOrden as $item) {
                    if($item->id_detalle_requerimiento >0 ){

                        $detalleRequerimientoLogistico = DetalleRequerimiento::where([['id_detalle_requerimiento',$item->id_detalle_requerimiento],['estado','!=',7]])->first();

                        if ($detalleRequerimientoLogistico) {
                            $requerimientoLogistico = Requerimiento::find($detalleRequerimientoLogistico->id_requerimiento);
                            $idPartida= $detalleRequerimientoLogistico->id_partida_pi;
                            $idRequerimiento=$detalleRequerimientoLogistico->id_requerimiento;
                            $fecha =$requerimientoLogistico->fecha_requerimiento;
                            $idPresupuesto= $requerimientoLogistico->id_presupuesto_interno;
                            $idDetalleRequerimiento= $item->id_detalle_requerimiento;
                            $idOrden= $item->id_orden_compra;
                            $idDetalleOrden= $item->id_detalle_orden;
                            if(isset($request->incluye_igv) && $item->tipo_item_id ==1){
                                $importe= floatval($item->cantidad) * floatval($item->precio) * floatval(1.18);
                            }else{
                                $importe= floatval($item->cantidad) * floatval($item->precio);
                            }

                            $estado= 2;
                            $operacion= 'R';
                            if($idPresupuesto > 0 && $idPartida > 0){
                                PresupuestoInternoHistorialHelper::actualizarHistorialSaldoParaDetalleRequerimientoLogisticoConOrden($idPresupuesto, $idPartida, $idRequerimiento, $idDetalleRequerimiento, $fecha, $idOrden, $idDetalleOrden,$importe, $estado,$operacion);
                            }
                        }

                    }
                }

                // if ($request->migrar_oc_softlink == true) {
                //     $statusMigracionSoftlink = (new MigrateOrdenSoftLinkController)->migrarOrdenCompra($idOrden)->original ?? null; //tipo : success , warning, error, mensaje : ""
                // }

                return response()->json([
                    'id_orden_compra' => $idOrden,
                    'id_tp_documento' => $idTipoDocumento,
                    'codigo' => $codigoOrden,
                    'mensaje' =>  'OK',
                    'tipo_estado' => 'success',
                    'lista_estado_requerimiento' => $actualizarEstados['lista_estado_requerimiento']??[],
                    'lista_finalizados' => $actualizarEstados['lista_finalizados']??[],
                    'error' => $actualizarEstados['error'],
                    'historial_presupuesto_interno' => $historialPresupuestoInterno??[]

                ]);
            } else { // si el estado de algun requerimiento viculado no esta habilitado, esta con estado 38 o 39

                return response()->json([
                    'id_orden_compra' => null,
                    'codigo' => null,
                    'id_tp_documento' => null,
                    'mensaje' => 'No puede guardar la orden, existe un requerimiento vinculado con estado "En pausa" o "Por regularizar"',
                    'tipo_estado' => 'warning',
                    'lista_estado_requerimiento' => null,
                    'lista_finalizados' => null,
                    'historial_presupuesto_interno' => []
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['id_orden_compra' => $idOrden, 'id_tp_documento' => $idTipoDocumento, 'codigo' => $codigoOrden, 'tipo_estado' => 'error', 'lista_finalizados' => ($actualizarEstados != null ? $actualizarEstados['lista_finalizados'] : []), 'historial_presupuesto_interno'=>[], 'mensaje' => 'Mensaje de error: ' . $e->getMessage()]);
        }
    }




    function obtenerIdRequerimientoList($idOrden)
    {

        $det_orden = DB::table('logistica.log_det_ord_compra')
            ->select('log_det_ord_compra.*')
            ->where('log_det_ord_compra.id_orden_compra', $idOrden)
            ->get();



        $idDetalleRequerimientoList = [];
        $idRequerimientoList = [];

        foreach ($det_orden as $value) {
            if ($value->id_detalle_requerimiento > 0) {
                $idDetalleRequerimientoList[] = $value->id_detalle_requerimiento;
            }
        }

        if (count($idDetalleRequerimientoList) > 0) {

            $alm_det_req = DB::table('almacen.alm_det_req')
                ->select('alm_det_req.*')
                ->where('alm_det_req.estado', '!=', 7)
                ->whereIn('alm_det_req.id_detalle_requerimiento', $idDetalleRequerimientoList)
                ->get();

            foreach ($alm_det_req as $value) {
                $idRequerimientoList[] = $value->id_requerimiento;
            }
        }


        return $idRequerimientoList;
    }

    function obtenerDetalleRequerimiento($idRequerimientoList)
    {
        $alm_det_req = DetalleRequerimiento::with(['reserva' => function ($q) {
            $q->where('alm_reserva.estado', '=', 1);
        }])
            ->where([['alm_det_req.tiene_transformacion', false], ['alm_det_req.estado', '!=', 7]])
            ->whereIn('alm_det_req.id_requerimiento', $idRequerimientoList)
            ->get();

        return $alm_det_req;
    }

    function obtenerItemBase($detalleRequerimiento)
    {

        $itemBase = [];
        foreach ($detalleRequerimiento as $value) {

            if ($value->tiene_transformacion == false || $value->tiene_transformacion == null) {
                $stock_comprometido = 0;
                if (count($value->reserva) > 0) {
                    foreach ($value->reserva as $reserva) {
                        $stock_comprometido += $reserva->stock_comprometido;
                    }
                }

                $itemBase[] = [
                    'id_detalle_requerimiento' => $value->id_detalle_requerimiento,
                    'id_requerimiento' => $value->id_requerimiento,
                    'cantidad' => $value->cantidad,
                    'stock_comprometido' => $stock_comprometido,
                    'estado' => $value->estado,
                    'cantidad_atendida' => 0,
                    'update' => false,
                ];
            }
        }
        return $itemBase;
    }

    function obtenerItemBaseEnOtrasOrdenesGeneradas($idOrden, $itemBaseList)
    {
        $idDetalleOrdenList = [];
        foreach ($itemBaseList as $value) {
            $idDetalleOrdenList[] = $value['id_detalle_requerimiento'];
        }

        $otros_det_orden = DB::table('logistica.log_det_ord_compra')
            ->select(
                'log_det_ord_compra.*'
            )
            ->where([['log_det_ord_compra.id_orden_compra', '!=', $idOrden], ['log_det_ord_compra.estado', '!=', 7]])
            ->whereIn('log_det_ord_compra.id_detalle_requerimiento', $idDetalleOrdenList)
            ->get();

        $data = [];
        foreach ($otros_det_orden as $value) {
            $data[] = [
                'id_detalle_orden' => $value->id_detalle_orden,
                'id_detalle_requerimiento' => $value->id_detalle_requerimiento,
                'id_orden_compra' => $value->id_orden_compra,
                'cantidad' => $value->cantidad
            ];
        }

        return $data;
    }
    function obtenerDetalleOrdenGenerada($idOrden)
    {


        $det_orden = DB::table('logistica.log_det_ord_compra')
            ->select(
                'log_det_ord_compra.*'
            )
            ->where([['log_det_ord_compra.id_orden_compra', '=', $idOrden]])
            ->get();
        $data = [];
        foreach ($det_orden as $value) {
            $data[] = [
                'id_detalle_requerimiento' => $value->id_detalle_requerimiento,
                'id_detalle_orden' => $value->id_detalle_orden,
                'id_orden_compra' => $value->id_orden_compra,
                'estado' => $value->estado,
                'cantidad' => $value->cantidad
            ];
        }

        return $data;
    }

    function obtenerItemAtendidoParcialOSinAtender($itemBaseList)
    {
        $ItemAtendidoParcialOSinAtenderList = [];
        foreach ($itemBaseList as $value) {
            if ($value['estado'] == 1 || $value['estado'] == 15 || $value['estado'] == 27) {
                $ItemAtendidoParcialOSinAtenderList[] = $value;
            }
        }
        return $ItemAtendidoParcialOSinAtenderList;
    }

    function obtenerNuevoEstadoDetalleRequerimiento($itemBaseList, $itemBaseEnOtrasOrdenesGeneradasList, $detalleOrdenGeneradaList, $itemAtendidoParcialOSinAtender)
    {
        $itemsOtrasOrden = [];
        if (count($itemBaseEnOtrasOrdenesGeneradasList) > 0) {
            foreach ($itemBaseEnOtrasOrdenesGeneradasList as $key => $value) {
                $itemsOtrasOrden[] = [
                    'id_detalle_requerimiento' => $value['id_detalle_requerimiento'],
                    'cantidad' => $value['cantidad']
                ];
            }
        }
        $itemsReqSinAtenderYParciales = [];
        if (count($itemAtendidoParcialOSinAtender) > 0) {
            foreach ($itemAtendidoParcialOSinAtender as $value) {
                $itemsReqSinAtenderYParciales[] = [
                    'id_detalle_requerimiento' => $value['id_detalle_requerimiento'],
                    'cantidad' => $value['cantidad'],
                    'stock_comprometido' => $value['stock_comprometido'],
                ];
            }
        }

        $itemsOrdenGeneradaHoy = [];
        if (count($detalleOrdenGeneradaList) > 0) {
            foreach ($detalleOrdenGeneradaList as $value) {
                $itemsOrdenGeneradaHoy[] = [
                    'id_detalle_requerimiento' => $value['id_detalle_requerimiento'],
                    'cantidad' => $value['cantidad'],
                    'estado' => $value['estado']
                ];
            }
        }

        // recorrer itembase para actualziar estado de detOrdenGeneradaHoy
        if (count($itemsOrdenGeneradaHoy) > 0) {
            foreach ($itemBaseList as $keyItemBase => $itemBase) {
                foreach ($itemsOrdenGeneradaHoy as $keyOrdenHoy => $ordenHoy) {
                    if ($itemBase['id_detalle_requerimiento'] == $ordenHoy['id_detalle_requerimiento']) {
                        $itemBaseList[$keyItemBase]['cantidad_atendida'] += floatval($ordenHoy['cantidad']);
                        $itemBaseList[$keyItemBase]['update'] = true;
                        if ($ordenHoy['estado'] == 7) {
                            $itemBaseList[$keyItemBase]['cantidad_atendida'] = 0;
                        }
                    }
                }
            }
        }
        // sumar si existe stockComprometido en itemAtendidoParcialOsinAtender
        if (count($itemsReqSinAtenderYParciales) > 0) {
            foreach ($itemBaseList as $keyItemBase => $itemBase) {
                foreach ($itemsReqSinAtenderYParciales as $keySinAtenderYParcial => $itemSinAtenderYParcial) {
                    if ($itemBase['id_detalle_requerimiento'] == $itemSinAtenderYParcial['id_detalle_requerimiento']) {
                        $itemBaseList[$keyItemBase]['cantidad_atendida'] += floatval($itemSinAtenderYParcial['stock_comprometido']);

                        if ($itemBase['estado'] != 1) {
                            $itemBaseList[$keyItemBase]['update'] = true;
                        }
                    }
                }
            }
        }

        // sumar cantidad de otra ordenes si existe
        if (count($itemBaseEnOtrasOrdenesGeneradasList) > 0) {
            foreach ($itemBaseList as $keyItemBase => $itemBase) {
                foreach ($itemBaseEnOtrasOrdenesGeneradasList as $keyItemOtrasOrdenes => $itemOtrasOrdenes) {
                    if ($itemBase['id_detalle_requerimiento'] == $itemOtrasOrdenes['id_detalle_requerimiento']) {
                        $itemBaseList[$keyItemBase]['cantidad_atendida'] += floatval($itemOtrasOrdenes['cantidad']);
                        $itemBaseList[$keyItemBase]['update'] = true;
                    }
                }
            }
        }

        // detenerminar con itemmBase el estado del detalle Req
        foreach ($itemBaseList as $keyItemBase => $itemBase) {
            if ($itemBase['estado'] != '28') { //diferente  a atentido almacén total
                if (($itemBase['cantidad'] == $itemBase['cantidad_atendida']) && ($itemBase['update'] == true)) {
                    $itemBaseList[$keyItemBase]['estado'] = 5; //atendido total
                } elseif ($itemBase['cantidad'] < $itemBase['cantidad_atendida']) {
                    $itemBaseList[$keyItemBase]['estado'] = 5; //atendido total

                } elseif (($itemBase['cantidad_atendida'] > 0) && ($itemBase['cantidad'] > $itemBase['cantidad_atendida'])) {
                    $itemBaseList[$keyItemBase]['estado'] = 15; //atendido parcial
                } elseif (($itemBase['cantidad_atendida'] == 0) && ($itemBase['cantidad'] > 0)) {
                    $itemBaseList[$keyItemBase]['estado'] = 1; //elaborado
                }
            }
        }



        return $itemBaseList;
    }

    function obtenerNuevoEstadoCabeceraRequerimiento($idRequerimientoList, $nuevoEstadoDetalleRequerimiento)
    {
        // Debugbar::info($idRequerimientoList);
        // Debugbar::info($nuevoEstadoDetalleRequerimiento);

        $estadoRequerimiento = [];
        foreach ($nuevoEstadoDetalleRequerimiento as $itemBase) {
            $estadoRequerimiento[$itemBase['id_requerimiento']][] = $itemBase['estado'];
        }
        // Debugbar::info($estadoRequerimiento);

        $nuevoEstadoCabeceraRequerimiento = [];

        $arrParaEstadosAtencionParcial = [1, 15, 27];

        foreach ($idRequerimientoList as $indice => $valueReqId) {
            $clave = 0;
            foreach ($arrParaEstadosAtencionParcial as $arr) {
                $clave += intval(in_array($arr, $estadoRequerimiento[$valueReqId]));
            }
            if ($clave > 0) {
                $nuevoEstadoCabeceraRequerimiento[] = [
                    'id_requerimiento' => $valueReqId,
                    'nuevo_estado' => 15
                ];
            } elseif ($clave == 0) {

                $nuevoEstadoCabeceraRequerimiento[] = [
                    'id_requerimiento' => $valueReqId,
                    'nuevo_estado' => 5
                ];
            }
        }
        return $nuevoEstadoCabeceraRequerimiento;
    }

    function actualizarNuevoEstadoRequerimiento($tipoPeticion, $idOrden, $codigo)
    {

        $idRequerimientoList = $this->obtenerIdRequerimientoList($idOrden);
        $detalleRequerimiento = $this->obtenerDetalleRequerimiento($idRequerimientoList);
        $itemBaseList = $this->obtenerItemBase($detalleRequerimiento);
        $itemBaseEnOtrasOrdenesGeneradasList = $this->obtenerItemBaseEnOtrasOrdenesGeneradas($idOrden, $itemBaseList);
        $detalleOrdenGeneradaList = $this->obtenerDetalleOrdenGenerada($idOrden);
        $itemAtendidoParcialOSinAtender = $this->obtenerItemAtendidoParcialOSinAtender($itemBaseList);
        // if(config('app.debug')){
        // Debugbar::info($idRequerimientoList);
        // Debugbar::info($detalleRequerimiento);
        // Debugbar::info($itemBaseList);
        // Debugbar::info($itemBaseEnOtrasOrdenesGeneradasList);
        // Debugbar::info($detalleOrdenGeneradaList);
        //     Debugbar::info($itemAtendidoParcialOSinAtender);
        // }


        $nuevoEstadoDetalleRequerimiento = $this->obtenerNuevoEstadoDetalleRequerimiento($itemBaseList, $itemBaseEnOtrasOrdenesGeneradasList, $detalleOrdenGeneradaList, $itemAtendidoParcialOSinAtender);
        // Debugbar::info($nuevoEstadoDetalleRequerimiento);
        $nuevoEstadoCabeceraRequerimiento = $this->obtenerNuevoEstadoCabeceraRequerimiento($idRequerimientoList, $nuevoEstadoDetalleRequerimiento);
        // Debugbar::info($nuevoEstadoCabeceraRequerimiento);


        // actualizar cabecera requerimiento
        foreach ($nuevoEstadoCabeceraRequerimiento as $c) {
            DB::table('almacen.alm_req')
                ->where('id_requerimiento', $c['id_requerimiento'])
                ->update(['estado' => $c['nuevo_estado']]);
        }

        // actualizar detalle requerimiento
        foreach ($nuevoEstadoDetalleRequerimiento as $d) {
            if ($d['update'] == true) {
                DB::table('almacen.alm_det_req')
                    ->where('id_detalle_requerimiento', $d['id_detalle_requerimiento'])
                    ->update(
                        [
                            'estado' => $d['estado']
                        ]
                    );
            }
        }


        // evaluar si requerimiento requere finalizar CDP
        $CantidadRequerimientoConCDP = 0;
        foreach ($idRequerimientoList as $key => $idRequerimiento) {
            $requerimientoOrden = Requerimiento::find($idRequerimiento);
            if ($requerimientoOrden->id_cc > 0) {
                $CantidadRequerimientoConCDP += 1;
            }
        }

        if ($CantidadRequerimientoConCDP > 0) {
            $finalizadosORestablecido = CuadroPresupuestoHelper::finalizar($tipoPeticion, array_unique($idRequerimientoList));
        } else {
            $finalizadosORestablecido['lista_finalizados'] = [];
            $finalizadosORestablecido['lista_restablecidos'] = [];
            $finalizadosORestablecido['error'] = [];
        }



        return [
            'lista_estado_requerimiento' => $nuevoEstadoCabeceraRequerimiento,
            'lista_finalizados' => $finalizadosORestablecido['lista_finalizados'],
            'lista_restablecidos' => $finalizadosORestablecido['lista_restablecidos'],
            'error' => $finalizadosORestablecido['error']
        ];
    }

    public function actualizar_orden_por_requerimiento(Request $request)
    {
        try {
            DB::beginTransaction();

            // evaluar si el estado del cierre periodo

            $añoPeriodo= Periodo::find($request->id_periodo)->descripcion;
            $idEmpresa = Sede::find($request->id_sede)->id_empresa;
            $estadoOperativo = (new CierreAperturaController)->consultarPeriodoOperativo($añoPeriodo,$idEmpresa);
            if ($estadoOperativo != 1) { //1:abierto, 2:cerrado, 3:Declarado
                return response()->json(['id_orden_compra' => 0, 'codigo' => '','id_tp_documento' => '', 'tipo_estado' => 'warning', 'mensaje' => 'No se puede actualizar la orden cuando el periodo operativo esta cerrado']);
            }


            $data = [];
            $status = 0;

            $idDetalleRequerimientoList = [];
            $count = count($request->idDetalleRequerimiento);
            for ($i = 0; $i < $count; $i++) {
                if ($request->idDetalleRequerimiento[$i] > 0) {
                    $idDetalleRequerimientoList[] = $request->idDetalleRequerimiento[$i];
                }
            }

            $requerimientoHelper = new RequerimientoHelper();
            if ($requerimientoHelper->EstaHabilitadoRequerimiento($idDetalleRequerimientoList) == true) {


                // if(in_array(Auth::user()->id_usuario,[14,5])){
                // $ValidarOrdenSoftlink['tipo']='success';
                // $ValidarOrdenSoftlink['mensaje']='ok';
                // }else{
                // $ValidarOrdenSoftlink = (new MigrateOrdenSoftLinkController)->validarOrdenSoftlink($request->id_orden);
                // }
                // if ($ValidarOrdenSoftlink['tipo'] == 'success') {
                $orden = Orden::where("id_orden_compra", $request->id_orden)->first();
                $orden->id_grupo_cotizacion = $request->id_grupo_cotizacion ? $request->id_grupo_cotizacion : null;
                $orden->id_tp_documento = ($request->id_tp_documento !== null ? $request->id_tp_documento : 2);
                $orden->id_usuario = Auth::user()->id_usuario;
                $orden->id_moneda = $request->id_moneda ? $request->id_moneda : null;
                $orden->fecha = $request->fecha_emision ? $request->fecha_emision : new Carbon();
                $orden->id_periodo = $request->id_periodo ? $request->id_periodo : null;
                $orden->incluye_igv = isset($request->incluye_igv) ? $request->incluye_igv : false;
                $orden->incluye_icbper = isset($request->incluye_icbper) ? true : false;
                $orden->monto_subtotal = isset($request->monto_subtotal) ? $request->monto_subtotal : null;
                $orden->monto_igv = isset($request->monto_igv) ? $request->monto_igv : null;
                $orden->monto_total = isset($request->monto_total) ? $request->monto_total : null;
                $orden->id_proveedor = $request->id_proveedor;
                $orden->id_cta_principal = isset($request->id_cuenta_principal_proveedor) ? $request->id_cuenta_principal_proveedor : null;
                $orden->id_contacto = isset($request->id_contacto_proveedor) ? $request->id_contacto_proveedor : null;
                $orden->plazo_entrega =  $request->plazo_entrega ? $request->plazo_entrega : null;
                $orden->id_condicion = $request->id_condicion ? $request->id_condicion : null;
                $orden->id_condicion_softlink = $request->id_condicion_softlink ? $request->id_condicion_softlink : null;
                $orden->plazo_dias = $request->plazo_dias ? $request->plazo_dias : null;
                $orden->id_cotizacion = $request->id_cotizacion ? $request->id_cotizacion : null;
                $orden->id_tp_doc = isset($request->id_tp_doc) ? $request->id_tp_doc : null;
                $orden->personal_autorizado_1 = $request->personal_autorizado_1 ? $request->personal_autorizado_1 : null;
                $orden->personal_autorizado_2 = $request->personal_autorizado_2 ? $request->personal_autorizado_2 : null;
                $orden->id_occ = $request->id_cc ? $request->id_cc : null;
                $orden->id_sede = $request->id_sede ? $request->id_sede : null;
                $orden->direccion_destino = $request->direccion_destino ? $request->direccion_destino : null;
                $orden->ubigeo_destino = isset($request->id_ubigeo_destino) ? $request->id_ubigeo_destino : null;
                $orden->codigo_softlink = $request->codigo_orden !== null ? $request->codigo_orden : '';
                $orden->observacion = isset($request->observacion) ? $request->observacion : null;
                $orden->tipo_cambio_compra = isset($request->tipo_cambio_compra) ? $request->tipo_cambio_compra : true;
                $orden->compra_local = isset($request->compra_local) ? $request->compra_local : false;
                $orden->save();

                if ($request->id_proveedor > 0 && $request->id_rubro_proveedor != null && $request->id_rubro_proveedor > 0) {
                    $proveedor = Proveedor::find($request->id_proveedor);
                    if ($proveedor->id_contribuyente > 0) {
                        $contribuyenteProveedor = Contribuyente::find($proveedor->id_contribuyente);
                        $contribuyenteProveedor->id_rubro = $request->id_rubro_proveedor;
                        $contribuyenteProveedor->save();
                    }
                }

                $idDetalleProcesado = [];
                $detalleArray=[];

                if (isset($request->cantidadAComprarRequerida)) {

                    $count = count($request->cantidadAComprarRequerida);
                    for ($i = 0; $i < $count; $i++) {
                        $id = $request->idRegister[$i];
                        if (preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $id)) // es un id con numeros y letras => es nuevo, insertar
                        {
                            $detalle = new OrdenCompraDetalle();
                            $detalle->id_orden_compra = $orden->id_orden_compra;
                            $detalle->id_producto = $request->idProducto[$i];
                            $detalle->id_detalle_requerimiento = $request->idDetalleRequerimiento[$i];
                            $detalle->cantidad = $request->cantidadAComprarRequerida[$i];
                            $detalle->id_unidad_medida = $request->unidad[$i];
                            $detalle->precio = $request->precioUnitario[$i];
                            $detalle->descripcion_adicional = $request->descripcion[$i] != null ? trim(strtoupper($request->descripcion[$i])) : null;
                            $detalle->descripcion_complementaria = ($request->descripcionComplementaria[$i] != null) ? trim(strtoupper(utf8_encode($request->descripcionComplementaria[$i]))) : null;
                            $detalle->subtotal = floatval($request->cantidadAComprarRequerida[$i] * $request->precioUnitario[$i]);
                            $detalle->tipo_item_id = $request->idTipoItem[$i];
                            $detalle->estado = 1;
                            $detalle->save();
                        } else { // es un id solo de numerico => actualiza
                            if ($request->idEstado[$i] == 7) {
                                if (is_numeric($id)) { // si es un numero
                                    $detalle = OrdenCompraDetalle::where("id_detalle_orden", $id)->first();
                                    $detalle->estado = 7;
                                    $detalle->save();
                                    $detalle->importe_item_para_presupuesto= (floatval($detalle->precio) * floatval($detalle->cantidad));
                                    $detalle->operacion_item_para_presupuesto= 'suma';
                                }
                            } else {

                                $detalle = OrdenCompraDetalle::where("id_detalle_orden", $id)->first();
                                $detalle->id_producto = $request->idProducto[$i];
                                $detalle->id_detalle_requerimiento = $request->idDetalleRequerimiento[$i];
                                $detalle->cantidad = $request->cantidadAComprarRequerida[$i];

                                $subtotalOrigen=floatval($detalle->precio) * floatval($detalle->cantidad);
                                $subtotalNuevo=  floatval($request->precioUnitario[$i]) * floatval($request->cantidadAComprarRequerida[$i]);
                                if($subtotalOrigen != $subtotalNuevo){
                                    if($subtotalOrigen < $subtotalNuevo){
                                        $importeItemParaPresupuesto=$subtotalNuevo - $subtotalOrigen;
                                        $tipoOperacionItemParaPresupuesto= 'resta';

                                    }elseif($subtotalOrigen > $subtotalNuevo){
                                        $importeItemParaPresupuesto=$subtotalOrigen - $subtotalNuevo;
                                        $tipoOperacionItemParaPresupuesto= 'suma';
                                        // Debugbar::info($importeItemParaPresupuesto);
                                    }
                                }

                                $detalle->id_unidad_medida = $request->unidad[$i];
                                $detalle->precio = $request->precioUnitario[$i];
                                $detalle->descripcion_adicional = ($request->descripcion[$i] != null) ? trim(strtoupper(utf8_encode($request->descripcion[$i]))) : null;
                                $detalle->descripcion_complementaria = ($request->descripcionComplementaria[$i] != null) ? trim(strtoupper(utf8_encode($request->descripcionComplementaria[$i]))) : null;
                                $detalle->subtotal = floatval($request->cantidadAComprarRequerida[$i] * $request->precioUnitario[$i]);
                                $detalle->tipo_item_id = $request->idTipoItem[$i];
                                $detalle->save();
                                $detalle->importe_item_para_presupuesto= $importeItemParaPresupuesto??0;
                                $detalle->operacion_item_para_presupuesto= $tipoOperacionItemParaPresupuesto??'';
                                $detalleArray[] = $detalle;

                                $tipoOperacionItemParaPresupuesto='';
                                $importeItemParaPresupuesto=0;

                                $idDetalleProcesado[] = $detalle->id_detalle_orden;
                            }
                        }
                    }

                }


                $detalleParaPresupuestoSumaArray=[];
                $detalleParaPresupuestoRestaArray=[];
                // Debugbar::info($detalleArray);
                foreach ($detalleArray as $key => $det) {
                    if(isset($det->operacion_item_para_presupuesto) && $det->operacion_item_para_presupuesto =='suma'){
                        $detalleParaPresupuestoSumaArray[]=$det;
                    }
                    if(isset($det->operacion_item_para_presupuesto) && $det->operacion_item_para_presupuesto =='resta'){
                        $detalleParaPresupuestoRestaArray[]=$det;
                    }
                }
                // Debugbar::info($detalleParaPresupuestoSumaArray);
                // Debugbar::info($detalleParaPresupuestoRestaArray);

                // $afectaPresupuestoInternoSuma = (new PresupuestoInternoController)->afectarPresupuestoInterno('suma','orden',$orden->id_orden_compra, $detalleParaPresupuestoSumaArray);
                // $afectaPresupuestoInternoResta = (new PresupuestoInternoController)->afectarPresupuestoInterno('resta','orden',$orden->id_orden_compra, $detalleParaPresupuestoRestaArray);



                $data = [
                    'id_orden_compra' => $orden->id_orden_compra,
                    'codigo' => $orden->codigo,
                    'id_tp_documento' => $orden->id_tp_documento,
                    'tipo_estado' => 'success',
                    'mensaje' => 'Orden actualizada',
                    'afecta_presupuesto_interno_suma'=>$afectaPresupuestoInternoSuma??[],
                    'afecta_presupuesto_interno_resta'=>$afectaPresupuestoInternoResta??[],
                    // 'mensaje' => $ValidarOrdenSoftlink['mensaje']
                    // 'status_migracion_softlink' => $ValidarOrdenSoftlink,
                ];

                // $status = 200;
                // } else {
                //     $data = [
                //         'id_orden_compra' => 0,
                //         'codigo' => '',
                //         'mensaje' => $ValidarOrdenSoftlink['mensaje'],
                //         'status_migracion_softlink' => $ValidarOrdenSoftlink,
                //     ];
                //     $status = 204;
                // }



                DB::commit();
                if (isset($request->id_orden) and $request->id_orden > 0) {
                    $this->actualizarNuevoEstadoRequerimiento('ACTUALIZAR', $request->id_orden, null);
                }

                //actualizar estado gasto
                foreach ($detalleArray as $item) {
                    if($item->id_detalle_requerimiento >0 ){

                        $detalleRequerimientoLogistico = DetalleRequerimiento::where([['id_detalle_requerimiento',$item->id_detalle_requerimiento],['estado','!=',7]])->first();

                        if ($detalleRequerimientoLogistico) {
                            $requerimientoLogistico = Requerimiento::find($detalleRequerimientoLogistico->id_requerimiento);
                            $idPartida= $detalleRequerimientoLogistico->id_partida_pi;
                            $idRequerimiento=$detalleRequerimientoLogistico->id_requerimiento;
                            $fecha =$requerimientoLogistico->fecha_requerimiento;
                            $idPresupuesto= $requerimientoLogistico->id_presupuesto_interno;
                            $idDetalleRequerimiento= $item->id_detalle_requerimiento;
                            $idOrden= $item->id_orden_compra;
                            $idDetalleOrden= $item->id_detalle_orden;

                            if(isset($request->incluye_igv) && $item->tipo_item_id ==1){ // incluir IGV
                                $importe= floatval($item->cantidad) * floatval($item->precio) * floatval(1.18);
                            }else{
                                $importe= floatval($item->cantidad) * floatval($item->precio);
                            }
                            $estado= 2;
                            $operacion= 'R';
                            if($idPresupuesto > 0 && $idPartida > 0){
                                PresupuestoInternoHistorialHelper::actualizarHistorialSaldoParaDetalleRequerimientoLogisticoConOrden($idPresupuesto, $idPartida, $idRequerimiento, $idDetalleRequerimiento, $fecha, $idOrden, $idDetalleOrden,$importe, $estado,$operacion);
                            }
                        }

                    }
                }
                // if (str_contains($data['mensaje'], 'No existe un id_softlink en la OC seleccionada')) {
                //     $migrarOrdenSoftlink = (new MigrateOrdenSoftLinkController)->migrarOrdenCompra($request->id_orden)->original;
                //     if ($migrarOrdenSoftlink['tipo'] == 'success') {
                //         $data = [
                //             'id_orden_compra' => 0,
                //             'codigo' => '',
                //             'tipo_estado' => 'success',
                //             'mensaje' => 'Se a obtenido una respuesta satisfactoria al intentar migrar la orden a softlink',
                //             'status_migracion_softlink' => $migrarOrdenSoftlink,
                //         ];
                //     }
                // }
                // if ($status == 200) {
                //     // if(in_array(Auth::user()->id_usuario,[14,5])){
                //     //     $migrarOrdenSoftlink['tipo'] = 'success';
                //     // }else{
                //         $migrarOrdenSoftlink = (new MigrateOrdenSoftLinkController)->migrarOrdenCompra($request->id_orden)->original;
                //     // }
                //     if ($migrarOrdenSoftlink['tipo'] == 'success') {
                //         $data = [
                //             'id_orden_compra' => $orden->id_orden_compra,
                //             'codigo' => $orden->codigo,
                //             'tipo_estado' => 'success',
                //             'mensaje' => 'Se a obtenido una respuesta satisfactoria al intentar migrar la orden a softlink',
                //             'status_migracion_softlink' => $migrarOrdenSoftlink,
                //         ];
                //     } else {
                //         $data = [
                //             'id_orden_compra' => 0,
                //             'codigo' => '',
                //             'tipo_estado' => 'warning',
                //             'mensaje' => 'Se a obtenido una respuesta de advertencia o error al intentar migrar la orden a softlink',
                //             'status_migracion_softlink' => $migrarOrdenSoftlink,
                //         ];
                //     }
                // }
                return response()->json($data);
                // } else {
                // return response()->json(['id_orden_compra' => 0, 'codigo' => '', 'tipo_estado' => 'warning', 'status_migracion_softlink' => null, 'mensaje' => 'No puede actualizar la orden, existe un requerimiento vinculado con estado "En pausa" o  "Por regularizar"']);
            }
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['id_orden_compra' => 0, 'codigo' => '', 'id_tp_documento' => '', 'tipo_estado' => 'error',  'mensaje' => 'Hubo un problema al actualizar la orden. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }


    public function validarOrdenAgilOrdenSoftlink(Request $request)
    {
        try {
            DB::beginTransaction();

            $ValidarOrdenSoftlink = (new MigrateOrdenSoftLinkController)->validarOrdenSoftlink($request->idOrden);

            DB::commit();


            return response()->json($ValidarOrdenSoftlink);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al actualizar la orden. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

    // public function ver_orden($id_orden)
    // {

    //     $log_ord_compra = DB::table('logistica.log_ord_compra')
    //     ->select(
    //         'log_ord_compra.*',
    //         'adm_contri.id_contribuyente',
    //         'adm_contri.razon_social',
    //         'adm_contri.nro_documento',
    //         'log_cdn_pago.descripcion as condicion',
    //         'sis_moneda.simbolo as simbolo_moneda',
    //         'sis_moneda.descripcion as descripcion_moneda',
    //         'cta_prin.nro_cuenta as nro_cuenta_prin',
    //         'cta_alter.nro_cuenta as nro_cuenta_alter',
    //         'cta_detra.nro_cuenta as nro_cuenta_detra',
    //         'estados_compra.descripcion as estado_doc',
    //         'log_ord_compra_pago.id_pago',
    //         'log_ord_compra_pago.detalle_pago',
    //         'log_ord_compra_pago.archivo_adjunto'
    //         )
    //     ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
    //     ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
    //     ->leftjoin('logistica.log_cdn_pago','log_cdn_pago.id_condicion_pago','=','log_ord_compra.id_condicion')
    //     ->leftjoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','log_ord_compra.id_moneda')
    //     ->leftjoin('contabilidad.adm_cta_contri as cta_prin','cta_prin.id_cuenta_contribuyente','=','log_ord_compra.id_cta_principal')
    //     ->leftjoin('contabilidad.adm_cta_contri as cta_alter','cta_alter.id_cuenta_contribuyente','=','log_ord_compra.id_cta_alternativa')
    //     ->leftjoin('contabilidad.adm_cta_contri as cta_detra','cta_detra.id_cuenta_contribuyente','=','log_ord_compra.id_cta_detraccion')
    //     ->leftjoin('logistica.estados_compra','estados_compra.id_estado','=','log_ord_compra.estado')
    //     ->leftjoin('logistica.log_ord_compra_pago','log_ord_compra_pago.id_orden_compra','=','log_ord_compra.id_orden_compra')
    //     ->where('log_ord_compra.id_orden_compra','=',$id_orden)
    //     ->get();

    //     if (isset($log_ord_compra)) {
    //         $orden = [];
    //         foreach ($log_ord_compra as $data) {
    //                 $orden = [
    //                     'id_orden_compra' => $data->id_orden_compra,
    //                     'codigo'         => $data->codigo,
    //                     'fecha'          => $data->fecha,
    //                     'codigo_softlink'=> $data->codigo_softlink,
    //                     'incluye_igv'    => $data->incluye_igv,
    //                     'razon_social'   => $data->razon_social,
    //                     'nro_documento'  => $data->nro_documento,
    //                     'condicion'      => $data->condicion,
    //                     'descripcion_moneda' => $data->descripcion_moneda,
    //                     'simbolo_moneda'        => $data->simbolo_moneda,
    //                     'id_estado'     => $data->estado,
    //                     'estado_doc'     => $data->estado_doc,
    //                     'id_condicion'   => $data->id_condicion,
    //                     'plazo_dias'     => $data->plazo_dias,
    //                     'plazo_entrega'  => $data->plazo_entrega,
    //                     'igv_porcentaje' => $data->igv_porcentaje,
    //                     'monto_subtotal' => $data->monto_subtotal,
    //                     'monto_igv'      => $data->monto_igv,
    //                     'monto_total'    => $data->monto_total,
    //                     'observacion'    => $data->observacion
    //                 ];
    //             }

    //     } else {

    //         $orden = [];
    //     }


    //     $log_det_ord_compra = DB::table('logistica.log_det_ord_compra')
    //     ->leftjoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
    //     ->leftjoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','log_ord_compra.id_moneda')
    //     ->leftJoin('almacen.alm_prod', 'log_det_ord_compra.id_producto', '=', 'alm_prod.id_producto')
    //     ->leftJoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
    //     ->leftJoin('almacen.alm_subcat','alm_subcat.id_subcategoria','=','alm_prod.id_subcategoria')
    //     ->leftJoin('almacen.alm_det_req', 'log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')
    //     ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
    //     ->leftJoin('almacen.alm_und_medida as und_medida_det_req', 'alm_det_req.id_unidad_medida', '=', 'und_medida_det_req.id_unidad_medida')
    //     // ->leftJoin('almacen.alm_det_req_adjuntos', 'alm_det_req_adjuntos.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')
    //     ->leftJoin('almacen.alm_almacen', 'alm_det_req.id_almacen_reserva', '=', 'alm_almacen.id_almacen')
    //     ->leftjoin('logistica.estados_compra','estados_compra.id_estado','=','log_det_ord_compra.estado')

    //     ->select(
    //         'log_det_ord_compra.id_detalle_orden',
    //         'log_det_ord_compra.id_orden_compra',
    //         'log_det_ord_compra.garantia',
    //         'log_det_ord_compra.estado',
    //         'log_det_ord_compra.personal_autorizado',
    //         'log_det_ord_compra.lugar_despacho',
    //         'log_det_ord_compra.descripcion_adicional',
    //         'log_det_ord_compra.id_unidad_medida',
    //         'sis_moneda.simbolo as simbolo_moneda',
    //         'sis_moneda.descripcion as descripcion_moneda',
    //         'log_det_ord_compra.precio',
    //         'log_det_ord_compra.cantidad',
    //         'log_det_ord_compra.estado as id_estado_detalle_orden',
    //         'estados_compra.descripcion as estado_detalle_orden',
    //         'alm_det_req.id_detalle_requerimiento',
    //         'alm_req.id_requerimiento',
    //         'alm_req.codigo AS codigo_requerimiento',
    //         'alm_det_req.id_requerimiento',
    //         'alm_det_req.precio_unitario',
    //         // 'alm_det_req.cantidad',
    //         // 'alm_det_req.id_unidad_medida',
    //         'und_medida_det_req.descripcion AS unidad_medida',
    //         'alm_det_req.fecha_registro AS fecha_registro_alm_det_req',
    //         'alm_det_req.lugar_entrega',
    //         'alm_det_req.descripcion_adicional',
    //         'alm_det_req.id_tipo_item',
    //         'alm_det_req.id_producto',
    //         'alm_cat_prod.descripcion as categoria',
    //         'alm_subcat.descripcion as subcategoria',
    //         'alm_prod.codigo AS alm_prod_codigo',
    //         'alm_prod.part_number',
    //         'alm_prod.descripcion AS alm_prod_descripcion',

    //         'alm_det_req.id_almacen_reserva',
    //         'alm_almacen.descripcion as almacen_reserva',
    //      )
    //     ->where([
    //         ['log_det_ord_compra.id_orden_compra', '=', $id_orden],
    //     ])

    //     ->orderBy('log_det_ord_compra.id_detalle_orden', 'desc')
    //     ->get();

    //     // return $log_det_ord_compra;
    //     $total = 0;
    //     if (isset($log_det_ord_compra)) {
    //         $lastId = "";
    //         $detalle_orden = [];
    //         foreach ($log_det_ord_compra as $data) {
    //             if ($data->id_detalle_requerimiento !== $lastId) {
    //                 $subtotal =+ $data->cantidad *  $data->precio_unitario;
    //                 $total = $subtotal;
    //                 $detalle_orden[] = [
    //                     'id_detalle_orden'          => $data->id_detalle_orden,
    //                     'id_orden_compra'          => $data->id_orden_compra,
    //                     'id_detalle_requerimiento'  => $data->id_detalle_requerimiento,
    //                     'id_requerimiento'          => $data->id_requerimiento,
    //                     'codigo_requerimiento'      => $data->codigo_requerimiento,
    //                     'cantidad'                  => $data->cantidad,
    //                     'id_unidad_medida'          => $data->id_unidad_medida,
    //                     'unidad_medida'             => $data->unidad_medida,
    //                     'descripcion_moneda'        => $data->descripcion_moneda,
    //                     'simbolo_moneda'            => $data->simbolo_moneda,
    //                     'precio_unitario'           => $data->precio_unitario,
    //                     'descripcion_adicional'     => $data->descripcion_adicional,
    //                     'lugar_entrega'             => $data->lugar_entrega,
    //                     'fecha_registro'            => $data->fecha_registro_alm_det_req,
    //                     'estado'                    => $data->estado,
    //                     'id_tipo_item'              => $data->id_tipo_item,
    //                     'codigo_producto'           => $data->alm_prod_codigo,
    //                     'id_producto'               => $data->id_producto,
    //                     'categoria'                 => $data->categoria,
    //                     'subcategoria'              => $data->subcategoria,
    //                     'part_number'               => $data->part_number,
    //                     'descripcion'               => $data->alm_prod_descripcion,
    //                     'id_almacen'                => $data->id_almacen_reserva,
    //                     'almacen_reserva'           => $data->almacen_reserva,
    //                     'subtotal'                  =>  $subtotal,
    //                     'id_estado_detalle_orden'   => $data->id_estado_detalle_orden,
    //                     'estado_detalle_orden'      => $data->estado_detalle_orden
    //                 ];
    //                 $lastId = $data->id_detalle_requerimiento;
    //             }
    //         }



    //     } else {

    //         $detalle_orden = [];
    //     }


    //     $output=['status'=>200, 'data'=>['orden'=>$orden,'detalle_orden'=>$detalle_orden]];


    //     return response()->json($output);
    // }


    public function generar_orden_por_requerimiento_pdf($id_orden_compra)
    {
        $pdf = \App::make('dompdf.wrapper');
        $id = $id_orden_compra;

        $pdf->loadHTML($this->imprimir_orden_por_requerimiento_pdf($id));
        // return response()->json($this->imprimir_orden_por_requerimiento_pdf($id));
        return $pdf->stream();
        return $pdf->download('orden_de_compra.pdf');
    }

    // public function generar_orden_pdf($id_orden_compra)
    // {
    //     $pdf = \App::make('dompdf.wrapper');
    //     // $id = $this->decode5t($id_orden_compra);
    //     $id = $id_orden_compra;
    //     // $data = $this->get_orden($id);
    //     // return response()->json($data);

    //     $pdf->loadHTML($this->imprimir_orden_pdf($id));
    //     return $pdf->stream();
    //     return $pdf->download('orden.pdf');
    // }

    public function guardar_contacto(Request $request)
    {
        $status = 0;
        $data = [];

        $id_datos_contacto = DB::table('contabilidad.adm_ctb_contac')->insertGetId(
            [
                'id_contribuyente' => $request->id_contribuyente,
                'nombre' => $request->nombre,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'cargo' => $request->cargo,
                'direccion' => $request->direccion,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
            'id_datos_contacto'
        );
        if ($id_datos_contacto > 0) {
            $status = 200;
            $data = DB::table('contabilidad.adm_ctb_contac')
                ->where([
                    ['id_datos_contacto', '=', $id_datos_contacto]
                ])
                ->first();
        }

        $output = ['status' => $status, 'data' => $data];

        return json_encode($output);
    }

    function tieneIngresoAlmacen($id_orden)
    {
        $status = 0;
        $msj = [];
        $data = [];
        $tipo_estado = '';
        // buscar en detalle_orden los id_detalle_requerimiento
        $log_det_ord_compra = DB::table('logistica.log_det_ord_compra')
            ->select(
                'log_det_ord_compra.*'
            )
            ->where([
                ['log_det_ord_compra.id_orden_compra', '=', $id_orden]
            ])
            ->get();

        // verificar si existe ingreso en almacen
        if (count($log_det_ord_compra) > 0) {
            foreach ($log_det_ord_compra as $data) {
                $id_detalle_orden_list[] = $data->id_detalle_orden;
            }

            $guia_com_det = DB::table('almacen.guia_com_det')
                ->select(
                    'guia_com_det.*'
                )
                ->whereIn('guia_com_det.id_oc_det', $id_detalle_orden_list)
                ->where('guia_com_det.estado', 1)
                ->get();
            if (count($guia_com_det) > 0) {
                $status = 401;
                $tipo_estado = 'warning';
                $msj[] = 'No se puede anular. La orden tiene items ingresados a almacén';
                $data = true;
            } else {
                $status = 200;
                $tipo_estado = 'success';
                $msj[] = 'La orden no tiene items ingresados a almacén';
                $data = false;
            }
        } else {
            $status = 204;
            $tipo_estado = 'warning';
            $msj[] = 'la orden no tiene detalle';
            $data = false;
        }

        $output = ['status' => $status, 'tipo_estado' => $tipo_estado, 'mensaje' => $msj, 'data' => $data];
        return $output;
    }

    function tienePagoAutorizado($id_orden)
    {
        $status = 0;
        $msj = [];
        $data = [];
        $tipo_estado = '';
        // buscar en detalle_orden los id_detalle_requerimiento
        $log_ord_compra = DB::table('logistica.log_ord_compra')
            ->select(
                'log_ord_compra.*'
            )
            ->where([
                ['log_ord_compra.id_orden_compra', '=', $id_orden]
            ])
            ->get();

        if (count($log_ord_compra) > 0) {
            if ($log_ord_compra->first()->estado_pago >= 5) { // verificar si tiene autorizado el pago
                $status = 401;
                $tipo_estado = 'warning';
                $msj[] = 'No se puede anular la orden tiene autorizado el pago';
                $data = false;
            } else {
                $status = 200;
                $tipo_estado = 'success';
                $msj[] = 'La orden no tiene pago autorizado';
                $data = false;
            }
        } else {
            $status = 204;
            $tipo_estado = 'warning';
            $msj[] = 'la orden no se encontró';
            $data = false;
        }

        $output = ['status' => $status, 'tipo_estado' => $tipo_estado, 'mensaje' => $msj, 'data' => $data];
        return $output;
    }

    function makeRevertirOrden($id_orden, $sustento)
    {
        $status = 0;
        $msj = [];
        $output = [];
        $id_requerimiento_list = [];
        $id_usuario_list = [];
        $tipo_estado = '';
        $idUsuariosAlAnularOrden = [];
        $notificacion = [];
        $detalleArray=[];

        $orden = Orden::with('sede')->find($id_orden);

        $revertirOrden = DB::table('logistica.log_ord_compra') //revertir orden
            ->where([
                ['id_orden_compra', $id_orden]
            ])
            ->update(
                [
                    'estado' => 7,
                    'codigo_softlink' => null,
                    'fecha_anulacion' => new Carbon(),
                    'sustento_anulacion' => $sustento
                ]
            );


        $detalleOrden = OrdenCompraDetalle::where('id_orden_compra', $id_orden)->get();
        foreach ($detalleOrden as $key => $item) {
            $detalle= OrdenCompraDetalle::find($item->id_detalle_orden);
            $detalle->estado=7;
            $detalle->save();
            $detalle->importe_item_para_presupuesto=$detalle->subtotal;
            $detalleArray[] = $detalle;
        }
        // $revertirDetalleOrden = DB::table('logistica.log_det_ord_compra') // revertir detalle orden
        //     ->where([['id_orden_compra', $id_orden]])
        //     ->update(
        //         [
        //             'estado' => 7
        //         ]
        //     );
        if ($revertirOrden > 0) {
            $status = 200;
            $tipo_estado = 'success';
            $msj[] = 'Orden Anulada';
        } else {
            $status = 204;
            $tipo_estado = 'warning';
            $msj[] = 'hubo un problema al tratar de anular la orden';
        }
        if (count($detalleArray) > 0) {
            $status = 200;
            $tipo_estado = 'success';
            // $msj[]='Detalle Orden Revertida';
        } else {
            $status = 204;
            $tipo_estado = 'warning';
            $msj[] = 'hubo un problema al tratar de anular el detalle de la orden';
        }
        // revertir requerimiento y detalle requerimiento ==>
        // buscar en detalle_orden los id_detalle_requerimiento
        $log_det_ord_compra = DB::table('logistica.log_det_ord_compra')
            ->select(
                'log_det_ord_compra.*'
            )
            ->where([
                ['log_det_ord_compra.id_orden_compra', '=', $id_orden]
            ])
            ->get();

        if (count($log_det_ord_compra) > 0) {
            foreach ($log_det_ord_compra as $data) {
                $id_detalle_req_list[] = $data->id_detalle_requerimiento;
            }
            // buscar id_requerimiento
            $alm_req = DB::table('almacen.alm_req')
                ->select(
                    'alm_req.*'
                )
                ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento')
                ->whereIn('alm_det_req.id_detalle_requerimiento', $id_detalle_req_list)
                ->get();

            if (count($alm_req) > 0) {
                foreach ($alm_req as $data) {
                    $id_requerimiento_list[] = $data->id_requerimiento;
                    $id_usuario_list[] = $data->id_usuario;
                }

                if (count($id_requerimiento_list) > 0) {
                    DB::table('almacen.alm_req')
                        ->whereIn('id_requerimiento', $id_requerimiento_list)
                        ->update(['estado' => 2]);

                    $detalleRequerimiento = DetalleRequerimiento::with(['reserva' => function ($q) {
                        $q->where('alm_reserva.estado', '=', 1);
                    }])->whereIn('id_requerimiento', $id_requerimiento_list)->get();

                    foreach ($detalleRequerimiento as $value) {
                        if (count($value->reserva) == 0) {
                            $det = DetalleRequerimiento::find($value->id_detalle_requerimiento);
                            $det->estado = 1;
                            $det->save();
                        }
                    }
                    $status = 200;
                    $tipo_estado = 'success';
                    $msj[] = 'se restableció el estado del requerimiento';
                    $finalizadosORestablecido = CuadroPresupuestoHelper::finalizar('ANULAR', $id_requerimiento_list);
                    $idUsuariosAlAnularOrden[] =  Auth::user()->id_usuario; // Usuarios que reciben correo al anula orden

                    if ($finalizadosORestablecido['lista_restablecidos']  && count($finalizadosORestablecido['lista_restablecidos']) > 0) {
                        foreach ($finalizadosORestablecido['lista_restablecidos'] as $lr) {

                            $msj[] = 'Se actualizo el estado del cuadro de prespuesto ' . $lr['oportunidad']->codigo_oportunidad;
                        }
                        // enviar correo de anulación de orden
                        $correosAnulaciónOrden = [];

                        if (config('app.debug')) {
                            $correosAnulaciónOrden[] = config('global.correoDebug1');
                            $idUsuariosAlAnularOrden[] = Auth::user()->id_usuario;
                        } else {

                            // $correosAnulaciónOrden[] = Auth::user()->email; //usuario en sessión que genero la acción
                            $idUsuariosAlAnularOrden = Usuario::getAllIdUsuariosPorRol(27); // Usuarios que reciben correo al anula orden
                            foreach ($idUsuariosAlAnularOrden as $id) {
                                if (Usuario::find($id) != null) {
                                    if (Usuario::find($id)->email != null) {
                                        $correosAnulaciónOrden[] = Usuario::withTrashed()->find($id)->email;
                                    }
                                }
                            }
                            foreach ($id_usuario_list as $idUsu) {
                                if (Usuario::find($idUsu) != null) {
                                    if (Usuario::find($idUsu)->email != null) {
                                        $correosAnulaciónOrden[] = Usuario::withTrashed()->find($idUsu)->email; // usuario dueño del requerimiento(s)
                                    }
                                }
                            }
                        }


                        // final de envio correo de anulación de orden


                    }

                    // Compras (Karla Quijano, Luis Alegre, Richard Dorado) //id_usuario (78,75,4)
                    // Despacho (Ricardo Visbal, Yennifer Chicata, Silvia Nashñate) //id_usuario (64,74,97)
                    // Almacen (Henry Lozano, Dora Casales, Leandro Somontes y Geraldine Capcha) //id_usuario (60,93,96,66)
                    // PM (Helen Ayma, Maricielo Hinostroza y Boris Correa) //id_usuario (95,87,82)
                    // Vendedor (según el CDP),
                    // Manuel Rivera, Jonathan Medina // id_usuario (26,6)
                    // Mail::to($correosAnulaciónOrden)->send(new EmailOrdenAnulada($orden, $finalizadosORestablecido['lista_restablecidos'], Auth::user()->nombre_corto));
                    $notificacion = NotificacionHelper::notificacionAnularOrden($orden, array_unique($idUsuariosAlAnularOrden));
                }
                // else {
                // $status = 204;
                // $msj[] = 'hubo un problema, no se pudo restablecer el estado del requerimientos';
                // }
            } //-> si no se encuentra req
            else {
                // $status = 200;
                $msj[] = 'no se encontro requerimientos';
            }

            // actualizar campo estado 7 del registro si la orden fue anulada
            PresupuestoInternoHistorialHelper::actualizarRegistroPorDocumentoAnuladoEnHistorialSaldo(null,$orden->id_orden_compra,null);

        } // -> si no tiene detalle la orden
        else {
            $status = 204;
            $tipo_estado = 'warning';
            $msj[] = 'no se encontro el detalle de la orden';
        }

        $output = ['status' => $status, 'tipo_estado' => $tipo_estado, 'mensaje' => $msj, 'requerimientoIdList' => $id_requerimiento_list, 'notificacion' => $notificacion];
        return $output;
    }

    function makeAnularItemOrdenByIdDetalleRequerimiento($idOrden, $idDetalleRequerimiento)
    {
        $status = 0;
        $msj = [];
        $output = [];
        if ($idOrden > 0 && $idDetalleRequerimiento > 0) {
            $detalleOrdenes = OrdenCompraDetalle::where([['id_orden_compra', $idOrden], ['id_detalle_requerimiento', $idDetalleRequerimiento]])->get();
            foreach ($detalleOrdenes as $do) {
                $do->estado = 7;
                $do->save();
            }
            $status = 200;
            $msj[] = 'Item Anulado';
        } else {
            $status = 204;
            $msj[] = 'hubo un problema al tratar de anular el item de la orden, id(s) debe ser > 0';
        }
        $output = ['status' => $status, 'mensaje' => $msj];
        return $output;
    }

    public function anularOrden(Request $request)
    {
        try {
            DB::beginTransaction();

            $idDetalleRequerimientoList = [];
            $detalleOrden = OrdenCompraDetalle::where([["id_orden_compra", $request->idOrden], ["estado", "!=", 7]])->get();
            $orden = Orden::where("id_orden_compra", $request->idOrden)->first();

            // verificar cierre de periodo operativo
            $añoPeriodo= Periodo::find($orden->id_periodo)->descripcion;
            $idEmpresa = Sede::find($orden->id_sede)->id_empresa;
            $estadoOperativo = (new CierreAperturaController)->consultarPeriodoOperativo($añoPeriodo,$idEmpresa);
            if ($estadoOperativo != 1) { //1:abierto, 2:cerrado, 3:Declarado
                return response()->json(['id_orden_compra' => 0, 'codigo' => '', 'tipo_estado' => 'warning', 'mensaje' => 'No se puede anular la orden cuando el periodo operativo esta cerrado']);
            }

            foreach ($detalleOrden as $do) {
                if ($do->id_detalle_requerimiento > 0) {
                    $idDetalleRequerimientoList[] = $do->id_detalle_requerimiento;
                }
            }

            $requerimientoHelper = new RequerimientoHelper();
            if ($requerimientoHelper->EstaHabilitadoRequerimiento($idDetalleRequerimientoList) == true) {
                $idOrden = $request->idOrden;
                $sustento =  $request->sustento != null ? trim(strtoupper($request->sustento)) : null;

                $status = 0;
                $tipo_estado = '';
                $msj = [];
                $output = [];
                $requerimientoIdList = [];
                $anulacion = '';
                $notificacion = [];

                $ValidarOrdenSoftlink = (new MigrateOrdenSoftLinkController)->validarOrdenSoftlink($idOrden);
                if ($ValidarOrdenSoftlink['tipo'] == 'success' || $ValidarOrdenSoftlink['tipo'] == 'error') {
                    $anulacion = 'OK';
                } else {
                    $output = [
                        'id_orden_compra' => 0,
                        'codigo' => '',
                        'status' => 204,
                        'tipo_estado' => 'warning',
                        'mensaje' => 'No se puede anular la orden',
                        'status_migracion_softlink' => $ValidarOrdenSoftlink,
                    ];
                    $status = 204;
                }

                // proceso anulación
                if ($anulacion == 'OK') {
                    $hasIngreso = $this->tieneingresoAlmacen($idOrden);
                    $hasPagoAutorizado = $this->tienePagoAutorizado($idOrden);
                    if (($hasIngreso['status'] == 200 && $hasIngreso['data'] == false) && ($hasPagoAutorizado['status'] == 200 && $hasPagoAutorizado['data'] == false)) {
                        $makeRevertirOrden = $this->makeRevertirOrden($idOrden, $sustento);
                        $status = $makeRevertirOrden['status'];
                        $tipo_estado = $makeRevertirOrden['tipo_estado'];
                        $msj[] = $makeRevertirOrden['mensaje'];
                        $requerimientoIdList = $makeRevertirOrden['requerimientoIdList'];
                        $notificacion = $makeRevertirOrden['notificacion'];
                    } else {
                        if ($hasIngreso['status'] != 200) {
                            $status = $hasIngreso['status'];
                            $tipo_estado = $hasIngreso['tipo_estado'];
                            $msj[] = $hasIngreso['mensaje'];

                        } elseif ($hasPagoAutorizado['status'] != 200) {
                            $status = $hasPagoAutorizado['status'];
                            $tipo_estado = $hasPagoAutorizado['tipo_estado'];
                            $msj[] = $hasPagoAutorizado['mensaje'];
                        }

                    }


                    if ($status == 200) {
                        $orden = Orden::select(
                            'log_ord_compra.codigo'
                        )
                            ->where('log_ord_compra.id_orden_compra', $idOrden)
                            ->first();

                        for ($i = 0; $i < count($requerimientoIdList); $i++) {
                            DB::table('almacen.alm_req_obs')
                                ->insert([
                                    'id_requerimiento' => $requerimientoIdList[$i],
                                    'accion' => 'ORDEN ANULADA',
                                    'descripcion' => 'Orden ' . ($orden->codigo ? $orden->codigo : "") . ' anulada',
                                    'id_usuario' => Auth::user()->id_usuario,
                                    'fecha_registro' => date('Y-m-d H:i:s')
                                ]);
                        }
                    }
                    // Debugbar::info($status);

                    $output = [
                        'id_orden_compra' => $idOrden,
                        'codigo' => $orden->codigo,
                        'status' => $status,
                        'tipo_estado' => $tipo_estado,
                        'mensaje' => $msj,
                        'status_migracion_softlink' => $ValidarOrdenSoftlink,
                        'notificacion' => $notificacion,
                    ];
                    $status = 200;
                }

                // proceso anulación


                DB::commit();

                if ($ValidarOrdenSoftlink['tipo'] == 'success') {
                    $migrarOrdenSoftlink = (new MigrateOrdenSoftLinkController)->anularOrdenSoftlink($idOrden)->original;
                    if ($migrarOrdenSoftlink['tipo'] == 'success') {
                        $output = [
                            'id_orden_compra' => $idOrden,
                            'codigo' => $orden->codigo,
                            'status' => 200,
                            'tipo_estado' => 'success',
                            'mensaje' => $msj,
                            'status_migracion_softlink' => $migrarOrdenSoftlink,
                        ];
                    } else {
                        $output = [
                            'id_orden_compra' => 0,
                            'codigo' => '',
                            'status' => 204,
                            'tipo_estado' => 'warning',
                            'mensaje' => 'No se pudo anular la orden',
                            'status_migracion_softlink' => $migrarOrdenSoftlink,
                        ];
                    }
                }
                return response()->json($output);
            } else {
                return response()->json(['id_orden_compra' => 0, 'codigo' => '', 'tipo_estado' => 'warning', 'status_migracion_softlink' => null, 'mensaje' => 'No puede anular la orden, existe un requerimiento vinculado con estado "En pausa" o  "Por regularizar"']);
            }
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['id_orden_compra' => 0, 'codigo' => '', 'tipo_estado' => 'error', 'status_migracion_softlink' => null, 'mensaje' => 'Hubo un problema al anular la orden. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }
    public function anularItemOrden(Request $request)
    {
        // try {
        //     DB::beginTransaction();

        $idOrden = $request->idOrden;
        $idDetalleRequerimiento = $request->idDetalleRequerimiento;


        $status = 0;
        $msj = [];
        $output = [];
        $requerimientoIdList = [];

        $hasIngreso = $this->TieneingresoAlmacen($idOrden);
        if ($hasIngreso['status'] == 200 && $hasIngreso['data'] == false) {
            $makeAnularItemOrdenByDetalleRequerimiento = $this->makeAnularItemOrdenByIdDetalleRequerimiento($idOrden, $idDetalleRequerimiento);
            $status = $makeAnularItemOrdenByDetalleRequerimiento['status'];
            $msj[] = $makeAnularItemOrdenByDetalleRequerimiento['mensaje'];

            $detallesReq = DetalleRequerimiento::where('id_detalle_requerimiento', $idDetalleRequerimiento)->get();
            foreach ($detallesReq as $dr) {
                $requerimiento = Requerimiento::find($dr->id_requerimiento);
                $requerimientoIdList[] = $requerimiento->id_requerimiento;
            }

            for ($i = 0; $i < count($requerimientoIdList); $i++) {
                DB::table('almacen.alm_req_obs')
                    ->insert([
                        'id_requerimiento' => $requerimientoIdList[$i],
                        'accion' => 'UN ITEM DE ORDEN FUE ANULADA',
                        'descripcion' => 'Orden ' . (Orden::find($idOrden)->codigo ?? "") . ' anulada',
                        'id_usuario' => Auth::user()->id_usuario,
                        'fecha_registro' => date('Y-m-d H:i:s')
                    ]);
            }

            (new ComprasPendientesController)->restablecerEstadoDetalleRequerimiento($idDetalleRequerimiento);
        } else {
            $status = $hasIngreso['status'];
            $msj[] = $hasIngreso['mensaje'];
        }

        // DB::commit();

        if ($status == 200) {
            $detOrdenes = OrdenCompraDetalle::where('id_orden_compra', $idOrden)->get();
            $cantidadItemOrden = 0;
            $cantidadItemOrdenAnulados = 0;
            foreach ($detOrdenes as $d) {
                $cantidadItemOrden++;
                if ($d->estado == 7) {
                    $cantidadItemOrdenAnulados++;
                }
            }
            if ($cantidadItemOrden == $cantidadItemOrdenAnulados) {
                $anularOrden = $this->makeAnularOrden($idOrden);
                $msj[] = $anularOrden['mensaje'];
            }
        }

        return response()->json(['status' => $status, 'mensaje' => $msj]);

        // } catch (\PDOException $e) {
        //     DB::rollBack();
        // }
    }

    function makeAnularOrden($idOrden)
    {
        $status = 0;
        $msj = [];

        if ($idOrden > 0) {

            $orden = Orden::find($idOrden);
            $orden->estado = 7;
            $orden->save();
            $status = 200;
            $msj[] = 'Orden Anulado';
        } else {
            $status = 204;
        }

        $output = ['status' => $status, 'mensaje' => $msj];
        return $output;
    }

    public function exportListaOrdenesNivelCabeceraExcel($filtro)
    {

        // return $this->reporteListaOrdenes($filtro);
        return Excel::download(new ListOrdenesHeadExport($filtro), 'lista de ordenes.xlsx');
    }
    public function exportListaOrdenesNivelDetalleExcel()
    {
        return Excel::download(new ListOrdenesDetailExport, 'lista de items de ordenes.xlsx');
    }
    public function reporteOrdenesCompraExcel($idEmpresa, $idSede, $fechaRegistroDesde, $fechaRegistroHasta)
    {
        return Excel::download(new ReporteOrdenesCompraExcel($idEmpresa, $idSede, $fechaRegistroDesde, $fechaRegistroHasta), 'reporte_ordenes_compra.xlsx');
    }
    public function reporteOrdenesServicioExcel($idEmpresa, $idSede, $fechaRegistroDesde, $fechaRegistroHasta)
    {
        return Excel::download(new ReporteOrdenesServicioExcel($idEmpresa, $idSede, $fechaRegistroDesde, $fechaRegistroHasta), 'reporte_ordenes_servicio.xlsx');
    }
    public function reporteTransitoOrdenesCompraExcel($idEmpresa, $idSede, $fechaRegistroDesde, $fechaRegistroHasta)
    {
        return Excel::download(new ReporteTransitoOrdenesCompraExcel($idEmpresa, $idSede, $fechaRegistroDesde, $fechaRegistroHasta), 'reporte_transito_ordenes_compra.xlsx');
    }

    public function listarCuentasBancariasProveedor($idProveedor)
    {
        // $cuentas = CuentaContribuyente::mostrarCuentasContribuyente($idProveedor)->get();
        $cuentas = Proveedor::mostrarCuentasProveedor($idProveedor)->get();
        return $cuentas;
    }

    public function guardarCuentaBancariaProveedor(Request $request)
    {
        $status = 0;

        $idContribuyente = Proveedor::find($request->id_proveedor)->id_contribuyente;

        $cuentaBancariaProveedor = new CuentaContribuyente();
        $cuentaBancariaProveedor->id_contribuyente  = $idContribuyente;
        $cuentaBancariaProveedor->id_banco  = $request->id_banco;
        $cuentaBancariaProveedor->id_tipo_cuenta  = $request->id_tipo_cuenta;
        $cuentaBancariaProveedor->id_moneda  = $request->id_moneda;
        $cuentaBancariaProveedor->nro_cuenta  = $request->nro_cuenta;
        $cuentaBancariaProveedor->nro_cuenta_interbancaria  = $request->nro_cuenta_interbancaria;
        $cuentaBancariaProveedor->swift  = $request->swift;
        $cuentaBancariaProveedor->estado  = 1;
        $cuentaBancariaProveedor->fecha_registro  = new Carbon();
        $cuentaBancariaProveedor->id_usuario = Auth::user()->id_usuario;

        $cuentaBancariaProveedor->save();


        if ($cuentaBancariaProveedor && $cuentaBancariaProveedor->id_cuenta_contribuyente > 0) {
            $comentario = 'Cuenta: '.($request->nro_cuenta??'').', CCI: '.($request->nro_cuenta_interbancaria??'').', Agregado por: '.Auth::user()->nombre_corto;
            LogActividad::registrar(Auth::user(), 'Orden Compra / servicio', 2, $cuentaBancariaProveedor->getTable(), null, $cuentaBancariaProveedor, $comentario);

            $status = 200;
        }

        $output = ['status' => $status, 'id_cuenta_contribuyente' => $cuentaBancariaProveedor->id_cuenta_contribuyente];

        return json_encode($output);
    }
    public function obtenerContactoProveedor($idProveedor)
    {

        $output = Proveedor::find($idProveedor)->contactoContribuyente;

        return json_encode($output);
    }

    public function mostrarProveedores(Request $request)
    {

        $idMoneda = $request->idMoneda;
        if($idMoneda != null && $idMoneda >0){
            $proveedores = Proveedor::with(['contribuyente.tipoDocumentoIdentidad', 'estadoProveedor', 'cuentaContribuyente'=> function($q) use($idMoneda){
                $q->where([['estado', '=', 1],['id_moneda','=',$idMoneda]]);
                }])
                ->whereHas('contribuyente', function ($q) {
                $q->where('estado', '=', 1);
            })->where('log_prove.estado', '=', 1);
        }else{
            $proveedores = Proveedor::with(['contribuyente.tipoDocumentoIdentidad', 'estadoProveedor', 'cuentaContribuyente'=> function($q){
                $q->where('estado', '=', 1);
                }])
                ->whereHas('contribuyente', function ($q) {
                $q->where('estado', '=', 1);
            })->where('log_prove.estado', '=', 1);
        }





        return datatables($proveedores)->toJson();
    }


    public function enviarNotificacionFinalizacionCDP(Request $request)
    {
        try {
            DB::beginTransaction();
            $idOrden = $request->idOrden;
            $idDetalleRequerimientoList = [];
            $idRequerimientoList = [];
            $idCuadroPresupuestoFinalizadoList = [];
            $codigoOportunidad = [];
            $payloadCuadroPresupuestoFinalizado = [];
            $idUsuarios = [];
            $tipoStatus = "";
            $mensaje = "";
            $mensajeNotificacion = "";

            if ($idOrden > 0) {
                $detalleOrden = OrdenCompraDetalle::where('id_orden_compra', $idOrden)->get();
                foreach ($detalleOrden as $do) {
                    if ($do->id_detalle_requerimiento > 0) {
                        $idDetalleRequerimientoList[] = $do->id_detalle_requerimiento;
                    }
                }
                if (count($idDetalleRequerimientoList) == 0) {
                    $tipoStatus = "error";
                    $mensaje = "No se encontro Id detalle requerimiento en el detalle de la orden";
                } else {
                    $detalleRequerimiento = DetalleRequerimiento::whereIn('id_detalle_requerimiento', $idDetalleRequerimientoList)->get();
                    foreach ($detalleRequerimiento as $dr) {
                        $idRequerimientoList[] = $dr->id_requerimiento;
                    }

                    //busca cdp finalizados
                    $correoVendedor = '';
                    foreach (array_unique($idRequerimientoList) as $idRequerimiento) {
                        $requerimiento = Requerimiento::find($idRequerimiento);
                        if ($requerimiento->id_cc > 0 && $requerimiento->id_tipo_requerimiento == 1) {
                            $cuadroPresupuesto = CuadroCosto::find($requerimiento->id_cc);
                            if ($cuadroPresupuesto->estado_aprobacion == 4) {
                                $idCuadroPresupuestoFinalizadoList[] = $requerimiento->id_cc;
                                $codigoOportunidad[] = $cuadroPresupuesto->oportunidad->codigo_oportunidad;
                                // obtener correo vendedor
                                $usuarioResponsable = DB::table('mgcp_usuarios.users')->where('id', $cuadroPresupuesto->oportunidad->id_responsable)->first();
                                $correoVendedor = $usuarioResponsable->email ?? '';

                                $payloadCuadroPresupuestoFinalizado[] = [
                                    'requerimiento' => $requerimiento,
                                    'cuadro_presupuesto' => $cuadroPresupuesto,
                                    'orden_compra_propia' => $cuadroPresupuesto->oportunidad->ordenCompraPropia,
                                    'oportunidad' => $cuadroPresupuesto->oportunidad
                                ];
                            }
                        }
                    }

                    // si existe CDP finalizados (estado_aprobacion = 4), preparar correo y enviar
                    if ($idCuadroPresupuestoFinalizadoList > 0) {
                        $correosOrdenServicioTransformacion = [];
                        $correoFinalizacionCuadroPresupuesto = [];

                        if (config('app.debug')) {
                            $correosOrdenServicioTransformacion[] = config('global.correoDebug1');
                            $correoFinalizacionCuadroPresupuesto[] = config('global.correoDebug1');
                        } else {
                            if ($correoVendedor != '' || $correoVendedor != null) {
                                $correosOrdenServicioTransformacion[] = $correoVendedor; // agregar correo de vendedor
                            }
                            $idUsuarios = Usuario::getAllIdUsuariosPorRol(25); //Rol de usuario de despacho externo
                            foreach ($idUsuarios as $id) {
                                $correosOrdenServicioTransformacion[] = Usuario::withTrashed()->find($id)->email;
                            }

                            //$correoUsuarioEnSession=Auth::user()->email;
                            $correoFinalizacionCuadroPresupuesto[] = Auth::user()->email;
                            $correoFinalizacionCuadroPresupuesto[] = Usuario::withTrashed()->find($requerimiento->id_usuario)->email;
                        }

                        // Mail::to(array_unique($correoFinalizacionCuadroPresupuesto))->send(new EmailFinalizacionCuadroPresupuesto($codigoOportunidad, $payloadCuadroPresupuestoFinalizado, Auth::user()->nombre_corto));
                        /**
                         * ENVIAR NOTIFICACION DE FINALIZACION
                         */

                        $tipoStatus = "success";
                        $mensaje = "La notificación fue enviada";

                        foreach ($payloadCuadroPresupuestoFinalizado as $pl) { // enviar orde servicio / transformacion a multiples usuarios
                            $transformacion =  Transformacion::select('transformacion.codigo', 'cc.id_oportunidad', 'adm_empresa.logo_empresa')
                                ->leftjoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'transformacion.id_cc')
                                ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'transformacion.id_almacen')
                                ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
                                ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                                ->where('cc.id', $pl['cuadro_presupuesto']->id)
                                ->first();
                            $logoEmpresa = empty($transformacion->logo_empresa) ? null : $transformacion->logo_empresa;
                            $codigoTransformacion = empty($transformacion->codigo) ? null : $transformacion->codigo;
                            Mail::to($correosOrdenServicioTransformacion)->send(new EmailOrdenServicioOrdenTransformacion($pl['oportunidad'], $logoEmpresa, $codigoTransformacion));
                        }
                    } else {
                        $tipoStatus = "warning";
                        $mensaje = "La notificación no puedo ser enviada, debido al estado actual del CDP";
                    }
                }
            } else {
                $mensaje = "No se encontro un id de orden valido";
                $tipoStatus = "error";
            }

            DB::commit();
            return ['tipo_estado' => $tipoStatus, 'mensaje' => $mensaje];
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['tipo_estado' => 'error',  'mensaje' => 'Mensaje de error: ' . $e->getMessage()]);
        }
    }


    public function vincularOcSoftlink(Request $request)
    {
        // $request->idOrden;
        // $request->movId;
        try {
            DB::beginTransaction();


            $orden = Orden::where("id_orden_compra", $request->idOrden)->first();
            $orden->id_softlink = $request->movId;
            $orden->save();

            $arrayRspta = array(
                'tipo_estado' => 'success',
                'mensaje' => 'Se vinculó correctamente la orden ' . $orden->codigo . ' con id ' . $request->movId,
                'ocAgile' => array('cabecera' => $orden),
            );

            DB::commit();

            return response()->json($arrayRspta, 200);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo_estado' => 'error', 'ocAgile' => null, 'mensaje' => 'Hubo un problema al actualizar la orden. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }


    function obtenerContribuyentePorIdProveedor($idProveedor)
    {
        try {
            DB::beginTransaction();

            $proveedor = Proveedor::find($idProveedor);
            if (!empty($proveedor) && $proveedor->id_proveedor > 0) {
                $contribuyente = Contribuyente::with("tipoDocumentoIdentidad", "cuentaContribuyente.banco.contribuyente", "cuentaContribuyente.tipoCuenta")->find($proveedor->id_contribuyente);
                if (!empty($contribuyente)) {

                    $arrayRspta = array(
                        'tipo_estado' => 'success',
                        'mensaje' => 'Ok',
                        'data' => $contribuyente
                    );
                }
            } else {
                $arrayRspta = array(
                    'tipo_estado' => 'warning',
                    'mensaje' => 'no se pudo obtener la data de contribuyente segun el id proveedor(' . $idProveedor . ') enviado',
                    'data' => []
                );
            }


            DB::commit();

            return response()->json($arrayRspta, 200);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo_estado' => 'error', 'data' => [], 'mensaje' => 'Hubo un problema al intentar obtener la data del contribuyente. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

    function registrarSolicitudDePagar(Request $request)
    {
        // return $request;exit;
        try {
            DB::beginTransaction();

            $orden = Orden::with('detalle.detalleRequerimiento.requerimiento')->find($request->id_orden_compra);

            if (!empty($orden)) {
                //ya fue autorizado?
                // Debugbar::info(isset($request->pagoEnCuotasCheckbox));
                if (intval($orden->estado_pago) !== 5) {
                    //ya fue pagado?
                    if (intval($orden->estado_pago) !== 6) {
                        if (isset($request->pagoEnCuotasCheckbox) == true) {
                            $registoSolicitudPagoEnCuotas = $this->registrarSolicitudDePagarEnCuotas($request);
                            $arrayRspta = array(
                                'tipo_estado' => $registoSolicitudPagoEnCuotas['tipo_estado'],
                                'mensaje' => $registoSolicitudPagoEnCuotas['mensaje'],
                                'data' => $registoSolicitudPagoEnCuotas['data']
                            );
                        } else {
                            $registoSolicitudPagoDirecta = $this->registrarSolicitudDePagarDirecta($request);
                            $arrayRspta = array(
                                'tipo_estado' => $registoSolicitudPagoDirecta['tipo_estado'],
                                'mensaje' => $registoSolicitudPagoDirecta['mensaje'],
                                'data' => $registoSolicitudPagoDirecta['data']
                            );

                        }

                    } else {
                        $arrayRspta = array(
                            'tipo_estado' => 'warning',
                            'mensaje' => 'Ésta orden ya fue pagada.',
                            'data' => $orden
                        );
                    }
                } else {
                    $arrayRspta = array(
                        'tipo_estado' => 'warning',
                        'mensaje' => 'Ésta orden ya fue autorizada.',
                        'data' => $orden
                    );
                }

                //actualiar tipo impuesto en los requerimientos
                if($request->tipo_impuesto >=0){                    
        
                    if(count($orden->detalle)>0){
                        foreach (($orden->detalle) as $detalleOrden) {
                            if($detalleOrden->estado != 7 && $detalleOrden->id_detalle_requerimiento >0){
                                if($detalleOrden->detalleRequerimiento !=null && $detalleOrden->detalleRequerimiento->requerimiento->tipo_impuesto >0){
                                    if($detalleOrden->detalleRequerimiento->requerimiento->id_requerimiento > 0){
                                        $requerimiento = Requerimiento::find($detalleOrden->detalleRequerimiento->requerimiento->id_requerimiento);
                                        if($requerimiento->tipo_impuesto != $request->tipo_impuesto){

                                            $anteriorValor = $requerimiento->tipo_impuesto;
                                            $nuevoValor = $request->tipo_impuesto;

                                            $requerimiento->tipo_impuesto = $request->tipo_impuesto!=''?$request->tipo_impuesto:null;
                                            $requerimiento->save();

                                            $comentario = 'Tipo de cuenta: '.($request->tipo_impuesto==1?'Detracción':($request->tipo_impuesto==2?'Renta':'No aplica')).', Agregado por: '.Auth::user()->nombre_corto;

                                            LogActividad::registrar(Auth::user(), 'Enviar a pago', 3, $requerimiento->getTable(), $anteriorValor, $nuevoValor, $comentario);

                                        }

                                    }
                                }
                            }
                        }
                    }
                }

            } else {
                $arrayRspta = array(
                    'tipo_estado' => 'warning',
                    'mensaje' => 'No se encontro la orden',
                    'data' => []
                );
            }

            DB::commit();

            return response()->json($arrayRspta, 200);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo_estado' => 'error', 'data' => [], 'mensaje' => 'Hubo un problema al intentar obtener la data del contribuyente. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

    public function registrarSolicitudDePagarEnCuotas(Request $request)
    {
        // try {
        //     DB::beginTransaction();
            $orden = Orden::find($request->id_orden_compra);

            // validar cantidad de cuotas vs monto total orden, si se completo el numero de cuotas enviar mensaje
            $sumaPagos = 0;
            $lastPagoCuota = PagoCuota::where([['id_orden', $orden->id_orden_compra]])->first();
            if(isset($lastPagoCuota->id_pago_cuota)==true){
                $lastPagoCuotaDetallePagadas = PagoCuotaDetalle::where([['id_pago_cuota', $lastPagoCuota->id_pago_cuota], ['id_estado', '=', 6]])->get();
                foreach ($lastPagoCuotaDetallePagadas as $key => $detCuota) {
                    $sumaPagos += $detCuota->monto_cuota;
                }

            }
            if (floatval($orden->monto_total) > floatval($sumaPagos)) {


                $orden->estado_pago = 8; //enviado a pago
                $orden->id_tipo_destinatario_pago = $request->id_tipo_destinatario;
                $orden->id_prioridad_pago = $request->id_prioridad;

                if( $request->id_tipo_destinatario == 2){
                    $orden->id_cta_principal = $request->id_cuenta;
                }elseif( $request->id_tipo_destinatario == 1){
                    $orden->id_cuenta_persona_pago = $request->id_cuenta;
                }
                $orden->id_persona_pago = $request->id_persona;
                $orden->comentario_pago = $request->comentario;
                $orden->tiene_pago_en_cuotas = $request->pagoEnCuotasCheckbox;
                $orden->fecha_solicitud_pago = Carbon::now();
                $orden->tipo_impuesto = $request->tipo_impuesto >0?$request->tipo_impuesto:null;
                $orden->save();

                if ((isset($request->pagoEnCuotasCheckbox) == true)) {
                    $findPagoCuota = PagoCuota::where('id_orden', $request->id_orden_compra)->first();
                    if (empty($findPagoCuota) == false && ($findPagoCuota) !=null) {
                        $pagoCuotaDetalle = new PagoCuotaDetalle();
                        $pagoCuotaDetalle->id_pago_cuota = $findPagoCuota->id_pago_cuota;
                        $pagoCuotaDetalle->monto_cuota = str_replace(',', '', $request->monto_a_pagar);
                        $pagoCuotaDetalle->observacion = $request->comentario;
                        $pagoCuotaDetalle->id_usuario = Auth::user()->id_usuario;
                        $pagoCuotaDetalle->fecha_registro = new Carbon();
                        $pagoCuotaDetalle->id_estado = 1;
                        $pagoCuotaDetalle->save();
                    } else {
                        $pagoCuota = new PagoCuota();
                        $pagoCuota->id_orden = $request->id_orden_compra;
                        $pagoCuota->numero_de_cuotas = $request->numero_de_cuotas;
                        $pagoCuota->id_estado = 1;
                        $pagoCuota->fecha_registro = new Carbon();
                        $pagoCuota->save();

                        $pagoCuotaDetalle = new PagoCuotaDetalle();
                        $pagoCuotaDetalle->id_pago_cuota = $pagoCuota->id_pago_cuota;
                        $pagoCuotaDetalle->monto_cuota = str_replace(',', '', $request->monto_a_pagar);
                        $pagoCuotaDetalle->observacion = $request->comentario;
                        $pagoCuotaDetalle->id_usuario = Auth::user()->id_usuario;
                        $pagoCuotaDetalle->fecha_registro = new Carbon();
                        $pagoCuotaDetalle->id_estado = 1;
                        $pagoCuotaDetalle->save();
                    }
                }
                if(isset($request->archivoAdjuntoRequerimientoObject)){
                    $ObjectoAdjunto = json_decode($request->archivoAdjuntoRequerimientoObject);
                    $adjuntoOtrosAdjuntosLength = $request->archivo_adjunto_list != null ? count($request->archivo_adjunto_list) : 0;
                    $idOrden = $request->id_orden_compra;
                    $codigoOrden = Orden::find($request->id_orden_compra)->codigo;
                    $archivoAdjuntoList = $request->archivo_adjunto_list;

                    foreach ($ObjectoAdjunto as $keyObj => $value) {
                        $ObjectoAdjunto[$keyObj]->id_orden = $idOrden;
                        $ObjectoAdjunto[$keyObj]->codigo_orden = $codigoOrden;
                        $ObjectoAdjunto[$keyObj]->id_pago_cuota_detalle = $pagoCuotaDetalle->id_pago_cuota_detalle;
                        if ($adjuntoOtrosAdjuntosLength > 0) {
                            foreach ($archivoAdjuntoList as $keyA => $archivo) {
                                if (is_file($archivo)) {
                                    $nombreArchivoAdjunto = $archivo->getClientOriginalName();
                                    if ($nombreArchivoAdjunto == $value->nameFile) {
                                        $ObjectoAdjunto[$keyObj]->file = $archivo;
                                    }
                                }
                            }
                        }
                    }
                    $idAdjunto = [];
                    if ($adjuntoOtrosAdjuntosLength > 0) {

                        $idAdjunto[] = $this->subirYRegistrarArchivoLogistico($ObjectoAdjunto);
                    }

                }
                $arrayRspta = array(
                    'tipo_estado' => 'success',
                    'mensaje' => 'Solicitud de pago registrado.',
                    'data' => $orden
                );
            } else {
                $arrayRspta = array(
                    'tipo_estado' => 'warning',
                    'mensaje' => 'Se completo el limite total de cuotas, orden pagada.',
                    'data' => $orden
                );
            }
            // DB::commit();

            return $arrayRspta;
        // } catch (Exception $e) {

        //     $arrayRspta = array(
        //         'tipo_estado' => 'error',
        //         'mensaje' => 'Hubo un problema al intentar registrar de solicitud de pago en cuotas',
        //         'data' => []
        //     );

        //     return $arrayRspta;
        // }
    }
    public function registrarSolicitudDePagarDirecta(Request $request)
    {
        // try {
        //     DB::beginTransaction();
            $orden = Orden::find($request->id_orden_compra);

            $orden->estado_pago = 8; //enviado a pago
            $orden->id_tipo_destinatario_pago = $request->id_tipo_destinatario;
            $orden->id_prioridad_pago = $request->id_prioridad;
            if( $request->id_tipo_destinatario == 2){
                $orden->id_cta_principal = $request->id_cuenta;
            }elseif( $request->id_tipo_destinatario == 1){
                $orden->id_cuenta_persona_pago = $request->id_cuenta;
            }
            $orden->id_persona_pago = $request->id_persona;
            $orden->comentario_pago = $request->comentario;
            $orden->tiene_pago_en_cuotas = false;
            $orden->fecha_solicitud_pago = Carbon::now();
            $orden->tipo_impuesto = $request->tipo_impuesto >0?$request->tipo_impuesto:null;
            $orden->save();


            if(isset($request->archivoAdjuntoRequerimientoObject)){
                $ObjectoAdjunto = json_decode($request->archivoAdjuntoRequerimientoObject);
                $adjuntoOtrosAdjuntosLength = $request->archivo_adjunto_list != null ? count($request->archivo_adjunto_list) : 0;
                $idOrden = $request->id_orden_compra;
                $codigoOrden = Orden::find($request->id_orden_compra)->codigo;
                $archivoAdjuntoList = $request->archivo_adjunto_list;

                foreach ($ObjectoAdjunto as $keyObj => $value) {
                    $ObjectoAdjunto[$keyObj]->id_orden = $idOrden;
                    $ObjectoAdjunto[$keyObj]->codigo_orden = $codigoOrden;
                    $ObjectoAdjunto[$keyObj]->id_pago_cuota_detalle = null;
                    if ($adjuntoOtrosAdjuntosLength > 0) {
                        foreach ($archivoAdjuntoList as $keyA => $archivo) {
                            if (is_file($archivo)) {
                                $nombreArchivoAdjunto = $archivo->getClientOriginalName();
                                if ($nombreArchivoAdjunto == $value->nameFile) {
                                    $ObjectoAdjunto[$keyObj]->file = $archivo;
                                }
                            }
                        }
                    }
                }
                $idAdjunto = [];
                if ($adjuntoOtrosAdjuntosLength > 0) {

                    $idAdjunto[] = $this->subirYRegistrarArchivoLogistico($ObjectoAdjunto);
                }

            }



            $arrayRspta = array(
                'tipo_estado' => 'success',
                'mensaje' => 'Solicitud de pago registrado.',
                'data' => $orden
            );

            // DB::commit();
            return $arrayRspta;
        // } catch (Exception $e) {

        //     $arrayRspta = array(
        //         'tipo_estado' => 'error',
        //         'mensaje' => 'Hubo un problema al intentar registrar de solicitud de pago',
        //         'data' => []
        //     );

        //     return $arrayRspta;
        // }
    }

    function ObtenerHistorialDeEnviosAPagoEnCuotas($idOrden)
    {
        return PagoCuota::with(['detalle' => function ($query) {
            $query->orderBy('fecha_registro', 'asc');
        }, 'detalle.creadoPor', 'detalle.adjuntos.moneda'])->where('id_orden', $idOrden)->first();
    }

    function obtenerContribuyente($idContribuyente)
    {
        return  Contribuyente::with("tipoDocumentoIdentidad", "cuentaContribuyente.banco.contribuyente", "cuentaContribuyente.tipoCuenta")->find($idContribuyente);
    }
    function obtenerPersona($idPersona)
    {
        return  Persona::with("tipoDocumentoIdentidad", "cuentaPersona.banco.contribuyente", "cuentaPersona.tipoCuenta", "cuentaPersona.moneda")->find($idPersona);
    }



    public static function reporteListaOrdenes($filtro='SIN_FILTRO')
    {


        $data = [];
        $ord_compra = OrdenesView::where('id_estado', '>=', 1)
            ->when(($filtro!='SIN_FILTRO'), function ($query) use ($filtro) {
                switch ($filtro) {
                    case 'ORDENES_SIN_ENVIAR_A_PAGO':
                        return $query->whereIn('ordenes_view.estado_pago',[1]);
                        break;
                    case 'ORDENES_AUTORIZADAS_PARA_PAGO':
                        return $query->whereIn('ordenes_view.estado_pago', [5]);
                        break;
                    default:
                        # code...
                        break;
                }
            })
            ->orderBy('ordenes_view.fecha_emision', 'desc')
            ->get();

        foreach ($ord_compra as $d) {

            $data[] = [
                'codigo' => $d['codigo'] ?? '',
                'tipo_orden' => $d['descripcion_tp_documento'] ?? '',
                'codigo_softlink' => $d['codigo_softlink'] ?? '',
                'codigo_requerimiento' => $d['data_codigo_requerimiento'] ?? '',
                'codigo_cdp' => $d['data_codigo_oportunidad'] ?? '',
                'descripcion_sede_empresa' => $d['descripcion_sede_empresa'] ?? '',
                'descripcion_moneda' => $d['descripcion_moneda'] ?? '',
                'fecha_emision' => $d['fecha_emision'] ?? '',
                'fecha_llegada' => $d['fecha_llegada'] ?? '',
                'tiempo_atencion_logistica' => $d['data_atencion_logistica'] ?? '',
                'fecha_ultimo_ingreso_almacen' => $d['fecha_ultimo_ingreso_almacen'] ?? '',
                'razon_social_proveedor' => $d['razon_social_proveedor'] ?? '',
                'condicion_compra'=>$d['condicion_compra'] ?? '',
                'credito_dias'=>$d['credito_dias'] ?? '',
                'estado_orden' => $d['descripcion_estado'] ?? '',
                'estado_pago' => $d['descripcion_estado_pago'] ?? '-',
                'monto_total' => $d['monto_total'] ?? '',
                'monto_total_cdp' =>  $d['data_importe_oportunidad'] ?? ''
            ];
        }

        return $data;
    }
    public static function reporteListaItemsOrdenes()
    {
        $data = [];
        $det_ord_compra = ItemsOrdenesView::where('id_estado', '>=', 1)
            ->orderBy('items_ordenes_view.fecha_emision', 'desc')
            ->get();

        foreach ($det_ord_compra as $key => $d) {
            $data[] = [
                'codigo_orden' => $d['codigo_orden'] ?? '',
                'codigo_requerimiento' => $d['codigo_requerimiento'] ?? '',
                'codigo_softlink' => $d['codigo_softlink'] ?? '',
                'nro_orden_mgc'=> $d['nro_orden_mgc']??'',
                'concepto_requerimiento' => $d['concepto_requerimiento'] ?? '',
                'razon_social_cliente' => $d['razon_social_cliente'] ?? '',
                'razon_social_proveedor' => $d['razon_social_proveedor'] ?? '',
                'codigo_am'=> $d['codigo_am']??'',
                'nombre_am'=> $d['nombre_am']??'',
                'descripcion_subcategoria' => $d['descripcion_subcategoria'] ?? '',
                'descripcion_categoria' => $d['descripcion_categoria'] ?? '',
                'codigo_producto' => $d['codigo_producto'] ?? '',
                'part_number_producto' => $d['part_number_producto'] ?? '',
                'cod_softlink_producto' => $d['cod_softlink_producto'] ?? '',
                'descripcion_producto' => $d['descripcion_producto'] ? $d['descripcion_producto'] : $d['descripcion_adicional'],
                'lugar_entrega_cdp'=> $d['lugar_entrega_cdp']??'',
                'cantidad' => $d['cantidad'] ?? '',
                'abreviatura_unidad_medida_producto' => $d['abreviatura_unidad_medida_producto'] ?? ($d['abreviatura_unidad_medida_det_orden']??'') ,
                'simbolo_moneda_orden' => $d['simbolo_moneda_orden'] ?? '',
                'precio' => $d['precio'] ?? '',
                'cc_fila_precio' => $d['cc_fila_precio'] ?? '',
                'fecha_emision' => $d['fecha_emision'] ?? '',
                'fecha_llegada' => $d['fecha_llegada'] ?? '',
                'fecha_ingreso_almacen' => $d['fecha_ingreso_almacen'] ?? '',
                'tiempo_atencion_proveedor' => $d['tiempo_atencion_proveedor'] ?? '',
                'descripcion_sede_empresa' => $d['descripcion_sede_empresa'] ?? '',
                'descripcion_estado' => $d['descripcion_estado'] ?? ''


            ];
        }


        return $data;
    }

    // static public function notificarFinalizacion(){
    //     return CuadroPresupuestoHelper::notificarFinalizacion();
    // }
    public function listarCategoriasAdjuntos()
    {
        // $categoria_adjunto =
        return DB::table('logistica.categoria_adjunto')->where('estado',1)
            ->get();
    }
    public function guardarAdjuntoOrden(Request $request)
    {
        # code...
        DB::beginTransaction();
        try {


            $ObjectoAdjunto = json_decode($request->archivoAdjuntoRequerimientoObject);
            $adjuntoOtrosAdjuntosLength = $request->archivo_adjunto_list != null ? count($request->archivo_adjunto_list) : 0;
            $idOrden = $request->id_orden;
            $codigoOrden = $request->codigo_orden;
            // $idAdjuntoList = $request->id_adjunto;
            $archivoAdjuntoList = $request->archivo_adjunto_list;

            // $nombreRealAdjunto = $request->nombre_real_adjunto;
            // $fechaEmisionAdjuntoList = $request->fecha_emision_adjunto;
            // $nroComprobanteAdjuntoList = $request->nro_comprobante_adjunto;
            // $idCategoriaAdjuntoList = $request->categoria_adjunto;
            // $accionAdjunto = $request->accion;
            // $estadoAdjunto = 1;
            // $adjuntoComprobanteContableLength = $request->archivoAdjuntoRequerimientoCabeceraFileGuardar3 != null ? count($request->archivoAdjuntoRequerimientoCabeceraFileGuardar3) : 0;
            // $adjuntoComprobanteBancarioLength = $request->archivoAdjuntoRequerimientoCabeceraFileGuardar4 != null ? count($request->archivoAdjuntoRequerimientoCabeceraFileGuardar4) : 0;


            foreach ($ObjectoAdjunto as $keyObj => $value) {
                $ObjectoAdjunto[$keyObj]->id_orden = $idOrden;
                $ObjectoAdjunto[$keyObj]->codigo_orden = $codigoOrden;
                if ($adjuntoOtrosAdjuntosLength > 0) {
                    foreach ($archivoAdjuntoList as $keyA => $archivo) {
                        if (is_file($archivo)) {
                            $nombreArchivoAdjunto = $archivo->getClientOriginalName();
                            if ($nombreArchivoAdjunto == $value->nameFile) {
                                $ObjectoAdjunto[$keyObj]->file = $archivo;
                            }
                        }
                    }
                }
            }
            // Debugbar::info($ObjectoAdjunto);

            // Debugbar::info($idOrden,$codigoOrden,$idAdjuntoList,$archivoAdjuntoList,$fechaEmisionAdjuntoList,$nroComprobanteAdjuntoList,$idCategoriaAdjuntoList,$accionAdjunto);

            $idAdjunto = [];
            if ($adjuntoOtrosAdjuntosLength > 0) {

                $idAdjunto[] = $this->subirYRegistrarArchivoLogistico($ObjectoAdjunto);
            }
            $estado_accion = 'error';
            if (count($idAdjunto) > 0) {
                $mensaje = 'Adjuntos guardados';
                $estado_accion = 'success';
            } else {
                $mensaje = 'Hubo un problema y no se pudo guardo los adjuntos' . count($idAdjunto);
                $estado_accion = 'warning';
            }
            DB::commit();

            return response()->json(['status' => $estado_accion, 'mensaje' => $mensaje]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'mensaje' => 'Hubo un problema al guardar los adjuntos. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

    function subirYRegistrarArchivoLogistico($ObjectoAdjunto)
    {

        $idList = [];
        foreach ($ObjectoAdjunto as $key => $archivo) {
            // if ($archivo != null) {

            if ($archivo->accion == "GUARDAR") {
                $fechaHoy = new Carbon();
                $sufijo = $fechaHoy->format('YmdHis');
                $file = ($archivo->file)->getClientOriginalName();

                $codigo = $archivo->codigo_orden;
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $newNameFile = $codigo . '_' . $key . $archivo->category . $sufijo . '.' . $extension;
                Storage::disk('archivos')->put("logistica/comporbantes_proveedor/" . $newNameFile, File::get($archivo->file));
                // if (preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $id)){
                // }
                $idAdjunto = DB::table('logistica.adjuntos_logisticos')->insertGetId(
                    [
                        'id_orden'              => intval($archivo->id_orden),
                        'archivo'               => $newNameFile,
                        'estado'                => 1,
                        'categoria_adjunto_id'  => intval($archivo->category),
                        'fecha_emision'         => $archivo->fecha_emision,
                        'nro_comprobante'       => $archivo->nro_comprobante,
                        'id_moneda'             => intval($archivo->id_moneda),
                        'monto_total'           => floatval($archivo->monto_total),
                        'id_pago_cuota_detalle' => (isset($archivo->id_pago_cuota_detalle) && $archivo->id_pago_cuota_detalle > 0 ? $archivo->id_pago_cuota_detalle : null),
                        'id_usuario'            => Auth::user()->id_usuario,
                        'fecha_registro'        => $fechaHoy
                    ],
                    'id_adjunto'
                );

                $idList[] = $idAdjunto;
            } elseif ($archivo->accion == "ACTUALIZAR") {

                $idAdjunto = DB::table('logistica.adjuntos_logisticos')
                    ->where('id_adjunto', $archivo->id)
                    ->update(
                        [
                            'estado'                => 1,
                            'categoria_adjunto_id'  => $archivo->category,
                            'fecha_emision'         => $archivo->fecha_emision,
                            'nro_comprobante'       => $archivo->nro_comprobante
                        ]
                    );

                $idList[] = $idAdjunto;
            } elseif ($archivo->accion == "ANULAR") {

                $idAdjunto = DB::table('logistica.adjuntos_logisticos')
                    ->where('id_adjunto', $archivo->id)
                    ->update(
                        [
                            'estado' => 7
                        ]
                    );
                $idList[] = $idAdjunto;
            }

            // }
        }

        return $idList;
    }
    public function listarArchivosOrder($idOrden)
    {
        return DB::table('logistica.adjuntos_logisticos')
            ->select('adjuntos_logisticos.*', 'categoria_adjunto.descripcion as descripcion_categoria_adjunto', 'sis_moneda.simbolo as simbolo_moneda')
            ->leftjoin('logistica.categoria_adjunto', 'adjuntos_logisticos.categoria_adjunto_id', '=', 'categoria_adjunto.id_categoria_adjunto')
            ->leftjoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'adjuntos_logisticos.id_moneda')
            ->where([['adjuntos_logisticos.id_orden', $idOrden], ['adjuntos_logisticos.estado', 1]])
            ->get();
    }

    public function listarArchivoAdjuntoPagoRequerimiento($idOrden)
    {
        // $ordenDetalle = OrdenCompraDetalle::where([['id_orden_compra',$idOrden],["estado", "!=", 7]]);

        // $idDetalleRequerimientoList=[];
        // foreach ($ordenDetalle as $key => $det) {
        //     $idDetalleRequerimientoList[]=$det->id_detalle_requerimiento;
        // }

        // $idOrdenList = [];
        // $idRegistroPagoList = [];
        $mensaje = '';
        $output = [];


        // if (count($idDetalleRequerimientoList) > 0) {
        // $detalleYOrdenList = OrdenCompraDetalle::with('orden')->whereIn('id_detalle_requerimiento', $idDetalleRequerimientoList)->get();
        // foreach ($detalleYOrdenList as $dyo) {
        //     $idOrdenList[] = $dyo->id_orden_compra;
        // }
        if ($idOrden > 0) {
            $dataOrden = DB::table('logistica.log_ord_compra')->select(
                'log_ord_compra.id_orden_compra',
                'log_ord_compra.codigo AS codigo_orden',
                'registro_pago_adjuntos.adjunto'

            )
                ->leftJoin('tesoreria.registro_pago', 'registro_pago.id_oc', '=', 'log_ord_compra.id_orden_compra')
                ->leftJoin('tesoreria.registro_pago_adjuntos', 'registro_pago_adjuntos.id_pago', '=', 'registro_pago.id_pago')
                ->where([['log_ord_compra.id_orden_compra', $idOrden], ['log_ord_compra.estado', '!=', 7], ['registro_pago.estado', '!=', 7], ['registro_pago_adjuntos.estado', '!=', 7]])
                // ->whereIn('log_ord_compra.id_orden_compra', $idOrdenList)
                ->get();
            $tempIdAgregadoAData = [];
            foreach ($dataOrden as $do) {
                if (!in_array($do->id_orden_compra, $tempIdAgregadoAData)) {
                    $output[] = array('id_orden' => $do->id_orden_compra, 'codigo_orden' => $do->codigo_orden, 'adjuntos' => []);
                    $tempIdAgregadoAData[] = $do->id_orden_compra;
                }
            }

            foreach ($dataOrden as $keyDo => $do) {
                foreach ($output as $keyOp => $op) {
                    if ($do->id_orden_compra == $op['id_orden']) {
                        $output[$keyOp]['adjuntos'][] = $do->adjunto;
                    }
                }
            }
        } else {
            $mensaje = 'Sin ordenes';
        }
        // } else {
        //     $mensaje = 'Sin detalle requerimiento';
        // }

        return ["data" => $output, "mensaje" => $mensaje];
    }


    public function calcularPrioridad($id_orden)
    {
        $detalle_orden = OrdenCompraDetalle::select('alm_req.*')
        ->where('log_det_ord_compra.estado','!=',7)
        ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
        ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
        ->where('log_det_ord_compra.id_orden_compra',$id_orden)
        ->get();

        $prioridad_mayor=0;
        $prioridad_menor=0;
        foreach($detalle_orden as $key => $value) {

            if($value->id_prioridad > $prioridad_mayor){
                $prioridad_mayor = $value->id_prioridad;
                $prioridad_menor = $prioridad_mayor;
            }
        }
        return response()->json([
            "success"=>true,
            "prioridad_id"=>$prioridad_mayor,
            // "requerimientos"=>$detalle_orden,
        ],200);
    }

    public function obtenerRequerimientosConImpuesto($idOrden){

        $mensaje='';
        $estado='info';

        $requerimientoList=[];
        $tipoImpuestoList=[];
        $payload=[];

        $orden = Orden::with('detalle.detalleRequerimiento.requerimiento')->find($idOrden);
        
        if(count($orden->detalle)>0){
            foreach (($orden->detalle) as $detalleOrden) {
                if($detalleOrden->estado != 7 && $detalleOrden->id_detalle_requerimiento >0){
                    if($detalleOrden->detalleRequerimiento !=null && $detalleOrden->detalleRequerimiento->requerimiento->tipo_impuesto >0){

                        $requerimientoList[]=['id_requerimiento'=>$detalleOrden->detalleRequerimiento->requerimiento->id_requerimiento, 'codigo'=>$detalleOrden->detalleRequerimiento->requerimiento->codigo]; 
                        $tipoImpuestoList[]=$detalleOrden->detalleRequerimiento->requerimiento->tipo_impuesto; 
                    }
                }
            }
        }

    
        $payload['lista_requerimientos']= array_unique($requerimientoList);

        if(count($tipoImpuestoList) ==1){
            
        $tipoImpuesto= $tipoImpuestoList[0];
        $estado='success';

        }else{
            $tipoImpuesto='';
            $mensaje = 'Debe seleccionar el tipo de impuesto, existe requerimientos con distinto tipo de impuesto ';
            $estado='warning';
        }

        $payload['tipo_impuesto']= $tipoImpuesto;

        return ["data" => $payload, 'estado'=>$estado, "mensaje" => $mensaje];

    }
}
