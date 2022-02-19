<?php

namespace App\Http\Controllers\Almacen\Catalogo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\almacen\Catalogo\Categoria;
use Illuminate\Support\Facades\DB;

class TipoProductoController extends Controller
{
    function view_tipo()
    {
        $clasificaciones = ClasificacionController::mostrar_clasificaciones_cbo();
        return view('almacen/producto/tipo', compact('clasificaciones'));
    }

    public function mostrarCategoriasPorClasificacion($id_clasificacion)
    {
        $data = Categoria::where([['estado', '=', 1], ['id_clasificacion', '=', $id_clasificacion]])
            ->orderBy('descripcion')
            ->get();
        return response()->json($data);

    }

    //Tipo de Producto
    public function listarCategorias()
    {
        $data = Categoria::select('alm_tp_prod.*', 'alm_clasif.descripcion as clasificacion_descripcion')
            ->join('almacen.alm_clasif', 'alm_clasif.id_clasificacion', '=', 'alm_tp_prod.id_clasificacion')
            ->where([['alm_tp_prod.estado', '=', 1]])
            ->orderBy('id_tipo_producto')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
        
    }

    public function mostrarCategoria($id)
    {
        $data = Categoria::where([['alm_tp_prod.id_tipo_producto', '=', $id]])
            ->get();
        return response()->json($data);

    }

    public function guardarCategoria(Request $request)
    {
        try{
            DB::beginTransaction();
            $fecha = date('Y-m-d H:i:s');
            $msj = '';
            $des = strtoupper($request->descripcion);

            $count = Categoria::where([['descripcion', '=', $des], ['estado', '=', 1]])
            ->count();

            if ($count == 0) {
                Categoria::insertGetId(
                    [
                        'id_clasificacion' => $request->id_clasificacion,
                        'descripcion' => $des,
                        'estado' => 1,
                        'fecha_registro' => $fecha
                    ],
                    'id_tipo_producto'
                );
                $msj = 'Se guardó la categoría correctamente';
                $status=200;
                $tipo='success';
            } else {
                $msj = 'No es posible guardar. Ya existe una categoría con dicha descripción.';
                $status=204;
                $tipo='warning';
            }
            DB::commit();
            return response()->json(['tipo' => $tipo, 'status' => $status, 'mensaje' => $msj]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    public function actualizarCategoria(Request $request)
    {
        try{
            DB::beginTransaction();
            $msj = '';
            $des = strtoupper($request->descripcion);

            $count = Categoria::where([['descripcion', '=', $des], ['estado', '=', 1]])
                ->count();

            if ($count <= 1) {
                Categoria::where('id_tipo_producto', $request->id_tipo_producto)
                ->update(['descripcion' => $des]);
                    $msj= 'Se actualizó la categoría correctamente';
                    $status=200;
                    $tipo='success';
  
            } else {
                $msj = 'No es posible actualizar. Ya existe una categoría con dicha descripción';
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

    public function anularCategoria(Request $request, $id)
    {
        try{
            DB::beginTransaction();
            $count = DB::table('almacen.alm_cat_prod')
            ->where([
                ['id_tipo_producto', '=', $id],
                ['estado', '=', 1]
            ])
            ->get()->count();
            if($count>=1){
                $mensaje ='La categoría ya fue relacionada';
                $status=204;
                $tipo='warning';
            }
            else{
                $data = Categoria::where('id_tipo_producto', $id)
                ->update(['estado' => 7]);
                $mensaje = 'La categoría se anuló correctamente';
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

    public function revisarCategoria($id)
    {
        $data = DB::table('almacen.alm_cat_prod')
            ->where([
                ['id_tipo_producto', '=', $id],
                ['estado', '=', 1]
            ])
            ->get()->count();
        return response()->json($data);
    }
}
