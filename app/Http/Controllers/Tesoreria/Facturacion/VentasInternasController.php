<?php

namespace App\Http\Controllers\Tesoreria\Facturacion;

use App\Exports\ValorizacionesIngresosActualizadasExport;
use App\Exports\VentasInternasActualizadasExport;
use App\Http\Controllers\Almacen\Movimiento\OrdenesPendientesController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Logistica\Distribucion\OrdenesDespachoExternoController;
use App\Models\Almacen\Requerimiento;
use App\Models\Distribucion\OrdenDespacho;
use App\Models\Logistica\Orden;
use App\Models\Tesoreria\TipoCambio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class VentasInternasController extends Controller
{
    public function autogenerarDocumentosCompra($id)
    {
        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $fecha = date('Y-m-d H:i:s');
            // $id_doc = null;

            $doc_ven = DB::table('almacen.doc_ven')
                ->select('doc_ven.*', 'log_prove.id_proveedor')
                ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'doc_ven.id_empresa')
                ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
                ->join('logistica.log_prove', 'log_prove.id_contribuyente', '=', 'adm_contri.id_contribuyente')
                ->where('id_doc_ven', $id)
                ->first();

            $detalle = DB::table('almacen.doc_ven_det')
                ->select(
                    'doc_ven_det.*',
                    'guia_com_det.id_guia_com_det',
                    'guia_com.id_almacen',
                    'alm_almacen.id_sede',
                    'alm_prod.id_unidad_medida',
                    'alm_prod.id_moneda',
                )
                ->join('almacen.guia_com_det', 'guia_com_det.id_guia_ven_det', '=', 'doc_ven_det.id_guia_ven_det')
                ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'doc_ven_det.id_item')
                ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
                ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'guia_com.id_almacen')
                ->where('doc_ven_det.id_doc', $id)
                ->get();

            if (($doc_ven->id_doc_ven !== null) && count($detalle) > 0) {

                $id_condicion_softlink = '';

                if ($doc_ven->id_condicion == 1) {
                    $id_condicion_softlink = '02';
                } else if ($doc_ven->id_condicion == 2) {
                    switch ($doc_ven->credito_dias) {
                        case 60:
                            $id_condicion_softlink = '03';
                            break;
                        case 20:
                            $id_condicion_softlink = '23';
                            break;
                        case 30:
                            $id_condicion_softlink = '01';
                            break;
                        case 45:
                            $id_condicion_softlink = '22';
                            break;
                        case 15:
                            $id_condicion_softlink = '06';
                            break;
                        case 7:
                            $id_condicion_softlink = '05';
                            break;
                        case 3:
                            $id_condicion_softlink = '14';
                            break;
                        case 40:
                            $id_condicion_softlink = '25';
                            break;
                        case 35:
                            $id_condicion_softlink = '24';
                            break;
                        default:
                            break;
                    }
                }

                $tipo_cambio = TipoCambio::where([['moneda', '=', 2], ['fecha', '<=', $doc_ven->fecha_emision]])
                    ->orderBy('fecha', 'DESC')->first();

                $id_doc = DB::table('almacen.doc_com')->insertGetId(
                    [
                        'serie' => strtoupper($doc_ven->serie),
                        'numero' => $doc_ven->numero,
                        'id_sede' => $detalle->first()->id_sede,
                        'id_tp_doc' => $doc_ven->id_tp_doc,
                        'id_proveedor' => $doc_ven->id_proveedor,
                        'fecha_emision' => $doc_ven->fecha_emision,
                        'fecha_vcmto' => $doc_ven->fecha_vcmto,
                        'id_condicion' => $doc_ven->id_condicion,
                        'credito_dias' => $doc_ven->credito_dias,
                        'moneda' => $doc_ven->moneda,
                        'id_condicion_softlink' => $id_condicion_softlink,
                        'sub_total' => $doc_ven->sub_total,
                        'total_igv' => $doc_ven->total_igv,
                        'total_icbper' => 0,
                        'tipo_cambio' => $tipo_cambio->venta,
                        'porcen_igv' => $doc_ven->porcen_igv,
                        'total_a_pagar' => $doc_ven->total_a_pagar,
                        'usuario' => $doc_ven->usuario,
                        'registrado_por' => $id_usuario,
                        'estado' => 1,
                        'fecha_registro' => $fecha,
                    ],
                    'id_doc_com'
                );

                $id_requerimiento = DB::table('almacen.alm_req')->insertGetId(
                    [
                        'codigo' => '-',
                        'id_tipo_requerimiento' => 7,
                        'id_usuario' => $id_usuario,
                        'fecha_requerimiento' => $fecha,
                        'concepto' => ('Compra segun doc ' . $doc_ven->serie . '-' . $doc_ven->numero),
                        'id_grupo' => 1,
                        'id_prioridad' => 1,
                        'observacion' => 'Creado de forma automática por venta interna',
                        'id_moneda' => 1,
                        'id_empresa' => $doc_ven->id_empresa,
                        'id_periodo' => 5, // ! actualizar
                        'id_sede' => $detalle->first()->id_sede,
                        'id_cliente' => $doc_ven->id_cliente,
                        'tipo_cliente' => 2,
                        'id_almacen' => $detalle->first()->id_almacen,
                        'confirmacion_pago' => true,
                        'fecha_entrega' => $doc_ven->fecha_emision,
                        'tiene_transformacion' => false,
                        'para_stock_almacen' => false,
                        'enviar_facturacion' => false,
                        'estado' => 9,
                        'fecha_registro' => $fecha,
                    ],
                    'id_requerimiento'
                );

                $codigo = Requerimiento::crearCodigo(7, 1, $id_requerimiento,5); // ! actualiar periodo 

                DB::table('almacen.alm_req')
                    ->where('id_requerimiento', $id_requerimiento)
                    ->update(['codigo' => $codigo]);

                $id_od = DB::table('almacen.orden_despacho')->insertGetId(
                    [
                        "id_requerimiento" => $id_requerimiento,
                        "id_cliente" => $doc_ven->id_cliente,
                        "codigo" => '-',
                        "direccion_destino" => 'Entrega por venta interna',
                        "fecha_despacho" => $fecha,
                        "fecha_entrega" => $fecha,
                        "aplica_cambios" => false,
                        "registrado_por" => $id_usuario,
                        "fecha_registro" => $fecha,
                        "estado" => 9, //Procesada
                        "id_sede" => $detalle->first()->id_sede,
                        "id_almacen" => $detalle->first()->id_almacen,
                        "hora_despacho" => $fecha,
                        "persona_contacto" => 'Creado de forma automática por venta interna'
                    ],
                    'id_od'
                );
                // $codigo = OrdenesDespachoExternoController::ODnextId($fecha, $detalle->first()->id_almacen, false, $id_od);
                $codigo = OrdenDespacho::ODnextId($detalle->first()->id_almacen, false, $id_od);

                if ($codigo !== null) {
                    DB::table('almacen.orden_despacho')
                        ->where('id_od', $id_od)
                        ->update(['codigo' => $codigo]);
                }

                $codigo_oc = Orden::nextCodigoOrden(2);

                $id_orden_compra = DB::table('logistica.log_ord_compra')->insertGetId(
                    [
                        'id_tp_documento' => 2,
                        'fecha' => $fecha,
                        'id_usuario' => $id_usuario,
                        'id_moneda' => 1,
                        'id_proveedor' => $doc_ven->id_proveedor,
                        'codigo' => $codigo_oc,
                        'id_condicion' => $doc_ven->id_condicion,
                        'plazo_dias' => $doc_ven->credito_dias,
                        'id_condicion_softlink' => $id_condicion_softlink,
                        'plazo_entrega' => 0,
                        'en_almacen' => true,
                        'id_sede' => $detalle->first()->id_sede,
                        'id_tp_doc' => 2,
                        'observacion' => 'Autogenerado por venta interna',
                        'incluye_igv' => true,
                        'estado' => 28
                    ],
                    'id_orden_compra'
                );

                foreach ($detalle as $item) {
                    DB::table('almacen.doc_com_det')->insert([
                        'id_doc' => $id_doc,
                        'id_guia_com_det' => $item->id_guia_com_det,
                        'id_item' => $item->id_item,
                        'cantidad' => $item->cantidad,
                        'id_unid_med' => $item->id_unid_med,
                        'precio_unitario' => $item->precio_unitario,
                        'sub_total' => $item->sub_total,
                        'porcen_dscto' => $item->porcen_dscto,
                        'total_dscto' => $item->total_dscto,
                        'precio_total' => $item->precio_total,
                        'estado' => 1,
                        'fecha_registro' => $fecha,
                    ]);

                    $id_det_req = DB::table('almacen.alm_det_req')->insertGetId(
                        [
                            'id_requerimiento' => $id_requerimiento,
                            'cantidad' => $item->cantidad,
                            'id_tipo_item' => 1,
                            'id_unidad_medida' => $item->id_unidad_medida,
                            'id_producto' => $item->id_item,
                            'id_moneda' => $doc_ven->moneda,
                            'tiene_transformacion' => false,
                            'precio_unitario' => $item->precio_unitario,
                            'estado' => 9,
                            'fecha_registro' => $fecha,
                        ],
                        'id_detalle_requerimiento'
                    );

                    $id_oc_det = DB::table('logistica.log_det_ord_compra')->insertGetId(
                        [
                            'id_orden_compra' => $id_orden_compra,
                            'cantidad' => $item->cantidad,
                            'precio' => $item->precio_unitario,
                            'id_unidad_medida' => $item->id_unidad_medida,
                            'subtotal' => $item->sub_total,
                            'id_producto' => $item->id_item,
                            'id_detalle_requerimiento' => $id_det_req,
                            'tipo_item_id' => 1,
                            'estado' => 28
                        ],
                        'id_detalle_orden'
                    );

                    DB::table('almacen.guia_com_det')
                        ->where('id_guia_com_det', $item->id_guia_com_det)
                        ->update(['id_oc_det' => $id_oc_det]);

                    $unitario = 0;

                    if ($item->id_moneda == $doc_ven->moneda) { //moneda del producto == moneda del documento
                        $unitario = $item->precio_unitario;
                    } else {
                        if ($item->id_moneda == 1) { //soles
                            $unitario = $item->precio_unitario * $tipo_cambio->venta;
                        } else if ($item->id_moneda == 2) { //dolares
                            $unitario = $item->precio_unitario / $tipo_cambio->venta;
                        }
                    }

                    DB::table('almacen.mov_alm_det')
                        ->where('id_guia_com_det', $item->id_guia_com_det)
                        ->update(['valorizacion' => ($unitario * $item->cantidad)]);

                    OrdenesPendientesController::actualiza_prod_ubi($item->id_item, $item->id_almacen);
                }
            }

            DB::commit();
            $rpta = "ok";
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            $rpta = "null";
        }
        return response()->json($rpta);
    }

    public function verDocumentosAutogenerados($id_doc_com)
    {
        $detalle = DB::table('almacen.doc_com_det')
            ->select(
                DB::raw("CONCAT(guia_com.serie, '-', guia_com.numero) as guia_com"),
                DB::raw("CONCAT(doc_com.serie, '-', doc_com.numero) as doc_com"),
                'mov_alm.id_mov_alm as id_ingreso',
                'log_ord_compra.codigo as codigo_oc',
                'log_ord_compra.id_orden_compra',
                'alm_req.codigo as codigo_req',
                'alm_req.id_requerimiento',
            )
            ->join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'doc_com_det.id_guia_com_det')
            // ->join('almacen.mov_alm_det', 'mov_alm_det.id_guia_com_det', '=', 'guia_com_det.id_guia_com_det')
            ->join('almacen.mov_alm_det', function ($join) {
                $join->on('mov_alm_det.id_guia_com_det', '=', 'guia_com_det.id_guia_com_det');
                $join->where('mov_alm_det.estado', '!=', 7);
            })
            // ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            ->join('almacen.mov_alm', function ($join) {
                $join->on('mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm');
                $join->where('mov_alm.estado', '!=', 7);
            })
            ->join('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det')
            ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
            ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->join('almacen.doc_com', 'doc_com.id_doc_com', '=', 'doc_com_det.id_doc')
            ->where('doc_com_det.id_doc', $id_doc_com)
            ->distinct()
            ->get();
        return response()->json($detalle);
    }

    public function actualizarCostosVentasInternas(Request $request)
    {
        $detalle = DB::table('almacen.guia_com_det')
            ->select(
                'guia_com_det.id_guia_com_det',
                'mov_alm_det.id_mov_alm_det',
                'mov_alm_det.cantidad',
                'mov_alm_det.valorizacion',
                'mov_alm.id_almacen',
                'mov_alm.codigo',
                'alm_almacen.descripcion as almacen_descripcion',
                'doc_com_det.precio_unitario',
                'alm_prod.codigo as codigo_producto',
                'alm_prod.id_moneda as id_moneda_producto',
                'doc_com.moneda as id_moneda_doc',
                'doc_com.fecha_emision',
            )
            ->join('almacen.doc_com_det', function ($join) {
                $join->on('doc_com_det.id_guia_com_det', '=', 'guia_com_det.id_guia_com_det');
                $join->where('doc_com_det.estado', '!=', 7);
            })
            ->join('almacen.doc_com', 'doc_com.id_doc_com', '=', 'doc_com_det.id_doc')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_com_det.id_producto')
            ->join('almacen.mov_alm_det', function ($join) {
                $join->on('mov_alm_det.id_guia_com_det', '=', 'guia_com_det.id_guia_com_det');
                $join->where('mov_alm_det.estado', '!=', 7);
            })
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
            ->whereNotNull('guia_com_det.id_trans_detalle')
            ->get();

        $lista = [];

        foreach ($detalle as $det) {
            $tipo_cambio = TipoCambio::where([['moneda', '=', 2], ['fecha', '<=', $det->fecha_emision]])
                ->orderBy('fecha', 'DESC')->first();

            $unitario = 0;

            if ($det->id_moneda_producto == $det->id_moneda_doc) { //moneda del producto == moneda del documento
                $unitario = floatval($det->precio_unitario);
            } else {
                if ($det->id_moneda_producto == 1) { //soles
                    $unitario = floatval($det->precio_unitario) * floatval($tipo_cambio->venta);
                } else if ($det->id_moneda_producto == 2) { //dolares
                    $unitario = floatval($det->precio_unitario) / floatval($tipo_cambio->venta);
                }
            }

            $unitario_ingreso_actual = floatval($det->valorizacion) / floatval($det->cantidad);

            if (round($unitario_ingreso_actual, 6, PHP_ROUND_HALF_UP) !== round($unitario, 6, PHP_ROUND_HALF_UP)) {
                $nueva_val = $unitario * floatval($det->cantidad);

                DB::table('almacen.mov_alm_det')
                    ->where('mov_alm_det.id_mov_alm_det', $det->id_mov_alm_det)
                    ->update([
                        'valorizacion' => $nueva_val,
                        'valorizacion_old' => $det->valorizacion,
                        'costo_promedio' => $unitario
                    ]);

                array_push($lista, [
                    'codigo' => $det->codigo,
                    'almacen_descripcion' => $det->almacen_descripcion,
                    'codigo_producto' => $det->codigo_producto,
                    'cantidad' => $det->cantidad,
                    'valorizacion' => $det->valorizacion,
                    'precio_unitario' => $det->precio_unitario,
                    'id_moneda_producto' => $det->id_moneda_producto,
                    'id_moneda_doc' => $det->id_moneda_doc,
                    'fecha_emision' => $det->fecha_emision,
                    'nueva_valorizacion' => $nueva_val,
                    'unitario_anterior' => $unitario_ingreso_actual,
                    'unitario_nuevo' => $unitario,
                ]);
            }
        }

        return Excel::download(new VentasInternasActualizadasExport(
            $lista,
        ), 'VentasInternasActualizadas.xlsx');

        // return response()->json(['nro_lista' => count($lista), 'lista' => $lista]);
    }

    public function actualizarValorizacionesIngresos()
    {
        $detalle = DB::table('almacen.mov_alm_det')
            ->select(
                'mov_alm_det.id_mov_alm_det',
                'mov_alm_det.cantidad',
                'mov_alm_det.valorizacion',
                'mov_alm.id_almacen',
                'mov_alm.codigo',
                'alm_almacen.descripcion as almacen_descripcion',
                'doc_com_det.precio_unitario',
                'alm_prod.codigo as codigo_producto',
                'alm_prod.id_moneda as id_moneda_producto',
                'alm_prod.id_moneda_old',
                'doc_com.moneda as id_moneda_doc',
                'doc_com.fecha_emision',
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
            // ->join('almacen.alm_prod', function ($join) {
            //     $join->on('alm_prod.id_producto', '=', 'mov_alm_det.id_producto');
            //     $join->where([['alm_prod.id_moneda', '!=', 'alm_prod.id_moneda_old']]);
            // })
            // ->join('almacen.mov_alm_det', 'mov_alm_det.id_producto', '=', 'alm_prod.id_producto')
            ->join('almacen.doc_com_det', function ($join) {
                $join->on('doc_com_det.id_guia_com_det', '=', 'mov_alm_det.id_guia_com_det');
                $join->where('doc_com_det.estado', '!=', 7);
            })
            ->join('almacen.doc_com', 'doc_com.id_doc_com', '=', 'doc_com_det.id_doc')
            ->join('almacen.mov_alm', function ($join) {
                $join->on('mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm');
                $join->where('mov_alm.estado', '!=', 7);
            })
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
            ->where([
                // ['alm_prod.id_moneda', '!=', 'alm_prod.id_moneda_old'],
                ['mov_alm.id_tp_mov', '=', 1]
            ])
            ->whereColumn('alm_prod.id_moneda', '!=', 'alm_prod.id_moneda_old')
            ->get();

        // return response()->json($detalle);
        $lista = [];

        foreach ($detalle as $det) {
            $tipo_cambio = TipoCambio::where([['moneda', '=', 2], ['fecha', '<=', $det->fecha_emision]])
                ->orderBy('fecha', 'DESC')->first();

            $unitario = 0;

            if ($det->id_moneda_producto == $det->id_moneda_doc) { //moneda del producto == moneda del documento
                $unitario = floatval($det->precio_unitario);
            } else {
                if ($det->id_moneda_producto == 1) { //soles
                    $unitario = floatval($det->precio_unitario) * floatval($tipo_cambio->venta);
                } else if ($det->id_moneda_producto == 2) { //dolares
                    $unitario = floatval($det->precio_unitario) / floatval($tipo_cambio->venta);
                }
            }

            $unitario_ingreso_actual = floatval($det->valorizacion) / floatval($det->cantidad);

            if (round($unitario_ingreso_actual, 6, PHP_ROUND_HALF_UP) !== round($unitario, 6, PHP_ROUND_HALF_UP)) {
                $nueva_val = $unitario * floatval($det->cantidad);

                DB::table('almacen.mov_alm_det')
                    ->where('mov_alm_det.id_mov_alm_det', $det->id_mov_alm_det)
                    ->update([
                        'valorizacion' => $nueva_val,
                        'valorizacion_old' => $det->valorizacion,
                        'costo_promedio' => $unitario
                    ]);

                array_push($lista, [
                    'codigo' => $det->codigo,
                    'almacen_descripcion' => $det->almacen_descripcion,
                    'codigo_producto' => $det->codigo_producto,
                    'cantidad' => $det->cantidad,
                    'valorizacion' => $det->valorizacion,
                    'precio_unitario' => $det->precio_unitario,
                    'id_moneda_producto' => $det->id_moneda_producto,
                    'id_moneda_old' => $det->id_moneda_old,
                    'id_moneda_doc' => $det->id_moneda_doc,
                    'fecha_emision' => $det->fecha_emision,
                    'nueva_valorizacion' => $nueva_val,
                    'unitario_anterior' => $unitario_ingreso_actual,
                    'unitario_nuevo' => $unitario,
                ]);
            }
        }

        return Excel::download(new ValorizacionesIngresosActualizadasExport(
            $lista,
        ), 'ValorizacionesIngresos.xlsx');

        // return response()->json(['nro_lista' => count($lista), 'lista' => $lista]);
    }
}
