<?php

namespace App\Http\Controllers\Almacen\Catalogo;

use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Almacen\Catalogo\SubCategoria;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubCategoriaController extends Controller
{
    function view_subcategoria()
    {
        return view('almacen/producto/subcategoria');
    }

    public static function mostrar_subcategorias_cbo()
    {
        $data = DB::table('almacen.alm_subcat')
            ->select('alm_subcat.id_subcategoria', 'alm_subcat.descripcion')
            ->where([['alm_subcat.estado', '=', 1]])
            ->orderBy('descripcion')
            ->get();
        return $data;
    }
    //SubCategorias
    public function mostrar_sub_categorias()
    {
        $data = DB::table('almacen.alm_subcat')
            ->where('estado', 1)->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_sub_categoria($id)
    {
        $data = DB::table('almacen.alm_subcat')
            ->select('alm_subcat.*', 'sis_usua.nombre_corto')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_subcat.registrado_por')
            ->where([['alm_subcat.id_subcategoria', '=', $id]])
            ->get();
        return response()->json($data);
    }

    public function subcategoria_nextId($id_categoria)
    {
        $cantidad = DB::table('almacen.alm_subcat')
            ->where('estado', 1)->get()->count();
        $nextId = AlmacenController::leftZero(3, $cantidad);
        return $nextId;
    }

    public function guardar_sub_categoria(Request $request)
    {
        // $codigo = $this->subcategoria_nextId($request->id_categoria);
        $fecha = date('Y-m-d H:i:s');
        $usuario = Auth::user()->id_usuario;
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('almacen.alm_subcat')
            ->where([['descripcion', '=', $des], ['estado', '=', 1]])
            ->count();

        if ($count == 0) {
            $data = DB::table('almacen.alm_subcat')->insertGetId(
                [
                    // 'codigo' => $codigo,
                    // 'id_categoria' => $request->id_categoria,
                    'descripcion' => $des,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                    'registrado_por' => $usuario
                ],
                'id_subcategoria'
            );
        } else {
            $msj = 'No es posible guardar. Ya existe una subcategoria con dicha descripción';
        }
        return response()->json($msj);
    }

    public function update_sub_categoria(Request $request)
    {
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('almacen.alm_subcat')
            ->where([['descripcion', '=', $des], ['estado', '=', 1]])
            ->count();

        if ($count <= 1) {
            $id_sub_cat = DB::table('almacen.alm_subcat')
                ->where('id_subcategoria', $request->id_subcategoria)
                ->update(['descripcion' => $des]);
        } else {
            $msj = 'No es posible actualizar. Ya existe una subcategoria con dicha descripción';
        }
        return response()->json($msj);
    }

    public function anular_sub_categoria(Request $request, $id)
    {
        $id_sub_cat = DB::table('almacen.alm_subcat')
            ->where('id_subcategoria', $id)
            ->update(['estado' => 7]);
        return response()->json($id_sub_cat);
    }

    public function subcat_revisar($id)
    {
        $data = DB::table('almacen.alm_prod')
            ->where([
                ['id_subcategoria', '=', $id],
                ['estado', '=', 1]
            ])
            ->get()->count();
        return response()->json($data);
    }

    public function guardar(Request $request)
    {
        $des = strtoupper($request->descripcion);
        $msj = '';
        $status = 0;

        if (SubCategoria::where([['descripcion', '=', $des], ['estado', '=', 1]])->count() == 0) {
            $subcategoria = new SubCategoria();
            $subcategoria->codigo = SubCategoria::nextId();
            $subcategoria->descripcion = $des;
            $subcategoria->estado = 1;
            $subcategoria->fecha_registro = new Carbon();
            $subcategoria->registrado_por = Auth::user()->id_usuario;
            $subcategoria->save();

            $status = 200;
            $msj = 'Guardado';

            $subcategoriaList = SubCategoria::mostrarSubcategorias();
        } else {
            $msj = 'No es posible guardar. Ya existe una subcategoria con dicha descripción';
            $status = 204;
        }
        return response()->json(['status' => $status, 'msj' => $msj, 'data' => $subcategoriaList]);
    }
}
