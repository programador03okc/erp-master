<?php

namespace App\Http\Controllers\Tesoreria\Facturacion;

use App\Http\Controllers\Almacen\Movimiento\OrdenesPendientesController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Almacen\Requerimiento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                ->join('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'doc_ven.id_cliente')
                ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
                ->join('logistica.log_prove', 'log_prove.id_contribuyente', '=', 'adm_contri.id_contribuyente')
                ->where('id_doc_ven', $id)
                ->first();

            $detalle = DB::table('almacen.doc_ven_det')
                ->select(
                    'doc_ven_det.*',
                    'guia_com_det.id_guia_com_det',
                    'guia_com.id_almacen',
                    'alm_almacen.id_sede',
                    'alm_prod.id_unidad_medida'
                )
                // ->join('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven_det', '=', 'doc_ven_det.id_guia_ven_det')
                ->join('almacen.guia_com_det', 'guia_com_det.id_guia_ven_det', '=', 'doc_ven_det.id_guia_ven_det')
                ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'doc_ven_det.id_item')
                ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
                ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'guia_com.id_almacen')
                ->where('doc_ven_det.id_doc', $id)
                ->get();

            if (($doc_ven->id_doc_ven !== null) && count($detalle) > 0) {

                $id_doc = DB::table('almacen.doc_com')->insertGetId(
                    [
                        'serie' => strtoupper($doc_ven->serie),
                        'numero' => $doc_ven->numero,
                        'id_tp_doc' => $doc_ven->id_tp_doc,
                        'id_proveedor' => $doc_ven->id_proveedor,
                        'fecha_emision' => $doc_ven->fecha_emision,
                        'fecha_vcmto' => $doc_ven->fecha_vcmto,
                        'id_condicion' => $doc_ven->id_condicion,
                        'credito_dias' => $doc_ven->credito_dias,
                        'id_sede' => $doc_ven->id_sede,
                        'moneda' => $doc_ven->moneda,
                        // 'tipo_cambio' => $tc,
                        'sub_total' => $doc_ven->sub_total,
                        'total_igv' => $doc_ven->total_igv,
                        'porcen_igv' => $doc_ven->porcen_igv,
                        'total_a_pagar' => $doc_ven->total_a_pagar,
                        'usuario' => $doc_ven->usuario,
                        'registrado_por' => $id_usuario,
                        'estado' => 1,
                        'fecha_registro' => $fecha,
                    ],
                    'id_doc_com'
                );

                $codigo = Requerimiento::crearCodigo(7, 1);

                $id_requerimiento = DB::table('almacen.alm_req')->insertGetId(
                    [
                        'codigo' => $codigo,
                        'id_tipo_requerimiento' => 7,
                        'id_usuario' => $id_usuario,
                        'fecha_requerimiento' => $fecha,
                        'concepto' => ('Compra segun doc ' . $doc_ven->serie . '-' . $doc_ven->numero),
                        'id_grupo' => 1,
                        'id_prioridad' => 1,
                        'observacion' => 'Creado de forma automática por venta interna',
                        'id_moneda' => 1,
                        'id_empresa' => $doc_ven->id_empresa,
                        'id_periodo' => 3,
                        'id_sede' => $detalle->first()->id_sede,
                        'id_cliente' => $doc_ven->id_cliente,
                        'tipo_cliente' => 2,
                        'id_almacen' => $detalle->first()->id_almacen,
                        'confirmacion_pago' => true,
                        'fecha_entrega' => $doc_ven->fecha_emision,
                        'tiene_transformacion' => false,
                        'para_stock_almacen' => false,
                        'enviar_facturacion' => false,
                        'estado' => 1,
                        'fecha_registro' => $fecha,
                    ],
                    'id_requerimiento'
                );

                $id_orden_compra = DB::table('logistica.log_ord_compra')->insertGetId(
                    [
                        'id_tp_documento' => 2,
                        'fecha' => $fecha,
                        'id_usuario' => $id_usuario,
                        'id_moneda' => 1,
                        'id_proveedor' => $doc_ven->id_proveedor,
                        'codigo' => $codigo,
                        'id_condicion' => $doc_ven->id_condicion,
                        'plazo_dias' => $doc_ven->credito_dias,
                        'plazo_entrega' => 0,
                        'en_almacen' => true,
                        'id_sede' => $detalle->first()->id_sede,
                        'id_tp_doc' => 2,
                        'observacion' => 'Autogenerado por venta interna',
                        'incluye_igv' => true,
                        'estado' => 1
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
                            'estado' => 1,
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
                            'estado' => 1
                        ],
                        'id_detalle_orden'
                    );

                    DB::table('almacen.guia_com_det')
                        ->where('id_guia_com_det', $item->id_guia_com_det)
                        ->update(['id_oc_det' => $id_oc_det]);

                    DB::table('almacen.mov_alm_det')
                        ->where('id_guia_com_det', $item->id_guia_com_det)
                        ->update(['valorizacion' => $item->precio_total]);

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
}
