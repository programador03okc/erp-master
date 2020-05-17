<?php

namespace App\Models\proyectos;

use Illuminate\Database\Eloquent\Model;

class proy_proyecto extends Model
{
    protected $table = 'proy_proyecto';

    protected $primaryKey ='id_proyecto';
    
    public $timestamps=false;

    protected $fillable = [
        'id_proyecto',
        'tp_proyecto',
        'empresa',
        'descripcion',
        'cliente',
        'fecha_inicio',
        'fecha_fin',
        'elaborado_por',
        'codigo_snip',
        'modalidad',
        'sis_contrato',
        'residente',
        'estado',
        'fecha_registro'
    ];
    protected $guarded = ['id_proyecto'];
}