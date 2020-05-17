<?php

namespace App\Models\proyectos;

use Illuminate\Database\Eloquent\Model;

class proy_tp_proyecto extends Model
{
    protected $table = 'proy_tp_proyecto';

    protected $primaryKey ='id_tp_proyecto';
    
    public $timestamps=false;

    protected $fillable = [
        'id_tp_proyecto',
        'descripcion',
        'estado',
        'fecha_registro'
    ];

    protected $guarded = ['id_tp_proyecto'];
}