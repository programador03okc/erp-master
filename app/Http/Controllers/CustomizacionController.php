<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
date_default_timezone_set('America/Lima');

class CustomizacionController extends Controller
{
    
    public function listar_todas_transformaciones(){
        $data = DB::table('almacen.transformacion')
        ->select('transformacion.*','adm_contri.razon_social','alm_almacen.descripcion',
        'respon.nombre_corto as nombre_responsable','regist.nombre_corto as nombre_registrado',
        'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','transformacion.id_almacen')
        ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
        ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
        ->leftjoin('configuracion.sis_usua as respon','respon.id_usuario','=','transformacion.responsable')
        ->join('configuracion.sis_usua as regist','regist.id_usuario','=','transformacion.registrado_por')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','transformacion.estado')
        // ->where([['transformacion.id_almacen','=',$id_almacen]])
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function listarTransformacionesProcesadas(){
        $data = DB::table('almacen.transformacion')
        ->select('transformacion.*','alm_almacen.descripcion as almacen_descripcion',
                 'sis_usua.nombre_corto as nombre_responsable','orden_despacho.codigo as cod_od',
                 'alm_req.codigo as cod_req','guia_ven.serie','guia_ven.numero',
                 'oportunidades.codigo_oportunidad','adm_estado_doc.estado_doc','alm_almacen.id_sede',
                 'adm_estado_doc.bootstrap_color','log_prove.id_proveedor','adm_contri.razon_social')
        ->join('almacen.orden_despacho','orden_despacho.id_od','=','transformacion.id_od')
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','transformacion.id_almacen')
        ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
        ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
        ->join('logistica.log_prove','log_prove.id_contribuyente','=','adm_contri.id_contribuyente')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->join('almacen.guia_ven', function($join)
                {   $join->on('guia_ven.id_od', '=', 'transformacion.id_od');
                    $join->where('guia_ven.estado','!=', 7);
                })
        ->leftjoin('mgcp_cuadro_costos.cc','cc.id','=','transformacion.id_cc')
        ->leftjoin('mgcp_oportunidades.oportunidades','oportunidades.id','=','cc.id_oportunidad')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','transformacion.estado')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','transformacion.responsable')
        ->where([['transformacion.estado','=',9]]);
        return datatables($data)->toJson();
    }

    public function listarDetalleTransformacion($id_transformacion){
        $materias = DB::table('almacen.transfor_materia')
        ->select('transfor_materia.id_producto','transfor_materia.cantidad','transfor_materia.valor_unitario',
        'transfor_materia.valor_total','alm_prod.descripcion','alm_prod.id_unidad_medida','alm_prod.part_number',
        'alm_prod.codigo as cod_prod','alm_und_medida.abreviatura')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','transfor_materia.id_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->where('id_transformacion',$id_transformacion);

        $transformados = DB::table('almacen.transfor_transformado')
        ->select('transfor_transformado.id_producto','transfor_transformado.cantidad','transfor_transformado.valor_unitario',
        'transfor_transformado.valor_total','alm_prod.descripcion','alm_prod.id_unidad_medida','alm_prod.part_number',
        'alm_prod.codigo as cod_prod','alm_und_medida.abreviatura')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','transfor_transformado.id_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->where('id_transformacion',$id_transformacion)
        ->unionAll($materias)
        ->get();

        return response()->json($transformados);
    }

    public function mostrar_transformacion($id_transformacion){
        $data = DB::table('almacen.transformacion')
        ->select('transformacion.*','oportunidades.codigo_oportunidad',
                 'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color','sis_usua.nombre_corto',
                 'orden_despacho.codigo as cod_od','alm_almacen.descripcion as almacen_descripcion',
                 'alm_req.codigo as cod_req','guia_ven.serie','guia_ven.numero')
        ->leftjoin('almacen.orden_despacho','orden_despacho.id_od','=','transformacion.id_od')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','transformacion.id_almacen')
        ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->leftjoin('almacen.guia_ven', function($join)
                {   $join->on('guia_ven.id_od', '=', 'transformacion.id_od');
                    $join->where('guia_ven.estado','!=', 7);
                })
        ->leftjoin('mgcp_cuadro_costos.cc','cc.id','=','transformacion.id_cc')
        ->leftjoin('mgcp_oportunidades.oportunidades','oportunidades.id','=','cc.id_oportunidad')
        ->leftjoin('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','transformacion.estado')
        ->leftjoin('configuracion.sis_usua','sis_usua.id_usuario','=','transformacion.responsable')
        ->where('transformacion.id_transformacion',$id_transformacion)
        ->first();

        return response()->json($data);
    }

