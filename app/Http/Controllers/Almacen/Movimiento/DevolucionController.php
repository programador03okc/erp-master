<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use App\Http\Controllers\Almacen\Ubicacion\AlmacenController;
use App\Http\Controllers\AlmacenController as GenericoAlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Presupuestos\Moneda;
use App\Models\Tesoreria\TipoCambio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DevolucionController extends Controller
{
    function viewDevolucion()
    {
        $almacenes = AlmacenController::mostrar_almacenes_cbo();
        $empresas = GenericoAlmacenController::select_empresa();
        $unidades = GenericoAlmacenController::mostrar_unidades_cbo();
        $usuarios = GenericoAlmacenController::select_usuarios();
        $monedas = Moneda::where('estado', 1)->get();
        return view('almacen/devoluciones/devolucion', compact('almacenes', 'empresas', 'usuarios', 'unidades', 'monedas'));
    }

    public function listarDevoluciones()
    {
        $lista = DB::table('cas.devolucion')->where('estado', '!=', 7)->get();
        return datatables($lista)->toJson();
    }

    public function mostrarDevolucion($id)
    {
        $devolucion = DB::table('cas.devolucion')
            ->select('devolucion.*', 'sis_usua.nombre_corto')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'devolucion.registrado_por')
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
            ->where('id_devolucion', $id)->get();

        return response()->json(['devolucion' => $devolucion, 'detalle' => $detalle]);
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

            $id_devolucion = DB::table('cas.devolucion')->insertGetId(
                [
                    'codigo' => $codigo,
                    'id_almacen' => $request->id_almacen,
                    // 'id_moneda' => $request->id_moneda,
                    // 'tipo_cambio' => $request->tipo_cambio,
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
                        'cantidad' => $item->cantidad,
                        // 'valor_unitario' => $item->unitario,
                        // 'valor_total' => round($item->total, 6, PHP_ROUND_HALF_UP),
                        'estado' => 1,
                        'fecha_registro' => new Carbon(),
                    ],
                    'id_detalle'
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

            DB::table('cas.devolucion')
                ->where('id_devolucion', $request->id_devolucion)
                ->update([
                    'id_almacen' => $request->id_almacen,
                    'observacion' => $request->observacion,
                    // 'registrado_por' => $usuario->id_usuario,
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
                    $id_detalle = DB::table('cas.devolucion_detalle')->insertGetId(
                        [
                            'id_devolucion' => $request->id_devolucion,
                            'id_producto' => $item->id_producto,
                            'cantidad' => $item->cantidad,
                            'estado' => 1,
                            'fecha_registro' => new Carbon(),
                        ],
                        'id_detalle'
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
}
