<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\TesoreriaController;
use App\Models\Tesoreria\Area;
use App\Models\Tesoreria\Empresa;
use App\Models\Tesoreria\Estado;
use App\Models\Tesoreria\Grupo;
use App\Models\Tesoreria\Imports\PlanillaPagoImport;
use App\Models\Tesoreria\Moneda;
use App\Models\Tesoreria\Prioridad;
use App\Models\Tesoreria\Sede;
use App\Models\Tesoreria\Solicitud;
use App\Models\Tesoreria\SolicitudDetalle;
use App\Models\Tesoreria\SolicitudesSubTipos;
use App\Models\Tesoreria\SolicitudesTipos;
use App\Models\Tesoreria\SolicitudSeguimiento;
use App\Models\Tesoreria\Trabajador;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
class SolicitudController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request, $id_tipo=null) {

		$adm = false;//$request->get('admin')?true:false;


		if (Auth::user()->hasAnyRole(TesoreriaController::$gerentes)) {
			$adm = true;
		}


		$tipo_solicitud = $request->get('tipo');

		$empresas = Empresa::all();
		$monedas = Moneda::orderBy('id_moneda')->get();
		$solicitud_tipos = SolicitudesTipos::all();
		$solicitud_subtipos = SolicitudesSubTipos::all();
		$estados = Estado::all();
		$trabajadores = Trabajador::with([
			'postulante.persona'
		])->get();

		//dd($trabajadores->toArray());

		$prioridades = Prioridad::where('estado',1)->get();

		if ($id_tipo != null){
			/*$area_id = 6;
			$a = Auth::user()->trabajador->roles->first(function($item) use ($area_id) {
				return $item->pivot->id_area == $area_id;
			});

			dd($a->toArray());

			$a = Auth::user()->pertenece_a_empresa->first(function($item) {
				return $item->id_empresa == 4;
			});

			dd($a->contribuyente);*/

			$solicitudes = Solicitud::with(
				'detalles.partida',
				'moneda',
				'subtipo.tipo',
				'area.grupo.sede.empresa.contribuyente',
				'usuario.trabajador.postulante.persona',
				'estado',
				'historial',
				'prioridad'
			)
			->where('estado_id', $id_tipo)->get();

			return view('tesoreria.solicitud.lista')->with([
				'solicitudes' => $solicitudes,

				'empresas' => $empresas,
				'monedas' => $monedas,
				'solicitud_subtipos' => $solicitud_subtipos,
				'estados' => $estados,
				'adm' => $adm,
				'tipo_solicitud' => $tipo_solicitud,
				'prioridades' => $prioridades,
				'trabajadores' => $trabajadores,
			]);
		}


