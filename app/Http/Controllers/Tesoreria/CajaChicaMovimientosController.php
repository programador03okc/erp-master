<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\HynoTechController;
use App\Models\Tesoreria\CajaChica;
use App\Models\Tesoreria\CajaChicaMovimiento;
use App\Models\Tesoreria\CajaChicaMovimientoVales;
use App\Models\Tesoreria\CajaChicaSaldos;
use App\Models\Tesoreria\DocumentosOperacion;
use App\Models\Tesoreria\Empresa;
use App\Models\Tesoreria\Moneda;
use App\Models\Tesoreria\Proveedor;
use App\Models\Tesoreria\TipoContribuyente;
use App\Models\Tesoreria\Usuario;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CajaChicaMovimientosController extends Controller
{

	public function __construct() {
		$this->middleware('roles:1,2,3,7');
	}

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	//dd(Auth::user()->toArray());

        //
		$empresas = Empresa::with('contribuyente')->get();
		$proveedores = Proveedor::with('contribuyente')->get();
		$monedas = Moneda::orderBy('id_moneda')->get();
		$docOperacion = DocumentosOperacion::all();
		$usuarios = Usuario::with('trabajador.postulante.persona')->get();

		$tipoContribuyente = TipoContribuyente::all();

		//dd($usuarios->toArray());

		//dd($empresas->toArray());

		return view('tesoreria.cajachica_movimientos.index')->with([
			'empresas'=>$empresas,
			'proveedores' => $proveedores,
			'monedas' => $monedas,
			'doc_operacion' => $docOperacion,
			'usuarios' => $usuarios,
			'tipo_contribuyente' => $tipoContribuyente
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
    	//dd($request->toArray());
    	$valeAct = false;

		DB::beginTransaction();
		$cajaChicaMovimiento = new CajaChicaMovimiento();
		$cajaChicaMovimiento->cajachica_id = $request->get('reg_cajachica_id');
		$cajaChicaMovimiento->fecha = now();
		$cajaChicaMovimiento->tipo_movimiento = $request->get('reg_tipo');
		$cajaChicaMovimiento->doc_operacion_id = $request->get('reg_orig_operacion');
		//$cajaChicaMovimiento->doc_pago = $request->get('reg_num_docu');

		$arrayDocsSustento = [];
		$dataProv = $request->get('sust_prov');
		$dataMonto = $request->get('sust_monto');
		$dataDocs = $request->get('sust_doc');

		if (is_array($dataDocs)){
			foreach ($dataDocs as $idx => $sust){
				$arrayDocsSustento[] = [
					'doc_tipo' => null,
					'num_docu' => $sust,
					'id_proveedor' => $dataProv[$idx],
					'monto' => $dataMonto[$idx]
				];
			}

			$cajaChicaMovimiento->data_pago = json_encode($arrayDocsSustento);
		}


		//$cajaChicaMovimiento->proveedor_id = $request->has('proveedor_id')?$request->get('proveedor_id'):null;
		$cajaChicaMovimiento->moneda_id = $request->get('reg_moneda');
		$cajaChicaMovimiento->tipo_cambio = $request->has('reg_t_cambio')?$request->get('reg_t_cambio'):0;
		$cajaChicaMovimiento->importe = $request->get('reg_importe');
		$cajaChicaMovimiento->observaciones = $request->get('reg_observacion');

		if ($cajaChicaMovimiento->save()){
			$saldoCajaChica = new CajaChicaSaldos();
			$saldoCajaChica->cajachica_movimiento_id = $cajaChicaMovimiento->id;
			$saldoCajaChica->fecha = $cajaChicaMovimiento->fecha;

			$cajaChicaID = $cajaChicaMovimiento->cajachica_id;
			$saldoAnterior = CajaChicaSaldos::with('cajachica_movimiento.cajachica')->whereHas('cajachica_movimiento.cajachica', function ($query) use ($cajaChicaID){
				$query->where('id', $cajaChicaID);
			})->orderByDesc('fecha')->firstOrFail();

			$saldoCajaChica->inicial = $saldoAnterior->saldo;

			//dd($cajaChicaMovimiento);

			switch ($cajaChicaMovimiento->tipo_movimiento){
				case 'I':
					$saldoCajaChica->ingreso = $cajaChicaMovimiento->importe;
					$saldoCajaChica->egreso = 0;
					break;
				case 'E':
					$saldoCajaChica->ingreso = 0;
					$saldoCajaChica->egreso = $cajaChicaMovimiento->importe;

					$reg_vale = $request->get('reg_vale');
					$reg_receptor_id = $request->get('reg_receptor_id');

					if(($reg_vale === '1') && ($reg_receptor_id !== '')){
						$prefijoNum = sprintf('%03d', $cajaChicaMovimiento->cajachica->empresa_id);
						$numeracionVale = $prefijoNum . sprintf("%08d", HynoTechController::obtenerNumeracion(CajaChicaMovimientoVales::class, $prefijoNum));
						$vale = new CajaChicaMovimientoVales();
						$vale->codigo = $numeracionVale;
						$vale->cajachica_movimiento_id = $cajaChicaMovimiento->id;
						$vale->emisor_id = Auth::user()->id_usuario;
						$vale->receptor_id = $reg_receptor_id;

						if ($vale->save()){
							$cajaChicaMovimiento->vale_numero = $vale->codigo;
							if($cajaChicaMovimiento->save()){
								$valeAct = true;
							}

						}
					}

					break;
			}
			$saldoCajaChica->saldo = $saldoCajaChica->inicial + $saldoCajaChica->ingreso - $saldoCajaChica->egreso;

			$cajaChica = CajaChica::findOrFail($cajaChicaID)->first();
			$cajaChica->saldo = $saldoCajaChica->saldo;

			if ($saldoCajaChica->save() && $cajaChica->save()){
				if($valeAct){
					$responseData = [
						'error' => false,
						'msg' => '',
						'data' => [
							'id' => $cajaChicaMovimiento->id,
							'vale_id' => $vale->id
						]
					];
					DB::commit();
				}
				else{
					$responseData = [
						'error' => false,
						'msg' => '',
						'data' => [
							'id' => $cajaChicaMovimiento->id
						]
					];
					DB::commit();
				}


			}
			else{
				$responseData = [
					'error' => true,
					'msg' => 'Error de Sistema'
				];
				DB::rollBack();
			}


		}
		else{
			$responseData = [
				'error' => true,
				'msg' => 'Error de Sistema'
			];
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
		//dd($request->toArray());

		$valeAct = false;

		DB::beginTransaction();
		$cajaChicaMovimiento =  CajaChicaMovimiento::findOrFail($id);
		$cajaChicaMovimiento->tipo_movimiento = $request->get('reg_tipo');
		$cajaChicaMovimiento->doc_operacion_id = $request->get('reg_orig_operacion');
		//$cajaChicaMovimiento->doc_pago = $request->get('reg_num_docu');

		$arrayDocsSustento = [];
		$dataProv = $request->get('sust_prov');
		$dataMonto = $request->get('sust_monto');
		$dataDocs = $request->get('sust_doc');

		if (is_array($dataDocs)) {
			foreach ($dataDocs as $idx => $sust) {
				$arrayDocsSustento[] = ['doc_tipo' => null, 'num_docu' => $sust, 'id_proveedor' => $dataProv[$idx], 'monto' => $dataMonto[$idx]];
			}
		}

		$cajaChicaMovimiento->data_pago = json_encode($arrayDocsSustento);

		$cajaChicaMovimiento->proveedor_id = $request->has('proveedor_id')?$request->get('proveedor_id'):null;
		$cajaChicaMovimiento->moneda_id = $request->get('reg_moneda');
		$cajaChicaMovimiento->importe = $request->get('reg_importe');
		$cajaChicaMovimiento->observaciones = $request->get('reg_observacion');

		if ($cajaChicaMovimiento->save()){
			$saldoCajaChica = CajaChicaSaldos::findOrFail($cajaChicaMovimiento->saldo->id);
			$saldoCajaChica->fecha = today();

			$cajaChicaID = $cajaChicaMovimiento->cajachica_id;
			$saldoAnterior = CajaChicaSaldos::with('cajachica_movimiento.cajachica')
				->where('id', '!=', $saldoCajaChica->id)
				->whereHas('cajachica_movimiento.cajachica', function ($query) use ($cajaChicaID){
				$query->where('id', $cajaChicaID);
			})->orderByDesc('fecha')->firstOrFail();

			$saldoCajaChica->inicial = $saldoAnterior->saldo;

			//dd($cajaChicaMovimiento);

			switch ($cajaChicaMovimiento->tipo_movimiento){
				case 'I':
					$saldoCajaChica->ingreso = $cajaChicaMovimiento->importe;
					$saldoCajaChica->egreso = 0;
					break;
				case 'E':
					$saldoCajaChica->ingreso = 0;
					$saldoCajaChica->egreso = $cajaChicaMovimiento->importe;

					$reg_vale = $request->get('reg_vale');
					$reg_receptor_id = $request->get('reg_receptor_id');



					if(($reg_vale === '1') && ($reg_receptor_id != '')){
						//dd($reg_receptor_id);
						$prefijoNum = sprintf('%03d', $cajaChicaMovimiento->cajachica->empresa_id);
						$numeracionVale = $prefijoNum . sprintf("%08d", HynoTechController::obtenerNumeracion(CajaChicaMovimientoVales::class, $prefijoNum));
						$vale = new CajaChicaMovimientoVales();
						$vale->codigo = $numeracionVale;
						$vale->cajachica_movimiento_id = $cajaChicaMovimiento->id;
						$vale->emisor_id = Auth::user()->id_usuario;
						$vale->receptor_id = $reg_receptor_id;

						if ($vale->save()){
							$cajaChicaMovimiento->vale_numero = $vale->codigo;
							if($cajaChicaMovimiento->save()){
								$valeAct = true;
							}

						}
					}
					break;
			}
			$saldoCajaChica->saldo = $saldoCajaChica->inicial + $saldoCajaChica->ingreso - $saldoCajaChica->egreso;

			$cajaChica = CajaChica::findOrFail($cajaChicaID)->first();
			$cajaChica->saldo = $saldoCajaChica->saldo;

			if ($saldoCajaChica->save() && $cajaChica->save()){
				if($valeAct){
					$responseData = [
						'error' => false,
						'msg' => '',
						'data' => [
							'id' => $cajaChicaMovimiento->id,
							'vale_id' => $vale->id
						]
					];
					DB::commit();
				}
				else{
					$responseData = [
						'error' => false,
						'msg' => '',
						'data' => [
							'id' => $cajaChicaMovimiento->id
						]
					];
					DB::commit();
				}


			}
			else{
				$responseData = [
					'error' => true,
					'msg' => 'Error de Sistema'
				];
				DB::rollBack();
			}


		}
		else{
			$responseData = [
				'error' => true,
				'msg' => 'Error de Sistema'
			];
			DB::rollBack();
		}
		return response()->json($responseData);
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
}
