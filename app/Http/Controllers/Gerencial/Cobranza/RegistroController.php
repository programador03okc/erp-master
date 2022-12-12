<?php

namespace App\Http\Controllers\Gerencial\Cobranza;

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
use App\Models\Contabilidad\Contribuyente;
use App\models\Gerencial\AreaResponsable;
use App\models\Gerencial\Cliente;
use App\models\Gerencial\CobanzaFase;
use App\models\Gerencial\Cobranza;
use App\models\Gerencial\Empresa;
use App\models\Gerencial\EstadoDocumento;
use App\Models\Gerencial\Penalidad;
use App\Models\Gerencial\ProgramacionPago;
use App\Models\Gerencial\RegistroCobranza;
use App\models\Gerencial\Sector;
use App\models\Gerencial\TipoTramite;
use App\Models\Gerencial\Vendedor;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
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

        $data = RegistroCobranza::where('registros_cobranzas.estado',1)->select('registros_cobranzas.*')->orderBy('id_registro_cobranza', 'desc');
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
            $data = $data->join('gerencia_cobranza.cobranza_fase', function ($join) use($fase_text){
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
            $id_cliente =$data->id_empresa;

            if ($data->id_empresa!==null && $data->id_empresa !=='') {
                $id_cliente =$data->id_empresa;

            }else{
                $id_cliente =$data->id_empresa_old;
                $adm_contri = Contribuyente::where('id_empresa_gerencial_old',$id_cliente)->first();
                $id_cliente= $adm_contri->id_contribuyente;
            }

            $empresa = DB::table('administracion.adm_empresa')
            ->select(
                'adm_empresa.id_contribuyente',
                'adm_empresa.codigo',
                'adm_contri.razon_social'
            )
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->where('adm_empresa.id_contribuyente',$id_cliente)
            ->first();
            return $empresa?$empresa->razon_social:'--';
            // return $data->empresa->nombre;
        })
        ->addColumn('cliente', function($data){

            $contribuyente=null;
            if (!empty($data->id_cliente)) {
                $contribuyente = Contribuyente::where('id_cliente_gerencial_old',$data->id_cliente)->where('id_cliente_gerencial_old','!=',null)->first();
            }
            if (!empty($data->id_cliente_agil)) {
                // if (!$contribuyente) {
                    $contribuyente = Contribuyente::where('id_contribuyente',$data->id_cliente_agil)->where('id_contribuyente','!=',null)->first();
                // }
            }



            return $contribuyente ? $contribuyente->razon_social:'--';
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
            $fase = CobanzaFase::where('id_cobranza', $data->id_cobranza_old)->where('id_cobranza','!=',null)->where('estado',1)->first();
            if (!$fase) {
                $fase = CobanzaFase::where('id_registro_cobranza', $data->id_registro_cobranza)->where('estado',1)->first();
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
        // if ($request->id_cliente!==null && $request->id_cliente!=='') {
        //     $cobranza->id_cliente       = $request->id_cliente;
        // }else{
        //     $cobranza->id_cliente_agil      = $request->id_contribuyente;
        // }
        $cobranza->id_cliente       = (!empty($request->id_cliente) ? $request->id_cliente:null);
        $cobranza->id_cliente_agil       = (!empty($request->id_contribuyente) ? $request->id_contribuyente:null) ;

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
            'requerimiento_logistico_view.id_contribuyente_cliente',

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

                // return response()->json([$response,$value]);exit;
                if (!empty($response->error)) {

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
        if (intval($registro_cobranza->vendedor)>0) {
            $vendedor = Vendedor::where('id_vendedor',$registro_cobranza->vendedor)->first();
        }

        if (!$vendedor) {
            $vendedor = Vendedor::where('nombre','like','%'.$registro_cobranza->vendedor.'%')->first();
        }
        // return $vendedor;exit;
        $contribuyente = array();
        if ($registro_cobranza->id_cliente_agil!==null) {
            $contribuyente = Contribuyente::where('id_contribuyente',$registro_cobranza->id_cliente_agil)->first();
        }

        if (!$contribuyente) {
            if ($registro_cobranza->id_cliente!==null) {
                $contribuyente = Contribuyente::where('id_cliente_gerencial_old',$registro_cobranza->id_cliente)->first();
            }

            if ($contribuyente) {
                array_push($cliente_array,array(
                    "id_cliente"=>null,
                    "id_contribuyente"=>$contribuyente->id_contribuyente,
                    "nro_documento"=>$contribuyente->nro_documento,
                    "razon_social"=>$contribuyente->razon_social
                ));
            }


        }else{
            array_push($cliente_array,array(
                "id_cliente"=>null,
                "id_contribuyente"=>$contribuyente->id_contribuyente,
                "nro_documento"=>$contribuyente->nro_documento,
                "razon_social"=>$contribuyente->razon_social
            ));
        }

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
            "cliente"=>$cliente_array,
            "vendedor"=>$vendedor?$vendedor:[]
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
        $cobranza->id_cliente_agil       = (!empty($request->id_contribuyente) ? $request->id_contribuyente:null);

        // $cobranza->id_cliente       = $request->id_cliente;
        // $cobranza->id_cliente_agil       = $request->id_contribuyente;

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
            $cobranzas_fases = CobanzaFase::where('id_cobranza',$registro_cobranza->id_cobranza_old)->where('id_cobranza','!=',null)->where('estado','!=',0)->get();
            if (sizeof($cobranzas_fases)===0) {
                $cobranzas_fases = CobanzaFase::where('id_registro_cobranza',$registro_cobranza->id_registro_cobranza)->where('estado','!=',0)->get();
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
        DB::table('gerencia_cobranza.cobranza_fase')
            ->where('id_registro_cobranza', $registro_cobranza->id_registro_cobranza)
            ->update(['estado' => 2]);
        DB::table('gerencia_cobranza.cobranza_fase')
            ->where('id_cobranza', $registro_cobranza->id_cobranza_old)
            ->where('id_cobranza','!=' , null)
            ->update(['estado' => 2]);
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
    public function scriptFase()
    {
        $cobranza_fase_id_cobranza = DB::table('gerencia_cobranza.cobranza_fase')
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
            $cobranza_fase = CobanzaFase::where('id_cobranza',$value)->where('estado',1)->orderBy('id_fase','DESC')->get();
            foreach ($cobranza_fase as $key => $value) {
                if ($key!==0) {
                    // array_push($array_cambios,array(
                    //     "id_fase"=> $value->id_fase,
                    //     "id_cobranza"=> $value->id_cobranza,
                    //     "fase"=> $value->fase,
                    //     "fecha"=> $value->fecha,
                    //     "estado"=> 2,
                    //     "fecha_registro"=> $value->fecha_registro,
                    //     "id_registro_cobranza"=> $value->id_registro_cobranza
                    // ));
                    DB::table('gerencia_cobranza.cobranza_fase')
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
        $penalidad = new Penalidad();
        $penalidad->tipo            = $request->tipo_penal;
        $penalidad->monto           = $request->importe_penal;
        $penalidad->documento       = $request->doc_penal;
        $penalidad->fecha           = $request->fecha_penal;
        $penalidad->observacion   = $request->obs_penal;
        $penalidad->estado          = 1;
        $penalidad->fecha_registro  = date('Y-m-d H:i:s');
        $penalidad->id_registro_cobranza  = $request->id_cobranza_penal;
        $penalidad->save();
        return response()->json([
            "status"=>200,
            "success"=>true,
        ]);
    }
    public function obtenerPenalidades($id_registro_cobranza)
    {
        $registro_cobranza = RegistroCobranza::where('id_registro_cobranza',$id_registro_cobranza)->first();
        // return $registro_cobranza;exit;
        $penalidad_gerencial = Penalidad::where('estado',1);
        $penalidad_gerencial = $penalidad_gerencial->where('id_cobranza',$registro_cobranza->id_cobranza_old)->orWhere('id_registro_cobranza',$id_registro_cobranza);
        // if (!empty($registro_cobranza->id_cobranza_old)) {
        //     $penalidad_gerencial = $penalidad_gerencial->where('id_cobranza',$registro_cobranza->id_cobranza_old);
        // }else{
        //     $penalidad_gerencial = $penalidad_gerencial->where('id_registro_cobranza',$id_registro_cobranza);
        // }

        $penalidad_gerencial = $penalidad_gerencial->get();

        return response()->json([
            "success"=>true,
            "status"=>200,
            "penalidades"=>$penalidad_gerencial
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
        $contribuyente = Contribuyente::where('id_cliente_gerencial_old',$id)->where('id_cliente_gerencial_old','!=',null)->first();
        $cliente_gerencial=null;
        if (!$contribuyente) {
            $cliente_gerencial = Cliente::where('id_cliente',$id)->first();

            $contribuyente = new Contribuyente;
            $contribuyente->nro_documento     = $cliente_gerencial->ruc;
            $contribuyente->razon_social      = $cliente_gerencial->nombre;
            $contribuyente->id_pais           = 170;
            $contribuyente->estado            = 1;
            $contribuyente->fecha_registro    = date('Y-m-d H:i:s');
            $contribuyente->transportista     = false;

            $contribuyente->ubigeo            = 0;

            $contribuyente->id_cliente_gerencial_old    = $cliente_gerencial->id_cliente;
            $contribuyente->save();

            $com_cliente = new ComercialCliente();
            $com_cliente->id_contribuyente=$contribuyente->id_contribuyente;
            $com_cliente->estado=1;
            $com_cliente->fecha_registro = date('Y-m-d H:i:s');
            $com_cliente->save();
        }
        return response()->json([
            "success"=>true,
            "status"=>200,
            "data"=>$contribuyente,
            "old"=>$cliente_gerencial
        ]);
    }
    public function scriptCobranza()
    {
        $cobranzas = DB::table('gerencial.cobranza')
        // ->limit(1)
        ->get();
        $array = [];
        foreach ($cobranzas as $key => $value) {
            $registro_cobranza = RegistroCobranza::where('id_cobranza_old',$value->id_cobranza)->first();
            if (!$registro_cobranza) {
                $registro_cobranza = new RegistroCobranza();
                $registro_cobranza->id_empresa        = null;
                $registro_cobranza->id_sector         = $value->id_sector;
                $registro_cobranza->id_cliente        = $value->id_cliente;
                $registro_cobranza->factura           = $value->factura;
                $registro_cobranza->uu_ee             = $value->uu_ee;
                $registro_cobranza->fuente_financ     = $value->fuente_financ;
                $registro_cobranza->oc                = $value->oc;
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
                $registro_cobranza->id_cliente_agil   = null;
                $registro_cobranza->id_cobranza_old   = $value->id_cobranza;
                $registro_cobranza->id_empresa_old    = $value->id_empresa;
                $registro_cobranza->save();
            }else{
                $registro_cobranza = RegistroCobranza::find($registro_cobranza->id_registro_cobranza);
                $registro_cobranza->id_empresa        = null;
                $registro_cobranza->id_sector         = $value->id_sector;
                $registro_cobranza->id_cliente        = $value->id_cliente;
                $registro_cobranza->factura           = $value->factura;
                $registro_cobranza->uu_ee             = $value->uu_ee;
                $registro_cobranza->fuente_financ     = $value->fuente_financ;
                $registro_cobranza->oc                = $value->oc;
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
                $registro_cobranza->id_cliente_agil   = null;
                $registro_cobranza->id_cobranza_old   = $value->id_cobranza;
                $registro_cobranza->id_empresa_old    = $value->id_empresa;
                $registro_cobranza->save();
            }

            // array_push($array,array(
            //     // "id_registro_cobranza" => ,
            //     "id_empresa"        =>null,
            //     "id_sector"         =>$value->id_sector,
            //     "id_cliente"        =>$value->id_cliente,
            //     "factura"           =>$value->factura,
            //     "uu_ee"             =>$value->uu_ee,
            //     "fuente_financ"     =>$value->fuente_financ,
            //     "oc"                =>$value->oc,
            //     "siaf"              =>$value->siaf,
            //     "fecha_emision"     =>$value->fecha_emision,
            //     "fecha_recepcion"   =>$value->fecha_recepcion,
            //     "moneda"            =>$value->moneda,
            //     "importe"           =>$value->importe,
            //     "id_estado_doc"     =>$value->id_estado_doc,
            //     "id_tipo_tramite"   =>$value->id_tipo_tramite,
            //     "vendedor"          =>$value->vendedor,
            //     "estado"            =>$value->estado,
            //     "fecha_registro"    =>$value->fecha_registro,
            //     "id_area"           =>$value->id_area,
            //     "id_periodo"        =>$value->id_periodo,
            //     "codigo_empresa"    =>$value->codigo_empresa,
            //     "categoria"         =>$value->categoria,
            //     "cdp"               =>$value->cdp,
            //     "plazo_credito"     =>$value->plazo_credito,
            //     "id_doc_ven"        =>$value->id_venta,
            //     "id_cliente_agil"   =>null,
            //     "id_cobranza_old"   =>$value->id_cobranza,
            //     "id_empresa_old"    =>$value->id_empresa,
            // ));
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
        // return response()->json($request);
        $data = RegistroCobranza::where('registros_cobranzas.estado',1)
        ->select(
            'registros_cobranzas.*',
            'sector.nombre AS nombre_sector',
        )
        ->join('gerencia_cobranza.sector', 'sector.id_sector','=', 'registros_cobranzas.id_sector')
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
            $data = $data->join('gerencia_cobranza.cobranza_fase', function ($join) use($fase_text){
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
            $id_cliente =$value->id_empresa;
            if (!$id_cliente) {
                $id_cliente =$value->id_empresa_old;
                $adm_contri = Contribuyente::where('id_empresa_gerencial_old',$value->id_empresa_old)->first();
                $id_cliente = $adm_contri->id_contribuyente;

            }

            $empresa = DB::table('administracion.adm_empresa')
            ->select(
                'adm_empresa.id_contribuyente',
                'adm_empresa.codigo',
                'adm_contri.razon_social'
            )
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->where('adm_empresa.id_contribuyente',$id_cliente)
            ->first();
            $value->empresa = $empresa?$empresa->razon_social:'--';

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
            $fase = CobanzaFase::where('id_cobranza', $value->id_cobranza_old)->where('id_cobranza','!=',null)->where('estado',1)->first();
            if (!$fase) {
                $fase = CobanzaFase::where('id_registro_cobranza', $value->id_registro_cobranza)->where('estado',1)->first();
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

        }
        return Excel::download(new CobranzasExpor($data), 'reporte_requerimientos_bienes_servicios.xlsx');
        // return response()->json($data);
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
}
