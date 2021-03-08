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

class TransferenciaController extends Controller
{
    public function __construct(){
        // session_start();
    }
    function view_listar_transferencias(){
        $clasificaciones = AlmacenController::mostrar_guia_clas_cbo();
        // $almacenes = AlmacenController::mostrar_almacenes_cbo();
        $almacenes = $this->listarAlmacenesAcceso();
        $usuarios = AlmacenController::select_usuarios();
        $motivos_anu = AlmacenController::select_motivo_anu();
        return view('almacen/transferencias/listar_transferencias', compact('clasificaciones','almacenes','usuarios','motivos_anu'));
    }

    public function listarAlmacenesAcceso(){
        $id_usuario = Auth::user()->id_usuario;
        $data = DB::table('configuracion.sis_usua_sede')
        ->select('alm_almacen.*')
        ->join('almacen.alm_almacen','alm_almacen.id_sede','=','sis_usua_sede.id_sede')
        ->where('sis_usua_sede.id_usuario',$id_usuario)
        ->get();
        return $data;
    }

    // public function listar_transferencias_pendientes($destino){
    //     $data = DB::table('almacen.trans')
    //     ->select('trans.*','guia_ven.fecha_emision as fecha_guia',
    //     DB::raw("(guia_ven.serie) || ' ' || (guia_ven.numero) as guia_ven"),
    //     DB::raw("(guia_com.serie) || ' ' || (guia_com.numero) as guia_com"),
    //     'alm_origen.descripcion as alm_origen_descripcion',
    //     'alm_destino.descripcion as alm_destino_descripcion',
    //     'usu_origen.nombre_corto as nombre_origen',
    //     'usu_destino.nombre_corto as nombre_destino',
    //     'usu_registro.nombre_corto as nombre_registro',
    //     'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
    //     'guia_ven.id_guia_com as guia_ingreso_compra','mov_alm.id_mov_alm as id_salida',
    //     'log_ord_compra.codigo as codigo_orden','alm_req.codigo as codigo_req',
    //     'alm_req.concepto as concepto_req','req_directo.id_requerimiento as id_requerimiento_directo',
    //     'req_directo.codigo as codigo_req_directo','req_directo.concepto as concepto_req_directo')
    //     ->leftJoin('almacen.mov_alm','mov_alm.id_guia_ven','=','trans.id_guia_ven')
    //     ->leftJoin('almacen.guia_ven','guia_ven.id_guia_ven','=','trans.id_guia_ven')
    //     ->leftJoin('almacen.guia_com as guia_compra','guia_compra.id_guia','=','guia_ven.id_guia_com')
    //     ->leftJoin('logistica.log_ord_compra', function($join)
    //     {   $join->on('log_ord_compra.id_orden_compra', '=', 'guia_compra.id_oc');
    //         $join->where('log_ord_compra.estado','!=', 7);
    //     })
    //     ->leftJoin('almacen.alm_req', function($join)
    //     {   $join->on('alm_req.id_requerimiento', '=', 'log_ord_compra.id_requerimiento');
    //         $join->where('alm_req.estado','!=', 7);
    //     })
    //     ->leftJoin('almacen.guia_com','guia_com.id_guia','=','trans.id_guia_com')
    //     ->join('almacen.alm_almacen as alm_origen','alm_origen.id_almacen','=','trans.id_almacen_origen')
    //     ->leftJoin('almacen.alm_almacen as alm_destino','alm_destino.id_almacen','=','trans.id_almacen_destino')
    //     ->leftJoin('configuracion.sis_usua as usu_origen','usu_origen.id_usuario','=','trans.responsable_origen')
    //     ->leftJoin('configuracion.sis_usua as usu_destino','usu_destino.id_usuario','=','trans.responsable_destino')
    //     ->leftJoin('almacen.alm_req as req_directo','req_directo.id_requerimiento','=','trans.id_requerimiento')
    //     ->join('configuracion.sis_usua as usu_registro','usu_registro.id_usuario','=','trans.registrado_por')
    //     ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','trans.estado')
    //     ->where([['trans.id_almacen_destino','=',$destino],
    //              ['trans.estado','!=',7],
    //              ['trans.estado','!=',14]])
    //     ->get();
    //     $output['data'] = $data;
    //     return response()->json($output);
    // }
    public function listarTransferenciasPorRecibir($destino){
        if ($destino == '0'){
            $data = DB::table('almacen.trans')
            ->select('trans.id_guia_ven','guia_ven.fecha_emision as fecha_guia',
            DB::raw("(guia_ven.serie) || ' ' || (guia_ven.numero) as guia_ven"),
            'trans.id_almacen_destino','trans.id_almacen_origen',
            'alm_origen.descripcion as alm_origen_descripcion',
            'alm_destino.descripcion as alm_destino_descripcion',
            'usu_origen.nombre_corto as nombre_origen',
            'usu_destino.nombre_corto as nombre_destino',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            'mov_alm.id_mov_alm as id_salida')
            ->leftJoin('almacen.mov_alm','mov_alm.id_guia_ven','=','trans.id_guia_ven')
            ->leftJoin('almacen.guia_ven','guia_ven.id_guia_ven','=','trans.id_guia_ven')
            ->join('almacen.alm_almacen as alm_origen','alm_origen.id_almacen','=','trans.id_almacen_origen')
            ->leftJoin('almacen.alm_almacen as alm_destino','alm_destino.id_almacen','=','trans.id_almacen_destino')
            ->leftJoin('configuracion.sis_usua as usu_origen','usu_origen.id_usuario','=','trans.responsable_origen')
            ->leftJoin('configuracion.sis_usua as usu_destino','usu_destino.id_usuario','=','trans.responsable_destino')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','trans.estado')
            ->where([['trans.estado','!=',7],
                     ['trans.estado','!=',14]])
            ->distinct()->get();
        } 
        else {
            $data = DB::table('almacen.trans')
            ->select('trans.id_guia_ven','guia_ven.fecha_emision as fecha_guia',
            DB::raw("(guia_ven.serie) || ' ' || (guia_ven.numero) as guia_ven"),
            'trans.id_almacen_destino','trans.id_almacen_origen',
            'alm_origen.descripcion as alm_origen_descripcion',
            'alm_destino.descripcion as alm_destino_descripcion',
            'usu_origen.nombre_corto as nombre_origen',
            'usu_destino.nombre_corto as nombre_destino',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            'mov_alm.id_mov_alm as id_salida')
            ->leftJoin('almacen.mov_alm','mov_alm.id_guia_ven','=','trans.id_guia_ven')
            ->leftJoin('almacen.guia_ven','guia_ven.id_guia_ven','=','trans.id_guia_ven')
            ->join('almacen.alm_almacen as alm_origen','alm_origen.id_almacen','=','trans.id_almacen_origen')
            ->leftJoin('almacen.alm_almacen as alm_destino','alm_destino.id_almacen','=','trans.id_almacen_destino')
            ->leftJoin('configuracion.sis_usua as usu_origen','usu_origen.id_usuario','=','trans.responsable_origen')
            ->leftJoin('configuracion.sis_usua as usu_destino','usu_destino.id_usuario','=','trans.responsable_destino')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','trans.estado')
            ->where([['trans.id_almacen_destino','=',$destino],
                     ['trans.estado','!=',7],
                     ['trans.estado','!=',14]])
            ->distinct()->get();
        }

        $output['data'] = $data;
        return response()->json($output);
    }
    
