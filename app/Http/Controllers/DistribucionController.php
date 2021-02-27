<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Dompdf\Dompdf;
use PDF;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
date_default_timezone_set('America/Lima');

class DistribucionController extends Controller
{
    public function __construct(){
        // session_start();
    }
    function view_ordenesDespacho(){
        $usuarios = AlmacenController::select_usuarios();
        $sis_identidad = AlmacenController::sis_identidad_cbo();
        $clasificaciones = AlmacenController::mostrar_clasificaciones_cbo();
        $subcategorias = AlmacenController::mostrar_subcategorias_cbo();
        $categorias = AlmacenController::mostrar_categorias_cbo();
        $unidades = AlmacenController::mostrar_unidades_cbo();
        return view('almacen/distribucion/ordenesDespacho', compact('usuarios','sis_identidad','clasificaciones','subcategorias','categorias','unidades'));
    }
    function view_despachosPendientes(){
        $tp_operacion = AlmacenController::tp_operacion_cbo_sal();
        $clasificaciones = AlmacenController::mostrar_guia_clas_cbo();
        $usuarios = AlmacenController::select_usuarios();
        $motivos_anu = AlmacenController::select_motivo_anu();
        return view('almacen/guias/despachosPendientes', compact('tp_operacion','clasificaciones','usuarios','motivos_anu'));
    }
    function view_requerimientoPagos(){
        // $usuarios = AlmacenController::select_usuarios();
        return view('almacen/pagos/requerimientoPagos');
    }
    function view_trazabilidad_requerimientos(){
        return view('almacen/distribucion/trazabilidadRequerimientos');
    }
    function view_guias_transportistas(){
        return view('almacen/distribucion/guiasTransportistas');
    }

