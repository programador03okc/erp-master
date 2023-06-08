<?php

namespace App\Http\Controllers\Gerencial\Cobranza;

use App\Helpers\ConfiguracionHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Comercial\Cliente;
use App\Models\Comercial\EstablecimientoCliente;
use App\models\Configuracion\AccesosUsuarios;
use App\Models\Configuracion\Departamento;
use App\Models\Configuracion\Distrito;
use App\Models\Configuracion\Moneda;
use App\Models\Configuracion\Pais;
use App\Models\Configuracion\Provincia;
use App\Models\Contabilidad\Banco;
use App\Models\Contabilidad\ContactoContribuyente;
use App\Models\Contabilidad\Contribuyente;
use App\Models\Contabilidad\CuentaContribuyente;
use App\Models\Contabilidad\Identidad;
use App\Models\Contabilidad\TipoContribuyente;
use App\Models\Contabilidad\TipoCuenta;
use App\Models\mgcp\Oportunidad\Status;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
// use App\Models\sistema\sistema_doc_identidad;
use Yajra\DataTables\Facades\DataTables;

class ClienteController extends Controller
{
    //
    public function cliente()
    {
        # code...
        #array de accesos de los modulos copiar en caso tenga accesos -----
        $array_accesos = [];
        $accesos_usuario = AccesosUsuarios::where('estado', 1)->where('id_usuario', Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos, $value->id_acceso);
        }
        #-------------------------------

