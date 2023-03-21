<?php

namespace App\Exports;

use App\Models\Gerencial\FondoView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FondoExport implements FromView, WithStyles, WithColumnFormatting, WithEvents
{
    public function view(): View {
        $data = FondoView::select(['*']);
        $data = $data->orderBy('fecha_solicitud', 'desc')->get();

        return view('gerencial.reportes.fondos_export', ['data' => $data]);
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
        $sheet->getStyle('N3:N' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('O3:O' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:P')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        return [
            'A:P' => [
                'font' => [ 'family' => 'Arial', 'size' => 10 ]
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            "H" => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
