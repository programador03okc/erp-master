<?php

namespace App\Http\Controllers\Proyectos\Variables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TipoInsumoController extends Controller
{
    function view_tipo_insumo(){
        return view('proyectos/variables/tipo_insumo');
    }
    //tipos de insumos
    public function mostrar_tipos_insumos()
    {
        $data = DB::table('proyectos.proy_tp_insumo')
            ->select('proy_tp_insumo.*')
            ->where('estado',1)
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_tp_insumo($id)
    {
        $data = DB::table('proyectos.proy_tp_insumo')
            ->select('proy_tp_insumo.*')
            ->where([['proy_tp_insumo.id_tp_insumo', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_tp_insumo(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_tp_insumo = DB::table('proyectos.proy_tp_insumo')->insertGetId(
            [
                'codigo' => strtoupper($request->codigo),
                'descripcion' => strtoupper($request->descripcion),
                'fecha_registro' => $fecha,
                'estado' => 1
            ],
                'id_tp_insumo'
            );
        return response()->json($id_tp_insumo);
    }
    public function update_tp_insumo(Request $request)
    {
        $data = DB::table('proyectos.proy_tp_insumo')
            ->where('id_tp_insumo', $request->id_tp_insumo)
            ->update([
                'codigo' => $request->codigo,
                'descripcion' => strtoupper($request->descripcion)
            ]);

        return response()->json($data);
    }
    public function anular_tp_insumo($id)
    {
        $data = DB::table('proyectos.proy_tp_insumo')
            ->where('id_tp_insumo', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }
    public function buscar_tp_insumo($id)
    {
        $insumos = DB::table('proyectos.proy_insumo')
        ->select('proy_insumo.id_insumo')
            ->where([['proy_insumo.tp_insumo', '=', $id]])
            ->get()->count();
        return response()->json($insumos);
    }
}
