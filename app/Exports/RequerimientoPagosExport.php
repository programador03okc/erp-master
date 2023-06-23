<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class RequerimientoPagosExport implements FromView, WithColumnFormatting, WithStyles
{
    public $data;
    public function __construct(string $data)
    {
        $this->data = $data;
    }

    public function view(): View{
        $json = json_decode($this->data);
        // va
        // dd(json_decode($this->data));exit;

        return view('tesoreria.Pagos.export.requerimientos_pagados', [
            "data"=>$json
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('D2:D'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:S')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        return [
            1    => ['font' => ['bold' => true] ],
            'A:S'  => ['font' => ['size' => 10]]
        ];
    }
    
    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Q' => NumberFormat::FORMAT_TEXT,
            'R' => NumberFormat::FORMAT_TEXT
        ];
    }

}
