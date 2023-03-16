<?php

namespace App\Exports;

use App\Models\Gerencial\CobranzaView;
use App\Models\Gerencial\Penalidad;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CobranzaExport implements FromView, WithStyles
{
    public function view(): View {
        $data = CobranzaView::select(['*']);

        if (session()->has('cobranzaEmpresa')) {
            $data = $data->where('empresa', session()->get('cobranzaEmpresa'));
        }

        if (session()->has('cobranzaFase')) {
            $data = $data->where('fase', session()->get('cobranzaFase'));
        }

        if (session()->has('cobranzaPeriodo')) {
            $data = $data->where('periodo', session()->get('cobranzaPeriodo'));
        }

        if (session()->has('cobranzaEmisionDesde')) {
            $data = $data->whereBetween('fecha_emision', [session()->get('cobranzaEmisionDesde'), session()->get('cobranzaEmisionHasta')]);
        }
        $data = $data->orderBy('fecha_emision', 'desc')->get();
        foreach ($data as $key => $value) {
            #penalidad
            $penalidad = Penalidad::where('id_registro_cobranza',$value->id)->where('tipo','PENALIDAD')
            ->orderBy('id_penalidad', 'desc')
            ->first();
            if ($penalidad) {
                $value->penalidad = $penalidad->tipo;
                $value->penalidad_importe = $penalidad->monto;
            }
            #retencion
            $penalidad = Penalidad::where('id_registro_cobranza',$value->id)->where('tipo','RETENCION')
            ->orderBy('id_penalidad', 'desc')
            ->first();
            if ($penalidad) {
                $value->retencion = $penalidad->tipo;
                $value->retencion_importe = $penalidad->monto;
            }
            #detraccion
            $penalidad = Penalidad::where('id_registro_cobranza',$value->id)->where('tipo','DETRACCION')
            ->orderBy('id_penalidad', 'desc')
            ->first();
            if ($penalidad) {
                $value->detraccion = $penalidad->tipo;
                $value->detraccion_importe = $penalidad->monto;
            }
        }

        return view('gerencial.reportes.cobranzas_export', ['data' => $data]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('D3:D' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('W3:W' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:W')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        return [
            'A3:Z' => [
                'font' => [ 'family' => 'Arial', 'size' => 10 ]
            ],
        ];
    }
}
