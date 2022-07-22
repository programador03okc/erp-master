<?php

namespace App\Exports;

use App\Http\Controllers\Tesoreria\Facturacion\PendientesFacturacionController;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ListadoVentasExternasExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
    }
    public function view(): View{

        $requerimientos = (new PendientesFacturacionController)->obtenerListadoVentasExternasExport()->orderBy('fecha_facturacion','desc')->get();

        return view('necesidades.reportes.listado_ventas_externas_export', [
            'requerimientos' => $requerimientos
        ]);
    }
}
