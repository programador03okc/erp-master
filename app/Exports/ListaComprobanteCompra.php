<?php

namespace App\Exports;

use App\Http\Controllers\ComprobanteCompraController;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class ListaComprobanteCompra implements FromView
{
    public function __construct()
    {
    }
    public function view(): View{
        $data = (new ComprobanteCompraController)->obtenerReporteComprobantes()->orderBy('fecha_emision','desc')->get();
        return view('necesidades.reportes.lista_comprobante_compra_export_excel', [
            'requerimientos' => $data
        ]);
    }
}
