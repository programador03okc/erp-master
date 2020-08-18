<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

date_default_timezone_set('America/Lima');

class AprobacionController extends Controller
{

    public function get_id_tipo_documentp($tipo_documento){
        $status ='';
        $message ='';
        $data ='';

        $alm_tp_req =  DB::table('administracion.adm_tp_docum')
        ->where('descripcion','like', '%'.$tipo_documento)
        ->get();

        if($alm_tp_req->count()>0){
            $data=  $alm_tp_req->first()->id_tp_documento;
            $status = 200;

        }else{
            $data =0;
            $status = 400;
        }

        $output =['data'=>$data,'status'=>$status];
        return $output;
    }

    function get_operacion($tipo_documento){

        $data_tipo_documento = $this->get_id_tipo_documentp($tipo_documento);
        if($data_tipo_documento['status']==200){

            $adm_operacion = DB::table('administracion.adm_operacion')
            ->where([
                ['id_tp_documento', '=', $data_tipo_documento['data']], 
                ['estado', '=', 1] 
                ])
            ->get();
            $status=200;
        }else{
            $adm_operacion=[];
            $status=400;
        }
        $output =['data'=>$adm_operacion,'status'=>$status];

        return $output;
    }


    function getCriterioPrioridad($id_grupo_criterios){
        if($id_grupo_criterios > 0){

            $adm_detalle_grupo_criterios = DB::table('administracion.adm_detalle_grupo_criterios')
            ->select('adm_detalle_grupo_criterios.id_prioridad')
            ->where([
                ['id_grupo_criterios', '=', $id_grupo_criterios], 
                ['estado', '=', 1] 
                ])
            ->get();

            $status=200;
            if($adm_detalle_grupo_criterios){
                foreach($adm_detalle_grupo_criterios as $element){
                    $id_prioridad_list[] = $element->id_prioridad;
                }

            }else{
                $id_prioridad_list=[];
            }

        }else{
            $id_prioridad_list=[];
            $status=400;
        }
        $output =['data'=>$id_prioridad_list,'status'=>$status];

        return $output;
    }

    function getIdFlujo($id_operacion){
        if($id_operacion > 0){

            $adm_flujo = DB::table('administracion.adm_flujo')
            ->select('adm_flujo.*',
            'adm_flujo.nombre as nombre_flujo',
            'sis_rol.descripcion as descripcion_rol'
            )
            ->leftJoin('configuracion.sis_rol', 'adm_flujo.id_rol', '=', 'sis_rol.id_rol')
            ->where([
                ['adm_flujo.id_operacion', '=', $id_operacion], 
                ['adm_flujo.estado', '=', 1] 
                ])
            ->orderBy('adm_flujo.orden', 'asc')
            ->get();

            $status=200;
            if($adm_flujo){
                foreach($adm_flujo as $element){
                    $flujo_list[] = $element;
                }

            }else{
                $flujo_list=[];
            }

        }else{
            $flujo_list=[];
            $status=400;
        }
        $output =['data'=>$flujo_list,'status'=>$status];

        return $output;
    }

    function getVoBo($id_doc_aprobacion){
        if($id_doc_aprobacion > 0){
            $adm_aprobacion = DB::table('administracion.adm_aprobacion')
            ->select(
                'adm_aprobacion.id_aprobacion',
                'adm_aprobacion.id_flujo',
                'adm_aprobacion.id_vobo',
                'adm_vobo.descripcion',
                'adm_aprobacion.id_usuario',
                'sis_usua.nombre_corto',
                'adm_aprobacion.detalle_observacion',
                'adm_flujo.id_operacion',
                'adm_flujo.id_rol',
                'sis_rol.descripcion as descripcion_rol',
                'adm_flujo.nombre as nombre_flujo',
                'adm_flujo.orden'
            )
            ->leftJoin('administracion.adm_flujo', 'adm_aprobacion.id_flujo', '=', 'adm_flujo.id_flujo')
            ->leftJoin('administracion.adm_vobo', 'adm_aprobacion.id_vobo', '=', 'adm_vobo.id_vobo')
            ->leftJoin('configuracion.sis_usua', 'adm_aprobacion.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('configuracion.sis_rol', 'adm_flujo.id_rol', '=', 'sis_rol.id_rol')
            ->leftJoin('administracion.adm_operacion', 'adm_flujo.id_operacion', '=', 'adm_operacion.id_operacion')
            ->where([
                ['id_doc_aprob', '=', $id_doc_aprobacion]
                ])
            ->orderBy('adm_flujo.orden', 'asc')
            ->get();

            $status=200;
            if($adm_aprobacion){
                foreach($adm_aprobacion as $element){
                    $aprobacion_list[] = $element;
                }

            }else{
                $aprobacion_list=[];
            }

        }else{
            $aprobacion_list=[];
            $status=400;
        }

        $output =['data'=>$aprobacion_list,'status'=>$status];

        return $output;
    }
}
