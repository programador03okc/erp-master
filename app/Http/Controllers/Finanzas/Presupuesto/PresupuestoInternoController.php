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
use App\Models\Finanzas\PresupuestoInternoModelo;
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
    public function presupuestoInternoDetalle(Request $request)
    {
        // return $request->tipo;exit;
        $presupuesto = [];
        $tipo='';
        $tipo_next='';
        $ordenamiento = [];
        switch ($request->tipo) {
            case '1':
                $tipo='INGRESOS';
                $presupuesto   = PresupuestoInternoModelo::where('id_tipo_presupuesto',1)->orderBy('partida')->get();
                $tipo_next=2;
                $ordenamiento = $this->ordenarPresupuesto($presupuesto);
                break;
            case '2':
                $tipo='COSTOS';
                $presupuesto     = PresupuestoInternoModelo::where('id_tipo_presupuesto',2)->orderBy('partida')->get();
                $tipo_next=3;
                $ordenamiento = $this->ordenarPresupuesto($presupuesto);
                break;

            case '3':
                $tipo='GASTOS';
                $presupuesto     = PresupuestoInternoModelo::where('id_tipo_presupuesto',3)->orderBy('partida')->get();
                break;
        }

        // return $ordenamiento;exit;
        return response()->json([
            "success"=>true,
            "presupuesto"=>$presupuesto,
            "tipo"=>$tipo,
            "id_tipo"=>$request->tipo,
            "tipo_next"=>$tipo_next,
            "ordemaniento"=>$ordenamiento
        ]);
    }
    public function ordenarPresupuesto($data)
    {
        $array_data=[];
        $cantidad=0;
        $nivel_maximo=0;
        foreach ($data as $key => $value) {
            $array_data = explode('.',$value->partida);
            $cantidad = sizeof($array_data);
            $value->nivel=$cantidad;
            if ($cantidad>$nivel_maximo) {
                $nivel_maximo=$cantidad;
            }
            // return $cantidad;
        }
        return ["data_ordenada"=>$data,"nivel_maximo"=>$nivel_maximo];
    }
    public function guardar(Request $request)
    {
        return response()->json([
            "success"=>true,
            "status"=>200,
            "data"=>$request->ingresos
        ]);
    }
}
