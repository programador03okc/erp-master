<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Model;

class TipoContribuyente extends Model
{
        protected $table = 'contabilidad.adm_tp_contri';
        protected $primaryKey = 'id_tipo_contribuyente';
        public $timestamps = false;

        public static function mostrar()
        {
            $data = TipoContribuyente::select('adm_tp_contri.id_tipo_contribuyente', 
            'adm_tp_contri.descripcion',
            'adm_tp_contri.estado'
            )
            ->where('adm_tp_contri.estado', '=', 1)
            ->orderBy('adm_tp_contri.descripcion', 'asc')
            ->get();
            return $data;
        }
    //      public function contribuyente_tipocontribuyente()
    //    {
    //        return $this->hasOne('App\Models\administracion\contribuyente','id_tipo_contribuyente','id_tipo_contribuyente');
    //    }
}
