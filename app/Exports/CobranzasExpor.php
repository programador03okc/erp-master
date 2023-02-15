<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class CobranzasExpor implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public $data;
    public function __construct(string $data)
    {
        $this->data = $data;
    }
    public function view(): View{
        $json = json_decode($this->data);
        // dd($this->data[0]["id_registro_cobranza"]);exit;
        return view('gerencial.reportes.cobranzas_export', [
            'data' => $json
        ]);
    }
}
