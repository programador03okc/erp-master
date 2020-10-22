<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
date_default_timezone_set('America/Lima');


class OrdenController extends Controller
{

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

        // $sedes = Auth::user()->sedesAcceso();

        return view('logistica/ordenes/generar_orden_requerimiento', compact('sedes','empresas','sis_identidad','tp_documento', 'tp_moneda','tp_doc','condiciones'));
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
            ->select('adm_empresa.id_empresa', 'adm_empresa.logo_empresa','adm_contri.nro_documento', 'adm_contri.razon_social')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->where('adm_empresa.estado', '=', 1)
            ->orderBy('adm_contri.razon_social', 'asc')
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

    public function select_sis_identidad()
    {
        $data = DB::table('contabilidad.sis_identi')
            ->select('sis_identi.id_doc_identidad', 'sis_identi.descripcion')
            ->where('sis_identi.estado', '=', 1)
            ->orderBy('sis_identi.descripcion', 'asc')->get();
        return $data;
    }

    function sedesAcceso($id_empresa){
        $id_usuario = Auth::user()->id_usuario;
        $sedes = DB::table('configuracion.sis_usua_sede')
        ->select(
            'sis_sede.*',
            DB::raw("(ubi_dis.descripcion) || ' ' || (ubi_prov.descripcion) || ' ' || (ubi_dpto.descripcion)  AS ubigeo_descripcion")
            )
        ->join('administracion.sis_sede','sis_sede.id_sede','=','sis_usua_sede.id_sede')
        ->leftJoin('configuracion.ubi_dis','ubi_dis.id_dis','=','sis_sede.id_ubigeo')
        ->leftJoin('configuracion.ubi_prov', 'ubi_dis.id_prov', '=', 'ubi_prov.id_prov')
        ->leftJoin('configuracion.ubi_dpto', 'ubi_prov.id_dpto', '=', 'ubi_dpto.id_dpto')

        ->where([['sis_usua_sede.id_usuario','=',$id_usuario],
                ['sis_usua_sede.estado','=', 1],
                ['sis_sede.estado','=', 1],
                ['sis_sede.id_empresa','=',$id_empresa]])
		->get();
        return $sedes;
    }

    public function listar_requerimientos_pendientes(){

        $alm_req = DB::table('almacen.alm_req')
        ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
        ->join('almacen.tipo_cliente', 'tipo_cliente.id_tipo_cliente', '=', 'alm_req.tipo_cliente')
        ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
        ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
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
            'alm_req.tipo_cliente',
            'tipo_cliente.descripcion AS tipo_cliente_desc',
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
            'alm_req.id_cliente',
            'alm_req.id_persona',
            'alm_req.id_prioridad',
            'alm_req.fecha_registro',
            'alm_req.estado',
            'alm_req.id_sede',
            'sis_sede.descripcion as empresa_sede',
            'adm_estado_doc.estado_doc',
            'adm_estado_doc.bootstrap_color',
            DB::raw("(CASE WHEN alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
    //         DB::raw("(SELECT  COUNT(log_ord_compra.id_orden_compra) FROM logistica.log_ord_compra
    // WHERE log_ord_compra.id_grupo_cotizacion = log_detalle_grupo_cotizacion.id_grupo_cotizacion)::integer as cantidad_orden"),
    //         DB::raw("(SELECT  COUNT(mov_alm.id_mov_alm) FROM almacen.mov_alm
    // WHERE mov_alm.id_guia_com = guia_com_oc.id_guia_com and 
    // guia_com_oc.id_oc = log_ord_compra.id_orden_compra)::integer as cantidad_entrada_almacen")

        )
        ->where([['alm_req.estado', '=', 1],['alm_req.confirmacion_pago','=',true],['alm_req.id_tipo_requerimiento', '=', 1]])
        ->orWhere([['alm_req.estado', '=', 1],['alm_req.id_tipo_requerimiento', '=', 1]])
        ->orWhere([['alm_req.estado', '=', 23],['alm_req.id_tipo_requerimiento', '=', 1]])
        ->orderBy('alm_req.id_requerimiento', 'desc')
        ->get();


        
    return response()->json(["data" => $alm_req]);
 
    }

