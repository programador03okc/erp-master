<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalidasProcesadasExport implements FromView
{
    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function view(): View
    {
        return view(
            'almacen/export/salidasProcesadasExcel',
            [
                'data' => $this->data,
            ]
        );
    }
}
