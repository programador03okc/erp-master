<?php

namespace App\Http\Controllers;

use App\Models\Logistica\OrdenCompraDetalle;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function testDescripcionAdicionalOrden()
    {
        // SELECT DISTINCT id_orden_compra FROM logistica.log_det_ord_compra where descripcion_adicional like '%â¢%';
        $lista = OrdenCompraDetalle::whereIN('id_orden_compra', [3141,4305,4326,4303,4321,5067,4291,4316,4365,4324,4319,5066,4496,3140,4294,4307,4325,4495,4318,4373,1261,2059,4317,4886,4314,4332,4333,4369,4331,4323,4296,4772,4451,4638,2092,4292,4362,4885,4497,4769,4329,3142,4766,4770,4730,3212,4771,4767,4371,4370,4300,4301,4320,4334,4312,4374,4372,5047,4361,4302,4364,4494,4368,4363,4498,4322,4731,4366,4328,4330,2091,4293,3210,4311,4367,4315,2902,4768])->get();
        $data = [];

        foreach ($lista as $key) {
            $nombre = trim(strtoupper(str_replace('•', '', utf8_decode($this->quitar_tildes($key->descripcion_adicional)))));

            $update = OrdenCompraDetalle::find($key->id_detalle_orden);
                $update->descripcion_adicional = $nombre;
            $update->save();
            
            $data[] = ['antes' => $key->descripcion_adicional, 'despues' => $nombre];
        }
        return response()->json($data, 200);
    }

    public function quitar_tildes($cadena)
    {
		//Reemplazamos la A y a
		$cadena = str_replace(
			array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
			array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
			$cadena
		);
		//Reemplazamos la E y e
		$cadena = str_replace(
			array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
			array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
			$cadena
		);
		//Reemplazamos la I y i
		$cadena = str_replace(
			array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
			array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
			$cadena
		);
		//Reemplazamos la O y o
		$cadena = str_replace(
			array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
			array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
			$cadena
		);
		//Reemplazamos la U y u
		$cadena = str_replace(
			array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
			array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
			$cadena
		);
		//Reemplazamos la N, n, C y c
		$cadena = str_replace(
			array('Ñ', 'ñ', 'Ç', 'ç'),
			array('N', 'n', 'C', 'c'),
			$cadena
		);
		return $cadena;
    }
}