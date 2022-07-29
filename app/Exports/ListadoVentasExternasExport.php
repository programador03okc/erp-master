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
        $data_export_excel=[];
        $requerimientos = (new PendientesFacturacionController)->obtenerListadoVentasExternasExport()->orderBy('fecha_facturacion','desc')->get();

        foreach ($requerimientos as $key => $value) {
            $requerimientosDetalle = (new PendientesFacturacionController)->obtenerListadoVentasExternasDetalleExport($value->id_requerimiento);

            foreach ($requerimientosDetalle as $key => $item) {
                // var_dump($item->condicion);exit;
                array_push( $data_export_excel,(object)
                    array(
                        'fecha_facturacion'=>$value->fecha_facturacion,
                        'obs_facturacion'=>$value->obs_facturacion,
                        'codigo'=>$value->codigo,
                        'concepto'=>$value->concepto,
                        'sede_descripcion'=>$value->sede_descripcion,
                        'razon_social'=>$value->razon_social,
                        'nombre_corto'=>$value->nombre_corto,
                        'nro_orden'=>$value->nro_orden,
                        'codigo_oportunidad'=>$value->codigo_oportunidad,

                        'id_requerimiento'=>$item->id_requerimiento,
                        'serie_numero'=>$item->serie_numero,
                        'empresa_razon_social'=>$item->empresa_razon_social,
                        'fecha_emision'=>$item->fecha_emision,
                        'razon_social_detalle'=>$item->razon_social,
                        'simbolo'=>$item->simbolo,
                        'total_a_pagar'=>$item->total_a_pagar,
                        'nombre_corto_detalle'=>$item->nombre_corto,
                        'condicion'=> ($item->condicion.' '.$item->credito_dias.' días')
                    )
                );
            }

        }

        // var_dump($data_json);exit;

        // $count_data_export = sizeof($requerimientos);
        // $count_data_json = sizeof($data_json);
        // $retVal = ($count_data_export<$count_data_json) ? $count_data_json : $count_data_export ;
        // $data_export_excel = [];
        // for ($i=0; $i < $retVal; $i++) {
        //     array_push( $data_export_excel,(object)
        //         array(
        //             'fecha_facturacion'=>$i<$count_data_export?$requerimientos[$i]->fecha_facturacion:' ',
        //             'obs_facturacion'=>$i<$count_data_export?$requerimientos[$i]->obs_facturacion:' ',
        //             'codigo'=>$i<$count_data_export?$requerimientos[$i]->codigo:' ',
        //             'concepto'=>$i<$count_data_export?$requerimientos[$i]->concepto:' ',
        //             'sede_descripcion'=>$i<$count_data_export?$requerimientos[$i]->sede_descripcion:' ',
        //             'razon_social'=>$i<$count_data_export?$requerimientos[$i]->razon_social:' ',
        //             'nombre_corto'=>$i<$count_data_export?$requerimientos[$i]->nombre_corto:' ',
        //             'nro_orden'=>$i<$count_data_export?$requerimientos[$i]->nro_orden:' ',
        //             'codigo_oportunidad'=>$i<$count_data_export?$requerimientos[$i]->codigo_oportunidad:' ',

        //             'id_requerimiento'=>$i<$count_data_json?$data_json[$i]->id_requerimiento:' ',
        //             'serie_numero'=>$i<$count_data_json?$data_json[$i]->serie_numero:' ',
        //             'empresa_razon_social'=>$i<$count_data_json?$data_json[$i]->empresa_razon_social:' ',
        //             'fecha_emision'=>$i<$count_data_json?$data_json[$i]->fecha_emision:' ',
        //             'razon_social'=>$i<$count_data_json?$data_json[$i]->razon_social:' ',
        //             'simbolo'=>$i<$count_data_json?$data_json[$i]->simbolo:' ',
        //             'total_a_pagar'=>$i<$count_data_json?$data_json[$i]->total_a_pagar:' ',
        //             'nombre_corto'=>$i<$count_data_json?$data_json[$i]->nombre_corto:' ',
        //             'condicion'=>$i<$count_data_json?$data_json[$i]->condicion.' '.$data_json[$i]->credito_dias.' días':' '
        //         )
        //     );
        // }

        return view('necesidades.reportes.listado_ventas_externas_export', [
            'requerimientos' => $data_export_excel, 'requerimientosDetalle'=>$data_json
        ]);
    }
}
