<?php

namespace App\Exports;

use App\Models\Gerencial\DevolucionPenalidadView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DevolucionPenalidadExport implements FromView, WithStyles, WithColumnFormatting, WithEvents
{
    public function view(): View {
        $data = DevolucionPenalidadView::select(['*']);
        $data = $data->orderBy('fecha_penalidad', 'desc')->get();

        return view('gerencial.reportes.devolucion_export', ['data' => $data]);
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
        $sheet->getStyle('C3:C' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('J3:J' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('P3:P' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('Q3:Q' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('R3:R' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:R')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        return [
            'A:R' => [
                'font' => [ 'family' => 'Arial', 'size' => 10 ]
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            "L" => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "M" => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
