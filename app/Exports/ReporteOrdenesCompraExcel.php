<?php

namespace App\Exports;

use App\Http\Controllers\ReporteLogisticaController;
use App\Models\Administracion\Sede;
use App\Models\Logistica\Orden;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class ReporteOrdenesCompraExcel implements FromView
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
        $ordenes = (new ReporteLogisticaController)->obtenerDataOrdenesCompra($idEmpresa,$idSede,$fechaRegistroDesde,$fechaRegistroHasta)->orderBy('fecha','desc')->get();
        $data=[];
        foreach($ordenes as $element){
            $fechaOrden = Carbon::create($element['fecha']);
            if($element->cuadro_costo!=null){
                $fechaAprobacionCC= Carbon::create(($element->cuadro_costo)[0]['fecha_aprobacion']);
                $diasRestantes = $fechaOrden->diffInDays($fechaAprobacionCC,false);
                $condicion = intval($diasRestantes) <=1?'ATENDIDO A TIEMPO':'ATENDIDO FUERA DE TIEMPO';
            }else{
                $diasRestantes='';
                $condicion='';
            }

            $fechaLlegada= Carbon::create($element['fecha'])->addDays($element['plazo_entrega']);
            $diasEntrega = $fechaOrden->diffInDays($fechaLlegada,false);
            $condicion2 = intval($diasEntrega) <=2?'ATENDIDO A TIEMPO':(intval($diasEntrega)>=15?'IMPORTACIÓN':'ATENDIDO FUERA DE TIEMPO');

            $data[]=[
                'codigo_oportunidad'=> $element->cuadro_costo?($element->cuadro_costo)[0]['codigo_oportunidad']:'',
                'codigo'=> $element->codigo,
                'sede'=> $element->sede->descripcion,
                'estado'=> $element->estado_orden,
                'cuadro_costo_fecha_limite'=>  $element->cuadro_costo?(($element->cuadro_costo)[0]['fecha_limite']??''):'',
                'cuadro_costo_estado_aprobacion_cuadro'=> $element->cuadro_costo?(($element->cuadro_costo)[0]['estado_aprobacion_cuadro']??''):'',
                'cuadro_costo_estado_fecha_estado'=> $element->cuadro_costo?(($element->cuadro_costo)[0]['fecha_aprobacion']??''):'',
                'dias_restantes_atencion_cc'=> $diasRestantes,
                'condicion1'=> $condicion,
                'fecha'=> $element->fecha,
                'dias_entrega'=> $diasEntrega,
                'condicion2'=> $condicion2,
                'fecha_entrega'=> $fechaLlegada

            ];
        }
        return view('logistica.reportes.view_ordenes_compra_export', [
            'ordenes' => $data
        ]);
    }

}
