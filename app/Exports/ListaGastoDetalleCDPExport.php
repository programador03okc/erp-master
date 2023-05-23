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

class ListaGastoDetalleCDPExport implements FromView,WithColumnFormatting, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct()
    {

    }
    public function view(): View{
        $data=[];
            $detalle = (new ReporteGastoController)->dataGastoCDP();   

            foreach ($detalle as $key => $value) {

                $data[]=[
                    'codigo_oportunidad'=>$value->codigo_oportunidad,
                    'oportunidad'=> str_replace("'", "", str_replace("", "" ,$value->oportunidad)),
                    'tipo_negocio'=>$value->tipo_negocio,
                    'moneda_oportunidad'=>$value->moneda_oportunidad == 's'?'S/':($value->moneda_oportunidad == 'd'?'$':''),
                    'importe_oportunidad'=>$value->importe_oportunidad,
                    'fecha_registro_oportunidad'=> $value->fecha_registro_oportunidad !=null ? date('d/m/Y', strtotime($value->fecha_registro_oportunidad)):'',
                    'estado_oportunidad'=>$value->estado_oportunidad,
                    'part_no'=> str_replace("'", "", str_replace("", "" ,$value->part_no)),
                    'descripcion'=> str_replace("'", "", str_replace("", "" ,$value->descripcion)),
                    'pvu_oc'=>$value->pvu_oc,
                    'flete_oc'=>$value->flete_oc,
                    'cantidad'=>$value->cantidad,
                    'garantia'=>$value->garantia,
                    'origen_costo'=>$value->origen_costo,
                    'razon_social_proveedor'=> str_replace("'", "", str_replace("", "" ,$value->razon_social_proveedor)),
                    'moneda_costo_unitario_proveedor'=>$value->moneda_costo_unitario_proveedor == 's'?'S/':($value->moneda_costo_unitario_proveedor == 'd'?'$':''),
                    'costo_unitario_proveedor'=>$value->costo_unitario_proveedor,
                    'plazo_proveedor'=>$value->plazo_proveedor,
                    'flete_proveedor'=>$value->flete_proveedor,
                    'fondo_proveedor'=>$value->fondo_proveedor,
                    'moneda_costo_compra'=>$value->moneda_costo_unitario_proveedor == 's'?'S/':($value->moneda_costo_unitario_proveedor == 'd'?'$':''),
                    'importe_costo_compra'=>$value->cantidad * $value->costo_unitario_proveedor,
                    'importe_costo_compra_soles'=> ($value->cantidad * $value->costo_unitario_proveedor) * $value->tipo_cambio,
                    'total_flete_proveedor'=>$value->cantidad * $value->flete_proveedor,
                    'costo_compra_mas_flete_proveedor'=>($value->cantidad * $value->flete_proveedor) + ( $value->cantidad * $value->costo_unitario_proveedo * $value->tipo_cambio ),
                    'nombre_autor'=>$value->nombre_autor,
                    'created_at'=> $value->created_at !=null ? date('d/m/Y', strtotime($value->created_at)):'',
                    'monto_adjudicado_soles'=> $value->pvu_oc * $value->cantidad,
                    'ganancia'=> ($value->pvu_oc * $value->cantidad) - (($value->cantidad * $value->flete_proveedor) + ( $value->cantidad * $value->costo_unitario_proveedo * $value->tipo_cambio )),
                    'tipo_cambio'=> $value->tipo_cambio,
                    'estado_aprobacion'=> $value->estado_aprobacion,
                ];
            }


        return view('finanzas.export.lista_gasto_detalle_cdp_export', [
            'items'        =>  $data
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('I2:I'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('O2:O'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:AE')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        return [
            1    => ['font' => ['bold' => true] ],
            'A:AE'  => ['font' => ['size' => 10]]
        ];
    }
    
    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'V' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'W' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AA' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AB' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
        ];
    }
}
