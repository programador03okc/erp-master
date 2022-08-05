<?php

namespace App\Http\Controllers\Notificaciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Configuracion\Notificacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('notificaciones/lista');
    }

    public function listaPendientes(Request $request)
    {
        $data = Notificacion::where('id_usuario', Auth::user()->id_usuario)->where('leido', false);
        return datatables($data)->rawColumns(['mensaje'])->toJson();
    }

    public function ver($id) {
        $notificacion = Notificacion::find($id);
        if ($notificacion == null || $notificacion->id_usuario != Auth::user()->id_usuario) {
            return redirect()->route('modulos');
        } else {
            $notificacion->leido = 1;
            $notificacion->save();

            if ($notificacion->url == '' || $notificacion->url == null) {
                return redirect()->route('modulos');
            } else {
                return redirect($notificacion->url);
            }
        }
    }
    
    public function eliminar(Request $request)
    {
        $notificacion = Notificacion::find($request->id);
        if ($notificacion == null || $notificacion->id_usuario != Auth::user()->id_usuario) {
            return response()->json(array('tipo' => "danger", 'mensaje' => 'No puede eliminar una notificación que no le fue asignada'), 200);
        } else {
            DB::beginTransaction();
                $notificacion->delete();
            DB::commit();
            return response()->json(array('tipo' => "info", 'mensaje' => 'Notificación eliminada'), 200);
        }
    }

    public function cantidadNoLeidas()
    {
        $data = Notificacion::where('id_usuario', Auth::user()->id_usuario)->where('leido', false)->get();
        return response()->json(array('tipo' =>"success", 'mensaje' => $data->count()), 200);
    }
}
