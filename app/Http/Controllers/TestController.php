<?php

namespace App\Http\Controllers;

use App\Models\Logistica\OrdenCompraDetalle;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function testDescripcionAdicionalOrden()
    {
        $lista = OrdenCompraDetalle::where('id_orden_compra', 2092)->get();
        $data = [];

        foreach ($lista as $key) {
            $nombre = trim(str_replace('•', '', utf8_decode($key->descripcion_adicional)));

            $update = OrdenCompraDetalle::find($key->id_detalle_orden);
                $update->descripcion_adicional = $nombre;
            $update->save();
            
            $data[] = ['antes' => $key->descripcion_adicional, 'despues' => $nombre];
        }
        return response()->json($data, 200);
    }
}