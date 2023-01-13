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
use App\Models\Finanzas\PresupuestoInternoDetalle;
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
        $presupuesto_interno_count = PresupuestoInterno::count();
        $presupuesto_interno_count = $presupuesto_interno_count +1;
        $codigo = StringHelper::leftZero(2,$presupuesto_interno_count);

        $presupuesto_interno                        = new PresupuestoInterno();
        $presupuesto_interno->codigo                = 'PI-'.$codigo;
        $presupuesto_interno->descripcion           = $request->descripcion;
        $presupuesto_interno->id_grupo              = $request->id_grupo;
        $presupuesto_interno->id_area               = $request->id_area;
        $presupuesto_interno->fecha_registro        = date('Y-m-d H:i:s');
        $presupuesto_interno->estado                = 1;
        $presupuesto_interno->id_moneda             = $request->id_moneda;
        $presupuesto_interno->mes                   = $request->mes;
        $presupuesto_interno->id_tipo_presupuesto   = $request->id_tipo_presupuesto;
        $presupuesto_interno->save();
        // return $request->id_tipo_presupuesto;exit;
        switch ($request->id_tipo_presupuesto) {
            case '1':
                foreach ($request->ingresos as $key => $value) {
                    $ingresos = new PresupuestoInternoDetalle();
                    $ingresos->partida                  = $value['partida'];
                    $ingresos->descripcion              = $value['descripcion'];
                    $ingresos->id_padre                 = $value['id_padre'];
                    $ingresos->id_hijo                  = $value['id_hijo'];
                    $ingresos->monto                    = (!empty($value['monto'])?$value['monto']:0);

                    $ingresos->id_tipo_presupuesto      = 1;
                    $ingresos->id_presupuesto_interno   = $presupuesto_interno->id_presupuesto_interno;
                    $ingresos->id_grupo                 = $request->id_grupo;
                    $ingresos->id_area                  = $request->id_area;
                    $ingresos->fecha_registro           = date('Y-m-d H:i:s');
                    $ingresos->estado                   = 1;
                    $ingresos->save();
                }

                foreach ($request->costos as $key => $value) {
                    $costos = new PresupuestoInternoDetalle();
                    $costos->partida                  = $value['partida'];
                    $costos->descripcion              = $value['descripcion'];
                    $costos->id_padre                 = $value['id_padre'];
                    $costos->id_hijo                  = $value['id_hijo'];
                    $costos->monto                    = (!empty($value['monto'])?$value->monto:0);

                    $costos->id_tipo_presupuesto      = 2;
                    $costos->id_presupuesto_interno   = $presupuesto_interno->id_presupuesto_interno;
                    $costos->id_grupo                 = $request->id_grupo;
                    $costos->id_area                  = $request->id_area;
                    $costos->fecha_registro           = date('Y-m-d H:i:s');
                    $costos->estado                   = 1;
                    $costos->save();
                }
                break;

            case '3':
                foreach ($request->gastos as $key => $value) {
                    $gastos = new PresupuestoInternoDetalle();
                    $gastos->partida                  = $value['partida'];
                    $gastos->descripcion              = $value['descripcion'];
                    $gastos->id_padre                 = $value['id_padre'];
                    $gastos->id_hijo                  = $value['id_hijo'];
                    $gastos->monto                    = (!empty($value['monto'])?$value['monto']:0);

                    $gastos->id_tipo_presupuesto      = 3;
                    $gastos->id_presupuesto_interno   = $presupuesto_interno->id_presupuesto_interno;
                    $gastos->id_grupo                 = $request->id_grupo;
                    $gastos->id_area                  = $request->id_area;
                    $gastos->fecha_registro           = date('Y-m-d H:i:s');
                    $gastos->estado                   = 1;
                    $gastos->save();
                }
                break;
        }


        return response()->json([
            "success"=>true,
            "status"=>200,
            "data"=>''
        ]);
    }
}
