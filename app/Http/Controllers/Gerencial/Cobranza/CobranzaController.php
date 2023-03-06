<?php

namespace App\Http\Controllers\Gerencial\Cobranza;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Administracion\Empresa;
use App\Models\Administracion\Periodo;
use App\Models\Configuracion\Departamento;
use App\Models\Configuracion\Pais;
use App\models\Gerencial\Cobranza;
use App\models\Gerencial\CobranzaFase;
use App\Models\Gerencial\CobranzaView;
use App\models\Gerencial\EstadoDocumento;
use App\Models\Gerencial\RegistroCobranza;
use App\Models\Gerencial\RegistroCobranzaFase;
use App\models\Gerencial\Sector;
use App\models\Gerencial\TipoTramite;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CobranzaController extends Controller
{
    public function registro()
    {
        $sector = Sector::where('estado', 1)->get();
        $tipo_ramite = TipoTramite::where('estado', 1)->get();
        $empresas = Empresa::with('contribuyente')->where('estado', 1)->get();
        $periodo = Periodo::where('estado', 1)->get();
        $estado_documento = EstadoDocumento::where('estado', 1)->get();
        $pais = Pais::all();
        $departamento = Departamento::all();
        return view('gerencial.cobranza.registro', get_defined_vars());
    }

    public function listarRegistros(Request $request)
    {
        $data = CobranzaView::all();
        return DataTables::of($data)
        ->addColumn('atraso', function($data){
            return ($this->restar_fechas($data->fecha_recepcion, date('Y-m-d')) > 0) ? $this->restar_fechas($data->fecha_recepcion, date('Y-m-d')) : '0';
        })
        ->make(true);
    }

    public function test()
    {
        // $data = [];
        // $empresas = Empresa::with('contribuyente')->get();

        // foreach ($empresas as $key) {
        //     $data[] = $key->id_empresa;
        // }
        $data = RegistroCobranza::where('estado', 1)->get();
        $cont = 0;
        foreach ($data as $key) {
            $nuevo = new RegistroCobranzaFase();
                $nuevo->id_registro_cobranza = $key->id_registro_cobranza;
                $nuevo->fase = 'COMPROMISO';
                $nuevo->fecha = $key->fecha_registro;
            $nuevo->save();
            $cont++;
        }
        return response()->json($cont, 200);
    }

    public function scriptPeriodos()
    {
        $cont = 0;
        // $cobranza = Cobranza::all();

        // foreach ($cobranza as $key) {
        //     $periodos = DB::table('gerencial.periodo')->select('descripcion')->where('id_periodo', $key->id_periodo)->first();
        //     RegistroCobranza::where('id_cobranza_old', $key->id_cobranza)->update(['periodo' => $periodos->descripcion]);
        //     $cont++;
        // }
        $cobranza = RegistroCobranza::all();

        foreach ($cobranza as $key) {
            $periodos = Periodo::where('descripcion', $key->periodo)->first();
            RegistroCobranza::where('id_registro_cobranza', $key->id_registro_cobranza)->update(['id_periodo' => $periodos->id_periodo]);
            $cont++;
        }
        return response()->json($cont, 200);
    }

    public function restar_fechas($fi, $ff){
		$ini = strtotime($fi);
		$fin = strtotime($ff);
		$dif = $fin - $ini;
		$diasFalt = ((($dif / 60) / 60) / 24);
		return ceil($diasFalt);
	}
}
