<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class DespachosExternosExport implements FromView
{
    public $data;
    public $select_mostrar;

    public function __construct($data, $select_mostrar)
    {
        $this->data = $data;
        $this->select_mostrar = $select_mostrar;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        return view(
            'almacen/export/despachosExternosExcel',
            [
                'data' => $this->data->get(),
                'select_mostrar' => $this->select_mostrar
            ]
        );
    }
}
