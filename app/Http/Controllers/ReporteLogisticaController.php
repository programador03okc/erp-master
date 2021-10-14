<?php

namespace App\Http\Controllers;

use App\Models\Administracion\Empresa;
use App\Models\Administracion\Sede;
use App\Models\Configuracion\Grupo;
use App\Models\Logistica\Orden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
// use Maatwebsite\Excel\Facades\Excel;

class ReporteLogisticaController extends Controller{
	

    public function viewReporteOrdenesCompra(){
		$empresas = Empresa::mostrar();
        $grupos = Grupo::mostrar();

		return view('logistica/reportes/ordenes_compra',compact('empresas','grupos'));
	}
	
    public function viewReporteTransitoOrdenesCompra(){
		$empresas = Empresa::mostrar();
        $grupos = Grupo::mostrar();
		return view('logistica/reportes/transito_ordenes_compra',compact('empresas','grupos'));
	}
	
	public function listaOrdenesCompra(Request $request){
		
		$idEmpresa = $request->idEmpresa;
        $idSede = $request->idSede;
		$fechaRegistroDesde = $request->fechaRegistroDesde;
        $fechaRegistroHasta = $request->fechaRegistroHasta;

		$data = Orden::with([
			'sede'=> function($q){
				$q->where([['sis_sede.estado', '!=', 7]]);
			},
			'estado'
		])
		// ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')

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
		->where([['log_ord_compra.id_tp_documento', '=', 2],['log_ord_compra.estado', '!=', 7]]);
		
 
		return datatables($data)->rawColumns(['requerimientos','cuadro_costo'])->toJson();

	}
	public function listaTransitoOrdenesCompra(Request $request){
		
		$idEmpresa = $request->idEmpresa;
        $idSede = $request->idSede;
		$fechaRegistroDesde = $request->fechaRegistroDesde;
        $fechaRegistroHasta = $request->fechaRegistroHasta;

		$data = Orden::with([
			'sede'=> function($q){
				$q->where([['sis_sede.estado', '!=', 7]]);
			},
			'moneda',
			'proveedor.contribuyente',
			'estado'
		])
		 
		// ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')

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
		->where([['log_ord_compra.id_tp_documento', '=', 2],['log_ord_compra.estado', '!=', 7]]);
		
 
		return datatables($data)->rawColumns(['monto','requerimientos','cuadro_costo','tiene_transformacion','cantidad_equipos'])->toJson();

	}	
}