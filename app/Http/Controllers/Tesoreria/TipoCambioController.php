<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\Controller;
use App\Models\Tesoreria\TipoCambio;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TipoCambioController extends Controller 
{
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function index()
	{
		return view('tesoreria.tipo_cambio.lista');
	}

	public function listar()
    {
        $data = DB::table('contabilidad.cont_tp_cambio');

        return DataTables::of($data)
        ->addColumn('accion', function ($data) { 
			return 
            '<div class="btn-group" role="group">
                <button type="button" class="btn btn-xs btn-primary" onclick="editar('.$data->id_tp_cambio.');"><span class="fas fa-edit"></span></button>
            </div>';
        })->rawColumns(['accion'])->make(true);
    }

    public function guardar(Request $request)
    {
        try {
            DB::table('contabilidad.cont_tp_cambio')->where('id_tp_cambio', $request->id)->update(['compra' => $request->compra, 'venta' => $request->venta, 'promedio' => $request->promedio]);

            $response = 'ok';
            $alert = 'success';
            $message = 'Se han actualizado los valores del TC';
            $error = '';
        } catch (Exception $ex) {
            $response = 'error';
            $alert = 'danger';
            $message ='Hubo un problema en el servidor. Por favor intente de nuevo';
            $error = $ex;
        }
        return response()->json(array('response' => $response, 'alert' => $alert, 'message' => $message, 'error' => $error), 200);
    }

    public function editar(Request $request)
    {
        $data = DB::table('contabilidad.cont_tp_cambio')->where('id_tp_cambio', $request->id)->get();
        return response()->json($data, 200);
    }
}
