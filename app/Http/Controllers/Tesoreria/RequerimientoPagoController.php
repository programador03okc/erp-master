<?php

namespace App\Http\Controllers\Tesoreria;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProyectosController;
use App\Models\Administracion\Division;
use App\Models\Administracion\Documento;
use App\Models\Administracion\Empresa;
use App\Models\Administracion\Periodo;
use App\Models\Administracion\Prioridad;
use App\Models\Almacen\Trazabilidad;
use App\Models\Almacen\UnidadMedida;
use App\Models\Configuracion\Grupo;
use App\Models\Configuracion\Moneda;
use App\Models\mgcp\CuadroCosto\CuadroCostoView;
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
    public function __construct(){
        // session_start();
    }

    public function viewListaRequerimientoPago(){
        $periodos = Periodo::mostrar();
        $prioridades = Prioridad::mostrar();
        $gruposUsuario = Auth::user()->getAllGrupo();

        $empresas = Empresa::mostrar();
        $grupos = Grupo::mostrar();
        $divisiones = Division::mostrar();
        $monedas = Moneda::mostrar();
        $unidadesMedida = UnidadMedida::mostrar();
        $proyectos_activos = (new ProyectosController)->listar_proyectos_activos();



        return view('tesoreria/requerimiento_pago/lista',compact('prioridades','empresas','grupos','periodos','monedas','unidadesMedida','divisiones','gruposUsuario','proyectos_activos'));

    }
    function listarRequerimientoPago(Request $request){
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
            ->select('requerimiento_pago.*',
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
            ->when((intval($division) >0), function ($query)  use ($division) {
                return $query->whereRaw('requerimiento_pago.division_id = ' . $division);
            })
            ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta == 'SIN_FILTRO')), function ($query) use($fechaRegistroDesde) {
                return $query->where('requerimiento_pago.fecha_registro' ,'>=',$fechaRegistroDesde); 
            })
            ->when((($fechaRegistroDesde == 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use($fechaRegistroHasta) {
                return $query->where('requerimiento_pago.fecha_registro' ,'<=',$fechaRegistroHasta); 
            })
            ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use($fechaRegistroDesde,$fechaRegistroHasta) {
                return $query->whereBetween('requerimiento_pago.fecha_registro' ,[$fechaRegistroDesde,$fechaRegistroHasta]); 
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


    function listarDetalleRequerimientoPago($idRequerimientoPago){

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



    function guardarRequerimientoPago(Request $request){
        DB::beginTransaction();
        try {

            $requerimientoPago = new RequerimientoPago();
            $requerimientoPago->id_usuario = Auth::user()->id_usuario;
            $requerimientoPago->concepto = strtoupper($request->concepto);
            $requerimientoPago->fecha_registro = new Carbon();
            $requerimientoPago->id_periodo = $request->periodo;
            $requerimientoPago->id_moneda = $request->moneda > 0 ? $request->moneda : null;
            $requerimientoPago->id_prioridad = $request->prioridad >0 ? $request->prioridad:null;
            $requerimientoPago->comentario = $request->comentario;
            $requerimientoPago->id_empresa = $request->empresa ? $request->empresa : null;
            $requerimientoPago->id_sede = $request->sede > 0 ? $request->sede : null;
            $requerimientoPago->id_grupo = $request->grupo > 0 ? $request->grupo : null;
            $requerimientoPago->id_division = $request->division;
            if($request->tipo_cuenta=='bcp'){
                $requerimientoPago->nro_cuenta = $request->nro_cuenta;
            }else if($request->tipo_cuenta=='cci'){
                $requerimientoPago->nro_cuenta_interbancaria = $request->nro_cuenta;
            }
            if($request->tipo_documento_identidad=='dni'){
                $requerimientoPago->dni = $request->nro_documento_idendidad;
            }else if($request->tipo_documento_identidad=='ruc'){
                $requerimientoPago->ruc = $request->nro_documento_idendidad;
            }
            // $requerimientoPago->confirmacion_pago = ($request->tipo_requerimiento == 2 ? ($request->fuente == 2 ? true : false) : true);
            $requerimientoPago->monto_total = $request->monto_total;
            $requerimientoPago->id_proyecto = $request->proyecto > 0 ? $request->proyecto : null;
            $requerimientoPago->id_cc = $request->id_cc > 0?$request->id_cc:null;
            $requerimientoPago->estado = 1;
            $requerimientoPago->save();

            $count = count($request->descripcion);
            $montoTotal = 0;
            for ($i = 0; $i < $count; $i++) {
                if ($request->cantidad[$i]<=0) {
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
                $montoTotal += $detalle->cantidad * $detalle->precio_unitario;
            }

            DB::commit();

            $codigo= RequerimientoPago::crearCodigo($request->grupo, $requerimientoPago->id_requerimiento_pago);
            $rp = RequerimientoPago::find($requerimientoPago->id_requerimiento_pago);
            $rp->codigo =$codigo;
            $rp->save();

            $documento = new Documento();
            $documento->id_tp_documento = 11;
            $documento->codigo_doc = $codigo;
            $documento->id_doc = $requerimientoPago->id_requerimiento_pago;
            $documento->save();
    
            return response()->json(['id_requerimiento_pago' => $requerimientoPago->id_requerimiento_pago, 'mensaje' => 'Se guardó el requerimiento de pago '.$codigo]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['id_requerimiento_pago' => 0, 'mensaje' => 'Hubo un problema al guardar el requerimiento de pago. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }


    function listaCuadroPresupuesto(Request $request){
        $data = CuadroCostoView::where('eliminado',false);
    
        return datatables($data)->toJson();
    }
    
    // function view_main_tesoreria(){
    //     $pagos_pendientes = DB::table('almacen.alm_req')
    //     ->where('estado',8)->count();

    //     $confirmaciones_pendientes = DB::table('almacen.alm_req')
    //     ->where([['estado','=',19],['confirmacion_pago','=',false]])->count();

    //     return view('tesoreria/main', compact('pagos_pendientes','confirmaciones_pendientes'));
    // }
    
    // function view_pendientes_pago(){
    //     return view('tesoreria/pagos/pendientesPago');
    // }



    // public function listarOrdenesCompra(){
    //     $data = DB::table('logistica.log_ord_compra')
    //     ->select(
    //         'log_ord_compra.*','adm_contri.razon_social',
    //         'estados_compra.descripcion as estado_doc',
    //         'sis_moneda.simbolo','log_cdn_pago.descripcion AS condicion_pago',
    //         'sis_sede.descripcion as sede_descripcion',
    //         // 'req_pagos.total_pago','req_pagos.adjunto',
    //         // 'req_pagos.fecha_pago','req_pagos.observacion',
    //         // 'registrado_por.nombre_corto as usuario_pago',
    //         'adm_cta_contri.nro_cuenta',
    //         DB::raw("(SELECT sum(subtotal) FROM logistica.log_det_ord_compra
    //                     WHERE log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra
    //                     and log_det_ord_compra.estado != 7) AS suma_total"),
    //         DB::raw("(SELECT sum(total_pago) FROM tesoreria.req_pagos
    //                     WHERE req_pagos.id_oc = log_ord_compra.id_orden_compra
    //                     and req_pagos.estado != 7) AS suma_pagado")
    //         )
    //     ->join('logistica.log_prove','log_prove.id_proveedor','=','log_ord_compra.id_proveedor')
    //     ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
    //     ->join('logistica.estados_compra','estados_compra.id_estado','=','log_ord_compra.estado')
    //     ->leftJoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','log_ord_compra.id_moneda')
    //     ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_ord_compra.id_condicion')
    //     ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
    //     // ->leftJoin('tesoreria.req_pagos','req_pagos.id_oc','=','log_ord_compra.id_orden_compra')
    //     // ->leftJoin('configuracion.sis_usua as registrado_por','registrado_por.id_usuario','=','req_pagos.registrado_por')
    //     ->leftJoin('contabilidad.adm_cta_contri','adm_cta_contri.id_cuenta_contribuyente','=','log_ord_compra.id_cta_principal')
    //     ->where([['log_ord_compra.id_condicion','=',1],['log_ord_compra.estado','!=',7]]);

    //     return datatables($data)->toJson();
    // }

    // public function listarComprobantesPagos(){
    //     $data = DB::table('almacen.doc_com')
    //     ->select(
    //         'doc_com.id_doc_com','doc_com.serie','doc_com.numero','adm_contri.razon_social',
    //         'doc_com.fecha_emision','doc_com.fecha_vcmto','doc_com.serie',
    //         'doc_com.total_a_pagar','doc_com.estado','doc_com.credito_dias',
    //         'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
    //         'sis_moneda.simbolo','log_cdn_pago.descripcion AS condicion_pago',
    //         'cont_tp_doc.descripcion as tipo_documento',
    //         'adm_cta_contri.nro_cuenta',
    //         DB::raw("(SELECT sum(total_pago) FROM tesoreria.req_pagos
    //                   WHERE req_pagos.id_doc_com = doc_com.id_doc_com
    //                     and req_pagos.estado != 7) AS suma_pagado")
    //         )
    //     ->join('logistica.log_prove','log_prove.id_proveedor','=','doc_com.id_proveedor')
    //     ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
    //     ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','doc_com.estado')
    //     ->leftJoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','doc_com.moneda')
    //     ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_com.id_condicion')
    //     ->leftJoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_com.id_tp_doc')
    //     ->leftJoin('contabilidad.adm_cta_contri','adm_cta_contri.id_cuenta_contribuyente','=','doc_com.id_cta_bancaria')
    //     ->where('doc_com.id_condicion',2)
    //     ->whereIn('doc_com.estado',[1,9]);

    //     // return datatables($data)->toJson();
    //     return DataTables::of($data)
    //     ->editColumn('fecha_emision', function ($data) { 
    //         return ($data->fecha_emision!==null ? date('d-m-Y', strtotime($data->fecha_emision)) : ''); 
    //     })
    //     ->editColumn('condicion_pago', function ($data) { 
    //         return ($data->condicion_pago!==null ? ($data->condicion_pago.' '.$data->credito_dias.' días') : ''); 
    //     })
    //     ->editColumn('fecha_vcmto', function ($data) { 
    //         return ($data->fecha_vcmto!==null ? date('d-m-Y', strtotime($data->fecha_vcmto)) : ''); 
    //     })
    //     ->addColumn('total_a_pagar_format', function ($data) { 
    //         return ($data->total_a_pagar!==null ? number_format($data->total_a_pagar,2) : '0.00'); 
    //     })
    //     ->addColumn('span_estado', function ($data) {
    //         $estado = ($data->estado==9 ? 'Pagada' : $data->estado_doc);
    //         return '<span class="label label-'.$data->bootstrap_color.'">'.$estado.'</span>'; 
    //     })
    //     ->rawColumns(['span_estado','total_a_pagar_format'])

    //     ->make(true);
    // }

    // public function pagosComprobante($id_doc_com)
    // {
    //     $detalles = DB::table('tesoreria.req_pagos')
    //         ->select('req_pagos.*','sis_usua.nombre_corto','sis_moneda.simbolo')
    //         ->leftJoin('almacen.doc_com','doc_com.id_doc_com','=','req_pagos.id_doc_com')
    //         ->leftJoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','doc_com.moneda')
    //         ->leftJoin('configuracion.sis_usua','sis_usua.id_usuario','=','req_pagos.registrado_por')
    //         ->where([['req_pagos.id_doc_com','=',$id_doc_com],
    //                  ['req_pagos.estado','!=',7]])
    //         ->get();

    //     return response()->json($detalles);
    // }

    // public function pagosOrdenes($id_oc)
    // {
    //     $detalles = DB::table('tesoreria.req_pagos')
    //         ->select('req_pagos.*','sis_usua.nombre_corto','sis_moneda.simbolo')
    //         ->leftJoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','req_pagos.id_oc')
    //         ->leftJoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','log_ord_compra.id_moneda')
    //         ->leftJoin('configuracion.sis_usua','sis_usua.id_usuario','=','req_pagos.registrado_por')
    //         ->where([['req_pagos.id_oc','=',$id_oc],
    //                  ['req_pagos.estado','!=',7]])
    //         ->get();

    //     return response()->json($detalles);
    // }

    // public function detalleComprobante($id_doc_com)
    // {
    //     $detalles = DB::table('almacen.doc_com_det')
    //         ->select('doc_com_det.*','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
    //                 'alm_prod.descripcion as producto_descripcion','alm_prod.codigo as producto_codigo',
    //                 'alm_und_medida.abreviatura','alm_prod.part_number')
    //         ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'doc_com_det.id_item')
    //         ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'doc_com_det.id_unid_med')
    //         ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'doc_com_det.estado')
    //         ->where([['doc_com_det.id_doc','=',$id_doc_com],
    //                  ['doc_com_det.estado','!=',7]])
    //         ->get();

    //     return response()->json($detalles);
    // }

    // function procesarPago(Request $request){
        
    //     try {
    //         DB::beginTransaction();

    //         $id_usuario = Auth::user()->id_usuario;
    //         $file = $request->file('adjunto');

    //         $id_pago = DB::table('tesoreria.req_pagos')
    //         ->insertGetId([ 'id_oc'=> $request->id_oc,
    //                         'id_doc_com'=>$request->id_doc_com,
    //                         'fecha_pago'=>$request->fecha_pago,
    //                         'observacion'=>$request->observacion,
    //                         'total_pago'=>round($request->total_pago, 2),
    //                         'registrado_por'=>$id_usuario,
    //                         'estado'=>1,
    //                         'fecha_registro'=>date('Y-m-d H:i:s')
    //             ],'id_pago');

    //         if (isset($file)){
    //             //obtenemos el nombre del archivo
    //             $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
    //             $nombre = $request->codigo.'.'.$id_pago.'.'.$extension;
    //             //indicamos que queremos guardar un nuevo archivo en el disco local
    //             File::delete(public_path('tesoreria/pagos/'.$nombre));
    //             Storage::disk('archivos')->put('tesoreria/pagos/'.$nombre,File::get($file));
                
    //             DB::table('tesoreria.req_pagos')
    //             ->where('id_pago',$id_pago)
    //             ->update([ 'adjunto'=>$nombre ]);
    //         }
            
    //         if (floatval($request->total_pago) >= floatval(round($request->total, 2))){

    //             if ($request->id_oc!==null){
    //                 DB::table('logistica.log_ord_compra')
    //                 ->where('id_orden_compra',$request->id_oc)
    //                 ->update(['estado'=>9]);//pagada
    //             }
    //             else if ($request->id_doc_com!==null){
    //                 DB::table('almacen.doc_com')
    //                 ->where('id_doc_com',$request->id_doc_com)
    //                 ->update(['estado'=>9]);//procesado
    //             }
    //         }

    //         DB::commit();
    //         return response()->json($id_pago);
            
    //     } catch (\PDOException $e) {
    //         DB::rollBack();
    //     }
    // }
}