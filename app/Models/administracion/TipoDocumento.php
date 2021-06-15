<?php

namespace App\Models\Administracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TipoDocumento extends Model
{
    protected $table = 'administracion.adm_tp_docum';
    protected $primaryKey = 'id_tp_documento';
    public $timestamps = false;

    public static function getIdTipoDocumentp($tipo_documento){
        $status ='';
        $message ='';
        $data ='';

        $alm_tp_req =  TipoDocumento::where('descripcion','like', '%'.$tipo_documento)
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
}
