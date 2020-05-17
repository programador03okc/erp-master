<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
// use Maatwebsite\Excel\Facades\Excel;

class ReporteLogisticaController extends Controller{
    
    public function reporte_excel_cuadro_comparativo_excel(Request $request){
		$especificaciones = $request->especificaciones;
		$array_proveedores = $request->lista_proveedores;
		$cabecera_cuadro_comp = $request->cabecera_cuadro_comp;
		$cantidad_cotizaciones = $request->cantidad_cotizaciones;
		$cotiza_cuadro_comp = $request->cotiza_cuadro_comp;
		$total_cotiza = $request->total_cotiza;
		$bunapro_cuadro_comp = $request->bunapro_cuadro_comp;
        
		$nombre_proveedores=''; 
		$propiedad_proveedores=''; 
		foreach($array_proveedores as $proveedor){
			$nombre_proveedores .= '<th colSpan="4" class="text-center">'.$proveedor[0].'<br><small>'.$proveedor[1].'</small></th>';
			$propiedad_proveedores .= '<th class="text-center">CANT.</th><th class="text-center">PRECIO</th><th class="text-center">SUBTOTAL</th><th class="text-center">FLETE</th>';
		}

		$cuerpo_cotiza='';

		function lista_items($cotiza){	
			$items_list='';
			foreach($cotiza as $item){
				$items_list .=
				'<td>'.$item['cantidad_cotizada'].'</td>
				<td>'.$item['precio_cotizado'].'</td>
				<td>'.$item['subtotal'].'</td>
				<td>'.$item['flete'].'</td>';
			}
			return $items_list;
		}

		foreach($cotiza_cuadro_comp as $cotiza){
		 $cuerpo_cotiza .= '<tr>
								<td>'.$cotiza['codigo'].'</td>
								<td>'.$cotiza['descripcion'].'</td>
								<td>'.$cotiza['unidad_medida'].'</td>
								<td>'.$cotiza['cantidad_solicitada'].'</td>
								<td>'.$cotiza['precio_referencial'].'</td>'
								.lista_items($cotiza['items']).
							'</tr>';
		}
		$totalcotizacion='';
		foreach($total_cotiza as $totalcotiza){
			$totalcotizacion .= '<td  class="text-uppercase text-center font-weight-bold" colSpan="4" key={ index }>S/.'.$totalcotiza['total_subtotal'].'</td>';
		}	
		$especificacion_list='';
		foreach($especificaciones as $especificacion){
			$especificacion_list .= '<tr><td>'.$especificacion[0].'</td>
											<td>'.$especificacion[1].'</td>
											<td>'.$especificacion[2].'</td>
											<td>'.$especificacion[3].'</td>
											<td>'.$especificacion[4].'</td>
											<td>'.$especificacion[5].'</td>
											<td>'.$especificacion[6].'</td>
											<td>'.$especificacion[7].'</td>
										</tr>';
		}	
		$buenapro_list='';
		foreach($bunapro_cuadro_comp as $buenapro){
			$buenapro_list .= '<tr>
									<td>'.$buenapro['codigo_item'].'</td>
									<td>'.$buenapro['fecha_registro'].'</td>
									<td>'."S/.".$buenapro['precio'].'</td>
									<td colSpan="2">'.$buenapro['tipo_doc']." ".$buenapro['nro_doc'].'</td>
									<td colSpan="2">'.$buenapro['proveedor'].'</td>
								</tr>
								<tr>
									<td colSpan="7">Se otorga la Buena Pro a '.$buenapro["proveedor"]." ".$buenapro["detalle"].'</td>
								</tr>';
		}	


		$html = ' 
		<caption>CUADRO COMPARATIVO'.$cabecera_cuadro_comp['codigo_cuadro_comp'].'</caption>
		<thead>
		<tr>
			<th class="text-uppercase text-center align-middle" width="10%" rowSpan="3">Item</th>
			<th class="text-uppercase text-center align-middle" width="30%" rowSpan="3">Descripcion</th>
			<th class="text-uppercase text-center align-middle" width="10%" rowSpan="3">Und.</th>
			<th class="text-uppercase text-center align-middle" width="10%" rowSpan="3">Cant.</th>
			<th class="text-uppercase text-center align-middle" width="10%" rowSpan="3">Precio Ref.</th>
			<th class="text-uppercase text-center align-middle" width="40%" colSpan="'.($cantidad_cotizaciones * 4).'" >Proveedores</th>
		</tr>
		<tr>'.$nombre_proveedores.'</tr>
		<tr>'.$propiedad_proveedores.'</tr>
		</thead>
		';

		$html .= '
		<tbody>
			 '.$cuerpo_cotiza.'
			 <tr class="bg-light"><td class="text-uppercase text-center font-weight-bold" colSpan="5">TOTAL COTIZACIÓN</td>'.$totalcotizacion.'</tr>


			 <tr><td colSpan="13"></td></tr>
			 
			 <tr><td colSpan="13">ESPECIFICACION DE COMPRA</td></tr>
			 <tr>
				<th class="text-uppercase" width="20%">Proveedor</th>
				<th class="text-uppercase" width="5%">Condición de Pago</th>
				<th class="text-uppercase" width="5%">Forma Pago Credito</th>
				<th class="text-uppercase" width="5%">Plazo Entrega</th>
				<th class="text-uppercase" width="10%">Fecha Entrega</th>
				<th class="text-uppercase" width="20%">Lugar Entrega</th>
				<th class="text-uppercase" width="20%">Detalle Envio</th>
				<th class="text-uppercase" width="20%">Observación</th>
			 </tr>

			 '.$especificacion_list.'

			 <tr><td colSpan="13"></td></tr>
			 <tr><td colSpan="13">BUENA PRO</td></tr>
			 <tr><td colSpan="13">Criterios de evalucacion para otorgar la Buena Pro:BIENES: Propuesta Economica (80%), Plazo de entrega (10%), Garantias comerciales (10%),SERVICIOS: Propuesta Economica (85%), Plazo de cumplimiento (10%), Experiencia (5%),No cumple con lo requierido en TR/ET,Se aplica una bonificacion de 10% sobre el total a proveedores locales en caso de de empate</td></tr>
			'.$buenapro_list.'
 


		</tbody>';

		return response()->json($html);
 
    }
}