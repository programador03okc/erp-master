<?php

namespace App\Models\Logistica;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    //
    protected $table = 'administracion.sis_sede';

    protected $primaryKey = 'id_sede';

    public $timestamps = false;


    protected $guarded = ['id_sede'];

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function grupos(){
        return $this->hasMany('App\Models\Tesoreria\Grupo','id_sede');
    }

}
