<?php

namespace App\Exports;

use App\Http\Controllers\ReporteLogisticaController;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class ReporteComprasLocalesExcel implements FromView
{


    public function __construct(string $idEmpresa,string $idSede, string $fechaRegistroDesde, string $fechaRegistroHasta, string $fechaRegistroDesdeCancelacion, string $fechaRegistroHastaCancelacion, string $razonSocialProveedor)
    {
        $this->idEmpresa = $idEmpresa;
        $this->idsede = $idSede;
        $this->fechaRegistroDesde = $fechaRegistroDesde;
        $this->fechaRegistroHasta = $fechaRegistroHasta;
        $this->fechaRegistroDesdeCancelacion = $fechaRegistroDesdeCancelacion;
        $this->fechaRegistroHastaCancelacion = $fechaRegistroHastaCancelacion;
        $this->razonSocialProveedor = $razonSocialProveedor;
    }

    public function view(): View{
        $idEmpresa= $this->idEmpresa;
        $idSede = $this->idsede;
        $fechaRegistroDesde = $this->fechaRegistroDesde;
        $fechaRegistroHasta = $this->fechaRegistroHasta;

        $fechaRegistroDesdeCancelacion = $this->fechaRegistroDesdeCancelacion;
        $fechaRegistroHastaCancelacion = $this->fechaRegistroHastaCancelacion;
        $razonSocialProveedor = $this->razonSocialProveedor;

        $comLocales = (new ReporteLogisticaController)->obtenerDataComprasLocales($idEmpresa,$idSede,$fechaRegistroDesde,$fechaRegistroHasta,$fechaRegistroDesdeCancelacion,$fechaRegistroHastaCancelacion,$razonSocialProveedor)->orderBy('fecha_emision','desc')->get();

        $data=[];
        foreach($comLocales as $element){
            $data[]=[
                'codigo'=> $element->codigo??'',
                'codigo_requerimiento'=> $element->codigo_requerimiento??'',
                'codigo_producto'=> $element->codigo_producto??'',
                'descripcion'=> $element->descripcion??'',
                'rubro_contribuyente'=> $element->rubro_contribuyente??'',
                'razon_social_contribuyente'=> $element->razon_social_contribuyente??'',
                'nro_documento_contribuyente'=> $element->nro_documento_contribuyente??'',
                'direccion_contribuyente'=> $element->direccion_contribuyente??'',
                'ubigeo_contribuyente'=> $element->ubigeo_contribuyente??'',
                'fecha_emision_comprobante_contribuyente'=> $element->fecha_emision_comprobante_contribuyente??'',
                'fecha_pago'=> $element->fecha_pago??'',
                'tiempo_cancelacion'=> $element->tiempo_cancelacion??'',
                'moneda_doc_com'=> $element->moneda_doc_com??'',
                'total_a_pagar_soles'=> $element->total_a_pagar_soles??'',
                'total_a_pagar_dolares'=> $element->total_a_pagar_dolares??'',
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
