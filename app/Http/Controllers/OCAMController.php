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
}