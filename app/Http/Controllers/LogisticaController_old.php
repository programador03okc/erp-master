<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

// use Mail;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Dompdf\Dompdf;
use PDF;

class LogisticaController_old extends Controller
{


    public function unique_multidim_array($array, $option1) { 
        $idArray = array();
        $temp_array = $array;
        foreach ($temp_array as $key => $row)
        {
            $idArray[$key] = $row[$option1];
        }
        array_multisort($idArray, SORT_ASC, $temp_array);
 
        $temp[$option1][0]='';
        $cotizacionItemArray=[];
        for($j=0; $j< sizeof($temp_array);$j++){
            if(!(in_array($temp_array[$j][$option1], $temp[$option1]))){
                array_push($cotizacionItemArray, $temp_array[$j]);
            }
            $temp[$option1][0] = $temp_array[$j][$option1];
        }

        return $cotizacionItemArray;
    } 
    public function unique_multidim_array2($array, $option1, $option2) { 

        $idArray = array();
        $temp_array = $array;
        foreach ($temp_array as $key => $row)
        {
            $idArray[$key] = $row[$option1];
        }
        array_multisort($idArray, SORT_ASC, $temp_array);
 
        $temp[$option1][0]='';
        $temp[$option2][0]='';

        $cotizacionItemArray=[];
        for($j=0; $j< sizeof($temp_array);$j++){
            if(!(in_array($temp_array[$j][$option1], $temp[$option1]) && in_array($temp_array[$j][$option2], $temp[$option2]))){
                array_push($cotizacionItemArray, $temp_array[$j]);
            }
            $temp[$option1][0] = $temp_array[$j][$option1];
            $temp[$option2][0] = $temp_array[$j][$option2];
        }
        return $cotizacionItemArray;
    } 


