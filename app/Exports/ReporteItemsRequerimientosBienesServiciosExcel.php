<?php

namespace App\Exports;

use App\Http\Controllers\Logistica\RequerimientoController;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class reporteItemsRequerimientosBienesServiciosExcel implements FromView
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
        $requerimientos = (new RequerimientoController)->listaDetalleRequerimiento($meOrAll,$idEmpresa,$idSede,$idGrupo,$idDivision,$fechaRegistroDesde,$fechaRegistroHasta,$idEstado);
        $data=[];
        foreach($requerimientos as $element){

            $data[]=[
                'prioridad'=> $element->prioridad,
                'codigo'=> $element->codigo,
                'codigo_oportunidad'=> $element->codigo_oportunidad,
                'centro_costo'=> $element->centro_costo,
                'descripcion_centro_costo'=> $element->descripcion_centro_costo,
                'partida'=> $element->partida,
                'descripcion_partida'=> $element->descripcion_partida,
                'concepto'=> str_replace("'", "", str_replace("", "", $element->concepto)),
                'descripcion'=> $element->descripcion_producto != null? str_replace("'", "", str_replace("", "" ,$element->descripcion_producto)): str_replace("'", "", str_replace("", "" ,$element->descripcion_detalle_requerimiento)),
                'cantidad'=> $element->cantidad,
                'precio_unitario'=> $element->precio_unitario,
                'subtotal'=> $element->subtotal,
                'fecha_registro'=> $element->fecha_registro,
                'tipo_requerimiento'=> $element->tipo_requerimiento,
                'empresa_razon_social'=> $element->empresa_razon_social,
                'sede'=> $element->sede,
                'grupo'=> $element->grupo,
                'division'=> $element->division,
                'descripcion_proyecto'=> $element->descripcion_proyecto,
                'simbolo_moneda'=> $element->simbolo_moneda,
                'observacion'=> $element->observacion,
                'motivo'=> $element->motivo,
                'estado_requerimiento'=> $element->estado_requerimiento

            ];
        }
        return view('necesidades.reportes.listado_items_requerimientos_bienes_servicios_export', [
            'items' => $data
        ]);
    }

}
