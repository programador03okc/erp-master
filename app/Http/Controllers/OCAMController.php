<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

use App\Models\Logistica\Empresa;
use App\Models\Tesoreria\Usuario;
use App\Models\Tesoreria\Grupo;
use DataTables;
date_default_timezone_set('America/Lima');

class OCAMController extends Controller
{

    function view_lista_ocams()
    {
        $grupos = Auth::user()->getAllGrupo();
        $roles = $this->userSession()['roles'];
        $empresas = $this->select_mostrar_empresas();
        $empresas_am =  $this->select_mostrar_empresas_am();
        $periodos = $this->mostrar_periodos();

        return view('logistica/ocam/lista_ocams', compact('periodos','grupos','roles','empresas','empresas_am'));
    }

    public function userSession()
    {
        $id_rol = Auth::user()->rol;
        $id_usuario = Auth::user()->id_usuario;
        $id_trabajador = Auth::user()->id_trabajador;
        $usuario = Auth::user()->usuario;
        $estado = Auth::user()->estado;
        $nombre_corto = Auth::user()->nombre_corto;

        $dateNow= date('Y-m-d');

        $dataSession=[
            'id_rol'=>$id_rol,
            'id_usuario'=>$id_usuario,
            'id_trabajador'=>$id_trabajador,
            'usuario'=>$usuario,
            'estado'=>$estado,
            'nombre_corto'=>$nombre_corto,
            'roles'=>[]
        ];

        $rolConceptoUser = DB::table('configuracion.sis_acceso')
        ->select(
            'sis_rol.id_rol',
            'sis_rol.id_grupo',
            'sis_rol.descripcion as rol_concepto',
            'sis_rol.estado'
        )
        ->leftJoin('configuracion.sis_rol', 'sis_rol.id_rol', '=', 'sis_acceso.id_rol')
        // ->where(function($q) use ($dateNow) {
        //     $q->where('rol_aprobacion.fecha_fin','>', $dateNow)
        //     ->orWhere('rol_aprobacion.fecha_fin', null);
        // })
        ->where([
            ['sis_acceso.id_usuario', '=', $id_usuario]
            ])
        ->get();

        $dataSession['roles']=$rolConceptoUser;

        return $dataSession;
    }
    public function select_mostrar_empresas()
    {
        $data = DB::table('administracion.adm_empresa')
            ->select('adm_empresa.id_empresa', 'adm_empresa.logo_empresa','adm_contri.nro_documento', 'adm_contri.razon_social')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->where('adm_empresa.estado', '=', 1)
            ->orderBy('adm_contri.razon_social', 'asc')
            ->get();
        return $data;
    }

    public function select_mostrar_empresas_am()
    {
        $empresas = DB::table('mgcp_acuerdo_marco.empresas')
            ->select('empresas.*')
            ->orderBy('empresas.id', 'asc')
            ->get();
        return $empresas;
    }
    function mostrar_periodos()
    {
        $data = DB::table('administracion.adm_periodo')
            ->select(
                'adm_periodo.*'
            )
            ->where([
                ['adm_periodo.estado', '=', 1]
            ])
            ->orderBy('adm_periodo.id_periodo', 'desc')
            ->get();
        return $data;
    }

