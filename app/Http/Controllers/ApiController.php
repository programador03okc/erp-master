<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tesoreria\TipoCambio;
use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function tipoCambioMasivo($desde, $hasta)
    {
        $fini = new DateTime($desde);
        $ffin = new DateTime($hasta);
        $compra = 0;
        $venta = 0;
        $data = [];

        for ($i = $fini; $i < $ffin; $i->modify('+1 day')) {
            $fecha = $i->format('Y-m-d');
            $query = DB::table('contabilidad.cont_tp_cambio')->where('fecha', $fecha);

            if ($query->count() == 0) {
                $apiQ = json_decode($this->consultaSunat('https://api.apis.net.pe/v1/tipo-cambio-sunat'));
                $compra = (float) $apiQ->compra;
                $venta = (float) $apiQ->venta;
                $promedio = ($compra + $venta) / 2;
    
                DB::table('contabilidad.cont_tp_cambio')->insertGetId([
                    'fecha'     => $fecha,
                    'moneda'    => 2,
                    'compra'    => $compra,
                    'venta'     => $venta,
                    'estado'    => 1,
                    'promedio'  => $promedio
                ], 'id_tp_cambio');
    
                $data[] = ['fecha' => $fecha, 'compra' => $compra, 'venta' => $venta];
            }
        }
        return response()->json($data, 200);
    }

    public function tipoCambioActual()
    {
        $fecha = date('Y-m-d');
        $compra = 0;
        $venta = 0;
        $query = DB::table('contabilidad.cont_tp_cambio')->where('fecha', $fecha);
        
        if ($query->count() > 0) {
            $rpta = 'exist';
            $compra = $query->latest()->first()->compra;
            $venta = $query->latest()->first()->venta;
        } else{
            $rpta = 'null';
            $apiQ = json_decode($this->consultaSunat('https://api.apis.net.pe/v1/tipo-cambio-sunat'));
            $compra = (float) $apiQ->compra;
            $venta = (float) $apiQ->venta;
        }
        return response()->json(array('response' => $rpta, 'compra' => $compra, 'venta' => $venta), 200);
    }

    public function consultaSunat($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curl, CURLOPT_HEADER, 0); 
        return curl_exec($curl);
    }
}
