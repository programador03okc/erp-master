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
        } else {
            $msj = 'No es posible guardar. Ya existe ' . $count . ' tipo registrado con la misma descripción.';
        }
        return response()->json($msj);
    }

    public function actualizarCategoria(Request $request)
    {
        $des = strtoupper($request->descripcion);
        $count = Categoria::where([['descripcion', '=', $des], ['estado', '=', 1]])
            ->count();
        $msj = '';
        if ($count <= 1) {
            $data = Categoria::where('id_tipo_producto', $request->id_tipo_producto)
                ->update([
                    'id_clasificacion' => $request->id_clasificacion,
                    'descripcion' => $des
                ]);
        } else {
            $msj = 'No es posible actualizar. Ya existe ' . $count . ' tipo registrado con la misma descripción.';
        }
        return response()->json($msj);
    }

    public function anularCategoria(Request $request, $id)
    {
        $msj = '';
        $count = DB::table('almacen.alm_cat_prod')
            ->where('id_tipo_producto', $id)
            ->count();
        if ($count == 0) {
            Categoria::where('id_tipo_producto', $id)
                ->update(['estado' => 7]);
        } else {
            $msj = 'No puede anular. Tiene vinculado ' . $count . ' categoría.';
        }
        return response()->json($msj);
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
