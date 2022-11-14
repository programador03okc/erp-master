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

class ValorizacionExport implements FromView, WithColumnFormatting, WithStyles
{
    public $data, $almacen, $fecha, $tc;

    public function __construct($data, $almacen, $fecha, $tc)
    {
        $this->data = $data;
        $this->almacen = $almacen;
        $this->fecha = $fecha;
        $this->tc = $tc;
    }
    public function view() : View
    {

        return view('almacen.export.reporteValorizacion', ['data' => $this->data, 'almacen' => $this->almacen, 'fecha' => $this->fecha, 'tc' => $this->tc]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('D2:D'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:H')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A2')->getFont()->setSize(10);
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
