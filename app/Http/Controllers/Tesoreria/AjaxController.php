<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\TesoreriaController;
use App\Models\Logistica\Contribuyente;
use App\Models\Tesoreria\Area;
use App\Models\Tesoreria\CajaChica;
use App\Models\Tesoreria\CajaChicaMovimiento;
use App\Models\Tesoreria\CajaChicaSaldos;
use App\Models\Tesoreria\Empresa;
use App\Models\Tesoreria\Grupo;
use App\Models\Tesoreria\Moneda;
use App\Models\Tesoreria\PlanillaPago;
use App\Models\Tesoreria\Presupuesto;
use App\Models\Tesoreria\PresupuestoTitulo;
use App\Models\Tesoreria\Proveedor;
use App\Models\Tesoreria\Sede;
use App\Models\Tesoreria\Solicitud;
use App\Models\Tesoreria\SolicitudesSubTipos;
use App\Models\Tesoreria\SolicitudesTipos;
use App\Models\Tesoreria\TipoCambio;
use App\Models\Tesoreria\TipoContribuyente;
use App\Models\Tesoreria\Usuario;
use Carbon\Carbon;
use DiDom\Document;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
//use Peru\Http\ContextClient;
//use Peru\Sunat\Ruc;

class AjaxController extends Controller
{
	public function getCajasChicas(Request $request){
		$cajasChicas = CajaChica::with(
			'solicitud',
			'area.grupo.sede.empresa.contribuyente',
			'usuario.trabajador.postulante.persona',
			'responsable.trabajador.postulante.persona',
			'moneda',
			'estado'
		);

		$empresa = $request->get('empresa');
		if ($empresa !== null){
			$cajasChicas = $cajasChicas->whereHas('area.grupo.sede.empresa', function ($query) use ($empresa){
				$query->where('id_empresa', $empresa);
			});
		}

		$valRet = $cajasChicas->get();
		$data['data'] = $valRet->toArray();
		return response()->json($data);
	}

	public function getPlanillaPagos(Request $request){
		$orden = $request->get('orden');
		$filtro = $request->get('filtro');

		$planillas = PlanillaPago::with([
			'historial',
			'solicitudes.solicitud.detalles',
			'solicitudes.persona',
			'solicitudes.proveedor',
			'cta_origen',
			'tipo_planilla',
			'usuario',
			'estado',
			'moneda',
		]);

		if ($orden != null){
			$newOrden = explode(':', $orden);
			if (count($newOrden) == 1){
				$planillas = $planillas->orderBy('id', $newOrden[0]);
			}
			else{
				$planillas = $planillas->orderBy($newOrden[0], $newOrden[1]);
			}

		}
		else{
			$planillas = $planillas->orderBy('id', 'DESC');
		}

		if ($filtro != null){
			$newFiltro = json_decode($filtro);
			foreach ($newFiltro as $f){
				if ($f->condicion == ''){
					$planillas = $planillas->where($f->campo, $f->valor);
				}
				else{
					$planillas = $planillas->where($f->campo, $f->condicion, $f->valor);
				}
			}
		}


		$valRet = $planillas->get();
		$data['data'] = $valRet->toArray();
		return response()->json($data);
	}

    public function getSolicitudes(Request $request){

    	$orden = $request->get('orden');
    	$filtro = $request->get('filtro');



        $solicitudes = Solicitud::with(
			'detalles.partida',
            'moneda',
            'subtipo.tipo',
            'area.grupo.sede.empresa.contribuyente',
            'usuario.trabajador.postulante.persona',
            'estado',
            'historial',
			'prioridad'
        );

        if ($orden != null){
        	$newOrden = explode(':', $orden);
        	if (count($newOrden) == 1){
				$solicitudes = $solicitudes->orderBy('id', $newOrden[0]);
			}
        	else{
				$solicitudes = $solicitudes->orderBy($newOrden[0], $newOrden[1]);
			}

		}
        else{
			$solicitudes = $solicitudes->orderBy('id', 'DESC');
		}

        /*
		if(!Auth::user()->hasAnyRole(TesoreriaController::$gerentes)){
			$solicitudes = $solicitudes->where('usuario_id', Auth::user()->id_usuario);
		}*/

		if ($filtro != null){
			$newFiltro = json_decode($filtro);
			foreach ($newFiltro as $f){
				if ($f->condicion == ''){
					$solicitudes = $solicitudes->where($f->campo, $f->valor);
				}
				else{
					$solicitudes = $solicitudes->where($f->campo, $f->condicion, $f->valor);
				}
			}
		}


        $valRet = $solicitudes->get();
        $data['data'] = $valRet->toArray();
        return response()->json($data);
    }