    public function transformacion_nextId($fecha, $id_almacen){
        $yyyy = date('Y',strtotime($fecha));
        
        $almacen = DB::table('almacen.alm_almacen')
        ->select('codigo')
        ->where('id_almacen',$id_almacen)
        ->first();

        $cantidad = DB::table('almacen.transformacion')
        ->where([['id_almacen','=',$id_almacen],['estado','!=',7]])
        ->whereYear('fecha_transformacion','=',$yyyy)
        ->get()->count();
        
        $val = AlmacenController::leftZero(3,($cantidad + 1));
        $nextId = "HT-".$almacen->codigo."-".$val;
        
        return $nextId;
    }
    public function guardar_transformacion(Request $request)
    {
        $usuario = Auth::user();
        $fecha = date('Y-m-d H:i:s');
        $codigo = $this->transformacion_nextId($request->fecha_transformacion, $request->id_almacen);
        $id_transformacion = DB::table('almacen.transformacion')->insertGetId(
            [
                'fecha_transformacion' => $request->fecha_transformacion,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'codigo' => $codigo,
                'responsable' => $request->responsable,
                'id_empresa' => $request->id_empresa,
                'id_almacen' => $request->id_almacen,
                'total_materias' => $request->total_materias,
                'total_directos' => $request->total_directos,
                'costo_primo' => $request->costo_primo,
                'total_indirectos' => $request->total_indirectos,
                'total_sobrantes' => $request->total_sobrantes,
                'costo_transformacion' => $request->costo_transformacion,
                'registrado_por' => $usuario->id_usuario,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_transformacion'
            );
        return response()->json($id_transformacion);
    }
    public function update_transformacion(Request $request)
    {
        $data = DB::table('almacen.transformacion')
            ->where('id_transformacion', $request->id_transformacion)
            ->update([
                'fecha_transformacion' => $request->fecha_transformacion,
                'serie' => $request->serie,
                'numero' => $request->numero,
                // 'codigo' => $request->codigo,
                'responsable' => $request->responsable,
                'id_empresa' => $request->id_empresa,
                'id_almacen' => $request->id_almacen,
                'total_materias' => $request->total_materias,
                'total_directos' => $request->total_directos,
                'costo_primo' => $request->costo_primo,
                'total_indirectos' => $request->total_indirectos,
                'total_sobrantes' => $request->total_sobrantes,
                'costo_transformacion' => $request->costo_transformacion
            ]);
        return response()->json($id_transformacion);
    }
    public function guardar_materia(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_materia = DB::table('almacen.transfor_materia')->insertGetId(
            [
                'id_transformacion' => $request->id_transformacion,
                'id_producto' => $request->id_producto,
                'cantidad' => $request->cantidad,
                'valor_unitario' => $request->valor_unitario,
                'valor_total' => round($request->valor_total,2,PHP_ROUND_HALF_UP),
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_materia'
            );
        return response()->json($id_materia);
    }
    public function update_materia(Request $request)
    {
        $data = DB::table('almacen.transfor_materia')
        ->where('id_materia',$request->id_materia)
        ->update([  'cantidad' => $request->cantidad,
                    'valor_unitario' => $request->valor_unitario,
                    'valor_total' => $request->valor_total,
                ]);
        return response()->json($data);
    }
    public function listar_materias($id_transformacion){
        $data = DB::table('almacen.transfor_materia')
        ->select('transfor_materia.*','alm_prod.codigo','alm_prod.descripcion',
        'alm_prod.part_number','alm_und_medida.abreviatura','alm_prod.series')
        ->leftjoin('almacen.alm_prod','alm_prod.id_producto','=','transfor_materia.id_producto')
        ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->where([['transfor_materia.id_transformacion','=',$id_transformacion],
                 ['transfor_materia.estado','=',1]])
        ->get();
        return response()->json($data);
    }

    public function anular_materia(Request $request, $id)
    {
        $data = DB::table('almacen.transfor_materia')->where('id_materia', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }

    public function guardar_directo(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_directo = DB::table('almacen.transfor_directo')->insertGetId(
            [
                'id_transformacion' => $request->id_transformacion,
                'descripcion' => $request->descripcion,
                // 'id_servicio' => $request->id_servicio,
                // 'cantidad' => $request->cantidad,
                // 'valor_unitario' => $request->valor_unitario,
                'valor_total' => round($request->valor_total,4,PHP_ROUND_HALF_UP),
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_directo'
            );
        return response()->json($id_directo);
    }
    public function update_directo(Request $request)
    {
        $data = DB::table('almacen.transfor_directo')
        ->where('id_directo',$request->id_directo)
        ->update([  'cantidad' => $request->cantidad,
                    'valor_unitario' => $request->valor_unitario,
                    'valor_total' => $request->valor_total,
                ]);
        return response()->json($data);
    }
    public function listar_directos($id_transformacion){
        $data = DB::table('almacen.transfor_directo')
        ->select('transfor_directo.*')
        // ->leftjoin('logistica.log_servi','log_servi.id_servicio','=','transfor_directo.id_servicio')
        // ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->where([['transfor_directo.id_transformacion','=',$id_transformacion],
                 ['transfor_directo.estado','=',1]])
        ->get();
        return response()->json($data);
    }
    public function anular_directo(Request $request, $id)
    {
        $data = DB::table('almacen.transfor_directo')->where('id_directo', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }

    public function guardar_indirecto(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_indirecto = DB::table('almacen.transfor_indirecto')->insertGetId(
            [
                'id_transformacion' => $request->id_transformacion,
                'cod_item' => $request->cod_item,
                'tasa' => $request->tasa,
                'parametro' => $request->parametro,
                'valor_unitario' => $request->valor_unitario,
                'valor_total' => round($request->valor_total,2,PHP_ROUND_HALF_UP),
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_indirecto'
            );
        return response()->json($id_indirecto);
    }
    public function update_indirecto(Request $request)
    {
        $data = DB::table('almacen.transfor_indirecto')
        ->where('id_indirecto',$request->id_indirecto)
        ->update([  'tasa' => $request->tasa,
                    'parametro' => $request->parametro,
                    'valor_unitario' => $request->valor_unitario,
                    'valor_total' => $request->valor_total,
                ]);
        return response()->json($data);
    }
    public function listar_indirectos($id_transformacion){
        $data = DB::table('almacen.transfor_indirecto')
        ->select('transfor_indirecto.*','log_servi.codigo','log_servi.descripcion')
        ->leftjoin('logistica.log_servi','log_servi.id_servicio','=','transfor_indirecto.cod_item')
        ->where([['transfor_indirecto.id_transformacion','=',$id_transformacion],
                 ['transfor_indirecto.estado','=',1]])
        ->get();
        return response()->json($data);
    }

    public function anular_indirecto(Request $request, $id)
    {
        $data = DB::table('almacen.transfor_indirecto')->where('id_indirecto', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }

    public function guardar_sobrante(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_sobrante = DB::table('almacen.transfor_sobrante')->insertGetId(
            [
                'id_transformacion' => $request->id_transformacion,
                'id_producto' => $request->id_producto,
                'cantidad' => $request->cantidad,
                'valor_unitario' => $request->valor_unitario,
                'valor_total' => round($request->valor_total,2,PHP_ROUND_HALF_UP),
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_sobrante'
            );
        return response()->json($id_sobrante);
    }
    public function update_sobrante(Request $request)
    {
        $data = DB::table('almacen.transfor_sobrante')
        ->where('id_sobrante',$request->id_sobrante)
        ->update([  'cantidad' => $request->cantidad,
                    'valor_unitario' => $request->valor_unitario,
                    'valor_total' => $request->valor_total,
                ]);
        return response()->json($data);
    }
    public function listar_sobrantes($id_transformacion){
        $data = DB::table('almacen.transfor_sobrante')
        ->select('transfor_sobrante.*','alm_prod.codigo','alm_prod.part_number as part_number_prod',
        'alm_prod.descripcion as descripcion_prod','alm_und_medida.abreviatura','alm_prod.series')
        ->leftjoin('almacen.alm_prod','alm_prod.id_producto','=','transfor_sobrante.id_producto')
        ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->where([['transfor_sobrante.id_transformacion','=',$id_transformacion],
                 ['transfor_sobrante.estado','=',1]])
        ->get();
        return response()->json($data);
        // $html = '';
        // $i = 1;
        // foreach ($data as $d){
        //     $html.='
        //     <tr id="sob-'.$d->id_sobrante.'">
        //         <td>'.($d->codigo!==null ? $d->codigo : '').'</td>
        //         <td>'.($d->part_number!==null ? $d->part_number : '').'</td>
        //         <td>'.($d->descripcion!== null ? $d->descripcion : '').'</td>
        //         <td><input type="number" class="input-data right" name="sob_cantidad" value="'.$d->cantidad.'" onChange="calcula_sobrante('.$d->id_sobrante.');" disabled="true"/></td>
        //         <td>'.($d->abreviatura!==null ? $d->abreviatura : '').'</td>
        //         <td><input type="number" class="input-data right" name="sob_valor_unitario" value="'.$d->valor_unitario.'" onChange="calcula_sobrante('.$d->id_sobrante.');" disabled="true"/></td>
        //         <td><input type="number" class="input-data right" name="sob_valor_total" value="'.round($d->valor_total,2,PHP_ROUND_HALF_UP).'" onChange="calcula_sobrante('.$d->id_sobrante.');" disabled="true"/></td>
        //         <td style="display:flex;">
        //             <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_sobrante('.$d->id_sobrante.');"></i>
        //             <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_sobrante('.$d->id_sobrante.');"></i>
        //             <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_sobrante('.$d->id_sobrante.');"></i>
        //         </td>
        //     </tr>
        //     ';
        //     $i++;
        // }
        // return json_encode($html);
    }
    public function anular_sobrante($id)
    {
        $data = DB::table('almacen.transfor_sobrante')->where('id_sobrante', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }

    public function guardar_transformado(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_transformado = DB::table('almacen.transfor_transformado')->insertGetId(
            [
                'id_transformacion' => $request->id_transformacion,
                'id_producto' => $request->id_producto,
                'cantidad' => $request->cantidad,
                'valor_unitario' => $request->valor_unitario,
                'valor_total' => round($request->valor_total,2,PHP_ROUND_HALF_UP),
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_transformado'
            );
        return response()->json($id_transformado);
    }
    public function update_transformado(Request $request)
    {
        $data = DB::table('almacen.transfor_transformado')
        ->where('id_transformado',$request->id_transformado)
        ->update([  'cantidad' => $request->cantidad,
                    'valor_unitario' => $request->valor_unitario,
                    'valor_total' => $request->valor_total,
                ]);
        return response()->json($data);
    }

    public function listar_transformados($id_transformacion){
        $data = DB::table('almacen.transfor_transformado')
        ->select('transfor_transformado.*','alm_prod.codigo','alm_prod.descripcion',
        'alm_prod.part_number','alm_und_medida.abreviatura','alm_prod.series')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','transfor_transformado.id_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->where([['transfor_transformado.id_transformacion','=',$id_transformacion],
                 ['transfor_transformado.estado','=',1]])
        ->get();
        return response()->json($data);
    }
    
    public function anular_transformado(Request $request, $id)
    {
        $data = DB::table('almacen.transfor_transformado')->where('id_transformado', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }

    // public function procesar_transformacion($id_transformacion){
    //     try {
    //         DB::beginTransaction();

    //         $id_usuario = Auth::user()->id_usuario;
    //         $fecha = date('Y-m-d H:i:s');
            
    //         $tra = DB::table('almacen.transformacion')
    //         ->where('id_transformacion',$id_transformacion)
    //         ->first();
            
    //         $salida = DB::table('almacen.transfor_materia')
    //         ->where([['id_transformacion','=',$id_transformacion],['estado','!=',7]])
    //         ->get();

    //         $id_salida = 0;
    //         if (count($salida) > 0){
    //             $codigo_sal = AlmacenController::nextMovimiento(2,$tra->fecha_transformacion,$tra->id_almacen);
    //             //guardar salida de almacén
    //             $id_salida = DB::table('almacen.mov_alm')->insertGetId(
    //                 [
    //                     'id_almacen' => $tra->id_almacen,
    //                     'id_tp_mov' => 2,//Salidas
    //                     'codigo' => $codigo_sal,
    //                     'fecha_emision' => $tra->fecha_transformacion,
    //                     'id_transformacion' => $id_transformacion,
    //                     'id_operacion' => 27,//Salida por servicio de producción
    //                     'revisado' => 0,
    //                     'usuario' => $id_usuario,
    //                     'estado' => 1,
    //                     'fecha_registro' => $fecha,
    //                 ],
    //                     'id_mov_alm'
    //                 );
    //             //guardar detalle de salida de almacén
    //             foreach($salida as $sal){
    //                 DB::table('almacen.mov_alm_det')->insertGetId(
    //                     [
    //                         'id_mov_alm' => $id_salida,
    //                         'id_producto' => $sal->id_producto,
    //                         // 'id_posicion' => $sal->id_posicion,
    //                         'cantidad' => $sal->cantidad,
    //                         'valorizacion' => ($sal->valor_total !== null ? $sal->valor_total : 0),
    //                         'usuario' => $id_usuario,
    //                         'estado' => 1,
    //                         'fecha_registro' => $fecha,
    //                     ],
    //                         'id_mov_alm_det'
    //                     );
    //             }
    //         }

    //         $sob = DB::table('almacen.transfor_sobrante')
    //         ->select('transfor_sobrante.id_producto','transfor_sobrante.cantidad',
    //         'transfor_sobrante.valor_unitario','transfor_sobrante.valor_total')
    //         ->where([['id_transformacion','=',$id_transformacion],['estado','!=',7]]);
            
    //         $ingreso = DB::table('almacen.transfor_transformado')
    //         ->select('transfor_transformado.id_producto','transfor_transformado.cantidad',
    //         'transfor_transformado.valor_unitario','transfor_transformado.valor_total')
    //         ->where([['id_transformacion','=',$id_transformacion],['estado','!=',7]])
    //         ->unionAll($sob)
    //         ->get()
    //         ->toArray();

    //         $id_ingreso = 0;
    //         if (count($ingreso) > 0){
    //             $codigo_ing = AlmacenController::nextMovimiento(1,$tra->fecha_transformacion,$tra->id_almacen);

    //             $id_ingreso = DB::table('almacen.mov_alm')->insertGetId(
    //                 [
    //                     'id_almacen' => $tra->id_almacen,
    //                     'id_tp_mov' => 1,//Ingresos
    //                     'codigo' => $codigo_ing,
    //                     'fecha_emision' => $tra->fecha_transformacion,
    //                     'id_transformacion' => $id_transformacion,
    //                     'id_operacion' => 26,//Entrada por servicio de producción
    //                     'revisado' => 0,
    //                     'usuario' => $id_usuario,
    //                     'estado' => 1,
    //                     'fecha_registro' => $fecha,
    //                 ],
    //                     'id_mov_alm'
    //                 );

    //             foreach($ingreso as $ing){
    //                 DB::table('almacen.mov_alm_det')->insertGetId(
    //                     [
    //                         'id_mov_alm' => $id_ingreso,
    //                         'id_producto' => $ing->id_producto,
    //                         // 'id_posicion' => $ing->id_posicion,
    //                         'cantidad' => $ing->cantidad,
    //                         'valorizacion' => ($ing->valor_total !== null ? $ing->valor_total : 0),
    //                         'usuario' => $id_usuario,
    //                         'estado' => 1,
    //                         'fecha_registro' => $fecha,
    //                     ],
    //                         'id_mov_alm_det'
    //                     );
    //             }
    //         }
    //         DB::table('almacen.transformacion')
    //         ->where('id_transformacion',$id_transformacion)
    //         ->update(['estado' => 9]);//Procesado

    //         return response()->json(['id_salida'=>$id_salida,'id_ingreso'=>$id_ingreso]);

    //         DB::commit();
    //         return response()->json($msj);
            
    //     } catch (\PDOException $e) {
    //         DB::rollBack();
    //     }
    // }
    public function anular_transformacion($id_transformacion){
        $rspta = '';
        $ing = DB::table('almacen.mov_alm')
        ->where([['id_transformacion','=',$id_transformacion],
                ['estado','=',1],['id_tp_mov','=',1]])//ingreso
        ->first();

        $sal = DB::table('almacen.mov_alm')
        ->where([['id_transformacion','=',$id_transformacion],
                ['estado','=',1],['id_tp_mov','=',2]])//salida
        ->first();

        $anula_trans = false;
        //Si existe ingreso y salida relacionado
        if (isset($ing) && isset($sal)){
            //Verifica que no esten revisado
            if ($ing->revisado == 0 && $sal->revisado == 0){
                DB::table('almacen.mov_alm')
                ->where('id_transformacion',$id_transformacion)
                ->whereIn('id_mov_alm',[ $ing->id_mov_alm, $sal->id_mov_alm ])
                ->update(['estado' => 7]);

                $det = DB::table('almacen.mov_alm_det')
                ->whereIn('mov_alm_det.id_mov_alm',[ $ing->id_mov_alm, $sal->id_mov_alm ])
                ->get();

                if (isset($det)){
                    foreach($det as $d){
                        DB::table('almacen.mov_alm_det')
                        ->where('id_mov_alm_det',$d->id_mov_alm_det)
                        ->update(['estado' => 7]);
                        $rspta = 'Se anuló correctamente....';
                    }
                }

                $anula_trans = true;
                if ($rspta == ''){
                    $rspta = 'Se anuló correctamente.';
                }
            } 
            else {
                $rspta = 'No es posible anular, su ingreso y/o salida ya fue revisada.';
            }
        }
        else {
            $anula_trans = true;
            $rspta = 'Se anuló correctamente.';
        }
        //anula la transformacion
        if ($anula_trans){
            DB::table('almacen.transformacion')
            ->where('id_transformacion',$id_transformacion)
            ->update(['estado' => 7]);
        }
        return response()->json($rspta);
    }
    
    public function listar_transformaciones(){
        $data = DB::table('almacen.transformacion')
        ->select('transformacion.*','alm_almacen.descripcion','guia_ven.serie','guia_ven.numero',
                 'alm_req.codigo as cod_req','oportunidades.codigo_oportunidad','adm_estado_doc.estado_doc',
                 'adm_estado_doc.bootstrap_color')
        ->leftjoin('almacen.orden_despacho','orden_despacho.id_od','=','transformacion.id_od')
        ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->leftjoin('almacen.guia_ven', function($join)
                {   $join->on('guia_ven.id_od', '=', 'transformacion.id_od');
                    $join->where('guia_ven.estado','!=', 7);
                })
        ->leftjoin('mgcp_cuadro_costos.cc','cc.id','=','transformacion.id_cc')
        ->leftjoin('mgcp_oportunidades.oportunidades','oportunidades.id','=','cc.id_oportunidad')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','transformacion.estado')
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','transformacion.id_almacen')
        ->where([['transformacion.estado','!=',7]])
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function id_ingreso_transformacion($id_transformacion){
        $ing = DB::table('almacen.mov_alm')
        ->where([['mov_alm.id_transformacion','=',$id_transformacion],
                ['id_tp_mov','=',1],//ingreso
                ['estado','=',1]])
        ->first();
        return response()->json($ing!==null ? $ing->id_mov_alm : null);
    }
    
    public function id_salida_transformacion($id_transformacion){
        $ing = DB::table('almacen.mov_alm')
        ->where([['mov_alm.id_transformacion','=',$id_transformacion],
                ['id_tp_mov','=',2],//salida
                ['estado','=',1]])
        ->first();
        return response()->json($ing->id_mov_alm);
    }

    public function procesar_transformacion(Request $request){
        $data = DB::table('almacen.transformacion')
        ->where('id_transformacion',$request->id_transformacion)
        ->update([  'estado'=>9,//procesado
                    'responsable'=>$request->responsable,
                    'observacion'=>$request->observacion,
                    'fecha_transformacion'=>date('Y-m-d H:i:s')
                    ]);
        return response()->json($data);
    }

    public function listarCuadrosCostos(){
        $data = DB::table('mgcp_cuadro_costos.cc')
        ->select('cc.id','cc.prioridad','cc.fecha_entrega','cc.tipo_cuadro',
        'oportunidades.codigo_oportunidad','oportunidades.oportunidad',
        'entidades.nombre','estados_aprobacion.estado','users.name')
        ->join('mgcp_oportunidades.oportunidades','oportunidades.id','=','cc.id_oportunidad')
        ->join('mgcp_acuerdo_marco.entidades','entidades.id','=','oportunidades.id_entidad')
        ->join('mgcp_cuadro_costos.estados_aprobacion','estados_aprobacion.id','=','cc.estado_aprobacion')
        ->join('mgcp_usuarios.users','users.id','=','oportunidades.id_responsable');
        // ->get();
        // return response()->json($data);
        return datatables($data)->toJson();
    }

    public function generarTransformacion(Request $request){
        
        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $fecha = date('Y-m-d H:i:s');
            $date = date('Y-m-d');

            $codigo = $this->transformacion_nextId($date, $request->id_almacen);
            
            $id_transformacion = DB::table('almacen.transformacion')->insertGetId(
                [
                    'fecha_transformacion' => $date,
                    'codigo' => $codigo,
                    'responsable' => $id_usuario,
                    'id_almacen' => $request->id_almacen,
                    'total_materias' => 0,
                    'total_directos' => 0,
                    'costo_primo' => 0,
                    'total_indirectos' => 0,
                    'total_sobrantes' => 0,
                    'costo_transformacion' => 0,
                    'registrado_por' => $id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                    'observacion' => $request->oportunidad,
                    'id_cc' => $request->id_cc,
                ],
                    'id_transformacion'
                );

            $materia_prima = json_decode($request->lista_materias);

            // if ($request->tipo == 1){
            //     $materia_prima = DB::table('mgcp_cuadro_costos.cc_am_filas')
            //     ->select('cc_am_filas.*','cc_am_proveedores.precio','cc_am_proveedores.moneda')
            //     ->join('mgcp_cuadro_costos.cc_am','cc_am.id_cc','=','cc_am_filas.id_cc_am')
            //     ->join('mgcp_cuadro_costos.cc','cc.id','=','cc_am.id_cc')
            //     ->join('mgcp_cuadro_costos.cc_am_proveedores','cc_am_proveedores.id','=','cc_am_filas.proveedor_seleccionado')
            //     ->where('cc.id',$request->id_cc)
            //     ->get();
            // } 
            // else {
            //     $materia_prima = DB::table('mgcp_cuadro_costos.cc_venta_filas')
            //     ->select('cc_venta_filas.*','cc_venta_proveedores.precio','cc_venta_proveedores.moneda')
            //     ->join('mgcp_cuadro_costos.cc_venta','cc_venta.id_cc','=','cc_venta_filas.id_cc_venta')
            //     ->join('mgcp_cuadro_costos.cc','cc.id','=','cc_venta.id_cc')
            //     ->join('mgcp_cuadro_costos.cc_venta_proveedores','cc_venta_proveedores.id','=','cc_venta_filas.proveedor_seleccionado')
            //     ->where('cc.id',$request->id_cc)
            //     ->get();
            // }
            
            foreach($materia_prima as $mat){
                DB::table('almacen.transfor_materia')->insert(
                [
                    'id_transformacion' => $id_transformacion,
                    'part_number_cc' => ($mat->part_no !== null ? $mat->part_no : ''),
                    'descripcion_cc' => $mat->descripcion,
                    'cantidad' => $mat->cantidad,
                    'valor_unitario' => $mat->unitario,
                    'valor_total' => round(($mat->cantidad * floatval($mat->unitario)),6,PHP_ROUND_HALF_UP),
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ]);
            }
            
            $servicios = json_decode($request->lista_servicios);
            // DB::table('mgcp_cuadro_costos.cc_bs_filas')
            // ->select('cc_bs_filas.*','cc_bs_proveedores.precio','cc_bs_proveedores.moneda')
            // ->join('mgcp_cuadro_costos.cc_bs','cc_bs.id_cc','=','cc_bs_filas.id_cc_bs')
            // ->join('mgcp_cuadro_costos.cc','cc.id','=','cc_bs.id_cc')
            // ->join('mgcp_cuadro_costos.cc_bs_proveedores','cc_bs_proveedores.id','=','cc_bs_filas.proveedor_seleccionado')
            // ->where('cc.id',$request->id_cc)
            // ->get();

            foreach($servicios as $ser){
                DB::table('almacen.transfor_directo')->insert(
                [
                    'id_transformacion' => $id_transformacion,
                    // 'id_servicio' => $request->id_servicio,
                    // 'part_number_cc' => $ser->part_no,
                    'descripcion' => $ser->descripcion,
                    // 'cantidad' => $ser->cantidad,
                    // 'valor_unitario' => $ser->precio,
                    'valor_total' => round($ser->total,6,PHP_ROUND_HALF_UP),
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ]);
            }

            $sobrantes = json_decode($request->lista_sobrantes);
            // DB::table('mgcp_cuadro_costos.cc_gg_filas')
            // ->select('cc_gg_filas.*')
            // ->join('mgcp_cuadro_costos.cc_gg','cc_gg.id_cc','=','cc_gg_filas.id_cc_gg')
            // ->join('mgcp_cuadro_costos.cc','cc.id','=','cc_gg.id_cc')
            // ->where('cc.id',$request->id_cc)
            // ->get();

            foreach($sobrantes as $sob){
                DB::table('almacen.transfor_sobrante')->insert(
                    [
                        'id_transformacion' => $id_transformacion,
                        // 'id_producto' => $sob->id_producto,
                        'part_number' => $sob->part_number,
                        'descripcion' => $sob->descripcion,
                        'cantidad' => $sob->cantidad,
                        'valor_unitario' => $sob->unitario,
                        'valor_total' => round(($sob->unitario * $sob->cantidad),6,PHP_ROUND_HALF_UP),
                        'estado' => 1,
                        'fecha_registro' => $fecha,
                    ]);
            }

            $transformados = json_decode($request->lista_transformados);
            foreach($transformados as $tra){
                DB::table('almacen.transfor_transformado')->insert(
                    [
                        'id_transformacion' => $id_transformacion,
                        'id_producto' => $tra->id_producto,
                        'cantidad' => $tra->cantidad,
                        'valor_unitario' => $tra->unitario,
                        'valor_total' => round(($tra->unitario * $tra->cantidad),6,PHP_ROUND_HALF_UP),
                        'estado' => 1,
                        'fecha_registro' => $fecha,
                    ]);
            }
            DB::commit();
            return response()->json("Se generó la Hoja de Transformación ".$codigo);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function obtenerCuadro($id_cc, $tipo){
        $materias_primas = [];

        if ($tipo == 1){
            $materias_primas = DB::table('mgcp_cuadro_costos.cc_am_filas')
            ->select('cc_am_filas.*','cc_am_proveedores.precio','cc_am_proveedores.moneda')
            ->join('mgcp_cuadro_costos.cc_am','cc_am.id_cc','=','cc_am_filas.id_cc_am')
            ->join('mgcp_cuadro_costos.cc','cc.id','=','cc_am.id_cc')
            ->join('mgcp_cuadro_costos.cc_am_proveedores','cc_am_proveedores.id','=','cc_am_filas.proveedor_seleccionado')
            ->where('cc.id',$id_cc)
            ->get();
        } 
        else {
            $materias_primas = DB::table('mgcp_cuadro_costos.cc_venta_filas')
            ->select('cc_venta_filas.*','cc_venta_proveedor.precio','cc_venta_proveedor.moneda')
            ->join('mgcp_cuadro_costos.cc_venta','cc_venta.id_cc','=','cc_venta_filas.id_cc_venta')
            ->join('mgcp_cuadro_costos.cc','cc.id','=','cc_venta.id_cc')
            ->join('mgcp_cuadro_costos.cc_venta_proveedor','cc_venta_proveedor.id','=','cc_venta_filas.proveedor_seleccionado')
            ->where('cc.id',$id_cc)
            ->get();
        }

        $servicios = DB::table('mgcp_cuadro_costos.cc_bs_filas')
            ->select('cc_bs_filas.*','cc_bs_proveedores.precio','cc_bs_proveedores.moneda')
            ->join('mgcp_cuadro_costos.cc_bs','cc_bs.id_cc','=','cc_bs_filas.id_cc_bs')
            ->join('mgcp_cuadro_costos.cc','cc.id','=','cc_bs.id_cc')
            ->join('mgcp_cuadro_costos.cc_bs_proveedores','cc_bs_proveedores.id','=','cc_bs_filas.proveedor_seleccionado')
            ->where('cc.id',$id_cc)
            ->get();

        $gastos = DB::table('mgcp_cuadro_costos.cc_gg_filas')
            ->select('cc_gg_filas.*')
            ->join('mgcp_cuadro_costos.cc_gg','cc_gg.id_cc','=','cc_gg_filas.id_cc_gg')
            ->join('mgcp_cuadro_costos.cc','cc.id','=','cc_gg.id_cc')
            ->where('cc.id',$id_cc)
            ->get();
        
        return response()->json(['materias_primas'=>$materias_primas,'servicios'=>$servicios, 'gastos'=>$gastos]);
    }

    public function pruebacc($id_cc){
        $materia_prima = DB::table('mgcp_cuadro_costos.cc_am_filas')
        ->select('cc_am_filas.*','cc_am_proveedores.precio','cc_am_proveedores.moneda')
        ->join('mgcp_cuadro_costos.cc_am','cc_am.id_cc','=','cc_am_filas.id_cc_am')
        ->join('mgcp_cuadro_costos.cc','cc.id','=','cc_am.id_cc')
        ->join('mgcp_cuadro_costos.cc_am_proveedores','cc_am_proveedores.id','=','cc_am_filas.proveedor_seleccionado')
        ->where('cc.id',$id_cc)
        ->get();
        return $materia_prima;
    }
}
