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
        ->addColumn('empresa', function ($data) { return (isset($data->cobranza->empresa)) ? $data->cobranza->empresa->codigo : ''; })
        ->addColumn('ocam', function ($data) { return $data->cobranza->ocam; })
        ->addColumn('cliente', function ($data) { return (isset($data->cobranza->cliente)) ? $data->cobranza->cliente->contribuyente->razon_social : ''; })
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
                    </button>
                    <button type="button" class="btn btn-primary btn-xs editar" data-id="'.$data->id.'">
                        <span class="fas fa-edit"></span>
                    </button>
                    <button type="button" class="btn btn-danger btn-xs eliminar" data-id="'.$data->id.'">
                        <span class="fas fa-trash-alt"></span>
                    </button>';
            }
            return $button;
        })
        ->editColumn('estado', function ($data) { 
            return ($data->estado == 'PENDIENTE') ? '<label class="label label-primary" style="font-size: 10.5px;">PENDIENTE</label>' : '<label class="label label-success" style="font-size: 10.5px;">PAGADO</label>';
        })
        ->editColumn('importe', function ($data) { return number_format($data->importe, 2); })
        ->editColumn('importe_cobro', function ($data) { return number_format($data->importe, 2); })
        ->rawColumns(['estado', 'accion'])
        ->make(true);
    }

    public function cargarCobroDev(Request $request)
    {
        $data = PenalidadCobro::find($request->id);
        return response()->json($data);
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
                $data->estado = 'PAGADO';
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

    public function guardarPagador(Request $request)
    {
        try {
            $data = PenalidadCobro::find($request->id);
                ($request->pagador_dev != null) ?? $data->pagador = $request->pagador_dev;
                ($request->importe_cobro_dev != null) ?? $data->importe_cobro = $request->importe_cobro_dev;
                ($request->motivo_dev != null) ?? $data->motivo = $request->motivo_dev;
            $data->save();

            $mensaje = 'Se ha actualizado los datos de la devolución';
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

    public function eliminar(Request $request)
    {
        try {
            $data = PenalidadCobro::find($request->id);
            $data->delete();
            $alerta = 'info';
            $mensaje = 'Se ha eliminado el registro de penalidad';
            $error = '';
        } catch (Exception $ex) {
            $alerta = 'error';
            $mensaje ='Hubo un problema al eliminar. Por favor intente de nuevo';
            $error = $ex;
        }
        return response()->json(array('mensaje' => $mensaje, 'alerta' => $alerta, 'error' => $error), 200);
    }
}