    public function getSolicitudesSubTipos($soltipo_id){
        $subTipos = SolicitudesSubTipos::where('solicitudes_tipos_id', $soltipo_id);

        $valRet = $subTipos->get();
        return response()->json($valRet->toArray());
    }


    public function getSedes($empresa_id){
        $sedes = Sede::where('id_empresa', $empresa_id)->get()->toArray();

        return response()->json($sedes);
    }
    public function getGruposAreas($sede_id){
        $sede = Sede::find($sede_id);
        $grupos = $sede->grupos()->with('areas');

        return response()->json($grupos->get()->toArray());

        dd($grupos->get()->toArray());
        $areas = $grupos->areas();

        dd($areas->get()->toArray());


        dd($s->grupos()->get()->toArray());
        $g = Grupo::with('sede')->get()->toArray();
        //$g = Sede::with('grupos')->get()->toArray();

        dd($g);

    }

	public function getPresupuesto($id_grupo){
		$presup = DB::table('finanzas.presup')
			->where([['id_grupo','=',$id_grupo],
				['estado','=',1]])
			->get();

		$html = '';
		foreach($presup as $idx => $p){
			$titulos = DB::table('finanzas.presup_titu')
				->where([['id_presup','=',$p->id_presup],
					['estado','=',1]])
				->orderBy('presup_titu.codigo')
				->get();
			$partidas = DB::table('finanzas.presup_par')
				->select('presup_par.*','presup_pardet.descripcion as des_pardet')
				->join('finanzas.presup_pardet','presup_pardet.id_pardet','=','presup_par.id_pardet')
				->where([['presup_par.id_presup','=',$p->id_presup],
					['presup_par.estado','=',1]])
				->orderBy('presup_par.codigo')
				->get();


			$html .='
            <div id='.$p->codigo.' class="panel panel-primary" style="width:100%;">
                <h5 onclick="apertura('.$p->id_presup.');" class="panel-heading" style="cursor: pointer; margin: 0;" >
                '.$p->descripcion.' </h5>
                <div id="pres-'.$p->id_presup.'" class="oculto" style="width:100%;">
                    <table class="table table-bordered partidas" width="100%">
                        <tbody> 
                ';
			foreach($titulos as $ti){
				$html .='
                    <tr id="com-'.$ti->id_titulo.'">
                        <td><strong>'.$ti->codigo.'</strong></td>
                        <td><strong>'.$ti->descripcion.'</strong></td>
                        <td class="right"><strong>'.$ti->total.'</strong></td>
                    </tr>';
				foreach($partidas as $par){
					if ($ti->codigo == $par->cod_padre){
						$html .='
                            <tr id="par-'.$par->id_partida.'" onclick="selectPartida('.$par->id_partida.');" style="cursor: pointer; margin: 0;">
                                <td name="codigo">'.$par->codigo.'</td>
                                <td name="descripcion">'.$par->des_pardet.'</td>
                                <td class="right">'.$par->importe_total.'</td>
                            </tr>';
					}
				}
			}
			$html .='
                    </tbody>
                </table>
            </div>
        </div>';
		}
		return response()->json($html);
	}


