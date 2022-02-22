<?php

namespace App\Http\Controllers\Cas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IncidenciaController extends Controller
{
    function view_incidencia()
    {
        return view('cas/garantias/incidencia');
    }
}
