<?php

namespace App\Http\Controllers\Finanzas\Presupuesto;

use App\Exports\PresupuestoInternoEjecutadoExport;
use App\Exports\PresupuestoInternoExport;
use App\Helpers\ConfiguracionHelper;
use App\Helpers\StringHelper;
use App\Helpers\Finanzas\PresupuestoInternoHistorialHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\administracion\AdmGrupo;
use App\Models\Administracion\Division;
use App\Models\administracion\DivisionCodigo;
use App\Models\Administracion\Empresa;
use App\Models\Administracion\Sede;
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
use App\Models\Tesoreria\RequerimientoPago;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

use App\models\Configuracion\AccesosUsuarios;
use App\models\Configuracion\UsuarioGrupo;
use App\Models\Finanzas\PresupuestoInternoDetalleHistorial;
use App\Models\Logistica\OrdenCompraDetalle;
use App\Models\Tesoreria\RequerimientoPagoDetalle;
use Illuminate\Support\Facades\Auth;
use Debugbar;

class PresupuestoInternoController extends Controller
{
    //
    public function lista()
    {
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }

        return view('finanzas.presupuesto_interno.lista', compact('array_accesos'));
    }
    public function listaPresupuestoInterno()
    {
        $array_grupos_id=[];
        $grupos = UsuarioGrupo::where('id_usuario',Auth::user()->id_usuario)->where('estado',1)->get();
        foreach ($grupos as $key => $value) {
            array_push($array_grupos_id,$value->id_grupo);
        }
        // return $grupos;exit;
        $data = PresupuestoInterno::where('presupuesto_interno.estado','!=',7)
        ->whereIn('presupuesto_interno.id_grupo', $array_grupos_id)
        ->select('presupuesto_interno.*', 'adm_grupo.descripcion as grupo', 'presupuesto_interno_estado.descripcion as estadopi','sis_sede.descripcion as sede')
        ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'presupuesto_interno.id_grupo')
        ->join('finanzas.presupuesto_interno_estado', 'presupuesto_interno_estado.id', '=', 'presupuesto_interno.estado')
        ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'presupuesto_interno.sede_id')
        ->get()
            ;
        return DataTables::of($data)
        ->addColumn('total', function ($data){
            $total = ($data->gastos=='3'?PresupuestoInterno::calcularTotalPresupuestoAnual($data->id_presupuesto_interno,3):0);
            // return floatval(str_replace(",", "", $total));
            return round($total,2);
        })
        ->addColumn('total_ejecutado', function ($data){
            $total_ejecutado = 0;
            if ($data->estado==2) {
                // $total_ejecutado = PresupuestoInterno::presupuestoEjecutado($data->id_presupuesto_interno,3);

                $meses_numero = date('m');
                $total_ejecutado =  PresupuestoInterno::totalEjecutatoMonto($meses_numero,$data->id_presupuesto_interno);
            }
            return round($total_ejecutado, 2);
        })
        // ->toJson();
        ->make(true);
    }
    public function crear()
    {
        $empresas = Empresa::all();
        $grupos = AdmGrupo::get();
        $area = FinanzasArea::where('estado',1)->get();
        $moneda = Moneda::where('estado',1)->get();

        $presupuesto_interno = PresupuestoInterno::count();
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
        return view('finanzas.presupuesto_interno.crear', compact('grupos','area','moneda','array_accesos','empresas'));
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
            // return $request->costos;exit;
            $presupuesto_interno_count = PresupuestoInterno::count();
            $presupuesto_interno_count = $presupuesto_interno_count +1;
            $codigo = StringHelper::leftZero(2,$presupuesto_interno_count);

            $division_codigo = DivisionCodigo::where('sede_id',$request->sede_id)->where('division_id',$request->id_area)->first();

            $codigo = ($division_codigo?$division_codigo->codigo:$codigo);
            $descripcion = $request->descripcion;

            $presupuesto_interno                        = new PresupuestoInterno();
            $presupuesto_interno->codigo                = $codigo;
            $presupuesto_interno->descripcion           = $descripcion;
            $presupuesto_interno->id_grupo              = $request->id_grupo;
            $presupuesto_interno->id_area               = $request->id_area;
            $presupuesto_interno->fecha_registro        = date('Y-m-d H:i:s');
            $presupuesto_interno->estado                = 1;
            $presupuesto_interno->id_moneda             = $request->id_moneda;
            $presupuesto_interno->gastos                = $request->tipo_gastos;
            $presupuesto_interno->ingresos              = $request->tipo_ingresos;
            $presupuesto_interno->empresa_id              = $request->empresa_id;
            $presupuesto_interno->sede_id              = $request->sede_id;
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
                    $ingresos->febrero_aux= $value['febrero'];
                    $ingresos->marzo_aux= $value['marzo'];
                    $ingresos->abril_aux= $value['abril'];
                    $ingresos->mayo_aux= $value['mayo'];
                    $ingresos->junio_aux= $value['junio'];
                    $ingresos->julio_aux= $value['julio'];
                    $ingresos->agosto_aux= $value['agosto'];
                    $ingresos->setiembre_aux= $value['setiembre'];
                    $ingresos->octubre_aux= $value['octubre'];
                    $ingresos->noviembre_aux= $value['noviembre'];
                    $ingresos->diciembre_aux= $value['diciembre'];

                    $ingresos->save();

                    // historial de ingresos

                    $ingresosHisorial = new PresupuestoInternoDetalleHistorial()  ;
                    $ingresosHisorial->partida                  = $value['partida'];
                    $ingresosHisorial->descripcion              = $value['descripcion'];
                    $ingresosHisorial->id_padre                 = $value['id_padre'];
                    $ingresosHisorial->id_hijo                  = $value['id_hijo'];
                    $ingresosHisorial->id_tipo_presupuesto      = 1;
                    $ingresosHisorial->id_presupuesto_interno   = $presupuesto_interno->id_presupuesto_interno;
                    $ingresosHisorial->id_grupo                 = $request->id_grupo;
                    $ingresosHisorial->id_area                  = $request->id_area;
                    $ingresosHisorial->fecha_registro           = date('Y-m-d H:i:s');
                    $ingresosHisorial->estado                   = 1;
                    $ingresosHisorial->registro                 = $value['registro'];
                    $ingresosHisorial->enero                    = $value['enero'];
                    $ingresosHisorial->febrero                  = $value['febrero'];
                    $ingresosHisorial->marzo                    = $value['marzo'];
                    $ingresosHisorial->abril                    = $value['abril'];
                    $ingresosHisorial->mayo                     = $value['mayo'];
                    $ingresosHisorial->junio                    = $value['junio'];
                    $ingresosHisorial->julio                    = $value['julio'];
                    $ingresosHisorial->agosto                   = $value['agosto'];
                    $ingresosHisorial->setiembre                = $value['setiembre'];
                    $ingresosHisorial->octubre                  = $value['octubre'];
                    $ingresosHisorial->noviembre                = $value['noviembre'];
                    $ingresosHisorial->diciembre                = $value['diciembre'];
                    $ingresosHisorial->porcentaje_gobierno      = $value['porcentaje_gobierno'];
                    $ingresosHisorial->porcentaje_privado       = $value['porcentaje_privado'];
                    $ingresosHisorial->porcentaje_comicion      = $value['porcentaje_comicion'];
                    $ingresosHisorial->porcentaje_penalidad     = $value['porcentaje_penalidad'];
                    $ingresosHisorial->porcentaje_costo         = $value['porcentaje_costo'];
                    $ingresosHisorial->enero_aux                = $value['enero'];
                    $ingresosHisorial->febrero_aux              = $value['febrero'];
                    $ingresosHisorial->marzo_aux                = $value['marzo'];
                    $ingresosHisorial->abril_aux                = $value['abril'];
                    $ingresosHisorial->mayo_aux                 = $value['mayo'];
                    $ingresosHisorial->junio_aux                = $value['junio'];
                    $ingresosHisorial->julio_aux                = $value['julio'];
                    $ingresosHisorial->agosto_aux               = $value['agosto'];
                    $ingresosHisorial->setiembre_aux            = $value['setiembre'];
                    $ingresosHisorial->octubre_aux              = $value['octubre'];
                    $ingresosHisorial->noviembre_aux            = $value['noviembre'];
                    $ingresosHisorial->diciembre_aux            = $value['diciembre'];
                    $ingresosHisorial->save();
                    // ---------------------------------------------------------

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
                    $costos->febrero_aux= $value['febrero'];
                    $costos->marzo_aux= $value['marzo'];
                    $costos->abril_aux= $value['abril'];
                    $costos->mayo_aux= $value['mayo'];
                    $costos->junio_aux= $value['junio'];
                    $costos->julio_aux= $value['julio'];
                    $costos->agosto_aux= $value['agosto'];
                    $costos->setiembre_aux= $value['setiembre'];
                    $costos->octubre_aux= $value['octubre'];
                    $costos->noviembre_aux= $value['noviembre'];
                    $costos->diciembre_aux= $value['diciembre'];

                    $costos->save();

                    // historial de ingresos

                    $costosHisorial = new PresupuestoInternoDetalleHistorial()  ;
                    $costosHisorial->partida                    = $value['partida'];
                    $costosHisorial->descripcion                = $value['descripcion'];
                    $costosHisorial->id_padre                   = $value['id_padre'];
                    $costosHisorial->id_hijo                    = $value['id_hijo'];
                    $costosHisorial->id_tipo_presupuesto        = 2;
                    $costosHisorial->id_presupuesto_interno     = $presupuesto_interno->id_presupuesto_interno;
                    $costosHisorial->id_grupo                   = $request->id_grupo;
                    $costosHisorial->id_area                    = $request->id_area;
                    $costosHisorial->fecha_registro             = date('Y-m-d H:i:s');
                    $costosHisorial->registro                   = $value['registro'];
                    $costosHisorial->estado                     = 1;
                    $costosHisorial->enero                      = $value['enero'];
                    $costosHisorial->febrero                    = $value['febrero'];
                    $costosHisorial->marzo                      = $value['marzo'];
                    $costosHisorial->abril                      = $value['abril'];
                    $costosHisorial->mayo                       = $value['mayo'];
                    $costosHisorial->junio                      = $value['junio'];
                    $costosHisorial->julio                      = $value['julio'];
                    $costosHisorial->agosto                     = $value['agosto'];
                    $costosHisorial->setiembre                  = $value['setiembre'];
                    $costosHisorial->octubre                    = $value['octubre'];
                    $costosHisorial->noviembre                  = $value['noviembre'];
                    $costosHisorial->diciembre                  = $value['diciembre'];
                    $costosHisorial->porcentaje_gobierno        = $value['porcentaje_gobierno'];
                    $costosHisorial->porcentaje_privado         = $value['porcentaje_privado'];
                    $costosHisorial->porcentaje_comicion        = $value['porcentaje_comicion'];
                    $costosHisorial->porcentaje_penalidad       = $value['porcentaje_penalidad'];
                    $costosHisorial->porcentaje_costo           = $value['porcentaje_costo'];
                    $costosHisorial->enero_aux                  = $value['enero'];
                    $costosHisorial->febrero_aux                = $value['febrero'];
                    $costosHisorial->marzo_aux                  = $value['marzo'];
                    $costosHisorial->abril_aux                  = $value['abril'];
                    $costosHisorial->mayo_aux                   = $value['mayo'];
                    $costosHisorial->junio_aux                  = $value['junio'];
                    $costosHisorial->julio_aux                  = $value['julio'];
                    $costosHisorial->agosto_aux                 = $value['agosto'];
                    $costosHisorial->setiembre_aux              = $value['setiembre'];
                    $costosHisorial->octubre_aux                = $value['octubre'];
                    $costosHisorial->noviembre_aux              = $value['noviembre'];
                    $costosHisorial->diciembre_aux              = $value['diciembre'];
                    $costosHisorial->save();
                    // ---------------------------------------------------------


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
                        $gastos->febrero_aux= $value['febrero'];
                        $gastos->marzo_aux= $value['marzo'];
                        $gastos->abril_aux= $value['abril'];
                        $gastos->mayo_aux= $value['mayo'];
                        $gastos->junio_aux= $value['junio'];
                        $gastos->julio_aux= $value['julio'];
                        $gastos->agosto_aux= $value['agosto'];
                        $gastos->setiembre_aux= $value['setiembre'];
                        $gastos->octubre_aux= $value['octubre'];
                        $gastos->noviembre_aux= $value['noviembre'];
                        $gastos->diciembre_aux= $value['diciembre'];
                    $gastos->save();

                    $historial = new HistorialPresupuestoInternoSaldo();
                        $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                        $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                        $historial->tipo = 'INGRESO';
                        $historial->importe = floatval(str_replace(",", "", $value['enero']));
                        $historial->mes = '01';
                        $historial->fecha_registro = date('Y-m-d H:i:s');
                        $historial->operacion = 'S';
                        $historial->estado = 3;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                        $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                        $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                        $historial->tipo = 'INGRESO';
                        $historial->importe = floatval(str_replace(",", "", $value['febrero']));
                        $historial->mes = '02';
                        $historial->fecha_registro = date('Y-m-d H:i:s');
                        $historial->operacion = 'S';
                        $historial->estado = 3;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                        $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                        $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                        $historial->tipo = 'INGRESO';
                        $historial->importe = floatval(str_replace(",", "", $value['marzo']));
                        $historial->mes = '03';
                        $historial->fecha_registro = date('Y-m-d H:i:s');
                        $historial->operacion = 'S';
                        $historial->estado = 3;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                        $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                        $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                        $historial->tipo = 'INGRESO';
                        $historial->importe = floatval(str_replace(",", "", $value['abril']));
                        $historial->mes = '04';
                        $historial->fecha_registro = date('Y-m-d H:i:s');
                        $historial->operacion = 'S';
                        $historial->estado = 3;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                        $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                        $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                        $historial->tipo = 'INGRESO';
                        $historial->importe = floatval(str_replace(",", "", $value['mayo']));
                        $historial->mes = '05';
                        $historial->fecha_registro = date('Y-m-d H:i:s');
                        $historial->operacion = 'S';
                        $historial->estado = 3;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                        $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                        $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                        $historial->tipo = 'INGRESO';
                        $historial->importe = floatval(str_replace(",", "", $value['junio']));
                        $historial->mes = '06';
                        $historial->fecha_registro = date('Y-m-d H:i:s');
                        $historial->operacion = 'S';
                        $historial->estado = 3;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                        $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                        $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                        $historial->tipo = 'INGRESO';
                        $historial->importe = floatval(str_replace(",", "", $value['julio']));
                        $historial->mes = '07';
                        $historial->fecha_registro = date('Y-m-d H:i:s');
                        $historial->operacion = 'S';
                        $historial->estado = 3;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                        $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                        $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                        $historial->tipo = 'INGRESO';
                        $historial->importe = floatval(str_replace(",", "", $value['agosto']));
                        $historial->mes = '08';
                        $historial->fecha_registro = date('Y-m-d H:i:s');
                        $historial->operacion = 'S';
                        $historial->estado = 3;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                        $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                        $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                        $historial->tipo = 'INGRESO';
                        $historial->importe = floatval(str_replace(",", "", $value['setiembre']));
                        $historial->mes = '09';
                        $historial->fecha_registro = date('Y-m-d H:i:s');
                        $historial->operacion = 'S';
                        $historial->estado = 3;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                        $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                        $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                        $historial->tipo = 'INGRESO';
                        $historial->importe = floatval(str_replace(",", "", $value['octubre']));
                        $historial->mes = '10';
                        $historial->fecha_registro = date('Y-m-d H:i:s');
                        $historial->operacion = 'S';
                        $historial->estado = 3;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                        $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                        $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                        $historial->tipo = 'INGRESO';
                        $historial->importe = floatval(str_replace(",", "", $value['noviembre']));
                        $historial->mes = '11';
                        $historial->fecha_registro = date('Y-m-d H:i:s');
                        $historial->operacion = 'S';
                        $historial->estado = 3;
                    $historial->save();
                    $historial = new HistorialPresupuestoInternoSaldo();
                        $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                        $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                        $historial->tipo = 'INGRESO';
                        $historial->importe = floatval(str_replace(",", "", $value['diciembre']));
                        $historial->mes = '12';
                        $historial->fecha_registro = date('Y-m-d H:i:s');
                        $historial->operacion = 'S';
                        $historial->estado = 3;
                    $historial->save();


                    // historial de ingresos

                    $gastosHisorial = new PresupuestoInternoDetalleHistorial()  ;
                    $gastosHisorial->partida                  = $value['partida'];
                    $gastosHisorial->descripcion              = $value['descripcion'];
                    $gastosHisorial->id_padre                 = $value['id_padre'];
                    $gastosHisorial->id_hijo                  = $value['id_hijo'];
                    $gastosHisorial->id_tipo_presupuesto      = 3;
                    $gastosHisorial->id_presupuesto_interno   = $presupuesto_interno->id_presupuesto_interno;
                    $gastosHisorial->id_grupo                 = $request->id_grupo;
                    $gastosHisorial->id_area                  = $request->id_area;
                    $gastosHisorial->fecha_registro           = date('Y-m-d H:i:s');
                    $gastosHisorial->estado                   = 1;
                    $gastosHisorial->registro                 = $value['registro'];
                    $gastosHisorial->enero                    = $value['enero'];
                    $gastosHisorial->febrero                  = $value['febrero'];
                    $gastosHisorial->marzo                    = $value['marzo'];
                    $gastosHisorial->abril                    = $value['abril'];
                    $gastosHisorial->mayo                     = $value['mayo'];
                    $gastosHisorial->junio                    = $value['junio'];
                    $gastosHisorial->julio                    = $value['julio'];
                    $gastosHisorial->agosto                   = $value['agosto'];
                    $gastosHisorial->setiembre                = $value['setiembre'];
                    $gastosHisorial->octubre                  = $value['octubre'];
                    $gastosHisorial->noviembre                = $value['noviembre'];
                    $gastosHisorial->diciembre                = $value['diciembre'];
                    $gastosHisorial->porcentaje_gobierno      = $value['porcentaje_gobierno'];
                    $gastosHisorial->porcentaje_privado       = $value['porcentaje_privado'];
                    $gastosHisorial->porcentaje_comicion      = $value['porcentaje_comicion'];
                    $gastosHisorial->porcentaje_penalidad     = $value['porcentaje_penalidad'];
                    $gastosHisorial->porcentaje_costo         = $value['porcentaje_costo'];
                    $gastosHisorial->enero_aux                  = $value['enero'];
                    $gastosHisorial->febrero_aux                = $value['febrero'];
                    $gastosHisorial->marzo_aux                  = $value['marzo'];
                    $gastosHisorial->abril_aux                  = $value['abril'];
                    $gastosHisorial->mayo_aux                   = $value['mayo'];
                    $gastosHisorial->junio_aux                  = $value['junio'];
                    $gastosHisorial->julio_aux                  = $value['julio'];
                    $gastosHisorial->agosto_aux                 = $value['agosto'];
                    $gastosHisorial->setiembre_aux              = $value['setiembre'];
                    $gastosHisorial->octubre_aux                = $value['octubre'];
                    $gastosHisorial->noviembre_aux              = $value['noviembre'];
                    $gastosHisorial->diciembre_aux              = $value['diciembre'];
                    $gastosHisorial->save();
                    // ---------------------------------------------------------
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
        $empresas = Empresa::all();
        $grupos = Grupo::get();
        // $area = Area::where('estado',1)->get();
        $area = Division::where('estado',1)->get();
        $moneda = Moneda::where('estado',1)->get();



        $id = $request->id;
        $presupuesto_interno = PresupuestoInterno::where('id_presupuesto_interno',$id)->first();
        $ingresos= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id)->where('id_tipo_presupuesto',1)->where('estado', 1)->orderBy('partida')->get();
        $costos= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id)->where('id_tipo_presupuesto',2)->where('estado', 1)->orderBy('partida')->get();
        $gastos = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id)->where('id_tipo_presupuesto',3)->where('estado', 1)->orderBy('partida')->get();

        // return PresupuestoInterno::calcularTotalPresupuestoFilas($id,2);exit;
        // return PresupuestoInterno::calcularTotalMensualColumnas($id,2,'02.01.01.01','enero');exit;

        // return PresupuestoInterno::calcularTotalMensualColumnasPorcentajes($id,1,'01.01.01.01','enero');exit;

        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }

        $sedes = Sede::listarSedesPorEmpresa($presupuesto_interno->empresa_id);
        // return $sedes;exit;

        return view('finanzas.presupuesto_interno.editar', compact('grupos','area','moneda','id','presupuesto_interno','ingresos','costos','gastos','array_accesos','empresas', 'sedes'));
    }
    public function editarPresupuestoAprobado(Request $request)
    {
        $empresas = Empresa::all();
        $grupos = Grupo::get();
        // $area = Area::where('estado',1)->get();
        $area = Division::where('estado',1)->get();
        $moneda = Moneda::where('estado',1)->get();


        $id = $request->id;
        $presupuesto_interno = PresupuestoInterno::where('id_presupuesto_interno',$id)->first();
        $ingresos= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id)->where('id_tipo_presupuesto',1)->where('estado', 1)->orderBy('partida')->get();
        $costos= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id)->where('id_tipo_presupuesto',2)->where('estado', 1)->orderBy('partida')->get();
        $gastos = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id)->where('id_tipo_presupuesto',3)->where('estado', 1)->orderBy('partida')->get();

        // return PresupuestoInterno::calcularTotalPresupuestoFilas($id,2);exit;
        // return PresupuestoInterno::calcularTotalMensualColumnas($id,2,'02.01.01.01','enero');exit;

        // return PresupuestoInterno::calcularTotalMensualColumnasPorcentajes($id,1,'01.01.01.01','enero');exit;

        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
        // return 'ss';exit;
        $sedes = Sede::listarSedesPorEmpresa($presupuesto_interno->empresa_id);
        return view('finanzas.presupuesto_interno.editar_presupuesto_aprobado', compact('grupos','area','moneda','id','presupuesto_interno','ingresos','costos','gastos','array_accesos','empresas','sedes'));
    }
    public function actualizar(Request $request)
    {
        // set_time_limit(0);
        ini_set('max_input_vars', 800000);

        $array_descripcion = explode('-',$request->descripcion);

        // return $request->descripcion ;exit;
        $division_codigo = DivisionCodigo::where('sede_id',$request->sede_id)->where('division_id',$request->id_area)->first();
        $descripcion = $request->descripcion;

        // return $descripcion ;exit;

        //se actualiza la cabecera del presupuesto
        $presupuesto_interno                        = PresupuestoInterno::find($request->id_presupuesto_interno);

        $codigo = ($division_codigo?$division_codigo->codigo:$presupuesto_interno->codigo);

        $presupuesto_interno->codigo                = $codigo;
        $presupuesto_interno->descripcion           = $descripcion;
        $presupuesto_interno->id_grupo              = $request->id_grupo;
        $presupuesto_interno->id_area               = $request->id_area;

        $presupuesto_interno->id_moneda             = $request->id_moneda;
        $presupuesto_interno->gastos                = $request->tipo_gastos;
        $presupuesto_interno->ingresos              = $request->tipo_ingresos;

        $presupuesto_interno->empresa_id            = $request->empresa_id;
        $presupuesto_interno->sede_id               = $request->sede_id;
        $presupuesto_interno->save();

        if ($request->tipo_ingresos==='1') {

            PresupuestoInternoDetalleHistorial::where('id_presupuesto_interno', $presupuesto_interno->id_presupuesto_interno)->where('id_tipo_presupuesto',1)->delete();

            foreach ($request->ingresos as $key => $value) {
                $auxiliar = PresupuestoInternoDetalle::find($value['id_presupuesto_interno_detalle']);

                // ------------------------------------------------------
                $ingresos = PresupuestoInternoDetalle::find($value['id_presupuesto_interno_detalle']);
                $ingresos->partida                  = $value['partida'];
                $ingresos->descripcion              = $value['descripcion'];
                $ingresos->id_padre                 = $value['id_padre'];
                $ingresos->id_hijo                  = $value['id_hijo'];
                $ingresos->id_tipo_presupuesto      = 1;
                $ingresos->id_presupuesto_interno   = $presupuesto_interno->id_presupuesto_interno;
                $ingresos->id_grupo                 = $request->id_grupo;
                $ingresos->id_area                  = $request->id_area;
                $ingresos->fecha_registro           = date('Y-m-d H:i:s');
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

                // Se guardara un historial del presupuesto si se llega actualizar------------------

                $ingresosHistorial = new PresupuestoInternoDetalleHistorial();
                $ingresosHistorial->partida                  = $value['partida'];
                $ingresosHistorial->descripcion              = $value['descripcion'];
                $ingresosHistorial->id_padre                 = $value['id_padre'];
                $ingresosHistorial->id_hijo                  = $value['id_hijo'];
                $ingresosHistorial->id_tipo_presupuesto      = 1;
                $ingresosHistorial->id_presupuesto_interno   = $presupuesto_interno->id_presupuesto_interno;
                $ingresosHistorial->id_grupo                 = $request->id_grupo;
                $ingresosHistorial->id_area                  = $request->id_area;
                $ingresosHistorial->fecha_registro           = date('Y-m-d H:i:s');
                $ingresosHistorial->registro                 = $value['registro'];
                $ingresosHistorial->enero                    = $value['enero'];
                $ingresosHistorial->febrero                  = $value['febrero'];
                $ingresosHistorial->marzo                    = $value['marzo'];
                $ingresosHistorial->abril                    = $value['abril'];
                $ingresosHistorial->mayo                     = $value['mayo'];
                $ingresosHistorial->junio                    = $value['junio'];
                $ingresosHistorial->julio                    = $value['julio'];
                $ingresosHistorial->agosto                   = $value['agosto'];
                $ingresosHistorial->setiembre                = $value['setiembre'];
                $ingresosHistorial->octubre                  = $value['octubre'];
                $ingresosHistorial->noviembre                = $value['noviembre'];
                $ingresosHistorial->diciembre                = $value['diciembre'];
                $ingresosHistorial->porcentaje_gobierno      = $value['porcentaje_gobierno'];
                $ingresosHistorial->porcentaje_privado       = $value['porcentaje_privado'];
                $ingresosHistorial->porcentaje_comicion      = $value['porcentaje_comicion'];
                $ingresosHistorial->porcentaje_penalidad     = $value['porcentaje_penalidad'];
                $ingresosHistorial->porcentaje_costo         = $value['porcentaje_costo'];
                $ingresosHistorial->created_at    = date('Y-m-d H:i:s');
                $ingresosHistorial->updated_at  = date('Y-m-d H:i:s');
                $ingresosHistorial->save();
                // -----------------------------------------------------

            }

            PresupuestoInternoDetalleHistorial::where('id_presupuesto_interno', $presupuesto_interno->id_presupuesto_interno)->where('id_tipo_presupuesto',2)->delete();
            foreach ($request->costos as $key => $value) {
                $auxiliar = PresupuestoInternoDetalle::find($value['id_presupuesto_interno_detalle']);
                $costos = PresupuestoInternoDetalle::find($value['id_presupuesto_interno_detalle']);
                $costos->partida                  = $value['partida'];
                $costos->descripcion              = $value['descripcion'];
                $costos->id_padre                 = $value['id_padre'];
                $costos->id_hijo                  = $value['id_hijo'];
                $costos->id_tipo_presupuesto      = 2;
                $costos->id_presupuesto_interno   = $presupuesto_interno->id_presupuesto_interno;
                $costos->id_grupo                 = $request->id_grupo;
                $costos->id_area                  = $request->id_area;
                $costos->fecha_registro           = date('Y-m-d H:i:s');
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


                // Se guardara un historial del presupuesto si se llega actualizar------------------


                $costosHistorial = new PresupuestoInternoDetalleHistorial();
                $costosHistorial->partida                  = $value['partida'];
                $costosHistorial->descripcion              = $value['descripcion'];
                $costosHistorial->id_padre                 = $value['id_padre'];
                $costosHistorial->id_hijo                  = $value['id_hijo'];
                $costosHistorial->id_tipo_presupuesto      = 2;
                $costosHistorial->id_presupuesto_interno   = $presupuesto_interno->id_presupuesto_interno;
                $costosHistorial->id_grupo                 = $request->id_grupo;
                $costosHistorial->id_area                  = $request->id_area;
                $costosHistorial->fecha_registro           = date('Y-m-d H:i:s');
                $costosHistorial->registro                 = $value['registro'];
                $costosHistorial->enero                    = $value['enero'];
                $costosHistorial->febrero                  = $value['febrero'];
                $costosHistorial->marzo                    = $value['marzo'];
                $costosHistorial->abril                    = $value['abril'];
                $costosHistorial->mayo                     = $value['mayo'];
                $costosHistorial->junio                    = $value['junio'];
                $costosHistorial->julio                    = $value['julio'];
                $costosHistorial->agosto                   = $value['agosto'];
                $costosHistorial->setiembre                = $value['setiembre'];
                $costosHistorial->octubre                  = $value['octubre'];
                $costosHistorial->noviembre                = $value['noviembre'];
                $costosHistorial->diciembre                = $value['diciembre'];
                $costosHistorial->porcentaje_gobierno      = $value['porcentaje_gobierno'];
                $costosHistorial->porcentaje_privado       = $value['porcentaje_privado'];
                $costosHistorial->porcentaje_comicion      = $value['porcentaje_comicion'];
                $costosHistorial->porcentaje_penalidad     = $value['porcentaje_penalidad'];
                $costosHistorial->porcentaje_costo         = $value['porcentaje_costo'];
                $costosHistorial->created_at    = date('Y-m-d H:i:s');
                $costosHistorial->updated_at  = date('Y-m-d H:i:s');
                $costosHistorial->save();
                // -----------------------------------------------------


            }
        }
        if ($request->tipo_gastos==='3') {
            PresupuestoInternoDetalleHistorial::where('id_presupuesto_interno', $presupuesto_interno->id_presupuesto_interno)->where('id_tipo_presupuesto',3)->delete();
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

                $gasto_enero       = ($gasto_enero>=0? floatval(str_replace(",", "", $value['enero'])) - $gasto_enero: floatval(str_replace(",", "", $value['enero'])) + ($gasto_enero*-1));
                $gasto_febrero     = ($gasto_febrero>=0? floatval(str_replace(",", "", $value['febrero'])) - $gasto_febrero: floatval(str_replace(",", "", $value['febrero'])) + ($gasto_febrero*-1));
                $gasto_marzo       = ($gasto_marzo>=0? floatval(str_replace(",", "", $value['marzo'])) - $gasto_marzo: floatval(str_replace(",", "", $value['marzo'])) + ($gasto_marzo*-1));
                $gasto_abril       = ($gasto_abril>=0? floatval(str_replace(",", "", $value['abril'])) - $gasto_abril: floatval(str_replace(",", "", $value['abril'])) + ($gasto_abril*-1));
                $gasto_mayo        = ($gasto_mayo>=0? floatval(str_replace(",", "", $value['mayo'])) - $gasto_mayo: floatval(str_replace(",", "", $value['mayo'])) + ($gasto_mayo*-1));
                $gasto_junio       = ($gasto_junio>=0? floatval(str_replace(",", "", $value['junio'])) - $gasto_junio: floatval(str_replace(",", "", $value['junio'])) + ($gasto_junio*-1));
                $gasto_julio       = ($gasto_julio>=0? floatval(str_replace(",", "", $value['julio'])) - $gasto_julio: floatval(str_replace(",", "", $value['julio'])) + ($gasto_julio*-1));
                $gasto_agosto      = ($gasto_agosto>=0? floatval(str_replace(",", "", $value['agosto'])) - $gasto_agosto: floatval(str_replace(",", "", $value['agosto'])) + ($gasto_agosto*-1));
                $gasto_setiembre   = ($gasto_setiembre>=0? floatval(str_replace(",", "", $value['setiembre'])) - $gasto_setiembre: floatval(str_replace(",", "", $value['setiembre'])) + ($gasto_setiembre*-1));
                $gasto_octubre     = ($gasto_octubre>=0? floatval(str_replace(",", "", $value['octubre'])) - $gasto_octubre: floatval(str_replace(",", "", $value['octubre'])) + ($gasto_octubre*-1));
                $gasto_noviembre   = ($gasto_noviembre>=0? floatval(str_replace(",", "", $value['noviembre'])) - $gasto_noviembre: floatval(str_replace(",", "", $value['noviembre'])) + ($gasto_noviembre*-1));
                $gasto_diciembre   = ($gasto_diciembre>=0? floatval(str_replace(",", "", $value['diciembre'])) - $gasto_diciembre: floatval(str_replace(",", "", $value['diciembre'])) + ($gasto_diciembre*-1));

                // ----------------------------------------------------
                // obtener el nuevo saldo --------------------------------
                $nuevo_saldo_enere       =  $gasto_enero;
                $nuevo_saldo_febrero     =  $gasto_febrero;
                $nuevo_saldo_marzo       =  $gasto_marzo;
                $nuevo_saldo_abril       =  $gasto_abril;
                $nuevo_saldo_mayo        =  $gasto_mayo;
                $nuevo_saldo_junio       =  $gasto_junio;
                $nuevo_saldo_julio       =  $gasto_julio;
                $nuevo_saldo_agosto      =  $gasto_agosto;
                $nuevo_saldo_setiembre   =  $gasto_setiembre;
                $nuevo_saldo_octubre     =  $gasto_octubre;
                $nuevo_saldo_noviembre   =  $gasto_noviembre;
                $nuevo_saldo_diciembre   =  $gasto_diciembre;
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
                    $historial->importe                 = ($diferencia_enere[0]>=0 ? $diferencia_enere[0] : ($diferencia_enere[0]*-1));
                    $historial->mes                     = '01';
                    $historial->fecha_registro          = date('Y-m-d H:i:s');
                    $historial->estado                  = 3;
                    $historial->operacion               = $diferencia_enere[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'MODIFICACION';
                    $historial->importe = ($diferencia_febrero[0]>=0 ? $diferencia_febrero[0] : ($diferencia_febrero[0]*-1));
                    $historial->mes = '02';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                    $historial->operacion               = $diferencia_febrero[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'MODIFICACION';
                    $historial->importe = ($diferencia_marzo[0]>=0 ? $diferencia_marzo[0] : ($diferencia_marzo[0]*-1));
                    $historial->mes = '03';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                    $historial->operacion               = $diferencia_marzo[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'MODIFICACION';
                    $historial->importe = ($diferencia_abril[0]>=0 ? $diferencia_abril[0] : ($diferencia_abril[0]*-1));
                    $historial->mes = '04';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                    $historial->operacion               = $diferencia_abril[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'MODIFICACION';
                    $historial->importe = ($diferencia_mayo[0]>=0 ? $diferencia_mayo[0] : ($diferencia_mayo[0]*-1));
                    $historial->mes = '05';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                    $historial->operacion               = $diferencia_mayo[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'MODIFICACION';
                    $historial->importe = ($diferencia_junio[0]>=0 ? $diferencia_junio[0] : ($diferencia_junio[0]*-1));
                    $historial->mes = '06';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                    $historial->operacion               = $diferencia_junio[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'MODIFICACION';
                    $historial->importe = ($diferencia_julio[0]>=0 ? $diferencia_julio[0] : ($diferencia_julio[0]*-1));
                    $historial->mes = '07';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                    $historial->operacion               = $diferencia_julio[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'MODIFICACION';
                    $historial->importe = ($diferencia_agosto[0]>=0 ? $diferencia_agosto[0] : ($diferencia_agosto[0]*-1));
                    $historial->mes = '08';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                    $historial->operacion               = $diferencia_agosto[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'MODIFICACION';
                    $historial->importe = ($diferencia_setiembre[0]>=0 ? $diferencia_setiembre[0] : ($diferencia_setiembre[0]*-1));
                    $historial->mes = '09';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                    $historial->operacion               = $diferencia_setiembre[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'MODIFICACION';
                    $historial->importe = ($diferencia_octubre[0]>=0 ? $diferencia_octubre[0] : ($diferencia_octubre[0]*-1));
                    $historial->mes = '10';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                    $historial->operacion               = $diferencia_octubre[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'MODIFICACION';
                    $historial->importe = ($diferencia_noviembre[0]>=0 ? $diferencia_noviembre[0] : ($diferencia_noviembre[0]*-1));
                    $historial->mes = '11';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                    $historial->operacion               = $diferencia_noviembre[1];
                $historial->save();
                $historial = new HistorialPresupuestoInternoSaldo();
                    $historial->id_presupuesto_interno = $presupuesto_interno->id_presupuesto_interno;
                    $historial->id_partida = $gastos->id_presupuesto_interno_detalle;
                    $historial->tipo = 'MODIFICACION';
                    $historial->importe = ($diferencia_diciembre[0]>=0 ? $diferencia_diciembre[0] : ($diferencia_diciembre[0]*-1));
                    $historial->mes = '12';
                    $historial->fecha_registro = date('Y-m-d H:i:s');
                    $historial->estado = 3;
                    $historial->operacion               = $diferencia_diciembre[1];
                $historial->save();

                // return $diferencia_enere;exit;


                // Se guardara un historial del presupuesto si se llega actualizar------------------


                $gastosHistorial = new PresupuestoInternoDetalleHistorial();
                $gastosHistorial->partida                  = $value['partida'];
                $gastosHistorial->descripcion              = $value['descripcion'];
                $gastosHistorial->id_padre                 = $value['id_padre'];
                $gastosHistorial->id_hijo                  = $value['id_hijo'];
                $gastosHistorial->id_tipo_presupuesto      = 3;
                $gastosHistorial->id_presupuesto_interno   = $presupuesto_interno->id_presupuesto_interno;
                $gastosHistorial->id_grupo                 = $request->id_grupo;
                $gastosHistorial->id_area                  = $request->id_area;
                $gastosHistorial->fecha_registro           = date('Y-m-d H:i:s');
                $gastosHistorial->registro                 = $value['registro'];
                $gastosHistorial->enero                    = $value['enero'];
                $gastosHistorial->febrero                  = $value['febrero'];
                $gastosHistorial->marzo                    = $value['marzo'];
                $gastosHistorial->abril                    = $value['abril'];
                $gastosHistorial->mayo                     = $value['mayo'];
                $gastosHistorial->junio                    = $value['junio'];
                $gastosHistorial->julio                    = $value['julio'];
                $gastosHistorial->agosto                   = $value['agosto'];
                $gastosHistorial->setiembre                = $value['setiembre'];
                $gastosHistorial->octubre                  = $value['octubre'];
                $gastosHistorial->noviembre                = $value['noviembre'];
                $gastosHistorial->diciembre                = $value['diciembre'];
                $gastosHistorial->porcentaje_gobierno      = $value['porcentaje_gobierno'];
                $gastosHistorial->porcentaje_privado       = $value['porcentaje_privado'];
                $gastosHistorial->porcentaje_comicion      = $value['porcentaje_comicion'];
                $gastosHistorial->porcentaje_penalidad     = $value['porcentaje_penalidad'];
                $gastosHistorial->porcentaje_costo         = $value['porcentaje_costo'];
                $gastosHistorial->enero_aux      = $nuevo_saldo_enere;
                $gastosHistorial->febrero_aux    = $nuevo_saldo_febrero;
                $gastosHistorial->marzo_aux      = $nuevo_saldo_marzo;
                $gastosHistorial->abril_aux      = $nuevo_saldo_abril;
                $gastosHistorial->mayo_aux       = $nuevo_saldo_mayo;
                $gastosHistorial->junio_aux      = $nuevo_saldo_junio;
                $gastosHistorial->julio_aux      = $nuevo_saldo_julio;
                $gastosHistorial->agosto_aux     = $nuevo_saldo_agosto;
                $gastosHistorial->setiembre_aux  = $nuevo_saldo_setiembre;
                $gastosHistorial->octubre_aux    = $nuevo_saldo_octubre;
                $gastosHistorial->noviembre_aux  = $nuevo_saldo_noviembre;
                $gastosHistorial->diciembre_aux  = $nuevo_saldo_diciembre;

                $gastosHistorial->created_at    = date('Y-m-d H:i:s');
                $gastosHistorial->updated_at  = date('Y-m-d H:i:s');
                $gastosHistorial->save();
                // -----------------------------------------------------
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

        $data = PresupuestoInterno::select(
            'presupuesto_interno.*',
            'sis_grupo.descripcion as grupo',
            'division.descripcion as area',
            'sis_moneda.descripcion as moneda','sis_moneda.simbolo'
            )
        ->join('configuracion.sis_grupo', 'sis_grupo.id_grupo', '=', 'presupuesto_interno.id_grupo')
        ->join('administracion.division', 'division.id_division', '=', 'presupuesto_interno.id_area')
        ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'presupuesto_interno.id_moneda')
        ->where('presupuesto_interno.id_presupuesto_interno',$request->id)
        ->first();
        // return $data;exit;
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

    public function comboPresupuestoInterno($idGrupo,$idArea){
        $data = PresupuestoInterno::where([['presupuesto_interno.estado','!=',7],['presupuesto_interno.id_grupo','=',$idGrupo],['presupuesto_interno.id_area','=',$idArea]])
        ->select('presupuesto_interno.*', 'adm_grupo.descripcion as descripcion_grupo', 'division.descripcion as descripcion_area')
        ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'presupuesto_interno.id_grupo')
        ->join('administracion.division', 'division.id_division', '=', 'presupuesto_interno.id_area')->get();
        return $data;
    }


    public function obtenerDetallePresupuestoInterno($idPresupuestoIterno){


        $presupuestoInterno= PresupuestoInterno::with(['detalle'=>function($q) use($idPresupuestoIterno){
            $q->where([['id_presupuesto_interno',$idPresupuestoIterno],['estado','!=',7]])->orderBy('partida','asc');
        }])->where([['id_presupuesto_interno',$idPresupuestoIterno],['estado',2]])->get();

        // agregar campo totales e inicializar en 0
        foreach ($presupuestoInterno as $key => $Presup) {
            foreach ($Presup['detalle'] as $keyd => $detPresup) {
                $detPresup['total_presupuesto_año'] = 0;
                $detPresup['total_presupuesto_mes'] = 0;
                $detPresup['total_consumido_mes'] = 0;
                $detPresup['total_saldo_mes'] = 0;
                $detPresup['total_saldo_año'] = 0;

            }
        }

        $totalFilas = PresupuestoInterno::calcularTotalPresupuestoFilas($idPresupuestoIterno,3); // para requerimiento enviar 3= gastos
        $detalleRequerimiento = PresupuestoInterno::calcularConsumidoPresupuestoFilas($idPresupuestoIterno,3); // para requerimiento enviar 3= gastos

        $numero_mes = date("m");
        $nombre_mes = $this->mes($numero_mes);


        // llenar totales
        foreach ($presupuestoInterno as $key => $Presup) {
            foreach ($Presup['detalle'] as $keyd => $detPresup) {

            if($detPresup['registro'] ==1){


                $detPresup['total_presupuesto_año'] = $this->obtenerTotalPrespuestoAñoDelPadrePartida($idPresupuestoIterno,3,$presupuestoInterno,$detPresup['id_hijo']);
                $detPresup['total_presupuesto_mes'] = $this->obtenerTotalPrespuestoMesDelPadrePartida($presupuestoInterno,$detPresup['id_hijo']);
                $detPresup['total_consumido_mes'] =   $this->obtenerConsumidoPrespuestoMesDelPadrePartida($idPresupuestoIterno,3,$presupuestoInterno,$detPresup['id_hijo']);
                $detPresup['total_saldo_mes'] =   $this->obtenerSaldoPrespuestoMesDelPadrePartida($presupuestoInterno,$detPresup['id_hijo']);
                $detPresup['total_saldo_año'] =   $this->obtenerSaldoPrespuestoAñoDelPadrePartida($idPresupuestoIterno,3,$presupuestoInterno,$detPresup['id_hijo']);

            }

            if($detPresup['registro'] ==2){
                $detPresup['total_presupuesto_año'] = 0;
                $detPresup['total_presupuesto_mes'] = floatval(preg_replace("/[^-0-9\.]/","",$detPresup[$nombre_mes]));
                $detPresup['total_consumido_mes'] = floatval(preg_replace("/[^-0-9\.]/","",$detPresup[$nombre_mes])) - floatval(preg_replace("/[^-0-9\.]/","",$detPresup[$nombre_mes.'_aux']));
                $detPresup['total_saldo_mes'] = floatval(preg_replace("/[^-0-9\.]/","",$detPresup[$nombre_mes.'_aux']));
                $detPresup['total_saldo_año'] = 0;



            //  completar total presupuesto año;
            foreach ($totalFilas as $key => $totFila) {
                if($totFila['partida'] == $detPresup['partida'] ){
                    $detPresup['total_presupuesto_año'] = $totFila['total'];
                }
            }

           //  completar saldo anual;
            foreach ($totalFilas as $key => $totFila) {
                if($totFila['partida'] == $detPresup['partida'] ){
                    $detPresup['total_saldo_año'] =  floatval(preg_replace("/[^-0-9\.]/","",$detPresup['enero_aux']))
                    + floatval(preg_replace("/[^-0-9\.]/","",$detPresup['febrero_aux']))
                    + floatval(preg_replace("/[^-0-9\.]/","",$detPresup['marzo_aux']))
                    + floatval(preg_replace("/[^-0-9\.]/","",$detPresup['abril_aux']))
                    + floatval(preg_replace("/[^-0-9\.]/","",$detPresup['mayo_aux']))
                    + floatval(preg_replace("/[^-0-9\.]/","",$detPresup['junio_aux']))
                    + floatval(preg_replace("/[^-0-9\.]/","",$detPresup['julio_aux']))
                    + floatval(preg_replace("/[^-0-9\.]/","",$detPresup['agosto_aux']))
                    + floatval(preg_replace("/[^-0-9\.]/","",$detPresup['setiembre_aux']))
                    + floatval(preg_replace("/[^-0-9\.]/","",$detPresup['octubre_aux']))
                    + floatval(preg_replace("/[^-0-9\.]/","",$detPresup['noviembre_aux']))
                    + floatval(preg_replace("/[^-0-9\.]/","",$detPresup['diciembre_aux']));
                }
            }
        }


            }
        }

        return $presupuestoInterno;
    }

    public function obtenerTotalPrespuestoAñoDelPadrePartida($idPresupuestoIterno,$tipo,$presupuestoInterno, $idHijo){

        $totalPresupuestoFilaList = PresupuestoInterno::calcularTotalPresupuestoFilas($idPresupuestoIterno,$tipo);
        $totalPresupuestoAño=0;

        foreach ($presupuestoInterno as $keyPi => $Presup) {
            foreach ($Presup['detalle'] as $keyD => $detPresup) {
                if($detPresup['id_padre'] == $idHijo){

                    foreach ($totalPresupuestoFilaList as $keyTf => $fila) {
                        if($fila['partida'] == $detPresup['partida']){
                            $totalPresupuestoAño+= $fila['total'];
                        }
                    }

                }
            }
        }

        return $totalPresupuestoAño;
    }

    public function obtenerTotalPrespuestoMesDelPadrePartida($presupuestoInterno, $idHijo){
        $numero_mes = date("m");
        $nombre_mes = $this->mes($numero_mes);
        $totalPresupuestoMes=0;
        foreach ($presupuestoInterno as $key => $Presup) {
            foreach ($Presup['detalle'] as $keyd => $detPresup) {
                if($detPresup['id_padre'] == $idHijo){
                $totalPresupuestoMes  += floatval(preg_replace("/[^-0-9\.]/","",$detPresup[$nombre_mes]));
                }
            }
        }

        return $totalPresupuestoMes;
    }

    public function obtenerConsumidoPrespuestoMesDelPadrePartida($idPresupuestoIterno,$tipo,$presupuestoInterno, $idHijo){

        $numero_mes = date("m");
        $totalConsumidoMesFilaList = PresupuestoInterno::calcularTotalConsumidoMesFilas($idPresupuestoIterno,$tipo,$numero_mes);

        $totalConsumidoMes=0;
        foreach ($presupuestoInterno as $key => $Presup) {
            foreach ($Presup['detalle'] as $keyd => $detPresup) {
                if($detPresup['id_padre'] == $idHijo){
                    foreach ($totalConsumidoMesFilaList as $keyTf => $fila) {
                        if($fila['partida'] == $detPresup['partida']){
                            $totalConsumidoMes+= $fila['total'];
                        }
                    }
                }
            }
        }
        return $totalConsumidoMes;
    }

    public function obtenerSaldoPrespuestoMesDelPadrePartida($presupuestoInterno, $idHijo){
        $numero_mes = date("m");
        $nombre_mes = $this->mes($numero_mes);
        $totalSaldoMes=0;
        foreach ($presupuestoInterno as $key => $Presup) {
            foreach ($Presup['detalle'] as $keyd => $detPresup) {
                if($detPresup['id_padre'] == $idHijo){
                    $totalSaldoMes  +=  (floatval(preg_replace("/[^-0-9\.]/","",$detPresup[$nombre_mes.'_aux'])));
                }
            }
        }
        return ( $totalSaldoMes);
    }
    public function obtenerSaldoPrespuestoAñoDelPadrePartida($idPresupuestoIterno,$tipo,$presupuestoInterno, $idHijo){

        $totalPresupuestoFilaList = PresupuestoInterno::calcularTotalPresupuestoFilas($idPresupuestoIterno,$tipo,2);

        $totalSaldoAño=0;
        foreach ($presupuestoInterno as $key => $Presup) {
            foreach ($Presup['detalle'] as $keyd => $detPresup) {
                if($detPresup['id_padre'] == $idHijo){
                    foreach ($totalPresupuestoFilaList as $keyTf => $fila) {
                        if($fila['partida'] == $detPresup['partida']){
                            $totalSaldoAño+= $fila['total'];
                        }
                    }
                }
            }
        }
        return $totalSaldoAño;
    }




    public function editarMontoPartida(Request $request){
         // return PresupuestoInterno::calcularTotalPresupuestoFilas($id,2);exit;
        // return PresupuestoInterno::calcularTotalMensualColumnas($id,2,'02.01.01.01','enero');exit;
        // return $request->all();exit;
        $mes = $request->mes;

        $ingresos   = PresupuestoInternoDetalle::where('id_presupuesto_interno',$request->id)->where('id_tipo_presupuesto',1)->where('estado', 1)->orderBy('partida')->get();
        $costos     = PresupuestoInternoDetalle::where('id_presupuesto_interno',$request->id)->where('id_tipo_presupuesto',2)->where('estado', 1)->orderBy('partida')->get();
        $gastos     = PresupuestoInternoDetalle::where('id_presupuesto_interno',$request->id)->where('id_tipo_presupuesto',3)->where('estado', 1)->orderBy('partida')->get();
        $success=false;
        if (sizeof($ingresos)>0) {
            $presupuesto_interno_partida_modificar= PresupuestoInternoDetalle::where('id_presupuesto_interno',$request->id)->where('estado', 1)->where('partida', $request->partida)->where('id_tipo_presupuesto', 1)->where('registro', 2)->first();
            if ($presupuesto_interno_partida_modificar) {
                $presupuesto_interno_partida_modificar->$mes = number_format($request->monto, 2);
                $presupuesto_interno_partida_modificar->save();
                PresupuestoInterno::calcularTotalMensualColumnas($request->id,1,$request->partida,$request->mes);

                PresupuestoInterno::calcularTotalMensualColumnasPorcentajes($request->id,1,$request->partida,$request->mes);
                $partida_costos='02';
                foreach (explode('.',$request->partida) as $key => $value) {
                    if ($key!==0) {
                        $partida_costos = $partida_costos.'.'.$value;
                    }
                }
                PresupuestoInterno::calcularTotalMensualColumnas($request->id,2,$partida_costos,$request->mes);
                $success=true;
            }





        }
        if (sizeof($gastos)>0) {
            $presupuesto_interno_partida_modificar= PresupuestoInternoDetalle::where('id_presupuesto_interno',$request->id)->where('estado', 1)->where('partida', $request->partida)->where('id_tipo_presupuesto', 3)->where('registro', 2)->first();
            if ($presupuesto_interno_partida_modificar) {
                $presupuesto_interno_partida_modificar->$mes = number_format($request->monto, 2);
                $presupuesto_interno_partida_modificar->save();

                PresupuestoInterno::calcularTotalMensualColumnasPorcentajes($request->id,3,$request->partida,$request->mes);

                PresupuestoInterno::calcularTotalMensualColumnas($request->id,3,$request->partida,$request->mes);
                if (
                    $presupuesto_interno_partida_modificar->partida === '03.01.01.01' ||$presupuesto_interno_partida_modificar->partida === '03.01.01.02' ||$presupuesto_interno_partida_modificar->partida === '03.01.01.03'
                ) {
                    PresupuestoInterno::calcularTotalMensualColumnas($request->id,3,'03.01.02.01',$request->mes);
                    PresupuestoInterno::calcularTotalMensualColumnas($request->id,3,'03.01.03.01',$request->mes);
                }
                $success=true;
            }


        }


        return response()->json($success,200);
    }
    public function buscarPartidaCombo(Request $request){
        $presupuesto_interno_detalle=[];
        if (!empty($request->searchTerm)) {
            $searchTerm=$request->searchTerm;
            $presupuesto_interno_detalle = PresupuestoInternoDetalle::where('estado',1);
            if (!empty($request->searchTerm)) {
                $presupuesto_interno_detalle = $presupuesto_interno_detalle->where('partida','like','%'.$searchTerm.'%')
                ->where('id_presupuesto_interno',$request->id_presupuesto_interno)
                ->where('registro','2')
                ->whereNotIn('partida', ['03.01.02.01', '03.01.02.02', '03.01.02.03','03.01.03.01','03.01.03.02','03.01.03.03']);
            }
            $presupuesto_interno_detalle = $presupuesto_interno_detalle->get();
            return response()->json($presupuesto_interno_detalle);
        }else{
            return response()->json([
                "status"=>404,
                "success"=>false
            ]);
        }
    }
    public function diferencia($monto_1, $monto_2)
    {
        $diferencia = floatval(str_replace(",", "", $monto_1)) - floatval(str_replace(",", "", $monto_2));
        $operacion = ($diferencia>=0?'R':'S');
        return [$diferencia,$operacion];
    }
    public function cierreMes()
    {
        // set_time_limit(0);
        $numero_mes = date("m");
        $numero_mes_siguiente = date('m', strtotime('+1 month'));

        // $nombre_mes = ConfiguracionHelper::mesNumero($numero_mes);
        // $nombre_mes_siguiente = ConfiguracionHelper::mesNumero($numero_mes_siguiente);
        // return $numero_mes_siguiente;exit;
        $nombre_mes = $this->mes($numero_mes);
        $nombre_mes_siguiente = $this->mes($numero_mes_siguiente);
        $saldo = PresupuestoInterno::cierreMensual(3,$numero_mes,$nombre_mes,$numero_mes_siguiente, $nombre_mes_siguiente);
        // $saldo = PresupuestoInterno::cierreMensual(3,$numero_mes,$nombre_mes,$numero_mes_siguiente, $nombre_mes_siguiente);
        // PresupuestoInterno::calcularColumnaAuxMensual(30, 3, 2960,'junio');

        // $año_actua = date('Y');

        // $presupuesto_interno = PresupuestoInterno::where('estado',2)
        // ->whereYear('fecha_registro',$año_actua)
        // ->get();

        // foreach ($presupuesto_interno as $key => $value) {
        //     foreach($value->detalle()->where('id_tipo_presupuesto',3)
        //     ->orderBy('partida', 'asc')->get() as $key_detalle =>$v_detalle){
        //         if ($key_detalle!==0 && $v_detalle->registro==='2') {
        //             // return $v_detalle;exit;
        //             PresupuestoInterno::calcularColumnaAuxMensual(
        //                 $v_detalle->id_presupuesto_interno,
        //                 $v_detalle->id_tipo_presupuesto,
        //                 $v_detalle->id_presupuesto_interno_detalle,
        //                 $nombre_mes_siguiente
        //             );
        //         }
        //     }

        // }


        return response()->json(["success"=>true],200);
    }
    public function mes($mes)
    {
        $nombre_mes='enero';
        switch ($mes) {
            case '1':
                $nombre_mes='enero';
            break;

            case '2':
                $nombre_mes='febrero';
            break;
            case '3':
                $nombre_mes='marzo';
            break;
            case '4':
                $nombre_mes='abril';
            break;
            case '5':
                $nombre_mes='mayo';
            break;
            case '6':
                $nombre_mes='junio';
            break;
            case '7':
                $nombre_mes='julio';
            break;
            case '8':
                $nombre_mes='agosto';
            break;
            case '9':
                $nombre_mes='setiembre';
            break;
            case '10':
                $nombre_mes='octubre';
            break;
            case '11':
                $nombre_mes='noviembre';
            break;
            case '12':
                $nombre_mes='diciembre';
            break;
        }
        return $nombre_mes;
    }

    public function afectarPresupuestoInterno($sumaResta, $tipoDocumento, $id, $detalle)
    {
        $mesLista = ['1' => 'enero', '2' => 'febrero', '3' => 'marzo', '4' => 'abril', '5' => 'mayo', '6' => 'junio', '7' => 'julio', '8' => 'agosto', '9' => 'setiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre'];
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

                        $mes = intval(date('m', strtotime($requerimiento->fecha_registro)));
                        $nombreMes = $mesLista[$mes];
                        $nombreMesAux = $nombreMes . '_aux';
                        $mesEnDosDigitos =str_pad($mes, 2, "0", STR_PAD_LEFT);

                        if ($requerimiento->id_presupuesto_interno > 0) {
                            $presupuestoInternoDetalle = PresupuestoInternoDetalle::where([
                                ['id_presupuesto_interno', $requerimiento->id_presupuesto_interno],
                                ['estado', 1], ['id_presupuesto_interno_detalle', $detalleRequerimiento->id_partida_pi]
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
                                // PresupuestoInterno::calcularColumnaAuxMensual($requerimiento->id_presupuesto_interno, 3, $detalleRequerimiento->partida, $nombreMes);
                                $historialPresupuestoInternoSaldo = new HistorialPresupuestoInternoSaldo();
                                $historialPresupuestoInternoSaldo->id_presupuesto_interno = $requerimiento->id_presupuesto_interno;
                                $historialPresupuestoInternoSaldo->id_partida = $detalleRequerimiento->id_partida_pi;
                                $historialPresupuestoInternoSaldo->tipo = $TipoHistorial;
                                $historialPresupuestoInternoSaldo->importe = $item->importe_item_para_presupuesto??0;
                                $historialPresupuestoInternoSaldo->mes = $mesEnDosDigitos;
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
                $mes = intval(date('m', strtotime($requerimientoPago->fecha_registro)));
                $nombreMes = $mesLista[$mes];
                $nombreMesAux = $nombreMes . '_aux';
                $mesEnDosDigitos =str_pad($mes, 2, "0", STR_PAD_LEFT);

                if($requerimientoPago->id_presupuesto_interno >0){
                    foreach ($detalle as $item) {
                        if ($item->id_partida_pi > 0) {

                            $presupuestoInternoDetalle = PresupuestoInternoDetalle::where([
                                ['id_presupuesto_interno', $requerimientoPago->id_presupuesto_interno],
                                ['estado', 1], ['id_presupuesto_interno_detalle', $item->id_partida_pi]
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

                                // Debugbar::info($requerimientoPago->id_presupuesto_interno, 3, $item->id_partida_pi, $nombreMes);

                                $historial_saldo = HistorialPresupuestoInternoSaldo::where('id_requerimiento_pago_detalle',$item->id_requerimiento_pago_detalle)->where('id_requerimiento_pago',$requerimientoPago->id_requerimiento_pago)->first();

                                if(!$historial_saldo) {
                                    PresupuestoInterno::calcularColumnaAuxMensual($requerimientoPago->id_presupuesto_interno, 3, $item->id_partida_pi, $nombreMes);
                                    $historialPresupuestoInternoSaldo = new HistorialPresupuestoInternoSaldo();
                                    $historialPresupuestoInternoSaldo->id_presupuesto_interno = $requerimientoPago->id_presupuesto_interno;
                                    $historialPresupuestoInternoSaldo->id_partida = $item->id_partida_pi;
                                    $historialPresupuestoInternoSaldo->tipo = $TipoHistorial;
                                    $historialPresupuestoInternoSaldo->importe = $item->importe_item_para_presupuesto??0;
                                    $historialPresupuestoInternoSaldo->mes = $mesEnDosDigitos;
                                    $historialPresupuestoInternoSaldo->id_requerimiento_pago = $requerimientoPago->id_requerimiento_pago;
                                    $historialPresupuestoInternoSaldo->id_requerimiento_pago_detalle = $item->id_requerimiento_pago_detalle;
                                    $historialPresupuestoInternoSaldo->operacion = $operacion;
                                    $historialPresupuestoInternoSaldo->estado = 1;
                                    $historialPresupuestoInternoSaldo->fecha_registro = new Carbon();
                                    $historial = $historialPresupuestoInternoSaldo;
                                    $historialPresupuestoInternoSaldo->save();
                                }
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

    public function actualizaEstadoHistorial($idDetalleRequerimiento,$estado){
        $historial = PresupuestoInternoHistorialHelper::actualizaReqLogisticoEstadoHistorial($idDetalleRequerimiento,$estado);
        return $historial;
    }

    public function presupuestoEjecutadoExcel(Request $request){

        $mesEnFormatoFechaList=[];
        foreach (range(0, intval(date('m'))) as $number) {
            $mesEnFormatoFechaList[] = ConfiguracionHelper::leftZero(2,$number);
        }

        $historial_saldo = HistorialPresupuestoInternoSaldo::where('id_presupuesto_interno',$request->id)
        ->where('tipo','SALIDA')
        ->whereIn('mes',$mesEnFormatoFechaList)
        ->orderBy('id','ASC')->get();

        foreach($historial_saldo as $key => $value) {
            $requerimiento = array();
            $requerimiento = Orden::find($value->id_orden);
            $requerimiento_detalle = OrdenCompraDetalle::where('id_detalle_orden',$value->id_orden_detalle)->get();
            $value->cuadro = 1;
            if (!$requerimiento) {

                $requerimiento = RequerimientoPago::find($value->id_requerimiento_pago);
                $requerimiento_detalle = RequerimientoPagoDetalle::where('id_requerimiento_pago_detalle',$value->id_requerimiento_pago_detalle)->get();
                $value->cuadro = 2;
            }
            $value->cabecera = $requerimiento;
            $value->detalle = $requerimiento_detalle;

            $partida_detalle = PresupuestoInternoDetalle::find($value->id_partida);
            $value->partida = $partida_detalle->partida;
            $value->partida_descripcion = $partida_detalle->descripcion;

            $tipo='';
            switch ($partida_detalle->id_tipo_presupuesto) {
                case 1:
                    $tipo='INGRESOS';
                break;

                case 2:
                    $tipo='COSTOS';
                break;
                case 3:
                    $tipo='GASTOS';
                break;
            }
            $value->tipo = $tipo;

            $presupuesto_interno = PresupuestoInterno::find($value->id_presupuesto_interno);
            $value->codigo_ppt = $presupuesto_interno->codigo;
            $value->codigo_nombre = $presupuesto_interno->descripcion;

            $partid_padre = PresupuestoInternoDetalle::where('id_hijo',$partida_detalle->id_padre)->where('id_presupuesto_interno',$value->id_presupuesto_interno)->first();
            $value->partida_padre = $partid_padre->partida;
            $value->partida_padre_descripcion = $partid_padre->descripcion;
        }

        // return response()->json($historial_saldo,200);
        return Excel::download(new PresupuestoInternoEjecutadoExport($historial_saldo), 'presupuesto_interno_monto_ejecutado.xlsx');

        // return response()->json($historial_saldo,200);
    }

}
