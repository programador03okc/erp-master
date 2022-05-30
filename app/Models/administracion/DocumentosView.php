<?php


namespace App\Models\Administracion;

 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DocumentosView extends Model
{

    protected $table = 'administracion.documentos_view';
    public $timestamps = false;
    protected $appends = ['id_flujo'];


    public function getIdFlujoAttribute()
    {

        $idRolUsuarioDocList=[];
        $allRolUsuarioDocList = Auth::user()->getAllRolUser( $this->attributes['id_usuario']);
        foreach ($allRolUsuarioDocList as $allroldoc) {
            $idRolUsuarioDocList[]=$allroldoc->id_rol;
        }

        $operaciones = Operacion::getOperacion(
            $this->attributes['id_tp_documento'], 
            $this->attributes['id_tipo_requerimiento'], 
            $this->attributes['id_grupo'], 
            $this->attributes['id_division'], 
            $this->attributes['id_prioridad'], 
            $this->attributes['id_moneda'], 
            $this->attributes['monto_total'], 
            $this->attributes['id_tipo_requerimiento'], 
            $idRolUsuarioDocList
        );

        if(count($operaciones)>1){
            return '';

        }elseif(count($operaciones)==1){

            $flujoTotal = Flujo::getIdFlujo($operaciones[0]->id_operacion)['data'];
            if(count($flujoTotal)==1){
                return $flujoTotal[0]->id_flujo;
            }else{
                return $flujoTotal[0]->id_flujo;
            }
        }

    }

}

