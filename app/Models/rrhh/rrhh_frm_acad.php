<?php

namespace App\Models\rrhh;

use Illuminate\Database\Eloquent\Model;

class rrhh_frm_acad extends Model
{
          // table name
          protected $table = 'rrhh_frm_acad';
          //primary key
          protected $primaryKey = 'id_formacion';
          // public $incrementing = false;
          //Timesptamps
          public $timestamps = false;
    
        protected $fillable = [
            'id_formacion',
            'id_postulante',
            'id_nivel_estudio',
            'fecha_inicio',
            'fecha_fin',
            'nombre_institucion',
            'id_pais',
            'ubigeo',
            // 'archivo_adjunto',
            'fecha_registro',
        ];
}

