<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\Controller;
use App\Models\Tesoreria\Almacen;
use App\Models\Tesoreria\CajaChica;
use App\Models\Tesoreria\CajaChicaMovimiento;
use App\Models\Tesoreria\CajaChicaSaldos;
use App\Models\Tesoreria\DocumentosOperacion;
use App\Models\Tesoreria\Empresa;
use App\Models\Tesoreria\Moneda;
use App\Models\Tesoreria\Proveedor;
use App\Models\Tesoreria\Sede;
use App\Models\Tesoreria\Solicitud;
use App\Models\Tesoreria\SolicitudDetalle;
use App\Models\Tesoreria\SolicitudSeguimiento;
use App\Models\Tesoreria\TipoCambio;
use App\Models\Tesoreria\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CajaChicaController extends Controller
{

	public function __construct() {
		//$this->middleware('roles:7');
		//$this->middleware('roles:1', ['except' => ['index','show']]);
		$this->middleware('roles:1,2,3,7');
	}

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	//dd(TipoCambioController::getTipoCambioActual(1 )->venta);

        $empresas = Empresa::with('contribuyente')->get();
        $proveedores = Proveedor::with('contribuyente')->get();
        $monedas = Moneda::orderBy('id_moneda')->get();
        $usuarios = Usuario::with('trabajador.postulante.persona')->get();

        //dd($usuarios->toArray());

        //dd($empresas->toArray());

        return view('tesoreria.administracion.cajachica')->with([
            'empresas'=>$empresas,
            'proveedores' => $proveedores,
            'monedas' => $monedas,
			'usuarios' => $usuarios
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
        //
		DB::beginTransaction();
		$cajaChica = new CajaChica();
		$cajaChica->descripcion = $request->get('reg_descripcion');
		$cajaChica->monto_apertura = $request->get('reg_monto_apertura');
		$cajaChica->saldo = $request->get('reg_monto_apertura');
		$cajaChica->monto_minimo = $request->get('reg_monto_minimo');
		$cajaChica->monto_maximo_movimiento = $request->get('reg_monto_maximo_movimiento');
		$cajaChica->fecha_creacion = now();
		$cajaChica->solicitud_id = $request->get('reg_solicitud_id');
		$cajaChica->area_id = $request->get('reg_area');
		$cajaChica->moneda_id = $request->get('reg_moneda');
		$cajaChica->responsable_id = $request->get('reg_responsable');
		$cajaChica->estado_id = 11;
		$cajaChica->usuario_id = Auth::user()->id_usuario;

		$estado = $request->get('reg_activacion');
		if($estado === 'on'){
			$cajaChica->estado_id = 11;
		}
		else{
			$cajaChica->estado_id = 7;
		}

		if ($cajaChica->save()){
			$movCajaChica = new CajaChicaMovimiento();
			$movCajaChica->cajachica_id = $cajaChica->id;
			$movCajaChica->fecha = $cajaChica->fecha_creacion;
			$movCajaChica->tipo_movimiento = 'I';
			$movCajaChica->doc_operacion_id = 10;
			$movCajaChica->moneda_id = $cajaChica->moneda_id;
			//$movCajaChica->doc_pago = $cajaChica->solicitud->codigo;
			$movCajaChica->data_pago = json_encode([
				['doc_tipo' => null, 'num_docu' => $cajaChica->solicitud->codigo, 'id_proveedor' => null, 'monto' => $cajaChica->monto_apertura, 'interno' => true]
			]);
			$movCajaChica->tipo_cambio = ($cajaChica->moneda_id === '1')?0:TipoCambioController::getTipoCambioActual($cajaChica->moneda_id )->venta;
			$movCajaChica->importe = $cajaChica->monto_apertura;
			$movCajaChica->observaciones = 'Apertura de Caja Chica';




			$solicitud = Solicitud::findOrFail($cajaChica->solicitud_id);
			$solicitud->estado_id = 5;

			if ($solicitud->save() && $movCajaChica->save()){
				$seguimiento = new SolicitudSeguimiento();
				$seguimiento->observacion = 'Solicitud Atendida';
				$seguimiento->solicitud_id = $solicitud->id;
				$seguimiento->estado_id = $solicitud->estado_id;
				$seguimiento->usuario_id = $cajaChica->usuario_id;
				$seguimiento->fecha = $cajaChica->fecha_creacion;

				$saldoCajaChica = new CajaChicaSaldos();
				$saldoCajaChica->cajachica_movimiento_id = $movCajaChica->id;
				$saldoCajaChica->fecha = $movCajaChica->fecha;
				$saldoCajaChica->ingreso = $movCajaChica->importe;
				$saldoCajaChica->saldo = $movCajaChica->importe;



				if ($seguimiento->save() && $saldoCajaChica->save()){
					$responseData = [
						'error' => false,
						'msg' => '',
						'data' => [
							'id' => $cajaChica->id
						]
					];
					DB::commit();
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
		}
		else{
			$responseData = [
				'error' => true,
				'msg' => 'Error de Sistema'
			];
			DB::rollBack();
		}

		//dd($request->toArray());
		return response()->json($responseData);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CajaChica  $cajaChica
     * @return \Illuminate\Http\Response
     */
    public function show(CajaChica $cajaChica)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CajaChica  $cajaChica
     * @return \Illuminate\Http\Response
     */
    public function edit(CajaChica $cajaChica)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CajaChica  $cajaChica
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $cajachica_id)
    {
        //
		DB::beginTransaction();
		//$cajaChica = new CajaChica();
		$cajaChica = CajaChica::findOrFail($cajachica_id);
		$cajaChica->descripcion = $request->get('reg_descripcion');
		//$cajaChica->monto_apertura = $request->get('reg_monto_apertura');
		//$cajaChica->saldo = $request->get('reg_monto_apertura');
		$cajaChica->monto_minimo = $request->get('reg_monto_minimo');
		$cajaChica->monto_maximo_movimiento = $request->get('reg_monto_maximo_movimiento');
		//$cajaChica->fecha_creacion = now();
		//$cajaChica->solicitud_id = $request->get('reg_solicitud_id');
		$cajaChica->area_id = $request->get('reg_area');
		$cajaChica->moneda_id = $request->get('reg_moneda');
		$cajaChica->responsable_id = $request->get('reg_responsable');

		//dd($cajaChica);

		$estado = $request->get('reg_activacion');
		if($estado === 'on'){
			$cajaChica->estado_id = 11;
		}
		else{
			$cajaChica->estado_id = 7;
		}
		//$cajaChica->usuario_id = 46;

		if ($cajaChica->save()){

			$responseData = [
				'error' => false,
				'msg' => '',
				'data' => [
					'id' => $cajaChica->id
				]
			];
			DB::commit();
		}
		else{
			$responseData = [
				'error' => true,
				'msg' => 'Error de Sistema'
			];
			DB::rollBack();
		}

		//dd($request->toArray());
		return response()->json($responseData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CajaChica  $cajaChica
     * @return \Illuminate\Http\Response
     */
    public function destroy(CajaChica $cajaChica)
    {
        //
    }

    public function ajaxTipoCambio($moneda_id, $fecha=null){
        $t_cambio = TipoCambio::where('moneda', $moneda_id);
        if ($fecha==null){
            $t_cambio = $t_cambio->orderBy('fecha', 'DESC');
        }
        else {
            $t_cambio = $t_cambio->where('fecha', $fecha);
        }
        if ($t_cambio->first()){
            return response()->json($t_cambio->first()->get());
        }
        //$t_cambio = $t_cambio->first()->get();
        //$t_cambio = TipoCambio::where('moneda', $moneda_id)->get();
        return response()->json([]);
    }

    public function ajaxListaAlmacenes($empresa=null, $sede=null){

        $resultados = Sede::with('almacenes');
        if ($empresa != null){
            $resultados = $resultados->where('id_empresa', $empresa);
            if ($sede != null){
                $resultados = $resultados->where('id_sede', $sede);
            }
        }
        $resultados = $resultados->get()->toArray();

        return response()->json($resultados);
    }


    public function ajaxListar(Request $request, $almacen_id = null){

        if ($almacen_id != null){
            $fecha = $request->get('fecha');

            if ($fecha == null){
                $fecha = today();
            }

            $movimientos = CajaChicaMovimiento::where('almacen_id', $almacen_id)
                ->where('fecha', $fecha)
                ->with(['almacen', 'moneda', 'doc_operacion'])
                ->get()
                ->toArray();

            //$movimientos = CajaChicaMovimiento::where('fecha', $fecha)->with(['almacen', 'moneda', 'doc_operacion'])->get()->toArray();

            $data['data'] = $movimientos;
            return response()->json($data);



        }

/*
        $sucursal = Sede::with('cajachica')->get();

        dd($sucursal);
        */
//$tmp = DocumentosOperacion::with('cajachica')->get();



        $fecha = $request->get('fecha');

        if ($fecha == null){
            $fecha = today();
            //dd($fecha);
        }

        //$movimientos = CajaChicaMovimiento::find(1)->sucursal()->get()->toArray();

        //dd($movimientos);

        $movimientos = CajaChicaMovimiento::where('fecha', $fecha)->with(['almacen', 'moneda', 'doc_operacion'])->get()->toArray();

        /*
         *                 {'data': 'id'},
                { 'data': null},
                {'data': 'tipo_movimiento', 'render':function (data) {
                    //console.log(data);
                    if(data == 'I'){
                        return '<i class="fas fa-sign-in-alt fa-2x text-success"></i>';
                    }
                    else if(data == 'E'){
                        return '<i class="fas fa-sign-out-alt fa-2x text-danger"></i>';
                    }
                }},
                {'data': 'operacion.descripcion'},
                {'data': 'doc_pago'},
                {'data': 'proveedor_id'},
                {'data': 'moneda.simbolo'},
                {'data': 'tipo_cambio'},
                {'data': 'importe'},
                {'data': 'observaciones'}
         */


        //$movimientos = CajaChicaMovimiento::with(['sucursal', 'moneda'])->get();
        //dd($movimientos);
        $data['data'] = $movimientos;
        return response()->json($data);

        dd($movimientos);

        /*
         *         "id" => 2
        "id_sucursal" => 1
        "fecha" => "2019-05-10"
        "tipo_movimiento" => "I"
        "id_docpago" => 1
        "doc_pago" => null
        "id_proveedor" => null
        "id_moneda" => 1
        "tipo_cambio" => "3.32"
        "importe" => "100.00"
        "observaciones" => "Saldo Anterior"
         */


        $data['data'][] = [
            'id' => '001',
            'tipo' => 'E',
            'det_mov' => 'Efectivo Disponible ILO',
            'num_doc' => 'F001-33203',
            'proveedor' => 'SANTOS CMI',
            'moneda' => 'SOLES (S/)',
            'tipo_cambio' => '3.382',
            'importe' => 20.0,
            'obs' => 'MIGUEL_POR NEVIO DE DOC A IQUITOS'
        ];
        $data['data'][] = [
            'id' => '002',
            'tipo' => 'I',
            'det_mov' => 'Efectivo Disponible ILO',
            'num_doc' => 'F001-33203',
            'proveedor' => 'SANTOS CMI',
            'moneda' => 'SOLES (S/)',
            'tipo_cambio' => '3.382',
            'importe' => 20.0,
            'obs' => 'MIGUEL_POR NEVIO DE DOC A IQUITOS'
        ];
        return response()->json($data);
    }
}
