<?php

namespace App\Exports;

use App\Http\Controllers\Tesoreria\Facturacion\PendientesFacturacionController;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ListadoVentasInternasExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
    }

    public function view(): View{

        $requerimientos = (new PendientesFacturacionController)->obtenerListadoVentasInternasExport()->orderBy('fecha_registro','desc')->get();

        return view('necesidades.reportes.listado_ventas_internas_export', [
            'requerimientos' => $requerimientos
        ]);
    }
}
