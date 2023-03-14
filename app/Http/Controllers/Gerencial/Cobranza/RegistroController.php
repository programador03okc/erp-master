<?php

namespace App\Http\Controllers\Gerencial\Cobranza;

use App\Exports\CobranzaPowerBIExport;
use App\Exports\CobranzasExpor;
use App\Gerencial\Cobranza as GerencialCobranza;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Gerencial\CobranzaAgil;
use App\Models\Administracion\Empresa as AdministracionEmpresa;
use App\Models\Administracion\Periodo;
use App\Models\almacen\DocumentoVenta;
use App\Models\almacen\DocVentReq;
use App\Models\Almacen\Requerimiento;
use App\Models\Comercial\Cliente as ComercialCliente;
use App\Models\Configuracion\Departamento;
use App\Models\Configuracion\Distrito;
use App\Models\Configuracion\Pais;
use App\Models\Configuracion\Provincia;
use App\Models\Configuracion\SisUsua;
use App\Models\Contabilidad\Contribuyente;
use App\Models\Contabilidad\TipoCuenta;
use App\models\Gerencial\AreaResponsable;
use App\models\Gerencial\Cliente;
use App\models\Gerencial\CobranzaFase;
use App\models\Gerencial\Cobranza;
use App\models\Gerencial\Empresa;
use App\models\Gerencial\EstadoDocumento;
use App\Models\Gerencial\Observaciones;
use App\Models\Gerencial\Penalidad;
use App\Models\Gerencial\PenalidadCobro;
use App\Models\Gerencial\ProgramacionPago;
use App\Models\Gerencial\RegistroCobranza;
use App\Models\Gerencial\RegistroCobranzaFase;
use App\Models\Gerencial\RegistroCobranzaOld;
use App\models\Gerencial\Sector;
use App\models\Gerencial\TipoTramite;
use App\Models\Gerencial\Vendedor;
use App\Models\mgcp\AcuerdoMarco\OrdenCompraPropias;
use App\Models\mgcp\OrdenCompra\Propia\Directa\OrdenCompraDirecta;
use App\Models\mgcp\OrdenCompra\Propia\OrdenCompraPropiaView;
use App\Models\Tesoreria\Empresa as TesoreriaEmpresa;
use App\Models\Tesoreria\TipoCambio;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;
use Yajra\DataTables\Facades\DataTables;

use function GuzzleHttp\json_encode;
ini_set('max_execution_time', '0');
class RegistroController extends Controller
{
    //
    public function registro()
    {
        $sector             = Sector::where('estado',1)->get();
        $tipo_ramite        = TipoTramite::where('estado',1)->get();
        $empresa            = DB::table('administracion.adm_empresa')
        ->select(
            'adm_empresa.id_contribuyente',
            'adm_empresa.id_empresa',
            'adm_empresa.codigo',
            'adm_contri.razon_social'
        )
        ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
        ->get();
        $periodo            = Periodo::where('estado',1)->get();
        $estado_documento   = EstadoDocumento::where('estado',1)->get();

        $pais = Pais::get();
        $departamento = Departamento::get();
        return view('gerencial.cobranza.registro',compact('sector','tipo_ramite','empresa','periodo','estado_documento', 'pais', 'departamento'));
    }

