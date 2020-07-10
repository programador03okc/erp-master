<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

// use Mail;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

use Dompdf\Dompdf;
use PDF;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Models\Logistica\Empresa;
use App\Models\Tesoreria\Usuario;
use App\Models\Tesoreria\Grupo;
use DataTables;
// use Debugbar;

date_default_timezone_set('America/Lima');

class LogisticaController extends Controller
{

    function view_lista_requerimientos()
    {
        $roles = $this->userSession()['roles'];
        $empresas = $this->select_mostrar_empresas();

        return view('logistica/requerimientos/lista_requerimientos', compact('roles','empresas'));
    }

    function view_gestionar_requerimiento()
    {
        $monedas = $this->mostrar_moneda();
        $prioridades = $this->mostrar_prioridad();
        $tipo_requerimiento = $this->mostrar_tipo();
        $empresas = Empresa::all();
        $areas = $this->mostrar_area();
        $unidades_medida = $this->mostrar_unidad_medida();
        $periodos = $this->mostrar_periodos();
        $roles = $this->userSession()['roles'];
        $sis_identidad = $this->select_sis_identidad();

        return view('logistica/requerimientos/gestionar_requerimiento', compact('sis_identidad','tipo_requerimiento','monedas', 'prioridades', 'empresas', 'unidades_medida','roles','periodos'));
    }

    function view_gestionar_cotizaciones()
    {
        $tp_contribuyente = $this->select_tp_contribuyente();
        $sis_identidad = $this->select_sis_identidad();
        $empresas = $this->select_mostrar_empresas();
        return view('logistica/cotizaciones/gestionar_cotizaciones', compact('empresas', 'tp_contribuyente', 'sis_identidad'));
    }

    function view_valoriacion()
    {
        $unidades_medida = $this->mostrar_unidad_medida();
        $condiciones = $this->select_condiciones();
        $empresas = $this->select_mostrar_empresas();

        return view('logistica/cotizaciones/gestionar_valorizacion', compact('condiciones','unidades_medida','empresas'));
    }
    function view_cuadro_comparativo()
    {
        $unidades_medida = $this->mostrar_unidad_medida();
        $condiciones = $this->select_condiciones();

        return view('logistica/cotizaciones/cuadro_comparativo', compact('condiciones','unidades_medida'));
    }

    function view_generar_orden()
    {
        $condiciones = $this->select_condiciones();
        $tp_doc = $this->select_tp_doc();
        $bancos = $this->select_bancos();
        $cuentas = $this->select_tipos_cuenta();
        $responsables = $this->select_responsables();
        $contactos = $this->select_contacto();
        $tp_moneda = $this->select_moneda();
        $tp_documento = $this->select_documento();
        $sis_identidad = $this->select_sis_identidad();
        $sedes = $this->select_sedes();

        return view('logistica/ordenes/generar_orden', compact('sedes','sis_identidad','condiciones', 'tp_doc', 'bancos', 'cuentas','contactos', 'responsables', 'tp_moneda','tp_documento'));
    }
    function view_generar_orden_requerimiento()
    {
        // $condiciones = $this->select_condiciones();
        // $tp_doc = $this->select_tp_doc();
        // $bancos = $this->select_bancos();
        // $cuentas = $this->select_tipos_cuenta();
        // $responsables = $this->select_responsables();
        // $contactos = $this->select_contacto();
        // $tp_moneda = $this->select_moneda();
        $tp_documento = $this->select_documento();
        $sis_identidad = $this->select_sis_identidad();
        // $sedes = $this->select_sedes();
        $sedes = Auth::user()->sedesAcceso();

        return view('logistica/ordenes/generar_orden_requerimiento', compact('sedes','sis_identidad','tp_documento'));
    }

    function view_lista_proveedores()
    {
        $estado_ruc = $this->estado_ruc();
        $condicion_ruc = $this->condicion_ruc();
        $tipo_contribuyente = $this->tipo_contribuyente();
        $paises = $this->paises();
        $tipo_establecimiento = $this->tipo_establecimiento();
        $bancos = $this->select_bancos();
        $tipo_cuenta_banco = $this->select_tipos_cuenta();
        // $contactos = $this->contacto_list();

        return view('logistica/proveedores/gestionar_proveedores',compact('estado_ruc','condicion_ruc','tipo_contribuyente','paises','tipo_establecimiento','bancos','tipo_cuenta_banco'));
    }
    function view_listar_ordenes()
    {
        return view('logistica/ordenes/listar_ordenes');
    }

    function sedesAcceso($id_empresa){
        $id_usuario = Auth::user()->id_usuario;
        $sedes = DB::table('configuracion.sis_usua_sede')
		->select('sis_sede.*','ubi_dis.descripcion as ubigeo_descripcion')
        ->join('administracion.sis_sede','sis_sede.id_sede','=','sis_usua_sede.id_sede')
        ->leftJoin('configuracion.ubi_dis','ubi_dis.id_dis','=','sis_sede.id_ubigeo')
        ->where([['sis_usua_sede.id_usuario','=',$id_usuario],
                 ['sis_sede.id_empresa','=',$id_empresa]])
		->get();
        return $sedes;
    }
    function listar_proveedores(){
        $output['data']=[];
        $prov = DB::table('contabilidad.adm_contri')
            ->select(
                'log_prove.id_proveedor',
                'adm_contri.*',
                'estado_ruc.descripcion as descripcion_estado_ruc',
                'adm_tp_contri.descripcion as tipo_contribuyente',
                DB::raw("(sis_identi.descripcion) || ' ' || (adm_contri.nro_documento) AS documento"),
                DB::raw("(CASE WHEN adm_contri.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_contri")

            )
            ->leftJoin('contabilidad.adm_tp_contri', 'adm_tp_contri.id_tipo_contribuyente', '=', 'adm_contri.id_tipo_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->leftJoin('contabilidad.estado_ruc', 'estado_ruc.id_estado_ruc', '=', 'adm_contri.id_estado_ruc')
            ->join('logistica.log_prove', 'log_prove.id_contribuyente', '=', 'adm_contri.id_contribuyente')

            ->where([['adm_contri.estado', '=', 1]])
            ->orderBy('adm_contri.id_contribuyente', 'asc')->get();

            foreach($prov as $data){
                $id_proveedor = $data->id_proveedor;
                $id_contribuyente = $data->id_contribuyente;
                $razon_social= $data->razon_social;
                $documento = $data->documento;
                $tipo_contribuyente = $data->tipo_contribuyente;
                $telefono = $data->telefono;
                $direccion_fiscal = $data->direccion_fiscal;
                $estado = $data->descripcion_estado_ruc;
                $output['data'][] = array($id_proveedor, $razon_social, $documento, $tipo_contribuyente, $telefono, $direccion_fiscal, $estado);


            }    
            return response()->json($output);
    }
    

    public function estado_ruc()
    {
        $data = DB::table('contabilidad.estado_ruc')
            ->select('estado_ruc.id_estado_ruc', 'estado_ruc.descripcion')
            ->where('estado_ruc.estado', 1)
            ->orderBy('estado_ruc.id_estado_ruc')
            ->get();
        return $data;
    }

    public function condicion_ruc()
    {
        $data = DB::table('contabilidad.condicion_ruc')
            ->select('condicion_ruc.id_condicion_ruc', 'condicion_ruc.descripcion')
            ->where('condicion_ruc.estado', 1)
            ->orderBy('condicion_ruc.id_condicion_ruc')
            ->get();
        return $data;
    }

    public function tipo_contribuyente()
    {
        $data = DB::table('contabilidad.adm_tp_contri')
            ->select('adm_tp_contri.id_tipo_contribuyente', 'adm_tp_contri.descripcion')
            ->where('adm_tp_contri.estado', 1)
            ->orderBy('adm_tp_contri.id_tipo_contribuyente')
            ->get();
        return $data;
    }
    public function paises()
    {
        $data = DB::table('configuracion.sis_pais')
            ->select('sis_pais.id_pais', 'sis_pais.descripcion')
            ->where('sis_pais.estado', 1)
            ->orderBy('sis_pais.id_pais')
            ->get();
        return $data;
    }
    public function tipo_establecimiento()
    {
        $data = DB::table('contabilidad.tipo_establecimiento')
            ->select('tipo_establecimiento.id_tipo_establecimiento', 'tipo_establecimiento.descripcion')
            ->where('tipo_establecimiento.estado', 1)
            ->orderBy('tipo_establecimiento.id_tipo_establecimiento')
            ->get();
        return $data;
    }
    public function contacto_establecimiento($id_proveedor)
    {
        $sql_contri =DB::table('logistica.log_prove')
        ->select(
            'log_prove.id_contribuyente'
        )
        ->where('id_proveedor',  $id_proveedor)
        ->first();

        $data = DB::table('contabilidad.establecimiento')
            ->select('establecimiento.id_establecimiento', 'establecimiento.direccion', 'tipo_establecimiento.id_tipo_establecimiento','tipo_establecimiento.descripcion as descripcion_tipo_establcimiento')
            ->leftJoin('contabilidad.tipo_establecimiento', 'establecimiento.id_tipo_establecimiento', '=', 'tipo_establecimiento.id_tipo_establecimiento')
            ->where([
                ['establecimiento.estado', 1],
                ['establecimiento.id_contribuyente', $sql_contri->id_contribuyente]
            ])
            ->orderBy('establecimiento.id_establecimiento')
            ->get();
        return $data;
    }
    // public function contacto_list()
    // {
    //     $data = DB::table('contabilidad.adm_ctb_contac')
    //         ->select('adm_ctb_contac.*')
    //         ->where('adm_ctb_contac.estado', 1)
    //         ->orderBy('adm_ctb_contac.id_datos_contacto')
    //         ->get();
    //     return $data;
    // }

    public function select_condiciones()
    {
        $data = DB::table('logistica.log_cdn_pago')
            ->select('log_cdn_pago.id_condicion_pago', 'log_cdn_pago.descripcion')
            ->where('log_cdn_pago.estado', 1)
            ->orderBy('log_cdn_pago.descripcion')
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
    public function select_tp_contribuyente()
    {
        $data = DB::table('contabilidad.adm_tp_contri')
            ->select('adm_tp_contri.id_tipo_contribuyente', 'adm_tp_contri.descripcion')
            ->where('adm_tp_contri.estado', '=', 1)
            ->orderBy('adm_tp_contri.descripcion', 'asc')->get();
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
 
    public function getIdEmpresa(Request $request)
    {
    $nombre_empresa = $request->nombre;
    
        $data = DB::table('administracion.adm_empresa')
            ->select('adm_empresa.id_empresa', 'adm_empresa.logo_empresa','adm_contri.nro_documento', 'adm_contri.razon_social')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->where([
                ['adm_contri.razon_social', '=', $nombre_empresa],
                ['adm_empresa.estado', '=', 1]
            ])
            ->orderBy('adm_contri.razon_social', 'asc')
            ->get();
            $id_empresa=0;
            if(count($data)>0){
                $id_empresa = $data->first()->id_empresa;
            }
        return $id_empresa;
        // return response()->json($id_empresa);

    }
    public function select_mostrar_empresas()
    {
        $data = DB::table('administracion.adm_empresa')
            ->select('adm_empresa.id_empresa', 'adm_empresa.logo_empresa','adm_contri.nro_documento', 'adm_contri.razon_social')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->where('adm_empresa.estado', '=', 1)
            ->orderBy('adm_contri.razon_social', 'asc')
            ->get();
        return $data;
    }
    public function select_bancos()
    {
        $data = DB::table('contabilidad.cont_banco')
            ->select('cont_banco.id_banco', 'adm_contri.razon_social')
            ->join('contabilidad.adm_contri', 'cont_banco.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->where('cont_banco.estado', '=', 1)
            ->orderBy('adm_contri.razon_social', 'asc')
            ->get();
        return $data;
    }
    public function select_tipos_cuenta()
    {
        $data = DB::table('contabilidad.adm_tp_cta')
            ->select('adm_tp_cta.id_tipo_cuenta', 'adm_tp_cta.descripcion')
            ->where('adm_tp_cta.estado', '=', 1)
            ->orderBy('adm_tp_cta.descripcion', 'asc')
            ->get();
        return $data;
    }

    public function select_contacto()
    {
        $data = DB::table('contabilidad.adm_ctb_contac')
            ->select('adm_ctb_contac.*'
            )
            ->orderBy('adm_ctb_contac.id_datos_contacto', 'asc')
            ->get();
        return $data;
    }
    
    public function select_responsables()
    {
        $data = DB::table('configuracion.sis_usua')
            ->select('sis_usua.id_usuario as id_responsable',
            DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno)  AS nombre_responsable ")

            )
            ->leftJoin('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'sis_usua.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->orderBy('sis_usua.id_usuario', 'asc')
            ->get();
        return $data;
    }

    public function select_moneda(){
        $data = DB::table('configuracion.sis_moneda')
            ->select('sis_moneda.id_moneda','sis_moneda.descripcion','sis_moneda.simbolo')
            ->where([
                ['sis_moneda.estado', '=', 1]
            ])
            ->orderBy('sis_moneda.id_moneda', 'asc')
            ->get();
        return $data;
    }
    public function select_documento(){
        $data = DB::table('administracion.adm_tp_docum')
            ->select('adm_tp_docum.id_tp_documento','adm_tp_docum.descripcion','adm_tp_docum.abreviatura')
            ->where([
                ['adm_tp_docum.estado', '=', 1],
                ['adm_tp_docum.descripcion', 'like', '%Orden%']
            ])
            ->orderBy('adm_tp_docum.id_tp_documento', 'asc')
            ->get();
        return $data;
    }



    public function mostrar_requerimientos($option)
    {
        if($option == 'SHOW_ALL'){
            $hasWhere = ['alm_req.estado','>',0];
        }
        if($option == 'ONLY_ACTIVOS'  ){
            $hasWhere = ['alm_req.estado', '!=', 7];

        }

        $alm_req = DB::table('almacen.alm_req')
            ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.id_estado_doc', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            ->leftJoin('proyectos.proy_op_com', 'proy_op_com.id_op_com', '=', 'alm_req.id_op_com')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            // ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_requerimiento', '=', 'alm_req.id_requerimiento')
            // ->leftJoin('logistica.log_ord_compra', 'log_ord_compra.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
            // ->leftJoin('almacen.guia_com_oc', 'guia_com_oc.id_oc', '=', 'log_ord_compra.id_orden_compra')
            ->select(
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.concepto',
                'alm_req.fecha_requerimiento',
                'alm_req.id_tipo_requerimiento',
                'alm_tp_req.descripcion AS tipo_req_desc',
                'sis_usua.usuario',
                'rrhh_rol.id_area',
                'adm_area.descripcion AS area_desc',
                'rrhh_rol.id_rol',
                'rrhh_rol.id_rol_concepto',
                'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
                'alm_req.id_grupo',
                'adm_grupo.descripcion AS adm_grupo_descripcion',
                'alm_req.id_op_com',
                'proy_op_com.codigo as codigo_op_com',
                'proy_op_com.descripcion as descripcion_op_com',
                'alm_req.concepto AS alm_req_concepto',
                // 'log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion',
                'alm_req.id_prioridad',
                'alm_req.fecha_registro',
                'alm_req.estado',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                DB::raw("(CASE WHEN alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
        //         DB::raw("(SELECT  COUNT(log_ord_compra.id_orden_compra) FROM logistica.log_ord_compra
        // WHERE log_ord_compra.id_grupo_cotizacion = log_detalle_grupo_cotizacion.id_grupo_cotizacion)::integer as cantidad_orden"),
        //         DB::raw("(SELECT  COUNT(mov_alm.id_mov_alm) FROM almacen.mov_alm
        // WHERE mov_alm.id_guia_com = guia_com_oc.id_guia_com and 
        // guia_com_oc.id_oc = log_ord_compra.id_orden_compra)::integer as cantidad_entrada_almacen")

            )
            ->where([$hasWhere])
            ->orderBy('alm_req.id_requerimiento', 'desc')
            ->get();
        return response()->json(["data" => $alm_req]);
    }

    public function mostrar_requerimiento($id, $codigo)
    {
        $requerimiento = $this->get_requerimiento($id, $codigo);
        return response()->json($requerimiento);
    }

    public function get_requerimiento($id, $codigo)
    {
        if ($id > 0) {
            $theWhere = ['alm_req.id_requerimiento', '=', $id];
        } else {

            $theWhere = ['alm_req.codigo', '=', $codigo];
        }
        $alm_req = DB::table('almacen.alm_req')
            ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->leftJoin('contabilidad.adm_contri as contrib', 'adm_empresa.id_contribuyente', '=', 'contrib.id_contribuyente')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.id_estado_doc', '=', 'adm_estado_doc.id_estado_doc')
            
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
            ->leftJoin('proyectos.proy_op_com', 'proy_op_com.id_op_com', '=', 'alm_req.id_op_com')
            ->leftJoin('proyectos.proy_presup', 'alm_req.id_presupuesto', '=', 'proy_presup.id_presupuesto')
            ->leftJoin('rrhh.rrhh_perso as perso_natural', 'alm_req.id_persona', '=', 'perso_natural.id_persona')
            ->leftJoin('comercial.com_cliente', 'alm_req.id_cliente', '=', 'com_cliente.id_cliente')
            ->leftJoin('contabilidad.adm_contri as contri_cliente', 'com_cliente.id_contribuyente', '=', 'contri_cliente.id_contribuyente')
            ->leftJoin('configuracion.ubi_dis', 'alm_req.id_ubigeo_entrega', '=', 'ubi_dis.id_dis')
            ->leftJoin('configuracion.ubi_prov', 'ubi_dis.id_prov', '=', 'ubi_prov.id_prov')
            ->leftJoin('configuracion.ubi_dpto', 'ubi_prov.id_dpto', '=', 'ubi_dpto.id_dpto')

            ->select(
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.concepto',
                'alm_req.id_moneda',
                'alm_req.id_periodo',
                'alm_req.id_prioridad',
                'alm_req.id_estado_doc',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'sis_sede.id_empresa',
                'alm_req.id_grupo',
                'contrib.razon_social as razon_social_empresa',
                'sis_sede.codigo as codigo_sede_empresa',
                'adm_empresa.logo_empresa',
                'alm_req.fecha_requerimiento',
                'alm_req.id_periodo',
                'alm_req.id_tipo_requerimiento',
                'alm_req.observacion',
                'alm_tp_req.descripcion AS tp_req_descripcion',
                'alm_req.id_usuario',
                DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno)  AS persona"),
                'sis_usua.usuario',
                'alm_req.id_rol',
                'rrhh_rol.id_rol_concepto',
                'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
                'alm_req.id_area',
                'adm_area.descripcion AS area_descripcion',
                'alm_req.id_op_com',
                'proy_op_com.codigo as codigo_op_com',
                'proy_op_com.descripcion as descripcion_op_com',
                'alm_req.id_presupuesto',
                'alm_req.objetivo',
                'alm_req.id_occ',
                'alm_req.archivo_adjunto',
                'alm_req.fecha_registro',
                'alm_req.estado',
                'alm_req.id_sede',
                'alm_req.id_persona',
                'perso_natural.nro_documento as dni_persona',
                DB::raw("(perso_natural.nombres) || ' ' || (perso_natural.apellido_paterno) || ' ' || (perso_natural.apellido_materno)  AS nombre_persona"),
                'alm_req.tipo_cliente',
                'alm_req.id_cliente',
                'contri_cliente.nro_documento as cliente_ruc',
                'contri_cliente.razon_social as cliente_razon_social',
                'alm_req.id_ubigeo_entrega',
                DB::raw("(ubi_dis.descripcion) || ' ' || (ubi_prov.descripcion) || ' ' || (ubi_dpto.descripcion)  AS name_ubigeo"),
                'alm_req.direccion_entrega',
                'alm_req.telefono',
                'alm_req.id_almacen',
                'alm_req.monto',
                DB::raw("(CASE WHEN alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
                $theWhere
                // ,['alm_req.estado', '=', 1]
            ])
            ->orderBy('alm_req.id_requerimiento', 'asc')
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
                    // 'objetivo' => $data->objetivo, deprecated ( eliminar campo)
                    'id_moneda' => $data->id_moneda,
                    'id_periodo' => $data->id_periodo,
                    'id_estado_doc' => $data->id_estado_doc,
                    'estado_doc' => $data->estado_doc,
                    'bootstrap_color' => $data->bootstrap_color,
                    'id_prioridad' => $data->id_prioridad,
                    'id_occ' => $data->id_occ,
                    'id_empresa' => $data->id_empresa,
                    'id_grupo' => $data->id_grupo,
                    'id_sede' => $data->id_sede,
                    'razon_social_empresa' => $data->razon_social_empresa,
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
                    'archivo_adjunto' => $data->archivo_adjunto,
                    'id_op_com' => $data->id_op_com,
                    'codigo_op_com' => $data->codigo_op_com,
                    'descripcion_op_com' => $data->descripcion_op_com,
                    'id_presupuesto' => $data->id_presupuesto,
                    'observacion' => $data->observacion,
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
                    'telefono' => $data->telefono,
                    'id_almacen' => $data->id_almacen,
                    'monto' => $data->monto
                    
                ];
            };

            $alm_det_req = DB::table('almacen.alm_item')
                ->rightJoin('almacen.alm_det_req', 'alm_item.id_item', '=', 'alm_det_req.id_item')
                ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
                ->leftJoin('almacen.alm_prod', 'alm_item.id_producto', '=', 'alm_prod.id_producto')
                ->leftJoin('logistica.log_servi', 'alm_item.id_servicio', '=', 'log_servi.id_servicio')
                ->leftJoin('logistica.log_tp_servi', 'log_tp_servi.id_tipo_servicio', '=', 'log_servi.id_tipo_servicio')

                ->leftJoin('almacen.alm_und_medida', 'alm_det_req.id_unidad_medida', '=', 'alm_und_medida.id_unidad_medida')
                ->leftJoin('almacen.alm_und_medida as und_medida_det_req', 'alm_det_req.id_unidad_medida', '=', 'und_medida_det_req.id_unidad_medida')
                // ->leftJoin('almacen.alm_clasif', 'alm_clasif.id_clasificacion', '=', 'alm_prod.id_clasif')
                // ->leftJoin('almacen.alm_subcategoria', 'alm_subcategoria.id_subcategoria', '=', 'alm_prod.id_subcategoria')
                // ->leftJoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_subcategoria.id_categoria')
                // ->leftJoin('almacen.alm_tp_prod', 'alm_tp_prod.id_tipo_producto', '=', 'alm_cat_prod.id_tipo_producto')
                ->leftJoin('logistica.equipo', 'alm_item.id_equipo', '=', 'equipo.id_equipo')

                ->leftJoin('almacen.alm_det_req_adjuntos', 'alm_det_req_adjuntos.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')

                ->leftJoin('finanzas.presup_par', 'presup_par.id_partida', '=', 'alm_det_req.partida')
                ->leftJoin('finanzas.presup_pardet', 'presup_pardet.id_pardet', '=', 'presup_par.id_pardet')

                ->select(
                    'alm_det_req.id_detalle_requerimiento',
                    'alm_req.id_requerimiento',
                    'alm_req.codigo AS codigo_requerimiento',
                    'alm_det_req.id_requerimiento',
                    'alm_det_req.id_item AS id_item_alm_det_req',
                    'alm_det_req.precio_referencial',
                    'alm_det_req.cantidad',
                    'alm_det_req.id_unidad_medida',
                    'und_medida_det_req.descripcion AS unidad_medida',
                    'alm_det_req.obs',
                    'alm_det_req.fecha_registro AS fecha_registro_alm_det_req',
                    'alm_det_req.fecha_entrega',
                    'alm_det_req.lugar_entrega',
                    'alm_det_req.descripcion_adicional',
                    'alm_det_req.id_tipo_item',
                    'alm_det_req.estado',
                    
                    'alm_det_req.partida',
                    'presup_par.codigo AS codigo_partida',
                    'presup_pardet.descripcion AS descripcion_partida',
                    
                    'alm_item.id_item',
                    'alm_det_req.id_producto',
                    'alm_item.codigo AS codigo_item',
                    'alm_item.fecha_registro AS alm_item_fecha_registro',
                    'alm_prod.codigo AS alm_prod_codigo',
                    'alm_prod.part_number',
                    'alm_prod.descripcion AS alm_prod_descripcion',

                    // 'alm_prod.id_unidad_medida AS prod_id_unidad_medida',
                    // 'alm_und_medida.abreviatura AS prod_unidad_medida_abreviatura',
                    // 'alm_und_medida.descripcion AS prod_unidad_medida_descripcion',

                    // 'alm_clasif.id_clasificacion',
                    // 'alm_clasif.descripcion AS alm_clasif_descripcion',
                    // 'alm_subcategoria.id_subcategoria',
                    // 'alm_subcategoria.descripcion AS alm_subcategoria_descripcion',
                    // 'alm_subcategoria.codigo AS alm_subcategoria_codigo',
                    // 'alm_cat_prod.id_categoria',
                    // 'alm_cat_prod.descripcion AS alm_cat_prod_descripcion',
                    // 'alm_cat_prod.codigo AS alm_cat_prod_codigo',
                    // 'alm_tp_prod.id_tipo_producto',
                    // 'alm_tp_prod.descripcion AS alm_tp_prod_descripcion',

                    'alm_item.id_servicio',
                    'log_servi.codigo as log_servi_codigo',
                    'log_servi.descripcion as log_servi_descripcion',
                    'log_servi.id_tipo_servicio',
                    'log_tp_servi.descripcion AS log_tp_servi_descripcion',

                    'alm_item.id_equipo',
                    'equipo.descripcion as equipo_descripcion',

                    'alm_det_req_adjuntos.id_adjunto AS adjunto_id_adjunto',
                    'alm_det_req_adjuntos.archivo AS adjunto_archivo',
                    'alm_det_req_adjuntos.estado AS adjunto_estado',
                    'alm_det_req_adjuntos.fecha_registro AS adjunto_fecha_registro',
                    'alm_det_req_adjuntos.id_detalle_requerimiento AS adjunto_id_detalle_requerimiento'
                )
                ->where([
                    ['alm_det_req.id_requerimiento', '=', $requerimiento[0]['id_requerimiento']]
                    // ,['alm_det_req.estado', '=', 1]
                ])
                ->orderBy('alm_item.id_item', 'asc')
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


            if (isset($alm_det_req)) {
                $lastId = "";
                $detalle_requerimiento = [];
                foreach ($alm_det_req as $data) {
                    if ($data->id_detalle_requerimiento !== $lastId) {
                        $detalle_requerimiento[] = [
                            'id_detalle_requerimiento'  => $data->id_detalle_requerimiento,
                            'id_requerimiento'          => $data->id_requerimiento,
                            'codigo_requerimiento'      => $data->codigo_requerimiento,
                            'id_item'                   => $data->id_item,
                            'cantidad'                  => $data->cantidad,
                            'id_unidad_medida'             => $data->id_unidad_medida,
                            'unidad_medida'             => $data->unidad_medida,
                            'precio_referencial'        => $data->precio_referencial,
                            'descripcion_adicional'     => $data->descripcion_adicional,
                            'fecha_entrega'             => $data->fecha_entrega,
                            'lugar_entrega'             => $data->lugar_entrega,
                            'fecha_registro'            => $data->fecha_registro_alm_det_req,
                            'obs'                       => $data->obs,
                            'estado'                    => $data->estado,
                            'adjunto'                   => [],
                            'codigo_item'                => $data->codigo_item,
                            'id_tipo_item'                => $data->id_tipo_item,

                            'id_servicio'               => $data->id_servicio,
                            'log_servi_codigo'           => $data->log_servi_codigo,
                            'id_tipo_servicio'           => $data->id_tipo_servicio,
                            'log_tp_servi_descripcion'   => $data->log_tp_servi_descripcion,

                            'id_producto'               => $data->id_producto,
                            'codigo_producto'            => $data->alm_prod_codigo,
                            'codigo_producto'            => $data->alm_prod_codigo,
                            // 'descripcion'               => $requerimiento[0]["id_tipo_requerimiento"] ==1?$data->alm_prod_descripcion:($requerimiento[0]["id_tipo_requerimiento"] ==2?$data->log_servi_descripcion:''),
                            'descripcion'               => $data->id_tipo_item == 1 ? $data->alm_prod_descripcion : ($data->id_tipo_item == 2 ? $data->log_servi_descripcion : ($data->id_tipo_item == 3 ? $data->equipo_descripcion : $data->descripcion_adicional)),
                            // 'prod_id_unidad_medida'          => $data->prod_id_unidad_medida,
                            // 'prod_unidad_medida_abreviatura'    => $data->prod_unidad_medida_abreviatura,
                            // 'prod_unidad_medida_descripcion'    => $data->prod_unidad_medida_descripcion,
                            'id_equipo'               => $data->id_equipo,
                            // 'id_clasificacion'             => $data->id_clasificacion,
                            // 'alm_clasif_descripcion'       => $data->alm_clasif_descripcion,
                            // 'id_subcategoria'              => $data->id_subcategoria,
                            // 'alm_subcategoria_descripcion' => $data->alm_subcategoria_descripcion,
                            // 'alm_subcategoria_codigo'      => $data->alm_subcategoria_codigo,
                            // 'id_categoria'                 => $data->id_categoria,
                            // 'alm_cat_prod_descripcion'     => $data->alm_cat_prod_descripcion,
                            // 'alm_cat_prod_codigo'          => $data->alm_cat_prod_codigo,
                            // 'id_tipo_producto'             => $data->id_tipo_producto,
                            // 'alm_tp_prod_descripcion'      => $data->alm_tp_prod_descripcion,

                            'id_partida'                    => $data->partida,
                            'codigo_partida'                => $data->codigo_partida,
                            'descripcion_partida'           => $data->descripcion_partida

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

            //get cotizaciones que tenga el requerimiento
            $log_valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')
                ->select(
                    'log_valorizacion_cotizacion.id_cotizacion'
                )
                ->where(
                    [
                        ['log_valorizacion_cotizacion.id_requerimiento', '=', $id_requerimiento]
                    ]
                )
                ->get();

            $cotizaciones = [];
            foreach ($log_valorizacion_cotizacion as $data) {
                if (in_array($data->id_cotizacion, $cotizaciones) === false) {
                    array_push($cotizaciones, $data->id_cotizacion);
                }
            }
            ////////////////////////////////////////////

            // get detalle grupo cot.
            $log_detalle_grupo_cotizacion = DB::table('logistica.log_detalle_grupo_cotizacion')
                ->select(
                    'log_detalle_grupo_cotizacion.id_grupo_cotizacion'
                )
                ->whereIn('log_detalle_grupo_cotizacion.id_cotizacion', $cotizaciones)
                ->get();
        }
        $grupo_cotizacion = [];
        foreach ($log_detalle_grupo_cotizacion as $data) {
            if (in_array($data->id_grupo_cotizacion, $grupo_cotizacion) === false) {
                array_push($grupo_cotizacion, $data->id_grupo_cotizacion);
            }
        }

        $estado_req = $this->consult_estado($id_requerimiento);
        $req_observacion = [];
        // $detalle_req_observacion = '';
        // $descripcion_observacion='';

        if ($estado_req === 3) { // estado observado
            // $id_doc_aprob = $this->consult_doc_aprob($id_requerimiento);
            // $countAprob = $this->consult_aprob($id_doc_aprob);
            // $countObs = $this->consult_obs($id_doc_aprob);
            // $sgt_aprob = ($countAprob + 1);
            // $niv_aprob = $this->next_aprob($sgt_aprob);
            // $na_orden = $niv_aprob['orden'];
            // $na_flujo = $niv_aprob['flujo'];
            // $no_aprob = is_numeric($niv_aprob['rol_aprob'] ) >0 ?$niv_aprob['rol_aprob']:5; //id_rol
            // // $obs = $this->get_observacion($id_requerimiento,$na_flujo,$no_aprob,3);
            $req_observacion = $this->get_header_observacion($id_requerimiento);

        }

        $num_doc = $this->consult_doc_aprob($id_requerimiento,1); 
        $cantidad_aprobados = $this->consult_aprob($num_doc); 


        $data = [
            "requerimiento" => $requerimiento,
            "det_req" => $detalle_requerimiento,
            "cotizaciones" => $cotizaciones,
            "grupo_cotizacion" => $grupo_cotizacion,
            // "no_aprob" => $no_aprob,
            "observacion_requerimiento" => $req_observacion ? $req_observacion : [],
            "aprobaciones" => $cantidad_aprobados
        ];

        return $data;
    }

    public function copiar_requerimiento(Request $request, $id_requerimiento){

        $sql_grupo = DB::table('administracion.adm_grupo')
        ->select('adm_grupo.id_grupo','adm_grupo.descripcion')
        ->where('adm_grupo.id_grupo', $request->requerimiento['id_grupo'])
        ->get();

        $id_grupo = $sql_grupo->first()->id_grupo;
        $descripcion_grupo = $sql_grupo->first()->descripcion;

        //---------------------GENERANDO CODIGO REQUERIMIENTO--------------------------
        $mes = date('m', strtotime("now"));
        $yy = date('y', strtotime("now"));
        $yyyy = date('Y', strtotime("now"));
        $documento = 'RQ';
        $grupo = $descripcion_grupo[0];
        $num = DB::table('almacen.alm_req')
            // ->whereMonth('fecha_registro', '=', $mes)
            ->whereYear('fecha_registro', '=', $yyyy)
            ->where('id_grupo', '=', $id_grupo)
            ->count();
        $correlativo = $this->leftZero(4, ($num + 1));
        $codigo = "{$documento}{$grupo}-{$yy}{$correlativo}";
        //----------------------------------------------------------------------------
        $data_req = DB::table('almacen.alm_req')->insertGetId(
            [
                'codigo'                => $codigo,
                'id_tipo_requerimiento' => 1,
                'id_usuario'            => $request->requerimiento['id_usuario'],
                'id_rol'                => $request->requerimiento['id_rol'],
                'fecha_requerimiento'   => $request->requerimiento['fecha_requerimiento'],
                'id_periodo'            => $request->requerimiento['id_periodo'],
                'concepto'              => $request->requerimiento['concepto'],
                'id_moneda'             => $request->requerimiento['id_moneda'],
                'id_grupo'              => $request->requerimiento['id_grupo'],
                'id_area'               => $request->requerimiento['id_area'],
                'id_op_com'             => $request->requerimiento['id_op_com'],
                'id_prioridad'          => $request->requerimiento['id_prioridad'],
                'fecha_registro'        => date('Y-m-d H:i:s'),
                'estado'                => $request->requerimiento['estado'],
                'id_estado_doc'         => $request->requerimiento['id_estado_doc']
            ],
            'id_requerimiento'
        );

        $detalle_reqArray = $request->detalle;
        $count_detalle_req = count($detalle_reqArray);
        if ($count_detalle_req > 0) {
            for ($i = 0; $i < $count_detalle_req; $i++) {
                if ($detalle_reqArray[$i]['estado'] > 0) {
                    $alm_det_req = DB::table('almacen.alm_det_req')->insertGetId(

                        [
                            'id_requerimiento'      => $data_req,
                            'id_item'               => is_numeric($detalle_reqArray[$i]['id_item']) == 1 && $detalle_reqArray[$i]['id_item']>0 ? $detalle_reqArray[$i]['id_item']:null,
                            'precio_referencial'    => is_numeric($detalle_reqArray[$i]['precio_referencial']) == 1 ?$detalle_reqArray[$i]['precio_referencial']:null,
                            'cantidad'              => $detalle_reqArray[$i]['cantidad']?$detalle_reqArray[$i]['cantidad']:null,
                            'fecha_entrega'         => $detalle_reqArray[$i]['fecha_entrega']?$detalle_reqArray[$i]['fecha_entrega']:null,
                            'lugar_entrega'         => $detalle_reqArray[$i]['lugar_entrega']?$detalle_reqArray[$i]['lugar_entrega']:null,
                            'descripcion_adicional' => $detalle_reqArray[$i]['des_item']?$detalle_reqArray[$i]['des_item']:null,
                            'partida'               => $detalle_reqArray[$i]['id_partida']?$detalle_reqArray[$i]['id_partida']:null,
                            'id_unidad_medida'      => is_numeric($detalle_reqArray[$i]['id_unidad_medida']) == 1 ? $detalle_reqArray[$i]['id_unidad_medida'] : null,
                            'id_tipo_item'          => is_numeric($detalle_reqArray[$i]['id_tipo_item']) == 1 ? $detalle_reqArray[$i]['id_tipo_item']:null,
                            'estado'                => $detalle_reqArray[$i]['estado']?$detalle_reqArray[$i]['estado']:null,
                            'fecha_registro'        => date('Y-m-d H:i:s'),
                            'estado'                => 1
                        ],
                        'id_detalle_requerimiento'
                    );
                }
            }
        }

        $requerimiento_guardado = DB::table('almacen.alm_req')
            ->select('alm_req.*')
            ->where([
                ['alm_req.id_requerimiento', '=', $data_req]
            ])
            ->orderBy('alm_req.id_requerimiento', 'asc')
            ->get();

        $data_doc_aprob = DB::table('administracion.adm_documentos_aprob')->insertGetId(
            [
                'id_tp_documento' => $requerimiento_guardado[0]->id_tipo_requerimiento,
                'codigo_doc'      => $requerimiento_guardado[0]->codigo,
                'id_doc'          => $requerimiento_guardado[0]->id_requerimiento

            ],
            'id_doc_aprob'
        );

        if($data_req){
            $rpta='OK';
        }else{
            $rsta='NO_COPIADO';
        }

        $output=['status'=>$rpta,'id_requerimiento'=>$data_req, 'codigo_requerimiento'=>$codigo];

        return response()->json($output);
    }

    public function imprimir_requerimiento_pdf($id, $codigo)
    {
        $requerimiento = $this->get_requerimiento($id, $codigo);
        $now = new \DateTime();
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
                        font-size: 11px;
                        box-sizing: border-box;
                        padding:20px;
                }
                
                table{
                width:100%;
                }
                .tablePDF thead{
                    padding:4px;
                    background-color:#e5e5e5;
                }
                .tablePDF,
                .tablePDF tr td{
                    border: 0px solid #ddd;
                }
                .tablePDF tr td{
                    padding: 5px;
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
            </style>
            </head>
            <body>
            
                <img src=".'.$requerimiento['requerimiento'][0]['logo_empresa'].'" alt="Logo" height="75px">

                <h1><center>REQUERIMIENTO N°' . $requerimiento['requerimiento'][0]['codigo'] . '</center></h1>
                <br><br>
            <table border="0">
            <tr>
                <td class="subtitle">REQ. N°</td>
                <td class="subtitle verticalTop">:</td>
                <td width="40%" class="verticalTop">' . $requerimiento['requerimiento'][0]['codigo'] . '</td>
                <td class="subtitle verticalTop">Fecha</td>
                <td class="subtitle verticalTop">:</td>
                <td>' . $requerimiento['requerimiento'][0]['fecha_requerimiento'] . '</td>
            </tr>
            </tr>  
                <tr>
                    <td class="subtitle">Solicitante</td>
                    <td class="subtitle verticalTop">:</td>
                    <td class="verticalTop">' . $requerimiento['requerimiento'][0]['persona'] . '</td>
                </tr>
                <tr>
                    <td class="subtitle">Empresa</td>
                    <td class="subtitle verticalTop">:</td>
                    <td class="verticalTop">' . $requerimiento['requerimiento'][0]['razon_social_empresa'].' - '.$requerimiento['requerimiento'][0]['codigo_sede_empresa'] . '</td>
                </tr>
                <tr>
                    <td class="subtitle">Área</td>
                    <td class="subtitle verticalTop">:</td>
                    <td class="verticalTop">' . $requerimiento['requerimiento'][0]['area_descripcion'] . '</td>
                </tr>
                <tr>
                    <td class="subtitle top">Proyecto</td>
                    <td class="subtitle verticalTop">:</td>
                    <td class="verticalTop justify" colspan="4" >' . $requerimiento['requerimiento'][0]['descripcion_op_com'] . '</td>
                </tr>    
                <tr>
                    <td class="subtitle">Presupuesto</td>
                    <td class="subtitle verticalTop">:</td>
                    <td class="verticalTop"></td>
                </tr>
                </table>
                <br>
                <hr>
                <br>
                <p class="subtitle">1.- DENOMINACIÓN DE LA ADQUISICIÓN</p>
                <div class="texttab">' . $requerimiento['requerimiento'][0]['concepto'] . '</div>';

        $html .=   '</div>
                <p class="subtitle">3.- DESCRIPCIÓN POR ITEM</p>
                <table width="100%" class="tablePDF" border=0>
                <thead>
                    <tr class="subtitle">
                        <td width="3%">#</td>
                        <td width="10%">Item</td>
                        <td width="30%">Descripcion</td>
                        <td width="9%">Fecha Entrega</td>
                        <td width="5%">Und.</td>
                        <td width="5%">Cant.</td>
                        <td width="6%">Precio Ref.</td>
                        <td width="7%">SubTotal</td>
                    </tr>   
                </thead>';
        $total = 0;
        foreach ($requerimiento['det_req'] as $key => $data) {
            $html .= '<tr>';
            $html .= '<td >' . ($key + 1) . '</td>';
            $html .= '<td >' . $data['codigo_item'] . '</td>';
            $html .= '<td >' . ($data['descripcion'] ? $data['descripcion'] : $data['descripcion_adicional']) . '</td>';
            $html .= '<td >' . $data['fecha_entrega'] . '</td>';
            $html .= '<td >' . $data['unidad_medida'] . '</td>';
            $html .= '<td class="right">' . $data['cantidad'] . '</td>';
            $html .= '<td class="right">S/.' . $data['precio_referencial'] . '</td>';
            $html .= '<td class="right">S/.' . $data['cantidad'] * $data['precio_referencial'] . '</td>';
            $html .= '</tr>';
            $total = $total + ($data['cantidad'] * $data['precio_referencial']);
        }
        $html .= '
            <tr>
                <td  class="right" style="font-weight:bold;" colspan="7">TOTAL</td>
                <td class="right">S/.' . $total . '</td>
            </tr>
            </table>
                <br/>
                <br/>
            
                <div class="right">Usuario: ' . $requerimiento['requerimiento'][0]['usuario'] . ' Fecha de Registro:' . $requerimiento['requerimiento'][0]['fecha_registro'] . '</div>
            </body>
            </html>';
        return $html;
    }

    public function generar_requerimiento_pdf($id, $codigo)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->imprimir_requerimiento_pdf($id, $codigo));
        return $pdf->stream();
        return $pdf->download('requerimiento.pdf');
    }

    public function mostrar_adjuntos($id_requerimiento)
    {
        $det_req = DB::table('almacen.alm_req')
            ->select('alm_det_req.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento')

            ->where([
                ['alm_req.id_requerimiento', '=', $id_requerimiento],
                ['alm_req.estado', '=', 1]
            ])
            ->get();
        foreach ($det_req as $data) {
            $det_req_list[] = $data->id_detalle_requerimiento;
        }

        $archivos = DB::table('almacen.alm_det_req_adjuntos')
            ->select(
                'alm_det_req_adjuntos.id_adjunto',
                'alm_det_req_adjuntos.id_detalle_requerimiento',
                'alm_det_req_adjuntos.id_valorizacion_cotizacion',
                'alm_det_req_adjuntos.archivo',
                'alm_det_req_adjuntos.estado',
                'alm_det_req_adjuntos.fecha_registro',
                DB::raw("(CASE WHEN almacen.alm_det_req_adjuntos.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->whereIn('alm_det_req_adjuntos.id_detalle_requerimiento', $det_req_list)
            ->orderBy('alm_det_req_adjuntos.id_adjunto', 'asc')
            ->get();

        return response()->json($archivos);
    }

    public function mostrar_archivos_adjuntos($id_detalle_requerimiento)
    {

        $data = DB::table('almacen.alm_det_req_adjuntos')
            ->select(
                'alm_det_req_adjuntos.id_adjunto',
                'alm_det_req_adjuntos.id_detalle_requerimiento',
                'alm_det_req_adjuntos.id_valorizacion_cotizacion',
                'alm_det_req_adjuntos.archivo',
                'alm_det_req_adjuntos.estado',
                'alm_det_req_adjuntos.fecha_registro',
                'alm_det_req.obs',
                DB::raw("(CASE WHEN almacen.alm_det_req_adjuntos.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'alm_det_req_adjuntos.id_detalle_requerimiento')

            ->where([
                ['alm_det_req_adjuntos.id_detalle_requerimiento','=', $id_detalle_requerimiento],
                ['alm_det_req_adjuntos.estado','=', 1]
                    ])
            ->orderBy('alm_det_req_adjuntos.id_adjunto', 'asc')
            ->get();

        return response()->json($data);
    }
    public function mostrar_archivos_adjuntos_proveedor($id)
    {

        $data = DB::table('almacen.alm_det_req_adjuntos')
            ->select(
                'alm_det_req_adjuntos.id_adjunto',
                'alm_det_req_adjuntos.id_detalle_requerimiento',
                'alm_det_req_adjuntos.id_valorizacion_cotizacion',
                'alm_det_req_adjuntos.archivo',
                'alm_det_req_adjuntos.estado',
                'alm_det_req_adjuntos.fecha_registro',
                DB::raw("(CASE WHEN almacen.alm_det_req_adjuntos.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where('alm_det_req_adjuntos.id_valorizacion_cotizacion', $id)
            ->orderBy('alm_det_req_adjuntos.id_adjunto', 'asc')
            ->get();

        return response()->json($data);
    }

    public function mostrar_archivos_adjuntos_requerimiento($id)
    {
        $data = DB::table('almacen.alm_req_adjuntos')
            ->select(
                'alm_req_adjuntos.id_adjunto',
                'alm_req_adjuntos.id_requerimiento',
                'alm_req_adjuntos.archivo',
                'alm_req_adjuntos.estado',
                'alm_req_adjuntos.fecha_registro',
                DB::raw("(CASE WHEN almacen.alm_req_adjuntos.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
                ['alm_req_adjuntos.id_requerimiento', $id],
                ['alm_req_adjuntos.estado', 1]
                ])
            ->orderBy('alm_req_adjuntos.id_adjunto', 'asc')
            ->get();

        return response()->json(['data'=>$data]);
    }

    public function eliminar_archivo_adjunto_requerimiento($id_adjunto){

        $estado_anulado = $this->get_estado_doc('Anulado');

        $sql= DB::table('almacen.alm_req_adjuntos')
        ->select('alm_req_adjuntos.*')
        ->where('id_adjunto', $id_adjunto)
        ->get();
        
        $id_requerimiento = $sql->first()->id_requerimiento;

        $update = DB::table('almacen.alm_req_adjuntos')->where('id_adjunto', $id_adjunto)
        ->update([
            'estado'          => $estado_anulado
        ]);

        if($update){
            $rpta ='ok';
            // Storage::disk('archivos')->put("logistica/detalle_requerimiento/" . $name_file, \File::get($file));

        }else{
            $rpta='no_actualiza';
        }

        $output=['status'=>$rpta,'id_requerimiento'=>$id_requerimiento];


    return response()->json($output);
        
    }


    public function guardar_archivos_adjuntos_proveedor(Request $request)
    {
        // $archivo_adjunto_length = count($request->only_adjuntos_proveedor);
        $detalle_adjunto = json_decode($request->detalle_adjuntos, true);
        // $detalle_adjuntos_length = count($detalle_adjunto);
        $name_file = '';
        foreach ($request->only_adjuntos_proveedor as $clave => $valor) {
            $file = $request->file('only_adjuntos_proveedor')[$clave];

            if (isset($file)) {
                $name_file = "COT" . time() . $file->getClientOriginalName();
                if ($request->id_valorizacion_cotizacion > 0 || $request->id_valorizacion_cotizacion !== NULL) {

                    $alm_det_req_adjuntos = DB::table('almacen.alm_det_req_adjuntos')->insertGetId(
                        [
                            // 'id_detalle_requerimiento'  => $request->id_detalle_requerimiento,
                            'id_valorizacion_cotizacion'  => $request->id_valorizacion_cotizacion,
                            'archivo'                   => $name_file,
                            'estado'                    => 1,
                            'fecha_registro'            => date('Y-m-d H:i:s')
                        ],
                        'id_adjunto'
                    );
                    Storage::disk('archivos')->put("logistica/cotizacion/" . $name_file, \File::get($file));
                }
            } else {
                $name_file = null;
            }
        }
        return response()->json($alm_det_req_adjuntos);
    }

    public function eliminar_archivo_adjunto_detalle_requerimiento($id_adjunto){

        $estado_anulado = $this->get_estado_doc('Anulado');

        $sql= DB::table('almacen.alm_det_req_adjuntos')
        ->select('alm_det_req_adjuntos.*')
        ->where('id_adjunto', $id_adjunto)
        ->get();
        
        $id_det_req = $sql->first()->id_detalle_requerimiento;

        $update = DB::table('almacen.alm_det_req_adjuntos')->where('id_adjunto', $id_adjunto)
        ->update([
            'estado'          => $estado_anulado
        ]);

        if($update){
            $rpta ='ok';
            // Storage::disk('archivos')->put("logistica/detalle_requerimiento/" . $name_file, \File::get($file));

        }else{
            $rpta='no_actualiza';
        }

        $output=['status'=>$rpta,'id_detalle_requerimiento'=>$id_det_req];


    return response()->json($output);
        
    }

    public function mostrar_archivos_adjuntos_cotizacion($id_cotizacion)
    {

        $data = DB::table('logistica.log_cotizacion')
            ->select(
                'alm_det_req_adjuntos.id_adjunto',
                'valoriza_coti_detalle.id_detalle_requerimiento',
                'alm_det_req_adjuntos.archivo',
                'alm_det_req_adjuntos.fecha_registro',
                'alm_det_req_adjuntos.estado'
            )
            ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
            ->leftJoin('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
            ->join('almacen.alm_det_req_adjuntos', 'alm_det_req_adjuntos.id_detalle_requerimiento', '=', 'valoriza_coti_detalle.id_detalle_requerimiento')
            ->where([
                ['log_cotizacion.id_cotizacion', '=', $id_cotizacion],
                ['log_cotizacion.estado', '=', 1],
                ['log_valorizacion_cotizacion.estado', '!=', 7]
                // ['alm_det_req_adjuntos.estado','=', 1]
            ])
            ->orderBy('alm_det_req_adjuntos.id_adjunto', 'asc')
            ->get();


        return response()->json($data);
    }
    public function getCodigoRequerimiento($id){
        $codigo=0;
        $alm_req = DB::table('almacen.alm_req')
            ->select('alm_req.codigo')
            ->where('alm_req.id_requerimiento', $id)
            ->get();
            if($alm_req){
                $codigo = $alm_req->first()->codigo;
            }
            return $codigo;
    }
    public function getNextvalAdjuntosRequerimiento(){
        $next=0;
        $result = DB::select( DB::raw("SELECT nextval('almacen.alm_req_adjuntos_id_adjunto_seq')"));
        if($result){
            $next= $result[0]->nextval;
        }
            return $next;
    }

    public function guardar_archivos_adjuntos_requerimiento(Request $request)
    {
        $codigo_requerimiento = $this->getCodigoRequerimiento($request->id_requerimiento);
        $nextValId = $this->getNextvalAdjuntosRequerimiento();

        $archivo_adjunto_length = count($request->only_adjuntos);
        $detalle_adjunto = json_decode($request->detalle_adjuntos, true);
        $detalle_adjuntos_length = count($detalle_adjunto);
        $name_file = '';
        // if (is_array($adjuntos)) {}
        foreach ($request->only_adjuntos as $clave => $valor) {
            $file = $request->file('only_adjuntos')[$clave];

            if (isset($file)) {
                // $name_file = "R" . time() . $file->getClientOriginalName();
                $name_file = $codigo_requerimiento.$nextValId;
                if ($request->id_requerimiento > 0 || $request->id_requerimiento !== NULL) {

                    $alm_req_adjuntos = DB::table('almacen.alm_req_adjuntos')->insertGetId(
                        [
                            'id_requerimiento'          => $request->id_requerimiento,
                            'archivo'                   => $name_file,
                            'estado'                    => 1,
                            'fecha_registro'            => date('Y-m-d H:i:s')
                        ],
                        'id_adjunto'
                    );
                    Storage::disk('archivos')->put("logistica/requerimiento/" . $name_file, \File::get($file));
                }
            } else {
                $name_file = null;
            }
        }

        return response()->json($alm_req_adjuntos);
    }
    public function getNextvalAdjuntosDetalleRequerimiento(){
        $next=0;
        $result = DB::select( DB::raw("SELECT nextval('almacen.alm_det_req_adjuntos_id_adjunto_seq')"));
        if($result){
            $next= $result[0]->nextval;
        }
            return $next;
    }

    public function guardar_archivos_adjuntos_detalle_requerimiento(Request $request)
    {
        $codigo_requerimiento = $this->getCodigoRequerimiento($request->id_requerimiento);
        $nextValId = $this->getNextvalAdjuntosDetalleRequerimiento();

        $archivo_adjunto_length = count($request->only_adjuntos);
        $detalle_adjunto = json_decode($request->detalle_adjuntos, true);
        $detalle_adjuntos_length = count($detalle_adjunto);
        $name_file = '';
        // if (is_array($adjuntos)) {}
        foreach ($request->only_adjuntos as $clave => $valor) {
            $file = $request->file('only_adjuntos')[$clave];

            if (isset($file)) {
                // $name_file = "DR" . time() . $file->getClientOriginalName();
                $name_file = $codigo_requerimiento.$nextValId;
                if ($request->id_detalle_requerimiento > 0 || $request->id_detalle_requerimiento !== NULL) {

                    $alm_det_req_adjuntos = DB::table('almacen.alm_det_req_adjuntos')->insertGetId(
                        [
                            'id_detalle_requerimiento'  => $request->id_detalle_requerimiento,
                            'archivo'                   => $name_file,
                            'estado'                    => 1,
                            'fecha_registro'            => date('Y-m-d H:i:s')
                        ],
                        'id_adjunto'
                    );
                    Storage::disk('archivos')->put("logistica/detalle_requerimiento/" . $name_file, \File::get($file));
                }
            } else {
                $name_file = null;
            }
        }
        //     for ($i=0; $i< $detalle_adjuntos_length; $i++){
        //         if($detalle_adjunto[$i]['id_archivo'] === 0 || $detalle_adjunto[$i]['id_archivo'] === null){

        //             $alm_det_req_adjuntos = DB::table('almacen.alm_det_req_adjuntos')->insertGetId(
        //                 [        
        //                     'id_detalle_requerimiento'  => $detalle_adjunto[$i]['id_detalle_requerimiento'],
        //                     'archivo'                   => $detalle_adjunto[$i]['archivo'],
        //                     'estado'                    => $detalle_adjunto[$i]['estado'],
        //                     'fecha_registro'            => date('Y-m-d H:i:s')
        //                 ],
        //                 'id_archivo'
        //             );
        //         }
        // }
        // if ($alm_det_req_adjuntos > 0){
        //     $value = $alm_det_req_adjuntos;
        // }else{
        //     $value = 0;
        // }
        return response()->json($alm_det_req_adjuntos);
    }
    public function telefonos_cliente($id_persona=null,$id_cliente=null){
        $data=[];
        if($id_persona > 0){
            $whereSelected = ['alm_req.id_persona','=',$id_persona];
        }
        if($id_cliente > 0){
            $whereSelected = ['alm_req.id_cliente','=',$id_cliente];

        }
        $tel_req = DB::table('almacen.alm_req')
        ->select(
            'alm_req.telefono'
        )
        ->where([$whereSelected])
        ->whereNotNull('alm_req.telefono')
        ->distinct()
        ->get();

        $data['data']=$tel_req;
        return response()->json($data);
    }

    public function direcciones_cliente($id_persona=null,$id_cliente=null){
        $data=[];
        if($id_persona > 0){
            $whereSelected = ['alm_req.id_persona','=',$id_persona];
        }
        if($id_cliente > 0){
            $whereSelected = ['alm_req.id_cliente','=',$id_cliente];

        }
        $tel_req = DB::table('almacen.alm_req')
        ->select(
            'alm_req.direccion_entrega as direccion'
        )
        ->where([$whereSelected])
        ->whereNotNull('alm_req.direccion_entrega')
        ->distinct()
        ->get();

        $data['data']=$tel_req;
        return response()->json($data);
    }

    public function guardar_requerimiento(Request $request)
    {
        if($request->requerimiento['tipo_requerimiento'] == 2){
            $mes = date('m', strtotime("now"));
            $yy = date('y', strtotime("now"));
            $yyyy = date('Y', strtotime("now"));
            $documento = 'RQ';
            $num = DB::table('almacen.alm_req')
            ->where('id_tipo_requerimiento',2)
            ->whereYear('fecha_registro', '=', $yyyy)
            ->count();
            $correlativo = $this->leftZero(4, ($num + 1));
            $codigo = "{$documento}-V-{$yy}-{$correlativo}";
        }else{
            if(isset($request->requerimiento['id_grupo'])){
                $sql_grupo = DB::table('administracion.adm_grupo')
                ->select('adm_grupo.id_grupo','adm_grupo.descripcion')
                ->where('adm_grupo.id_grupo', $request->requerimiento['id_grupo'])
                ->get();
        
                $id_grupo = $sql_grupo->first()->id_grupo;
                $descripcion_grupo = $sql_grupo->first()->descripcion;  
            //---------------------GENERANDO CODIGO REQUERIMIENTO--------------------------
                $mes = date('m', strtotime("now"));
                $yy = date('y', strtotime("now"));
                $yyyy = date('Y', strtotime("now"));
                $documento = 'RQ';
                $grupo = $descripcion_grupo[0];
                $num = DB::table('almacen.alm_req')
                ->whereYear('fecha_registro', '=', $yyyy)
                ->where('id_grupo', '=', $id_grupo)
                ->count();
                $correlativo = $this->leftZero(4, ($num + 1));
                $codigo = "{$documento}-{$grupo}-{$yy}-{$correlativo}";
            }else{
                $mes = date('m', strtotime("now"));
                $yy = date('y', strtotime("now"));
                $yyyy = date('Y', strtotime("now"));
                $documento = 'RQ';
                $num = DB::table('almacen.alm_req')
                ->where('id_tipo_requerimiento',$request->requerimiento['tipo_requerimiento'])
                ->whereYear('fecha_registro', '=', $yyyy)
                ->count();
                $correlativo = $this->leftZero(4, ($num + 1));
                $tp = '';
                if ($request->requerimiento['tipo_requerimiento'] == 1){
                    $tp = 'C';
                } else if ($request->requerimiento['tipo_requerimiento'] == 3){
                    $tp = 'S';
                }
                $codigo = "{$documento}-{$tp}-{$yy}-{$correlativo}";
                
            }
        }

        if($request->detalle == '' || $request->detalle == null || count($request->detalle)==0){
            return 0;
        }else{

        //----------------------------------------------------------------------------
        $id_requerimiento = DB::table('almacen.alm_req')->insertGetId(
            [
                'codigo'                => $codigo,
                'id_tipo_requerimiento' => $request->requerimiento['tipo_requerimiento'],
                'id_usuario'            => Auth::user()->id_usuario,
                'id_rol'                => isset($request->requerimiento['id_rol'])?$request->requerimiento['id_rol']:null,
                'fecha_requerimiento'   => isset($request->requerimiento['fecha_requerimiento'])?$request->requerimiento['fecha_requerimiento']:null,
                'id_periodo'            => $request->requerimiento['id_periodo'],
                'concepto'              => isset($request->requerimiento['concepto'])?strtoupper($request->requerimiento['concepto']):null,
                'id_moneda'             => isset($request->requerimiento['id_moneda'])?$request->requerimiento['id_moneda']:null,
                'observacion'           => isset($request->requerimiento['observacion'])?$request->requerimiento['observacion']:null,
                'id_grupo'              => isset($request->requerimiento['id_grupo'])?$request->requerimiento['id_grupo']:null,
                'id_area'               => isset($request->requerimiento['id_area'])?$request->requerimiento['id_area']:null,
                'id_op_com'             => isset($request->requerimiento['id_op_com'])?$request->requerimiento['id_op_com']:null,
                'id_prioridad'          => isset($request->requerimiento['id_prioridad'])?$request->requerimiento['id_prioridad']:null,
                'fecha_registro'        => date('Y-m-d H:i:s'),
                'estado'                => ($request->requerimiento['tipo_requerimiento'] ==2?19:1),
                'id_estado_doc'         => $request->requerimiento['id_estado_doc'],
                'codigo_occ'            => isset($request->requerimiento['codigo_occ'])?$request->requerimiento['codigo_occ']:null,
                'id_empresa'            => isset($request->requerimiento['id_empresa'])?$request->requerimiento['id_empresa']:null,
                'id_sede'               => isset($request->requerimiento['id_sede'])?$request->requerimiento['id_sede']:null,
                'tipo_cliente'          => isset($request->requerimiento['tipo_cliente'])?$request->requerimiento['tipo_cliente']:null,
                'id_cliente'            => isset($request->requerimiento['id_cliente'])?$request->requerimiento['id_cliente']:null,
                'id_persona'            => isset($request->requerimiento['id_persona'])?$request->requerimiento['id_persona']:null,
                'direccion_entrega'     => isset($request->requerimiento['direccion_entrega'])?$request->requerimiento['direccion_entrega']:null,
                'telefono'              => isset($request->requerimiento['telefono'])?$request->requerimiento['telefono']:null,
                'id_ubigeo_entrega'     => isset($request->requerimiento['ubigeo'])?$request->requerimiento['ubigeo']:null,
                'id_almacen'            => isset($request->requerimiento['id_almacen'])?$request->requerimiento['id_almacen']:null,
                'confirmacion_pago'     => false,
                'monto'                 => isset($request->requerimiento['monto'])?$request->requerimiento['monto']:null
            ],
            'id_requerimiento'
        );

        // guardar telefono cliente 
        if($request->requerimiento['telefono'] != null || $request->requerimiento['telefono'] != ''){
            $this->actualizar_telefono_cliente($request->requerimiento['tipo_cliente'],$request->requerimiento['id_persona'],$request->requerimiento['id_cliente'],$request->requerimiento['telefono']);
            $this->actualizar_direccion_cliente($request->requerimiento['tipo_cliente'],$request->requerimiento['id_persona'],$request->requerimiento['id_cliente'],$request->requerimiento['direccion_entrega']);
        }

        $detalle_reqArray = $request->detalle;
        $count_detalle_req = count($detalle_reqArray);
        if ($count_detalle_req > 0) {
            for ($i = 0; $i < $count_detalle_req; $i++) {
                    $alm_det_req = DB::table('almacen.alm_det_req')->insertGetId(

                        [
                            'id_requerimiento'      => $id_requerimiento,
                            'id_item'               => is_numeric($detalle_reqArray[$i]['id_item']) == 1 && $detalle_reqArray[$i]['id_item']>0 ? $detalle_reqArray[$i]['id_item']:null,
                            'id_producto'           => is_numeric($detalle_reqArray[$i]['id_producto']) == 1 && $detalle_reqArray[$i]['id_producto']>0 ? $detalle_reqArray[$i]['id_producto']:null,
                            'precio_referencial'    => is_numeric($detalle_reqArray[$i]['precio_referencial']) == 1 ?$detalle_reqArray[$i]['precio_referencial']:null,
                            'cantidad'              => $detalle_reqArray[$i]['cantidad']?$detalle_reqArray[$i]['cantidad']:null,
                            'fecha_entrega'         => isset($detalle_reqArray[$i]['fecha_entrega'])?$detalle_reqArray[$i]['fecha_entrega']:null,
                            'lugar_entrega'         => isset($detalle_reqArray[$i]['lugar_entrega'])?$detalle_reqArray[$i]['lugar_entrega']:null,
                            'descripcion_adicional' => isset($detalle_reqArray[$i]['des_item'])?$detalle_reqArray[$i]['des_item']:null,
                            'partida'               => is_numeric($detalle_reqArray[$i]['id_partida']) == 1 && $detalle_reqArray[$i]['id_partida']>0 ?$detalle_reqArray[$i]['id_partida']:null,
                            'id_unidad_medida'      => is_numeric($detalle_reqArray[$i]['id_unidad_medida']) == 1 ? $detalle_reqArray[$i]['id_unidad_medida'] : null,
                            'id_tipo_item'          => is_numeric($detalle_reqArray[$i]['id_tipo_item']) == 1 ? $detalle_reqArray[$i]['id_tipo_item']:null,
                            'fecha_registro'        => date('Y-m-d H:i:s'),
                            'estado'                => ($request->requerimiento['tipo_requerimiento'] ==2?19:1)
                        ],
                        'id_detalle_requerimiento'
                    );
            }
        }

        $data_doc_aprob = DB::table('administracion.adm_documentos_aprob')->insertGetId(
            [
                'id_tp_documento' => 1,
                'codigo_doc'      => $codigo,
                'id_doc'          => $id_requerimiento

            ],
            'id_doc_aprob'
        );

        return response()->json($id_requerimiento);
        }

    }

    public function  actualizar_telefono_cliente($tipo_cliente,$id_persona,$id_cliente,$telefono){
        if($tipo_cliente ==1){ // persona natural
            $req_tel_pers = DB::table('rrhh.rrhh_perso')
            ->select(
                'rrhh_perso.telefono'
            )
            ->where('rrhh_perso.id_persona',$id_persona)
            ->get();

            if($req_tel_pers->count() > 0){
                if(trim($req_tel_pers->first()->telefono) !== trim($telefono)){
                    // actualizar telefono
                    $update_tel_persona = DB::table('rrhh.rrhh_perso')
                    ->where('id_persona', $id_persona)
                    ->update([               
                        'telefono' => trim($telefono)
                    ]);
                    
                }
            }

        }else if($tipo_cliente == 2){ // persona juridica
            $req_tel_cli = DB::table('comercial.com_cliente')
            ->select(
                'adm_contri.id_contribuyente',
                'adm_contri.telefono'
            )
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->where('com_cliente.id_cliente',$id_cliente)
            ->get();
            
            if($req_tel_cli->count() > 0){
                if(trim($req_tel_cli->first()->telefono) !== trim($telefono)){
                    // actualizar telefono
                    $update_tel_persona = DB::table('contabilidad.adm_contri')
                    ->where('id_contribuyente', $req_tel_cli->first()->id_contribuyente)
                    ->update([               
                        'telefono' => trim($telefono)
                    ]);
                }
            }
        }
    }

    public function  actualizar_direccion_cliente($tipo_cliente,$id_persona,$id_cliente,$direccion){
        if($tipo_cliente ==1){ // persona natural
            $req_dir_pers = DB::table('rrhh.rrhh_perso')
            ->select(
                'rrhh_perso.direccion' 
            )
            ->where('rrhh_perso.id_persona',$id_persona)
            ->get();

            if($req_dir_pers->count() > 0){
                if(trim($req_dir_pers->first()->direccion) !== trim($direccion)){
                    // actualizar telefono
                    $update_dir_persona = DB::table('rrhh.rrhh_perso')
                    ->where('id_persona', $id_persona)
                    ->update([               
                        'direccion' => trim($direccion)
                    ]);
                    
                }
            }

        }else if($tipo_cliente == 2){ // persona juridica
            $req_dir_cli = DB::table('comercial.com_cliente')
            ->select(
                'adm_contri.id_contribuyente',
                'adm_contri.direccion_fiscal'
            )
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->where('com_cliente.id_cliente',$id_cliente)
            ->get();
            
            if($req_dir_cli->count() > 0){
                if(trim($req_dir_cli->first()->direccion_fiscal) !== trim($direccion)){
                    // actualizar telefono
                    $update_dir_persona = DB::table('contabilidad.adm_contri')
                    ->where('id_contribuyente', $req_dir_cli->first()->id_contribuyente)
                    ->update([               
                        'direccion_fiscal' => trim($direccion)
                    ]);
                }
            }
        }

    }
    

    public function anular_requerimiento($id){
        $status=0;
        if($id > 0){
            $userId = Auth::user()->id_usuario;
            $id_usuario_req= DB::table('almacen.alm_req')
            ->select(
                'alm_req.id_usuario'
            )
            ->where([
                ['alm_req.id_requerimiento', '=', $id],
                ['alm_req.estado', '=', 1]
            ])
            ->first()->id_usuario;        

            if($id_usuario_req == $userId){
                $requerimiento = DB::table('almacen.alm_req')->where('id_requerimiento', $id)
                ->update([               
                    'estado' => 7
                ]);
                $detalle_re = DB::table('almacen.alm_det_req')
                ->where('id_requerimiento', '=', $id)
                ->update([
                    'estado' => 7
                ]);
                $status=1;
            }else{
                $status= 2;
            }

           $output=['status'=>$status,'id_usuario_req'=>$id_usuario_req, 'id_usuario_auth'=>$userId];
        }
        return response()->json($output);

    }

    public function actualizar_requerimiento(Request $request, $id)
    {
        $codigo = $request->requerimiento['codigo'];
        $tipo_requerimiento = $request->requerimiento['tipo_requerimiento'];
        $usuario = $request->requerimiento['id_usuario'];
        $id_rol = $request->requerimiento['id_rol'];
        $id_grupo = $request->requerimiento['id_grupo'];
        $fecha_req = $request->requerimiento['fecha_requerimiento'];
        $id_periodo = $request->requerimiento['id_periodo'];
        $concepto = $request->requerimiento['concepto'];
        $observacion = isset($request->requerimiento['observacion'])?$request->requerimiento['observacion']:null;
        $id_sede =  isset($request->requerimiento['id_sede'])?$request->requerimiento['id_sede']:null;
        $tipo_cliente = isset($request->requerimiento['tipo_cliente'])?$request->requerimiento['tipo_cliente']:null;
        $id_persona = isset($request->requerimiento['id_persona'])?$request->requerimiento['id_persona']:null;
        $direccion_entrega = isset($request->requerimiento['direccion_entrega'])?$request->requerimiento['direccion_entrega']:null;
        $ubigeo = isset($request->requerimiento['ubigeo'])?$request->requerimiento['ubigeo']:null;
        $id_almacen = isset($request->requerimiento['id_almacen'])?$request->requerimiento['id_almacen']:null;
        $monto = isset($request->requerimiento['monto'])?$request->requerimiento['monto']:null;
        $moneda = $request->requerimiento['id_moneda'];
        $id_area = $request->requerimiento['id_area'];
        $id_op_com = $request->requerimiento['id_op_com'];
        $id_priori = $request->requerimiento['id_prioridad'];
        $codigo_occ = $request->requerimiento['codigo_occ'];

        if ($id != NULL) {
            $data_requerimiento = DB::table('almacen.alm_req')->where('id_requerimiento', $id)
                ->update([
                    'codigo'                => $codigo,
                    'id_tipo_requerimiento' => $tipo_requerimiento,
                    'id_usuario'            => $usuario,
                    'id_rol'                => is_numeric($id_rol) == 1 ? $id_rol : null,
                    'fecha_requerimiento'   => $fecha_req,
                    'id_periodo'            => $id_periodo,
                    'concepto'              => $concepto,
                    'observacion'           => $observacion,
                    'tipo_cliente'          => $tipo_cliente,
                    'id_persona'            => $id_persona,
                    'direccion_entrega'     => $direccion_entrega,
                    'id_ubigeo_entrega'     => $ubigeo,
                    'id_sede'               => $id_sede,
                    'id_almacen'            => $id_almacen,
                    'id_moneda'             => is_numeric($moneda) == 1 ? $moneda : null,
                    'id_grupo'               => is_numeric($id_grupo) == 1 ? $id_grupo : null,
                    'id_area'               => is_numeric($id_area) == 1 ? $id_area : null,
                    'id_op_com'             => is_numeric($id_op_com) == 1 ? $id_op_com : null,
                    'id_prioridad'          => is_numeric($id_priori) == 1 ? $id_priori : null,
                    'codigo_occ'            => $codigo_occ,
                    'monto'                 => $monto
                ]);
            $count_detalle = count($request->detalle);
            if ($count_detalle > 0) {
                for ($i = 0; $i < $count_detalle; $i++) {
                    $id_det_req = $request->detalle[$i]['id_detalle_requerimiento'];
                    $id_item = $request->detalle[$i]['id_item'];
                    $id_producto = $request->detalle[$i]['id_producto'];
                    $precio_ref = $request->detalle[$i]['precio_referencial'];
                    $cantidad = $request->detalle[$i]['cantidad'];
                    $fecha_entrega = $request->detalle[$i]['fecha_entrega'];
                    $lugar_entrega = $request->detalle[$i]['lugar_entrega'];
                    $des_item = $request->detalle[$i]['des_item'];
                    $id_parti = $request->detalle[$i]['id_partida'];
                    $id_unit = $request->detalle[$i]['id_unidad_medida'];
                    $id_tipo_item = $request->detalle[$i]['id_tipo_item'];
                    $estado = $request->detalle[$i]['estado'];

                    if ($id_det_req > 0) {
                        $data_detalle = DB::table('almacen.alm_det_req')
                            ->where('id_detalle_requerimiento', '=', $id_det_req)
                            ->update([
                                'id_requerimiento'      => $id,
                                'id_item'               => is_numeric($id_item) == 1 ? $id_item : null,
                                'id_producto'           => is_numeric($id_producto) == 1 ? $id_producto : null,
                                'precio_referencial'    => $precio_ref,
                                'cantidad'              => $cantidad,
                                'fecha_entrega'         => $fecha_entrega,
                                'lugar_entrega'         => $lugar_entrega,
                                'descripcion_adicional' => $des_item,
                                'partida'               => is_numeric($id_parti) == 1 ? $id_parti : null,
                                'id_unidad_medida'      => is_numeric($id_unit) == 1 ? $id_unit : null,
                                'id_tipo_item'          => is_numeric($id_tipo_item) == 1 ? $id_tipo_item : null,
                                'estado'                => $estado
                            ]);
                    } else {
                        $data_detalle = DB::table('almacen.alm_det_req')->insertGetId(
                            [
                                'id_requerimiento'      => $id,
                                'id_item'               => $id_item,
                                'precio_referencial'    => $precio_ref,
                                'cantidad'              => $cantidad,
                                'fecha_entrega'         => $fecha_entrega,
                                'lugar_entrega'         => $lugar_entrega,
                                'descripcion_adicional' => $des_item,
                                'partida'               => $id_parti,
                                'id_unidad_medida'      => $id_unit,
                                'id_tipo_item'          => $id_tipo_item,
                                'estado'                => $estado,
                                'fecha_registro'        => date('Y-m-d H:i:s'),
                                'estado'                => 1
                            ],
                            'id_detalle_requerimiento'
                        );
                    }
                }
                return response()->json($data_detalle);
            }
            return response()->json($data_requerimiento);
        } else {
            return response(0);
        }
    }

    function mostrar_moneda()
    {
        $data = DB::table('configuracion.sis_moneda')
            ->select(
                'sis_moneda.id_moneda',
                'sis_moneda.descripcion',
                'sis_moneda.simbolo',
                'sis_moneda.estado',
                DB::raw("(CASE WHEN configuracion.sis_moneda.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
                ['sis_moneda.estado', '=', 1]
            ])
            ->orderBy('sis_moneda.id_moneda', 'asc')
            ->get();
        return $data;
    }

    function mostrar_prioridad()
    {
        $data = DB::table('administracion.adm_prioridad')
            ->select(
                'adm_prioridad.id_prioridad',
                'adm_prioridad.descripcion'
            )
            ->where([
                ['adm_prioridad.estado', '=', 1]
            ])
            ->orderBy('adm_prioridad.id_prioridad', 'asc')
            ->get();
        return $data;
    }

    function mostrar_tipo()
    {
        $data = DB::table('almacen.alm_tp_req')
            ->select(
                'alm_tp_req.id_tipo_requerimiento',
                'alm_tp_req.descripcion'
            )
            ->orderBy('alm_tp_req.id_tipo_requerimiento', 'asc')
            ->get();
        return $data;
    }
    public function cargar_estructura_org($id)
    {
        $html = '';
        $sql1 = DB::table('administracion.sis_sede')->where('id_empresa', '=', $id)->get();
        foreach ($sql1 as $row) {
            $id_sede = $row->id_sede;
            $html .= '<ul>';
            $sql2 = DB::table('administracion.adm_grupo')->where('id_sede', '=', $row->id_sede)->get();
            if ($sql2->count() > 0) {
                $html .=
                    '<li class="firstNode" onClick="showEfectOkc(' . $row->id_sede . ');">
                    <h5>+ <b> Sede - ' . $row->descripcion . '</b></h5>
                    <ul class="ul-nivel1" id="detalle-' . $row->id_sede . '">';
                foreach ($sql2 as $key) {
                    $id_grupo = $key->id_grupo;
                    $sql3 = DB::table('administracion.adm_area')->where('id_grupo', '=', $key->id_grupo)->get();
                    if ($sql3->count() > 0) {
                        $html .= '<li><b>Grupo - ' . $key->descripcion . '</b><ul class="ul-nivel2">';
                        foreach ($sql3 as $value) {
                            $id_area = $value->id_area;
                            $area = $value->descripcion;
                            $txtArea = "'" . $area . "'";
                            $html .= '<li id="' . $id_area . '" onClick="areaSelectModal(' . $id_sede . ', ' . $id_grupo . ', ' . $id_area . ', ' . $txtArea . ');"> ' . $area . '</li>';
                        }
                    } else {
                        $html .= '<li> ' . $key->descripcion . '</li>';
                    }
                    $html .= '</li></ul>';
                }
                $html .= '</li></ul>';
            } else {
                $html .= '<li>' . $row->descripcion . '</li>';
            }
            $html .= '</ul>';
        }

        return response()->json($html);
    }

    function mostrar_area()
    {
        $data = DB::table('administracion.adm_area')
            ->select(
                'adm_area.*',
                DB::raw("(CASE WHEN administracion.adm_area.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")

            )
            ->where([
                ['adm_area.estado', '=', 1]
            ])
            ->orderBy('adm_area.id_area', 'asc')
            ->get();
        return $data;
    }

    function mostrar_condicion_pago()
    {
        $data = DB::table('logistica.log_cdn_pago')
            ->select(
                'log_cdn_pago.*',
                DB::raw("(CASE WHEN logistica.log_cdn_pago.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
                ['log_cdn_pago.estado', '=', 1]
            ])
            ->orderBy('log_cdn_pago.id_condicion_pago', 'asc')
            ->get();
        return $data;
    }

    function mostrar_tipo_documento()
    {
        $data = DB::table('contabilidad.cont_tp_doc')
            ->select(
                'cont_tp_doc.*',
                DB::raw("(CASE WHEN contabilidad.cont_tp_doc.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
                ['cont_tp_doc.estado', '=', 1]
            ])
            ->orderBy('cont_tp_doc.id_tp_doc', 'asc')
            ->get();
        return $data;
    }

    function mostrar_periodos()
    {
        $data = DB::table('administracion.adm_periodo')
            ->select(
                'adm_periodo.*'
            )
            ->where([
                ['adm_periodo.estado', '=', 1]
            ])
            ->orderBy('adm_periodo.id_periodo', 'desc')
            ->get();
        return $data;
    }
    function mostrar_unidad_medida()
    {
        $data = DB::table('almacen.alm_und_medida')
            ->select(
                'alm_und_medida.id_unidad_medida',
                'alm_und_medida.descripcion',
                'alm_und_medida.abreviatura',
                'alm_und_medida.estado',
                DB::raw("(CASE WHEN almacen.alm_und_medida.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
                ['alm_und_medida.estado', '=', 1]
            ])
            ->orderBy('alm_und_medida.id_unidad_medida', 'asc')
            ->get();
        return $data;
    }

    function detalle_unidad_medida($id)
    {
        $data = DB::table('almacen.alm_und_medida')
            ->select(
                'alm_und_medida.*',
                DB::raw("(CASE WHEN almacen.alm_und_medida.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
                ['alm_und_medida.estado', '=', 1],
                ['alm_und_medida.id_unidad_medida', '=', $id]
            ])
            ->orderBy('alm_und_medida.id_unidad_medida', 'asc')
            ->first();
        return response()->json($data);
    }

    public function mostrar_items()
    {
        $data = DB::table('almacen.alm_item')
            ->select(
                'alm_item.id_item',
                'alm_item.codigo',
                'alm_item.id_producto',
                'alm_item.id_servicio',
                'alm_item.id_equipo',
                DB::raw("(CASE 
                            WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
                            WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
                            WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
                            ELSE 'nulo' END) AS descripcion
                            "),
                DB::raw("(CASE 
                            WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.descripcion
                            WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'Servicio' 
                            WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'Equipo' 
                            ELSE 'nulo' END) AS unidad_medida_descripcion
                            "),

                'alm_prod.part_number',
                'alm_prod.id_unidad_medida',
                'alm_prod_ubi.stock'
            )
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('almacen.alm_prod_ubi', 'alm_prod_ubi.id_producto', '=', 'alm_prod.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
            // ->where([
            // ['alm_prod_ubi.stock', '>', 0],
            // ['alm_prod_ubi.estado', '=', 1]
            // ])
            // ->limit(500)
            ->get();
        return response()->json(["data" => $data]);
    }

    public function mostrar_item($id_item)
    {
        $data = DB::table('almacen.alm_item')
            ->leftJoin('almacen.alm_prod', 'alm_item.id_producto', '=', 'alm_prod.id_producto')
            ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
            ->leftJoin('almacen.alm_prod_ubi', 'alm_prod_ubi.id_producto', '=', 'alm_prod.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->select(
                'alm_item.id_item',
                'alm_item.codigo',
                'alm_item.id_producto',
                'alm_item.id_servicio',
                'alm_item.id_equipo',
                DB::raw("(CASE 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
                ELSE 'nulo' END) AS descripcion
                "),
                'alm_prod.part_number',
                'alm_prod.id_unidad_medida',
                'alm_und_medida.descripcion AS unidad_medida_descripcion',
                'alm_prod_ubi.stock'
            )
            ->where([
                ['alm_item.id_item', '=', $id_item]
            ])
            ->orderBy('alm_item.id_item', 'asc')
            ->get();
        return response()->json($data);
    }


    public function get_current_user(){
        $userRolId = Auth::user()->login_rol;
        $userId = Auth::user()->id_usuario;
        $userName = Auth::user()->usuario;
        $userFullName = Usuario::find($userId)->trabajador->postulante->persona->nombre_completo;

        $roles = Auth::user()->trabajador->roles;
        $rol_id_list=[];
        $rol_concepto_id_list=[];
        $rol_id_area_list=[];
        foreach($roles as $rol){
            if($rol->estado > 0){
                $rol_id_list[]= $rol->pivot->id_rol;
                $rol_concepto_id_list[]= $rol->pivot->id_rol_concepto;
                $rol_id_area_list[]= $rol->pivot->id_area;
            }

            $roles_usuario_list[]=[
                'descripcion'=> $rol->descripcion,
                'id_rol'=> $rol->pivot->id_rol,
                'id_area'=> $rol->pivot->id_area,
                'id_rol_concepto'=> $rol->pivot->id_rol_concepto,
                'estado'=> $rol->estado

            ];
        }

        $user_current=[
            'userRoles'=> $roles_usuario_list,
            'idRolList'=> $rol_id_list,
            'idRolConceptoList'=> $rol_concepto_id_list,
            'idRolAreaList'=> $rol_id_area_list,
            'userId'=> $userId,
            'userName'=> $userName,
            'userFullName'=> $userFullName
        ];
        return $user_current;
        
    }

    public function get_req_list($id_empresa, $id_sede, $id_grupo){

        $grupoList=[];


        $hasWhereGrupo=[
            ['adm_grupo.estado', '=', 1],
            ['sis_sede.estado', '=', 1]
        ];

        if($id_empresa >0){
            $hasWhereGrupo[]=['sis_sede.id_empresa', '=', $id_empresa];
        }
        if($id_sede >0){
            $hasWhereGrupo[]=['sis_sede.id_sede', '=', $id_sede];
        }
        if($id_grupo >0){
            $hasWhereGrupo[]=['adm_grupo.id_grupo', '=', $id_grupo];
        }

        $grupo = DB::table('administracion.adm_grupo')
        ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
        ->select(
            'adm_grupo.id_grupo',
            'sis_sede.id_sede'
        )
        ->where($hasWhereGrupo)
        ->get();

        foreach($grupo as $data){
            $grupoList[]=$data->id_grupo;
        }
        $req     = array();
        $det_req = array();

        $sql_req = DB::table('almacen.alm_req')
        ->leftJoin('administracion.adm_estado_doc', 'alm_req.id_estado_doc', '=', 'adm_estado_doc.id_estado_doc')
        ->leftJoin('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
        ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
        ->leftJoin('administracion.adm_grupo', 'alm_req.id_grupo', '=', 'adm_grupo.id_grupo')
        ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
        ->leftJoin('proyectos.proy_op_com', 'proy_op_com.id_op_com', '=', 'alm_req.id_op_com')
        ->leftJoin('configuracion.sis_moneda', 'alm_req.id_moneda', '=', 'sis_moneda.id_moneda')
        ->leftJoin('administracion.adm_periodo', 'adm_periodo.id_periodo', '=', 'alm_req.id_periodo')
        ->select(
            'alm_req.id_requerimiento',
            'alm_req.codigo',
            'alm_req.id_tipo_requerimiento',
            'alm_req.id_usuario',
            'alm_req.id_rol',
            'alm_req.fecha_requerimiento',
            'alm_req.id_periodo',
            'adm_periodo.descripcion as descripcion_periodo',
            'alm_req.concepto',
            'alm_req.id_grupo',
            'alm_req.id_op_com',
            'proy_op_com.codigo as codigo_op_com',
            'proy_op_com.descripcion as descripcion_op_com',
            'alm_req.concepto AS alm_req_concepto',
            'alm_req.estado',
            'alm_req.fecha_registro',
            'alm_req.id_area',
            'alm_req.archivo_adjunto',
            'alm_req.id_prioridad',
            'alm_req.id_estado_doc',
            'alm_req.id_presupuesto',
            'alm_req.objetivo',
            'alm_req.tipo_occ',
            'alm_req.id_occ',
            'alm_req.id_moneda',
            'alm_req.desembolso',
            'adm_estado_doc.estado_doc',
            'alm_tp_req.descripcion AS tipo_requerimiento',
            'adm_prioridad.descripcion AS priori',
            'adm_grupo.descripcion AS grupo',
            'adm_area.descripcion AS area',
            'sis_moneda.simbolo AS simbolo_moneda'


        )
        ->where('alm_req.estado', '>=', 1)->orderBy('alm_req.id_requerimiento', 'DESC')
        ->whereIn('alm_req.id_grupo', $grupoList)
        ->get();

        foreach($sql_req as $data){
            $req[]=[
                'id_requerimiento' => $data->id_requerimiento,
                'codigo' => $data->codigo,
                'id_tipo_requerimiento' => $data->id_tipo_requerimiento,
                'id_usuario' => $data->id_usuario,
                'id_rol' => $data->id_rol,
                'fecha_requerimiento' => $data->fecha_requerimiento,
                'id_periodo' => $data->id_periodo,
                'descripcion_periodo' => $data->descripcion_periodo,
                'concepto' => $data->concepto,
                'id_grupo' => $data->id_grupo,
                'id_op_com' => $data->id_op_com,
                'codigo_op_com' => $data->codigo_op_com,
                'descripcion_op_com' => $data->descripcion_op_com,
                'estado' => $data->estado,
                'fecha_registro' => $data->fecha_registro,
                'id_area' => $data->id_area,
                'archivo_adjunto' => $data->archivo_adjunto,
                'id_prioridad' => $data->id_prioridad,
                'id_estado_doc' => $data->id_estado_doc,
                'id_presupuesto' => $data->id_presupuesto,
                'objetivo' => $data->objetivo,
                'tipo_occ' => $data->tipo_occ,
                'id_occ' => $data->id_occ,
                'id_moneda' => $data->id_moneda,
                'simbolo_moneda' => $data->simbolo_moneda,
                'desembolso' => $data->desembolso,
                'estado_doc' => $data->estado_doc,
                'tipo_requerimiento' => $data->tipo_requerimiento,
                'priori' => $data->priori,
                'grupo' => $data->grupo,
                'area' => $data->area
                
            ];
        }

        $size_req= count($req);

        $sql_det_req = DB::table('almacen.alm_det_req')
        ->select(
            'alm_det_req.id_detalle_requerimiento',
            'alm_det_req.id_requerimiento',
            'alm_det_req.id_item',
            'alm_det_req.precio_referencial',
            'alm_det_req.cantidad',
            'alm_det_req.fecha_entrega',
            'alm_det_req.descripcion_adicional',
            'alm_det_req.obs',
            'alm_det_req.partida',
            'alm_det_req.unidad_medida',
            'alm_det_req.estado',
            'alm_det_req.fecha_registro',
            'alm_det_req.lugar_entrega',
            'alm_det_req.id_unidad_medida',
            'alm_det_req.id_tipo_item'
        )
        ->where('alm_det_req.estado', '=', 1)->orderBy('alm_det_req.id_requerimiento', 'DESC')
        ->get();
        
        if(isset($sql_det_req) && sizeof($sql_det_req) > 0){
            foreach($sql_det_req as $data){
                $det_req[]=[
                    'id_detalle_requerimiento'=> $data->id_detalle_requerimiento,
                    'id_requerimiento'=> $data->id_requerimiento,
                    'id_item'=> $data->id_item,
                    'precio_referencial'=> $data->precio_referencial,
                    'cantidad'=> $data->cantidad,
                    'fecha_entrega'=> $data->fecha_entrega,
                    'descripcion_adicional'=> $data->descripcion_adicional,
                    'obs'=> $data->obs,
                    'partida'=> $data->partida,
                    'unidad_medida'=> $data->unidad_medida,
                    'estado'=> $data->estado,
                    'fecha_registro'=> $data->fecha_registro,
                    'lugar_entrega'=> $data->lugar_entrega,
                    'id_unidad_medida'=> $data->id_unidad_medida,
                    'id_tipo_item'=> $data->id_tipo_item
                ];
            }
    
            $size_det_req= count($det_req);
            
            for($i = 0; $i < $size_req; $i++ ){
                for($j = 0; $j < $size_det_req; $j++ ){
                    $req[$i]['detalle'] = [];
    
                }
            }
    
            for($i = 0; $i < $size_req; $i++ ){
                for($j = 0; $j < $size_det_req; $j++ ){
                    if($det_req[$j]['id_requerimiento'] == $req[$i]['id_requerimiento']){
                        $req[$i]['detalle'][] = $det_req[$j];
                    }
                }
            }
        }else{ // si no existe datos en detalle_requerimiento
            for($i = 0; $i < $size_req; $i++ ){
                $req[$i]['detalle'] = [];
            }

        }


        return $req;
    }

    public function reqHasQuotation($id_requerimiento){
        
        $cantidad_cotizaciones = count($this->get_cotizacion_by_req($id_requerimiento));
        return $cantidad_cotizaciones;
    }

    public function reqHasOrder($id_requerimiento){

        $cantidad_ordenes = count($this->get_orden_by_req($id_requerimiento));
        return $cantidad_ordenes;
    }

    public function hasCriterios($id_flujo){
        $sql = DB::table('administracion.adm_detalle_grupo_criterios')
        ->select(
            'adm_detalle_grupo_criterios.*'
        )
        ->leftJoin('administracion.adm_grupo_criterios', 'adm_grupo_criterios.id_grupo_criterios', '=', 'adm_detalle_grupo_criterios.id_grupo_criterios')
        ->where([
            ['adm_detalle_grupo_criterios.id_flujo', '=', $id_flujo],
            ['adm_grupo_criterios.estado', '=', 1],
            ['adm_detalle_grupo_criterios.estado', '=', 1],
        ])
        ->get();
        if ($sql->count() > 0) {
            $id_detalle_grupo_criterios = $sql->first()->id_detalle_grupo_criterios;
            $id_criterio_monto = $sql->first()->id_criterio_monto;
            $id_criterio_prioridad = $sql->first()->id_criterio_prioridad;
        }else{
            $id_detalle_grupo_criterios=0;
            $id_criterio_monto=0;
            $id_criterio_prioridad=0;
        }
        $array = array('id_detalle_grupo_criterios' => $id_detalle_grupo_criterios, 'id_criterio_monto' => $id_criterio_monto, 'id_criterio_prioridad' => $id_criterio_prioridad);
        return $array;
    }

    public function getCriterioMonto($id_criterio_monto){
        $sql = DB::table('administracion.adm_criterio_monto')
        ->select(
            'adm_criterio_monto.*',
            'op1.descripcion as descripcion_operador1',
            'op1.estado as estado_op1',
            'op1.signo as signo1',
            'op2.descripcion as descripcion_operador2',
            'op2.signo as signo2',
            'op2.estado as estado_op2'
        )
        ->leftJoin('administracion.operadores as op1', 'op1.id_operador', '=', 'adm_criterio_monto.id_operador1')
        ->leftJoin('administracion.operadores as op2', 'op2.id_operador', '=', 'adm_criterio_monto.id_operador2')
        ->where([
            ['adm_criterio_monto.id_criterio_monto', '=', $id_criterio_monto],
            ['adm_criterio_monto.estado', '=', 1],
        ])
        ->get();
        if ($sql->count() > 0) {
            $id_criterio_monto = $sql->first()->id_criterio_monto;
            $descripcion = $sql->first()->descripcion;
            $id_operador1 = $sql->first()->id_operador1;
            $descripcion_operador1 = $sql->first()->descripcion_operador1;
            $signo1 = $sql->first()->signo1;
            $estado_op1 = $sql->first()->estado_op1;
            $monto1 = $sql->first()->monto1;
            $id_operador2 = $sql->first()->id_operador2;
            $descripcion_operador2 = $sql->first()->descripcion_operador2;
            $signo2 = $sql->first()->signo2;
            $estado_op2 = $sql->first()->estado_op2;
            $monto2 = $sql->first()->monto2;
            $estado = $sql->first()->estado;
        }else{
            $id_criterio_monto = 0;
            $descripcion = '';
            $id_operador1 = 0;
            $descripcion_operador1 = '';
            $signo1 = '';
            $estado_op1 = '';
            $monto1 = 0;
            $id_operador2 = 0;
            $descripcion_operador2 = '';
            $signo2 = '';
            $estado_op1 = '';
            $monto2 = 0;
            $estado = 0;
        }
        $array = array(
            'id_criterio_monto' => $id_criterio_monto,
            'descripcion' => $descripcion,
            'id_operador1' => $id_operador1,
            'descripcion_operador1' => $descripcion_operador1,
            'signo1' => $signo1,
            'estado_op1' => $estado_op1,
            'monto1' => $monto1,
            'id_operador2' => $id_operador2,
            'descripcion_operador2' => $descripcion_operador2,
            'signo2' => $signo2,
            'estado_op2' => $estado_op2,
            'monto2' => $monto2,
            'estado' => $estado
        );
        return $array;
    }
    public function getOperacionCriterio($montoReq,$signo,$monto){
        switch($signo){
            case '=': return $montoReq == $monto;
            case '>': return $montoReq > $monto;
            case '>=': return $montoReq >= $monto;
            case '<': return $montoReq < $monto;
            case '<=': return $montoReq <= $monto;
            
        }
    }

    public function evalateCriterioMonto($montoReq,$montoCM1,$signoCM1,$montoCM2,$signoCM2){
        if(($signoCM1 != null || $signoCM1 != '') && ($signoCM2 != null || $signoCM2 != '')){
            $primerCriterioMonto = $this->getOperacionCriterio($montoReq,$signoCM1,montoCM1);
            $segundoCriterioMonto =$this->getOperacionCriterio($montoReq,$signoCM2,montoCM2);

            if($primerCriterioMonto ==true && $segundoCriterioMonto ==true){
                //    continua con el flujo que tien el criterio
                return true;
            }else{
                //    salta el flujo que sigue
                return false;
            }

        }elseif(($signoCM1 != null || $signoCM1 != '')){
            $primerCriterioMonto = $this->getOperacionCriterio($montoReq,$signoCM1,montoCM1);
            if($primerCriterioMonto ==true){
                //    continua con el flujo que tien el criterio
                return true;
            }else{
                //    salta el flujo que sigue
                return false;
            }

        }elseif(($signoCM2 != null || $signoCM2 != '')){
            $segundoCriterioMonto = $this->getOperacionCriterio($montoReq,$signoCM2,montoCM2);
            if($segundoCriterioMonto ==true){
                //    continua con el flujo que tien el criterio
                return true;
            }else{
                //    salta el flujo que sigue
                return false;
            }
        }
    }

    public function get_id_doc($id_doc_aprob,$tp_doc){
        $sql = DB::table('administracion.adm_documentos_aprob')
        ->where([['id_tp_documento', '=', $tp_doc], 
        ['id_doc_aprob', '=', $id_doc_aprob]])
        ->get();

        if ($sql->count() > 0) {
            $val = $sql->first()->id_doc;
        } else {
            $val = 0;
        }
        return $val;
    }

    public function get_id_rol_req($id_req){
        $sql = DB::table('almacen.alm_req')
        ->where([['id_requerimiento', '=', $id_req]])
        ->get();
        if ($sql->count() > 0) {
            $val = $sql->first()->id_rol;
        } else {
            $val = 0;
        }
        return $val;
    }

    public function get_id_area_rol($id_rol_req){
        $sql = DB::table('administracion.rol_aprobacion')
        ->where([['id_rol_aprobacion', '=', $id_rol_req]])
        ->get();
        if ($sql->count() > 0) {
            $val = $sql->first()->id_area;
        } else {
            $val = 0;
        }
        return $val;
    }

    public function get_id_req_orden($id_orden){
        
    }

    public function getAreaOfRolAprob($id_doc_aprob, $tp_doc){

        $id_area_rol = 0;
        $msg = 'OK';

        switch ($tp_doc) {
            case '1': //requerimiento
                # code...
                $id_req = $this->get_id_doc($id_doc_aprob,1);
                if($id_req == 0){
                    $msg='error id_req';
                }else{
                    $id_rol_req = $this-> get_id_rol_req($id_req);
                    if($id_rol_req == 0){
                        $msg='error id_rol_req';
                    }else{
                        $id_area_rol = $this->get_id_area_rol($id_rol_req);
                        if($id_area_rol == 0){
                            $msg='error id_area_rol';
                        }
                    }
                }

                break;
            
            case '2': //orden
                # code...
                $id_orden = $this->get_id_doc($id_doc_aprob,2);
                
                if($id_orden == 0){
                    $msg='error id_orden';
                }else{
                    $dataReq= $this-> get_data_req_by_id_orden($id_orden);
                    if(count($dataReq) == 0){
                        $msg='error dataReq';
                    }else{
                        $id_area_rol= array_unique($dataReq['data']['rol']);
                        if(count($id_area_rol) == 0){
                            $msg='error id_area_rol';
                        }        
                    }
                }
                break;
            
            default:
                # code...
                break;
        }

        $array = array('id'=>$id_area_rol, 'msg'=>$msg);
        return $array;
    }
    
    public function listar_requerimiento_v2($id_empresa,$id_sede,$id_grupo){

        $userRoles=$this->userSession()['roles'];
        $userId=$this->userSession()['id_usuario'];
        $userRolConceptoList=[];

        foreach($userRoles as $us){
            $userRolConceptoList[]=$us->id_rol_concepto;
        }

        // $output['data']=[];
        $output=[];
        // datos del requerimiento
        foreach ($this->get_req_list($id_empresa,$id_sede,$id_grupo) as $row) {
            $id_req = $row['id_requerimiento'];
            $codigo = $row['codigo'];
            $concepto = $row['concepto'];
            $simbolo_moneda = $row['simbolo_moneda'];
            $id_pri = $row['id_prioridad'];
            $priori = $row['priori'];
            $tp_req = $row['tipo_requerimiento'];
            $id_usu = $row['id_usuario'];
            $id_rol = $row['id_rol'];
            $fec_rq = date('d/m/Y', strtotime($row['fecha_requerimiento']));
            $id_periodo = $row['id_periodo'];
            $desc_periodo = $row['descripcion_periodo'];
            $id_est = $row['id_estado_doc'];
            $estado = $row['estado_doc'];
            $id_area = $row['id_area'];
            $area = $row['area'];
            $id_grp = $row['id_grupo'];
            $grupo = $row['grupo'];
            $proyec = $row['descripcion_op_com'];
            $method = '';
        
            $detalle = $row['detalle'];
            $aux_sum=0;
            foreach($detalle as $data){
                $total = intval($data['cantidad']) * floatval($data['precio_referencial']);
                $aux_sum = $aux_sum + floatval($total);
            }
            $monto_total_referencial= $simbolo_moneda.(number_format($aux_sum,2,'.', ''));


            if ($proyec != null) {
                    $gral = $proyec;
                } else {
                    $gral = $area;
                }
            

            
            if (strtolower($priori) == 'normal') {
                $flag = '<center> <i class="fas fa-thermometer-empty green"  data-toggle="tooltip" data-placement="right" title="Normal" ></i></center>';
            } elseif (strtolower($priori) == 'normaaltal') {
                $flag = '<center> <i class="fas fa-thermometer-half orange"  data-toggle="tooltip" data-placement="right" title="Alta"  ></i></center>';
            } else {
                $flag = '<center> <i class="fas fa-thermometer-full red"  data-toggle="tooltip" data-placement="right" title="Crítico"  ></i></center>';
            }

            $usuario = Usuario::find($id_usu)->trabajador->postulante->persona->nombre_completo;
            $empresa = Grupo::find($id_grp)->sede->empresa->contribuyente->razon_social;
            // $id_cnc_rol = $this->consult_id_rol_aprob($userRolList);


            // numero de documento (adm_documentos_aprob.id_doc)
            $num_doc = $this->consult_doc_aprob($id_req,1); 
            $areaOfRolAprob = $this->getAreaOfRolAprob($num_doc,1); //{num doc},{tp doc} 
            $tp_doc=1; // tipo de documento = requerimiento 
            $id_operacion= $this->get_id_operacion($id_grp,$areaOfRolAprob['id'],$tp_doc);
            // nivel de aprobación para conocer si el usuario esta en alguna fase del flujo Function(id_rol,id_prioridad) return id_fñujo, orden , id_rol
            // return $id_area;
            $niv_aprob = $this->consult_nivel_aprob($userRolConceptoList, $id_operacion);
            // $na_orden = $niv_aprob['orden'];
            $na_flujo = $niv_aprob['flujo'];
            // $no_aprob = $niv_aprob['rol_aprob'];
            $cantidad_aprobados = 0;
            $cantidad_observados = 0;
        
            $cantidad_observados = $this->consult_obs($id_req); 
            $cantidad_aprobados = $this->consult_aprob($num_doc); 
            $totalFlujo = $this->consult_tamaño_flujo($id_req);

        // buscar ultima aprobacion registrada return id_flujo 
        $last_aprob = $this->last_aprob($num_doc);
        $id_flujo_last_aprob = $last_aprob['id_flujo'];
   
        $reqHasQuotation=$this->reqHasQuotation($id_req);
        $reqHasOrder=$this->reqHasOrder($id_req);

        $ap_apr = "'aprobar'";
        $ap_obs = "'observar'";
        $ap_dng = "'denegar'";
        $ap_sus = "'aprobar_sustento'";
        $status = "";
        $indicadorCotizacion = "";
        $indicadorOrden = "";
        $aprobs = "";
        $method='';

        if($reqHasQuotation >0){
            $indicadorCotizacion ='<label class="label label-success" title="Tiene Cotización">C</label>';
        }else{
            $indicadorCotizacion="";
        }
        if($reqHasOrder >0){
            $indicadorOrden ='<label class="label label-danger" title="Tiene Orden">O</label>';
        }else{
            $indicadorOrden="";
        }

        foreach($userRoles as $value){
            if($value->rol_concepto == 'JEFE DE LOGISTICA' || $value->rol_concepto == 'INSPECTOR DE FASES DE APROBACION'){
                $na_flujo=1000;
            }
        }
        $containerOpenBrackets='<center><div class="btn-group" role="group" style="margin-bottom: 5px;">';
        $containerCloseBrackets='</div></center>';
        
        $btnEditar='<button type="button" class="btn btn-sm btn-log bg-primary" title="Ver o editar" onClick="editarListaReq(' . $id_req . ');"><i class="fas fa-edit fa-xs"></i></button>';
        $btnDetalleRapido='<button type="button" class="btn btn-sm btn-log bg-maroon" title="Ver detalle rápido" onClick="viewFlujo(' . $id_req . ', ' . $num_doc . ');"><i class="fas fa-eye fa-xs"></i></button>';
        $btnSolicitudCotizacion=' <button type="button" class="btn btn-sm btn-log btn-info" title="Crear solicitud de cotización" onClick="crearCoti(' . $id_req . ');"><i class="fas fa-file fa-xs"></i></button>';
        $btnAprobar=' <button type="button" class="btn btn-sm btn-log bg-green" title="Aprobar" onClick="atender_requerimiento(' . $id_req . ', ' . $num_doc . ', ' . $na_flujo . ', ' . $ap_apr . ');"><i class="fas fa-check fa-xs"></i></button>';
        $btnObservar='<button type="button" class="btn btn-sm btn-log bg-yellow" title="Observar" onClick="atender_requerimiento(' . $id_req . ', ' . $num_doc . ', ' . $na_flujo . ', ' . $ap_obs . ');"><i class="fas fa-exclamation-triangle fa-xs"></i><span class="badge badge-light">'.$cantidad_observados.'</span></button>';
        $btnDenegar='<button type="button" class="btn btn-sm btn-log bg-red" title="Denegar" onClick="atender_requerimiento(' . $id_req . ', ' . $num_doc . ', ' . $na_flujo . ', ' . $ap_dng . ');"><i class="fas fa-ban fa-xs"></i></button>';
        $btnTracking='<button type="button" class="btn btn-sm btn-log bg-primary" title="Explorar Requerimiento" onClick="tracking_requerimiento(' . $id_req . ');"><i class="fas fa-globe fa-xs"></i></button>';

        $estElaborado= '<center><label class="label label-default">Elaborado</label></center>';
        $estPendienteAprob= '<center><label class="label label-default">Pendiente Aprobación</label></center>';
        $estAprobado= '<center><label class="label label-primary">Aprobado</label></center>';
        $estAtendido= '<center><label class="label label-success">Atentido</label></center>';
        $estEnAlmacen= '<center><label class="label label-primary">En Almacén</label></center>';
        $estProcesado= '<center><label class="label label-success">Procesado</label></center>';
        $estSustentado= '<center><label class="label label-info">Sustentado</label></center>';
        $estObservado= '<center><label class="label label-warning">Observado</label></center>';
        $estDenegado= '<center><label class="label label-danger">Denegado</label></center>';
        $estAnulado= '<center><label class="label label-black">Anulado</label></center>';
        
        foreach($userRoles as $value){
            if($value->rol_concepto == 'JEFE DE LOGISTICA' || $value->rol_concepto == 'INSPECTOR DE FASES DE APROBACION'){
                if($id_est != 4){ // diferente a estado doc= denegado
                    $aprobs .=  $btnObservar;
                }
            }
        }
        // if($cnc_rol =='JEFE DE LOGISTICA' || $cnc_rol =='INSPECTOR DE FASES DE APROBACION'){
        //     if($id_est != 4){ // diferente a estado doc= denegado
        //         $aprobs .=  $btnObservar;
        //     }
        // }
        // $hasObsDetalleReq= $this->hasObsDetReq($id_req);// cantidad  valores  =t en  alm_det_req


        $descripcion_rol_last_obs_log='';
        if($id_flujo_last_aprob >0){

            // buscar orden de la ultima aprobacion  mediante el id_flujo de la ultima aprobacion
            $nro_orden_last_aprob = $this->get_nro_orden_by_flujo($id_flujo_last_aprob,$id_operacion);

            // obterner el id_rol de la siguiente aprobacion
            $next_apro = $this->next_aprob($nro_orden_last_aprob+1,$id_operacion);
            $id_rol_next_aprob = $next_apro['rol_aprob']?$next_apro['rol_aprob']:0;
            $id_flujo_next_apro = $next_apro['flujo'];
            
            // evaluar si existe un criterio de monto y prioridad
            $hasCriterios=$this->hasCriterios($id_flujo_next_apro);
            if($hasCriterios['id_criterio_monto'] > 0){
                $criterioMonto=$this->getCriterioMonto($hasCriterios['id_criterio_monto']);
                if($criterioMonto['id_criterio_monto'] >0){
                    $montoCM1=$criterioMonto['monto1'];
                    $signoCM1=$criterioMonto['signo1'];
                    $montoCM2=$criterioMonto['monto2'];
                    $signoCM2=$criterioMonto['signo2'];
                    // if($criterioMonto['id_operador1'] >0){
                    //     $isAllowedCriterioMonto=$this->evalateCriterioMonto($montoReq,$montoCM1,$signoCM1,$montoCM2,$signoCM2);
                    //     if($isAllowedCriterioMonto == true){
                    //     //si el monto esta dentro del rango - continuar nivel

                    //     }else{
                    //     //si el mont0 NO esta dentro del rango - saltar nivel
                    //     }
                    // }

                }

            }
            

            // si al usuario le corresponde una aprobacion.
            // if($id_rol_next_aprob == $id_cnc_rol){ // le corrresponde aprobacion
            if(in_array($id_rol_next_aprob, $userRolConceptoList)){ // le corrresponde aprobacion

                switch ($id_est) {
                    case '12': // si estado dedocumento es pendiente aprobacion
                        $status = '<center><label class="text-black">Pendiente Aprobación</label></center>';
                        $method .= $btnDetalleRapido;
                        $aprobs .=  $btnAprobar.$btnObservar.$btnDenegar;
                        break;

                    case '2': // aprobado
                        $status = $estAprobado;
                        $method .= $btnDetalleRapido;
                        // if($next_apro >0){
                        //     $status = '<center><label class="label label-primary">Aprobado '.$nro_orden_last_aprob.'</label></center>';
                        // }
                        break;
                    case '3': // observado
                        $status = $estObservado;
                        $method .= $btnDetalleRapido.$btnEditar;
                        break;

                        case '4': // denegado
                        $status = $estDenegado;
                        $method .= $btnDetalleRapido;
                        break;

                    case '5': // atendido
                        $status = $estAtendido;
                        $method .= $btnDetalleRapido;
                        break;

                    case '6': // en almacen
                        $status = $estEnAlmacen;
                        $method .= $btnDetalleRapido;
                        break;

                    case '9': // procesado
                        $status = $estProcesado;
                        $method .= $btnDetalleRapido;
                        break;
                    case '13': // sustentado
                        $status = $estSustentado;
                        $method .= $btnDetalleRapido;
                        if(($descripcion_rol_last_obs_log =='JEFE DE LOGISTICA' || $descripcion_rol_last_obs_log =='INSPECTOR DE FASES DE APROBACION')  && $id_vobo_last_obs_log ==3 && trim($estado)=="Sustentado"){
                            $aprobs .=  '';

                        }else{

                            $aprobs .=  $btnAprobar.$btnObservar.$btnDenegar;
                        }
                        break;
    

                    default:
                        $method= $btnDetalleRapido;
                        # code...
                        break;
                }
            }else{ // si next_apro_id_rol = 0 
                switch ($id_est) {
                    case '2': // aprobado
                        $status = $estAprobado;
                        $method .= $btnDetalleRapido;

                        // if($next_apro >0){
                        //     $status = '<center><label class="label label-primary">Aprobado '.$nro_orden_last_aprob.'</label></center>';
                        // }
                        foreach($userRoles as $value){
                            if($value->rol_concepto == 'COTIZADOR' || $value->rol_concepto == 'JEFE DE LOGISTICA' || $value->rol_concepto == 'INSPECTOR DE FASES DE APROBACION'){
                                if($cantidad_aprobados == $totalFlujo){
                                    $method .=  $btnSolicitudCotizacion;
                                }
                            }
                        }
                        break;
                    case '3': // observado
                        $status = $estObservado;
                        $method .= $btnDetalleRapido;
                        if($userId == $id_usu){
                            $method .= $btnEditar;
                        }
                        break;

                    case '12': // Pendiente    
                        $status = $estPendienteAprob;
                        $method .= $btnDetalleRapido;
                        break;
                    case '13': // sustentado    
                        $status = $estSustentado;
                        $method .= $btnDetalleRapido;
                        break;

                    case '5': // atendido
                        $status = $estAtendido;
                        $method .= $btnDetalleRapido;
                        break;
                }
            }



        }else{ // no tiene ninguna aprobacion



            switch ($id_est) {

                    case '1': // elaborado
                        $status = $estElaborado;
                        $method .= $btnDetalleRapido;
                        if($userId ==$id_usu){
                            $method .= $btnEditar;
                        }
                        break;
                    case '2': // aprobado
                        $status = $estAprobado;
                        $method .= $btnDetalleRapido;

                        foreach($userRoles as $value){
                            if($value->rol_concepto == 'JEFE DE LOGISTICA' || $value->rol_concepto == 'INSPECTOR DE FASES DE APROBACION'){
                                    $aprobs .=  $btnSolicitudCotizacion;
                            }
                        }
                        break;
                    case '4': // denegado
                        $status = $estDenegado;
                        $method .= $btnDetalleRapido;
                        break;

                    case '5': // atendido
                        $status = $estAtendido;
                        $method .= $btnDetalleRapido;
                        break;

                    case '6': // en almacen
                        $status = $estEnAlmacen;
                        $method .= $btnDetalleRapido;
                        break;

                    case '9': // procesado
                        $status = $estProcesado;
                        $method .= $btnDetalleRapido;
                        break;

                    case '12': // pendiente aprobacion    
                        $status = $estPendienteAprob;
                        $method .= $btnDetalleRapido;

                    break;
                    case '13': // sustentado    
                        $status = $estSustentado;
                        $method .= $btnDetalleRapido;
                        // if($hasObsDetalleReq >0){ // aun existe un det_req con obs = t
                        //     $status = '<center><label class="label labe-info">Sustentado </br>['.$hasObsDetalleReq.' Obs. Pend.]</label></center>';
                        //     $method .= $btnEditar;
                        // }

                    break;
                    case '3': // observado
                        $status = $estObservado;
                        $method .= $btnDetalleRapido;
                        if($userId == $id_usu){
                            $method .= $btnEditar;
                        }
                        break;
                    
                    default:
                        $status ='--';
                        $method .= $btnDetalleRapido;
                        break;
                    }
                    
                 
                $cantidad_aprobados = $this->consult_aprob($num_doc); 
                if($cantidad_aprobados == "" || $cantidad_aprobados == null ){
                    // $status = '<center><label class="text-black">AUN NINGUNA APROBACIÓN</label></center>';
                }
                if($cantidad_aprobados <= 0){
                    $statusConsultaPrimeraAprob = $this->consulta_req_primera_aprob($id_req)['status'];
                    if($statusConsultaPrimeraAprob =='success'){
                        $id_rol_first_aprob = $this->consulta_req_primera_aprob($id_req)['id_rol'];

                        if(in_array($id_rol_first_aprob, $userRolConceptoList)){ // le corrresponde la primera aprobacion
                            if($id_est != 4){ // diferente a estado doc= denegado
                                $aprobs .=  $btnAprobar.$btnObservar.$btnDenegar;
                            }
    
                        }
                        if(in_array($id_rol_first_aprob, $userRolConceptoList) && $id_est ==3){ // primera aprobacion , observa
                            $aprobs =  $btnObservar;
                        }
                    }elseif($statusConsultaPrimeraAprob =='fail'){
                        $status = '<center><label class="text-black">Error</label></center>';
                        $aprobs = '';
                        $method = '';
                    }

                }
        }
        $groupMethod = $containerOpenBrackets. $method.$btnTracking. $containerCloseBrackets;
        $groupAprob = $containerOpenBrackets. $aprobs. $containerCloseBrackets;
        $action = $groupMethod . $groupAprob;

        // $output['data'][] = array($flag, $codigo, $concepto,$monto_total_referencial, $fec_rq, $desc_periodo, $tp_req, $empresa, $gral, $usuario, $status.'<center>'.$indicadorCotizacion.$indicadorOrden.'</center>', $action);
        $output[] = [
            'flag'=> $flag, 
            'codigo'=> $codigo, 
            'concepto'=> $concepto,
            'monto_total_referencial'=> $monto_total_referencial,
            'fec_rq'=> $fec_rq, 
            'desc_periodo'=> $desc_periodo, 
            'tp_req'=> $tp_req,
            'empresa'=> $empresa, 
            'gral'=> $gral, 
            'usuario'=> $usuario, 
            'status'=> $status.'<center>'.$indicadorCotizacion.$indicadorOrden.'</center>', 
            'action'=> $action
        ];
        
    }
        // return response()->json($output);
        return DataTables::of($output)
        ->addColumn('flag',function($output){
                $flag = $output['flag'];
                return $flag;
        })
        ->addColumn('status',function($output){
                $status = $output['status'];
                return $status;
        })
        ->addColumn('action',function($output){
                $action = $output['action'];
                return $action;
        })
        ->rawColumns(['flag','status','action'])
        ->make(true);
    }


    // function hasObsDetReq($idReq){ 
    //     $ObsDetReq = DB::table('almacen.alm_det_req')
    //     ->where([
    //         ['alm_det_req.obs', '=', 't'],
    //         ['alm_det_req.id_requerimiento', '=', $idReq]

    //     ])
    //     ->count();
    //     return $ObsDetReq;
    // }

    public function mostrar_nombre_grupo($id_grupo){
        $sql = DB::table('administracion.adm_grupo')
        ->select('adm_grupo.id_grupo','adm_grupo.descripcion')
        ->where('adm_grupo.id_grupo', $id_grupo)
        ->get();
    

        if ($sql->count() > 0) {
            $id_grupo = $sql->first()->id_grupo;
            $descripcion = $sql->first()->descripcion;
        }else{
            $id_grupo=0;
            $descripcion='';
        }
        $array = array('id_grupo' => $id_grupo, 'descripcion' => $descripcion);
        return $array;
    }


    function mostrar_requerimiento_id($id, $type)
    {
        $sql = DB::table('almacen.alm_req')
            ->leftJoin('administracion.adm_periodo', 'adm_periodo.id_periodo', '=', 'alm_req.id_periodo')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.id_estado_doc', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
            ->leftJoin('administracion.adm_grupo', 'alm_req.id_grupo', '=', 'adm_grupo.id_grupo')
            ->leftJoin('administracion.sis_sede', 'adm_grupo.id_sede', '=', 'sis_sede.id_sede')
            ->leftJoin('administracion.adm_empresa', 'sis_sede.id_empresa', '=', 'adm_empresa.id_empresa')
            ->leftJoin('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            ->leftJoin('proyectos.proy_op_com', 'proy_op_com.id_op_com', '=', 'alm_req.id_op_com')
            ->select(
                'alm_req.*',
                'adm_periodo.descripcion as descripcion_periodo',
                'adm_estado_doc.estado_doc',
                'alm_tp_req.descripcion AS tipo_requerimiento',
                'adm_prioridad.descripcion AS priori',
                'adm_grupo.descripcion AS grupo',
                'adm_area.descripcion AS area',
                'proy_op_com.codigo as codigo_op_com',
                'proy_op_com.descripcion as descripcion_op_com',
                'alm_req.concepto AS alm_req_concepto',            
                'alm_req.estado',
                'adm_contri.razon_social',
                'sis_sede.codigo as codigo_sede'
            )
            ->where('alm_req.id_requerimiento', '=', $id)->get();
        $html = '';

        foreach ($sql as $row) {
            $code = $row->codigo;
            $motivo = $row->concepto;
            $empresa_sede = $row->razon_social.' - '.$row->codigo_sede;
            $id_usu = $row->id_usuario;
            $grupo = $row->id_grupo;
            $area_id = $row->id_area;
            $id_op_com = $row->id_op_com;
            $date = date('d/m/Y', strtotime($row->fecha_requerimiento));
            $id_periodo = $row->id_periodo;
            $descripcion_periodo = $row->descripcion_periodo;
            $moneda = $row->id_moneda;
            $codigo_occ = $row->codigo_occ;

            $infoGrupo = $this->mostrar_nombre_grupo($grupo);

            if ($infoGrupo['descripcion'] == 'Proyectos') {
                if ($id_op_com != null) {
                    $destino = $row->descripcion_op_com;
                } else {
                    $destino = $row->area . ' - GASTOS ADMINISTRATIVOS';
                }
            } else {
                if ($area_id != 6) {
                    $destino = $row->area;
                } else {
                    $destino = $row->area . ' - ' . $row->occ;
                }
            }

            $responsable = Usuario::find($id_usu)->trabajador->postulante->persona->nombre_completo;
            $simbol = $this->consult_moneda($moneda);
        }

        $html =
            '<table width="100%">
            <thead>
                <tr>
                    <th width="140">Código:</th>
                    <td>' . $code . '</td>
                </tr>
                <tr>
                    <th width="140">Motivo:</th>
                    <td>' . $motivo . '</td>
                </tr>
                <tr>
                    <th width="140">Empresa:</th>
                    <td>' . $empresa_sede . '</td>
                </tr>
                <tr>
                    <th width="140">Responsable:</th>
                    <td>' . $responsable . '</td>
                </tr>
                <tr>
                    <th>Area o Servicio:</th>
                    <td>' . $destino . '</td>
                </tr>';
                if($destino == 'COMERCIAL'){
                    $html.='<tr>
                                <th>OCC:</th>
                                <td>' . $codigo_occ . '</td>
                            </tr>';
                }

        $html.='<tr>
                    <th>Fecha:</th>
                    <td colspan="2">' . $date . '</td>
                </tr>
                <tr>';
        if ($type == 1) {
            $html .=
                '<th>Moneda:</th>
                    <td>' . $simbol . '</td>';
        } elseif ($type == 2) {
            $html .=
                '<th>Moneda:</th>
                    <td>' . $simbol . '</td>
                    <td width="100" align="right"><button class="btn btn-primary" onClick="imprimirReq(' . $id . ');"><i class="fas fa-print"></i> Imprimir formato</button></td>
                    <td>&nbsp;</td>
                    <td width="100" align="right"><button class="btn btn-info" onClick="verArchivosAdjuntosRequerimiento(' . $id . ');"><i class="fas fa-folder"></i> Archivos Adjuntos</button></td>';
        }
        $html .=
            '</tr>
            <tr>
                <th>Periodo:</th>
                <td colspan="2">' . $descripcion_periodo . '</td>
            </tr>
            </thead>
        </table>
        <br>
        <table class="table table-bordered table-striped table-view-okc" width="100%">';
        if ($type == 1) {
            $html .=
                '<thead style="background-color:#5c5c5c; color:#fff;">
                    <th>N°</th>
                    <th>Descripción del Bien o Servicio</th>
                    <th width="150">Partida</th>
                    <th width="90">Fecha Entrega</th>
                    <th width="90">Unidad</th>
                    <th width="100">Cantidad</th>
                    <th width="100">Precio Unit.</th>
                    <th width="110">Subtotal</th>
                </thead>
                <tbody>';
        } elseif ($type == 2) {
            $html .=
                '<thead style="background-color:#5c5c5c; color:#fff;">
                    <th width="30">N°</th>
                    <th>Descripción del Bien o Servicio</th>
                    <th width="150">Partida</th>
                    <th width="90">Fecha Entrega</th>
                    <th width="90">Unidad</th>
                    <th width="100">Cantidad</th>
                    <th width="100">Precio Unit.</th>
                    <th width="110">Subtotal</th>
                </thead>
                <tbody>';
        }

        $cont = 1;
        $total = 0;

        $detail = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.*', 'alm_und_medida.descripcion as unidad_medida_descripcion')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            ->where('id_requerimiento', $id)
            ->get();

        foreach ($detail as $clave => $det) {
            $id_det = $det->id_detalle_requerimiento;
            $id_item = $det->id_item;
            $precio = $det->precio_referencial;
            $cant = $det->cantidad;
            $obs = $det->obs;
            $id_part = $det->partida;
            $fecha_entrega = $det->fecha_entrega;
            $unit = $det->unidad_medida_descripcion;
            $active = '';

            if (is_numeric($id_part)) {
                $name_part = DB::table('finanzas.presup_par')->select('codigo')->where('id_partida', $id_part)->first();
                $partida = $name_part->codigo;
            } else {
                $partida = ''/*$id_part*/;
            }

            $subtotal = $precio * $cant;
            $total += $subtotal;
            $unidad='S/N';
            if ($id_item != null) {
                $prod = DB::table('almacen.alm_item')
                    ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
                    ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
                    ->select('alm_prod.descripcion AS producto', 'log_servi.descripcion AS servicio', 'alm_item.id_producto', 'alm_item.id_servicio', 'alm_item.id_equipo')
                    ->where('alm_item.id_item', $id_item)->first();
                $name = ($prod->id_producto != null) ? $prod->producto : $prod->servicio;
                $unidad = ($prod->id_servicio > 0) ? 'Servicio' : (($prod->id_equipo > 0) ? 'Equipo' : 'S/N');
                
            } else {
                $name = $det->descripcion_adicional;
            }

            if ($obs == 't' or $obs == '1' or $obs == 'true') {
                $active = 'checked="checked" disabled';
            }

            if ($type == 1) {
                $html .=
                    '<tr>
                    <td> ' . ($clave+1) . '</td>
                    <td>' . $name . '</td>
                    <td>' . $partida . '</td>
                    <td>' . $fecha_entrega . '</td>
                    <td>' . $unit . '</td>
                    <td class="text-right">' . number_format($cant, 3) . '</td>
                    <td class="text-right">' . number_format($precio, 2) . '</td>
                    <td class="text-right">' . number_format($subtotal, 2) . '</td>
                </tr>';
            } elseif ($type == 2) {
                $html .=
                    '<tr>
                    <td>' . $cont . '</td>
                    <td>' . $name . '</td>
                    <td>' . $partida . '</td>
                    <td>' . $fecha_entrega . '</td>
                    <td>' . ($unit ? $unit : $unidad) . '</td>
                    <td class="text-right">' . number_format($cant, 3) . '</td>
                    <td class="text-right">' . number_format($precio, 2) . '</td>
                    <td class="text-right">' . number_format($subtotal, 2) . '</td>
                </tr>';
            }

            $cont++;
        }

        $html .=
            '<tr>
            <th colspan="7" class="text-right">Total:</th>
            <td class="text-right">' . number_format($total, 2) . '</td>
        </tr>
        </tbody></table>';

        // if ($type == 1){
        //     return response()->json($html);
        // }elseif ($type == 2){
        //     return $html;
        // }
        return $html;
    }

    function get_id_operacion($id_grp,$id_area, $tp_doc){
        $filterBy=[];
        if($id_area == 0 && $id_grp >0 ){
            $filterBy =[['adm_operacion.id_grupo', '=', $id_grp]];
        }else if($id_area > 0 && $id_grp > 0){
            $filterBy =[['id_area', '=', $id_area],['id_grupo', '=', $id_grp]];
        }

        $sql = DB::table('administracion.adm_operacion')
        ->where([
            $filterBy[0], 
            ['id_tp_documento', '=', $tp_doc], 
            ['estado', '=', 1] 
            ])
        ->get();
        if ($sql->count() > 0) {
            $operacion = $sql->first()->id_operacion;
        }else{
            $operacion=0;
        }
        return $operacion;
    }
    function get_id_grupo($req){
        $sql = DB::table('almacen.alm_req')
        ->where([['id_requerimiento', '=', $req] ])
        ->get();
        if ($sql->count() > 0) {
            $id_grupo = $sql->first()->id_grupo;
        }else{
            $id_grupo=0;
        }
        return $id_grupo;
    }
    function get_id_area($req){
        $sql = DB::table('almacen.alm_req')
        ->where([['id_requerimiento', '=', $req] ])
        ->get();
        if ($sql->count() > 0) {
            $id_area = $sql->first()->id_area;
        }else{
            $id_area=0;
        }
        return $id_area;
    }
    function get_id_prioridad($req){
        $sql = DB::table('almacen.alm_req')
        ->where([['id_requerimiento', '=', $req] ])
        ->get();
        if ($sql->count() > 0) {
            $id_prioridad = $sql->first()->id_prioridad;
        }else{
            $id_prioridad=0;
        }
        return $id_prioridad;
    }
    
    function consult_nivel_aprob($roles, $ope)
    {
        $sql = DB::table('administracion.adm_flujo')
            ->where([
                ['id_operacion', '=', $ope],
                ['estado', '=', 1]
                ])
                ->whereIn('id_rol',$roles)
            ->get();

        if ($sql->count() > 0) {
            $flujo = $sql->first()->id_flujo;
            $orden = $sql->first()->orden;
            $id_rol = $sql->first()->id_rol;
        } else {
            $flujo = 0;
            $orden = 0;
            $id_rol = '';
        }

        $array = array('orden' => $orden, 'flujo' => $flujo, 'rol_aprob' => $id_rol);
        return $array;
    }

    function consult_doc_aprob($id_doc,$tp_doc)
    {
        $sql = DB::table('administracion.adm_documentos_aprob')->where([['id_tp_documento', '=', $tp_doc], ['id_doc', '=', $id_doc]])->get();

        if ($sql->count() > 0) {
            $val = $sql->first()->id_doc_aprob;
        } else {
            $val = 0;
        }

        return $val;
    }
 

    function last_obs_log($doc)
    {
        $sql = DB::table('administracion.adm_aprobacion')
        ->select('adm_aprobacion.*')
        ->where([['id_vobo', '=', 3],['id_rol', '=', 5], ['id_doc_aprob', '=', $doc]])
        ->orderby('fecha_vobo', 'desc')
        ->get();

        if ($sql->count() > 0) {
            $id_flujo = $sql->first()->id_flujo;
            $id_vobo = $sql->first()->id_vobo;
            $id_rol = $sql->first()->id_rol;
        } else {
            $id_flujo = 0;
            $id_vobo = 0;
            $id_rol = '';
        }
         $array = array('id_flujo' => $id_flujo, 'id_vobo' => $id_vobo, 'id_rol' => $id_rol);
        return $array;
    }

    function get_id_rol_concepto_by_rol($id_rol){
        $id=0;
        $rrhh_rol=DB::table('rrhh.rrhh_rol')
        ->select('rrhh_rol.id_rol_concepto')
        ->where('id_rol','=',$id_rol)
        ->get();
        if($rrhh_rol->count()){
            $id=$rrhh_rol->first()->id_rol_concepto;
        }else{
            $id=0;
        }
        return $id;
    }
    function get_id_rol_by_id_trab_id_rol_concepto($id_trabajador,$id_rol_concepto_list){
        
        $rrhh_rol=DB::table('rrhh.rrhh_rol')
        ->select('rrhh_rol.id_rol')
        ->where('id_trabajador','=',$id_trabajador)
        ->whereIn('id_rol_concepto',$id_rol_concepto_list)
        ->get();
        $id_rol_list=[];
        foreach($rrhh_rol as $data){
            $id_rol_list[]=$data->id_rol;
        }
        return $id_rol_list;
    }

    function get_id_area_by_id_rol($id_rol){
         
        $id=0;
        $rrhh_rol=DB::table('rrhh.rrhh_rol')
        ->select('rrhh_rol.id_area')
        ->where('id_rol','=',$id_rol)
        ->get();
        if($rrhh_rol->count()){
            $id=$rrhh_rol->first()->id_area;
        }else{
            $id=0;
        }
        return $id;
    }

    function idRolConceptoEnableToApprovedList($doc,$id_operacion_list){
        $output=[];
        $sql1 = DB::table('administracion.adm_aprobacion')
        ->select('adm_aprobacion.*')
        ->where([['id_vobo', '=', 1], ['id_doc_aprob', '=', $doc]])
        ->orderby('fecha_vobo', 'desc')
        ->get();
        $id_rol_list=[];
        foreach($sql1 as $d){
            $id_rol_concepto=$this->get_id_rol_concepto_by_rol($d->id_rol);
            array_push($id_rol_list,$id_rol_concepto);
        }

        $sql2 = DB::table('administracion.adm_flujo')
        ->select('adm_flujo.id_rol')
        ->whereIn('id_operacion', $id_operacion_list)
        ->get();
        $id_rol_flujo_list=[];

        foreach($sql2 as $f){
            $id_rol_flujo_list[]=$f->id_rol;
        }

        if(count($id_rol_list)>0){
            $output = array_diff($id_rol_flujo_list, $id_rol_list);
        }

        return $output;
    }

    function getIdOperacionByIdGrupoList($idGrupoList,$tipoDoc,$estado){
        $sql = DB::table('administracion.adm_operacion')
        ->select('adm_operacion.id_operacion')
        ->whereIn('id_grupo', $idGrupoList)
        ->where([
            ['id_tp_documento', '=', $tipoDoc],
            ['estado', '=', $estado]
            ])
        ->get();

        $id_operacion_list=[];
        if($sql){
            foreach($sql as $data){
                $id_operacion_list[]=$data->id_operacion;
            }
        }
        return $id_operacion_list;
    }

    function getIdRolByIdOpeList($idOperacionList){
        $sql = DB::table('administracion.adm_flujo')
        ->select('adm_flujo.id_rol')
        ->whereIn('id_operacion',$idOperacionList)  
        ->where([
            ['estado', '=', 1]
            ])     
        ->get();
        $id_rol_list=[];
        if($sql){
            foreach($sql as $data){
                $id_rol_list[]=$data->id_rol;
            }
        }
        return $id_rol_list;
    }

    function last_aprob($doc)
    {
        $sql = DB::table('administracion.adm_aprobacion')
        ->select('adm_aprobacion.*')
        ->where([['id_vobo', '=', 1], ['id_doc_aprob', '=', $doc]])
        ->orderby('fecha_vobo', 'desc')
        ->get();

        if ($sql->count() > 0) {
            $id_flujo = $sql->first()->id_flujo;
            $id_vobo = $sql->first()->id_vobo;
            $id_rol = $sql->first()->id_rol;
        } else {
            $id_flujo = 0;
            $id_vobo = 0;
            $id_rol = '';
        }
         $array = array('id_flujo' => $id_flujo, 'id_vobo' => $id_vobo, 'id_rol' => $id_rol);
        return $array;
    }

    function size_flujo($id_operacion)
    {
        $flujo = DB::table('administracion.adm_flujo')->where([
            ['id_operacion', '=', $id_operacion], 
            ['estado', '=', 1]])
            ->get();
            return $flujo->count();

    }

    function consult_tamaño_flujo($id_req)
    {
        $id_tipo_doc = $this->get_id_tipo_documento('Requerimiento');

        $req = DB::table('almacen.alm_req')
        ->where([
            ['id_requerimiento', '=', $id_req], 
            ['estado', '=', 1]])
        ->first();
        // $id_prioridad = $req->id_prioridad;
        $id_prioridad = 1;
        $id_grupo = isset($req->id_grupo)?$req->id_grupo:0;
        $id_area = isset($req->id_area)?$req->id_area:0;

        $sql_operacion = DB::table('administracion.adm_operacion')
        ->where([
            ['id_grupo', '=', $id_grupo],
            ['id_area', '=', $id_area],
            ['id_tp_documento', '=', 1],
            ['estado', '=', $id_tipo_doc]])
            ->get();
        if ($sql_operacion->count() > 0) {
            $id_operacion = $sql_operacion->first()->id_operacion;
        }else{
            $id_operacion=0;
        }

        $flujo = DB::table('administracion.adm_flujo')->where([
            ['id_operacion', '=', $id_operacion], 
            ['estado', '=', 1]])
            ->get();

        return $flujo->count();

    }
    function consult_aprob($doc)
    {
        $sql = DB::table('administracion.adm_aprobacion')->where([['id_vobo', '=', 1], ['id_doc_aprob', '=', $doc]])->get();
        return $sql->count();
    }
    function consult_obs($id_req)
    {
        $sql = DB::table('almacen.req_obs')->where([['id_requerimiento', '=', $id_req]])->get();
        return $sql->count();
    }

    function consult_estado($req)
    {
        $sql = DB::table('almacen.alm_req')->select('id_estado_doc')->where('id_requerimiento', $req)->first();
        return $sql->id_estado_doc;
    }

    function consult_usuario_elab($req)
    {
        $sql = DB::table('almacen.alm_req')->select('id_usuario')->where('id_requerimiento', $req)->first();
        return $sql->id_usuario;
    }
    function consulta_req_primera_aprob($req)
    {
        $id_tipo_doc = $this->get_id_tipo_documento('Requerimiento');
        $message='';
        $statusOption=['success','fail'];
        $status='';
        $output=[];

        $sql1 = DB::table('almacen.alm_req')->select('id_grupo','id_area')->where('id_requerimiento', $req)->get();
        if(sizeof($sql1) > 0){
            $sql11 = DB::table('administracion.adm_operacion')->where([['id_grupo', $sql1->first()->id_grupo],['id_area', $sql1->first()->id_area],['id_tp_documento', $id_tipo_doc],['estado', 1]])->get();
            if(sizeof($sql11) > 0){
                    $sql2 = DB::table('administracion.adm_flujo')->where([['id_operacion', $sql11->first()->id_operacion],['estado', 1]])
                    ->orderby('orden', 'asc')
                    ->get();

                    $nombre = ($sql2->count() > 0) ? $sql2->first()->nombre: '';
                    $id_rol = ($sql2->count() > 0) ? $sql2->first()->id_rol: '';
                    $status=$statusOption[0];
                    $array = array('nombre' => $nombre, 'id_rol' => $id_rol, 'status'=>$status, 'message'=>'Flujo Encontrado');
                    
                    return $array;

            }else{
                $message='No existe id operacion con id_area='.$sql1->first()->id_area.',id_grupo='.$sql1->first()->id_grupo;
                $status=$statusOption[1];
                $output=['message'=>$message,'status'=>$status];

                $sql111 = DB::table('administracion.adm_operacion')->where([['id_grupo', $sql1->first()->id_grupo],['id_area',null],['id_tp_documento', 1],['estado', 1]])->get();
                if(sizeof($sql111) > 0){
                    $sql2 = DB::table('administracion.adm_flujo')->where([['id_operacion', $sql111->first()->id_operacion],['estado', 1]])
                    ->orderby('orden', 'asc')
                    ->get();

                    $nombre = ($sql2->count() > 0) ? $sql2->first()->nombre: '';
                    $id_rol = ($sql2->count() > 0) ? $sql2->first()->id_rol: '';
                    $status=$statusOption[0];
                    $array = array('nombre' => $nombre, 'id_rol' => $id_rol, 'status'=>$status, 'message'=>'Flujo Encontrado');
                    
                    return $array;
                }else{
                    $message='No existe id operacion con id_area= null, id_grupo='.$sql1->first()->id_grupo;
                    $status=$statusOption[1];
                    $output=['message'=>$message,'status'=>$status];
    
                }


                return $output;

            }
        }else{
            $message='No existe id requerimiento';
            $status=$statusOption[1];
            $output=['message'=>$message,'status'=>$status];
            return $output;
        }


    }

    // function consult_rol_aprob($rol)
    // {
    //     $sql = DB::table('rrhh.rrhh_rol')
    //         ->join('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
    //         ->select('rrhh_rol_concepto.descripcion')->where('rrhh_rol.id_rol', '=', $rol)->first();
    //     return $sql->descripcion;
    // }
    // function consult_id_rol_aprob($roles)
    // {
    //     $sql = DB::table('rrhh.rrhh_rol')
    //         ->join('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
    //         ->select('rrhh_rol_concepto.id_rol_concepto')->whereIn('rrhh_rol.id_rol', $roles)->first();
    //     return $sql->id_rol_concepto;
    // }

    function get_nro_orden_by_flujo($id_flujo,$id_ope){
        $sql = DB::table('administracion.adm_flujo')
        ->select('orden')->where([
            ['id_operacion', '=', $id_ope],
            ['id_flujo', '=', $id_flujo], 
            ['estado', '=', 1]])
        ->get();

        if ($sql->count() > 0) {
            $orden = $sql->first()->orden;

        }else{
            $orden=0;
        }

        return $orden;

    }

    function next_aprob($orden,$operacion)
    {
        $sql = DB::table('administracion.adm_flujo')->select('adm_flujo.*')
        ->where([
            ['adm_flujo.id_operacion', '=', $operacion], 
            ['adm_flujo.orden', '=', $orden], 
            ['adm_flujo.estado', '=', 1]])
        ->get();
        if ($sql->count() > 0) {
            $flujo = $sql->first()->id_flujo;
            $orden = $sql->first()->orden;
            $id_rol = $sql->first()->id_rol;
        } else {
            $flujo = 0;
            $orden = 0;
            $id_rol = '';
        }
        $array = array('orden' => $orden, 'flujo' => $flujo, 'rol_aprob' => $id_rol);
        return $array;
    }

    function cantidad_actual_observaciones($id_req){
        $sql = DB::select("SELECT
        count(req_obs.id_observacion) as cantidad_obs
        FROM
            almacen.req_obs
                where id_observacion in(SELECT MAX(id_observacion) as obs from almacen.req_obs
                WHERE id_requerimiento=".$id_req." and req_obs.estado=1
                GROUP BY id_requerimiento, id_usuario ORDER BY obs ASC)");

        return $sql[0]->cantidad_obs;
    }

    function get_header_observacion($id_req){
        
        $sql_obs_req = DB::select("SELECT req_obs.id_observacion, req_obs.id_usuario, 
        (rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) as nombre_completo, 
        req_obs.descripcion, req_obs.estado 
        FROM almacen.req_obs
        LEFT JOIN configuracion.sis_usua on sis_usua.id_usuario = req_obs.id_usuario 
        LEFT JOIN rrhh.rrhh_trab on rrhh_trab.id_trabajador = sis_usua.id_trabajador 
        LEFT JOIN rrhh.rrhh_postu on rrhh_postu.id_postulante = rrhh_trab.id_postulante 
        LEFT JOIN rrhh.rrhh_perso on rrhh_perso.id_persona = rrhh_postu.id_persona
        where id_observacion in(SELECT MAX(id_observacion) as obs from almacen.req_obs
        WHERE id_requerimiento=".$id_req." and req_obs.estado=1
        GROUP BY id_requerimiento, id_usuario ORDER BY obs ASC)");




        $id_usu_list=[];
        $obs=[];
        if(isset($sql_obs_req) && count($sql_obs_req)>0){
            foreach ($sql_obs_req as $key => $value) {
            $id_usu_list[]=$value->id_usuario;
            
            $obs[]=[
                'id_usuario'=> $value->id_usuario, 
                'nombre_completo'=> $value->nombre_completo, 
                'descripcion'=>$value->descripcion,
                'estado'=>$value->estado
            ];
            }

        
        }
        // $sql_obs_req[0]->obs_item=[];

        // $sql_obs_req_det = DB::select("SELECT req_obs.id_observacion, req_obs.id_usuario, CONCAT(rrhh_perso.nombres,' ' ,rrhh_perso.apellido_paterno,' ' ,rrhh_perso.apellido_materno) as nombre_completo, descripcion FROM almacen.req_obs
        // LEFT JOIN configuracion.sis_usua on sis_usua.id_usuario = req_obs.id_usuario 
        // LEFT JOIN rrhh.rrhh_trab on rrhh_trab.id_trabajador = sis_usua.id_trabajador 
        // LEFT JOIN rrhh.rrhh_postu on rrhh_postu.id_postulante = rrhh_trab.id_postulante 
        // LEFT JOIN rrhh.rrhh_perso on rrhh_perso.id_persona = rrhh_postu.id_persona
        // where id_observacion in(SELECT MAX(id_observacion) as obs
        // FROM almacen.req_obs
        // WHERE id_requerimiento = ".$id_req."  AND id_usuario IN (".implode(",", $id_usu_list).")
        // GROUP BY id_requerimiento ORDER BY obs ASC)");
        
        // if(isset($sql_obs_req_det) && count($sql_obs_req_det)>0){
            

            // foreach ($sql_obs_req as $value1) {
            //     foreach ($sql_obs_req_det as $value2) {
            //         if($value1->id_usuario == $value2->id_usuario){
            //             $value1->obs_item[] = [
            //                                 'id_detalle_requerimiento'=>$value2->id_detalle_requerimiento,
            //                                 'descripcion'=>$value2->descripcion
            //                                 ];
            //         }
            //     }
            // }
        // }

        // DB::table('almacen.alm_req_obs')
        // ->select(['id_usuario as id_usuario','descripcion as descripcion'])
        // ->whereIn('id_observacion', function($query) 
        //     {
        //     $query->select(DB::raw('almacen.alm_req_obs.id_observacion'))
        //     ->from('almacen.alm_req_obs')
        //     ->where('accion', '=', 'OBSERVADO')
        //     ->whereNull('id_detalle_requerimiento')
        //     ->where('id_requerimiento', '=', 1)
        //     ->groupBy('id_requerimiento', 'id_usuario' )
        //     // ->orderBy('almacen.alm_req_obs.id_observacion', 'ASC')
        //     ->max('alm_req_obs.id_observacion');
        // })
        // ->get();
        

        return $sql_obs_req;

    }

    function get_observacion($id_req,$idFlujo,$idRol,$idVobo){
        $req_obs = DB::table('almacen.alm_req_obs')
        ->select('alm_req_obs.descripcion')
        ->where([ ['alm_req_obs.id_requerimiento', '=', $id_req], ['alm_req_obs.id_usuario', '=', $idRol], 
        ['alm_req_obs.accion', '=', 'OBSERVADO']])
        ->orderby('fecha_registro', 'desc')
        ->first();

        // $det_obs = DB::table('almacen.alm_req_obs')
        // ->select('alm_req_obs.descripcion')
        // ->where([ ['alm_req_obs.id_requerimiento', '=', $idDoc], ['adm_aprobacion.id_usuario', '=', $idRol], ['adm_aprobacion.id_vobo', '=', $req_obs->id_usuario]])
        // ->orderby('fecha_vobo', 'desc')
        // ->get();
 
        return $req_obs;
    }

    

    function consult_sgt_aprob($orden,$operacion)
    {
        $sql = DB::table('administracion.adm_flujo')
                ->select('id_rol')
                ->where([['id_operacion', '=', $operacion], ['orden', '=', $orden], ['estado', '=', 1]])
                ->first();
        $rol = $sql->id_rol;

        $trab = DB::table('rrhh.rrhh_trab')
            ->select('rrhh_perso.nombres', 'rrhh_perso.apellido_paterno', 'rrhh_perso.apellido_materno', 'rrhh_rol_concepto.descripcion AS rol')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->join('rrhh.rrhh_rol', 'rrhh_rol.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->join('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->where('rrhh_rol.id_rol_concepto', $rol)->first();
        $nombre = $trab->nombres . ' ' . $trab->apellido_paterno . ' - ' . $trab->rol;
        return $nombre;
    }

    function consult_moneda($id)
    {
        $sql = DB::table('configuracion.sis_moneda')->select('descripcion')->where('id_moneda', '=', $id)->first();
        return $sql->descripcion;
    }

    // function totalAprobOp($operacion)
    // {
    //     $sql = DB::table('administracion.adm_flujo')->where([['id_operacion', '=', 1], ['estado', '=', 1]])->get();
    //     return $sql->count();
    // }

    function aprobar_requerimiento(Request $request)
    {
        $userSession=$this->userSession();
        // $usuario = Auth::user();
        $id_req = $request->id_documento;
        $doc_ap = $request->doc_aprobacion;
        $flujos = $request->flujo;
        $idvobo = 1;
        $motivo = $request->motivo;
        $id_usu = $userSession['id_usuario'];
        $id_rol = $request->id_rol;
        $idarea = $request->id_area;

        // $rolesUsuario = $usuario->trabajador->roles;
        // foreach ($rolesUsuario as $role) {
        //     $idarea = $role->pivot->id_area;
        // }

        $hoy = date('Y-m-d H:i:s');
        $insertar = DB::table('administracion.adm_aprobacion')->insertGetId(
            [
                'id_flujo'              => $flujos,
                'id_doc_aprob'          => $doc_ap,
                'id_vobo'               => $idvobo,
                'id_usuario'            => $id_usu,
                'id_area'               => $idarea,
                'fecha_vobo'            => $hoy,
                'detalle_observacion'   => $motivo,
                'id_rol'                => $id_rol
            ],
            'id_aprobacion'
        );
        if ($insertar > 0) {
            $totalFlujo = $this->consult_tamaño_flujo($id_req);
            $totalAprob = $this->consult_aprob($doc_ap);
            if ($totalFlujo == 1) {
                $data = DB::table('almacen.alm_req')->where('id_requerimiento', $id_req)->update(['id_estado_doc' => 2]);
                if ($data) {
                    $rpta = 'ok';
                } else {
                    $rpta = 'no_actualiza';
                }
            }else{
                if ($totalFlujo > $totalAprob) {
                    $data = DB::table('almacen.alm_req')->where('id_requerimiento', $id_req)->update(['id_estado_doc' => 12]);
                    if ($data) {
                        $rpta = 'ok';
                    } else {
                        $rpta = 'no_actualiza';
                    }
                } else {
                    $data = DB::table('almacen.alm_req')->where('id_requerimiento', $id_req)->update(['id_estado_doc' => 2]);
                    if ($data) {
                        $rpta = 'ok';
                    } else {
                        $rpta = 'no_actualiza';
                    }
                }
            }


        } else {
            $rpta = 'no_guarda';
        }

        return response()->json($rpta);
    }

    function get_data_requerimiento($req){
        $data = DB::table('almacen.alm_req')
        ->select('alm_req.*')
        ->where('alm_req.id_requerimiento', $req)->first();
        return $data;
    }

    function observar_requerimiento_vista($req, $doc)
    {
        $id_area= $this->get_id_area($req);
        $codigo= $this->get_data_requerimiento($req)->codigo;
        $html = $this->mostrar_requerimiento_id($req, 1);
        $array = array('view' => $html, 'id_req' => $req, 'id_area'=>$id_area,'codigo'=>$codigo );
        return response()->json($array);
    }


    function observar_requerimiento(Request $request)
    {
        $id_req = $request->id_requerimiento;
        $doc_ap = $request->doc_req;
        $flujos = $request->flujo_req;
        // $idvobo = 3; //observado
        $motivo = $request->motivo_req;
        $usuario = Auth::user();
        $id_usu = $usuario->id_usuario;
        $id_rol = $usuario->login_rol;
        $hoy = date('Y-m-d H:i:s');
        $rpta='';
        $msgSendEmail='';
        $status=0;
            $insertar = DB::table('almacen.req_obs')->insertGetId(
                [
                    'id_requerimiento'        => $id_req,
                    'id_doc_aprob'            => $doc_ap,
                    'descripcion'             => $motivo,
                    'id_usuario'              => $id_usu,
                    'fecha_registro'          => $hoy,
                    'estado'                  => 1
                ],
                'id_observacion'
            );

        if ($insertar > 0) {
            $estado_observado= $this->get_estado_doc('Observado');

            $data = DB::table('almacen.alm_req')
            ->where('id_requerimiento', $id_req)
            ->update(['id_estado_doc' => $estado_observado]);
            if ($data) {
                $rpta = 'Se Observo el Documento';
                $sendEmail = $this->componer_email_notificacion($doc_ap,$motivo,$id_usu);
                $msgSendEmail = $sendEmail['mensaje'];
                $status = 200;

            } else {
                $rpta = 'problema al actualizar el estado del requerimiento';
                $status = 500;
            }
        } else {
            $rpta = 'problema al registrar la observación';
            $status = 500;

        }

        $ouput=['mensaje'=>$rpta.', '.$msgSendEmail,'status'=>$status];
        
        return response()->json($ouput);
    }

    function componer_email_notificacion($id_doc_aprob,$motivo,$id_usu){
        $documentos = DB::table('administracion.adm_documentos_aprob')
            ->select('adm_documentos_aprob.*')
            ->where([
                ['adm_documentos_aprob.id_doc_aprob', '=', $id_doc_aprob]
            ])
            ->get();

            $id_doc=0;
            $id_tp_documento=0;
            $id_usuario_propietario=0;
            $payload=[];
            $estado_envio=[];

            if ($documentos->count() > 0) {
                $id_doc = $documentos->first()->id_doc;
                $id_tp_documento = $documentos->first()->id_tp_documento;
            }

            if($id_tp_documento == 1){
                $req = DB::table('almacen.alm_req')
                ->select('alm_req.*')
                ->where([
                    ['alm_req.id_requerimiento', '=', $id_doc]
                ])
                ->get();

                if ($req->count() > 0) {
                    $codigo = $req->first()->codigo;
                    $id_usuario_propietario = $req->first()->id_usuario;

                    $data_propietario= $this->get_data_usuario($id_usuario_propietario);
                    $data_observador= $this->get_data_usuario($id_usu);
                    $payload=[
                        'id_usuario_propietario'=>$id_usuario_propietario,
                        'email_propietario'=>$data_propietario['email'],
                        'nombre_completo_usuario_observado'=>$data_observador['nombre_completo'],
                        'id_usuario_observador'=>$id_usu,
                        'motivo'=>$motivo,
                        'codigo_documento'=>$codigo
                    ];

                    $estado_envio =(new CorreoController)->enviar_correo_a_usuario($payload);

                }


            }
            

        return $estado_envio;
    }

    function get_data_usuario($id_usurio){
        $email='';
        $ouput=[];
        $sql = DB::table('configuracion.sis_usua')
        ->select('rrhh_postu.*',
        DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno)  AS nombre_completo ")
        )
        ->leftJoin('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'sis_usua.id_trabajador')
        ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
        ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
        ->where([
            ['sis_usua.id_usuario', '=', $id_usurio]
        ])
        ->get();
        if ($sql->count() > 0) {
            $email = $sql->first()->correo;
            $nombre_completo = $sql->first()->nombre_completo;
            $ouput=['nombre_completo'=>$nombre_completo,'email'=>$email];
        }
        return $ouput;
    }

    function denegar_requerimiento(Request $request)
    {
        $usuario = Auth::user();
        $id_req = $request->id_documento;
        $doc_ap = $request->doc_aprobacion;
        $flujos = $request->flujo;
        $idvobo = 2;
        $motivo = $request->motivo;
        $id_usu = $usuario->id_usuario;
        $id_rol = $usuario->login_rol;

        $rolesUsuario = $usuario->trabajador->roles;
        $idarea = 0;

        foreach ($rolesUsuario as $role) {
            $idarea = $role->pivot->id_area;
        }

        $hoy = date('Y-m-d H:i:s');

        $insertar = DB::table('administracion.adm_aprobacion')->insertGetId(
            [
                'id_flujo' => $flujos,
                'id_doc_aprob' => $doc_ap,
                'id_vobo' => $idvobo,
                'id_usuario' => $id_usu,
                'id_area' => $idarea,
                'fecha_vobo' => $hoy,
                'detalle_observacion' => $motivo,
                'id_rol' => $id_rol
            ],
            'id_aprobacion'
        );

        if ($insertar > 0) {
            $data = DB::table('almacen.alm_req')->where('id_requerimiento', $id_req)->update(['id_estado_doc' => 4]);
            if ($data) {
                $rpta = 'ok';
            } else {
                $rpta = 'no_actualiza';
            }
        } else {
            $rpta = 'no_guarda';
        }
        return response()->json($rpta);
    }

    function guardar_sustento(Request $request)
    {
        $id_req = $request->id_requerimiento_sustento;
        $id_obs = $request->id_observacion_sustento;
        $motivo = $request->motivo_sustento;
        $usuario = Auth::user();
        $id_usu = $usuario->id_usuario;
        $hoy = date('Y-m-d H:i:s');
        $num_doc = $this->consult_doc_aprob($id_req,1); 
        
        $cantidad_obs = $this->cantidad_actual_observaciones($id_req);
        $cantidad_aprobados = $this->consult_aprob($num_doc); 

        if($cantidad_obs === 1){ //cambiar estado de requerimiento
            if($cantidad_aprobados === 0){
                DB::table('almacen.alm_req')->where('id_requerimiento', $id_req)->update(['id_estado_doc' => 1]); // estado Sustentado
            }else{
                DB::table('almacen.alm_req')->where('id_requerimiento', $id_req)->update(['id_estado_doc' => 2]); // estado Aprobado
            }
        }

        
        if ($id_obs > 0) {
            $insertar_sust = DB::table('almacen.req_sust')->insertGetId(
                [
                    'descripcion'               => $motivo,
                    'id_usuario'                => $id_usu,
                    'fecha_registro'            => $hoy,
                    'estado'                    => 1
                ],
                'id_sustentacion'
            );

            if ($insertar_sust > 0) {
                $update_req_obs= DB::table('almacen.req_obs')
                ->where([
                    ['id_requerimiento', $id_req],
                    ['id_observacion', $id_obs]])
                ->update([
                    'id_sustentacion' => $insertar_sust,
                    'estado' => 0
                ]);
                
                if ($update_req_obs) {
                    $rpta = 'ok';
                } else {
                    $rpta = 'no_actualiza';
                }
            } else {
                $rpta = 'no_guarda';
            }
        } 
        else {

            if ($insertar_sust > 0) {
                $rpta = 'ok';
            } else {
                $rpta = 'no_guarda';
            }
        }

        $output = ["status"=>$rpta, 'data'=>$id_req];

        return response()->json($output);
        // return response()->json($cantidad_aprobados);
    }

    function consulta_nombre_usuario($id_rol){
        $query = DB::table('administracion.rol_aprobacion')
        ->select(
        DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno)  AS nombre_completo")
        )
        ->where([
            ['rol_aprobacion.id_rol_concepto', '=', $id_rol],
            ['rol_aprobacion.estado', '=', 1]
        ])
        ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rol_aprobacion.id_trabajador')
        ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
        ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')

        ->orderby('rol_aprobacion.id_rol_aprobacion','desc')
        ->get();
        return $query;

    }


    function flujo_aprobacion($req, $doc)
    {
        
        $cont = 1;
        $footer='';
        
        $dataFinal = array();
        $alert = '<ul style="list-style: none; padding: 0;">';

        $dataFinal = $this->get_historial_aprobacion($req, $doc);

        foreach ($dataFinal as $value => $val) {
            $usu = $val['usuario'];
            $est = $val['estado'];
            $day = $val['fecha'];
            $obs = $val['obs'];
            $name_user = $val['nombre_usuario'];

            if (strtoupper($est) == 'ELABORADO') {
                $claseObs = 'alert-okc alert-okc-primary';
            } elseif (strtoupper($est) == 'OBSERVADO') {
                $claseObs = 'alert-okc alert-okc-warning';
            } elseif (strtoupper($est) == 'DENEGADO') {
                $claseObs = 'alert-okc alert-okc-danger';
            } elseif (strtoupper($est) == 'SUSTENTO') {
                $claseObs = 'alert-okc alert-okc-info';
            } elseif (strtoupper($est) == 'APROBADO') {
                $claseObs = 'alert-okc alert-okc-success';
            }


            $alert .=
            '<li class="' . $claseObs . '" style="padding: 5px; margin-bottom: 8px;">
            <strong>' . strtoupper($est) . ' - ' . $name_user . '</strong>
            <small>(' . date('d/m/Y H:i:s', strtotime($day)) . ')</small>
            <br>' . $obs . '
            </li>';

            $cont++;

            foreach ($val['detalle'] as $key => $value) {

                if(count($val['detalle'][$key]) > 0){
                    
                    $usu_sus = $value['usuario'];
                    $est_sus = $value['estado'];
                    $day_sus = $value['fecha'];
                    $obs_sus = $value['obs'];
                    $name_user_sus = $value['nombre_usuario'];

                    if (strtoupper($est_sus) == 'ELABORADO') {
                        $claseObs = 'alert-okc alert-okc-primary';
                    } elseif (strtoupper($est_sus) == 'OBSERVADO') {
                        $claseObs = 'alert-okc alert-okc-warning';
                    } elseif (strtoupper($est_sus) == 'DENEGADO') {
                        $claseObs = 'alert-okc alert-okc-danger';
                    } elseif (strtoupper($est_sus) == 'SUSTENTO') {
                        $claseObs = 'alert-okc alert-okc-info';
                    } elseif (strtoupper($est_sus) == 'APROBADO') {
                        $claseObs = 'alert-okc alert-okc-success';
                    }

                    $alert .=
                    '<li class="' . $claseObs . '" style="padding: 5px; margin-bottom: 8px;">
                    <strong>' . strtoupper($est_sus) .' - ' . $name_user_sus . '</strong>
                    <small>(' . date('d/m/Y H:i:s', strtotime($day_sus)) . ')</small>
                    <br>' . $obs_sus . '
                </li>';


                }
            }

        }
        $estado_req = $this->consult_estado($req); // get id_estado_doc
        // $totalFlujo = $this->totalAprobOp(1);
        // $totalAprob = $this->consult_aprob($doc); // cantidad aprobaciones


        $id_grupo = $this->get_id_grupo($req);
        $id_area = $this->get_id_area($req);

        $num_doc = $this->consult_doc_aprob($req,1); 
        $total_aprob = $this->consult_aprob($num_doc);
        $total_flujo = $this->consult_tamaño_flujo($req);
        $areaOfRolAprob = $this->getAreaOfRolAprob($num_doc,1); //{num doc},{tp doc} 

        $tp_doc=1; // tipo de documento = requerimiento 
        $id_operacion= $this->get_id_operacion($id_grupo,$areaOfRolAprob['id'],$tp_doc);

        // $sgt_aprob='-';
        // $sgt_per='-';

        if ($estado_req == 12 ) {
            if ($total_aprob > 0) {
                if ($total_flujo > $total_aprob) {
                    $sgt_aprob = ($total_aprob + 1);
                    $sgt_per = $this->consult_sgt_aprob($sgt_aprob,$id_operacion);
                    $footer .= '<strong>Próximo en aprobar: </strong>' . $sgt_per;
                }

            }
        } elseif ($estado_req == 3) {
            $usuario_crea = $this->consult_usuario_elab($req);
            $usu_elab = Usuario::find($usuario_crea)->trabajador->postulante->persona->nombre_completo;
            $footer .= '<strong>Por sustentar </strong>' . $usu_elab;
        } elseif ($estado_req == 13) {
            if ($total_flujo > $total_aprob) {
                $sgt_aprob = ($total_aprob + 1);
                $sgt_per = $this->consult_sgt_aprob($sgt_aprob,$id_operacion);
                $footer .= '<strong>Próximo en aprobar: </strong>' . $sgt_per;
            }

        } elseif($estado_req ==1) {
            $PrimeraApro = $this->consulta_req_primera_aprob($req);
            $usuPrimeraApro = $PrimeraApro['nombre'];
            $rolPrimeraApro = $PrimeraApro['id_rol'];
            $nameUserPrimeraApro = $this->consulta_nombre_usuario($rolPrimeraApro);
            $json = json_decode($nameUserPrimeraApro);
            $allnameUserPrimeraApro = implode(", ", array_map(function($obj) { foreach ($obj as $p => $v) { return $v;} }, $json));

            $footer = '<strong>Pendiente </strong><abbr title="'.$allnameUserPrimeraApro.'">'. $usuPrimeraApro.'</abbr>';
        }elseif($estado_req ==2){
            $footer='<strong>Aprobado</strong>';

        } 

        // if($sql3->first()->observacion != null){
        //     $footer .= ' <strong> Por Aceptar Sustento:</strong> Logistica' ;
        // }

        $reqs = $this->mostrar_requerimiento_id($req, 2);

        $data = ['flujo' => $alert, 'siguiente' => $footer, 'requerimiento' => $reqs, 'cont' => $cont];
        return response()->json($data);
    }
    /* Rocio */
    public function listaCotizacionesPorGrupo($id_cotizacion = null)
    {

        $output['data'] = $this->get_cotizacion_list($id_cotizacion);
        return response()->json($output);
    }

    public function get_cotizacion_list($id_cotizacion=null){
        $whereCoti=[
                    ['log_cotizacion.estado', '=', 1],
                    ['log_valorizacion_cotizacion.estado', '!=', 7] // No esten anulado
        ];
        if($id_cotizacion != null && $id_cotizacion > 0 ){
            $whereCoti[]= ['log_cotizacion.id_cotizacion', '=', $id_cotizacion];

        }

        $cotizaciones = DB::table('logistica.log_cotizacion')
        ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
        ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
        ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
        ->leftJoin('contabilidad.adm_contri as contri', 'contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi as identi', 'identi.id_doc_identidad', '=', 'contri.id_doc_identidad')
        ->leftJoin('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_datos_contacto', '=', 'log_cotizacion.id_contacto')
        ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
        ->leftJoin('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
        ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'valoriza_coti_detalle.id_requerimiento')
        ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
        ->leftJoin('logistica.log_grupo_cotizacion', 'log_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
        ->leftJoin('administracion.adm_estado_doc as estado1', 'estado1.id_estado_doc', '=', 'log_cotizacion.estado')
        ->leftJoin('administracion.adm_estado_doc as estado2', 'estado2.id_estado_doc', '=', 'log_cotizacion.estado_envio')
        ->select(
            'log_grupo_cotizacion.id_grupo_cotizacion',
            'log_grupo_cotizacion.codigo_grupo',
            'log_cotizacion.id_cotizacion',
            'log_cotizacion.codigo_cotizacion',
            'log_cotizacion.id_proveedor',
            'log_cotizacion.id_contacto',
            'adm_ctb_contac.nombre as nombre_contacto',
            'adm_ctb_contac.email as email_contacto',
            'adm_ctb_contac.telefono as telefono_contacto',
            'adm_ctb_contac.dni as dni_contacto',
            'log_cotizacion.estado_envio',
            'estado2.estado_doc as descripcion_estado_envio',
            'log_cotizacion.estado',
            'estado1.estado_doc as descripcion_estado',
            'log_cotizacion.id_empresa',
            'log_cotizacion.fecha_registro',

            'adm_contri.id_contribuyente',
            'adm_contri.razon_social',
            'adm_contri.nro_documento',
            'adm_contri.id_doc_identidad',
            'sis_identi.descripcion as nombre_doc_identidad',

            'contri.razon_social as razon_social_empresa',
            'contri.nro_documento as nro_documento_empresa',
            'contri.id_doc_identidad as id_doc_identidad_empresa',
            'identi.descripcion as nombre_doc_idendidad_empresa',
            DB::raw("(SELECT  COUNT(log_valorizacion_cotizacion.id_cotizacion) FROM logistica.log_valorizacion_cotizacion
            WHERE log_valorizacion_cotizacion.id_cotizacion = log_cotizacion.id_cotizacion)::integer as cantidad_items"),
            'alm_req.id_requerimiento',
            'alm_req.codigo AS codigo_requerimiento'

        )
        ->where($whereCoti)
        ->orderby('log_cotizacion.fecha_registro','desc')
        // ->whereIn('alm_req.id_requerimiento',$auxIdReq)
        ->get();

        $cotizacionAux = [];
        $cotizacionList = [];
        $requerimiento__cotizacion = [];

        foreach ($cotizaciones as $data) {
            $requerimiento__cotizacion[] = [
                'id_cotizacion' => $data->id_cotizacion,
                'id_requerimiento' => $data->id_requerimiento,
                'codigo_requerimiento' => $data->codigo_requerimiento
            ];
            if (in_array($data->id_cotizacion, $cotizacionAux) === false) {
                $cotizacionAux[] = $data->id_cotizacion;
                $cotizacionList[] = [
                    'id_grupo_cotizacion' => $data->id_grupo_cotizacion,
                    'codigo_grupo' => $data->codigo_grupo,
                    'id_cotizacion' => $data->id_cotizacion,
                    'codigo_cotizacion' => $data->codigo_cotizacion,
                    'id_proveedor' => $data->id_proveedor,
                    'id_contacto' => $data->id_contacto,
                    'dni_contacto' => $data->dni_contacto,
                    'nombre_contacto' => $data->nombre_contacto,
                    'email_contacto' => $data->email_contacto,
                    'telefono_contacto' => $data->telefono_contacto,
                    'id_contribuyente' => $data->id_contribuyente,
                    'estado_envio' => $data->estado_envio,
                    'descripcion_estado_envio' => $data->descripcion_estado_envio,
                    'fecha_registro' => $data->fecha_registro,
                    'estado' => $data->estado,
                    'descripcion_estado' => $data->descripcion_estado,
                    'id_empresa' => $data->id_empresa,
                    'razon_social' => $data->razon_social,
                    'nro_documento' => $data->nro_documento,
                    'id_doc_identidad' => $data->id_doc_identidad,
                    'nombre_doc_identidad' => $data->nombre_doc_identidad,
                    'razon_social_empresa' => $data->razon_social_empresa,
                    'nro_documento_empresa' => $data->nro_documento_empresa,
                    'id_doc_identidad_empresa' => $data->id_doc_identidad_empresa,
                    'nombre_doc_idendidad_empresa' => $data->nombre_doc_idendidad_empresa,
                    'cantidad_items' => $data->cantidad_items
                ];
            }
        }

        $aux = [];
        for ($k = 0; $k < sizeof($requerimiento__cotizacion); $k++) {
            if (in_array($requerimiento__cotizacion[$k]['id_cotizacion'] . $requerimiento__cotizacion[$k]['id_requerimiento'], $aux) === false) {
                $aux[] = $requerimiento__cotizacion[$k]['id_cotizacion'] . $requerimiento__cotizacion[$k]['id_requerimiento'];
                $requerimientos_cotiza[] = $requerimiento__cotizacion[$k];
            }
        }

        $aux = [];
        $req = '';
        for ($i = 0; $i < sizeof($cotizacionList); $i++) {
            for ($k = 0; $k < sizeof($requerimientos_cotiza); $k++) {
                if ($cotizacionList[$i]['id_cotizacion'] == $requerimientos_cotiza[$k]['id_cotizacion']) {
                    $cotizacionList[$i]['requerimiento'][] = $requerimientos_cotiza[$k];
                }
            }
        }
        return $cotizacionList;
    }
    public function get_cotizacion_by_req($id_requerimiento){
        $cotizaciones = DB::table('logistica.log_cotizacion')
        ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
        ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
        ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
        ->leftJoin('contabilidad.adm_contri as contri', 'contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi as identi', 'identi.id_doc_identidad', '=', 'contri.id_doc_identidad')
        ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
        ->leftJoin('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
        ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'valoriza_coti_detalle.id_requerimiento')
        ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
        ->leftJoin('logistica.log_grupo_cotizacion', 'log_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
        ->select(
            'log_grupo_cotizacion.id_grupo_cotizacion',
            'log_grupo_cotizacion.codigo_grupo',
            'log_cotizacion.id_cotizacion',
            'log_cotizacion.codigo_cotizacion',
            'log_cotizacion.id_proveedor',
            'log_cotizacion.email_proveedor',
            'log_cotizacion.estado_envio',
            'log_cotizacion.estado',
            'log_cotizacion.id_empresa',
            'log_cotizacion.fecha_registro',

            'adm_contri.id_contribuyente',
            'adm_contri.razon_social',
            'adm_contri.nro_documento',
            'adm_contri.id_doc_identidad',
            'sis_identi.descripcion as nombre_doc_identidad',

            'contri.razon_social as razon_social_empresa',
            'contri.nro_documento as nro_documento_empresa',
            'contri.id_doc_identidad as id_doc_identidad_empresa',
            'identi.descripcion as nombre_doc_idendidad_empresa',
            DB::raw("(SELECT  COUNT(log_valorizacion_cotizacion.id_cotizacion) FROM logistica.log_valorizacion_cotizacion
            WHERE log_valorizacion_cotizacion.id_cotizacion = log_cotizacion.id_cotizacion)::integer as cantidad_items"),
            'alm_req.id_requerimiento',
            'alm_req.codigo AS codigo_requerimiento'

        )
        ->where([
            ['log_cotizacion.estado', '=', 1],
            ['log_valorizacion_cotizacion.estado', '!=', 7], //no anulados
            ['alm_req.id_requerimiento', '=', $id_requerimiento] 
        ])
        // ->whereIn('alm_req.id_requerimiento',$id_requerimiento)
        ->get();

        $cotizacionAux = [];
        $cotizacionList = [];
        $requerimiento__cotizacion = [];

        foreach ($cotizaciones as $data) {
            $requerimiento__cotizacion[] = [
                'id_cotizacion' => $data->id_cotizacion,
                'id_requerimiento' => $data->id_requerimiento,
                'codigo_requerimiento' => $data->codigo_requerimiento
            ];
            if (in_array($data->id_cotizacion, $cotizacionAux) === false) {
                $cotizacionAux[] = $data->id_cotizacion;
                $cotizacionList[] = [
                    'id_grupo_cotizacion' => $data->id_grupo_cotizacion,
                    'codigo_grupo' => $data->codigo_grupo,
                    'id_cotizacion' => $data->id_cotizacion,
                    'codigo_cotizacion' => $data->codigo_cotizacion,
                    'id_proveedor' => $data->id_proveedor,
                    'id_contribuyente' => $data->id_contribuyente,
                    'email_proveedor' => $data->email_proveedor,
                    'estado_envio' => $data->estado_envio,
                    'estado' => $data->estado,
                    'fecha_registro' => $data->fecha_registro,
                    'id_empresa' => $data->id_empresa,
                    'razon_social' => $data->razon_social,
                    'nro_documento' => $data->nro_documento,
                    'id_doc_identidad' => $data->id_doc_identidad,
                    'nombre_doc_identidad' => $data->nombre_doc_identidad,
                    'razon_social_empresa' => $data->razon_social_empresa,
                    'nro_documento_empresa' => $data->nro_documento_empresa,
                    'id_doc_identidad_empresa' => $data->id_doc_identidad_empresa,
                    'nombre_doc_idendidad_empresa' => $data->nombre_doc_idendidad_empresa,
                    'cantidad_items' => $data->cantidad_items
                ];
            }
        }

        $aux = [];
        for ($k = 0; $k < sizeof($requerimiento__cotizacion); $k++) {
            if (in_array($requerimiento__cotizacion[$k]['id_cotizacion'] . $requerimiento__cotizacion[$k]['id_requerimiento'], $aux) === false) {
                $aux[] = $requerimiento__cotizacion[$k]['id_cotizacion'] . $requerimiento__cotizacion[$k]['id_requerimiento'];
                $requerimientos_cotiza[] = $requerimiento__cotizacion[$k];
            }
        }

        $aux = [];
        $req = '';
        for ($i = 0; $i < sizeof($cotizacionList); $i++) {
            for ($k = 0; $k < sizeof($requerimientos_cotiza); $k++) {
                if ($cotizacionList[$i]['id_cotizacion'] == $requerimientos_cotiza[$k]['id_cotizacion']) {
                    $cotizacionList[$i]['requerimiento'][] = $requerimientos_cotiza[$k];
                }
            }
        }
        return $cotizacionList;
    }

    public function cargar_almacenes($id_sede){
        $data = DB::table('almacen.alm_almacen')
        ->select('alm_almacen.id_almacen','alm_almacen.id_sede','alm_almacen.codigo','alm_almacen.descripcion',
        'sis_sede.descripcion as sede_descripcion','alm_tp_almacen.descripcion as tp_almacen')
        ->leftjoin('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
        ->join('almacen.alm_tp_almacen','alm_tp_almacen.id_tipo_almacen','=','alm_almacen.id_tipo_almacen')
        ->where([['alm_almacen.estado', '=', 1],
        ['alm_almacen.id_sede','=',$id_sede]])
        ->orderBy('codigo')
        ->get();
        return $data;
    }

    public function is_true($val, $return_null=false){
        $boolval = ( is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val );
        return ( $boolval===null && !$return_null ? false : $boolval );
    }

    public function detalle_requerimiento( Request $request )
    {
        
        $checkList= $request->data;
        $idReqList=[];

        foreach($checkList as $data){
            if($this->is_true($data['stateCheck']) == true){
                $idReqList[]= $data['id_req'];
            }
        }



        // return $idReqList;
            $det = DB::table('almacen.alm_det_req')
            ->select(
                'alm_det_req.*', 
                'alm_req.codigo as cod_req',
                'alm_und_medida.abreviatura as unidad_medida_detalle_req',
                'alm_almacen.descripcion as descripcion_almacen'
                
                )
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            ->leftjoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_det_req.id_almacen')

            ->whereIn('alm_det_req.id_requerimiento', $idReqList)
            ->get();
        
 
      

        $html = '';
        $i = 1;
        $payload=[];
        foreach ($det as $clave => $d) {
            $item = DB::table('almacen.alm_item')
                ->select(
                    'alm_item.*',
                    'alm_prod.id_producto',
                    'alm_prod.codigo as cod_producto',
                    'alm_prod.descripcion as des_producto',
                    'log_servi.codigo as cod_servicio',
                    'log_servi.descripcion as des_servicio',
                    'alm_und_medida.abreviatura as unidad_medida_item'
                )
                ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
                ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
                ->leftjoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
                ->where('id_item', $d->id_item)
                ->first();

            if (isset($item)) { // si existe variable
                
                if ($item->id_producto !== null || is_numeric($item->id_producto) == 1) {
                    $sedeReq = DB::table('almacen.alm_req')
                    ->select(
                        'adm_grupo.id_sede'
                    )
                    ->leftjoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
                    ->where('alm_req.id_requerimiento', $d->id_requerimiento)
                    ->first();
                    $almacenes  = $this->cargar_almacenes($sedeReq->id_sede);

                    $payload[]=[
                        'id_requerimiento'=>$d->id_requerimiento,
                        'id_detalle_requerimiento'=>$d->id_detalle_requerimiento,
                        'id_item'=>$d->id_item,
                        'id_tipo_item'=>$d->id_tipo_item,
                        'cod_req' =>$d->cod_req,
                        'descripcion_adicional'=>$d->descripcion_adicional,
                        'lugar_entrega'=>$d->lugar_entrega,
                        'fecha_entrega'=>$d->fecha_entrega,
                        'id_producto'=>$item->id_producto,
                        'cod_producto' =>$item->cod_producto?$item->cod_producto:$item->cod_servicio,
                        'des_producto' =>$item->des_producto?$item->des_producto:$item->des_servicio,
                        'unidad_medida_detalle_req' =>$d->unidad_medida_detalle_req?$d->unidad_medida_detalle_req:'',
                        'unidad_medida_item' =>$item->unidad_medida_item?$item->unidad_medida_item:'',
                        'cantidad' =>$d->cantidad,
                        'precio_referencial' =>$d->precio_referencial,
                        'stock_comprometido' =>$d->stock_comprometido,
                        'descripcion_almacen' =>$d->descripcion_almacen,
                        'almacen'=> $almacenes
                    ];
                }
            }else{
                $payload[]=[
                    'id_requerimiento'=>$d->id_requerimiento,
                    'id_detalle_requerimiento'=>$d->id_detalle_requerimiento,
                    'id_item'=>0,
                    'id_tipo_item'=>0,
                    'cod_req' =>$d->cod_req,
                    'descripcion_adicional'=>$d->descripcion_adicional,
                    'lugar_entrega'=>$d->lugar_entrega,
                    'fecha_entrega'=>$d->fecha_entrega,
                    'id_producto'=>0,
                    'cod_producto' =>0,
                    'des_producto' =>'',
                    'unidad_medida_detalle_req' =>$d->unidad_medida_detalle_req?$d->unidad_medida_detalle_req:'',
                    'unidad_medida_item' =>'',
                    'cantidad' =>$d->cantidad,
                    'precio_referencial' =>$d->precio_referencial,
                    'stock_comprometido' =>$d->stock_comprometido,
                    'descripcion_almacen' =>$d->descripcion_almacen,
                    'almacen'=> []
                ];
            }


                //     if($type_view =='VIEW_CHECKBOX'){
                //     $html .= '
                //         <tr>
                //             <td>
                //                 <input class="oculto" value="' . $d->id_requerimiento . '" name="id_requerimiento"/>
                //                 <input class="oculto" value="' . $d->id_detalle_requerimiento . '" name="id_detalle"/>
                //                 <input type="checkbox"/>
                //             </td>
                //             <td>' . $d->cod_req . '</td>
                //             <td>-</td>
                //             <td>' . $item->cod_producto . '</td>
                //             <td>' . $item->des_producto . '</td>
                //             <td>' . $item->abreviatura . '</td>
                //             <td>' . $d->cantidad . '</td>
                //             <td>' . $d->precio_referencial . '</td>
                //             <td> <input type="number" min="0" max="'.$d->cantidad.'" value="'.$d->stock_comprometido .'" class="form-control activation stock_comprometido" data-id-det-req="'.$d->id_detalle_requerimiento.'"  data-id-req="'.$d->id_requerimiento.'"name="stock_comprometido[]" disabled></td>
                //             <td>
                //                 <select class="form-control almacen_selected" name="" data-id-det-req="'.$d->id_detalle_requerimiento.'">';
                //                 foreach($almacenes as $al){
                //                     $html.='<option value="'.$al->id_almacen.'">'.$al->descripcion.'</option>';
                //                 }
                //         $html.='</select>
                //             </td>

                //         </tr>
                //     ';
                //     }else{
                //         $html .= '
                //         <tr>
                //             <td>
                //                 <input class="oculto" value="' . $d->id_requerimiento . '" name="id_requerimiento"/>
                //                 <input class="oculto" value="' . $d->id_detalle_requerimiento . '" name="id_detalle"/>';
                //         $html.= $clave;
                //         $html.='
                //             </td>
                //             <td>' . $d->cod_req . '</td>
                //             <td>' . $item->cod_producto . '</td>
                //             <td>' . $item->des_producto . '</td>
                //             <td>' . $item->abreviatura . '</td>
                //             <td>' . $d->cantidad . '</td>
                //             <td>' . $d->precio_referencial . '</td>
                //             <td>' . $d->stock_comprometido . '</td>
                //         </tr>
                //         ';
                //     }
                // } else if ($item->id_servicio !== null || is_numeric($item->id_servicio) == 1) {
                //     if($type_view =='VIEW_CHECKBOX'){
                //     $html .= '
                //         <tr>
                //             <td>
                //                 <input class="oculto" value="' . $d->id_requerimiento . '" name="id_requerimiento"/>
                //                 <input class="oculto" value="' . $d->id_detalle_requerimiento . '" name="id_detalle"/>';
                //                 '<input type="checkbox"/>
                //             </td>
                //             <td>' . $d->cod_req . '</td>
                //             <td>'.$item->codigo.'</td>
                //             <td>' . $item->cod_servicio . '</td>
                //             <td>' . $item->des_servicio . '</td>
                //             <td>serv</td>
                //             <td>' . $d->cantidad . '</td>
                //             <td>' . $d->precio_referencial . '</td>
                //             <td> <input type="number" min="0" max="'.$d->cantidad.'" value="'.$d->stock_comprometido .'" class="form-control activation stock_comprometido" data-id-det-req="'.$d->id_detalle_requerimiento.'"  data-id-req="'.$d->id_requerimiento.'"name="stock_comprometido[]" disabled></td>

                //         </tr>
                //         ';
                //     }else{
                //         $html .= '
                //         <tr>
                //             <td>
                //                 <input class="oculto" value="' . $d->id_requerimiento . '" name="id_requerimiento"/>
                //                 <input class="oculto" value="' . $d->id_detalle_requerimiento . '" name="id_detalle"/>';
                //         $html.= $clave;
                //         $html.= '
                //             </td>
                //             <td>' . $d->cod_req . '</td>
                //             <td>' . $item->cod_servicio . '</td>
                //             <td>' . $item->des_servicio . '</td>
                //             <td>serv</td>
                //             <td>' . $d->cantidad . '</td>
                //             <td>' . $d->precio_referencial . '</td>
                //             <td>' . $d->stock_comprometido . '</td>

                //         </tr>
                //         ';                        
                // ';
                //         ';                        
                //     }
                // }
            // } else { // si no existe | no existe id_item
            //     if($type_view =='VIEW_CHECKBOX'){
            //         $sedeReq = DB::table('almacen.alm_req')
            //         ->select(
            //             'adm_grupo.id_sede'
            //         )
            //         ->leftjoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            //         ->where('alm_req.id_requerimiento', $d->id_requerimiento)
            //         ->first();
            //         $almacenes  = $this->cargar_almacenes($sedeReq->id_sede);
            //     $html .= '
            //         <tr>
            //             <td>
            //                 <input class="oculto" value="' . $d->id_requerimiento . '" name="id_requerimiento"/>
            //                 <input class="oculto" value="' . $d->id_detalle_requerimiento . '" name="id_detalle"/>
            //                 <input type="checkbox"/>
            //             </td>
            //             <td>' . $d->cod_req . '</td>
            //             <td>-</td>
            //             <td>-</td>
            //             <td>' . $d->descripcion_adicional . '</td>
            //             <td>' . $d->abreviatura . '</td>
            //             <td>' . $d->cantidad . '</td>
            //             <td>' . $d->precio_referencial . '</td>
            //             <td> <input type="number" min="0" max="'.$d->cantidad.'" value="'.$d->stock_comprometido .'" class="form-control activation stock_comprometido" data-id-det-req="'.$d->id_detalle_requerimiento.'"  data-id-req="'.$d->id_requerimiento.'"name="stock_comprometido[]" disabled></td>
            //             <td>
            //                 <select class="form-control almacen_selected" name="" data-id-det-req="'.$d->id_detalle_requerimiento.'">';
            //                 foreach($almacenes as $al){
            //                     $html.='<option value="'.$al->id_almacen.'">'.$al->descripcion.'</option>';
            //                 }
            //         $html.='</select>
            //             </td>

            //         </tr>
            //     ';
            //     }else{
            //         $html .= '
            //         <tr>
            //             <td>
            //                 <input class="oculto" value="' . $d->id_requerimiento . '" name="id_requerimiento"/>
            //                 <input class="oculto" value="' . $d->id_detalle_requerimiento . '" name="id_detalle"/>';
            //         $html.= $clave;
            //         $html.='</td>
            //             <td>' . $d->cod_req . '</td>
            //             <td>0</td>
            //             <td>' . $d->descripcion_adicional . '</td>
            //             <td>' . $d->abreviatura . '</td>
            //             <td>' . $d->cantidad . '</td>
            //             <td>' . $d->precio_referencial . '</td>
            //             <td>' . $d->stock_comprometido . '</td>

            //         </tr>
            //     '; 
            //     }


        }
        return json_encode($payload);
    }

    public function guardar_proveedor(Request $request){
        try {
            DB::beginTransaction();

            $fecha = date('Y-m-d H:i:s');

            $exist = DB::table('contabilidad.adm_contri')
            ->where([['nro_documento','=',$request->nro_documento],['estado','!=',7]])
            ->first();

            $id_proveedor = 0;

            if ($exist == null){
                $id_contribuyente = DB::table('contabilidad.adm_contri')->insertGetId(
                    [
                        'id_tipo_contribuyente'=>$request->id_tipo_contribuyente, 
                        'id_doc_identidad'=>$request->id_doc_identidad, 
                        'nro_documento'=>$request->nro_documento, 
                        'razon_social'=>strtoupper($request->razon_social), 
                        'estado'=>1,
                        'fecha_registro'=>$fecha
                    ],
                        'id_contribuyente'
                    );
                $id_proveedor = DB::table('logistica.log_prove')->insertGetId(
                    [
                        'id_contribuyente'=>$id_contribuyente,
                        // 'codigo'=>'000',
                        'estado'=>1,
                        'fecha_registro'=>$fecha
                    ],
                        'id_proveedor'
                    );
            }
            
            DB::commit();
            return response()->json(['id_proveedor'=>$id_proveedor,'razon_social'=>strtoupper($request->razon_social)]);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
        // $data = DB::table('logistica.log_prove')
        //     ->select('log_prove.id_proveedor','adm_contri.nro_documento','adm_contri.razon_social')
        //     ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        //     ->where([['adm_contri.estado','=',1],['log_prove.estado','=',1]])->get();
        // $html = '';

        // foreach($data as $d){
        //     $output[] = ['id_proveedor'=>$id_proveedor, 'nro_documento'=>$request->nro_documento, 'razon_social'=>$request->razon_social];
        // }
        // return json_encode($output);
    }
    public function registrar_proveedor(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $arr = $request->data;

        foreach($arr as $data){
    
            $contri = DB::table('contabilidad.adm_contri')->insertGetId(
                [
                    'id_tipo_contribuyente' => $data['id_tipo_contribuyente'],
                    'id_doc_identidad' => $data['id_doc_identidad'],
                    'nro_documento' => $data['nro_documento'],
                    'razon_social' => $data['razon_social'],
                    'telefono' => $data['telefono'],
                    'direccion_fiscal' => $data['direccion_fiscal'],
                    'ubigeo' => $data['ubigeo'],
                    'id_pais' => $data['id_pais'],
                    'estado' => $data['estado'],
                    'fecha_registro' => $fecha,
                    'id_estado_ruc' => $data['id_estado_ruc'],
                    'id_condicion_ruc' => $data['id_condicion_ruc']
                ],
                'id_contribuyente'
            );

            $prov = DB::table('logistica.log_prove')->insertGetId(
                [
                    'id_contribuyente' => $contri,
                    'estado' => $data['estado'],
                    'fecha_registro' => $fecha
                ],
                'id_proveedor'
            );
        }
        return response()->json($prov);
 
    }
    public function update_proveedor(Request $request)
    {
        // $fecha = date('Y-m-d H:i:s');

        $arr = $request->data;

        foreach($arr as $data){
            $sql_contri =DB::table('logistica.log_prove')
            ->select(
                'log_prove.id_contribuyente'
            )
            ->where('id_proveedor',  $data['id_proveedor'])
            ->first();

            $contri = DB::table('contabilidad.adm_contri')
            ->where([
                ['id_contribuyente', $sql_contri->id_contribuyente]
            ])
            ->update(
                [
                    'id_tipo_contribuyente' => $data['id_tipo_contribuyente'],
                    'id_doc_identidad' => $data['id_doc_identidad'],
                    'nro_documento' => $data['nro_documento'],
                    'razon_social' => $data['razon_social'],
                    'direccion_fiscal' => $data['direccion_fiscal'],
                    'ubigeo' => $data['ubigeo'],
                    'telefono' => $data['telefono'],
                    'id_pais' => $data['id_pais'],
                    'id_estado_ruc' => $data['id_estado_ruc'],
                    'id_condicion_ruc' => $data['id_estado_ruc'],
                    'estado' => $data['estado']
                ],
                'id_contribuyente'
            );

        }
        return json_encode($contri);
    }

    public function registrar_establecimiento(Request $request)
    {
        // $fecha = date('Y-m-d H:i:s');
        $arr = $request->data;

        $sql_contri =DB::table('logistica.log_prove')
        ->select(
            'log_prove.id_contribuyente'
        )
        ->where('id_proveedor', $request->id_proveedor)
        ->first();

        foreach($arr as $data){
            
            $sql_contri =DB::table('logistica.log_prove')
            ->select(
                'log_prove.id_contribuyente'
            )
            ->where('id_proveedor',  $data['id_proveedor'])
            ->first();

            $establecimiento = DB::table('contabilidad.establecimiento')->insertGetId(
                [
                    'id_contribuyente' => $sql_contri->id_contribuyente,
                    'id_tipo_establecimiento' => $data['id_tipo_establecimiento'],
                    'direccion' => $data['direccion'],
                    'estado' => 1
                ],
                'id_establecimiento'
            );
        }
        return response()->json($establecimiento);
 
    }
    public function update_establecimiento(Request $request)
    {
        // $fecha = date('Y-m-d H:i:s');
        $arr = $request->data;

        foreach($arr as $data){
            
            $sql_contri =DB::table('logistica.log_prove')
            ->select(
                'log_prove.id_contribuyente'
            )
            ->where('id_proveedor',  $data['id_proveedor'])
            ->first();

            $estab = DB::table('contabilidad.establecimiento')
            ->where([
                ['id_contribuyente', $sql_contri->id_contribuyente],
                ['id_establecimiento', $data['id_establecimiento']]
            ])
            ->update(
                [
                    'id_tipo_establecimiento' => $data['id_tipo_establecimiento'],
                    'direccion' => $data['direccion'],
                    'estado' => $data['estado']
                ],
                'id_establecimiento'
            );
        }
        return response()->json($estab);
    }

    public function registrar_cuenta_bancaria(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $arr = $request->data;
        $sql_contri =DB::table('logistica.log_prove')
        ->select(
            'log_prove.id_contribuyente'
        )
        ->where('id_proveedor', $request->id_proveedor)
        ->first();

        foreach($arr as $data){
            
            $sql_contri =DB::table('logistica.log_prove')
            ->select(
                'log_prove.id_contribuyente'
            )
            ->where('id_proveedor',  $data['id_proveedor'])
            ->first();

            $adm_cta_contri = DB::table('contabilidad.adm_cta_contri')->insertGetId(
                [
                    'id_contribuyente' => $sql_contri->id_contribuyente,
                    'id_banco' => $data['id_banco'],
                    'id_tipo_cuenta' => $data['id_tipo_cuenta'],
                    'nro_cuenta' => $data['nro_cuenta'],
                    'nro_cuenta_interbancaria' => $data['nro_cuenta_interbancaria'],
                    'fecha_registro' => $fecha,
                    'estado' => 1
                ],
                'id_cuenta_contribuyente'
            );
        }
        return response()->json($adm_cta_contri);
 
    }
    
    public function update_cuenta_bancaria(Request $request)
    {
        $arr = $request->data;

        foreach($arr as $data){
            
            $sql_contri =DB::table('logistica.log_prove')
            ->select(
                'log_prove.id_contribuyente'
            )
            ->where('id_proveedor',  $data['id_proveedor'])
            ->first();

            $cta_contri = DB::table('contabilidad.adm_cta_contri')
            ->where([
                ['id_contribuyente', $sql_contri->id_contribuyente],
                ['id_cuenta_contribuyente', $data['id_cuenta_contribuyente']]
            ])
            ->update(
                [
                    'id_banco' => $data['id_banco'],
                    'id_tipo_cuenta' => $data['id_tipo_cuenta'],
                    'nro_cuenta' => $data['nro_cuenta'],
                    'nro_cuenta_interbancaria' => $data['nro_cuenta_interbancaria'],
                    'estado' => $data['estado']
                ],
                'id_cuenta_contribuyente'
            );
        }
        return response()->json($cta_contri);
    }

    public function registrar_contacto(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $arr = $request->data;

        foreach($arr as $data){
            $sql_contri =DB::table('logistica.log_prove')
            ->select(
                'log_prove.id_contribuyente'
            )
            ->where('id_proveedor',  $data['id_proveedor'])
            ->first();

            $adm_cta = DB::table('contabilidad.adm_ctb_contac')->insertGetId(
                [
                    'id_contribuyente' => $sql_contri->id_contribuyente,
                    'nombre' => $data['nombre'],
                    'telefono' => $data['telefono'],
                    'email' => $data['email'],
                    'cargo' => $data['cargo'],
                    'id_establecimiento' => $data['id_establecimiento'],
                    'fecha_registro' => $fecha,
                    'estado' => 1
                ],
                'id_datos_contacto'
            );
        }
        return response()->json($adm_cta);
    }

    public function update_contacto(Request $request)
    {
        $arr = $request->data;
        foreach($arr as $data){
            
            $sql_contri =DB::table('logistica.log_prove')
            ->select(
                'log_prove.id_contribuyente'
            )
            ->where('id_proveedor',  $data['id_proveedor'])
            ->first();

            $cta = DB::table('contabilidad.adm_ctb_contac')
            ->where([
                ['id_contribuyente', $sql_contri->id_contribuyente],
                ['id_datos_contacto', $data['id_datos_contacto']]
            ])
            ->update(
                [
                    'nombre' => $data['nombre'],
                    'telefono' => $data['telefono'],
                    'email' => $data['email'],
                    'cargo' => $data['cargo'],
                    'id_establecimiento' => $data['id_establecimiento'],
                    'estado' => $data['estado']
                ],
                'id_datos_contacto'
            );
        }
        return response()->json($cta);
    }

    public function registrar_adjunto_proveedor(Request $request)
    {

        $fecha = date('Y-m-d H:i:s');
        $name_file = '';
        $statusStorage = false;
        $arch_prov=false;
    

        $detalle_adjunto = json_decode($request->detalle_adjuntos, true);
        $detalle_adjuntos_length = count($detalle_adjunto);

        foreach ($request->only_adjuntos as $clave => $valor) {
            $file = $request->file('only_adjuntos')[$clave];

            if (isset($file)) {
                $name_file = "FP" . time() . $file->getClientOriginalName();
                if ($detalle_adjuntos_length > 0) {

                    $arch_prov = DB::table('logistica.prove_archivos')->insertGetId(
                        [
                            'id_proveedor' => $request->id_proveedor,
                            'archivo' => $name_file,
                            'fecha_registro' => $fecha,
                            'estado' => 1
                        ],
                        'id_archivo'
                    );
                    $statusStorage=Storage::disk('archivos')->put("logistica/proveedores/" . $name_file, \File::get($file));
                }
            } else {
                $statusRegister = false;
                $name_file = null;
                $statusStorage=false;
            }
        }
        
        $output=[
            'status_register' => $arch_prov > 0?true:false,
            'status_file' => $statusStorage,
            'name_file' => $name_file
        ];

        return response()->json($output);
    }
    // public function registrar_adjunto_proveedor(Request $request)
    // {
    //     $fecha = date('Y-m-d H:i:s');
    //     $arr = $request->data;

    //     foreach($arr as $data){
        
    //         $arch_prov = DB::table('logistica.prove_archivos')->insertGetId(
    //             [
    //                 'id_proveedor' => $data['id_proveedor'],
    //                 'archivo' => $data['archivo'],
    //                 'fecha_registro' => $fecha,
    //                 'estado' => 1
    //             ],
    //             'id_archivo'
    //         );
    //     }
    //     return response()->json($arch_prov);
    // }
    public function update_adjunto_proveedor(Request $request)
    {
        $arr = $request->data;
        foreach($arr as $data){
            $arch_prov = DB::table('logistica.prove_archivos')
            ->where([
                ['id_proveedor', $data['id_proveedor']],
                ['id_archivo', $data['id_archivo']]
            ])
            ->update(
                [
                    'archivo' => $data['archivo'],
                    'estado' => $data['estado']
                ],
                'id_archivo'
            );
        }
        return response()->json($arch_prov);
    }

    public function nextCodigoCotizacion()
    {
        $mes = date('m', strtotime("now"));
        $anio = date('y', strtotime("now"));
        $num = DB::table('logistica.log_cotizacion')->count();
        $correlativo = $this->leftZero(4, ($num + 1));
        $codigo = "CO-{$anio}{$mes}-{$correlativo}";
        return $codigo;
    }

    public function nextCodigoGrupo()
    {
        $mes = date('m', strtotime("now"));
        $anio = date('y', strtotime("now"));
        $num = DB::table('logistica.log_grupo_cotizacion')->count();
        $correlativo = $this->leftZero(4, ($num + 1));
        $codigoGrupo = "CC-{$anio}{$mes}-{$correlativo}";
        return $codigoGrupo;
    }

    public function get_estado_doc($nombreEstadoDoc){
        $estado_doc =  DB::table('administracion.adm_estado_doc')
        ->where('estado_doc', $nombreEstadoDoc)
        ->get();
        if($estado_doc->count()>0){
            $id_estado_doc=  $estado_doc->first()->id_estado_doc;
        }else{
            $id_estado_doc =0;
        }

        return $id_estado_doc;
    }


    public function guardar_cotizacion( Request $request, $id_grupo )
    {  
        $dataFormCotiza = $request->data;
        $items_array=[];
        foreach($dataFormCotiza['req'] as $data){
            $items_array[]= $data['id_det_req'];
        }

        $id_estado_doc1= $this->get_estado_doc('Elaborado');
        // $id_estado_doc2= $this->get_estado_doc('En Cotización');
        $rsta=[];
        $statusOption=['success','fail'];
        $status='';
        $codigo = $this->nextCodigoCotizacion();
        $id_cotizacion = DB::table('logistica.log_cotizacion')->insertGetId(
            [
                'id_empresa' => $dataFormCotiza['formdata']['id_empresa'],
                'id_sede' => $dataFormCotiza['formdata']['id_sede'],
                'id_proveedor' => $dataFormCotiza['formdata']['id_proveedor'],
                'id_contacto' => $dataFormCotiza['formdata']['id_contacto'],
                'email_proveedor' => $dataFormCotiza['formdata']['email_contacto'],
                'codigo_cotizacion' => $codigo,
                'estado_envio' => 0,
                'estado' => $id_estado_doc1,
                'fecha_registro' => date('Y-m-d H:i:s'),
            ],
            'id_cotizacion'
        );

        if($id_cotizacion > 0){
            array_push($rsta,'Se inserto una nueva cotización');
            $status=$statusOption[0];
        }else{
            array_push($rsta,'Error al insertar una nueva cotización');
            $status=$statusOption[1];

        }

        $count = count($items_array);


        $id_item_list=[];
        $item_list=[];
        $item_cero_list=[];
        $item_repeted_list=[];
        $det_req_list= [];

        for ($i = 0; $i < $count; $i++) {
            $id = $items_array[$i];
            $detalle = DB::table('almacen.alm_det_req')
                ->where('id_detalle_requerimiento', $id)
                ->first();
            $det_req_list[]=[
                'id_detalle_requerimiento'=> $detalle->id_detalle_requerimiento,
                'id_requerimiento'=> $detalle->id_requerimiento,
                'id_item'=> $detalle->id_item, 
                'precio_referencial'=> $detalle->precio_referencial,
                'cantidad'=> $detalle->cantidad,
                'fecha_entrega'=> $detalle->fecha_entrega,
                'descripcion_adicional'=> $detalle->descripcion_adicional,
                'obs'=> $detalle->obs, 
                'partida'=> $detalle->partida,
                'unidad_medida'=> $detalle->unidad_medida, 
                'estado'=> $detalle->estado,
                'fecha_registro'=> $detalle->fecha_registro, 
                'lugar_entrega'=> $detalle->lugar_entrega,
                'id_unidad_medida'=> $detalle->id_unidad_medida, 
                'id_tipo_item'=> $detalle->id_tipo_item,
                'stock_comprometido'=> $detalle->stock_comprometido,
                'id_almacen'=> $detalle->id_almacen,
                'merge'=>[]
            ];
            // if($detalle->id_item !=null){
            //     if(in_array($detalle->id_item,$temp_id_item_list) == false){
            //         array_push($temp_id_item_list,$detalle->id_item);
            //     }else{
            //         array_push($id_item_repeted_list,$detalle->id_item);
            //     }
            // }
        }

        // asignar nueva cantidad asignada si existe 
        foreach($dataFormCotiza['req'] as $kfc => $fc){
            foreach($det_req_list  as $krl => $rl){
                if($fc['id_det_req'] == $rl['id_detalle_requerimiento']){
                    if($fc['newCantidad']>0){
                        $det_req_list[$krl]['cantidad']=$fc['newCantidad'];
                    }
                }
            }
        }

        // sepparar los id_item repetidos si existen

        for ($i = 0; $i < sizeof($det_req_list); $i++) {
            if($det_req_list[$i]['id_item']==0 || $det_req_list[$i]['id_item']==''){
                $item_cero_list[]=$det_req_list[$i]; // lista de det_req con id_item=0 

            }elseif((in_array($det_req_list[$i]['id_item'],$id_item_list ) == false) && ($det_req_list[$i]['id_item']>0)){
                $id_item_list[]=$det_req_list[$i]['id_item'];
                $item_list[]=$det_req_list[$i]; // lista de no reepditos

            }else{
                $item_repeted_list[]=$det_req_list[$i]; // lista de repetidos

            }

        }

            //  si existen repetidos se sumara la cantidad de la lista de reqpetidos a la lista de  item_list (no repetidos)
            if(count($item_repeted_list)>0){
                for ($i = 0; $i < sizeof($item_repeted_list); $i++) {
                    for ($j = 0; $j < sizeof($item_list); $j++) {
                        if($item_repeted_list[$i]['id_item']== $item_list[$j]['id_item'] ){
                            $item_list[$j]['cantidad'] += $item_repeted_list[$i]['cantidad'];
                            $item_list[$j]['merge'][] = 
                            [
                                'id_requerimiento'=> $item_repeted_list[$i]['id_requerimiento'],
                                'id_detalle_requerimiento'=> $item_repeted_list[$i]['id_detalle_requerimiento']
                            ];
                        }
                    }
                }
            }

            // combinar det_req con id_item == 0 
            if(count($item_cero_list)>0){
                $item_list = array_merge($item_list, $item_cero_list);
            }
            


        // return response()->json($item_list);


 
        // // agregando item repetidos
        for ($i = 0; $i < sizeof($item_list); $i++) {
            
                $cantidadCotizada= (intval($item_list[$i]['cantidad']));
                $val_item_rep=DB::table('logistica.log_valorizacion_cotizacion')->insertGetId(
                [
                        'id_cotizacion' => $id_cotizacion,
                        'cantidad_cotizada' => $cantidadCotizada,
                        'estado' => $id_estado_doc1,
                        'fecha_registro' => date('Y-m-d H:i:s')
                        // 'id_requerimiento' => $item_rep[$i]['id_requerimiento']
                ],
                'id_valorizacion_cotizacion'
                );

                $valoriza_coti_detalle=DB::table('logistica.valoriza_coti_detalle')->insertGetId(
                    [
                        'id_requerimiento' => $item_list[$i]['id_requerimiento'],
                        'id_valorizacion_cotizacion'=>$val_item_rep,
                        'id_detalle_requerimiento' => $item_list[$i]['id_detalle_requerimiento'],
                        'fecha_registro' => date('Y-m-d H:i:s'),
                        'estado' => $id_estado_doc1
                    ],
                        'id_valoriza_coti_detalle'
                    );

            
                if(count($item_list[$i]['merge'])>0){ // si existe un item combinado con el item actual
                    for ($j = 0; $j < sizeof($item_list[$i]['merge']); $j++) {

                        $valoriza_coti_detalle=DB::table('logistica.valoriza_coti_detalle')->insertGetId(
                            [
                                'id_requerimiento' => $item_list[$i]['merge'][$j]['id_requerimiento'],
                                'id_valorizacion_cotizacion'=>$val_item_rep,
                                'id_detalle_requerimiento' => $item_list[$i]['merge'][$j]['id_detalle_requerimiento'],
                                'fecha_registro' => date('Y-m-d H:i:s'),
                                'estado' => $id_estado_doc1
                            ],
                                'id_valoriza_coti_detalle'
                            );
                    }

                }

        }
        // return response()->json($val_item_rep);



            
            //agrega grupo_cotizacion
            if ($id_grupo != '0') {
                $detalle_grupo_cotizacion = DB::table('logistica.log_detalle_grupo_cotizacion')->insert(
                    [
                        'id_grupo_cotizacion' => $id_grupo,
                        'id_cotizacion' => $id_cotizacion,
                        'estado' => 1
                    ]);
                    if($detalle_grupo_cotizacion){
                        array_push($rsta,'Se inserto un nuevo registro a la tabla log_detalle_grupo_cotizacion');
                        $status=$statusOption[0];


                    }else{
                        array_push($rsta,'Error, No Se puedo insertar un nuevo registro a la tabla log_detalle_grupo_cotizacion');
                        $status=$statusOption[1];

                    }

            } else {
                $codigo_grupo = $this->nextCodigoGrupo();
                $id_usuario = Auth::user()->id_usuario;

                $id_grupo = DB::table('logistica.log_grupo_cotizacion')->insertGetId(
                    [
                        'codigo_grupo' => $codigo_grupo,
                        'id_usuario' => $id_usuario,
                        'fecha_inicio' => date('Y-m-d'),
                        // 'fecha_fin'=> date('Y-m-d'), 
                        'estado' => 1
                    ],
                    'id_grupo_cotizacion'
                );

                if($id_grupo){
                    array_push($rsta,'Se inserto un nuevo registro a la tabla log_grupo_cotizacion');
                    $status=$statusOption[0];


                }else{
                    array_push($rsta,'Error, No Se puedo insertar un nuevo registro a la tabla log_grupo_cotizacion');
                    $status=$statusOption[1];

                }

                $detalle_grupo_cotizacion = DB::table('logistica.log_detalle_grupo_cotizacion')->insert(
                    [
                        'id_grupo_cotizacion' => $id_grupo,
                        'id_cotizacion' => $id_cotizacion,
                        'estado' => 1
                    ]
                );

                if($detalle_grupo_cotizacion){
                    array_push($rsta,'Se inserto un nuevo registro a la tabla log_detalle_grupo_cotizacion');
                    $status=$statusOption[0];


                }else{
                    array_push($rsta,'Error, No Se puedo insertar un nuevo registro a la tabla log_detalle_grupo_cotizacion');
                    $status=$statusOption[1];

                }
            }
        return response()->json(['id_cotizacion' => $id_cotizacion, 'id_grupo' => $id_grupo, 'message'=>$rsta, 'status'=>$status]);
    }

    function agregar_item_a_cotizacion(Request $request,$id_cotizacion){

        $rsta=[];
        $statusOption=['success','fail'];
        $status='';

        $dataForm = $request->data;
        $items_array=[];
        foreach($dataForm as $data){
            $items_array[]= $data['id_det_req'];
        }

        $estado_elaborado= $this->get_estado_doc('Elaborado');

        $cotizacion = DB::table('logistica.log_cotizacion')
        ->where([
            ['log_cotizacion.id_cotizacion','=' ,$id_cotizacion],
            ['log_cotizacion.estado','!=' ,7]
            ])
        ->get();

        if(count($cotizacion) >0){
      
            array_push($rsta,'Se encontro la cotizacion');
        
            

            $count = count($items_array);
            $id_item_list=[];
            $item_list=[];
            $item_cero_list=[];
            $item_repeted_list=[];
            $det_req_list= [];
    
            for ($i = 0; $i < $count; $i++) {
                $id = $items_array[$i];
                $detalle = DB::table('almacen.alm_det_req')
                    ->where('id_detalle_requerimiento', $id)
                    ->first();
                $det_req_list[]=[
                    'id_detalle_requerimiento'=> $detalle->id_detalle_requerimiento,
                    'id_requerimiento'=> $detalle->id_requerimiento,
                    'id_item'=> $detalle->id_item, 
                    'precio_referencial'=> $detalle->precio_referencial,
                    'cantidad'=> $detalle->cantidad,
                    'fecha_entrega'=> $detalle->fecha_entrega,
                    'descripcion_adicional'=> $detalle->descripcion_adicional,
                    'obs'=> $detalle->obs, 
                    'partida'=> $detalle->partida,
                    'unidad_medida'=> $detalle->unidad_medida, 
                    'estado'=> $detalle->estado,
                    'fecha_registro'=> $detalle->fecha_registro, 
                    'lugar_entrega'=> $detalle->lugar_entrega,
                    'id_unidad_medida'=> $detalle->id_unidad_medida, 
                    'id_tipo_item'=> $detalle->id_tipo_item,
                    'stock_comprometido'=> $detalle->stock_comprometido,
                    'id_almacen'=> $detalle->id_almacen,
                    'merge'=>[]
                ];
     
            }

            // asignar nueva cantidad asignada si existe 
            foreach($dataForm as $kfc => $fc){
                foreach($det_req_list  as $krl => $rl){
                    if($fc['id_det_req'] == $rl['id_detalle_requerimiento']){
                        if($fc['newCantidad']>0){
                            $det_req_list[$krl]['cantidad']=$fc['newCantidad'];
                        }
                    }
                }
            }
            
            // sepparar los id_item repetidos si existen
            for ($i = 0; $i < sizeof($det_req_list); $i++) {
                if($det_req_list[$i]['id_item']==0 || $det_req_list[$i]['id_item']==''){
                    $item_cero_list[]=$det_req_list[$i]; // lista de det_req con id_item=0 
                }elseif((in_array($det_req_list[$i]['id_item'],$id_item_list ) == false) && ($det_req_list[$i]['id_item']>0)){
                    $id_item_list[]=$det_req_list[$i]['id_item'];
                    $item_list[]=$det_req_list[$i]; // lista de no reepditos
                }else{
                    $item_repeted_list[]=$det_req_list[$i]; // lista de repetidos
                }
            }

            //  si existen repetidos se sumara la cantidad de la lista de reqpetidos a la lista de  item_list (no repetidos)
            if(count($item_repeted_list)>0){
                array_push($rsta,'Se encontro item repetidos');

                for ($i = 0; $i < sizeof($item_repeted_list); $i++) {
                    for ($j = 0; $j < sizeof($item_list); $j++) {
                        if($item_repeted_list[$i]['id_item']== $item_list[$j]['id_item'] ){
                            $item_list[$j]['cantidad'] += $item_repeted_list[$i]['cantidad'];
                            $item_list[$j]['merge'][] = 
                            [
                                'id_requerimiento'=> $item_repeted_list[$i]['id_requerimiento'],
                                'id_detalle_requerimiento'=> $item_repeted_list[$i]['id_detalle_requerimiento']
                            ];
                        }
                    }
                }
            }

            // combinar det_req con id_item == 0 
            if(count($item_cero_list)>0){
                array_push($rsta,'Se combinaran det_re con id_item = 0');

                $item_list = array_merge($item_list, $item_cero_list);
            }

                // agregando item 
                for ($i = 0; $i < sizeof($item_list); $i++) {
            
                    $cantidadCotizada= (intval($item_list[$i]['cantidad']));
                    $val_item_rep=DB::table('logistica.log_valorizacion_cotizacion')->insertGetId(
                    [
                            'id_cotizacion' => $id_cotizacion,
                            'cantidad_cotizada' => $cantidadCotizada,
                            'estado' => $estado_elaborado,
                            'fecha_registro' => date('Y-m-d H:i:s')
                            // 'id_requerimiento' => $item_rep[$i]['id_requerimiento']
                    ],
                    'id_valorizacion_cotizacion'
                    );

                    if($val_item_rep >0){
                        array_push($rsta,'Se inserto una nueva valorizacion lista para cotizar');
                        $status=$statusOption[0];
                    }else{
                        array_push($rsta,'No se puedo ingresar una nueva valorizacion');
                        $status=$statusOption[1];
                    }
    
                    $valoriza_coti_detalle=DB::table('logistica.valoriza_coti_detalle')->insertGetId(
                        [
                            'id_requerimiento' => $item_list[$i]['id_requerimiento'],
                            'id_valorizacion_cotizacion'=>$val_item_rep,
                            'id_detalle_requerimiento' => $item_list[$i]['id_detalle_requerimiento'],
                            'fecha_registro' => date('Y-m-d H:i:s'),
                            'estado' => $estado_elaborado
                        ],
                            'id_valoriza_coti_detalle'
                    );

                    if($valoriza_coti_detalle>0){
                        array_push($rsta,'Se inserto una nuevo detalle valorizacion');
                        $status=$statusOption[0];
                    }else{
                        array_push($rsta,'No se puedo ingresar un nuevo detalle de valorizacion');
                        $status=$statusOption[1];
                    }

                if(count($item_list[$i]['merge'])>0){ // si existe un item combinado con el item actual
                    array_push($rsta,'Se insertara item combinado a valoriza_coti_detale');

                    for ($j = 0; $j < sizeof($item_list[$i]['merge']); $j++) {

                        $valoriza_coti_detalle=DB::table('logistica.valoriza_coti_detalle')->insertGetId(
                            [
                                'id_requerimiento' => $item_list[$i]['merge'][$j]['id_requerimiento'],
                                'id_valorizacion_cotizacion'=>$val_item_rep,
                                'id_detalle_requerimiento' => $item_list[$i]['merge'][$j]['id_detalle_requerimiento'],
                                'fecha_registro' => date('Y-m-d H:i:s'),
                                'estado' => $estado_elaborado
                            ],
                                'id_valoriza_coti_detalle'
                            );
                    }
                }
            }

            // return response()->json($item_list);
        }
        else{
                array_push($rsta,'Error al buscar la cotización');
                $status=$statusOption[1];
            }

            return response()->json(['id_cotizacion' => $id_cotizacion, 'message'=>$rsta, 'status'=>$status]);

    }

    function eliminar_item_a_cotizacion(Request $request,$id_cotizacion){

        $rsta=[];
        $statusOption=['success','fail'];
        $status='';

        $dataForm = $request->data;
        $id_det_req_list=[];

        $estado_anulado= $this->get_estado_doc('Anulado');

        foreach($dataForm as $data){
            if($this->is_true($data['stateCheck'])==true){
                $id_det_req_list[]= $data['id_det_req'];
            }
        }

        $valoriza_coti_detalle_by_id_coti = DB::table('logistica.log_valorizacion_cotizacion')
        ->where('id_cotizacion', $id_cotizacion)
        ->get();

        $id_valorizacion_cotizacion_list=[];
        foreach($valoriza_coti_detalle_by_id_coti as $data){
            $id_valorizacion_cotizacion_list[]=$data->id_valorizacion_cotizacion;
        }


                
        $valoriza_coti_detalle_by_valoriza_det_req = DB::table('logistica.valoriza_coti_detalle')
        ->whereIn('id_valorizacion_cotizacion' ,$id_valorizacion_cotizacion_list)
        ->whereIn('id_detalle_requerimiento' ,$id_det_req_list)
        ->get();

        $id_valorizacion_cotizacion_list=[];
        foreach($valoriza_coti_detalle_by_valoriza_det_req as $data){
            $id_valorizacion_cotizacion_list[]=$data->id_valorizacion_cotizacion;
        }


            $valoriza_coti_detalle_update = DB::table('logistica.valoriza_coti_detalle')
            ->whereIn('id_valorizacion_cotizacion' ,$id_valorizacion_cotizacion_list)
            ->update([
                'estado' => $estado_anulado
            ]);
        
            $log_valorizacion_cotizacion_update = DB::table('logistica.log_valorizacion_cotizacion')
            ->whereIn('id_valorizacion_cotizacion' ,$id_valorizacion_cotizacion_list)
            ->update([
                'estado' => $estado_anulado
            ]);

        if($valoriza_coti_detalle_update > 0 && $log_valorizacion_cotizacion_update >0){
            $status=$statusOption[0];
            $rsta[]='se cambio el estado del item en la cotizacion (tabla valoriza_coti_detalle)';
        }


        return response()->json(['id_cotizacion' => $id_cotizacion, 'message'=>$rsta, 'status'=>$status]);

    }

    function actualizar_empresa_cotizacion(Request $request){
        $rsta=[];
        $statusOption=['success','fail'];
        $status='';
        $id_empresa = $request->data['id_empresa'];
        $id_cotizacion = $request->data['id_cotizacion'];

        if($id_empresa >0 && $id_cotizacion){
            $log_cotizacion_update = DB::table('logistica.log_cotizacion')
            ->where('id_cotizacion', $id_cotizacion)
            ->update([
                'id_empresa' => $id_empresa
            ]);
            if($log_cotizacion_update >0){
                $rsta[]='La empresa fue actualizo';
                $status=$statusOption[0];
            }
        }else{
            $rsta[]='El id_empresa o id_cotizacion es cero o no estan definidos';
            $status=$statusOption[1];
        }

        return response()->json(['id_cotizacion' => $id_cotizacion, 'message'=>$rsta, 'status'=>$status]);

    }

    function actualizar_proveedor_cotizacion(Request $request){
        $rsta=[];
        $statusOption=['success','fail'];
        $status='';
        $id_proveedor = $request->data['id_proveedor'];
        $id_cotizacion = $request->data['id_cotizacion'];

        if($id_proveedor >0 && $id_cotizacion >0){
            $log_cotizacion_update = DB::table('logistica.log_cotizacion')
            ->where('id_cotizacion', $id_cotizacion)
            ->update([
                'id_proveedor' => $id_proveedor,
                'id_contacto' => null,
                'email_proveedor' => null
            ]);
            if($log_cotizacion_update >0){
                $rsta[]='El proveedor fue actualizo';
                $status=$statusOption[0];
            }
        }else{
            $rsta[]='El id_proveedor o id_cotizacion es cero o no estan definidos';
            $status=$statusOption[1];
        }

        return response()->json(['id_cotizacion' => $id_cotizacion, 'message'=>$rsta, 'status'=>$status]);

    }
    function actualizar_contacto_cotizacion(Request $request){
        $rsta=[];
        $statusOption=['success','fail'];
        $status='';
        $id_contacto = $request->data['id_contacto'];
        $id_cotizacion = $request->data['id_cotizacion'];
        $email_contacto = $request->data['email_contacto'];

        if($id_contacto >0 && $id_cotizacion >0){
            $log_cotizacion_update = DB::table('logistica.log_cotizacion')
            ->where('id_cotizacion', $id_cotizacion)
            ->update([
                'id_contacto' => $id_contacto,
                'email_proveedor' => $email_contacto
                ]);
            if($log_cotizacion_update >0){
                $rsta[]='El el contacto fue actualizo';
                $status=$statusOption[0];
            }
        }else{
            $rsta[]='El id_contacto o id_cotizacion es cero o no estan definidos';
            $status=$statusOption[1];
        }

        return response()->json(['id_cotizacion' => $id_cotizacion, 'message'=>$rsta, 'status'=>$status]);

    }
    // public function cotizaciones_por_grupo($id_grupo)
    // {
    //     $detalle = DB::table('logistica.log_detalle_grupo_cotizacion')
    //         ->where('id_grupo_cotizacion', $id_grupo)
    //         ->get();

    //     $html = '';
    //     $i = 1;

    //     foreach ($detalle as $det) {
    //         $cotizacion = DB::table('logistica.log_cotizacion')
    //             ->select(
    //                 'log_cotizacion.*',
    //                 'prov.nro_documento',
    //                 'prov.id_contribuyente',
    //                 'prov.razon_social',
    //                 'empresa.razon_social as empresa',
    //                 'adm_estado_doc.estado_doc'
    //             )
    //             ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
    //             ->leftjoin('contabilidad.adm_contri as prov', 'prov.id_contribuyente', '=', 'log_prove.id_contribuyente')
    //             ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
    //             ->leftjoin('contabilidad.adm_contri as empresa', 'empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
    //             ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'log_cotizacion.estado')
    //             ->where('id_cotizacion', $det->id_cotizacion)
    //             ->first();

    //         $nro_items = DB::table('logistica.log_valorizacion_cotizacion')
    //             ->where([
    //                 ['id_cotizacion', '=', $det->id_cotizacion],
    //                 ['estado', '!=', 7] //no mostrar anulados
    //             ])
    //             ->count();

    //         if ($cotizacion->estado != 7) {
    //             $codigo = "'" . $cotizacion->codigo_cotizacion . "'";
    //             $id_contri = $cotizacion->id_contribuyente?$cotizacion->id_contribuyente:'0';
    //             $html .= '
    //             <tr>
    //                 <td>' . $i . '</td>
    //                 <td>' . $cotizacion->codigo_cotizacion . '</td>
    //                 <td>' . $nro_items . ' items</td>
    //                 <td>' . $cotizacion->nro_documento . ' - ' . $cotizacion->razon_social . '</td>
    //                 <td><a href="mailto:' . $cotizacion->email_proveedor . '?cc=logistica@proyectec.com.pe&subject=Solicitud de Cotizacion&body=Señores ' . $cotizacion->razon_social . ', de nuestra consideración tengo el agrado de dirigirme a usted, para saludarle cordialmente en nombre del OK COMPUTER EIRL y le solicitamos cotizar los siguientes productos de acuerdo a los términos que se adjuntan. RICHARD BALTAZAR DORADO BACA - Jefe de Logística">' . $cotizacion->email_proveedor . '</a></td>
    //                 <td>' . $cotizacion->empresa . '</td>
    //                 <td>' . $cotizacion->estado_doc . '</td>
    //                 <td>
    //                     <div class="btn-group" role="group">
    //                         <button type="button" class="btn btn-warning btn-sm" title="Editar" onClick="open_cotizacion(' . $cotizacion->id_cotizacion . ',' . $codigo . ');">
    //                             <i class="fas fa-edit"></i>
    //                         </button>
    //                         <button type="button" class="btn btn-success btn-sm" title="Formato de Solicitud de Cotizacion" onClick="downloadSolicitudCotizacion(' . $cotizacion->id_cotizacion . ','.$id_contri.');">
    //                             <i class="fas fa-file-excel"></i>
    //                         </button>
    //                         <button type="button" class="btn btn-primary btn-sm" title="Archivos Adjuntos" onClick="ModalArchivosAdjuntosCotizacion(' . $cotizacion->id_cotizacion . ');">
    //                             <i class="fas fa-folder"></i>
    //                         </button>
    //                         <button type="button" class="btn btn-danger btn-sm" title="Eliminar" onClick="anular_cotizacion(' . $cotizacion->id_cotizacion . ');">
    //                             <i class="fas fa-trash-alt"></i>
    //                         </button>
    //                     </div>
    //                 </td>
    //             </tr>';
    //             $i++;
    //         }
    //     }
    //     return json_encode($html);
    // }

    // public function get_items_cotizaciones_por_grupo($id_grupo){
    //     $detalle = DB::table('logistica.log_detalle_grupo_cotizacion')
    //     ->where('id_grupo_cotizacion', $id_grupo)
    //     ->get();

  
    //     foreach ($detalle as $det) {
    //     $cotizacion = DB::table('logistica.log_cotizacion')
    //         ->select(
    //             'log_cotizacion.*',
    //             'prov.nro_documento',
    //             'prov.razon_social',
    //             'empresa.razon_social as empresa',
    //             'adm_estado_doc.estado_doc'
    //         )
    //         ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
    //         ->leftjoin('contabilidad.adm_contri as prov', 'prov.id_contribuyente', '=', 'log_prove.id_contribuyente')
    //         ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
    //         ->leftjoin('contabilidad.adm_contri as empresa', 'empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
    //         ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'log_cotizacion.estado')
    //         ->where('id_cotizacion', $det->id_cotizacion)
    //         ->first();

    //     $items = DB::table('logistica.log_valorizacion_cotizacion')
    //         ->select(
    //             'log_valorizacion_cotizacion.*',
    //             DB::raw("(CASE 
    //                 WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
    //                 WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
    //                 WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
    //                 ELSE 'nulo' END) AS descripcion
    //                 "),
    //             DB::raw("(CASE 
    //                 WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.codigo 
    //                 WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.codigo 
    //                 WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.codigo 
    //                 ELSE 'nulo' END) AS codigo
    //                 "),
    //             DB::raw("(CASE 
    //                 WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.abreviatura
    //                 WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'serv' 
    //                 WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'und' 
    //                 ELSE 'nulo' END) AS unidad_medida
    //                 "),
    //             'alm_item.id_producto',
    //             'alm_item.id_servicio',
    //             'alm_item.id_equipo',
    //             'alm_req.id_requerimiento',
    //             'alm_req.codigo as cod_req',
    //             'log_valorizacion_cotizacion.cantidad_cotizada as cantidad',
    //             'alm_det_req.precio_referencial',
    //             'alm_det_req.id_tipo_item',
    //             'alm_det_req.descripcion_adicional',
    //             'alm_det_req.stock_comprometido',
    //             'log_cotizacion.codigo_cotizacion'
    //         )
    //         ->join('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_valorizacion_cotizacion.id_cotizacion')
    //         ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_valorizacion_cotizacion.id_detalle_requerimiento')
    //         ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
    //         ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
    //         ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
    //         ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
    //         ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
    //         ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
    //         ->where([
    //             ['log_valorizacion_cotizacion.id_cotizacion', '=', $det->id_cotizacion],
    //             ['log_valorizacion_cotizacion.estado', '=', 1]
    //         ])
    //         ->get();
    // }
    //         return $items;
    // }

    // public function items_cotizaciones_por_grupo($id_grupo)
    // {
    //     $items= $this->get_items_cotizaciones_por_grupo($id_grupo);
    //     $html = '';
    //     $i = 1;
    //         foreach ($items as $clave => $item) {
    //             $descripcion = "'" . $item->descripcion . "'";
    //             $html .= '
    //             <tr>
    //                 <td>
    //                     <input class="oculto" value="' . $item->id_requerimiento . '" name="id_requerimiento"/>
    //                     <input class="oculto" value="' . $item->id_detalle_requerimiento . '" name="id_detalle"/>
    //                     ' . $i . '
    //                 </td>
    //                 <td>' . $item->cod_req . '</td>
    //                 <td>' . $item->codigo_cotizacion . '</td>
    //                 <td>' . ($item->codigo ? $item->codigo : "0") . '</td>
    //                 <td>' . ($item->descripcion ? $item->descripcion : $item->descripcion_adicional) . '</td>
    //                 <td>' . $item->unidad_medida . '</td>
    //                 <td>' . $item->cantidad . '</td>
    //                 <td>' . $item->precio_referencial . '</td>
    //                 <td> <input type="number" min="0" max="'.$item->cantidad.'" value="'.$item->stock_comprometido .'" class="form-control activation stock_comprometido" data-id-det-req="'.$item->id_detalle_requerimiento.'"  data-id-req="'.$item->id_requerimiento.'"name="stock_comprometido[]" disabled></td>
    //                 <td>
    //                     <button type="button" class="btn btn-primary btn-sm" title="Ver Saldos" 
    //                     onClick="ver_saldos(' . $item->id_producto . ',' . $item->id_tipo_item . ');">
    //                     <i class="fas fa-search"></i>
    //                     </button>
    //                 </td>
    //             </tr>
    //             ';
    //             $i++;
    //         }
        
    //     return json_encode($html);
    // }

    public function actualizar_stock_comprometido(Request $request){
        $result=[];
        $status_stock ='';
        $status_alm ='';
        $statusOption =['success','fail'];

        foreach($request->comprometer_stock as $item){
            $alm_det_req = DB::table('almacen.alm_det_req')
            ->where('id_detalle_requerimiento', $item['id_detalle_requerimiento'])
            ->update([
                'stock_comprometido' => $item['stock_comprometido']
            ]);
        }
        if($alm_det_req != null){
            // array_push($message,'DETALLE_REQUERIMIENTO_ACTUALIZADO');
            $status_stock=$statusOption[0];

            foreach($request->comprometer_stock as $item){
                $alm_req = DB::table('almacen.alm_req')
                ->where('id_requerimiento', $item['id_requerimiento'])
                ->update([
                    'stock_comprometido' => 1
                ]);
            }
            if($alm_req != null){
                // array_push($message,'REQUERIMIENTO_ACTUALIZADO');
                $status_stock=$statusOption[0];

            }else{
                // array_push($message,'REQUERIMIENTO_NO_ACTUALIZADO');
                $status_stock=$statusOption[1];


            }

        }else{
            // array_push($message,'DETALLE_REQUERIMIENTO_NO_ACTUALIZADO');
            $status_stock=$statusOption[1];


        }

         

        // 
        foreach($request->almacen as $item){
            $det_req = DB::table('almacen.alm_det_req')
            ->where('id_detalle_requerimiento', $item['id_detalle_requerimiento'])
            ->update([
                'id_almacen' => $item['id_almacen']
            ]);
        }
        if($det_req != null){
            // array_push($message,'DETALLE_REQUERIMIENTO_ACTUALIZADO');
            $status_alm=$statusOption[0];


        }else{
            // array_push($message,'DETALLE_REQUERIMIENTO_NO_ACTUALIZADO');
            $status_alm=$statusOption[1];


        }

        $result = ['status_stock'=>$status_stock,'status_almacen'=>$status_alm];





    return response()->json($result);
     }

    public function mostrar_grupo_cotizacion($id_grupo)
    {
        $data = DB::table('logistica.log_grupo_cotizacion')
            ->where('id_grupo_cotizacion', $id_grupo)
            ->first();
        return response()->json($data);
    }

    public function mostrar_cotizacion($id_cotizacion)
    {
        $data = DB::table('logistica.log_cotizacion')
            ->select('log_cotizacion.*', 'adm_contri.id_contribuyente', 'adm_contri.razon_social','log_detalle_grupo_cotizacion.id_grupo_cotizacion')
            ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftjoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
            ->where('log_cotizacion.id_cotizacion', $id_cotizacion)
            ->first();
        $contacto = $this->listar_contacto_proveedor($data->id_proveedor);
        $cuentas = $this->listar_cuentas_proveedor($data->id_proveedor);
        return response()->json(['cotizacion' => $data, 'contacto' => $contacto, 'cuentas'=>$cuentas]);
    }

    public function mostrar_proveedores()
    {
        $data = DB::table('logistica.log_prove')
            ->select('log_prove.id_proveedor', 'adm_contri.id_contribuyente', 'adm_contri.nro_documento', 'adm_contri.razon_social','adm_contri.telefono')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->where([['log_prove.estado', '=', 1]])
            ->orderBy('adm_contri.nro_documento')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_proveedor($id_proveedor)
    {
        $proveedor = DB::table('logistica.log_prove')
            ->select(
            'log_prove.id_proveedor',
            'log_prove.id_contribuyente',
             'adm_contri.id_tipo_contribuyente',
             'adm_tp_contri.descripcion as descripcion_tipo_contribuyente',
             'adm_contri.id_doc_identidad',
             'sis_identi.descripcion as descripcion_tipo_documento',
             'adm_contri.razon_social',
             'adm_contri.nro_documento',
             'adm_contri.telefono',
             'adm_contri.direccion_fiscal',
             'adm_contri.ubigeo',
             'adm_contri.id_pais',
             'sis_pais.descripcion as descripcion_pais',
             'adm_contri.id_condicion_ruc',
             'condicion_ruc.descripcion as descripcion_condicion_ruc',
             'adm_contri.id_estado_ruc',
            'estado_ruc.descripcion as descripcion_estado_ru',
            'log_prove.estado'
            )
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->leftJoin('contabilidad.adm_tp_contri', 'adm_tp_contri.id_tipo_contribuyente', '=', 'adm_contri.id_tipo_contribuyente')
            ->leftJoin('configuracion.sis_pais', 'sis_pais.id_pais', '=', 'adm_contri.id_pais')
            ->leftJoin('contabilidad.estado_ruc', 'estado_ruc.id_estado_ruc', '=', 'adm_contri.id_estado_ruc')
            ->leftJoin('contabilidad.condicion_ruc', 'condicion_ruc.id_condicion_ruc', '=', 'adm_contri.id_condicion_ruc')
            ->where([
                ['log_prove.estado', '=', 1],
                ['log_prove.id_proveedor', '=', $id_proveedor]
                ])
            ->orderBy('adm_contri.nro_documento')
            ->get();

            
            foreach($proveedor as $data){
                $proveedor_list[]=[
                    'id_proveedor'=> $data->id_proveedor,
                    'id_contribuyente'=> $data->id_contribuyente,
                    'id_tipo_contribuyente'=> $data->id_tipo_contribuyente,
                    'descripcion_tipo_contribuyente'=> $data->descripcion_tipo_contribuyente,
                    'id_doc_identidad'=> $data->id_doc_identidad,
                    'descripcion_tipo_documento'=> $data->descripcion_tipo_documento,
                    'razon_social'=> $data->razon_social,
                    'nro_documento'=> $data->nro_documento,
                    'telefono'=> $data->telefono,
                    'direccion_fiscal'=> $data->direccion_fiscal,
                    'ubigeo'=> $data->ubigeo,
                    'id_pais'=> $data->id_pais,
                    'descripcion_pais'=> $data->descripcion_pais,
                    'id_condicion_ruc'=> $data->id_condicion_ruc,
                    'descripcion_condicion_ruc'=> $data->descripcion_condicion_ruc,
                    'id_estado_ruc'=> $data->id_estado_ruc,
                    'descripcion_estado_ru'=> $data->descripcion_estado_ru,
                    'estado'=> $data->estado
                ];
            }

            $establecimiento_list = DB::table('logistica.log_prove')
            ->select(
            'log_prove.id_proveedor',
            'log_prove.id_contribuyente',
            'establecimiento.id_establecimiento',
            'establecimiento.id_tipo_establecimiento',
            'tipo_establecimiento.descripcion as tipo_establecimiento',
            'establecimiento.direccion',
            'establecimiento.estado'
            )
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftJoin('contabilidad.establecimiento', 'establecimiento.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->leftJoin('contabilidad.tipo_establecimiento', 'tipo_establecimiento.id_tipo_establecimiento', '=', 'establecimiento.id_tipo_establecimiento')

 
            ->where([
                ['establecimiento.estado', '=', 1],
                ['log_prove.id_proveedor', '=', $id_proveedor]
                ])
            ->orderBy('establecimiento.id_establecimiento')
            ->get();


            $cuenta_banco_list = DB::table('logistica.log_prove')
            ->select(
                'log_prove.id_proveedor',
                'log_prove.id_contribuyente',
                'adm_cta_contri.id_cuenta_contribuyente',
                'adm_cta_contri.id_banco',
                'contri_bank.razon_social as nombre_banco',
                'adm_cta_contri.id_tipo_cuenta',
                'adm_tp_cta.descripcion as descripcion_tipo_cuenta',
                'adm_cta_contri.nro_cuenta',
                'adm_cta_contri.nro_cuenta_interbancaria',
                'adm_cta_contri.estado'
            )
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftJoin('contabilidad.adm_cta_contri', 'adm_cta_contri.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->leftJoin('contabilidad.cont_banco', 'cont_banco.id_banco', '=', 'adm_cta_contri.id_banco')
            ->leftJoin('contabilidad.adm_contri as contri_bank', 'contri_bank.id_contribuyente', '=', 'cont_banco.id_contribuyente')
            ->leftJoin('contabilidad.adm_tp_cta', 'adm_tp_cta.id_tipo_cuenta', '=', 'adm_cta_contri.id_tipo_cuenta')
            ->where([
                ['log_prove.estado', '=', 1],
                ['adm_cta_contri.estado', '=', 1],
                ['log_prove.id_proveedor', '=', $id_proveedor]
                ])
            ->orderBy('adm_cta_contri.id_cuenta_contribuyente')
            ->get();

            
            $contacto_list = DB::table('logistica.log_prove')
            ->select(
                'log_prove.id_proveedor',
                'log_prove.id_contribuyente',
                'adm_ctb_contac.id_datos_contacto',
                'adm_ctb_contac.nombre',
                'adm_ctb_contac.telefono',
                'adm_ctb_contac.email',
                'adm_ctb_contac.cargo',
                'adm_ctb_contac.id_establecimiento',
                'establecimiento.id_tipo_establecimiento',
                'tipo_establecimiento.descripcion as tipo_establecimiento',
                'establecimiento.direccion as establecimiento_direccion',
                'adm_ctb_contac.estado'
            )
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftJoin('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->leftJoin('contabilidad.establecimiento', 'establecimiento.id_establecimiento', '=', 'adm_ctb_contac.id_establecimiento')
            ->leftJoin('contabilidad.tipo_establecimiento', 'tipo_establecimiento.id_tipo_establecimiento', '=', 'establecimiento.id_tipo_establecimiento')
            ->where([
                ['log_prove.estado', '=', 1],
                ['adm_ctb_contac.estado', '=', 1],
                ['log_prove.id_proveedor', '=', $id_proveedor]
                ])
            ->orderBy('adm_ctb_contac.id_datos_contacto')
            ->get();

            $archivo_list = DB::table('logistica.log_prove')
            ->select(
                'log_prove.id_proveedor',
                'log_prove.id_contribuyente',
                'prove_archivos.id_archivo',
                'prove_archivos.archivo',
                'prove_archivos.fecha_registro',
                'prove_archivos.estado'
 
  
            )
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftJoin('logistica.prove_archivos', 'prove_archivos.id_proveedor', '=', 'log_prove.id_proveedor')
 
            ->where([
                ['log_prove.estado', '=', 1],
                ['prove_archivos.estado', '=', 1],
                ['log_prove.id_proveedor', '=', $id_proveedor]
                ])
            ->orderBy('prove_archivos.id_archivo')
            ->get();

        $array = ['proveedor' => $proveedor_list, 'establecimientos' => $establecimiento_list, 'cuentas_bancarias'=>$cuenta_banco_list, 'contactos'=>$contacto_list, 'archivos'=>$archivo_list];
        return response()->json($array);
    }

    public function listar_cuentas_proveedor($id_proveedor)
    {
        $data = DB::table('contabilidad.adm_cta_contri')
            ->select(
                'log_prove.id_proveedor',
                'log_prove.id_contribuyente',
                'adm_cta_contri.id_tipo_cuenta',
                // 'adm_cta_contri.id_contribuyente',
                'adm_cta_contri.id_cuenta_contribuyente',
                'adm_cta_contri.nro_cuenta',
                'adm_cta_contri.nro_cuenta_interbancaria',
                'adm_cta_contri.id_moneda',
                'cont_banco.id_banco',
                'adm_contri.razon_social'
                )
            ->join('logistica.log_prove', 'log_prove.id_contribuyente', '=', 'adm_cta_contri.id_contribuyente')
            ->join('contabilidad.cont_banco', 'cont_banco.id_banco', '=', 'adm_cta_contri.id_banco')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'cont_banco.id_contribuyente')
            ->where([
                ['log_prove.id_proveedor', '=', $id_proveedor],
                ['adm_cta_contri.estado', '=', 1]
            ])
            ->orderBy('adm_cta_contri.id_cuenta_contribuyente', 'asc')
            ->get();
        return $data;
    }

    public function listar_contacto_proveedor($id_proveedor)
    {
        $data = DB::table('contabilidad.adm_ctb_contac')
            ->join('logistica.log_prove', 'log_prove.id_contribuyente', '=', 'adm_ctb_contac.id_contribuyente')
            ->where([
                ['log_prove.id_proveedor', '=', $id_proveedor],
                ['adm_ctb_contac.estado', '=', 1]
            ])
            ->orderBy('adm_ctb_contac.email', 'asc')
            ->get();
        return $data;
    }


    public function mostrar_email_proveedor($id_proveedor)
    {
        $data = $this->listar_contacto_proveedor($id_proveedor);
        return response()->json($data);
    }

    public function update_cotizacion(Request $request)
    {
        $statusOption=['success','fail'];
        $status='';

        $sql = DB::table('logistica.log_cotizacion')
            ->where('id_cotizacion', $request->id_cotizacion)
            ->update([
                'id_proveedor' => $request->id_proveedor,
                'id_empresa' => $request->id_empresa,
                'id_contacto' => $request->id_contacto,
                'email_proveedor' => $request->email_proveedor
            ]);

            if($sql){
                $status=$statusOption[0];
            }else{
                $status=$statusOption[1];

            }

        $output=['status'=>$status, 'data'=>$sql];
            
        return response()->json($output);
    }

    public function duplicate_cotizacion(Request $request)
    {

        $statusOption=['success','fail'];
        $status='';
        $message=[];
        $out=[];

        $id_estado_elaborado= $this->get_estado_doc('Elaborado');

        $DataGrupo = DB::table('logistica.log_detalle_grupo_cotizacion')
            ->select(
                'log_detalle_grupo_cotizacion.*'
            )
            ->where('id_cotizacion', $request->id_cotizacion)
            ->first();

        if($DataGrupo){
            $status=$statusOption[0];
            array_push($message,'se localizo el grupo en la tabla detalle_grupo_cotización ');
        }else{
            $status=$statusOption[1];
            array_push($message,'No se puedo localizar el grupo en la tabla detalle_grupo_cotización ');
        }

        $DataCotizacion = DB::table('logistica.log_cotizacion')
            ->select(
                'log_cotizacion.*'
            )
            ->where('id_cotizacion', $request->id_cotizacion)
            ->get();

            if($DataCotizacion){
                $status=$statusOption[0];
                array_push($message,'se localizo la cotización en la tabla log_cotizacion ');
            }else{
                $status=$statusOption[1];
                array_push($message,'No se puedo localizar la cotización en la tabla log_cotizacion ');
            }

        $DataValorizacionCotizacion = DB::table('logistica.log_valorizacion_cotizacion')
            ->select(
                'log_valorizacion_cotizacion.*'
            )
            ->where('id_cotizacion', $request->id_cotizacion)
            ->get();

            $id_valorizacion_cotizacion_list=[];
            
            foreach($DataValorizacionCotizacion as $data ){
                $id_valorizacion_cotizacion_list[]=$data->id_valorizacion_cotizacion;
            }

   


            if($DataValorizacionCotizacion){
                $status=$statusOption[0];
                array_push($message,'se localizo la valorizacion en la tabla log_valorizacion_cotizacion ');
            }else{
                $status=$statusOption[1];
                array_push($message,'No se puedo localizar la valorizacion en la tabla log_valorizacion_cotizacion ');
            }
        $cantidadCotizcion = count($DataCotizacion);
        if ($cantidadCotizcion > 0) {
            array_push($message,'hay '.$cantidadCotizcion.' de cotizaciones');

            foreach ($DataCotizacion as $data) {
                $codigo = $this->nextCodigoCotizacion();
                $cotizacion = DB::table('logistica.log_cotizacion')->insertGetId(
                    [
                        'codigo_cotizacion' => $codigo,
                        'id_proveedor' => $request->id_proveedor,
                        'id_empresa' => $request->id_empresa,
                        'email_proveedor' => $request->email_proveedor,
                        'id_contacto' => $request->id_contacto,
                        'estado_envio' => 0,
                        'estado' => $id_estado_elaborado,
                        'fecha_registro' => date('Y-m-d H:i:s')
                    ],
                    'id_cotizacion'
                );

                if($cotizacion){
                    $status=$statusOption[0];
                    array_push($message,'Se creo una nueva cotizacion con id'.$cotizacion);
                }else{
                    $status=$statusOption[1];
                    array_push($message,'no se agregó una nueva cotización');
                }

                $detalle_grupo = DB::table('logistica.log_detalle_grupo_cotizacion')->insertGetId(
                    [
                        'id_grupo_cotizacion' => $DataGrupo->id_grupo_cotizacion,
                        'id_cotizacion' => $cotizacion,
                        'estado' => $id_estado_elaborado
                    ],
                    'id_detalle_grupo_cotizacion'
                );

                if($detalle_grupo){
                    $status=$statusOption[0];
                    array_push($message,'Se creo un nuevo detalle grupo cotizacion  con id'.$detalle_grupo);
                }else{
                    $status=$statusOption[1];
                    array_push($message,'no se agregó un nuevo detalle grupo cotizacion');
                }
            }


            foreach ($DataValorizacionCotizacion as $data) {

                $valorizacionCot = DB::table('logistica.log_valorizacion_cotizacion')->insertGetId(
                    [
                        // 'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                        'id_cotizacion' => $cotizacion,
                        'id_requerimiento' => $data->id_requerimiento,
                        'cantidad_cotizada' => $data->cantidad_cotizada,
                        'estado' => $id_estado_elaborado,
                        'fecha_registro' => date('Y-m-d H:i:s')
                    ],
                    'id_valorizacion_cotizacion'
                );

                $DataValorizaCotiDetalle = DB::table('logistica.valoriza_coti_detalle')
                ->select(
                    'valoriza_coti_detalle.*'
                )
                ->where('id_valorizacion_cotizacion', $data->id_valorizacion_cotizacion)
                ->get();

                if($DataValorizaCotiDetalle){
                    $status=$statusOption[0];
                    array_push($message,'existe registros en valoriza_coti_detalle');
                }else{
                    $status=$statusOption[1];
                    array_push($message,'No existe registros en valoriza_coti_detalle');
                }

                foreach($DataValorizaCotiDetalle as $d){

                    $valoriza_coti_detalle=DB::table('logistica.valoriza_coti_detalle')->insertGetId(
                        [
                            'id_requerimiento' => $d->id_requerimiento,
                            'id_valorizacion_cotizacion'=>$valorizacionCot,
                            'id_detalle_requerimiento' =>  $d->id_detalle_requerimiento,
                            'fecha_registro' => date('Y-m-d H:i:s'),
                            'estado' => $d->estado
                        ],
                            'id_valoriza_coti_detalle'
                        );
    
                        if($valoriza_coti_detalle){
                            $status=$statusOption[0];
                            array_push($message,'Se creo un nueva detalle valoriza_coti_detalle');
                        }else{
                            $status=$statusOption[1];
                            array_push($message,'no se agregó un detalle valoriza_coti_detalle');
        
                        }
                }

    
                if($valorizacionCot){
                    $status=$statusOption[0];
                    array_push($message,'Se creo un nueva valorizacion id'.$valorizacionCot);
 
                }else{
                    $status=$statusOption[1];
                    array_push($message,'no se agregó un nueva valorizacion');
                }
            }


            $output=['status'=>$status, 'message'=>$message, 'data'=>$valorizacionCot];
        }else{
            $output=['status'=>$status, 'message'=>$message, 'data'=>[]];

        }

        return response()->json($output);
    }

    public function guardar_contacto(Request $request)
    {
        $id_datos_contacto = DB::table('contabilidad.adm_ctb_contac')->insertGetId(
            [
                'id_contribuyente' => $request->id_contribuyente,
                'nombre' => $request->nombre,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'cargo' => $request->cargo,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
            'id_datos_contacto'
        );

        $data = DB::table('contabilidad.adm_ctb_contac')
            ->where([
                ['id_contribuyente', '=', $request->id_contribuyente],
                ['estado', '=', 1]
            ])
            ->get();

        $html = '';
        foreach ($data as $d) {
            if ($id_datos_contacto == $d->id_datos_contacto) {
                $html .= '<option value="' . $d->id_datos_contacto . '" selected>' . $d->nombre . ' - ' . $d->cargo . ' - ' . $d->email . '</option>';
            } else {
                $html .= '<option value="' . $d->id_datos_contacto . '">' . $d->nombre . ' - ' . $d->cargo . ' - ' . $d->email . '</option>';
            }
        }
        return json_encode($html);
    }


    public function get_cotizacion($id_cotizacion)
    {
        // $cotizacion =[];
        $cotizacion = DB::table('logistica.log_cotizacion')
            ->select('log_cotizacion.*')
            ->where([
                ['log_cotizacion.estado', '>', 0],
                ['log_cotizacion.id_cotizacion', '=', $id_cotizacion]
            ])
            ->first();
            if($cotizacion->id_proveedor =='' || $cotizacion->id_proveedor ==null){
                return 0;
            }

        $proveedor=[];
        $proveedor = DB::table('logistica.log_prove')
            ->select(
                'log_prove.id_proveedor',
                'adm_contri.id_doc_identidad',
                'sis_identi.descripcion AS nombre_doc_identidad',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'adm_contri.telefono',
                'adm_contri.celular',
                'adm_contri.direccion_fiscal',
                'sis_pais.descripcion AS nombre_pais'
            )
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->leftJoin('configuracion.sis_pais', 'sis_pais.id_pais', '=', 'adm_contri.id_pais')
            ->where(
                [
                    ['log_prove.estado', '>', 0],
                    ['log_prove.id_proveedor', '=', $cotizacion->id_proveedor]
                ]
            )
            ->get();
        $empresa=[];
        $empresa = DB::table('administracion.adm_empresa')
            ->select(
                'adm_empresa.id_empresa',
                'adm_contri.id_doc_identidad',
                'sis_identi.descripcion AS nombre_doc_identidad',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'adm_contri.telefono',
                'adm_contri.celular',
                'adm_contri.direccion_fiscal'
            )
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->where(
                [
                    ['adm_empresa.estado', '>', 0],
                    ['adm_empresa.id_empresa', '=', $cotizacion->id_empresa]
                ]
            )
            ->get();
        $detalle_grupo_cotizacion=[];
        $detalle_grupo_cotizacion = DB::table('logistica.log_detalle_grupo_cotizacion')
            ->select(
                'log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion',
                'log_detalle_grupo_cotizacion.id_grupo_cotizacion',
                'log_detalle_grupo_cotizacion.id_oc_cliente',
                'log_detalle_grupo_cotizacion.id_cotizacion'
            )
            ->where(
                [
                    ['log_detalle_grupo_cotizacion.estado', '>', 0],
                    ['log_detalle_grupo_cotizacion.id_cotizacion', '=', $cotizacion->id_cotizacion]
                ]
            )
            ->get();
        $valorizacion_cotizacion=[];
        $valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')
            ->select(
                'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
                'log_valorizacion_cotizacion.id_cotizacion',
                'log_valorizacion_cotizacion.id_detalle_oc_cliente',
                'log_valorizacion_cotizacion.precio_cotizado',
                'log_valorizacion_cotizacion.cantidad_cotizada',
                'log_valorizacion_cotizacion.subtotal',
                'log_valorizacion_cotizacion.flete',
                'log_valorizacion_cotizacion.porcentaje_descuento',
                'log_valorizacion_cotizacion.monto_descuento',
                'log_valorizacion_cotizacion.estado AS estado_valorizacion',
                'log_valorizacion_cotizacion.justificacion',
                // 'log_valorizacion_cotizacion.id_requerimiento',
                'valoriza_coti_detalle.id_detalle_requerimiento',
                'alm_req.codigo as codigo_requerimiento',

                'alm_item.codigo',
                'alm_item.id_producto',
                'alm_prod.descripcion as descripcion_producto',
                'alm_prod.descripcion',
                'alm_prod.id_unidad_medida',
                'alm_prod.estado AS estado_prod',
                'alm_und_medida.descripcion AS unidad_medida_descripcion',

                'alm_item.id_servicio',
                'alm_det_req.id_requerimiento',
                'alm_det_req.id_item',
                'alm_det_req.precio_referencial',
                'alm_det_req.cantidad',
                'alm_det_req.fecha_entrega',
                'alm_det_req.lugar_entrega',
                'alm_det_req.descripcion_adicional',
                'alm_det_req.obs',
                'alm_det_req.partida',
                'alm_det_req.unidad_medida',
                'alm_det_req.fecha_registro',
                'alm_det_req.stock_comprometido'
            )
            ->leftJoin('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'valoriza_coti_detalle.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
            ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            ->where(
                [
                    ['log_valorizacion_cotizacion.estado', '!=', 7],
                    ['valoriza_coti_detalle.estado', '!=', 7],
                    ['log_valorizacion_cotizacion.id_cotizacion', '=', $cotizacion->id_cotizacion]
                ]
            )
            ->get();

        $items = [];
        $id_item_list=[];
        $idDetReqList=[];
        // return $valorizacion_cotizacion;
        foreach ($valorizacion_cotizacion as $data) {
            
            if(in_array($data->id_item,$id_item_list)==false || $data->id_item =='' || $data->id_item == null){
            // if(in_array($data->id_detalle_requerimiento,$idDetReqList)==false){
                $id_item_list[]=$data->id_item;
                $idDetReqList[]=$data->id_detalle_requerimiento;
                $items[] = [
                    'id_cotizacion' => $data->id_cotizacion,
                    'id_requerimiento' => $data->id_requerimiento,
                    'codigo_requerimiento' => $data->codigo_requerimiento,
                    'id_item' => $data->id_item,
                    'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                    'codigo' => $data->codigo ? $data->codigo : '0',
                    'descripcion' => $data->descripcion ? $data->descripcion : $data->descripcion_adicional,
                    'cantidad' => intval($data->cantidad),
                    'cantidad_cotizada' => intval($data->cantidad_cotizada),
                    'precio_referencial'=>$data->precio_referencial,
                    'fecha_entrega'=>$data->fecha_entrega,
                    'lugar_entrega'=>$data->lugar_entrega,
                    'stock_comprometido' => $data->stock_comprometido?$data->stock_comprometido:0,
                    'id_unidad_medida' => $data->id_unidad_medida,
                    'unidad_medida_descripcion' => $data->unidad_medida_descripcion,
                    'fecha_registro' => $data->fecha_registro,
                    'estado' => $data->estado_prod,
                    'adjuntos' => []
                ];
            }else{ // si es repetido
                foreach($items as $clave => $valor){
                    if($valor['id_item'] == $data->id_item){
                        $items[$clave]['cantidad'] += $data->cantidad;
                        $items[$clave]['stock_comprometido'] += $data->stock_comprometido;
                    }
                }
            }
   
        }
        // return $idDetReqList;

    
        $alm_det_req_adjuntos = DB::table('almacen.alm_det_req_adjuntos')
        ->select(
            'alm_det_req_adjuntos.*'
        )
        ->whereIn('alm_det_req_adjuntos.id_detalle_requerimiento',$idDetReqList)
        ->where(
            [
                ['alm_det_req_adjuntos.estado', '>', 0]
            ]
        )
        ->get();


        foreach($items as $keyItem => $item){
            foreach($alm_det_req_adjuntos as $keyReArch => $reqArch){
                if($item['id_detalle_requerimiento'] == $reqArch->id_detalle_requerimiento){
                    $items[$keyItem]['adjuntos'][]=$reqArch;
                }
            }
        }
        // return $items;


        $cotizacion_item=[];
        $cotizacion_item = [
            'id_cotizacion' => $cotizacion->id_cotizacion,
            'id_grupo_cotizacion' => $detalle_grupo_cotizacion[0]->id_grupo_cotizacion,
            'codigo_cotizacion' => $cotizacion->codigo_cotizacion,
            'estado_envio' => $cotizacion->estado_envio,
            'estado' => $cotizacion->estado,
            'fecha_registro' => $cotizacion->fecha_registro,
            'empresa' => [
                'id_empresa' => $empresa[0]->id_empresa,
                'razon_social' => $empresa[0]->razon_social,
                'nro_documento' => $empresa[0]->nro_documento,
                'nombre_doc_identidad' => $empresa[0]->nombre_doc_identidad
            ],
            'proveedor' => [
                'id_proveedor' => $proveedor[0]->id_proveedor,
                'razon_social' => $proveedor[0]->razon_social,
                'nro_documento' => $proveedor[0]->nro_documento,
                'id_doc_identidad' => $proveedor[0]->id_doc_identidad,
                'nombre_doc_identidad' => $proveedor[0]->nombre_doc_identidad,
                'contacto' => [
                    'email' => $cotizacion->email_proveedor,
                    'telefono' => $proveedor[0]->telefono
                ]
            ],
            'items' => $items
        ];

        return [$cotizacion_item];
    }

    public function requerimientos_entrante_a_cotizacion()
    {
        $estado_aprobado = $this->get_estado_doc('Aprobado');
        $estado_anulado = $this->get_estado_doc('Anulado');
        $estado_observado = $this->get_estado_doc('Observado');
        $estado_denegado = $this->get_estado_doc('Denegado');
        $estado_excluidos=[$estado_anulado, $estado_observado,$estado_denegado];

            $id_detalle_req_list_in_coti = DB::table('logistica.log_valorizacion_cotizacion')
            ->select('valoriza_coti_detalle.id_detalle_requerimiento')
            ->leftJoin('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
            ->where([['log_valorizacion_cotizacion.estado', '!=',$estado_anulado ],
            ['valoriza_coti_detalle.estado', '!=',$estado_anulado ]]) 
            ->groupBy('valoriza_coti_detalle.id_detalle_requerimiento')
            ->orderBy('valoriza_coti_detalle.id_detalle_requerimiento', 'desc')
            ->get();

            $detReqList=[];
            foreach($id_detalle_req_list_in_coti as $data){
                array_push($detReqList, $data->id_detalle_requerimiento);
            }

            $idReqInValCotiList=[];

            //  requerimiento y detalle requerimiento en  valorizacion_cotizacion
            $reqDetInValCoti = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.id_requerimiento','alm_det_req.id_detalle_requerimiento')
            ->whereIn('alm_det_req.id_detalle_requerimiento',$detReqList) 
            ->whereNotIn('alm_det_req.estado',$estado_excluidos)
            ->orderBy('alm_det_req.id_requerimiento', 'desc')
            ->get();

            $reqDetInValCotiList=[];
            foreach($reqDetInValCoti as $data){
                array_push($idReqInValCotiList,$data->id_requerimiento);

                array_push($reqDetInValCotiList,[
                    'id_requerimiento'=>$data->id_requerimiento,
                    'id_detalle_requerimiento'=>$data->id_detalle_requerimiento
                    ]);
            }
            $idReqInValCotiListUniq=array_values(array_unique($idReqInValCotiList));
            //  agrupando 
        $reqValorizaCotizaList=[];

            for ($j = 0; $j < sizeof($reqDetInValCotiList); $j++) {
                for ($i = 0; $i < sizeof($idReqInValCotiListUniq); $i++) {
                    if ($reqDetInValCotiList[$j]['id_requerimiento'] == $idReqInValCotiListUniq[$i]) {
                        $reqValorizaCotizaList[$i]['id_requerimiento'] = $idReqInValCotiListUniq[$i];
                        $reqValorizaCotizaList[$i]['id_detalle_requerimiento'][] = $reqDetInValCotiList[$j]['id_detalle_requerimiento'];
                    }
                }
            }


            // //lista de requerimientos y detalle requerimiento
            $req=[];
            $alm_req = DB::table('almacen.alm_req')
            ->select('alm_det_req.id_requerimiento','alm_det_req.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento')
            ->whereNotIn('alm_det_req.estado',$estado_excluidos)
            ->orderBy('alm_det_req.id_requerimiento', 'desc')
            ->get(); 
            $reqDet=[];
            foreach($alm_req as $data){
                array_push($req,$data->id_requerimiento);
                array_push($reqDet,[
                    'id_requerimiento'=>$data->id_requerimiento,
                    'id_detalle_requerimiento'=>$data->id_detalle_requerimiento
                    ]);
            }
            $idReqLisUniq=array_values(array_unique($req));
            // agrupando 
        $reqList=[];
            for ($j = 0; $j < sizeof($reqDet); $j++) {
                for ($i = 0; $i < sizeof($idReqLisUniq); $i++) {
                    if ($reqDet[$j]['id_requerimiento'] == $idReqLisUniq[$i]) {
                        $reqList[$i]['id_requerimiento'] = $idReqLisUniq[$i];
                        $reqList[$i]['id_detalle_requerimiento'][] = $reqDet[$j]['id_detalle_requerimiento'];
                    }
                }
            }

            //  filtrar todo los detalle requerimientos que no este en valorizacion_Cotizacion
                $arr_diff=[];
            for ($i = 0; $i < sizeof($reqList); $i++) {
                for ($j = 0; $j < sizeof($reqValorizaCotizaList); $j++) {
                    if($reqList[$i]['id_requerimiento'] == $reqValorizaCotizaList[$j]['id_requerimiento'] ){
                            $arr= array_diff($reqList[$i]['id_detalle_requerimiento'],$reqValorizaCotizaList[$j]['id_detalle_requerimiento']);
                            if(sizeof(array_values($arr)) > 0){
                                array_push($arr_diff,[
                                    'id_requerimiento'=>$reqList[$i]['id_requerimiento'],
                                    'id_detalle_requerimiento'=>array_values($arr)
                                ]);
                            }
                    }
                }
            }

            $reqDetReqWithoutCotList =$arr_diff; // lista de requerimientos y detalle que no estan incluidos en una cotizacion


            
            $reqNoInsideValCoti = DB::table('almacen.alm_req')
            ->select('alm_req.id_requerimiento')
            ->where([
                ['alm_req.id_estado_doc', '=',$estado_aprobado ],
                ['alm_req.estado', '!=',$estado_anulado ]
            ]) 
            ->whereNotIn('alm_req.id_requerimiento',$idReqInValCotiList)
            ->orderBy('alm_req.fecha_registro', 'desc')
            ->get();

            $ReqAproNoInsideValCot =[];
            $idReqAproNoInsideValCot =[];
            foreach($reqNoInsideValCoti as $value){
                $idReqAproNoInsideValCot[]= $value->id_requerimiento;
            }

            $detReqNoInsideValCoti = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.id_requerimiento','alm_det_req.id_detalle_requerimiento')
            ->where([
                ['alm_det_req.estado', '!=',$estado_anulado ]
            ]) 
            ->whereIn('alm_det_req.id_requerimiento',$idReqAproNoInsideValCot)
            ->orderBy('alm_det_req.fecha_registro', 'desc')
            ->get();

            $detReqNoInsideValCotiList=[];
                foreach($detReqNoInsideValCoti as $data){
                    $detReqNoInsideValCotiList[]=[
                        'id_requerimiento'=> $data->id_requerimiento,
                        'id_detalle_requerimiento'=> $data->id_detalle_requerimiento
                    ];
                }
               
                $reqDetReqNoInsideValCotiList =[];
                for ($j = 0; $j < sizeof($detReqNoInsideValCotiList); $j++) {
                for ($i = 0; $i < sizeof($idReqAproNoInsideValCot); $i++) {
                        if($detReqNoInsideValCotiList[$j]['id_requerimiento'] == $idReqAproNoInsideValCot[$i]){
                            $reqDetReqNoInsideValCotiList[$i]['id_requerimiento'] = $idReqAproNoInsideValCot[$i];
                            $reqDetReqNoInsideValCotiList[$i]['id_detalle_requerimiento'][] = $detReqNoInsideValCotiList[$j]['id_detalle_requerimiento'];
    
                        }
                    }
                }
     
            $IdReqList=[]; // lista de requerimientos 
            $IdDetReqList=[]; //lista detalle requerimientos permitido
            $reqReadyForCotList = array_merge($reqDetReqWithoutCotList, $reqDetReqNoInsideValCotiList);
                foreach($reqReadyForCotList as $data){
                    $IdReqList[]=$data['id_requerimiento'];
                    
                    foreach($data['id_detalle_requerimiento'] as $value){
                        array_push($IdDetReqList,$value);
                    }   
                }
            $req = DB::table('almacen.alm_req')   
            ->select('alm_req.*', 'adm_area.descripcion as des_area', 'adm_estado_doc.estado_doc')
            ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.id_estado_doc', '=', 'adm_estado_doc.id_estado_doc')
            ->whereIn('alm_req.id_requerimiento',$IdReqList)
            ->orderBy('alm_req.fecha_registro', 'desc')
            ->get();

                // recorrer req e insertar en  reqReadyForCotList la data de requerimiento
                // dejando al campo id_detalle_requerimiento como array
            for ($j = 0; $j < sizeof($req); $j++) {
                for ($i = 0; $i < sizeof($reqReadyForCotList); $i++) {
                    if($reqReadyForCotList[$i]['id_requerimiento'] == $req[$j]->id_requerimiento){
                        $reqReadyForCotList[$i]["codigo"]= $req[$j]->codigo;
                        $reqReadyForCotList[$i]["id_tipo_requerimiento"]= $req[$j]->id_tipo_requerimiento; 
                        $reqReadyForCotList[$i]["id_usuario"]= $req[$j]->id_usuario; 
                        $reqReadyForCotList[$i]["id_rol"]= $req[$j]->id_rol; 
                        $reqReadyForCotList[$i]["fecha_requerimiento"]= $req[$j]->fecha_requerimiento;
                        $reqReadyForCotList[$i]["concepto"]= $req[$j]->concepto;
                        $reqReadyForCotList[$i]["id_grupo"]= $req[$j]->id_grupo; 
                        $reqReadyForCotList[$i]["id_op_com"]= $req[$j]->id_op_com; 
                        $reqReadyForCotList[$i]["estado"]= $req[$j]->estado; 
                        $reqReadyForCotList[$i]["fecha_registro"]= $req[$j]->fecha_registro;
                        $reqReadyForCotList[$i]["id_area"]= $req[$j]->id_area; 
                        $reqReadyForCotList[$i]["id_prioridad"]= $req[$j]->id_prioridad; 
                        $reqReadyForCotList[$i]["id_estado_doc"]= $req[$j]->id_estado_doc; 
                        $reqReadyForCotList[$i]["id_moneda"]= $req[$j]->id_moneda; 
                        $reqReadyForCotList[$i]["obs_log"]= $req[$j]->obs_log; 
                        $reqReadyForCotList[$i]["stock_comprometido"]= $req[$j]->stock_comprometido; 
                        $reqReadyForCotList[$i]["des_area"]= $req[$j]->des_area;
                        $reqReadyForCotList[$i]["estado_doc"]= $req[$j]->estado_doc;
                    }
                }
            }
 

        $output['data'] = $reqReadyForCotList;
        // return response()->json($output);

        // return response()->json($output);
        return response()->json($output);
    }

    public function requerimientos_entrante_a_cotizacion_v2($id_empresa = null,$id_sede=null)
    {
        $estado_aprobado = $this->get_estado_doc('Aprobado');
        $estado_anulado = $this->get_estado_doc('Anulado');
        $estado_observado = $this->get_estado_doc('Observado');
        $estado_denegado = $this->get_estado_doc('Denegado');
        $estado_elaborado = $this->get_estado_doc('Elaborado');
        $estado_excluidos=[$estado_elaborado,$estado_anulado, $estado_observado,$estado_denegado];

            $id_detalle_req_list_in_coti = DB::table('logistica.log_valorizacion_cotizacion')
            ->select('valoriza_coti_detalle.id_requerimiento')
            ->leftJoin('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
            ->where([['log_valorizacion_cotizacion.estado', '!=',$estado_anulado ],
            ['valoriza_coti_detalle.estado', '!=',$estado_anulado ]]) 
            ->groupBy('valoriza_coti_detalle.id_requerimiento')
            ->orderBy('valoriza_coti_detalle.id_requerimiento', 'desc')
            ->get();

              $reqList=[];
            foreach($id_detalle_req_list_in_coti as $data){
                 array_push($reqList, $data->id_requerimiento);
            }

            $idReqInValCotiListUniq=array_unique($reqList);

  
            // $id_empresa = 2; //enviar como parametro el id_empresa
            $whereIdEmpresa=[];
            if($id_empresa != null && $id_empresa >0){
                $whereIdEmpresa[] =['sis_sede.id_empresa','=',$id_empresa];
            }
            if($id_sede != null && $id_sede >0){
                $whereIdEmpresa[] =['sis_sede.id_sede','=',$id_sede];
            }
            $gruposByEmpresa=[];

            $SQLgrupoByEmpresa = DB::table('administracion.adm_grupo')   
            ->select('adm_grupo.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
            ->where($whereIdEmpresa)
            ->orderBy('adm_grupo.id_grupo', 'desc')
            ->get();
            if($SQLgrupoByEmpresa){
                foreach($SQLgrupoByEmpresa as $data){
                    $gruposByEmpresa[]=$data->id_grupo;
                }
            }
            // return $gruposByEmpresa;
 
            $req = DB::table('almacen.alm_req')   
            ->select('alm_req.*', 'adm_area.descripcion as des_area', 'adm_estado_doc.estado_doc')
            ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.id_estado_doc', '=', 'adm_estado_doc.id_estado_doc')
            ->whereNotIn(
                'alm_req.id_estado_doc',$estado_excluidos
                )
            ->whereIn(
                'alm_req.id_grupo',$gruposByEmpresa

            )
            ->orderBy('alm_req.fecha_registro', 'desc')
            ->get();
            $requerimientos=[];
            foreach($req as $data){
                $requerimientos[]=[
                    'id_requerimiento'=>$data->id_requerimiento,
                    'codigo'=>$data->codigo,
                    'id_tipo_requerimiento'=>$data->id_tipo_requerimiento,
                    'id_usuario'=>$data->id_usuario,
                    'id_rol'=>$data->id_rol,
                    'fecha_requerimiento'=>$data->fecha_requerimiento,
                    'concepto'=>$data->concepto,
                    'id_grupo'=>$data->id_grupo,
                    'id_op_com'=>$data->id_op_com,
                    'fecha_registro'=>$data->fecha_registro,
                    'id_area'=>$data->id_area,
                    'id_prioridad'=>$data->id_prioridad,
                    'id_estado_doc'=>$data->id_estado_doc,
                    'id_moneda'=>$data->id_moneda,
                    'obs_log'=>$data->obs_log,
                    'stock_comprometido'=>$data->stock_comprometido,
                    'des_area'=>$data->des_area,
                    'estado_doc'=>$data->estado_doc,
                    'has_cotizacion'=>in_array($data->id_requerimiento, $idReqInValCotiListUniq)?true:false,
                ];
                

            }
          
 
 

        $output['data'] = $requerimientos;
        // return response()->json($output);

        // return response()->json($output);
        return response()->json($output);
    }




    public function descargar_solicitud_cotizacion_excel($id_cotizacion)
    {
        $cotizacionArray = $this->get_cotizacion($id_cotizacion);
        $file =(new CorreoController)->generarCotizacionInServer($id_cotizacion,$cotizacionArray);

        if($file['status']>0){
            $ruta = '/files/logistica/cotizacion/co'.$id_cotizacion.'.xlsx';
        }else{
            $ruta='';
        }

        return ['status'=>$file['status'],'ruta'=>$ruta,'message'=>$file['message']];
    }

    
    public function anular_cotizacion($id_cotizacion)
    {
        $Cotizacion = DB::table('logistica.log_cotizacion')
            ->where('id_cotizacion', $id_cotizacion)
            ->update(['estado' => 7]); //Anulado
        $Valorizacion = DB::table('logistica.log_valorizacion_cotizacion')
            ->where('id_cotizacion', $id_cotizacion)
            ->update(['estado' => 7]); //Anulado
        $Valorizacion = DB::table('logistica.log_detalle_grupo_cotizacion')
            ->where('id_cotizacion', $id_cotizacion)
            ->update(['estado' => 7]); //Anulado
        return response()->json($Cotizacion);
    }

    public function detalle_cotizacion($id_cotizacion)
    {
        // $cotizacion = DB::table('logistica.log_cotizacion')
        //     ->where('id_cotizacion', $id_cotizacion)
        //     ->first();
        $detalle_val=[];
        $mostrar_coti = $this->mostrar_cotizacion($id_cotizacion);
        $cotizacion = $mostrar_coti->getData()->cotizacion;
        $cuentas = $mostrar_coti->getData()->cuentas;

        $detalle = DB::table('logistica.log_valorizacion_cotizacion')
            ->select(
                'log_valorizacion_cotizacion.*',
                'valoriza_coti_detalle.id_detalle_requerimiento',
                // DB::raw("SUM(log_valorizacion_cotizacion.subtotal) as suma_subtotal"),
                DB::raw("(CASE 
                    WHEN alm_item.id_item isNUll THEN alm_det_req.descripcion_adicional 
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
                    "),
                'alm_item.id_producto',
                'alm_item.id_servicio',
                'alm_item.id_equipo',
                'alm_det_req.id_item'
            )
            ->join('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
            ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'valoriza_coti_detalle.id_detalle_requerimiento')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftjoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
            ->where([
                ['log_valorizacion_cotizacion.id_cotizacion', '=', $id_cotizacion],
                ['log_valorizacion_cotizacion.estado', '=', 2] // aprobado con buena pro
            ]) //solo los aprobados
            ->get();

        // return $detalle;
        $id_val_cot_list=[];
        foreach ($detalle as $d) {
            if(in_array($d->id_valorizacion_cotizacion,$id_val_cot_list)==false){
                array_push($id_val_cot_list,$d->id_valorizacion_cotizacion);
                $detalle_val[]=$d;
            }
        }

        // $detalle_val=[];
        // foreach ($id_val_cot_list as $id_val) {
        //     foreach ($detalle as $d) {
        //         if($d->id_valorizacion_cotizacion == $id_val){
        //         }
        //     }
        // }



        $html_item_valorizacion = '';
        $i = 1;
        $sum_subtotal = 0;

        foreach ($detalle_val as $d) {
            $sum_subtotal += floatval($d->subtotal);
            
            $tdActualizarCodigo='<td><input class="oculto" name="id_item" value="' . $d->id_item . '"/><button type="button" class="btn btn-xs btn-info" name="btnActualizarCodigoItem" title="Actualizar Código" onClick="modalActualizarCodigoItem(0,'.$d->id_valorizacion_cotizacion.');" disabled >Actualizar Códgio</button></td>';
            $tdMostrarCodigo= '<td><input class="oculto" name="id_item" value="' . $d->id_item . '"/>' . $d->codigo . '</td>';

            $html_item_valorizacion .= '
            <tr>
            <td><input class="oculto" name="id_valorizacion_cotizacion" value="' . $d->id_valorizacion_cotizacion . '"/>' . $i . '</td>';

            if($d->codigo == null || $d->codigo == 0){
                $html_item_valorizacion.=$tdActualizarCodigo;
            }else{
                $html_item_valorizacion.=$tdMostrarCodigo;
            }
                
            $html_item_valorizacion.= '<td>' .$d->descripcion. '</td>
            <td>' . $d->unidad_medida . '</td>
            <td>' . $d->cantidad_cotizada . '</td>
            <td>' . $d->precio_cotizado . '</td>
            <td>' . $d->monto_descuento . '</td>
            <td>' . $d->subtotal . '</td>
            <td></td>
            <td></td>'
            ;

            $html_item_valorizacion.='
            <td><button type="button" class="btn btn-sm btn-primary activation" title="Editar Despacho" name="btnEditarDespacho" onClick="editarDespacho(event, ' . $d->id_valorizacion_cotizacion . ');"><i class="fas fa-edit"></i></button></td>

            <td class="oculto"><input class="oculto" name="id_producto" value="' . $d->id_producto . '"/></td>
            <td class="oculto"><input class="oculto" name="id_servicio" value="' . $d->id_servicio . '"/></td>
            <td class="oculto"><input class="oculto" name="id_equipo" value="' . $d->id_equipo . '"/></td>
            </tr>';


            $i++;
        }
        $igv = DB::table('contabilidad.cont_impuesto')
            ->where([['codigo', '=', 'IGV'], ['fecha_inicio', '<', date('Y-m-d')]])
            ->orderBy('fecha_inicio', 'desc')
            ->first();

        $html_cuenta='';
        $html_cuenta_detra='';
        foreach($cuentas as $data){
            if ($data->id_tipo_cuenta !== 2) {
                $html_cuenta.='<option value="'.$data->id_cuenta_contribuyente.'">'.$data->nro_cuenta.'- '.$data->razon_social.' </option>';
            }else{
                $html_cuenta_detra.='<option value="'.$data->id_cuenta_contribuyente.'">'.$data->nro_cuenta.'- '.$data->razon_social.' </option>';
            }
        }

        return response()->json([
            'cotizacion'=>$cotizacion,
            'valorizacion_cotizacion'=>$detalle, 
            'html_item_valorizacion' => $html_item_valorizacion, 
            'html_cuenta'=>$html_cuenta, 
            'html_cuenta_detra'=>$html_cuenta_detra, 
            'sub_total' => $sum_subtotal, 
            'igv' => $igv->porcentaje
            ]);
    }

    public function nextCodigoOrden($id_tp_docum)
    {
        $mes = date('m', strtotime("now"));
        $anio = date('y', strtotime("now"));

        $num = DB::table('logistica.log_ord_compra')
            ->where('id_tp_documento', $id_tp_docum)->count();

        $correlativo = $this->leftZero(4, ($num + 1));

        if ($id_tp_docum == 2) {
            $codigoOrden = "OC-{$anio}{$mes}-{$correlativo}";
        } else if ($id_tp_docum == 3) {
            $codigoOrden = "OS-{$anio}{$mes}-{$correlativo}";
        } else {
            $codigoOrden = "-{$anio}{$mes}-{$correlativo}";
        }
        return $codigoOrden;
    }
    public function listar_cuadros_compartivos(){
        $sql_detalle_grupo_cotizacion = DB::table('logistica.log_grupo_cotizacion')
         ->select(
            'log_grupo_cotizacion.id_grupo_cotizacion',
            'log_grupo_cotizacion.codigo_grupo',
            'log_grupo_cotizacion.fecha_inicio',
            'log_grupo_cotizacion.fecha_fin',
            'log_grupo_cotizacion.estado as estado_grupo_cotizacion'
        )
        ->where([
            ['log_grupo_cotizacion.estado', '!=', 7]
         ])
        ->get();

        $sql_cotizacion = DB::table('logistica.log_cotizacion')
        ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
        ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
        ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
        ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
        ->leftJoin('contabilidad.adm_contri as contri', 'contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi as identi', 'identi.id_doc_identidad', '=', 'contri.id_doc_identidad')
        ->select(
            'log_cotizacion.id_cotizacion',
            'log_detalle_grupo_cotizacion.id_grupo_cotizacion',
            'log_cotizacion.codigo_cotizacion',
            'log_cotizacion.id_proveedor',
            'log_cotizacion.estado_envio',
            'log_cotizacion.estado as estado_cotizacion',
            'log_cotizacion.id_empresa',
            'log_cotizacion.fecha_registro',

            'adm_contri.id_contribuyente',
            'adm_contri.razon_social',
            'adm_contri.nro_documento',
            'adm_contri.id_doc_identidad',
            'sis_identi.descripcion as nombre_doc_identidad',

            'contri.razon_social as razon_social_empresa',
            'contri.nro_documento as nro_documento_empresa',
            'contri.id_doc_identidad as id_doc_identidad_empresa',
            'identi.descripcion as nombre_doc_idendidad_empresa'
            // DB::raw("(SELECT  COUNT(log_valorizacion_cotizacion.id_cotizacion) FROM logistica.log_valorizacion_cotizacion
            // WHERE log_valorizacion_cotizacion.id_cotizacion = log_cotizacion.id_cotizacion)::integer as cantidad_items")
            // 'alm_req.id_requerimiento',
            // 'alm_req.codigo AS codigo_requerimiento'
        )
        ->where([
            ['log_cotizacion.estado', '!=', 7]
        ])
        ->get();

        $cotizacionList = [];
        $grupoList = [];

        foreach ($sql_detalle_grupo_cotizacion as $data) {
            $grupoList[] = [
                'id_grupo_cotizacion' => $data->id_grupo_cotizacion,
                'codigo_grupo' => $data->codigo_grupo,
                'fecha_inicio' => $data->fecha_inicio,
                'fecha_fin' => $data->fecha_fin,
                'estado' => $data->estado_grupo_cotizacion
            ];
        }

        foreach($sql_cotizacion as $data){
            $cotizacionList[] = [
                'id_cotizacion' => $data->id_cotizacion,
                'id_grupo_cotizacion' => $data->id_grupo_cotizacion,
                'codigo_cotizacion' => $data->codigo_cotizacion,
                'id_proveedor' => $data->id_proveedor,
                'id_contribuyente' => $data->id_contribuyente,
                'estado_envio' => $data->estado_envio,
                'fecha_registro' => $data->fecha_registro,
                'estado' => $data->estado_cotizacion,
                'id_empresa' => $data->id_empresa,
                'razon_social' => $data->razon_social,
                'nro_documento' => $data->nro_documento,
                'id_doc_identidad' => $data->id_doc_identidad,
                'nombre_doc_identidad' => $data->nombre_doc_identidad,
                'razon_social_empresa' => $data->razon_social_empresa,
                'nro_documento_empresa' => $data->nro_documento_empresa,
                'id_doc_identidad_empresa' => $data->id_doc_identidad_empresa,
                'nombre_doc_idendidad_empresa' => $data->nombre_doc_idendidad_empresa
            ];
        }

        $cuadroComparativoList= $grupoList;

        for ($i = 0; $i < sizeof($grupoList); $i++) {
            for ($k = 0; $k < sizeof($cotizacionList); $k++) {
                if ($cotizacionList[$k]['id_grupo_cotizacion'] == $grupoList[$i]['id_grupo_cotizacion']) {
                    $cuadroComparativoList[$i]['cotizacion'][] = $cotizacionList[$k];
                }
            }
        }
    return  $cuadroComparativoList;
    }

    public function comparative_board_enabled_to_value()
    {
        $output['data'] = $this->listar_cuadros_compartivos();
        return response()->json($output);
    }

    public function listar_buenas_pro(){
        $query = DB::table('logistica.log_valorizacion_cotizacion')
        ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_valorizacion_cotizacion.id_cotizacion')
        ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
        ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
        ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
        ->leftJoin('contabilidad.adm_contri as contri', 'contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi as identi', 'identi.id_doc_identidad', '=', 'contri.id_doc_identidad')
        ->leftJoin('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
        ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'valoriza_coti_detalle.id_requerimiento')
        ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
        ->leftJoin('logistica.log_grupo_cotizacion', 'log_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
        ->select(
            'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
            'log_grupo_cotizacion.id_grupo_cotizacion',
            'log_grupo_cotizacion.codigo_grupo',
            'log_cotizacion.id_cotizacion',
            'log_cotizacion.codigo_cotizacion',
            'log_cotizacion.id_proveedor',
            'log_cotizacion.estado_envio',
            'log_cotizacion.estado',
            'log_cotizacion.id_empresa',
            'log_cotizacion.fecha_registro',

            'adm_contri.id_contribuyente',
            'adm_contri.razon_social',
            'adm_contri.nro_documento',
            'adm_contri.id_doc_identidad',
            'sis_identi.descripcion as nombre_doc_identidad',

            'contri.razon_social as razon_social_empresa',
            'contri.nro_documento as nro_documento_empresa',
            'contri.id_doc_identidad as id_doc_identidad_empresa',
            'identi.descripcion as nombre_doc_idendidad_empresa',
            DB::raw("(SELECT  COUNT(log_valorizacion_cotizacion.id_cotizacion) FROM logistica.log_valorizacion_cotizacion
            WHERE log_valorizacion_cotizacion.id_cotizacion = log_cotizacion.id_cotizacion)::integer as cantidad_items"),
            'alm_req.id_requerimiento',
            'alm_req.codigo AS codigo_requerimiento'

        )
        ->where([
            ['log_cotizacion.estado', '!=', 7],
            ['log_valorizacion_cotizacion.estado', '=', 2], // tenga buena pro
        ])
        ->get();


        $cotizacionAux = [];
        $cotizacionList = [];
        $requerimiento__cotizacion = [];

        foreach ($query as $data) {
            $requerimiento__cotizacion[] = [
                'id_cotizacion' => $data->id_cotizacion,
                'id_requerimiento' => $data->id_requerimiento,
                'codigo_requerimiento' => $data->codigo_requerimiento
            ];
            if (in_array($data->id_cotizacion, $cotizacionAux) === false) {
                $cotizacionAux[] = $data->id_cotizacion;
                $cotizacionList[] = [
                    'id_valorizacion_cotizacion' => $data->id_valorizacion_cotizacion,
                    'id_grupo_cotizacion' => $data->id_grupo_cotizacion,
                    'codigo_grupo' => $data->codigo_grupo,
                    'id_cotizacion' => $data->id_cotizacion,
                    'codigo_cotizacion' => $data->codigo_cotizacion,
                    'id_proveedor' => $data->id_proveedor,
                    'id_contribuyente' => $data->id_contribuyente,
                    'estado_envio' => $data->estado_envio,
                    'fecha_registro' => $data->fecha_registro,
                    'estado' => $data->estado,
                    'id_empresa' => $data->id_empresa,
                    'razon_social' => $data->razon_social,
                    'nro_documento' => $data->nro_documento,
                    'id_doc_identidad' => $data->id_doc_identidad,
                    'nombre_doc_identidad' => $data->nombre_doc_identidad,
                    'razon_social_empresa' => $data->razon_social_empresa,
                    'nro_documento_empresa' => $data->nro_documento_empresa,
                    'id_doc_identidad_empresa' => $data->id_doc_identidad_empresa,
                    'nombre_doc_idendidad_empresa' => $data->nombre_doc_idendidad_empresa,
                    'cantidad_items' => $data->cantidad_items
                ];
            }
        }

        $aux = [];
        for ($k = 0; $k < sizeof($requerimiento__cotizacion); $k++) {
            if (in_array($requerimiento__cotizacion[$k]['id_cotizacion'] . $requerimiento__cotizacion[$k]['id_requerimiento'], $aux) === false) {
                $aux[] = $requerimiento__cotizacion[$k]['id_cotizacion'] . $requerimiento__cotizacion[$k]['id_requerimiento'];
                $requerimientos_cotiza[] = $requerimiento__cotizacion[$k];
            }
        }

        $aux = [];
        $req = '';
        for ($i = 0; $i < sizeof($cotizacionList); $i++) {
            for ($k = 0; $k < sizeof($requerimientos_cotiza); $k++) {
                if ($cotizacionList[$i]['id_cotizacion'] == $requerimientos_cotiza[$k]['id_cotizacion']) {
                    $cotizacionList[$i]['requerimiento'][] = $requerimientos_cotiza[$k];
                }
            }
        }
    return  $cotizacionList;

    }

    public function data_buenas_pro()
    {
        $output['data'] = $this->listar_buenas_pro();
        return response()->json($output);
    }

    public function listar_requerimientos_pendientes(){
        $alm_req = DB::table('almacen.alm_req')
            ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.id_estado_doc', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            ->leftJoin('proyectos.proy_op_com', 'proy_op_com.id_op_com', '=', 'alm_req.id_op_com')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')

            // ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_requerimiento', '=', 'alm_req.id_requerimiento')
            // ->leftJoin('logistica.log_ord_compra', 'log_ord_compra.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
            // ->leftJoin('almacen.guia_com_oc', 'guia_com_oc.id_oc', '=', 'log_ord_compra.id_orden_compra')
            ->select(
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.concepto',
                'alm_req.fecha_requerimiento',
                'alm_req.id_tipo_requerimiento',
                'alm_tp_req.descripcion AS tipo_req_desc',
                'sis_usua.usuario',
                'rrhh_rol.id_area',
                'adm_area.descripcion AS area_desc',
                'rrhh_rol.id_rol',
                'rrhh_rol.id_rol_concepto',
                'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
                'alm_req.id_grupo',
                'adm_grupo.descripcion AS adm_grupo_descripcion',
                'alm_req.id_op_com',
                'proy_op_com.codigo as codigo_op_com',
                'proy_op_com.descripcion as descripcion_op_com',
                'alm_req.concepto AS alm_req_concepto',
                // 'log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion',
                'alm_req.id_prioridad',
                'alm_req.fecha_registro',
                'alm_req.estado',
                'alm_req.id_sede',
                'sis_sede.codigo as codigo_sede_empresa',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                DB::raw("(CASE WHEN alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
        //         DB::raw("(SELECT  COUNT(log_ord_compra.id_orden_compra) FROM logistica.log_ord_compra
        // WHERE log_ord_compra.id_grupo_cotizacion = log_detalle_grupo_cotizacion.id_grupo_cotizacion)::integer as cantidad_orden"),
        //         DB::raw("(SELECT  COUNT(mov_alm.id_mov_alm) FROM almacen.mov_alm
        // WHERE mov_alm.id_guia_com = guia_com_oc.id_guia_com and 
        // guia_com_oc.id_oc = log_ord_compra.id_orden_compra)::integer as cantidad_entrada_almacen")

            )
            ->where([['alm_req.estado', '=', 1],['alm_req.id_tipo_requerimiento','=',1],['alm_req.confirmacion_pago','=',true]])
            ->orderBy('alm_req.id_requerimiento', 'desc')
            ->get();
        return response()->json(["data" => $alm_req]);

 
    }
    public function listar_requerimientos_atendidos(){
        $alm_req = DB::table('almacen.alm_req')
            ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.id_estado_doc', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            ->leftJoin('proyectos.proy_op_com', 'proy_op_com.id_op_com', '=', 'alm_req.id_op_com')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->join('logistica.log_ord_compra', 'alm_req.id_requerimiento', '=', 'log_ord_compra.id_requerimiento')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')

            ->select(
                'alm_req.id_requerimiento',
                'log_ord_compra.id_orden_compra',
                'log_ord_compra.codigo_softlink',
                'log_ord_compra.fecha as fecha_orden',
                'alm_req.codigo',
                'alm_req.concepto',
                'alm_req.fecha_requerimiento',
                'alm_req.id_tipo_requerimiento',
                'alm_tp_req.descripcion AS tipo_req_desc',
                'sis_usua.usuario',
                'rrhh_rol.id_area',
                'adm_area.descripcion AS area_desc',
                'rrhh_rol.id_rol',
                'rrhh_rol.id_rol_concepto',
                'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
                'alm_req.id_grupo',
                'adm_grupo.descripcion AS adm_grupo_descripcion',
                'alm_req.id_op_com',
                'proy_op_com.codigo as codigo_op_com',
                'proy_op_com.descripcion as descripcion_op_com',
                'alm_req.concepto AS alm_req_concepto',
                // 'log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion',
                'alm_req.id_prioridad',
                'alm_req.fecha_registro',
                'alm_req.estado as estado_requerimiento',
                'log_ord_compra.estado as estado_orden',
                'log_ord_compra.id_sede',
                'sis_sede.codigo as codigo_sede_empresa',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                DB::raw("(CASE WHEN alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")

            )
            ->where([['alm_req.estado', '=', 5],['log_ord_compra.estado', '!=', 7],['alm_req.id_tipo_requerimiento','=',1],['alm_req.confirmacion_pago','=',true]])
            ->orderBy('alm_req.id_requerimiento', 'desc')
            ->get();

            $output['data']=$alm_req;

        return response()->json($output);

 
    }


    public function get_requerimiento_orden($id)
    {

        $alm_req = DB::table('almacen.alm_req')
            ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->leftJoin('contabilidad.adm_contri as contrib', 'adm_empresa.id_contribuyente', '=', 'contrib.id_contribuyente')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.id_estado_doc', '=', 'adm_estado_doc.id_estado_doc')
            
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
            ->leftJoin('proyectos.proy_op_com', 'proy_op_com.id_op_com', '=', 'alm_req.id_op_com')
            ->leftJoin('proyectos.proy_presup', 'alm_req.id_presupuesto', '=', 'proy_presup.id_presupuesto')
            ->leftJoin('rrhh.rrhh_perso as perso_natural', 'alm_req.id_persona', '=', 'perso_natural.id_persona')
            ->leftJoin('comercial.com_cliente', 'alm_req.id_cliente', '=', 'com_cliente.id_cliente')
            ->leftJoin('contabilidad.adm_contri as contri_cliente', 'com_cliente.id_contribuyente', '=', 'contri_cliente.id_contribuyente')
            ->leftJoin('configuracion.ubi_dis', 'alm_req.id_ubigeo_entrega', '=', 'ubi_dis.id_dis')
            ->leftJoin('configuracion.ubi_prov', 'ubi_dis.id_prov', '=', 'ubi_prov.id_prov')
            ->leftJoin('configuracion.ubi_dpto', 'ubi_prov.id_dpto', '=', 'ubi_dpto.id_dpto')

            ->select(
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.concepto',
                'alm_req.id_moneda',
                'alm_req.id_periodo',
                'alm_req.id_prioridad',
                'alm_req.id_estado_doc',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'sis_sede.id_empresa',
                'alm_req.id_grupo',
                'contrib.razon_social as razon_social_empresa',
                'sis_sede.codigo as codigo_sede_empresa',
                'adm_empresa.logo_empresa',
                'alm_req.fecha_requerimiento',
                'alm_req.id_periodo',
                'alm_req.id_tipo_requerimiento',
                'alm_req.observacion',
                'alm_tp_req.descripcion AS tp_req_descripcion',
                'alm_req.id_usuario',
                DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno)  AS persona"),
                'sis_usua.usuario',
                'alm_req.id_rol',
                'rrhh_rol.id_rol_concepto',
                'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
                'alm_req.id_area',
                'adm_area.descripcion AS area_descripcion',
                'alm_req.id_op_com',
                'proy_op_com.codigo as codigo_op_com',
                'proy_op_com.descripcion as descripcion_op_com',
                'alm_req.id_presupuesto',
                'alm_req.objetivo',
                'alm_req.id_occ',
                'alm_req.archivo_adjunto',
                'alm_req.fecha_registro',
                'alm_req.estado',
                'alm_req.id_sede',
                'alm_req.id_persona',
                'perso_natural.nro_documento as dni_persona',
                DB::raw("(perso_natural.nombres) || ' ' || (perso_natural.apellido_paterno) || ' ' || (perso_natural.apellido_materno)  AS nombre_persona"),
                'alm_req.tipo_cliente',
                'alm_req.id_cliente',
                'contri_cliente.nro_documento as cliente_ruc',
                'contri_cliente.razon_social as cliente_razon_social',
                'alm_req.id_ubigeo_entrega',
                DB::raw("(ubi_dis.descripcion) || ' ' || (ubi_prov.descripcion) || ' ' || (ubi_dpto.descripcion)  AS name_ubigeo"),
                'alm_req.direccion_entrega',
                'alm_req.id_almacen',
                DB::raw("(CASE WHEN alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
                ['alm_req.id_requerimiento', '=', $id],
                ['alm_req.estado', '=', 1]
            ])
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
                    // 'objetivo' => $data->objetivo, deprecated ( eliminar campo)
                    'id_moneda' => $data->id_moneda,
                    'id_periodo' => $data->id_periodo,
                    'id_estado_doc' => $data->id_estado_doc,
                    'estado_doc' => $data->estado_doc,
                    'bootstrap_color' => $data->bootstrap_color,
                    'id_prioridad' => $data->id_prioridad,
                    'id_occ' => $data->id_occ,
                    'id_empresa' => $data->id_empresa,
                    'id_grupo' => $data->id_grupo,
                    'id_sede' => $data->id_sede,
                    'razon_social_empresa' => $data->razon_social_empresa,
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
                    'archivo_adjunto' => $data->archivo_adjunto,
                    'id_op_com' => $data->id_op_com,
                    'codigo_op_com' => $data->codigo_op_com,
                    'descripcion_op_com' => $data->descripcion_op_com,
                    'id_presupuesto' => $data->id_presupuesto,
                    'observacion' => $data->observacion,
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
                    'id_almacen' => $data->id_almacen
                    
                ];
            };

            $alm_det_req = DB::table('almacen.alm_prod')
                ->leftJoin('almacen.alm_item', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
                ->leftJoin('almacen.alm_det_req', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
                ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
                ->leftJoin('almacen.alm_und_medida as und_medida_det_req', 'alm_det_req.id_unidad_medida', '=', 'und_medida_det_req.id_unidad_medida')
                ->leftJoin('almacen.alm_det_req_adjuntos', 'alm_det_req_adjuntos.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')

                ->select(
                    'alm_det_req.id_detalle_requerimiento',
                    'alm_req.id_requerimiento',
                    'alm_req.codigo AS codigo_requerimiento',
                    'alm_det_req.id_requerimiento',
                    'alm_det_req.id_item AS id_item_alm_det_req',
                    'alm_det_req.precio_referencial',
                    'alm_det_req.cantidad',
                    'alm_det_req.id_unidad_medida',
                    'und_medida_det_req.descripcion AS unidad_medida',
                    'alm_det_req.obs',
                    'alm_det_req.fecha_registro AS fecha_registro_alm_det_req',
                    'alm_det_req.fecha_entrega',
                    'alm_det_req.lugar_entrega',
                    'alm_det_req.descripcion_adicional',
                    'alm_det_req.id_tipo_item',
                    'alm_det_req.estado',
                    
                    
                    
                    'alm_item.id_item',
                    'alm_det_req.id_producto',
                    'alm_item.codigo AS codigo_item',
                    'alm_item.fecha_registro AS alm_item_fecha_registro',
                    'alm_prod.codigo AS alm_prod_codigo',
                    'alm_prod.part_number',
                    'alm_prod.descripcion AS alm_prod_descripcion',

                    'alm_det_req_adjuntos.id_adjunto AS adjunto_id_adjunto',
                    'alm_det_req_adjuntos.archivo AS adjunto_archivo',
                    'alm_det_req_adjuntos.estado AS adjunto_estado',
                    'alm_det_req_adjuntos.fecha_registro AS adjunto_fecha_registro',
                    'alm_det_req_adjuntos.id_detalle_requerimiento AS adjunto_id_detalle_requerimiento'
                )
                ->where([
                    ['alm_det_req.id_requerimiento', '=', $requerimiento[0]['id_requerimiento']]
                    // ,['alm_det_req.estado', '=', 1]
                ])
                ->orderBy('alm_item.id_item', 'asc')
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

            $total = 0;
            if (isset($alm_det_req)) {
                $lastId = "";
                $detalle_requerimiento = [];
                foreach ($alm_det_req as $data) {
                    if ($data->id_detalle_requerimiento !== $lastId) {
                        $subtotal =+ $data->cantidad *  $data->precio_referencial;
                        $total = $subtotal;
                        $detalle_requerimiento[] = [
                            'id_detalle_requerimiento'  => $data->id_detalle_requerimiento,
                            'id_requerimiento'          => $data->id_requerimiento,
                            'codigo_requerimiento'      => $data->codigo_requerimiento,
                            'id_item'                   => $data->id_item,
                            'cantidad'                  => $data->cantidad,
                            'id_unidad_medida'             => $data->id_unidad_medida,
                            'unidad_medida'             => $data->unidad_medida,
                            'precio_referencial'        => $data->precio_referencial,
                            'descripcion_adicional'     => $data->descripcion_adicional,
                            'fecha_entrega'             => $data->fecha_entrega,
                            'lugar_entrega'             => $data->lugar_entrega,
                            'fecha_registro'            => $data->fecha_registro_alm_det_req,
                            'obs'                       => $data->obs,
                            'estado'                    => $data->estado,
                            'codigo_item'                => $data->codigo_item,
                            'id_tipo_item'                => $data->id_tipo_item,
                            'id_producto'               => $data->id_producto,
                            'part_number'               => $data->part_number,
                            'descripcion'               => $data->descripcion_adicional,
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

        $collect = collect($requerimiento[0]);
        $collect->put('total',$total);

        $data = [
            "requerimiento" => $collect,
            "det_req" => $detalle_requerimiento
        ];

        return $data;
    }

    public function revertir_orden_requerimiento($id_orden, $id_requerimiento){
        try {
            DB::beginTransaction();
            $status = 0;
            $msj = [];
            $countOrden = DB::table('logistica.log_ord_compra')
            ->where([
                    ['id_requerimiento','=',$id_requerimiento],
                    ['id_orden_compra','=',$id_orden]
                    ])
            ->count();
            if ($countOrden == 1){
                DB::table('logistica.log_ord_compra')
                ->where([
                        ['id_requerimiento',$id_requerimiento],
                        ['id_orden_compra',$id_orden]])
                ->update(
                    [
                        'estado' => 7,
                        'codigo_softlink' => null
                    ]);

                DB::table('logistica.log_det_ord_compra')
                ->where([['id_orden_compra',$id_orden]])
                ->update(
                    [
                        'estado' => 7
                    ]);

                $status = 200;

            }else{
                $msj[] = 'No es posible actualizar. existe '.$count.' orden(s) registrados.';
                $status = 402;
            }

            $countReq = DB::table('almacen.alm_req')
            ->where([
                    ['id_requerimiento','=',$id_requerimiento]
                    ])
            ->count();

            if ($countOrden == 1){
                DB::table('almacen.alm_req')
                ->where('id_requerimiento',$id_requerimiento)
                ->update(['estado'=>1]);
        
                DB::table('almacen.alm_det_req')
                ->where('id_requerimiento',$id_requerimiento)
                ->update(['estado'=>1]);
                $status = 200;

            }else{
                $msj[] = 'No es posible actualizar. existe '.$count.' requerimiento(s) registrados.';
                $status = 402;

            }
            $output=['status'=>$status, 'mensaje'=>$msj];

            DB::commit();
            return response()->json($output);

        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function save_cliente(Request $request){
        try {
            DB::beginTransaction();

        $status=0;
        $tipo_cliente = $request->tipo_cliente;
        if($tipo_cliente == 1){ #persona natural

            $id_persona = DB::table('rrhh.rrhh_perso')
            ->insertGetId(
                [
                    'id_documento_identidad' => $request->tipo_documento?$request->tipo_documento:null,
                    'nro_documento' => $request->nro_documento?$request->nro_documento:null,
                    'nombres' => $request->nombre?$request->nombre:null,
                    'apellido_paterno' => $request->apellido_paterno?$request->apellido_paterno:null,
                    'apellido_materno' => $request->apellido_materno?$request->apellido_materno:null,
                    'estado' => 1,
                    'telefono' => $request->telefono?$request->telefono:null,
                    'direccion' => $request->direccion?$request->direccion:null,
                    'fecha_registro' => date('Y-m-d H:i:s')
 
                ],
                'id_persona'
            );
            if($id_persona>0){
                $status=200;
            }

        }else if($tipo_cliente == 2){ #persona juridica
            $id_contribuyente = DB::table('contabilidad.adm_contri')
            ->insertGetId(
                [
                    'id_doc_identidad' => $request->tipo_documento?$request->tipo_documento:null,
                    'nro_documento' => $request->nro_documento?$request->nro_documento:null,
                    'razon_social' => $request->razon_social?$request->razon_social:null,
                    'estado' => 1,
                    'telefono' => $request->telefono?$request->telefono:null,
                    'direccion_fiscal' => $request->direccion?$request->direccion:null,
                    'fecha_registro' => date('Y-m-d H:i:s')
 
                ],
                'id_contribuyente'
            );
            if($id_contribuyente>0){
                $status=200;
            }
            $id_cliente = DB::table('comercial.com_cliente')
            ->insertGetId(
                [
                    'id_contribuyente' => $id_contribuyente,
                    'estado' => 1,
                    'fecha_registro' => date('Y-m-d H:i:s')
                ],
                'id_cliente'
            );
            if($id_cliente>0){
                $status=200;
            }
        }
        $output=['status'=>$status];

        DB::commit();
        return response()->json($output);

        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function guardar_orden_por_requerimiento(Request $request){
        try {
            DB::beginTransaction();

            $usuario = Auth::user()->id_usuario;
            $tp_doc = ($request->id_tipo_doc !== null ? $request->id_tipo_doc : 2);
            $codigo = $this->nextCodigoOrden($tp_doc);

            $id_orden = DB::table('logistica.log_ord_compra')
            ->insertGetId(
                [
                    'id_grupo_cotizacion' => $request->id_grupo_cotizacion?$request->id_grupo_cotizacion:null,
                    'id_tp_documento' =>  $tp_doc,
                    'fecha' => date('Y-m-d H:i:s'),
                    'id_usuario' => $usuario,
                    'id_moneda' => ($request->id_moneda?$request->id_moneda:null),
                    'id_proveedor' => $request->id_proveedor,
                    'codigo' => $codigo,
                    'monto_subtotal' => $request->monto_subtotal?$request->monto_subtotal:null,
                    'igv_porcentaje' => $request->igv_porcentaje?$request->igv_porcentaje:null,
                    'monto_igv' => $request->monto_igv?$request->monto_igv:null,
                    'monto_total' => $request->monto_total?$request->monto_total:null,
                    'plazo_entrega' => $request->plazo_entrega?$request->plazo_entrega:null,
                    'id_condicion' => $request->id_condicion?$request->id_condicion:null,
                    'plazo_dias' => $request->plazo_dias?$request->plazo_dias:null,
                    'id_cotizacion' => $request->id_cotizacion?$request->id_cotizacion:null,
                    'id_cta_principal' => $request->id_cta_principal?$request->id_cta_principal:null,
                    'id_cta_alternativa' => $request->id_cta_alternativa?$request->id_cta_alternativa:null,
                    'id_cta_detraccion' => $request->id_cta_detraccion?$request->id_cta_detraccion:null,
                    'personal_responsable' => $request->contacto_responsable?$request->contacto_responsable:null,
                    'id_sede' => $request->sede?$request->sede:null,
                    'id_requerimiento' => $request->id_requerimiento,
                    'en_almacen' => false,
                    'estado' => 1,
                    'codigo_softlink' => ($request->codigo_orden!==null ? $request->codigo_orden : ''),
                ],
                'id_orden_compra'
            );

            $dataDetalle = json_decode($request->detalle_requerimiento);


            foreach ($dataDetalle as $d) {
                DB::table('logistica.log_det_ord_compra')
                ->insert([
                    'id_orden_compra'=>$id_orden,
                    'id_item'=> ($d->id_item ? $d->id_item : null),
                    'cantidad'=> $d->cantidad,
                    'id_unidad_medida'=> $d->id_unidad_medida,
                    'precio'=> $d->precio_referencial,
                    'subtotal'=> ($d->precio_referencial * $d->cantidad),
                    'estado'=> 1
                    // 'fecha_registro'=> date('Y-m-d H:i:s')
                ]);
            }

                DB::table('almacen.alm_req')
                ->where('id_requerimiento',$request->id_requerimiento)
                ->update(['estado'=>5]);
        
                DB::table('almacen.alm_det_req')
                ->where('id_requerimiento',$request->id_requerimiento)
                ->update(['estado'=>5]);

            //Agrega accion en requerimiento
            DB::table('almacen.alm_req_obs')
            ->insert([  'id_requerimiento'=>$request->id_requerimiento,
                        'accion'=>'ATENDIDO',
                        'descripcion'=>'Se generó Orden de Compra '.$codigo,
                        'id_usuario'=>$usuario,
                        'fecha_registro'=>date('Y-m-d H:i:s')
            ]);

            DB::commit();
            return response()->json($id_orden);

        } catch (\PDOException $e) {
            DB::rollBack();
        }

    }

    public function guardar_orden_compra(Request $request)
    {
        $id_tp_documento =  $request->id_tp_documento;
        $usuario = Auth::user()->id_usuario;
        $codigo = $this->nextCodigoOrden($id_tp_documento);
        $id_orden = DB::table('logistica.log_ord_compra')
            ->insertGetId(
                [
                    'id_grupo_cotizacion' => $request->id_grupo_cotizacion,
                    'id_tp_documento' => $id_tp_documento,
                    'fecha' => date('Y-m-d H:i:s'),
                    'id_usuario' => $usuario,
                    'id_moneda' => $request->id_moneda,
                    'id_proveedor' => $request->id_proveedor,
                    'codigo' => $codigo,
                    'monto_subtotal' => $request->monto_subtotal,
                    'igv_porcentaje' => $request->igv_porcentaje,
                    'monto_igv' => $request->monto_igv,
                    'monto_total' => $request->monto_total,
                    'plazo_entrega' => $request->plazo_entrega,
                    'id_condicion' => $request->id_condicion,
                    'plazo_dias' => $request->plazo_dias,
                    'id_cotizacion' => $request->id_cotizacion,
                    'id_cta_principal' => $request->id_cta_principal,
                    'id_cta_alternativa' => $request->id_cta_alternativa,
                    'id_cta_detraccion' => $request->id_cta_detraccion,
                    'personal_responsable' => $request->contacto_responsable,
                    'en_almacen' => false,
                    'estado' => 1
                ],
                'id_orden_compra'
            );
        $id_val_array = explode(',', $request->id_val);
        $id_item_array = explode(',', $request->id_item);
        $count = count($id_val_array);
        $id_val='';
        for ($i = 0; $i < $count; $i++) {
            $id_val = $id_val_array[$i];
            $id_item = $id_item_array[$i];

            DB::table('logistica.log_det_ord_compra')->insert([
                'id_orden_compra' => $id_orden,
                'id_item' => ($id_item ? $id_item : null),
                'id_valorizacion_cotizacion' => $id_val,
                'estado' => 1
            ]);

            DB::table('logistica.log_valorizacion_cotizacion')
                ->where('id_valorizacion_cotizacion', $id_val)
                ->update(['estado' => 5]); // estado Atendido ( con orden)
        }


            // buscar id_req por id_cotizacion
        $id_req = $this->get_id_req_by_id_coti($request->id_cotizacion);
        if(isset($id_req) && $id_req > 0){
            DB::table('almacen.alm_req') //requerimiento cambia su estado
                ->where('id_requerimiento', $id_req)
                ->update(['id_estado_doc' => 5]); // estado Atendido ( con orden)            
        }

        $data_doc_aprob = DB::table('administracion.adm_documentos_aprob')->insertGetId(
            [
                'id_tp_documento' => $id_tp_documento,
                'codigo_doc'      => $codigo,
                'id_doc'          => $id_orden

            ],
            'id_doc_aprob'
        );

        return response()->json($id_orden);
    }

    public function get_id_req_by_id_coti($id_cotizacion){
        $output = DB::table('logistica.log_valorizacion_cotizacion')
        ->select(
            'alm_det_req.id_requerimiento'
        )
        ->join('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
        ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'valoriza_coti_detalle.id_detalle_requerimiento')
        ->join('almacen.alm_req', 'alm_det_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
        ->where([
            ['log_valorizacion_cotizacion.id_cotizacion', '=', $id_cotizacion]
            ])
        ->first();
 
        $id = $output?$output->id_requerimiento:0;
        return $id;
    }

    public function update_orden_compra(Request $request)
    {
        $data = DB::table('logistica.log_ord_compra')
            ->where('id_orden_compra', $request->id_orden_compra)
            ->update(
                [
                    // 'id_grupo_cotizacion' => $request->id_grupo_cotizacion,
                    'id_moneda' => $request->id_moneda,
                    'id_proveedor' => $request->id_proveedor,
                    'monto_subtotal' => $request->monto_subtotal,
                    'igv_porcentaje' => $request->igv_porcentaje,
                    'monto_igv' => $request->monto_igv,
                    'monto_total' => $request->monto_total,
                    // 'id_condicion' => $request->id_condicion,
                    'plazo_dias' => $request->plazo_dias,
                    'id_cotizacion' => $request->id_cotizacion,
                    'id_cta_principal' => $request->id_cta_principal,
                    'id_cta_alternativa' => $request->id_cta_alternativa,
                    'id_cta_detraccion' => $request->id_cta_detraccion,
                    'personal_responsable' => $request->responsable
                 ],
                'id_orden_compra'
            );

        // $detalle = DB::table('logistica.log_det_ord_compra')
        // ->where([['id_orden_compra','=',$request->id_orden_compra],
        //         ['estado','=',1]])
        // ->get();

        // foreach($detalle as $det){
        // DB::table('logistica.log_det_ord_compra')
        //     ->where('id_orden_compra', $request->id_orden_compra)
        //     ->update(['estado' => 7]);
        // }

        // $id_val_array = explode(',', $request->id_val);
        // $id_item_array = explode(',', $request->id_item);
        // $count = count($id_val_array);

        // for ($i = 0; $i < $count; $i++) {
        //     $id_val = $id_val_array[$i];
        //     $id_item = $id_item_array[$i];

        //     DB::table('logistica.log_det_ord_compra')
        //     ->where('id_orden_compra', $request->id_orden_compra)
        //     ->update([
        //         'id_orden_compra' => $request->id_orden_compra,
        //         'id_item' => $id_item,
        //         'id_valorizacion_cotizacion' => $id_val
        //         // 'estado' => 1
        //     ]);
        // }
        return response()->json($request->id_orden_compra);
    }


    public function guardar_aprobacion_orden(Request $request){
        $r= $request->all();
        $statusOption=['success','fail'];
        $status = '';
        $userRolConceptoList= $this->get_current_user()['idRolConceptoList'];
        $idvobo = 1;
        $motivo = "Aprobado";
        $usuario = Auth::user();
        $id_usu = $usuario->id_usuario;
        $id_tra = $usuario->id_trabajador;
        $id_rol = $usuario->login_rol;
        $rolesUsuario = $usuario->trabajador->roles;
        $idarea = 0;
        $na_flujo=0;
        $id_rol_concepto_list=[];
        $id_rol_list=[];
        $id_tipo_doc=0;
        $id_operacion_list=[];
        $consult_nivel_aprob=[];
        $numDocList=[];
        $idAreaOfRolAprobList=[];

        foreach ($rolesUsuario as $role) {
            $idarea = $role->pivot->id_area;
        
        }

        $id_doc_aprob=$this->consult_doc_aprob($r['id_orden'],2);
         
        $dataReq = $this->get_data_req_by_id_orden($r['id_orden']);
        $grupoReq= array_unique($dataReq['data']['grupo']);
        $tipoReq= array_unique($dataReq['data']['tipo_requerimiento']);
        // $rolReq= array_unique($dataReq['data']['rol']);
        $idsReqList= array_unique($dataReq['data']['requerimiento']);

        if(in_array(1,$tipoReq)){
            $id_tipo_doc = $this->get_id_tipo_documento('Orden de Compra');
        }else{
            $id_tipo_doc= $this->get_id_tipo_documento('Orden de Servicio');
        }
        foreach($idsReqList as $id_req){
            $numDocList[] = $this->consult_doc_aprob($id_req,1); 
        }

        foreach ($numDocList as $numDoc) {
            $idAreaOfRolAprobList[] = $this->getAreaOfRolAprob($numDoc, 1)['id']; //{num doc},{tp doc}
        }

        // refactoring ...

        foreach($grupoReq as $id_g ){
            $id_operacion_list[] =$this->get_id_operacion($id_g,0,$id_tipo_doc);
        }
        if(count($id_operacion_list)>0){
            foreach($id_operacion_list as $id_op){
                $consult_nivel_aprob[] = $this->consult_nivel_aprob($userRolConceptoList,$id_op);
            }
            
            
            if(count($consult_nivel_aprob)>0){
                foreach($consult_nivel_aprob as $cna){
                    if($cna['flujo'] >0){
                        $na_flujo=$cna['flujo'];
                        $id_rol_concepto_list[]=$cna['rol_aprob'];
                    }
                }
                // cmabiar id_rol_concepto por id_rol segun id_trabajador y los id_rol_conceptos de adm_operacion
                $id_rol_list= $this->get_id_rol_by_id_trab_id_rol_concepto($id_tra,$id_rol_concepto_list);
            

            }else{
                return ['status'=>$statusOption[0], 'data'=>[]]; 
            } 


        }else{
            return ['status'=>$statusOption[0], 'data'=>[]]; 
        }

        // $output[]=$na_flujo;
         if( $id_rol_list[0]<= 0){
            return ['status'=>$statusOption[0], 'data'=>[]]; 
        }

        $id_area= $this->get_id_area_by_id_rol($id_rol_list[0]);

        // return $id_rol_list[0];
        

        $hoy = date('Y-m-d H:i:s');
        $insertar = DB::table('administracion.adm_aprobacion')->insertGetId(
            [
                'id_flujo'              => $na_flujo,
                'id_doc_aprob'          => $id_doc_aprob,
                'id_vobo'               => $idvobo,
                'id_usuario'            => $id_usu,
                'id_area'               => $id_area,
                'fecha_vobo'            => $hoy,
                'detalle_observacion'   => $motivo,
                'id_rol'                => $id_rol_list[0]
            ],
            'id_aprobacion'
        );

 

        $estado_aprobado = $this->get_estado_doc('Aprobado');

        $output=[];

        $sql = DB::table('logistica.log_ord_compra')
        ->where('id_orden_compra', $r['id_orden'])
        ->update(
            [
                'estado' => $estado_aprobado
            ],
            'id_orden_compra'
        );

        if($sql){
            $status= $statusOption[0];
        }else{
            $status=$statusOption[1];
        }

        $output=['status'=>$status, 'data'=>$sql];

        return response()->json($output);
 
    }


    public function mostrar_cuentas_bco($id_contribuyente)
    {
        $data = DB::table('contabilidad.adm_cta_contri')
            ->select(
                'adm_cta_contri.*',
                'adm_contri.razon_social as banco',
                'adm_tp_cta.descripcion as tipo_cta'
            )
            ->join('contabilidad.cont_banco', 'cont_banco.id_banco', '=', 'adm_cta_contri.id_banco')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'cont_banco.id_contribuyente')
            ->join('contabilidad.adm_tp_cta', 'adm_tp_cta.id_tipo_cuenta', '=', 'adm_cta_contri.id_tipo_cuenta')
            ->where([
                ['adm_cta_contri.id_contribuyente', '=', $id_contribuyente],
                ['adm_cta_contri.estado', '=', 1]
            ])
            ->get();
        return response()->json($data);
    }

    public function listar_ordenes()
    {
        $data = DB::table('logistica.log_ord_compra')
            ->select(
                'log_ord_compra.*',
                'adm_contri.id_contribuyente',
                'adm_contri.razon_social',
                'adm_contri.nro_documento'
            )
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->where([['log_ord_compra.estado', '!=', 7]])
            ->orderBy('log_ord_compra.fecha','desc')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function listar_ordenes_proveedor($id_proveedor)
    {
        $data = DB::table('logistica.log_ord_compra')
            ->select(
                'log_ord_compra.*',
                'adm_contri.id_contribuyente',
                'adm_contri.razon_social',
                'adm_contri.nro_documento'
            )
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->where([['log_ord_compra.estado', '!=', 7],
                     ['log_ord_compra.id_proveedor','=',$id_proveedor],
                     ['log_ord_compra.en_almacen','=',false],
                     ['log_ord_compra.id_tp_documento','=',2]])
            ->orderBy('log_ord_compra.fecha','desc')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_orden($id_orden)
    {
        $orden = DB::table('logistica.log_ord_compra')
            ->select(
                'log_ord_compra.*',
                'adm_contri.id_contribuyente',
                'adm_contri.razon_social',
                'adm_contri.nro_documento',
                'adm_estado_doc.estado_doc'
            )
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'log_ord_compra.estado')
            ->where([['log_ord_compra.id_orden_compra', '=', $id_orden]])
            ->first();

        $data = DB::table('contabilidad.adm_cta_contri')
            ->select(
                'adm_cta_contri.*',
                'adm_contri.razon_social as banco',
                'adm_tp_cta.descripcion as tipo_cta'
            )
            ->join('contabilidad.cont_banco', 'cont_banco.id_banco', '=', 'adm_cta_contri.id_banco')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'cont_banco.id_contribuyente')
            ->join('contabilidad.adm_tp_cta', 'adm_tp_cta.id_tipo_cuenta', '=', 'adm_cta_contri.id_tipo_cuenta')
            ->where([
                ['adm_cta_contri.id_contribuyente', '=', $orden->id_contribuyente],
                ['adm_cta_contri.estado', '=', 1]
            ])
            ->get();

        $html_cuenta = '';
        $detra = '';
        foreach ($data as $d) {
            if ($d->id_tipo_cuenta !== 2) {
                $html_cuenta .= '<option value="' . $d->id_cuenta_contribuyente . '">' . $d->nro_cuenta . ' - ' . $d->banco . '</option>';
            } else {
                $detra .= '<option value="' . $d->id_cuenta_contribuyente . '">' . $d->nro_cuenta . ' - ' . $d->banco . '</option>';
            }
        }
        return response()->json(['orden' => $orden, 'html' => $html_cuenta, 'detra' => $detra]);
    }

    public function listar_detalle_orden($id_orden)
    {
        $detalle = DB::table('logistica.log_det_ord_compra')
            ->select(
                'log_det_ord_compra.*',
                DB::raw("(pers_aut.nombres) || ' ' || (pers_aut.apellido_paterno) || ' ' || (pers_aut.apellido_materno)  AS nombre_personal_autorizado"),

                DB::raw("(CASE 
                WHEN alm_item.id_item isNUll THEN alm_det_req.descripcion_adicional 
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
                "),
                'alm_item.id_producto',
                'alm_item.id_servicio',
                'alm_item.id_equipo',
                'log_valorizacion_cotizacion.cantidad_cotizada',
                'log_valorizacion_cotizacion.precio_cotizado',
                'log_valorizacion_cotizacion.monto_descuento',
                'log_valorizacion_cotizacion.subtotal as subtotal_cotizada'
                // 'alm_det_req.id_item'
            )
            ->leftJoin('configuracion.sis_usua as sis_usua_aut', 'sis_usua_aut.id_usuario', '=', 'log_det_ord_compra.personal_autorizado')
            ->leftJoin('rrhh.rrhh_trab as trab_aut', 'trab_aut.id_trabajador', '=', 'sis_usua_aut.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu as post_aut', 'post_aut.id_postulante', '=', 'trab_aut.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as pers_aut', 'pers_aut.id_persona', '=', 'post_aut.id_persona')

            ->leftjoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', 'log_det_ord_compra.id_valorizacion_cotizacion')
            ->leftjoin('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
            ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'valoriza_coti_detalle.id_detalle_requerimiento')
            ->leftjoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_det_ord_compra.id_unidad_medida')
            ->leftjoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftjoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
            ->where([
                ['log_det_ord_compra.id_orden_compra', '=', $id_orden]
                // ['log_det_ord_compra.estado', '<>', 7]
            ])
            ->get();

            
            $detalle_val=[];
            $id_val_cot_list=[];
            foreach ($detalle as $d) {
                if(in_array($d->id_valorizacion_cotizacion,$id_val_cot_list)==false){
                    array_push($id_val_cot_list,$d->id_valorizacion_cotizacion);
                    $detalle_val[]=$d;
                }
            }

            // return $detalle_val;

         $i = 1;
        $html = '';

        foreach ($detalle_val as $d) {

            // $tdSinDescripcionAdicional='<td><input class="oculto" name="descripcion_adicional" value="' . $d->id_item . '"/><button type="button" class="btn btn-xs btn-primary" name="btnSinDescripcionAdicional" title="Agregar Descripcion Adicional" onClick="modalSinDescripcionAdicional('.$d->id_detalle_orden.','.$d->id_valorizacion_cotizacion.');" >Descripción Adicional</button></td>';

            $tdSinDescripcionAdicional='<td></td>';
            $tdMostrarDescripcionAdicional='<td> <i class="fas fa-sticky-note fa-2x" data-toggle="tooltip" data-original-title="'.$d->descripcion_adicional.'"></i></td>';

            $tdActualizarCodigo='<td><input class="oculto" name="id_item" value="' . $d->id_item . '"/><button type="button" class="btn btn-xs btn-info" name="btnActualizarCodigoItem" title="Actualizar Código" onClick="modalActualizarCodigoItem('.$d->id_detalle_orden.','.$d->id_valorizacion_cotizacion.');" disabled>Actualizar Códgio</button></td>';
            $tdMostrarCodigo= '<td><input class="oculto" name="id_item" value="' . $d->id_item . '"/>' . $d->codigo . '</td>';

            $html .= '
            <tr>
                <td><input class="oculto" name="id_valorizacion_cotizacion" value="' . ($d->id_valorizacion_cotizacion?$d->id_valorizacion_cotizacion:'') . '"/>' . $i . '</td>';
                
                if($d->id_item == null || $d->id_item == 0){
                    $html.=$tdActualizarCodigo;
                }else{
                    $html.=$tdMostrarCodigo;
                }

                $html.= ' <td>' . $d->descripcion . '</td>
                <td>' . ($d->unidad_medida?$d->unidad_medida:'') . '</td>
                <td>' . ($d->cantidad?$d->cantidad:$d->cantidad_cotizada) . '</td>
                <td>' . ($d->precio?$d->precio:$d->precio_cotizado) . '</td>
                <td>' . ($d->monto_descuento?$d->monto_descuento:'') . '</td>
                <td>' . ($d->subtotal?$d->subtotal:$d->subtotal_cotizada) . '</td>';
                
 
                if($d->descripcion_adicional == null){
                    $html.=$tdSinDescripcionAdicional;
                }else{
                    $html.=$tdMostrarDescripcionAdicional;
                }

                $html.='<td>';
                if(trim($d->nombre_personal_autorizado) != null || strlen(trim($d->nombre_personal_autorizado)) > 0){
                    $html.='<i class="fas fa-address-card fa-2x fa-pull-left" data-toggle="tooltip" data-original-title="PERSONAL AUTORIZADO: '.$d->nombre_personal_autorizado.'"></i>';
                }
                if($d->lugar_despacho != null || strlen($d->lugar_despacho) > 0){
                $html.='<i class="fas fa-map-marker-alt fa-2x fa-pull-left" data-toggle="tooltip" data-original-title="DESPACHO: '.$d->lugar_despacho.'"></i>';
                }
                $html.='</td>
                <td><button type="button" class="btn btn-sm btn-primary activation" name="btnEditarDespacho" title="Editar Despacho" onClick="editarDespacho( event,' . $d->id_valorizacion_cotizacion . ');"><i class="fas fa-edit"></i></button></td>
            </tr>';
            $i++;
        }
        return response()->json($html);
    }

    public function guardar_cuenta_banco(Request $request)
    {
        $id_cuenta_contribuyente = DB::table('contabilidad.adm_cta_contri')->insertGetId(
            [
                'id_contribuyente' => $request->id_contribuyente,
                'id_banco' => $request->id_banco,
                'id_tipo_cuenta' => $request->id_tipo_cuenta,
                'nro_cuenta' => $request->nro_cuenta,
                'nro_cuenta_interbancaria' => $request->nro_cuenta_interbancaria,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
            'id_cuenta_contribuyente'
        );

        $data = DB::table('contabilidad.adm_cta_contri')
            ->select(
                'adm_cta_contri.*',
                'adm_contri.razon_social as banco',
                'adm_tp_cta.descripcion as tipo_cta'
            )
            ->join('contabilidad.cont_banco', 'cont_banco.id_banco', '=', 'adm_cta_contri.id_banco')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'cont_banco.id_contribuyente')
            ->join('contabilidad.adm_tp_cta', 'adm_tp_cta.id_tipo_cuenta', '=', 'adm_cta_contri.id_tipo_cuenta')
            ->where([
                ['adm_cta_contri.id_contribuyente', '=', $request->id_contribuyente],
                ['adm_cta_contri.estado', '=', 1]
            ])
            ->get();

        $html = '';
        $detra = '';

        foreach ($data as $d) {
            if ($d->id_tipo_cuenta == 2) { //   2->cta de detracción 
                if ($d->id_cuenta_contribuyente == $id_cuenta_contribuyente) {
                    $detra .= '<option value="' . $d->id_cuenta_contribuyente . '" selected>' . $d->nro_cuenta . ' - ' . $d->banco . '</option>';
                } else {
                    $detra .= '<option value="' . $d->id_cuenta_contribuyente . '">' . $d->nro_cuenta . ' - ' . $d->banco . '</option>';
                }
            } else {
                if ($d->id_cuenta_contribuyente == $id_cuenta_contribuyente) {
                    $html .= '<option value="' . $d->id_cuenta_contribuyente . '" selected>' . $d->nro_cuenta . ' - ' . $d->banco . '</option>';
                } else {
                    $html .= '<option value="' . $d->id_cuenta_contribuyente . '">' . $d->nro_cuenta . ' - ' . $d->banco . '</option>';
                }
            }
        }
        if ($request->id_tipo_cuenta == 2) {
            return json_encode(['html' => $detra, 'tipo' => $request->id_tipo_cuenta]);
        } else {
            return json_encode(['html' => $html, 'tipo' => $request->id_tipo_cuenta]);
        }
    }

    public function get_orden_by_req($id_requerimiento){
        $cotizaciones = DB::table('logistica.log_cotizacion')
        ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
        ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'log_valorizacion_cotizacion.id_requerimiento')
        ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
        ->leftJoin('logistica.log_grupo_cotizacion', 'log_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
        ->select(
            'log_grupo_cotizacion.id_grupo_cotizacion'
            // 'log_cotizacion.id_cotizacion'
        )
        ->where([
            ['log_cotizacion.estado', '=', 1],
            ['alm_req.id_requerimiento', '=', $id_requerimiento] 
        ])
        ->get();

        if(isset($cotizaciones ) && count($cotizaciones)>0){
        
        
        foreach($cotizaciones as $data){
            $id_grupo_cotizacion[]= $data->id_grupo_cotizacion;
        }

        $id_grupo_cotizacion_list = array_unique($id_grupo_cotizacion);
        $log_ord_compra = DB::table('logistica.log_ord_compra')
        ->select(
            'log_ord_compra.id_orden_compra',
            'log_ord_compra.id_grupo_cotizacion',
            'log_ord_compra.codigo',
            'log_ord_compra.id_proveedor',
            'adm_contri.razon_social AS razon_social_proveedor',
            'sis_identi.descripcion AS tipo_doc_proveedor',
            'adm_contri.nro_documento AS nro_documento_proveedor',
            'log_ord_compra.fecha',
            'log_ord_compra.id_usuario',
            DB::raw("(pers.nombres) || ' ' || (pers.apellido_paterno) || ' ' || (pers.apellido_materno) as nombre_usuario"),
            'log_ord_compra.personal_responsable',
            DB::raw("(pers_res.nombres) || ' ' || (pers_res.apellido_paterno) || ' ' || (pers_res.apellido_materno) as nombre_personal_responsable"),
            'log_ord_compra.monto_total',
            'log_ord_compra.estado'
        )
        ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
        ->Join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->Join('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')

        ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'log_ord_compra.id_usuario')
        ->leftJoin('rrhh.rrhh_trab as trab', 'trab.id_trabajador', '=', 'sis_usua.id_trabajador')
        ->leftJoin('rrhh.rrhh_postu as post', 'post.id_postulante', '=', 'trab.id_postulante')
        ->leftJoin('rrhh.rrhh_perso as pers', 'pers.id_persona', '=', 'post.id_persona')

        ->leftJoin('configuracion.sis_usua as sis_usua_res', 'sis_usua_res.id_usuario', '=', 'log_ord_compra.personal_responsable')
        ->leftJoin('rrhh.rrhh_trab as trab_res', 'trab_res.id_trabajador', '=', 'sis_usua_res.id_trabajador')
        ->leftJoin('rrhh.rrhh_postu as post_res', 'post_res.id_postulante', '=', 'trab_res.id_postulante')
        ->leftJoin('rrhh.rrhh_perso as pers_res', 'pers_res.id_persona', '=', 'post_res.id_persona')
        ->where([
            ['log_ord_compra.estado', '>', 0]
        ])
        ->whereIn('log_ord_compra.id_grupo_cotizacion', $id_grupo_cotizacion_list)
        ->get();
        $id_orden_compra_list=[];
        foreach($log_ord_compra as $data){
            $id_orden_compra_list[]=$data->id_orden_compra; //array id_orden_compra
            $ord_compra[]=[
                'id_orden_compra'=>$data->id_orden_compra,
                'id_grupo_cotizacion'=>$data->id_grupo_cotizacion,
                'codigo'=>$data->codigo,
                'id_proveedor'=>$data->id_proveedor,
                'razon_social_proveedor'=>$data->razon_social_proveedor,
                'tipo_doc_proveedor'=>$data->tipo_doc_proveedor,
                'nro_documento_proveedor'=>$data->nro_documento_proveedor,
                'fecha'=>$data->fecha,
                'id_usuario'=>$data->id_usuario,
                'nombre_usuario'=>$data->nombre_usuario,
                'personal_responsable'=>$data->personal_responsable,
                'monto_total'=>$data->monto_total,
                'estado'=>$data->estado
            ];
        }
        if(count($id_orden_compra_list) ==0){
            return [];
        }
        $log_det_ord_compra = DB::table('logistica.log_det_ord_compra')
        ->select(
            'log_det_ord_compra.id_orden_compra',
            'log_det_ord_compra.id_valorizacion_cotizacion'
        )
        ->where([
            ['log_det_ord_compra.estado', '>', 0]
        ])
        ->whereIn('log_det_ord_compra.id_orden_compra', $id_orden_compra_list)
        ->get();

        foreach($log_det_ord_compra as $data){
            $id_valorizacion_cotizacion_list[]= $data->id_valorizacion_cotizacion;
        }

        $log_valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')
        ->select(
            'log_valorizacion_cotizacion.id_cotizacion'
         )
        ->where([
            ['log_valorizacion_cotizacion.estado', '>', 0]
        ])
        ->whereIn('log_valorizacion_cotizacion.id_valorizacion_cotizacion', $id_valorizacion_cotizacion_list)
        ->get();
        
        foreach($log_valorizacion_cotizacion as $data){
            $id_cotizacion_list_uniq[]=$data->id_cotizacion;
        }
        
        $id_cotizacion_list_uniq= array_unique($id_cotizacion_list_uniq);

        $log_cotizacion = DB::table('logistica.log_cotizacion')
        ->select(
            'log_cotizacion.id_cotizacion',
            'log_cotizacion.id_empresa',
            'adm_contri.razon_social as razon_social_empresa',
            'adm_contri.id_doc_identidad',
            'sis_identi.descripcion as tipo_documento_empresa',
            'adm_contri.nro_documento as nro_documento_empresa'
         )
         ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
         ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
         ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')

        ->where([
            ['log_cotizacion.estado', '>', 0]
        ])
        ->whereIn('log_cotizacion.id_cotizacion', $id_cotizacion_list_uniq)
        ->get();
        
        foreach($log_cotizacion as $data){
           $cotizacion[]=[
                'id_cotizacion'=>$data->id_cotizacion,
                'id_empresa'=>$data->id_empresa,
                'razon_social_empresa'=>$data->razon_social_empresa,
                'tipo_documento_empresa'=>$data->tipo_documento_empresa,
                'nro_documento_empresa'=>$data->nro_documento_empresa
            ];
        }

        $ord_compra[0]['cotizaciones']= $cotizacion;
        $output = $ord_compra;
        return $output;

    }else{ return [];}
        
    }

    public function get_orden($id_orden_compra)
    {
        $data = DB::table('logistica.log_ord_compra')
            ->select(
                'log_ord_compra.id_orden_compra',
                'log_ord_compra.codigo',
                'log_ord_compra.plazo_dias',
                'log_ord_compra.fecha AS fecha_orden',
                'log_ord_compra.id_usuario',
                'log_ord_compra.id_moneda',
                'sis_moneda.simbolo as moneda_simbolo',
                'sis_moneda.descripcion as moneda_descripcion',
                'log_ord_compra.monto_igv',
                'log_ord_compra.monto_total',
                DB::raw("(pers.nombres) || ' ' || (pers.apellido_paterno) || ' ' || (pers.apellido_materno) as nombre_usuario"),
                
                'log_ord_compra.personal_responsable',
                DB::raw("(adm_ctb_contac.nombre) || ' - ' || (adm_ctb_contac.cargo) as nombre_personal_responsable"),
                // DB::raw("CONCAT(pers_res.nombres,' ',pers_res.apellido_paterno,' ',pers_res.apellido_materno) as nombre_personal_responsable"),

                'adm_tp_docum.descripcion AS tipo_documento',
                'sis_identi.descripcion AS tipo_doc_proveedor',
                'log_prove.id_proveedor',
                'adm_contri.razon_social AS razon_social_proveedor',
                'adm_contri.nro_documento AS nro_documento_proveedor',
                'adm_contri.telefono AS telefono_proveedor',
                'adm_contri.direccion_fiscal AS direccion_fiscal_proveedor',
                'log_cotizacion.id_empresa',
                'contab_sis_identi.descripcion AS tipo_doc_empresa',
                'contab_contri.razon_social AS razon_social_empresa',
                'contab_contri.nro_documento AS nro_documento_empresa',
                'contab_contri.direccion_fiscal AS direccion_fiscal_empresa',
                'alm_req.codigo AS codigo_requerimiento',

                'cont_tp_doc.descripcion AS tipo_doc_contable',
                'log_cdn_pago.descripcion AS condicion_pago',
                'log_cotizacion.condicion_credito_dias',
                'log_cotizacion.nro_cuenta_principal',
                'log_cotizacion.nro_cuenta_alternativa',
                'log_cotizacion.nro_cuenta_detraccion',
                'log_cotizacion.email_proveedor',
                // 'log_det_ord_compra.*',
                'log_det_ord_compra.personal_autorizado',
                'log_det_ord_compra.lugar_despacho as lugar_despacho_orden',
                DB::raw("(pers_aut.nombres) || ' ' || (pers_aut.apellido_paterno) || ' ' || (pers_aut.apellido_materno) AS nombre_personal_autorizado"),
                'log_det_ord_compra.descripcion_adicional AS descripcion_detalle_orden',
                

                'valoriza_coti_detalle.id_detalle_requerimiento',
                'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
                'log_valorizacion_cotizacion.cantidad_cotizada',
                'log_valorizacion_cotizacion.precio_cotizado',
                'log_valorizacion_cotizacion.id_unidad_medida',
                'alm_und_medida.descripcion AS unidad_medida_cotizado',
                'log_valorizacion_cotizacion.flete',
                'log_valorizacion_cotizacion.porcentaje_descuento',
                'log_valorizacion_cotizacion.monto_descuento',
                'log_valorizacion_cotizacion.subtotal',
                'log_valorizacion_cotizacion.plazo_entrega',
                'log_valorizacion_cotizacion.incluye_igv',
                'log_valorizacion_cotizacion.garantia',
                // 'log_valorizacion_cotizacion.lugar_despacho',
                'alm_det_req.descripcion_adicional AS descripcion_requerimiento',
                'alm_det_req.id_item',
                'alm_item.codigo AS codigo_item',
                'alm_prod.descripcion AS descripcion_producto',
                'alm_prod.codigo AS producto_codigo',
                'log_servi.codigo AS servicio_codigo',
                'log_servi.descripcion AS descripcion_servicio'
            )
            ->leftJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_orden_compra', '=', 'log_ord_compra.id_orden_compra')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_ord_compra.id_condicion')
            ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', 'log_det_ord_compra.id_valorizacion_cotizacion')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_valorizacion_cotizacion.id_unidad_medida')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'log_ord_compra.id_usuario')
            ->leftJoin('rrhh.rrhh_trab as trab', 'trab.id_trabajador', '=', 'sis_usua.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu as post', 'post.id_postulante', '=', 'trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as pers', 'pers.id_persona', '=', 'post.id_persona')

            // ->leftJoin('configuracion.sis_usua as sis_usua_res', 'sis_usua_res.id_usuario', '=', 'log_ord_compra.personal_responsable')
            // ->leftJoin('rrhh.rrhh_trab as trab_res', 'trab_res.id_trabajador', '=', 'sis_usua_res.id_trabajador')
            // ->leftJoin('rrhh.rrhh_postu as post_res', 'post_res.id_postulante', '=', 'trab_res.id_postulante')
            // ->leftJoin('rrhh.rrhh_perso as pers_res', 'pers_res.id_persona', '=', 'post_res.id_persona')

            ->leftJoin('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_datos_contacto', '=', 'log_ord_compra.personal_responsable')

            ->leftJoin('configuracion.sis_usua as sis_usua_aut', 'sis_usua_aut.id_usuario', '=', 'log_det_ord_compra.personal_autorizado')
            ->leftJoin('rrhh.rrhh_trab as trab_aut', 'trab_aut.id_trabajador', '=', 'sis_usua_aut.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu as post_aut', 'post_aut.id_postulante', '=', 'trab_aut.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as pers_aut', 'pers_aut.id_persona', '=', 'post_aut.id_persona')

            ->leftJoin('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'log_valorizacion_cotizacion.personal_autorizado')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->Join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->Join('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->Join('administracion.adm_tp_docum', 'adm_tp_docum.id_tp_documento', '=', 'log_ord_compra.id_tp_documento')
            // ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', 'log_det_ord_compra.id_valorizacion_cotizacion')
            ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_valorizacion_cotizacion.id_cotizacion')
            ->Join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
            ->leftJoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'log_cotizacion.id_tp_doc')
            ->Join('contabilidad.adm_contri as contab_contri', 'contab_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->Join('contabilidad.sis_identi as contab_sis_identi', 'contab_sis_identi.id_doc_identidad', '=', 'contab_contri.id_doc_identidad')
            ->Join('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
            ->Join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'valoriza_coti_detalle.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->where([
                ['log_ord_compra.id_orden_compra', '=', $id_orden_compra],
                ['log_ord_compra.estado', '!=', 7]
            ])
            ->get();

        $orden_header_orden = [];
        $orden_header_proveedor = [];
        $orden_header_empresa = [];
        $orden_condiciones = [];
        $det_orden = [];
        $orden_aprob =[];

        $id_val_cot_list=[];

        $num_doc = $this->consult_doc_aprob($id_orden_compra,2); 

        $adm_aprob=DB::table('administracion.adm_aprobacion')
        ->select('adm_flujo.nombre','adm_aprobacion.fecha_vobo')
        ->Join('administracion.adm_flujo', 'adm_flujo.id_flujo', '=', 'adm_aprobacion.id_flujo')
        ->where([
            ['adm_aprobacion.id_doc_aprob', '=', $num_doc]
        ])
        ->get();

        $groupIncluded = count($this->groupIncluded($id_orden_compra));

        $orden_aprob['aprob_necesarias']=$groupIncluded;
        $orden_aprob['total_aprob']=count($adm_aprob);
        foreach($adm_aprob as $item) {
            $orden_aprob['aprobaciones'][]=$item;
        }


        foreach ($data as $data) {
            if(in_array($data->id_valorizacion_cotizacion,$id_val_cot_list)==false){
                array_push($id_val_cot_list,$data->id_valorizacion_cotizacion);

                $orden_header_orden = [
                    'id_orden_compra' => $data->id_orden_compra,
                    'codigo' => $data->codigo,
                    'tipo_documento' => $data->tipo_documento,
                    'fecha_orden' => $data->fecha_orden,
                    'nombre_usuario' => $data->nombre_usuario,
                    'nombre_personal_responsable' => $data->nombre_personal_responsable,
                    'codigo_requerimiento' => $data->codigo_requerimiento,
                    'moneda_simbolo' => $data->moneda_simbolo,
                    'monto_igv' => $data->monto_igv,
                    'monto_total' => $data->monto_total,
                    'moneda_descripcion' => $data->moneda_descripcion,
                ];
                $orden_header_proveedor = [
                    'id_proveedor' => $data->id_proveedor,
                    'razon_social_proveedor' => $data->razon_social_proveedor,
                    'tipo_doc_proveedor' => $data->tipo_doc_proveedor,
                    'nro_documento_proveedor' => $data->nro_documento_proveedor,
                    'telefono_proveedor' => $data->telefono_proveedor,
                    'direccion_fiscal_proveedor' => $data->direccion_fiscal_proveedor,
                    'email_proveedor' => $data->email_proveedor
                ];
                $orden_header_empresa = [
                    'id_empresa' => $data->id_empresa,
                    'razon_social_empresa' => $data->razon_social_empresa,
                    'tipo_doc_empresa' => $data->tipo_doc_empresa,
                    'nro_documento_empresa' => $data->nro_documento_empresa,
                    'direccion_fiscal_empresa' => $data->direccion_fiscal_empresa
                ];
                $orden_condiciones = [
                    'tipo_doc_contable' => $data->tipo_doc_contable,
                    'condicion_pago' => $data->condicion_pago,
                    'plazo_dias' => $data->plazo_dias,
                    'condicion_credito_dias' => $data->condicion_credito_dias,
                    'nro_cuenta_principal' => $data->nro_cuenta_principal,
                    'nro_cuenta_alternativa' => $data->nro_cuenta_alternativa,
                    'nro_cuenta_detraccion' => $data->nro_cuenta_detraccion
                ];
    

                $det_orden[] = [
                    'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                    'id_item' => $data->id_item,
                    'codigo_item' => $data->codigo_item,
                    'descripcion_producto' => $data->descripcion_producto,
                    'descripcion_requerimiento' => $data->descripcion_requerimiento,
                    'descripcion_detalle_orden' => $data->descripcion_detalle_orden,
                    'cantidad_cotizada' => $data->cantidad_cotizada,
                    'id_unidad_medida' => $data->id_unidad_medida,
                    'unidad_medida_cotizado' => $data->unidad_medida_cotizado,
                    'precio_cotizado' => $data->precio_cotizado,
                    'flete' => $data->flete,
                    'porcentaje_descuento' => $data->porcentaje_descuento,
                    'monto_descuento' => $data->monto_descuento,
                    'subtotal' => $data->subtotal,
                    'plazo_entrega' => $data->plazo_entrega,
                    'incluye_igv' => $data->incluye_igv,
                    'garantia' => $data->garantia,
                    // 'lugar_despacho' => $data->lugar_despacho,
                    'nombre_personal_autorizado' => $data->nombre_personal_autorizado,
                    'lugar_despacho_orden' => $data->lugar_despacho_orden
                ];
            }
        }
        $result = [
            'header_orden' => $orden_header_orden,
            'header_proveedor' => $orden_header_proveedor,
            'header_empresa' => $orden_header_empresa,
            'condiciones' => $orden_condiciones,
            'detalle_orden' => $det_orden,
            'aprobaciones' => $orden_aprob,
        ];

        return $result;
    }

    public function imprimir_orden_pdf($id_orden_compra)
    {
        $ordenArray = $this->get_orden($id_orden_compra);
        // $ordenArray = json_decode($orden, true);
        $sizeOrdenHeader=count($ordenArray['header_orden']);
        
        if($sizeOrdenHeader == 0){
            $html = 'Error en documento';
            return $html;
        }

        $now = new \DateTime();

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
                    font-size: 9px;
                    box-sizing: border-box;
                    padding:10px;
                }
                table{
                    width:100%;
                    border-collapse: collapse;
                }
                .tablePDF thead{
                    padding:4px;
                    background-color:#d04f46;
                }
                .bgColorRed{
                
                }
                .tablePDF,
                .tablePDF tr td{
                    border: 1px solid #dbdbdb;
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
                footer {
                    position: absolute;
                    bottom: 0;
                    width: 100%;
                    height: auto;
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
            </style>
            </head>
            <body>
                <img src="./images/LogoSlogan-80.png" alt="Logo" height="75px">
                <br>
                <hr>
                <h1><center>' . $ordenArray['header_orden']['tipo_documento'] . '<br>' . $ordenArray['header_orden']['codigo'] . '</center></h1>
                <table border="0">
                    <tr>
                        <td class="subtitle verticalTop">Sr.(s)</td>
                        <td class="subtitle verticalTop">:</td>
                        <td width="50%" class="verticalTop">' . $ordenArray['header_proveedor']['razon_social_proveedor'] . '</td>
                        <td width="15%" class="subtitle verticalTop">Fecha de Emisión</td>
                        <td class="subtitle verticalTop">:</td>
                        <td>' . substr($ordenArray['header_orden']['fecha_orden'], 0, 11) . '</td>
                    </tr>
                    <tr>
                        <td class="subtitle">Dirección</td>
                        <td class="subtitle verticalTop">:</td>
                        <td class="verticalTop">' . $ordenArray['header_proveedor']['direccion_fiscal_proveedor'] . '</td>
                    </tr>
                    <tr>
                        <td class="subtitle">Telefono</td>
                        <td class="subtitle verticalTop">:</td>
                        <td class="verticalTop">' . $ordenArray['header_proveedor']['telefono_proveedor'] . '</td>
                    </tr>
                    <tr>
                        <td class="subtitle">Contacto</td>
                        <td class="subtitle verticalTop">:</td>
                        <td class="verticalTop">' . $ordenArray['header_proveedor']['email_proveedor'] . '</td>
                    </tr>
                    <tr>
                        <td class="subtitle">Responsable</td>
                        <td class="subtitle verticalTop">:</td>
                        <td class="verticalTop">' . $ordenArray['header_orden']['nombre_personal_responsable'] . '</td>
                    </tr>
                </table>';

                $html.='<p class="left" style="color:#d04f46">';

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

                $html.='</p>';


                $html.='<br>

                <table width="100%" class="tablePDF" border=0>
                <thead>
                    <tr class="subtitle">
                        <td width="2%">#</td>
                        <td width="48%">Descripción</td>
                        <td width="5%">Und</td>
                        <td width="5%">Cant.</td>
                        <td width="5%">Precio</td>
                        <td width="5%">IGV</td>
                        <td width="5%">Monto Dscto</td>
                        <td width="5%">Total</td>
                    </tr>   
                </thead>';

        $total = 0;
        foreach ($ordenArray['detalle_orden'] as $key => $data) {
            $html .= '<tr>';
            $html .= '<td>' . ($key + 1) . '</td>';
            if($data['descripcion_detalle_orden'] != null && strlen($data['descripcion_detalle_orden']) > 0){

                $html .= '<td>' . ($data['codigo_item'] ? $data['codigo_item'] : '0') . ' - ' . ($data['descripcion_producto'] ? $data['descripcion_producto'] : $data['descripcion_requerimiento']) . '<br><small><ul><li>'.$data['descripcion_detalle_orden'].'</li></ul></small></td>';

            }else{
                $html .= '<td>' . ($data['codigo_item'] ? $data['codigo_item'] : '0') . ' - ' . ($data['descripcion_producto'] ? $data['descripcion_producto'] : $data['descripcion_requerimiento']) . '</td>';
            }
            $html .= '<td>' . $data['unidad_medida_cotizado'] . '</td>';
            $html .= '<td class="right">' . $data['cantidad_cotizada'] . '</td>';
            $html .= '<td class="right">' . $data['precio_cotizado'] . '</td>';
            $html .= '<td class="right">0</td>';
            $html .= '<td class="right">' . $data['monto_descuento'] . '</td>';
            $html .= '<td class="right">' . $data['cantidad_cotizada'] * $data['precio_cotizado'] . '</td>';
            $html .= '</tr>';
            $total = $total + ($data['cantidad_cotizada'] * $data['precio_cotizado']);
        }

        $html .= '
                <tr>
                    <td class="right noBorder textBold"  colspan="7">Monto Neto '.$ordenArray['header_orden']['moneda_simbolo'].'</td>
                    <td class="right  noBorder textBold">' . $total . '</td>
                </tr>
                <tr>
                    <td class="right noBorder textBold"  colspan="7">IGV '.$ordenArray['header_orden']['moneda_simbolo'].'</td>
                    <td class="right noBorder textBold">' . $ordenArray['header_orden']['monto_igv'] . '</td>
                </tr>
                <tr>
                    <td class="right noBorder textBold"  colspan="7">Monto Total '.$ordenArray['header_orden']['moneda_simbolo'].'</td>
                    <td class="right noBorder textBold">' . $ordenArray['header_orden']['monto_total'] . '</td>
                </tr>
                </table>
                <br>

                <p class="subtitle">Datos para Despacho</p>
                <table width="100%" class="tablePDF" border=0>
                    <thead>
                        <tr class="subtitle">
                            <td width="2%">#</td>
                            <td width="38%">Descripción</td>
                            <td width="30%">Lugar de Despacho</td>
                            <td width="30%">Personal Autorizado</td>
                        </tr>
                    </thead>
        ';
            $total = 0;
            foreach ($ordenArray['detalle_orden'] as $key => $data) {
                $contador= $key + 1;
                $codigo= $data['codigo_item'] ? $data['codigo_item'] : '0';
                $desItem= $data['descripcion_producto'] ? $data['descripcion_producto'] : $data['descripcion_requerimiento'];
                $lugarDesp= $data['lugar_despacho_orden'];
                $persAut= $data['nombre_personal_autorizado'];
 
                $html .= '<tr>';
                $html .= '<td>' . $contador. '</td>';
                $html .= '<td>' .$codigo.'-'. $desItem . '</td>';
                $html .= '<td class="right">' . $lugarDesp . '</td>';
                $html .= '<td class="right">' . $persAut . '</td>';
                $html .= '</tr>';
            }

        $html.= '</table>
                <br>
                <p class="subtitle">Condición de Compra</p>
                <table border="0">
                    <tr>
                        <td width="20%"class="verticalTop">Forma de Pago</td>
                        <td width="5%" class="verticalTop">:</td>
                        <td width="70%" class="verticalTop">' . $ordenArray['condiciones']['condicion_pago'] . '</td>
                    </tr>
                    <tr>
                        <td width="20%" class="verticalTop">Plazo</td>
                        <td width="5%" class="verticalTop">:</td>
                        <td width="70%" class="verticalTop">' . $ordenArray['condiciones']['plazo_dias'] . '';
        if ($ordenArray['condiciones']['plazo_dias'] > 0) {
            $html .= ' días';
        }
        $html .= '</td>
                    </tr>
                    <tr>
                        <td width="20%"class="verticalTop">Req.</td>
                        <td width="5%" class="verticalTop">:</td>
                        <td width="70%" class="verticalTop">' . $ordenArray['header_orden']['codigo_requerimiento'] . '</td>
                    </tr>
                    <br>
                </table>
 
                <br>
                <p class="subtitle">Datos de Facturación</p>
                <table border="0">
                    <tr>
                        <td width="20%" class="verticalTop">Razon Social</td>
                        <td width="5%" class="verticalTop">:</td>
                        <td width="70%" class="verticalTop">' . $ordenArray['header_empresa']['razon_social_empresa'] . '</td>
                    </tr>
                    <tr>
                        <td width="20%"class="verticalTop">' . $ordenArray['header_empresa']['tipo_doc_empresa'] . '</td>
                        <td width="5%" class="verticalTop">:</td>
                        <td width="70%" class="verticalTop">' . $ordenArray['header_empresa']['nro_documento_empresa'] . '</td>
                    </tr>
                    <tr>
                        <td width="20%"class="verticalTop">Dirección</td>
                        <td width="5%" class="verticalTop">:</td>
                        <td width="70%" class="verticalTop">' . $ordenArray['header_empresa']['direccion_fiscal_empresa'] . '</td>
                    </tr>
                </table>

                <br/>
                <br/>
                <footer class="left">
                <p>GENERADO POR: ' . $ordenArray['header_orden']['nombre_usuario'] . '</p>
                <hr/>
                <table>
                    <tr>
                        <td>Oficina Principal</td>
                        <td>Urb. Villa del Mar M 22 - Fono: 053-484354 - Ilo</td>
                    </tr>
                    <tr>
                        <td>Oficina Logística</td>
                        <td>Urbanización Municipal cal. Condesuyos 103 - Arequipa</td>
                    </tr>
                    <tr>
                        <td>Oficina Logística</td>
                        <td>Mza. A Lote 03 APV. Vimcoop Samegua - Mariscal Nieto - Moquegua</td>
                    </tr>
                    <tr>
                        <td>Oficina Logística</td>
                        <td>Cal. R. Palma/p. Gamboa 960 - Tacna</td>
                    </tr>
                    <tr>
                        <td>Oficina Coorporativa</td>
                        <td>Cal Amador Merino Reyna 125 San Isidro - Lima</td>
                    </tr>
                    <tr>
                        <td>Mail</td>
                        <td>contacto@okcomputer.com.pe</td>
                    </tr>
                    <tr>
                        <td>Web</td>
                        <td>www.okcomputer.com.pe</td>
                    </tr>
                </footer>
            </body>
            
        </html>';
        // <p class="subtitle">Datos para Despacho</p>
        // <table border="0">
        //     <tr>
        //         <td width="20%" class="verticalTop">Destino / Dirección</td>
        //         <td width="5%" class="verticalTop">:</td>
        //         <td width="70%" class="verticalTop"></td>
        //     </tr>
        //     <tr>
        //         <td width="20%"class="verticalTop">Atención / Personal Autorizado</td>
        //         <td width="5%" class="verticalTop">:</td>
        //         <td width="70%" class="verticalTop"></td>
        //     </tr>
        // </table>
        return $html;
    }

    public function generar_orden_pdf($id_orden_compra)
    {
        $pdf = \App::make('dompdf.wrapper');
        // $id = $this->decode5t($id_orden_compra);
        $id = $id_orden_compra;
        $pdf->loadHTML($this->imprimir_orden_pdf($id));
        return $pdf->stream();
        return $pdf->download('orden.pdf');
    }

    public function anular_orden_compra($id_orden)
    {

    $orden = DB::table('logistica.log_ord_compra')
            ->where('id_orden_compra', $id_orden)
            ->update(['estado' => 7]);
    $detalle_orden = DB::table('logistica.log_det_ord_compra')
            ->where('id_orden_compra', $id_orden)
        ->update(['estado' => 7]);
        return response()->json($orden);
    }


    public function actualizar_item_det_req($id_item,$id_detalle_requerimiento){
        $statusOption=[200,400];
        $status='';

        if(isset($id_item) && isset($id_detalle_requerimiento)){ // actualizar item en alm_det_req
            $alm_det_req = DB::table('almacen.alm_det_req')
            ->where('id_detalle_requerimiento', '=', $id_detalle_requerimiento)
            ->update([
                'id_item' => $id_item    
                ],'id_detalle_requerimiento');
            if($alm_det_req){
                $status=$statusOption[0];
            }else{
                $status=$statusOption[1];
            }
        }
        $output=['status'=>$status, 'id_requerimiento'=>$alm_det_req];

        return $output;
    }

    public function actualizar_item_det_orden($id_item,$id_detalle_orden){
        $statusOption=['success','fail'];
        $status='';

        $sqldet_ord_compra = DB::table('logistica.log_det_ord_compra')
        ->select(
            'log_det_ord_compra.id_orden_compra'
        )
        ->where('id_detalle_orden', $id_detalle_orden)
        ->get();


        if(isset($id_item) && isset($id_detalle_orden)){ // actualizar item en alm_det_req
            $log_det_ord_compra = DB::table('logistica.log_det_ord_compra')
            ->where('id_detalle_orden', '=', $id_detalle_orden)
            ->update([
                'id_item' => $id_item    
            ],'id_detalle_orden');

            if($log_det_ord_compra){
                $status=$statusOption[0];
            }else{
                $status=$statusOption[1];
            }
        }
        $output=['status'=>$status, 'id_orden_compra'=>$sqldet_ord_compra->first()->id_orden_compra];
        return $output;
    }

    public function actualizar_item_sin_codigo(Request $request, $id_detalle_orden, $id_valorizacion){
        if($id_detalle_orden == 0){
            $status=204; //no content (sin id_detalle_orden)
            return ['status'=>$status];
        }
        $status=0;
        $id_item = $request->id_item;
        $sql1='';
        $sql2='';
        $log_valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')
        ->select('log_valorizacion_cotizacion.id_valorizacion_cotizacion','log_valorizacion_cotizacion.id_cotizacion')
        ->where('id_valorizacion_cotizacion', '=', $id_valorizacion)
        ->first();

        $valoriza_coti_detalle = DB::table('logistica.valoriza_coti_detalle')
        ->select(
            'valoriza_coti_detalle.id_detalle_requerimiento'
            )
        ->where('valoriza_coti_detalle.id_valorizacion_cotizacion', '=', $log_valorizacion_cotizacion->id_valorizacion_cotizacion)
        ->first();

        $alm_item = DB::table('almacen.alm_item')
        ->select(
            'alm_item.id_item',
            'alm_item.id_producto',
            'alm_item.id_servicio',
            'alm_item.id_equipo'
            )
        ->where('alm_item.id_item', '=', $id_item)
        ->first();

        
        if($alm_item){
            if($alm_item->id_producto !=null){
                    $sql2 = $this->actualizar_item_det_orden($id_item,$id_detalle_orden);
                
            }else if($alm_item->id_servicio !=null){
                    $sql2 = $this->actualizar_item_det_orden($id_item,$id_detalle_orden);
            }else if($alm_item->id_equipo !=null){
                    $sql2 = $this->actualizar_item_det_orden($id_item,$id_detalle_orden);
            }
        }

        $output = [ 'status'=>$sql2['status'],'id_orden_compra'=>$sql2['id_orden_compra']];

        return $output;
    }
    

    public function leftZero($lenght, $number)
    {
        $nLen = strlen($number);
        $zeros = '';
        for ($i = 0; $i < ($lenght - $nLen); $i++) {
            $zeros = $zeros . '0';
        }
        return $zeros . $number;
    }

    public function verSession()
    {
        $data = Auth::user();
        return $data;
    }

    public function userSession()
    {
        $id_rol = Auth::user()->login_rol;
        $id_usuario = Auth::user()->id_usuario;
        $id_trabajador = Auth::user()->id_trabajador;
        $usuario = Auth::user()->usuario;
        $estado = Auth::user()->estado;
        $nombre_corto = Auth::user()->nombre_corto;

        $dateNow= date('Y-m-d');

        $dataSession=[
            'id_rol'=>$id_rol,
            'id_usuario'=>$id_usuario,
            'id_trabajador'=>$id_trabajador,
            'usuario'=>$usuario,
            'estado'=>$estado,
            'nombre_corto'=>$nombre_corto,
            'roles'=>[]
        ];

        $rolConceptoUser = DB::table('administracion.rol_aprobacion')
        ->select(
            'rol_aprobacion.id_rol_aprobacion',
            'rol_aprobacion.id_area',
            'adm_area.descripcion as nombre_area',
            'rol_aprobacion.id_rol_concepto',
            'rrhh_rol_concepto.descripcion as rol_concepto',
            'rol_aprobacion.estado'
        )
        ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rol_aprobacion.id_rol_concepto')
        ->leftJoin('administracion.adm_area', 'adm_area.id_area', '=', 'rol_aprobacion.id_area')
        // ->where(function($q) use ($dateNow) {
        //     $q->where('rol_aprobacion.fecha_fin','>', $dateNow)
        //     ->orWhere('rol_aprobacion.fecha_fin', null);
        // })
        ->where([
            ['rol_aprobacion.id_trabajador', '=', $dataSession['id_trabajador']]
            ])
        ->whereNotIn( 'rol_aprobacion.estado', [2,7])
        ->get();

        $dataSession['roles']=$rolConceptoUser;

        return $dataSession;
    }

    // cuadro comparativo

    // public function only_valorizaciones($id_grupo){
    //     $hasWhere = null;

    //     if ($id_grupo > 0) {
    //         $hasWhere = ['log_grupo_cotizacion.id_grupo_cotizacion', '=', $id_grupo];
    //     }

    //     $log_cotizacion = DB::table('logistica.log_cotizacion')
    //         ->select(
    //             'log_cotizacion.id_cotizacion',
    //             'log_grupo_cotizacion.id_grupo_cotizacion',
    //             'log_grupo_cotizacion.codigo_grupo',
    //             'log_cotizacion.codigo_cotizacion',
    //             'cont_tp_doc.descripcion as tipo_documento',
    //             'log_cdn_pago.descripcion AS condicion_pago',
    //             'log_cotizacion.nro_cuenta_principal',
    //             'log_cotizacion.nro_cuenta_alternativa',
    //             'log_cotizacion.nro_cuenta_detraccion',
    //             'log_prove.id_proveedor',
    //             'adm_contri.razon_social',
    //             'adm_contri.nro_documento',
    //             'adm_contri.id_doc_identidad',
    //             'sis_identi.descripcion as nombre_doc_identidad'
    //         )
    //         ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')

    //         ->leftJoin('logistica.log_grupo_cotizacion', 'log_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
    //         ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
    //         ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
    //         ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
    //         ->leftJoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'log_cotizacion.id_tp_doc')
    //         ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_cotizacion.id_condicion_pago')
    //         ->where(
    //             [
    //                 ['log_cotizacion.estado', '>', 0],
    //                 ['log_cotizacion.estado', '!=', 7], // 7 =anulados
    //                 $hasWhere
    //             ]
    //         )
    //         ->get();
    //     if (sizeof($log_cotizacion) > 0) {
    //         foreach ($log_cotizacion as $data) {
    //             $id_cotizaciones[] = $data->id_cotizacion;
    //             $cotizacionArray[] = [

    //                 'id_cotizacion' => $data->id_cotizacion,
    //                 'id_grupo_cotizacion' => $data->id_grupo_cotizacion,
    //                 'codigo_grupo' => $data->codigo_grupo,
    //                 'codigo_cotizacion' => $data->codigo_cotizacion,
    //                 'tipo_documento' => $data->tipo_documento,
    //                 'condicion_pago' => $data->condicion_pago,
    //                 'nro_cuenta_principal' => $data->nro_cuenta_principal,
    //                 'nro_cuenta_alternativa' => $data->nro_cuenta_alternativa,
    //                 'nro_cuenta_detraccion' => $data->nro_cuenta_detraccion,
    //                 'proveedor' => [
    //                     "id_proveedor" => $data->id_proveedor,
    //                     "razon_social" => $data->razon_social,
    //                     "nro_documento" => $data->nro_documento,
    //                     "id_doc_identidad" => $data->id_doc_identidad,
    //                     "nombre_doc_identidad" => $data->nombre_doc_identidad
    //                 ],
    //                 'requerimientos' => []
    //             ];
    //         }


    //         $log_valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')
    //             ->select(
    //                 'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
    //                 'log_valorizacion_cotizacion.id_cotizacion',
    //                 'log_valorizacion_cotizacion.id_requerimiento',
    //                 'alm_req.codigo as codigo_requerimiento'
    //             )
    //             ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'log_valorizacion_cotizacion.id_requerimiento')
    //             ->where(
    //                 [
    //                     ['log_valorizacion_cotizacion.cantidad_cotizada', '<>', null],
    //                     ['log_valorizacion_cotizacion.precio_cotizado', '<>', null],
    //                     ['log_valorizacion_cotizacion.estado', '>', 0]
    //                 ]
    //             )
    //             ->whereIn('log_valorizacion_cotizacion.id_cotizacion', $id_cotizaciones)
    //             ->get();

    //         $idCotizaciones = [];
    //         $idRequerimientos = [];
    //         $tam_log_valorizacion = count($log_valorizacion_cotizacion);
    //         if($tam_log_valorizacion >0){
    //             foreach ($log_valorizacion_cotizacion as $data) {
    //                 $valorizacionArray[] = [
    //                     'id_cotizacion' => $data->id_cotizacion,
    //                     'id_requerimiento' => $data->id_requerimiento,
    //                     'codigo_requerimiento' => $data->codigo_requerimiento
    //                 ];
    //                 // }
    
    //             }
    //             // add codigo de requerimiento
    //             $storageIdRequerimiento = [];
    //             for ($i = 0; $i < sizeof($cotizacionArray); $i++) {
    //                 for ($j = 0; $j < sizeof($valorizacionArray); $j++) {
    //                     if ($cotizacionArray[$i]['id_cotizacion'] == $valorizacionArray[$j]['id_cotizacion']) {
    //                         if (in_array($valorizacionArray[$j]['id_requerimiento'], $storageIdRequerimiento) == false) {
    //                             array_push($storageIdRequerimiento, $valorizacionArray[$j]['id_requerimiento']);
    //                             $cotizacionArray[$i]['requerimientos'][] = $valorizacionArray[$j];
    //                         }
    //                     }
    //                 }
    //                 $storageIdRequerimiento = [];
    //             }
    //         }else{
    //             $cotizacionArray = [];
    //         }

    //     } else {
    //         $cotizacionArray = [];
    //         return response()->json($cotizacionArray);
    //     }
    //     return response()->json($cotizacionArray);
    // }

    public function grupo_cotizaciones($codigo_cotiazacion, $codigo_cuadro_comparativo, $id_grupo, $estado_envio, $id_empresa, $valorizacion_completa_incompleta, $id_cotizacion_alone)
    {
        $ListaCotiConValorizacionIncompleta=[];
        $output=[];
        $idGrupoCotizacion=0;
        $ListaCotiConValorizacionFilter=[];
        $hasWhere = [
            ['log_cotizacion.estado', '>', 0],
            ['log_cotizacion.estado', '!=', 7] // 7 =anulados
        ];
        if (strlen($codigo_cotiazacion) > 1) {
            $grupo_cotizacion = DB::table('logistica.log_grupo_cotizacion')
                ->select(
                    'log_grupo_cotizacion.id_grupo_cotizacion'
                )
                ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_grupo_cotizacion.id_grupo_cotizacion')
                ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_cotizacion')
                ->where([['log_cotizacion.codigo_cotizacion', '=', $codigo_cotiazacion]])
                ->first();
            $hasWhere[] = ['log_grupo_cotizacion.id_grupo_cotizacion', '=', $grupo_cotizacion->id_grupo_cotizacion];
        }

        if (strlen($codigo_cuadro_comparativo) > 1) {
            $hasWhere[] = ['log_grupo_cotizacion.codigo_grupo', '=', $codigo_cuadro_comparativo];
        }

        if ($id_grupo > 0) {
            // $hasWhere[] = ['log_grupo_cotizacion.id_grupo_cotizacion', '=', $id_grupo];
        }
        if ($estado_envio > 0) {
            $hasWhere[] = ['log_cotizacion.estado_envio', '=', $estado_envio];
        }
        if($id_empresa > 0){
            $hasWhere[] = ['log_cotizacion.id_empresa', '=', $id_empresa];

        } 
        if($id_cotizacion_alone >0){
            $detalleGrupoCotizacion = DB::table('logistica.log_detalle_grupo_cotizacion')
            ->select('log_detalle_grupo_cotizacion.id_grupo_cotizacion')
            ->where([
                ['log_detalle_grupo_cotizacion.estado', '>', 0],
                ['log_detalle_grupo_cotizacion.estado', '!=', 7], // 7 =anulados
                ['log_detalle_grupo_cotizacion.id_cotizacion', '=', $id_cotizacion_alone]
            ])
            ->get();
            if(count($detalleGrupoCotizacion)>0){
                $idGrupoCotizacion = $detalleGrupoCotizacion->first()->id_grupo_cotizacion;
                $hasWhere[] = ['log_detalle_grupo_cotizacion.id_grupo_cotizacion', '=', $idGrupoCotizacion];
            }

        }
        $log_cotizacion = DB::table('logistica.log_cotizacion')
            ->select(
                'log_cotizacion.id_cotizacion',
                'log_grupo_cotizacion.id_grupo_cotizacion',
                'log_grupo_cotizacion.codigo_grupo',
                'log_cotizacion.codigo_cotizacion',
                'log_cotizacion.fecha_registro',
                'log_cotizacion.estado as id_estado_cotizacion',
                'adm_estado_coti.estado_doc as estado_cotizacion_descripcion',
                'log_cotizacion.estado_envio as id_estado_envio',
                'adm_estado_envio.estado_doc as estado_envio_descripcion',
                'cont_tp_doc.descripcion as tipo_documento',
                'log_cdn_pago.descripcion AS condicion_pago',
                'log_cotizacion.nro_cuenta_principal',
                'log_cotizacion.nro_cuenta_alternativa',
                'log_cotizacion.nro_cuenta_detraccion',
                'log_cotizacion.id_proveedor',
                'log_cotizacion.id_empresa',
                'contri_empresa.razon_social as razon_social_empresa',
                'adm_contri.razon_social as razon_social_proveedor',
                'adm_contri.nro_documento',
                'adm_contri.id_doc_identidad',
                'sis_identi.descripcion as nombre_doc_identidad'
                // 'alm_req.codigo AS codigo_req'
            )
            ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
            // ->Join('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_cotizacion','=','log_cotizacion.id_cotizacion')
            // ->join('almacen.alm_req','alm_req.id_requerimiento','=','log_valorizacion_cotizacion.id_requerimiento')

            ->leftJoin('logistica.log_grupo_cotizacion', 'log_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
            ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->leftJoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'log_cotizacion.id_tp_doc')
            ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_cotizacion.id_condicion_pago')
            
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
            ->leftJoin('contabilidad.adm_contri as contri_empresa', 'contri_empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('administracion.adm_estado_doc as adm_estado_envio', 'adm_estado_envio.id_estado_doc', '=', 'log_cotizacion.estado_envio')
            ->leftJoin('administracion.adm_estado_doc as adm_estado_coti', 'adm_estado_coti.id_estado_doc', '=', 'log_cotizacion.estado')

            ->where($hasWhere)
            ->get();


        if (sizeof($log_cotizacion) > 0) {
            foreach ($log_cotizacion as $data) {
                $id_cotizaciones[] = $data->id_cotizacion;
                $cotizacionArray[] = [

                    'id_cotizacion' => $data->id_cotizacion,
                    'id_grupo_cotizacion' => $data->id_grupo_cotizacion,
                    'codigo_grupo' => $data->codigo_grupo,
                    'codigo_cotizacion' => $data->codigo_cotizacion,
                    'fecha_registro' => $data->fecha_registro,
                    // 'codigo_requerimiento'=> $data->codigo_req,
                    'tipo_documento' => $data->tipo_documento,
                    'condicion_pago' => $data->condicion_pago,
                    'nro_cuenta_principal' => $data->nro_cuenta_principal,
                    'nro_cuenta_alternativa' => $data->nro_cuenta_alternativa,
                    'nro_cuenta_detraccion' => $data->nro_cuenta_detraccion,
                    'id_estado_envio' => $data->id_estado_envio,
                    'estado_envio_descripcion' => $data->estado_envio_descripcion,
                    'id_estado_cotizacion' => $data->id_estado_cotizacion,
                    'estado_cotizacion_descripcion' => $data->estado_cotizacion_descripcion,
                    'cantidad_items'=> 0,
                    'cantidad_items_valorizado'=> 0,
                    'empresa' => [
                        'id_empresa'=>$data->id_empresa,
                        'razon_social'=>$data->razon_social_empresa
                    ],
                    'proveedor' => [
                        "id_proveedor" => $data->id_proveedor,
                        "razon_social" => $data->razon_social_proveedor,
                        "nro_documento" => $data->nro_documento,
                        "id_doc_identidad" => $data->id_doc_identidad,
                        "nombre_doc_identidad" => $data->nombre_doc_identidad
                    ],
                    'requerimientos' => []
                ];
            }


            $log_valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')
                ->select(
                    // 'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
                    'log_valorizacion_cotizacion.id_cotizacion',
                    'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
                    'log_valorizacion_cotizacion.precio_cotizado',
                    'valoriza_coti_detalle.id_detalle_requerimiento',
                    'alm_det_req.descripcion_adicional',
                    // 'valoriza_coti_detalle.id_requerimiento',
                    'alm_req.id_requerimiento',
                    'alm_req.codigo as codigo_requerimiento'
                )
                ->join('logistica.valoriza_coti_detalle', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', 'valoriza_coti_detalle.id_valorizacion_cotizacion')
                // ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'valoriza_coti_detalle.id_requerimiento')
                ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'valoriza_coti_detalle.id_detalle_requerimiento')
                ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
                ->where(
                    [
                        ['log_valorizacion_cotizacion.estado', '!=', 7]
                    ]
                )
                ->whereIn('log_valorizacion_cotizacion.id_cotizacion', $id_cotizaciones)
                ->get();

            $idCotizaciones = [];
            $idRequerimientos = [];
            foreach ($log_valorizacion_cotizacion as $data) {
                // if(in_array($data->id_cotizacion, $idCotizaciones)==false && in_array($data->id_requerimiento, $idRequerimientos)==false){
                // array_push($idCotizaciones,$data->id_cotizacion);
                // array_push($idRequerimientos,$data->id_requerimiento);

                $valorizacionArray[] = [
                    'id_valorizacion_cotizacion'=> $data->id_valorizacion_cotizacion,
                    'descripcion_adicional'=> $data->descripcion_adicional,
                    'id_cotizacion' => $data->id_cotizacion,
                    'precio_cotizado' => floatval($data->precio_cotizado),
                    // 'id_detalle_requerimiento'=> $data->id_detalle_requerimiento,
                    'id_requerimiento' => $data->id_requerimiento,
                    'codigo_requerimiento' => $data->codigo_requerimiento,
                    'merge'=>[]
                ];
                // }

            }

            // si existen item en valorizacion_cotizacion que sea compuesto por varios requerimientos
            $temp_id_val_coti_list=[];
            $temp_val_coti_relation_list=[];
            $newValorizacionArray=[];
            for ($i = 0; $i < sizeof($valorizacionArray); $i++) {
                if(in_array($valorizacionArray[$i]['id_valorizacion_cotizacion'],$temp_id_val_coti_list) ==false){
                    array_push($temp_id_val_coti_list, $valorizacionArray[$i]['id_valorizacion_cotizacion']);
                    $newValorizacionArray[]=$valorizacionArray[$i];
                }else{
                    $temp_val_coti_relation_list[]=[ // valorizacion cotizacion relacionada con otra valorizacion cotizacion
                        'id_valorizacion_cotizacion'=>$valorizacionArray[$i]['id_valorizacion_cotizacion'],
                        'id_requerimiento'=>$valorizacionArray[$i]['id_requerimiento'],
                        'codigo_requerimiento'=>$valorizacionArray[$i]['codigo_requerimiento']
                    ];
                }
            }
            
            for ($i = 0; $i < sizeof($newValorizacionArray); $i++) {
    
                for ($j = 0; $j < sizeof($temp_val_coti_relation_list); $j++) {
                    if($newValorizacionArray[$i]['id_valorizacion_cotizacion'] == $temp_val_coti_relation_list[$j]['id_valorizacion_cotizacion']){
                        $newValorizacionArray[$i]['merge'][]=[
                            'id_valorizacion_cotizacion'=>$temp_val_coti_relation_list[$j]['id_valorizacion_cotizacion'],
                            'id_requerimiento'=>$temp_val_coti_relation_list[$j]['id_requerimiento'],
                            'codigo_requerimiento'=>$temp_val_coti_relation_list[$j]['codigo_requerimiento']
                        ];
                    }
                }
            }
            

            // return $newValorizacionArray;
            // add codigo de requerimiento
            $storageIdRequerimiento = [];
            for ($i = 0; $i < sizeof($cotizacionArray); $i++) {
                for ($j = 0; $j < sizeof($newValorizacionArray); $j++) {
                    if ($cotizacionArray[$i]['id_cotizacion'] == $newValorizacionArray[$j]['id_cotizacion']) {
                        
                        // if (in_array($newValorizacionArray[$j]['id_requerimiento'], $storageIdRequerimiento) == false) {
                            array_push($storageIdRequerimiento, $newValorizacionArray[$j]['id_requerimiento']);
                            $cotizacionArray[$i]['requerimientos'][] = $newValorizacionArray[$j];
                        // }
                    }
                }
                $storageIdRequerimiento = [];
            }
            
            // return $cotizacionArray;
            // cancular cantida de items y cantidad de items valorizados
            
            for ($i = 0; $i < sizeof($cotizacionArray); $i++) {
                $cotizacionArray[$i]['cantidad_items']=count($cotizacionArray[$i]['requerimientos']);
                for ($j = 0; $j < sizeof($cotizacionArray[$i]['requerimientos']); $j++) {
                    if($cotizacionArray[$i]['requerimientos'][$j]['precio_cotizado'] > 0){
                        $cotizacionArray[$i]['cantidad_items_valorizado']+=1;
                    }
                }
            }
            // return $cotizacionArray;
            switch ($valorizacion_completa_incompleta) {
                case 'VALORIZACION_INCOMPLETA':
                    # code...
                    // filtrar solo las cotizaciones con valorizacion incompleta
                        for ($i = 0; $i < sizeof($cotizacionArray); $i++) {
                            if($cotizacionArray[$i]['cantidad_items'] != $cotizacionArray[$i]['cantidad_items_valorizado']){
                                $output[] = $cotizacionArray[$i];
                            }
                        }

                    break;
                
                case 'VALORIZACION_COMPLETA':
                    # code...
                    // filtrar solo las cotizaciones con valorizacion completas
                    for ($i = 0; $i < sizeof($cotizacionArray); $i++) {
                        if($cotizacionArray[$i]['cantidad_items'] == $cotizacionArray[$i]['cantidad_items_valorizado']){
                            $output[] = $cotizacionArray[$i];
                        }
                    }
                
                case 'GRUPO_VALORIZADO_COMPLETO':
                    # code...
                    // filtrar solo las cotizaciones con valorizacion completas
                    $listIdCotizaSinCompletarGrupo=[];
                    $ListaIdCotizacion=[];
                    $idGrupoList=[];
                    $listaCotizacionesEnabledToAction=[];
                    
                    for ($i = 0; $i < sizeof($cotizacionArray); $i++) {
                        if($cotizacionArray[$i]['cantidad_items'] == $cotizacionArray[$i]['cantidad_items_valorizado']){
                            $ListaCotiConValorizacionFilter[] = $cotizacionArray[$i];
                            $ListaIdCotizacion[]=$cotizacionArray[$i]['id_cotizacion'];
                        }
                    }

                    $listReducidaGrupoCoti=[];
                    $tempArrIdGrupo=[];
                    foreach($ListaCotiConValorizacionFilter as $dataValorizacion){

                        $detalleGrupoCoti = DB::table('logistica.log_detalle_grupo_cotizacion')
                        ->select('log_detalle_grupo_cotizacion.*')
                        ->where([
                            ['log_detalle_grupo_cotizacion.estado', '>', 0],
                            ['log_detalle_grupo_cotizacion.estado', '!=', 7], // 7 =anulados
                            ['log_detalle_grupo_cotizacion.id_grupo_cotizacion', '=', $dataValorizacion['id_grupo_cotizacion']]
                        ])
                        ->get();

                        $cantidadDataGrupoCoti=0;
                        $sizeDetalleGrupoCoti=count($detalleGrupoCoti);
                        foreach($detalleGrupoCoti as $dataGrupoCoti){

                            if(in_array($dataGrupoCoti->id_grupo_cotizacion,$tempArrIdGrupo)==false){
                                array_push($tempArrIdGrupo,$dataGrupoCoti->id_grupo_cotizacion);  //temporal array
                                $idGrupoList[]=['id_grupo_cotizacion'=>$dataGrupoCoti->id_grupo_cotizacion]; //lista de solo id_grupo sin repetir
                            }

                            $listReducidaGrupoCoti[]=[  //lista  desordenada de id_grupo e id_cotizacion 
                                'id_grupo_cotizacion'=>$dataGrupoCoti->id_grupo_cotizacion,
                                'id_cotizacion'=>$dataGrupoCoti->id_cotizacion
                            ];
                        }

                    }

                    //agrupar lista reducida
                    $tempIdCoti=[];
                    for($i=0;$i<count($idGrupoList);$i++){
                        for($j=0;$j<count($listReducidaGrupoCoti);$j++){
                            if($idGrupoList[$i]['id_grupo_cotizacion'] == $listReducidaGrupoCoti[$j]['id_grupo_cotizacion']){
                                if(in_array($listReducidaGrupoCoti[$j]['id_cotizacion'], $tempIdCoti)==false){
                                    array_push($tempIdCoti,$listReducidaGrupoCoti[$j]['id_cotizacion']); //temporal array
                                    $idGrupoList[$i]['id_cotizacion'][]=$listReducidaGrupoCoti[$j]['id_cotizacion']; //lista de grupo cotizacion con la relacion de id_cotizacion

                                }
                            }
                        }
                    }
 
                    // // recorrer idGrupoList para verficiar si todas las id_cotizaciones de un grupo esta valorizadas
                    $GrupoEnabledToAction=[];
                    for($i=0;$i<count($idGrupoList);$i++){
                            if(count(array_diff($idGrupoList[$i]['id_cotizacion'], $ListaIdCotizacion)) ==0){
                                // no tiene una id_cotizacion  que no aparece en la lista  o cotizacion sin valorizar
                                $GrupoEnabledToAction[]=$idGrupoList[$i]['id_grupo_cotizacion']; // lista de id_grupo habilitados y listo para generar un cadro comparativo (todas la cotizaciones del grupo se deben mostrar )
                            }   
                            
                    }

                    foreach($ListaCotiConValorizacionFilter as $data){
                        if(in_array($data['id_grupo_cotizacion'],$GrupoEnabledToAction)==true){
                            $listaCotizacionesEnabledToAction[]=$data;
                        }

                    }


                    $output =$listaCotizacionesEnabledToAction;
                    // $ListaCotiConValorizacionFilter =$listIdCotizaSinCompletarGrupo;
              
                    break;
                
                default:
                    # code...
                    break;
            }




        } else {
            // $cotizacionArray = [];
            return response()->json($output);
        }
        return response()->json($output);
    }


    public function get_cuadro_comparativo_by_req($id_requerimiento){
        $det_req = DB::table('almacen.alm_det_req')
        ->select(
            'alm_det_req.id_detalle_requerimiento'
        )
        ->where([
            ['alm_det_req.id_requerimiento', '=', $id_requerimiento]
        ])
        ->get();

        foreach($det_req as $data){
            $id_det_req_list[]=$data->id_detalle_requerimiento;
        }
        
        

        $log_valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')
        ->select(
            'log_cotizacion.codigo_cotizacion',
            'log_cotizacion.id_proveedor',
            'adm_contri.razon_social',
            'sis_identi.descripcion as nombre_doc_identidad',
            'adm_contri.id_doc_identidad',
            'adm_contri.nro_documento',
            'log_valorizacion_cotizacion.id_cotizacion',
            DB::raw('count(*) as total')
        )
        ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_valorizacion_cotizacion.id_cotizacion')
        ->leftJoin('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
        ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
        ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')

        ->where([
            ['log_valorizacion_cotizacion.estado', '>', 1],
            ['log_valorizacion_cotizacion.justificacion', '<>', null]
        ])
        ->whereIn('valoriza_coti_detalle.id_detalle_requerimiento', $id_det_req_list)
        ->groupBy('log_valorizacion_cotizacion.id_cotizacion','log_cotizacion.id_proveedor','adm_contri.razon_social','sis_identi.descripcion','adm_contri.nro_documento','adm_contri.id_doc_identidad','log_cotizacion.codigo_cotizacion')
        ->get();
        
        if( isset($log_valorizacion_cotizacion) && count($log_valorizacion_cotizacion)>0){
        

            foreach ($log_valorizacion_cotizacion as $data) {
                $id_cotizacion_list[]=$data->id_cotizacion; //array de id_cotizacion
                $valorizacion_cotizacion[]=[
                    'codigo_cotizacion'=> $data->codigo_cotizacion,
                    'id_proveedor'=> $data->id_proveedor,
                    'razon_social'=> $data->razon_social,
                    'nombre_doc_identidad'=> $data->nombre_doc_identidad,
                    'nro_documento'=> $data->nro_documento,
                    'id_cotizacion'=> $data->id_cotizacion,
                    'total_buena_pro'=> $data->total
                ];
            }



        $log_detalle_grupo_cotizacion = DB::table('logistica.log_detalle_grupo_cotizacion')
        ->select(
            'log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion',
            'log_detalle_grupo_cotizacion.id_grupo_cotizacion',
            'log_detalle_grupo_cotizacion.estado'
        )
        ->where([
            ['log_detalle_grupo_cotizacion.estado', '>', 0]
        ])
        ->whereIn('log_detalle_grupo_cotizacion.id_cotizacion', $id_cotizacion_list)
        ->get();

        foreach ($log_detalle_grupo_cotizacion as $data) {
            $id_grupo_cotizacion[]=$data->id_grupo_cotizacion; //array de id_grupo_cotizacion
            $detalle_grupo_cotizacion[]=[
                'id_detalle_grupo_cotizacion'=> $data->id_detalle_grupo_cotizacion,
                'id_grupo_cotizacion'=> $data->id_grupo_cotizacion,
                'estado'=> $data->estado
            ];
        }
        $id_grupo_coti_arr = array_unique($id_grupo_cotizacion);

        $log_grupo_cotizacion = DB::table('logistica.log_grupo_cotizacion')
        ->select(
            'log_grupo_cotizacion.*',
            DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_usuario")

        )
        ->leftJoin('configuracion.sis_usua', 'log_grupo_cotizacion.id_usuario', '=', 'sis_usua.id_usuario')
        ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
        ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
        ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
        ->where([
            ['log_grupo_cotizacion.estado', '>', 0]
        ])
        ->whereIn('log_grupo_cotizacion.id_grupo_cotizacion', $id_grupo_coti_arr)
        ->get();

        foreach ($log_grupo_cotizacion as $data) {
            $grupo_cotizacion[]=[
                'id_grupo_cotizacion'=> $data->id_grupo_cotizacion,
                'codigo_grupo'=> $data->codigo_grupo,
                'id_usuario'=> $data->id_usuario,
                'nombre_usuario'=> $data->nombre_usuario,
                'fecha_inicio'=> $data->fecha_inicio,
                'fecha_fin'=> $data->fecha_fin,
                'estado'=> $data->estado
            ];
        }

        $grupo_cotizacion[0]['cotizaciones']=$valorizacion_cotizacion;
        $cc= $grupo_cotizacion;
        }else{
            return $cc =[];
        }

    return $cc;
    }


    public function get_cuadro_comparativos(){
        $log_grupo_cotizacion = DB::table('logistica.log_grupo_cotizacion')
        // ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_grupo_cotizacion.id_grupo_cotizacion')
        // ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_cotizacion')
        ->select(
            'log_grupo_cotizacion.*'
        )
        ->where([
            ['log_grupo_cotizacion.estado', '=', 1]
        ])
        ->orderBy('log_grupo_cotizacion.id_grupo_cotizacion', 'desc')
        ->get();

        $grupo=[];
        foreach ($log_grupo_cotizacion as $data) {
            $grupo[] = [
                'id_grupo_cotizacion' => $data->id_grupo_cotizacion,
                'codigo_grupo' => $data->codigo_grupo,
                'fecha_inicio' => $data->fecha_inicio
            ];
        }



        $log_detalle_grupo_cotizacion = DB::table('logistica.log_detalle_grupo_cotizacion')
            ->select(
                'log_detalle_grupo_cotizacion.*'
            )
            ->where('log_detalle_grupo_cotizacion.estado', '<>', 7)

            ->orderBy('log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion', 'desc')
            ->get();

            $detalle_grupo=[];
            foreach ($log_detalle_grupo_cotizacion as $data) {
                $detalle_grupo[] = [
                    'id_detalle_grupo_cotizacion' => $data->id_detalle_grupo_cotizacion,
                    'id_grupo_cotizacion' => $data->id_grupo_cotizacion,
                    'id_cotizacion' => $data->id_cotizacion
                ];
            }



        $log_cotizacion=[];
        $log_cotizacion = DB::table('logistica.log_cotizacion')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
            ->leftJoin('contabilidad.adm_contri as adm_contri_empresa', 'adm_contri_empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
            ->leftJoin('contabilidad.adm_contri as adm_contri_prove', 'adm_contri_prove.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->select(
                'log_cotizacion.id_cotizacion',
                'log_cotizacion.codigo_cotizacion',
                'log_cotizacion.id_proveedor',
                'log_cotizacion.id_empresa',
                'adm_contri_empresa.razon_social as razon_social_empresa',
                'adm_contri_prove.razon_social as razon_social_proveedor'
            )
            ->where('log_cotizacion.estado', '<>', 7 )

            ->orderBy('log_cotizacion.id_cotizacion', 'desc')
            ->get();

        foreach ($log_cotizacion as $data) {
            $cotizacion[] = [
                'id_cotizacion' => $data->id_cotizacion,
                'codigo_cotizacion' => $data->codigo_cotizacion
            ];
            $empresa[] = [
                'id_cotizacion' => $data->id_cotizacion,
                'id_empresa' => $data->id_empresa,
                'razon_social_empresa' => $data->razon_social_empresa

            ];
            $proveedor[] = [
                'id_cotizacion' => $data->id_cotizacion,
                'id_proveedor' => $data->id_proveedor,
                'razon_social_proveedor' => $data->razon_social_proveedor
            ];
        }

        // for ($i = 0; $i < sizeof($detalle_grupo); $i++) {
        //     $detalle_grupo[$i]['cotizacion'] = [];
        // }
        $log_valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')
        ->select(
            // 'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
            'log_valorizacion_cotizacion.id_cotizacion',
            'log_valorizacion_cotizacion.estado',
            DB::raw('count(*) as total')
        )
        ->where([
            ['log_valorizacion_cotizacion.estado', '<>', 7]
        ])
        // ->orderBy('log_valorizacion_cotizacion.id_valorizacion_cotizacion', 'desc')
        ->groupBy('id_cotizacion','estado')
        ->get();

        $valorizacion_cotizacion=[];
        foreach ($log_valorizacion_cotizacion as $data) {

            $valorizacion_cotizacion[]=[
                'id_cotizacion'=> $data->id_cotizacion,
                'estado'=> $data->estado,
                // 'total_buena_pro'=> $data->total
            ];
        }
        // return $grupo;
        $new_detalle_grupo = $detalle_grupo;

        for ($i = 0; $i < sizeof($grupo); $i++) {
            $grupo[$i]['cantidad_buena_pros'] = 0;
            $grupo[$i]['empresa'] = [];
            $grupo[$i]['proveedor'] = [];

        }


        
        $detalle_grupo_cotizacion = $new_detalle_grupo;
        for ($d = 0; $d < sizeof($detalle_grupo_cotizacion); $d++) {
            $detalle_grupo_cotizacion[$d]['cotizacion']=[];
            $detalle_grupo_cotizacion[$d]['empresa']=[];
            $detalle_grupo_cotizacion[$d]['proveedor']=[];
        }
        

        for ($i = 0; $i < sizeof($detalle_grupo); $i++) {
            for ($j = 0; $j < sizeof($cotizacion); $j++) {
                if ($detalle_grupo[$i]['id_cotizacion'] == $cotizacion[$j]['id_cotizacion']) {
                    $detalle_grupo_cotizacion[$i]['cotizacion'][] = $cotizacion[$j];
                    $detalle_grupo_cotizacion[$i]['empresa'][] = $empresa[$j];
                    $detalle_grupo_cotizacion[$i]['proveedor'][] = $proveedor[$j];
                }
            }
        }

        $grupo_cotizacion = $grupo;
        $empresa_list=[];
        for ($i = 0; $i < sizeof($grupo); $i++) {
            for ($j = 0; $j < sizeof($detalle_grupo_cotizacion); $j++) {
                if ($grupo[$i]['id_grupo_cotizacion'] == $detalle_grupo_cotizacion[$j]['id_grupo_cotizacion']) {
                    if (count($detalle_grupo_cotizacion[$j]['cotizacion']) > 0) {
                        $grupo_cotizacion[$i]['proveedor'][]['razon_social'] = $detalle_grupo_cotizacion[$j]['proveedor'][0]['razon_social_proveedor'];
                            $grupo_cotizacion[$i]['empresa'][]['razon_social'] = $detalle_grupo_cotizacion[$j]['empresa'][0]['razon_social_empresa'];
                            break;
                    }
                }
            }
        }

        $log_valorizacion_cotizacion_buena_pros = DB::table('logistica.log_valorizacion_cotizacion')
        ->select(
            'log_valorizacion_cotizacion.id_cotizacion',
            'log_valorizacion_cotizacion.estado',
            DB::raw('count(*) as cantidad_buena_pros')
        )
        ->whereIn('log_valorizacion_cotizacion.estado', [2,5,6])
        ->groupBy('id_cotizacion','estado')
        ->get();

        $valorizacion_cotizacion_buena_pros=[];
        foreach($log_valorizacion_cotizacion_buena_pros as $data){
            $valorizacion_cotizacion_buena_pros[]=[
                'id_cotizacion' =>$data->id_cotizacion,
                'cantidad_buena_pros' =>$data->cantidad_buena_pros,
                'estado' =>$data->estado
            ];
        }

        $cuadro_comparativo_list= $grupo_cotizacion;
        for($i = 0; $i < sizeof($grupo_cotizacion); $i++) {
            for ($j = 0; $j < sizeof($valorizacion_cotizacion_buena_pros); $j++) {
                $cuadro_comparativo_list[$i]['cantidad_buena_pros']= $valorizacion_cotizacion_buena_pros[$j]['cantidad_buena_pros'];
            }
        }



    return $cuadro_comparativo_list;
    }

    public function mostrar_cuadro_comparativos()
    {
        $grupo_cotizacion = $this->get_cuadro_comparativos();
        return response()->json(["data" => $grupo_cotizacion]);
    }

    public function mostrar_cuadro_comparativo($id)
    {
        $log_grupo_cotizacion = DB::table('logistica.log_grupo_cotizacion')
            ->select(
                'log_grupo_cotizacion.*'
            )
            ->where([
                ['log_grupo_cotizacion.estado', '=', 1],
                ['log_grupo_cotizacion.id_grupo_cotizacion', '=', $id]
            ])
            ->orderBy('log_grupo_cotizacion.id_grupo_cotizacion', 'desc')
            ->first();
        return response()->json($log_grupo_cotizacion);
    }

    // public function itemValorizacion($id_valorizacion_cotizacion)
    // {
    //     $valorizacion = DB::table('logistica.log_valorizacion_cotizacion')
    //         ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_valorizacion_cotizacion.id_detalle_requerimiento')
    //         ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
    //         ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
    //         ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
    //         ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
    //         ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
    //         ->leftJoin('almacen.alm_und_medida as alm_und_medida_prov', 'alm_und_medida_prov.id_unidad_medida', '=', 'log_valorizacion_cotizacion.id_unidad_medida')
    //         ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_valorizacion_cotizacion.id_cotizacion')
    //         ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
    //         ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')

    //         ->select(
    //             'alm_det_req.id_detalle_requerimiento',
    //                         DB::raw("(CASE 
    //             WHEN alm_item.id_item isNUll THEN alm_det_req.descripcion_adicional 
    //             WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
    //             WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
    //             WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 

    //             ELSE 'nulo' END) AS descripcion
    //             "),
    //                         DB::raw("(CASE 
    //             WHEN alm_item.id_item isNUll THEN 'SIN CODIGO' 
    //             WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.codigo 
    //             WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.codigo 
    //             WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.codigo 
    //             ELSE 'nulo' END) AS codigo
    //             "),
    //                         DB::raw("(CASE 
    //             WHEN alm_item.id_item isNUll THEN '-' 
    //             WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.abreviatura
    //             WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'serv' 
    //             WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'und' 
    //             ELSE 'nulo' END) AS unidad_medida
    //             "),
    //             'alm_item.id_item',
    //             'alm_item.id_producto',
    //             'alm_item.id_servicio',
    //             'alm_item.id_equipo',
    //             'alm_det_req.cantidad',
    //             'alm_det_req.precio_referencial',
    //             'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
    //             'log_valorizacion_cotizacion.id_cotizacion',
    //             'log_valorizacion_cotizacion.id_detalle_requerimiento',
    //             'log_valorizacion_cotizacion.id_detalle_oc_cliente',
    //             'log_valorizacion_cotizacion.precio_cotizado',
    //             'log_valorizacion_cotizacion.cantidad_cotizada',
    //             'log_valorizacion_cotizacion.precio_sin_igv',
    //             'alm_und_medida_prov.abreviatura as abrev_unidad_medida_cotizado',
    //             'log_valorizacion_cotizacion.id_unidad_medida as id_unidad_medida_cotizado',
    //             'log_valorizacion_cotizacion.subtotal',
    //             'log_valorizacion_cotizacion.flete',
    //             'log_valorizacion_cotizacion.porcentaje_descuento',
    //             'log_valorizacion_cotizacion.monto_descuento',
    //             'log_valorizacion_cotizacion.subtotal',
    //             'log_valorizacion_cotizacion.estado',
    //             'log_valorizacion_cotizacion.incluye_igv',
    //             'log_valorizacion_cotizacion.garantia',
    //             'log_valorizacion_cotizacion.plazo_entrega',
    //             'log_valorizacion_cotizacion.lugar_despacho',
    //             'log_valorizacion_cotizacion.detalle',
    //             'adm_contri.razon_social'

    //         )
    //         ->where([
    //             ['log_valorizacion_cotizacion.estado', '>', 0],
    //             ['log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', $id_valorizacion_cotizacion]
    //         ])
    //         ->orderBy('log_valorizacion_cotizacion.id_valorizacion_cotizacion', 'asc')
    //         ->first();

    //     return response()->json($valorizacion);
    // }

    
    public function condicion_valorizacion(Request $request){
        $req= $request->all();
        $id_cotizacion = $req['id_cotizacion'];
        $id_condicion = $req['id_condicion'];
        $plazo_dias = $req['plazo_dias'];
        $status='';
        $update = DB::table('logistica.log_cotizacion')->where('id_cotizacion', $id_cotizacion)
        ->update([
            'id_condicion_pago' => $id_condicion,
            'plazo_dias' => $plazo_dias
        ]);
        
        if($update >0){
            $status='ACTUALIZADO';
        }else{
            $status='NO_ACTUALIZADO';
        }

        return  response()->json($status);
    }

    public function update_descripcion_adicional_detalle_orden(Request $request){
        $req= $request->all();
        $id_valorizacion_cotizacion = $req['id_valorizacion_cotizacion'];
        $descripcion_adicional = $req['descripcion_adicional'];
        $status='';
        $update = DB::table('logistica.log_det_ord_compra')->where('id_valorizacion_cotizacion', $id_valorizacion_cotizacion)
        ->update([
            'descripcion_adicional' => $descripcion_adicional
        ]);
        
        if($update >0){
            $status='ACTUALIZADO';
        }else{
            $status='NO_ACTUALIZADO';
        }

        return  response()->json($status);
    }


    public function updateDespacho(Request $request){
        $req= $request->all();
        $id_valorizacion_cotizacion = $req['id_valorizacion_cotizacion'];
        $personal_autorizado = $req['personal_autorizado'];
        $lugar_despacho = $req['lugar_despacho'];
        $status='';
        $update = DB::table('logistica.log_det_ord_compra')->where('id_valorizacion_cotizacion', $id_valorizacion_cotizacion)
        ->update([
            'personal_autorizado' => $personal_autorizado,
            'lugar_despacho' => $lugar_despacho
        ]);
        
        if($update >0){
            $status='ACTUALIZADO';
        }else{
            $status='NO_ACTUALIZADO';
        }

        return  response()->json($status);
    }


    public function listaItemValorizar($id_cotizacion)
    {

        $cotizacion = DB::table('logistica.log_cotizacion')
        ->select('log_cotizacion.*')
        ->where([
            ['log_cotizacion.estado', '>', 0],
            ['log_cotizacion.id_cotizacion', '=', $id_cotizacion]
        ])
        ->get();

 
        $item_cotizacion = DB::table('logistica.log_cotizacion')
            ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
            ->leftJoin('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'valoriza_coti_detalle.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftJoin('almacen.alm_und_medida as alm_und_medida_prov', 'alm_und_medida_prov.id_unidad_medida', '=', 'log_valorizacion_cotizacion.id_unidad_medida')
            ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')

            ->select(
                DB::raw("(CASE 
                WHEN alm_item.id_item isNUll THEN alm_det_req.descripcion_adicional 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 

                ELSE 'nulo' END) AS descripcion
                "),
                            DB::raw("(CASE 
                WHEN alm_item.id_item isNUll THEN 'SIN CODIGO' 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.codigo 
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.codigo 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.codigo 
                ELSE 'nulo' END) AS codigo
                "),
                            DB::raw("(CASE 
                WHEN alm_item.id_item isNUll THEN '-' 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.abreviatura
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'serv' 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'und' 
                ELSE 'nulo' END) AS unidad_medida
                "),
                'alm_item.id_item',
                'alm_item.id_producto',
                'alm_item.id_servicio',
                'alm_item.id_equipo',
                'alm_det_req.cantidad',
                'alm_det_req.id_unidad_medida',
                'alm_det_req.precio_referencial',
                'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
                'log_valorizacion_cotizacion.id_cotizacion',
                'valoriza_coti_detalle.id_detalle_requerimiento',
                'log_valorizacion_cotizacion.id_detalle_oc_cliente',
                'log_valorizacion_cotizacion.precio_cotizado',
                'log_valorizacion_cotizacion.cantidad_cotizada',
                'alm_und_medida_prov.abreviatura as abrev_unidad_medida_cotizado',
                'log_valorizacion_cotizacion.id_unidad_medida as id_unidad_medida_cotizado',
                'log_valorizacion_cotizacion.subtotal',
                'log_valorizacion_cotizacion.flete',
                'log_valorizacion_cotizacion.porcentaje_descuento',
                'log_valorizacion_cotizacion.monto_descuento',
                'log_valorizacion_cotizacion.subtotal',
                'log_valorizacion_cotizacion.estado',
                'log_valorizacion_cotizacion.incluye_igv',
                'log_valorizacion_cotizacion.garantia',
                'log_valorizacion_cotizacion.plazo_entrega',
                'log_valorizacion_cotizacion.lugar_despacho',
                'log_valorizacion_cotizacion.detalle',
                'log_valorizacion_cotizacion.precio_sin_igv',
                'log_valorizacion_cotizacion.igv',
                'adm_contri.razon_social'

            )
            ->where([
                ['log_cotizacion.estado', '!=', 7],
                ['log_cotizacion.id_cotizacion', '=', $id_cotizacion]
            ])
            ->orderBy('log_cotizacion.id_cotizacion', 'asc')
            ->get();

            $idValorizacionCotiList=[];
            $items_valorizacion_cotizacion=[];
            foreach($item_cotizacion as $data){
                $idValorizacionCotiList[]=$data->id_valorizacion_cotizacion;
            }

            $idValorizacionCotiListUniq=array_unique($idValorizacionCotiList);
            $auxArray=[];
            foreach($idValorizacionCotiListUniq as $idValCot){
                foreach($item_cotizacion as $data){
                    if($idValCot == $data->id_valorizacion_cotizacion){
                        if(in_array($data->id_valorizacion_cotizacion,$auxArray) === false ){
                            array_push($auxArray,$data->id_valorizacion_cotizacion);
                            $items_valorizacion_cotizacion[]=[
                                'descripcion'=>$data->descripcion,
                                'codigo'=>$data->codigo,
                                'unidad_medida'=>$data->unidad_medida,
                                'id_item'=>$data->id_item,
                                'id_producto'=>$data->id_producto,
                                'id_servicio'=>$data->id_servicio,
                                'id_equipo'=>$data->id_equipo,
                                'cantidad'=>$data->cantidad,
                                'id_unidad_medida'=>$data->id_unidad_medida,
                                'precio_referencial'=>$data->precio_referencial,
                                'id_valorizacion_cotizacion'=>$data->id_valorizacion_cotizacion,
                                'id_cotizacion'=>$data->id_cotizacion,
                                'id_detalle_requerimiento'=>$data->id_detalle_requerimiento,
                                'id_detalle_oc_cliente'=>$data->id_detalle_oc_cliente,
                                'precio_cotizado'=>$data->precio_cotizado,
                                'cantidad_cotizada'=>$data->cantidad_cotizada,
                                'abrev_unidad_medida_cotizado'=>$data->abrev_unidad_medida_cotizado,
                                'id_unidad_medida_cotizado'=>$data->id_unidad_medida_cotizado,
                                'subtotal'=>$data->subtotal,
                                'flete'=>$data->flete,
                                'porcentaje_descuento'=>$data->porcentaje_descuento,
                                'monto_descuento'=>$data->monto_descuento,
                                'subtotal'=>$data->subtotal,
                                'estado'=>$data->estado,
                                'incluye_igv'=>$data->incluye_igv,
                                'garantia'=>$data->garantia,
                                'plazo_entrega'=>$data->plazo_entrega,
                                'lugar_despacho'=>$data->lugar_despacho,
                                'detalle'=>$data->detalle,
                                'precio_sin_igv'=>$data->precio_sin_igv,
                                'igv'=>$data->igv,
                                'razon_social'=>$data->razon_social
                            ];
                        }

                    }
                   
                
                
                }
            }

            $output=['cotizacion'=>$cotizacion->first(), 'item_cotizacion'=>$items_valorizacion_cotizacion];
        return response()->json($output);
    }
    public function ItemValorizar($id_valorizacion_cotizacion)
    {
        $item_cotizacion = DB::table('logistica.log_cotizacion')
            ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
            ->leftJoin('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'valoriza_coti_detalle.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftJoin('almacen.alm_und_medida as alm_und_medida_prov', 'alm_und_medida_prov.id_unidad_medida', '=', 'log_valorizacion_cotizacion.id_unidad_medida')
            ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')

            ->leftJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')

            ->select(
                'alm_det_req.id_detalle_requerimiento',
                            DB::raw("(CASE 
                WHEN alm_item.id_item isNUll THEN alm_det_req.descripcion_adicional 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 

                ELSE 'nulo' END) AS descripcion
                "),
                            DB::raw("(CASE 
                WHEN alm_item.id_item isNUll THEN 'SIN CODIGO' 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.codigo 
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.codigo 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.codigo 
                ELSE 'nulo' END) AS codigo
                "),
                            DB::raw("(CASE 
                WHEN alm_item.id_item isNUll THEN '-' 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.abreviatura
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'serv' 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'und' 
                ELSE 'nulo' END) AS unidad_medida
                "),
                'alm_item.id_item',
                'alm_item.id_producto',
                'alm_item.id_servicio',
                'alm_item.id_equipo',
                'alm_det_req.cantidad',
                'alm_det_req.precio_referencial',
                'alm_det_req.lugar_entrega as lugar_entrega_requerimiento',
                'alm_det_req.descripcion_adicional as descripcion_adicional_detalle_requerimiento',
                'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
                'log_valorizacion_cotizacion.id_cotizacion',
                'valoriza_coti_detalle.id_detalle_requerimiento',
                'log_valorizacion_cotizacion.id_detalle_oc_cliente',
                'log_valorizacion_cotizacion.precio_cotizado',
                'log_valorizacion_cotizacion.cantidad_cotizada',
                'alm_und_medida_prov.abreviatura as abrev_unidad_medida_cotizado',
                'log_valorizacion_cotizacion.id_unidad_medida as id_unidad_medida_cotizado',
                'log_valorizacion_cotizacion.subtotal',
                'log_valorizacion_cotizacion.flete',
                'log_valorizacion_cotizacion.porcentaje_descuento',
                'log_valorizacion_cotizacion.monto_descuento',
                'log_valorizacion_cotizacion.subtotal',
                'log_valorizacion_cotizacion.estado',
                'log_valorizacion_cotizacion.incluye_igv',
                'log_valorizacion_cotizacion.garantia',
                'log_valorizacion_cotizacion.plazo_entrega',
                'log_valorizacion_cotizacion.lugar_despacho as lugar_despacho_valorizacion',
                'log_valorizacion_cotizacion.detalle',
                'adm_contri.razon_social',
                'log_det_ord_compra.personal_autorizado as personal_autorizado_orden',
                'log_det_ord_compra.lugar_despacho as lugar_despacho_orden',
                'log_det_ord_compra.descripcion_adicional as descripcion_adicional_detalle_orden'
            )
            ->where([
                ['log_cotizacion.estado', '>', 0],
                ['log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', $id_valorizacion_cotizacion]
            ])
            ->orderBy('log_cotizacion.id_cotizacion', 'asc')
            ->first();

        return response()->json($item_cotizacion);
    }

    // public function update_valorizacion(Request $request){
    //     $valorizacion_item = $request->valorizacion_item;
    //     $valorizacion_especificacion = $request->valorizacion_especificacion;
        
    //     $updateValorizacionItem = $this->update_valorizacion_item($valorizacion_item);
    //     $updateValorizacionEspecificacion = $this->update_valorizacion_especificacion($valorizacion_especificacion);


    // }

    public function update_valorizacion(Request $request)
    {
        

        $id_estado_doc = $this->get_estado_doc('Valorizado'); //18 valorizado
        $valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')
        ->select(
            'log_valorizacion_cotizacion.id_requerimiento'
        )
        ->where('id_valorizacion_cotizacion', $request->id_valorizacion_cotizacion)
        ->get();

        $rsta=[];
        $statusOption=['success','fail'];
        $status='';
        $data=[];

        $id_requerimiento =0;
        if($valorizacion_cotizacion){
            $id_requerimiento = $valorizacion_cotizacion->first()->id_requerimiento;
            // array_push($rsta,'id requerimiento ok');
            $status = $statusOption[0];
        }else{
            array_push($rsta,'no existe id de requerimiento vinculado a la valorización');
            $status = $statusOption[1];
        }

        $data =[
            'id_valorizacion_cotizacion'=>$request->id_valorizacion_cotizacion, 
            'id_cotizacion'=>$request->id_cotizacion
        ];

        $valoriza_cotiza = DB::table('logistica.log_valorizacion_cotizacion')->where('id_valorizacion_cotizacion', $request->id_valorizacion_cotizacion)
            ->update([
                'precio_cotizado' => $request->precio_valorizacion,
                'cantidad_cotizada' => $request->cantidad_valorizacion,
                'incluye_igv' => $request->igv,
                'igv' => $request->monto_igv,
                'subtotal' => $request->subtotal_valorizacion,
                'flete' => $request->flete_valorizacion,
                'porcentaje_descuento' => $request->porcentaje_descuento_valorizacion,
                'monto_descuento' => $request->monto_descuento_valorizacion,
                'id_unidad_medida' => $request->unidad_medida_valorizacion,
                'precio_sin_igv' => $request->monto_neto,
                'plazo_entrega' => $request->plazo_entrega,
                'garantia'      => $request->garantia,
                'lugar_despacho' => $request->lugar_entrega,
                'detalle'       => $request->detalle_adicional,
                'estado' => $id_estado_doc
            ]);
        if ($valoriza_cotiza > 0) {
            array_push($rsta,'Se actualizó la valorización');
            $status = $statusOption[0];
        } else {
            array_push($rsta,'error al actualizar estado en la tabla log_valorizacion_cotizacion');
            $status = $statusOption[1];
        }
        
        $res = ['status'=> $status, 'message'=>$rsta, 'data'=>$data];
        return response()->json($res);
    }

    // public function update_valorizacion_especificacion($valorizacion_especificacion)
    // {
    //     $rsta=[];
    //     $statusOption=['success','fail'];
    //     $status='';

    //     $id_val_cot = $valorizacion_especificacion->id_valorizacion_cotizacion;
    //     $id_cot = $valorizacion_especificacion->id_cotizacion;
    //     $update = DB::table('logistica.log_valorizacion_cotizacion')->where('id_valorizacion_cotizacion', $id_val_cot)
    //         ->update([
    //             'plazo_entrega' => $valorizacion_especificacion->plazo_entrega,
    //             'garantia'      => $valorizacion_especificacion->garantia,
    //             'lugar_despacho' => $valorizacion_especificacion->lugar_entrega,
    //             'detalle'       => $valorizacion_especificacion->detalle_adicional
    //         ]);
    //         if ($update > 0) {
    //             array_push($rsta,'Se actualizó detalle especificacion');
    //             $status = $statusOption[0];
    //         } else {
    //             array_push($rsta,'error al actualizar estado en la tabla log_valorizacion_cotizacion');
    //             $status = $statusOption[1];
    //         }
            
    //         $res = ['status'=> $status, 'message'=>$rsta, 'data'=>$data];

    //     return response()->json($val);
    // }



    //  ****************** imprimir todo el cuadro comparartivo ****************************************

    public function get_cuadro_comparativo($id)
    {

        $estado_aprobado = $this->get_estado_doc('Aprobado');
        $estado_valorizado = $this->get_estado_doc('Valorizado');
        $estado_atentido = $this->get_estado_doc('Atendido');
        $estado_en_almacen= $this->get_estado_doc('En Almacen');

        $grupo_cotizacion = DB::table('logistica.log_grupo_cotizacion')
            ->select(
                'log_grupo_cotizacion.*'
            )
            ->where([['log_grupo_cotizacion.id_grupo_cotizacion', '=', $id]])
            ->first();

        $cotizaciones = DB::table('logistica.log_grupo_cotizacion')
            ->select(
                'log_detalle_grupo_cotizacion.id_cotizacion'
            )
            ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_grupo_cotizacion.id_grupo_cotizacion')
            ->where([
                ['log_detalle_grupo_cotizacion.id_grupo_cotizacion', '=', $grupo_cotizacion->id_grupo_cotizacion],
                ['log_detalle_grupo_cotizacion.estado', '!=', 7]
                ])
            ->get();

        $cotizacioneArray = [];
        foreach ($cotizaciones as $data) {
            $cotizacioneArray[] = $data->id_cotizacion;
        }

        $requIds = DB::table('logistica.log_valorizacion_cotizacion')
            ->select(
                'valoriza_coti_detalle.id_requerimiento'
            )
            ->leftJoin('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')

            ->where(
                [
                    ['log_valorizacion_cotizacion.estado', '>', 0],
                    ['log_valorizacion_cotizacion.estado', '!=', 7],
                ]
            )
            ->whereIn('log_valorizacion_cotizacion.id_cotizacion', $cotizacioneArray)
            ->get();

         $reqIdArray = [];
        foreach ($requIds as $data) {
            $reqIdArray[] = $data->id_requerimiento;
        }


        $detalle_requerimiento = DB::table('almacen.alm_req')
            ->select(
                'alm_det_req.id_detalle_requerimiento',
                'alm_req.id_requerimiento',
                'alm_req.codigo as codigo_requerimiento',
                'alm_item.id_item',
                'alm_item.id_producto',
                'alm_item.id_servicio',
                'alm_item.id_equipo',
                DB::raw("(CASE 
                WHEN alm_item.id_item isNUll THEN 'S/C.' 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.codigo 
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.codigo 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.codigo 
                ELSE 'nulo' END) AS codigo
                "),
                DB::raw("(CASE
                
                WHEN alm_item.id_item isNUll THEN alm_det_req.descripcion_adicional 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
                ELSE 'nulo' END) AS descripcion
                "),
                'alm_det_req.cantidad',
                'alm_det_req.fecha_entrega',
                DB::raw("(CASE 
                WHEN alm_item.id_item isNUll THEN alm_und_medida.descripcion 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.descripcion
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'serv' 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'und' 
                ELSE 'nulo' END) AS unidad_medida
                "),
                'alm_det_req.precio_referencial'
            )
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento')
            ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftjoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftjoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')

            ->whereIn('alm_req.id_requerimiento', $reqIdArray)
            ->get();

            
            $log_cotizacion = DB::table('logistica.log_cotizacion')
            ->select(
                'log_cotizacion.id_cotizacion',
                'log_cotizacion.id_empresa',
                'empresa_adm_contri.id_doc_identidad AS empresa_id_doc_identidad',
                'empresa_sis_identi.descripcion AS empresa_nombre_doc_identidad',
                'empresa_adm_contri.nro_documento AS empresa_nro_documento',
                'empresa_adm_contri.razon_social AS empresa_razon_social',
                'empresa_adm_contri.telefono AS empresa_telefono',
                'empresa_adm_contri.celular AS empresa_celular',
                'empresa_adm_contri.direccion_fiscal AS empresa_direccion_fiscal',
                'log_grupo_cotizacion.codigo_grupo',
                'log_grupo_cotizacion.id_usuario',
                'log_grupo_cotizacion.fecha_inicio',
                'log_grupo_cotizacion.fecha_fin',
                DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS full_name"),
                // 'log_cotizacion.codigo_cotizacion',
                'cont_tp_doc.descripcion as tipo_documento',
                'log_cotizacion.id_condicion_pago',
                'log_cdn_pago.descripcion AS condicion_pago',
                'log_cotizacion.plazo_dias',
                'log_cotizacion.nro_cuenta_principal',
                'log_cotizacion.nro_cuenta_alternativa',
                'log_cotizacion.nro_cuenta_detraccion',
                'log_cotizacion.email_proveedor',
                'log_prove.id_proveedor',
                'adm_contri.razon_social',
                'adm_contri.nro_documento',
                'adm_contri.id_doc_identidad',
                'sis_identi.descripcion as nombre_doc_identidad'
                // 'alm_req.codigo AS codigo_req'
            )
            ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
            // ->leftJoin('almacen.alm_req','alm_req.id_requerimiento','=','log_detalle_grupo_cotizacion.id_requerimiento')
            ->leftJoin('logistica.log_grupo_cotizacion', 'log_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
            ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->leftJoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'log_cotizacion.id_tp_doc')
            ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_cotizacion.id_condicion_pago')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
            ->leftJoin('contabilidad.adm_contri as empresa_adm_contri', 'empresa_adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi as empresa_sis_identi', 'empresa_sis_identi.id_doc_identidad', '=', 'empresa_adm_contri.id_doc_identidad')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'log_grupo_cotizacion.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'sis_usua.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->where(
                [
                    ['log_cotizacion.estado', '=', 1],
                    ['log_grupo_cotizacion.id_grupo_cotizacion', '=', $grupo_cotizacion->id_grupo_cotizacion]
                ]
            )
            ->get();

        $empresa_cotizacion = [];
        $proveedor_cotizacion = [];
        $head_cuadro = [];
        foreach ($log_cotizacion as $data) {
            $head_cuadro[] = [
                'codigo_grupo' => $data->codigo_grupo,
                'full_name' => $data->full_name,
                'fecha_inicio' => $data->fecha_inicio,
                'fecha_fin' => $data->fecha_fin,
                'id_empresa' => $data->id_empresa,
                'empresa_nombre_doc_identidad' => $data->empresa_nombre_doc_identidad,
                'empresa_nro_documento' => $data->empresa_nro_documento,
                'empresa_razon_social' => $data->empresa_razon_social
            ];
            $empresa_cotizacion[] = [
                'id_empresa' => $data->id_empresa,
                'empresa_id_doc_identidad' => $data->empresa_id_doc_identidad,
                'empresa_nombre_doc_identidad' => $data->empresa_nombre_doc_identidad,
                'empresa_nro_documento' => $data->empresa_nro_documento,
                'empresa_razon_social' => $data->empresa_razon_social,
                'empresa_telefono' => $data->empresa_telefono,
                'empresa_celular' => $data->empresa_celular,
                'empresa_direccion_fiscal' => $data->empresa_direccion_fiscal
            ];
            $proveedor_cotizacion[] = [
                'id_proveedor' => $data->id_proveedor,
                'tipo_documento' => $data->tipo_documento,
                'id_condicion_pago' => $data->id_condicion_pago,
                'condicion_pago' => $data->condicion_pago,
                'plazo_dias' => $data->plazo_dias,
                'nro_cuenta_principal' => $data->nro_cuenta_principal,
                'nro_cuenta_alternativa' => $data->nro_cuenta_alternativa,
                'nro_cuenta_detraccion' => $data->nro_cuenta_detraccion,
                'email_proveedor' => $data->email_proveedor,
                'razon_social' => $data->razon_social,
                'nro_documento' => $data->nro_documento,
                'id_doc_identidad' => $data->id_doc_identidad,
                'nombre_doc_identidad' => $data->nombre_doc_identidad
            ];
        }
        $det_req = [];
        foreach ($detalle_requerimiento as $data) {
            $det_req[] = [
                'id_requerimiento' => $data->id_requerimiento,
                'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                'id_item' => $data->id_item,
                'codigo_requerimiento' => $data->codigo_requerimiento,
                'codigo' => $data->codigo,
                'descripcion' => $data->descripcion,
                'cantidad' => $data->cantidad,
                'unidad_medida' => $data->unidad_medida,
                'precio_referencial' => $data->precio_referencial,
                'fecha_entrega' => $data->fecha_entrega
            ];
        }
        // merge item with same quantity
        $idItemDetReq = [];
        $detReqWithoutDuplicated = [];
            foreach($det_req as $dataDetReq){
                if(in_array($dataDetReq['id_item'],$idItemDetReq)==false || $dataDetReq['id_item']==0){
                    $idItemDetReq[]=$dataDetReq['id_item'];
                    $detReqWithoutDuplicated[]=$dataDetReq;
                }
                else if(in_array($dataDetReq['id_item'],$idItemDetReq)==true){
                    foreach($detReqWithoutDuplicated as $key => $detReqWD){
                        if($detReqWD['id_item']===$dataDetReq['id_item']){
                            $detReqWithoutDuplicated[$key]['cantidad']+=$dataDetReq['cantidad'];
                            $detReqWithoutDuplicated[$key]['merge']=[
                                'id_requerimiento'=>$dataDetReq['id_requerimiento'],
                                'id_detalle_requerimiento'=>$dataDetReq['id_detalle_requerimiento'],
                                'precio_referencial'=>$dataDetReq['precio_referencial'],
                                'fecha_entrega'=>$dataDetReq['fecha_entrega'],
                                'unidad_medida'=>$dataDetReq['unidad_medida']
                            ];
                        }
                    }
                }   
            }   

        // return $detReqWithoutDuplicated;

        $valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')
            ->select(
                'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
                'log_valorizacion_cotizacion.id_cotizacion',
                'log_cotizacion.id_proveedor',
                'log_cotizacion.id_empresa',
                'empresa_sis_identi.descripcion AS empresa_nombre_doc_identidad',
                'empresa_adm_contri.nro_documento AS empresa_nro_documento',
                'empresa_adm_contri.razon_social AS empresa_razon_social',
                'valoriza_coti_detalle.id_detalle_requerimiento',
                'log_valorizacion_cotizacion.id_detalle_oc_cliente',
                'log_valorizacion_cotizacion.precio_cotizado',
                'log_valorizacion_cotizacion.incluye_igv',
                'log_valorizacion_cotizacion.cantidad_cotizada',
                'log_valorizacion_cotizacion.subtotal',
                'log_valorizacion_cotizacion.flete',
                'log_valorizacion_cotizacion.lugar_despacho',
                'log_valorizacion_cotizacion.plazo_entrega',
                'log_valorizacion_cotizacion.garantia',
                'log_valorizacion_cotizacion.fecha_registro',
                'log_valorizacion_cotizacion.porcentaje_descuento',
                'log_valorizacion_cotizacion.monto_descuento',
                'log_valorizacion_cotizacion.estado AS estado_valorizacion',
                'log_valorizacion_cotizacion.justificacion',
                'alm_item.id_item',
                'alm_prod.estado AS estado_prod',
                DB::raw("(CASE 
            WHEN alm_item.id_item isNUll THEN 'SIN CODIGO' 
            WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.codigo 
            WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.codigo 
            WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.codigo 
            ELSE 'nulo' END) AS codigo
        "),
                DB::raw("(CASE 
            WHEN alm_item.id_item isNUll THEN alm_det_req.descripcion_adicional 
            WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
            WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
            WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
            ELSE 'nulo' END) AS descripcion_item
        "),
                'log_valorizacion_cotizacion.id_unidad_medida',
                DB::raw("(CASE 
            WHEN alm_item.id_item isNUll THEN '-' 
            WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.descripcion
            WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'serv' 
            WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'und' 
            ELSE 'nulo' END) AS unidad_medida_descripcion
        ")
            )
            ->leftJoin('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'valoriza_coti_detalle.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_valorizacion_cotizacion.id_unidad_medida')
            ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_valorizacion_cotizacion.id_cotizacion')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
            ->leftJoin('contabilidad.adm_contri as empresa_adm_contri', 'empresa_adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi as empresa_sis_identi', 'empresa_sis_identi.id_doc_identidad', '=', 'empresa_adm_contri.id_doc_identidad')
            ->where(
                [
                    ['log_valorizacion_cotizacion.estado', '!=', 7],
                ]
            )
            ->whereIn('log_valorizacion_cotizacion.id_cotizacion', $cotizacioneArray)
            ->get();

        $buena_pro = [];
        $valorizacion = [];
        $idValorizacionCotiList=[];
        foreach ($valorizacion_cotizacion as $data) {
            array_push($idValorizacionCotiList,$data->id_valorizacion_cotizacion);
        }
        // $idValorizacionCotiListUniq=array_unique($idValorizacionCotiList);
        
        $auxArray=[];
        $id_det_req_in_valoriza_list=[];
        foreach($idValorizacionCotiList as $idValCoti){
            foreach($valorizacion_cotizacion as $data){
                if(in_array($data->id_valorizacion_cotizacion,$auxArray)==false){
                    array_push($auxArray,$data->id_valorizacion_cotizacion);
                    if ($data->estado_valorizacion == $estado_aprobado 
                    || $data->estado_valorizacion == $estado_atentido 
                    || $data->estado_valorizacion == $estado_en_almacen) { // 2 estado aprobado
                        $buena_pro[] = [
                            'id_valorizacion_cotizacion' => $data->id_valorizacion_cotizacion,
                            'id_cotizacion' => $data->id_cotizacion,
                            'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                            'id_item' => $data->id_item,
                            'codigo_item' => $data->codigo,
                            'descripcion_item' => $data->descripcion_item,
                            'fecha_registro' => $data->fecha_registro,
                            'precio_cotizado' => $data->precio_cotizado?$data->precio_cotizado:0,
                            'cantidad_cotizada' => $data->cantidad_cotizada?$data->cantidad_cotizada:0,
                            'id_unidad_medida' => $data->id_unidad_medida,
                            'unidad_medida_cotizada' => $data->unidad_medida_descripcion,
                            'id_proveedor' => $data->id_proveedor,
                            'id_empresa' => $data->id_empresa,
                            'empresa_razon_social' => $data->empresa_razon_social,
                            'empresa_nombre_doc_identidad' => $data->empresa_nombre_doc_identidad,
                            'empresa_nro_documento' => $data->empresa_nro_documento,
                            'justificacion' => $data->justificacion
                        ];
                    }

                    // agregando a lista  todo los  id_detalle_requerimiento de la valorizacion
                    if(in_array($data->id_detalle_requerimiento,$id_det_req_in_valoriza_list) == false){
                        array_push($id_det_req_in_valoriza_list,$data->id_detalle_requerimiento);
                    }


                    $valorizacion[] = [
                        'id_valorizacion_cotizacion' => $data->id_valorizacion_cotizacion,
                        'id_cotizacion' => $data->id_cotizacion,
                        'id_proveedor' => $data->id_proveedor,
                        'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                        'id_item' => $data->id_item,
                        'id_detalle_oc_cliente' => $data->id_detalle_oc_cliente,
                        'precio_cotizado' => is_numeric($data->precio_cotizado) == 1 ? $data->precio_cotizado : '',
                        'incluye_igv' => $data->incluye_igv?$data->incluye_igv:'',
                        'cantidad_cotizada' => $data->cantidad_cotizada?$data->cantidad_cotizada:0,
                        'id_unidad_medida' => $data->id_unidad_medida,
                        'unidad_medida_cotizada' => $data->unidad_medida_descripcion,
                        'subtotal' => $data->subtotal?$data->subtotal:0,
                        'flete' => $data->flete?$data->flete:0,
                        'lugar_despacho' => $data->lugar_despacho?$data->lugar_despacho:'',
                        'plazo_entrega' => $data->plazo_entrega?$data->plazo_entrega:0,
                        'garantia' => $data->garantia?$data->garantia:'',
                        'porcentaje_descuento' => $data->porcentaje_descuento?$data->porcentaje_descuento:0,
                        'monto_descuento' => $data->monto_descuento?$data->monto_descuento:0,
                        'justificacion' => $data->justificacion?$data->justificacion:'',
                        'estado' => $data->estado_valorizacion,
                        'id_empresa' => $data->id_empresa
                    ];
                }
                }
        }

        // eliminar id_detalle_rquerimiento que no esten en la valorizacion
        $items=[];
        foreach($id_det_req_in_valoriza_list as $id_det_req_valoriza){
            foreach($detReqWithoutDuplicated as $clave => $valor){
                if($id_det_req_valoriza == $detReqWithoutDuplicated[$clave]['id_detalle_requerimiento']){
                    $items[]= $valor;
                }
            }
        }
        // return $det_req;
        //create => new matriz
 
        for ($i = 0; $i < sizeof($valorizacion); $i++) {
            for ($k = 0; $k < sizeof($empresa_cotizacion); $k++) {
                if ($valorizacion[$i]['id_empresa'] === $empresa_cotizacion[$k]['id_empresa']) {
                    $valorizacion[$i]['empresa'] = $empresa_cotizacion[$k];
                }
            }
        }

        // agregar todos los proveedores cada item de detaller requerimiento 
        for ($j = 0; $j < sizeof($proveedor_cotizacion); $j++) {
            for ($i = 0; $i < sizeof($items); $i++) {
                $items[$i]['proveedores'][] = $proveedor_cotizacion[$j];
                $items[$i]['proveedores'][$j]['valorizacion'] = json_decode('{}');
            }
        }

        //agregar valorización
        for ($i = 0; $i < sizeof($items); $i++) {
            for ($j = 0; $j < sizeof($valorizacion); $j++) {
                for ($k = 0; $k < sizeof($items[$i]['proveedores']); $k++) {
                    if ($items[$i]['proveedores'][$k]['id_proveedor'] === $valorizacion[$j]['id_proveedor'] && $items[$i]['id_detalle_requerimiento'] === $valorizacion[$j]['id_detalle_requerimiento']) {
                        $items[$i]['proveedores'][$k]['valorizacion'] = $valorizacion[$j];
                    }
                }
            }
        }
 
        //add => data proveedor a buena_pro
        for ($i = 0; $i < sizeof($buena_pro); $i++) {
            for ($j = 0; $j < sizeof($proveedor_cotizacion); $j++) {
                if ($buena_pro[$i]['id_proveedor'] === $proveedor_cotizacion[$j]['id_proveedor']) {
                    $buena_pro[$i]['razon_social'] = $proveedor_cotizacion[$j]['razon_social'];
                    $buena_pro[$i]['nombre_doc_identidad'] = $proveedor_cotizacion[$j]['nombre_doc_identidad'];
                    $buena_pro[$i]['nro_documento'] = $proveedor_cotizacion[$j]['nro_documento'];
                }
            }
        }

        $result = [
            'head' => $head_cuadro[0],
            'cuadro_comparativo' => $items,
            'proveedores' => $proveedor_cotizacion,
            'buena_pro' => $buena_pro
        ];
        return $result;
    }
    public function ultimas_compras($id_item,$id_detalle_requerimiento)
    {
        $rsta='';
        $data=[];

        if($id_item >0){
            $alm_det_req = new \stdClass();
            $alm_det_req->id_item = $id_item;

 
        }elseif($id_detalle_requerimiento >0){

            $alm_det_req = DB::table('almacen.alm_det_req')
            ->select(
                'alm_det_req.id_item'
                )
            ->where([
                ['alm_det_req.id_detalle_requerimiento', '=', $id_detalle_requerimiento]
            ])
            ->orderBy('alm_det_req.id_item', 'asc')
            ->first();
        }

        if($alm_det_req != null){
            $rsta='ok';
            $doc_com_det = DB::table('almacen.doc_com_det')
            ->select(
                'doc_com_det.id_item',
                'alm_item.id_producto',
                'alm_item.id_servicio',
                'alm_item.id_equipo',
                DB::raw("(CASE 
                WHEN alm_item.id_item isNUll THEN 'NO EXISTE' 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
                ELSE 'nulo' END) AS descripcion
                "),
                'doc_com_det.precio_unitario',
                'doc_com_det.obs',
                'doc_com_det.fecha_registro'
                )
                ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'doc_com_det.id_item')
                ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_item')
                ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
                ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
                ->where([
                    ['doc_com_det.id_item', '=', $alm_det_req->id_item]
                    ])
            ->orderBy('doc_com_det.fecha_registro', 'desc')
            ->limit(3)
            ->get();
            
            if(count($doc_com_det) > 0){
                $rsta='ok';
                
                foreach($doc_com_det as $key => $data){
                    $obs = $data->obs?explode(" ",$data->obs,2):'';
                 
                    $arr[]=[
                        'id'=>$key+1,
                        'id_item'=>$data->id_item,
                        'descripcion'=>$data->descripcion,
                        'precio_unitario'=>$data->precio_unitario,
                        'proveedor'=> $obs?$obs[1]:'-',
                        'documento'=> $obs?$obs[0]:'-',
                        'fecha_registro'=>$data->fecha_registro
                    ];
                }
                $data =$arr;


            }else{
                $rsta='no_existe_item';
            }
        }else{
            $rsta='no_existe_item';
        }
        $result = ['estado'=>$rsta,'data'=>$data];
        return response()->json($result);
    }

    public function mostrar_comparativo($id_cotizacion)
    {
        $cuadro_comparativo = $this->get_cuadro_comparativo($id_cotizacion);
        return response()->json($cuadro_comparativo);
    }

    function decode5t($str)
    {
        for ($i = 0; $i < 5; $i++) {
            $str = base64_decode(strrev($str));
        }
        return $str;
    }


    public function guardar_buenas_pro(Request $request)
    {
        $estado_aprobado = $this->get_estado_doc('Aprobado');

        $buenaProList =  json_decode($request->buenasPro, true);
        $tam = count($buenaProList);
        if ($tam > 0) {
            for ($j = 0; $j < $tam; $j++) {
                $data = DB::table('logistica.log_valorizacion_cotizacion')->where('id_valorizacion_cotizacion', $buenaProList[$j]['id_valorizacion_cotizacion'])
                    ->update([
                        'cantidad_cotizada'   => $buenaProList[$j]['cantidad_valorizacion'],
                        'precio_cotizado'   => $buenaProList[$j]['precio_valorizacion'],
                        'justificacion'   => $buenaProList[$j]['justificacion'],
                        'estado'          => $estado_aprobado
                    ]);
            }
        } else {
            $data = 0;
        }
        return response()->json($data);
    }

    public function eliminar_buena_pro($id_valorizacion)
    {

        $data = DB::table('logistica.log_valorizacion_cotizacion')->where('id_valorizacion_cotizacion', $id_valorizacion)
            ->update([
                'justificacion'   => '',
                'estado'          => 1
            ]);

        return response()->json($data);
    }

    public function exportar_cuadro_comparativo_excel($id_grupo)
    {
        $data = $this->get_cuadro_comparativo($id_grupo);
        $now = new \DateTime();

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
                        font-size: 12px;
                        box-sizing: border-box;
                }
                .tablePDF,
                .tablePDF tr td{
                    border: 1px solid #ddd;
                }
                .tablePDF tr td{
                    padding: 5px;
                }
                th{
                    background:#ecf0f5;
                }
                .subtitle{
                    font-weight: bold;
                }
                .center{
                    text-align:center;
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

            </style>
            </head>
            <body>
            ';



    
        $url_logo_empresa_okc='https://i.ibb.co/y0fjzVH/empresa-4.png';
        $url_logo_empresa_proyectec='https://i.ibb.co/2jdDnCN/empresa-3.png';
        $url_logo_empresa_smart_value='https://i.ibb.co/b3pM5k5/empresa-5.png';

        $url_logo_empresa ='';
        switch ($data['head']['id_empresa']){
            case 1:
                $url_logo_empresa= $url_logo_empresa_okc;
            break;
            case 2:
                $url_logo_empresa= $url_logo_empresa_proyectec;
            break;
            case 3:
                $url_logo_empresa= $url_logo_empresa_smart_value;

            break;
        }


        // $nameFile = "/images/logo_okc.png";
        // $nameFile = $request->file("/logo_okc.png");
        // $nameFile =  './../../public/images logo_okc.png';
        // $nameFile = 'https://www.okcomputer.com.pe/wp-content/uploads/2014/11/LogoSlogan-Peque.png';

        $html .= '
            <img src="' . $url_logo_empresa . '" height="75px" >

                <h1><center>' . $data['head']['codigo_grupo'] . '</center></h1>
                <br><br>
                <table border="0">
            <tr>
                <td class="subtitle">EMPRESA</td>
                <td>' . $data['head']['empresa_razon_social'] . ' - ' . $data['head']['empresa_nombre_doc_identidad'] . ' ' . $data['head']['empresa_nro_documento'] . ' </td>
            </tr>
            </tr>  
                <tr>
                    <td class="subtitle">N° CUADRO COMP.</td>
                    <td width="300">' . $data['head']['codigo_grupo'] . '</td>
                    <td class="subtitle">FECHA INICIO COTIZACIÓN</td>
                    <td>' . $data['head']['fecha_inicio'] . '</td>
                </tr>
                <tr>
                    <td class="subtitle">COTIZADOR</td>
                    <td>' . $data['head']['full_name'] . '</td>
                    <td class="subtitle">FECHA FIN COTIZACIÓN</td>
                    <td>' . $data['head']['fecha_fin'] . '</td>
                </tr>    
                </table>
                <hr>
                <table width="100%" class="tablePDF">
                <tr class="subtitle">
                    <th rowspan="2">Item</th>
                    <th rowspan="2">Descripcion</th>
                    <th rowspan="2">Cantidad</th>
                    <th rowspan="2">Und. Medida</th>
                    <th rowspan="2">Precio Ref.</th>
                ';

        // foreach ($data['cuadro_comparativo'] as $row){
        foreach ($data['cuadro_comparativo'][0]['proveedores'] as $item) {
            $html .= '<th colspan="9" class="center">' . $item['razon_social'] . '<br>' . $item['nombre_doc_identidad'] . ' ' . $item['nro_documento'] . '</th>';
        }
        // }


        $html .= '
                </tr>
                <tr class="subtitle">
                ';


        // foreach ($data['cuadro_comparativo'] as $row){
        foreach ($data['proveedores'] as $item) {
            $html .= '
                    <th width="10%">Unidad</th>
                    <th width="10%">Cantidad</th>
                    <th width="10%">Precio</th>
                    <th width="10%">IGV</th>
                    <th width="10%">% Descuento</th>
                    <th width="10%">Monto Descuento</th>
                    <th width="10%">Sub-total</th>
                    <th width="10%">Plazo Entrega</th>
                    <th width="20%">Despacho</th>
                    ';
        }
        // }

        $html .= '</tr>';

        foreach ($data['cuadro_comparativo'] as $row) {

            $html .= '
                
                    <tr>
                    <td>&nbsp;' . $row['codigo'] . '</td>
                    <td>' . $row['descripcion'] . '</td>
                    <td>' . $row['cantidad'] . '</td>
                    <td>' . $row['unidad_medida'] . '</td>
                    <td>' . $row['precio_referencial'] . '</td>';

            foreach ($row['proveedores'] as $item) {
                if (count((array) $item['valorizacion']) > 0) {
                    $html .=
                        '
                                <td>' . $item['valorizacion']['cantidad_cotizada'] . '</td>
                                <td>' . $item['valorizacion']['unidad_medida_cotizada'] . '</td>
                                <td>' . $item['valorizacion']['precio_cotizado'] . '</td>
                                <td>' . $item['valorizacion']['incluye_igv'] . '</td>
                                <td>' . $item['valorizacion']['porcentaje_descuento'] . '</td>
                                <td>' . $item['valorizacion']['monto_descuento'] . '</td>
                                <td>' . $item['valorizacion']['subtotal'] . '</td>
                                <td>' . $item['valorizacion']['plazo_entrega'] . '</td>
                                <td>' . $item['valorizacion']['lugar_despacho'] . '</td>
                        ';
                } else {
                    $html .=
                        '
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                    ';
                }
            }
            $html .= '  </tr>';
        }

        $html .= '
        </table>
        <br/>

        <table width="100%" class="tablePDF">
        <tr>
            <th  colspan="5" class="right">Tipo Comprobante</th>
        ';
        // foreach ($data['cuadro_comparativo'] as $row){
        foreach ($data['cuadro_comparativo'][0]['proveedores'] as $item) {
            $html .= '<td colspan="9">' . $item['tipo_documento'] . '</td>';
        }
        // }
        $html .=  '
        </tr>
        <tr>
            <th  colspan="5" class="right">Condicion de Compra</th>
            ';
        // foreach ($data['cuadro_comparativo'] as $row){
        foreach ($data['cuadro_comparativo'][0]['proveedores'] as $item) {
            $html .= '<td colspan="9">' . $item['condicion_pago'] . '</td>';
        }
        // }
        $html .=  '
        </tr>
        <tr>
            <th  colspan="5" class="right">Número de Cuenta Banco Principal</th>
        ';
        // foreach ($data['cuadro_comparativo'] as $row){
        foreach ($data['cuadro_comparativo'][0]['proveedores'] as $item) {
            $html .= '<td colspan="9">&nbsp;' . $item['nro_cuenta_principal'] . '</td>';
        }
        // }
        $html .=   '
        </tr>
        <tr>
            <th  colspan="5" class="right">Número de Cuenta Banco Alternativa</th>
        ';
        // foreach ($data['cuadro_comparativo'] as $row){
        foreach ($data['cuadro_comparativo'][0]['proveedores'] as $item) {
            $html .= '<td colspan="9">&nbsp;' . $item['nro_cuenta_alternativa'] . '</td>';
        }
        // }
        $html .= '
        </tr>
        <tr>
            <th  colspan="5" class="right">Número de Cuenta Banco Detracción</th>
        ';
        // foreach ($data['cuadro_comparativo'] as $row){
        foreach ($data['cuadro_comparativo'][0]['proveedores'] as $item) {
            $html .= '<td colspan="9">&nbsp;' . $item['nro_cuenta_detraccion'] . '</td>';
        }
        // }
        $html .= '
        </tr>
        </table>
        
        <br/> 

        <h3 class="subtitle">BUENA PRO</h3>
        <table width="100%" class="tablePDF">';
        foreach ($data['buena_pro'] as $buenaPro) {
            $html .= '<tr><th class="left">Proveedor:</th><td>' . $buenaPro['razon_social'] . ' ' . $buenaPro['nombre_doc_identidad'] . ':' . $buenaPro['nro_documento'] . '</td><th class="left">Item:</th><td>[' . $buenaPro['codigo_item'] . '] ' . $buenaPro['descripcion_item'] . '</td><th class="left"> Cantidad:</th><td>' . $buenaPro['cantidad_cotizada'] . '</td><th class="left"> Precio:</th><td>' . $buenaPro['precio_cotizado'] . '</td></tr>';
            $html .=  '<tr><td colspan="8" rowspan="2">' . $buenaPro['justificacion'] . '</td></tr>';
            $html .=  '<tr></tr>';
        }
        $html .=  '
        </table>
        </body>
        </html>';

        return $html;
    }

    public function solicitud_cuadro_comparativo_excel($id_grupo)
    {
        $data = $this->exportar_cuadro_comparativo_excel($id_grupo);
        return view('logistica/reportes/downloadExcelFormatoCuadroComparativo', compact('data'));
    }


    
    public function get_historial_aprobacion($req){
        
        $doc = $this->consult_doc_aprob($req,1); 
        
        $new_data = array();
        $dataFinal = array();
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();

        $req_elaborado = DB::table('almacen.alm_req')
                        ->where('id_requerimiento', '=', $req)
                        ->get();
        $cant_req_elaborado = $req_elaborado->count();

        if ($cant_req_elaborado > 0) {
            foreach ($req_elaborado as $row) {
                $id_us = $row->id_usuario;
                $fechae = $row->fecha_registro;
                $data1[] = array('estado' => 'ELABORADO', 'usuario' => $id_us, 'fecha' => $fechae, 'obs' => '', 
                'nombre_usuario'=>Usuario::find($row->id_usuario)->trabajador->postulante->persona->nombre_completo,
                'detalle'=>[]);
            }
        }
 
        

        $req_aprobado = DB::table('administracion.adm_aprobacion')
        ->join('administracion.adm_vobo', 'adm_vobo.id_vobo', '=', 'adm_aprobacion.id_vobo')
        ->select('adm_aprobacion.*', 'adm_vobo.descripcion AS vobo')
        ->where('adm_aprobacion.id_doc_aprob', '=', $doc)->get();

        $cant_req_aprob = $req_aprobado->count();

        if ($cant_req_aprob > 0) {
            foreach ($req_aprobado as $key) {
                $id_usua = $key->id_usuario;
                $my_vobo = $key->vobo;
                $fechavb = $key->fecha_vobo;
                $det_obs = $key->detalle_observacion;
                $data2[] = array('estado' => $my_vobo, 'usuario' => $id_usua, 'fecha' => $fechavb,
                'nombre_usuario'=>Usuario::find($key->id_usuario)->trabajador->postulante->persona->nombre_completo,
                'obs' => $det_obs,'detalle'=>[]);
            }
        }

        $req_obs =DB::table('almacen.req_obs')
                    ->select('req_obs.*')
                    ->where([['req_obs.id_requerimiento', '=', $req]])
                    ->orderBy('req_obs.id_observacion', 'asc')
                    ->get();
        $cant_req_obs = $req_obs->count();

        $id_sustentacion_list=[];
        if ($cant_req_obs > 0) {
            foreach ($req_obs as $row) {
                $id_sustentacion_list[] = $row->id_sustentacion;
                $id_us = $row->id_usuario;
                $fechae = $row->fecha_registro;
                $obs = $row->descripcion;
                $id_sustentacion = $row->id_sustentacion;
                $data3[] = array('estado' => 'OBSERVADO', 'usuario' => $id_us, 'fecha' => $fechae, 
                'nombre_usuario'=>Usuario::find($row->id_usuario)->trabajador->postulante->persona->nombre_completo,
                'obs' => $obs,
                'id_sustentacion'=>$id_sustentacion, 'detalle'=>[]
                );
            }
        }
        $req_sust =DB::table('almacen.req_sust')
                    ->select('req_sust.*')
                    ->whereIn('req_sust.id_sustentacion', $id_sustentacion_list)
                    ->orderBy('req_sust.id_sustentacion', 'asc')
                    ->get();
        $cant_req_sust = $req_sust->count();

        if ($cant_req_sust > 0) {
            foreach ($req_sust as $row) {
                $id_sustentacion = $row->id_sustentacion;
                $id_us = $row->id_usuario;
                $fechae = $row->fecha_registro;
                $obs = $row->descripcion;
                $data4[] = array('estado' => 'SUSTENTO', 'usuario' => $id_us, 'fecha' => $fechae, 
                'nombre_usuario'=>Usuario::find($row->id_usuario)->trabajador->postulante->persona->nombre_completo,
                'obs' => $obs, 'id_sustentacion' =>$id_sustentacion);
            }
        }
        $new_data= $data3;

        for ($i=0; $i< $cant_req_obs; $i++){
            for ($j=0; $j< $cant_req_sust; $j++){
                if($new_data[$i]['id_sustentacion'] == $data4[$j]['id_sustentacion']){
                    $new_data[$i]['detalle'][]=$data4[$j];
                }
            }

        }

        $dataFinal = array_merge($data1,$data2,$new_data);
        $date = array();
        foreach ($dataFinal as $row) {
            $date[] = $row['fecha'];
        }
        array_multisort($date, SORT_ASC, $dataFinal);

        return $dataFinal;
    }


    public function get_flujo_aprobacion($id_operacion,$id_area){
        $adm_flujo_aprobacion = DB::table('administracion.adm_flujo')
        ->select(
            'adm_flujo.id_flujo',
            'adm_flujo.id_operacion',
            'adm_flujo.id_rol',
            DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_responsable"),
            'rol_aprobacion.id_area',
            'rrhh_rol_concepto.descripcion as descripcion_rol',
            'adm_flujo.nombre as nombre_fase',
            'adm_flujo.orden',
            'adm_flujo.estado'
            )
        ->leftJoin('administracion.rol_aprobacion', 'rol_aprobacion.id_rol_concepto', '=', 'adm_flujo.id_rol')
        ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rol_aprobacion.id_rol_concepto')
        ->leftJoin('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rol_aprobacion.id_trabajador')
        ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
        ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
        ->where([
            ['adm_flujo.estado', '=', 1],
            ['rol_aprobacion.estado', '=', 1],
            // ['rol_aprobacion.id_area', '=', $id_area],
             ['adm_flujo.id_operacion', '=', $id_operacion]
        ])
        ->orderBy('adm_flujo.orden', 'asc')
        ->get();
        // return $adm_flujo_aprobacion;
        $flujo_aprobacion=[];
        $id_flujo_list=[];

        foreach($adm_flujo_aprobacion as $data){
            
            $id_flujo_list[]= $data->id_flujo;

            $flujo_aprobacion[]=[
                'id_flujo'=>$data->id_flujo,
                'nombre_fase'=>$data->nombre_fase,
                'id_operacion'=>$data->id_operacion,
                'id_rol'=>$data->id_rol,
                'id_area'=>$data->id_area,
                'nombre_responsable'=>$data->nombre_responsable,
                'descripcion_rol'=>$data->descripcion_rol,
                'orden'=>$data->orden,
                'estado'=>$data->estado,
                'criterio_monto'=>[],
                'criterio_prioridad'=>[]
            ];
        }

        $criterios = DB::table('administracion.adm_detalle_grupo_criterios')
        ->select(
            'adm_detalle_grupo_criterios.id_flujo',
            'adm_detalle_grupo_criterios.id_criterio_prioridad',
            'adm_prioridad.descripcion as descripcion_prioridad',
            'adm_criterio_monto.*'
        )
        ->leftJoin('administracion.adm_criterio_monto', 'adm_criterio_monto.id_criterio_monto', '=', 'adm_detalle_grupo_criterios.id_criterio_monto')
        ->leftJoin('administracion.adm_prioridad', 'adm_prioridad.id_prioridad', '=', 'adm_detalle_grupo_criterios.id_criterio_prioridad')
        ->where([
            ['adm_detalle_grupo_criterios.estado', '=', 1]
        ])
        ->whereIn('adm_detalle_grupo_criterios.id_flujo', $id_flujo_list)
        ->orderBy('adm_detalle_grupo_criterios.id_detalle_grupo_criterios', 'asc')
        ->get();

        $criterio_monto=[];
        $criterio_prioridad=[];
        foreach($criterios as $data){
            if ($data->id_criterio_monto >0) {
                $criterio_monto[]=[
                'id_flujo'=>$data->id_flujo,
                'id_criterio_monto'=>$data->id_criterio_monto,
                'descripcion'=>$data->descripcion,
                'id_operador1'=>$data->id_operador1,
                'monto1'=>$data->monto1,
                'id_operador2'=>$data->id_operador2,
                'monto2'=>$data->monto2,
                'estado'=>$data->estado
            ];
            }

            if($data->id_criterio_prioridad >0){
                $criterio_prioridad[]=[
                    'id_flujo'=>$data->id_flujo,
                    'id_criterio_prioridad'=>$data->id_criterio_prioridad,
                    'descripcion'=>$data->descripcion_prioridad
                ];
            }

        }
        if(count($criterio_monto) > 0){
            foreach($flujo_aprobacion as $c1 => $valor1){
                foreach($criterio_monto as $c2 => $valor2){
                    if($valor1['id_flujo'] == $valor2['id_flujo']){
                        $flujo_aprobacion[$c1]['criterio_monto'][]=$valor2;
                    } 
                }
            }
        }

        if(count($criterio_prioridad) > 0){
            foreach($flujo_aprobacion as $c1 => $valor1){
                foreach($criterio_prioridad as $c2 => $valor2){
                    if($valor1['id_flujo'] == $valor2['id_flujo']){
                        $flujo_aprobacion[$c1]['criterio_prioridad'][]=$valor2;
                    } 
                }
            }
        }

        return $flujo_aprobacion;
    }

    public function explorar_requerimiento($id_requerimiento){
        $requerimiento = DB::table('almacen.alm_req')
        ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
        ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
        ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
        ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
        ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
        ->leftJoin('administracion.rol_aprobacion', 'alm_req.id_rol', '=', 'rol_aprobacion.id_rol_aprobacion')
        ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rol_aprobacion.id_rol_concepto')
        ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
        ->leftJoin('proyectos.proy_op_com', 'proy_op_com.id_op_com', '=', 'alm_req.id_op_com')
        ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
        ->leftJoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.id_estado_doc')

        // ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_requerimiento', '=', 'alm_req.id_requerimiento')
        // ->leftJoin('logistica.log_ord_compra', 'log_ord_compra.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
        // ->leftJoin('almacen.guia_com_oc', 'guia_com_oc.id_oc', '=', 'log_ord_compra.id_orden_compra')
        ->select(
            'alm_req.id_requerimiento',
            'alm_req.codigo',
            'alm_req.fecha_requerimiento',
            'alm_req.id_tipo_requerimiento',
            'alm_tp_req.descripcion AS tipo_req_desc',
            'sis_usua.usuario',
            DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_responsable"),

            'alm_req.id_area',
            'adm_area.descripcion AS area_desc',
            'rol_aprobacion.id_rol_aprobacion as id_rol',
            'rol_aprobacion.id_rol_concepto',
            'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
            'alm_req.id_grupo',
            'adm_grupo.descripcion AS adm_grupo_descripcion',
            'alm_req.id_op_com',
            'proy_op_com.codigo as codigo_op_com',
            'proy_op_com.descripcion as descripcion_op_com',
            'alm_req.concepto AS alm_req_concepto',
            'alm_req.fecha_registro',
            'alm_req.id_prioridad',
            'alm_req.id_estado_doc',
            'adm_estado_doc.estado_doc',
            'alm_req.estado'
        )
        ->where([
            ['alm_req.id_requerimiento', '=', $id_requerimiento]
        ])
        ->orderBy('alm_req.id_requerimiento', 'desc')
        ->get();

        // $id_prioridad= $requerimiento->first()->id_prioridad;
        $id_prioridad= 1;
        $tipo_documento= 1;
        $id_grupo= $requerimiento->first()->id_grupo;
        // $id_area= $requerimiento->first()->id_area;

        $num_doc = $this->consult_doc_aprob($id_requerimiento,1); 
        // $id_operacion=$this->get_id_operacion($id_grupo,$id_area,$tipo_documento);
        $areaOfRolAprob = $this->getAreaOfRolAprob($num_doc,1); //{num doc},{tp doc} 

        $id_operacion=$this->get_id_operacion($id_grupo,$areaOfRolAprob['id'],$tipo_documento);

        // get flujo aprobación
        $flujo_aprobacion = $this->get_flujo_aprobacion($id_operacion,$areaOfRolAprob['id']);
        // $flujo_aprobacion = $id_operacion;
        // Lista de historial aprobación
        $historial_aprobacion = $this->get_historial_aprobacion($id_requerimiento);
        // lista de Solicitud de Cotización
        $solicitud_de_cotizaciones = $this->get_cotizacion_by_req($id_requerimiento);
        // Lista de Cuadros Comparativo
        $cuadros_comparativos = $this->get_cuadro_comparativo_by_req($id_requerimiento);
        //lista de ordenes
        $ordenes = $this->get_orden_by_req($id_requerimiento);

        // salida
        $output=[
            'requerimiento'=>$requerimiento,
            'flujo_aprobacion'=>$flujo_aprobacion,
            'historial_aprobacion'=>$historial_aprobacion,
            'solicitud_cotizaciones'=>$solicitud_de_cotizaciones,
            'cuadros_comparativos'=>$cuadros_comparativos,
            'ordenes'=>$ordenes
        ];

        return response()->json($output);
    }

// public function mostrar_roles(){
//     $roles = Auth::user()->trabajador->roles;
//     foreach($roles as $rol){
//         $roles_usuario_list[]=[
//             'descripcion'=> $rol->descripcion,
//             'id_rol'=> $rol->pivot->id_rol,
//             'id_area'=> $rol->pivot->id_area,
//             'id_rol_concepto'=> $rol->pivot->id_rol_concepto,
//             'estado'=> $rol->estado

//         ];
    
//     }
//     return $roles_usuario_list;
// }



    public function getReqByOrden($id_orden){
        $grupo_cotizacion=DB::table('logistica.log_ord_compra')
        ->select('log_ord_compra.*')
        ->where('id_orden_compra',$id_orden)
        ->first();
        
        $cotizaciones=DB::table('logistica.log_detalle_grupo_cotizacion')
        ->select('log_detalle_grupo_cotizacion.id_cotizacion')
        ->where('id_grupo_cotizacion',$grupo_cotizacion->id_grupo_cotizacion)
        ->get();

        $message=[];
        $idCotizacionList=[];
        $idDetalleRequerimientoList=[];
        $idRequerimientoList=[];
        $payLoadReq=[];
        // $payLoadReq=['grupo'=>[],'area'=>[]];

        if($cotizaciones){
            foreach($cotizaciones as $data){
                $idCotizacionList[]=$data->id_cotizacion;
            }

            $detalleRequerimientoList=DB::table('logistica.log_valorizacion_cotizacion')
            ->select('log_valorizacion_cotizacion.*','valoriza_coti_detalle.*')
            ->leftjoin('logistica.valoriza_coti_detalle','valoriza_coti_detalle.id_valorizacion_cotizacion','=','log_valorizacion_cotizacion.id_valorizacion_cotizacion')

            ->whereIn('id_cotizacion',$idCotizacionList)
            ->get();
            if($detalleRequerimientoList){
                foreach($detalleRequerimientoList as $data){
                    $idDetalleRequerimientoList[]=$data->id_detalle_requerimiento;
                }
                $idDetalleRequerimientoList=array_unique($idDetalleRequerimientoList);

                if(sizeof($idDetalleRequerimientoList)>0){
                    $detRequerimientoList=DB::table('almacen.alm_det_req')
                    ->select('alm_det_req.*')
                    ->whereIn('id_detalle_requerimiento',$idDetalleRequerimientoList)
                    ->get();  

                    if($detRequerimientoList){
                        foreach($detRequerimientoList as $data){
                            $idRequerimientoList[]=$data->id_requerimiento;
                        }
                        $idRequerimientoList=array_unique($idRequerimientoList);
                            $RequerimientoList=DB::table('almacen.alm_req')
                            ->select('alm_req.*')
                            ->whereIn('id_requerimiento',$idRequerimientoList)
                            ->get(); 

                            foreach($RequerimientoList as $data){
                                $num_doc = $this->consult_doc_aprob($data->id_requerimiento,1); 

                                $payLoadReq['requerimiento'][]=[
                                    'id_requerimiento' => $data->id_requerimiento,
                                    'num_doc' => $num_doc,
                                    'id_grupo' => $data->id_grupo,
                                    'id_area' => $data->id_area,
                                    'id_tipo_requerimiento' => $data->id_tipo_requerimiento,
                                    'aprobaciones'=>[],
                                    'operaciones'=>[],
                                    'flujos'=>[],
                                ];
                            }

                    }else{
                        array_push($message,'no existe lista requerimiento');

                    }
                }else{
                    array_push($message,'no existe lista detalle requerimiento');

                }
                

            }else{
                array_push($message,'no existe valorizaciones');
            }


        }else{
            array_push($message,'no existe cotizaciones');

        }
        $output=["data"=>$payLoadReq,'message'=>$message];

        return $output;
    }

    public function getOperacionByIdReq($payloadReq){
        $operacion=[];
        foreach($payloadReq as $data){
            $id_requerimiento = $data['id_requerimiento'];
            $id_grupo = $data['id_grupo'];
            $id_area = $data['id_area'];
            $tp_doc = 1;

            $filterBy=[];
            if($id_area == 0 && $id_grupo >0 ){
                $filterBy =[['adm_operacion.id_grupo', '=', $id_grupo]];
            }else if($id_area > 0 && $id_grupo > 0){
                $filterBy =[['id_area', '=', $id_area],['id_grupo', '=', $id_grupo]];
            }

            $sql = DB::table('administracion.adm_operacion')
            ->where([
                $filterBy[0], 
                ['id_tp_documento', '=', $tp_doc], 
                ['estado', '=', 1] 
                ])
            ->get();
            if ($sql->count() > 0) {
                $operacion[]=[
                    'id_requerimiento' =>  $id_requerimiento,
                    'id_grupo' =>  $id_grupo,
                    'id_area' =>  $id_area,
                    'id_operacion' =>  $sql->first()->id_operacion
                ];
            }else{
                $operacion[]=[
                    'id_requerimiento' =>  $id_requerimiento,
                    'id_operacion' =>  0
                ];            }
        }
        return $operacion;
    }

    public function getFlujoByOpe($payloadOperacion){
        $flujo=[];
        foreach($payloadOperacion as $data){
            $id_requerimiento= $data['id_requerimiento'];
            $id_operacion=$data['id_operacion'];
            // $id_area=$data['id_area'];
            // $id_grupo=$data['id_grupo'];

            $sql = DB::table('administracion.adm_flujo')
            ->select(
                'adm_flujo.id_flujo',
                'adm_flujo.id_operacion',
                'adm_flujo.id_rol',
                DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_responsable"),
                'rol_aprobacion.id_area',
                'rrhh_rol_concepto.descripcion as descripcion_rol',
                'adm_flujo.nombre as nombre_fase',
                'adm_flujo.orden',
                'adm_flujo.estado'
                )
            ->leftJoin('administracion.rol_aprobacion', 'rol_aprobacion.id_rol_concepto', '=', 'adm_flujo.id_rol')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rol_aprobacion.id_rol_concepto')
            ->leftJoin('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rol_aprobacion.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->where([
                ['adm_flujo.estado', '=', 1],
                ['rol_aprobacion.estado', '=', 1],
                ['adm_flujo.id_operacion', '=', $id_operacion]
            ])
            ->orderBy('adm_flujo.orden', 'asc')
            ->get();

            if ($sql->count() > 0) {
                foreach($sql as $data){
                    $flujo[]=[
                        'id_requerimiento' =>  $id_requerimiento,
                        'id_flujo' =>  $data->id_flujo,
                        'id_rol' =>  $data->id_rol,
                        'descripcion_rol' =>  $data->descripcion_rol,
                        'nombre_responsable' => $data->nombre_responsable,
                        'orden' =>  $data->orden,
                        'id_operacion' =>   $data->id_operacion
                    ];

                }
            }else{
                $flujo[]=[
                    'id_requerimiento' => 0,
                    'id_flujo' => 0,
                    'id_rol' =>  0,
                    'descripcion_rol'=>'',
                    'nombre_responsable'=>'',
                    'nombre' =>  '',
                    'orden' =>  0,
                    'id_operacion' => 0
                ];  

            }
            
        }
        return $flujo;
    }

    public function getReqOperacionFlujoAprob($id,$tipoId){
        switch ($tipoId) {
            case 'ORDEN':
                $payloadReq=$this->getReqByOrden($id)['data']['requerimiento'];
                // $payloadReq =  [
                    //  id_requerimiento' => $data->id_requerimiento,
                    //  'num_doc' => $num_doc,
                    //  'id_grupo' => $data->id_grupo,
                    //  'id_area' => $data->id_area,
                    //  'id_tipo_requerimiento' => $data->id_tipo_requerimiento,
                    //  'aprobaciones'=>[],
                    //  'operaciones'=>[],
                    //  'flujos'=>[]
                   //  ]                                   '
                $payloadOperacion=$this->getOperacionByIdReq($payloadReq);
               // payloadOperacion={
               //     id_requerimiento:
               //      id_grupo:
               //      id_area:
               //     id_operacion:
               // }
                $payloadFlujo=$this->getFlujoByOpe($payloadOperacion);

               // payloadFlujo = {
                   //     'id_requerimiento' =>  $id_requerimiento,
                   //     'id_flujo' =>  $data->id_flujo,
                   //     'id_rol' =>  $data->id_rol,
                   //     'descripcion_rol' =>  $data->descripcion_rol,
                   //     'nombre_responsable' => $data->nombre_responsable,
                   //     'orden' =>  $data->orden,
                   //     'id_operacion' =>   $data->id_operacion
               // }
                
                for($i=0;$i < count($payloadReq);$i++){
                    for($j=0;$j < count($payloadOperacion);$j++){
                        if($payloadReq[$i]['id_requerimiento']==$payloadOperacion[$j]['id_requerimiento'] ){
                            $payloadReq[$i]['operaciones'][]=$payloadOperacion[$j];
                        }
                    }
                }
                for($i=0;$i < count($payloadReq);$i++){
                    for($j=0;$j < count($payloadFlujo);$j++){
                        if($payloadReq[$i]['id_requerimiento']==$payloadFlujo[$j]['id_requerimiento'] ){
                            $payloadReq[$i]['flujos'][]=$payloadFlujo[$j];
                        }
                    }
                }
                return $payloadReq;

                break;
            
            default:
                # code...
                break;
        }
    }

    public function get_data_req_by_id_orden($id_orden){
        $grupo_cotizacion=DB::table('logistica.log_ord_compra')
        ->select('log_ord_compra.*')
        ->where('id_orden_compra',$id_orden)
        ->first();
        
        $cotizaciones=DB::table('logistica.log_detalle_grupo_cotizacion')
        ->select('log_detalle_grupo_cotizacion.id_cotizacion')
        ->where('id_grupo_cotizacion',$grupo_cotizacion->id_grupo_cotizacion)
        ->get();

        $message=[];
        $idCotizacionList=[];
        $idDetalleRequerimientoList=[];
        $idRequerimientoList=[];
        $payLoadReq=[];
        // $payLoadReq=['grupo'=>[],'area'=>[]];

        if($cotizaciones){
            foreach($cotizaciones as $data){
                $idCotizacionList[]=$data->id_cotizacion;
            }

            $detalleRequerimientoList=DB::table('logistica.log_valorizacion_cotizacion')
            ->select('log_valorizacion_cotizacion.*','valoriza_coti_detalle.*')
            ->leftjoin('logistica.valoriza_coti_detalle','valoriza_coti_detalle.id_valorizacion_cotizacion','=','log_valorizacion_cotizacion.id_valorizacion_cotizacion')

            ->whereIn('id_cotizacion',$idCotizacionList)
            ->get();
            if($detalleRequerimientoList){
                foreach($detalleRequerimientoList as $data){
                    $idDetalleRequerimientoList[]=$data->id_detalle_requerimiento;
                }
                $idDetalleRequerimientoList=array_unique($idDetalleRequerimientoList);

                if(sizeof($idDetalleRequerimientoList)>0){
                    $detRequerimientoList=DB::table('almacen.alm_det_req')
                    ->select('alm_det_req.*')
                    ->whereIn('id_detalle_requerimiento',$idDetalleRequerimientoList)
                    ->get();  

                    if($detRequerimientoList){
                        foreach($detRequerimientoList as $data){
                            $idRequerimientoList[]=$data->id_requerimiento;
                        }
                        $idRequerimientoList=array_unique($idRequerimientoList);
                            $RequerimientoList=DB::table('almacen.alm_req')
                            ->select('alm_req.*')
                            ->whereIn('id_requerimiento',$idRequerimientoList)
                            ->get(); 

                            foreach($RequerimientoList as $data){
                                $payLoadReq['requerimiento'][]= $data->id_requerimiento;
                                $payLoadReq['rol'][]= $data->id_rol;
                                $payLoadReq['grupo'][]= $data->id_grupo;
                                $payLoadReq['area'][]= $data->id_area;
                                $payLoadReq['tipo_requerimiento'][]= $data->id_tipo_requerimiento;
                            }

                    }else{
                        array_push($message,'no existe lista requerimiento');

                    }
                }else{
                    array_push($message,'no existe lista detalle requerimiento');

                }
                

            }else{
                array_push($message,'no existe valorizaciones');
            }


        }else{
            array_push($message,'no existe cotizaciones');

        }


        
        $output=["data"=>$payLoadReq,'message'=>$message];

        return $output;
    }
    public function get_id_tipo_documento($descripcion){
        $adm_tp_docum=DB::table('administracion.adm_tp_docum')
        ->select('adm_tp_docum.*')
        ->where('descripcion','like','%'.$descripcion)
        ->get()->first()->id_tp_documento;

        return $adm_tp_docum;
    }

    public function get_id_rol_concepto($descripcion){
        $id=0;
        $rrhh_rol_concepto=DB::table('rrhh.rrhh_rol_concepto')
        ->select('rrhh_rol_concepto.id_rol_concepto')
        ->where('descripcion','like','%'.$descripcion)
        ->get();
        if($rrhh_rol_concepto->count()){
            $id=$rrhh_rol_concepto->first()->id_rol_concepto;
        }else{
            $id=0;
        }
        return $id;
    }
    public function get_area($descripcion){
        $adm_area=DB::table('administracion.adm_area')
        ->select('adm_area.*')
        ->where('descripcion','like','%'.$descripcion)
        ->get()->first()->id_area;

        return $adm_area;
    }
 
    public function get_ord_list(){
        $estado_anulado = $this->get_estado_doc('Anulado');

        $ord=[];
        $orden = DB::table('logistica.log_ord_compra')
        ->select(
            'log_ord_compra.*',
            'adm_contri.id_contribuyente',
            'adm_contri.razon_social',
            'adm_contri.nro_documento',
            'log_cdn_pago.descripcion as condicion',
            'sis_moneda.simbolo',
            'cta_prin.nro_cuenta as nro_cuenta_prin',
            'cta_alter.nro_cuenta as nro_cuenta_alter',
            'cta_detra.nro_cuenta as nro_cuenta_detra',
            'log_grupo_cotizacion.codigo_grupo',
            'adm_estado_doc.estado_doc',
            'adm_estado_doc.bootstrap_color',
            'log_ord_compra_pago.id_pago',
            'log_ord_compra_pago.detalle_pago',
            'log_ord_compra_pago.archivo_adjunto'
            )
        ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
        ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->leftjoin('logistica.log_cdn_pago','log_cdn_pago.id_condicion_pago','=','log_ord_compra.id_condicion')
        ->leftjoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','log_ord_compra.id_moneda')
        ->leftjoin('contabilidad.adm_cta_contri as cta_prin','cta_prin.id_cuenta_contribuyente','=','log_ord_compra.id_cta_principal')
        ->leftjoin('contabilidad.adm_cta_contri as cta_alter','cta_alter.id_cuenta_contribuyente','=','log_ord_compra.id_cta_alternativa')
        ->leftjoin('contabilidad.adm_cta_contri as cta_detra','cta_detra.id_cuenta_contribuyente','=','log_ord_compra.id_cta_detraccion')
        ->join('logistica.log_grupo_cotizacion','log_grupo_cotizacion.id_grupo_cotizacion','=','log_ord_compra.id_grupo_cotizacion')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','log_ord_compra.estado')
        ->leftjoin('logistica.log_ord_compra_pago','log_ord_compra_pago.id_orden_compra','=','log_ord_compra.id_orden_compra')
        ->where('log_ord_compra.estado','!=',$estado_anulado)
        ->get();
        
  
        foreach($orden as $data){
            $ord[]=[
                'id_orden_compra' => $data->id_orden_compra,
                'id_grupo_cotizacion' => $data->id_grupo_cotizacion,
                'id_tp_documento' => $data->id_tp_documento,
                'fecha' => $data->fecha,
                'id_usuario' => $data->id_usuario,
                'id_moneda' => $data->id_moneda,
                'igv_porcentaje' => $data->igv_porcentaje,
                'monto_subtotal' => $data->monto_subtotal,
                'monto_igv' => $data->monto_igv,
                'monto_total' => $data->monto_total,
                'estado' => $data->estado,
                'id_proveedor' => $data->id_proveedor,
                'codigo' => $data->codigo,
                 'plazo_dias' => $data->plazo_dias,
                'id_cta_principal' => $data->id_cta_principal,
                'id_cta_alternativa' => $data->id_cta_alternativa,
                'id_cta_detraccion' => $data->id_cta_detraccion,
                'personal_responsable' => $data->personal_responsable,
                'plazo_entrega' => $data->plazo_entrega,
                'en_almacen' => $data->en_almacen,
                'id_occ' => $data->id_occ,
                'id_contribuyente' => $data->id_contribuyente,
                'razon_social' => $data->razon_social,
                'nro_documento' => $data->nro_documento,
                'id_condicion' => $data->id_condicion,
                'condicion' => $data->condicion,
                'simbolo' => $data->simbolo,
                'nro_cuenta_prin' => $data->nro_cuenta_prin,
                'nro_cuenta_alter' => $data->nro_cuenta_alter,
                'nro_cuenta_detra' => $data->nro_cuenta_detra,
                'codigo_grupo' => $data->codigo_grupo,
                'estado_doc' => $data->estado_doc,
                'bootstrap_color' => $data->bootstrap_color,
                'id_pago' => $data->id_pago,
                'detalle_pago' => $data->detalle_pago,
                'archivo_adjunto' => $data->archivo_adjunto
            ];
        }
    


        return $ord;
    }
    public function groupIncluded($id_orden){
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

        $output=[];
        $idGrupoList=[];
        foreach($sql as $data){
            if(in_array($data->id_grupo,$idGrupoList) == false){
                array_push($idGrupoList,$data->id_grupo);
                $output[]=[
                    'id_orden'=>$data->id_grupo,
                    'estado_orden'=>$data->estado_orden,
                    'codigo_orden'=>$data->codigo_orden,
                    'id_requerimiento'=>$data->id_requerimiento,
                    'codigo_requerimiento'=>$data->codigo_requerimiento,
                    'id_grupo'=>$data->id_grupo,
                    'nombre_grupo'=>$data->descripcion_grupo,
                    'id_area'=>$data->id_area,
                    'nombre_area'=>$data->descripcion_area
                ];
            }

        }
        return $output;
    }

    public function groupApproved($id_orden){
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
 
        return $sql;
    }

    public function listar_todas_ordenes(){
        $userSession=$this->userSession()['roles'];
        $userSessionRolConceptoList=[];
        $userSessionRolAreaList=[];
        foreach($userSession as $us){

            $userSessionRolConceptoList[]=$us->id_rol_concepto; //id_rol_concepto actuales
            $userSessionRolAreaList[]=$us->id_area; //id area de usuario actual
        }
        // $userSessionRolConceptoList=array_unique($userSessionRolConceptoList);
        $userSessionRolAreaList=array_unique($userSessionRolAreaList);
        
        
        // $rolConceptoHabilitado[] = $this->get_id_rol_concepto('ASISTENTE ADMINISTRATIVO');
        // $rolConceptoHabilitado[] = $this->get_id_rol_concepto('JEFE DE FINANZAS Y TESORERÍA');
        // $rolConceptoHabilitado[] = $this->get_id_rol_concepto('ASISTENTE CONTABLE');

        
        $usuarioPuedeRegistraPago = false;
        // $countRG=0;
        // foreach($userSessionRolConceptoList as $idSessionRolConcept){
        //     if(array_search($idSessionRolConcept,$rolConceptoHabilitado)){ //si id_rol_cocepto de la session esta en los roles permitidos (asistente administrativo, jefe de fianznas tesoreria)
        //         $countRG +=1; // cantidad de coicidencias
        //     }
        // }
        // if($countRG >0){
        //     $usuarioPuedeRegistraPago = true; // esta habilidado para registrar pago
        // }

        $AreaHabilitada = $this->get_area('CONTABILIDAD');
        if(in_array($AreaHabilitada,$userSessionRolAreaList)){
            $usuarioPuedeRegistraPago= true;
        }else{
            $usuarioPuedeRegistraPago= false;
        }

        $id_tipo_doc_oc = $this->get_id_tipo_documento('Orden de Compra');
        $id_tipo_doc_os= $this->get_id_tipo_documento('Orden de Servicio');

         
        $ap_apr = "'aprobar'";
        $ap_obs = "'observar'";
        $ap_dng = "'denegar'";
        
  
        
        $output=[];
       

        $grupoReq=[];
        $id_operacion=0;
   
         $orderProcessed=[]; 

 

        foreach($this->get_ord_list() as $row){    
                $id_orden_compra =  $row['id_orden_compra'];
                $id_grupo_cotizacion =  $row['id_grupo_cotizacion'];
                $id_tp_documento =  $row['id_tp_documento'];
                $fecha =  $row['fecha'];
                $id_usuario =  $row['id_usuario'];
                $id_moneda =  $row['id_moneda'];
                $igv_porcentaje =  $row['igv_porcentaje'];
                $monto_subtotal =  $row['monto_subtotal'];
                $monto_igv =  $row['monto_igv'];
                $monto_total =  $row['monto_total'];
                $estado =  $row['estado'];
                $id_proveedor =  $row['id_proveedor'];
                $codigo =  $row['codigo'];
                $id_condicion =  $row['id_condicion'];
                $plazo_dias =  $row['plazo_dias'];
                $id_cta_principal =  $row['id_cta_principal'];
                $id_cta_alternativa =  $row['id_cta_alternativa'];
                $id_cta_detraccion =  $row['id_cta_detraccion'];
                $personal_responsable =  $row['personal_responsable'];
                $plazo_entrega =  $row['plazo_entrega'];
                $en_almacen =  $row['en_almacen'];
                $id_contribuyente =  $row['id_contribuyente'];
                $razon_social =  $row['razon_social'];
                $nro_documento =  $row['nro_documento'];
                $condicion =  $row['condicion'];
                $simbolo =  $row['simbolo'];
                $nro_cuenta_prin =  $row['nro_cuenta_prin'];
                $nro_cuenta_alter =  $row['nro_cuenta_alter'];
                $nro_cuenta_detra =  $row['nro_cuenta_detra'];
                $codigo_grupo =  $row['codigo_grupo'];
                $estado_doc =  $row['estado_doc'];
                $bootstrap_color =  $row['bootstrap_color'];
                $id_pago =  $row['id_pago'];
                $detalle_pago =  $row['detalle_pago'];
                $archivo_adjunto =  $row['archivo_adjunto'];

                $containerOpenBrackets='<center><div class="btn-group" role="group" style="margin-bottom: 5px; width:100px">';
                $containerCloseBrackets='</div></center>';
        
                $btnAprobarOrden= '<button type="button" title="Aprobar Orden" class="aprobar_orden btn btn-md btn-success boton" data-toggle="tooltip" data-placement="bottom" data-id-orden-compra="'.$id_orden_compra.'"  data-id-pago="'.$id_pago.'"> <i class="fas fa-check fa-xs"></i> </button>';
                $btnAprobarOrdenDisabled= '<button type="button" title="Aprobar Orden"  class="aprobar_orden btn btn-md btn-success boton" data-toggle="tooltip" data-placement="bottom" disabled > <i class="fas fa-check fa-xs"></i> </button>';
                $btnRegistrarPago= '<button type="button" class="pagar btn btn-md btn-info boton" data-toggle="tooltip" data-placement="bottom" title="Registrar Pago" data-id-orden-compra="'.$id_orden_compra.'"  data-id-pago="'.$id_pago.'" > <i class="fas fa-money-bill-wave fa-xs" ></i> </button>';
                $btnRegistrarPagoDisabled= '<button type="button" class="pagar btn btn-md btn-info boton" data-toggle="tooltip" data-placement="bottom" title="Registrar Pago" disabled > <i class="fas fa-money-bill-wave fa-xs"></i> </button>';
                $btnEliminarPago= '<button type="button" class="eliminar btn btn-md btn-danger boton" data-toggle="tooltip" data-placement="bottom" title="Quitar Pago"  data-id-orden-compra="'.$id_orden_compra.'"  data-id-pago="'.$id_pago.'"><i class="fas fa-trash fa-xs"></i> </button> ';
                $btnEliminarPagoDisabled= '<button type="button" class="eliminar btn btn-md btn-danger boton" data-toggle="tooltip" data-placement="bottom" title="Quitar Pago" disabled ><i class="fas fa-trash fa-xs"></i> </button>';
                $btnImprimirOrden= '<button type="button" title="Imprimir Orden" class="imprimir_orden btn btn-md btn-warning boton" data-toggle="tooltip" data-placement="bottom" data-id-orden-compra="'.$id_orden_compra.'"  data-id-pago="'.$id_pago.'"> <i class="fas fa-file-pdf"></i> </button>';
                $btnExplorarOrden= '<button type="button" title="Explorar Orden" class="tracking_orden btn btn-md btn-primary boton" data-toggle="tooltip" data-placement="bottom" data-id-orden-compra="'.$id_orden_compra.'"> <i class="fas fa-globe fa-xs"></i> </button>';
        
                $estElaborado= '<center><label class="label label-default">Elaborado</label></center>';
                $estPendienteAprob= '<center><label class="label label-default">Pendiente Aprobación</label></center>';
                $estAprobado= '<center><label class="label label-primary">Aprobado</label></center>';
                $estAtendido= '<center><label class="label label-success">Atentido</label></center>';
                $estProcesado= '<center><label class="label label-success">Procesado</label></center>';
                $estSustentado= '<center><label class="label label-info">Sustentado</label></center>';
                $estObservado= '<center><label class="label label-warning">Observado</label></center>';
                $estDenegado= '<center><label class="label label-danger">Denegado</label></center>';
                $estAnulado= '<center><label class="label label-black">Anulado</label></center>';
                $method='';



                $dataReq = $this->get_data_req_by_id_orden($id_orden_compra);
                // return $dataReq['data'];
                $id_est =  $estado;
                $grupoReq = $dataReq['data']?array_unique($dataReq['data']['grupo']):0; //  lista de id_grupo de todo los requerimientos de la orden
                $idReqList = $dataReq['data']?array_unique($dataReq['data']['requerimiento']):0; // lista de id_requerimiento de todos los req. de la orden


                $payLoadReq = $this->getReqOperacionFlujoAprob($id_orden_compra,'ORDEN'); // {ORDEN},{REQUERIMIENTO}
 
                    
     
                // $requerimiento = [ 
                //     id_req => ,
                //     num_doc =>, 
                //     id_grupo =>,
                //     id_area => , 
                //     tp_doc =>
                //     aprobaciones[
                //         'id_rol_concepto'=>
                //         'id_area'=>
                //         'id_grupo'=>
                //         'orden_aprob'=>
                //         'nombre_rol'=>
                //         'nombre_usuario'=>
                //         'has_aprob'=>
                //         'fecha_aprob'=>
                //     ],
                //     operacion[
                //         'id_operacion'=>
                //     ],
                //     flujo=>[
                //         'id_flujo'=>,
                //         'id_orden'=>,
                //         'hasAprobacion'=>,
                //         'id_rol'=>
                //     ]
                // ]


                // recorrer array_id_operacion
                    // list_flujos[] = get_lujo{id_operacion}
                
                //$countGroupApproved =$this->consult_aprob($num_doc); // cantidad aprobaciones
                // if $countGroupApproved == 0 //si no tiene aprobaciones:
                    // get_primera_aprob(recorrer list_flujos where orden ==1) => id_flujo_last_aprob
                    // $next_apro = $this->next_aprob(1,$id_operacion);
                    // $next_apro['rolaprbo]

                // si tiene aprobaciones:
                // $last_aprob = $this->last_aprob($num_doc);
                // $id_flujo_last_aprob = $last_aprob['id_flujo'];
                // $nro_orden_last_aprob = $this->get_nro_orden_by_flujo($id_flujo_last_aprob,$id_operacion);
                // $next_apro = $this->next_aprob($nro_orden_last_aprob+1,$id_operacion);
                // $next_apro['rolaprbo]
                $id_operacion_list=[];

                 if(sizeof($grupoReq)>0){ // si el tamaño de la lista de grupos es mayor a 0
                    $num_doc_req_list=[];
                    $AreaOfRolAprobList=[];
                        foreach($grupoReq as $id_g ){ // recorrrer cada id_grupo
                            if(in_array($id_orden_compra,$orderProcessed) == false){
                                array_push($orderProcessed,$id_orden_compra); // al ser varios grupos , varios requerimientos pero con una misma orden, evitar que se procese la orden nuevamente.
                                
                                $num_doc = $this->consult_doc_aprob($id_orden_compra,2); //obtener el numero de doc (id_doc_aprob)
                                // id_req_list[]
                                foreach($idReqList as $data){
                                    $num_doc_req_list[] = $this->consult_doc_aprob($data,1); //obtener el numero de doc (id_doc_aprob)
                                }
                                foreach($num_doc_req_list as $data){
                                    $AreaOfRolAprobList[] = $this->getAreaOfRolAprob($data,1)['id']; //{num doc},{tp doc} 
                                }
                                // foreach($AreaOfRolAprobList as $area){ //recorrer areaRolAprob
                                // } //end recorrer areaRolAprob
                                // $id_operacion =$this->get_id_operacion($id_g,0,$id_tipo_doc_oc);
                                // $id_operacion_list=$this->getIdOperacionByIdGrupoList($grupoReq,2,1);
                                // $consult_nivel_aprob = $this->consult_nivel_aprob($userSessionRolConceptoList,$id_operacion);
                                // $na_flujo = $consult_nivel_aprob['flujo'];
                                // $size_flujo = $this->size_flujo($id_operacion);
                                // $last_aprob = $this->last_aprob($num_doc);
                                // $id_flujo_last_aprob = $last_aprob['id_flujo'];
                                // $nro_orden_last_aprob = $this->get_nro_orden_by_flujo($id_flujo_last_aprob,$id_operacion);
                                // $next_apro = $this->next_aprob($nro_orden_last_aprob+1,$id_operacion);
                                //---------------------------------------------------- 
                                // cuando la orden tiene varias gerencias
                                $grupoIncluded='';
                                $countGroupIncluded=0;
                                $groupIncluded = $this->groupIncluded($id_orden_compra);

                                    foreach($groupIncluded as $gi){
                                        $grupoIncluded.='<center><label class="label label-default" title="'.$gi['nombre_grupo'].'">A</label></center>';
                                    }
                                    $countGroupIncluded = count($groupIncluded);
                                    if($countGroupIncluded >1){ // si la orden tiene mas de un grupo ( varioas gerencias)
                                        
                                            $method .= $btnImprimirOrden;
                                            // $usuarioPuedeRegistraPago=true;
                                        

                                    }
                                //---------------------------------------------------- 
                                                //  cuando la orden tiene una unica gerencia
                                        if($countGroupIncluded ==1){ 

                                                $method .= $btnImprimirOrden;
                                                // $usuarioPuedeRegistraPago=true;

                                            
                                        }
                                        
                                        if($usuarioPuedeRegistraPago ==true ){
                                            $method .= $btnRegistrarPago.$btnEliminarPago;
                                        }



                                $groupMethod = $containerOpenBrackets. $method. $containerCloseBrackets;

                                if($id_condicion == 2){
                                    $condic= $condicion .' '.$plazo_dias .' DÍAS';
                                }else{
                                    $condic= $condicion;
                                }

                                if ($archivo_adjunto !== null){
                                    $archivoAdju='<a href="/files/logistica/pagos/'.$archivo_adjunto.'" target="_blank">'.$archivo_adjunto.'</a>';
                                }else{
                                    $archivoAdju='';
                                }
                                $date = date_create($fecha);

                                    //si tiene mas de una gerencia mostrar info
                                    $grupoIncluded='';
                                    $infoIncluded='';
                                    $groupIncluded = $this->groupIncluded($id_orden_compra);
                                    $groupIncludeJson= htmlspecialchars(json_encode($groupIncluded));
                                    $grupoIncluded.='<center>';
                                    foreach($groupIncluded as $gi){
                                        $grupoIncluded.='<label class="label label-default" title="'.$gi['nombre_grupo'].'" >'.$gi['nombre_grupo'][0].'</label>';
                                    }
                                    $grupoIncluded.='</center>';

                                    $infoIncluded='<center><label class="label label-warning" title="info" style="cursor:pointer;" onClick="viewGroupInfo(event)" data-group-info="'.$groupIncludeJson.'" >i</label></center>';
                                // 

                                $output['data'][] = array(
                                    $id_orden_compra, 
                                    date_format($date,'Y-m-d'), 
                                    '<label class="lbl-codigo" title="Abrir Orden" onClick="abrir_orden('.$id_orden_compra.')">'.$codigo.'</label>',
                                    $nro_documento, 
                                    $razon_social, 
                                    $simbolo, 
                                    $monto_subtotal, 
                                    $monto_igv, 
                                    $monto_total, 
                                    $condic, 
                                    $plazo_entrega, 
                                    $nro_cuenta_prin, 
                                    $nro_cuenta_alter, 
                                    $nro_cuenta_detra, 
                                    '<label class="lbl-codigo" title="Abrir Cuadro" onClick="abrir_cuadro('.$id_grupo_cotizacion.')">'.$codigo_grupo.'</label>',
                                    $infoIncluded.'<span class="label label-'.$bootstrap_color.'">'.$estado_doc.'</span>'.$grupoIncluded, 
                                    $detalle_pago, 
                                    $archivoAdju,
                                    $groupMethod
                                );                            
                                
                            } // end in_array id_orden_compra,$orderProcessed == false

                        } // end recorrrer cada id_grupo
                    }
                }
                // return $output;
                return response()->json($output);
    }

    public function guardar_pago_orden(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $id_usuario = Auth::user()->id_usuario;
        $id_pago = DB::table('logistica.log_ord_compra_pago')->insertGetId(
            [
                'id_orden_compra' => $request->id_orden_compra,
                'detalle_pago' => $request->detalle_pago,
                'estado' => 1,
                'registrado_por' => $id_usuario,
                'fecha_registro' => $fecha
            ],
                'id_pago'
            );
        //obtenemos el campo file definido en el formulario
        $file = $request->file('archivo_adjunto');
        if (isset($file)){
            //obtenemos el nombre del archivo
            $nombre = $id_pago.'.'.$request->codigo_orden.'.'.$file->getClientOriginalName();
            //indicamos que queremos guardar un nuevo archivo en el disco local
            \File::delete(public_path('logistica/pagos/'.$nombre));
            \Storage::disk('archivos')->put('logistica/pagos/'.$nombre,\File::get($file));
            
            $update = DB::table('logistica.log_ord_compra_pago')
                ->where('id_pago', $id_pago)
                ->update(['archivo_adjunto' => $nombre]); 
        } else {
            $nombre = null;
        }
        return response()->json($id_pago);
    }

    function eliminar_pago($id_pago){
        $data = DB::table('logistica.log_ord_compra_pago')
        ->where('id_pago',$id_pago)
        ->delete();
        return response()->json($data);
    }

    public function listar_occ(){
        $data = DB::connection('mgcp')->table('ordenes_compra')
        ->select('ordenes_compra.*','entidades.ruc','entidades.entidad')
        ->leftjoin('entidades','entidades.id','=','ordenes_compra.id_entidad')
        ->where('ordenes_compra.estado_am','ACEPTADA C/ENTREGA PENDIENTE')
        ->get();

        $output['data'] = $data;
        return response()->json($output);
    }

    public function listar_occ_pendientes(){
        $pendientes = DB::table('logistica.log_ord_compra')
        ->select('log_ord_compra.id_occ')
        ->where([['id_occ','!=',null]])
        ->get();
        
        $ids_occ = [];
        foreach($pendientes as $p){
            array_push($ids_occ, $p->id_occ);
        }

        $data = DB::connection('mgcp')->table('ordenes_compra')
        ->select('ordenes_compra.*','entidades.ruc','entidades.entidad')
        ->leftjoin('entidades','entidades.id','=','ordenes_compra.id_entidad')
        ->whereIn('ordenes_compra.id',$ids_occ)
        ->get();
        
        $output['data'] = $data;
        return response()->json($output);
    }

    public function copiar_items_occ($id){
        $detalle = DB::connection('mgcp')->table('ordenes_compra')
        ->select('orden_publica_detalles.*','ordenes_compra.id','productos_am.descripcion',
        'ordenes_compra.fecha_entrega','ordenes_compra.lugar_entrega')
        ->leftjoin('orden_publica_detalles','orden_publica_detalles.id_oc','=','ordenes_compra.orden_compra')
        ->leftjoin('productos_am','productos_am.id','=','orden_publica_detalles.id_producto')
        ->where('ordenes_compra.id',$id)
        ->get();
        $html = '';
        $i = 0;
        $data_item = [];

        foreach($detalle as $det){
            $lugar = str_replace('   ', '', $det->lugar_entrega);
            $html.='
            <tr>
                <td>0</td>
                <td>SIN CODIGO</td>
                <td>'.$det->descripcion.'</td>
                <td></td>
                <td>'.$det->cantidad.'</td>
                <td>'.$det->importe.'</td>
                <td>'.$det->fecha_entrega.'</td>
                <td>'.$lugar.'</td>
                <td>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Second group">
                        <button class="btn btn-secondary btn-sm" name="btnEditarItem" data-toggle="tooltip" title="Editar" onClick="detalleRequerimientoModal(event, '.$i.');" ><i class="fas fa-edit"></i></button>
                        <button class="btn btn-danger btn-sm" name="btnEliminarItem" data-toggle="tooltip" title="Eliminar" onclick="eliminarItemDetalleRequerimiento(event, '.$i.');" ><i class="fas fa-trash-alt"></i></button>
                        <button class="btn btn-primary btn-sm" name="btnAdjuntarArchivos" data-toggle="tooltip" title="Adjuntos" onClick="archivosAdjuntosModal(event, '.$i.');"><i class="fas fa-paperclip"></i></button>
                    </div>
                </td>
            </tr>
            ';
            $item = [
                'id_item'=> 0,
                'id_tipo_item'=> 0,
                'id_producto'=> 0,    
                'id_servicio'=> 0,
                'id_equipo'=> 0,
                'id_detalle_requerimiento'=> 0,
                'cod_item'=> 'SIN CODIGO',
                'des_item'=> $det->descripcion,
                'id_unidad_medida'=> 0,
                'unidad'=> '',
                'cantidad'=> $det->cantidad,
                'precio_referencial'=> $det->importe,
                'fecha_entrega'=> $det->fecha_entrega,
                'lugar_entrega'=> $lugar,
                'id_partida'=> 0,
                'cod_partida'=> null,
                'des_partida'=> null,
                'estado'=> 1
            ];
            array_push($data_item, $item);
        }
        // return json_encode(['html'=>$html,'data_item'=>$data_item]);
        return response()->json($detalle);
    }


    function view_main_logistica()
    {
        $cantidades = AlmacenController::cantidades_main();
        $cantidad_requerimientos_elaborados = $cantidades['requerimientos'];
        $cantidad_ordenes_pendientes = $cantidades['orden'];
        $cantidad_despachos_pendientes = $cantidades['despachos'];
        $cantidad_ingresos_pendientes = $cantidades['ingresos'];
        $cantidad_salidas_pendientes = $cantidades['salidas'];
        $cantidad_transferencias_pendientes = $cantidades['transferencias'];
        $cantidad_pagos_pendientes = $cantidades['pagos'];

        return view('logistica/main', compact(
            'cantidad_requerimientos_elaborados',
            'cantidad_ordenes_pendientes',
            'cantidad_despachos_pendientes',
            'cantidad_ingresos_pendientes',
            'cantidad_salidas_pendientes',
            'cantidad_transferencias_pendientes',
            'cantidad_pagos_pendientes'
            ));

        // $cantidad_requerimientos_generados = $this->cantidad_requerimientos_generados();
        // $cantidad_requerimientos_aprobados = $this->cantidad_requerimientos_aprobados();
        // $cantidad_requerimientos_observados = $this->cantidad_requerimientos_observados();
        // $cantidad_requerimientos_anulados = $this->cantidad_requerimientos_anulados();
        // return view('logistica/main', compact(
        //     'cantidad_requerimientos_generados',
        //     'cantidad_requerimientos_aprobados',
        //     'cantidad_requerimientos_observados',
        //     'cantidad_requerimientos_anulados'
        //     ));
    }

    public function cantidad_requerimientos_generados(){
        $estado_elaborado = $this->get_estado_doc('Elaborado');

        $data = DB::table('almacen.alm_req')
            ->select('alm_req.id_requerimiento')
            ->where('alm_req.id_estado_doc', '>=',$estado_elaborado)
            ->count();
        return $data;
    }

    public function cantidad_requerimientos_aprobados(){
        $estado_aprobado = $this->get_estado_doc('Aprobado');

        $data = DB::table('almacen.alm_req')
            ->select('alm_req.id_requerimiento')
            ->where('alm_req.id_estado_doc', '=',$estado_aprobado)
            ->count();
        return $data;
    }
    public function cantidad_requerimientos_observados(){
        $estado_observado = $this->get_estado_doc('Observado');

        $data = DB::table('almacen.alm_req')
            ->select('alm_req.id_requerimiento')
            ->where('alm_req.id_estado_doc', '=',$estado_observado)
            ->count();
        return $data;
    }

    public function cantidad_requerimientos_anulados(){
        $estado_anulado = $this->get_estado_doc('Anulado');

        $data = DB::table('almacen.alm_req')
            ->select('alm_req.id_requerimiento')
            ->where('alm_req.id_estado_doc', '=',$estado_anulado)
            ->count();
        return $data;
    }

    function get_header_orden($id_orden){
        $orden_header_orden = [];
        $orden_header_proveedor = [];
        $orden_header_empresa = [];
        $orden_condiciones = [];
        $result = [];
        $data = DB::table('logistica.log_ord_compra')
            ->select(
                'log_ord_compra.codigo',
                'log_ord_compra.plazo_dias',
                'log_ord_compra.fecha AS fecha_orden',
                'log_ord_compra.id_usuario',
                'log_ord_compra.id_moneda',
                'sis_moneda.simbolo as moneda_simbolo',
                'sis_moneda.descripcion as moneda_descripcion',
                'log_ord_compra.monto_igv',
                'log_ord_compra.monto_total',
                DB::raw("(pers.nombres) || ' ' || (pers.apellido_paterno) || ' ' || (pers.apellido_materno) as nombre_usuario"),
                
                'log_ord_compra.personal_responsable',
                DB::raw("(pers_res.nombres) || ' ' || (pers_res.apellido_paterno) || ' ' || (pers_res.apellido_materno) as nombre_personal_responsable"),

                'adm_tp_docum.descripcion AS tipo_documento',
                'sis_identi.descripcion AS tipo_doc_proveedor',
                'adm_contri.razon_social AS razon_social_proveedor',
                'adm_contri.nro_documento AS nro_documento_proveedor',
                'adm_contri.telefono AS telefono_proveedor',
                'adm_contri.direccion_fiscal AS direccion_fiscal_proveedor',
                'log_cotizacion.id_empresa',
                'contab_sis_identi.descripcion AS tipo_doc_empresa',
                'contab_contri.razon_social AS razon_social_empresa',
                'contab_contri.nro_documento AS nro_documento_empresa',
                'contab_contri.direccion_fiscal AS direccion_fiscal_empresa',
                'alm_req.codigo AS codigo_requerimiento',

                'cont_tp_doc.descripcion AS tipo_doc_contable',
                'log_cdn_pago.descripcion AS condicion_pago',
                'log_cotizacion.condicion_credito_dias',
                'log_cotizacion.nro_cuenta_principal',
                'log_cotizacion.nro_cuenta_alternativa',
                'log_cotizacion.nro_cuenta_detraccion',
                'log_cotizacion.email_proveedor',
                'log_det_ord_compra.personal_autorizado',
                'log_det_ord_compra.lugar_despacho as lugar_despacho_orden',
                DB::raw("(pers_aut.nombres) ||' ' || (pers_aut.apellido_paterno) || ' ' || (pers_res.apellido_materno) AS nombre_personal_autorizado"),
                'log_det_ord_compra.descripcion_adicional AS descripcion_detalle_orden',

                'valoriza_coti_detalle.id_detalle_requerimiento',
                'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
                'log_valorizacion_cotizacion.cantidad_cotizada',
                'log_valorizacion_cotizacion.precio_cotizado',
                'alm_und_medida.descripcion AS unidad_medida_cotizado',
                'log_valorizacion_cotizacion.flete',
                'log_valorizacion_cotizacion.porcentaje_descuento',
                'log_valorizacion_cotizacion.monto_descuento',
                'log_valorizacion_cotizacion.subtotal',
                'log_valorizacion_cotizacion.plazo_entrega',
                'log_valorizacion_cotizacion.incluye_igv',
                'log_valorizacion_cotizacion.garantia',
                'alm_det_req.descripcion_adicional AS descripcion_requerimiento',
                'alm_det_req.id_item',
                'alm_item.codigo AS codigo_item',
                'alm_prod.descripcion AS descripcion_producto',
                'alm_prod.codigo AS producto_codigo',
                'log_servi.codigo AS servicio_codigo',
                'log_servi.descripcion AS descripcion_servicio'
            )
            ->leftJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_orden_compra', '=', 'log_ord_compra.id_orden_compra')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_ord_compra.id_condicion')
            ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', 'log_det_ord_compra.id_valorizacion_cotizacion')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_valorizacion_cotizacion.id_unidad_medida')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'log_ord_compra.id_usuario')
            ->leftJoin('rrhh.rrhh_trab as trab', 'trab.id_trabajador', '=', 'sis_usua.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu as post', 'post.id_postulante', '=', 'trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as pers', 'pers.id_persona', '=', 'post.id_persona')

            ->leftJoin('configuracion.sis_usua as sis_usua_res', 'sis_usua_res.id_usuario', '=', 'log_ord_compra.personal_responsable')
            ->leftJoin('rrhh.rrhh_trab as trab_res', 'trab_res.id_trabajador', '=', 'sis_usua_res.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu as post_res', 'post_res.id_postulante', '=', 'trab_res.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as pers_res', 'pers_res.id_persona', '=', 'post_res.id_persona')

            ->leftJoin('configuracion.sis_usua as sis_usua_aut', 'sis_usua_aut.id_usuario', '=', 'log_det_ord_compra.personal_autorizado')
            ->leftJoin('rrhh.rrhh_trab as trab_aut', 'trab_aut.id_trabajador', '=', 'sis_usua_res.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu as post_aut', 'post_aut.id_postulante', '=', 'trab_aut.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as pers_aut', 'pers_aut.id_persona', '=', 'post_aut.id_persona')

            ->leftJoin('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'log_valorizacion_cotizacion.personal_autorizado')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->Join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->Join('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->Join('administracion.adm_tp_docum', 'adm_tp_docum.id_tp_documento', '=', 'log_ord_compra.id_tp_documento')
            ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_valorizacion_cotizacion.id_cotizacion')
            ->Join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
            ->leftJoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'log_cotizacion.id_tp_doc')
            ->Join('contabilidad.adm_contri as contab_contri', 'contab_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->Join('contabilidad.sis_identi as contab_sis_identi', 'contab_sis_identi.id_doc_identidad', '=', 'contab_contri.id_doc_identidad')
            ->Join('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
            ->Join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'valoriza_coti_detalle.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->where([
                ['log_ord_compra.id_orden_compra', '=', $id_orden],
                ['log_ord_compra.estado', '!=', 7]
            ])
            ->get();

            $id_val_cot_list=[];

            foreach ($data as $data) {
                if(in_array($data->id_valorizacion_cotizacion,$id_val_cot_list)==false){
                    array_push($id_val_cot_list,$data->id_valorizacion_cotizacion);
    
                    $orden_header_orden = [
                        'codigo' => $data->codigo,
                        'tipo_documento' => $data->tipo_documento,
                        'fecha_orden' => $data->fecha_orden,
                        'nombre_usuario' => $data->nombre_usuario,
                        'nombre_personal_responsable' => $data->nombre_personal_responsable,
                        'codigo_requerimiento' => $data->codigo_requerimiento,
                        'moneda_simbolo' => $data->moneda_simbolo,
                        'monto_igv' => $data->monto_igv,
                        'monto_total' => $data->monto_total,
                        'moneda_descripcion' => $data->moneda_descripcion,
                    ];
                    $orden_header_proveedor = [
                        'razon_social_proveedor' => $data->razon_social_proveedor,
                        'tipo_doc_proveedor' => $data->tipo_doc_proveedor,
                        'nro_documento_proveedor' => $data->nro_documento_proveedor,
                        'telefono_proveedor' => $data->telefono_proveedor,
                        'direccion_fiscal_proveedor' => $data->direccion_fiscal_proveedor,
                        'email_proveedor' => $data->email_proveedor
                    ];
                    $orden_header_empresa = [
                        'id_empresa' => $data->id_empresa,
                        'razon_social_empresa' => $data->razon_social_empresa,
                        'tipo_doc_empresa' => $data->tipo_doc_empresa,
                        'nro_documento_empresa' => $data->nro_documento_empresa,
                        'direccion_fiscal_empresa' => $data->direccion_fiscal_empresa
                    ];
                    $orden_condiciones = [
                        'tipo_doc_contable' => $data->tipo_doc_contable,
                        'condicion_pago' => $data->condicion_pago,
                        'plazo_dias' => $data->plazo_dias,
                        'condicion_credito_dias' => $data->condicion_credito_dias,
                        'nro_cuenta_principal' => $data->nro_cuenta_principal,
                        'nro_cuenta_alternativa' => $data->nro_cuenta_alternativa,
                        'nro_cuenta_detraccion' => $data->nro_cuenta_detraccion
                    ];

                }
            }
            $result = [
                'orden' => $orden_header_orden,
                'proveedor' => $orden_header_proveedor,
                'empresa' => $orden_header_empresa,
                'condiciones' => $orden_condiciones
            ];
    
            return $result;
            
    }

    public function flujo_aprobacion_orden($id_operacion){
        
        $adm_flujo_aprobacion = DB::table('administracion.adm_flujo')
        ->select(
            'adm_flujo.id_flujo',
            'adm_flujo.id_operacion',
            'adm_flujo.id_rol',
            DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_responsable"),
            'rrhh_rol.id_area',
            'rrhh_rol_concepto.descripcion as descripcion_rol',
            'adm_flujo.nombre as nombre_fase',
            'adm_flujo.orden',
            'adm_flujo.estado'
            )
        ->leftJoin('rrhh.rrhh_rol', 'rrhh_rol.id_rol_concepto', '=', 'adm_flujo.id_rol')
        ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
        ->leftJoin('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_rol.id_trabajador')
        ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
        ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
        ->where([
            ['adm_flujo.estado', '=', 1],
            // ['rrhh_rol.id_area', '=', $id_area],
             ['adm_flujo.id_operacion', '=', $id_operacion]
        ])
        ->orderBy('adm_flujo.orden', 'asc')
        ->get()->unique();
        // return $adm_flujo_aprobacion;
        $flujo_aprobacion=[];
        foreach($adm_flujo_aprobacion as $data){
            
            // $rol_list[]= $data->id_rol;

            $flujo_aprobacion[]=[
                'id_flujo'=>$data->id_flujo,
                'nombre_fase'=>$data->nombre_fase,
                'id_operacion'=>$data->id_operacion,
                'id_rol'=>$data->id_rol,
                'nombre_responsable'=>$data->nombre_responsable,
                'descripcion_rol'=>$data->descripcion_rol,
                'orden'=>$data->orden,
                'estado'=>$data->estado
            ];
        }
        return $flujo_aprobacion;
    }

    function get_requerimientos_orden($id_orden){
        $sql = DB::table('logistica.log_det_ord_compra')
        ->select(
   
            'valoriza_coti_detalle.id_requerimiento'
            )
        ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', 'log_det_ord_compra.id_valorizacion_cotizacion')
        ->leftJoin('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
         ->where([
            ['log_det_ord_compra.id_orden_compra', '=', $id_orden]
        ])
        ->orderBy('log_det_ord_compra.id_detalle_orden', 'asc')
        ->get()->unique();

        $list_req=[];
        if($sql){
            foreach($sql as $data){
                $list_req[]=$data->id_requerimiento;
            }
        }

        return $list_req;
    }

    function get_gegistro_pago($id_orden){
        $sql = DB::table('logistica.log_ord_compra_pago')
        ->select(
            'log_ord_compra_pago.*',
            DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) as nombre_responsable")
            )
        ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'log_ord_compra_pago.registrado_por')
        ->leftJoin('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'sis_usua.id_trabajador')
        ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
        ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
         ->where([
            ['log_ord_compra_pago.id_orden_compra', '=', $id_orden]
        ])
        ->orderBy('log_ord_compra_pago.id_pago', 'asc')
        ->get();
        return $sql;
    }
    function get_entrada_almacen($id_orden){
        $sql = DB::table('logistica.log_det_ord_compra')
        ->select(
            'log_det_ord_compra.id_detalle_orden',
            'log_det_ord_compra.id_item',
            'alm_prod.descripcion as descripcion_producto',
            'guia_com_det.id_unid_med',
            'alm_und_medida.abreviatura',
            'guia_com_det.cantidad',
            'guia_com_det.unitario',
            'guia_com_det.total',
            'guia_com.id_guia',
            DB::raw("(guia_com.serie) || ' ' || (guia_com.numero) as serie_numero"),
            'guia_com.fecha_emision',
            'guia_com.fecha_almacen',
            'guia_com.id_almacen',
            'mov_alm.codigo',
            'alm_almacen.descripcion as descripcion_almacen',
            'guia_com.id_tp_doc_almacen',
            'tp_doc_almacen.descripcion as descripcion_tp_doc_almacen'
            )
            ->join('almacen.guia_com_det', 'guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden')
            ->leftJoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->leftJoin('almacen.mov_alm', 'mov_alm.id_guia_com', '=', 'guia_com.id_guia')
            ->leftJoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'guia_com.id_almacen')
            ->leftJoin('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_com.id_tp_doc_almacen')
            ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'guia_com_det.id_unid_med')
         ->where([
            ['log_det_ord_compra.id_orden_compra', '=', $id_orden]
        ])
        ->orderBy('log_det_ord_compra.id_detalle_orden', 'asc')
        ->get();
        return $sql;
    }

    function get_depacho($id_orden){
        $sql = DB::table('logistica.log_det_ord_compra')
        ->select(
            'log_det_ord_compra.id_detalle_orden',
            'log_det_ord_compra.id_item',
            'alm_prod.descripcion as descripcion_producto',
            'guia_ven_det.id_unid_med',
            'alm_und_medida.abreviatura',
            'guia_ven_det.cantidad',
            'guia_ven.id_guia_ven',
            DB::raw("(guia_ven.serie) || ' ' || (guia_ven.numero) AS serie_numero"),
            'guia_ven.fecha_emision',
            'guia_ven.fecha_almacen',
            'guia_ven.id_almacen',
            'mov_alm.codigo',
            'alm_almacen.descripcion as descripcion_almacen',
            'guia_ven.id_tp_doc_almacen',
            'tp_doc_almacen.descripcion as descripcion_tp_doc_almacen'
            )
            ->join('almacen.guia_com_det', 'guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden')
            ->leftJoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->join('almacen.mov_alm', 'mov_alm.id_guia_com', '=', 'guia_com.id_guia')
            ->join('almacen.mov_alm_det', 'mov_alm_det.id_mov_alm', '=', 'mov_alm.id_mov_alm')
            ->leftJoin('almacen.guia_ven_det', 'guia_ven_det.id_ing_det', '=', 'mov_alm_det.id_guia_com_det')
            ->leftJoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')
            ->leftJoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'guia_ven.id_almacen')
            ->leftJoin('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_com.id_tp_doc_almacen')
            ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'guia_ven_det.id_unid_med')
         ->where([
            ['log_det_ord_compra.id_orden_compra', '=', $id_orden]
        ])
        ->orderBy('log_det_ord_compra.id_detalle_orden', 'asc')
        ->get();
        return $sql;
    }

    function explorar_orden($id_orden){
        $orden=[];
        $flujo_aprobacion=[];
        $historial_aprobacion=[];

        $orden =  $this->get_header_orden($id_orden);
        $registro_pago = $this->get_gegistro_pago($id_orden);
        $entrada_almacen = $this->get_entrada_almacen($id_orden);
        $despacho = $this->get_depacho($id_orden);

        $requerimiento_orden_list= $this->get_requerimientos_orden($id_orden);

        // $id_grupos_list=[];
        // $id_areas_list=[];
        $id_grupo_area_list=[];

        if(count($requerimiento_orden_list)>0){
            foreach($requerimiento_orden_list as $r){
                // $id_grupos_list[]=$this->get_id_grupo($r);
                // $id_areas_list[]=$this->get_id_area($r);
                $id_grupo_area_list[]= ["id_grupo"=>$this->get_id_grupo($r),'id_area'=>$this->get_id_area($r)];
            }
        }
 
        $tipo_documento= 2;
        $id_prioridad= 1;

        // get id_operacion
        $id_operacion_list=[];
        foreach($id_grupo_area_list as $ga){
            $id_operacion_list[]=$this->get_id_operacion($ga['id_grupo'],$ga['id_area'],$tipo_documento);
        }
        
        // get flujo_aprobación
        $flujo_aprobacion_orden_list=[];
        // foreach($id_operacion_list as $op){
                // $flujo_aprobacion_orden_list[] =  $this->flujo_aprobacion_orden($op)[0];
        // }

        // limpiar array de flujo_aprobación si es el mismo id_rol
        $temp=[];
        // $new_flujo_aprobacion_orden_list=[];
        // foreach($flujo_aprobacion_orden_list as $fao){
        //     if(in_array($fao['id_rol'],$temp)==false){
        //         $temp[]=$fao['id_rol'];
        //         $new_flujo_aprobacion_orden_list[]=$fao;
        //     }
        // }

        $output=[
            'header'=>$orden,
            'registro_pago'=>$registro_pago,
            'entrada_almacen'=>$entrada_almacen,
            'despacho'=>$despacho,
            // 'new_flujo_aprobacion_orden_list'=>$new_flujo_aprobacion_orden_list,
            'new_flujo_aprobacion_orden_list'=>[],
            'flujo_aprobacion'=>$flujo_aprobacion,
            'historial_aprobacion'=>$historial_aprobacion
        ];

        return response()->json($output);
    }

    // comprobantes

    function view_doc_compra(){
        $proveedores = $this->mostrar_proveedores_cbo();
        $clasificaciones = $this->mostrar_guia_clas_cbo();
        $condiciones = $this->mostrar_condiciones_cbo();
        $tp_doc = $this->mostrar_tp_doc_cbo();
        $moneda = $this->mostrar_moneda_cbo();
        $detracciones = $this->mostrar_detracciones_cbo();
        $impuestos = $this->mostrar_impuestos_cbo();
        $usuarios = $this->select_usuarios();
        $tp_contribuyente = $this->tp_contribuyente_cbo();
        $sis_identidad = $this->sis_identidad_cbo();
        return view('logistica/comprobantes/doc_compra', compact('proveedores','clasificaciones','condiciones','tp_doc','moneda','detracciones','impuestos','usuarios','tp_contribuyente','sis_identidad'));
    }

    public function listar_docs_compra(){
        $data = DB::table('almacen.doc_com')
        ->select('doc_com.*','adm_contri.razon_social','adm_estado_doc.estado_doc as des_estado')
        ->join('logistica.log_prove','log_prove.id_proveedor','=','doc_com.id_proveedor')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','doc_com.estado')
        ->where('doc_com.estado','!=',7)
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function listar_doc_guias($id_doc){
        $guias = DB::table('almacen.doc_com_guia')
        ->select('doc_com_guia.*',DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as guia"),
        'guia_com.fecha_emision as fecha_guia','tp_ope.descripcion as des_operacion',
        'adm_contri.razon_social')
        ->join('almacen.guia_com','guia_com.id_guia','=','doc_com_guia.id_guia_com')
        ->join('almacen.tp_ope','tp_ope.id_operacion','=','guia_com.id_operacion')
        ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->where([['doc_com_guia.id_doc_com','=',$id_doc],
                ['doc_com_guia.estado','=',1]])
        ->get();
        $html ='';
        foreach($guias as $guia){
            $html .= '
            <tr id="doc-'.$guia->id_doc_com_guia.'">
                <td>'.$guia->guia.'</td>
                <td>'.$guia->fecha_guia.'</td>
                <td>'.$guia->razon_social.'</td>
                <td>'.$guia->des_operacion.'</td>
                <td><i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                    title="Anular Guia" onClick="anular_guia('.$guia->id_guia_com.','.$guia->id_doc_com_guia.');"></i>
                </td>
            </tr>';
        }
        return json_encode($html);
    }

    public function anular_orden_doc_com($id_doc_com,$id_orden_compra)
    {
        $data=0;
        $ordenes = DB::table('logistica.log_det_ord_compra')
            ->select('log_det_ord_compra.id_detalle_orden')
            ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
            ->where([
                ['log_det_ord_compra.id_orden_compra','=',$id_orden_compra]
                ])
            ->get()->toArray();

        foreach($ordenes as $orden){
            $data= DB::table('almacen.doc_com_det')
            ->where('id_detalle_orden', $orden->id_detalle_orden)
            ->update([ 'estado' => 7 ]);
        }

        return response()->json($data);
    }

    public function listar_doc_items($id_doc){
        $detalle = DB::table('almacen.doc_com_det')
            ->select('doc_com_det.*','alm_prod.codigo','alm_prod.descripcion',
            DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as guia"),
            'alm_und_medida.abreviatura')
            ->join('almacen.alm_item','alm_item.id_item','=','doc_com_det.id_item')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
            ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','doc_com_det.id_guia_com_det')
            ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','doc_com_det.id_unid_med')
            ->where([['doc_com_det.id_doc','=',$id_doc],
                    ['doc_com_det.estado','=',1]])
            ->get();
        $html = '';
        foreach($detalle as $det){
            $html .= '
            <tr id="det-'.$det->id_doc_det.'">
                <td>'.$det->guia.'</td>
                <td>'.$det->codigo.'</td>
                <td>'.$det->descripcion.'</td>
                <td><input type="number" class="input-data right" name="cantidad" 
                    value="'.$det->cantidad.'" onChange="calcula_total('.$det->id_doc_det.');" 
                    disabled="true"/>
                </td>
                <td>'.$det->abreviatura.'</td>
                <td><input type="number" class="input-data right" name="precio_unitario" 
                    value="'.$det->precio_unitario.'" onChange="calcula_total('.$det->id_doc_det.');" 
                    disabled="true"/>
                </td>
                <td><input type="number" class="input-data right" name="porcen_dscto" 
                    value="'.$det->porcen_dscto.'" onChange="calcula_dscto('.$det->id_doc_det.');" 
                    disabled="true"/>
                </td>
                <td><input type="number" class="input-data right" name="total_dscto" 
                    value="'.$det->total_dscto.'" onChange="calcula_total('.$det->id_doc_det.');" 
                    disabled="true"/>
                </td>
                <td><input type="number" class="input-data right" name="precio_total" 
                    value="'.$det->precio_total.'" disabled="true"/>
                </td>
                <td style="display:flex;">
                    <i class="fas fa-pen-square icon-tabla blue boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_detalle('.$det->id_doc_det.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_detalle('.$det->id_doc_det.');"></i>
                </td>
            </tr>';
        }
        return json_encode($html);
    }

    public function guardar_doc_compra(Request $request)
    {
        $usuario = Auth::user();
        $fecha = date('Y-m-d H:i:s');
        $id_doc = DB::table('almacen.doc_com')->insertGetId(
            [
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_tp_doc' => $request->id_tp_doc,
                'id_proveedor' => $request->id_proveedor,
                'fecha_emision' => $request->fecha_emision,
                'fecha_vcmto' => $request->fecha_vcmto,
                'id_condicion' => $request->id_condicion,
                'credito_dias' => $request->credito_dias,
                'moneda' => $request->moneda,
                'tipo_cambio' => $request->tipo_cambio,
                'sub_total' => $request->sub_total,
                'total_descuento' => $request->total_descuento,
                'porcen_descuento' => $request->porcen_descuento,
                'total' => $request->total,
                'total_igv' => $request->total_igv,
                'total_ant_igv' => $request->total_ant_igv,
                'porcen_igv' => $request->porcen_igv,
                'porcen_anticipo' => $request->porcen_anticipo,
                'total_otros' => $request->total_otros,
                'total_a_pagar' => $request->total_a_pagar,
                'usuario' => $request->usuario,
                'registrado_por' => $usuario->id_usuario,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_doc_com'
            );
        return response()->json(["id_doc"=>$id_doc,"id_proveedor"=>$request->id_proveedor]);
    }

    public function update_doc_compra(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $usuario = Auth::user();
        $data = DB::table('almacen.doc_com')
            ->where('id_doc_com',$request->id_doc_com)
            ->update([
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_tp_doc' => $request->id_tp_doc,
                'id_proveedor' => $request->id_proveedor,
                'fecha_emision' => $request->fecha_emision,
                'fecha_vcmto' => $request->fecha_vcmto,
                'id_condicion' => $request->id_condicion,
                'credito_dias' => $request->credito_dias,
                'moneda' => $request->moneda,
                'tipo_cambio' => $request->tipo_cambio,
                'sub_total' => $request->sub_total,
                'total_descuento' => $request->total_descuento,
                'porcen_descuento' => $request->porcen_descuento,
                'total' => $request->total,
                'total_igv' => $request->total_igv,
                'total_ant_igv' => $request->total_ant_igv,
                'porcen_igv' => $request->porcen_igv,
                'porcen_anticipo' => $request->porcen_anticipo,
                'total_otros' => $request->total_otros,
                'total_a_pagar' => $request->total_a_pagar,
                'usuario' => $request->usuario,
                'registrado_por' => $usuario->id_usuario,
            ]);
        return response()->json($data);
    }

    public function update_doc_detalle(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $data = DB::table('almacen.doc_com_det')
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

    public function anular_doc_detalle($id_doc_det)
    {
        $data = DB::table('almacen.doc_com_det')
            ->where('id_doc_det', $id_doc_det)
            ->update(['estado' => 7]);
        return response()->json($data);
    }
    
    public function anular_doc_compra($id)
    {
        $guias = DB::table('almacen.doc_com_guia')
        ->join('almacen.guia_com','guia_com.id_guia','=','doc_com_guia.id_guia_com')
        ->where([['doc_com_guia.id_doc_com','=', $id],
                 ['doc_com_guia.estado','=',1],
                 ['guia_com.estado','!=',7]])
                 ->count();

        $rspta = '';
        if ($guias > 0){
            $rspta .= 'El documento esta relacionado con Guias Activas.';
        }

        $prorrateo = DB::table('almacen.guia_com_prorrateo')
        ->where([['id_doc_com','=',$id]])->count();

        if ($prorrateo > 0){
            $rspta .= 'El documento esta como Documento de Prorrateo.';
        }

        if ($guias == 0 && $prorrateo == 0){
            DB::table('almacen.doc_com')->where('id_doc_com', $id)
                ->update([ 'estado' => 7 ]);
            DB::table('almacen.doc_com_det')->where('id_doc', $id)
                ->update([ 'estado' => 7 ]);
            DB::table('almacen.doc_com_guia')->where('id_doc_com', $id)
                ->update([ 'estado' => 7 ]);
        }
        return response()->json($rspta);
    }


    public function mostrar_doc_com($id){
        $doc = DB::table('almacen.doc_com')
            ->select('doc_com.*','adm_estado_doc.estado_doc','sis_usua.nombre_corto','sis_moneda.simbolo',
            'adm_contri.nro_documento','adm_contri.razon_social')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','doc_com.estado')
            ->leftJoin('configuracion.sis_usua','sis_usua.id_usuario','=','doc_com.registrado_por')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','doc_com.moneda')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','doc_com.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->where('doc_com.id_doc_com',$id)
            ->get();

        $doc_det = DB::table('almacen.doc_com_det')
        ->select('doc_com_det.id_detalle_orden')
        ->where('doc_com_det.id_doc',$id)
        ->get();
        

        $collect = collect($doc->first());
        
        if(count($doc_det)>0){
            $collect->put('doc_com_det',$doc_det);
        }else{
            $collect->put('doc_com_det',[]);
        }

        return response()->json([$collect]);
    }


 

    public function listar_guias_prov($id_proveedor){
        $data = DB::table('almacen.guia_com')
            ->select('guia_com.*',DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as guia"),
            'adm_contri.razon_social','adm_estado_doc.estado_doc')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_com.estado')
            ->leftjoin('almacen.doc_com_guia','doc_com_guia.id_guia_com','=','guia_com.id_guia')
            ->where([['guia_com.id_proveedor','=',$id_proveedor],
                    // ['guia_com.estado','=',9],//Guias procesadas
                    ['doc_com_guia.id_guia_com','=',null]])
            // ->orWhere('doc_com_guia.estado',2)
            ->get();
        return response()->json($data);
    }

    public function guardar_doc_items_guia($id_guia, $id_doc){
        $fecha = date('Y-m-d H:i:s');
        $detalle = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*','log_valorizacion_cotizacion.precio_cotizado as precio')//jalar el precio de la oc o cotizacion
            ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
            ->leftjoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','log_det_ord_compra.id_valorizacion_cotizacion')
            ->where([['guia_com_det.id_guia_com','=',$id_guia],
                    ['guia_com_det.estado','=',1 ]])
            ->get();
        $nuevo_detalle = [];
        $cant = 0;
    
        foreach ($detalle as $det){
            $exist = false;
            foreach ($nuevo_detalle as $nue => $value){
                if ($det->id_producto == $value['id_producto'] && $det->id_guia_com == $value['id_guia_com']){
                    $nuevo_detalle[$nue]['cantidad'] = floatval($value['cantidad']) + floatval($det->cantidad);
                    $nuevo_detalle[$nue]['unitario'] = floatval($value['unitario']) + floatval($det->unitario);
                    $nuevo_detalle[$nue]['total'] = floatval($value['total']) + floatval($det->total);
                    $exist = true;
                }
            }
            if ($exist === false){
                $nuevo = [
                    'id_guia_com_det' => $det->id_guia_com_det,
                    'id_guia_com' => $det->id_guia_com,
                    'id_producto' => $det->id_producto,
                    'id_unid_med' => $det->id_unid_med,
                    'cantidad' => floatval($det->cantidad),
                    'unitario' => floatval($det->precio),
                    'total' => (floatval($det->cantidad) * floatval($det->precio))
                    ];
                array_push($nuevo_detalle, $nuevo);
            }
        }
        foreach($nuevo_detalle as $det){
            $item = DB::table('almacen.alm_item')
                ->where('id_producto',$det['id_producto'])
                ->first();

            $id_det = DB::table('almacen.doc_com_det')->insert(
                [
                    'id_doc'=>$id_doc,
                    'id_item'=>$item->id_item,
                    'cantidad'=>$det['cantidad'],
                    'id_unid_med'=>$det['id_unid_med'],
                    'precio_unitario'=>$det['unitario'],
                    'sub_total'=>$det['total'],
                    'porcen_dscto'=>0,
                    'total_dscto'=>0,
                    'precio_total'=>$det['total'],
                    'id_guia_com_det'=>$det['id_guia_com_det'],
                    'estado'=>1,
                    'fecha_registro'=>$fecha
                ]);
        }
        $guia = DB::table('almacen.doc_com_guia')->insert(
            [
                'id_doc_com'=>$id_doc,
                'id_guia_com'=>$id_guia,
                'estado'=>1,
                'fecha_registro'=>$fecha
            ]);
        $ingreso = DB::table('almacen.mov_alm')
            ->where('mov_alm.id_guia_com',$id_guia)
            ->first();

        if (isset($ingreso->id_mov_alm)){
            DB::table('almacen.mov_alm')
                ->where('id_mov_alm',$ingreso->id_mov_alm)
                ->update(['id_doc_com'=>$id_doc]);
        }

        return response()->json($guia);
    }

    public function guardar_doc_com_det_orden(Request $request, $id_doc){
        $status =0;
        $fecha = date('Y-m-d H:i:s');
        $header_id_orden_compra = $request->header['id_orden_compra'];
        $header_codigo_orden = $request->header['codigo_orden'];
        $header_id_proveedor = $request->header['id_proveedor'];

        foreach($request->detalle_orden as $data){
            $doc_com_det = DB::table('almacen.doc_com_det')->insertGetId(
                [
                    'id_doc'  => $id_doc,
                    'id_item'  => $data['id_item'],
                    'cantidad'  => $data['cantidad_cotizada'],
                    'id_unid_med'  => $data['id_unidad_medida'],
                    'precio_unitario'  => $data['precio_cotizado'],
                    'sub_total'  => $data['subtotal'],
                    'porcen_dscto'  => $data['porcentaje_descuento'],
                    'total_dscto'  => $data['monto_descuento'],
                    'precio_total'  => $data['subtotal'],
                    // 'id_guia_com_det'  => $data[''],
                    'estado'  => 1,
                    'fecha_registro'  => $fecha,
                    // 'obs'  => '',
                    'id_detalle_orden'  => $data['id_detalle_requerimiento']
                ],
                'id_doc_det'
            );
        }
        if($doc_com_det > 0){
            $status = 200;
        }

        return response()->json($status);

    }

    public function listar_doc_com_orden($id_doc){
        $status=0;
        $doc_com_doc_com_det=[];
        $ordenes=[];
        $doc_com = DB::table('almacen.doc_com')
        ->select(
            'doc_com.*'
        )
        ->where([
            ['doc_com.id_doc_com','=',$id_doc],
            ['doc_com.estado','=',1]
            ])
        ->get();

        $doc_com_det = DB::table('almacen.doc_com_det')
        ->select('doc_com_det.*','alm_prod.codigo','alm_prod.descripcion',
        DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as guia"),
        'alm_und_medida.abreviatura',
        'log_ord_compra.codigo as codigo_orden'
        )
        ->join('almacen.alm_item','alm_item.id_item','=','doc_com_det.id_item')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
        ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','doc_com_det.id_guia_com_det')
        ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','doc_com_det.id_unid_med')
        ->join('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','doc_com_det.id_detalle_orden')
        ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
        ->where([['doc_com_det.id_doc','=',$id_doc],['doc_com_det.estado','=',1]])
        ->get();

        foreach($doc_com as $data){
            $doc_com_doc_com_det[]=[
                'id_doc_com'=>$data->id_doc_com,
                'serie'=>$data->serie,
                'numero'=>$data->numero,
                'id_tp_doc'=>$data->id_tp_doc,
                'id_proveedor'=>$data->id_proveedor,
                'fecha_emision'=>$data->fecha_emision,
                'fecha_vcmto'=>$data->fecha_vcmto,
                'id_condicion'=>$data->id_condicion,
                'moneda'=>$data->moneda,
                'tipo_cambio'=>$data->tipo_cambio,
                'sub_total'=>$data->sub_total,
                'total_descuento'=>$data->total_descuento,
                'porcen_descuento'=>$data->porcen_descuento,
                'total'=>$data->total,
                'total_igv'=>$data->total_igv,
                'total_ant_igv'=>$data->total_ant_igv,
                'total_a_pagar'=>$data->total_a_pagar,
                'usuario'=>$data->usuario,
                'estado'=>$data->estado,
                'fecha_registro'=>$data->fecha_registro,
                'credito_dias'=>$data->credito_dias,
                'porcen_igv'=>$data->porcen_igv,
                'porcen_anticipo'=>$data->porcen_anticipo,
                'total_otros'=>$data->total_otros,
                'registrado_por'=>$data->registrado_por,
                'id_sede'=>$data->id_sede,
                'doc_com_det'=>$doc_com_det
            ];
        }

        //listar y almacenar en una array todo los id_detalle_orden para obtener la cabecera de la orden
        $id_det_orden_list=[];
        foreach($doc_com_det as $data){
            if($data->id_detalle_orden != null){
                $id_det_orden_list[]= $data->id_detalle_orden;
            }
        }
        if(count($id_det_orden_list) > 0 ){
            $ordenes=$this->getOrdenByDetOrden($id_det_orden_list);
        }

        if(count($ordenes)> 0){
            $status = 200;
        }

        $output=[
                'doc_com_doc_com_det'=>$doc_com_doc_com_det,
                'ordenes'=>$ordenes,
                'status'=>$status
        ];
        return response()->json($output);
    }

   public function getOrdenByDetOrden($id_det_orden_list){
        $ord = DB::table('logistica.log_det_ord_compra')
        ->select(
            'log_ord_compra.id_orden_compra',
            'log_ord_compra.codigo',
            'log_ord_compra.fecha',
            'log_ord_compra.id_proveedor',
            'log_ord_compra.id_sede',
            'adm_contri.id_contribuyente',
            'adm_contri.razon_social',
            'adm_contri.nro_documento',
            'adm_tp_docum.descripcion AS tipo_documento'

        )
        ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
        ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
        ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->Join('administracion.adm_tp_docum', 'adm_tp_docum.id_tp_documento', '=', 'log_ord_compra.id_tp_documento')


        ->whereIn('log_det_ord_compra.id_detalle_orden',[$id_det_orden_list])
        ->get();
        return $ord;
    }

    public function mostrar_doc_detalle($id_doc_det){
        $data = DB::table('almacen.doc_com_det')
            ->select('doc_com_det.*','alm_prod.codigo','alm_prod.descripcion',
            DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as guia"),
            'alm_und_medida.abreviatura')
            ->join('almacen.alm_item','alm_item.id_item','=','doc_com_det.id_item')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
            ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','doc_com_det.id_guia_com_det')
            ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','doc_com_det.id_unid_med')
            ->where([['doc_com_det.id_doc_det','=',$id_doc_det]])
            ->first();
        return response()->json($data);
    }

    public function actualiza_totales_doc($por_dscto, $id_doc, $fecha_emision){
        $detalle = DB::table('almacen.doc_com_det')
        ->select(DB::raw('sum(doc_com_det.precio_total) as sub_total'))
        ->where([['id_doc','=',$id_doc],['estado','=',1]])
        ->first();

        //obtiene IGV
        $impuesto = DB::table('contabilidad.cont_impuesto')
            ->where([['codigo','=','IGV'],['fecha_inicio','<',$fecha_emision]])
            ->orderBy('fecha_inicio','desc')
            ->first();

        $dscto = $por_dscto * $detalle->sub_total / 100;
        $total = $detalle->sub_total - $dscto;
        $igv = $impuesto->porcentaje * $total / 100;

        //actualiza totales
        $data = DB::table('almacen.doc_com')->where('id_doc_com',$id_doc)
        ->update([
            'sub_total'=>$detalle->sub_total,
            'total_descuento'=>$dscto,
            'porcen_descuento'=>$por_dscto,
            'total'=>$total,
            'total_igv'=>$igv,
            'total_ant_igv'=>0,
            'porcen_igv' => $impuesto->porcentaje,
            'porcen_anticipo' => 0,
            'total_otros' => 0,
            'total_a_pagar'=>($total + $igv)
        ]);
        return response()->json($data);
    }

    public function listar_ordenes_sin_comprobante($id_proveedor){
        $estado_elaborado= $this->get_estado_doc('Elaborado');

        $data = DB::table('logistica.log_ord_compra')
        ->select(
            'log_ord_compra.*',
            'adm_estado_doc.estado_doc as des_estado',
            'adm_contri.id_contribuyente',
            'adm_contri.razon_social',
            'adm_contri.nro_documento'
        )
        ->leftJoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'log_ord_compra.estado')
        ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
        ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->where([
                ['log_ord_compra.estado', '=', $estado_elaborado],
                ['log_ord_compra.id_proveedor','=',$id_proveedor],
                ['log_ord_compra.id_tp_documento','=',2]])
        ->orderBy('log_ord_compra.fecha','desc')
        ->get();
    $output['data'] = $data;
    return response()->json($output);
    }

    // 



    public function mostrar_proveedores_cbo()
    {
        $data = DB::table('logistica.log_prove')
            ->select('log_prove.id_proveedor','adm_contri.nro_documento','adm_contri.razon_social')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->where([['log_prove.estado', '=', 1]])
                ->orderBy('adm_contri.nro_documento')
                ->get();
        return $data;
    }

    public function mostrar_guia_clas_cbo()
    {
        $data = DB::table('almacen.guia_clas')
            ->select('guia_clas.id_clasificacion','guia_clas.descripcion')
            ->where([['guia_clas.estado', '=', 1]])
            ->orderBy('guia_clas.id_clasificacion')
            ->get();
        return $data;
    }

    public function mostrar_condiciones_cbo()
    {
        $data = DB::table('logistica.log_cdn_pago')
            ->select('log_cdn_pago.id_condicion_pago','log_cdn_pago.descripcion')
            ->where('log_cdn_pago.estado',1)
            ->orderBy('log_cdn_pago.descripcion')
            ->get();
        return $data;
    }

    public function mostrar_tp_doc_cbo()
    {
        $data = DB::table('contabilidad.cont_tp_doc')
            ->select('cont_tp_doc.id_tp_doc','cont_tp_doc.cod_sunat','cont_tp_doc.descripcion')
            ->where([['cont_tp_doc.estado', '=', 1]])
            ->orderBy('cont_tp_doc.cod_sunat','asc')
            ->get();
        return $data;
    }

    public function mostrar_moneda_cbo()
    {
        $data = DB::table('configuracion.sis_moneda')
            ->select('sis_moneda.id_moneda','sis_moneda.simbolo','sis_moneda.descripcion')
            ->where([['sis_moneda.estado', '=', 1]])
            ->orderBy('sis_moneda.id_moneda')
            ->get();
        return $data;
    }

    public function mostrar_detracciones_cbo()
    {
        $data = DB::table('contabilidad.cont_detra_det')
            ->select('cont_detra_det.id_detra_det','cont_detra.cod_sunat','cont_detra_det.porcentaje','cont_detra.descripcion')
            ->join('contabilidad.cont_detra','cont_detra.id_cont_detra','=','cont_detra_det.id_detra')
            ->where([['cont_detra_det.estado', '=', 1]])
                ->orderBy('cont_detra.descripcion')
                ->get();
        return $data;
    }

    public function mostrar_impuestos_cbo(){
        $data = DB::table('contabilidad.cont_impuesto')
            ->select('cont_impuesto.id_impuesto','cont_impuesto.descripcion',
            'cont_impuesto.porcentaje')
            ->where('cont_impuesto.estado', '=', 1)
            ->get();
        return $data;
    }

    public function select_usuarios(){
        $data = DB::table('configuracion.sis_usua')
            ->select('sis_usua.id_usuario','sis_usua.nombre_corto')
            ->where([['sis_usua.estado', '=', 1],['sis_usua.nombre_corto', '<>', null]])
            ->get();
        return $data;
    }

    public function tp_contribuyente_cbo(){
        $data = DB::table('contabilidad.adm_tp_contri')
            ->select('adm_tp_contri.id_tipo_contribuyente', 'adm_tp_contri.descripcion')
            ->where('adm_tp_contri.estado', '=', 1)
            ->orderBy('adm_tp_contri.descripcion', 'asc')->get();
        return $data;
    }

    public function sis_identidad_cbo(){
        $data = DB::table('contabilidad.sis_identi')
            ->select('sis_identi.id_doc_identidad', 'sis_identi.descripcion')
            ->where('sis_identi.estado', '=', 1)
            ->orderBy('sis_identi.descripcion', 'asc')->get();
        return $data;
    }

    // reportes 
    function view_reporte_productos_comprados()
    {
        $proveedores = $this->select_proveedor();
        $empresas = $this->select_mostrar_empresas();
        return view('logistica/reportes/productos_comprados',compact('proveedores','empresas'));
    }

    function productos_comprados(Request $request){
        $data= $this->get_productos_comprados($request);
        return response()->json($data);
    }

    function get_productos_comprados(Request $request=null, $data=null)
    {
        $output=[];
        $estado_anulado = $this->get_estado_doc('Anulado');
        $hasWhere[]= ['log_ord_compra.estado','!=',$estado_anulado];

        
        if($request == null){
            $id_proveedor = $data['id_proveedor'];
            $id_empresa = $data['id_empresa'];
            $fecha_desde = $data['fecha_desde'];
            $fecha_hasta = $data['fecha_hasta'];
        }else{
            $id_proveedor = $request->id_proveedor;
            $id_empresa = $request->id_empresa;
            $fecha_desde = $request->fecha_desde;
            $fecha_hasta = $request->fecha_hasta;
        }



        if($id_proveedor >0){
            $hasWhere[]= ['log_ord_compra.id_proveedor','=',$id_proveedor];
        }
        if( strlen($fecha_desde) > 0 ){
            $hasWhere[]= ['log_ord_compra.fecha','>=',$fecha_desde];
        }
        if( strlen($fecha_hasta) > 0 ){
            $hasWhere[]= ['log_ord_compra.fecha','<=',$fecha_hasta];
        }
        if( $id_empresa > 0 ){
            $hasWhere[]= ['log_cotizacion.id_empresa','=',$id_empresa];
        }


        $data = DB::table('logistica.log_ord_compra')
        ->select(
            // 'log_ord_compra.*',
            'log_ord_compra.codigo as codigo_orden',
            'log_cotizacion.id_empresa',
            'adm_empresa.logo_empresa',
            'log_ord_compra.fecha',
            'log_ord_compra.id_moneda',
            'log_ord_compra.id_proveedor',
            'adm_contri.razon_social',
            'sis_moneda.descripcion as moneda',
            'log_det_ord_compra.garantia',
            'log_det_ord_compra.lugar_despacho',
            'log_det_ord_compra.descripcion_adicional',
            'alm_item.id_item',
            'alm_item.id_producto',
            'alm_item.id_servicio',
            'alm_item.id_equipo',
            'alm_item.codigo',
            DB::raw("(CASE 
            WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
            WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
            WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
            ELSE 'nulo' END) AS descripcion
            "),
            DB::raw("(CASE 
            WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.descripcion
            WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'Servicio' 
            WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'Equipo' 
            ELSE 'nulo' END) AS unidad_medida_descripcion
            "),
            'log_valorizacion_cotizacion.cantidad_cotizada',
            'log_valorizacion_cotizacion.id_unidad_medida',
            'val_coti_medida.descripcion as unidad_medida_cotizada',
            'log_valorizacion_cotizacion.precio_cotizado',
            'log_valorizacion_cotizacion.igv',
            'log_valorizacion_cotizacion.subtotal',
            'log_valorizacion_cotizacion.precio_sin_igv',
            'log_valorizacion_cotizacion.flete',
            'log_valorizacion_cotizacion.porcentaje_descuento',
            'log_valorizacion_cotizacion.monto_descuento',
            'log_valorizacion_cotizacion.garantia',
            'log_valorizacion_cotizacion.plazo_entrega',
            'log_valorizacion_cotizacion.lugar_despacho'
        )
        ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
        ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_ord_compra.id_cotizacion')
        ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
        ->leftJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_orden_compra', '=', 'log_ord_compra.id_orden_compra')
        ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
        ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', 'log_det_ord_compra.id_valorizacion_cotizacion')
        ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
        ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
        ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
        ->leftJoin('almacen.alm_und_medida as val_coti_medida', 'val_coti_medida.id_unidad_medida', '=', 'log_valorizacion_cotizacion.id_unidad_medida')
        ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
        ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
        ->where($hasWhere)
        ->orderBy('log_ord_compra.id_orden_compra', 'asc')
        ->get();

        foreach($data as $row){
            $output[]=[
                'codigo_orden'=>$row->codigo_orden,
                'id_empresa'=>$row->id_empresa,
                'logo_empresa'=>$row->logo_empresa,
                'fecha'=>$row->fecha,
                'id_moneda'=>$row->id_moneda,
                'moneda'=>$row->moneda,
                'garantia'=>$row->garantia,
                'lugar_despacho'=>$row->lugar_despacho,
                'descripcion_adicional'=>$row->descripcion_adicional,
                'id_item'=>$row->id_item,
                'id_producto'=>$row->id_producto,
                'id_servicio'=>$row->id_servicio,
                'id_equipo'=>$row->id_equipo,
                'codigo'=>$row->codigo,
                'descripcion'=>$row->descripcion,
                'unidad_medida_descripcion'=>$row->unidad_medida_descripcion,
                'cantidad_cotizada'=>$row->cantidad_cotizada,
                'id_unidad_medida'=>$row->id_unidad_medida,
                'unidad_medida_cotizada'=>$row->unidad_medida_cotizada,
                'precio_cotizado'=>$row->precio_cotizado,
                'igv'=>$row->igv,
                'subtotal'=>$row->subtotal,
                'precio_sin_igv'=>$row->precio_sin_igv,
                'flete'=>$row->flete,
                'porcentaje_descuento'=>$row->porcentaje_descuento,
                'monto_descuento'=>$row->monto_descuento,
                'plazo_entrega'=>$row->plazo_entrega,
                'proveedor'=>$row->razon_social,

            ];
        }

        return $output;
    }

    public function generarReporteProductosCompradosExcel($dataArray){
        $spreadsheet = new Spreadsheet();
        // $spreadsheet->getActiveSheet()->getStyle("B6:B8")->getFont()->setBold(true);
        // $spreadsheet->getActiveSheet()->getStyle("H6")->getFont()->setBold(true);
        $sheet = $spreadsheet->getActiveSheet();
        foreach (range('B', $spreadsheet->getActiveSheet()->getHighestDataColumn()) as $col) {
            $spreadsheet->getActiveSheet()
                    ->getColumnDimension($col)
                    ->setAutoSize(true);
            $spreadsheet->getActiveSheet()
                        ->getStyle($col)
                        ->getNumberFormat()
                        // ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                        ->setFormatCode('#');

                        
        } 


        $styleArrayTabelTitle = array(
            'font' => array(
                'color' => array('rgb' => '111112'),
                'bold' =>true
            ),
         
            'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'rotation'   => 0,
                    'wrap'       => true
            ),
            'borders' => array(
                'allBorders' => array(
                      'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, //BORDER_THIN BORDER_MEDIUM BORDER_HAIR
                    'color' => array('rgb' => '808296')
                )
            )
        );

        $styleArrayTabelBody = array(
            'font' => array(
                'color' => array('rgb' => '111112'),
                'bold' =>false
            ),
            'format' => array(
                'color' => array('rgb' => '111112'),
                'bold' =>false
            ),
            'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'rotation'   => 0,
                    'wrap'       => true
            ),
            'borders' => array(
                'allBorders' => array(
                      'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, //BORDER_THIN BORDER_MEDIUM BORDER_HAIR
                    'color' => array('rgb' => '808296')
                )
            )
        );
        $sheet->getStyle("B3")->getFont()->setSize(24);
        $sheet->getStyle("B3")->getFont()->setBold(true);
        $sheet->getStyle("B3")->getAlignment()->setHorizontal('center');
        $title= 'Productos Comprados ';
        $sheet->setCellValue('B3', $title);
        $sheet->mergeCells('B3:N3');
        

        // switch ($dataArray[0]['id_empresa']){
        //     case 1:
        //         $logo_empresa= 'logo_okc.png';
        //     break;
        //     case 2:
        //         $logo_empresa= 'logo_proyectec.png';
        //     break;
        //     case 3:
        //         $logo_empresa= 'logo_smart.png';
        //     break;
        //     default:
        //         $logo_empresa='img-default.jpg';
        // }

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Paid');
        $drawing->setDescription('Paid');
        // $drawing->setPath('images/'.$logo_empresa); // put your path and image here
        $img= explode('/', $dataArray[0]['logo_empresa']);
        $drawing->setPath('images/'.$img[2]); // put your path and image here
        $drawing->setWidthAndHeight(220,80);
        $drawing->setResizeProportional(true);
        $drawing->setCoordinates('B2');
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $spreadsheet->getActiveSheet()->getStyle('B10:S10')->applyFromArray($styleArrayTabelTitle);
        
        $sheet->setCellValueByColumnAndRow(2, 10, 'Proveedor.');
        $sheet->setCellValueByColumnAndRow(3, 10, 'Código.');
        $sheet->setCellValueByColumnAndRow(4, 10, 'Item');
        $sheet->setCellValueByColumnAndRow(5, 10, 'Cantidad');
        $sheet->setCellValueByColumnAndRow(6, 10, 'Und. Medida');
        $sheet->setCellValueByColumnAndRow(7, 10, 'Precio');
        $sheet->setCellValueByColumnAndRow(8, 10, 'IGV');
        $sheet->setCellValueByColumnAndRow(9, 10, 'Precio sin IGV');
        $sheet->setCellValueByColumnAndRow(10, 10, 'Sub-Total');
        $sheet->setCellValueByColumnAndRow(11, 10, 'Flete');
        $sheet->setCellValueByColumnAndRow(12, 10, 'Porcentaje Descuento');
        $sheet->setCellValueByColumnAndRow(13, 10, 'Monto Descuento');
        $sheet->setCellValueByColumnAndRow(14, 10, 'Garantía (mes)');
        $sheet->setCellValueByColumnAndRow(15, 10, 'Lugar Despacho');
        $sheet->setCellValueByColumnAndRow(16, 10, 'Plazo Entrega (días)');
        $sheet->setCellValueByColumnAndRow(17, 10, 'Moneda ');
        $sheet->setCellValueByColumnAndRow(18, 10, 'Documento ');
        $sheet->setCellValueByColumnAndRow(19, 10, 'Fecha ');

        $inicioDataReqX=11;
        $inicioDataReqY=2;
                
        foreach ($dataArray as $row) {
                $proveedor = $row['proveedor'];
                $codigo = $row['codigo'];
                $descripcion = $row['descripcion'];
                $cantidad_cotizada = $row['cantidad_cotizada'];
                $unidad_medida_cotizada = $row['unidad_medida_cotizada'];
                $precio_cotizado = $row['precio_cotizado'];
                $igv = $row['igv'];
                $precio_sin_igv = $row['precio_sin_igv'];
                $subtotal = $row['subtotal'];
                $flete = $row['flete'];
                $porcentaje_descuento = $row['porcentaje_descuento'];
                $monto_descuento = $row['monto_descuento'];
                $garantia = $row['garantia'];
                $lugar_despacho = $row['lugar_despacho'];
                $plazo_entrega = $row['plazo_entrega'];
                $moneda = $row['moneda'];
                $codigo_orden = $row['codigo_orden'];
                $fecha = $row['fecha'];


                $sheet->setCellValueByColumnAndRow($inicioDataReqY+0,$inicioDataReqX , $proveedor);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+1,$inicioDataReqX , $codigo);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+2,$inicioDataReqX , $descripcion);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+3,$inicioDataReqX , $cantidad_cotizada);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+4,$inicioDataReqX , $unidad_medida_cotizada);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+5,$inicioDataReqX , $precio_cotizado);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+6,$inicioDataReqX , $igv);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+7,$inicioDataReqX , $precio_sin_igv);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+8,$inicioDataReqX , $subtotal);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+9,$inicioDataReqX , $flete);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+10,$inicioDataReqX , $porcentaje_descuento);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+11,$inicioDataReqX , $monto_descuento);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+12,$inicioDataReqX , $garantia);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+13,$inicioDataReqX , $lugar_despacho);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+14,$inicioDataReqX , $plazo_entrega);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+15,$inicioDataReqX , $moneda);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+16,$inicioDataReqX , $codigo_orden);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+17,$inicioDataReqX , $fecha);
                $spreadsheet->getActiveSheet()->getStyle('B'.$inicioDataReqX.':S'.$inicioDataReqX)->applyFromArray($styleArrayTabelBody);

                $inicioDataReqX+=1;

            }



        

		$writer = new Xlsx($spreadsheet);
        try {
            $writer->save('./files/logistica/reportes/productos_comprados.xlsx');
			$message = 'File Created';
			$ouput=['status'=>1,'message'=>$message];
            return $ouput;
        }
        catch (Exception $e) {
			$message = 'Unable to save file. Please close any other applications(s) that are using it: [",  $e->getMessage(), "]\n"';
			$ouput=['status'=>-1,'message'=> $message];

            return $ouput;
        }
    }

    public function productos_comprados_excel(Request $request){
        // $dataArray = $this->get_productos_comprados($request->data);
        $data = $this->get_productos_comprados(null,$request->data);
        $file =$this->generarReporteProductosCompradosExcel($data);

        if($file['status']>0){
            $ruta = '/files/logistica/reportes/productos_comprados.xlsx';
        }else{
            $ruta='';
        }

        return ['status'=>$file['status'],'ruta'=>$ruta,'message'=>$file['message']];
    }


    function view_reporte_compras_por_proveedor()
    {
        $proveedores = $this->select_proveedor();
        $empresas = $this->select_mostrar_empresas();
        return view('logistica/reportes/compras_por_proveedor',compact('proveedores','empresas'));
    }

    function get_compras_por_proveedor(Request $request=null, $data=null)
    {
        $listaOrdenes=[];
        $estado_anulado = $this->get_estado_doc('Anulado');
        $hasWhere[]= ['log_ord_compra.estado','!=',$estado_anulado];

        if($request == null){
            $id_proveedor = $data['id_proveedor'];
            $id_empresa = $data['id_empresa'];
            $año = $data['año'];
        }else{
            $id_proveedor = $request->id_proveedor;
            $id_empresa = $request->id_empresa;
            $tipo_periodo = $request->tipo_periodo;
            $año = $request->año;
        }

        if($id_proveedor >0){
            $hasWhere[]= ['log_ord_compra.id_proveedor','=',$id_proveedor];
        }
 
        if( $id_empresa > 0 ){
            $hasWhere[]= ['log_cotizacion.id_empresa','=',$id_empresa];
        }


        $data = DB::table('logistica.log_ord_compra')
        ->select(
            // 'log_ord_compra.*',
            'log_ord_compra.codigo as codigo_orden',
            'log_cotizacion.id_empresa',
            'adm_empresa.logo_empresa',
            'contri_empresa.razon_social as razon_social_empresa',
            'log_ord_compra.fecha',
            'log_ord_compra.id_proveedor',
            'adm_contri.razon_social',
            'adm_contri.nro_documento'
        )
        ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
        ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_ord_compra.id_cotizacion')
        ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
        ->leftJoin('contabilidad.adm_contri as contri_empresa', 'contri_empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
        ->where($hasWhere)
        ->whereYear('log_ord_compra.fecha', '=', $año)
        ->orderBy('log_ord_compra.id_orden_compra', 'asc')
        ->get();


        foreach($data as $row){
            $listaOrdenes[]=[
                'codigo_orden'=>$row->codigo_orden,
                'fecha'=> date('d-m-Y', strtotime($row->fecha)) ,
                'mes'=> date('m', strtotime($row->fecha)) ,
                'año'=> date('Y', strtotime($row->fecha)) ,
                'id_empresa'=>$row->id_empresa,
                'razon_social_empresa'=>$row->razon_social_empresa,
                'logo_empresa'=>$row->logo_empresa,
                'id_proveedor'=>$row->id_proveedor,
                'razon_social'=>$row->razon_social,
                'nro_documento'=>$row->nro_documento
            ];
        }

        
        $formato=[
            'id_proveedor'=>'',
            'razon_social'=>'',
            'nro_documento'=>'',
            'logo_empresa'=>'',
            'cantidad_compras'=>[
                '01'=>0,
                '02'=>0,
                '03'=>0,
                '04'=>0,
                '05'=>0,
                '06'=>0,
                '07'=>0,
                '08'=>0,
                '09'=>0,
                '10'=>0,
                '11'=>0,
                '12'=>0
            ]
        ];
        $temIdProveedor=[];
        $ComprasProveedorList=[];
        foreach($listaOrdenes as $row){
            if(in_array($row['id_proveedor'],$temIdProveedor) == false){
                $temIdProveedor[]= $row['id_proveedor'];
                $formato_copy = $formato;
                $formato_copy['id_proveedor'] = $row['id_proveedor'];
                $formato_copy['razon_social'] = $row['razon_social'];
                $formato_copy['nro_documento'] = $row['nro_documento'];
                $formato_copy['logo_empresa'] = $row['logo_empresa'];
                $ComprasProveedorList[]=$formato_copy;
            }
        }


        foreach($ComprasProveedorList as $claveCP => $rowCP){
            foreach($listaOrdenes as $claveLO => $rowLO){
                if($rowCP['id_proveedor'] == $rowLO['id_proveedor'] ){

                    $reemplazos= array($rowLO['mes'] => 1);
                    $ComprasProveedorList[$claveCP]['cantidad_compras'][$rowLO['mes']] += 1;
                }
            }
        }




        return $ComprasProveedorList;
    }

    public function generarReporteComprasPorProveedorExcel($dataArray){
        $spreadsheet = new Spreadsheet();
        // $spreadsheet->getActiveSheet()->getStyle("B6:B8")->getFont()->setBold(true);
        // $spreadsheet->getActiveSheet()->getStyle("H6")->getFont()->setBold(true);
        $sheet = $spreadsheet->getActiveSheet();
        foreach (range('B', $spreadsheet->getActiveSheet()->getHighestDataColumn()) as $col) {
            $spreadsheet->getActiveSheet()
                    ->getColumnDimension($col)
                    ->setAutoSize(true);
            $spreadsheet->getActiveSheet()
                        ->getStyle($col)
                        ->getNumberFormat()
                        // ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                        ->setFormatCode('#');

                        
        } 


        $styleArrayTabelTitle = array(
            'font' => array(
                'color' => array('rgb' => '111112'),
                'bold' =>true
            ),
         
            'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'rotation'   => 0,
                    'wrap'       => true
            ),
            'borders' => array(
                'allBorders' => array(
                      'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, //BORDER_THIN BORDER_MEDIUM BORDER_HAIR
                    'color' => array('rgb' => '808296')
                )
            )
        );

        $styleArrayTabelBody = array(
            'font' => array(
                'color' => array('rgb' => '111112'),
                'bold' =>false
            ),
            'format' => array(
                'color' => array('rgb' => '111112'),
                'bold' =>false
            ),
            'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'rotation'   => 0,
                    'wrap'       => true
            ),
            'borders' => array(
                'allBorders' => array(
                      'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, //BORDER_THIN BORDER_MEDIUM BORDER_HAIR
                    'color' => array('rgb' => '808296')
                )
            )
        );
        $sheet->getStyle("B3")->getFont()->setSize(24);
        $sheet->getStyle("B3")->getFont()->setBold(true);
        $sheet->getStyle("B3")->getAlignment()->setHorizontal('center');
        $title= 'Reporte Compras Por Proveedor ';
        $sheet->setCellValue('B3', $title);
        $sheet->mergeCells('B3:N3');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Paid');
        $drawing->setDescription('Paid');
        // $drawing->setPath('images/'.$logo_empresa); // put your path and image here
        $img= explode('/', $dataArray[0]['logo_empresa']);
        $drawing->setPath('images/'.$img[2]); // put your path and image here
        $drawing->setWidthAndHeight(220,80);
        $drawing->setResizeProportional(true);
        $drawing->setCoordinates('B2');
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $spreadsheet->getActiveSheet()->getStyle('B10:N10')->applyFromArray($styleArrayTabelTitle);
        
        $sheet->setCellValueByColumnAndRow(2, 10, 'Proveedor.');
        $sheet->setCellValueByColumnAndRow(3, 10, 'Enero');
        $sheet->setCellValueByColumnAndRow(4, 10, 'Febrero');
        $sheet->setCellValueByColumnAndRow(5, 10, 'Marzo');
        $sheet->setCellValueByColumnAndRow(6, 10, 'Abril');
        $sheet->setCellValueByColumnAndRow(7, 10, 'Mayo');
        $sheet->setCellValueByColumnAndRow(8, 10, 'Junio');
        $sheet->setCellValueByColumnAndRow(9, 10, 'Julio');
        $sheet->setCellValueByColumnAndRow(10, 10, 'Agosto');
        $sheet->setCellValueByColumnAndRow(11, 10, 'Septiembre');
        $sheet->setCellValueByColumnAndRow(12, 10, 'Octubre');
        $sheet->setCellValueByColumnAndRow(13, 10, 'Noviembre');
        $sheet->setCellValueByColumnAndRow(14, 10, 'Diciembre');

        $inicioDataReqX=11;
        $inicioDataReqY=2;
                
        foreach ($dataArray as $row) {
                $proveedor = $row['razon_social'].'-'.$row['nro_documento'];
                // foreach ($row['cantidad_compras'] as $rowCantidadCompras) {
                    $enero = $row['cantidad_compras']['01'];
                    $febrero = $row['cantidad_compras']['02'];
                    $marzo = $row['cantidad_compras']['03'];
                    $abril = $row['cantidad_compras']['04'];
                    $mayo = $row['cantidad_compras']['05'];
                    $junio = $row['cantidad_compras']['06'];
                    $julio = $row['cantidad_compras']['07'];
                    $agosto = $row['cantidad_compras']['08'];
                    $septiembre = $row['cantidad_compras']['09'];
                    $octubre = $row['cantidad_compras']['10'];
                    $noviembre = $row['cantidad_compras']['11'];
                    $diciembre = $row['cantidad_compras']['12'];
                // }


                $sheet->setCellValueByColumnAndRow($inicioDataReqY+0,$inicioDataReqX , $proveedor);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+1,$inicioDataReqX , $enero);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+2,$inicioDataReqX , $febrero);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+3,$inicioDataReqX , $marzo);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+4,$inicioDataReqX , $abril);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+5,$inicioDataReqX , $mayo);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+6,$inicioDataReqX , $junio);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+7,$inicioDataReqX , $julio);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+8,$inicioDataReqX , $agosto);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+9,$inicioDataReqX , $septiembre);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+10,$inicioDataReqX , $octubre);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+11,$inicioDataReqX , $noviembre);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+12,$inicioDataReqX , $diciembre);
                $spreadsheet->getActiveSheet()->getStyle('B'.$inicioDataReqX.':N'.$inicioDataReqX)->applyFromArray($styleArrayTabelBody);

                $inicioDataReqX+=1;

            }



        

		$writer = new Xlsx($spreadsheet);
        try {
            $writer->save('./files/logistica/reportes/compras_por_proveedor.xlsx');
			$message = 'File Created';
			$ouput=['status'=>1,'message'=>$message];
            return $ouput;
        }
        catch (Exception $e) {
			$message = 'Unable to save file. Please close any other applications(s) that are using it: [",  $e->getMessage(), "]\n"';
			$ouput=['status'=>-1,'message'=> $message];

            return $ouput;
        }
    }
    
    function compras_por_proveedor_excel(Request $request){
        $data = $this->get_compras_por_proveedor(null,$request->data);
        $file =$this->generarReporteComprasPorProveedorExcel($data);

        if($file['status']>0){
            $ruta = '/files/logistica/reportes/compras_por_proveedor.xlsx';
        }else{
            $ruta='';
        }

        return ['status'=>$file['status'],'ruta'=>$ruta,'message'=>$file['message']];
    }

    function compras_por_proveedor(Request $request){
        $data= $this->get_compras_por_proveedor($request);
        return response()->json($data);
    }



    function view_reporte_compras_por_producto()
    {
        $empresas = $this->select_mostrar_empresas();
        return view('logistica/reportes/compras_por_producto',compact('empresas'));
    }

    function listar_productos(){
        $data = DB::table('almacen.alm_item')
            ->select(
                'alm_item.id_item',
                'alm_item.codigo',
                'alm_item.id_producto',
                DB::raw("(CASE 
                            WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
                            ELSE 'nulo' END) AS descripcion
                            "),
                DB::raw("(CASE 
                            WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.descripcion 
                            ELSE 'nulo' END) AS unidad_medida_descripcion
                            "),

                'alm_prod.id_unidad_medida',
                'alm_prod_ubi.stock'
            )
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('almacen.alm_prod_ubi', 'alm_prod_ubi.id_producto', '=', 'alm_prod.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            // ->where([
            // ['alm_prod_ubi.stock', '>', 0],
            // ['alm_prod_ubi.estado', '=', 1]
            // ])
            // ->limit(500)
            ->get();
        return response()->json(["data" => $data]);
    }


    function get_compras_por_producto(Request $request=null, $data=null)
    {
        $listaOrdenes=[];
        $estado_anulado = $this->get_estado_doc('Anulado');
        $hasWhere[]= ['log_ord_compra.estado','!=',$estado_anulado];

        if($request == null){
            $id_producto = $data['id_producto']?$data['id_producto']:0;
            $id_empresa = $data['id_empresa'];
            $año = $data['año'];
        }else{
            $id_producto = $request->id_producto;
            $id_empresa = $request->id_empresa;
            $tipo_periodo = $request->tipo_periodo;
            $año = $request->año;
        }



        if($id_producto >0){
            $hasWhere[]= ['alm_item.id_producto','=',$id_producto];
        }
 
        if( $id_empresa > 0 ){
            $hasWhere[]= ['log_cotizacion.id_empresa','=',$id_empresa];
        }


        $data = DB::table('logistica.log_ord_compra')
        ->select(
            'alm_item.id_item',
            'alm_item.id_producto',
            'alm_item.codigo as codigo_item',
            'alm_prod.descripcion as descripcion_producto',
            'log_ord_compra.codigo as codigo_orden',
            'log_cotizacion.id_empresa',
            'adm_empresa.logo_empresa',
            'contri_empresa.razon_social as razon_social_empresa',
            'log_ord_compra.fecha',
            'log_ord_compra.id_proveedor',
            'adm_contri.razon_social',
            'adm_contri.nro_documento'
        )
        ->leftJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_orden_compra', '=', 'log_ord_compra.id_orden_compra')
        ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
        ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
        ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
        ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_ord_compra.id_cotizacion')
        ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
        ->leftJoin('contabilidad.adm_contri as contri_empresa', 'contri_empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
        ->where($hasWhere)
        ->whereYear('log_ord_compra.fecha', '=', $año)
        ->orderBy('log_ord_compra.id_orden_compra', 'asc')
        ->get();


        foreach($data as $row){
            $listaOrdenes[]=[
                'codigo_orden'=>$row->codigo_orden,
                'fecha'=> date('d-m-Y', strtotime($row->fecha)) ,
                'mes'=> date('m', strtotime($row->fecha)) ,
                'año'=> date('Y', strtotime($row->fecha)) ,
                'id_empresa'=>$row->id_empresa,
                'razon_social_empresa'=>$row->razon_social_empresa,
                'logo_empresa'=>$row->logo_empresa,
                'id_proveedor'=>$row->id_proveedor,
                'razon_social'=>$row->razon_social,
                'nro_documento'=>$row->nro_documento,
                'id_producto'=>$row->id_producto,
                'codigo_item'=>$row->codigo_item,
                'descripcion_producto'=>$row->descripcion_producto
            ];
        }

        
        $formato=[
            'id_producto'=>'',
            'descripcion_producto'=>'',
            'razon_social'=>'',
            'nro_documento'=>'',
            'logo_empresa'=>'',
            'cantidad_compras'=>[
                '01'=>0,
                '02'=>0,
                '03'=>0,
                '04'=>0,
                '05'=>0,
                '06'=>0,
                '07'=>0,
                '08'=>0,
                '09'=>0,
                '10'=>0,
                '11'=>0,
                '12'=>0
            ]
        ];
        $temIdProducto=[];
        $ComprasProductoList=[];
        foreach($listaOrdenes as $row){
            if(in_array($row['id_producto'],$temIdProducto) == false){
                $temIdProducto[]= $row['id_producto'];
                $formato_copy = $formato;
                $formato_copy['id_producto'] = $row['id_producto'];
                $formato_copy['descripcion_producto'] = $row['descripcion_producto'];
                $formato_copy['razon_social'] = $row['razon_social'];
                $formato_copy['nro_documento'] = $row['nro_documento'];
                $formato_copy['logo_empresa'] = $row['logo_empresa'];
                $ComprasProductoList[]=$formato_copy;
            }
        }


        foreach($ComprasProductoList as $claveCP => $rowCP){
            foreach($listaOrdenes as $claveLO => $rowLO){
                if($rowCP['id_producto'] == $rowLO['id_producto'] ){

                    $reemplazos= array($rowLO['mes'] => 1);
                    $ComprasProductoList[$claveCP]['cantidad_compras'][$rowLO['mes']] += 1;
                }
            }
        }

        return $ComprasProductoList;
    }

    function compras_por_producto(Request $request){
        $data= $this->get_compras_por_producto($request);
        return response()->json($data);
    }


    public function generarReporteComprasPorProductoExcel($dataArray){
        $spreadsheet = new Spreadsheet();
        // $spreadsheet->getActiveSheet()->getStyle("B6:B8")->getFont()->setBold(true);
        // $spreadsheet->getActiveSheet()->getStyle("H6")->getFont()->setBold(true);
        $sheet = $spreadsheet->getActiveSheet();
        foreach (range('B', $spreadsheet->getActiveSheet()->getHighestDataColumn()) as $col) {
            $spreadsheet->getActiveSheet()
                    ->getColumnDimension($col)
                    ->setAutoSize(true);
            $spreadsheet->getActiveSheet()
                        ->getStyle($col)
                        ->getNumberFormat()
                        // ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                        ->setFormatCode('#');

                        
        } 


        $styleArrayTabelTitle = array(
            'font' => array(
                'color' => array('rgb' => '111112'),
                'bold' =>true
            ),
         
            'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'rotation'   => 0,
                    'wrap'       => true
            ),
            'borders' => array(
                'allBorders' => array(
                      'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, //BORDER_THIN BORDER_MEDIUM BORDER_HAIR
                    'color' => array('rgb' => '808296')
                )
            )
        );

        $styleArrayTabelBody = array(
            'font' => array(
                'color' => array('rgb' => '111112'),
                'bold' =>false
            ),
            'format' => array(
                'color' => array('rgb' => '111112'),
                'bold' =>false
            ),
            'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'rotation'   => 0,
                    'wrap'       => true
            ),
            'borders' => array(
                'allBorders' => array(
                      'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, //BORDER_THIN BORDER_MEDIUM BORDER_HAIR
                    'color' => array('rgb' => '808296')
                )
            )
        );
        $sheet->getStyle("B3")->getFont()->setSize(24);
        $sheet->getStyle("B3")->getFont()->setBold(true);
        $sheet->getStyle("B3")->getAlignment()->setHorizontal('center');
        $title= 'Reporte Compras Por Producto ';
        $sheet->setCellValue('B3', $title);
        $sheet->mergeCells('B3:N3');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Paid');
        $drawing->setDescription('Paid');
        // $drawing->setPath('images/'.$logo_empresa); // put your path and image here
        $img= explode('/', $dataArray[0]['logo_empresa']);
        $drawing->setPath('images/'.$img[2]); // put your path and image here
        $drawing->setWidthAndHeight(220,80);
        $drawing->setResizeProportional(true);
        $drawing->setCoordinates('B2');
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $spreadsheet->getActiveSheet()->getStyle('B10:N10')->applyFromArray($styleArrayTabelTitle);
        
        $sheet->setCellValueByColumnAndRow(2, 10, 'Producto.');
        $sheet->setCellValueByColumnAndRow(3, 10, 'Enero');
        $sheet->setCellValueByColumnAndRow(4, 10, 'Febrero');
        $sheet->setCellValueByColumnAndRow(5, 10, 'Marzo');
        $sheet->setCellValueByColumnAndRow(6, 10, 'Abril');
        $sheet->setCellValueByColumnAndRow(7, 10, 'Mayo');
        $sheet->setCellValueByColumnAndRow(8, 10, 'Junio');
        $sheet->setCellValueByColumnAndRow(9, 10, 'Julio');
        $sheet->setCellValueByColumnAndRow(10, 10, 'Agosto');
        $sheet->setCellValueByColumnAndRow(11, 10, 'Septiembre');
        $sheet->setCellValueByColumnAndRow(12, 10, 'Octubre');
        $sheet->setCellValueByColumnAndRow(13, 10, 'Noviembre');
        $sheet->setCellValueByColumnAndRow(14, 10, 'Diciembre');

        $inicioDataReqX=11;
        $inicioDataReqY=2;
                
        foreach ($dataArray as $row) {
                $producto = $row['descripcion_producto'];
                // foreach ($row['cantidad_compras'] as $rowCantidadCompras) {
                    $enero = $row['cantidad_compras']['01'];
                    $febrero = $row['cantidad_compras']['02'];
                    $marzo = $row['cantidad_compras']['03'];
                    $abril = $row['cantidad_compras']['04'];
                    $mayo = $row['cantidad_compras']['05'];
                    $junio = $row['cantidad_compras']['06'];
                    $julio = $row['cantidad_compras']['07'];
                    $agosto = $row['cantidad_compras']['08'];
                    $septiembre = $row['cantidad_compras']['09'];
                    $octubre = $row['cantidad_compras']['10'];
                    $noviembre = $row['cantidad_compras']['11'];
                    $diciembre = $row['cantidad_compras']['12'];
                // }


                $sheet->setCellValueByColumnAndRow($inicioDataReqY+0,$inicioDataReqX , $producto);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+1,$inicioDataReqX , $enero);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+2,$inicioDataReqX , $febrero);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+3,$inicioDataReqX , $marzo);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+4,$inicioDataReqX , $abril);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+5,$inicioDataReqX , $mayo);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+6,$inicioDataReqX , $junio);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+7,$inicioDataReqX , $julio);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+8,$inicioDataReqX , $agosto);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+9,$inicioDataReqX , $septiembre);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+10,$inicioDataReqX , $octubre);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+11,$inicioDataReqX , $noviembre);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+12,$inicioDataReqX , $diciembre);
                $spreadsheet->getActiveSheet()->getStyle('B'.$inicioDataReqX.':N'.$inicioDataReqX)->applyFromArray($styleArrayTabelBody);

                $inicioDataReqX+=1;

            }



        

		$writer = new Xlsx($spreadsheet);
        try {
            $writer->save('./files/logistica/reportes/compras_por_producto.xlsx');
			$message = 'File Created';
			$ouput=['status'=>1,'message'=>$message];
            return $ouput;
        }
        catch (Exception $e) {
			$message = 'Unable to save file. Please close any other applications(s) that are using it: [",  $e->getMessage(), "]\n"';
			$ouput=['status'=>-1,'message'=> $message];

            return $ouput;
        }
    }
    
    function compras_por_producto_excel(Request $request){
        $data = $this->get_compras_por_producto(null,$request->data);
        $file =$this->generarReporteComprasPorProductoExcel($data);

        if($file['status']>0){
            $ruta = '/files/logistica/reportes/compras_por_producto.xlsx';
        }else{
            $ruta='';
        }

        return ['status'=>$file['status'],'ruta'=>$ruta,'message'=>$file['message']];
    }


function view_reporte_mejores_proveedores()
{
    $empresas = $this->select_mostrar_empresas();
    return view('logistica/reportes/mejores_proveedores',compact('empresas'));
}

function get_operador($condicion){
    switch ($condicion) {
        case 'igual':
            # code...
            return '=';
        break;
        case 'menor':
            # code...
            return '<';
        break;
        case 'menor_igual':
            # code...
            return '<=';
        break;
        case 'mayor':
            # code...
            return '>';
        break;
        case 'mayor_igual':
            # code...
            return '>=';
            break;
        
        default:
            # code...
            break;
    }
}

function get_mejores_proveedores(Request $request=null, $data=null){

    $listaOrdenes=[];
    $estado_anulado = $this->get_estado_doc('Anulado');
    $hasWhere[]= ['log_ord_compra.estado','!=',$estado_anulado];
    if($request==null){
        $id_producto = $data['id_producto']?$data['id_producto']:0;
        $id_empresa = $data['id_empresa'];
        $tipo_periodo = $data['tipo_periodo'];
        $año = $data['año'];
        $condicion_precio = $data['condicion_precio'];
        $precio = $data['precio'];
        $condicion_garantia = $data['condicion_garantia'];
        $garantia = $data['garantia'];
        $condicion_tiempo_entrega = $data['condicion_tiempo_entrega'];
        $tiempo_entrega = $data['tiempo_entrega'];
        $optionMejorPrecio = $data['optionMejorPrecio'];
    }else{
        $id_producto = $request->id_producto;
        $id_empresa = $request->id_empresa;
        $tipo_periodo = $request->tipo_periodo;
        $año = $request->año;
        $condicion_precio = $request->condicion_precio;
        $precio = $request->precio;
        $condicion_garantia = $request->condicion_garantia;
        $garantia = $request->garantia;
        $condicion_tiempo_entrega = $request->condicion_tiempo_entrega;
        $tiempo_entrega = $request->tiempo_entrega;
        $optionMejorPrecio = $request->optionMejorPrecio;
    }
 

    if($id_producto >0){
        $hasWhere[]= ['alm_item.id_producto','=',$id_producto];
    }

    if( $id_empresa > 0 ){
        $hasWhere[]= ['log_cotizacion.id_empresa','=',$id_empresa];
    }

    if($optionMejorPrecio == false){

        if( $precio > 0 ){
            $operador = $this->get_operador($condicion_precio);
            $hasWhere[]= ['log_valorizacion_cotizacion.precio_cotizado',$operador ,$precio];
        }
        if( $garantia > 0 ){
            $operador = $this->get_operador($condicion_garantia);
            $hasWhere[]= ['log_valorizacion_cotizacion.precio_cotizado',$operador ,$garantia];
        }
        if( $tiempo_entrega > 0 ){
            $operador = $this->get_operador($condicion_tiempo_entrega);
            $hasWhere[]= ['log_valorizacion_cotizacion.precio_cotizado',$operador ,$tiempo_entrega];
        }
    
    }


    $data = DB::table('logistica.log_ord_compra')
    ->select(
        'alm_item.id_item',
        'alm_item.id_producto',
        'alm_item.codigo as codigo_item',
        'alm_prod.descripcion as descripcion_producto',
        'log_valorizacion_cotizacion.cantidad_cotizada',
        'log_valorizacion_cotizacion.id_unidad_medida',
        'alm_und_medida.descripcion as unidad_medida_cotizada',
        'log_valorizacion_cotizacion.precio_cotizado',
        'log_valorizacion_cotizacion.flete',
        'log_valorizacion_cotizacion.plazo_entrega',
        'log_valorizacion_cotizacion.garantia',
        'log_ord_compra.codigo as codigo_orden',
        'log_cotizacion.id_empresa',
        'adm_empresa.logo_empresa',
        'contri_empresa.razon_social as razon_social_empresa',
        'log_ord_compra.fecha',
        'log_ord_compra.id_proveedor',
        'adm_contri.razon_social',
        'adm_contri.nro_documento'
    )
    ->leftJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_orden_compra', '=', 'log_ord_compra.id_orden_compra')
    ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', 'log_det_ord_compra.id_valorizacion_cotizacion')
    ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_valorizacion_cotizacion.id_unidad_medida')
    ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
    ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
    ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
    ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
    ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_ord_compra.id_cotizacion')
    ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
    ->leftJoin('contabilidad.adm_contri as contri_empresa', 'contri_empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
    ->where($hasWhere)
    ->whereYear('log_ord_compra.fecha', '=', $año)
    ->orderBy('log_valorizacion_cotizacion.precio_cotizado', 'asc')

    ->get();


    foreach($data as $row){
        $listaOrdenes[]=[
            'codigo_orden'=>$row->codigo_orden,
            'fecha'=> date('d-m-Y', strtotime($row->fecha)) ,
            'mes'=> date('m', strtotime($row->fecha)) ,
            'año'=> date('Y', strtotime($row->fecha)) ,
            'id_empresa'=>$row->id_empresa,
            'razon_social_empresa'=>$row->razon_social_empresa,
            'logo_empresa'=>$row->logo_empresa,
            'id_proveedor'=>$row->id_proveedor,
            'razon_social'=>$row->razon_social,
            'nro_documento'=>$row->nro_documento,
            'id_producto'=>$row->id_producto,
            'codigo_item'=>$row->codigo_item,
            'descripcion_producto'=>$row->descripcion_producto,
            'cantidad_cotizada'=>$row->cantidad_cotizada,
            'id_unidad_medida'=>$row->id_unidad_medida,
            'unidad_medida_cotizada'=>$row->unidad_medida_cotizada,
            'precio_cotizado'=>$row->precio_cotizado,
            'flete'=>$row->flete,
            'garantia'=>$row->garantia,
            'plazo_entrega'=>$row->plazo_entrega
        ];
    }

    
    $formato=[
        'id_producto'=>'',
        'descripcion_producto'=>'',
        // 'razon_social'=>'',
        // 'nro_documento'=>'',
        'logo_empresa'=>'',
        'proveedor'=>[]

    ];
    $temIdProducto=[];
    $ComprasProductoList=[];
    $comprasList=[];
    $proveedorList=[];
    $temIdProveedor=[];
    foreach($listaOrdenes as $key => $row){ //todas las compras

        if(in_array($row['id_proveedor'],$temIdProveedor) == false){ 
            $temIdProveedor[]= $row['id_proveedor'];//no se repiten los proveedor
            $id_proveedor=$row['id_proveedor'];
            $razon_social=$row['razon_social'];
            $nro_documento=$row['nro_documento'];
            $proveedorList[] = [
                'id_proveedor'=>$id_proveedor,
                'razon_social'=>$razon_social,
                'nro_documento'=>$nro_documento, 
                'compras'=>[] 
            ];
        }
        
        // toda la lista de compras
        $comprasList[$key]['id_proveedor'] = $row['id_proveedor'];
        $comprasList[$key]['id_producto'] = $row['id_producto'];
        $comprasList[$key]['cantidad_cotizada'] = $row['cantidad_cotizada'];
        $comprasList[$key]['unidad_medida_cotizada'] = $row['unidad_medida_cotizada'];
        $comprasList[$key]['precio_cotizado'] = $row['precio_cotizado'];
        $comprasList[$key]['flete'] = $row['flete']?$row['flete']:"";
        $comprasList[$key]['garantia'] = $row['garantia'];
        $comprasList[$key]['plazo_entrega'] = $row['plazo_entrega'];
        $comprasList[$key]['razon_social'] = $row['razon_social'];
        $comprasList[$key]['nro_documento'] = $row['nro_documento'];

        if(in_array($row['id_producto'],$temIdProducto) == false){  
            $temIdProducto[]= $row['id_producto'];//no se repiten los productos
            $formato_copy = $formato;
            $formato_copy['id_producto'] = $row['id_producto'];
            $formato_copy['descripcion_producto'] = $row['descripcion_producto'];
            $formato_copy['descripcion_producto'] = $row['descripcion_producto'];
            $formato_copy['logo_empresa'] = $row['logo_empresa'];
            $ComprasProductoList[]=$formato_copy;
        }
    }

// http://localhost:3000/logistica/mejores_proveedores?id_producto=&id_empresa=1&tipo_periodo=MENSUAL&a%C3%B1o=2020
    foreach($ComprasProductoList as $claveCP => $rowCP){
        $ComprasProductoList[$claveCP]['proveedor']=$proveedorList;
    }


     foreach($ComprasProductoList as $claveCP => $rowCP){
        foreach($comprasList as $claveCL => $rowCL){
            if( $ComprasProductoList[$claveCP]['id_producto'] == $rowCL['id_producto'] ){
                foreach($ComprasProductoList[$claveCP]['proveedor'] as $claveProv => $prov){
                     if($ComprasProductoList[$claveCP]['proveedor'][$claveProv]['id_proveedor'] == $rowCL['id_proveedor'] ){
                        $ComprasProductoList[$claveCP]['proveedor'][$claveProv]['compras'][]= $rowCL;
                    }
                }
            }
        }
    }


    return $ComprasProductoList;
}

function mejores_proveedores(Request $request){
    $data= $this->get_mejores_proveedores($request);
    return response()->json($data);
}

public function generarReporteMejoresProveedoresExcel($dataArray){

    $spreadsheet = new Spreadsheet();
    // $spreadsheet->getActiveSheet()->getStyle("B6:B8")->getFont()->setBold(true);
    // $spreadsheet->getActiveSheet()->getStyle("H6")->getFont()->setBold(true);
    $sheet = $spreadsheet->getActiveSheet();
    foreach (range('B', $spreadsheet->getActiveSheet()->getHighestDataColumn()) as $col) {
        $spreadsheet->getActiveSheet()
                ->getColumnDimension($col)
                ->setAutoSize(true);
        $spreadsheet->getActiveSheet()
                    ->getStyle($col)
                    ->getNumberFormat()
                    // ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    ->setFormatCode('#');

                    
    } 


    $styleArrayTabelTitle = array(
        'font' => array(
            'color' => array('rgb' => '111112'),
            'bold' =>true
        ),
     
        'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'rotation'   => 0,
                'wrap'       => true
        ),
        'borders' => array(
            'allBorders' => array(
                  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, //BORDER_THIN BORDER_MEDIUM BORDER_HAIR
                'color' => array('rgb' => '808296')
            )
        )
    );

    $styleArrayTabelBody = array(
        'font' => array(
            'color' => array('rgb' => '111112'),
            'bold' =>false
        ),
        'format' => array(
            'color' => array('rgb' => '111112'),
            'bold' =>false
        ),
        'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'rotation'   => 0,
                'wrap'       => true
        ),
        'borders' => array(
            'allBorders' => array(
                  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, //BORDER_THIN BORDER_MEDIUM BORDER_HAIR
                'color' => array('rgb' => '808296')
            )
        )
    );
    $sheet->getStyle("B3")->getFont()->setSize(24);
    $sheet->getStyle("B3")->getFont()->setBold(true);
    $sheet->getStyle("B3")->getAlignment()->setHorizontal('center');
    $title= 'Reporte Mejores Proveedores';
    $sheet->setCellValue('B3', $title);
    $sheet->mergeCells('B3:N3');

    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing->setName('Paid');
    $drawing->setDescription('Paid');
    $img= explode('/', $dataArray[0]['logo_empresa']);
    $drawing->setPath('images/'.$img[2]); // put your path and image here
    $drawing->setWidthAndHeight(220,80);
    $drawing->setResizeProportional(true);
    $drawing->setCoordinates('B2');
    $drawing->setWorksheet($spreadsheet->getActiveSheet());

   
    
    $sheet->setCellValueByColumnAndRow(3, 9, 'Proveedor 1');
    $sheet->mergeCells('C9:G9');
    $sheet->setCellValueByColumnAndRow(8, 9, 'Proveedor 2');
    $sheet->mergeCells('H9:L9');
    $sheet->setCellValueByColumnAndRow(13, 9, 'Proveedor 3');
    $sheet->mergeCells('M9:Q9');
    $sheet->setCellValueByColumnAndRow(18, 9, 'Proveedor 4');
    $sheet->mergeCells('R9:V9');
    $sheet->setCellValueByColumnAndRow(23, 9, 'Proveedor 5');
    $sheet->mergeCells('W9:AA9');
    


 



  
     $inicioDataReqX=11;
    $inicioDataReqY=2;
             
    foreach ($dataArray as $row) {
                $sheet->setCellValueByColumnAndRow(2,$inicioDataReqX , $row['descripcion_producto']);
                if(count($row['proveedor'])>=1){
                    $sheet->setCellValueByColumnAndRow(2, 10, 'Producto.');
                    $sheet->setCellValueByColumnAndRow(3, 10, 'Unidad');
                    $sheet->setCellValueByColumnAndRow(4, 10, 'Precio');
                    $sheet->setCellValueByColumnAndRow(5, 10, 'Garantia');
                    $sheet->setCellValueByColumnAndRow(6, 10, 'Plazo Entrega(días)');
                    $sheet->setCellValueByColumnAndRow(7, 10, 'Flete');

                    $spreadsheet->getActiveSheet()->getStyle('B'.$inicioDataReqX.':G'.$inicioDataReqX)->applyFromArray($styleArrayTabelBody);
                    $spreadsheet->getActiveSheet()->getStyle('B10:G10')->applyFromArray($styleArrayTabelTitle);
                    $spreadsheet->getActiveSheet()->getStyle('C9:G9')->applyFromArray($styleArrayTabelBody);
                    $sheet->getStyle("C9:G9")->getFont()->setBold(true);

                }
                $sheet->setCellValueByColumnAndRow(3,9 ,  count($row['proveedor'])>=1?$row['proveedor'][0]['razon_social']:"");
                $sheet->setCellValueByColumnAndRow(3,$inicioDataReqX , count($row['proveedor'])>=1  && count($row['proveedor'][0]['compras'])>0 ?$row['proveedor'][0]['compras'][0]['unidad_medida_cotizada']:"");
                $sheet->setCellValueByColumnAndRow(4,$inicioDataReqX , count($row['proveedor'])>=1  && count($row['proveedor'][0]['compras'])>0 ?$row['proveedor'][0]['compras'][0]['precio_cotizado']:"");
                $sheet->setCellValueByColumnAndRow(5,$inicioDataReqX , count($row['proveedor'])>=1  && count($row['proveedor'][0]['compras'])>0 ?$row['proveedor'][0]['compras'][0]['garantia']:"");
                $sheet->setCellValueByColumnAndRow(6,$inicioDataReqX , count($row['proveedor'])>=1  && count($row['proveedor'][0]['compras'])>0 ?$row['proveedor'][0]['compras'][0]['plazo_entrega']:"");
                $sheet->setCellValueByColumnAndRow(7,$inicioDataReqX , count($row['proveedor'])>=1  && count($row['proveedor'][0]['compras'])>0 ?$row['proveedor'][0]['compras'][0]['flete']:"");

                if(count($row['proveedor'])>=2){   
                    $sheet->setCellValueByColumnAndRow(8, 10, 'Unidad');
                    $sheet->setCellValueByColumnAndRow(9, 10, 'Precio');
                    $sheet->setCellValueByColumnAndRow(10, 10, 'Garantia');
                    $sheet->setCellValueByColumnAndRow(11, 10, 'Plazo Entrega(días)');
                    $sheet->setCellValueByColumnAndRow(12, 10, 'Flete');

                    $spreadsheet->getActiveSheet()->getStyle('B'.$inicioDataReqX.':L'.$inicioDataReqX)->applyFromArray($styleArrayTabelBody);
                    $spreadsheet->getActiveSheet()->getStyle('B10:L10')->applyFromArray($styleArrayTabelTitle);
                    $spreadsheet->getActiveSheet()->getStyle('C9:L9')->applyFromArray($styleArrayTabelBody);
                    $sheet->getStyle("C9:L9")->getFont()->setBold(true);
                }

                $sheet->setCellValueByColumnAndRow(8,9 ,  count($row['proveedor'])>=2?$row['proveedor'][1]['razon_social']:"");
                $sheet->setCellValueByColumnAndRow(8,$inicioDataReqX , count($row['proveedor'])>=2  && count($row['proveedor'][1]['compras'])>0 ?$row['proveedor'][1]['compras'][0]['unidad_medida_cotizada']:"");
                $sheet->setCellValueByColumnAndRow(9,$inicioDataReqX , count($row['proveedor'])>=2  && count($row['proveedor'][1]['compras'])>0 ?$row['proveedor'][1]['compras'][0]['precio_cotizado']:"");
                $sheet->setCellValueByColumnAndRow(10,$inicioDataReqX ,count($row['proveedor'])>=2  && count($row['proveedor'][1]['compras'])>0 ?$row['proveedor'][1]['compras'][0]['garantia']:"");
                $sheet->setCellValueByColumnAndRow(11,$inicioDataReqX ,count($row['proveedor'])>=2  && count($row['proveedor'][1]['compras'])>0 ?$row['proveedor'][1]['compras'][0]['plazo_entrega']:"");
                $sheet->setCellValueByColumnAndRow(12,$inicioDataReqX ,count($row['proveedor'])>=2  && count($row['proveedor'][1]['compras'])>0 ?$row['proveedor'][1]['compras'][0]['flete']:"");

                if(count($row['proveedor'])>=3){

                    $sheet->setCellValueByColumnAndRow(13, 10, 'Unidad');
                    $sheet->setCellValueByColumnAndRow(14, 10, 'Precio');
                    $sheet->setCellValueByColumnAndRow(15, 10, 'Garantia');
                    $sheet->setCellValueByColumnAndRow(16, 10, 'Plazo Entrega(días)');
                    $sheet->setCellValueByColumnAndRow(17, 10, 'Flete');

                    $spreadsheet->getActiveSheet()->getStyle('B'.$inicioDataReqX.':Q'.$inicioDataReqX)->applyFromArray($styleArrayTabelBody);
                    $spreadsheet->getActiveSheet()->getStyle('B10:Q10')->applyFromArray($styleArrayTabelTitle);
                    $spreadsheet->getActiveSheet()->getStyle('C9:Q9')->applyFromArray($styleArrayTabelBody);
                    $sheet->getStyle("C9:Q9")->getFont()->setBold(true);

                }

                
                $sheet->setCellValueByColumnAndRow(13,9 , count($row['proveedor'])>=3?$row['proveedor'][2]['razon_social']:"");
                
                $sheet->setCellValueByColumnAndRow(13,$inicioDataReqX , count($row['proveedor'])>=3  && count($row['proveedor'][2]['compras'])>0 ?$row['proveedor'][2]['compras'][0]['unidad_medida_cotizada']:"");
                $sheet->setCellValueByColumnAndRow(14,$inicioDataReqX , count($row['proveedor'])>=3  && count($row['proveedor'][2]['compras'])>0 ?$row['proveedor'][2]['compras'][0]['precio_cotizado']:"");
                $sheet->setCellValueByColumnAndRow(15,$inicioDataReqX , count($row['proveedor'])>=3  && count($row['proveedor'][2]['compras'])>0 ?$row['proveedor'][2]['compras'][0]['garantia']:"");
                $sheet->setCellValueByColumnAndRow(16,$inicioDataReqX , count($row['proveedor'])>=3  && count($row['proveedor'][2]['compras'])>0 ?$row['proveedor'][2]['compras'][0]['plazo_entrega']:"");
                $sheet->setCellValueByColumnAndRow(17,$inicioDataReqX , count($row['proveedor'])>=3  && count($row['proveedor'][2]['compras'])>0 ?$row['proveedor'][2]['compras'][0]['flete']:"");

                if(count($row['proveedor'])>=4){
 
                    $sheet->setCellValueByColumnAndRow(18, 10, 'Unidad');
                    $sheet->setCellValueByColumnAndRow(19, 10, 'Precio');
                    $sheet->setCellValueByColumnAndRow(20, 10, 'Garantia');
                    $sheet->setCellValueByColumnAndRow(21, 10, 'Plazo Entrega(días)');
                    $sheet->setCellValueByColumnAndRow(22, 10, 'Flete');

                    $spreadsheet->getActiveSheet()->getStyle('B'.$inicioDataReqX.':V'.$inicioDataReqX)->applyFromArray($styleArrayTabelBody);
                    $spreadsheet->getActiveSheet()->getStyle('B10:V10')->applyFromArray($styleArrayTabelTitle);
                    $spreadsheet->getActiveSheet()->getStyle('C9:V9')->applyFromArray($styleArrayTabelBody);
                    $sheet->getStyle("C9:V9")->getFont()->setBold(true);

                }

                $sheet->setCellValueByColumnAndRow(18,9 , count($row['proveedor'])>=4?$row['proveedor'][3]['razon_social']:"");
                $sheet->setCellValueByColumnAndRow(18,$inicioDataReqX ,  count($row['proveedor'])>=4  && count($row['proveedor'][3]['compras'])>0 ?$row['proveedor'][3]['compras'][0]['unidad_medida_cotizada']:"");
                $sheet->setCellValueByColumnAndRow(19,$inicioDataReqX ,  count($row['proveedor'])>=4  && count($row['proveedor'][3]['compras'])>0 ?$row['proveedor'][3]['compras'][0]['precio_cotizado']:"");
                $sheet->setCellValueByColumnAndRow(20,$inicioDataReqX ,  count($row['proveedor'])>=4  && count($row['proveedor'][3]['compras'])>0 ?$row['proveedor'][3]['compras'][0]['garantia']:"");
                $sheet->setCellValueByColumnAndRow(21,$inicioDataReqX ,  count($row['proveedor'])>=4  && count($row['proveedor'][3]['compras'])>0 ?$row['proveedor'][3]['compras'][0]['plazo_entrega']:"");
                $sheet->setCellValueByColumnAndRow(22,$inicioDataReqX ,  count($row['proveedor'])>=4  && count($row['proveedor'][3]['compras'])>0 ?$row['proveedor'][3]['compras'][0]['flete']:"");

                if(count($row['proveedor'])>=5){
                    $sheet->setCellValueByColumnAndRow(23, 10, 'Unidad');
                    $sheet->setCellValueByColumnAndRow(24, 10, 'Precio');
                    $sheet->setCellValueByColumnAndRow(25, 10, 'Garantia');
                    $sheet->setCellValueByColumnAndRow(26, 10, 'Plazo Entrega(días)');
                    $sheet->setCellValueByColumnAndRow(27, 10, 'Flete');
                    $spreadsheet->getActiveSheet()->getStyle('B'.$inicioDataReqX.':AA'.$inicioDataReqX)->applyFromArray($styleArrayTabelBody);
                    $spreadsheet->getActiveSheet()->getStyle('B10:AA10')->applyFromArray($styleArrayTabelTitle);
                    $spreadsheet->getActiveSheet()->getStyle('C9:AA9')->applyFromArray($styleArrayTabelBody);
                    $sheet->getStyle("C9:AA9")->getFont()->setBold(true);

                }
                $sheet->setCellValueByColumnAndRow(23,9 ,count($row['proveedor'])>=5?$row['proveedor'][4]['razon_social']:"");
                $sheet->setCellValueByColumnAndRow(23,$inicioDataReqX , count($row['proveedor'])>=5  && count($row['proveedor'][4]['compras'])>0 ?$row['proveedor'][4]['compras'][0]['unidad_medida_cotizada']:"");
                $sheet->setCellValueByColumnAndRow(24,$inicioDataReqX , count($row['proveedor'])>=5  && count($row['proveedor'][4]['compras'])>0 ?$row['proveedor'][4]['compras'][0]['precio_cotizado']:"");
                $sheet->setCellValueByColumnAndRow(25,$inicioDataReqX , count($row['proveedor'])>=5  && count($row['proveedor'][4]['compras'])>0 ?$row['proveedor'][4]['compras'][0]['garantia']:"");
                $sheet->setCellValueByColumnAndRow(26,$inicioDataReqX , count($row['proveedor'])>=5  && count($row['proveedor'][4]['compras'])>0 ?$row['proveedor'][4]['compras'][0]['plazo_entrega']:"");
                $sheet->setCellValueByColumnAndRow(27,$inicioDataReqX , count($row['proveedor'])>=5  && count($row['proveedor'][4]['compras'])>0 ?$row['proveedor'][4]['compras'][0]['flete']:"");

  
                $inicioDataReqX+=1;
                    


        
    }
 
 

    $writer = new Xlsx($spreadsheet);
    try {
        $writer->save('./files/logistica/reportes/mejores_proveedores.xlsx');
        $message = 'File Created';
        $ouput=['status'=>1,'message'=>$message];
        return $ouput;
    }
    catch (Exception $e) {
        $message = 'Unable to save file. Please close any other applications(s) that are using it: [",  $e->getMessage(), "]\n"';
        $ouput=['status'=>-1,'message'=> $message];

        return $ouput;
    }

}


function mejores_proveedores_excel(Request $request){
    $data = $this->get_mejores_proveedores(null,$request->data);
    // return $data;
    $file = $this->generarReporteMejoresProveedoresExcel($data);
    if($file['status']>0){
        $ruta = '/files/logistica/reportes/mejores_proveedores.xlsx';
    }else{
        $ruta='';
    }
     return ['status'=>$file['status'],'ruta'=>$ruta,'message'=>$file['message']];
}



function view_reporte_proveedores_producto_determinado()
{
    $empresas = $this->select_mostrar_empresas();
    return view('logistica/reportes/proveedores_producto_determinado',compact('empresas'));
}

function get_proveedores_producto_determinado(Request $request=null, $data=null){
    $listaOrdenes=[];
    $estado_anulado = $this->get_estado_doc('Anulado');
    $hasWhere[]= ['log_ord_compra.estado','!=',$estado_anulado];
    if($request == null){

        $id_producto = $data['id_producto']?$data['id_producto']:0;
        $id_empresa = $data['id_empresa'];
        $año = $data['año'];
    }else{
        $id_producto = $request->id_producto;
        $id_empresa = $request->id_empresa;
        $tipo_periodo = $request->tipo_periodo;
        $año = $request->año;
    }
 

    if($id_producto >0){
        $hasWhere[]= ['alm_item.id_producto','=',$id_producto];
    }

    if( $id_empresa > 0 ){
        $hasWhere[]= ['log_cotizacion.id_empresa','=',$id_empresa];
    }


    $data = DB::table('logistica.log_ord_compra')
    ->select(
        'log_ord_compra.id_proveedor',
        'adm_contri.id_contribuyente',
        'adm_contri.razon_social',
        'adm_contri.nro_documento',
        'sis_identi.descripcion as tipo_documento',
        'adm_contri.direccion_fiscal',
        'adm_contri.telefono',
        'adm_contri.ubigeo',
        'sis_pais.descripcion as pais',
        'log_cotizacion.id_empresa',
        'adm_empresa.logo_empresa',
        'contri_empresa.razon_social as razon_social_empresa',
        'alm_prod.descripcion as descripcion_producto'

    )
    ->leftJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_orden_compra', '=', 'log_ord_compra.id_orden_compra')
    ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', 'log_det_ord_compra.id_valorizacion_cotizacion')
    ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
    ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
    ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
    ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
    ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
    ->leftJoin('configuracion.sis_pais', 'sis_pais.id_pais', '=', 'adm_contri.id_pais')
    ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_ord_compra.id_cotizacion')
    ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
    ->leftJoin('contabilidad.adm_contri as contri_empresa', 'contri_empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
    ->where($hasWhere)
    ->whereYear('log_ord_compra.fecha', '=', $año)
    ->orderBy('log_ord_compra.id_orden_compra', 'asc')
    ->get();


    $temIdProveedor=[];
    $temIdContribuyente=[];
    $proveedorList=[];

    $temContribuyente=[];

    foreach($data as $row){ //todas las compras

        if(in_array($row->id_proveedor,$temIdProveedor) == false){ 
            $temIdProveedor[]= $row->id_proveedor;//no se repiten los proveedor
            $temIdContribuyente[]= $row->id_contribuyente;//no se repiten los proveedor
            $proveedorList[] = [
                'id_proveedor'=> $row->id_proveedor,
                'id_contribuyente'=> $row->id_contribuyente,
                'razon_social'=> $row->razon_social,
                'tipo_documento'=>$row->tipo_documento, 
                'nro_documento'=> $row->nro_documento, 
                'direccion_fiscal'=> $row->direccion_fiscal, 
                'telefono'=> $row->telefono, 
                'pais'=> $row->pais, 
                'id_empresa'=> $row->id_empresa,
                'logo_empresa'=> $row->logo_empresa,
                'descripcion_producto'=> $row->descripcion_producto,
                'periodo'=> $año,
                'contacto'=>[] 
            ];
        }
        
    }

    // return $proveedorList; //array de proveedores  sin repetir

    $estado_elaborado= $this->get_estado_doc('Elaborado');
    $dataContacto = DB::table('contabilidad.adm_ctb_contac')
    ->select(
        'adm_ctb_contac.id_contribuyente',
        'adm_ctb_contac.id_datos_contacto',
        'adm_ctb_contac.nombre',
        'adm_ctb_contac.dni',
        'adm_ctb_contac.telefono',
        'adm_ctb_contac.email',
        'adm_ctb_contac.cargo',
        'adm_ctb_contac.fecha_registro',
        'establecimiento.direccion as direccion_establecimiento',
        'tipo_establecimiento.descripcion as tipo_establecimiento'
    ) 
    ->leftJoin('contabilidad.establecimiento', 'establecimiento.id_establecimiento', '=', 'adm_ctb_contac.id_establecimiento')
    ->leftJoin('contabilidad.tipo_establecimiento', 'tipo_establecimiento.id_tipo_establecimiento', '=', 'establecimiento.id_tipo_establecimiento')
    ->where('adm_ctb_contac.estado', '=',$estado_elaborado)
    ->whereIn('adm_ctb_contac.id_contribuyente', $temIdContribuyente)
    ->orderBy('adm_ctb_contac.id_contribuyente', 'asc')
    ->get();

    $temIdContribuyente=[];
        foreach($dataContacto as $row){
            if(in_array($row->id_contribuyente,$temIdContribuyente) == false){ 
                $temIdContribuyente[]=$row->id_contribuyente;
            $temContribuyente[]=[
                'id_contribuyente'=>$row->id_contribuyente,
                'contacto'=>[]
            ];
 
            }
        }

        foreach($dataContacto as $row){
            $contactoLista=[
                'id_datos_contacto' => $row->id_datos_contacto,
                'nombre' => $row->nombre,
                'dni' => $row->dni,
                'telefono' => $row->telefono,
                'email' => $row->email,
                'cargo' => $row->cargo,
                'fecha_registro' => $row->fecha_registro,
                'direccion_establecimiento' => $row->direccion_establecimiento,
                'tipo_establecimiento' => $row->tipo_establecimiento,
            ];
            foreach($temContribuyente as $keyIdContr=>$RowIdContri){
                if($RowIdContri['id_contribuyente'] == $row->id_contribuyente){
                    $temContribuyente[$keyIdContr]['contacto'][]=$contactoLista;
                }
            }

        }

    // return $temContribuyente; // array de contactos id_controbuyente

    foreach($proveedorList as $provKey=>$prov){
        foreach($temContribuyente as $cont){
            if($prov['id_contribuyente'] == $cont['id_contribuyente']){
                $proveedorList[$provKey]['contacto']=$cont['contacto'];
            }
        }
    }

    return $proveedorList;

}

function proveedores_producto_determinado(Request $request){
    $data= $this->get_proveedores_producto_determinado($request);
    return response()->json($data);
}

public function generarProveedoresProductoDeterminadoExcel($dataArray){
    $spreadsheet = new Spreadsheet();
    // $spreadsheet->getActiveSheet()->getStyle("B6:B8")->getFont()->setBold(true);
    // $spreadsheet->getActiveSheet()->getStyle("H6")->getFont()->setBold(true);
    $sheet = $spreadsheet->getActiveSheet();
    foreach (range('B', $spreadsheet->getActiveSheet()->getHighestDataColumn()) as $col) {
        $spreadsheet->getActiveSheet()
                ->getColumnDimension($col)
                ->setAutoSize(true);
        $spreadsheet->getActiveSheet()
                    ->getStyle($col)
                    ->getNumberFormat()
                    // ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    ->setFormatCode('#');
                    
    } 


    $styleArrayTabelTitle = array(
        'font' => array(
            'color' => array('rgb' => '111112'),
            'bold' =>true
        ),
        'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'rotation'   => 0,
                'wrap'       => true
        ),
        'borders' => array(
            'allBorders' => array(
                  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, //BORDER_THIN BORDER_MEDIUM BORDER_HAIR
                'color' => array('rgb' => '808296')
            )
        )
    );

    $styleArrayTabelBody = array(
        'font' => array(
            'color' => array('rgb' => '111112'),
            'bold' =>false
        ),
        'format' => array(
            'color' => array('rgb' => '111112'),
            'bold' =>false
        ),
        'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'rotation'   => 0,
                'wrap'       => true
        ),
        'borders' => array(
            'allBorders' => array(
                  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, //BORDER_THIN BORDER_MEDIUM BORDER_HAIR
                'color' => array('rgb' => '808296')
            )
        )
    );
    $sheet->getStyle("B3")->getFont()->setSize(24);
    $sheet->getStyle("B3")->getFont()->setBold(true);
    $sheet->getStyle("B3")->getAlignment()->setHorizontal('center');
    $title= 'Reporte Proveedores Por Producto Determinado';
    $sheet->setCellValue('B3', $title);
    $sheet->mergeCells('B3:N3');

    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing->setName('Paid');
    $drawing->setDescription('Paid');
    // $drawing->setPath('images/'.$logo_empresa); // put your path and image here
    $img= explode('/', $dataArray[0]['logo_empresa']);
    $drawing->setPath('images/'.$img[2]); // put your path and image here
    $drawing->setWidthAndHeight(220,80);
    $drawing->setResizeProportional(true);
    $drawing->setCoordinates('B2');
    $drawing->setWorksheet($spreadsheet->getActiveSheet());

    $spreadsheet->getActiveSheet()->getStyle('B10:G10')->applyFromArray($styleArrayTabelTitle);

    $sheet->setCellValueByColumnAndRow(2, 10, 'Razon Social.');
    $sheet->setCellValueByColumnAndRow(3, 10, 'Documento');
    $sheet->setCellValueByColumnAndRow(4, 10, 'Dirección');
    $sheet->setCellValueByColumnAndRow(5, 10, 'Telefono');
    $sheet->setCellValueByColumnAndRow(6, 10, 'País');
    $sheet->setCellValueByColumnAndRow(7, 10, 'Contacto');

    $inicioDataReqX=11;
    $inicioDataReqY=2;
            
    foreach ($dataArray as $row) {
            $descripcion_producto = $row['descripcion_producto'];
            $periodo = $row['periodo'];
            $razon_social = $row['razon_social'];
            // foreach ($row['cantidad_compras'] as $rowCantidadCompras) {
                $documento = $row['tipo_documento'].': '.$row['nro_documento'];
                $direccion_fiscal = $row['direccion_fiscal'];
                $telefono = $row['telefono'];
                $pais = $row['pais'];
               
            // }

            $sheet->setCellValueByColumnAndRow(2,7 , 'Producto: '.$descripcion_producto);
            $sheet->setCellValueByColumnAndRow(2,8 , 'Periodo: '.$periodo);

            $sheet->setCellValueByColumnAndRow($inicioDataReqY+0,$inicioDataReqX , $razon_social);
            $sheet->setCellValueByColumnAndRow($inicioDataReqY+1,$inicioDataReqX , $documento);
            $sheet->setCellValueByColumnAndRow($inicioDataReqY+2,$inicioDataReqX , $direccion_fiscal);
            $sheet->setCellValueByColumnAndRow($inicioDataReqY+3,$inicioDataReqX , $telefono);
            $sheet->setCellValueByColumnAndRow($inicioDataReqY+4,$inicioDataReqX , $pais);
            $emails=[];
            foreach ($row['contacto'] as $contacto) {
                $emails[]=$contacto['email'];
            }
            $sheet->setCellValueByColumnAndRow($inicioDataReqY+5,$inicioDataReqX , implode(", ",$emails ));
            $spreadsheet->getActiveSheet()->getStyle('B'.$inicioDataReqX.':G'.$inicioDataReqX)->applyFromArray($styleArrayTabelBody);

            $inicioDataReqX+=1;

        }

    $writer = new Xlsx($spreadsheet);
    try {
        $writer->save('./files/logistica/reportes/proveedores_producto_determonado.xlsx');
        $message = 'File Created';
        $ouput=['status'=>1,'message'=>$message];
        return $ouput;
    }
    catch (Exception $e) {
        $message = 'Unable to save file. Please close any other applications(s) that are using it: [",  $e->getMessage(), "]\n"';
        $ouput=['status'=>-1,'message'=> $message];

        return $ouput;
    }
}

function proveedores_producto_determinado_excel(Request $request){
    $data = $this->get_proveedores_producto_determinado(null,$request->data);
    $file =$this->generarProveedoresProductoDeterminadoExcel($data);

    if($file['status']>0){
        $ruta = '/files/logistica/reportes/proveedores_producto_determonado.xlsx';
    }else{
        $ruta='';
    }

    return ['status'=>$file['status'],'ruta'=>$ruta,'message'=>$file['message']];
}


function view_reporte_frecuencia_compras()
{
    $empresas = $this->select_mostrar_empresas();
    return view('logistica/reportes/frecuencia_compra_por_producto',compact('empresas'));
}

function get_frecuencia_compras(Request $request=null, $data=null){

    $listaOrdenes=[];
    $estado_anulado = $this->get_estado_doc('Anulado');
    $hasWhere[]= ['log_ord_compra.estado','!=',$estado_anulado];
    if($request==null){
        $id_producto = $data['id_producto']?$data['id_producto']:0;
        $id_empresa = $data['id_empresa'];
        $año = $data['año'];
 
    }else{
        $id_producto = $request->id_producto;
        $id_empresa = $request->id_empresa;
        $año = $request->año;
    }
 

    if($id_producto >0){
        $hasWhere[]= ['alm_item.id_producto','=',$id_producto];
    }

    if( $id_empresa > 0 ){
        $hasWhere[]= ['log_cotizacion.id_empresa','=',$id_empresa];
    }



    $data = DB::table('logistica.log_ord_compra')
    ->select(
        'alm_item.id_item',
        'alm_item.id_producto',
        'alm_item.codigo as codigo_item',
        'alm_prod.descripcion as descripcion_producto',
        'log_valorizacion_cotizacion.cantidad_cotizada',
        'log_valorizacion_cotizacion.id_unidad_medida',
        'alm_und_medida.descripcion as unidad_medida_cotizada',
        'log_valorizacion_cotizacion.precio_cotizado',
        'log_valorizacion_cotizacion.flete',
        'log_valorizacion_cotizacion.plazo_entrega',
        'log_valorizacion_cotizacion.garantia',
        'log_ord_compra.codigo as codigo_orden',
        'log_cotizacion.id_empresa',
        'adm_empresa.logo_empresa',
        'contri_empresa.razon_social as razon_social_empresa',
        'log_ord_compra.fecha',
        'log_ord_compra.id_proveedor',
        'adm_contri.razon_social',
        'adm_contri.nro_documento'
    )
    ->leftJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_orden_compra', '=', 'log_ord_compra.id_orden_compra')
    ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', 'log_det_ord_compra.id_valorizacion_cotizacion')
    ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_valorizacion_cotizacion.id_unidad_medida')
    ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
    ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
    ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
    ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
    ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_ord_compra.id_cotizacion')
    ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
    ->leftJoin('contabilidad.adm_contri as contri_empresa', 'contri_empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
    ->where($hasWhere)
    ->whereYear('log_ord_compra.fecha', '=', $año)
    ->orderBy('log_valorizacion_cotizacion.precio_cotizado', 'asc')

    ->get();


    foreach($data as $row){
        $listaOrdenes[]=[
            'codigo_orden'=>$row->codigo_orden,
            'fecha'=> date('d-m-Y', strtotime($row->fecha)) ,
            'mes'=> date('m', strtotime($row->fecha)) ,
            'año'=> date('Y', strtotime($row->fecha)) ,
            'id_empresa'=>$row->id_empresa,
            'razon_social_empresa'=>$row->razon_social_empresa,
            'logo_empresa'=>$row->logo_empresa,
            'id_proveedor'=>$row->id_proveedor,
            'razon_social'=>$row->razon_social,
            'nro_documento'=>$row->nro_documento,
            'id_producto'=>$row->id_producto,
            'codigo_item'=>$row->codigo_item,
            'descripcion_producto'=>$row->descripcion_producto,
            'cantidad_cotizada'=>$row->cantidad_cotizada,
            'id_unidad_medida'=>$row->id_unidad_medida,
            'unidad_medida_cotizada'=>$row->unidad_medida_cotizada,
            'precio_cotizado'=>$row->precio_cotizado,
            'flete'=>$row->flete,
            'garantia'=>$row->garantia,
            'plazo_entrega'=>$row->plazo_entrega
        ];
    }

    
    $formato=[
        'id_producto'=>'',
        'descripcion_producto'=>'',
        'periodo'=>'',
        'id_proveedor'=>'',
        'razon_social'=>'',
        'nro_documento'=>'',
        'logo_empresa'=>'',
        'primera_compra'=>'', 
        'ultima_compra'=>'',
        'rango'=>'',
        'nro_compras'=>'',
        'frecuencia'=>''

    ];
    $temProv=[];
    $frecuenciaComprasList=[];
    $comprasList=[];
    $proveedorList=[];
    $temIdProveedor=[];
    foreach($listaOrdenes as $key => $row){ //todas las compras

        if(in_array($row['id_proveedor'],$temIdProveedor) == false){ 
            $temIdProveedor[]= $row['id_proveedor'];//no se repiten los proveedor
            $id_proveedor=$row['id_proveedor'];
            $razon_social=$row['razon_social'];
            $nro_documento=$row['nro_documento'];
            $proveedorList[] = [
                'id_proveedor'=>$id_proveedor,
                'razon_social'=>$razon_social,
                'nro_documento'=>$nro_documento

            ];
        }
        
        // toda la lista de compras
        $comprasList[$key]['id_proveedor'] = $row['id_proveedor'];
        $comprasList[$key]['id_producto'] = $row['id_producto'];
        $comprasList[$key]['cantidad_cotizada'] = $row['cantidad_cotizada'];
        $comprasList[$key]['unidad_medida_cotizada'] = $row['unidad_medida_cotizada'];
        $comprasList[$key]['precio_cotizado'] = $row['precio_cotizado'];
        $comprasList[$key]['flete'] = $row['flete']?$row['flete']:"";
        $comprasList[$key]['garantia'] = $row['garantia'];
        $comprasList[$key]['plazo_entrega'] = $row['plazo_entrega'];
        $comprasList[$key]['razon_social'] = $row['razon_social'];
        $comprasList[$key]['nro_documento'] = $row['nro_documento'];
        $comprasList[$key]['fecha'] = $row['fecha'];

        if(in_array($row['id_proveedor'],$temProv) == false){  
            $temProv[]= $row['id_proveedor'];//no se repiten los productos
            $formato_copy = $formato;
            $formato_copy['id_proveedor'] = $row['id_proveedor'];
            $formato_copy['razon_social'] = $row['razon_social'];
            $formato_copy['nro_documento'] = $row['nro_documento'];
            $formato_copy['id_producto'] = $row['id_producto'];
            $formato_copy['descripcion_producto'] = $row['descripcion_producto'];
            $formato_copy['periodo'] = $año;
            $formato_copy['logo_empresa'] = $row['logo_empresa'];
            $frecuenciaComprasList[]=$formato_copy;
        }
    }
    // // primera compra 
    foreach($frecuenciaComprasList as $keyFCL => $rowFCL){
        $arrFecha=[];
        $nroCompras=0;
        foreach($comprasList as $keyCL => $rowCL){
            if($rowFCL['id_proveedor'] == $rowCL['id_proveedor'] ){
                // date('d', strtotime($rowCL['fecha']))
                $arrFecha[]=$rowCL['fecha'];
                $nroCompras+=1;
                $minDate= min($arrFecha);
                $maxDate= max($arrFecha);
                $frecuenciaComprasList[$keyFCL]['primera_compra'] = $minDate;
                $frecuenciaComprasList[$keyFCL]['ultima_compra'] = $maxDate;
                $frecuenciaComprasList[$keyFCL]['nro_compras'] = $nroCompras;
                $d1=date_create($minDate);
                $d2=date_create($maxDate);
                $rangoDias=date_diff($d1,$d2)->days;
                $frecuenciaComprasList[$keyFCL]['rango'] =$rangoDias;
                $frecuenciaComprasList[$keyFCL]['frecuencia'] = round($rangoDias/$nroCompras,2) ;
            }
        }
    }
 


    return $frecuenciaComprasList;
}

function frecuencia_compras(Request $request){
    $data= $this->get_frecuencia_compras($request);
    return response()->json($data);
} 

public function generarReporteFrecuenciaComprasExcel($dataArray){
    
    $spreadsheet = new Spreadsheet();
    // $spreadsheet->getActiveSheet()->getStyle("B6:B8")->getFont()->setBold(true);
    // $spreadsheet->getActiveSheet()->getStyle("H6")->getFont()->setBold(true);
    $sheet = $spreadsheet->getActiveSheet();
    foreach (range('B', $spreadsheet->getActiveSheet()->getHighestDataColumn()) as $col) {
        $spreadsheet->getActiveSheet()
                ->getColumnDimension($col)
                ->setAutoSize(true);
        $spreadsheet->getActiveSheet()
                    ->getStyle($col)
                    ->getNumberFormat()
                    // ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    ->setFormatCode('#');
    } 


    $styleArrayTabelTitle = array(
        'font' => array(
            'color' => array('rgb' => '111112'),
            'bold' =>true
        ),
        'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'rotation'   => 0,
                'wrap'       => true
        ),
        'borders' => array(
            'allBorders' => array(
                  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, //BORDER_THIN BORDER_MEDIUM BORDER_HAIR
                'color' => array('rgb' => '808296')
            )
        )
    );

    $styleArrayTabelBody = array(
        'font' => array(
            'color' => array('rgb' => '111112'),
            'bold' =>false
        ),
        'format' => array(
            'color' => array('rgb' => '111112'),
            'bold' =>false
        ),
        'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'rotation'   => 0,
                'wrap'       => true
        ),
        'borders' => array(
            'allBorders' => array(
                  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, //BORDER_THIN BORDER_MEDIUM BORDER_HAIR
                'color' => array('rgb' => '808296')
            )
        )
    );
    $sheet->getStyle("B3")->getFont()->setSize(24);
    $sheet->getStyle("B3")->getFont()->setBold(true);
    $sheet->getStyle("B3")->getAlignment()->setHorizontal('center');
    $title= 'Reporte Frecuencia de Compra';
    $sheet->setCellValue('B3', $title);
    $sheet->mergeCells('B3:N3');

    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing->setName('Paid');
    $drawing->setDescription('Paid');
    $img= explode('/', $dataArray[0]['logo_empresa']);
    $drawing->setPath('images/'.$img[2]); // put your path and image here
    $drawing->setWidthAndHeight(220,80);
    $drawing->setResizeProportional(true);
    $drawing->setCoordinates('B2');
    $drawing->setWorksheet($spreadsheet->getActiveSheet());

    $spreadsheet->getActiveSheet()->getStyle('B10:G10')->applyFromArray($styleArrayTabelTitle);


    $sheet->setCellValueByColumnAndRow(2, 10, 'Razon Social.');
    $sheet->setCellValueByColumnAndRow(3, 10, 'Primera Compra');
    $sheet->setCellValueByColumnAndRow(4, 10, 'Ultima Compra');
    $sheet->setCellValueByColumnAndRow(5, 10, 'Rango');
    $sheet->setCellValueByColumnAndRow(6, 10, 'Nro Compras');
    $sheet->setCellValueByColumnAndRow(7, 10, 'Frecuencia');

    

    $inicioDataReqX=11;
    $inicioDataReqY=2;

    foreach ($dataArray as $row) {
        $descripcion_producto = $row['descripcion_producto'];
        $periodo = $row['periodo'];
        $razon_social = $row['razon_social'];
        $primera_compra = $row['primera_compra'];
        $ultima_compra = $row['ultima_compra'];
        $rango = $row['rango'];
        $nro_compras = $row['nro_compras'];
        $frecuencia = $row['frecuencia'];
 

        $sheet->setCellValueByColumnAndRow(2,7 , 'Producto: '.$descripcion_producto);
        $sheet->setCellValueByColumnAndRow(2,8 , 'Periodo: '.$periodo);

        $sheet->setCellValueByColumnAndRow($inicioDataReqY+0,$inicioDataReqX , $razon_social);
        $sheet->setCellValueByColumnAndRow($inicioDataReqY+1,$inicioDataReqX , $primera_compra);
        $sheet->setCellValueByColumnAndRow($inicioDataReqY+2,$inicioDataReqX , $ultima_compra);
        $sheet->setCellValueByColumnAndRow($inicioDataReqY+3,$inicioDataReqX , $rango);
        $sheet->setCellValueByColumnAndRow($inicioDataReqY+4,$inicioDataReqX , $nro_compras);
        $sheet->setCellValueByColumnAndRow($inicioDataReqY+5,$inicioDataReqX , $frecuencia);
 
        $spreadsheet->getActiveSheet()->getStyle('B'.$inicioDataReqX.':G'.$inicioDataReqX)->applyFromArray($styleArrayTabelBody);

        $inicioDataReqX+=1;

    }

    $writer = new Xlsx($spreadsheet);
    try {
        $writer->save('./files/logistica/reportes/frecuencia_compras.xlsx');
        $message = 'File Created';
        $ouput=['status'=>1,'message'=>$message];
        return $ouput;
    }
    catch (Exception $e) {
        $message = 'Unable to save file. Please close any other applications(s) that are using it: [",  $e->getMessage(), "]\n"';
        $ouput=['status'=>-1,'message'=> $message];

        return $ouput;
    }
}


function frecuencia_compras_excel(Request $request){
    $data = $this->get_frecuencia_compras(null,$request->data);
    // return $data;
    $file = $this->generarReporteFrecuenciaComprasExcel($data);
    if($file['status']>0){
        $ruta = '/files/logistica/reportes/frecuencia_compras.xlsx';
    }else{
        $ruta='';
    }
    return ['status'=>$file['status'],'ruta'=>$ruta,'message'=>$file['message']];
}




function view_reporte_historial_precios()
{
    $empresas = $this->select_mostrar_empresas();
    return view('logistica/reportes/historial_precios',compact('empresas'));
}

function get_historial_precios(Request $request=null, $data=null){

    $estado_anulado = $this->get_estado_doc('Anulado');
    $hasWhere[]= ['doc_com.estado','!=',$estado_anulado];
    if($request==null){
        $id_producto = $data['id_producto']?$data['id_producto']:0;
        $id_empresa = $data['id_empresa'];
        $año = $data['año'];
 
    }else{
        $id_producto = $request->id_producto;
        $id_empresa = $request->id_empresa;
        $año = $request->año;
    }
 

    if($id_producto >0){
        $hasWhere[]= ['alm_item.id_producto','=',$id_producto];
    }

    if( $id_empresa > 0 ){
        $hasWhere[]= ['log_cotizacion.id_empresa','=',$id_empresa];
    }

    $arrHistoricaPrecios=[];

    $doc_com_det = DB::table('almacen.doc_com_det')
    ->select(
        'doc_com_det.id_item',
        'alm_item.id_producto',
        'alm_item.id_servicio',
        'alm_item.id_equipo',
        DB::raw("(CASE 
        WHEN alm_item.id_item isNUll THEN 'NO EXISTE' 
        WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
        WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
        WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
        ELSE 'nulo' END) AS descripcion
        "),
        'doc_com_det.precio_unitario',
        'doc_com_det.id_unid_med',
        'alm_und_medida.descripcion as unidad_medida',
        'doc_com_det.obs',
        'doc_com.id_proveedor',
        'adm_contri.razon_social as razon_social_proveedor',
        'sis_identi.descripcion as tipo_documento',
        'adm_contri.nro_documento as nro_documento_proveedor',
        'adm_empresa.logo_empresa',
        'contri_empresa.razon_social as razon_social_empresa',
        'doc_com_det.fecha_registro'
        )
        ->leftJoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'doc_com_det.id_doc')
        ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'doc_com_det.id_item')
        ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_item')
        ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
        ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
        ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'doc_com_det.id_unid_med')
        ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'doc_com.id_sede')
        ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
        ->leftJoin('contabilidad.adm_contri as contri_empresa', 'contri_empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')

        ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'doc_com.id_proveedor')
        ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')

        ->where($hasWhere)
        ->whereYear('doc_com_det.fecha_registro', '=', $año)

        ->orderBy('doc_com_det.fecha_registro', 'desc')
    // ->limit(3)
    ->get();

    if(count($doc_com_det) > 0){

        foreach($doc_com_det as $key => $data){
            $obs = $data->obs?explode(" ",$data->obs,2):'';
        
            $arrHistoricaPrecios[]=[
                'id'=>$key+1,
                'id_item'=>$data->id_item,
                'id_producto'=>$data->id_producto,
                'descripcion'=>$data->descripcion,
                'unidad_medida'=>$data->unidad_medida,
                'precio_unitario'=>$data->precio_unitario,
                'id_proveedor'=> $data->id_proveedor,
                'razon_social_proveedor'=> $data->razon_social_proveedor,
                'tipo_documento'=> $data->tipo_documento,
                'nro_documento_proveedor'=> $data->nro_documento_proveedor,
                'proveedor'=> $obs?$obs[1]:'-',
                'documento'=> $obs?$obs[0]:'-',
                'id_sede'=> '-',
                'id_empresa'=> '-',
                'logo_empresa'=> '-',
                'razon_social_empresa'=> '-',
                'fecha_registro'=>$data->fecha_registro,
                'periodo'=>$año
            ];
        }
    }

    
 


    return $arrHistoricaPrecios;
}

function historial_precios(Request $request){
    $data= $this->get_historial_precios($request);
    return response()->json($data);
} 


public function generarReporteHistorialPreciosExcel($dataArray){
    
    $spreadsheet = new Spreadsheet();
    // $spreadsheet->getActiveSheet()->getStyle("B6:B8")->getFont()->setBold(true);
    // $spreadsheet->getActiveSheet()->getStyle("H6")->getFont()->setBold(true);
    $sheet = $spreadsheet->getActiveSheet();
    foreach (range('B', $spreadsheet->getActiveSheet()->getHighestDataColumn()) as $col) {
        $spreadsheet->getActiveSheet()
                ->getColumnDimension($col)
                ->setAutoSize(true);
        $spreadsheet->getActiveSheet()
                    ->getStyle($col)
                    ->getNumberFormat()
                    // ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    ->setFormatCode('#');
    } 


    $styleArrayTabelTitle = array(
        'font' => array(
            'color' => array('rgb' => '111112'),
            'bold' =>true
        ),
        'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'rotation'   => 0,
                'wrap'       => true
        ),
        'borders' => array(
            'allBorders' => array(
                  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, //BORDER_THIN BORDER_MEDIUM BORDER_HAIR
                'color' => array('rgb' => '808296')
            )
        )
    );

    $styleArrayTabelBody = array(
        'font' => array(
            'color' => array('rgb' => '111112'),
            'bold' =>false
        ),
        'format' => array(
            'color' => array('rgb' => '111112'),
            'bold' =>false
        ),
        'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'rotation'   => 0,
                'wrap'       => true
        ),
        'borders' => array(
            'allBorders' => array(
                  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, //BORDER_THIN BORDER_MEDIUM BORDER_HAIR
                'color' => array('rgb' => '808296')
            )
        )
    );
    $sheet->getStyle("B3")->getFont()->setSize(24);
    $sheet->getStyle("B3")->getFont()->setBold(true);
    $sheet->getStyle("B3")->getAlignment()->setHorizontal('center');
    $title= 'Reporte Historial de Precios';
    $sheet->setCellValue('B3', $title);
    $sheet->mergeCells('B3:N3');

    // $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    // $drawing->setName('Paid');
    // $drawing->setDescription('Paid');
    // $img= explode('/', $dataArray[0]['logo_empresa']);
    // $drawing->setPath('images/'.$img[2]); // put your path and image here
    // $drawing->setWidthAndHeight(220,80);
    // $drawing->setResizeProportional(true);
    // $drawing->setCoordinates('B2');
    // $drawing->setWorksheet($spreadsheet->getActiveSheet());

    $spreadsheet->getActiveSheet()->getStyle('B10:H10')->applyFromArray($styleArrayTabelTitle);


    $sheet->setCellValueByColumnAndRow(2, 10, 'Item.');
    $sheet->setCellValueByColumnAndRow(3, 10, 'Producto');
    $sheet->setCellValueByColumnAndRow(4, 10, 'Unidad');
    $sheet->setCellValueByColumnAndRow(5, 10, 'Precio Unitario');
    $sheet->setCellValueByColumnAndRow(6, 10, 'Proveedor');
    $sheet->setCellValueByColumnAndRow(7, 10, 'Documento');
    $sheet->setCellValueByColumnAndRow(8, 10, 'Fecha Registro');

    

    $inicioDataReqX=11;
    $inicioDataReqY=2;

    foreach ($dataArray as $row) {
        $id_item = $row['id_item'];
        $descripcion_producto = $row['descripcion'];
        $unidad_medida = $row['unidad_medida'];
        $precio_unitario = $row['precio_unitario'];
        $proveedor = $row['razon_social_proveedor'].' RUC: '.$row['nro_documento_proveedor'];
        $factura = $row['documento'];
        $fecha_registro = $row['fecha_registro'];
        $periodo = $row['periodo'];
 
 

        // $sheet->setCellValueByColumnAndRow(2,7 , 'Producto: '.$descripcion_producto);
        $sheet->setCellValueByColumnAndRow(2,8 , 'Periodo: '.$periodo);

        $sheet->setCellValueByColumnAndRow($inicioDataReqY+0,$inicioDataReqX , $id_item);
        $sheet->setCellValueByColumnAndRow($inicioDataReqY+1,$inicioDataReqX , $descripcion_producto);
        $sheet->setCellValueByColumnAndRow($inicioDataReqY+2,$inicioDataReqX , $unidad_medida);
        $sheet->setCellValueByColumnAndRow($inicioDataReqY+3,$inicioDataReqX , $precio_unitario);
        $sheet->setCellValueByColumnAndRow($inicioDataReqY+4,$inicioDataReqX , $proveedor);
        $sheet->setCellValueByColumnAndRow($inicioDataReqY+5,$inicioDataReqX , $factura);
        $sheet->setCellValueByColumnAndRow($inicioDataReqY+6,$inicioDataReqX , $fecha_registro);
 
        $spreadsheet->getActiveSheet()->getStyle('B'.$inicioDataReqX.':H'.$inicioDataReqX)->applyFromArray($styleArrayTabelBody);

        $inicioDataReqX+=1;

    }

    $writer = new Xlsx($spreadsheet);
    try {
        $writer->save('./files/logistica/reportes/historial_precios.xlsx');
        $message = 'File Created';
        $ouput=['status'=>1,'message'=>$message];
        return $ouput;
    }
    catch (Exception $e) {
        $message = 'Unable to save file. Please close any other applications(s) that are using it: [",  $e->getMessage(), "]\n"';
        $ouput=['status'=>-1,'message'=> $message];

        return $ouput;
    }
}


function historial_precios_excel(Request $request){
    $data = $this->get_historial_precios(null,$request->data);
    // return $data;
    $file = $this->generarReporteHistorialPreciosExcel($data);
    if($file['status']>0){
        $ruta = '/files/logistica/reportes/historial_precios.xlsx';
    }else{
        $ruta='';
    }
    return ['status'=>$file['status'],'ruta'=>$ruta,'message'=>$file['message']];
}




    public function select_proveedor()
    {
        $data = DB::table('logistica.log_prove')
            ->select(
                'log_prove.*',
                'adm_contri.nro_documento',
                'adm_contri.razon_social'
            )
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->orderBy('log_prove.id_proveedor', 'asc')
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

    public function select_sede_by_empresa($id_empresa)
    {
        $data = DB::table('administracion.sis_sede')
            ->select(
                'sis_sede.*', 'ubi_dis.descripcion as ubigeo_descripcion'
            )
            ->leftJoin('configuracion.ubi_dis','ubi_dis.id_dis','=','sis_sede.id_ubigeo')
            ->where('sis_sede.id_empresa','=',$id_empresa)
            ->orderBy('sis_sede.id_empresa', 'asc')
            ->get();
        return $data;
    }

    public function select_grupo_by_sede($id_sede)
    {
        $data = DB::table('administracion.adm_grupo')
            ->select(
                'adm_grupo.*'
            )
            ->where('adm_grupo.id_sede','=',$id_sede)
            ->orderBy('adm_grupo.id_grupo', 'asc')
            ->get();
        return $data;
    }

public function get_cuadro_costos_comercial(){
    // prueba de consulta
    $data = DB::connection("mgcp_pgsql")->table('mgcp_cuadro_costos.cc')
    ->select(
        'cc.id',
        'cc.fecha_entrega',
        'oportunidades.codigo_oportunidad',
        'oportunidades.oportunidad',
        'oportunidades.probabilidad',
        'oportunidades.fecha_limite',
        'oportunidades.moneda',
        'oportunidades.importe',
        'oportunidades.id_tipo_negocio',
        'oportunidades.nombre_contacto',
        'tipos_negocio.tipo',
        'oportunidades.created_at'
    )
    ->join('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
    ->join('mgcp_oportunidades.tipos_negocio', 'tipos_negocio.id', '=', 'oportunidades.id_tipo_negocio')
    ->orderBy('cc.id', 'asc')
    ->get();
    return $data;
}

}
