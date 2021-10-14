<?php

namespace App\Exports;

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

		$ordenes = Orden::with([
			'sede'=> function($q){
				$q->where([['sis_sede.estado', '!=', 7]]);
			},
			'moneda',
			'proveedor.contribuyente'
		])

        ->when(($idEmpresa > 0), function ($query) use($idEmpresa) {
			$sedes= Sede::where('id_empresa',$idEmpresa)->get();
			$idSedeList=[];
			foreach($sedes as $sede){
				$idSedeList[]=$sede->id_sede;
			}
            return $query->whereIn('id_sede', $idSedeList);
        })
        ->when(($idSede > 0), function ($query) use($idSede) {
            return $query->where('id_sede',$idSede);
        })

        ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta == 'SIN_FILTRO')), function ($query) use($fechaRegistroDesde) {
            return $query->where('log_ord_compra.fecha' ,'>=',$fechaRegistroDesde); 
        })
        ->when((($fechaRegistroDesde == 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use($fechaRegistroHasta) {
            return $query->where('log_ord_compra.fecha' ,'<=',$fechaRegistroHasta); 
        })
        ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use($fechaRegistroDesde,$fechaRegistroHasta) {
            return $query->whereBetween('log_ord_compra.fecha' ,[$fechaRegistroDesde,$fechaRegistroHasta]); 
        })
		->where([['log_ord_compra.id_tp_documento', '=', 2],['log_ord_compra.estado', '!=', 7]])
        // ->limit(50)
        ->orderBy('fecha','desc')
        ->get();
        $data=[];
        foreach($ordenes as $element){

            $fechaOrden = Carbon::create($element['fecha']);
            if($element->cuadro_costo!=null){
                $fechaAprobacionCC= Carbon::create($element->cuadro_costo['fecha_estado']);
                $diasRestantes = $fechaAprobacionCC->diffInDays($fechaOrden);
                $condicion = intval($diasRestantes) <=1?'ATENDIDO A TIEMPO':'ATENDIDO FUERA DE TIEMPO';
            }else{
                $diasRestantes='';
                $condicion='';
            }

            $fechaLlegada= Carbon::create($element['fecha'])->addDays($element['plazo_entrega']);
            $diasEntrega = $fechaLlegada->diffInDays($fechaOrden);
            $condicion2 = intval($diasEntrega) <=2?'ATENDIDO A TIEMPO':(intval($diasEntrega)>=15?'IMPORTACIÓN':'ATENDIDO FUERA DE TIEMPO');

            $data[]=[
                'codigo_oportunidad'=> $element->cuadro_costo?$element->cuadro_costo['codigo_oportunidad']:'',
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
        return view('logistica.reportes.view_transito_ordenes_compra_export', [
            'transitoOrdenes' => $data
        ]);
    }

}
