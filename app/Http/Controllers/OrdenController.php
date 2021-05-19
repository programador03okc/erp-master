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
use Debugbar;

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
        $clasificaciones = (new AlmacenController)->mostrar_clasificaciones_cbo();
        $subcategorias = (new AlmacenController)->mostrar_subcategorias_cbo();
        $categorias = (new AlmacenController)->mostrar_categorias_cbo();
        $unidades = (new AlmacenController)->mostrar_unidades_cbo();

        $unidades_medida = (new LogisticaController)->mostrar_unidad_medida();
        $monedas = (new LogisticaController)->mostrar_moneda();
        // $sedes = Auth::user()->sedesAcceso();

        return view('logistica/ordenes/generar_orden_requerimiento', compact('sedes','empresas','sis_identidad','tp_documento', 'tp_moneda','tp_doc','condiciones','clasificaciones','subcategorias','categorias','unidades','unidades_medida','monedas'));
    }
    function view_crear_orden_requerimiento()
    {
        $condiciones = $this->select_condiciones();
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

        $unidades_medida = (new LogisticaController)->mostrar_unidad_medida();
        $monedas = (new LogisticaController)->mostrar_moneda();
        // $sedes = Auth::user()->sedesAcceso();

        return view('logistica/gestion_logistica/compras/ordenes/elaborar/crear_orden_requerimiento', compact('sedes','sis_identidad','tp_documento', 'tp_moneda','tp_doc','condiciones','clasificaciones','subcategorias','categorias','unidades','unidades_medida','monedas'));
    }

    function lista_contactos_proveedor($id_proveedor){
        
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
        ->leftJoin('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->where('log_prove.id_proveedor',$id_proveedor)
        ->orderby('adm_ctb_contac.nombre','asc')
        ->get();
        

        return response()->json(['data'=>$data]);


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

    public function select_empresa_sede()
    {
        $data = DB::table('administracion.sis_sede')
            ->select(
                'sis_sede.*', 'ubi_dis.descripcion as ubigeo_descripcion'
            )
            ->leftJoin('configuracion.ubi_dis','ubi_dis.id_dis','=','sis_sede.id_ubigeo')
            ->where('sis_sede.estado','=','1')
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

    public function select_mostrar_estados_compra()
    {
          $data = DB::table('logistica.estados_compra')
            ->select('estados_compra.*')
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


   

    public function listarDetalleOrden($tipoOrden, $vinculadoPor, $empresa, $sede, $tipoProveedor, $enAlmacen, $signoSubTotal, $subtotal, $estado){

        switch ($signoSubTotal) {
            case 'MAYOR':
                $simboloSubtotal='>';

                break;
            
            case 'MENOR':
                $simboloSubtotal='<';

                break;
            
            case 'IGUAL':
                $simboloSubtotal='=';

                break;
            
            case 'MAYOR_IGUAL':
                $simboloSubtotal='>=';

                break;
            
            case 'MENOR_IGUAL':
                $simboloSubtotal='<=';
                break;
            
            default:
                $simboloSubtotal='>';

                break;
        }

        $subtotal= $subtotal >0?$subtotal:'0';


        $orden_list=[];
        $detalle_orden_list=[];

        $orden_obj = DB::table('logistica.log_ord_compra')
        ->select(
            'log_ord_compra.id_orden_compra as orden_id_orden_compra',
            'log_ord_compra.id_grupo_cotizacion as orden_id_grupo_cotizacion',
            'log_ord_compra.id_tp_documento as orden_id_tp_documento',
            'log_ord_compra.fecha as orden_fecha',
            'log_ord_compra.id_usuario as orden_id_usuario',
            'log_ord_compra.id_moneda as orden_id_moneda',
            'log_ord_compra.igv_porcentaje as orden_igv_porcentaje',
            'log_ord_compra.monto_subtotal as orden_monto_subtotal',
            'log_ord_compra.monto_igv as orden_monto_igv',
            'log_ord_compra.monto_total as orden_monto_total',
            'log_ord_compra.estado as orden_estado',
            'log_ord_compra.id_proveedor as orden_id_proveedor',
            'log_ord_compra.codigo as orden_codigo',
            'log_ord_compra.id_cotizacion as orden_id_cotizacion',
            'log_ord_compra.id_condicion as orden_id_condicion',
            'log_ord_compra.plazo_dias as orden_plazo_dias',
            'log_ord_compra.id_cta_principal as orden_id_cta_principal',
            'log_ord_compra.id_cta_alternativa as orden_id_cta_alternativa',
            'log_ord_compra.id_cta_detraccion as orden_id_cta_detraccion',
            'log_ord_compra.personal_autorizado as orden_personal_autorizado',
            'log_ord_compra.plazo_entrega as orden_plazo_entrega',
            'log_ord_compra.en_almacen as orden_en_almacen',
            'log_ord_compra.id_occ as orden_id_occ',
            'log_ord_compra.id_sede as orden_id_sede',
            'log_ord_compra.id_requerimiento as orden_id_requerimiento',
            'log_ord_compra.codigo_softlink as orden_codigo_softlink',
            'adm_contri.id_contribuyente',
            'adm_contri.razon_social',
            'adm_contri.nro_documento',
            'sis_sede.descripcion as empresa_sede',
            'log_det_ord_compra.id_detalle_orden as detalle_orden_id_detalle_orden',
            'log_det_ord_compra.id_orden_compra as detalle_orden_id_orden_compra',
            'log_det_ord_compra.id_item as detalle_orden_id_item',
            'log_det_ord_compra.garantia as detalle_orden_garantia',
            'log_det_ord_compra.id_valorizacion_cotizacion as detalle_orden_id_valorizacion_cotizacion',
            'log_det_ord_compra.estado as id_detalle_orden_estado',
            'estados_compra.descripcion as detalle_orden_estado',
            'log_det_ord_compra.personal_autorizado as detalle_orden_personal_autorizado',
            'log_det_ord_compra.lugar_despacho as detalle_orden_lugar_despacho',
            'log_det_ord_compra.descripcion_adicional as detalle_orden_descripcion_adicional',
            'log_det_ord_compra.cantidad as detalle_orden_cantidad',
            'log_det_ord_compra.precio as detalle_orden_precio',
            'log_det_ord_compra.id_unidad_medida as detalle_orden_id_unidad_medida',
            'log_det_ord_compra.subtotal as detalle_orden_subtotal',
            'log_det_ord_compra.id_detalle_requerimiento as detalle_orden_id_detalle_requerimiento',
            'log_det_ord_compra.tipo_item_id',
            'alm_det_req.observacion',
            'alm_req.concepto',
            'alm_req.id_cliente',
            'contri_cli.razon_social as razon_social_cliente',
            'alm_req.codigo as codigo_requerimiento',
            'alm_prod.codigo AS alm_prod_codigo',
            'alm_prod.part_number',
            'alm_cat_prod.descripcion as categoria',
            'alm_subcat.descripcion as subcategoria',
            'alm_prod.descripcion AS alm_prod_descripcion'
        )
        ->leftJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_orden_compra', '=', 'log_ord_compra.id_orden_compra')
        ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
        ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
        ->leftJoin('logistica.estados_compra', 'log_det_ord_compra.estado', '=', 'estados_compra.id_estado')
        ->leftJoin('almacen.alm_item', 'log_det_ord_compra.id_item', '=', 'alm_item.id_item')
        ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
        ->leftJoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
        ->leftJoin('almacen.alm_subcat','alm_subcat.id_subcategoria','=','alm_prod.id_subcategoria')    
        ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
        ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
        ->leftJoin('comercial.com_cliente','com_cliente.id_cliente','=','alm_req.id_cliente')
        ->leftJoin('contabilidad.adm_contri as contri_cli','contri_cli.id_contribuyente','=','com_cliente.id_contribuyente')

        ->where([
            ['log_ord_compra.estado', '!=', 7],
            $tipoOrden >0 ? ['log_ord_compra.id_tp_documento',$tipoOrden]:[null],
            $empresa >0 ? ['sis_sede.id_empresa',$empresa]:[null],
            $sede >0 ? ['sis_sede.id_sede',$sede]:[null],
            ($tipoProveedor =='NACIONAL') ? ['adm_contri.id_pais','=','170']:($tipoProveedor =='EXTRANJERO' ? ['adm_contri.id_pais','=','170']:[null]),
            $estado >0 ? ['log_ord_compra.estado',$estado]:[null]
            ])
        ->when(($vinculadoPor !='null'), function($query) use ($vinculadoPor)  {
            if($vinculadoPor== 'REQUERIMIENTO'){
                $whereVinculadoPor='log_det_ord_compra.id_detalle_requerimiento > 0';
            }elseif($vinculadoPor == 'CUADRO_COMPARATIVO'){
                $whereVinculadoPor='log_det_ord_compra.detalle_cuadro_comparativo_id > 0';
            }
            return $query->WhereIn('log_ord_compra.id_orden_compra', function($query) use ($whereVinculadoPor)
            {
                $query->select('log_det_ord_compra.id_orden_compra')
                ->from('logistica.log_det_ord_compra')
                ->whereRaw($whereVinculadoPor);
            });
        })
        ->when(($enAlmacen =='true'), function($query)  {
            return $query->WhereIn('log_ord_compra.id_orden_compra', function($query)
            {
                $query->select('log_det_ord_compra.id_orden_compra')
                    ->from('logistica.log_det_ord_compra')
                    ->leftjoin('almacen.guia_com_det', 'guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden')
                    ->whereRaw('guia_com_det.id_guia_com_det > 0');
            });
        })
        ->orderBy('log_ord_compra.fecha','desc')
        ->whereRaw('coalesce((log_det_ord_compra.cantidad * log_det_ord_compra.precio) ,0) '.$simboloSubtotal.' '.$subtotal)
        ->get();

        $orden_list = collect($orden_obj)->map(function($x){ return (array) $x; })->toArray(); 

        $output['data']=$orden_list;
        return $output;
    }

    function documentosVinculadosOrden($id_orden){
        $status=0;
        $id_cc='';
        $tipo_cuadro='';
        $id_oportunidad='';
        $documentos=[];

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
        
        if(count($log_ord_compra)>0){
            foreach($log_ord_compra as $data){
                $id_cc=$data->id_cc;
                $tipo_cuadro=$data->tipo_cuadro;
            }

            $cc = DB::table('mgcp_cuadro_costos.cc')
            ->select('cc.id_oportunidad')
            ->where([
                ['cc.id', '=', $id_cc]
            ])
            ->get();

            if(count($cc)>0){

                $id_oportunidad = $cc->first()->id_oportunidad;
                $oc_propias = DB::table('mgcp_acuerdo_marco.oc_propias')
                ->select('oc_propias.id','oc_propias.url_oc_fisica')
                ->where([
                    ['oc_propias.id_oportunidad', '=', $id_oportunidad]
                ])
                ->get();
                
                if(count($oc_propias)>0){
                    $orden_electronica= "https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=".($oc_propias->first()->id)."&ImprimirCompleto=1";
                    $orden_fisica= $oc_propias->first()->url_oc_fisica;
                    $documentos[]=[
                        'orden_fisica'=>$orden_fisica,
                        'orden_electronica'=>$orden_electronica,
                    ];
                    $status=200;

                }else{
                    $status=204;
                }


            }else{
                $status=204;
            }


        }else{
            $status=204;
        }

        $output=['status'=>$status, 'data'=>$documentos];

        return response()->json($output);
    }

    public function cantidadCompradaDetalleOrden($id_detalle_requerimiento ){
        $cantiadComprada= 0;
        $det_ord_compra = DB::table('logistica.log_det_ord_compra')
        ->select('log_det_ord_compra.*')
        ->where([
            ['log_det_ord_compra.id_detalle_requerimiento', '=',$id_detalle_requerimiento],
            ['log_det_ord_compra.estado','!=',7]
            ]
        )
        ->get();

        if(isset($det_ord_compra) && sizeof($det_ord_compra)> 0){
            foreach($det_ord_compra as $data){
                $cantiadComprada += $data->cantidad;

            }
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
            ->leftJoin('configuracion.ubi_dis as dis_empresa_sede', 'sis_sede.id_ubigeo', '=', 'dis_empresa_sede.id_dis')
            ->leftJoin('configuracion.ubi_prov as prov_empresa_sede', 'dis_empresa_sede.id_prov', '=', 'prov_empresa_sede.id_prov')
            ->leftJoin('configuracion.ubi_dpto as dpto_empresa_sede', 'prov_empresa_sede.id_dpto', '=', 'dpto_empresa_sede.id_dpto')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
            
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
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
                'alm_req.observacion',
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
            ->whereIn('alm_req.id_requerimiento', $requerimientoList)
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
                    'id_almacen' => $data->id_almacen,
                    'codigo_oportunidad' => $data->codigo_oportunidad,
                    'nombre_ejecutivo_responsable' => $data->nombre_ejecutivo_responsable,
                    'id_cc' => $data->id_cc

                    
                ];
            };

            $alm_det_req = DB::table('almacen.alm_det_req')
                ->leftJoin('almacen.alm_prod', 'alm_det_req.id_producto', '=', 'alm_prod.id_producto')
                ->leftJoin('almacen.alm_item', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
                ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
                ->leftJoin('almacen.alm_und_medida as und_medida_det_req', 'alm_det_req.id_unidad_medida', '=', 'und_medida_det_req.id_unidad_medida')
                ->leftJoin('almacen.alm_det_req_adjuntos', 'alm_det_req_adjuntos.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')

                ->select(
                    'alm_det_req.id_detalle_requerimiento',
                    'alm_req.id_requerimiento',
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
                ->whereIn('alm_det_req.tiene_transformacion',[false])
                ->whereNotIn('alm_det_req.estado', [7])
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
                        $subtotal =+ $data->cantidad *  $data->precio_unitario;
                        // $total = $subtotal;
                        $detalle_requerimiento[] = [
                            'id'                        => $data->id_detalle_requerimiento,
                            'id_detalle_requerimiento'  => $data->id_detalle_requerimiento,
                            'id_requerimiento'          => $data->id_requerimiento,
                            'codigo_requerimiento'      => $data->codigo_requerimiento,
                            'id_item'                   => $data->id_item,
                            'cantidad'                  => $data->cantidad - ($data->stock_comprometido?$data->stock_comprometido:0) - ($this->cantidadCompradaDetalleOrden($data->id_detalle_requerimiento)),
                            'id_unidad_medida'          => $data->id_unidad_medida,
                            'unidad_medida'             => $data->unidad_medida,
                            'precio_unitario'           => $data->precio_unitario,
                            'subtotal'                  => $data->subtotal,
                            'descripcion_adicional'     => $data->descripcion_adicional,
                            'lugar_entrega'             => $data->lugar_entrega,
                            'fecha_registro'            => $data->fecha_registro_alm_det_req,
                            'estado'                    => $data->estado,
                            'codigo_item'               => $data->codigo_item,
                            'id_tipo_item'              => $data->id_tipo_item,
                            'id_producto'               => $data->id_producto,
                            'alm_prod_codigo'           => $data->alm_prod_codigo,
                            'part_number'               => $data->part_number,
                            'descripcion'               => $data->alm_prod_descripcion,
                            'stock_comprometido'        => $data->stock_comprometido,
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

    function update_estado_item_orden(Request $request ){
        $id_detalle_orden_compra = $request->id_detalle_orden_compra;
        $id_estado_detalle_orden_selected = $request->id_estado_detalle_orden_selected;
        
       $update_log_det_ord_compra = DB::table('logistica.log_det_ord_compra')
        ->where([
                ['id_detalle_orden',$id_detalle_orden_compra]])
        ->update(
            [
                'estado' => $id_estado_detalle_orden_selected
            ]);

        return $update_log_det_ord_compra;

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
        $empresas = $this->select_mostrar_empresas();
        $estados = $this->select_mostrar_estados_compra();

        return view('logistica/gestion_logistica/compras/ordenes/listado/listar_ordenes', compact('empresas','estados'));
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

    public function listarOrdenes($tipoOrden, $vinculadoPor, $empresa, $sede, $tipoProveedor, $enAlmacen, $signoOrden, $montoOrden, $estado){
       
        switch ($signoOrden) {
            case 'MAYOR':
                $simboloMontoOrden='>';

                break;
            
            case 'MENOR':
                $simboloMontoOrden='<';

                break;
            
            case 'IGUAL':
                $simboloMontoOrden='=';

                break;
            
            case 'MAYOR_IGUAL':
                $simboloMontoOrden='>=';

                break;
            
            case 'MENOR_IGUAL':
                $simboloMontoOrden='<=';
                break;
            
            default:
                $simboloMontoOrden='>';

                break;
        }

        $montoOrden= $montoOrden >0?$montoOrden:'0';



     

        $ord_compra = DB::table('logistica.log_ord_compra')
        ->select(
            'log_ord_compra.*',
            'sis_sede.descripcion as descripcion_sede_empresa',

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
            'estados_compra.descripcion as estado_doc',
            'log_ord_compra.estado',
            'log_ord_compra_pago.id_pago',
            'log_ord_compra_pago.detalle_pago',
            'log_ord_compra_pago.archivo_adjunto',
            DB::raw("(SELECT  coalesce(sum((log_det_ord_compra.cantidad * log_det_ord_compra.precio))*1.18 ,0) AS suma_subtotal
            FROM logistica.log_det_ord_compra 
            WHERE   log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra AND
                    log_det_ord_compra.estado != 7) AS suma_subtotal")
        )
        ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
        ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
        ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
        ->leftjoin('logistica.log_cdn_pago','log_cdn_pago.id_condicion_pago','=','log_ord_compra.id_condicion')
        ->leftjoin('contabilidad.adm_cta_contri as cta_prin','cta_prin.id_cuenta_contribuyente','=','log_ord_compra.id_cta_principal')
        ->leftjoin('contabilidad.adm_cta_contri as cta_alter','cta_alter.id_cuenta_contribuyente','=','log_ord_compra.id_cta_alternativa')
        ->leftjoin('contabilidad.adm_cta_contri as cta_detra','cta_detra.id_cuenta_contribuyente','=','log_ord_compra.id_cta_detraccion')
        ->leftjoin('logistica.estados_compra','estados_compra.id_estado','=','log_ord_compra.estado')
        ->leftjoin('logistica.log_ord_compra_pago','log_ord_compra_pago.id_orden_compra','=','log_ord_compra.id_orden_compra')


        ->where([
            ['log_ord_compra.estado', '!=', 7],
            ['log_ord_compra.id_grupo_cotizacion', '=', null],
            $tipoOrden >0 ? ['log_ord_compra.id_tp_documento',$tipoOrden]:[null],
            $empresa >0 ? ['sis_sede.id_empresa',$empresa]:[null],
            $sede >0 ? ['sis_sede.id_sede',$sede]:[null],
            ($tipoProveedor =='NACIONAL') ? ['adm_contri.id_pais','=','170']:($tipoProveedor =='EXTRANJERO' ? ['adm_contri.id_pais','=','170']:[null]),
            $estado >0 ? ['log_ord_compra.estado',$estado]:[null]
            // $montoOrden >0 ? ['suma_subtotal','>',$montoOrden]:[null]

        ])

        ->when(($vinculadoPor !='null'), function($query) use ($vinculadoPor)  {
            if($vinculadoPor== 'REQUERIMIENTO'){
                $whereVinculadoPor='log_det_ord_compra.id_detalle_requerimiento > 0';
            }elseif($vinculadoPor == 'CUADRO_COMPARATIVO'){
                $whereVinculadoPor='log_det_ord_compra.detalle_cuadro_comparativo_id > 0';
            }
            return $query->WhereIn('log_ord_compra.id_orden_compra', function($query) use ($whereVinculadoPor)
            {
                $query->select('log_det_ord_compra.id_orden_compra')
                ->from('logistica.log_det_ord_compra')
                ->whereRaw($whereVinculadoPor);
            });
        })

        ->when(($enAlmacen =='true'), function($query)  {
            return $query->WhereIn('log_ord_compra.id_orden_compra', function($query)
            {
                $query->select('log_det_ord_compra.id_orden_compra')
                    ->from('logistica.log_det_ord_compra')
                    ->leftjoin('almacen.guia_com_det', 'guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden')
                    ->whereRaw('guia_com_det.id_guia_com_det > 0');
            });
        })

            ->orderBy('log_ord_compra.fecha','desc')

            ->whereRaw('(SELECT  coalesce(sum((log_det_ord_compra.cantidad * log_det_ord_compra.precio))*1.18 ,0)
            FROM logistica.log_det_ord_compra 
            WHERE   log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra AND
                    log_det_ord_compra.estado != 7) '.$simboloMontoOrden.' '.$montoOrden)
        ->get();


        $data_orden=[];
        if(count($ord_compra)>0){
            foreach($ord_compra as $element){

                $data_orden[]=[
                    'id_orden_compra'=> $element->id_orden_compra,
                    'id_tp_documento'=> $element->id_tp_documento,
                    'fecha' => date_format(date_create($element->fecha),'Y-m-d'), 
                    'codigo'=> $element->codigo,
                    'descripcion_sede_empresa'=> $element->descripcion_sede_empresa,
                    'nro_documento'=> $element->nro_documento, 
                    'razon_social'=> $element->razon_social,
                    'moneda_simbolo'=> $element->moneda_simbolo, 
                    // 'monto_subtotal'=> $element->monto_subtotal, 
                    'monto_igv'=> $element->monto_igv, 
                    'monto_total'=>$element->monto_total, 
                    'condicion'=> $element->condicion, 
                    'plazo_entrega'=> $element->plazo_entrega, 
                    'nro_cuenta_prin'=> $element->nro_cuenta_prin, 
                    'nro_cuenta_alter'=> $element->nro_cuenta_alter, 
                    'nro_cuenta_detra'=> $element->nro_cuenta_detra,
                    'codigo_cuadro_comparativo'=> '',
                    'estado'=>$element->estado,
                    'estado_doc'=>$element->estado_doc,
                    'detalle_pago'=> $element->detalle_pago, 
                    'archivo_adjunto'=> $element->archivo_adjunto,
                    'suma_subtotal'=> $element->suma_subtotal,
                    'codigo_requerimiento'=> []
                    
                ];
            }
        }

        $detalle_orden = DB::table('logistica.log_ord_compra')
        ->select(
            'log_ord_compra.id_orden_compra',
            'log_det_ord_compra.id_detalle_orden',
            'alm_req.codigo as codigo_requerimiento',
            'alm_req.fecha_registro as fecha_registro_requerimiento',
            'oportunidades.codigo_oportunidad',
            'oc_propias.fecha_entrega',
            'guia_com_det.fecha_registro as fecha_ingreso_almacen',
            'oc_propias.fecha_estado',
            'cc.estado_aprobacion',
            'estados_aprobacion.estado as estado_aprobacion'
            )
        ->leftJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_orden_compra', '=', 'log_ord_compra.id_orden_compra')
        ->leftJoin('almacen.guia_com_det', 'guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden')
        ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
        ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
        ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
        ->leftJoin('mgcp_cuadro_costos.estados_aprobacion', 'estados_aprobacion.id', '=', 'cc.estado_aprobacion')
        ->leftJoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
        ->leftJoin('mgcp_acuerdo_marco.oc_propias', 'oc_propias.id_oportunidad', '=', 'oportunidades.id')
        ->where([['log_ord_compra.estado', '!=', 7]])
        ->orderBy('log_ord_compra.fecha','desc')
        ->get();

        $data_detalle_orden=[];
        if(count($ord_compra)>0){
            foreach($detalle_orden as $element){
                
                $data_detalle_orden[]=[
                    'id_orden_compra'=> $element->id_orden_compra,
                    'id_detalle_orden'=> $element->id_detalle_orden,
                    'codigo_requerimiento'=> $element->codigo_requerimiento,
                    'codigo_oportunidad'=> $element->codigo_oportunidad,
                    'fecha_entrega'=> $element->fecha_entrega,
                    'fecha_ingreso_almacen'=> $element->fecha_ingreso_almacen,
                    'estado_aprobacion'=> $element->estado_aprobacion,
                    'fecha_estado'=> $element->fecha_estado,
                    'fecha_registro_requerimiento'=> $element->fecha_registro_requerimiento
                ];
            }
        }

      

        foreach ($data_orden as $ordenKey => $ordenValue) {
            foreach ($data_detalle_orden as $detalleOrdnKey => $detalleOrdenValue) {
                if($ordenValue['id_orden_compra'] == $detalleOrdenValue['id_orden_compra']){
                    if(in_array($detalleOrdenValue['codigo_requerimiento'],$data_orden[$ordenKey]['codigo_requerimiento'])==false){
                        $data_orden[$ordenKey]['codigo_requerimiento'][]=$detalleOrdenValue['codigo_requerimiento'];
                        $data_orden[$ordenKey]['codigo_oportunidad']=$detalleOrdenValue['codigo_oportunidad'];
                        $data_orden[$ordenKey]['fecha_vencimiento_ocam']=$detalleOrdenValue['fecha_entrega'];
                        $data_orden[$ordenKey]['fecha_ingreso_almacen']=$detalleOrdenValue['fecha_ingreso_almacen'];
                        $data_orden[$ordenKey]['estado_aprobacion_cc']=$detalleOrdenValue['estado_aprobacion'];
                        $data_orden[$ordenKey]['fecha_estado']=$detalleOrdenValue['fecha_estado'];
                        $data_orden[$ordenKey]['fecha_registro_requerimiento']=$detalleOrdenValue['fecha_registro_requerimiento'];
                    }
                }
            }
        }

        $output['data'] = $data_orden;
        return $output;
    }

    public function detalleOrden($idOrden){
        $detalle = DB::table('logistica.log_det_ord_compra')
        ->select(
            'log_det_ord_compra.*','alm_item.id_producto','alm_prod.codigo',
            'alm_prod.part_number','alm_cat_prod.descripcion as categoria',
            'alm_subcat.descripcion as subcategoria','alm_req.id_requerimiento',
            'alm_prod.descripcion','alm_und_medida.abreviatura','alm_req.codigo as codigo_req',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color','sis_sede.descripcion as sede_req',
            'oc_propias.orden_am','oportunidades.oportunidad','oportunidades.codigo_oportunidad',
            'entidades.nombre','oc_propias.id as id_oc_propia','oc_propias.url_oc_fisica',
            'users.name as user_name'
        )
        ->leftjoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
        ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
        ->leftjoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
        ->leftjoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
        ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_det_ord_compra.id_unidad_medida')
        ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
        ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
        ->leftjoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
        ->leftjoin('mgcp_cuadro_costos.cc','cc.id','=','alm_req.id_cc')
        ->leftjoin('mgcp_oportunidades.oportunidades','oportunidades.id','=','cc.id_oportunidad')
        ->leftjoin('mgcp_usuarios.users','users.id','=','oportunidades.id_responsable')
        ->leftjoin('mgcp_acuerdo_marco.oc_propias','oc_propias.id_oportunidad','=','oportunidades.id')
        ->leftjoin('mgcp_acuerdo_marco.entidades','entidades.id','=','oportunidades.id_entidad')
        ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'log_det_ord_compra.estado')
        ->where([
            ['log_det_ord_compra.id_orden_compra', '=', $idOrden]
        ])
        ->get();
    return response()->json($detalle);
    }

    public function listaHistorialOrdenes(){
        $data = DB::table('logistica.log_ord_compra')
        ->select(
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
        ->leftjoin('logistica.log_cdn_pago','log_cdn_pago.id_condicion_pago','=','log_ord_compra.id_condicion')
        ->leftjoin('logistica.estados_compra','estados_compra.id_estado','=','log_ord_compra.estado')

        ->leftjoin('logistica.log_ord_compra_pago','log_ord_compra_pago.id_orden_compra','=','log_ord_compra.id_orden_compra')
        ->leftJoin('configuracion.ubi_dis as dis_destino', 'log_ord_compra.ubigeo_destino', '=', 'dis_destino.id_dis')
        ->leftJoin('configuracion.ubi_prov as prov_destino', 'dis_destino.id_prov', '=', 'prov_destino.id_prov')
        ->leftJoin('configuracion.ubi_dpto as dpto_destino', 'prov_destino.id_dpto', '=', 'dpto_destino.id_dpto')

        ->where([
            ['log_ord_compra.estado', '!=', 7]
        ])
        ->orderBy('log_ord_compra.fecha','desc')
        ->get();
        $output['data'] = $data;
        return $output;
    }

    public function mostrarOrden($id_orden){
        $head_orden_compra = DB::table('logistica.log_ord_compra')
        ->select(
            'log_ord_compra.id_orden_compra',
            'log_ord_compra.id_tp_documento',
            'log_ord_compra.codigo as codigo_orden',
            'log_ord_compra.id_moneda',
            'sis_moneda.simbolo as moneda_simbolo',
            'sis_moneda.descripcion as moneda_descripcion',
            'log_ord_compra.codigo_softlink',
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
            DB::raw("(dis_proveedor.descripcion) || ' - ' || (prov_proveedor.descripcion) || ' - ' || (dpto_proveedor.descripcion)  AS ubigeo_proveedor"),
            'log_ord_compra.id_contacto',
            'adm_ctb_contac.nombre as nombre_contacto',
            'adm_ctb_contac.nombre as telefono_contacto',
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
            'log_ord_compra.personal_autorizado',
            DB::raw("concat(pers_aut.nombres,' ',pers_aut.apellido_paterno,' ',pers_aut.apellido_materno) AS nombre_personal_autorizado"),
            'log_ord_compra.estado',
            'adm_estado_doc.estado_doc',
            'adm_estado_doc.bootstrap_color'
        )
        ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
        ->leftJoin('administracion.adm_empresa', 'sis_sede.id_empresa', '=', 'adm_empresa.id_empresa')
        ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
        ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->leftJoin('configuracion.ubi_dis as dis_proveedor', 'adm_contri.ubigeo', '=', 'dis_proveedor.id_dis')
        ->leftJoin('configuracion.ubi_prov as prov_proveedor', 'dis_proveedor.id_prov', '=', 'prov_proveedor.id_prov')
        ->leftJoin('configuracion.ubi_dpto as dpto_proveedor', 'prov_proveedor.id_dpto', '=', 'dpto_proveedor.id_dpto')
        ->leftJoin('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_datos_contacto', '=', 'log_ord_compra.id_contacto')

        ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
        ->leftjoin('logistica.log_cdn_pago','log_cdn_pago.id_condicion_pago','=','log_ord_compra.id_condicion')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','log_ord_compra.estado')
        ->leftjoin('logistica.log_ord_compra_pago','log_ord_compra_pago.id_orden_compra','=','log_ord_compra.id_orden_compra')
        ->leftJoin('configuracion.ubi_dis as dis_destino', 'log_ord_compra.ubigeo_destino', '=', 'dis_destino.id_dis')
        ->leftJoin('configuracion.ubi_prov as prov_destino', 'dis_destino.id_prov', '=', 'prov_destino.id_prov')
        ->leftJoin('configuracion.ubi_dpto as dpto_destino', 'prov_destino.id_dpto', '=', 'dpto_destino.id_dpto')
        ->leftJoin('rrhh.rrhh_trab as trab_aut', 'trab_aut.id_trabajador', '=', 'log_ord_compra.personal_autorizado')
        ->leftJoin('rrhh.rrhh_postu as post_aut', 'post_aut.id_postulante', '=', 'trab_aut.id_postulante')
        ->leftJoin('rrhh.rrhh_perso as pers_aut', 'pers_aut.id_persona', '=', 'post_aut.id_persona')
        ->where([
            ['log_ord_compra.id_orden_compra', '=', $id_orden]
        ])
        ->get();

        $head=[];
        if(count($head_orden_compra)>0){
            foreach ($head_orden_compra as $data) {
                $head = [
                    'id_orden_compra' => $data->id_orden_compra,
                    'id_tp_documento' => $data->id_tp_documento,
                    'id_moneda' => $data->id_moneda,
                    'moneda_simbolo' => $data->moneda_simbolo,
                    'moneda_descripcion' => $data->moneda_descripcion,
                    'codigo_orden' => $data->codigo_orden,
                    'codigo_softlink' => $data->codigo_softlink,
                    'fecha' => $data->fecha,
                    'id_sede' => $data->id_sede,
                    'id_empresa' => $data->id_empresa,
                    'logo_empresa' => $data->logo_empresa,
                    'codigo_sede_empresa' => $data->codigo_sede_empresa,
                    'id_proveedor' => $data->id_proveedor,
                    'id_contribuyente' => $data->id_contribuyente,
                    'razon_social' => $data->razon_social,
                    'nro_documento' => $data->nro_documento,
                    'nro_documento' => $data->nro_documento,
                    'direccion_fiscal' => $data->direccion_fiscal,
                    'ubigeo' => $data->ubigeo,
                    'ubigeo_proveedor' => $data->ubigeo_proveedor,
                    'id_contacto' => $data->id_contacto,
                    'nombre_contacto' => $data->nombre_contacto,
                    'telefono_contacto' => $data->telefono_contacto,
                    'id_condicion' => $data->id_condicion,
                    'condicion' => $data->condicion,
                    'plazo_dias' => $data->plazo_dias,
                    'plazo_entrega' => $data->plazo_entrega,
                    'id_tp_doc' => $data->id_tp_doc,

                    'direccion_destino' => $data->direccion_destino,
                    'ubigeo_destino_id' => $data->ubigeo_destino_id,
                    'ubigeo_destino' => $data->ubigeo_destino,
                    'personal_autorizado' => $data->personal_autorizado,
                    'nombre_personal_autorizado' => $data->nombre_personal_autorizado,
                    'estado' => $data->estado,
                    'estado_doc' => $data->estado_doc

                ];
            }
        }

        $detalle_orden_compra = DB::table('logistica.log_det_ord_compra')
        ->select(
        'log_det_ord_compra.id_detalle_orden as id',
        'log_det_ord_compra.id_detalle_orden',
        'log_det_ord_compra.id_orden_compra',
        'alm_req.id_requerimiento',
        'alm_req.codigo as codigo_requerimiento',
        'log_det_ord_compra.id_producto',
        'log_det_ord_compra.id_item',
        'alm_item.codigo AS codigo_item',
        'alm_prod.descripcion AS descripcion_producto',
        'alm_prod.codigo AS codigo_producto',
        'alm_prod.part_number',
        'log_det_ord_compra.garantia',
        'log_det_ord_compra.estado',
        'log_det_ord_compra.personal_autorizado',
        'log_det_ord_compra.lugar_despacho',
        'log_det_ord_compra.descripcion_adicional',
        'log_det_ord_compra.cantidad',
        'log_det_ord_compra.precio',
        'log_det_ord_compra.id_unidad_medida',
        'alm_und_medida.descripcion AS unidad_medida',
        'log_det_ord_compra.subtotal',
        'log_det_ord_compra.id_detalle_requerimiento',
        'log_det_ord_compra.estado'
    )
    ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_det_ord_compra.id_unidad_medida')
    ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
    ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'log_det_ord_compra.id_producto')
    ->leftJoin('configuracion.sis_usua as sis_usua_aut', 'sis_usua_aut.id_usuario', '=', 'log_det_ord_compra.personal_autorizado')
    ->leftJoin('rrhh.rrhh_trab as trab_aut', 'trab_aut.id_trabajador', '=', 'sis_usua_aut.id_trabajador')
    ->leftJoin('rrhh.rrhh_postu as post_aut', 'post_aut.id_postulante', '=', 'trab_aut.id_postulante')
    ->leftJoin('rrhh.rrhh_perso as pers_aut', 'pers_aut.id_persona', '=', 'post_aut.id_persona')
    ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
    ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
    ->where([
        ['log_det_ord_compra.id_orden_compra', '=', $id_orden]
    ])
    ->get();

    $detalle = [];
    $idDetalleReqList=[];
    if(count($detalle_orden_compra)>0){
        foreach ($detalle_orden_compra as $data) {
            $detalle[] = [
                'id' => $data->id,
                'id_detalle_orden' => $data->id_detalle_orden,
                'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                'id_requerimiento' => $data->id_requerimiento,
                'codigo_requerimiento' => $data->codigo_requerimiento,
                'id_item' => $data->id_item,
                'id_tipo_item' => 1,
                'id_producto' => $data->id_producto,
                'codigo_item' => $data->codigo_item,
                'codigo_producto' => $data->codigo_producto,
                'descripcion' => $data->descripcion_producto,
                'descripcion_producto' => $data->descripcion_producto,
                'part_number' => $data->part_number,
                'descripcion_adicional' => $data->descripcion_adicional,
                'cantidad' => $data->cantidad,
                'cantidad_a_comprar' => $data->cantidad,
                'id_unidad_medida' => $data->id_unidad_medida,
                'unidad_medida' => $data->unidad_medida,
                'precio_unitario' => $data->precio,
                // 'flete' => $data->flete,
                // 'porcentaje_descuento' => $data->porcentaje_descuento,
                // 'monto_descuento' => $data->monto_descuento,
                'subtotal' => $data->subtotal,
                // 'plazo_entrega' => $data->plazo_entrega,
                // 'incluye_igv' => $data->incluye_igv,
                'garantia' => $data->garantia,
                'lugar_despacho' => $data->lugar_despacho,
                'estado' => $data->estado
                // 'nombre_personal_autorizado' => $data->nombre_personal_autorizado 
            ];
            $idDetalleReqList[]=$data->id_detalle_requerimiento;
        }
    }

    $codigoReqList=[];
    $idCcList=[];
    if(count($idDetalleReqList) > 0){
        $req = DB::table('almacen.alm_det_req')
        ->select(
            'alm_req.id_requerimiento',
            'alm_req.codigo',
            'alm_req.concepto',
            'alm_req.id_cc'
            )
        ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
        ->whereIn('alm_det_req.id_detalle_requerimiento',$idDetalleReqList)
        ->distinct()
        ->get();

        if(count($req) >0){
            foreach($req as $data){
                $codigoReqList[]=$data->codigo;
                $idCcList[]=$data->id_cc;
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
        ->whereIn('cc.id',$idCcList)
        ->distinct()
        ->get();

        $codigo_oportunidad=count($cdc)>0 ?$cdc->first()->codigo_oportunidad:'';
        $nombre_responsable=count($cdc)>0 ?$cdc->first()->nombre_responsable:'';
        }

        $codigoReqText = implode(",", $codigoReqList);

        $result = [
            'head' => $head,
            'detalle' => $detalle 
        ];
        
        $result['head']['codigo_requerimiento']=$codigoReqText;
        $result['head']['codigo_cc']= isset($codigo_oportunidad)?$codigo_oportunidad:'';
        $result['head']['nombre_responsable_cc']=isset($nombre_responsable)?$nombre_responsable:'';


        return response()->json($result);

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
    
    public function get_orden_por_requerimiento($id_orden_compra)
    {
        
        $head_orden_compra = DB::table('logistica.log_ord_compra')
            ->select(
                'log_ord_compra.id_orden_compra',
                'log_ord_compra.id_tp_documento',
                'adm_tp_docum.descripcion AS tipo_documento',
                'log_ord_compra.fecha',
                'log_ord_compra.id_usuario',
                DB::raw("concat(pers.nombres,' - ',pers.apellido_paterno,' - ',pers.apellido_materno) as nombre_usuario"),
                'log_ord_compra.id_moneda',
                'sis_moneda.simbolo as moneda_simbolo',
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
                'log_ord_compra.id_condicion',
                'log_cdn_pago.descripcion AS condicion_pago',
                'log_ord_compra.plazo_dias',
                'log_ord_compra.id_cta_principal',
                'cta_prin.nro_cuenta as nro_cuenta_principal',
                'log_ord_compra.id_cta_alternativa',
                'cta_alter.nro_cuenta as nro_cuenta_alternativa',
                'log_ord_compra.id_cta_detraccion',
                'cta_detra.nro_cuenta as nro_cuenta_detraccion',
                'log_ord_compra.plazo_entrega',
                'log_ord_compra.en_almacen',
                'log_ord_compra.id_occ',
                'log_ord_compra.id_sede',
                'log_ord_compra.direccion_destino',
                'log_ord_compra.ubigeo_destino',
                DB::raw("(dis_destino.descripcion) || ' - ' || (prov_destino.descripcion) || ' - ' || (dpto_destino.descripcion)  AS ubigeo_destino"),
                'log_ord_compra.personal_autorizado',
                DB::raw("concat(pers_aut.nombres,' ',pers_aut.apellido_paterno,' ',pers_aut.apellido_materno) AS nombre_personal_autorizado"),
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
                'log_ord_compra.personal_autorizado',
                'log_ord_compra.id_contacto',
                'adm_ctb_contac.nombre as nombre_contacto',
                'adm_ctb_contac.telefono as telefono_contacto',
                'adm_ctb_contac.email as email_contacto',
                'adm_ctb_contac.cargo as cargo_contacto',
                'adm_ctb_contac.direccion as direccion_contacto',
                'adm_ctb_contac.horario as horario_contacto',
                DB::raw("(dis_contac.descripcion) || ' ' || (prov_contac.descripcion) || ' ' || (dpto_contac.descripcion)  AS ubigeo_contacto")

            )
            ->Join('administracion.adm_tp_docum', 'adm_tp_docum.id_tp_documento', '=', 'log_ord_compra.id_tp_documento')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'log_ord_compra.id_usuario')
            ->leftJoin('rrhh.rrhh_trab as trab', 'trab.id_trabajador', '=', 'sis_usua.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu as post', 'post.id_postulante', '=', 'trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as pers', 'pers.id_persona', '=', 'post.id_persona')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_ord_compra.id_condicion')
            // ->leftJoin('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_datos_contacto', '=', 'log_ord_compra.personal_responsable')
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            // ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'log_ord_compra.id_requerimiento')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->leftJoin('contabilidad.adm_contri as contab_contri', 'contab_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi as contab_sis_identi', 'contab_sis_identi.id_doc_identidad', '=', 'contab_contri.id_doc_identidad')
            ->leftJoin('contabilidad.adm_contri as contrib', 'adm_empresa.id_contribuyente', '=', 'contrib.id_contribuyente')
            ->leftjoin('contabilidad.adm_cta_contri as cta_prin','cta_prin.id_cuenta_contribuyente','=','log_ord_compra.id_cta_principal')
            ->leftjoin('contabilidad.adm_cta_contri as cta_alter','cta_alter.id_cuenta_contribuyente','=','log_ord_compra.id_cta_alternativa')
            ->leftjoin('contabilidad.adm_cta_contri as cta_detra','cta_detra.id_cuenta_contribuyente','=','log_ord_compra.id_cta_detraccion')
            ->leftJoin('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_datos_contacto', '=', 'log_ord_compra.id_contacto')
            ->leftJoin('rrhh.rrhh_trab as trab_aut', 'trab_aut.id_trabajador', '=', 'log_ord_compra.personal_autorizado')
            ->leftJoin('rrhh.rrhh_postu as post_aut', 'post_aut.id_postulante', '=', 'trab_aut.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as pers_aut', 'pers_aut.id_persona', '=', 'post_aut.id_persona')
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
                ['log_ord_compra.id_orden_compra', '=', $id_orden_compra],
                ['log_ord_compra.estado', '!=', 7]
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
            // DB::raw("(pers_aut.nombres) || ' ' || (pers_aut.apellido_paterno) || ' ' || (pers_aut.apellido_materno) AS nombre_personal_autorizado"),
            'log_det_ord_compra.lugar_despacho',
            'log_det_ord_compra.descripcion_adicional',
            'log_det_ord_compra.cantidad',
            'log_det_ord_compra.precio',
            'log_det_ord_compra.id_unidad_medida',
            'alm_und_medida.descripcion AS unidad_medida',
            'log_det_ord_compra.subtotal',
            'log_det_ord_compra.id_detalle_requerimiento'
        )
        ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_det_ord_compra.id_unidad_medida')
        ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
        ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
        ->leftJoin('configuracion.sis_usua as sis_usua_aut', 'sis_usua_aut.id_usuario', '=', 'log_det_ord_compra.personal_autorizado')
        ->leftJoin('rrhh.rrhh_trab as trab_aut', 'trab_aut.id_trabajador', '=', 'sis_usua_aut.id_trabajador')
        ->leftJoin('rrhh.rrhh_postu as post_aut', 'post_aut.id_postulante', '=', 'trab_aut.id_postulante')
        ->leftJoin('rrhh.rrhh_perso as pers_aut', 'pers_aut.id_persona', '=', 'post_aut.id_persona')
        ->where([
            ['log_det_ord_compra.id_orden_compra', '=', $id_orden_compra],
            ['log_det_ord_compra.estado', '!=', 7]
        ])
        ->get();


        $head = [];
        $detalle = [];

        if(count($head_orden_compra)>0){
            foreach ($head_orden_compra as $data) {
                $head = [
                    'id_orden_compra' => $data->id_orden_compra,
                    'logo_empresa'=>$data->logo_empresa,
                    'codigo' => $data->codigo,
                    'fecha_orden' => $data->fecha_orden,
                    'tipo_documento' => $data->tipo_documento,
                    // 'codigo_requerimiento' => $data->codigo_requerimiento,
                    'fecha_registro' => $data->fecha,
                    'moneda_simbolo' => $data->moneda_simbolo, 
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

                        'contacto'=>[
                            'nombre_contacto' => $data->nombre_contacto,
                            'telefono_contacto' => $data->telefono_contacto,
                            'email_contacto' => $data->email_contacto,
                            'cargo_contacto' => $data->cargo_contacto,
                            'direccion_contacto' => $data->direccion_contacto,
                            'horario_contacto' => $data->horario_contacto,
                            'ubigeo_contacto' => $data->ubigeo_contacto
                        ],
                    ], 
                
                    'condicion_compra'=>[
                        'id_condicion' => $data->id_condicion,
                        'condicion_pago' => $data->condicion_pago,
                        'plazo_dias' => $data->plazo_dias,
                        'plazo_entrega' => $data->plazo_entrega


                    ],
                    'datos_para_despacho'=>[
                        'id_empresa' => $data->id_empresa,
                        'sede'=>$data->codigo_sede_empresa,   
                        'razon_social_empresa' => $data->razon_social_empresa,
                        'tipo_doc_empresa' => $data->tipo_doc_empresa,
                        'nro_documento_empresa' => $data->nro_documento_empresa,
                        'direccion_sede' => $data->direccion_fiscal_empresa_sede,
                        'direccion_destino'=>$data->direccion_destino,
                        'ubigeo_destino'=>$data->ubigeo_destino,
                        'personal_autorizado' => $data->personal_autorizado,
                        'nombre_personal_autorizado' => $data->nombre_personal_autorizado 
                    ],
                    'facturar_a_nombre'=>[
                        'id_empresa' => $data->id_empresa,
                        'razon_social_empresa' => $data->razon_social_empresa,
                        'tipo_doc_empresa' => $data->tipo_doc_empresa,
                        'nro_documento_empresa' => $data->nro_documento_empresa,
                        'direccion_fiscal_empresa' => $data->direccion_fiscal_empresa,
                        'ubigeo_empresa'=>$data->ubigeo_empresa,
                        // 'direccion_sede' => $data->direccion_fiscal_empresa_sede,
                        // 'telefono_empresa' => $data->telefono_empresa,
                        'email_empresa' => $data->email_empresa,
                        'sede'=>$data->codigo_sede_empresa
                    ],
                    'nombre_usuario' => $data->nombre_usuario
                ];

    
            }
        }

        $idDetalleReqList=[];
        if(count($detalle_orden_compra)>0){
            foreach ($detalle_orden_compra as $data) {
                $detalle[] = [
                    'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                    'id_item' => $data->id_item,
                    'codigo_item' => $data->codigo_item,
                    'codigo_producto' => $data->codigo_producto,
                    'descripcion_producto' => $data->descripcion_producto,
                    'part_number' => $data->part_number,
                    'descripcion_adicional' => $data->descripcion_adicional,
                    'cantidad' => $data->cantidad,
                    'id_unidad_medida' => $data->id_unidad_medida,
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
                $idDetalleReqList[]=$data->id_detalle_requerimiento;
            }
        }

        $codigoReqList=[];
        $idCcList=[];
        if(count($idDetalleReqList) > 0){
            $req = DB::table('almacen.alm_det_req')
            ->select(
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.concepto',
                'alm_req.id_cc'
                )
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->whereIn('alm_det_req.id_detalle_requerimiento',$idDetalleReqList)
            ->distinct()
            ->get();

            if(count($req) >0){
                foreach($req as $data){
                    $codigoReqList[]=$data->codigo;
                    $idCcList[]=$data->id_cc;
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
            ->whereIn('cc.id',$idCcList)
            ->distinct()
            ->get();

            $codigo_oportunidad=count($cdc)>0 ?$cdc->first()->codigo_oportunidad:'';
            $nombre_responsable=count($cdc)>0 ?$cdc->first()->nombre_responsable:'';
            // dd( $cdc );

        }





        $codigoReqText = implode(",", $codigoReqList);

        $result = [
            'head' => $head,
            'detalle' => $detalle 
        ];
        
        $result['head']['codigo_requerimiento']=$codigoReqText;
        $result['head']['codigo_cc']= isset($codigo_oportunidad)?$codigo_oportunidad:'';
        $result['head']['nombre_responsable_cc']=isset($nombre_responsable)?$nombre_responsable:'';
        
        return $result;
    }
    public function imprimir_orden_por_requerimiento_pdf($id_orden_compra)
    {
        $ordenArray = $this->get_orden_por_requerimiento($id_orden_compra);
        // return $ordenArray;
        $sizeOrdenHeader=count($ordenArray['head']);
        
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
                }

            </style>
            </head>
            <body>';
            
                if($ordenArray['head']['logo_empresa'] != null){
                    $html.='<img src=".'.$ordenArray['head']['logo_empresa'].'" alt="Logo" height="75px">';
                }

            $html.='
            
                <br>
                <hr>
                <h1><center>' . $ordenArray['head']['tipo_documento'] . '<br>' . $ordenArray['head']['codigo'] . '</center></h1>
                <table border="0" >
                    <tr>
                        <td nowrap  width="15%" class="subtitle verticalTop">Sr.(s):</td>
                        <td width="50%" class="verticalTop">' . $ordenArray['head']['proveedor']['nro_documento_proveedor'].' - '.$ordenArray['head']['proveedor']['razon_social_proveedor'] . '</td>
                        <td nowrap  width="15%" class="subtitle verticalTop">Fecha de Emisión:</td>
                        <td>' . substr($ordenArray['head']['fecha_orden'], 0, 11) . '</td>
                    </tr>
                    <tr>
                        <td nowrap  width="15%" class="subtitle">Dirección:</td>
                        <td class="verticalTop">' . $ordenArray['head']['proveedor']['direccion_fiscal_proveedor'] .'<br>'.$ordenArray['head']['proveedor']['ubigeo_proveedor'] .'</td>
                        <td nowrap  width="15%" class="subtitle verticalTop"> Teléfono:</td>
                        <td class="verticalTop">' . $ordenArray['head']['proveedor']['telefono_proveedor']. '</td>
                    </tr>
                    <tr>
                        <td nowrap  width="15%" class="subtitle">Contacto:</td>
                        <td class="verticalTop">' . $ordenArray['head']['proveedor']['contacto']['nombre_contacto'] . ' - '.$ordenArray['head']['proveedor']['contacto']['cargo_contacto'] . '</td>
                         
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

                <table width="100%" class="tablePDF" border="0" style="font-size:8px;">
                <thead>
                    <tr class="subtitle">
                        <td width="3%">Código</td>
                        <td width="3%">Part Number</td>
                        <td width="20%">Descripción</td>
                        <td width="3%">Und</td>
                        <td width="3%">Cant.</td>
                        <td width="3%">Precio</td>
                        <td width="3%">Descuento</td>
                        <td width="5%">Total</td>
                    </tr>   
                </thead>';

        // $total = 0;
        $monto_neto=0;
        foreach ($ordenArray['detalle'] as $key => $data) {
            $monto_neto += ($data['cantidad'] * $data['precio']) ;
        }

        foreach ($ordenArray['detalle'] as $key => $data) {
          
            $igv =(($monto_neto)*0.18);
            $monto_total =($monto_neto+$igv);


            $html .= '<tr style="text-align:left">';
            // $html .= '<td>' . ($key + 1) . '</td>';
            $html .= '<td>' . $data['codigo_producto'] . '</td>';
            $html .= '<td>' . $data['part_number'] . '</td>';
            if($data['descripcion_adicional'] != null && strlen($data['descripcion_adicional']) > 0){

                $html .= '<td>' . ($data['codigo_item'] ? $data['codigo_item'] : '0') . ' - ' . ($data['descripcion_adicional'] ? $data['descripcion_adicional'] : $data['descripcion_adicional']) . '</td>';

            }else{
                $html .= '<td>' . ($data['codigo_item'] ? $data['codigo_item'] : '0') . ' - ' . ($data['descripcion_producto'] ? $data['descripcion_producto'] : $data['descripcion_adicional']) . '</td>';
            }
            $html .= '<td>' . $data['unidad_medida'] . '</td>';
            $html .= '<td style="text-align:center">' . $data['cantidad'] . '</td>';
            $html .= '<td style="text-align:center">' . number_format($data['precio'],2,'.','') . '</td>';
            // $html .= '<td class="right">' . number_format((($data['cantidad'] * $data['precio']) - (($data['cantidad']* $data['precio'])/1.18)),2,'.','') . '</td>';
            $html .= '<td style="text-align:right"> </td>';
            $html .= '<td style="text-align:right">' . number_format($data['cantidad'] * $data['precio'],2,'.','') . '</td>';
            $html .= '</tr>';
            // $total = $total + ($data['cantidad'] * $data['precio']);
        }



        $html .= '
                <tr>
                    <td class="right noBorder textBold"  colspan="7">Monto Neto '.$ordenArray['head']['moneda_simbolo'].'</td>
                    <td class="right  noBorder textBold">' . number_format($monto_neto,2,'.','') . '</td>
                </tr>
                <tr>
                    <td class="right noBorder textBold"  colspan="7">IGV '.$ordenArray['head']['moneda_simbolo'].'</td>
                    <td class="right noBorder textBold">' . number_format($igv,2,'.','') . '</td>
                </tr>
                <tr>
                    <td class="right noBorder textBold"  colspan="7">Monto Total '.$ordenArray['head']['moneda_simbolo'].'</td>
                    <td class="right noBorder textBold">' . number_format($monto_total,2,'.','') . '</td>
                </tr>
                </table>
                <br>
                <br>';
                
        
                $html.='
                <table width="100%" border=0>
                <caption class="left subtitle" style="padding-bottom:10px; font-size:0.6rem">Condición de Compra:</caption>

                <tr>
                <td nowrap  width="15%" class="subtitle">Forma de Pago: </td>
                <td  class="verticalTop left">' . $ordenArray['head']['condicion_compra']['condicion_pago'].' '.(($ordenArray['head']['condicion_compra']['id_condicion'] ==2)?$ordenArray['head']['condicion_compra']['plazo_dias']. ' días':''). '</td>';

            
            $html.=' 
                    <td width="15%" class="verticalTop subtitle">Plazo entrega: </td>
                    <td class="verticalTop">' . $ordenArray['head']['condicion_compra']['plazo_entrega'].' Días</td>
            
                </tr>
                <tr>
                    <td width="15%" class="verticalTop subtitle">CDC / Req.: </td>
                    <td class="verticalTop">' . ($ordenArray['head']['codigo_cc']?$ordenArray['head']['codigo_cc']:$ordenArray['head']['codigo_requerimiento']) . '</td
                    <td nowrap width="15%" class="verticalTop subtitle">Ejecutivo Responsable: </td>
                    <td class="verticalTop ">' . ($ordenArray['head']['nombre_responsable_cc']) . '</td
                </tr>
                </table>
                <br>
                ';                
       $html.='
                <table width="100%" border=0>
                <caption class="left subtitle" style="padding-bottom:10px; font-size:0.6rem">Datos para el Despacho:</caption>

                <tr>
                    <td nowrap  width="15%" class="verticalTop subtitle">Destino / Dirección: </td>
                    <td class="verticalTop">' . $ordenArray['head']['datos_para_despacho']['direccion_destino'] .'<br>'.$ordenArray['head']['datos_para_despacho']['ubigeo_destino'] .'</td>
                    <td width="15%" class="verticalTop subtitle">Autorizado:</td>
                    <td class="verticalTop">' . $ordenArray['head']['datos_para_despacho']['nombre_personal_autorizado'] .'</td>
                </tr>
                </table>
                <br>
                
        ';

        $html.='
            <table width="100%" border=0>
                <caption class="left subtitle" style="padding-bottom:10px; font-size:0.6rem">Facturar a Nombre:</caption>

                <tr>
                    <td nowrap  width="15%" class="verticalTop subtitle">Razón Social: </td>
                    <td class="verticalTop">' . $ordenArray['head']['facturar_a_nombre']['razon_social_empresa'].'</td>
                </tr>
                <tr>
                    <td nowrap  width="15%" class="verticalTop subtitle">RUC: </td>
                    <td class="verticalTop">' . $ordenArray['head']['facturar_a_nombre']['nro_documento_empresa'].'</td>
                </tr>
                <tr>
                    <td nowrap  width="15%" class="verticalTop subtitle">Dirección: </td>
                    <td class="verticalTop">' .$ordenArray['head']['facturar_a_nombre']['direccion_fiscal_empresa'] .',<br>'.$ordenArray['head']['facturar_a_nombre']['ubigeo_empresa'].'</td>

                </tr>
            </table>
            <br>
        ';

        $html.= '<br>

                    <footer>
                        <p style="font-size:7px; " class="pie_de_pagina">GENERADO POR:' . $ordenArray['head']['nombre_usuario'] .  '</p>
                        <p style="font-size:7px; " class="pie_de_pagina">' . $ordenArray['head']['fecha_registro'] .  '</p>
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

    function cambioElEstadoActualDetalleReq($id_detalle_requerimiento){
            $alm_det_req = DB::table('almacen.alm_det_req')
            ->select(
                'alm_det_req.*'
            )
            ->where('alm_det_req.id_detalle_requerimiento',$id_detalle_requerimiento)
            ->whereNotIn('alm_det_req.estado', [1,2,15,5,7])
            ->get();

            if(count($alm_det_req)>0){
                return true;
            }else{
                return false;
            }

    }
    function cambioElEstadoActualReq($id_requerimiento){
            $alm_req = DB::table('almacen.alm_req')
            ->select(
                'alm_req.*'
            )
            ->where('alm_req.id_requerimiento',$id_requerimiento)
            ->whereNotIn('alm_req.estado', [1,2,15,5,7])
            ->get();

            if(count($alm_req)>0){
                return true;
            }else{
                return false;
            }

    }

    public function guardar_orden_por_requerimiento(Request $request){
        try {
            DB::beginTransaction();

            $usuario = Auth::user()->id_usuario;
            $tp_doc = ($request->id_tp_documento !== null ? $request->id_tp_documento : 2);
            $codigo = $this->nextCodigoOrden($tp_doc);
            $guardarEnRequerimiento = $request->guardarEnRequerimiento;

            $id_orden = DB::table('logistica.log_ord_compra')
            ->insertGetId(
                [
                    'id_grupo_cotizacion' => isset($request->id_grupo_cotizacion)?$request->id_grupo_cotizacion:null,
                    'id_tp_documento' =>  $tp_doc,
                    'fecha' => date('Y-m-d H:i:s'),
                    'id_usuario' => $usuario?$usuario:null,
                    'id_moneda' => ($request->id_moneda?$request->id_moneda:null),
                    'id_proveedor' => $request->id_proveedor,
                    'id_contacto' => $request->id_contacto_proveedor?$request->id_contacto_proveedor:null,
                    'codigo' => $codigo?$codigo:null,
                    'plazo_entrega' => $request->plazo_entrega?$request->plazo_entrega:null,
                    'id_condicion' => $request->id_condicion?$request->id_condicion:null,
                    'plazo_dias' => $request->plazo_dias?$request->plazo_dias:null,
                    'id_cotizacion' => $request->id_cotizacion?$request->id_cotizacion:null,
                    'id_tp_doc' =>  isset($request->id_tp_doc)?$request->id_tp_doc:null,
                    'personal_autorizado' => $request->id_trabajador?$request->id_trabajador:null,
                    'id_occ' => $request->id_cc?$request->id_cc:null,
                    'id_sede' => $request->id_sede?$request->id_sede:null,
                    'direccion_destino' => $request->direccion_destino?$request->direccion_destino:null,
                    'ubigeo_destino' => isset($request->id_ubigeo_destino)?$request->id_ubigeo_destino:null,
                    'en_almacen' => false,
                    'estado' => 1,
                    'codigo_softlink' => ($request->codigo_orden!==null ? $request->codigo_orden : ''),
                ],
                'id_orden_compra'
            );

            $dataDetalle = $request->detalle;

            $allidReqList=[];
            $allIdDetReqList=[];
            foreach($dataDetalle as $d) {
                $allIdDetReqList[]=$d['id_detalle_requerimiento'];

                if($d['cantidad_a_comprar'] > 0 && $d['estado'] != 7){
                    if($guardarEnRequerimiento == false){
                        DB::table('logistica.log_det_ord_compra')
                        ->insert([
                            'id_orden_compra'=>$id_orden,
                            'id_item'=> ($d['id_item'] ? $d['id_item'] : null),
                            'id_producto'=> (isset($d['id_producto']) ? $d['id_producto'] : null),
                            'id_detalle_requerimiento'=> ($d['id_detalle_requerimiento'] ? $d['id_detalle_requerimiento'] : null),
                            'cantidad'=> $d['cantidad_a_comprar'],
                            'id_unidad_medida'=> $d['id_unidad_medida'],
                            'precio'=> $d['precio_unitario'],
                            'descripcion_adicional'=> $d['descripcion_adicional'],
                            // 'subtotal'=> ($d->precio_referencial * $d->cantidad),
                            'subtotal'=> $d['subtotal']?$d['subtotal']:0,
                            'tipo_item_id'=> (isset($d['id_tipo_item'])?$d['id_tipo_item']:null),
                            'estado'=> 1
                            // 'fecha_registro'=> date('Y-m-d H:i:s')
                        ]);
                    }

                }
            }

            $nuevoEstadoDeRequerimiento=[];
            if(isset($id_orden) and $id_orden >0){
                 $this->actualizarEstadoRequerimientoPorOrdenCreada($id_orden,$codigo);
            }
            

            // $idRequerimientoAtentidosTotalList=[];
            // $idRequerimientoAtentidosParcialList=[];

            // $sizeDataDetalle = count($dataDetalle);
            // $countAtendido=0;
            
            //     foreach ($dataDetalle as $d) {
            //         if(($d['id_tipo_item']==1) && (($d['cantidad_a_comprar'] + ($d['stock_comprometido']?$d['stock_comprometido']:0) ) == $d['cantidad']) ){
            //             $countAtendido+=1;
            //             $idRequerimientoAtentidosTotalList[]=$d['id_requerimiento'];

            //             if($this->cambioElEstadoActualDetalleReq($d['id_detalle_requerimiento']) == false ){
            //                 DB::table('almacen.alm_det_req')
            //                 ->where('id_detalle_requerimiento',$d['id_detalle_requerimiento'])
            //                 ->update(
            //                     [
            //                         'estado'=>5, //atendido total
            //                         'stock_comprometido'=> $d['stock_comprometido']?$d['stock_comprometido']:null
            //                     ] 
            //                 ); 
            //             }

            //         }
            //         if(($d['id_tipo_item']==1) && (($d['cantidad_a_comprar'] + ($d['stock_comprometido']?$d['stock_comprometido']:0) ) < $d['cantidad'])){
            //             $idRequerimientoAtentidosParcialList[]=$d['id_requerimiento'];
            //             if($this->cambioElEstadoActualDetalleReq($d['id_detalle_requerimiento']) == false ){
            //                 DB::table('almacen.alm_det_req')
            //                 ->where('id_detalle_requerimiento',$d['id_detalle_requerimiento'])
            //                 ->update(
            //                     [
            //                         'estado'=>15, // atendido parcial
            //                         'stock_comprometido'=> $d['stock_comprometido']?$d['stock_comprometido']:0
            //                     ]
                                
            //                 ); 
            //             }
            //         }

            //         if(($d['id_tipo_item']==2)){ // servicio
            //             DB::table('almacen.alm_det_req')
            //             ->where('id_detalle_requerimiento',$d['id_detalle_requerimiento'])
            //             ->update(
            //                 [
            //                     'estado'=>5,
            //                     'stock_comprometido'=> $d['stock_comprometido']?$d['stock_comprometido']:0
            //                 ]
                            
            //             ); 
            //         }
            //     } 

            //     $idRequerimientoAtentidosTotalList = array_unique($idRequerimientoAtentidosTotalList);
            //     $idRequerimientoAtentidosParcialList = array_unique($idRequerimientoAtentidosParcialList);

            //     // Debugbar::info($idRequerimientoAtentidosTotalList);
            //     // Debugbar::info($idRequerimientoAtentidosParcialList);

                
            //     if(count($idRequerimientoAtentidosTotalList)>0){


            //         foreach ($idRequerimientoAtentidosTotalList as $id_req) {

            //             if($countAtendido ==$sizeDataDetalle){
            //                     DB::table('almacen.alm_req')
            //                     ->where('id_requerimiento',$id_req)
            //                     ->update(['estado'=>5]); // atendido total
        
            //                     DB::table('almacen.alm_req_obs')
            //                     ->insert([  'id_requerimiento'=>$id_req,
            //                                 'accion'=>'ATENDIDO TOTAL',
            //                                 'descripcion'=>'Se generó Orden de Compra '.$codigo,
            //                                 'id_usuario'=>$usuario,
            //                                 'fecha_registro'=>date('Y-m-d H:i:s')
            //                     ]);
                            
            //             }else{
            //                     DB::table('almacen.alm_req')
            //                     ->where('id_requerimiento',$id_req)
            //                     ->update(['estado'=>15]); // atendido parcial
        
            //                     DB::table('almacen.alm_req_obs')
            //                     ->insert([  'id_requerimiento'=>$id_req,
            //                                 'accion'=>'ATENDIDO PARCIAL',
            //                                 'descripcion'=>'Se generó Orden de Compra '.$codigo,
            //                                 'id_usuario'=>$usuario,
            //                                 'fecha_registro'=>date('Y-m-d H:i:s')
            //                     ]);

            //             }

            //         }

            //     }
            //     if(count($idRequerimientoAtentidosParcialList)>0){
            //         // Debugbar::info('$idRequerimientoAtentidosParcialList)>0');
                    
            //         foreach ($idRequerimientoAtentidosParcialList as $key => $id_req) {
            //             if($this->cambioElEstadoActualReq($id_req) == false ){
            //                 DB::table('almacen.alm_req')
            //                 ->where('id_requerimiento',$id_req)
            //                 ->update(['estado'=>15]); // atendido parcial
                            
            //                 DB::table('almacen.alm_req_obs')
            //                 ->insert([  'id_requerimiento'=>$id_req,
            //                 'accion'=>'ATENDIDO PARCIAL',
            //                 'descripcion'=>'Se generó Orden de Compra '.$codigo,
            //                 'id_usuario'=>$usuario,
            //                 'fecha_registro'=>date('Y-m-d H:i:s')
            //                 ]);
            //             }
            //         }
            //     } 


        DB::commit();
            return response()->json($id_orden);

        } catch (\PDOException $e) {
            DB::rollBack();
        }

    }


    function actualizarEstadoRequerimientoPorOrdenCreada($idOrden,$codigo){
        $det_orden = DB::table('logistica.log_det_ord_compra')
        ->select(
            'log_det_ord_compra.*'
            )
        ->where('log_det_ord_compra.id_orden_compra',$idOrden)
        ->get();

        $hasVinculoDetalleRequerimiento=0;
        $hasVinculoDetalleCuadroComparativo=0;
        $idDetalleRequerimientoList=[];
        $idDetalleCuadroComparativoList=[];

        foreach ($det_orden as $value) {
            if($value->id_detalle_requerimiento >0){
                $hasVinculoDetalleRequerimiento++;
                $idDetalleRequerimientoList[]=$value->id_detalle_requerimiento;
            }elseif($value->detalle_cuadro_comparativo_id >0){
                $hasVinculoDetalleCuadroComparativo++;
                $idDetalleCuadroComparativoList[]=$value->detalle_cuadro_comparativo_id;
            }
        }


        if($hasVinculoDetalleRequerimiento >0){

            $alm_det_req = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.*')
            ->whereIn('alm_det_req.id_detalle_requerimiento',$idDetalleRequerimientoList)
            ->get();

            // get all id_requerimiento
            $idRequerimientoList=[];
            foreach ($alm_det_req as $value) {
                $idRequerimientoList[]=$value->id_requerimiento;
            }
            $idRequerimientoList = array_unique($idRequerimientoList);
            // Debugbar::info($idRequerimientoList);

            $arrEstadoDetalle=[];
            $arr=[];
            foreach ($idRequerimientoList as $idReq) {
                foreach ($alm_det_req as $detalleReq) {
                    foreach ($det_orden as $detalleOrden) {
                        if($idReq ==$detalleReq->id_requerimiento ){
                            // Debugbar::info($detalleReq->id_detalle_requerimiento);
                            // Debugbar::info($detalleOrden->id_detalle_requerimiento);

                            if($detalleOrden->id_detalle_requerimiento == $detalleReq->id_detalle_requerimiento){
                                if((($detalleOrden->cantidad + ($detalleReq->stock_comprometido>0?$detalleReq->stock_comprometido:0)) == $detalleReq->cantidad)){
                                    // es una atencion total 
                                    $arrEstadoDetalle[] = [   
                                                'id_requerimiento'=>$idReq, 
                                                'id_detalle_requerimiento'=>$detalleReq->id_detalle_requerimiento,
                                                'estado'=>'ATENDIDO_TOTAL'
                                                ];
                                }else{
                                    // es una atencion parcial
                                    $arrEstadoDetalle[] = [   
                                        'id_requerimiento'=>$idReq, 
                                        'id_detalle_requerimiento'=>$detalleReq->id_detalle_requerimiento,
                                        'estado'=>'ATENDIDO_PARCIAL'
                                        ];
                                }
                            }
                        }
                    }
                }
            }
            foreach ($arrEstadoDetalle as $value) {
                    $arr[$value['id_requerimiento']][] = $value['estado'];
            }

            $arrEstadoReq=[];
            foreach ($arr as $k => $value){
                $arr_unique=array_unique($value);
                if(count($arr_unique) >1){
                    $arrEstadoReq[$k]='ATENDIDO_PARCIAL';
            
                }else{
                    $arrEstadoReq[$k]=$arr_unique[0];
                }
            
            }

            // actualizando estados de req cabecera
            foreach ($arrEstadoReq as $key => $value) {
                // Debugbar::info($key);
                // Debugbar::info("--value--");
                // Debugbar::info($value);
                // Debugbar::info("--end value--");
                // Debugbar::info("--value[0]--");
                // Debugbar::info($value[0]);
                // Debugbar::info("--end value[0]--");
                if($value=='ATENDIDO_PARCIAL'){

                    DB::table('almacen.alm_req')
                    ->where('id_requerimiento',$key)
                    ->update(['estado'=>15]);  

                    DB::table('almacen.alm_req_obs')
                    ->insert([  'id_requerimiento'=>$key,
                                'accion'=>'ATENDIDO PARCIAL',
                                'descripcion'=>'Se generó Orden de Compra '.$codigo,
                                'id_usuario'=>Auth::user()->id_usuario,
                                'fecha_registro'=>date('Y-m-d H:i:s')
                    ]);

                }elseif($value=='ATENDIDO_TOTAL'){
                    DB::table('almacen.alm_req')
                    ->where('id_requerimiento',$key)
                    ->update(['estado'=>5]);  

                    DB::table('almacen.alm_req_obs')
                    ->insert([  'id_requerimiento'=>$key,
                                'accion'=>'ATENDIDO TOTAL',
                                'descripcion'=>'Se generó Orden de Compra '.$codigo,
                                'id_usuario'=>Auth::user()->id_usuario,
                                'fecha_registro'=>date('Y-m-d H:i:s')
                    ]);
                }
            }
            // actualizando esatado detalle req
            foreach ($arrEstadoDetalle as $value) {
            
                if($value['estado'] == 'ATENDIDO_PARCIAL'){
                    DB::table('almacen.alm_det_req')
                    ->where('id_detalle_requerimiento',$value['id_detalle_requerimiento'])
                    ->update(
                        [
                            'estado'=>15
                        ]
                    ); 
                }elseif($value['estado'] == 'ATENDIDO_TOTAL'){
                    DB::table('almacen.alm_det_req')
                    ->where('id_detalle_requerimiento',$value['id_detalle_requerimiento'])
                    ->update(
                        [
                            'estado'=>5
                        ]
                    );  
                }
            }



        }
        // Debugbar::info($arrEstadoDetalle);

        // Debugbar::info($arrEstadoReq);
 
        return $arrEstadoReq;


    }



    public function actualizar_orden_por_requerimiento(Request $request){
        try {
            DB::beginTransaction();

            $usuario = Auth::user()->id_usuario;
            $id_orden = $request->id_orden;
            $tp_doc = ($request->id_tp_documento !== null ? $request->id_tp_documento : 2);
            $codigo = $this->nextCodigoOrden($tp_doc);

            DB::table('logistica.log_ord_compra')
            ->where('log_ord_compra.id_orden_compra',$id_orden)
            ->update(
                [
                    'id_grupo_cotizacion' => isset($request->id_grupo_cotizacion)?$request->id_grupo_cotizacion:null,
                    'id_tp_documento' =>  $tp_doc,
                    'fecha' => isset($request->fecha_emision)?str_replace("T", " ", $request->fecha_emision):null,
                    'id_usuario' => $usuario?$usuario:null,
                    'id_moneda' => ($request->id_moneda?$request->id_moneda:null),
                    'id_proveedor' => $request->id_proveedor,
                    'id_contacto' => $request->id_contacto_proveedor?$request->id_contacto_proveedor:null,
                    'codigo' => $codigo?$codigo:null,

                    'plazo_entrega' => $request->plazo_entrega?$request->plazo_entrega:null,
                    'id_condicion' => $request->id_condicion?$request->id_condicion:null,
                    'plazo_dias' => $request->plazo_dias?$request->plazo_dias:null,
                    'id_cotizacion' => $request->id_cotizacion?$request->id_cotizacion:null,
                    'id_tp_doc' =>  isset($request->id_tp_doc)?$request->id_tp_doc:null,
                    'personal_autorizado' => $request->id_trabajador?$request->id_trabajador:null,
                    'id_occ' => $request->id_cc?$request->id_cc:null,
                    'id_sede' => $request->id_sede?$request->id_sede:null,
                    'direccion_destino' => $request->direccion_destino?$request->direccion_destino:null,
                    'ubigeo_destino' => isset($request->id_ubigeo_destino)?$request->id_ubigeo_destino:null,
                    // 'id_requerimiento' => $request->id_requerimiento,
                    // 'en_almacen' => false,
                    'estado' => 17,
                    'codigo_softlink' => ($request->codigo_orden!==null ? $request->codigo_orden : ''),
                ]
            );

            $dataDetalle = $request->detalle;
 

            $allidReqList=[];
            foreach($dataDetalle as $d) {
                $allidReqList[]= $d['id_requerimiento'];

                if($d['cantidad_a_comprar'] > 0 && $d['estado'] > 0){
                    DB::table('logistica.log_det_ord_compra')
                    ->where('log_det_ord_compra.id_detalle_orden',$d['id_detalle_orden'])
                    ->update([
                        'id_orden_compra'=>$id_orden,
                        'id_item'=> ($d['id_item'] ? $d['id_item'] : null),
                        'id_producto'=> (isset($d['id_producto']) ? $d['id_producto'] : null),
                        'id_detalle_requerimiento'=> ($d['id_detalle_requerimiento'] ? $d['id_detalle_requerimiento'] : null),
                        'cantidad'=> $d['cantidad_a_comprar'],
                        'id_unidad_medida'=> $d['id_unidad_medida'],
                        'precio'=> $d['precio_unitario'],
                        'descripcion_adicional'=> $d['descripcion_producto'],
                        'tipo_item_id'=> $d['id_tipo_item']?$d['id_tipo_item']:null,
                        'subtotal'=> $d['subtotal']?$d['subtotal']:0,
                        'estado'=> $d['estado']?$d['estado']:17
                    ]);
                    

                }

                if($d['cantidad_a_comprar'] > 0 && $d['estado'] == 0){
                    DB::table('logistica.log_det_ord_compra')
                    ->insert([
                        'id_orden_compra'=>$id_orden,
                        'id_item'=> ($d['id_item'] ? $d['id_item'] : null),
                        'id_producto'=> (isset($d['id_producto']) ? $d['id_producto'] : null),
                        'id_detalle_requerimiento'=>  null,
                        'cantidad'=> $d['cantidad_a_comprar'],
                        'id_unidad_medida'=> $d['id_unidad_medida'],
                        'precio'=> $d['precio_unitario'],
                        'descripcion_adicional'=> $d['descripcion_producto'],
                        'tipo_item_id'=> $d['id_tipo_item']?$d['id_tipo_item']:null,
                        'subtotal'=> $d['subtotal']?$d['subtotal']:0,
                        'estado'=> 1
                    ]);
                    

                }
            }

            $uniqueIdReqList = array_unique($allidReqList);

            $idRequerimientoAtentidosTotalList=[];
            $idRequerimientoAtentidosParcialList=[];

            $sizeDataDetalle = count($dataDetalle);
            $countAtendido=0;
            
                // foreach ($dataDetalle as $d) {
                //     if(($d['cantidad_a_comprar'] + $d['stock_comprometido'] ) == $d['cantidad'] ){
                //         $countAtendido+=1;
                //         $idRequerimientoAtentidosTotalList[]=$d['id_requerimiento'];

                //         if($this->cambioElEstadoActualDetalleReq($d['id_detalle_requerimiento']) == false ){
                //             DB::table('almacen.alm_det_req')
                //             ->where('id_detalle_requerimiento',$d['id_detalle_requerimiento'])
                //             ->update(
                //                 [
                //                     'estado'=>5, //atendido total
                //                     'stock_comprometido'=> $d['stock_comprometido']?$d['stock_comprometido']:0
                //                 ] 
                //             ); 
                //         }

                //     }
                //     if((($d['cantidad_a_comprar'] + $d['stock_comprometido'] )>0) && (($d['cantidad_a_comprar'] + $d['stock_comprometido'] ) < $d['cantidad'])){
                //         $idRequerimientoAtentidosParcialList[]=$d['id_requerimiento'];
                //         if($this->cambioElEstadoActualDetalleReq($d['id_detalle_requerimiento']) == false ){
                //             DB::table('almacen.alm_det_req')
                //             ->where('id_detalle_requerimiento',$d['id_detalle_requerimiento'])
                //             ->update(
                //                 [
                //                     'estado'=>15, // atendido parcial
                //                     'stock_comprometido'=> $d['stock_comprometido']?$d['stock_comprometido']:0
                //                 ]
                                
                //             ); 
                //         }
                //     }
                // } 
                // if(count($idRequerimientoAtentidosTotalList)>0){
                //     foreach ($idRequerimientoAtentidosTotalList as $key => $id_req) {

                //         if($countAtendido ==$sizeDataDetalle){

                //             if($this->cambioElEstadoActualReq($id_req) == false ){
                //                 DB::table('almacen.alm_req')
                //                 ->where('id_requerimiento',$id_req)
                //                 ->update(['estado'=>5]); // atendido total
        
                //                 DB::table('almacen.alm_req_obs')
                //                 ->insert([  'id_requerimiento'=>$id_req,
                //                             'accion'=>'ATENDIDO TOTAL',
                //                             'descripcion'=>'Se generó Orden de Compra '.$codigo,
                //                             'id_usuario'=>$usuario,
                //                             'fecha_registro'=>date('Y-m-d H:i:s')
                //                 ]);
                //             }
                //         }else{
                //             if($this->cambioElEstadoActualReq($id_req) == false ){
                //                 DB::table('almacen.alm_req')
                //                 ->where('id_requerimiento',$id_req)
                //                 ->update(['estado'=>15]); // atendido parcial
        
                //                 DB::table('almacen.alm_req_obs')
                //                 ->insert([  'id_requerimiento'=>$id_req,
                //                             'accion'=>'ATENDIDO PARCIAL',
                //                             'descripcion'=>'Se generó Orden de Compra '.$codigo,
                //                             'id_usuario'=>$usuario,
                //                             'fecha_registro'=>date('Y-m-d H:i:s')
                //                 ]);
                //             }
                //         }

                //     }

                // }
            //     if(count($idRequerimientoAtentidosParcialList)>0){
            //         foreach ($idRequerimientoAtentidosParcialList as $key => $id_req) {
            //             if($this->cambioElEstadoActualReq($id_req) == false ){
            //             DB::table('almacen.alm_req')
            //             ->where('id_requerimiento',$id_req)
            //             ->update(['estado'=>15]); // atendido parcial

            //             DB::table('almacen.alm_req_obs')
            //             ->insert([  'id_requerimiento'=>$id_req,
            //                         'accion'=>'ATENDIDO PARCIAL',
            //                         'descripcion'=>'Se generó Orden de Compra '.$codigo,
            //                         'id_usuario'=>$usuario,
            //                         'fecha_registro'=>date('Y-m-d H:i:s')
            //             ]);
            //         }
            //     }
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
            'estados_compra.descripcion as estado_doc',
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
        ->leftjoin('logistica.estados_compra','estados_compra.id_estado','=','log_ord_compra.estado')
        ->leftjoin('logistica.log_ord_compra_pago','log_ord_compra_pago.id_orden_compra','=','log_ord_compra.id_orden_compra')
        ->where('log_ord_compra.id_orden_compra','=',$id_orden)
        ->get();

        if (isset($log_ord_compra)) {
            $orden = [];
            foreach ($log_ord_compra as $data) {
                    $orden = [
                        'id_orden_compra' => $data->id_orden_compra,
                        'codigo'         => $data->codigo,
                        'fecha'          => $data->fecha,
                        'codigo_softlink'=> $data->codigo_softlink,
                        'razon_social'   => $data->razon_social,
                        'nro_documento'  => $data->nro_documento,
                        'condicion'      => $data->condicion,
                        'simbolo'        => $data->simbolo,
                        'id_estado'     => $data->estado,
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
        ->leftjoin('logistica.estados_compra','estados_compra.id_estado','=','log_det_ord_compra.estado')

        ->select(
            'log_det_ord_compra.id_detalle_orden',
            'log_det_ord_compra.id_orden_compra',
            'log_det_ord_compra.garantia',
            'log_det_ord_compra.estado',
            'log_det_ord_compra.personal_autorizado',
            'log_det_ord_compra.lugar_despacho',
            'log_det_ord_compra.descripcion_adicional',
            'log_det_ord_compra.id_unidad_medida',
            'log_det_ord_compra.precio',
            'log_det_ord_compra.cantidad',
            'log_det_ord_compra.estado as id_estado_detalle_orden',
            'estados_compra.descripcion as estado_detalle_orden',
            'alm_det_req.id_detalle_requerimiento',
            'alm_req.id_requerimiento',
            'alm_req.codigo AS codigo_requerimiento',
            'alm_det_req.id_requerimiento',
            'alm_det_req.id_item AS id_item_alm_det_req',
            'alm_det_req.precio_unitario',
            // 'alm_det_req.cantidad',
            // 'alm_det_req.id_unidad_medida',
            'und_medida_det_req.descripcion AS unidad_medida',
            'alm_det_req.fecha_registro AS fecha_registro_alm_det_req',
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
                    $subtotal =+ $data->cantidad *  $data->precio_unitario;
                    $total = $subtotal;
                    $detalle_orden[] = [
                        'id_detalle_orden'          => $data->id_detalle_orden,
                        'id_orden_compra'          => $data->id_orden_compra,
                        'id_detalle_requerimiento'  => $data->id_detalle_requerimiento,
                        'id_requerimiento'          => $data->id_requerimiento,
                        'codigo_requerimiento'      => $data->codigo_requerimiento,
                        'id_item'                   => $data->id_item,
                        'cantidad'                  => $data->cantidad,
                        'id_unidad_medida'             => $data->id_unidad_medida,
                        'unidad_medida'             => $data->unidad_medida,
                        'precio_unitario'        => $data->precio_unitario,
                        'descripcion_adicional'     => $data->descripcion_adicional,
                        'lugar_entrega'             => $data->lugar_entrega,
                        'fecha_registro'            => $data->fecha_registro_alm_det_req,
                        'estado'                    => $data->estado,
                        'codigo_item'               => $data->codigo_item,
                        'id_tipo_item'              => $data->id_tipo_item,
                        'id_producto'               => $data->id_producto,
                        'categoria'                 => $data->categoria,
                        'subcategoria'              => $data->subcategoria,
                        'part_number'               => $data->part_number,
                        'descripcion'               => $data->descripcion_adicional,
                        'id_almacen'                => $data->id_almacen_reserva,
                        'almacen_reserva'           => $data->almacen_reserva,
                        'subtotal'                  =>  $subtotal,
                        'id_estado_detalle_orden'   => $data->id_estado_detalle_orden,
                        'estado_detalle_orden'      => $data->estado_detalle_orden
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
        $status=0;
        $data=[];

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
        if($id_datos_contacto >0){
            $status=200;
            $data = DB::table('contabilidad.adm_ctb_contac')
                ->where([
                    ['id_datos_contacto', '=', $id_datos_contacto]
                ])
                ->first();
        }

        $output = ['status'=>$status,'data'=>$data];
 
        return json_encode($output);
    }



    function tieneIngresoAlmacen($id_orden){
        $status=0;
        $msj=[];
        $data=[];
        // buscar en detalle_orden los id_detalle_requerimiento
        $log_det_ord_compra = DB::table('logistica.log_det_ord_compra')
        ->select(
            'log_det_ord_compra.*'
            )
        ->where([
            ['log_det_ord_compra.id_orden_compra','=',$id_orden]
            ])
        ->get();
    
        // verificar si existe ingreso en almacen 
        if(count($log_det_ord_compra)>0){
            foreach($log_det_ord_compra as $data){
                $id_detalle_orden_list[]=$data->id_detalle_orden;
            }
    
            $guia_com_det = DB::table('almacen.guia_com_det')
            ->select(
                'guia_com_det.*'
                )
            ->whereIn('guia_com_det.id_oc_det',$id_detalle_orden_list)
            ->where('guia_com_det.estado',1)
            ->get();
            if(count($guia_com_det)>0){
                $status = 200;
                $msj[]='La orden tiene items ingresados a almacén';
                $data=true;
    
            }else{
                $status = 200;
                $msj[]='La orden aun no tiene items ingresos al almacén';
                $data=false;
            }
    
        }else{
            $status = 204;
            $msj[]='la orden no tiene detalle';
            $data=false;
        }
    
        $output=['status'=>$status, 'mensaje'=>$msj,'data'=>$data];
        return $output;  
    }
    
    function makeRevertirOrden($id_orden){
        $status=0;
        $msj=[];
        $output=[];
        $id_requerimiento_list=[];

       $revertirOrden = DB::table('logistica.log_ord_compra') //revertir orden
        ->where([
                ['id_orden_compra',$id_orden]])
        ->update(
            [
                'estado' => 7,
                'codigo_softlink' => null
            ]);

       $revertirDetalleOrden = DB::table('logistica.log_det_ord_compra') // revertir detalle orden
        ->where([['id_orden_compra',$id_orden]])
        ->update(
            [
                'estado' => 7
            ]);
        if($revertirOrden > 0){
            $status=200;
            $msj[]='Orden Revertida';
        }else{
            $status=204;
            $msj[]='hubo un problema al tratar de revertir la orden';

        }
        if($revertirDetalleOrden > 0){
            $status=200;
            $msj[]='Detalle Orden Revertida';
        }else{
            $status=204;
            $msj[]='hubo un problema al tratar de revertir el detalle de la orden';

        }
        // revertir requerimiento y detalle requerimiento ==>
        // buscar en detalle_orden los id_detalle_requerimiento
        $log_det_ord_compra = DB::table('logistica.log_det_ord_compra')
        ->select(
            'log_det_ord_compra.*'
            )
        ->where([
            ['log_det_ord_compra.id_orden_compra','=',$id_orden]
            ])
        ->get();

        if(count($log_det_ord_compra)>0){
            foreach($log_det_ord_compra as $data){
                $id_detalle_req_list[]=$data->id_detalle_requerimiento;
            }
            // buscar id_requerimiento
            $alm_req = DB::table('almacen.alm_req')
            ->select(
                'alm_req.*'
                )
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento')
            ->whereIn('alm_det_req.id_detalle_requerimiento',$id_detalle_req_list)
            ->get();

            if(count($alm_req)>0){
                foreach($alm_req as $data){
                    $id_requerimiento_list[]= $data->id_requerimiento;
                }

                if(count($id_requerimiento_list)>0){
                    DB::table('almacen.alm_req')
                    ->whereIn('id_requerimiento',$id_requerimiento_list)
                    ->update(['estado'=>2]);
            
                    DB::table('almacen.alm_det_req')
                    ->whereIn('id_requerimiento',$id_requerimiento_list)
                    ->where('id_almacen_reserva',null)
                    ->update(['estado'=>1]);
                    $status = 200;
                    $msj[]='se restableció el estado del requerimiento';

                }else{
                    $status = 204;
                    $msj[]='hubo un problema, no se pudo restablecer el estado del requerimientos';
                }

            }//-> si no se encuentra req
            else{
                $status = 204;
                $msj[]='no se encontro requerimientos';
            }

        }// -> si no tiene detalle la orden
        else{
            $status = 204;
            $msj[]='no se encontro el detalle de la orden';

        }

    $output=['status'=>$status, 'mensaje'=>$msj, 'requerimientoIdList'=>$id_requerimiento_list];
    return $output;

}

    public function anularOrden($id_orden){
        try {
            DB::beginTransaction();
            $status = 0;
            $msj = [];
            $output = [];
            $requerimientoIdList = [];

            $hasIngreso = $this->TieneingresoAlmacen($id_orden);
            if($hasIngreso['status'] == 200 && $hasIngreso['data'] == false){
                $makeRevertirOrden = $this->makeRevertirOrden($id_orden);
                $status = $makeRevertirOrden['status'];
                $msj[] = $makeRevertirOrden['mensaje'];
                $requerimientoIdList = $makeRevertirOrden['requerimientoIdList'];
            }
            else{
                $status= $hasIngreso['status'];
                $msj[]= $hasIngreso['mensaje'];
            }

            if($status ==200){
                $orden = DB::table('logistica.log_ord_compra')
                ->select(
                    'log_ord_compra.codigo'
                )
                ->where('log_ord_compra.id_orden_compra',$id_orden)
                ->first();

                for ($i = 0; $i < count($requerimientoIdList); $i++) {
                    DB::table('almacen.alm_req_obs')
                    ->insert([  'id_requerimiento'=>$requerimientoIdList[$i],
                                'accion'=>'ORDEN ANULADA',
                                'descripcion'=>'Orden '.($orden->codigo?$orden->codigo:"").' anulada',
                                'id_usuario'=>Auth::user()->id_usuario,
                                'fecha_registro'=>date('Y-m-d H:i:s')
                    ]);
                }
            }

            $output=['status'=>$status, 'mensaje'=>$msj];

            DB::commit();
            return response()->json($output);

        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }




}