    function lista_ordenes_propias($id_empresa,$year_publicacion,$condicion){

        $hasWhere=[];
        
        if($id_empresa >0){
            $hasWhere[]=['oc_propias.id_empresa','=',$id_empresa];
        }
        if($condicion == 'PENDIENTES'){
            $hasWhere[]=['alm_req.id_requerimiento','=',NULL];
        }
        if($condicion == 'VINCULADAS'){
            $hasWhere[]=['alm_req.id_requerimiento','>',0];
        }

        
        $oc_propias = DB::table('mgcp_acuerdo_marco.oc_propias')
        ->select(
            'oc_propias.*',
            'empresas.empresa',
            'acuerdo_marco.descripcion_corta as am',
            'entidades.nombre as entidad',
            'cc.estado_aprobacion as id_estado_aprobacion_cc',
            'estados_aprobacion.estado as estado_aprobacion_cc',
            'oportunidades.id_tipo_negocio',
            'tipos_negocio.tipo as tipo_negocio',
            'cc.id as id_cc',
            'alm_req.id_requerimiento',
            'alm_req.codigo as codigo_requerimiento',
            'cc.tipo_cuadro',
            'cc_am_filas.id as id_am_filas',
            'cc_venta_filas.id as id_venta_filas',
            'oportunidades.id_tipo_negocio',
            'tipos_negocio.tipo as tipo_negocio',
            DB::raw("(SELECT COUNT(id) FROM mgcp_cuadro_costos.cc_am_filas WHERE cc_am_filas.descripcion_producto_transformado IS NOT NULL AND cc_am_filas.id_cc_am =cc.id ) AS cantidad_producto_con_transformacion")
            )
        ->leftJoin('mgcp_acuerdo_marco.empresas', 'empresas.id', '=', 'oc_propias.id_empresa')
        ->leftJoin('mgcp_acuerdo_marco.entidades', 'entidades.id', '=', 'oc_propias.id_entidad')
        ->leftJoin('mgcp_acuerdo_marco.catalogos', 'catalogos.id', '=', 'oc_propias.id_catalogo')
        ->leftJoin('mgcp_acuerdo_marco.acuerdo_marco', 'acuerdo_marco.id', '=', 'catalogos.id_acuerdo_marco')
        ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id_oportunidad', '=', 'oc_propias.id_oportunidad')
        ->leftJoin('mgcp_cuadro_costos.estados_aprobacion', 'estados_aprobacion.id', '=', 'cc.estado_aprobacion')
        ->leftJoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
        ->leftJoin('mgcp_oportunidades.tipos_negocio', 'tipos_negocio.id', '=', 'oportunidades.id_tipo_negocio')
        ->leftJoin('mgcp_cuadro_costos.cc_venta_filas', 'cc_venta_filas.id', '=', 'cc.id')
        ->leftJoin('mgcp_cuadro_costos.cc_am_filas', 'cc_am_filas.id', '=', 'cc.id')
        ->leftJoin('almacen.alm_req', 'alm_req.id_cc', '=', 'cc.id')
        ->where($hasWhere)
        // ->whereYear($hasWhereYear)
        ->orderBy('oc_propias.fecha_publicacion', 'desc');

        if($year_publicacion != 'null'){
            $oc_propias->whereYear('oc_propias.fecha_publicacion','=',$year_publicacion)->get();
        }else{
            $oc_propias->get();
        }

        // return datatables($oc_propias)->toJson();
       return Datatables::of($oc_propias)
    //    ->filterColumn('cantidad_producto_con_transformacion', function($query, $keyword) {
    //     $sql = "(SELECT COUNT(*) FROM mgcp_cuadro_costos.cc_am_filas 
    //     WHERE cc_am_filas.descripcion_producto_transformado IS NOT NULL 
    //     AND cc_am_filas.id_cc_am = cc.id ) AS cantidad_producto_con_transformacion";
    //     $query->whereRaw($sql);

    //     })
        ->toJson();

        // ->make(true);
        // return response()->json($response);


    }

