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
