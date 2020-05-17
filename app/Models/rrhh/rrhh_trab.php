<?php

namespace App\Models\rrhh;

use Illuminate\Database\Eloquent\Model;

class rrhh_trab extends Model
{
          // table name
          protected $table = 'rrhh_trab';
          //primary key
          protected $primaryKey = 'id_trabajador';
          // public $incrementing = false;
          //Timesptamps
          public $timestamps = false;
    
        protected $fillable = [
            'id_trabajador',
            'id_postulante',
            'id_tipo_trabajador',
            'condicion',
            'hijos',
            'id_pension',
            'seguro',
            // 'archivo_adjunto',
            'estado',
            'fecha_registro'
        ];
}

