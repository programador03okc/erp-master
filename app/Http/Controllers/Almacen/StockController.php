<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StockController extends Controller
{
    //
    function view_importar(){
        return view('almacen/stocks/importar');
    }

    function view_toma_inventario(){
        return view('almacen/stocks/toma_inventario');
    }
}
