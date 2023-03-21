<?php

namespace App\Exports;

use App\Models\Gerencial\CobranzaView;
use App\Models\Gerencial\Penalidad;
use App\Models\Gerencial\ProgramacionPago;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CobranzaExport implements FromView, WithStyles, WithColumnFormatting
{
    public function view(): View {
        $data = CobranzaView::select(['*']);

        if (session()->has('cobranzaEmpresa')) {
            $data = $data->where('empresa', session()->get('cobranzaEmpresa'));
        }

        if (session()->has('cobranzaFase')) {
            $data = $data->where('fase', session()->get('cobranzaFase'));
        }

        // if (session()->has('cobranzaPeriodo')) {
        //     $data = $data->where('periodo', session()->get('cobranzaPeriodo'));
        // }

        if (session()->has('cobranzaEmisionDesde')) {
            $data = $data->whereBetween('fecha_emision', [session()->get('cobranzaEmisionDesde'), session()->get('cobranzaEmisionHasta')]);
        }
        $data = $data->orderBy('fecha_emision', 'desc')->get();

        return view('gerencial.reportes.cobranzas_export', ['data' => $data]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('D3:D' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('E3:E' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('U3:U' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('V3:V' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:AC')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        return [
            'A:Z' => [
                'font' => [ 'family' => 'Arial', 'size' => 10 ]
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            "O" => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "X" => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "AB" => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
