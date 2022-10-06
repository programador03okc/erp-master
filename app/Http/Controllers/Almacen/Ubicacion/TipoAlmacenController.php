<?php

namespace App\Http\Controllers\Almacen\Ubicacion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Configuracion\AccesosUsuarios;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TipoAlmacenController extends Controller
{
    function view_tipo_almacen(){
        $array_accesos_botonera=array();
        $accesos_botonera = AccesosUsuarios::where('accesos_usuarios.estado','=',1)
        ->select('accesos.*')
        ->join('configuracion.accesos','accesos.id_acceso','=','accesos_usuarios.id_acceso')
        ->where('accesos_usuarios.id_usuario',Auth::user()->id_usuario)
        ->where('accesos_usuarios.id_modulo',66)
        ->where('accesos_usuarios.id_padre',14)
        ->get();
        foreach ($accesos_botonera as $key => $value) {
            $value->accesos;
            array_push($array_accesos_botonera,$value->accesos->accesos_grupo);
        }
        $modulo='almacen';
        return view('almacen/variables/tipo_almacen',compact('modulo','array_accesos_botonera'));
    }

    /* Tipo Almacen */
    public function mostrar_tipo_almacen(){
        $data = DB::table('almacen.alm_tp_almacen')->orderBy('id_tipo_almacen')->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_tipo_almacenes($id){
        $data = DB::table('almacen.alm_tp_almacen')->orderBy('id_tipo_almacen')
            ->where([['alm_tp_almacen.id_tipo_almacen', '=', $id]])->get();
        return response()->json($data);
    }

    public function guardar_tipo_almacen(Request $request){
        $id_almacen = DB::table('almacen.alm_tp_almacen')->insertGetId(
            [
                'descripcion' => $request->descripcion,
                'estado' => 1
            ],
                'id_tipo_almacen'
            );
        return response()->json($id_almacen);
    }

    public function update_tipo_almacen(Request $request){
        $data = DB::table('almacen.alm_tp_almacen')->where('id_tipo_almacen', $request->id_tipo_almacen)
            ->update([
                'descripcion' => $request->descripcion,
                'estado' => 1
            ]);
        return response()->json($data);
    }

    public function anular_tipo_almacen($id){
        $data = DB::table('almacen.alm_tp_almacen')->where('id_tipo_almacen', $id)
            ->update([
                'estado' => 7
            ]);
        return response()->json($data);
    }

}
