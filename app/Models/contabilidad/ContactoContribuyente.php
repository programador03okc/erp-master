<?php

namespace App\Models\Contabilidad;

use App\Models\Configuracion\Distrito;
use Illuminate\Database\Eloquent\Model;

class ContactoContribuyente extends Model
{
    protected $table = 'contabilidad.adm_ctb_contac';
    protected $primaryKey = 'id_datos_contacto';
    public $timestamps = false;
    protected $appends = ['ubigeo_completo'];

    public function getUbigeoCompletoAttribute(){
        $dis= $this->attributes['ubigeo'];
        if($dis>0){
            $ubigeo=Distrito::with('provincia.departamento')->where('id_dis',$dis)->first();
            $dist= $ubigeo->descripcion;
            $prov= $ubigeo->provincia->descripcion;
            $dpto= $ubigeo->provincia->departamento->descripcion;
            return ($dist.' - '.$prov.' - '.$dpto);
        }else{
            return '';
        }

    }

    //    public function contribuyentecontacto()
    //    {
    //        return $this->hasOne('App\Models\administracion\contribuyente','id_contribuyente','id_contribuyente');

    //    }
}
