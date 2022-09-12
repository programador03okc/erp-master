<?php

namespace App\Models\rrhh;

use Illuminate\Database\Eloquent\Model;

class rrhh_perso extends Model
{
          // table name
          protected $table = 'rrhh.rrhh_perso';
          //primary key
          protected $primaryKey = 'id_persona';
          // public $incrementing = false;
          //Timesptamps
          public $timestamps = false;

        protected $fillable = [
            'id_persona',
            'id_documento_identidad',
            'nro_documento',
            'nombres',
            'apellido_paterno',
            'apellido_materno',
            'fecha_nacimiento',
            'sexo',
            'id_estado_civil',
            'fecha_registro'
        ];
}

