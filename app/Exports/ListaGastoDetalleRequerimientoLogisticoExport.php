<?php

namespace App\Exports;

use App\Http\Controllers\Finanzas\Reportes\ReporteGastoController;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ListaGastoDetalleRequerimientoLogisticoExport implements FromView, WithColumnFormatting, WithStyles
{


    public function __construct()
    {
    }

    public function view(): View{
    
        $requerimientos = (new ReporteGastoController)->dataGastoDetalleRequerimientoLogistico();
        $data=[];
        foreach($requerimientos as $element){

            $data[]=[
                'prioridad'=> $element->prioridad,
                'codigo'=> $element->codigo,
                'codigo_oportunidad'=> $element->codigo_oportunidad,
                'codigo_presupuesto_old'=> $element->codigo_presupuesto_old,
                'descripcion_presupuesto_old'=> $element->descripcion_presupuesto_old,
                'codigo_presupuesto_interno'=> $element->codigo_presupuesto_interno,
                'descripcion_presupuesto_interno'=> $element->descripcion_presupuesto_interno,
                'padre_centro_costo'=> $element->padre_centro_costo,
                'padre_descripcion_centro_costo'=> $element->padre_descripcion_centro_costo,
                'centro_costo'=> $element->centro_costo,
                'descripcion_centro_costo'=> $element->descripcion_centro_costo,
                'descripcion_partida_padre'=> $element->descripcion_partida_padre,
                'partida'=> $element->partida,
                'descripcion_partida'=> $element->descripcion_partida,
                'descripcion_partida_presupuesto_interno'=>$element->descripcion_partida_presupuesto_interno,
                'codigo_sub_partida_presupuesto_interno'=>$element->codigo_sub_partida_presupuesto_interno,
                'descripcion_sub_partida_presupuesto_interno'=>$element->descripcion_sub_partida_presupuesto_interno,
                'concepto'=> str_replace("'", "", str_replace("", "", $element->concepto)),
                'descripcion'=> $element->descripcion_producto != null? str_replace("'", "", str_replace("", "" ,$element->descripcion_producto)): str_replace("'", "", str_replace("", "" ,$element->descripcion_detalle_requerimiento)),
                'cantidad'=> $element->cantidad,
                'precio_unitario'=> $element->precio_unitario,
                'subtotal'=> $element->subtotal,
                'simbolo_moneda'=> $element->simbolo_moneda,
                'nro_orden'=> $element->nro_orden,
                'estado_orden'=> $element->estado_orden,
                'estado_pago'=> $element->estado_pago,
                'codigo_producto'=> $element->codigo_producto,
                'cantidad_orden'=> $element->cantidad_orden,
                'precio_orden'=> $element->precio_orden,
                'subtotal_orden'=> $element->subtotal_orden,
                'simbolo_moneda_orden'=> $element->simbolo_moneda_orden,
                'subtotal_orden_considera_igv'=> $element->subtotal_orden_considera_igv,
                'fecha_requerimiento'=> date('d/m/Y', strtotime($element->fecha_requerimiento)),
                'tipo_cambio'=> $element->tipo_cambio,
                'tipo_requerimiento'=> $element->tipo_requerimiento,
                'empresa_razon_social'=> $element->empresa_razon_social,
                'sede'=> $element->sede,
                'grupo'=> $element->grupo,
                'division'=> $element->division,
                'descripcion_proyecto'=> $element->descripcion_proyecto,
                'observacion'=> $element->observacion,
                'motivo'=> $element->motivo,
                'fecha_registro'=> date('d/m/Y', strtotime($element->fecha_registro)),
                'hora_registro'=> date('H:i:s', strtotime($element->fecha_registro)),
                'estado_requerimiento'=> $element->estado_requerimiento,
                'estado_despacho'=>$element->estado_despacho,
                'nro_salida_int'=>$element->nro_salida_int,
                'nro_salida_ext'=>$element->nro_salida_ext,
                'almacen_salida'=>$element->almacen_salida,
                'fecha_salida'=>$element->fecha_salida,
                'codigo_producto_salida'=>$element->codigo_producto_salida,
                'cantidad_salida'=>$element->cantidad_salida,
                'moneda_producto_salida'=>$element->moneda_producto_salida,
                'costo_unitario_salida'=>$element->costo_unitario_salida,
                'costo_total_salida'=>$element->costo_total_salida

            ];
        }
        return view('finanzas.export.lista_gasto_detalle_requerimiento_logistico_export', [
            'items' => $data
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('Q3:Q'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('AE3:AE'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:AF')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        return [
            1    => ['font' => ['bold' => true] ],
            2    => ['font' => ['bold' => true] ],
            'A:AF'  => ['font' => ['size' => 10]]
        ];
    }
    
    public function columnFormats(): array
    {
        return [
            'AA' => NumberFormat::FORMAT_NUMBER,
            'AB' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AC' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
        ];
    }

}