    public function lista_ordenes_en_proceso(){
        $orden_list=[];
        $detalle_orden_list=[];

        $orden_obj = DB::table('logistica.log_ord_compra')
        ->select(
            'log_ord_compra.*',
            'adm_contri.id_contribuyente',
            'adm_contri.razon_social',
            'adm_contri.nro_documento',
            'sis_sede.descripcion as empresa_sede',
            'adm_estado_doc.estado_doc',
            'adm_estado_doc.bootstrap_color',
        )
        ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
        ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
        ->leftJoin('administracion.adm_estado_doc', 'log_ord_compra.estado', '=', 'adm_estado_doc.id_estado_doc')

        ->where([['log_ord_compra.estado', '!=', 7]])
        ->orderBy('log_ord_compra.fecha','desc')
        ->get();

        $orden_list = collect($orden_obj)->map(function($x){ return (array) $x; })->toArray(); 


        $detalle_orden_obj = DB::table('logistica.log_det_ord_compra')
        ->select(
            'log_det_ord_compra.*',
            'alm_det_req.observacion',
            'alm_req.codigo',
            'alm_prod.codigo AS alm_prod_codigo',
            'alm_prod.part_number',
            'alm_cat_prod.descripcion as categoria',
            'alm_subcat.descripcion as subcategoria',
            'alm_prod.descripcion AS alm_prod_descripcion'
        )
        ->leftJoin('almacen.alm_item', 'log_det_ord_compra.id_item', '=', 'alm_item.id_item')
        ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
        ->leftJoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
        ->leftJoin('almacen.alm_subcat','alm_subcat.id_subcategoria','=','alm_prod.id_subcategoria')
    
        ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
        ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')

        ->where([['log_det_ord_compra.estado', '!=', 7]])
        ->get();

        $detalle_orden_list = collect($detalle_orden_obj)->map(function($x){ return (array) $x; })->toArray(); 
        foreach ($orden_list as $keyOrd => $valueOrd) {
            $orden_list[$keyOrd]['detalle']=[];
        }

        foreach ($orden_list as $keyOrd => $valueOrd) {
            foreach ($detalle_orden_list as $keyDetOrd => $valueDetOrd) {
                if($valueOrd['id_orden_compra'] == $valueDetOrd['id_orden_compra']){
                    $orden_list[$keyOrd]['detalle'][]= $valueDetOrd;
                }
            }
        }


        // $alm_req = DB::table('almacen.alm_req')
        //     ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
        //     ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
        //     ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
        //     ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
        //     ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
        //     ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
        //     ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
        //     ->leftJoin('proyectos.proy_op_com', 'proy_op_com.id_op_com', '=', 'alm_req.id_op_com')
        //     ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
        //     ->leftJoin('logistica.log_ord_compra', 'alm_req.id_requerimiento', '=', 'log_ord_compra.id_requerimiento')
        //     ->leftJoin('administracion.adm_estado_doc as estado', 'log_ord_compra.estado', '=', 'estado.id_estado_doc')

        //     ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')

        //     ->select(
        //         'alm_req.id_requerimiento',
        //         'log_ord_compra.id_orden_compra',
        //         'log_ord_compra.codigo_softlink',
        //         'log_ord_compra.fecha as fecha_orden',
        //         'alm_req.codigo',
        //         'alm_req.concepto',
        //         'alm_req.fecha_requerimiento',
        //         'alm_req.id_tipo_requerimiento',
        //         'alm_tp_req.descripcion AS tipo_req_desc',
        //         'sis_usua.usuario',
        //         'rrhh_rol.id_area',
        //         'adm_area.descripcion AS area_desc',
        //         'rrhh_rol.id_rol',
        //         'rrhh_rol.id_rol_concepto',
        //         'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
        //         'alm_req.id_grupo',
        //         'adm_grupo.descripcion AS adm_grupo_descripcion',
        //         'alm_req.id_op_com',
        //         'proy_op_com.codigo as codigo_op_com',
        //         'proy_op_com.descripcion as descripcion_op_com',
        //         'alm_req.concepto AS alm_req_concepto',
        //         // 'log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion',
        //         'alm_req.id_prioridad',
        //         'alm_req.fecha_registro',
        //         'alm_req.estado as estado_requerimiento',
        //         'log_ord_compra.estado as id_estado_orden',
        //         'estado.estado_doc as estado_orden',
        //         'log_ord_compra.id_sede',
        //         'sis_sede.codigo as codigo_sede_empresa',
        //         'adm_estado_doc.estado_doc',
        //         'adm_estado_doc.bootstrap_color',
        //         // DB::raw("(CASE WHEN alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")

        //     )
        //     ->where([['alm_req.estado', '=', 5],['log_ord_compra.estado', '!=', 7],['alm_req.id_tipo_requerimiento','=',1],['alm_req.confirmacion_pago','=',true]])
        //     // ->orWhere([['alm_req.estado', '=', 5],['log_ord_compra.estado', '!=', 7],['alm_req.id_tipo_requerimiento','=',1],['alm_req.tipo_cliente','=',3]])
        //     ->orderBy('alm_req.id_requerimiento', 'desc')
        //     ->get();

            $output['data']=$orden_list;

        return response()->json($output);
    }

