<?php

namespace App\Exports;

use App\Http\Controllers\Logistica\RequerimientoController;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class solicitudCotizacionExcel implements FromView
{


    public function __construct(int $idRequerimiento)
    {
        $this->idRequerimiento = $idRequerimiento;
    }

    public function view(): View{
        $idRequerimiento= $this->idRequerimiento;
        $requerimiento = (new RequerimientoController)->mostrarRequerimiento($idRequerimiento,0);

        return view('necesidades.reportes.view_solicitud_cotizacion_export', [
            'requerimiento' => $requerimiento
        ]);
    }

}
