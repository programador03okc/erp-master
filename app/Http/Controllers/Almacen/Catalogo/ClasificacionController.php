<?php

namespace App\Http\Controllers\Almacen\Catalogo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\almacen\Catalogo\Clasificacion;
use App\Models\Almacen\Producto;
use App\models\Configuracion\AccesosUsuarios;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClasificacionController extends Controller
{
    function view_clasificacion()
    {
        $array_accesos_botonera=array();
        $accesos_botonera = AccesosUsuarios::where('accesos_usuarios.estado','=',1)
        ->select('accesos.*')
        ->join('configuracion.accesos','accesos.id_acceso','=','accesos_usuarios.id_acceso')
        ->where('accesos_usuarios.id_usuario',Auth::user()->id_usuario)
        ->where('accesos_usuarios.id_modulo',61)
        ->where('accesos_usuarios.id_padre',4)
        ->get();
        foreach ($accesos_botonera as $key => $value) {
            $value->accesos;
            array_push($array_accesos_botonera,$value->accesos->accesos_grupo);
        }
        $modulo='almacen';
        return view('almacen/producto/clasificacion',compact('array_accesos_botonera','modulo'));
    }
    public static function mostrar_clasificaciones_cbo()
    {
        $data = Clasificacion::select('alm_clasif.id_clasificacion', 'alm_clasif.descripcion')
            ->where('alm_clasif.estado', '=', 1)
            ->orderBy('descripcion')
            ->get();
        return $data;
    }
    //Clasificaciones
    public function listarClasificaciones()
    {
        $data = Clasificacion::select('alm_clasif.*')
            ->where('alm_clasif.estado', 1)
            ->orderBy('id_clasificacion')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrarClasificacion($id)
    {
        $data = Clasificacion::where('alm_clasif.id_clasificacion', $id)
            ->get();
        return response()->json($data);
    }

    public function guardarClasificacion(Request $request)
    {
        try{
            DB::beginTransaction();
            $fecha = date('Y-m-d H:i:s');
            $msj = '';
            $des = strtoupper($request->descripcion);

            $count = Clasificacion::where([['descripcion', '=', $des], ['estado', '=', 1]])
                ->count();

            if ($count == 0) {
                Clasificacion::insertGetId(
                    [
                        'descripcion' => $des,
                        'estado' => 1,
                        'fecha_registro' => $fecha
                    ],
                    'id_clasificacion'
                );
                $msj = 'Se guardó la clasificación correctamente';
                $status=200;
                $tipo='success';
            } else {
                $msj = 'No es posible guardar. Ya existe una clasificación con dicha descripción.';
                $status=204;
                $tipo='warning';
            }
            DB::commit();
            return response()->json(['tipo' => $tipo, 'status' => $status, 'mensaje' => $msj]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    public function actualizarClasificacion(Request $request)
    {
        try{
            DB::beginTransaction();
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = Clasificacion::where([['descripcion', '=', $des], ['estado', '=', 1]])
            ->count();

        if ($count <= 1) {
            $data = Clasificacion::where('id_clasificacion', $request->id_clasificacion)
                ->update(['descripcion' => $des]);
                $msj = 'Se actualizó la clasificación correctamente';
                $status=200;
                $tipo='success';
        } else {
            $msj = 'No es posible actualizar. Ya existe una clasificación con dicha descripción.';
            $status=204;
            $tipo='warning';
        }
        DB::commit();
        return response()->json(['tipo' => $tipo, 'status' => $status, 'mensaje' => $msj]);
    } catch (\PDOException $e) {
        DB::rollBack();
        return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al actualizar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
    }
    }

    public function anularClasificacion(Request $request, $id)
    {
        try{
            DB::beginTransaction();
        $count = Producto::where([
            ['id_clasif', '=', $id],
            ['estado', '=', 1]
        ])
            ->get()->count();
            if($count>=1){
                $mensaje ='La clasificación ya fue relacionada en un producto';
                $status=204;
                $tipo='warning';
            }
            else{
                $data = Clasificacion::where('id_clasificacion', $id)
                ->update(['estado' => 7]);
                $mensaje = 'La clasificación se anuló correctamente';
                $status=200;
                $tipo='success';
            }
            DB::commit();
            return response()->json(['tipo' => $tipo, 'status' => $status, 'mensaje' => $mensaje]);
        }  catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al anular. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    public function revisarClasificacion($id)
    {
        $data = Producto::where([
            ['id_clasif', '=', $id],
            ['estado', '=', 1]
        ])
            ->get()->count();
        return response()->json($data);
    }
}
