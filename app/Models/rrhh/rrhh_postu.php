<?php

namespace App\Models\rrhh;

use Illuminate\Database\Eloquent\Model;

class rrhh_postu extends Model
{
          // table name
          protected $table = 'rrhh_postu';
          //primary key
          protected $primaryKey = 'id_postulante';
          // public $incrementing = false;
          //Timesptamps
          public $timestamps = false;
    
        protected $fillable = [
            'id_postulante',
            'id_persona',
            'direccion',
            'telefono',
            'correo',
            'brevette',
            'id_pais',
            'ubigeo',
            'fecha_registro'
        ];
}

