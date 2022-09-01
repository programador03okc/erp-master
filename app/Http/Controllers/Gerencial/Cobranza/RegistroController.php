<?php

namespace App\Http\Controllers\Gerencial\Cobranza;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Gerencial\AreaResponsable;
use App\models\Gerencial\CobanzaFase;
use App\models\Gerencial\Cobranza;
use App\models\Gerencial\Empresa;
use App\models\Gerencial\EstadoDocumento;
use Exception;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RegistroController extends Controller
{
    //
    public function registro()
    {
        # code...
        return view('gerencial/cobranza/registro');
    }

    public function prueba()
    {
        $cobranza = Cobranza::with(['cobranzaFase' => function ($query) {
            $query->orderBy('id_cobranza', 'desc')->first(); // latest()
        }])->limit(20)->get();
        // return $cobranza[0];

        $data = Cobranza::select('*')->orderBy('id_cobranza', 'desc')->limit(50);
        return DataTables::of($data)
        ->addColumn('empresa', function($data){ return $data->empresa->nombre; })
        ->addColumn('fase', function($data) {
            $fase = CobanzaFase::where('id_cobranza', $data->id_cobranza)->orderBy('id_fase', 'desc')->first();
            if ($fase) {
                return $fase->fase;
            } else {
                return '';
            }
        })->make(true);
        // return response()->json($cobranza, 200);
    }

    public function listarRegistros()
    {
        $data = Cobranza::select('*')->orderBy('id_cobranza', 'desc');
        return DataTables::of($data)
        ->addColumn('empresa', function($data){ return $data->empresa->nombre; })
        ->addColumn('cliente', function($data){ return $data->cliente->nombre; })
        ->addColumn('atraso', function($data){
            return ($this->restar_fechas($data->fecha_recepcion, date('Y-m-d')) > 0) ? $this->restar_fechas($data->fecha_recepcion, date('Y-m-d')) : '0';
         })
        ->addColumn('moneda', function($data){ return ($data->moneda == 1) ? 'S/' : 'US $'; })
        ->addColumn('importe', function($data){ return number_format($data->importe, 2); })
        ->addColumn('estado', function($data){
            $estado_documento = EstadoDocumento::where('estado',1)->get();
            return [$data->estadoDocumento->id_estado_doc,$estado_documento];
        })
        ->addColumn('area', function($data){
            $area_responsable = AreaResponsable::where('estado',1)->get();
            return [$data->areaResponsable->id_area, $area_responsable];
         })
        ->addColumn('fase', function($data) {
            $fase = CobanzaFase::where('id_cobranza', $data->id_cobranza)->orderBy('id_fase', 'desc')->first();
            return ($fase?$fase->fase[0] : '-');
        })->make(true);
    }
    public function restar_fechas($fi, $ff){
		$ini = strtotime($fi);
		$fin = strtotime($ff);
		$dif = $fin - $ini;
		$diasFalt = ((($dif / 60) / 60) / 24);
		return ceil($diasFalt);
	}
}
