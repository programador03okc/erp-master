<?php

namespace App\Exports;

use App\Http\Controllers\ReporteLogisticaController;
use App\Models\Administracion\Sede;
use App\Models\Logistica\Orden;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class ReporteOrdenesServicioExcel implements FromView
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
        $ordenes = (new ReporteLogisticaController)->obtenerDataOrdenesServicio($idEmpresa,$idSede,$fechaRegistroDesde,$fechaRegistroHasta)->orderBy('fecha','desc')->get();
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
            
            $codigoRequerimiento=[];
            foreach ($element->requerimientos as $value) {
                $codigoRequerimiento[]= $value->codigo;
            }

            $data[]=[
                'requerimientos'=> implode(",", $codigoRequerimiento),
                'codigo'=> $element->codigo,
                'codigo_softlink'=> $element->codigo_softlink,
                'sede'=> $element->sede->descripcion,
                'estado'=> $element->estado_orden,
                'condicion1'=> $condicion,
                'fecha'=> $element->fecha,
                'dias_entrega'=> $diasEntrega,
                'condicion2'=> $condicion2,
                'fecha_entrega'=> $fechaLlegada,
                'observacion'=> $element->observacion

            ];
        }
        return view('logistica.reportes.view_ordenes_servicio_export', [
            'ordenes' => $data
        ]);
    }

}
