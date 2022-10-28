<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use App\Http\Controllers\Almacen\Ubicacion\AlmacenController;
use App\Http\Controllers\AlmacenController as GenericoAlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Configuracion\AccesosUsuarios;
use App\Models\Presupuestos\Moneda;
use App\Models\Tesoreria\TipoCambio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DevolucionController extends Controller
{
    function viewDevolucion()
    {
        $almacenes = AlmacenController::mostrar_almacenes_cbo();
        $empresas = GenericoAlmacenController::select_empresa();
        $unidades = GenericoAlmacenController::mostrar_unidades_cbo();
        $usuarios = GenericoAlmacenController::select_usuarios();
        $monedas = Moneda::where('estado', 1)->get();
        $tipos = DB::table('cas.devolucion_tipo')->where('estado', 1)->get();
        $array_accesos = [];
        $accesos_usuario = AccesosUsuarios::where('estado', 1)->where('id_usuario', Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos, $value->id_acceso);
        }
        return view('almacen/devoluciones/devolucion', compact(
            'almacenes',
            'empresas',
            'usuarios',
            'unidades',
            'monedas',
            'tipos',
            'array_accesos'
        ));
    }

    function viewDevolucionCas()
    {
        $almacenes = AlmacenController::mostrar_almacenes_cbo();
        $empresas = GenericoAlmacenController::select_empresa();
        $unidades = GenericoAlmacenController::mostrar_unidades_cbo();
        $usuarios = GenericoAlmacenController::select_usuarios();
        $monedas = Moneda::where('estado', 1)->get();
        return view('almacen/devoluciones/devolucionCas', compact('almacenes', 'empresas', 'usuarios', 'unidades', 'monedas'));
    }

    public function listarDevoluciones()
    {
        $lista = DB::table('cas.devolucion')
            ->select(
                'devolucion.*',
                'sis_usua.nombre_corto',
                'devolucion_estado.descripcion as estado_doc',
                'devolucion_estado.bootstrap_color',
                DB::raw("(SELECT COUNT(*) FROM cas.devolucion_ficha where
                    devolucion_ficha.id_devolucion = devolucion.id_devolucion
                    and devolucion_ficha.estado != 7) AS count_fichas"),
                'usuario_conforme.nombre_corto as usuario_conformidad',
                'log_prove.id_proveedor',
                'adm_contri.razon_social',
                'alm_almacen.id_sede',
                'alm_almacen.descripcion as almacen_descripcion',
            )
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'devolucion.registrado_por')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'devolucion.id_almacen')
            // ->join('configuracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
            ->leftJoin('configuracion.sis_usua as usuario_conforme', 'usuario_conforme.id_usuario', '=', 'devolucion.revisado_por')
            ->leftJoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'devolucion.id_cliente')
            ->leftjoin('contabilidad.adm_contri', function ($join) {
                $join->on('adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente');
                $join->where('adm_contri.estado', '!=', 7);
            })
            ->leftjoin('logistica.log_prove', function ($join) {
                $join->on('log_prove.id_contribuyente', '=', 'adm_contri.id_contribuyente');
                $join->where('log_prove.estado', '!=', 7);
            })
            // ->leftJoin('logistica.log_prove', 'log_prove.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->join('cas.devolucion_estado', 'devolucion_estado.id_estado', '=', 'devolucion.estado')
            ->where('devolucion.estado', '!=', 7)->get();
        return datatables($lista)->toJson();
        // return response()->json($lista);
    }

    public function listarDevolucionesRevisadas()
    {
        $lista = DB::table('cas.devolucion')
            ->select(
                'devolucion.*',
                'sis_usua.nombre_corto',
                'devolucion_estado.descripcion as estado_doc',
                'devolucion_estado.bootstrap_color',
                DB::raw("(SELECT COUNT(*) FROM cas.devolucion_ficha where
                    devolucion_ficha.id_devolucion = devolucion.id_devolucion
                    and devolucion_ficha.estado != 7) AS count_fichas"),
                'usuario_conforme.nombre_corto as usuario_conformidad',
                'log_prove.id_proveedor',
                'adm_contri.razon_social',
                'alm_almacen.id_sede',
                'alm_almacen.descripcion as almacen_descripcion',
            )
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'devolucion.registrado_por')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'devolucion.id_almacen')
            ->leftJoin('configuracion.sis_usua as usuario_conforme', 'usuario_conforme.id_usuario', '=', 'devolucion.revisado_por')
            ->leftJoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'devolucion.id_cliente')
            ->leftjoin('contabilidad.adm_contri', function ($join) {
                $join->on('adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente');
                $join->where('adm_contri.estado', '!=', 7);
            })
            ->leftjoin('logistica.log_prove', function ($join) {
                $join->on('log_prove.id_contribuyente', '=', 'adm_contri.id_contribuyente');
                $join->where('log_prove.estado', '!=', 7);
            })
            ->join('cas.devolucion_estado', 'devolucion_estado.id_estado', '=', 'devolucion.estado')
            ->where([
                ['devolucion.estado', '=', 2],
                ['devolucion.id_tipo', '=', 1]
            ])
            ->get();
        return datatables($lista)->toJson();
    }


    public function verFichasTecnicasAdjuntas($id)
    {
        $adjuntos = DB::table('cas.devolucion_ficha')->where([['id_devolucion', '=', $id], ['estado', '!=', 7]])->get();
        return response()->json($adjuntos);
    }

    public function mostrarDevolucion($id)
    {
        $devolucion = DB::table('cas.devolucion')
            ->select(
                'devolucion.*',
                'sis_usua.nombre_corto',
                'usu_revisado.nombre_corto as nombre_revisado',
                'proveedor.id_contribuyente',
                'proveedor.razon_social as proveedor_razon_social',
                'cliente.razon_social as cliente_razon_social',
                'devolucion_estado.descripcion as estado_descripcion',
                'devolucion_estado.bootstrap_color'
            )
            ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'devolucion.id_proveedor')
            ->leftjoin('contabilidad.adm_contri as proveedor', 'proveedor.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftjoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'devolucion.id_cliente')
            ->leftjoin('contabilidad.adm_contri as cliente', 'cliente.id_contribuyente', '=', 'devolucion.registrado_por')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'devolucion.registrado_por')
            ->leftjoin('configuracion.sis_usua as usu_revisado', 'usu_revisado.id_usuario', '=', 'devolucion.revisado_por')
            ->join('cas.devolucion_estado', 'devolucion_estado.id_estado', '=', 'devolucion.estado')
            ->where('id_devolucion', $id)->first();

        $detalle = DB::table('cas.devolucion_detalle')
            ->select(
                'devolucion_detalle.*',
                'alm_prod.part_number',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.id_moneda',
                'alm_und_medida.abreviatura as unid_med'
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'devolucion_detalle.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where('devolucion_detalle.id_devolucion', $id)
            ->where('devolucion_detalle.estado', 1)->get();

        $salidas = DB::table('cas.devolucion_venta')
            ->select(
                'devolucion_venta.id',
                'devolucion_venta.id_devolucion',
                'devolucion_venta.id_salida',
                'mov_alm.codigo',
                'mov_alm.estado',
                DB::raw("(concat(guia_ven.serie,'-',guia_ven.numero) ) as serie_numero_guia"),
                'adm_contri.razon_social',
                DB::raw("(select concat(dv.serie,'-', dv.numero) from almacen.doc_ven as dv 
                inner join almacen.doc_ven_det as d on(
                    d.id_doc=dv.id_doc_ven)
                inner join almacen.guia_ven_det as g on(
                    g.id_guia_ven_det=d.id_guia_ven_det)
                where g.id_guia_ven=mov_alm.id_guia_ven
                group by concat(dv.serie,'-', dv.numero)
                    limit 1) AS serie_numero_doc")
            )
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'devolucion_venta.id_salida')
            ->join('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'mov_alm.id_guia_ven')
            ->leftjoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'guia_ven.id_almacen')
            ->leftjoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'guia_ven.id_cliente')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->where('devolucion_venta.id_devolucion', $id)
            ->where('devolucion_venta.estado', 1)
            ->get();

        $incidencias = DB::table('cas.devolucion_incidencia')
            ->select(
                'devolucion_incidencia.id',
                'devolucion_incidencia.estado',
                'incidencia.id_incidencia',
                'incidencia.codigo',
                'incidencia.fecha_reporte',
                'adm_contri.razon_social',
                'adm_contri.id_contribuyente',
                'sis_usua.nombre_corto',
                'incidencia_estado.descripcion as estado_descripcion',
            )
            ->join('cas.incidencia', 'incidencia.id_incidencia', '=', 'devolucion_incidencia.id_incidencia')
            ->leftjoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'incidencia.id_responsable')
            ->leftjoin('cas.incidencia_estado', 'incidencia_estado.id_estado', '=', 'incidencia.estado')
            ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'incidencia.id_empresa')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'incidencia.id_contribuyente')
            ->where('devolucion_incidencia.id_devolucion', $id)
            ->where('devolucion_incidencia.estado', 1)
            ->get();

        return response()->json(['devolucion' => $devolucion, 'detalle' => $detalle, 'salidas' => $salidas, 'incidencias' => $incidencias]);
    }

    public function listarDetalleDevolucion($id_devolucion)
    {
        $detalle = DB::table('cas.devolucion_detalle')
            ->select(
                'devolucion_detalle.*',
                'devolucion.codigo as codigo_devolucion',
                'alm_prod.part_number',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.id_moneda',
                'alm_prod.id_unidad_medida',
                'alm_prod.series',
                'alm_und_medida.abreviatura'
            )
            ->join('cas.devolucion', 'devolucion.id_devolucion', '=', 'devolucion_detalle.id_devolucion')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'devolucion_detalle.id_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where([
                ['devolucion_detalle.id_devolucion', '=', $id_devolucion],
                ['devolucion_detalle.estado', '=', 1]
            ])->get();

        return response()->json($detalle);
    }

    public function devolucionNextId($fecha, $id_almacen)
    {
        $yyyy = date('Y', strtotime($fecha));

        $almacen = DB::table('almacen.alm_almacen')
            ->select('codigo')
            ->where('id_almacen', $id_almacen)
            ->first();

        $cantidad = DB::table('cas.devolucion')
            ->where([['id_almacen', '=', $id_almacen]])
            ->whereYear('fecha_registro', '=', $yyyy)
            ->get()->count();

        $val = GenericoAlmacenController::leftZero(3, ($cantidad + 1));
        $nextId = "DEV-" . $almacen->codigo . "-" . $yyyy . $val;

        return $nextId;
    }

    public function guardarDevolucion(Request $request)
    {
        try {
            DB::beginTransaction();
            $mensaje = '';
            $tipo = '';
            $fecha = new Carbon();

            $codigo = $this->devolucionNextId($fecha, $request->id_almacen);
            $usuario = Auth::user();
            $id_cliente = null;
            $id_proveedor = null;

            if ($request->tipo == 'cliente') {
                $cliente = DB::table('comercial.com_cliente')
                    ->where([
                        ['id_contribuyente', '=', $request->id_contribuyente],
                        ['estado', '=', 1]
                    ])
                    ->first();

                if ($cliente == null) {
                    $id_cliente = DB::table('comercial.com_cliente')
                        ->insertGetId([
                            'id_contribuyente' => $request->id_contribuyente,
                            'estado' => 1,
                            'fecha_registro' => new Carbon(),
                        ], 'id_cliente');
                } else {
                    $id_cliente = $cliente->id_cliente;
                }

                $proveedor = DB::table('logistica.log_prove')
                    ->where([
                        ['id_contribuyente', '=', $request->id_contribuyente],
                        ['estado', '=', 1]
                    ])
                    ->first();

                if ($proveedor == null) {
                    $id_proveedor = DB::table('logistica.log_prove')
                        ->insertGetId([
                            'id_contribuyente' => $request->id_contribuyente,
                            'estado' => 1,
                            'fecha_registro' => new Carbon(),
                        ], 'id_proveedor');
                } else {
                    $id_proveedor = $proveedor->id_proveedor;
                }
            } else if ($request->tipo == 'proveedor') {
                $proveedor = DB::table('logistica.log_prove')
                    ->where([
                        ['id_contribuyente', '=', $request->id_contribuyente],
                        ['estado', '=', 1]
                    ])
                    ->first();

                if ($proveedor == null) {
                    $id_proveedor = DB::table('logistica.log_prove')
                        ->insertGetId([
                            'id_contribuyente' => $request->id_contribuyente,
                            'estado' => 1,
                            'fecha_registro' => new Carbon(),
                        ], 'id_proveedor');
                } else {
                    $id_proveedor = $proveedor->id_proveedor;
                }
            }

            $id_devolucion = DB::table('cas.devolucion')->insertGetId(
                [
                    'codigo' => $codigo,
                    'id_tipo' => $request->id_tipo,
                    'id_almacen' => $request->id_almacen,
                    // 'id_moneda' => $request->id_moneda,
                    'id_cliente' => $id_cliente,
                    'id_proveedor' => $id_proveedor,
                    'observacion' => $request->observacion,
                    'registrado_por' => $usuario->id_usuario,
                    'estado' => 1,
                    'fecha_registro' => new Carbon(),
                ],
                'id_devolucion'
            );

            $items = json_decode($request->items);

            foreach ($items as $item) {
                $id_detalle = DB::table('cas.devolucion_detalle')->insertGetId(
                    [
                        'id_devolucion' => $id_devolucion,
                        'id_producto' => $item->id_producto,
                        'id_salida_detalle' => $item->id_salida_detalle,
                        'cantidad' => $item->cantidad,
                        // 'valor_unitario' => $item->unitario,
                        // 'valor_total' => round($item->total, 6, PHP_ROUND_HALF_UP),
                        'estado' => 1,
                        'fecha_registro' => new Carbon(),
                    ],
                    'id_detalle'
                );
            }

            $incidencias = json_decode($request->incidencias);

            foreach ($incidencias as $inc) {
                DB::table('cas.devolucion_incidencia')->insert(
                    [
                        'id_devolucion' => $id_devolucion,
                        'id_incidencia' => $inc->id_incidencia,
                        'estado' => 1,
                    ]
                );
            }

            $salidas = json_decode($request->salidas);

            foreach ($salidas as $sal) {
                DB::table('cas.devolucion_venta')->insert(
                    [
                        'id_devolucion' => $id_devolucion,
                        'id_salida' => $sal->id_salida,
                        'estado' => 1,
                    ]
                );
            }

            $devolucion = DB::table('cas.devolucion')->where('id_devolucion', $id_devolucion)->first();
            $mensaje = 'Se guardó la devolución correctamente';
            $tipo = 'success';

            DB::commit();

            return response()->json(['devolucion' => $devolucion, 'tipo' => $tipo, 'mensaje' => $mensaje]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    public function actualizarDevolucion(Request $request)
    {
        try {
            DB::beginTransaction();
            $usuario = Auth::user();
            $mensaje = '';
            $tipo = '';


            if ($request->tipo == 'cliente') {
                $cliente = DB::table('comercial.com_cliente')
                    ->where([
                        ['id_contribuyente', '=', $request->id_contribuyente],
                        ['estado', '=', 1]
                    ])
                    ->first();

                if ($cliente == null) {
                    $id_cliente = DB::table('comercial.com_cliente')
                        ->insertGetId([
                            'id_contribuyente' => $request->id_contribuyente,
                            'estado' => 1,
                            'fecha_registro' => new Carbon(),
                        ], 'id_cliente');
                } else {
                    $id_cliente = $cliente->id_cliente;
                }

                $proveedor = DB::table('logistica.log_prove')
                    ->where([
                        ['id_contribuyente', '=', $request->id_contribuyente],
                        ['estado', '=', 1]
                    ])
                    ->first();

                if ($proveedor == null) {
                    $id_proveedor = DB::table('logistica.log_prove')
                        ->insertGetId([
                            'id_contribuyente' => $request->id_contribuyente,
                            'estado' => 1,
                            'fecha_registro' => new Carbon(),
                        ], 'id_proveedor');
                } else {
                    $id_proveedor = $proveedor->id_proveedor;
                }
            } else if ($request->tipo == 'proveedor') {
                $proveedor = DB::table('logistica.log_prove')
                    ->where([
                        ['id_contribuyente', '=', $request->id_contribuyente],
                        ['estado', '=', 1]
                    ])
                    ->first();

                if ($proveedor == null) {
                    $id_proveedor = DB::table('logistica.log_prove')
                        ->insertGetId([
                            'id_contribuyente' => $request->id_contribuyente,
                            'estado' => 1,
                            'fecha_registro' => new Carbon(),
                        ], 'id_proveedor');
                } else {
                    $id_proveedor = $proveedor->id_proveedor;
                }
            }

            DB::table('cas.devolucion')
                ->where('id_devolucion', $request->id_devolucion)
                ->update([
                    'id_almacen' => $request->id_almacen,
                    'id_tipo' => $request->id_tipo,
                    'id_cliente' => $id_cliente,
                    'id_proveedor' => $id_proveedor,
                    'observacion' => $request->observacion,

                ]);

            $items = json_decode($request->items);

            foreach ($items as $item) {

                if ($item->id_detalle > 0) {

                    if ($item->estado == 7) {
                        DB::table('cas.devolucion_detalle')
                            ->where('id_detalle', $item->id_detalle)
                            ->update(['estado' => 7]);
                    } else {
                        DB::table('cas.devolucion_detalle')
                            ->where('id_detalle', $item->id_detalle)
                            ->update([
                                'id_producto' => $item->id_producto,
                                'cantidad' => $item->cantidad,
                            ]);
                    }
                } else {
                    DB::table('cas.devolucion_detalle')->insert(
                        [
                            'id_devolucion' => $request->id_devolucion,
                            'id_producto' => $item->id_producto,
                            'id_salida_detalle' => $item->id_salida_detalle,
                            'cantidad' => $item->cantidad,
                            'estado' => 1,
                            'fecha_registro' => new Carbon(),
                        ]
                    );
                }
            }

            $incidencias = json_decode($request->incidencias);

            foreach ($incidencias as $inc) {
                if ($inc->id > 0) {
                    if ($inc->estado == 7) {
                        DB::table('cas.devolucion_incidencia')
                            ->where('id', $inc->id)
                            ->update(['estado' => $inc->estado]);
                    }
                } else {
                    DB::table('cas.devolucion_incidencia')->insert(
                        [
                            'id_devolucion' => $request->id_devolucion,
                            'id_incidencia' => $inc->id_incidencia,
                            'estado' => 1,
                        ]
                    );
                }
            }

            $salidas = json_decode($request->salidas);

            foreach ($salidas as $sal) {
                if ($sal->id > 0) {
                    if ($sal->estado == 7) {
                        DB::table('cas.devolucion_venta')
                            ->where('id', $sal->id)
                            ->update(['estado' => $sal->estado]);
                    }
                } else {
                    DB::table('cas.devolucion_venta')->insert(
                        [
                            'id_devolucion' => $request->id_devolucion,
                            'id_salida' => $sal->id_salida,
                            'estado' => 1,
                        ]
                    );
                }
            }
            $devolucion = DB::table('cas.devolucion')->where('id_devolucion', $request->id_devolucion)->first();
            $mensaje = 'Se actualizó la devolución correctamente';
            $tipo = 'success';

            DB::commit();

            return response()->json(['devolucion' => $devolucion, 'tipo' => $tipo, 'mensaje' => $mensaje]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    function validarEdicion($id_devolucion)
    {
        $mov = DB::table('cas.devolucion')
            ->where('id_devolucion', $id_devolucion)
            ->first();
        //Si existe ingreso y salida relacionado
        if ($mov->estado == 1) {
            $mensaje = 'Ok';
            $tipo = 'success';
        } else if ($mov->estado == 2) {
            $mensaje = 'La devolución ya fue revisada.';
            $tipo = 'warning';
        } else if ($mov->estado == 3) {
            $mensaje = 'La devolución ya fue procesada.';
            $tipo = 'warning';
        }
        return response()->json(['tipo' => $tipo, 'mensaje' => $mensaje]);
    }

    function anularDevolucion($id_devolucion)
    {
        $mov = DB::table('cas.devolucion')
            ->where('id_devolucion', $id_devolucion)
            ->first();
        //Si existe ingreso y salida relacionado
        if ($mov->estado == 1) {
            DB::table('cas.devolucion')
                ->where('id_devolucion', $id_devolucion)
                ->update(['estado' => 7]);

            DB::table('cas.devolucion_detalle')
                ->where('id_devolucion', $id_devolucion)
                ->update(['estado' => 7]);

            $mensaje = 'Se anuló correctamente';
            $tipo = 'success';
        } else if ($mov->estado == 2) {
            $mensaje = 'La devolución ya fue revisada.';
            $tipo = 'warning';
        } else if ($mov->estado == 3) {
            $mensaje = 'La devolución ya fue procesada.';
            $tipo = 'warning';
        }
        return response()->json(['tipo' => $tipo, 'mensaje' => $mensaje]);
    }

    function guardarFichaTecnica(Request $request)
    {
        try {
            DB::beginTransaction();
            $mensaje = '';
            $tipo = '';

            //Guardar archivos subidos
            if ($request->hasFile('archivos')) {
                $archivos = $request->file('archivos');

                foreach ($archivos as $archivo) {
                    $id_ficha = DB::table('cas.devolucion_ficha')
                        ->insertGetId([
                            'id_devolucion' => $request->padre_id_devolucion,
                            'estado' => 1,
                        ], 'id_ficha');

                    //obtenemos el nombre del archivo
                    $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
                    $nombre = $request->padre_id_devolucion . '-' . $id_ficha . '-' . $archivo->getClientOriginalName();

                    //indicamos que queremos guardar un nuevo archivo en el disco local
                    File::delete(public_path('cas/devoluciones/fichas/' . $nombre));
                    Storage::disk('archivos')->put('cas/devoluciones/fichas/' . $nombre, File::get($archivo));

                    DB::table('cas.devolucion_ficha')
                        ->where('id_ficha', $id_ficha)
                        ->update(['adjunto' => $nombre]);
                }
            }

            $mensaje = 'Se guardó la ficha reporte correctamente';
            $tipo = 'success';

            DB::commit();
            return response()->json(['tipo' => $tipo, 'mensaje' => $mensaje]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    function conformidadDevolucion(Request $request)
    {
        try {
            DB::beginTransaction();

            $mensaje = '';
            $tipo = '';
            //valida segun BD
            $mov = DB::table('cas.devolucion')
                ->where('id_devolucion', $request->id_devolucion)
                ->first();
            //Si existe ingreso y salida relacionado
            if ($mov->estado == 1) {
                DB::table('cas.devolucion')
                    ->where('id_devolucion', $request->id_devolucion)
                    ->update([
                        'estado' => 2,
                        'revisado_por' => $request->responsable_revision,
                        'comentario_revision' => $request->comentario_revision,
                        'fecha_revision' => new Carbon(),
                    ]);
                $mensaje = 'Se dió la conformidad correctamente.';
                $tipo = 'success';
                //Revisada
            } else if ($mov->estado == 2) {
                $mensaje = 'La devolución ya fue revisada.';
                $tipo = 'warning';
                //Procesada
            } else if ($mov->estado == 3) {
                $mensaje = 'La devolución ya fue procesada.';
                $tipo = 'warning';
            }
            DB::commit();
            return response()->json(['tipo' => $tipo, 'mensaje' => $mensaje]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al procesar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    function revertirConformidad($id_devolucion)
    {
        try {
            DB::beginTransaction();
            $mensaje = '';
            $tipo = '';
            //valida segun BD
            $dev = DB::table('cas.devolucion')
                ->where('id_devolucion', $id_devolucion)
                ->first();
            //Si existe ingreso y salida relacionado
            if ($dev->estado !== 3) {
                DB::table('cas.devolucion')
                    ->where('id_devolucion', $id_devolucion)
                    ->update(['estado' => 1]);
                $mensaje = 'Se dió revertió correctamente.';
                $tipo = 'success';
                //Revisada
            } else {
                $mensaje = 'La devolución ya fue procesada.';
                $tipo = 'warning';
            }
            DB::commit();
            return response()->json(['tipo' => $tipo, 'mensaje' => $mensaje]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al procesar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    public function mostrarContribuyentes()
    {
        $lista = DB::table('contabilidad.adm_contri')
            ->select('adm_contri.*')
            ->where('estado', 1);

        return datatables($lista)->toJson();
    }


    function listarSalidasVenta($id_almacen, $id_contribuyente)
    {
        $lista = DB::table('almacen.mov_alm')
            ->select(
                'mov_alm.*',
                DB::raw("(concat(guia_ven.serie,'-',guia_ven.numero) ) as serie_numero_guia"),
                'adm_contri.id_contribuyente',
                'adm_contri.razon_social',
                'alm_almacen.descripcion as almacen_descripcion',
                DB::raw("(select concat(dv.serie,'-', dv.numero) from almacen.doc_ven as dv 
                inner join almacen.doc_ven_det as d on(
                d.id_doc=dv.id_doc_ven)
                inner join almacen.guia_ven_det as g on(
                g.id_guia_ven_det=d.id_guia_ven_det)
                where g.id_guia_ven=mov_alm.id_guia_ven
                group by concat(dv.serie,'-', dv.numero)
                limit 1) AS serie_numero_doc")
            )
            ->join('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'mov_alm.id_guia_ven')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'guia_ven.id_almacen')
            ->join('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'guia_ven.id_cliente')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->where('adm_contri.id_contribuyente', $id_contribuyente)
            ->where('mov_alm.id_almacen', $id_almacen)
            ->where('mov_alm.id_tp_mov', 2)
            ->where('mov_alm.estado', 1)
            ->where('mov_alm.id_operacion', 1)->get();

        return datatables($lista)->toJson();
        // return response()->json($lista);
    }

    function obtenerSalidaDetalle($id_salida)
    {
        $lista = DB::table('almacen.mov_alm_det')
            ->select(
                'mov_alm_det.id_mov_alm_det',
                'mov_alm_det.id_producto',
                'mov_alm_det.cantidad',
                'mov_alm_det.valorizacion',
                'mov_alm_det.id_mov_alm',
                'alm_prod.codigo',
                'alm_prod.part_number',
                'alm_prod.descripcion',
                'alm_und_medida.abreviatura',
                'alm_prod.id_moneda',
                'alm_prod.series'
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where('mov_alm_det.id_mov_alm', $id_salida)
            ->get();
        return response()->json($lista);
    }
}
