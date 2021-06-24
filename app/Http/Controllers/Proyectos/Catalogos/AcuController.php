<?php

namespace App\Http\Controllers\Proyectos\Catalogos;

use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Proyectos\Variables\CategoriaAcuController;
use App\Http\Controllers\Proyectos\Variables\IuController;
use App\Http\Controllers\Proyectos\Variables\TipoInsumoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcuController extends Controller
{
    function view_acu(){
        $unidades = AlmacenController::mostrar_unidades_cbo();
        $tipos = TipoInsumoController::mostrar_tipos_insumos_cbo();
        $ius = IuController::mostrar_ius_cbo();
        $categorias = CategoriaAcuController::select_categorias_acus();
        return view('proyectos/acu/acu', compact('unidades','tipos','ius','categorias'));
    }
    public function listar_acus()
    {
        $data = DB::table('proyectos.proy_cu_partida')
            ->select('proy_cu_partida.*','proy_cu.codigo','proy_cu.descripcion','proy_cu.observacion',
            'alm_und_medida.abreviatura','proy_cu_cat.descripcion as cat_descripcion','proy_cu.id_categoria',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->leftjoin('proyectos.proy_cu_cat','proy_cu_cat.id_categoria','=','proy_cu.id_categoria')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu_partida.unid_medida')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','proy_cu_partida.estado')
            ->where([['proy_cu_partida.estado', '=', 1]])
            ->orderBy('proy_cu_partida.id_cu')
            ->get();

        $lista = [];
        foreach($data as $d){
            $presupuestos = DB::table('proyectos.proy_cu_partida')
            ->select('proy_presup.codigo')
            ->leftjoin('proyectos.proy_cd_partida','proy_cd_partida.id_cu_partida','=','proy_cu_partida.id_cu_partida')
            ->leftjoin('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
            ->where([['proy_cu_partida.id_cu_partida', '=', $d->id_cu_partida],
                     ['proy_cd_partida.estado','!=',7],
                     ['proy_presup.estado','!=',7]])
                    ->distinct()
                    ->get();
            
            $nro_pres = $this->valida_acu_editar($d->id_cu_partida);
            $cod_pres = '';
            if (isset($presupuestos)){
                foreach($presupuestos as $p){
                    if ($cod_pres !== ''){
                        $cod_pres .= ', '.$p->codigo;
                    } else {
                        $cod_pres .= $p->codigo;
                    }
                }
            }
            $nuevo = [
                'id_cu_partida'=>$d->id_cu_partida,
                'id_cu'=>$d->id_cu,
                'id_categoria'=>$d->id_categoria,
                'cat_descripcion'=>$d->cat_descripcion,
                'codigo'=>$d->codigo,
                'descripcion'=>$d->descripcion,
                'rendimiento'=>$d->rendimiento,
                'unid_medida'=>$d->unid_medida,
                'abreviatura'=>$d->abreviatura,
                'total'=>$d->total,
                'observacion'=>$d->observacion,
                'estado_doc'=>$d->estado_doc,
                'bootstrap_color'=>$d->bootstrap_color,
                'presupuestos'=>$cod_pres,
                'nro_pres'=>$nro_pres
            ];
            array_push($lista,$nuevo);
        }
        $output['data'] = $lista;
        return response()->json($output);
    }
    //Partidas cu_partida
    public function listar_acus_sin_presup()
    {
        $data = DB::table('proyectos.proy_cu_partida')
            ->select('proy_cu_partida.*','proy_cu.codigo','proy_cu.descripcion','proy_cu.observacion',
            'alm_und_medida.abreviatura','proy_cu_cat.descripcion as cat_descripcion','proy_cu.id_categoria',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->leftjoin('proyectos.proy_cu_cat','proy_cu_cat.id_categoria','=','proy_cu.id_categoria')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu_partida.unid_medida')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','proy_cu_partida.estado')
            ->where([['proy_cu_partida.estado', '=', 1]])
            ->get();

        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_acu($id)
    {
        $nro_pres = $this->valida_acu_editar($id);
        $acu = '';
        $detalle = '';

        $acu = DB::table('proyectos.proy_cu_partida')
        ->select('proy_cu_partida.*', 'alm_und_medida.abreviatura','proy_cu.codigo','proy_cu.descripcion')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu_partida.unid_medida')
        ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->where([['proy_cu_partida.id_cu_partida', '=', $id]])
            ->get();

        $detalle = DB::table('proyectos.proy_cu_detalle')
            ->select('proy_cu_detalle.*', 'proy_insumo.codigo','proy_insumo.descripcion',
            'proy_insumo.tp_insumo','proy_insumo.codigo as cod_insumo',
            'alm_und_medida.abreviatura','proy_tp_insumo.codigo as cod_tp_insumo')
            ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
            ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_cu_detalle.id_cu_partida', '=', $id],
                     ['proy_cu_detalle.estado','!=',7]])
            ->get();
            
        return response()->json(['acu'=>$acu,'detalle'=>$detalle,'nro_pres'=>$nro_pres]);
    }

    public function listar_acu_detalle($id){
        $detalle = DB::table('proyectos.proy_cu_detalle')
            ->select('proy_cu_detalle.*', 'proy_insumo.codigo','proy_insumo.descripcion',
            'proy_insumo.tp_insumo','proy_insumo.codigo as cod_insumo',
            'alm_und_medida.abreviatura','proy_tp_insumo.codigo as cod_tp_insumo')
            ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
            ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_cu_detalle.id_cu_partida', '=', $id],
                     ['proy_cu_detalle.estado','=',1]])
            ->get();
        return response()->json($detalle);
    }

    public function guardar_acu(Request $request)
    {
        try{
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $id_cu_partida = 0;
            $partida = null;
    
            $id_cu_partida = DB::table('proyectos.proy_cu_partida')->insertGetId(
            [
                'id_cu' => $request->id_cu,
                'unid_medida' => $request->unid_medida,
                'total' => $request->total_acu,
                'rendimiento' => $request->rendimiento,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'usuario_registro' => $id_usuario,
            ],
                'id_cu_partida'
            );
    
            $insumos = json_decode($request->insumos);

            foreach ($insumos as $ins) {
                DB::table('proyectos.proy_cu_detalle')->insert(
                    [
                        'id_cu_partida' => $id_cu_partida,
                        'id_insumo' => $ins->id_insumo,
                        // 'id_precio' => $id_precio,
                        'cantidad' => $ins->cantidad,
                        'cuadrilla' => $ins->cuadrilla,
                        'precio_unit' => $ins->unitario,
                        'precio_total' => $ins->total,
                        'fecha_registro' => date('Y-m-d H:i:s'),
                        'estado' => 1
                    ]
                );
            }
            $partida = DB::table('proyectos.proy_cu_partida')
            ->select('proy_cu_partida.*','proy_cu.codigo','proy_cu.descripcion','alm_und_medida.abreviatura')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu_partida.unid_medida')
            ->where('id_cu_partida',$id_cu_partida)->first();

            DB::commit();
            return response()->json(['id_cu_partida'=>$id_cu_partida,'partida'=>$partida]);

        } catch (\PDOException $e) {
            DB::rollBack();
        }
        
    }

    //update_acu
    public function update_acu(Request $request)
    {
        try{
            DB::beginTransaction();

            DB::table('proyectos.proy_cu_partida')->where('id_cu_partida', $request->id_cu_partida)
                ->update([
                    // 'descripcion' => strtoupper($request->descripcion),
                    // 'id_categoria' => $request->id_categoria,
                    'id_cu' => $request->id_cu,
                    'unid_medida' => $request->unid_medida,
                    'total' => $request->total_acu,
                    'rendimiento' => $request->rendimiento,
                ]);
    
            $insumos = json_decode($request->insumos);

            foreach ($insumos as $ins) {
                if ($ins->id_cu_detalle == '0'){
                    $update = DB::table('proyectos.proy_cu_detalle')->insert(
                        [
                            'id_cu_partida' => $request->id_cu_partida,
                            'id_insumo' => $ins->id_insumo,
                            'cantidad' => $ins->cantidad,
                            'cuadrilla' => $ins->cuadrilla,
                            'precio_unit' => $ins->unitario,
                            'precio_total' => $ins->total,
                            'fecha_registro' => date('Y-m-d H:i:s'),
                            'estado' => 1
                        ]
                    );
                }
                else {
                    $update = DB::table('proyectos.proy_cu_detalle')
                    ->where('id_cu_detalle',$ins->id_cu_detalle)
                    ->update([
                            'cantidad' => $ins->cantidad,
                            'cuadrilla' => $ins->cuadrilla,
                            'precio_unit' => $ins->unitario,
                            'precio_total' => $ins->total,
                        ]
                    );
                }
            }
            $elim = explode(',',$request->det_eliminados);
            $count1 = count($elim);
    
            if (!empty($request->det_eliminados)){
    
                for ($i=0; $i<$count1; $i++){
                    $id_eli = $elim[$i];
                    $update = DB::table('proyectos.proy_cu_detalle')
                    ->where('id_cu_detalle',$id_eli)
                    ->update([ 'estado' => 7 ]);
                }
            }

            $partida = DB::table('proyectos.proy_cu_partida')
            ->select('proy_cu_partida.*','proy_cu.codigo','proy_cu.descripcion','alm_und_medida.abreviatura')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu_partida.unid_medida')
            ->where('id_cu_partida',$request->id_cu_partida)->first();

            DB::commit();
            return response()->json(['id_cu_partida'=>$request->id_cu_partida,'partida'=>$partida]);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function anular_acu($id){
        $data = DB::table('proyectos.proy_cu_partida')->where('id_cu_partida', $id)
            ->update([ 'estado' => 7 ]);
        DB::table('proyectos.proy_cu_detalle')->where('id_cu_partida', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }

    public function valida_acu_editar($id_cu_partida){
        $nro_pres = DB::table('proyectos.proy_cu_partida')
            ->select('proy_presup.codigo')
            ->join('proyectos.proy_cd_partida','proy_cd_partida.id_cu_partida','=','proy_cu_partida.id_cu_partida')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
            ->where([['proy_cu_partida.id_cu_partida','=',$id_cu_partida],
                     ['proy_cd_partida.estado','!=',7],
                     ['proy_presup.estado','!=',1],
                     ['proy_presup.estado','!=',7]])
                    ->distinct()
                    ->count();
        return $nro_pres;
    }
    
    public function partida_insumos_precio($id,$id_insumo)
    {
        $cd_insumos = DB::table('proyectos.proy_presup')
        ->select('proy_cu_detalle.cantidad as cantidad_cu','proy_cu_detalle.precio_unit',
        'proy_cu.codigo','proy_cu.descripcion','alm_und_medida.abreviatura',
        'proy_cd_partida.cantidad as cantidad_partida','proy_insumo.id_insumo',
        DB::raw('(proy_cu_detalle.cantidad * proy_cd_partida.cantidad) as cantidad'),
        DB::raw('(proy_cu_detalle.precio_total * proy_cd_partida.cantidad) as importe_parcial'))
        ->join('proyectos.proy_cd_partida','proy_cd_partida.id_cd','=','proy_presup.id_presupuesto')
        ->join('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
        ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
        ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu_partida','=','proy_cu_partida.id_cu_partida')
        ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_presup.id_presupuesto','=',$id],
                    ['proy_cd_partida.estado','=',1],
                    ['proy_cu_detalle.id_insumo','=',$id_insumo],
                    ['proy_cu_detalle.estado','=',1]])
            ->get();

        $insumo = DB::table('proyectos.proy_insumo')
        ->where('id_insumo',$id_insumo)->first();
        return response()->json(['cd_insumos'=>$cd_insumos,'descripcion_insumo'=>$insumo->codigo.' - '.$insumo->descripcion]);
    }
    
    public function mostrar_presupuestos_acu($id_cu)
    {
        $proy_cd = DB::table('proyectos.proy_cd_partida')
            ->select('proy_presup.id_presupuesto', 'proy_presup.codigo', 'proy_presup.id_tp_presupuesto',
                     'proy_op_com.descripcion','adm_contri.nro_documento','adm_contri.razon_social',
                     'adm_estado_doc.estado_doc','proy_presup.fecha_emision','proy_tp_pres.descripcion as des_tipo',
                     'proy_presup_importe.sub_total','sis_moneda.simbolo')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','proy_presup.estado')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('proyectos.proy_tp_pres','proy_tp_pres.id_tp_pres','=','proy_presup.id_tp_presupuesto')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
                ->where([['proy_cd_partida.id_cu_partida', '=', $id_cu],
                         ['proy_cd_partida.estado', '=', 1]])
                ->distinct();

        $proy_ci = DB::table('proyectos.proy_ci_detalle')
            ->select('proy_presup.id_presupuesto', 'proy_presup.codigo', 'proy_presup.id_tp_presupuesto',
                     'proy_op_com.descripcion','adm_contri.nro_documento','adm_contri.razon_social',
                     'adm_estado_doc.estado_doc','proy_presup.fecha_emision','proy_tp_pres.descripcion as des_tipo',
                     'proy_presup_importe.sub_total','sis_moneda.simbolo')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_ci_detalle.id_ci')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','proy_presup.estado')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('proyectos.proy_tp_pres','proy_tp_pres.id_tp_pres','=','proy_presup.id_tp_presupuesto')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
                ->where([['proy_ci_detalle.id_cu_partida', '=', $id_cu],
                         ['proy_ci_detalle.estado', '=', 1]])
                ->distinct()
                ->unionAll($proy_cd);

        $proy_gg = DB::table('proyectos.proy_gg_detalle')
            ->select('proy_presup.id_presupuesto', 'proy_presup.codigo', 'proy_presup.id_tp_presupuesto',
                     'proy_op_com.descripcion','adm_contri.nro_documento','adm_contri.razon_social',
                     'adm_estado_doc.estado_doc','proy_presup.fecha_emision','proy_tp_pres.descripcion as des_tipo',
                     'proy_presup_importe.sub_total','sis_moneda.simbolo')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_gg_detalle.id_gg')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','proy_presup.estado')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('proyectos.proy_tp_pres','proy_tp_pres.id_tp_pres','=','proy_presup.id_tp_presupuesto')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
                ->where([['proy_gg_detalle.id_cu_partida', '=', $id_cu],
                        ['proy_gg_detalle.estado', '=', 1]])
                ->distinct()
                ->unionAll($proy_ci)
                ->get()
                ->toArray();

        // $resultado = array_map("unserialize", array_unique(array_map("serialize", $proy_gg)));
 
        return $proy_gg;
    }
}
