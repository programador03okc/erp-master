<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Model;

class Contribuyente extends Model
{
        protected $table = 'contabilidad.adm_contri';
        protected $primaryKey = 'id_contribuyente';
        public $timestamps = false;

    //    public function tipocontribuyente()
    //    {
    //        return $this->hasOne('App\Models\administracion\tipo_contribuyente','id_tipo_contribuyente','id_tipo_contribuyente');
 
    //    }
    //    public function contribuyentecontacto()
    //    {
    //        return $this->hasMany('App\Models\administracion\contribuyente_contacto','id_contribuyente','id_contribuyente');
 
    //    }
    //    public function rubro()
    //    {
    //        return $this->hasOne('App\Models\administracion\contribuyente_rubro','id_contribuyente','id_contribuyente');
 
    //    }

    //    public function cuenta()
    //    {
    //        return $this->hasOne('App\Models\administracion\cuenta_contribuyente','id_contribuyente','id_contribuyente');
 
    //    }
}