    public function listar_transferencias_recibidas($destino){
        
        if ($destino == '0'){
            $data = DB::table('almacen.trans')
            ->select('trans.*','guia_ven.fecha_emision as fecha_guia',
            DB::raw("(guia_ven.serie) || ' ' || (guia_ven.numero) as guia_ven"),
            DB::raw("(guia_com.serie) || ' ' || (guia_com.numero) as guia_com"),
            'alm_origen.descripcion as alm_origen_descripcion',
            'alm_destino.descripcion as alm_destino_descripcion',
            'usu_origen.nombre_corto as nombre_origen',
            'usu_destino.nombre_corto as nombre_destino',
            'usu_registro.nombre_corto as nombre_registro',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            'guia_ven.id_guia_com as guia_ingreso_compra','ingreso.id_mov_alm as id_ingreso',
            'salida.id_mov_alm as id_salida',
            'log_ord_compra.codigo as codigo_orden','alm_req.codigo as codigo_req',
            'alm_req.concepto as concepto_req','req_directo.codigo as codigo_req_directo',
            'req_directo.concepto as concepto_req_directo')
            ->leftJoin('almacen.mov_alm as ingreso','ingreso.id_guia_com','=','trans.id_guia_com')
            ->leftJoin('almacen.mov_alm as salida','salida.id_guia_ven','=','trans.id_guia_ven')
            ->leftJoin('almacen.guia_ven','guia_ven.id_guia_ven','=','trans.id_guia_ven')
            ->leftJoin('almacen.guia_com as guia_compra','guia_compra.id_guia','=','guia_ven.id_guia_com')
            ->leftJoin('logistica.log_ord_compra', function($join)
            {   $join->on('log_ord_compra.id_orden_compra', '=', 'guia_compra.id_oc');
                $join->where('log_ord_compra.estado','!=', 7);
            })
            ->leftJoin('almacen.alm_req', function($join)
            {   $join->on('alm_req.id_requerimiento', '=', 'log_ord_compra.id_requerimiento');
                $join->where('alm_req.estado','!=', 7);
            })
            ->leftJoin('almacen.alm_req as req_directo', function($join)
            {   $join->on('req_directo.id_requerimiento', '=', 'trans.id_requerimiento');
                $join->where('req_directo.estado','!=', 7);
            })
            ->leftJoin('almacen.guia_com','guia_com.id_guia','=','trans.id_guia_com')
            ->join('almacen.alm_almacen as alm_origen','alm_origen.id_almacen','=','trans.id_almacen_origen')
            ->leftJoin('almacen.alm_almacen as alm_destino','alm_destino.id_almacen','=','trans.id_almacen_destino')
            ->leftJoin('configuracion.sis_usua as usu_origen','usu_origen.id_usuario','=','trans.responsable_origen')
            ->leftJoin('configuracion.sis_usua as usu_destino','usu_destino.id_usuario','=','trans.responsable_destino')
            ->join('configuracion.sis_usua as usu_registro','usu_registro.id_usuario','=','trans.registrado_por')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','trans.estado')
            ->where([['trans.estado','=',14]])
            ->get();
        } 
        else {
            $data = DB::table('almacen.trans')
            ->select('trans.*','guia_ven.fecha_emision as fecha_guia',
            DB::raw("(guia_ven.serie) || ' ' || (guia_ven.numero) as guia_ven"),
            DB::raw("(guia_com.serie) || ' ' || (guia_com.numero) as guia_com"),
            'alm_origen.descripcion as alm_origen_descripcion',
            'alm_destino.descripcion as alm_destino_descripcion',
            'usu_origen.nombre_corto as nombre_origen',
            'usu_destino.nombre_corto as nombre_destino',
            'usu_registro.nombre_corto as nombre_registro',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            'guia_ven.id_guia_com as guia_ingreso_compra','ingreso.id_mov_alm as id_ingreso',
            'salida.id_mov_alm as id_salida',
            'log_ord_compra.codigo as codigo_orden','alm_req.codigo as codigo_req',
            'alm_req.concepto as concepto_req','req_directo.codigo as codigo_req_directo',
            'req_directo.concepto as concepto_req_directo')
            ->leftJoin('almacen.mov_alm as ingreso','ingreso.id_guia_com','=','trans.id_guia_com')
            ->leftJoin('almacen.mov_alm as salida','salida.id_guia_ven','=','trans.id_guia_ven')
            ->leftJoin('almacen.guia_ven','guia_ven.id_guia_ven','=','trans.id_guia_ven')
            ->leftJoin('almacen.guia_com as guia_compra','guia_compra.id_guia','=','guia_ven.id_guia_com')
            ->leftJoin('logistica.log_ord_compra', function($join)
            {   $join->on('log_ord_compra.id_orden_compra', '=', 'guia_compra.id_oc');
                $join->where('log_ord_compra.estado','!=', 7);
            })
            ->leftJoin('almacen.alm_req', function($join)
            {   $join->on('alm_req.id_requerimiento', '=', 'log_ord_compra.id_requerimiento');
                $join->where('alm_req.estado','!=', 7);
            })
            ->leftJoin('almacen.alm_req as req_directo', function($join)
            {   $join->on('req_directo.id_requerimiento', '=', 'trans.id_requerimiento');
                $join->where('req_directo.estado','!=', 7);
            })
            ->leftJoin('almacen.guia_com','guia_com.id_guia','=','trans.id_guia_com')
            ->join('almacen.alm_almacen as alm_origen','alm_origen.id_almacen','=','trans.id_almacen_origen')
            ->leftJoin('almacen.alm_almacen as alm_destino','alm_destino.id_almacen','=','trans.id_almacen_destino')
            ->leftJoin('configuracion.sis_usua as usu_origen','usu_origen.id_usuario','=','trans.responsable_origen')
            ->leftJoin('configuracion.sis_usua as usu_destino','usu_destino.id_usuario','=','trans.responsable_destino')
            ->join('configuracion.sis_usua as usu_registro','usu_registro.id_usuario','=','trans.registrado_por')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','trans.estado')
            ->where([['trans.id_almacen_destino','=',$destino],['trans.estado','=',14]])
            ->get();

        }
        $output['data'] = $data;
        return response()->json($output);
    }

