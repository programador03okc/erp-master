<?php

namespace App\Http\Controllers\Almacen\Ubicacion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AlmacenController as GenericoAlmacenController;

class AlmacenController extends Controller
{
    function view_almacenes(){
        $sedes = GenericoAlmacenController::mostrar_sedes_cbo();
        $tipos = GenericoAlmacenController::mostrar_tp_almacen_cbo();
        return view('almacen/ubicacion/almacenes', compact('sedes','tipos'));
    }
    
    public static function mostrar_almacenes_cbo(){
        $data = DB::table('almacen.alm_almacen')
            ->select('alm_almacen.id_almacen','alm_almacen.codigo','alm_almacen.descripcion')
            ->where([['alm_almacen.estado', '=', 1]])
                ->orderBy('codigo')
                ->get();
        return $data;
    }
    
    /*Almacen*/
    public function mostrar_almacenes()
    {
        $data = DB::table('almacen.alm_almacen')
            ->select('alm_almacen.*', 'sis_sede.id_empresa', 'sis_sede.descripcion as sede_descripcion',
            'alm_tp_almacen.descripcion as tp_almacen')
            ->leftjoin('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
            ->join('almacen.alm_tp_almacen','alm_tp_almacen.id_tipo_almacen','=','alm_almacen.id_tipo_almacen')
            ->where([['alm_almacen.estado', '=', 1]])
                ->orderBy('id_empresa', 'asc')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_almacen($id)
    {
        $data = DB::table('almacen.alm_almacen')
        ->select('alm_almacen.*', 'sis_sede.descripcion as sede_descripcion',
        DB::raw("(ubi_dis.descripcion) || ' ' || (ubi_prov.descripcion) || ' ' || (ubi_dpto.descripcion) as name_ubigeo"))
        ->leftjoin('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
        ->leftjoin('configuracion.ubi_dis','ubi_dis.id_dis','=','alm_almacen.ubigeo')
        ->leftjoin('configuracion.ubi_prov','ubi_prov.id_prov','=','ubi_dis.id_prov')
        ->leftjoin('configuracion.ubi_dpto','ubi_dpto.id_dpto','=','ubi_prov.id_dpto')
        ->where([['alm_almacen.id_almacen', '=', $id]])
            ->get();
        return response()->json($data);
    }

    public function guardar_almacen(Request $request)
    {
        $id_usuario = Auth::user()->id_usuario;
        $fecha = date('Y-m-d H:i:s');
        $id_almacen = DB::table('almacen.alm_almacen')->insertGetId(
            [
                'id_sede' => $request->id_sede,
                'descripcion' => $request->descripcion,
                'ubicacion' => $request->ubicacion,
                'id_tipo_almacen' => $request->id_tipo_almacen,
                'codigo' => $request->codigo,
                'ubigeo' => $request->ubigeo,
                'estado' => 1,
                'registrado_por' => $id_usuario,
                'fecha_registro' => $fecha
            ],
                'id_almacen'
            );
        return response()->json($id_almacen);
    }

    public function update_almacen(Request $request)
    {
        $id_usuario = Auth::user()->id_usuario;
        $fecha = date('Y-m-d H:i:s');
        $data = DB::table('almacen.alm_almacen')->where('id_almacen', $request->id_almacen)
            ->update([
                'codigo' => $request->codigo,
                'id_sede' => $request->id_sede,
                'descripcion' => $request->descripcion,
                'ubicacion' => $request->ubicacion,
                'id_tipo_almacen' => $request->id_tipo_almacen,
                'ubigeo' => $request->ubigeo,
                'registrado_por' => $id_usuario,
                'fecha_registro' => $fecha
            ]);
        return response()->json($data);
    }

    public function anular_almacen(Request $request, $id)
    {
        $data = DB::table('almacen.alm_almacen')->where('id_almacen', $id)
            ->update([
                'estado' => 7
            ]);
        return response()->json($data);
    }

    public function listar_ubigeos(){
        $data = DB::table('configuracion.ubi_dis')
        ->select('ubi_dis.*','ubi_prov.descripcion as provincia','ubi_dpto.descripcion as departamento')
        ->join('configuracion.ubi_prov','ubi_prov.id_prov','=','ubi_dis.id_prov')
        ->join('configuracion.ubi_dpto','ubi_dpto.id_dpto','=','ubi_prov.id_dpto')
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function cargar_almacenes($id_sede){
        $data = DB::table('almacen.alm_almacen')
        ->select('alm_almacen.*','sis_sede.descripcion as sede_descripcion',
        'adm_empresa.id_empresa','alm_tp_almacen.descripcion as tp_almacen')
            ->leftjoin('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
            ->leftjoin('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
            ->join('almacen.alm_tp_almacen','alm_tp_almacen.id_tipo_almacen','=','alm_almacen.id_tipo_almacen')
            ->where([['alm_almacen.estado', '=', 1],
                     ['alm_almacen.id_sede','=',$id_sede]])
                ->orderBy('codigo')
                ->get();
        return $data;
    }
}