    function listaProductosBaseoTransformado($idRequerimiento,$conTransformacion){
        $status=0;
        $data = DB::table('almacen.alm_det_req')
        ->select(
            'alm_det_req.id_detalle_requerimiento',
            'alm_req.id_requerimiento',
            'alm_req.codigo AS codigo_requerimiento',
            'alm_req.id_sede',
            'alm_det_req.id_requerimiento',
            'alm_det_req.id_item AS id_item_alm_det_req',
            'alm_det_req.precio_unitario',
            'alm_det_req.subtotal',
            'alm_det_req.cantidad',
            'alm_det_req.stock_comprometido',
            'alm_det_req.id_unidad_medida',
            'und_medida_det_req.descripcion AS unidad_medida',
            'alm_det_req.observacion',
            'alm_det_req.fecha_registro AS fecha_registro_alm_det_req',
            'alm_det_req.lugar_entrega',
            'alm_det_req.descripcion_adicional',
            'alm_det_req.id_tipo_item',
            'alm_det_req.id_moneda as id_tipo_moneda',
            'sis_moneda.descripcion as tipo_moneda',
            'sis_moneda.simbolo as simbolo_moneda',
            'alm_det_req.estado',
            'adm_estado_doc.estado_doc',
            'adm_estado_doc.bootstrap_color',
            'alm_det_req.partida',
            'alm_det_req.centro_costo_id as id_centro_costo',
            'centro_costo.codigo as codigo_centro_costo',
            'alm_det_req.id_producto',
            'alm_cat_prod.descripcion as categoria',
            'alm_subcat.descripcion as subcategoria',
            'alm_det_req.id_almacen_reserva',
            'alm_almacen.descripcion as almacen_reserva',
            'alm_item.codigo AS codigo_item',
            'alm_item.fecha_registro AS alm_item_fecha_registro',
            'alm_prod.codigo AS alm_prod_codigo',
            'alm_prod.part_number',
            // 'alm_prod.descripcion AS alm_prod_descripcion',
            'alm_det_req.tiene_transformacion',
            'alm_det_req.proveedor_id',
            'adm_contri.nro_documento as proveedor_nro_documento',
            'adm_contri.razon_social as proveedor_razon_social',
            'alm_det_req.id_cc_am_filas',
            'alm_det_req.id_cc_venta_filas',  
            'alm_det_req_adjuntos.id_adjunto AS adjunto_id_adjunto',
            'alm_det_req_adjuntos.archivo AS adjunto_archivo',
            'alm_det_req_adjuntos.estado AS adjunto_estado',
            'alm_det_req_adjuntos.fecha_registro AS adjunto_fecha_registro',
            'alm_det_req_adjuntos.id_detalle_requerimiento AS adjunto_id_detalle_requerimiento'
        )
        ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
        ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
        ->leftJoin('almacen.alm_prod', 'alm_item.id_producto', '=', 'alm_prod.id_producto')
        ->leftJoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
        ->leftJoin('almacen.alm_subcat','alm_subcat.id_subcategoria','=','alm_prod.id_subcategoria')
        ->leftJoin('almacen.alm_almacen', 'alm_det_req.id_almacen_reserva', '=', 'alm_almacen.id_almacen')
        ->leftJoin('configuracion.sis_moneda', 'alm_det_req.id_moneda', '=', 'sis_moneda.id_moneda')
        ->leftJoin('almacen.alm_und_medida', 'alm_det_req.id_unidad_medida', '=', 'alm_und_medida.id_unidad_medida')
        ->leftJoin('almacen.alm_und_medida as und_medida_det_req', 'alm_det_req.id_unidad_medida', '=', 'und_medida_det_req.id_unidad_medida')
        ->leftJoin('almacen.alm_det_req_adjuntos', 'alm_det_req_adjuntos.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')
        ->leftJoin('finanzas.centro_costo', 'centro_costo.id_centro_costo', '=', 'alm_det_req.centro_costo_id')
        ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'alm_det_req.proveedor_id')
        ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->leftJoin('administracion.adm_estado_doc', 'alm_det_req.estado', '=', 'adm_estado_doc.id_estado_doc')

        ->where([
            ['alm_det_req.id_requerimiento', '=', $idRequerimiento],
            ['alm_det_req.tiene_transformacion', '=', $conTransformacion]
        ])
        ->orderBy('alm_det_req.id_detalle_requerimiento', 'asc')
        ->get();

        if(count($data)>=1){
            $status = 200;
        }

    return ['data'=>$data, 'status'=>$status];
    }
}