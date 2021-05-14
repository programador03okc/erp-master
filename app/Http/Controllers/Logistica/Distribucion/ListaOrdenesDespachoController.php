<?php

namespace App\Http\Controllers\Logistica\Distribucion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ListaOrdenesDespachoController extends Controller
{
    public function __construct(){
        // session_start();
    }

    function view_ordenes_despacho(){
        
        return view('almacen/distribucion/listaOrdenesDespacho');
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
            // ->where([['alm_req.tiene_transformacion','=',true],['alm_req.estado','=',17]])
            ->where('alm_req.tiene_transformacion',true)
            ->whereIn('alm_req.estado',[17,27,28])
            // ->orWhere('alm_req.estado',29)
            // ->orWhere('alm_req.estado',27)
            // ->orWhere('alm_req.estado',28)
            ->orWhere([['alm_req.estado','=',19], ['alm_req.confirmacion_pago','=',true]])
            // ->orWhere([['alm_req.estado','=',22]])
            ->orderBy('alm_req.fecha_entrega','desc');
        return datatables($data)->toJson();
        // return response()->json($data);
    }
}
