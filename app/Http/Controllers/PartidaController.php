<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presupuestos\Partida;
use App\Models\Presupuestos\Titulo;

class PartidaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store()
    {
        $partida = Partida::create([
                'id_presup' => request('id_presup'),
                'codigo' => request('codigo'),
                'descripcion' => strtoupper(request('descripcion')),
                'cod_padre' => request('cod_padre'),
                'importe_total' => request('importe_total'),
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ]);
        
        $this->actualizarTotales($partida);
        
        return response()->json($partida);
    }

    public function update()
    {
        $partida = Partida::findOrFail(request('id_partida'));
        $partida->update([
            'descripcion' => strtoupper(request('descripcion')),
            'importe_total' => request('importe_total'),
        ]);
        
        $this->actualizarTotales($partida);

        return response()->json($partida);
    }

    public function actualizarTotales($partida)
    {
        $suma = Partida::where([['id_presup','=',$partida->id_presup],
                                ['cod_padre','=',$partida->cod_padre],
                                ['estado','!=',7] ])
                                ->sum('importe_total');
        
        $titulo = Titulo::where([   ['id_presup','=',$partida->id_presup],
                                    ['codigo','=',$partida->cod_padre],
                                    ['estado','!=',7] ])
                                    ->first();
        
        $titulo->update([ 'total' => $suma ]);

        if ($titulo->cod_padre !== ''){

            $actualizar_padre = $titulo->cod_padre;

            while($actualizar_padre !== null){
                
                $suma_abu = Titulo::where([ ['id_presup','=',$partida->id_presup],
                                            ['cod_padre','=',$actualizar_padre],
                                            ['estado','!=',7]])
                                            ->sum('total');

                $abuelo = Titulo::where([   ['id_presup','=',$partida->id_presup],
                                            ['codigo','=',$actualizar_padre],
                                            ['estado','!=',7] ])
                                            ->first();

                $abuelo->update([ 'total' => $suma_abu ]);
                $actualizar_padre = ((isset($abuelo) && $abuelo->cod_padre !== '') 
                                        ? $abuelo->cod_padre : null);
            }
        }
    }

    public function destroy($id)
    {
        $partida = Partida::findOrFail($id);
        $partida->update(['estado' => 7]);

        return response()->json($partida);
    }
}
