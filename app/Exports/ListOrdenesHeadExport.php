<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Http\Controllers\OrdenController;
class ListOrdenesHeadExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct(string $filtro)
    {
        $this->filtro = $filtro;
    }
    
    public function collection()
    {
        // $data =Orden::reporteListaOrdenes();
        $data =(new OrdenController)->reporteListaOrdenes($this->filtro);
        return collect($data);


    }
    public function headings(): array
    {
        return [
            "Nro. orden", 
            "Tipo orden", 
            "Cod. softlink",
            "Cod. req.",
            "Cod. CDP",
            "Empresa - sede",
            "Moneda",
            "Fecha emisión",
            "Fecha llegada",
            "Tiempo atención logística",
            "Fecha último ingreso almacén",
            "Proveedor",
            "Condición compra",
            "Crédito días",
            "Estado de orden",
            "Estado de pago",
            "Importe total orden",
            "Importe total CDP"
   
        ];
    }
}
