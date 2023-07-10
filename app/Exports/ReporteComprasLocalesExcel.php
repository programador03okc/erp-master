<?php

namespace App\Exports;

use App\Http\Controllers\ReporteLogisticaController;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;
use phpDocumentor\Reflection\Types\Integer;

class ReporteComprasLocalesExcel implements FromView
{

    public function view(): View
    {
        $comLocales = (new ReporteLogisticaController)->obtenerReporteCompras();

        $data = [];
        foreach($comLocales as $element){
            $data[] = [
                'codigo'                                    => $element->codigo ?? '',
                'fecha_emision'                             =>date('Y-m-d', strtotime($element->fecha_emision)) ?? '',
                'codigo_requerimiento'                      => $element->codigo_requerimiento ?? '',
                'codigo_producto'                           => $element->codigo_producto ?? '',
                'descripcion'                               => str_replace("'", "", str_replace("", "", $element->descripcion)) ?? '',
                'rubro_contribuyente'                       => $element->rubro_contribuyente ?? '',
                'razon_social_contribuyente'                => $element->razon_social_contribuyente ?? '',
                'nro_documento_contribuyente'               => $element->nro_documento_contribuyente ?? '',
                'direccion_contribuyente'                   => $element->direccion_contribuyente ?? '',
                'ubigeo_contribuyente'                      => $element->ubigeo_contribuyente ?? '',
                'fecha_emision_comprobante_contribuyente'   => $element->fecha_emision_comprobante_contribuyente ?? '',
                'fecha_pago'                                => $element->fecha_pago ?? '',
                'tiempo_cancelacion'                        => $element->tiempo_cancelacion ?? '',
                'cantidad'                                  => $element->cantidad ?? '',
                'moneda_orden'                              => $element->moneda_orden ?? '',
                'total_precio_soles_item'                   => $element->total_precio_soles_item ?? '',
                'total_precio_dolares_item'                 => $element->total_precio_dolares_item ?? '',
                'total_a_pagar_soles'                       => $element->total_a_pagar_soles ?? '',
                'total_a_pagar_dolares'                     => $element->total_a_pagar_dolares ?? '',
                'tipo_doc_com'                              => $element->tipo_doc_com ?? '',
                'nro_comprobante'                           => $element->nro_comprobante ?? '',
                'descripcion_sede_empresa'                  => $element->descripcion_sede_empresa ?? '',
                'descripcion_grupo'                         => $element->descripcion_grupo ?? '',
                'descripcion_proyecto'                      => $element->descripcion_proyecto ?? '',
                'descripcion_estado_pago'                   => $element->descripcion_estado_pago ?? '',
                'observacion_orden'                               => $element->observacion_orden ?? ''
            ];
        }
        return view('logistica.reportes.view_compras_locales_export', ['comprasLocales' => $data]);
    }

}
