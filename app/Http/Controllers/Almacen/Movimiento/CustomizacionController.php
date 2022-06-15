<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use App\Http\Controllers\Almacen\Ubicacion\AlmacenController;
use App\Http\Controllers\AlmacenController as GenericoAlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Presupuestos\Moneda;
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
        return view('almacen/customizacion/customizacion', compact('almacenes', 'empresas', 'usuarios', 'unidades', 'monedas'));
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
                // 'orden_despacho.codigo as cod_od',
                'alm_almacen.descripcion as almacen_descripcion',
            )
            // ->leftjoin('almacen.orden_despacho', 'orden_despacho.id_od', '=', 'transformacion.id_od')
            ->leftjoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'transformacion.id_almacen')
            // ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
            ->leftjoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'transformacion.estado')
            ->leftjoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'transformacion.responsable')
            ->leftjoin('configuracion.sis_usua as registrado', 'registrado.id_usuario', '=', 'transformacion.registrado_por')
            ->where('transformacion.id_transformacion', $id_transformacion)
            ->first();

        $bases = DB::table('almacen.transfor_materia')
            ->select(
                'transfor_materia.id_materia',
                'transfor_materia.id_producto',
                'transfor_materia.costo_promedio',
                'transfor_materia.cantidad',
                'transfor_materia.valor_unitario as unitario',
                'transfor_materia.valor_total as total',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.part_number',
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

        $sobrantes = DB::table('almacen.transfor_sobrante')
            ->select(
                'transfor_sobrante.id_sobrante',
                'transfor_sobrante.id_producto',
                'transfor_sobrante.cantidad',
                'transfor_sobrante.valor_unitario as unitario',
                'transfor_sobrante.valor_total as total',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.part_number',
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

        $transformados = DB::table('almacen.transfor_transformado')
            ->select(
                'transfor_transformado.id_transformado',
                'transfor_transformado.id_producto',
                'transfor_transformado.cantidad',
                'transfor_transformado.valor_unitario as unitario',
                'transfor_transformado.valor_total as total',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.part_number',
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

        return response()->json(['customizacion' => $data, 'bases' => $bases, 'sobrantes' => $sobrantes, 'transformados' => $transformados]);
    }

    public function guardarCustomizacion(Request $request)
    {
        try {
            DB::beginTransaction();
            $mensaje = '';
            $tipo = '';

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
                    // 'total_materias' => $request->total_materias,
                    // 'total_directos' => $request->total_directos,
                    // 'costo_primo' => $request->costo_primo,
                    // 'total_indirectos' => $request->total_indirectos,
                    // 'total_sobrantes' => $request->total_sobrantes,
                    // 'costo_transformacion' => $request->costo_transformacion,
                    'registrado_por' => $usuario->id_usuario,
                    'estado' => 1,
                    'fecha_registro' => new Carbon(),
                ],
                'id_transformacion'
            );

            $items_base = json_decode($request->items_base);

            foreach ($items_base as $item) {
                DB::table('almacen.transfor_materia')->insert(
                    [
                        'id_transformacion' => $id_transformacion,
                        'id_producto' => $item->id_producto,
                        'cantidad' => $item->cantidad,
                        'costo_promedio' => $item->costo_promedio,
                        'valor_unitario' => $item->unitario,
                        'valor_total' => round($item->total, 6, PHP_ROUND_HALF_UP),
                        'estado' => 1,
                        'fecha_registro' => new Carbon(),
                    ]
                );
            }

            $items_transformado = json_decode($request->items_transformado);

            foreach ($items_transformado as $item) {
                DB::table('almacen.transfor_transformado')->insert(
                    [
                        'id_transformacion' => $id_transformacion,
                        'id_producto' => $item->id_producto,
                        'cantidad' => $item->cantidad,
                        'valor_unitario' => $item->unitario,
                        'valor_total' => round($item->total, 6, PHP_ROUND_HALF_UP),
                        'estado' => 1,
                        'fecha_registro' => new Carbon(),
                    ]
                );
            }

            $items_sobrante = json_decode($request->items_sobrante);

            foreach ($items_sobrante as $item) {
                DB::table('almacen.transfor_sobrante')->insert(
                    [
                        'id_transformacion' => $id_transformacion,
                        'id_producto' => $item->id_producto,
                        'cantidad' => $item->cantidad,
                        'valor_unitario' => $item->unitario,
                        'valor_total' => round($item->total, 6, PHP_ROUND_HALF_UP),
                        'estado' => 1,
                        'fecha_registro' => new Carbon(),
                    ]
                );
            }

            $customizacion = DB::table('almacen.transformacion')->where('id_transformacion', $id_transformacion)->first();
            $mensaje = 'Se guardó la customización correctamente';
            $tipo = 'success';

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
                    DB::table('almacen.transfor_materia')
                        ->where('id_materia', $item->id_materia)
                        ->update([
                            'cantidad' => $item->cantidad,
                            'costo_promedio' => $item->costo_promedio,
                            'valor_unitario' => $item->unitario,
                            'valor_total' => round($item->total, 6, PHP_ROUND_HALF_UP),
                        ]);
                } else {
                    DB::table('almacen.transfor_materia')->insert(
                        [
                            'id_transformacion' => $request->id_customizacion,
                            'id_producto' => $item->id_producto,
                            'cantidad' => $item->cantidad,
                            'costo_promedio' => $item->costo_promedio,
                            'valor_unitario' => $item->unitario,
                            'valor_total' => round($item->total, 6, PHP_ROUND_HALF_UP),
                            'estado' => 1,
                            'fecha_registro' => new Carbon(),
                        ]
                    );
                }
            }

            $items_transformado = json_decode($request->items_transformado);

            foreach ($items_transformado as $item) {

                if ($item->id_transformado > 0) {
                    DB::table('almacen.transfor_transformado')
                        ->where('id_transformado', $item->id_transformado)
                        ->update([
                            'cantidad' => $item->cantidad,
                            'valor_unitario' => $item->unitario,
                            'valor_total' => round($item->total, 6, PHP_ROUND_HALF_UP),
                        ]);
                } else {
                    DB::table('almacen.transfor_transformado')->insert(
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
                }
            }

            $items_sobrante = json_decode($request->items_sobrante);

            foreach ($items_sobrante as $item) {

                if ($item->id_sobrante > 0) {
                    DB::table('almacen.transfor_sobrante')
                        ->where('id_sobrante', $item->id_sobrante)
                        ->update([
                            'cantidad' => $item->cantidad,
                            'valor_unitario' => $item->unitario,
                            'valor_total' => round($item->total, 6, PHP_ROUND_HALF_UP),
                        ]);
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
                }
            }

            $customizacion = DB::table('almacen.transformacion')->where('id_transformacion', $request->id_customizacion)->first();
            $mensaje = 'Se actualizó la customización correctamente';
            $tipo = 'success';

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
                $mensaje = 'No es posible anular. La customización ya fue finalizada.';
                $tipo = 'warning';
            } else {
                DB::table('almacen.transformacion')
                    ->where('id_transformacion', $id_transformacion)
                    ->update(['estado' => 7]);

                DB::table('almacen.transfor_materia')
                    ->where('id_transformacion', $id_transformacion)
                    ->update(['estado' => 7]);

                DB::table('almacen.transfor_transformado')
                    ->where('id_transformacion', $id_transformacion)
                    ->update(['estado' => 7]);

                DB::table('almacen.transfor_sobrante')
                    ->where('id_transformacion', $id_transformacion)
                    ->update(['estado' => 7]);

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
                    ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'transfor_materia.id_producto')
                    ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
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
                            // 'id_guia_ven_det' => $id_guia_ven_det,
                            'estado' => 1,
                            'fecha_registro' => new Carbon(),
                        ],
                        'id_mov_alm_det'
                    );
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
                    ->select('transfor_sobrante.*')
                    ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'transfor_sobrante.id_producto')
                    ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
                    ->where([
                        ['transfor_sobrante.id_transformacion', '=', $id_transformacion],
                        ['transfor_sobrante.estado', '!=', 7]
                    ])
                    ->get();

                foreach ($sobrantes as $item) {
                    //Guardo los items del ingreso
                    DB::table('almacen.mov_alm_det')->insertGetId(
                        [
                            'id_mov_alm' => $id_ingreso,
                            'id_producto' => $item->id_producto,
                            'cantidad' => $item->cantidad,
                            'costo_promedio' => $item->valor_unitario,
                            'valorizacion' => $item->valor_total,
                            'usuario' => $id_usuario,
                            // 'id_guia_ven_det' => $id_guia_ven_det,
                            'estado' => 1,
                            'fecha_registro' => new Carbon(),
                        ],
                        'id_mov_alm_det'
                    );
                }

                $mensaje = 'La customización se procesó correctamente.';
                $tipo = 'success';
            }

            DB::commit();
            return response()->json(['tipo' => $tipo, 'mensaje' => $mensaje]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al anular. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }
}
