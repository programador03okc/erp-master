<?php

namespace App\Http\Controllers\Finanzas\Presupuesto;

use App\Helpers\ConfiguracionHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\StringHelper;

use App\Models\Administracion\Division;
use App\Models\administracion\DivisionCodigo;
use App\Models\Administracion\Sede;
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
            // array(
            //     "division"=>1,
            //     "empresa"=>1,
            //     "sede"=>4,
            // ),
        ); //area
        // array(
        //     "division"=>1,
        //     "empresa"=>1,
        //     "sede"=>1,
        // ),
        foreach ($division as $key => $value) {

            $admDivision = Division::find($value['division']);
            $sede = Sede::find($value['sede']);
            $division_codigo = DivisionCodigo::where('sede_id',$value['sede'])->where('division_id',$value['division'])->first();

            $presupuesto_interno = new PresupuestoInterno();
            $presupuesto_interno->codigo                = $division_codigo->codigo;
            $presupuesto_interno->descripcion           = $division_codigo->descripcion.' '.date('Y');
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
                $gastos->id_hijo                  = $value_partidas->id_modelo_presupuesto_interno;

                $gastos->id_tipo_presupuesto      = 3;
                $gastos->id_presupuesto_interno   = $presupuesto_interno->id_presupuesto_interno;
                $gastos->id_grupo                 = $admDivision->grupo_id;
                $gastos->id_area                  = $admDivision->id_division;
                $gastos->fecha_registro           = date('Y-m-d H:i:s');
                $gastos->estado                   = 1;
                $gastos->registro                 = $value_partidas->registro;

                $gastos->enero                    = 	'0.00';
                $gastos->febrero                  = 	'0.00';
                $gastos->marzo                    = 	'0.00';
                $gastos->abril                    = 	'0.00';
                $gastos->mayo                     = 	'0.00';
                $gastos->junio                    = 	'0.00';
                $gastos->julio                    = 	'0.00';
                $gastos->agosto                   = 	'0.00';
                $gastos->setiembre                = 	'0.00';
                $gastos->octubre                  = 	'0.00';
                $gastos->noviembre                = 	'0.00';
                $gastos->diciembre                = 	'0.00';
                $gastos->porcentaje_gobierno      = 	0.00;
                $gastos->porcentaje_privado       = 	0.00;
                $gastos->porcentaje_comicion      = 	0.00;
                $gastos->porcentaje_penalidad     = 	0.00;
                $gastos->porcentaje_costo         = 	0.00;
                $gastos->enero_aux               = 	'0.00';
                $gastos->febrero_aux             = 	'0.00';
                $gastos->marzo_aux               = 	'0.00';
                $gastos->abril_aux               = 	'0.00';
                $gastos->mayo_aux                = 	'0.00';
                $gastos->junio_aux               = 	'0.00';
                $gastos->julio_aux               = 	'0.00';
                $gastos->agosto_aux              = 	'0.00';
                $gastos->setiembre_aux           = 	'0.00';
                $gastos->octubre_aux             = 	'0.00';
                $gastos->noviembre_aux           = 	'0.00';
                $gastos->diciembre_aux           = 	'0.00';
                $gastos->save();

                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", '0.00'));
                    $historial->mes = '01';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", '0.00'));
                    $historial->mes = '02';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", '0.00'));
                    $historial->mes = '03';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", '0.00'));
                    $historial->mes = '04';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", '0.00'));
                    $historial->mes = '05';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", '0.00'));
                    $historial->mes = '06';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", '0.00'));
                    $historial->mes = '07';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", '0.00'));
                    $historial->mes = '08';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", '0.00'));
                    $historial->mes = '09';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", '0.00'));
                    $historial->mes = '10';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", '0.00'));
                    $historial->mes = '11';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'INGRESO';
                    $historial->importe = floatval(str_replace(",", "", '0.00'));
                    $historial->mes = '12';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
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

                $gastosHisorial->enero                    = 	'0.00';
                $gastosHisorial->febrero                  = 	'0.00';
                $gastosHisorial->marzo                    = 	'0.00';
                $gastosHisorial->abril                    = 	'0.00';
                $gastosHisorial->mayo                     = 	'0.00';
                $gastosHisorial->junio                    = 	'0.00';
                $gastosHisorial->julio                    = 	'0.00';
                $gastosHisorial->agosto                   = 	'0.00';
                $gastosHisorial->setiembre                = 	'0.00';
                $gastosHisorial->octubre                  = 	'0.00';
                $gastosHisorial->noviembre                = 	'0.00';
                $gastosHisorial->diciembre                = 	'0.00';
                $gastosHisorial->porcentaje_gobierno      = 	0.00;
                $gastosHisorial->porcentaje_privado       = 	0.00;
                $gastosHisorial->porcentaje_comicion      = 	0.00;
                $gastosHisorial->porcentaje_penalidad     = 	0.00;
                $gastosHisorial->porcentaje_costo         = 	0.00;
                $gastosHisorial->enero_aux               = 	'0.00';
                $gastosHisorial->febrero_aux             = 	'0.00';
                $gastosHisorial->marzo_aux               = 	'0.00';
                $gastosHisorial->abril_aux               = 	'0.00';
                $gastosHisorial->mayo_aux                = 	'0.00';
                $gastosHisorial->junio_aux               = 	'0.00';
                $gastosHisorial->julio_aux               = 	'0.00';
                $gastosHisorial->agosto_aux              = 	'0.00';
                $gastosHisorial->setiembre_aux           = 	'0.00';
                $gastosHisorial->octubre_aux             = 	'0.00';
                $gastosHisorial->noviembre_aux           = 	'0.00';
                $gastosHisorial->diciembre_aux           = 	'0.00';
                $gastosHisorial->save();
                // ---------------------------------------------------------
            }

        }

        return response()->json(["success"=>$presupuestpInterno],200);
    }
    public function homologarPartida()
    {
        $homologacionPartidas = [
            "03-GASTOS",
            "03.01-REMUNERACIONES",
            "03.01.01-PERSONAL DE OFICINA",
            "03.01.01.01-SUELDOS",
            "03.01.01.02-SUELDOS NUEVAS CONTRATACIONES",
            "03.01.01.03-INCREMENTOS SALARIALES",
            "03.01.01.04-BONIFICACIONES",
            "03.01.01.05-COMISIONES DE VENTAS",
            "03.01.01.06-ASIGNACIÓN FAMILIAR",
            "03.01.02-APORTES PATRONALES",
            "03.01.02.01-ESSALUD",
            "03.01.02.02-SCTR",
            "03.01.02.03-ESSALUD VIDA",
            "03.01.03-PROVISIONES",
            "03.01.03.01-COMPENSACION POR TIEMPO DE SERVICIOS",
            "03.01.03.02-GRATIFICACIONES",
            "03.01.03.03-VACACIONES",
            "03.01.04-PERSONAL DE SERVICIOS NO PERSONALES",
            "03.01.04.01-RECIBOS POR HONORARIOS PERSONAL OPERATIVO",
            "03.02-BIENES Y SERVICIOS",
            "03.02.01-GASTOS DE OPERACIÓN Y FUNCIONAMIENTO",
            "03.02.01.01-UTILES DE OFICINA",
            "03.02.01.02-MATERIALES DE LIMPIEZA Y ASEO",
            "03.02.01.03-MATERIALES DE FERRETERIA",
            "03.02.01.04-MATERIALES DE MANTENIMIENTO DE BIENES",
            "03.02.01.05-COMBUSTIBLE",
            "03.02.01.06-SERVICIOS DE FOTOCOPIADO E IMPRESIÓN",
            "03.02.01.07-SERVICIO DE MANTENIMIENTO DE BIENES",
            "03.02.01.08-SERVICIO DE LUZ",
            "03.02.01.09-SERVICIO DE AGUA",
            "03.02.01.10-SERVICIO DE TELEFONIA MOVIL, FIJA, CABLE E INTERNET",
            "03.02.01.11-MOVILIDAD LOCAL",
            "03.02.01.12-CAJA CHICA",
            "03.02.01.13-SERVICIO DE ENVIO Y/O ENCOMIENDAS",
            "03.02.01.14-SERVICIO DE VIGILANCIA",
            "03.02.01.15-SERVICIO DE LIMPIEZA",
            "03.02.01.16-SERVICIO DE ALIMENTACIÓN O CONSUMO",
            "03.02.01.17-OTROS SERVICIOS DE OPERACIÓN Y FUNCIONAMIENTO",
            "03.02.01.18-SEGUROS",
            "03.02.01.19-LINEA DEDICADA",
            "03.02.01.20-GASTOS DE REPRESENTACION",
            "03.02.01.21-SERVICIO CENTRAL DE RIESGO",
            "03.02.01.22-OTROS SERVICIOS (RECIBOS POR HONORARIOS)",
            "03.02.02-ALQUILER DE BIENES E INMUEBLES",
            "03.02.02.01-ALQUILER DE OFICINAS",
            "03.02.02.02-ALQUILER DE ALMACENES",
            "03.02.02.03-ALQUILER DE VEHICULOS",
            "03.02.02.04-OTROS ALQUILER DE BIENES E INMUEBLES",
            "03.02.03-PUBLICIDAD, DIFUSION, EVENTOS",
            "03.02.03.01-PUBLICIDAD POR RADIO, TELEVISION Y PERIODICOS",
            "03.02.03.02-IMPRESIÓN DE TRÍPTICOS, VOLANTES, GIGANTOGRAFIAS Y OTROS",
            "03.02.03.03-SOUVENIR",
            "03.02.03.04-EVENTOS",
            "03.02.03.05-OTROS GASTOS DE PUBLICIDAD, DIFUSION, EVENTOS",
            "03.02.04-VIATICOS",
            "03.02.04.01-PASAJES",
            "03.02.04.02-ALIMENTACION",
            "03.02.04.03-ALOJAMIENTO",
            "03.02.04.04-MOVILIDAD LOCAL",
            "03.02.04.05-COMBUSTIBLE",
            "03.02.04.06-ALQUILER DE MOVILIDAD",
            "03.02.04.07-PEAJES",
            "03.02.05-SERVICIOS ESPECIALIZADOS",
            "03.02.05.01-SERVICIOS NOTARIALES",
            "03.02.05.02-SERVICIOS REGISTRALES (VIGENCIA DE PODER, OTROS)",
            "03.02.05.03-SERVICIOS DE ASESORIA LEGAL",
            "03.02.05.04-SERVICIOS DE ASESORIA CONTABLE Y AUDITORIA",
            "03.02.05.05-SERVICIOS DE ASESORIA EN MARKETING",
            "03.02.05.06-SERVICIOS DE COBRANZA",
            "03.02.05.07-SERVICIOS DE REPORTE FINANCIERO",
            "03.02.05.08-SERVICIOS DE CAPACITACIÓN",
            "03.02.05.09-OTROS SERVICIOS ESPECIALIZADOS",
            "03.03-INVERSIONES",
            "03.03.01-ADQUISICION DE INMUEBLES",
            "03.03.01.01-OFICINA",
            "03.03.01.02-ALMACEN",
            "03.03.01.03-OTROS ADQUISICION DE INMUEBLES",
            "03.03.02-CONSTRUCCION DE INMUEBLES",
            "03.03.02.01-OFICINA",
            "03.03.02.02-ALMACEN",
            "03.03.02.03-OTROS CONSTRUCCION DE INMUEBLES",
            "03.03.03-REMODELACION DE INMUEBLES",
            "03.03.03.01-OFICINA",
            "03.03.03.02-ALMACEN",
            "03.03.03.03-OTROS REMODELACION DE INMUEBLES",
            "03.03.04-VEHICULOS Y EQUIPAMIENTO",
            "03.03.04.01-VEHICULOS",
            "03.03.04.02-HERRAMIENTAS",
            "03.03.04.03-MAQUINARIAS",
            "03.03.04.04-EQUIPOS INFORMATICOS",
            "03.03.04.05-OTROS EQUIPOS",
            "03.03.05-MOBILIARIO",
            "03.03.05.01-OFICINA",
            "03.03.05.02-ALMACEN",
            "03.03.05.03-OTROS",
            "03.03.06-OTROS GASTOS DE INVERSION",
            "03.03.06.01-CAPACITACION ESPECIALIZADA",
            "03.03.06.02-GASTOS DE INVESTIGACION Y DESARROLLO",
            "03.03.06.03-SOFTWARE",
            "03.04-FINANCIAMIENTO",
            "03.04.01-GASTOS DE FINANCIAMIENTO",
            "03.04.01.01-COMISIONES Y PORTES DE CUENTAS",
            "03.04.01.02-INTERESES DE PRESTAMOS",
            "03.04.01.03-CUOTA DE PRESTAMO",
            "03.04.01.04-INTERESES LEASING",
            "03.04.01.05-CUOTA DE LEASING",
        ];


        // return PresupuestoInternoModelo::where('partida',"03.02.01.01")->where('id_tipo_presupuesto',3)->first();exit;
        $array_modificados=array();
        foreach($homologacionPartidas as $key=>$item){
            $array_descripcion = explode('-',$item);
            $modelo_partidas = PresupuestoInternoModelo::where('partida',$array_descripcion[0])->where('id_tipo_presupuesto',3)->first();

            // return $array_descripcion[1];exit;
            // if ("03.02.01.01"==$array_descripcion[0]) {
                // return $modelo_partidas;exit;
            // }
            if (!$modelo_partidas) {
                return 'ssdsd';exit;
            }

            if($modelo_partidas){
                $modelo_partidas->descripcion = $array_descripcion[1];
                $modelo_partidas->save();
                array_push($array_modificados,array("partida"=>$modelo_partidas->partida,"descripcion"=>$modelo_partidas->descripcion));
            }

            // return response()->json(["success"=>$array_modificados],200);
        }
        return response()->json(["success"=>$array_modificados],200);
    }
    public function totalPresupuesto($presup,$tipo)
    {
        // $total = PresupuestoInterno::calcularTotalPresupuestoFilas($presup, $tipo);
        // return $presup;
        // exit;

        $ingresos = 0;
        $costos = 0;
        // $costos = $gastos = PresupuestoInterno::calcularTotalPresupuestoAnual($presup,2);
        $gastos = PresupuestoInterno::calcularTotalPresupuestoAnual($presup,3);
        $total = $ingresos - $costos - $gastos;
        return response()->json(["ingresos"=>$ingresos,"costos"=>$costos,"gastos"=>$gastos,"total"=>$total],200);
    }
    public function totalConsumidoMes($presup,$tipo,$mes)
    {
        $total = PresupuestoInterno::calcularTotalConsumidoMesFilas($presup, $tipo,$mes);
        return $total;
    }
    public function totalEjecutado()
    {
        // $valor= PresupuestoInterno::calcularColumnaAuxMensual(31,3,);
        // return ;exit;
        $presupuesto_interno_aprobado = PresupuestoInterno::where('estado',2)->get();
        $array_total=array();
        foreach ($presupuesto_interno_aprobado as $key => $value) {
            $total_ejecutado = PresupuestoInterno::presupuestoEjecutado($value->id_presupuesto_interno,3);
            $total_ppti = PresupuestoInterno::calcularTotalPresupuestoAnual($value->id_presupuesto_interno,3);
            array_push($array_total,array(
                "id"=>$value->id_presupuesto_interno,
                "codigo"=>$value->codigo,
                "total_ppti"=>round($total_ppti, 2),
                "total_ejecutado"=>round($total_ejecutado, 2),
            ));
        }

        return response()->json($array_total,200);
    }

    public function montosRegular()
    {
        $meses_numero='04';
        $id_presupuesto_interno=31;
        $excluir = array(); //almacenare los id que tengan como monto mayor 0 y no se pueda restar para mantenerlos intacto

        $total_presupuesto_interno = $this->primerosMeses($id_presupuesto_interno,intval($meses_numero));
        $array_total = $total_presupuesto_interno;



        $saldo = HistorialPresupuestoInternoSaldo::where('id_presupuesto_interno',$id_presupuesto_interno)
        // ->whereNotNull('id_requerimiento')
        ->orderBy('id','ASC')
        ->get();

        // $ejecucion = $this->presupuestoEjecutadoMonto($meses_numero,$id_presupuesto_interno);
        $ejecucion = PresupuestoInterno::totalEjecutatoMonto($meses_numero,$id_presupuesto_interno);
        // return $ejecucion;exit;


        foreach ($saldo as $key => $value) {
            $detalle_presupuesto_interno = PresupuestoInternoDetalle::find($value->id_partida);
            $index = array_search($detalle_presupuesto_interno->partida, array_column($total_presupuesto_interno, 'partida'));

            return [$detalle_presupuesto_interno->float_enero,$detalle_presupuesto_interno->enero];exit;
            if ($total_presupuesto_interno[$index]['total'] > $value->importe) {

                if ($value->operacion==='R') {
                    $total_presupuesto_interno[$index]['total'] = floatval($total_presupuesto_interno[$index]['total']) - floatval($value->importe);
                }
                if ($value->operacion==='S'){
                    $total_presupuesto_interno[$index]['total'] = floatval($total_presupuesto_interno[$index]['total']) + floatval($value->importe);
                }

            }else{
                array_push($excluir,$value->id_partida);
            }


        }

        foreach ($total_presupuesto_interno as $key => $value) {
            $detalle = PresupuestoInternoDetalle::find($value['id']);
            $detalle->enero_aux = 0;
            $detalle->febrero_aux = 0;
            $detalle->marzo_aux = 0;
            $detalle->abril_aux = $value['total'];
            $detalle->save();
        }
        return [
            "id_partidas"=>$excluir,
            // "index"=>$total_presupuesto_interno[$index],
            "historial_saldo"=>$saldo,
            // "ppti"=>$detalle_presupuesto_interno,
            "saldos"=>$total_presupuesto_interno,
            "suma_meses"=>$array_total
        ];exit;

        return response()->json($saldo,200);
    }
    public function primerosMeses($id_presupuesto_interno, $meses_numero)
    {
        $total_presupuesto_interno=array();
        $excluir = array();
        for ($i=1; $i <= $meses_numero ; $i++) {
            if ($i===1) {
                $total_presupuesto_interno = PresupuestoInterno::obtenerPresupuestoFilasMesRegistro($id_presupuesto_interno,3,$i);
            }else
            {
                $presupuesto_interno_mensual = PresupuestoInterno::obtenerPresupuestoFilasMesRegistro($id_presupuesto_interno,3,$i);

                foreach ($presupuesto_interno_mensual as $key => $value) {
                    $index = array_search($value['partida'], array_column($total_presupuesto_interno, 'partida'));

                    $total_presupuesto_interno[$index]['total'] = round(($total_presupuesto_interno[$index]['total'] + $value['total']), 2);
                }
            }
        }


        return $total_presupuesto_interno;
    }
    public function totalPresupuestoAnualPartidasNiveles($presupuesto_interno_id, $tipo, $nivel, $tipoCampo)
    {
        $array = PresupuestoInterno::totalPartidasAnualFilasNivel(
            $presupuesto_interno_id,
            $tipo,
            $nivel,
            $tipoCampo
        );
        return response()->json($array,200);
    }


}