    public function listarRegistros(Request $request)
    {
        // $data = Cobranza::select('*')->orderBy('id_cobranza', 'desc');

        $data = RegistroCobranza::where('registros_cobranzas.estado',1)->select('registros_cobranzas.*')->orderBy('id_registro_cobranza', 'desc');
        if (!empty($request->empresa)) {
            $data = $data->where('registros_cobranzas.id_empresa',$request->empres);
        }
        if (!empty($request->estado)) {
            $data = $data->where('registros_cobranzas.id_estado_doc',$request->estado);
        }
        if (!empty($request->fase)) {
            $fase_text = $request->fase;
            $data = $data->join('cobranza.cobranza_fase', function ($join) use($fase_text){
                $join->on('cobranza_fase.id_registro_cobranza', '=', 'registros_cobranzas.id_registro_cobranza')
                    ->orOn('cobranza_fase.id_cobranza', '=', 'registros_cobranzas.id_cobranza_old');
            });
            $data->where('cobranza_fase.fase', 'like' ,'%'.$fase_text.'%')
            ->where('cobranza_fase.estado',1);
        }
        if (!empty($request->fecha_emision_inicio)) {
            $data = $data->where('registros_cobranzas.fecha_emision','>=',$request->fecha_emision_inicio);
        }
        if (!empty($request->fecha_emision_fin)) {
            $data = $data->where('registros_cobranzas.fecha_emision','<=',$request->fecha_emision_fin);
        }
        if (!empty($request->simbolo) && (int)$request->simbolo=== 1 ) {
            $importe = $request->importe!==''||$request->importe!==null?$request->importe:0;
            $data = $data->where('registros_cobranzas.importe','<',(int) $importe);
        }
        if (!empty($request->simbolo) && (int)$request->simbolo=== 2 ) {
            $importe = $request->importe!==''||$request->importe!==null?$request->importe:0;
            $data = $data->where('registros_cobranzas.importe','>',(int) $importe);
        }
        return DataTables::of($data)
        ->addColumn('empresa', function($data){

            $empresa = AdministracionEmpresa::find($data->id_empresa);
            return $empresa?$empresa->codigo:' ';
        })
        ->addColumn('cliente', function($data){

            // $com_cliente = ComercialCliente::find($data->id_cliente_agil);
            $com_cliente = ComercialCliente::find($data->id_cliente);
            $contribuyente = array();
            if ($com_cliente) {
                $contribuyente = Contribuyente::where('id_contribuyente',$com_cliente->id_contribuyente)->where('id_contribuyente','!=',null)->first();
            }

            return $contribuyente ? $contribuyente->razon_social:' ';
        })
        ->addColumn('atraso', function($data){
            return ($this->restar_fechas($data->fecha_recepcion, date('Y-m-d')) > 0) ? $this->restar_fechas($data->fecha_recepcion, date('Y-m-d')) : '0';
         })
        ->addColumn('moneda', function($data){
            return ($data->moneda == 1) ? 'S/' : 'US $';
        })
        ->addColumn('importe', function($data){
            return number_format($data->importe, 2);
        })
        ->addColumn('estado', function($data){
            $estado_documento_nombre = EstadoDocumento::where('id_estado_doc',$data->id_estado_doc)->first();
            return $estado_documento_nombre->nombre;
        })
        ->addColumn('area', function($data){
            $area_responsable_nombre = AreaResponsable::where('id_area',$data->id_area)->first();
            return $area_responsable_nombre->descripcion;
         })
        ->addColumn('fase', function($data) {
            // $fase = CobranzaFase::where('id_cobranza', $data->id_cobranza_old)->where('id_cobranza','!=',null)->where('estado',1)->first();
            // if (!$fase) {
            //     $fase = CobranzaFase::where('id_registro_cobranza', $data->id_registro_cobranza)->where('estado',1)->first();
            // }
            $fase = RegistroCobranzaFase::where('id_registro_cobranza',$data->id_registro_cobranza)->orderBy('id','DESC')->first();
            return ($fase?$fase->fase : '-');
        })
        ->make(true);
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
        // $data = Cliente::select('*')->orderBy('id_cliente', 'desc');
        // $data = Contribuyente::where('adm_contri.estado',1)
        // ->select(
        //     'adm_contri.*'
        // )
        // ->join('comercial.com_cliente', 'com_cliente.id_contribuyente', '=', 'adm_contri.id_contribuyente');
        // $data = Contribuyente::all();
        // $data = Contribuyente::where('estado',1);
        $data = Contribuyente::all();
        return DataTables::of($data)
        ->make(true);

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
        $cliente=[];
        $cliente_gerencial=[];
        $id_cliente_gerencial_old = 0;
        if (isset($request->nuevo_ruc_dni_cliente)) {
            $cliente = Contribuyente::where('nro_documento',$request->nuevo_ruc_dni_cliente)
            // ->where('razon_social',$request->nuevo_cliente)
            ->first();

            $cliente_gerencial = DB::table('gerencial.cliente')->where('estado',1)
            ->where('ruc',$request->nuevo_ruc_dni_cliente)
            // ->where('nombre',$request->nuevo_cliente)
            ->first();
        }
        if (isset($request->nuevo_cliente) && !$cliente) {
            $cliente = Contribuyente::
            // ->where('nro_documento',$request->nuevo_ruc_dni_cliente)
            where('razon_social',$request->nuevo_cliente)
            ->first();

            $cliente_gerencial = DB::table('gerencial.cliente')->where('estado',1)
            // ->where('ruc',$request->nuevo_ruc_dni_cliente)
            ->where('nombre',$request->nuevo_cliente)
            ->first();
        }
        if (isset($request->nuevo_cliente) && !$cliente_gerencial) {

            $cliente_gerencial = DB::table('gerencial.cliente')->where('estado',1)
            // ->where('ruc',$request->nuevo_ruc_dni_cliente)
            ->where('nombre',$request->nuevo_cliente)
            ->first();
        }
        // return response()->json([
        //     $cliente,
        //     $cliente_gerencial
        // ]);

        if (!$cliente_gerencial) {
            $gerencial_cliente = new Cliente();
            $gerencial_cliente->ruc = $request->nuevo_ruc_dni_cliente;
            $gerencial_cliente->nombre = $request->nuevo_cliente;
            $gerencial_cliente->estado = 1;
            $gerencial_cliente->save();

            $id_cliente_gerencial_old = $gerencial_cliente->id_cliente;
        }else{
            $id_cliente_gerencial_old = $cliente_gerencial->id_cliente;
        }



        if (!$cliente) {
            $cliente = new Contribuyente;
            $cliente->nro_documento     = $request->nuevo_ruc_dni_cliente;
            $cliente->razon_social      = $request->nuevo_cliente;
            $cliente->id_pais           = $request->pais;
            $cliente->estado            = 1;
            $cliente->fecha_registro    = date('Y-m-d H:i:s');
            $cliente->transportista     = false;

            $cliente->ubigeo            = $request->distrito;

            $cliente->id_cliente_gerencial_old    = $id_cliente_gerencial_old;
            $cliente->save();

            $com_cliente = new ComercialCliente();
            $com_cliente->id_contribuyente=$cliente->id_contribuyente;
            $com_cliente->estado=1;
            $com_cliente->fecha_registro = date('Y-m-d H:i:s');
            $com_cliente->save();
        }else{
            Contribuyente::where('id_contribuyente', $cliente->id_contribuyente)
            ->update(['id_cliente_gerencial_old' => $id_cliente_gerencial_old]);
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

        // return response()->json([$cliente_gerencial,$cliente_erp]);exit;
        // $departamento ='';
        $id_dis     = 0;
        $id_prov     = 0;
        $id_dpto     = 0;

        $distrito     = [];
        $provincia     = [];

        if ($cliente_erp && $cliente_erp->ubigeo !==null && $cliente_erp->ubigeo !=='') {
            $distrito_first   = Distrito::where('id_dis',$cliente_erp->ubigeo)->first();
            $id_dis     = $cliente_erp->ubigeo;

            $provincia_first  = Provincia::where('id_prov',$distrito_first->id_prov)->first();
            $id_prov    = $provincia_first->id_prov;

            $distrito  = Distrito::where('id_prov',$id_prov)->get();
            $provincia  = Provincia::where('id_dpto',$provincia_first->id_dpto)->get();

            $id_dpto = $provincia_first->id_dpto;
        }

        return response()->json([
            "success"=>true,
            "status"=>200,
            "data_old"=>$cliente_gerencial,
            "data"=>$cliente_erp,
            "distrito"=>$distrito,
            "provincia"=>$provincia,
            "id_dis"=>$id_dis,
            "id_prov"=>$id_prov,
            "id_dpto"=>$id_dpto
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

    public function guardarRegistroCobranza(Request $request)
    {
        $data = $request;
        $empresa = DB::table('administracion.adm_empresa')->where('id_empresa',$request->empresa)->first();
        $cobranza = new RegistroCobranza();

        $cobranza->id_empresa       = $request->empresa;
        $cobranza->id_sector        = $request->sector;

        // $cobranza->id_cliente       = (!empty($request->id_cliente) ? $request->id_cliente:null);
        $cobranza->id_cliente  = (!empty($request->id_contribuyente) ? $request->id_contribuyente:null) ;

        $cobranza->factura          = $request->fact;
        $cobranza->uu_ee            = $request->ue;
        $cobranza->fuente_financ    = $request->ff;
        $cobranza->ocam             = $request->oc; // OCAM es igul que la oc
        $cobranza->siaf             = $request->siaf;
        $cobranza->fecha_emision    = $request->fecha_emi;
        $cobranza->fecha_recepcion  = $request->fecha_rec;
        $cobranza->moneda           = $request->moneda;
        $cobranza->importe          = $request->importe;
        $cobranza->id_estado_doc    = $request->estado_doc;
        $cobranza->id_tipo_tramite  = $request->tramite;
        $cobranza->vendedor         = ($request->nom_vendedor?$request->nom_vendedor:null);
        $cobranza->estado           = 1;
        $cobranza->fecha_registro   = date('Y-m-d H:i:s');
        $cobranza->id_area          = $request->area;
        $cobranza->id_periodo       = $request->periodo;
        // $cobranza->ocam             = $request->ocam;
        $cobranza->codigo_empresa   = $empresa->codigo;
        $cobranza->categoria        = $request->categ;
        $cobranza->cdp              = $request->cdp;
        $cobranza->plazo_credito    = $request->plazo_credito;
        $cobranza->id_doc_ven       = $request->id_doc_ven;
        $cobranza->oc_fisica        = $request->orden_compra;
        $cobranza->inicio_entrega       = $request->fecha_inicio;
        $cobranza->fecha_entrega       = $request->fecha_entrega;
        // $cobranza->id_vent          = ;
        $cobranza->id_oc       = $request->id_oc;
        $cobranza->save();

        if ($cobranza) {
            $programacion_pago = new ProgramacionPago();
            $programacion_pago->id_registro_cobranza = $cobranza->id_registro_cobranza;
            $programacion_pago->fecha   = $request->fecha_ppago;
            $programacion_pago->estado  = 1;
            $programacion_pago->fecha_registro = date('Y-m-d H:i:s');
            $programacion_pago->save();
        }
        // uso de la formula de la penalidad
        if ($request->importe) {
            // return $request->atraso;exit;
            if (intval($request->dias_atraso)>0) {
                $formula_penalidad = (0.10*floatval($request->importe))/(0.4*intval($request->dias_atraso));

                $penalidad = new Penalidad();

                $penalidad->tipo                    = 'PENALIDAD';
                $penalidad->monto                   = $formula_penalidad;
                $penalidad->documento               = '--';
                $penalidad->fecha                   = date('Y-m-d');
                $penalidad->observacion             = 'PENALIDAD CALCULADA';
                $penalidad->estado                  = 1;
                $penalidad->fecha_registro          = date('Y-m-d H:i:s');
                $penalidad->id_registro_cobranza    = $cobranza->id_registro_cobranza;
                $penalidad->save();


            }

        }


        $busqueda = strpos(str_replace(' ','',$cobranza->ocam), 'DIRECTA');
        if ($busqueda !== false ) {


            OrdenCompraDirecta::where('nro_orden', str_replace(' ','',$cobranza->ocam))
            ->update(
                [
                    'factura'        => ($cobranza->factura!==''&&$cobranza->factura!=null? $cobranza->factura : ''),
                    'siaf'           => ($cobranza->siaf!==''&&$cobranza->siaf!=null? $cobranza->siaf : ''),
                    'orden_compra'   => ($cobranza->oc_fisica!==''&&$cobranza->oc_fisica!=null? $cobranza->oc_fisica : ''),
                ]
            );

        }
        $busqueda = strpos(str_replace(' ','',$cobranza->ocam), 'OCAM');
        if ($busqueda !== false ) {

            OrdenCompraPropias::where('orden_am', str_replace(' ','',$cobranza->ocam))
            ->update(
                [
                    'factura'        => ($cobranza->factura!==''&&$cobranza->factura!=null? $cobranza->factura : ''),
                    'siaf'           => ($cobranza->siaf!==''&&$cobranza->siaf!=null? $cobranza->siaf : ''),
                    'orden_compra'   => ($cobranza->oc_fisica!==''&&$cobranza->oc_fisica!=null? $cobranza->oc_fisica : ''),
                ]
            );

        }
        $nuevo = new RegistroCobranzaFase();
            $nuevo->id_registro_cobranza = $cobranza->id_registro_cobranza;
            $nuevo->fase = 'COMPROMISO';
            $nuevo->fecha = $cobranza->fecha_registro;
        $nuevo->save();

        return response()->json([
            "success"=>true,
            "status"=>200,
            "data"=>$cobranza
        ]);
    }

    public function actualizarDocVentReq()
    {
        $success=false;
        $status=404;
        $json_obtener_listado=[];
        $array_id=[];
        $obtener_listado = DB::table('almacen.alm_req')
        ->select(
            'alm_req.id_requerimiento',
            'alm_req.fecha_registro as fecha_registro_requerimiento',
            'alm_req.codigo',
            'alm_det_req.id_detalle_requerimiento',
            'doc_ven_det.id_doc_det',
            'doc_ven.id_doc_ven',
            'doc_ven.fecha_emision',
            'doc_ven.serie',
            'doc_ven.numero'
        )
        ->join('almacen.alm_det_req' , 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento')
        ->join('almacen.doc_ven_det' , 'doc_ven_det.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')
        ->join('almacen.doc_ven' , 'doc_ven.id_doc_ven', '=', 'doc_ven_det.id_doc')

        ->where('alm_req.enviar_facturacion','t')
        ->where('doc_ven.estado',1)
        // ->where('alm_req.traslado',1)
        ->get();

        // return response()->json($obtener_listado,200);exit;
        if (sizeof($obtener_listado)>0) {
            $success=true;
            $status=200;

            foreach ($obtener_listado as $key => $value) {
                if (!in_array($value->id_requerimiento, $array_id) )
                {
                    array_push($json_obtener_listado,(object) array(
                        "id_requerimiento"=>$value->id_requerimiento,
                        "id_doc_ven"=>$value->id_doc_ven,
                    ));
                    array_push($array_id,$value->id_requerimiento);
                }
            }
            foreach ($json_obtener_listado as $key => $value) {
                $doc_vent_req = new DocVentReq();
                $doc_vent_req->id_requerimiento = $value->id_requerimiento;
                $doc_vent_req->id_doc_venta = $value->id_doc_ven;
                $doc_vent_req->estado = 1;
                $doc_vent_req->save();

                DB::table('almacen.alm_req')->where('id_requerimiento',$value->id_requerimiento)->update([
                    'traslado'=>2
                ]);
            }

        }
        return response()->json([
            "success"=>$success,
            "status"=>$status,
            "data"=>$json_obtener_listado
        ]);
    }

    public function listarVentasProcesas()
    {
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
            'requerimiento_logistico_view.id_requerimiento_logistico',
            'requerimiento_logistico_view.codigo_oportunidad',
            'requerimiento_logistico_view.nro_orden',
            'doc_vent_req.id_documento_venta_requerimiento',
            'doc_ven.id_doc_ven',
            // 'doc_ven_det.id_doc_det',
            'doc_ven.serie',
            'doc_ven.numero',
            'doc_ven.fecha_emision',
            'doc_ven.credito_dias',
            'doc_ven.total_a_pagar',
            // 'doc_ven.modena'

        )
        ->join('almacen.doc_vent_req', 'doc_vent_req.id_requerimiento', '=', 'requerimiento_logistico_view.id_requerimiento_logistico')
        ->join('almacen.doc_ven', 'doc_ven.id_doc_ven', '=', 'doc_vent_req.id_doc_venta');
        // ->join('almacen.doc_ven_det', 'doc_ven_det.id_doc', '=', 'doc_ven.id_doc_ven');
        return datatables($cliente_gerencial)->toJson();
    }

    public function selecconarRequerimiento($id_requerimiento)
    {
        $cliente_gerencial = DB::table('almacen.requerimiento_logistico_view')
        ->where('requerimiento_logistico_view.id_requerimiento_logistico',$id_requerimiento)
        ->select(
            'requerimiento_logistico_view.id_requerimiento_logistico',
            'requerimiento_logistico_view.codigo_oportunidad',
            'requerimiento_logistico_view.nro_orden',
            'requerimiento_logistico_view.id_contribuyente_cliente',
            'requerimiento_logistico_view.id_contribuyente_empresa',
            'doc_vent_req.id_documento_venta_requerimiento',
            'doc_ven.id_doc_ven',
            'doc_ven.serie',
            'doc_ven.numero',
            'doc_ven.fecha_emision',
            'doc_ven.credito_dias',
            'doc_ven.total_a_pagar',
            'adm_contri.nro_documento',
            'adm_contri.razon_social',
            'com_cliente.id_cliente'
        )
        ->join('almacen.doc_vent_req', 'doc_vent_req.id_requerimiento', '=', 'requerimiento_logistico_view.id_requerimiento_logistico')
        ->join('almacen.doc_ven', 'doc_ven.id_doc_ven', '=', 'doc_vent_req.id_doc_venta')

        ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'requerimiento_logistico_view.id_requerimiento_logistico')
        ->join('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
        ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')->first();

        $doc_ven = [];
        $oc_propias_view = DB::table('mgcp_ordenes_compra.oc_propias_view')->where('nro_orden',$cliente_gerencial->nro_orden)->first();

        if ($cliente_gerencial) {
            $doc_ven = DocumentoVenta::where('id_doc_ven', $cliente_gerencial->id_doc_ven)->first();
            return response()->json(["status"=>200, "success"=>true, "data"=>$cliente_gerencial, "factura"=>$doc_ven, "oc"=>$oc_propias_view ? $oc_propias_view:[]]);
        }else{
            return response()->json(["status"=>400, "success"=>false, "data"=>$cliente_gerencial, "factura"=>$doc_ven, "oc"=> $oc_propias_view ? $oc_propias_view : []]);
        }

    }

    public function scriptCliente()
    {
        $clientes_faltantes =array();
        $json_faltantes=array();

        DB::table('gerencial.cliente')->where('ruc',null)
        ->update(
            ['ruc' => 'undefined']
        );

        // return DB::table('gerencial.cliente')->where('ruc',null)->get();exit;

        $cliente = DB::table('gerencial.cliente')->where('ruc','!=',null)->get();
        foreach ($cliente as $key => $value) {
            $contri = DB::table('contabilidad.adm_contri')->where('nro_documento',$value->ruc)->first();
            if (!$contri) {
                $contri = DB::table('contabilidad.adm_contri')->where('razon_social',$value->nombre)->first();
            }

            if ($contri) {
                $update = Contribuyente::where('id_contribuyente',$contri->id_contribuyente)
                ->update(
                    [
                        'id_cliente_gerencial_old' => $value->id_cliente,
                    ]
                );

                DB::table('gerencial.cliente')->where('id_cliente',$value->id_cliente)
                ->update(
                    ['comparar' => 2]
                );
            }else{
                array_push($clientes_faltantes, $value);
            }

        }

        foreach ($clientes_faltantes as $key => $value) {
            if ($value->ruc!=='undefined' && strlen($value->ruc)===11) {
                // api de reniec en busca por el ruc
                $curl = curl_init();

                    curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.apis.net.pe/v1/ruc?numero='.$value->ruc,
                    // CURLOPT_URL => 'https://api.apis.net.pe/v1/ruc?numero=74250891',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: Bearer apis-token-3057.Bd6ln-qewOEgNxkqhR7p4purLtmCNFZ5'
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                $response = json_decode($response);
                // return response()->json([$response,empty($response->numeroDocumento)]);exit;
                // if ($value->ruc==='20341946531') {
                //     return response()->json([$response,empty($response->numeroDocumento)]);exit;
                // }

                // return response()->json([$response,$value]);exit;
                if (!empty($response->numeroDocumento)) {

                    // $ubigeo_distrito=[];
                    // if (!isset($response->distrito)) {

                    //     $ubigeo_distrito = Distrito::where('descripcion',$response->distrito)->first();
                    // }
                    $ubigeo_distrito = Distrito::where('descripcion',$response->distrito)->first();
                    $ubigeo=0;
                    if ($ubigeo_distrito) {
                        $ubigeo = $ubigeo_distrito->id_dis;
                    }else{
                        $ubigeo = $response->ubigeo;
                    }
                    $guardar_contribuyente = new Contribuyente;
                    $guardar_contribuyente->nro_documento   =$response->numeroDocumento;
                    $guardar_contribuyente->razon_social    =$response->nombre;
                    $guardar_contribuyente->ubigeo          =(int)$ubigeo;
                    $guardar_contribuyente->id_pais         =170;
                    $guardar_contribuyente->fecha_registro  =date('Y-m-d H:i:s');
                    $guardar_contribuyente->id_cliente_gerencial_old    =$value->id_cliente;
                    $guardar_contribuyente->estado          =1;
                    $guardar_contribuyente->transportista   ='f';
                    $guardar_contribuyente->save();

                    DB::table('gerencial.cliente')->where('id_cliente',$value->id_cliente)
                    ->update(
                        ['comparar' => 2]
                    );
                }else{
                    array_push($json_faltantes, $value);
                }
            }else{
                // return response()->json($value);exit;
            }
        }
        return response()->json($json_faltantes);
    }

    public function editarCliente(Request $request)
    {
        if (isset($request->id_cliente)) {
            $cliente        = Cliente::find($request->id_cliente);
            $cliente->ruc   = $request->edit_ruc_dni_cliente;
            $cliente->save();
        }

        if (isset($request->id_contribuyente)) {
            $contribuyente =  Contribuyente::find($request->id_contribuyente);
            $contribuyente->id_pais         = $request->pais;
            $contribuyente->nro_documento   = $request->edit_ruc_dni_cliente;
            $contribuyente->ubigeo          = $request->distrito;
            $contribuyente->save();

        }
        return response()->json([
            "status"=>200,
            "success"=>true,
        ]);
    }

    public function scriptClienteRuc()
    {

        $array_clientes_razon_social = array(
            array("ruc"=>10070498575,"razon"=>"AGÜERO MASS LUIS SANTIAGO", "BASE"=>"AGÜERO MASS LUIS SANTIAGO"),
            array("ruc"=>20553647291,"razon"=>"C & R ECOCLEAN MULTISERVICIOS S.A.C.", "BASE"=>"C R ECOCLEAN MULTISERVICIOS S.A.C."),
            array("ruc"=>20520755307,"razon"=>"CAMED COMUNICACIONES S.A.C.", "BASE"=>"CAMED COMUNICACIONES S.A.C."),
            array("ruc"=>20538298485,"razon"=>"CENTRO NACIONAL DE ABASTECIMIENTO DE RECURSOS ESTRATEGICOS EN SALUD", "BASE"=>"CENTRO NACIONAL DE ABASTECIMIENTO DE RECURSOS ESTRATEGICOS EN SALUD"),
            array("ruc"=>20606780347,"razon"=>"CENTRO PROMOTOR DE SALUD P & G SOCIEDAD COMERCIAL DE RESPONSABILIDAD LIMITADA", "BASE"=>"CENTRO PROMOTOR DE SALUD P "),
            array("ruc"=>20339267821,"razon"=>"COMIS.NAC.PARA DESAR.Y VIDA SIN DROGAS", "BASE"=>"COMISIÓN NACIONAL PARA EL DESARROLLO Y VIDA SIN DROGAS - DEVIDA"),
            array("ruc"=>20455494967,"razon"=>"CONSULTORIA & MONITOREO PERU S.A.C. ", "BASE"=>"CONSULTORIA "),
            array("ruc"=>20604565406,"razon"=>"CORPORACION ARIDEL S.A.C.", "BASE"=>"CORPORACION ARIDEL S.A.C."),
            array("ruc"=>20166236950,"razon"=>"DIRECCION REGIONAL DE EDUCACION DE MOQUEGUA", "BASE"=>"DIRECCION REGIONAL DE EDUCACION DE MOQUEGUA"),
            array("ruc"=>20146045881,"razon"=>"DIRECCION REGIONAL DE SALUD HUANUCO", "BASE"=>"DIRECCION REGIONAL DE SALUD - HUANUCO - GRH"),
            array("ruc"=>20262221335,"razon"=>"EMPRESA DE GENERACION ELECTRICA SAN GABAN S.A.", "BASE"=>"EMPRESA DE GENERACION ELECTRICA SAN GABAN S.A."),
            array("ruc"=>20100164958,"razon"=>"EMPRESA MUNICIPAL DE MERCADOS S.A.", "BASE"=>"EMPRESA MUNICIPAL DE MERCADOS S.A."),
            array("ruc"=>20523421981,"razon"=>"GRUPO LOGISTICO ECONOMICO Y FINANCIERO DEL PERU SOCIEDAD ANONIMA CERRADA - GLEF PERU SAC", "BASE"=>"GLEF PERU SAC"),
            array("ruc"=>20162086716,"razon"=>"DIRECCION REGIONAL DE SALUD DE LIMA", "BASE"=>"GOBIERNO REGIONAL DE LIMA - DIRECCION DE SALUD III  LIMA NORTE"),
            array("ruc"=>20602754104,"razon"=>"GRUPO TASPAC EMPRESA INDIVIDUAL DE RESPONSABILIDAD LIMITADA", "BASE"=>"GRUPO TASPAC E.I.R.L."),
            array("ruc"=>20514772194,"razon"=>"HOSPITAL MUNICIPAL LOS OLIVOS", "BASE"=>"HOSPITAL MUNICIPAL LOS OLIVOS"),
            array("ruc"=>20600444531,"razon"=>"I.T.V. CAMBRIDGE S.A.C.", "BASE"=>"I.T.V. CAMBRIDGE S.A.C."),
            array("ruc"=>20399849382,"razon"=>"INPE-DIRECCION REGIONAL SUR ORIENTE CUSCO", "BASE"=>"INPE-DIRECCION REGIONAL SUR ORIENTE CUSCO"),
            array("ruc"=>20131366885,"razon"=>"INTENDENCIA NACIONAL DE BOMBEROS DEL PERU O INBP", "BASE"=>"INTENDENCIA NACIONAL DE BOMBEROS DEL PERU O INBP"),
            array("ruc"=>20341946531,"razon"=>"L.C. GROUP S.A.C.", "BASE"=>"LC GROUP S.A.C."),
            array("ruc"=>20602878083,"razon"=>"MEGATECSA SOCIEDAD ANÓNIMA CERRADA - MEGATECSA S.A.C.", "BASE"=>"MEGATECSA S.A.C."),
            array("ruc"=>20555546841,"razon"=>"MUNDIMEDIA SAC", "BASE"=>"MUNDIMEDIA SAC"),
            array("ruc"=>20147796715,"razon"=>"MUNICIPALIDAD DISTRITAL DE ALTO DE LA ALIANZA", "BASE"=>"MUNICIPALIDAD DISTRITAL DE ALTO DE LA ALIANZA"),
            array("ruc"=>20163611512,"razon"=>"MUNICIPALIDAD DISTRITAL DE MIRAFLORES", "BASE"=>"MUNICIPALIDAD DISTRITAL DE AREQUIPA"),
            array("ruc"=>20172022279,"razon"=>"MUNICIPALIDAD DISTRITAL DE CARMEN DE LA LEGUA REYNOSO", "BASE"=>"MUNICIPALIDAD DISTRITAL DE CARMEN DE LA LEGUA REYNOSO"),
            array("ruc"=>20312108284,"razon"=>"MUNICIP DIST JOSE L BUSTAMANTE Y RIVERO", "BASE"=>"MUNICIPALIDAD DISTRITAL DE JOSE LUIS BUSTAMANTE Y RIVERO"),
            array("ruc"=>20176249111,"razon"=>"MUNICIPALIDAD DISTRITAL DE KAÑARIS", "BASE"=>"MUNICIPALIDAD DISTRITAL DE KAÑARIS"),
            array("ruc"=>20143114911,"razon"=>"MUNICIP.DISTRIT.DE SAN JUAN BAUTISTA", "BASE"=>"MUNICIPALIDAD DISTRITAL DE SAN JUAN BAUTISTA"),
            array("ruc"=>20154432516,"razon"=>"MUNICIPALIDAD DISTRITAL SANTIAGO", "BASE"=>"MUNICIPALIDAD DISTRITAL DE SANTIAGO - CUSCO"),
            array("ruc"=>20170327391,"razon"=>"MUNICIPALIDAD DISTRITAL DE VILCABAMBA", "BASE"=>"MUNICIPALIDAD DISTRITAL DE VILCABAMBA - LA CONVENCION"),
            array("ruc"=>10447347763,"razon"=>"VASQUEZ MOQUILLAZA NATALY CAROLINA", "BASE"=>"NATALY VASQUEZ MOQUILLAZA"),
            array("ruc"=>20470145901,"razon"=>"NEXSYS DEL PERU S.A.C.", "BASE"=>"NEXSYS DEL PERU S.A.C."),
            array("ruc"=>20522224783,"razon"=>"ORGANISMO DE SUPERVISION DE LOS RECURSOS FORESTALES Y DE FAUNA SILVESTRE - OSINFOR", "BASE"=>"ORGANISMO DE SUPERVISION DE LOS RECURSOS FORESTALES Y DE FAUNA SILVESTRE"),
            array("ruc"=>20565423372,"razon"=>"ORGANISMO TÉCNICO DE LA ADMINISTRACIÓN DE LOS SERVICIOS DE SANEAMIENTO-OTASS", "BASE"=>"ORGANISMO TECNICO DE LA ADMINISTRACION DE LOS SERVICIOS DE SANEAMIENTO-OTASS"),
            array("ruc"=>20511366594,"razon"=>"UNIDAD DE COORDINACION DE PROYECTOS DEL PODER JUDICIAL", "BASE"=>"PODER JUDICIAL - UNIDAD DE COORDINACION DE PROYECTOS DEL PODER JUDICIAL"),
            array("ruc"=>20550154065,"razon"=>"PROGRAMA NACIONAL DE ALIMENTACIÓN ESCOLAR QALI WARMA", "BASE"=>"PROGRAMA NACIONAL DE ALIMENTACION ESCOLAR QALI WARMA"),
            array("ruc"=>20530015999,"razon"=>"QUIMERA FISH SOCIEDAD ANONIMA CERRADA - QUIMERA FISH S.A.C.", "BASE"=>"QUIMERA FISH S.A.C."),
            array("ruc"=>20602467971,"razon"=>"REGION POLICIAL AYACUCHO - ICA", "BASE"=>"REGION POLICIAL AYACUCHO - ICA"),
            array("ruc"=>20337101276,"razon"=>"SERVICIO DE ADMINISTRACION TRIBUTARIA", "BASE"=>"SERVICIO DE ADMINISTRACION TRIBUTARIA - LIMA"),
            array("ruc"=>20131366028,"razon"=>"SERVICIO NACIONAL METEOREOLOGIA E HIDROL.", "BASE"=>"SERVICIO NACIONAL METEOREOLOGIA E HIDROL."),
            array("ruc"=>20158219655,"razon"=>"SUPERINTENDENCIA NAC.SERV.DE SANEAMIENTO", "BASE"=>"SUPERINTENDENCIA NACIONAL DE SERVICIOS DE SANEAMIENTO"),
            array("ruc"=>20600244605,"razon"=>"TRANSPORTE TERRAPERU SAC", "BASE"=>"TRANSPORTE TERRAPERU SAC"),
            array("ruc"=>20607706957,"razon"=>"UE 005: PROGRAMA MEJORAMIENTO DE LOS SERVICIOS DE JUSTICIA EN MATERIA PENAL EN EL PERÚ - PMSJMPP", "BASE"=>"UE 005: PROGRAMA MEJORAMIENTO DE LOS SERVICIOS DE JUSTICIA EN MATERIA PENAL EN EL PERU - PMSJMPP"),
            array("ruc"=>20344832138,"razon"=>"UNIDAD DE GESTION EDUCATIVA LOCAL # 01", "BASE"=>"UNIDAD DE GESTION EDUCATIVA LOCAL 01"),
            array("ruc"=>20285139415,"razon"=>"ZONA REGISTRAL Nø III SEDE MOYOBAMBA", "BASE"=>"ZONA REGISTRAL III - SEDE MOYOBAMBA")
        );

        $clientes_faltates=array();
        $clientes_cambiados=array();
        foreach ($array_clientes_razon_social as $key => $value) {
            $cliente = DB::table('gerencial.cliente')->where('nombre','=',$value['BASE'])->first();
            if (!$cliente) {
                $cliente = DB::table('gerencial.cliente')->where('nombre','=',$value['razon'])->first();
            }


            if (!$cliente) {
                array_push($clientes_faltates,$value);
            }else{
                // $cliente_cambio = Cliente::find($cliente->id_cliente);

                // $cliente_cambio->ruc = $value['ruc'];
                // $cliente_cambio->nombre = $value['razon'];

                // $cliente_cambio->save();
                DB::table('gerencial.cliente')->where('nombre',$cliente->nombre)
                ->update([
                    'ruc' => $value['ruc'],
                    'nombre' => $value['razon']
                ]);
                array_push($clientes_cambiados,$cliente);
            }
        }

        return response()->json([
            "succes"=>true,
            "status"=>200,
            "data"=>$clientes_faltates,
            "encontrados"=>$clientes_cambiados
        ]);
    }

    public function editarRegistro($id)
    {
        $cliente_array=array();
        $registro_cobranza = RegistroCobranza::where('id_registro_cobranza',$id)->first();

        $vendedor=[];
        if (intval($registro_cobranza->vendedor)>0 && $registro_cobranza->vendedor!==null) {
            $vendedor = Vendedor::where('id_vendedor',$registro_cobranza->vendedor)->first();
        }

        if (!$vendedor && $registro_cobranza->vendedor!==null) {
            $vendedor = Vendedor::where('nombre','like','%'.$registro_cobranza->vendedor.'%')->first();
        }
        // return $vendedor;exit;
        $contribuyente = array();
        $comercial_cliente = ComercialCliente::find($registro_cobranza->id_cliente);
        if ($comercial_cliente) {
            $contribuyente = Contribuyente::where('id_contribuyente',$comercial_cliente->id_contribuyente)->first();
        }
        // return $registro_cobranza->id_registro_cobranza;exit;
        $programacion_pago = ProgramacionPago::where('id_registro_cobranza',$registro_cobranza->id_registro_cobranza)
        ->where('estado',1)
        ->orWhere('id_cobranza',$registro_cobranza->id_cobranza_old)
        ->orderBy('id_programacion_pago','desc')
        ->first();
        return response()->json([
            "status"=>200,
            "success"=>true,
            "data"=>$registro_cobranza,
            "programacion_pago"=>$programacion_pago,
            "cliente"=>$contribuyente,
            "vendedor"=>$vendedor?$vendedor:[]
        ]);
    }

    public function modificarRegistro(Request $request)
    {
        // dd($request->all());
        // exit();
        DB::beginTransaction();
        try {
            $empresa = Empresa::find($request->empresa);

            $cobranza = RegistroCobranza::find($request->id_registro_cobranza);
                $cobranza->id_empresa       = $request->empresa;
                $cobranza->id_sector        = $request->sector;
                $cobranza->id_cliente       = (!empty($request->id_contribuyente) ? $request->id_contribuyente:null);
                $cobranza->factura          = $request->fact;
                $cobranza->uu_ee            = $request->ue;
                $cobranza->fuente_financ    = $request->ff;
                $cobranza->ocam             = $request->oc; // OCAM es igul que la oc
                $cobranza->siaf             = $request->siaf;
                $cobranza->fecha_emision    = $request->fecha_emi;
                $cobranza->fecha_recepcion  = $request->fecha_rec;
                $cobranza->moneda           = $request->moneda;
                $cobranza->importe          = $request->importe;
                $cobranza->id_estado_doc    = $request->estado_doc;
                $cobranza->id_tipo_tramite  = $request->tramite;
                $cobranza->vendedor         = ($request->nom_vendedor?$request->nom_vendedor:null);
                $cobranza->estado           = 1;
                $cobranza->id_area          = $request->area;
                $cobranza->id_periodo       = $request->periodo;
                $cobranza->codigo_empresa   = $empresa->codigo;
                $cobranza->categoria        = $request->categ;
                $cobranza->cdp              = $request->cdp;
                $cobranza->plazo_credito    = $request->plazo_credito;
                $cobranza->id_doc_ven       = $request->id_doc_ven;
                $cobranza->oc_fisica       = $request->orden_compra;
                $cobranza->inicio_entrega       = $request->fecha_inicio;
                $cobranza->fecha_entrega       = $request->fecha_entrega;
                $cobranza->id_oc       = $request->id_oc;
            $cobranza->save();

            if ($cobranza) {
                $programacion_pago = ProgramacionPago::where('id_registro_cobranza', $cobranza->id_registro_cobranza)->first();
                if ($programacion_pago) {
                    $programacion_pago->fecha   = $request->fecha_ppago;
                    $programacion_pago->estado  = 1;
                    $programacion_pago->save();
                }else{
                    $programacion_pago = new ProgramacionPago();
                    $programacion_pago->id_registro_cobranza = $cobranza->id_registro_cobranza;
                    $programacion_pago->fecha   = $request->fecha_ppago;
                    $programacion_pago->estado  = 1;
                    $programacion_pago->fecha_registro = date('Y-m-d H:i:s');
                    $programacion_pago->save();
                }
            }

            $ordenVista = 0;
            if ($cobranza->id_oc != null) {
                $ordenVista = OrdenCompraPropiaView::where('nro_orden', $request->oc)->orWhere('codigo_oportunidad', $request->cdp)->count();

                if ($ordenVista > 0) {
                    $busqueda = strpos($cobranza->ocam, 'DIRECTA');
                    if ($busqueda == true) {
                        OrdenCompraDirecta::where('nro_orden', rtrim($cobranza->ocam))
                        ->update([
                            'factura'        => (($cobranza->factura !== '') && ($cobranza->factura != null)) ? $cobranza->factura : '',
                            'siaf'           => (($cobranza->siaf !== '') && ($cobranza->siaf != null)) ? $cobranza->siaf : '',
                            'orden_compra'   => (($cobranza->oc_fisica !== '') && ($cobranza->oc_fisica != null )) ? $cobranza->oc_fisica : '',
                        ]);
                    }
                    $busqueda = strpos($cobranza->ocam, 'OCAM');
                    if ($busqueda == true) {
                        OrdenCompraPropias::where('orden_am', rtrim($cobranza->ocam))
                        ->update([
                            'factura'        => (($cobranza->factura !== '') && ($cobranza->factura != null)) ? $cobranza->factura : '',
                            'siaf'           => (($cobranza->siaf !== '') && ($cobranza->siaf != null)) ? $cobranza->siaf : '',
                            'orden_compra'   => (($cobranza->oc_fisica !== '') && ($cobranza->oc_fisica != null )) ? $cobranza->oc_fisica : '',
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(["success" => true, "status" => 200, "data" => $cobranza, "pago" => $programacion_pago, "view" => $ordenVista]);
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json(["success" => true, "status" => 500, "error" => $ex]);
        }
    }

    public function obtenerFase($id)
    {
        // $registro_cobranza = RegistroCobranza::where('id_registro_cobranza',$id)->first();
        $cobranza_registro_fase = RegistroCobranzaFase::where('id_registro_cobranza',$id)->get();
        // return $registro_cobranza;
        return response()->json([
            "success"=>true,
            "status"=>200,
            "fases"=>$cobranza_registro_fase
        ]);
        // if ($registro_cobranza) {
        //     $cobranzas_fases = CobranzaFase::where('id_cobranza',$registro_cobranza->id_cobranza_old)->where('id_cobranza','!=',null)->where('estado','!=',0)->get();
        //     if (sizeof($cobranzas_fases)===0) {
        //         $cobranzas_fases = CobranzaFase::where('id_registro_cobranza',$registro_cobranza->id_registro_cobranza)->where('estado','!=',0)->get();
        //     }
        //     if (sizeof($cobranzas_fases)>0) {
        //         return response()->json([
        //             "success"=>true,
        //             "status"=>200,
        //             "fases"=>$cobranzas_fases
        //         ]);
        //     }else{
        //         return response()->json([
        //             "success"=>false,
        //             "status"=>404,
        //             "fases"=>null
        //         ]);
        //     }
        // }else{
        //     return response()->json([
        //         "success"=>false,
        //         "status"=>404,
        //         "fases"=>null
        //     ]);
        // }


    }

    public function guardarFase(Request $request)
    {
        $registro_cobranza = RegistroCobranza::where('id_registro_cobranza',$request->id_registro_cobranza)->first();
        // $cobranza_fase = CobranzaFase::where('id_cobranza',$registro_cobranza->id_cobranza_old)->first();
        DB::table('cobranza.cobranza_fase')
            ->where('id_registro_cobranza', $registro_cobranza->id_registro_cobranza)
            ->where('estado','!=', 0)
            ->update(['estado' => 2]);

        DB::table('cobranza.cobranza_fase')
            ->where('id_cobranza', $registro_cobranza->id_cobranza_old)
            ->where('estado','!=', 0)
            ->where('id_cobranza','!=' , null)
            ->update(['estado' => 2]);
        $cobranza_fase          = new CobranzaFase();
        if ($registro_cobranza) {
            $cobranza_fase->id_cobranza    = $registro_cobranza->id_cobranza_old;
        }

        $cobranza_fase->fase    = $request->fase;
        $cobranza_fase->fecha   = $request->fecha_fase;
        $cobranza_fase->fecha_registro  = date('Y-m-d H:i:s');
        $cobranza_fase->estado  = 1;
        $cobranza_fase->id_registro_cobranza  = $request->id_registro_cobranza;
        $cobranza_fase->save();

        $nuevo = new RegistroCobranzaFase();
            $nuevo->id_registro_cobranza = $request->id_registro_cobranza;
            $nuevo->fase = $request->fase;
            $nuevo->fecha = $request->fecha_fase;
        $nuevo->save();

        return response()->json([
            "success"=>true,
            "status"=>200,
        ]);
    }

    public function eliminarFase(Request $request)
    {
        // $cobranza_fase = CobranzaFase::find($request->id);
        // $cobranza_fase->estado = 0;
        // $cobranza_fase->save();

        $registro_cobranza_fase = RegistroCobranzaFase::find($request->id);
        $registro_cobranza_fase->delete();
        // if ($cobranza_fase) {
        //     return response()->json([
        //         "success"=>true,
        //         "status"=>200,
        //         "data"=>$cobranza_fase
        //     ]);
        // }else{
        //     return response()->json([
        //         "success"=>false,
        //         "status"=>404,
        //     ]);
        // }
        return response()->json([
            "success"=>true,
            "status"=>200,
            // "data"=>$cobranza_fase
        ]);
    }

    public function scriptEmpresa()
    {
        // return $empresa_agil = Contribuyente::where('nro_documento',10804138582)->first();exit;
        $empresa_gerencial = Empresa::where('estado',1)->get();
        // $empresa_agil      = DB::table('administracion.adm_empresa')
        // ->select(
        //     'adm_empresa.id_contribuyente',
        //     'adm_empresa.codigo',
        //     'adm_contri.razon_social'
        // )
        // ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
        // ->get();
        $encontrados = array();
        $faltantes = array();
        $encontrados_administracion = array();
        $faltantes_administracion = array();
        foreach ($empresa_gerencial as $key => $value) {
            $empresa_agil = Contribuyente::where('nro_documento',$value->ruc)->first();
            if (!$empresa_agil) {
                $empresa_agil = Contribuyente::where('razon_social',$value->nombre)->first();
            }

            if ($empresa_agil) {

                array_push($encontrados,$empresa_agil);
                $editar_empresa = Contribuyente::find($empresa_agil->id_contribuyente);
                $editar_empresa->id_empresa_gerencial_old = $value->id_empresa;
                $editar_empresa->save();

                RegistroCobranza::where('id_empresa_old', $value->id_empresa)
                ->update(['id_empresa' => $empresa_agil->id_contribuyente]);

                $administracion_empresa = DB::table('administracion.adm_empresa')->where('id_contribuyente',$empresa_agil->id_contribuyente)->first();

                if (!$administracion_empresa) {
                    array_push($faltantes_administracion,$administracion_empresa);
                }else{
                    array_push($encontrados_administracion,$administracion_empresa);
                }
            }else{
                // return 'else';exit;
                // return $empresa_agil;exit;
                // return $empresa_agil = Contribuyente::where('nro_documento',$value->ruc)->first();exit;
                array_push($faltantes,$value);

                $guardar_contribuyente = new Contribuyente;
                $guardar_contribuyente->nro_documento   =$value->ruc;
                $guardar_contribuyente->razon_social    =$value->nombre;
                $guardar_contribuyente->ubigeo          =0;
                $guardar_contribuyente->id_pais         =170;
                $guardar_contribuyente->fecha_registro  =date('Y-m-d H:i:s');
                $guardar_contribuyente->id_empresa_gerencial_old    =$value->id_empresa;
                $guardar_contribuyente->estado          =1;
                $guardar_contribuyente->transportista   ='f';
                $guardar_contribuyente->save();

                $guardar_adm_empresa = new AdministracionEmpresa();
                $guardar_adm_empresa->id_contribuyente  = $guardar_contribuyente->id_contribuyente;
                $guardar_adm_empresa->codigo            = $value->codigo;
                $guardar_adm_empresa->estado            = 1;
                $guardar_adm_empresa->fecha_registro    = date('Y-m-d H:i:s');
                $guardar_adm_empresa->logo_empresa      = ' ';
                $guardar_adm_empresa->save();

                RegistroCobranza::where('id_empresa_old', $value->id_empresa)
                ->update(['id_empresa' => $guardar_contribuyente->id_contribuyente]);
            }

        }
        return response()->json([
            "success"=>true,
            "status"=>200,
            // "gerencial"=>$empresa_gerencial,
            // "encontrados"=>$encontrados,
            // "faltantes"=>$faltantes,
            "encontrados"=>$encontrados_administracion,
            "faltantes"=>$faltantes_administracion,
            // "agil"=>$empresa_agil
        ]);
    }

    public function scriptFase()
    {
        $cobranza_fase_id_cobranza = DB::table('cobranza.cobranza_fase')
        ->select('id_cobranza')
        ->where('id_cobranza','!=',null)
        ->where('estado',1)
        ->orderBy('id_cobranza','DESC')
        ->groupBy('id_cobranza')
        ->get();
        $array_id_conbranza = [];
        foreach ($cobranza_fase_id_cobranza as $key => $value) {
            array_push($array_id_conbranza,$value->id_cobranza);
        }
        $array_cambios=array();
        foreach ($array_id_conbranza as $key => $value) {
            $cobranza_fase = CobranzaFase::where('id_cobranza',$value)->where('estado',1)->orderBy('id_fase','DESC')->get();
            foreach ($cobranza_fase as $key => $value) {
                if ($key!==0) {
                    DB::table('cobranza.cobranza_fase')
                    ->where('id_fase', $value->id_fase)
                    ->update(['estado' => 2]);
                }
            }
        }
        return response()->json([
            "success"=>true,
            "status"=>200,
            // "fase"=>$array_cambios,
            "id"=>$array_id_conbranza
        ]);
    }

    public function guardarPenalidad(Request $request)
    {

        $penalidad =array();
        $registro_cobranza = RegistroCobranza::find($request->id_cobranza_penal);


        if (intval($request->id) === 0) {
            $penalidad = new Penalidad();
            $penal_est = ($request->tipo_penal == 'PENALIDAD') ? 'APLICADA' :'ELABORADO';
        }else{
            $penalidad = Penalidad::find($request->id);
        }
        // return $penalidad;exit;
        $penalidad->tipo            = $request->tipo_penal;
        $penalidad->monto           = $request->importe_penal;
        $penalidad->documento       = $request->doc_penal;
        $penalidad->fecha           = $request->fecha_penal;
        $penalidad->observacion     = $request->obs_penal;
        $penalidad->estado          = 1;
        $penalidad->fecha_registro  = date('Y-m-d H:i:s');
        $penalidad->id_registro_cobranza  = $request->id_cobranza_penal;
        $penalidad->id_oc  = $registro_cobranza->id_oc;
        if ($request->id == 0) {
            $penalidad->estado_penalidad = $penal_est;
        }
        $penalidad->save();
        return response()->json(["status"=>200, "success"=>true, "data" => $penalidad]);
    }

    public function obtenerPenalidades(Request $request)
    {
        $registro_cobranza = RegistroCobranza::where('id_registro_cobranza', $request->id)->first();
        $array_penalidades = array();
        // return $registro_cobranza;exit;
        $penalidad_cobranza = Penalidad::where('id_cobranza', $registro_cobranza->id_cobranza_old)
                            ->where('tipo', $request->tipo)->where('id_cobranza','!=',null)->where('estado','!=',7)
                            ->orderBy('fecha_registro', 'desc')->get();
        $penalidad_registro = Penalidad::where('id_registro_cobranza', $request->id)
                            ->where('tipo', $request->tipo)->where('estado','!=',7)
                            ->orderBy('fecha_registro', 'desc')->get();

        if (sizeof($penalidad_cobranza)>0) {
            foreach ($penalidad_cobranza as $key => $value) {
                array_push($array_penalidades,array(
                    "id_penalidad"=>$value->id_penalidad,
                    "id_cobranza"=>$value->id_cobranza,
                    "tipo"=>$value->tipo,
                    "monto"=>$value->monto,
                    "documento"=>$value->documento,
                    "fecha"=>$value->fecha,
                    "observacion"=>$value->observacion,
                    "estado"=>$value->estado,
                    "fecha_registro"=>$value->fecha_registro,
                    "id_registro_cobranza"=>$value->id_registro_cobranza,
                    "id_oc"=>$value->id_oc,
                    "estado_penalidad"=>$value->estado_penalidad,
                ));
            }
        }
        if (sizeof($penalidad_registro)>0) {
            foreach ($penalidad_registro as $key => $value) {
                array_push($array_penalidades,array(
                    "id_penalidad"=>$value->id_penalidad,
                    "id_cobranza"=>$value->id_cobranza,
                    "tipo"=>$value->tipo,
                    "monto"=>$value->monto,
                    "documento"=>$value->documento,
                    "fecha"=>$value->fecha,
                    "observacion"=>$value->observacion,
                    "estado"=>$value->estado,
                    "fecha_registro"=>$value->fecha_registro,
                    "id_registro_cobranza"=>$value->id_registro_cobranza,
                    "id_oc"=>$value->id_oc,
                    "estado_penalidad"=>$value->estado_penalidad,
                ));
            }
        }

        return response()->json([
            "success"=>true,
            "status"=>200,
            "penalidades"=>$array_penalidades
        ]);
    }

    public function buscarVendedor( Request $request)
    {
        $vendedor=[];
        if (!empty($request->searchTerm)) {
            $searchTerm=strtoupper($request->searchTerm);
            $vendedor = Vendedor::where('estado',1);
            if (!empty($request->searchTerm)) {
                $vendedor = $vendedor->where('nombre','like','%'.$searchTerm.'%');
            }
            $vendedor = $vendedor->get();
            return response()->json($vendedor);
        }else{
            return response()->json([
                "status"=>404,
                "success"=>false
            ]);
        }
    }

    public function eliminarRegistroCobranza($id_registro_cobranza)
    {
        $registro_cobranza = RegistroCobranza::find($id_registro_cobranza);
        $registro_cobranza->estado=0;
        $registro_cobranza->save();
        return response()->json([
            "success"=>true,
            "status"=>200
        ]);
    }

    public function buscarClienteSeleccionado($id)
    {
        // $contribuyente = Contribuyente::where('id_cliente_gerencial_old',$id)->where('id_cliente_gerencial_old','!=',null)->first();
        $contribuyente = Contribuyente::where('id_contribuyente',$id)->first();
        $cliente_gerencial=null;

        $com_cliente = ComercialCliente::where('id_contribuyente',$contribuyente->id_contribuyente)->first();
        if (!$com_cliente) {
            $com_cliente = new ComercialCliente();
            $com_cliente->id_contribuyente = $contribuyente->id_contribuyente;
            $com_cliente->estado = 1;
            $com_cliente->fecha_registro = date('Y-m-d H:i:s');
            $com_cliente->save();
        }
        $cliente=array(
            "razon_social"=>$contribuyente->razon_social,
            "id_contribuyente"=>$com_cliente->id_cliente,
        );
        return response()->json([
            "success"=>true,
            "status"=>200,
            "data"=>$cliente,
            "old"=>$cliente_gerencial
        ]);
    }

    public function scriptCobranza()
    {
        $cobranzas = DB::table('gerencial.cobranza')->get();

        $array = [];
        foreach ($cobranzas as $key => $value) {
            $success = true ;
            $registro_cobranza = RegistroCobranza::where('id_cobranza_old',$value->id_cobranza)->first();
            if (!$registro_cobranza) {
                $registro_cobranza = new RegistroCobranza();
                $success = true ;

                $registro_cobranza->id_empresa        = null;
                $registro_cobranza->id_sector         = $value->id_sector;
                // $registro_cobranza->id_cliente        = $value->id_cliente;
                $registro_cobranza->factura           = $value->factura;
                $registro_cobranza->uu_ee             = $value->uu_ee;
                $registro_cobranza->fuente_financ     = $value->fuente_financ;
                $registro_cobranza->ocam              = $value->ocam;
                $registro_cobranza->siaf              = $value->siaf;
                $registro_cobranza->fecha_emision     = $value->fecha_emision;
                $registro_cobranza->fecha_recepcion   = $value->fecha_recepcion;
                $registro_cobranza->moneda            = $value->moneda;
                $registro_cobranza->importe           = $value->importe;
                $registro_cobranza->id_estado_doc     = $value->id_estado_doc;
                $registro_cobranza->id_tipo_tramite   = $value->id_tipo_tramite;
                $registro_cobranza->vendedor          = $value->vendedor;
                $registro_cobranza->estado            = $value->estado;
                $registro_cobranza->fecha_registro    = $value->fecha_registro;
                $registro_cobranza->id_area           = $value->id_area;
                $registro_cobranza->id_periodo        = $value->id_periodo;
                $registro_cobranza->codigo_empresa    = $value->codigo_empresa;
                $registro_cobranza->categoria         = $value->categoria;
                $registro_cobranza->cdp               = $value->cdp;
                $registro_cobranza->plazo_credito     = $value->plazo_credito;
                $registro_cobranza->id_doc_ven       = $value->id_venta;
                // $registro_cobranza->id_cliente_agil   = null;
                $registro_cobranza->id_cobranza_old   = $value->id_cobranza;
                $registro_cobranza->id_empresa_old    = $value->id_empresa;
                $registro_cobranza->oc_fisica        = $value->oc;
                $registro_cobranza->save();

                if ($registro_cobranza) {
                    $programaciones_pagos = DB::table('gerencial.programacion_pago')->where('id_cobranza',$value->id_cobranza)->get();
                    foreach ($programaciones_pagos as $key_programaciones_pagos => $value_programaciones_pagos) {
                        $programacion_pago = new ProgramacionPago();
                        $programacion_pago->id_registro_cobranza    = $registro_cobranza->id_registro_cobranza;
                        $programacion_pago->fecha                   = $value_programaciones_pagos->fecha;
                        $programacion_pago->estado                  = $value_programaciones_pagos->estado;
                        $programacion_pago->fecha_registro          = $value_programaciones_pagos->fecha_registro;
                        $programacion_pago->id_cobranza             = $value_programaciones_pagos->id_cobranza;
                        $programacion_pago->save();
                    }

                    $cobranzas_fases = DB::table('gerencial.cobranza_fase')->where('id_cobranza',$value->id_cobranza)->get();
                    foreach ($cobranzas_fases as $key_cobranzas_fases => $value_cobranzas_fases) {
                        $cobranza_fase = new CobranzaFase();
                        $cobranza_fase->id_registro_cobranza    = $registro_cobranza->id_registro_cobranza;
                        $cobranza_fase->fase                    = $value_cobranzas_fases->fase;
                        $cobranza_fase->fecha                   = $value_cobranzas_fases->fecha;
                        $cobranza_fase->estado                  = $value_cobranzas_fases->estado;
                        $cobranza_fase->fecha_registro          = $value_cobranzas_fases->fecha_registro;
                        $cobranza_fase->id_cobranza             = $value_cobranzas_fases->id_cobranza;
                        $cobranza_fase->save();
                    }

                    $penalidades = DB::table('gerencial.penalidad')->where('id_cobranza',$value->id_cobranza)->get();
                    foreach ($penalidades as $key_penalidades => $value_penalidades ) {
                        $cobranza_fase = new Penalidad();
                        $cobranza_fase->id_registro_cobranza    = $registro_cobranza->id_registro_cobranza;
                        $cobranza_fase->tipo                    = $value_penalidades->tipo;
                        $cobranza_fase->monto                   = $value_penalidades->monto;
                        $cobranza_fase->documento               = $value_penalidades->documento;
                        $cobranza_fase->fecha                   = $value_penalidades->fecha;
                        $cobranza_fase->observacion             = $value_penalidades->observacion;
                        $cobranza_fase->estado                  = $value_penalidades->estado;
                        $cobranza_fase->fecha_registro          = $value_penalidades->fecha_registro;
                        $cobranza_fase->id_cobranza             = $value_penalidades->id_cobranza;
                        $cobranza_fase->save();
                    }

                    $observaciones = DB::table('gerencial.cobranza_obs')->where('id_cobranza',$value->id_cobranza)->get();
                    foreach ($observaciones as $key_observaciones => $value_observaciones ) {
                        $observacion = new Observaciones();
                        $observacion->descripcion       = $value_observaciones->observacion;
                        $observacion->cobranza_id       = $registro_cobranza->id_registro_cobranza;
                        // $observacion->usuario_id        = ;
                        // $observacion->oc_id             = ;
                        $observacion->estado            = $value_observaciones->estado;
                        $observacion->created_at        = $value_observaciones->fecha_registro;
                        $observacion->updated_at        = $value_observaciones->fecha_registro;
                        // $observacion->deleted_at        = $value_penalidades->fecha_registro;
                        $observacion->save();
                    }
                }
            }
            else{
                /*
                $registro_cobranza = RegistroCobranza::find($registro_cobranza->id_registro_cobranza);
                if ($value->factura!='--' && $value->factura!='-' && $value->factura!='---' && $value->factura!=null && $value->factura!='') {
                    $registro_cobranza->factura =$value->factura;
                }
                if ($value->siaf!='--' && $value->siaf!='-' && $value->siaf!='---' && $value->siaf!=null && $value->siaf!='') {
                    $registro_cobranza->siaf = $value->siaf;
                }
                if (
                    $value->oc!=='--' &&
                    $value->oc!='-' &&
                    $value->oc!=='---' &&
                    $value->oc!==null &&
                    $value->oc!==''
                ) {
                    $registro_cobranza->oc_fisica = $value->oc;
                }
                if ($value->fecha_emision!='--' && $value->fecha_emision!='-' && $value->fecha_emision!='---' && $value->fecha_emision!=null && $value->fecha_emision!='') {
                    $registro_cobranza->fecha_emision = $value->fecha_emision;
                }
                if ($value->fecha_recepcion!='--' && $value->fecha_recepcion!='-' && $value->fecha_recepcion!='---' && $value->fecha_recepcion!=null && $value->fecha_recepcion!='') {
                    $registro_cobranza->fecha_recepcion = $value->fecha_recepcion;
                }
                if ($value->ocam!='--' && $value->ocam!='-' && $value->ocam!='---' && $value->ocam!=null && $value->ocam!='') {
                    $registro_cobranza->ocam = $value->ocam;
                }
                if ($value->cdp!='--' && $value->cdp!='-' && $value->cdp!='---' && $value->cdp!=null && $value->cdp!='') {
                    $registro_cobranza->cdp = $value->cdp;
                }

                if ($value->categoria!='--' && $value->categoria!='-' && $value->categoria!='---' && $value->categoria!=null && $value->categoria!='') {
                    $registro_cobranza->categoria = $value->categoria;
                }

                if ($value->id_tipo_tramite!='--' && $value->id_tipo_tramite!='-' && $value->id_tipo_tramite!='---' && $value->id_tipo_tramite!=null && $value->id_tipo_tramite!='') {
                    $registro_cobranza->id_tipo_tramite = $value->id_tipo_tramite;
                }

                if ($value->plazo_credito!='--' && $value->plazo_credito!='-' && $value->plazo_credito!='---' && $value->plazo_credito!=null && $value->plazo_credito!='') {
                    $registro_cobranza->plazo_credito = $value->plazo_credito;
                }
                */
                if ($value->id_area!='--' && $value->id_area!='-' && $value->id_area!='---' && $value->id_area!=null && $value->id_area!='') {
                    $registro_cobranza->id_area = $value->id_area;
                }

                if ($value->id_estado_doc!='--' && $value->id_estado_doc!='-' && $value->id_estado_doc!='---' && $value->id_estado_doc!=null && $value->id_estado_doc!='') {
                    $registro_cobranza->id_estado_doc = $value->id_estado_doc;
                }
                $registro_cobranza->save();
                $success = false;
            }


        }

        return response()->json([
            "status"=>200,
            "success"=>true
        ]);
    }

    public function scriptEmpresaUnicos()
    {
        $registro_cobranzas = RegistroCobranza::where('estado',1)->get();
        // $registro_cobranzas = RegistroCobranza::where('estado',0)->get();
        foreach ($registro_cobranzas as $key => $value) {
            $cliente_gerencial = Cliente::where('id_cliente',$value->id_cliente)->first();
            if ($cliente_gerencial) {
                $adm_contri = Contribuyente::where('nro_documento',$cliente_gerencial->ruc)->first();
                if (!$adm_contri) {
                    $adm_contri = Contribuyente::where('razon_social',$cliente_gerencial->nombre)->first();
                }
                if ($adm_contri) {
                    $nueva_cobranza = RegistroCobranza::find($value->id_registro_cobranza);
                    $nueva_cobranza->id_cliente = $adm_contri->id_cliente_gerencial_old;
                    $nueva_cobranza->save();
                }
            }
        }

        return response()->json([
            "success"=>true,
            "status"=>200
        ]);
    }

    public function scriptMatchCobranzaPenalidad()
    {
        $penalidades = Penalidad::get();
        foreach ($penalidades as $key => $value) {
            if ($value->id_cobranza!==null && $value->id_cobranza!=='') {
                $registro_cobranza = RegistroCobranza::where('id_cobranza_old',$value->id_cobranza)->first();


                $update = Penalidad::find($value->id_penalidad);
                $update->id_registro_cobranza = $registro_cobranza->id_registro_cobranza;
                $update->save();
            }

        }

        return response()->json([
            "success"=>true,
            "status"=>200,
            "data"=>$penalidades
        ]);
    }

    public function exportarExcel($request)
    {
        $request = json_decode($request);

        $data = RegistroCobranza::where('registros_cobranzas.estado',1)
        ->select(
            'registros_cobranzas.*',
            'sector.nombre AS nombre_sector',
        )
        ->join('cobranza.sector', 'sector.id_sector','=', 'registros_cobranzas.id_sector')
        ->orderBy('id_registro_cobranza', 'desc');
        if (!empty($request->empresa)) {

            // $empresa = DB::table('contabilidad.adm_contri')
            // ->where('id_contribuyente',$request->empresa)
            // ->first();
            $data = $data->where('registros_cobranzas.id_empresa',$request->empres);
        }
        if (!empty($request->estado)) {
            $data = $data->where('registros_cobranzas.id_estado_doc',$request->estado);
        }
        if (!empty($request->fase)) {
            $fase_text = $request->fase;
            $data = $data->join('cobranza.cobranza_fase', function ($join) use($fase_text){
                $join->on('cobranza_fase.id_registro_cobranza', '=', 'registros_cobranzas.id_registro_cobranza')
                    ->orOn('cobranza_fase.id_cobranza', '=', 'registros_cobranzas.id_cobranza_old');
            });
            $data->where('cobranza_fase.fase', 'like' ,'%'.$fase_text.'%')
            ->where('cobranza_fase.estado',1);
        }
        if (!empty($request->fecha_emision_inicio)) {
            $data = $data->where('registros_cobranzas.fecha_emision','>=',$request->fecha_emision_inicio);
        }
        if (!empty($request->fecha_emision_fin)) {
            $data = $data->where('registros_cobranzas.fecha_emision','<=',$request->fecha_emision_fin);
        }
        if (!empty($request->simbolo) && (int)$request->simbolo=== 1 ) {
            $importe = $request->importe!==''||$request->importe!==null?$request->importe:0;
            $data = $data->where('registros_cobranzas.importe','<',(int) $importe);
        }
        if (!empty($request->simbolo) && (int)$request->simbolo=== 2 ) {
            $importe = $request->importe!==''||$request->importe!==null?$request->importe:0;
            $data = $data->where('registros_cobranzas.importe','>',(int) $importe);
        }
        $data=$data->get();

        foreach ($data as $key => $value) {

            # empresa
            $admi_empresa = AdministracionEmpresa::find($value->id_empresa);
            $adm_contri = Contribuyente::where('id_contribuyente',$admi_empresa->id_contribuyente)->first();
            $value->empresa = $adm_contri?$adm_contri->razon_social:'--';

            #cliente
            $contribuyente=null;
            $com_cliente = ComercialCliente::find($value->id_cliente);
            $contribuyente = Contribuyente::where('id_contribuyente',$com_cliente->id_contribuyente)->first();

            $value->cliente =  $contribuyente ? $contribuyente->razon_social:'--';
            $value->cliente_ruc =  $contribuyente ? $contribuyente->nro_documento:'--';

            #atraso
            $value->atraso = ($this->restar_fechas($value->fecha_recepcion, date('Y-m-d')) > 0) ? $this->restar_fechas($value->fecha_recepcion, date('Y-m-d')) : '0';

            #modena
            $value->moneda =  ($value->moneda == 1) ? 'S/' : 'US $';

            #importe
            $value->importe = number_format($value->importe, 2);

            #estado
            $estado_documento_nombre = EstadoDocumento::where('id_estado_doc',$value->id_estado_doc)->first();
            $value->estado =$estado_documento_nombre->nombre;

            #area
            $area_responsable_nombre = AreaResponsable::where('id_area',$value->id_area)->first();
            $value->area =  $area_responsable_nombre->descripcion;

            #fase
            $fase = CobranzaFase::where('id_cobranza', $value->id_cobranza_old)->where('id_cobranza','!=',null)->where('estado',1)->first();
            if (!$fase) {
                $fase = CobranzaFase::where('id_registro_cobranza', $value->id_registro_cobranza)->where('estado',1)->first();
            }
            $value->fase = ($fase?$fase->fase : '-');
            #fecha de pago
            $programacion_pago = ProgramacionPago::where('id_registro_cobranza',$value->id_registro_cobranza)->where('estado',1)->first();
            if (!$programacion_pago) {
                $programacion_pago = ProgramacionPago::where('id_cobranza',$value->id_cobranza_old)->where('estado',1)->first();
            }
            $value->fecha_pago = $programacion_pago? $programacion_pago->fecha:'--';

            #penalidad / retencion / detraccion
            $value->penalidad_importe='0';
            $value->detraccion_importe='0';
            $value->retencion_importe='0';
            # penalidad
            $penalidad_gerencial = Penalidad::where('estado',1)
                ->where('id_registro_cobranza',$value->id_registro_cobranza)
                ->orderBy('id_penalidad', 'desc')
                ->where('tipo','PENALIDAD')
                ->first();
            $value->penalidad = '-';
            if ($penalidad_gerencial) {
                $value->penalidad = $penalidad_gerencial->tipo;
                $value->penalidad_importe = $penalidad_gerencial->monto;
            }
            # detraccion
            $penalidad_detraccion = Penalidad::where('estado',1)
                ->where('id_registro_cobranza',$value->id_registro_cobranza)
                ->orderBy('id_penalidad', 'desc')
                ->where('tipo','DETRACCION')
                ->first();
            $value->detraccion = '--';
            if ($penalidad_detraccion) {
                $value->detraccion = $penalidad_detraccion->tipo;
                $value->detraccion_importe = $penalidad_detraccion->monto;
            }
            # retencion
            $penalidad_retencion = Penalidad::where('estado',1)
                ->where('id_registro_cobranza',$value->id_registro_cobranza)
                ->orderBy('id_penalidad', 'desc')
                ->where('tipo','RETENCION')
                ->first();
            $value->retencion = '---';
            if ($penalidad_retencion) {
                $value->retencion = $penalidad_retencion->tipo;
                $value->retencion_importe = $penalidad_retencion->monto;
            }
            if (intval($value->vendedor>0)) {
                $vendedor = Vendedor::where('id_vendedor',intval($value->vendedor))->first();
                if ($vendedor) {
                    $value->vendedor = $vendedor->nombre;
                }
            }
            #observacion
            $observacion = Observaciones::where('cobranza_id',$value->id_registro_cobranza)->where('estado',1)->orderBy('id', 'desc')->first();
            $value->observacion = ($observacion?$observacion->descripcion:'---');

        }
        return Excel::download(new CobranzasExpor($data), 'cobranza.xlsx');
        // return response()->json($data);
    }
    public function exportarExcelPrueba(Request $request)
    {
        // $data = json_encode($request->data);
        $request = json_decode($request->data);
        $data = RegistroCobranza::where('registros_cobranzas.estado',1)
            ->select(
                'registros_cobranzas.*',
                'sector.nombre AS nombre_sector',
            )
            ->join('cobranza.sector', 'sector.id_sector','=', 'registros_cobranzas.id_sector')
            ->orderBy('id_registro_cobranza', 'desc');
            if (!empty($request->empresa)) {
                $empresa = DB::table('contabilidad.adm_contri')
                ->where('id_contribuyente',$request->empresa)
                ->first();
                $data = $data->where('registros_cobranzas.id_empresa',$empresa->id_contribuyente)->orWhere('registros_cobranzas.id_empresa_old',$empresa->id_empresa_gerencial_old);
                // $data = $data->where('id_empresa_old',$empresa->id_empresa_gerencial_old);
            }
            if (!empty($request->estado)) {
                $data = $data->where('registros_cobranzas.id_estado_doc',$request->estado);
            }
            if (!empty($request->fase)) {
                $fase_text = $request->fase;
                $data = $data->join('cobranza.cobranza_fase', function ($join) use($fase_text){
                    $join->on('cobranza_fase.id_registro_cobranza', '=', 'registros_cobranzas.id_registro_cobranza')
                        ->orOn('cobranza_fase.id_cobranza', '=', 'registros_cobranzas.id_cobranza_old');
                });
                $data->where('cobranza_fase.fase', 'like' ,'%'.$fase_text.'%')
                ->where('cobranza_fase.estado',1);
            }
            if (!empty($request->fecha_emision_inicio)) {
                $data = $data->where('registros_cobranzas.fecha_emision','>=',$request->fecha_emision_inicio);
            }
            if (!empty($request->fecha_emision_fin)) {
                $data = $data->where('registros_cobranzas.fecha_emision','<=',$request->fecha_emision_fin);
            }
            if (!empty($request->simbolo) && (int)$request->simbolo=== 1 ) {
                $importe = $request->importe!==''||$request->importe!==null?$request->importe:0;
                $data = $data->where('registros_cobranzas.importe','<',(int) $importe);
            }
            if (!empty($request->simbolo) && (int)$request->simbolo=== 2 ) {
                $importe = $request->importe!==''||$request->importe!==null?$request->importe:0;
                $data = $data->where('registros_cobranzas.importe','>',(int) $importe);
            }
        $data=$data->get();
        return response()->json($data);exit;
    }
    public function scriptMatchCobranzaVendedor()
    {
        $vendedores_gerencial   = DB::table('gerencial.vendedor')->get();
        $registro_cobranza      = RegistroCobranza::where('estado',1)->where('vendedor','!=','--')->where('vendedor','!=',null)->get();
        $vendedores_excluidos = [];
        foreach ($registro_cobranza as $key => $value) {

            if ($value->vendedor!=='--' && $value->vendedor!==null && !intval($value->vendedor)) {
                $new_sentence = str_replace('.', '', $value->vendedor);
                $new_sentence = strtoupper($new_sentence);
                $vendedor = Vendedor::where('nombre','like','%'.$new_sentence.'%')->first();

                if (!$vendedor) {
                    array_push($vendedores_excluidos,$new_sentence);

                }else{
                    $actualizar_registro_cobranza = RegistroCobranza::find($value->id_registro_cobranza);
                    $actualizar_registro_cobranza->vendedor = $vendedor->id_vendedor;
                    $actualizar_registro_cobranza->save();
                }


                // $registro     = RegistroCobranza::where('id_registro_cobranza',$value->id_registro_cobranza)->first();
                // return response()->json([$registro,$new_sentence,$vendedor]);exit;
            }


        }
        return response()->json([
            "success"=>true,
            "status"=>200,
            "no_encontrados"=>$vendedores_excluidos
        ]);
    }

    public function scriptEmpresaActualizacion()
    {
        $array_razon_social=array(
            'UNIDAD EJECUTORA 037: PERU SEGURO 2025',
            'UNIDAD EJECUTORA 149. PROGRAMA DE INVERSION CREACION DE REDES INTEGRADAS DE SALUD',
            'COMPUTO Y PERIFERICOS S.A.C.',
            'GOBIERNO REGIONAL DE CALLAO',
            'UNIDAD EJECUTORA 406 SALUD SANCHEZ CARRION',
            'AS.PROM.ED.COLEG. MARISCAL RAMON CASTILLA',
            'GOBIERNO REGIONAL DE CUSCO',
            'GOBIERNO REGIONAL DE MADRE DE DIOS',
            'MINISTERIO PUBLICO - GERENCIA GENERAL',
            'UNIDAD EJECUTORA HOSPITAL DE REHABILITACION DEL CALLAO',
            'GOBIERNO REGIONAL DE HUANUCO',
            'RED DE SALUD AREQUIPA CAYLLOMA - GRA-SALUD RED PERIFERICA AREQUIPA',
            'UNIVERSIDAD NACIONAL AUTONOMA DE CHOTA',
            'UNIDAD EJECUTORA 403-1169 - REGION CUSCO - HOSPITAL ANTONIO LORENA',
            'MINISTERIO DE VIVIENDA, CONSTRUCCIÓN Y SANEAMIENTO',
            'DIRECCION REGIONAL DE EDUCACION LIMA METROPOLITANA',
            'MINISTERIO PUBLICO',
            'SUPERINTENDENCIA NACIONAL DE SERVICIO DE SANEAMIENTO',
            'PETROLEOS DEL PERU PETROPERU SA',
            'UNIDAD EJECUTORA 405 RED DE SALUD ANGARAES',
            'MANTINNI S.R.L.',
            'S Y S SOLUCIONES TI S.A.C.',
            'JUNTA DE USUARIOS DEL SECTOR HIDRÁULICO DE LA JOYA ANTIGUA',
            'NINA GOMEZ EDWIN ROYSI',
            'BOTICA SANTA LUCIA E.I.R.L.',
            'DOMINIO CONSULTORES EN MARKETING S.A.C',
            'G Y S CONSORCIO E INVERSIONES GENERALES S.A.C',
            'ELECSEIN DEL SUR S.R.L.',
            'MUNICIPALIDAD DISTRITAL J.CRESPO Y CASTILLO',
            'OFICINA DE GESTION DE SERVICIOS DE SALUD ALTO',
            'UNIDAD EJECUTORA HOSPITAL DE REHABILITACIÃN DEL CALLAO',
            'GERENCIA SUBREGIONAL JAEN',
            'UNIDAD EJECUTORA 003 GESTIÓN INTEGRAL DE LA CALIDAD AMBIENTAL',
            'HOSPITAL REGIONAL LAMBAYEQUE - GRL',
            'ORGANISMO DE EVALUACIÓN Y FISCALIZACIÓN AMBIENTAL - OEFA',
            'INVERSIONES 5VILLA S.A.C.',
            'ODP CONSULTORES S.A.C.',
            'GOLD TECH E.I.R.L',
            'ALMACENES ASOCIADOS S. A. C.',
            'SAIRA QUISPE EDILBERTO WILFREDO',
            'TAI TEC SOLUTIONS S.R.L.',
            'SANTANDER URIBE MARCOS',
            'MULTISERVICIOS',
            'EMP. SERV. LIMP. MUNIC. PUBLICA CALLAO S.A.',
            'SERVICIOS BASICOS DE SALUD CAÑETE-YAUYOS',
            'FATIMA RENT A CAR E.I.R.L.',
            'SOPORTE GERIATRICO MEDICO S.A.C.',
            'SEGURIDAD Y VIGILANCIA VISESJA S.A.C.',
            'CAHUANA CCOPA EDWIN FRANKLIN',
            'PORTUGAL ALVAREZ GAHUDY ARELLY',
            'MANUELO TAIPE DOMITILA',
            'GINECEO S.A.C',
            'EMPRESA MUNICIPAL ADMINISTRADORA DE PEAJE DE LIMA S.A',
            'INTELNETPERU E.I.R.L.',
            'PIPOL COMUNICACIONES S.A.C.',
            'GERIATRICOS AQP SP E.I.R.L.',
            'UNIDAD EJECUTORA 120 PROGRAMA NACIONAL DE DOTACIÓN DE MATERIALES EDUCATIVOS',
            'D Y Q TRANSPORTES INVERSIONES Y SERVICIOS GENERALES S.A.C.',
            'CYRUS ASISTENCIA DE CONTENEDORES S.R.L',
            'CRUCERO THOURS S.R.L.',
            'DIRECCIÓN SUB REGIONAL DE SALUD CHOTA',
            'DYQ TRANSPORTES INVERSIONES Y SERVICIOS GENERALES S.A.C.',
            'JAMES ICE CREAMS E.I.R.L.',
            'RESTOBAR S.R.L.',
            'C Y C NEGOCIOS E.I.R.L.',
            'PASOL DE ILO CONTRATISTAS GENERALES E.I.R.L.',
            'GUILLEN VALLEJO MARIA TERESA',
            'VARINZA S.A.C.',
            'MAMANI PAYHUANCA LEONARDO GENARO',
            'GERENCIA REGIONAL DE SALUD DEL GOBIERNO REGIONAL DE AREQUIPA',
            'DIRECCION REGIONAL DE SALUD MADRE DE DIOS',
            'PROGRAMA DE COMPENSACIONES PARA LA COMPETITIVIDAD',
            'GERENCIA REGIONAL DE TRANSPORTES Y COMUNICACIONES MOQUEGUA',
            'UNIDAD EJECUTORA 407 HOSPITAL DE APOYO PALPA',
            'MUNICIPALIDAD PROVINCIAL SAN ANTONIO PUTINA',
            'UGEL CAMANA',
            'DIRECCION REGIONAL DE TRANSPORTES Y COMUNICACIONES-CUSCO',
            'MUNICIPALIDAD DE CHORRILLOS',
            'DIRECCION REGIONAL DE TRANSPORTES Y COMUNICACIONES HUANUCO',
            'SUB REGION DE SALUD BAGUA',
            'MUNICIPIO DISTRITAL DE QUIÑOTA',
            'EMAPA HUARAL S.A.',
            'UNIDAD DE GESTION EDUCATIVA LOCAL MARISCAL NIETO',
            'UNIDAD EJECUTORA 314 EDUCACION ACOMAYO',
            'UNIDAD EJECUTORA ESCUELA NACIONAL SUPERIOR DE ARTE DRAMATICO "GUILLERMO UGARTE CHAMORRO"',
            'SALUD HOSPITAL REGIONAL DE LORETO',
            'PROYECTO ESPECIAL OLMOS - TINAJONES',
            'SISTEMA NACIONAL DE EVALUACION, ACREDITACION Y CERTIFICACION DE LA CALIDAD EDUCATIVA',
            'MARCO MARKETING CONSULTANTS PERU S.A.C.',
            'SATTEL CHILE LIMITADA',
            'POOL JONATHAN TORRES RODRIGUEZ',
            'RAUL TAPIA DIAZ',
            'CLE',
            'MUNICIPALIDAD DISTRITAL JACOBO D HUNTER',
            'ENTERCOMM PERU S.A.C.',
            'ESTABLECIMIENTO DE SALUD MUNICIPAL - ESAMU',
            'REDEES MACUSANI',
            'ELECTRONORTE S.A.',
            'BLANCAS LAVADO EVELYN',
            'SERVICIO NACIONAL METEOROLOGIA E HIDROL.',
            'MUNICIPALIDAD PROVINCIAL DE CONTUMUZA',
            "ESCUELA NACIONAL DE MARINA MERCANTE 'ALMIRANTE MIGUEL GRAU'",
            'UNIDAD EJECUTORA EDUCACION HUANCAYO',
            'EMPRESA PRESTADORA DE SERVICIOS DE SANEAMIENTO DE MOYOBAMBA S.A. - EPS MOYOBAMBA S.A.',
            'J Y C CORP S.R.L.',
            'EMPRESA PRESTADORA DE SERVICIOS DE SANEAMIENTO DE MOYOBAMBA SOCIEDAD ANÃNIMA - EPS MOYOBAMBA S.A.',
            'UGEL CONDESUYOS',
            'J',
            'UNIDAD EJECUTORA PROGRAMA NACIONAL DE CENTROS JUVENILES-PRONACEJ',
            'PS',
            'DRAGON TECNOLOGY E. I. R. L.',
            'PAMELA MODA',
            'UNIDAD TERRITORIAL DE SALUD SATIPO',
            'PAMELA MODA Y SPORT E.I.R.L.',
            'PURIMETRO E.I.R.L.',
            'INDUSTRIA MAGIOBET S.R.L.',
            'CORPORACION AGROPECUARIA DEL PACIFICO S.A.',
            'EMP. DE TRANS. FLORES HNOS.',
            'NUEVA LATINA CENTER S.R.L.',
            'EMPRESA ESTACION DE SERVICIOS GENERALES JORGE E.I.R.L.',
            'VARGAS VELASQUEZ ANTHONY DANIEL',
            'CARITAS TACNA - MOQUEGUA',
            'DISTRIBUCION Y SERVICIOS TOTALES S.R.L.',
            'VENGOA FIGUEROA CONTRATISTAS GENERALES S.R.L.',
            'CORPORACION LERIBE S.A.C',
            'DELICIA Y ARTE CULINARIO',
            'CONSTRUCTORA CUBA BULEJE ASOCIADOS S.A.C.',
            'ELECTRO SUR ESTE S.A.A.',
            'SECRETARIA TECNICA DE APOYO A LA COMISION AD HOC CREADA POR LA LEY 29625',
            'TERAN GLOBAL IMPORT S.R.L.',
            'DIRECCION DE RED SALUD BAGUA',
            'COMPUSOFT DATA S.A.C.',
            'UNIDAD EJECUTORA: UGEL CAYLLOMA',
            'HOSPITAL DE APOYO III SULLANA',
            'MUNICIPALIDAD PROVINCIAL TARMA',
            'TERMINAL PORTUARIO DE CHIMBOTE',
            'JC CONSORCIO Y SERVICIOS GENERALES S.C.R.L.',

        );
        $array_modificado=array();
        $array_encontrados=array();
        $array_faltantes=array();
        $contador=0;
        // return sizeof($array_razon_social);exit;
        foreach ($array_razon_social as $key => $value) {
            $cliente = Cliente::where('nombre','like','%'.$value.'%')->where('ruc','!=','undefined')->first();
            if ($cliente) {
                // array_push($array_encontrados,$cliente);
                $contribuyente = Contribuyente::where('razon_social','like','%'.$cliente->nombre.'%')->where('nro_documento',null)->first();
                if ($contribuyente) {

                    $update_contribuyente = Contribuyente::find($contribuyente->id_contribuyente);
                    $update_contribuyente->nro_documento = $cliente->ruc;
                    $update_contribuyente->save();

                    array_push($array_encontrados,$update_contribuyente);
                }else{
                    array_push($array_faltantes,$contribuyente);
                }

            }

        }

        return response()->json([
            "status"=>200,
            "success"=>true,
            "count_array"=>sizeof($array_razon_social),
            "count_vacios"=>$contador,
            "faltantes"=>$array_faltantes,
            "encontrados"=>$array_encontrados
        ]);
    }

    public function scriptVendedor()
    {
        $vendedores_array = array(
            "J ALFARO"=>"JORGE ALFARO",
            "H MEDINA"=>"HEBER MEDINA",
            "HEBERT MEDINA"=>"HEBER MEDINA",
            "J ALFARO"=>"JORGE ALFARO",
            "J MEDINA"=>"JONATHAN MEDINA",
            "ALE V"=>"ALEJANDRA VALENCIA",
            "JHUACO"=>"JOHAN HUACCO",
            "PROYECTOS"=>"REMY BARREDA",
            "J HUACO"=>"JOHAN HUACCO",
            "H AYMA"=>"HELEN AYMA",
            "JMEDINA"=>"JONATHAN MEDINA",
            "M RIVERA"=>"MANUEL RIVERA",
            "ANGEL MORÓN"=>"ANGEL MORON",
            "C MAMANI"=>"CELIA MAMANI",
            "J BEGAZO"=>"JUAN BEGAZO",
            "ALEXANDER M"=>"ALEXANDER MENDEZ",
            "ALEJANDRO"=>"ALE VALENCIA",
            // "---"=>"falta",
            "J DEZA"=>"JONATHAN DEZA",
            "A ROJAS"=>"ALFONSO ROJAS",
            "M SANCHEZ"=>"MAYKOL SANCHEZ",
            "MKPLACE"=>"ERICK ENCINAS",
            "JOHAN HUACO"=>"JOHAN HUACCO",
            "SALENKA CARPIO"=>"SALENKA ",
            "HEBERT M"=>"HEBER MEDINA",
            "R BARREDA"=>"REMY BARREDA",
            "J MARIN"=>"JORGE MARIN",
            "H CONDORI"=>"HEBERT CONDORI",
            // "----"=>"falta",
            "MAYKOL S"=>"MAYKOL SANCHEZ",
            "M HINOSTROZA"=>"MARICIELO HINOSTROZA",
            "R VISVAL"=>"RICARDO VISBAL",
            "R VISBAL"=>"RICARDO VISBAL",
            "A LAMAS"=>"ALEXANDER LAMAS",


            "LUCIO R"=>"LUCIO REYNOSO",
            "ANGEL M"=>"ANGEL MORON",
            "BORIS C"=>"BORIS CORRREA",
            "MANUEL R"=>"MANUEL RIVERA",
            "JUAN B"=>"JUAN BEGAZO",
            "JONATHAN MEDINA"=>"JONATHAN MEDINA",
            "ALEJANDRA V"=>"ALEJANDRA VALENCIA",
            "HEBER M"=>"HEBER MEDINA",
            // "A ROJAS"=>"ALFONSO ROJAS",

            "ERICK E"=>"ERICK ENCINAS",
            "HELEN A"=>"HELEN AYMA",
            "RVISBAL"=>"RICARDO VISBAL",
            "E AQUINO"=>"ELMER AQUINO",

            "M Hinostroza"=>"MARICIELO HINOSTROZA",
            "JORGE ALFARO"=>"JORGE ALFARO",
            "B CORREA"=>"BORIS CORRREA",
            "REMY B"=>"REMY BARREDA",
            "ALEJANDRA"=>"ALEJANDRA VALENCIA",
            "ALFONSO R"=>"ALFONSO ROJAS",
            "KARINA MUÑOZ"=>"KARINA MUÑOZ",
            "ALFONSO ROJAS"=>"ALFONSO ROJAS",
            "RIVERA"=>"MANUEL RIVERA",
            "MANUEL RIVERA"=>"MANUEL RIVERA",

            "RICARDO V"=>"RICARDO VISBAL",
            "ELMER A"=>"ELMER AQUINO",
            "LUCIO REYNOSO"=>"LUCIO REYNOSO",

            "CANDY R"=>"CANDY RODRIGUEZ",
            "JORGE MARIN"=>"JORGE MARIN",
            // "CANDY R"=>"CANDY RODRIGUEZ",
            // "CANDY R"=>"CANDY RODRIGUEZ",
            // "CANDY R"=>"CANDY RODRIGUEZ",

        );
        $vendedores_cobranzas=array();
        $registro_cobranza = RegistroCobranza::get();
        $registro_cobranza = $registro_cobranza->groupBy('vendedor');

        foreach ($registro_cobranza as $key => $value) {
            // return $key;exit;
            if ($key!=='--' && $key!=='-' && $key!=='') {
                $vendedor = str_replace('.', '', $key);
                array_push($vendedores_cobranzas,(object)array(
                    "vendedor_cobranza"=>strtoupper($key),
                    "vendedor_str"=>strtoupper($vendedor)
                ));
            }

        }
        foreach ($vendedores_cobranzas as $key_cobranza => $value_cobranza) {
            $encontrado = false;
            foreach ($vendedores_array as $key_array => $value_array) {

                if ($key_array === $value_cobranza->vendedor_str) {

                    $value_cobranza->nombre_completo = strtoupper($value_array);
                    $vendedor_first = Vendedor::where('nombre','like','%'.strtoupper($value_array).'%')->first();
                    if ('KARINA MUÑOZ'===$value_cobranza->vendedor_str) {
                        // return $value_cobranza->vendedor_str;exit;
                    }
                    if ($vendedor_first) {
                        $value_cobranza->id_vendedor = $vendedor_first->id_vendedor;
                    }else{
                        $value_cobranza->id_vendedor = 0;
                    }
                    $encontrado=true;
                }else{
                    // $vendedor_first = Vendedor::where('nombre','like','%'.strtoupper($value_array).'%')->first();
                    // if ($vendedor_first) {
                    //     $value_cobranza->id_vendedor = $vendedor_first->id_vendedor;
                    // }else{
                    //     $value_cobranza->id_vendedor = 0;
                    // }
                    // $encontrado=true;
                }
            }
        }
        $array_id_vendedor = array();
        foreach ($vendedores_cobranzas as $key => $value) {
            if (isset($value->id_vendedor)) {
                array_push($array_id_vendedor,(object) array(
                    "vendedor_cobranza"=>strtoupper($value->vendedor_cobranza),
                    "vendedor_str"=>strtoupper($value->vendedor_str),
                    "nombre_completo" => strtoupper($value->nombre_completo),
                    "id_vendedor" => $value->id_vendedor,
                ));
            }

            // if (!($value->id_vendedor)) {
            //     return [$value->id_vendedor];exit;
            // }else{
            //     return [$value];exit;
            // }
            // RegistroCobranza::where('vendedor', $value->vendedor_cobranza)
            // ->update(['vendedor' => $value->id_vendedor]);
        }
        foreach ($array_id_vendedor as $key => $value) {
            if ($value->id_vendedor==0) {

                $nuevo_vendedor = new Vendedor();
                $nuevo_vendedor->nombre = $value->nombre_completo;
                $nuevo_vendedor->estado = 1;
                $nuevo_vendedor->save();

                $value->id_vendedor = $nuevo_vendedor->id_vendedor;
            }
            if($value->id_vendedor!==0){

                RegistroCobranza::where('vendedor', $value->vendedor_cobranza)
                ->update(['vendedor' => $value->id_vendedor]);
            }

        }
        return response()->json([
            "success"=>true,
            "data"=>$array_id_vendedor
        ]);
    }

    public function editarPenalidad($id)
    {
        $penalidad = Penalidad::find($id);
        return response()->json($penalidad,200);
    }
    public function eliminarPenalidad(Request $request)
    {
        // return $request->all();exit;
        $penalidad = Penalidad::find($request->id);
        $penalidad->estado = $request->estado;
        $penalidad->save();
        $penalidades = Penalidad::where('estado','!=',7)->where('tipo',$request->tipo)->where('id_registro_cobranza',$request->id_registro_cobranza)->get();
        return response()->json($penalidades,200);
    }

    public function obtenerObservaciones(Request $request)
    {
        // return $request->all();exit;
        $registro_cobranza = RegistroCobranza::find($request->id);
        $observaciones = Observaciones::select('descripcion','usuario_id','estado','created_at','cobranza_id','id')
        ->where('cobranza_id',$request->id)
        // ->where('oc_id',$registro_cobranza->id_oc)
        ->where('estado',1)
        ->get();
        foreach ($observaciones as $key => $value) {
            $value->created_at = date("d-m-Y", strtotime($value->created_at));
            $usuario = SisUsua::find($value->usuario_id);
            $value->usuario =  ($usuario ?$usuario->nombre_corto:'--');
            $value->estado =  ($value->estado==1 ?'ELEABORADO':'ANULADO');
        }
        return response()->json($observaciones,200);
    }

    public function guardarObservaciones(Request $request)
    {
        $registro_cobranza = RegistroCobranza::find($request->id);

        $observacion = new Observaciones();
        $observacion->descripcion = $request->descripcion;
        $observacion->cobranza_id = $request->id;
        $observacion->usuario_id = Auth::user()->id_usuario;
        $observacion->oc_id = $registro_cobranza->id_oc;
        $observacion->estado = 1;
        $observacion->created_at = date('Y-m-d H:i:s');
        $observacion->updated_at = date('Y-m-d H:i:s');
        $observacion->save();

        $observaciones = Observaciones::select('descripcion','usuario_id','estado','created_at','cobranza_id','id')->where('cobranza_id',$request->id)
        // ->where('oc_id',$registro_cobranza->id_oc)
        ->where('estado',1)
        ->get();
        foreach ($observaciones as $key => $value) {
            $value->created_at = date("d-m-Y", strtotime($value->created_at));
            $usuario = SisUsua::find($value->usuario_id);
            $value->usuario =  ($usuario ?$usuario->nombre_corto:'--');
            $value->estado =  ($value->estado==1 ?'ELEABORADO':'ANULADO');
        }
        return response()->json($observaciones,200);
    }

    public function eliminarObservaciones(Request $request)
    {
        $registro_cobranza = RegistroCobranza::find($request->id_registro_cobranza);

        $observacion = Observaciones::find($request->id);
        $observacion->estado = 7;
        $observacion->save();

        $observaciones = Observaciones::select('descripcion','usuario_id','estado','created_at','cobranza_id','id')->where('cobranza_id',$request->id_registro_cobranza)
        // ->where('oc_id',$registro_cobranza->id_oc)
        ->where('estado',1)
        ->get();
        foreach ($observaciones as $key => $value) {
            $value->created_at = date("d-m-Y", strtotime($value->created_at));
            $usuario = SisUsua::find($value->usuario_id);
            $value->usuario = ($usuario ?$usuario->nombre_corto:'--');
            $value->estado =  ($value->estado==1 ?'ELEABORADO':'ANULADO');

        }
        return response()->json($observaciones,200);
    }

    public function scriptObservacionesOC()
    {
        // $registro_cobranza = RegistroCobranza::where('estado',1)->get();
        $array_faltantes=[];
        $array_encontrados=[];
        $select = DB::table('cobranza.registros_cobranzas')
        ->select(
            'registros_cobranzas.id_registro_cobranza',
            'registros_cobranzas.ocam',
            'oc_propias_view.id',
            'oc_propias_view.inicio_entrega',
            'oc_propias_view.fecha_entrega'
        )
        ->join('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.nro_orden', '=', 'registros_cobranzas.ocam')
        ->get();
        foreach ($select as $key => $value) {
            Observaciones::where('cobranza_id', $value->id_registro_cobranza)
            ->update(['oc_id' => $value->id]);

            $registro_cobranza = RegistroCobranza::find($value->id_registro_cobranza);
            $registro_cobranza->id_oc = $value->id;
            $registro_cobranza->inicio_entrega = $value->inicio_entrega;
            $registro_cobranza->fecha_entrega = $value->fecha_entrega;
            $registro_cobranza->save();


            Penalidad::where('id_registro_cobranza', $value->id_registro_cobranza)
            ->update(['id_oc' => $value->id]);
        }


        return response()->json([$select],200);
    }

    public function cargarCobranzaNuevo()
    {
        $cobranza = RegistroCobranzaOld::all();
        $count = 0;

        foreach ($cobranza as $key) {
            $nuevo = new RegistroCobranza();
                $nuevo->id_empresa = $key->id_empresa;
                $nuevo->id_sector = $key->id_sector;
                $nuevo->id_cliente = $key->id_cliente;
                $nuevo->id_oc = $key->id_oc;
                $nuevo->factura = ($key->factura == 'xxx') ? null: $key->factura;
                $nuevo->uu_ee = ($key->uu_ee == '--') ? null : $key->uu_ee;
                $nuevo->fuente_financ = $key->fuente_financ;
                $nuevo->ocam = null;
                $nuevo->siaf = ($key->siaf == '--') ? null : $key->siaf;
                $nuevo->fecha_emision = $key->fecha_emision;
                $nuevo->fecha_recepcion = $key->fecha_recepcion;
                $nuevo->moneda = $key->moneda;
                $nuevo->importe = $key->importe;
                $nuevo->id_estado_doc  = $key->id_estado_doc;
                $nuevo->id_tipo_tramite = $key->id_tipo_tramite;
                $nuevo->vendedor = $key->vendedor;
                $nuevo->estado = $key->estado;
                $nuevo->id_area = $key->id_area;
                $nuevo->id_periodo = $key->id_periodo;
                $nuevo->codigo_empresa = $key->codigo_empresa;
                $nuevo->categoria = $key->categoria;
                $nuevo->cdp = $key->cdp;
                $nuevo->oc_fisica = $key->oc;
                $nuevo->plazo_credito = $key->plazo_credito;
                $nuevo->id_doc_ven = $key->id_doc_ven;
                $nuevo->id_cliente_agil = $key->id_cliente_agil;
                $nuevo->id_cobranza_old = $key->id_cobranza_old;
                $nuevo->id_empresa_old = $key->id_empresa_old;
                $nuevo->inicio_entrega = null;
                $nuevo->fecha_entrega = null;
                $nuevo->fecha_registro = $key->fecha_registro;
            $nuevo->save();
            $count++;
        }
        return response()->json($count, 200);
    }

    /**
     * Script para cobranzas
     */
    public function cargarOrdenNuevo()
    {
        $cobranza = RegistroCobranza::all();
        $count = 0;
        foreach ($cobranza as $key) {
            $busqueda = Cobranza::find($key->id_cobranza_old);
            $cobranzas = RegistroCobranza::find($key->id_registro_cobranza);
                $cobranzas->ocam = ($busqueda->ocam != null) ? $busqueda->ocam : null;
            $cobranzas->save();
            $count++;
        }
        return response()->json(array('contador' => $count), 200);
    }

    public function cargarOrdenesFaltantes($tipo)
    {
        $cobranza = RegistroCobranza::all();
        $lista = $this->arrayFaltantes($tipo);
        $count = 0;

        foreach ($cobranza as $key) {
            foreach ($lista as $valor) {
                if ($key->id_cobranza_old == $valor->key) {
                    $cobranzas = RegistroCobranza::find($key->id_registro_cobranza);
                        $cobranzas->ocam = $valor->ocam;
                    $cobranzas->save();
                    $count++;
                }
            }
        }
        return response()->json(array('contador' => $count), 200);
    }

    public function limpiarCodigoOrden()
    {
        set_time_limit(6000);
        $count = 0;
        $cobranza = RegistroCobranza::where('ocam', 'not like', '%OCAM%')
                                    ->where('ocam', 'not like', '%DIRECTA%')
                                    ->where('ocam', 'not like', '%VENTA%')
                                    ->where('ocam', 'not like', '%DEUDA%')->get();

        foreach ($cobranza as $key) {
            $cobranzas = RegistroCobranza::find($key->id_registro_cobranza);
                $cobranzas->ocam = 'OCAM-'.$key->ocam;
            $cobranzas->save();
            $count++;
        }
        return response()->json(array('contador' => $count), 200);
    }

    public function cargarOrdenesId()
    {
        set_time_limit(6000);
        $count = 0;
        $cobranza = RegistroCobranza::all();

        foreach ($cobranza as $key) {
            $oc = OrdenCompraPropiaView::where('nro_orden', $key->ocam)->first();

            if ($oc) {
                $registro_cobranza = RegistroCobranza::find($key->id_registro_cobranza);
                    $registro_cobranza->id_oc = $oc->id;
                    $registro_cobranza->inicio_entrega = $oc->inicio_entrega;
                    $registro_cobranza->fecha_entrega = $oc->fecha_entrega;
                $registro_cobranza->save();
                $count++;
            }
        }
        return response()->json(array('contador' => $count), 200);
    }

    public function arrayFaltantes($tipo)
    {
        $matriz = [];

        if ($tipo == 'ocam') {
            $objetoOcam = new stdClass();
            $objetoOcam->key = 3991;
            $objetoOcam->ocam = 'OCAM-2021-1662-8-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3895;
            $objetoOcam->ocam = 'OCAM-2021-98-131-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 4052;
            $objetoOcam->ocam = 'OCAM-2021-1376-25-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3424;
            $objetoOcam->ocam = 'OCAM-2021-300308-97';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3378;
            $objetoOcam->ocam = 'OCAM-2021-301537-10-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3433;
            $objetoOcam->ocam = 'OCAM-2021-300699-16';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3504;
            $objetoOcam->ocam = 'OCAM-2021-301694-15';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3454;
            $objetoOcam->ocam = 'OCAM-2020-1253-587-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3400;
            $objetoOcam->ocam = 'OCAM-2021-300682-61';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3817;
            $objetoOcam->ocam = 'OCAM-2021-833-23-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 1119;
            $objetoOcam->ocam = 'OCAM-2020-875-1211-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 1145;
            $objetoOcam->ocam = 'OCAM-2020-788-451-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 1996;
            $objetoOcam->ocam = 'OCAM-2020-1239-247-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 2687;
            $objetoOcam->ocam = 'OCAM-2020-300423-278-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3975;
            $objetoOcam->ocam = 'OCAM-2021-1078-15-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3442;
            $objetoOcam->ocam = 'OCAM-2021-500256-29';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3992;
            $objetoOcam->ocam = 'OCAM-2021-1662-8-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 4053;
            $objetoOcam->ocam = 'OCAM-2021-98-165';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3949;
            $objetoOcam->ocam = 'OCAM-2021-300712-38-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3401;
            $objetoOcam->ocam = 'OCAM-2021-300934-13';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3379;
            $objetoOcam->ocam = 'OCAM-2021-1372-2';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3443;
            $objetoOcam->ocam = 'OCAM-2021-61-1';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 2480;
            $objetoOcam->ocam = 'OCAM-2020-201-388-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3760;
            $objetoOcam->ocam = 'OCAM-2021-1045-45-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3976;
            $objetoOcam->ocam = 'OCAM-2021-996-45-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3993;
            $objetoOcam->ocam = 'OCAM-2021-1662-8-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 4014;
            $objetoOcam->ocam = 'OCAM-2021-99-35-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3380;
            $objetoOcam->ocam = 'OCAM-2021-1345-16-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3402;
            $objetoOcam->ocam = 'OCAM-2021-1230-36-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3456;
            $objetoOcam->ocam = 'OCAM-2021-301270-40';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3416;
            $objetoOcam->ocam = 'OCAM-2021-301873-4';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3425;
            $objetoOcam->ocam = 'OCAM-2021-300308-97';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3616;
            $objetoOcam->ocam = 'OCAM-2021-1137-17';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 2059;
            $objetoOcam->ocam = 'OCAM-2020-301250-1658-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3897;
            $objetoOcam->ocam = 'OCAM-2021-1406-32-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3819;
            $objetoOcam->ocam = 'OCAM-2021-301294-63-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3876;
            $objetoOcam->ocam = 'OCAM-2021-117-74-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3381;
            $objetoOcam->ocam = 'OCAM-2021-1437-10-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 4055;
            $objetoOcam->ocam = 'OCAM-2021-721-138-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3440;
            $objetoOcam->ocam = 'OCAM-2020-880-1552';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3951;
            $objetoOcam->ocam = 'OCAM-2021-301291-21-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 4123;
            $objetoOcam->ocam = 'OCAM-2021-1190-51-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 2914;
            $objetoOcam->ocam = 'OCAM-2020-902-2090-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 4329;
            $objetoOcam->ocam = 'OCAM-2021-830-118-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3417;
            $objetoOcam->ocam = 'OCAM-2021-789-39';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 4375;
            $objetoOcam->ocam = 'OCAM-2021-300792-173-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3457;
            $objetoOcam->ocam = 'OCAM-2021-301884-16';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3426;
            $objetoOcam->ocam = 'OCAM-2021-300578-21';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3820;
            $objetoOcam->ocam = 'OCAM-2021-300251-164-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3898;
            $objetoOcam->ocam = 'OCAM-2021-1712-89-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3952;
            $objetoOcam->ocam = 'OCAM-2021-301291-21-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3248;
            $objetoOcam->ocam = 'OCAM-2020-804-1501-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3427;
            $objetoOcam->ocam = 'OCAM-2021-300809-22';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3995;
            $objetoOcam->ocam = 'OCAM-2021-300927-246-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3382;
            $objetoOcam->ocam = 'OCAM-2021-300934-11-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3441;
            $objetoOcam->ocam = 'OCAM-2021-300422-5';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 4124;
            $objetoOcam->ocam = 'OCAM-2021-301315-135-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3463;
            $objetoOcam->ocam = 'OCAM-2021-300635-7';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 4018;
            $objetoOcam->ocam = 'OCAM-2021-23-157-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3726;
            $objetoOcam->ocam = 'OCAM-2021-301838-238-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 2713;
            $objetoOcam->ocam = 'OCAM-2020-300357-149-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 2764;
            $objetoOcam->ocam = 'OCAM-2020-300423-285-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3899;
            $objetoOcam->ocam = 'OCAM-2021-1285-24-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3763;
            $objetoOcam->ocam = 'OCAM-2021-855-185';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3953;
            $objetoOcam->ocam = 'OCAM-2021-300752-22-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3900;
            $objetoOcam->ocam = 'OCAM-2021-301095-48-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3996;
            $objetoOcam->ocam = 'OCAM-2021-804-154-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3383;
            $objetoOcam->ocam = 'OCAM-2021-500133-17';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3822;
            $objetoOcam->ocam = 'OCAM-2021-500256-95-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 4331;
            $objetoOcam->ocam = 'OCAM-2021-1683-96-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3464;
            $objetoOcam->ocam = 'OCAM-2021-300741-14';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3619;
            $objetoOcam->ocam = 'OCAM-2021-160-13';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3561;
            $objetoOcam->ocam = 'OCAM-2021-1230-84';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 4125;
            $objetoOcam->ocam = 'OCAM-2021-301315-135-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 4165;
            $objetoOcam->ocam = 'OCAM-2021-1549-58-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 4153;
            $objetoOcam->ocam = 'OCAM-2021-91-224-0';
            $matriz[] = $objetoOcam;

            $objetoOcam = new stdClass();
            $objetoOcam->key = 3764;
            $objetoOcam->ocam = 'OCAM-2021-855-187-0';
            $matriz[] = $objetoOcam;
        } else {
            $objetoDirecta = new stdClass();
            $objetoDirecta->key = 4122;
            $objetoDirecta->ocam = 'DIRECTA-2021-07-014';
            $matriz[] = $objetoDirecta;

            $objetoDirecta = new stdClass();
            $objetoDirecta->key = 1126;
            $objetoDirecta->ocam = 'DIRECTA-2021-03-014';
            $matriz[] = $objetoDirecta;

            $objetoDirecta = new stdClass();
            $objetoDirecta->key = 1122;
            $objetoDirecta->ocam = 'DIRECTA-2021-05-005';
            $matriz[] = $objetoDirecta;

            $objetoDirecta = new stdClass();
            $objetoDirecta->key = 1124;
            $objetoDirecta->ocam = 'DIRECTA-2021-02-006';
            $matriz[] = $objetoDirecta;

            $objetoDirecta = new stdClass();
            $objetoDirecta->key = 3435;
            $objetoDirecta->ocam = 'DIRECTA-2021-05-001';
            $matriz[] = $objetoDirecta;

            $objetoDirecta = new stdClass();
            $objetoDirecta->key = 3461;
            $objetoDirecta->ocam = 'DIRECTA-2021-05-002';
            $matriz[] = $objetoDirecta;

            $objetoDirecta = new stdClass();
            $objetoDirecta->key = 3761;
            $objetoDirecta->ocam = 'DIRECTA-2021-03-013';
            $matriz[] = $objetoDirecta;

            $objetoDirecta = new stdClass();
            $objetoDirecta->key = 3481;
            $objetoDirecta->ocam = 'DIRECTA-2021-03-014';
            $matriz[] = $objetoDirecta;

            $objetoDirecta = new stdClass();
            $objetoDirecta->key = 3672;
            $objetoDirecta->ocam = 'DIRECTA-2021-05-005';
            $matriz[] = $objetoDirecta;

            $objetoDirecta = new stdClass();
            $objetoDirecta->key = 3506;
            $objetoDirecta->ocam = 'DIRECTA-2021-02-006';
            $matriz[] = $objetoDirecta;

            $objetoDirecta = new stdClass();
            $objetoDirecta->key = 3673;
            $objetoDirecta->ocam = 'DIRECTA-2021-05-001';
            $matriz[] = $objetoDirecta;

            $objetoDirecta = new stdClass();
            $objetoDirecta->key = 3674;
            $objetoDirecta->ocam = 'DIRECTA-2021-05-002';
            $matriz[] = $objetoDirecta;

            $objetoDirecta = new stdClass();
            $objetoDirecta->key = 3483;
            $objetoDirecta->ocam = 'DIRECTA-2021-03-013';
            $matriz[] = $objetoDirecta;
        }
        return $matriz;
    }
    public function exportarExcelPowerBI(Request $request)
    {
        # code...
        $request = json_decode($request);

        $data = RegistroCobranza::where('registros_cobranzas.estado',1)
        ->select(
            'registros_cobranzas.*',
            'sector.nombre AS nombre_sector',
        )
        ->join('cobranza.sector', 'sector.id_sector','=', 'registros_cobranzas.id_sector')
        ->orderBy('id_registro_cobranza', 'desc');
        if (!empty($request->empresa)) {
            $empresa = DB::table('contabilidad.adm_contri')
            ->where('id_contribuyente',$request->empresa)
            ->first();
            $data = $data->where('registros_cobranzas.id_empresa',$empresa->id_contribuyente)->orWhere('registros_cobranzas.id_empresa_old',$empresa->id_empresa_gerencial_old);
            // $data = $data->where('id_empresa_old',$empresa->id_empresa_gerencial_old);
        }
        if (!empty($request->estado)) {
            $data = $data->where('registros_cobranzas.id_estado_doc',$request->estado);
        }
        if (!empty($request->fase)) {
            $fase_text = $request->fase;
            $data = $data->join('cobranza.cobranza_fase', function ($join) use($fase_text){
                $join->on('cobranza_fase.id_registro_cobranza', '=', 'registros_cobranzas.id_registro_cobranza')
                    ->orOn('cobranza_fase.id_cobranza', '=', 'registros_cobranzas.id_cobranza_old');
            });
            $data->where('cobranza_fase.fase', 'like' ,'%'.$fase_text.'%')
            ->where('cobranza_fase.estado',1);
        }
        if (!empty($request->fecha_emision_inicio)) {
            $data = $data->where('registros_cobranzas.fecha_emision','>=',$request->fecha_emision_inicio);
        }
        if (!empty($request->fecha_emision_fin)) {
            $data = $data->where('registros_cobranzas.fecha_emision','<=',$request->fecha_emision_fin);
        }
        if (!empty($request->simbolo) && (int)$request->simbolo=== 1 ) {
            $importe = $request->importe!==''||$request->importe!==null?$request->importe:0;
            $data = $data->where('registros_cobranzas.importe','<',(int) $importe);
        }
        if (!empty($request->simbolo) && (int)$request->simbolo=== 2 ) {
            $importe = $request->importe!==''||$request->importe!==null?$request->importe:0;
            $data = $data->where('registros_cobranzas.importe','>',(int) $importe);
        }
        $data=$data->get();
        // $tipo_cambios = TipoCambio::orderBy('fecha','DESC')->first()->venta;
        $tipo_cambios = 3.95;
        foreach ($data as $key => $value) {
            # empresa
            $id_cliente =$value->id_empresa;
            $adm_contri = Contribuyente::where('id_contribuyente',$value->id_empresa)->first();

            $empresa = TesoreriaEmpresa::where('id_contribuyente',$adm_contri->id_contribuyente)->first();
            $value->empresa = $empresa?$empresa->codigo:'--';

            #cliente
            $contribuyente=null;
            if (!empty($value->id_cliente)) {
                $contribuyente = Contribuyente::where('id_cliente_gerencial_old',$value->id_cliente)->where('id_cliente_gerencial_old','!=',null)->first();
            }
            if (!empty($value->id_cliente_agil)) {
                // if (!$contribuyente) {
                    $contribuyente = Contribuyente::where('id_contribuyente',$value->id_cliente_agil)->where('id_contribuyente','!=',null)->first();
                // }
            }
            $value->cliente =  $contribuyente ? $contribuyente->razon_social:'--';
            // $value->cliente_ruc =  $contribuyente ? $contribuyente->nro_documento:'--';

            #atraso
            $value->atraso = ($this->restar_fechas($value->fecha_recepcion, date('Y-m-d')) > 0) ? $this->restar_fechas($value->fecha_recepcion, date('Y-m-d')) : '0';

            #modena
            $value->moneda =  ($value->moneda == 1) ? 'S/' : 'US $';

            #importe
            // $value->importe = number_format($value->importe, 2);

            #Dias
            $fechaInicial = $value->fecha_emision;
            $fechaFinal = date('Y-m-d');
            $fechaInicialSegundos = strtotime($fechaInicial);
            $fechaFinalSegundos = strtotime($fechaFinal);
            $dias = ($fechaFinalSegundos - $fechaInicialSegundos) / 86400;
            $value->dias =  $dias;

            #Dias para cobrar
            $value->dias_cobrar = $value->plazo_credito - $dias;

            #condicion
            $value->condicion = ($value->dias_cobrar>0?'En plazo':'Vencido');

            #rango
            $value->rango='---';

            #estado
            $estado_documento_nombre = EstadoDocumento::where('id_estado_doc',$value->id_estado_doc)->first();
            $value->estado =$estado_documento_nombre->nombre;

            #periodo
            $periodo = Periodo::where('id_periodo',$value->id_periodo)->first();
            $value->periodo =($periodo?$periodo->descripcion:'--');

            #controversia
            $value->controversia = '--';
            #comentarios
            $value->comentarios = '--';
        }
        // return $tipo_cambios;exit;
        return Excel::download(new CobranzaPowerBIExport($data), 'cobranza-power-bi.xlsx');
    }
    public function cambioEstadoPenalidad(Request $request)
    {
        // return $request->all();exit;
        $penalidad = Penalidad::find($request->id);
        $penalidad->estado_penalidad = $request->estado_penalidad;
        $penalidad->motivo = ($request->estado_penalidad == 'DEVOLUCION') ? $request->estado_penalidad.' DE LA PENALIDAD' : 'PENALIDAD '.$request->estado_penalidad;
        $penalidad->save();
        $penalidades = Penalidad::where('estado','!=',7)->where('tipo',$request->tipo)->where('id_registro_cobranza',$request->id_registro_cobranza)->get();

        if ($request->estado_penalidad == 'DEVOLUCION') {
            $control = new PenalidadCobro();
                $control->id_penalidad = $penalidad->id_penalidad;
                $control->id_registro_cobranza = $penalidad->id_registro_cobranza;
                $control->importe = $penalidad->monto;
                $control->estado = 'PENDIENTE';
                $control->gestion = $request->gestion;
            $control->save();
        }
        return response()->json($penalidades,200);
    }
    public function anularPenalidad(Request $request)
    {
        $penalidad = Penalidad::find($request->id);
        $penalidad->estado = 2;
        $penalidad->save();
        $penalidades = Penalidad::where('estado','!=',7)->where('tipo',$request->tipo)->where('id_registro_cobranza',$request->id_registro_cobranza)->get();

        return response()->json($penalidades,200);
    }
    public function scriptClienteUnificar()
    {
        $registro_cobranza = RegistroCobranza::all();
        $array_excluidos = array();
        foreach ($registro_cobranza as $key => $value) {
            if ($value->id_cliente!==null && $value->id_cliente!=='') {
                $cliente_gerencial = Cliente::find($value->id_cliente);
                $contribuyente = Contribuyente::where('nro_documento','!=',null)
                ->where('nro_documento',$cliente_gerencial->ruc)
                ->first();
                if (!$contribuyente) {
                    $contribuyente = Contribuyente::where('razon_social','!=',null)
                    ->where('razon_social',$cliente_gerencial->nombre)
                    ->first();
                }

                if ($contribuyente) {
                    $actualizar_registro_cobranza = RegistroCobranza::find($value->id_registro_cobranza);
                    $actualizar_registro_cobranza->id_cliente_agil = $contribuyente->id_contribuyente;
                    $actualizar_registro_cobranza->save();
                }


            }else{
                array_push($array_excluidos,$value);
            }

        }
        return response()->json($array_excluidos,200);
    }
    // remplazar para empresas
    public function scriptEmpresaRemplazarAdmCliente()
    {
        $adm_empresa = AdministracionEmpresa::all();
        $registro_cobranza = RegistroCobranza::where('estado',1)
        // ->where('id_registro_cobranza',6455)
        // ->where('id_registro_cobranza',6458)
        ->get();
        foreach ($registro_cobranza as $key_cobranza => $value_cobranza) {
            foreach ($adm_empresa as $key_empresa => $value_empresa) {
                if ($value_empresa->id_contribuyente === $value_cobranza->id_empresa) {

                    $cobranza_actualizar = RegistroCobranza::find($value_cobranza->id_registro_cobranza);
                    $cobranza_actualizar->id_empresa_old = $value_cobranza->id_empresa;
                    $cobranza_actualizar->save();

                    $cobranza_actualizar = RegistroCobranza::find($value_cobranza->id_registro_cobranza);
                    $cobranza_actualizar->id_empresa = $value_empresa->id_empresa;
                    $cobranza_actualizar->save();
                }
            }
        }
        return response()->json(["success"=>true],200);
    }
    public function scriptEmpresaRemplazarAdmClienteRevertir()
    {
        $registro_cobranza = RegistroCobranza::where('estado',1)
        // ->where('id_registro_cobranza',6458)
        ->where('id_registro_cobranza',6455)
        ->get();
        foreach ($registro_cobranza as $key_cobranza => $value_cobranza) {
            $cobranza_actualizar = RegistroCobranza::find($value_cobranza->id_registro_cobranza);
            $cobranza_actualizar->id_empresa = $value_cobranza->id_empresa_old;
            $cobranza_actualizar->save();
        }
    }
    // remplazar para clientes
    public function scriptClienteRemplazarComCliente()
    {
        $com_cliente = ComercialCliente::all();
        $registro_cobranza = RegistroCobranza::where('estado',1)
        // ->where('id_registro_cobranza',598)
        // ->where('id_registro_cobranza',6458)
        ->get();

        $array_agregar=array();
        foreach ($registro_cobranza as $key_cobranza => $value_cobranza) {
            if ($value_cobranza->id_cliente_agil) {

                $cobranza_actualizar = RegistroCobranza::find($value_cobranza->id_registro_cobranza);
                $cobranza_actualizar->id_cliente = $value_cobranza->id_cliente_agil;
                $cobranza_actualizar->save();

                $com_cliente = ComercialCliente::where('id_contribuyente',$value_cobranza->id_cliente_agil)->first();
                // return $com_cliente;exit;
                if ($com_cliente) {
                    $cobranza_actualizar = RegistroCobranza::find($value_cobranza->id_registro_cobranza);
                    $cobranza_actualizar->id_cliente_agil = $com_cliente->id_cliente;
                    $cobranza_actualizar->save();
                }else{
                    $com_cliente = new ComercialCliente();
                    $com_cliente->id_contribuyente = $value_cobranza->id_cliente_agil;
                    $com_cliente->estado = 1;
                    $com_cliente->fecha_registro = date('Y-m-d H:i:s');
                    $com_cliente->save();

                    $cobranza_actualizar = RegistroCobranza::find($value_cobranza->id_registro_cobranza);
                    $cobranza_actualizar->id_cliente_agil = $com_cliente->id_cliente;
                    $cobranza_actualizar->save();
                    // return $com_cliente;exit;
                    array_push($array_agregar,array(
                        "id"=>$value_cobranza->id_cliente_agil,
                        "id_cobranza"=>$cobranza_actualizar->id_registro_cobranza,
                    ));
                }
            }


            // }
            // if (!$com_cliente) {
            //     return $value_cobranza;exit;
            // }
            // return $com_cliente;exit;
        }
        return response()->json(["success"=>$array_agregar],200);
    }
    public function scriptClienteRemplazarComClienteRevertir()
    {
        $registro_cobranza = RegistroCobranza::where('estado',1)
        // ->where('id_registro_cobranza',6458)
        // ->where('id_registro_cobranza',6412)
        ->get();
        foreach ($registro_cobranza as $key_cobranza => $value_cobranza) {
            $cobranza_actualizar = RegistroCobranza::find($value_cobranza->id_registro_cobranza);
            $cobranza_actualizar->id_cliente_agil = $value_cobranza->id_cliente;
            $cobranza_actualizar->save();
        }
    }

    public function scriptEmpresaRemplazarAdmClienteEstadoCero()
    {
        $adm_empresa = AdministracionEmpresa::all();
        $registro_cobranza = RegistroCobranza::where('estado',0)
        // ->where('id_registro_cobranza',6455)
        // ->where('id_registro_cobranza',6458)
        ->get();
        foreach ($registro_cobranza as $key_cobranza => $value_cobranza) {
            foreach ($adm_empresa as $key_empresa => $value_empresa) {
                if ($value_empresa->id_contribuyente === $value_cobranza->id_empresa) {

                    $cobranza_actualizar = RegistroCobranza::find($value_cobranza->id_registro_cobranza);
                    $cobranza_actualizar->id_empresa_old = $value_cobranza->id_empresa;
                    $cobranza_actualizar->save();

                    $cobranza_actualizar = RegistroCobranza::find($value_cobranza->id_registro_cobranza);
                    $cobranza_actualizar->id_empresa = $value_empresa->id_empresa;
                    $cobranza_actualizar->save();
                }
            }
        }
        return response()->json(["success"=>true],200);
    }
    public function scriptClienteRemplazarComClienteEstadoCero()
    {
        # code...
        $com_cliente = ComercialCliente::all();
        $registro_cobranza = RegistroCobranza::where('estado',0)
        // ->where('id_registro_cobranza',598)
        // ->where('id_registro_cobranza',6458)
        ->get();

        $array_agregar=array();
        foreach ($registro_cobranza as $key_cobranza => $value_cobranza) {
            if ($value_cobranza->id_cliente_agil) {

                $cobranza_actualizar = RegistroCobranza::find($value_cobranza->id_registro_cobranza);
                $cobranza_actualizar->id_cliente = $value_cobranza->id_cliente_agil;
                $cobranza_actualizar->save();

                $com_cliente = ComercialCliente::where('id_contribuyente',$value_cobranza->id_cliente_agil)->first();
                // return $com_cliente;exit;
                if ($com_cliente) {
                    $cobranza_actualizar = RegistroCobranza::find($value_cobranza->id_registro_cobranza);
                    $cobranza_actualizar->id_cliente_agil = $com_cliente->id_cliente;
                    $cobranza_actualizar->save();
                }else{
                    $com_cliente = new ComercialCliente();
                    $com_cliente->id_contribuyente = $value_cobranza->id_cliente_agil;
                    $com_cliente->estado = 1;
                    $com_cliente->fecha_registro = date('Y-m-d H:i:s');
                    $com_cliente->save();

                    $cobranza_actualizar = RegistroCobranza::find($value_cobranza->id_registro_cobranza);
                    $cobranza_actualizar->id_cliente_agil = $com_cliente->id_cliente;
                    $cobranza_actualizar->save();
                    // return $com_cliente;exit;
                    array_push($array_agregar,array(
                        "id"=>$value_cobranza->id_cliente_agil,
                        "id_cobranza"=>$cobranza_actualizar->id_registro_cobranza,
                    ));
                }
            }


            // }
            // if (!$com_cliente) {
            //     return $value_cobranza;exit;
            // }
            // return $com_cliente;exit;
        }
        return response()->json(["success"=>$array_agregar],200);
    }
    public function scriptClienteNuevosIngresados()
    {
        $com_cliente = ComercialCliente::all();
        $registro_cobranza = RegistroCobranza::where('id_cliente',null)->where('estado',1)
        // ->where('id_registro_cobranza',6474)
        // ->where('id_registro_cobranza',6458)
        ->get();

        $array_agregar=array();
        foreach ($registro_cobranza as $key_cobranza => $value_cobranza) {
            if ($value_cobranza->id_cliente_agil) {

                $cobranza_actualizar = RegistroCobranza::find($value_cobranza->id_registro_cobranza);
                $cobranza_actualizar->id_cliente = $value_cobranza->id_cliente_agil;
                $cobranza_actualizar->save();

                $com_cliente = ComercialCliente::where('id_contribuyente',$value_cobranza->id_cliente_agil)->first();
                // return $com_cliente;exit;
                if ($com_cliente) {
                    $cobranza_actualizar = RegistroCobranza::find($value_cobranza->id_registro_cobranza);
                    $cobranza_actualizar->id_cliente_agil = $com_cliente->id_cliente;
                    $cobranza_actualizar->save();
                }else{
                    $com_cliente = new ComercialCliente();
                    $com_cliente->id_contribuyente = $value_cobranza->id_cliente_agil;
                    $com_cliente->estado = 1;
                    $com_cliente->fecha_registro = date('Y-m-d H:i:s');
                    $com_cliente->save();

                    $cobranza_actualizar = RegistroCobranza::find($value_cobranza->id_registro_cobranza);
                    $cobranza_actualizar->id_cliente_agil = $com_cliente->id_cliente;
                    $cobranza_actualizar->save();
                    // return $com_cliente;exit;
                    array_push($array_agregar,array(
                        "id"=>$value_cobranza->id_cliente_agil,
                        "id_cobranza"=>$cobranza_actualizar->id_registro_cobranza,
                    ));
                }
            }
        }

        return response()->json(["success"=>$registro_cobranza],200);
    }
    public function scriptClienteVistaNull()
    {
        // $registro_cobranza = RegistroCobranza::where('id_cliente',null)->where('estado',1)

        // ->get();
        $data = RegistroCobranza::where('registros_cobranzas.estado',1)
        ->where('com_cliente.id_cliente',null)
        ->select(
            // 'registros_cobranzas.id_cliente_agil as cliente_agil',
            // 'registros_cobranzas.id_registro_cobranza',
            // 'registros_cobranzas.id_cobranza_old',
            'registros_cobranzas.*',
            'com_cliente.id_cliente as cliente',
        )
        ->leftJoin('comercial.com_cliente', 'com_cliente.id_cliente','=', 'registros_cobranzas.id_cliente_agil');
        $count = $data->count();
        $data = $data->get();

        $array_agregar =array();
        foreach ($data as $key => $value) {
            $cobranza_actualizar = RegistroCobranza::find($value->id_registro_cobranza);
            $cobranza_actualizar->id_cliente = $value->id_cliente_agil;
            $cobranza_actualizar->save();

            $comercial_cliente = ComercialCliente::where('id_contribuyente',$value->id_cliente_agil)->first();
            if ($comercial_cliente) {
                $cobranza_actualizar = RegistroCobranza::find($value->id_registro_cobranza);
                $cobranza_actualizar->id_cliente_agil = $comercial_cliente->id_cliente;
                $cobranza_actualizar->save();
            }else{
                $comercial_cliente = new ComercialCliente();
                $comercial_cliente->id_contribuyente = $value->id_cliente_agil;
                $comercial_cliente->estado = 1;
                $comercial_cliente->fecha_registro = date('Y-m-d H:i:s');
                $comercial_cliente->save();

                $cobranza_actualizar = RegistroCobranza::find($value->id_registro_cobranza);
                $cobranza_actualizar->id_cliente_agil = $comercial_cliente->id_cliente;
                $cobranza_actualizar->save();
                // return $com_cliente;exit;
                array_push($array_agregar,array(
                    "id_cliente_agil"=>$value->id_cliente_agil,
                    "id_cobranza"=>$value->id_registro_cobranza,
                ));
            }
        }
        return response()->json(["contador"=>$count,"data"=>$array_agregar],200);
    }
    public function scriptClienteContribuyenteVistaNull()
    {
        // $registro_cobranza = RegistroCobranza::where('id_cliente',null)->where('estado',1)

        // ->get();
        $data = RegistroCobranza::where('registros_cobranzas.estado',1)
        ->where('adm_contri.razon_social',null)
        ->select(
            // 'registros_cobranzas.id_cliente_agil as cliente_agil',
            // 'registros_cobranzas.id_registro_cobranza',
            // 'registros_cobranzas.id_cobranza_old',
            'registros_cobranzas.*',
            'com_cliente.id_cliente as cliente',
            'adm_contri.id_contribuyente',
            'adm_contri.razon_social'
        )
        ->join('comercial.com_cliente', 'com_cliente.id_cliente','=', 'registros_cobranzas.id_cliente_agil')
        ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente','=', 'com_cliente.id_contribuyente');
        $count = $data->count();
        $data = $data->get();

        $array_cobranza_null =array();
        $array_gerencial_cliente_null =array();
        $array_gerencial_cliente_encontrado =array();

        $array_contribuyente_encontrado =array();
        $array_contribuyente_no_encontrado =array();

        $array_contribuyente_comercial_encontrado =array();
        $array_contribuyente_no_comercial_encontrado =array();
        foreach ($data as $key => $value) {
            $gerencial_cobranza = DB::table('gerencial.cobranza')->where('id_cobranza',$value->id_cobranza_old)->first();

            if ($gerencial_cobranza) {

                $gerencial_cliente = DB::table('gerencial.cliente')->where('id_cliente',$gerencial_cobranza->id_cliente)->first();
                if ($gerencial_cliente) {
                    $contribuyente = Contribuyente::where('razon_social','like','%'.$gerencial_cliente->nombre.'%')->first();
                    if (!$contribuyente) {
                        $contribuyente = Contribuyente::where('nro_documento',$gerencial_cliente->ruc)->first();
                    }
                    if ($contribuyente) {
                        array_push($array_contribuyente_encontrado,$contribuyente);

                        $comercial_cliente = ComercialCliente::where('id_contribuyente',$contribuyente->id_contribuyente)->first();

                        if ($comercial_cliente) {
                            array_push($array_contribuyente_comercial_encontrado,$comercial_cliente);
                            $cobranza_actualizar = RegistroCobranza::find($value->id_registro_cobranza);
                            $cobranza_actualizar->id_cliente_agil = $comercial_cliente->id_cliente;
                            $cobranza_actualizar->save();
                        }else{
                            array_push($array_contribuyente_no_comercial_encontrado,$contribuyente);
                            $comercial_cliente = new ComercialCliente();
                            $comercial_cliente->id_contribuyente = $value->id_cliente_agil;
                            $comercial_cliente->estado = 1;
                            $comercial_cliente->fecha_registro = date('Y-m-d H:i:s');
                            $comercial_cliente->save();

                            $cobranza_actualizar = RegistroCobranza::find($value->id_registro_cobranza);
                            $cobranza_actualizar->id_cliente_agil = $comercial_cliente->id_cliente;
                            $cobranza_actualizar->save();

                        }
                    }else{
                        $guardar_contribuyente = new Contribuyente;
                        $guardar_contribuyente->nro_documento   =$gerencial_cliente->ruc;
                        $guardar_contribuyente->razon_social    =$gerencial_cliente->nombre;
                        $guardar_contribuyente->ubigeo          =0;
                        $guardar_contribuyente->id_pais         =170;
                        $guardar_contribuyente->fecha_registro  =date('Y-m-d H:i:s');
                        $guardar_contribuyente->id_cliente_gerencial_old    =$gerencial_cobranza->id_cobranza;
                        $guardar_contribuyente->estado          =1;
                        $guardar_contribuyente->transportista   ='f';
                        $guardar_contribuyente->save();

                        $comercial_cliente = new ComercialCliente();
                        $comercial_cliente->id_contribuyente = $guardar_contribuyente->id_contribuyente;
                        $comercial_cliente->estado = 1;
                        $comercial_cliente->fecha_registro = date('Y-m-d H:i:s');
                        $comercial_cliente->save();

                        $cobranza_actualizar = RegistroCobranza::find($value->id_registro_cobranza);
                        $cobranza_actualizar->id_cliente_agil = $comercial_cliente->id_cliente;
                        $cobranza_actualizar->save();

                        array_push($array_contribuyente_no_encontrado,$gerencial_cliente);
                    }


                    // if ($contribuyente) {
                    //     $cobranza_actualizar = RegistroCobranza::find($value->id_registro_cobranza);
                    //     $cobranza_actualizar->id_cliente = $value->id_cliente_agil;
                    //     $cobranza_actualizar->save();
                    // }
                    array_push($array_gerencial_cliente_encontrado,$gerencial_cliente);
                }else{
                    array_push($array_gerencial_cliente_null,$gerencial_cobranza);
                }
            }else{
                array_push($array_cobranza_null,$value);
            }

        }
        return response()->json([
            "contador"=>$count,
            "cobranza_null"=>sizeof($array_cobranza_null),

            "gerencial_cliente_encontrado"=>sizeof($array_gerencial_cliente_encontrado),
            "gerencial_cliente_null"=>sizeof($array_gerencial_cliente_null),

            "contribuyente_encontrado"=>sizeof($array_contribuyente_encontrado),
            "contribuyente_no_encontrado"=>sizeof($array_contribuyente_no_encontrado),
            "contribuyente_comercial_encontrado"=>sizeof($array_contribuyente_comercial_encontrado),
            "contribuyente_no_comercial_encontrado"=>sizeof($array_contribuyente_no_comercial_encontrado)
        ],200);
    }
    public function scriptClienteAgilGerencial()
    {
        $registro_cobranza = RegistroCobranza::where('id_cobranza_old','!=',null);
        $count = $registro_cobranza->count();
        $registro_cobranza = $registro_cobranza->get();

        $contador_gerencial_cobranza = 0;
        $contador_gerencial_cobranza_cliente = 0;
        #comparando con los del agil si estan todos registrados
        $contador_agil_contribuyente = 0;
        $contador_agil_contribuyente_no_encontrados = 0;
        $contador_agil_cliente = 0;
        $contador_agil_cliente_no_encontrados = 0;
        #faltantes
        $contador_contribuyente_no_clientes = array();

        foreach ($registro_cobranza as $key => $value) {
            $gerencial_cobranza = DB::table('gerencial.cobranza')->where('id_cobranza',$value->id_cobranza_old)->first();

            if ($gerencial_cobranza) {
                $contador_gerencial_cobranza = DB::table('gerencial.cobranza')->where('id_cobranza',$value->id_cobranza_old)->count() + $contador_gerencial_cobranza;

                $gerencial_cliente = DB::table('gerencial.cliente')->where('id_cliente',$gerencial_cobranza->id_cliente)->first();
                if ($gerencial_cliente) {
                    #contador
                    $contador_gerencial_cobranza_cliente = DB::table('gerencial.cliente')->where('id_cliente',$gerencial_cobranza->id_cliente)->count() + $contador_gerencial_cobranza_cliente;
                    #----
                    $contribuyente = Contribuyente::where('razon_social','like','%'.$gerencial_cliente->nombre.'%')->first();
                    if (!$contribuyente) {
                        $contribuyente = Contribuyente::where('nro_documento',$gerencial_cliente->ruc)->first();
                    }
                    if ($contribuyente) {
                        $contador_agil_contribuyente = $contador_agil_contribuyente + 1;
                        $comercial_cliente = ComercialCliente::where('id_contribuyente',$contribuyente->id_contribuyente)->first();

                        if ($comercial_cliente) {
                            $contador_agil_cliente = $contador_agil_cliente+1;
                        //     array_push($array_contribuyente_comercial_encontrado,$comercial_cliente);
                            $cobranza_actualizar = RegistroCobranza::find($value->id_registro_cobranza);

                            $cobranza_actualizar->id_cliente = $value->id_cliente_agil;
                            $cobranza_actualizar->id_cliente_agil = $comercial_cliente->id_cliente;
                            $cobranza_actualizar->id_cliente_auxiliar = $comercial_cliente->id_cliente;

                            $cobranza_actualizar->save();
                        }else{
                            array_push($contador_contribuyente_no_clientes,$contribuyente);
                            $contador_agil_cliente_no_encontrados = $contador_agil_cliente_no_encontrados+1;
                        //     array_push($array_contribuyente_no_comercial_encontrado,$contribuyente);
                            $comercial_cliente = new ComercialCliente();
                            $comercial_cliente->id_contribuyente = $contribuyente->id_contribuyente;
                            $comercial_cliente->estado = 1;
                            $comercial_cliente->fecha_registro = date('Y-m-d H:i:s');
                            $comercial_cliente->save();

                            $cobranza_actualizar = RegistroCobranza::find($value->id_registro_cobranza);
                            $cobranza_actualizar->id_cliente = $value->id_cliente_agil;
                            $cobranza_actualizar->id_cliente_agil = $comercial_cliente->id_cliente;
                            $cobranza_actualizar->id_cliente_auxiliar = $comercial_cliente->id_cliente;

                            $cobranza_actualizar->save();

                        }
                    }else{
                        $contador_agil_contribuyente_no_encontrados = $contador_agil_contribuyente_no_encontrados+1;
                        $guardar_contribuyente = new Contribuyente;
                        $guardar_contribuyente->nro_documento   =$gerencial_cliente->ruc;
                        $guardar_contribuyente->razon_social    =$gerencial_cliente->nombre;
                        $guardar_contribuyente->ubigeo          =0;
                        $guardar_contribuyente->id_pais         =170;
                        $guardar_contribuyente->fecha_registro  =date('Y-m-d H:i:s');
                        $guardar_contribuyente->id_cliente_gerencial_old    =$gerencial_cobranza->id_cobranza;
                        $guardar_contribuyente->estado          =1;
                        $guardar_contribuyente->transportista   ='f';
                        $guardar_contribuyente->save();

                        $comercial_cliente = new ComercialCliente();
                        $comercial_cliente->id_contribuyente = $guardar_contribuyente->id_contribuyente;
                        $comercial_cliente->estado = 1;
                        $comercial_cliente->fecha_registro = date('Y-m-d H:i:s');
                        $comercial_cliente->save();

                        $cobranza_actualizar = RegistroCobranza::find($value->id_registro_cobranza);
                        $cobranza_actualizar->id_cliente = $value->id_cliente_agil;
                        $cobranza_actualizar->id_cliente_agil = $comercial_cliente->id_cliente;
                        $cobranza_actualizar->id_cliente_auxiliar = $comercial_cliente->id_cliente;
                        $cobranza_actualizar->save();
                    }
                }
            }else{
                // array_push($array_cobranza_null,$value);
            }

        }
        $contador_cliente_auxiliar = RegistroCobranza::where('id_cliente_auxiliar','!=',null);
        $contador_cliente_auxiliar = $contador_cliente_auxiliar->count();
        return response()->json([
            "total"=>$count,
            "gerencial_cobranza"=>$contador_gerencial_cobranza,
            "gerencial_cobranza_cliente"=>$contador_gerencial_cobranza_cliente,
            "agil_contribuyente"=>$contador_agil_contribuyente,
            "agil_contribuyente_no_encontrados"=>$contador_agil_contribuyente_no_encontrados,
            "agil_cliente"=>$contador_agil_cliente,
            "agil_cliente_no_encontrados"=>$contador_agil_cliente_no_encontrados,
            "cliente_auxiliar"=>$contador_cliente_auxiliar,
            "agil_contribuyentes_no_clientes"=>$contador_contribuyente_no_clientes,
        ],200);
    }

    public function scriptRegistroFase()
    {
        $data = RegistroCobranza::where('estado', 1)->get();
        $cont = 0;
        foreach ($data as $key) {
            $nuevo = new RegistroCobranzaFase();
                $nuevo->id_registro_cobranza = $key->id_registro_cobranza;
                $nuevo->fase = 'COMPROMISO';
                $nuevo->fecha = $key->fecha_registro;
            $nuevo->save();
            $cont++;
        }
        return response()->json($cont, 200);
    }

    public function scriptPeriodosActual()
    {
        $cont = 0;
        // $cobranza = Cobranza::all();

        // foreach ($cobranza as $key) {
        //     $periodos = DB::table('gerencial.periodo')->select('descripcion')->where('id_periodo', $key->id_periodo)->first();
        //     RegistroCobranza::where('id_cobranza_old', $key->id_cobranza)->update(['periodo' => $periodos->descripcion]);
        //     $cont++;
        // }
        $cobranza = RegistroCobranza::all();

        foreach ($cobranza as $key) {
            $periodos = Periodo::where('descripcion', $key->periodo)->first();
            RegistroCobranza::where('id_registro_cobranza', $key->id_registro_cobranza)->update(['id_periodo' => $periodos->id_periodo]);
            $cont++;
        }
        return response()->json($cont, 200);
    }

    public function scriptFasesActual()
    {
        $dataActiva = CobranzaFase::all();
        $cont = 0;

        foreach ($dataActiva as $key) {
            $nuevo = new RegistroCobranzaFase();
                $nuevo->id_registro_cobranza = $key->id_registro_cobranza;
                $nuevo->fase = $key->fase;
                $nuevo->fecha = $key->fecha;
            $nuevo->save();
            $cont++;
        }

        $dataInactiva = CobranzaFase::where('estado', 1)->get();
        $dele = 0;

        foreach ($dataInactiva as $row) {
            $eliminar = RegistroCobranzaFase::find($row->id_registro_cobranza);
            if ($eliminar) {
                $eliminar->delete();
            }
            $dele++;
        }
        return response()->json(array("cargados" => $cont, "eliminados" => $dele), 200);
    }
}
