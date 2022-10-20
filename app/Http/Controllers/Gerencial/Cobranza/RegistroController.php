<?php

namespace App\Http\Controllers\Gerencial\Cobranza;

use App\Gerencial\Cobranza as GerencialCobranza;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Gerencial\CobranzaAgil;
use App\Models\Administracion\Periodo;
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
        })
        ->toJson();
        // ->make(true);
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

        // $departamento ='';

        $distrito_first   = Distrito::where('id_dis',$cliente_erp->ubigeo)->first();
        $id_dis     = $cliente_erp->ubigeo;

        $provincia_first  = Provincia::where('id_prov',$distrito_first->id_prov)->first();
        $id_prov    = $provincia_first->id_prov;

        $distrito  = Distrito::where('id_prov',$id_prov)->get();
        $provincia  = Provincia::where('id_dpto',$provincia_first->id_dpto)->get();

        $id_dpto = $provincia_first->id_dpto;
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
        return response()->json([
            "success"=>true,
            "status"=>200,
            "data"=>$request
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
        if ($cliente_gerencial) {
            return response()->json([
                "status"=>200,
                "success"=>true,
                "data"=>$cliente_gerencial
            ]);
        }else{
            return response()->json([
                "status"=>400,
                "success"=>false,
                "data"=>$cliente_gerencial
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
}
