<?php

namespace App\Models\proyectos;

use Illuminate\Database\Eloquent\Model;

class proy_sis_contrato extends Model
{
    protected $table = 'proy_sis_contrato';

    protected $primaryKey ='id_sis_contrato';
    
    public $timestamps=false;

    protected $fillable = [
        'id_sis_contrato',
        'descripcion',
        'estado',
        'fecha_registro'
    ];
}