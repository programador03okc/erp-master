<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;

class GuiaSalidaOKCExcel implements FromView, WithEvents
{


    public function __construct()
    {
    }

    public function view(): View
    {

        return view('almacen.export.guia_salida_okc_export', [
            'data' => []
        ]);
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('all')->getFont()->setSize(10);

                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(3);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('c')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(8);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(4);
                $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(4);
                $event->sheet->getStyle('H16:H20')->getAlignment()->setWrapText(true);
                $event->sheet->getStyle('J7:J8')->getAlignment()->setWrapText(true);
                $event->sheet->getStyle('D17')->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getRowDimension(11)->setRowHeight(11);

                $style = array(
                    'alignment' => array(
                        'wrap' => true
                    )
                );
                $event->sheet->getStyle("H16:H20")->applyFromArray($style);
                $event->sheet->getStyle("J7:J8")->applyFromArray($style);
                $event->sheet->getDelegate()->getStyle('D7')->getAlignment()->setVertical("TOP");
                $event->sheet->getDelegate()->getStyle('C8')->getAlignment()->setVertical("TOP");
          
            },



        ];
    }


}
