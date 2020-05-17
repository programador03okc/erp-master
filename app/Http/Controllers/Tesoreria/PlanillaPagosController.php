<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\TesoreriaController;
use App\Models\Logistica\Empresa;
use App\Models\Tesoreria\ContribuyenteCuenta;
use App\Models\Tesoreria\Moneda;
use App\Models\Tesoreria\Persona;
use App\Models\Tesoreria\PlanillaPago;
use App\Models\Tesoreria\PlanillaPagosSeguimiento;
use App\Models\Tesoreria\PlanillaPagosSolicitudes;
use App\Models\Tesoreria\Prioridad;
use App\Models\Tesoreria\Proveedor;
use App\Models\Tesoreria\Solicitud;
use App\Models\Tesoreria\TipoCuenta;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlanillaPagosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    	//dd($request->toArray());
    	$dataName = explode('.', request()->route()->getName());


    	//dd([strpos( '.ordinario', request()->route()->getName() ), '.ordinario', request()->route()->getName()]);
    	//dd(request()->route()->uri());
    	//dd(request()->route()->getName());

    	$planillas = PlanillaPago::with([
    		'historial',
			'solicitudes',
			'cta_origen',
			'tipo_planilla',
			'usuario',
			'estado',
			'moneda',
		]);


		if (in_array('ordinario', $dataName)){
    		$planillas = $planillas->where('planillapago_tipo_id', 1);
			$tipo_planilla = 'Ordinario';
		}
    	else{

			//dd('llegasssdddddd');
			$planillas = $planillas->whereNotIn('planillapago_tipo_id', [1]);
			$tipo_planilla = 'Extraordinario';
		}

    	$planillas = $planillas->get();



		$idSolicitud = $request->get('idSolicitud');

		$listaIds = explode(',', $idSolicitud);


		$empresas = Empresa::where('id_empresa', '>', 0)->pluck('id_contribuyente')->toArray();

		$dataCuentas = ContribuyenteCuenta::with([
			'contribuyente',
			'banco',
			'tipo_cuenta'
		])->whereIn('id_contribuyente', $empresas)->get();

		$prioridades = Prioridad::where('estado',1)->get();

		$tipoCuentas = TipoCuenta::all();

		$dataSolicitudes = [];
		//dd(($listaIds));
		if ($idSolicitud){
			$dataSolicitudes = Solicitud::with('detalles', 'moneda', 'subtipo.tipo', 'area.grupo.sede.empresa.contribuyente', 'usuario.trabajador.postulante.persona', 'estado', 'prioridad')->findOrFail($listaIds);
		}

		$personas = Persona::all();
		$proveedores = Proveedor::with('contribuyente')->get();
		$monedas = Moneda::orderBy('id_moneda')->get();

		$valCampoProveedorPersona = [];

		foreach ($personas as $persona){
			$valCampoProveedorPersona['persona'][] = [
				'id' => 'persona_' . $persona->id_persona,
				'txt' => $persona->nombre_completo . ' (' . $persona->nro_documento .')'
			];
		}

		foreach ($proveedores as $proveedor){
			$valCampoProveedorPersona['proveedor'][] = [
				'id' => 'proveedor' . $proveedor->id_proveedor,
				'txt' => $proveedor->contribuyente->razon_social . ' (' .$proveedor->contribuyente->nro_documento. ')'
			];
		}


		$adm = false;//$request->get('admin')?true:false;


		if (Auth::user()->hasAnyRole(TesoreriaController::$gerentes)) {
			$adm = true;
		}

		// dd($dataSolicitudes->toArray());

		return view('tesoreria.planillapagos.index')->with([
			'tipo_planilla' => $tipo_planilla,

			'planillas' => $planillas,

			'dataSolicitudes' => $dataSolicitudes,
			'monedas' => $monedas,
			'persona_proveedor' => $valCampoProveedorPersona,
			'dataCuentas' => $dataCuentas,
			'tipoCuentas' => $tipoCuentas,
			'prioridades' => $prioridades,

			'adm' => $adm,
		]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	/*
    	 *         'fecha',
        'detalle',
        'cuenta_origen_id',
        'cuenta_destino',
		'cuenta_destino_tipo',
        'importe',
        'observaciones',
        'solicitud_id',
        'proveedor_id',
        'persona_id',
        'moneda_id',
        'estado_id',
        'usuario_id',
    	 */
        //
		 //dd($request->toArray());

		$idSolicitud = $request->get('reg_solicitud_id');
		$reg_tipo_cta_destino = $request->get('reg_tipo_cta_destino');
		$reg_cta_destino = $request->get('reg_cta_destino');
		$reg_pers_prov = $request->get('reg_persona_proveedor');


		$solicitudes = Solicitud::with(
			'detalles',
			'moneda',
			//'subtipo.tipo',
			//'area.grupo.sede.empresa.contribuyente',
			//'usuario.trabajador.postulante.persona',
			'estado',
			'prioridad')->findOrFail($idSolicitud);

		// $empresas = Empresa::where('id_empresa', '>', 0)->pluck('id_contribuyente')->toArray();

		$codigosSolicitud = implode(', ', $solicitudes->pluck('codigo')->toArray() );
		$importeTotal = $solicitudes->sum('importe');


		//echo $htmlDetalle;
		//dd($solicitud->toArray());
		// $cuentaOrigen = ContribuyenteCuenta::

		DB::beginTransaction();
		$planilla = new PlanillaPago();
		$planilla->fecha = now();
		$planilla->detalle = $codigosSolicitud;
		$planilla->cuenta_origen_id = $request->get('reg_cta_origen');
		$planilla->importe = $importeTotal;
		$planilla->observaciones = $request->get('reg_observaciones');
		$planilla->moneda_id = $request->get('reg_moneda');
		$planilla->estado_id = 1;
		$planilla->usuario_id = Auth::user()->id_usuario;
		if(in_array($request->get('reg_prioridad'), [2,3])){
			$planilla->planillapago_tipo_id = 2;
		}
		else{
			$planilla->planillapago_tipo_id = 1;
		}


		if ($planilla->save()) {

			$exito = true;


			foreach ($idSolicitud as $idx => $sol){
				$tmp = new PlanillaPagosSolicitudes();
				$tmp->planillapagos_id = $planilla->id;
				$tmp->solicitud_id = $sol;
				$tmp->cuenta_destino = $reg_cta_destino[$idx];
				$tmp->cuenta_destino_tipo = $reg_tipo_cta_destino[$idx];

				$prov_per = explode('_',$reg_pers_prov[$idx]);
				switch ($prov_per[0]){
					case 'persona':
						$tmp->persona_id = $prov_per[1];
						break;
					case 'proveedor':
						$tmp->proveedor_id = $prov_per[1];
						break;
				}



				$solicitud_tmp = Solicitud::findOrFail($sol);



				$segSolicitud = app(SolicitudController::class)->cambiarEstado($solicitud_tmp, 8, 'Generado Planilla de Pago');


				if (!$tmp->save() || !$segSolicitud){
					$exito = false;
					//dd('salio');

					break;
				}

			}


			//$seguimiento = $this->cambiarEstado($planilla, $planilla->estado_id, 'Generado por el usuario');



			if (/*$seguimiento &&*/ $segSolicitud && $exito) {
				$responseData = ['error' => false, 'msg' => '', 'data' => ['id' => $planilla->id]];
				DB::commit();
			} else {
				$responseData = ['error' => true, 'msg' => 'Error de Sistema'];
				DB::rollBack();
			}


		} else {
			$responseData = ['error' => true, 'msg' => 'Datos invalidos'];
			DB::rollBack();
		}


		return response()->json($responseData);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


	public function cambiarEstado(PlanillaPago $planillaPago, $estado_id, $observacion='') {
		$planillaPago->estado_id = $estado_id;
		$exito = false;
		DB::beginTransaction();
		if ($planillaPago->save()) {

			$seguimiento = new PlanillaPagosSeguimiento();
			$seguimiento->observacion = $observacion;
			$seguimiento->planillapagos_id = $planillaPago->id;
			$seguimiento->estado_id = $planillaPago->estado_id;
			$seguimiento->usuario_id = Auth::user()->id_usuario;
			$seguimiento->fecha = now();

			if ($seguimiento->save()) {
				$exito = true;
				DB::commit();
			} else {
				DB::rollBack();
			}
		} else {
			DB::rollBack();
		}
		return $exito;
	}

	public function cambiarEstadoAjax(Request $request) {

    	$idxs = $request->get('idxs');

    	//dd($request->toArray());
		$estadosFinal = [];
		foreach ($idxs as $indice => $idx) {
			$planilla = PlanillaPago::findorFail($idx['id']);

			$error = $this->cambiarEstado($planilla, $idx['estado'], $idx['observacion']);
			$estadosFinal[$indice] = $error;
    	}

		if (in_array(false, $estadosFinal)){
			$responseData = ['error' => true, 'msg' => 'Algunos datos no se guardaron correctamente'];
		}
		else{
			$responseData = ['error' => false, 'msg' => ''];
		}

		return response()->json($responseData);

	}
}
