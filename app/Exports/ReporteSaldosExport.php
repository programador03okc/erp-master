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
    public function view() : View
    {
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

        if (session()->has('filtroAlmacen')) {
            $data->whereIn('alm_prod_ubi.id_almacen', session()->get('filtroAlmacen'));
        }
        return view('almacen.export.reporteSaldos', ['saldos' => $data->orderBy('almacen_descripcion', 'asc')->get()]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('D2:D'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:I')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