    public function cantidadCompradaDetalleOrden($id_detalle_requerimiento ){
        $cantiadComprada= 0;
        $det_ord_compra = DB::table('logistica.log_det_ord_compra')
        ->select('log_det_ord_compra.*')
        ->where('log_det_ord_compra.id_detalle_requerimiento', $id_detalle_requerimiento)
        ->get();

        if(isset($det_ord_compra) && sizeof($det_ord_compra)> 0){
            $cantiadComprada = $det_ord_compra->first()->cantidad;
        }
        return $cantiadComprada;

    }
    public function get_detalle_requerimiento_orden(Request $request )
    {

        $requerimientoList = $request->requerimientoList;
        // return $requerimientoList;
        // return response()->json($output);


        $alm_req = DB::table('almacen.alm_req')
            ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->leftJoin('contabilidad.adm_contri as contrib', 'adm_empresa.id_contribuyente', '=', 'contrib.id_contribuyente')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
            
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
                'proy_op_com.codigo as codigo_op_com',
                'proy_op_com.descripcion as descripcion_op_com',
                'alm_req.id_presupuesto',
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
            ->whereIn('alm_req.id_requerimiento', $requerimientoList)
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
                    'alm_det_req.stock_comprometido',
                    'alm_det_req.observacion',
                    
                    
                    
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
                ->whereIn('alm_req.id_requerimiento', $requerimientoList)
                ->whereIn('alm_det_req.estado', [1,23,7])

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

            // $total = 0;
            if (isset($alm_det_req)) {
                $lastId = "";
                $detalle_requerimiento = [];
                foreach ($alm_det_req as $data) {
                    if ($data->id_detalle_requerimiento !== $lastId) {
                        $subtotal =+ $data->cantidad *  $data->precio_referencial;
                        // $total = $subtotal;
                        $detalle_requerimiento[] = [
                            'id_detalle_requerimiento'  => $data->id_detalle_requerimiento,
                            'id_requerimiento'          => $data->id_requerimiento,
                            'codigo_requerimiento'      => $data->codigo_requerimiento,
                            'id_item'                   => $data->id_item,
                            'cantidad'                  => $data->cantidad - ($data->stock_comprometido?$data->stock_comprometido:0) - ($this->cantidadCompradaDetalleOrden($data->id_detalle_requerimiento)),
                            'id_unidad_medida'          => $data->id_unidad_medida,
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
                            'stock_comprometido'        => $data->stock_comprometido,
                            'observacion'               => $data->observacion,
                            'cantidad_a_comprar'        => 0,
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

        // $collect = collect($requerimiento);
        // $collect->put('total',$total);

        $data = [
            "requerimiento" => $requerimiento,
            "det_req" => $detalle_requerimiento
        ];

        return $data;
    }
    
    function update_estado_orden(Request $request ){
        $id_orden_compra = $request->id_orden_compra;
        $id_estado_orden_selected = $request->id_estado_orden_selected;
        
       $update_log_ord_compra = DB::table('logistica.log_ord_compra')
        ->where([
                ['id_orden_compra',$id_orden_compra]])
        ->update(
            [
                'estado' => $id_estado_orden_selected
            ]);

        return $update_log_ord_compra;

    }
    function update_estado_detalle_requerimiento($id_detalle_requerimiento,$estado ){
        
        $status=500;
        $alm_det_req = DB::table('almacen.alm_det_req')
        ->where([
                ['id_detalle_requerimiento',$id_detalle_requerimiento]])
        ->update(
            [
                'estado' => $estado
            ]);
        if(isset($alm_det_req) && $alm_det_req>0){
            $status= 200;
        }else{
            $status= 204;

        }
        $output = [ 'status'=>$status];

        return $output;

    }

    function view_listar_ordenes()
    {
        return view('logistica/ordenes/listar_ordenes');
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

    public function get_lista_ordes_por_requerimiento(){
        $ord_compra = DB::table('logistica.log_ord_compra')
            ->select(
                'log_ord_compra.*',
                 // 'log_cdn_pago.descripcion as condicion',
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
                'cta_prin.nro_cuenta as nro_cuenta_prin',
                'cta_alter.nro_cuenta as nro_cuenta_alter',
                'cta_detra.nro_cuenta as nro_cuenta_detra',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'log_ord_compra_pago.id_pago',
                'log_ord_compra_pago.detalle_pago',
                'log_ord_compra_pago.archivo_adjunto'
            )
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->leftjoin('logistica.log_cdn_pago','log_cdn_pago.id_condicion_pago','=','log_ord_compra.id_condicion')
            ->leftjoin('contabilidad.adm_cta_contri as cta_prin','cta_prin.id_cuenta_contribuyente','=','log_ord_compra.id_cta_principal')
            ->leftjoin('contabilidad.adm_cta_contri as cta_alter','cta_alter.id_cuenta_contribuyente','=','log_ord_compra.id_cta_alternativa')
            ->leftjoin('contabilidad.adm_cta_contri as cta_detra','cta_detra.id_cuenta_contribuyente','=','log_ord_compra.id_cta_detraccion')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','log_ord_compra.estado')
            ->leftjoin('logistica.log_ord_compra_pago','log_ord_compra_pago.id_orden_compra','=','log_ord_compra.id_orden_compra')


            ->where([
                ['log_ord_compra.estado', '!=', 7],
                ['log_ord_compra.id_grupo_cotizacion', '=', null]
            ])
            ->orderBy('log_ord_compra.fecha','desc')
            ->get();
            
            
            $data=[];
            $containerOpenBrackets='<div class="btn-group" role="group" style="margin-bottom: 5px; width:100px">';
            $containerCloseBrackets='</div>';

            if(count($ord_compra)>0){
                foreach($ord_compra as $element){

                    $btnImprimirOrden= '<button type="button" onClick="imprimir_orden(event)" title="Imprimir Orden" class="imprimir_orden btn btn-md btn-warning boton" data-toggle="tooltip" data-placement="bottom" data-id-orden-compra="'.$element->id_orden_compra.'"  data-id-pago=""> <i class="fas fa-file-pdf"></i> </button>';

                    $data[]=[
                        'id_orden_compra'=> $element->id_orden_compra,
                        'fecha' => date_format(date_create($element->fecha),'Y-m-d'), 
                        'codigo'=> '<label class="lbl-codigo" title="Abrir Orden" onClick="abrir_orden('.$element->id_orden_compra.')">'.$element->codigo.'</label>',
                        'nro_documento'=> $element->nro_documento, 
                        'razon_social'=> $element->razon_social,
                        'moneda_simbolo'=> $element->moneda_simbolo, 
                        'monto_subtotal'=> $element->monto_subtotal, 
                        'monto_igv'=> $element->monto_igv, 
                        'monto_total'=>$element->monto_total, 
                        'condicion'=> $element->condicion, 
                        'plazo_entrega'=> $element->plazo_entrega, 
                        'nro_cuenta_prin'=> $element->nro_cuenta_prin, 
                        'nro_cuenta_alter'=> $element->nro_cuenta_alter, 
                        'nro_cuenta_detra'=> $element->nro_cuenta_detra,
                        'codigo_cuadro_comparativo'=> '',
                        'estado'=> '<center><label class="label label-warning" title="info" style="cursor:pointer;" onClick="viewGroupInfo(event)" data-group-info="" >i</label></center><span class="label label-'.$element->bootstrap_color.'">'.$element->estado_doc.'</span></center>',
                        'detalle_pago'=> $element->detalle_pago, 
                        'archivo_adjunto'=> $element->archivo_adjunto,
                        'botones_accion'=>$containerOpenBrackets.$btnImprimirOrden.$containerCloseBrackets
                    ];
                }
            }
        $output['data'] = $data;
        return $output;
    }

    public function listar_todas_ordenes(){
        $allRol = Auth::user()->getAllRol();
        $userSessionRolConceptoList=[];
        foreach($allRol as $rol){

            $userSessionRolConceptoList[]=$rol->id_rol; //id_rol_concepto actuales
        }

        $ordenPorCotizacionList=[
            "data"=>[
            // ["id_orden_compra"=> 30,
            // "fecha"=> "2020-08-22",
            // "codigo"=> "<label class=\"lbl-codigo\" title=\"Abrir Orden\" onClick=\"abrir_orden(31)\">OC-2008-0005</label>",
            // "nro_documento"=> "20619865472",
            // "razon_social"=> "MAXIMA SA",
            // "moneda_simbolo"=> null,
            // "monto_subtotal"=> null,
            // "monto_igv"=> null,
            // "monto_total"=> null,
            // "condicion"=> "Crédito 12 Días",
            // "plazo_entrega"=> null,
            // "nro_cuenta_prin"=> null,
            // "nro_cuenta_alter"=> null,
            // "nro_cuenta_detra"=> null,
            // "codigo_cuadro_comparativo"=> "",
            // "estado"=> "<center><label class=\"label label-warning\" title=\"info\" style=\"cursor=>pointer;\" onClick=\"viewGroupInfo(event)\" data-group-info=\"\" >i</label></center><span class=\"label label-default\">Elaborado</span></center>",
            // "detalle_pago"=> null,
            // "archivo_adjunto"=> null,
            // "botones_accion"=> ""]
            ]
        ];
        $ordenPorRequerimientoList= $this->get_lista_ordes_por_requerimiento();
                $returnedTarget =  array_merge((array) $ordenPorCotizacionList['data'], (array) $ordenPorRequerimientoList['data']); 
                $output = ['data'=>$returnedTarget];
                return response()->json($output);
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
                

                'log_det_ord_compra.id_detalle_requerimiento',
                'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
                'log_det_ord_compra.cantidad',
                'log_det_ord_compra.precio',
                'log_det_ord_compra.id_unidad_medida',
                'alm_und_medida.descripcion AS unidad_medida',
                'log_det_ord_compra.subtotal',
                'log_valorizacion_cotizacion.flete',
                'log_valorizacion_cotizacion.porcentaje_descuento',
                'log_valorizacion_cotizacion.monto_descuento',
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
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_det_ord_compra.id_unidad_medida')
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
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
            ->leftJoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'log_cotizacion.id_tp_doc')
            ->leftJoin('contabilidad.adm_contri as contab_contri', 'contab_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi as contab_sis_identi', 'contab_sis_identi.id_doc_identidad', '=', 'contab_contri.id_doc_identidad')
            ->leftJoin('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'valoriza_coti_detalle.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->where([
                ['log_ord_compra.id_orden_compra', '=', $id_orden_compra],
                ['log_ord_compra.estado', '!=', 7]
            ])
            ->get();

        // return $data;
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
                    'cantidad' => $data->cantidad,
                    'id_unidad_medida' => $data->id_unidad_medida,
                    'unidad_medida' => $data->unidad_medida,
                    'precio' => $data->precio,
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
            $html .= '<td>' . $data['unidad_medida'] . '</td>';
            $html .= '<td class="right">' . $data['cantidad'] . '</td>';
            $html .= '<td class="right">' . $data['precio'] . '</td>';
            $html .= '<td class="right">' . number_format((($data['cantidad'] * $data['precio']) - (($data['cantidad']* $data['precio'])/1.18)),2,'.','') . '</td>';
            $html .= '<td class="right">' . $data['monto_descuento'] . '</td>';
            $html .= '<td class="right">' . $data['cantidad'] * $data['precio'] . '</td>';
            $html .= '</tr>';
            $total = $total + ($data['cantidad'] * $data['precio']);
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
    public function leftZero($lenght, $number)
    {
        $nLen = strlen($number);
        $zeros = '';
        for ($i = 0; $i < ($lenght - $nLen); $i++) {
            $zeros = $zeros . '0';
        }
        return $zeros . $number;
    }

    public function nextCodigoOrden($id_tp_docum)
    {
        $mes = date('m', strtotime("now"));
        $anio = date('y', strtotime("now"));

        $num = DB::table('logistica.log_ord_compra')
            ->where('id_tp_documento', $id_tp_docum)->count();

        $correlativo = $this->leftZero(4, ($num + 1));

        if ($id_tp_docum == 2) {
            $codigoOrden = "OC-{$anio}{$mes}{$correlativo}";
        } else if ($id_tp_docum == 3) {
            $codigoOrden = "OS-{$anio}{$mes}{$correlativo}";
        } else {
            $codigoOrden = "-{$anio}{$mes}{$correlativo}";
        }
        return $codigoOrden;
    }

    public function guardar_orden_por_requerimiento(Request $request){
        try {
            DB::beginTransaction();

            $usuario = Auth::user()->id_usuario;
            $tp_doc = ($request->id_tipo_doc !== null ? $request->id_tipo_doc : 2);
            $codigo = $this->nextCodigoOrden($tp_doc);
            $guardarEnRequerimiento = $request->guardarEnRequerimiento;

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
                    'estado' => 17,
                    'codigo_softlink' => ($request->codigo_orden!==null ? $request->codigo_orden : ''),
                ],
                'id_orden_compra'
            );

            $dataDetalle = json_decode($request->detalle_requerimiento);

            $allidReqList=[];
            foreach ($dataDetalle as $d) {
                
                $allidReqList[]= $d->id_requerimiento;

                if($d->cantidad_a_comprar > 0){
                    if($guardarEnRequerimiento == false){
                        DB::table('logistica.log_det_ord_compra')
                        ->insert([
                            'id_orden_compra'=>$id_orden,
                            'id_item'=> ($d->id_item ? $d->id_item : null),
                            'id_detalle_requerimiento'=> ($d->id_detalle_requerimiento ? $d->id_detalle_requerimiento : null),
                            'cantidad'=> $d->cantidad_a_comprar,
                            'id_unidad_medida'=> $d->id_unidad_medida,
                            'precio'=> $d->precio_referencial,
                            // 'subtotal'=> ($d->precio_referencial * $d->cantidad),
                            'subtotal'=> $d->subtotal?$d->subtotal:0,
                            'estado'=> 17
                            // 'fecha_registro'=> date('Y-m-d H:i:s')
                        ]);
                    }elseif($guardarEnRequerimiento == true){
                        if(isset($d->id ) == true){
                            $id_new_det_req = DB::table('almacen.alm_det_req')->insertGetId(
                                [
                                    'id_requerimiento'      => ($d->id_requerimiento ? $d->id_requerimiento : null),
                                    'id_item'               => ($d->id_item ? $d->id_item : null),
                                    'id_producto'           => ($d->id_producto ? $d->id_producto : null),
                                    'precio_referencial'    => ($d->precio_referencial ? $d->precio_referencial : null),
                                    'cantidad'              => ($d->cantidad_a_comprar ? $d->cantidad_a_comprar : null),
                                    'fecha_entrega'         => null,
                                    'lugar_entrega'         => null,
                                    'descripcion_adicional' => ($d->descripcion_adicional ? $d->descripcion_adicional : null),
                                    'partida'               => null,
                                    'id_unidad_medida'      => ($d->cantidad_a_comprar ? $d->cantidad_a_comprar : null),
                                    'id_tipo_item'          => ($d->id_tipo_item ? $d->id_tipo_item : null),
                                    'fecha_registro'        => date('Y-m-d H:i:s'),
                                    'estado'                => ($d->estado ? $d->estado : null),
                                    'id_almacen_reserva'     => null
                                ],
                                'id_detalle_requerimiento'
                            );

                            DB::table('logistica.log_det_ord_compra')
                            ->insert([
                                'id_orden_compra'=>$id_orden,
                                'id_item'=> $d->id_item ? $d->id_item : null,
                                'id_detalle_requerimiento'=> $id_new_det_req,
                                'cantidad'=> $d->cantidad_a_comprar?$d->cantidad_a_comprar:null,
                                'id_unidad_medida'=> $d->id_unidad_medida?$d->id_unidad_medida:null,
                                'precio'=> $d->precio_referencial?$d->precio_referencial:null,
                                'subtotal'=> $d->subtotal?$d->subtotal:null,
                                'estado'=> 17
                            ]);
                        }
                    }

                }
            }

            $uniqueIdReqList = array_unique($allidReqList);

            $idRequerimientoAtentidosTotalList=[];
            $idRequerimientoAtentidosParcialList=[];

            $sizeDataDetalle = count($dataDetalle);
            $countAtendido=0;
            
                foreach ($dataDetalle as $d) {
                    if(($d->cantidad_a_comprar + $d->stock_comprometido ) == $d->cantidad ){
                        $countAtendido+=1;
                        $idRequerimientoAtentidosTotalList[]=$d->id_requerimiento;
                        DB::table('almacen.alm_det_req')
                        ->where('id_detalle_requerimiento',$d->id_detalle_requerimiento)
                        ->update(
                            [
                                'estado'=>5, //atendido total
                                'stock_comprometido'=> $d->stock_comprometido
                            ] 
                                ); 
                    }
                    if((($d->cantidad_a_comprar + $d->stock_comprometido )>0) && (($d->cantidad_a_comprar + $d->stock_comprometido ) < $d->cantidad)){
                        $idRequerimientoAtentidosParcialList[]=$d->id_requerimiento;
                        DB::table('almacen.alm_det_req')
                        ->where('id_detalle_requerimiento',$d->id_detalle_requerimiento)
                        ->update(
                            [
                                'estado'=>23, // atendido parcial
                                'stock_comprometido'=> $d->stock_comprometido
                            ]
                            
                        ); 
                    }
                } 
                if(count($idRequerimientoAtentidosTotalList)>0){
                    foreach ($idRequerimientoAtentidosTotalList as $key => $id_req) {

                        if($countAtendido ==$sizeDataDetalle){
                            DB::table('almacen.alm_req')
                            ->where('id_requerimiento',$id_req)
                            ->update(['estado'=>5]); // atendido total
    
                            DB::table('almacen.alm_req_obs')
                            ->insert([  'id_requerimiento'=>$id_req,
                                        'accion'=>'ATENDIDO TOTAL',
                                        'descripcion'=>'Se generó Orden de Compra '.$codigo,
                                        'id_usuario'=>$usuario,
                                        'fecha_registro'=>date('Y-m-d H:i:s')
                            ]);
                        }else{
                            DB::table('almacen.alm_req')
                            ->where('id_requerimiento',$id_req)
                            ->update(['estado'=>23]); // atendido parcial
    
                            DB::table('almacen.alm_req_obs')
                            ->insert([  'id_requerimiento'=>$id_req,
                                        'accion'=>'ATENDIDO PARCIAL',
                                        'descripcion'=>'Se generó Orden de Compra '.$codigo,
                                        'id_usuario'=>$usuario,
                                        'fecha_registro'=>date('Y-m-d H:i:s')
                            ]);
                        }

                    }

                }
                if(count($idRequerimientoAtentidosParcialList)>0){
                    foreach ($idRequerimientoAtentidosParcialList as $key => $id_req) {
                        DB::table('almacen.alm_req')
                        ->where('id_requerimiento',$id_req)
                        ->update(['estado'=>23]); // atendido parcial

                        DB::table('almacen.alm_req_obs')
                        ->insert([  'id_requerimiento'=>$id_req,
                                    'accion'=>'ATENDIDO PARCIAL',
                                    'descripcion'=>'Se generó Orden de Compra '.$codigo,
                                    'id_usuario'=>$usuario,
                                    'fecha_registro'=>date('Y-m-d H:i:s')
                        ]);
                    }
                }

            // $alm_det_req = DB::table('almacen.alm_det_req')
            // ->select(
            //     'alm_det_req.*'
            // )
            // ->whereIn('alm_det_req.id_requerimiento',$uniqueIdReqList)
            // ->where([
            //     ['alm_det_req.estado', '!=', 7]
            //     ])
            // ->get();

            // $cantidad_det_req_con_orden = DB::table('almacen.alm_det_req')
            // ->whereIn('alm_det_req.id_requerimiento',$uniqueIdReqList)
            // ->where([
            //     ['alm_det_req.estado', '=', 5]
            //     ])
            // ->count();

            // $cantidad_det_req=0;
            // if($alm_det_req){
            //     $cantidad_det_req = $alm_det_req->count();
            // }

            // Debugbar::info($cantidad_det_req);
            // Debugbar::info($cantidad_det_req_con_orden);

            // if($cantidad_det_req == $cantidad_det_req_con_orden){
            //     DB::table('almacen.alm_req')
            //     ->whereIn('alm_req.id_requerimiento',$uniqueIdReqList)
            //     ->update(['estado'=>5]);
            // }


            //Agrega accion en requerimiento
            // foreach ($uniqueIdReqList as $key => $id_requerimiento) {
            //     DB::table('almacen.alm_req_obs')
            //     ->insert([  'id_requerimiento'=>$id_requerimiento,
            //                 'accion'=>'ATENDIDO',
            //                 'descripcion'=>'Se generó Orden de Compra '.$codigo,
            //                 'id_usuario'=>$usuario,
            //                 'fecha_registro'=>date('Y-m-d H:i:s')
            //     ]);
            // }
  

        DB::commit();
            return response()->json($id_orden);

        } catch (\PDOException $e) {
            DB::rollBack();
        }

    }

    public function ver_orden($id_orden)
    {

        $log_ord_compra = DB::table('logistica.log_ord_compra')
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
            'adm_estado_doc.estado_doc',
            'adm_estado_doc.bootstrap_color',
            'log_ord_compra_pago.id_pago',
            'log_ord_compra_pago.detalle_pago',
            'log_ord_compra_pago.archivo_adjunto'
            )
        ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
        ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->leftjoin('logistica.log_cdn_pago','log_cdn_pago.id_condicion_pago','=','log_ord_compra.id_condicion')
        ->leftjoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','log_ord_compra.id_moneda')
        ->leftjoin('contabilidad.adm_cta_contri as cta_prin','cta_prin.id_cuenta_contribuyente','=','log_ord_compra.id_cta_principal')
        ->leftjoin('contabilidad.adm_cta_contri as cta_alter','cta_alter.id_cuenta_contribuyente','=','log_ord_compra.id_cta_alternativa')
        ->leftjoin('contabilidad.adm_cta_contri as cta_detra','cta_detra.id_cuenta_contribuyente','=','log_ord_compra.id_cta_detraccion')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','log_ord_compra.estado')
        ->leftjoin('logistica.log_ord_compra_pago','log_ord_compra_pago.id_orden_compra','=','log_ord_compra.id_orden_compra')
        ->where('log_ord_compra.id_orden_compra','=',$id_orden)
        ->get();

        if (isset($log_ord_compra)) {
            $orden = [];
            foreach ($log_ord_compra as $data) {
                    $orden = [
                        'codigo'         => $data->codigo,
                        'fecha'          => $data->fecha,
                        'codigo_softlink'=> $data->codigo_softlink,
                        'razon_social'   => $data->razon_social,
                        'nro_documento'  => $data->nro_documento,
                        'condicion'      => $data->condicion,
                        'simbolo'        => $data->simbolo,
                        'estado_doc'     => $data->estado_doc,
                        'id_condicion'   => $data->id_condicion,
                        'plazo_dias'     => $data->plazo_dias,
                        'plazo_entrega'  => $data->plazo_entrega,
                        'igv_porcentaje' => $data->igv_porcentaje,
                        'monto_subtotal' => $data->monto_subtotal,
                        'monto_igv'      => $data->monto_igv,
                        'monto_total'    => $data->monto_total
                    ];
                }

        } else {

            $orden = [];
        }


        $log_det_ord_compra = DB::table('logistica.log_det_ord_compra')
        ->leftJoin('almacen.alm_item', 'log_det_ord_compra.id_item', '=', 'alm_item.id_item')
        ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
        ->leftJoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
        ->leftJoin('almacen.alm_subcat','alm_subcat.id_subcategoria','=','alm_prod.id_subcategoria')
        ->leftJoin('almacen.alm_det_req', 'log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')
        ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
        ->leftJoin('almacen.alm_und_medida as und_medida_det_req', 'alm_det_req.id_unidad_medida', '=', 'und_medida_det_req.id_unidad_medida')
        // ->leftJoin('almacen.alm_det_req_adjuntos', 'alm_det_req_adjuntos.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')
        ->leftJoin('almacen.alm_almacen', 'alm_det_req.id_almacen_reserva', '=', 'alm_almacen.id_almacen')

        ->select(
            'log_det_ord_compra.id_detalle_orden',
            'log_det_ord_compra.garantia',
            'log_det_ord_compra.estado',
            'log_det_ord_compra.personal_autorizado',
            'log_det_ord_compra.lugar_despacho',
            'log_det_ord_compra.descripcion_adicional',
            'log_det_ord_compra.id_unidad_medida',
            'log_det_ord_compra.precio',
            'log_det_ord_compra.cantidad',
            'alm_det_req.id_detalle_requerimiento',
            'alm_req.id_requerimiento',
            'alm_req.codigo AS codigo_requerimiento',
            'alm_det_req.id_requerimiento',
            'alm_det_req.id_item AS id_item_alm_det_req',
            'alm_det_req.precio_referencial',
            // 'alm_det_req.cantidad',
            // 'alm_det_req.id_unidad_medida',
            'und_medida_det_req.descripcion AS unidad_medida',
            'alm_det_req.obs',
            'alm_det_req.fecha_registro AS fecha_registro_alm_det_req',
            'alm_det_req.fecha_entrega',
            'alm_det_req.lugar_entrega',
            'alm_det_req.descripcion_adicional',
            'alm_det_req.id_tipo_item',
            'alm_item.id_item',
            'alm_det_req.id_producto',
            'alm_cat_prod.descripcion as categoria',
            'alm_subcat.descripcion as subcategoria',
            'alm_item.codigo AS codigo_item',
            'alm_item.fecha_registro AS alm_item_fecha_registro',
            'alm_prod.codigo AS alm_prod_codigo',
            'alm_prod.part_number',
            'alm_prod.descripcion AS alm_prod_descripcion',

            'alm_det_req.id_almacen_reserva',
            'alm_almacen.descripcion as almacen_reserva',
         )
        ->where([
            ['log_det_ord_compra.id_orden_compra', '=', $id_orden],
        ])

        ->orderBy('log_det_ord_compra.id_detalle_orden', 'desc')
        ->get();

        // return $log_det_ord_compra;
        $total = 0;
        if (isset($log_det_ord_compra)) {
            $lastId = "";
            $detalle_orden = [];
            foreach ($log_det_ord_compra as $data) {
                if ($data->id_detalle_requerimiento !== $lastId) {
                    $subtotal =+ $data->cantidad *  $data->precio_referencial;
                    $total = $subtotal;
                    $detalle_orden[] = [
                        'id_detalle_orden'          => $data->id_detalle_orden,
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
                        'categoria'               => $data->categoria,
                        'subcategoria'               => $data->subcategoria,
                        'part_number'               => $data->part_number,
                        'descripcion'               => $data->descripcion_adicional,
                        'id_almacen'                => $data->id_almacen_reserva,
                        'almacen_reserva'               => $data->almacen_reserva,
                        'subtotal'               =>  $subtotal,
                    ];
                    $lastId = $data->id_detalle_requerimiento;
                }
            }

 

        } else {

            $detalle_orden = [];
        }

 
        $output=['status'=>200, 'data'=>['orden'=>$orden,'detalle_orden'=>$detalle_orden]];


        return response()->json($output);
    }


    public function generar_orden_pdf($id_orden_compra)
    {
        $pdf = \App::make('dompdf.wrapper');
        // $id = $this->decode5t($id_orden_compra);
        $id = $id_orden_compra;
        // $data = $this->get_orden($id);
        // return response()->json($data);

        $pdf->loadHTML($this->imprimir_orden_pdf($id));
        return $pdf->stream();
        return $pdf->download('orden.pdf');
    }

}
