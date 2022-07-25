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
        $data_json=[];
        $requerimientos = (new PendientesFacturacionController)->obtenerListadoVentasInternasExport()->orderBy('fecha_registro','desc')->get();


        foreach ($requerimientos as $key => $value) {
            $requerimientosDetalles = (new PendientesFacturacionController)->obtenerListadoVentasInternasDetallesExport($value->id_guia_ven);
            foreach ($requerimientosDetalles as $key_re => $reque) {
                array_push($data_json,$reque);
            }
        }
        // var_dump($data_json);exit;

        return view('necesidades.reportes.listado_ventas_internas_export', [
            'requerimientos' => $requerimientos, 'requerimientoDetaller'=>$data_json
        ]);
    }
}
