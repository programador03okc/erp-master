<?php

namespace App\Http\Controllers\Finanzas\Presupuesto;

use App\Exports\CuadroGastosExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Presupuestos\Presupuesto;
use App\Models\Presupuestos\Grupo;
use App\Models\Presupuestos\Moneda;
use App\Http\Controllers\Controller;
use App\Models\Administracion\Empresa;
use App\Models\Configuracion\Usuario;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PresupuestoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $presupuestos = Presupuesto::all()->where('estado', 1);

        return view('finanzas.presupuestos.index', compact('presupuestos'));
    }

    public function create()
    {
        $presupuesto = new Presupuesto();
        $grupos = Grupo::all();
        $empresas = Empresa::all();
        $monedas = Moneda::all();
        $presupuestos = Presupuesto::where('estado', 1)->get();

        return view('finanzas.presupuestos.create', compact('presupuesto','empresas', 'grupos', 'monedas', 'presupuestos'));
    }

    public function mostrarPartidas($id)
    {
        $presup = Presupuesto::findOrFail($id);
        $presup->grupo;
        $presup->empresa;
        $presup->monedaSeleccionada;
        $presup->titulos;
        $presup->partidas;

        return response()->json($presup);
    }

    public function mostrarRequerimientosDetalle($id)
    {
        $detalle = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.*', 'alm_req.codigo', 'alm_req.concepto', 'alm_req.fecha_requerimiento')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->where([
                ['alm_det_req.partida', '=', $id],
                ['alm_det_req.estado', '!=', 7]
            ])
            ->get();

        $pagos = DB::table('tesoreria.requerimiento_pago_detalle')
            ->select(
                'requerimiento_pago_detalle.*',
                'requerimiento_pago.codigo',
                'requerimiento_pago.concepto',
                'requerimiento_pago.fecha_registro'
            )
            ->join('tesoreria.requerimiento_pago', 'requerimiento_pago.id_requerimiento_pago', '=', 'requerimiento_pago_detalle.id_requerimiento_pago')
            ->where([
                ['requerimiento_pago_detalle.id_partida', '=', $id],
                ['requerimiento_pago_detalle.id_estado', '!=', 7]
            ])
            ->get();

        return response()->json(['req_compras' => $detalle, 'req_pagos' => $pagos]);
    }


    public function obtenerDetallePresupuesto($id_presupuesto)
    {
        $detalle = DB::table('logistica.log_det_ord_compra')
            ->select(
                'log_det_ord_compra.*',
                'alm_req.codigo',
                'alm_req.fecha_requerimiento',
                'adm_contri.razon_social',
                'adm_contri.nro_documento as nro_documento_proveedor',
                // 'registro_pago.fecha_pago',
                'alm_und_medida.abreviatura',
                'log_ord_compra.fecha as fecha_orden',
                DB::raw("(SELECT cont_tp_cambio.venta FROM contabilidad.cont_tp_cambio
                WHERE cont_tp_cambio.fecha = log_ord_compra.fecha
                order by cont_tp_cambio.fecha desc limit 1) AS tipo_cambio"),
                'log_ord_compra.codigo as codigo_oc',
                'log_ord_compra.id_moneda',
                'sis_moneda.simbolo',
                'proveedor.nro_documento',
                'proveedor.razon_social as proveedor_razon_social',
                'presup_par.descripcion as partida_descripcion',
                DB::raw("(SELECT presup_titu.descripcion FROM finanzas.presup_titu
                WHERE presup_titu.codigo = presup_par.cod_padre
                and presup_titu.id_presup = presup_par.id_presup) AS titulo_descripcion"),
                DB::raw("(SELECT registro_pago.fecha_pago FROM tesoreria.registro_pago
                WHERE registro_pago.id_oc = log_ord_compra.id_orden_compra
                limit 1) AS fecha_pago"),
                'requerimiento_pago_estado.descripcion as estado_pago',
                DB::raw("CONCAT(doc_com.serie,'-',doc_com.numero) AS serie_numero"),
                'cont_tp_doc.descripcion as tipo_comprobante',
                'doc_com.fecha_emision as fecha_emision_comprobante'
                // 'adjuntos_logisticos.monto_total as monto_total_comprobante'
                // DB::raw("(SELECT sum(registro_pago.total_pago) FROM tesoreria.registro_pago
                // WHERE registro_pago.id_oc = log_ord_compra.id_orden_compra) AS suma_pago"),
            )
            ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'alm_req.id_empresa')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->join('finanzas.presup_par', 'presup_par.id_partida', '=', 'alm_det_req.partida')
            ->join('finanzas.presup', 'presup.id_presup', '=', 'presup_par.id_presup')
            ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            ->leftJoin('almacen.guia_com_det', function ($join) {
                $join->on('guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden');
                $join->where('guia_com_det.estado', '!=', 7);
            })
            // ->leftjoin('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', '=', 'guia_com_det.id_guia_com_det')
            ->leftJoin('almacen.doc_com_det', function ($join) {
                $join->on('doc_com_det.id_guia_com_det', '=', 'guia_com_det.id_guia_com_det');
                $join->where('doc_com_det.estado', '!=', 7);
            })
            // ->leftjoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'doc_com_det.id_doc')
            ->leftJoin('almacen.doc_com', function ($join) {
                $join->on('doc_com.id_doc_com', '=', 'doc_com_det.id_doc');
                $join->where('doc_com.estado', '!=', 7);
            })
            // ->leftjoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_com.id_tp_doc')
            ->leftJoin('contabilidad.cont_tp_doc', function ($join) {
                $join->on('cont_tp_doc.id_tp_doc', '=', 'doc_com.id_tp_doc');
                $join->where('cont_tp_doc.estado', '=', 1);
            })
            // ->leftJoin('finanzas.presup_titu', function ($join) {
            //     $join->on('presup_titu.codigo', '=', 'presup_par.cod_padre');
            //     $join->where('presup_titu.id_presup', '=', 'presup_par.id_presup');
            // })
            ->join('tesoreria.requerimiento_pago_estado', 'requerimiento_pago_estado.id_requerimiento_pago_estado', '=', 'log_ord_compra.estado_pago')
            ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri as proveedor', 'proveedor.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->join('tesoreria.registro_pago', 'registro_pago.id_oc', '=', 'log_det_ord_compra.id_orden_compra')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            // ->leftjoin('logistica.adjuntos_logisticos', 'adjuntos_logisticos.id_orden', '=', 'log_ord_compra.id_orden_compra')
            // ->leftjoin('logistica.categoria_adjunto', 'categoria_adjunto.id_categoria_adjunto', '=', 'adjuntos_logisticos.categoria_adjunto_id')

            ->where([
                ['presup.id_presup', '=', $id_presupuesto],
                ['log_det_ord_compra.estado', '!=', 7]
            ])
            ->get();

        return $detalle;
    }
    public function obtenerPagosPresupuesto($id_presupuesto)
    {
        $pagos = DB::table('tesoreria.requerimiento_pago_detalle')
            ->select(
                'requerimiento_pago_detalle.*',
                'requerimiento_pago.codigo',
                'adm_contri.razon_social',
                'sis_moneda.simbolo',
                'alm_und_medida.abreviatura',
                'requerimiento_pago_estado.descripcion as estado_pago',
                'presup_par.descripcion as partida_descripcion',
                DB::raw("(SELECT presup_titu.descripcion FROM finanzas.presup_titu
                WHERE presup_titu.codigo = presup_par.cod_padre
                and presup_titu.id_presup = presup_par.id_presup limit 1) AS titulo_descripcion"),
                // DB::raw("(SELECT registro_pago.fecha_pago FROM tesoreria.registro_pago
                // WHERE registro_pago.id_requerimiento_pago = requerimiento_pago.id_requerimiento_pago
                // limit 1) AS fecha_pago"),
                'rrhh_perso.apellido_paterno','rrhh_perso.apellido_materno','rrhh_perso.nombres',
                'rrhh_perso.nro_documento as nro_documento_persona',
                'requerimiento_pago_categoria_adjunto.descripcion as tipo_comprobante',
                'requerimiento_pago_adjunto.nro_comprobante',
                'requerimiento_pago_adjunto.fecha_emision as fecha_emision_comprobante'
                // 'requerimiento_pago_adjunto.monto_total as monto_tota_comprobante'
                // DB::raw("(SELECT sum(registro_pago.total_pago) FROM tesoreria.registro_pago
                // WHERE registro_pago.id_requerimiento_pago = requerimiento_pago.id_requerimiento_pago) AS suma_pago"),
            )
            // ->join('tesoreria.requerimiento_pago', 'requerimiento_pago.id_requerimiento_pago', '=', 'registro_pago.id_requerimiento_pago')
            ->join('tesoreria.requerimiento_pago', 'requerimiento_pago.id_requerimiento_pago', '=', 'requerimiento_pago_detalle.id_requerimiento_pago')
            ->join('tesoreria.requerimiento_pago_estado', 'requerimiento_pago_estado.id_requerimiento_pago_estado', '=', 'requerimiento_pago.id_estado')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'requerimiento_pago.id_empresa')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->join('finanzas.presup_par', 'presup_par.id_partida', '=', 'requerimiento_pago_detalle.id_partida')
            ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'requerimiento_pago.id_moneda')
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'requerimiento_pago.id_trabajador')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            // ->leftJoin('finanzas.presup_titu', function ($join) {
            //     $join->on('presup_titu.codigo', '=', 'presup_par.cod_padre');
            //     $join->where('presup_titu.id_presup', '=', 'presup_par.id_presup');
            // })
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'requerimiento_pago_detalle.id_unidad_medida')
            // ->leftjoin('tesoreria.requerimiento_pago_adjunto', 'requerimiento_pago_adjunto.id_requerimiento_pago', '=', 'requerimiento_pago.id_requerimiento_pago')
            ->leftJoin('tesoreria.requerimiento_pago_adjunto', function ($join) {
                    $join->on('requerimiento_pago_adjunto.id_requerimiento_pago', '=', 'requerimiento_pago.id_requerimiento_pago');
                    $join->where('requerimiento_pago_adjunto.id_estado', '!=', 7);
                })
            ->leftjoin('tesoreria.requerimiento_pago_categoria_adjunto', 'requerimiento_pago_categoria_adjunto.id_requerimiento_pago_categoria_adjunto', '=', 'requerimiento_pago_adjunto.id_categoria_adjunto')
            ->where([
                ['presup_par.id_presup', '=', $id_presupuesto],
                // ['registro_pago.estado', '!=', 7],
                ['requerimiento_pago_detalle.id_estado', '!=', 7],
            ])
            ->get();
        return $pagos;
    }
    public function mostrarGastosPorPresupuesto($id_presupuesto)
    {
        $detalle = $this->obtenerDetallePresupuesto($id_presupuesto);

        $pagos = $this->obtenerPagosPresupuesto($id_presupuesto);

        return response()->json(['req_compras' => $detalle, 'req_pagos' => $pagos]);
    }

    public function cuadroGastosExcel(Request $request)
    {
        $presup = DB::table('finanzas.presup')->where('id_presup', $request->id_presupuesto)->first();
        $detalle = $this->obtenerDetallePresupuesto($request->id_presupuesto);
        $pagos = $this->obtenerPagosPresupuesto($request->id_presupuesto);

        return Excel::download(new CuadroGastosExport(
            $detalle,
            $pagos
        ), $presup->descripcion . '.xlsx');
    }

    public function mostrarPresupuestosProyectos(){
        $lista = DB::table('finanzas.presup')
        ->select('presup.id_presup','presup.descripcion')
        ->where('tp_presup',4) //presupuestos ejecucion proyectos
        ->whereNotNull('id_proyecto')
        ->get();
        return response()->json($lista);
    }

    public function store()
    {
        $codigo = $this->presupNextCodigo(
            request('id_grupo'),
            request('fecha_emision')
        );

        $data = Presupuesto::create([
            'id_empresa' =>  request('id_empresa'),
            'id_grupo' => request('id_grupo'),
            'fecha_emision' => request('fecha_emision'),
            'codigo' => $codigo,
            'descripcion' => strtoupper(request('descripcion')),
            'moneda' => request('moneda'),
            // 'responsable' => request('responsable'),
            // 'unid_program' => request('unid_program'),
            // 'cantidad' => request('cantidad'),
            'fecha_registro' => date('Y-m-d H:i:s'),
            'estado' => 1
        ]);

        return response()->json($data);
    }

    public function update()
    {
        $data = Presupuesto::findOrFail(request('id_presup'));
        $data->update([
            'id_grupo' => request('id_grupo'),
            'fecha_emision' => request('fecha_emision'),
            'descripcion' => strtoupper(request('descripcion')),
            'moneda' => request('moneda')
        ]);
        return response()->json($data);
    }

    public function presupNextCodigo($id_grupo, $fecha)
    {
        $yyyy = date('Y', strtotime($fecha));
        $anio = date('y', strtotime($fecha));

        $grupo = Grupo::findOrFail($id_grupo);

        $correlativo = Presupuesto::where([
            ['id_grupo', '=', $id_grupo],
            ['estado', '=', 1]
        ])
            ->whereYear('fecha_emision', '=', $yyyy)
            ->count();

        $next = $this->leftZero(3, $correlativo + 1);

        return 'P' . $grupo->abreviatura . '-' . $anio . '-' . $next;
    }

    public function leftZero($lenght, $number)
    {
        $nLen = strlen($number);
        $zeros = '';
        for ($i = 0; $i < ($lenght - $nLen); $i++) {
            $zeros = $zeros . '0';
        }
        return $zeros . $number;
    }

    function getAllGrupos()
    {
        $grupos = DB::table('configuracion.usuario_grupo')
            ->join('configuracion.sis_grupo', 'sis_grupo.id_grupo', '=', 'usuario_grupo.id_grupo')
            ->where('usuario_grupo.id_usuario', Auth::user()->id_usuario)
            ->select('sis_grupo.*')
            ->distinct('id_grupo')->get();

        $array_grupos = [];
        foreach ($grupos as $grupo) {
            $array_grupos[] = [$grupo->id_grupo];
        }
        return $array_grupos;
    }


    public function mostrarPresupuestos($idGrupoList, $id_proyecto = null)
    {
        $presupuestos = [];
        $titulos = [];
        $partidas = [];
        $grupos = $this->getAllGrupos();

        if ($id_proyecto != null || $id_proyecto != '') {

            $presupuestos = DB::table('finanzas.presup')
                ->select('presup.*')
                ->where([
                    ['id_proyecto', '=', $id_proyecto],
                    ['estado', '=', 1],
                    ['tp_presup', '=', 4]
                ])->get();
        } else {

            $presupuestos = DB::table('finanzas.presup')
                ->select('presup.*')
                ->where([
                    ['id_proyecto', '=', null],
                    ['estado', '=', 1],
                    ['tp_presup', '=', 2]
                ])->whereIn('id_grupo', $grupos)->get();
        }


        foreach ($presupuestos as $p) {
            $resTitulos = DB::table('finanzas.presup_titu')
                ->select('presup_titu.*')
                ->where([
                    ['presup_titu.id_presup', '=', $p->id_presup],
                    ['presup_titu.estado', '=', 1]
                ])
                ->orderBy('presup_titu.codigo')
                ->get();

            foreach ($resTitulos as $titulo) {
                array_push($titulos, $titulo);
            }

            $resPartidas = DB::table('finanzas.presup_par')
                ->select('presup_par.*')
                ->where([
                    ['presup_par.id_presup', '=', $p->id_presup],
                    ['presup_par.estado', '=', 1]
                ])
                ->orderBy('presup_par.codigo')
                ->get();

            foreach ($resPartidas as $partida) {
                array_push($partidas, $partida);
            }
        }



        return response()->json(['presupuesto' => $presupuestos, 'titulos' => $titulos, 'partidas' => $partidas]);
    }
}