		return view('tesoreria.solicitud.index')->with([
			'empresas' => $empresas,
			'monedas' => $monedas,
			'solicitud_subtipos' => $solicitud_subtipos,
			'estados' => $estados,
			'adm' => $adm,
			'tipo_solicitud' => $tipo_solicitud,
			'prioridades' => $prioridades,
			'trabajadores' => $trabajadores,
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {

	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {

		//$arch = $request->archivo->store('solicitudes/adjuntos');

		//return response()->download(storage_path('app/'.$arch));

		//dd($arch);

		//$d_detalle = json_decode($request->get('detalle_solicitud'));
		//dd($d_detalle);
		//dd($_FILES);

		DB::beginTransaction();
		$nSolicitud = new Solicitud();
		$nSolicitud->codigo = $this->newSolicitudCode($request->get('reg_area'));
		$nSolicitud->detalle = $request->get('reg_detalle');
		$nSolicitud->importe = $request->get('reg_importe');
		$nSolicitud->fecha = now();//today();
		$nSolicitud->prioridad_id = $request->get('reg_prioridad');
		$nSolicitud->solicitud_subtipo_id = $request->get('reg_subtipo');
		$nSolicitud->estado_id = 1;
		$nSolicitud->usuario_id = Auth::user()->id_usuario;
		$nSolicitud->area_id = $request->get('reg_area');
		$nSolicitud->moneda_id = $request->get('reg_moneda');
		$nSolicitud->trabajador_id = $request->get('reg_usuario_final');

		//dd($nSolicitud);
		if ($nSolicitud->save()) {

			//dd($nSolicitud);
			$archivo = $request->file('archivo');

			if ($archivo) {
				$archivoUrl = $request->archivo->store('solicitudes/adjuntos');
				$nSolicitud->adjuntos = $archivoUrl;
				$nSolicitud->save();
				//Storage::put('ash.'.$archivo->getClientOriginalExtension() , $archivo);

				// $archivo->move('uploads', Hash::make($nSolicitud->id).'.'.$archivo->getClientOriginalExtension());
			}



			$d_detalle = json_decode($request->get('detalle_solicitud'));
			$vError = false;

			foreach ($d_detalle as $det) {
				$detalle = new SolicitudDetalle();
				$detalle->descripcion = $det->descripcion;
				$detalle->estimado = $det->estimado;
				$detalle->solicitud_id = $nSolicitud->id;
				$detalle->partida_id = $det->partida_id;
				//$detalle->estado = 1;

				if (!$detalle->save()) {
					$vError = true;
					break;
				}
			}


			$seguimiento = new SolicitudSeguimiento();
			$seguimiento->observacion = 'Generado por por el usuario';
			$seguimiento->solicitud_id = $nSolicitud->id;
			$seguimiento->estado_id = $nSolicitud->estado_id;
			$seguimiento->usuario_id = $nSolicitud->usuario_id;
			$seguimiento->fecha = $nSolicitud->fecha;

			if ($seguimiento->save() && (!$vError)) {
				$responseData = ['error' => false, 'msg' => '', 'data' => ['id' => $nSolicitud->id]];
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
	 * @param int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id) {
		//dd($id);
		$empresas = Empresa::all();
		$monedas = Moneda::orderBy('id_moneda')->get();
		$solicitud_tipos = SolicitudesTipos::all();

		//$solicitud = Solicitud::findOrFail($id);

		//dd($solicitud);

		$solicitud = Solicitud::with('detalles', 'moneda', 'subtipo.tipo', 'area.grupo.sede.empresa.contribuyente', 'usuario.trabajador.postulante.persona', 'estado', 'prioridad')->findOrFail($id);

		if ($request->ajax()) {
			return response()->json($solicitud->toArray());
		}


		//dd($solicitud->subtipo);


		return view('tesoreria.solicitud.create')->with(['solicitud' => $solicitud, 'empresas' => $empresas, 'monedas' => $monedas, 'solicitud_tipos' => $solicitud_tipos]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param int                      $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {

		$estado = $request->get('estado');
		DB::beginTransaction();
		$solicitud = Solicitud::findOrFail($id);
		if ($estado === null) {
			$cambEstado = false;
			$solicitud->detalle = $request->get('reg_detalle');
			$solicitud->importe = $request->get('reg_importe');
			$solicitud->prioridad_id = $request->get('reg_prioridad');
			$solicitud->solicitud_subtipo_id = $request->get('reg_subtipo');
			$solicitud->area_id = $request->get('reg_area');
			$solicitud->moneda_id = $request->get('reg_moneda');
			$solicitud->trabajador_id = $request->get('reg_usuario_final');
			if ($solicitud->estado_id != 1){
				$cambEstado = true;
			}


			if ($solicitud->save()) {

				SolicitudDetalle::where('solicitud_id', $solicitud->id)->delete();

				if ($cambEstado){
					$this->cambiarEstado($solicitud,1,'Levantando Observacion');
				}

				$d_detalle = json_decode($request->get('detalle_solicitud'));
				$vError = false;

				foreach ($d_detalle as $det) {
					$detalle = new SolicitudDetalle();
					$detalle->descripcion = $det->descripcion;
					$detalle->estimado = $det->estimado;
					$detalle->solicitud_id = $solicitud->id;
					$detalle->partida_id = $det->partida_id;

					if (!$detalle->save()) {
						$vError = true;
						break;
					}
				}

				if (!$vError) {
					$responseData = ['error' => false, 'msg' => '', 'data' => ['id' => $solicitud->id]];
					DB::commit();
				} else {
					$responseData = ['error' => true, 'msg' => 'Error de Sistema'];
					DB::rollBack();
				}


			} else {
				$responseData = ['error' => true, 'msg' => 'Datos invalidos'];
				DB::rollBack();
			}

		} else {
			$observacion = $request->get('observacion');

			$solicitud->estado_id = $estado;

			if ($solicitud->save()) {

				$seguimiento = new SolicitudSeguimiento();
				$seguimiento->observacion = $observacion;
				$seguimiento->solicitud_id = $solicitud->id;
				$seguimiento->estado_id = $solicitud->estado_id;
				$seguimiento->usuario_id = $solicitud->usuario_id;
				$seguimiento->fecha = now();

				if ($seguimiento->save()) {
					$responseData = ['error' => false, 'msg' => '', 'data' => ['id' => $solicitud->id]];
					DB::commit();
				} else {
					$responseData = ['error' => true, 'msg' => 'Error de Sistema'];
					DB::rollBack();
				}


			} else {
				$responseData = ['error' => true, 'msg' => 'Datos invalidos'];
				DB::rollBack();
			}
		}


		return response()->json($responseData);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		//
		DB::beginTransaction();
		$solicitud = Solicitud::findOrFail($id);
		$solicitud->estado_id = 7;
		if ($solicitud->save()) {
			$nSeguimiento = new SolicitudSeguimiento();
			$nSeguimiento->observacion = 'Anulado por el usuario';
			$nSeguimiento->solicitud_id = $solicitud->id;
			$nSeguimiento->estado_id = $solicitud->estado_id;
			$nSeguimiento->usuario_id = $solicitud->usuario_id;
			$nSeguimiento->fecha = now();
			if ($nSeguimiento->save()) {
				$responseData = ['error' => false, 'msg' => '', 'data' => []];
				DB::commit();
			} else {
				$responseData = ['error' => true, 'msg' => 'Error de Sistema destroy'];
				DB::rollBack();
			}
		} else {
			$responseData = ['error' => true, 'msg' => 'Error al anular, contacte con el administrador'];
			DB::rollBack();
		}

		return response()->json($responseData);
	}

	public function cambiarEstado(Solicitud $solicitud, $estado_id, $observacion='') {
		$solicitud->estado_id = $estado_id;
		$exito = false;

		DB::beginTransaction();
		if ($solicitud->save()) {

			$seguimiento = new SolicitudSeguimiento();
			$seguimiento->observacion = $observacion;
			$seguimiento->solicitud_id = $solicitud->id;
			$seguimiento->estado_id = $solicitud->estado_id;
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

	public function verExcel(Request $request) {
		$path = Storage::disk('local')->path($request->archivo);

		$dataExcel = Excel::toCollection(new PlanillaPagoImport(), $path);
		//$dataExcel = Excel::toCollection(null, $path);
		return response()->json($dataExcel->first()->toArray());

		/*
		foreach ($dataExcel->first() as $idx => $reg) {
			if ($idx == 0) {

			}
		}
		*/

		dd($dataExcel->first()->first());

		dd(Excel::toCollection(null, $path));

		$data = Excel::import(new PlanillaPagoImport(), $path);
		dd($data);

		//dd($path);

		\Excel::import($path, function($reader) {

			dd($reader);

			$results = $reader->get();
			dd($results);

		});
	}

	public function descargarAdjunto(Request $request){
		ob_end_clean();
		return Storage::download($request->archivo);
	}

	public function cambiarEstadoAjax(Request $request) {

		$idxs = $request->get('idxs');

		//dd($request->toArray());
		$estadosFinal = [];
		foreach ($idxs as $indice => $idx) {
			$solicitud = Solicitud::findorFail($idx['id']);

			$error = $this->cambiarEstado($solicitud, $idx['estado'], $idx['observacion']);
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





	public function newSolicitudCode($area_id) {
		$emp = Area::with('grupo.sede.empresa.contribuyente')->findOrFail($area_id);
		$codEmp = $emp->grupo->sede->empresa->codigo;

		//dd(today()->format('ymd'));

		$codBusc = $codEmp . '-S' . today()->format('ymd');
		$solBusc = Solicitud::where('codigo', 'like', $codBusc . '%')->get()->count() + 1;

		$return = $codBusc . '-' . str_pad($solBusc, 3, "0", STR_PAD_LEFT);

		return $return;
	}
}