    public function actualizaCantidadDespachosTabs(){
        $count_pendientes = DB::table('almacen.alm_req')
        ->where([['alm_req.estado','=',1]])
            // ->where([['alm_req.estado','=',1], ['alm_req.confirmacion_pago','=',false]])//muestra todos los reservados
            // ->orWhere([['alm_req.id_tipo_requerimiento','!=',1], ['alm_req.estado','=',19], ['alm_req.confirmacion_pago','=',false]])
                ->count();

        $count_confirmados = DB::table('almacen.alm_req')
            ->leftJoin('almacen.orden_despacho', function($join){   
                        $join->on('orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento');
                        $join->where('orden_despacho.estado','!=', 7);
                    })
            // ->where([['alm_req.estado','=',1], ['alm_req.confirmacion_pago','=',true]])
            ->orWhere([['alm_req.estado','=',5]])
            ->orWhere([['alm_req.estado','=',15]])
            // ->orWhere([['alm_req.id_tipo_requerimiento','!=',1], ['alm_req.estado','=',19], 
            //         ['alm_req.confirmacion_pago','=',true], ['orden_despacho.id_od','=',null]])
                ->count();
        
        $count_en_proceso = DB::table('almacen.alm_req')
            ->leftJoin('almacen.orden_despacho', function($join){   
                $join->on('orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento');
                $join->where('orden_despacho.aplica_cambios', '=', false);
                $join->where('orden_despacho.estado','!=', 7);
            })
            ->where('alm_req.estado',17)
            // ->orWhere('alm_req.estado',10)
            // ->orWhere('alm_req.estado',29)
            ->orWhere('alm_req.estado',27)
            ->orWhere('alm_req.estado',28)
            ->orWhere([['alm_req.estado','=',19], ['alm_req.confirmacion_pago','=',true]])
            // ->orWhere('alm_req.estado',22)
            ->count();

        $count_en_transformacion = DB::table('almacen.alm_req')
            ->leftJoin('almacen.orden_despacho', function($join){   
                $join->on('orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento');
                $join->where('orden_despacho.aplica_cambios', '=', false);
                $join->where('orden_despacho.estado','!=', 7);
            })
            // ->where('alm_req.estado',17)
            ->orWhere('alm_req.estado',10)
            ->orWhere('alm_req.estado',29)
            // ->orWhere('alm_req.estado',27)
            // ->orWhere('alm_req.estado',28)
            // ->orWhere([['alm_req.estado','=',19], ['alm_req.confirmacion_pago','=',true]])
            ->orWhere('alm_req.estado',22)
            ->count();

        $count_por_despachar = DB::table('almacen.orden_despacho')
            ->where('orden_despacho.estado',23)
            ->count();

        $count_despachados = DB::table('almacen.orden_despacho_grupo_det')
        ->join('almacen.orden_despacho','orden_despacho.id_od','=','orden_despacho_grupo_det.id_od')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
        ->where([['orden_despacho_grupo_det.estado','!=',7],['alm_req.estado','=',20]])//Despachado
        ->count();

        $count_cargo = DB::table('almacen.orden_despacho_grupo_det')
        ->join('almacen.orden_despacho','orden_despacho.id_od','=','orden_despacho_grupo_det.id_od')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
        ->where([['orden_despacho_grupo_det.estado','!=',7],['alm_req.estado','=',25]])//Pendiente de cargo
        ->count();
        
        return response()->json(['count_pendientes'=>$count_pendientes,
                                 'count_confirmados'=>$count_confirmados,
                                 'count_en_proceso'=>$count_en_proceso,
                                 'count_en_transformacion'=>$count_en_transformacion,
                                 'count_por_despachar'=>$count_por_despachar,
                                 'count_despachados'=>$count_despachados,
                                 'count_cargo'=>$count_cargo]);
    }
    public function listarRequerimientosElaborados(){
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*','sis_usua.nombre_corto as responsable',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            // DB::raw("(ubi_dis.descripcion) || ' - ' || (ubi_prov.descripcion) || ' - ' || (ubi_dpto.descripcion) AS ubigeo_descripcion"),
            // 'rrhh_perso.nro_documento as dni_persona','alm_almacen.descripcion as almacen_descripcion',
            'alm_req.id_sede as sede_requerimiento','sede_req.descripcion as sede_descripcion_req',
            'oc_propias.orden_am','oportunidades.oportunidad','oportunidades.codigo_oportunidad',
            'entidades.nombre','oc_propias.id as id_oc_propia','oc_propias.url_oc_fisica',
            'oc_propias.monto_total','users.name as user_name'
            // 'alm_tp_req.descripcion as tipo_req',
            // DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
                    // 'adm_contri.nro_documento as cliente_ruc','adm_contri.razon_social as cliente_razon_social',
            )
            // ->join('almacen.alm_tp_req','alm_tp_req.id_tipo_requerimiento','=','alm_req.id_tipo_requerimiento')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            // ->leftjoin('administracion.adm_grupo','adm_grupo.id_grupo','=','alm_req.id_grupo')
            ->leftjoin('mgcp_cuadro_costos.cc','cc.id','=','alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades','oportunidades.id','=','cc.id_oportunidad')
            ->leftjoin('mgcp_usuarios.users','users.id','=','oportunidades.id_responsable')
            ->leftjoin('mgcp_acuerdo_marco.oc_propias','oc_propias.id_oportunidad','=','oportunidades.id')
            ->leftjoin('mgcp_acuerdo_marco.entidades','entidades.id','=','oportunidades.id_entidad')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
            ->leftJoin('administracion.sis_sede as sede_req','sede_req.id_sede','=','alm_req.id_sede')
            // ->leftJoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_req.id_almacen')
            // ->leftJoin('configuracion.ubi_dis','ubi_dis.id_dis','=','alm_req.id_ubigeo_entrega')
            // ->leftJoin('configuracion.ubi_prov','ubi_prov.id_prov','=','ubi_dis.id_prov')
            // ->leftJoin('configuracion.ubi_dpto','ubi_dpto.id_dpto','=','ubi_prov.id_dpto')
            // ->leftJoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','alm_req.id_persona')
            // ->leftJoin('comercial.com_cliente','com_cliente.id_cliente','=','alm_req.id_cliente')
            // ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->where([['alm_req.estado','=',1]])//muestra todos los reservados  ['alm_req.confirmacion_pago','=',false]
            // ->orWhere([['alm_req.id_tipo_requerimiento','!=',1], ['alm_req.estado','=',19], ['alm_req.confirmacion_pago','=',false]])
            ->orderBy('alm_req.fecha_requerimiento','desc');
            // ->get();
        return datatables($data)->toJson();
        // return response()->json($data);
    }

    public function listarRequerimientosConfirmados(){
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*','sis_usua.nombre_corto as responsable',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            DB::raw("(ubi_dis.descripcion) || ' - ' || (ubi_prov.descripcion) || ' - ' || (ubi_dpto.descripcion) AS ubigeo_descripcion"),
            // 'rrhh_perso.nro_documento as dni_persona','alm_almacen.descripcion as almacen_descripcion',
            'alm_req.id_sede as sede_requerimiento','sede_req.descripcion as sede_descripcion_req',
            // 'alm_tp_req.descripcion as tipo_req',
            // DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
            // 'adm_contri.nro_documento as cliente_ruc','adm_contri.razon_social as cliente_razon_social',
            'oc_propias.orden_am','oportunidades.oportunidad','oportunidades.codigo_oportunidad',
            'entidades.nombre','orden_despacho.id_od','oc_propias.id as id_oc_propia','oc_propias.url_oc_fisica',
            'oc_propias.monto_total','users.name as user_name'
            //,'orden_despacho.codigo as codigo_od','orden_despacho.estado as estado_od'
            )
            // ->join('almacen.alm_tp_req','alm_tp_req.id_tipo_requerimiento','=','alm_req.id_tipo_requerimiento')
            ->leftjoin('mgcp_cuadro_costos.cc','cc.id','=','alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades','oportunidades.id','=','cc.id_oportunidad')
            ->leftjoin('mgcp_usuarios.users','users.id','=','oportunidades.id_responsable')
            ->leftjoin('mgcp_acuerdo_marco.oc_propias','oc_propias.id_oportunidad','=','oportunidades.id')
            ->leftjoin('mgcp_acuerdo_marco.entidades','entidades.id','=','oportunidades.id_entidad')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            // ->leftjoin('administracion.adm_grupo','adm_grupo.id_grupo','=','alm_req.id_grupo')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
            ->leftJoin('administracion.sis_sede as sede_req','sede_req.id_sede','=','alm_req.id_sede')
            // ->leftJoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_req.id_almacen')
            ->leftJoin('configuracion.ubi_dis','ubi_dis.id_dis','=','alm_req.id_ubigeo_entrega')
            ->leftJoin('configuracion.ubi_prov','ubi_prov.id_prov','=','ubi_dis.id_prov')
            ->leftJoin('configuracion.ubi_dpto','ubi_dpto.id_dpto','=','ubi_prov.id_dpto')
            // ->leftJoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','alm_req.id_persona')
            // ->leftJoin('comercial.com_cliente','com_cliente.id_cliente','=','alm_req.id_cliente')
            // ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->leftJoin('almacen.orden_despacho', function($join)
                         {  $join->on('orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento');
                            $join->where('orden_despacho.estado','!=', 7);
                         })
            // ->where([['alm_req.estado','=',1], ['alm_req.confirmacion_pago','=',true]])
            ->orWhere([['alm_req.estado','=',5]])
            ->orWhere([['alm_req.estado','=',15]])
            // ->orWhere([['alm_req.id_tipo_requerimiento','!=',1], ['alm_req.estado','=',19], 
            //            ['alm_req.confirmacion_pago','=',true], ['orden_despacho.id_od','=',null]])
            ->orderBy('alm_req.fecha_requerimiento','desc');
            // ->get();
        return datatables($data)->toJson();
        // return response()->json($data);
    }

    public function listarRequerimientosEnProceso(){
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*','sis_usua.nombre_corto as responsable',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            DB::raw("(ubi_dis.descripcion) || ' - ' || (ubi_prov.descripcion) || ' - ' || (ubi_dpto.descripcion) AS ubigeo_descripcion"),
            'rrhh_perso.nro_documento as dni_persona',
            'alm_almacen.descripcion as almacen_descripcion',
            'alm_req.id_sede as sede_requerimiento',
            'sede_req.descripcion as sede_descripcion_req',
            'orden_despacho.id_od','orden_despacho.codigo as codigo_od','orden_despacho.estado as estado_od',
            'orden_despacho.aplica_cambios',
            DB::raw("(SELECT COUNT(*) FROM almacen.orden_despacho where
                    orden_despacho.id_requerimiento = alm_req.id_requerimiento
                    and orden_despacho.aplica_cambios = true
                    and orden_despacho.estado != 7) AS count_despachos_internos"),
            DB::raw("(SELECT COUNT(*) FROM almacen.orden_despacho_adjunto where
                    orden_despacho_adjunto.id_od = orden_despacho.id_od
                    and orden_despacho_adjunto.estado != 7) AS count_despacho_adjuntos"),
            'orden_despacho.fecha_despacho','orden_despacho.hora_despacho',
            DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
            'adm_contri.nro_documento as cliente_ruc','adm_contri.razon_social as cliente_razon_social',
            DB::raw("(SELECT COUNT(*) FROM almacen.trans where
                        trans.id_requerimiento = alm_req.id_requerimiento
                        and trans.estado != 7) AS count_transferencia"),
            DB::raw("(SELECT COUNT(*) FROM almacen.trans where
                        trans.id_requerimiento = alm_req.id_requerimiento
                        and trans.estado = 14) AS count_transferencia_recibida"),
            'oc_propias.orden_am','oportunidades.oportunidad','oportunidades.codigo_oportunidad','entidades.id as id_entidad',
            'entidades.nombre','orden_despacho.id_od','oc_propias.id as id_oc_propia','oc_propias.url_oc_fisica',
            'oc_propias.monto_total','users.name as user_name',
            'entidades.responsable as entidad_persona','entidades.direccion as entidad_direccion','entidades.telefono as entidad_telefono','entidades.correo as entidad_email',
            'adm_ctb_contac.nombre as contacto_persona','adm_ctb_contac.direccion as contacto_direccion',
            'adm_ctb_contac.telefono as contacto_telefono','adm_ctb_contac.email as contacto_email'
            )
            ->leftjoin('mgcp_cuadro_costos.cc','cc.id','=','alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades','oportunidades.id','=','cc.id_oportunidad')
            ->leftjoin('mgcp_usuarios.users','users.id','=','oportunidades.id_responsable')
            ->leftjoin('mgcp_acuerdo_marco.oc_propias','oc_propias.id_oportunidad','=','oportunidades.id')
            ->leftjoin('contabilidad.adm_ctb_contac','adm_ctb_contac.id_datos_contacto','=','oc_propias.id_contacto')
            ->leftjoin('mgcp_acuerdo_marco.entidades','entidades.id','=','oportunidades.id_entidad')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
            ->leftJoin('administracion.sis_sede as sede_req','sede_req.id_sede','=','alm_req.id_sede')
            ->leftJoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_req.id_almacen')
            ->leftJoin('configuracion.ubi_dis','ubi_dis.id_dis','=','alm_req.id_ubigeo_entrega')
            ->leftJoin('configuracion.ubi_prov','ubi_prov.id_prov','=','ubi_dis.id_prov')
            ->leftJoin('configuracion.ubi_dpto','ubi_dpto.id_dpto','=','ubi_prov.id_dpto')
            ->leftJoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','alm_req.id_persona')
            ->leftJoin('comercial.com_cliente','com_cliente.id_cliente','=','alm_req.id_cliente')
            ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->leftJoin('almacen.orden_despacho', function($join)
                         {  $join->on('orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento');
                            $join->where('orden_despacho.aplica_cambios', '=', false);
                            $join->where('orden_despacho.estado','!=', 7);
                         })
            ->where('alm_req.estado',17)
            // ->orWhere('alm_req.estado',10)
            // ->orWhere('alm_req.estado',29)
            ->orWhere('alm_req.estado',27)
            ->orWhere('alm_req.estado',28)
            ->orWhere([['alm_req.estado','=',19], ['alm_req.confirmacion_pago','=',true]])
            // ->orWhere([['alm_req.estado','=',22]])
            ->orderBy('alm_req.fecha_entrega','desc');
        return datatables($data)->toJson();
        // return response()->json($data);
    }

    public function listarRequerimientosEnTransformacion(){
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*','sis_usua.nombre_corto as responsable',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            DB::raw("(ubi_dis.descripcion) || ' - ' || (ubi_prov.descripcion) || ' - ' || (ubi_dpto.descripcion) AS ubigeo_descripcion"),
            'rrhh_perso.nro_documento as dni_persona',
            'alm_almacen.descripcion as almacen_descripcion',
            'alm_req.id_sede as sede_requerimiento',
            'sede_req.descripcion as sede_descripcion_req',
            'orden_despacho.id_od','orden_despacho.codigo as codigo_od','orden_despacho.estado as estado_od',
            'orden_despacho.aplica_cambios',
            DB::raw("(SELECT COUNT(*) FROM almacen.orden_despacho where
                    orden_despacho.id_requerimiento = alm_req.id_requerimiento
                    and orden_despacho.aplica_cambios = true
                    and orden_despacho.estado != 7) AS count_despachos_internos"),
            DB::raw("(SELECT COUNT(*) FROM almacen.orden_despacho_adjunto where
                    orden_despacho_adjunto.id_od = orden_despacho.id_od
                    and orden_despacho_adjunto.estado != 7) AS count_despacho_adjuntos"),
            'orden_despacho.fecha_despacho','orden_despacho.hora_despacho',
            DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
            'adm_contri.nro_documento as cliente_ruc','adm_contri.razon_social as cliente_razon_social',
            DB::raw("(SELECT COUNT(*) FROM almacen.trans where
                        trans.id_requerimiento = alm_req.id_requerimiento
                        and trans.estado != 7) AS count_transferencia"),
            DB::raw("(SELECT COUNT(*) FROM almacen.trans where
                        trans.id_requerimiento = alm_req.id_requerimiento
                        and trans.estado = 14) AS count_transferencia_recibida"),
            'oc_propias.orden_am','oportunidades.oportunidad','oportunidades.codigo_oportunidad','entidades.id as id_entidad',
            'entidades.nombre','orden_despacho.id_od','oc_propias.id as id_oc_propia','oc_propias.url_oc_fisica',
            'oc_propias.monto_total','users.name as user_name',
            'entidades.responsable as entidad_persona','entidades.direccion as entidad_direccion','entidades.telefono as entidad_telefono','entidades.correo as entidad_email',
            'adm_ctb_contac.nombre as contacto_persona','adm_ctb_contac.direccion as contacto_direccion',
            'adm_ctb_contac.telefono as contacto_telefono','adm_ctb_contac.email as contacto_email'
            )
            ->leftjoin('mgcp_cuadro_costos.cc','cc.id','=','alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades','oportunidades.id','=','cc.id_oportunidad')
            ->leftjoin('mgcp_usuarios.users','users.id','=','oportunidades.id_responsable')
            ->leftjoin('mgcp_acuerdo_marco.oc_propias','oc_propias.id_oportunidad','=','oportunidades.id')
            ->leftjoin('contabilidad.adm_ctb_contac','adm_ctb_contac.id_datos_contacto','=','oc_propias.id_contacto')
            ->leftjoin('mgcp_acuerdo_marco.entidades','entidades.id','=','oportunidades.id_entidad')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
            ->leftJoin('administracion.sis_sede as sede_req','sede_req.id_sede','=','alm_req.id_sede')
            ->leftJoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_req.id_almacen')
            ->leftJoin('configuracion.ubi_dis','ubi_dis.id_dis','=','alm_req.id_ubigeo_entrega')
            ->leftJoin('configuracion.ubi_prov','ubi_prov.id_prov','=','ubi_dis.id_prov')
            ->leftJoin('configuracion.ubi_dpto','ubi_dpto.id_dpto','=','ubi_prov.id_dpto')
            ->leftJoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','alm_req.id_persona')
            ->leftJoin('comercial.com_cliente','com_cliente.id_cliente','=','alm_req.id_cliente')
            ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->leftJoin('almacen.orden_despacho', function($join)
                         {  $join->on('orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento');
                            $join->where('orden_despacho.aplica_cambios', '=', false);
                            $join->where('orden_despacho.estado','!=', 7);
                         })
            // ->where('alm_req.estado',17)
            ->orWhere('alm_req.estado',10)
            ->orWhere('alm_req.estado',29)
            // ->orWhere('alm_req.estado',27)
            // ->orWhere('alm_req.estado',28)
            // ->orWhere([['alm_req.estado','=',19], ['alm_req.confirmacion_pago','=',true]])
            ->orWhere([['alm_req.estado','=',22]])
            ->orderBy('alm_req.fecha_entrega','desc');
        return datatables($data)->toJson();
        // return response()->json($data);
    }

    public function listarOrdenesDespacho(Request $request){
        $data = DB::table('almacen.orden_despacho')
        ->select('orden_despacho.*',
        'adm_contri.nro_documento', 'adm_contri.razon_social',
        'alm_req.codigo as codigo_req','alm_req.concepto','ubi_dis.descripcion as ubigeo_descripcion',
        'sis_usua.nombre_corto','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
        DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
        'alm_almacen.descripcion as almacen_descripcion','rrhh_perso.telefono',
        DB::raw("(SELECT COUNT(*) FROM almacen.orden_despacho_adjunto where
                    orden_despacho_adjunto.id_od = orden_despacho.id_od
                    and orden_despacho_adjunto.estado != 7) AS count_despacho_adjuntos"),
        'oc_propias.orden_am','oportunidades.oportunidad','oportunidades.codigo_oportunidad','oc_propias.monto_total',
        'entidades.nombre','orden_despacho.id_od','oc_propias.id as id_oc_propia','oc_propias.url_oc_fisica',
        'users.name as user_name')
        ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','orden_despacho.id_cliente')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
        ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','orden_despacho.id_persona')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','orden_despacho.id_almacen')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->leftjoin('mgcp_cuadro_costos.cc','cc.id','=','alm_req.id_cc')
        ->leftjoin('mgcp_oportunidades.oportunidades','oportunidades.id','=','cc.id_oportunidad')
        ->leftjoin('mgcp_usuarios.users','users.id','=','oportunidades.id_responsable')
        ->leftjoin('mgcp_acuerdo_marco.oc_propias','oc_propias.id_oportunidad','=','oportunidades.id')
        ->leftjoin('mgcp_acuerdo_marco.entidades','entidades.id','=','oportunidades.id_entidad')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
        ->join('configuracion.ubi_dis','ubi_dis.id_dis','=','orden_despacho.ubigeo_destino')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','orden_despacho.registrado_por')
        ->where('orden_despacho.estado',23);
        // ->get();
        return datatables($data)->toJson();
    }

    public function listarGruposDespachados(Request $request){
        $data = DB::table('almacen.orden_despacho_grupo_det')
        ->select('orden_despacho_grupo_det.*','orden_despacho_grupo.fecha_despacho','orden_despacho.codigo as codigo_od',
        'orden_despacho_grupo.observaciones','orden_despacho.direccion_destino','sis_usua.nombre_corto as trabajador_despacho',
        'adm_contri.razon_social as proveedor_despacho','cliente.razon_social as cliente_razon_social',
        DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS cliente_persona"),
        'alm_req.codigo as codigo_req','alm_req.concepto','alm_req.id_requerimiento',
        'ubi_dis.descripcion as ubigeo_descripcion','orden_despacho_grupo.mov_entrega',
        'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color','alm_almacen.descripcion as almacen_descripcion',
        'orden_despacho_grupo.codigo as codigo_odg','orden_despacho.estado as estado_od',
        'oc_propias.orden_am','oportunidades.oportunidad','oportunidades.codigo_oportunidad','oc_propias.monto_total',
        'entidades.nombre','orden_despacho.id_od','oc_propias.id as id_oc_propia','oc_propias.url_oc_fisica',
        'users.name as user_name',
        DB::raw("(SELECT COUNT(*) FROM almacen.orden_despacho_adjunto where
                    orden_despacho_adjunto.id_od = orden_despacho.id_od
                    and orden_despacho_adjunto.estado != 7) AS count_despacho_adjuntos"))
        ->join('almacen.orden_despacho_grupo','orden_despacho_grupo.id_od_grupo','=','orden_despacho_grupo_det.id_od_grupo')
        ->leftjoin('configuracion.sis_usua','sis_usua.id_usuario','=','orden_despacho_grupo.responsable')
        ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','orden_despacho_grupo.id_proveedor')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        // ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','orden_despacho_grupo.estado')
        ->join('almacen.orden_despacho','orden_despacho.id_od','=','orden_despacho_grupo_det.id_od')
        ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','orden_despacho.id_cliente')
        ->leftjoin('contabilidad.adm_contri as cliente','cliente.id_contribuyente','=','com_cliente.id_contribuyente')
        ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','orden_despacho.id_persona')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','orden_despacho.id_almacen')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->leftjoin('mgcp_cuadro_costos.cc','cc.id','=','alm_req.id_cc')
        ->leftjoin('mgcp_oportunidades.oportunidades','oportunidades.id','=','cc.id_oportunidad')
        ->leftjoin('mgcp_usuarios.users','users.id','=','oportunidades.id_responsable')
        ->leftjoin('mgcp_acuerdo_marco.oc_propias','oc_propias.id_oportunidad','=','oportunidades.id')
        ->leftjoin('mgcp_acuerdo_marco.entidades','entidades.id','=','oportunidades.id_entidad')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
        ->join('configuracion.ubi_dis','ubi_dis.id_dis','=','orden_despacho.ubigeo_destino')
        ->where([['orden_despacho_grupo_det.estado','!=',7],['orden_despacho.estado','=',20]]);
        //->get();
        return datatables($data)->toJson();
    }

    public function listarGruposDespachadosPendientesCargo(Request $request){
        $data = DB::table('almacen.orden_despacho_grupo_det')
        ->select('orden_despacho_grupo_det.*','orden_despacho_grupo.fecha_despacho','orden_despacho.codigo as codigo_od',
        'orden_despacho_grupo.observaciones','orden_despacho.direccion_destino','sis_usua.nombre_corto as trabajador_despacho',
        'adm_contri.razon_social as proveedor_despacho','cliente.razon_social as cliente_razon_social',
        DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS cliente_persona"),
        'alm_req.codigo as codigo_req','alm_req.concepto','alm_req.id_requerimiento',
        'ubi_dis.descripcion as ubigeo_descripcion','orden_despacho_grupo.mov_entrega',
        'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color','alm_almacen.descripcion as almacen_descripcion',
        'orden_despacho_grupo.codigo as codigo_odg','orden_despacho.estado as estado_od',
        'oc_propias.orden_am','oportunidades.oportunidad','oportunidades.codigo_oportunidad','oc_propias.monto_total',
        'entidades.nombre','orden_despacho.id_od','oc_propias.id as id_oc_propia','oc_propias.url_oc_fisica',
        'users.name as user_name',
        DB::raw("(SELECT COUNT(*) FROM almacen.orden_despacho_adjunto where
                    orden_despacho_adjunto.id_od = orden_despacho.id_od
                    and orden_despacho_adjunto.estado != 7) AS count_despacho_adjuntos"))
        ->join('almacen.orden_despacho_grupo','orden_despacho_grupo.id_od_grupo','=','orden_despacho_grupo_det.id_od_grupo')
        ->leftjoin('configuracion.sis_usua','sis_usua.id_usuario','=','orden_despacho_grupo.responsable')
        ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','orden_despacho_grupo.id_proveedor')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        // ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','orden_despacho_grupo.estado')
        ->join('almacen.orden_despacho','orden_despacho.id_od','=','orden_despacho_grupo_det.id_od')
        ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','orden_despacho.id_cliente')
        ->leftjoin('contabilidad.adm_contri as cliente','cliente.id_contribuyente','=','com_cliente.id_contribuyente')
        ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','orden_despacho.id_persona')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','orden_despacho.id_almacen')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->leftjoin('mgcp_cuadro_costos.cc','cc.id','=','alm_req.id_cc')
        ->leftjoin('mgcp_oportunidades.oportunidades','oportunidades.id','=','cc.id_oportunidad')
        ->leftjoin('mgcp_usuarios.users','users.id','=','oportunidades.id_responsable')
        ->leftjoin('mgcp_acuerdo_marco.oc_propias','oc_propias.id_oportunidad','=','oportunidades.id')
        ->leftjoin('mgcp_acuerdo_marco.entidades','entidades.id','=','oportunidades.id_entidad')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
        ->join('configuracion.ubi_dis','ubi_dis.id_dis','=','orden_despacho.ubigeo_destino')
        ->where([['orden_despacho_grupo_det.estado','!=',7],['orden_despacho.estado','=',25]]);
        //->get();
        return datatables($data)->toJson();
    }

    public function verDetalleGrupoDespacho($id_od_grupo){
        $data = DB::table('almacen.orden_despacho_grupo_det')
        ->select('orden_despacho_grupo_det.*','orden_despacho.codigo','orden_despacho.direccion_destino',
        'orden_despacho.fecha_despacho','orden_despacho.fecha_entrega','adm_contri.nro_documento',
        'adm_contri.razon_social','alm_req.codigo as codigo_req','alm_req.concepto',
        'ubi_dis.descripcion as ubigeo_descripcion','sis_usua.nombre_corto','adm_estado_doc.estado_doc',
        DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
        'adm_estado_doc.bootstrap_color')
        ->join('almacen.orden_despacho','orden_despacho.id_od','=','orden_despacho_grupo_det.id_od')
        ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','orden_despacho.id_cliente')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
        ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','orden_despacho.id_persona')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->join('configuracion.ubi_dis','ubi_dis.id_dis','=','orden_despacho.ubigeo_destino')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','orden_despacho.registrado_por')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','orden_despacho.estado')
        ->where([['orden_despacho_grupo_det.id_od_grupo','=',$id_od_grupo],['orden_despacho_grupo_det.estado','!=',7]])
        ->get();
        return response()->json($data);
    }

    public function verDetalleDespacho($id_od){
        $data = DB::table('almacen.orden_despacho_det')
        ->select('orden_despacho_det.*','alm_prod.codigo','alm_prod.descripcion',
        'alm_ubi_posicion.codigo as posicion','alm_und_medida.abreviatura','alm_prod.part_number',
        'alm_cat_prod.descripcion as categoria','alm_subcat.descripcion as subcategoria')
        ->leftJoin('almacen.alm_prod','alm_prod.id_producto','=','orden_despacho_det.id_producto')
        ->leftJoin('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=','alm_prod.id_categoria')
        ->leftJoin('almacen.alm_subcat','alm_subcat.id_subcategoria','=','alm_prod.id_subcategoria')
        ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
        ->leftJoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','orden_despacho_det.id_posicion')
        ->where([['orden_despacho_det.id_od','=',$id_od],['orden_despacho_det.estado','!=',7]])
        ->get();
        return response()->json($data);
    }


    public function getEstadosRequerimientos($filtro){
        $hoy = date('Y-m-d');

        if ($filtro == '1'){
            $data = DB::table('almacen.alm_req')
            ->select('alm_req.estado','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
                DB::raw('count(alm_req.id_requerimiento) as cantidad'))
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
            ->groupBy('alm_req.estado','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
                ->where([['alm_req.estado','!=',7],['fecha_requerimiento','=',$hoy]])
                ->orderBy('alm_req.estado','desc')
                ->get();
        } 
        else if ($filtro == '2'){

            $data = DB::table('almacen.alm_req')
            ->select('alm_req.estado','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
                DB::raw('count(alm_req.id_requerimiento) as cantidad'))
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
            ->groupBy('alm_req.estado','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
                ->where([['alm_req.estado','!=',7]])
                ->whereBetween('fecha_requerimiento', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ])
                ->orderBy('alm_req.estado','desc')
                ->get();
        } 
        else if ($filtro == '3'){
            $mes = date('m', strtotime($hoy));

            $data = DB::table('almacen.alm_req')
            ->select('alm_req.estado','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
                DB::raw('count(alm_req.id_requerimiento) as cantidad'))
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
            ->groupBy('alm_req.estado','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
                ->where([['alm_req.estado','!=',7]])
                ->whereMonth('fecha_requerimiento', '=', $mes)
                ->orderBy('alm_req.estado','desc')
                ->get();
        }
        else if ($filtro == '4'){
            $anio = date('Y', strtotime($hoy));

            $data = DB::table('almacen.alm_req')
            ->select('alm_req.estado','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
                DB::raw('count(alm_req.id_requerimiento) as cantidad'))
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
            ->groupBy('alm_req.estado','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
                ->where([['alm_req.estado','!=',7]])
                ->whereYear('fecha_requerimiento', '=', $anio)
                ->orderBy('alm_req.estado','desc')
                ->get();
        }
        
        return response()->json($data);
    }

    public function listarEstadosRequerimientos($estado, $filtro){
        $hoy = date('Y-m-d');

        if ($filtro == '1'){
            $data = DB::table('almacen.alm_req')
            ->select('alm_req.id_requerimiento','alm_req.codigo','alm_req.concepto','sis_usua.nombre_corto',
            'alm_req.fecha_requerimiento')
                ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
                ->where([['alm_req.estado','=',$estado],['fecha_requerimiento','=',$hoy]])
                ->get();
        } 
        else if ($filtro == '2'){
            $data = DB::table('almacen.alm_req')
            ->select('alm_req.id_requerimiento','alm_req.codigo','alm_req.concepto','sis_usua.nombre_corto',
            'alm_req.fecha_requerimiento')
                ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
                ->where([['alm_req.estado','=',$estado]])
                ->whereBetween('fecha_requerimiento', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ])
                ->get();
        } 
        else if ($filtro == '3'){
            $mes = date('m', strtotime($hoy));
            
            $data = DB::table('almacen.alm_req')
            ->select('alm_req.id_requerimiento','alm_req.codigo','alm_req.concepto','sis_usua.nombre_corto',
            'alm_req.fecha_requerimiento')
                ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
                ->where([['alm_req.estado','=',$estado]])
                ->whereMonth('fecha_requerimiento', '=', $mes)
                ->get();
        }
        else if ($filtro == '4'){
            $anio = date('Y', strtotime($hoy));
            
            $data = DB::table('almacen.alm_req')
            ->select('alm_req.id_requerimiento','alm_req.codigo','alm_req.concepto','sis_usua.nombre_corto',
            'alm_req.fecha_requerimiento')
                ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
                ->where([['alm_req.estado','=',$estado]])
                ->whereYear('fecha_requerimiento', '=', $anio)
                ->get();
        }
        return response()->json($data);
    }

    public function listarRequerimientosPendientesPagos(Request $request){
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*','sis_usua.nombre_corto as responsable','adm_grupo.descripcion as grupo',
            'adm_grupo.id_sede','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            'ubi_dis.descripcion as ubigeo_descripcion',
            'rrhh_perso.nro_documento as dni_persona','alm_almacen.descripcion as almacen_descripcion',
            'alm_almacen.id_sede as sede_almacen',
            'alm_tp_req.descripcion as tipo_req','sis_moneda.simbolo',
            DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
            'adm_contri.nro_documento as cliente_ruc','adm_contri.razon_social as cliente_razon_social')
            ->join('almacen.alm_tp_req','alm_tp_req.id_tipo_requerimiento','=','alm_req.id_tipo_requerimiento')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->leftjoin('administracion.adm_grupo','adm_grupo.id_grupo','=','alm_req.id_grupo')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
            ->leftJoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_req.id_almacen')
            ->leftJoin('configuracion.ubi_dis','ubi_dis.id_dis','=','alm_req.id_ubigeo_entrega')
            ->leftJoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','alm_req.id_persona')
            ->leftJoin('comercial.com_cliente','com_cliente.id_cliente','=','alm_req.id_cliente')
            ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->leftJoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','alm_req.id_moneda')
            ->where([['alm_req.estado','=',1],['alm_req.confirmacion_pago','=',false],['alm_req.tipo_cliente','!=',3],['alm_req.tipo_cliente','!=',4]])
            ->orWhere([['alm_req.estado','=',19],['alm_req.id_tipo_requerimiento','=',2],['alm_req.confirmacion_pago','=',false]]);//muestra todos los reservados
            // ->get();
        return datatables($data)->toJson();
    }

    public function listarRequerimientosConfirmadosPagos(Request $request){
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*','sis_usua.nombre_corto as responsable','adm_grupo.descripcion as grupo',
            'adm_grupo.id_sede','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            'ubi_dis.descripcion as ubigeo_descripcion',
            'rrhh_perso.nro_documento as dni_persona','alm_almacen.descripcion as almacen_descripcion',
            'alm_almacen.id_sede as sede_almacen',
            'alm_tp_req.descripcion as tipo_req',
            DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
            'adm_contri.nro_documento as cliente_ruc','adm_contri.razon_social as cliente_razon_social')
            ->join('almacen.alm_tp_req','alm_tp_req.id_tipo_requerimiento','=','alm_req.id_tipo_requerimiento')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->leftjoin('administracion.adm_grupo','adm_grupo.id_grupo','=','alm_req.id_grupo')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
            ->leftJoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_req.id_almacen')
            ->leftJoin('configuracion.ubi_dis','ubi_dis.id_dis','=','alm_req.id_ubigeo_entrega')
            ->leftJoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','alm_req.id_persona')
            ->leftJoin('comercial.com_cliente','com_cliente.id_cliente','=','alm_req.id_cliente')
            ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->where([['alm_req.estado','=',1],['alm_req.id_tipo_requerimiento','=',1],['alm_req.confirmacion_pago','=',true]])
            ->orWhere([['alm_req.estado','=',19],['alm_req.id_tipo_requerimiento','=',2],['alm_req.confirmacion_pago','=',true]])
            ->orWhere([['alm_req.estado','=',7],['alm_req.confirmacion_pago','=',false],['alm_req.obs_confirmacion','!=',null]]);
            // ->get();
        return datatables($data)->toJson();
    }

    public function verRequerimientosReservados($id,$almacen){
        $detalles = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.*','alm_req.codigo','alm_req.concepto','sis_usua.nombre_corto',
            'alm_almacen.descripcion as almacen_descripcion')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_det_req.id_almacen_reserva')
            ->where([['alm_det_req.id_producto','=',$id],
                     ['alm_det_req.id_almacen_reserva','=',$almacen],
                     ['alm_det_req.estado','=',19]])
            ->get();
        return response()->json($detalles);
    }

    public function verDetalleRequerimiento($id_requerimiento){
        $detalles = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.*','alm_almacen.descripcion as almacen_descripcion',
                    'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
                    'alm_prod.descripcion as producto_descripcion','alm_prod.codigo as producto_codigo',
                    'alm_prod.series','alm_req.id_almacen',
                    'alm_und_medida.abreviatura','alm_cat_prod.descripcion as categoria',
                    'alm_subcat.descripcion as subcategoria','alm_prod.part_number',
                    DB::raw("(SELECT SUM(cantidad) 
                        FROM almacen.orden_despacho_det AS odd
                        INNER JOIN almacen.orden_despacho AS od
                            on(odd.id_od = od.id_od)
                        WHERE odd.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento
                            and odd.estado != 7
                            and od.aplica_cambios = true) AS suma_despachos_internos"),
                    DB::raw("(SELECT SUM(cantidad) 
                        FROM almacen.orden_despacho_det AS odd
                        INNER JOIN almacen.orden_despacho AS od
                            on(odd.id_od = od.id_od)
                        WHERE odd.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento
                            and odd.estado != 7
                            and od.aplica_cambios = false) AS suma_despachos_externos"),
                    // DB::raw("(SELECT SUM(guia.cantidad) 
                    //     FROM almacen.guia_com_det AS guia
                    //     INNER JOIN logistica.log_det_ord_compra AS oc
                    //         on(guia.id_oc_det = oc.id_detalle_orden)
                    //     INNER JOIN almacen.alm_det_req AS req
                    //         on(oc.id_detalle_requerimiento = req.id_detalle_requerimiento)
                    //     INNER JOIN almacen.guia_com AS g
                    //         on(g.id_guia = guia.id_guia_com)
                    //     WHERE req.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento
                    //         and guia.estado != 7
                    //         and g.id_almacen = alm_req.id_almacen
                    //         and oc.estado != 7) AS suma_ingresos"),
                    DB::raw("(SELECT SUM(guia.cantidad) 
                        FROM almacen.guia_com_det AS guia
                        INNER JOIN logistica.log_det_ord_compra AS oc
                            on(guia.id_oc_det = oc.id_detalle_orden)
                        INNER JOIN almacen.alm_det_req AS req
                            on(oc.id_detalle_requerimiento = req.id_detalle_requerimiento)
                        WHERE req.id_detalle_requerimiento = alm_det_req.id_detalle_requerimiento
                            and guia.estado != 7
                            and oc.estado != 7) AS suma_ingresos"),
                    // DB::raw("(SELECT SUM(trans_detalle.cantidad) 
                    //     FROM almacen.trans_detalle 
                    //     WHERE   trans_detalle.id_requerimiento_detalle = alm_det_req.id_detalle_requerimiento AND
                    //         trans_detalle.estado != 7) AS suma_transferencias"),
                    // DB::raw("(SELECT SUM(trans_detalle.cantidad) 
                    //     FROM almacen.trans_detalle 
                    //     WHERE   trans_detalle.id_requerimiento_detalle = alm_det_req.id_detalle_requerimiento AND
                    //         trans_detalle.estado = 14) AS suma_transferencias_recibidas")
                            )
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
            ->leftJoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->leftJoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            ->leftJoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_det_req.id_almacen_reserva')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_det_req.estado')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->where([['alm_det_req.id_requerimiento','=',$id_requerimiento],['alm_det_req.estado','!=',7]])
            ->get();
        return response()->json($detalles);
    }

    public function verSeries($id_detalle_requerimiento){
        $series = DB::table('almacen.alm_det_req')
        ->select('alm_prod_serie.serie','guia_com.serie as serie_guia_com','guia_com.numero as numero_guia_com',
        'guia_ven.serie as serie_guia_ven','guia_ven.numero as numero_guia_ven')
        ->leftJoin('logistica.log_det_ord_compra', function($join)
        {  $join->on('log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
           $join->where('log_det_ord_compra.estado','!=', 7);
        })
        ->leftJoin('almacen.guia_com_det', function($join)
        {  $join->on('guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden');
           $join->where('guia_com_det.estado','!=', 7);
        })
        ->leftJoin('almacen.alm_prod_serie', function($join)
        {  $join->on('alm_prod_serie.id_guia_com_det', '=', 'guia_com_det.id_guia_com_det');
           $join->where('alm_prod_serie.estado','!=', 7);
        })
        ->leftJoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
        ->leftJoin('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven_det', '=', 'alm_prod_serie.id_guia_ven_det')
        ->leftJoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')
        ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_det_req.estado')
        ->where([['alm_det_req.estado','!=',7],['alm_prod_serie.serie','!=', null]])
        ->get();
        return response()->json($series);
    }

    public function verDetalleIngreso($id_requerimiento){
        $data = DB::table('almacen.mov_alm_det')
        ->select('mov_alm_det.*','alm_prod.codigo as codigo_producto','alm_prod.part_number',
        'alm_cat_prod.descripcion as categoria','alm_subcat.descripcion as subcategoria',
        'alm_prod.descripcion as producto_descripcion','alm_und_medida.abreviatura as unidad_producto')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
        ->join('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=','alm_prod.id_categoria')
        ->join('almacen.alm_subcat','alm_subcat.id_subcategoria','=','alm_prod.id_subcategoria')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
        ->join('almacen.guia_com','guia_com.id_guia','=','mov_alm.id_guia_com')
        ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','guia_com.id_oc')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','log_ord_compra.id_requerimiento')
        ->where([['log_ord_compra.id_requerimiento','=',$id_requerimiento],['mov_alm_det.estado','!=',7]])
        ->get();
        return response()->json($data);
    }

    public function guardar_orden_despacho(Request $request){

        try {
            DB::beginTransaction();

            $codigo = $this->ODnextId($request->fecha_despacho,$request->id_sede);
            $usuario = Auth::user()->id_usuario;

            $id_od = DB::table('almacen.orden_despacho')
                ->insertGetId([
                    'id_sede'=>$request->id_sede,
                    'id_requerimiento'=>$request->id_requerimiento,
                    'id_cliente'=>$request->id_cliente,
                    'id_persona'=>($request->id_persona > 0 ? $request->id_persona : null),
                    'id_almacen'=>$request->id_almacen,
                    'telefono'=>$request->telefono_cliente,
                    'codigo'=>$codigo,
                    'ubigeo_destino'=>$request->ubigeo,
                    'direccion_destino'=>$request->direccion_destino,
                    'correo_cliente'=>$request->correo_cliente,
                    'fecha_despacho'=>$request->fecha_despacho,
                    'hora_despacho'=>$request->hora_despacho,
                    'fecha_entrega'=>$request->fecha_entrega,
                    'aplica_cambios'=>($request->aplica_cambios_valor == 'si' ? true : false),
                    'registrado_por'=>$usuario,
                    'tipo_entrega'=>$request->tipo_entrega,
                    'fecha_registro'=>date('Y-m-d H:i:s'),
                    'documento'=>$request->documento,
                    'estado'=>1,
                    'tipo_cliente'=>$request->tipo_cliente
                ],
                    'id_od'
            );

            //Agrega accion en requerimiento
            DB::table('almacen.alm_req_obs')
            ->insert([  'id_requerimiento'=>$request->id_requerimiento,
                        'accion'=>'ORDEN DE DESPACHO',
                        'descripcion'=>'Se generó la Orden de Despacho '.$codigo,
                        'id_usuario'=>$usuario,
                        'fecha_registro'=>date('Y-m-d H:i:s')
                ]);

            if ($request->aplica_cambios_valor == 'si'){
                $fecha_actual = date('Y-m-d');
                $codTrans = $this->transformacion_nextId($fecha_actual);

                $id_transformacion = DB::table('almacen.transformacion')
                    ->insertGetId([
                        'fecha_transformacion'=>$fecha_actual,
                        'codigo'=>$codTrans,
                        // 'responsable'=>$usuario,
                        'id_od'=>$id_od,
                        'id_cc'=>$request->id_cc,
                        'id_moneda'=>1,
                        'id_almacen'=>$request->id_almacen,
                        'descripcion_sobrantes'=>$request->descripcion_sobrantes,
                        'total_materias'=>0,
                        'total_directos'=>0,
                        'costo_primo'=>0,
                        'total_indirectos'=>0,
                        'total_sobrantes'=>0,
                        'costo_transformacion'=>0,
                        'registrado_por'=>$usuario,
                        'tipo_cambio'=>1,
                        'fecha_registro'=>date('Y-m-d H:i:s'),
                        'estado'=>1,
                        // 'observacion'=>'SALE: '.$request->sale
                    ],
                        'id_transformacion'
                );
                
                $ingresa = json_decode($request->detalle_ingresa);
                
                foreach ($ingresa as $i) {

                    $id_od_detalle = DB::table('almacen.orden_despacho_det')
                    ->insertGetId([
                        'id_od'=>$id_od,
                        'id_producto'=>$i->id_producto,
                        'id_detalle_requerimiento'=>$i->id_detalle_requerimiento,
                        'cantidad'=>$i->cantidad,
                        // 'descripcion_producto'=>$i->descripcion,
                        'part_number_transformado'=>($i->part_number_transformado ? $i->part_number_transformado : null),
                        'descripcion_transformado'=>($i->descripcion_transformado ? $i->descripcion_transformado : null),
                        'comentario_transformado'=>($i->comentario_transformado ? $i->comentario_transformado : null),
                        'cantidad_transformado'=>($i->cantidad_transformado ? $i->cantidad_transformado : null),
                        'estado'=>1,
                        'fecha_registro'=>date('Y-m-d H:i:s')
                        ],
                        'id_od_detalle'
                    );
                    
                    DB::table('almacen.transfor_materia')
                    ->insert([
                        'id_transformacion'=>$id_transformacion,
                        'id_producto'=>$i->id_producto,
                        'cantidad'=>$i->cantidad,
                        'id_od_detalle'=>$id_od_detalle,
                        'valor_unitario'=>0,
                        'valor_total'=>0,
                        'estado'=>1,
                        'fecha_registro'=>date('Y-m-d H:i:s')
                    ]);

                    $detreq = DB::table('almacen.alm_det_req')
                        ->where('id_detalle_requerimiento',$i->id_detalle_requerimiento)
                        ->first();

                    $detdes = DB::table('almacen.orden_despacho_det')
                                ->select(DB::raw('SUM(cantidad) as suma_cantidad'))
                                ->join('almacen.orden_despacho','orden_despacho.id_od','=','orden_despacho_det.id_od')
                                ->where([['orden_despacho_det.id_detalle_requerimiento','=',$i->id_detalle_requerimiento],
                                        ['orden_despacho.estado','!=',7],
                                        ['orden_despacho.aplica_cambios','=',true]])
                                ->first();

                    //orden de despacho detalle estado   procesado
                    if ($detdes->suma_cantidad >= $detreq->cantidad){
                        DB::table('almacen.alm_det_req')
                        ->where('id_detalle_requerimiento',$i->id_detalle_requerimiento)
                        ->update(['estado'=>22]);//despacho interno
                    }
                }

                $todo = DB::table('almacen.alm_det_req')
                    ->where([['id_requerimiento','=',$request->id_requerimiento],
                            ['tiene_transformacion','=',false],
                            ['estado','!=',7]])
                    ->count();

                $desp = DB::table('almacen.alm_det_req')
                    ->where([['id_requerimiento','=',$request->id_requerimiento],
                            ['estado','=',22]])//despacho interno
                    ->count();
                    
                if ($desp == $todo){
                    DB::table('almacen.alm_req')
                    ->where('id_requerimiento',$request->id_requerimiento)
                    ->update(['estado'=>22]);//despacho interno
                }

                $sale = json_decode($request->detalle_sale);
                
                foreach ($sale as $s) {
                    DB::table('almacen.transfor_transformado')
                    ->insert([
                        'id_transformacion'=>$id_transformacion,
                        'id_producto'=>$s->id_producto,
                        'cantidad'=>$s->cantidad,
                        'valor_unitario'=>0,
                        'valor_total'=>0,
                        'estado'=>1,
                        'fecha_registro'=>date('Y-m-d H:i:s')
                    ]);
                }

            }
            else {
                if ($request->tiene_transformacion == 'si'){
                    $data = json_decode($request->detalle_sale);
                    
                    foreach ($data as $d) {
                        // $descripcion = ($d->producto_descripcion !== null ? $d->producto_descripcion : $d->descripcion_adicional);
                        DB::table('almacen.orden_despacho_det')
                        ->insert([
                            'id_od'=>$id_od,
                            'id_producto'=>$d->id_producto,
                            'id_detalle_requerimiento'=>$d->id_detalle_requerimiento,
                            'cantidad'=>$d->cantidad,
                            'estado'=>1,
                            'fecha_registro'=>date('Y-m-d H:i:s')
                        ]);
    
                        if ($d->id_detalle_requerimiento !== null){

                            DB::table('almacen.alm_det_req')
                            ->where('id_detalle_requerimiento',$d->id_detalle_requerimiento)
                            ->update(['estado'=>29]);//por despachar
                        }
                    }
                } else {

                    $data = json_decode($request->detalle_requerimiento);
                    
                    foreach ($data as $d) {
                        // $descripcion = ($d->producto_descripcion !== null ? $d->producto_descripcion : $d->descripcion_adicional);
                        DB::table('almacen.orden_despacho_det')
                        ->insert([
                            'id_od'=>$id_od,
                            'id_producto'=>$d->id_producto,
                            'id_detalle_requerimiento'=>$d->id_detalle_requerimiento,
                            'cantidad'=>$d->cantidad,
                            // 'descripcion_producto'=>$descripcion,
                            'estado'=>1,
                            'fecha_registro'=>date('Y-m-d H:i:s')
                        ]);
    
                        DB::table('almacen.alm_det_req')
                        ->where('id_detalle_requerimiento',$d->id_detalle_requerimiento)
                        ->update(['estado'=>29]);//por despachar
                    }
                }
                DB::table('almacen.alm_req')
                ->where('id_requerimiento',$request->id_requerimiento)
                ->update(['estado'=>29]);//por despachar
            }

            if ($request->aplica_cambios_valor == 'no'){

                $empresa = DB::table('administracion.sis_sede')
                ->select('adm_empresa.id_empresa','adm_contri.razon_social')
                ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
                ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
                ->where('id_sede',$request->id_sede)->first();
    
            // if ($empresa !== null){
                $req = DB::table('almacen.alm_req')
                ->select('alm_req.*','oc_propias.id as id_oc_propia','oc_propias.url_oc_fisica')
                ->leftjoin('mgcp_cuadro_costos.cc','cc.id','=','alm_req.id_cc')
                ->leftjoin('mgcp_oportunidades.oportunidades','oportunidades.id','=','cc.id_oportunidad')
                ->leftjoin('mgcp_acuerdo_marco.oc_propias','oc_propias.id_oportunidad','=','oportunidades.id')
                // ->leftjoin('mgcp_acuerdo_marco.entidades','entidades.id','=','oportunidades.id_entidad')
                ->where('id_requerimiento',$request->id_requerimiento)
                ->first();
    
                $items = DB::table('almacen.alm_det_req')
                ->select('alm_det_req.cantidad','alm_det_req.precio_referencial',
                DB::raw("(item_cat.descripcion) || ' ' || (item_subcat.descripcion) || ' ' || (item.descripcion) AS item_descripcion"),
                DB::raw("(prod_cat.descripcion) || ' ' || (prod_subcat.descripcion) || ' ' || (prod.descripcion) AS prod_descripcion"),
                'item_unidad.abreviatura as item_unid','prod_unidad.abreviatura as prod_unid',
                'item.part_number as item_part_number','prod.part_number as prod_part_number',
                'sis_moneda.simbolo')
                ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
                ->leftJoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','alm_req.id_moneda')
                ->leftJoin('almacen.alm_item','alm_item.id_item','=','alm_det_req.id_item')
                ->leftJoin('almacen.alm_prod as item','item.id_producto','=','alm_item.id_producto')
                ->leftJoin('almacen.alm_und_medida as item_unidad','item_unidad.id_unidad_medida','=','item.id_unidad_medida')
                ->leftJoin('almacen.alm_cat_prod as item_cat','item_cat.id_categoria','=','item.id_categoria')
                ->leftJoin('almacen.alm_subcat as item_subcat','item_subcat.id_subcategoria','=','item.id_subcategoria')
    
                ->leftJoin('almacen.alm_prod as prod','prod.id_producto','=','alm_det_req.id_producto')
                ->leftJoin('almacen.alm_und_medida as prod_unidad','prod_unidad.id_unidad_medida','=','prod.id_unidad_medida')
                ->leftJoin('almacen.alm_cat_prod as prod_cat','prod_cat.id_categoria','=','prod.id_categoria')
                ->leftJoin('almacen.alm_subcat as prod_subcat','prod_subcat.id_subcategoria','=','prod.id_subcategoria')
                
                ->where([['alm_det_req.id_requerimiento','=',$request->id_requerimiento],
                        ['alm_det_req.estado','!=',7],
                        ['alm_det_req.tiene_transformacion','=',($request->tiene_transformacion == 'si' ? true : false)]])
                ->get();
    
                $text = '';
                $i = 1;
                foreach ($items as $item) {
                    $text .= $i.'.- '.($item->item_part_number !== null ? $item->item_part_number : $item->prod_part_number).
                    ' '.($item->item_descripcion !== null ? $item->item_descripcion : $item->prod_descripcion).
                    '   Cantidad: '.$item->cantidad.' '.($item->item_unid !== null ? $item->item_unid : $item->prod_unid).
                    '   Precio: '.($item->precio_referencial !== null ? ($item->simbolo.' '.$item->precio_referencial) : 0).'
                    ';
                    $i++;
                }
    
                $asunto_facturacion = 'Generar '.$request->documento.' para el '.$req->codigo.' '.$req->concepto;
                $asunto_almacen = 'Generar Guía de Venta para el '.$req->codigo.' '.$req->concepto;
    
                $contenido = ' para el '.$req->codigo.' '.$req->concepto.' 
        Empresa: '.$empresa->razon_social.'
                
        Datos del Cliente:
        - '.($request->documento == 'Boleta' ? 'DNI: '.$request->dni_persona : 'RUC: '.$request->cliente_ruc).'
        - '.($request->documento == 'Boleta' ? 'Nombres y Apellidos: '.$request->nombre_persona : 'Razon Social: '.$request->cliente_razon_social).'
        - Dirección: '.$request->direccion_destino.'
        - Fecha Despacho: '.$request->fecha_despacho.'
        - Hora Despacho: '.$request->hora_despacho.'
    
        Descripcion de Items:
                    '.$text.'
        '.($req->id_oc_propia !== null 
        ? ('Ver Orden Física: '.$req->url_oc_fisica.' 
        Ver Orden Electrónica: https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra='.$req->id_oc_propia.'&ImprimirCompleto=1') : '').'

        *Este correo es generado de manera automatica, por favor no responder.
        
        Saludos,
        Módulo de Logística y Almacenes
        System AGILE
        ';
            
                $contenido_facturacion = '
        Favor de generar '.$request->documento.$contenido;
    
                $contenido_almacen = '
        Favor de generar Guía de Venta'.$contenido;
    
                $destinatario_facturacion = 'programador01@okcomputer.com.pe';
                $destinatario_almacen = 'asistente.almacenilo@okcomputer.com.pe';
                $msj = '';
    
                $rspta_facturacion = CorreoController::enviar_correo( $empresa->id_empresa, $destinatario_facturacion, 
                                                                      $asunto_facturacion, $contenido_facturacion);
                $rspta_almacen = CorreoController::enviar_correo( $empresa->id_empresa, $destinatario_almacen, 
                                                                  $asunto_almacen, $contenido_almacen);
    
                if ($rspta_facturacion !== 'Mensaje Enviado.'){
                    $msj = 'No se pudo enviar el mensaje a '.$destinatario_facturacion;
                } else {
                    $msj = 'Mensaje enviado correctamente a '.$destinatario_facturacion;
                }
                if ($rspta_almacen !== 'Mensaje Enviado.'){
                    $msj .= '
    No se pudo enviar el mensaje a '.$destinatario_almacen;
                } else {
                    $msj .= '
    Mensaje enviado correctamente a '.$destinatario_almacen;
                }
            } else {
                $msj = 'Se guardó existosamente la Orden de Despacho y Hoja de Transformación';
            }

            DB::commit();
            return response()->json($msj);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function listarOrdenesDespachoPendientes(Request $request){
        $data = DB::table('almacen.orden_despacho')
        ->select('orden_despacho.*','adm_contri.nro_documento','adm_contri.razon_social',
        'alm_req.codigo as codigo_req','alm_req.concepto','ubi_dis.descripcion as ubigeo_descripcion',
        'sis_usua.nombre_corto','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
        DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
        'alm_almacen.descripcion as almacen_descripcion')
        ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','orden_despacho.id_cliente')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
        ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','orden_despacho.id_persona')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','orden_despacho.id_almacen')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->join('configuracion.ubi_dis','ubi_dis.id_dis','=','orden_despacho.ubigeo_destino')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','orden_despacho.registrado_por')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','orden_despacho.estado')
        ->where('orden_despacho.estado',1);
        // ->get();
        return datatables($data)->toJson();
    }

    public function guardar_grupo_despacho(Request $request){

        try {
            DB::beginTransaction();

            $codigo = $this->grupoODnextId($request->fecha_despacho,$request->id_sede);
            $id_usuario = Auth::user()->id_usuario;

            $id_od_grupo = DB::table('almacen.orden_despacho_grupo')
            ->insertGetId([
                'codigo'=>$codigo,
                'id_sede'=>$request->id_sede,
                'fecha_despacho'=>$request->fecha_despacho,
                'responsable'=>($request->responsable > 0 ? $request->responsable : null),
                'mov_entrega'=>$request->mov_entrega,
                'id_proveedor'=>$request->id_proveedor,
                'observaciones'=>$request->observaciones,
                'registrado_por'=>$id_usuario,
                'estado'=>1,
                'fecha_registro'=>date('Y-m-d H:i:s')
                ],
                'id_od_grupo'
            );
            $data = json_decode($request->ordenes_despacho);
            
            foreach ($data as $d) {
                DB::table('almacen.orden_despacho_grupo_det')
                ->insert([
                    'id_od_grupo'=>$id_od_grupo,
                    'id_od'=>$d->id_od,
                    'confirmacion'=>false,
                    'estado'=>1,
                    'fecha_registro'=>date('Y-m-d H:i:s')
                ]);
                //actualiza estado despachado
                DB::table('almacen.orden_despacho')
                ->where('id_od',$d->id_od)
                ->update(['estado'=>20]);//Despachado

                DB::table('almacen.orden_despacho_det')
                ->where('id_od',$d->id_od)
                ->update(['estado'=>20]);//Despachado

                DB::table('almacen.alm_req')
                ->where('id_requerimiento',$d->id_requerimiento)
                ->update(['estado'=>20]);//Despachado

                DB::table('almacen.alm_det_req')
                ->where('id_requerimiento',$d->id_requerimiento)
                ->update(['estado'=>20]);//Despachado

                //Agrega accion en requerimiento
                DB::table('almacen.alm_req_obs')
                ->insert([  'id_requerimiento'=>$request->id_requerimiento,
                            'accion'=>'DESPACHADO',
                            'descripcion'=>'Requerimiento Despachado',
                            'id_usuario'=>$id_usuario,
                            'fecha_registro'=>date('Y-m-d H:i:s')
                    ]);

            }
            DB::commit();
            return response()->json($id_od_grupo);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function despacho_transportista(Request $request){
        try {
            DB::beginTransaction();

            $requerimiento = null;

            if ($request->tr_id_proveedor !== null){
                DB::table('almacen.orden_despacho')
                ->where('id_od',$request->id_od)
                ->update([
                    'estado'=>25, 
                    // 'agencia'=>$request->agencia,
                    'id_transportista'=>$request->tr_id_proveedor,
                    'serie'=>$request->serie,
                    'numero'=>$request->numero,
                    'fecha_transportista'=>$request->fecha_transportista,
                    'codigo_envio'=>$request->codigo_envio,
                    'importe_flete'=>$request->importe_flete
                    ]);
                $requerimiento = $request->con_id_requerimiento;
            } else {
                DB::table('almacen.orden_despacho')
                ->where('id_od',$request->id_od)
                ->update(['estado'=>21]);
                $requerimiento = $request->id_requerimiento;
            }

            // $data = DB::table('almacen.orden_despacho_grupo_det')
            // ->where('id_od_grupo_detalle',$request->id_od_grupo_detalle)
            // ->update(['confirmacion'=>true,
            //         'obs_confirmacion'=>'Entregado Conforme']);

            $id_usuario = Auth::user()->id_usuario;

            $data = DB::table('almacen.orden_despacho_obs')
                ->insert([
                    'id_od'=>$request->id_od,
                    'accion'=>'TRANSPORTANDOSE',
                    'observacion'=>'Se agrego los Datos del transportista. '.$request->serie.'-'.$request->numero,
                    'registrado_por'=>$id_usuario,
                    'fecha_registro'=>date('Y-m-d H:i:s')
                    ]);
            
            if ($requerimiento !== null){
                DB::table('almacen.alm_req')
                ->where('id_requerimiento',$requerimiento)
                ->update(['estado'=>25]);

                DB::table('almacen.alm_det_req')
                ->where('id_requerimiento',$requerimiento)
                ->update(['estado'=>25]);
                //Agrega accion en requerimiento
                DB::table('almacen.alm_req_obs')
                ->insert([  'id_requerimiento'=>$requerimiento,
                            'accion'=>'TRANSPORTANDOSE',
                            'descripcion'=>'Se agrego los Datos del transportista. '.$request->serie.'-'.$request->numero,
                            'id_usuario'=>$id_usuario,
                            'fecha_registro'=>date('Y-m-d H:i:s')
                    ]);
            }
            DB::commit();
            return response()->json($data);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function despacho_conforme(Request $request){
        try {
            DB::beginTransaction();

            DB::table('almacen.orden_despacho')
            ->where('id_od',$request->id_od)
            ->update(['estado'=>21]);

            $data = DB::table('almacen.orden_despacho_grupo_det')
            ->where('id_od_grupo_detalle',$request->id_od_grupo_detalle)
            ->update([  'confirmacion'=>true,
                        'obs_confirmacion'=>'Entregado Conforme'
                        ]);

            $id_usuario = Auth::user()->id_usuario;

            DB::table('almacen.orden_despacho_obs')
            ->insert([
                    'id_od'=>$request->id_od,
                    'accion'=>'ENTREGADO',
                    'observacion'=>'Entregado Conforme',
                    'registrado_por'=>$id_usuario,
                    'fecha_registro'=>date('Y-m-d H:i:s')
                    ]);
            
            if ($request->id_requerimiento !== null){
                DB::table('almacen.alm_req')
                ->where('id_requerimiento',$request->id_requerimiento)
                ->update(['estado'=>21]);

                DB::table('almacen.alm_det_req')
                ->where('id_requerimiento',$request->id_requerimiento)
                ->update(['estado'=>21]);
                //Agrega accion en requerimiento
                DB::table('almacen.alm_req_obs')
                ->insert([  'id_requerimiento'=>$request->id_requerimiento,
                            'accion'=>'ENTREGADO',
                            'descripcion'=>'Requerimiento Entregado',
                            'id_usuario'=>$id_usuario,
                            'fecha_registro'=>date('Y-m-d H:i:s')
                    ]);
            }
            DB::commit();
            return response()->json($data);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function despacho_revertir_despacho(Request $request){
        try {
            DB::beginTransaction();

            // $data = DB::table('almacen.orden_despacho_grupo_det')
            // ->where('id_od_grupo_detalle',$request->id_od_grupo_detalle)
            // ->update(['confirmacion'=>false,
            //           'obs_confirmacion'=>$request->obs_confirmacion]);

            DB::table('almacen.orden_despacho')
            ->where('id_od',$request->id_od)
            ->update(['estado'=>23]);

            DB::table('almacen.orden_despacho_grupo_det')
            ->where('id_od_grupo_detalle',$request->id_od_grupo_detalle)
            ->update(['estado'=>7]);

            $id_usuario = Auth::user()->id_usuario;

            // DB::table('almacen.orden_despacho_obs')
            // ->insert([  'id_od'=>$request->id_od,
            //             'accion'=>'NO ENTREGADO',
            //             'observacion'=>$request->obs_confirmacion,
            //             'registrado_por'=>$id_usuario,
            //             'fecha_registro'=>date('Y-m-d H:i:s')
            //         ]);
            //Agrega accion en requerimiento
            DB::table('almacen.alm_req_obs')
            ->insert([  'id_requerimiento'=>$request->id_requerimiento,
                        'accion'=>'REVERTIR',
                        'descripcion'=>'Se revertió el Requerimiento a Por Despachar. Regresa a estado Despacho Externo.',
                        'id_usuario'=>$id_usuario,
                        'fecha_registro'=>date('Y-m-d H:i:s')
                ]);

            DB::table('almacen.alm_det_req')
                ->where('id_requerimiento',$request->id_requerimiento)
                ->update(['estado'=>23]);

            DB::table('almacen.alm_req')
                ->where('id_requerimiento',$request->id_requerimiento)
                ->update(['estado'=>23]);
/*            
            $od_detalle = DB::table('almacen.orden_despacho_det')
            ->where('id_od',$request->id_od)
            ->get();

            foreach ($od_detalle as $det){
                $detreq = DB::table('almacen.alm_det_req')
                            ->where('id_detalle_requerimiento',$det->id_detalle_requerimiento)
                            ->first();

                $detdes = DB::table('almacen.orden_despacho_det')
                            ->select(DB::raw('SUM(cantidad) as suma_cantidad'))
                            ->join('almacen.orden_despacho','orden_despacho.id_od','=','orden_despacho_det.id_od')
                            ->where([['orden_despacho_det.id_detalle_requerimiento','=',$det->id_detalle_requerimiento],
                                    ['orden_despacho.estado','!=',7],
                                    ['orden_despacho.aplica_cambios','=',false]])
                            ->first();

                //orden de despacho detalle estado   procesado
                if ($detdes->suma_cantidad >= $detreq->cantidad){
                    DB::table('almacen.alm_det_req')
                    ->where('id_detalle_requerimiento',$det->id_detalle_requerimiento)
                    ->update(['estado'=>23]);
                }
            }
            //requerimiento despachado
            $todo = DB::table('almacen.alm_det_req')
            ->where([['id_requerimiento','=',$request->id_requerimiento],
                    ['estado','!=',7]])
            ->count();

            $desp = DB::table('almacen.alm_det_req')
            ->where([['id_requerimiento','=',$request->id_requerimiento],
                    ['estado','=',23]])
            ->count();
            
            if ($desp == $todo){
                DB::table('almacen.alm_req')
                ->where('id_requerimiento',$request->id_requerimiento)
                ->update(['estado'=>23]);
            }
  */          
            DB::commit();
            return response()->json(1);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function despacho_no_conforme(Request $request){
        try {
            DB::beginTransaction();

            DB::table('almacen.orden_despacho')
            ->where('id_od',$request->id_od)
            ->update(['estado'=>20]);

            $id_usuario = Auth::user()->id_usuario;
            //Agrega accion en requerimiento
            $data = DB::table('almacen.alm_req_obs')
            ->insert([  'id_requerimiento'=>$request->id_requerimiento,
                        'accion'=>'REVERTIR',
                        'descripcion'=>'Se revertió el Requerimiento a Pendientes de Transporte. Regresa a estado Despachado.',
                        'id_usuario'=>$id_usuario,
                        'fecha_registro'=>date('Y-m-d H:i:s')
                ]);

            DB::table('almacen.alm_det_req')
                ->where('id_requerimiento',$request->id_requerimiento)
                ->update(['estado'=>20]);

            DB::table('almacen.alm_req')
                ->where('id_requerimiento',$request->id_requerimiento)
                ->update(['estado'=>20]);

            DB::commit();
            return response()->json($data);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function guardar_guia_despacho(Request $request){
        try {
            DB::beginTransaction();
            $id_salida = null;

            if ($request->id_od !== null){

                $id_tp_doc_almacen = 2;//Guia Venta
                $id_usuario = Auth::user()->id_usuario;
                $fecha_registro = date('Y-m-d H:i:s');
    
                $od = DB::table('almacen.orden_despacho')
                ->where('id_od',$request->id_od)
                ->first();

                if ($od !== null){
                    $id_guia_ven = DB::table('almacen.guia_ven')->insertGetId(
                        [
                            'id_tp_doc_almacen' => $id_tp_doc_almacen,
                            'id_od' => $request->id_od,
                            'serie' => $request->serie,
                            'numero' => $request->numero,
                            'id_sede' => $request->id_sede,
                            'id_cliente' => $request->id_cliente,
                            'id_persona' => $request->id_persona,
                            'fecha_emision' => $request->fecha_emision,
                            'fecha_almacen' => $request->fecha_emision,
                            'id_almacen' => $request->id_almacen,
                            'id_operacion' => $request->id_operacion,
                            'usuario' => $id_usuario,
                            'registrado_por' => $id_usuario,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro,
                        ],
                            'id_guia_ven'
                        );

                    //Genero la salida
                    $codigo = AlmacenController::nextMovimiento(2,//salida
                    $request->fecha_emision,
                    $request->id_almacen);
                    
                    $transformacion = DB::table('almacen.transformacion')
                    ->select('id_transformacion')
                    ->where('id_od',$request->id_od)
                    ->first();

                    if ($transformacion !== null){
                        DB::table('almacen.transformacion')
                        ->where('id_transformacion',$transformacion->id_transformacion)
                        ->update([  'estado'=>21,
                                    'fecha_entrega'=>date('Y-m-d H:i:s')
                                ]);//Entregado
                    }

                    $id_salida = DB::table('almacen.mov_alm')->insertGetId(
                        [
                            'id_almacen' => $request->id_almacen,
                            'id_tp_mov' => 2,//Salidas
                            'codigo' => $codigo,
                            'fecha_emision' => $request->fecha_emision,
                            'id_guia_ven' => $id_guia_ven,
                            'id_operacion' => $request->id_operacion,
                            'id_transformacion' => ($transformacion!==null ? $transformacion->id_transformacion : null),
                            'revisado' => 0,
                            'usuario' => $id_usuario,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro,
                        ],
                            'id_mov_alm'
                        );

                    $detalle = DB::table('almacen.orden_despacho_det')
                    ->select('orden_despacho_det.*','alm_prod.id_unidad_medida')
                    ->join('almacen.alm_prod','alm_prod.id_producto','=','orden_despacho_det.id_producto')
                    ->where([['orden_despacho_det.id_od','=',$request->id_od],
                            ['orden_despacho_det.estado','!=',7]])
                    ->get();
                    //orden de despacho estado   procesado
                    $est = ($request->id_operacion == 27 ? 22 : 23);
                    $aplica_cambios = ($request->id_operacion == 27 ? true : false);
                    $count_est = 0;

                    foreach ($detalle as $det) {
                        //guardo los items de la guia ven
                        $id_guia_ven_det = DB::table('almacen.guia_ven_det')->insertGetId([
                            'id_guia_ven' => $id_guia_ven,
                            'id_producto' => $det->id_producto,
                            // 'id_posicion' => $request->id_posicion,
                            'cantidad' => $det->cantidad,
                            'id_unid_med' => $det->id_unidad_medida,
                            'estado' => 1,
                            'fecha_registro' => $fecha_registro
                        ],
                            'id_guia_ven_det'
                        );
                        //obtener costo promedio
                        $saldos_ubi = DB::table('almacen.alm_prod_ubi')
                        ->where([['id_producto','=',$det->id_producto],
                                ['id_almacen','=',$request->id_almacen]])
                        ->first();
                        //Guardo los items de la salida
                        $id_det = DB::table('almacen.mov_alm_det')->insertGetId(
                            [
                                'id_mov_alm' => $id_salida,
                                'id_producto' => $det->id_producto,
                                // 'id_posicion' => $det->id_posicion,
                                'cantidad' => $det->cantidad,
                                'valorizacion' => ($saldos_ubi !== null ? ($saldos_ubi->costo_promedio * $det->cantidad) : 0),
                                'usuario' => $id_usuario,
                                'id_guia_ven_det' => $id_guia_ven_det,
                                'estado' => 1,
                                'fecha_registro' => $fecha_registro,
                            ],
                                'id_mov_alm_det'
                            );
                        
                        //Actualizo los saldos del producto
                        //Obtengo el registro de saldos
                        $ubi = DB::table('almacen.alm_prod_ubi')
                        ->where([['id_producto','=',$det->id_producto],
                                ['id_almacen','=',$request->id_almacen]])
                        ->first();
                        //Traer stockActual
                        $saldo = AlmacenController::saldo_actual_almacen($det->id_producto, $request->id_almacen);
                        $valor = AlmacenController::valorizacion_almacen($det->id_producto, $request->id_almacen);
                        $cprom = ($saldo > 0 ? $valor/$saldo : 0);
                        //guardo saldos actualizados
                        if ($ubi !== null){//si no existe -> creo la ubicacion
                            DB::table('almacen.alm_prod_ubi')
                            ->where('id_prod_ubi',$ubi->id_prod_ubi)
                            ->update([  'stock' => $saldo,
                                        'valorizacion' => $valor,
                                        'costo_promedio' => $cprom
                                ]);
                        } else {
                            DB::table('almacen.alm_prod_ubi')->insert([
                                'id_producto' => $det->id_producto,
                                'id_almacen' => $request->id_almacen,
                                'stock' => $saldo,
                                'valorizacion' => $valor,
                                'costo_promedio' => $cprom,
                                'estado' => 1,
                                'fecha_registro' => $fecha_registro
                                ]);
                        }

                        $detreq = DB::table('almacen.alm_det_req')
                                    ->where('id_detalle_requerimiento',$det->id_detalle_requerimiento)
                                    ->first();

                        $detdes = DB::table('almacen.orden_despacho_det')
                                    ->select(DB::raw('SUM(cantidad) as suma_cantidad'))
                                    ->join('almacen.orden_despacho','orden_despacho.id_od','=','orden_despacho_det.id_od')
                                    ->where([['orden_despacho_det.id_detalle_requerimiento','=',$det->id_detalle_requerimiento],
                                            ['orden_despacho.estado','!=',7],
                                            ['orden_despacho.aplica_cambios','=',$aplica_cambios]])
                                    ->first();

                        //orden de despacho detalle estado   procesado
                        if ($detdes->suma_cantidad >= $detreq->cantidad){
                            DB::table('almacen.alm_det_req')
                            ->where('id_detalle_requerimiento',$det->id_detalle_requerimiento)
                            ->update(['estado'=>$est]);
                        }
                    }
                    
                    DB::table('almacen.orden_despacho')
                    ->where('id_od',$request->id_od)
                    ->update(['estado'=>$est]);
                    //orden de despacho detalle estado   procesado
                    DB::table('almacen.orden_despacho_det')
                    ->where('id_od',$request->id_od)
                    ->update(['estado'=>$est]);

                    $requerimiento = DB::table('almacen.alm_req')
                        ->where('id_requerimiento',$request->id_requerimiento)
                        ->first();
                    //requerimiento despachado
                    if ($requerimiento->tiene_transformacion){

                        if ($aplica_cambios){

                            $todo = DB::table('almacen.alm_det_req')
                            ->where([['id_requerimiento','=',$request->id_requerimiento],
                                    ['tiene_transformacion','=',false],
                                    ['estado','!=',7]])
                            ->count();       
                        } 
                        else {
                            $todo = DB::table('almacen.alm_det_req')
                            ->where([['id_requerimiento','=',$request->id_requerimiento],
                                    ['tiene_transformacion','=',true],
                                    ['estado','!=',7]])
                            ->count();
                        }
                    }
                    else {
                        $todo = DB::table('almacen.alm_det_req')
                        ->where([['id_requerimiento','=',$request->id_requerimiento],
                                ['tiene_transformacion','=',false],
                                ['estado','!=',7]])
                        ->count(); 
                    }
                    $desp = DB::table('almacen.alm_det_req')
                    ->where([['id_requerimiento','=',$request->id_requerimiento],
                            ['estado','=',$est]])
                    ->count();
                    
                    if ($desp == $todo){
                        DB::table('almacen.alm_req')
                        ->where('id_requerimiento',$request->id_requerimiento)
                        ->update(['estado'=>$est]);
                    }
                    //Agrega accion en requerimiento
                    DB::table('almacen.alm_req_obs')
                    ->insert([  'id_requerimiento'=>$request->id_requerimiento,
                                'accion'=>'SALIDA DE ALMACÉN',
                                'descripcion'=>'Se generó la Salida del Almacén con Guía '.$request->serie.'-'.$request->numero,
                                'id_usuario'=>$id_usuario,
                                'fecha_registro'=>date('Y-m-d H:i:s')
                        ]);
                    
                }
            }

            DB::commit();
            return response()->json($id_salida);
            
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            // return response()->json($e);
        }
    }

    public function listarSalidasDespacho(Request $request){
        $data = DB::table('almacen.mov_alm')
        ->select('mov_alm.*','guia_ven.serie','guia_ven.numero','guia_ven.id_od','orden_despacho.codigo as codigo_od',
            DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
            'alm_req.codigo as codigo_requerimiento','adm_contri.razon_social','alm_req.concepto',
            'alm_almacen.descripcion as almacen_descripcion','sis_usua.nombre_corto')
            ->join('almacen.guia_ven','guia_ven.id_guia_ven','=','mov_alm.id_guia_ven')
            ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','guia_ven.id_cliente')
            ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','guia_ven.id_persona')
            ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','guia_ven.id_almacen')
            ->leftjoin('configuracion.sis_usua','sis_usua.id_usuario','=','guia_ven.usuario')
            ->join('almacen.orden_despacho','orden_despacho.id_od','=','guia_ven.id_od')
            ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
            ->where([['mov_alm.estado','!=','7']]);
            // ->get();
        // return response()->json($data);
        return datatables($data)->toJson();
    }

    public function imprimir_despacho($id_od_grupo){
        
        $id = $this->decode5t($id_od_grupo);

        $despacho_grupo = DB::table('almacen.orden_despacho_grupo')
        ->select('orden_despacho_grupo.*','sis_sede.descripcion as sede_descripcion',
        'sis_usua.nombre_corto as trabajador_despacho','adm_contri.nro_documento as ruc_empresa',
        'proveedor.razon_social as proveedor_despacho','adm_contri.razon_social as empresa_razon_social',
        'registrado.nombre_corto')
        ->leftjoin('configuracion.sis_usua','sis_usua.id_usuario','=','orden_despacho_grupo.responsable')
        ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','orden_despacho_grupo.id_proveedor')
        ->leftjoin('contabilidad.adm_contri as proveedor','proveedor.id_contribuyente','=','log_prove.id_contribuyente')
        ->join('administracion.sis_sede','sis_sede.id_sede','=','orden_despacho_grupo.id_sede')
        ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
        ->join('configuracion.sis_usua as registrado','registrado.id_usuario','=','orden_despacho_grupo.registrado_por')
        ->where('orden_despacho_grupo.id_od_grupo',$id)
        ->first();

        $ordenes_despacho = DB::table('almacen.orden_despacho_grupo_det')
        ->select('orden_despacho.*','adm_contri.nro_documento','adm_contri.razon_social',
        DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
        'ubi_dis.descripcion as ubigeo_descripcion','alm_almacen.descripcion as almacen_descripcion',
        'guia_ven.serie','guia_ven.numero','alm_req.codigo as codigo_req','alm_req.concepto',
        'rrhh_perso.nro_documento as dni')
        ->join('almacen.orden_despacho','orden_despacho.id_od','=','orden_despacho_grupo_det.id_od')
        ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','orden_despacho.id_cliente')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
        ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','orden_despacho.id_persona')
        ->leftjoin('configuracion.ubi_dis','ubi_dis.id_dis','=','orden_despacho.ubigeo_destino')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','orden_despacho.id_almacen')
        ->leftJoin('almacen.guia_ven', function($join)
                         {   $join->on('guia_ven.id_od', '=', 'orden_despacho.id_od');
                             $join->where('guia_ven.estado','!=', 7);
                         })
        ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
        ->where([['orden_despacho_grupo_det.id_od_grupo','=',$id],['orden_despacho_grupo_det.estado','!=',7]])
        ->get();
        
        $fecha_actual = date('Y-m-d');
        $hora_actual = date('H:i:s');

        $html = '
        <html>
            <head>
                <style type="text/css">
                *{ 
                    font-family: "DejaVu Sans";
                }
                table{
                    width:100%;
                    font-size:12px;
                }
                #detalle thead{
                    padding: 4px;
                    background-color: #e5e5e5;
                }
                #detalle tbody tr td{
                    font-size:11px;
                    padding: 4px;
                }
                .right{
                    text-align: right;
                }
                .sup{
                    vertical-align:top;
                }
                </style>
            </head>
            <body>
                <table width="100%">
                    <tr>
                        <td>
                            <p style="text-align:left;font-size:10px;margin:0px;">'.$despacho_grupo->ruc_empresa.'</p>
                            <p style="text-align:left;font-size:10px;margin:0px;">'.$despacho_grupo->empresa_razon_social.'</p>
                            <p style="text-align:left;font-size:10px;margin:0px;">.::Sistema ERP v1.0::.</p>
                        </td>
                        <td>
                            <p style="text-align:right;font-size:10px;margin:0px;">Fecha: '.$fecha_actual.'</p>
                            <p style="text-align:right;font-size:10px;margin:0px;">Hora: '.$hora_actual.'</p>
                            <p style="text-align:right;font-size:10px;margin:0px;">Despacho: '.$despacho_grupo->fecha_despacho.'</p>
                        </td>
                    </tr>
                </table>
                <h3 style="margin:0px;"><center>DESPACHO</center></h3>
                <h5><center>'.($despacho_grupo->trabajador_despacho !== null ? $despacho_grupo->trabajador_despacho : ($despacho_grupo->proveedor_despacho !== null ? $despacho_grupo->proveedor_despacho : $despacho_grupo->mov_entrega)).'</center></h5>
                <p>'.strtoupper($despacho_grupo->observaciones).'</p>
                ';

                foreach ($ordenes_despacho as $od) {
                    # code...
                    $html.='<br/><table border="0">
                    <tbody>
                    <tr>
                        <td>OD N°</td>
                        <td width=10px>:</td>
                        <td class="verticalTop">'.$od->codigo.'</td>
                        <td width=100px>Cliente</td>
                        <td width=10px>:</td>
                        <td>'.($od->razon_social !== null ? ($od->nro_documento.' - '.$od->razon_social) : (($od->dni!==null ? $od->dni.' - ' : '').$od->nombre_persona)).'</td>
                    </tr>
                    <tr>
                        <td width=100px>Requerimiento</td>
                        <td width=10px>:</td>
                        <td class="verticalTop">'.$od->codigo_req.'</td>
                        <td>Concepto</td>
                        <td width=10px>:</td>
                        <td>'.($od->concepto !== null ? ($od->concepto) : '').'</td>
                    </tr>
                    <tr>
                        <td>Distrito</td>
                        <td width=10px>:</td>
                        <td width=170px class="verticalTop">'.$od->ubigeo_descripcion.'</td>
                        <td>Dirección</td>
                        <td width=10px>:</td>
                        <td>'.$od->direccion_destino.'</td>
                    </tr>
                    <tr>
                        <td>Teléfono</td>
                        <td width=10px>:</td>
                        <td width=170px class="verticalTop">'.($od->telefono!==null ? $od->telefono : '').'</td>
                        <td></td>
                        <td width=10px></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Almacén</td>
                        <td width=10px>:</td>
                        <td class="verticalTop">'.$od->almacen_descripcion.'</td>
                        <td>Guia Remisión</td>
                        <td width=10px>:</td>
                        <td>'.$od->serie.' - '.$od->numero.'</td>
                    </tr>
                    </tbody>
                    </table>
                    <br/>';

                    $detalle = DB::table('almacen.orden_despacho_det')
                    ->select('orden_despacho_det.*','alm_prod.codigo','alm_prod.descripcion',
                    'alm_und_medida.abreviatura')
                    ->join('almacen.alm_prod','alm_prod.id_producto','=','orden_despacho_det.id_producto')
                    ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
                    ->where([['orden_despacho_det.id_od','=',$od->id_od],['orden_despacho_det.estado','!=','7']])
                    ->get();

                    $i = 1;
                    $html.='<table border="1" cellspacing=0 cellpadding=2>
                    <tbody>
                    <tr style="background-color: lightblue;font-size:11px;">
                        <th>#</th>
                        <th with=50px>Codigo</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Und</th>
                    </tr>';
                    // background-color:lightgrey; 
                    foreach($detalle as $det){
                        $html.='
                        <tr style="font-size:11px;">
                            <td class="right">'.$i.'</td>
                            <td with=50px>'.$det->codigo.'</td>
                            <td>'.$det->descripcion.'</td>
                            <td class="right">'.$det->cantidad.'</td>
                            <td>'.$det->abreviatura.'</td>
                        </tr>';
                        $i++;
                    }
                    $html.='</tbody>
                    </table>';
                }
                
            $html.='<p style="text-align:right;font-size:11px;">Elaborado por: '.$despacho_grupo->nombre_corto.' '.$despacho_grupo->fecha_registro.'</p>
            </body>
        </html>';

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        return $pdf->stream();
        return $pdf->download('despacho.pdf');

    }

    public function anular_requerimiento(Request $request){
        try{
            DB::beginTransaction();
        
            $data = DB::table('almacen.alm_req')
            ->where('id_requerimiento',$request->obs_id_requerimiento)
            ->update(['estado'=>7]);
    
            $data = DB::table('almacen.alm_det_req')
            ->where('id_requerimiento',$request->obs_id_requerimiento)
            ->update(['estado'=>7]);
    
            $id_usuario = Auth::user()->id_usuario;

            $data = DB::table('almacen.alm_req_obs')
            ->insert(['id_requerimiento'=>$request->obs_id_requerimiento,
                      'accion'=>'ANULADO',
                      'descripcion'=>$request->obs_motivo,
                      'id_usuario'=>$id_usuario,
                      'fecha_registro'=>date('Y-m-d H:i:s')]);

            DB::commit();
            return response()->json($data);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }

    }

    public function pago_confirmado(Request $request){
        try {
            DB::beginTransaction();

            $data = DB::table('almacen.alm_req')
            ->where('id_requerimiento',$request->obs_id_requerimiento)
            ->update(['confirmacion_pago'=>true,
                      'obs_confirmacion'=>$request->obs_motivo
                      ]);

            $id_usuario = Auth::user()->id_usuario;

            DB::table('almacen.alm_req_obs')
            ->insert(['id_requerimiento'=>$request->obs_id_requerimiento,
                      'accion'=>'PAGO CONFIRMADO',
                      'descripcion'=>$request->obs_motivo,
                      'id_usuario'=>$id_usuario,
                      'fecha_registro'=>date('Y-m-d H:i:s')
                      ]);

            DB::commit();
            return response()->json($data);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function pago_no_confirmado(Request $request){
        try {
            DB::beginTransaction();

            $data = DB::table('almacen.alm_req')
            ->where('id_requerimiento',$request->obs_id_requerimiento)
            ->update(['confirmacion_pago'=>false,
                      'estado'=>7,
                      'obs_confirmacion'=>$request->obs_motivo]);

            DB::table('almacen.alm_det_req')
            ->where('id_requerimiento',$request->obs_id_requerimiento)
            ->update(['estado'=>7]);

            $id_usuario = Auth::user()->id_usuario;
            
            $id = DB::table('almacen.alm_req_obs')
            ->insertGetId(['id_requerimiento'=>$request->obs_id_requerimiento,
                      'accion'=>'PAGO NO CONFIRMADO',
                      'descripcion'=>$request->obs_motivo,
                      'id_usuario'=>$id_usuario,
                      'fecha_registro'=>date('Y-m-d H:i:s')
                    ],
                      'id_observacion'
                );
      
            DB::commit();
            return response()->json($id);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }


    public function ODnextId($fecha_despacho,$id_sede){
        $yyyy = date('Y',strtotime($fecha_despacho));
        
        $cantidad = DB::table('almacen.orden_despacho')
        ->whereYear('fecha_despacho','=',$yyyy)
        ->where([['id_sede','=',$id_sede],['estado','!=',7]])
        ->get()->count();

        $val = AlmacenController::leftZero(3,($cantidad + 1));
        $nextId = "OD-".$yyyy."-".$val;
        return $nextId;
    }

    public function grupoODnextId($fecha_despacho,$id_sede){
        $yyyy = date('Y',strtotime($fecha_despacho));
        
        $cantidad = DB::table('almacen.orden_despacho_grupo')
        ->whereYear('fecha_despacho','=',$yyyy)
        ->where([['id_sede','=',$id_sede],['estado','!=',7]])
        ->get()->count();

        $val = AlmacenController::leftZero(3,($cantidad + 1));
        $nextId = "D-".$yyyy."-".$val;
        return $nextId;
    }

    public function transformacion_nextId($fecha_transformacion){
        $yyyy = date('Y',strtotime($fecha_transformacion));
        
        $cantidad = DB::table('almacen.transformacion')
        ->whereYear('fecha_transformacion','=',$yyyy)
        ->where([['estado','!=',7]])
        ->get()->count();

        $val = AlmacenController::leftZero(3,($cantidad + 1));
        $nextId = "TF-".$yyyy."-".$val;
        return $nextId;
    }
       
    public function decode5t($str){
        for($i=0; $i<5;$i++){
            $str=base64_decode(strrev($str));
        }
        return $str;
    }
    
    public function cambio_serie_numero(Request $request){
    
        try {
            DB::beginTransaction();
    
            $id_usuario = Auth::user()->id_usuario;
            $msj = '';
    
            $sal = DB::table('almacen.mov_alm')
            ->where('id_mov_alm', $request->id_salida)
            ->first();
            //si la salida no esta revisada
            if ($sal->revisado == 0){
                //si existe una orden
                if ($request->id_od !== null) {
                    //Verifica si ya fue despachado
                    $od = DB::table('almacen.orden_despacho')
                    ->select('orden_despacho.*','adm_estado_doc.estado_doc')
                    ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','orden_despacho.estado')
                    ->where('id_od',$request->id_od)
                    ->first();
                    //si la orden de despacho es despacho externo
                    if ($od->estado == 23){
                        //Anula la Guia
                        $update = DB::table('almacen.guia_ven')
                        ->where('id_guia_ven', $request->id_guia_ven)
                        ->update([  'serie' => $request->serie_nuevo,
                                    'numero'=> $request->numero_nuevo ]);
                        //Agrega motivo anulacion a la guia
                        DB::table('almacen.guia_ven_obs')->insert(
                            [
                                'id_guia_ven'=>$request->id_guia_ven,
                                'observacion'=>'Se cambió la serie-número de la Guía Venta a '.$request->serie_nuevo.'-'.$request->numero_nuevo,
                                'registrado_por'=>$id_usuario,
                                'id_motivo_anu'=>$request->id_motivo_obs_cambio,
                                'fecha_registro'=>date('Y-m-d H:i:s')
                            ]);

                        if ($od->id_requerimiento !== null){
                            //Agrega accion en requerimiento
                            DB::table('almacen.alm_req_obs')
                            ->insert([  'id_requerimiento'=>$od->id_requerimiento,
                                        'accion'=>'CAMBIO DE SERIE-NUMERO',
                                        'descripcion'=>'Se cambió la serie-número de la Guía Venta a '.$request->serie_nuevo.'-'.$request->numero_nuevo,
                                        'id_usuario'=>$id_usuario,
                                        'fecha_registro'=>date('Y-m-d H:i:s')
                                ]);
                        }
                    } else {
                        $msj = 'La Orden de Despacho ya está con '.$od->estado_doc;
                    }
                } else {
                    $msj = 'No existe una orden de despacho enlazada';
                }
            } else {
                $msj = 'La salida ya fue revisada por el Jefe de Almacén';
            }
            DB::commit();
            return response()->json($msj);
            
        } catch (\PDOException $e) {
    
            DB::rollBack();
        }
    }

    public function anular_salida(Request $request){
    
        try {
            DB::beginTransaction();
    
            $id_usuario = Auth::user()->id_usuario;
            $msj = '';
    
            $sal = DB::table('almacen.mov_alm')
            ->where('id_mov_alm', $request->id_salida)
            ->first();
            //si la salida no esta revisada
            if ($sal->revisado == 0){
                //si existe una orden
                if ($request->id_od !== null) {
                    //Verifica si ya fue despachado
                    $od = DB::table('almacen.orden_despacho')
                    ->select('orden_despacho.*','adm_estado_doc.estado_doc')
                    ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','orden_despacho.estado')
                    ->where('id_od',$request->id_od)
                    ->first();
                    //si la orden de despacho es Procesado
                    if ($od->estado == 9 || $od->estado == 23){
                        //Anula salida
                        $update = DB::table('almacen.mov_alm')
                        ->where('id_mov_alm', $request->id_salida)
                        ->update([ 'estado' => 7 ]);
                        //Anula el detalle
                        $update = DB::table('almacen.mov_alm_det')
                        ->where('id_mov_alm', $request->id_salida)
                        ->update([ 'estado' => 7 ]);
                        //Agrega motivo anulacion a la guia
                        DB::table('almacen.guia_ven_obs')->insert(
                        [
                            'id_guia_ven'=>$request->id_guia_ven,
                            'observacion'=>$request->observacion_guia_ven,
                            'registrado_por'=>$id_usuario,
                            'id_motivo_anu'=>$request->id_motivo_obs_ven,
                            'fecha_registro'=>date('Y-m-d H:i:s')
                        ]);
                        //Anula la Guia
                        $update = DB::table('almacen.guia_ven')
                        ->where('id_guia_ven', $request->id_guia_ven)
                        ->update([ 'estado' => 7 ]);
                        //Anula la Guia Detalle
                        $update = DB::table('almacen.guia_ven_det')
                        ->where('id_guia_ven', $request->id_guia_ven)
                        ->update([ 'estado' => 7 ]);
                        //Quita estado de la orden
                        DB::table('almacen.orden_despacho')
                        ->where('id_od',$request->id_od)
                        ->update(['estado' => 1]);

                        if ($od->id_requerimiento !== null){
                            //Requerimiento regresa a por despachar
                            DB::table('almacen.alm_req')
                            ->where('id_requerimiento',$od->id_requerimiento)
                            ->update(['estado'=>29]);//por despachar
    
                            DB::table('almacen.alm_det_req')
                            ->where('id_requerimiento',$od->id_requerimiento)
                            ->update(['estado'=>29]);//por despachar
                            //Agrega accion en requerimiento
                            DB::table('almacen.alm_req_obs')
                            ->insert([  'id_requerimiento'=>$od->id_requerimiento,
                                        'accion'=>'SALIDA ANULADA',
                                        'descripcion'=>'Requerimiento regresa a Reservado',
                                        'id_usuario'=>$id_usuario,
                                        'fecha_registro'=>date('Y-m-d H:i:s')
                                ]);
                        }
                    } else {
                        $msj = 'La Orden de Despacho ya está con '.$od->estado_doc;
                    }
                } else {
                    $msj = 'No existe una orden de despacho enlazada';
                }
            } else {
                $msj = 'La salida ya fue revisada por el Jefe de Almacén';
            }
            DB::commit();
            return response()->json($msj);
            
        } catch (\PDOException $e) {
    
            DB::rollBack();
        }
    }
    
    function anular_orden_despacho($id_od){
        try {
            DB::beginTransaction();

            $update = DB::table('almacen.orden_despacho')
            ->where('id_od',$id_od)
            ->update(['estado'=>7]);

            $detalle = DB::table('almacen.orden_despacho_det')
            ->where('id_od',$id_od)->get();

            foreach($detalle as $det){
                
                $update = DB::table('almacen.orden_despacho_det')
                            ->where('id_od_detalle',$det->id_od_detalle)
                            ->update(['estado'=>7]);

                $detreq = DB::table('almacen.alm_det_req')
                            ->where('id_detalle_requerimiento',$det->id_detalle_requerimiento)
                            ->update(['estado'=>19]);
            }

            $od = DB::table('almacen.orden_despacho')
                ->select('orden_despacho.*','alm_req.id_tipo_requerimiento')
                ->join('almacen.alm_req','alm_req.id_requerimiento','=','orden_despacho.id_requerimiento')
                ->where('id_od',$id_od)
                ->first();

            $count_ods = DB::table('almacen.orden_despacho')
            ->where([['id_requerimiento','=',$od->id_requerimiento],
                        ['aplica_cambios','=',true],
                        ['estado','!=',7]])
            ->count();

            if ($od->aplica_cambios){
                DB::table('almacen.transformacion')
                ->where('id_od',$id_od)
                ->update(['estado'=>7]);
                
                if ($count_ods > 0){
                    DB::table('almacen.alm_req')
                        ->where('id_requerimiento',$od->id_requerimiento)
                        ->update(['estado'=>22]);//despacho interno
                } else {
                    DB::table('almacen.alm_req')
                        ->where('id_requerimiento',$od->id_requerimiento)
                        ->update(['estado'=>28]);//en almacen total
                }
            } else {
                if ($count_ods > 0){
                    DB::table('almacen.alm_req')
                        ->where('id_requerimiento',$od->id_requerimiento)
                        ->update(['estado'=>10]);//transformado
                } else {
                    if ($od->id_tipo_requerimiento !== 1){
                        DB::table('almacen.alm_req')
                            ->where('id_requerimiento',$od->id_requerimiento)
                            ->update(['estado'=>19]);//en almacen total
                    } else {
                        DB::table('almacen.alm_req')
                            ->where('id_requerimiento',$od->id_requerimiento)
                            ->update(['estado'=>28]);//en almacen total
                    }
                }
            }


            $id_usuario = Auth::user()->id_usuario;
            //Agrega accion en requerimiento
            $obs = DB::table('almacen.alm_req_obs')
            ->insertGetId([ 'id_requerimiento'=>$od->id_requerimiento,
                            'accion'=>'O.D. ANULADA',
                            'descripcion'=>'Orden de Despacho Anulado',
                            'id_usuario'=>$id_usuario,
                            'fecha_registro'=>date('Y-m-d H:i:s')
                ],
                    'id_observacion'
                );

            DB::commit();
            return response()->json($obs);
            
        } catch (\PDOException $e) {
    
            DB::rollBack();
        }
    }

    public function listarRequerimientosTrazabilidad(){
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*','sis_usua.nombre_corto as responsable','adm_grupo.descripcion as grupo',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            'ubi_dis.descripcion as ubigeo_descripcion',
            'rrhh_perso.nro_documento as dni_persona','alm_almacen.descripcion as almacen_descripcion',
            'sede_req.descripcion as sede_descripcion_req',
            'orden_despacho.id_od','orden_despacho.codigo as codigo_od','orden_despacho.estado as estado_od',
            DB::raw("(transportista.razon_social) || ' ' || (orden_despacho.serie) || '-' || (orden_despacho.numero) || ' Cod.' || (orden_despacho.codigo_envio) AS guia_transportista"),
            'orden_despacho.importe_flete',
            'alm_tp_req.descripcion as tipo_req','orden_despacho_grupo.id_od_grupo',
            DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
            'adm_contri.nro_documento as cliente_ruc','adm_contri.razon_social as cliente_razon_social',
            'oc_propias.orden_am','oc_propias.monto_total','entidades.nombre',
            'oc_propias.id as id_oc_propia','oc_propias.url_oc_fisica','users.name')
            ->join('almacen.alm_tp_req','alm_tp_req.id_tipo_requerimiento','=','alm_req.id_tipo_requerimiento')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->leftjoin('administracion.adm_grupo','adm_grupo.id_grupo','=','alm_req.id_grupo')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
            ->leftJoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_req.id_almacen')
            ->leftJoin('administracion.sis_sede as sede_req','sede_req.id_sede','=','alm_almacen.id_sede')
            ->leftJoin('configuracion.ubi_dis','ubi_dis.id_dis','=','alm_req.id_ubigeo_entrega')
            ->leftJoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','alm_req.id_persona')
            ->leftJoin('comercial.com_cliente','com_cliente.id_cliente','=','alm_req.id_cliente')
            ->leftJoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->leftJoin('almacen.orden_despacho', function($join)
                         {   $join->on('orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento');
                             $join->where('orden_despacho.estado','!=', 7);
                         })
            ->leftJoin('almacen.orden_despacho_grupo_det', function($join)
                        {   $join->on('orden_despacho_grupo_det.id_od', '=', 'orden_despacho.id_od');
                            $join->where('orden_despacho_grupo_det.estado','!=', 7);
                        })
            ->leftJoin('almacen.orden_despacho_grupo', function($join)
                        {   $join->on('orden_despacho_grupo.id_od_grupo', '=', 'orden_despacho_grupo_det.id_od_grupo');
                            $join->where('orden_despacho_grupo.estado','!=', 7);
                        })
            ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','orden_despacho.id_transportista')
            ->leftjoin('contabilidad.adm_contri as transportista','transportista.id_contribuyente','=','log_prove.id_contribuyente')
            ->leftjoin('mgcp_cuadro_costos.cc','cc.id','=','alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades','oportunidades.id','=','cc.id_oportunidad')
            ->leftjoin('mgcp_acuerdo_marco.oc_propias','oc_propias.id_oportunidad','=','oportunidades.id')
            ->leftjoin('mgcp_usuarios.users','users.id','=','oc_propias.id_corporativo')
            ->leftjoin('mgcp_acuerdo_marco.entidades','entidades.id','=','oportunidades.id_entidad')
                ->where([['alm_req.estado','!=',7]])
            ->orderBy('alm_req.fecha_requerimiento','desc');
            // ->get();
        return datatables($data)->toJson();
        // return response()->json($data);
    }

    public function verTrazabilidadRequerimiento($id_requerimiento){
        $data = DB::table('almacen.alm_req_obs')
        ->select('alm_req_obs.*','sis_usua.nombre_corto')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req_obs.id_usuario')
        ->where('alm_req_obs.id_requerimiento',$id_requerimiento)
        ->orderBy('fecha_registro','asc')
        ->get();
        return response()->json($data);
    }

    public function verRequerimientoAdjuntos($id_requerimiento){
        $data = DB::table('almacen.alm_req_adjuntos')
        ->where([['alm_req_adjuntos.id_requerimiento','=',$id_requerimiento],['estado','=',1]])
        ->orderBy('fecha_registro','desc')
        ->get();
        $i = 1;
        $html = '';
        foreach($data as $d){
            $ruta = '/logistica/requerimiento/'.$d->archivo;
            $file = asset('files').$ruta;
            $html .= '  
                <tr id="seg-'.$d->id_adjunto.'">
                    <td>'.$i.'</td>
                    <td><a href="'.$file.'" target="_blank">'.$d->archivo.'</a></td>
                    <td>'.$d->fecha_registro.'</td>
                </tr>';
            $i++;
        }
        return json_encode($html);
    }

    public function listarAdjuntosOrdenDespacho($id_od){
        $data = DB::table('almacen.orden_despacho_adjunto')
        ->where([['orden_despacho_adjunto.id_od','=',$id_od],['estado','!=',7]])
        ->get();
        $i = 1;
        $html = '';
        foreach($data as $d){
            $ruta = '/almacen/orden_despacho/'.$d->archivo_adjunto;
            $file = asset('files').$ruta;
            $html .= '  
                <tr id="'.$d->id_od_adjunto.'">
                    <td>'.$i.'</td>
                    <td>'.($d->descripcion!=null ? $d->descripcion : '').'</td>
                    <td><a href="'.$file.'" target="_blank">'.$d->archivo_adjunto.'</a></td>
                    <td>'.$d->fecha_registro.'</td>
                    <td><i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                    title="Anular Adjunto" onClick="anular_adjunto('.$d->id_od_adjunto.');"></i></td>
                </tr>';
            $i++;
        }
        return json_encode($html);
    }

    public function guardar_od_adjunto(Request $request){
        $file = $request->file('archivo_adjunto');
        $id = 0;
        if (isset($file)){
            //obtenemos el nombre del archivo
            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $nombre = $request->codigo_od.'.'.$request->numero.'.'.$extension;
            //indicamos que queremos guardar un nuevo archivo en el disco local
            \File::delete(public_path('almacen/orden_despacho/'.$nombre));
            \Storage::disk('archivos')->put('almacen/orden_despacho/'.$nombre,\File::get($file));
            
            $id = DB::table('almacen.orden_despacho_adjunto')->insertGetId(
                [
                    'id_od' => $request->id_od,
                    'descripcion' => $request->descripcion,
                    'archivo_adjunto' => $nombre,
                    'estado' => 1,
                    'fecha_registro' => date('Y-m-d H:i:s')
                ],
                    'id_od_adjunto'
                );
        }
        else if ($request->descripcion !== null){
            $id = DB::table('almacen.orden_despacho_adjunto')->insertGetId(
                [
                    'id_od' => $request->id_od,
                    'descripcion' => $request->descripcion,
                    // 'archivo_adjunto' => null,
                    'estado' => 1,
                    'fecha_registro' => date('Y-m-d H:i:s')
                ],
                    'id_od_adjunto'
                );
        }
        return response()->json($id);
    }

    public function anular_od_adjunto($id_od_adjunto){
        try {
            DB::beginTransaction();

            $update = 0;
            $adjunto = DB::table('almacen.orden_despacho_adjunto')
            ->where('id_od_adjunto',$id_od_adjunto)
            ->first();

            $file_path = public_path()."\\files\almacen\orden_despacho\\".$adjunto->archivo_adjunto;

            if (file_exists($file_path)){
                File::delete($file_path);

                $update = DB::table('almacen.orden_despacho_adjunto')
                ->where('id_od_adjunto',$id_od_adjunto)
                ->update(['estado'=>7]);
            }
            
            DB::commit();
            return response()->json($update);
            
        } catch (\PDOException $e) {
    
            DB::rollBack();
        }
    }

    public function mostrar_transportistas()
    {
        $data = DB::table('logistica.log_prove')
            ->select('log_prove.id_proveedor', 'adm_contri.id_contribuyente', 'adm_contri.nro_documento', 'adm_contri.razon_social','adm_contri.telefono')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->where([   ['log_prove.estado', '=', 1],
                        ['adm_contri.transportista', '=', true]])
            ->orderBy('adm_contri.nro_documento')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function listarGuiasTransportistas()
    {
        $data = DB::table('almacen.orden_despacho')
            ->select('orden_despacho.*', 'adm_contri.razon_social','oc_propias.orden_am',
            'oc_propias.id as id_oc_propia','oc_propias.url_oc_fisica','alm_req.codigo as cod_req',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color','entidades.nombre')
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'orden_despacho.id_transportista')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
            ->leftjoin('mgcp_cuadro_costos.cc','cc.id','=','alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades','oportunidades.id','=','cc.id_oportunidad')
            ->leftjoin('mgcp_acuerdo_marco.oc_propias','oc_propias.id_oportunidad','=','oportunidades.id')
            ->leftjoin('mgcp_acuerdo_marco.entidades','entidades.id','=','oportunidades.id_entidad')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','orden_despacho.estado')
            ->orderBy('orden_despacho.fecha_transportista','desc')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
}
