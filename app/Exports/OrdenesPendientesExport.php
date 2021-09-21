<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class OrdenesPendientesExport implements FromView
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
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        return view(
            'almacen/export/ordenesPendientesExcel',
            [
                'data' => $this->data->get(),
                'finicio' => $this->finicio,
                'ffin' => $this->ffin
            ]
        );
    }
}
