<?php

namespace App\Exports;

use App\Http\Controllers\AlmacenController;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;
use phpDocumentor\Reflection\Types\Integer;

class ReporteStockSeriesExcel implements FromView
{


    public function __construct()
    {
    }

    public function view(): View{
        $stockSeries = (new AlmacenController)->obtener_data_stock_series();

        // foreach($stockSeries as $element){
        //     $data[]=[
        //         'almacen'=>$element->almacen??'',
        //         'codigo_producto'=>$element->codigo_producto??'',
        //         'part_number'=>$element->part_number??'',
        //         'serie'=>$element->serie??'',
        //         'descripcion'=>$element->descripcion??'',
        //         'unidad_medida'=>$element->unidad_medida??'',
        //         'afecto_igv'=>$element->afecto_igv??'',
        //         'fecha_ingreso'=>$element->fecha_ingreso??'',
        //         'guia_fecha_emision'=>$element->guia_fecha_emision??'',
        //         'documento_compra'=>$element->documento_compra??''
        //     ];
        // }

        return view('almacen.reportes.stock_series_excel', [
            'stockSeries' => $stockSeries
        ]);
    }

}
