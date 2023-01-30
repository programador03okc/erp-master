<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use App\Http\Controllers\Almacen\Ubicacion\AlmacenController;
use App\Http\Controllers\AlmacenController as GenericoAlmacenController;
use App\Http\Controllers\Tesoreria\CierreAperturaController as CierreAperturaController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Configuracion\AccesosUsuarios;
use App\Models\Presupuestos\Moneda;
use App\Models\Tesoreria\TipoCambio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomizacionController extends Controller
{
    function viewCustomizacion()
    {
        $almacenes = AlmacenController::mostrar_almacenes_cbo();
        $empresas = GenericoAlmacenController::select_empresa();
        $unidades = GenericoAlmacenController::mostrar_unidades_cbo();
        $usuarios = GenericoAlmacenController::select_usuarios();
        $monedas = Moneda::where('estado', 1)->get();
        // $array_accesos = [];
        // $accesos_usuario = AccesosUsuarios::where('estado', 1)->where('id_usuario', Auth::user()->id_usuario)->get();
        // foreach ($accesos_usuario as $key => $value) {
        //     array_push($array_accesos, $value->id_acceso);
        // }
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
        return view('almacen/customizacion/customizacion', compact('almacenes', 'empresas', 'usuarios', 'unidades', 'monedas','array_accesos'));
        // return view('almacen/customizacion/customizacion', compact('almacenes', 'empresas', 'usuarios', 'unidades', 'monedas', 'array_accesos'));
    }

    public function obtenerTipoCambio($fecha, $id_moneda)
    {
        $tipo_cambio = TipoCambio::where([['moneda', '=', $id_moneda], ['fecha', '<=', $fecha]])
            ->orderBy('fecha', 'DESC')->first();
        return response()->json($tipo_cambio);
    }

    public function transformacion_nextId($fecha, $id_almacen)
    {
        $yyyy = date('Y', strtotime($fecha));

        $almacen = DB::table('almacen.alm_almacen')
            ->select('codigo')
            ->where('id_almacen', $id_almacen)
            ->first();

        $cantidad = DB::table('almacen.transformacion')
            ->where([['id_almacen', '=', $id_almacen], ['tipo', '=', "C"]])
            ->whereYear('fecha_transformacion', '=', $yyyy)
            ->get()->count();

        $val = GenericoAlmacenController::leftZero(3, ($cantidad + 1));
        $nextId = "C-" . $almacen->codigo . "-" . $yyyy . $val;

        return $nextId;
    }

    public function mostrarCustomizacion($id_transformacion)
    {
        $data = DB::table('almacen.transformacion')
            ->select(
                'transformacion.*',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'sis_usua.nombre_corto',
                'registrado.nombre_corto as registrado_por_nombre',
                'alm_almacen.descripcion as almacen_descripcion',
            )
            ->leftjoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'transformacion.id_almacen')
            ->leftjoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'transformacion.estado')
            ->leftjoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'transformacion.responsable')
            ->leftjoin('configuracion.sis_usua as registrado', 'registrado.id_usuario', '=', 'transformacion.registrado_por')
            ->where('transformacion.id_transformacion', $id_transformacion)
            ->first();

        $listaBases = DB::table('almacen.transfor_materia')
            ->select(
                'transfor_materia.id_materia',
                'transfor_materia.id_producto',
                'transfor_materia.costo_promedio',
                'transfor_materia.cantidad',
                'transfor_materia.valor_unitario as unitario',
                'transfor_materia.valor_total as total',
                'transfor_materia.estado',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.part_number',
                'alm_prod.id_moneda',
                'alm_und_medida.abreviatura as unid_med',
                'alm_prod.series',
            )
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'transfor_materia.id_producto')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where([
                ['transfor_materia.id_transformacion', '=', $id_transformacion],
                ['transfor_materia.estado', '!=', 7]
            ])
            ->get();

        $bases = [];

        foreach ($listaBases as $b) {
            $series = DB::table('almacen.alm_prod_serie')
                ->select('alm_prod_serie.*')
                ->where([
                    ['alm_prod_serie.id_base', '=', $b->id_materia],
                    ['alm_prod_serie.estado', '!=', 7]
                ])
                ->get();

            array_push(
                $bases,
                [
                    'id_materia' => $b->id_materia,
                    'id_producto' => $b->id_producto,
                    'costo_promedio' => $b->costo_promedio,
                    'cantidad' => $b->cantidad,
                    'id_moneda' => $b->id_moneda,
                    'unitario' => $b->unitario,
                    'total' => $b->total,
                    'estado' => $b->estado,
                    'codigo' => $b->codigo,
                    'descripcion' => $b->descripcion,
                    'part_number' => $b->part_number,
                    'unid_med' => $b->unid_med,
                    'control_series' => $b->series,
                    'series' => $series,
                ]
            );
        }

        $listaSobrantes = DB::table('almacen.transfor_sobrante')
            ->select(
                'transfor_sobrante.id_sobrante',
                'transfor_sobrante.id_producto',
                'transfor_sobrante.cantidad',
                'transfor_sobrante.valor_unitario as unitario',
                'transfor_sobrante.valor_total as total',
                'transfor_sobrante.estado',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.part_number',
                'alm_prod.id_moneda',
                'alm_und_medida.abreviatura as unid_med',
                'alm_prod.series',
            )
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'transfor_sobrante.id_producto')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where([
                ['transfor_sobrante.id_transformacion', '=', $id_transformacion],
                ['transfor_sobrante.estado', '!=', 7]
            ])
            ->get();

        $sobrantes = [];

        foreach ($listaSobrantes as $b) {
            $series = DB::table('almacen.alm_prod_serie')
                ->select('alm_prod_serie.*')
                ->where([
                    ['alm_prod_serie.id_sobrante', '=', $b->id_sobrante],
                    ['alm_prod_serie.estado', '!=', 7]
                ])
                ->get();

            array_push(
                $sobrantes,
                [
                    'id_sobrante' => $b->id_sobrante,
                    'id_producto' => $b->id_producto,
                    'cantidad' => $b->cantidad,
                    'id_moneda' => $b->id_moneda,
                    'unitario' => $b->unitario,
                    'total' => $b->total,
                    'estado' => $b->estado,
                    'codigo' => $b->codigo,
                    'descripcion' => $b->descripcion,
                    'part_number' => $b->part_number,
                    'unid_med' => $b->unid_med,
                    'control_series' => $b->series,
                    'series' => $series,
                ]
            );
        }

        $listaTransformados = DB::table('almacen.transfor_transformado')
            ->select(
                'transfor_transformado.id_transformado',
                'transfor_transformado.id_producto',
                'transfor_transformado.cantidad',
                'transfor_transformado.valor_unitario as unitario',
                'transfor_transformado.valor_total as total',
                'transfor_transformado.estado',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.part_number',
                'alm_prod.id_moneda',
                'alm_und_medida.abreviatura as unid_med',
                'alm_prod.series',
            )
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'transfor_transformado.id_producto')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->where([
                ['transfor_transformado.id_transformacion', '=', $id_transformacion],
                ['transfor_transformado.estado', '!=', 7]
            ])
            ->get();

        $transformados = [];

        foreach ($listaTransformados as $b) {
            $series = DB::table('almacen.alm_prod_serie')
                ->select('alm_prod_serie.*')
                ->where([
                    ['alm_prod_serie.id_transformado', '=', $b->id_transformado],
                    ['alm_prod_serie.estado', '!=', 7]
                ])
                ->get();

            array_push(
                $transformados,
                [
                    'id_transformado' => $b->id_transformado,
                    'id_producto' => $b->id_producto,
                    'cantidad' => $b->cantidad,
                    'id_moneda' => $b->id_moneda,
                    'unitario' => $b->unitario,
                    'total' => $b->total,
                    'estado' => $b->estado,
                    'codigo' => $b->codigo,
                    'descripcion' => $b->descripcion,
                    'part_number' => $b->part_number,
                    'unid_med' => $b->unid_med,
                    'control_series' => $b->series,
                    'series' => $series,
                ]
            );
        }

        $ingreso = DB::table('almacen.mov_alm')
            ->select('mov_alm.id_mov_alm')
            ->where([
                ['id_transformacion', '=', $id_transformacion],
                ['id_tp_mov', '=', 1],
                ['estado', '=', 1]
            ])
            ->first();

        $salida = DB::table('almacen.mov_alm')
            ->select('mov_alm.id_mov_alm')
            ->where([
                ['id_transformacion', '=', $id_transformacion],
                ['id_tp_mov', '=', 2],
                ['estado', '=', 1]
            ])
            ->first();

        return response()->json([
            'customizacion' => $data, 'bases' => $bases, 'sobrantes' => $sobrantes, 'transformados' => $transformados,
            'id_ingreso' => ($ingreso !== null ? $ingreso->id_mov_alm : 0), 'id_salida' => ($salida !== null ? $salida->id_mov_alm : 0)
        ]);
    }

    public function guardarCustomizacion(Request $request)
    {
        try {
            DB::beginTransaction();
            $mensaje = '';
            $tipo = '';
            $customizacion = null;

            $periodo_estado = CierreAperturaController::consultarPeriodo($request->fecha_proceso);

            if (intval($periodo_estado) == 2){
                $mensaje = 'El periodo esta cerrado. Consulte con contabilidad.';
                $tipo = 'warning';
            } else {

                $codigo = $this->transformacion_nextId($request->fecha_proceso, $request->id_almacen);
                $usuario = Auth::user();
    
                $id_transformacion = DB::table('almacen.transformacion')->insertGetId(
                    [
                        'fecha_transformacion' => $request->fecha_proceso,
                        'fecha_inicio' => $request->fecha_proceso,
                        'fecha_entrega' => $request->fecha_proceso,
                        // 'serie' => $request->serie,
                        // 'numero' => $request->numero,
                        'codigo' => $codigo,
                        'tipo' => "C",
                        'responsable' => $request->id_usuario,
                        'id_almacen' => $request->id_almacen,
                        'id_moneda' => $request->id_moneda,
                        'tipo_cambio' => $request->tipo_cambio,
                        'observacion' => $request->observacion,
                        'registrado_por' => $usuario->id_usuario,
                        'estado' => 1,
                        'fecha_registro' => new Carbon(),
                    ],
                    'id_transformacion'
                );
    
                $items_base = json_decode($request->items_base);
    
                foreach ($items_base as $item) {
                    $id_materia = DB::table('almacen.transfor_materia')->insertGetId(
                        [
                            'id_transformacion' => $id_transformacion,
                            'id_producto' => $item->id_producto,
                            'cantidad' => $item->cantidad,
                            'costo_promedio' => $item->costo_promedio,
                            'valor_unitario' => $item->unitario,
                            'valor_total' => round($item->total, 6, PHP_ROUND_HALF_UP),
                            'estado' => 1,
                            'fecha_registro' => new Carbon(),
                        ],
                        'id_materia'
                    );
                    if ($item->series !== null && $item->series !== []) {
                        //agrega series
                        foreach ($item->series as $serie) {
                            if ($serie->estado == 1) {
                                DB::table('almacen.alm_prod_serie')
                                    ->where('id_prod_serie', $serie->id_prod_serie)
                                    ->update(['id_base' => $id_materia]);
                            }
                        }
                    }
                }
    
                $items_transformado = json_decode($request->items_transformado);
    
                foreach ($items_transformado as $item) {
                    $id_transformado = DB::table('almacen.transfor_transformado')->insertGetId(
                        [
                            'id_transformacion' => $id_transformacion,
                            'id_producto' => $item->id_producto,
                            'cantidad' => $item->cantidad,
                            'valor_unitario' => $item->unitario,
                            'valor_total' => round($item->total, 6, PHP_ROUND_HALF_UP),
                            'estado' => 1,
                            'fecha_registro' => new Carbon(),
                        ],
                        'id_transformado'
                    );
    
                    if ($item->series !== null && $item->series !== []) {
                        //agrega series
                        foreach ($item->series as $serie) {
                            DB::table('almacen.alm_prod_serie')->insert(
                                [
                                    'id_prod' => $item->id_producto,
                                    'id_almacen' => $request->id_almacen,
                                    'serie' => $serie->serie,
                                    'estado' => 1,
                                    'fecha_registro' => new Carbon(),
                                    'id_transformado' => $id_transformado
                                ]
                            );
                        }
                    }
                }
    
                $items_sobrante = json_decode($request->items_sobrante);
    
                foreach ($items_sobrante as $item) {
                    $id_sobrante = DB::table('almacen.transfor_sobrante')->insertGetId(
                        [
                            'id_transformacion' => $id_transformacion,
                            'id_producto' => $item->id_producto,
                            'cantidad' => $item->cantidad,
                            'valor_unitario' => $item->unitario,
                            'valor_total' => round($item->total, 6, PHP_ROUND_HALF_UP),
                            'estado' => 1,
                            'fecha_registro' => new Carbon(),
                        ],
                        'id_sobrante'
                    );
    
                    if ($item->series !== null && $item->series !== []) {
                        //agrega series
                        foreach ($item->series as $serie) {
                            DB::table('almacen.alm_prod_serie')->insert(
                                [
                                    'id_prod' => $item->id_producto,
                                    'id_almacen' => $request->id_almacen,
                                    'serie' => $serie->serie,
                                    'estado' => 1,
                                    'fecha_registro' => new Carbon(),
                                    'id_sobrante' => $id_sobrante
                                ]
                            );
                        }
                    }
                }
                $customizacion = DB::table('almacen.transformacion')->where('id_transformacion', $id_transformacion)->first();
                $mensaje = 'Se guardó la customización correctamente';
                $tipo = 'success';
            }

            DB::commit();

            return response()->json(['customizacion' => $customizacion, 'tipo' => $tipo, 'mensaje' => $mensaje]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    public function actualizarCustomizacion(Request $request)
    {
        try {
            DB::beginTransaction();
            $mensaje = '';
            $tipo = '';
            $customizacion = null;

            $periodo_estado = CierreAperturaController::consultarPeriodo($request->fecha_proceso);

            if (intval($periodo_estado) == 2){
                $mensaje = 'El periodo esta cerrado. Consulte con contabilidad.';
                $tipo = 'warning';
            } else {

                DB::table('almacen.transformacion')
                    ->where('id_transformacion', $request->id_customizacion)
                    ->update([
                        'fecha_transformacion' => $request->fecha_proceso,
                        'fecha_inicio' => $request->fecha_proceso,
                        'fecha_entrega' => $request->fecha_proceso,
                        'responsable' => $request->id_usuario,
                        'id_almacen' => $request->id_almacen,
                        'id_moneda' => $request->id_moneda,
                        'tipo_cambio' => $request->tipo_cambio,
                        'observacion' => $request->observacion,
                    ]);
    
                $items_base = json_decode($request->items_base);
    
                foreach ($items_base as $item) {
    
                    if ($item->id_materia > 0) {
    
                        if ($item->estado == 7) {
                            DB::table('almacen.transfor_materia')
                                ->where('id_materia', $item->id_materia)
                                ->update(['estado' => 7]);
    
                            //elimina las series del id_base
                            DB::table('almacen.alm_prod_serie')
                                ->where('id_base', $item->id_materia)
                                ->update(['id_base' => null]);
                        } else {
                            DB::table('almacen.transfor_materia')
                                ->where('id_materia', $item->id_materia)
                                ->update([
                                    'cantidad' => $item->cantidad,
                                    'costo_promedio' => $item->costo_promedio,
                                    'valor_unitario' => $item->unitario,
                                    'valor_total' => round($item->total, 6, PHP_ROUND_HALF_UP),
                                ]);
    
                            if ($item->series !== null && $item->series !== []) {
                                //elimina las series del id_base
                                DB::table('almacen.alm_prod_serie')
                                    ->where('id_base', $item->id_materia)
                                    ->update(['id_base' => null]);
    
                                //agrega series
                                foreach ($item->series as $serie) {
                                    if ($serie->estado == 1) {
                                        DB::table('almacen.alm_prod_serie')
                                            ->where('id_prod_serie', $serie->id_prod_serie)
                                            ->update(['id_base' => $item->id_materia]);
                                    }
                                }
                            }
                        }
                    } else {
                        $id_materia = DB::table('almacen.transfor_materia')->insertGetId(
                            [
                                'id_transformacion' => $request->id_customizacion,
                                'id_producto' => $item->id_producto,
                                'cantidad' => $item->cantidad,
                                'costo_promedio' => $item->costo_promedio,
                                'valor_unitario' => $item->unitario,
                                'valor_total' => round($item->total, 6, PHP_ROUND_HALF_UP),
                                'estado' => 1,
                                'fecha_registro' => new Carbon(),
                            ],
                            'id_materia'
                        );
    
                        if ($item->series !== null && $item->series !== []) {
                            //agrega series
                            foreach ($item->series as $serie) {
                                if ($serie->estado == 1) {
                                    DB::table('almacen.alm_prod_serie')
                                        ->where('id_prod_serie', $serie->id_prod_serie)
                                        ->update(['id_base' => $id_materia]);
                                }
                            }
                        }
                    }
                }
    
                $items_transformado = json_decode($request->items_transformado);
    
                foreach ($items_transformado as $item) {
    
                    if ($item->id_transformado > 0) {
    
                        if ($item->estado == 7) {
                            DB::table('almacen.transfor_transformado')
                                ->where('id_transformado', $item->id_transformado)
                                ->update(['estado' => 7]);
    
                            //elimina las series del id_base
                            DB::table('almacen.alm_prod_serie')
                                ->where('id_transformado', $item->id_transformado)
                                ->update(['estado' => 7]);
                        } else {
                            DB::table('almacen.transfor_transformado')
                                ->where('id_transformado', $item->id_transformado)
                                ->update([
                                    'cantidad' => $item->cantidad,
                                    'valor_unitario' => $item->unitario,
                                    'valor_total' => round($item->total, 6, PHP_ROUND_HALF_UP),
                                ]);
    
                            if ($item->series !== null && $item->series !== []) {
                                //elimina las series del id_base
                                DB::table('almacen.alm_prod_serie')
                                    ->where('id_transformado', $item->id_transformado)
                                    ->update(['estado' => 7]);
    
                                //agrega series
                                foreach ($item->series as $serie) {
                                    // if ($serie->estado == 1) {
                                    DB::table('almacen.alm_prod_serie')->insert(
                                        [
                                            'id_prod' => $item->id_producto,
                                            'id_almacen' => $request->id_almacen,
                                            'serie' => $serie->serie,
                                            'estado' => 1,
                                            'fecha_registro' => new Carbon(),
                                            'id_transformado' => $item->id_transformado
                                        ]
                                    );
                                    // }
                                }
                            }
                        }
                    } else {
                        $id_transformado = DB::table('almacen.transfor_transformado')->insertGetId(
                            [
                                'id_transformacion' => $request->id_customizacion,
                                'id_producto' => $item->id_producto,
                                'cantidad' => $item->cantidad,
                                'valor_unitario' => $item->unitario,
                                'valor_total' => round($item->total, 6, PHP_ROUND_HALF_UP),
                                'estado' => 1,
                                'fecha_registro' => new Carbon(),
                            ],
                            'id_transformado'
                        );
    
                        if ($item->series !== null && $item->series !== []) {
                            //agrega series
                            foreach ($item->series as $serie) {
                                // if ($serie->estado == 1) {
                                DB::table('almacen.alm_prod_serie')->insert(
                                    [
                                        'id_prod' => $item->id_producto,
                                        'id_almacen' => $request->id_almacen,
                                        'serie' => $serie->serie,
                                        'estado' => 1,
                                        'fecha_registro' => new Carbon(),
                                        'id_transformado' => $id_transformado
                                    ]
                                );
                                // }
                            }
                        }
                    }
                }
    
                $items_sobrante = json_decode($request->items_sobrante);
    
                foreach ($items_sobrante as $item) {
    
                    if ($item->id_sobrante > 0) {
                        if ($item->estado == 7) {
                            DB::table('almacen.transfor_sobrante')
                                ->where('id_sobrante', $item->id_sobrante)
                                ->update(['estado' => 7]);
    
                            //elimina las series del id_base
                            DB::table('almacen.alm_prod_serie')
                                ->where('id_sobrante', $item->id_sobrante)
                                ->update(['estado' => 7]);
                        } else {
                            DB::table('almacen.transfor_sobrante')
                                ->where('id_sobrante', $item->id_sobrante)
                                ->update([
                                    'cantidad' => $item->cantidad,
                                    'valor_unitario' => $item->unitario,
                                    'valor_total' => round($item->total, 6, PHP_ROUND_HALF_UP),
                                ]);
    
                            if ($item->series !== null && $item->series !== []) {
                                //elimina las series del id_base
                                DB::table('almacen.alm_prod_serie')
                                    ->where('id_sobrante', $item->id_sobrante)
                                    ->update(['estado' => 7]);
    
                                //agrega series
                                foreach ($item->series as $serie) {
                                    // if ($serie->estado == 1) {
                                    DB::table('almacen.alm_prod_serie')->insert(
                                        [
                                            'id_prod' => $item->id_producto,
                                            'id_almacen' => $request->id_almacen,
                                            'serie' => $serie->serie,
                                            'estado' => 1,
                                            'fecha_registro' => new Carbon(),
                                            'id_sobrante' => $item->id_sobrante
                                        ]
                                    );
                                    // }
                                }
                            }
                        }
                    } else {
                        DB::table('almacen.transfor_sobrante')->insert(
                            [
                                'id_transformacion' => $request->id_customizacion,
                                'id_producto' => $item->id_producto,
                                'cantidad' => $item->cantidad,
                                'valor_unitario' => $item->unitario,
                                'valor_total' => round($item->total, 6, PHP_ROUND_HALF_UP),
                                'estado' => 1,
                                'fecha_registro' => new Carbon(),
                            ]
                        );
                        if ($item->series !== null && $item->series !== []) {
                            //agrega series
                            foreach ($item->series as $serie) {
                                // if ($serie->estado == 1) {
                                DB::table('almacen.alm_prod_serie')->insert(
                                    [
                                        'id_prod' => $item->id_producto,
                                        'id_almacen' => $request->id_almacen,
                                        'serie' => $serie->serie,
                                        'estado' => 1,
                                        'fecha_registro' => new Carbon(),
                                        'id_sobrante' => $item->id_sobrante
                                    ]
                                );
                                // }
                            }
                        }
                    }
                }
                $customizacion = DB::table('almacen.transformacion')->where('id_transformacion', $request->id_customizacion)->first();
                $mensaje = 'Se actualizó la customización correctamente';
                $tipo = 'success';
            }

            DB::commit();

            return response()->json(['customizacion' => $customizacion, 'tipo' => $tipo, 'mensaje' => $mensaje]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    public function actualizarCostosBase(Request $request)
    {
        try {
            DB::beginTransaction();
            $items_base = json_decode($request->items_base);

            foreach ($items_base as $item) {
                $item->costo_promedio = (new SalidaPdfController())->obtenerCostoPromedioSalida($item->id_producto, $item->id_almacen, '2022-01-01', new Carbon());
            }
            $mensaje = 'Se actualizaron los costos correctamente';
            $tipo = 'success';

            DB::commit();
            return response()->json(['items_base' => $items_base, 'tipo' => $tipo, 'mensaje' => $mensaje]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al actualizar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    public function anularCustomizacion($id_transformacion)
    {
        try {
            DB::beginTransaction();
            $mensaje = '';
            $tipo = '';

            $mov = DB::table('almacen.mov_alm')
                ->where([
                    ['id_transformacion', '=', $id_transformacion],
                    ['estado', '=', 1]
                ])
                ->get();

            //Si existe ingreso y salida relacionado
            if (count($mov) > 0) {
                $mensaje = 'No es posible anular. La customización ya fue procesada.';
                $tipo = 'warning';
            } else {
                DB::table('almacen.transformacion')
                    ->where('id_transformacion', $id_transformacion)
                    ->update(['estado' => 7]);

                DB::table('almacen.transfor_materia')
                    ->where('id_transformacion', $id_transformacion)
                    ->update(['estado' => 7]);

                $bases = DB::table('almacen.transfor_materia')
                    ->where('id_transformacion', $id_transformacion)
                    ->get();

                foreach ($bases as $trans) {
                    //elimina las series del id_base
                    DB::table('almacen.alm_prod_serie')
                        ->where('id_base', $trans->id_materia)
                        ->update(['id_base' => null]);
                }

                DB::table('almacen.transfor_transformado')
                    ->where('id_transformacion', $id_transformacion)
                    ->update(['estado' => 7]);

                $transformaciones = DB::table('almacen.transfor_transformado')
                    ->where('id_transformacion', $id_transformacion)
                    ->get();

                foreach ($transformaciones as $trans) {
                    //elimina las series del id_base
                    DB::table('almacen.alm_prod_serie')
                        ->where('id_transformado', $trans->id_transformado)
                        ->update(['estado' => 7]);
                }

                DB::table('almacen.transfor_sobrante')
                    ->where('id_transformacion', $id_transformacion)
                    ->update(['estado' => 7]);

                $sobrantes = DB::table('almacen.transfor_sobrante')
                    ->where('id_transformacion', $id_transformacion)
                    ->get();

                foreach ($sobrantes as $sob) {
                    //elimina las series del id_base
                    DB::table('almacen.alm_prod_serie')
                        ->where('id_sobrante', $sob->id_sobrante)
                        ->update(['estado' => 7]);
                }

                $mensaje = 'La customización se anuló correctamente.';
                $tipo = 'success';
            }

            DB::commit();
            return response()->json(['tipo' => $tipo, 'mensaje' => $mensaje]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al anular. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    public function procesarCustomizacion($id_transformacion)
    {
        try {
            DB::beginTransaction();

            $mensaje = '';
            $tipo = '';
            $id_ingreso = null;
            $id_salida = null;

            $transformacion = DB::table('almacen.transformacion')
                ->where('id_transformacion', $id_transformacion)
                ->first();

            $mov = DB::table('almacen.mov_alm')
                ->where([
                    ['id_transformacion', '=', $id_transformacion],
                    ['estado', '=', 1]
                ])
                ->get();

            //Si existe ingreso y salida relacionado
            if (count($mov) > 0) {
                $mensaje = 'La customización ya fue procesada.';
                $tipo = 'warning';
            } else {
                DB::table('almacen.transformacion')
                    ->where('id_transformacion', $id_transformacion)
                    ->update([
                        'estado' => 10, //Culminado
                        // 'fecha_transformacion' => new Carbon()
                    ]);
                //Genera el codigo de la salida
                $codigo = GenericoAlmacenController::nextMovimiento(
                    2, //salida
                    $transformacion->fecha_transformacion,
                    $transformacion->id_almacen
                );
                $operacion = 27; //SALIDA PARA SERVICIO DE PRODUCCION
                $id_usuario = Auth::user()->id_usuario;

                $id_salida = DB::table('almacen.mov_alm')->insertGetId(
                    [
                        'id_almacen' => $transformacion->id_almacen,
                        'id_tp_mov' => 2, //Salidas
                        'codigo' => $codigo,
                        'fecha_emision' => $transformacion->fecha_transformacion,
                        // 'id_guia_ven' => $id_guia_ven,
                        'id_operacion' => $operacion,
                        'id_transformacion' => $id_transformacion,
                        'revisado' => 0,
                        'usuario' => $id_usuario,
                        'estado' => 1,
                        'fecha_registro' => new Carbon(),
                    ],
                    'id_mov_alm'
                );

                $bases = DB::table('almacen.transfor_materia')
                    ->select('transfor_materia.*')
                    ->where([
                        ['transfor_materia.id_transformacion', '=', $id_transformacion],
                        ['transfor_materia.estado', '!=', 7]
                    ])
                    ->get();

                foreach ($bases as $item) {

                    //Guardo los items de la salida
                    DB::table('almacen.mov_alm_det')->insertGetId(
                        [
                            'id_mov_alm' => $id_salida,
                            'id_producto' => $item->id_producto,
                            'cantidad' => $item->cantidad,
                            'costo_promedio' => $item->valor_unitario,
                            'valorizacion' => $item->valor_total,
                            'usuario' => $id_usuario,
                            'id_materia' => $item->id_materia,
                            'estado' => 1,
                            'fecha_registro' => new Carbon(),
                        ],
                        'id_mov_alm_det'
                    );
                    //Obtengo el registro de la tabla producto ubicacion
                    $ubi = DB::table('almacen.alm_prod_ubi')
                        ->where([
                            ['id_producto', '=', $item->id_producto],
                            ['id_almacen', '=', $transformacion->id_almacen]
                        ])
                        ->first();
                    //si no existe guardo una nueva relacion
                    if ($ubi == null) {
                        DB::table('almacen.alm_prod_ubi')->insert([
                            'id_producto' => $item->id_producto,
                            'id_almacen' => $transformacion->id_almacen,
                            'stock' => 0,
                            'valorizacion' => 0,
                            'costo_promedio' => 0,
                            'estado' => 1,
                            'fecha_registro' => date('Y-m-d H:i:s')
                        ]);
                    }
                }

                //Genera el codigo de ingreso
                $codigo = GenericoAlmacenController::nextMovimiento(
                    1, //ingreso
                    $transformacion->fecha_transformacion,
                    $transformacion->id_almacen
                );
                $operacion = 26; //INGRESO POR SERVICIO DE PRODUCCION

                $id_ingreso = DB::table('almacen.mov_alm')->insertGetId(
                    [
                        'id_almacen' => $transformacion->id_almacen,
                        'id_tp_mov' => 1, //ingreso
                        'codigo' => $codigo,
                        'fecha_emision' => $transformacion->fecha_transformacion,
                        // 'id_guia_ven' => $id_guia_ven,
                        'id_operacion' => $operacion,
                        'id_transformacion' => $id_transformacion,
                        'revisado' => 0,
                        'usuario' => $id_usuario,
                        'estado' => 1,
                        'fecha_registro' => new Carbon(),
                    ],
                    'id_mov_alm'
                );

                $sobrantes = DB::table('almacen.transfor_sobrante')
                    ->select('transfor_sobrante.*', 'alm_prod.id_moneda')
                    ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'transfor_sobrante.id_producto')
                    ->where([
                        ['transfor_sobrante.id_transformacion', '=', $id_transformacion],
                        ['transfor_sobrante.estado', '!=', 7]
                    ])
                    ->get();

                foreach ($sobrantes as $item) {
                    $unitario = 0;

                    if ($item->id_moneda == $transformacion->id_moneda) {
                        $unitario = floatval($item->valor_unitario);
                    } else {
                        if ($item->id_moneda == 1) { //soles
                            $unitario = floatval($item->valor_unitario) * floatval($transformacion->tipo_cambio);
                        } else { //dolares
                            $unitario = floatval($item->valor_unitario) / floatval($transformacion->tipo_cambio);
                        }
                    }
                    //Guardo los items del ingreso
                    DB::table('almacen.mov_alm_det')->insertGetId(
                        [
                            'id_mov_alm' => $id_ingreso,
                            'id_producto' => $item->id_producto,
                            'cantidad' => $item->cantidad,
                            'costo_promedio' => $unitario,
                            'valorizacion' => ($unitario * floatval($item->cantidad)),
                            'usuario' => $id_usuario,
                            'id_sobrante' => $item->id_sobrante,
                            'estado' => 1,
                            'fecha_registro' => new Carbon(),
                        ],
                        'id_mov_alm_det'
                    );

                    //Obtengo el registro de la tabla producto ubicacion
                    $ubi = DB::table('almacen.alm_prod_ubi')
                        ->where([
                            ['id_producto', '=', $item->id_producto],
                            ['id_almacen', '=', $transformacion->id_almacen]
                        ])
                        ->first();
                    //si no existe guardo una nueva relacion
                    if ($ubi == null) {
                        DB::table('almacen.alm_prod_ubi')->insert([
                            'id_producto' => $item->id_producto,
                            'id_almacen' => $transformacion->id_almacen,
                            'stock' => 0,
                            'valorizacion' => 0,
                            'costo_promedio' => 0,
                            'estado' => 1,
                            'fecha_registro' => date('Y-m-d H:i:s')
                        ]);
                    }
                }

                $transformados = DB::table('almacen.transfor_transformado')
                    ->select('transfor_transformado.*', 'alm_prod.id_moneda')
                    ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'transfor_transformado.id_producto')
                    ->where([
                        ['transfor_transformado.id_transformacion', '=', $id_transformacion],
                        ['transfor_transformado.estado', '!=', 7]
                    ])
                    ->get();

                foreach ($transformados as $item) {
                    $unitario = 0;

                    if ($item->id_moneda == $transformacion->id_moneda) {
                        $unitario = floatval($item->valor_unitario);
                    } else {
                        if ($item->id_moneda == 1) { //soles
                            $unitario = floatval($item->valor_unitario) * floatval($transformacion->tipo_cambio);
                        } else { //dolares
                            $unitario = floatval($item->valor_unitario) / floatval($transformacion->tipo_cambio);
                        }
                    }
                    //Guardo los items del ingreso
                    DB::table('almacen.mov_alm_det')->insertGetId(
                        [
                            'id_mov_alm' => $id_ingreso,
                            'id_producto' => $item->id_producto,
                            'cantidad' => $item->cantidad,
                            'costo_promedio' => $unitario,
                            'valorizacion' => ($unitario * floatval($item->cantidad)),
                            'usuario' => $id_usuario,
                            'id_transformado' => $item->id_transformado,
                            'estado' => 1,
                            'fecha_registro' => new Carbon(),
                        ],
                        'id_mov_alm_det'
                    );
                    //Obtengo el registro de la tabla producto ubicacion
                    $ubi = DB::table('almacen.alm_prod_ubi')
                        ->where([
                            ['id_producto', '=', $item->id_producto],
                            ['id_almacen', '=', $transformacion->id_almacen]
                        ])
                        ->first();
                    //si no existe guardo una nueva relacion
                    if ($ubi == null) {
                        DB::table('almacen.alm_prod_ubi')->insert([
                            'id_producto' => $item->id_producto,
                            'id_almacen' => $transformacion->id_almacen,
                            'stock' => 0,
                            'valorizacion' => 0,
                            'costo_promedio' => 0,
                            'estado' => 1,
                            'fecha_registro' => date('Y-m-d H:i:s')
                        ]);
                    }
                }

                $mensaje = 'La customización se procesó correctamente.';
                $tipo = 'success';
            }

            DB::commit();
            return response()->json(['tipo' => $tipo, 'mensaje' => $mensaje, 'id_ingreso' => $id_ingreso, 'id_salida' => $id_salida]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al anular. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    function validarEdicion($id_transformacion)
    {
        $mov = DB::table('almacen.mov_alm')
            ->where([
                ['id_transformacion', '=', $id_transformacion],
                ['estado', '=', 1]
            ])
            ->get();

        //Si existe ingreso y salida relacionado
        if (count($mov) > 0) {
            $mensaje = 'La customización ya fue procesada.';
            $tipo = 'warning';
        } else {
            $mensaje = 'Ok';
            $tipo = 'success';
        }
        return response()->json(['tipo' => $tipo, 'mensaje' => $mensaje]);
    }
}
