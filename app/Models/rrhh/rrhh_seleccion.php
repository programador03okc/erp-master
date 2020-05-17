<?php

namespace App\Models\rrhh;

use Illuminate\Database\Eloquent\Model;

class rrhh_seleccion extends Model
{
          // table name
          protected $table = 'rrhh_selec';
          //primary key
          protected $primaryKey = 'id_seleccion';
          // public $incrementing = false;
          //Timesptamps
          public $timestamps = false;
    
        protected $fillable = [
            'id_seleccion',
            'id_empresa',
            'id_cargo',
            'requisitos',
            'perfil',
            'lugar',
            'cantidad',
            'fecha_inicio',
            'fecha_fin',
            'estado',
            'fecha_registro'
        ];
}

