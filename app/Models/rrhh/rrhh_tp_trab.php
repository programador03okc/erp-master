<?php

namespace App\Models\rrhh;

use Illuminate\Database\Eloquent\Model;

class rrhh_tp_trab extends Model
{
          // table name
          protected $table = 'rrhh_tp_trab';
          //primary key
          protected $primaryKey = 'id_tipo_trabajador';
          // public $incrementing = false;
          //Timesptamps
          public $timestamps = false;
    
        protected $fillable = [
            'id_tipo_trabajador',
            'descripcion',
            'estado'
        ];
}

