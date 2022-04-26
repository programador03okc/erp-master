<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KardexGeneralExport implements FromView, WithColumnFormatting, WithStyles
{
    public $data, $almacen, $fecha_ini, $fecha_fin;

    public function __construct($data, $almacen, $fecha_ini, $fecha_fin)
    {
        $this->data = $data;
        $this->almacen = $almacen;
        $this->fecha_ini = $fecha_ini;
        $this->fecha_fin = $fecha_fin;
    }

    public function view() : View
    {
        return view('almacen.export.reporteKardexGeneral', ['data' => $this->data, 'almacen' => $this->almacen, 'fecha_ini' => $this->fecha_ini, 'fecha_fin' => $this->fecha_fin]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('B2:B'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('E2:E'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('G2:G'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('Q2:Q'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('R2:R'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('S2:S'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('T2:T'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('U2:U'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:U')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => array('rgb' => '000000'),
                ],
            ],
        ];
        
        $sheet->getStyle('A1:U1')->applyFromArray($styleArray);

        return [
            1    => ['font' => ['bold' => true] ],
            'A:U'  => ['font' => ['size' => 10]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
        ];
    }
}
