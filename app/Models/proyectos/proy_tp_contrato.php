<?php

namespace App\Models\proyectos;

use Illuminate\Database\Eloquent\Model;

class proy_tp_contrato extends Model
{
    protected $table = 'proy_tp_contrato';

    protected $primaryKey ='id_tp_contrato';
    
    public $timestamps=false;

    protected $fillable = [
        'id_tp_contrato',
        'descripcion',
        'estado',
        'fecha_registro'
    ];

    protected $guarded = ['id_tp_contrato'];
}