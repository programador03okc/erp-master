<?php

namespace App\Exports;


use App\Http\Controllers\Almacen\Reporte\SaldosController;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReporteSaldosSeriesExport implements FromView, WithColumnFormatting, WithStyles
{
    public function view(): View
    {
        $query = (new SaldosController)->reporteSaldosLista()->get();

        foreach ($query as $d) {
            $movimientos = DB::table('almacen.mov_alm')
                ->join('almacen.mov_alm_det', 'mov_alm_det.id_mov_alm', '=', 'mov_alm.id_mov_alm')
                ->select(
                    // 'mov_alm.codigo',
                    'mov_alm.id_tp_mov',
                    'mov_alm.fecha_emision',
                    'mov_alm_det.id_producto',
                    'mov_alm_det.cantidad',
                    'mov_alm_det.valorizacion',
                    'mov_alm_det.valorizacion',
                )
                ->where('mov_alm.id_almacen', $d->id_almacen)
                ->where('mov_alm.fecha_emision', '<=', session()->get('filtroFecha'))
                ->where('mov_alm_det.id_producto', $d->id_producto)
                ->where('mov_alm_det.estado', 1)
                ->orderBy('mov_alm.fecha_emision');

            if ($movimientos->count() > 0) {
                $saldo = 0;
                $saldo_valor = 0;
                $costo_promedio = 0;

                foreach ($movimientos->get() as $key) {
                    if ($key->id_tp_mov == 0 || $key->id_tp_mov == 1) {
                        $saldo += (float) $key->cantidad;
                        $saldo_valor += (float) $key->valorizacion;
                    } else if ($key->id_tp_mov == 2) {
                        $saldo -= (float) $key->cantidad;
                        $valor_salida = $costo_promedio * (float) $key->cantidad;
                        $saldo_valor -= (float) $valor_salida;
                    }
                    $costo_promedio = (float) ($saldo == 0 ? 0 : $saldo_valor / $saldo);
                }

                $reserva = ($d->cantidad_reserva == null) ? 0 : $d->cantidad_reserva;
                $disponibilidad = ($saldo - $reserva);

                $fecha = session()->get('filtroFecha');

                $series = DB::table('almacen.alm_prod_serie')
                    ->select('alm_prod_serie.serie')
                    ->leftjoin('almacen.guia_com_det', function ($join) {
                        $join->on('guia_com_det.id_guia_com_det', '=', 'alm_prod_serie.id_guia_com_det');
                        $join->where('guia_com_det.estado', 1);
                    })
                    ->leftjoin('almacen.guia_com', function ($join) use ($fecha) {
                        $join->on('guia_com.id_guia', '=', 'guia_com_det.id_guia_com');
                        $join->where('guia_com.fecha_almacen', '<=', $fecha);
                        $join->where('guia_com.estado', 1);
                    })
                    ->leftjoin('almacen.guia_com_det as guia_sobrante_det', function ($join) {
                        $join->on('guia_sobrante_det.id_sobrante', '=', 'alm_prod_serie.id_sobrante');
                        $join->where('guia_sobrante_det.estado', 1);
                    })
                    ->leftjoin('almacen.guia_com as guia_sobrante', function ($join) use ($fecha) {
                        $join->on('guia_sobrante.id_guia', '=', 'guia_sobrante_det.id_guia_com');
                        $join->where('guia_sobrante.fecha_almacen', '<=', $fecha);
                        $join->where('guia_sobrante.estado', 1);
                    })
                    ->leftjoin('almacen.guia_com_det as guia_transformado_det', function ($join) {
                        $join->on('guia_transformado_det.id_transformado', '=', 'alm_prod_serie.id_transformado');
                        $join->where('guia_transformado_det.estado', 1);
                    })
                    ->leftjoin('almacen.guia_com as guia_transformado', function ($join) use ($fecha) {
                        $join->on('guia_transformado.id_guia', '=', 'guia_transformado_det.id_guia_com');
                        $join->where('guia_transformado.fecha_almacen', '<=', $fecha);
                        $join->where('guia_transformado.estado', 1);
                    })
                    ->where([
                        ['alm_prod_serie.id_prod', '=', $d->id_producto],
                        ['alm_prod_serie.id_almacen', '=', $d->id_almacen],
                        ['alm_prod_serie.estado', '!=', 7]
                    ])
                    ->whereNull('alm_prod_serie.id_guia_ven_det')
                    ->whereNull('alm_prod_serie.id_base')
                    ->get();

                $strSeries = '';

                if ($series !== null) {
                    foreach ($series as $s) {
                        if ($strSeries !== '') {
                            $strSeries .= ', ' . $s->serie;
                        } else {
                            $strSeries = $s->serie;
                        }
                    }
                }

                if ($reserva > 0 || $disponibilidad > 0 || $saldo > 0) {
                    $data[] = [
                        'id_producto'           => $d->id_producto,
                        'id_almacen'            => $d->id_almacen,
                        'codigo'                => ($d->codigo != null) ?  str_replace("'","",$d->codigo) : '',
                        'cod_softlink'          => ($d->cod_softlink != null) ?  str_replace("'","",$d->cod_softlink) : '',
                        'part_number'           => ($d->part_number != null) ?  str_replace("'","",trim($d->part_number)) : '',
                        'producto'              => str_replace("'","",trim($d->producto)),
                        'categoria'             => str_replace("'","",trim($d->categoria)),
                        'simbolo'               => ($d->simbolo != null) ?  $d->simbolo : '',
                        'valorizacion'          => $saldo_valor,
                        'costo_promedio'        => $costo_promedio,
                        'abreviatura'           => ($d->abreviatura != null) ?  $d->abreviatura : '',
                        'stock'                 => $saldo,
                        'reserva'               => $reserva,
                        'disponible'            => ($saldo - $reserva),
                        'almacen_descripcion'   => ($d->almacen_descripcion != null) ?  str_replace("'","",$d->almacen_descripcion) : '',
                        'count_series'          => count($series),
                        'series'                => str_replace("'","",$strSeries)
                    ];
                }
            }
        }
        return view('almacen.export.reporteSaldosSeries', ['saldos' => $data]);
    }


    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('D2:D' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:L')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