	public function getPresupuesto_Mio($area_id){

		$area = Area::with('grupo')->findOrFail($area_id);

		//dd($area->toArray());

		$presupuesto = Presupuesto::with('presupuesto_titulos.hijosRecursivo')->where('id_grupo', $area->id_grupo);

		//$da = $presupuesto->first()->presupuesto_titulos->where('cod_padre', '');
		//dd($da);



		$datRet = [];
		foreach ($presupuesto->get() as $presup){

			//dd($presup);

			$datRet[] = $presup->presupuesto_titulos->where('cod_padre', '');

		}
		return response()->json($datRet);
		dd($datRet);


		dd($presupuesto->toArray());

    	$aa = PresupuestoTitulo::with('hijosRecursivo')->where('cod_padre','')->get()->toArray();
    	//$aa = PresupuestoTitulo::with('hijosRecursivo')->where('cod_padre', '!=', '')->get()->toArray();

    	dd($aa);

		$newArray = [];
		foreach ($presupuesto as $prep){
			foreach ($prep->presupuesto_titulos as $pres){
				$codPadre = $pres->cod_padre;
				$codigo = $pres->codigo;

				//dd($codPadre);

				if ($codPadre != ""){
					//dd($pres->toArray());
					$nId = str_replace($codPadre . '.', '', $codigo);
					$detCod = explode('.', $codPadre);
					foreach ($detCod as $idx => $det ){
						//dd($detCod);
						//dd([count($detCod), $idx]);
						if (count($detCod) == ($idx + 1)){
							$newArray[$det] = $pres;
						}
						else{
							$newArray[$det] = [];
						}
					}
				}
				else{
					$newArray[$pres->codigo] = $pres;
				}
			}

		}


		dd($newArray);

		$nArray = [];
		$col = collect([]);
    	foreach ($presupuesto as $pres){
			foreach ($pres->presupuesto_titulos as $tit){
				$cod = $tit->codigo;
				$codList = explode('.', $cod);
				$nArray[count($codList)][] = $tit->toArray();
				$col = $col->concat($tit);
			}
		}

    	//$tita = $presupuesto->presupuesto_titulos();


    	//dd($presupuesto->toArray());


    	dd($nArray);

	}

	public function getCajaChicaMovimientos(Request $request, $cajachica_id ){

		$movimientos = CajaChicaMovimiento::with(
			//'cajachica',
			'moneda',
			'doc_operacion',
			'proveedor',
			'vale',
			'saldo'
		)->where('cajachica_id', $cajachica_id);

		$fecha_ini = $request->get('f_ini');

		if($fecha_ini !== null){
			$fecha_fin = $request->get('f_fin');
			if ($fecha_fin !== null){
				$movimientos = $movimientos->whereBetween('fecha', [$fecha_ini, $fecha_fin]);
			}
			else{
				$movimientos = $movimientos->whereDate('fecha', $fecha_ini);
			}
		}

		$movimientos = $movimientos->get();

		$data['data'] = $movimientos->toArray();
		return response()->json($data);
	}

	public function getSaldoCajaChica($cajachica_id){

		$saldoAnterior = CajaChicaSaldos::with('cajachica_movimiento.cajachica')->whereHas('cajachica_movimiento.cajachica', function ($query) use ($cajachica_id){
			$query->where('id', $cajachica_id);
		})->orderByDesc('fecha')->get();

		//dd($saldoAnterior->first()->toArray());

		$retVal = [
			'inicial' => $saldoAnterior->first()->cajachica_movimiento->cajachica->monto_apertura,
			'ingresos' => $saldoAnterior->sum('ingreso'),
			'egresos' => $saldoAnterior->sum('egreso'),
			'saldo' => $saldoAnterior->first()->cajachica_movimiento->cajachica->saldo,
		];

		return response()->json($retVal);
	}

