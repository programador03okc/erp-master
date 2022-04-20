<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use App\Http\Controllers\AlmacenController as GenericoAlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Almacen\Almacen;
use App\Models\almacen\Reserva;
use App\Models\Almacen\Transferencia;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

//date_default_timezone_set('America/Lima');

class TransferenciaController extends Controller
{
    public function __construct()
    {
        // session_start();
    }
    function view_listar_transferencias()
    {
        $clasificaciones = GenericoAlmacenController::mostrar_guia_clas_cbo();
        $almacenes = $this->almacenesPorUsuario();
        $todos_almacenes = Almacen::where('estado', 1)->orderBy('codigo')->get();
        $usuarios = GenericoAlmacenController::select_usuarios();
        $motivos_anu = GenericoAlmacenController::select_motivo_anu();
        $nro_pendientes = $this->nroPendientes();
        $nro_por_enviar = $this->nroPorEnviar();
        $nro_por_recibir = $this->nroPorRecibir();

        return view(
            'almacen/transferencias/listarTransferencias',
            compact(
                'clasificaciones',
                'almacenes',
                'usuarios',
                'motivos_anu',
                'nro_pendientes',
                'todos_almacenes',
                'nro_por_enviar',
                'nro_por_recibir'
            )
        );
    }

    public function getAlmacenesPorEmpresa($id_almacen_origen)
    {
        $almacenes = [];

        if ($id_almacen_origen !== null) {
            $almacen_origen = Almacen::where('alm_almacen.id_almacen', $id_almacen_origen)
                ->select('sis_sede.id_empresa')
                ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
                ->first();
            $almacenes = Almacen::join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
                ->select('alm_almacen.*')
                ->where([
                    ['alm_almacen.estado', '=', 1],
                    ['sis_sede.id_empresa', '=', $almacen_origen->id_empresa]
                ])
                ->orderBy('alm_almacen.codigo')->get();
        }

        return response()->json($almacenes);
    }

