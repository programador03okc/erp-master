<?php
namespace App\Http\Controllers\Gerencial\Cobranza;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Gerencial\PenalidadCobro;
use Exception;
use Yajra\DataTables\Facades\DataTables;

class DevolucionPenalidadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('gerencial.cobranza.devolucion', get_defined_vars());
    }

    public function lista()
    {
        $data = PenalidadCobro::all();
        return DataTables::of($data)
        ->addColumn('empresa', function ($data) { return $data->cobranza->empresa->codigo; })
        ->addColumn('ocam', function ($data) { return $data->cobranza->ocam; })
        ->addColumn('cliente', function ($data) { return (isset($data->cliente)) ? $data->cliente->contribuyente->razon_social : ''; })
        ->addColumn('factura', function ($data) { return $data->cobranza->factura; })
        ->addColumn('oc_fisica', function ($data) { return $data->cobranza->oc_fisica; })
        ->addColumn('siaf', function ($data) { return $data->cobranza->siaf; })
        ->addColumn('moneda', function($data) { return ($data->cobranza->moneda == 1) ? 'S/' : 'USD'; })
        ->addColumn('accion', function ($data) {
            $button = '';
            if ($data->estado == 'PENDIENTE') {
                $button .=
                    '<button type="button" class="btn btn-success btn-xs cobrar" data-id="'.$data->id.'">
                        <span class="fas fa-check"></span>
                    </button>';
            }
            return $button;
        })
        ->editColumn('estado', function ($data) { 
            return ($data->estado == 'PENDIENTE') ? '<label class="label label-primary" style="font-size: 10.5px;">PENDIENTE</label>' : '<label class="label label-success" style="font-size: 10.5px;">FINALIZADO</label>';
        })
        ->rawColumns(['estado', 'accion'])
        ->make(true);
    }

    public function guardar(Request $request)
    {
        try {
            $data = PenalidadCobro::find($request->cobranza_penalidad_id);
                $data->fecha_cobro = $request->fecha_cobro;
                $data->nro_documento = $request->nro_documento;
                $data->pagador = $request->pagador;
                $data->importe_cobro = $request->importe_cobro;
                $data->motivo = $request->motivo;
                $data->estado = 'FINALIZADO';
            $data->save();

            $mensaje = 'Se ha cerrado el registro de devolución';
            $respuesta = 'ok';
            $alerta = 'success';
            $error = '';
        } catch (Exception $ex) {
            $respuesta = 'error';
            $alerta = 'error';
            $mensaje = 'Hubo un problema al registrar. Por favor intente de nuevo';
            $error = $ex;
        }
        return response()->json(array('respuesta' => $respuesta, 'alerta' => $alerta, 'mensaje' => $mensaje, 'error' => $error), 200);
    }
}