    public function requerimiento_fill_input(){

        $log_cdn_pago = DB::table('logistica.log_cdn_pago')
        ->select(
            'log_cdn_pago.*',
            DB::raw("(CASE WHEN logistica.log_cdn_pago.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
            ])
            ->orderBy('log_cdn_pago.id_condicion_pago', 'asc')
        ->get();

        $cont_tp_doc = DB::table('contabilidad.cont_tp_doc')
        ->select(
            'cont_tp_doc.*',
            DB::raw("(CASE WHEN contabilidad.cont_tp_doc.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
            ])
            ->orderBy('cont_tp_doc.id_tp_doc', 'asc')
        ->get();

        $conf_moneda = DB::table('configuracion.sis_moneda')
        ->select(
            'sis_moneda.id_moneda',
            'sis_moneda.descripcion',
            'sis_moneda.simbolo',
            'sis_moneda.estado',
            DB::raw("(CASE WHEN configuracion.sis_moneda.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
            ])
            ->orderBy('sis_moneda.id_moneda', 'asc')
        ->get();
        
        $alm_unidad_medida = DB::table('almacen.alm_und_medida')
        ->select(
            'alm_und_medida.id_unidad_medida',
            'alm_und_medida.descripcion',
            'alm_und_medida.abreviatura',
            'alm_und_medida.estado',
            DB::raw("(CASE WHEN almacen.alm_und_medida.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
            ])
            ->orderBy('alm_und_medida.id_unidad_medida', 'asc')
        ->get();

        $adm_grupo = DB::table('administracion.adm_grupo')
        ->select(
            'adm_grupo.id_grupo',
            'adm_grupo.descripcion',
            'adm_grupo.estado',
            'adm_grupo.fecha_registro',
            DB::raw("(CASE WHEN administracion.adm_grupo.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
            ['adm_grupo.id_sede', '=', 1],
            ['adm_grupo.estado', '=', 1]
            ])
            ->orderBy('adm_grupo.id_grupo', 'asc')
        ->get();

        $adm_area = DB::table('administracion.adm_area')
        ->select(
            'adm_area.*',
            DB::raw("(CASE WHEN administracion.adm_area.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            
            )
            ->where([
                
               ['adm_area.estado', '=', 1]
               ])
            ->orderBy('adm_area.id_area', 'asc')
        ->get();
       
       
        $alm_tp_req = DB::table('almacen.alm_tp_req')
        ->select(
            'alm_tp_req.id_tipo_requerimiento',
            'alm_tp_req.descripcion' 
            )
            ->orderBy('alm_tp_req.id_tipo_requerimiento', 'asc')
        ->get();

        
        $adm_prioridad = DB::table('administracion.adm_prioridad')
        ->select(
            'adm_prioridad.id_prioridad',
            'adm_prioridad.descripcion' 
            )
            ->orderBy('adm_prioridad.id_prioridad', 'asc')
        ->get();

        return response()->json([
            'log_cdn_pago'=> $log_cdn_pago,
            'cont_tp_doc'=> $cont_tp_doc,
            'alm_unidad_medida'=>$alm_unidad_medida,
            'adm_grupo'=>$adm_grupo,
            'adm_area'=>$adm_area,
            'alm_tp_req'=>$alm_tp_req,
            'adm_prioridad'=>$adm_prioridad,
            'conf_moneda'=>$conf_moneda
            ]);
    }
    public function mostrar_requerimientos(){
        $alm_req = DB::table('almacen.alm_req')
        ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
        ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
        ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
        ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
        ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
        ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
        ->leftJoin('proyectos.proy_proyecto', 'alm_req.id_proyecto', '=', 'proy_proyecto.id_proyecto')
        ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
        ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_requerimiento', '=', 'alm_req.id_requerimiento')
        // ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_detalle_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion')

        ->leftJoin('logistica.log_ord_compra', 'log_ord_compra.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
        ->leftJoin('almacen.guia_com_oc', 'guia_com_oc.id_oc', '=', 'log_ord_compra.id_orden_compra')
        // ->leftJoin('administracion.adm_tp_docum', 'adm_tp_docum.id_tp_documento', '=', 'log_ord_compra.id_tp_documento')

        ->select(
            'alm_req.id_requerimiento',
            'alm_req.codigo',
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
            'alm_req.id_proyecto',
            'proy_proyecto.codigo AS proy_proyecto_codigo',
            'proy_proyecto.descripcion AS proy_proyecto_descripcion',
            'alm_req.concepto AS alm_req_concepto',
            'log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion',
            // 'log_cotizacion.id_cotizacion',
            // 'log_cotizacion.codigo_cotizacion',
            // 'log_ord_compra.id_orden_compra',
            // 'log_ord_compra.id_tp_documento',
            // 'adm_tp_docum.abreviatura AS adm_tp_docum_abreviatura',
            // 'log_ord_compra.numero',
            // 'log_ord_compra.serie',
 
            'alm_req.id_prioridad',
            'alm_req.fecha_registro',
            'alm_req.estado',
            DB::raw("(CASE WHEN alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc"),
            // DB::raw("SUM(CASE WHEN log_cotizacion.id_cotizacion > 0 THEN 1 ELSE 0 END) AS count_cotizazcion"),
            // DB::raw("SUM(CASE WHEN log_ord_compra.id_orden_compra > 0 THEN 1 ELSE 0 END) AS count_orden")
            // DB::raw("(SELECT COUNT(log_cotizacion.id_cotizacion) FROM logistica.log_cotizacion
            // WHERE log_cotizacion.id_detalle_grupo_cotizacion = log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion)::integer as cantidad_cotizacion"),
            DB::raw("(SELECT  COUNT(log_ord_compra.id_orden_compra) FROM logistica.log_ord_compra
            WHERE log_ord_compra.id_grupo_cotizacion = log_detalle_grupo_cotizacion.id_grupo_cotizacion)::integer as cantidad_orden"),
            DB::raw("(SELECT  COUNT(mov_alm.id_mov_alm) FROM almacen.mov_alm
            WHERE mov_alm.id_guia_com = guia_com_oc.id_guia_com and 
            guia_com_oc.id_oc = log_ord_compra.id_orden_compra)::integer as cantidad_entrada_almacen")

            )
            // ->groupBy('alm_req.id_requerimiento',
            // 'alm_tp_req.descripcion',
            // 'sis_usua.usuario',
            // 'rrhh_rol.id_area',
            // 'adm_area.descripcion',
            // 'rrhh_rol.id_rol',
            // 'rrhh_rol_concepto.descripcion',
            // 'adm_grupo.descripcion',
            // 'proy_proyecto.codigo',
            // 'proy_proyecto.descripcion',
            // // 'log_cotizacion.id_cotizacion',
 
            // 'log_ord_compra.id_orden_compra'
            // // 'adm_tp_docum.abreviatura'
 
            // )

            ->where([
                
            ['alm_req.estado', '=', 1]
            ])
            ->orderBy('alm_req.id_requerimiento', 'desc')
        ->get();
        // $data = ["alm_req"=>$alm_req];   

        return response()->json(["data"=>$alm_req]);
    }


    public function mostrar_detalles_requerimiento($id){


        $alm_req = DB::table('almacen.alm_req')
        ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
        ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
        ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
        ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
        ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
        ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
        ->leftJoin('proyectos.proy_proyecto', 'alm_req.id_proyecto', '=', 'proy_proyecto.id_proyecto')
        ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
        ->leftJoin('comercial.com_cliente', 'proy_proyecto.cliente', '=', 'com_cliente.id_cliente')
         ->leftJoin('contabilidad.adm_contri', 'com_cliente.id_contribuyente', '=', 'adm_contri.id_contribuyente')
        ->leftJoin('proyectos.proy_presup', 'alm_req.id_presupuesto', '=', 'proy_presup.id_presupuesto')
        ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_requerimiento', '=', 'alm_req.id_requerimiento')
        ->leftJoin('logistica.log_grupo_cotizacion', 'log_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
          ->select(  
            'alm_req.id_requerimiento',
            'alm_req.codigo',
             'alm_req.id_grupo',
             'adm_grupo.descripcion AS adm_grupo_descripcion',
            'alm_req.fecha_requerimiento',
            'alm_req.id_tipo_requerimiento',
            'alm_tp_req.descripcion AS tipo_req_desc',
            'alm_req.observacion',
            'alm_req.id_usuario',
            'sis_usua.usuario',
            'alm_req.id_rol',
            'rrhh_rol.id_rol_concepto',
            'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
            'alm_req.id_area',
            'adm_area.descripcion AS area_desc',
            'alm_req.id_proyecto',
            'proy_proyecto.descripcion AS descripcion_proyecto',
            'alm_req.concepto',
        
            'alm_req.objetivo',
     
             'alm_req.fecha_registro',
            'alm_req.estado',
            'log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion',
            'log_detalle_grupo_cotizacion.id_grupo_cotizacion',
            'log_detalle_grupo_cotizacion.id_oc_cliente',
            'log_grupo_cotizacion.id_grupo_cotizacion AS grupo_cotizacion_id_grupo_cotizacion',
            'log_grupo_cotizacion.codigo_grupo',
            'log_grupo_cotizacion.id_usuario AS grupo_cotizacion_grupo_cotizacion',
            'log_grupo_cotizacion.fecha_inicio',
            'log_grupo_cotizacion.fecha_fin',
            'log_grupo_cotizacion.estado AS grupo_cotizacion_estado',

            // 'log_cotizacion.id_cotizacion',
            // 'conf_usu_coti.usuario as usuario_creador_cotiza',
            // 'log_cotizacion.codigo_cotizacion',
            // 'log_ord_compra.id_orden_compra',
            // 'log_ord_compra.codigo AS codigo_orden',
            // 'conf_usu_orden.usuario AS usuario_creador_orden',
            // 'log_ord_compra.fecha AS fecha_orden',
            DB::raw("(CASE WHEN alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
 

            )
            ->where([
               ['alm_req.id_requerimiento', '=', $id],
               ['alm_req.estado', '=', 1]
              ])
              
           ->orderBy('alm_req.id_requerimiento', 'asc')
            ->get();

       if(sizeof($alm_req)<=0){
           $alm_req=[ "requerimiento"=>[]];
           return response()->json($alm_req);
       }

       $cotizaciones = DB::table('almacen.alm_req')
       ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_requerimiento', '=', 'alm_req.id_requerimiento')
       ->leftJoin('logistica.log_grupo_cotizacion', 'log_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
       ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'log_grupo_cotizacion.id_usuario')
       ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_detalle_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion')
        ->select(
           'alm_req.id_requerimiento',
           'alm_req.codigo',
           'log_grupo_cotizacion.fecha_inicio',
           'log_grupo_cotizacion.fecha_fin',
           'log_cotizacion.id_cotizacion',
           'sis_usua.usuario as usuario_creador_cotiza',
           'log_cotizacion.codigo_cotizacion'
         )
           ->where([
               ['alm_req.id_requerimiento', '=', $id],
               ['alm_req.estado', '=', 1]
              ])
           ->orderBy('alm_req.id_requerimiento', 'asc')
            ->get();

       if(sizeof($cotizaciones)<=0){
               $cotizaciones=[];
       }else{

           foreach($cotizaciones as $data){
           $cotizacion[]=[
               'id_cotizacion'      => $data->id_cotizacion,
               'codigo'             => $data->codigo_cotizacion,
               'usuario'            => $data->usuario_creador_cotiza,
               'fecha_inicio'       => $data->fecha_inicio,
               'fecha_fin'          => $data->fecha_fin
           ];
   
           }
       }

       $ordenes = DB::table('almacen.alm_req')
       ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_requerimiento', '=', 'alm_req.id_requerimiento')
       ->leftJoin('logistica.log_grupo_cotizacion', 'log_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
       ->leftJoin('logistica.log_ord_compra','log_ord_compra.id_grupo_cotizacion','=','log_grupo_cotizacion.id_grupo_cotizacion')
       ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'log_ord_compra.id_usuario')
       ->select(
           'alm_req.id_requerimiento',
           'alm_req.codigo',     
           'log_ord_compra.id_orden_compra',
           'log_ord_compra.codigo AS codigo_orden',
           'sis_usua.usuario AS usuario_creador_orden',
           'log_ord_compra.fecha AS fecha_orden')
           ->where([
               ['alm_req.id_requerimiento', '=', $id],
               ['alm_req.estado', '=', 1]
              ])
           ->orderBy('alm_req.id_requerimiento', 'asc')
            ->get();

       if(sizeof($ordenes)<=0){
       $ordenes=[];
       }else{
           foreach($ordenes as $data){
           $orden[]=[
               'id_orden_compra'    => $data->id_orden_compra,
               'codigo'             => $data->codigo_orden,
               'usuario'            => $data->usuario_creador_orden,
               'fecha'              => $data->fecha_orden

           ];
           }
       }    
       
       $entradasAlmacen = DB::table('almacen.alm_req')
       ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_requerimiento', '=', 'alm_req.id_requerimiento')
       ->leftJoin('logistica.log_grupo_cotizacion', 'log_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
       ->leftJoin('logistica.log_ord_compra','log_ord_compra.id_grupo_cotizacion','=','log_grupo_cotizacion.id_grupo_cotizacion')
       ->leftJoin('almacen.guia_com_oc','guia_com_oc.id_oc','=','log_ord_compra.id_orden_compra')
       ->leftJoin('almacen.mov_alm','mov_alm.id_guia_com','=','guia_com_oc.id_guia_com')
       ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'mov_alm.usuario')
       ->select(
           'alm_req.id_requerimiento',     
           'log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion',     
           'log_grupo_cotizacion.id_grupo_cotizacion',     
           'log_ord_compra.id_orden_compra',     
           'guia_com_oc.id_guia_com_oc',     
             'mov_alm.id_mov_alm',     
            'mov_alm.codigo',     
            'sis_usua.usuario',     
            'mov_alm.fecha_emision'     
            )
           ->where([
               ['alm_req.id_requerimiento', '=', $id],
               ['alm_req.estado', '=', 1]
              ])
           ->orderBy('alm_req.id_requerimiento', 'asc')
            ->get();

       if(sizeof($entradasAlmacen)<=0){
       $entradaAlmacen=[];
       }else{
           foreach($entradasAlmacen as $data){
           $entradaAlmacen[]=[
 
                'id_mov_alm'    => $data->id_mov_alm,
                'codigo'        => $data->codigo,
                'usuario'       => $data->usuario,
                'fecha_emision' => $data->fecha_emision

           ];
           }
        }

         

       foreach($alm_req as $data){
        $detalle_grupo_cotizacion[] = [
            'id_detalle_grupo_cotizacion'=> $data->id_detalle_grupo_cotizacion,
            'id_grupo_cotizacion'=> $data->id_grupo_cotizacion,
            'id_requerimiento'=> $data->id_requerimiento,
            'id_oc_cliente'=> $data->id_oc_cliente
        ];
        $detalle_cotizacion[] = [
            'id_grupo_cotizacion'=> $data->grupo_cotizacion_id_grupo_cotizacion,
            'codigo_grupo'=> $data->codigo_grupo,
            'id_usuario'=> $data->grupo_cotizacion_grupo_cotizacion,
            'fecha_inicio'=> $data->fecha_inicio,
            'fecha_fin,'=> $data->fecha_fin,
            'estado'=> $data->grupo_cotizacion_estado

        ];
        $requerimiento[] = [
            'id_requerimiento'=> $data->id_requerimiento,
            'codigo'=>$data->codigo,
            'concepto'=>$data->concepto,
             'objetivo'=>$data->objetivo,
             'id_grupo'=>$data->id_grupo,
            'adm_grupo_descripcion'=>$data->adm_grupo_descripcion,
           'fecha_requerimiento'=>$data->fecha_requerimiento,
           'id_tipo_requerimiento'=> $data->id_tipo_requerimiento,
           'tipo_req_desc'=>$data->tipo_req_desc,
           'id_usuario'=>$data->id_usuario,
           'usuario'=>$data->usuario,
           'id_rol'=>$data->id_rol,
           'id_area'=>$data->id_area,
            //    'id_rol'=> $data->archivo_adjunto,
               'area_desc'=>$data->area_desc,
           'id_proyecto'=> $data->id_proyecto,
           'descripcion_proyecto'=> $data->descripcion_proyecto,
        //    'codigo_presupuesto'=> $data->codigo_presupuesto,
        //    'descripcion_cliente'=> $data->descripcion_cliente,
        //    'id_presupuesto'=> $data->id_presupuesto,
            'observacion'=> $data->observacion,
            'fecha_registro'=> $data->fecha_registro,
           'estado'=> $data->estado,
           'estado_desc'=> $data->estado_desc
       ];  


    };
    
    if($requerimiento[0]['id_tipo_requerimiento']==1){ // alm_det_req- bienes
       $alm_det_req = DB::table('almacen.alm_item')
       ->leftJoin('almacen.alm_det_req', 'alm_item.id_item', '=', 'alm_det_req.id_item')
       ->leftJoin('almacen.alm_prod', 'alm_item.id_producto', '=', 'alm_prod.id_producto')
       ->leftJoin('almacen.alm_und_medida', 'alm_prod.id_unidad_medida', '=', 'alm_und_medida.id_unidad_medida')
       ->leftJoin('almacen.alm_clasif','alm_clasif.id_clasificacion','=', 'alm_prod.id_clasif')
       ->leftJoin('almacen.alm_subcategoria','alm_subcategoria.id_subcategoria','=', 'alm_prod.id_subcategoria')
       ->leftJoin('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=', 'alm_subcategoria.id_categoria')
       ->leftJoin('almacen.alm_tp_prod','alm_tp_prod.id_tipo_producto','=', 'alm_cat_prod.id_tipo_producto')
       ->select(
            'alm_det_req.id_detalle_requerimiento',
            'alm_det_req.id_requerimiento',
            'alm_det_req.id_item AS id_item_alm_det_req',
            'alm_det_req.precio_referencial',
            'alm_det_req.cantidad',
            'alm_det_req.precio_referencial',
              'alm_det_req.fecha_registro AS fecha_registro_alm_det_req',
            'alm_det_req.fecha_entrega',
            'alm_det_req.estado',
           'alm_item.id_item AS alm_item_id_item',
           'alm_item.id_producto AS alm_item_id_producto',
           'alm_item.id_servicio',
           'alm_item.codigo AS alm_item_codigo',
           'alm_item.fecha_registro AS alm_item_fecha_registro',
           'alm_prod.id_producto AS alm_prod_id_producto',
           'alm_prod.codigo AS alm_prod_codigo',
        //    'alm_prod.codigo_anexo AS alm_prod_codigo_anexo',
           'alm_prod.descripcion AS alm_prod_descripcion',
           'alm_prod.id_unidad_medida AS alm_prod_id_unidad_medida',
             'alm_und_medida.descripcion AS alm_und_medida_descripcion',
           'alm_und_medida.abreviatura AS alm_und_medida_abreviatura',
           'alm_clasif.id_clasificacion',
           'alm_clasif.descripcion AS alm_clasif_descripcion',
           'alm_subcategoria.id_subcategoria',
           'alm_subcategoria.descripcion AS alm_subcategoria_descripcion',
           'alm_subcategoria.codigo AS alm_subcategoria_codigo',
           'alm_cat_prod.id_categoria',
           'alm_cat_prod.descripcion AS alm_cat_prod_descripcion',
           'alm_cat_prod.codigo AS alm_cat_prod_codigo',
           'alm_tp_prod.id_tipo_producto',
           'alm_tp_prod.descripcion AS alm_tp_prod_descripcion'
            )
            ->where([
                    ['alm_det_req.id_requerimiento', '=', $id],
                ['alm_det_req.estado', '=', 1]
                 ])
            ->orderBy('alm_item.id_item', 'asc')
            ->get();

    }else if($requerimiento[0]['id_tipo_requerimiento']==2){  // alm_det_req- servicios
    $alm_det_req = DB::table('almacen.alm_item')
    ->leftJoin('almacen.alm_det_req', 'alm_item.id_item', '=', 'alm_det_req.id_item')
    ->leftJoin('logistica.log_servi', 'alm_item.id_servicio', '=', 'log_servi.id_servicio')
     ->leftJoin('logistica.log_tp_servi', 'log_tp_servi.id_tipo_servicio', '=', 'log_servi.id_tipo_servicio')
    ->select(
        'alm_det_req.id_detalle_requerimiento',
        'alm_det_req.id_requerimiento',
        'alm_det_req.id_item AS id_item_alm_det_req',
        'alm_det_req.precio_referencial',
        'alm_det_req.cantidad',
        'alm_det_req.precio_referencial',
          'alm_det_req.fecha_registro AS fecha_registro_alm_det_req',
        'alm_det_req.fecha_entrega',
        'alm_det_req.estado',

        'alm_item.id_item AS alm_item_id_item',
        'alm_item.id_producto AS alm_item_id_producto',
        'alm_item.id_servicio',
        'alm_item.codigo AS alm_item_codigo',
         'alm_item.fecha_registro AS alm_item_fecha_registro',
        'log_servi.id_servicio',
        'log_servi.codigo',
        'log_servi.id_servicio',
        'log_servi.descripcion',
        'log_servi.id_tipo_servicio',
        'log_tp_servi.descripcion AS log_tp_servi_descripcion'
         )
        ->where([
                ['alm_det_req.id_requerimiento', '=', $id],
            ['alm_det_req.estado', '=', 1]
            ])
        ->orderBy('alm_item.id_item', 'asc')
        ->get();
    }

    $requerimiento = $this->unique_multidim_array($requerimiento,'id_requerimiento');
    $lastId = "";
    $detalle_requerimiento=[];
      foreach($alm_det_req as $data){
        if ($data->id_detalle_requerimiento !== $lastId) {
            if($requerimiento[0]['id_tipo_requerimiento']==1){ // alm_det_req- bienes

                $detalle_requerimiento[]=[
                    'id_detalle_requerimiento'  =>$data->id_detalle_requerimiento,
                    'id_item'                   =>$data->id_item_alm_det_req,
                    'id_requerimiento'          =>$data->id_requerimiento,
                    'cantidad'                  =>$data->cantidad,
                    'precio_referencial'        =>$data->precio_referencial,
                    'fecha_entrega'             =>$data->fecha_entrega,
                    'fecha_registro'            =>$data->fecha_registro_alm_det_req,
                    'codigo_item'               =>$data->alm_item_codigo,
                    'codigo_producto'           =>$data->alm_prod_codigo,
                    // 'codigo_anexo'              =>$data->alm_prod_codigo_anexo,
                    'descripcion'               =>$data->alm_prod_descripcion,
                    'id_unidad_medida'          =>$data->alm_prod_id_unidad_medida,
                    'abreviatura'               =>$data->alm_und_medida_abreviatura,
                    'alm_und_medida_descripcion'=>$data->alm_und_medida_descripcion,

                    'id_clasificacion'              =>$data->id_clasificacion,
                    'alm_clasif_descripcion'        =>$data->alm_clasif_descripcion,
                    'id_subcategoria'               =>$data->id_subcategoria,
                    'alm_subcategoria_descripcion'  =>$data->alm_subcategoria_descripcion,
                    'alm_subcategoria_codigo'       =>$data->alm_subcategoria_codigo,
                    'id_categoria'                  =>$data->id_categoria,
                    'alm_cat_prod_descripcion'      =>$data->alm_cat_prod_descripcion,
                    'alm_cat_prod_codigo'           =>$data->alm_cat_prod_codigo,
                    'id_tipo_producto'              =>$data->id_tipo_producto,
                    'alm_tp_prod_descripcion'       =>$data->alm_tp_prod_descripcion,
                    'estado'                        =>$data->estado   
                ];      
                $lastId = $data->id_detalle_requerimiento;
            }
            else if($requerimiento[0]['id_tipo_requerimiento']==2){ // alm_det_req- servicios
                $detalle_requerimiento[]=[
                    'id_detalle_requerimiento'  =>$data->id_detalle_requerimiento,
                    'id_item'                   =>$data->id_item_alm_det_req,
                    'id_requerimiento'          =>$data->id_requerimiento,
                    'cantidad'                  =>$data->cantidad,
                    'precio_referencial'        =>$data->precio_referencial,
                      'fecha_entrega'           =>$data->fecha_entrega,
                    'fecha_registro'            =>$data->fecha_registro_alm_det_req,
                    'codigo_item'               => $data->alm_item_codigo,
                    'codigo_servicio'           => $data->codigo,
                    'descripcion'               => $data->descripcion,
                    'id_tipo_servicio'          => $data->id_tipo_servicio,
                    'tipo_servicio'             => $data->log_tp_servi_descripcion,
                    'precio_unitario'           => $data->precio_unitario,
                    'estado'                    =>$data->estado   

                ];      
                $lastId = $data->id_detalle_requerimiento;    
            }
        }     
      }

     
 
       return response()->json([
           "requerimiento"=>$requerimiento,
           "det_req"=>$detalle_requerimiento,
           "detalle_grupo_cotizacion"=>$detalle_grupo_cotizacion, 
           "detalle_cotizacion"=>$detalle_cotizacion,
           "cotizacion"=>$cotizacion,
           "orden"=>$orden,
           "entradaAlmacen"=>$entradaAlmacen
           ]);
     
    }
// //////////////////////////////////////////////////////





 


    public function get_requerimiento($id, $codigo){
                if($id > 0){
                    $theWhere = ['alm_req.id_requerimiento', '=', $id];
                }else{
                    
                    $theWhere = ['alm_req.codigo', '=', $codigo];
                } 

            $alm_req = DB::table('almacen.alm_req')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')

            ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
            ->leftJoin('proyectos.proy_proyecto', 'alm_req.id_proyecto', '=', 'proy_proyecto.id_proyecto')
            ->leftJoin('comercial.com_cliente', 'proy_proyecto.cliente', '=', 'com_cliente.id_cliente')
            ->leftJoin('contabilidad.adm_contri', 'com_cliente.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->leftJoin('proyectos.proy_presup', 'alm_req.id_presupuesto', '=', 'proy_presup.id_presupuesto')
            // ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_requerimiento', '=', 'alm_req.id_requerimiento')
            // ->leftJoin('logistica.log_grupo_cotizacion', 'log_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
            // ->leftJoin('log_det_coti', 'alm_req.id_requerimiento', '=', 'log_det_coti.id_requerimiento')
            // ->leftJoin('log_coti', 'log_det_coti.id_cotizacion', '=', 'log_coti.id_cotizacion')
            // ->leftJoin('log_ord_compra', 'log_coti.id_cotizacion', '=', 'log_ord_compra.id_cotizacion')
            ->select(  
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.concepto',
                'alm_req.id_prioridad',
                'alm_req.id_estado_doc',
                'alm_req.id_grupo',
                'alm_req.fecha_requerimiento',
                'alm_req.observacion',
                'alm_req.id_usuario',
                DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as persona"),
                'sis_usua.usuario',
                'alm_req.id_rol',
                'rrhh_rol.id_rol_concepto',
                'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
                'alm_req.id_area',
                'adm_area.descripcion AS area_descripcion',
                'alm_req.id_proyecto',
                'proy_proyecto.descripcion AS descripcion_proyecto',
                'proy_proyecto.codigo AS codigo_presupuesto',
                'adm_contri.razon_social AS descripcion_cliente',
                'alm_req.id_presupuesto',
                'alm_req.objetivo',
                'alm_req.occ',
                'alm_req.archivo_adjunto',
                'alm_req.fecha_registro',
                'alm_req.estado',
                // 'log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion',
                // 'log_detalle_grupo_cotizacion.id_grupo_cotizacion',
                // 'log_detalle_grupo_cotizacion.id_requerimiento',
                // 'log_detalle_grupo_cotizacion.id_oc_cliente',
                // 'log_grupo_cotizacion.id_grupo_cotizacion AS grupo_cotizacion_id_grupo_cotizacion',
                // 'log_grupo_cotizacion.codigo_grupo',
                // 'log_grupo_cotizacion.id_usuario AS grupo_cotizacion_grupo_cotizacion',
                // 'log_grupo_cotizacion.fecha_inicio',
                // 'log_grupo_cotizacion.fecha_fin',
                // 'log_grupo_cotizacion.estado AS grupo_cotizacion_estado',
                DB::raw("(CASE WHEN alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
                )
                ->where([
                    $theWhere,
                ['alm_req.estado', '=', 1]
                ])
            ->orderBy('alm_req.id_requerimiento', 'asc')
        ->get();
            
        if(sizeof($alm_req)<=0){
            $alm_req=[];
            return response()->json($alm_req);
        }else{

        foreach($alm_req as $data){
            
            $id_requerimiento = $data->id_requerimiento;

            $requerimiento[] = [
                'id_requerimiento'=> $data->id_requerimiento,
                'codigo'=>$data->codigo,
                'concepto'=>$data->concepto,
                'objetivo'=>$data->objetivo,
                'id_estado_doc'=>$data->id_estado_doc,
                'id_prioridad'=>$data->id_prioridad,
                'occ'=>$data->occ,
                'id_grupo'=>$data->id_grupo,
                // 'coti_codigo'=>$data->coti_codigo,
                // 'orden_codigo'=>$data->orden_codigo,
                // 'orden_serie'=>$data->orden_serie,
            'fecha_requerimiento'=>$data->fecha_requerimiento,
            'id_usuario'=>$data->id_usuario,
            'persona'=>$data->persona,
            'usuario'=>$data->usuario,
            'id_rol'=>$data->id_rol,
            'id_area'=>$data->id_area,
            'area_descripcion'=>$data->area_descripcion,
            'archivo_adjunto'=> $data->archivo_adjunto,
                //    'area_desc'=>$data->area_desc,
            'id_proyecto'=> $data->id_proyecto,
            'descripcion_proyecto'=> $data->descripcion_proyecto,
            'codigo_presupuesto'=> $data->codigo_presupuesto,
            'descripcion_cliente'=> $data->descripcion_cliente,
            'id_presupuesto'=> $data->id_presupuesto,
                'observacion'=> $data->observacion,
                'fecha_registro'=> $data->fecha_registro,
            'estado'=> $data->estado,
            'estado_desc'=> $data->estado_desc
        ];  
        };
        
            if($requerimiento[0]['id_tipo_requerimiento']==1){ // alm_det_req- bienes
            $alm_det_req = DB::table('almacen.alm_item')
            ->rightJoin('almacen.alm_det_req', 'alm_item.id_item', '=', 'alm_det_req.id_item')
            ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftJoin('almacen.alm_prod', 'alm_item.id_producto', '=', 'alm_prod.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_prod.id_unidad_medida', '=', 'alm_und_medida.id_unidad_medida')
            ->leftJoin('almacen.alm_clasif','alm_clasif.id_clasificacion','=', 'alm_prod.id_clasif')
            ->leftJoin('almacen.alm_subcategoria','alm_subcategoria.id_subcategoria','=', 'alm_prod.id_subcategoria')
            ->leftJoin('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=', 'alm_subcategoria.id_categoria')
            ->leftJoin('almacen.alm_tp_prod','alm_tp_prod.id_tipo_producto','=', 'alm_cat_prod.id_tipo_producto')
            ->leftJoin('almacen.alm_req_archivos', 'alm_req_archivos.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')

            ->select(
                    'alm_det_req.id_detalle_requerimiento',
                    'alm_req.id_requerimiento',
                    'alm_req.codigo AS codigo_requerimiento',
                    'alm_det_req.id_requerimiento',
                    'alm_det_req.id_item AS id_item_alm_det_req',
                    'alm_det_req.precio_referencial',
                    'alm_det_req.cantidad',
                    'alm_det_req.unidad_medida',
                    'alm_det_req.obs',
                    'alm_det_req.partida',
                    'alm_det_req.fecha_registro AS fecha_registro_alm_det_req',
                    'alm_det_req.fecha_entrega',
                    'alm_det_req.descripcion_adicional',
                    'alm_det_req.estado',
                    'alm_req_archivos.id_archivo AS archivo_id_archivo',
                    'alm_req_archivos.archivo AS archivo_archivo',
                    'alm_req_archivos.estado AS archivo_estado',
                    'alm_req_archivos.fecha_registro AS archivo_fecha_registro',
                    'alm_req_archivos.id_detalle_requerimiento AS archivo_id_detalle_requerimiento',


                'alm_item.id_item AS alm_item_id_item',
                'alm_item.id_producto AS alm_item_id_producto',
                'alm_item.id_servicio',
                'alm_item.codigo AS alm_item_codigo',
                'alm_item.fecha_registro AS alm_item_fecha_registro',
                'alm_prod.id_producto AS alm_prod_id_producto',
                'alm_prod.codigo AS alm_prod_codigo',
                // 'alm_prod.codigo_anexo AS alm_prod_codigo_anexo',
                'alm_prod.descripcion AS alm_prod_descripcion',
                'alm_prod.id_unidad_medida',
                'alm_und_medida.descripcion AS unidad_medida_descripcion',
                'alm_und_medida.abreviatura AS unidad_medida_abreviatura',


                'alm_clasif.id_clasificacion',
                'alm_clasif.descripcion AS alm_clasif_descripcion',
                'alm_subcategoria.id_subcategoria',
                'alm_subcategoria.descripcion AS alm_subcategoria_descripcion',
                'alm_subcategoria.codigo AS alm_subcategoria_codigo',
                'alm_cat_prod.id_categoria',
                'alm_cat_prod.descripcion AS alm_cat_prod_descripcion',
                'alm_cat_prod.codigo AS alm_cat_prod_codigo',
                'alm_tp_prod.id_tipo_producto',
                'alm_tp_prod.descripcion AS alm_tp_prod_descripcion'
            
                    )
                    ->where([
                            ['alm_det_req.id_requerimiento', '=', $requerimiento[0]['id_requerimiento']],
                        ['alm_det_req.estado', '=', 1]
                        ])
                    ->orderBy('alm_item.id_item', 'asc')
                    ->get();

                }else if($requerimiento[0]['id_tipo_requerimiento']==2){  // alm_det_req- servicios
                $alm_det_req = DB::table('almacen.alm_item')
                ->leftJoin('almacen.alm_det_req', 'alm_item.id_item', '=', 'alm_det_req.id_item')
                ->leftJoin('logistica.log_servi', 'alm_item.id_servicio', '=', 'log_servi.id_servicio')
                ->leftJoin('logistica.log_tp_servi', 'log_tp_servi.id_tipo_servicio', '=', 'log_servi.id_tipo_servicio')
                ->leftJoin('almacen.alm_req_archivos', 'alm_req_archivos.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')

                ->select(
                    'alm_det_req.id_detalle_requerimiento',
                    'alm_det_req.id_requerimiento',
                    'alm_det_req.id_item AS id_item_alm_det_req',
                    'alm_det_req.precio_referencial',
                    'alm_det_req.unidad_medida',
                    'alm_det_req.cantidad',
                    'alm_det_req.descripcion_adicional',
                    'alm_det_req.obs',
                    'alm_det_req.fecha_registro AS fecha_registro_alm_det_req',
                    'alm_det_req.fecha_entrega',
                    'alm_det_req.estado',
                    'alm_req_archivos.id_archivo AS archivo_id_archivo',
                    'alm_req_archivos.archivo AS archivo_archivo',
                    'alm_req_archivos.estado AS archivo_estado',
                    'alm_req_archivos.fecha_registro AS archivo_fecha_registro',
                    'alm_req_archivos.id_detalle_requerimiento AS archivo_id_detalle_requerimiento',

                    'alm_item.id_item AS alm_item_id_item',
                    'alm_item.id_producto AS alm_item_id_producto',
                    'alm_item.id_servicio',
                    'alm_item.codigo AS alm_item_codigo',
                    'alm_item.fecha_registro AS alm_item_fecha_registro',
                    'log_servi.id_servicio',
                    'log_servi.codigo',
                    'log_servi.id_servicio',
                    'log_servi.descripcion',
                    'log_servi.id_tipo_servicio',
                    'log_tp_servi.descripcion AS log_tp_servi_descripcion'
                    )
                    ->where([
                        ['alm_det_req.id_requerimiento', '=', $requerimiento[0]['id_requerimiento']],
                        ['alm_det_req.estado', '=', 1]
                        ])
                    ->orderBy('alm_item.id_item', 'asc')
                    ->get();
                }
                // archivos adjuntos de items
                if(isset($alm_det_req)){
                    $detalle_requerimiento_adjunto=[];
                    foreach($alm_det_req as $data){
                                $detalle_requerimiento_adjunto[]=[
                                    'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                                    'archivo_id_archivo' => $data->archivo_id_archivo,
                                    'archivo_archivo' => $data->archivo_archivo,
                                    'archivo_id_detalle_requerimiento' => $data->archivo_id_detalle_requerimiento,
                                    'archivo_fecha_registro' => $data->archivo_fecha_registro,
                                    'archivo_estado' => $data->archivo_estado
                                ];
                    }
                }else{
                    $detalle_requerimiento_adjunto=[];
                }


                if(isset($alm_det_req)){
                    $lastId = "";
                    $detalle_requerimiento=[];
                    foreach($alm_det_req as $data){
                        if ($data->id_detalle_requerimiento !== $lastId) {
                            if($requerimiento[0]['id_tipo_requerimiento']==1){ // alm_det_req- bienes
                                $detalle_requerimiento[]=[
                                    'id_detalle_requerimiento'  =>$data->id_detalle_requerimiento,
                                    'id_requerimiento'  =>$data->id_requerimiento,
                                    'codigo_requerimiento'  =>$data->codigo_requerimiento,
                                    'id_item'                   =>$data->id_item_alm_det_req,
                                    'id_requerimiento'          =>$data->id_requerimiento,
                                    'cantidad'                  =>$data->cantidad,
                                    'precio_referencial'        =>$data->precio_referencial,
                                    'fecha_entrega'             =>$data->fecha_entrega,
                                    'fecha_registro'            =>$data->fecha_registro_alm_det_req,
                                    'codigo_item'                =>$data->alm_item_codigo,
                                    'id_producto'               =>$data->alm_prod_id_producto,
                                    'codigo_producto'            =>$data->alm_prod_codigo,
                                    'partida'                    =>$data->partida,
                                    'descripcion'               =>$data->alm_prod_descripcion,
                                    'id_unidad_medida'          =>$data->id_unidad_medida,
                                    'unidad_medida'             =>$data->unidad_medida,
                                    'unidad_medida_abreviatura'     =>$data->unidad_medida_abreviatura,
                                    'unidad_medida_descripcion'    =>$data->unidad_medida_descripcion,
                                    'id_clasificacion'  =>$data->id_clasificacion,
                                    'alm_clasif_descripcion'    =>$data->alm_clasif_descripcion,
                                    'id_subcategoria'   =>$data->id_subcategoria,
                                    'alm_subcategoria_descripcion'  =>$data->alm_subcategoria_descripcion,
                                    'alm_subcategoria_codigo'   =>$data->alm_subcategoria_codigo,
                                    'id_categoria'  =>$data->id_categoria,
                                    'alm_cat_prod_descripcion'  =>$data->alm_cat_prod_descripcion,
                                    'alm_cat_prod_codigo'   =>$data->alm_cat_prod_codigo,
                                    'id_tipo_producto'  =>$data->id_tipo_producto,
                                    'alm_tp_prod_descripcion'   =>$data->alm_tp_prod_descripcion,
                                    'obs'   => $data->obs,
                                    'descripcion_adicional' => $data->descripcion_adicional,
                                    'estado'          =>$data->estado

                                ];      
                                $lastId = $data->id_detalle_requerimiento;
                            }
                            else if($requerimiento[0]['id_tipo_requerimiento']==2){ // alm_det_req- servicios
                                $detalle_requerimiento[]=[
                                    'id_detalle_requerimiento'  =>$data->id_detalle_requerimiento,
                                    'id_item'                   =>$data->id_item_alm_det_req,
                                    'id_requerimiento'          =>$data->id_requerimiento,
                                    'cantidad'                  =>$data->cantidad,
                                    'unidad_medida'                  =>$data->unidad_medida,
                                    'precio_referencial'        =>$data->precio_referencial,
                                    'fecha_entrega'             =>$data->fecha_entrega,
                                    'fecha_registro'            =>$data->fecha_registro_alm_det_req,
                                    'codigo_item'               => $data->alm_item_codigo,
                                    'codigo_servicio'   => $data->codigo,
                                    'descripcion'       => $data->descripcion,
                                    'id_tipo_servicio'  => $data->id_tipo_servicio,
                                    'tipo_servicio'     => $data->log_tp_servi_descripcion,
                                    'estado'          =>$data->estado   
    
                                ];      
                                $lastId = $data->id_detalle_requerimiento;    
                            }
                        }     
                    }

                    // insertar adjuntos
                    for($j=0; $j< sizeof($detalle_requerimiento);$j++){
                            for($i=0; $i< sizeof($detalle_requerimiento_adjunto);$i++){
                                if($detalle_requerimiento[$j]['id_detalle_requerimiento'] === $detalle_requerimiento_adjunto[$i]['id_detalle_requerimiento']){
                                    if($detalle_requerimiento_adjunto[$i]['archivo_estado'] === NUll){
                                        $detalle_requerimiento_adjunto[$i]['archivo_estado']=0;
                                    }
                                    $detalle_requerimiento[$j]['adjunto'][]=$detalle_requerimiento_adjunto[$i];
                                }
                            }
                        }
                    // end insertar adjuntos

                }else{

                    $detalle_requerimiento=[];
                }

                //get cotizaciones que tenga el requerimiento
                $log_valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')
                ->select(
                    'log_valorizacion_cotizacion.id_cotizacion'
                    )
                    ->where(
                        [ 
                            ['log_valorizacion_cotizacion.id_requerimiento', '=', $id_requerimiento]
                        ])
                ->get();

                $cotizaciones=[];
                foreach($log_valorizacion_cotizacion as $data){
                    if(in_array($data->id_cotizacion, $cotizaciones)===false){
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
                $grupo_cotizacion=[];
                foreach($log_detalle_grupo_cotizacion as $data){
                    if(in_array($data->id_grupo_cotizacion, $grupo_cotizacion)===false){
                    array_push($grupo_cotizacion, $data->id_grupo_cotizacion);
                    }

                }
                ////////////////////////////////////////////


            $data =[
                "requerimiento"=>$requerimiento,
                "det_req"=>$detalle_requerimiento,
                "cotizaciones"=>$cotizaciones,
                "grupo_cotizacion"=>$grupo_cotizacion
                // "detalle_cotizacion"=>$detalle_cotizacion
            ];
  
    return $data;
    
    }
 
    public function mostrar_requerimiento($id, $codigo){
        $requerimiento= $this->get_requerimiento($id, $codigo);    
        return response()->json($requerimiento);

    }
    public function imprimir_requerimiento_pdf($id ,$codigo){
        $requerimiento= $this->get_requerimiento($id, $codigo);    
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
            <img src="./images/LogoSlogan-80.png" alt="Logo" height="75px">


                 <h1><center>REQUERIMIENTO N°'.$requerimiento['requerimiento'][0]['codigo'].'</center></h1>
                 <br><br>
            <table border="0">
            <tr>
                <td class="subtitle">REQ. N°</td>
                <td class="subtitle verticalTop">:</td>
                <td width="50%" class="verticalTop">'.$requerimiento['requerimiento'][0]['codigo'].'</td>
                <td class="subtitle verticalTop">Fecha</td>
                <td class="subtitle verticalTop">:</td>
                <td>'.$requerimiento['requerimiento'][0]['fecha_requerimiento'].'</td>
            </tr>
            </tr>  
                <tr>
                    <td class="subtitle">Solicitante</td>
                    <td class="subtitle verticalTop">:</td>
                     <td class="verticalTop">'.$requerimiento['requerimiento'][0]['persona'].'</td>
                     <td class="subtitle verticalTop">Tipo</td>
                     <td class="subtitle verticalTop">:</td>
                     <td>'.$requerimiento['requerimiento'][0]['tipo_requerimiento'].'</td>

                 
                </tr>
                <tr>
                    <td class="subtitle">Área</td>
                    <td class="subtitle verticalTop">:</td>
                    <td class="verticalTop">'.$requerimiento['requerimiento'][0]['area_descripcion'].'</td>
                </tr>
                <tr>
                    <td class="subtitle top">Proyecto</td>
                    <td class="subtitle verticalTop">:</td>
                    <td class="verticalTop justify" colspan="4" >'.$requerimiento['requerimiento'][0]['codigo_presupuesto'].'-'.$requerimiento['requerimiento'][0]['descripcion_proyecto'].'</td>

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
                <div class="texttab">'.$requerimiento['requerimiento'][0]['concepto'].'</div>';
                    
                $html.=   '</div>

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
 
$total=0;
    foreach($requerimiento['det_req'] as $key=>$data){
        $html .= '<tr>';
        $html .= '<td >'.($key+1).'</td>';
        $html .= '<td >'.$data['codigo_producto'].'</td>';
        $html .= '<td >'.$data['descripcion'].'</td>';
        $html .= '<td >'.$data['fecha_entrega'].'</td>';
        $html .= '<td >'.$data['unidad_medida_descripcion'].'</td>';
        $html .= '<td class="right">'.$data['cantidad'].'</td>';
        $html .= '<td class="right">S/.'.$data['precio_referencial'].'</td>';
        $html .= '<td class="right">S/.'.$data['cantidad']*$data['precio_referencial'].'</td>';
        $html .= '</tr>';
        $total = $total+ ($data['cantidad']*$data['precio_referencial']); 

    }


        $html .= '
        <tr>
            <td  class="right" style="font-weight:bold;" colspan="7">TOTAL</td>
            <td class="right">S/.'.$total.'</td>
        </tr>
        </table>
            <br/>
            <br/>
        
            <div class="right">Usuario: '.$requerimiento['requerimiento'][0]['usuario'].' Fecha de Registro:'.$requerimiento['requerimiento'][0]['fecha_registro'].'</div>
        

        </body>
        </html>';

        return $html;
 
    }

    public function generar_requerimiento_pdf($id ,$codigo){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->imprimir_requerimiento_pdf($id ,$codigo));
        return $pdf->stream();
        return $pdf->download('requerimiento.pdf');
    
    }























    public function leftZero($lenght, $number){
        $nLen = strlen($number);
        $zeros = '';
        for($i=0; $i<($lenght-$nLen); $i++){
            $zeros = $zeros.'0';
        }
        return $zeros.$number;
    }


 
    
    public function guardar_requerimiento(Request $request){
        $abreviatura_documento = DB::table('administracion.adm_tp_docum')
        ->select('adm_tp_docum.*')
        ->where('adm_tp_docum.id_tp_documento', $request->id_tp_documento)
        ->first();
        $grupo_descripcion = DB::table('administracion.adm_grupo')
        ->select('adm_grupo.descripcion')
        ->where('adm_grupo.id_grupo', $request->id_grupo)
        ->first();
        //---------------------GENERANDO CODIGO REQUERIMIENTO--------------------------
        $mes = date('m',strtotime($request->fecha_requerimiento));
        $yyyy = date('Y',strtotime($request->fecha_requerimiento));
        $anio = date('y',strtotime($request->fecha_requerimiento));
        $documento = $abreviatura_documento->abreviatura;
        $grupo = $grupo_descripcion->descripcion[0];
        $num = DB::table('almacen.alm_req')
        ->whereMonth('fecha_requerimiento', '=', $mes)
        ->whereYear('fecha_requerimiento', '=', $yyyy)
        ->count();
        $correlativo = $this->leftZero(4,($num+1));
        $codigo = "{$documento}{$grupo}-{$anio}{$correlativo}";
        //----------------------------------------------------------------------------
        $data_req = DB::table('almacen.alm_req')->insertGetId(
            [
            'codigo'                => $codigo,
            'id_tipo_requerimiento' => $request->id_tipo_requerimiento,
            'id_usuario'            => $request->id_usuario,
            'id_rol'                => $request->id_rol,
            'fecha_requerimiento'   => $request->fecha_requerimiento,
            'concepto'              => $request->concepto,
            'id_grupo'              => $request->id_grupo,
            'id_area'               => $request->id_area,
            'id_proyecto'           => $request->id_proyecto,
            'id_presupuesto'        => $request->id_presupuesto,
            'id_moneda'             => $request->id_moneda,
            'estado'                => 1,
            'fecha_registro'        => $request->fecha_registro,
            'occ'                   => $request->occ,
            'id_prioridad'          => $request->id_prioridad,
            'id_estado_doc'         => $request->id_estado_doc
            ],
            'id_requerimiento'
        );
        
        $detalle_reqArray = $request->detalle;
        $count_detalle_req= count($detalle_reqArray);
        if($count_detalle_req>0){
            for ($i=0; $i< $count_detalle_req; $i++){
                if($detalle_reqArray[$i]['estado']>0){
                            $alm_det_req = DB::table('almacen.alm_det_req')->insertGetId(
                                
                                [
                                    'id_requerimiento'      => $data_req, 
                                    'id_item'               => $detalle_reqArray[$i]['id_item'],
                                    'precio_referencial'    => $detalle_reqArray[$i]['precio_referencial'], 
                                    'cantidad'              => $detalle_reqArray[$i]['cantidad'], 
                                    'fecha_entrega'         => $detalle_reqArray[$i]['fecha_entrega'] ?? '', 
                                    'descripcion_adicional' => $detalle_reqArray[$i]['descripcion'], 
                                    //'obs'                   => $detalle_reqArray[$i]['obs'], 
                                    'partida'               => $detalle_reqArray[$i]['partida'], 
                                    'unidad_medida'         => $detalle_reqArray[$i]['unidad_medida_descripcion'], 
                                    'fecha_registro'        => date('Y-m-d H:i:s'),
                                    'estado'                => 1
                                ],
                                'id_detalle_requerimiento'
                            );
                            $count_det_partidas =count($detalle_reqArray[$i]['det_partidas']);
                            if($count_det_partidas > 0){
                                for ($j=0; $j< $count_det_partidas; $j++){ 
                                    $proy_pdetalle = DB::table('proyectos.proy_pdetalle')->insertGetId(
                                        [
                                            'id_det_req'            => $alm_det_req, 
                                            'id_cd_partida'         => $detalle_reqArray[$i]['det_partidas'][$j]['id_cd_partida'],
                                            'id_gg_detalle'         => $detalle_reqArray[$i]['det_partidas'][$j]['id_gg_detalle'],
                                            'id_ci_detalle'         => $detalle_reqArray[$i]['det_partidas'][$j]['id_ci_detalle'],
                                            'cantidad'              => $detalle_reqArray[$i]['det_partidas'][$j]['cantidad'],
                                            'cantidad_anulada'      => $detalle_reqArray[$i]['det_partidas'][$j]['cantidad_anulada'],
                                            'importe_unitario'      => $detalle_reqArray[$i]['det_partidas'][$j]['importe_unitario'],
                                            'id_insumo'             => $detalle_reqArray[$i]['det_partidas'][$j]['id_insumo'],
                                            'id_item'               => $detalle_reqArray[$i]['det_partidas'][$j]['id_item'],
                                            'importe_parcial'       => $detalle_reqArray[$i]['det_partidas'][$j]['importe_parcial'],
                                            'unid_medida'           => $detalle_reqArray[$i]['det_partidas'][$j]['unid_medida'],
                                            'cantidad_ejec'         => $detalle_reqArray[$i]['det_partidas'][$j]['cantidad_ejec'],
                                            'importe_unitario_ejec' => $detalle_reqArray[$i]['det_partidas'][$j]['importe_unitario_ejec'],
                                            'importe_parcial_ejec'  => $detalle_reqArray[$i]['det_partidas'][$j]['importe_parcial_ejec'],
                                            'fecha_requerimiento'   => $detalle_reqArray[$i]['det_partidas'][$j]['fecha_requerimiento'],
                                            'usuario'               => $detalle_reqArray[$i]['det_partidas'][$j]['usuario'],
                                            'fecha_registro'        => $detalle_reqArray[$i]['det_partidas'][$j]['fecha_registro'],
                                            'estado'                => $detalle_reqArray[$i]['det_partidas'][$j]['estado'],
                                        ],
                                        'id_det_partida'
                                    );
                                }
                            }
                        }
                            }    
        }
    return response()->json($data_req);
    }

    public function update_requerimiento(Request $request,$id){

            // obtener  los id_detalle_requerimiento que actualmete existen en la BD
            // $ReqInDB = $this->mostrar_requerimientoX(($id);

            // $ActualReqInDB= $ReqInDB->original;
            // $ActualDetReqInDB= $ActualReqInDB["det_req"];

            // $count_actual_de_req =count($ActualDetReqInDB);
            // $ActualDet_req=[];

            // for ($i=0; $i< $count_actual_de_req; $i++){
            // $ActualDet_req[]= $ActualDetReqInDB[$i]['id_detalle_requerimiento'];
            // }
             //----------------------------------------------------------------------

        if($request->codigo != NULL){

            $data = DB::table('almacen.alm_req')->where('id_requerimiento', $id)
            ->update([
                'codigo'                => $request->codigo,
                'id_tipo_requerimiento' => $request->id_tipo_requerimiento,
                'id_usuario'            => $request->id_usuario,
                'id_rol'                => $request->id_rol,
                'fecha_requerimiento'   => $request->fecha_requerimiento,
                'concepto'              => $request->concepto,
                'objetivo'              => $request->objetivo,
                'id_grupo'              => $request->id_grupo,
                'id_area'               => $request->id_area,
                'id_proyecto'           => $request->id_proyecto,
                'id_presupuesto'        => $request->id_presupuesto,
                'estado'                => $request->estado,
                'occ'                   => $request->occ,
                'id_prioridad'          => $request->id_prioridad,
                'id_estado_doc'         => $request->id_estado_doc               
                ]);
            }else{
                $data=0;
            }
        $detalle_reqArray = $request->detalle;
 
         // $detalle_reqArray = json_decode($a, true); //// 
 
        //$detalle_reqArray = json_decode($detalle_req, true); //// 
         $count_detalle_req= count($detalle_reqArray);      
        if($data > 0){
            for ($i=0; $i< $count_detalle_req; $i++){
                //   if(in_array($detalle_reqArray[$i]['id_detalle_requerimiento'], $ActualDet_req)){
                  if(empty($detalle_reqArray[$i]['id_detalle_requerimiento'])=== false){ // empty = false =>existe variable
   
                    //actualiza
                    $data2 = DB::table('almacen.alm_det_req')->where('id_detalle_requerimiento', $detalle_reqArray[$i]['id_detalle_requerimiento'])
                    ->update([
                        'id_requerimiento'      => $data, 
                        'id_item'               => $detalle_reqArray[$i]['id_item'],
                        'precio_referencial'    => $detalle_reqArray[$i]['precio_referencial'], 
                        'cantidad'              => $detalle_reqArray[$i]['cantidad'], 
                        'fecha_entrega'         => $detalle_reqArray[$i]['fecha_entrega'], 
                        'descripcion_adicional' => $detalle_reqArray[$i]['descripcion_adicional'], 
                        //'obs'                   => $detalle_reqArray[$i]['obs'], 
                        'partida'              => $detalle_reqArray[$i]['partida'], 
                        'unidad_medida'         => $detalle_reqArray[$i]['unidad_medida_descripcion'], 
                         'estado'               => 1
                    ]);
                }else{
                //     //guardar nuevo
                    $data_req = DB::table('almacen.alm_det_req')->insert(
                        [
                            'id_requerimiento'      => $data, 
                            'id_item'               => $detalle_reqArray[$i]['id_item'],
                            'precio_referencial'    => $detalle_reqArray[$i]['precio_referencial'], 
                            'cantidad'              => $detalle_reqArray[$i]['cantidad'], 
                            'fecha_entrega'         => $detalle_reqArray[$i]['fecha_entrega'], 
                            'descripcion_adicional' => $detalle_reqArray[$i]['descripcion'], 
                            //'obs'                   => $detalle_reqArray[$i]['obs'], 
                            'partida'              => $detalle_reqArray[$i]['partida'], 
                            'unidad_medida'         => $detalle_reqArray[$i]['unidad_medida_descripcion'], 
                            'fecha_registro'        => date('Y-m-d H:i:s'),
                            'estado'                => 1
                        ] 
                    );
                }
 
            }


        }
        // $item_delete = $request->itemfordelete;
        // $count_item_delete =count($item_delete);

        // // if($item_delete > 0){
        // //     for ($i=0; $i< $count_item_delete; $i++){
        // //         $data = DB::table('alm_det_req')->where('id_detalle_requerimiento', $item_delete[$i]['id_detalle_requerimiento'])
        // //         ->update([
        // //             'estado' => 0
        // //         ]);
        // //      }
        // // }
        return response()->json($data);
    
        // return response()->json($count_detalle_req);
     }
    //Productos
    public function mostrar_bien_items(){
        $data = DB::table('almacen.alm_item')
        ->select(
                'alm_item.id_item',
                'alm_item.codigo',
                'alm_item.id_producto',
                'alm_prod.descripcion',
                'alm_prod.id_unidad_medida',
                'alm_und_medida.descripcion AS unidad_medida_descripcion',
                'alm_und_medida.abreviatura AS unidad_medida_abrev',
                'alm_prod_ubi.stock',
                
                'alm_tp_prod.id_tipo_producto', 
                'alm_tp_prod.descripcion as tipo_producto_descripcion',
                'alm_cat_prod.id_categoria', 
                'alm_cat_prod.descripcion as categoria_producto_descripcion',
                'alm_subcategoria.id_subcategoria',
                'alm_subcategoria.descripcion as subcategoria_producto_descripcion'
                )
            ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
             ->leftJoin('almacen.alm_prod_ubi','alm_prod_ubi.id_producto','=','alm_prod.id_producto')
             ->join('almacen.alm_subcategoria','alm_subcategoria.id_subcategoria','=','alm_prod.id_subcategoria')
            ->join('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=','alm_subcategoria.id_categoria')
            ->join('almacen.alm_tp_prod','alm_tp_prod.id_tipo_producto','=','alm_cat_prod.id_tipo_producto')
            ->leftJoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            // ->where([
            //     ['alm_item.id_producto', '>', 0]
            //  ])
            //->limit(500)
             ->get();
         return response()->json($data);
    }

    
    public function mostrar_archivos_adjuntos($id_cotizacion){
  
        $valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')
        ->select(
                'log_valorizacion_cotizacion.id_detalle_requerimiento'
                )
           
            ->where([
                ['log_valorizacion_cotizacion.id_cotizacion', '=', $id_cotizacion]
            ])
             ->get();
             
             foreach($valorizacion_cotizacion as $data){
                $id_detalles_req[]= $data->id_detalle_requerimiento;
             }

             if(isset($id_detalles_req) == true){

                 $adjuntos = DB::table('almacen.alm_req_archivos')
                 ->select(
                     'alm_req_archivos.*'
                     )
                     
                     ->whereIn('alm_req_archivos.id_detalle_requerimiento',$id_detalles_req)
                     ->get();
                     return response()->json($adjuntos);
                    }
         return response()->json(0);
                    
                 
    }

    public function mostrar_producto($id_producto){
        $data = DB::table('almacen.alm_item')
        ->select(
                'alm_item.id_item',
                'alm_item.codigo AS codigo_item',
                'alm_item.id_producto',
                'alm_prod.descripcion',
                 'alm_prod.id_unidad_medida',
                'alm_und_medida.descripcion AS unidad_medida_descripcion',
                'alm_und_medida.abreviatura AS unidad_medida_abrev',
                'alm_tp_prod.descripcion as tipo_producto_descripcion',
                'alm_cat_prod.id_categoria', 
                'alm_cat_prod.descripcion as categoria_producto_descripcion',
                'alm_subcategoria.id_subcategoria',
                'alm_subcategoria.descripcion as subcategoria_producto_descripcion'
                )
            ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
            ->join('almacen.alm_subcategoria','alm_subcategoria.id_subcategoria','=','alm_prod.id_subcategoria')
            ->join('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=','alm_subcategoria.id_categoria')
            ->join('almacen.alm_tp_prod','alm_tp_prod.id_tipo_producto','=','alm_cat_prod.id_tipo_producto')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            ->where([
                 ['alm_item.id_producto', '=', $id_producto]
             ])
             ->get();
         return response()->json($data);
    }

    public function mostrar_item($cod_req)
    {
 
       $data = DB::table('almacen.alm_item')
       ->leftJoin('almacen.alm_det_req', 'alm_item.id_item', '=', 'alm_det_req.id_item')
       ->leftJoin('almacen.alm_prod', 'alm_item.id_producto', '=', 'alm_prod.id_producto')
       ->leftJoin('almacen.alm_und_medida', 'alm_prod.id_unidad_medida', '=', 'alm_und_medida.id_unidad_medida')
 
       ->select(
        'alm_det_req.id_detalle_requerimiento',
        'alm_det_req.id_requerimiento',
        'alm_det_req.id_item AS id_item_alm_det_req',
        'alm_det_req.precio_referencial',
        'alm_det_req.cantidad',
        'alm_det_req.precio_referencial',
        'alm_det_req.observaciones',
        'alm_det_req.fecha_registro AS fecha_registro_alm_det_req',
        'alm_det_req.fecha_entrega',


           'alm_item.id_item AS alm_item_id_item',
           'alm_item.id_producto AS alm_item_id_producto',
           'alm_item.id_servicio',
           'alm_item.codigo AS alm_item_codigo',
           'alm_item.estado AS alm_item_estado',
           'alm_item.fecha_registro AS alm_item_fecha_registro'
           )
          ->where([
                ['alm_det_req.id_requerimiento', '=', $cod_req],
              ['alm_item.estado', '=', 1]
             ])
          ->orderBy('alm_item.id_item', 'asc')
      ->get();
       return response()->json(["item"=>$data]);
     
    } 
    

    //servicios
    public function mostrar_servicios(){
        $data = DB::table('logistica.log_servi')
        ->select('log_servi.*', 'log_tp_servi.descripcion as tp_servi_descripcion',
                'log_tp_servi.estado as tp_servi_estado'
                )
        ->leftJoin('logistica.log_tp_servi','log_tp_servi.id_tipo_servicio','=','log_servi.id_tipo_servicio')
            ->where([['log_tp_servi.estado', '=', 1]])
            ->get();
        return response()->json($data);
    }
    public function mostrar_servicio($id){
        $data = DB::table('almacen.alm_item')
        ->select(
                'alm_item.codigo AS codigo_item',
                'alm_item.*',
                'log_servi.*',
                'log_tp_servi.id_tipo_servicio',
                'log_tp_servi.descripcion as tipo_descripcion'
                // 'log_cat_serv.descripcion as cat_descripcion'
                )
        ->join('logistica.log_servi','log_servi.id_servicio','=','alm_item.id_servicio')
        // ->join('log_cat_serv','log_cat_serv.id_categoria','=','log_servi.id_cat_servicio')
        ->join('logistica.log_tp_servi','log_tp_servi.id_tipo_servicio','=','log_servi.id_tipo_servicio')
            ->where([['log_servi.id_servicio', '=', $id]])
            ->get();
        return response()->json($data);
    }

    public function mostrar_proveedores(){
        $proveedores = DB::table('logistica.log_prove')
        ->select(
            'log_prove.id_proveedor',
            'log_prove.codigo',
            'adm_contri.id_contribuyente', 
            'adm_contri.nro_documento', 
            'adm_contri.id_tipo_contribuyente', 
             'adm_contri.razon_social', 
            'adm_contri.direccion_fiscal', 
            'adm_contri.ubigeo', 
            'adm_contri.id_pais', 
            'sis_identi.id_doc_identidad', 
            'sis_identi.descripcion AS identi_descripcion', 
            
            'adm_tp_contri.descripcion AS contri_descripcion', 

            'adm_ctb_contac.nombre AS contac_nombre',
            'adm_ctb_contac.telefono AS contac_telefono',
            'adm_ctb_contac.email AS contac_email',
            'adm_ctb_contac.cargo AS contac_cargo',

            'adm_ctb_rubro.id_rubro AS rubro_id_rubro',
            
            'adm_rubro.descripcion AS rubro_descripcion'

                )
        ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->leftJoin('contabilidad.adm_tp_contri','adm_tp_contri.id_tipo_contribuyente','=','adm_contri.id_tipo_contribuyente')
        ->leftJoin('contabilidad.sis_identi','sis_identi.id_doc_identidad','=','adm_contri.id_doc_identidad')
        ->leftJoin('contabilidad.adm_ctb_contac','adm_ctb_contac.id_contribuyente','=','adm_contri.id_contribuyente')
        ->leftJoin('contabilidad.adm_ctb_rubro','adm_ctb_rubro.id_contribuyente','=','adm_contri.id_contribuyente')
        ->leftJoin('contabilidad.adm_rubro','adm_rubro.id_rubro','=','adm_ctb_rubro.id_rubro')
            ->where(
                    [['adm_contri.estado', '=', 1],
                    ['adm_tp_contri.id_tipo_contribuyente', '=', 3],
                    ['log_prove.estado','=',1]
                    ])
            ->get();
            
            $lastId = "";
            if(sizeof($proveedores)>0){
                foreach($proveedores as $data){
                    if ($data->id_proveedor !== $lastId) {
                        $proveedorArray[] = [
                            'id_proveedor'=> $data->id_proveedor,
                            'codigo'=> $data->codigo,
                            'id_contribuyente'=> $data->id_contribuyente,
                            'nro_documento'=> $data->nro_documento,
                            'id_tipo_contribuyente'=> $data->id_tipo_contribuyente,
                            'razon_social'=> $data->razon_social,
                            'direccion_fiscal'=> $data->direccion_fiscal,
                            'ubigeo'=> $data->ubigeo,
                            'id_pais'=> $data->id_pais,
                            'id_doc_identidad'=> $data->id_doc_identidad,
                            'identi_descripcion'=> $data->identi_descripcion,
                            'contri_descripcion'=> $data->contri_descripcion
                        ];
                    $lastId = $data->id_proveedor;
                    }

                    $proveedorContactoArray[] = [
                        'id_proveedor'=> $data->id_proveedor,
                        'contac_nombre'=> $data->contac_nombre,
                        'contac_telefono'=> $data->contac_telefono,
                        'contac_email'=> $data->contac_email,
                        'contac_cargo'=> $data->contac_cargo,
                        'rubro_id_rubro'=> $data->rubro_id_rubro,
                        'rubro_descripcion'=> $data->rubro_descripcion
                     ];
                }

                for($j=0; $j< sizeof($proveedorContactoArray);$j++){
                    for($i=0; $i< sizeof($proveedorArray);$i++){
                        if($proveedorContactoArray[$j]['id_proveedor'] === $proveedorArray[$i]['id_proveedor']){
                            $proveedorArray[$i]['contacto'][]=$proveedorContactoArray[$j];
                        }
                    }
                }
            }

            
        return response()->json($proveedorArray);

    }

    public function mostrar_empresas(){
        $data = DB::table('administracion.adm_empresa')
        ->select(
            'adm_empresa.id_empresa',
            'adm_empresa.id_contribuyente',
            'adm_empresa.codigo',
 
            'adm_contri.id_contribuyente', 
            'adm_contri.nro_documento', 
            'adm_contri.id_tipo_contribuyente', 
            'adm_contri.razon_social', 
            'adm_contri.direccion_fiscal', 
            'adm_contri.ubigeo', 
            'adm_contri.id_pais', 
            'sis_identi.descripcion AS identi_descripcion', 
            
            'adm_tp_contri.descripcion AS contri_descripcion'
                )
        ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
        ->leftJoin('contabilidad.adm_tp_contri','adm_tp_contri.id_tipo_contribuyente','=','adm_contri.id_tipo_contribuyente')
        ->leftJoin('contabilidad.sis_identi','sis_identi.id_doc_identidad','=','adm_contri.id_doc_identidad')
 
            ->where(
                    [['adm_empresa.estado', '=', 1],
                     ['adm_contri.estado','=',1]
                    ])
            ->get();
    
        return response()->json($data);

    }

    public function mostrar_cotizaciones(){
        $data = DB::table('logistica.log_cotizacion')
        ->select(
            'log_cotizacion.id_cotizacion',
            'alm_req.codigo',
            'log_cotizacion.codigo_cotizacion',
            'adm_contri.razon_social',
            // 'adm_contri.id_doc_identidad',
            'sis_identi.descripcion AS tipo_documento',
            'adm_contri.nro_documento',
            'log_cotizacion.estado_envio'
                )
        ->leftJoin('logistica.log_prove','log_prove.id_proveedor','=','log_cotizacion.id_proveedor')
        ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi','sis_identi.id_doc_identidad','=','adm_contri.id_doc_identidad')
        ->leftJoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_cotizacion','=','log_cotizacion.id_cotizacion')
        ->leftJoin('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','log_valorizacion_cotizacion.id_detalle_requerimiento')
        ->leftJoin('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
        ->where(
                [['log_cotizacion.estado', '=', 1]
                ])
        ->groupBy('log_cotizacion.id_cotizacion','alm_req.codigo','log_cotizacion.codigo_cotizacion','adm_contri.razon_social','sis_identi.descripcion','adm_contri.nro_documento','log_cotizacion.estado_envio')

        ->get();
        return response()->json($data);
    }

    public function mostrar_grupo_cotizacion($num_req){
   

        $log_grupo_cotizacion = DB::table('almacen.alm_req')
        ->select(
            'log_grupo_cotizacion.id_grupo_cotizacion',
            'log_grupo_cotizacion.codigo_grupo',
            'log_grupo_cotizacion.id_usuario',
            'log_grupo_cotizacion.fecha_inicio',
            'log_grupo_cotizacion.fecha_fin',
            'log_grupo_cotizacion.estado'
            )
             ->leftJoin('logistica.log_detalle_grupo_cotizacion','log_detalle_grupo_cotizacion.id_requerimiento','=','alm_req.id_requerimiento')
             ->leftJoin('logistica.log_grupo_cotizacion','log_grupo_cotizacion.id_grupo_cotizacion','=','log_detalle_grupo_cotizacion.id_grupo_cotizacion')
             ->where(
                [ 
                    ['alm_req.codigo', '=', $num_req]
                ])
        ->get();
            

        return response()->json($log_grupo_cotizacion);

    }

    // public function especificacion_compra_fill_input($id_especificacion_compra){
    //     $data = DB::table('logistica.log_esp_compra')
    //     ->select(
    //         'log_esp_compra.id_especificacion_compra',
    //         'log_esp_compra.id_condicion_pago',
    //         'log_esp_compra.plazo_entrega',
    //         'log_esp_compra.fecha_entrega',
    //         'log_esp_compra.lugar_entrega',
    //         'log_esp_compra.detalle_envio',
    //         'log_esp_compra.observacion',
    //         'log_esp_compra.forma_pago_credito'
    //             )
    //     // ->leftJoin('logistica.log_prove','log_prove.id_proveedor','=','log_cotizacion.id_proveedor')
    //     // ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
    //     // ->leftJoin('contabilidad.sis_identi','sis_identi.id_doc_identidad','=','adm_contri.id_doc_identidad')
    //     // ->leftJoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_cotizacion','=','log_cotizacion.id_cotizacion')
    //     // ->leftJoin('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','log_valorizacion_cotizacion.id_detalle_requerimiento')
    //     // ->leftJoin('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
    //     ->where(
    //             [['log_esp_compra.id_especificacion_compra', '=', $id_especificacion_compra]
    //             ])
 
    //     ->get();
    //     return response()->json($data);
    // }


    public function actualizar_cotizacion(Request $request,$id_grupo_cotizacion){

        $detalle_grupo_cotizacion =$request->detalle_grupoCotizacion;
        $grupo_cotizacion =$request->grupo_cotizacion;
        $cotizaciones  = $request->cotizaciones;
        
        $count_cotizacion = count($cotizacion);      

         for($i=0; $i< $count_cotizacion; $i++) {
           
            if($cotizaciones[$i]['id_cotizacion'] > 0){ // si existe id => actualiza

                $data_cotizacion = DB::table('logistica.log_cotizacion')
                ->where('id_cotizacion', '=',  $cotizaciones[$i]['id_cotizacion'])
                ->update([
                    // 'id_detalle_grupo_cotizacion'   => $detalle_grupoCotizacion['id_detalle_grupo_cotizacion'],
                    // 'codigo_cotizacion'             => $cotizaciones[$i]['codigo_cotizacion'],
                    'id_proveedor'                  => $cotizaciones[$i]['proveedor']['id_proveedor'],
                    // 'codigo_proveedor'              => $cotizaciones[$i]['codigo_proveedor'],
                    'estado_envio'                  => $cotizaciones[$i]['estado_envio'],
                    'estado'                        => $cotizaciones[$i]['estado'],
                    'id_empresa'                    => $cotizaciones[$i]['empresa']['id_empresa'],
                    'email_proveedor'               => $cotizaciones[$i]['proveedor']['contacto']['email']
                    ]);
    

            }else{ // debe agregar una nueva cotizacion


                $mes = date('m',strtotime("now"));
                $anio = date('y',strtotime("now"));
                $num = DB::table('logistica.log_cotizacion')
                ->count();
                $correlativo = $this->leftZero(4,($num+1));
                $codigo = "CO-{$anio}{$mes}-{$correlativo}";

                $log_cotizacion = DB::table('logistica.log_cotizacion')->insertGetId(
                    [
                    'codigo_cotizacion'             => $codigo,
                    'id_empresa'                    => $cotizaciones[$i]['empresa']['id_empresa'],
                    'id_proveedor'                  => $cotizaciones[$i]['proveedor']['id_proveedor'],
                    'codigo_proveedor'              => $cotizaciones[$i]['proveedor']['codigo_proveedor'],
                    'estado_envio'                  => 0,
                    'email_proveedor'               => $cotizaciones[$i]['proveedor']['contacto']['email'],
                    'detalle'                       => $cotizaciones[$i]['detalle'],
                    'estado'                        => 1
                    ],
                    'id_cotizacion'
                );
                

                for ($j=0; $j< count($cotizaciones[$i]['items']); $j++){

                    $especificacion_compra = DB::table('logistica.log_esp_compra')->insertGetId(
                        [
                        'estado'         => 1,
          
                        ],
                        'id_especificacion_compra'
                    );

                    $log_valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')->insertGetId(
                    [
                    'id_cotizacion'             => $log_cotizacion,
                    'id_especificacion_compra'  => $especificacion_compra,
                    'id_detalle_requerimiento'  => $cotizaciones[$i]['items'][$j]['id_detalle_requerimiento'],
                    'estado'                    => 1,
 
                        ],
                        'id_valorizacion_cotizacion'
                    );
                }


            }


        }
        
        // $cotizacion_old = DB::table('logistica.log_cotizacion')
        // ->select(
        //     'log_cotizacion.id_cotizacion',
        //     'log_cotizacion.id_detalle_grupo_cotizacion',
        //     'log_cotizacion.codigo_cotizacion',
        //     'log_cotizacion.id_proveedor',
        //     'log_cotizacion.codigo_proveedor',
        //     'log_cotizacion.id_especificacion_compra',
        //     'log_cotizacion.estado_envio',
        //     'log_cotizacion.estado',
        //     'log_cotizacion.id_empresa',
        //     'log_cotizacion.email_proveedor'
        //         )
        //     ->where(
        //         [['log_cotizacion.estado', '>', 0],
        //         ['log_cotizacion.id_detalle_grupo_cotizacion', '=', $id_detalle_grupo_cotizacion]
        //         ])
        // ->get();
          
        // $count_cotizacion_old = count($cotizacion_old);
        // $coti_old = json_decode($cotizacion_old, true);
        // $clean_cotiza=array(); 

        // if($count_cotizacion == 0){ // caso que se eleminaran todos
        //         for($i=0; $i< $count_cotizacion_old; $i++) {
        //             array_push($clean_cotiza, $coti_old[$i]['id_cotizacion']) ;
        //         }
        
        // }

        // if($count_cotizacion > 0){ //   solo se elimino algunos 

        //     for($i=0; $i< $count_cotizacion_old; $i++) {
        //         for($j=0; $j< $count_cotizacion; $j++) {
        //             if(!in_array($coti_old[$i]['id_cotizacion'], $cotizacion[$j])){                        
        //                 array_push($clean_cotiza, $coti_old[$i]['id_cotizacion']) ;
        //             }


        //         }   
        //     }

        // }   
        
        // $clean_cotiza_array=array_unique($clean_cotiza);
        

        return response()->json($data_especificacion_compra);

    }
    public function remover_cotizacion($id){

        $cotizacion = DB::table('logistica.log_cotizacion')
        ->select('log_cotizacion.*')
        ->where([
            ['log_cotizacion.id_cotizacion', '=', $id],
            ['log_cotizacion.estado', '=', 1]
            ])
         ->get();

        $data_cotizacion = DB::table('logistica.log_cotizacion')
        ->where('id_cotizacion','=', $id)
        ->update([
            'estado'        => 0
        ],'id_cotizacion');


        $data_valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')
        ->where('id_cotizacion','=', $cotizacion[0]->id_cotizacion)
        ->update([
            'estado'        => 0
        ]);
        $data_detalle_grupo_cotizacion = DB::table('logistica.log_detalle_grupo_cotizacion')
        ->where('id_cotizacion','=', $cotizacion[0]->id_cotizacion)
        ->update([
            'estado'        => 0
        ]);


        return response()->json($data_detalle_grupo_cotizacion);

    }
    


    public function mostrar_documentos_pendientes($id_usuario,$id_rol){

            $usuario_data=DB::table('rrhh.rrhh_rol')
            ->select(  
            'rrhh_rol.id_area'
                    )
            ->where([
                ['rrhh_rol.id_rol', '=', $id_rol]
                ])
            ->get();
  
            $req_nuevos = DB::table('almacen.alm_req')
            ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.id_estado_doc')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
            // ->leftJoin('adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            ->leftJoin('proyectos.proy_proyecto', 'alm_req.id_proyecto', '=', 'proy_proyecto.id_proyecto')
            ->leftJoin('administracion.adm_documentos_aprob', 'alm_req.codigo', '=', 'adm_documentos_aprob.codigo_doc')
 
            ->select(  
                'alm_req.id_requerimiento'
                // ,'alm_req.codigo'
                // ,'alm_req.id_grupo'
                // ,'alm_req.id_prioridad'
                     )
                ->where([
                    ['alm_req.estado', '=', 1],
                    ['adm_documentos_aprob', '=', null],
                    ['alm_req.id_area', '=', $usuario_data[0]->id_area],
                    ['adm_estado_doc.estado_doc', '=', "Elaborado"]
                    ])
                ->orderBy('alm_req.id_requerimiento', 'asc')
            ->get();

            $req = DB::table('almacen.alm_req')
            ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.id_estado_doc')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
            ->leftJoin('proyectos.proy_proyecto', 'alm_req.id_proyecto', '=', 'proy_proyecto.id_proyecto')
            // ->leftJoin('adm_operacion', 'alm_req.id_prioridad', '=', 'adm_operacion.id_prioridad')
             ->leftJoin('administracion.adm_operacion', function ($join) {
                $join->on('almacen.alm_req.id_prioridad', '=', 'adm_operacion.id_prioridad')
                ->on('almacen.alm_req.id_grupo', '=', 'adm_operacion.id_grupo');
            })
            ->select(  
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.id_grupo',
                'alm_req.id_prioridad',
                'adm_operacion.id_operacion'
        
                   )
                ->where([
                    ['alm_req.estado', '=', 1],
                    ['adm_estado_doc.estado_doc', '=', "Elaborado"]
                    ])
                ->orderBy('alm_req.id_requerimiento', 'asc')
            ->get();

            if(sizeof($req)>0){
            foreach($req as $data){ 
                $req_array[]=[
                'id_requerimiento'=> $data->id_requerimiento,
                'codigo'=> $data->codigo,
                'id_grupo'=> $data->id_grupo,
                'id_operacion'=> $data->id_operacion,
                'id_prioridad'=> $data->id_prioridad
                 ];      
            };
        }else{
            $req_array=[];
        }
        $flujo_rol_array=[];
            $flujo_rol = DB::table('administracion.adm_flujo')
             ->select(  
                'adm_flujo.id_flujo',
                'adm_flujo.id_operacion',
                'adm_flujo.id_rol',
                'adm_flujo.nombre',
                'adm_flujo.orden',
                'adm_flujo.estado'
                   )
                ->where([
                ['adm_flujo.estado', '=', 1],
                ['adm_flujo.id_rol', '=', $id_rol]
                 ])
            ->orderBy('adm_flujo.orden', 'asc')
            ->get();
            foreach($flujo_rol as $data){ 
                $flujo_rol_array[]=[
                'id_flujo'=> $data->id_flujo,
                'id_operacion'=> $data->id_operacion,
                'id_rol'=> $data->id_rol,
                'orden'=> $data->orden
                 ];      
            };
            
            // $new_req_array = $req_array;
            $new_req_array = Array();
             for($i=0;$i<sizeof($req_array);$i++){
                for($j=0;$j<sizeof($flujo_rol_array);$j++){
                    if( $req_array[$i]['id_operacion'] ==  $flujo_rol_array[$j]['id_operacion']){
                        //  $new_req_array[$i]['id_requerimiento']= $req_array[$i]['id_requerimiento'];
                        //  $new_req_array[$i]['orden']= $flujo_rol_array[$j]['orden'];
                        array_push($new_req_array,array(
                                                        'id_requerimiento' => $req_array[$i]['id_requerimiento']
                                                        ,'codigo' => $req_array[$i]['codigo']
                                                        ,'id_operacion' => $flujo_rol_array[$j]['id_operacion']
                                                        ,'orden' => $flujo_rol_array[$j]['orden']
                                                    ));
                    } 
                }
            }

            $aprobacion = DB::table('almacen.alm_req')
            ->leftJoin('administracion.adm_documentos_aprob', 'adm_documentos_aprob.codigo_doc', '=', 'alm_req.codigo')
            ->leftJoin('administracion.adm_tp_docum', 'adm_tp_docum.id_tp_documento', '=', 'adm_documentos_aprob.id_tp_documento')
            ->leftJoin('administracion.adm_aprobacion', 'adm_aprobacion.id_doc_aprob', '=', 'adm_documentos_aprob.id_doc_aprob')
            ->select( 
                'alm_req.id_requerimiento', 
               DB::raw('(COUNT(administracion.adm_aprobacion.id_aprobacion)+1) as aprobacion')
                  )
                ->where([
                ['adm_tp_docum.abreviatura', '=', 'RQ'], // tipo de documento
                ['adm_aprobacion.id_vobo', '=', 1]  // aprobados
                ])
                ->groupBy( 'alm_req.id_requerimiento')
                ->get();

                if(sizeof($aprobacion)>0){
                foreach($aprobacion as $data){ 
                    $aprobacion_array[]=[
                    'id_requerimiento'=> $data->id_requerimiento,
                    'aprobacion'=> $data->aprobacion
                     ];      
                };
                }else{
                    $aprobacion_array=[];
                }

                //requerimientos pendientes para el usuario actual
                $id_req_pendientes_array = Array();
                for($i=0; $i < sizeof($new_req_array); $i++){
                    // $id_req_pendientes_array=$new_req_array[$i]['orden'];
                    for($j=0; $j < sizeof($aprobacion_array); $j++){
                        if( $new_req_array[$i]['orden'] ==  $aprobacion_array[$j]['aprobacion'] && $new_req_array[$i]['id_requerimiento'] ==  $aprobacion_array[$j]['id_requerimiento']){
                              array_push($id_req_pendientes_array, $aprobacion_array[$j]['id_requerimiento']);
                        } 
                    }
                }

             if(sizeof($req_nuevos)>0){
                for($j=0; $j < sizeof($req_nuevos); $j++){
                array_push($id_req_pendientes_array, $req_nuevos[$j]->id_requerimiento);
                }
             }   

            // imprimir requerimientos pendientes
            $req_pendientes = DB::table('almacen.alm_req')
            ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
            ->leftJoin('proyectos.proy_proyecto', 'alm_req.id_proyecto', '=', 'proy_proyecto.id_proyecto')

            ->select(  
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.concepto',
                 'alm_req.id_grupo',
                'adm_grupo.descripcion as grupo_descripcion',
                'alm_req.fecha_requerimiento',
                'alm_req.id_tipo_requerimiento',
                'alm_req.id_usuario',
                'sis_usua.usuario',
                'alm_req.id_rol',
                'rrhh_rol.id_rol_concepto',
                'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
                'alm_req.id_area',
                'adm_area.descripcion as area_descripcion',
                'alm_req.id_proyecto',
                'alm_req.fecha_registro',
                'alm_req.estado',
                'adm_prioridad.id_prioridad',
                DB::raw("(CASE WHEN almacen.alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
                )
                ->whereIn('alm_req.id_requerimiento', $id_req_pendientes_array)
                ->orderBy('alm_req.id_requerimiento', 'asc')
            ->get();
            return response()->json($req_pendientes);
    }

    
    public function mostrar_documentos_aprobados($id_usuario,$id_rol){
        $req = DB::table('almacen.alm_req')
        ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
        ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
        ->leftJoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.id_estado_doc')
        ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
        ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
        ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
        ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
        ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
        ->leftJoin('proyectos.proy_proyecto', 'alm_req.id_proyecto', '=', 'proy_proyecto.id_proyecto')
        // ->leftJoin('adm_operacion', 'alm_req.id_prioridad', '=', 'adm_operacion.id_prioridad')
         ->leftJoin('administracion.adm_operacion', function ($join) {
            $join->on('almacen.alm_req.id_prioridad', '=', 'adm_operacion.id_prioridad')
            ->on('almacen.alm_req.id_grupo', '=', 'adm_operacion.id_grupo');
        })
        ->select(  
            'alm_req.id_requerimiento',
            'alm_req.codigo',
            'alm_req.id_grupo',
            'alm_req.id_prioridad',
            'adm_operacion.id_operacion'
    
               )
            ->where([
                ['alm_req.estado', '=', 1],
                ['adm_estado_doc.estado_doc', '=', "Aprobado"]
                ])
            ->orderBy('alm_req.id_requerimiento', 'asc')
        ->get();
        if(sizeof($req)>0){
            foreach($req as $data){ 
                $req_array[]=[
                    'id_requerimiento'=> $data->id_requerimiento,
            'codigo'=> $data->codigo,
            'id_grupo'=> $data->id_grupo,
            'id_operacion'=> $data->id_operacion,
            'id_prioridad'=> $data->id_prioridad
             ];      
            };
        }else{
            $req_array=[];
        }

        $flujo_rol = DB::table('administracion.adm_flujo')
         ->select(  
            'adm_flujo.id_flujo',
            'adm_flujo.id_operacion',
            'adm_flujo.id_rol',
            'adm_flujo.nombre',
            'adm_flujo.orden',
            'adm_flujo.estado'
               )
            ->where([
            ['adm_flujo.estado', '=', 1],
            ['adm_flujo.id_rol', '=', $id_rol]
             ])
        ->orderBy('adm_flujo.orden', 'asc')
        ->get();
        foreach($flujo_rol as $data){ 
            $flujo_rol_array[]=[
            'id_flujo'=> $data->id_flujo,
            'id_operacion'=> $data->id_operacion,
            'id_rol'=> $data->id_rol,
            'orden'=> $data->orden
             ];      
        };
        
        //// $new_req_array = $req_array;
        $new_req_array = Array();
         for($i=0;$i<sizeof($req_array);$i++){
            for($j=0;$j<sizeof($flujo_rol_array);$j++){
                if( $req_array[$i]['id_operacion'] ==  $flujo_rol_array[$j]['id_operacion']){
                    //  $new_req_array[$i]['id_requerimiento']= $req_array[$i]['id_requerimiento'];
                    //  $new_req_array[$i]['orden']= $flujo_rol_array[$j]['orden'];
                    array_push($new_req_array,array(
                                                    'id_requerimiento' => $req_array[$i]['id_requerimiento']
                                                    ,'codigo' => $req_array[$i]['codigo']
                                                    ,'id_operacion' => $flujo_rol_array[$j]['id_operacion']
                                                    ,'orden' => $flujo_rol_array[$j]['orden']
                                                ));
                } 
            }
        }

        $aprobacion = DB::table('almacen.alm_req')
        ->leftJoin('administracion.adm_documentos_aprob', 'adm_documentos_aprob.codigo_doc', '=', 'alm_req.codigo')
        ->leftJoin('administracion.adm_tp_docum', 'adm_tp_docum.id_tp_documento', '=', 'adm_documentos_aprob.id_tp_documento')
        ->leftJoin('administracion.adm_aprobacion', 'adm_aprobacion.id_doc_aprob', '=', 'adm_documentos_aprob.id_doc_aprob')
        ->select( 
            'alm_req.id_requerimiento', 
           DB::raw('(COUNT(administracion.adm_aprobacion.id_aprobacion)) as aprobacion')
              )
            ->where([
            ['adm_tp_docum.abreviatura', '=', 'RQ'], // tipo de documento
            ['adm_aprobacion.id_vobo', '=', 1],  // aprobados
            ['adm_aprobacion.id_usuario', '=', $id_usuario]  // id_usuario
            ])
            ->groupBy( 'alm_req.id_requerimiento')
            ->get();

            if(sizeof($aprobacion)>0){
            foreach($aprobacion as $data){ 
                $aprobacion_array[]=[
                'id_requerimiento'=> $data->id_requerimiento,
                'aprobacion'=> $data->aprobacion
                 ];      
            };
            }else{
               $aprobacion_array=[]; 
            }   

            //requerimientos aprobados para el usuario actual
            $id_req_pendientes_array = Array();
            for($i=0; $i < sizeof($new_req_array); $i++){
                // $id_req_pendientes_array=$new_req_array[$i]['orden'];
                for($j=0; $j < sizeof($aprobacion_array); $j++){
                    if( $new_req_array[$i]['id_requerimiento'] ==  $aprobacion_array[$j]['id_requerimiento']){
                          array_push($id_req_pendientes_array, $aprobacion_array[$j]['id_requerimiento']);
                    } 
                }
            }

        //// imprimir requerimientos aprobados
        $req_aprobados = DB::table('almacen.alm_req')
        ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
        ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
        ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
        ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
        ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
        ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
        ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
        ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
        ->leftJoin('proyectos.proy_proyecto', 'alm_req.id_proyecto', '=', 'proy_proyecto.id_proyecto')

        ->select(  
            'alm_req.id_requerimiento',
            'alm_req.codigo',
            'alm_req.concepto',
            'alm_req.id_grupo',
            'adm_grupo.descripcion as grupo_descripcion',
            'alm_req.fecha_requerimiento',
            'alm_req.id_tipo_requerimiento',
            'alm_req.id_usuario',
            'sis_usua.usuario',
            'alm_req.id_rol',
            'rrhh_rol.id_rol_concepto',
            'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
            'alm_req.id_area',
            'adm_area.descripcion as area_descripcion',
            'alm_req.id_proyecto',
            'alm_req.fecha_registro',
            'alm_req.estado',
            'adm_prioridad.id_prioridad',
            DB::raw("(CASE WHEN almacen.alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->whereIn('alm_req.id_requerimiento', $id_req_pendientes_array)
            ->orderBy('alm_req.id_requerimiento', 'asc')
        ->get();
        return response()->json($req_aprobados);
     }


    public function mostrar_documentos_observados($id_usuario,$id_rol){
        $req = DB::table('almacen.alm_req')
        ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
        ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
        ->leftJoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.id_estado_doc')
        ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
        ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
        ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
        ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
        ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
        ->leftJoin('proyectos.proy_proyecto', 'alm_req.id_proyecto', '=', 'proy_proyecto.id_proyecto')
          ->leftJoin('administracion.adm_operacion', function ($join) {
            $join->on('almacen.alm_req.id_prioridad', '=', 'adm_operacion.id_prioridad')
            ->on('almacen.alm_req.id_grupo', '=', 'adm_operacion.id_grupo');
        })
        ->select(  
            'alm_req.id_requerimiento',
            'alm_req.codigo',
            'alm_req.id_grupo',
            'alm_req.id_prioridad',
            'adm_operacion.id_operacion'
    
               )
            ->where([
                ['alm_req.estado', '=', 1],
                ['adm_estado_doc.estado_doc', '=', "Elaborado"]
                ])
            ->orderBy('alm_req.id_requerimiento', 'asc')
        ->get();
        if(sizeof($req)>0){
            foreach($req as $data){ 
                $req_array[]=[
                    'id_requerimiento'=> $data->id_requerimiento,
            'codigo'=> $data->codigo,
            'id_grupo'=> $data->id_grupo,
            'id_operacion'=> $data->id_operacion,
            'id_prioridad'=> $data->id_prioridad
             ];      
            };
        }else{
            $req_array=[];
        }

        $flujo_rol = DB::table('administracion.adm_flujo')
         ->select(  
            'adm_flujo.id_flujo',
            'adm_flujo.id_operacion',
            'adm_flujo.id_rol',
            'adm_flujo.nombre',
            'adm_flujo.orden',
            'adm_flujo.estado'
               )
            ->where([
            ['adm_flujo.estado', '=', 1],
            ['adm_flujo.id_rol', '=', $id_rol]
             ])
        ->orderBy('adm_flujo.orden', 'asc')
        ->get();
        foreach($flujo_rol as $data){ 
            $flujo_rol_array[]=[
            'id_flujo'=> $data->id_flujo,
            'id_operacion'=> $data->id_operacion,
            'id_rol'=> $data->id_rol,
            'orden'=> $data->orden
             ];      
        };
        
        //// $new_req_array = $req_array;
        $new_req_array = Array();
         for($i=0;$i<sizeof($req_array);$i++){
            for($j=0;$j<sizeof($flujo_rol_array);$j++){
                if( $req_array[$i]['id_operacion'] ==  $flujo_rol_array[$j]['id_operacion']){
                    array_push($new_req_array,array(
                                                    'id_requerimiento' => $req_array[$i]['id_requerimiento']
                                                    ,'codigo' => $req_array[$i]['codigo']
                                                    ,'id_operacion' => $flujo_rol_array[$j]['id_operacion']
                                                    ,'orden' => $flujo_rol_array[$j]['orden']
                                                ));
                } 
            }
        }

        $observados = DB::table('almacen.alm_req')
        ->leftJoin('administracion.adm_documentos_aprob', 'adm_documentos_aprob.codigo_doc', '=', 'alm_req.codigo')
        ->leftJoin('administracion.adm_tp_docum', 'adm_tp_docum.id_tp_documento', '=', 'adm_documentos_aprob.id_tp_documento')
        ->leftJoin('administracion.adm_aprobacion', 'adm_aprobacion.id_doc_aprob', '=', 'adm_documentos_aprob.id_doc_aprob')
        ->select( 
            'alm_req.id_requerimiento', 
           DB::raw('(COUNT(administracion.adm_aprobacion.id_aprobacion)) as observados')
              )
            ->where([
            ['adm_tp_docum.abreviatura', '=', 'RQ'], // tipo de documento
            ['adm_aprobacion.id_vobo', '=', 3],  // observados
            ['adm_aprobacion.id_usuario', '=', $id_usuario]  // id_usuario
            ])
            ->groupBy( 'alm_req.id_requerimiento')
            ->get();

            if(sizeof($observados)>0){
                foreach($observados as $data){ 
                    $observados_array[]=[
                    'id_requerimiento'=> $data->id_requerimiento,
                    'observados'=> $data->observados
                     ];      
                };
            }else{
                $observados_array=[];
            }

 

            //requerimientos observados para el usuario actual
            $id_req_observados_array = Array();
            for($i=0; $i < sizeof($new_req_array); $i++){
                 for($j=0; $j < sizeof($observados_array); $j++){
                    if( $new_req_array[$i]['id_requerimiento'] ==  $observados_array[$j]['id_requerimiento']){
                          array_push($id_req_observados_array, $observados_array[$j]['id_requerimiento']);
                    } 
                }
            }

        //// imprimir requerimientos observados
        $req_observados = DB::table('almacen.alm_req')
        ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
        ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
        ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
        ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
        ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
        ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
        ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
        ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
        ->leftJoin('proyectos.proy_proyecto', 'alm_req.id_proyecto', '=', 'proy_proyecto.id_proyecto')

        ->select(  
            'alm_req.id_requerimiento',
            'alm_req.codigo',
            'alm_req.concepto',
             'alm_req.id_grupo',
            'adm_grupo.descripcion as grupo_descripcion',
            'alm_req.fecha_requerimiento',
            'alm_req.id_tipo_requerimiento',
            'alm_req.id_usuario',
            'sis_usua.usuario',
            'alm_req.id_rol',
            'rrhh_rol.id_rol_concepto',
            'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
            'alm_req.id_area',
            'adm_area.descripcion as area_descripcion',
            'alm_req.id_proyecto',
            'alm_req.fecha_registro',
            'alm_req.estado',
            'adm_prioridad.id_prioridad',
            DB::raw("(CASE WHEN almacen.alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->whereIn('alm_req.id_requerimiento', $id_req_observados_array)
            ->orderBy('alm_req.id_requerimiento', 'asc')
        ->get();
        return response()->json($req_observados);
    }

    public function mostrar_documentos_denegados($id_usuario,$id_rol){
        $req = DB::table('almacen.alm_req')
        ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
        ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
        ->leftJoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.id_estado_doc')
        ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
        ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
        ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
        ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
        ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
        ->leftJoin('proyectos.proy_proyecto', 'alm_req.id_proyecto', '=', 'proy_proyecto.id_proyecto')
          ->leftJoin('administracion.adm_operacion', function ($join) {
            $join->on('almacen.alm_req.id_prioridad', '=', 'adm_operacion.id_prioridad')
            ->on('almacen.alm_req.id_grupo', '=', 'adm_operacion.id_grupo');
        })
        ->select(  
            'alm_req.id_requerimiento',
            'alm_req.codigo',
            'alm_req.id_grupo',
            'alm_req.id_prioridad',
            'adm_operacion.id_operacion'
    
               )
            ->where([
                ['alm_req.estado', '=', 1],
                ['adm_estado_doc.estado_doc', '=', "Elaborado"]
                ])
            ->orderBy('alm_req.id_requerimiento', 'asc')
        ->get();
        if(sizeof($req)>0){
            foreach($req as $data){ 
                $req_array[]=[
                    'id_requerimiento'=> $data->id_requerimiento,
            'codigo'=> $data->codigo,
            'id_grupo'=> $data->id_grupo,
            'id_operacion'=> $data->id_operacion,
            'id_prioridad'=> $data->id_prioridad
             ];      
            };
        }else{
            $req_array=[];
        }

        $flujo_rol = DB::table('administracion.adm_flujo')
         ->select(  
            'adm_flujo.id_flujo',
            'adm_flujo.id_operacion',
            'adm_flujo.id_rol',
            'adm_flujo.nombre',
            'adm_flujo.orden',
            'adm_flujo.estado'
               )
            ->where([
            ['adm_flujo.estado', '=', 1],
            ['adm_flujo.id_rol', '=', $id_rol]
             ])
        ->orderBy('adm_flujo.orden', 'asc')
        ->get();
        foreach($flujo_rol as $data){ 
            $flujo_rol_array[]=[
            'id_flujo'=> $data->id_flujo,
            'id_operacion'=> $data->id_operacion,
            'id_rol'=> $data->id_rol,
            'orden'=> $data->orden
             ];      
        };
        
        //// $new_req_array = $req_array;
        $new_req_array = Array();
         for($i=0;$i<sizeof($req_array);$i++){
            for($j=0;$j<sizeof($flujo_rol_array);$j++){
                if( $req_array[$i]['id_operacion'] ==  $flujo_rol_array[$j]['id_operacion']){
                    array_push($new_req_array,array(
                                                    'id_requerimiento' => $req_array[$i]['id_requerimiento']
                                                    ,'codigo' => $req_array[$i]['codigo']
                                                    ,'id_operacion' => $flujo_rol_array[$j]['id_operacion']
                                                    ,'orden' => $flujo_rol_array[$j]['orden']
                                                ));
                } 
            }
        }

        $denegados = DB::table('almacen.alm_req')
        ->leftJoin('administracion.adm_documentos_aprob', 'adm_documentos_aprob.codigo_doc', '=', 'alm_req.codigo')
        ->leftJoin('administracion.adm_tp_docum', 'adm_tp_docum.id_tp_documento', '=', 'adm_documentos_aprob.id_tp_documento')
        ->leftJoin('administracion.adm_aprobacion', 'adm_aprobacion.id_doc_aprob', '=', 'adm_documentos_aprob.id_doc_aprob')
        ->select( 
            'alm_req.id_requerimiento', 
           DB::raw('(COUNT(administracion.adm_aprobacion.id_aprobacion)) as denegados')
              )
            ->where([
            ['adm_tp_docum.abreviatura', '=', 'RQ'], // tipo de documento
            ['adm_aprobacion.id_vobo', '=', 2],  // denegados
            ['adm_aprobacion.id_usuario', '=', $id_usuario]  // id_usuario
            ])
            ->groupBy( 'alm_req.id_requerimiento')
            ->get();

            if(sizeof($denegados)>0){
            foreach($denegados as $data){ 
                $denegados_array[]=[
                'id_requerimiento'=> $data->id_requerimiento,
                'denegados'=> $data->denegados
                 ];      
            };
            }else{
                $denegados_array=[];
            }
 

            //requerimientos denegados para el usuario actual
            $id_req_observados_array = Array();
            for($i=0; $i < sizeof($new_req_array); $i++){
                 for($j=0; $j < sizeof($denegados_array); $j++){
                    if( $new_req_array[$i]['id_requerimiento'] ==  $denegados_array[$j]['id_requerimiento']){
                          array_push($id_req_observados_array, $denegados_array[$j]['id_requerimiento']);
                    } 
                }
            }

        //// imprimir requerimientos denegados
        $req_observados = DB::table('almacen.alm_req')
        ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
        ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
        ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
        ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
        ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
        ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
        ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
        ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
        ->leftJoin('proyectos.proy_proyecto', 'alm_req.id_proyecto', '=', 'proy_proyecto.id_proyecto')

        ->select(  
            'alm_req.id_requerimiento',
            'alm_req.codigo',
            'alm_req.concepto',
             'alm_req.id_grupo',
            'adm_grupo.descripcion as grupo_descripcion',
            'alm_req.fecha_requerimiento',
            'alm_req.id_tipo_requerimiento',
            'alm_req.id_usuario',
            'sis_usua.usuario',
            'alm_req.id_rol',
            'rrhh_rol.id_rol_concepto',
            'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
            'alm_req.id_area',
            'adm_area.descripcion as area_descripcion',
            'alm_req.id_proyecto',
            'alm_req.fecha_registro',
            'alm_req.estado',
            'adm_prioridad.id_prioridad',
            DB::raw("(CASE WHEN almacen.alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->whereIn('alm_req.id_requerimiento', $id_req_observados_array)
            ->orderBy('alm_req.id_requerimiento', 'asc')
        ->get();
        return response()->json($req_observados);
       return response()->json($req_denegados);
    }


     public function aprobacion_documento_accion_aprobar(Request $request){

       $action          = $request->action;
       $id_documento    = $request->id_documento;
       $codigo          = $request->codigo;
       $tipo_documento  = $request->tipo_documento;
       $id_usuario      = $request->id_usuario;
       $id_rol          = $request->id_rol;
       $id_area_usuario = $request->id_area_usuario;
       $id_prioridad    = $request->id_prioridad;
       

                   
       $id_tipo_documento = DB::table('administrcion.adm_tp_docum')
       ->select(  
       'adm_tp_docum.id_tp_documento'
           )
        ->where([
           ['adm_tp_docum.abreviatura', '=', $tipo_documento],      
            ['adm_tp_docum.estado', '=', 1]          
          ])
        ->get();

       if($tipo_documento='RQ'){
            //si id_requerimiento esta dentro de adm_documentos_aprob
            $req_in_adm_doc_aprob = DB::table('almacen.alm_req')
            ->leftJoin('administracion.adm_documentos_aprob', 'alm_req.codigo', '=', 'adm_documentos_aprob.codigo_doc')
            ->leftJoin('administracion.adm_operacion', function ($join) {
                $join->on('almacen.alm_req.id_prioridad', '=', 'adm_operacion.id_prioridad')
                ->on('almacen.alm_req.id_grupo', '=', 'adm_operacion.id_grupo');
            })
            ->select(  
                'alm_req.id_requerimiento'
                ,'alm_req.codigo'
                ,'alm_req.id_grupo'
                ,'alm_req.id_area'
                ,'adm_documentos_aprob.id_doc_aprob'
                ,'adm_operacion.id_operacion'
                )
                ->where([
                    ['alm_req.id_requerimiento', '=', $id_documento]   // id_flujo = 10 (jefe de area)       
                     ,['alm_req.estado', '=', 1]
                    ])
                ->whereRaw("alm_req.codigo IN (SELECT codigo_doc FROM public.adm_documentos_aprob)")
             ->get();

            //si id_requerimiento es nuevo
            $req_nuevo = DB::table('almacen.alm_req')
            ->leftJoin('adm_documentos_aprob', 'alm_req.codigo', '=', 'adm_documentos_aprob.codigo_doc')
            ->leftJoin('adm_operacion', function ($join) {
                $join->on('alm_req.id_prioridad', '=', 'adm_operacion.id_prioridad')
                ->on('alm_req.id_grupo', '=', 'adm_operacion.id_grupo');
            })
            ->select(  
                'alm_req.id_requerimiento'
                ,'alm_req.codigo'
                ,'alm_req.id_grupo'
                ,'alm_req.id_area'
                ,'adm_documentos_aprob.id_doc_aprob'
                ,'adm_operacion.id_operacion'
                )
                ->where([
                    ['alm_req.id_requerimiento', '=', $id_documento]   // id_flujo = 10 (jefe de area)       
                     ,['alm_req.estado', '=', 1]
                    ])
                ->whereRaw("alm_req.codigo NOT IN (SELECT codigo_doc FROM public.adm_documentos_aprob)")
             ->get();
             



        if(sizeof($req_in_adm_doc_aprob)>0){ // si existen req_in_adm_doc_aprob => esta en tabal de aprobacion 
            $flujo = DB::table('administracion.adm_flujo')
            ->select(  
            'adm_flujo.id_flujo',
            'adm_flujo.id_operacion',
            'adm_flujo.id_rol',
            'adm_flujo.nombre',
            'adm_flujo.orden'
              )
             ->where([
                ['adm_flujo.id_operacion', '=', $req_in_adm_doc_aprob[0]->id_operacion],      
                ['adm_flujo.id_rol', '=', $id_rol],      
                ['adm_flujo.estado', '=', 1]          
               ])
            ->orderBy('adm_flujo.orden', 'asc')
            ->get();
            $adm_aprobacion = DB::table('administracion.adm_aprobacion')->insertGetId(
                [
                    'id_flujo'      => $flujo[0]->id_flujo,
                    'id_doc_aprob'  => $req_in_adm_doc_aprob[0]->id_doc_aprob,
                    'id_vobo'       => 1, // 1 => aprobado 
                    'id_usuario'    => $id_usuario,
                    'id_area'       => $id_area_usuario,
                    'fecha_vobo'    => date('Y-m-d H:i:s')
                    // 'detalle_observacion'  => $request->observacion 
                ],
                'id_aprobacion'
            );
        }else{ // es un requerimiento nuevo que debe ser aprobado por "jefe de area"
              //
              $flujo = DB::table('administracion.adm_flujo')
              ->select(  
              'adm_flujo.id_flujo',
              'adm_flujo.id_operacion',
              'adm_flujo.id_rol',
              'adm_flujo.nombre',
              'adm_flujo.orden'
                )
               ->where([
                  ['adm_flujo.id_operacion', '=', $req_nuevo[0]->id_operacion],      
                  ['adm_flujo.id_rol', '=', $id_rol],      
                  ['adm_flujo.estado', '=', 1]          
                 ])
              ->orderBy('adm_flujo.orden', 'asc')
              ->get();
            $adm_documentos_aprob = DB::table('administracion.adm_documentos_aprob')->insertGetId(
                [
                    'id_tp_documento'   => $id_tipo_documento[0]->id_documento,
                    'codigo_doc'        => $req_nuevo[0]->codigo 
                 ],
                'id_doc_aprob'
            );
            if( $adm_documentos_aprob > 0){
                $adm_aprobacion = DB::table('administracion.adm_aprobacion')->insertGetId(
                    [
                        'id_flujo'      => $flujo[0]->id_flujo,
                        'id_doc_aprob'  => $adm_documentos_aprob,
                        'id_vobo'       => 1, // 1 => aprobado 
                        'id_usuario'    => $id_usuario,
                        'id_area'       => $id_area_usuario,
                        'fecha_vobo'    => date('Y-m-d H:i:s')
                        // 'detalle_observacion'  => $request->observacion 
                    ],
                    'id_aprobacion'
                ); 
            }
        }
            
        } //--tipo_documento



    //    // obteniendo el id del documento
    //    $adm_tp_docum = DB::table('adm_tp_docum')
    //     ->select(  
    //         'adm_tp_docum.id_documento'
    //       )
    //     ->where([
    //        ['adm_tp_docum.abreviatura', '=', $documento],   // id_flujo = 10 (jefe de area)       
    //        ['adm_tp_docum.estado', '=', 1]   // id_vobo= 2 (denegado)       
    //       ])
    //    ->get();

    //     foreach($adm_tp_docum as $data){ 
    //     $id_documento[]=[
    //         'id_documento'=> $data->id_documento
    //      ];      
    //     };

    //     if($id_documento[0]['id_documento'] >0){
    //     // con el tipo de documento verificar la operacion (adm_operacion) para definir dentro del flujo de (adm_flujo) continuará 
    //     $operacion = DB::table('adm_operacion')
    //     ->select(  
    //     'adm_operacion.id_operacion'
    //       )
    //      ->where([
    //         ['adm_operacion.id_documento', '=', $id_documento[0]['id_documento']],   // id_flujo = 10 (jefe de area)       
    //         ['adm_operacion.id_prioridad', '=', $id_prioridad],       
    //         ['adm_operacion.estado', '=', 1]   // id_vobo= 2 (denegado)       
    //        ])
    //     ->orderBy('adm_operacion.id_operacion', 'asc')
    //     ->get();
    //     }

    //     foreach($operacion as $data){ 
    //         $id_operacion_actual[]=[
    //             'id_operacion'=> $data->id_operacion
    //          ];      
    //         };

    //     if($id_operacion_actual[0]['id_operacion'] >0){

    //     $flujo = DB::table('adm_flujo')
    //     ->select(  
    //     'adm_flujo.id_flujo',
    //     'adm_flujo.id_operacion',
    //     'adm_flujo.id_rol',
    //     'adm_flujo.nombre',
    //     'adm_flujo.orden'
    //       )
    //      ->where([
    //         ['adm_flujo.id_operacion', '=', $id_operacion_actual[0]['id_operacion']],      
    //         ['adm_flujo.estado', '=', 1]          
    //        ])
    //     ->orderBy('adm_flujo.orden', 'asc')
    //     ->get();
    //     }

    //     foreach($flujo as $data){ 
    //         $flujo_array[]=[
    //             'id_flujo'=> $data->id_flujo,
    //             'id_operacion'=> $data->id_operacion,
    //             'id_rol'=> $data->id_rol,
    //             'nombre'=> $data->nombre,
    //             'orden'=> $data->orden
    //          ];      
    //         };

    //         // buscar si existe el codigo dentro de la tabla "adm_documentos_aprob" 
    //         $doc_aprob = DB::table('adm_documentos_aprob')
    //         ->select(  
    //         'adm_documentos_aprob.id_doc_aprob'
    //           )
    //          ->where([
    //             ['adm_documentos_aprob.id_tp_documento', '=', $id_documento[0]['id_documento']],      
    //             ['adm_documentos_aprob.codigo_doc', '=', $codigo]          
    //            ])
    //         ->orderBy('adm_documentos_aprob.id_doc_aprob', 'asc')
    //         ->get();

    //         if(sizeof($doc_aprob) >0){ // si existe id_doc_aprob en tabla
    //             foreach($doc_aprob as $data){ 
    //                 $doc_aprob_array[]=[
    //                     'id_doc_aprob'=> $data->id_doc_aprob
    //                     ];      
    //                 };

    //                 // verificando que existe id_doc_aprob en adm_aprobacion  
    //                 $aprobacion = DB::table('adm_aprobacion')
    //                 ->leftJoin('adm_flujo', 'adm_flujo.id_flujo', '=', 'adm_aprobacion.id_flujo')

    //                 ->select(  
    //                     'adm_aprobacion.id_aprobacion',
    //                     'adm_aprobacion.id_flujo',
    //                     'adm_aprobacion.id_doc_aprob',
    //                     'adm_aprobacion.id_vobo',
    //                     'adm_aprobacion.id_usuario',
    //                     'adm_aprobacion.id_area',
    //                     'adm_aprobacion.fecha_vobo',
    //                     'adm_aprobacion.detalle_observacion',
    //                     'adm_flujo.orden'
    //                   )
    //                  ->where([
    //                     ['adm_aprobacion.id_doc_aprob', '=', $doc_aprob_array[0]['id_doc_aprob']]   
    //                     ])
    //                 ->orderBy('adm_aprobacion.id_aprobacion', 'asc')
    //                 ->get();
    //                 foreach($aprobacion as $data){ 
    //                     $aprobacion_array[]=[
    //                         'id_aprobacion'=> $data->id_aprobacion,
    //                         'id_flujo'=> $data->id_flujo,
    //                         'id_vobo'=> $data->id_vobo,
    //                         'orden'=> $data->orden
    //                          ];      
    //                     };
            
    //         //////////////////////////////////////////////////////////////////////////
    //         // el resultado anterior, si id_doc_aprob  existe en "adm_aprobacion" => debe actualizar tomando el nro_orden vigente y actualizar registro ( el id_flujo, id_vobo, usuario, area, fecha )
    //         $nextIdFlujo=0;
    //         if($aprobacion_array[0]['id_aprobacion']>0){
    //             // recorriendo flujo_array para determinar el id_flujo siguiente al actual
    //             foreach ($flujo_array as $key => $val){
    //                 if ($val['orden'] === $aprobacion_array[0]['orden']+1) {
    //                      $nextIdFlujo = $val['id_flujo'];
    //                 }
    //             }

    //             $adm_aprobacion = DB::table('adm_aprobacion')->where('id_aprobacion', $aprobacion_array[0]['id_aprobacion'])
    //             ->update([
    //                 'id_flujo'            => $nextIdFlujo,
    //                 // 'id_vobo'             => $request->id_vobo,
    //                 'id_usuario'          => $id_usuario,
    //                 'id_area'             => $id_area,
    //                 'fecha_vobo'          => date('Y-m-d H:i:s')
    //                 // 'detalle_observacion' => $request->detalle_observacion
    //             ]);
    //         //////////////////////////////////////////////////////////////////////////
    //         }
    //         }else{ // es nuevo en la tabla de aprobacion // el resultado anterior, si id_doc_aprob  no existe en "adm_aprobacion" => debe crearse iniciando el nro_orden 1,
    //             $adm_documentos_aprob = DB::table('adm_documentos_aprob')->insertGetId(
    //                 [
    //                     'id_tp_documento'  => $id_documento[0]['id_documento'],
    //                     'codigo_doc'       => $codigo
    //                 ],
    //                 'id_doc_aprob'
    //             );
    //             if($adm_documentos_aprob >0){
    //                 $adm_aprobacion = DB::table('adm_aprobacion')->insertGetId(
    //                     [
    //                         'id_flujo'      => $flujo_array[0]['id_flujo'], // nro orden inicial del flujo_array 
    //                         'id_doc_aprob'  => $adm_documentos_aprob,
    //                         'id_vobo'       => 1, // 1 => aprobado 
    //                         'id_usuario'    => $id_usuario,
    //                         'id_area'       => $id_area,
    //                         'fecha_vobo'    => date('Y-m-d H:i:s')
    //                         // 'detalle_observacion'  => $request->observacion 
    //                     ],
    //                     'id_aprobacion'
    //                 );
    //             }
    //         }
 
 
        return response()->json($adm_aprobacion);

     }

    // menu de aprobaciones 
    // public function mostrar_requerimientos_pendientes(){
    //     $alm_req = DB::table('alm_req')
    //     ->join('alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
    //     ->leftJoin('sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
    //     ->leftJoin('rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
    //     ->leftJoin('rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
    //     ->leftJoin('adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
    //     ->leftJoin('proy_proyecto', 'alm_req.id_proyecto', '=', 'proy_proyecto.id_proyecto')
    //     // ->leftJoin('log_det_coti', 'alm_req.id_requerimiento', '=', 'log_det_coti.id_requerimiento')
    //     // ->leftJoin('log_coti', 'log_det_coti.id_cotizacion', '=', 'log_coti.id_cotizacion')
    //     // ->leftJoin('log_ord_compra', 'log_coti.id_cotizacion', '=', 'log_ord_compra.id_cotizacion')
    //     ->select(
    //         'alm_req.id_requerimiento',
    //         'alm_req.codigo',
    //         'alm_req.fecha_requerimiento',
    //         'alm_req.id_tipo_requerimiento',
    //         'alm_tp_req.descripcion AS tipo_req_desc',
    //         'sis_usua.usuario',
    //         'rrhh_rol.id_area',
    //         // 'adm_area.descripcion AS area_desc',
    //         'rrhh_rol.id_rol',
    //         'rrhh_rol.concepto AS rrhh_rol_concepto',
    //         'alm_req.id_proyecto',
    //         // 'proy_proyecto.descripcion AS proy_desc',
    //          'alm_req.concepto AS alm_req_concepto',
    //         // 'log_coti.codigo AS coti_codigo',
    //         // 'log_ord_compra.numero AS orden_codigo',
    //         // 'log_ord_compra.serie AS orden_serie',
    //         'alm_req.fecha_registro',
    //         'alm_req.estado',
    //         DB::raw("(CASE WHEN alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            
    //         )
    //         ->where([
    //            ['alm_req.estado', '=', 1]
    //            ])
    
    //          ->whereIn('adm_documentos_aprob.codigo', $codigo)

    //         ->orderBy('alm_req.id_requerimiento', 'asc')
    //     ->get();
    //     // $data = ["alm_req"=>$alm_req];   

    //     return response()->json(["requerimiento"=>$alm_req]);

    // }
 

    public function cotizacion_fill_input(){
        $log_cdn_pago = DB::table('logistica.log_cdn_pago')
            ->select(
            'log_cdn_pago.id_condicion_pago',
            'log_cdn_pago.descripcion as condicion_pago_desc'
            )
            ->where([
               ['log_cdn_pago.estado', '=', 1]
               ])
            ->orderBy('log_cdn_pago.id_condicion_pago', 'asc')
            ->get();
  
        return response()->json(['log_cdn_pago'=>$log_cdn_pago]);
    }


    // public function crear_nueva_cotizacion(Request $request){
    //     $detalle_grupoCotizacion =$request->detalle_grupoCotizacion;
    //     $grupo_cotizacion =$request->grupo_cotizacion;
    //     $cotizacion  = $request->cotizacion; 

    //     for ($i=0; $i< count($cotizacion); $i++){
    //         if($cotizacion[$i]['id_cotizacion'] ===0){

                        
    //                 // // #################  crear especificacion compra ########################
    //                 $especificacion_compra = DB::table('logistica.log_esp_compra')->insertGetId(
    //                     [
    //                     // 'id_condicion_pago'         => null,
    //                     // 'plazo_entrega'             => null,
    //                     // 'fecha_entrega'             => null,
    //                     // 'lugar_entrega'             => null,
    //                     // 'detalle_envio'             => null,
    //                     // 'observacion'               => null,
    //                     // 'forma_pago_credito'        => null,
    //                     'estado'                    => 1

    //                     ],
    //                     'id_especificacion_compra'
    //                 );
    //                 // // ######################################################################


    //                // #################  crear cotizacion ########################
    //                $mes = date('m',strtotime("now"));
    //                $anio = date('y',strtotime("now"));
    //                $num = DB::table('logistica.log_cotizacion')
    //                ->count();
    //                $correlativo = $this->leftZero(4,($num+1));
    //                 $codigo = "CO-{$anio}{$mes}-{$correlativo}";

    //                $log_cotizacion = DB::table('logistica.log_cotizacion')->insertGetId(
    //                    [
    //                     'codigo_cotizacion'             => $codigo,
    //                 //    'id_empresa'                    => $cotizacion[$i]['empresa'],
    //                 //    'id_proveedor'                  => $cotizacion[$i]['proveedor'],
    //                 //    'codigo_proveedor'              => '',
    //                    'id_especificacion_compra'      => $especificacion_compra,
    //                 //    'estado_envio'                  => null,
    //                 //    'email_proveedor'               => $cotizacion[$i]['proveedor']['contacto']['email'],
    //                    'estado'                        => 1
    //                    ],
    //                    'id_cotizacion'
    //                );
    //            // ######################################################################

    //            // ##########################  valorizacion ############################################
    //            for ($j=0; $j< count($cotizacion[$i]['items']); $j++){
    //            $log_valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')->insertGetId(
    //                [
    //                'id_cotizacion'             => $log_cotizacion,
    //                'id_detalle_requerimiento'  => $cotizacion[$i]['items'][$j]['id_detalle_requerimiento'],
    //                'estado'                    => 1


    //                    ],
    //                    'id_valorizacion_cotizacion'
    //                );
    //            }
    //            // ######################################################################
    //         }
    //     }




    //     if(count($cotizacion)>0){ // si el tamaño de array es mayor a cero
    //         if($grupo_cotizacion['id_grupo_cotizacion'] ===0 || $grupo_cotizacion['id_grupo_cotizacion'] ===null){ // si NO existe grupo_cotizacion => alterar tabla grupo_cotiazcion,detalle_grupo_cotizacion, especificacion, cotizacion, valorizacion 
    //             for ($i=0; $i< count($cotizacion); $i++){
    //                 if($cotizacion[$i]['id_cotizacion'] ===0){

    //                     $grupo_cotizacion = DB::table('logistica.log_grupo_cotizacion')->insertGetId(
    //                         [
    //                         'codigo_grupo'  => $grupo_cotizacion['codigo_grupo'],
    //                         'id_usuario'    => $grupo_cotizacion['id_usuario'],
    //                         'fecha_inicio'  => $grupo_cotizacion['fecha_inicio'],
    //                         'fecha_fin'     => $grupo_cotizacion['fecha_fin'],
    //                         'estado'        => 1
    //                         ],
    //                         'id_grupo_cotizacion'
    //                     );
    //                     $detalle_grupo_cotizacion = DB::table('logistica.log_detalle_grupo_cotizacion')->insertGetId(
    //                         [
    //                         'id_requerimiento'    => $detalle_grupoCotizacion['id_requerimiento'],
    //                         'id_grupo_cotizacion' => $grupo_cotizacion,
    //                         // 'id_oc_cliente'       => $request->id_oc_cliente
    //                         'id_cotizacion'       => $log_cotizacion,
    //                         'estado'              => 1
    //                         ],
    //                         'id_detalle_grupo_cotizacion'
    //                     );




    //                 // devolver array con la cotizacion actualizada
    //                // $cotizacionUpdated= $this->mostrar_relacion_cotizacion_item_proveedor(0,$detalle_grupoCotizacion['id_requerimiento']);
    //                 //              

    //                 }
    //             }

    //         // }else{// existe grupo de cotizacion => solo alterar tabla cotizacion con su especificac_compra y valorizacion
    //             // for ($i=0; $i< count($cotizacion); $i++){
    //             //     if($cotizacion[$i]['id_cotizacion'] ===0){


    //                 // #################  crear especificacion compra ########################
    //                 // $especificacion_compra = DB::table('logistica.log_esp_compra')->insertGetId(
    //                 //     [
    //                 //     'id_condicion_pago'         => null,
    //                 //     'plazo_entrega'             => null,
    //                 //     'fecha_entrega'             => null,
    //                 //     'lugar_entrega'             => null,
    //                 //     'detalle_envio'             => null,
    //                 //     'observacion'               => null,
    //                 //     'forma_pago_credito'        => null,
    //                 //     'estado'                    => 1
    //                 //     ],
    //                 //     'id_especificacion_compra'
    //                 // );
    //                 // ######################################################################

    //                // #################  crear cotizacion ########################
    //                     // $mes = date('m',strtotime("now"));
    //                     // $anio = date('y',strtotime("now"));
    //                     // $num = DB::table('logistica.log_cotizacion')
    //                     // ->count();
    //                     // $correlativo = $this->leftZero(4,($num+1));
    //                     //  $codigo = "CO-{$anio}{$mes}-{$correlativo}";

    //                     // $log_cotizacion = DB::table('logistica.log_cotizacion')->insertGetId(
    //                     //     [
    //                     //     'id_detalle_grupo_cotizacion'   => $detalle_grupoCotizacion['id_detalle_grupo_cotizacion'],
    //                     //     'codigo_cotizacion'             => $codigo,
    //                     //     'id_empresa'                    => null,
    //                     //     'id_proveedor'                  => $cotizacion[$i]['proveedor']['id_proveedor'],
    //                     //     'codigo_proveedor'              => $cotizacion[$i]['proveedor']['codigo_proveedor'],
    //                     //     'id_especificacion_compra'      => $especificacion_compra,
    //                     //     'estado_envio'                  => null,
    //                     //     'email_proveedor'               => $cotizacion[$i]['proveedor']['contacto']['email'],
    //                     //     'estado'                        => 1
    //                     //     ],
    //                     //     'id_cotizacion'
    //                     // );
    //                 // ######################################################################

    //                 // ##########################  valorizacion ############################################
    //                 // for ($j=0; $j< count($cotizacion[$i]['items']); $j++){
    //                 // $log_valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')->insertGetId(
    //                 //     [
    //                 //     'id_cotizacion'             => $log_cotizacion,
    //                 //     'id_detalle_requerimiento'  => $cotizacion[$i]['items'][$j]['id_detalle_requerimiento'],
    //                 //     'estado'  => 1
     
    //                 //         ],
    //                 //         'id_valorizacion_cotizacion'
    //                 //     );
    //                 // }
    //                 // ######################################################################

    //             // devolver array con la cotizacion actualizada
    //             $cotizacionUpdated= $this->mostrar_relacion_cotizacion_item_proveedor(0,$detalle_grupoCotizacion['id_requerimiento']);
    //             // 
    //                 // }else{ // no existe nuevo item para agregar => no hacer nada
    
    //                 // }
    //             // }


    //         } 
    //     } 

   
   
 
    //     // return response()->json($detalle_grupoCotizacion['id_requerimiento']);
    //     return $cotizacionUpdated;
    // }


    public function eliminar_cotizacion($id_grupo_cotiza){
        // obtener los id_cotizacion de log_detalle_grupo_cotizacion 
        $cotizaciones = DB::table('logistica.log_detalle_grupo_cotizacion')
        ->select(
        'log_detalle_grupo_cotizacion.id_cotizacion',
        'log_valorizacion_cotizacion.id_especificacion_compra'
        )
       ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_cotizacion')
       ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')

        ->where([
           ['log_detalle_grupo_cotizacion.id_grupo_cotizacion', '=', $id_grupo_cotiza]
           ])
         ->get();

         $arr_cotiza = [];
         $arr_esp_compra = [];
        foreach($cotizaciones as $data){ 
            $arr_cotiza[]=$data->id_cotizacion;      
            $arr_esp_compra[]=$data->id_especificacion_compra;      
        };
        // eliminar log_valorizacion_cotizacion
        $valoriza = DB::table('logistica.log_valorizacion_cotizacion')->whereIn('id_cotizacion', $arr_cotiza)->delete();
        // eliminar log_esp_compra
        $esp_compra = DB::table('logistica.log_esp_compra')->whereIn('id_especificacion_compra', $arr_esp_compra)->delete();
        // eliminar log_cotizacion
        $cotiza = DB::table('logistica.log_cotizacion')->whereIn('id_cotizacion', $arr_cotiza)->delete();
        // eliminar log_grupo_cotizacion
        $grupo = DB::table('logistica.log_grupo_cotizacion')->where('id_grupo_cotizacion','=', $id_grupo_cotiza)->delete();
        // eliminar log_detalle_grupo_cotizacion
        $det_grupo = DB::table('logistica.log_detalle_grupo_cotizacion')->whereIn('id_grupo_cotizacion', $id_grupo_cotiza)->delete();


        return response()->json($grupo);

    }


    // public function agrupar_cotizaciones(Request $request){
    //     $cotizacion  = $request->cotizacion;

    //     if(count($cotizacion)>0){

    //         // crear nuevo codigo de grupo cotizacion
    //         $grupo_cotizacion = DB::table('logistica.log_grupo_cotizacion')->insertGetId(
    //             [
    //             'codigo_grupo'  => $grupo_cotizacion['codigo_grupo'],
    //             'id_usuario'    => $grupo_cotizacion['id_usuario'],
    //             'fecha_inicio'  => $grupo_cotizacion['fecha_inicio'],
    //             'fecha_fin'     => $grupo_cotizacion['fecha_fin'],
    //             'estado'        => 1
    //             ],
    //             'id_grupo_cotizacion'
    //         );

    //         // actualiar detalle_grupo_cotizacion con el codigo de grupo cotización
    //         $detalle_grupo_cotizacion = DB::table('logistica.log_detalle_grupo_cotizacion')
    //         ->where('id_cotizacion', '=',  $id_cotizacion)
    //          ->update([
    //             'id_requerimiento'    => $detalle_grupoCotizacion['id_requerimiento'],
    //             'id_grupo_cotizacion' => $grupo_cotizacion
    //                 // 'id_oc_cliente'       => $request->id_oc_cliente
    //             ]);

 
           
           
    //     }
    // }


    public function guardar_cotizacion(Request $request){


        // $detalle_grupo_cotizacion =$request->detalle_grupo_cotizacion;
        $grupo_cotizacion =$request->grupo_cotizacion;
        $cotizaciones  = $request->cotizaciones;
        
        // $id_requerimiento = $detalle_grupo_cotizacion['id_requerimiento'];
        if(count($cotizaciones)>0){
        if($grupo_cotizacion['id_grupo_cotizacion']=='' || $grupo_cotizacion['id_grupo_cotizacion']==null || $grupo_cotizacion['id_grupo_cotizacion']==0){

            $mes = date('m',strtotime("now"));
            $anio = date('y',strtotime("now"));
            $num = DB::table('logistica.log_grupo_cotizacion')
            ->count();
            $correlativo = $this->leftZero(4,($num+1));
            $codigoGrupo = "CC-{$anio}{$mes}-{$correlativo}";

            $grupo_cotizacion = DB::table('logistica.log_grupo_cotizacion')->insertGetId(
                [
                'codigo_grupo'  => $grupo_cotizacion['codigo_grupo'],
                'id_usuario'    => $grupo_cotizacion['id_usuario'],
                'codigo_grupo'  => $codigoGrupo,
                'fecha_inicio'  => $grupo_cotizacion['fecha_inicio'],
                'estado'        => 1
                ],
                'id_grupo_cotizacion'
            );

            for ($i=0; $i< count($cotizaciones); $i++){
                $mes = date('m',strtotime("now"));
                $anio = date('y',strtotime("now"));
                $num = DB::table('logistica.log_cotizacion')
                ->count();
                $correlativo = $this->leftZero(4,($num+1));
                $codigo = "CO-{$anio}{$mes}-{$correlativo}";

                $log_cotizacion = DB::table('logistica.log_cotizacion')->insertGetId(
                    [
                    'codigo_cotizacion'             => $codigo,
                    'id_empresa'                    => $cotizaciones[$i]['empresa']['id_empresa'],
                    'id_proveedor'                  => $cotizaciones[$i]['proveedor']['id_proveedor'],
                    'estado_envio'                  => 0,
                    'email_proveedor'               => $cotizaciones[$i]['proveedor']['contacto']['email'],
                    'detalle'                       => $cotizaciones[$i]['detalle'],
                    'estado'                        => 1
                    ],
                    'id_cotizacion'
                );


                if($grupo_cotizacion > 0 && $log_cotizacion >0){
                    $detalle_grupo_cotizacion = DB::table('logistica.log_detalle_grupo_cotizacion')->insertGetId(
                        [
                        'id_grupo_cotizacion' => $grupo_cotizacion,
                        'id_cotizacion'       => $log_cotizacion,
                        'estado' => 1
                        ],
                        'id_detalle_grupo_cotizacion'
                    );
                }

                for ($j=0; $j< count($cotizaciones[$i]['items']); $j++){
                    $log_valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')->insertGetId(
                    [
                    'id_cotizacion'             => $log_cotizacion,
                    'id_requerimiento'          => $cotizaciones[$i]['items'][$j]['id_requerimiento'],
                    'id_detalle_requerimiento'  => $cotizaciones[$i]['items'][$j]['id_detalle_requerimiento'],
                    'estado'                    => 1,
                        ],
                        'id_valorizacion_cotizacion'
                    );
                }
            }
        }else{
            $status=['status'=>'Houston tenemos un problema... La Data no se pudo guardar','action'=>0];
            return response()->json($status);
            
        }
    }
    $status=['status'=>'Se guardo Correctamente','action'=>1, 'id_grupo_cotizacion'=>$grupo_cotizacion];
    return response()->json($status);
    }

    public function cotizaciones_por_grupo_cotizacion($id_grupo_cotizacion){
        $grupo_cotizacion = DB::table('logistica.log_grupo_cotizacion')
        ->select(
            'log_grupo_cotizacion.*'
        )
        ->where([
            ['log_grupo_cotizacion.estado', '=', 1],
            ['log_grupo_cotizacion.id_grupo_cotizacion', '=', $id_grupo_cotizacion]
            ])
        ->get();

        $detalle_grupo_cotizacion__cotizacion = DB::table('logistica.log_detalle_grupo_cotizacion')
        ->leftJoin('logistica.log_grupo_cotizacion', 'log_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
        ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_cotizacion')
        ->leftJoin('logistica.log_prove','log_prove.id_proveedor','=','log_cotizacion.id_proveedor')
        ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi','sis_identi.id_doc_identidad','=','adm_contri.id_doc_identidad')
        ->leftJoin('administracion.adm_empresa','adm_empresa.id_empresa','=','log_cotizacion.id_empresa')
        ->leftJoin('contabilidad.adm_contri as contri','contri.id_contribuyente','=','adm_empresa.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi as identi','identi.id_doc_identidad','=','contri.id_doc_identidad')

        ->select(
            'log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion AS detalle_grupo_cotizacion_id_detalle_grupo_cotizacion',
            'log_detalle_grupo_cotizacion.id_grupo_cotizacion AS detalle_grupo_cotizacion_id_grupo_cotizacion',
            'log_detalle_grupo_cotizacion.id_oc_cliente AS detalle_grupo_cotizacion_id_oc_cliente',
            'log_detalle_grupo_cotizacion.id_cotizacion AS detalle_grupo_cotizacion_id_cotizacion',
            'log_detalle_grupo_cotizacion.estado AS detalle_grupo_cotizacion_estado',
            'log_cotizacion.id_cotizacion AS cotizacion_id_cotizacion',
            'log_cotizacion.codigo_cotizacion AS cotizacion_codigo_cotizacion',
            'log_cotizacion.id_proveedor AS cotizacion_id_proveedor',
            'log_cotizacion.codigo_proveedor AS cotizacion_codigo_proveedor',
            'log_cotizacion.estado_envio AS cotizacion_estado_envio',
            'log_cotizacion.estado AS cotizacion_estado',
            'log_cotizacion.id_empresa AS cotizacion_id_empresa',
            'log_cotizacion.email_proveedor AS cotizacion_email_proveedor',
            'log_cotizacion.detalle AS cotizacion_detalle',
            'log_cotizacion.id_tp_doc AS cotizacion_id_tp_doc',
            'log_cotizacion.id_condicion_pago AS cotizacion_id_condicion_pago',
            'log_cotizacion.nro_cuenta_principal AS cotizacion_nro_cuenta_principal',
            'log_cotizacion.nro_cuenta_alternativa AS cotizacion_nro_cuenta_alternativa',
            'log_cotizacion.nro_cuenta_detraccion AS cotizacion_nro_cuenta_detraccion',
            'log_cotizacion.condicion_credito_dias AS cotizacion_condicion_credito_dias',
            'adm_contri.razon_social',
            'adm_contri.nro_documento',
            'adm_contri.id_doc_identidad',
            'sis_identi.descripcion as nombre_doc_identidad',
            'contri.razon_social as razon_social_empresa',
            'contri.nro_documento as nro_documento_empresa',
            'contri.id_doc_identidad as id_doc_identidad_empresa',
            'identi.descripcion as nombre_doc_idendidad_empresa'
        )
        ->where([
            ['log_detalle_grupo_cotizacion.estado', '=', 1],
            ['log_detalle_grupo_cotizacion.id_grupo_cotizacion', '=', $id_grupo_cotizacion]
            ])
        ->get();

        foreach($detalle_grupo_cotizacion__cotizacion as $data){ 
            $detalle_grupo_cotizacion[]=[
                'id_detalle_grupo_cotizacion' => $data->detalle_grupo_cotizacion_id_detalle_grupo_cotizacion,
                'id_grupo_cotizacion' => $data->detalle_grupo_cotizacion_id_grupo_cotizacion,
                'id_oc_cliente' => $data->detalle_grupo_cotizacion_id_oc_cliente,
                'id_cotizacion' => $data->detalle_grupo_cotizacion_id_cotizacion,
                'estado' => $data->detalle_grupo_cotizacion_estado
            ];
            $cotizacion[]=[
                'id_cotizacion' => $data->cotizacion_id_cotizacion,
                'codigo_cotizacion' => $data->cotizacion_codigo_cotizacion,
                'id_proveedor' => $data->cotizacion_id_proveedor,
                'codigo_proveedor' => $data->cotizacion_codigo_proveedor,
                'proveedor'=>[
                        "id_proveedor"=>$data->cotizacion_id_proveedor,
                        "razon_social"=>$data->razon_social,
                        "nro_documento"=>$data->nro_documento,
                        "id_doc_identidad"=>$data->id_doc_identidad,
                        "nombre_doc_identidad"=>$data->nombre_doc_identidad,
                        "contacto"=>[
                            "email"=>$data->cotizacion_email_proveedor
                            ]
                        ],
                'estado_envio' => $data->cotizacion_estado_envio,
                'estado' => $data->cotizacion_estado,
                'id_empresa' => $data->cotizacion_id_empresa,
                'empresa'=>[
                    'id_empresa'=> $data->cotizacion_id_empresa,
                    'razon_social'=> $data->razon_social_empresa,
                    'nro_documento'=> $data->nro_documento_empresa,
                    'nombre_doc_identidad'=> $data->nombre_doc_idendidad_empresa
                ],
                'email_proveedor' => $data->cotizacion_email_proveedor,
                'detalle' => $data->cotizacion_detalle,
                'id_tp_doc' => $data->cotizacion_id_tp_doc,
                'id_condicion_pago' => $data->cotizacion_id_condicion_pago,
                'nro_cuenta_principal' => $data->cotizacion_nro_cuenta_principal,
                'nro_cuenta_alternativa' => $data->cotizacion_nro_cuenta_alternativa,
                'nro_cuenta_detraccion' => $data->cotizacion_nro_cuenta_detraccion,
                'condicion_credito_dias' => $data->cotizacion_condicion_credito_dias
            ];
            $id_cotizaciones[]=$data->cotizacion_id_cotizacion;
        }


        $items_valorizaciones = DB::table('logistica.log_valorizacion_cotizacion')
        ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_valorizacion_cotizacion.id_detalle_requerimiento')
        ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
        ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
        ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
        ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
        ->select(
            'log_valorizacion_cotizacion.*',
            'alm_req.id_requerimiento',
            'alm_req.codigo AS codigo_requerimiento',
            'alm_det_req.id_item',
            'alm_item.codigo AS codigo_item',
            'alm_prod.descripcion AS alm_prod_descripcion',
            'log_servi.descripcion AS log_servi_descripcion',
            'alm_det_req.descripcion_adicional',
            'alm_det_req.cantidad',
            'alm_det_req.unidad_medida',
            'alm_det_req.precio_referencial',
            'alm_det_req.partida',
            'alm_det_req.fecha_registro',
            'alm_det_req.lugar_entrega',
            'alm_det_req.obs',
            'alm_det_req.estado'
        )
        ->where([
            ['log_valorizacion_cotizacion.estado', '>=', 1]
            ])
        ->whereIn('log_valorizacion_cotizacion.id_cotizacion',$id_cotizaciones)
        ->get();

        $requerimientoList=[];
        $auxIdReq=[];
        foreach($items_valorizaciones as  $data){
            if(in_array($data->id_requerimiento, $auxIdReq)===false){
                array_push($auxIdReq, $data->id_requerimiento);
                array_push($requerimientoList, 
                ['id_requerimiento'=>$data->id_requerimiento,
                'codigo_requerimiento'=>$data->codigo_requerimiento]);
            }
            $item_valorizacion[]=[
                'id_valorizacion_cotizacion'=> $data->id_valorizacion_cotizacion,
                'id_cotizacion'=> $data->id_cotizacion,
                'id_requerimiento'=> $data->id_requerimiento,
                'codigo_requerimiento'=> $data->codigo_requerimiento,
                'id_detalle_requerimiento'=> $data->id_detalle_requerimiento,
                'id_item'=> $data->id_item,
                'codigo_item'=> $data->codigo_item,
                'alm_prod_descripcion'=> $data->alm_prod_descripcion,
                'log_servi_descripcion'=> $data->log_servi_descripcion,
                'descripcion_adicional'=> $data->descripcion_adicional,
                'cantidad'=> $data->cantidad,
                'unidad_medida'=> $data->unidad_medida,
                'precio_referencial'=> $data->precio_referencial,
                'partida'=> $data->partida,
                'fecha_registro'=> $data->fecha_registro,
                'lugar_entrega'=> $data->lugar_entrega,
                'obs'=> $data->obs,
                'estado'=> $data->estado,
                'valorizacion'=>[
                    'id_valorizacion_cotizacion'=> $data->id_valorizacion_cotizacion,
                    'id_cotizacion'=> $data->id_cotizacion,
                    'id_detalle_requerimiento'=> $data->id_detalle_requerimiento,
                    'id_detalle_oc_cliente'=> $data->id_detalle_oc_cliente,
                    'precio_cotizado'=> $data->precio_cotizado,
                    'cantidad_cotizada'=> $data->cantidad_cotizada,
                    'subtotal'=> $data->subtotal,
                    'flete'=> $data->flete,
                    'porcentaje_descuento'=> $data->porcentaje_descuento,
                    'monto_descuento'=> $data->monto_descuento,
                    'estado'=> $data->estado,
                    'justificacion'=> $data->justificacion,
                    'detalle'=> $data->detalle,
                    'plazo_entrega'=> $data->plazo_entrega,
                    'incluye_igv'=> $data->incluye_igv,
                    'garantia'=> $data->garantia,
                    'lugar_despacho'=> $data->lugar_despacho
                ]


            ];
        }

        $detalle_requerimiento = DB::table('almacen.alm_det_req')
        ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
        ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
        ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
        ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
        ->select(
            'alm_req.id_requerimiento',
            'alm_req.id_tipo_requerimiento',
            'alm_req.codigo AS codigo_requerimiento',
            'alm_det_req.id_item',
            'alm_item.codigo AS codigo_item',
            'alm_prod.descripcion AS alm_prod_descripcion',
            'log_servi.descripcion AS log_servi_descripcion',
            'alm_det_req.descripcion_adicional',
            'alm_det_req.cantidad',
            'alm_det_req.unidad_medida',
            'alm_det_req.precio_referencial',
            'alm_det_req.partida',
            'alm_det_req.fecha_registro',
            'alm_det_req.lugar_entrega',
            'alm_det_req.fecha_entrega',
            'alm_det_req.obs',
            'alm_det_req.estado'
        )
        ->where([
            ['alm_det_req.estado', '=', 1],
            ])
        ->whereIn('alm_req.id_requerimiento',$auxIdReq)
        ->get();

        foreach($detalle_requerimiento as $data){
                $detalle_requerimientoList[]=[
                    'id_requerimiento'=> $data->id_requerimiento,
                    'id_tipo_requerimiento'=> $data->id_tipo_requerimiento,
                    'codigo_requerimiento'=> $data->codigo_requerimiento,
                    'id_item'=> $data->id_item,
                    'codigo_item'=> $data->codigo_item,
                    'descripcion'=> $data->id_tipo_requerimiento ==1?$data->alm_prod_descripcion:($data->id_tipo_requerimiento ==2?$data->log_servi_descripcion:''),
                    'descripcion_adicional'=> $data->descripcion_adicional,
                    'cantidad'=> $data->cantidad,
                    'unidad_medida'=> $data->unidad_medida,
                    'precio_referencial'=> $data->precio_referencial,
                    'partida'=> $data->partida,
                    'fecha_registro'=> $data->fecha_registro,
                    'lugar_entrega'=> $data->lugar_entrega,
                    'fecha_entrega'=> $data->fecha_entrega,
                    'obs'=> $data->obs,
                    'estado'=> $data->estado
                ]; 
        }



        // $requerimiento__detalle_requerimiento =$requerimientoList;

        // for($i=0; $i < sizeof($requerimientoList);$i++){
        //     for($k=0; $k < sizeof($detalle_requerimientoList);$k++){
        //         if($requerimientoList[$i]['id_requerimiento'] == $detalle_requerimientoList[$k]['id_requerimiento']){
        //             $requerimiento__detalle_requerimiento[$i]['detalle_requerimiento'][]=$detalle_requerimientoList[$k];
        //         }
        //     }
        // }

        $cotizacion__valorizacion =$cotizacion;

        for($i=0; $i < sizeof($cotizacion);$i++){
            for($k=0; $k < sizeof($item_valorizacion);$k++){
                if($cotizacion[$i]['id_cotizacion'] == $item_valorizacion[$k]['id_cotizacion']){
                    $cotizacion__valorizacion[$i]['items'][]=$item_valorizacion[$k];
                }
            }
        }

        $cotizacion__valorizacion__grupo_cotizacion = $cotizacion__valorizacion;
        for($i=0; $i < sizeof($cotizacion__valorizacion);$i++){
            for($k=0; $k < sizeof($detalle_grupo_cotizacion);$k++){
                if($cotizacion__valorizacion[$i]['id_cotizacion'] == $detalle_grupo_cotizacion[$k]['id_cotizacion']){
                    $cotizacion__valorizacion__grupo_cotizacion[$i]['detalle_grupo'][]=$detalle_grupo_cotizacion[$k];
                }
            }
        }

        $result=[
            'grupo'=>$grupo_cotizacion,
            'requerimiento'=>$requerimientoList,
            'det_req'=>$detalle_requerimientoList,
            // 'detalle_requerimiento'=>$detalle_requerimiento,
            'cotizacion'=>$cotizacion__valorizacion__grupo_cotizacion
        ];
        return response()->json($result);
    }
    
    
    public function cotizaciones_generadas(){
        $cotizaciones = DB::table('logistica.log_cotizacion')
        ->leftJoin('logistica.log_prove','log_prove.id_proveedor','=','log_cotizacion.id_proveedor')
        ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi','sis_identi.id_doc_identidad','=','adm_contri.id_doc_identidad')
        ->leftJoin('administracion.adm_empresa','adm_empresa.id_empresa','=','log_cotizacion.id_empresa')
        ->leftJoin('contabilidad.adm_contri as contri','contri.id_contribuyente','=','adm_empresa.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi as identi','identi.id_doc_identidad','=','contri.id_doc_identidad')
        ->leftJoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_cotizacion','=','log_cotizacion.id_cotizacion')
        ->leftJoin('almacen.alm_req','alm_req.id_requerimiento','=','log_valorizacion_cotizacion.id_requerimiento')
        ->leftJoin('logistica.log_detalle_grupo_cotizacion','log_detalle_grupo_cotizacion.id_cotizacion','=','log_cotizacion.id_cotizacion')
        ->leftJoin('logistica.log_grupo_cotizacion','log_grupo_cotizacion.id_grupo_cotizacion','=','log_detalle_grupo_cotizacion.id_grupo_cotizacion')
        ->select(
            'log_grupo_cotizacion.id_grupo_cotizacion',
            'log_grupo_cotizacion.codigo_grupo',
            'log_cotizacion.id_cotizacion',
            'log_cotizacion.codigo_cotizacion',
            'log_cotizacion.id_proveedor',
            'log_cotizacion.estado_envio',
            'log_cotizacion.estado',
            'log_cotizacion.id_empresa',

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
            ])
        // ->whereIn('alm_req.id_requerimiento',$auxIdReq)
        ->get();
        
        $cotizacionAux=[];
        $cotizacionList=[];
        $requerimiento__cotizacion=[];

        foreach($cotizaciones as $data){
            $requerimiento__cotizacion[]=[
                'id_cotizacion'=> $data->id_cotizacion,
                'id_requerimiento'=> $data->id_requerimiento,
                'codigo_requerimiento'=>$data->codigo_requerimiento
            ];
            if(in_array($data->id_cotizacion,$cotizacionAux)===false){
                $cotizacionAux[]=$data->id_cotizacion;
                $cotizacionList[]=[
                    'id_grupo_cotizacion'=> $data->id_grupo_cotizacion,
                    'codigo_grupo'=> $data->codigo_grupo,
                    'id_cotizacion'=> $data->id_cotizacion,
                    'codigo_cotizacion'=> $data->codigo_cotizacion,
                    'id_proveedor'=> $data->id_proveedor,
                    'estado_envio'=> $data->estado_envio,
                    'estado'=> $data->estado,
                    'id_empresa'=> $data->id_empresa,
                    'razon_social'=> $data->razon_social,
                    'nro_documento'=> $data->nro_documento,
                    'id_doc_identidad'=> $data->id_doc_identidad,
                    'nombre_doc_identidad'=> $data->nombre_doc_identidad,
                    'razon_social_empresa'=> $data->razon_social_empresa,
                    'nro_documento_empresa'=> $data->nro_documento_empresa,
                    'id_doc_identidad_empresa'=> $data->id_doc_identidad_empresa,
                    'nombre_doc_idendidad_empresa'=> $data->nombre_doc_idendidad_empresa,
                    'cantidad_items'=> $data->cantidad_items
                ];


            }
        }

        $aux=[];
             for($k=0; $k < sizeof($requerimiento__cotizacion);$k++){
            
                    if(in_array($requerimiento__cotizacion[$k]['id_cotizacion'].$requerimiento__cotizacion[$k]['id_requerimiento'], $aux)===false ){

                        $aux[]=$requerimiento__cotizacion[$k]['id_cotizacion'].$requerimiento__cotizacion[$k]['id_requerimiento'];
                      
                        $requerimientos_cotiza[]=$requerimiento__cotizacion[$k];
                

                }
            }



        $aux=[];
        for($i=0; $i < sizeof($cotizacionList);$i++){
            for($k=0; $k < sizeof($requerimientos_cotiza);$k++){
                if($cotizacionList[$i]['id_cotizacion'] == $requerimientos_cotiza[$k]['id_cotizacion']){
                    $cotizacionList[$i]['requerimiento'][]=$requerimientos_cotiza[$k];
                    }
            }
        }

        return response()->json($cotizacionList);
    }


    public function item_cotizacion($id_cotizacion){

        $item_cotizacion = DB::table('logistica.log_cotizacion')
        ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
        ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_valorizacion_cotizacion.id_detalle_requerimiento')
        ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
        ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
        ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
         ->select(
        'alm_det_req.id_detalle_requerimiento',
        'alm_item.codigo',
        'alm_prod.descripcion',
        'alm_und_medida.descripcion AS unidad_medida',
        'alm_det_req.cantidad',
        'alm_det_req.precio_referencial',
        'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
        'log_valorizacion_cotizacion.id_cotizacion',
        'log_valorizacion_cotizacion.id_detalle_requerimiento',
        'log_valorizacion_cotizacion.id_detalle_oc_cliente',
        'log_valorizacion_cotizacion.precio_cotizado',
        'log_valorizacion_cotizacion.cantidad_cotizada',
        'log_valorizacion_cotizacion.subtotal',
        'log_valorizacion_cotizacion.flete',
        'log_valorizacion_cotizacion.porcentaje_descuento',
        'log_valorizacion_cotizacion.monto_descuento',
        'log_valorizacion_cotizacion.subtotal',
        'log_valorizacion_cotizacion.estado'
        )
        ->where([
           ['log_cotizacion.estado', '=', 1],
           ['log_cotizacion.id_cotizacion', '=', $id_cotizacion]
           ])
        ->orderBy('log_cotizacion.id_cotizacion', 'asc')
        ->get();

    return response()->json($item_cotizacion);
    }

    public function actualizar_valorizacion_item(Request $request){
        
        $valorizacion_item =$request->data;
        $count_valorizacion_item = count($valorizacion_item);      

        for($i=0; $i< $count_valorizacion_item; $i++) {

            $data = DB::table('logistica.log_valorizacion_cotizacion')
            ->where('id_cotizacion','=', $valorizacion_item[$i]['id_cotizacion'])
            ->where('id_detalle_requerimiento','=', $valorizacion_item[$i]['id_detalle_requerimiento'])
            ->update([
                'cantidad_cotizada'     => $valorizacion_item[$i]['cantidad'],
                'precio_cotizado'       => $valorizacion_item[$i]['precio'],
                'flete'                 => $valorizacion_item[$i]['flete'],
                'porcentaje_descuento'  => $valorizacion_item[$i]['porcentaje_descuento'],
                'monto_descuento'       => $valorizacion_item[$i]['monto_descuento'],
                'subtotal'              => $valorizacion_item[$i]['subtotal'],
                'estado'                => 1
                ],
                'id_valorizacion_cotizacion'
            );
        }
        return response()->json($data);

    }
    public function actualizar_empresa_cotizacion(Request $request){
        
        $empresa =$request->data;

        $data = DB::table('logistica.log_cotizacion')
        ->where('id_cotizacion','=', $empresa['id_cotizacion'])
        ->update([
            'id_empresa'     => $empresa['id_empresa']

            ],
            'id_cotizacion'
        );
        
        return response()->json($data);

    }
    public function actualizar_especificacion_compra(Request $request){
        
        $especificacion_compra =$request->data;

        $data = DB::table('logistica.log_esp_compra')
        ->where('id_especificacion_compra','=', $especificacion_compra['id_especificacion_compra'])
        ->update([
            'id_condicion_pago' => $especificacion_compra['id_condicion_pago'],
            'plazo_entrega'     => $especificacion_compra['plazo_entrega'],
            'fecha_entrega'     => $especificacion_compra['fecha_entrega'],
            'lugar_entrega'     => $especificacion_compra['lugar_entrega'],
            'detalle_envio'     => $especificacion_compra['detalle_envio'],
            'observacion'       => $especificacion_compra['observacion'],
            'forma_pago_credito'=> $especificacion_compra['forma_pago_credito'],
            'estado'            => $especificacion_compra['estado']

            ],
            'id_especificacion_compra'
        );
        
        return response()->json($data);

    }


    // public function get_cotizacion($id_cotizacion){
    //     $log_cotizacion = DB::table('logistica.log_cotizacion')
    //     ->select(
    //         'alm_req.id_requerimiento',
    //         'alm_req.codigo as codigo_req',
    //         'log_cotizacion.id_cotizacion',
    //         'alm_det_req.id_detalle_requerimiento',
    //         'alm_det_req.cantidad',
    //         'alm_item.codigo',
    //         'alm_prod.descripcion as descripcion_producto',
    //         'alm_prod.descripcion',
    //         'alm_prod.id_unidad_medida',
    //         'alm_und_medida.descripcion AS unidad_medida_descripcion',
    //         'alm_prod.fecha_registro',
    //         'alm_prod.estado',
    //         'log_cotizacion.codigo_cotizacion',
    //         'log_cotizacion.id_proveedor',
    //         'log_cotizacion.codigo_proveedor',
    //         'log_cotizacion.id_empresa',
    //         'contri.razon_social as razon_social_empresa',
    //         'contri.nro_documento as nro_documento_empresa',
    //         'contri.id_doc_identidad as id_doc_identidad_empresa',
    //         'identi.descripcion as nombre_doc_idendidad_empresa',
    //         'adm_contri.razon_social',
    //         'adm_contri.nro_documento',
    //         'adm_contri.id_doc_identidad',
    //         'sis_identi.descripcion as nombre_doc_identidad',
    //         'adm_ctb_contac.email',
    //         'log_cotizacion.email_proveedor',
    //         'log_cotizacion.estado_envio',
    //         'log_cotizacion.estado AS estado_cotizacion',
    //         'log_esp_compra.id_especificacion_compra',
    //         'log_esp_compra.id_condicion_pago',
    //         'log_esp_compra.plazo_entrega',
    //         'log_esp_compra.fecha_entrega',
    //         'log_esp_compra.lugar_entrega',
    //         'log_esp_compra.detalle_envio',
    //         'log_esp_compra.observacion',
    //         'log_esp_compra.forma_pago_credito',
    //         'log_esp_compra.estado AS estado_especificacion_compra',
    //         'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
    //         'log_valorizacion_cotizacion.id_cotizacion AS valorizacion_id_cotizacion',
    //         'log_valorizacion_cotizacion.id_detalle_requerimiento AS valorizacion_id_detalle_requerimiento',
    //         'log_valorizacion_cotizacion.id_detalle_oc_cliente',
    //         'log_valorizacion_cotizacion.precio_cotizado',
    //         'log_valorizacion_cotizacion.cantidad_cotizada',
    //         'log_valorizacion_cotizacion.subtotal',
    //         'log_valorizacion_cotizacion.flete',
    //         'log_valorizacion_cotizacion.porcentaje_descuento',
    //         'log_valorizacion_cotizacion.monto_descuento',
    //         'log_valorizacion_cotizacion.estado AS valorizacion_estado'
    //             )
    //     ->leftJoin('logistica.log_prove','log_prove.id_proveedor','=','log_cotizacion.id_proveedor')
    //     ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
    //     ->leftJoin('contabilidad.sis_identi','sis_identi.id_doc_identidad','=','adm_contri.id_doc_identidad')
    //     ->leftJoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_cotizacion','=','log_cotizacion.id_cotizacion')
        
    //     ->leftJoin('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','log_valorizacion_cotizacion.id_detalle_requerimiento')
    //     ->leftJoin('almacen.alm_item','alm_item.id_item','=','alm_det_req.id_item')
    //     ->leftJoin('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
 
    //     ->leftJoin('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
    //     ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')

    //     ->leftJoin('contabilidad.adm_ctb_contac','adm_ctb_contac.id_contribuyente','=','adm_contri.id_contribuyente')
    //     ->leftJoin('administracion.adm_empresa','adm_empresa.id_empresa','=','log_cotizacion.id_empresa')
    //     ->leftJoin('contabilidad.adm_contri as contri','contri.id_contribuyente','=','adm_empresa.id_contribuyente')
    //     ->leftJoin('contabilidad.sis_identi as identi','identi.id_doc_identidad','=','contri.id_doc_identidad')
    //     ->leftJoin('logistica.log_esp_compra','log_esp_compra.id_especificacion_compra','=','log_valorizacion_cotizacion.id_especificacion_compra')

    //     ->where(
    //         [['log_cotizacion.estado', '>', 0],
    //         ['log_cotizacion.id_cotizacion', '=', $id_cotizacion]
    //         ])
    //     ->get();


  

    //      if(sizeof($log_cotizacion)>0){
    //         foreach($log_cotizacion as $data){
    //             $cotizacionArray[] = [
                    
    //                 'id_cotizacion'=> $data->id_cotizacion,
    //                 'codigo_cotizacion'=> $data->codigo_cotizacion,
    //                 'codigo_requerimiento'=> $data->codigo_req,
    //                 'codigo_proveedor'=> $data->codigo_proveedor,
    //                 'estado_envio'=> $data->estado_envio,
    //                 'estado'=> $data->estado_cotizacion,
    //                 'empresa'=>[
    //                     'id_empresa'=> $data->id_empresa,
    //                     'razon_social'=> $data->razon_social_empresa,
    //                     'nro_documento'=> $data->nro_documento_empresa,
    //                     'nombre_doc_identidad'=> $data->nombre_doc_idendidad_empresa
    //                 ],
    //                 'proveedor'=>[
    //                                 "id_proveedor"=>$data->id_proveedor,
    //                                 "razon_social"=>$data->razon_social,
    //                                 "nro_documento"=>$data->nro_documento,
    //                                 "id_doc_identidad"=>$data->id_doc_identidad,
    //                                 "nombre_doc_identidad"=>$data->nombre_doc_identidad,
    //                                 "contacto"=>[
    //                                     "email"=>$data->email_proveedor
    //                                     ]
    //                                 ]
    //             ];


    //             $cotizacionItemArray[] = [
    //                 "id_cotizacion"=>$data->id_cotizacion,
    //                 "id_detalle_requerimiento"=>$data->id_detalle_requerimiento,
    //                 "codigo_cotizacion"=>$data->codigo_cotizacion,
    //                 "codigo"=>$data->codigo,
    //                 "descripcion"=>$data->descripcion,
    //                 "cantidad"=>$data->cantidad,
    //                 "id_unidad_medida"=>$data->id_unidad_medida,
    //                 "unidad_medida_descripcion"=>$data->unidad_medida_descripcion,
    //                 "fecha_registro"=>$data->fecha_registro,
    //                 "estado"=>$data->estado
    //             ];

    //             $especificacionCompraArray[] = [
    //                 "id_cotizacion"=>$data->valorizacion_id_cotizacion,
    //                 "id_especificacion_compra"=>$data->id_especificacion_compra,
    //                 "id_condicion_pago"=>$data->id_condicion_pago,
    //                 "plazo_entrega"=>$data->plazo_entrega,
    //                 "lugar_entrega"=>$data->lugar_entrega,
    //                 "fecha_entrega"=>$data->fecha_entrega,
    //                 "detalle_envio"=>$data->detalle_envio,
    //                 "observacion"=>$data->observacion,
    //                 "forma_pago_credito"=>$data->forma_pago_credito,
    //                 "estado"=>$data->estado_especificacion_compra
    //             ];

    //             $valorizacionItemArray[]=[
    //                 "id_valorizacion_cotizacion"=>$data->id_valorizacion_cotizacion,
    //                 "id_cotizacion"=>$data->valorizacion_id_cotizacion,
    //                 "id_detalle_requerimiento"=>$data->valorizacion_id_detalle_requerimiento,
    //                 "id_detalle_oc_cliente"=>$data->id_detalle_oc_cliente,
    //                 "id_especificacion_compra"=> $data->id_especificacion_compra,
    //                 "precio_cotizado"=>$data->precio_cotizado,
    //                 "cantidad_cotizada"=>$data->cantidad_cotizada,
    //                 "subtotal"=>$data->subtotal,
    //                 "flete"=>$data->flete,
    //                 "porcentaje_descuento"=>$data->porcentaje_descuento,
    //                 "monto_descuento"=>$data->monto_descuento,
    //                 "estado"=>$data->valorizacion_estado
    //             ];


    //         }

         
    //     $cotizacionArray = $this->unique_multidim_array($cotizacionArray,'id_cotizacion');
    //     $cotizacionItemArray = $this->unique_multidim_array2($cotizacionItemArray,'id_cotizacion','id_detalle_requerimiento');
        
    //         for($j=0; $j< sizeof($cotizacionItemArray);$j++){
    //             for($i=0; $i< sizeof($cotizacionArray);$i++){

    //                 if($cotizacionItemArray[$j]['id_cotizacion'] === $cotizacionArray[$i]['id_cotizacion']){
    //                         $cotizacionArray[$i]['items'][] = [
    //                             'id_cotizacion' => $cotizacionItemArray[$j]['id_cotizacion'],
    //                             'id_detalle_requerimiento' => $cotizacionItemArray[$j]['id_detalle_requerimiento'],
    //                             'codigo' => $cotizacionItemArray[$j]['codigo'],
    //                             'descripcion' => $cotizacionItemArray[$j]['descripcion'], 
    //                             'cantidad' => $cotizacionItemArray[$j]['cantidad'], 
    //                             'id_unidad_medida' => $cotizacionItemArray[$j]['id_unidad_medida'], 
    //                             'unidad_medida_descripcion' => $cotizacionItemArray[$j]['unidad_medida_descripcion'], 
    //                             'fecha_registro' => $cotizacionItemArray[$j]['fecha_registro'],
    //                             'estado' => $cotizacionItemArray[$j]['estado']
    //                         ];
    //                  }
    //             }
    //         }

    //         for($j=0; $j< sizeof($especificacionCompraArray);$j++){
    //             for($i=0; $i< sizeof($cotizacionArray);$i++){
    //                 if($especificacionCompraArray[$j]['id_cotizacion'] === $cotizacionArray[$i]['id_cotizacion']){
    //                       $cotizacionArray[$i]['especificacion_compra']= [
    //                                                                          'id_especificacion_compra'=>$especificacionCompraArray[$j]['id_especificacion_compra'],
    //                                                                          'id_condicion_pago'=>$especificacionCompraArray[$j]['id_condicion_pago'],
    //                                                                         'plazo_entrega'=>$especificacionCompraArray[$j]['plazo_entrega'],
    //                                                                         'lugar_entrega'=>$especificacionCompraArray[$j]['lugar_entrega'],
    //                                                                         'fecha_entrega'=>$especificacionCompraArray[$j]['fecha_entrega'],
    //                                                                         'detalle_envio'=>$especificacionCompraArray[$j]['detalle_envio'],
    //                                                                         'observacion'=>$especificacionCompraArray[$j]['observacion'],
    //                                                                         'forma_pago_credito'=>$especificacionCompraArray[$j]['forma_pago_credito'],
    //                                                                         'estado'=>$especificacionCompraArray[$j]['estado']
    //                                                                        ];

                 
    //                  }
    //             }
    //         }

    //         $valorizacionItemArray = $this->unique_multidim_array($valorizacionItemArray,'id_valorizacion_cotizacion');

    //         for($j=0; $j< sizeof($valorizacionItemArray);$j++){
    //             for($i=0; $i< sizeof($cotizacionArray);$i++){
    //                 if($valorizacionItemArray[$j]['id_cotizacion'] === $cotizacionArray[$i]['id_cotizacion']){
    //                       $cotizacionArray[$i]['valorizacion'][]= [
    //                                                                         'id_valorizacion_cotizacion'=>$valorizacionItemArray[$j]['id_valorizacion_cotizacion'],
    //                                                                         'id_cotizacion'=>$valorizacionItemArray[$j]['id_cotizacion'],
    //                                                                         'id_detalle_requerimiento'=>$valorizacionItemArray[$j]['id_detalle_requerimiento'],
    //                                                                         'id_detalle_oc_cliente'=>$valorizacionItemArray[$j]['id_detalle_oc_cliente'],
    //                                                                         'id_especificacion_compra'=>$valorizacionItemArray[$j]['id_especificacion_compra'],
    //                                                                         'precio_cotizado'=>$valorizacionItemArray[$j]['precio_cotizado'],
    //                                                                         'cantidad_cotizada'=>$valorizacionItemArray[$j]['cantidad_cotizada'],
    //                                                                         'subtotal'=>$valorizacionItemArray[$j]['subtotal'],
    //                                                                         'flete'=>$valorizacionItemArray[$j]['flete'],
    //                                                                         'porcentaje_descuento'=>$valorizacionItemArray[$j]['porcentaje_descuento'],
    //                                                                         'monto_descuento'=>$valorizacionItemArray[$j]['monto_descuento'],
    //                                                                         'estado'=>$valorizacionItemArray[$j]['estado']
    //                                                                        ];                
    //                  }
    //             }
    //         }
    //     }else{
    //         return $cotizacionArray=[];
    //     }
    //     return $cotizacionArray;
    // }

    public function get_cotizacion($id_cotizacion){

        $cotizacion = DB::table('logistica.log_cotizacion')
        ->select(

            'log_cotizacion.id_cotizacion',
            'log_cotizacion.codigo_cotizacion',
            'log_cotizacion.id_proveedor',
            'log_cotizacion.codigo_proveedor',
            'log_cotizacion.estado_envio',
            'log_cotizacion.estado',
            'log_cotizacion.id_empresa',
            'log_cotizacion.email_proveedor',
            'log_cotizacion.detalle'
            )
            ->where(
                [['log_cotizacion.estado', '>', 0],
                ['log_cotizacion.id_cotizacion', '=', $id_cotizacion]
                ])
            ->first();

        $proveedor = DB::table('logistica.log_prove')
        ->select(
            'log_prove.id_proveedor',
            'log_prove.codigo',
            // 'log_prove.id_contribuyente',
            'adm_contri.id_doc_identidad',
            'sis_identi.descripcion AS nombre_doc_identidad',
            'adm_contri.nro_documento',
            'adm_contri.razon_social',
            'adm_contri.telefono',
            'adm_contri.celular',
            'adm_contri.direccion_fiscal',
            'sis_pais.descripcion AS nombre_pais'
            // 'adm_contri.estado'
            )
        ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi','sis_identi.id_doc_identidad','=','adm_contri.id_doc_identidad')
        ->leftJoin('configuracion.sis_pais','sis_pais.id_pais','=','adm_contri.id_pais')
            ->where(
                [['log_prove.estado', '>', 0],
                ['log_prove.id_proveedor', '=', $cotizacion->id_proveedor]
                ])
            ->get();

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
        ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi','sis_identi.id_doc_identidad','=','adm_contri.id_doc_identidad')
            ->where(
                [['adm_empresa.estado', '>', 0],
                ['adm_empresa.id_empresa', '=', $cotizacion->id_empresa]
                ])
            ->get();

        $detalle_grupo_cotizacion = DB::table('logistica.log_detalle_grupo_cotizacion')
        ->select(
            'log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion',
            'log_detalle_grupo_cotizacion.id_grupo_cotizacion',
            'log_detalle_grupo_cotizacion.id_oc_cliente',
            'log_detalle_grupo_cotizacion.id_cotizacion'
            )
            ->where(
                [['log_detalle_grupo_cotizacion.estado', '>', 0],
                ['log_detalle_grupo_cotizacion.id_cotizacion', '=', $cotizacion->id_cotizacion]
                ])
            ->get();



        $valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')
        ->select(
            'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
            'log_valorizacion_cotizacion.id_cotizacion',
            'log_valorizacion_cotizacion.id_detalle_requerimiento',
            'log_valorizacion_cotizacion.id_detalle_oc_cliente' ,
            'log_valorizacion_cotizacion.precio_cotizado',
            'log_valorizacion_cotizacion.cantidad_cotizada',
            'log_valorizacion_cotizacion.subtotal',
            'log_valorizacion_cotizacion.flete',
            'log_valorizacion_cotizacion.porcentaje_descuento',
            'log_valorizacion_cotizacion.monto_descuento',
            'log_valorizacion_cotizacion.estado AS estado_valorizacion',
            'log_valorizacion_cotizacion.justificacion',
            'log_valorizacion_cotizacion.id_requerimiento',
            'alm_req.codigo as codigo_requerimiento',

            'alm_item.codigo',
            'alm_item.id_producto',
            'alm_prod.descripcion as descripcion_producto',
            'alm_prod.descripcion',
            'alm_prod.id_unidad_medida',
            'alm_prod.estado AS estado_prod',
            'alm_und_medida.descripcion AS unidad_medida_descripcion',

            'alm_item.id_servicio',
            // 'alm_det_req.id_requerimiento',
            'alm_det_req.id_item',
            'alm_det_req.precio_referencial',
            'alm_det_req.cantidad',
            'alm_det_req.fecha_entrega',
            'alm_det_req.descripcion_adicional',
            'alm_det_req.obs',
            'alm_det_req.partida',
            'alm_det_req.unidad_medida',
            'alm_det_req.fecha_registro'
            )
        ->leftJoin('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','log_valorizacion_cotizacion.id_detalle_requerimiento')
        ->leftJoin('almacen.alm_req','alm_req.id_requerimiento','=','log_valorizacion_cotizacion.id_requerimiento')
        ->leftJoin('almacen.alm_item','alm_item.id_item','=','alm_det_req.id_item')
        ->leftJoin('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
        ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where(
                [['log_valorizacion_cotizacion.estado', '>', 0],
                ['log_valorizacion_cotizacion.id_cotizacion', '=', $cotizacion->id_cotizacion]
                ])
            ->get();


            // $requerimiento = DB::table('almacen.alm_req')
            // ->select(
            //     'alm_req.id_requerimiento',
            //     'alm_req.codigo')
            //     ->where(
            //         [['alm_req.estado', '>', 0],
            //         ['alm_req.id_requerimiento', '=', $detalle_grupo_cotizacion[0]->id_requerimiento]
            //         ])
            //     ->get();



            $items=[];
            foreach($valorizacion_cotizacion as $data){
                $items[]=[
                    'id_cotizacion' => $data->id_cotizacion,
                    'id_requerimiento'=> $data->id_requerimiento,
                    'codigo_requerimiento'=> $data->codigo_requerimiento,
                    'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                    'codigo' => $data->codigo,
                    'descripcion' => $data->descripcion, 
                    'cantidad' => $data->cantidad, 
                    'id_unidad_medida' => $data->id_unidad_medida, 
                    'unidad_medida_descripcion' => $data->unidad_medida_descripcion, 
                    'fecha_registro' => $data->fecha_registro,
                    'estado' => $data->estado_prod
                ];

            }

            $cotizacion_item=[
                    'id_cotizacion'=> $cotizacion->id_cotizacion,
                    'id_grupo_cotizacion'=> $detalle_grupo_cotizacion[0]->id_grupo_cotizacion,
                    'codigo_cotizacion'=> $cotizacion->codigo_cotizacion,
                    'codigo_proveedor'=> $proveedor[0]->codigo,
                    'estado_envio'=> $cotizacion->estado_envio,
                    'estado'=> $cotizacion->estado,
                    'empresa'=>[
                        'id_empresa'=> $empresa[0]->id_empresa,
                        'razon_social'=> $empresa[0]->razon_social,
                        'nro_documento'=> $empresa[0]->nro_documento,
                        'nombre_doc_identidad'=> $empresa[0]->nombre_doc_identidad
                    ],
                    'proveedor'=>[
                                    "id_proveedor"=>$proveedor[0]->id_proveedor,
                                    "razon_social"=>$proveedor[0]->razon_social,
                                    "nro_documento"=>$proveedor[0]->nro_documento,
                                    "id_doc_identidad"=>$proveedor[0]->id_doc_identidad,
                                    "nombre_doc_identidad"=>$proveedor[0]->nombre_doc_identidad,
                                    "contacto"=>[
                                        "email"=>$cotizacion->email_proveedor,
                                        "telefono"=>$proveedor[0]->telefono
                                        ]
                                    ],
                    'items'=>$items
                
                    ];

    
        return [$cotizacion_item];
    } 



    public function mostrar_cotizacion($id_cotizacion){
        $cotizacionArray= $this->get_cotizacion($id_cotizacion);    
        return response()->json($cotizacionArray);

    }


    
    public function guardar_valorizacion_proveedor(Request $request,$id_cotizacion){

    $valorizacion = $request->valorizacion;

        // $data_cotizacion = DB::table('logistica.log_cotizacion')->where('id_cotizacion','=', $id_cotizacion)
        //         ->update([
        //         'id_condicion_pago'      => $request->condicion_compra,
        //         'id_tp_doc'              => $request->tipo_comprobante,
        //         'nro_cuenta_principal'     => $request->nro_cuenta_principal,
        //         'nro_cuenta_alternativa'     => $request->nro_cuenta_alternativa,
        //         'nro_cuenta_detraccion'=> $request->nro_cuenta_detraccion,
        //         'detalle'                => $request->detalle,
        //         'estado'                 => 1              
        //         ]);

        $count_valorizacion= count($valorizacion);      
            for ($i=0; $i< $count_valorizacion; $i++){

                $data_valorizacion = DB::table('logistica.log_valorizacion_cotizacion')
                ->where([
                    ['id_cotizacion', $id_cotizacion],
                    ['id_detalle_requerimiento', $valorizacion[$i]['id_detalle_requerimiento']]
                    ])
                ->update([
                    'precio_cotizado'        => $valorizacion[$i]['precio_cotizado'],
                    'cantidad_cotizada'      => $valorizacion[$i]['cantidad_cotizada'],
                    'subtotal'               => $valorizacion[$i]['subtotal'],
                    'flete'                  => $valorizacion[$i]['flete'],
                    'porcentaje_descuento'   => $valorizacion[$i]['porcentaje_descuento'],
                    'monto_descuento'        => $valorizacion[$i]['monto_descuento'],
                    'estado'                 => 1,
                    'detalle'                => $valorizacion[$i]['detalle'],              
                    'plazo_entrega'          => $valorizacion[$i]['plazo_entrega'],              
                    'incluye_igv'             => $valorizacion[$i]['incluye_igv'],              
                    'garantia'               => $valorizacion[$i]['garantia']              
                    ]);
            }
            return response()->json($data_valorizacion);

    }

        
    public function imprimir_cotizacion_excel($id_cotizacion){

        $cotizacionArray= $this->get_cotizacion($id_cotizacion);    
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
                .subtitle{
                    font-weight: bold;
                }
 
            </style>
            </head>
            <body>
                <h1><center>COTIZACIÓN N°'.$cotizacionArray[0]['codigo_cotizacion'].'</center></h1>
                 <br><br>
                <table border="0">
            <tr>
                <td class="subtitle">N° REQ.</td>
                <td>'.$cotizacionArray[0]['codigo_requerimiento'].'</td>
            </tr>
            </tr>  
                <tr>
                    <td class="subtitle">N° COTIZACIÓN</td>
                    <td width="300">'.$cotizacionArray[0]['codigo_cotizacion'].'</td>
                    <td class="subtitle">FECHA</td>
                    <td>'.$now->format('d-m-Y').'</td>
                </tr>
                <tr>
                    <td class="subtitle">CLIENTE</td>
                    <td>'.$cotizacionArray[0]['empresa']['razon_social'].'</td>
                </tr>    
                <tr>
                    <td class="subtitle">PROVEEDOR</td>
                <td>'.$cotizacionArray[0]['proveedor']['razon_social'].'</td>
                </tr>
                </table>
                <br>
                <hr>
                <br>
                <table width="100%" class="tablePDF">
                <tr class="subtitle">
                    <td>Item</td>
                    <td>Descripcion</td>
                    <td>Und. Medida</td>
                    <td>Cantidad Solicitada</td>
                    <td>Und. de Medida</td>
                    <td>Cantidad</td>
                    <td>Precio</td>
                    <td>Lugar de Despacho</td>
                    <td>Sub-Total</td>
                    <td>Incluye IGV</td>
                    <td>Plazo Entrega</td>
                    <td>Garantia</td>
                    <td>Observación</td>
                     
                </tr>   ';
                foreach ($cotizacionArray as $row){
                foreach ($row['items'] as $item){
              
                           
                   
                    $id_cotizacion = $item['id_cotizacion'];
                    $id_detalle_requerimiento = $item['id_detalle_requerimiento'];
                    $codigo = $item['codigo'];
                    $unidad_medida = $item['unidad_medida_descripcion'];
                    $cantidad = $item['cantidad'];
                    $descripcion = $item['descripcion'];
        
       
        
 
 
                    $html .=
                        '
                            <tr>
                                <td>'.$codigo.'</td>
                                <td>'.$descripcion.'</td>
                                <td>'.$unidad_medida.'</td>
                                <td>'.$cantidad.'</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                    ';
                }
            }
            
        $html .= '
        </table>
        <br/>

        <table width="100%" class="tablePDF">
        <tr>
            <th>Tipo Comprobante</th>
            <td colspan="12"></td>

        </tr>
        <tr>
            <th>Condicion Compra</th>
            <td colspan="12"></td>

        </tr>
        <tr>
            <th>N° Cuenta Banco Principal</th>
            <td colspan="12"></td>

        </tr>
        <tr>
            <th>N° Cuenta Banco Alternativa</th>
            <td colspan="12"></td>

        </tr>
        <tr>
            <th>N° Cuenta Detracción</th>
            <td colspan="12"></td>
        </tr>
        </table>
        
        <br/> 
        
        <table>
        <tr>
        <td>* Adjuntar fichas técnicas</td>
        </tr>
        </table>
        
        </body>
        </html>';

        return $html;
 
    }

    public function solicitud_cotizacion_excel($id_cotizacion){
        $data = $this->imprimir_cotizacion_excel($id_cotizacion);
        return view('logistica/reportes/downloadExcelFormatoSolicitudCotizacion', compact('data'));
 
    }

    public function imprimir_cuadro_comparativo_excel($id_cotizacion){

        $data= $this->get_cuadro_comparativo($id_cotizacion);    
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

         

            $nameFile = asset('images').'/logo_okc.png';             

            $html.='
            <img src="'.$nameFile.'" height="75px" >

                <h1><center>CUADRO COMPARATIVO '.$data['head']['codigo_grupo'].'</center></h1>
                <br><br>
                <table border="0">
            <tr>
                <td class="subtitle">EMPRESA</td>
                <td>'.$data['head']['empresa_razon_social'].' - '.$data['head']['empresa_nombre_doc_identidad'].' '.$data['head']['empresa_nro_documento'].' </td>
            </tr>
            <tr>
                <td class="subtitle">N° REQ.</td>
                <td>'.$data['head']['codigo_req'].'</td>
            </tr>
            </tr>  
                <tr>
                    <td class="subtitle">N° CUADRO COMP.</td>
                    <td width="300">'.$data['head']['codigo_grupo'].'</td>
                    <td class="subtitle">FECHA INICIO COTIZACIÓN</td>
                    <td>'.$data['head']['fecha_inicio'].'</td>
                </tr>
                <tr>
                    <td class="subtitle">COTIZADOR</td>
                    <td>'.$data['head']['full_name'].'</td>
                    <td class="subtitle">FECHA FIN COTIZACIÓN</td>
                    <td>'.$data['head']['fecha_fin'].'</td>
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
                    foreach ($data['cuadro_comparativo'][0]['valorizacion'] as $item){
                        $html .='<th colspan="9" class="center">'.$item['proveedor']['razon_social'].'<br>'.$item['proveedor']['nombre_doc_identidad'].' '. $item['proveedor']['nro_documento'].'</th>';
                    }
                // }


                $html .='
                </tr>
                <tr class="subtitle">
                ';


                // foreach ($data['cuadro_comparativo'] as $row){
                    foreach ($data['cuadro_comparativo'][0]['valorizacion'] as $item){
                $html .='
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

                $html .='</tr>';

                foreach ($data['cuadro_comparativo'] as $row){

                    $html .='
                
                    <tr>
                    <td>&nbsp;'.$row['codigo'].'</td>
                    <td>'.$row['descripcion'].'</td>
                    <td>'.$row['cantidad'].'</td>
                    <td>'.$row['unidad_medida'].'</td>
                    <td>'.$row['precio_referencial'].'</td>';

                foreach ($row['valorizacion'] as $item){

                    $html .=
                        '
                            <td>'.$item['cantidad_cotizada'].'</td>
                            <td>'.$item['unidad_medida'].'</td>
                            <td>'.$item['precio_cotizado'].'</td>
                            <td>'.$item['incluye_igv'].'</td>
                            <td>'.$item['porcentaje_descuento'].'</td>
                            <td>'.$item['monto_descuento'].'</td>
                            <td>'.$item['subtotal'].'</td>
                            <td>'.$item['plazo_entrega'].'</td>
                            <td>'.$item['lugar_despacho'].'</td>
                    ';
                }
                $html.='  </tr>';
            }
            
        $html .= '
        </table>
        <br/>

        <table width="100%" class="tablePDF">
        <tr>
            <th  colspan="5" class="right">Tipo Comprobante</th>
        ';
            // foreach ($data['cuadro_comparativo'] as $row){
                foreach ($data['cuadro_comparativo'][0]['valorizacion'] as $item){
            $html .='<td colspan="9">'.$item['proveedor']['tipo_documento'].'</td>';
                }
            // }
        $html .=  '
        </tr>
        <tr>
            <th  colspan="5" class="right">Condicion de Compra</th>
            ';
            // foreach ($data['cuadro_comparativo'] as $row){
                foreach ($data['cuadro_comparativo'][0]['valorizacion'] as $item){
            $html .='<td colspan="9">'.$item['proveedor']['condicion_pago'].'</td>';
                }
            // }
        $html .=  '
        </tr>
        <tr>
            <th  colspan="5" class="right">Número de Cuenta Banco Principal</th>
        ';
            // foreach ($data['cuadro_comparativo'] as $row){
                foreach ($data['cuadro_comparativo'][0]['valorizacion'] as $item){
            $html .='<td colspan="9">&nbsp;'.$item['proveedor']['nro_cuenta_principal'].'</td>';
                }
            // }
        $html .=   '
        </tr>
        <tr>
            <th  colspan="5" class="right">Número de Cuenta Banco Alternativa</th>
        ';
            // foreach ($data['cuadro_comparativo'] as $row){
                foreach ($data['cuadro_comparativo'][0]['valorizacion'] as $item){
            $html .='<td colspan="9">&nbsp;'.$item['proveedor']['nro_cuenta_alternativa'].'</td>';
                }
            // }
        $html .= '
        </tr>
        <tr>
            <th  colspan="5" class="right">Número de Cuenta Banco Detracción</th>
        ';
            // foreach ($data['cuadro_comparativo'] as $row){
                foreach ($data['cuadro_comparativo'][0]['valorizacion'] as $item){
            $html .='<td colspan="9">&nbsp;'.$item['proveedor']['nro_cuenta_detraccion'].'</td>';
                }
            // }
    $html .='
        </tr>
        </table>
        
        <br/> 
        </body>
        </html>';

        return $html;
 
    }

    public function cuadro_comparativo_excel($id_cotizacion){
        $data = $this->imprimir_cuadro_comparativo_excel($id_cotizacion);
        return view('logistica/reportes/downloadExcelFormatoCuadroComparativo', compact('data'));
 
    }

    public function get_cotizacion_valorizacion_especificacion($id_cotizacion){

        $cotizacion= $this->get_cotizacion($id_cotizacion);    

        $valorizacion = DB::table('logistica.log_valorizacion_cotizacion')
        ->select(

            'log_valorizacion_cotizacion.*'
            )
            ->where(
                [['log_valorizacion_cotizacion.estado', '>', 0],
                ['log_valorizacion_cotizacion.id_cotizacion', '=', $id_cotizacion]
                ])
            ->get();

  



        $cotizacion[0]['valorizacion']=$valorizacion;
 
        return $cotizacion;
    } 

    public function mostrar_cotizacion_valorizacion_especificacion($id_cotizacion){
        $cotizacionValorizacion= $this->get_cotizacion_valorizacion_especificacion($id_cotizacion);    
        return response()->json($cotizacionValorizacion);

    }


    public function get_cuadro_comparativo($id_cotizacion){
        $grupo_cotizacion = DB::table('logistica.log_grupo_cotizacion')
        ->select( 
                'log_grupo_cotizacion.id_grupo_cotizacion',                                 
                'log_detalle_grupo_cotizacion.id_requerimiento'                                
                )
        ->leftJoin('logistica.log_detalle_grupo_cotizacion','log_detalle_grupo_cotizacion.id_grupo_cotizacion','=','log_grupo_cotizacion.id_grupo_cotizacion')
        // ->leftJoin('almacen.alm_req','alm_req.id_requerimiento','=','log_detalle_grupo_cotizacion.id_requerimiento')
        ->leftJoin('logistica.log_cotizacion','log_cotizacion.id_cotizacion','=','log_detalle_grupo_cotizacion.id_cotizacion')
        ->where([['log_cotizacion.id_cotizacion', '=', $id_cotizacion]])
        ->first();

        $cotizaciones = DB::table('logistica.log_grupo_cotizacion')
        ->select( 
                'log_detalle_grupo_cotizacion.id_cotizacion'                                 
                )
        ->leftJoin('logistica.log_detalle_grupo_cotizacion','log_detalle_grupo_cotizacion.id_grupo_cotizacion','=','log_grupo_cotizacion.id_grupo_cotizacion')
        ->where([['log_detalle_grupo_cotizacion.id_grupo_cotizacion', '=', $grupo_cotizacion->id_grupo_cotizacion]])
        ->get();

        $cotizacioneArray=[];
        foreach($cotizaciones as $data){
            $cotizacioneArray[]=$data->id_cotizacion;
        }

        $requIds = DB::table('logistica.log_valorizacion_cotizacion')
        ->select(
            'log_valorizacion_cotizacion.id_requerimiento'
        )
        ->where(
            [['log_valorizacion_cotizacion.estado', '=', 1],
            ])
            ->whereIn('log_valorizacion_cotizacion.id_cotizacion',$cotizacioneArray)
        ->get();

        $reqIdArray=[];
        foreach($requIds as $data){
            $reqIdArray[]=$data->id_requerimiento;
        }

            
        $detalle_requerimiento = DB::table('almacen.alm_req')
        ->select( 
                    'alm_det_req.id_detalle_requerimiento',                                 
                    'alm_item.codigo',                                 
                    'alm_prod.descripcion',                                 
                    'alm_det_req.cantidad',                                 
                    'alm_det_req.unidad_medida',                                 
                    'alm_det_req.precio_referencial'                                 
                )
        ->leftJoin('almacen.alm_det_req','alm_det_req.id_requerimiento','=','alm_req.id_requerimiento')
        ->leftJoin('almacen.alm_item','alm_item.id_item','=','alm_det_req.id_item')
        ->leftJoin('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
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
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS full_name"),
            'log_cotizacion.codigo_cotizacion',
            'cont_tp_doc.descripcion as tipo_documento',
            'log_cdn_pago.descripcion AS condicion_pago',
            'log_cotizacion.nro_cuenta_principal',
            'log_cotizacion.nro_cuenta_alternativa',
            'log_cotizacion.nro_cuenta_detraccion',
            'log_cotizacion.email_proveedor',
            'log_prove.id_proveedor',
            'adm_contri.razon_social',
            'adm_contri.nro_documento',
            'adm_contri.id_doc_identidad',
            'sis_identi.descripcion as nombre_doc_identidad',
            'alm_req.codigo AS codigo_req'
                )
        ->leftJoin('logistica.log_detalle_grupo_cotizacion','log_detalle_grupo_cotizacion.id_cotizacion','=','log_cotizacion.id_cotizacion')
        ->leftJoin('almacen.alm_req','alm_req.id_requerimiento','=','log_detalle_grupo_cotizacion.id_requerimiento')
        ->leftJoin('logistica.log_grupo_cotizacion','log_grupo_cotizacion.id_grupo_cotizacion','=','log_detalle_grupo_cotizacion.id_grupo_cotizacion')
        ->leftJoin('logistica.log_prove','log_prove.id_proveedor','=','log_cotizacion.id_proveedor')
        ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi','sis_identi.id_doc_identidad','=','adm_contri.id_doc_identidad')
        ->leftJoin('contabilidad.cont_tp_doc','cont_tp_doc.id_tp_doc','=','log_cotizacion.id_tp_doc')
        ->leftJoin('logistica.log_cdn_pago','log_cdn_pago.id_condicion_pago','=','log_cotizacion.id_condicion_pago')
        ->leftJoin('administracion.adm_empresa','adm_empresa.id_empresa','=','log_cotizacion.id_empresa')
        ->leftJoin('contabilidad.adm_contri as empresa_adm_contri','empresa_adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi as empresa_sis_identi','empresa_sis_identi.id_doc_identidad','=','empresa_adm_contri.id_doc_identidad')
        ->leftJoin('configuracion.sis_usua','sis_usua.id_usuario','=','log_grupo_cotizacion.id_usuario')
        ->leftJoin('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','sis_usua.id_trabajador')
        ->leftJoin('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
        ->leftJoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->where(
                [['log_cotizacion.estado', '>', 0],
                ['log_grupo_cotizacion.id_grupo_cotizacion', '=', $grupo_cotizacion->id_grupo_cotizacion]
                ])
        ->get();

    $empresa_cotizacion=[];
    $proveedor_cotizacion=[];
    $head_cuadro=[];
        foreach($log_cotizacion as $data){
            
            $head_cuadro[]=[
                'codigo_req' => $data->codigo_req,
                'codigo_grupo' => $data->codigo_grupo,
                'full_name' => $data->full_name,
                'fecha_inicio' => $data->fecha_inicio,
                'fecha_fin' => $data->fecha_fin,
                'empresa_nombre_doc_identidad' => $data->empresa_nombre_doc_identidad,
                'empresa_nro_documento' => $data->empresa_nro_documento,
                'empresa_razon_social' => $data->empresa_razon_social
            ];

            $empresa_cotizacion[]=[
                'id_cotizacion' => $data->id_cotizacion,
                'codigo_cotizacion' => $data->codigo_cotizacion,
                'codigo_grupo' => $data->codigo_grupo,
                'id_empresa' => $data->id_empresa,
                'empresa_id_doc_identidad' => $data->empresa_id_doc_identidad,
                'empresa_nombre_doc_identidad' => $data->empresa_nombre_doc_identidad,
                'empresa_nro_documento' => $data->empresa_nro_documento,
                'empresa_razon_social' => $data->empresa_razon_social,
                'empresa_telefono' => $data->empresa_telefono,
                'empresa_celular' => $data->empresa_celular,
                'empresa_direccion_fiscal' => $data->empresa_direccion_fiscal    
            ];
            $proveedor_cotizacion[]=[
                'id_cotizacion' => $data->id_cotizacion,
                'codigo_cotizacion' => $data->codigo_cotizacion,
                'codigo_grupo' => $data->codigo_grupo,
                'tipo_documento' => $data->tipo_documento,
                'condicion_pago' => $data->condicion_pago,
                'nro_cuenta_principal' => $data->nro_cuenta_principal,
                'nro_cuenta_alternativa' => $data->nro_cuenta_alternativa,
                'nro_cuenta_detraccion' => $data->nro_cuenta_detraccion,
                'email_proveedor' => $data->email_proveedor,
                'id_proveedor' => $data->id_proveedor,
                'razon_social' => $data->razon_social,
                'nro_documento' => $data->nro_documento,
                'id_doc_identidad' => $data->id_doc_identidad,
                'nombre_doc_identidad' => $data->nombre_doc_identidad,
                'codigo_req' => $data->codigo_req
            ];
        }


        $det_req=[];
        foreach($detalle_requerimiento as $data){
            $det_req[]=[
                'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                'codigo' => $data->codigo,
                'descripcion' => $data->descripcion,
                'cantidad' => $data->cantidad,
                'unidad_medida' => $data->unidad_medida,
                'precio_referencial' => $data->precio_referencial,
                'valorizacion'=>[]
            
            ];

        }
        

        $valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')
        ->select(
        
            'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
            'log_valorizacion_cotizacion.id_cotizacion',
            'log_cotizacion.id_proveedor',
            'log_valorizacion_cotizacion.id_detalle_requerimiento',
            'log_valorizacion_cotizacion.id_detalle_oc_cliente' ,
            'log_valorizacion_cotizacion.precio_cotizado',
            'log_valorizacion_cotizacion.incluye_igv',
            'log_valorizacion_cotizacion.cantidad_cotizada',
            'log_valorizacion_cotizacion.subtotal',
            'log_valorizacion_cotizacion.flete',
            'log_valorizacion_cotizacion.lugar_despacho',
            'log_valorizacion_cotizacion.plazo_entrega',
            'log_valorizacion_cotizacion.fecha_registro',
            'log_valorizacion_cotizacion.porcentaje_descuento',
            'log_valorizacion_cotizacion.monto_descuento',
            'log_valorizacion_cotizacion.estado AS estado_valorizacion',
            'log_valorizacion_cotizacion.justificacion',
            'alm_item.id_item',
            'alm_item.codigo',
            'alm_prod.descripcion as descripcion_producto',
            'alm_prod.id_unidad_medida',
            'alm_prod.estado AS estado_prod',
            'alm_und_medida.descripcion AS unidad_medida_descripcion'
            )
        ->leftJoin('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','log_valorizacion_cotizacion.id_detalle_requerimiento')
        ->leftJoin('almacen.alm_item','alm_item.id_item','=','alm_det_req.id_item')
        ->leftJoin('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
        ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
        ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_valorizacion_cotizacion.id_cotizacion')
            ->where(
                [['log_valorizacion_cotizacion.estado', '>', 0],
                // ['log_valorizacion_cotizacion.id_cotizacion', '=',  $id_cotizacion]
                ])
                ->whereIn('log_valorizacion_cotizacion.id_cotizacion',$cotizacioneArray)
            ->get();

            $buena_pro=[];
            $valorizacion=[];

            foreach($valorizacion_cotizacion as $data){

                if($data->estado_valorizacion === 2){
    //salida de buena_pro->
        // id_valorizacion_cotizacion: "54"
        // id_cotizacion:
        // id_detalle_requerimiento:
        // id_item:
        // codigo_item: "300300050001"
        // descripcion_item: "ABRAZADERA (GRAPA) FE GALVANIZADO 1 OREJA 1 1/2""
        // fecha_registro: "2019-06-04"
        // precio: "15"
        // razon_social: "MAXIMA SA"
        // nro_documento: "20619865472"
        // id_proveedor: "2"
        // justificacion: "prec"
                    $buena_pro[]=[
                        'id_valorizacion_cotizacion' => $data->id_valorizacion_cotizacion,
                        'id_cotizacion' => $data->id_cotizacion,
                        'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                        'id_item' => $data->id_item,
                        'codigo_item' => $data->codigo,
                        'descripcion_item' => $data->descripcion_producto,
                        'fecha_registro' => $data->fecha_registro,
                        'precio' => $data->precio_cotizado,
                        'id_proveedor' => $data->id_proveedor,
                        'justificacion' => $data->justificacion
                    ];
                }

                $valorizacion[]=[
                    'id_valorizacion_cotizacion' => $data->id_valorizacion_cotizacion,
                    'id_cotizacion' => $data->id_cotizacion,
                    'id_proveedor' => $data->id_proveedor,
                    'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                    'id_item' => $data->id_item,
                    'codigo' => $data->codigo,
                    'descripcion' => $data->descripcion_producto, 
                    'unidad_medida' => $data->unidad_medida_descripcion, 
                    'id_detalle_oc_cliente' => $data->id_detalle_oc_cliente, 
                    'precio_cotizado' => $data->precio_cotizado, 
                    'incluye_igv' => $data->incluye_igv, 
                    'cantidad_cotizada' => $data->cantidad_cotizada, 
                    'subtotal' => $data->subtotal, 
                    'flete' => $data->flete,
                    'lugar_despacho' => $data->lugar_despacho,
                    'plazo_entrega' => $data->plazo_entrega,
                    'porcentaje_descuento' => $data->porcentaje_descuento,
                    'monto_descuento' => $data->monto_descuento,
                    'justificacion' => $data->justificacion,
                    'estado' => $data->estado_valorizacion
                ];
            }

        //create => new matriz
        $items = $det_req;
        for($i=0; $i < sizeof($det_req);$i++){
            for($k=0; $k < sizeof($proveedor_cotizacion);$k++){
                $items[$i]['valorizacion'][]=[
                    
                    "id_valorizacion_cotizacion"=> 0,
                    "id_cotizacion"=> 0,
                    "id_proveedor"=> $proveedor_cotizacion[$k]['id_proveedor'],
                    "id_detalle_requerimiento"=> 0,
                    "id_item"=> 0,
                    "codigo"=> "",
                    "descripcion"=> "",
                    "unidad_medida"=> "",
                    "id_detalle_oc_cliente"=> 0,
                    "precio_cotizado"=> "",
                    "incluye_igv"=> "",
                    "cantidad_cotizada"=> "",
                    "subtotal"=> "",
                    "flete"=> "",
                    "lugar_despacho"=> "",
                    "plazo_entrega"=> "",
                    "porcentaje_descuento"=> "",
                    "monto_descuento"=> "",
                    "justificacion"=> "",
                    "estado"=> 0                    
                ]; 

            }
        }

        //add => valorización
        for($i=0; $i< sizeof($items);$i++){
            for($j=0; $j< sizeof($valorizacion);$j++){
                if($items[$i]['id_detalle_requerimiento'] === $valorizacion[$j]['id_detalle_requerimiento']){
                    for($k=0; $k < sizeof($items[$i]['valorizacion']); $k++){                   
                        if($items[$i]['valorizacion'][$k]['id_proveedor'] === $valorizacion[$j]['id_proveedor']){
                            $items[$i]['valorizacion'][$k]= $valorizacion[$j];
                        }
                    }
                }

            }
        }

        //add => data proveedor con valorizacion
        for($j=0; $j< sizeof($proveedor_cotizacion);$j++){
            for($i=0; $i< sizeof($items);$i++){
                for($n=0; $n< sizeof($items[$i]['valorizacion']);$n++){
                    if($items[$i]['valorizacion'][$n]['id_cotizacion'] === $proveedor_cotizacion[$j]['id_cotizacion']){
                        $items[$i]['valorizacion'][$n]['proveedor']=$proveedor_cotizacion[$j];
                    }
                    if($items[$i]['valorizacion'][$n]['id_cotizacion'] === 0){
                        $items[$i]['valorizacion'][$n]['proveedor']=$proveedor_cotizacion[$j];
                    }

                }
            }
        }
        //add => data proveedor a buena_pro

            for($i=0; $i< sizeof($buena_pro);$i++){
                for($j=0; $j< sizeof($proveedor_cotizacion);$j++){
                    if($buena_pro[$i]['id_proveedor'] === $proveedor_cotizacion[$j]['id_proveedor']){
                        $buena_pro[$i]['razon_social'] = $proveedor_cotizacion[$j]['razon_social'];
                        $buena_pro[$i]['nombre_doc_identidad'] = $proveedor_cotizacion[$j]['nombre_doc_identidad'];
                        $buena_pro[$i]['nro_documento'] = $proveedor_cotizacion[$j]['nro_documento'];
                    }
                }
            }
        

        //add => data empresa
        for($j=0; $j< sizeof($empresa_cotizacion);$j++){
            for($i=0; $i< sizeof($items);$i++){
                for($n=0; $n< sizeof($items[$i]['valorizacion']);$n++){
                    if($items[$i]['valorizacion'][$n]['id_cotizacion'] === $empresa_cotizacion[$j]['id_cotizacion']){
                        $items[$i]['valorizacion'][$n]['empresa']=$empresa_cotizacion[$j];
                    }
                }
            }
        }
        
        $result=['head'=>$head_cuadro[0], 'cuadro_comparativo'=>$items, 'buena_pro'=>$buena_pro];
        return $result;
    }

 

    public function mostrar_cuadro_comparativo($id_cotizacion){
        $cuadro_comparativo= $this->get_cuadro_comparativo($id_cotizacion);    
        return response()->json($cuadro_comparativo);
    }

    public function guardar_buena_pro(Request $request){
        $buena_pro = $request->all();
        $count_buena_pro=0;
        $data=0;
        if (is_array($buena_pro)) {
            $count_buena_pro = count($buena_pro);
            for ($i=0; $i< $count_buena_pro; $i++){
                $data = DB::table('logistica.log_valorizacion_cotizacion')->where('id_valorizacion_cotizacion', $buena_pro[$i]['id_valorizacion_cotizacion'])
                ->update([
                    'justificacion'      => $buena_pro[$i]['justificacion'], 
                    'estado'             => 2
                ]);
            } 
        }


        return response()->json($data);

    }



    public function get_orden($id_cotizacion){
        $data = DB::table('logistica.log_ord_compra')
        ->select(
                'log_ord_compra.codigo',
                'adm_tp_docum.descripcion AS tipo_documento',
                'adm_contri.razon_social AS razon_social_proveedor',
                'sis_identi.descripcion AS tipo_doc_proveedor',
                'adm_contri.nro_documento AS nro_documento_proveedor',
                'adm_contri.telefono AS telefono_proveedor',
                'adm_contri.direccion_fiscal AS direccion_fiscal_proveedor',
                'log_ord_compra.fecha AS fecha_orden',
                'log_cotizacion.id_empresa',
                'contab_contri.razon_social AS razon_social_empresa',
                'contab_sis_identi.descripcion AS tipo_doc_empresa',
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
                'log_valorizacion_cotizacion.id_detalle_requerimiento',
                'log_valorizacion_cotizacion.cantidad_cotizada',
                'log_valorizacion_cotizacion.precio_cotizado',
                'log_valorizacion_cotizacion.flete',
                'log_valorizacion_cotizacion.porcentaje_descuento',
                'log_valorizacion_cotizacion.monto_descuento',
                'log_valorizacion_cotizacion.subtotal',
                'log_valorizacion_cotizacion.plazo_entrega',
                'log_valorizacion_cotizacion.incluye_igv',
                'log_valorizacion_cotizacion.garantia',
                'log_valorizacion_cotizacion.lugar_despacho',

                'alm_det_req.descripcion_adicional AS descripcion_requerimiento',
                'alm_det_req.id_item',
                'alm_item.codigo AS codigo_item',
                'alm_prod.descripcion AS descripcion_producto',
                'alm_prod.codigo AS producto_codigo',
                'log_servi.codigo AS servicio_codigo',
                'log_servi.descripcion AS descripcion_servicio'
                )
                ->leftJoin('logistica.log_det_ord_compra','log_det_ord_compra.id_orden_compra','=','log_ord_compra.id_orden_compra')
                ->leftJoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','log_det_ord_compra.id_valorizacion_cotizacion')
                ->join('logistica.log_prove','log_prove.id_proveedor','=','log_ord_compra.id_proveedor')
                ->Join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
                ->Join('contabilidad.sis_identi','sis_identi.id_doc_identidad','=','adm_contri.id_doc_identidad')
                ->Join('administracion.adm_tp_docum','adm_tp_docum.id_tp_documento','=','log_ord_compra.id_tp_documento')
                ->leftJoin('logistica.log_cotizacion','log_cotizacion.id_cotizacion','=','log_ord_compra.id_cotizacion')
                ->Join('administracion.adm_empresa','adm_empresa.id_empresa','=','log_cotizacion.id_empresa')
                ->leftJoin('contabilidad.cont_tp_doc','cont_tp_doc.id_tp_doc','=','log_cotizacion.id_tp_doc')
                ->leftJoin('logistica.log_cdn_pago','log_cdn_pago.id_condicion_pago','=','log_cotizacion.id_condicion_pago')
                ->Join('contabilidad.adm_contri as contab_contri','contab_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
                ->Join('contabilidad.sis_identi as contab_sis_identi','contab_sis_identi.id_doc_identidad','=','contab_contri.id_doc_identidad')
                ->leftJoin('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','log_valorizacion_cotizacion.id_detalle_requerimiento')
                ->leftJoin('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
                ->leftJoin('almacen.alm_item','alm_item.id_item','=','alm_det_req.id_item')
                ->leftJoin('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
                ->leftJoin('logistica.log_servi','log_servi.id_servicio','=','alm_item.id_servicio')
        ->where([
            ['log_ord_compra.id_cotizacion', '=', $id_cotizacion],
            ['log_ord_compra.estado', '=', 1]
        ])
        ->get();

        $orden_header_orden=[];
        $orden_header_proveedor=[];
        $orden_header_empresa=[];
        $orden_condiciones=[];
        $valorizacion=[];

        foreach($data as $data){
            $orden_header_orden=[
                'codigo' => $data->codigo,
                'tipo_documento' => $data->tipo_documento,
                'fecha_orden' => $data->fecha_orden,
                'codigo_requerimiento' => $data->codigo_requerimiento
            ];
        
        $orden_header_proveedor=[
            'razon_social_proveedor'=> $data->razon_social_proveedor,
            'tipo_doc_proveedor'=> $data->tipo_doc_proveedor,
            'nro_documento_proveedor'=> $data->nro_documento_proveedor,
            'telefono_proveedor'=> $data->telefono_proveedor,
            'direccion_fiscal_proveedor'=> $data->direccion_fiscal_proveedor,
            'email_proveedor'=> $data->email_proveedor,
        ];   
        $orden_header_empresa=[
            'id_empresa'=> $data->id_empresa,
            'razon_social_empresa'=> $data->razon_social_empresa,
            'tipo_doc_empresa'=> $data->tipo_doc_empresa,
            'nro_documento_empresa'=> $data->nro_documento_empresa,
            'direccion_fiscal_empresa'=> $data->direccion_fiscal_empresa,
        ];   
        $orden_condiciones=[
            'tipo_doc_contable'=> $data->tipo_doc_contable,
            'condicion_pago'=> $data->condicion_pago,
            'condicion_credito_dias'=> $data->condicion_credito_dias,
            'nro_cuenta_principal'=> $data->nro_cuenta_principal,
            'nro_cuenta_alternativa'=> $data->nro_cuenta_alternativa,
            'nro_cuenta_detraccion'=> $data->nro_cuenta_detraccion,
        ];   

        $valorizacion[]=[
            'id_detalle_requerimiento'=> $data->id_detalle_requerimiento,
            'codigo_item'=> $data->codigo_item,
            'descripcion_producto'=>$data->descripcion_producto,
            'descripcion_requerimiento'=> $data->descripcion_requerimiento,
            'cantidad_cotizada'=> $data->cantidad_cotizada,
            'precio_cotizado'=> $data->precio_cotizado,
            'flete'=> $data->flete,
            'porcentaje_descuento'=> $data->porcentaje_descuento,
            'monto_descuento'=> $data->monto_descuento,
            'subtotal'=> $data->subtotal,
            'plazo_entrega'=> $data->plazo_entrega,
            'incluye_igv'=> $data->incluye_igv,
            'garantia'=> $data->garantia,
            'lugar_despacho'=> $data->lugar_despacho
        ];
    }
        $result=[
            'header_orden'=>$orden_header_orden,
            'header_proveedor'=>$orden_header_proveedor,
            'header_empresa'=>$orden_header_empresa,
            'condiciones'=>$orden_condiciones,
            'valorizacion'=>$valorizacion
        ];

        return $result;
    }
         
    public function mostrar_orden_por_cotizacion($codigo){
        $orden_por_cuadro_comparativo= $this->get_orden($codigo);    
        return response()->json($orden_por_cuadro_comparativo);
    }

    public function imprimir_orden_pdf($codigo){
        $ordenArray= $this->get_orden($codigo);    
        // $ordenArray = json_decode($orden, true);

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
                    background-color:#cc352a;
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
            </style>
            </head>
            <body>
                <img src="./images/LogoSlogan-80.png" alt="Logo" height="75px">
                <br>
                <hr>
               
                <h1><center>'.$ordenArray['header_orden']['tipo_documento'].'<br>'.$ordenArray['header_orden']['codigo'].'</center></h1>

                <table border="0">
                    <tr>
                        <td class="subtitle verticalTop">Sr.(s)</td>
                        <td class="subtitle verticalTop">:</td>
                        <td width="50%" class="verticalTop">'.$ordenArray['header_proveedor']['razon_social_proveedor'].'</td>
                        <td class="subtitle verticalTop">Fecha</td>
                        <td class="subtitle verticalTop">:</td>
                        <td>'.substr($ordenArray['header_orden']['fecha_orden'],0,11).'</td>
                    </tr>
             
                    <tr>
                        <td class="subtitle">Dirección</td>
                        <td class="subtitle verticalTop">:</td>
                        <td class="verticalTop">'.$ordenArray['header_proveedor']['direccion_fiscal_proveedor'].'</td>
                    </tr>
                    <tr>
                        <td class="subtitle">Telefono</td>
                        <td class="subtitle verticalTop">:</td>
                        <td class="verticalTop">'.$ordenArray['header_proveedor']['telefono_proveedor'].'</td>
                    </tr>
                    <tr>
                        <td class="subtitle">Contacto</td>
                        <td class="subtitle verticalTop">:</td>
                        <td class="verticalTop">'.$ordenArray['header_proveedor']['email_proveedor'].'</td>
                    </tr>
                </table>
                <br>

                <table width="80%" class="tablePDF" border=0>
                <thead>
                    <tr class="subtitle">
                        <td width="3%">#</td>
                        <td width="10%">Item</td>
                        <td width="20%">Descripcion</td>
                        <td width="5%">Undidad</td>
                        <td width="5%">Cantidad</td>
                        <td width="5%">Precio</td>
                        <td width="5%">IGV</td>
                        <td width="5%">Monto Descuento</td>
                        <td width="5%">% Descuento</td>
                        <td width="5%">Total</td>
                    </tr>   
                </thead>';
          
$total=0;
foreach($ordenArray['valorizacion'] as $key=>$data){
    $html .= '<tr>';
    $html .= '<td >'.($key+1).'</td>';
    $html .= '<td >'.$data['codigo_item'].'</td>';
    $html .= '<td >'.$data['descripcion_producto'].'</td>';
    $html .= '<td ></td>';
    $html .= '<td class="right">'.$data['cantidad_cotizada'].'</td>';
    $html .= '<td class="right">'.$data['precio_cotizado'].'</td>';
    $html .= '<td class="right">0</td>';
    $html .= '<td class="right">0</td>';
    $html .= '<td class="right">0</td>';
    $html .= '<td class="right">'.$data['cantidad_cotizada']*$data['precio_cotizado'].'</td>';
    $html .= '</tr>';
    $total = $total+ ($data['cantidad_cotizada']*$data['precio_cotizado']); 

}
            
    $html .= '

    <tr>
        <td  class="right" style="font-weight:bold;" colspan="9">TOTAL S/.</td>
        <td class="right">'.$total.'</td>
    </tr>
        </table>
<br>

<p class="subtitle">Condición de Compra</p>
<table border="0">
<tr>
    <td width="20%" class="verticalTop">Plazo Entrega</td>
    <td width="5%" class="verticalTop">:</td>
    <td width="70%" class="verticalTop"></td>
</tr>
<tr>
    <td width="20%"class="verticalTop">Forma de Pago</td>
    <td width="5%" class="verticalTop">:</td>
    <td width="70%" class="verticalTop">'.$ordenArray['condiciones']['condicion_pago'].'</td>
</tr>
<tr>
    <td width="20%"class="verticalTop">Req.</td>
    <td width="5%" class="verticalTop">:</td>
    <td width="70%" class="verticalTop">'.$ordenArray['header_orden']['codigo_requerimiento'].'</td>
</tr>
<br>
</table>
<p class="subtitle">Datos para Despacho</p>
<table border="0">
<tr>
    <td width="20%" class="verticalTop">Destino / Dirección</td>
    <td width="5%" class="verticalTop">:</td>
    <td width="70%" class="verticalTop"></td>
</tr>
<tr>
    <td width="20%"class="verticalTop">Atención / Personal Autorizado</td>
    <td width="5%" class="verticalTop">:</td>
    <td width="70%" class="verticalTop"></td>
</tr>
</table>
<br>
</table>
<p class="subtitle">Datos de Facturación</p>
<table border="0">
<tr>
    <td width="20%" class="verticalTop">Razon Social</td>
    <td width="5%" class="verticalTop">:</td>
    <td width="70%" class="verticalTop">'.$ordenArray['header_empresa']['razon_social_empresa'].'</td>
</tr>
<tr>
    <td width="20%"class="verticalTop">'.$ordenArray['header_empresa']['tipo_doc_empresa'].'</td>
    <td width="5%" class="verticalTop">:</td>
    <td width="70%" class="verticalTop">'.$ordenArray['header_empresa']['nro_documento_empresa'].'</td>
</tr>
<tr>
    <td width="20%"class="verticalTop">Dirección</td>
    <td width="5%" class="verticalTop">:</td>
    <td width="70%" class="verticalTop">'.$ordenArray['header_empresa']['direccion_fiscal_empresa'].'</td>
</tr>
</table>
            </body>
        </html>';

        return $html;

    }

    public function generar_orden_pdf($codigo){
      $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->imprimir_orden_pdf($codigo));
        return $pdf->stream();
        return $pdf->download('orden.pdf');
    }



    public function grupo_cotizaciones($codigo_cotiazacion,$codigo_cuadro_comparativo){

        
        $hasWhere=null;

        if($codigo_cotiazacion == "0"){
        $hasWhere=['log_grupo_cotizacion.codigo_grupo', '=', $codigo_cuadro_comparativo];

        }

        if($codigo_cuadro_comparativo == "0"){
            $grupo_cotizacion = DB::table('logistica.log_grupo_cotizacion')
            ->select( 
                    'log_grupo_cotizacion.id_grupo_cotizacion',                                 
                    'log_detalle_grupo_cotizacion.id_requerimiento'                                 
                    )
            ->leftJoin('logistica.log_detalle_grupo_cotizacion','log_detalle_grupo_cotizacion.id_grupo_cotizacion','=','log_grupo_cotizacion.id_grupo_cotizacion')
            ->leftJoin('almacen.alm_req','alm_req.id_requerimiento','=','log_detalle_grupo_cotizacion.id_requerimiento')
            ->leftJoin('logistica.log_cotizacion','log_cotizacion.id_cotizacion','=','log_detalle_grupo_cotizacion.id_cotizacion')
            ->where([['log_cotizacion.codigo_cotizacion', '=', $codigo_cotiazacion]])
            ->first();
         $hasWhere=['log_grupo_cotizacion.id_grupo_cotizacion', '=', $grupo_cotizacion->id_grupo_cotizacion];
        }

        $log_cotizacion = DB::table('logistica.log_cotizacion')
        ->select(
            'log_cotizacion.id_cotizacion',
            'log_grupo_cotizacion.codigo_grupo',
            'log_cotizacion.codigo_cotizacion',
            'cont_tp_doc.descripcion as tipo_documento',
            'log_cdn_pago.descripcion AS condicion_pago',
            'log_cotizacion.nro_cuenta_principal',
            'log_cotizacion.nro_cuenta_alternativa',
            'log_cotizacion.nro_cuenta_detraccion',
            'log_prove.id_proveedor',
            'adm_contri.razon_social',
            'adm_contri.nro_documento',
            'adm_contri.id_doc_identidad',
            'sis_identi.descripcion as nombre_doc_identidad',
            'alm_req.codigo AS codigo_req'
                )
        ->leftJoin('logistica.log_detalle_grupo_cotizacion','log_detalle_grupo_cotizacion.id_cotizacion','=','log_cotizacion.id_cotizacion')
        ->leftJoin('almacen.alm_req','alm_req.id_requerimiento','=','log_detalle_grupo_cotizacion.id_requerimiento')
        ->leftJoin('logistica.log_grupo_cotizacion','log_grupo_cotizacion.id_grupo_cotizacion','=','log_detalle_grupo_cotizacion.id_grupo_cotizacion')
        ->leftJoin('logistica.log_prove','log_prove.id_proveedor','=','log_cotizacion.id_proveedor')
        ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi','sis_identi.id_doc_identidad','=','adm_contri.id_doc_identidad')
        ->leftJoin('contabilidad.cont_tp_doc','cont_tp_doc.id_tp_doc','=','log_cotizacion.id_tp_doc')
        ->leftJoin('logistica.log_cdn_pago','log_cdn_pago.id_condicion_pago','=','log_cotizacion.id_condicion_pago')
        ->where(
                [['log_cotizacion.estado', '>', 0],
                $hasWhere
                ])
        ->get();


         if(sizeof($log_cotizacion)>0){
            foreach($log_cotizacion as $data){
                $cotizacionArray[] = [
                    
                    'id_cotizacion'=> $data->id_cotizacion,
                    'codigo_grupo'=> $data->codigo_grupo,
                    'codigo_cotizacion'=> $data->codigo_cotizacion,
                    'codigo_requerimiento'=> $data->codigo_req,
                    'tipo_documento'=>$data->tipo_documento,
                    'condicion_pago'=>$data->condicion_pago,
                    'nro_cuenta_principal'=>$data->nro_cuenta_principal,
                    'nro_cuenta_alternativa'=>$data->nro_cuenta_alternativa,
                    'nro_cuenta_detraccion'=>$data->nro_cuenta_detraccion,
                    'proveedor'=>[
                                    "id_proveedor"=>$data->id_proveedor,
                                    "razon_social"=>$data->razon_social,
                                    "nro_documento"=>$data->nro_documento,
                                    "id_doc_identidad"=>$data->id_doc_identidad,
                                    "nombre_doc_identidad"=>$data->nombre_doc_identidad
                                    ]
                ];
     

            }

        }else{
             $cotizacionArray=[];
            return response()->json($cotizacionArray);

        }
         return response()->json($cotizacionArray);

    }



    public function listar_partidas($id_grupo){
        $presup = DB::table('finanzas.presup')
        ->where([['id_grupo','=',$id_grupo],
                ['estado','=',1]])
        ->get();

        $html = '';
        foreach($presup as $p){
            $titulos = DB::table('finanzas.presup_titu')
                ->where([['id_presup','=',$p->id_presup],
                        ['estado','=',1]])
                ->orderBy('presup_titu.codigo')
                ->get();
            $partidas = DB::table('finanzas.presup_par')
                ->select('presup_par.*','presup_pardet.descripcion as des_pardet')
                ->join('finanzas.presup_pardet','presup_pardet.id_pardet','=','presup_par.id_pardet')
                ->where([['presup_par.id_presup','=',$p->id_presup],
                        ['presup_par.estado','=',1]])
                ->orderBy('presup_par.codigo')
                ->get();
            $html .='
            <div id='.$p->codigo.' class="panel panel-primary" style="width:100%;">
                <h5 onclick="apertura('.$p->id_presup.');" class="panel-heading" style="cursor: pointer; margin: 0;">
                '.$p->descripcion.' </h5>
                <div id="pres-'.$p->id_presup.'" class="" style="width:100%;">
                    <table class="table table-bordered table-sm table-okc-cc" width="100%">
                        <tbody id="clickEvent"> 
                ';
                foreach($titulos as $ti){
                    $html .='
                    <tr id="com-'.$ti->id_titulo.'">
                        <td><strong>'.$ti->codigo.'</strong></td>
                        <td><strong>'.$ti->descripcion.'</strong></td>
                        <td class="right"><strong>'.$ti->total.'</strong></td>
                    </tr>';
                    foreach($partidas as $par){
                        if ($ti->codigo == $par->cod_padre){
                            $html .='
                            <tr id="par-'.$par->id_partida.' style="cursor: pointer; margin: 0;">
                                <td hidden>'.$par->id_partida.'</td>
                                <td name="codigo">'.$par->codigo.'</td>
                                <td name="descripcion">'.$par->des_pardet.'</td>
                                <td class="right">'.$par->importe_total.'</td>
                            </tr>';
                        }
                    }
                }
            $html .='
                    </tbody>
                </table>
            </div>
        </div>';
        }
        return json_encode($html);
    }

        }


