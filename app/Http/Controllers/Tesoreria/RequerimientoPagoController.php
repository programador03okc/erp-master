<?php

namespace App\Http\Controllers\Tesoreria;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProyectosController;
use App\Models\Administracion\DivisionArea;
use App\Models\Administracion\Documento;
use App\Models\Administracion\Empresa;
use App\Models\Administracion\Periodo;
use App\Models\Administracion\Prioridad;
use App\Models\Almacen\Trazabilidad;
use App\Models\Almacen\UnidadMedida;
use App\Models\Configuracion\Grupo;
use App\Models\Configuracion\Moneda;
use App\Models\Contabilidad\Banco;
use App\Models\Contabilidad\TipoCuenta;
use App\Models\mgcp\CuadroCosto\CuadroCostoView;
use App\Models\Tesoreria\AdjuntoRequerimientoPago;
use App\Models\Tesoreria\CategoriaAdjunto;
use App\Models\Tesoreria\DetalleRequerimientoPago;
use App\Models\Tesoreria\RequerimientoPago;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Yajra\DataTables\Facades\DataTables;

class RequerimientoPagoController extends Controller
{
    public function __construct()
    {
        // session_start();
    }

    public function viewListaRequerimientoPago()
    {
        $periodos = Periodo::mostrar();
        $prioridades = Prioridad::mostrar();
        $gruposUsuario = Auth::user()->getAllGrupo();

        $empresas = Empresa::mostrar();
        $grupos = Grupo::mostrar();
        $divisiones = DivisionArea::mostrar();
        $monedas = Moneda::mostrar();
        $unidadesMedida = UnidadMedida::mostrar();
        $proyectos_activos = (new ProyectosController)->listar_proyectos_activos();
        $bancos = Banco::mostrar();
        $tipo_cuenta = TipoCuenta::mostrar();



        return view('tesoreria/requerimiento_pago/lista', compact('prioridades', 'empresas', 'grupos', 'periodos', 'monedas', 'unidadesMedida', 'divisiones', 'gruposUsuario', 'proyectos_activos','bancos','tipo_cuenta'));
    }
    public function viewRevisarAprobarRequerimientoPago()
    {
        $periodos = Periodo::mostrar();
        $prioridades = Prioridad::mostrar();
        $gruposUsuario = Auth::user()->getAllGrupo();

        $empresas = Empresa::mostrar();
        $grupos = Grupo::mostrar();
        $divisiones = DivisionArea::mostrar();
        $monedas = Moneda::mostrar();
        $unidadesMedida = UnidadMedida::mostrar();
        $proyectos_activos = (new ProyectosController)->listar_proyectos_activos();



        return view('tesoreria/requerimiento_pago/revisar_aprobar', compact('prioridades', 'empresas', 'grupos', 'periodos', 'monedas', 'unidadesMedida', 'divisiones', 'gruposUsuario', 'proyectos_activos'));
    }
    function listarRequerimientoPago(Request $request)
    {
        $mostrar = $request->meOrAll;
        $idEmpresa = $request->idEmpresa;
        $idSede = $request->idSede;
        $idGrupo = $request->idGrupo;
        $division = $request->idDivision;
        $fechaRegistroDesde = $request->fechaRegistroDesde;
        $fechaRegistroHasta = $request->fechaRegistroHasta;
        $idEstado = $request->idEstado;

        $data = RequerimientoPago::with('detalle')
            ->leftJoin('administracion.adm_estado_doc', 'requerimiento_pago.estado', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('administracion.adm_prioridad', 'requerimiento_pago.id_prioridad', '=', 'adm_prioridad.id_prioridad')
            ->leftJoin('configuracion.sis_grupo', 'requerimiento_pago.id_grupo', '=', 'sis_grupo.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'requerimiento_pago.id_sede')
            ->leftJoin('configuracion.sis_moneda', 'requerimiento_pago.id_moneda', '=', 'sis_moneda.id_moneda')
            ->leftJoin('administracion.adm_periodo', 'adm_periodo.id_periodo', '=', 'requerimiento_pago.id_periodo')
            ->leftJoin('administracion.adm_empresa', 'requerimiento_pago.id_empresa', '=', 'adm_empresa.id_empresa')
            ->leftJoin('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'requerimiento_pago.id_usuario')
            ->leftJoin('administracion.division', 'division.id_division', '=', 'requerimiento_pago.id_division')
            ->select(
                'requerimiento_pago.*',
                'sis_moneda.descripcion as moneda',
                'adm_periodo.descripcion as periodo',
                'adm_prioridad.descripcion as prioridad',
                'sis_grupo.descripcion as grupo',
                'sis_sede.codigo as sede',
                'division.descripcion as division',
                'adm_contri.razon_social as empresa_razon_social',
                'adm_contri.nro_documento as empresa_nro_documento',
                'sis_identi.descripcion as empresa_tipo_documento',
                'sis_usua.nombre_corto as usuario_nombre_corto'
            )
            ->when(($mostrar === 'ME'), function ($query) {
                $idUsuario = Auth::user()->id_usuario;
                return $query->whereRaw('requerimiento_pago.id_usuario = ' . $idUsuario);
            })
            ->when(($mostrar === 'ALL'), function ($query) {
                return $query->whereRaw('requerimiento_pago.id_usuario > 0');
            })
            ->when((intval($idEmpresa) > 0), function ($query)  use ($idEmpresa) {
                return $query->whereRaw('requerimiento_pago.id_empresa = ' . $idEmpresa);
            })
            ->when((intval($idSede) > 0), function ($query)  use ($idSede) {
                return $query->whereRaw('requerimiento_pago.id_sede = ' . $idSede);
            })
            ->when((intval($idGrupo) > 0), function ($query)  use ($idGrupo) {
                return $query->whereRaw('sis_grupo.id_grupo = ' . $idGrupo);
            })
            ->when((intval($division) > 0), function ($query)  use ($division) {
                return $query->whereRaw('requerimiento_pago.division_id = ' . $division);
            })
            ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta == 'SIN_FILTRO')), function ($query) use ($fechaRegistroDesde) {
                return $query->where('requerimiento_pago.fecha_registro', '>=', $fechaRegistroDesde);
            })
            ->when((($fechaRegistroDesde == 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use ($fechaRegistroHasta) {
                return $query->where('requerimiento_pago.fecha_registro', '<=', $fechaRegistroHasta);
            })
            ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use ($fechaRegistroDesde, $fechaRegistroHasta) {
                return $query->whereBetween('requerimiento_pago.fecha_registro', [$fechaRegistroDesde, $fechaRegistroHasta]);
            })

            ->when((intval($idEstado) > 0), function ($query)  use ($idEstado) {
                return $query->whereRaw('requerimiento_pago.estado = ' . $idEstado);
            });

        return datatables($data)
            ->filterColumn('requerimiento_pago.fecha_registro', function ($query, $keyword) {
                try {
                    $desde = Carbon::createFromFormat('d-m-Y', trim($keyword))->hour(0)->minute(0)->second(0);
                    $hasta = Carbon::createFromFormat('d-m-Y', trim($keyword));
                    $query->whereBetween('requerimiento_pago.fecha_registro', [$desde, $hasta->addDay()->addSeconds(-1)]);
                } catch (\Throwable $th) {
                }
            })
            ->rawColumns(['termometro'])->toJson();
    }


    function listarDetalleRequerimientoPago($idRequerimientoPago)
    {

        $detalles = DetalleRequerimientoPago::select(
            'requerimiento_pago.codigo as codigo_requerimiento_pago',
            'detalle_requerimiento_pago.*',
            'sis_moneda.simbolo as moneda_simbolo',
            'sis_moneda.descripcion as moneda_descripcion',
            'adm_estado_doc.estado_doc',
            'adm_estado_doc.bootstrap_color',
            'alm_prod.descripcion as producto_descripcion',
            'alm_prod.codigo as producto_codigo',
            'alm_prod.cod_softlink as producto_codigo_softlink',
            'alm_prod.part_number as producto_part_number',
            'alm_und_medida.abreviatura'
        )
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'detalle_requerimiento_pago.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'detalle_requerimiento_pago.id_unidad_medida')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'detalle_requerimiento_pago.estado')
            ->join('tesoreria.requerimiento_pago', 'requerimiento_pago.id_requerimiento_pago', '=', 'detalle_requerimiento_pago.id_requerimiento_pago')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'requerimiento_pago.id_moneda')
            ->where([
                ['requerimiento_pago.id_requerimiento_pago', '=', $idRequerimientoPago],
                ['requerimiento_pago.estado', '!=', 7]
            ])
            ->get();

        return response()->json($detalles);
    }



