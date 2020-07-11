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

    public function listar_transferencias_pendientes($destino){
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
        'guia_ven.id_guia_com as guia_ingreso_compra','mov_alm.id_mov_alm as id_salida',
        'log_ord_compra.codigo as codigo_orden','alm_req.codigo as codigo_req',
        'alm_req.concepto as concepto_req')
        ->leftJoin('almacen.mov_alm','mov_alm.id_guia_ven','=','trans.id_guia_ven')
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
        ->leftJoin('almacen.guia_com','guia_com.id_guia','=','trans.id_guia_com')
        ->join('almacen.alm_almacen as alm_origen','alm_origen.id_almacen','=','trans.id_almacen_origen')
        ->leftJoin('almacen.alm_almacen as alm_destino','alm_destino.id_almacen','=','trans.id_almacen_destino')
        ->leftJoin('configuracion.sis_usua as usu_origen','usu_origen.id_usuario','=','trans.responsable_origen')
        ->leftJoin('configuracion.sis_usua as usu_destino','usu_destino.id_usuario','=','trans.responsable_destino')
        ->join('configuracion.sis_usua as usu_registro','usu_registro.id_usuario','=','trans.registrado_por')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','trans.estado')
        ->where([['trans.id_almacen_destino','=',$destino],
                 ['trans.estado','=',1]])
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    
    public function listar_transferencias_recibidas($destino){
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
        'alm_req.concepto as concepto_req')
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
        ->leftJoin('almacen.guia_com','guia_com.id_guia','=','trans.id_guia_com')
        ->join('almacen.alm_almacen as alm_origen','alm_origen.id_almacen','=','trans.id_almacen_origen')
        ->leftJoin('almacen.alm_almacen as alm_destino','alm_destino.id_almacen','=','trans.id_almacen_destino')
        ->leftJoin('configuracion.sis_usua as usu_origen','usu_origen.id_usuario','=','trans.responsable_origen')
        ->leftJoin('configuracion.sis_usua as usu_destino','usu_destino.id_usuario','=','trans.responsable_destino')
        ->join('configuracion.sis_usua as usu_registro','usu_registro.id_usuario','=','trans.registrado_por')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','trans.estado')
        ->where([['trans.id_almacen_destino','=',$destino],['trans.estado','=',14]])
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function listar_transferencia_detalle($id_transferencia){
        $detalle = DB::table('almacen.guia_ven_det')
        ->select('guia_ven_det.*','alm_prod.codigo','alm_prod.descripcion','alm_und_medida.abreviatura',
        'alm_prod.part_number','alm_cat_prod.descripcion as categoria','alm_subcat.descripcion as subcategoria')
        ->join('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
        ->join('almacen.trans','trans.id_guia_ven','=','guia_ven.id_guia_ven')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','guia_ven_det.id_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->join('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=','alm_prod.id_categoria')
        ->join('almacen.alm_subcat','alm_subcat.id_subcategoria','=','alm_prod.id_subcategoria')
        ->where([['trans.id_transferencia','=',$id_transferencia],
                ['guia_ven_det.estado','!=',7]])
        ->get();
        
        $trans = DB::table('almacen.trans')
        ->where('id_transferencia',$id_transferencia)
        ->first();

        //listar posiciones que no estan enlazadas con ningun producto
        $posiciones = DB::table('almacen.alm_ubi_posicion')
        ->select('alm_ubi_posicion.id_posicion','alm_ubi_posicion.codigo')
        ->leftjoin('almacen.alm_prod_ubi','alm_prod_ubi.id_posicion','=','alm_ubi_posicion.id_posicion')
        ->leftjoin('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
        ->leftjoin('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
        ->where([['alm_prod_ubi.id_posicion','=',null],
                ['alm_ubi_posicion.estado','=',1],
                ['alm_almacen.id_almacen','=',$trans->id_almacen_destino]])
        ->get();

        $html = '';
        foreach($detalle as $d){
            $o = false;
            //jalar posicion relacionada con el producto
            $posicion = DB::table('almacen.alm_prod_ubi')
            ->select('alm_ubi_posicion.id_posicion','alm_ubi_posicion.codigo')
            ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            // ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_prod_ubi.id_producto','=',$d->id_producto],
                    ['alm_prod_ubi.estado','=',1],
                    ['alm_ubi_estante.id_almacen','=',$trans->id_almacen_destino]])
            ->get();
            $count = count($posicion);
            if ($count > 0){
                $posiciones = $posicion;
                $o = true;
            }
            $guia_com_det = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*')
            ->where([['guia_com_det.id_guia_ven_det','=',$d->id_guia_ven_det], 
                    ['guia_com_det.estado','=',1]])
            ->first();

            $nueva_cant = $d->cantidad;

            if ($guia_com_det !== null && $guia_com_det->cantidad !== null){
                $nueva_cant = $d->cantidad - $guia_com_det->cantidad;
            }

            // if ($agrega){

                $html.='
                <tr id="'.$d->id_guia_ven_det.'">
                    <td><input type="checkbox" checked change="onCheck('.$d->id_guia_ven_det.');"/></td>
                    <td>'.$d->codigo.'</td>
                    <td>'.$d->part_number.'</td>
                    <td>'.$d->categoria.'</td>
                    <td>'.$d->subcategoria.'</td>
                    <td>'.$d->descripcion.'</td>
                    <td>'.$d->cantidad.'</td>
                    <td><input type="number" class="input-data right" style="width:80px;" name="cantidad_recibida" value="'.$nueva_cant.'" max="'.$nueva_cant.'"/></td>
                    <td>'.$d->abreviatura.'</td>
                    <td>
                        <select class="input-data" name="id_posicion">
                            <option value="0">Elija una opción</option>';
                            foreach ($posiciones as $row) {
                                if ($o){
                                    $html.='<option value="'.$row->id_posicion.'" selected>'.$row->codigo.'</option>';
                                } else {
                                    $html.='<option value="'.$row->id_posicion.'">'.$row->codigo.'</option>';
                                }
                            }
                        $html.='</select>
                    </td>
                    <td><input type="text" class="input-data" name="observacion"/></td>
                </tr>
                ';
            // }
        }
        return json_encode($html);
        // return response()->json($detalle);
    }

    public function anular_transferencia_ingreso(Request $request){
        try {
            DB::beginTransaction();

            $ing = DB::table('almacen.mov_alm')
                ->select('mov_alm.*','log_ord_compra.id_requerimiento','orden_despacho.id_od')
                ->leftJoin('almacen.trans','trans.id_transferencia','=','mov_alm.id_transferencia')
                ->leftJoin('almacen.guia_ven', function($join)
                {   $join->on('guia_ven.id_guia_ven', '=', 'trans.id_guia_ven');
                    $join->where('guia_ven.estado','!=', 7);
                })
                // ->leftJoin('almacen.guia_ven','guia_ven.id_guia_ven','=','trans.id_guia_ven')
                ->leftJoin('almacen.guia_com', function($join)
                {   $join->on('guia_com.id_guia', '=', 'guia_ven.id_guia_com');
                    $join->where('guia_com.estado','!=', 7);
                })
                // ->leftJoin('almacen.guia_com','guia_com.id_guia','=','guia_ven.id_guia_com')
                ->leftJoin('logistica.log_ord_compra', function($join)
                {   $join->on('log_ord_compra.id_orden_compra', '=', 'guia_com.id_oc');
                    $join->where('log_ord_compra.estado','!=', 7);
                })
                // ->leftJoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','guia_com.id_oc')
                ->leftJoin('almacen.orden_despacho', function($join)
                {   $join->on('orden_despacho.id_requerimiento', '=', 'log_ord_compra.id_requerimiento');
                    $join->where('orden_despacho.estado','!=', 7);
                })
                // ->leftJoin('almacen.orden_despacho','orden_despacho.id_requerimiento','=','log_ord_compra.id_requerimiento')
                    ->where([['mov_alm.id_mov_alm', '=', $request->id_mov_alm]])//ingreso
                    ->first();

            $msj = '';
            //si el ingreso no esta revisado
            if ($ing->revisado == 0){

                if ($ing->id_od == null){
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
                    //Transferencia cambia estado elaborado
                    $data = DB::table('almacen.trans')
                        ->where('id_transferencia',$request->id_transferencia)
                        ->update([ 'estado' => 1,
                                   'id_guia_com' => null ]);
                    //Si existe un requerimiento
                    if ($ing->id_requerimiento !== null){
                        //Requerimiento regresa a enviado
                        DB::table('almacen.alm_req')
                        ->where('id_requerimiento',$ing->id_requerimiento)
                        ->update(['estado'=>17]);//Enviado

                        DB::table('almacen.alm_det_req')
                        ->where('id_requerimiento',$ing->id_requerimiento)
                        ->update(['estado'=>17]);//Enviado
                        $id_usuario = Auth::user()->id_usuario;
                        //Agrega accion en requerimiento
                        DB::table('almacen.alm_req_obs')
                        ->insert([  'id_requerimiento'=>$ing->id_requerimiento,
                                    'accion'=>'INGRESO ANULADO',
                                    'descripcion'=>'Ingreso por Transferencia Anulado. Requerimiento regresa a Enviado.',
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
                ->select('mov_alm.*','log_ord_compra.id_requerimiento',
                'guia_com.id_almacen as almacen_compra','trans.id_guia_com')
                ->leftJoin('almacen.trans','trans.id_transferencia','=','mov_alm.id_transferencia')
                ->leftJoin('almacen.guia_ven', function($join)
                {   $join->on('guia_ven.id_guia_ven', '=', 'trans.id_guia_ven');
                    $join->where('guia_ven.estado','!=', 7);
                })
                // ->leftJoin('almacen.guia_ven','guia_ven.id_guia_ven','=','trans.id_guia_ven')
                ->leftJoin('almacen.guia_com', function($join)
                {   $join->on('guia_com.id_guia', '=', 'guia_ven.id_guia_com');
                    $join->where('guia_com.estado','!=', 7);
                })
                // ->leftJoin('almacen.guia_com','guia_com.id_guia','=','guia_ven.id_guia_com')
                ->leftJoin('logistica.log_ord_compra', function($join)
                {   $join->on('log_ord_compra.id_orden_compra', '=', 'guia_com.id_oc');
                    $join->where('log_ord_compra.estado','!=', 7);
                })
                // ->leftJoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','guia_com.id_oc')
                    ->where([['mov_alm.id_mov_alm', '=', $request->id_salida]])//salida
                    ->first();

            $msj = '';
            //si el salida no esta revisado
            if ($sal->revisado == 0){

                if ($sal->id_guia_com == null){
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
                    //Transferencia cambia estado anula
                    $data = DB::table('almacen.trans')
                        ->where('id_transferencia',$request->id_transferencia)
                        ->update([ 'estado' => 7 ]);
                    //Si existe un requerimiento
                    if ($sal->id_requerimiento !== null){
                        //Requerimiento regresa a Reservado
                        DB::table('almacen.alm_req')
                        ->where('id_requerimiento',$sal->id_requerimiento)
                        ->update(['estado'=>19]);//Reservado

                        DB::table('almacen.alm_det_req')
                        ->where('id_requerimiento',$sal->id_requerimiento)
                        ->update(['estado'=>19,
                                  'id_almacen_reserva'=>$sal->almacen_compra]);//Reservado
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

            DB::table('almacen.trans')->where('id_transferencia',$request->id_transferencia)
            ->update(['responsable_destino'=>$request->responsable_destino]);

            $guia_ven = DB::table('almacen.guia_ven')
            ->select('guia_ven.*','adm_empresa.id_contribuyente as empresa_contribuyente',
            'log_prove.id_proveedor as empresa_proveedor','com_cliente.id_contribuyente as cliente_contribuyente',
            'prove_cliente.id_proveedor as cliente_proveedor','log_ord_compra.id_requerimiento',
            'alm_req.id_tipo_requerimiento')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','guia_ven.id_sede')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
            ->leftJoin('logistica.log_prove','log_prove.id_contribuyente','=','adm_empresa.id_contribuyente')
            ->leftJoin('comercial.com_cliente','com_cliente.id_cliente','=','guia_ven.id_cliente')
            ->leftJoin('logistica.log_prove as prove_cliente','prove_cliente.id_contribuyente','=','com_cliente.id_contribuyente')
            // ->leftJoin('almacen.mov_alm','mov_alm.id_guia_ven','=','guia_ven.id_guia_ven')
            ->leftJoin('almacen.guia_com','guia_com.id_guia','=','guia_ven.id_guia_com')
            ->leftJoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','guia_com.id_oc')
            ->leftJoin('almacen.alm_req','alm_req.id_requerimiento','=','log_ord_compra.id_requerimiento')
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
                    'id_transferencia' => $request->id_transferencia,
                    'revisado' => 0,
                    'usuario' => $usuario->id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                    'id_mov_alm'
                );

            // $guia_det = explode(',',$request->id_guia_ven_det);
            // $cant_recibida = explode(',',$request->cantidad_recibida);
            // $observacion = explode(',',$request->observacion);
            // $ubicaciones = explode(',',$request->ubicaciones);
            // $count = count($guia_det);
            $detalle = json_decode($request->detalle);

            // if (!empty($request->id_guia_ven_det)){
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
                            'id_posicion' => (($d->ubicacion !== '' && $d->ubicacion !== '0' && $d->ubicacion !== null) ? $d->ubicacion : null),
                            'cantidad' => $d->cantidad_recibida,
                            'id_unid_med' => $det->id_unid_med,
                            'id_guia_ven_det' => $d->id_guia_ven_det,
                            // 'unitario' => $det->unitario,
                            // 'unitario_adicional' => 0,
                            // 'total' => $det->total,
                            'usuario' => $usuario->id_usuario,
                            'estado' => 1,
                            'fecha_registro' => $fecha
                        ],
                            'id_guia_com_det'
                        );
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
                            'id_posicion' => (($d->ubicacion !== '' && $d->ubicacion !== '0' && $d->ubicacion !== null) ? $d->ubicacion : null),
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
                    // if ($d->ubicacion !== null){
                        
                    //     $ubi = DB::table('almacen.alm_prod_ubi')
                    //         ->where([['id_producto','=',$det->id_producto],
                    //                 ['id_posicion','=',$ubicaciones[$i]]])
                    //         ->first();
                    //     //traer stockActual
                    //     $saldo = $this->saldo_actual($det->id_producto, $ubicaciones[$i]);
                    //     $costo = $this->costo_promedio($det->id_producto, $ubicaciones[$i]);
        
                    //     if (!isset($ubi->id_posicion)){//si no existe -> creo la ubicacion
                    //         DB::table('almacen.alm_prod_ubi')->insert([
                    //             'id_producto' => $det->id_producto,
                    //             'id_posicion' => $ubicaciones[$i],
                    //             'stock' => $saldo,
                    //             'costo_promedio' => $costo,
                    //             'estado' => 1,
                    //             'fecha_registro' => $fecha
                    //             ]);
                    //     } else {
                    //         DB::table('almacen.alm_prod_ubi')
                    //         ->where('id_prod_ubi',$ubi->id_prod_ubi)
                    //         ->update([  'stock' => $saldo,
                    //                     'costo_promedio' => $costo ]);
                    //     }
                    // }

                    
            // }

            // if ($request->estado == 'true'){
            //     $est = 5;//Atendido
            // } else {
            //     $est = 14;//Atención Parcial
            // }
            $id_trans = DB::table('almacen.trans')
            ->where('id_transferencia',$request->id_transferencia)
            ->update(['estado' => 14,//Recibido
                      'id_guia_com' => $id_guia_com
                    ]);

            if ($guia_ven->id_requerimiento !== null) {
                $accion = '';
                if ($guia_ven->id_tipo_requerimiento == 1){
                    $accion = 'Reservado';
                    DB::table('almacen.alm_req')
                    ->where('id_requerimiento',$guia_ven->id_requerimiento)
                    ->update(['estado'=>19]);//Reservdo
    
                    DB::table('almacen.alm_det_req')
                    ->where('id_requerimiento',$guia_ven->id_requerimiento)
                    ->update(['estado'=>19,
                              'id_almacen_reserva'=>$request->id_almacen_destino]);//Reservado
                } 
                else if ($guia_ven->id_tipo_requerimiento == 3){
                    $accion = 'Procesado';
                    DB::table('almacen.alm_req')
                    ->where('id_requerimiento',$guia_ven->id_requerimiento)
                    ->update(['estado'=>9]);//Procesado
    
                    DB::table('almacen.alm_det_req')
                    ->where('id_requerimiento',$guia_ven->id_requerimiento)
                    ->update(['estado'=>9,
                              'id_almacen_reserva'=>null]);//Procesado
                }
                //Agrega accion en requerimiento
                DB::table('almacen.alm_req_obs')
                ->insert([  'id_requerimiento'=>$guia_ven->id_requerimiento,
                            'accion'=>'INGRESO POR TRANSFERENCIA',
                            'descripcion'=>'Ingresó al Almacén por Transferencia con Guía '.$guia_ven->serie.'-'.$guia_ven->numero.' y fue '.$accion,
                            'id_usuario'=>$usuario->id_usuario,
                            'fecha_registro'=>$fecha
                    ]);
            }
            DB::commit();
            return response()->json($id_ingreso);
            
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            // return response()->json($e);
        }
        
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
}
