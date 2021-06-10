<?php

namespace App\Http\Controllers\Proyectos\Variables;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CategoriaInsumoController extends Controller
{
    function view_cat_insumo(){
        return view('proyectos/variables/cat_insumo');
    }
    
    public static function select_categorias_insumos(){
        $data = DB::table('proyectos.proy_insumo_cat')
            ->select('proy_insumo_cat.id_categoria','proy_insumo_cat.descripcion')
            ->where('estado',1)
            ->get();
        return $data;
    }
    //Categoria de Insumos
    public function listar_cat_insumos()
    {
        $data = DB::table('proyectos.proy_insumo_cat')
            ->select('proy_insumo_cat.*')
            ->where('estado',1)
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_cat_insumo($id)
    {
        $data = DB::table('proyectos.proy_insumo_cat')
            ->select('proy_insumo_cat.*')
            ->where([['proy_insumo_cat.id_categoria', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_cat_insumo(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_categoria = DB::table('proyectos.proy_insumo_cat')->insertGetId(
            [
                'descripcion' => strtoupper($request->descripcion),
                'fecha_registro' => $fecha,
                'estado' => 1
            ],
                'id_categoria'
            );
        return response()->json($id_categoria);
    }
    public function update_cat_insumo(Request $request)
    {
        $data = DB::table('proyectos.proy_insumo_cat')
            ->where('id_categoria', $request->id_categoria)
            ->update([
                'descripcion' => strtoupper($request->descripcion)
            ]);

        return response()->json($data);
    }
    public function anular_cat_insumo(Request $request, $id)
    {
        $insumos = DB::table('proyectos.proy_insumo')
        ->where([['id_categoria','=',$id],['estado','=',1]])
        ->count();
        if ($insumos > 0){
            $data = 0;
        } else {
            $data = DB::table('proyectos.proy_insumo_cat')
                ->where('id_categoria', $id)
                ->update([ 'estado' => 7 ]);
        }
        return response()->json($data);
    }
}
