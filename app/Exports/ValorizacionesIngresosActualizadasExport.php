<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ValorizacionesIngresosActualizadasExport implements FromView
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    public function view(): View
    {
        return view(
            'almacen/export/actualizacionValorizacionIngresos',
            [
                'data' => $this->data
            ]
        );
    }
}
