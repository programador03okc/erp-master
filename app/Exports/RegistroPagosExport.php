<?php

namespace App\Exports;

use App\Http\Controllers\Tesoreria\RegistroPagoController;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;

class RegistroPagosExport implements FromView
{
    public function view(): View{

        $ingresos = (new RegistroPagoController)->obtenerRegistroPagos()->orderBy('id_requerimiento_pago', 'ASC')->get();

        return view('tesoreria.reportes.registro_pagos_export_excel', [
            'requerimientos' => $ingresos
        ]);
    }
}
