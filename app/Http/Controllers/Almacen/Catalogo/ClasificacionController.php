<?php

namespace App\Http\Controllers\Almacen\Catalogo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ClasificacionController extends Controller
{
    function view_clasificacion(){
        return view('almacen/producto/clasificacion');
    }

    //Clasificaciones
    public function mostrar_clasificaciones(){
        $data = DB::table('almacen.alm_clasif')
            ->select('alm_clasif.*')
            ->where([['alm_clasif.estado', '=', 1]])
                ->orderBy('id_clasificacion')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_clasificacion($id){
        $data = DB::table('almacen.alm_clasif')
            ->where([['alm_clasif.id_clasificacion', '=', $id]])
            ->get();
        return response()->json($data);
    }

    public function guardar_clasificacion(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('almacen.alm_clasif')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count == 0){
            $id_clasificacion = DB::table('almacen.alm_clasif')->insertGetId(
                [
                    'descripcion' => $des,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_clasificacion'
                );
        } else {
            $msj = 'No es posible guardar. Ya existe una clasificación con dicha descripción.';
        }
        return response()->json($msj);
    }

    public function update_clasificacion(Request $request)
    {
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('almacen.alm_clasif')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count <= 1){
            $data = DB::table('almacen.alm_clasif')
                ->where('id_clasificacion',$request->id_clasificacion)
                ->update([ 'descripcion' => $des ]);
        } else {
            $msj = 'No es posible guardar. Ya existe una clasificación con dicha descripción.';
        }
        return response()->json($msj);
    }

    public function anular_clasificacion(Request $request,$id){
        $data = DB::table('almacen.alm_clasif')
            ->where('id_clasificacion',$id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }
    
    public function clas_revisar($id){
        $data = DB::table('almacen.alm_prod')
        ->where([['id_clasif','=',$id],
                ['estado','=',1]])
        ->get()->count();
        return response()->json($data);
    }
}
