<?php

namespace App\Exports;

use App\Http\Controllers\Almacen\Reporte\SaldosController;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReporteAntiguedadesExcel implements FromView, WithColumnFormatting, WithStyles
{
    public function __construct()
    {
    }

    public function view(): View
    {
        $nfecha = session()->get('filtroFecha') . ' 23:59:59';
        $ft_fecha = date('Y-m-d', strtotime($nfecha));

        $query = DB::table('almacen.alm_prod_serie')
            ->select(
                'alm_prod_serie.serie',
                'alm_prod_serie.fecha_ingreso_soft',
                'alm_prod_serie.precio_unitario_soft',
                'alm_prod_serie.doc_ingreso_soft',
                'alm_prod_serie.moneda_soft',
                'alm_prod_serie.id_almacen',
                'alm_prod_serie.id_prod as id_producto',
                'alm_prod.codigo',
                'alm_prod.cod_softlink',
                'alm_prod.descripcion AS producto',
                'alm_und_medida.abreviatura',
                'alm_prod.part_number',
                'alm_cat_prod.descripcion AS categoria',
                'sis_moneda.simbolo',
                'alm_prod.id_moneda',
                'alm_prod.id_unidad_medida',
                'alm_almacen.descripcion AS almacen_descripcion'
            )
            // ->leftjoin('almacen.alm_prod_serie', function ($join) {
            //     $join->on('alm_prod_serie.id_almacen', '=', 'alm_prod_ubi.id_almacen');
            //     $join->on('alm_prod_serie.id_prod', '=', 'alm_prod_ubi.id_producto');
            //     $join->whereNull('alm_prod_serie.id_guia_ven_det');
            //     $join->whereNull('alm_prod_serie.id_base');
            //     $join->where('alm_prod_serie.estado', 1);
            // })
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_prod_serie.id_almacen')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_prod_serie.id_prod')
            ->join('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftjoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'alm_prod.id_moneda')
            ->where([
                ['alm_prod_serie.estado', '=', 1],
                ['alm_prod.estado', '=', 1]
            ])
            ->whereNull('alm_prod_serie.id_guia_ven_det')
            ->whereNull('alm_prod_serie.id_base');

        if (session()->has('filtroAlmacen')) {
            $query = $query->whereIn('alm_prod_serie.id_almacen', session()->get('filtroAlmacen'));
        }
        // $query = $query->orderBy('alm_prod.id_producto', 'asc')
        //     ->orderBy('alm_prod_serie.id_almacen', 'asc')->get();

        // $unicos = [];
        // $precio_unitario_soft = 0;
        // $fecha_ingreso_soft = null;
        // $doc_ingreso_soft = '';

        foreach ($query->get() as $q) {
            /*
            $movimientos = DB::table('almacen.mov_alm')
                ->join('almacen.mov_alm_det', 'mov_alm_det.id_mov_alm', '=', 'mov_alm.id_mov_alm')
                ->select(
                    'mov_alm.id_tp_mov',
                    'mov_alm.fecha_emision',
                    'mov_alm.codigo',
                    'mov_alm_det.id_producto',
                    'mov_alm_det.cantidad',
                    'mov_alm_det.valorizacion',
                )
                ->where('mov_alm.id_almacen', $q->id_almacen)
                ->where('mov_alm.fecha_emision', '<=', session()->get('filtroFecha'))
                ->where('mov_alm_det.id_producto', $q->id_producto)
                ->where('mov_alm_det.estado', 1)
                ->orderBy('mov_alm.fecha_emision', 'asc')
                ->orderBy('mov_alm.id_tp_mov', 'asc');

            if ($movimientos->count() > 0) {
                $saldo = 0;
                $saldo_valor = 0;
                $costo_promedio = 0;
                $precio_unitario_soft = 0;
                $fecha_ingreso_soft = null;
                $doc_ingreso_soft = '';

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

                    if ($fecha_ingreso_soft == null && ($key->id_tp_mov == 0 || $key->id_tp_mov == 1)) {
                        $precio_unitario_soft = $costo_promedio;
                        $fecha_ingreso_soft = $key->fecha_emision;
                        $doc_ingreso_soft = $key->codigo;
                    }
                }

                if ($saldo > 0) {*/
            $data[] = [
                'id_producto'           => $q->id_producto,
                'id_almacen'            => $q->id_almacen,
                'codigo'                => ($q->codigo != null) ?  str_replace("'", "", $q->codigo) : '',
                'cod_softlink'          => ($q->cod_softlink != null) ?  str_replace("'", "", str_replace("", "", $q->cod_softlink)) : '',
                'part_number'           => ($q->part_number != null) ?  str_replace("'", "", str_replace("", "", trim($q->part_number))) : '',
                'producto'              => str_replace("'", "", str_replace("", "", $q->producto)),
                'categoria'             => str_replace("'", "", trim($q->categoria)),
                'simbolo'               => ($q->simbolo != null) ?  $q->simbolo : '',
                // 'valorizacion'          => $saldo_valor,
                // 'costo_promedio'        => $costo_promedio,
                'abreviatura'           => ($q->abreviatura != null) ?  $q->abreviatura : '',
                // 'stock'                 => $saldo,
                'almacen_descripcion'   => ($q->almacen_descripcion != null) ?  str_replace("'", "", $q->almacen_descripcion) : '',
                'serie'                 => $q->serie,
                'fecha_ingreso_soft'    => ($q->fecha_ingreso_soft !== null ? $q->fecha_ingreso_soft : ''),
                'precio_unitario_soft'  => ($q->precio_unitario_soft !== null ? $q->precio_unitario_soft : ''),
                'doc_ingreso_soft'      => ($q->doc_ingreso_soft !== null ? $q->doc_ingreso_soft : ''),
                'moneda_soft'           => ($q->moneda_soft !== null ? $q->moneda_soft : ''),
            ];
            //     }
            // }

        }
        return view('almacen.export.reporteAntiguedades', ['saldos' => $data]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('D2:D' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:L')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
