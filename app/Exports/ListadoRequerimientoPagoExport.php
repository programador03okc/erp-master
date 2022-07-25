<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Tesoreria\RequerimientoPagoController;

class ListadoRequerimientoPagoExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     //
    // }
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
        $requerimientos = (new RequerimientoPagoController)->obtenerRequerimientosElaborados($meOrAll,$idEmpresa,$idSede,$idGrupo,$idDivision,$fechaRegistroDesde,$fechaRegistroHasta,$idEstado)->orderBy('fecha_registro','desc')->get();

        $data=[];
        foreach($requerimientos as $element){

            $data[]=[
                'priori'=> $element->priori,
                'codigo'=> $element->codigo,
                'concepto'=> $element->concepto,
                'fecha_registro'=> $element->fecha_registro,
                // 'fecha_entrega'=> $element->fecha_entrega,
                'tipo_requerimiento'=> $element->descripcion_requerimiento_pago_tipo,
                'razon_social'=> $element->descripcion_empresa_sede,
                'grupo'=> $element->grupo,
                'division'=> $element->division,
                'descripcion_proyecto'=> $element->descripcion_proyecto,
                'simbolo_moneda'=> $element->simbolo_moneda,
                'monto_total'=> number_format($element->monto_total,2),
                'observacion'=> $element->observacion,
                'nombre_usuario'=> $element->usuario_nombre_corto,
                'observacion'=> $element->observacion,
                'estado_doc'=> $element->nombre_estado

            ];
        }
        return view('necesidades.reportes.listado_requerimiento_pago_export_excel', [
            'requerimientos' => $data
        ]);
    }
}