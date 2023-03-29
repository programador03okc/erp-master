<?php

namespace App\Exports;

use App\Models\Gerencial\CobranzaView;
use App\Models\Gerencial\Penalidad;
use App\Models\Gerencial\ProgramacionPago;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CobranzaExport implements FromView, WithStyles, WithColumnFormatting, WithEvents
{
    public function view(): View {
        $data = CobranzaView::select(['*']);

        if (session()->has('cobranzaPenalidad')) {
            $data = $data->where('tiene_penalidad', session()->get('cobranzaPenalidad'));
        }

        if (session()->has('cobranzaEmpresa')) {
            $data = $data->where('empresa', session()->get('cobranzaEmpresa'));
        }

        if (session()->has('cobranzaFase')) {
            $data = $data->where('fase', session()->get('cobranzaFase'));
        }

        if (session()->has('cobranzaEstadoDoc')) {
            $data = $data->where('estado_cobranza', session()->get('cobranzaEstadoDoc'));
        }

        if (session()->has('cobranzaEmisionDesde')) {
            $data = $data->whereBetween('fecha_emision', [session()->get('cobranzaEmisionDesde'), session()->get('cobranzaEmisionHasta')]);
        }
        $data = $data->orderBy('fecha_emision', 'desc')->get();

        return view('gerencial.reportes.cobranzas_export', ['data' => $data]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('1')->getFont()->setSize(16);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('D3:D' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('F3:F' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('V3:V' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('W3:W' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:AD')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        return [
            'A:AC' => [
                'font' => [ 'family' => 'Arial', 'size' => 10 ]
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            "P" => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "Y" => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "AC" => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
