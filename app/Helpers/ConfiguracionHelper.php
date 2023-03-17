<?php

namespace App\Helpers;

use App\Models\Comercial\Cliente;
use App\Models\Logistica\Proveedor;
use Carbon\Carbon;

class ConfiguracionHelper
{
	/*
	 * Función para generar el correlativo de los documentos y/o códigos para identificar un registro
	 * serie = texto inicial definido
	 * separador = caracter que separa el texto inicial del correlativo
	 * cantidad = cantidad de numeros para el correlativo
	 * modulo = nombre del módulo del cual se obtendrá el total de registros actual
	 * periodo = valor que define si necesita el valor del periodo/año dentro del código (SI/NO)
	 * valorPeriodo = cuando se necesite del periodo se deberá colocar PERIODO = SI, seguido de un valor referente al anño (2023) en caso que sea 0 el año se considerará al año en curso
	 * tipo =  valor que definirá el tipo de texto inicial cuando sea autogenerado por otra función
	 * id_empresa = ID necesario para identificar una empresa en particular
	 * id_grupo = ID necesario para identificar un grupo en particular
	 */
    public static function generarCodigo($serie = '', $separador = '', $cantidad = 0, $modulo, $periodo = 'NO', $valorPeriodo = 0, $tipo = 1, $id_empresa = 0, $id_grupo = 0)
    {
		$numero = ConfiguracionHelper::contadorModular($modulo);
        $correlativo = ConfiguracionHelper::leftZero($cantidad, $numero);
		$inicial = '';
		switch ($tipo) {
			case 1:
				$inicial = $serie;
			break;
			// case 2:
			// 	$inicial = ConfiguracionHelper::codigoEmpresa($id_empresa);
			// break;
			// case 3:
			// 	$inicial = ConfiguracionHelper::codigoGrupo($id_grupo);
			// break;
			// case 4:
			// 	$inicial = $serie.ConfiguracionHelper::codigoGrupo($id_grupo);
			// break;
		}
		$anio = ($periodo == 'NO') ? '' : ConfiguracionHelper::generarCodigoAnual($valorPeriodo);
        return $inicial.$anio.$separador.$correlativo;
    }

	/*
	 * Función para autogenerar un correlativo de números donde se rellena de ceros a la izquierda
	 */
    public static function leftZero($lenght, $number){
		$nLen = strlen($number);
		$zeros = '';
		for($i = 0; $i < ($lenght - $nLen); $i++){
			$zeros = $zeros.'0';
		}
		return $zeros.$number;
	}

	/*
	 * Función para extraer el último registro a nivel de módulos (tablas)
	 */
	public static function contadorModular($modulo)
	{
		$correlativo = 0;
		switch ($modulo) {
            case 'cliente':
				$correlativo = Cliente::count() + 1; //El contador inicia en 1
			break;
            case 'clienteCodigo':
				$correlativo = Cliente::where('codigo','!=',null)->count() + 1; //El contador inicia en 1
			break;
            case 'proveedores':
				$correlativo = Proveedor::count() + 1; //El contador inicia en 1
			break;
            case 'proveedoresCodigo':
				$correlativo = Proveedor::where('codigo','!=',null)->count() + 1; //El contador inicia en 1
			break;
		}
		return $correlativo;
	}

	/*
	 * Función que extrae un string del año en ejecución o periodo seleccionado (2 últimos dígitos)
	 * Si el año tiene valor 0 la función tomará el año en curso
	 * Si el año tiene un valor (Ejm: 20233) ese será el año a ejecutar
	 */
	public static function generarCodigoAnual($anio)
	{
		$valor = ($anio > 0) ? $anio : Carbon::now()->format('Y');
		return substr($valor, 2);
	}

	/*
	 * Función para extraer la abreviatura de la empresa (Tabla: administracion::empresas)
	 */
	// public static function codigoEmpresa($idEmpresa)
	// {
	// 	return Empresa::find($idEmpresa)->abreviatura;
	// }

	/*
	 * Función para generar un texto único según el grupo al que pertenece (Tabla: administracion::grupos)
	 */
	// public static function codigoGrupo($id_grupo)
	// {
	// 	$codigoGrupo = '';
	// 	$grupo=Grupo::find($id_grupo);
	// 	if(isset($grupo)){
	// 		$porciones = explode(" ", $grupo->descripcion);
	// 		foreach ($porciones as $key => $porcion) {
	// 			$codigoGrupo .=$porcion[0];
	// 		}
	// 	}

	// 	return $codigoGrupo;
	// }

	// public static function periodoActivo($tipo)
	// {
	// 	$periodos = Periodo::orderBy('descripcion', 'desc')->first();
	// 	return ($tipo == 1) ? $periodos->id : $periodos->descripcion;
	// }
}
