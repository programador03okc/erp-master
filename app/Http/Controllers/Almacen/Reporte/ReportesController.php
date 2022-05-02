<?php

namespace App\Http\Controllers\Almacen\Reporte;

use App\Exports\KardexGeneralExport;
use App\Exports\ReporteSaldosExport;
use App\Exports\ValorizacionExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Almacen\Movimiento;
use App\Models\Almacen\MovimientoDetalle;
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
        $almacenes = DB::table('almacen.alm_almacen')->where('estado', 1)->orderBy('codigo', 'asc')->get();
        return view('almacen/reportes/saldos', get_defined_vars());
    }

    public function filtrar(Request $request)
    {
        if (isset($request->almacen)) {
            $request->session()->put('filtroAlmacen', $request->almacen);
        } else {
            $request->session()->forget('filtroAlmacen');
        }

        if (isset($request->fecha)) {
            $request->session()->put('filtroFecha', $request->fecha);
        } else {
            $request->session()->forget('filtroFecha');
        }

        return response()->json(session()->get('filtroAlmacen'), 200);
    }

    public function listar(Request $request)
    {
        $data = [];

        if ($request->type == 2) {
            $nfecha = $request->session()->get('filtroFecha') . ' 23:59:59';
            $ft_fecha = date('Y-m-d', strtotime($nfecha));

            $query = DB::table('almacen.alm_prod_ubi')
                ->select(
                    'alm_prod_ubi.*',
                    'alm_prod.codigo',
                    'alm_prod.cod_softlink',
                    'alm_prod.descripcion AS producto',
                    'alm_und_medida.abreviatura',
                    'alm_prod.part_number',
                    'sis_moneda.simbolo',
                    'alm_prod.id_moneda',
                    'alm_prod.id_unidad_medida',
                    'alm_almacen.descripcion AS almacen_descripcion',
                    DB::raw("(SELECT SUM(alm_reserva.stock_comprometido) 
                        FROM almacen.alm_reserva 
                        WHERE alm_reserva.id_producto = alm_prod_ubi.id_producto
                        AND alm_reserva.id_almacen_reserva = alm_prod_ubi.id_almacen 
                        AND (alm_reserva.estado = 1 OR alm_reserva.estado = 17)
                        AND alm_reserva.fecha_registro <= '" . $nfecha . "') AS cantidad_reserva")
                )
                ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_prod_ubi.id_almacen')
                ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_prod_ubi.id_producto')
                ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
                ->leftjoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'alm_prod.id_moneda')
                ->where([['alm_prod_ubi.estado', '=', 1], ['alm_prod.estado', '=', 1]]);

            if ($request->session()->has('filtroAlmacen')) {
                $query = $query->whereIn('alm_prod_ubi.id_almacen', $request->session()->get('filtroAlmacen'));
            }
            $query = $query->get();

            foreach ($query as $d) {
                $movimientos = DB::table('almacen.mov_alm')
                    ->join('almacen.mov_alm_det', 'mov_alm_det.id_mov_alm', '=', 'mov_alm.id_mov_alm')
                    ->select(
                        'mov_alm.codigo',
                        'mov_alm.id_tp_mov',
                        'mov_alm.fecha_emision',
                        'mov_alm_det.id_producto',
                        'mov_alm_det.cantidad',
                        'mov_alm_det.valorizacion'
                    )
                    ->where('mov_alm.id_almacen', $d->id_almacen)
                    ->where('mov_alm.estado', 1)
                    ->where('mov_alm.fecha_emision', '<=', $request->session()->get('filtroFecha'))
                    ->where('mov_alm_det.id_producto', $d->id_producto)
                    ->where('mov_alm_det.estado', 1)
                    ->orderBy('mov_alm.fecha_emision');

                if ($movimientos->count() > 0) {
                    $saldo = 0;
                    $saldo_valor = 0;
                    $costo_promedio = 0;

                    foreach ($movimientos->get() as $key) {
                        if ($key->id_tp_mov == 0 || $key->id_tp_mov == 1) {
                            $saldo += (float) $key->cantidad;
                            $saldo_valor += (float) $key->valorizacion;
                        } else if ($key->id_tp_mov == 2) {
                            $saldo -= (float) $key->cantidad;
                            $valor_salida = $costo_promedio * (float) $key->cantidad;
                            $saldo_valor -= (float) $valor_salida;
                        }
                        $costo_promedio = (float) ($saldo == 0 ? 0 : $saldo_valor / $saldo);
                    }

                    $reserva = ($d->cantidad_reserva == null) ? 0 : $d->cantidad_reserva;
                    $data[] = [
                        'id_producto'           => $d->id_producto,
                        'id_almacen'            => $d->id_almacen,
                        'codigo'                => ($d->codigo != null) ?  $d->codigo : '',
                        'cod_softlink'          => ($d->cod_softlink != null) ?  $d->cod_softlink : '',
                        'part_number'           => ($d->part_number != null) ?  $d->part_number : '',
                        'producto'              => $d->producto,
                        'simbolo'               => ($d->simbolo != null) ?  $d->simbolo : '',
                        'valorizacion'          => $saldo_valor,
                        'costo_promedio'        => $costo_promedio,
                        'abreviatura'           => ($d->abreviatura != null) ?  $d->abreviatura : '',
                        'stock'                 => $saldo,
                        'reserva'               => $reserva,
                        'disponible'            => ($saldo - $reserva),
                        'almacen_descripcion'   => ($d->almacen_descripcion != null) ?  $d->almacen_descripcion : '',
                    ];
                }
            }
        }
        return DataTables::of($data)->make(true);
    }

    public function verRequerimientosReservados($id, $almacen)
    {
        $detalles = DB::table('almacen.alm_reserva')
            ->select(
                'alm_reserva.stock_comprometido',
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.concepto',
                'sis_usua.nombre_corto',
                'alm_almacen.descripcion as almacen_descripcion',
                DB::raw("CONCAT(guia_com.serie,'-',guia_com.numero) as guia_com"),
                'trans.codigo as codigo_trans',
                'transformacion.codigo as codigo_transfor_materia',
                'transformado.codigo as codigo_transfor_transformado',
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
        $productos = $this->listar_productos($request->almacen, $request->fecha);
        $data = [];
        $alm = DB::table('almacen.alm_almacen')->where('id_almacen', $request->almacen)->first();
        $tca = DB::table('contabilidad.cont_tp_cambio')->where('fecha', $request->fecha);
        $tc = ($tca->count() > 0) ? (float) $tca->first()->compra : 1;

        foreach ($productos as $row => $value) {
            $sum_ing = 0;
            $sum_sal = 0;
            $sum_val_sol = 0;
            $count = 0;

            $movimientos = DB::table('almacen.mov_alm')
                ->join('almacen.mov_alm_det', 'mov_alm_det.id_mov_alm', '=', 'mov_alm.id_mov_alm')
                ->select('mov_alm.codigo', 'mov_alm.id_tp_mov', 'mov_alm.fecha_emision', 'mov_alm_det.id_producto', 'mov_alm_det.cantidad', 'mov_alm_det.valorizacion')
                ->where('mov_alm.id_almacen', $request->almacen)->where('mov_alm.fecha_emision', '<=', $request->fecha)->where('mov_alm_det.id_producto', $value);

            if ($movimientos->count() > 0) {
                $prod = DB::table('almacen.alm_prod')->where('id_producto', $value)->first();

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
                    'codigo'            => ($prod->codigo != null) ?  $prod->codigo : '',
                    'codigo_softlink'   => ($prod->cod_softlink != null) ?  $prod->cod_softlink : '',
                    'producto'          => $prod->descripcion,
                    'stock'             => $sum_stock,
                    'valorizacion_sol'  => $sum_valor_sol,
                    'valorizacion_dol'  => $sum_valor_dol
                ];
            }
        }
        return Excel::download(new ValorizacionExport($data, $alm->descripcion, $request->fecha, $tc), 'valorizacion.xlsx');
    }

    public function listar_productos($id_almacen, $fecha)
    {
        $productos = [];
        $query_mov = DB::table('almacen.mov_alm')->where('id_almacen', $id_almacen)->where('mov_alm.fecha_emision', '<=', $fecha)->get();

        foreach ($query_mov as $mov) {
            $query_pro = DB::table('almacen.mov_alm_det')->select('id_producto')->where('id_mov_alm', $mov->id_mov_alm)->get();
            foreach ($query_pro as $pro) {
                if ($pro->id_producto != null) {
                    array_push($productos, $pro->id_producto);
                }
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

    public function exportarKardex($almacen, $fini, $ffin)
    {
        $alm_array = explode(',', $almacen);
        $query = MovimientoDetalle::select(
            'mov_alm_det.*',
            'mov_alm.fecha_emision',
            'mov_alm.id_tp_mov',
            'mov_alm.codigo',
            'alm_prod.descripcion as prod_descripcion',
            'alm_prod.codigo as prod_codigo',
            'alm_prod.part_number as prod_part_number',
            'alm_cat_prod.descripcion as categoria',
            'alm_subcat.descripcion as subcategoria',
            'alm_und_medida.abreviatura',
            'tp_ope_com.cod_sunat as cod_sunat_com',
            'tp_ope_com.descripcion as tp_com_descripcion',
            'tp_ope_ven.cod_sunat as cod_sunat_ven',
            'tp_ope_ven.descripcion as tp_ven_descripcion',
            DB::raw("(tp_guia_com.abreviatura) || '-' || (guia_com.serie) || '-' || (guia_com.numero) as guia_com"),
            DB::raw("(tp_guia_ven.abreviatura) || '-' || (guia_ven.serie) || '-' || (guia_ven.numero) as guia_ven"),
            'guia_com.id_guia',
            'guia_ven.id_guia_ven',
            'alm_almacen.descripcion as almacen_descripcion',
            'transformacion.codigo as cod_transformacion',
            'trans.codigo as cod_transferencia'
        )
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            ->leftjoin('almacen.transformacion', 'transformacion.id_transformacion', '=', 'mov_alm.id_transformacion')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
            ->join('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->join('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'mov_alm.id_guia_com')
            ->leftjoin('almacen.tp_doc_almacen as tp_guia_com', 'tp_guia_com.id_tp_doc_almacen', '=', 'guia_com.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope as tp_ope_com', 'tp_ope_com.id_operacion', '=', 'mov_alm.id_operacion')
            ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'mov_alm.id_guia_ven')
            ->leftjoin('almacen.tp_doc_almacen as tp_guia_ven', 'tp_guia_ven.id_tp_doc_almacen', '=', 'guia_ven.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope as tp_ope_ven', 'tp_ope_ven.id_operacion', '=', 'mov_alm.id_operacion')
            ->leftjoin('almacen.trans', 'trans.id_transferencia', '=', 'mov_alm.id_transferencia')
            ->leftjoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
            ->where([
                ['mov_alm.fecha_emision', '>=', $fini],
                ['mov_alm.fecha_emision', '<=', $ffin],
                ['mov_alm_det.estado', '=', 1]
            ])
            ->whereIn('mov_alm.id_almacen', $alm_array)
            ->orderBy('alm_prod.codigo', 'asc')
            ->orderBy('mov_alm.fecha_emision', 'asc')
            ->orderBy('mov_alm.id_tp_mov', 'asc')
            ->get();

        $saldo = 0;
        $saldo_valor = 0;
        $data = [];
        $codigo = '';
        $ordenes = "";
        $comprobantes_array = [];

        foreach ($query as $d) {
            if ($d->prod_codigo !== $codigo) {
                $saldo = 0;
                $saldo_valor = 0;
            }

            if ($d->id_tp_mov == 1 || $d->id_tp_mov == 0) {
                $saldo += $d->cantidad;
                $saldo_valor += $d->valorizacion;

                if ($d->id_guia_com_det !== null) {
                    $ordenes = $d->movimiento->requerimientos;
                    $comprobantes = MovimientoDetalle::join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', 'mov_alm_det.id_guia_com_det')
                        ->join('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', 'guia_com_det.id_guia_com_det')
                        ->join('almacen.doc_com', 'doc_com.id_doc_com', 'doc_com_det.id_doc')
                        ->join('logistica.log_prove', 'log_prove.id_proveedor', 'doc_com.id_proveedor')
                        ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', 'log_prove.id_contribuyente')
                        ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_com.moneda')
                        ->join('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_com.id_condicion')
                        ->where([
                            ['mov_alm_det.id_mov_alm', '=', $d->id_mov_alm],
                            ['mov_alm_det.estado', '!=', 7],
                            ['guia_com_det.estado', '!=', 7],
                            ['doc_com_det.estado', '!=', 7]
                        ])
                        ->select([
                            'doc_com.serie', 'doc_com.numero', 'doc_com.fecha_emision', 'sis_moneda.simbolo', 'doc_com.moneda',
                            'adm_contri.nro_documento', 'adm_contri.razon_social', 'log_cdn_pago.descripcion as des_condicion',
                            'doc_com.credito_dias', 'doc_com.sub_total', 'doc_com.total_igv', 'doc_com.total_a_pagar'
                        ])
                        ->distinct()->get();

                    foreach ($comprobantes as $doc) {
                        array_push($comprobantes_array, $doc->serie . '-' . $doc->numero);
                    }
                }
            } else if ($d->id_tp_mov == 2) {
                $saldo -= $d->cantidad;
                $saldo_valor -= $d->valorizacion;
            }
            $codigo = $d->prod_codigo;

            $nuevo = [
                "id_mov_alm_det" => $d->id_mov_alm_det,
                "codigo" => $d->codigo,
                "categoria" => $d->categoria,
                "subcategoria" => $d->subcategoria,
                "prod_codigo" => $d->prod_codigo,
                "prod_part_number" => $d->prod_part_number,
                "prod_descripcion" => $d->prod_descripcion,
                "fecha_emision" => $d->fecha_emision,
                "almacen_descripcion" => $d->almacen_descripcion,
                "abreviatura" => $d->abreviatura,
                "tipo" => $d->id_tp_mov,
                "cantidad" => $d->cantidad,
                "saldo" => $saldo,
                "valorizacion" => $d->valorizacion,
                "saldo_valor" => $saldo_valor,
                "cod_sunat_com" => $d->cod_sunat_com,
                "cod_sunat_ven" => $d->cod_sunat_ven,
                "tp_com_descripcion" => $d->tp_com_descripcion,
                "tp_ven_descripcion" => $d->tp_ven_descripcion,
                "id_guia_com" => $d->id_guia,
                "id_guia_ven" => $d->id_guia_ven,
                "guia_com" => $d->guia_com,
                "guia_ven" => $d->guia_ven,
                "cod_transformacion" => $d->cod_transformacion,
                "cod_transferencia" => $d->cod_transferencia,
                "orden" => $ordenes,
                "docs" => implode(', ', $comprobantes_array),
            ];
            array_push($data, $nuevo);
        }

        return Excel::download(new KardexGeneralExport($data, $almacen, $fini, $ffin), 'kardex_general.xlsx');
    }
}
