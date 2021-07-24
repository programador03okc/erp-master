<?php

namespace App\Http\Controllers\Tesoreria\Facturacion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PendientesFacturacionController extends Controller
{
    function view_pendientes_facturacion()
    {
        return view('tesoreria/facturacion/pendientesFacturacion');
    }
}
