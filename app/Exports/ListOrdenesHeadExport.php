<?php

namespace App\Exports;
use App\Models\Logistica\Orden;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
// use App\Http\Controllers\OrdenController;
class ListOrdenesHeadExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data =Orden::reporteListaOrdenes();
        // $data = (new OrdenController)->listarOrdenes(null, null, null, null, null, null, null, null, null)['data']->get(['codigo']);
        return collect($data);


    }
    public function headings(): array
    {
        return [
            "Cuadro costos",
            "Proveedor", 
            "Nro. orden", 
            "Req/Cuadro comp.",
            "Estado",
            "Fecha vencimiento",
            "Fecha llegada",
            "Estado aprobación CC",
            "Fecha aprobación CC",
            "Fecha requerimiento",
            "Leadtime",
            "Empresa / Sede",
            "Moneda",
            "Condición",
            "Fecha em.",
            "Tiem. atenc. log.",
            "Tiem. atenc. prov.",
            "Facturas",
            "Monto presup. CC",
            "Monto orden",
            "Detalle pago",
            "Tipo cambio compra"
        ];
    }
}
