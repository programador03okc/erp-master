<?php

namespace App\Exports;

use App\Http\Controllers\Finanzas\Reportes\ReporteGastoController;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ListaGastoDetalleRequerimientoPagoExport implements FromView,WithColumnFormatting, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct()
    {

    }
    public function view(): View{
        $data=[];
            $requerimientosDetalle = (new ReporteGastoController)->dataGastoDetalleRequerimientoPago();   

            foreach ($requerimientosDetalle as $key => $value) {

                $data[]=[
                    'prioridad'=>$value->prioridad,
                    'codigo'=> $value->codigo,
                    'descripcion_centro_costo'=> $value->descripcion_centro_costo,
                    'descripcion_partida_padre'=> $value->descripcion_partida_padre,
                    'codigo_presupuesto_old'=> $value->codigo_presupuesto_old,
                    'descripcion_presupuesto_old'=> $value->descripcion_presupuesto_old,
                    'codigo_presupuesto_interno'=> $value->codigo_presupuesto_interno,
                    'descripcion_presupuesto_interno'=> $value->descripcion_presupuesto_interno,
                    'partida'=> $value->partida,
                    'descripcion_partida'=> $value->descripcion_partida,
                    'descripcion_partida_presupuesto_interno'=> $value->descripcion_partida_presupuesto_interno,
                    'codigo_sub_partida_presupuesto_interno'=> $value->codigo_sub_partida_presupuesto_interno,
                    'descripcion_sub_partida_presupuesto_interno'=> $value->descripcion_sub_partida_presupuesto_interno,
                    'padre_centro_costo'=> $value->padre_centro_costo,
                    'padre_descripcion_centro_costo'=> $value->padre_descripcion_centro_costo,
                    'centro_costo'=> $value->centro_costo,
                    'codigo_oportunidad'=> str_replace("'", "", str_replace("", "" ,$value->codigo_oportunidad)),
                    'motivo'=> str_replace("'", "", str_replace("", "" ,$value->motivo)),
                    'concepto'=> str_replace("'", "", str_replace("", "" ,$value->concepto)),
                    'descripcion'=>  str_replace("'", "", str_replace("", "" ,$value->descripcion)),
                    'fecha_registro'=> $value->fecha_registro !=null ? date('d/m/Y', strtotime($value->fecha_registro)):'',
                    'tipo_cambio'=> $value->tipo_cambio,
                    'fecha_aprobacion'=> $value->fecha_aprobacion !=null ? date('d/m/Y', strtotime($value->fecha_aprobacion)):'',
                    'usuario_aprobador'=> $value->usuario_aprobador,
                    'nombre_destinatario'=> $value->nombre_destinatario,
                    'tipo_documento_destinatario'=> $value->tipo_documento_destinatario,
                    'nro_documento_destinatario'=> $value->nro_documento_destinatario,
                    'hora_registro'=> $value->fecha_registro!=null ? date('H:i:s', strtotime($value->fecha_registro)):'',
                    'tipo_requerimiento'=> $value->tipo_requerimiento,
                    'empresa_razon_social'=> $value->empresa_razon_social,
                    'sede'=> $value->sede,
                    'grupo'=> $value->grupo,
                    'division'=> $value->division,
                    'descripcion_proyecto'=> str_replace("'", "", str_replace("", "" ,$value->descripcion_proyecto)),
                    'simbolo_moneda'=> str_replace("'", "", str_replace("", "" ,$value->simbolo_moneda)),
                    'cantidad'=> $value->cantidad,
                    'precio_unitario'=> $value->precio_unitario,
                    'subtotal'=> $value->subtotal,
                    'subtotal_soles'=> $value->subtotal_soles,
                    'estado_requerimiento'=> $value->estado_requerimiento,
                    'comentario'=> str_replace("'", "", str_replace("", "" ,$value->comentario))
                ];
            }


        return view('finanzas.export.lista_gasto_detalle_requerimiento_pago_export', [
            'items'        =>  $data
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('U3:U'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('AG3:AG'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
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
            'AC' => NumberFormat::FORMAT_NUMBER,
            'AD' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AE' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AG' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'V' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AH' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }
}
