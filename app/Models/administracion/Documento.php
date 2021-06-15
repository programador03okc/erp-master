<?php

namespace App\Models\Administracion;

use App\Models\Almacen\Requerimiento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Documento extends Model
{
    protected $table = 'administracion.adm_documentos_aprob';
    protected $primaryKey = 'id_doc_aprob';
    public $timestamps = false;

    public static function getIdDocByIdDocAprob($id_doc_aprob){
        $documentos_aprob =  Documento::where('id_doc_aprob', $id_doc_aprob)
        ->get();
        if($documentos_aprob->count()>0){
            $id_doc=  $documentos_aprob->first()->id_doc;
        }else{
            $id_doc =0;
        }

        return $id_doc;
    }

    public static function getFlujoByIdDocumento($id_doc){

        $id='';
        $id_tipo_doc='';
        $flujo=[];
        $documentos_aprob = Documento::where([
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
                    $req = Requerimiento::where([
                        ['id_requerimiento', '=', $id] 
                        ])
                    ->get();
                    
                    if(isset($req) && count($req)>0){
                        $id_grupo=$req->first()->id_grupo;
                        $id_prioridad=$req->first()->id_prioridad;
                    }

                    $operacion= Operacion::getOperacion('Requerimiento',$id_grupo,$id_prioridad)['data'];
                    $id_operacion=$operacion[0]->id_operacion;

                    $flujo = Flujo::getIdFlujo($id_operacion)['data'];

                    return $flujo;
                    break;
                
                default:
                    # code...
                    break;
            }
        }

    }


    public static function searchIdFlujoByOrden($flujo,$orden){
        $id_flujo=0;
        foreach ($flujo as $key => $value) {
            if($value->orden == $orden){
                $id_flujo =$value->id_flujo;
            }
        }
        return $id_flujo;
    }
    public static function searchIdFlujoPorIdRol($flujo,$allRol){
        $idFlujoList=[];
        foreach ($allRol as $rol) {
            foreach ($flujo as $f) {
                if($f->id_rol == $rol->id_rol){
                    $idFlujoList[] =$f->id_flujo;
                }
            }
        
        }
        return $idFlujoList;
    }
}
