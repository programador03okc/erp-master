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

            $incidencias = (new FichaReporteController)->obtenerListadoIncidencias($value->id_incidencia);
            // var_dump($incidencias);exit;
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
                        'fecha_registro_detalle'=>$item->fecha_registro,

                        'serie'=>$incidencias->serie,
                        'marca'=>$incidencias->marca,
                        'producto'=>$incidencias->producto,
                        'tipo'=>$incidencias->tipo,
                        'modelo'=>$incidencias->modelo,

                        'tipo_de_falla'=>$incidencias->tipo_falla,
                        'modo'=>$incidencias->modo,
                        'tipo_garantía'=>$incidencias->tipo_garantia,
                        'tipo_de_servicio'=>$incidencias->tipo_servicio,
                        'medio_reporte'=>$incidencias->medio,
                        'atiende'=>$incidencias->atiende,
                        'equipo_operativo'=>$incidencias->equipo_operativo==true?'SI':'NO',

                        'conformidad'=>$incidencias->conformidad,
                        'nro_de_caso'=>$incidencias->numero_caso,

                        'departamento_text'=>$incidencias->departamento_text,
                        'provincia_text'=>$incidencias->provincia_text,
                        'distrito_text'=>$incidencias->distrito_text,
                        'importe_gastado'=>$incidencias->importe_gastado,
                        'parte_reemplazada'=>$incidencias->parte_reemplazada,
                        'comentarios_cierre'=>$incidencias->comentarios_cierre
                    )
                );

            }

        }
        return view(
            'cas/export/incidenciasExcel',
            [
                'data' => $data_export_excel,
                // 'data_detalle' => $data_json
            ]
        );
    }
}
