<?php

namespace App\Http\Controllers\Finanzas\Presupuesto;

use App\Exports\PresupuestoInternoExport;
use App\Helpers\StringHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\administracion\AdmGrupo;
use App\Models\Administracion\Area;
use App\Models\Administracion\Division;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Requerimiento;
use App\Models\Configuracion\Grupo;
use App\Models\Configuracion\Moneda;
use App\Models\Finanzas\FinanzasArea;
use App\Models\Finanzas\HistorialPresupuestoInternoSaldo;
use App\Models\Finanzas\PresupuestoInterno;
use App\Models\Finanzas\PresupuestoInternoDetalle;
use App\Models\Finanzas\PresupuestoInternoModelo;
use App\Models\Logistica\Orden;
use App\Models\mgcp\CuadroCosto\HistorialPrecio;
use App\Models\Tesoreria\RequerimientoPago;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Debugbar;

class PresupuestoInternoController extends Controller
{
    //
    public function lista()
    {
        return view('finanzas.presupuesto_interno.lista');
    }
    public function listaPresupuestoInterno()
    {
        $data = PresupuestoInterno::where('presupuesto_interno.estado', '!=', 7)
            ->select('presupuesto_interno.*', 'sis_grupo.descripcion')
            ->join('configuracion.sis_grupo', 'sis_grupo.id_grupo', '=', 'presupuesto_interno.id_grupo');
        return DataTables::of($data)
            // ->toJson();
            ->make(true);
    }
    public function crear()
    {
        // $grupos = Grupo::get();
        $grupos = AdmGrupo::get();
        $area = FinanzasArea::where('estado', 1)->get();
        $moneda = Moneda::where('estado', 1)->get();

        $presupuesto_interno = PresupuestoInterno::count();

        return view('finanzas.presupuesto_interno.crear', compact('grupos', 'area', 'moneda'));
    }
    public function presupuestoInternoDetalle(Request $request)
    {
        // return $request->tipo;exit;
        $presupuesto = [];
        $tipo = '';
        $tipo_next = '';
        $ordenamiento = [];
        switch ($request->tipo) {
            case '1':
                $tipo = 'INGRESOS';
                $presupuesto   = PresupuestoInternoModelo::where('id_tipo_presupuesto', 1)->orderBy('partida')->get();
                $tipo_next = 2;
                $ordenamiento = $this->ordenarPresupuesto($presupuesto);
                break;
            case '2':
                $tipo = 'COSTOS';
                $presupuesto     = PresupuestoInternoModelo::where('id_tipo_presupuesto', 2)->orderBy('partida')->get();
                $tipo_next = 3;
                $ordenamiento = $this->ordenarPresupuesto($presupuesto);
                break;

            case '3':
                $tipo = 'GASTOS';
                $presupuesto     = PresupuestoInternoModelo::where('id_tipo_presupuesto', 3)->orderBy('partida')->get();
                break;
        }

        // return $ordenamiento;exit;
        return response()->json([
            "success" => true,
            "presupuesto" => $presupuesto,
            "tipo" => $tipo,
            "id_tipo" => $request->tipo,
            "tipo_next" => $tipo_next,
            "ordemaniento" => $ordenamiento
        ]);
    }
    public function ordenarPresupuesto($data)
    {
        $array_data = [];
        $cantidad = 0;
        $nivel_maximo = 0;
        foreach ($data as $key => $value) {
            $array_data = explode('.', $value->partida);
            $cantidad = sizeof($array_data);
            $value->nivel = $cantidad;
            if ($cantidad > $nivel_maximo) {
                $nivel_maximo = $cantidad;
            }
            // return $cantidad;
        }
        return ["data_ordenada" => $data, "nivel_maximo" => $nivel_maximo];
    }
    public function guardar(Request $request)
    {
        if ($request->tipo_ingresos || $request->tipo_gastos) {
            // return $request->gastos;exit;
            // return $request->costos;exit;
            $presupuesto_interno_count = PresupuestoInterno::count();
            $presupuesto_interno_count = $presupuesto_interno_count + 1;
            $codigo = StringHelper::leftZero(2, $presupuesto_interno_count);

            $presupuesto_interno                        = new PresupuestoInterno();
            $presupuesto_interno->codigo                = 'PI-' . $codigo;
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

                    $ingresos->enero_aux = $value['enero'];
                    $ingresos->febrero_aux = $value['febrero'];
                    $ingresos->marzo_aux = $value['marzo'];
                    $ingresos->abril_aux = $value['abril'];
                    $ingresos->mayo_aux = $value['mayo'];
                    $ingresos->junio_aux = $value['junio'];
                    $ingresos->julio_aux = $value['julio'];
                    $ingresos->agosto_aux = $value['agosto'];
                    $ingresos->setiembre_aux = $value['setiembre'];
                    $ingresos->octubre_aux = $value['octubre'];
                    $ingresos->noviembre_aux = $value['noviembre'];
                    $ingresos->diciembre_aux = $value['diciembre'];

                    $ingresos->save();


                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['enero']));
                    $historial->mes = 1;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['febrero']));
                    $historial->mes = 2;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['marzo']));
                    $historial->mes = 3;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['abril']));
                    $historial->mes = 4;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['mayo']));
                    $historial->mes = 5;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['junio']));
                    $historial->mes = 6;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['julio']));
                    $historial->mes = 7;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['agosto']));
                    $historial->mes = 8;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['setiembre']));
                    $historial->mes = 9;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['octubre']));
                    $historial->mes = 10;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['noviembre']));
                    $historial->mes = 11;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['diciembre']));
                    $historial->mes = 12;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
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

                    $costos->enero_aux = $value['enero'];
                    $costos->febrero_aux = $value['febrero'];
                    $costos->marzo_aux = $value['marzo'];
                    $costos->abril_aux = $value['abril'];
                    $costos->mayo_aux = $value['mayo'];
                    $costos->junio_aux = $value['junio'];
                    $costos->julio_aux = $value['julio'];
                    $costos->agosto_aux = $value['agosto'];
                    $costos->setiembre_aux = $value['setiembre'];
                    $costos->octubre_aux = $value['octubre'];
                    $costos->noviembre_aux = $value['noviembre'];
                    $costos->diciembre_aux = $value['diciembre'];

                    $costos->save();

                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['enero']));
                    $historial->mes = 1;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['febrero']));
                    $historial->mes = 2;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['marzo']));
                    $historial->mes = 3;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['abril']));
                    $historial->mes = 4;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['mayo']));
                    $historial->mes = 5;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['junio']));
                    $historial->mes = 6;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['julio']));
                    $historial->mes = 7;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['agosto']));
                    $historial->mes = 8;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['setiembre']));
                    $historial->mes = 9;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['octubre']));
                    $historial->mes = 10;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['noviembre']));
                    $historial->mes = 11;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['diciembre']));
                    $historial->mes = 12;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
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

                    $gastos->enero_aux = $value['enero'];
                    $gastos->febrero_aux = $value['febrero'];
                    $gastos->marzo_aux = $value['marzo'];
                    $gastos->abril_aux = $value['abril'];
                    $gastos->mayo_aux = $value['mayo'];
                    $gastos->junio_aux = $value['junio'];
                    $gastos->julio_aux = $value['julio'];
                    $gastos->agosto_aux = $value['agosto'];
                    $gastos->setiembre_aux = $value['setiembre'];
                    $gastos->octubre_aux = $value['octubre'];
                    $gastos->noviembre_aux = $value['noviembre'];
                    $gastos->diciembre_aux = $value['diciembre'];
                    $gastos->save();

                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['enero']));
                    $historial->mes = 1;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['febrero']));
                    $historial->mes = 2;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['marzo']));
                    $historial->mes = 3;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['abril']));
                    $historial->mes = 4;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['mayo']));
                    $historial->mes = 5;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['junio']));
                    $historial->mes = 6;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['julio']));
                    $historial->mes = 7;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['agosto']));
                    $historial->mes = 8;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['setiembre']));
                    $historial->mes = 9;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['octubre']));
                    $historial->mes = 10;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['noviembre']));
                    $historial->mes = 11;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", $value['diciembre']));
                    $historial->mes = 12;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                    // $historial->operacion = 1;
                    $historial->save();
                }
            }



            return response()->json([
                "success" => true,
                "status" => 200,
                "data" => ''
            ]);
        } else {
            return response()->json([
                "success" => false,
                "status" => 400,
                "title" => 'Presupuesto interno',
                "msg" => 'Seleccione un cuadro de presupuesto',
                "type" => 'warning',
            ]);
        }
    }
    public function editar(Request $request)
    {
        $grupos = Grupo::get();
        // $area = Area::where('estado',1)->get();
        $area = Division::where('estado', 1)->get();
        $moneda = Moneda::where('estado', 1)->get();


        $id = $request->id;
        $presupuesto_interno = PresupuestoInterno::where('id_presupuesto_interno', $id)->first();
        $ingresos = PresupuestoInternoDetalle::where('id_presupuesto_interno', $id)->where('id_tipo_presupuesto', 1)->where('estado', 1)->orderBy('partida')->get();
        $costos = PresupuestoInternoDetalle::where('id_presupuesto_interno', $id)->where('id_tipo_presupuesto', 2)->where('estado', 1)->orderBy('partida')->get();
        $gastos = PresupuestoInternoDetalle::where('id_presupuesto_interno', $id)->where('id_tipo_presupuesto', 3)->where('estado', 1)->orderBy('partida')->get();

        // return PresupuestoInterno::calcularTotalPresupuestoFilas($id,2);exit;
        // return PresupuestoInterno::calcularTotalMensualColumnas($id,2,'02.01.01.01','enero');exit;

        // return PresupuestoInterno::calcularTotalMensualColumnasPorcentajes($id,1,'01.01.01.01','enero');exit;


        return view('finanzas.presupuesto_interno.editar', compact('grupos', 'area', 'moneda', 'id', 'presupuesto_interno', 'ingresos', 'costos', 'gastos'));
    }
    public function actualizar(Request $request)
    {

        $presupuesto_interno                        = PresupuestoInterno::find($request->id_presupuesto_interno);

        $presupuesto_interno->descripcion           = $request->descripcion;
        $presupuesto_interno->id_grupo              = $request->id_grupo;
        $presupuesto_interno->id_area               = $request->id_area;

        $presupuesto_interno->id_moneda             = $request->id_moneda;
        $presupuesto_interno->gastos                = $request->tipo_gastos;
        $presupuesto_interno->ingresos              = $request->tipo_ingresos;
        $presupuesto_interno->save();

        if ($request->tipo_ingresos === '1') {

            // PresupuestoInternoDetalle::where('estado', 1)
            // ->where('id_tipo_presupuesto', 1)
            // ->where('id_presupuesto_interno', $presupuesto_interno->id_presupuesto_interno)
            // ->update(['estado' => 7]);

            foreach ($request->ingresos as $key => $value) {
                // calculamos la diferencia entre el monto nuevo inicial y el anterior monto inicial
                $auxiliar = PresupuestoInternoDetalle::find($value['id_presupuesto_interno_detalle']);

                $diferencia_enere       = $this->diferencia($auxiliar->enero, $value['enero'])[0];
                $diferencia_febrero     = $this->diferencia($auxiliar->febrero, $value['febrero'])[0];
                $diferencia_marzo       = $this->diferencia($auxiliar->marzo, $value['marzo'])[0];
                $diferencia_abril       = $this->diferencia($auxiliar->abril, $value['abril'])[0];
                $diferencia_mayo        = $this->diferencia($auxiliar->mayo, $value['mayo'])[0];
                $diferencia_junio       = $this->diferencia($auxiliar->junio, $value['junio'])[0];
                $diferencia_julio       = $this->diferencia($auxiliar->julio, $value['julio'])[0];
                $diferencia_agosto      = $this->diferencia($auxiliar->agosto, $value['agosto'])[0];
                $diferencia_setiembre   = $this->diferencia($auxiliar->setiembre, $value['setiembre'])[0];
                $diferencia_octubre     = $this->diferencia($auxiliar->octubre, $value['octubre'])[0];
                $diferencia_noviembre   = $this->diferencia($auxiliar->noviembre, $value['noviembre'])[0];
                $diferencia_diciembre   = $this->diferencia($auxiliar->diciembre, $value['diciembre'])[0];


                // ------------------------------------------------------
                $ingresos = PresupuestoInternoDetalle::find($value['id_presupuesto_interno_detalle']);
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
                // $ingresos->estado                   = 1;
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

                // guardamos la diferencia al momento de actualizar el presupuesto------------------



                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_enere;
                $historial->mes = 1;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_febrero;
                $historial->mes = 2;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_marzo;
                $historial->mes = 3;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_abril;
                $historial->mes = 4;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_mayo;
                $historial->mes = 5;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_junio;
                $historial->mes = 6;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_julio;
                $historial->mes = 7;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_agosto;
                $historial->mes = 8;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_setiembre;
                $historial->mes = 9;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_octubre;
                $historial->mes = 10;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_noviembre;
                $historial->mes = 11;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $ingresos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_diciembre;
                $historial->mes = 12;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
            }
            // PresupuestoInternoDetalle::where('estado', 1)
            // ->where('id_tipo_presupuesto', 2)
            // ->where('id_presupuesto_interno', $presupuesto_interno->id_presupuesto_interno)
            // ->update(['estado' => 7]);
            foreach ($request->costos as $key => $value) {
                $auxiliar = PresupuestoInternoDetalle::find($value['id_presupuesto_interno_detalle']);
                $costos = PresupuestoInternoDetalle::find($value['id_presupuesto_interno_detalle']);
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
                // $costos->estado                   = 1;
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

                // auxiliares ------------------------------
                $diferencia_enere       = $this->diferencia($auxiliar->enero, $value['enero'])[0];
                $diferencia_febrero     = $this->diferencia($auxiliar->febrero, $value['febrero'])[0];
                $diferencia_marzo       = $this->diferencia($auxiliar->marzo, $value['marzo'])[0];
                $diferencia_abril       = $this->diferencia($auxiliar->abril, $value['abril'])[0];
                $diferencia_mayo        = $this->diferencia($auxiliar->mayo, $value['mayo'])[0];
                $diferencia_junio       = $this->diferencia($auxiliar->junio, $value['junio'])[0];
                $diferencia_julio       = $this->diferencia($auxiliar->julio, $value['julio'])[0];
                $diferencia_agosto      = $this->diferencia($auxiliar->agosto, $value['agosto'])[0];
                $diferencia_setiembre   = $this->diferencia($auxiliar->setiembre, $value['setiembre'])[0];
                $diferencia_octubre     = $this->diferencia($auxiliar->octubre, $value['octubre'])[0];
                $diferencia_noviembre   = $this->diferencia($auxiliar->noviembre, $value['noviembre'])[0];
                $diferencia_diciembre   = $this->diferencia($auxiliar->diciembre, $value['diciembre'])[0];

                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_enere;
                $historial->mes = 1;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_febrero;
                $historial->mes = 2;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_marzo;
                $historial->mes = 3;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_abril;
                $historial->mes = 4;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_mayo;
                $historial->mes = 5;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_junio;
                $historial->mes = 6;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_julio;
                $historial->mes = 7;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_agosto;
                $historial->mes = 8;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_setiembre;
                $historial->mes = 9;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_octubre;
                $historial->mes = 10;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_noviembre;
                $historial->mes = 11;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $costos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_diciembre;
                $historial->mes = 12;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->save();
            }
        }
        if ($request->tipo_gastos === '3') {
            // PresupuestoInternoDetalle::where('estado', 1)
            // ->where('id_tipo_presupuesto', 3)
            // ->where('id_presupuesto_interno', $presupuesto_interno->id_presupuesto_interno)
            // ->update(['estado' => 7]);
            foreach ($request->gastos as $key => $value) {
                $auxiliar = PresupuestoInternoDetalle::find($value['id_presupuesto_interno_detalle']);
                // obtener los gastos-------------------------------
                $gasto_enero       = $this->diferencia($auxiliar->enero, $auxiliar->enero_aux)[0];
                $gasto_febrero     = $this->diferencia($auxiliar->febrero, $auxiliar->febrero_aux)[0];
                $gasto_marzo       = $this->diferencia($auxiliar->marzo, $auxiliar->marzo_aux)[0];
                $gasto_abril       = $this->diferencia($auxiliar->abril, $auxiliar->abril_aux)[0];
                $gasto_mayo        = $this->diferencia($auxiliar->mayo, $auxiliar->mayo_aux)[0];
                $gasto_junio       = $this->diferencia($auxiliar->junio, $auxiliar->junio_aux)[0];
                $gasto_julio       = $this->diferencia($auxiliar->julio, $auxiliar->julio_aux)[0];
                $gasto_agosto      = $this->diferencia($auxiliar->agosto, $auxiliar->agosto_aux)[0];
                $gasto_setiembre   = $this->diferencia($auxiliar->setiembre, $auxiliar->setiembre_aux)[0];
                $gasto_octubre     = $this->diferencia($auxiliar->octubre, $auxiliar->octubre_aux)[0];
                $gasto_noviembre   = $this->diferencia($auxiliar->noviembre, $auxiliar->noviembre_aux)[0];
                $gasto_diciembre   = $this->diferencia($auxiliar->diciembre, $auxiliar->diciembre_aux)[0];
                // ----------------------------------------------------
                // obtener el nuevo saldo --------------------------------
                $nuevo_saldo_enere       = floatval(str_replace(",", "", $value['enero']))  - $gasto_enero;
                $nuevo_saldo_febrero     = floatval(str_replace(",", "", $value['febrero']))  - $gasto_febrero;
                $nuevo_saldo_marzo       = floatval(str_replace(",", "", $value['marzo']))  - $gasto_marzo;
                $nuevo_saldo_abril       = floatval(str_replace(",", "", $value['abril']))  - $gasto_abril;
                $nuevo_saldo_mayo        = floatval(str_replace(",", "", $value['mayo']))  - $gasto_mayo;
                $nuevo_saldo_junio       = floatval(str_replace(",", "", $value['junio']))  - $gasto_junio;
                $nuevo_saldo_julio       = floatval(str_replace(",", "", $value['julio']))  - $gasto_julio;
                $nuevo_saldo_agosto      = floatval(str_replace(",", "", $value['agosto']))  - $gasto_agosto;
                $nuevo_saldo_setiembre   = floatval(str_replace(",", "", $value['setiembre']))  - $gasto_setiembre;
                $nuevo_saldo_octubre     = floatval(str_replace(",", "", $value['octubre']))  - $gasto_octubre;
                $nuevo_saldo_noviembre   = floatval(str_replace(",", "", $value['noviembre']))  - $gasto_noviembre;
                $nuevo_saldo_diciembre   = floatval(str_replace(",", "", $value['diciembre']))  - $gasto_diciembre;
                // ----------------------------------------------------
                $gastos = PresupuestoInternoDetalle::find($value['id_presupuesto_interno_detalle']);
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
                // $gastos->estado                   = 1;

                $gastos->porcentaje_gobierno      = $value['porcentaje_gobierno'];
                $gastos->porcentaje_privado       = $value['porcentaje_privado'];
                $gastos->porcentaje_comicion      = $value['porcentaje_comicion'];
                $gastos->porcentaje_penalidad     = $value['porcentaje_penalidad'];
                $gastos->porcentaje_costo         = $value['porcentaje_costo'];
                // auxiliares ------------------------------
                $gastos->enero_aux      = $nuevo_saldo_enere;
                $gastos->febrero_aux    = $nuevo_saldo_febrero;
                $gastos->marzo_aux      = $nuevo_saldo_marzo;
                $gastos->abril_aux      = $nuevo_saldo_abril;
                $gastos->mayo_aux       = $nuevo_saldo_mayo;
                $gastos->junio_aux      = $nuevo_saldo_junio;
                $gastos->julio_aux      = $nuevo_saldo_julio;
                $gastos->agosto_aux     = $nuevo_saldo_agosto;
                $gastos->setiembre_aux  = $nuevo_saldo_setiembre;
                $gastos->octubre_aux    = $nuevo_saldo_octubre;
                $gastos->noviembre_aux  = $nuevo_saldo_noviembre;
                $gastos->diciembre_aux  = $nuevo_saldo_diciembre;

                // registrar los salgos
                $gastos->save();

                // historial de registro ------------------------------
                $diferencia_enere       = $this->diferencia($auxiliar->enero, $value['enero']);
                $diferencia_febrero     = $this->diferencia($auxiliar->febrero, $value['febrero']);
                $diferencia_marzo       = $this->diferencia($auxiliar->marzo, $value['marzo']);
                $diferencia_abril       = $this->diferencia($auxiliar->abril, $value['abril']);
                $diferencia_mayo        = $this->diferencia($auxiliar->mayo, $value['mayo']);
                $diferencia_junio       = $this->diferencia($auxiliar->junio, $value['junio']);
                $diferencia_julio       = $this->diferencia($auxiliar->julio, $value['julio']);
                $diferencia_agosto      = $this->diferencia($auxiliar->agosto, $value['agosto']);
                $diferencia_setiembre   = $this->diferencia($auxiliar->setiembre, $value['setiembre']);
                $diferencia_octubre     = $this->diferencia($auxiliar->octubre, $value['octubre']);
                $diferencia_noviembre   = $this->diferencia($auxiliar->noviembre, $value['noviembre']);
                $diferencia_diciembre   = $this->diferencia($auxiliar->diciembre, $value['diciembre']);


                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno  = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida              = $gastos->id_presupuesto_interno_detalle;
                $historial->tipo                    = 'MODIFICACION';
                $historial->importe                 = $diferencia_enere[0];
                $historial->mes                     = 1;
                $historial->fecha_registro          = date('Y-m-d H:i:s');
                $historial->estado                  = 1;
                $historial->operacion               = $diferencia_enere[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_febrero[0];
                $historial->mes = 2;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->operacion               = $diferencia_febrero[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_marzo[0];
                $historial->mes = 3;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->operacion               = $diferencia_marzo[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_abril[0];
                $historial->mes = 4;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->operacion               = $diferencia_abril[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_mayo[0];
                $historial->mes = 5;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->operacion               = $diferencia_mayo[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_junio[0];
                $historial->mes = 6;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->operacion               = $diferencia_junio[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_julio[0];
                $historial->mes = 7;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->operacion               = $diferencia_julio[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_agosto[0];
                $historial->mes = 8;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->operacion               = $diferencia_agosto[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_setiembre[0];
                $historial->mes = 9;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->operacion               = $diferencia_setiembre[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_octubre[0];
                $historial->mes = 10;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->operacion               = $diferencia_octubre[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_noviembre[0];
                $historial->mes = 11;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->operacion               = $diferencia_noviembre[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                $historial->tipo = 'MODIFICACION';
                $historial->importe = $diferencia_diciembre[0];
                $historial->mes = 12;
                $historial->fecha_registro = date('Y-m-d H:i:s');
                $historial->estado = 1;
                $historial->operacion               = $diferencia_diciembre[1];
                $historial->save();

                // return $diferencia_enere;exit;
            }
        }

        return response()->json([
            "success" => true,
            "status" => 200,
            "data" => ''
        ]);
    }
    public function eliminar(Request $request)
    {
        $presupuesto_interno = PresupuestoInterno::find($request->id);
        $presupuesto_interno->estado = 7;
        $presupuesto_interno->save();
        return response()->json([
            "success" => true,
            "status" => 200,
            "data" => ''
        ]);
    }
    public function getArea(Request $request)
    {
        $area = Division::where('estado', 1)->where('grupo_id', $request->id_grupo)->get();
        return response()->json([
            "success" => true,
            "status" => 200,
            "data" => $area
        ]);
    }
    public function getPresupuestoInterno(Request $request)
    {

        $data = PresupuestoInterno::select('presupuesto_interno.*', 'sis_grupo.descripcion as grupo', 'area.descripcion as area', 'sis_moneda.descripcion as moneda', 'sis_moneda.simbolo')
            ->join('configuracion.sis_grupo', 'sis_grupo.id_grupo', '=', 'presupuesto_interno.id_grupo')
            ->join('finanzas.area', 'area.id_area', '=', 'presupuesto_interno.id_area')
            ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'presupuesto_interno.id_moneda')
            ->where('id_presupuesto_interno', $request->id)
            ->first();
        $array_presupuesto = [];
        $array_presupuesto['ingresos'] = [];
        $array_presupuesto['costos'] = [];
        $array_presupuesto['gastos'] = [];

        $ingresos = PresupuestoInternoDetalle::where('id_presupuesto_interno', $request->id)->where('id_tipo_presupuesto', 1)->where('estado', 1)->orderBy('partida')->get();

        $costos   = PresupuestoInternoDetalle::where('id_presupuesto_interno', $request->id)->where('id_tipo_presupuesto', 2)->where('estado', 1)->orderBy('partida')->get();

        $array_presupuesto['ingresos'] = $ingresos;
        $array_presupuesto['costos'] = $costos;

        $gastos     = PresupuestoInternoDetalle::where('id_presupuesto_interno', $request->id)->where('id_tipo_presupuesto', 3)->where('estado', 1)->orderBy('partida')->get();
        $array_presupuesto['gastos'] = $gastos;

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
            "success" => true,
            "status" => 200,
        ]);
    }

    public function comboPresupuestoInterno($idGrupo, $idArea)
    {
        $data = PresupuestoInterno::where('presupuesto_interno.estado', '!=', 7)
        ->when((intval($idGrupo) > 0), function ($query)  use ($idGrupo) {
            return $query->whereRaw('presupuesto_interno.id_grupo = ' . $idGrupo);
        })
        ->when((intval($idArea) > 0), function ($query)  use ($idArea) {
            return $query->whereRaw('presupuesto_interno.id_area = ' . $idArea);
        })
        ->select('presupuesto_interno.*', 'adm_grupo.descripcion as descripcion_grupo', 'division.descripcion as descripcion_area')
        ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'presupuesto_interno.id_grupo')
        ->leftJoin('administracion.division', 'division.id_division', '=', 'presupuesto_interno.id_area')->get();
        return $data;
    }

    public function obtenerDetallePresupuestoInterno($idPresupuestoIterno, $anualOMensual)
    {
        $presupuestoInterno = PresupuestoInterno::with(['detalle' => function ($q) use ($idPresupuestoIterno) {
            $q->where([['id_presupuesto_interno', $idPresupuestoIterno], ['estado', '!=', 7]])->orderBy('partida', 'asc');
        }])->where([['id_presupuesto_interno', $idPresupuestoIterno], ['estado', 2]])->get();

        if ($anualOMensual == 'ANUAL') {
            $totalFilas = PresupuestoInterno::calcularTotalPresupuestoFilas($idPresupuestoIterno, 3); // para requerimiento enviar 3= gastos
        } elseif ($anualOMensual == 'MENSUAL') {
            $totalFilas = PresupuestoInterno::obtenerPresupuestoFilasMes($idPresupuestoIterno, 3, 0); // para requerimiento enviar 3= gastos

        }
        $detalleRequerimiento = PresupuestoInterno::calcularConsumidoPresupuestoFilas($idPresupuestoIterno, 3); // para requerimiento enviar 3= gastos


        foreach ($presupuestoInterno as $key => $Presup) {
            foreach ($Presup['detalle'] as $keyd => $detPresup) {

                $detPresup['monto_inicial'] = 0;
                $detPresup['monto_consumido'] = 0;
                $detPresup['monto_saldo'] = 0;
            }
        }
        //  completar monto inicial;
        foreach ($presupuestoInterno as $key => $Presup) {
            foreach ($Presup['detalle'] as $key => $detPresup) {
                foreach ($totalFilas as $key => $totFila) {
                    if ($totFila['partida'] == $detPresup['partida']) {
                        $detPresup['monto_inicial'] = $totFila['total'];
                    }
                }
            }
        }
        //  completar monto consumido;
        foreach ($presupuestoInterno as $key => $Presup) {
            foreach ($Presup['detalle'] as $key => $detPresup) {
                foreach ($detalleRequerimiento as $key => $detalleReq) {
                    if ($detalleReq['partida'] == $detPresup['partida']) {
                        $detPresup['monto_consumido'] += $detalleReq['subtotal_orden'];
                    }
                }
            }
        }

        //  completar monto saldo;
        foreach ($presupuestoInterno as $key => $Presup) {
            foreach ($Presup['detalle'] as $key => $detPresup) {
                $detPresup['monto_saldo'] = (floatval($detPresup['monto_inicial']) - floatval($detPresup['monto_consumido']));
            }
        }



        return $presupuestoInterno;
    }
    public function editarMontoPartida(Request $request)
    {
        // return PresupuestoInterno::calcularTotalPresupuestoFilas($id,2);exit;
        // return PresupuestoInterno::calcularTotalMensualColumnas($id,2,'02.01.01.01','enero');exit;
        // return $request->all();exit;
        $mes = $request->mes;

        $ingresos   = PresupuestoInternoDetalle::where('id_presupuesto_interno', $request->id)->where('id_tipo_presupuesto', 1)->where('estado', 1)->orderBy('partida')->get();
        $costos     = PresupuestoInternoDetalle::where('id_presupuesto_interno', $request->id)->where('id_tipo_presupuesto', 2)->where('estado', 1)->orderBy('partida')->get();
        $gastos     = PresupuestoInternoDetalle::where('id_presupuesto_interno', $request->id)->where('id_tipo_presupuesto', 3)->where('estado', 1)->orderBy('partida')->get();
        $success = false;
        if (sizeof($ingresos) > 0) {
            $presupuesto_interno_partida_modificar = PresupuestoInternoDetalle::where('id_presupuesto_interno', $request->id)->where('estado', 1)->where('partida', $request->partida)->where('id_tipo_presupuesto', 1)->where('registro', 2)->first();
            if ($presupuesto_interno_partida_modificar) {
                $presupuesto_interno_partida_modificar->$mes = number_format($request->monto, 2);
                $presupuesto_interno_partida_modificar->save();
                PresupuestoInterno::calcularTotalMensualColumnas($request->id, 1, $request->partida, $request->mes);

                PresupuestoInterno::calcularTotalMensualColumnasPorcentajes($request->id, 1, $request->partida, $request->mes);
                $partida_costos = '02';
                foreach (explode('.', $request->partida) as $key => $value) {
                    if ($key !== 0) {
                        $partida_costos = $partida_costos . '.' . $value;
                    }
                }
                PresupuestoInterno::calcularTotalMensualColumnas($request->id, 2, $partida_costos, $request->mes);
                $success = true;
            }
        }
        if (sizeof($gastos) > 0) {
            $presupuesto_interno_partida_modificar = PresupuestoInternoDetalle::where('id_presupuesto_interno', $request->id)->where('estado', 1)->where('partida', $request->partida)->where('id_tipo_presupuesto', 3)->where('registro', 2)->first();
            if ($presupuesto_interno_partida_modificar) {
                $presupuesto_interno_partida_modificar->$mes = number_format($request->monto, 2);
                $presupuesto_interno_partida_modificar->save();

                PresupuestoInterno::calcularTotalMensualColumnasPorcentajes($request->id, 3, $request->partida, $request->mes);

                PresupuestoInterno::calcularTotalMensualColumnas($request->id, 3, $request->partida, $request->mes);
                if (
                    $presupuesto_interno_partida_modificar->partida === '03.01.01.01' || $presupuesto_interno_partida_modificar->partida === '03.01.01.02' || $presupuesto_interno_partida_modificar->partida === '03.01.01.03'
                ) {
                    PresupuestoInterno::calcularTotalMensualColumnas($request->id, 3, '03.01.02.01', $request->mes);
                    PresupuestoInterno::calcularTotalMensualColumnas($request->id, 3, '03.01.03.01', $request->mes);
                }
                $success = true;
            }
        }


        return response()->json($success, 200);
    }
    public function buscarPartidaCombo(Request $request)
    {
        $presupuesto_interno_detalle = [];
        if (!empty($request->searchTerm)) {
            $searchTerm = $request->searchTerm;
            $presupuesto_interno_detalle = PresupuestoInternoDetalle::where('estado', 1);
            if (!empty($request->searchTerm)) {
                $presupuesto_interno_detalle = $presupuesto_interno_detalle->where('partida', 'like', '%' . $searchTerm . '%')
                    ->where('id_presupuesto_interno', $request->id_presupuesto_interno)
                    ->where('registro', '2')
                    ->whereNotIn('partida', ['03.01.02.01', '03.01.02.02', '03.01.02.03', '03.01.03.01', '03.01.03.02', '03.01.03.03']);
            }
            $presupuesto_interno_detalle = $presupuesto_interno_detalle->get();
            return response()->json($presupuesto_interno_detalle);
        } else {
            return response()->json([
                "status" => 404,
                "success" => false
            ]);
        }
    }
    public function diferencia($monto_1, $monto_2)
    {
        $diferencia = floatval(str_replace(",", "", $monto_1)) - floatval(str_replace(",", "", $monto_2));
        $operacion = ($diferencia >= 0 ? 'R' : 'S');
        return [$diferencia, $operacion];
    }
    public function cierreMes()
    {
        $numero_mes = date("m");
        $numero_mes_siguiente = date('m', strtotime('+1 month'));;
        $nombre_mes = $this->mes($numero_mes);
        $nombre_mes_siguiente = $this->mes($numero_mes_siguiente);

        $saldo = PresupuestoInterno::cierreMensual(3, $numero_mes, $nombre_mes, $numero_mes_siguiente, $nombre_mes_siguiente);
        // PresupuestoInterno::calcularColumnaAuxMensual(11, 3, 1165,$nombre_mes);

        return response()->json($saldo, 200);
    }
    public function mes($mes)
    {
        $nombre_mes = 'enero';
        switch ($mes) {
            case '1':
                $nombre_mes = 'enero';
                break;

            case '2':
                $nombre_mes = 'febrero';
                break;
            case '3':
                $nombre_mes = 'marzo';
                break;
            case '4':
                $nombre_mes = 'abril';
                break;
            case '5':
                $nombre_mes = 'mayo';
                break;
            case '6':
                $nombre_mes = 'junio';
                break;
            case '7':
                $nombre_mes = 'julio';
                break;
            case '8':
                $nombre_mes = 'agosto';
                break;
            case '9':
                $nombre_mes = 'setiembre';
                break;
            case '10':
                $nombre_mes = 'octubre';
                break;
            case '11':
                $nombre_mes = 'noviembre';
                break;
            case '12':
                $nombre_mes = 'diciembre';
                break;
        }
        return $nombre_mes;
    }

    public function afectarPresupuestoInterno($sumaResta, $tipoDocumento, $id, $detalle)
    {
        $fechaHoy = date("Y-m-d");
        $mesLista = ['1' => 'enero', '2' => 'febrero', '3' => 'marzo', '4' => 'abril', '5' => 'mayo', '6' => 'junio', '7' => 'julio', '8' => 'agosto', '9' => 'setiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre'];
        $mes = intval(date('m', strtotime($fechaHoy)));
        $nombreMes = $mesLista[$mes];
        $nombreMesAux = $nombreMes . '_aux';
        $TipoHistorial = '';
        $operacion = '';
        $importe = 0;
        $historial = [];
        switch ($tipoDocumento) {
            case 'orden':
                foreach ($detalle as $item) {
                    if ($item->id_detalle_requerimiento > 0) {
                        $detalleRequerimiento = DetalleRequerimiento::find($item->id_detalle_requerimiento);
                        $requerimiento = Requerimiento::find($detalleRequerimiento->id_requerimiento);
                        if ($requerimiento->id_presupuesto_interno > 0) {
                            $presupuestoInternoDetalle = PresupuestoInternoDetalle::where([
                                ['id_presupuesto_interno', $requerimiento->id_presupuesto_interno],
                                ['estado', 1], ['id_presupuesto_interno_detalle', $detalleRequerimiento->partida]
                            ])->first();
                            if ($presupuestoInternoDetalle) {
                                if ($sumaResta == 'resta') {
                                    $importe = floatval($presupuestoInternoDetalle->$nombreMesAux) - (isset($item->importe_item_para_presupuesto)?floatval($item->importe_item_para_presupuesto):0);
                                    $presupuestoInternoDetalle->$nombreMesAux = $importe;
                                    $presupuestoInternoDetalle->save();
                                    $TipoHistorial = 'SALIDA';
                                    $operacion = 'R';
                                } elseif ($sumaResta == 'suma') {
                                    $importe = floatval($presupuestoInternoDetalle->$nombreMesAux) + (isset($item->importe_item_para_presupuesto)?floatval($item->importe_item_para_presupuesto):0);
                                    $presupuestoInternoDetalle->$nombreMesAux = $importe;
                                    $presupuestoInternoDetalle->save();
                                    $TipoHistorial = 'RETORNO';
                                    $operacion = 'S';
                                }
                                PresupuestoInterno::calcularColumnaAuxMensual($requerimiento->id_presupuesto_interno, 3, $detalleRequerimiento->partida, $nombreMes);
                                $historialPresupuestoInternoSaldo = new HistorialPresupuestoInternoSaldo();
                                $historialPresupuestoInternoSaldo->id_presupuesto_interno = $requerimiento->id_presupuesto_interno;
                                $historialPresupuestoInternoSaldo->id_partida = $detalleRequerimiento->partida;
                                $historialPresupuestoInternoSaldo->tipo = $TipoHistorial;
                                $historialPresupuestoInternoSaldo->importe = $item->importe_item_para_presupuesto??0;
                                $historialPresupuestoInternoSaldo->mes = $nombreMes;
                                $historialPresupuestoInternoSaldo->id_requerimiento = $requerimiento->id_requerimiento;
                                $historialPresupuestoInternoSaldo->id_requerimiento_detalle = $detalleRequerimiento->id_detalle_requerimiento;
                                $historialPresupuestoInternoSaldo->id_orden = $id;
                                $historialPresupuestoInternoSaldo->id_orden_detalle = $item->id_detalle_orden;
                                $historialPresupuestoInternoSaldo->operacion = $operacion;
                                $historialPresupuestoInternoSaldo->estado = 1;
                                $historialPresupuestoInternoSaldo->fecha_registro = new Carbon();
                                $historial = $historialPresupuestoInternoSaldo;
                                $historialPresupuestoInternoSaldo->save();
                            }
                        }
                    }
                }

                if ($operacion == 'R' || $operacion == 'S') {
                    $ordenAfectaPresupuestoInterno = Orden::find($id);
                    $ordenAfectaPresupuestoInterno->afectado_presupuesto_interno = true;
                    $ordenAfectaPresupuestoInterno->save();
                }
                break;

            case 'requerimiento de pago':
                
                $requerimientoPago=RequerimientoPago::find($id);
                if($requerimientoPago->id_presupuesto_interno >0){
                    foreach ($detalle as $item) {
                        if ($item->id_partida > 0) {

                            $presupuestoInternoDetalle = PresupuestoInternoDetalle::where([
                                ['id_presupuesto_interno', $requerimientoPago->id_presupuesto_interno],
                                ['estado', 1], ['id_presupuesto_interno_detalle', $item->id_partida]
                            ])->first();

                            if ($presupuestoInternoDetalle) {
                                if ($sumaResta == 'resta') {
                                    $importe = floatval($presupuestoInternoDetalle->$nombreMesAux) -  (isset($item->importe_item_para_presupuesto) && ($item->importe_item_para_presupuesto>0)?floatval($item->importe_item_para_presupuesto):0) ;
                                    $presupuestoInternoDetalle->$nombreMesAux = $importe;
                                    $presupuestoInternoDetalle->save();
                                    $TipoHistorial = 'SALIDA';
                                    $operacion = 'R';
                                } elseif ($sumaResta == 'suma') {
                                    $importe = floatval($presupuestoInternoDetalle->$nombreMesAux) +  (isset($item->importe_item_para_presupuesto) && ($item->importe_item_para_presupuesto>0)?floatval($item->importe_item_para_presupuesto):0);
                                    $presupuestoInternoDetalle->$nombreMesAux = $importe;
                                    $presupuestoInternoDetalle->save();
                                    $TipoHistorial = 'RETORNO';
                                    $operacion = 'S';
                                }
                                PresupuestoInterno::calcularColumnaAuxMensual($requerimientoPago->id_presupuesto_interno, 3, $item->id_partida, $nombreMes);
                                $historialPresupuestoInternoSaldo = new HistorialPresupuestoInternoSaldo();
                                $historialPresupuestoInternoSaldo->id_presupuesto_interno = $requerimientoPago->id_presupuesto_interno;
                                $historialPresupuestoInternoSaldo->id_partida = $item->id_partida;
                                $historialPresupuestoInternoSaldo->tipo = $TipoHistorial;
                                $historialPresupuestoInternoSaldo->importe = $item->importe_item_para_presupuesto??0;
                                $historialPresupuestoInternoSaldo->mes = $nombreMes;
                                $historialPresupuestoInternoSaldo->id_requerimiento = $requerimientoPago->id_requerimiento_pago;
                                $historialPresupuestoInternoSaldo->id_requerimiento_detalle = $item->id_requerimiento_pago_detalle;
                                $historialPresupuestoInternoSaldo->operacion = $operacion;
                                $historialPresupuestoInternoSaldo->estado = 1;
                                $historialPresupuestoInternoSaldo->fecha_registro = new Carbon();
                                $historial = $historialPresupuestoInternoSaldo;
                                $historialPresupuestoInternoSaldo->save();
                            }
                        }
                    }
                }
                break;

            default:

                break;
        }

        return $historial;
    }
}
