<?php

namespace App\Http\Controllers\Gerencial\Cobranza;

use App\Exports\FondoExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Administracion\Periodo;
use App\Models\Comercial\Cliente;
use App\models\Configuracion\AccesosUsuarios;
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
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class CobranzaFondoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        #array de accesos de los modulos copiar en caso tenga accesos -----
        $array_accesos = [];
        $accesos_usuario = AccesosUsuarios::where('estado', 1)->where('id_usuario', Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos, $value->id_acceso);
        }
        #-------------------------------

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
        ->editColumn('nro_documento', function ($data) { return ($data->nro_documento != null) ? $data->nro_documento : ''; })
        ->editColumn('fecha_solicitud', function ($data) { return date('d-m-Y', strtotime($data->fecha_solicitud)); })
        ->addColumn('tipo_gestion', function ($data) { return $data->tipo_gestion->descripcion; })
        ->addColumn('tipo_negocio', function ($data) { return $data->tipo_negocio->descripcion; })
        ->addColumn('forma_pago', function ($data) { return $data->forma_pago->descripcion; })
        ->addColumn('moneda', function ($data) { return $data->moneda->codigo_divisa; })
        ->addColumn('cliente', function ($data) { return $data->cliente->contribuyente->razon_social; })
        ->addColumn('responsable', function ($data) { return $data->responsable->nombre_corto; })
        ->addColumn('fechas', function ($data) { return 'Inicio: '.date('d-m-Y', strtotime($data->fecha_inicio)).'<br>Venc: '.date('d-m-Y', strtotime($data->fecha_vencimiento)); })
        ->addColumn('flag_estado', function($data) {
            return ($data->estado == 1) ? '<label class="label label-primary" style="font-size: 10.5px;">PENDIENTE</label>' : '<label class="label label-success" style="font-size: 10.5px;">COBRADO</label>';
        })
        ->addColumn('accion', function ($data) {
            #array de accesos de los modulos copiar en caso tenga accesos -----
            $array_accesos = [];
            $accesos_usuario = AccesosUsuarios::where('estado', 1)->where('id_usuario', Auth::user()->id_usuario)->get();
            foreach ($accesos_usuario as $key => $value) {
                array_push($array_accesos, $value->id_acceso);
            }
            #-------------------------------

            $button = '' ;
            $documento = "$data->nro_documento";
            if ($data->estado == 1) {
                if (Auth::user()->id_usuario == 1 || Auth::user()->id_usuario == 20 || Auth::user()->id_usuario == 27) {
                    $button .=
                    (in_array(327, $array_accesos, true)?'<button type="button" class="btn btn-success btn-xs cobrar" data-id="'.$data->id.'" data-documento="'.$documento.'"> <span class="fas fa-check"></span> </button>':'').
                    (in_array(321, $array_accesos, true)?'<button type="button" class="btn btn-primary btn-xs editar" data-id="'.$data->id.'"> <span class="fas fa-edit"></span> </button>':'')
                    .''.
                    (in_array(328, $array_accesos, true)?'<button type="button" class="btn btn-danger btn-xs eliminar" data-id="'.$data->id.'"> <span class="fas fa-trash-alt"></span> </button>':'').
                    '';
                } else {
                    $button .=
                    (in_array(321, $array_accesos, true)?'<button type="button" class="btn btn-primary btn-xs editar" data-id="'.$data->id.'"> <span class="fas fa-edit"></span> </button>':'');
                }
            } else {
                $button .=
                (in_array(321, $array_accesos, true)?'<button type="button" class="btn btn-primary btn-xs editar" data-id="'.$data->id.'"> <span class="fas fa-edit"></span> </button>':'')
                    ;
            }
            return $button;
        })
        ->editColumn('importe', function ($data) { return number_format($data->importe, 2); })
        ->rawColumns(['fechas', 'flag_estado', 'accion'])->make(true);
    }

    public function guardar(Request $request)
    {
        try {
            $data = CobranzaFondo::firstOrNew(['id' => $request->id]);
                $data->fecha_solicitud = $request->fecha_solicitud;
                $data->tipo_gestion_id = $request->tipo_gestion_id;
                $data->tipo_negocio_id = $request->tipo_negocio_id;
                $data->forma_pago_id = $request->forma_pago_id;
                $data->cliente_id = $request->cliente_id;
                $data->moneda_id = $request->moneda_id;
                $data->importe = $request->importe;
                $data->fecha_inicio = $request->fecha_inicio;
                $data->fecha_vencimiento = $request->fecha_vencimiento;
                $data->periodo_id = $request->periodo_id;
                $data->nro_documento = $request->nro_documento;
                $data->responsable_id = $request->responsable_id;
                $data->detalles = $request->detalles;
                $data->claim = $request->claim;
                $data->pagador = $request->pagador;
                if ($request->id == 0) {
                    $data->estado = 1;
                }
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

    public function cargarCobro(Request $request)
    {
        $data = CobranzaFondo::find($request->id);
        return response()->json($data);
    }

    public function guardarCobro(Request $request)
    {
        try {
            $data = CobranzaFondo::find($request->cobranza_fondo_id);
                $data->fecha_cobranza = $request->fecha_cobranza;
                $data->nro_documento = $request->nro_documento_cobro;
                $data->observaciones = $request->observaciones;
                $data->estado = 2;
            $data->save();

            $mensaje = 'Se ha cerrado el registro de cobranza';
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
            $data = CobranzaFondo::find($request->id);
            $data->delete();
            $alerta = 'info';
            $mensaje = 'Se ha eliminado el registro de cobranza';
            $error = '';
        } catch (Exception $ex) {
            $alerta = 'error';
            $mensaje ='Hubo un problema al eliminar. Por favor intente de nuevo';
            $error = $ex;
        }
        return response()->json(array('mensaje' => $mensaje, 'alerta' => $alerta, 'error' => $error), 200);
    }

    public function exportarExcel()
    {
        return Excel::download(new FondoExport(), 'fondo.xlsx');
    }
}