        $pais = Pais::get();
        $departamento = Departamento::get();
        $tipo_documentos = Identidad::where('estado',1)->get();
        return view('gerencial.cobranza.cliente',compact('pais','departamento','tipo_documentos','array_accesos'));
    }
    public function listarCliente()
    {
        $data = Contribuyente::where('adm_contri.estado',1)
        ->select(
            'adm_contri.*'
        )
        ->join('comercial.com_cliente', 'com_cliente.id_contribuyente', '=', 'adm_contri.id_contribuyente');
        // $data = Contribuyente::all();
        return DataTables::of($data)
        // return datatables($data)
        // ->toJson();
        ->make(true);
    }
    public function crear(Request $request)
    {
        $success = false;
        $status = 400;
        $title= 'Información';
        $text=  'Este usuario ya esta registrado';
        $icon = 'warning';

        $contribuyente = Contribuyente::where([
            ["estado", 1],
            // ["id_doc_identidad", $request->tipo_documnto],
            ["nro_documento", $request->documento]
        ])->first();
            // return $contribuyente;exit;
        if (!$contribuyente) {
            $contribuyente = new Contribuyente();
            $contribuyente->id_tipo_contribuyente = $request->tipo_contribuyente;
            $contribuyente->id_doc_identidad = $request->tipo_documnto > 0 ? $request->tipo_documnto : null;
            $contribuyente->nro_documento = $request->documento;
            $contribuyente->razon_social = $request->razon_social;
            $contribuyente->direccion_fiscal = $request->direccion;
            $contribuyente->id_pais = $request->pais > 0 ? $request->pais : null;
            $contribuyente->ubigeo = $request->distrito;
            $contribuyente->telefono = $request->telefono;
            $contribuyente->celular = $request->celular;
            $contribuyente->email = $request->email;
            $contribuyente->estado = 1;
            $contribuyente->fecha_registro = new Carbon();
            $contribuyente->transportista = false;
            $contribuyente->save();

            $success = true;
            $status = 200;
            $title= 'Éxito';
            $text=  'Se registro con éxito';
            $icon = 'success';

            $com_cliente = Cliente::where('id_contribuyente',$contribuyente->id_contribuyente)->where('estado',1)->first();

            if (!$com_cliente) {
                $codigo = ConfiguracionHelper::generarCodigo('C','-',3,'cliente');
                $com_cliente = new Cliente();
                $com_cliente->id_contribuyente = $contribuyente->id_contribuyente;
                $com_cliente->observacion = $request->observacion;
                $com_cliente->estado = 1;
                $com_cliente->codigo = $codigo;
                $com_cliente->fecha_registro = new Carbon();
                $com_cliente->save();

                $success = true;
                $status = 200;
                $title= 'Éxito';
                $text=  'Se registro con éxito';
                $icon = 'success';
            }

            if ($status===200) {
                foreach ( (object)$request->establecimiento as $key => $value) {
                    $establecimientoProveedor = new EstablecimientoCliente();
                    $establecimientoProveedor->id_cliente = $com_cliente->id_cliente;
                    $establecimientoProveedor->direccion = $value['direccion'];
                    $establecimientoProveedor->horario = $value['horario'];
                    $establecimientoProveedor->ubigeo = $value['ubigeo'];
                    $establecimientoProveedor->estado = 1;
                    $establecimientoProveedor->fecha_registro = new Carbon();
                    $establecimientoProveedor->save();
                }

                foreach ( (object)$request->contacto as $key => $value) {
                    $contactoContribuyente = new ContactoContribuyente();
                    $contactoContribuyente->id_contribuyente    = $contribuyente->id_contribuyente;
                    $contactoContribuyente->nombre              = $value['nombre'];
                    $contactoContribuyente->telefono            = $value['telefono'];
                    $contactoContribuyente->email               = $value['email'];
                    $contactoContribuyente->cargo               = $value['cargo'];
                    $contactoContribuyente->fecha_registro      = new Carbon();
                    $contactoContribuyente->direccion           = $value['direccion'];
                    $contactoContribuyente->estado              = 1;
                    $contactoContribuyente->horario             = $value['horario'];
                    $contactoContribuyente->ubigeo              = $value['ubigeo'];
                    $contactoContribuyente->save();
                }

                foreach ( (object)$request->cuenta_bancaria as $key => $value) {
                    $cuentaBancariaProveedor = new CuentaContribuyente();
                    $cuentaBancariaProveedor->id_contribuyente          = $contribuyente->id_contribuyente;
                    $cuentaBancariaProveedor->id_banco                  = $value['banco'];
                    $cuentaBancariaProveedor->id_tipo_cuenta            = $value['tipo_cuenta'];
                    $cuentaBancariaProveedor->id_moneda                 = $value['moneda'];
                    $cuentaBancariaProveedor->nro_cuenta                = $value['numero_cuenta'];
                    $cuentaBancariaProveedor->nro_cuenta_interbancaria  = $value['cuenta_interbancaria'];
                    $cuentaBancariaProveedor->swift                     = $value['swift'];
                    $cuentaBancariaProveedor->estado                    = 1;
                    $cuentaBancariaProveedor->fecha_registro            = new Carbon();
                    $cuentaBancariaProveedor->save();
                }
            }
        }




        return response()->json([
            "success"=>$success,
            "status"=>$status,
            "data"=>$request->pais,
            "title"=> $title,
            "text"=> $text,
            "icon"=> $icon,
        ]);
    }
    public function editar(Request $request)
    {
        $contribuyente = Contribuyente::find($request->id_contribuyente);
        $distrito   = Distrito::where('id_dis',$contribuyente->ubigeo)->first();
        $provincia=[];
        $distrito_all=[];
        if ($distrito) {
            $distrito_all  = Distrito::where('id_prov',$distrito->id_prov)->get();
            $provincia  = Provincia::where('id_prov',$distrito->id_prov)->first();
        }
        $provincia_all=[];
        $departamento=[];
        if ($provincia) {
            $provincia_all  = Provincia::where('id_dpto',$provincia->id_dpto)->get();
            $departamento  = Departamento::where('id_dpto',$provincia->id_dpto)->first();
        }
        return response()->json([
            "success"=>true,
            "status"=>200,
            "contribuyente"=>$contribuyente,
            "distrito"=>$distrito?$distrito:[],
            "provincia"=>$provincia,
            "departamento"=>$departamento,
            "distrito_all"=>$distrito_all,
            "provincia_all"=>$provincia_all
        ]);
    }
    public function actualizar(Request $request)
    {
        // return $request->id_contribuyente;exit;
        $success = false;
        $status = 400;
        $title= 'Información';
        $text=  'Este usuario ya esta registrado';
        $icon = 'warning';

        $contribuyente = Contribuyente::find($request->id_contribuyente);
        $contribuyente->id_tipo_contribuyente = $request->tipo_contribuyente;
        $contribuyente->id_doc_identidad = $request->tipo_documnto > 0 ? $request->tipo_documnto : null;
        $contribuyente->nro_documento = $request->documento;
        $contribuyente->razon_social = $request->razon_social;
        $contribuyente->direccion_fiscal = $request->direccion;
        $contribuyente->id_pais = $request->pais > 0 ? $request->pais : null;
        $contribuyente->ubigeo = $request->distrito;
        $contribuyente->telefono = $request->telefono;
        $contribuyente->celular = $request->celular;
        $contribuyente->email = $request->email;
        $contribuyente->estado = 1;
        // $contribuyente->fecha_registro = new Carbon();
        $contribuyente->transportista = false;
        $contribuyente->save();

        if ($contribuyente) {
            $success = true;
            $status = 200;
            $title= 'Éxito';
            $text=  'Se registro con éxito';
            $icon = 'success';
        }

        $com_cliente = Cliente::where('id_contribuyente',$contribuyente->id_contribuyente)->first();
        // $com_cliente = Cliente::find($request->id_cliente);
        // return $com_cliente;exit;
        if ($com_cliente) {
            $com_cliente->id_contribuyente = $contribuyente->id_contribuyente;
            $com_cliente->observacion = $request->observacion;
            $com_cliente->estado = 1;
            // $com_cliente->fecha_registro = new Carbon();
            $com_cliente->save();
        }


        if ($com_cliente) {

            $success = true;
            $status = 200;
            $title= 'Éxito';
            $text=  'Se registro con éxito';
            $icon = 'success';
        }

        if ($status===200) {
            EstablecimientoCliente::where('estado', 1)
            ->where('id_cliente', $com_cliente->id_cliente)
            ->update(['estado' => 7]);
            foreach ( (object)$request->establecimiento as $key => $value) {
                $establecimientoProveedor = new EstablecimientoCliente();
                $establecimientoProveedor->id_cliente = $com_cliente->id_cliente;
                $establecimientoProveedor->direccion = $value['direccion'];
                $establecimientoProveedor->horario = $value['horario'];
                $establecimientoProveedor->ubigeo = $value['ubigeo'];
                $establecimientoProveedor->estado = 1;
                $establecimientoProveedor->fecha_registro = new Carbon();
                $establecimientoProveedor->save();
            }
            ContactoContribuyente::where('estado', 1)
            ->where('id_contribuyente', $contribuyente->id_contribuyente)
            ->update(['estado' => 7]);
            foreach ( (object)$request->contacto as $key => $value) {
                $contactoContribuyente = new ContactoContribuyente();
                $contactoContribuyente->id_contribuyente    = $contribuyente->id_contribuyente;
                $contactoContribuyente->nombre              = $value['nombre'];
                $contactoContribuyente->telefono            = $value['telefono'];
                $contactoContribuyente->email               = $value['email'];
                $contactoContribuyente->cargo               = $value['cargo'];
                $contactoContribuyente->fecha_registro      = new Carbon();
                $contactoContribuyente->direccion           = $value['direccion'];
                $contactoContribuyente->estado              = 1;
                $contactoContribuyente->horario             = $value['horario'];
                $contactoContribuyente->ubigeo              = $value['ubigeo'];
                $contactoContribuyente->save();
            }
            CuentaContribuyente::where('estado', 1)
            ->where('id_contribuyente', $contribuyente->id_contribuyente)
            ->update(['estado' => 7]);

            foreach ( (object)$request->cuenta_bancaria as $key => $value) {
                $cuentaBancariaProveedor = new CuentaContribuyente();
                $cuentaBancariaProveedor->id_contribuyente          = $contribuyente->id_contribuyente;
                $cuentaBancariaProveedor->id_banco                  = $value['banco'];
                $cuentaBancariaProveedor->id_tipo_cuenta            = $value['tipo_cuenta'];
                $cuentaBancariaProveedor->id_moneda                 = $value['moneda'];
                $cuentaBancariaProveedor->nro_cuenta                = $value['numero_cuenta'];
                $cuentaBancariaProveedor->nro_cuenta_interbancaria  = $value['cuenta_interbancaria'];
                $cuentaBancariaProveedor->swift                     = $value['swift'];
                $cuentaBancariaProveedor->estado                    = 1;
                $cuentaBancariaProveedor->fecha_registro            = new Carbon();
                $cuentaBancariaProveedor->save();
            }
        }


        return response()->json([
            "success"=>$success,
            "status"=>$status,
            "data"=>$request->pais,
            "title"=> $title,
            "text"=> $text,
            "icon"=> $icon,
        ]);
    }
    public function eliminar(Request $request)
    {
        $title= 'Información';
        $text=  'Este usuario ya esta registrado';
        $icon = 'warning';
        $success = false;
        $status = 400;
        $contribuyente = Contribuyente::find($request->id_contribuyente);
        $contribuyente->estado = 7;
        $contribuyente->save();
        if ($contribuyente) {
            $title= 'Exito';
            $text=  'Se anulo con éxito';
            $icon = 'success';
            $success = true;
            $status = 200;
        }
        return response()->json([
            "success"=>$success,
            "status"=>$status,
            "title"=> $title,
            "text"=> $text,
            "icon"=> $icon,
        ]);
    }
    public function nuevoCliente()
    {
        $pais = Pais::get();
        $departamento = Departamento::get();
        $tipo_documentos = Identidad::where('estado',1)->get();
        $tipo_contribuyente = TipoContribuyente::where('estado',1)->get();
        $monedas = Moneda::where('estado',1)->get();
        $bancos = Banco::mostrar();
        $tipo_cuenta = TipoCuenta::mostrar();
        return view('gerencial.cobranza.nuevo_cliente',compact('pais','departamento','tipo_documentos','tipo_contribuyente','monedas','bancos','tipo_cuenta'));
    }
    public function getDistrito($id_provincia)
    {
        $distrito_first = Distrito::where('id_dis',$id_provincia)->first();
        $provincia_first = Provincia::where('id_prov',$distrito_first->id_prov)->first();
        $departamento_first = Departamento::where('id_dpto',$provincia_first->id_dpto)->first();

        $provincia_get = Provincia::where('id_dpto',$departamento_first->id_dpto)->get();
        $distrito_get = Distrito::where('id_prov',$provincia_first->id_prov)->get();
        return response()->json([
            "success"=>true,
            "status"=>200,
            "distrito"=>$distrito_first,
            "provincia"=>$provincia_first,
            "departamento"=>$departamento_first,
            "provincia_all"=>$provincia_get,
            "distrito_all"=>$distrito_get
        ]);
    }
    public function editarContribuyente($id_contribuyente)
    {
        $pais = Pais::get();
        $departamento = Departamento::get();
        $tipo_documentos = Identidad::where('estado',1)->get();
        $tipo_contribuyente = TipoContribuyente::where('estado',1)->get();
        $monedas = Moneda::where('estado',1)->get();
        $bancos = Banco::mostrar();
        $tipo_cuenta = TipoCuenta::mostrar();


        $contribuyente = Contribuyente::where('id_contribuyente',$id_contribuyente)->first();

        $distrito_first=array();
        $provincia_first=array();
        $departamento_first=array();
        $provincia_get=array();
        $distrito_get=array();

        if ($contribuyente->ubigeo) {
            $distrito_first = Distrito::where('id_dis',$contribuyente->ubigeo)->first();
            $provincia_first = Provincia::where('id_prov',$distrito_first->id_prov)->first();
            $departamento_first = Departamento::where('id_dpto',$provincia_first->id_dpto)->first();

            $provincia_get = Provincia::where('id_dpto',$departamento_first->id_dpto)->get();
            $distrito_get = Distrito::where('id_prov',$provincia_first->id_prov)->get();
        }

        $establecimiento_cliente=array();
        $cliente = Cliente::where('id_contribuyente',$id_contribuyente)->first();
        if ($cliente) {
            $establecimiento_cliente = EstablecimientoCliente::where('id_cliente',$cliente->id_cliente)->where('estado',1)->get();
        }

        $contacto = ContactoContribuyente::where('id_contribuyente',$id_contribuyente)->where('estado',1)->get();
        $cuenta_bancaria = CuentaContribuyente::where('id_contribuyente',$id_contribuyente)->where('estado',1)->get();

        // return $contacto;exit;

        $data_ubigeo=array();
        if ($establecimiento_cliente) {
            foreach ($establecimiento_cliente as $key => $value) {
                $data_ubigeo = $this->getDistrito($value->ubigeo);
                $data_ubigeo = json_encode($data_ubigeo);
                $data_ubigeo = json_decode($data_ubigeo);
                $value->ubigeo_text = $data_ubigeo->original->departamento->descripcion.' - '. $data_ubigeo->original->provincia->descripcion.' - '.$data_ubigeo->original->distrito->descripcion;
            }
        }
        if ($contacto) {
            foreach ($contacto as $key => $value) {
                $data_ubigeo = $this->getDistrito($value->ubigeo);
                $data_ubigeo = json_encode($data_ubigeo);
                $data_ubigeo = json_decode($data_ubigeo);
                $value->ubigeo_text = $data_ubigeo->original->departamento->descripcion.' - '. $data_ubigeo->original->provincia->descripcion.' - '.$data_ubigeo->original->distrito->descripcion;
                // return $value;exit;
            }
        }

        if ($cuenta_bancaria) {
            foreach ($cuenta_bancaria as $key => $value) {
                $bancos_first = Banco::find($value->id_banco)->contribuyente;
                $value->banco_text = $bancos_first->razon_social;

                $tipo_cuenta_first = TipoCuenta::find($value->id_tipo_cuenta);
                $value->cuenta_text = $tipo_cuenta_first->descripcion;

                $moneda_first = Moneda::find($value->id_moneda);
                $value->modena_text = $moneda_first->descripcion;
                // return $tipo_cuenta_first;exit;
            }
        }


        // return $cuenta_bancaria;exit;
        return view('gerencial/cobranza/editar_cliente',compact('pais','departamento','tipo_documentos','tipo_contribuyente','monedas','bancos','tipo_cuenta','distrito_first','provincia_first','departamento_first','provincia_get','distrito_get','contribuyente','cliente','establecimiento_cliente','contacto','cuenta_bancaria'));
    }
    public function ver($id_contribuyente)
    {
        $contribuyente = Contribuyente::where('id_contribuyente',$id_contribuyente)->first();
        $pais = Pais::find($contribuyente->id_pais);
        $distrito_first=array();
        $provincia_first=array();
        $departamento_first=array();
        $provincia_get=array();
        $distrito_get=array();

        if ($contribuyente->ubigeo) {
            $distrito_first = Distrito::where('id_dis',$contribuyente->ubigeo)->first();
            $provincia_first = Provincia::where('id_prov',$distrito_first->id_prov)->first();
            $departamento_first = Departamento::where('id_dpto',$provincia_first->id_dpto)->first();
        }

        $establecimiento_cliente=array();
        $cliente = Cliente::where('id_contribuyente',$id_contribuyente)->first();
        if ($cliente) {
            $establecimiento_cliente = EstablecimientoCliente::where('id_cliente',$cliente->id_cliente)->where('estado',1)->get();
        }

        $contacto = ContactoContribuyente::where('id_contribuyente',$id_contribuyente)->where('estado',1)->get();
        $cuenta_bancaria = CuentaContribuyente::where('id_contribuyente',$id_contribuyente)->where('estado',1)->get();

        $data_ubigeo=array();
        if ($establecimiento_cliente) {
            foreach ($establecimiento_cliente as $key => $value) {
                $data_ubigeo = $this->getDistrito($value->ubigeo);
                $data_ubigeo = json_encode($data_ubigeo);
                $data_ubigeo = json_decode($data_ubigeo);
                $value->ubigeo_text = $data_ubigeo->original->departamento->descripcion.' - '. $data_ubigeo->original->provincia->descripcion.' - '.$data_ubigeo->original->distrito->descripcion;
            }
        }
        if ($contacto) {
            foreach ($contacto as $key => $value) {
                $data_ubigeo = $this->getDistrito($value->ubigeo);
                $data_ubigeo = json_encode($data_ubigeo);
                $data_ubigeo = json_decode($data_ubigeo);
                $value->ubigeo_text = $data_ubigeo->original->departamento->descripcion.' - '. $data_ubigeo->original->provincia->descripcion.' - '.$data_ubigeo->original->distrito->descripcion;
                // return $value;exit;
            }
        }

        if ($cuenta_bancaria) {
            foreach ($cuenta_bancaria as $key => $value) {
                $bancos_first = Banco::find($value->id_banco)->contribuyente;
                $value->banco_text = $bancos_first->razon_social;

                $tipo_cuenta_first = TipoCuenta::find($value->id_tipo_cuenta);
                $value->cuenta_text = $tipo_cuenta_first->descripcion;

                $moneda_first = Moneda::find($value->id_moneda);
                $value->modena_text = $moneda_first->descripcion;
                // return $tipo_cuenta_first;exit;
            }
        }
        $tipo_documento=array();
        $tipo_contribuyente=array();
        $tipo_documento = Identidad::where('id_doc_identidad',$contribuyente->id_doc_identidad)->first();
        $tipo_contribuyente = TipoContribuyente::where('id_tipo_contribuyente',$contribuyente->id_tipo_contribuyente)->first();
        return response()->json([
            "success"=>true,
            "status"=>200,
            "contribuyente"=>$contribuyente,
            "distrito_first"=>$distrito_first,
            "provincia_first"=>$provincia_first,
            "departamento_first"=>$departamento_first,
            "establecimiento_cliente"=>$establecimiento_cliente,
            "contacto"=>$contacto,
            "cuenta_bancaria"=>$cuenta_bancaria,
            "pais"=>$pais,
            "tipo_documento"=>$tipo_documento ? $tipo_documento : [],
            "tipo_contribuyente"=>$tipo_contribuyente ?$tipo_contribuyente :[],
            "cliente"=>$cliente
        ]);
    }
    public function buscarClienteDocumento(Request $request)
    {
        $contribuyente = Contribuyente::where('nro_documento',$request->documento)->first();
        $success =false;
        if ($contribuyente) {
            $success =true;
        }

        return response()->json(["success"=>$success,"data"=>$contribuyente],200);
    }
    public function buscarClienteDocumentoEditar(Request $request)
    {
        $contribuyente = Contribuyente::where('nro_documento',$request->documento)->first();
        $success =false;
        if ($contribuyente) {
            $success =true;
            if ($contribuyente->nro_documento==$request->data_documento) {
                $success =false;
            }
        }

        return response()->json(
            [
                "success"=>$success,
                "data"=>$contribuyente
            ],
            200
        );
    }
}
