<?php

namespace App\Http\Controllers\Almacen\Catalogo;

use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{
    function view_categoria(){
        $tipos = AlmacenController::mostrar_tipos_cbo();
        return view('almacen/producto/categoria', compact('tipos'));
    }
    
    //Categorias
    public function mostrar_categorias(){
        $data = DB::table('almacen.alm_cat_prod')
            ->select('alm_cat_prod.*', 'alm_tp_prod.descripcion as tipo_descripcion')
            ->join('almacen.alm_tp_prod','alm_tp_prod.id_tipo_producto','=','alm_cat_prod.id_tipo_producto')
            ->where([['alm_cat_prod.estado', '=', 1]])
                ->orderBy('id_categoria')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_categorias_tipo($id_tipo){
        $data = DB::table('almacen.alm_cat_prod')
            ->where([['estado', '=', 1],['id_tipo_producto', '=', $id_tipo]])
                ->orderBy('descripcion')
                ->get();
        return response()->json($data);
    }

    public function mostrar_categoria($id){
        $data = DB::table('almacen.alm_cat_prod')
        ->select('alm_cat_prod.*', 'alm_tp_prod.descripcion as tipo_descripcion',
                 'alm_tp_prod.id_tipo_producto')
        ->join('almacen.alm_tp_prod','alm_tp_prod.id_tipo_producto','=','alm_cat_prod.id_tipo_producto')
            ->where([['alm_cat_prod.id_categoria', '=', $id]])
            ->get();
        return response()->json($data);
    }

    public function categoria_nextId($id_tipo_producto){
        $cantidad = DB::table('almacen.alm_cat_prod')
        ->where('id_tipo_producto',$id_tipo_producto)
        ->get()->count();
        $val = AlmacenController::leftZero(3,$cantidad);
        $nextId = "".$id_tipo_producto."".$val;
        return $nextId;
    }

    public function guardar_categoria(Request $request){
        // $codigo = $this->categoria_nextId($request->id_tipo_producto);
        $fecha = date('Y-m-d H:i:s');
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('almacen.alm_cat_prod')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count == 0){
            $id_categoria = DB::table('almacen.alm_cat_prod')->insertGetId(
                [
                    // 'codigo' => $codigo,
                    'id_tipo_producto' => $request->id_tipo_producto,
                    'descripcion' => $des,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_categoria'
                );
        } else {
            $msj = 'No puede guardar. Ya existe dicha descripción.';
        }
        return response()->json($msj);
    }

    public function update_categoria(Request $request){
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('almacen.alm_cat_prod')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count <= 1){
            $id_categoria = DB::table('almacen.alm_cat_prod')
            ->where('id_categoria',$request->id_categoria)
            ->update([ 'descripcion' => $des ]);
        } else {
            $msj = 'No puede actualizar. Ya existe dicha descripción.';
        }
        return response()->json($msj);
    }

    public function anular_categoria(Request $request,$id){
        $id_categoria = DB::table('almacen.alm_cat_prod')
        ->where('id_categoria',$id)
        ->update([ 'estado' => 7 ]);
        return response()->json($id_categoria);
    }

    public function cat_revisar($id){
        $data = DB::table('almacen.alm_prod')
        ->where([['id_categoria','=',$id],
                ['estado','=',1]])
        ->get()->count();
        return response()->json($data);
    }
}
