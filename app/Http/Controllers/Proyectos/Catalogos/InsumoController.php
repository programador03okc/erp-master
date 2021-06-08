<?php

namespace App\Http\Controllers\Proyectos\Catalogos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class InsumoController extends Controller
{
    
    function view_insumo(){
        $tipos = $this->mostrar_tipos_insumos_cbo();
        $unidades = $this->mostrar_unidades_cbo();
        $ius = $this->mostrar_ius_cbo();
        $categorias = $this->select_categorias_insumos();
        return view('proyectos/insumo/insumo', compact('tipos','unidades','ius','categorias'));
    }
    
    //Insumos
    public function listar_insumos()
    {
        $data = DB::table('proyectos.proy_insumo')
        ->select('proy_insumo.*','alm_und_medida.abreviatura',
        'proy_tp_insumo.codigo as cod_tp_insumo','proy_insumo_cat.descripcion as cat_descripcion',
        'proy_iu.descripcion as iu_descripcion',
        DB::raw("(SELECT proy_cu_detalle.precio_unit FROM proyectos.proy_cu_detalle 
        WHERE proy_cu_detalle.id_insumo=proy_insumo.id_insumo AND proy_cu_detalle.estado!=7
        ORDER BY fecha_registro desc limit 1) as precio_insumo"))
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
        ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
        ->leftjoin('proyectos.proy_insumo_cat','proy_insumo_cat.id_categoria','=','proy_insumo.id_categoria')
        ->join('proyectos.proy_iu','proy_iu.id_iu','=','proy_insumo.iu')
        ->where([['proy_insumo.estado', '=', 1]])
            ->orderBy('codigo')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_insumo($id)
    {
        $data = DB::table('proyectos.proy_insumo')
        ->select('proy_insumo.*', 'alm_und_medida.abreviatura',
        'proy_tp_insumo.codigo as cod_tp_insumo')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
        ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
            ->where([['proy_insumo.id_insumo', '=', $id]])
            ->get();
        return response()->json($data);
    }
    
    public function next_cod_insumo(){
        $data = DB::table('proyectos.proy_insumo')
        ->orderBy('codigo','desc')
        ->where('estado',1)
        ->first();
        $codigo = ((int)$data->codigo)+1;
        return ((string)$codigo);
    }

    public function guardar_insumo(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $codigo = $this->next_cod_insumo();
        $id_insumo = 0;

        $count = DB::table('proyectos.proy_insumo')
        ->where([['descripcion','=',strtoupper($request->descripcion)],
                 ['estado','=',1]])
        ->count();

        if ($count == 0){
            $id_insumo = DB::table('proyectos.proy_insumo')->insertGetId(
            [
                'codigo' => $codigo,
                'descripcion' => strtoupper($request->descripcion),
                'tp_insumo' => $request->tp_insumo,
                'id_categoria' => $request->id_categoria,
                'unid_medida' => $request->unid_medida,
                'precio' => $request->precio,
                'flete' => $request->flete,
                'peso_unitario' => $request->peso_unitario,
                'iu' => $request->iu,
                'fecha_registro' => $fecha,
                'estado' => 1,
            ],
                'id_insumo'
            );
        }
        return response()->json($id_insumo);
    }

    public function update_insumo(Request $request)
    {
        $id_insumo = DB::table('proyectos.proy_insumo')
        ->where('id_insumo',$request->id_insumo)
        ->update([
            'descripcion' => strtoupper($request->descripcion),
            'tp_insumo' => $request->tp_insumo,
            'id_categoria' => $request->id_categoria,
            'unid_medida' => $request->unid_medida,
            'precio' => $request->precio,
            'flete' => $request->flete,
            'peso_unitario' => $request->peso_unitario,
            'iu' => $request->iu,
        ]);
        return response()->json($id_insumo);
    }

    public function anular_insumo(Request $request, $id)
    {
        DB::table('proyectos.proy_insumo')
            ->where('id_insumo', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($id);
    }

    public function listar_insumo_precios($id){
        $lista = DB::table('proyectos.proy_cu_detalle')
            ->select('proy_cu_detalle.precio_unit','proy_presup.codigo','proy_presup.fecha_emision','proy_op_com.descripcion')
            ->join('proyectos.proy_cd_partida','proy_cd_partida.id_cu_partida','=','proy_cu_detalle.id_cu_partida')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->where([['proy_cu_detalle.id_insumo', '=', $id],
                     ['proy_cu_detalle.estado','!=',7],
                     ['proy_cd_partida.estado','!=',7],
                     ['proy_presup.estado','!=',7]])
                     ->groupBy('proy_cu_detalle.precio_unit','proy_presup.codigo','proy_presup.fecha_emision','proy_op_com.descripcion')
                     ->get();
        $output['data'] = $lista;
        return response()->json($output);
    }

    public function add_unid_med(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_unidad_medida = DB::table('almacen.alm_und_medida')->insertGetId(
            [
                'descripcion' => $request->descripcion_unidad,
                'abreviatura' => $request->abreviatura_unidad,
                'estado' => 1
            ],
                'id_unidad_medida'
            );
        $unid = DB::table('almacen.alm_und_medida')
            ->where('estado',1)->orderBy('descripcion','asc')->get();

        $html = '';
        foreach($unid as $unid){
            if ($id_unidad_medida == $unid->id_unidad_medida){
                $html .= '<option value="'.$unid->id_unidad_medida.'" selected>'.$unid->descripcion.'</option>';
            } else {
                $html .= '<option value="'.$unid->id_unidad_medida.'">'.$unid->descripcion.'</option>';
            }
        }
        return json_encode($html);
    }
}
