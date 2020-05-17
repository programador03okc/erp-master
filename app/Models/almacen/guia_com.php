<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class guia_com extends Model
{
    protected $table = 'almacen.guia_com';

    protected $primaryKey ='id_guia_com';
    
    public $timestamps=false;

    protected $fillable = [
        'id_guia', 
        'serie', 
        'numero', 
        'id_proveedor', 
        'fecha_emision', 
        'fecha_almacen', 
        'archivo_adjunto', 
        'id_almacen', 
        'usuario', 
        'estado', 
        'fecha_registro', 
        'id_motivo', 
        'id_guia_clas', 
        'id_guia_cond'
    ];
    protected $guarded = ['id_guia_com'];
}