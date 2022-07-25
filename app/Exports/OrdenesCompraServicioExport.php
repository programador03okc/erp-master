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
        $data_json = [];
        $ingresos = (new RegistroPagoController)->obtenerOrdenesCompraServicio()->orderBy('id_orden_compra', 'ASC')->get();

        foreach ($ingresos as $key => $value) {
            $ingresosDetalle = (new RegistroPagoController)->obtenerOrdenesCompraServicioDetalle($value->id_orden_compra);

            foreach ($ingresosDetalle as $key => $item) {

                array_push($data_json,$item);
            }
        }
        return view('tesoreria.reportes.ordenes_compra_servicio_export_excel', [
            'requerimientos' => $ingresos, 'requerimientosDetalle'=>$data_json
        ]);
    }
}
