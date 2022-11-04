<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Tesoreria\RequerimientoPagoController;

class ListadoItemsRequerimientoPagoExport implements FromView
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

        $data=[];
            $requerimientosDetalle = (new RequerimientoPagoController)->obtenerItemsRequerimientoPagoElaborados($meOrAll, $idEmpresa, $idSede, $idGrupo, $idDivision, $fechaRegistroDesde, $fechaRegistroHasta, $idEstado);   

            foreach ($requerimientosDetalle as $key => $value) {

                $data[]=[
                    'prioridad'=>$value->prioridad,
                    'codigo'=> $value->codigo,
                    'partida'=> $value->partida,
                    'centro_costo'=> $value->centro_costo,
                    'codigo_oportunidad'=> str_replace("'", "", str_replace("", "" ,$value->codigo_oportunidad)),
                    'motivo'=> str_replace("'", "", str_replace("", "" ,$value->motivo)),
                    'concepto'=> str_replace("'", "", str_replace("", "" ,$value->concepto)),
                    'descripcion'=>  str_replace("'", "", str_replace("", "" ,$value->descripcion)),
                    'fecha_registro'=> $value->fecha_registro,
                    'tipo_requerimiento'=> $value->tipo_requerimiento,
                    'empresa_razon_social'=> $value->empresa_razon_social,
                    'sede'=> $value->sede,
                    'grupo'=> $value->grupo,
                    'division'=> $value->division,
                    'descripcion_proyecto'=> str_replace("'", "", str_replace("", "" ,$value->descripcion_proyecto)),
                    'simbolo_moneda'=> str_replace("'", "", str_replace("", "" ,$value->simbolo_moneda)),
                    'cantidad'=> $value->cantidad,
                    'precio_unitario'=> $value->precio_unitario,
                    'subtotal'=> $value->subtotal,
                    'estado_requerimiento'=> $value->estado_requerimiento,
                    'comentario'=> str_replace("'", "", str_replace("", "" ,$value->comentario))
                ];
            }


        return view('necesidades.reportes.listado_items_requerimiento_pago_export_excel', [
            'items'        =>  $data
        ]);
    }
}
