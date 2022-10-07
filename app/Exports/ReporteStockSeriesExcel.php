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
        $data = (new AlmacenController)->obtener_data_stock_series();
        return view('almacen.reportes.stock_series_excel', [
            'stockSeries' => $data
        ]);
    }

}
