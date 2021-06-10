<?php

namespace App\Http\Controllers\Proyectos\Catalogos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Proyectos\Variables\CategoriaAcuController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NombresAcuController extends Controller
{
    function view_nombres_cu(){
        $categorias = CategoriaAcuController::select_categorias_acus();
        return view('proyectos/acu/cu', compact('categorias'));
    }

    //Nombre de Analisis de Costos Unitarios
    public function listar_nombres_cus()
    {
        $data = DB::table('proyectos.proy_cu')
            ->select('proy_cu.*','proy_cu_cat.descripcion as cat_descripcion',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color','sis_usua.nombre_corto')
            ->leftjoin('proyectos.proy_cu_cat','proy_cu_cat.id_categoria','=','proy_cu.id_categoria')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','proy_cu.estado')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_cu.usuario_registro')
            ->where([['proy_cu.estado', '!=', 7]])
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function next_cod_acu(){
        $data = DB::table('proyectos.proy_cu')
            ->orderBy('codigo','desc')
            ->where('estado',1)
            ->first();

        $codigo = 1;
        if (isset($data)){
            $codigo = ((int)$data->codigo)+1;
        }
        return GenericoController::leftZero(4,$codigo);
    }
    
    public function guardar_cu(Request $request)
    {
        $codigo = $this->next_cod_acu();
        $count = DB::table('proyectos.proy_cu')
        ->where([['descripcion','=',strtoupper($request->cu_descripcion)],
                ['estado','=',1]])
        ->count();
        $id_usuario = Auth::user()->id_usuario;
        $id_cu = 0;
        $cu = null;

        if ($count == 0){
            $id_cu = DB::table('proyectos.proy_cu')->insertGetId(
            [
                'codigo' => $codigo,
                'descripcion' => strtoupper($request->cu_descripcion),
                'id_categoria' => $request->id_categoria,
                'observacion' => $request->observacion,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'usuario_registro' => $id_usuario,
            ],
                'id_cu'
            );
            $cu = DB::table('proyectos.proy_cu')->where('id_cu',$id_cu)->first();
        }
        return response()->json(['id_cu'=>$id_cu,'cu'=>$cu]);
    }
    public function update_cu(Request $request)
    {
        $count = DB::table('proyectos.proy_cu')
        ->where([['descripcion','=',strtoupper($request->cu_descripcion)],
                ['estado','!=',7],
                ['id_cu','!=',$request->id_cu]])
        ->count();
        $id_cu = 0;

        if ($count <= 0){
            $id_cu = DB::table('proyectos.proy_cu')->where('id_cu',$request->id_cu)
            ->update([
                'descripcion' => strtoupper($request->cu_descripcion),
                'id_categoria' => $request->id_categoria,
                'observacion' => $request->observacion,
            ]);
        }
        return response()->json(['id_cu'=>$id_cu]);
    }
    public function anular_cu($id_cu)
    {
        $count = DB::table('proyectos.proy_cu_partida')
        ->where([['id_cu','=',$id_cu],['estado','!=',7]])
        ->count();
        $data = 0;
        if ($count == 0){
            $data = DB::table('proyectos.proy_cu')->where('id_cu',$id_cu)
            ->update(['estado'=>7]);
        }
        return response()->json($data);
    }
    public function listar_partidas_cu($id_cu)
    {
        $data = DB::table('proyectos.proy_cu_partida')
        ->select('proy_cu_partida.*','proy_cd_partida.cantidad','proy_cd_partida.importe_parcial',
        'alm_und_medida.abreviatura','proy_presup.id_presupuesto','proy_presup.codigo','proy_op_com.descripcion')
        ->leftJoin('proyectos.proy_cd_partida','proy_cd_partida.id_cu_partida','=','proy_cu_partida.id_cu_partida')
        ->leftJoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu_partida.unid_medida')
        ->leftJoin('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
        ->leftJoin('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
        ->where([['proy_cu_partida.id_cu','=',$id_cu],['proy_cu_partida.estado','=',1]])
        ->get();
        return response()->json($data);
    }

}
