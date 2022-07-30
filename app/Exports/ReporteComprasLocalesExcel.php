<?php

namespace App\Exports;

use App\Http\Controllers\ReporteLogisticaController;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class ReporteComprasLocalesExcel implements FromView
{


    public function __construct(string $idEmpresa,string $idSede, string $fechaRegistroDesde, string $fechaRegistroHasta)
    {
        $this->idEmpresa = $idEmpresa;
        $this->idsede = $idSede;
        $this->fechaRegistroDesde = $fechaRegistroDesde;
        $this->fechaRegistroHasta = $fechaRegistroHasta;
    }

    public function view(): View{
        $idEmpresa= $this->idEmpresa;
        $idSede = $this->idsede;
        $fechaRegistroDesde = $this->fechaRegistroDesde;
        $fechaRegistroHasta = $this->fechaRegistroHasta;

		
        $comLocales = (new ReporteLogisticaController)->obtenerDataComprasLocales($idEmpresa,$idSede,$fechaRegistroDesde,$fechaRegistroHasta)->orderBy('fecha_emision','desc')->get();

        $data=[];
        foreach($comLocales as $element){
            $data[]=[
                'descripcion'=> $element->descripcion??'',
                'razon_social_proveedor'=> $element->razon_social_proveedor??'',
                'nro_documento_proveedor'=> $element->nro_documento_proveedor??'',
                'direccion_proveedor'=> $element->direccion_proveedor??'',
                'ubigeo_proveedor'=> $element->ubigeo_proveedor??'',
                'fecha_emision_doc_com'=> $element->fecha_emision_doc_com??'',
                'fecha_pago'=> $element->fecha_pago??'',
                'tiempo_cancelacion'=> $element->tiempo_cancelacion??'',
                'moneda_doc_com'=> $element->moneda_doc_com??'',
                'total_igv_doc_com'=> $element->total_igv_doc_com??'',
                'total_a_pagar_doc_com'=> $element->total_a_pagar_doc_com??'',
                'tipo_doc_com'=> $element->tipo_doc_com??'',
                'nro_doc_com'=> $element->nro_doc_com??'',
                'descripcion_sede_empresa'=> $element->descripcion_sede_empresa??'',
                'descripcion_grupo'=> $element->descripcion_grupo??''
            ];
        }
        return view('logistica.reportes.view_compras_locales_export', [
            'comprasLocales' => $data
        ]);
    }

}
