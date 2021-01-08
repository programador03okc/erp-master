<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

// use Mail;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

// use Dompdf\Dompdf;
// use PDF;

// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


use DataTables;

date_default_timezone_set('America/Lima');

class CotizacionController extends Controller
{


    public function get_estado_doc($nombreEstadoDoc){
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


    
    public function requerimientos_entrante_a_cotizacion($id_empresa = null,$id_sede=null)
    {
        $estado_aprobado = $this->get_estado_doc('Aprobado');
        $estado_anulado = $this->get_estado_doc('Anulado');
        $estado_observado = $this->get_estado_doc('Observado');
        $estado_denegado = $this->get_estado_doc('Denegado');
        $estado_elaborado = $this->get_estado_doc('Elaborado');
        $estado_excluidos=[$estado_elaborado,$estado_anulado, $estado_observado,$estado_denegado];

            $id_detalle_req_list_in_coti = DB::table('logistica.log_valorizacion_cotizacion')
            ->select('valoriza_coti_detalle.id_requerimiento')
            ->leftJoin('logistica.valoriza_coti_detalle', 'valoriza_coti_detalle.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
            ->where([['log_valorizacion_cotizacion.estado', '!=',$estado_anulado ],
            ['valoriza_coti_detalle.estado', '!=',$estado_anulado ]]) 
            ->groupBy('valoriza_coti_detalle.id_requerimiento')
            ->orderBy('valoriza_coti_detalle.id_requerimiento', 'desc')
            ->get();

              $reqList=[];
            foreach($id_detalle_req_list_in_coti as $data){
                 array_push($reqList, $data->id_requerimiento);
            }

            $idReqInValCotiListUniq=array_unique($reqList);
  
            // $id_empresa = 2; //enviar como parametro el id_empresa
            $whereIdEmpresa=[];
            if($id_empresa != null && $id_empresa >0){
                $whereIdEmpresa[] =['sis_sede.id_empresa','=',$id_empresa];
            }
            if($id_sede != null && $id_sede >0){
                $whereIdEmpresa[] =['sis_sede.id_sede','=',$id_sede];
            }
            $gruposByEmpresa=[];

            $SQLgrupoByEmpresa = DB::table('administracion.adm_grupo')   
            ->select('adm_grupo.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
            ->where($whereIdEmpresa)
            ->orderBy('adm_grupo.id_grupo', 'desc')
            ->get();
            if($SQLgrupoByEmpresa){
                foreach($SQLgrupoByEmpresa as $data){
                    $gruposByEmpresa[]=$data->id_grupo;
                }
            }
            // return $gruposByEmpresa;
 
            $req = DB::table('almacen.alm_req')   
            ->select('alm_req.*', 'adm_area.descripcion as des_area', 'adm_estado_doc.estado_doc')
            ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
            ->where([
                ['id_tipo_requerimiento',1]
            ])
            ->where(
                'alm_req.estado',$estado_aprobado
                )
            // ->whereNotIn(
            //     'alm_req.estado',$estado_excluidos
            //     )
            ->whereIn(
                'alm_req.id_grupo',$gruposByEmpresa

            )
            ->orderBy('alm_req.fecha_registro', 'desc')
            ->get();
            $requerimientos=[];
            foreach($req as $data){
                $requerimientos[]=[
                    'id_requerimiento'=>$data->id_requerimiento,
                    'codigo'=>$data->codigo,
                    'id_tipo_requerimiento'=>$data->id_tipo_requerimiento,
                    'id_usuario'=>$data->id_usuario,
                    'id_rol'=>$data->id_rol,
                    'fecha_requerimiento'=>$data->fecha_requerimiento,
                    'concepto'=>$data->concepto,
                    'id_grupo'=>$data->id_grupo,
                    'fecha_registro'=>$data->fecha_registro,
                    'id_area'=>$data->id_area,
                    'id_prioridad'=>$data->id_prioridad,
                    'id_estado'=>$data->estado,
                    'id_moneda'=>$data->id_moneda,
                    // 'obs_log'=>$data->obs_log,
                    'des_area'=>$data->des_area,
                    'estado_doc'=>$data->estado_doc,
                    'has_cotizacion'=>in_array($data->id_requerimiento, $idReqInValCotiListUniq)?true:false,
                ];
                

            } 

        $output['data'] = $requerimientos;

        return response()->json($output);
    }

}