    public function nroPendientes()
    {
        $array_almacen = $this->almacenesPorUsuarioArray();
        $pendientes = DB::table('almacen.alm_reserva')
            ->select('alm_req.id_requerimiento')
            ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'alm_reserva.id_detalle_requerimiento')
            ->join('almacen.alm_req', function ($join) {
                $join->on('alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento');
                $join->on('alm_req.id_almacen', '!=', 'alm_reserva.id_almacen_reserva');
                $join->whereNotNull('alm_reserva.id_almacen_reserva');
            })
            ->whereIn('alm_reserva.id_almacen_reserva', $array_almacen)
            ->where([
                ['alm_det_req.estado', '!=', 7],
                ['alm_reserva.estado', '=', 1],
                ['alm_reserva.stock_comprometido', '>', 0],
                ['alm_req.estado', '!=', 7]
            ])
            ->distinct()->get();
        return $pendientes->count();
    }

    public function nroPorEnviar()
    {
        $array_almacen = $this->almacenesPorUsuarioArray();
        $nro_por_enviar = DB::table('almacen.trans')
            ->whereIn('trans.id_almacen_origen', $array_almacen)
            ->where([['trans.estado', '=', 1]])
            ->count();
        return $nro_por_enviar;
    }

    public function nroPorRecibir()
    {
        $array_almacen = $this->almacenesPorUsuarioArray();
        $nro_por_recibir = DB::table('almacen.trans')
            ->whereIn('trans.id_almacen_destino', $array_almacen)
            ->where([['trans.estado', '=', 17]])
            ->count();
        return $nro_por_recibir;
    }

    public function listarTransferenciasPorEnviar(Request $request)
    {
        if ($request->id_almacen_origen == '0') {
            $array_almacen = $this->almacenesPorUsuarioArray();
        } else {
            $array_almacen[] = [$request->id_almacen_origen];
        }
        $lista = DB::table('almacen.trans')
            ->select(
                'trans.*',
                'alm_req.codigo as cod_req',
                'alm_req.concepto as concepto_req',
                DB::raw("CONCAT(guia_ven.serie, '-', guia_ven.numero) as guia_ven"),
                'guia_ven.id_guia_ven',
                'mov_alm.id_mov_alm as id_salida',
                'sede_origen.id_empresa as id_empresa_origen',
                'sede_origen.id_sede as id_sede_origen',
                'sede_origen.descripcion as sede_origen_descripcion',
                'sede_destino.id_empresa as id_empresa_destino',
                'sede_destino.id_sede as id_sede_destino',
                'sede_destino.descripcion as sede_destino_descripcion',
                'origen.descripcion as alm_origen_descripcion',
                'destino.descripcion as alm_destino_descripcion',
                'origen.ubicacion as alm_origen_direccion',
                'destino.ubicacion as alm_destino_direccion',
                'sis_usua.nombre_corto',
                'adm_estado_doc.bootstrap_color',
                'adm_estado_doc.estado_doc',
            )
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'trans.id_requerimiento')
            // ->leftjoin('administracion.sis_sede as sede_solicita', 'sede_solicita.id_sede', '=', 'alm_req.id_sede')
            ->join('almacen.alm_almacen as origen', 'origen.id_almacen', '=', 'trans.id_almacen_origen')
            ->join('administracion.sis_sede as sede_origen', 'sede_origen.id_sede', '=', 'origen.id_sede')
            ->join('almacen.alm_almacen as destino', 'destino.id_almacen', '=', 'trans.id_almacen_destino')
            ->join('administracion.sis_sede as sede_destino', 'sede_destino.id_sede', '=', 'destino.id_sede')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'trans.responsable_origen')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'trans.estado')
            ->leftJoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'trans.id_guia_ven')
            ->leftJoin('almacen.mov_alm', 'mov_alm.id_guia_ven', '=', 'trans.id_guia_ven')
            ->whereIn('trans.id_almacen_origen', $array_almacen)
            ->whereIn('trans.estado', [1, 17])
            ->get();

        return datatables($lista)->toJson();
        // $output['data'] = $lista;
        // return response()->json($output);
    }

    public function listarTransferenciasPorRecibir(Request $request)
    {
        if ($request->id_almacen_destino == '0') {
            $array_almacen = $this->almacenesPorUsuarioArray();
        } else {
            $array_almacen[] = [$request->id_almacen_destino];
        }
        $data = Transferencia::select(
            'trans.id_transferencia',
            'trans.codigo',
            'trans.id_guia_ven',
            'guia_ven.fecha_emision as fecha_guia',
            DB::raw("CONCAT(guia_ven.serie, '-', guia_ven.numero) as guia_ven"),
            'trans.id_almacen_destino',
            'trans.id_almacen_origen',
            'alm_origen.descripcion as alm_origen_descripcion',
            'alm_destino.descripcion as alm_destino_descripcion',
            'sede_origen.id_empresa as id_empresa_origen',
            'sede_destino.id_empresa as id_empresa_destino',
            'usu_origen.nombre_corto as nombre_origen',
            'usu_destino.nombre_corto as nombre_destino',
            'adm_estado_doc.estado_doc',
            'adm_estado_doc.bootstrap_color',
            'mov_alm.id_mov_alm as id_salida'
        )
            ->leftJoin('almacen.mov_alm', 'mov_alm.id_guia_ven', '=', 'trans.id_guia_ven')
            ->join('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'trans.id_guia_ven')
            ->join('almacen.alm_almacen as alm_origen', 'alm_origen.id_almacen', '=', 'trans.id_almacen_origen')
            ->join('almacen.alm_almacen as alm_destino', 'alm_destino.id_almacen', '=', 'trans.id_almacen_destino')
            ->join('administracion.sis_sede as sede_origen', 'sede_origen.id_sede', '=', 'alm_origen.id_sede')
            ->join('administracion.sis_sede as sede_destino', 'sede_destino.id_sede', '=', 'alm_destino.id_sede')
            ->leftJoin('configuracion.sis_usua as usu_origen', 'usu_origen.id_usuario', '=', 'trans.responsable_origen')
            ->leftJoin('configuracion.sis_usua as usu_destino', 'usu_destino.id_usuario', '=', 'trans.responsable_destino')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'trans.estado')
            ->whereIn('trans.id_almacen_destino', $array_almacen)
            ->where([
                ['trans.estado', '!=', 7],
                ['trans.estado', '!=', 14]
            ])
            ->distinct()->get();

        return datatables($data)->toJson();
        // $output['data'] = $data;
        // return response()->json($output);
    }

    public function listarTransferenciasRecibidas(Request $request)
    {
        if ($request->id_almacen_destino_recibida == '0') {
            $array_almacen = $this->almacenesPorUsuarioArray();
        } else {
            $array_almacen[] = [$request->id_almacen_destino_recibida];
        }
        //USAR CONCAT
        $data = DB::table('almacen.trans_detalle')
            ->select(
                'trans.*',
                'guia_ven.fecha_emision as fecha_guia',
                DB::raw("CONCAT(guia_ven.serie,'-',guia_ven.numero) as guia_ven"),
                DB::raw("CONCAT(guia_com.serie,'-',guia_com.numero) as guia_com"),
                DB::raw("CONCAT(doc_ven.serie,'-',doc_ven.numero) as doc_ven"),
                DB::raw("CONCAT(doc_com.serie,'-',doc_com.numero) as doc_com"),
                'doc_com.id_doc_com',
                'alm_origen.descripcion as alm_origen_descripcion',
                'alm_destino.descripcion as alm_destino_descripcion',
                'sede_origen.id_empresa as id_empresa_origen',
                'sede_destino.id_empresa as id_empresa_destino',
                // 'usu_origen.nombre_corto as nombre_origen',
                // 'usu_destino.nombre_corto as nombre_destino',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'ingreso.id_mov_alm as id_ingreso',
                'salida.id_mov_alm as id_salida',
                'alm_req.codigo as codigo_req',
                'alm_req.concepto as concepto_req',
                'doc_ven.id_doc_ven'
            )
            ->join('almacen.trans', 'trans.id_transferencia', '=', 'trans_detalle.id_transferencia')
            ->join('almacen.mov_alm as ingreso', 'ingreso.id_guia_com', '=', 'trans.id_guia_com')
            ->join('almacen.mov_alm as salida', 'salida.id_guia_ven', '=', 'trans.id_guia_ven')
            ->join('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'trans.id_guia_ven')
            ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'trans.id_guia_com')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'trans.id_requerimiento')
            // ->leftJoin('almacen.alm_req', function ($join) {
            //     $join->on('alm_req.id_requerimiento', '=', 'trans.id_requerimiento');
            //     $join->where('alm_req.estado', '!=', 7);
            // })
            ->join('almacen.alm_almacen as alm_origen', 'alm_origen.id_almacen', '=', 'trans.id_almacen_origen')
            ->join('almacen.alm_almacen as alm_destino', 'alm_destino.id_almacen', '=', 'trans.id_almacen_destino')
            ->join('administracion.sis_sede as sede_origen', 'sede_origen.id_sede', '=', 'alm_origen.id_sede')
            ->join('administracion.sis_sede as sede_destino', 'sede_destino.id_sede', '=', 'alm_destino.id_sede')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'trans.estado')
            ->join('almacen.guia_ven_det', 'guia_ven_det.id_trans_det', '=', 'trans_detalle.id_trans_detalle')
            ->leftJoin('almacen.doc_ven_det', 'doc_ven_det.id_guia_ven_det', '=', 'guia_ven_det.id_guia_ven_det')
            ->leftJoin('almacen.doc_ven', 'doc_ven.id_doc_ven', '=', 'doc_ven_det.id_doc')
            ->join('almacen.guia_com_det', 'guia_com_det.id_trans_detalle', '=', 'trans_detalle.id_trans_detalle')
            ->leftJoin('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', '=', 'guia_com_det.id_guia_com_det')
            ->leftJoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'doc_com_det.id_doc')
            ->whereIn('trans.id_almacen_destino', $array_almacen)
            ->where([
                ['trans.estado', '=', 14],
                ['trans_detalle.estado', '!=', 7],
                ['guia_ven_det.estado', '!=', 7],
                ['guia_com_det.estado', '!=', 7],
            ])
            ->distinct()
            ->get();

        return datatables($data)->toJson();
        // $output['data'] = $data;
        // return response()->json($output);
    }

    public function listarTransferenciaDetalle($id_transferencia)
    {
        $detalle = DB::table('almacen.trans_detalle')
            ->select(
                'trans_detalle.*',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.series',
                'alm_und_medida.abreviatura',
                'alm_prod.part_number',
                'guia_com.serie',
                'guia_com.numero',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color'
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'trans_detalle.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'trans_detalle.estado')
            ->leftJoin('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'trans_detalle.id_guia_com_det')
            ->leftJoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->where([
                ['trans_detalle.id_transferencia', '=', $id_transferencia],
                ['trans_detalle.estado', '!=', 7]
            ])
            ->get();
        return response()->json($detalle);
    }

    public function listarGuiaTransferenciaDetalle($id_guia_ven)
    {
        $detalle = DB::table('almacen.guia_ven_det')
            ->select(
                'guia_ven_det.*',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_und_medida.abreviatura',
                'alm_prod.part_number',
                'alm_prod.series',
                'trans.codigo as codigo_trans',
                'alm_req.codigo as codigo_req',
                'alm_req.concepto',
                'trans_detalle.id_trans_detalle',
                'trans_detalle.id_requerimiento_detalle',
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_ven_det.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftJoin('almacen.trans_detalle', function ($join) {
                $join->on('trans_detalle.id_trans_detalle', '=', 'guia_ven_det.id_trans_det');
                $join->where('trans_detalle.estado', '!=', 7);
            })
            ->join('almacen.trans', 'trans.id_transferencia', '=', 'trans_detalle.id_transferencia')
            ->leftJoin('almacen.alm_det_req', function ($join) {
                $join->on('alm_det_req.id_detalle_requerimiento', '=', 'trans_detalle.id_requerimiento_detalle');
                $join->where('alm_det_req.estado', '!=', 7);
            })
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->where([
                ['guia_ven_det.id_guia_ven', '=', $id_guia_ven],
                ['guia_ven_det.estado', '!=', 7]
            ])
            ->get();

        $motivos_perdida = DB::table('almacen.trans_motivo_perdida')
            ->where('estado', 1)->get();

        $lista_detalle = [];

        foreach ($detalle as $det) {
            $series = DB::table('almacen.alm_prod_serie')
                ->where('id_guia_ven_det', $det->id_guia_ven_det)
                ->get();

            array_push($lista_detalle, [
                'id_guia_ven_det' => $det->id_guia_ven_det,
                'id_trans_detalle' => $det->id_trans_detalle,
                'id_detalle_requerimiento' => $det->id_requerimiento_detalle,
                'codigo_trans' => $det->codigo_trans,
                'codigo_req' => $det->codigo_req,
                'concepto' => $det->concepto,
                'codigo' => $det->codigo,
                'part_number' => $det->part_number,
                'descripcion' => $det->descripcion,
                'cantidad' => $det->cantidad,
                'abreviatura' => $det->abreviatura,
                'series' => $series
            ]);
        }
        return response()->json(['detalleTransferencia' => $lista_detalle, 'motivos' => $motivos_perdida]);
    }

    public function anular_transferencia($id_transferencia)
    {
        try {
            DB::beginTransaction();

            $trans = DB::table('almacen.trans')
                ->where('id_transferencia', $id_transferencia)
                ->update(['estado' => 7]);

            $trans = DB::table('almacen.trans_detalle')
                ->where('id_transferencia', $id_transferencia)
                ->update(['estado' => 7]);

            $detalle = DB::table('almacen.trans_detalle')
                ->where('id_transferencia', $id_transferencia)
                ->get();

            foreach ($detalle as $det) {
                DB::table('almacen.alm_reserva')
                    ->where('id_trans_detalle', $det->id_trans_detalle)
                    ->update([
                        'estado' => 1,
                        'id_trans_detalle' => null
                    ]);
            }

            DB::commit();
            return response()->json(['nroPorEnviar' => $this->nroPorEnviar()]);
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            return response()->json("Ha ocurrido un problema. " . $e . ". Intente nuevamente.");
        }
    }

    public function anularTransferenciaIngreso(Request $request)
    {
        try {
            DB::beginTransaction();

            $ing = DB::table('almacen.mov_alm')
                ->where([['mov_alm.id_mov_alm', '=', $request->id_mov_alm]]) //ingreso
                ->first();

            $msj = '';
            //si el ingreso no esta revisado
            if ($ing->revisado == 0) {

                $transferencias = DB::table('almacen.trans')
                    ->select(
                        'trans.id_transferencia',
                        'trans.id_requerimiento',
                        'trans.id_guia_com',
                        'trans.id_almacen_origen',
                        'orden_despacho.id_od'
                    )
                    ->leftJoin('almacen.orden_despacho', function ($join) {
                        $join->on('orden_despacho.id_requerimiento', '=', 'trans.id_requerimiento');
                        $join->where('orden_despacho.estado', '=', 1);
                    })
                    ->where([['trans.id_guia_com', '=', $ing->id_guia_com], ['trans.estado', '!=', 7]])
                    ->get();

                $rollback = 0;
                foreach ($transferencias as $t) {
                    if ($t->id_od !== null) {
                        $rollback++;
                    }
                }

                if ($rollback == 0) {

                    $id_usuario = Auth::user()->id_usuario;
                    //Anula ingreso
                    DB::table('almacen.mov_alm')
                        ->where('id_mov_alm', $request->id_mov_alm)
                        ->update(['estado' => 7]);
                    //Anula el detalle
                    DB::table('almacen.mov_alm_det')
                        ->where('id_mov_alm', $request->id_mov_alm)
                        ->update(['estado' => 7]);
                    //Agrega motivo anulacion a la guia
                    DB::table('almacen.guia_com_obs')->insert(
                        [
                            'id_guia_com' => $request->id_guia_com,
                            'observacion' => $request->observacion,
                            'registrado_por' => $id_usuario,
                            'id_motivo_anu' => $request->id_motivo_obs,
                            'fecha_registro' => new Carbon() //date('Y-m-d H:i:s')
                        ]
                    );
                    //Anula la Guia
                    DB::table('almacen.guia_com')
                        ->where('id_guia', $request->id_guia_com)
                        ->update(['estado' => 7]);
                    //Anula la Guia Detalle
                    DB::table('almacen.guia_com_det')
                        ->where('id_guia_com', $request->id_guia_com)
                        ->update(['estado' => 7]);

                    $detalle = DB::table('almacen.guia_com_det')
                        ->select('guia_com_det.id_guia_com_det')
                        ->where('id_guia_com', $request->id_guia_com)
                        ->get();

                    foreach ($detalle as $det) {
                        DB::table('almacen.alm_prod_serie')
                            ->where('id_guia_com_det', $det->id_guia_com_det)
                            ->update([
                                'id_guia_com_det' => null,
                                'estado' => 7
                            ]);
                        DB::table('almacen.alm_reserva')
                            ->where('id_guia_com_det', $det->id_guia_com_det)
                            ->update(['estado' => 7]);
                    }
                    //Transferencia cambia estado elaborado
                    foreach ($transferencias as $tra) {
                        DB::table('almacen.trans')
                            ->where('id_transferencia', $tra->id_transferencia)
                            ->update([
                                'estado' => 17,
                                'id_guia_com' => null
                            ]);
                        //Transferencia Detalle cambia estado elaborado
                        DB::table('almacen.trans_detalle')
                            ->where('id_transferencia', $tra->id_transferencia)
                            ->update(['estado' => 17]);
                        //Requerimiento regresa a enviado
                        // DB::table('almacen.alm_req')
                        //     ->where('id_requerimiento', $tra->id_requerimiento)
                        //     ->update(['estado' => 17]); //Enviado

                        // DB::table('almacen.alm_det_req')
                        //     ->where('id_requerimiento', $tra->id_requerimiento)
                        //     ->update(['estado' => 17]); //Enviado
                        //Agrega accion en requerimiento
                        DB::table('almacen.alm_req_obs')
                            ->insert([
                                'id_requerimiento' => $tra->id_requerimiento,
                                'accion' => 'INGRESO ANULADO',
                                'descripcion' => 'Ingreso por Transferencia Anulado. ' . $request->id_motivo_obs . '. Requerimiento regresa a Enviado.',
                                'id_usuario' => Auth::user()->id_usuario,
                                'fecha_registro' => date('Y-m-d H:i:s') //Carbon
                            ]);
                    }
                } else {
                    $msj = 'No es posible anular. Ya se generó una Orden de Despacho.';
                }
            } else {
                $msj = 'No es posible anular. El ingreso ya fue revisado por el Jefe de Almacén.';
            }

            DB::commit();

            return response()->json($msj);
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            return response()->json('Hubo un problema  al procesar los datos, por favor intente de nuevo. Mensaje de error: ' . $e->getMessage());
            // return response()->json($e);
        }
    }

    public function anularTransferenciaSalida(Request $request)
    {
        try {
            DB::beginTransaction();

            $sal = DB::table('almacen.mov_alm')
                ->select('mov_alm.*')
                ->where([['mov_alm.id_mov_alm', '=', $request->id_salida]]) //salida
                ->first();

            $msj = '';
            $tipo = '';
            //si el salida no esta revisado
            if ($sal->revisado == 0) {

                $transferencias = DB::table('almacen.trans')
                    ->select(
                        'trans.id_transferencia',
                        'trans.id_requerimiento',
                        'trans.id_guia_com',
                        'trans.id_almacen_origen'
                    )
                    ->where([
                        ['id_guia_ven', '=', $request->id_guia_ven],
                        ['estado', '!=', 7]
                    ])
                    ->get();

                $rollback = 0;
                foreach ($transferencias as $t) {
                    if ($t->id_guia_com !== null) {
                        $rollback++;
                    }
                }

                if ($rollback == 0) {
                    $id_usuario = Auth::user()->id_usuario;
                    //Anula salida
                    DB::table('almacen.mov_alm')
                        ->where('id_mov_alm', $request->id_salida)
                        ->update(['estado' => 7]);
                    //Anula el detalle
                    DB::table('almacen.mov_alm_det')
                        ->where('id_mov_alm', $request->id_salida)
                        ->update(['estado' => 7]);
                    //Agrega motivo anulacion a la guia
                    DB::table('almacen.guia_ven_obs')->insert(
                        [
                            'id_guia_ven' => $request->id_guia_ven,
                            'observacion' => $request->observacion_guia_ven,
                            'registrado_por' => $id_usuario,
                            'id_motivo_anu' => $request->id_motivo_obs_ven,
                            'fecha_registro' => date('Y-m-d H:i:s')
                        ]
                    );
                    //Anula la Guia
                    DB::table('almacen.guia_ven')
                        ->where('id_guia_ven', $request->id_guia_ven)
                        ->update(['estado' => 7]);
                    //Anula la Guia Detalle
                    DB::table('almacen.guia_ven_det')
                        ->where('id_guia_ven', $request->id_guia_ven)
                        ->update(['estado' => 7]);

                    $detalle = DB::table('almacen.guia_ven_det')
                        ->select('guia_ven_det.id_guia_ven_det')
                        ->where('id_guia_ven', $request->id_guia_ven)
                        ->get();
                    //elimina relacion de guia ven en las series
                    foreach ($detalle as $det) {
                        DB::table('almacen.alm_prod_serie')
                            ->where('id_guia_ven_det', '=', $det->id_guia_ven_det)
                            ->update(['id_guia_ven_det' => null]);
                    }
                    //Transferencia cambia estado elaborado
                    foreach ($transferencias as $tra) {
                        DB::table('almacen.trans')
                            ->where('id_transferencia', $tra->id_transferencia)
                            ->update([
                                'estado' => 1,
                                'id_guia_ven' => null
                            ]);
                        //Transferencia Detalle cambia estado elaborado
                        DB::table('almacen.trans_detalle')
                            ->where('id_transferencia', $tra->id_transferencia)
                            ->update(['estado' => 1]);

                        $transDetalle = DB::table('almacen.trans_detalle')
                            ->where('id_transferencia', $tra->id_transferencia)
                            ->get();

                        foreach ($transDetalle as $tdet) {
                            DB::table('almacen.alm_reserva')
                                ->where('id_trans_detalle', $tdet->id_trans_detalle)
                                ->update(['estado' => 17]);
                        }
                        //Requerimiento regresa a Reservado
                        // DB::table('almacen.alm_req')
                        //     ->where('id_requerimiento', $tra->id_requerimiento)
                        //     ->update(['estado' => 19]); //Reservado

                        // DB::table('almacen.alm_det_req')
                        //     ->where('id_requerimiento', $tra->id_requerimiento)
                        //     ->update([
                        //         'estado' => 19,
                        //         'id_almacen_reserva' => $tra->id_almacen_origen
                        //     ]); //Reservado

                        DB::table('almacen.alm_req_obs')
                            ->insert([
                                'id_requerimiento'  => $tra->id_requerimiento,
                                'accion'            => 'ANULA SALIDA TRANSFERENCIA',
                                'descripcion'       => 'Se anula la salida por transferencia. ' . $request->observacion_guia_ven,
                                'id_usuario'        => $id_usuario,
                                'fecha_registro'    => new Carbon()
                            ]);
                    }
                    $msj = 'Salida anulada con éxito.';
                    $tipo = 'success';
                } else {
                    $msj = 'Ya se generó el Ingreso en el Almacén Destino.';
                    $tipo = 'warning';
                }
            } else {
                $msj = 'La salida ya fue revisado por el Jefe de Almacén.';
                $tipo = 'warning';
            }

            DB::commit();

            return response()->json([
                'tipo' => $tipo,
                'mensaje' => $msj,
                'nroPorEnviar' => $this->nroPorEnviar(), 200
            ]);
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            //MENSAJE
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al anular la salida. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    public function ingreso_transferencia($id_guia_com)
    {
        $data = DB::table('almacen.mov_alm')
            ->where('id_guia_com', $id_guia_com)->get();
        return response()->json($data);
    }

    public function guardarIngresoTransferencia(Request $request)
    {
        try {
            DB::beginTransaction();

            $usuario = Auth::user();
            $fecha = date('Y-m-d H:i:s');

            DB::table('almacen.trans')
                ->where('id_guia_ven', $request->id_guia_ven)
                ->update(['responsable_destino' => $request->responsable_destino]);

            $guia_ven = DB::table('almacen.guia_ven')
                ->select(
                    'guia_ven.*',
                    'adm_empresa.id_contribuyente as empresa_contribuyente',
                    'log_prove.id_proveedor as empresa_proveedor',
                    'adm_empresa.id_empresa'
                )
                ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'guia_ven.id_almacen')
                ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
                ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                ->leftJoin('logistica.log_prove', 'log_prove.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
                ->where('guia_ven.id_guia_ven', $request->id_guia_ven)
                ->first();

            $id_proveedor = null;

            if ($guia_ven->empresa_proveedor !== null) {
                $id_proveedor = $guia_ven->empresa_proveedor;
            } else if ($guia_ven->empresa_contribuyente !== null) {
                $id_proveedor = DB::table('logistica.log_prove')->insertGetId(
                    [
                        'id_contribuyente' => $guia_ven->empresa_contribuyente,
                        'estado' => 1,
                        'fecha_registro' => $fecha,
                    ],
                    'id_proveedor'
                );
            }

            $destino_emp = DB::table('almacen.alm_almacen')
                ->select('adm_empresa.id_empresa')
                ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
                ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                ->where('id_almacen', $request->id_almacen_destino)
                ->first();

            $ope = ($destino_emp->id_empresa == $guia_ven->id_empresa ? 21 : 2);

            $id_guia_com = DB::table('almacen.guia_com')->insertGetId(
                [
                    'id_tp_doc_almacen' => 1, //Guia Compra
                    'serie' => $guia_ven->serie,
                    'numero' => $guia_ven->numero,
                    'id_proveedor' => ($id_proveedor !== null ? $id_proveedor : null),
                    'fecha_emision' => $guia_ven->fecha_emision,
                    'fecha_almacen' => $request->fecha_almacen,
                    'id_almacen' => $request->id_almacen_destino,
                    'comentario' => $request->comentario_recibir,
                    // 'id_motivo' => $guia_ven->id_motivo,
                    'id_guia_clas' => 1,
                    'id_operacion' => $ope,
                    'id_transferencia' => $request->id_transferencia,
                    // 'punto_partida' => $guia_ven->punto_partida,
                    // 'punto_llegada' => $guia_ven->punto_llegada,
                    // 'transportista' => $guia_ven->transportista,
                    // 'fecha_traslado' => $guia_ven->fecha_traslado,
                    // 'tra_serie' => $guia_ven->tra_serie,
                    // 'tra_numero' => $guia_ven->tra_numero,
                    // 'placa' => $guia_ven->placa,
                    'usuario' => $request->responsable_destino,
                    'registrado_por' => $usuario->id_usuario,
                    'estado' => 9,
                    'fecha_registro' => $fecha
                ],
                'id_guia'
            );

            $codigo = GenericoAlmacenController::nextMovimiento(
                1,
                $request->fecha_almacen,
                $request->id_almacen_destino
            );

            $id_ingreso = DB::table('almacen.mov_alm')->insertGetId(
                [
                    'id_almacen' => $request->id_almacen_destino,
                    'id_tp_mov' => 1, //Ingresos
                    'codigo' => $codigo,
                    'fecha_emision' => $request->fecha_almacen,
                    'id_guia_com' => $id_guia_com,
                    'id_operacion' => $ope, //entrada por transferencia entre almacenes
                    'id_transferencia' => $request->id_transferencia,
                    'revisado' => 0,
                    'usuario' => $usuario->id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                'id_mov_alm'
            );

            $detalle = json_decode($request->detalle);

            foreach ($detalle as $d) {

                $det = DB::table('almacen.guia_ven_det')
                    ->select('guia_ven_det.*', 'mov_alm_det.valorizacion', 'mov_alm_det.cantidad as cant_mov')
                    ->leftJoin('almacen.mov_alm_det', function ($join) {
                        $join->on('mov_alm_det.id_guia_ven_det', '=', 'guia_ven_det.id_guia_ven_det');
                        $join->where('mov_alm_det.estado', '!=', 7);
                    })
                    ->where([['guia_ven_det.id_guia_ven_det', '=', $d->id_guia_ven_det]])
                    ->first();

                if ($det !== null) {

                    $unitario = (floatval($det->cant_mov) > 0 ? (floatval($det->valorizacion) / floatval($det->cant_mov)) : 0);

                    $id_guia_com_det = DB::table('almacen.guia_com_det')->insertGetId(
                        [
                            'id_guia_com' => $id_guia_com,
                            'id_producto' => $det->id_producto,
                            'cantidad' => $d->cantidad_recibida,
                            'unitario' => $unitario,
                            'total' => ($unitario * $d->cantidad_recibida),
                            'id_unid_med' => $det->id_unid_med,
                            'id_guia_ven_det' => $d->id_guia_ven_det,
                            'id_trans_detalle' => ($det->id_trans_det !== null ? $det->id_trans_det : null),
                            'usuario' => $usuario->id_usuario,
                            'estado' => 1,
                            'fecha_registro' => $fecha
                        ],
                        'id_guia_com_det'
                    );

                    $series = DB::table('almacen.alm_prod_serie')
                        ->select('alm_prod_serie.serie')
                        ->where([
                            ['alm_prod_serie.id_guia_ven_det', '=', $d->id_guia_ven_det],
                            ['alm_prod_serie.estado', '!=', 7]
                        ])
                        ->get();

                    foreach ($series as $s) {
                        //Inserta serie
                        DB::table('almacen.alm_prod_serie')->insert([
                            'id_prod' => $det->id_producto,
                            'serie' => $s->serie,
                            'estado' => 1,
                            'fecha_registro' => date('Y-m-d H:i:s'),
                            'id_guia_com_det' => $id_guia_com_det,
                            'id_almacen' => $request->id_almacen_destino
                        ]);
                    }

                    if ($d->observacion !== '' && $d->observacion !== null) {
                        DB::table('almacen.guia_com_det_obs')->insertGetId(
                            [
                                'id_guia_com_det' => $id_guia_com_det,
                                'observacion' => $d->observacion,
                                'registrado_por' => $usuario->id_usuario,
                                'fecha_registro' => $fecha,
                            ],
                            'id_obs'
                        );
                    }
                    //guarda ingreso detalle
                    DB::table('almacen.mov_alm_det')->insertGetId(
                        [
                            'id_mov_alm' => $id_ingreso,
                            'id_producto' => $det->id_producto,
                            'cantidad' => $d->cantidad_recibida,
                            'valorizacion' => $unitario * floatval($d->cantidad_recibida),
                            'usuario' => $usuario->id_usuario,
                            'id_guia_com_det' => $id_guia_com_det,
                            'estado' => 1,
                            'fecha_registro' => $fecha,
                        ],
                        'id_mov_alm_det'
                    );
                    //Actualizo los saldos del producto
                    OrdenesPendientesController::actualiza_prod_ubi($det->id_producto, $request->id_almacen_destino);

                    DB::table('almacen.trans_detalle')
                        ->where('id_trans_detalle', $d->id_trans_detalle)
                        ->update(['estado' => 14]); //recepcionada

                    if ($d->id_detalle_requerimiento !== null) {

                        DB::table('almacen.alm_det_req')
                            ->where('id_detalle_requerimiento', $d->id_detalle_requerimiento)
                            ->update([
                                'estado' => 28, //en almacen total
                            ]);

                        DB::table('almacen.alm_reserva')
                            ->insert([
                                'codigo' => Reserva::crearCodigo($request->id_almacen_destino),
                                // 'codigo' => 'prueba',
                                'id_producto' => $det->id_producto,
                                'stock_comprometido' => $d->cantidad_recibida,
                                'id_almacen_reserva' => $request->id_almacen_destino,
                                'id_detalle_requerimiento' =>  $d->id_detalle_requerimiento,
                                'id_trans_detalle' => $d->id_trans_detalle,
                                'id_guia_com_det' => $id_guia_com_det,
                                'estado' => 1,
                                'usuario_registro' => $usuario->id_usuario,
                                'fecha_registro' => date('Y-m-d H:i:s'),
                            ]);
                    }
                }
            }

            $reqs = DB::table('almacen.trans')
                ->select('trans.id_transferencia', 'trans.id_requerimiento')
                ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'trans.id_requerimiento')
                ->where([
                    ['trans.id_guia_ven', '=', $request->id_guia_ven],
                    ['trans.estado', '=', 17] //enviado
                ])
                ->get();

            foreach ($reqs as $r) {
                DB::table('almacen.trans')
                    ->where('id_transferencia', $r->id_transferencia)
                    ->update([
                        'estado' => 14, //Recibido
                        'id_guia_com' => $id_guia_com
                    ]);

                if ($r->id_requerimiento !== null) {

                    $count_recibido = DB::table('almacen.alm_det_req')
                        ->where([
                            ['id_requerimiento', '=', $r->id_requerimiento],
                            ['tiene_transformacion', '=', false],
                            ['estado', '=', 28] //en almacen total
                        ])
                        ->count();

                    $count_todo = DB::table('almacen.alm_det_req')
                        ->where([
                            ['id_requerimiento', '=', $r->id_requerimiento],
                            ['tiene_transformacion', '=', false],
                            ['estado', '!=', 7]
                        ])
                        ->count();

                    if ($count_recibido == $count_todo) {
                        DB::table('almacen.alm_req')
                            ->where('id_requerimiento', $r->id_requerimiento)
                            ->update(['estado' => 28]); //en atencion total
                    }

                    //Agrega accion en requerimiento
                    DB::table('almacen.alm_req_obs')
                        ->insert([
                            'id_requerimiento' => $r->id_requerimiento,
                            'accion' => 'INGRESO POR TRANSFERENCIA',
                            'descripcion' => 'Ingresó al Almacén por Transferencia con Guía ' . $guia_ven->serie . '-' . $guia_ven->numero,
                            'id_usuario' => $usuario->id_usuario,
                            'fecha_registro' => $fecha
                        ]);
                }
            }
            DB::commit();
            return response()->json([
                'tipo' => 'success',
                'mensaje' => 'Se guardó correctamente el ingreso',
                'id_ingreso' => $id_ingreso,
                'nroPorRecibir' => $this->nroPorRecibir(), 200
            ]);
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar el ingreso. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }
    public function reservaNextCodigo($id_almacen)
    {
        $yyyy = date('Y', strtotime(date('Y-m-d H:i:s')));
        $anio = date('y', strtotime(date('Y-m-d H:i:s')));

        $cantidad = DB::table('almacen.alm_reserva')
            ->where('id_almacen_reserva', $id_almacen)
            ->whereYear('fecha_registro', '=', $yyyy)
            ->get()->count();

        $val = GenericoAlmacenController::leftZero(4, ($cantidad + 1));
        $nextId = "RE-" . $id_almacen . "-" . $anio . $val;

        return $nextId;
    }

    public function guardarSalidaTransferencia(Request $request)
    {
        try {
            DB::beginTransaction();
            $id_tp_doc_almacen = 2; //guia venta
            $operacion_transferencia = 11; //salida por transferencia
            $operacion_venta = 1; //venta 
            $fecha_registro = new Carbon();
            $fecha = new Carbon();
            $usuario = Auth::user()->id_usuario;
            $mensaje = '';

            $destino_emp = DB::table('almacen.alm_almacen')
                ->select('adm_empresa.id_empresa', 'com_cliente.id_cliente')
                ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
                ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                ->leftjoin('comercial.com_cliente', 'com_cliente.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
                ->where('id_almacen', $request->id_almacen_destino)
                ->first();

            $origen_emp = DB::table('almacen.alm_almacen')
                ->select('adm_empresa.id_empresa')
                ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
                ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                ->where('id_almacen', $request->id_almacen_origen)
                ->first();

            $operacion = ($destino_emp->id_empresa == $origen_emp->id_empresa ? $operacion_transferencia : $operacion_venta);

            $trans_sel = null;
            if ($request->trans_seleccionadas !== null) {
                $trans_sel = json_decode($request->trans_seleccionadas);
            }

            $query = DB::table('almacen.trans_detalle')
                ->select(
                    'trans_detalle.*',
                    'alm_prod.id_unidad_medida',
                    'alm_prod.codigo',
                    'alm_prod.descripcion',
                    'guia_com_det.id_guia_com_det',
                    'guia_oc.id_guia_com_det as id_guia_oc_det',
                    // DB::raw('(mov_alm_det.valorizacion / mov_alm_det.cantidad) as unitario'),
                    DB::raw('CASE WHEN mov_alm_det.cantidad=0 THEN 0 
                    ELSE (mov_alm_det.valorizacion / mov_alm_det.cantidad) END as unitario'),
                    DB::raw('(mov_oc.valorizacion / mov_oc.cantidad) as unitario_oc')
                )
                ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'trans_detalle.id_producto')
                ->leftJoin('almacen.guia_com_det', function ($join) {
                    $join->on('guia_com_det.id_guia_com_det', '=', 'trans_detalle.id_guia_com_det');
                    $join->where('guia_com_det.estado', '!=', 7);
                })
                ->leftJoin('almacen.mov_alm_det', function ($join) {
                    $join->on('mov_alm_det.id_guia_com_det', '=', 'guia_com_det.id_guia_com_det');
                    $join->where('mov_alm_det.estado', '!=', 7);
                })
                ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'trans_detalle.id_requerimiento_detalle')
                ->leftJoin('logistica.log_det_ord_compra', function ($join) {
                    $join->on('log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
                    $join->where('log_det_ord_compra.estado', '!=', 7);
                })
                ->leftJoin('almacen.guia_com_det as guia_oc', function ($join) {
                    $join->on('guia_oc.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden');
                    $join->where('guia_oc.estado', '!=', 7);
                })
                ->leftJoin('almacen.mov_alm_det as mov_oc', function ($join) {
                    $join->on('mov_oc.id_guia_com_det', '=', 'guia_oc.id_guia_com_det');
                    $join->where('mov_oc.estado', '!=', 7);
                });
            if ($trans_sel !== null) {
                $detalle = $query->whereIn('trans_detalle.id_transferencia', $trans_sel)->get();
            } else {
                $detalle = $query->where('trans_detalle.id_transferencia', $request->id_transferencia)->get();
            }

            foreach ($detalle as $det) {
                $stockDisponible = (new SalidasPendientesController)->validaStockDisponible($det->id_producto, $request->id_almacen_origen);

                if ($stockDisponible <= 0) {
                    $mensaje .= $det->codigo . ' - ' . $det->descripcion . ' \n';
                }
            }

            if ($mensaje == '') {

                $id_guia = DB::table('almacen.guia_ven')->insertGetId(
                    [
                        'id_tp_doc_almacen' => $id_tp_doc_almacen,
                        'serie' => $request->trans_serie,
                        'numero' => $request->trans_numero,
                        'fecha_emision' => $request->fecha_emision,
                        'fecha_almacen' => $request->fecha_almacen,
                        'id_almacen' => $request->id_almacen_origen,
                        'transportista' => $request->id_transportista,
                        'tra_serie' => $request->tra_serie,
                        'tra_numero' => $request->tra_numero,
                        'placa' => $request->placa,
                        'punto_partida' => $request->punto_partida,
                        'punto_llegada' => $request->punto_llegada,
                        'comentario' => $request->comentario_enviar,
                        'id_cliente' => ($destino_emp->id_cliente !== null ? $destino_emp->id_cliente : null),
                        'usuario' => $usuario,
                        'estado' => 1,
                        'fecha_registro' => $fecha_registro,
                        'id_sede' => $request->id_sede,
                        'fecha_traslado' => $fecha,
                        'id_operacion' => $operacion,
                        'id_transferencia' => (isset($request->id_transferencia) ? $request->id_transferencia : null),
                        'registrado_por' => $usuario,
                    ],
                    'id_guia_ven'
                );
                //cambia estado serie-numero
                // if ($request->id_serie_numero !== null && $request->id_serie_numero !== '') {
                //     DB::table('almacen.serie_numero')
                //         ->where('id_serie_numero', $request->id_serie_numero)
                //         ->update(['estado' => 8]); //emitido -> 8
                // }

                //actualizo la transferencia
                if ($trans_sel !== null) {
                    foreach ($trans_sel as $trans) {
                        DB::table('almacen.trans')->where('id_transferencia', $trans)
                            ->update([
                                'id_almacen_destino' => $request->id_almacen_destino,
                                'id_guia_ven' => $id_guia,
                                'responsable_origen' => $usuario,
                                'responsable_destino' => $request->responsable_destino_trans,
                                'estado' => 17, //enviado
                                'fecha_transferencia' => $fecha
                            ]);
                        DB::table('almacen.trans_detalle')
                            ->where('id_transferencia', $trans)
                            ->update(['estado' => 17]); //enviado
                    }
                } else {
                    DB::table('almacen.trans')->where('id_transferencia', $request->id_transferencia)
                        ->update([
                            'id_almacen_destino' => $request->id_almacen_destino,
                            'id_guia_ven' => $id_guia,
                            'responsable_origen' => $usuario,
                            'responsable_destino' => $request->responsable_destino_trans,
                            'estado' => 17,
                            'fecha_transferencia' => $fecha
                        ]);
                    DB::table('almacen.trans_detalle')
                        ->where('id_transferencia', $request->id_transferencia)
                        ->update(['estado' => 17]);
                }
                //Genero la salida
                $codigo = GenericoAlmacenController::nextMovimiento(
                    2, //salida
                    $request->fecha_almacen,
                    $request->id_almacen_origen
                );

                $id_salida = DB::table('almacen.mov_alm')->insertGetId(
                    [
                        'id_almacen' => $request->id_almacen_origen,
                        'id_tp_mov' => 2, //Salidas
                        'codigo' => $codigo,
                        'fecha_emision' => $request->fecha_almacen,
                        'id_guia_ven' => $id_guia,
                        'id_transferencia' => ($request->id_transferencia !== null ? $request->id_transferencia : null),
                        'id_operacion' => $operacion,
                        'revisado' => 0,
                        'usuario' => $usuario,
                        'estado' => 1,
                        'fecha_registro' => $fecha_registro,
                    ],
                    'id_mov_alm'
                );

                $detalle_trans = json_decode($request->detalle);

                foreach ($detalle as $det) {

                    $id_guia_ven_det = DB::table('almacen.guia_ven_det')->insertGetId(
                        [
                            'id_guia_ven' => $id_guia,
                            'id_producto' => $det->id_producto,
                            'cantidad' => $det->cantidad,
                            'id_unid_med' => $det->id_unidad_medida,
                            'id_trans_det' => $det->id_trans_detalle,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro,
                        ],
                        'id_guia_ven_det'
                    );
                    //atiende la reserva
                    DB::table('almacen.alm_reserva')
                        ->where('id_trans_detalle', $det->id_trans_detalle)
                        ->update(['estado' => 5]);

                    foreach ($detalle_trans as $dt) {

                        if ($dt->id_trans_detalle == $det->id_trans_detalle) {

                            foreach ($dt->series as $s) {
                                //Guardo relacion guia_ven_det en las series
                                if ($s->id_prod_serie !== null && $s->estado == 1) {
                                    DB::table('almacen.alm_prod_serie')
                                        ->where([['id_prod_serie', '=', $s->id_prod_serie]])
                                        ->update(['id_guia_ven_det' => $id_guia_ven_det]);
                                }
                            }
                        }
                    }
                    //Guardo los items de la salida
                    DB::table('almacen.mov_alm_det')->insert(
                        [
                            'id_mov_alm' => $id_salida,
                            'id_producto' => $det->id_producto,
                            // 'id_posicion' => $det->id_posicion,
                            'cantidad' => $det->cantidad,
                            'valorizacion' => ($det->unitario !== null ? ($det->cantidad * $det->unitario) : ($det->unitario_oc !== null ?
                                ($det->unitario_oc * $det->cantidad) : 0)),
                            'usuario' => $usuario,
                            'id_guia_ven_det' => $id_guia_ven_det,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro,
                        ]
                    );
                    //Actualizo los saldos del producto
                    OrdenesPendientesController::actualiza_prod_ubi($det->id_producto, $request->id_almacen_origen);
                    //Actualiza estado requerimiento_detalle: enviado -> X REVISAR
                    // DB::table('almacen.alm_det_req')
                    //     ->where('id_detalle_requerimiento', $det->id_requerimiento_detalle)
                    //     ->update(['estado' => 17]); //enviado

                }

                $reqs = [];
                if ($trans_sel !== null) {
                    $reqs = DB::table('almacen.trans')
                        ->select('trans.id_requerimiento', 'trans.id_almacen_destino')
                        ->whereIn('trans.id_transferencia', $trans_sel)
                        ->distinct()->get();
                } else {
                    // $reqs = $request->id_requerimiento;
                    array_push($reqs, $request->id_requerimiento);
                }
                //actualiza estado requerimiento: enviado
                foreach ($reqs as $req) {
                    //Agrega accion en requerimiento
                    DB::table('almacen.alm_req_obs')
                        ->insert([
                            'id_requerimiento' => $req->id_requerimiento,
                            'accion' => 'SALIDA POR TRANSFERENCIA',
                            'descripcion' => 'Salió del Almacén por Transferencia con Guía ' . $request->trans_serie . '-' . $request->trans_numero,
                            'id_usuario' => $usuario,
                            'fecha_registro' => $fecha_registro
                        ]);
                }

                $tipo = 'success';
                $mensaje = 'Se guardó correctamente la salida de almacén';
            } else {
                $tipo = 'warning';
                $mensaje = 'No hay stock disponible para éstos productos: ' . $mensaje;
            }

            DB::commit();
            return response()->json(
                [
                    'tipo' => $tipo,
                    'mensaje' => $mensaje,
                    'nroPorEnviar' => $this->nroPorEnviar(), 200
                ]
            );
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar la salida. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    public function listarDetalleTransferencias($id_requerimiento)
    {
        $trans = DB::table('almacen.trans')
            ->select(
                'trans.*',
                'origen.descripcion as almacen_origen',
                'destino.descripcion as almacen_destino',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'guia_com.serie as serie_com',
                'guia_com.numero as numero_com',
                'guia_ven.serie as serie_ven',
                'guia_ven.numero as numero_ven'
            )
            ->join('almacen.alm_almacen as origen', 'origen.id_almacen', '=', 'trans.id_almacen_origen')
            ->join('almacen.alm_almacen as destino', 'destino.id_almacen', '=', 'trans.id_almacen_destino')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'trans.estado')
            ->leftJoin('almacen.guia_com', 'guia_com.id_guia', '=', 'trans.id_guia_com')
            ->leftJoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'trans.id_guia_ven')
            ->where([['trans.id_requerimiento', '=', $id_requerimiento], ['trans.estado', '!=', 7]])
            ->get();
        $html = '';
        $i = 1;
        foreach ($trans as $t) {
            $html .= '
            <tr style="background-color: lightgray;">
                <td>' . $i . '</td>
                <td>' . $t->codigo . '</td>
                <td>' . $t->almacen_origen . '</td>
                <td>' . $t->almacen_destino . '</td>
                <td>' . ($t->serie_ven !== null ? ($t->serie_ven . '-' . $t->numero_ven) : '') . '</td>
                <td>' . ($t->serie_com !== null ? ($t->serie_com . '-' . $t->numero_com) : '') . '</td>
                <td><span class="label label-' . $t->bootstrap_color . '">' . $t->estado_doc . '</span></td>
            <tr/>';
            $i++;

            $detalle = DB::table('almacen.trans_detalle')
                ->select(
                    'alm_prod.codigo',
                    'alm_prod.part_number',
                    'alm_prod.descripcion',
                    'trans_detalle.cantidad',
                    'trans_detalle.estado',
                    'adm_estado_doc.estado_doc',
                    'adm_estado_doc.bootstrap_color'
                )
                ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'trans_detalle.id_producto')
                ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'trans_detalle.estado')
                ->where([
                    ['trans_detalle.id_transferencia', '=', $t->id_transferencia],
                    ['trans_detalle.estado', '!=', 7]
                ])
                ->get();

            foreach ($detalle as $det) {
                $html .= '
                <tr>
                    <td></td>
                    <td>' . $det->codigo . '</td>
                    <td>' . $det->part_number . '</td>
                    <td colSpan="2">' . $det->descripcion . '</td>
                    <td>' . $det->cantidad . '</td>
                    <td><span class="label label-' . $det->bootstrap_color . '">' . $det->estado_doc . '</span></td>
                <tr/>';
            }
        }
        return json_encode($html);
    }

    public function listarDetalleTransferencia(Request $request)
    {
        $detalle = DB::table('almacen.trans_detalle')
            ->select(
                'trans_detalle.*',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.series',
                // 'alm_cat_prod.descripcion as categoria',
                // 'alm_subcat.descripcion as subcategoria',
                'alm_prod.part_number',
                'alm_und_medida.abreviatura',
                'trans.codigo as codigo_trans',
                'alm_req.codigo as codigo_req',
                'alm_req.concepto',
                'guia_com_det.id_guia_com_det',
                'guia_oc.id_guia_com_det as id_guia_oc_det'
            )
            ->join('almacen.trans', 'trans.id_transferencia', '=', 'trans_detalle.id_transferencia')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'trans_detalle.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            // ->join('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            // ->join('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'trans_detalle.id_requerimiento_detalle')
            ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftJoin('logistica.log_det_ord_compra', function ($join) {
                $join->on('log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
                $join->where('log_det_ord_compra.estado', '!=', 7);
            })
            ->leftJoin('almacen.guia_com_det as guia_oc', function ($join) {
                $join->on('guia_oc.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden');
                $join->where('guia_oc.estado', '!=', 7);
            })
            ->leftJoin('almacen.guia_com_det', function ($join) {
                $join->on('guia_com_det.id_guia_com_det', '=', 'trans_detalle.id_guia_com_det');
                $join->where('guia_com_det.estado', '!=', 7);
            })
            ->where([['trans_detalle.estado', '!=', 7]]);

        if ($request->type == 1) {
            $query = $detalle->where('trans_detalle.id_transferencia', $request->id)->get();
        } else {
            $query = $detalle->whereIn('trans_detalle.id_transferencia', $request->data)->get();
        }

        $lista_detalle = [];

        foreach ($query as $det) {

            if ($det->id_guia_oc_det !== null) {
                $series = DB::table('almacen.alm_prod_serie')
                    ->where('id_guia_com_det', $det->id_guia_oc_det)
                    ->get();
            } else if ($det->id_guia_com_det !== null) {
                $series = DB::table('almacen.alm_prod_serie')
                    ->where('id_guia_com_det', $det->id_guia_com_det)
                    ->get();
            } else {
                $series = [];
            }

            $existe = false;
            $det_existente = null;

            foreach ($lista_detalle as $d) {
                if ($d->id_trans_detalle == $det->id_trans_detalle) {
                    $existe = true;
                    $det_existente = $d;
                }
            }

            if ($existe) {
                array_push($series, $det_existente->series);
                $det_existente->series = $series;
            } else {
                array_push($lista_detalle, [
                    'id_guia_com_det' => $det->id_guia_com_det,
                    'id_trans_detalle' => $det->id_trans_detalle,
                    'id_producto' => $det->id_producto,
                    'codigo_trans' => $det->codigo_trans,
                    'codigo_req' => $det->codigo_req,
                    'concepto' => $det->concepto,
                    'codigo' => $det->codigo,
                    'part_number' => $det->part_number,
                    'descripcion' => $det->descripcion,
                    'cantidad' => $det->cantidad,
                    'abreviatura' => $det->abreviatura,
                    'control_series' => $det->series,
                    'series' => $series
                ]);
            }
        }

        return response()->json($lista_detalle);
    }

    public function listarSeries($id_guia_com_det)
    {
        $series = DB::table('almacen.alm_prod_serie')
            ->select(
                'alm_prod_serie.*',
                DB::raw("(guia_com.serie) || '-' || (guia_com.numero) AS guia_com")
            )
            ->join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'alm_prod_serie.id_guia_com_det')
            ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->where([
                ['alm_prod_serie.id_guia_com_det', '=', $id_guia_com_det],
                ['alm_prod_serie.estado', '!=', 7]
            ])
            ->get();
        return response()->json($series);
    }

    public function listarSeriesVen($id_guia_ven_det)
    {
        $series = DB::table('almacen.alm_prod_serie')
            ->select(
                'alm_prod_serie.*',
                DB::raw("(guia_ven.serie) || '-' || (guia_ven.numero) AS guia_ven")
            )
            ->join('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven_det', '=', 'alm_prod_serie.id_guia_ven_det')
            ->join('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')
            ->where([
                ['alm_prod_serie.id_guia_ven_det', '=', $id_guia_ven_det],
                ['alm_prod_serie.estado', '!=', 7]
            ])
            ->get();
        return response()->json($series);
    }

    public static function transferencia_nextId($id_alm_origen)
    {
        $yyyy = date('Y', strtotime(date('Y-m-d H:i:s')));
        $anio = date('y', strtotime(date('Y-m-d H:i:s')));

        $cantidad = DB::table('almacen.trans')
            ->where('id_almacen_origen', $id_alm_origen)
            ->whereYear('fecha_registro', '=', $yyyy)
            ->get()->count();

        $alm = DB::table('almacen.alm_almacen')
            ->select('codigo')
            ->where('id_almacen', $id_alm_origen)->first();

        $val = GenericoAlmacenController::leftZero(3, ($cantidad + 1));
        $nextId = "TR-" . $alm->codigo . "-" . $anio . $val;

        return $nextId;
    }

    public function listar_guias_compra()
    {
        $data = DB::table('almacen.guia_com')
            ->select(
                'guia_com.*',
                'adm_contri.razon_social',
                'tp_ope.descripcion as operacion',
                'alm_almacen.descripcion as almacen_descripcion',
                'mov_alm.codigo'
            )
            // DB::raw("(SELECT COUNT(*) FROM almacen.guia_com_det where
            //             guia_com_det.id_guia_com = guia_com.id_guia
            //             and guia_com_det.id_trans_detalle > 0
            //             and guia_com_det.estado != 7) AS count_transferencias_detalle")
            ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'guia_com.id_proveedor')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftjoin('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'guia_com.id_operacion')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'guia_com.id_almacen')
            ->join('almacen.mov_alm', 'mov_alm.id_guia_com', '=', 'guia_com.id_guia')
            ->where([['guia_com.estado', '!=', 7]])
            ->orderBy('fecha_emision', 'desc')
            ->get();

        // $lista = [];
        // foreach ($data as $d) {
        //     if ($d->count_transferencias_detalle == 0){
        //         array_push($lista, $d);
        //     }
        // }
        $output['data'] = $data;
        return response()->json($output);
    }

    public function verGuiaCompraTransferencia($id_guia)
    {
        $guia = DB::table('almacen.guia_com')
            ->select(
                'guia_com.*',
                'alm_almacen.descripcion as almacen_descripcion',
                'tp_ope.descripcion as operacion',
                'guia_clas.descripcion as clasificacion'
            )
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'guia_com.id_almacen')
            ->join('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'guia_com.id_operacion')
            ->join('almacen.guia_clas', 'guia_clas.id_clasificacion', '=', 'guia_com.id_guia_clas')
            ->where('id_guia', $id_guia)
            ->first();

        $detalle = DB::table('almacen.guia_com_det')
            ->select(
                'guia_com_det.*',
                'log_ord_compra.codigo as codigo_orden',
                'alm_req.codigo as codigo_req',
                'sis_sede.descripcion as sede_req',
                'alm_prod.codigo',
                'alm_prod.part_number',
                'alm_prod.descripcion',
                'alm_und_medida.abreviatura',
                'req_od.codigo as codigo_req_od',
                'transformacion.codigo as codigo_transfor',
                'sede_req_od.descripcion as sede_req_od'
            )
            ->leftjoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det')
            ->leftjoin('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftjoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftjoin('almacen.transfor_transformado', 'transfor_transformado.id_transformado', '=', 'guia_com_det.id_transformado')
            ->leftjoin('almacen.transformacion', 'transformacion.id_transformacion', '=', 'transfor_transformado.id_transformacion')
            ->leftjoin('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'transformacion.id_od')
            ->leftjoin('almacen.alm_req as req_od', 'req_od.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
            ->leftjoin('administracion.sis_sede as sede_req_od', 'sede_req_od.id_sede', '=', 'req_od.id_sede')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_com_det.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where([['guia_com_det.id_guia_com', '=', $id_guia], ['guia_com_det.estado', '!=', 7]])
            ->get();

        $lista_detalle = [];

        foreach ($detalle as $det) {
            $series = DB::table('almacen.alm_prod_serie')
                ->where('id_guia_com_det', $det->id_guia_com_det)
                ->get();

            array_push($lista_detalle, [
                'id_guia_com_det' => $det->id_guia_com_det,
                'codigo_orden' => $det->codigo_orden,
                'codigo_transfor' => $det->codigo_transfor,
                'codigo_req' => ($det->codigo_req !== null ? $det->codigo_req : $det->codigo_req_od),
                'sede_req' => ($det->sede_req !== null ? $det->sede_req : $det->sede_req_od),
                'codigo' => $det->codigo,
                'part_number' => $det->part_number,
                'descripcion' => $det->descripcion,
                'abreviatura' => $det->abreviatura,
                'cantidad' => $det->cantidad,
                'series' => $series
            ]);
        }

        return response()->json(['guia' => $guia, 'detalle' => $lista_detalle]);
    }


    public function generarTransferenciaRequerimiento(Request $request)
    {
        try {
            DB::beginTransaction();

            $fecha = date('Y-m-d H:i:s');
            $id_usuario = Auth::user()->id_usuario;
            $mensaje = '';
            $tipo = '';

            $req = DB::table('almacen.alm_req')
                ->select('alm_req.id_requerimiento', 'trans.codigo')
                ->leftJoin('almacen.trans', function ($join) {
                    $join->on('trans.id_requerimiento', '=', 'alm_req.id_requerimiento');
                    $join->where('trans.estado', '!=', 7);
                })
                ->where([['alm_req.id_requerimiento', '=', $request->id_requerimiento]])
                ->first();

            if ($req !== null) {

                // if ($req->codigo == null) {

                $array_almacen = [];
                foreach ($request->detalle as $det) {

                    if (!in_array($det['id_almacen_reserva'], $array_almacen)) {
                        array_push($array_almacen, $det['id_almacen_reserva']);
                    }
                }

                if ($array_almacen !== []) {

                    foreach ($array_almacen as $alm) {
                        $codigo = TransferenciaController::transferencia_nextId($alm);

                        if ($mensaje == '') {
                            $mensaje = 'Se ha creado la(s) transferencia(s): ' . $codigo . ' exitosamente.';
                        } else {
                            $mensaje .= ', ' . $codigo;
                        }
                        $tipo = 'success';
                        $id_trans = DB::table('almacen.trans')->insertGetId(
                            [
                                'id_almacen_origen' => $alm,
                                'id_almacen_destino' => $request->id_almacen_destino,
                                'codigo' => $codigo,
                                'id_requerimiento' =>  $req->id_requerimiento,
                                'id_guia_ven' => null,
                                'responsable_origen' => $id_usuario,
                                'responsable_destino' => $id_usuario,
                                'fecha_transferencia' => date('Y-m-d'),
                                'registrado_por' => $id_usuario,
                                'estado' => 1,
                                'fecha_registro' => $fecha
                            ],
                            'id_transferencia'
                        );

                        foreach ($request->detalle as $item) {

                            $id_almacen_origen = ($item['id_almacen_reserva'] !== null ? $item['id_almacen_reserva'] : null);
                            //$id_almacen_origen=$item['id_almacen_reserva'];
                            if (intVal($id_almacen_origen) === intVal($alm)) {

                                $id_trans_detalle = DB::table('almacen.trans_detalle')->insertGetId(
                                    [
                                        'id_transferencia' => $id_trans,
                                        'id_producto' => $item['id_producto'],
                                        'cantidad' => $item['stock_comprometido'],
                                        'estado' => 1,
                                        'fecha_registro' => $fecha,
                                        'id_requerimiento_detalle' => $item['id_detalle_requerimiento'],
                                        'id_guia_com_det' => $item['id_guia_com_det'],
                                    ],
                                    'id_trans_detalle'
                                );
                                //envia la reserva
                                // DB::table('almacen.alm_reserva')
                                //     ->where('id_reserva', $item['id_reserva'])
                                //     ->update([
                                //         'estado' => 17,
                                //         'id_trans_detalle' => $id_trans_detalle
                                //     ]);
                                if ($item['id_detalle_requerimiento'] !== null) {
                                    DB::table('almacen.alm_reserva')
                                        ->where('id_detalle_requerimiento', $item['id_detalle_requerimiento'])
                                        ->update([
                                            'estado' => 17,
                                            'id_trans_detalle' => $id_trans_detalle
                                        ]);
                                }
                            }
                        }
                    }
                } else {
                    $mensaje = 'No hay almacenes en el requerimiento';
                    $tipo = 'warning';
                }
                // } else {
                //     $mensaje = 'Ya se generó la(s) transferencia(s)';
                //     $tipo = 'warning';
                // }
            } else {
                $mensaje = 'No existe el requerimiento seleccionado';
                $tipo = 'warning';
            }

            DB::commit();
            return response()->json(['tipo' => $tipo, 'mensaje' => $mensaje, 'nroPendientes' => $this->nroPendientes(), 200]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar la transferencia. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }


    public function nuevaTransferencia(Request $request)
    {
        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;

            $codigo = TransferenciaController::transferencia_nextId($request->id_almacen_origen);

            $id_transferencia = DB::table('almacen.trans')->insertGetId(
                [
                    'id_almacen_origen' => $request->id_almacen_origen,
                    'id_almacen_destino' => $request->id_almacen_destino,
                    'concepto' => $request->concepto,
                    'codigo' => $codigo,
                    'id_requerimiento' => null,
                    'id_guia_ven' => null,
                    'responsable_origen' => $id_usuario,
                    'responsable_destino' => null,
                    'fecha_transferencia' => new Carbon(),
                    'registrado_por' => $id_usuario,
                    'estado' => 1,
                    'fecha_registro' => new Carbon()
                ],
                'id_transferencia'
            );

            foreach ($request->detalle as $item) {

                $id_trans_detalle = DB::table('almacen.trans_detalle')->insertGetId(
                    [
                        'id_transferencia' => $id_transferencia,
                        'id_producto' => $item['id_producto'],
                        'cantidad' => $item['cantidad'],
                        'estado' => 1,
                        'fecha_registro' => new Carbon(),
                    ],
                    'id_trans_detalle'
                );
                //envia la reserva
                DB::table('almacen.alm_reserva')
                    ->insert([
                        'codigo' => Reserva::crearCodigo($request->id_almacen_origen),
                        'id_producto' => $item['id_producto'],
                        'stock_comprometido' => $item['cantidad'],
                        'id_almacen_reserva' => $request->id_almacen_origen,
                        'id_trans_detalle' => $id_trans_detalle,
                        'estado' => 1,
                        'usuario_registro' => $id_usuario,
                        'fecha_registro' => new Carbon(),
                    ]);
            }
            DB::commit();
            return response()->json(['tipo' => 'success', 'mensaje' => 'Se guardó correctamente', 'nroPendientes' => $this->nroPendientes(), 200]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar la transferencia. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    public function verRequerimiento($id)
    {
        $req = DB::table('almacen.alm_req')
            ->select('alm_req.*', 'adm_estado_doc.estado_doc', 'sis_sede.descripcion as sede_requerimiento')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.estado')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->where('id_requerimiento', $id)
            ->first();

        $almacenes = DB::table('almacen.alm_almacen')
            ->select('alm_almacen.id_almacen', 'alm_almacen.descripcion', 'alm_almacen.id_tipo_almacen')
            ->where([['alm_almacen.id_sede', '=', $req->id_sede], ['alm_almacen.estado', '!=', 7]])
            ->orderBy('descripcion')
            ->get();

        //revisar que obtenga el stock comprometido segun producto y almacen
        $req_detalle = DB::table('almacen.alm_reserva')
            ->select(
                'alm_det_req.*',
                'alm_reserva.id_reserva',
                'alm_reserva.id_almacen_reserva',
                'alm_almacen.descripcion as almacen_descripcion',
                'sis_sede.id_sede as id_sede_reserva',
                'almacen_guia.id_almacen as id_almacen_guia',
                'sede_guia.id_sede as id_sede_guia',
                'sede_guia.descripcion as sede_guia_descripcion',
                'sis_sede.descripcion as sede_reserva_descripcion',
                'log_ord_compra.codigo as codigo_orden',
                'alm_prod.codigo',
                'alm_prod.part_number',
                'alm_prod.descripcion',
                'alm_und_medida.abreviatura',
                'guia_com_det.id_guia_com_det',
                DB::raw("(guia_com.serie) || ' ' || (guia_com.numero) as serie_numero"),
                DB::raw("(SELECT sum(cantidad) FROM almacen.trans_detalle
                        WHERE trans_detalle.id_requerimiento_detalle = alm_det_req.id_detalle_requerimiento
                        and trans_detalle.estado != 7) AS cantidad_transferida"),
                DB::raw("(SELECT sum(alm_reserva.stock_comprometido) FROM almacen.alm_reserva
                        WHERE alm_reserva.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento
                        and alm_reserva.id_almacen_reserva != alm_req.id_almacen
                        and alm_reserva.estado = 1) AS stock_comprometido")
            )
            ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'alm_reserva.id_detalle_requerimiento')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftjoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_reserva.id_almacen_reserva')
            ->leftjoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
            ->leftJoin('logistica.log_det_ord_compra', function ($join) {
                $join->on('log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
                $join->where('log_det_ord_compra.estado', '!=', 7);
            })
            ->leftjoin('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            ->leftJoin('almacen.guia_com_det', function ($join) {
                $join->on('guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden');
                $join->where('guia_com_det.estado', '!=', 7);
            })
            ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->leftjoin('almacen.alm_almacen as almacen_guia', 'almacen_guia.id_almacen', '=', 'guia_com.id_almacen')
            ->leftjoin('administracion.sis_sede as sede_guia', 'sede_guia.id_sede', '=', 'almacen_guia.id_sede')
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where([
                ['alm_det_req.id_requerimiento', '=', $id],
                ['alm_reserva.estado', '=', 1]
            ])
            ->get();


        $items = [];

        foreach ($req_detalle as $det) {

            if (floatval($det->stock_comprometido) > 0) {

                if ($det->id_guia_com_det !== null) {
                    $series = DB::table('almacen.alm_prod_serie')
                        ->where('id_guia_com_det', $det->id_guia_com_det)
                        ->get();
                } else {
                    $series = [];
                }

                // if (($det->id_sede_guia !== null && $req->id_sede !== $det->id_sede_guia) ||
                //     ($det->id_sede_reserva !== null && $req->id_sede !== $det->id_sede_reserva)
                // ) {
                if ($det->id_almacen_reserva !== $req->id_almacen) {

                    $item_det = [
                        'id_detalle_requerimiento' => $det->id_detalle_requerimiento,
                        'codigo_orden' => ($det->codigo_orden !== null ? $det->codigo_orden : null),
                        'guia' => ($det->serie_numero !== null ? $det->serie_numero : null),
                        'sede' => ($det->id_sede_guia !== null ? $det->sede_guia_descripcion : ($det->id_sede_reserva !== null ? $det->sede_reserva_descripcion : '')),
                        'id_producto' => $det->id_producto,
                        'codigo' => $det->codigo,
                        'part_number' => $det->part_number,
                        'descripcion' => $det->descripcion,
                        'abreviatura' => $det->abreviatura,
                        'stock_comprometido' => floatval($det->stock_comprometido),
                        'id_reserva' => $det->id_reserva,
                        'id_almacen_reserva' => $det->id_almacen_reserva,
                        'almacen_descripcion' => $det->almacen_descripcion,
                        'id_guia_com_det' => $det->id_guia_com_det,
                        'series' => $series
                    ];

                    // if ($det->tiene_transformacion) {
                    $exist = false;
                    foreach ($items as $item) {
                        if ($item['id_detalle_requerimiento'] == $det->id_detalle_requerimiento) {
                            $exist = true;
                        }
                    }
                    if (!$exist) {
                        array_push($items, $item_det);
                    }
                }
            }
        }
        return response()->json([
            'requerimiento' => $req, 'detalle' => $items, 'almacenes' => $almacenes //($transformacion ? $items_transf : $items_base),
            // 'items_transf' => $items_transf, 'items_base' => $items_base,
            // 'transformaciones' => $transformaciones, 'todas_transformaciones' => $todas_transformaciones
        ]);
    }


    public function guardar_guia_transferencia(Request $request)
    {

        try {
            DB::beginTransaction();
            // database queries here
            $id_tp_doc_almacen = 2; //guia venta
            $id_operacion = 11; //salida por transferencia
            $fecha_registro = date('Y-m-d H:i:s');
            $fecha = date('Y-m-d');
            $usuario = Auth::user()->id_usuario;

            $id_guia = DB::table('almacen.guia_ven')->insertGetId(
                [
                    'id_tp_doc_almacen' => $id_tp_doc_almacen,
                    'serie' => $request->trans_serie,
                    'numero' => $request->trans_numero,
                    'fecha_emision' => $request->fecha_emision,
                    'fecha_almacen' => $request->fecha_almacen,
                    'id_almacen' => $request->id_almacen_origen,
                    // 'usuario' => $request->responsable_origen,
                    'usuario' => $usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha_registro,
                    'id_sede' => $request->id_sede,
                    'fecha_traslado' => $fecha,
                    'id_operacion' => $id_operacion,
                    'id_guia_com' => ($request->id_guia_com !== '' ? $request->id_guia_com : null),
                    // 'id_cliente' => $request->numero,
                    'registrado_por' => $usuario,
                ],
                'id_guia_ven'
            );
            //cambia estado serie-numero
            if ($request->id_serie_numero !== null && $request->id_serie_numero !== '') {
                DB::table('almacen.serie_numero')
                    ->where('id_serie_numero', $request->id_serie_numero)
                    ->update(['estado' => 8]); //emitido -> 8
            }

            $codigo_trans = TransferenciaController::transferencia_nextId($request->id_almacen_origen);
            //crear la transferencia
            $id_trans = DB::table('almacen.trans')->insertGetId(
                [
                    'id_almacen_origen' => $request->id_almacen_origen,
                    'id_almacen_destino' => $request->id_almacen_destino,
                    'codigo' => $codigo_trans,
                    'id_guia_ven' => $id_guia,
                    // 'responsable_origen' => $request->responsable_origen,
                    'responsable_origen' => $usuario,
                    'responsable_destino' => $request->responsable_destino_trans,
                    'fecha_transferencia' => $fecha,
                    'registrado_por' => $usuario,
                    'estado' => 17, //enviado
                    'fecha_registro' => $fecha_registro,
                ],
                'id_transferencia'
            );
            // //copia id_transferencia en el ingreso
            // DB::table('almacen.mov_alm')
            //     ->where('id_mov_alm',$request->id_mov_alm)
            //     ->update(['id_transferencia'=>$id_trans]);
            //Genero la salida
            $codigo = GenericoAlmacenController::nextMovimiento(
                2, //salida
                $request->fecha_almacen,
                $request->id_almacen_origen
            );

            $id_salida = DB::table('almacen.mov_alm')->insertGetId(
                [
                    'id_almacen' => $request->id_almacen_origen,
                    'id_tp_mov' => 2, //Salidas
                    'codigo' => $codigo,
                    'fecha_emision' => $request->fecha_almacen,
                    'id_guia_ven' => $id_guia,
                    'id_transferencia' => $id_trans,
                    'id_operacion' => $id_operacion,
                    'revisado' => 0,
                    'usuario' => $usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha_registro,
                ],
                'id_mov_alm'
            );

            $detalle = DB::table('almacen.mov_alm_det')
                ->select('mov_alm_det.*', 'alm_prod.id_unidad_medida')
                ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
                ->where([['mov_alm_det.id_mov_alm', $request->id_mov_alm], ['mov_alm_det.estado', '!=', 7]])
                ->get();

            foreach ($detalle as $det) {
                $id_guia_ven_det = DB::table('almacen.guia_ven_det')->insertGetId(
                    [
                        'id_guia_ven' => $id_guia,
                        'id_producto' => $det->id_producto,
                        'cantidad' => $det->cantidad,
                        'id_unid_med' => $det->id_unidad_medida,
                        'id_ing_det' => $det->id_mov_alm_det,
                        'estado' => 1,
                        'fecha_registro' => $fecha_registro,
                    ],
                    'id_guia_ven_det'
                );

                //Guardo los items de la salida
                $id_det = DB::table('almacen.mov_alm_det')->insertGetId(
                    [
                        'id_mov_alm' => $id_salida,
                        'id_producto' => $det->id_producto,
                        // 'id_posicion' => $det->id_posicion,
                        'cantidad' => $det->cantidad,
                        'valorizacion' => $det->valorizacion,
                        'usuario' => $usuario,
                        'id_guia_ven_det' => $id_guia_ven_det,
                        'estado' => 1,
                        'fecha_registro' => $fecha_registro,
                    ],
                    'id_mov_alm_det'
                );
                //Actualizo los saldos del producto
                //Obtengo el registro de saldos
                $ubi = DB::table('almacen.alm_prod_ubi')
                    ->where([
                        ['id_producto', '=', $det->id_producto],
                        ['id_almacen', '=', $request->id_almacen_origen]
                    ])
                    ->first();
                //Traer stockActual
                $saldo = GenericoAlmacenController::saldo_actual_almacen($det->id_producto, $request->id_almacen_origen);
                $valor = GenericoAlmacenController::valorizacion_almacen($det->id_producto, $request->id_almacen_origen);
                $cprom = ($saldo > 0 ? $valor / $saldo : 0);
                //guardo saldos actualizados
                if ($ubi !== null) { //si no existe -> creo la ubicacion
                    DB::table('almacen.alm_prod_ubi')
                        ->where('id_prod_ubi', $ubi->id_prod_ubi)
                        ->update([
                            'stock' => $saldo,
                            'valorizacion' => $valor,
                            'costo_promedio' => $cprom
                        ]);
                } else {
                    DB::table('almacen.alm_prod_ubi')->insert([
                        'id_producto' => $det->id_producto,
                        'id_almacen' => $request->id_almacen_origen,
                        'stock' => $saldo,
                        'valorizacion' => $valor,
                        'costo_promedio' => $cprom,
                        'estado' => 1,
                        'fecha_registro' => $fecha_registro
                    ]);
                }
            }

            //actualiza estado requerimiento: enviado
            DB::table('almacen.alm_req')
                ->where('id_requerimiento', $request->id_requerimiento)
                ->update([
                    'estado' => 17,
                    'id_almacen' => $request->id_almacen_destino
                ]); //enviado
            //actualiza estado requerimiento_detalle: enviado
            DB::table('almacen.alm_det_req')
                ->where('id_requerimiento', $request->id_requerimiento)
                ->update(['estado' => 17]); //enviado
            //Agrega accion en requerimiento
            DB::table('almacen.alm_req_obs')
                ->insert([
                    'id_requerimiento' => $request->id_requerimiento,
                    'accion' => 'SALIDA POR TRANSFERENCIA',
                    'descripcion' => 'Salió del Almacén por Transferencia con Guía ' . $request->trans_serie . '-' . $request->trans_numero,
                    'id_usuario' => $usuario,
                    'fecha_registro' => $fecha_registro
                ]);

            DB::commit();
            return response()->json($id_salida);
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            // return response()->json($e);
        }
    }

    function almacenesPorUsuario()
    {
        return DB::table('almacen.alm_almacen_usuario')
            ->select('alm_almacen.*')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_almacen_usuario.id_almacen')
            ->where('alm_almacen_usuario.id_usuario', Auth::user()->id_usuario)
            ->where('alm_almacen_usuario.estado', 1)
            ->get();
    }

    function almacenesPorUsuarioArray()
    {
        $almacenes = $this->almacenesPorUsuario();

        $array_almacen = [];
        foreach ($almacenes as $alm) {
            $array_almacen[] = [$alm->id_almacen];
        }

        return $array_almacen;
    }

    function listarRequerimientos()
    {
        $array_almacen = $this->almacenesPorUsuarioArray();

        $data = DB::table('almacen.alm_reserva')
            ->select(
                'alm_req.*',
                'adm_contri.razon_social',
                'sis_usua.nombre_corto',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'sis_sede.descripcion as sede_descripcion',
                'oc_propias_view.nro_orden',
                // 'oc_propias_view.codigo_oportunidad',
                'oportunidades.codigo_oportunidad',
                'oc_propias_view.id as id_oc_propia',
                'oc_propias_view.tipo'
            )
            ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'alm_reserva.id_detalle_requerimiento')
            ->join('almacen.alm_req', function ($join) {
                $join->on('alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento');
                $join->on('alm_req.id_almacen', '!=', 'alm_reserva.id_almacen_reserva');
                $join->whereNotNull('alm_reserva.id_almacen_reserva');
            })
            // ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            // ->join('almacen.alm_reserva', function ($join) {
            //     $join->on('alm_reserva.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
            //     $join->on('alm_reserva.id_almacen_reserva', '!=', 'alm_req.id_almacen');
            //     $join->where('alm_reserva.estado', 1);
            // })
            ->leftJoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.estado')
            ->whereIn('alm_reserva.id_almacen_reserva', $array_almacen)
            ->where([
                ['alm_det_req.estado', '!=', 7],
                ['alm_reserva.estado', '=', 1],
                ['alm_reserva.stock_comprometido', '>', 0],
                ['alm_req.estado', '!=', 7],
                // ['alm_reserva.id_almacen_reserva', '!=', 'alm_req.id_almacen'],
            ])
            ->distinct();

        return datatables($data)->toJson();
    }

    public function imprimir_transferencia($id_transferencia)
    {
        $transferencia = DB::table('almacen.trans')
            ->select(
                'trans.*',
                'alm_req.codigo as codigo_req',
                'alm_req.concepto as concepto_req',
                'almacen_origen.descripcion as almacen_origen',
                'almacen_destino.descripcion as almacen_destino',
                'empresa_origen.logo_empresa',
                'contribuyente_origen.razon_social as razon_social_origen',
                'contribuyente_destino.razon_social as razon_social_destino',
                'usuario_origen.nombre_corto as responsable_origen',
                'usuario_destino.nombre_corto as responsable_destino',
                'usuario_registro.nombre_corto as registrado_por',
                DB::raw("CONCAT(guia_com.serie, '-', guia_com.numero) as guia_com"),
                DB::raw("CONCAT(guia_ven.serie, '-', guia_ven.numero) as guia_ven"),
            )
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'trans.id_requerimiento')
            ->join('almacen.alm_almacen as almacen_origen', 'almacen_origen.id_almacen', '=', 'trans.id_almacen_origen')
            ->join('almacen.alm_almacen as almacen_destino', 'almacen_destino.id_almacen', '=', 'trans.id_almacen_destino')
            ->join('administracion.sis_sede as sede_origen', 'sede_origen.id_sede', '=', 'almacen_origen.id_sede')
            ->join('administracion.adm_empresa as empresa_origen', 'empresa_origen.id_empresa', '=', 'sede_origen.id_empresa')
            ->join('contabilidad.adm_contri as contribuyente_origen', 'contribuyente_origen.id_contribuyente', '=', 'empresa_origen.id_contribuyente')
            ->join('administracion.sis_sede as sede_destino', 'sede_destino.id_sede', '=', 'almacen_destino.id_sede')
            ->join('administracion.adm_empresa as empresa_destino', 'empresa_destino.id_empresa', '=', 'sede_destino.id_empresa')
            ->join('contabilidad.adm_contri as contribuyente_destino', 'contribuyente_destino.id_contribuyente', '=', 'empresa_destino.id_contribuyente')
            ->join('configuracion.sis_usua as usuario_origen', 'usuario_origen.id_usuario', '=', 'trans.responsable_origen')
            ->join('configuracion.sis_usua as usuario_destino', 'usuario_destino.id_usuario', '=', 'trans.responsable_destino')
            ->join('configuracion.sis_usua as usuario_registro', 'usuario_registro.id_usuario', '=', 'trans.registrado_por')
            ->leftJoin('almacen.guia_com', 'guia_com.id_guia', '=', 'trans.id_guia_com')
            ->leftJoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'trans.id_guia_ven')
            ->where('trans.id_transferencia', $id_transferencia)
            ->first();

        $detalle = DB::table('almacen.trans_detalle')
            ->select(
                'alm_prod.codigo',
                'alm_prod.part_number',
                'alm_prod.descripcion',
                'alm_und_medida.abreviatura',
                'trans_detalle.cantidad'
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'trans_detalle.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where([
                ['trans_detalle.id_transferencia', '=', $id_transferencia],
                ['trans_detalle.estado', '!=', 7]
            ])
            ->get();

        $logo_empresa = ".$transferencia->logo_empresa";
        $fecha_registro =  (new Carbon($transferencia->fecha_registro))->format('d-m-Y');
        $hora_registro = (new Carbon($transferencia->fecha_registro))->format('H:i:s');

        $vista = View::make(
            'almacen/transferencias/transferencia_pdf',
            compact('transferencia', 'logo_empresa', 'detalle', 'fecha_registro', 'hora_registro')
        )->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($vista);

        return $pdf->stream();
        return $pdf->download($transferencia->codigo . '.pdf');
    }
}
