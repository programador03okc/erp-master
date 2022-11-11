<?php

namespace App\Exports;

use App\Models\Almacen\Almacen;
use App\Models\almacen\softlink\ProductoSerie;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductoSerieExport implements FromView, WithColumnFormatting, WithStyles
{
    public function view() : View
    {
        $productos = [];
        $lista = ProductoSerie::select('id_almacen', 'nombre', 'fecha', 'documento', DB::raw('COUNT(*) AS conteo'))
                            ->groupBy('id_almacen', 'nombre', 'fecha', 'documento')->orderBy('nombre', 'asc')->get();

        foreach ($lista as $item) {
            $queryProducto = ProductoSerie::where([['nombre', $item->nombre], ['fecha', $item->fecha], ['documento', $item->documento]])->first();
            $querySeries = ProductoSerie::select('serie')->where([['nombre', $item->nombre], ['fecha', $item->fecha], ['documento', $item->documento]])->get();
            $queryAlmacen = Almacen::find($item->id_almacen);
            // $listaSerie = [];
            $listaSerie = '';

            foreach ($querySeries as $key) {
                // array_push($listaSerie, $key->serie);
                $listaSerie .= $key->serie.' ';
            }

            $productos[] = [
                "almacen"   => $queryAlmacen->descripcion,
                "producto"  => $item->nombre,
                "fecha"     => $item->fecha,
                "periodo"   => date('Y', strtotime($item->fecha)),
                "documento" => $item->documento,
                "codigo"    => $queryProducto->codigo,
                "total"     => $item->conteo,
                "series"    => $listaSerie,
            ];
        }
        return view('migraciones.exportar.reporte-series', ['lista' => $productos]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A2:A' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('C2:C' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('H2:H' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:H')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A:H')->getFont()->setSize(10);
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
        ];
    }
}
