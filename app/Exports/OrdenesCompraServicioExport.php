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

        $count_data_export = sizeof($ingresos);
        $count_data_json = sizeof($data_json);
        $retVal = ($count_data_export<$count_data_json) ? $count_data_json : $count_data_export ;
        $data_export_excel = [];
        for ($i=0; $i < $retVal; $i++) {
            array_push( $data_export_excel,(object)
                array(
                    'prioridad'=>$i<$count_data_export?$ingresos[$i]->prioridad:' ',
                    'requerimientos'=>$i<$count_data_export?$ingresos[$i]->requerimientos:' ',
                    'codigo_empresa'=>$i<$count_data_export?$ingresos[$i]->codigo_empresa:' ',
                    'codigo'=>$i<$count_data_export?$ingresos[$i]->codigo:' ',
                    'razon_social'=>$i<$count_data_export?$ingresos[$i]->razon_social:' ',
                    'fecha_solicitud_pago'=>$i<$count_data_export?$ingresos[$i]->fecha_solicitud_pago:' ',
                    'simbolo'=>$i<$count_data_export?$ingresos[$i]->simbolo:' ',
                    'monto_total'=>$i<$count_data_export?$ingresos[$i]->monto_total:' ',
                    'suma_pagado'=>$i<$count_data_export?$ingresos[$i]->suma_pagado:' ',
                    'estado_doc'=>$i<$count_data_export?$ingresos[$i]->estado_doc:' ',
                    'nombre_autorizado'=>$i<$count_data_export?$ingresos[$i]->nombre_autorizado:' ',
                    'fecha_autorizacion'=>$i<$count_data_export?$ingresos[$i]->fecha_autorizacion:' ',

                    'fecha_pago'=>$i<$count_data_json?$data_json[$i]->fecha_pago:' ',
                    'razon_social_empresa'=>$i<$count_data_json?$data_json[$i]->razon_social_empresa:' ',
                    'nro_cuenta'=>$i<$count_data_json?$data_json[$i]->nro_cuenta:' ',
                    'observacion'=>$i<$count_data_json?$data_json[$i]->observacion:' ',
                    'simbolo'=>$i<$count_data_json?$data_json[$i]->simbolo:' ',
                    'total_pago'=>$i<$count_data_json?$data_json[$i]->total_pago:' ',
                    'nombre_corto'=>$i<$count_data_json?$data_json[$i]->nombre_corto:' ',
                    'fecha_registro'=>$i<$count_data_json?$data_json[$i]->fecha_registro:' '
                )
            );
        }
        return view('tesoreria.reportes.ordenes_compra_servicio_export_excel', [
            'requerimientos' => $data_export_excel, 'requerimientosDetalle'=>$data_json
        ]);
    }
}
