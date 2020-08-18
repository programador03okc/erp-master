<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

use Dompdf\Dompdf;
use PDF;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

 
use DataTables;
use Debugbar;

date_default_timezone_set('America/Lima');

class RequerimientoController extends Controller
{

    
    
    public function requerimientos_pendientes_aprobacion(){
        // $id_usuario = Auth::user()->id_usuario;
        // $nombre_corto = Auth::user()->nombre_corto;
        // $rolActual = Auth::user()->rol;
        // $allRol = Auth::user()->getAllRol();
        // $allGrupo = Auth::user()->getAllGrupo();

        // $estado_elaborado =(new LogisticaController)->get_estado_doc('Elaborado');
        $uso_administracion =(new LogisticaController)->get_tipo_cliente('Uso Administración');
        $compra =(new LogisticaController)->get_tipo_requerimiento('Compra');
        $operaciones =(new AprobacionController)->get_operacion('Requerimiento');
        $tipo_documento = 1; // Requerimientos

        $requerimientos = DB::table('almacen.alm_req')
            ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('almacen.tipo_cliente', 'alm_req.tipo_cliente', '=', 'tipo_cliente.id_tipo_cliente')
            ->leftJoin('almacen.alm_almacen', 'alm_req.id_almacen', '=', 'alm_almacen.id_almacen')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->leftJoin('contabilidad.adm_contri as contrib', 'adm_empresa.id_contribuyente', '=', 'contrib.id_contribuyente')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
            ->leftJoin('proyectos.proy_op_com', 'proy_op_com.id_op_com', '=', 'alm_req.id_op_com')
            ->leftJoin('proyectos.proy_presup', 'alm_req.id_presupuesto', '=', 'proy_presup.id_presupuesto')
            ->leftJoin('comercial.com_cliente', 'alm_req.id_cliente', '=', 'com_cliente.id_cliente')
            ->leftJoin('configuracion.ubi_dis', 'alm_req.id_ubigeo_entrega', '=', 'ubi_dis.id_dis')
            ->leftJoin('configuracion.ubi_prov', 'ubi_dis.id_prov', '=', 'ubi_prov.id_prov')
            ->leftJoin('configuracion.ubi_dpto', 'ubi_prov.id_dpto', '=', 'ubi_dpto.id_dpto')
            ->leftJoin('configuracion.sis_moneda', 'alm_req.id_moneda', '=', 'sis_moneda.id_moneda')
            ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
            ->leftJoin('administracion.adm_periodo', 'alm_req.id_periodo', '=', 'adm_periodo.id_periodo')
            ->leftJoin('configuracion.sis_rol', 'alm_req.id_rol', '=', 'sis_rol.id_rol')
            ->leftJoin('administracion.adm_documentos_aprob', 'alm_req.id_requerimiento', '=', 'adm_documentos_aprob.id_doc')

            ->select(
                'alm_req.id_requerimiento',
                'adm_documentos_aprob.id_doc_aprob',
                'alm_req.codigo',
                'alm_req.concepto',
                'alm_req.id_moneda',
                'sis_moneda.descripcion as desrcipcion_moneda',
                'alm_req.id_periodo',
                'adm_periodo.descripcion as descripcion_periodo',
                'alm_req.id_prioridad',
                'adm_prioridad.descripcion as descripcion_prioridad',
                'alm_req.estado',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'sis_sede.id_empresa',
                'alm_req.id_grupo',
                'adm_grupo.descripcion as descripcion_grupo',
                'contrib.razon_social as razon_social_empresa',
                'sis_sede.codigo as codigo_sede_empresa',
                'adm_empresa.logo_empresa',
                'alm_req.fecha_requerimiento',
                'alm_req.id_tipo_requerimiento',
                'alm_req.observacion',
                'alm_tp_req.descripcion AS tp_req_descripcion',
                'alm_req.id_usuario',
                DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno)  AS persona"),
                'sis_usua.usuario',
                'alm_req.id_rol',
                'sis_rol.descripcion as descripcion_rol',
                'rrhh_rol.id_rol_concepto',
                'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
                'alm_req.id_area',
                'adm_area.descripcion AS area_descripcion',
                'alm_req.id_op_com',
                'proy_op_com.codigo as codigo_op_com',
                'proy_op_com.descripcion as descripcion_op_com',
                'alm_req.archivo_adjunto',
                'alm_req.fecha_registro',
                'alm_req.id_sede',
                'alm_req.tipo_cliente as id_tipo_cliente',
                'tipo_cliente.descripcion as descripcion_tipo_cliente',
                'alm_req.id_ubigeo_entrega',
                DB::raw("(ubi_dis.descripcion) || ' ' || (ubi_prov.descripcion) || ' ' || (ubi_dpto.descripcion)  AS name_ubigeo"),
                'alm_req.id_almacen',
                'alm_almacen.descripcion as descripcion_almacen',
                'alm_req.monto',
                'alm_req.fecha_entrega'
            )
            ->where([
                ['alm_req.id_tipo_requerimiento','=',$compra], // compra
                ['alm_req.tipo_cliente','=',$uso_administracion] // uso administracion
                // ['alm_req.estado','=',$estado_elaborado] // elaborado
            ])
            ->orderBy('alm_req.id_requerimiento', 'asc')
        ->get();

