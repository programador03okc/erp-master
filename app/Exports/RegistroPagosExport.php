<?php

namespace App\Exports;

use App\Http\Controllers\Tesoreria\RegistroPagoController;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;

class RegistroPagosExport implements FromView
{
    public function view(): View{
        $data_json = [];
        $ingresos = (new RegistroPagoController)->obtenerRegistroPagos()->orderBy('id_requerimiento_pago', 'ASC')->get();

        foreach ($ingresos as $key => $value) {
            $ingresosDetalle = (new RegistroPagoController)->obtenerRegistroPagosDetalle($value->id_requerimiento_pago);
            foreach ($ingresosDetalle as $key => $item) {
                array_push($data_json,$item);
            }
        }
        // var_dump($data_json);exit;
        return view('tesoreria.reportes.registro_pagos_export_excel', [
            'requerimientos' => $ingresos, 'requerimientosDetalle'=>$data_json
        ]);
    }
}
