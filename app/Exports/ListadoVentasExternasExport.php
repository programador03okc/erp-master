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

        $data_json = [];
        $requerimientos = (new PendientesFacturacionController)->obtenerListadoVentasExternasExport()->orderBy('fecha_facturacion','desc')->get();

        foreach ($requerimientos as $key => $value) {
            $requerimientosDetalle = (new PendientesFacturacionController)->obtenerListadoVentasExternasDetalleExport($value->id_requerimiento);

            foreach ($requerimientosDetalle as $key => $item) {
               array_push($data_json,$item);
            }

        }

        // var_dump($data_json);exit;

        return view('necesidades.reportes.listado_ventas_externas_export', [
            'requerimientos' => $requerimientos, 'requerimientosDetalle'=>$data_json
        ]);
    }
}
