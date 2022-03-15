<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class IncidenciasExport implements FromView
{
    public $data;
    public $finicio;
    public $ffin;

    public function __construct($data)
    {
        $this->data = $data;
        // $this->finicio = $finicio;
        // $this->ffin = $ffin;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        return view(
            'cas/export/incidenciasExcel',
            [
                'data' => $this->data->get(),
                // 'finicio' => $this->finicio,
                // 'ffin' => $this->ffin
            ]
        );
    }
}
