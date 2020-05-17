<?php

namespace App\Models\Tesoreria;

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

    public function almacenes(){
        return $this->hasMany('App\Models\Tesoreria\Almacen','id_sede');
    }
    public function grupos(){
        return $this->hasMany('App\Models\Tesoreria\Grupo','id_sede');
    }

    public function cajachica(){
        return $this->hasOne('App\Models\Tesoreria\CajaChicaMovimiento','id');
    }
}
