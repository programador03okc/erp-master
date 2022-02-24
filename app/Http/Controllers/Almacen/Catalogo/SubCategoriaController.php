<?php

namespace App\Http\Controllers\Almacen\Catalogo;

use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\almacen\Catalogo\Categoria;
<<<<<<< HEAD
=======
use App\Models\almacen\Catalogo\Clasificacion;
>>>>>>> 0afa8485695289e3277a2ac8f01bf5e23e49492e
use App\Models\Almacen\Catalogo\SubCategoria;
use Illuminate\Support\Facades\DB;

class SubCategoriaController extends Controller
{
<<<<<<< HEAD
    function viewSubCategoria()
    {
        $clasificaciones = ClasificacionController::mostrar_clasificaciones_cbo();
        $tipos = Categoria::where('estado', 1 )->get();
        return view('almacen/producto/categoria', compact('tipos', 'clasificaciones'));
=======
    function view_sub_categoria()
    {
        $clasificaciones = Clasificacion::where('estado', 1)->get();
        $tipos = Categoria::where('estado', 1)->get();
        return view('almacen/producto/subCategoria', compact('tipos', 'clasificaciones'));
    }

    public function mostrarSubCategoriasPorCategoria($id_tipo)
    {
        $data = SubCategoria::where([['estado', '=', 1], ['id_tipo_producto', '=', $id_tipo]])
            ->orderBy('descripcion')
            ->get();
        return response()->json($data);
>>>>>>> 0afa8485695289e3277a2ac8f01bf5e23e49492e
    }

    public static function mostrar_categorias_cbo()
    {
        $data = SubCategoria::select('alm_cat_prod.id_categoria', 'alm_cat_prod.descripcion')
            ->where([['alm_cat_prod.estado', '=', 1]])
            ->orderBy('descripcion')
            ->get();
        return $data;
    }

    //Categorias
    public function listarSubCategorias()
    {
<<<<<<< HEAD
        $data = SubCategoria::
        select(
                'alm_cat_prod.*',
                'alm_tp_prod.descripcion as tipo_descripcion',
                'alm_clasif.descripcion as clasificacion_descripcion'
            )
=======
        $data = SubCategoria::select(
            'alm_cat_prod.*',
            'alm_tp_prod.descripcion as tipo_descripcion',
            'alm_clasif.descripcion as clasificacion_descripcion'
        )
>>>>>>> 0afa8485695289e3277a2ac8f01bf5e23e49492e
            ->join('almacen.alm_tp_prod', 'alm_tp_prod.id_tipo_producto', '=', 'alm_cat_prod.id_tipo_producto')
            ->join('almacen.alm_clasif', 'alm_clasif.id_clasificacion', '=', 'alm_tp_prod.id_clasificacion')
            ->where([['alm_cat_prod.estado', '=', 1]])
            ->orderBy('id_categoria')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_categorias_tipo($id_tipo)
    {
        $data = SubCategoria::where([['estado', '=', 1], ['id_tipo_producto', '=', $id_tipo]])
            ->orderBy('descripcion')
            ->get();
        return response()->json($data);
    }

    public function mostrarSubCategoria($id)
    {
<<<<<<< HEAD
        $data = SubCategoria::
        select(
                'alm_cat_prod.*',
                'alm_tp_prod.descripcion as tipo_descripcion',
                'alm_tp_prod.id_tipo_producto',
                'alm_clasif.id_clasificacion',
            )
=======
        $data = SubCategoria::select(
            'alm_cat_prod.*',
            'alm_tp_prod.descripcion as tipo_descripcion',
            'alm_tp_prod.id_tipo_producto',
            'alm_clasif.id_clasificacion',
        )
>>>>>>> 0afa8485695289e3277a2ac8f01bf5e23e49492e
            ->join('almacen.alm_tp_prod', 'alm_tp_prod.id_tipo_producto', '=', 'alm_cat_prod.id_tipo_producto')
            ->join('almacen.alm_clasif', 'alm_clasif.id_clasificacion', '=', 'alm_tp_prod.id_clasificacion')
            ->where([['alm_cat_prod.id_categoria', '=', $id]])
            ->get();
        return response()->json($data);
    }

    public function categoria_nextId($id_tipo_producto)
    {
        $cantidad = SubCategoria::where('id_tipo_producto', $id_tipo_producto)
            ->get()->count();
        $val = AlmacenController::leftZero(3, $cantidad);
        $nextId = "" . $id_tipo_producto . "" . $val;
        return $nextId;
    }

    public function guardarSubCategoria(Request $request)
    {
<<<<<<< HEAD
        try{
            DB::beginTransaction();
            $fecha = date('Y-m-d H:i:s');
            $des = strtoupper($request->descripcion);
            $msj = '';
            
            $count = SubCategoria::where([['descripcion', '=', $des], ['estado', '=', 1]])
=======
        // $codigo = $this->categoria_nextId($request->id_tipo_producto);
        $fecha = date('Y-m-d H:i:s');
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = SubCategoria::where([['descripcion', '=', $des], ['estado', '=', 1]])
>>>>>>> 0afa8485695289e3277a2ac8f01bf5e23e49492e
            ->count();
            if ($count == 0) {
                SubCategoria::insertGetId(
                    [
                        // 'codigo' => $codigo,
                        'id_tipo_producto' => $request->id_tipo_producto,
                        'descripcion' => $des,
                        'estado' => 1,
                        'fecha_registro' => $fecha
                    ],
                    'id_categoria'
                );
    
                $msj = 'Se guardó la subcategoría correctamente';
                $status= 200;
                $tipo='success';
            } else {
                $msj = 'No es posible guardar. Ya existe una subcategoría con dicha descripción';
                $status = 204;
                $tipo='warning';
            }

<<<<<<< HEAD
            DB::commit();
            return response()->json(['tipo' => $tipo, 'status' => $status, 'mensaje' => $msj]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
=======
        if ($count == 0) {
            SubCategoria::insertGetId(
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
>>>>>>> 0afa8485695289e3277a2ac8f01bf5e23e49492e
        }
    }

    public function actualizarSubCategoria(Request $request)
    {
        try{
            DB::beginTransaction();
            $msj = '';
            $des = strtoupper($request->descripcion);

<<<<<<< HEAD
            $count = SubCategoria::where([['descripcion', '=', $des], ['estado', '=', 1]])
                ->count();

            if ($count <= 1) {
                SubCategoria::where('id_categoria', $request->id_categoria)
                    ->update(['descripcion' => $des]);
                    $msj= 'Se actualizó la subcategoría correctamente';
                    $status=200;
                    $tipo='success';
            } else {
                $msj = 'No es posible actualizar. Ya existe una subcategoría con dicha descripción';
                $status=204;
                $tipo='warning';
            }

            DB::commit();
            return response()->json(['tipo' => $tipo, 'status' => $status, 'mensaje' => $msj]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al actualizar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
=======
        $count = SubCategoria::where([['descripcion', '=', $des], ['estado', '=', 1]])
            ->count();

        if ($count <= 1) {
            SubCategoria::where('id_categoria', $request->id_categoria)
                ->update(['descripcion' => $des]);
        } else {
            $msj = 'No puede actualizar. Ya existe dicha descripción.';
>>>>>>> 0afa8485695289e3277a2ac8f01bf5e23e49492e
        }

    }

    public function anularSubCategoria(Request $request, $id)
    {
<<<<<<< HEAD
        try{
            DB::beginTransaction();
        $count =  DB::table('almacen.alm_prod')
        ->where([
            ['id_categoria', '=', $id],
            ['estado', '=', 1]
        ])
            ->get()->count();
            if($count>=1){
                $mensaje ='La subcategoría ya fue relacionada';
                $status=204;
                $tipo='warning';
            }
            else{
                $data = SubCategoria::where('id_categoria', $id)
                ->update(['estado' => 7]);
                $mensaje = 'La subcategoría se anuló correctamente';
                $status=200;
                $tipo='success';
            }
            DB::commit();
            return response()->json(['tipo' => $tipo, 'status' => $status, 'mensaje' => $mensaje]);
        }  catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al anular. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
=======
        $id_categoria = SubCategoria::where('id_categoria', $id)
            ->update(['estado' => 7]);
        return response()->json($id_categoria);
>>>>>>> 0afa8485695289e3277a2ac8f01bf5e23e49492e
    }

    public function revisarCat($id)
    {
        $data = DB::table('almacen.alm_prod')
            ->where([
                ['id_categoria', '=', $id],
                ['estado', '=', 1]
            ])
            ->get()->count();
        return response()->json($data);
    }
}
