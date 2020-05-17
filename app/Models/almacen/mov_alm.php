<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class mov_alm extends Model
{
    protected $table = 'almacen.mov_alm';

    protected $primaryKey ='id_mov_alm';
    
    public $timestamps=false;

    protected $fillable = [
        'id_mov_alm', 
        'id_almacen', 
        'id_tp_mov', 
        'codigo', 
        'fecha_emision', 
        'id_guia_com', 
        'id_guia_ven', 
        'id_doc_com', 
        'id_doc_ven', 
        'id_transferencia', 
        'usuario', 
        'estado', 
        'fecha_registro'
    ];
    protected $guarded = ['id_mov_alm'];
}