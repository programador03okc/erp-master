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

    function get_operacion($tipo_documento,$grupo,$prioridad){

        $data_tipo_documento = $this->get_id_tipo_documentp($tipo_documento);
        if($data_tipo_documento['status']==200){

            $adm_operacion = DB::table('administracion.adm_operacion')
            ->where([
                ['id_tp_documento', '=', $data_tipo_documento['data']], 
                ['id_prioridad', '=', $prioridad ], 
                ['id_grupo', '=', $grupo ], 
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


    // function getCriterioPrioridad($id_grupo_criterios){
    //     if($id_grupo_criterios > 0){

    //         $adm_detalle_grupo_criterios = DB::table('administracion.adm_detalle_grupo_criterios')
    //         ->select('adm_detalle_grupo_criterios.id_prioridad')
    //         ->where([
    //             ['id_grupo_criterios', '=', $id_grupo_criterios], 
    //             ['estado', '=', 1] 
    //             ])
    //         ->get();

    //         $status=200;
    //         if($adm_detalle_grupo_criterios){
    //             foreach($adm_detalle_grupo_criterios as $element){
    //                 $id_prioridad_list[] = $element->id_prioridad;
    //             }

    //         }else{
    //             $id_prioridad_list=[];
    //         }

    //     }else{
    //         $id_prioridad_list=[];
    //         $status=400;
    //     }
    //     $output =['data'=>$id_prioridad_list,'status'=>$status];

    //     return $output;
    // }

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

            if(isset($adm_flujo) && count($adm_flujo)>0){
                $status=200;
                $message ='OK';
                foreach($adm_flujo as $element){
                    $flujo_list[] = $element;
                }
                
            }else{
                $flujo_list=[];
                $status=204;
                $message ='No Content, data vacia';

            }

        }else{
            $flujo_list=[];
            $status=400;
            $message ='Bad Request, necesita un parametro';

        }
        $output =['data'=>$flujo_list,'status'=>$status,'message'=>$message];

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
                ['id_doc_aprob', '=', $id_doc_aprobacion],
                ['adm_flujo.estado', '=', 1]
                ])
            ->orderBy('adm_flujo.orden', 'asc')
            ->get();
 
            if(isset($adm_aprobacion) && (count($adm_aprobacion) > 0) ){
                $status=200;
                $message ='OK';
                foreach($adm_aprobacion as $element){
                    $aprobacion_list[] = $element;
                }
                
            }else{
                $aprobacion_list=[];
                $status=204; // No Content
                $message ='No Content, data vacia';

            }

        }else{
            $aprobacion_list=[];
            $status=400; //Bad Request
            $message ='Bad Request, necesita un parametro';
        }

        $output =['data'=>$aprobacion_list,'status'=>$status, 'message'=>$message];

        return $output;
    }

    function getFlujoByIdDocumento($id_doc){

        $id='';
        $id_tipo_doc='';
        $flujo=[];
        $documentos_aprob = DB::table('administracion.adm_documentos_aprob')
        ->where([
            ['id_doc_aprob', '=', $id_doc] 
            ])
        ->get();
        if(isset($documentos_aprob) && count($documentos_aprob)>0){
            $id=$documentos_aprob->first()->id_doc;
            $id_tipo_doc=$documentos_aprob->first()->id_tp_documento;
        }


        if($id_tipo_doc>0){
            switch ($id_tipo_doc) {
                case 1: //requerimiento
                    $req = DB::table('almacen.alm_req')
                    ->where([
                        ['id_requerimiento', '=', $id] 
                        ])
                    ->get();
                    
                    if(isset($req) && count($req)>0){
                        $id_grupo=$req->first()->id_grupo;
                        $id_prioridad=$req->first()->id_prioridad;
                    }

                    $operacion= $this->get_operacion('Requerimiento',$id_grupo,$id_prioridad)['data'];
                    $id_operacion=$operacion[0]->id_operacion;

                    $flujo = $this->getIdFlujo($id_operacion)['data'];

                    return $flujo;
                    break;
                
                default:
                    # code...
                    break;
            }
        }

    }
    
    public function getIdVoBo($nombreVoBo){
        $estado_doc =  DB::table('administracion.adm_vobo')
        ->where('descripcion', $nombreVoBo)
        ->get();
        if($estado_doc->count()>0){
            $id_vobo=  $estado_doc->first()->id_vobo;
        }else{
            $id_vobo =0;
        }
        return $id_vobo;
    }
    
    function searchIdFlujoByOrden($flujo,$orden){
        $id_flujo=0;
        foreach ($flujo as $key => $value) {
            if($value->orden == $orden){
                $id_flujo =$value->id_flujo;
            }
        }
        return $id_flujo;
    }
    
    function guardar_aprobacion_documento($id_flujo, $id_doc_aprob, $id_vobo, $detalle_observacion, $id_usuario, $id_rol){
        $hoy = date('Y-m-d H:i:s');
        $nuevaAprobacion = DB::table('administracion.adm_aprobacion')->insertGetId(
            [
                'id_flujo'              => $id_flujo,
                'id_doc_aprob'          => $id_doc_aprob,
                'id_vobo'               => $id_vobo,
                'id_usuario'            => $id_usuario,
                'fecha_vobo'            => $hoy,
                'detalle_observacion'   => $detalle_observacion,
                'id_rol'                => $id_rol
            ],
            'id_aprobacion'
        );

        return $nuevaAprobacion;
    }

    public function getIdDocByIdDocAprob($id_doc_aprob){
        $documentos_aprob =  DB::table('administracion.adm_documentos_aprob')
        ->where('id_doc_aprob', $id_doc_aprob)
        ->get();
        if($documentos_aprob->count()>0){
            $id_doc=  $documentos_aprob->first()->id_doc;
        }else{
            $id_doc =0;
        }

        return $id_doc;
    }

    public function getEstadoDocByName($nombreEstadoDoc){
        $estado_doc =  DB::table('administracion.adm_estado_doc')
        ->where('estado_doc', $nombreEstadoDoc)
        ->get();
        if($estado_doc->count()>0){
            $id_estado_doc=  $estado_doc->first()->id_estado_doc;
        }else{
            $id_estado_doc =0;
        }

        return $id_estado_doc;
    }

    function aprobar_documento(Request $request){
        $id_doc_aprob = $request->id_doc_aprob;
        $detalle_observacion = $request->detalle_observacion;
        $id_rol = $request->id_rol;
        $id_vobo = $this->getIdVoBo('Aprobado');
        $id_usuario = Auth::user()->id_usuario;

        $status='';
        $message ='';

        // ### determinar flujo , tamaño de flujo
        $flujo = $this->getFlujoByIdDocumento($id_doc_aprob);
        $tamaño_flujo = count($flujo);
        
        $aprobaciones = $this->getVoBo($id_doc_aprob);
        $aprobacionList =$aprobaciones['data'];
        $cantidad_aprobaciones =count($aprobacionList);
        // ### tiene aprobaciones? cantidad de aprobaciones realizadas?
        // ### si tiene aprobaciones determinar si es la ultima aprobacion
        // return $aprobacionList;
        $id_flujo = $this->searchIdFlujoByOrden($flujo,$cantidad_aprobaciones +1);

        if($cantidad_aprobaciones < $tamaño_flujo){
            $nuevaAprobacion= $this->guardar_aprobacion_documento($id_flujo, $id_doc_aprob, $id_vobo, $detalle_observacion, $id_usuario, $id_rol);
            if($nuevaAprobacion >0){
                $status=200; // No Content
                $message ='Ok';
                $aprobaciones = $this->getVoBo($id_doc_aprob);
                $aprobacionList =$aprobaciones['data'];
                $cantidad_aprobaciones =count($aprobacionList);
                if($cantidad_aprobaciones == $tamaño_flujo){
                    $id_req=$this->getIdDocByIdDocAprob($id_doc_aprob);
                    $estado_aprobado= $this->getEstadoDocByName('Aprobado');
                    $update_requerimiento = DB::table('almacen.alm_req')->where('id_requerimiento', $id_req)->update(['estado' => $estado_aprobado]);
                }else{
                    $id_req=$this->getIdDocByIdDocAprob($id_doc_aprob);
                    $estado_pendiente_aprobacion= $this->getEstadoDocByName('Pendiente de Aprobación');
                    $update_requerimiento = DB::table('almacen.alm_req')->where('id_requerimiento', $id_req)->update(['estado' => $estado_pendiente_aprobacion]);
                }
            }else{
                $status=204; // No Content
                $message ='No Content, data vacia';
            }
        }
        
        $output=['status'=>$status,'message'=>$message];
        return $output;
    }

}
