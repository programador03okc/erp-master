<?php

namespace App\Exports;

use App\Http\Controllers\Tesoreria\RegistroPagoController;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class OrdenesCompraServicioExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View{

        $ingresos = (new RegistroPagoController)->obtenerOrdenesCompraServicio()->orderBy('id_orden_compra', 'ASC')->get();

        return view('tesoreria.reportes.ordenes_compra_servicio_export_excel', [
            'requerimientos' => $ingresos
        ]);
    }
}
