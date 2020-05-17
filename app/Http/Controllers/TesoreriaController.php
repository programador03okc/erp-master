<?php

namespace App\Http\Controllers;

use App\Models\Tesoreria\Area;
use App\Models\Tesoreria\CajaChicaMovimiento;
use App\Models\Tesoreria\ContribuyenteCuenta;
use App\Models\Tesoreria\DocumentosOperacion;
use App\Models\Tesoreria\Empresa;
use App\Models\Tesoreria\PlanillaPagosEstados;
use App\Models\Tesoreria\PlanillaPagosTipos;
use App\Models\Tesoreria\SolicitudesSubTipos;
use App\Models\Tesoreria\SolicitudesTipos;
use App\Models\Tesoreria\TipoCambio;
use App\Models\Tesoreria\TipoCuenta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class TesoreriaController extends Controller
{
	public static $gerentes = [1,2,3,7];
	public static $respCaja_adm = [7];
	public static $respCaja_usr = [7];
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {


		$this->middleware('auth');
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	//dd(session('login_rol'));
		//dd(session()->all());


    	/*$usuario_roles = Auth::user()->roles;
		foreach ($usuario_roles as $role) {
			$idArea = $role->pivot->id_area;

			$nEmpresa = Empresa::whereHas('sedes.grupos.areas', function ($q) use ($idArea){
				$q->where('id_area', $idArea);
			})->count();

			if ($nEmpresa > 0){
				//VAlidado
				break;
			}
    	}*/




		$fechaHoy = now(); //Carbon::now();

		$tipoCambio = TipoCambio::where('fecha', '=', $fechaHoy)->first();

		//dd($tipoCampo);

		$arrayDatosDolar = [];

		if(is_null($tipoCambio)){
			$dataDolar = HynoTechController::obtenerDatosDolar($fechaHoy->year, $fechaHoy->month);

			$tipoCambio = new TipoCambio();
			$tipoCambio->fecha = $fechaHoy;
			$tipoCambio->compra = $dataDolar['compra'];
			$tipoCambio->venta = $dataDolar['venta'];
			$tipoCambio->moneda = 2;
			$tipoCambio->save();




			//dd($arrayDatosDolar);
			//TipoCambio::create($arrayDatosDolar);
		}

		//dd($tipoCambio);


		if(is_null($tipoCambio->estado)){
			$arrayDatosDolar = [
				'id' => $tipoCambio->id_tp_cambio,
				'fecha' => $fechaHoy->format('d/m/Y'),
				'sunat' => [
					'compra' => $tipoCambio->compra,
					'venta' => $tipoCambio->venta,
				]

			];
		}
		//dd($arrayDatosDolar);
        return view('tesoreria.main', compact('arrayDatosDolar'));
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

    public function eliminarTablas(){

		Schema::connection('pgsql')->dropIfExists('finanzas.solicitudes_detalles');
		Schema::connection('pgsql')->dropIfExists('finanzas.solicitudes_seguimiento');

		Schema::connection('pgsql')->dropIfExists('finanzas.cajachica_movimientos_vales');
		Schema::connection('pgsql')->dropIfExists('finanzas.cajachica_saldos');
		Schema::connection('pgsql')->dropIfExists('finanzas.cajachica_vales');
		Schema::connection('pgsql')->dropIfExists('finanzas.cajachica_movimientos');
		Schema::connection('pgsql')->dropIfExists('finanzas.cajachica');

		Schema::connection('pgsql')->dropIfExists('finanzas.solicitudes');
		Schema::connection('pgsql')->dropIfExists('finanzas.solicitudes_subtipos');
		Schema::connection('pgsql')->dropIfExists('finanzas.solicitudes_tipos');


		Schema::connection('pgsql')->dropIfExists('finanzas.sis_documentos_operacion');





		Schema::connection('pgsql')->dropIfExists('finanzas.planillapagos_seguimiento');
		Schema::connection('pgsql')->dropIfExists('finanzas.planillapagos');

		Schema::connection('pgsql')->dropIfExists('finanzas.planillapagos_tipos');
		Schema::connection('pgsql')->dropIfExists('finanzas.planillapagos_estados');

	}

    public function crearUno(){

		Schema::connection('pgsql')->dropIfExists('finanzas.planillapagos_seguimiento');
		Schema::connection('pgsql')->dropIfExists('finanzas.planillapagos');

		Schema::connection('pgsql')->dropIfExists('finanzas.planillapagos_tipos');
		Schema::connection('pgsql')->dropIfExists('finanzas.planillapagos_estados');


		Schema::connection('pgsql')->create('finanzas.planillapagos_tipos', function($table) {
			$table->increments('id');
			$table->string('descripcion', 100);
			$table->smallInteger('estado')->default(1);
			$table->timestamps();
		});
		$data = [
			[ 'estado' => 1, 'descripcion' => 'Normal' ],
			[ 'estado' => 1, 'descripcion' => 'Extraordinaria' ],
		];

		PlanillaPagosTipos::insert($data);



		Schema::connection('pgsql')->create('finanzas.planillapagos_estados', function($table) {
			$table->increments('id');
			$table->string('descripcion', 100);
			$table->string('bootstrap_color', 15);
			$table->smallInteger('estado')->default(1);
			$table->timestamps();
		});
		$data = [
			[ 'estado' => 1, 'descripcion' => 'Enviado', 'bootstrap_color' =>  'default'],
			[ 'estado' => 1, 'descripcion' => 'Procesado', 'bootstrap_color' =>  'primary' ],
			[ 'estado' => 1, 'descripcion' => 'Abonado', 'bootstrap_color' =>  'success' ],
			[ 'estado' => 1, 'descripcion' => 'Abonado por rendir', 'bootstrap_color' =>  'warning' ],
			[ 'estado' => 1, 'descripcion' => 'Anulado', 'bootstrap_color' =>  'default' ],
			[ 'estado' => 1, 'descripcion' => 'Rechazado', 'bootstrap_color' =>  'danger' ],
		];

		PlanillaPagosEstados::insert($data);


		Schema::connection('pgsql')->create('finanzas.planillapagos', function($table) {
			$table->increments('id');

			$table->dateTime('fecha');

			$table->string('detalle');

			$table->integer('cuenta_origen_id')->unsigned();
			$table->foreign('cuenta_origen_id')
				->references('id_cuenta_contribuyente')
				->on('contabilidad.adm_cta_contri')
				->onDelete('cascade');
			$table->string('cuenta_destino');
			$table->string('cuenta_destino_tipo');

			$table->decimal('importe');

			$table->string('observaciones');
			/*
			$table->string('tipo_doc_referencia');
			$table->string('num_doc_referencia');*/

			$table->integer('solicitud_id')->unsigned();
			$table->foreign('solicitud_id')
				->references('id')
				->on('finanzas.solicitudes')
				->onDelete('cascade');

			$table->integer('proveedor_id')->unsigned()->nullable();
			$table->foreign('proveedor_id')
				->references('id_proveedor')
				->on('logistica.log_prove')
				->onDelete('cascade');

			$table->integer('persona_id')->unsigned()->nullable();
			$table->foreign('persona_id')
				->references('id_persona')
				->on('rrhh.rrhh_perso')
				->onDelete('cascade');

			$table->integer('moneda_id')->unsigned();
			$table->foreign('moneda_id')
				->references('id_moneda')
				->on('configuracion.sis_moneda')
				->onDelete('cascade');

			$table->integer('planillapago_tipo_id')->unsigned()->nullable();
			$table->foreign('planillapago_tipo_id')
				->references('id')
				->on('finanzas.planillapagos_tipos')
				->onDelete('cascade');

			$table->integer('estado_id')->unsigned()->nullable();
			$table->foreign('estado_id')
				->references('id')
				->on('finanzas.planillapagos_estados')
				->onDelete('cascade');

			$table->integer('usuario_id')->unsigned();
			$table->foreign('usuario_id')
				->references('id_usuario')
				->on('configuracion.sis_usua')
				->onDelete('cascade');

			$table->timestamps();
		});



		Schema::connection('pgsql')->create('finanzas.planillapagos_seguimiento', function ($table) {
			$table->increments('id');
			$table->string('observacion')->nullable();
			$table->integer('planillapagos_id')->unsigned();
			$table->foreign('planillapagos_id')
				->references('id')
				->on('finanzas.planillapagos')
				->onDelete('cascade');
			$table->integer('estado_id')->unsigned();
			$table->foreign('estado_id')
				->references('id')
				->on('finanzas.planillapagos_estados')
				->onDelete('cascade');
			$table->integer('usuario_id')->unsigned();
			$table->foreign('usuario_id')
				->references('id_usuario')
				->on('configuracion.sis_usua')
				->onDelete('cascade');

			$table->dateTime('fecha');

			$table->timestamps();
		});





    }

    public function crearTablas(){
		Schema::connection('pgsql')->dropIfExists('finanzas.solicitudes_detalles');
		Schema::connection('pgsql')->dropIfExists('finanzas.solicitudes_seguimiento');

		Schema::connection('pgsql')->dropIfExists('finanzas.cajachica_movimientos_vales');
		Schema::connection('pgsql')->dropIfExists('finanzas.cajachica_saldos');
		Schema::connection('pgsql')->dropIfExists('finanzas.cajachica_vales');
		Schema::connection('pgsql')->dropIfExists('finanzas.cajachica_movimientos');
		Schema::connection('pgsql')->dropIfExists('finanzas.cajachica');

		Schema::connection('pgsql')->dropIfExists('finanzas.solicitudes');
		Schema::connection('pgsql')->dropIfExists('finanzas.solicitudes_subtipos');
		Schema::connection('pgsql')->dropIfExists('finanzas.solicitudes_tipos');


		Schema::connection('pgsql')->dropIfExists('finanzas.sis_documentos_operacion');

		//dd('a');





		try{
            // CREAR CAJA CHICA

            Schema::connection('pgsql')->create('finanzas.solicitudes_tipos', function ($table) {
                $table->increments('id');
                $table->string('codigo', 100);
                $table->string('descripcion', 100);
				$table->smallInteger('estado')->nullable();
            });

            Schema::connection('pgsql')->create('finanzas.solicitudes_subtipos', function ($table) {
                $table->increments('id');
                $table->string('codigo', 100);
                $table->string('descripcion', 100);
                $table->integer('solicitudes_tipos_id')->unsigned();
                $table->foreign('solicitudes_tipos_id')
                    ->references('id')
                    ->on('finanzas.solicitudes_tipos')
					->onDelete('cascade');
				$table->smallInteger('estado')->nullable();
            });

            Schema::connection('pgsql')->create('finanzas.solicitudes', function ($table) {
                $table->increments('id');
                $table->string('codigo', 100)->nullable();
                $table->string('detalle', 100);
                $table->decimal('importe');
                $table->dateTime('fecha');

                $table->integer('prioridad_id')->unsigned();
                $table->foreign('prioridad_id')
                    ->references('id_prioridad')
                    ->on('administracion.adm_prioridad')
					->onDelete('cascade');

                $table->integer('solicitud_subtipo_id')->unsigned();
                $table->foreign('solicitud_subtipo_id')
                    ->references('id')
                    ->on('finanzas.solicitudes_subtipos')
					->onDelete('cascade');

                $table->integer('usuario_id')->unsigned();
                $table->foreign('usuario_id')
                    ->references('id_usuario')
                    ->on('configuracion.sis_usua')
					->onDelete('cascade');

                $table->integer('estado_id')->unsigned();
                $table->foreign('estado_id')
                    ->references('id_estado_doc')
                    ->on('administracion.adm_estado_doc')
					->onDelete('cascade');
                $table->integer('area_id')->unsigned();
                $table->foreign('area_id')
                    ->references('id_area')
                    ->on('administracion.adm_area')
					->onDelete('cascade');
                $table->integer('moneda_id')->unsigned();
                $table->foreign('moneda_id')
                    ->references('id_moneda')
                    ->on('configuracion.sis_moneda')
					->onDelete('cascade');
            });

			Schema::connection('pgsql')->create('finanzas.solicitudes_seguimiento', function ($table) {
				$table->increments('id');
				$table->string('observacion')->nullable();
				$table->integer('solicitud_id')->unsigned();
				$table->foreign('solicitud_id')
					->references('id')
					->on('finanzas.solicitudes')
					->onDelete('cascade');
				$table->integer('estado_id')->unsigned();
				$table->foreign('estado_id')
					->references('id_estado_doc')
					->on('administracion.adm_estado_doc')
					->onDelete('cascade');
				$table->integer('usuario_id')->unsigned();
				$table->foreign('usuario_id')
					->references('id_usuario')
					->on('configuracion.sis_usua')
					->onDelete('cascade');

				$table->dateTime('fecha');
			});

			Schema::connection('pgsql')->create('finanzas.solicitudes_detalles', function ($table) {
				$table->increments('id');


				$table->string('descripcion');
				$table->decimal('estimado');
				$table->integer('solicitud_id')->unsigned();
				$table->foreign('solicitud_id')
					->references('id')
					->on('finanzas.solicitudes')
					->onDelete('cascade');
				$table->integer('partida_id')->unsigned();
				$table->foreign('partida_id')
					->references('id_partida')
					->on('finanzas.presup_par')
					->onDelete('cascade');

				//$table->smallInteger('estado')->nullable();
			});



			$tabla = 'finanzas.cajachica';
			Schema::connection('pgsql')->create($tabla, function ($table) {
				$table->increments('id');

				$table->string('descripcion');

				$table->decimal('monto_apertura')->nullable();
				$table->decimal('saldo')->nullable();
				$table->decimal('monto_minimo')->nullable();
				$table->decimal('monto_maximo_movimiento')->nullable();
				$table->dateTime('fecha_creacion');


				$table->integer('solicitud_id')->unsigned()->nullable();
				$table->foreign('solicitud_id')
					->references('id')
					->on('finanzas.solicitudes')
					->onDelete('cascade');

				$table->integer('area_id')->unsigned();
				$table->foreign('area_id')
					->references('id_area')
					->on('administracion.adm_area')
					->onDelete('cascade');
				$table->integer('moneda_id')->unsigned();
				$table->foreign('moneda_id')
					->references('id_moneda')
					->on('configuracion.sis_moneda')
					->onDelete('cascade');

				$table->integer('responsable_id')->unsigned();
				$table->foreign('responsable_id')
					->references('id_usuario')
					->on('configuracion.sis_usua')
					->onDelete('cascade');

				$table->integer('usuario_id')->unsigned();
				$table->foreign('usuario_id')
					->references('id_usuario')
					->on('configuracion.sis_usua')
					->onDelete('cascade');

				$table->integer('estado_id')->unsigned();
				$table->foreign('estado_id')
					->references('id_estado_doc')
					->on('administracion.adm_estado_doc')
					->onDelete('cascade');
			});

            Schema::connection('pgsql')->create('finanzas.sis_documentos_operacion', function ($table) {
                $table->increments('id');
                $table->string('codigo', 10);
                $table->string('descripcion');
                $table->integer('moneda_id')->unsigned()->nullable();
                $table->foreign('moneda_id')
                    ->references('id_moneda')
                    ->on('configuracion.sis_moneda')
					->onDelete('cascade');
                //$table->timestamps();
            });

            Schema::connection('pgsql')->create('finanzas.cajachica_movimientos', function($table) {
                $table->increments('id');
                $table->integer('cajachica_id')->unsigned();
                $table->foreign('cajachica_id')
                    ->references('id')
                    ->on('finanzas.cajachica')
					->onDelete('cascade');
                $table->datetime('fecha');
                $table->string('tipo_movimiento',1);
                $table->integer('doc_operacion_id'); //esto puede ser EI -> efectivo ILO, EM -> efectivo Moquegua
                $table->foreign('doc_operacion_id')
                    ->references('id')
                    ->on('finanzas.sis_documentos_operacion')
					->onDelete('cascade');
                $table->integer('vale_numero')->nullable();
                $table->string('data_pago')->nullable();/*
                $table->integer('proveedor_id')->unsigned()->nullable();
                $table->foreign('proveedor_id')
                    ->references('id_proveedor')
                    ->on('logistica.log_prove')
					->onDelete('cascade'); */
                $table->integer('moneda_id')->unsigned();
                $table->foreign('moneda_id')
                    ->references('id_moneda')
                    ->on('configuracion.sis_moneda')
					->onDelete('cascade');
                $table->decimal('tipo_cambio')->default(0);
                $table->decimal('importe')->default(0);
                $table->string('observaciones', 200);

				$table->timestamps();
            });

			Schema::connection('pgsql')->create('finanzas.cajachica_saldos', function($table) {
				$table->increments('id');
				$table->integer('cajachica_movimiento_id')->unsigned();
				$table->foreign('cajachica_movimiento_id')
					->references('id')
					->on('finanzas.cajachica_movimientos')
					->onDelete('cascade');
				$table->datetime('fecha');
				$table->decimal('inicial')->default(0);;
				$table->decimal('ingreso')->default(0);
				$table->decimal('egreso')->default(0);
				$table->decimal('saldo')->default(0);

				$table->timestamps();
			});


			$tabla = 'finanzas.cajachica_movimientos_vales';
			Schema::connection('pgsql')->create($tabla, function($table) {
				$table->increments('id');

				$table->string('codigo');

				$table->integer('cajachica_movimiento_id')->unsigned();
				$table->foreign('cajachica_movimiento_id')
					->references('id')
					->on('finanzas.cajachica_movimientos')
					->onDelete('cascade');

				$table->integer('emisor_id')->unsigned();
				$table->foreign('emisor_id')
					->references('id_usuario')
					->on('configuracion.sis_usua')
					->onDelete('cascade');

				$table->integer('receptor_id')->unsigned();
				$table->foreign('receptor_id')
					->references('id_usuario')
					->on('configuracion.sis_usua')
					->onDelete('cascade');

				$table->integer('estado_id')->unsigned()->nullable();
				$table->foreign('estado_id')
					->references('id_estado_doc')
					->on('administracion.adm_estado_doc')
					->onDelete('cascade');
				$table->timestamps();
			});

        }
        catch( \Error $e) {
            return $e;
        }


    }

    public function llenarDataInicial(){
    	/*
		$data = [
			[ 'codigo' => 'AR', 'descripcion' => 'Abono a rendir' ],
			[ 'codigo' => 'PS', 'descripcion' => 'Pago sustentado' ],
		];

		SolicitudesTipos::insert($data);

		$data = [
			[ 'codigo' => 'ACC', 'descripcion' => 'Apertura de caja chica', 'solicitudes_tipos_id' => 1 ],
			[ 'codigo' => 'RCC', 'descripcion' => 'Reposicion de caja chica', 'solicitudes_tipos_id' => 1 ],
			[ 'codigo' => 'Via', 'descripcion' => 'Viaticos', 'solicitudes_tipos_id' => 1 ],
			[ 'codigo' => 'ASS', 'descripcion' => 'Anticipo de sueldo por sustentar', 'solicitudes_tipos_id' => 1 ],
			[ 'codigo' => 'TC', 'descripcion' => 'Trubutos contables', 'solicitudes_tipos_id' => 2 ],
			[ 'codigo' => 'PR', 'descripcion' => 'Planilla de remuneraciones', 'solicitudes_tipos_id' => 2 ],
			[ 'codigo' => 'DevS', 'descripcion' => 'Devolucion de saldo', 'solicitudes_tipos_id' => 2 ],
			[ 'codigo' => 'PP', 'descripcion' => 'Planilla de proveedores', 'solicitudes_tipos_id' => 2 ],
		];

		SolicitudesSubTipos::insert($data);

		$data = [
			['codigo' => 'CH', 'descripcion' => 'CHEQUE', 'moneda_id' => 1],
			['codigo' => 'LE', 'descripcion' => 'LETRA DE CAMBIO', 'moneda_id' => 1],
			['codigo' => 'CR', 'descripcion' => 'COMPROBANTE DE RETENCION', 'moneda_id' => 1],
			['codigo' => 'MC', 'descripcion' => 'MULTI CANJE POR LETRAS', 'moneda_id' => 1],
			['codigo' => 'M2', 'descripcion' => 'MOV. AUTOMATICO CLIENTE', 'moneda_id' => 1],
			['codigo' => 'M1', 'descripcion' => 'MOV. AUTOMATICO PROVEEDOR', 'moneda_id' => 1],
			['codigo' => 'BD', 'descripcion' => 'DEPOSITO EN CUENTA', 'moneda_id' => 2],
			['codigo' => 'IT', 'descripcion' => 'I.T.F.', 'moneda_id' => 1],
			['codigo' => 'PO', 'descripcion' => 'PORTES Y MANTENIMIENTO', 'moneda_id' => 1],
			['codigo' => 'TF', 'descripcion' => 'TRANSFERENCIA BANCARIA', 'moneda_id' => 1],
			['codigo' => 'T1', 'descripcion' => 'TARJETA VISANET', 'moneda_id' => 1],
			['codigo' => 'T2', 'descripcion' => 'TARJETA MASTERCARD', 'moneda_id' => 1],
			['codigo' => 'ND', 'descripcion' => 'NOTA DE DEBITO', 'moneda_id' => 1],
			['codigo' => 'EE', 'descripcion' => 'EGRESOS EXTRAORDINARIOS', 'moneda_id' => 1],
			['codigo' => 'EM', 'descripcion' => 'EFECTIVO DISPONIBLE MOQUEGUA', 'moneda_id' => 1],
			['codigo' => 'EI', 'descripcion' => 'EFECTIVO DISPONIBLE ILO', 'moneda_id' => 1],
			['codigo' => 'EA', 'descripcion' => 'EFECTIVO DISPONIBLE AREQUIPA', 'moneda_id' => 1],
			['codigo' => 'PL', 'descripcion' => 'PLANILLA', 'moneda_id' => 1],
			['codigo' => 'CS', 'descripcion' => 'CTA CTE SOLES', 'moneda_id' => 1],
			['codigo' => 'PC', 'descripcion' => 'PRESTAMO CORTO PLAZO', 'moneda_id' => 1],
			['codigo' => 'PI', 'descripcion' => 'PRESTAMO INMUEBLE', 'moneda_id' => 1],
			['codigo' => 'PT', 'descripcion' => 'PRESTAMO TERCEROS', 'moneda_id' => 1],
			['codigo' => 'ID', 'descripcion' => 'INTERESES DEUDORES', 'moneda_id' => 1],
			['codigo' => 'CD', 'descripcion' => 'CTA CTE DOLARES', 'moneda_id' => 1],
			['codigo' => 'PP', 'descripcion' => 'PAGARE', 'moneda_id' => 1],
			['codigo' => 'PG', 'descripcion' => 'PERCEPCION IGV', 'moneda_id' => 1],
			['codigo' => 'FT', 'descripcion' => 'FLETE  EXTERIOR', 'moneda_id' => 1],
			['codigo' => 'RE', 'descripcion' => 'RENDIR CTA', 'moneda_id' => 1],
			['codigo' => 'IF', 'descripcion' => 'INTERES FINANCIERO', 'moneda_id' => 1],
			['codigo' => 'DC', 'descripcion' => 'DIFERENCIA DE CAMBIO', 'moneda_id' => 1],
			['codigo' => 'IG', 'descripcion' => 'INTERESES GANADOS', 'moneda_id' => 1],
			['codigo' => 'IR', 'descripcion' => 'IMPUESTO A LA RENTA', 'moneda_id' => 1],
			['codigo' => 'IE', 'descripcion' => 'ESSALUD', 'moneda_id' => 1],
			['codigo' => 'PF', 'descripcion' => 'PERDIDA INSTRUMENTOS FINANCIER', 'moneda_id' => 1],
			['codigo' => 'GF', 'descripcion' => 'GANANCIA FINANCIERA', 'moneda_id' => 1],
			['codigo' => 'CF', 'descripcion' => 'COMISION CARTAS FIANZAS', 'moneda_id' => 1],
			['codigo' => 'PJ', 'descripcion' => 'PRESTAMO JONATHAN', 'moneda_id' => 1],
			['codigo' => 'IM', 'descripcion' => 'INTERES MORATORIO', 'moneda_id' => 1],
			['codigo' => 'MM', 'descripcion' => 'MULTAS', 'moneda_id' => 1],
			['codigo' => 'ON', 'descripcion' => 'ONP', 'moneda_id' => 1],
			['codigo' => 'RQ', 'descripcion' => 'RENTA DE 5TA', 'moneda_id' => 1],
			['codigo' => 'TV', 'descripcion' => 'TARJETA VISA', 'moneda_id' => 1],
			['codigo' => 'PE', 'descripcion' => 'PENALIDADES', 'moneda_id' => 1],
			['codigo' => 'EL', 'descripcion' => 'EFECTIVO DISPONIBLE LIMA', 'moneda_id' => 1],
			['codigo' => 'FE', 'descripcion' => 'FED BCP', 'moneda_id' => 1],
			['codigo' => 'CB', 'descripcion' => 'CERTIFICADO BANCARIO', 'moneda_id' => 1],
			['codigo' => 'AL', 'descripcion' => 'ALQUILER', 'moneda_id' => 1],
			['codigo' => 'EP', 'descripcion' => 'EFECTIVO PROYECTOS', 'moneda_id' => 1],
			['codigo' => 'ED', 'descripcion' => 'DINERO ACCIONES ENTREGADO', 'moneda_id' => 1],
			['codigo' => 'GP', 'descripcion' => 'GASTOS PERSONALES', 'moneda_id' => 1],
			['codigo' => 'CP', 'descripcion' => 'CUENTA SOLES RECAUDADORA', 'moneda_id' => 1],
			['codigo' => 'IN', 'descripcion' => 'INCOBRABLES', 'moneda_id' => 1],
			['codigo' => 'CT', 'descripcion' => 'CONSTANCIA DE DETRACCION', 'moneda_id' => 1],
			['codigo' => 'DT', 'descripcion' => 'DETRACCION', 'moneda_id' => 1],
			['codigo' => 'IV', 'descripcion' => 'IGV', 'moneda_id' => 1],
			['codigo' => 'RC', 'descripcion' => 'RENTA CUARTA', 'moneda_id' => 1],
			['codigo' => 'SV', 'descripcion' => 'SEGURO VEHICULAR', 'moneda_id' => 1],
			['codigo' => 'BV', 'descripcion' => 'BANCO CONTINENTAL DOLARES', 'moneda_id' => 2,]
		];


		DocumentosOperacion::insert($data);

		*/

		$data = [
			['descripcion' => 'Corriente - Recaudadora', 'estado' => 1],
		];

		TipoCuenta::insert($data);

		$data = [
			[
				'id_contribuyente' => 1,
				'id_banco' => 1,
				'id_tipo_cuenta' => 1,
				'nro_cuenta' => '385-1462346-0-58',
				'nro_cuenta_interbancaria' => '002-385-001462346058-32',
				'estado' => 1,
				'fecha_registro' => today()
			],
			[
				'id_contribuyente' => 1,
				'id_banco' => 1,
				'id_tipo_cuenta' => 2,
				'nro_cuenta' => '385-1621537-0-48',
				'nro_cuenta_interbancaria' => '002-385-001621537048-33',
				'estado' => 1,
				'fecha_registro' => today()
			],
			[
				'id_contribuyente' => 1,
				'id_banco' => 1,
				'id_tipo_cuenta' => 1,
				'nro_cuenta' => '385-1556612-1-50',
				'nro_cuenta_interbancaria' => '002-385-001556612150-33',
				'estado' => 1,
				'fecha_registro' => today()
			],
			[
				'id_contribuyente' => 1,
				'id_banco' => 1,
				'id_tipo_cuenta' => 2,
				'nro_cuenta' => '385-2036320-1-86',
				'nro_cuenta_interbancaria' => '002-385-002036320186-32',
				'estado' => 1,
				'fecha_registro' => today()
			],
		];
		ContribuyenteCuenta::insert($data);
	}
}