    function guardarRequerimientoPago(Request $request)
    {
        DB::beginTransaction();
        try {

            $requerimientoPago = new RequerimientoPago();
            $requerimientoPago->id_usuario = Auth::user()->id_usuario;
            $requerimientoPago->concepto = strtoupper($request->concepto);
            $requerimientoPago->fecha_registro = new Carbon();
            $requerimientoPago->id_periodo = $request->periodo;
            $requerimientoPago->id_moneda = $request->moneda > 0 ? $request->moneda : null;
            $requerimientoPago->id_prioridad = $request->prioridad > 0 ? $request->prioridad : null;
            $requerimientoPago->comentario = $request->comentario;
            $requerimientoPago->id_empresa = $request->empresa ? $request->empresa : null;
            $requerimientoPago->id_sede = $request->sede > 0 ? $request->sede : null;
            $requerimientoPago->id_grupo = $request->grupo > 0 ? $request->grupo : null;
            $requerimientoPago->id_division = $request->division;
            $requerimientoPago->id_proveedor = $request->id_proveedor >0 ? $request->id_proveedor :null;
            $requerimientoPago->id_cuenta_proveedor = $request->id_cuenta_principal_proveedor>0?$request->id_cuenta_principal_proveedor:null;
            // $requerimientoPago->confirmacion_pago = ($request->tipo_requerimiento == 2 ? ($request->fuente == 2 ? true : false) : true);
            $requerimientoPago->monto_total = $request->monto_total;
            $requerimientoPago->id_proyecto = $request->proyecto > 0 ? $request->proyecto : null;
            $requerimientoPago->id_cc = $request->id_cc > 0 ? $request->id_cc : null;
            $requerimientoPago->estado = 1;
            $requerimientoPago->save();

            $count = count($request->descripcion);
            $montoTotal = 0;
            for ($i = 0; $i < $count; $i++) {
                if ($request->cantidad[$i] <= 0) {
                    return response()->json(['id_requerimiento_pago' => 0, 'codigo' => '', 'mensaje' => 'La cantidad solicitada debe ser mayor a 0']);
                }

                $detalle = new DetalleRequerimientoPago();
                $detalle->id_requerimiento_pago = $requerimientoPago->id_requerimiento_pago;
                $detalle->id_tipo_item = $request->tipoItem[$i];
                $detalle->id_partida = $request->idPartida[$i];
                $detalle->id_centro_costo = $request->idCentroCosto[$i];
                $detalle->part_number = $request->partNumber[$i];
                $detalle->descripcion = $request->descripcion[$i];
                $detalle->id_unidad_medida = $request->unidad[$i];
                $detalle->cantidad = $request->cantidad[$i];
                $detalle->precio_unitario = floatval($request->precioUnitario[$i]);
                $detalle->subtotal = floatval($request->cantidad[$i] * $request->precioUnitario[$i]);
                $detalle->fecha_registro = new Carbon();
                $detalle->estado = 1;
                $detalle->save();
                $detalle->idRegister = $request->idRegister[$i];
                $detalleArray[] = $detalle;
                $montoTotal += $detalle->cantidad * $detalle->precioUnitario;
            }

            DB::commit();

            $codigo = RequerimientoPago::crearCodigo($request->grupo, $requerimientoPago->id_requerimiento_pago);
            $rp = RequerimientoPago::find($requerimientoPago->id_requerimiento_pago);
            $rp->codigo = $codigo;
            $rp->save();

            $documento = new Documento();
            $documento->id_tp_documento = 11;
            $documento->codigo_doc = $codigo;
            $documento->id_doc = $requerimientoPago->id_requerimiento_pago;
            $documento->save();

            return response()->json(['id_requerimiento_pago' => $requerimientoPago->id_requerimiento_pago, 'mensaje' => 'Se guardó el requerimiento de pago ' . $codigo]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['id_requerimiento_pago' => 0, 'mensaje' => 'Hubo un problema al guardar el requerimiento de pago. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

    function actualizarRequerimientoPago(Request $request)
    {
        DB::beginTransaction();
        try {
            $requerimientoPago = RequerimientoPago::where("id_requerimiento_pago", $request->id_requerimiento_pago)->first();
            $requerimientoPago->id_usuario = Auth::user()->id_usuario;
            $requerimientoPago->concepto = strtoupper($request->concepto);
            $requerimientoPago->id_periodo = $request->periodo;
            $requerimientoPago->id_moneda = $request->moneda > 0 ? $request->moneda : null;
            $requerimientoPago->id_prioridad = $request->prioridad > 0 ? $request->prioridad : null;
            $requerimientoPago->comentario = $request->comentario;
            $requerimientoPago->id_empresa = $request->empresa ? $request->empresa : null;
            $requerimientoPago->id_sede = $request->sede > 0 ? $request->sede : null;
            $requerimientoPago->id_grupo = $request->grupo > 0 ? $request->grupo : null;
            $requerimientoPago->id_division = $request->division;
            $requerimientoPago->id_proveedor = $request->id_proveedor >0 ? $request->id_proveedor :null;
            $requerimientoPago->id_cuenta_proveedor = $request->id_cuenta_principal_proveedor>0?$request->id_cuenta_principal_proveedor:null;
            // $requerimientoPago->confirmacion_pago = ($request->tipo_requerimiento == 2 ? ($request->fuente == 2 ? true : false) : true);
            $requerimientoPago->monto_total = $request->monto_total;
            $requerimientoPago->id_proyecto = $request->proyecto > 0 ? $request->proyecto : null;
            $requerimientoPago->id_cc = $request->id_cc > 0 ? $request->id_cc : null;
            $requerimientoPago->save();

            $count = count($request->descripcion);

            for ($i = 0; $i < $count; $i++) {
                $id = $request->idRegister[$i];

                if (preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $id)) // es un id con numeros y letras => es nuevo, insertar
                {
                    $detalle = new DetalleRequerimientoPago();

                    $detalle->id_requerimiento_pago = $requerimientoPago->id_requerimiento_pago;
                    $detalle->id_tipo_item = $request->tipoItem[$i];
                    $detalle->id_partida = $request->idPartida[$i];
                    $detalle->id_centro_costo = $request->idCentroCosto[$i];
                    $detalle->part_number = $request->partNumber[$i];
                    $detalle->descripcion = $request->descripcion[$i] != null ? trim(strtoupper($request->descripcion[$i])) : null;
                    $detalle->id_unidad_medida = $request->unidad[$i];
                    $detalle->cantidad = $request->cantidad[$i];
                    $detalle->precio_unitario = floatval($request->precioUnitario[$i]);
                    $detalle->subtotal = floatval($request->cantidad[$i] * $request->precioUnitario[$i]);
                    $detalle->fecha_registro = new Carbon();
                    $detalle->estado = 1;
                    $detalle->save();
                } else { // es un id solo de numerico => actualiza
                    if ($request->idEstado[$i] == 7) {
                        if (is_numeric($id)) { // si es un numero 
                            $detalle = DetalleRequerimientoPago::where("id_detalle_requerimiento_pago", $id)->first();
                            $detalle->estado = 7;
                            $detalle->save();
                        }
                    } else {

                        $detalle = DetalleRequerimientoPago::where("id_detalle_requerimiento_pago", $id)->first();
                        $detalle->id_tipo_item = $request->tipoItem[$i];
                        $detalle->id_partida = $request->idPartida[$i];
                        $detalle->id_centro_costo = $request->idCentroCosto[$i];
                        $detalle->part_number = $request->partNumber[$i];
                        $detalle->descripcion = $request->descripcion[$i] != null ? trim(strtoupper($request->descripcion[$i])) : null;
                        $detalle->id_unidad_medida = $request->unidad[$i];
                        $detalle->cantidad = $request->cantidad[$i];
                        $detalle->precio_unitario = floatval($request->precioUnitario[$i]);
                        $detalle->subtotal = floatval($request->cantidad[$i] * $request->precioUnitario[$i]);
                        $detalle->save();
                    }
                }
            }

            DB::commit();



            return response()->json(['id_requerimiento_pago' => $requerimientoPago->id_requerimiento_pago, 'mensaje' => 'Se actualizó el requerimiento de pago ' . $requerimientoPago->codigo]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['id_requerimiento_pago' => 0, 'mensaje' => 'Hubo un problema al actualizar el requerimiento de pago. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

    function anularRequerimientoPago(Request $request)
    {
        try {
            DB::beginTransaction();

            $idRequerimientoPago = $request->idRequerimientoPago;
            $output = [];

            if ($idRequerimientoPago > 0) {
                $requerimientoPago = RequerimientoPago::find($idRequerimientoPago);
                $todoDetalleRequerimientoPago = DetalleRequerimientoPago::where("id_requerimiento_pago", $idRequerimientoPago)->get();

                if (in_array($requerimientoPago->estado, [1, 3])) { // estado elaborado, estado observado
                    $requerimientoPago->estado = 7;
                    $requerimientoPago->save();

                    foreach ($todoDetalleRequerimientoPago as $detalleRequerimientoPago) {
                        $detalle = DetalleRequerimientoPago::where("id_detalle_requerimiento_pago", $detalleRequerimientoPago->id_detalle_requerimiento_pago)->first();
                        $detalle->estado = 7;
                        $detalle->save();
                    }
                    $output = [
                        'id_requerimiento_pago' => $idRequerimientoPago,
                        'status' => 200,
                        'tipo_estado' => 'success',
                        'mensaje' => 'El requerimiento de pago ' . $requerimientoPago->codigo . ' fue anulado',
                    ];
                } else {
                    $output = [
                        'id_requerimiento_pago' => 0,
                        'status' => 204,
                        'tipo_estado' => 'warning',
                        'mensaje' => 'No se pudo anular el requerimiento de pago, únicamente se puede anular requerimientos en estado elaborado o observado',
                    ];
                }
            } else {
                $output = [
                    'id_requerimiento_pago' => 0,
                    'status' => 204,
                    'tipo_estado' => 'warning',
                    'mensaje' => 'No se pudo anular el requerimiento de pago, el id no es valido',
                ];
            }


            DB::commit();

            return response()->json($output);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['id_requerimiento_pago' => 0, 'tipo_estado' => 'error',  'mensaje' => 'Hubo un problema en el método para anular el requerimiento de pago. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }


    function listaCuadroPresupuesto(Request $request)
    {
        $data = CuadroCostoView::where('eliminado', false);

        return datatables($data)->toJson();
    }


    function mostrarRequerimientoPago($idRequerimientoPago)
    {

        // $data = RequerimientoPago::where('id_requerimiento_pago',$idRequerimientoPago)->with(['detalle'=> function($q){
        //     $q->where('estado', '!=', 7);
        // },'empresa','sede','grupo','division','moneda','creadoPor','detalle.unidadMedida','detalle.producto','detalle.estado'])->get();
        // return $data ;
        // $detalleRequerimientoPagoList=  DetalleRequerimientoPago::select(
        //     'detalle_requerimiento_pago.*',
        //     'adm_estado_doc.estado_doc',
        //     'alm_prod.codigo as producto_codigo',
        //     'alm_prod.part_number as producto_part_number',
        //     'alm_prod.descripcion as producto_descripcion',
        //     'alm_prod.id_unidad_medida as producto_id_unidad_medida',
        //     'presup_par.codigo AS codigo_partida',
        //     'presup_pardet.descripcion AS descripcion_partida',
        //     'presup_par.importe_total AS presupuesto_total_partida'
        // )
        // ->leftJoin('almacen.alm_prod', 'detalle_requerimiento_pago.id_producto', '=', 'alm_prod.id_producto')
        // ->leftJoin('administracion.adm_estado_doc', 'detalle_requerimiento_pago.estado', '=', 'adm_estado_doc.id_estado_doc')
        // ->leftJoin('finanzas.presup_par', 'presup_par.id_partida', '=', 'detalle_requerimiento_pago.id_partida')
        // ->leftJoin('finanzas.presup_pardet', 'presup_pardet.id_pardet', '=', 'presup_par.id_pardet')
        // ->where([['detalle_requerimiento_pago.id_requerimiento_pago',$idRequerimientoPago],['detalle_requerimiento_pago.estado','!=',7]])
        // ->get();
        $detalleRequerimientoPagoList = DetalleRequerimientoPago::with('unidadMedida', 'producto', 'partida.presupuesto', 'centroCosto', 'estado')->where([['id_requerimiento_pago', $idRequerimientoPago], ['estado', '!=', 7]])->get();

        $requerimientoPago = RequerimientoPago::where('id_requerimiento_pago', $idRequerimientoPago)->with('periodo', 'prioridad', 'moneda', 'creadoPor', 'empresa', 'sede', 'grupo', 'division', 'cuadroCostos', 'proyecto')->first();

        return $requerimientoPago->setAttribute('detalle', $detalleRequerimientoPagoList);
    }

    function listaAdjuntosCabeceraRequerimientoPago($idRequerimientoPago){
        $data = AdjuntoRequerimientoPago::where([['id_requerimiento_pago',$idRequerimientoPago],['estado','!=',7]])->with('categoriaAdjunto')->get();
        return response()->json($data);

    }
    function listaCategoriaAdjuntos(){
        $data = CategoriaAdjunto::where("estado",'!=',7)->get();
        return response()->json($data);

    }

}
