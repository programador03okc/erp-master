<?php

namespace App\Http\Controllers\Tesoreria;

use App\Models\Tesoreria\SolicitudesSubTipos;
use App\Models\Tesoreria\SolicitudesTipos;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SolicitudesTiposController extends Controller
{
    //
	public function index(){

		$tiposSolicitud = SolicitudesTipos::with('subtipos')->get();
		return view('tesoreria.administracion.solicitudes_tipos')->with([
			'tipos' => $tiposSolicitud
		]);
	}

	public function store(Request $request){

		//dd($request->toArray());

		$objGuardar = null;

		DB::beginTransaction();
		$idRegistro = $request->get( 'hidden_id' );
		switch ($request->get('hidden_chk_tipo')){
			case 'tipo':
				$objGuardar = ($idRegistro)?SolicitudesTipos::findOrFail( $idRegistro ):new SolicitudesTipos();
				break;
			case 'subtipo':
				$objGuardar = ($idRegistro)?SolicitudesSubTipos::findOrFail( $idRegistro ):new SolicitudesSubTipos();
				$objGuardar->solicitudes_tipos_id = $request->get('hidden_idpadre');
				//$objGuardar = SolicitudesSubTipos::findOrFail( $request->get( 'hidden_id' ) );
				break;
			default:
				break;
		}

		$objGuardar->descripcion = $request->get('txt_descripcion');
		$objGuardar->codigo = $request->get('txt_codigo');

		//dd($objGuardar->toArray());

		if ($objGuardar->save()){
			$responseData = [
				'error' => false,
				'msg' => '',
				'data' => [
					'id' => $objGuardar->id
				]
			];
			DB::commit();
		}
		else{
			$responseData = [
				'error' => true,
				'msg' => 'Error en el procesamiento',
				'data' => null
			];
			DB::rollBack();
		}

		return response()->json($responseData);

		//dd($objGuardar->toArray());

		//dd($request->toArray());
	}

	public function delete(Request $request){

	}
}
