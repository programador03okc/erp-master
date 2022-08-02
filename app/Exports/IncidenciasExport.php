<?php

namespace App\Exports;

use App\Http\Controllers\Cas\FichaReporteController;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class IncidenciasExport implements FromView
{
    public $data;
    public $finicio;
    public $ffin;

    public function __construct($data)
    {
        $this->data = $data;
        // $this->finicio = $finicio;
        // $this->ffin = $ffin;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $data_json = [];
        $data_export_excel=[];
        $data_export = $this->data->get();
        foreach ($data_export as $key => $value) {
            $requerimientos = (new FichaReporteController)->obtenerListadoGestionincidenciasDetalleExport($value->id_incidencia);
            foreach ($requerimientos as $key => $item) {
                array_push($data_json,$item);

                array_push( $data_export_excel,(object)
                    array(
                        'codigo'=>$value->codigo,
                        'estado_doc'=>$value->estado_doc,
                        'empresa_razon_social'=>$value->empresa_razon_social,
                        'cliente'=>$value->cliente,
                        'nro_orden'=>$value->nro_orden,
                        'factura'=>$value->factura,
                        'usuario_final'=>$value->usuario_final,
                        'nombre_contacto'=>$value->nombre_contacto,
                        'cargo_contacto'=>$value->cargo_contacto,
                        'telefono_contacto'=>$value->telefono_contacto,
                        'direccion_contacto'=>$value->direccion_contacto,
                        'fecha_reporte'=>$value->fecha_reporte,
                        'nombre_corto'=>$value->nombre_corto,
                        'falla_reportada'=>$value->falla_reportada,

                        'id_incidencia_reporte'=>$item->id_incidencia_reporte,
                        'fecha_reporte_detalle'=>$item->fecha_reporte,
                        'nombre_corto_detalle'=>$item->nombre_corto,
                        'acciones_realizadas'=>$item->acciones_realizadas,
                        'fecha_registro_detalle'=>$item->fecha_registro
                    )
                );

            }

        }
        // $count_data_export = sizeof($data_export);
        // $count_data_json = sizeof($data_json);
        // $retVal = ($count_data_export<$count_data_json) ? $count_data_json : $count_data_export ;
        // $data_export_excel = [];
        // for ($i=0; $i < $retVal; $i++) {
        //     array_push( $data_export_excel,(object)
        //         array(
        //             'codigo'=>$i<$count_data_export?$data_export[$i]->codigo:' ',
        //             'estado_doc'=>$i<$count_data_export?$data_export[$i]->estado_doc:' ',
        //             'empresa_razon_social'=>$i<$count_data_export?$data_export[$i]->empresa_razon_social:' ',
        //             'cliente'=>$i<$count_data_export?$data_export[$i]->cliente:' ',
        //             'nro_orden'=>$i<$count_data_export?$data_export[$i]->nro_orden:' ',
        //             'factura'=>$i<$count_data_export?$data_export[$i]->factura:' ',
        //             'usuario_final'=>$i<$count_data_export?$data_export[$i]->usuario_final:' ',
        //             'nombre_contacto'=>$i<$count_data_export?$data_export[$i]->nombre_contacto:' ',
        //             'cargo_contacto'=>$i<$count_data_export?$data_export[$i]->cargo_contacto:' ',
        //             'telefono_contacto'=>$i<$count_data_export?$data_export[$i]->telefono_contacto:' ',
        //             'direccion_contacto'=>$i<$count_data_export?$data_export[$i]->direccion_contacto:' ',
        //             'fecha_reporte'=>$i<$count_data_export?$data_export[$i]->fecha_reporte:' ',
        //             'nombre_corto'=>$i<$count_data_export?$data_export[$i]->nombre_corto:' ',
        //             'falla_reportada'=>$i<$count_data_export?$data_export[$i]->falla_reportada:' ',

        //             'id_incidencia_reporte'=>$i<$count_data_json?$data_json[$i]->id_incidencia_reporte:' ',
        //             'fecha_reporte_detalle'=>$i<$count_data_json?$data_json[$i]->fecha_reporte:' ',
        //             'nombre_corto_detalle'=>$i<$count_data_json?$data_json[$i]->nombre_corto:' ',
        //             'acciones_realizadas'=>$i<$count_data_json?$data_json[$i]->acciones_realizadas:' ',
        //             'fecha_registro_detalle'=>$i<$count_data_json?$data_json[$i]->fecha_registro:' '
        //         )
        //     );
        // }
        return view(
            'cas/export/incidenciasExcel',
            [
                'data' => $data_export_excel,
                'data_detalle' => $data_json
                // 'finicio' => $this->finicio,
                // 'ffin' => $this->ffin
            ]
        );
    }
}
