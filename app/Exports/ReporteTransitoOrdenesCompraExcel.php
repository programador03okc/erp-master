<?php

namespace App\Exports;

use App\Http\Controllers\ReporteLogisticaController;
use App\Models\Administracion\Sede;
use App\Models\Logistica\Orden;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class ReporteTransitoOrdenesCompraExcel implements FromView
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

		
        $ordenes = (new ReporteLogisticaController)->obtenerDataTransitoOrdenesCompra($idEmpresa,$idSede,$fechaRegistroDesde,$fechaRegistroHasta)->orderBy('fecha','desc')->get();

        $data=[];
        foreach($ordenes as $element){
            $fechaOrden = Carbon::create($element['fecha']);
            if($element->cuadro_costo!=null){
                $fechaAprobacionCC= Carbon::create($element->cuadro_costo[0]['fecha_aprobacion']);
                $diasRestantes = $fechaAprobacionCC->diffInDays($fechaOrden,false);
                $condicion = intval($diasRestantes) <=1?'ATENDIDO A TIEMPO':'ATENDIDO FUERA DE TIEMPO';
            }else{
                $diasRestantes='';
                $condicion='';
            }

            $fechaLlegada= Carbon::create($element['fecha'])->addDays($element['plazo_entrega']);
            $diasEntrega = $fechaLlegada->diffInDays($fechaOrden,false);
            $condicion2 = intval($diasEntrega) <=2?'ATENDIDO A TIEMPO':(intval($diasEntrega)>=15?'IMPORTACIÓN':'ATENDIDO FUERA DE TIEMPO');
            $data[]=[
                'codigo_oportunidad'=> $element->cuadro_costo?$element->cuadro_costo[0]['codigo_oportunidad']:'',
                'razon_social_proveedor'=> $element->proveedor['contribuyente']['razon_social']??'',
                'codigo'=> $element->codigo,
                'fecha'=> $element->fecha,
                'sede'=> $element->sede->descripcion,
                'moneda'=> $element->moneda['simbolo'],
                'monto'=> number_format($element->monto,2),
                'estado'=> $element->estado_orden,
                'tiene_transformacion'=> $element->tiene_transformacion ==true?'SI':'NO',
                'cantidad_equipos'=> $element->cantidad_equipos

                
            ];
        }
        // dd($data);
        return view('logistica.reportes.view_transito_ordenes_compra_export', [
            'transitoOrdenes' => $data
        ]);
    }

}
