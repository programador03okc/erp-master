<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class IngresosProcesadosExport implements FromView
{
    public $data;
    public $finicio;
    public $ffin;

    public function __construct($data, $finicio, $ffin)
    {
        $this->data = $data;
        $this->finicio = $finicio;
        $this->ffin = $ffin;
    }
    public function view(): View
    {
        return view(
            'almacen/export/ingresosProcesadosExcel',
            [
                'data' => $this->data->get(),
                'finicio' => $this->finicio,
                'ffin' => $this->ffin
            ]
        );
    }
}
