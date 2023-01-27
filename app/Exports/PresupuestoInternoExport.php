<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
class PresupuestoInternoExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public $presupuesto_interno;
    public $presupuesto_interno_detalle;

    public function __construct($presupuesto_interno, $presupuesto_interno_detalle)
    {
        $this->presupuesto_interno = $presupuesto_interno;
        $this->presupuesto_interno_detalle = $presupuesto_interno_detalle;
    }
    public function view(): View
    {
        // dd($this->presupuesto_interno_detalle);exit;
        return view(
            'finanzas.export.presupuesto_interno',
            [
                'presupuesto_interno' => $this->presupuesto_interno,
                'ingresos'  => $this->presupuesto_interno_detalle['ingresos'],
                'costos'    => $this->presupuesto_interno_detalle['costos'],
                'gastos'    => $this->presupuesto_interno_detalle['gastos'],
            ]
        );
    }
}
