<?php

namespace App\Models\proyectos;

use Illuminate\Database\Eloquent\Model;

class proy_modalidad extends Model
{
    protected $table = 'proy_modalidad';

    protected $primaryKey ='id_modalidad';
    
    public $timestamps=false;

    protected $fillable = [
        'id_modalidad',
        'descripcion',
        'estado',
        'fecha_registro',
        'usuario_registro'
    ];
}