<?php

namespace App\Exports;

use App\Http\Controllers\Almacen\Reporte\ListaSalidasController;
use App\Models\Administracion\Sede;
use App\Models\Almacen\MovimientoDetalle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteSalidasExcel implements FromView
{


    public function __construct(string $idEmpresa,string $idSede,string $almacenes,string $condiciones,string $fechaInicio,string $fechaFin, string $idCliente, string $idUsuario, string $moneda)
    {
        $this->idEmpresa = $idEmpresa;
        $this->idsede = $idSede;
        $this->almacenes = $almacenes;
        $this->condiciones = $condiciones;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->idUsuario = $idUsuario;
        $this->idCliente = $idCliente;
        $this->moneda = $moneda;
    }

    public function view(): View{
        $idEmpresa= $this->idEmpresa;
        $idSede = $this->idsede;
        $alm_array = explode(',', $this->almacenes);
        $con_array = explode(',', $this->condiciones);
        $fechaInicio = $this->fechaInicio;
        $fechaFin = $this->fechaFin;
        $idUsuario = $this->idUsuario;
        $idCliente = $this->idCliente;
        $moneda = $this->moneda;

        $salidas = (new ListaSalidasController)->obtenerDataSalidas($idEmpresa,$idSede,$alm_array,$con_array,$fechaInicio,$fechaFin,$idUsuario,$idCliente,$moneda)->orderBy('mov_alm.fecha_registro','desc')->get();
        $data=[];
        foreach($salidas as $element){
            $data[]=[
                'revisado'=>$element->revisado == 0?'No Reviado':($element->revisado==1?'Revisado':($element->revisado ==2?'Observado':'')),
                'fecha_emision'=>$element->fecha_emision??'',
                'codigo'=>$element->codigo??'',
                'guia_fecha_emision'=>$element->guia_venta->fecha_emision??'',
                'guia'=>$element->guia_venta?($element->guia_venta->serie.'-'.$element->guia_venta->numero):'',
                'fecha_documento_venta'=>$element->documento_venta->fecha_emision??'',
                'tipo_documento_venta'=>$element->documento_venta->tipo_documento->abreviatura??'',
                'documento_venta'=>$element->documento_venta?($element->documento_venta->serie.'-'.$element->documento_venta->numero):'',
                'cliente_nro_documento'=>$element->guia_venta->cliente->contribuyente->nro_documento??'',
                'cliente_razon_social'=>$element->guia_venta->cliente->contribuyente->razon_social??'',
                'moneda'=>$element->documento_venta->moneda->simbolo??'',
                'total'=>$element->documento_venta->total??'',
                'total_igv'=>$element->documento_venta->total_igv??'',
                'total_a_pagar'=>$element->documento_venta->total_a_pagar??'',
                'saldo'=>'',
                'condicion'=>$element->documento_venta->condicion_pago->descripcion??'',
                'dias'=>$element->documento_venta->credito_dias??'',
                'operacion'=>$element->operacion->descripcion??'',
                'fecha_vencimiento'=>$element->documento_venta->fecha_vcmto??'',
                'responsable'=>$element->usuario->nombre_corto??'',
                'tipo_cambio'=>$element->documento_venta->tipo_cambio??'',
                'almacen'=>$element->almacen->descripcion??'',
                'fecha_registro'=>$element->fecha_registro??''

            ];
        }


        return view('almacen.reportes.view_salidas_export', [
            'salidas' => $data
        ]);
    }

}
