<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
    //
    protected $table = 'configuracion.sis_moneda';

    protected $primaryKey = 'id_moneda';

    public $timestamps = false;
/*
   protected $fillable = [
        'codigo',
        'descripcion',
        'direccion',
        'estado',
    ];
   */
    protected $guarded = ['id_moneda'];

    public function solicitudes(){
        return $this->hasMany('App\Models\Tesoreria\Solicitud', 'id');
    }

    public function cajachica(){
        return $this->hasOne('App\Models\Tesoreria\CajaChicaMovimiento','id');
    }
}
