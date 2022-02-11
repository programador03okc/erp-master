<?php

namespace App\Http\Controllers\Almacen\Catalogo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\almacen\Catalogo\Clasificacion;
use App\Models\Almacen\Producto;
use Illuminate\Support\Facades\DB;

class ClasificacionController extends Controller
{
    function view_clasificacion()
    {
        return view('almacen/producto/clasificacion');
    }
    public static function mostrar_clasificaciones_cbo()
    {
        $data = Clasificacion::select('alm_clasif.id_clasificacion', 'alm_clasif.descripcion')
            ->where('alm_clasif.estado', '=', 1)
            ->orderBy('descripcion')
            ->get();
        return $data;
    }
    //Clasificaciones
    public function listarClasificaciones()
    {
        $data = Clasificacion::select('alm_clasif.*')
            ->where('alm_clasif.estado', 1)
            ->orderBy('id_clasificacion')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrarClasificacion($id)
    {
        $data = Clasificacion::where('alm_clasif.id_clasificacion', $id)
            ->get();
        return response()->json($data);
    }

    public function guardarClasificacion(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = Clasificacion::where([['descripcion', '=', $des], ['estado', '=', 1]])
            ->count();

        if ($count == 0) {
            Clasificacion::insertGetId(
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

    public function actualizarClasificacion(Request $request)
    {
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = Clasificacion::where([['descripcion', '=', $des], ['estado', '=', 1]])
            ->count();

        if ($count <= 1) {
            $data = Clasificacion::where('id_clasificacion', $request->id_clasificacion)
                ->update(['descripcion' => $des]);
        } else {
            $msj = 'No es posible guardar. Ya existe una clasificación con dicha descripción.';
        }
        return response()->json($msj);
    }

    public function anularClasificacion(Request $request, $id)
    {
        $data = Clasificacion::where('id_clasificacion', $id)
            ->update(['estado' => 7]);
        return response()->json($data);
    }

    public function revisarClasificacion($id)
    {
        $data = Producto::where([
            ['id_clasif', '=', $id],
            ['estado', '=', 1]
        ])
            ->get()->count();
        return response()->json($data);
    }
}
