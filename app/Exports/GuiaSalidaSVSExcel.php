<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class GuiaSalidaSVSExcel implements FromView, WithEvents
{
    public $guia;
    public $detalle;

    public function __construct($guia, $detalle)
    {
        $this->guia = $guia;
        $this->detalle = $detalle;
    }

    public function view(): View
    {

        return view('almacen.export.guia_salida_svs_export', [
            'guia' => $this->guia,
            'detalle' => $this->detalle,
        ]);
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:U29')->getFont()->setSize(10);
                $event->sheet->getDelegate()->getStyle('A1:U29')->getFont()->setName('Times New Roman');

                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(2);
                $event->sheet->getDelegate()->getColumnDimension('N')->setWidth(2);
                $event->sheet->getDelegate()->getColumnDimension('O')->setWidth(2);
                $event->sheet->getDelegate()->getColumnDimension('P')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('Q')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('R')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('S')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('T')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('U')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('V')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('W')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('X')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('Y')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('Z')->setWidth(4);
                $event->sheet->getStyle('J17:J20')->getAlignment()->setWrapText(true);
                $event->sheet->getStyle('E8')->getAlignment()->setWrapText(true);
                $event->sheet->getStyle('C9')->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getRowDimension(5)->setRowHeight(11);
 
                 $rowCount = $event->sheet->getHighestRow();
                $event->sheet->setCellValue('A1',$rowCount.'Top Triggers Report Top Triggers Report Top Triggers Report');
                $event->sheet->mergeCells(sprintf('A1:E1'));
                $event->sheet->mergeCells(sprintf('A2:%s2','G'));
                $event->sheet->getStyle('A1')->getAlignment()->setWrapText(true);


                    


                $event->sheet->getDelegate()->getStyle('C10')->getAlignment()->setVertical("TOP");
                $event->sheet->getDelegate()->getStyle('E8')->getAlignment()->setVertical("TOP");
                $event->sheet->getDelegate()->getStyle('P7')->getAlignment()->setVertical("TOP");
            },



        ];
    }
}
