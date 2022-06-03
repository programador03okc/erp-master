<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tesoreria\TipoCambio;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class SalidaPdfController extends Controller
{
    public function imprimir_salida($id_salida)
    {
        $salida = DB::table('almacen.mov_alm')
            ->select(
                'mov_alm.*',
                'alm_almacen.descripcion as des_almacen',
                'sis_usua.nombre_corto',
                'adm_empresa.logo_empresa',
                'tp_ope.cod_sunat',
                'tp_ope.descripcion as ope_descripcion',
                DB::raw("(guia_ven.serie) || '-' || (guia_ven.numero) as guia"),
                'trans.codigo as trans_codigo',
                // 'trans.fecha_transferencia',
                'alm_destino.descripcion as trans_almacen_destino',
                // DB::raw("(cont_tp_doc.abreviatura) || '-' || (doc_ven.serie) || '-' || (doc_ven.numero) as doc"),
                // DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) as persona"),
                'transformacion.codigo as cod_transformacion', //'transformacion.serie','transformacion.numero',
                'transformacion.fecha_transformacion',
                'guia_ven.fecha_emision as fecha_guia',
                'adm_contri.nro_documento as ruc_empresa',
                'adm_contri.razon_social as empresa_razon_social',
                'cliente.nro_documento as ruc_cliente',
                'cliente.razon_social as razon_social_cliente',
            )
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'mov_alm.id_guia_ven')
            ->leftjoin('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'mov_alm.id_operacion')
            ->leftjoin('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_ven.id_tp_doc_almacen')
            ->leftjoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'guia_ven.id_cliente')
            ->leftjoin('contabilidad.adm_contri as cliente', 'cliente.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->leftjoin('almacen.trans', 'trans.id_guia_ven', '=', 'guia_ven.id_guia_ven')
            ->leftjoin('almacen.alm_almacen as alm_destino', 'alm_destino.id_almacen', '=', 'trans.id_almacen_destino')
            // ->leftjoin('almacen.doc_ven', 'doc_ven.id_doc_ven', '=', 'mov_alm.id_doc_ven')
            // ->leftjoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_ven.id_tp_doc')
            ->leftjoin('almacen.transformacion', 'transformacion.id_transformacion', '=', 'mov_alm.id_transformacion')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'mov_alm.usuario')
            // ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'sis_usua.id_trabajador')
            // ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            // ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->where('mov_alm.id_mov_alm', $id_salida)
            ->first();

        $lista = DB::table('almacen.mov_alm_det')
            ->select(
                'mov_alm_det.*',
                'alm_prod.codigo',
                'alm_prod.part_number',
                'alm_prod.descripcion',
                'alm_prod.id_moneda',
                'alm_und_medida.abreviatura',
                'sis_moneda.simbolo',
                'trans.codigo as cod_trans',
                'doc_ven.fecha_emision',
                'doc_ven_det.precio_unitario',
                'doc_moneda.simbolo as moneda_doc',
                DB::raw("(cont_tp_doc.abreviatura) || '-' ||(doc_ven.serie) || '-' || (doc_ven.numero) as doc")
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
            ->leftjoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'alm_prod.id_moneda')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->join('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven_det', '=', 'mov_alm_det.id_guia_ven_det')
            ->leftjoin('almacen.doc_ven_det', function ($join) {
                $join->on('doc_ven_det.id_guia_ven_det', '=', 'guia_ven_det.id_guia_ven_det');
                $join->where('doc_ven_det.estado', '!=', 7);
            })
            ->leftjoin('almacen.doc_ven', function ($join) {
                $join->on('doc_ven.id_doc_ven', '=', 'doc_ven_det.id_doc');
                $join->where('doc_ven.estado', '!=', 7);
            })
            ->leftjoin('configuracion.sis_moneda as doc_moneda', 'doc_moneda.id_moneda', '=', 'doc_ven.moneda')
            ->leftjoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_ven.id_tp_doc')
            ->leftjoin('almacen.trans_detalle', function ($join) {
                $join->on('trans_detalle.id_trans_detalle', '=', 'guia_ven_det.id_trans_det');
                $join->where('trans_detalle.estado', '!=', 7);
            })
            ->leftjoin('almacen.trans', function ($join) {
                $join->on('trans.id_transferencia', '=', 'trans_detalle.id_transferencia');
                $join->where('trans.estado', '!=', 7);
            })
            // ->leftjoin('almacen.trans', 'trans.id_transferencia', '=', 'trans_detalle.id_transferencia')
            ->where([['mov_alm_det.id_mov_alm', '=', $id_salida], ['mov_alm_det.estado', '=', 1]])
            ->get();

        $docs_array = [];
        $docs_fecha_array = [];
        $detalle = [];
        $valor_dolar = 0;

        if ($salida !== null) {
            foreach ($lista as $det) {

                if (!in_array($det->doc, $docs_array)) {
                    array_push($docs_array, $det->doc);
                }
                if (!in_array($det->fecha_emision, $docs_fecha_array)) {
                    array_push($docs_fecha_array, $det->fecha_emision);
                }
                //corregir fecha inicial tengo sueño
                $costo_promedio = $this->obtenerCostoPromedioSalida($det->id_producto, $salida->id_almacen, '2022-01-01', $salida->fecha_emision);

                if ($salida->id_operacion == 27) {
                    $tipo_cambio = TipoCambio::where([
                        ['moneda', '=', 2],
                        ['fecha', '<=', $salida->fecha_emision]
                    ])->orderBy('fecha', 'DESC')->first();

                    if (intval($det->id_moneda) == 2) {
                        $valor_dolar = $costo_promedio;
                    } else {
                        $valor_dolar = (floatval($costo_promedio) > 0 ? floatval($costo_promedio) / floatval($tipo_cambio->venta) : 0);
                    }
                }

                //agregar series
                $det_series = DB::table('almacen.alm_prod_serie')
                    ->select('alm_prod_serie.serie')
                    ->where([
                        ['alm_prod_serie.id_prod', '=', $det->id_producto],
                        ['alm_prod_serie.id_guia_ven_det', '=', $det->id_guia_ven_det],
                        ['alm_prod_serie.estado', '!=', 7]
                    ])
                    ->get();

                $series = '';

                if ($det_series !== null) {
                    foreach ($det_series as $s) {
                        if ($s->serie !== null) {
                            if ($series !== '') {
                                $series .= ', ' . $s->serie;
                            } else {
                                $series = 'Serie(s): ' . $s->serie;
                            }
                        }
                    }
                }

                array_push(
                    $detalle,
                    [
                        'id_guia_ven_det' => $det->id_guia_ven_det,
                        'id_producto' => $det->id_producto,
                        'cantidad' => $det->cantidad,
                        'costo_promedio' => $costo_promedio,
                        'valorizacion' => ($costo_promedio * $det->cantidad),
                        'codigo' => $det->codigo,
                        'part_number' => $det->part_number,
                        'descripcion' => $det->descripcion,
                        'abreviatura' => $det->abreviatura,
                        'simbolo' => $det->simbolo,
                        'valor_dolar' => $valor_dolar,
                        'series' => $series,
                    ]
                );
            }
        }
        return $detalle;

        // $logo_empresa = ".$salida->logo_empresa";
        // $fecha_registro =  (new Carbon($salida->fecha_registro))->format('d-m-Y');
        // $hora_registro = (new Carbon($salida->fecha_registro))->format('H:i:s');
        // // $ocs = implode(",", $ocs_array);
        // // $softlink = implode(",", $softlink_array);
        // $docs = implode(",", $docs_array);
        // $docs_fecha = implode(",", $docs_fecha_array);

        // $vista = View::make(
        //     'almacen/guias/salida_pdf',
        //     compact(
        //         'salida',
        //         'logo_empresa',
        //         'detalle',
        //         'docs',
        //         'docs_fecha',
        //         'fecha_registro',
        //         'hora_registro'
        //     )
        // )->render();
        // $pdf = App::make('dompdf.wrapper');
        // $pdf->loadHTML($vista);

        // return $pdf->stream();
        // return $pdf->download($salida->codigo . '.pdf');
    }

    public function obtenerCostoPromedioSalida($id_producto, $almacen, $finicio, $ffin)
    {
        $data = DB::table('almacen.mov_alm_det')
            ->select(
                'mov_alm_det.*',
                // 'sis_moneda.simbolo',
                'mov_alm.fecha_emision',
                'mov_alm.id_tp_mov',
            )
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            // ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
            ->where([
                ['mov_alm_det.id_producto', '=', $id_producto],
                ['mov_alm.fecha_emision', '>=', $finicio],
                ['mov_alm.fecha_emision', '<=', $ffin],
                ['mov_alm.id_almacen', '=', $almacen],
                ['mov_alm_det.estado', '=', 1]
            ])
            ->orderBy('mov_alm.fecha_emision', 'asc')
            ->orderBy('mov_alm.id_tp_mov', 'asc')
            ->get();

        $saldo = 0;
        $saldo_valor = 0;
        $costo_promedio = 0;
        $valor_salida = 0;

        foreach ($data as $d) {

            if ($d->id_tp_mov == 1 || $d->id_tp_mov == 0) { //ingreso o inicial
                $saldo += $d->cantidad;
                $saldo_valor += $d->valorizacion;
            } else if ($d->id_tp_mov == 2) { //salida
                $saldo -= $d->cantidad;
                $valor_salida = $costo_promedio * $d->cantidad;
                $saldo_valor -= $valor_salida;
            }

            if ($saldo !== 0) {
                $costo_promedio = ($saldo == 0 ? 0 : $saldo_valor / $saldo);
            }
        }
        return $costo_promedio;
    }

    public function obtenerSaldo($id_producto, $almacen, $finicio, $ffin)
    {
        $data = DB::table('almacen.mov_alm_det')
            ->select(
                'mov_alm_det.*',
                // 'sis_moneda.simbolo',
                'mov_alm.fecha_emision',
                'mov_alm.id_tp_mov',
            )
            ->join('almacen.mov_alm', 'mov_alm.id_mov_alm', '=', 'mov_alm_det.id_mov_alm')
            // ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'mov_alm_det.id_producto')
            ->where([
                ['mov_alm_det.id_producto', '=', $id_producto],
                ['mov_alm.fecha_emision', '>=', $finicio],
                ['mov_alm.fecha_emision', '<=', $ffin],
                ['mov_alm.id_almacen', '=', $almacen],
                ['mov_alm_det.estado', '=', 1]
            ])
            ->orderBy('mov_alm.fecha_emision', 'asc')
            ->orderBy('mov_alm.id_tp_mov', 'asc')
            ->get();

        $saldo = 0;
        $saldo_valor = 0;
        $costo_promedio = 0;
        $valor_salida = 0;

        foreach ($data as $d) {

            if ($d->id_tp_mov == 1 || $d->id_tp_mov == 0) { //ingreso o inicial
                $saldo += $d->cantidad;
                // $saldo_valor += $d->valorizacion;
            } else if ($d->id_tp_mov == 2) { //salida
                $saldo -= $d->cantidad;
                // $valor_salida = $costo_promedio * $d->cantidad;
                // $saldo_valor -= $valor_salida;
            }

            // if ($saldo !== 0) {
            //     $costo_promedio = ($saldo == 0 ? 0 : $saldo_valor / $saldo);
            // }
        }
        return $saldo;
    }
}
