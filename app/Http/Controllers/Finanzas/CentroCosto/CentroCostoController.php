<?php

namespace App\Http\Controllers\Finanzas\CentroCosto;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Presupuestos\CentroCosto;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CentroCostoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // $centro_costos = CentroCosto::orderBy('codigo','asc')->where('estado',1)->get();        
        // return view('centro_costos.index',compact('centro_costos'));
        return view('finanzas.centro_costos.index');
    }

    public function mostrarCentroCostos()
    {
        $centro_costos = CentroCosto::orderBy('codigo','asc')->where('estado',1)->get();
        
        return response()->json($centro_costos);
    }

    public function mostrarCentroCostosSegunGrupoUsuario()
    {
        $grupos = Auth::user()->getAllGrupo();
        
        foreach($grupos as $grupo){
            $idGrupoList[]=$grupo->id_grupo;
        }

        $centro_costos = CentroCosto::orderBy('codigo','asc')
        ->where('estado',1)
        ->whereIn('id_grupo',$idGrupoList)
        ->whereRaw('centro_costo.version = (select max("version") from finanzas.centro_costo)')
        ->get();
        
        return response()->json($centro_costos);
    }

    public function store()
    {
        $codigo = request('codigo');

        if (Str::contains($codigo,'.')){
            
            $cod_padre = substr($codigo, 0, (strlen($codigo)-3) );
            
            $cc_padre = CentroCosto::where('codigo',$cod_padre)->first();
            $id_padre = ($cc_padre!==null ? $cc_padre->id_centro_costo : null);
            
            $nivel = 2;
        } 
        else {
            $id_padre = null;
            $nivel = 1;
        }

        $data = CentroCosto::create([
            'codigo' => $codigo,
            'descripcion' => request('descripcion'),
            'id_padre' => $id_padre,
            'nivel' => $nivel,
            'estado' => 1
        ]);

        return response()->json($data);
    }

    public function update()
    {
        $cc = CentroCosto::findOrFail(request('id_centro_costo'));

        $codigo = request('codigo');

        if (Str::contains($codigo,'.')){
            
            $cod_padre = substr($codigo, 0, (strlen($codigo)-3) );
            
            $cc_padre = CentroCosto::where('codigo',$cod_padre)->first();
            $id_padre = ($cc_padre!==null ? $cc_padre->id_centro_costo : null);
            
            $nivel = 2;
        } 
        else {
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
