<?php

namespace App\Http\Controllers\Finanzas\Presupuesto;

use App\Exports\PresupuestoInternoExport;
use App\Helpers\StringHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Administracion\Area;
use App\Models\Administracion\Division;
use App\Models\Configuracion\Grupo;
use App\Models\Configuracion\Moneda;
use App\Models\Finanzas\FinanzasArea;
use App\Models\Finanzas\PresupuestoInterno;
use App\Models\Finanzas\PresupuestoInternoDetalle;
use App\Models\Finanzas\PresupuestoInternoModelo;
use Maatwebsite\Excel\Facades\Excel;
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
        $data = PresupuestoInterno::where('presupuesto_interno.estado','!=',7)
        ->select('presupuesto_interno.*', 'sis_grupo.descripcion')
        ->join('configuracion.sis_grupo', 'sis_grupo.id_grupo', '=', 'presupuesto_interno.id_grupo')
            ;
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
        if ($request->tipo_ingresos || $request->tipo_gastos) {
            // return $request->gastos;exit;
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
            $presupuesto_interno->gastos                = $request->tipo_gastos;
            $presupuesto_interno->ingresos              = $request->tipo_ingresos;
            $presupuesto_interno->save();
            // return $request->id_tipo_presupuesto;exit;
            if ($request->tipo_ingresos === '1') {

                foreach ($request->ingresos as $key => $value) {
                    $ingresos = new PresupuestoInternoDetalle();
                    $ingresos->partida                  = $value['partida'];
                    $ingresos->descripcion              = $value['descripcion'];
                    $ingresos->id_padre                 = $value['id_padre'];
                    $ingresos->id_hijo                  = $value['id_hijo'];
                    // $ingresos->monto                    = $value['monto'];

                    $ingresos->id_tipo_presupuesto      = 1;
                    $ingresos->id_presupuesto_interno   = $presupuesto_interno->id_presupuesto_interno;
                    $ingresos->id_grupo                 = $request->id_grupo;
                    $ingresos->id_area                  = $request->id_area;
                    $ingresos->fecha_registro           = date('Y-m-d H:i:s');
                    $ingresos->estado                   = 1;
                    $ingresos->registro                 = $value['registro'];

                    $ingresos->enero                    = $value['enero'];
                    $ingresos->febrero                  = $value['febrero'];
                    $ingresos->marzo                    = $value['marzo'];
                    $ingresos->abril                    = $value['abril'];
                    $ingresos->mayo                     = $value['mayo'];
                    $ingresos->junio                    = $value['junio'];
                    $ingresos->julio                    = $value['julio'];
                    $ingresos->agosto                   = $value['agosto'];
                    $ingresos->setiembre                = $value['setiembre'];
                    $ingresos->octubre                  = $value['octubre'];
                    $ingresos->noviembre                = $value['noviembre'];
                    $ingresos->diciembre                = $value['diciembre'];

                    $ingresos->porcentaje_gobierno      = $value['porcentaje_gobierno'];
                    $ingresos->porcentaje_privado       = $value['porcentaje_privado'];
                    $ingresos->porcentaje_comicion      = $value['porcentaje_comicion'];
                    $ingresos->porcentaje_penalidad     = $value['porcentaje_penalidad'];
                    $ingresos->porcentaje_costo         = $value['porcentaje_costo'];
                    $ingresos->save();
                }

                foreach ($request->costos as $key => $value) {
                    $costos = new PresupuestoInternoDetalle();
                    $costos->partida                  = $value['partida'];
                    $costos->descripcion              = $value['descripcion'];
                    $costos->id_padre                 = $value['id_padre'];
                    $costos->id_hijo                  = $value['id_hijo'];
                    // $costos->monto                    = $value['monto'];

                    $costos->id_tipo_presupuesto      = 2;
                    $costos->id_presupuesto_interno   = $presupuesto_interno->id_presupuesto_interno;
                    $costos->id_grupo                 = $request->id_grupo;
                    $costos->id_area                  = $request->id_area;
                    $costos->fecha_registro           = date('Y-m-d H:i:s');
                    $costos->registro                 = $value['registro'];
                    $costos->estado                   = 1;

                    $costos->enero                    = $value['enero'];
                    $costos->febrero                  = $value['febrero'];
                    $costos->marzo                    = $value['marzo'];
                    $costos->abril                    = $value['abril'];
                    $costos->mayo                     = $value['mayo'];
                    $costos->junio                    = $value['junio'];
                    $costos->julio                    = $value['julio'];
                    $costos->agosto                   = $value['agosto'];
                    $costos->setiembre                = $value['setiembre'];
                    $costos->octubre                  = $value['octubre'];
                    $costos->noviembre                = $value['noviembre'];
                    $costos->diciembre                = $value['diciembre'];

                    $costos->porcentaje_gobierno      = $value['porcentaje_gobierno'];
                    $costos->porcentaje_privado       = $value['porcentaje_privado'];
                    $costos->porcentaje_comicion      = $value['porcentaje_comicion'];
                    $costos->porcentaje_penalidad     = $value['porcentaje_penalidad'];
                    $costos->porcentaje_costo         = $value['porcentaje_costo'];

                    $costos->save();
                }

            }
            if ($request->tipo_gastos === '3') {
                foreach ($request->gastos as $key => $value) {
                    $gastos = new PresupuestoInternoDetalle();
                    $gastos->partida                  = $value['partida'];
                    $gastos->descripcion              = $value['descripcion'];
                    $gastos->id_padre                 = $value['id_padre'];
                    $gastos->id_hijo                  = $value['id_hijo'];
                    // $gastos->monto                    = $value['monto'];

                    $gastos->id_tipo_presupuesto      = 3;
                    $gastos->id_presupuesto_interno   = $presupuesto_interno->id_presupuesto_interno;
                    $gastos->id_grupo                 = $request->id_grupo;
                    $gastos->id_area                  = $request->id_area;
                    $gastos->fecha_registro           = date('Y-m-d H:i:s');
                    $gastos->estado                   = 1;
                    $gastos->registro                 = $value['registro'];

                    $gastos->enero                    = $value['enero'];
                    $gastos->febrero                  = $value['febrero'];
                    $gastos->marzo                    = $value['marzo'];
                    $gastos->abril                    = $value['abril'];
                    $gastos->mayo                     = $value['mayo'];
                    $gastos->junio                    = $value['junio'];
                    $gastos->julio                    = $value['julio'];
                    $gastos->agosto                   = $value['agosto'];
                    $gastos->setiembre                = $value['setiembre'];
                    $gastos->octubre                  = $value['octubre'];
                    $gastos->noviembre                = $value['noviembre'];
                    $gastos->diciembre                = $value['diciembre'];

                    $gastos->porcentaje_gobierno      = $value['porcentaje_gobierno'];
                    $gastos->porcentaje_privado       = $value['porcentaje_privado'];
                    $gastos->porcentaje_comicion      = $value['porcentaje_comicion'];
                    $gastos->porcentaje_penalidad     = $value['porcentaje_penalidad'];
                    $gastos->porcentaje_costo         = $value['porcentaje_costo'];

                    $gastos->save();
                }
            }



            return response()->json([
                "success"=>true,
                "status"=>200,
                "data"=>''
            ]);
        }else{
            return response()->json([
                "success"=>false,
                "status"=>400,
                "title"=>'Presupuesto interno',
                "msg"=>'Seleccione un cuadro de presupuesto',
                "type"=>'warning',
            ]);
        }

    }
    public function editar(Request $request)
    {
        $grupos = Grupo::get();
        // $area = Area::where('estado',1)->get();
        $area = Division::where('estado',1)->get();
        $moneda = Moneda::where('estado',1)->get();


        $id = $request->id;
        $presupuesto_interno = PresupuestoInterno::where('id_presupuesto_interno',$id)->first();
        $ingresos= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id)->where('id_tipo_presupuesto',1)->where('estado', 1)->orderBy('partida')->get();
        $costos= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id)->where('id_tipo_presupuesto',2)->where('estado', 1)->orderBy('partida')->get();
        $gastos = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id)->where('id_tipo_presupuesto',3)->where('estado', 1)->orderBy('partida')->get();

        return view('finanzas.presupuesto_interno.editar', compact('grupos','area','moneda','id','presupuesto_interno','ingresos','costos','gastos'));
    }
    public function actualizar(Request $request)
    {
        // return $request->descripcion;exit;
        $presupuesto_interno                        = PresupuestoInterno::find($request->id_presupuesto_interno);
        // return $presupuesto_interno;exit;
        // $presupuesto_interno->codigo                = 'PI-'.$codigo;
        $presupuesto_interno->descripcion           = $request->descripcion;
        $presupuesto_interno->id_grupo              = $request->id_grupo;
        $presupuesto_interno->id_area               = $request->id_area;
        // $presupuesto_interno->fecha_registro        = date('Y-m-d H:i:s');
        $presupuesto_interno->estado                = 1;
        $presupuesto_interno->id_moneda             = $request->id_moneda;
        $presupuesto_interno->gastos                = $request->tipo_gastos;
        $presupuesto_interno->ingresos              = $request->tipo_ingresos;
        $presupuesto_interno->save();
        // return $request->id_tipo_presupuesto;exit;
        // PresupuestoInternoDetalle::where('estado', 1)
        // ->where('id_presupuesto_interno', $presupuesto_interno->id_presupuesto_interno)
        // ->update(['estado' => 7]);
        // $presupuesto_interno = PresupuestoInternoDetalle::where('id_presupuesto_interno',$request->id_presupuesto_interno)->where('mes',$request->mes)->where('id_tipo_presupuesto',1)->first();
        if ($request->tipo_ingresos==='1') {

            PresupuestoInternoDetalle::where('estado', 1)
            ->where('id_tipo_presupuesto', 1)
            ->where('id_presupuesto_interno', $presupuesto_interno->id_presupuesto_interno)
            ->update(['estado' => 7]);
            foreach ($request->ingresos as $key => $value) {
                $ingresos = new PresupuestoInternoDetalle();
                $ingresos->partida                  = $value['partida'];
                $ingresos->descripcion              = $value['descripcion'];
                $ingresos->id_padre                 = $value['id_padre'];
                $ingresos->id_hijo                  = $value['id_hijo'];
                // $ingresos->monto                    = $value['monto'];

                $ingresos->id_tipo_presupuesto      = 1;
                $ingresos->id_presupuesto_interno   = $presupuesto_interno->id_presupuesto_interno;
                $ingresos->id_grupo                 = $request->id_grupo;
                $ingresos->id_area                  = $request->id_area;
                $ingresos->fecha_registro           = date('Y-m-d H:i:s');
                $ingresos->estado                   = 1;

                $ingresos->registro                 = $value['registro'];

                $ingresos->enero                    = $value['enero'];
                $ingresos->febrero                  = $value['febrero'];
                $ingresos->marzo                    = $value['marzo'];
                $ingresos->abril                    = $value['abril'];
                $ingresos->mayo                     = $value['mayo'];
                $ingresos->junio                    = $value['junio'];
                $ingresos->julio                    = $value['julio'];
                $ingresos->agosto                   = $value['agosto'];
                $ingresos->setiembre                = $value['setiembre'];
                $ingresos->octubre                  = $value['octubre'];
                $ingresos->noviembre                = $value['noviembre'];
                $ingresos->diciembre                = $value['diciembre'];

                $ingresos->porcentaje_gobierno      = $value['porcentaje_gobierno'];
                $ingresos->porcentaje_privado       = $value['porcentaje_privado'];
                $ingresos->porcentaje_comicion      = $value['porcentaje_comicion'];
                $ingresos->porcentaje_penalidad     = $value['porcentaje_penalidad'];
                $ingresos->porcentaje_costo         = $value['porcentaje_costo'];

                $ingresos->save();
            }
            PresupuestoInternoDetalle::where('estado', 1)
            ->where('id_tipo_presupuesto', 2)
            ->where('id_presupuesto_interno', $presupuesto_interno->id_presupuesto_interno)
            ->update(['estado' => 7]);
            foreach ($request->costos as $key => $value) {
                $costos = new PresupuestoInternoDetalle();
                $costos->partida                  = $value['partida'];
                $costos->descripcion              = $value['descripcion'];
                $costos->id_padre                 = $value['id_padre'];
                $costos->id_hijo                  = $value['id_hijo'];
                // $costos->monto                    = $value['monto'];

                $costos->id_tipo_presupuesto      = 2;
                $costos->id_presupuesto_interno   = $presupuesto_interno->id_presupuesto_interno;
                $costos->id_grupo                 = $request->id_grupo;
                $costos->id_area                  = $request->id_area;
                $costos->fecha_registro           = date('Y-m-d H:i:s');
                $costos->estado                   = 1;
                $costos->registro                 = $value['registro'];

                $costos->enero                    = $value['enero'];
                $costos->febrero                  = $value['febrero'];
                $costos->marzo                    = $value['marzo'];
                $costos->abril                    = $value['abril'];
                $costos->mayo                     = $value['mayo'];
                $costos->junio                    = $value['junio'];
                $costos->julio                    = $value['julio'];
                $costos->agosto                   = $value['agosto'];
                $costos->setiembre                = $value['setiembre'];
                $costos->octubre                  = $value['octubre'];
                $costos->noviembre                = $value['noviembre'];
                $costos->diciembre                = $value['diciembre'];

                $costos->porcentaje_gobierno      = $value['porcentaje_gobierno'];
                $costos->porcentaje_privado       = $value['porcentaje_privado'];
                $costos->porcentaje_comicion      = $value['porcentaje_comicion'];
                $costos->porcentaje_penalidad     = $value['porcentaje_penalidad'];
                $costos->porcentaje_costo         = $value['porcentaje_costo'];

                $costos->save();
            }
        }
        if ($request->tipo_gastos==='3') {
            PresupuestoInternoDetalle::where('estado', 1)
            ->where('id_tipo_presupuesto', 3)
            ->where('id_presupuesto_interno', $presupuesto_interno->id_presupuesto_interno)
            ->update(['estado' => 7]);
            foreach ($request->gastos as $key => $value) {
                $gastos = new PresupuestoInternoDetalle();
                $gastos->partida                  = $value['partida'];
                $gastos->descripcion              = $value['descripcion'];
                $gastos->id_padre                 = $value['id_padre'];
                $gastos->id_hijo                  = $value['id_hijo'];
                // $gastos->monto                    = $value['monto'];

                $gastos->id_tipo_presupuesto      = 3;
                $gastos->id_presupuesto_interno   = $presupuesto_interno->id_presupuesto_interno;
                $gastos->id_grupo                 = $request->id_grupo;
                $gastos->id_area                  = $request->id_area;
                $gastos->fecha_registro           = date('Y-m-d H:i:s');
                $gastos->registro                 = $value['registro'];

                $gastos->enero                    = $value['enero'];
                $gastos->febrero                  = $value['febrero'];
                $gastos->marzo                    = $value['marzo'];
                $gastos->abril                    = $value['abril'];
                $gastos->mayo                     = $value['mayo'];
                $gastos->junio                    = $value['junio'];
                $gastos->julio                    = $value['julio'];
                $gastos->agosto                   = $value['agosto'];
                $gastos->setiembre                = $value['setiembre'];
                $gastos->octubre                  = $value['octubre'];
                $gastos->noviembre                = $value['noviembre'];
                $gastos->diciembre                = $value['diciembre'];
                $gastos->estado                   = 1;

                $gastos->porcentaje_gobierno      = $value['porcentaje_gobierno'];
                $gastos->porcentaje_privado       = $value['porcentaje_privado'];
                $gastos->porcentaje_comicion      = $value['porcentaje_comicion'];
                $gastos->porcentaje_penalidad     = $value['porcentaje_penalidad'];
                $gastos->porcentaje_costo         = $value['porcentaje_costo'];
                $gastos->save();
            }
        }

        return response()->json([
            "success"=>true,
            "status"=>200,
            "data"=>''
        ]);
    }
    public function eliminar(Request $request)
    {
        $presupuesto_interno = PresupuestoInterno::find($request->id);
        $presupuesto_interno->estado = 7;
        $presupuesto_interno->save();
        return response()->json([
            "success"=>true,
            "status"=>200,
            "data"=>''
        ]);
    }
    public function getArea(Request $request)
    {
        $area = Division::where('estado',1)->where('grupo_id',$request->id_grupo)->get();
        return response()->json([
            "success"=>true,
            "status"=>200,
            "data"=>$area
        ]);
    }
    public function getPresupuestoInterno(Request $request)
    {

        $data = PresupuestoInterno::select('presupuesto_interno.*', 'sis_grupo.descripcion as grupo', 'area.descripcion as area','sis_moneda.descripcion as moneda','sis_moneda.simbolo')
        ->join('configuracion.sis_grupo', 'sis_grupo.id_grupo', '=', 'presupuesto_interno.id_grupo')
        ->join('finanzas.area', 'area.id_area', '=', 'presupuesto_interno.id_area')
        ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'presupuesto_interno.id_moneda')
        ->where('id_presupuesto_interno',$request->id)
        ->first();
        $array_presupuesto = [];
        $array_presupuesto['ingresos']=[];
        $array_presupuesto['costos']=[];
        $array_presupuesto['gastos']=[];

        $ingresos = PresupuestoInternoDetalle::where('id_presupuesto_interno',$request->id)->where('id_tipo_presupuesto',1)->where('estado', 1)->orderBy('partida')->get();

        $costos   = PresupuestoInternoDetalle::where('id_presupuesto_interno',$request->id)->where('id_tipo_presupuesto',2)->where('estado', 1)->orderBy('partida')->get();

        $array_presupuesto['ingresos']=$ingresos;
        $array_presupuesto['costos']=$costos;

        $gastos     = PresupuestoInternoDetalle::where('id_presupuesto_interno',$request->id)->where('id_tipo_presupuesto',3)->where('estado', 1)->orderBy('partida')->get();
        $array_presupuesto['gastos']=$gastos;

        return Excel::download(new PresupuestoInternoExport($data, $array_presupuesto), 'presupuesto_interno.xlsx');
        // return response()->json([
        //     "success"=>true,
        //     "status"=>200,
        //     "data"=>$data,
        //     "presupuesto"=>$array_presupuesto
        // ]);
    }
    public function aprobar(Request $request)
    {
        $presupuesto_interno = PresupuestoInterno::find($request->id);
        $presupuesto_interno->estado = 2;
        $presupuesto_interno->save();
        return response()->json([
            "success"=>true,
            "status"=>200,
        ]);
    }
}
