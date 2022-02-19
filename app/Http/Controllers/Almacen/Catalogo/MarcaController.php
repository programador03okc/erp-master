<?php

namespace App\Http\Controllers\Almacen\Catalogo;

use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Almacen\Catalogo\SubCategoria;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Almacen\Catalogo\Marca;

class MarcaController extends Controller
{
    function viewMarca()
    {
        return view('almacen/producto/marca');
    }
    
    public static function mostrar_subcategorias_cbo()
    {
        $data = Marca::select('alm_subcat.id_subcategoria', 'alm_subcat.descripcion')
            ->where([['alm_subcat.estado', '=', 1]])
            ->orderBy('descripcion')
            ->get();
        return $data;
    }
    //SubCategorias
    public function listarMarcas()
    {
        $data = Marca::where('estado', 1)->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrarMarca($id)
    {
        $data = Marca::select('alm_subcat.*', 'sis_usua.nombre_corto')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_subcat.registrado_por')
            ->where([['alm_subcat.id_subcategoria', '=', $id]])
            ->get();
        return response()->json($data);
    }

    public function subcategoria_nextId($id_categoria)
    {
        $cantidad = Marca::where('estado', 1)->get()->count();
        $nextId = AlmacenController::leftZero(3, $cantidad);
        return $nextId;
    }

    public function actualizarMarca(Request $request)
    {
        try{
            DB::beginTransaction();
            $msj = '';
            $des = strtoupper($request->descripcion);

            $count = Marca::where([['descripcion', '=', $des], ['estado', '=', 1]])
                ->count();

            if ($count <= 1) {
                Marca::where('id_subcategoria', $request->id_subcategoria)
                    ->update(['descripcion' => $des]);
                    $msj= 'Se actualizó la marca correctamente';
                    $status=200;
                    $tipo='success';
            } else {
                $msj = 'No es posible actualizar. Ya existe una marca con dicha descripción';
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

    public function anularMarca(Request $request, $id)
    {
        try{
            DB::beginTransaction();
        $count = DB::table('almacen.alm_prod')
        ->where([
            ['id_subcategoria', '=', $id],
            ['estado', '=', 1]
        ])
            ->get()->count();
            if($count>=1){
                $mensaje ='La marca ya fue relacionada';
                $status=204;
                $tipo='warning';
            }
            else{
                $data = Marca::where('id_subcategoria', $id)
                ->update(['estado' => 7]);
                $mensaje = 'La marca se anuló correctamente';
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

    public function revisarMarca($id)
    {
        $data = DB::table('almacen.alm_prod')
            ->where([
                ['id_subcategoria', '=', $id],
                ['estado', '=', 1]
            ])
            ->get()->count();
        return response()->json($data);
    }

    public function guardarMarca(Request $request)
    {
        try{
            DB::beginTransaction();
            $fecha = date('Y-m-d H:i:s');
            $des = strtoupper($request->descripcion);
            $msj = '';
            
            $count = Marca::where([['descripcion', '=', $des], ['estado', '=', 1]])
            ->count();
            if ($count == 0) {
                $subcategoria = new Marca();
                //$subcategoria->codigo = Marca::nextId();
                $subcategoria->descripcion = $des;
                $subcategoria->estado = 1;
                $subcategoria->fecha_registro = new Carbon();
                $subcategoria->registrado_por = Auth::user()->id_usuario;
                $subcategoria->save();
    
                $msj = 'Se guardó la marca correctamente';
                $status= 200;
                $tipo='success';
            } else {
                $msj = 'No es posible guardar. Ya existe una marca con dicha descripción';
                $status = 204;
                $tipo='warning';
            }

            DB::commit();
            return response()->json(['tipo' => $tipo, 'status' => $status, 'mensaje' => $msj]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    
    }
}
