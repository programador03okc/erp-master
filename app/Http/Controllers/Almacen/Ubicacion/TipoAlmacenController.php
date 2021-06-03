<?php

namespace App\Http\Controllers\Almacen\Ubicacion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TipoAlmacenController extends Controller
{
    function view_tipo_almacen(){
        return view('almacen/variables/tipo_almacen');
    }
    
    /* Tipo Almacen */
    public function mostrar_tipo_almacen(){
        $data = DB::table('almacen.alm_tp_almacen')->orderBy('id_tipo_almacen')->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_tipo_almacenes($id){
        $data = DB::table('almacen.alm_tp_almacen')->orderBy('id_tipo_almacen')
            ->where([['alm_tp_almacen.id_tipo_almacen', '=', $id]])->get();
        return response()->json($data);
    }

    public function guardar_tipo_almacen(Request $request){
        $id_almacen = DB::table('almacen.alm_tp_almacen')->insertGetId(
            [
                'descripcion' => $request->descripcion,
                'estado' => 1
            ],
                'id_tipo_almacen'
            );
        return response()->json($id_almacen);
    }

    public function update_tipo_almacen(Request $request){
        $data = DB::table('almacen.alm_tp_almacen')->where('id_tipo_almacen', $request->id_tipo_almacen)
            ->update([
                'descripcion' => $request->descripcion,
                'estado' => 1
            ]);
        return response()->json($data);
    }
    
    public function anular_tipo_almacen($id){
        $data = DB::table('almacen.alm_tp_almacen')->where('id_tipo_almacen', $id)
            ->update([
                'estado' => 7
            ]);
        return response()->json($data);
    }

}
