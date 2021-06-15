<?php

namespace App\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class VoBo extends Model
{
    protected $table = 'administracion.adm_vobo';
    protected $primaryKey = 'id_vobo';
    public $timestamps = false;


    public static function getIdVoBo($nombreVoBo){
        $estado_doc =  VoBo::where('descripcion', $nombreVoBo)
        ->get();
        if($estado_doc->count()>0){
            $id_vobo=  $estado_doc->first()->id_vobo;
        }else{
            $id_vobo =0;
        }
        return $id_vobo;
    }
}