	public function getProveedores(){
		$proveedores = Proveedor::with('contribuyente')->get();

		return response()->json($proveedores);
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


    public function listarMovimientos(Request $request, $almacen_id = null){

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




    // METODOS QUE DEPENDEN DE UN EXTERNO
	const URL_CONSULT = 'http://e-consultaruc.sunat.gob.pe/cl-ti-itmrconsruc/jcrS00Alias';
	const URL_RANDOM = 'http://e-consultaruc.sunat.gob.pe/cl-ti-itmrconsruc/captcha?accion=random';


	public static function getDataPersonaContribuyente($tipo, $identificador, $interno=false) {

		//

/*
		$req = new Client(['cookies' => true]);

		$reqCodigo = $req->get(self::URL_RANDOM)->getBody()->getContents();

		$url = self::URL_CONSULT."?accion=consPorRuc&nroRuc={$identificador}&numRnd={$reqCodigo}&tipdoc=";

		//dd([$reqCodigo, $url]);

		$req = $req->get($url);

		dd($req->getBody()->getContents());
*/

		/**
		 *         'id_tipo_contribuyente',
		'id_doc_identidad',
		'razon_social',
		'telefono',
		'celular',
		'direccion_fiscal',
		'ubigeo',
		'id_pais',
		'estado',
		'fecha_registro'
		 */



		$intentos = 0;
		while (true){

			try {


				switch ($tipo) {
					case 'persona':
						//$obj = new \Peru\Jne\Dni();
						break;
					case 'contribuyente':

						//dd($identificador);

						$contribuyente = Contribuyente::where('nro_documento', $identificador)->first();

						//dd($contribuyente->toArray());

						if ($contribuyente){
							$respuesta = [
								'tipo_contribuyente' => null,
								'id_contribuyente' => $contribuyente->id_contribuyente,
								'id_tipo_contribuyente' => $contribuyente->id_tipo_contribuyente,
								'nro_documento' => $contribuyente->nro_documento,
								'razon_social' => $contribuyente->razon_social,
								'direccion_fiscal' => $contribuyente->direccion_fiscal,
							];
						}
						else{
							$req = new Client(['cookies' => true]);

							$reqCodigo = $req->get(self::URL_RANDOM)->getBody()->getContents();

							$url = self::URL_CONSULT."?accion=consPorRuc&nroRuc={$identificador}&numRnd={$reqCodigo}&tipdoc=";

							//dd([$reqCodigo, $url]);

							$req = $req->get($url);

							$htmlCompleto = $req->getBody()->getContents();

							$sunat = new Document($htmlCompleto);

							$tabla = $sunat->first('table');

							$razon_social = $sunat->first('input[name^=desRuc]')->attr('value');

							$tipo_empresa = $tabla->first('.bgn:contains("Tipo Contribuyente:")')->parent()->first('.bg')->text();
							$direccion_fiscal = $tabla->first('.bgn:contains("Domicilio Fiscal:")')->parent()->first('.bg')->text();


							$tpContribuyente = TipoContribuyente::where('descripcion','like','%' . trim($tipo_empresa) . '%')->first();

							$respuesta = [
								'tipo_contribuyente' => $tipo_empresa,
								'id_contribuyente' => null,
								'id_tipo_contribuyente' => $tpContribuyente->id_tipo_contribuyente,
								'nro_documento' => $identificador,
								'razon_social' => $razon_social,
								'direccion_fiscal' => preg_replace('!\s+!', ' ', $direccion_fiscal)  //$direccion_fiscal,
							];
						}
						break;
				}




				break;
			} catch (\Exception $e) {
				$intentos++;
				//echo 'Excepción capturada: ',  $e->getMessage(), "\n";
				if ($intentos > 5){
					$respuesta = [
						'tipo_contribuyente' => '',
						'nro_documento' => $identificador,
						'razon_social' => '',
						'direccion_fiscal' => '',
					];
				}
			}
		}

		return response()->json($respuesta);














/*
		$dataResponse = [];
		switch ($tipo) {
			case 'persona':
				$obj = new \Peru\Jne\Dni();
				break;
			case 'contribuyente':
				$obj = new \Peru\Sunat\Ruc();
				break;
		}
		$obj->setClient(new \Peru\Http\ContextClient());
		$dataResponse = $obj->get($identificador);
		if ($dataResponse === false) {
			echo $obj->getError();
			exit();
		}

		if ($interno){
			return $dataResponse;
		}

		return response()->json($dataResponse);

		*/
	}

}
