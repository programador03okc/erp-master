<?php

namespace App\Http\Controllers\Almacen\Reporte;

use App\Exports\ReporteSaldosExport;
use App\Exports\ValorizacionExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ReportesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function view_saldos(Request $request)
    {
        $fecha = new Carbon();
        $almacenes = $this->almacenesPorUsuario();
        return view('almacen/reportes/saldos', get_defined_vars());
    }

    public function filtrar(Request $request)
    {
        if (isset($request->almacen)) {
            $request->session()->put('filtroAlmacen', $request->almacen);
        } else {
            $request->session()->forget('filtroAlmacen');
        }

        return response()->json(session()->get('filtroAlmacen'), 200);
    }

    public function listar(Request $request)
    {
        $data = [];

        if ($request->type == 2) {
            $data = DB::table('almacen.alm_prod_ubi')
                ->select('alm_prod_ubi.*', 'alm_prod.codigo', 'alm_prod.cod_softlink', 'alm_prod.descripcion', 'alm_und_medida.abreviatura', 'alm_prod.part_number',
                    'sis_moneda.simbolo', 'alm_prod.id_moneda', 'alm_prod.id_unidad_medida', 'alm_almacen.descripcion as almacen_descripcion',
                    DB::raw("(SELECT SUM(alm_reserva.stock_comprometido) FROM almacen.alm_reserva WHERE alm_reserva.id_producto = alm_prod_ubi.id_producto
                    AND alm_reserva.id_almacen_reserva = alm_prod_ubi.id_almacen AND (alm_reserva.estado = 1 OR alm_reserva.estado = 17) ) as cantidad_reserva")
                )
                ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_prod_ubi.id_almacen')
                ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_prod_ubi.id_producto')
                ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
                ->leftjoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'alm_prod.id_moneda')
                ->where([['alm_prod_ubi.estado', '=', 1]]);

            if ($request->session()->has('filtroAlmacen')) {
                $data->whereIn('alm_prod_ubi.id_almacen', $request->session()->get('filtroAlmacen'));
            }
            $data->get();
        }
        
        return DataTables::of($data)
        ->editColumn('valorizacion', function ($data) {
            return number_format($data->valorizacion, 3);
        })
        ->editColumn('costo_promedio', function ($data) {
            return number_format($data->valorizacion, 3);
        })
        ->addColumn('reserva', function ($data) {
            $reserva = '';
            if ($data->cantidad_reserva) {
                $reserva .= '<a class="label label-danger" onclick="verRequerimiento('.$data->id_producto.', '.$data->id_almacen.');" style="font-size: 11px">'.$data->cantidad_reserva.'</a>';
            }
            return $reserva;
        })
        ->addColumn('disponible' , function ($data) {
            $reservas = ($data->cantidad_reserva != null) ? $data->cantidad_reserva : 0;
            return ($data->stock - $reservas);
        })->rawColumns(['reserva'])->make(true);
    }

    public function verRequerimientosReservados($id, $almacen)
    {
        $detalles = DB::table('almacen.alm_reserva')
            ->select('alm_reserva.stock_comprometido','alm_req.id_requerimiento','alm_req.codigo','alm_req.concepto', 'sis_usua.nombre_corto','alm_almacen.descripcion as almacen_descripcion',
                DB::raw("CONCAT(guia_com.serie,'-',guia_com.numero) as guia_com"), 'trans.codigo as codigo_trans', 'transformacion.codigo as codigo_transfor_materia', 'transformado.codigo as codigo_transfor_transformado',
            )
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_reserva.id_almacen_reserva')
            ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'alm_reserva.id_detalle_requerimiento')
            ->leftjoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftjoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->leftjoin('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'alm_reserva.id_guia_com_det')
            ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->leftjoin('almacen.trans_detalle', 'trans_detalle.id_trans_detalle', '=', 'alm_reserva.id_trans_detalle')
            ->leftjoin('almacen.trans', 'trans.id_transferencia', '=', 'trans_detalle.id_transferencia')
            ->leftjoin('almacen.transfor_materia', 'transfor_materia.id_materia', '=', 'alm_reserva.id_materia')
            ->leftjoin('almacen.transformacion', 'transformacion.id_transformacion', '=', 'transfor_materia.id_transformacion')
            ->leftjoin('almacen.transfor_transformado', 'transfor_transformado.id_transformado', '=', 'alm_reserva.id_transformado')
            ->leftjoin('almacen.transformacion as transformado', 'transformado.id_transformacion', '=', 'transfor_transformado.id_transformacion')
            ->where([
                ['alm_reserva.id_producto', '=', $id],
                ['alm_reserva.id_almacen_reserva', '=', $almacen],
                ['alm_reserva.estado', '!=', 7],
                ['alm_reserva.estado', '!=', 5],
            ])->get();
        return response()->json($detalles);
    }

    public function exportar()
    {
        return Excel::download(new ReporteSaldosExport, 'reporte_saldos.xlsx');
    }

    public function valorizacion(Request $request)
    {
        $productos = $this->listar_productos($request->almacen);
        $data = [];
        $alm = DB::table('almacen.alm_almacen')->where('id_almacen', $request->almacen)->first();
        $tca = DB::table('contabilidad.cont_tp_cambio')->where('fecha', $request->fecha)->first();
        $tc = (float) $tca->compra;
        
        foreach ($productos as $row => $value) {
            $sum_ing = 0; $sum_sal = 0; $sum_val_sol = 0; $count = 0;

            $movimientos = DB::table('almacen.mov_alm')
            ->join('almacen.mov_alm_det', 'mov_alm_det.id_mov_alm', '=', 'mov_alm.id_mov_alm')
            ->select('mov_alm.codigo', 'mov_alm.id_tp_mov', 'mov_alm.fecha_emision', 'mov_alm_det.id_producto', 'mov_alm_det.cantidad', 'mov_alm_det.valorizacion')
            ->where('mov_alm.id_almacen', $request->almacen)->where('mov_alm.fecha_emision', '<=', $request->fecha)->where('mov_alm_det.id_producto', $value);

            $prod = DB::table('almacen.alm_prod')->where('id_producto', $value)->first();
                            
            if ($movimientos->count() > 0) {
                foreach ($movimientos->get() as $key) {
                    if ($key->id_tp_mov == 0 || $key->id_tp_mov == 1) {
                        $sum_ing += (float) $key->cantidad;
                    } else if ($key->id_tp_mov == 2) {
                        $sum_sal += (float) $key->cantidad;
                    }
                    $sum_val_sol += (float) $key->valorizacion;
                    $count++;
                }
                $sum_stock = $sum_ing - $sum_sal;
                $sum_valor_sol = $sum_val_sol / $count;
                $sum_valor_dol = $sum_valor_sol / $tc;
                
                $data[] = [
                    'id_producto'       => $value,
                    'codigo'            => $prod->codigo,
                    'codigo_softlink'   => $prod->cod_softlink,
                    'producto'          => $prod->descripcion,
                    'stock'             => $sum_stock,
                    'valorizacion_sol'  => $sum_valor_sol,
                    'valorizacion_dol'  => $sum_valor_dol
                ];
            }
        }
        // return response()->json($data, 200);
        return Excel::download(new ValorizacionExport($data, $alm->descripcion, $request->fecha, $tc), 'valorizacion.xlsx');
    }

    public function listar_productos($id_almacen)
    {
        $productos = [];
        $query_mov = DB::table('almacen.mov_alm')->where('id_almacen', $id_almacen)->get();

        foreach ($query_mov as $mov) {
            $query_pro = DB::table('almacen.mov_alm_det')->select('id_producto')->where('id_mov_alm', $mov->id_mov_alm)->get();
            foreach ($query_pro as $pro) {
                array_push($productos, $pro->id_producto);
            }
        }
        sort($productos, SORT_ASC);
        $productos = array_unique($productos);
        return $productos;
    }

    public function almacenesPorUsuario()
    {
        return DB::table('almacen.alm_almacen_usuario')
            ->select('alm_almacen.*')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_almacen_usuario.id_almacen')
            ->where('alm_almacen_usuario.id_usuario', Auth::user()->id_usuario)
            ->where('alm_almacen_usuario.estado', 1)
            ->get();
    }
}
