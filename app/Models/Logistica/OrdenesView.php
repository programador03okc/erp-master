<?php


namespace App\Models\Logistica;

 
use Illuminate\Database\Eloquent\Model;
 

class OrdenesView extends Model
{

    protected $table = 'logistica.ordenes_view';
    public $timestamps = false;
    protected $casts = [
        'data_requerimiento' => 'json'
    ];

    // public function requerimiento()
    // {
 
    //     return $this->hasMany('App\Models\almacen\Requerimiento','id_requerimiento', 'data_requerimiento.id_requerimiento');
    // }

}

