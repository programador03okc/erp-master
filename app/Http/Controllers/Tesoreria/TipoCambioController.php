<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\Controller;
use App\Models\Tesoreria\TipoCambio;
use Carbon\Carbon;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Validator;

class TipoCambioController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		//
		$req = $request->all();
		$validar = Validator::make($req, [
			'id' => ['required', function ($attribute, $value, $fail) {
			$idTip = TipoCambio::find($value);

			if(!$idTip){
				$fail($attribute . ' es invalido ');
			}
			}],//'required|exists:cont_tp_cambio,id_tp_cambio',
			'fecha' => ['required', 'date_format:d/m/Y', function ($attribute, $value, $fail) {
			$f = Carbon::createFromFormat('d/m/Y', $value);

			//dd($f);
			$dat = TipoCambio::where('fecha', $f)->first();
			if (!$dat){
				$fail($attribute . ' es invalido ');
			}/*
			dd($dat->toArray());
			if (!(is_null($dat)) && ($dat->count() != 1)) {
				$fail($attribute . ' es invalido ');
			}*/
			//exists:tipo_cambios,fecha
		}], 'compra' => 'required|numeric', 'venta' => 'required|numeric']);//->validate();

		if($validar->fails()) {
			//dump($request->toArray());
			//dd($validar->errors()->toArray());
			$responseData = [
				'error' => true,
				'msg' => 'Error de Sistema'
			];
			return response()->json($responseData);
			//return response()->json(['success' => false, 'toastr' => 'warning', 'msg' => 'Se encontraron algunos errores.', 'errors' => $validar->errors()], 422);
		}
		$objTipoCambio = TipoCambio::find($request->id);

		//dd([Carbon::parse($objTipoCambio->fecha)->startOfDay() , Carbon::createFromFormat('d/m/Y', $request->fecha)->startOfDay()]);

		if( Carbon::parse($objTipoCambio->fecha)->startOfDay() != Carbon::createFromFormat('d/m/Y', $request->fecha)->startOfDay() ){
			return response()->json(['error' => true, 'toastr' => 'error', 'msg' => 'Error... Contacte con el administrador', 'redirectto' => 'home']);
		}

		//if
/*
		if ($objTipoCambio->fecha->format('d/m/Y') != Carbon::createFromFormat('d/m/Y', $req['fecha'])->format('d/m/Y')) {
			return response()->json(['success' => false, 'toastr' => 'error', 'msg' => 'Error... Contacte con el administrador', 'redirectto' => 'home']);
		}
*/
		$objTipoCambio->compra = $req['compra'];
		$objTipoCambio->venta = $req['venta'];
		$objTipoCambio->estado = 1;

		if ($objTipoCambio->save()) {
			$responseData = ['error' => false, 'msg' => ''];
			return response()->json($responseData);
		}

		//dd([$objTipoCambio->fecha->format('d/m/Y'), Carbon::createFromFormat('d/m/Y', $req['fecha'])->format('d/m/Y')]);

		//dd($objTipoCambio->fecha);

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int                      $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		//
	}

	public static function getTipoCambioActual($moneda_id){
		$tCambio = TipoCambio::where('moneda', $moneda_id)->orderBy('fecha', 'DESC')->first();
		return $tCambio;
	}
}
