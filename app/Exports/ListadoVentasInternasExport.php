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

        $data_export_excel=[];
        foreach ($requerimientos as $key => $value) {
            $requerimientosDetalles = (new PendientesFacturacionController)->obtenerListadoVentasInternasDetallesExport($value->id_guia_ven);
            foreach ($requerimientosDetalles as $key_re => $reque) {
                // array_push($data_json,$reque);

                array_push( $data_export_excel,(object)
                    array(
                        'serie'=> $value->serie.'-'.$value->numero,
                        'fecha_emision'=>$value->fecha_emision,
                        'sede_descripcion'=>$value->sede_descripcion,
                        'razon_social'=>$value->razon_social,
                        'nombre_corto_trans'=>$value->nombre_corto_trans,
                        'codigo_trans'=>$value->codigo_trans,

                        'id_requerimiento'=>$reque->id_requerimiento,
                        'serie_numero'=>$reque->serie_numero,
                        'empresa_razon_social'=>$reque->empresa_razon_social,
                        'fecha_emision_detalle'=>$reque->fecha_emision,
                        'razon_social_detalle'=>$reque->razon_social,
                        'simbolo'=>$reque->simbolo,
                        'total_a_pagar'=>$reque->total_a_pagar,
                        'nombre_corto'=>$reque->nombre_corto,
                        'condicion'=>$reque->credito_dias.' días'
                    )
                );


            }
        }

        // var_dump($data_export_excel);exit;
        // $count_data_export = sizeof($requerimientos);
        // $count_data_json = sizeof($data_json);
        // $retVal = ($count_data_export<$count_data_json) ? $count_data_json : $count_data_export ;
        // $data_export_excel = [];
        // for ($i=0; $i < $retVal; $i++) {
        //     array_push( $data_export_excel,(object)
        //         array(
        //             'serie'=>$i<$count_data_export?$requerimientos[$i]->serie.' '.$requerimientos[$i]->numero:' ',
        //             'fecha_emision'=>$i<$count_data_export?$requerimientos[$i]->fecha_emision:' ',
        //             'sede_descripcion'=>$i<$count_data_export?$requerimientos[$i]->sede_descripcion:' ',
        //             'razon_social'=>$i<$count_data_export?$requerimientos[$i]->razon_social:' ',
        //             'nombre_corto_trans'=>$i<$count_data_export?$requerimientos[$i]->nombre_corto_trans:' ',
        //             'codigo_trans'=>$i<$count_data_export?$requerimientos[$i]->codigo_trans:' ',

        //             'id_requerimiento'=>$i<$count_data_json?$data_json[$i]->id_requerimiento:' ',
        //             'serie_numero'=>$i<$count_data_json?$data_json[$i]->serie_numero:' ',
        //             'empresa_razon_social'=>$i<$count_data_json?$data_json[$i]->empresa_razon_social:' ',
        //             'fecha_emision_detalle'=>$i<$count_data_json?$data_json[$i]->fecha_emision:' ',
        //             'razon_social_detalle'=>$i<$count_data_json?$data_json[$i]->razon_social:' ',
        //             'simbolo'=>$i<$count_data_json?$data_json[$i]->simbolo:' ',
        //             'total_a_pagar'=>$i<$count_data_json?$data_json[$i]->total_a_pagar:' ',
        //             'nombre_corto'=>$i<$count_data_json?$data_json[$i]->nombre_corto:' ',
        //             'condicion'=>$i<$count_data_json?$data_json[$i]->condicion.' '.$data_json[$i]->credito_dias.' días':' '
        //         )
        //     );
        // }

        return view('necesidades.reportes.listado_ventas_internas_export', [
            'requerimientos' => $data_export_excel, 'requerimientoDetaller'=>$data_json
        ]);
    }
}
