<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Dompdf\Dompdf;
use PDF;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
date_default_timezone_set('America/Lima');

class DistribucionController extends Controller
{
    public function __construct(){
        // session_start();
    }
    function view_ordenesDespacho(){
        $usuarios = AlmacenController::select_usuarios();
        return view('almacen/distribucion/ordenesDespacho', compact('usuarios'));
    }
    function view_grupoDespachos(){
        // $usuarios = AlmacenController::select_usuarios();
        return view('almacen/distribucion/grupoDespachos');
    }

    public function listarRequerimientosPendientes(){
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*','sis_usua.nombre_corto','adm_grupo.descripcion as grupo',
            'adm_grupo.id_sede')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->join('administracion.adm_grupo','adm_grupo.id_grupo','=','alm_req.id_grupo')
            ->where('alm_req.estado',5)
            ->get();
            $output['data'] = $data;
        return response()->json($output);
    }

    public function verDetalleRequerimiento($id_requerimiento){
        $detalles = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.*','alm_almacen.descripcion as almacen_descripcion',
            DB::raw("(CASE 
                    WHEN alm_item.id_item isNUll THEN alm_det_req.descripcion_adicional 
                    WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
                    WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
                    WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
                    ELSE 'nulo' END) AS descripcion_item
                    "),
                DB::raw("(CASE 
                    WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.codigo 
                    WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.codigo 
                    WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.codigo 
                    ELSE 'nulo' END) AS codigo_item
                    "),
                DB::raw("(CASE 
                    WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.abreviatura
                    WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'serv' 
                    WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'und' 
                    ELSE 'nulo' END) AS unidad_medida_item
                    "),
                    'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
            ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
            ->leftJoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_det_req.id_almacen')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_det_req.estado')
            // ->leftJoin('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')
            ->where([['alm_det_req.id_requerimiento','=',$id_requerimiento],['alm_det_req.estado','!=',7]])
            ->get();

            $data = [];

            foreach ($detalles as $det) {
                $valori = DB::table('logistica.valoriza_coti_detalle')
                ->select('log_ord_compra.id_orden_compra','log_ord_compra.codigo as codigo_orden','log_det_ord_compra.lugar_despacho',
                'guia_com.id_guia','guia_com.serie','guia_com.numero','adm_contri.razon_social','adm_contri.nro_documento',
                'guia_com_det.id_producto','guia_com_det.id_posicion','alm_prod.codigo as codigo_producto',
                'alm_prod.descripcion as descripcion_producto','alm_ubi_posicion.codigo as codigo_posicion',
                'alm_und_medida.abreviatura')
                // ->leftJoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','valoriza_coti_detalle.id_valorizacion_cotizacion')
                ->join('logistica.log_det_ord_compra','log_det_ord_compra.id_valorizacion_cotizacion','=','valoriza_coti_detalle.id_valorizacion_cotizacion')
                ->leftJoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
                ->leftJoin('logistica.log_prove','log_prove.id_proveedor','=','log_ord_compra.id_proveedor')
                ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
                ->leftJoin('almacen.guia_com_det','guia_com_det.id_oc_det','=','log_det_ord_compra.id_detalle_orden')
                ->leftJoin('almacen.alm_prod','alm_prod.id_producto','=','guia_com_det.id_producto')
                ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
                ->leftJoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','guia_com_det.id_posicion')
                ->leftJoin('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
                ->where([
                         ['valoriza_coti_detalle.estado','!=',7],
                         ['valoriza_coti_detalle.id_detalle_requerimiento','=',$det->id_detalle_requerimiento],
                        //  ['log_valorizacion_cotizacion.estado','=',5],//cotizacion ganadora
                         ['log_det_ord_compra.estado','!=',7],
                         ['guia_com_det.estado','!=',7]
                         ])
                ->get();

                $nuevo = [
                    'id_detalle_requerimiento'=>$det->id_detalle_requerimiento,
                    'id_requerimiento'=>$det->id_requerimiento,
                    'codigo_item'=>$det->codigo_item,
                    'descripcion_item'=>$det->descripcion_item,
                    'cantidad'=>$det->cantidad,
                    'unidad_medida_item'=>$det->unidad_medida_item,
                    'lugar_entrega'=>$det->lugar_entrega,
                    'estado_doc'=>$det->estado_doc,
                    'bootstrap_color'=>$det->bootstrap_color,
                    'id_almacen'=>$det->id_almacen,
                    'almacen_descripcion'=>$det->almacen_descripcion,
                    'stock_comprometido'=>$det->stock_comprometido,
                    'codigo_producto'=>($valori[0]->codigo_producto !== null ? $valori[0]->codigo_producto : ''),
                    'descripcion_producto'=>($valori[0]->descripcion_producto !== null ? $valori[0]->descripcion_producto : ''),
                    'codigo_posicion'=>($valori[0]->codigo_posicion !== null ? $valori[0]->codigo_posicion : ''),
                    'id_producto'=>($valori[0]->id_producto !== null ? $valori[0]->id_producto : ''),
                    'id_posicion'=>($valori[0]->id_posicion !== null ? $valori[0]->id_posicion : ''),
                    'unidad_medida_producto'=>($valori[0]->abreviatura !== null ? $valori[0]->abreviatura : ''),
                    'lugar_despacho_orden'=>($valori[0]->lugar_despacho !== null ? $valori[0]->lugar_despacho : '')
                    // 'valorizaciones'=>$valori
                ];
                array_push($data, $nuevo);
            }
        return response()->json($data);
    }

    public function guardar_orden_despacho(Request $request){
        $codigo = $this->ODnextId($request->fecha_despacho,$request->id_sede);
        $usuario = Auth::user()->id_usuario;

        $id_od = DB::table('almacen.orden_despacho')
            ->insertGetId([
                'id_sede'=>$request->id_sede,
                'id_requerimiento'=>$request->id_requerimiento,
                'id_cliente'=>$request->id_cliente,
                'codigo'=>$codigo,
                'ubigeo_destino'=>$request->ubigeo,
                'direccion_destino'=>$request->direccion_destino,
                'fecha_despacho'=>$request->fecha_despacho,
                'fecha_entrega'=>$request->fecha_entrega,
                'aplica_cambios'=>($request->aplica_cambios_valor == 'si' ? true : false),
                'registrado_por'=>$usuario,
                'tipo_entrega'=>$request->tipo_entrega,
                'fecha_registro'=>date('Y-m-d H:i:s'),
                'estado'=>1
            ],
                'id_od'
        );

        if ($request->aplica_cambios_valor == 'si'){
            $fecha_actual = date('Y-m-d');
            $codTrans = $this->transformacion_nextId($fecha_actual);

            $id_transformacion = DB::table('almacen.transformacion')
                ->insertGetId([
                    'fecha_transformacion'=>$fecha_actual,
                    'codigo'=>$codTrans,
                    // 'responsable'=>,
                    'id_moneda'=>1,
                    // 'id_almacen'=>,
                    'total_materias'=>0,
                    'total_directos'=>0,
                    'costo_primo'=>0,
                    'total_indirectos'=>0,
                    'total_sobrantes'=>0,
                    'costo_transformacion'=>0,
                    'registrado_por'=>$usuario,
                    'tipo_cambio'=>1,
                    'fecha_registro'=>date('Y-m-d H:i:s'),
                    'estado'=>1,
                    'observacion'=>'SALE: '.$request->sale
                ],
                    'id_transformacion'
            );

            
            $data = json_decode($request->detalle_ingresa);
            
            foreach ($data as $d) {
                DB::table('almacen.transfor_materia')
                ->insert([
                    'id_transformacion'=>$id_transformacion,
                    'id_producto'=>$d->id_producto,
                    'id_posicion'=>$d->id_posicion,
                    'cantidad'=>$d->cantidad,
                    'valor_unitario'=>0,
                    'valor_total'=>0,
                    'estado'=>1,
                    'fecha_registro'=>date('Y-m-d H:i:s')
                ]);
            }
        }
        else {
            $data = json_decode($request->detalle_requerimiento);
            
            foreach ($data as $d) {
                DB::table('almacen.orden_despacho_det')
                ->insert([
                    'id_od'=>$id_od,
                    'id_producto'=>$d->id_producto,
                    'id_posicion'=>$d->id_posicion,
                    'cantidad'=>$d->cantidad,
                    'descripcion_producto'=>($d->descripcion_item !== null ? $d->descripcion_item : $d->descripcion_producto),
                    'estado'=>1,
                    'fecha_registro'=>date('Y-m-d H:i:s')
                ]);
            }
        }
        return response()->json($id_od);
    }

    public function listarOrdenesDespacho(){
        $data = DB::table('almacen.orden_despacho')
        ->select('orden_despacho.*','adm_contri.nro_documento','adm_contri.razon_social',
        'alm_req.codigo as codigo_req','alm_req.concepto','ubi_dis.descripcion as ubigeo_descripcion',
        'sis_usua.nombre_corto','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
        ->join('comercial.com_cliente','com_cliente.id_cliente','=','orden_despacho.id_cliente')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->join('configuracion.ubi_dis','ubi_dis.id_dis','=','orden_despacho.ubigeo_destino')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','orden_despacho.registrado_por')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','orden_despacho.estado')
        ->where([['orden_despacho.estado','!=',7]])
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function verDetalleDespacho($id_od){
        $data = DB::table('almacen.orden_despacho_det')
        ->select('orden_despacho_det.*','alm_prod.codigo','alm_prod.descripcion',
        'alm_ubi_posicion.codigo as posicion','alm_und_medida.abreviatura')
        ->leftJoin('almacen.alm_prod','alm_prod.id_producto','=','orden_despacho_det.id_producto')
        ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
        ->leftJoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','orden_despacho_det.id_posicion')
        ->where([['orden_despacho_det.id_od','=',$id_od],['orden_despacho_det.estado','!=',7]])
        ->get();
        return response()->json($data);
    }

    public function guardar_grupo_despacho(Request $request){
        $codigo = $this->grupoODnextId($request->fecha_despacho,$request->id_sede);

        $id_od_grupo = DB::table('almacen.orden_despacho_grupo')
        ->insertGetId([
            'codigo'=>$codigo,
            'id_sede'=>$request->id_sede,
            'fecha_despacho'=>$request->fecha_despacho,
            'responsable'=>$request->responsable,
            'estado'=>1,
            'fecha_registro'=>date('Y-m-d H:i:s')
            ],
            'id_od_grupo'
        );
        $data = json_decode($request->ordenes_despacho);
        
        foreach ($data as $d) {
            DB::table('almacen.orden_despacho_grupo_det')
            ->insert([
                'id_od_grupo'=>$id_od_grupo,
                'id_od'=>$d->id_od,
                'estado'=>1,
                'fecha_registro'=>date('Y-m-d H:i:s')
            ]);
        }
        return response()->json($id_od_grupo);
    }

    public function listarGruposDespachados(){
        $data = DB::table('almacen.orden_despacho_grupo')
        ->select('orden_despacho_grupo.*','sis_usua.nombre_corto',
        'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','orden_despacho_grupo.responsable')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','orden_despacho_grupo.estado')
        ->where([['orden_despacho_grupo.estado','!=',7]])
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function verDetalleGrupoDespacho($id_od_grupo){
        $data = DB::table('almacen.orden_despacho_grupo_det')
        ->select('orden_despacho_grupo_det.*','orden_despacho.codigo','orden_despacho.direccion_destino',
        'orden_despacho.fecha_despacho','orden_despacho.fecha_entrega','adm_contri.nro_documento',
        'adm_contri.razon_social','alm_req.codigo as codigo_req','alm_req.concepto',
        'ubi_dis.descripcion as ubigeo_descripcion','sis_usua.nombre_corto','adm_estado_doc.estado_doc',
        'adm_estado_doc.bootstrap_color')
        ->join('almacen.orden_despacho','orden_despacho.id_od','=','orden_despacho_grupo_det.id_od')
        ->join('comercial.com_cliente','com_cliente.id_cliente','=','orden_despacho.id_cliente')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->join('configuracion.ubi_dis','ubi_dis.id_dis','=','orden_despacho.ubigeo_destino')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','orden_despacho.registrado_por')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','orden_despacho.estado')
        ->where([['orden_despacho_grupo_det.estado','!=',7]])
        ->get();
        return response()->json($data);
    }

    public function ODnextId($fecha_despacho,$id_sede){
        $yyyy = date('Y',strtotime($fecha_despacho));
        
        $cantidad = DB::table('almacen.orden_despacho')
        ->whereYear('fecha_despacho','=',$yyyy)
        ->where([['id_sede','=',$id_sede],['estado','!=',7]])
        ->get()->count();

        $val = AlmacenController::leftZero(3,($cantidad + 1));
        $nextId = "OD-".$yyyy."-".$val;
        return $nextId;
    }

    public function grupoODnextId($fecha_despacho,$id_sede){
        $yyyy = date('Y',strtotime($fecha_despacho));
        
        $cantidad = DB::table('almacen.orden_despacho_grupo')
        ->whereYear('fecha_despacho','=',$yyyy)
        ->where([['id_sede','=',$id_sede],['estado','!=',7]])
        ->get()->count();

        $val = AlmacenController::leftZero(3,($cantidad + 1));
        $nextId = "D-".$yyyy."-".$val;
        return $nextId;
    }

    public function transformacion_nextId($fecha_transformacion){
        $yyyy = date('Y',strtotime($fecha_transformacion));
        
        $cantidad = DB::table('almacen.transformacion')
        ->whereYear('fecha_transformacion','=',$yyyy)
        ->where([['estado','!=',7]])
        ->get()->count();

        $val = AlmacenController::leftZero(3,($cantidad + 1));
        $nextId = "TF-".$yyyy."-".$val;
        return $nextId;
    }
}
