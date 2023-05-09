<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Tesoreria\RequerimientoPagoController;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
class ListadoItemsRequerimientoPagoExport implements FromView,WithColumnFormatting, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     //
    // }
    public function __construct(string $meOrAll, string $idEmpresa,string $idSede,string $idGrupo,string $idDivision, string $fechaRegistroDesde, string $fechaRegistroHasta, string $idEstado)
    {
        $this->meOrAll = $meOrAll;
        $this->idEmpresa = $idEmpresa;
        $this->idSede = $idSede;
        $this->idGrupo = $idGrupo;
        $this->idDivision = $idDivision;
        $this->fechaRegistroDesde = $fechaRegistroDesde;
        $this->fechaRegistroHasta = $fechaRegistroHasta;
        $this->idEstado = $idEstado;
    }
    public function view(): View{
        $meOrAll= $this->meOrAll;
        $idEmpresa= $this->idEmpresa;
        $idSede = $this->idSede;
        $idGrupo = $this->idGrupo;
        $idDivision = $this->idDivision;
        $fechaRegistroDesde = $this->fechaRegistroDesde;
        $fechaRegistroHasta = $this->fechaRegistroHasta;
        $idEstado = $this->idEstado;

        $data=[];
            $requerimientosDetalle = (new RequerimientoPagoController)->obtenerItemsRequerimientoPagoElaborados($meOrAll, $idEmpresa, $idSede, $idGrupo, $idDivision, $fechaRegistroDesde, $fechaRegistroHasta, $idEstado);   

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
                    'centro_costo'=> $value->centro_costo,
                    'codigo_oportunidad'=> str_replace("'", "", str_replace("", "" ,$value->codigo_oportunidad)),
                    'motivo'=> str_replace("'", "", str_replace("", "" ,$value->motivo)),
                    'concepto'=> str_replace("'", "", str_replace("", "" ,$value->concepto)),
                    'descripcion'=>  str_replace("'", "", str_replace("", "" ,$value->descripcion)),
                    'fecha_registro'=> date('d/m/Y', strtotime($value->fecha_registro)),
                    'hora_registro'=> date('H:i:s', strtotime($value->fecha_registro)),
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
                    'estado_requerimiento'=> $value->estado_requerimiento,
                    'comentario'=> str_replace("'", "", str_replace("", "" ,$value->comentario))
                ];
            }


        return view('necesidades.reportes.listado_items_requerimiento_pago_export_excel', [
            'items'        =>  $data
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
