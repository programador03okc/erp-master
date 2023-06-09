<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Http\Controllers\OrdenController;
class ListOrdenesDetailExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // $data =Orden::reporteListaOrdenes();
        $data =(new OrdenController)->reporteListaItemsOrdenes();
        return collect($data);


    }
    public function headings(): array
    {
        return [
            "Cod. orden", 
            "Cod. req.", 
            "Cod. orden softlink", 
            "Nro. Orden MGC",
            "Concepto", 
            "Cliente", 
            "Proveedor", 
            "Cod.AM",
            "Nombre AM",
            "Marca", 
            "Categoría", 
            "Cod. producto", 
            "Part number", 
            "Cod. softlink", 
            "Descripción", 
            "Lugar entrega MGC",
            "Cantidad", 
            "Unitdad medida", 
            "Moneda", 
            "Precio unit. Ord.", 
            "Precio unit. CDP", 
            "Fecha emisión orden", 
            "Plazo entrega", 
            "Fecha ingreso almacén", 
            "Tiempo atención proveedor", 
            "Empresa - sede", 
            "Estado" 
        ];
    }
}
