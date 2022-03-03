<?php

namespace App\Http\Controllers\Finanzas\CentroCosto;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Presupuestos\CentroCosto;
use App\Http\Controllers\Controller;
use App\Models\Administracion\Periodo;
use App\Models\Configuracion\Grupo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CentroCostoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $grupos = Grupo::all();
        $periodos = Periodo::all();
        return view('finanzas.centro_costos.index', compact('grupos', 'periodos'));
    }

    public function mostrarCentroCostos()
    {
        $anio = date('Y', strtotime(date('Y-m-d')));
        $centro_costos = CentroCosto::orderBy('codigo', 'asc')
            ->where('estado', 1)
            ->where('periodo', $anio)
            ->get();

        return response()->json($centro_costos);
    }

    public function mostrarCentroCostosSegunGrupoUsuario()
    {
        $grupos = Auth::user()->getAllGrupo();

        foreach ($grupos as $grupo) {
            $idGrupoList[] = $grupo->id_grupo;
        }

        // $centro_costos = CentroCosto::orderBy('codigo','asc')
        // ->where('estado',1)
        // ->whereIn('id_grupo',$idGrupoList)
        // ->whereRaw('centro_costo.version = (select max("version") from finanzas.centro_costo)')
        // ->select(['*'])
        // ->get();
        $centroCostos = CentroCosto::orderBy('codigo', 'asc')
            ->where('estado', 1)
            ->whereIn('id_grupo', $idGrupoList)
            ->whereRaw('centro_costo.version = (select max("version") from finanzas.centro_costo)')
            ->select(['*', DB::raw("CASE WHEN (SELECT cc.codigo FROM finanzas.centro_costo AS cc WHERE centro_costo.codigo!=cc.codigo AND cc.codigo LIKE centro_costo.codigo || '.%' 
        AND cc.version=centro_costo.version LIMIT 1) IS NULL THEN true ELSE false END AS seleccionable")])->get();

        return response()->json($centroCostos);
    }

    public function guardarCentroCosto(Request $request)
    {
        $codigo = $request->codigo;

        if (Str::contains($codigo, '.')) {
            $cod_padre = substr($codigo, 0, (strlen($codigo) - 3));
            $cc_padre = CentroCosto::where('codigo', $cod_padre)->first();
            $id_padre = ($cc_padre !== null ? $cc_padre->id_centro_costo : null);

            $nivel = 2;
        } else {
            $id_padre = null;
            $nivel = 1;
        }

        $data = CentroCosto::create([
            'codigo' => $codigo,
            'descripcion' => $request->descripcion,
            'id_grupo' => $request->id_grupo,
            'periodo' => $request->periodo,
            'id_padre' => $id_padre,
            'nivel' => $nivel,
            'version' => 1,
            'estado' => 1,
            'fecha_registro' => new Carbon(),
        ]);

        return response()->json($data);
    }

    public function update()
    {
        $cc = CentroCosto::findOrFail(request('id_centro_costo'));

        $codigo = request('codigo');

        if (Str::contains($codigo, '.')) {

            $cod_padre = substr($codigo, 0, (strlen($codigo) - 3));

            $cc_padre = CentroCosto::where('codigo', $cod_padre)->first();
            $id_padre = ($cc_padre !== null ? $cc_padre->id_centro_costo : null);

            $nivel = 2;
        } else {
            $id_padre = null;
            $nivel = 1;
        }

        $cc->update([
            'codigo' => $codigo,
            'descripcion' => request('descripcion'),
            'id_padre' => $id_padre,
            'nivel' => $nivel
        ]);

        return response()->json($cc);
    }

    public function destroy($id)
    {
        $cc = CentroCosto::findOrFail($id);
        $cc->update(['estado' => 7]);

        return response()->json($cc);
    }
}
