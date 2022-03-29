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

                // $event->sheet->getDelegate()->getRowDimension(14)->setRowHeight(8);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(1);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(4);

                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(4);
                // $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(14);
                // $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(1);
                $event->sheet->getStyle('F19:F29')->getAlignment()->setWrapText(true);
                $event->sheet->getStyle('H16:H17')->getAlignment()->setWrapText(true);
                $event->sheet->getStyle('C17')->getAlignment()->setWrapText(true);
                
            },



        ];
    }


}
