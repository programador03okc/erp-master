<?php

namespace App\Exports;

use App\Http\Controllers\Logistica\RequerimientoController;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use Carbon\Carbon;

class reporteRequerimientosBienesServiciosExcel implements FromView,ShouldAutoSize
{


    public function __construct(string $meOrAll, string $idEmpresa,string $idSede,string $idGrupo,string $idDivision, string $fechaRegistroDesde, string $fechaRegistroHasta, string $idEstado)
    {
        $this->meOrAll = $meOrAll;
        $this->idEmpresa = $idEmpresa;
        $this->idSede = $idSede;
        $this->idGrupo = $idGrupo;
        $this->idDivision = $idDivision;
        $this->fechaRegistroDesde = $fechaRegistroDesde;
        $this->fechaRegistroHasta = $fechaRegistroHasta;
        $this->idEstado = $idEstado;
    }

    public function view(): View{
        $meOrAll= $this->meOrAll;
        $idEmpresa= $this->idEmpresa;
        $idSede = $this->idSede;
        $idGrupo = $this->idGrupo;
        $idDivision = $this->idDivision;
        $fechaRegistroDesde = $this->fechaRegistroDesde;
        $fechaRegistroHasta = $this->fechaRegistroHasta;
        $idEstado = $this->idEstado;
        $requerimientos = (new RequerimientoController)->obtenerRequerimientosElaborados($meOrAll,$idEmpresa,$idSede,$idGrupo,$idDivision,$fechaRegistroDesde,$fechaRegistroHasta,$idEstado)->orderBy('fecha_registro','desc')->get();
        $data=[];
        foreach($requerimientos as $element){

            $data[]=[
                'priori'=> $element->priori,
                'codigo'=> $element->codigo,
                'codigo_oportunidad'=> $element->codigo_oportunidad,
                'concepto'=> str_replace("'", "", str_replace("", "", $element->concepto)),
                'fecha_registro'=> $element->fecha_registro,
                'fecha_entrega'=> $element->fecha_entrega,
                'tipo_requerimiento'=> $element->tipo_requerimiento,
                'razon_social'=> $element->razon_social,
                'sede'=> $element->sede,
                'grupo'=> $element->grupo,
                'division'=> $element->division,
                'descripcion_proyecto'=> $element->descripcion_proyecto,
                'simbolo_moneda'=> $element->simbolo_moneda,
                'monto_total'=> number_format($element->monto_total,2),
                'observacion'=> $element->observacion,
                'nombre_usuario'=> ucfirst(trim($element->nombre_usuario)),
                'nombre_solicitado_por'=>ucfirst(trim(strtolower($element->nombre_solicitado_por))),
                'ultimo_aprobador'=>$element->ultimo_aprobador,
                'observacion'=> $element->observacion,
                'estado_doc'=> $element->nombre_estado

            ];
        }
        return view('necesidades.reportes.view_requerimientos_bienes_servicios_export', [
            'requerimientos' => $data
        ]);
    }

}
