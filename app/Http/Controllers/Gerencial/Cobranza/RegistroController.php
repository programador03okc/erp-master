<?php

namespace App\Http\Controllers\Gerencial\Cobranza;

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
use App\Models\Contabilidad\Contribuyente;
use App\models\Gerencial\AreaResponsable;
use App\models\Gerencial\Cliente;
use App\models\Gerencial\CobanzaFase;
use App\models\Gerencial\Cobranza;
use App\models\Gerencial\Empresa;
use App\models\Gerencial\EstadoDocumento;
use App\Models\Gerencial\ProgramacionPago;
use App\Models\Gerencial\RegistroCobranza;
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
        $empresa            = DB::table('administracion.adm_empresa')
        ->select(
            'adm_empresa.id_contribuyente',
            'adm_empresa.codigo',
            'adm_contri.razon_social'
        )
        ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
        ->get();
        $periodo            = Periodo::where('estado',1)->get();
        $estado_documento   = EstadoDocumento::where('estado',1)->get();

        $pais = Pais::get();
        $departamento = Departamento::get();
        return view('gerencial/cobranza/registro',compact('sector','tipo_ramite','empresa','periodo','estado_documento', 'pais', 'departamento'));
    }
    public function listarRegistros(Request $request)
    {
        // $data = Cobranza::select('*')->orderBy('id_cobranza', 'desc');

        $data = RegistroCobranza::select('*')->orderBy('id_registro_cobranza', 'desc');
        if (!empty($request->empresa)) {
            $empresa = DB::table('contabilidad.adm_contri')
            ->where('id_contribuyente',$request->empresa)
            ->first();
            $data = $data->where('id_empresa',$empresa->id_contribuyente)->orWhere('id_empresa_old',$empresa->id_empresa_gerencial_old);
            // $data = $data->where('id_empresa_old',$empresa->id_empresa_gerencial_old);
        }
        if (!empty($request->estado)) {
            $data = $data->where('id_estado_doc',$request->estado);
        }
        if (!empty($request->fase)) {
            $data = $data->where('id_estado_doc',$request->fase);
        }
        return DataTables::of($data)
        ->addColumn('empresa', function($data){
            $id_cliente =$data->id_empresa;
            $empresa            = DB::table('administracion.adm_empresa')
            ->select(
                'adm_empresa.id_contribuyente',
                'adm_empresa.codigo',
                'adm_contri.razon_social'
            )
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->where('adm_empresa.id_contribuyente',$id_cliente)
            ->first();
            return $empresa->razon_social;
            // return $data->empresa->nombre;
        })
        ->addColumn('cliente', function($data){
            $id_cliente = $data->id_cliente;
            if (!$id_cliente) {
                $id_cliente = $data->id_cliente_agil;
            }
            return $id_cliente;
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
            // $estado_documento = EstadoDocumento::where('estado',1)->get();
            $estado_documento_nombre = EstadoDocumento::where('id_estado_doc',$data->id_estado_doc)->first();
            // return [$data->estadoDocumento->id_estado_doc,$estado_documento,$estado_documento_nombre];
            return $estado_documento_nombre->nombre;
        })
        ->addColumn('area', function($data){
            // $area_responsable = AreaResponsable::where('estado',1)->get();
            $area_responsable_nombre = AreaResponsable::where('id_area',$data->id_area)->first();
            // return [$data->areaResponsable->id_area, $area_responsable];
            return $area_responsable_nombre->descripcion;
         })
        ->addColumn('fase', function($data) {
            $fase = CobanzaFase::where('id_cobranza', $data->id_cobranza_old)->where('id_cobranza','!=',null)->where('estado',1)->orderBy('id_fase', 'desc')->first();
            if (!$fase) {
                $fase = CobanzaFase::where('id_registro_cobranza', $data->id_registro_cobranza)->where('estado',1)->orderBy('id_fase', 'desc')->first();
            }
            return ($fase?$fase->fase : '-');
            // return ($fase?$fase->fase[0] : '-');
        })
        // ->toJson();
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
        $empresa = DB::table('administracion.adm_empresa')->where('id_contribuyente',$request->empresa)->first();
        $cobranza = new RegistroCobranza();

        $cobranza->id_empresa       = $request->empresa;
        $cobranza->id_sector        = $request->sector;
        if ($request->id_cliente!==null && $request->id_cliente!=='') {
            $cobranza->id_cliente       = $request->id_cliente;
        }else{
            $cobranza->id_cliente_agil      = $request->id_contribuyente;
        }

        $cobranza->factura          = $request->fact;
        $cobranza->uu_ee            = $request->ue;
        $cobranza->fuente_financ    = $request->ff;
        $cobranza->oc               = $request->oc; // OCAM es igul que la oc
        $cobranza->siaf             = $request->siaf;
        $cobranza->fecha_emision    = $request->fecha_emi;
        $cobranza->fecha_recepcion  = $request->fecha_rec;
        $cobranza->moneda           = $request->moneda;
        $cobranza->importe          = $request->importe;
        $cobranza->id_estado_doc    = $request->estado_doc;
        $cobranza->id_tipo_tramite  = $request->tramite;
        $cobranza->vendedor         = $request->nom_vendedor;
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
        // $cobranza->id_vent          = ;

        $cobranza->save();

        if ($cobranza) {
            $programacion_pago = new ProgramacionPago();
            $programacion_pago->id_registro_cobranza = $cobranza->id_registro_cobranza;
            $programacion_pago->fecha   = $request->fecha_ppago;
            $programacion_pago->estado  = 1;
            $programacion_pago->fecha_registro = date('Y-m-d H:i:s');
            $programacion_pago->save();
        }
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
        $obtener_listado = DB::table('almacen.alm_req')->where('alm_req.enviar_facturacion','t')->where('doc_ven.estado',1)
        ->where('alm_req.traslado',1)
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
        ->join('almacen.alm_det_req' , 'alm_det_req.id_requerimiento', '=' ,'alm_req.id_requerimiento')
        ->join('almacen.doc_ven_det' , 'doc_ven_det.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')
        ->join('almacen.doc_ven' , 'doc_ven.id_doc_ven' ,'=' ,'doc_ven_det.id_doc')
        // ->groupBy('alm_req.id_requerimiento')
        ->get();

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
            }
            DB::table('almacen.alm_req')->where('traslado',1)->update([
                'traslado'=>2
            ]);
        }
        return response()->json([
            "success"=>$success,
            "status"=>$status,
            "data"=>$json_obtener_listado
        ]);
    }
    public function listarVentasProcesas()
    {
        # code...
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
        // if ($tipo==='oc') {
        //     $cliente_gerencial->where('requerimiento_logistico_view.nro_orden',$data);
        // }
        // if ($tipo === 'cdp') {
        //     $cliente_gerencial->where('requerimiento_logistico_view.codigo_oportunidad',$data);
        // }
        // $cliente_gerencial = $cliente_gerencial
        ->where('requerimiento_logistico_view.id_requerimiento_logistico',$id_requerimiento)
        ->select(
            'requerimiento_logistico_view.id_requerimiento_logistico',
            'requerimiento_logistico_view.codigo_oportunidad',
            'requerimiento_logistico_view.nro_orden',

            'doc_vent_req.id_documento_venta_requerimiento',
            'doc_ven.id_doc_ven',

            'doc_ven.serie',
            'doc_ven.numero',
            'doc_ven.fecha_emision',
            'doc_ven.credito_dias',
            'doc_ven.total_a_pagar',
            // 'doc_ven.modena'
            'adm_contri.nro_documento',
            'adm_contri.razon_social',
            'com_cliente.id_cliente'

        )
        ->join('almacen.doc_vent_req', 'doc_vent_req.id_requerimiento', '=', 'requerimiento_logistico_view.id_requerimiento_logistico')
        ->join('almacen.doc_ven', 'doc_ven.id_doc_ven', '=', 'doc_vent_req.id_doc_venta')

        ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'requerimiento_logistico_view.id_requerimiento_logistico')
        ->join('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
        ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
        ->first();

        $doc_ven=[];

        if ($cliente_gerencial) {

            $doc_ven = DocumentoVenta::where('id_doc_ven',$cliente_gerencial->id_doc_ven)->first();
            return response()->json([
                "status"=>200,
                "success"=>true,
                "data"=>$cliente_gerencial,
                "factura"=>$doc_ven,
            ]);
        }else{
            return response()->json([
                "status"=>400,
                "success"=>false,
                "data"=>$cliente_gerencial,
                "factura"=>$doc_ven,
            ]);
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
                $update = Contribuyente::where('estado',1)
                ->where('id_contribuyente',$contri->id_contribuyente)
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
            // if ($value->ruc!=='undefined' && strlen($value->ruc)===11) {
                // api de reniec en busca por el ruc
                $curl = curl_init();

                    curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.apis.net.pe/v1/ruc?numero='.$value->ruc,
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
                // return response()->json($ubigeo_distrito);
                if (empty($response->error)) {
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
            // }
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
        $contribuyente = Contribuyente::where('id_contribuyente',$registro_cobranza->id_cliente_agil)->first();
        if (!$contribuyente) {
            $contribuyente = Cliente::where('id_cliente',$registro_cobranza->id_cliente)->first();
            array_push($cliente_array,array(
                "id_cliente"=>$contribuyente->id_cliente,
                "id_contribuyente"=>null,
                "nro_documento"=>$contribuyente->ruc,
                "razon_social"=>$contribuyente->nombre
            ));
            // $cliente_array=[
            //     "id_cliente"=>$contribuyente->id_cliente,
            //     "id_contribuyente"=>null,
            //     "nro_documento"=>$contribuyente->ruc,
            //     "razon_social"=>$contribuyente->nombre
            // ];
        }else{
            array_push($cliente_array,array(
                "id_cliente"=>null,
                "id_contribuyente"=>$contribuyente->id_contribuyente,
                "nro_documento"=>$contribuyente->nro_documento,
                "razon_social"=>$contribuyente->razon_social
            ));
            // $cliente_array=[
            //     "id_cliente"=>$contribuyente->id_cliente,
            //     "id_contribuyente"=>null,
            //     "nro_documento"=>$contribuyente->ruc,
            //     "razon_social"=>$contribuyente->nombre
            // ];
        }
        $programacion_pago = ProgramacionPago::where('id_registro_cobranza',$id)->where('estado',1)->first();
        return response()->json([
            "status"=>200,
            "success"=>true,
            "data"=>$registro_cobranza,
            "programacion_pago"=>$programacion_pago,
            "cliente"=>$cliente_array
        ]);
    }
    public function modificarRegistro(Request $request)
    {
        $data=$request;
        $empresa = DB::table('administracion.adm_empresa')->where('id_contribuyente',$request->empresa)->first();
        $cobranza = RegistroCobranza::find($request->id_registro_cobranza);

        $cobranza->id_empresa       = $request->empresa;
        $cobranza->id_sector        = $request->sector;

        $cobranza->id_cliente       = (!empty($request->id_cliente) ? $request->id_cliente:null);
        $cobranza->id_cliente_agil       = (!empty($request->id_cliente_agil) ? $request->id_cliente_agil:null) ;

        $cobranza->factura          = $request->fact;
        $cobranza->uu_ee            = $request->ue;
        $cobranza->fuente_financ    = $request->ff;
        $cobranza->oc               = $request->oc; // OCAM es igul que la oc
        $cobranza->siaf             = $request->siaf;
        $cobranza->fecha_emision    = $request->fecha_emi;
        $cobranza->fecha_recepcion  = $request->fecha_rec;
        $cobranza->moneda           = $request->moneda;
        $cobranza->importe          = $request->importe;
        $cobranza->id_estado_doc    = $request->estado_doc;
        $cobranza->id_tipo_tramite  = $request->tramite;
        $cobranza->vendedor         = $request->nom_vendedor;
        $cobranza->estado           = 1;
        // $cobranza->fecha_registro   = date('Y-m-d H:i:s');
        $cobranza->id_area          = $request->area;
        $cobranza->id_periodo       = $request->periodo;
        // $cobranza->ocam             = $request->ocam;
        $cobranza->codigo_empresa   = $empresa->codigo;
        $cobranza->categoria        = $request->categ;
        $cobranza->cdp              = $request->cdp;
        $cobranza->plazo_credito    = $request->plazo_credito;
        $cobranza->id_doc_ven       = $request->id_doc_ven;
        // $cobranza->id_vent          = ;

        $cobranza->save();

        if ($cobranza) {
            $programacion_pago = ProgramacionPago::where('id_registro_cobranza',$cobranza->id_registro_cobranza)->first();
            if ($programacion_pago) {
                $programacion_pago = ProgramacionPago::find($cobranza->id_registro_cobranza);
                // $programacion_pago->id_registro_cobranza = $cobranza->id_registro_cobranza;
                $programacion_pago->fecha   = $request->fecha_ppago;
                $programacion_pago->estado  = 1;
                $programacion_pago->fecha_registro = date('Y-m-d H:i:s');
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

        return response()->json([
            "success"=>true,
            "status"=>200,
            "data"=>$data
        ]);
    }
    public function obtenerFase($id)
    {
        $registro_cobranza = RegistroCobranza::where('id_registro_cobranza',$id)->first();
        // return $registro_cobranza;
        if ($registro_cobranza) {
            $cobranzas_fases = CobanzaFase::where('id_cobranza',$registro_cobranza->id_cobranza_old)->where('id_cobranza','!=',null)->where('estado',1)->get();
            if (sizeof($cobranzas_fases)===0) {
                $cobranzas_fases = CobanzaFase::where('id_registro_cobranza',$registro_cobranza->id_registro_cobranza)->where('estado',1)->get();
            }
            if (sizeof($cobranzas_fases)>0) {
                return response()->json([
                    "success"=>true,
                    "status"=>200,
                    "fases"=>$cobranzas_fases
                ]);
            }else{
                return response()->json([
                    "success"=>false,
                    "status"=>404,
                    "fases"=>null
                ]);
            }
        }else{
            return response()->json([
                "success"=>false,
                "status"=>404,
                "fases"=>null
            ]);
        }


    }
    public function guardarFase(Request $request)
    {
        $registro_cobranza = RegistroCobranza::where('id_registro_cobranza',$request->id_registro_cobranza)->first();
        // $cobranza_fase = CobanzaFase::where('id_cobranza',$registro_cobranza->id_cobranza_old)->first();
        $cobranza_fase          = new CobanzaFase();
        if ($registro_cobranza) {
            $cobranza_fase->id_cobranza    = $registro_cobranza->id_cobranza_old;
        }

        $cobranza_fase->fase    = $request->fase;
        $cobranza_fase->fecha   = $request->fecha_fase;
        $cobranza_fase->fecha_registro  = date('Y-m-d H:i:s');
        $cobranza_fase->estado  = 1;
        $cobranza_fase->id_registro_cobranza  = $request->id_registro_cobranza;
        $cobranza_fase->save();
        return response()->json([
            "success"=>true,
            "status"=>200,
        ]);
    }
    public function eliminarFase(Request $request)
    {
        $cobranza_fase = CobanzaFase::find($request->id);
        $cobranza_fase->estado = 0;
        $cobranza_fase->save();
        if ($cobranza_fase) {
            return response()->json([
                "success"=>true,
                "status"=>200,
                "data"=>$cobranza_fase
            ]);
        }else{
            return response()->json([
                "success"=>false,
                "status"=>404,
            ]);
        }

    }
    public function scriptEmpresa()
    {
        // return $empresa_agil = Contribuyente::where('nro_documento',10804138582)->first();exit;
        $empresa_gerencial = Empresa::get();
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
}
