<?php

namespace App\Http\Controllers\Almacen\Ubicacion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AlmacenController as GenericoAlmacenController;
use App\models\Configuracion\AccesosUsuarios;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosicionController extends Controller
{
    function view_ubicacion(){
        $almacenes = GenericoAlmacenController::mostrar_almacenes_cbo();
        $estantes = $this->mostrar_estantes_cbo();
        $niveles = $this->mostrar_niveles_cbo();
        $array_accesos_botonera=array();
        $accesos_botonera = AccesosUsuarios::where('accesos_usuarios.estado','=',1)
        ->select('accesos.*')
        ->join('configuracion.accesos','accesos.id_acceso','=','accesos_usuarios.id_acceso')
        ->where('accesos_usuarios.id_usuario',Auth::user()->id_usuario)
        ->where('accesos_usuarios.id_modulo',68)
        ->where('accesos_usuarios.id_padre',14)
        ->get();
        foreach ($accesos_botonera as $key => $value) {
            $value->accesos;
            array_push($array_accesos_botonera,$value->accesos->accesos_grupo);
        }
        $modulo='almacen';
        return view('almacen/ubicacion/ubicacion', compact('almacenes','estantes','niveles','array_accesos_botonera','modulo'));
    }
    public function mostrar_estantes_cbo(){
        $data = DB::table('almacen.alm_ubi_estante')
            ->select('alm_ubi_estante.id_estante','alm_ubi_estante.codigo')
            ->where([['alm_ubi_estante.estado', '=', 1]])
                ->orderBy('codigo')
                ->get();
        return $data;
    }
    public function mostrar_niveles_cbo()
    {
        $data = DB::table('almacen.alm_ubi_nivel')
            ->select('alm_ubi_nivel.id_nivel','alm_ubi_nivel.codigo')
            ->where([['alm_ubi_nivel.estado', '=', 1]])
                ->orderBy('codigo')
                ->get();
        return $data;
    }
    /* Estante */
    public function mostrar_estantes()
    {
        $data = DB::table('almacen.alm_ubi_estante')
            ->select('alm_ubi_estante.*','alm_almacen.id_almacen',
            'alm_almacen.descripcion as alm_descripcion')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
                ->orderBy('codigo')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_estantes_almacen($id)
    {
        $data = DB::table('almacen.alm_ubi_estante')
            ->select('alm_ubi_estante.*', 'alm_almacen.descripcion as alm_descripcion')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_estante.id_almacen', '=', $id]])
                ->orderBy('codigo')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_estante($id)
    {
        $data = DB::table('almacen.alm_ubi_estante')
            ->select('alm_ubi_estante.*')
            ->where([['alm_ubi_estante.id_estante', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function guardar_estante(Request $request){
        $id_almacen = DB::table('almacen.alm_ubi_estante')->insertGetId(
            [
                'id_almacen' => $request->id_almacen,
                'codigo' => $request->codigo,
                'estado' => 1
            ],
                'id_estante'
            );
        return response()->json($id_almacen);
    }
    public function guardar_estantes(Request $request){
        $id_almacen = $request->id_almacen;
        $desde = $request->desde;
        $hasta = $request->hasta;

        $almacen = DB::table('almacen.alm_almacen')
        ->where('id_almacen',$request->id_almacen)
        ->first();

        for ($i=$desde; $i<=$hasta; $i++) {
            $codigo = $almacen->codigo."-".GenericoAlmacenController::leftZero(2,$i);

            $exist = DB::table('almacen.alm_ubi_estante')
                ->where('codigo',$codigo)->get()->count();

            if ($exist === 0){
                $data = DB::table('almacen.alm_ubi_estante')->insertGetId([
                    'id_almacen' => $id_almacen,
                    'codigo' => $codigo,
                    'estado' => 1
                ],
                    'id_estante'
                );
            }
        }
        return response()->json($data);
    }
    public function update_estante(Request $request){
        $data = DB::table('almacen.alm_ubi_estante')
            ->where([['alm_ubi_estante.id_estante','=',$request->id_estante]])
            ->update([
                'id_almacen' => $request->id_almacen,
                'codigo' => $request->codigo
            ]);
        return response()->json($data);
    }
    public function anular_estante(Request $request, $id){
        $data = DB::table('almacen.alm_ubi_estante')
            ->where([['alm_ubi_estante.id_estante','=',$id]])
            ->update(['estado' => 7]);
        return response()->json($data);
    }
    public function revisar_estante($id){
        $data = DB::table('almacen.alm_ubi_nivel')
            ->where([['alm_ubi_nivel.id_estante','=',$id],
                    ['estado','=', 1]])
            ->get()->count();
        return response()->json($data);
    }
/* Nivel */
    public function mostrar_niveles()
    {
        $data = DB::table('almacen.alm_ubi_nivel')
            ->select('alm_ubi_nivel.*','alm_almacen.id_almacen',
            'alm_almacen.descripcion as alm_descripcion',
            'alm_ubi_estante.id_estante','alm_ubi_estante.codigo as cod_estante')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
                ->orderBy('codigo')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_niveles_estante($id)
    {
        $data = DB::table('almacen.alm_ubi_nivel')
            ->select('alm_ubi_nivel.*', 'alm_almacen.descripcion as alm_descripcion',
                'alm_ubi_estante.codigo as cod_estante')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_nivel.id_estante', '=', $id]])
            ->orderBy('codigo')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_nivel($id)
    {
        $data = DB::table('almacen.alm_ubi_nivel')
            ->select('alm_ubi_nivel.*','alm_almacen.id_almacen',
            'alm_ubi_estante.id_estante')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_nivel.id_nivel', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function guardar_nivel(Request $request){
        $id_almacen = DB::table('almacen.alm_ubi_nivel')->insertGetId(
            [
                'id_estante' => $request->id_estante,
                'codigo' => $request->codigo,
                'estado' => 1
            ],
                'id_nivel'
            );
        return response()->json($id_almacen);
    }
    public function guardar_niveles(Request $request){
        $abc = [0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',9=>'J',10=>'K',11=>'L',12=>'M',13=>'N',14=>'O',15=>'P',16=>'Q',17=>'R',18=>'S',19=>'T',20=>'U',21=>'V',22=>'W',23=>'X',24=>'Y',25=>'Z'];

        $desde = array_search(strtoupper($request->desde),$abc);
        $hasta = array_search(strtoupper($request->hasta),$abc);
        $i = 0;

        for ($i=$desde; $i<=$hasta; $i++) {
            $codigo = $request->cod_estante."-".$abc[$i];

            $data = DB::table('almacen.alm_ubi_nivel')->insertGetId([
                'id_estante' => $request->id_estante,
                'codigo' => $codigo,
                'estado' => 1
            ],
                'id_nivel'
            );
        }
        return response()->json($data);
    }
    public function update_nivel(Request $request){
        $data = DB::table('almacen.alm_ubi_nivel')
            ->where([['alm_ubi_nivel.id_nivel','=',$request->id_nivel]])
            ->update([
                'id_estante' => $request->id_estante,
                'codigo' => $request->codigo
            ]);
        return response()->json($data);
    }
    public function anular_nivel(Request $request, $id){
        $data = DB::table('almacen.alm_ubi_nivel')
            ->where([['alm_ubi_nivel.id_nivel','=',$id]])
            ->update(['estado' => 7]);
        return response()->json($data);
    }
    public function revisar_nivel($id){
        $data = DB::table('almacen.alm_ubi_posicion')
            ->where([['alm_ubi_posicion.id_nivel','=',$id],
                    ['estado','=', 1]])
            ->get()->count();
        return response()->json($data);
    }
    /* Posicion */
    public function mostrar_posiciones()
    {
        $data = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_ubi_posicion.*', 'alm_almacen.descripcion as alm_descripcion',
                'alm_ubi_estante.codigo as cod_estante','alm_ubi_nivel.codigo as cod_nivel')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
                ->orderBy('codigo')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_posiciones_nivel($id)
    {
        $data = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_ubi_posicion.*', 'alm_almacen.descripcion as alm_descripcion',
                'alm_ubi_estante.codigo as cod_estante','alm_ubi_nivel.codigo as cod_nivel')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_posicion.id_nivel', '=', $id]])
            ->orderBy('codigo')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_posicion($id)
    {
        $data = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_ubi_posicion.*','alm_almacen.id_almacen',
            'alm_ubi_estante.id_estante','alm_ubi_nivel.id_nivel')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_posicion.id_posicion', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function guardar_posicion(Request $request){
        if ($request->id_nivel !== null){
            $id_posicion = DB::table('almacen.alm_ubi_posicion')->insertGetId(
                [
                    'id_nivel' => $request->id_nivel,
                    'codigo' => $request->codigo,
                    'estado' => 1
                ],
                    'id_posicion'
                );
        }
        return response()->json($id_posicion);
    }
    public function guardar_posiciones(Request $request){
        $cod_nivel = $request->cod_nivel;
        $desde = $request->desde;
        $hasta = $request->hasta;
        $i = 0;
        for ($i=$desde; $i<=$hasta; $i++) {
            $codigo = $cod_nivel."-".GenericoAlmacenController::leftZero(2,$i);

            $exist = DB::table('almacen.alm_ubi_posicion')
                ->where('codigo',$codigo)->get()->count();

            if ($exist === 0){
                $data = DB::table('almacen.alm_ubi_posicion')->insertGetId([
                    'id_nivel' => $request->id_nivel,
                    'codigo' => $codigo,
                    'estado' => 1
                ],
                    'id_posicion'
                );
            }
        }
        return response()->json($data);
    }

    public function anular_posicion(Request $request, $id){
        $data = DB::table('almacen.alm_ubi_posicion')
            ->where([['alm_ubi_posicion.id_posicion','=',$id]])
            ->update(['estado' => 7]);
        return response()->json($data);
    }

    public function almacen_posicion($id)
    {
        $data = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_almacen.descripcion as alm_descripcion')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_posicion.id_posicion', '=', $id]])
                ->get();
        return response()->json($data);
    }
}
