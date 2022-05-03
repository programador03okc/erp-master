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
