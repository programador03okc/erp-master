<?php

namespace App\Models\rrhh;

use Illuminate\Database\Eloquent\Model;

class rrhh_pensi extends Model
{
          // table name
          protected $table = 'rrhh_pensi';
          //primary key
          protected $primaryKey = 'id_pensi';
          // public $incrementing = false;
          //Timesptamps
          public $timestamps = false;
    
        protected $fillable = [
            'id_pensi',
            'descripcion',
            'procentaje_general',
            'aporte',
            'prima_seguro',
            'comision',
            'estado'
        ];
}

