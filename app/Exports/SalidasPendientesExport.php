<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class SalidasPendientesExport implements FromView
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        return view(
            'almacen/export/SalidasPendientesExcel',
            [
                'data' => $this->data
            ]
        );
    }
}
