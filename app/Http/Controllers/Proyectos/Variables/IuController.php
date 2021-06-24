<?php

namespace App\Http\Controllers\Proyectos\Variables;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class IuController extends Controller
{
    function view_iu(){
        return view('proyectos/variables/iu');
    }
    public static function mostrar_ius_cbo(){
        $data = DB::table('proyectos.proy_iu')
            ->select('proy_iu.id_iu','proy_iu.descripcion')
            ->where('estado', 1)
            ->orderBy('descripcion')
            ->get();
        return $data;
    }
    //iu
    public function mostrar_ius(){
        $data = DB::table('proyectos.proy_iu')
            ->select('proy_iu.*')
            ->where('estado',1)
            ->orderBy('codigo')
            ->get();        
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_iu($iu)
    {
        $data = DB::table('proyectos.proy_iu')
            ->select('proy_iu.*')
            ->where([['id_iu', '=', $iu]])
            ->get();        
        // $data = proy_iu::where('id_iu', $iu)->first();
        return response()->json($data);
    }
    public function guardar_iu(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_iu = DB::table('proyectos.proy_iu')->insertGetId(
            [
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'fecha_registro' => $fecha,
                'estado' => 1
            ],
                'id_iu'
            );

        return response()->json($id_iu);
    }
    public function update_iu(Request $request)
    {
        $iu = DB::table('proyectos.proy_iu')
            ->where('id_iu',$request->id_iu)
            ->update([
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'estado' => $request->estado
            ]);
        return response()->json($iu);
    }
    public function anular_iu(Request $request,$id_iu)
    {
        $iu = DB::table('proyectos.proy_iu')
            ->where('id_iu',$id_iu)
            ->update([ 'estado' => 7 ]);
        return response()->json($iu);
    }
    public function buscar_iu(Request $request,$id_iu)
    {
        $insumos = DB::table('proyectos.proy_insumo')
        ->select('proy_insumo.id_insumo')
            ->where([['proy_insumo.iu', '=', $id_iu]])
            ->get()->count();
        return response()->json($insumos);
    }

}
