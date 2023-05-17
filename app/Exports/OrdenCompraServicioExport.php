<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class OrdenCompraServicioExport implements FromView
{
    public $data;
    public function __construct(string $data)
    {
        $this->data = $data;
    }
    public function view(): View{
        $json = json_decode($this->data);
        // va
        // dd(json_decode($this->data));exit;

        return view('tesoreria.Pagos.export.ordenes_compra_servicio', [
            "data"=>$json
        ]);
    }
}
