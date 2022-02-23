<?php

namespace App\Http\Controllers\Almacen\Catalogo;

use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\almacen\Catalogo\Categoria;
use App\Models\Almacen\Catalogo\SubCategoria;
use Illuminate\Support\Facades\DB;
class SubCategoriaController extends Controller
{
    function viewSubCategoria()
    {
        $clasificaciones = ClasificacionController::mostrar_clasificaciones_cbo();
        $tipos = Categoria::where('estado', 1 )->get();
        return view('almacen/producto/categoria', compact('tipos', 'clasificaciones'));
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
        $data = SubCategoria::
        select(
                'alm_cat_prod.*',
                'alm_tp_prod.descripcion as tipo_descripcion',
                'alm_clasif.descripcion as clasificacion_descripcion'
            )
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
        $data = SubCategoria::
        select(
                'alm_cat_prod.*',
                'alm_tp_prod.descripcion as tipo_descripcion',
                'alm_tp_prod.id_tipo_producto',
                'alm_clasif.id_clasificacion',
            )
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
        try{
            DB::beginTransaction();
            $fecha = date('Y-m-d H:i:s');
            $des = strtoupper($request->descripcion);
            $msj = '';
            
            $count = SubCategoria::where([['descripcion', '=', $des], ['estado', '=', 1]])
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

            DB::commit();
            return response()->json(['tipo' => $tipo, 'status' => $status, 'mensaje' => $msj]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    public function actualizarSubCategoria(Request $request)
    {
        try{
            DB::beginTransaction();
            $msj = '';
            $des = strtoupper($request->descripcion);

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
        }

    }

    public function anularSubCategoria(Request $request, $id)
    {
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
