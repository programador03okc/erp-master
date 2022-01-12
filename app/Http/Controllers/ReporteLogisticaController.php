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
	

	public function obtenerDataOrdenesCompra($idEmpresa,$idSede,$fechaRegistroDesde,$fechaRegistroHasta){
		$data = Orden::with([
			'sede'=> function($q){
				$q->where([['sis_sede.estado', '!=', 7]]);
			},
			'estado'
		])
		->select('log_ord_compra.*',  DB::raw("(SELECT  array_to_json(array_agg(json_build_object( 
            'codigo_oportunidad',cc_view.codigo_oportunidad ,
			'fecha_creacion',cc_view.fecha_creacion,
			'fecha_limite',cc_view.fecha_limite,
			'estado_aprobacion_cuadro',oc_propias_view.estado_aprobacion_cuadro,
			'fecha_estado',oc_propias_view.fecha_estado

      		))) FROM logistica.log_det_ord_compra
			INNER JOIN almacen.alm_det_req ON log_det_ord_compra.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento
			INNER JOIN almacen.alm_req ON alm_req.id_requerimiento = alm_det_req.id_requerimiento
			INNER JOIN mgcp_cuadro_costos.cc_view ON alm_req.id_cc = cc_view.id
			INNER JOIN mgcp_ordenes_compra.oc_propias_view ON oc_propias_view.id_oportunidad = cc_view.id_oportunidad
			WHERE log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra )  as cuadro_costo"))
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
		->where([['log_ord_compra.id_tp_documento', '=', 2],['log_ord_compra.estado', '!=', 7]])
		->whereDate('log_ord_compra.fecha','=',date('Y-m-d'));

		return $data;
	}

 
	public function listaOrdenesCompra(Request $request){
		
		$idEmpresa = $request->idEmpresa;
        $idSede = $request->idSede;
		$fechaRegistroDesde = $request->fechaRegistroDesde;
        $fechaRegistroHasta = $request->fechaRegistroHasta;

		$data = $this->obtenerDataOrdenesCompra($idEmpresa,$idSede,$fechaRegistroDesde,$fechaRegistroHasta);
		
		
		return datatables($data)->rawColumns(['requerimientos','cuadro_costo'])->toJson();

	}

	public function obtenerDataTransitoOrdenesCompra($idEmpresa,$idSede,$fechaRegistroDesde,$fechaRegistroHasta){
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
		
		return $data;
	}
	
	public function listaTransitoOrdenesCompra(Request $request){
		
		$idEmpresa = $request->idEmpresa;
        $idSede = $request->idSede;
		$fechaRegistroDesde = $request->fechaRegistroDesde;
        $fechaRegistroHasta = $request->fechaRegistroHasta;
		$data = $this->obtenerDataTransitoOrdenesCompra($idEmpresa,$idSede,$fechaRegistroDesde,$fechaRegistroHasta);


 
		return datatables($data)->rawColumns(['monto','requerimientos','cuadro_costo','tiene_transformacion','cantidad_equipos'])->toJson();

	}	
}