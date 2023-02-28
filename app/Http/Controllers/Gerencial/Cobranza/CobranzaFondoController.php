<?php

namespace App\Http\Controllers\Gerencial\Cobranza;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Administracion\Periodo;
use App\Models\Comercial\Cliente;
use App\Models\Configuracion\Moneda;
use App\Models\Configuracion\Usuario;
use App\Models\Gerencial\CobranzaFondo;
use App\Models\Gerencial\FormaPago;
use App\Models\Gerencial\TipoGestion;
use App\Models\Gerencial\TipoNegocio;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CobranzaFondoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $tipoGestion = TipoGestion::orderBy('descripcion', 'asc')->get();
        $tipoNegocio = TipoNegocio::orderBy('descripcion', 'asc')->get();
        $formaPago = FormaPago::orderBy('descripcion', 'asc')->get();
        $clientes = Cliente::with('contribuyente')->get();
        $periodos = Periodo::where('estado', 1)->orderBy('descripcion', 'asc')->get();
        $monedas = Moneda::orderBy('id_moneda', 'asc')->get();
        $responsables = Usuario::where('estado', 1)->orderBy('nombre_corto', 'asc')->get();
        return view('gerencial.cobranza.fondos', get_defined_vars());
    }

    public function lista()
    {
        $data = CobranzaFondo::all();
        return DataTables::of($data)
        ->editColumn('fecha_solicitud', function ($data) { return date('d-m-Y', strtotime($data->fecha_solicitud)); })
        ->addColumn('tipo_gestion', function ($data) { return $data->tipo_gestion->descripcion; })
        ->addColumn('tipo_negocio', function ($data) { return $data->tipo_negocio->descripcion; })
        ->addColumn('forma_pago', function ($data) { return $data->forma_pago->descripcion; })
        ->addColumn('moneda', function ($data) { return $data->moneda->codigo_divisa; })
        ->addColumn('cliente', function ($data) { return $data->cliente->contribuyente->razon_social; })
        ->addColumn('responsable', function ($data) { return $data->responsable->nombre_corto; })
        ->addColumn('fechas', function ($data) { return 'Ini: '.date('d-m-Y', strtotime($data->fecha_inicio)).'<br>Venc: '.date('d-m-Y', strtotime($data->fecha_vencimiento)); })
        ->addColumn('accion', function ($data) { return 
            '<button type="button" class="btn btn-success btn-xs" data-id="'.$data->id.'">
                <span class="fas fa-check"></span>
            </button>
            <button type="button" class="btn btn-primary btn-xs" data-id="'.$data->id.'">
                <span class="fas fa-edit"></span>
            </button>
            <button type="button" class="btn btn-danger btn-xs" data-id="'.$data->id.'">
                <span class="fas fa-trash-alt"></span>
            </button>';
        })->rawColumns(['fechas', 'accion'])->make(true);
    }

    public function guardar(Request $request)
    {
        try {
            $data = CobranzaFondo::firstOrNew(['id' => $request->id]);
                $data->fecha_solicitud = $request->fecha_solicitud;
                $data->tipo_gestion_id = $request->tipo_gestion_id;
                $data->tipo_negocio_id = $request->tipo_negocio_id;
                $data->periodo_id = $request->periodo_id;
                $data->forma_pago_id = $request->forma_pago_id;
                $data->cliente_id = $request->cliente_id;
                $data->moneda_id = $request->moneda_id;
                $data->importe = $request->importe;
                $data->fecha_inicio = $request->fecha_inicio;
                $data->fecha_vencimiento = $request->fecha_vencimiento;
                $data->periodo_id = $request->periodo_id;
                $data->responsable_id = $request->responsable_id;
                $data->detalles = $request->detalles;
                $data->claim = $request->claim;
                $data->pagador = $request->pagador;
                $data->estado = 1;
                $data->usuario_id = Auth::user()->id_usuario;
            $data->save();

            $mensaje = ($request->id > 0) ? 'Se ha editado el registro' : 'Se ha registrado el registro';
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
