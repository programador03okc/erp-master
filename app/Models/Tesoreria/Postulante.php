<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class Postulante extends Model
{
    //
    protected $table = 'rrhh.rrhh_postu';

    protected $primaryKey = 'id_postulante';

    public $timestamps = false;
/*
   protected $fillable = [
        'codigo',
        'descripcion',
        'direccion',
        'estado',
    ];
   */
    protected $guarded = ['id_postulante'];


    public function persona(){
        return $this->belongsTo('App\Models\Tesoreria\Persona','id_persona','id_persona');
    }

}
