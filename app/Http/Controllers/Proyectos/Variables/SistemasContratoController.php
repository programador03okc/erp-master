<?php

namespace App\Http\Controllers\Proyectos\Variables;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SistemasContratoController extends Controller
{
    function view_sis_contrato(){
        return view('proyectos/variables/sis_contrato');
    }
    //sistemas de contrato
    public function mostrar_sis_contratos()
    {
        $data = DB::table('proyectos.proy_sis_contrato')
        ->select('proy_sis_contrato.*')
        ->where('estado',1)
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_sis_contrato($id)
    {
        $data = DB::table('proyectos.proy_sis_contrato')
        ->select('proy_sis_contrato.*')
            ->where([['id_sis_contrato', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_sis_contrato(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $data = DB::table('proyectos.proy_sis_contrato')->insertGetId(
            [
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'fecha_registro' => $fecha,
                'estado' => 1
            ],
                'id_sis_contrato'
            );
            
        return response()->json($data);
    }
    public function update_sis_contrato(Request $request)
    {
        $data = DB::table('proyectos.proy_sis_contrato')
            ->where('id_sis_contrato', $request->id_sis_contrato)
            ->update([
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion
            ]);

        return response()->json($data);
    }
    public function anular_sis_contrato(Request $request, $id)
    {
        $data = DB::table('proyectos.proy_sis_contrato')
            ->where('id_sis_contrato', $id)
            ->update([ 'estado' => 7 ]);

        return response()->json($data);
    }

}
