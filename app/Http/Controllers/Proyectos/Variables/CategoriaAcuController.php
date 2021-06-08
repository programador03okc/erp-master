<?php

namespace App\Http\Controllers\Proyectos\Variables;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CategoriaAcuController extends Controller
{
    function view_cat_acu(){
        return view('proyectos/variables/cat_acu');
    }
    //Categoria de Acus
    public function listar_cat_acus()
    {
        $data = DB::table('proyectos.proy_cu_cat')
            ->select('proy_cu_cat.*')
            ->where('estado',1)
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_cat_acu($id)
    {
        $data = DB::table('proyectos.proy_cu_cat')
            ->select('proy_cu_cat.*')
            ->where([['proy_cu_cat.id_categoria', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_cat_acu(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_categoria = DB::table('proyectos.proy_cu_cat')->insertGetId(
            [
                'descripcion' => strtoupper($request->descripcion),
                'fecha_registro' => $fecha,
                'estado' => 1
            ],
                'id_categoria'
            );
        return response()->json($id_categoria);
    }
    public function update_cat_acu(Request $request)
    {
        $data = DB::table('proyectos.proy_cu_cat')
            ->where('id_categoria', $request->id_categoria)
            ->update([
                'descripcion' => strtoupper($request->descripcion)
            ]);

        return response()->json($data);
    }
    public function anular_cat_acu(Request $request, $id)
    {
        $cus = DB::table('proyectos.proy_cu')
        ->where([['id_categoria','=',$id],['estado','=',1]])
        ->count();
        if ($cus > 0){
            $data = 0;
        } else {
            $data = DB::table('proyectos.proy_cu_cat')
                ->where('id_categoria', $id)
                ->update([ 'estado' => 7 ]);
        }
        return response()->json($data);
    }
}
