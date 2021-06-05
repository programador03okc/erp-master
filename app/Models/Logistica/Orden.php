<?php


namespace App\Models\Logistica;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpParser\Node\Expr\New_;

class Orden extends Model {

    protected $table = 'logistica.log_ord_compra';
    protected $primaryKey = 'id_orden_compra';
    public $timestamps = false;


    public function mostrar()
    {
        $data = Orden::select('log_ord_compra.*')
		->where('log_ord_compra.estado',1)
		->get();
		return $data;
    }


    public static function reporteListaOrdenes(){
        $ord_compra = Orden::select(
            'log_ord_compra.*',
            'sis_sede.descripcion as descripcion_sede_empresa',
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
            'log_ord_compra.estado',
            'log_ord_compra_pago.id_pago',
            'log_ord_compra_pago.detalle_pago',
            'log_ord_compra_pago.archivo_adjunto',
            DB::raw("(SELECT  coalesce(sum((log_det_ord_compra.cantidad * log_det_ord_compra.precio))*1.18 ,0) AS suma_subtotal
            FROM logistica.log_det_ord_compra 
            WHERE   log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra AND
                    log_det_ord_compra.estado != 7) AS suma_subtotal")
            // DB::raw("( 
            
            //     SELECT array_agg(concat(doc_com.serie, '-', doc_com.numero)) AS facturas
            //     FROM logistica.log_det_ord_compra
            //     INNER JOIN almacen.guia_com_det on guia_com_det.id_oc_det = log_det_ord_compra.id_detalle_orden
            //     INNER JOIN almacen.doc_com_det on doc_com_det.id_guia_com_det = guia_com_det.id_guia_com_det
            //     INNER JOIN almacen.doc_com on doc_com.id_doc_com = doc_com_det.id_doc
            //     WHERE 
            //     log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra 
            //     AND log_det_ord_compra.id_detalle_orden = guia_com_det.id_oc_det 
            //     AND log_det_ord_compra.estado != 7 
            //     LIMIT 1 ) as facturas
            //     ")
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
        ->orderBy('log_ord_compra.fecha','desc')
        ->get();

        $data_orden=[];
        if(count($ord_compra)>0){
            foreach($ord_compra as $element){



                $data_orden[]=[
                    'id_orden_compra'=> $element->id_orden_compra,
                    'id_tp_documento'=> $element->id_tp_documento,
                    'fecha' => date_format(date_create($element->fecha),'Y-m-d'), 
                    // 'fecha' => $element->fecha, 
                    'codigo'=> $element->codigo,
                    'descripcion_sede_empresa'=> $element->descripcion_sede_empresa,
                    'nro_documento'=> $element->nro_documento, 
                    'razon_social'=> $element->razon_social,
                    'moneda_simbolo'=> $element->moneda_simbolo, 
                    'incluye_igv'=> $element->incluye_igv,
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
                    'facturas'=> implode(',',Orden::obtenerFacturas($element->id_orden_compra)),
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
                    'fecha_ingreso_almacen'=>date_format(date_create($element->fecha_ingreso_almacen),'Y-m-d'),
                    'estado_aprobacion'=> $element->estado_aprobacion,
                    'fecha_estado'=> $element->fecha_estado,
                    'fecha_registro_requerimiento'=> date_format(date_create($element->fecha_registro_requerimiento),'Y-m-d')
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

        $data=[];
        foreach($data_orden as $d){
            $fechaHoy =Carbon::now();
            $fechaOrden = Carbon::create($d['fecha']);
            $fechaLlegada= Carbon::create($d['fecha'])->addDays($d['plazo_entrega']);
            $diasRestantes = $fechaLlegada->diffInDays($fechaHoy);

            // $fechaRegistroRequerimiento = new Carbon($d['fecha_registro_requerimiento']);
            // $fechaRegistroAlmacen=  new Carbon($d['fecha_ingreso_almacen']);

            $fechaRegistroRequerimiento = $d['fecha_registro_requerimiento'];
            $fechaRegistroAlmacen = $d['fecha_ingreso_almacen'];

          
            $tiempo_atencion_logistica =$fechaOrden->diffInDays($fechaRegistroRequerimiento);
            $tiempo_atencion_almacen = $fechaOrden->diffInDays($fechaRegistroAlmacen);
            $data[]=[
                'codigo_cuadro_costos'=>$d['codigo_oportunidad'],
                'proveedor'=>$d['razon_social'],
                'codigo_orden'=>$d['codigo'],
                'codigo_requerimiento_o_codigo_cuadro_comparativo'=>$d['codigo_requerimiento']?$d['codigo_requerimiento']:$d['codigo_cuadro_comparativo'],
                'estado'=>$d['estado_doc'],
                'fecha_vencimiento'=>$d['fecha_vencimiento_ocam'],
                'fecha_llegada'=>$fechaRegistroAlmacen,
                'estado_aprobacion_cc'=>$d['estado_aprobacion_cc'],
                'fecha_estado'=>$d['fecha_estado'],
                'fecha_registro_requerimiento'=>$fechaRegistroRequerimiento,
                'leadtime'=>$fechaLlegada->toDateString().' (días restantes: '.$diasRestantes.')',
                'empresa_sede'=>$d['descripcion_sede_empresa'],
                'moneda'=>$d['moneda_simbolo'],
                'condicion'=>$d['condicion'],
                'fecha_orden'=>$d['fecha'],
                'tiempo_atencion_logistica'=>isset($tiempo_atencion_logistica)?$tiempo_atencion_logistica:'',
                'tiempo_atencion_almacen'=>isset($tiempo_atencion_almacen)?$tiempo_atencion_almacen:'',
                'facturas'=>$fechaOrden,
                'detalle_pago'=>$d['detalle_pago']
            ];
        }

        return $data;
    }


    public static function obtenerFacturas($idOrden){
        $facturas=[];
        $sql_facturas = DB::table('logistica.log_det_ord_compra')
        ->select(DB::raw("concat(doc_com.serie, '-', doc_com.numero) AS facturas"))
        ->join('almacen.guia_com_det', 'guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden')
        ->leftjoin('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', '=', 'guia_com_det.id_guia_com_det')
        ->leftjoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'doc_com_det.id_doc')
        ->where('log_det_ord_compra.id_orden_compra',$idOrden)
        ->get();
        if(count($sql_facturas)>0){
            foreach ($sql_facturas as $value) {
                $facturas[]=$value->facturas;
            }

        }
        return array_values(array_unique($facturas));
    }


}
