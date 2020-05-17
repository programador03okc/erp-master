<?php

namespace App\Models\rrhh;

use Illuminate\Database\Eloquent\Model;

class rrhh_niv_estud extends Model
{
          // table name
          protected $table = 'rrhh_niv_estud';
          //primary key
          protected $primaryKey = 'id_nivel_estudio';
          // public $incrementing = false;
          //Timesptamps
          public $timestamps = false;
    
        protected $fillable = [
            'id_nivel_estudio',
            'descripcion',
            'estado'
        ];
}

