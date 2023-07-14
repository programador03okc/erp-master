<?php

namespace App\Exports;

use App\Http\Controllers\ComprasPendientesController;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class reporteRequerimientosAtendidosExcel implements FromView
{


    public function __construct(string $idEmpresa,string $idSede, string $fechaRegistroDesde, string $fechaRegistroHasta, string $reserva, string $orden)
{
        $this->idEmpresa = $idEmpresa;
        $this->idSede = $idSede;
        $this->fechaRegistroDesde = $fechaRegistroDesde;
        $this->fechaRegistroHasta = $fechaRegistroHasta;
        $this->reserva = $reserva;
        $this->orden = $orden;
    }

    public function view(): View{
        $idEmpresa= $this->idEmpresa;
        $idSede = $this->idSede;
        $fechaRegistroDesde = $this->fechaRegistroDesde;
        $fechaRegistroHasta = $this->fechaRegistroHasta;
        $reserva = $this->reserva;
        $orden = $this->orden;
        $requerimientosAtendidos = (new ComprasPendientesController)->obtenerRequerimientosAtendidos($idEmpresa,$idSede,$fechaRegistroDesde,$fechaRegistroHasta,$reserva,$orden)->orderBy('fecha_registro','desc')->get();
        $data=[];
        foreach($requerimientosAtendidos as $element){

            $data[]=[
                'empresa_sede'=> $element->empresa_sede,
                'codigo'=> str_replace("'", "", str_replace("", "", $element->codigo)),
                'fecha_registro'=> $element->fecha_registro,
                'fecha_entrega'=> $element->fecha_entrega,
                'concepto'=> str_replace("'", "", str_replace("", "", $element->concepto)),
                'tipo_req_desc'=> $element->tipo_req_desc,
                'division'=> $element->division,
                'nombre_usuario'=> ucwords(strtolower($element->nombre_usuario)),
                'nombre_solicitado_por'=> ucwords(strtolower($element->nombre_solicitado_por)),
                'estado_doc'=> $element->estado_doc    

            ];
        }
        return view('logistica.gestion_logistica.compras.pendientes.reportes.view_requerimientos_atendidos_export', [
            'requerimientos' => $data
        ]);
    }

}
