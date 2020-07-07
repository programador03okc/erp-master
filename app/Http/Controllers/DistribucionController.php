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
        $sis_identidad = AlmacenController::sis_identidad_cbo();
        return view('almacen/distribucion/ordenesDespacho', compact('usuarios','sis_identidad'));
    }
    function view_despachosPendientes(){
        $tp_operacion = AlmacenController::tp_operacion_cbo_sal();
        $clasificaciones = AlmacenController::mostrar_guia_clas_cbo();
        $usuarios = AlmacenController::select_usuarios();
        $motivos_anu = AlmacenController::select_motivo_anu();
        return view('almacen/guias/despachosPendientes', compact('tp_operacion','clasificaciones','usuarios','motivos_anu'));
    }
    function view_requerimientoPagos(){
        // $usuarios = AlmacenController::select_usuarios();
        return view('almacen/pagos/requerimientoPagos');
    }
    function view_trazabilidad_requerimientos(){
        return view('almacen/distribucion/trazabilidadRequerimientos');
    }

    public function listarRequerimientosPendientes(){
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*','sis_usua.nombre_corto as responsable','adm_grupo.descripcion as grupo',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            'log_ord_compra.codigo as codigo_orden','guia_com.serie','guia_com.numero',
            'trans.id_transferencia','trans.codigo as codigo_transferencia','ubi_dis.descripcion as ubigeo_descripcion',
            'rrhh_perso.nro_documento as dni_persona','alm_almacen.descripcion as almacen_descripcion',
            'alm_almacen.id_sede as sede_requerimiento','log_ord_compra.id_sede as sede_orden',
            'sis_sede.descripcion as sede_descripcion_orden',
            'orden_despacho.id_od','orden_despacho.codigo as codigo_od','orden_despacho.estado as estado_od',
            'alm_tp_req.descripcion as tipo_req',
            DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
            'adm_contri.nro_documento as cliente_ruc','adm_contri.razon_social as cliente_razon_social')
            ->join('almacen.alm_tp_req','alm_tp_req.id_tipo_requerimiento','=','alm_req.id_tipo_requerimiento')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->leftjoin('administracion.adm_grupo','adm_grupo.id_grupo','=','alm_req.id_grupo')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
            ->leftJoin('logistica.log_ord_compra', function($join)
                         {   $join->on('log_ord_compra.id_requerimiento', '=', 'alm_req.id_requerimiento');
                             $join->where('log_ord_compra.estado','!=', 7);
                         })
            ->leftJoin('administracion.sis_sede','sis_sede.id_sede','=','log_ord_compra.id_sede')
            // ->leftJoin('almacen.guia_com','guia_com.id_oc','=','log_ord_compra.id_orden_compra')
            // ->leftJoin('almacen.mov_alm','mov_alm.id_guia_com','=','guia_com.id_guia')
            ->leftJoin('almacen.guia_com', function($join)
                         {   $join->on('guia_com.id_oc', '=', 'log_ord_compra.id_orden_compra');
                             $join->where('guia_com.estado','!=', 7);
                         })
            ->leftJoin('almacen.guia_ven', function($join)
                         {   $join->on('guia_ven.id_guia_com', '=', 'guia_com.id_guia');
                             $join->where('guia_ven.estado','!=', 7);
                         })
            // ->leftJoin('almacen.guia_ven','guia_ven.id_guia_com','=','guia_com.id_guia')
            ->leftJoin('almacen.trans', function($join)
                         {   $join->on('trans.id_guia_ven', '=', 'guia_ven.id_guia_ven');
                             $join->where('trans.estado','!=', 7);
                         })
            // ->leftJoin('almacen.trans','trans.id_guia_ven','=','guia_ven.id_guia_ven')
            ->leftJoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_req.id_almacen')
            ->leftJoin('configuracion.ubi_dis','ubi_dis.id_dis','=','alm_req.id_ubigeo_entrega')
            ->leftJoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','alm_req.id_persona')
            ->leftJoin('comercial.com_cliente','com_cliente.id_cliente','=','alm_req.id_cliente')
            ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->leftJoin('almacen.orden_despacho', function($join)
                         {   $join->on('orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento');
                             $join->where('orden_despacho.estado','!=', 7);
                         })
            // ->leftJoin('almacen.orden_despacho','orden_despacho.id_requerimiento','=','alm_req.id_requerimiento')
            ->where([['alm_req.estado','!=',1], ['alm_req.estado','!=',7], ['alm_req.estado','!=',20], 
            ['alm_req.estado','!=',21]])//muestra todos los reservados
            ->orderBy('alm_req.fecha_requerimiento','desc');
            // ->get();
        return datatables($data)->toJson();
        // return response()->json($data);
    }

    public function getEstadosRequerimientos(){
        $data = DB::table('almacen.alm_req')
        ->select('alm_req.estado','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            DB::raw('count(alm_req.id_requerimiento) as cantidad'))
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
        ->groupBy('alm_req.estado','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
            ->where([['alm_req.estado','!=',7]])
            ->orderBy('alm_req.estado','desc')
            ->get();
        return response()->json($data);
    }

    public function listarEstadosRequerimientos($estado){
        $data = DB::table('almacen.alm_req')
        ->select('alm_req.id_requerimiento','alm_req.codigo','alm_req.concepto','sis_usua.nombre_corto')
            // ->join('almacen.alm_tp_req','alm_tp_req.id_tipo_requerimiento','=','alm_req.id_tipo_requerimiento')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->where([['alm_req.estado','=',$estado]])
            ->get();
        return response()->json($data);
    }

    public function listarRequerimientosPendientesPagos(Request $request){
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*','sis_usua.nombre_corto as responsable','adm_grupo.descripcion as grupo',
            'adm_grupo.id_sede','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            'ubi_dis.descripcion as ubigeo_descripcion',
            'rrhh_perso.nro_documento as dni_persona','alm_almacen.descripcion as almacen_descripcion',
            'alm_almacen.id_sede as sede_almacen',
            'alm_tp_req.descripcion as tipo_req','sis_moneda.simbolo',
            DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
            'adm_contri.nro_documento as cliente_ruc','adm_contri.razon_social as cliente_razon_social')
            ->join('almacen.alm_tp_req','alm_tp_req.id_tipo_requerimiento','=','alm_req.id_tipo_requerimiento')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->leftjoin('administracion.adm_grupo','adm_grupo.id_grupo','=','alm_req.id_grupo')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
            ->leftJoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_req.id_almacen')
            ->leftJoin('configuracion.ubi_dis','ubi_dis.id_dis','=','alm_req.id_ubigeo_entrega')
            ->leftJoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','alm_req.id_persona')
            ->leftJoin('comercial.com_cliente','com_cliente.id_cliente','=','alm_req.id_cliente')
            ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->leftJoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','alm_req.id_moneda')
            ->where([['alm_req.estado','=',1],['alm_req.confirmacion_pago','=',false]])
            ->orWhere([['alm_req.estado','=',19],['alm_req.id_tipo_requerimiento','=',2],['alm_req.confirmacion_pago','=',false]]);//muestra todos los reservados
            // ->get();
        return datatables($data)->toJson();
    }

    public function listarRequerimientosConfirmadosPagos(Request $request){
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*','sis_usua.nombre_corto as responsable','adm_grupo.descripcion as grupo',
            'adm_grupo.id_sede','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            'ubi_dis.descripcion as ubigeo_descripcion',
            'rrhh_perso.nro_documento as dni_persona','alm_almacen.descripcion as almacen_descripcion',
            'alm_almacen.id_sede as sede_almacen',
            'alm_tp_req.descripcion as tipo_req',
            DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
            'adm_contri.nro_documento as cliente_ruc','adm_contri.razon_social as cliente_razon_social')
            ->join('almacen.alm_tp_req','alm_tp_req.id_tipo_requerimiento','=','alm_req.id_tipo_requerimiento')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->leftjoin('administracion.adm_grupo','adm_grupo.id_grupo','=','alm_req.id_grupo')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
            ->leftJoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_req.id_almacen')
            ->leftJoin('configuracion.ubi_dis','ubi_dis.id_dis','=','alm_req.id_ubigeo_entrega')
            ->leftJoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','alm_req.id_persona')
            ->leftJoin('comercial.com_cliente','com_cliente.id_cliente','=','alm_req.id_cliente')
            ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->where([['alm_req.estado','=',1],['alm_req.id_tipo_requerimiento','=',1],['alm_req.confirmacion_pago','=',true]])
            ->orWhere([['alm_req.estado','=',19],['alm_req.id_tipo_requerimiento','=',2],['alm_req.confirmacion_pago','=',true]])
            ->orWhere([['alm_req.estado','=',7],['alm_req.confirmacion_pago','=',false],['alm_req.obs_confirmacion','!=',null]]);
            // ->get();
        return datatables($data)->toJson();
    }

    public function verRequerimientosReservados($id,$almacen){
        $detalles = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.*','alm_req.codigo','alm_req.concepto','sis_usua.nombre_corto')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->where([['alm_det_req.id_producto','=',$id],
                     ['alm_det_req.id_almacen_reserva','=',$almacen],
                     ['alm_det_req.estado','=',19]])
            ->get();
        return response()->json($detalles);
    }

    public function verDetalleRequerimiento($id_requerimiento){
        $detalles = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.*','alm_almacen.descripcion as almacen_descripcion',
                    'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
                    'alm_prod.descripcion as producto_descripcion','alm_prod.codigo as producto_codigo',
                    'alm_und_medida.abreviatura')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
            // ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            ->leftJoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_det_req.id_almacen')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_det_req.estado')
            ->where([['alm_det_req.id_requerimiento','=',$id_requerimiento],['alm_det_req.estado','!=',7]])
            ->get();
        return response()->json($detalles);
    }

    public function verDetalleIngreso($id_requerimiento){
        $data = DB::table('almacen.mov_alm_det')
        ->select('mov_alm_det.*','alm_prod.codigo as codigo_producto',
        'alm_prod.descripcion as producto_descripcion','alm_und_medida.abreviatura as unidad_producto')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
        ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
        ->join('almacen.guia_com','guia_com.id_guia','=','mov_alm.id_guia_com')
        ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','guia_com.id_oc')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','log_ord_compra.id_requerimiento')
        ->where([['log_ord_compra.id_requerimiento','=',$id_requerimiento]])
        ->get();
        return response()->json($data);
    }

    public function guardar_orden_despacho(Request $request){

        try {
            DB::beginTransaction();

            $codigo = $this->ODnextId($request->fecha_despacho,$request->id_sede);
            $usuario = Auth::user()->id_usuario;

            $id_od = DB::table('almacen.orden_despacho')
                ->insertGetId([
                    'id_sede'=>$request->id_sede,
                    'id_requerimiento'=>$request->id_requerimiento,
                    'id_cliente'=>$request->id_cliente,
                    'id_persona'=>$request->id_persona,
                    'id_almacen'=>$request->id_almacen,
                    'telefono'=>$request->telefono,
                    'codigo'=>$codigo,
                    'ubigeo_destino'=>$request->ubigeo,
                    'direccion_destino'=>$request->direccion_destino,
                    'fecha_despacho'=>$request->fecha_despacho,
                    'fecha_entrega'=>$request->fecha_entrega,
                    'aplica_cambios'=>($request->aplica_cambios_valor == 'si' ? true : false),
                    'registrado_por'=>$usuario,
                    'tipo_entrega'=>$request->tipo_entrega,
                    'fecha_registro'=>date('Y-m-d H:i:s'),
                    'estado'=>1,
                    'tipo_cliente'=>$request->tipo_cliente
                ],
                    'id_od'
            );

            //Agrega accion en requerimiento
            DB::table('almacen.alm_req_obs')
            ->insert([  'id_requerimiento'=>$request->id_requerimiento,
                        'accion'=>'ORDEN DE DESPACHO',
                        'descripcion'=>'Se generó la Orden de Despacho '.$codigo,
                        'id_usuario'=>$usuario,
                        'fecha_registro'=>date('Y-m-d H:i:s')
                ]);

            if ($request->aplica_cambios_valor == 'si'){
                $fecha_actual = date('Y-m-d');
                $codTrans = $this->transformacion_nextId($fecha_actual);

                $id_transformacion = DB::table('almacen.transformacion')
                    ->insertGetId([
                        'fecha_transformacion'=>$fecha_actual,
                        'codigo'=>$codTrans,
                        // 'responsable'=>$usuario,
                        'id_moneda'=>1,
                        'id_almacen'=>$request->id_almacen,
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
                        // 'id_posicion'=>$d->id_posicion,
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
                        // 'id_posicion'=>($d->id_posicion),
                        'cantidad'=>$d->cantidad,
                        'descripcion_producto'=>($d->producto_descripcion !== null ? $d->producto_descripcion : $d->descripcion_adicional),
                        'estado'=>1,
                        'fecha_registro'=>date('Y-m-d H:i:s')
                    ]);
                }
            }
            DB::commit();
            return response()->json($id_od);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function listarOrdenesDespachoPendientes(Request $request){
        $data = DB::table('almacen.orden_despacho')
        ->select('orden_despacho.*','adm_contri.nro_documento','adm_contri.razon_social',
        'alm_req.codigo as codigo_req','alm_req.concepto','ubi_dis.descripcion as ubigeo_descripcion',
        'sis_usua.nombre_corto','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
        DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
        'alm_almacen.descripcion as almacen_descripcion')
        ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','orden_despacho.id_cliente')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
        ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','orden_despacho.id_persona')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','orden_despacho.id_almacen')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->join('configuracion.ubi_dis','ubi_dis.id_dis','=','orden_despacho.ubigeo_destino')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','orden_despacho.registrado_por')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','orden_despacho.estado')
        ->where('orden_despacho.estado',1);
        // ->get();
        return datatables($data)->toJson();
    }

    public function listarOrdenesDespacho(Request $request){
        $data = DB::table('almacen.orden_despacho')
        ->select('orden_despacho.*','adm_contri.nro_documento','adm_contri.razon_social',
        'alm_req.codigo as codigo_req','alm_req.concepto','ubi_dis.descripcion as ubigeo_descripcion',
        'sis_usua.nombre_corto','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
        DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
        'alm_almacen.descripcion as almacen_descripcion','rrhh_perso.telefono')
        ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','orden_despacho.id_cliente')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
        ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','orden_despacho.id_persona')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','orden_despacho.id_almacen')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
        ->join('configuracion.ubi_dis','ubi_dis.id_dis','=','orden_despacho.ubigeo_destino')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','orden_despacho.registrado_por')
        ->where('orden_despacho.estado',9);
        // ->get();
        return datatables($data)->toJson();
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

        try {
            DB::beginTransaction();

            $codigo = $this->grupoODnextId($request->fecha_despacho,$request->id_sede);
            $id_usuario = Auth::user()->id_usuario;

            $id_od_grupo = DB::table('almacen.orden_despacho_grupo')
            ->insertGetId([
                'codigo'=>$codigo,
                'id_sede'=>$request->id_sede,
                'fecha_despacho'=>$request->fecha_despacho,
                'responsable'=>$request->responsable,
                'mov_propia'=>($request->mov_propia_valor == 'si' ? true : false),
                'id_proveedor'=>$request->id_proveedor,
                'observaciones'=>$request->observaciones,
                'registrado_por'=>$id_usuario,
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
                    'confirmacion'=>false,
                    'estado'=>1,
                    'fecha_registro'=>date('Y-m-d H:i:s')
                ]);
                //actualiza estado despachado
                DB::table('almacen.orden_despacho')
                ->where('id_od',$d->id_od)
                ->update(['estado'=>20]);//Despachado

                DB::table('almacen.orden_despacho_det')
                ->where('id_od',$d->id_od)
                ->update(['estado'=>20]);//Despachado

                DB::table('almacen.alm_req')
                ->where('id_requerimiento',$d->id_requerimiento)
                ->update(['estado'=>20]);//Despachado

                DB::table('almacen.alm_det_req')
                ->where('id_requerimiento',$d->id_requerimiento)
                ->update(['estado'=>20]);//Despachado

                //Agrega accion en requerimiento
                DB::table('almacen.alm_req_obs')
                ->insert([  'id_requerimiento'=>$request->id_requerimiento,
                            'accion'=>'DESPACHADO',
                            'descripcion'=>'Requerimiento Despachado',
                            'id_usuario'=>$id_usuario,
                            'fecha_registro'=>date('Y-m-d H:i:s')
                    ]);

            }
            DB::commit();
            return response()->json($id_od_grupo);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function listarGruposDespachados(Request $request){
        $data = DB::table('almacen.orden_despacho_grupo_det')
        ->select('orden_despacho_grupo_det.*','orden_despacho_grupo.fecha_despacho','orden_despacho.codigo as codigo_od',
        'orden_despacho_grupo.observaciones','orden_despacho.direccion_destino','sis_usua.nombre_corto as trabajador_despacho',
        'adm_contri.razon_social as proveedor_despacho','cliente.razon_social as cliente_razon_social',
        DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS cliente_persona"),
        'alm_req.codigo as codigo_req','alm_req.concepto','alm_req.id_requerimiento',
        'ubi_dis.descripcion as ubigeo_descripcion',
        'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color','alm_almacen.descripcion as almacen_descripcion',
        'orden_despacho_grupo.codigo as codigo_odg','orden_despacho.estado as estado_od')
        ->join('almacen.orden_despacho_grupo','orden_despacho_grupo.id_od_grupo','=','orden_despacho_grupo_det.id_od_grupo')
        ->leftjoin('configuracion.sis_usua','sis_usua.id_usuario','=','orden_despacho_grupo.responsable')
        ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','orden_despacho_grupo.id_proveedor')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        // ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','orden_despacho_grupo.estado')
        ->join('almacen.orden_despacho','orden_despacho.id_od','=','orden_despacho_grupo_det.id_od')
        ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','orden_despacho.id_cliente')
        ->leftjoin('contabilidad.adm_contri as cliente','cliente.id_contribuyente','=','com_cliente.id_contribuyente')
        ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','orden_despacho.id_persona')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','orden_despacho.id_almacen')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
        ->join('configuracion.ubi_dis','ubi_dis.id_dis','=','orden_despacho.ubigeo_destino')
        ->where([['orden_despacho_grupo_det.estado','!=',7]]);
        //->get();
        return datatables($data)->toJson();
    }

    public function verDetalleGrupoDespacho($id_od_grupo){
        $data = DB::table('almacen.orden_despacho_grupo_det')
        ->select('orden_despacho_grupo_det.*','orden_despacho.codigo','orden_despacho.direccion_destino',
        'orden_despacho.fecha_despacho','orden_despacho.fecha_entrega','adm_contri.nro_documento',
        'adm_contri.razon_social','alm_req.codigo as codigo_req','alm_req.concepto',
        'ubi_dis.descripcion as ubigeo_descripcion','sis_usua.nombre_corto','adm_estado_doc.estado_doc',
        DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
        'adm_estado_doc.bootstrap_color')
        ->join('almacen.orden_despacho','orden_despacho.id_od','=','orden_despacho_grupo_det.id_od')
        ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','orden_despacho.id_cliente')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
        ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','orden_despacho.id_persona')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->join('configuracion.ubi_dis','ubi_dis.id_dis','=','orden_despacho.ubigeo_destino')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','orden_despacho.registrado_por')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','orden_despacho.estado')
        ->where([['orden_despacho_grupo_det.id_od_grupo','=',$id_od_grupo],['orden_despacho_grupo_det.estado','!=',7]])
        ->get();
        return response()->json($data);
    }

    public function despacho_conforme(Request $request){
        try {
            DB::beginTransaction();

            $data = DB::table('almacen.orden_despacho_grupo_det')
            ->where('id_od_grupo_detalle',$request->id_od_grupo_detalle)
            ->update(['confirmacion'=>true,
                    'obs_confirmacion'=>'Entregado Conforme']);

            DB::table('almacen.orden_despacho')
            ->where('id_od',$request->id_od)
            ->update(['estado'=>21]);

            $id_usuario = Auth::user()->id_usuario;

            DB::table('almacen.orden_despacho_obs')
            ->insert([
                    'id_od'=>$request->id_od,
                    'accion'=>'ENTREGADO',
                    'observacion'=>'Entregado Conforme',
                    'registrado_por'=>$id_usuario,
                    'fecha_registro'=>date('Y-m-d H:i:s')
                    ]);
            
            if ($request->id_requerimiento !== null){
                DB::table('almacen.alm_req')
                ->where('id_requerimiento',$request->id_requerimiento)
                ->update(['estado'=>21]);

                DB::table('almacen.alm_det_req')
                ->where('id_requerimiento',$request->id_requerimiento)
                ->update(['estado'=>21]);
                //Agrega accion en requerimiento
                DB::table('almacen.alm_req_obs')
                ->insert([  'id_requerimiento'=>$request->id_requerimiento,
                            'accion'=>'ENTREGADO',
                            'descripcion'=>'Requerimiento Entregado',
                            'id_usuario'=>$id_usuario,
                            'fecha_registro'=>date('Y-m-d H:i:s')
                    ]);
            }
            DB::commit();
            return response()->json($data);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function despacho_no_conforme(Request $request){
        try {
            DB::beginTransaction();

            $data = DB::table('almacen.orden_despacho_grupo_det')
            ->where('id_od_grupo_detalle',$request->id_od_grupo_detalle)
            ->update(['confirmacion'=>false,
                      'obs_confirmacion'=>$request->obs_confirmacion]);

            DB::table('almacen.orden_despacho')
            ->where('id_od',$request->id_od)
            ->update(['estado'=>9]);

            DB::table('almacen.orden_despacho_grupo_det')
            ->where('id_od_grupo_detalle',$request->id_od_grupo_detalle)
            ->update(['estado'=>7]);

            $id_usuario = Auth::user()->id_usuario;

            DB::table('almacen.orden_despacho_obs')
            ->insert(['id_od'=>$request->id_od,
                    'accion'=>'NO ENTREGADO',
                    'observacion'=>$request->obs_confirmacion,
                    'registrado_por'=>$id_usuario,
                    'fecha_registro'=>date('Y-m-d H:i:s')
                    ]);
            //Agrega accion en requerimiento
            DB::table('almacen.alm_req_obs')
            ->insert([  'id_requerimiento'=>$request->id_requerimiento,
                        'accion'=>'ENTREGADO',
                        'descripcion'=>'Requerimiento Entregado',
                        'id_usuario'=>$id_usuario,
                        'fecha_registro'=>date('Y-m-d H:i:s')
                ]);
            DB::commit();
            return response()->json($data);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function guardar_guia_despacho(Request $request){
        try {
            DB::beginTransaction();
            $id_salida = null;

            if ($request->id_od !== null){

                $id_tp_doc_almacen = 2;//Guia Venta
                $id_usuario = Auth::user()->id_usuario;
                $fecha_registro = date('Y-m-d H:i:s');
    
                $od = DB::table('almacen.orden_despacho')
                ->where('id_od',$request->id_od)
                ->first();

                if ($od !== null){
                    $id_guia_ven = DB::table('almacen.guia_ven')->insertGetId(
                        [
                            'id_tp_doc_almacen' => $id_tp_doc_almacen,
                            'id_od' => $request->id_od,
                            'serie' => $request->serie,
                            'numero' => $request->numero,
                            'id_sede' => $request->id_sede,
                            'id_cliente' => $request->id_cliente,
                            'id_persona' => $request->id_persona,
                            'fecha_emision' => $request->fecha_emision,
                            'fecha_almacen' => $request->fecha_almacen,
                            'id_almacen' => $request->id_almacen,
                            'id_operacion' => $request->id_operacion,
                            // 'transportista' => $request->transportista,
                            // 'tra_serie' => $request->tra_serie,
                            // 'tra_numero' => $request->tra_numero,
                            // 'punto_partida' => $request->punto_partida,
                            // 'punto_llegada' => $request->punto_llegada,
                            // 'fecha_traslado' => $request->fecha_traslado,
                            // 'placa' => $request->placa,
                            // 'usuario' => $request->responsable,
                            'usuario' => $id_usuario,
                            'registrado_por' => $id_usuario,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro,
                        ],
                            'id_guia_ven'
                        );

                    //Genero la salida
                    $codigo = AlmacenController::nextMovimiento(2,//salida
                    $request->fecha_almacen,
                    $request->id_almacen);
    
                    $id_salida = DB::table('almacen.mov_alm')->insertGetId(
                        [
                            'id_almacen' => $request->id_almacen,
                            'id_tp_mov' => 2,//Salidas
                            'codigo' => $codigo,
                            'fecha_emision' => $request->fecha_almacen,
                            'id_guia_ven' => $id_guia_ven,
                            'id_operacion' => $request->id_operacion,
                            'revisado' => 0,
                            'usuario' => $id_usuario,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro,
                        ],
                            'id_mov_alm'
                        );

                    $detalle = DB::table('almacen.orden_despacho_det')
                    ->select('orden_despacho_det.*','alm_prod.id_unidad_medida')
                    ->join('almacen.alm_prod','alm_prod.id_producto','=','orden_despacho_det.id_producto')
                    ->where([['orden_despacho_det.id_od','=',$request->id_od],
                            ['orden_despacho_det.estado','!=',7]])
                    ->get();

                    foreach ($detalle as $det) {
                        //guardo los items de la guia ven
                        $id_guia_ven_det = DB::table('almacen.guia_ven_det')->insertGetId([
                            'id_guia_ven' => $id_guia_ven,
                            'id_producto' => $det->id_producto,
                            // 'id_posicion' => $request->id_posicion,
                            'cantidad' => $det->cantidad,
                            'id_unid_med' => $det->id_unidad_medida,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro
                        ],
                            'id_guia_ven_det'
                        );
                        //obtener costo promedio
                        $saldos_ubi = DB::table('almacen.alm_prod_ubi')
                        ->where([['id_producto','=',$det->id_producto],
                                ['id_almacen','=',$request->id_almacen]])
                        ->first();
                        //Guardo los items de la salida
                        $id_det = DB::table('almacen.mov_alm_det')->insertGetId(
                            [
                                'id_mov_alm' => $id_salida,
                                'id_producto' => $det->id_producto,
                                // 'id_posicion' => $det->id_posicion,
                                'cantidad' => $det->cantidad,
                                'valorizacion' => ($saldos_ubi !== null ? ($saldos_ubi->costo_promedio * $det->cantidad) : 0),
                                'usuario' => $id_usuario,
                                'id_guia_ven_det' => $id_guia_ven_det,
                                'estado' => 1,
                                'fecha_registro' => $fecha_registro,
                            ],
                                'id_mov_alm_det'
                            );
                        
                        //Actualizo los saldos del producto
                        //Obtengo el registro de saldos
                        $ubi = DB::table('almacen.alm_prod_ubi')
                        ->where([['id_producto','=',$det->id_producto],
                                ['id_almacen','=',$request->id_almacen]])
                        ->first();
                        //Traer stockActual
                        $saldo = AlmacenController::saldo_actual_almacen($det->id_producto, $request->id_almacen);
                        $valor = AlmacenController::valorizacion_almacen($det->id_producto, $request->id_almacen);
                        $cprom = ($saldo > 0 ? $valor/$saldo : 0);
                        //guardo saldos actualizados
                        if ($ubi !== null){//si no existe -> creo la ubicacion
                            DB::table('almacen.alm_prod_ubi')
                            ->where('id_prod_ubi',$ubi->id_prod_ubi)
                            ->update([  'stock' => $saldo,
                                        'valorizacion' => $valor,
                                        'costo_promedio' => $cprom
                                ]);
                        } else {
                            DB::table('almacen.alm_prod_ubi')->insert([
                                'id_producto' => $det->id_producto,
                                'id_almacen' => $request->id_almacen,
                                'stock' => $saldo,
                                'valorizacion' => $valor,
                                'costo_promedio' => $cprom,
                                'estado' => 1,
                                'fecha_registro' => $fecha_registro
                                ]);
                        }
                    }
                    //orden de despacho estado   procesado
                    DB::table('almacen.orden_despacho')
                    ->where('id_od',$request->id_od)
                    ->update(['estado'=>9]);
                    //orden de despacho detalle estado   procesado
                    DB::table('almacen.orden_despacho_det')
                    ->where('id_od',$request->id_od)
                    ->update(['estado'=>9]);
                    //requerimiento despachado
                    DB::table('almacen.alm_req')
                    ->where('id_requerimiento',$request->id_requerimiento)
                    ->update(['estado'=>9]);
                    //orden de despacho detalle estado   procesado
                    DB::table('almacen.alm_det_req')
                    ->where('id_requerimiento',$request->id_requerimiento)
                    ->update(['estado'=>9]);
                    //Agrega accion en requerimiento
                    DB::table('almacen.alm_req_obs')
                    ->insert([  'id_requerimiento'=>$request->id_requerimiento,
                                'accion'=>'SALIDA DE ALMACÉN',
                                'descripcion'=>'Se generó la Salida del Almacén con Guía '.$request->serie.'-'.$request->numero,
                                'id_usuario'=>$id_usuario,
                                'fecha_registro'=>date('Y-m-d H:i:s')
                        ]);
                }
            }

            DB::commit();
            return response()->json($id_salida);
            
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            // return response()->json($e);
        }
    }

    public function listarSalidasDespacho(Request $request){
        $data = DB::table('almacen.mov_alm')
        ->select('mov_alm.*','guia_ven.serie','guia_ven.numero','guia_ven.id_od','orden_despacho.codigo as codigo_od',
            DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
            'alm_req.codigo as codigo_requerimiento','adm_contri.razon_social','alm_req.concepto',
            'alm_almacen.descripcion as almacen_descripcion','sis_usua.nombre_corto')
            ->join('almacen.guia_ven','guia_ven.id_guia_ven','=','mov_alm.id_guia_ven')
            ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','guia_ven.id_cliente')
            ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','guia_ven.id_persona')
            ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','guia_ven.id_almacen')
            ->leftjoin('configuracion.sis_usua','sis_usua.id_usuario','=','guia_ven.usuario')
            ->join('almacen.orden_despacho','orden_despacho.id_od','=','guia_ven.id_od')
            ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
            ->where([['mov_alm.estado','!=','7']]);
            // ->get();
        // return response()->json($data);
        return datatables($data)->toJson();
    }

    public function imprimir_despacho($id_od_grupo){
        
        $id = $this->decode5t($id_od_grupo);

        $despacho_grupo = DB::table('almacen.orden_despacho_grupo')
        ->select('orden_despacho_grupo.*','sis_sede.descripcion as sede_descripcion',
        'sis_usua.nombre_corto as trabajador_despacho','adm_contri.nro_documento as ruc_empresa',
        'proveedor.razon_social as proveedor_despacho','adm_contri.razon_social as empresa_razon_social',
        'registrado.nombre_corto')
        ->leftjoin('configuracion.sis_usua','sis_usua.id_usuario','=','orden_despacho_grupo.responsable')
        ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','orden_despacho_grupo.id_proveedor')
        ->leftjoin('contabilidad.adm_contri as proveedor','proveedor.id_contribuyente','=','log_prove.id_contribuyente')
        ->join('administracion.sis_sede','sis_sede.id_sede','=','orden_despacho_grupo.id_sede')
        ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
        ->join('configuracion.sis_usua as registrado','registrado.id_usuario','=','orden_despacho_grupo.registrado_por')
        ->where('orden_despacho_grupo.id_od_grupo',$id)
        ->first();

        $ordenes_despacho = DB::table('almacen.orden_despacho_grupo_det')
        ->select('orden_despacho.*','adm_contri.nro_documento','adm_contri.razon_social',
        DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
        'ubi_dis.descripcion as ubigeo_descripcion','alm_almacen.descripcion as almacen_descripcion',
        'guia_ven.serie','guia_ven.numero','alm_req.codigo as codigo_req','alm_req.concepto',
        'rrhh_perso.nro_documento as dni')
        ->join('almacen.orden_despacho','orden_despacho.id_od','=','orden_despacho_grupo_det.id_od')
        ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','orden_despacho.id_cliente')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
        ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','orden_despacho.id_persona')
        ->leftjoin('configuracion.ubi_dis','ubi_dis.id_dis','=','orden_despacho.ubigeo_destino')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','orden_despacho.id_almacen')
        ->leftjoin('almacen.guia_ven','guia_ven.id_od','=','orden_despacho.id_od')
        ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->where([['orden_despacho_grupo_det.id_od_grupo','=',$id],['orden_despacho_grupo_det.estado','!=',7]])
        ->get();
        
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
                    font-size:12px;
                }
                #detalle thead{
                    padding: 4px;
                    background-color: #e5e5e5;
                }
                #detalle tbody tr td{
                    font-size:11px;
                    padding: 4px;
                }
                .right{
                    text-align: right;
                }
                .sup{
                    vertical-align:top;
                }
                </style>
            </head>
            <body>
                <table width="100%">
                    <tr>
                        <td>
                            <p style="text-align:left;font-size:10px;margin:0px;">'.$despacho_grupo->ruc_empresa.'</p>
                            <p style="text-align:left;font-size:10px;margin:0px;">'.$despacho_grupo->empresa_razon_social.'</p>
                            <p style="text-align:left;font-size:10px;margin:0px;">.::Sistema ERP v1.0::.</p>
                        </td>
                        <td>
                            <p style="text-align:right;font-size:10px;margin:0px;">Fecha: '.$fecha_actual.'</p>
                            <p style="text-align:right;font-size:10px;margin:0px;">Hora: '.$hora_actual.'</p>
                            <p style="text-align:right;font-size:10px;margin:0px;">Despacho: '.$despacho_grupo->fecha_despacho.'</p>
                        </td>
                    </tr>
                </table>
                <h3 style="margin:0px;"><center>DESPACHO</center></h3>
                <h5><center>'.($despacho_grupo->trabajador_despacho !== null ? $despacho_grupo->trabajador_despacho : $despacho_grupo->proveedor_despacho).'</center></h5>
                <p>'.strtoupper($despacho_grupo->observaciones).'</p>
                ';

                foreach ($ordenes_despacho as $od) {
                    # code...
                    $html.='<br/><table border="0">
                    <tbody>
                    <tr>
                        <td>OD N°</td>
                        <td width=10px>:</td>
                        <td class="verticalTop">'.$od->codigo.'</td>
                        <td width=100px>Cliente</td>
                        <td width=10px>:</td>
                        <td>'.($od->razon_social !== null ? ($od->nro_documento.' - '.$od->razon_social) : (($od->dni!==null ? $od->dni.' - ' : '').$od->nombre_persona)).'</td>
                    </tr>
                    <tr>
                        <td width=100px>Requerimiento</td>
                        <td width=10px>:</td>
                        <td class="verticalTop">'.$od->codigo_req.'</td>
                        <td>Concepto</td>
                        <td width=10px>:</td>
                        <td>'.($od->concepto !== null ? ($od->concepto) : '').'</td>
                    </tr>
                    <tr>
                        <td>Distrito</td>
                        <td width=10px>:</td>
                        <td width=170px class="verticalTop">'.$od->ubigeo_descripcion.'</td>
                        <td>Dirección</td>
                        <td width=10px>:</td>
                        <td>'.$od->direccion_destino.'</td>
                    </tr>
                    <tr>
                        <td>Teléfono</td>
                        <td width=10px>:</td>
                        <td width=170px class="verticalTop">'.($od->telefono!==null ? $od->telefono : '').'</td>
                        <td></td>
                        <td width=10px></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Almacén</td>
                        <td width=10px>:</td>
                        <td class="verticalTop">'.$od->almacen_descripcion.'</td>
                        <td>Guia Remisión</td>
                        <td width=10px>:</td>
                        <td>'.$od->serie.' - '.$od->numero.'</td>
                    </tr>
                    </tbody>
                    </table>
                    <br/>';

                    $detalle = DB::table('almacen.orden_despacho_det')
                    ->select('orden_despacho_det.*','alm_prod.codigo','alm_prod.descripcion',
                    'alm_und_medida.abreviatura')
                    ->join('almacen.alm_prod','alm_prod.id_producto','=','orden_despacho_det.id_producto')
                    ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
                    ->where([['orden_despacho_det.id_od','=',$od->id_od],['orden_despacho_det.estado','!=','7']])
                    ->get();

                    $i = 1;
                    $html.='<table border="1" cellspacing=0 cellpadding=2>
                    <tbody>
                    <tr style="background-color: lightblue;font-size:11px;">
                        <th>#</th>
                        <th with=50px>Codigo</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Und</th>
                    </tr>';
                    // background-color:lightgrey; 
                    foreach($detalle as $det){
                        $html.='
                        <tr style="font-size:11px;">
                            <td class="right">'.$i.'</td>
                            <td with=50px>'.$det->codigo.'</td>
                            <td>'.$det->descripcion.'</td>
                            <td class="right">'.$det->cantidad.'</td>
                            <td>'.$det->abreviatura.'</td>
                        </tr>';
                        $i++;
                    }
                    $html.='</tbody>
                    </table>';
                }
                
            $html.='<p style="text-align:right;font-size:11px;">Elaborado por: '.$despacho_grupo->nombre_corto.' '.$despacho_grupo->fecha_registro.'</p>
            </body>
        </html>';

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        return $pdf->stream();
        return $pdf->download('despacho.pdf');

    }

    public function anular_requerimiento(Request $request){
        try{
            DB::beginTransaction();
        
            $data = DB::table('almacen.alm_req')
            ->where('id_requerimiento',$request->obs_id_requerimiento)
            ->update(['estado'=>7]);
    
            $data = DB::table('almacen.alm_det_req')
            ->where('id_requerimiento',$request->obs_id_requerimiento)
            ->update(['estado'=>7]);
    
            $id_usuario = Auth::user()->id_usuario;

            $data = DB::table('almacen.alm_req_obs')
            ->insert(['id_requerimiento'=>$request->obs_id_requerimiento,
                      'accion'=>'ANULAR',
                      'descripcion'=>$request->obs_motivo,
                      'id_usuario'=>$id_usuario,
                      'fecha_registro'=>date('Y-m-d H:i:s')]);

            DB::commit();
            return response()->json($data);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }

    }

    public function pago_confirmado(Request $request){
        try {
            DB::beginTransaction();

            $data = DB::table('almacen.alm_req')
            ->where('id_requerimiento',$request->obs_id_requerimiento)
            ->update(['confirmacion_pago'=>true,
                      'obs_confirmacion'=>$request->obs_motivo
                      ]);

            $id_usuario = Auth::user()->id_usuario;

            DB::table('almacen.alm_req_obs')
            ->insert(['id_requerimiento'=>$request->obs_id_requerimiento,
                      'accion'=>'PAGO CONFIRMADO',
                      'descripcion'=>$request->obs_motivo,
                      'id_usuario'=>$id_usuario,
                      'fecha_registro'=>date('Y-m-d H:i:s')
                      ]);

            DB::commit();
            return response()->json($data);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function pago_no_confirmado(Request $request){
        try {
            DB::beginTransaction();

            $data = DB::table('almacen.alm_req')
            ->where('id_requerimiento',$request->obs_id_requerimiento)
            ->update(['confirmacion_pago'=>false,
                      'estado'=>7,
                      'obs_confirmacion'=>$request->obs_motivo]);

            $data = DB::table('almacen.alm_det_req')
            ->where('id_requerimiento',$request->obs_id_requerimiento)
            ->update(['estado'=>7]);

            $id_usuario = Auth::user()->id_usuario;
            
            DB::table('almacen.alm_req_obs')
            ->insert(['id_requerimiento'=>$request->obs_id_requerimiento,
                      'accion'=>'PAGO NO CONFIRMADO',
                      'descripcion'=>$request->obs_motivo,
                      'id_usuario'=>$id_usuario,
                      'fecha_registro'=>date('Y-m-d H:i:s')
                      ]);
      
            DB::commit();
            return response()->json($data);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
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
       
    public function decode5t($str){
        for($i=0; $i<5;$i++){
            $str=base64_decode(strrev($str));
        }
        return $str;
    }
    
    public function anular_salida(Request $request){
    
        try {
            DB::beginTransaction();
    
            $id_usuario = Auth::user()->id_usuario;
            $msj = '';
    
            $sal = DB::table('almacen.mov_alm')
            ->where('id_mov_alm', $request->id_salida)
            ->first();
            //si la salida no esta revisada
            if ($sal->revisado == 0){
                //si existe una orden
                if ($request->id_od !== null) {
                    //Verifica si ya fue despachado
                    $od = DB::table('almacen.orden_despacho')
                    ->select('orden_despacho.*','adm_estado_doc.estado_doc')
                    ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','orden_despacho.estado')
                    ->where('id_od',$request->id_od)
                    ->first();
                    //si la orden de despacho es Procesado
                    if ($od->estado == 9){
                        //Anula salida
                        $update = DB::table('almacen.mov_alm')
                        ->where('id_mov_alm', $request->id_salida)
                        ->update([ 'estado' => 7 ]);
                        //Anula el detalle
                        $update = DB::table('almacen.mov_alm_det')
                        ->where('id_mov_alm', $request->id_salida)
                        ->update([ 'estado' => 7 ]);
                        //Agrega motivo anulacion a la guia
                        DB::table('almacen.guia_ven_obs')->insert(
                        [
                            'id_guia_ven'=>$request->id_guia_ven,
                            'observacion'=>$request->observacion_guia_ven,
                            'registrado_por'=>$id_usuario,
                            'id_motivo_anu'=>$request->id_motivo_obs_ven,
                            'fecha_registro'=>date('Y-m-d H:i:s')
                        ]);
                        //Anula la Guia
                        $update = DB::table('almacen.guia_ven')
                        ->where('id_guia_ven', $request->id_guia_ven)
                        ->update([ 'estado' => 7 ]);
                        //Anula la Guia Detalle
                        $update = DB::table('almacen.guia_ven_det')
                        ->where('id_guia_ven', $request->id_guia_ven)
                        ->update([ 'estado' => 7 ]);
                        //Quita estado de la orden
                        DB::table('almacen.orden_despacho')
                        ->where('id_od',$request->id_od)
                        ->update(['estado' => 1]);

                        if ($od->id_requerimiento !== null){
                            //Requerimiento regresa a Reservado
                            DB::table('almacen.alm_req')
                            ->where('id_requerimiento',$od->id_requerimiento)
                            ->update(['estado'=>19]);//Reservado
    
                            DB::table('almacen.alm_det_req')
                            ->where('id_requerimiento',$od->id_requerimiento)
                            ->update(['estado'=>19]);//Reservado
                            //Agrega accion en requerimiento
                            DB::table('almacen.alm_req_obs')
                            ->insert([  'id_requerimiento'=>$od->id_requerimiento,
                                        'accion'=>'SALIDA ANULADA',
                                        'descripcion'=>'Requerimiento regresa a Reservado',
                                        'id_usuario'=>$id_usuario,
                                        'fecha_registro'=>date('Y-m-d H:i:s')
                                ]);
                        }
                    } else {
                        $msj = 'La Orden de Despacho ya fue '.$od->estado_doc;
                    }
                } else {
                    $msj = 'No existe una orden de despacho enlazada';
                }
            } else {
                $msj = 'La salida ya fue revisada por el Jefe de Almacén';
            }
            DB::commit();
            return response()->json($msj);
            
        } catch (\PDOException $e) {
    
            DB::rollBack();
        }
    }
    
    function anular_orden_despacho($id_od){
        try {
            DB::beginTransaction();

            $update = DB::table('almacen.orden_despacho')
            ->where('id_od',$id_od)
            ->update(['estado'=>7]);

            $update = DB::table('almacen.orden_despacho_det')
            ->where('id_od',$id_od)
            ->update(['estado'=>7]);

            $od = DB::table('almacen.orden_despacho')
            ->where('id_od',$id_od)
            ->first();
            $id_usuario = Auth::user()->id_usuario;
            //Agrega accion en requerimiento
            DB::table('almacen.alm_req_obs')
            ->insert([  'id_requerimiento'=>$od->id_requerimiento,
                        'accion'=>'O.D. ANULADA',
                        'descripcion'=>'Orden de Despacho Anulado',
                        'id_usuario'=>$id_usuario,
                        'fecha_registro'=>date('Y-m-d H:i:s')
                ]);

            DB::commit();
            return response()->json($update);
            
        } catch (\PDOException $e) {
    
            DB::rollBack();
        }
    }

    public function listarRequerimientosTrazabilidad(){
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*','sis_usua.nombre_corto as responsable','adm_grupo.descripcion as grupo',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            'log_ord_compra.codigo as codigo_orden','guia_com.serie','guia_com.numero',
            'trans.id_transferencia','trans.codigo as codigo_transferencia','ubi_dis.descripcion as ubigeo_descripcion',
            'rrhh_perso.nro_documento as dni_persona','alm_almacen.descripcion as almacen_descripcion',
            'alm_almacen.id_sede as sede_requerimiento','log_ord_compra.id_sede as sede_orden',
            'sis_sede.descripcion as sede_descripcion_orden','sede_req.descripcion as sede_descripcion_req',
            'orden_despacho.id_od','orden_despacho.codigo as codigo_od','orden_despacho.estado as estado_od',
            'alm_tp_req.descripcion as tipo_req',
            DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
            'adm_contri.nro_documento as cliente_ruc','adm_contri.razon_social as cliente_razon_social')
            ->join('almacen.alm_tp_req','alm_tp_req.id_tipo_requerimiento','=','alm_req.id_tipo_requerimiento')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->leftjoin('administracion.adm_grupo','adm_grupo.id_grupo','=','alm_req.id_grupo')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
            ->leftJoin('logistica.log_ord_compra', function($join)
                         {   $join->on('log_ord_compra.id_requerimiento', '=', 'alm_req.id_requerimiento');
                             $join->where('log_ord_compra.estado','!=', 7);
                         })
            ->leftJoin('administracion.sis_sede','sis_sede.id_sede','=','log_ord_compra.id_sede')
            // ->leftJoin('almacen.guia_com','guia_com.id_oc','=','log_ord_compra.id_orden_compra')
            // ->leftJoin('almacen.mov_alm','mov_alm.id_guia_com','=','guia_com.id_guia')
            ->leftJoin('almacen.guia_com', function($join)
                         {   $join->on('guia_com.id_oc', '=', 'log_ord_compra.id_orden_compra');
                             $join->where('guia_com.estado','!=', 7);
                         })
            ->leftJoin('almacen.guia_ven', function($join)
                         {   $join->on('guia_ven.id_guia_com', '=', 'guia_com.id_guia');
                             $join->where('guia_ven.estado','!=', 7);
                         })
            // ->leftJoin('almacen.guia_ven','guia_ven.id_guia_com','=','guia_com.id_guia')
            ->leftJoin('almacen.trans', function($join)
                         {   $join->on('trans.id_guia_ven', '=', 'guia_ven.id_guia_ven');
                             $join->where('trans.estado','!=', 7);
                         })
            // ->leftJoin('almacen.trans','trans.id_guia_ven','=','guia_ven.id_guia_ven')
            ->leftJoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_req.id_almacen')
            ->leftJoin('administracion.sis_sede as sede_req','sede_req.id_sede','=','alm_almacen.id_sede')
            ->leftJoin('configuracion.ubi_dis','ubi_dis.id_dis','=','alm_req.id_ubigeo_entrega')
            ->leftJoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','alm_req.id_persona')
            ->leftJoin('comercial.com_cliente','com_cliente.id_cliente','=','alm_req.id_cliente')
            ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->leftJoin('almacen.orden_despacho', function($join)
                         {   $join->on('orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento');
                             $join->where('orden_despacho.estado','!=', 7);
                         })
            // ->leftJoin('almacen.orden_despacho','orden_despacho.id_requerimiento','=','alm_req.id_requerimiento')
            ->where([['alm_req.estado','!=',7]])
            ->orderBy('alm_req.fecha_requerimiento','desc');
            // ->get();
        return datatables($data)->toJson();
        // return response()->json($data);
    }

    public function verTrazabilidadRequerimiento($id_requerimiento){
        $data = DB::table('almacen.alm_req_obs')
        ->select('alm_req_obs.*','sis_usua.nombre_corto')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req_obs.id_usuario')
        ->where('alm_req_obs.id_requerimiento',$id_requerimiento)
        ->orderBy('fecha_registro','desc')
        ->get();
        return response()->json($data);
    }

    public function verRequerimientoAdjuntos($id_requerimiento){
        $data = DB::table('almacen.alm_req_adjuntos')
        ->where('alm_req_adjuntos.id_requerimiento',$id_requerimiento)
        ->orderBy('fecha_registro','desc')
        ->get();
        $i = 1;
        $html = '';
        foreach($data as $d){
            $ruta = '/logistica/requerimiento/'.$d->archivo;
            $file = asset('files').$ruta;
            $html .= '  
                <tr id="seg-'.$d->id_adjunto.'">
                    <td>'.$i.'</td>
                    <td><a href="'.$file.'" target="_blank">'.$d->archivo.'</a></td>
                    <td>'.$d->fecha_registro.'</td>
                </tr>';
            $i++;
        }
        return json_encode($html);
    }

}
