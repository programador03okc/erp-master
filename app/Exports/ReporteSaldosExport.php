<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReporteSaldosExport implements FromView, WithColumnFormatting, WithStyles
{
    public function view(): View
    {
        $nfecha = session()->get('filtroFecha') . ' 23:59:59';
        $ft_fecha = date('Y-m-d', strtotime($nfecha));

        $query = DB::table('almacen.alm_prod_ubi')
            ->select(
                'alm_prod_ubi.*',
                'alm_prod.codigo',
                'alm_prod.cod_softlink',
                'alm_prod.descripcion AS producto',
                'alm_und_medida.abreviatura',
                'alm_prod.part_number',
                'alm_cat_prod.descripcion AS categoria',
                'sis_moneda.simbolo',
                'alm_prod.id_moneda',
                'alm_prod.id_unidad_medida',
                'alm_almacen.descripcion AS almacen_descripcion',
                DB::raw("(SELECT SUM(alm_reserva.stock_comprometido) 
                    FROM almacen.alm_reserva 
                    WHERE alm_reserva.id_producto = alm_prod_ubi.id_producto
                    AND alm_reserva.id_almacen_reserva = alm_prod_ubi.id_almacen 
                    AND (alm_reserva.estado = 1 OR alm_reserva.estado = 17)
                    AND alm_reserva.fecha_registro <= '" . $ft_fecha . "') AS cantidad_reserva")
            )
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_prod_ubi.id_almacen')
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_prod_ubi.id_producto')
            ->join('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftjoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'alm_prod.id_moneda')
            ->where([['alm_prod_ubi.estado', '=', 1], ['alm_prod.estado', '=', 1]]);

        if (session()->has('filtroAlmacen')) {
            $query = $query->whereIn('alm_prod_ubi.id_almacen', session()->get('filtroAlmacen'));
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
                ->where('mov_alm.fecha_emision', '<=', session()->get('filtroFecha'))
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
                    'part_number'           => ($d->part_number != null) ?  trim($d->part_number) : '',
                    'producto'              => trim($d->producto),
                    'categoria'             => trim($d->categoria),
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
        return view('almacen.export.reporteSaldos', ['saldos' => $data]);
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