    public function listar_transferencia_detalle($id_transferencia){
        $detalle = DB::table('almacen.trans_detalle')
        ->select('trans_detalle.*','alm_prod.codigo','alm_prod.descripcion','alm_prod.series',
        'alm_und_medida.abreviatura','alm_prod.part_number','guia_com.serie','guia_com.numero',
        'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','trans_detalle.id_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','trans_detalle.estado')
        ->leftJoin('almacen.guia_com_det','guia_com_det.id_trans_detalle','=','trans_detalle.id_trans_detalle')
        ->leftJoin('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
        ->where([['trans_detalle.id_transferencia','=',$id_transferencia],
                 ['trans_detalle.estado','!=',7]])
        ->get();        
        return response()->json($detalle);
    }

    public function listar_guia_transferencia_detalle($id_guia_ven){
        $detalle = DB::table('almacen.guia_ven_det')
        ->select('guia_ven_det.*','alm_prod.codigo','alm_prod.descripcion','alm_und_medida.abreviatura',
        'alm_prod.part_number','alm_cat_prod.descripcion as categoria','alm_subcat.descripcion as subcategoria',
        'alm_prod.series','trans.codigo as codigo_trans','alm_req.codigo as codigo_req','alm_req.concepto')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','guia_ven_det.id_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->join('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=','alm_prod.id_categoria')
        ->join('almacen.alm_subcat','alm_subcat.id_subcategoria','=','alm_prod.id_subcategoria')
        ->join('almacen.trans_detalle','trans_detalle.id_trans_detalle','=','guia_ven_det.id_trans_det')
        ->join('almacen.trans','trans.id_transferencia','=','trans_detalle.id_transferencia')
        ->leftjoin('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','trans_detalle.id_requerimiento_detalle')
        ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
        ->where([['guia_ven_det.id_guia_ven','=',$id_guia_ven],
                 ['guia_ven_det.estado','!=',7]])
        ->get();

        $lista_detalle = [];

        foreach ($detalle as $det) {
            $series = DB::table('almacen.alm_prod_serie')
            ->where('id_guia_ven_det',$det->id_guia_ven_det)
            ->get();

            array_push($lista_detalle, [
                'id_guia_ven_det' => $det->id_guia_ven_det,
                'codigo_trans' => $det->codigo_trans,
                'codigo_req' => $det->codigo_req,
                'concepto' => $det->concepto,
                'codigo' => $det->codigo,
                'part_number' => $det->part_number,
                'descripcion' => $det->descripcion,
                'cantidad' => $det->cantidad,
                'abreviatura' => $det->abreviatura,
                'series' => $series
            ]);
        }
        return response()->json($lista_detalle);
    }

    public function anular_transferencia($id_transferencia){

        $trans = DB::table('almacen.trans')
        ->where('id_transferencia',$id_transferencia)
        ->update(['estado'=>7]);

        $trans = DB::table('almacen.trans_detalle')
        ->where('id_transferencia',$id_transferencia)
        ->update(['estado'=>7]);

        return response()->json($trans);
    }

    public function anular_transferencia_ingreso(Request $request){
        try {
            DB::beginTransaction();

            $ing = DB::table('almacen.mov_alm')
            ->where([['mov_alm.id_mov_alm', '=', $request->id_mov_alm]])//ingreso
            ->first();

            $msj = '';
            //si el ingreso no esta revisado
            if ($ing->revisado == 0){

                $transferencias = DB::table('almacen.trans')
                ->select('trans.id_transferencia','trans.id_requerimiento',
                         'trans.id_guia_com','trans.id_almacen_origen','orden_despacho.id_od')
                ->leftJoin('almacen.orden_despacho', function($join)
                         {   $join->on('orden_despacho.id_requerimiento', '=', 'trans.id_requerimiento');
                             $join->where('orden_despacho.estado','!=', 7);
                         })
                ->where([['trans.id_guia_com', '=', $ing->id_guia_com],['trans.estado','!=',7]])
                ->get();

                $rollback = 0;
                foreach ($transferencias as $t) {
                    if ($t->id_od !== null){
                        $rollback++;
                    }
                }

                if ($rollback == 0){

                    $id_usuario = Auth::user()->id_usuario;
                    //Anula ingreso
                    $update = DB::table('almacen.mov_alm')
                    ->where('id_mov_alm', $request->id_mov_alm)
                    ->update([ 'estado' => 7 ]);
                    //Anula el detalle
                    $update = DB::table('almacen.mov_alm_det')
                    ->where('id_mov_alm', $request->id_mov_alm)
                    ->update([ 'estado' => 7 ]);
                    //Agrega motivo anulacion a la guia
                    DB::table('almacen.guia_com_obs')->insert(
                    [
                        'id_guia_com'=>$request->id_guia_com,
                        'observacion'=>$request->observacion,
                        'registrado_por'=>$id_usuario,
                        'id_motivo_anu'=>$request->id_motivo_obs,
                        'fecha_registro'=>date('Y-m-d H:i:s')
                    ]);
                    //Anula la Guia
                    $update = DB::table('almacen.guia_com')
                    ->where('id_guia', $request->id_guia_com)
                    ->update([ 'estado' => 7 ]);
                    //Anula la Guia Detalle
                    $update = DB::table('almacen.guia_com_det')
                    ->where('id_guia_com', $request->id_guia_com)
                    ->update([ 'estado' => 7 ]);

                    $detalle = DB::table('almacen.guia_com_det')
                    ->select('guia_com_det.id_guia_com_det')
                    ->where('id_guia_com', $request->id_guia_com)
                    ->get();

                    foreach ($detalle as $det) {
                        DB::table('almacen.alm_prod_serie')
                        ->where('id_guia_com_det','=',$det->id_guia_com_det)
                        ->update(['id_guia_com_det' => null, 
                                  'estado' => 7]);
                    }
                    //Transferencia cambia estado elaborado
                    foreach ($transferencias as $tra) {
                        DB::table('almacen.trans')
                        ->where('id_transferencia',$tra->id_transferencia)
                        ->update([ 'estado' => 17,
                                    'id_guia_com' => null ]);
                        //Transferencia Detalle cambia estado elaborado
                        DB::table('almacen.trans_detalle')
                        ->where('id_transferencia',$tra->id_transferencia)
                        ->update([ 'estado' => 17 ]);
                        //Requerimiento regresa a enviado
                        DB::table('almacen.alm_req')
                        ->where('id_requerimiento',$tra->id_requerimiento)
                        ->update([ 'estado' => 17 ]);//Enviado

                        DB::table('almacen.alm_det_req')
                        ->where('id_requerimiento',$tra->id_requerimiento)
                        ->update([ 'estado' => 17 ]);//Enviado
                        $id_usuario = Auth::user()->id_usuario;
                        //Agrega accion en requerimiento
                        DB::table('almacen.alm_req_obs')
                        ->insert([  'id_requerimiento'=>$tra->id_requerimiento,
                                    'accion'=>'INGRESO ANULADO',
                                    'descripcion'=>'Ingreso por Transferencia Anulado. '.$request->id_motivo_obs.'. Requerimiento regresa a Enviado.',
                                    'id_usuario'=>$id_usuario,
                                    'fecha_registro'=>date('Y-m-d H:i:s')
                            ]);
                    }
                } else {
                    $msj = 'Ya se generó una Orden de Despacho.';
                }
            } else {
                $msj = 'El ingreso ya fue revisado por el Jefe de Almacén.';
            }

            DB::commit();
            
            return response()->json($msj);
            
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            // return response()->json($e);
        }
    }

    public function anular_transferencia_salida(Request $request){
        try {
            DB::beginTransaction();

            $sal = DB::table('almacen.mov_alm')
                ->select('mov_alm.*')
                ->where([['mov_alm.id_mov_alm', '=', $request->id_salida]])//salida
                ->first();

            $msj = '';
            //si el salida no esta revisado
            if ($sal->revisado == 0){

                $transferencias = DB::table('almacen.trans')
                ->select('trans.id_transferencia','trans.id_requerimiento',
                         'trans.id_guia_com','trans.id_almacen_origen')
                ->where([['id_guia_ven', '=', $request->id_guia_ven],['estado','!=',7]])
                ->get();

                $rollback = 0;
                foreach ($transferencias as $t) {
                    if ($t->id_guia_com !== null){
                        $rollback++;
                    }
                }

                if ($rollback == 0){
                    $id_usuario = Auth::user()->id_usuario;
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

                    $detalle = DB::table('almacen.guia_ven_det')
                    ->select('guia_ven_det.id_guia_ven_det')
                    ->where('id_guia_ven', $request->id_guia_ven)
                    ->get();

                    foreach ($detalle as $det) {
                        DB::table('almacen.alm_prod_serie')
                        ->where('id_guia_ven_det','=',$det->id_guia_ven_det)
                        ->update(['id_guia_ven_det' => null]);
                    }
                    //Transferencia cambia estado elaborado
                    foreach ($transferencias as $tra) {
                        DB::table('almacen.trans')
                        ->where('id_transferencia',$tra->id_transferencia)
                        ->update([ 'estado' => 1,
                                   'id_guia_ven' => null ]);
                        //Transferencia Detalle cambia estado elaborado
                        DB::table('almacen.trans_detalle')
                        ->where('id_transferencia',$tra->id_transferencia)
                        ->update([ 'estado' => 1 ]);
                        //Requerimiento regresa a Reservado
                        DB::table('almacen.alm_req')
                        ->where('id_requerimiento',$tra->id_requerimiento)
                        ->update(['estado' => 19]);//Reservado

                        DB::table('almacen.alm_det_req')
                        ->where('id_requerimiento',$tra->id_requerimiento)
                        ->update(['estado' => 19,
                                  'id_almacen_reserva'=>$tra->id_almacen_origen]);//Reservado

                        DB::table('almacen.alm_req_obs')
                        ->insert([
                            'id_requerimiento'  => $tra->id_requerimiento,
                            'accion'            => 'ANULA SALIDA TRANSFERENCIA',
                            'descripcion'       => 'Se anula la salida por transferencia. '.$request->observacion_guia_ven,
                            'id_usuario'        => $id_usuario,
                            'fecha_registro'    => date('Y-m-d H:i:s')
                            ]);
                    }
                } else {
                    $msj = 'Ya se generó el Ingreso en el Almacén Destino.';
                }
            } else {
                $msj = 'La salida ya fue revisado por el Jefe de Almacén.';
            }

            DB::commit();
            
            return response()->json($msj);
            
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            // return response()->json($e);
        }
    }

    public function ingreso_transferencia($id_guia_com){
        $data = DB::table('almacen.mov_alm')
        ->where('id_guia_com',$id_guia_com)->get();
        return response()->json($data);
    }

    public function guardar_ingreso_transferencia(Request $request){ 

        try {
            DB::beginTransaction();

            $usuario = Auth::user();
            $fecha = date('Y-m-d H:i:s');

            DB::table('almacen.trans')->where('id_guia_ven',$request->id_guia_ven)
            ->update(['responsable_destino'=>$request->responsable_destino]);

            $guia_ven = DB::table('almacen.guia_ven')
            ->select('guia_ven.*','adm_empresa.id_contribuyente as empresa_contribuyente',
            'log_prove.id_proveedor as empresa_proveedor','com_cliente.id_contribuyente as cliente_contribuyente',
            'prove_cliente.id_proveedor as cliente_proveedor')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','guia_ven.id_sede')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
            ->leftJoin('logistica.log_prove','log_prove.id_contribuyente','=','adm_empresa.id_contribuyente')
            ->leftJoin('comercial.com_cliente','com_cliente.id_cliente','=','guia_ven.id_cliente')
            ->leftJoin('logistica.log_prove as prove_cliente','prove_cliente.id_contribuyente','=','com_cliente.id_contribuyente')
            ->where('guia_ven.id_guia_ven',$request->id_guia_ven)
            ->first();
            
            $id_proveedor = null;

            if ($guia_ven->id_cliente !== null){
                //si existe, copia el id_proveedor
                if ($guia_ven->cliente_proveedor !== null){
                    $id_proveedor = $guia_ven->cliente_proveedor;
                }
                else if ($guia_ven->cliente_contribuyente !== null){
                        $id_proveedor = DB::table('logistica.log_prove')->insertGetId([
                            'id_contribuyente'=>$guia_ven->cliente_contribuyente,
                            'estado'=>1,
                            'fecha_registro'=>$fecha,
                        ], 
                            'id_proveedor'
                        );
                    } //si no existe proveedor, id_empresa
            } 
            else {
                if ($guia_ven->empresa_proveedor !== null){
                    $id_proveedor = $guia_ven->empresa_proveedor;
                } 
                else if ($guia_ven->empresa_contribuyente !== null){
                    $id_proveedor = DB::table('logistica.log_prove')->insertGetId([
                        'id_contribuyente'=>$guia_ven->empresa_contribuyente,
                        'estado'=>1,
                        'fecha_registro'=>$fecha,
                    ], 
                        'id_proveedor'
                    );
                }
            }

            $id_guia_com = DB::table('almacen.guia_com')->insertGetId(
                [
                    'id_tp_doc_almacen' => 1,//Guia Compra
                    'serie' => $guia_ven->serie,
                    'numero' => $guia_ven->numero,
                    'id_proveedor' => ($id_proveedor !== null ? $id_proveedor : null),
                    'fecha_emision' => date('Y-m-d'),
                    'fecha_almacen' => $request->fecha_almacen,
                    'id_almacen' => $request->id_almacen_destino,
                    // 'id_motivo' => $guia_ven->id_motivo,
                    'id_guia_clas' => 1,
                    'id_operacion' => 21,//entrada por transferencia entre almacenes
                    'punto_partida' => $guia_ven->punto_partida,
                    'punto_llegada' => $guia_ven->punto_llegada,
                    'transportista' => $guia_ven->transportista,
                    'fecha_traslado' => $guia_ven->fecha_traslado,
                    'tra_serie' => $guia_ven->tra_serie,
                    'tra_numero' => $guia_ven->tra_numero,
                    'placa' => $guia_ven->placa,
                    'usuario' => $request->responsable_destino,
                    'registrado_por' => $usuario->id_usuario,
                    'estado' => 9,
                    'fecha_registro' => $fecha
                ],
                    'id_guia'
                );

            $codigo = AlmacenController::nextMovimiento(1,
                $request->fecha_almacen,
                $request->id_almacen_destino);

            $id_ingreso = DB::table('almacen.mov_alm')->insertGetId(
                [
                    'id_almacen' => $request->id_almacen_destino,
                    'id_tp_mov' => 1,//Ingresos
                    'codigo' => $codigo,
                    'fecha_emision' => $request->fecha_almacen,
                    'id_guia_com' => $id_guia_com,
                    'id_operacion' => 21,//entrada por transferencia entre almacenes
                    // 'id_transferencia' => $request->id_transferencia,
                    'revisado' => 0,
                    'usuario' => $usuario->id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                    'id_mov_alm'
                );

            $detalle = json_decode($request->detalle);

            foreach($detalle as $d){
                
                $det = DB::table('almacen.guia_ven_det')
                ->select('guia_ven_det.*','mov_alm_det.valorizacion')
                ->leftJoin('almacen.mov_alm_det','mov_alm_det.id_mov_alm_det','=','guia_ven_det.id_ing_det')
                ->where([['guia_ven_det.id_guia_ven_det','=',$d->id_guia_ven_det]])
                ->first();
        
                if ($det !== null){

                    $id_guia_com_det = DB::table('almacen.guia_com_det')->insertGetId([
                        'id_guia_com' => $id_guia_com,
                        'id_producto' => $det->id_producto,
                        'cantidad' => $d->cantidad_recibida,
                        'id_unid_med' => $det->id_unid_med,
                        'id_guia_ven_det' => $d->id_guia_ven_det,
                        'usuario' => $usuario->id_usuario,
                        'estado' => 1,
                        'fecha_registro' => $fecha
                    ],
                        'id_guia_com_det'
                    );
                    
                    $series = DB::table('almacen.alm_prod_serie')
                    ->select('alm_prod_serie.serie')
                    ->where([['alm_prod_serie.id_guia_ven_det','=',$d->id_guia_ven_det],
                             ['alm_prod_serie.estado','!=',7]])
                    ->get();

                    foreach ($series as $s) {
                        //Inserta serie
                        DB::table('almacen.alm_prod_serie')->insert([
                            'id_prod' => $det->id_producto,
                            'serie' => $s->serie,
                            'estado' => 1,
                            'fecha_registro' => date('Y-m-d H:i:s'),
                            'id_guia_com_det' => $id_guia_com_det,
                            'id_almacen' => $request->id_almacen_destino
                        ]);
                    }

                    if ($d->observacion !== '' && $d->observacion !== null){
                        DB::table('almacen.guia_com_det_obs')->insertGetId([
                            'id_guia_com_det' => $id_guia_com_det,
                            'observacion' => $d->observacion,
                            'registrado_por' => $usuario->id_usuario,
                            'fecha_registro' => $fecha,
                        ],
                            'id_obs'
                        );
                    }
                    $unitario = ($det->cantidad > 0 ? ($det->valorizacion/$det->cantidad) : 0);
                    //guarda ingreso detalle
                    DB::table('almacen.mov_alm_det')->insertGetId([
                        'id_mov_alm' => $id_ingreso,
                        'id_producto' => $det->id_producto,
                        'cantidad' => $d->cantidad_recibida,
                        'valorizacion' => ($unitario * $d->cantidad_recibida),
                        'usuario' => $usuario->id_usuario,
                        'id_guia_com_det' => $id_guia_com_det,
                        'estado' => 1,
                        'fecha_registro' => $fecha,
                    ],
                        'id_mov_alm_det'
                    );
                    //Actualizo los saldos del producto
                    //Obtengo el registro de saldos
                    $ubi = DB::table('almacen.alm_prod_ubi')
                    ->where([['id_producto','=',$det->id_producto],
                            ['id_almacen','=',$request->id_almacen_destino]])
                    ->first();
                    //Traer stockActual
                    $saldo = AlmacenController::saldo_actual_almacen($det->id_producto, $request->id_almacen_destino);
                    $valor = AlmacenController::valorizacion_almacen($det->id_producto, $request->id_almacen_destino);
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
                            'id_almacen' => $request->id_almacen_destino,
                            'stock' => $saldo,
                            'valorizacion' => $valor,
                            'costo_promedio' => $cprom,
                            'estado' => 1,
                            'fecha_registro' => $fecha
                            ]);
                    }
                    
                }    
            }

            $reqs = DB::table('almacen.trans')
            ->select('trans.*','alm_req.id_tipo_requerimiento','alm_req.tipo_cliente')
            ->join('almacen.alm_req','alm_req.id_requerimiento','=','trans.id_requerimiento')
            ->where([['trans.id_guia_ven','=',$request->id_guia_ven],['trans.estado','!=',7]])
            ->get();

            foreach ($reqs as $r) {
                DB::table('almacen.trans')
                ->where('id_transferencia',$r->id_transferencia)
                ->update([  'estado' => 14,//Recibido
                            'id_guia_com' => $id_guia_com]);

                DB::table('almacen.trans_detalle')
                ->where('id_transferencia',$r->id_transferencia)
                ->update([  'estado' => 14  ]);

                $trans_det = DB::table('almacen.trans_detalle')
                ->where('id_transferencia',$r->id_transferencia)
                ->get();

                foreach ($trans_det as $det) {

                    $det_req = DB::table('almacen.alm_det_req')
                    ->where('id_detalle_requerimiento',$det->id_requerimiento_detalle)
                    ->first();

                    $suma = DB::table('almacen.trans_detalle')
                    ->where([['id_requerimiento_detalle','=',$det->id_requerimiento_detalle],
                            ['estado','=',14]])//Recibido
                    ->sum('cantidad');

                    if ($suma >= $det_req->cantidad){
                        DB::table('almacen.alm_det_req')
                        ->where('id_detalle_requerimiento',$det->id_requerimiento_detalle)
                        ->update([  'estado'=>28,//En Almacen Total
                            //'estado'=>14,//Recibido
                                    'id_almacen_reserva'=>$request->id_almacen_destino]);
                    }
                }

                $count_recibido = DB::table('almacen.alm_det_req')
                ->where([['id_requerimiento','=',$r->id_requerimiento],
                         ['estado','=',28]])
                        // ['estado','=',14]
                ->count();

                $count_todo = DB::table('almacen.alm_det_req')
                ->where([['id_requerimiento','=',$r->id_requerimiento],
                        ['tiene_transformacion','=',false],
                        ['estado','!=',7]])
                ->count();

                if ($count_recibido >= $count_todo){
                    DB::table('almacen.alm_req')
                    ->where('id_requerimiento',$r->id_requerimiento)
                    ->update(['estado'=>28]);//en atencion total
                } else {
                    DB::table('almacen.alm_req')
                    ->where('id_requerimiento',$r->id_requerimiento)
                    ->update(['estado'=>27]);//en atencion parcial
                }

                //Agrega accion en requerimiento
                DB::table('almacen.alm_req_obs')
                ->insert([  'id_requerimiento'=>$r->id_requerimiento,
                            'accion'=>'INGRESO POR TRANSFERENCIA',
                            'descripcion'=>'Ingresó al Almacén por Transferencia con Guía '.$guia_ven->serie.'-'.$guia_ven->numero,
                            'id_usuario'=>$usuario->id_usuario,
                            'fecha_registro'=>$fecha
                    ]);
            }
                // if ($r->id_requerimiento !== null) {
                //     $accion = '';

                //     if (($r->id_tipo_requerimiento == 1 && $r->tipo_cliente !== 3) ||
                //         ($r->id_tipo_requerimiento == 2) ||
                //         ($r->id_tipo_requerimiento == 3 && $r->tipo_cliente == 4)){
                            
                //         $accion = 'Reservado';
                //         DB::table('almacen.alm_req')
                //         ->where('id_requerimiento',$r->id_requerimiento)
                //         ->update(['estado'=>19]);//Reservdo
        
                //         $trans_det = DB::table('almacen.trans_detalle')
                //         ->where('id_transferencia',$r->id_transferencia)
                //         ->get();

                //         foreach ($trans_det as $det) {
                //             DB::table('almacen.alm_det_req')
                //             ->where('id_detalle_requerimiento',$det->id_requerimiento_detalle)
                //             ->update(['estado'=>19,
                //                       'id_almacen_reserva'=>$request->id_almacen_destino]);//Reservado
                //         }
                //     } 
                //     else {
                //         $accion = 'Procesado';
                //         DB::table('almacen.alm_req')
                //         ->where('id_requerimiento',$r->id_requerimiento)
                //         ->update(['estado'=>9]);//Procesado
        
                //         $trans_det = DB::table('almacen.trans_detalle')
                //         ->where('id_transferencia',$r->id_transferencia)
                //         ->get();

                //         foreach ($trans_det as $det) {
                //             DB::table('almacen.alm_det_req')
                //             ->where('id_detalle_requerimiento',$det->id_requerimiento_detalle)
                //             ->update(['estado'=>9,
                //                       'id_almacen_reserva'=>null]);//Procesado
                //         }
                //     }
                   
            DB::commit();
            return response()->json($id_ingreso);
            
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
        }
        
    }

    public function listarTransferenciasPorEnviar($alm_origen){
        
        if ($alm_origen == '0'){
            $lista = DB::table('almacen.trans')
            ->select('trans.*','alm_req.codigo as cod_req','alm_req.concepto','sede_solicita.id_sede as id_sede_destino','sede_solicita.descripcion as sede_descripcion',
            'origen.descripcion as alm_origen_descripcion','sis_usua.nombre_corto','sede_almacen.id_sede as id_sede_origen',
            'sede_almacen.descripcion as sede_almacen_descripcion','destino.descripcion as alm_destino_descripcion')
            ->join('almacen.alm_req','alm_req.id_requerimiento','=','trans.id_requerimiento')
            ->join('administracion.sis_sede as sede_solicita','sede_solicita.id_sede','=','alm_req.id_sede')
            ->join('almacen.alm_almacen as origen','origen.id_almacen','=','trans.id_almacen_origen')
            ->join('administracion.sis_sede as sede_almacen','sede_almacen.id_sede','=','origen.id_sede')
            ->join('almacen.alm_almacen as destino','destino.id_almacen','=','trans.id_almacen_destino') 
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->where([['trans.id_guia_ven','=',null],
                     ['alm_req.confirmacion_pago','=',true],
                     ['trans.estado','!=',7]]);
        } else {
            $lista = DB::table('almacen.trans')
            ->select('trans.*','alm_req.codigo as cod_req','alm_req.concepto','sede_solicita.id_sede as id_sede_destino','sede_solicita.descripcion as sede_descripcion',
            'origen.descripcion as alm_origen_descripcion','sis_usua.nombre_corto','sede_almacen.id_sede as id_sede_origen',
            'sede_almacen.descripcion as sede_almacen_descripcion','destino.descripcion as alm_destino_descripcion')
            ->join('almacen.alm_req','alm_req.id_requerimiento','=','trans.id_requerimiento')
            ->join('administracion.sis_sede as sede_solicita','sede_solicita.id_sede','=','alm_req.id_sede')
            ->join('almacen.alm_almacen as origen','origen.id_almacen','=','trans.id_almacen_origen')
            ->join('administracion.sis_sede as sede_almacen','sede_almacen.id_sede','=','origen.id_sede')
            ->join('almacen.alm_almacen as destino','destino.id_almacen','=','trans.id_almacen_destino') 
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->where([['trans.id_almacen_origen','=',$alm_origen],
                     ['trans.id_guia_ven','=',null],
                     ['alm_req.confirmacion_pago','=',true],
                     ['trans.estado','!=',7]]);
        }
        return datatables($lista)->toJson();
    }

    public function update_guia_transferencia(Request $request){

        try {
            DB::beginTransaction();
            // database queries here
            $id_tp_doc_almacen = 2;//guia venta
            $id_operacion = 11;//salida por transferencia
            $fecha_registro = date('Y-m-d H:i:s');
            $fecha = date('Y-m-d');
            $usuario = Auth::user()->id_usuario;

            $id_guia = DB::table('almacen.guia_ven')->insertGetId(
                [
                    'id_tp_doc_almacen' => $id_tp_doc_almacen,
                    'serie' => $request->trans_serie,
                    'numero' => $request->trans_numero,
                    'fecha_emision' => $request->fecha_emision,
                    'fecha_almacen' => $request->fecha_almacen,
                    'id_almacen' => $request->id_almacen_origen,
                    'usuario' => $usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha_registro,
                    'id_sede' => $request->id_sede,
                    'fecha_traslado' => $fecha,
                    'id_operacion' => $id_operacion,
                    // 'id_guia_com' => ($request->id_guia_com !== '' ? $request->id_guia_com : null),
                    'registrado_por' => $usuario,
                ],
                'id_guia_ven'
            );
            //cambia estado serie-numero
            if ($request->id_serie_numero !== null && $request->id_serie_numero !== ''){
                DB::table('almacen.serie_numero')
                ->where('id_serie_numero',$request->id_serie_numero)
                ->update(['estado' => 8]);//emitido -> 8
            }
            $trans_sel = null;
            if ($request->trans_seleccionadas !== null){
                $trans_sel = json_decode($request->trans_seleccionadas);
            }
            //actualizo la transferencia
            if ($trans_sel !== null){
                foreach ($trans_sel as $trans) {
                    DB::table('almacen.trans')->where('id_transferencia',$trans)
                    ->update([
                        'id_almacen_destino' => $request->id_almacen_destino,
                        'id_guia_ven' => $id_guia,
                        'responsable_origen' => $usuario,
                        'responsable_destino' => $request->responsable_destino_trans,
                        'estado' => 17,
                        'fecha_transferencia' => $fecha
                    ]);
                    DB::table('almacen.trans_detalle')
                    ->where('id_transferencia',$trans)
                    ->update(['estado' => 17]);
                }
            } else {
                DB::table('almacen.trans')->where('id_transferencia',$request->id_transferencia)
                ->update([
                    'id_almacen_destino' => $request->id_almacen_destino,
                    'id_guia_ven' => $id_guia,
                    'responsable_origen' => $usuario,
                    'responsable_destino' => $request->responsable_destino_trans,
                    'estado' => 17,
                    'fecha_transferencia' => $fecha
                ]);
                DB::table('almacen.trans_detalle')
                ->where('id_transferencia',$request->id_transferencia)
                ->update(['estado' => 17]);
            }
            //Genero la salida
            $codigo = AlmacenController::nextMovimiento(2,//salida
            $request->fecha_almacen,
            $request->id_almacen_origen);

            $id_salida = DB::table('almacen.mov_alm')->insertGetId(
                [
                    'id_almacen' => $request->id_almacen_origen,
                    'id_tp_mov' => 2,//Salidas
                    'codigo' => $codigo,
                    'fecha_emision' => $request->fecha_almacen,
                    'id_guia_ven' => $id_guia,
                    'id_transferencia' => ($request->id_transferencia!==null?$request->id_transferencia:null),
                    'id_operacion' => $id_operacion,
                    'revisado' => 0,
                    'usuario' => $usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha_registro,
                ],
                    'id_mov_alm'
                );

            if ($trans_sel !== null){
                $detalle = DB::table('almacen.trans_detalle')
                ->select('trans_detalle.*','alm_prod.id_unidad_medida','guia_com_det.id_guia_com_det',
                DB::raw('(mov_alm_det.valorizacion / mov_alm_det.cantidad) as unitario'))
                ->join('almacen.alm_prod','alm_prod.id_producto','=','trans_detalle.id_producto')
                ->leftjoin('almacen.guia_com_det','guia_com_det.id_trans_detalle','=','trans_detalle.id_trans_detalle')
                ->leftjoin('almacen.mov_alm_det','mov_alm_det.id_guia_com_det','=','guia_com_det.id_guia_com_det')
                ->whereIn('trans_detalle.id_transferencia',$trans_sel)
                ->where('trans_detalle.estado',17)
                ->get();
            } else {
                $detalle = DB::table('almacen.trans_detalle')
                ->select('trans_detalle.*','alm_prod.id_unidad_medida','guia_com_det.id_guia_com_det',
                DB::raw('(mov_alm_det.valorizacion / mov_alm_det.cantidad) as unitario'))
                ->join('almacen.alm_prod','alm_prod.id_producto','=','trans_detalle.id_producto')
                ->leftjoin('almacen.guia_com_det','guia_com_det.id_trans_detalle','=','trans_detalle.id_trans_detalle')
                ->leftjoin('almacen.mov_alm_det','mov_alm_det.id_guia_com_det','=','guia_com_det.id_guia_com_det')
                ->where([['trans_detalle.id_transferencia',$request->id_transferencia],['trans_detalle.estado','=',17]])
                ->get();
            }

            foreach($detalle as $det){
                $id_guia_ven_det = DB::table('almacen.guia_ven_det')->insertGetId(
                    [
                        'id_guia_ven' => $id_guia,
                        'id_producto' => $det->id_producto,
                        'cantidad' => $det->cantidad,
                        'id_unid_med' => $det->id_unidad_medida,
                        'id_trans_det' => $det->id_trans_detalle,
                        'estado' => 1,
                        'fecha_registro' => $fecha_registro,
                    ],
                        'id_guia_ven_det'
                    );
                //Guardo relacion guia_ven_det en las series
                if ($det->id_guia_com_det!==null){
                    DB::table('almacen.alm_prod_serie')
                    ->where([['id_guia_com_det','=',$det->id_guia_com_det],
                             ['id_prod','=',$det->id_producto],['estado','!=',7]])
                    ->update(['id_guia_ven_det'=>$id_guia_ven_det]);
                }
                //Guardo los items de la salida
                $id_det = DB::table('almacen.mov_alm_det')->insertGetId(
                    [
                        'id_mov_alm' => $id_salida,
                        'id_producto' => $det->id_producto,
                        // 'id_posicion' => $det->id_posicion,
                        'cantidad' => $det->cantidad,
                        'valorizacion' => ($det->unitario !== null ? ($det->cantidad * $det->unitario) : 0),
                        'usuario' => $usuario,
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
                        ['id_almacen','=',$request->id_almacen_origen]])
                ->first();
                //Traer stockActual
                $saldo = AlmacenController::saldo_actual_almacen($det->id_producto, $request->id_almacen_origen);
                $valor = AlmacenController::valorizacion_almacen($det->id_producto, $request->id_almacen_origen);
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
                        'id_almacen' => $request->id_almacen_origen,
                        'stock' => $saldo,
                        'valorizacion' => $valor,
                        'costo_promedio' => $cprom,
                        'estado' => 1,
                        'fecha_registro' => $fecha_registro
                        ]);
                }
            }

            $reqs = [];
            if ($trans_sel !== null){
                $reqs = DB::table('almacen.trans')
                ->select('trans.id_requerimiento','trans.id_almacen_destino')
                ->whereIn('trans.id_transferencia',$trans_sel)
                ->distinct()->get();
            } else {
                // $reqs = $request->id_requerimiento;
                array_push($reqs, $request->id_requerimiento);
            }
            //actualiza estado requerimiento: enviado
            foreach ($reqs as $req) {
                DB::table('almacen.alm_req')
                    ->where('id_requerimiento',$req->id_requerimiento)
                    ->update(['estado'=>17,
                              'id_almacen'=>$req->id_almacen_destino]);//enviado
                //actualiza estado requerimiento_detalle: enviado
                DB::table('almacen.alm_det_req')
                    ->where('id_requerimiento',$req->id_requerimiento)
                    ->update(['estado'=>17]);//enviado
                //Agrega accion en requerimiento
                DB::table('almacen.alm_req_obs')
                ->insert(['id_requerimiento'=>$req->id_requerimiento,
                    'accion'=>'SALIDA POR TRANSFERENCIA',
                    'descripcion'=>'Salió del Almacén por Transferencia con Guía '.$request->trans_serie.'-'.$request->trans_numero,
                    'id_usuario'=>$usuario,
                    'fecha_registro'=>$fecha_registro
                    ]);
            }

            DB::commit();
            return response()->json($id_salida);
            
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            // return response()->json($e);
        }
    }

    // public static function generarTransferenciaRequerimiento(Request $request){
    //     $sede = $request->id_sede;

    //     if ($request->id_tipo_requerimiento == 2 || $request->id_tipo_requerimiento == 3){
    //         $array_items = [];
    //         $array_almacen = [];

    //         foreach ($request->detalle_items as $det) {
    //             $almacen = DB::table('almacen.alm_almacen')
    //             ->select('sis_sede.id_sede')
    //             ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
    //             ->where('id_almacen',$det->id_almacen_reserva)
    //             ->first();
                
    //             if ($almacen !== null && $sede !== $almacen->id_sede){
    //                 array_push($array_items, $det);

    //                 if (!in_array($det->almacen_reserva, $array_almacen)){
    //                     array_push($array_almacen, $det->almacen_reserva);
    //                 }
    //             }
    //         }

    //         foreach ($array_almacen as $alm){
                
    //             $fecha = date('Y-m-d H:i:s');
    //             $codigo = TransferenciaController::transferencia_nextId($alm);
    //             $id_usuario = Auth::user()->id_usuario;
    //             $guardar = false;

    //             $id_trans = DB::table('almacen.trans')->insertGetId(
    //                 [
    //                     'id_almacen_origen' => $alm,
    //                     'id_almacen_destino' => $request->id_almacen_destino,
    //                     'codigo' => $codigo,
    //                     'id_requerimiento' => $request->id_requerimiento,
    //                     'id_guia_ven' => null,
    //                     'responsable_origen' => null,
    //                     'responsable_destino' => null,
    //                     'fecha_transferencia' => date('Y-m-d'),
    //                     'registrado_por' => $id_usuario,
    //                     'estado' => 1,
    //                     'fecha_registro' => $fecha
    //                 ],
    //                     'id_transferencia'
    //                 );
                
    //             foreach ($array_items as $item) {
    //                 if ($item->id_almacen_reserva == $alm){
    //                     DB::table('almacen.trans_detalle')->insert(
    //                     [
    //                         'id_transferencia' => $id_trans,
    //                         'id_producto' => $item->id_producto,
    //                         'cantidad' => $item->cantidad,
    //                         'estado' => 1,
    //                         'fecha_registro' => $fecha
    //                     ]);
    //                 }
    //             }
    //         }
    //     }
    // }

    public function listarDetalleTransferencias($id_requerimiento){
        $trans = DB::table('almacen.trans')
        ->select('trans.*','origen.descripcion as almacen_origen','destino.descripcion as almacen_destino',
        'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color','guia_com.serie as serie_com',
        'guia_com.numero as numero_com','guia_ven.serie as serie_ven','guia_ven.numero as numero_ven')
        ->join('almacen.alm_almacen as origen','origen.id_almacen','=','trans.id_almacen_origen')
        ->join('almacen.alm_almacen as destino','destino.id_almacen','=','trans.id_almacen_destino')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','trans.estado')
        ->leftJoin('almacen.guia_com','guia_com.id_guia','=','trans.id_guia_com')
        ->leftJoin('almacen.guia_ven','guia_ven.id_guia_ven','=','trans.id_guia_ven')
        ->where([['trans.id_requerimiento','=', $id_requerimiento],['trans.estado','!=',7]])
        ->get();
        $html = '';
        $i = 1;
        foreach ($trans as $t) {
            $html.='
            <tr style="background-color: lightgray;">
                <td>'.$i.'</td>
                <td>'.$t->codigo.'</td>
                <td>'.$t->almacen_origen.'</td>
                <td>'.$t->almacen_destino.'</td>
                <td>'.($t->serie_ven !== null ? ($t->serie_ven.'-'.$t->numero_ven) : '').'</td>
                <td>'.($t->serie_com !== null ? ($t->serie_com.'-'.$t->numero_com) : '').'</td>
                <td><span class="label label-'.$t->bootstrap_color.'">'.$t->estado_doc.'</span></td>
            <tr/>';
            $i++;

            $detalle = DB::table('almacen.trans_detalle')
            ->select('alm_prod.codigo','alm_prod.part_number','alm_prod.descripcion','trans_detalle.cantidad',
            'trans_detalle.estado','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','trans_detalle.id_producto')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','trans_detalle.estado')
            ->where('id_transferencia',$t->id_transferencia)
            ->get();

            foreach ($detalle as $det) {
                $html.='
                <tr>
                    <td></td>
                    <td>'.$det->codigo.'</td>
                    <td>'.$det->part_number.'</td>
                    <td colSpan="2">'.$det->descripcion.'</td>
                    <td>'.$det->cantidad.'</td>
                    <td><span class="label label-'.$det->bootstrap_color.'">'.$det->estado_doc.'</span></td>
                <tr/>';
            }
        }
        return json_encode($html);
    }

    public function listarDetalleTransferencia($id_trans){
        $detalle = DB::table('almacen.trans_detalle')
        ->select('trans_detalle.*','alm_prod.codigo','alm_prod.descripcion','alm_prod.series',
        'alm_cat_prod.descripcion as categoria','alm_subcat.descripcion as subcategoria',
        'alm_prod.part_number','alm_und_medida.abreviatura','trans.codigo as codigo_trans',
        'alm_req.codigo as codigo_req','alm_req.concepto','guia_com_det.id_guia_com_det')
        ->join('almacen.trans','trans.id_transferencia','=','trans_detalle.id_transferencia')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','trans_detalle.id_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->join('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=','alm_prod.id_categoria')
        ->join('almacen.alm_subcat','alm_subcat.id_subcategoria','=','alm_prod.id_subcategoria')
        ->join('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','trans_detalle.id_requerimiento_detalle')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
        ->leftjoin('almacen.guia_com_det','guia_com_det.id_trans_detalle','=','trans_detalle.id_trans_detalle')
        ->where([['trans_detalle.id_transferencia','=',$id_trans],
                 ['trans_detalle.estado','!=',7]])
        ->get();

        $lista_detalle = [];

        foreach ($detalle as $det) {
            $series = DB::table('almacen.alm_prod_serie')
            ->where('id_guia_com_det',$det->id_guia_com_det)
            ->get();

            array_push($lista_detalle, [
                'id_guia_com_det' => $det->id_guia_com_det,
                'codigo_trans' => $det->codigo_trans,
                'codigo_req' => $det->codigo_req,
                'concepto' => $det->concepto,
                'codigo' => $det->codigo,
                'part_number' => $det->part_number,
                'descripcion' => $det->descripcion,
                'cantidad' => $det->cantidad,
                'abreviatura' => $det->abreviatura,
                'series' => $series
            ]);
        }

        return response()->json($lista_detalle);
    }

    public function listarDetalleTransferenciasSeleccionadas(Request $request){
        $transferencias = json_decode($request->trans_seleccionadas);
        $detalle = DB::table('almacen.trans_detalle')
        ->select('trans_detalle.*','alm_req.codigo as codigo_req','alm_req.concepto',
        'alm_prod.codigo','alm_prod.part_number','alm_prod.series','alm_cat_prod.descripcion as categoria',
        'alm_subcat.descripcion as subcategoria','alm_prod.descripcion','alm_und_medida.abreviatura',
        'trans.codigo as codigo_trans','guia_com_det.id_guia_com_det')
        ->join('almacen.trans','trans.id_transferencia','=','trans_detalle.id_transferencia')
        ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'trans_detalle.id_producto')
        ->leftjoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
        ->leftjoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
        ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
        ->join('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','trans_detalle.id_requerimiento_detalle')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
        ->leftjoin('almacen.guia_com_det','guia_com_det.id_trans_detalle','=','trans_detalle.id_trans_detalle')
        ->where('trans_detalle.estado',1)
        ->whereIn('trans_detalle.id_transferencia',$transferencias)
        ->get();
        
        $lista_detalle = [];

        foreach ($detalle as $det) {
            $series = DB::table('almacen.alm_prod_serie')
            ->where('id_guia_com_det',$det->id_guia_com_det)
            ->get();

            array_push($lista_detalle, [
                'id_guia_com_det' => $det->id_guia_com_det,
                'codigo_trans' => $det->codigo_trans,
                'codigo_req' => $det->codigo_req,
                'concepto' => $det->concepto,
                'codigo' => $det->codigo,
                'part_number' => $det->part_number,
                'descripcion' => $det->descripcion,
                'cantidad' => $det->cantidad,
                'abreviatura' => $det->abreviatura,
                'series' => $series
            ]);
        }
        return response()->json($lista_detalle);
    }

    public function listarSeries($id_guia_com_det){
        $series = DB::table('almacen.alm_prod_serie')
        ->select('alm_prod_serie.*',
        DB::raw("(guia_com.serie) || '-' || (guia_com.numero) AS guia_com"))
        ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','alm_prod_serie.id_guia_com_det')
        ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
        ->where([['alm_prod_serie.id_guia_com_det','=',$id_guia_com_det],
                 ['alm_prod_serie.estado','!=',7]])
        ->get();
        return response()->json($series);
    }

    public function listarSeriesVen($id_guia_ven_det){
        $series = DB::table('almacen.alm_prod_serie')
        ->select('alm_prod_serie.*',
        DB::raw("(guia_ven.serie) || '-' || (guia_ven.numero) AS guia_ven"))
        ->join('almacen.guia_ven_det','guia_ven_det.id_guia_ven_det','=','alm_prod_serie.id_guia_ven_det')
        ->join('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
        ->where([['alm_prod_serie.id_guia_ven_det','=',$id_guia_ven_det],
                 ['alm_prod_serie.estado','!=',7]])
        ->get();
        return response()->json($series);
    }

    public static function transferencia_nextId($id_alm_origen){
        $cantidad = DB::table('almacen.trans')
        ->where([['id_almacen_origen','=',$id_alm_origen],
                ['estado','!=',7]])
        ->get()->count();
        $val = AlmacenController::leftZero(3,($cantidad + 1));
        $nextId = "Tr-".$id_alm_origen."-".$val;
        return $nextId;
    }

    public function listar_guias_compra()
    {
        $data = DB::table('almacen.guia_com')
        ->select('guia_com.*','adm_contri.razon_social','tp_ope.descripcion as operacion',
        'alm_almacen.descripcion as almacen_descripcion','mov_alm.codigo',
        DB::raw("(SELECT COUNT(*) FROM almacen.guia_com_det where
                    guia_com_det.id_guia_com = guia_com.id_guia
                    and guia_com_det.id_trans_detalle > 0
                    and guia_com_det.estado != 7) AS count_transferencias_detalle"))
        ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->leftjoin('almacen.tp_ope','tp_ope.id_operacion','=','guia_com.id_operacion')
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','guia_com.id_almacen')
        ->join('almacen.mov_alm','mov_alm.id_guia_com','=','guia_com.id_guia')
            ->where([['guia_com.estado','!=',7]])
            ->orderBy('fecha_emision','desc')
            ->get();

        $lista = [];
        foreach ($data as $d) {
            if ($d->count_transferencias_detalle == 0){
                array_push($lista, $d);
            }
        }
        $output['data'] = $lista;
        return response()->json($output);
    }

    public function verGuiaCompraTransferencia($id_guia){
        $guia = DB::table('almacen.guia_com')
        ->select('guia_com.*','alm_almacen.descripcion as almacen_descripcion',
        'tp_ope.descripcion as operacion','guia_clas.descripcion as clasificacion')
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','guia_com.id_almacen')
        ->join('almacen.tp_ope','tp_ope.id_operacion','=','guia_com.id_operacion')
        ->join('almacen.guia_clas','guia_clas.id_clasificacion','=','guia_com.id_guia_clas')
        ->where('id_guia',$id_guia)
        ->first();

        $detalle = DB::table('almacen.guia_com_det')
        ->select('guia_com_det.*','log_ord_compra.codigo as codigo_orden','alm_req.codigo as codigo_req',
        'sis_sede.descripcion as sede_req','alm_prod.codigo','alm_prod.part_number','alm_prod.descripcion',
        'alm_und_medida.abreviatura')
        ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
        ->leftjoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
        ->leftjoin('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','log_det_ord_compra.id_detalle_requerimiento')
        ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
        ->leftjoin('administracion.sis_sede','sis_sede.id_sede','=','alm_req.id_sede')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','guia_com_det.id_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->where([['guia_com_det.id_guia_com','=',$id_guia],['guia_com_det.estado','!=',7]])
        ->get();

        $lista_detalle = [];

        foreach ($detalle as $det) {
            $series = DB::table('almacen.alm_prod_serie')
            ->where('id_guia_com_det',$det->id_guia_com_det)
            ->get();

            array_push($lista_detalle, [
                'id_guia_com_det' => $det->id_guia_com_det,
                'codigo_orden' => $det->codigo_orden,
                'codigo_req' => $det->codigo_req,
                'sede_req' => $det->sede_req,
                'codigo' => $det->codigo,
                'part_number' => $det->part_number,
                'descripcion' => $det->descripcion,
                'abreviatura' => $det->abreviatura,
                'cantidad' => $det->cantidad,
                'series' => $series
            ]);
        }
        
        return response()->json(['guia'=>$guia,'detalle'=>$lista_detalle]);
    }

    public function generarTransferenciaRequerimiento($id_requerimiento){

        $req = DB::table('almacen.alm_req')
        ->where([['id_requerimiento','=',$id_requerimiento]])
        ->first();

        $valor_transformacion = ($req->estado == 10 ? true : false);

        $detalle_req = DB::table('almacen.alm_det_req')
        ->select('alm_det_req.*','alm_req.id_almacen','sis_sede.id_sede as id_sede_reserva',
        'almacen_guia.id_almacen as id_almacen_guia',
        'sede_guia.id_sede as id_sede_guia')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_det_req.id_almacen_reserva')
        ->leftjoin('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
        ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_requerimiento','=','alm_det_req.id_detalle_requerimiento')
        ->leftjoin('almacen.guia_com_det','guia_com_det.id_oc_det','=','log_det_ord_compra.id_detalle_orden')
        ->leftjoin('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
        ->leftjoin('almacen.alm_almacen as almacen_guia','almacen_guia.id_almacen','=','guia_com.id_almacen')
        ->leftjoin('administracion.sis_sede as sede_guia','sede_guia.id_sede','=','almacen_guia.id_sede')
        ->where([['alm_det_req.id_requerimiento','=',$id_requerimiento],
                ['alm_det_req.estado','!=',7],
                ['tiene_transformacion','=',$valor_transformacion]])
        ->get();

        return $detalle_req;

        

        $sede = $req->id_sede;
        $id_almacen_destino = $req->id_almacen;

        $id_trans_detalle_list=[];

        $array_items = [];
        $array_almacen = [];

        foreach ($detalle_req as $det) {
        
            if ($det->id_sede_guia !== null && $sede !== $det->id_sede_guia){
                array_push($array_items, $det);
            
                if (!in_array($det->id_almacen_guia, $array_almacen)){
                    array_push($array_almacen, $det->id_almacen_guia);
                }
            } 
            else if ($det->id_sede_reserva !== null && $sede !== $det->id_sede_reserva){
                array_push($array_items, $det);
            
                if (!in_array($det->id_almacen_reserva, $array_almacen)){
                    array_push($array_almacen, $det->id_almacen_reserva);
                }
            }
        }

        $fecha = date('Y-m-d H:i:s');
        $id_usuario = Auth::user()->id_usuario;
        $msj = '';
        
        foreach ($array_almacen as $alm){
            $codigo = TransferenciaController::transferencia_nextId($alm);
            
            if ($msj == ''){
                $msj = 'Se generó transferencia. '.$codigo;
            } else {
                $msj .= ', '.$codigo;
            }

            $id_trans = DB::table('almacen.trans')->insertGetId(
                [
                    'id_almacen_origen' => $alm,
                    'id_almacen_destino' => $id_almacen_destino,
                    'codigo' => $codigo,
                    'id_requerimiento' =>  $req->id_requerimiento,
                    'id_guia_ven' => null,
                    'responsable_origen' => null,
                    'responsable_destino' => null,
                    'fecha_transferencia' => date('Y-m-d'),
                    'registrado_por' => $id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_transferencia'
                );

            foreach ($array_items as $item) {

                $id_almacen_origen = ($det->id_almacen_guia !== null ? $det->id_almacen_guia : $det->id_almacen_reserva);
                
                if ($id_almacen_origen == $alm){
                    $id_trans_detalle_list[]= DB::table('almacen.trans_detalle')->insertGetId(
                    [
                        'id_transferencia' => $id_trans,
                        'id_producto' => $item->id_producto,
                        'cantidad' => (($item->stock_comprometido!==null && $item->stock_comprometido > 0)?$item->stock_comprometido:$item->cantidad),
                        'estado' => 1,
                        'fecha_registro' => $fecha,
                        'id_requerimiento_detalle' => $item->id_detalle_requerimiento
                    ],
                    'id_trans_detalle'
                    );

                }
            }
        }
        return response()->json($msj);
    }

    public function verRequerimiento($id){

        $req = DB::table('almacen.alm_req')
        ->select('alm_req.*','adm_estado_doc.estado_doc','sis_sede.descripcion as sede_requerimiento')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
        ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_req.id_sede')
        ->where('id_requerimiento',$id)
        ->first();

        $req_detalle = DB::table('almacen.alm_det_req')
        ->select('alm_det_req.*','sis_sede.id_sede as id_sede_reserva',
        'almacen_guia.id_almacen as id_almacen_guia','sede_guia.id_sede as id_sede_guia',
        'sede_guia.descripcion as sede_guia_descripcion','sis_sede.descripcion as sede_reserva_descripcion',
        'log_ord_compra.codigo as codigo_orden','alm_prod.codigo','alm_prod.part_number',
        'alm_prod.descripcion','alm_und_medida.abreviatura')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_det_req.id_almacen_reserva')
        ->leftjoin('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
        ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_requerimiento','=','alm_det_req.id_detalle_requerimiento')
        ->leftjoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
        ->leftjoin('almacen.guia_com_det','guia_com_det.id_oc_det','=','log_det_ord_compra.id_detalle_orden')
        ->leftjoin('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
        ->leftjoin('almacen.alm_almacen as almacen_guia','almacen_guia.id_almacen','=','guia_com.id_almacen')
        ->leftjoin('administracion.sis_sede as sede_guia','sede_guia.id_sede','=','almacen_guia.id_sede')
        ->leftjoin('almacen.alm_prod','alm_prod.id_producto','=','alm_det_req.id_producto')
        ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->where([['alm_det_req.id_requerimiento','=',$id],
                 ['alm_det_req.tiene_transformacion','=',$req->tiene_transformacion]])
        ->get();

        $array_items = [];

        foreach ($req_detalle as $det) {
        
            if (($det->id_sede_guia !== null && $req->id_sede !== $det->id_sede_guia) ||
                ($det->id_sede_reserva !== null && $req->id_sede !== $det->id_sede_reserva)){
                array_push($array_items, $det);
            }
        }

        return response()->json(['requerimiento'=>$req,'detalle'=>$array_items]);
    }
}
