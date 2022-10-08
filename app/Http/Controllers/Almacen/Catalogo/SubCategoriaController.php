<?php

namespace App\Http\Controllers\Almacen\Catalogo;

use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\almacen\Catalogo\Categoria;
use App\Models\almacen\Catalogo\Clasificacion;
use App\Models\Almacen\Catalogo\SubCategoria;
use App\models\Configuracion\AccesosUsuarios;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubCategoriaController extends Controller
{
    function view_sub_categoria()
    {
        $clasificaciones = Clasificacion::where('estado', 1)->get();
        $tipos = Categoria::where('estado', 1)->get();

        $clasificaciones = ClasificacionController::mostrar_clasificaciones_cbo();
        $array_accesos_botonera=array();
        $accesos_botonera = AccesosUsuarios::where('accesos_usuarios.estado','=',1)
        ->select('accesos.*')
        ->join('configuracion.accesos','accesos.id_acceso','=','accesos_usuarios.id_acceso')
        ->where('accesos_usuarios.id_usuario',Auth::user()->id_usuario)
        ->where('accesos_usuarios.id_modulo',63)
        ->where('accesos_usuarios.id_padre',4)
        ->get();
        foreach ($accesos_botonera as $key => $value) {
            $value->accesos;
            array_push($array_accesos_botonera,$value->accesos->accesos_grupo);
        }
        $modulo='almacen';

        return view('almacen/producto/subCategoria', compact('tipos', 'clasificaciones','modulo','array_accesos_botonera'));
    }

    public function mostrarSubCategoriasPorCategoria($id_tipo)
    {
        $data = SubCategoria::where([['estado', '=', 1], ['id_tipo_producto', '=', $id_tipo]])
            ->orderBy('descripcion')
            ->get();
        return response()->json($data);
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
    public function mostrar_categorias()
    {
        $data = SubCategoria::select(
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

    public function mostrar_categoria($id)
    {
        $data = SubCategoria::select(
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

    public function guardar_categoria(Request $request)
    {
        // $codigo = $this->categoria_nextId($request->id_tipo_producto);
        $fecha = date('Y-m-d H:i:s');
        $msj = '';
        $des = strtoupper($request->descripcion);

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
        } else {
            $msj = 'No puede guardar. Ya existe dicha descripción.';
        }
        return response()->json($msj);
    }

    public function update_categoria(Request $request)
    {
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = SubCategoria::where([['descripcion', '=', $des], ['estado', '=', 1]])
            ->count();

        if ($count <= 1) {
            SubCategoria::where('id_categoria', $request->id_categoria)
                ->update(['descripcion' => $des]);
        } else {
            $msj = 'No puede actualizar. Ya existe dicha descripción.';
        }
        return response()->json($msj);
    }

    public function anular_categoria(Request $request, $id)
    {
        $id_categoria = SubCategoria::where('id_categoria', $id)
            ->update(['estado' => 7]);
        return response()->json($id_categoria);
    }

    public function cat_revisar($id)
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
