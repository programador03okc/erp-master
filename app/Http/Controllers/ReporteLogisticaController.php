<?php

namespace App\Http\Controllers;

use App\Exports\ReporteComprasLocalesExcel;
use App\Models\Administracion\Empresa;
use App\Models\Administracion\Sede;
use App\models\Configuracion\AccesosUsuarios;
use App\Models\Configuracion\Grupo;
use App\Models\Logistica\ComprasLocalesView;
use App\Models\Logistica\Orden;
use App\Models\Proyectos\Proyecto;
use App\Models\Tesoreria\Estado;
use App\Models\Tesoreria\RequerimientoPagoEstados;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

// use Maatwebsite\Excel\Facades\Excel;

class ReporteLogisticaController extends Controller{


    public function viewReporteOrdenesCompra(){
		$empresas = Empresa::mostrar();
        $grupos = Grupo::mostrar();
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
		return view('logistica/reportes/ordenes_compra',compact('empresas','grupos','array_accesos'));
	}
    public function viewReporteOrdenesServicio(){
		$empresas = Empresa::mostrar();
        $grupos = Grupo::mostrar();
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
		return view('logistica/reportes/ordenes_servicio',compact('empresas','grupos','array_accesos'));
	}

    public function viewReporteTransitoOrdenesCompra(){
		$empresas = Empresa::mostrar();
        $grupos = Grupo::mostrar();
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }

		return view('logistica/reportes/transito_ordenes_compra',compact('empresas','grupos','array_accesos'));
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
		->where([['log_ord_compra.id_tp_documento', '=', 2],['log_ord_compra.estado', '!=', 7]]);

		return $data;
	}

	public function obtenerDataOrdenesServicio($idEmpresa,$idSede,$fechaRegistroDesde,$fechaRegistroHasta){
		$data = Orden::with([
			'sede'=> function($q){
				$q->where([['sis_sede.estado', '!=', 7]]);
			},
			'estado'
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
		->where([['log_ord_compra.id_tp_documento', '=', 3],['log_ord_compra.estado', '!=', 7]]);

		return $data;
	}


	public function listaOrdenesCompra(Request $request){

		$idEmpresa = $request->idEmpresa;
        $idSede = $request->idSede;
		$fechaRegistroDesde = $request->fechaRegistroDesde;
        $fechaRegistroHasta = $request->fechaRegistroHasta;

		$data = $this->obtenerDataOrdenesCompra($idEmpresa,$idSede,$fechaRegistroDesde,$fechaRegistroHasta);


		return datatables($data)
		// ->filterColumn('codigo_requerimiento', function ($query, $keyword) {
		// 	$query->where('alm_req.codigo', $keyword);
		// })
		->rawColumns(['requerimientos','cuadro_costo'])->toJson();

	}
	public function listaOrdenesServicio(Request $request){

		$idEmpresa = $request->idEmpresa;
        $idSede = $request->idSede;
		$fechaRegistroDesde = $request->fechaRegistroDesde;
        $fechaRegistroHasta = $request->fechaRegistroHasta;

		$data = $this->obtenerDataOrdenesServicio($idEmpresa,$idSede,$fechaRegistroDesde,$fechaRegistroHasta);

		return datatables($data)
		->rawColumns(['requerimientos','cuadro_costo'])->toJson();

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

	public function viewReporteComprasLocales(){
		$empresas = Empresa::mostrar();
        $grupos = Grupo::mostrar();
        $proyectos = Proyecto::mostrar();
        $estadosPago = RequerimientoPagoEstados::mostrar();
        $fechaActual = new Carbon();
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
		return view('logistica/reportes/compras_locales', get_defined_vars());
	}

    public function obtenerFiltrosCompras(Request $request)
    {
        if ($request->chkFechaRegistro == 'on') {
            $request->session()->put('clFechaRegistroDesde', $request->fechaRegistroDesde);
            $request->session()->put('clFechaRegistroHasta', $request->fechaRegistroHasta);
        } else {
            $request->session()->forget('clFechaRegistroDesde');
            $request->session()->forget('clFechaRegistroHasta');
        }

        if ($request->chkFechaCancelacion == 'on') {
            $request->session()->put('clFechaCancelacionDesde', $request->fechaCancelacionDesde);
            $request->session()->put('clFechaCancelacionHasta', $request->fechaCancelacionHasta);
        } else {
            $request->session()->forget('clFechaCancelacionDesde');
            $request->session()->forget('clFechaCancelacionHasta');
        }

        if ($request->chkEmpresa == 'on') {
            $request->session()->put('clEmpresa', $request->empresa);
        } else {
            $request->session()->forget('clEmpresa');
        }

        if ($request->chkGrupo == 'on') {
            $request->session()->put('clGrupo', $request->grupo);
        } else {
            $request->session()->forget('clGrupo');
        }

        if ($request->chkProyecto == 'on') {
            $request->session()->put('clProyecto', $request->proyecto);
        } else {
            $request->session()->forget('clProyecto');
        }

        if ($request->chkEstadoPago == 'on') {
            $request->session()->put('clEstadoPago', $request->estadoPago);
        } else {
            $request->session()->forget('clEstadoPago');
        }

        if ($request->chkRazonSocialProveedor == 'on') {
            $request->session()->put('clProveedor', $request->razon_social_proveedor);
        } else {
            $request->session()->forget('clProveedor');
        }

        if ($request->chkCompraLocal == 'on') {
            $request->session()->put('clTipoReporte', $request->tipo_reporte);
        } else {
            $request->session()->forget('clTipoReporte');
        }
    }

	public function listarCompras(Request $request)
    {
        $this->obtenerFiltrosCompras($request);
        $data = $this->obtenerReporteCompras();
		return datatables($data)->toJson();

	}

    public function obtenerReporteCompras()
    {
        $data = ComprasLocalesView::orderBy('fecha_emision', 'desc');

        if (session()->has('clFechaRegistroDesde')) {
            $data = $data->whereBetween('fecha_emision_comprobante_contribuyente', [session('clFechaRegistroDesde'), session('clFechaRegistroHasta')]);
        }
        if (session()->has('clFechaCancelacionDesde')) {
            $data = $data->whereBetween('fecha_pago', [session('clFechaCancelacionDesde'), session('clFechaCancelacionHasta')]);
        }
        if (session()->has('clEmpresa')) {
            $data = $data->where('id_empresa', session()->get('clEmpresa'));
        } else {
            $data = $data->where('id_empresa', '>', 0);
        }
        if (session()->has('clGrupo')) {
            $data = $data->where('id_grupo', session()->get('clGrupo'));
        }
        if (session()->has('clProyecto')) {
            $data = $data->where('id_proyecto', session()->get('clProyecto'));
        }
        if (session()->has('clEstadoPago')) {
            $data = $data->where('id_requerimiento_pago_estado', session()->get('clEstadoPago'));
        }
        if (session()->has('clProveedor')) {
            $data = $data->where('razon_social_contribuyente', 'like', '%'.session()->get('clProveedor').'%');
        }
        if (session()->has('clTipoReporte')) {
            if (session()->get('clTipoReporte') == true) {
                $data = $data->where('compra_local', true);
            }
        }
        return $data->get();
    }

    public function reporteCompraLocalesExcel()
    {
        return Excel::download(new ReporteComprasLocalesExcel(), 'reporte_compra_locales.xlsx');
    }
}
