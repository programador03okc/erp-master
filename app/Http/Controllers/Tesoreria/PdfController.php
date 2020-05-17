<?php

namespace App\Http\Controllers\Tesoreria;

//use Barryvdh\DomPDF\PDF;
use App\Models\Tesoreria\CajaChica;
use App\Models\Tesoreria\CajaChicaMovimiento;
use App\Models\Tesoreria\CajaChicaMovimientoVales;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PdfController extends Controller
{
    //
	public function generateValeSalida($vale_id){

		$valeData = CajaChicaMovimientoVales::with(
			'cajachica_movimiento.cajachica',
			'emisor.trabajador.postulante.persona',
			'receptor.trabajador.postulante.persona'
		)->findOrFail($vale_id);

		//dd($valeData->toArray());

		//$a = today()->day;

		$data = [
			'logo_empresa' => resource_path('assets/images/empresa_' . $valeData->cajachica_movimiento->cajachica->empresa_id . '.png'),

			'vale' => $valeData,
			'monto_letras' => NumerosEnLetrasController::convertir($valeData->cajachica_movimiento->importe,'soles', true, 'Centimos')
		];

		/*return view('tesoreria.cajachica_movimientos.pdf-vale')->with(
			$data
		);*/

		//dd(resource_path('assets/images/logo_okc.png'));


		$pdf = PDF::loadView('tesoreria.cajachica_movimientos.pdf-vale', $data);

		$pdf->setPaper('A7', 'landscape');

		return $pdf->stream();

		return $pdf->download('itsolutionstuff.pdf');
	}

	public function generarHistorialCajaChica(Request $request, $cajachica_id){
		$especifico = $request->get('especifico');
		$f_ini = $request->get('f_ini');
		$f_fin = $request->get('f_fin');

		//dd($request->toArray());

		switch ($especifico){
			case 'mes_actual':
				$fecha_ini = now()->startOfMonth();
				$fecha_fin = now()->lastOfMonth();
				break;
			default:
				$fecha_ini = Carbon::parse($f_ini);
				$fecha_fin = Carbon::parse($f_fin);
				break;
		}

		//dd([$fecha_ini, $fecha_fin]);


		$movimientos = CajaChicaMovimiento::with(
			'cajachica',
			'moneda',
			'doc_operacion',
			'proveedor',
			'vale',
			'saldo'
		)->where('cajachica_id', $cajachica_id);

		//dd($movimientos->get()->toArray());


		if($fecha_ini !== null){
			if ($fecha_fin !== null){
				$movimientos = $movimientos->whereBetween('fecha', [$fecha_ini, $fecha_fin]);
			}
			else{
				$movimientos = $movimientos->whereDate('fecha', $fecha_ini);
			}
		}

		$mov = clone $movimientos;

		$egresos = $movimientos->where('tipo_movimiento', 'E')->get();

		$ingresos = $mov->where('tipo_movimiento', 'I')->get();

		$cajachica = CajaChica::findorFail($cajachica_id);


		$data = [
			'logo_empresa' => resource_path('assets/images/empresa_' . $cajachica->empresa_id . '.png'),
			'cajachica' => $cajachica,

			'fecha_ini' => $fecha_ini->format('d/m/Y'),
			'fecha_fin' => $fecha_fin->format('d/m/Y'),

			'ingresos' => $ingresos,
			'egresos' => $egresos,

			//'monto_letras' => NumerosEnLetrasController::convertir($valeData->cajachica_movimiento->importe,'soles', true, 'Centimos')
		];

		/*return view('tesoreria.cajachica_movimientos.pdf-vale')->with(
			$data
		);*/

		//dd(resource_path('assets/images/logo_okc.png'));


		$pdf = PDF::loadView('tesoreria.cajachica_movimientos.pdf-movimientos', $data);
		$pdf->getDomPDF()->set_option("enable_php", true);
		$pdf->getDomPDF()->set_option("enable_remote", true);
		$pdf->setPaper('A4', 'portrait');

		return $pdf->stream();
		//return $pdf->download('Resumen-CajaChica-'.$cajachica_id.'.pdf');

	}
}
