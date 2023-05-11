<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
class PresupuestoInternoEjecutadoExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        // dd($this->data);exit;
        return view('finanzas.export.presupuesto_interno_monto_ejecutado',['data' => $this->data,]);
    }
}
