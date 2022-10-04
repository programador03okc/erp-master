<?php

namespace App\Exports;

use App\Http\Controllers\ReporteLogisticaController;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;
use phpDocumentor\Reflection\Types\Integer;

class ReporteComprasLocalesExcel implements FromView
{


    public function __construct(string $idEmpresa,string $idSede, string $fechaRegistroDesde, string $fechaRegistroHasta, string $fechaRegistroDesdeCancelacion, string $fechaRegistroHastaCancelacion, string $razonSocialProveedor, string $idGrupo, string $idProyecto, string $observacionOrden, string $estadoPago)
    {
        $this->idEmpresa = $idEmpresa;
        $this->idsede = $idSede;
        $this->fechaRegistroDesde = $fechaRegistroDesde;
        $this->fechaRegistroHasta = $fechaRegistroHasta;
        $this->fechaRegistroDesdeCancelacion = $fechaRegistroDesdeCancelacion;
        $this->fechaRegistroHastaCancelacion = $fechaRegistroHastaCancelacion;
        $this->razonSocialProveedor = $razonSocialProveedor;
        $this->idGrupo = $idGrupo;
        $this->idProyecto = $idProyecto;
        $this->observacionOrden = $observacionOrden;
        $this->estadoPago = $estadoPago;
    }

    public function view(): View{
        $idEmpresa= $this->idEmpresa;
        $idSede = $this->idsede;
        $fechaRegistroDesde = $this->fechaRegistroDesde;
        $fechaRegistroHasta = $this->fechaRegistroHasta;

        $fechaRegistroDesdeCancelacion = $this->fechaRegistroDesdeCancelacion;
        $fechaRegistroHastaCancelacion = $this->fechaRegistroHastaCancelacion;
        $razonSocialProveedor = $this->razonSocialProveedor;
        $idGrupo =  $this->idGrupo;
        $idProyecto = $this->idProyecto;
        $observacionOrden = $this->observacionOrden;
        $estadoPago = $this->estadoPago;

        $comLocales = (new ReporteLogisticaController)->obtenerDataComprasLocales($idEmpresa,$idSede,$fechaRegistroDesde,$fechaRegistroHasta,$fechaRegistroDesdeCancelacion,$fechaRegistroHastaCancelacion,$razonSocialProveedor,$idGrupo,$idProyecto,$observacionOrden,$estadoPago)->orderBy('fecha_emision','desc')->get();

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
                'cantidad'=> $element->cantidad??'',
                'moneda_orden'=> $element->moneda_orden??'',
                'total_precio_soles_item'=> $element->total_precio_soles_item??'',
                'total_precio_dolares_item'=> $element->total_precio_dolares_item??'',
                'total_a_pagar_soles'=> $element->total_a_pagar_soles??'',
                'total_a_pagar_dolares'=> $element->total_a_pagar_dolares??'',
                'tipo_doc_com'=> $element->tipo_doc_com??'',
                'nro_doc_com'=> $element->nro_doc_com??'',
                'descripcion_sede_empresa'=> $element->descripcion_sede_empresa??'',
                'descripcion_grupo'=> $element->descripcion_grupo??'',
                'descripcion_proyecto'=> $element->descripcion_proyecto??'',
                'descripcion_estado_pago'=> $element->descripcion_estado_pago??''
            ];
        }
        return view('logistica.reportes.view_compras_locales_export', [
            'comprasLocales' => $data
        ]);
    }

}
