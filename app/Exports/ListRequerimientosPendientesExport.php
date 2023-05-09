<?php

namespace App\Exports;

use App\Http\Controllers\ComprasPendientesController;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class ListRequerimientosPendientesExport implements FromView

{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    public function view(): View{
    
        $data =(new ComprasPendientesController)->obtenerListarRequerimientosPendientes();
        return view('logistica.reportes.listado_requerimientos_pendientes_export', [
            'requerimientos' => $data
        ]);

    }
}
