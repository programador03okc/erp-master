<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presupuestos\Titulo;

class TituloController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store()
    {
        $data = Titulo::create([
                'id_presup' => request('id_presup'),
                'codigo' => request('codigo'),
                'descripcion' => strtoupper(request('descripcion')),
                'cod_padre' => request('cod_padre'),
                'total' => 0,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ]);

        return response()->json($data);
    }

    public function update()
    {
        $title = Titulo::findOrFail(request('id_titulo'));
        $title->update([
            'descripcion' => strtoupper(request('descripcion'))
        ]);

        return response()->json($title);
    }

    public function destroy($id)
    {
        $title = Titulo::findOrFail($id);
        $title->update(['estado' => 7]);

        return response()->json($title);
    }

}
