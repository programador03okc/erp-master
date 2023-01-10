<?php

namespace App\Http\Controllers\Finanzas\Presupuesto;

use App\Helpers\StringHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Administracion\Area;
use App\Models\Configuracion\Grupo;
use App\Models\Configuracion\Moneda;
use App\Models\Finanzas\FinanzasArea;
use App\Models\Finanzas\PresupuestoInterno;
use Yajra\DataTables\Facades\DataTables;

class PresupuestoInternoController extends Controller
{
    //
    public function lista()
    {
        return view('finanzas.presupuesto_interno.lista');
    }
    public function listaPresupuestoInterno()
    {
        $data = PresupuestoInterno::where('estado',1);
        return DataTables::of($data)
        // ->toJson();
        ->make(true);
    }
    public function crear()
    {
        $grupos = Grupo::get();
        $area = FinanzasArea::where('estado',1)->get();
        $moneda = Moneda::where('estado',1)->get();

        $presupuesto_interno = PresupuestoInterno::count();

        // $correlativo = StringHelper::leftZero(2,$presupuesto_interno);

        // $inicial='PI';
        // $yy = date('Y');
        // $mm = date('m');
        // $dd = date('d');

        // return $inicial.'-'.$correlativo;exit;
        return view('finanzas.presupuesto_interno.crear', compact('grupos','area','moneda'));
    }
    public function presupuestoInternoDetalle()
    {
        $presupuesto_ingresos= PresupuestoInterno
        $presupuesto_costos;
        $presupuesto_gastos;
    }
}
