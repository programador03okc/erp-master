<?php

namespace App\Http\Controllers\Finanzas\Presupuesto;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Administracion\Area;
use App\Models\Configuracion\Grupo;
use App\Models\Configuracion\Moneda;
use App\Models\Finanzas\FinanzasArea;

class PresupuestoInternoController extends Controller
{
    //
    public function inicio()
    {
        $grupos = Grupo::get();
        $area = FinanzasArea::where('estado',1)->get();
        $moneda = Moneda::where('estado',1)->get();
        return view('finanzas.presupuesto_interno.inicio', compact('grupos','area','moneda'));
    }
}
