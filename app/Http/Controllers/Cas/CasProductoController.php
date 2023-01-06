<?php

namespace App\Http\Controllers\Cas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cas\CasProducto;

class CasProductoController extends Controller
{
    //
    public function inicio()
    {
        return view('cas.cas_producto.inicio');
    }
    public function listar(Request $request)
    {
        $query = CasProducto::where('estado',1);
        return datatables($query)->toJson();
    }
    public function guardar(Request $request)
    {
        $cas_marca = new CasProducto();
        $cas_marca->descripcion= $request->descripcion;
        $cas_marca->estado=1;
        $cas_marca->save();
        return response()->json([
            "success"=>true,
            "status"=>200
        ]);
    }
    public function editar(Request $request)
    {
        $cas_marca = CasProducto::find($request->id);
        return response()->json([
            "success"=>true,
            "status"=>200,
            "data"=>$cas_marca
        ]);
    }
    public function actualizar(Request $request)
    {
        $cas_marca = CasProducto::find($request->id);
        $cas_marca->descripcion = $request->descripcion;
        $cas_marca->save();
        return response()->json([
            "success"=>true,
            "status"=>200,
            "data"=>$cas_marca
        ]);
    }
    public function eliminar(Request $request)
    {
        $cas_marca = CasProducto::find($request->id);
        $cas_marca->estado=7;
        $cas_marca->save();
        return response()->json([
            "success"=>true,
            "status"=>200
        ]);
    }
}
