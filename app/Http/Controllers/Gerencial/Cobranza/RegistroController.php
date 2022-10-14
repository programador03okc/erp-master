<?php

namespace App\Http\Controllers\Gerencial\Cobranza;

use App\Gerencial\Cobranza as GerencialCobranza;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Gerencial\CobranzaAgil;
use App\Models\Administracion\Periodo;
use App\Models\Almacen\Requerimiento;
use App\Models\Comercial\Cliente as ComercialCliente;
use App\Models\Configuracion\Departamento;
use App\Models\Configuracion\Distrito;
use App\Models\Configuracion\Pais;
use App\Models\Configuracion\Provincia;
use App\Models\Contabilidad\Contribuyente;
use App\models\Gerencial\AreaResponsable;
use App\models\Gerencial\Cliente;
use App\models\Gerencial\CobanzaFase;
use App\models\Gerencial\Cobranza;
use App\models\Gerencial\Empresa;
use App\models\Gerencial\EstadoDocumento;
use App\models\Gerencial\Sector;
use App\models\Gerencial\TipoTramite;
use Exception;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RegistroController extends Controller
{
    //
    public function registro()
    {
        # code...
        $sector             = Sector::where('estado',1)->get();
        $tipo_ramite        = TipoTramite::where('estado',1)->get();
        $empresa            = Empresa::where('estado',1)->get();
        $periodo            = Periodo::where('estado',1)->get();
        $estado_documento   = EstadoDocumento::where('estado',1)->get();

        $pais = Pais::get();
        $departamento = Departamento::get();

        $obtener_listado = Requerimiento::where('alm_req.enviar_facturacion','t')->where('doc_ven.estado',1)
        ->select(
            'alm_req.id_requerimiento',
            'alm_req.codigo',
            'alm_det_req.id_detalle_requerimiento',
            'doc_ven_det.id_doc_det',
            'doc_ven.id_doc_ven',
            'doc_ven.serie',
            'doc_ven.numero'
        )
        ->join('almacen.alm_det_req' , 'alm_det_req.id_requerimiento', '=' ,'alm_req.id_requerimiento')
        ->join('almacen.doc_ven_det' , 'doc_ven_det.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')
        ->join('almacen.doc_ven' , 'doc_ven.id_doc_ven' ,'=' ,'doc_ven_det.id_doc')
        ->get();
        return view('gerencial/cobranza/registro',compact('sector','tipo_ramite','empresa','periodo','estado_documento', 'pais', 'departamento'));
    }
    public function listarRegistros()
    {
        $data = Cobranza::select('*')->orderBy('id_cobranza', 'desc');
        return DataTables::of($data)
        ->addColumn('empresa', function($data){ return $data->empresa->nombre; })
        ->addColumn('cliente', function($data){ return $data->cliente->nombre; })
        ->addColumn('atraso', function($data){
            return ($this->restar_fechas($data->fecha_recepcion, date('Y-m-d')) > 0) ? $this->restar_fechas($data->fecha_recepcion, date('Y-m-d')) : '0';
         })
        ->addColumn('moneda', function($data){ return ($data->moneda == 1) ? 'S/' : 'US $'; })
        ->addColumn('importe', function($data){ return number_format($data->importe, 2); })
        ->addColumn('estado', function($data){
            $estado_documento = EstadoDocumento::where('estado',1)->get();
            return [$data->estadoDocumento->id_estado_doc,$estado_documento];
        })
        ->addColumn('area', function($data){
            $area_responsable = AreaResponsable::where('estado',1)->get();
            return [$data->areaResponsable->id_area, $area_responsable];
         })
        ->addColumn('fase', function($data) {
            $fase = CobanzaFase::where('id_cobranza', $data->id_cobranza)->orderBy('id_fase', 'desc')->first();
            return ($fase?$fase->fase[0] : '-');
        })->make(true);
    }
    public function restar_fechas($fi, $ff){
		$ini = strtotime($fi);
		$fin = strtotime($ff);
		$dif = $fin - $ini;
		$diasFalt = ((($dif / 60) / 60) / 24);
		return ceil($diasFalt);
	}
    public function listarClientes()
    {
        $data = Cliente::select('*')->orderBy('id_cliente', 'desc');
        return DataTables::of($data)->make(true);;
    }
    public function prueba()
    {
        $data = Cliente::select('*')->orderBy('id_cliente', 'desc');
        return DataTables::of($data);
        // return response()->json($cobranza, 200);
    }
    public function nuevoCliente(Request $request)
    {
        // $cliente = Cliente::where('ruc',$request->nuevo_ruc_dni_cliente)->orWhere('nombre','like','%'.$request->nuevo_cliente.'%')->first();

        $cliente = Contribuyente::where('estado',1)->where('nro_documento',$request->nuevo_ruc_dni_cliente)
        ->where('razon_social',$request->nuevo_cliente)
        ->first();

        $cliente_gerencial = DB::table('gerencial.cliente')->where('estado',1)->where('ruc',$request->nuevo_ruc_dni_cliente)
        ->where('nombre',$request->nuevo_cliente)
        ->first();
        // DB::insert('insert into users (id, name) values (?, ?)', [1, 'Dayle']);

        if (empty($cliente_gerencial)) {
            DB::table('gerencial.cliente')->insert([
                ['ruc'      => $request->nuevo_ruc_dni_cliente,
                'nombre'    => $request->nuevo_cliente,
                'estado'    => 1],
            ]);

            $id_cliente = DB::table('gerencial.cliente')->orderByDesc('id_cliente')->get();
            $cliente_gerencial = $id_cliente[0];
        }

        if (empty($cliente)) {
            $cliente = new Contribuyente;
            $cliente->nro_documento     = $request->nuevo_ruc_dni_cliente;
            $cliente->razon_social      = $request->nuevo_cliente;
            $cliente->id_pais           = 170;
            $cliente->estado            = 1;
            $cliente->fecha_registro    = date('Y-m-d H:i:s');
            $cliente->transportista     = false;

            $cliente->ubigeo            = $request->distrito;

            $cliente->id_cliente_gerencial_old    = $cliente_gerencial->id_cliente;
            $cliente->save();

            $com_cliente = new ComercialCliente();
            $com_cliente->id_contribuyente=$cliente->id_contribuyente;
            $com_cliente->estado=1;
            $com_cliente->fecha_registro = date('Y-m-d H:i:s');
            $com_cliente->save();
        }
        return response()->json([
            "succes"=>true,
            "status"=>200,
            "usuario_nuevo"=>$cliente_gerencial,
            "usuario_erp" =>$cliente
        ]);
    }
    public function provincia($id_departamento)
    {
        $provincia = Provincia::where('id_dpto',$id_departamento)->get();
        if ($provincia) {
            return response()->json([
                "success"=>true,
                "status"=>200,
                "data"=>$provincia,
            ]);
        }else{
            return response()->json([
                "success"=>false,
                "status"=>404,
            ]);
        }

    }
    public function distrito($id_provincia)
    {
        $distrito = Distrito::where('id_prov',$id_provincia)->get();
        if ($distrito) {
            return response()->json([
                "success"=>true,
                "status"=>200,
                "data"=>$distrito,
            ]);
        }else{
            return response()->json([
                "success"=>false,
                "status"=>404,
            ]);
        }
    }
    public function getCliente($id_cliente)
    {
        $cliente_gerencial = DB::table('gerencial.cliente')->where('estado',1)->where('id_cliente',$id_cliente)->first();
        $cliente_erp = Contribuyente::where('id_cliente_gerencial_old',$cliente_gerencial->id_cliente)->first();

        return response()->json([
            "success"=>true,
            "status"=>200,
            "data_old"=>$cliente_gerencial,
            "data"=>$cliente_erp
        ]);
    }
    public function getFactura($factura)
    {
        $factura = explode('-',$factura);
        $serie  = $factura[0];
        $numero = $factura[1];
        $factura = DB::table('almacen.doc_ven')->where('doc_ven.estado',1)->where('doc_ven.serie',$serie)->where('doc_ven.numero',$numero)
        ->select(
            'doc_ven.*',

        )
        ->join('almacen.doc_ven_det', 'doc_ven_det.id_doc', '=', 'doc_ven.id_doc_ven')
        ->join('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven_Det', '=', 'doc_ven_det.id_doc')
        ->join('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')
        ->orderByDesc('doc_ven.id_doc_ven')
        ->first();
        return response()->json([
            "success"=>true,
            "status"=>200,
            "data"=>$factura
        ]);
    }
    public function getRegistro($data, $tipo)
    {

        $cliente_gerencial = DB::table('almacen.requerimiento_logistico_view');
        if ($tipo==='oc') {
            $cliente_gerencial->where('requerimiento_logistico_view.nro_orden',$data);
        }
        if ($tipo === 'cdp') {
            $cliente_gerencial->where('requerimiento_logistico_view.codigo_oportunidad',$data);
        }
        $cliente_gerencial = $cliente_gerencial
        ->select(
            'requerimiento_logistico_view.id_moneda',
            'alm_req.id_requerimiento',
            'trans.id_transferencia',
            'guia_ven.id_guia_ven',
            'guia_ven_det.id_guia_ven_det',
            'doc_ven_det.id_doc_det',
            'doc_ven.id_doc_ven',
            'doc_ven.modena',
            'doc_ven.sub_total',
            'doc_ven.total_a_pagar',
            'doc_ven.fecha_registro',
            'doc_ven.fecha_emision',
            'doc_ven.credito_dias',

        )
        ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'requerimiento_logistico_view.id_requerimiento_logistico')
        ->join('almacen.trans', 'trans.id_requerimiento', '=', 'alm_req.id_requerimiento')
        ->join('almacen.guia_ven', 'guia_ven.id_transferencia', '=', 'trans.id_transferencia')
        ->join('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven', '=', 'guia_ven.id_guia_ven')
        ->join('almacen.doc_ven_det', 'doc_ven_det.id_guia_ven_det', '=', 'guia_ven_det.id_guia_ven_det')
        ->join('almacen.doc_ven', 'doc_ven.id_doc_ven','=','doc_ven_det.id_doc')
        ->first();
        if ($cliente_gerencial) {
            return response()->json([
                "success"=>true,
                "status"=>200,
                "data"=>$cliente_gerencial
            ]);
        }else{
            return response()->json([
                "success"=>false,
                "status"=>404,
                "data"=>[]
            ]);
        }

    }
    public function guardarRegistroCobranza(Request $request)
    {
        $data = $request;
        $empresa = Empresa::where('estado',1)->where('')->get();
        $cobranza = new Cobranza();

        $cobranza->id_empresa       = $request->empresa;
        $cobranza->id_sector        = $request->sector;
        $cobranza->id_cliente       = $request->cliente;
        $cobranza->factura          = $request->fact;
        $cobranza->uu_ee            = $request->ue;
        $cobranza->fuente_financ    = $request->ff;
        $cobranza->oc               = $request->oc;
        $cobranza->siaf             = $request->siaf;
        $cobranza->fecha_emision    = $request->fecha_emi;
        $cobranza->fecha_recepcion  = $request->fecha_rec;
        $cobranza->moneda           = $request->moneda;
        $cobranza->importe          = $request->importe;
        $cobranza->id_estado_doc    = $request->estado_doc;
        $cobranza->id_tipo_tramite  = $request->tramite;
        $cobranza->vendedor         = $request->nom_vendedor;
        // $cobranza->estado           = $request->empresa;
        $cobranza->fecha_registro   = date('Y-m-d H:i:s');
        $cobranza->id_area          = $request->area;
        $cobranza->id_periodo       = $request->periodo;
        $cobranza->ocam             = $request->ocam;
        // $cobranza->codigo_empresa   = $request->empresa;
        $cobranza->categoria        = $request->categ;
        $cobranza->cdp              = $request->cdp;
        $cobranza->plazo_credito    = $request->plazo_credito;
        // $cobranza->id_vent          = $request->empresa;

        $cobranza->save();
        return response()->json([
            "success"=>true,
            "status"=>200,
            "data"=>$request
        ]);
    }
}
