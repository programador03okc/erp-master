<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class mov_alm_det extends Model
{
    protected $table = 'almacen.mov_alm_det';

    protected $primaryKey ='id_mov_alm_det';
    
    public $timestamps=false;

    protected $fillable = [
        'id_mov_alm_det', 
        'id_mov_alm', 
        'id_producto', 
        'id_ubicacion', 
        'cantidad', 
        'valorizacion', 
        'usuario', 
        'estado', 
        'fecha_registro'
    ];
    protected $guarded = ['id_mov_alm_det'];
}