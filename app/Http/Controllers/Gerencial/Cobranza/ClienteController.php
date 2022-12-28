<?php

namespace App\Http\Controllers\Gerencial\Cobranza;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Configuracion\Departamento;
use App\Models\Configuracion\Distrito;
use App\Models\Configuracion\Moneda;
use App\Models\Configuracion\Pais;
use App\Models\Configuracion\Provincia;
use App\Models\Contabilidad\Banco;
use App\Models\Contabilidad\Contribuyente;
use App\Models\Contabilidad\Identidad;
use App\Models\Contabilidad\TipoContribuyente;
use App\Models\Contabilidad\TipoCuenta;
// use App\Models\sistema\sistema_doc_identidad;
use Yajra\DataTables\Facades\DataTables;

class ClienteController extends Controller
{
    //
    public function cliente()
    {
        # code...
        $pais = Pais::get();
        $departamento = Departamento::get();
        $tipo_documentos = Identidad::where('estado',1)->get();
        return view('gerencial/cobranza/cliente',compact('pais','departamento','tipo_documentos'));
    }
    public function listarCliente()
    {
        $data = Contribuyente::where('estado',1);

        return DataTables::of($data)
        // return datatables($data)
        // ->toJson();
        ->make(true);
    }
    public function crear(Request $request)
    {
        return response()->json([
            $request->establecimiento,
        ]);
        $contribuyente = Contribuyente::where('nro_documento',$request->documento)->first();
        $success = false;
        $status = 400;
        $title= 'Información';
        $text=  'Este usuario ya esta registrado';
        $icon = 'warning';
        // return $contribuyente;exit;
        // if (!$contribuyente) {
        //     $success = true;
        //     $status = 200;
        //     $contribuyente = new Contribuyente();
        //     $contribuyente->id_doc_identidad = $request->tipo_documnto;
        //     $contribuyente->nro_documento = $request->documento;
        //     $contribuyente->razon_social = $request->razon_social;
        //     $contribuyente->ubigeo = $request->distrito;
        //     $contribuyente->id_pais = $request->pais;
        //     $contribuyente->estado = 1;
        //     $contribuyente->transportista = 'f';
        //     $contribuyente->fecha_registro = date('Y-m-d h:i:s');
        //     $contribuyente->save();
        //     $title= 'Éxito';
        //     $text=  'Se guardo con éxito';
        //     $icon = 'success';
        // }
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
        $title= 'Información';
        $text=  'Este usuario ya esta registrado';
        $icon = 'warning';
        $success = false;
        $status = 400;
        $contribuyente = Contribuyente::find($request->id_contribuyente);
        $contribuyente->id_doc_identidad = $request->tipo_documnto;
        $contribuyente->nro_documento = $request->documento;
        $contribuyente->razon_social = $request->razon_social;
        $contribuyente->ubigeo = $request->distrito;
        $contribuyente->id_pais = $request->pais;
        $contribuyente->estado = 1;
        // $contribuyente->transportista = 'f';
        // $contribuyente->fecha_registro = date('Y-m-d h:i:s');
        $contribuyente->save();
        if ($contribuyente) {
            $success = true;
            $status = 200;
            $title= 'Éxito';
            $text=  'Se guardo con éxito';
            $icon = 'success';
        }


        return response()->json([
            "success"=>$success,
            "status"=>$status,
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
        return view('gerencial/cobranza/nuevo_cliente',compact('pais','departamento','tipo_documentos','tipo_contribuyente','monedas','bancos','tipo_cuenta'));
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
}