        foreach($requerimientos as $element){
            $payload=[];
            $operacion_selected=0;
            $flujo_list_selected=[];
            $aprobaciones=[];
            $pendiente_aprobacion=[];

            $id_doc_aprobacion_req = $element->id_doc_aprob;
            $id_grupo_req = $element->id_grupo;
            $id_tipo_requerimiento_req = $element->id_tipo_requerimiento;
            $id_prioridad_req = $element->id_prioridad;
            $estado_req = $element->estado;

            $voboList =(new AprobacionController)->getVoBo($id_doc_aprobacion_req); // todas las vobo
            if($voboList['status']== 200){
                foreach($voboList['data'] as $vobo){ 
                    $aprobaciones[]= $vobo; //lista de aprobaciones
                }
            }
            // return $aprobaciones;

            // ##### obteniendo un array de id_flujos de aprobacion ###
            $id_flujo_array=[];
            foreach($aprobaciones as $aprobacion){
                $id_flujo_array[]= $aprobacion->id_flujo;
            }
            // #####

            $prioridadList=['data'=>[],'status'=>400];
            foreach($operaciones['data'] as $operacion){
                if($operacion->id_grupo == $id_grupo_req && $operacion->id_tp_documento == $tipo_documento){ 
                    $operacion_selected = $operacion->id_operacion;

                    if($operacion->id_grupo_criterios !=null){
                        // $prioridadArrayList =(new AprobacionController)->getCriterioPrioridad($operacion->id_grupo_criterios);
                        // if($prioridadList['status']==200){
                                // if(count($prioridadList['data'] > 0)){
                                    //  tiene criterio prioridad

                                // }
                                // return $prioridadArrayList;
                        // }
                        // $rangoMonto = $this->getCriterioMonto(); // only declared
                    }
                    $flujo_list =(new AprobacionController)->getIdFlujo($operacion_selected);

                    $pendiente_aprobacion= $flujo_list['data'];
                    //eliminando flujo ya aprobados
                    foreach ($pendiente_aprobacion as $key => $object) {
                        // if ($object->id_flujo == 3) {
                        if (in_array($object->id_flujo,$id_flujo_array)) {
                            array_splice($pendiente_aprobacion, $key, 1);

                        }
                    }
                    
                }
            }

            // filtar requerimientos para usuario en sesion 
            $allRol = Auth::user()->getAllRol();
           
            $id_rol_list=[];
            foreach($allRol as $rol){
                $id_rol_list[]= $rol->id_rol; // lista de id_rol del usuario en sesion
            }

            if(in_array($pendiente_aprobacion[0]->id_rol, $id_rol_list) == true){
                $payload[]=[
                    'id_requerimiento'=>$element->id_requerimiento,
                    'id_doc_aprob'=> $id_doc_aprobacion_req,
                    'id_tipo_requerimiento'=>$element->id_tipo_requerimiento,
                    'tp_req_descripcion'=>$element->tp_req_descripcion,
                    'id_tipo_cliente'=>$element->id_tipo_cliente,
                    'descripcion_tipo_cliente'=>$element->descripcion_tipo_cliente,
                    'id_prioridad'=>$element->id_prioridad,
                    'descripcion_prioridad'=>$element->descripcion_prioridad,
                    'id_periodo'=>$element->id_periodo,
                    'descripcion_periodo'=>$element->descripcion_periodo,
                    'codigo'=>$element->codigo,
                    'concepto'=>$element->concepto,
                    'id_empresa'=>$element->id_empresa,
                    'razon_social_empresa'=>$element->razon_social_empresa,
                    'codigo_sede_empresa'=>$element->codigo_sede_empresa,
                    'logo_empresa'=>$element->logo_empresa,
                    'id_grupo'=>$element->id_grupo,
                    'descripcion_grupo'=>$element->descripcion_grupo,
                    'fecha_requerimiento'=>$element->fecha_requerimiento,
                    'observacion'=>$element->observacion,
                    'name_ubigeo'=>$element->name_ubigeo,
                    'id_moneda'=>$element->id_moneda,
                    'desrcipcion_moneda'=>$element->desrcipcion_moneda,
                    'monto'=>$element->monto,
                    'fecha_entrega'=>$element->fecha_entrega,
                    'id_usuario'=>$element->id_usuario,
                    'id_rol'=>$element->id_rol,
                    'descripcion_rol'=>$element->descripcion_rol,
                    'usuario'=>$element->usuario,
                    'persona'=>$element->persona,
                    'id_almacen'=>$element->id_almacen,
                    'descripcion_almacen'=>$element->descripcion_almacen,
                    'aprobaciones'=>$aprobaciones,
                    'pendiente_aprobacion'=>$pendiente_aprobacion,
                    'estado'=>$element->estado,
                    'estado_doc'=>$element->estado_doc
                ];
            }

            
        }
        $output = ['data'=>$payload];
        return $output;

 
        //  return DataTables::of($output)
        // ->addColumn('flag',function($output){
        //         $flag = $output['flag'];
        //         return $flag;
        // })
        // ->addColumn('status',function($output){
        //         $status = $output['status'];
        //         return $status;
        // })
        // ->addColumn('action',function($output){
        //         $action = $output['action'];
        //         return $action;
        // })
        // ->rawColumns(['flag','status','action'])
        // ->make(true);
    }

    
}