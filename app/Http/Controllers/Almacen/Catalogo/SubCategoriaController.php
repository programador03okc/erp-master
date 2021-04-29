<?php

namespace App\Http\Controllers\Almacen\Catalogo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Almacen\Catalogo\SubCategoria;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SubCategoriaController extends Controller
{
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
