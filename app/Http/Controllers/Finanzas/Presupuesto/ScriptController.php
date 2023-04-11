<?php

namespace App\Http\Controllers\Finanzas\Presupuesto;

use App\Helpers\StringHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Administracion\Division;
use App\Models\Finanzas\HistorialPresupuestoInternoSaldo;
use App\Models\Finanzas\PresupuestoInterno;
use App\Models\Finanzas\PresupuestoInternoModelo;
use App\Models\Finanzas\PresupuestoInternoDetalle;
use App\Models\Finanzas\PresupuestoInternoDetalleHistorial;

class ScriptController extends Controller
{
    //
    public function generarPresupuestoGastos()
    {
        ini_set('max_execution_time', 5000);
        $presupuestpInterno = PresupuestoInternoModelo::where('id_tipo_presupuesto',3)->get();
        $division = array(
            array(
                "division"=>1,
                "empresa"=>1,
                "sede"=>1,
            ),
            array(
                "division"=>2,
                "empresa"=>1,
                "sede"=>1,
            ),
            array(
                "division"=>5,
                "empresa"=>1,
                "sede"=>1,
            ),
            array(
                "division"=>10,
                "empresa"=>1,
                "sede"=>1,
            ),
            array(
                "division"=>11,
                "empresa"=>1,
                "sede"=>1,
            ),
            array(
                "division"=>9,
                "empresa"=>1,
                "sede"=>1,
            ),
            array(
                "division"=>12,
                "empresa"=>1,
                "sede"=>1,
            ),
            array(
                "division"=>13,
                "empresa"=>1,
                "sede"=>1,
            ),
            array(
                "division"=>14,
                "empresa"=>1,
                "sede"=>1,
            ),
            array(
                "division"=>15,
                "empresa"=>1,
                "sede"=>1,
            ),
            array(
                "division"=>16,
                "empresa"=>1,
                "sede"=>1,
            ),
            array(
                "division"=>20,
                "empresa"=>1,
                "sede"=>1,
            ),
            array(
                "division"=>21,
                "empresa"=>1,
                "sede"=>1,
            ),
            array(
                "division"=>22,
                "empresa"=>1,
                "sede"=>1,
            ),

            array(
                "division"=>15,
                "empresa"=>1,
                "sede"=>4,
            ),
            array(
                "division"=>11,
                "empresa"=>1,
                "sede"=>4,
            ),
            array(
                "division"=>1,
                "empresa"=>1,
                "sede"=>4,
            ),
        ); //area
        foreach ($division as $key => $value) {

            $admDivision = Division::find($value['division']);
            // $presupuesto_interno_count = PresupuestoInterno::count();
            // $presupuesto_interno_count = $presupuesto_interno_count +1;
            // $codigo = StringHelper::leftZero(2,$presupuesto_interno_count);

            $presupuesto_interno = new PresupuestoInterno();
            $presupuesto_interno->codigo                = $admDivision->codigo;
            $presupuesto_interno->descripcion           = $admDivision->codigo;
            $presupuesto_interno->id_grupo              = $admDivision->grupo_id;
            $presupuesto_interno->id_area               = $admDivision->id_division;
            $presupuesto_interno->fecha_registro        = date('Y-m-d H:i:s');
            $presupuesto_interno->estado                = 1;
            $presupuesto_interno->id_moneda             = 1;
            $presupuesto_interno->gastos                = 3;
            $presupuesto_interno->ingresos              = 0;//1 si es que se usa
            $presupuesto_interno->empresa_id            = $value['empresa'];
            $presupuesto_interno->sede_id            = $value['sede'];
            $presupuesto_interno->save();

            foreach ($presupuestpInterno as $key_partidas => $value_partidas) {
                $areglo_partida = explode('.',$value_partidas->partida);
                $value_partidas->registro = (sizeof($areglo_partida)===4?2:1);

                $gastos = new PresupuestoInternoDetalle();
                $gastos->partida                  = $value_partidas->partida;
                $gastos->descripcion              = $value_partidas->descripcion;
                $gastos->id_padre                 = $value_partidas->id_padre;
                $gastos->id_hijo                  = $value_partidas->$value_partidas;

                $gastos->id_tipo_presupuesto      = 3;
                $gastos->id_presupuesto_interno   = $presupuesto_interno->id_presupuesto_interno;
                $gastos->id_grupo                 = $admDivision->grupo_id;
                $gastos->id_area                  = $admDivision->id_division;
                $gastos->fecha_registro           = date('Y-m-d H:i:s');
                $gastos->estado                   = 1;
                $gastos->registro                 = $value_partidas->registro;

                $gastos->enero                    = 	0.00;
                $gastos->febrero                  = 	0.00;
                $gastos->marzo                    = 	0.00;
                $gastos->abril                    = 	0.00;
                $gastos->mayo                     = 	0.00;
                $gastos->junio                    = 	0.00;
                $gastos->julio                    = 	0.00;
                $gastos->agosto                   = 	0.00;
                $gastos->setiembre                = 	0.00;
                $gastos->octubre                  = 	0.00;
                $gastos->noviembre                = 	0.00;
                $gastos->diciembre                = 	0.00;
                $gastos->porcentaje_gobierno      = 	0.00;
                $gastos->porcentaje_privado       = 	0.00;
                $gastos->porcentaje_comicion      = 	0.00;
                $gastos->porcentaje_penalidad     = 	0.00;
                $gastos->porcentaje_costo         = 	0.00;
                $gastos->enero_aux               = 	0.00;
                $gastos->febrero_aux             = 	0.00;
                $gastos->marzo_aux               = 	0.00;
                $gastos->abril_aux               = 	0.00;
                $gastos->mayo_aux                = 	0.00;
                $gastos->junio_aux               = 	0.00;
                $gastos->julio_aux               = 	0.00;
                $gastos->agosto_aux              = 	0.00;
                $gastos->setiembre_aux           = 	0.00;
                $gastos->octubre_aux             = 	0.00;
                $gastos->noviembre_aux           = 	0.00;
                $gastos->diciembre_aux           = 	0.00;
                $gastos->save();

                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", 0.00));
                    $historial->mes = 1;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", 0.00));
                    $historial->mes = 2;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", 0.00));
                    $historial->mes = 3;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", 0.00));
                    $historial->mes = 4;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", 0.00));
                    $historial->mes = 5;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", 0.00));
                    $historial->mes = 6;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", 0.00));
                    $historial->mes = 7;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", 0.00));
                    $historial->mes = 8;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", 0.00));
                    $historial->mes = 9;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", 0.00));
                    $historial->mes = 10;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", 0.00));
                    $historial->mes = 11;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", 0.00));
                    $historial->mes = 12;
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 1;
                $historial->save();


                // historial de ingresos

                $gastosHisorial = new PresupuestoInternoDetalleHistorial()  ;
                $gastosHisorial->partida                  = $value_partidas->partida;
                $gastosHisorial->descripcion              = $value_partidas->descripcion;
                $gastosHisorial->id_padre                 = $value_partidas->id_padre;
                $gastosHisorial->id_hijo                  = $value_partidas->$value_partidas;

                $gastosHisorial->id_tipo_presupuesto      = 3;
                $gastosHisorial->id_presupuesto_interno   = $presupuesto_interno->id_presupuesto_interno;
                $gastosHisorial->id_grupo                 = $admDivision->grupo_id;
                $gastosHisorial->id_area                  = $admDivision->id_division;
                $gastosHisorial->fecha_registro           = date('Y-m-d H:i:s');
                $gastosHisorial->estado                   = 1;
                $gastosHisorial->registro                 = $value_partidas->registro;

                $gastosHisorial->enero                    = 	0.00;
                $gastosHisorial->febrero                  = 	0.00;
                $gastosHisorial->marzo                    = 	0.00;
                $gastosHisorial->abril                    = 	0.00;
                $gastosHisorial->mayo                     = 	0.00;
                $gastosHisorial->junio                    = 	0.00;
                $gastosHisorial->julio                    = 	0.00;
                $gastosHisorial->agosto                   = 	0.00;
                $gastosHisorial->setiembre                = 	0.00;
                $gastosHisorial->octubre                  = 	0.00;
                $gastosHisorial->noviembre                = 	0.00;
                $gastosHisorial->diciembre                = 	0.00;
                $gastosHisorial->porcentaje_gobierno      = 	0.00;
                $gastosHisorial->porcentaje_privado       = 	0.00;
                $gastosHisorial->porcentaje_comicion      = 	0.00;
                $gastosHisorial->porcentaje_penalidad     = 	0.00;
                $gastosHisorial->porcentaje_costo         = 	0.00;
                $gastosHisorial->enero_aux               = 	0.00;
                $gastosHisorial->febrero_aux             = 	0.00;
                $gastosHisorial->marzo_aux               = 	0.00;
                $gastosHisorial->abril_aux               = 	0.00;
                $gastosHisorial->mayo_aux                = 	0.00;
                $gastosHisorial->junio_aux               = 	0.00;
                $gastosHisorial->julio_aux               = 	0.00;
                $gastosHisorial->agosto_aux              = 	0.00;
                $gastosHisorial->setiembre_aux           = 	0.00;
                $gastosHisorial->octubre_aux             = 	0.00;
                $gastosHisorial->noviembre_aux           = 	0.00;
                $gastosHisorial->diciembre_aux           = 	0.00;
                $gastosHisorial->save();
                // ---------------------------------------------------------
            }

        }

        return response()->json(["success"=>$presupuestpInterno],200);
    }
}
