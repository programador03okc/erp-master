<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class SolicitudesTipos extends Model
{
    //
    protected $table = 'finanzas.solicitudes_tipos';

    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'descripcion',
		'estado',
    ];

    public function subtipos(){
        return $this->hasMany('App\Models\Tesoreria\SolicitudesSubTipos');
    }

}
